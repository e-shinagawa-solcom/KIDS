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

// セッション確認
$objAuth = fncIsSession($aryData["strSessionID"], $objAuth, $objDB);

// 700 仕入管理
if (!fncCheckAuthority(DEF_FUNCTION_PC0, $objAuth)) {
    fncOutputError(9052, DEF_WARNING, "アクセス権限がありません。", true, "pc/regist/index.php?strSessionID=" . $aryData["strSessionID"], $objDB);
}

// 705 仕入管理（ 仕入修正）
if (!fncCheckAuthority(DEF_FUNCTION_PC5, $objAuth)) {
    fncOutputError(9018, DEF_WARNING, "アクセス権限がありません。", true, $strReturnPath, $objDB);
}

$lngUserCode = trim($objAuth->UserID);
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
    $aryQuery[] = "od.strproductcode, "; // 製品コード
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
    // $aryQuery[] = "od.lngdeliverymethodcode as lngdeliverymethodcode, "; // 運搬方法コード
    // $aryQuery[] = "dm.strdeliverymethodname as strdeliverymethodname, "; // 運搬方法名称
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
    $aryQuery[] = "pod.lngdeliverymethodcode,"; // 運搬方法
    $aryQuery[] = "pod.strdeliverymethodname,"; // 運搬方法名
    $aryQuery[] = "pod.strnote,"; // 明細備考
    $aryQuery[] = "od.cursubtotalprice as cursubtotalprice, "; // 小計金額
    $aryQuery[] = "od.strnote as strdetailnote, "; // 備考
    $aryQuery[] = "od.strmoldno as strSerialNo, "; // シリアル
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
    $aryQuery[] = " lngorderno, lngorderstatuscode, lngmonetaryunitcode,lnggroupcode,lngusercode,";
    $aryQuery[] = " lngmonetaryratecode, lngcustomercompanycode, lngpayconditioncode ";
    $aryQuery[] = "FROM";
    $aryQuery[] = "  m_order ";
    $aryQuery[] = "WHERE";
    $aryQuery[] = "  lngorderno = " . $aryDetailData[$i]["lngOrderNo"] . " ";
    $aryQuery[] = "  AND lngrevisionno = " . $aryDetailData[$i]["lngRevisionNo"] . " ";
    $aryQuery[] = "  AND bytinvalidflag = false ";
    $aryQuery[] = ") o on o.lngorderno = od.lngorderno";
    $aryQuery[] = "  inner join t_purchaseorderdetail pod ";
    $aryQuery[] = "    on pod.lngorderno = od.lngorderno ";
    $aryQuery[] = "    and pod.lngorderdetailno = od.lngorderdetailno ";
    $aryQuery[] = "    and pod.lngorderrevisionno = od.lngrevisionno ";
    $aryQuery[] = "  inner join ( ";
    $aryQuery[] = "    select";
    $aryQuery[] = "      mpo1.lngpurchaseorderno";
    $aryQuery[] = "      , mpo1.lngrevisionno";
    $aryQuery[] = "      , mpo1.lngpayconditioncode ";
    $aryQuery[] = "    from";
    $aryQuery[] = "      m_purchaseorder mpo1 ";
    $aryQuery[] = "      inner join ( ";
    $aryQuery[] = "        select";
    $aryQuery[] = "          lngpurchaseorderno";
    $aryQuery[] = "          , max(lngrevisionno) as lngrevisionno ";
    $aryQuery[] = "        from";
    $aryQuery[] = "          m_purchaseorder ";
    $aryQuery[] = "        group by";
    $aryQuery[] = "          lngpurchaseorderno";
    $aryQuery[] = "      ) max_rev ";
    $aryQuery[] = "        on max_rev.lngpurchaseorderno = mpo1.lngpurchaseorderno ";
    $aryQuery[] = "        and max_rev.lngrevisionno = mpo1.lngrevisionno ";
    $aryQuery[] = "    where";
    $aryQuery[] = "      not exists ( ";
    $aryQuery[] = "        select";
    $aryQuery[] = "          mpo2.lngpurchaseorderno ";
    $aryQuery[] = "        from";
    $aryQuery[] = "          m_purchaseorder mpo2 ";
    $aryQuery[] = "        where";
    $aryQuery[] = "          mpo2.lngpurchaseorderno = mpo1.lngpurchaseorderno ";
    $aryQuery[] = "          and mpo2.lngrevisionno = - 1";
    $aryQuery[] = "      )";
    $aryQuery[] = "  ) mpo ";
    $aryQuery[] = "    on mpo.lngpurchaseorderno = pod.lngpurchaseorderno ";
    $aryQuery[] = "    and mpo.lngrevisionno = pod.lngrevisionno ";
    $aryQuery[] = " LEFT JOIN (SELECT m_product.* FROM m_product ";
    $aryQuery[] = "     INNER JOIN (";
    $aryQuery[] = "         SELECT ";
    $aryQuery[] = "             lngproductno,strrevisecode,MAX(lngrevisionno) as lngrevisionno ";
    $aryQuery[] = "         FROM m_product GROUP BY lngproductno,strrevisecode";
    $aryQuery[] = "     ) mp1 ON mp1.lngproductno = m_product.lngproductno";
    $aryQuery[] = "     AND mp1.strrevisecode = m_product.strrevisecode";
    $aryQuery[] = "     AND mp1.lngrevisionno = m_product.lngrevisionno";
    $aryQuery[] = " ) p on p.strproductcode = od.strproductcode and p.strrevisecode = od.strrevisecode";
    $aryQuery[] = " LEFT JOIN m_stocksubject ss on ss.lngstocksubjectcode = od.lngstocksubjectcode";
    $aryQuery[] = " LEFT JOIN m_stockitem si on si.lngstocksubjectcode = od.lngstocksubjectcode and si.lngstockitemcode = od.lngstockitemcode";
    $aryQuery[] = " LEFT JOIN m_monetaryunit mu on mu.lngmonetaryunitcode = o.lngmonetaryunitcode";
    $aryQuery[] = " LEFT JOIN m_orderstatus os on os.lngorderstatuscode = o.lngorderstatuscode";
    $aryQuery[] = " LEFT JOIN m_productunit pu on pu.lngproductunitcode = od.lngproductunitcode";
    $aryQuery[] = " LEFT JOIN m_deliverymethod dm on dm.lngdeliverymethodcode = od.lngdeliverymethodcode";
    $aryQuery[] = " LEFT JOIN m_company c on c.lngcompanycode = o.lngcustomercompanycode";
	$aryQuery[] = " LEFT JOIN m_Group g on o.lnggroupcode = g.lnggroupcode";
	$aryQuery[] = " LEFT JOIN m_User u on o.lngusercode = u.lngusercode";
    $aryQuery[] = " WHERE od.lngorderno = " . $aryDetailData[$i]["lngOrderNo"] . " ";
    $aryQuery[] = "  AND od.lngrevisionno = " . $aryDetailData[$i]["lngRevisionNo"] . " ";
    $aryQuery[] = "  AND od.lngorderdetailno = " . $aryDetailData[$i]["lngOrderDetailNo"] . " ";
    $aryQuery[] = " ORDER BY od.lngSortKey";
    $strQuery = implode("\n", $aryQuery);
    // echo $strQuery;
    list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);
    if ($lngResultNum) {
        if ($lngResultNum == 1) {
            $detailDataResult = $objDB->fetchArray($lngResultID, 0);
            // $aryData["dtmexpirationdate"] = $detailDataResult["dtmexpirationdate"];
            $aryNewResult["lngGroupCode"] = $detailDataResult["lnggroupcode"];
            $aryNewResult["strGroupName"] = $detailDataResult["strgroupname"];
            $aryNewResult["lngUserCode"] = $detailDataResult["lngusercode"];
            $aryNewResult["strUserName"] = $detailDataResult["strusername"];
            // 数量
            $detailDataResult["lngstockdetailno"] = $i+1;
            // 数量
            $detailDataResult["lngproductquantity"] = number_format($detailDataResult["lngproductquantity"]);
            // 単価
            $detailDataResult["curproductprice"] = toMoneyFormat($detailDataResult["lngmonetaryunitcode"], $detailDataResult["strmonetaryunitsign"], number_format($detailDataResult["curproductprice"],2 ));
            // 小計金額
            $cursubtotalprice = $detailDataResult["cursubtotalprice"];
            $detailDataResult["cursubtotalprice"] = toMoneyFormat($detailDataResult["lngmonetaryunitcode"], $detailDataResult["strmonetaryunitsign"], number_format($detailDataResult["cursubtotalprice"],2 ));

            // 税率
            $detailDataResult["curTax"] = $aryDetailData[$i]["curTax"];
            // 消費税区分
            $detailDataResult["lngtaxclasscode"] = $aryDetailData[$i]["lngTaxClassCode"];
            // 消費税区分名称
            $detailDataResult["strtaxclassname"] = mb_convert_encoding($aryDetailData[$i]["strTaxClassName"], 'EUC-JP', 'UTF-8');
            // 消費税額
            $detailDataResult["curtaxprice"] = toMoneyFormat($detailDataResult["lngmonetaryunitcode"], $detailDataResult["strmonetaryunitsign"], number_format($aryDetailData[$i]["curTaxPrice"],2 ));

            // 合計金額の設定(小計金額の合計)
            $curtotalprice += $cursubtotalprice;
        }
    }


    // テンプレート読み込み
    $objTemplate = new clsTemplate();
    $objTemplate->getTemplate("pc/modify/pc_parts_confirm_modify.html");
    // テンプレート生成
    $objTemplate->replace($detailDataResult);
    $objTemplate->complete();

    // HTML出力
    $aryDetailTable[] = $objTemplate->strTemplate;
}

// 合計金額の設定
$strmonetaryunitsign = fncGetMasterValue("m_monetaryunit", "lngmonetaryunitcode", "strmonetaryunitsign", $aryData["lngMonetaryUnitCode"], '', $objDB);
$aryNewResult["curtotalprice"] = toMoneyFormat($aryData["lngMonetaryUnitCode"], $strmonetaryunitsign, number_format($curtotalprice,2));
$aryData["curTotalPrice"] =   $curtotalprice;        
$aryNewResult["strDetailTable"] = implode ("\n", $aryDetailTable );
// 作成日
$aryData["dtminsertdate"] = date('Y/m/d', time());
// 入力コード
$aryData["lnginputusercode"] = $lngUserCode;
// 入力者名称
$aryNewResult["strinputusername"] = $strUserName;
// テンプレート読み込み
$objTemplate = new clsTemplate();
$objTemplate->getTemplate("pc/modify/pc_confirm_modify.html");
mb_convert_variables('EUC-JP', 'UTF-8', $aryData);
// テンプレート生成
$objTemplate->replace($aryNewResult);
$objTemplate->replace($aryData);
$objTemplate->complete();

// 検索結果テーブル生成の為DOMDocumentを使用
$doc = new DOMDocument();
// パースエラー抑制
libxml_use_internal_errors(true);
// DOMパース
$doc->loadHTML(mb_convert_encoding($objTemplate->strTemplate, "utf8", "eucjp-win"));
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