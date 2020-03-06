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
// edit.php -> strSessionID          -> confirm.php
// edit.php -> lngActionCode         -> confirm.php
// edit.php -> strMasterTableName    -> confirm.php
// edit.php -> strKeyName            -> confirm.php
// edit.php -> lngKeyCode            -> confirm.php
// edit.php -> (lngStockSubjectCode) -> confirm.php

// 実行へ
// confirm.php -> strSessionID          -> action.php
// confirm.php -> lngActionCode         -> action.php
// confirm.php -> strMasterTableName    -> action.php
// confirm.php -> strKeyName            -> action.php
// confirm.php -> lngKeyCode            -> action.php
// confirm.php -> (lngStockSubjectCode) -> action.php


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


// 仕入部品の場合に使用するlngStockSubjectCodeの成形
list ( $aryData["lngstocksubjectcode"], $i ) = mb_split ( ":", $aryData["lngstocksubjectcode"] );


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
$aryCheck[$aryData["strKeyName"]] = "null:number(,2147483647)";


// 文字列チェック
$aryCheckResult = fncAllCheck( $aryData, $aryCheck );
fncPutStringCheckError( $aryCheckResult, $objDB );



// マスターオブジェクト生成
$objMaster = new clsMaster();
$objMaster->setMasterTable( $aryData["strMasterTableName"], $aryData["strKeyName"], $aryData[$aryData["strKeyName"]], Array ( "lngstocksubjectcode" => $aryData["lngstocksubjectcode"] ), $objDB );
$objMaster->setAryMasterInfo( $aryData[$aryData["strKeyName"]], $aryData["lngstocksubjectcode"] );




// 入力データの文字列チェック
if ( $aryData["lngActionCode"] != DEF_ACTION_DELETE )
{
	// 全文字列チェック
	$aryCheckResult = fncAllCheck( $aryData, $objMaster->aryCheck );
}
elseif ( $aryData["lngActionCode"] == DEF_ACTION_DELETE )
{
	// 削除の場合、キーコードのみチェック
	$aryCheck[$objMaster->strColumnName[0]] = $objMaster->aryCheck[$objMaster->strColumnName[0]];
	//$aryCheckResult = fncAllCheck( $aryData, $aryCheck );
}


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
		$aryCheckResult[$aryData["strKeyName"] . "_Error"] = 1;
	}
}

// 削除の場合、削除チェック実行
elseif ( $aryData["lngActionCode"] == DEF_ACTION_DELETE )
{
	$count = count ( $objMaster->aryCheckQuery["DELETE"] );
	for ( $i = 0; $i < $count; $i++ )
	{
		$strQuery = $objMaster->aryCheckQuery["DELETE"][$i];

		list ( $lngResultID, $lngResultNum ) = fncQuery ( $strQuery, $objDB );
		if ( $lngResultNum > 0 )
		{
			fncOutputError ( 1201, DEF_WARNING, "マスタ管理失敗", TRUE, "", $objDB );
		}
	}
}


// エラー項目表示処理
list ( $aryData, $bytErrorFlag ) = getArrayErrorVisibility( $aryData, $aryCheckResult, $objDB );


//////////////////////////////////////////////////////////////////////////
// 出力
//////////////////////////////////////////////////////////////////////////
$count = count ( $objMaster->aryColumnName );

if ( $aryData["lngActionCode"] == DEF_ACTION_DELETE )
{
	for ( $i = 0; $i < $count; $i++ )
	{
		$aryData[$objMaster->aryColumnName[$i]] = $objMaster->aryData[0][$objMaster->aryColumnName[$i]];
	}
}

$aryParts["lngLanguageCode"] = 1;
$aryParts["lngActionCode"]   = $aryData["lngActionCode"];
$aryParts["strTableName"]    = $objMaster->strTableName;
$aryParts["strKeyName"]      = $aryData["strKeyName"];
$aryParts["lngKeyCode"]      = $aryData["lngKeyCode"];
$aryParts["strSessionID"]    = $aryData["strSessionID"];


$aryData = fncToHTMLString( $aryData );

// カラム分だけテーブル行生成
for ( $i = 0; $i < $count; $i++ )
{
	// 最初のカラム かつ 新規登録 かつ 仕入科目マスタではない かつ
	// 仕入部品マスタではない かつ 国マスタではない かつ 組織マスタではない
	// 場合、表示しない
	if ( $i == 0 && $aryData["lngActionCode"] == DEF_ACTION_INSERT && $aryData["strMasterTableName"] != "m_StockSubject" && $aryData["strMasterTableName"] != "m_StockItem" && $aryData["strMasterTableName"] != "m_Country" && $aryData["strMasterTableName"] != "m_Organization" )
	{
		$aryMaster[] = "<tr><td id=\"Column$i\" class=\"SegColumn\" width=\"25%\">Column$i</td><td class=\"Segs\"></td></tr>\n";
	}
	else
	{
		$aryMaster[] = "<tr><td id=\"Column$i\" class=\"SegColumn\" width=\"25%\">Column$i</td><td class=\"Segs\">" . $aryData[$objMaster->aryColumnName[$i]] . "</td></tr>\n";
	}
	$aryParts["HIDDEN"] .= "<input type=\"hidden\" name=\"" . $objMaster->aryColumnName[$i] . "\" value=\"" . $aryData[$objMaster->aryColumnName[$i]] . "\">\n";
}

// 仕入科目の場合、「コード＞名称」変換
if ( $objMaster->strTableName == "m_StockSubject" )
{
	$strName = fncGetMasterValue( "m_StockClass", "lngStockClassCode", "strStockClassName", $aryData[$objMaster->aryColumnName[1]], "", $objDB );
	$aryMaster[1] = "<tr><td id=\"Column1\" class=\"SegColumn\" width=\"25%\">Column1</td><td class=\"Segs\">$strName</td></tr>\n";
}

// 仕入部品の場合、「コード＞名称」変換
if ( $objMaster->strTableName == "m_StockItem" )
{
	$strName = fncGetMasterValue( "m_StockSubject", "lngStockSubjectCode", "strStockSubjectName", $aryData[$objMaster->aryColumnName[1]], "", $objDB );
	$aryMaster[1] = "<tr><td id=\"Column1\" class=\"SegColumn\" width=\"25%\">Column1</td><td class=\"Segs\">$strName</td></tr>\n";
}


// 表示マスター配列の結合
$aryParts["MASTER"] = join ( "", $aryMaster );


if ( $bytErrorFlag )
{
	//echo fncGetReplacedHtml( "m/regist/edit.tmpl", $aryData, $objAuth );
	echo "<form action=/m/regist/c/edit.php method=GET>";
	//echo getArrayTable( $aryData, "TABLE" );exit;
	echo getArrayTable( $aryData, "HIDDEN" );
	echo "</form>";
	echo "<script language=javascript>document.forms[0].submit();</script>";
}
else
{
	//echo getArrayTable( $aryData, "TABLE" );exit;
	$objTemplate = new clsTemplate();
	$objTemplate->getTemplate( "m/regist/c/confirm.tmpl" );
	$objTemplate->replace( $aryParts );
	$objTemplate->complete();
	echo $objTemplate->strTemplate;
}


$objDB->close();


return TRUE;
?>


