<?php

// ----------------------------------------------------------------------------
/**
*       金型履歴管理  修正画面
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

// 1900 金型管理
if ( !fncCheckAuthority( DEF_FUNCTION_MR0, $objAuth ) )
{
	fncOutputError ( 9018, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
}

// 1904 金型帳票管理（修正）
if ( !fncCheckAuthority( DEF_FUNCTION_MR4, $objAuth ) )
{
	fncOutputError ( 9018, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
}

// パラメータ取得
$moldReportId = $_REQUEST["MoldReportId"];
$revision = $_REQUEST["Revision"];
$version = $_REQUEST["Version"];

if (!$moldReportId || !(0 <= $revision) || !(0 <= $version))
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

try
{
	// 金型帳票の索引
	$report = $utilMold->selectMoldReport($moldReportId, $revision, $version);

	// 帳票ステータスが完了の場合
	if ($report[TableMoldReport::Status] == '50')
	{
		fncOutputError(9069, DEF_ERROR, "", TRUE, "", $objDB);
	}

	// 金型帳票詳細の索引
	$details = $utilMold->selectMoldReportDetail($moldReportId, $revision);

	// 事業部(顧客) -> 表示会社コードの索引
	$customerCode = $report[TableMoldReport::CustomerCode];
	$displayCustomerCode = $utilCompany->selectDisplayCodeByCompanyCode($customerCode);

	// 担当部署 -> 表示グループコードの索引
	$groupCode = $report[TableMoldReport::KuwagataGroupCode];
	$displayGroupCode = $utilGroup->selectDisplayCodeByGroupCode($groupCode);

	// 担当者 -> 表示ユーザコードの索引
	$userCode = $report[TableMoldReport::KuwagataUserCode];
	$displayUserCode = $utilUser->selectDisplayCodeByUserCode($userCode);

	switch ($report[TableMoldReport::ReportCategory])
	{
		// 移動版/返却版の場合
		case "10":
		case "20":
			// 保管工場
			$srcFactoryCode = $report[TableMoldReport::SourceFactory];
			$displaySrcFactoryCode = $utilCompany->selectDisplayCodeByCompanyCode($srcFactoryCode);
			// 移動先工場
			$dstFactoryCode = $report[TableMoldReport::DestinationFactory];
			$displayDstFactoryCode = $utilCompany->selectDisplayCodeByCompanyCode($dstFactoryCode);
			break;
	}
}
catch (SQLException $e)
{
	// エラーログ出力
	error_log($e->getMessage(), 0);
	// 情報の取得に失敗しました
	fncOutputError(9061, DEF_ERROR, "不正なデータか対象のデータが変更された可能性があります。", TRUE, "", $objDB);
}

// 置換文字列群の作成
$replacement = array();

// 金型帳票ID
$replacement["MoldReportId"] = $moldReportId;
// リビジョン
$replacement["Revision"] = sprintf("00", $revision);

// 製品コード
$replacement["Header_ProductCode"] =$report[TableMoldReport::ProductCode];
$replacement["Detail_ProductCode"] =$report[TableMoldReport::ProductCode];

// 依頼日
$replacement[FormMoldReport::RequestDate] = str_replace ( "-", "/", $report[TableMoldReport::RequestDate]);
// 希望日
$replacement[FormMoldReport::ActionRequestDate] = str_replace ( "-", "/", $report[TableMoldReport::ActionRequestDate]);
// 返却予定日
$replacement[FormMoldReport::ReturnSchedule] = str_replace ( "-", "/", $report[TableMoldReport::ReturnSchedule]);

// 帳票区分
$replacement[FormMoldReport::ReportCategory] = $report[TableMoldReport::ReportCategory];
// 依頼区分
$replacement[FormMoldReport::RequestCategory] = $report[TableMoldReport::RequestCategory];
// 移動方法
$replacement[FormMoldReport::TransferMethod] = $report[TableMoldReport::TransferMethod];
// 指示区分
$replacement[FormMoldReport::InstructionCategory] = $report[TableMoldReport::InstructionCategory];
// 生産後の処理
$replacement[FormMoldReport::FinalKeep] = $report[TableMoldReport::FinalKeep];

// 事業部(顧客)
$replacement[FormMoldReport::CustomerCode] = $displayCustomerCode;
// 担当部署
$replacement[FormMoldReport::KuwagataGroupCode] = $displayGroupCode;
// 担当者
$replacement[FormMoldReport::KuwagataUserCode] = $displayUserCode;

// 保管工場
$replacement[FormMoldReport::SourceFactory] = $displaySrcFactoryCode ;
// 移動先工場
$replacement[FormMoldReport::DestinationFactory] = $displayDstFactoryCode;

// その他
$replacement[FormMoldReport::Note] = $report[TableMoldReport::Note];
// 欄外備考
$replacement[FormMoldReport::MarginalNote] = $report[TableMoldReport::MarginalNote];

// テンプレート読み込み
$template = fncGetReplacedHtmlWithBase("base_mold_noframes.html", "mr/modify/mr_modify.tmpl", $replacement ,$objAuth );

// DOMDocument
$doc = new DOMDocument();

// パースエラー抑制
libxml_use_internal_errors(true);
// DOMパース
$doc->loadHTML($template);
// パースエラークリア
libxml_clear_errors();
// パースエラー抑制解除
libxml_use_internal_errors(false);

// 選択済みの金型リストdivの取得
$initMoldInfo = $doc->getElementById("init-mold-info");

// 金型帳票詳細の件数分走査
foreach ($details as $num => $row)
{
	$index = $num + 1;

	// 金型NO
	$moldNo = $row[TableMoldReportDetail::MoldNo];
	// 金型説明
	$desc = $row[TableMoldReportDetail::MoldDescription];

	// 金型情報埋め込み用input要素作成
	$inputMoldNo = $doc->createElement("input");
	$inputMoldNo->setAttribute("class", "init-mold-info__record");
	$inputMoldNo->setAttribute("index", $index);
	$inputMoldNo->setAttribute("moldno", toUTF8($moldNo));
	$inputMoldNo->setAttribute("desc",  toUTF8($desc));
	$inputMoldNo->setAttribute("style", "display:none");
	$inputMoldNo->setAttribute("disabled", "disabled");
	// div > input
	$initMoldInfo->appendChild($inputMoldNo);
}

// COOKIE設定
setcookie("MoldReportId", $moldReportId);
setcookie("Revision", $revision);
setcookie("Version", $version);

// HTML出力
echo $doc->saveHTML();

function toUTF8($str)
{
	return htmlspecialchars(mb_convert_encoding($str, "utf-8", "eucjp-win"));
}
