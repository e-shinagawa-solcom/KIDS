<?
/** 
*	マスタ管理 グループマスタ データ入力画面
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
// index.php -> lnggroupcode        -> edit.php
//
// 確認画面へ
// edit.php -> strSessionID         -> confirm.php
// edit.php -> lngActionCode        -> confirm.php
// edit.php -> lnggroupcode         -> confirm.php
// edit.php -> lngcompanycode       -> confirm.php
// edit.php -> strgroupname         -> confirm.php
// edit.php -> bytgroupdisplayflag  -> confirm.php
// edit.php -> strgroupdisplaycode  -> confirm.php
// edit.php -> strgroupdisplayname  -> confirm.php
// edit.php -> strgroupdisplaycolor -> confirm.php


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
$objMaster->setMasterTable( "m_Group", "lnggroupcode", $aryData["lnggroupcode"], "", $objDB );
$objMaster->setAryMasterInfo( $aryData["lnggroupcode"], "" );



// カラム数取得
$lngColumnNum = count ( $objMaster->aryColumnName );

//////////////////////////////////////////////////////////////////////////
// 入力欄表示処理
//////////////////////////////////////////////////////////////////////////
// 新規の場合、キー入力項目生成
if ( $aryData["lngActionCode"] == DEF_ACTION_INSERT )
{
	// データ初期化
	$objMaster->aryData[0] = Array ();

	if ( !$aryData[$objMaster->aryColumnName[0]] )
	{
		$aryData[$objMaster->aryColumnName[0]] = $objMaster->lngRecordRow;
	}
	//$seq = fncIsSequence( $objMaster->strTableName . "." . $objMaster->aryColumnName[0], $objDB );

	// インクリメント後のシーケンスが9999だった場合さらに1足した数字を取得
	//if ( ( $seq + 1 ) == 9999 )
	//{
	//	$seq = fncGetSequence( $objMaster->strTableName . "." . $objMaster->aryColumnName[0], $objDB );
	//}

	// グループコードのセット
	$objMaster->aryData[0][$objMaster->aryColumnName[0]] =& $aryData[$objMaster->aryColumnName[0]];

	// 会社コードのセット
	$objMaster->aryData[0][$objMaster->aryColumnName[1]] =& $aryData[$objMaster->aryColumnName[1]];

	// グループ名
	$objMaster->aryData[0][$objMaster->aryColumnName[2]] =& $aryData[$objMaster->aryColumnName[2]];

	// 表示グループフラグ
	$objMaster->aryData[0][$objMaster->aryColumnName[3]] =& $aryData[$objMaster->aryColumnName[3]];

	// 表示グループコード
	$objMaster->aryData[0][$objMaster->aryColumnName[4]] =& $aryData[$objMaster->aryColumnName[4]];

	// 表示グループ名称
	$objMaster->aryData[0][$objMaster->aryColumnName[5]] =& $aryData[$objMaster->aryColumnName[5]];

	// グループ表示色
	$objMaster->aryData[0][$objMaster->aryColumnName[6]] =& $aryData[$objMaster->aryColumnName[6]];
}

// グループ
if ( $aryData["lngActionCode"] == DEF_ACTION_UPDATE )
{
	$lngOutputGroupCode =& $objMaster->aryData[0][$objMaster->aryColumnName[0]];
}
$aryParts["MASTER"][0]  = "<span class=\"InputSegs\"><input type=\"text\" id=\"Input0\" value=\"" . $lngOutputGroupCode . "\" disabled></span>\n";

$aryData["HIDDEN"]  = "<input type=\"hidden\" name=\"" . $objMaster->aryColumnName[0] . "\" value=\"" . $objMaster->aryData[0][$objMaster->aryColumnName[0]] . "\">\n";

// 修正の場合、スモークをかける
if ( $aryData["lngActionCode"] == DEF_ACTION_UPDATE )
{
	$strQuery = "SELECT * FROM m_GroupRelation WHERE lngGroupCode = " . $aryData["lnggroupcode"];

	// 会社名(修正時)
	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
	if ( $lngResultNum > 0 )
	{
		$objDB->freeResult( $lngResultID );

		// 会社名(すでに属者がいる場合、スモークを掛ける)
		$aryParts["MASTER"][1]  = "<span class=\"InputSegs\"><select id=\"Input1\" disabled>\n";
		$aryParts["MASTER"][1] .= fncGetPulldown( "m_Company", "lngCompanyCode", "strCompanyName",  $objMaster->aryData[0][$objMaster->aryColumnName[1]], "WHERE bytCompanyDisplayFlag = TRUE", $objDB );
		$aryParts["MASTER"][1] .= "</select></span>\n";

		$aryData["HIDDEN"] .= "<input type=\"hidden\" name=\"" . $objMaster->aryColumnName[1] . "\" value=\"" . $objMaster->aryData[0][$objMaster->aryColumnName[1]] . "\">\n";

		$flag = 1;
	}
}

// 会社名入力欄を表示していない場合、表示
if ( !$flag )
{
	// 会社名
	$aryParts["MASTER"][1]  = "<span class=\"InputSegs\"><select id=\"Input1\" name=\"" . $objMaster->aryColumnName[1] . "\">\n";
	$aryParts["MASTER"][1] .= fncGetPulldown( "m_Company", "lngCompanyCode", "strCompanyName",  $objMaster->aryData[0][$objMaster->aryColumnName[1]], "WHERE bytCompanyDisplayFlag = TRUE", $objDB );
	$aryParts["MASTER"][1] .= "</select></span>\n";
}
$flag = "";

// グループ名
$aryParts["MASTER"][2] = "<span class=\"InputSegs\"><input id=\"Input2\" type=\"text\" name=\"" . $objMaster->aryColumnName[2] . "\" value=\"" . fncHTMLSpecialChars( $objMaster->aryData[0][$objMaster->aryColumnName[2]] ) . "\" onfocus=\"chColorOn(this);\" onblur=\"chColorOff(this);\" maxlength=\"100\"></span>\n";

// 表示グループフラグ
if ( $objMaster->aryData[0][$objMaster->aryColumnName[3]] == "t" || $aryData["lngActionCode"] == DEF_ACTION_INSERT )
{
	$flag = " checked";
}
$aryParts["MASTER"][3] = "<span class=\"InputSegs\"><input id=\"Input3\" type=\"checkbox\" name=\"" . $objMaster->aryColumnName[3] . "\" value=\"t\"$flag></span>\n";
// 表示グループコード
$aryParts["MASTER"][4] = "<span class=\"InputSegs\"><input id=\"Input4\" type=\"text\" name=\"" . $objMaster->aryColumnName[4] . "\" value=\"" . $objMaster->aryData[0][$objMaster->aryColumnName[4]] . "\" onfocus=\"chColorOn(this);\" onblur=\"chColorOff(this);\" maxlength=\"3\"></span>\n";

// 表示グループ名称
$aryParts["MASTER"][5] = "<span class=\"InputSegs\"><input id=\"Input5\" type=\"text\" name=\"" . $objMaster->aryColumnName[5] . "\" value=\"" . fncHTMLSpecialChars( $objMaster->aryData[0][$objMaster->aryColumnName[5]] ) . "\" onfocus=\"chColorOn(this);\" onblur=\"chColorOff(this);\" maxlength=\"100\"></span>\n";

// グループ表示色
$aryParts["MASTER"][6] = "<span class=\"InputSegs\"><input id=\"Input6\" type=\"text\" name=\"" . $objMaster->aryColumnName[6] . "\" value=\"" . $objMaster->aryData[0][$objMaster->aryColumnName[6]] . "\" onfocus=\"chColorOn(this);\" onblur=\"chColorOff(this);\" maxlength=\"7\"></span>\n";




//////////////////////////////////////////////////////////////////////////
// 結果取得、出力処理
//////////////////////////////////////////////////////////////////////////
//echo fncGetReplacedHtml( "m/regist/parts.tmpl", $aryData, $objAuth );
$count = count ( $aryParts["MASTER"] );
for ( $i = 0; $i < $count; $i++ )
{
	$aryData["MASTER"] .= $aryParts["MASTER"][$i];

	// カラム表示
	$aryData["COLUMN"] .= "<span id=\"Column$i\" class=\"ColumnSegs\"></span>\n";
}

$objDB->close();


$aryData["lngLanguageCode"] = $_COOKIE["lngLanguageCode"];
$aryData["strTableName"]    = $objMaster->strTableName;


// HTML出力
$objTemplate = new clsTemplate();
$objTemplate->getTemplate( "m/regist/c/edit.tmpl" );
$objTemplate->replace( $aryData );
$objTemplate->complete();
echo $objTemplate->strTemplate;


return TRUE;
?>
