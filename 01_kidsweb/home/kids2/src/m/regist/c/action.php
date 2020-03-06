<?
/** 
*	マスタ管理 共通マスタ 確認画面
*
*	@package   KIDS
*	@license   http://www.wiseknot.co.jp/ 
*	@copyright Copyright &copy; 2003, Wiseknot 
*	@author    Kenji Chiba <k-chiba@wiseknot.co.jp> 
*	@access    public
*	@version   1.00
*
*/
// confirm.php -> strSessionID       -> action.php
// confirm.php -> lngActionCode      -> action.php
// confirm.php -> strMasterTableName -> action.php
// confirm.php -> strKeyName         -> action.php
// confirm.php -> *(カラム名)        -> action.php


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
	fncOutputError ( 9060, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
}


$aryCheck["strSessionID"]       = "null:numenglish(32,32)";
$aryCheck["strMasterTableName"] = "null:ascii(1,32)";
$aryCheck["strKeyName"]         = "ascii(1,32)";


// 文字列チェック
$aryCheckResult = fncAllCheck( $aryData, $aryCheck );
fncPutStringCheckError( $aryCheckResult, $objDB );



// マスターオブジェクト生成
$objMaster = new clsMaster();
$objMaster->setMasterTable( $aryData["strMasterTableName"], $aryData["strKeyName"], $aryData[$aryData["strKeyName"]], Array ( "lngstocksubjectcode" => $aryData["lngstocksubjectcode"] ), $objDB );
$objMaster->setAryMasterInfo( $aryData[$aryData["strKeyName"]], $aryData["lngstocksubjectcode"] );

//////////////////////////////////////////////////////////////////////////
// 処理の有効性をチェック
//////////////////////////////////////////////////////////////////////////
// ( 登録 または 修正 ) かつ キーにエラーがない 場合、
// 新規登録、修正チェック実行
if ( ( $aryData["lngActionCode"] == DEF_ACTION_INSERT || $aryData["lngActionCode"] == DEF_ACTION_UPDATE ) && !$aryCheckResult[$aryData["strKeyName"] . "_Error"] )
{
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

		// 仕入科目マスタ、仕入部品マスタ、国マスタ以外は
		// シリアルにて新規コード発行
		//if ( $objMaster->strTableName != "m_StockSubject" && $objMaster->strTableName != "m_StockItem" && $objMaster->strTableName != "m_Country" )
		//{
			//$aryValue[0] = fncGetSequence ( $objMaster->strTableName . "." . $objMaster->aryColumnName[0], $objDB );
		//	$aryValue[0] = $objMaster->lngRecordRow + 1;
		//}
		//else
		//{
			$aryValue[0] = $aryData[$objMaster->aryColumnName[0]];
		//}
		// INSERT VALUES 生成
		for ( $i = 1; $i < $count; $i++ )
		{
			// TEXT 型だった場合、クォート付加
			if ( $objMaster->aryType[$i] == "text" || $objMaster->aryType[$i] == "bool" )
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
		for ( $i = 1; $i < $count; $i++ )
		{
			// TEXT 型だった場合、クォート付加
			if ( $objMaster->aryType[$i] == "text" || $objMaster->aryType[$i] == "bool" )
			{
				$aryValue[$i] = $objMaster->aryColumnName[$i] . " = '" . $aryData[$objMaster->aryColumnName[$i]] . "'";
			}
			else
			{
				$aryValue[$i] = $objMaster->aryColumnName[$i] . " = " . $aryData[$objMaster->aryColumnName[$i]];
			}
		}

		// 仕入部品マスタの場合、キーが2つあるための条件を追加する
		if ( $objMaster->strTableName == "m_StockItem" )
		{
			$where = " AND lngStockSubjectCode = " . $aryData["lngstocksubjectcode"];
		}

		// 対象マスタロック
		$aryQuery[] = "SELECT * FROM " . $objMaster->strTableName . " WHERE " . $aryData["strKeyName"] . " = " . $aryData[$aryData["strKeyName"]] . $where . " FOR UPDATE";

		// 対象マスタUPDATEクエリ
		$aryQuery[] = "UPDATE " . $objMaster->strTableName . " SET " . join ( ", ", $aryValue ) . " WHERE " . $aryData["strKeyName"] . " = " . $aryData[$aryData["strKeyName"]] . $where;
	}
}

// 削除の場合、削除チェック実行
elseif ( $aryData["lngActionCode"] == DEF_ACTION_DELETE )
{
	$count = count ( $objMaster->aryCheckQuery["DELETE"] );
	for ( $i = 0; $i < $count; $i++ )
	{
		list ( $lngResultID, $lngResultNum ) = fncQuery ( $objMaster->aryCheckQuery["DELETE"][$i], $objDB );
		if ( $lngResultNum > 0 )
		{
			fncOutputError ( 1201, DEF_WARNING, "マスタ管理失敗", TRUE, "", $objDB );
		}
	}

	// 仕入部品マスタの場合、キーが2つあるための条件を追加する
	if ( $objMaster->strTableName == "m_StockItem" )
	{
		$where = " AND lngStockSubjectCode = " . $aryData["lngstocksubjectcode"];
	}

	$aryQuery[] = "DELETE FROM " . $objMaster->strTableName . " WHERE " . $aryData["strKeyName"] . " = " . $aryData[$aryData["strKeyName"]] . $where;
}


////////////////////////////////////////////////////////////////////////////
// クエリ実行
////////////////////////////////////////////////////////////////////////////
$objDB->transactionBegin();

$count = count ( $aryQuery );
for ( $i = 0; $i < $count; $i++ )
{
	list ( $lngResultID, $lngResultNum ) = fncQuery ( $aryQuery[$i], $objDB );
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
	//echo "<form name=form1><input type=hidden name=strSessionID value=" . $aryData["strSessionID"] . "></form>";
	echo "<script language=javascript>window.returnValue=true;window.open('about:blank','_parent').close();</script>";
}


$objDB->close();


return TRUE;
?>


