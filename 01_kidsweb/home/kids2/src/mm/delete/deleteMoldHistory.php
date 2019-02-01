<?php

// ----------------------------------------------------------------------------
/**
 * 金型履歴管理 削除処理
 */
// ----------------------------------------------------------------------------
include('conf.inc');
require(LIB_FILE);
require_once(SRC_ROOT.'/mold/lib/cache/FormCache.class.php');
require_once (SRC_ROOT.'/mold/lib/UtilBussinesscode.class.php');
require_once(SRC_ROOT.'/mold/validation/UtilValidation.class.php');
require_once (SRC_ROOT.'/mold/lib/UtilMold.class.php');
require_once(SRC_ROOT.'/mold/lib/UtilGroup.class.php');
require_once(SRC_ROOT.'/mold/lib/UtilUser.class.php');
require_once(SRC_ROOT.'/mold/lib/UtilProduct.class.php');
require_once(SRC_ROOT.'/mold/lib/UtilCompany.class.php');
require_once (SRC_ROOT.'/mold/lib/index/FormMoldReport.class.php');
require_once(SRC_ROOT.'/mold/lib/index/TableMoldReport.class.php');
require_once(SRC_ROOT.'/mold/lib/index/TableMoldReportDetail.class.php');

$objDB = new clsDB ();
$objAuth = new clsAuth ();
$objDB->open ( "", "", "", "" );

$aryData = $_REQUEST;

// セッション確認
$objAuth = fncIsSession ( $aryData ["strSessionID"], $objAuth, $objDB);

// 1800 金型履歴管理
if ( !fncCheckAuthority( DEF_FUNCTION_MM0, $objAuth ) )
{
	fncOutputError( 9018, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
}

// 1805 金型履歴管理(削除)
if ( !fncCheckAuthority( DEF_FUNCTION_MM5, $objAuth ) )
{
	fncOutputError( 9018, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
}

// パラメータ取得
$moldNo = $_REQUEST["MoldNo"];
$historyNo = $_REQUEST["HistoryNo"];
$version = $_REQUEST["Version"];

if (!$moldNo && !(0 <= $historyNo) && !(0 <= $version))
{
	// 情報の取得に失敗しました
	fncOutputError(9061, DEF_ERROR, "", TRUE, "", $objDB);
}

// ユーティリティのインスタンス取得
$utilMold = UtilMold::getInstance();
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
	$descStatus = $utilBussinesscode->getDescription("金型ステータス", $status);

	// 製品コード/名称
	$productCode = $infoMold[TableMold::ProductCode];
	$productName = $utilProduct->selectProductNameByProductCode($productCode);

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

// 金型履歴レコードの無効化
$affect_count = $utilMold->disableMoldHistory($moldNo, $historyNo, $version);

// 置換文字列群の作成
$replacement = $record;

// テンプレート読み込み
$objTemplate = new clsTemplate ();
$objTemplate->getTemplate ("/mm/delete/mm_finish_delete.html");

// プレースホルダー置換
$objTemplate->replace($replacement);
$objTemplate->complete();

echo $objTemplate->strTemplate;