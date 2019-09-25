<?
/** 
*	マスタ管理 通貨レートマスタ データ入力画面
*
*	@package   KIDS
*	@license   http://www.wiseknot.co.jp/ 
*	@copyright Copyright &copy; 2003, Wiseknot 
*	@author    Kenji Chiba <k-chiba@wiseknot.co.jp> 
*	@access    public
*	@version   1.00
*
*/
// 登録画面
// index.php -> strSessionID        -> edit.php
// index.php -> lngActionCode       -> edit.php
//
// 修正画面
// index.php -> strSessionID        -> edit.php
// index.php -> lngActionCode       -> edit.php
// index.php -> lngmonetaryratecode -> edit.php
// index.php -> lngmonetaryunitcode -> edit.php
// index.php -> dtmapplystartdate   -> edit.php
//
// 確認画面へ
// edit.php -> strSessionID        -> confirm.php
// edit.php -> lngActionCode       -> confirm.php
// edit.php -> lngmonetaryratecode -> confirm.php
// edit.php -> lngmonetaryunitcode -> confirm.php
// edit.php -> curconversionrate   -> confirm.php
// edit.php -> dtmapplystartdate   -> confirm.php
// edit.php -> dtmapplyenddate     -> confirm.php


// 設定読み込み
include_once('conf.inc');

// ライブラリ読み込み
require (LIB_FILE);
require (SRC_ROOT . "m/cmn/lib_m.php");

// DB接続
$objDB   = new clsDB();
$objAuth = new clsAuth();
$objDB->open( "", "", "", "" );

// POSTデータ取得
$aryData = $_GET;


// セッション確認
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );


// 権限確認
if ( !fncCheckAuthority( DEF_FUNCTION_M0, $objAuth ) )
{
	fncOutputError ( 9052, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
}


$aryCheck["strSessionID"]        = "null:numenglish(32,32)";
$aryCheck["lngActionCode"]       = "null:number(" . DEF_ACTION_INSERT . "," . DEF_ACTION_DELETE . ")";

if ( $aryData["lngActionCode"] == DEF_ACTION_UPDATE )
{
	$aryCheck["lngmonetaryratecode"] = "null:number(1,2147483647)";
	$aryCheck["lngmonetaryunitcode"] = "null:number(1,2147483647)";
	$aryCheck["dtmapplystartdate"]   = "null:date(/)";
}

// 文字列チェック
$aryCheckResult = fncAllCheck( $aryData, $aryCheck );
fncPutStringCheckError( $aryCheckResult, $objDB );


// エラーがない場合、マスターオブジェクト生成、文字列チェック実行
if ( join ( $aryCheckResult ) )
{
	fncOutputError ( 9052, DEF_WARNING, "マスタ管理失敗", TRUE, "", $objDB );
}

// マスターオブジェクト生成
$objMaster = new clsMaster();

//////////////////////////////////////////////////////////////////////////
// キーコードの表示処理
//////////////////////////////////////////////////////////////////////////
// 新規の場合、キー入力項目生成
if ( $aryData["lngActionCode"] == DEF_ACTION_INSERT )
{
	// 通貨レートマスタから行データとカラム名取得
	$objMaster->setMasterTable( "m_MonetaryRate", "lngmonetaryratecode", NULL, Array (), $objDB );

	// カラム数取得
	$lngColumnNum = count ( $objMaster->aryColumnName );

	// 通貨レート
	$aryParts["MASTER"][0]  = "<span class=\"InputSegs\"><select id=\"Input0\" name=\"" . $objMaster->aryColumnName[0] . "\" onChange=\"subLoadMasterText( 'cnMonetaryRate', this, document.forms[0]." . $objMaster->aryColumnName[3] . ", Array(this.value, document.forms[0]." . $objMaster->aryColumnName[1] . ".value), objDataSourceSetting, 0 );subLoadMasterText( 'cnMonetaryRate2', this, document.forms[0]." . $objMaster->aryColumnName[4] . ", Array(document.forms[0]." . $objMaster->aryColumnName[0] . ".value, document.forms[0]." . $objMaster->aryColumnName[1] . ".value), objDataSourceSetting2, 1 );\">\n";
	$aryParts["MASTER"][0] .= fncGetPulldown( "m_MonetaryRateClass", "lngMonetaryRateCode", "strMonetaryRateName", $aryData["lngmonetaryratecode"], "", $objDB );
	$aryParts["MASTER"][0] .= "</select></span>\n";

	// 通貨単位
	$aryParts["MASTER"][1]  = "<span class=\"InputSegs\"><select id=\"Input1\" name=\"" . $objMaster->aryColumnName[1] . "\" onChange=\"subLoadMasterText( 'cnMonetaryRate', this, document.forms[0]." . $objMaster->aryColumnName[3] . ", Array(document.forms[0]." . $objMaster->aryColumnName[0] . ".value, this.value), objDataSourceSetting, 0 );subLoadMasterText( 'cnMonetaryRate2', this, document.forms[0]." . $objMaster->aryColumnName[4] . ", Array(document.forms[0]." . $objMaster->aryColumnName[0] . ".value, document.forms[0]." . $objMaster->aryColumnName[1] . ".value), objDataSourceSetting2, 1 );\">\n";
	$aryParts["MASTER"][1] .= fncGetPulldown( "m_MonetaryUnit", "lngMonetaryUnitCode", "strMonetaryUnitName || ':' ||strMonetaryUnitSign", $aryData["lngmonetaryunitcode"], "WHERE lngMonetaryUnitCode > 1", $objDB );
	$aryParts["MASTER"][1] .= "</select></span>\n";

	// 適用開始月
	$aryParts["MASTER"][3] = "<span class=\"InputSegs\"><input id=\"Input3\" type=\"text\" name=\"" . $objMaster->aryColumnName[3] . "\" value=\"" . $aryData[$objMaster->aryColumnName[3]]. "\" onfocus=\"chColorOn(this);\" onblur=\"chColorOff(this);\" maxlength=\"10\"></span>\n";

	// 換算レートに戻ってきた際のデータを格納
	$objMaster->aryData[0][$objMaster->aryColumnName[2]] = $aryData[$objMaster->aryColumnName[2]];

	// 適用終了月に戻ってきた際のデータを格納
	$objMaster->aryData[0][$objMaster->aryColumnName[4]] = $aryData[$objMaster->aryColumnName[4]];
}

// 登録以外の場合、キー入力項目にスモークを掛ける
elseif ( $aryData["lngActionCode"] != DEF_ACTION_INSERT )
{
	// 通貨レートマスタから指定行データとカラム名取得
	$objMaster->setMasterTable( "m_MonetaryRate", "lngmonetaryratecode", $aryData["lngmonetaryratecode"], Array ( "lngmonetaryunitcode" => $aryData["lngmonetaryunitcode"], "dtmapplystartdate" => $aryData["dtmapplystartdate"] ), $objDB );

	// カラム数取得
	$lngColumnNum = count ( $objMaster->aryColumnName );

	// キーコード表示
	$aryData["HIDDEN"]  = "<input type=\"hidden\" name=\"" . $objMaster->aryColumnName[0] . "\" value=\"" . $objMaster->aryData[0][$objMaster->aryColumnName[0]] . "\">\n";
	$aryData["HIDDEN"] .= "<input type=\"hidden\" name=\"" . $objMaster->aryColumnName[1] . "\" value=\"" . $objMaster->aryData[0][$objMaster->aryColumnName[1]] . "\">\n";
	$aryData["HIDDEN"] .= "<input type=\"hidden\" name=\"" . $objMaster->aryColumnName[3] . "\" value=\"" . $objMaster->aryData[0][$objMaster->aryColumnName[3]] . "\">\n";

	// 通貨レート
	$aryParts["MASTER"][0]  = "<span class=\"InputSegs\"><select id=\"Input0\" disabled>\n";
	$aryParts["MASTER"][0] .= fncGetPulldown( "m_MonetaryRateClass", "lngMonetaryRateCode", "strMonetaryRateName", $objMaster->aryData[0][$objMaster->aryColumnName[0]], "", $objDB );
	$aryParts["MASTER"][0] .= "</select></span>\n";

	// 通貨単位
	$aryParts["MASTER"][1]  = "<span class=\"InputSegs\"><select id=\"Input1\" disabled>\n";
	$aryParts["MASTER"][1] .= fncGetPulldown( "m_MonetaryUnit", "lngMonetaryUnitCode", "strMonetaryUnitName || ':' ||strMonetaryUnitSign", $objMaster->aryData[0][$objMaster->aryColumnName[1]], "", $objDB );
	$aryParts["MASTER"][1] .= "</select></span>\n";

	// 適用開始月
	$aryParts["MASTER"][3] = "<span class=\"InputSegs\"><input id=\"Input3\" type=\"text\" value=\"" . $objMaster->aryData[0][$objMaster->aryColumnName[3]] . "\" onfocus=\"chColorOn(this);\" onblur=\"chColorOff(this);\" disabled></span>\n";}

// 新規登録、修正共通入力項目
// 換算レート
$aryParts["MASTER"][2] = "<span class=\"InputSegs\"><input id=\"Input2\" type=\"text\" name=\"" . $objMaster->aryColumnName[2] . "\" value=\"" . $objMaster->aryData[0][$objMaster->aryColumnName[2]]. "\" onfocus=\"chColorOn(this);\" onblur=\"chColorOff(this);\" maxlength=\"10\"></span>\n";

// 適用終了月
$aryParts["MASTER"][4] = "<span class=\"InputSegs\"><input id=\"Input4\" type=\"text\" name=\"" . $objMaster->aryColumnName[4] . "\" value=\"" . $objMaster->aryData[0][$objMaster->aryColumnName[4]]. "\" onfocus=\"chColorOn(this);\" onblur=\"chColorOff(this);\" maxlength=\"10\"></span>\n";

// カラム表示
$aryData["COLUMN"] = "<span id=\"Column0\" class=\"ColumnSegs\"></span>\n<span id=\"Column1\" class=\"ColumnSegs\"></span>\n<span id=\"Column2\" class=\"ColumnSegs\"></span>\n<span id=\"Column3\" class=\"ColumnSegs\"></span>\n<span id=\"Column4\" class=\"ColumnSegs\"></span>\n";


//////////////////////////////////////////////////////////////////////////
// 結果取得、出力処理
//////////////////////////////////////////////////////////////////////////
//echo fncGetReplacedHtml( "m/regist/parts.tmpl", $aryData, $objAuth );
$count = count ( $aryParts["MASTER"] );
for ( $i = 0; $i < $count; $i++ )
{
	$aryData["MASTER"] .= $aryParts["MASTER"][$i];
}

$objDB->close();


$aryData["lngLanguageCode"] = 1;
$aryData["strTableName"]    = $objMaster->strTableName;

// HTML出力
$objTemplate = new clsTemplate();
$objTemplate->getTemplate( "m/regist/c/edit.tmpl" );
$objTemplate->replace( $aryData );
$objTemplate->complete();
echo $objTemplate->strTemplate;


return TRUE;
?>
