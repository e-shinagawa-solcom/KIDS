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
    require SRC_ROOT . "pc/cmn/lib_pc.php";
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
            $strQuery = fncGetSearchMSlipSQL ($params, null, $objDB);
            // クエリ実行
            list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
            // 結果件数を取得
            if($lngResultNum > 0)
            {
                // レコード件数分走査
                for ($i = 0; $i < $lngResultNum; $i++)
                {

                    $resultDataSet = $objDB->fetchObject( $lngResultID, $i );
                    
                    // 検索結果レコードをオブジェクトで取得し必要なjsonデータに加工する
                    foreach($objDB->fetchObject( $lngResultID, $i ) as $column => $val)
                    {
                        $json[$i][$column] = $val;
                    }
                    // 売上区分名称の取得
                    $salesClassNameArry = fncGetSalesClassNameLst($resultDataSet->lngslipno, $resultDataSet->lngrevisionno, $objDB);

                    $json[$i]['strsalesclassname'] = $salesClassNameArry;
                }
			    $objDB->close();
	            // レスポンスヘッダ設定
	            header('Content-Type: application/json');
                echo json_encode($json);
                exit;
            }
            else
            {
            	$json["Message"] = "該当するレコードが見つかりませんでした";
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
        $revisionNo = (int)$aryData["revisionNo"];
        $strQuery = fncGetSearchMSlipInvoiceNoSQL($invoiceNo, $revisionNo);
        // クエリ実行
        list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
        // 結果件数を取得
        if($lngResultNum > 0)
        {
            // レコード件数分走査
            for ($i = 0; $i < $lngResultNum; $i++)
            {

                $resultDataSet= $objDB->fetchObject( $lngResultID, $i );
                // 検索結果レコードをオブジェクトで取得し必要なjsonデータに加工する
                foreach($objDB->fetchObject( $lngResultID, $i ) as $column => $val)
                {
                    // $json[$i][$column] = $val;
                    $json[$i][$column] = $val;
                }
                // 売上区分名称の取得
                $salesClassNameArry = fncGetSalesClassNameLst($resultDataSet->lngslipno, $resultDataSet->lngrevisionno, $objDB);

                $json[$i]['strsalesclassname'] = $salesClassNameArry;
            }
            
            $objDB->close();
            // レスポンスヘッダ設定
            header('Content-Type: application/json');
            echo json_encode($json);
        } else {
            echo "納品書マスタデータが取得できません。";
        }


        // $objDB->close();
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

    // 税マスタを取得
    $taxList = fncGetTaxInfo($aryData["actionDate"], $objDB);
    for ($i = 0; $i < count($taxList); $i++) {
        $optionValue = $taxList[$i]->curtax;
        $displayText = $taxList[$i]->curtax * 100 . "%"; // 小数点末尾の0をカット
        $strHtml .= "<OPTION VALUE=\"$optionValue\">$displayText</OPTION>\n";
    }
    $aryData["taxList"] = '<OPTION VALUE="">未選択</OPTION><OPTION VALUE="0.0000">0%</OPTION>' .$strHtml;
    // システムdateの月初・月末を求める
    $aryData["DeliveryFrom"] = date('Y/m/d', strtotime('first day of ' . null));
    $aryData["DeliveryTo"]   = date('Y/m/d', strtotime('last day of '  . null));

    // テンプレート読み込み
    echo fncGetReplacedHtmlWithBase("inv/base_condition.html", "inv/regist/condition.html", $aryData ,$objAuth );

    $objDB->close();

    return true;

?>

