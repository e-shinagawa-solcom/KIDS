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

// 1900 金型帳票管理
if ( !fncCheckAuthority( DEF_FUNCTION_MR0, $objAuth ) )
{
	fncOutputError( 9018, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
}

// 1903 金型帳票管理(検索)
if ( !fncCheckAuthority( DEF_FUNCTION_MR3, $objAuth ) )
{
	fncOutputError( 9018, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
}

// パラメータ取得
$moldReportId = $_REQUEST["MoldReportId"];
$revision = $_REQUEST["Revision"];
$version = $_REQUEST["Version"];

// 金型帳票IDの指定がない場合
if (!$moldReportId || !(0 <= $revision) || !(0 <= $version))
{
	// 情報取得エラー
	fncOutputError(9061, DEF_ERROR, "パラメータが不正です。", TRUE, "", $objDB);
}

// ユーティリティのインスタンス取得
$utilMold = UtilMold::getInstance();
$utilBussinesscode = UtilBussinesscode::getInstance();
$utilCompany = UtilCompany::getInstance();
$utilGroup = UtilGroup::getInstance();
$utilUser = UtilUser::getInstance();
$utilProduct = UtilProduct::getInstance();

// 金型帳票の取得
if (!$recordMoldReport = $utilMold->selectMoldReport($moldReportId, $revision, $version))
{
	// 情報取得エラー
	fncOutputError(9061, DEF_ERROR, "金型帳票の取得に失敗しました。対象データが変更された可能性があります。", TRUE, "", $objDB);
}

// 金型帳票詳細の取得
if (!$recordMoldReportDetail = $utilMold->selectMoldReportDetail($moldReportId, $recordMoldReport[TableMoldReport::Revision]))
{
	// 情報取得エラー
	fncOutputError(9061, DEF_ERROR, "金型帳票詳細の取得に失敗しました。対象データが変更された可能性があります。", TRUE, "", $objDB);
}

// 金型帳票/金型帳票詳細 配列マージ
$replacement = array_merge($recordMoldReport, $recordMoldReportDetail);

// 業務コードからコード説明を索引
$replacement["ReportCategoryDesc"] = $utilBussinesscode->getDescription('帳票区分',  $replacement[TableMoldReport::ReportCategory]);
$replacement["RequestCategoryDesc"] = $utilBussinesscode->getDescription('依頼区分', $replacement[TableMoldReport::RequestCategory]);
$replacement["InstructionCategoryDesc"] = $utilBussinesscode->getDescription('指示区分', $replacement[TableMoldReport::InstructionCategory]);

// コードから表示名を取得
$replacement["CustomerName"] = $utilCompany->selectDisplayNameByCompanyCode($replacement[TableMoldReport::CustomerCode]);
$replacement["KuwagataGroupName"] = $utilGroup->selectDisplayNameByGroupCode($replacement[TableMoldReport::KuwagataGroupCode]);
$replacement["KuwagataUserName"] = $utilUser->selectDisplayNameByUserCode($replacement[TableMoldReport::KuwagataUserCode]);
$replacement["CreateByName"] = $utilUser->selectDisplayNameByUserCode($replacement[TableMoldReport::CreateBy]);
$replacement["UpdateByName"] = $utilUser->selectDisplayNameByUserCode($replacement[TableMoldReport::UpdateBy]);

// コードから表示コードに置き換え
$replacement[TableMoldReport::CustomerCode] = $utilCompany->selectDisplayCodeByCompanyCode($replacement[TableMoldReport::CustomerCode]);
$replacement[TableMoldReport::KuwagataGroupCode] = $utilGroup->selectDisplayCodeByGroupCode($replacement[TableMoldReport::KuwagataGroupCode]);
$replacement[TableMoldReport::KuwagataUserCode] = $utilUser->selectDisplayCodeByUserCode($replacement[TableMoldReport::KuwagataUserCode]);
$replacement[TableMoldReport::CreateBy] = $utilUser->selectDisplayCodeByUserCode($replacement[TableMoldReport::CreateBy]);
$replacement[TableMoldReport::UpdateBy] = $utilUser->selectDisplayCodeByUserCode($replacement[TableMoldReport::UpdateBy]);

switch ($recordMoldReport[TableMoldReport::ReportCategory])
{
	case "10":
	case "20":
		// 業務コードからコード説明を索引
		$replacement["TransferMethodDesc"] = $utilBussinesscode->getDescription('移動方法', $replacement[TableMoldReport::TransferMethod]);
		$replacement["FinalKeepDesc"] = $utilBussinesscode->getDescription('生産後の処理', $replacement[TableMoldReport::FinalKeep]);
		// コードから表示名を取得
		$replacement["SourceFactoryName"] = $utilCompany->selectDisplayNameByCompanyCode($replacement[TableMoldReport::SourceFactory]);
		$replacement["DestinationFactoryName"] = $utilCompany->selectDisplayNameByCompanyCode($replacement[TableMoldReport::DestinationFactory]);
		// コードから表示コードに置き換え
		$replacement[TableMoldReport::SourceFactory] = $utilCompany->selectDisplayCodeByCompanyCode($replacement[TableMoldReport::SourceFactory]);
		$replacement[TableMoldReport::DestinationFactory] = $utilCompany->selectDisplayCodeByCompanyCode($replacement[TableMoldReport::DestinationFactory]);
		break;
}

// 製品名称の取得
$replacement["ProductName"] = $utilProduct->selectProductNameByProductCode($replacement[TableMoldReport::ProductCode], $replacement[TableMoldReport::strReviseCode]);

// TO項目(仕入元会社)の索引 暫定的に最初の金型の仕入元を取得する
$venderInfo = $utilMold->getVenderInfomation($recordMoldReportDetail[0][TableMoldReportDetail::MoldNo]);
$replacement["SendTo"] = $venderInfo["companydisplaycode"];
$replacement["SendToName"] = $venderInfo["companydisplayname"];

// テンプレート読み込み
$objTemplate = new clsTemplate ();
$objTemplate->getTemplate ("/mr/detail/mr_detail.html");

// プレースホルダー置換
$objTemplate->replace($replacement);
$objTemplate->complete();

// 金型テーブル生成の為DOMDocumentを使用
$doc = new DOMDocument();

// パースエラー抑制
libxml_use_internal_errors(true);
// DOMパース
$doc->loadHTML(mb_convert_encoding($objTemplate->strTemplate, "utf8", "eucjp-win"));
// パースエラークリア
libxml_clear_errors();
// パースエラー抑制解除
libxml_use_internal_errors(false);

// 金型テーブルの取得
$moldTable = $doc->getElementById("MoldTable");

// 金型帳票詳細の件数分走査
foreach ($recordMoldReportDetail as $i => $record)
{
	$index = $i + 1;

	// 金型テーブルのtr作成
	$tr = $doc->createElement("tr");

	// 金型テーブルのtd要素作成
	$cellIndex = $doc->createElement("td", $index);
	$cellMoldNo = $doc->createElement("td", toUTF8($record[TableMoldReportDetail::MoldNo]));
	$cellDescription = $doc->createElement("td", toUTF8($record[TableMoldReportDetail::MoldDescription]));

	// td要素をtr要素に追加
	$tr->appendChild($cellIndex);
	$tr->appendChild($cellMoldNo);
	$tr->appendChild($cellDescription);

	// 金型テーブルへtr要素を追加
	$moldTable->appendChild($tr);
}

// HTML出力
echo $doc->saveHTML();

