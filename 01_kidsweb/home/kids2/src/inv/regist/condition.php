<?php

// ----------------------------------------------------------------------------
/**
 *       請求管理  明細書検索画面
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
 *         ・明細書詳細を検索する
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
    require_once (SRC_ROOT.'/cmn/exception/SQLException.class.php');
	require_once (LIB_DEBUGFILE);

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

    // セッション確認
    $objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

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

    // ajax 検索
    if(isset($aryData["mode"]) && $aryData["mode"] == 'ajax')
    {
        // 検索条件を含んでいる場合
        if(array_key_exists("conditions", $_POST) && count($_POST["conditions"]))
        {

            // クエリパラメータの作成
            foreach ($_POST["conditions"] as $key=>$condition)
            {
                $params[$key] = pg_escape_string($condition);
            }

            // SQL文取得
            $strQuery = fncGetSearchMSlipSQL ($params, false, $objDB);
//             error_log($strQuery,"3",LOG_FILE);
            // EUC-JPへ変換
//            $strQuery = mb_convert_encoding($strQuery, "EUC-JP", "auto");

            // クエリ実行
            list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

            // 結果件数を取得
            if($lngResultNum > 0)
            {
                // レコード件数分走査
                for ($i = 0; $i < $lngResultNum; $i++)
                {

    //                 $resultDataSet[] = pg_fetch_array($objResult, $i, PGSQL_ASSOC);
                    $resultDataSet[] = $objDB->fetchObject( $lngResultID, $i );
    //                 $resultDataSet[] = $objDB->fetchArray( $lngResultID, $i );
                    // 検索結果レコードをオブジェクトで取得し必要なjsonデータに加工する
                    foreach($objDB->fetchObject( $lngResultID, $i ) as $column => $val)
                    {
                        $json[$i][mb_convert_encoding($column,"UTF-8","auto")] = mb_convert_encoding($val,"UTF-8","auto");
                    }
//                     $json[$i]['sql'] = $strQuery;
                }

                // json変換の為、一時的にUTF-8へ変換
//                mb_convert_variables('UTF-8', 'euc-jp', $json);
			    $objDB->close();
	            // レスポンスヘッダ設定
	            header('Content-Type: application/json');
                echo json_encode($json);
                exit;
            }
            else
            {
            	$json[mb_convert_encoding("Message","UTF-8", "auto")] = mb_convert_encoding("該当するレコードが見つかりませんでした", "UTF-8", "auto");
                echo  json_encode($json);
            }
        }
        // 結果が得られなかった(クエリに失敗した)場合
        else
        {
            throw new SQLException(
                "問い合わせに失敗しました",
                $strQuery,
                $params);
        }

	    $objDB->close();
	    return true;
    }
    else if(isset($aryData["mode"]) && $aryData["mode"] == 'ajaxRenew')
    {
        // SQL文取得
        $invoiceNo = (int)$aryData["invoiceNo"];
        $strQuery = fncGetSearchMSlipInvoiceNoSQL($invoiceNo);
        // EUC-JPへ変換
        $strQuery = mb_convert_encoding($strQuery, "EUC-JP", "auto");
        // クエリ実行
        list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

        // 結果件数を取得
        if($lngResultNum > 0)
        {
            // レコード件数分走査
            for ($i = 0; $i < $lngResultNum; $i++)
            {

                //                 $resultDataSet[] = pg_fetch_array($objResult, $i, PGSQL_ASSOC);
                $resultDataSet[] = $objDB->fetchObject( $lngResultID, $i );
                //                 $resultDataSet[] = $objDB->fetchArray( $lngResultID, $i );
                // 検索結果レコードをオブジェクトで取得し必要なjsonデータに加工する
                foreach($objDB->fetchObject( $lngResultID, $i ) as $column => $val)
                {
                    $json[$i][$column] = $val;
                }
            }

            // レスポンスヘッダ設定
//            header('Content-Type: application/json');
            // json変換の為、一時的にUTF-8へ変換
            mb_convert_variables('UTF-8', 'eucjp-win', $json);

            echo json_encode($json);
        }


        $objDB->close();
        return true;

    }

    // 明細検索面
    $aryData["invConditionUrl"] = '/inv/regist/condition.php?strSessionID=' . $aryData["strSessionID"] . '&lngFunctionCode=' . $aryData["lngFunctionCode"] . '&lngApplicantUserCodeVisible=1&lngInputUserCodeVisible=1&dtmStartDateVisible=1&lngInChargeCodeVisible=1&lngWorkflowStatusCodeVisible=1&lngWorkflowStatusCodeConditions=1&lngSelectFunctionCode=500';

    // 通貨単位マスタを取得
    $strQuery = "SELECT lngmonetaryunitcode, strmonetaryunitsign FROM m_monetaryunit ORDER BY lngmonetaryunitcode";
    $aryData["monetaryunitList"] = fncGetPulldownQueryExec($strQuery, 1, $objDB, false);
    // 税区分マスタを取得
    $strQuery = "SELECT lngtaxclasscode, strtaxclassname FROM m_taxclass ORDER BY lngtaxclasscode";
    $taxclassList = fncGetPulldownQueryExec($strQuery, 0, $objDB, false);
    $aryData["taxclassList"] = '<OPTION VALUE="0">未選択</OPTION>' .$taxclassList;

    // システムdateの月初・月末を求める
    $aryData["DeliveryFrom"] = date('Y/m/d', strtotime('first day of ' . null));
    $aryData["DeliveryTo"]   = date('Y/m/d', strtotime('last day of '  . null));

    // テンプレート読み込み
//     echo fncGetReplacedHtml( "/inv/regist/condition.tmpl", $aryData, $objAuth );
    // テンプレート読み込み
    echo fncGetReplacedHtmlWithBase("inv/base_condition.html", "inv/regist/condition.tmpl", $aryData ,$objAuth );

    $objDB->close();

    return true;

?>

