<?php

// ----------------------------------------------------------------------------
/**
 *       仕入管理  登録確認
 *
 *       処理概要
 *         ・登録した仕入情報を登録確認画面に表示する処理
 *
 *       更新履歴
 *
 */
// ----------------------------------------------------------------------------

include 'conf.inc';
require LIB_FILE;
include 'JSON.php';

//////////////////////////////////////////////////////////////////////////
// GETデータ取得
//////////////////////////////////////////////////////////////////////////
$postdata = file_get_contents("php://input");
$aryData = json_decode($postdata, true);
$objDB = new clsDB();
$objAuth = new clsAuth();
$objDB->open("", "", "", "");

//値が存在しない場合は通常の POST で受ける
if ($aryData == null) {
    $aryData = $_POST;
}
$aryDetailData = json_decode($aryData["detailData"], true);
$lngPurchaseOrderNo = json_decode($aryData["lngPurchaseOrderNo"], true);
$lngPurchaseOrderRevisionNo = json_decode($aryData["lngPurchaseOrderRevisionNo"], true);

// セッション確認
$objAuth = fncIsSession($aryData["strSessionID"], $objAuth, $objDB);

// 700 仕入管理
if (!fncCheckAuthority(DEF_FUNCTION_PC0, $objAuth)) {
    fncOutputError(9060, DEF_WARNING, "アクセス権限がありません。", true, "", $objDB);
}

// 701 仕入管理（ 仕入登録）
if (fncCheckAuthority(DEF_FUNCTION_PC1, $objAuth)) {
    $aryData["strRegistURL"] = "pc/regist/index.php?strSessionID=" . $aryData["strSessionID"];
}

$lngUserCode = $objAuth->UserCode;
$strUserName = $objAuth->UserDisplayName;

$curtotalprice = 0;
// 明細情報の出力
for ($i = 0; $i < count($aryDetailData); $i++) {
    // 明細行
    $aryQuery = array();
    $aryQuery[] = "SELECT ";
    $aryQuery[] = "od.lngorderdetailno, "; // 発注番号
    $aryQuery[] = "od.lngorderno, "; // 発注明細番号
    $aryQuery[] = "od.lngrevisionno, "; // リビジョン番号
    $aryQuery[] = "od.strproductcode || '_' || od.strrevisecode as strproductcode, "; // 製品コード
    $aryQuery[] = "p.strproductname, "; // 製品名称
    $aryQuery[] = "od.lngstocksubjectcode, "; // 仕入科目コード
    $aryQuery[] = "ss.strstocksubjectname, "; // 仕入科目名称
    $aryQuery[] = "c.strcompanydisplaycode, "; // 仕入先コード
    $aryQuery[] = "c.strcompanydisplayname, "; // 仕入先名称
    $aryQuery[] = "od.lngstockitemcode, "; // 仕入部品コード
    $aryQuery[] = "si.strstockitemname, "; // 仕入部品名称
	$aryQuery[] = "g.strGroupDisplayCode AS lnggroupcode, ";
	$aryQuery[] = "g.strGroupDisplayName AS strgroupname, ";
	$aryQuery[] = "u.strUserDisplayCode AS lngusercode, ";
	$aryQuery[] = "u.strUserDisplayName AS strusername, ";
    $aryQuery[] = "To_char( od.dtmdeliverydate, 'YYYY/mm/dd' ) as dtmdeliverydate, "; // 納品日
    $aryQuery[] = "pod.lngdeliverymethodcode as lngdeliverymethodcode, "; // 運搬方法コード
    $aryQuery[] = "dm.strdeliverymethodname as strdeliverymethodname, "; // 運搬方法名称
    $aryQuery[] = "od.lngconversionclasscode, "; // 換算区分コード / 1：単位計上/ 2：荷姿単位計上
    $aryQuery[] = "od.curproductprice as curproductprice, "; // 製品価格
    $aryQuery[] = "od.lngproductquantity, "; // 製品数量
    $aryQuery[] = "od.lngproductunitcode, "; // 製品単位コード
    $aryQuery[] = "pu.strproductunitname, "; // 製品単位名称
/*
    $aryQuery[] = "od.lngtaxclasscode, "; // 消費税区分コード
    $aryQuery[] = "od.lngtaxcode, "; // 消費税コード
    $aryQuery[] = "od.curtaxprice, "; // 消費税金額
*/
    $aryQuery[] = "od.cursubtotalprice as cursubtotalprice, "; // 小計金額
    $aryQuery[] = "od.strnote as strdetailnote,"; // 備考
    $aryQuery[] = "od.strmoldno as strmoldno, "; // シリアル
    $aryQuery[] = "o.lngorderstatuscode as lngorderstatuscode, "; // 発注ステータス
    $aryQuery[] = "os.strorderstatusname as strorderstatusname, "; // 発注ステータス
    $aryQuery[] = "o.lngmonetaryunitcode as lngmonetaryunitcode, "; // 通貨単位コード
    $aryQuery[] = "mu.strmonetaryunitname as strmonetaryunitname, "; // 通貨単位名称
    $aryQuery[] = "mu.strmonetaryunitsign as strmonetaryunitsign, "; // 通貨単位名称
    $aryQuery[] = "c.lngcountrycode as lngcountrycode, "; // 国コード
    $aryQuery[] = "o.lngmonetaryratecode as lngmonetaryratecode, "; // 通貨レートコード
    $aryQuery[] = "o.lngpayconditioncode as lngpayconditioncode "; // 支払条件
    $aryQuery[] = "FROM t_orderdetail od";
    $aryQuery[] = "inner join  ";
    $aryQuery[] = "(";
    $aryQuery[] = "select";
    $aryQuery[] = " lngorderno, lngrevisionno,lngorderstatuscode, lngmonetaryunitcode,lnggroupcode,lngusercode,";
    $aryQuery[] = " lngmonetaryratecode, lngcustomercompanycode, lngpayconditioncode ";
    $aryQuery[] = "FROM";
    $aryQuery[] = "  m_order ";
    $aryQuery[] = "WHERE";
    $aryQuery[] = "  lngorderno = " . $aryDetailData[$i]["lngOrderNo"] . " ";
    $aryQuery[] = "  AND lngrevisionno = " . $aryDetailData[$i]["lngRevisionNo"] . " ";
    $aryQuery[] = "  AND bytinvalidflag = false ";
    $aryQuery[] = ") o on o.lngorderno = od.lngorderno";
    $aryQuery[] = " AND o.lngrevisionno = od.lngrevisionno";
    $aryQuery[] = " INNER JOIN m_product p on p.strproductcode = od.strproductcode and p.strrevisecode = od.strrevisecode";
    $aryQuery[] = " INNER JOIN (SELECT strproductcode,strrevisecode,MAX(lngrevisionno) AS lngrevisionno FROM m_product GROUP BY strproductcode,strrevisecode) max_p_rev";
    $aryQuery[] = " on max_p_rev.strproductcode = p.strproductcode and max_p_rev.strrevisecode = p.strrevisecode and max_p_rev.lngrevisionno = p.lngrevisionno";
    $aryQuery[] = " INNER JOIN t_purchaseorderdetail pod";
    $aryQuery[] = " ON pod.lngorderno = od.lngorderno";
    $aryQuery[] = " and pod.lngorderdetailno = od.lngorderdetailno";
    $aryQuery[] = " and pod.lngorderrevisionno = od.lngrevisionno";
    $aryQuery[] = " INNER JOIN ( ";
    $aryQuery[] = " SELECT lngpurchaseorderno, lngpurchaseorderdetailno, MAX(lngrevisionno) as lngrevisionno";
    $aryQuery[] = " FROM t_purchaseorderdetail GROUP BY lngpurchaseorderno, lngpurchaseorderdetailno";
    $aryQuery[] = " ) pod_rev_max";
    $aryQuery[] = " on pod_rev_max.lngpurchaseorderno = pod.lngpurchaseorderno";
    $aryQuery[] = " and pod_rev_max.lngpurchaseorderdetailno = pod.lngpurchaseorderdetailno";
    $aryQuery[] = " and pod_rev_max.lngrevisionno = pod.lngrevisionno";
    $aryQuery[] = " INNER JOIN m_purchaseorder mpo";
    $aryQuery[] = " on mpo.lngpurchaseorderno = pod.lngpurchaseorderno";
    $aryQuery[] = " AND mpo.lngrevisionno = pod.lngrevisionno";
    $aryQuery[] = " INNER JOIN (";
    $aryQuery[] = " SELECT lngpurchaseorderno, MAX(lngrevisionno) as max_rev, MIN(lngrevisionno) as min_rev";
    $aryQuery[] = " FROM m_purchaseorder";
    $aryQuery[] = " GROUP BY lngpurchaseorderno";
    $aryQuery[] = " ) mpo_rev";
    $aryQuery[] = " on mpo_rev.lngpurchaseorderno = mpo.lngpurchaseorderno";
    $aryQuery[] = " AND mpo_rev.max_rev = mpo.lngrevisionno";
    $aryQuery[] = " AND mpo_rev.min_rev >= 0";
    $aryQuery[] = " LEFT JOIN m_stocksubject ss on ss.lngstocksubjectcode = od.lngstocksubjectcode";
    $aryQuery[] = " LEFT JOIN m_stockitem si on si.lngstocksubjectcode = od.lngstocksubjectcode and si.lngstockitemcode = od.lngstockitemcode";
    $aryQuery[] = " LEFT JOIN m_monetaryunit mu on mu.lngmonetaryunitcode = o.lngmonetaryunitcode";
    $aryQuery[] = " LEFT JOIN m_orderstatus os on os.lngorderstatuscode = o.lngorderstatuscode";
    $aryQuery[] = " LEFT JOIN m_productunit pu on pu.lngproductunitcode = od.lngproductunitcode";
    $aryQuery[] = " LEFT JOIN m_deliverymethod dm on dm.lngdeliverymethodcode = pod.lngdeliverymethodcode";
    $aryQuery[] = " LEFT JOIN m_company c on c.lngcompanycode = o.lngcustomercompanycode";
	$aryQuery[] = " LEFT JOIN m_Group g on p.lnginchargegroupcode = g.lnggroupcode";
	$aryQuery[] = " LEFT JOIN m_User u on p.lnginchargeusercode = u.lngusercode";
    $aryQuery[] = " WHERE od.lngorderno = " . $aryDetailData[$i]["lngOrderNo"] . " ";
    $aryQuery[] = "  AND od.lngrevisionno = " . $aryDetailData[$i]["lngRevisionNo"] . " ";
    $aryQuery[] = "  AND od.lngorderdetailno = " . $aryDetailData[$i]["lngOrderDetailNo"] . " ";
    $aryQuery[] = " ORDER BY od.lngSortKey";
    $strQuery = implode("\n", $aryQuery);
// echo $strQuery . "<br>";
    list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);
    if ($lngResultNum) {
        if ($lngResultNum == 1) {
            $detailDataResult = $objDB->fetchArray($lngResultID, 0);
            // $aryData["dtmexpirationdate"] = $detailDataResult["dtmexpirationdate"];
            $aryNewResult["lngGroupCode"] = $detailDataResult["lnggroupcode"];
            $aryNewResult["strGroupName"] = $detailDataResult["strgroupname"];
            $aryNewResult["lngUserCode"] = $detailDataResult["lngusercode"];
            $aryNewResult["strUserName"] = $detailDataResult["strusername"];
            // 明細行番号
            $detailDataResult["lngstockdetailno"] = $i+1;
            // 数量
            $detailDataResult["lngproductquantity"] = number_format($detailDataResult["lngproductquantity"]);
            // 単価
            $detailDataResult["curproductprice"] = convertPrice($detailDataResult["lngmonetaryunitcode"], $detailDataResult["strmonetaryunitsign"], $detailDataResult["curproductprice"], 'unitprice');
            // 小計金額
            $cursubtotalprice = $detailDataResult["cursubtotalprice"];
            $detailDataResult["cursubtotalprice"] = convertPrice($detailDataResult["lngmonetaryunitcode"], $detailDataResult["strmonetaryunitsign"], $detailDataResult["cursubtotalprice"], 'price');
            // 税率
            $detailDataResult["curTax"] = $aryDetailData[$i]["curTax"];
            // 消費税区分
            $detailDataResult["lngtaxclasscode"] = $aryDetailData[$i]["lngTaxClassCode"];
            // 消費税区分名称
            $detailDataResult["strtaxclassname"] = $aryDetailData[$i]["strTaxClassName"];
            // 消費税額
            $detailDataResult["curtaxprice"] = convertPrice($detailDataResult["lngmonetaryunitcode"], $detailDataResult["strmonetaryunitsign"], $aryDetailData[$i]["curTaxPrice"], 'taxprice');
            // 合計金額の設定(小計金額の合計)
            $curtotalprice += $cursubtotalprice;
        }
    }


    // テンプレート読み込み
    $objTemplate = new clsTemplate();
    $objTemplate->getTemplate("pc/regist/pc_parts_confirm_regist.html");
    // テンプレート生成
    $objTemplate->replace($detailDataResult);
    $objTemplate->complete();

    // HTML出力
    $aryDetailTable[] = $objTemplate->strTemplate;
}

// 合計金額の設定
$strmonetaryunitsign = fncGetMasterValue("m_monetaryunit", "lngmonetaryunitcode", "strmonetaryunitsign", $aryData["lngMonetaryUnitCode"], '', $objDB);
$aryNewResult["curtotalprice"] = convertPrice($aryData["lngMonetaryUnitCode"], $strmonetaryunitsign, $curtotalprice, 'price');
$aryData["curTotalPrice"] =   $curtotalprice;        
$aryNewResult["strDetailTable"] = implode ("\n", $aryDetailTable );
// 作成日
$aryData["dtminsertdate"] = date('Y/m/d', time());
// 入力コード
$aryData["lnginputusercode"] = $lngUserCode;
// 入力者名称
$aryNewResult["strinputusername"] = $strUserName;

$aryData["lngPurchaseOrderNo"] = $lngPurchaseOrderNo;
$aryData["lngpurchaserevisionno"] = $lngPurchaseOrderRevisionNo;

// テンプレート読み込み
$objTemplate = new clsTemplate();
$objTemplate->getTemplate("pc/regist/pc_confirm_regist.html");
// テンプレート生成
$objTemplate->replace($aryNewResult);
$objTemplate->replace($aryData);
$objTemplate->complete();

// 検索結果テーブル生成の為DOMDocumentを使用
$doc = new DOMDocument();
// パースエラー抑制
libxml_use_internal_errors(true);
// DOMパース
$doc->loadHTML($objTemplate->strTemplate);
// パースエラークリア
libxml_clear_errors();
// パースエラー抑制解除
libxml_use_internal_errors(false);

// 検索結果テーブルの取得
$tableDetail = $doc->getElementById("tbl_stock_info");
$tbodyDetail = $tableDetail->getElementsByTagName("tbody")->item(0);

$aryData["lngGroupCode"] = $aryNewResult["lngGroupCode"];
$aryData["lngUserCode"] = $aryNewResult["lngUserCode"];

// 明細情報の出力
$num = 0;
foreach ($aryData as $key => $value) {
    // tbody > tr要素作成
    $trBody = $doc->createElement("tr");
    // key
    $td = $doc->createElement("td", $key);
    $trBody->appendChild($td);
    // value
    $td = $doc->createElement("td", toUTF8($value));
    $trBody->appendChild($td);

    $trBody->appendChild($td);
    // tbody > tr
    $tbodyDetail->appendChild($trBody);
}

$objDB->close();

// HTML出力
echo $doc->saveHTML();