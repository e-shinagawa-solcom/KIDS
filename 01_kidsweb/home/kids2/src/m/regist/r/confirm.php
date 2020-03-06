<?
/** 
*	マスタ管理 通貨レートマスタ 確認画面
*
*	@package   KIDS
*	@license   http://www.wiseknot.co.jp/ 
*	@copyright Copyright &copy; 2003, Wiseknot 
*	@author    Kenji Chiba <k-chiba@wiseknot.co.jp> 
*	@access    public
*	@version   1.00
*
*/
// edit.php -> strSessionID        -> confirm.php
// edit.php -> lngActionCode       -> confirm.php
// edit.php -> lngmonetaryratecode -> confirm.php
// edit.php -> lngmonetaryunitcode -> confirm.php
// edit.php -> curconversionrate   -> confirm.php
// edit.php -> dtmapplystartdate   -> confirm.php
// edit.php -> dtmapplyenddate     -> confirm.php

// 実行へ
// confirm.php -> strSessionID        -> action.php
// confirm.php -> lngActionCode       -> action.php
// confirm.php -> lngmonetaryratecode -> action.php
// confirm.php -> lngmonetaryunitcode -> action.php
// confirm.php -> curconversionrate   -> action.php
// confirm.php -> dtmapplystartdate   -> action.php
// confirm.php -> dtmapplyenddate     -> action.php


// 設定読み込み
include_once('conf.inc');

// ライブラリ読み込み
require (LIB_FILE);
require (SRC_ROOT . "m/cmn/lib_m.php");

// DB接続
$objDB   = new clsDB();
$objAuth = new clsAuth();
$objDB->open( "", "", "", "" );

// GETデータ取得
$aryData = $_GET;



// セッション確認
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );


// 権限確認
if ( !fncCheckAuthority( DEF_FUNCTION_M0, $objAuth ) )
{
	fncOutputError ( 9060, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
}


$aryCheck["strSessionID"]        = "null:numenglish(32,32)";
$aryCheck["lngActionCode"]       = "null:number(" . DEF_ACTION_INSERT . "," . DEF_ACTION_DELETE . ")";
$aryCheck["lngmonetaryratecode"] = "null:number(1,2147483647)";
$aryCheck["lngmonetaryunitcode"] = "null:number(1,2147483647)";
$aryCheck["curconversionrate"]   = "null:number(0.000001,9999999999.999999)";
$aryCheck["dtmapplystartdate"]   = "null:date(/)";
$aryCheck["dtmapplyenddate"]     = "null:date(/)";


// 文字列チェック
$aryCheckResult = fncAllCheck( $aryData, $aryCheck );
//fncPutStringCheckError( $aryCheckResult, $objDB );

if ( $aryData["dtmapplystartdate"] > $aryData["dtmapplyenddate"] )
{
	$aryCheckResult["dtmapplystartdate_Error"] = 1;
	$aryCheckResult["dtmapplyenddate_Error"]   = 1;
}

// エラーがない場合、マスターオブジェクト生成、文字列チェック実行
if ( !$aryCheckResult["strSessionID"] && !join ( $aryCheckResult ) )
{
	// マスターオブジェクト生成
	$objMaster = new clsMaster();
	$objMaster->setMasterTable( "m_MonetaryRate", "lngmonetaryratecode", $aryData["lngmonetaryratecode"], Array ( "lngmonetaryunitcode" => $aryData["lngmonetaryunitcode"], "dtmapplystartdate" => $aryData["dtmapplystartdate"] ), $objDB );
	$objMaster->setAryMasterInfo( $aryData["lngmonetaryratecode"], $aryData["lngmonetaryunitcode"] );

}


//////////////////////////////////////////////////////////////////////////
// 処理の有効性をチェック
//////////////////////////////////////////////////////////////////////////
// ( 登録 または 修正 ) エラーがない 場合、
// 新規登録、修正チェック実行
if ( ( $aryData["lngActionCode"] == DEF_ACTION_INSERT || $aryData["lngActionCode"] == DEF_ACTION_UPDATE ) && !join ( $aryCheckResult ) )
{
	// チェッククエリ設定
	// 開始、終了2つとも時期が重ならない以外の条件を付加
	// AND NOT ( 終了年月日 < 入力開始年月日 OR 開始年月日 > 入力終了年月日 )
	// 条件追加
	$objMaster->aryCheckQuery["INSERT"] .= " AND NOT ( " . $objMaster->aryColumnName[4] . " < '" . $aryData[$objMaster->aryColumnName[3]] . "' OR " . $objMaster->aryColumnName[3] . " > '" . $aryData[$objMaster->aryColumnName[4]] . "' )";

	list ( $lngResultID, $lngResultNum ) = fncQuery ( $objMaster->aryCheckQuery["INSERT"], $objDB );

	// 新規登録 かつ 結果件数が0以上
	// または
	// 修正 かつ 結果件数が1以外 の場合、エラー
	if ( ( $aryData["lngActionCode"] == DEF_ACTION_INSERT && $lngResultNum > 0 ) || ( $aryData["lngActionCode"] == DEF_ACTION_UPDATE && $lngResultNum != 1 ) )
	{
		$aryCheckResult[$objMaster->aryColumnName[0] . "_Error"] = 1;
		$aryCheckResult[$objMaster->aryColumnName[1] . "_Error"] = 1;
	}
}


// エラー項目表示処理
list ( $aryData, $bytErrorFlag ) = getArrayErrorVisibility( $aryData, $aryCheckResult, $objDB );


//////////////////////////////////////////////////////////////////////////
// 出力
//////////////////////////////////////////////////////////////////////////
$count = count ( $objMaster->aryColumnName );


$aryParts["lngLanguageCode"] = 1;
$aryParts["lngActionCode"]   = $aryData["lngActionCode"];
$aryParts["strTableName"]    = $objMaster->strTableName;
$aryParts["strKeyName"]      = $objMaster->aryColumnName[0];
$aryParts["lngKeyCode"]      = $aryData[$objMaster->aryColumnName[0]];
$aryParts["strSessionID"]    = $aryData["strSessionID"];


// lngMonetaryRateCode の(CODE+NAME)取得
$aryMonetaryRateCode = fncGetMasterValue( "m_MonetaryRateClass", "lngMonetaryRateCode", "strMonetaryRateName", "Array", "", $objDB );
// lngMonetaryUnitCode の(CODE+NAME)取得
$aryMonetaryUnitCode = fncGetMasterValue( "m_MonetaryUnit", "lngMonetaryUnitCode", "strMonetaryUnitName || ':' || strMonetaryUnitSign", "Array", "", $objDB );

$aryParts["MASTER"] .= "				<tr><td id=\"Column0\" class=\"SegColumn\" width=\"25%\">Column0</td><td class=\"Segs\">" . $aryMonetaryRateCode[$aryData[$objMaster->aryColumnName[0]]] . "</td></tr>\n";
$aryParts["HIDDEN"] .= "<input type=\"hidden\" name=\"" . $objMaster->aryColumnName[0] . "\" value=\"" . $aryData[$objMaster->aryColumnName[0]] . "\">\n";

$aryParts["MASTER"] .= "				<tr><td id=\"Column1\" class=\"SegColumn\" width=\"25%\">Column1</td><td class=\"Segs\">" . $aryMonetaryUnitCode[$aryData[$objMaster->aryColumnName[1]]] . "</td></tr>\n";
$aryParts["HIDDEN"] .= "<input type=\"hidden\" name=\"" . $objMaster->aryColumnName[1] . "\" value=\"" . $aryData[$objMaster->aryColumnName[1]] . "\">\n";

for ( $i = 2; $i < $count; $i++ )
{
	$aryParts["MASTER"] .= "				<tr><td id=\"Column$i\" class=\"SegColumn\" width=\"25%\">Column$i</td><td class=\"Segs\">" . $aryData[$objMaster->aryColumnName[$i]] . "</td></tr>\n";
	$aryParts["HIDDEN"] .= "<input type=\"hidden\" name=\"" . $objMaster->aryColumnName[$i] . "\" value=\"" . $aryData[$objMaster->aryColumnName[$i]] . "\">\n";
}


if ( $bytErrorFlag )
{
	//echo fncGetReplacedHtml( "m/regist/edit.tmpl", $aryData, $objAuth );
	echo "<meta http-equiv=\"content-type\" content=\"text/html; charset=UTF-8\">";
	echo "<form action=/m/regist/r/edit.php method=GET>";
	echo getArrayTable( $aryData, "HIDDEN" );
	echo "</form>";
	echo "<script language=javascript>document.forms[0].submit();</script>";
}
else
{
	$objTemplate = new clsTemplate();
	$objTemplate->getTemplate( "m/regist/c/confirm.tmpl" );
	$objTemplate->replace( $aryParts );
	$objTemplate->complete();
	echo $objTemplate->strTemplate;
}


$objDB->close();


return TRUE;
?>


