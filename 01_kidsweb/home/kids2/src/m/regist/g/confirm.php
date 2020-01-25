<?
/** 
*	マスタ管理 グループマスタ 確認画面
*
*	@package   KIDS
*	@license   http://www.wiseknot.co.jp/ 
*	@copyright Copyright &copy; 2003, Wiseknot 
*	@author    Kenji Chiba <k-chiba@wiseknot.co.jp> 
*	@access    public
*	@version   1.00
*
*/
// 登録、修正
// edit.php -> strSessionID         -> confirm.php
// edit.php -> lngActionCode        -> confirm.php
// edit.php -> lnggroupcode         -> confirm.php
// edit.php -> lngcompanycode       -> confirm.php
// edit.php -> strgroupname         -> confirm.php
// edit.php -> bytgroupdisplayflag  -> confirm.php
// edit.php -> strgroupdisplaycode  -> confirm.php
// edit.php -> strgroupdisplayname  -> confirm.php
// edit.php -> strgroupdisplaycolor -> confirm.php
//
// 削除
// index.php -> strSessionID         -> confirm.php
// index.php -> lngActionCode        -> confirm.php
// index.php -> lnggroupcode         -> confirm.php
//
// 登録、修正実行へ
// confirm.php -> strSessionID         -> action.php
// confirm.php -> lngActionCode        -> action.php
// confirm.php -> lnggroupcode         -> action.php
// confirm.php -> lngcompanycode       -> action.php
// confirm.php -> strgroupname         -> action.php
// confirm.php -> bytgroupdisplayflag  -> action.php
// confirm.php -> strgroupdisplaycode  -> action.php
// confirm.php -> strgroupdisplayname  -> action.php
// confirm.php -> strgroupdisplaycolor -> action.php
//
// 削除実行へ
// confirm.php -> strSessionID        -> action.php
// confirm.php -> lngActionCode       -> action.php
// confirm.php -> lnggroupcode        -> action.php


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
	fncOutputError ( 9052, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
}


$aryCheck["strSessionID"]        = "null:numenglish(32,32)";
$aryCheck["lngActionCode"]       = "null:number(" . DEF_ACTION_INSERT . "," . DEF_ACTION_DELETE . ")";

if ( $aryData["lngActionCode"] != DEF_ACTION_DELETE )
{
	// 色指定がなかった場合、デフォルトで白を設定
	if ( $aryData["strgroupdisplaycolor"] == "" )
	{
		$aryData["strgroupdisplaycolor"] = "#FFFFFF";
	}

	$aryCheck["lnggroupcode"]         = "null:number(0,2147483647)";
	$aryCheck["lngcompanycode"]       = "null:number(0,2147483647)";
	$aryCheck["strgroupname"]         = "null:length(1,100)";
	$aryCheck["bytgroupdisplayflag"]  = "english(1,1)";
	$aryCheck["strgroupdisplaycode"]  = "null:numenglish(1,3)";
	$aryCheck["strgroupdisplayname"]  = "null:length(1,100)";
	$aryCheck["strgroupdisplaycolor"] = "null:color";
}

// 文字列チェック
$aryCheckResult = fncAllCheck( $aryData, $aryCheck );
//fncPutStringCheckError( $aryCheckResult, $objDB );



//////////////////////////////////////////////////////////////////////////
// 処理の有効性をチェック
//////////////////////////////////////////////////////////////////////////
// ( 登録 または 修正 ) エラーがない 場合、
// 新規登録、修正チェック実行
if ( ( $aryData["lngActionCode"] == DEF_ACTION_INSERT || $aryData["lngActionCode"] == DEF_ACTION_UPDATE ) && !join ( $aryCheckResult ) )
{
	// グループコード重複チェック
	$strQuery = "SELECT * FROM m_Group " .
                "WHERE lngGroupCode = " . $aryData["lnggroupcode"];

	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	// 新規登録 かつ 結果件数が0以上
	// または
	// 修正 かつ 結果件数が1以外 の場合、エラー
	if ( ( $aryData["lngActionCode"] == DEF_ACTION_INSERT && $lngResultNum > 0 ) || ( $aryData["lngActionCode"] == DEF_ACTION_UPDATE && $lngResultNum != 1 ) )
	{
		$aryCheckResult["lnggroupcode_Error"] = 1;
		$objDB->freeResult( $lngResultID );
	}

	// 同じ企業内における表示グループコード重複チェック
	$strQuery = "SELECT * FROM m_Group " .
                "WHERE lngCompanyCode = " . $aryData["lngcompanycode"] .
                " AND strGroupDisplayCode = '" . $aryData["strgroupdisplaycode"] . "'";

	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	// 結果件数が0以上の場合、エラー判定処理へ
	if ( $lngResultNum > 0 )
	{
		$objResult = $objDB->fetchObject( $lngResultID, 0 );

		// ( 更新 かつ グループコードが同じ ) 以外 の場合、エラー
		if ( !( $aryData["lngActionCode"] == DEF_ACTION_UPDATE && $objResult->lnggroupcode == $aryData["lnggroupcode"] ) )
		{
			$aryCheckResult["strgroupdisplaycode_Error"] = 1;
		}

		$objDB->freeResult( $lngResultID );
	}

	// 修正かつ表示フラグがOFFの場合、ユーザー所属チェック実行
	if ( $aryData["lngActionCode"] == DEF_ACTION_UPDATE && !$aryData["bytgroupdisplayflag"] )
	{
		$strQuery = "SELECT * FROM m_GroupRelation " .
	                "WHERE lngGroupCode = " . $aryData["lnggroupcode"];

		list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

		// 結果件数が1以上の場合、エラー
		if ( $lngResultNum > 0 )
		{
			$aryCheckResult["lnggroupcode_Error"] = 1;
			$objDB->freeResult( $lngResultID );
		}
	}
}

// 削除 かつ エラーがない 場合、
// 削除チェック実行
elseif ( $aryData["lngActionCode"] == DEF_ACTION_DELETE && !join ( $aryCheckResult ) )
{
	// チェック対象テーブル名配列を定義
	$aryTableName = Array ( "m_GroupRelation", "m_Order", "m_Receive", "m_Sales", "m_Stock" );

	// チェッククエリ生成
	for ( $i = 0; $i < count ( $aryTableName ); $i++ )
	{
		$aryQuery[] = "SELECT lngGroupCode FROM " . $aryTableName[$i] . " WHERE lngGroupCode = " . $aryData["lnggroupcode"];
	}
	$aryQuery[] = "SELECT lngInChargeGroupCode FROM m_Product WHERE lngInChargeGroupCode = " . $aryData["lnggroupcode"] ." OR lngCustomerGroupCode = " . $aryData["lnggroupcode"];

	$strQuery = join ( " UNION ", $aryQuery );

	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	// 結果が1件でもあった場合、削除不可能とし、エラー出力
	if ( $lngResultNum > 0 )
	{
		$objDB->freeResult( $lngResultID );
		fncOutputError ( 1201, DEF_WARNING, "マスタ管理失敗", TRUE, "", $objDB );
	}

	// 削除対象表示のためのデータを取得
	$strQuery = "SELECT * FROM m_Group WHERE lngGroupCode = " . $aryData["lnggroupcode"];

	$objMaster = new clsMaster();
	$objMaster->setMasterTableData( $strQuery, $objDB );

	$aryKeys = array_keys ( $objMaster->aryData[0] );

	foreach ( $aryKeys as $strKey )
	{
		$aryData[$strKey] = $objMaster->aryData[0][$strKey];
	}

	$objMaster = new clsMaster();
	$aryKeys = Array ();
}

// エラー項目表示処理
list ( $aryData, $bytErrorFlag ) = getArrayErrorVisibility( $aryData, $aryCheckResult, $objDB );


// グループ表示フラグ設定
if ( $aryData["bytgroupdisplayflag"] == "t" )
{
	$aryData["bytgroupdisplayflag"] = "TRUE";
}
else
{
	$aryData["bytgroupdisplayflag"] = "FALSE";
}

//////////////////////////////////////////////////////////////////////////
// 出力
//////////////////////////////////////////////////////////////////////////
$aryParts["lngLanguageCode"] =1;
$aryParts["lngActionCode"]   =& $aryData["lngActionCode"];
$aryParts["strTableName"]    =  "m_Group";
$aryParts["strKeyName"]      =  "lnggroupcode";
$aryParts["lngKeyCode"]      =& $aryData["lnggroupcode"];
$aryParts["strSessionID"]    =& $aryData["strSessionID"];


// lngCompanyCode の(CODE+NAME)取得
$aryCompanyCode = fncGetMasterValue( "m_Company", "lngCompanyCode", "strCompanyName", "Array", "", $objDB );
// bytGroupDisplayFlag の(CODE+NAME)取得
$aryGroupDisplayFlag = Array ( "TRUE" => "表示", "FALSE" => "非表示" );

if ( $aryData["lngActionCode"] != DEF_ACTION_INSERT )
{
	$lngOutputGroupCode =& $aryData["lnggroupcode"];
}
$aryParts["MASTER"] .= "				<tr><td id=\"Column0\" class=\"SegColumn\" width=\"25%\">Column0</td><td class=\"Segs\">" . $lngOutputGroupCode . "</td></tr>\n";
$aryParts["HIDDEN"] .= "<input type=\"hidden\" name=\"lnggroupcode\" value=\"" . $aryData["lnggroupcode"] . "\">\n";

$aryParts["MASTER"] .= "				<tr><td id=\"Column1\" class=\"SegColumn\" width=\"25%\">Column1</td><td class=\"Segs\">" . $aryCompanyCode[$aryData["lngcompanycode"]] . "</td></tr>\n";
$aryParts["HIDDEN"] .= "<input type=\"hidden\" name=\"lngcompanycode\" value=\"" . $aryData["lngcompanycode"] . "\">\n";

$aryParts["MASTER"] .= "				<tr><td id=\"Column2\" class=\"SegColumn\" width=\"25%\">Column1</td><td class=\"Segs\">" . fncHTMLSpecialChars( $aryData["strgroupname"] ) . "</td></tr>\n";
$aryParts["HIDDEN"] .= "<input type=\"hidden\" name=\"strgroupname\" value=\"" . fncHTMLSpecialChars( $aryData["strgroupname"] ) . "\">\n";

$aryParts["MASTER"] .= "				<tr><td id=\"Column3\" class=\"SegColumn\" width=\"25%\">Column1</td><td class=\"Segs\">" . $aryGroupDisplayFlag[$aryData["bytgroupdisplayflag"]] . "</td></tr>\n";
$aryParts["HIDDEN"] .= "<input type=\"hidden\" name=\"bytgroupdisplayflag\" value=\"" . $aryData["bytgroupdisplayflag"] . "\">\n";

$aryParts["MASTER"] .= "				<tr><td id=\"Column4\" class=\"SegColumn\" width=\"25%\">Column1</td><td class=\"Segs\">" . $aryData["strgroupdisplaycode"] . "</td></tr>\n";
$aryParts["HIDDEN"] .= "<input type=\"hidden\" name=\"strgroupdisplaycode\" value=\"" . $aryData["strgroupdisplaycode"] . "\">\n";

$aryParts["MASTER"] .= "				<tr><td id=\"Column5\" class=\"SegColumn\" width=\"25%\">Column1</td><td class=\"Segs\">" . fncHTMLSpecialChars( $aryData["strgroupdisplayname"] ) . "</td></tr>\n";
$aryParts["HIDDEN"] .= "<input type=\"hidden\" name=\"strgroupdisplayname\" value=\"" . fncHTMLSpecialChars( $aryData["strgroupdisplayname"] ) . "\">\n";

$aryParts["MASTER"] .= "				<tr><td id=\"Column6\" class=\"SegColumn\" width=\"25%\">Column1</td><td class=\"Segs\">" . $aryData["strgroupdisplaycolor"] . "</td></tr>\n";
$aryParts["HIDDEN"] .= "<input type=\"hidden\" name=\"strgroupdisplaycolor\" value=\"" . $aryData["strgroupdisplaycolor"] . "\">\n";


if ( $bytErrorFlag )
{
	//echo fncGetReplacedHtml( "m/regist/edit.tmpl", $aryData, $objAuth );
	echo "<meta http-equiv=\"content-type\" content=\"text/html; charset=UTF-8\">";
	echo "<form action=/m/regist/g/edit.php method=GET>";
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


