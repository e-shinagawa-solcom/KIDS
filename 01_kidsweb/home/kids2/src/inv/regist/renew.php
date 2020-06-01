<?php

// ----------------------------------------------------------------------------
/**
 *       請求管理  請求書修正画面
 *
 *
 *       @package    K.I.D.S.
 *       @license    http://www.kuwagata.co.jp/
 *       @copyright  KUWAGATA CO., LTD.
 *       @author     K.I.D.S. Groups <info@kids-groups.com>
 *       @access     public
 *       @version    2.00
 *
 *
 *       処理概要
 *         ・請求書修正時の入力画面を表示
 *
 *       更新履歴
 *
 */
// ----------------------------------------------------------------------------


    // 設定読み込み
    include_once('conf.inc');

    // ライブラリ読み込み
    require (LIB_FILE);
    require (SRC_ROOT . "m/cmn/lib_m.php");
    require (SRC_ROOT . "inv/cmn/lib_regist.php");
    require (LIB_EXCLUSIVEFILE);

    // 固定エラーメッセージ ToDo DB登録
    define ("ERROR_NO_1", '' );

    // オブジェクト生成
    $objDB   = new clsDB();
    $objAuth = new clsAuth();

    // DBオープン
    $objDB->open("", "", "", "");

    // パラメータ取得
    if ( $_POST )
    {
        $aryData = $_POST;
    }
    elseif ( $_GET )
    {
        $aryData = $_GET;
    }

    if ( !$aryData["lngInvoiceNo"] )
    {
        fncOutputError ( 9061, DEF_ERROR, "データ異常です。", TRUE, "", $objDB );
    }

    // セッション確認
    $objAuth = fncIsSession($aryData["strSessionID"], $objAuth, $objDB);

    // cookieにSET
    if( !empty($aryData["strSessionID"]) )
        setcookie("strSessionID", $aryData["strSessionID"], 0, "/");

    // セッション確認
    $objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );


    // 文字列チェック
    $aryCheck["strSessionID"] = "null:numenglish(32,32)";
//     $aryCheck["lngInvoiceNo"] = "null:number(0,10)";

    // 2200 請求管理
    if ( !fncCheckAuthority( DEF_FUNCTION_INV0, $objAuth ) )
    {
        fncOutputError ( 9060, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
    }

    // 2205 請求書修正
    if ( !fncCheckAuthority( DEF_FUNCTION_INV5, $objAuth ) )
    {
        fncOutputError ( 9060, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
    }

    // ヘルプ対応
    $aryData["lngFunctionCode"] = DEF_FUNCTION_INV0;

    // ユーザーコード取得
    $lngUserCode = $objAuth->UserCode;


    // 指定請求書番号の請求書マスタ取得用SQL文の作成
    $lngInvoiceNo = $aryData["lngInvoiceNo"];
    $lngrevisionno = $aryData["lngRevisionNo"];
    $strQuery     = fncGetInvoiceMSQL ( $lngInvoiceNo, $lngrevisionno);
    // 詳細データの取得
    list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

    if ( $lngResultNum == 1 )
    {
        $aryResult = $objDB->fetchArray( $lngResultID, 0 );
    }
    else
    {
        MoveToErrorPage( "データは削除済みです" );
    }

    $aryNewResult = fncSetInvoiceHeadTableData($aryResult);
    $aryNewResult['lngInvoiceNo'] = $aryData["lngInvoiceNo"];
    $aryNewResult['strSessionID'] = $aryData["strSessionID"];
    $aryNewResult['actionName']   = 'renew.php';
    // preview画面
    if(isset($aryData["strMode"]) && $aryData["strMode"] == 'renewPrev')
    {
        $aryNewResult['strMode']      = 'insertRenew';
        $aryNewResult['slipNoList'] = $aryData["slipNoList"];
        $aryNewResult['revisionNoList'] = $aryData["revisionNoList"];
        $aryPrevResult = array_merge($aryNewResult, fncSetPreviewTableData($aryData, $lngInvoiceNo, $objDB));
        // テンプレート読み込み
        $objTemplate = new clsTemplate ();
        $objTemplate->getTemplate ("inv/base_preview.html");

        $objTemplate->replace($aryPrevResult);
        $objTemplate->complete();

        $doc = new DOMDocument();

        // パースエラー抑制
        libxml_use_internal_errors(true);
        // DOMパース
        $doc->loadHTML($objTemplate->strTemplate);
        // パースエラークリア
        libxml_clear_errors();
        // パースエラー抑制解除
        libxml_use_internal_errors(false);
        // 画面出力
        // header("Content-type: text/html; charset=utf-8");
        $out = $doc->saveHTML();
        echo $out;
        return true;

    }
    elseif(isset($aryData["strMode"]) && $aryData["strMode"] == 'insertRenew')
    {
        // *****************************************************
        //   UPDATE処理実行（Submit時）
        // *****************************************************

        // トランザクション開始
        $objDB->transactionBegin();

        if( !lockInvoice($lngInvoiceNo, $objDB) )
        {
            MoveToErrorPage("請求書データのロックに失敗しました");
        }

        if( isInvoiceModified($lngInvoiceNo, $lngrevisionno, $objDB) )
        {
            MoveToErrorPage("請求書データが更新または削除されています");
        }
        // DB登録の為のデータ配列を返す
        $insertData = fncInvoiceInsertReturnArray($aryData, $aryResult, $objAuth, $objDB);
        // 出力明細が1件もない場合
        $slipCodeArray = $insertData['slipCodeArray'];
        $slipNoArray = $insertData['slipNoArray'];
        $revisionNoArray = $insertData['revisionNoArray'];
        if(count($slipNoArray) < 0)
        {
            MoveToErrorPage("出力明細が選択されていません。");
        }

        for( $i=0; $i<COUNT($slipNoArray); $i++ ) {
            if( !lockSlip($slipNoArray[$i], $objDB) ){
                //fncOutputError ( 9051, DEF_ERROR, "登録対象納品書データのロックに失敗しました", TRUE, "", $objDB );
                MoveToErrorPage("登録対象納品書データのロックに失敗しました");
            }
            if( isSlipModified($slipNoArray[$i], $revisionNoArray[$i], $objDB) ){
                //fncOutputError ( 9051, DEF_ERROR, "登録対象納品書データが削除または更新されています", TRUE, "", $objDB );
                MoveToErrorPage("登録対象納品書データが削除または更新されています");
            }
            $condition['lngSlipNo'] = $slipNoArray[$i];
            $strQuery = fncGetSearchMSlipSQL($condition, $lngInvoiceNo, $objDB);
            $aryCurTax[] = array();
            // 明細データの取得
            list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
            if ( $lngResultNum )
            {
                for ( $j = 0; $j < $lngResultNum; $j++ )
                {
                    $Result = $objDB->fetchArray( $lngResultID, $j );
                    // 消費税率の配列
                    $aryCurTax[$i] = $Result['curtax'];

                    if ($Result['lngtaxclasscode'] != $insertData['lngtaxclasscode']) {
                        MoveToErrorPage("課税区分の異なる納品書は請求書の明細に混在できません");
                    }
                }
            }
            else
           {
                $strMessage = fncOutputError( 603, DEF_WARNING, "納品伝票マスタが存在しません", FALSE, "../inv/regist/renew.php?strSessionID=".$aryData["strSessionID"], $objDB );
            }
        }
        // 消費税率が同じかチェック
        $baseTax = null;
        foreach($aryCurTax as $tax){
            $baseTax = empty($baseTax) ? $tax : $baseTax;
            if($baseTax != $tax)
            {
                MoveToErrorPage("消費税率の異なる納品書は請求書の明細に混在できません");
            }
        }

        // --------------------------------
        //    登録処理
        // --------------------------------

        // 請求書番号に紐づいている売上マスタの請求書番号を空にする
        if (!fncUpdateInvoicenoToMSales($lngInvoiceNo, $objDB))
        {
            fncOutputError ( 9051, DEF_FATAL, "更新処理に伴う売上マスタテーブル処理失敗", TRUE, "", $objDB );
        }

        // 請求書マスタ・請求書明細・売上マスタを更新する
        $aryResult = fncInvoiceInsert( $insertData , $objDB, $objAuth);
        if (!$aryResult["result"])
        {
            fncOutputError ( 9051, DEF_FATAL, "更新処理に伴う売上マスタテーブル処理失敗", TRUE, "", $objDB );
        }

        // トランザクションコミット
        $objDB->transactionCommit();

        // 完了画面の表示
        $insertData["strAction"] = "/inv/renew.php?strSessionID=";
        $insertData["strSessionID"] = $aryData["strSessionID"];
        $insertData["time"]  = date('Y-m-d h:i:s');

        $insertData["strPreviewUrl"] = "/list/result/frameset.php?strSessionID=" 
        .$aryData["strSessionID"] ."&lngReportClassCode=6&strReportKeyCode=" .$aryResult["strReportKeyCode"];

        // 言語コード：日本語
        $insertData["lngLanguageCode"] = 1;

        // テンプレート読み込み
        $objTemplate = new clsTemplate();
        $objTemplate->getTemplate( "inv/regist/regist_result.tmpl" );

        // テンプレート生成
        $objTemplate->replace( $insertData );
        $objTemplate->complete();

        // HTML出力
        echo $objTemplate->strTemplate;

        $objDB->close();

        return true;
    }
    else
    {
        // $sql = fncGetSearchMSlipInvoiceNoSQL(2);
        
        $aryNewResult['strMode']      = 'renewPrev';
        // 明細検索面
        $aryNewResult["invConditionUrl"] = '/inv/regist/condition.php?strSessionID=' . $aryData["strSessionID"] . '&lngFunctionCode=' . $aryData["lngFunctionCode"] . '&lngApplicantUserCodeVisible=1&lngInputUserCodeVisible=1&dtmStartDateVisible=1&lngInChargeCodeVisible=1&lngWorkflowStatusCodeVisible=1&lngWorkflowStatusCodeConditions=1&lngSelectFunctionCode=500';

        // 入力値用に変換
        $aryNewResult['noTaxCurtTisMonthAmount'] = explode(" ", $aryNewResult['curThisMonthAmount'])[1];
        $aryNewResult['curThisMonthAmount']  = explode(" ", $aryNewResult['curSubTotal1'])[1];
        $aryNewResult['curLastMonthBalance'] = explode(" ", $aryNewResult['curLastMonthBalance'])[1];
        $aryNewResult['curTaxPrice1'] = explode(" ", $aryNewResult['curTaxPrice1'])[1];
        if ($aryNewResult['strInvoiceMode'] == '1') {
            $aryNewResult['invoiceMode1'] = 'checked';
        } else {            
            $aryNewResult['invoiceMode2'] = 'checked';
        }

        // テンプレート読み込み
        $objTemplate = new clsTemplate();
        $objTemplate->getTemplate( "inv/regist/renew.html" );
        // テンプレート生成
        $objTemplate->replace( $aryNewResult );
        $objTemplate->complete();
        
        // HTML出力
        echo $objTemplate->strTemplate;

        // テンプレート読み込み
        // echo fncGetReplacedHtmlWithBase("inv/base_inv.html", "inv/regist/renew.tmpl", $aryNewResult ,$objAuth );

    }


    $objDB->close();

    return true;


    // エラー画面への遷移
    function MoveToErrorPage($strMessage){

        // エラーメッセージの設定
        $aryHtml["strErrorMessage"] = $strMessage;

        // テンプレート読み込み
        $objTemplate = new clsTemplate();
        $objTemplate->getTemplate( "/result/error/parts.tmpl" );

        // テンプレート生成
        $objTemplate->replace( $aryHtml );
        $objTemplate->complete();

        // HTML出力
        echo $objTemplate->strTemplate;

        exit;
    }


?>

