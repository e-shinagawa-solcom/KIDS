<?php

// ----------------------------------------------------------------------------
/**
 * 金型帳票管理 詳細画面*
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

$aryData = $_REQUEST;

// セッション確認
$objAuth = fncIsSession ( $aryData ["strSessionID"], $objAuth, $objDB);

// 1800 金型帳票管理
if ( !fncCheckAuthority( DEF_FUNCTION_MM0, $objAuth ) )
{
	fncOutputError( 9018, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
}

// 1803 金型管理(詳細)
if ( !fncCheckAuthority( DEF_FUNCTION_MM3, $objAuth ) )
{
	fncOutputError( 9018, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
}


$moldNo = $_REQUEST["MoldNo"];
$historyNo = $_REQUEST["HistoryNo"];
$version = $_REQUEST["Version"];

// パラメータの指定がない場合
if (!$moldNo || !(0 <= $historyNo) || !(0 <= $version))
{
	// 情報取得エラー
	fncOutputError(9061, DEF_ERROR, "不正なパラメータです。", TRUE, "", $objDB);
}


// ユーティリティのインスタンス取得
$utilMold = UtilMold::getInstance();
$utilBussinesscode = UtilBussinesscode::getInstance();
$utilCompany = UtilCompany::getInstance();
$utilGroup = UtilGroup::getInstance();
$utilUser = UtilUser::getInstance();
$utilProduct = UtilProduct::getInstance();

// 金型履歴の取得
if (!$record = $utilMold->selectMoldHistory($moldNo, $historyNo, $version))
{
	// 情報取得エラー
	fncOutputError(9061, DEF_ERROR, "金型帳票の取得に失敗しました。", TRUE, "", $objDB);
}
// 金型マスタの取得
if (!$mold = $utilMold->selectMold($moldNo))
{
	// 情報取得エラー
	fncOutputError(9061, DEF_ERROR, "金型マスタの取得に失敗しました。", TRUE, "", $objDB);
}

// 金型履歴を置換文字列群に追加
$replacement = $record;
$status = $record[TableMoldHistory::Status];
// 業務コードからコード説明を索引
$replacement[TableMoldHistory::Status."Desc"] = $utilBussinesscode->getDescription('金型ステータス',  $replacement[TableMoldHistory::Status]);

switch($status)
{
	case "10":
	case "20":
		// コードから表示名を取得
		$replacement["SourceFactoryName"] = $utilCompany->selectDisplayNameByCompanyCode($replacement[TableMoldHistory::SourceFactory]);
		$replacement["DestinationFactoryName"] = $utilCompany->selectDisplayNameByCompanyCode($replacement[TableMoldHistory::DestinationFactory]);
		// コードから表示コードに置き換え
		$replacement[TableMoldHistory::SourceFactory] = $utilCompany->selectDisplayCodeByCompanyCode($replacement[TableMoldHistory::SourceFactory]);
		$replacement[TableMoldHistory::DestinationFactory] = $utilCompany->selectDisplayCodeByCompanyCode($replacement[TableMoldHistory::DestinationFactory]);
		break;
}

// コードから表示名を取得
$replacement["CreateByName"] = $utilUser->selectDisplayNameByUserCode($replacement[TableMoldHistory::CreateBy]);
$replacement["UpdateByName"] = $utilUser->selectDisplayNameByUserCode($replacement[TableMoldHistory::UpdateBy]);

// コードから表示コードに置き換え
$replacement[TableMoldHistory::CreateBy] = $utilUser->selectDisplayCodeByUserCode($replacement[TableMoldHistory::CreateBy]);
$replacement[TableMoldHistory::UpdateBy] = $utilUser->selectDisplayCodeByUserCode($replacement[TableMoldHistory::UpdateBy]);

// 製品コードの取得
$replacement[TableMold::ProductCode] = $mold[TableMold::ProductCode];
// 製品名称の取得
$replacement["ProductName"] = $utilProduct->selectProductNameByProductCode($replacement[TableMold::ProductCode]);

// テンプレート読み込み
$objTemplate = new clsTemplate ();
$objTemplate->getTemplate ("/mm/detail/mm_detail.html");

// プレースホルダー置換
$objTemplate->replace($replacement);
$objTemplate->complete();

// HTML出力
echo $objTemplate->strTemplate;
