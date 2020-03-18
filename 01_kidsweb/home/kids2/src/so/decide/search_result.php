<?php
// ----------------------------------------------------------------------------
/**
 *       受注管理 確定画面の確定ボタン
 *
 *       処理概要
 *         ・確定対象明細行選択部で選択した行を確定する処理
 *
 *       更新履歴
 *
 */
// ----------------------------------------------------------------------------

// 読み込み
include 'conf.inc';
require LIB_FILE;

//PHP標準のJSON変換メソッドはエラーになるので外部のライブラリ(恐らくエンコードの問題)
include 'JSON.php';

//値の取得
$postdata = file_get_contents("php://input");
$aryData = json_decode($postdata, true);
$objDB = new clsDB();
$objAuth = new clsAuth();
$objDB->open("", "", "", "");
//JSONクラスインスタンス化
$s = new Services_JSON();

//値が存在しない場合は通常の POST で受ける
if ($aryData == null) {
    $aryData = $_POST;
}

// セッション確認
$objAuth = fncIsSession($_GET["strSessionID"], $objAuth, $objDB);

$aryQuery = array();
$aryQuery[] = "SELECT ";
$aryQuery[] = "rd.lngReceiveNo as lngReceiveNo, rd.lngRevisionNo as lngRevisionNo";
$aryQuery[] = ", r.strReceiveCode";
$aryQuery[] = ", r.strcustomerreceivecode";
$aryQuery[] = ", r.lngMonetaryUnitCode";
$aryQuery[] = ", r.strMonetaryUnitSign";
$aryQuery[] = ", rd.lngreceivedetailno as lngreceivedetailno";
$aryQuery[] = ", rd.strProductCode as strProductCode"; // 製品コード・名称
$aryQuery[] = ", p.strProductName as strProductName";
$aryQuery[] = ", r.strCompanyDisplayCode as strCustomerDisplayCode"; // 顧客コード・名称
$aryQuery[] = ", r.strCompanyDisplayName as strCustomerDisplayName";
$aryQuery[] = ", p.lngproductno as lngproductno";
$aryQuery[] = ", sd.lngsalesdivisioncode"; // 売上区分
$aryQuery[] = ", sd.strsalesdivisionname";
$aryQuery[] = ", rd.lngSalesClassCode as lngSalesClassCode"; // 売上区分
$aryQuery[] = ", ss.strSalesClassName as strSalesClassName";
// 製品番号
$aryQuery[] = " , p.lngProductNo";
$aryQuery[] = " , p.strrevisecode";
$aryQuery[] = " , p.lngRevisionNo as lngProductRevisionNo";
$aryQuery[] = ", p.strGoodsCode as strGoodsCode"; // 顧客品番
// 部門
$aryQuery[] = ", p.lnginchargegroupcode as lngInChargeGroupCode";
$aryQuery[] = ", inchg_g.strGroupDisplayCode as strInChargeGroupDisplayCode";
$aryQuery[] = ", inchg_g.strGroupDisplayName as strInChargeGroupDisplayName";
// 担当者
$aryQuery[] = ", p.lnginchargeusercode as lngInChargeUserCode";
$aryQuery[] = ", inchg_u.strUserDisplayCode as strInChargeUserDisplayCode";
$aryQuery[] = ", inchg_u.strUserDisplayName as strInChargeUserDisplayName";
// 開発担当者
$aryQuery[] = ", p.lngdevelopusercode as lngdevelopusercode";
$aryQuery[] = ", delp_u.strUserDisplayCode as strdevelopuserdisplaycode";
$aryQuery[] = ", delp_u.strUserDisplayName as strdevelopuserdisplayname";

$aryQuery[] = ", rd.dtmDeliveryDate as dtmDeliveryDate"; // 納期
$aryQuery[] = ", rd.curProductPrice"; // 単価
$aryQuery[] = ", rd.lngProductUnitCode as lngProductUnitCode"; // 単位
$aryQuery[] = ", pu.strProductUnitName as strProductUnitName";
$aryQuery[] = ", p.lngcartonquantity"; // カートン入数
$aryQuery[] = ", rd.curSubTotalPrice"; // 税抜金額
$aryQuery[] = ", rd.strNote as strDetailNote"; // 明細備考
$aryQuery[] = ", ed.lngproductquantity as lngproductquantity"; // 製品数量
$aryQuery[] = " FROM t_ReceiveDetail rd";
$aryQuery[] = "  INNER JOIN ( ";
$aryQuery[] = "    SELECT";
$aryQuery[] = "      r1.*";
$aryQuery[] = "      , m_MonetaryUnit.strMonetaryUnitSign";
$aryQuery[] = "      , cust_c.strCompanyDisplayCode";
$aryQuery[] = "      , cust_c.strshortname as strCompanyDisplayName ";
$aryQuery[] = "    from";
$aryQuery[] = "      m_Receive r1 ";
$aryQuery[] = "      inner join ( ";
$aryQuery[] = "        select";
$aryQuery[] = "          max(lngrevisionno) lngrevisionno";
$aryQuery[] = "          , lngReceiveNo ";
$aryQuery[] = "        from";
$aryQuery[] = "          m_Receive ";
$aryQuery[] = "        group by";
$aryQuery[] = "          lngReceiveNo";
$aryQuery[] = "      ) r2 ";
$aryQuery[] = "        on r1.lngrevisionno = r2.lngrevisionno ";
$aryQuery[] = "        and r1.lngReceiveNo = r2.lngReceiveNo ";
$aryQuery[] = "      LEFT JOIN m_MonetaryUnit ";
$aryQuery[] = "        USING (lngMonetaryUnitCode) ";
$aryQuery[] = "      LEFT JOIN m_Company cust_c ";
$aryQuery[] = "        ON r1.lngCustomerCompanyCode = cust_c.lngCompanyCode ";
$aryQuery[] = "    WHERE";
$aryQuery[] = "      r1.lngreceivestatuscode = " . DEF_RECEIVE_APPLICATE . " ";
if ($aryData["lngCustomerCode"] != "") {
    $aryQuery[] = " AND cust_c.strCompanyDisplayCode = '" . $aryData["lngCustomerCode"] . "' ";
}
$aryQuery[] = "     and r1.lngcustomercompanycode != 0 ";
$aryQuery[] = " ) r on rd.lngReceiveNo = r.lngReceiveNo and rd.lngRevisionNo = r.lngRevisionNo";
$aryQuery[] = "        LEFT JOIN (";
$aryQuery[] = "            select p1.*  from m_product p1 ";
$aryQuery[] = "        	inner join (select max(lngrevisionno) lngrevisionno, strproductcode, strrevisecode from m_Product group by strProductCode, strrevisecode) p2";
$aryQuery[] = "            on p1.lngrevisionno = p2.lngrevisionno and p1.strproductcode = p2.strproductcode and p1.strrevisecode = p2.strrevisecode";
$aryQuery[] = "          ) p ";
$aryQuery[] = "          ON rd.strProductCode = p.strProductCode AND rd.strrevisecode = p.strrevisecode ";
$aryQuery[] = " LEFT JOIN m_Group inchg_g ON p.lnginchargegroupcode = inchg_g.lngGroupCode";
$aryQuery[] = " LEFT JOIN m_User inchg_u ON p.lnginchargeusercode = inchg_u.lngUserCode";
$aryQuery[] = " LEFT JOIN m_User delp_u ON p.lngdevelopusercode = delp_u.lngUserCode";
$aryQuery[] = " LEFT JOIN t_estimatedetail ed on ed.lngestimateno = rd.lngestimateno";
$aryQuery[] = " and ed.lngrevisionno = rd.lngestimaterevisionno";
$aryQuery[] = " and ed.lngestimatedetailno = rd.lngestimatedetailno";
$aryQuery[] = " LEFT JOIN m_SalesClass ss on rd.lngSalesClassCode = ss.lngSalesClassCode";
$aryQuery[] = " LEFT JOIN m_salesclassdivisonlink ssdl on ssdl.lngSalesClassCode = ed.lngSalesClassCode";
$aryQuery[] = " and ssdl.lngsalesdivisioncode = ed.lngsalesdivisioncode";
$aryQuery[] = " LEFT JOIN m_salesdivision sd on sd.lngsalesdivisioncode = ssdl.lngsalesdivisioncode";
$aryQuery[] = " LEFT JOIN m_ProductUnit pu ON rd.lngProductUnitCode = pu.lngProductUnitCode";
$aryQuery[] = " WHERE not exists (select strreceivecode from m_receive where lngrevisionno < 0  and strreceivecode = r.strreceivecode)";

if ($aryData["strProductCode"] != "") {
    if (strpos($aryData["strProductCode"], '_') !== false) {
        $aryQuery[] = " AND rd.strProductCode = '" . explode("_", $aryData["strProductCode"])[0] . "'";
        $aryQuery[] = " AND rd.strrevisecode = '" . explode("_", $aryData["strProductCode"])[1] . "'";
    } else {
        $aryQuery[] = " AND rd.strProductCode = '" . $aryData["strProductCode"] . "'";
    }
}
if ($aryData["lngSalesDivisionCode"] != "") {
    $aryQuery[] = " AND ed.lngSalesDivisionCode = '" . $aryData["lngSalesDivisionCode"] . "' ";
}
if ($aryData["lngSalesClassCode"] != "") {
    $aryQuery[] = " AND rd.lngSalesClassCode = '" . $aryData["lngSalesClassCode"] . "' ";
}
if ($aryData["From_dtmDeliveryDate"] != "") {
    $aryQuery[] = " AND rd.dtmDeliveryDate >= '" . $aryData["From_dtmDeliveryDate"] . "' ";
}
if ($aryData["To_dtmDeliveryDate"] != "") {
    $aryQuery[] = " AND rd.dtmDeliveryDate <= '" . $aryData["To_dtmDeliveryDate"] . "' ";
}
$aryQuery[] = " ORDER BY rd.lngSortKey ASC ";

$strQuery = implode("\n", $aryQuery);
// echo $strQuery;
list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);
$aryDetailResult = array();
if ($lngResultNum) {
    for ($i = 0; $i < $lngResultNum; $i++) {
        $aryDetailResult[] = $objDB->fetchArray($lngResultID, $i);
    }
}
$objDB->freeResult($lngResultID);

$strQuery = "SELECT lngproductunitcode, strproductunitname FROM m_productunit";
list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);
// 検索件数がありの場合
if ($lngResultNum) {
    // 指定数以内であれば通常処理
    for ($i = 0; $i < $lngResultNum; $i++) {
        $aryProductUnit[] = $objDB->fetchArray($lngResultID, $i);
    }
}

$objDB->freeResult($lngResultID);
$objDB->close();

$lngReceiveNos = explode(",", $aryData["lngReceiveNo"]);
if ($lngReceiveNos) {
    // 表示項目の抽出
    foreach ($lngReceiveNos as $key) {
        $lngReceiveNos[$key] = $key;
    }
}

// 検索結果テーブル生成の為DOMDocumentを使用
$doc = new DOMDocument();
// 明細情報の出力
$detailNum = 0;
$decideNum = 0;
$tblA_chkbox_body_html = "";
$tblA_detail_body_html = "";
$tblB_no_html = "";
$tblB_body_html = "";
foreach ($aryDetailResult as $detailResult) {
    $isdecideObj = false;
    if (array_key_exists($detailResult["lngreceiveno"], $lngReceiveNos)) {
        $isdecideObj = true;
    }

    if (!$isdecideObj) {
        $detailNum += 1;
        // tbody > tr要素作成
        $trChkBox = $doc->createElement("tr");
        // 選択チェックボックス
        $chkBox = $doc->createElement("input");
        $chkBox->setAttribute("type", "checkbox");
        $chkBox->setAttribute("name", "edit");
        $id = $detailResult["lngreceiveno"] . "_" . $detailResult["lngreceivedetailno"] . "_" . $detailResult["lngrevisionno"];
        $chkBox->setAttribute("id", $id);
        $chkBox->setAttribute("style", "width: 10px;");
        $tdChkBox = $doc->createElement("td");
        $tdChkBox->setAttribute("style", "width: 20px;text-align:center;");
        $tdChkBox->appendChild($chkBox);
        $trChkBox->appendChild($tdChkBox);
        // tbody > tr
        $tblA_chkbox_body_html .= $doc->saveXML($trChkBox);

    } else {
        $decideNum += 1;
        // tbody > tr要素作成
        $trBody = $doc->createElement("tr");
        // No.
        $td = $doc->createElement("td", $decideNum);
        $td->setAttribute("style", "width: 25px;");
        $trBody->appendChild($td);
        // tbody > tr
        $tblB_no_html .= $doc->saveXML($trBody);
    }

    // tbody > tr要素作成
    $trBody = $doc->createElement("tr");

    // No.
    $td = $doc->createElement("td", $detailNum);
    $td->setAttribute("style", "width: 25px;");
    if ($isdecideObj) {
        $td->setAttribute("style", "display:none");
    }
    $trBody->appendChild($td);

    // 顧客受注番号
    $td = $doc->createElement("td");
    $td->setAttribute("id", "strcustomerreceivecode");
    $td->setAttribute("style", "text-align:center;");
    $text = $doc->createElement("input");
    $text->setAttribute("type", "text");
    $text->setAttribute("style", "ime-mode:disabled;");
    $text->setAttribute("class", "form-control form-control-sm txt-kids");
    $text->setAttribute("value", $detailResult["strcustomerreceivecode"]);
    $td->appendChild($text);
    $trBody->appendChild($td);

    // 顧客
    if ($detailResult["strcustomerdisplaycode"] != "") {
        $textContent = "[" . $detailResult["strcustomerdisplaycode"] . "]" . " " . $detailResult["strcustomerdisplayname"];
    } else {
        $textContent = "";
    }
    $td = $doc->createElement("td", $textContent);
    $td->setAttribute("id", "strcompanydisplaycode");
    $trBody->appendChild($td);

    // 納期
    $td = $doc->createElement("td", $detailResult["dtmdeliverydate"]);
    $td->setAttribute("id", "dtmdeliverydate");
    $trBody->appendChild($td);

    // 売上分類
    $textContent = "[" . $detailResult["lngsalesdivisioncode"] . "]" . " " . $detailResult["strsalesdivisionname"];
    $td = $doc->createElement("td", $textContent);
    $td->setAttribute("id", "lngsalesdivisioncode");
    $trBody->appendChild($td);

    // 売上区分
    $textContent = "[" . $detailResult["lngsalesclasscode"] . "]" . " " . $detailResult["strsalesclassname"];
    $td = $doc->createElement("td", $textContent);
    $td->setAttribute("id", "lngsalesclasscode");
    $trBody->appendChild($td);

    // 単価
    $textContent = convertPrice($detailResult["lngmonetaryunitcode"], $detailResult["strmonetaryunitsign"], $detailResult["curproductprice"], "unitprice");
    $td = $doc->createElement("td", $textContent);
    $td->setAttribute("id", "curproductprice");
    $trBody->appendChild($td);

    $lngunitquantity = 1;
    $detailResult["lngcartonquantity"] = $detailResult["lngcartonquantity"] == null ? 0 : $detailResult["lngcartonquantity"];
    $detailResult["lngproductquantity"] = $detailResult["lngproductquantity_est"] == null ? 0 : $detailResult["lngproductquantity_est"];
    $lngproductquantity = $detailResult["lngproductquantity"] / $lngunitquantity;
    if ($detailResult["lngproductunitcode"] == 2) {
        $lngunitquantity = $detailResult["lngcartonquantity"];
        $lngproductquantity = $detailResult["lngproductquantity"] / $lngunitquantity;
    }

    // 数量
    $textContent = number_format($lngproductquantity);
    $td = $doc->createElement("td", $textContent);
    $td->setAttribute("id", "lngproductquantity_re");
    $trBody->appendChild($td);

    // 単位
    $td = $doc->createElement("td");
    $td->setAttribute("id", "lngproductunitcode");
    $select = $doc->createElement("select");
    foreach ($aryProductUnit as $productunit) {
        $option = $doc->createElement("option", $productunit["strproductunitname"]);
        $option->setAttribute("value", $productunit["lngproductunitcode"]);
        if ($productunit["lngproductunitcode"] == $detailResult["lngproductunitcode"]) {
            $option->setAttribute("selected", "true");
        }
        $select->appendChild($option);
    }
    $td->appendChild($select);
    $trBody->appendChild($td);

    // 入数
    if ($detailResult["lngproductunitcode"] == 2) {
        $td = $doc->createElement("td");
        $text = $doc->createElement("input");
        $text->setAttribute("type", "text");
        $text->setAttribute("name", "unitQuantity");
        $text->setAttribute("class", "form-control form-control-sm txt-kids");
        $text->setAttribute("style", "width:90px;");
        $text->setAttribute("value", $lngunitquantity);
        $td->appendChild($text);
    } else {
        $td = $doc->createElement("td", $lngunitquantity);
    }
    $td->setAttribute("id", "lngunitquantity");
    $td->setAttribute("style", "width:100px;");
    $trBody->appendChild($td);

    // 小計
    $textContent = convertPrice($detailResult["lngmonetaryunitcode"], $detailResult["strmonetaryunitsign"], $detailResult["cursubtotalprice"], "price");
    $td = $doc->createElement("td", $textContent);
    $td->setAttribute("id", "cursubtotalprice");
    $trBody->appendChild($td);

    // 備考
    $td = $doc->createElement("td");
    $td->setAttribute("id", "strdetailnote");
    $text = $doc->createElement("input");
    $text->setAttribute("type", "text");
    $text->setAttribute("class", "form-control form-control-sm txt-kids");
    $text->setAttribute("style", "width:240px;");
    $text->setAttribute("value", toUTF8($detailResult["strdetailnote"]));
    $td->appendChild($text);
    $trBody->appendChild($td);

    // 製品コード
    $textContent = $detailResult["strproductcode"];
    $td = $doc->createElement("td", $textContent);
    $td->setAttribute("id", "strproductcode");
    $td->setAttribute("style", "display:none");
    $trBody->appendChild($td);

    // 製品名称
    $textContent = "[" . $detailResult["strproductcode"] . "]" . " " . $detailResult["strproductname"];
    $td = $doc->createElement("td", $textContent);
    $td->setAttribute("id", "strproductname");
    $td->setAttribute("style", "display:none");
    $trBody->appendChild($td);

    // 受注番号
    $textContent = $detailResult["lngreceiveno"];
    $td = $doc->createElement("td", $textContent);
    $td->setAttribute("id", "lngreceiveno");
    $td->setAttribute("style", "display:none");
    $trBody->appendChild($td);

    // 明細行番号
    $td = $doc->createElement("td", $detailResult["lngreceivedetailno"]);
    $td->setAttribute("id", "lngreceivedetailno");
    $td->setAttribute("style", "display:none");
    $trBody->appendChild($td);

    // 受注コード
    $textContent = $detailResult["strreceivecode"];
    $td = $doc->createElement("td", $textContent);
    $td->setAttribute("id", "strreceivecode");
    $td->setAttribute("style", "display:none");
    $trBody->appendChild($td);

    // リビジョン番号
    $textContent = $detailResult["lngrevisionno"];
    $td = $doc->createElement("td", $textContent);
    $td->setAttribute("id", "lngrevisionno");
    $td->setAttribute("style", "display:none");
    $trBody->appendChild($td);

    // カートン入数
    $textContent = $detailResult["lngcartonquantity"];
    $td = $doc->createElement("td", $textContent);
    $td->setAttribute("id", "lngcartonquantity");
    $td->setAttribute("style", "display:none");
    $trBody->appendChild($td);

    // 数量
    $textContent = $detailResult["lngproductquantity"];
    $td = $doc->createElement("td", $textContent);
    $td->setAttribute("id", "lngproductquantity");
    $td->setAttribute("style", "display:none");
    $trBody->appendChild($td);

    // 製品リビジョン番号
    $textContent = $detailResult["strrevisecode"];
    $td = $doc->createElement("td", $textContent);
    $td->setAttribute("id", "strrevisecode");
    $td->setAttribute("style", "display:none");
    $trBody->appendChild($td);

    // 通貨単位コード
    $textContent = $detailResult["lngmonetaryunitcode"];
    $td = $doc->createElement("td", toUTF8($textContent));
    $td->setAttribute("id", "lngmonetaryunitcode");
    $td->setAttribute("style", "display:none");
    $trBody->appendChild($td);

    // 通貨単位記号
    $textContent = $detailResult["strmonetaryunitsign"];
    $td = $doc->createElement("td", toUTF8($textContent));
    $td->setAttribute("id", "strmonetaryunitsign");
    $td->setAttribute("style", "display:none");
    $trBody->appendChild($td);

    if (!$isdecideObj) {
        // tbody > tr
        // $tbodyDetail->appendChild($trBody);

        $tblA_detail_body_html .= $doc->saveXML($trBody);
    } else {
        // tbody > tr
        // $tbodyDecideBody->appendChild($trBody);

        $tblB_body_html .= $doc->saveXML($trBody);

    }
}

// 取得データの調整
$aryResult = array();
$aryResult["strProductCode"] = $aryDetailResult[0]["strproductcode"] . "_" . $aryDetailResult[0]["strrevisecode"];
$aryResult["strProductName"] = $aryDetailResult[0]["strproductname"];
$aryResult["strGoodsCode"] = $aryDetailResult[0]["strgoodscode"];
$aryResult["lngProductNo"] = $aryDetailResult[0]["lngproductno"];
$aryResult["lngProductRevisionNo"] = $aryDetailResult[0]["lngproductrevisionno"];
$aryResult["strReviseCode"] = $aryDetailResult[0]["strrevisecode"];
$aryResult["lngInChargeGroupCode"] = $aryDetailResult[0]["strinchargegroupdisplaycode"];
$aryResult["strInChargeGroupName"] = $aryDetailResult[0]["strinchargegroupdisplayname"];
$aryResult["lngInChargeUserCode"] = $aryDetailResult[0]["strinchargeuserdisplaycode"];
$aryResult["strInChargeUserName"] = $aryDetailResult[0]["strinchargeuserdisplayname"];
$aryResult["lngDevelopUserCode"] = $aryDetailResult[0]["strdevelopuserdisplaycode"];
$aryResult["strDevelopUserName"] = $aryDetailResult[0]["strdevelopuserdisplayname"];
$aryResult["strSessionID"] = $_GET["strSessionID"];
$aryResult["tblA_chkbox_result"] = $tblA_chkbox_body_html;
$aryResult["tblA_detail_result"] = $tblA_detail_body_html;
$aryResult["tblB_no_result"] = $tblB_no_html;
$aryResult["tblB_body_result"] = $tblB_body_html;
$aryResult["count"] = count($aryDetailResult);

//結果出力
echo $s->encodeUnsafe($aryResult);
