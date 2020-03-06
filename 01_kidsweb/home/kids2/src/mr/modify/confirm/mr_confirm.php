<?php

// ----------------------------------------------------------------------------
/**
 * 金型帳票管理 登録確認画面*
 *
 * 処理概要
 * ・登録確認画面を表示
 */
// ----------------------------------------------------------------------------
include('conf.inc');
require(LIB_FILE);
require_once(SRC_ROOT.'/mold/lib/cache/FormCache.class.php');
require_once(SRC_ROOT.'/mold/lib/UtilBussinesscode.class.php');
require_once(SRC_ROOT.'/mold/lib/UtilMold.class.php');
require_once (SRC_ROOT.'/mold/lib/index/FormMoldReport.class.php');

$objDB = new clsDB ();
$objAuth = new clsAuth ();
$objDB->open ( "", "", "", "" );

$aryData = $_REQUEST;

// セッション確認
$objAuth = fncIsSession ( $aryData ["strSessionID"], $objAuth, $objDB);

// 1900 金型帳票管理
if ( !fncCheckAuthority( DEF_FUNCTION_MR0, $objAuth ) )
{
	fncOutputError( 9060, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
}

// 1904金型管理(修正)
if ( !fncCheckAuthority( DEF_FUNCTION_MR4, $objAuth ) )
{
	fncOutputError( 9060, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
}


$moldReportId = $_REQUEST["MoldReportId"];
$revision = $_REQUEST["Revision"];
$version = $_REQUEST["Version"];

// 金型帳票ID又はリビジョンの指定がない場合
if (!$moldReportId || !(0 <= $revision) || !(0 <= $version))
{
	// 情報取得エラー
	fncOutputError(9061, DEF_ERROR, "パラメータが不正です。", TRUE, "", $objDB);
}

// キャッシュインスタンスの取得
$formCache = FormCache::getInstance();

// キャッシュ(フォーム)データの取り出し
$resultFormCache = $formCache->get($aryData["resultHash"]);

// キャッシュ(フォーム)データが取り出せた場合
if($resultFormCache && pg_num_rows($resultFormCache) == 1)
{
	// キャッシュレコード取得
	$workCache = pg_fetch_array($resultFormCache, 0, PGSQL_ASSOC);

	// デシリアライズ
	$workFormData = FormCache::deserialize($workCache["serializeddata"]);

	// ユーティリティインスタンスの取得
	$utilBussinesscode = UtilBussinesscode::getInstance();
	$utilMold = UtilMold::getInstance();

	// 業務コードからコード説明を索引
	$aryData["ReportCategoryDesc"] = $utilBussinesscode->getDescription('帳票区分',  $workFormData[FormMoldReport::ReportCategory]);
	$aryData["RequestCategoryDesc"] = $utilBussinesscode->getDescription('依頼区分', $workFormData[FormMoldReport::RequestCategory]);
	$aryData["InstructionCategoryDesc"] = $utilBussinesscode->getDescription('指示区分', $workFormData[FormMoldReport::InstructionCategory]);

	// 帳票区分が10:移動版又は20:返却版の場合
	if (($workFormData[FormMoldReport::ReportCategory] == "10" ||
		 $workFormData[FormMoldReport::ReportCategory] == "20"))
	{
		$aryData["TransferMethodDesc"] = $utilBussinesscode->getDescription('移動方法', $workFormData[FormMoldReport::TransferMethod]);
		$aryData["FinalKeepDesc"] = $utilBussinesscode->getDescription('生産後の処理', $workFormData[FormMoldReport::FinalKeep]);
	}

	// TO項目(仕入元会社)の索引 暫定的に最初の金型の仕入元を取得する
	$venderInfo = $utilMold->getVenderInfomation($workFormData[FormMoldReport::MoldNo."1"]);
	$aryData["SendTo"] = $venderInfo["companydisplaycode"];
	$aryData["SendToName"] = $venderInfo["companydisplayname"];

	// テンプレート読み込み
	$objTemplate = new clsTemplate ();
	$objTemplate->getTemplate ( "/mr/modify/mr_modify_confirm.html" );

	// 置換文字列群
	$replacement = array_merge($aryData, $workFormData);
	$replacement["MoldReportId"] = $moldReportId;
	$replacement["Revision"] = $revision;

	// プレースホルダー置換
	$objTemplate->replace($replacement);
	$objTemplate->complete();

	// 金型NOの抽出
	$listMoldNo = UtilMold::extractArray($workFormData, FormMoldReport::MoldNo);
	// 金型説明の抽出
	$listMoldDescription = UtilMold::extractArray($workFormData, FormMoldReport::MoldDescription);

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

	// 金型テーブルの取得
	$moldTable = $doc->getElementById("MoldTable");

	// 金型NOの件数分走査
	for($i = 1; $i <= count($listMoldNo); $i++)
	{
		// 金型テーブルのtr作成
		$elmTableRacord = $doc->createElement("tr");

		// 金型テーブルのtd要素作成
		$elmTableCellIndex = $doc->createElement("td");
		$elmTableCellMoldNo = $doc->createElement("td");
		$elmTableCellDescription = $doc->createElement("td");

		// td要素内のテキスト設定
		$elmTableCellIndex->appendChild($doc->createTextNode($i));
		$elmTableCellMoldNo->appendChild($doc->createTextNode(toUTF8($listMoldNo[FormMoldReport::MoldNo.$i])));
		$elmTableCellDescription->appendChild($doc->createTextNode(toUTF8($listMoldDescription[FormMoldReport::MoldDescription.$i])));

		// td要素をtr要素に追加
		$elmTableRacord->appendChild($elmTableCellIndex);
		$elmTableRacord->appendChild($elmTableCellMoldNo);
		$elmTableRacord->appendChild($elmTableCellDescription);

		// 金型テーブルへtr要素を追加
		$moldTable->appendChild($elmTableRacord);
	}

	// cookieセット
	setcookie("strSessionID", $_REQUEST["strSessionID"]);
	setcookie("resultHash", $_REQUEST["resultHash"]);

	// html出力
	echo $doc->saveHTML();
}
// キャッシュ(フォーム)データが取り出せなかった場合
else
{
	// キャッシュ取り出し失敗
	fncOutputError(9065, DEF_ERROR, "", TRUE, "", $objDB);
}