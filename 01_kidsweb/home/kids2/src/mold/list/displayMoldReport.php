<?php

// ----------------------------------------------------------------------------
//       金型帳票出力 帳票出力画面
// ----------------------------------------------------------------------------
// ライブラリファイル読込
include( 'conf.inc' );
require( LIB_FILE );


require_once(SRC_ROOT.'/mold/lib/UtilBussinesscode.class.php');
require_once(SRC_ROOT.'/mold/lib/UtilMold.class.php');
require_once(SRC_ROOT.'/mold/lib/UtilGroup.class.php');
require_once(SRC_ROOT.'/mold/lib/UtilUser.class.php');
require_once(SRC_ROOT.'/mold/lib/UtilProduct.class.php');
require_once(SRC_ROOT.'/mold/lib/UtilCompany.class.php');

require_once (SRC_ROOT.'/mold/lib/index/TableMoldReport.class.php');
require_once (SRC_ROOT.'/mold/lib/index/TableMoldReportDetail.class.php');

// 金型帳票テンプレートパス
const PATH_TEMPLATE = "/list/result/mr_base.html";
// 金型帳票メインコンテンツのID
const ID_REPORT_CONTENTS = "mold-report-page";
// 金型帳票ページ番号表示位置の取得
const ID_PAGE_NUMBER = "page-number";

// 金型帳票テンプレート IDプレフィックス
const ID_MOLD_INDEX = "mold-index";
const ID_MOLD_NO = "mold-no";
const ID_MOLD_DESC = "mold-desc";

// オブジェクト生成
$objDB   = new clsDB();
$objAuth = new clsAuth();

// DBオープン
$objDB->open("", "", "", "");

setcookie("strSessionID", $_REQUEST["strSessionID"]);

// セッション確認
$objAuth = fncIsSession( $_REQUEST["strSessionID"], $objAuth, $objDB );

// 1900 金型帳票管理
if ( !fncCheckAuthority( DEF_FUNCTION_MR0, $objAuth ) )
{
	fncOutputError( 9018, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
}

// パラメータの取得
$moldReportId = $_REQUEST["MoldReportId"];
$revision = $_REQUEST["Revision"];
$version = $_REQUEST["Version"];

if($moldReportId && (0 <= $revision) && (0 <= $version))
{
	// ユーティリティクラスのインスタンス生成
	$utilMold = UtilMold::getInstance();
	$utilBussinesscode = UtilBussinesscode::getInstance();
	$utilGroup = UtilGroup::getInstance();
	$utilUser = UtilUser::getInstance();
	$utilProduct = UtilProduct::getInstance();
	$utilCompany = UtilCompany::getInstance();

	try
	{
		// 金型帳票データの取得
		$recordMoldReport = $utilMold->selectMoldReport($moldReportId, $revision, $version);

		// リビジョンの取得
		$revision = $recordMoldReport[TableMoldReport::Revision];
		// 印刷済フラグの取得
		$printed =  $recordMoldReport[TableMoldReport::Printed];

		// リクエストが原本印刷の場合 かつ
		// リクエスト元が金型帳票登録完了画面以外の場合
		if (!array_key_exists("isCopy", $_REQUEST) && !isset($_REQUEST['isRegist']))
		{
			// 原本が印刷されていない場合
			if($printed == "f")
			{
				try
				{
					// トランザクション開始
					$objDB->transactionBegin();
					// 印刷済みフラグをtrueに更新
					$utilMold->updateAlredyPrintedReport($moldReportId, $revision);
					// コミット
					$objDB->transactionCommit();
				}
				catch (SQLException $e)
				{
					// ロールバック
					$objDB->transactionRollback();
					throw $e;
				}
			}
		}

		// 業務コード説明の取得(_%ReportCategoryDesc%_)
		$recordMoldReport[TableMoldReport::ReportCategory."Desc"] =
			$utilBussinesscode->getDescription('帳票区分', $recordMoldReport[TableMoldReport::ReportCategory]);

		// 製品名称の取得(_%ProductName%_)
		$recordMoldReport["ProductName"] =
			$utilProduct->selectProductNameByProductCode($recordMoldReport[TableMoldReport::ProductCode], $recordMoldReport[TableMoldReport::ReviseCode]);

		// 表示会社名を取得
		// _%SendTo%_
		$recordMoldReport[TableMoldReport::SendTo] =
			$utilCompany->selectDisplayNameByCompanyCode($recordMoldReport[TableMoldReport::SendTo]);
		// _%CustomerName%_
		$recordMoldReport["CustomerName"] =
			$utilCompany->selectDisplayNameByCompanyCode($recordMoldReport[TableMoldReport::CustomerCode]);

		// 帳票区分が10:移動版又は20:返却版の場合
		if (($recordMoldReport[TableMoldReport::ReportCategory] == "10" ||
			 $recordMoldReport[TableMoldReport::ReportCategory] == "20"))
		{
			// _%SourceFactory%_
			$recordMoldReport[TableMoldReport::SourceFactory] =
				$utilCompany->selectDisplayNameByCompanyCode($recordMoldReport[TableMoldReport::SourceFactory]);
			// _%DestinationFactory%_
			$recordMoldReport[TableMoldReport::DestinationFactory] =
				$utilCompany->selectDisplayNameByCompanyCode($recordMoldReport[TableMoldReport::DestinationFactory]);
		}

		// 表示グループ名を取得(_%KuwagataGroupName%_)
		$recordMoldReport["KuwagataGroupName"] =
			$utilGroup->selectDisplayNameByGroupCode($recordMoldReport[TableMoldReport::KuwagataGroupCode]);
		// 表示ユーザ名を取得(_%KuwagataUserName%_)
		$recordMoldReport["KuwagataUserName"] =
			$utilUser->selectDisplayNameByUserCode($recordMoldReport[TableMoldReport::KuwagataUserCode]);

		// 日付の分割(ActionRequestDate)
		list($yyyy, $mm, $dd) = explode("-", $recordMoldReport[TableMoldReport::ActionRequestDate]);
		$recordMoldReport[TableMoldReport::ActionRequestDate."0"] = $yyyy;
		$recordMoldReport[TableMoldReport::ActionRequestDate."1"] = $mm;
		$recordMoldReport[TableMoldReport::ActionRequestDate."2"] = $dd;

		// 日付の分割(RequestDate)
		list($yyyy, $mm, $dd) = explode("-", $recordMoldReport[TableMoldReport::RequestDate]);
		$recordMoldReport[TableMoldReport::RequestDate."0"] = $yyyy;
		$recordMoldReport[TableMoldReport::RequestDate."1"] = $mm;
		$recordMoldReport[TableMoldReport::RequestDate."2"] = $dd;

		// 日付の分割(ReturnSchedule)
		list($yyyy, $mm, $dd) = explode("-", $recordMoldReport[TableMoldReport::ReturnSchedule]);
		$recordMoldReport[TableMoldReport::ReturnSchedule."0"] = $yyyy;
		$recordMoldReport[TableMoldReport::ReturnSchedule."1"] = $mm;
		$recordMoldReport[TableMoldReport::ReturnSchedule."2"] = $dd;

		// 金型詳細の取得
		$details = $utilMold->selectMoldReportDetail(
				$recordMoldReport[TableMoldReport::MoldReportId],
				$recordMoldReport[TableMoldReport::Revision]);

		// 金型詳細件数の取得
		$cntDetails = count($details);

		// 金型帳票IDの出力(フォーマット変更)
		$revision = "-" . str_pad($recordMoldReport[TableMoldReport::Revision], 2, "0", STR_PAD_LEFT);
		$recordMoldReport[TableMoldReport::MoldReportId] = $recordMoldReport[TableMoldReport::MoldReportId] . $revision;

		// テンプレート読み込み
		$objTemplate = new clsTemplate ();
		$objTemplate->getTemplate (PATH_TEMPLATE);

		// プレースホルダー置換
		$objTemplate->replace($recordMoldReport);
		$objTemplate->complete();

		$doc = new DOMDocument();

		// パースエラー抑制
		libxml_use_internal_errors(true);
		// DOMパース
		$doc->loadHTML($objTemplate->strTemplate);
		// パースエラークリア
		libxml_clear_errors();
		// パースエラー抑制解除
		libxml_use_internal_errors(false);

		// body要素の取得
		$body = $doc->getElementsByTagName("body")->item(0);
		// 金型帳票ページ単位取得
		$page = $doc->getElementById(ID_REPORT_CONTENTS);
		// ページ設定(1ページ目)
		$page->setAttribute("page", 1);

		// 依頼区分
		switch ($recordMoldReport[TableMoldReport::RequestCategory])
		{
			case "10":
				setSelectedCell($doc->getElementById("japan-request"));
				break;
			case "20":
				setSelectedCell($doc->getElementById("hongkong-request"));
				break;
			default:
				// プログラムエラー
				fncOutputError(9054, DEF_ERROR, "不正な依頼区分です。:"."", TRUE, "", $objDB);
				break;
		}

		// 指示区分
		switch ($recordMoldReport[TableMoldReport::InstructionCategory])
		{
			case "10":
				setSelectedCell($doc->getElementById("instruction-customer"));
				break;
			case "20":
				setSelectedCell($doc->getElementById("instruction-kuwagata"));
				break;
			default:
				// TODO 例外出力 業務コードエラー
				break;
		}

		// 移動版/返却版のみセルの塗り潰しを行う項目
		if ($recordMoldReport[TableMoldReport::ReportCategory] == "10" ||
				$recordMoldReport[TableMoldReport::ReportCategory] == "20")
		{
			// 移動方法
			switch ($recordMoldReport[TableMoldReport::TransferMethod])
			{
				case "10":
					setSelectedCell($doc->getElementById("deliver-to-receiver"));
					break;
				case "20":
					setSelectedCell($doc->getElementById("pickup-by-receiver"));
					break;
				case "30":
					setSelectedCell($doc->getElementById("via-hong-kong"));
					break;
				default:
					// TODO 例外出力 業務コードエラー
					break;
			}
			// 生産後の処理
			switch ($recordMoldReport[TableMoldReport::FinalKeep])
			{
				case "10":
					setSelectedCell($doc->getElementById("keep-by-receiver"));
					break;
				case "20":
					setSelectedCell($doc->getElementById("return-to-original"));
					break;
				default:
					// TODO 例外出力 業務コードエラー
					break;
			}
		}

		// 詳細件数が10件以下の場合(帳票1枚)
		// # 0件は索引時に例外を投げるので考えない
		if ($cntDetails <= 10)
		{
			foreach($details as $i => $record)
			{
				$index = $i + 1;

				// 金型 各種セルの取得
				$td_index = $doc->getElementById(ID_MOLD_INDEX.$index);
				$td_moldNo = $doc->getElementById(ID_MOLD_NO.$index);
				$td_desc = $doc->getElementById(ID_MOLD_DESC.$index);

				// テキストノードの作成
				$text_index = $doc->createTextNode($index);
				$text_moldNo = $doc->createTextNode(toUTF8($details[$i][TableMoldReportDetail::MoldNo]));
				$text_desc = $doc->createTextNode(toUTF8($details[$i][TableMoldReportDetail::MoldDescription]));

				// テキストノードの追加
				$td_index->appendChild($text_index);
				$td_moldNo->appendChild($text_moldNo);
				$td_desc->appendChild($text_desc);
			}

			// ページ番号設定
			$td_pageNum = $doc->getElementById(ID_PAGE_NUMBER);
			$td_pageNum->appendChild($doc->createTextNode("1 / 1"));

			// 画面出力
			// header("Content-type: text/html; charset=utf-8");
			$out = $doc->saveHTML();
			echo $out;
		}
		// 10件以上(帳票2枚以上)
		else
		{
			// 最大ページ数の算出
			$maxPage = floor($cntDetails / 10) + ($cntDetails % 10 ? 1 : 0);

			// 共通部分保存の為、一旦clsTemplate内に退避
			$objTemplate->strTemplate = $doc->saveHTML();

			// 帳票ページ単位でDOMDocumentを作成
			$docs[] = $doc;

			// パースエラー抑制
			libxml_use_internal_errors(true);

			// 金型数に応じて追加ページを作成する(1ページ分下駄を履かせる)
			for ($i = 1; $i < $maxPage; $i++)
			{
				// 新規ページの作成用のDOMDocumentを作成
				$newDoc = new DOMDocument();
				$newDoc->loadHTML($objTemplate->strTemplate);
				// 金型帳票ページ単位取得
				$newPage = $newDoc->getElementById(ID_REPORT_CONTENTS);
				// ページ番号設定
				$newPage->setAttribute("page", $i + 1);
				// DOMDocumentの追加
				$docs[] = $newDoc;
			}

			// パースエラークリア
			libxml_clear_errors();
			// パースエラー抑制解除
			libxml_use_internal_errors(false);

			// (テンプレート上の)ページ番号の設定
			foreach($docs as $pageNum => $innerDoc)
			{
				$td_pageNum = $innerDoc->getElementById(ID_PAGE_NUMBER);
				// ページ番号テキストノードの作成
				$text_pageNum = $innerDoc->createTextNode(($pageNum + 1)." / ".$maxPage);
				$td_pageNum->appendChild($text_pageNum);
			}

			// 金型数分走査
			foreach($details as $i => $record)
			{
				$index = $i + 1;

				// 現在のページ番号を採番
				if ($index <=10)
				{
					$currentPage = 0;
				}
				// 2ページ名以上(改ページ)
				else if ($index%10 == 1)
				{
					// 改ページ
					$currentPage++;
				}

				// ID取得用のindex作成(接頭辞+1～10までなので)
				$index_loop = ($index%10 == 0) ? 10 : $index - floor($index/10)*10;

				// 金型 各種セルの取得
				$td_index = $docs[$currentPage]->getElementById(ID_MOLD_INDEX.$index_loop);
				$td_moldNo = $docs[$currentPage]->getElementById(ID_MOLD_NO.$index_loop);
				$td_desc = $docs[$currentPage]->getElementById(ID_MOLD_DESC.$index_loop);

				// テキストノードの作成
				$text_index = $docs[$currentPage]->createTextNode($index);
				$text_moldNo = $docs[$currentPage]->createTextNode(toUTF8($details[$i][TableMoldReportDetail::MoldNo]));
				$text_desc = $docs[$currentPage]->createTextNode(toUTF8($details[$i][TableMoldReportDetail::MoldDescription]));

				// テキストノードの追加
				$td_index->appendChild($text_index);
				$td_moldNo->appendChild($text_moldNo);
				$td_desc->appendChild($text_desc);
			}

			// 親(マージ先)となる先頭要素のDOMDocumentの退避
			array_shift($docs);

			// ノードのマージ
			foreach ($docs as $innerDoc)
			{
				// 帳票コンテンツの取得
				$srcContents = $innerDoc->getElementById(ID_REPORT_CONTENTS);
				// 帳票コンテンツのインポート
				$currentContents = $doc->importNode($srcContents, true);
				$body->appendChild($currentContents);
			}

			// 画面出力
			// header("Content-type: text/html; charset=utf-8");
			echo $doc->saveHTML();
		}
	}
	catch (SQLException $e)
	{
		echo ($e->getMessage());
		// 情報の取得に失敗しました。
		fncOutputError(9061, DEF_WARNING, " 対象のデータが変更された可能性があります。", TRUE, "", $objDB);
	}
	catch (InvalidArgumentException $e)
	{
		// プログラムエラー
		fncOutputError(9054, DEF_WARNING, "", TRUE, "", $objDB);
	}
}
else
{
	fncOutputError(9061, DEF_WARNING, "パラメータが不正です。", TRUE, "", $objDB);
}

/**
 * 金型帳票の選択欄を塗りつぶす
 * @param DOMElement $element
 */
function setSelectedCell(DOMElement $element)
{
	$currentClassName = $element->getAttribute("class");
	$element->setAttribute("class", $currentClassName." "."selected-cell");
}