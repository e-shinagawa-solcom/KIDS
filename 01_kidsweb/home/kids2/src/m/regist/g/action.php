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
// 登録、修正実行
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
// 削除実行
// confirm.php -> strSessionID  -> action.php
// confirm.php -> lngActionCode -> action.php
// confirm.php -> lnggroupcode  -> action.php


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
	$aryCheck["bytgroupdisplayflag"]  = "null:english(4,5)";
	$aryCheck["strgroupdisplaycode"]  = "null:numenglish(1,3)";
	$aryCheck["strgroupdisplayname"]  = "null:length(1,100)";
	$aryCheck["strgroupdisplaycolor"] = "null:color";
}


// 文字列チェック
$aryCheckResult = fncAllCheck( $aryData, $aryCheck );
fncPutStringCheckError( $aryCheckResult, $objDB );



//////////////////////////////////////////////////////////////////////////
// 処理の有効性をチェック
//////////////////////////////////////////////////////////////////////////
// ( 登録 または 修正 ) かつ キーにエラーがない 場合、
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
		fncOutputError ( 9052, DEF_WARNING, "マスタ管理失敗", TRUE, "", $objDB );
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
			fncOutputError ( 9052, DEF_WARNING, "マスタ管理失敗", TRUE, "", $objDB );
		}

		$objDB->freeResult( $lngResultID );
	}

	// 修正かつ表示フラグが"FALSE"の場合、ユーザー所属チェック実行
	if ( $aryData["lngActionCode"] == DEF_ACTION_UPDATE && $aryData["bytgroupdisplayflag"] == "FALSE" )
	{
		$strQuery = "SELECT * FROM m_GroupRelation " .
	                "WHERE lngGroupCode = " . $aryData["lnggroupcode"];

		list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

		// 結果件数が1以上の場合、エラー
		if ( $lngResultNum > 0 )
		{
			$objDB->freeResult( $lngResultID );
			fncOutputError ( 9052, DEF_WARNING, "マスタ管理失敗", TRUE, "", $objDB );
		}
	}

	// 登録処理(INSERT)
	if ( $aryData["lngActionCode"] == DEF_ACTION_INSERT )
	{

		//$aryData["lnggroupcode"] = fncGetSequence( "m_Group.lngGroupCode", $objDB );
		$aryQuery[] = "INSERT INTO m_Group VALUES ( " .
                      $aryData["lnggroupcode"] . ", " .
                      $aryData["lngcompanycode"] . ", '" .
                      $aryData["strgroupname"] . "', " .
                      $aryData["bytgroupdisplayflag"] . ", '" .
                      $aryData["strgroupdisplaycode"] . "', '" .
                      $aryData["strgroupdisplayname"] . "', '" .
                      $aryData["strgroupdisplaycolor"] . "'" .
                      " )";
	}

	// 修正処理(UPDATE)
	elseif ( $aryData["lngActionCode"] == DEF_ACTION_UPDATE )
	{
		// ロック
		$aryQuery[] = "SELECT * FROM m_Group WHERE lngGroupCode = " . $aryData["lnggroupcode"] . " FOR UPDATE";

		// UPDATE クエリ
		$aryQuery[] = "UPDATE m_Group SET" .
                      " lngCompanyCode = " . $aryData["lngcompanycode"] . "," .
                      " strGroupName = '" . $aryData["strgroupname"] . "'," .
                      " bytGroupDisplayFlag = " . $aryData["bytgroupdisplayflag"] . "," .
                      " strGroupDisplayCode = '" . $aryData["strgroupdisplaycode"] . "'," .
                      " strGroupDisplayName = '" . $aryData["strgroupdisplayname"] . "'," .
                      " strGroupDisplayColor = '" . $aryData["strgroupdisplaycolor"] . "'" .
                      "WHERE lngGroupCode = " . $aryData["lnggroupcode"];
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
	$aryQuery = Array();

	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	// 結果が1件でもあった場合、削除不可能とし、エラー出力
	if ( $lngResultNum > 0 )
	{
		fncOutputError ( 1201, DEF_WARNING, "マスタ管理失敗", TRUE, "", $objDB );
	}

	// 削除処理(DELETE)
	$aryQuery[] = "DELETE FROM m_Group WHERE lngGroupCode = " . $aryData["lnggroupcode"];
}



////////////////////////////////////////////////////////////////////////////
// クエリ実行
// ////////////////////////////////////////////////////////////////////////////
$objDB->transactionBegin();

for ( $i = 0; $i < count ( $aryQuery ); $i++ )
{
	echo "<p>$aryQuery[$i]</p>\n";
	list ( $lngResultID, $lngResultNum ) = fncQuery( $aryQuery[$i], $objDB );
}

$objDB->transactionCommit();


// $objDB->close();



//////////////////////////////////////////////////////////////////////////
// 出力
//////////////////////////////////////////////////////////////////////////
echo "<script language=javascript>window.returnValue=true;window.open('about:blank','_parent').close();</script>";



return TRUE;
?>


