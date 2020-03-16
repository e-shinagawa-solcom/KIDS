<?php

// ----------------------------------------------------------------------------
/**
 *       請求管理  請求書登録画面
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
 *         ・登録時の入力画面を表示
 *
 *       更新履歴
 *
 */
// ----------------------------------------------------------------------------


    // 設定読み込み
    include_once('conf.inc');

    // ライブラリ読み込み
    require (LIB_FILE);
    require (LIB_EXCLUSIVEFILE);
    require (SRC_ROOT . "m/cmn/lib_m.php");
    require (SRC_ROOT . "inv/cmn/lib_regist.php");
    require_once(LIB_DEBUGFILE);

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

    // セッション確認
    $objAuth = fncIsSession($aryData["strSessionID"], $objAuth, $objDB);

    // cookieにSET
    if( !empty($aryData["strSessionID"]) )
        setcookie("strSessionID", $aryData["strSessionID"], 0, "/");


    // 文字列チェック
    $aryCheck["strSessionID"]   = "null:numenglish(32,32)";
    $aryResult = fncAllCheck( $aryData, $aryCheck );
    fncPutStringCheckError( $aryResult, $objDB );

    // 2200 請求管理
    if ( !fncCheckAuthority( DEF_FUNCTION_INV0, $objAuth ) )
    {
        fncOutputError ( 9060, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
    }

    // 2201 請求書発行
    if ( !fncCheckAuthority( DEF_FUNCTION_INV1, $objAuth ) )
    {
        fncOutputError ( 9060, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
    }

    // ヘルプ対応
    $aryData["lngFunctionCode"] = DEF_FUNCTION_INV0;

    // ユーザーコード取得
    $lngUserCode = $objAuth->UserID;

    // preview画面
    if(isset($aryData["strMode"]) && $aryData["strMode"] == 'prev')
    {
        $aryNewResult = fncSetInvoiceHeadTableData($aryResult);
        $aryNewResult['strSessionID'] = $aryData["strSessionID"];
        $aryNewResult['slipNoList'] = $aryData["slipNoList"];
        $aryNewResult['revisionNoList'] = $aryData["revisionNoList"];
        $aryNewResult['strSessionID'] = $aryData["strSessionID"];
        $aryNewResult['actionName']   = 'index.php';
        $aryNewResult['strMode']      = 'insert';

        $aryPrevResult = array_merge($aryNewResult, fncSetPreviewTableData($aryData, null, $objDB));

        // テンプレート読み込み
        $objTemplate = new clsTemplate ();
        $objTemplate->getTemplate ("inv/base_preview.html");


        // プレースホルダー置換
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
    elseif(isset($aryData["strMode"]) && $aryData["strMode"] == 'insert')
    {
        // *****************************************************
        //   INSERT処理実行（Submit時）
        // *****************************************************

        // トランザクション開始
        $objDB->transactionBegin();
        
        // DB登録の為のデータ配列を返す
        $insertData = fncInvoiceInsertReturnArray($aryData, $aryResult, $objAuth, $objDB);
        // 出力明細が1件もない場合
        $slipNoArray = $insertData['slipNoArray'];
        $revisionNoArray = $insertData['revisionNoArray'];
        if(count($slipNoArray) < 0)
        {
            MoveToErrorPage("出力明細が選択されていません。");
        }
        $slipCodeArray = $insertData['slipCodeArray'];


        for( $i=0; $i<COUNT($slipNoArray); $i++ ) {
            if( !lockSlip($slipNoArray[$i], $objDB) ){
                //fncOutputError ( 9051, DEF_ERROR, "登録対象納品書データのロックに失敗しました", TRUE, "", $objDB );
                MoveToErrorPage("登録対象納品書データのロックに失敗しました");
            }
            if( isSlipModified($slipNoArray[$i], $revisionNoArray[$i], $objDB) ){
                //fncOutputError ( 9051, DEF_ERROR, "登録対象納品書データが削除または更新されています", TRUE, "", $objDB );
                MoveToErrorPage("登録対象納品書データが削除または更新されています");
            }
            $condition['strSlipCode'] = $slipCodeArray[$i];
            $strQuery = fncGetSearchMSlipSQL($condition, null, $objDB);
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
                //fncOutputError( 9051, DEF_ERROR, "対象の納品書データに請求済データが含まれます", TRUE, "", $objDB );
                MoveToErrorPage("対象の納品書データに請求済データが含まれます");
            }
        }
        // 消費税率が同じかチェック
        $baseTax = null;
        if( !is_array($aryCurTax) )
        {
            $aryCurTax[] = $aryCurTax;
        }
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
        // 締め日の取得に失敗
        if(empty($closeDay))
        {
            MoveToErrorPage("締め日の取得ができませんでした。");
        }

        // $baseDateTime = new DateTime($closeDay);
        // foreach($aryDeliveryDate as $date){
        //     $deliveryDateTiem = new DateTime($date);
        //     $diff = $baseDateTime->diff($deliveryDateTiem);
        //     // 納品日がシステム日付の1か月前後でない場合
        //     if($diff->format('%a') > 30)
        //     {
        //         MoveToErrorPage("納品日は当月度の前後1ヶ月の間を指定してください");
        //     }
        //     // 納品日と異なる月の明細の場合
        //     $deliveryDateMonth = date('m', strtotime($date));
        //     if( (int)$baseMonth != (int)$deliveryDateMonth )
        //     {
        //         MoveToErrorPage("出力明細には、入力された納品日と異なる月に納品された明細を指定できません");
        //     }
        // }

        // --------------------------------
        //    登録処理
        // --------------------------------

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
        // 起票者が未選択は登録者を入れる
        if(empty($aryData['lngInputUserCode']))
        {
            $aryData['lngInputUserCode'] = $lngUserCode;
            $aryData['lngInputUserName'] = $objAuth->UserFullName;
        }
        if(empty($aryData['ActionDate']))
        {
            $aryData['ActionDate'] = date('Y/m/d');
        }
        // 明細検索面
        $aryData["invConditionUrl"] = '/inv/regist/condition.php?strSessionID=' . $aryData["strSessionID"] . '&lngFunctionCode=' . $aryData["lngFunctionCode"] . '&lngApplicantUserCodeVisible=1&lngInputUserCodeVisible=1&dtmStartDateVisible=1&lngInChargeCodeVisible=1&lngWorkflowStatusCodeVisible=1&lngWorkflowStatusCodeConditions=1&lngSelectFunctionCode=500';

        // テンプレート読み込み
        echo fncGetReplacedHtmlWithBase("base_sc.html", "inv/regist/index.html", $aryData ,$objAuth );

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

