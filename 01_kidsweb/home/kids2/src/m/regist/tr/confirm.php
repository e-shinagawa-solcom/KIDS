<?
/** 
*	マスタ管理 想定レートマスタ 確認画面
*
*	@package   KIDS
*	@license   http://www.Solcom.co.jp/ 
*	@copyright Copyright &copy; 2019, Solcom 
*	@author    Solcom rin 
*	@access    public
*	@version   1.00
*
*/
// edit.php -> strSessionID        -> confirm.php
// edit.php -> lngActionCode       -> confirm.php
// edit.php -> lngmonetaryunitcode -> confirm.php
// edit.php -> curconversionrate   -> confirm.php
// edit.php -> dtmapplystartdate   -> confirm.php
// edit.php -> dtmapplyenddate     -> confirm.php

// 実行へ
// confirm.php -> strSessionID        -> action.php
// confirm.php -> lngActionCode       -> action.php
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
$aryCheck["lngmonetaryunitcode"] = "null:number(1,2147483647)";
$aryCheck["curconversionrate"]   = "null:number(0.000001,9999999999.999999)";
$aryCheck["dtmapplystartdate"]   = "null:date(/)";
$aryCheck["dtmapplyenddate"]     = "null:date(/)";

// 文字列チェック
$aryCheckResult = fncAllCheck( $aryData, $aryCheck );
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
	$objMaster->setMasterTable( "m_TemporaryRate", "lngmonetaryunitcode", $aryData["lngmonetaryunitcode"], Array ( "dtmapplystartdate" => $aryData["dtmapplystartdate"] ), $objDB );
	$objMaster->setAryMasterInfo( $aryData["lngmonetaryunitcode"], "" );
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
	$objMaster->aryCheckQuery["INSERT"] .= " AND NOT (dtmapplyenddate < '" . $aryData["dtmapplystartdate"] . "' OR dtmapplystartdate > '" . $aryData["dtmapplyenddate"] . "' )";
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
$aryParts["lngActionCode"]   = $aryData["lngActionCode"];
$aryParts["strSessionID"]    = $aryData["strSessionID"];
// lngmonetaryunitcode の(CODE+NAME)取得
$aryMonetaryUnitCode = fncGetMasterValue( "m_MonetaryUnit", "lngmonetaryunitcode", "strMonetaryUnitName || ':' || strMonetaryUnitSign", "Array", "", $objDB );
$aryParts["lngMonetaryUnitName"] = $aryMonetaryUnitCode[$aryData["lngmonetaryunitcode"]];
$aryParts["lngmonetaryunitcode"] = $aryData["lngmonetaryunitcode"];
$aryParts["curconversionrate"] = $aryData["curconversionrate"];
$aryParts["dtmapplystartdate"] = $aryData["dtmapplystartdate"];
$aryParts["dtmapplyenddate"] = $aryData["dtmapplyenddate"];

if ( $bytErrorFlag )
{
	//echo fncGetReplacedHtml( "m/regist/edit.tmpl", $aryData, $objAuth );
	echo "<meta http-equiv=\"content-type\" content=\"text/html; charset=UTF-8\">";
	echo "<form action=/m/regist/tr/edit.php method=GET>";
	echo getArrayTable( $aryData, "HIDDEN" );
	echo "</form>";
	echo "<script language=javascript>document.forms[0].submit();</script>";
}
else
{
	$objTemplate = new clsTemplate();
	$objTemplate->getTemplate( "m/regist/tr/confirm.tmpl" );
	$objTemplate->replace( $aryParts );
	$objTemplate->complete();
	echo $objTemplate->strTemplate;
}


$objDB->close();


return TRUE;
?>


