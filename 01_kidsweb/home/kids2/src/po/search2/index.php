<?php

// ----------------------------------------------------------------------------
/**
*       発注管理  発注書検索画面
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

// 501 発注管理（発注登録）
if ( fncCheckAuthority( DEF_FUNCTION_PO1, $objAuth ) )
{
	$aryData["strRegistURL"]   = "regist/index.php?strSessionID=" . $aryData["strSessionID"];
}

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

// 支払条件
$aryData["lngPayConditionCode"] = fncGetPulldown("m_paycondition", "lngpayconditioncode", "strpayconditionname", 0, '', $objDB);

// 通貨
$aryData["lngMonetaryunitCode"] = fncGetPulldown("m_monetaryunit", "lngmonetaryunitcode", "strmonetaryunitname", 0, '', $objDB);
// 通貨レート
$aryData["lngMonetaryrateCode"] = fncGetPulldown("m_monetaryrate", "lngmonetaryunitcode || '-' || lngmonetaryratecode", "lngmonetaryratecode, curconversionrate", 0, '', $objDB);
// 通貨レート復元用
$TmpAry = explode("\n",$aryData["lngMonetaryrateCode"]);

foreach($TmpAry as $key => $value) {
	if ($value) {
		$ValuePosS = 15;
		$ValuePosE = mb_strpos($value, ">", $ValuePosS) -1;
		$DispPosS = $ValuePosE + 2;
		$DispPosE = mb_strpos($value, "OPTION", $DispPosS) - 2;
		if (array_key_exists('lngMonetaryRateCodeValue', $aryData)) {
			$aryData["lngMonetaryRateCodeValue"] 	= $aryData["lngMonetaryRateCodeValue"] . ",," . substr($value,$ValuePosS,$ValuePosE - $ValuePosS);
			$aryData["curConversionRate"] 	= $aryData["curConversionRate"] . ",," . mb_ereg_replace("</OPTION>","",substr($value,$DispPosS));
		}
		else
		{
			$aryData["lngMonetaryRateCodeValue"] 	= substr($value,$ValuePosS,$ValuePosE - $ValuePosS);
			$aryData["curConversionRate"] 	= mb_ereg_replace("</OPTION>","",substr($value,$DispPosS));
		}
	}
}

//　プルダウンリストの取得に失敗した場合エラー表示
if ( !$aryData["lngMonetaryunitCode"] or !$aryData["lngMonetaryrateCode"] )
{
	fncOutputError ( 9055, DEF_WARNING, "システム管理者にお問い合わせ下さい。", TRUE, "", $objDB );
}

$aryData["lngMonetaryRateCodeValue"]	= "<input type=\"hidden\" name=\"lngMonetaryRateCodeValue\" value=\"" . $aryData["lngMonetaryRateCodeValue"] . "\"></option>";
$aryData["curConversionRate"]	= mb_convert_encoding("<input type=\"hidden\" name=\"curConversionRate\" value=\"" . $aryData["curConversionRate"] . "\"></option>","EUC-JP","ASCII,JIS,UTF-8,EUC-JP,SJIS");

// 文字列チェック
$aryCheck["strSessionID"]          = "null:numenglish(32,32)";
$aryResult = fncAllCheck( $aryData, $aryCheck );
fncPutStringCheckError( $aryResult, $objDB );

// ヘルプ対応
$aryData["lngFunctionCode"] = DEF_FUNCTION_PO2;

// テンプレート読み込み
echo fncGetReplacedHtmlWithBase("search/base_search.html", "po/search2/po_search.tmpl", $aryData ,$objAuth );

$objDB->close();

return true;

?>
