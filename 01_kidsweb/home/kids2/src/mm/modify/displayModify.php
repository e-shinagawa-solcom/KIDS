<?php

// ----------------------------------------------------------------------------
/**
*       金型履歴管理  修正画面
*
*/
// ----------------------------------------------------------------------------

// 設定の読み込み
include_once ( "conf.inc" );
require ( LIB_FILE );
require_once(SRC_ROOT.'/mold/validation/UtilValidation.class.php');
require_once (SRC_ROOT.'/mold/lib/index/FormMoldHistory.class.php');
require_once (SRC_ROOT.'/mold/lib/UtilBussinesscode.class.php');
require_once (SRC_ROOT.'/mold/lib/UtilMold.class.php');
require_once(SRC_ROOT.'/mold/lib/UtilGroup.class.php');
require_once(SRC_ROOT.'/mold/lib/UtilUser.class.php');
require_once(SRC_ROOT.'/mold/lib/UtilProduct.class.php');
require_once(SRC_ROOT.'/mold/lib/UtilCompany.class.php');

$objDB   = new clsDB();
$objAuth = new clsAuth();
$objDB->open( "", "", "", "" );

setcookie("strSessionID", $_REQUEST["strSessionID"]);

// セッション確認
$objAuth = fncIsSession($_REQUEST["strSessionID"], $objAuth, $objDB );

// 権限確認
// 1800 金型管理
if ( !fncCheckAuthority( DEF_FUNCTION_MM0, $objAuth ) )
{
	fncOutputError ( 9060, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
}

// 1804 金型管理（修正）
if ( !fncCheckAuthority( DEF_FUNCTION_MM4, $objAuth ) )
{
	fncOutputError ( 9060, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
}

// パラメータ取得
$moldNo = $_REQUEST["MoldNo"];
$historyNo = $_REQUEST["HistoryNo"];
$version = $_REQUEST["Version"];

if (!$moldNo || !(0 <= $historyNo) || !(0 <= $version))
{
	// 情報の取得に失敗しました
	fncOutputError(9061, DEF_ERROR, "", TRUE, "", $objDB);
}

// ユーティリティクラスのインスタンス取得
$utilMold = UtilMold::getInstance();
$utilValidation = UtilValidation::getInstance();
$utilBussinesscode = UtilBussinesscode::getInstance();
$utilCompany = UtilCompany::getInstance();
$utilGroup = UtilGroup::getInstance();
$utilUser = UtilUser::getInstance();
$utilProduct = UtilProduct::getInstance();

// 金型履歴の索引
try
{
	// 金型情報
	$record = $utilMold->selectMoldHistory($moldNo, $historyNo, $version);
	$infoMold = $utilMold->selectMold($moldNo);
	$status = $record[TableMoldHistory::Status];

	// 製品コード/名称
	$productCode = $infoMold[TableMold::ProductCode];
	$reviseCode = $infoMold[TableMold::ReviseCode];
// echo "reviseCode:" . $reviseCode . "<br>";
	$productName = $utilProduct->selectProductNameByProductCode($productCode, $reviseCode);

	switch ($status)
	{
		case "10":
		case "20":
			// 保管工場
			$srcFactoryCode = $record[TableMoldHistory::SourceFactory];
			$displaySrcFactoryCode = $utilCompany->selectDisplayCodeByCompanyCode($srcFactoryCode);
			$displaySrcFactoryName = $utilCompany->selectDisplayNameByCompanyCode($srcFactoryCode);
			// 移動先工場
			$dstFactoryCode = $record[TableMoldHistory::DestinationFactory];
			$displayDstFactoryCode = $utilCompany->selectDisplayCodeByCompanyCode($dstFactoryCode);
			$displayDstFactoryName = $utilCompany->selectDisplayNameByCompanyCode($dstFactoryCode);
			break;
	}


}
catch (SQLException $e)
{
	// 情報の取得に失敗しました
	fncOutputError(9061, DEF_ERROR, "不正なデータか対象のデータが変更された可能性があります。", TRUE, "", $objDB);
}

// 置換文字列群の作成
$replacement = $record;
$replacement[TableMoldHistory::ActionDate] = str_replace("-", "/", $record[TableMoldHistory::ActionDate]);
$replacement[FormMoldHistory::ProductCode] = $productCode;
$replacement[FormMoldHistory::ReviseCode] = $reviseCode;
$replacement[FormMoldHistory::ProductName] = $productName;
$replacement[TableMoldHistory::SourceFactory] = $displaySrcFactoryCode;
$replacement[FormMoldHistory::SourceFactoryName] = $displaySrcFactoryName;
$replacement[TableMoldHistory::DestinationFactory] = $displayDstFactoryCode;
$replacement[FormMoldHistory::DestinationFactoryName] = $displayDstFactoryName;
$replacement["DummyStatus"] = $record[TableMoldHistory::Status];
// テンプレート読み込み
echo fncGetReplacedHtmlWithBase("base_mold_noframes.html", "mm/modify/mm_modify.tmpl", $replacement ,$objAuth );
