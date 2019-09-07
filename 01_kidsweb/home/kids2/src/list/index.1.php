<?
/** 
*	帳票出力 帳票選択画面
*
*	@package   KIDS
*	@license   http://www.wiseknot.co.jp/ 
*	@copyright Copyright &copy; 2003, Wiseknot 
*	@author    Kenji Chiba <k-chiba@wiseknot.co.jp> 
*	@access    public
*	@version   1.00
*
*/
// 帳票選択画面
// index.php -> strSessionID    -> index.php

// 検索画面へ( * は指定帳票のファイル名 )
// index.php -> strSessionID    -> *.php

// 設定読み込み
include_once('conf.inc');

// ライブラリ読み込み
require (LIB_FILE);
require (SRC_ROOT . "list/cmn/lib_lo.php");

$objDB   = new clsDB();
$objAuth = new clsAuth();
$objDB->open( "", "", "", "" );

//////////////////////////////////////////////////////////////////////////
// POST(一部GET)データ取得
//////////////////////////////////////////////////////////////////////////
if ( $_POST )
{
	$aryData = $_POST;
}
elseif ( $_GET )
{
	$aryData = $_GET;
}

// 文字列チェック
$aryCheck["strSessionID"]          = "null:numenglish(32,32)";
$aryResult = fncAllCheck( $aryData, $aryCheck );
fncPutStringCheckError( $aryResult, $objDB );

// セッション確認
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

// 権限確認
if ( !fncCheckAuthority( DEF_FUNCTION_LO0, $objAuth ) )
{
	fncOutputError ( 9018, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
}


if ( fncCheckAuthority( DEF_FUNCTION_LO1, $objAuth ) )
{
	// 商品化企画書帳票出力可能
	$aryParts["strManagementMenu"] .= "<a href=search/" . $aryListOutputMenu[DEF_REPORT_PRODUCT]["file"] . ".php?strSessionID=" . $aryData["strSessionID"] . ">" . $aryListOutputMenu[DEF_REPORT_PRODUCT]["name"] . "</a>\n";
}
if ( fncCheckAuthority( DEF_FUNCTION_LO2, $objAuth ) )
{
	// 発注書（P.O）帳票出力可能
	$aryParts["strManagementMenu"] .= "<a href=search/" . $aryListOutputMenu[DEF_REPORT_ORDER]["file"] . ".php?strSessionID=" . $aryData["strSessionID"] . ">" . $aryListOutputMenu[DEF_REPORT_ORDER]["name"] . "</a>\n";
}





$objDB->close();


// ヘルプリンク用機能コードをセット
$aryData["lngFunctionCode"] = DEF_FUNCTION_LO0;



	//-------------------------------------------------------------------------
	// 読み込みページの設定
	//-------------------------------------------------------------------------
	if( !$aryData["strListMode"] )
	{
		// 帳票選択ページ
		$aryData["strListUrl"] = '/list/select.php?strSessionID=' . $aryData["strSessionID"] . '&lngFunctionCode=' . $aryData["lngFunctionCode"] . '&lngApplicantUserCodeVisible=1&lngInputUserCodeVisible=1&dtmStartDateVisible=1&lngInChargeCodeVisible=1&lngWorkflowStatusCodeVisible=1&lngWorkflowStatusCodeConditions=1&lngSelectFunctionCode=500';
	}
	else
	{
		switch( $aryData["strListMode"] )
		{
			// 商品化企画書 検索ページ
			case 'p':
				$aryData["strListUrl"] = '/list/search/p/search.php?strSessionID=' . $aryData["strSessionID"] . '&lngFunctionCode=' . $aryData["lngFunctionCode"] . '&lngApplicantUserCodeVisible=1&lngInputUserCodeVisible=1&dtmStartDateVisible=1&lngInChargeCodeVisible=1&lngWorkflowStatusCodeVisible=1&lngWorkflowStatusCodeConditions=1&lngSelectFunctionCode=500';
				break;

			// 発注書 検索ページ
			case 'po':
				$aryData["strListUrl"] = '/list/search/po/search.php?strSessionID=' . $aryData["strSessionID"] . '&lngFunctionCode=' . $aryData["lngFunctionCode"] . '&lngApplicantUserCodeVisible=1&lngInputUserCodeVisible=1&dtmStartDateVisible=1&lngInChargeCodeVisible=1&lngWorkflowStatusCodeVisible=1&lngWorkflowStatusCodeConditions=1&lngSelectFunctionCode=500';
				break;

			// 見積原価書 検索ページ
			case 'es':
				$aryData["strListUrl"] = '/list/search/estimate/search.php?strSessionID=' . $aryData["strSessionID"] . '&lngFunctionCode=' . $aryData["lngFunctionCode"] . '&lngApplicantUserCodeVisible=1&lngInputUserCodeVisible=1&dtmStartDateVisible=1&lngInChargeCodeVisible=1&lngWorkflowStatusCodeVisible=1&lngWorkflowStatusCodeConditions=1&lngSelectFunctionCode=500';
				break;

			default:
				break;
		}
	}
	//-------------------------------------------------------------------------



// HTML出力
if(!$aryData["strListMode"])
{
	echo fncGetReplacedHtml( "/list/list/parts.html", $aryData, $objAuth );
}
else
{
	// テンプレート読み込み
echo fncGetReplacedHtmlWithBase("search/base_search.html", "/list/search/p/p_search.html", $aryData ,$objAuth );

	// echo fncGetReplacedHtml( "/list/list/parts_button.html", $aryData, $objAuth );
}

?>
