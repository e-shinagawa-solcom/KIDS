<?php

// ----------------------------------------------------------------------------
/**
 *       商品管理  修正画面
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
 *         ・修正時登録画面を表示
 *         ・入力エラーチェック
 *         ・登録ボタン押下後、登録確認画面へ
 *
 *       更新履歴
 *
 */
// ----------------------------------------------------------------------------

//-------------------------------------------------------------------------
// ■ ライブラリファイル読込
//-------------------------------------------------------------------------
include 'conf.inc';
require LIB_FILE;
require "libsql.php";
// require_once LIB_DEBUGFILE;
require_once CLS_IMAGELO_FILE;

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
if ($_GET) {
    $aryData = $_GET;
} else if ($_POST) {
    $aryData = $_POST;
}

// セッション確認
$objAuth = fncIsSession($aryData["strSessionID"], $objAuth, $objDB);

$lngInputUserCode = $objAuth->UserCode;

// 300 商品管理
if (!fncCheckAuthority(DEF_FUNCTION_P0, $objAuth)) {
    fncOutputError(9018, DEF_WARNING, "アクセス権限がありません。", true, "", $objDB);
}

// 306 商品管理（商品修正）
if (!fncCheckAuthority(DEF_FUNCTION_P6, $objAuth)) {
    fncOutputError(9018, DEF_WARNING, "アクセス権限がありません。", true, "", $objDB);
}

$lngProductNo = $aryData['lngProductNo'];
$lngRevisionNo = $aryData["lngRevisionNo"];

$aryQuery = array();
$aryQuery[] = "SELECT ";
$aryQuery[] = "lngproductno, ";
$aryQuery[] = "strProductCode, "; //2:製品コード
$aryQuery[] = "strProductName, "; //3:製品名称
$aryQuery[] = "strProductEnglishName, "; //4:製品名称(英語)
$aryQuery[] = "lngInChargeGroupCode, "; //5:部門
$aryQuery[] = "lngInChargeUserCode, "; //6:担当者
$aryQuery[] = "lnginputusercode, "; //7:入力者
$aryQuery[] = "lngDevelopUserCode, "; //8:開発担当者
$aryQuery[] = "strGoodsCode, "; //9:商品コード
$aryQuery[] = "strGoodsName, "; //10:商品名称
$aryQuery[] = "lngCustomerCompanyCode, "; //11:顧客
$aryQuery[] = "lngCustomerUserCode, "; //13:顧客担当者コード (NULL)
$aryQuery[] = "strCustomerUserName, "; //14:顧客担当者()
$aryQuery[] = "lngPackingUnitCode, "; //15:荷姿単位(int2)
$aryQuery[] = "lngProductUnitCode, "; //16:製品単位(int2)
$aryQuery[] = "trim(To_char(lngBoxQuantity, '9,999,999,999')) as lngBoxQuantity, "; //17:内箱（袋）入数(int4)
$aryQuery[] = "trim(To_char(lngCartonQuantity,'9,999,999,999')) as lngCartonQuantity, "; //18:カートン入数(int4)
$aryQuery[] = "trim(To_char(lngProductionQuantity,'9,999,999,999')) as lngProductionQuantity, "; //19:生産予定数()
$aryQuery[] = "lngProductionUnitCode, "; //20:生産予定数の単位()
$aryQuery[] = "trim(To_char(lngFirstDeliveryQuantity,'9,999,999,999')) as lngFirstDeliveryQuantity, "; //21:初回納品数(int4)
$aryQuery[] = "lngFirstDeliveryUnitCode, "; //22:初回納品数の単位()
$aryQuery[] = "lngFactoryCode, "; //23:生産工場()
$aryQuery[] = "lngAssemblyFactoryCode, "; //24:アッセンブリ工場()
$aryQuery[] = "lngDeliveryPlaceCode, "; //25:納品場所(int2)
$aryQuery[] = "To_char(dtmDeliveryLimitDate,'YYYY/MM') as dtmDeliveryLimitDate, "; //26:納品期限日()
$aryQuery[] = "trim(To_char(curProductPrice, '9,999,999,990.99')) as curProductPrice, "; //27:卸値()
$aryQuery[] = "trim(To_char(curRetailPrice, '9,999,999,990.99')) as curRetailPrice,"; //28:売値()
$aryQuery[] = "lngTargetAgeCode, "; //29:対象年齢()
$aryQuery[] = "trim(To_char(lngRoyalty, '990.99')) as lngRoyalty,"; //30:ロイヤルティー()
$aryQuery[] = "lngCertificateClassCode, "; //31:証紙()
$aryQuery[] = "lngCopyrightCode, "; //32:版権元()
$aryQuery[] = "strCopyrightDisplayStamp, "; //33:版権表示(刻印)
$aryQuery[] = "strCopyrightDisplayPrint, "; //34:版権表示(印刷物)
$aryQuery[] = "lngProductFormCode, "; //35:商品形態()
$aryQuery[] = "strProductComposition, "; //36:製品構成()
$aryQuery[] = "strAssemblyContents, "; //37:アッセンブリ内容()
$aryQuery[] = "strSpecificationDetails, "; //38:仕様詳細()
$aryQuery[] = "strNote, "; //39:備考
$aryQuery[] = "To_char(dtmInsertDate,'YYYY/MM/DD HH24:MI') as dtmInsertDate, "; //41:登録日
$aryQuery[] = "strcopyrightnote, "; //43:版権元備考
$aryQuery[] = "lngCategoryCode, "; // カテゴリー
$aryQuery[] = "strrevisecode "; // 再販コード

$aryQuery[] = "FROM m_product ";
$aryQuery[] = "WHERE  bytinvalidflag = false";
$aryQuery[] = " AND lngproductno = " . $lngProductNo ."";
$aryQuery[] = " AND lngRevisionNo = " . $lngRevisionNo ."";
$strQuery = implode("\n", $aryQuery);

$objDB->freeResult($lngResultID);
if (!$lngResultID = $objDB->execute($strQuery)) {
    fncOutputError(9051, DEF_ERROR, "", true, "", $objDB);
    $objDB->close();
    return true;

}

if (!$lngResultNum = pg_Num_Rows($lngResultID)) {
    fncOutputError(303, DEF_ERROR, "", true, "", $objDB);
    $objDB->close();
    return true;
}

$aryResult = array();
$aryResult = $objDB->fetchArray($lngResultID, 0);

//-------------------------------------------------------------------------
// ■「製品」にログインユーザーが属しているかチェック
//-------------------------------------------------------------------------
$strFncFlag = "P";
$blnCheck = fncCheckInChargeProduct($aryResult["lngproductno"], $lngInputUserCode, $strFncFlag, $objDB);

// ユーザーが対象製品に属していない場合
if (!$blnCheck) {
    fncOutputError(9060, DEF_WARNING, "", true, "", $objDB);
}

//コードから値を参照

// 部門のコード
$lngInchargeGroupCode = $aryResult["lnginchargegroupcode"];
if ($lngInchargeGroupCode) {
    $aryResult["lnginchargegroupcode"] = fncGetMasterValue("m_group", "lnggroupcode", "strgroupdisplaycode", $lngInchargeGroupCode, 'bytGroupDisplayFlag=true', $objDB);
    // 部門の名称
    $aryResult["strinchargegroupname"] = fncGetMasterValue("m_group", "lnggroupcode", "strgroupdisplayname", $lngInchargeGroupCode, "bytgroupdisplayflag=true", $objDB);
}

// 担当者のコード
$lngUserCode = $aryResult["lnginchargeusercode"];

if ($lngUserCode) {
    $aryResult["lnginchargeusercode"] = fncGetMasterValue("m_user", "lngusercode", "struserdisplaycode", $lngUserCode, '', $objDB);
    // 担当者の名称
    $aryResult["strinchargeusername"] = fncGetMasterValue("m_user", "lngusercode", "struserdisplayname", $lngUserCode, '', $objDB);
}

// 開発担当者のコード
$lngDevelopUserCode = $aryResult["lngdevelopusercode"];
if ($lngDevelopUserCode) {
    $aryResult["lngdevelopusercode"] = fncGetMasterValue("m_user", "lngusercode", "struserdisplaycode", $lngDevelopUserCode, '', $objDB);
    // 開発担当者の名称
    $aryResult["strdevelopusername"] = fncGetMasterValue("m_user", "lngusercode", "struserdisplayname", $lngDevelopUserCode, '', $objDB);
}
// 顧客の名称コード
$lngCustomerCompanyCode = $aryResult["lngcustomercompanycode"];
if ($lngCustomerCompanyCode) {
    $aryResult["lngcustomercompanycode"] = fncGetMasterValue("m_company", "lngcompanycode", "strcompanydisplaycode", $lngCustomerCompanyCode, '', $objDB);
    // 顧客の名称
    $aryResult["strcustomercompanyname"] = fncGetMasterValue("m_company", "lngcompanycode", "strcompanydisplayname", $lngCustomerCompanyCode, '', $objDB);
    // :顧客識別コード
    $aryResult["strcustomerdistinctcode"] = fncGetMasterValue("m_company", "lngcompanycode", "strdistinctcode", $aryResult["lngcustomercompanycode"], '', $objDB);
}

//生産工場コード
$lngFactoryCode = $aryResult["lngfactorycode"];
if ($lngFactoryCode) {
    $aryResult["lngfactorycode"] = fncGetMasterValue("m_company", "lngcompanycode", "strcompanydisplaycode", $lngFactoryCode, '', $objDB);
    //納品場所の名称
    $aryResult["strfactoryname"] = fncGetMasterValue("m_company", "lngcompanycode", "strcompanydisplayname", $lngFactoryCode, '', $objDB);
}

//アッセンブリ工場コード
$lngAssemblyFactoryCode = $aryResult["lngassemblyfactorycode"];
if ($lngAssemblyFactoryCode) {
    $aryResult["lngassemblyfactorycode"] = fncGetMasterValue("m_company", "lngcompanycode", "strcompanydisplaycode", $lngAssemblyFactoryCode, '', $objDB);
    //アッセンブリ工場
    $aryResult["strassemblyfactoryname"] = fncGetMasterValue("m_company", "lngcompanycode", "strcompanydisplayname", $lngAssemblyFactoryCode, '', $objDB);}

//納品場所コード
$lngDeliveryPlaceCode = $aryResult["lngdeliveryplacecode"];
if ($lngDeliveryPlaceCode) {
    $aryResult["lngdeliveryplacecode"] = fncGetMasterValue("m_company", "lngcompanycode", "strcompanydisplaycode", $lngDeliveryPlaceCode, '', $objDB);
    //納品場所
    $aryResult["strdeliveryplacename"] = fncGetMasterValue("m_company", "lngcompanycode", "strcompanydisplayname", $lngDeliveryPlaceCode, '', $objDB);
}

// 顧客担当者
$lngCustomerUserCode = $aryResult["lngcustomerusercode"];

if (strcmp($aryResult["lngcustomerusercode"], "") != 0) {
    $aryResult["strcustomerusercode"] = fncGetMasterValue("m_user", "lngusercode", "struserdisplaycode", $lngCustomerUserCode, '', $objDB);
    $aryResult["strcustomerusername"] = fncGetMasterValue("m_user", "lngusercode", "struserdisplayname", $lngCustomerUserCode, '', $objDB);
}

// 仕様詳細の特殊文字変換
$aryResult["strspecificationdetails"] = fncHTMLSpecialChars($aryResult["strspecificationdetails"]);

//オプション値の設定 ==============================================================
// 連想配列のインデックスには、小文字で指定しないとだめ

// カテゴリー
$aryResult["lngcategorycode"] = fncGetPulldownQueryExec(fncSqlqueryCategory(array(0 => $objAuth->UserCode)), $aryResult["lngcategorycode"], $objDB);
// 荷姿単位
$aryResult["lngpackingunitcode"] = fncGetPulldown("m_productunit", "lngProductUnitCode", "strProductUnitName", $aryResult["lngpackingunitcode"], "WHERE bytpackingconversionflag=true", $objDB);
// 製品単位
$aryResult["lngproductunitcode"] = fncGetPulldown("m_productunit", "lngProductUnitCode", "strProductUnitName", $aryResult["lngproductunitcode"], "WHERE bytproductconversionflag=true", $objDB);
// 生産予定数の単位
$aryResult["lngproductionunitcode"] = fncGetPulldown("m_productunit", "lngProductUnitCode", "strProductUnitName", $aryResult["lngproductionunitcode"], '', $objDB);
// 初回納品数の単位
$aryResult["lngfirstdeliveryunitcode"] = fncGetPulldown("m_productunit", "lngProductUnitCode", "strProductUnitName", $aryResult["lngfirstdeliveryunitcode"], '', $objDB);
// 対象年齢
$aryResult["lngtargetagecode"] = fncGetPulldown("m_targetage", "lngTargetAgeCode", "strTargetAgeName", $aryResult["lngtargetagecode"], '', $objDB);
// 証紙 テーブルなし
$aryResult["lngcertificateclasscode"] = fncGetPulldown("m_CertificateClass", "lngcertificateclasscode", "strcertificateclassname", $aryResult["lngcertificateclasscode"], '', $objDB);
// 版権元
$aryResult["lngcopyrightcode"] = fncGetPulldown("m_copyright", "lngcopyrightcode", "strcopyrightname", $aryResult["lngcopyrightcode"], '', $objDB);
// 商品形態 テーブルなし
$aryResult["lngproductformcode"] = fncGetPulldown("m_productform", "lngproductformcode", "strproductformname", $aryResult["lngproductformcode"], '', $objDB);

// 企画進行状況 ===================================================================
$lngproductno = $aryResult["lngproductno"];
$aryQuery2[] = "SELECT lnggoodsplancode,lngrevisionno,lnggoodsplanprogresscode, ";
$aryQuery2[] = "To_char(dtmrevisiondate,'YYYY/MM/DD HH24:MI') as dtmrevisiondate ";
$aryQuery2[] = "FROM t_goodsplan WHERE lnggoodsplancode = (";
$aryQuery2[] = "SELECT max(lnggoodsplancode) FROM t_goodsplan WHERE lngproductno = ";
$aryQuery2[] = "$lngproductno )";

$strQuery2 = "";
$strQuery2 = implode("\n", $aryQuery2);

//echo "$strQuery2<br><br>";
$objDB->freeResult($lngResultID2);
if (!$lngResultID2 = $objDB->execute($strQuery2)) {
    fncOutputError(9051, DEF_ERROR, "", true, "", $objDB);
    $objDB->close();
    return true;

}

if (!$lngResultNum = pg_Num_Rows($lngResultID2)) {
    fncOutputError(303, DEF_ERROR, "", true, "", $objDB);
    $objDB->close();
    return true;
}

$aryResult2 = array();
$aryResult2 = $objDB->fetchArray($lngResultID2, 0);

// 企画進行状況 =============================================================
$aryResult["lngGoodsPlanProgressCode"] = fncGetPulldown("m_goodsplanprogress", "lnggoodsplanprogresscode", "strgoodsplanprogressname", $aryResult2["lnggoodsplanprogresscode"], '', $objDB);
//改訂番号
$aryResult["lngRevisionNo"] = $aryResult2["lngrevisionno"];
//改訂日時
$aryResult["dtmRevisionData"] = $aryResult2["dtmrevisiondate"];
//goodsplancode
$aryResult["lnGgoodsPlanCode"] = $aryResult2["lnggoodsplancode"];
//-------------------------------------------------------------------------
// イメージファイルの取得処理
//-------------------------------------------------------------------------

$objImageLo = new clsImageLo();
$strDestPath = constant("USER_IMAGE_PEDIT_TMPDIR");
// キーコード（製品コード）を基にして、イメージファイルの抽出処理（関連画像がテンポラリディレクトリに出力される）
$objImageLo->getImageLo($objDB, $strProductCode, $strDestPath, $aryImageInfo);

// フォームURL
if (strcmp($aryData["strurl"], "") == 0) {
    $aryResult["strurl"] = 'renew.php?strProductCode=$strProductCode&strSessionID=$aryData["strSessionID"]';
}

$aryResult["strActionURL"] = 'renew.php?strProductCode=$strProductCode&strSessionID=$aryData["strSessionID"]';

$aryResult["strSessionID"] = $aryData["strSessionID"];
$aryResult["strProductCode"] = $aryData["strProductCode"];
$aryResult["RENEW"] = true;

// submit関数
$aryResult["lngRegistConfirm"] = 0;

// ヘルプ対応
$aryResult["lngFunctionCode"] = DEF_FUNCTION_P6;

/**
debug

仕様詳細画像ファイルHIDDEN生成
 */
// 再取得用に設定
if (is_array($aryImageInfo['strTempImageFile'])) {
    $lngImageCnt = count($aryImageInfo['strTempImageFile']);
} else {
    $lngImageCnt = 0;
}

if ($lngImageCnt) {
    for ($i = 0; $i < $lngImageCnt; $i++) {
        $aryUploadImagesHidden[] = '<input type="hidden" name="uploadimages[]" value="' . $aryImageInfo['strTempImageFile'][$i] . '" />';
    }

    // 再取得用に設定
    $aryResult["re_uploadimages"] = implode("\n", $aryUploadImagesHidden);
    $aryResult["re_editordir"] = '<input type="hidden" name="strTempImageDir" value="' . $aryImageInfo['strTempImageDir'][0] . '" />';
}

// テンプレート読み込み
echo fncGetReplacedHtmlWithBase("base_mold.html", "p/modify/p_modify.html", $aryResult ,$objAuth );

$objDB->close();
return true;
