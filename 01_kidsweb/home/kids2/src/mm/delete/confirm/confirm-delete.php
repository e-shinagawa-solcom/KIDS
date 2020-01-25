<?php

// ----------------------------------------------------------------------------
/**
 * 金型履歴管理 削除画面表示
 */
// ----------------------------------------------------------------------------
include('conf.inc');
require(LIB_FILE);
require_once(SRC_ROOT.'/mold/lib/cache/FormCache.class.php');
require_once (SRC_ROOT.'/mold/lib/UtilBussinesscode.class.php');
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

// セッション確認
$objAuth = fncIsSession ($_REQUEST["strSessionID"], $objAuth, $objDB);

// 1800 金型帳票管理
if ( !fncCheckAuthority( DEF_FUNCTION_MM0, $objAuth ) )
{
	fncOutputError( 9018, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
}

// 1805 金型管理(削除)
if ( !fncCheckAuthority( DEF_FUNCTION_MM5, $objAuth ) )
{
	fncOutputError( 9018, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
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
	$reviseCode = $infoMold[TableMold::ReviseCode];
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
$replacement[TableMoldHistory::Status] = $descStatus;
$replacement[FormMoldHistory::ProductCode] = $productCode;
$replacement[FormMoldHistory::ReviseCode] = $reviseCode;
$replacement[FormMoldHistory::ProductName] = $productName;
$replacement[TableMoldHistory::SourceFactory] = $displaySrcFactoryCode;
$replacement[FormMoldHistory::SourceFactoryName] = $displaySrcFactoryName;
$replacement[TableMoldHistory::DestinationFactory] = $displayDstFactoryCode;
$replacement[FormMoldHistory::DestinationFactoryName] = $displayDstFactoryName;

// テンプレート読み込み
$objTemplate = new clsTemplate ();
$objTemplate->getTemplate ("/mm/delete/mm_confirm_delete.html");

// プレースホルダー置換
$objTemplate->replace($replacement);
$objTemplate->complete();

// 金型テーブル生成の為DOMDocumentを使用
$doc = new DOMDocument();

// パースエラー抑制
libxml_use_internal_errors(true);
// DOMパース
$doc->loadHTML($objTemplate->strTemplate);
// パースエラークリア
libxml_clear_errors();
// パースエラー抑制解除
libxml_use_internal_errors(false);

// 金型帳票IDとリビジョンの埋め込み
$btnDelete = $doc->getElementById("delete-button");
$btnDelete->setAttribute("MoldNo", $moldNo);
$btnDelete->setAttribute("HistoryNo", $historyNo);
$btnDelete->setAttribute("Version", $version);

setcookie("strSessionID", $_REQUEST["strSessionID"]);

// HTML出力
echo $doc->saveHTML();
