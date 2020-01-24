<?
/** 
*	マスター管理 一覧マスタ選択画面
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

// 設定読み込み
include_once('conf.inc');

// ライブラリ読み込み
require (LIB_FILE);
require (SRC_ROOT . "m/cmn/lib_m.php");

$objDB   = new clsDB();
$objAuth = new clsAuth();
$objDB->open( "", "", "", "" );

$aryData["strSessionID"]    = $_GET["strSessionID"];

// 文字列チェック
$aryCheck["strSessionID"]          = "null:numenglish(32,32)";
$aryResult = fncAllCheck( $aryData, $aryCheck );
fncPutStringCheckError( $aryResult, $objDB );

// セッション確認
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

// 権限確認
if ( !fncCheckAuthority( DEF_FUNCTION_M0, $objAuth ) )
{
	fncOutputError ( 9052, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
}

$objDB->close();


$aryMasterTableKeyName = array_keys ( $aryListTableName );
foreach ( $aryMasterTableKeyName as $key)
{
	$aryData["strMasterTableName"] .= "
				<tr class=\"Segs\">
					<td align=\"center\"><a href=\"javascript:fncRequestCommonMasterEdit( '$key' , window.LIST );\"><img onmouseover=\"DetailOn(this);\" onmouseout=\"DetailOff(this);\" src=\"/img/type01/wf/result/detail_off_bt.gif\" width=\"15\" height=\"15\" border=\"0\" alt=\"選択\"></a></td>
					<td id=\"$key\">$aryListTableName[$key]</td>
					<td></td>
				</tr>
";
}

// テンプレート読み込み
$objTemplate = new clsTemplate();
$objTemplate->getTemplate( "m/list/select.tmpl" );

// テンプレート生成
$objTemplate->replace( $aryData );
$objTemplate->complete();

// HTML出力
echo $objTemplate->strTemplate;
?>
