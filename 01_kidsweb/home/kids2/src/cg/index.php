<?
// 設定読み込み
include_once('conf.inc');

// ライブラリ読み込み
require (LIB_FILE);
require (SRC_ROOT . "uc/cmn/lib_uc.php");

// DB接続
$objDB   = new clsDB();
$objAuth = new clsAuth();
$objDB->open( "", "", "", "" );

$aryData = $_GET;

// ユーザー情報、ユーザー修正の場合
if ( $aryData["lngFunctionCode"] == DEF_FUNCTION_UC1 || $aryData["lngFunctionCode"] == DEF_FUNCTION_UC5 )
{
	// 企業SELECTメニュー(同じ属性の企業のみ)
	$aryParts["slctCompany"]  = fncGetPulldown( "m_Company c, m_AttributeRelation ar, m_AttributeRelation ar2", "c.lngCompanyCode", "c.strCompanyDisplayCode, c.strCompanyName", $aryData["lngCompanyCode"], "WHERE ar2.lngCompanyCode = " . $aryData["lngCompanyCode"] . " AND ( ar2.lngAttributeCode = 1 OR ar2.lngAttributeCode = 2 ) AND ar.lngAttributeCode = ar2.lngAttributeCode AND c.bytCompanyDisplayFlag = TRUE AND c.lngCompanyCode = ar.lngCompanyCode GROUP BY c.lngCompanyCode, c.strCompanyDisplayCode, c.strCompanyName", $objDB );

	// グループSELECTメニュー
	$aryParts["slctGroup2"]  = fncGetPulldown( "m_Group", "lngGroupCode", "strGroupDisplayCode, strGroupName", "", "WHERE bytGroupDisplayFlag = TRUE AND lngCompanyCode = " . $aryData["lngCompanyCode"], $objDB );

}

// ユーザー登録の場合
else
{
	// 企業SELECTメニュー(すべての企業)
	$aryParts["slctCompany"]  = fncGetPulldown( "m_Company c, m_AttributeRelation ar", "c.lngCompanyCode", "c.strCompanyDisplayCode, c.strCompanyName", $aryData["lngCompanyCode"], "WHERE ( ar.lngAttributeCode = 1 OR ar.lngAttributeCode = 2 ) AND c.bytCompanyDisplayFlag = TRUE AND c.lngCompanyCode = ar.lngCompanyCode", $objDB );
}

// HTML出力

$objTemplate = new clsTemplate();
$objTemplate->getTemplate( "cg/index.tmpl" );
$objTemplate->replace( $aryParts );
$objTemplate->complete();
echo $objTemplate->strTemplate;

?>
