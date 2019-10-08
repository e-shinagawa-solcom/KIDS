<?php

// ----------------------------------------------------------------------------
/**
*       発注管理  検索画面
*
*
*       @package    K.I.D.S.
*       @license    http://www.kuwagata.co.jp/
*       @copyright  KUWAGATA CO., LTD.
*       @author     K.I.D.S. Groups <info@kids-groups.com>
*       @access     public
*       @version    2.00
*
*
*       処理概要
*         ・検索画面表示処理
*
*       更新履歴
*
*/
// ----------------------------------------------------------------------------



// 設定の読み込み
include_once ( "conf.inc" );

// ライブラリ読み込み
require ( LIB_FILE );
require(SRC_ROOT."po/cmn/lib_po.php");

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

setcookie("strSessionID", $aryData["strSessionID"], 0, "/");

// 文字列チェック
$aryCheck["strSessionID"]   = "null:numenglish(32,32)";
$aryResult = fncAllCheck( $aryData, $aryCheck );
fncPutStringCheckError( $aryResult, $objDB );

// セッション確認
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

// 権限チェック
// 500	発注管理
if ( !fncCheckAuthority( DEF_FUNCTION_PO0, $objAuth ) )
{
    fncOutputError ( 9052, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
}

// // 501 発注管理（発注登録）
// if ( fncCheckAuthority( DEF_FUNCTION_PO1, $objAuth ) )
// {
// 	$aryData["strRegistURL"]   = "regist/index.php?strSessionID=" . $aryData["strSessionID"];
// }

// 502 発注管理（発注検索）
if ( fncCheckAuthority( DEF_FUNCTION_PO2, $objAuth ) )
{
	$aryData["strSearchURL"]   = "search/index.php?strSessionID=" . $aryData["strSessionID"];
}

// 503 発注管理（発注検索　管理モード）
if ( fncCheckAuthority( DEF_FUNCTION_PO3, $objAuth ) )
{
	$aryData["AdminSet_visibility"] = "visible";
	// 507 発注管理（無効化）
	if ( fncCheckAuthority( DEF_FUNCTION_PO7, $objAuth ) )
	{
		$aryData["btnInvalid_visibility"] = "visible";
		$aryData["btnInvalidVisible"] = "disabled";
	}
	else
	{
		$aryData["btnInvalid_visibility"] = "hidden";
		$aryData["btnInvalidVisible"] = "disabled";
	}
}
else
{
	$aryData["AdminSet_visibility"] = "hidden";
	$aryData["btnInvalid_visibility"] = "hidden";
	$aryData["btnInvalidVisible"] = "";
}
// 504 発注管理（詳細表示）
if ( fncCheckAuthority( DEF_FUNCTION_PO4, $objAuth ) )
{
	$aryData["btnDetail_visibility"] = "visible";
	$aryData["btnDetailVisible"] = "checked";
}
else
{
	$aryData["btnDetail_visibility"] = "hidden";
	$aryData["btnDetailVisible"] = "";
}

// 管理者モード
if($objAuth->AuthorityGroupCode <= 3){
	$aryData["displayMode"] = "inline";
} else {
	$aryData["displayMode"] = "none";
}

// 仕入科目
$aryData["lngStockSubjectCode"]		= fncGetPulldown( "m_stocksubject", "lngstocksubjectcode", "lngstocksubjectcode,	strstocksubjectname", 1, '', $objDB );
// 仕入部品
$aryData["lngStockItemCode"] 		= fncGetPulldown( "m_stockitem", "lngstocksubjectcode || '-' || lngstockitemcode", "lngstockitemcode, 	strstockitemname", 0, '', $objDB );
// 仕入状態
$aryData["lngOrderStatusCode"] = fncGetCheckBoxObject("m_orderstatus", "lngorderstatuscode", "strorderstatusname", "lngOrderStatusCode[]", 'where lngOrderStatusCode not in (1)', $objDB);

// 仕入部品復元用
$TmpAry = explode("\n",$aryData["lngStockItemCode"]);

foreach($TmpAry as $key => $value) {
	if ($value) {
		$ValuePosS = 15;
		$ValuePosE = mb_strpos($value, ">", $ValuePosS) -1;
		$DispPosS = $ValuePosE + 2;
		$DispPosE = mb_strpos($value, "OPTION", $DispPosS) - 2;
		if (array_key_exists('lngStockItemCodeValue', $aryData)) {
			$aryData["lngStockItemCodeValue"] 	= $aryData["lngStockItemCodeValue"] . ",," . substr($value,$ValuePosS,$ValuePosE - $ValuePosS);
			$aryData["lngStockItemCodeDisp"] 	= $aryData["lngStockItemCodeDisp"] . ",," . mb_ereg_replace("</OPTION>","",substr($value,$DispPosS));
		}
		else
		{
			$aryData["lngStockItemCodeValue"] 	= substr($value,$ValuePosS,$ValuePosE - $ValuePosS);
			$aryData["lngStockItemCodeDisp"] 	= mb_ereg_replace("</OPTION>","",substr($value,$DispPosS));
		}
	}
}

//　プルダウンリストの取得に失敗した場合エラー表示
if ( !$aryData["lngStockSubjectCode"] or !$aryData["lngStockItemCode"] )
{
	fncOutputError ( 9055, DEF_WARNING, "システム管理者にお問い合わせ下さい。", TRUE, "", $objDB );
}

$aryData["lngStockItemCodeValue"]	= "<input type=\"hidden\" name=\"lngStockItemCodeValue\" value=\"" . $aryData["lngStockItemCodeValue"] . "\"></option>";
$aryData["lngStockItemCodeDisp"]	= mb_convert_encoding("<input type=\"hidden\" name=\"lngStockItemCodeDisp\" value=\"" . $aryData["lngStockItemCodeDisp"] . "\"></option>","EUC-JP","ASCII,JIS,UTF-8,EUC-JP,SJIS");

// 文字列チェック
$aryCheck["strSessionID"]          = "null:numenglish(32,32)";
$aryResult = fncAllCheck( $aryData, $aryCheck );
fncPutStringCheckError( $aryResult, $objDB );

// ヘルプ対応
$aryData["lngFunctionCode"] = DEF_FUNCTION_PO2;

// テンプレート読み込み
echo fncGetReplacedHtmlWithBase("search/base_search.html", "po/search/po_search.tmpl", $aryData ,$objAuth );

$objDB->close();

return true;

?>
