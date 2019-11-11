<?php

// ----------------------------------------------------------------------------
/**
 *       請求管理  請求書削除画面
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
 *         ・請求書削除画面を表示
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
    require (SRC_ROOT . "inv/cmn/column.php");

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

    // 文字列チェック
    $aryCheck["strSessionID"] = "null:numenglish(32,32)";
//     $aryCheck["lngInvoiceNo"] = "null:number(0,10)";

    // セッション確認
    $objAuth = fncIsSession($aryData["strSessionID"], $objAuth, $objDB);

    // cookieにSET
    if( !empty($aryData["strSessionID"]) )
        setcookie("strSessionID", $aryData["strSessionID"], 0, "/");

    // 2200 請求管理
    if ( !fncCheckAuthority( DEF_FUNCTION_INV0, $objAuth ) )
    {
        fncOutputError ( 9052, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
    }

    // 2202 請求書検索
    if ( !fncCheckAuthority( DEF_FUNCTION_INV2, $objAuth ) )
    {
        fncOutputError ( 9052, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
    }


    // 削除対象の請求書番号のマスタデータ確認
    $lngInvoiceNo = $aryData["lngInvoiceNo"];
    $lngRevisionNo = $aryData["lngRevisionNo"];

    // 指定請求書番号の請求書マスタ取得用SQL文の作成
    $strQuery = fncGetInvoiceMSQL ( $lngInvoiceNo, $lngRevisionNo );

    // 詳細データの取得
    list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

    if ( $lngResultNum == 1 )
    {
        $aryResult = $objDB->fetchArray( $lngResultID, 0 );
    }
    else
    {
        fncOutputError( 9061, DEF_ERROR, "データが異常です", TRUE, "../inv/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
    }


    $objDB->freeResult( $lngResultID );

    // *****************************************************
    //   削除処理実行（Submit時）
    // *****************************************************
    if( $aryData["strSubmit"] )
    {
        // --------------------------------
        //    削除可能かどうかのチェック
        // --------------------------------
        // 請求書明細に紐づ売上マスタの売上ステータスが締済みは削除不可
        if (fncSalesStatusIsClosed($lngInvoiceNo, $objDB))
        {
            MoveToErrorPage("締済みのため、削除できません");
        }

        // --------------------------------
        //    削除処理
        // --------------------------------
        // トランザクション開始
        $objDB->transactionBegin();

        // 請求書マスタを削除
        if (!fncDeleteInvoice($lngInvoiceNo, $lngRevisionNo, $objDB, $objAuth))
        {
           fncOutputError ( 9051, DEF_FATAL, "削除処理に伴う請求書マスタ処理失敗", TRUE, "", $objDB );
        }

        // 請求書番号に紐づいている売上マスタの請求書番号を空にする
        if (!fncUpdateInvoicenoToMSales($lngInvoiceNo, $objDB))
        {
            fncOutputError ( 9051, DEF_FATAL, "削除処理に伴う売上マスタテーブル処理失敗", TRUE, "", $objDB );
        }

        // トランザクションコミット
        $objDB->transactionCommit();

        // 削除完了画面の表示
        $aryDeleteData = $aryHeadResult;
        $aryDeleteData["strAction"] = "/inv/search/index.php?strSessionID=";
        $aryDeleteData["strSessionID"] = $aryData["strSessionID"];

        // 言語コード：日本語
        $aryDeleteData["lngLanguageCode"] = 1;

        // テンプレート読み込み
        $objTemplate = new clsTemplate();
        $objTemplate->getTemplate( "inv/result/delete_result.tmpl" );

        // テンプレート生成
        $objTemplate->replace( $aryDeleteData );
        $objTemplate->complete();

        // HTML出力
        echo $objTemplate->strTemplate;

        $objDB->close();

        return true;
    }

    // 取得データを表示用に整形
    // 顧客名・顧客社名・顧客コード
    list ($aryResult['printCustomerName'], $aryResult['printCompanyName'], $aryResult['lngCustomerCodeForCompaany']) = fncGetCompanyPrintName( $aryResult['strcustomercode'] ,$objDB);

    $aryNewResult = fncSetInvoiceHeadTableData ( $aryResult );

    // ヘッダ部のカラム名の設定（キーの頭に"CN"を付与する）
    $aryHeadColumnNames_CN = fncAddColumnNameArrayKeyToCN ( $aryHeadColumnNames );
    // 明細部のカラム名の設定（キーの頭に"CN"を付与する）
    $aryDetailColumnNames_CN = fncAddColumnNameArrayKeyToCN ( $aryDetailColumnNames );

    // 請求書明細データ取得
    $strQuery = fncGetSearchInvoiceDetailSQL($lngInvoiceNo , $aryResult['lngrevisionno']);

    // 明細データの取得
    list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

    if ( $lngResultNum )
    {
        for ( $i = 0; $i < $lngResultNum; $i++ )
        {
            $aryDetailResult[] = $objDB->fetchArray( $lngResultID, $i );
        }
    }
    else
    {
        $strMessage = fncOutputError( 603, DEF_WARNING, "請求書番号に対する明細情報が見つかりません。", FALSE, "../inv/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
    }

    $objDB->freeResult( $lngResultID );

    for ( $i = 0; $i < count($aryDetailResult); $i++)
    {
        $aryNewDetailResult[$i] = fncSetInvoiceDetailTableData ( $aryDetailResult[$i], $aryNewResult );

        // テンプレート読み込み
        $objTemplate = new clsTemplate();
        $objTemplate->getTemplate( "inv/result/parts_detail.tmpl" );

        // テンプレート生成
        $objTemplate->replace( $aryDetailColumnNames_CN );
        $objTemplate->replace( $aryNewDetailResult[$i] );
        $objTemplate->complete();

        // HTML出力
        $aryDetailTable[] = $objTemplate->strTemplate;
    }

    $aryNewResult["strDetailTable"] = implode ("\n", $aryDetailTable );

    // テンプレート読み込み
    $objTemplate = new clsTemplate();
    $objTemplate->getTemplate( "inv/result/parts3.tmpl" );
//     $objTemplate->getTemplate( "sc/result2/parts_detail.tmpl" );

    // 画面タイトル
    $aryNewResult['Title'] = "削除確認";
    // 明細枚数
    $aryNewResult['detailCount'] = count($aryDetailResult);

    $aryNewResult["strSessionID"] = $aryData["strSessionID"];
    $aryNewResult["strAction"]    = "index3.php";
    $aryNewResult["strSubmit"]    = "submit";
    $aryNewResult["strMode"] = "delete";

    // テンプレート生成
    $objTemplate->replace( $aryNewResult );
    $objTemplate->replace( $aryHeadColumnNames_CN );
    $objTemplate->complete();

    // HTML出力
    echo $objTemplate->strTemplate;

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

