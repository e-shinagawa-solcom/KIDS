<?php

// ----------------------------------------------------------------------------
/**
 *      仕入管理  登録画面
 */
// ----------------------------------------------------------------------------

//-------------------------------------------------------------------------
// ■ ライブラリファイル読込
//-------------------------------------------------------------------------
include 'conf.inc';
require LIB_FILE;
// require( SRC_ROOT."po/cmn/lib_po.php" );

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

// 701 仕入管理（ 仕入登録）
if (!fncCheckAuthority(DEF_FUNCTION_PC1, $objAuth)) {
    fncOutputError(9018, DEF_WARNING, "アクセス権限がありません。", true, $strReturnPath, $objDB);
}

// 通貨
$aryData["lngMonetaryUnitCode"] = fncGetPulldown( "m_monetaryunit", "lngmonetaryunitcode", "strmonetaryunitname", '', '', $objDB );
// レートタイプ
$aryData["lngMonetaryRateCode"] = fncGetPulldown( "m_monetaryrateclass", "lngmonetaryratecode", "strmonetaryratename", '', '', $objDB );
// 支払条件
$aryData["lngPayConditionCode"] = fncGetPulldown( "m_paycondition", "lngpayconditioncode", "strpayconditionname", '', '', $objDB );
// 仕入日
$aryData["dtmStockAppDate"] = date('Y/m/d',time());
// フォームURL
$aryData["actionUrl"] = "/pc/regist/regist_confirm.php";

$objDB->close();

echo fncGetReplacedHtmlWithBase("base_mold.html", "pc/regist/pc_regist.html", $aryData, $objAuth);
