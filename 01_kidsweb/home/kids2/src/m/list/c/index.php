<?
/** 
*	マスタ管理 共通マスタ マスターテーブル結果一覧画面
*
*	@package   KIDS
*	@license   http://www.wiseknot.co.jp/ 
*	@copyright Copyright &copy; 2003, Wiseknot 
*	@author    Kenji Chiba <k-chiba@wiseknot.co.jp> 
*	@access    public
*	@version   1.00
*
*/
// index.php -> strSessionID    -> index.php
//
// 登録画面
// index.php -> strSessionID          -> edit.php
// index.php -> lngActionCode         -> edit.php
// index.php -> strMasterTableName    -> edit.php
// index.php -> strKeyName            -> edit.php
//
// 修正画面
// index.php -> strSessionID          -> edit.php
// index.php -> lngActionCode         -> edit.php
// index.php -> strMasterTableName    -> edit.php
// index.php -> strKeyName            -> edit.php
// index.php -> lngKeyCode            -> edit.php
// index.php -> (lngStockSubjectCode) -> edit.php
//
// 削除画面
// index.php -> strSessionID          -> confirm.php
// index.php -> lngActionCode         -> confirm.php
// index.php -> strMasterTableName    -> confirm.php
// index.php -> strKeyName            -> confirm.php
// index.php -> lngKeyCode            -> confirm.php
// index.php -> (lngStockSubjectCode) -> confirm.php


// 設定読み込み
include_once('conf.inc');

// ライブラリ読み込み
require (LIB_FILE);
require (SRC_ROOT . "m/cmn/lib_m.php");



$objDB   = new clsDB();
$objAuth = new clsAuth();
$objDB->open( "", "", "", "" );

$aryData = $_POST;

// 文字列チェック
$aryCheck["strSessionID"]       = "null:numenglish(32,32)";
$aryCheck["strMasterTableName"] = "null:ascii(1,32)";
$aryResult = fncAllCheck( $aryData, $aryCheck );
fncPutStringCheckError( $aryResult, $objDB );

// セッション確認
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

// 権限確認
if ( !fncCheckAuthority( DEF_FUNCTION_M0, $objAuth ) )
{
	fncOutputError ( 9052, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
}

// マスターオブジェクト生成
$objMaster = new clsMaster();
$objMaster->setMasterTable( $aryData["strMasterTableName"], "", "", $aryData, $objDB );


///////////////////////////////////////////////////////////////////
// 仕入関連マスタ特殊処理
///////////////////////////////////////////////////////////////////
// 仕入科目マスタの場合、コード＋名称のカラムを表示する特殊処理
if ( $objMaster->strTableName == "m_StockSubject" )
{
	// 仕入区分マスタからマスタデータを取得し、code をキーとする連想配列に代入
	$aryMaster = fncGetMasterValue( "m_StockClass", "lngStockClassCode", "strStockClassName", "Array", "", $objDB );

	$count = count ( $objMaster->aryData );
	for ( $i = 0; $i < $count; $i++ )
	{
		$objMaster->aryData[$i]["lngstockclasscode"] = $objMaster->aryData[$i]["lngstockclasscode"] . ":" . $aryMaster[$objMaster->aryData[$i]["lngstockclasscode"]];
	}
}

// 仕入部品マスタの場合、コード＋名称のカラムを表示する特殊処理
elseif ( $objMaster->strTableName == "m_StockItem" )
{
	// 仕入科目マスタからマスタデータを取得し、code をキーとする連想配列に代入
	$aryMaster = fncGetMasterValue( "m_StockSubject", "lngStockSubjectCode", "strStockSubjectName", "Array", "", $objDB );

	$count = count ( $objMaster->aryData );
	for ( $i = 0; $i < $count; $i++ )
	{
		$objMaster->aryData[$i]["lngstocksubjectcode"] = $objMaster->aryData[$i]["lngstocksubjectcode"] . ":" . $aryMaster[$objMaster->aryData[$i]["lngstocksubjectcode"]];
	}
}



///////////////////////////////////////////////////////////////////
// テーブル生成
///////////////////////////////////////////////////////////////////
// フィールド名表示
$aryData["lngColumnNum"] = 0;
foreach ( $objMaster->aryColumnName as $strColumnName )
{
	$aryData["strColumnHtml"] .= "		<td id=\"Column$aryData[lngColumnNum]\" nowrap onmouseover=\"SortOn( this );\" onmouseout=\"SortOff( this );\" onclick=\"location.href='#';\">$strColumnName</td>\n";
	$aryData["lngColumnNum"]++;
}
$aryData["lngColumnNum"]++;


// 結果行表示
$count = 0;
foreach ( $objMaster->aryData as $record )
{
	// 最初のカラムをキーとする
	$lngKeyCode = $record[$objMaster->aryColumnName[0]];

	$aryData["strResultHtml"] .= "	<tr id=\"Mrecord$count\" class=\"Segs\" onclick=\"fncSelectTrColor( this );\" style=\"background:#ffffff;\">\n";

	// カラム生成
	foreach ( $record as $colmun )
	{
		$aryData["strResultHtml"] .= "		<td nowrap>" . fncHTMLSpecialChars( $colmun ) . "</td>\n";
	}

	// GETで渡す文字列生成
	$getUrl = "strSessionID=".$aryData["strSessionID"]. "&strMasterTableName=" .$aryData["strMasterTableName"]."&strKeyName=" .  $objMaster->aryColumnName[0] ."&" .  $objMaster->aryColumnName[0] ."=" . $lngKeyCode;

	// 仕入部品マスタの場合、2つ目のカラムもキーとする
	if ( $objMaster->strTableName == "m_StockItem" )
	{
		$getUrl .= "&" .  $objMaster->aryColumnName[1] ."=" .  $record[$objMaster->aryColumnName[1]];
	}


	// 修正ボタン生成
	$aryData["strResultHtml"] .= "		<td bgcolor=\"#ffffff\" nowrap><a href=\"javascript:fncShowDialogCommonMaster('/m/regist/c/edit.php?lngActionCode=" . DEF_ACTION_UPDATE . "&$getUrl' , window.form1 , 'ResultIframeCommonMaster' , 'NO' , $_COOKIE[lngLanguageCode] , 'fix' );\"><img onmouseover=\"RenewOn(this);\" onmouseout=\"RenewOff(this);\" src=\"/img/type01/cmn/seg/renew_off_bt.gif\" width=\"15\" height=\"15\" border=\"0\" alt=\"RENEW\"></a></td>\n";

	// 削除ボタン生成
	$aryData["strResultHtml"] .= "		<td bgcolor=\"#ffffff\" nowrap><a href=\"javascript:fncShowDialogCommonMaster('/m/regist/c/confirm.php?lngActionCode=" . DEF_ACTION_DELETE . "&$getUrl' , window.form1 , 'ResultIframeCommonMaster' , 'NO' , $_COOKIE[lngLanguageCode] , 'delete' );\"><img onmouseover=\"RemoveOn(this);\" onmouseout=\"RemoveOff(this);\" src=\"/img/type01/cmn/seg/remove_off_bt.gif\" width=\"15\" height=\"15\" border=\"0\" alt=\"REMOVE\"></a></td>\n";

	$aryData["strResultHtml"] .= "	</tr>\n";

}



$objDB->close();



// 登録ボタンのGET文字列生成
$aryData["strInsertForm"] = "/m/regist/c/edit.php?strSessionID=". $aryData["strSessionID"] . "&lngActionCode=" . DEF_ACTION_INSERT . "&strMasterTableName=" . $aryData["strMasterTableName"] ."&strKeyName=" .  $objMaster->aryColumnName[0];

$aryData["strTableName"] =& $objMaster->strTableName;
$aryData["lngLanguageCode"] =& $_COOKIE["lngLanguageCode"];

// HTML出力
$objTemplate = new clsTemplate();
$objTemplate->getTemplate( "m/list/c/parts.tmpl" );
$objTemplate->replace( $aryData );
$objTemplate->complete();
echo $objTemplate->strTemplate;



return TRUE;
?>
