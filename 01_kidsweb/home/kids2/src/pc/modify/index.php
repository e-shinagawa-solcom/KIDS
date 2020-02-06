<?php

// ----------------------------------------------------------------------------
/**
 *      仕入管理  修正画面
 */
// ----------------------------------------------------------------------------

//-------------------------------------------------------------------------
// ■ ライブラリファイル読込
//-------------------------------------------------------------------------
include 'conf.inc';
require LIB_FILE;
require SRC_ROOT . "pc/cmn/lib_pc.php";

//-------------------------------------------------------------------------
// ■ オブジェクト生成
//-------------------------------------------------------------------------
$objDB = new clsDB();
$objAuth = new clsAuth();

//-------------------------------------------------------------------------
// ■ DBオープン
//-------------------------------------------------------------------------
$objDB->open("", "", "", "");

//-------------------------------------------------------------------------
// ■ パラメータ取得
//-------------------------------------------------------------------------
$aryData = $_GET;

setcookie("strSessionID", $aryData["strSessionID"], 0, "/");

// セッション確認
$objAuth = fncIsSession($aryData["strSessionID"], $objAuth, $objDB);

// エラー画面での戻りURL
$strReturnPath = "../pc/search/index.php?strSessionID=" . $aryData["strSessionID"];

// 700 仕入管理
if (!fncCheckAuthority(DEF_FUNCTION_PC0, $objAuth)) {
    fncOutputError(9052, DEF_WARNING, "アクセス権限がありません。", true, $strReturnPath, $objDB);
}

// 705 仕入管理（ 仕入修正）
if (!fncCheckAuthority(DEF_FUNCTION_PC5, $objAuth)) {
    fncOutputError(9018, DEF_WARNING, "アクセス権限がありません。", true, $strReturnPath, $objDB);
}

// 仕入番号の取得
$lngStockNo = $aryData["lngStockNo"];
$lngRevisionNo = $aryData["lngRevisionNo"];

$lngStockRevisionNo = $aryData["lngStockRevisionNo"];
// 修正対象の仕入NOの仕入情報取得
$strQuery = fncGetStockHeadNoToInfoSQL($lngStockNo, $lngRevisionNo);

list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);

if ($lngResultNum) {
    if ($lngResultNum == 1) {
        $aryStock = $objDB->fetchArray($lngResultID, 0);
        // 該当仕入の状態が「締め済」の状態であれば
        if ($aryStock["lngstockstatuscode"] == DEF_STOCK_CLOSED) {
            fncOutputError(711, DEF_WARNING, "", true, $strReturnPath, $objDB);
        }
    } else {
        fncOutputError(703, DEF_ERROR, "該当データの取得に失敗しました", true, $strReturnPath, $objDB);
    }
} else {
    fncOutputError(703, DEF_ERROR, "データが異常です", true, $strReturnPath, $objDB);
}

// 指定仕入番号の仕入明細データ取得用SQL文の作成
$strQuery = fncGetStockDetailNoToInfoSQL($lngStockNo, $lngRevisionNo);

// 明細データの取得
list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);

if ($lngResultNum) {
    for ($i = 0; $i < $lngResultNum; $i++) {
        $aryStockDetail[] = $objDB->fetchArray($lngResultID, $i);
    }
} else {
    fncOutputError(703, DEF_WARNING, "仕入番号に対する明細情報が見つかりません。", true, $strReturnPath, $objDB);
}

$objDB->freeResult($lngResultID);

// 発注情報を仕入情報から取得
$aryOrderDetail = fncGetPoInfoSQLByStock($lngStockNo, $lngRevisionNo, $aryStock["strrealordercode"], $objDB);

// 消費税情報を取得
$taxObj = fncGetTaxInfo($aryStock["dtmstockappdate"], $objDB);

// 消費税区分を取得
$aryTaxclass = fncGetTaxClassAry($objDB);

if ($taxObj == null) {
    fncOutputError(703, DEF_ERROR, "消費税情報の取得に失敗しました。", true, $strReturnPath, $objDB);
}

//$aryTaxclass = fncGetTaxClassAry($objDB);

// 通貨
//$aryStock["lngmonetaryunitcode"] = fncGetPulldown("m_monetaryunit", "lngmonetaryunitcode", "strmonetaryunitname", $aryStock["lngmonetaryunitcode"], '', $objDB);
// レートタイプ
$aryStock["lngmonetaryratecode"] = fncGetPulldown("m_monetaryrateclass", "lngmonetaryratecode", "strmonetaryratename", $aryStock["lngmonetaryratecode"], '', $objDB);
// 支払条件
//$aryStock["lngpayconditioncode"] = fncGetPulldown("m_paycondition", "lngpayconditioncode", "strpayconditionname", $aryStock["lngpayconditioncode"], '', $objDB);
// フォームURL
$aryStock["actionUrl"] = "/pc/modify/modify_confirm.php";
$objDB->close();
// $aryStock["strnote"] = $aryStock["strnote"]!='' ? $aryStock["strnote"] : " ";

$aryStock["lngStockNo"] = $lngStockNo;
$aryStock["lngstockrevisionno"] = $lngRevisionNo;

// テンプレート読み込み
// $objTemplate = new clsTemplate();
// $objTemplate->getTemplate("pc/modify/pc_modify.html");
// // テンプレート生成
// $objTemplate->replace($aryData);
// $objTemplate->replace($aryNewResult);
// $objTemplate->complete();

$objTemplate = new clsTemplate();
$objTemplate->getTemplate("pc/modify/pc_modify.html");
$objTemplate->replace($aryStock);
$strTemplate = $objTemplate->strTemplate;


// $strTemplate = fncGetReplacedHtmlWithBase("pc/base.html", "pc/modify/pc_modify.html", $aryStock, $objAuth);

// 検索結果テーブル生成の為DOMDocumentを使用
$doc = new DOMDocument();
// パースエラー抑制
libxml_use_internal_errors(true);
// DOMパース
$doc->loadHTML($strTemplate);
// $doc->loadHTML($strTemplate);
// パースエラークリア
libxml_clear_errors();
// パースエラー抑制解除
libxml_use_internal_errors(false);

// 検索結果テーブルの取得
$tbodyChkbox = $doc->getElementById("tableB_chkbox")->getElementsByTagName("tbody")->item(0);
$tbodyDetail = $doc->getElementById("tableB")->getElementsByTagName("tbody")->item(0);
// $tbodyDetail = $tableDetail->getElementsByTagName("tbody")->item(0);

$aryData["lngGroupCode"] = $aryNewResult["lngGroupCode"];
$aryData["lngUserCode"] = $aryNewResult["lngUserCode"];

// 明細情報の出力
$num = 0;
foreach ($aryOrderDetail as $orderDetail) {
    $num += 1;
    // 仕入登録済フラグ
    $isStocked = false;
    // 消費税コード
    $lngtaxcode = $orderDetail["lngtaxcode"];
    // 消費税区分コード
    $lngtaxclasscode = DEF_TAXCLASS_HIKAZEI;
    // 消費税金額
    $curtaxprice = 0;
    // 消費税率
    $curtax = 0;
    if ($orderDetail["lngcountrycode"] == 81) {
        $curtax = $taxObj->curtax;
        $lngtaxclasscode = DEF_TAXCLASS_SOTOZEI;
    }
    // １：非課税
    if ($lngtaxclasscode == DEF_TAXCLASS_HIKAZEI) {
        $curtaxprice = 0;
        //　2:外税
    } else if ($lngtaxclasscode == DEF_TAXCLASS_SOTOZEI) {
        $curtaxprice = floor($orderDetail["cursubtotalprice"] * (1 + $curtax));
        // 3:内税
    } else {
        $curtaxprice = $orderDetail["cursubtotalprice"] - floor(($orderDetail["cursubtotalprice"] / (1 + $curtax)) * $curtax);
    }

    // 仕入明細をループし、発注番号、発注明細番号が同じの場合、仕入登録済フラグをtrue
    foreach ($aryStockDetail as $stockDetail) {
        if ($stockDetail["lngorderno"] == $orderDetail["lngorderno"]
            && $stockDetail["lngorderdetailno"] == $orderDetail["lngorderdetailno"]) {
            $isStocked = true;
            $lngtaxclasscode = $stockDetail["lngtaxclasscode"];
            $lngtaxcode = $stockDetail["lngtaxcode"];
            $curtax = $stockDetail["curtax"];
            $curtaxprice = $stockDetail["curtaxprice_comm"];
        }
    }

    // tbody > tr要素作成
    $trBody = $doc->createElement("tr");
    $trBody->setAttribute("class", "row".$num);

    // // No.
    // $td = $doc->createElement("td", $num);
    // $td->setAttribute("class", "col1");
    // $trBody->appendChild($td);

    // 対象
    $td = $doc->createElement("td");
    $td->setAttribute("class", "col2");
    $td->setAttribute("style", "text-align: center;");
    $chkBox = $doc->createElement("input");
    $chkBox->setAttribute("type", "checkbox");
    $chkBox->setAttribute("name", "edit");
    $chkBox->setAttribute("style", "width: 10px;");
    if ($isStocked) {
        $chkBox->setAttribute('checked', 'checked');
    }
    $td->appendChild($chkBox);
    $trBody->appendChild($td);   
    // tbody > tr
    $tbodyChkbox->appendChild($trBody);

    // tbody > tr要素作成
    $trBody = $doc->createElement("tr");
    $trBody->setAttribute("class", "row".$num);

    // No.
    $td = $doc->createElement("td", $num);
    $td->setAttribute("style", "text-align: center;");
    $td->setAttribute("class", "col1");
    $trBody->appendChild($td);

    // // 製品
    // $textContent = "[". $orderDetail["strproductcode"]. "] ". $orderDetail["strproductname"];
    // $td = $doc->createElement("td", toUTF8($textContent));
    // $td->setAttribute("class", "col3");
    // $trBody->appendChild($td);

    // 仕入科目
    $textContent = "[". $orderDetail["lngstocksubjectcode"]. "] ". $orderDetail["strstocksubjectname"];
    $td = $doc->createElement("td", toUTF8($textContent));
    $td->setAttribute("class", "col4");
    $trBody->appendChild($td);

    // 仕入部品
    $textContent = "[". $orderDetail["lngstockitemcode"]. "] ". $orderDetail["strstockitemname"];
    $td = $doc->createElement("td", toUTF8($textContent));
    $td->setAttribute("class", "col5");
    $trBody->appendChild($td);

    // 単価
    $td = $doc->createElement("td", toMoneyFormat($orderDetail["lngmonetaryunitcode"], $orderDetail["strmonetaryunitsign"], $orderDetail["curproductprice"]));
    $td->setAttribute("class", "col6");
    $trBody->appendChild($td);

    // 単位
    $td = $doc->createElement("td", toUTF8($orderDetail["strproductunitname"]));
    $td->setAttribute("class", "col7");
    $trBody->appendChild($td);

    // 数量
    $td = $doc->createElement("td", number_format($orderDetail["lngproductquantity"]));
    $td->setAttribute("class", "col8");
    $trBody->appendChild($td);

    // 税抜金額
    $td = $doc->createElement("td", toMoneyFormat($orderDetail["lngmonetaryunitcode"], $orderDetail["strmonetaryunitsign"], $orderDetail["cursubtotalprice"]));
    $td->setAttribute("class", "col9");
    $trBody->appendChild($td);

    // 消費税区分
    $td = $doc->createElement("td");
    $td->setAttribute("class", "col10");
    $select = $doc->createElement("select");
    $select->setAttribute("onchange", "resetTaxPrice(this,1)");
    $select->setAttribute("style", "width: 90px;");
    foreach ($aryTaxclass as $taxclass) {
        $option = $doc->createElement("option", toUTF8($taxclass["strtaxclassname"]));
        $option->setAttribute("value", $taxclass["lngtaxclasscode"]);
        if ($lngtaxclasscode == $taxclass["lngtaxclasscode"]) {
            $option->setAttribute("selected", "selected");
            $select->appendChild($option);
        }
        else if($orderDetail["lngcountrycode"] == 81){
            $select->appendChild($option);
        }
    }
    if ($isStocked) {
        $chkBox->setAttribute('checked', 'checked');
    }
    $td->appendChild($select);
    $trBody->appendChild($td);

    // 消費税率 
    $td = $doc->createElement("td");
    $td->setAttribute("class", "col11");
    // if($orderDetail["lngcountrycode"] == 81 && (!$lngtaxclasscode || $lngtaxclasscode != 1))
    if(!$lngtaxclasscode || $lngtaxclasscode != 1)
    {
        $select = $doc->createElement("select");
        $select->setAttribute("style", "width: 90px;");
        $select->setAttribute("onchange", "resetTaxPrice(this,2)");
        foreach ($taxObj as $tax) {
            $option = $doc->createElement("option", $tax->curtax * 100);
            $option->setAttribute("value", $tax->lngtaxcode);
            if ($lngtaxcode == $tax->lngtaxcode) {
                $option->setAttribute("selected", "selected");
            }
            $select->appendChild($option);
        }
        $td->appendChild($select);
    }
    else
    {
        $td = $doc->createElement("td", $curtax);
        $td->setAttribute("class", "col11");
    }
	$trBody->appendChild($td);

    // 消費税率プルダウン復元用
    if( $num == 1 )
    {
        $hidden = $doc->getElementById("taxList");
        $select = $doc->createElement("select");
        $select->setAttribute("style", "width: 90px;");
        $select->setAttribute("onchange", "resetTaxPrice(this,2)");
        $count = 0;
        foreach ($taxObj as $tax) {
            $option = $doc->createElement("option", $tax->curtax * 100);
            $option->setAttribute("value", $tax->lngtaxcode);
            if ($count == 0) {
                $option->setAttribute("selected", "selected");
            }
            $select->appendChild($option);
            $count++;
        }
        $hidden->appendChild($select);
    }

/*    
    // 消費税率   $taxObj
    $td = $doc->createElement("td", $curtax);
    $td->setAttribute("class", "col11");
    $trBody->appendChild($td);
*/
    // 消費税額
    $td = $doc->createElement("td", toMoneyFormat($orderDetail["lngmonetaryunitcode"], $orderDetail["strmonetaryunitsign"], $curtaxprice));
    $td->setAttribute("class", "col12");
    $trBody->appendChild($td);

    // 納期
    $td = $doc->createElement("td", toUTF8($orderDetail["dtmdeliverydate"]));
    $td->setAttribute("class", "dtmdeliverydate");
    $trBody->appendChild($td);

    // 備考
    $td = $doc->createElement("td", toUTF8($orderDetail["strnote"]));
    $td->setAttribute("class", "col14");
    $trBody->appendChild($td);

    // 税抜金額（金額フォーマット変換前）
    $td = $doc->createElement("td", $orderDetail["cursubtotalprice"]);
    $td->setAttribute("class", "cursubtotalprice");
    $td->setAttribute("style", "display:none");
    $trBody->appendChild($td);

    // 税率
    $td = $doc->createElement("td", $taxObj->curtax);
    $td->setAttribute("class", "curtax");
    $td->setAttribute("style", "display:none");
    $trBody->appendChild($td);

    // 単位コード
    $td = $doc->createElement("td", $orderDetail["lngmonetaryunitcode"]);
    $td->setAttribute("class", "lngmonetaryunitcode");
    $td->setAttribute("style", "display:none");
    $trBody->appendChild($td);

    // 単位記号
    $td = $doc->createElement("td", $orderDetail["strmonetaryunitsign"]);
    $td->setAttribute("class", "strmonetaryunitsign");
    $td->setAttribute("style", "display:none");
    $trBody->appendChild($td);

    // 税額
    $td = $doc->createElement("td", $curtaxprice);
    $td->setAttribute("class", "curtaxprice");
    $td->setAttribute("style", "display:none");
    $trBody->appendChild($td);

    // 発注番号
    $td = $doc->createElement("td", $orderDetail["lngorderno"]);
    $td->setAttribute("class", "lngorderno");
    $td->setAttribute("style", "display:none");
    $trBody->appendChild($td);

    // 発注リビジョン番号
    $td = $doc->createElement("td", $orderDetail["lngrevisionno"]);
    $td->setAttribute("class", "lngrevisionno");
    $td->setAttribute("style", "display:none");
    $trBody->appendChild($td);

    // 発注明細番号
    $td = $doc->createElement("td", $orderDetail["lngorderdetailno"]);
    $td->setAttribute("class", "lngorderdetailno");
    $td->setAttribute("style", "display:none");
    $trBody->appendChild($td);

    // 消費税コード
    $td = $doc->createElement("td", $lngtaxcode);
    $td->setAttribute("class", "lngtaxcode");
    $td->setAttribute("style", "display:none");
    $trBody->appendChild($td);
    
    // 課税区分コード
    $td = $doc->createElement("td", $lngtaxclasscode);
    $td->setAttribute("class", "lngtaxclasscode");
    $td->setAttribute("style", "display:none");
    $trBody->appendChild($td);
    
    // tbody > tr
    $tbodyDetail->appendChild($trBody);
}

$objDB->close();

// HTML出力
echo $doc->saveHTML();
