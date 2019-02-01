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
$aryCheck["lngmonetaryratecode"] = "null:number(1,2147483647)";
$aryCheck["lngmonetaryunitcode"] = "null:number(1,2147483647)";
$aryCheck["curconversionrate"]   = "null:number(0.000001,9999999999.999999)";
$aryCheck["dtmapplystartdate"]   = "null:date(/)";
$aryCheck["dtmapplyenddate"]     = "null:date(/)";


// 文字列チェック
$aryCheckResult = fncAllCheck( $aryData, $aryCheck );
fncPutStringCheckError( $aryCheckResult, $objDB );


// マスターオブジェクト生成
$objMaster = new clsMaster();
$objMaster->setMasterTable( "m_MonetaryRate", "lngmonetaryratecode", $aryData["lngmonetaryratecode"], Array ( "lngmonetaryunitcode" => $aryData["lngmonetaryunitcode"], "dtmapplystartdate" => $aryData["dtmapplystartdate"] ), $objDB );
$objMaster->setAryMasterInfo( $aryData["lngmonetaryratecode"], $aryData["lngmonetaryunitcode"] );



//////////////////////////////////////////////////////////////////////////
// 処理の有効性をチェック
//////////////////////////////////////////////////////////////////////////
// ( 登録 または 修正 ) かつ キーにエラーがない 場合、
// 新規登録、修正チェック実行
if ( ( $aryData["lngActionCode"] == DEF_ACTION_INSERT || $aryData["lngActionCode"] == DEF_ACTION_UPDATE ) && !join ( $aryCheckResult ) )
{
	// チェッククエリ設定
	// AND NOT ( 終了年月日 < 入力開始年月日 OR 開始年月日 > 入力終了年月日 )
	// 条件追加
	$objMaster->aryCheckQuery["INSERT"] .= " AND NOT ( " . $objMaster->aryColumnName[4] . " < '" . $aryData[$objMaster->aryColumnName[3]] . "' OR " . $objMaster->aryColumnName[3] . " > '" . $aryData[$objMaster->aryColumnName[4]] . "' )";

	 list ( $lngResultID, $lngResultNum ) = fncQuery ( $objMaster->aryCheckQuery["INSERT"], $objDB );

	// 新規登録 かつ 結果件数が0以上
	// または
	// 修正 かつ 結果件数が1以外 の場合、エラー
	if ( ( $aryData["lngActionCode"] == DEF_ACTION_INSERT && $lngResultNum > 0 ) || ( $aryData["lngActionCode"] == DEF_ACTION_UPDATE && $lngResultNum != 1 ) )
	{
		fncOutputError ( 9052, DEF_WARNING, "マスタ管理失敗", TRUE, "", $objDB );
	}

	// 登録処理(INSERT)
	elseif ( $aryData["lngActionCode"] == DEF_ACTION_INSERT )
	{
		$count = count ( $objMaster->aryColumnName );

		// INSERT VALUES 生成
		for ( $i = 0; $i < $count; $i++ )
		{
			// TEXT 型だった場合、クォート付加
			if ( $objMaster->aryType[$i] == "text" || $objMaster->aryType[$i] == "date" )
			{
				$aryValue[$i] = "'" . $aryData[$objMaster->aryColumnName[$i]] . "'";
			}
			else
			{
				$aryValue[$i] = $aryData[$objMaster->aryColumnName[$i]];
			}
		}

		$aryQuery[] = "INSERT INTO " . $objMaster->strTableName . " VALUES ( " . join ( ", ", $aryValue ) . ")";
	}

	// 修正処理(UPDATE)
	elseif ( $aryData["lngActionCode"] == DEF_ACTION_UPDATE )
	{
		$count = count ( $objMaster->aryColumnName );

		// UPDATE VALUES 生成
		for ( $i = 2; $i < $count; $i++ )
		{
			// TEXT 型だった場合、クォート付加
			if ( $objMaster->aryType[$i] == "text" || $objMaster->aryType[$i] == "date" )
			{
				$aryValue[$i] = $objMaster->aryColumnName[$i] . " = '" . $aryData[$objMaster->aryColumnName[$i]] . "'";
			}
			else
			{
				$aryValue[$i] = $objMaster->aryColumnName[$i] . " = " . $aryData[$objMaster->aryColumnName[$i]];
			}
		}

		// ロック
		$aryQuery[] = "SELECT * FROM " . $objMaster->strTableName . " WHERE " . $objMaster->aryColumnName[0] . " = " . $aryData[$objMaster->aryColumnName[0]] . " AND " . $objMaster->aryColumnName[1] . " = " . $aryData[$objMaster->aryColumnName[1]] . " AND " . $objMaster->aryColumnName[3] . " = '" . $aryData[$objMaster->aryColumnName[3]] . "' FOR UPDATE";

		// UPDATE クエリ
		$aryQuery[] = "UPDATE " . $objMaster->strTableName . " SET " . join ( ", ", $aryValue ) . " WHERE " . $objMaster->aryColumnName[0] . " = " . $aryData[$objMaster->aryColumnName[0]] . " AND " . $objMaster->aryColumnName[1] . " = " . $aryData[$objMaster->aryColumnName[1]] . " AND " . $objMaster->aryColumnName[3] . " = '" . $aryData[$objMaster->aryColumnName[3]] . "'";
	}
}



////////////////////////////////////////////////////////////////////////////
// クエリ実行
////////////////////////////////////////////////////////////////////////////
$objDB->transactionBegin();

for ( $i = 0; $i < count ( $aryQuery ); $i++ )
{
	echo "<p>$aryQuery[$i]</p>\n";
	list ( $lngResultID, $lngResultNum ) = fncQuery( $aryQuery[$i], $objDB );
}

$objDB->transactionCommit();


// エラー項目表示処理
list ( $aryData, $bytErrorFlag ) = getArrayErrorVisibility( $aryData, $aryCheckResult, $objDB );


//////////////////////////////////////////////////////////////////////////
// 出力
//////////////////////////////////////////////////////////////////////////
if ( $bytErrorFlag )
{
	fncOutputError ( 9052, DEF_WARNING, "マスタ管理失敗", TRUE, "", $objDB );
}
else
{
	echo "<script language=javascript>window.returnValue=true;window.close();</script>";
}


$objDB->close();


return TRUE;
?>


