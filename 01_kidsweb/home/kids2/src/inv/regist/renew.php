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
        fncOutputError ( 9052, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
    }

    // 2201 請求書発行
    if ( !fncCheckAuthority( DEF_FUNCTION_INV1, $objAuth ) )
    {
        fncOutputError ( 9052, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
    }

    // ヘルプ対応
    $aryData["lngFunctionCode"] = DEF_FUNCTION_INV0;

    // ユーザーコード取得
    $lngUserCode = $objAuth->UserCode;


    // 指定請求書番号の請求書マスタ取得用SQL文の作成
    $lngInvoiceNo = $aryData["lngInvoiceNo"];
    $strQuery     = fncGetInvoiceMSQL ( $lngInvoiceNo );

    // 詳細データの取得
    list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

    if ( $lngResultNum == 1 )
    {
        $aryResult = $objDB->fetchArray( $lngResultID, 0 );
    }
    else
    {
        fncOutputError( 9061, DEF_ERROR, "データが異常です", TRUE, "../inv/regist/renew.php?strSessionID=".$aryData["strSessionID"], $objDB );
    }

    $aryNewResult = fncSetInvoiceHeadTableData($aryResult);
    $aryNewResult['lngInvoiceNo'] = $aryData["lngInvoiceNo"];
    $aryNewResult['strSessionID'] = $aryData["strSessionID"];
    $aryNewResult['actionName']   = 'renew.php';

    // preview画面
    if(isset($aryData["strMode"]) && $aryData["strMode"] == 'renewPrev')
    {
        $aryNewResult['strMode']      = 'insertRenew';

        $aryPrevResult = array_merge($aryNewResult, fncSetPreviewTableData($aryData, $lngInvoiceNo, $objDB));
        // テンプレート読み込み
        $objTemplate = new clsTemplate ();
        $objTemplate->getTemplate ("inv/base_preview.html");


        // プレースホルダー置換
        // mb_convert_variables("utf8", "eucjp-win", $recordMoldReport);
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

        // --------------------------------
        //    修正可能かどうかのチェック
        // --------------------------------
        // 請求書明細に紐づ売上マスタの売上ステータスが締済みは不可
        if (fncSalesStatusIsClosed($lngInvoiceNo, $objDB))
        {
            MoveToErrorPage("締済みのため、修正できません");
        }

        // DB登録の為のデータ配列を返す
        $insertData = fncInvoiceInsertReturnArray($aryData, $aryResult, $objAuth, $objDB);

        // 出力明細が1件もない場合
        $slipCodeArray = $insertData['slipCodeArray'];
        if(count($slipCodeArray) < 0)
        {
            MoveToErrorPage("出力明細が選択されていません。");
        }

        for( $i=0; $i<COUNT($slipCodeArray); $i++ ) {
            $condition['strSlipCode'] = $slipCodeArray[$i];
            $strQuery = fncGetSearchMSlipSQL($condition, true, $objDB);
            // 明細データの取得
            list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
            if ( $lngResultNum )
            {
                for ( $j = 0; $j < $lngResultNum; $j++ )
                {
                    $Result = $objDB->fetchArray( $lngResultID, $j );
                    // 消費税率の配列
                    $aryCurTax[] = $Result['curtax'];
                    // 納品日
                    $aryDeliveryDate[] = $Result['dtmdeliverydate'];
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

        // 納品日
        $dtminvoicedate = $insertData['dtminvoicedate'];
        // 納品日の月
        $baseMonth = date('m', strtotime($dtminvoicedate));
        // システム日付で算出した締め日の前後1ヶ月以内
        $closeDay = fncGetCompanyClosedDay($insertData['strcustomercode'], $dtminvoicedate, $objDB);
        $baseDateTime = new DateTime($closeDay);
        foreach($aryDeliveryDate as $date){
            $deliveryDateTiem = new DateTime($date);
            $diff = $baseDateTime->diff($deliveryDateTiem);
            // 納品日がシステム日付の1か月前後でない場合
            if($diff->format('%a') > 30)
            {
                MoveToErrorPage("納品日は今月の前後1ヶ月の間を指定してください");
            }
            // 納品日と異なる月の明細の場合
            $deliveryDateMonth = date('m', strtotime($date));
            if( (int)$baseMonth != (int)$deliveryDateMonth )
            {
                MoveToErrorPage("出力明細には、入力された納品日と異なる月に納品された明細を指定できません");
            }
        }

        // --------------------------------
        //    登録処理
        // --------------------------------
        // トランザクション開始
        $objDB->transactionBegin();

        // 請求書番号に紐づいている売上マスタの請求書番号を空にする
        if (!fncUpdateInvoicenoToMSales($lngInvoiceNo, $objDB))
        {
            fncOutputError ( 9051, DEF_FATAL, "更新処理に伴う売上マスタテーブル処理失敗", TRUE, "", $objDB );
        }

        // 請求書マスタ・請求書明細・売上マスタを更新する
        if (!fncInvoiceInsert( $insertData , $objDB))
        {
            fncOutputError ( 9051, DEF_FATAL, "更新処理に伴う売上マスタテーブル処理失敗", TRUE, "", $objDB );
        }

        // トランザクションコミット
        $objDB->transactionCommit();

        // 完了画面の表示
        $insertData["strAction"] = "/inv/renew.php?strSessionID=";
        $insertData["strSessionID"] = $aryData["strSessionID"];
        $insertData["time"]  = date('Y-m-d h:i:s');

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
        $sql = fncGetSearchMSlipInvoiceNoSQL(2);
        $aryNewResult['strMode']      = 'renewPrev';
        // 明細検索面
        $aryNewResult["invConditionUrl"] = '/inv/regist/condition.php?strSessionID=' . $aryData["strSessionID"] . '&lngFunctionCode=' . $aryData["lngFunctionCode"] . '&lngApplicantUserCodeVisible=1&lngInputUserCodeVisible=1&dtmStartDateVisible=1&lngInChargeCodeVisible=1&lngWorkflowStatusCodeVisible=1&lngWorkflowStatusCodeConditions=1&lngSelectFunctionCode=500';

        // 入力値用に変換
        $aryNewResult['curThisMonthAmount']  = (int)preg_replace('/,/', '', $aryNewResult['curThisMonthAmount']);
        $aryNewResult['curLastMonthBalance'] = (int)preg_replace('/,/', '', $aryNewResult['curLastMonthBalance']);
        $aryNewResult['curSubTotal1'] = (int)preg_replace('/,/', '', $aryNewResult['curSubTotal1']);
        $aryNewResult['curTaxPrice1'] = (int)preg_replace('/,/', '', $aryNewResult['curTaxPrice1']);


        // テンプレート読み込み
        echo fncGetReplacedHtmlWithBase("inv/base_inv.html", "inv/regist/renew.tmpl", $aryNewResult ,$objAuth );

    }


    $objDB->close();

    return true;


    // エラー画面への遷移
    function MoveToErrorPage($strMessage){

        // 言語コード：日本語
        $aryHtml["lngLanguageCode"] = 1;

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

