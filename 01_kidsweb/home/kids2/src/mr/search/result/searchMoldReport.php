<?php

// ----------------------------------------------------------------------------
/**
*       金型帳票管理  検索処理
*/
// ----------------------------------------------------------------------------
include( 'conf.inc' );
require_once( LIB_FILE );
require_once(SRC_ROOT.'/mold/lib/cache/FormCache.class.php');
require_once (SRC_ROOT.'/mold/lib/UtilBussinesscode.class.php');
require_once (SRC_ROOT.'/mold/validation/UtilValidation.class.php');
require_once (SRC_ROOT.'/mold/lib/UtilMold.class.php');
require_once(SRC_ROOT.'/mold/lib/UtilGroup.class.php');
require_once(SRC_ROOT.'/mold/lib/UtilUser.class.php');
require_once(SRC_ROOT.'/mold/lib/UtilProduct.class.php');
require_once(SRC_ROOT.'/mold/lib/UtilCompany.class.php');
require_once(SRC_ROOT.'/mold/lib/UtilSearchForm.class.php');
require_once (SRC_ROOT.'/mold/lib/index/FormMoldReport.class.php');
require_once(SRC_ROOT.'/mold/lib/index/TableMoldReport.class.php');
require_once(SRC_ROOT.'/mold/lib/index/TableMoldReportDetail.class.php');

$objDB   = new clsDB();
$objAuth = new clsAuth();

// DBオープン
$objDB->open("", "", "", "");

// リクエスト取得
$aryData = $_REQUEST;

// 言語コードを取得(0->false: 英語, 1->true: 日本語)
$lngLanguageCode = 1;

// セッション確認
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

// 1900 金型帳票管理
if ( !fncCheckAuthority( DEF_FUNCTION_MR0, $objAuth ) )
{
	fncOutputError( 9018, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
}

// 1902 金型帳票管理(検索)
if ( !fncCheckAuthority( DEF_FUNCTION_MR2, $objAuth ) )
{
	fncOutputError( 9018, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
}

// フォームデータから各カテゴリの振り分けを行う
$isDisplay = UtilSearchForm::extractArrayByIsDisplay($_REQUEST);
$isSearch = UtilSearchForm::extractArrayByIsSearch($_REQUEST);
$from = UtilSearchForm::extractArrayByFrom($_REQUEST);
$to = UtilSearchForm::extractArrayByTo($_REQUEST);
$searchValue = $_REQUEST;

// クエリの組立に使用するフォームデータを抽出
$searchColumns = array();
$displayColumns = array();
$conditions = array();

// 表示項目の抽出
foreach($isDisplay as $key => $flag)
{
	if ($flag == "on")
	{
		$displayColumns[$key] = $key;
	}
}

// 検索項目の抽出
foreach($isSearch as $key => $flag)
{
	if ($flag == "on")
	{
		$searchColumns[$key] = $key;
	}
}

// クエリの組立て
$query = array();
$query[] = "SELECT";
$query[] = "      mr.moldreportid";
$query[] = "    , mr.revision";
$query[] = "    , mr.reportcategory";
$query[] = "    , mr.status";
$query[] = "    , mr.requestdate";
$query[] = "    , mr.sendto";
$query[] = "    , mr.attention";
$query[] = "    , mr.carboncopy";
$query[] = "    , mr.productcode || '_' || mr.strrevisecode as productcode";
$query[] = "    , mp.strproductname";
$query[] = "    , mp.strproductenglishname";
$query[] = "    , mr.goodscode";
$query[] = "    , mr.requestcategory";
$query[] = "    , mr.actionrequestdate";
$query[] = "    , mr.actiondate";
$query[] = "    , mr.transfermethod";
$query[] = "    , mr.sourcefactory";
$query[] = "    , mr.destinationfactory";
$query[] = "    , mr.instructioncategory";
$query[] = "    , mr.customercode";
$query[] = "    , mr.kuwagatagroupcode";
$query[] = "    , mr.kuwagatausercode";
$query[] = "    , mr.note";
$query[] = "    , mr.finalkeep";
$query[] = "    , mr.returnschedule";
$query[] = "    , mr.marginalnote";
$query[] = "    , mr.created::date";
$query[] = "    , mr.createby";
$query[] = "    , mr.updated::date";
$query[] = "    , mr.updateby";
$query[] = "    , mr.version";
$query[] = "    , mr.deleteflag";
$query[] = "    , mrd.moldno";
$query[] = "FROM";
$query[] = "    m_moldreport mr";
$query[] = "INNER JOIN";
$query[] = "(";
$query[] = "    SELECT";
$query[] = "         moldreportid";
$query[] = "       , max(revision) revision";
$query[] = "       , array_to_string(array_agg(moldno), ',') moldno";
$query[] = "    FROM";
$query[] = "        t_moldreportdetail";
$query[] = "    WHERE";
$query[] = "        deleteflag = false";
$query[] = "    GROUP BY";
$query[] = "        moldreportid";
$query[] = ") mrd";
$query[] = "  ON";
$query[] = "        mr.moldreportid = mrd.moldreportid";
$query[] = "    AND mrd.revision = mrd.revision";
$query[] = "  LEFT OUTER JOIN ( ";
$query[] = "    SELECT";
$query[] = "      p.* ";
$query[] = "    FROM";
$query[] = "      m_product p ";
$query[] = "      inner join ( ";
$query[] = "        SELECT";
$query[] = "          MAX(lngrevisionno) lngrevisionno";
$query[] = "          ,lngproductno, strrevisecode  ";
$query[] = "        FROM";
$query[] = "          m_product ";
$query[] = "        WHERE";
$query[] = "          bytInvalidFlag = false ";
$query[] = "        group by";
$query[] = "          lngproductno, strrevisecode";
$query[] = "      ) p1 ";
$query[] = "        on p.lngproductno = p1.lngproductno ";
$query[] = "        and p.strrevisecode = p1.strrevisecode ";
$query[] = "        and p.lngrevisionno = p1.lngrevisionno ";
$query[] = "    where";
$query[] = "      p.lngrevisionno >= 0";
$query[] = "  ) mp ";
$query[] = "  ON";
$query[] = "    mr.productcode = mp.strproductcode";
$query[] = "  AND  mr.strrevisecode = mp.strrevisecode";
$query[] = "WHERE";
$query[] = "    (mr.moldreportid, mr.revision) in";
$query[] = "    (";
$query[] = "        SELECT";
$query[] = "              moldreportid";
$query[] = "            , max(revision)";
$query[] = "        FROM";
$query[] = "            m_moldreport";
$query[] = "        WHERE";
$query[] = "            deleteflag = false";
$query[] = "        GROUP BY";
$query[] = "            moldreportid";
$query[] = "    )";
$query[] = "AND (mr.moldreportid, mr.revision) in";
$query[] = "    (";
$query[] = "        SELECT";
$query[] = "              moldreportid";
$query[] = "            , max(revision)";
$query[] = "        FROM";
$query[] = "            t_moldreportdetail";
$query[] = "        WHERE";
$query[] = "            deleteflag = false";
$query[] = "        GROUP BY";
$query[] = "            moldreportid";
$query[] = "    )";

// ユーティリティのインスタンス取得
$utilBussinesscode = UtilBussinesscode::getInstance();
$utilCompany = UtilCompany::getInstance();
$utilGroup = UtilGroup::getInstance();
$utilUser = UtilUser::getInstance();

// 検索項目のキーを小文字に変換
$searchColumns = array_change_key_case($searchColumns, CASE_LOWER);
// 検索値のキーを小文字に変換
$searchValue = array_change_key_case($searchValue, CASE_LOWER);
$from = array_change_key_case($from, CASE_LOWER);
$to = array_change_key_case($to, CASE_LOWER);

// 帳票区分
if (array_key_exists(TableMoldReport::ReportCategory, $searchColumns) &&
	array_key_exists(TableMoldReport::ReportCategory, $searchValue))
{
	// 業務コードマスタ上に存在する値の場合
	if($utilBussinesscode->getDescription('帳票区分', $searchValue[TableMoldReport::ReportCategory], true))
	{
		$query[] = "AND mr.reportcategory = '".$searchValue[TableMoldReport::ReportCategory]."'";
	}
}

// 帳票ステータス
if (array_key_exists(TableMoldReport::Status, $searchColumns) &&
	array_key_exists(TableMoldReport::Status, $searchValue))
{
	// 業務コードマスタ上に存在する値の場合
	if($utilBussinesscode->getDescription('帳票ステータス', $searchValue[TableMoldReport::Status], true))
	{
		$query[] = "AND mr.status = '".$searchValue[TableMoldReport::Status]."'";
	}
}

// 依頼日
if (array_key_exists(TableMoldReport::RequestDate, $searchColumns) &&
	array_key_exists(TableMoldReport::RequestDate, $from) &&
	array_key_exists(TableMoldReport::RequestDate, $to) &&
	UtilValidation::checkDateFormatYMD($from[TableMoldReport::RequestDate]) &&
	UtilValidation::checkDateFormatYMD($to[TableMoldReport::RequestDate]))
{
	$query[] = "AND mr.requestdate".
					" between '".$from[TableMoldReport::RequestDate]."'".
					" AND "."'".$to[TableMoldReport::RequestDate]."'";
}

// 金型帳票ID
if (array_key_exists(TableMoldReport::MoldReportId, $searchColumns) &&
	array_key_exists(TableMoldReport::MoldReportId, $from) &&
	array_key_exists(TableMoldReport::MoldReportId, $to))
{
	$query[] = "AND mr.moldreportid".
					" between '".pg_escape_string($from[TableMoldReport::MoldReportId])."'".
					" AND "."'".pg_escape_string($to[TableMoldReport::MoldReportId])."'";
}

// 製品コード
if (array_key_exists(TableMoldReport::ProductCode, $searchColumns) &&
	array_key_exists(TableMoldReport::ProductCode, $from) &&
	array_key_exists(TableMoldReport::ProductCode, $to))
{
	$query[] = "AND mr.productcode".
					" between '".pg_escape_string($from[TableMoldReport::ProductCode])."'".
					" AND "."'".pg_escape_string($to[TableMoldReport::ProductCode])."'";
}

// 製品名称
if (array_key_exists("strproductname", $searchColumns) &&
	array_key_exists("strproductname", $searchValue))
{
	$query[] = "AND mp.strproductname like '%".pg_escape_string($searchValue["strproductname"])."%'";
}
// 製品名称(英語)
if (array_key_exists("strproductenglishname", $searchColumns) &&
	array_key_exists("strproductenglishname", $searchValue))
{
	$query[] = "AND mp.strproductenglishname like '%".pg_escape_string($searchValue["strproductenglishname"])."%'";
}

// 顧客品番
if (array_key_exists(TableMoldReport::GoodsCode, $searchColumns) &&
	array_key_exists(TableMoldReport::GoodsCode, $searchValue))
{
	$query[] = "AND mr.goodscode = '".pg_escape_string($searchValue[TableMoldReport::GoodsCode])."'";
}

// 金型NO
if (array_key_exists("moldno", $searchColumns) &&
	array_key_exists("choosenmoldlist", $searchValue) &&
	count($searchValue["choosenmoldlist"]))
{
	$query[] = "AND (";

	// 金型件数分走査
	foreach ($searchValue["choosenmoldlist"] as $index => $moldno)
	{
		$query[] = "        mrd.moldno SIMILAR TO '%".pg_escape_string($moldno)."%' OR";
	}

	// 末尾のカンマを削除
	$query[] = rtrim(array_pop($query), 'OR');
	$query[] = "    )";
}

// 保管工場
if (array_key_exists(TableMoldReport::SourceFactory, $searchColumns) &&
	array_key_exists(TableMoldReport::SourceFactory, $searchValue))
{
	// 表示会社コードを基に会社コードを索引
	if ($companyCode = $utilCompany->selectCompanyCodeByDisplayCompanyCode($searchValue[TableMoldReport::SourceFactory], false))
	{
		$query[] = "AND mr.sourcefactory = '".$companyCode."'";
	}
}

// 移動先工場
if (array_key_exists(TableMoldReport::DestinationFactory, $searchColumns) &&
	array_key_exists(TableMoldReport::DestinationFactory, $searchValue))
{
	// 表示会社コードを基に会社コードを索引
	if ($companyCode = $utilCompany->selectCompanyCodeByDisplayCompanyCode($searchValue[TableMoldReport::DestinationFactory], false))
	{
		$query[] = "AND mr.destinationfactory = '".$companyCode."'";
	}
}

// 依頼区分
if (array_key_exists(TableMoldReport::RequestCategory, $searchColumns) &&
	array_key_exists(TableMoldReport::RequestCategory, $searchValue))
{
	// 業務コードマスタ上に存在する値の場合
	if($utilBussinesscode->getDescription('依頼区分', $searchValue[TableMoldReport::RequestCategory], true))
	{
		$query[] = "AND mr.requestcategory = '".$searchValue[TableMoldReport::RequestCategory]."'";
	}
}

// 希望日
if (array_key_exists(TableMoldReport::ActionRequestDate, $searchColumns) &&
	array_key_exists(TableMoldReport::ActionRequestDate, $from) &&
	array_key_exists(TableMoldReport::ActionRequestDate, $to) &&
	UtilValidation::checkDateFormatYMD($from[TableMoldReport::ActionRequestDate]) &&
	UtilValidation::checkDateFormatYMD($to[TableMoldReport::ActionRequestDate]))
{
	$query[] = "AND mr.actionrequestdate".
					" between '".$from[TableMoldReport::ActionRequestDate]."'".
					" AND "."'".$to[TableMoldReport::ActionRequestDate]."'";
}

// 移動方法
if (array_key_exists(TableMoldReport::TransferMethod, $searchColumns) &&
	array_key_exists(TableMoldReport::TransferMethod, $searchValue))
{
	// 業務コードマスタ上に存在する値の場合
	if($utilBussinesscode->getDescription('移動方法', $searchValue[TableMoldReport::TransferMethod], true))
	{
		$query[] = "AND mr.transfermethod = '".$searchValue[TableMoldReport::TransferMethod]."'";
	}
}

// 指示区分
if (array_key_exists(TableMoldReport::InstructionCategory, $searchColumns) &&
	array_key_exists(TableMoldReport::InstructionCategory, $searchValue))
{
	// 業務コードマスタ上に存在する値の場合
	if($utilBussinesscode->getDescription('指示区分', $searchValue[TableMoldReport::InstructionCategory], true))
	{
		$query[] = "AND mr.instructioncategory = '".$searchValue[TableMoldReport::InstructionCategory]."'";
	}
}

// 事業部(顧客)
if (array_key_exists(TableMoldReport::CustomerCode, $searchColumns) &&
	array_key_exists(TableMoldReport::CustomerCode, $searchValue))
{
	// 表示会社コードを基に会社コードを索引
	if ($companyCode = $utilCompany->selectCompanyCodeByDisplayCompanyCode($searchValue[TableMoldReport::CustomerCode], false))
	{
		$query[] = "AND mr.customercode = '".$companyCode."'";
	}
}
// KWG担当部署
if (array_key_exists(TableMoldReport::KuwagataGroupCode, $searchColumns) &&
	array_key_exists(TableMoldReport::KuwagataGroupCode, $searchValue))
{
	// 表示会社コードを基に会社コードを索引
	if ($groupCode = $utilGroup->selectGroupCodeByDisplayGroupCode($searchValue[TableMoldReport::KuwagataGroupCode], false))
	{
		$query[] = "AND mr.kuwagatagroupcode = '".$groupCode."'";
	}
}

// KWG担当者
if (array_key_exists(TableMoldReport::KuwagataUserCode, $searchColumns) &&
	array_key_exists(TableMoldReport::KuwagataUserCode, $searchValue))
{
	// 表示会社コードを基に会社コードを索引
	if ($userCode = $utilUser->selectUserCodeByDisplayUserCode($searchValue[TableMoldReport::KuwagataUserCode], false))
	{
		$query[] = "AND mr.kuwagatausercode = '".$userCode."'";
	}
}

// 生産後の処理
if (array_key_exists(TableMoldReport::FinalKeep, $searchColumns) &&
	array_key_exists(TableMoldReport::FinalKeep, $searchValue))
{
	// 業務コードマスタ上に存在する値の場合
	if($utilBussinesscode->getDescription('生産後の処理', $searchValue[TableMoldReport::FinalKeep], true))
	{
		$query[] = "AND mr.finalkeep = '".$searchValue[TableMoldReport::FinalKeep]."'";
	}
}

// 返却予定日
if (array_key_exists(TableMoldReport::ReturnSchedule, $searchColumns) &&
	array_key_exists(TableMoldReport::ReturnSchedule, $from) &&
	array_key_exists(TableMoldReport::ReturnSchedule, $to) &&
	UtilValidation::checkDateFormatYMD($from[TableMoldReport::ReturnSchedule]) &&
	UtilValidation::checkDateFormatYMD($to[TableMoldReport::ReturnSchedule]))
{
	$query[] = "AND mr.returnschedule".
				" between '".$from[TableMoldReport::ReturnSchedule]."'".
				" AND "."'".$to[TableMoldReport::ReturnSchedule]."'";
}

// 登録日
if (array_key_exists(TableMoldReport::Created, $searchColumns) &&
	array_key_exists(TableMoldReport::Created, $from) &&
	array_key_exists(TableMoldReport::Created, $to) &&
	UtilValidation::checkDateFormatYMD($from[TableMoldReport::Created]) &&
	UtilValidation::checkDateFormatYMD($to[TableMoldReport::Created]))
{
	$query[] = "AND mr.created".
				" between '".$from[TableMoldReport::Created]." 00:00:00'".
				" AND "."'".$to[TableMoldReport::Created]." 23:59:59.99999'";
}

// 登録者
if (array_key_exists(TableMoldReport::CreateBy, $searchColumns) &&
	array_key_exists(TableMoldReport::CreateBy, $searchValue))
{
	// 表示会社コードを基に会社コードを索引
	if ($userCode = $utilUser->selectUserCodeByDisplayUserCode($searchValue[TableMoldReport::CreateBy], false))
	{
		$query[] = "AND mr.createby = '".$userCode."'";
	}
}

// 更新日
if (array_key_exists(TableMoldReport::Updated, $searchColumns) &&
	array_key_exists(TableMoldReport::Updated, $from) &&
	array_key_exists(TableMoldReport::Updated, $to) &&
	UtilValidation::checkDateFormatYMD($from[TableMoldReport::Updated]) &&
	UtilValidation::checkDateFormatYMD($to[TableMoldReport::Updated]))
{
	$query[] = "AND mr.updated".
				" between '".$from[TableMoldReport::Updated]." 00:00:00'".
				" AND "."'".$to[TableMoldReport::Updated]." 23:59:59.99999'";
}

// 更新者
if (array_key_exists(TableMoldReport::UpdateBy, $searchColumns) &&
	array_key_exists(TableMoldReport::UpdateBy, $searchValue))
{
	// 表示会社コードを基に会社コードを索引
	if ($userCode = $utilUser->selectUserCodeByDisplayUserCode($searchValue[TableMoldReport::UpdateBy], false))
	{
		$query[] = "AND mr.updateby = '".$userCode."'";
	}
}

$query[] = "ORDER BY";
$query[] = "      mr.moldreportid";
$query[] = "    , mr.revision";

// クエリを平易な文字列に変換
$query = implode("\n",$query);

// クエリ実行
$lngResultID = pg_query($query);

// 検索結果が得られなかった場合
if (!pg_num_rows($lngResultID))
{
	// 該当帳票データなし
	$strMessage = fncOutputError(9064, DEF_WARNING, "" ,FALSE, "", $objDB );

	// [lngLanguageCode]書き出し
	$aryHtml["lngLanguageCode"] = $aryData["lngLanguageCode"];

	// [strErrorMessage]書き出し
	$aryHtml["strErrorMessage"] = $strMessage;

	// テンプレート読み込み
	$objTemplate = new clsTemplate();
	$objTemplate->getTemplate( "/result/error/parts.tmpl" );

	// テンプレート生成
	$objTemplate->replace( $aryHtml );
	$objTemplate->complete();

	// HTML出力
	echo $objTemplate->strTemplate;
	exit;
}

// 検索結果連想配列を取得
$records = pg_fetch_all($lngResultID);

// テンプレート読み込み
$objTemplate = new clsTemplate ();
$objTemplate->getTemplate ( "/mr/search/mr_search_result.html" );

// 検索結果テーブル生成の為DOMDocumentを使用
$doc = new DOMDocument();
// パースエラー抑制
libxml_use_internal_errors(true);
// DOMパース
$doc->loadHTML(mb_convert_encoding($objTemplate->strTemplate, "utf8", "eucjp-win"));
// パースエラークリア
libxml_clear_errors();
// パースエラー抑制解除
libxml_use_internal_errors(false);

// 検索結果テーブルの取得
$table = $doc->getElementById("result");
$thead = $table->getElementsByTagName("thead")->item(0);
$tbody = $table->getElementsByTagName("tbody")->item(0);

// キー文字列を小文字に変換
$displayColumns = array_change_key_case($displayColumns, CASE_LOWER);

// カラムの表示順を示す配列を取得
$columnOrder = UtilSearchForm::getColumnOrderForMoldReport();

// -------------------------------------------------------
// 各種ボタン表示チェック/権限チェック
// -------------------------------------------------------
// 詳細カラムを表示
$existsDetail = array_key_exists("detail", $displayColumns);
// 修正カラムを表示
$existsModify = array_key_exists("modify", $displayColumns);
// プレビューを表示
$existsPreview = array_key_exists("preview", $displayColumns);
// 削除カラムを表示
$existsDelete = array_key_exists("delete", $displayColumns);

// 詳細ボタンを表示
$allowedDetail = fncCheckAuthority( DEF_FUNCTION_MR3, $objAuth );
// 修正ボタンを表示
$allowedModify = fncCheckAuthority( DEF_FUNCTION_MR4, $objAuth );
// 削除カラムを表示
$allowedDelete = fncCheckAuthority( DEF_FUNCTION_MR5, $objAuth );

// -------------------------------------------------------
// テーブルヘッダ作成
// -------------------------------------------------------
// thead > tr要素作成
$trHead = $doc->createElement("tr");

// クリップボード除外対象クラス
$exclude = "exclude-in-clip-board-target";

// 項番カラム
$thIndex = $doc->createElement("th");
$thIndex->setAttribute("class", $exclude);
// コピーボタン
$imgCopy = $doc->createElement("img");
$imgCopy->setAttribute("src", "/mold/img/copy_off_bt.gif");
$imgCopy->setAttribute("class", "copy button");
// 項番カラム > コピーボタン
$thIndex->appendChild($imgCopy);
// ヘッダに追加
$trHead->appendChild($thIndex);

// 詳細を表示
if($existsDetail)
{
	// 詳細カラム
	$thDetail = $doc->createElement("th", toUTF8("詳細"));
	$thDetail->setAttribute("class", $exclude);
	// ヘッダに追加
	$trHead->appendChild($thDetail);
}

// 修正項目を表示
if($existsModify)
{
	// 修正カラム
	$thModify = $doc->createElement("th", toUTF8("修正"));
	$thModify->setAttribute("class", $exclude);
	// ヘッダに追加
	$trHead->appendChild($thModify);
}

// COPY/プレビュー項目を表示
if ($existsPreview)
{
	// COPYカラム
	$thPreview = $doc->createElement("th", toUTF8("COPY"));
	$thPreview->setAttribute("class", $exclude);
	// ヘッダに追加
	$trHead->appendChild($thPreview);

	// プレビューカラム
	$thPreview = $doc->createElement("th", toUTF8("プレビュー"));
	$thPreview->setAttribute("class", $exclude);
	// ヘッダに追加
	$trHead->appendChild($thPreview);
}

// TODO 要リファクタリング
// 指定されたテーブル項目のカラムを作成する
foreach($columnOrder as $columnName)
{
	// 表示対象のカラムの場合
	if (array_key_exists($columnName, $displayColumns))
	{
		// 項目別に表示テキストを設定
		switch ($columnName)
		{
			case TableMoldReport::MoldReportId :
				$th = $doc->createElement("th", toUTF8("金型帳票ID"));
				$trHead->appendChild($th);
				break;
			case TableMoldReport::Revision :
				$th = $doc->createElement("th", toUTF8("リビジョン"));
				$trHead->appendChild($th);
				break;
			case TableMoldReport::ReportCategory :
				$th = $doc->createElement("th", toUTF8("帳票区分"));
				$trHead->appendChild($th);
				break;
			case TableMoldReport::Status :
				$th = $doc->createElement("th", toUTF8("帳票ステータス"));
				$trHead->appendChild($th);
				break;
			case TableMoldReport::RequestDate :
				$th = $doc->createElement("th", toUTF8("依頼日"));
				$trHead->appendChild($th);
				break;
			case TableMoldReport::ProductCode :
				$th = $doc->createElement("th", toUTF8("製品コード"));
				$trHead->appendChild($th);
				break;
			case "strproductname" :
				$th = $doc->createElement("th", toUTF8("製品名称"));
				$trHead->appendChild($th);
				break;
			case "strproductenglishname" :
				$th = $doc->createElement("th", toUTF8("製品名称(英語)"));
				$trHead->appendChild($th);
				break;
			case TableMoldReport::GoodsCode :
				$th = $doc->createElement("th", toUTF8("顧客品番(商品コード)"));
				$trHead->appendChild($th);
				break;
			case TableMoldReport::RequestCategory :
				$th = $doc->createElement("th", toUTF8("依頼区分"));
				$trHead->appendChild($th);
				break;
			case TableMoldReport::ActionRequestDate :
				$th = $doc->createElement("th", toUTF8("希望日"));
				$trHead->appendChild($th);
				break;
			case TableMoldReport::ActionDate :
				$th = $doc->createElement("th", toUTF8("実施日"));
				$trHead->appendChild($th);
				break;
			case TableMoldReport::TransferMethod :
				$th = $doc->createElement("th", toUTF8("移動方法"));
				$trHead->appendChild($th);
				break;
			case TableMoldReport::SourceFactory :
				$th = $doc->createElement("th", toUTF8("保管元工場"));
				$trHead->appendChild($th);
				break;
			case TableMoldReport::DestinationFactory :
				$th = $doc->createElement("th", toUTF8("移動先工場"));
				$trHead->appendChild($th);
				break;
			case TableMoldReport::InstructionCategory :
				$th = $doc->createElement("th", toUTF8("指示区分"));
				$trHead->appendChild($th);
				break;
			case TableMoldReport::CustomerCode :
				$th = $doc->createElement("th", toUTF8("事業部(顧客)"));
				$trHead->appendChild($th);
				break;
			case TableMoldReport::KuwagataGroupCode :
				$th = $doc->createElement("th", toUTF8("担当部署"));
				$trHead->appendChild($th);
				break;
			case TableMoldReport::KuwagataUserCode :
				$th = $doc->createElement("th", toUTF8("担当者"));
				$trHead->appendChild($th);
				break;
			case TableMoldReport::Note :
				$th = $doc->createElement("th", toUTF8("その他"));
				$trHead->appendChild($th);
				break;
			case TableMoldReport::FinalKeep :
				$th = $doc->createElement("th", toUTF8("生産後の処理"));
				$trHead->appendChild($th);
				break;
			case TableMoldReport::ReturnSchedule :
				$th = $doc->createElement("th", toUTF8("返却予定日"));
				$trHead->appendChild($th);
				break;
			case TableMoldReport::MarginalNote :
				$th = $doc->createElement("th", toUTF8("欄外備考"));
				$trHead->appendChild($th);
				break;
			case TableMoldReport::Created :
				$th = $doc->createElement("th", toUTF8("登録日"));
				$trHead->appendChild($th);
				break;
			case TableMoldReport::CreateBy :
				$th = $doc->createElement("th", toUTF8("登録者"));
				$trHead->appendChild($th);
				break;
			case TableMoldReport::Updated :
				$th = $doc->createElement("th", toUTF8("更新日"));
				$trHead->appendChild($th);
				break;
			case TableMoldReport::UpdateBy :
				$th = $doc->createElement("th", toUTF8("更新者"));
				$trHead->appendChild($th);
				break;
			case TableMoldReport::Version :
				$th = $doc->createElement("th", toUTF8("バージョン"));
				$trHead->appendChild($th);
				break;
			case TableMoldReport::DeleteFlag :
				$th = $doc->createElement("th", toUTF8("削除フラグ"));
				$trHead->appendChild($th);
				break;
			case TableMoldReportDetail::MoldNo :
				$th = $doc->createElement("th", toUTF8("金型NO"));
				$trHead->appendChild($th);
				break;
		}
	}
}

// 削除項目を表示
if($existsDelete)
{
	// 削除カラム
	$thDelete = $doc->createElement("th", toUTF8("削除"));
	$thDelete->setAttribute("class", $exclude);
	// ヘッダに追加
	$trHead->appendChild($thDelete);
}

// thead > tr
$thead->appendChild($trHead);
// -------------------------------------------------------
// テーブルセル作成
// -------------------------------------------------------
// 検索結果件数分走査
foreach ($records as $i => $record)
{
	$index = $i + 1;

	// tbody > tr要素作成
	$trBody = $doc->createElement("tr");

	// 項番
	$tdIndex = $doc->createElement("td", $index);
	$tdIndex->setAttribute("class", $exclude);
	$trBody->appendChild($tdIndex);

	// 詳細を表示
	if($existsDetail)
	{
		// 詳細セル
		$tdDetail = $doc->createElement("td");
		$tdDetail->setAttribute("class", $exclude);
		// 詳細ボタン
		$imgDetail = $doc->createElement("img");
		$imgDetail->setAttribute("src", "/mold/img/detail_off_bt.gif");
		$imgDetail->setAttribute("id", $record[TableMoldReport::MoldReportId]);
		$imgDetail->setAttribute("revision", $record[TableMoldReport::Revision]);
		$imgDetail->setAttribute("version", $record[TableMoldReport::Version]);
		$imgDetail->setAttribute("class", "detail button");
		// td > img
		$tdDetail->appendChild($imgDetail);
		// tr > td
		$trBody->appendChild($tdDetail);
	}

	// 修正項目を表示
	if($existsModify)
	{
		// 修正セル
		$tdModify = $doc->createElement("td");
		$tdModify->setAttribute("class", $exclude);
		// 修正ボタン
		$imgModify = $doc->createElement("img");
		$imgModify->setAttribute("src", "/mold/img/renew_off_bt.gif");
		$imgModify->setAttribute("id", $record[TableMoldReport::MoldReportId]);
		$imgModify->setAttribute("revision", $record[TableMoldReport::Revision]);
		$imgModify->setAttribute("version", $record[TableMoldReport::Version]);
		$imgModify->setAttribute("class", "modify button");
		// td > img
		$tdModify->appendChild($imgModify);
		// tr > td
		$trBody->appendChild($tdModify);
	}

	// COPY/プレビュー項目を表示
	if ($existsPreview)
	{
		// COPYセル
		$tdCopy = $doc->createElement("td");
		$tdCopy->setAttribute("class", $exclude);
		// COPYボタン
		$imgCopy = $doc->createElement("img");
		$imgCopy->setAttribute("src", "/mold/img/copybig_off_bt.gif");
		$imgCopy->setAttribute("id", $record[TableMoldReport::MoldReportId]);
		$imgCopy->setAttribute("revision", $record[TableMoldReport::Revision]);
		$imgCopy->setAttribute("version", $record[TableMoldReport::Version]);
		$imgCopy->setAttribute("class", "copy-preview button");
		// td > img
		$tdCopy->appendChild($imgCopy);
		// tr > td
		$trBody->appendChild($tdCopy);

		// プレビューセル
		$tdPreview = $doc->createElement("td");
		$tdPreview->setAttribute("class", $exclude);
		// プレビューボタン
		$imgPreview = $doc->createElement("img");
		$imgPreview->setAttribute("src", "/mold/img/preview_off_bt.gif");
		$imgPreview->setAttribute("id", $record[TableMoldReport::MoldReportId]);
		$imgPreview->setAttribute("revision", $record[TableMoldReport::Revision]);
		$imgPreview->setAttribute("version", $record[TableMoldReport::Version]);
		$imgPreview->setAttribute("class", "preview button");
		// td > img
		$tdPreview->appendChild($imgPreview);
		// tr > td
		$trBody->appendChild($tdPreview);
	}

	// TODO 要リファクタリング
	// 指定されたテーブル項目のセルを作成する
	foreach($columnOrder as $columnName)
	{
		// 表示対象のカラムの場合
		if (array_key_exists($columnName, $displayColumns))
		{
			// 項目別に表示テキストを設定
			switch ($columnName)
			{
				case TableMoldReport::MoldReportId : // 金型帳票ID
					$td = $doc->createElement("td", $record[TableMoldReport::MoldReportId]);
					$trBody->appendChild($td);
					break;
				case TableMoldReport::Revision : // リビジョン
					$td = $doc->createElement("td", $record[TableMoldReport::Revision]);
					$trBody->appendChild($td);
					break;
				case TableMoldReport::ReportCategory : // 帳票区分
					$record[TableMoldReport::ReportCategory] ?
						  $td = $doc->createElement("td", toUTF8($utilBussinesscode->getDescription("帳票区分", $record[TableMoldReport::ReportCategory])))
						: $td = $doc->createElement("td");
					$trBody->appendChild($td);
					break;
				case TableMoldReport::Status : // 帳票ステータス
					$record[TableMoldReport::Status] ?
						  $td = $doc->createElement("td", toUTF8($utilBussinesscode->getDescription("帳票ステータス", $record[TableMoldReport::Status])))
						: $td = $doc->createElement("td");
					$trBody->appendChild($td);
					break;
				case TableMoldReport::RequestDate : // 依頼日
					$td = $doc->createElement("td", $record[TableMoldReport::RequestDate]);
					$trBody->appendChild($td);
					break;
				case TableMoldReport::ProductCode : // 製品コード
					$td = $doc->createElement("td", $record[TableMoldReport::ProductCode]);
					$trBody->appendChild($td);
					break;
				case "strproductname" : // 製品名称
					$td = $doc->createElement("td", toUTF8($record["strproductname"]));
					$trBody->appendChild($td);
					break;
				case "strproductenglishname" : // 製品名称(英語)
					$td = $doc->createElement("td", toUTF8($record["strproductenglishname"]));
					$trBody->appendChild($td);
					break;
				case TableMoldReport::GoodsCode : // 顧客品番(商品コード)
					$td = $doc->createElement("td", toUTF8($record[TableMoldReport::GoodsCode]));
					$trBody->appendChild($td);
					break;
				case TableMoldReport::RequestCategory : // 依頼区分
					$record[TableMoldReport::RequestCategory] ?
						  $td = $doc->createElement("td", toUTF8($utilBussinesscode->getDescription("依頼区分", $record[TableMoldReport::RequestCategory])))
						: $td = $doc->createElement("td");
					$trBody->appendChild($td);
					break;
				case TableMoldReport::ActionRequestDate : // 希望日
					$td = $doc->createElement("td", $record[TableMoldReport::ActionRequestDate]);
					$trBody->appendChild($td);
					break;
				case TableMoldReport::ActionDate : // 実施日
					$td = $doc->createElement("td", $record[TableMoldReport::ActionDate]);
					$trBody->appendChild($td);
					break;
				case TableMoldReport::TransferMethod : // 移動方法
					$record[TableMoldReport::TransferMethod] ?
						$td = $doc->createElement("td", toUTF8($utilBussinesscode->getDescription("移動方法", $record[TableMoldReport::TransferMethod])))
						: $td = $doc->createElement("td");
					$trBody->appendChild($td);
					break;
				case TableMoldReport::SourceFactory : // 保管元工場
					if ($record[TableMoldReport::SourceFactory] || $record[TableMoldHistory::SourceFactory] === "0")
					{
						$displayCode = $utilCompany->selectDisplayCodeByCompanyCode($record[TableMoldReport::SourceFactory]);
						$displayName = $utilCompany->selectDisplayNameByCompanyCode($record[TableMoldReport::SourceFactory]);
						$textContent = "[".$displayCode."]"." ".$displayName;
						$td = $doc->createElement("td", toUTF8($textContent));
					}
					else
					{
						$td = $doc->createElement("td");
					}
					$trBody->appendChild($td);
					break;
				case TableMoldReport::DestinationFactory : // 移動先工場
					if ($record[TableMoldReport::DestinationFactory] || $record[TableMoldHistory::DestinationFactory] === "0")
					{
						$displayCode = $utilCompany->selectDisplayCodeByCompanyCode($record[TableMoldReport::DestinationFactory]);
						$displayName = $utilCompany->selectDisplayNameByCompanyCode($record[TableMoldReport::DestinationFactory]);
						$textContent = "[".$displayCode."]"." ".$displayName;
						$td = $doc->createElement("td", toUTF8($textContent));
					}
					else
					{
						$td = $doc->createElement("td");
					}
					$trBody->appendChild($td);
					break;
				case TableMoldReport::InstructionCategory : // 指示区分
					$record[TableMoldReport::InstructionCategory] ?
						  $td = $doc->createElement("td", toUTF8($utilBussinesscode->getDescription("指示区分", $record[TableMoldReport::InstructionCategory])))
						: $td = $doc->createElement("td");
					$trBody->appendChild($td);
					break;
				case TableMoldReport::CustomerCode : // 事業部(顧客)
					if ($record[TableMoldReport::CustomerCode])
					{
						$displayCode = $utilCompany->selectDisplayCodeByCompanyCode($record[TableMoldReport::CustomerCode]);
						$displayName = $utilCompany->selectDisplayNameByCompanyCode($record[TableMoldReport::CustomerCode]);
						$textContent = "[".$displayCode."]"." ".$displayName;
						$td = $doc->createElement("td", toUTF8($textContent));
					}
					else
					{
						$td = $doc->createElement("td");
					}
					$trBody->appendChild($td);
					break;
				case TableMoldReport::KuwagataGroupCode : // 担当部署
					if ($record[TableMoldReport::KuwagataGroupCode])
					{
						$displayCode = $utilGroup->selectDisplayCodeByGroupCode($record[TableMoldReport::KuwagataGroupCode]);
						$displayName = $utilGroup->selectDisplayNameByGroupCode($record[TableMoldReport::KuwagataGroupCode]);
						$textContent = "[".$displayCode."]"." ".$displayName;
						$td = $doc->createElement("td", toUTF8($textContent));
					}
					else
					{
						$td = $doc->createElement("td");
					}
					$trBody->appendChild($td);
					break;
				case TableMoldReport::KuwagataUserCode : // 担当者
					if ($record[TableMoldReport::KuwagataUserCode])
					{
						$displayCode = $utilUser->selectDisplayCodeByUserCode($record[TableMoldReport::KuwagataUserCode]);
						$displayName = $utilUser->selectDisplayNameByUserCode($record[TableMoldReport::KuwagataUserCode]);
						$textContent = "[".$displayCode."]"." ".$displayName;
						$td = $doc->createElement("td", toUTF8($textContent));
					}
					else
					{
						$td = $doc->createElement("td");
					}
					$trBody->appendChild($td);
					break;
				case TableMoldReport::Note : // その他
					$td = $doc->createElement("td", $record[TableMoldReport::Note]);
					$trBody->appendChild($td);
					break;
				case TableMoldReport::FinalKeep : // 生産後の処理
					$record[TableMoldReport::FinalKeep] ?
						  $td = $doc->createElement("td", toUTF8($utilBussinesscode->getDescription("生産後の処理", $record[TableMoldReport::FinalKeep])))
						: $td = $doc->createElement("td", $record[TableMoldReport::FinalKeep]);
					$trBody->appendChild($td);
					break;
				case TableMoldReport::ReturnSchedule : // 返却予定日
					$td = $doc->createElement("td", $record[TableMoldReport::ReturnSchedule]);
					$trBody->appendChild($td);
					break;
				case TableMoldReport::MarginalNote : // 欄外備考
					$td = $doc->createElement("td", $record[TableMoldReport::MarginalNote]);
					$trBody->appendChild($td);
					break;
				case TableMoldReport::Created : // 作成日
					$td = $doc->createElement("td", $record[TableMoldReport::Created]);
					$trBody->appendChild($td);
					break;
				case TableMoldReport::CreateBy : // 作成者
					if ($record[TableMoldReport::CreateBy])
					{
						$displayCode = $utilUser->selectDisplayCodeByUserCode($record[TableMoldReport::CreateBy]);
						$displayName = $utilUser->selectDisplayNameByUserCode($record[TableMoldReport::CreateBy]);
						$textContent = "[".$displayCode."]"." ".$displayName;
						$td = $doc->createElement("td", toUTF8($textContent));
					}
					else
					{
						$td = $doc->createElement("td");
					}
					$trBody->appendChild($td);
					break;
				case TableMoldReport::Updated : // 更新日時
					$td = $doc->createElement("td", $record[TableMoldReport::Updated]);
					$trBody->appendChild($td);
					break;
				case TableMoldReport::UpdateBy : // 更新者
					if ($record[TableMoldReport::UpdateBy])
					{
						$displayCode = $utilUser->selectDisplayCodeByUserCode($record[TableMoldReport::UpdateBy]);
						$displayName = $utilUser->selectDisplayNameByUserCode($record[TableMoldReport::UpdateBy]);
						$textContent = "[".$displayCode."]"." ".$displayName;
						$td = $doc->createElement("td", toUTF8($textContent));
					}
					else
					{
						$td = $doc->createElement("td");
					}
					$trBody->appendChild($td);
					break;
				case TableMoldReport::Version : // バージョン
					$td = $doc->createElement("td", $record[TableMoldReport::Version]);
					$trBody->appendChild($td);
					break;
				case TableMoldReport::DeleteFlag : // 削除フラグ
					$td = $doc->createElement("td", $record[TableMoldReport::DeleteFlag]);
					$trBody->appendChild($td);
					break;
				case TableMoldReportDetail::MoldNo : // 金型情報
					$td = $doc->createElement("td");
					$td->setAttribute("class", "moldinfo");
					// カンマを<br>要素に置き換える
					foreach (explode(",", $record[TableMoldReportDetail::MoldNo]) as $index => $moldno)
					{
						$td->appendChild($doc->createTextNode(toUTF8($moldno)));
						$td->appendChild($doc->createElement("br"));
					}
					$trBody->appendChild($td);
					break;
			}
		}
	}

	// 削除項目を表示
	if($existsDelete)
	{
		// 削除セル
		$tdDelete = $doc->createElement("td");
		$tdDelete->setAttribute("class", $exclude);
		// 削除ボタン
		$imgDelete = $doc->createElement("img");
		$imgDelete->setAttribute("src", "/mold/img/remove_off_bt.gif");
		$imgDelete->setAttribute("id", $record[TableMoldReport::MoldReportId]);
		$imgDelete->setAttribute("revision", $record[TableMoldReport::Revision]);
		$imgDelete->setAttribute("version", $record[TableMoldReport::Version]);
		$imgDelete->setAttribute("class", "delete button");
		// td > img
		$tdDelete->appendChild($imgDelete);
		// tr > td
		$trBody->appendChild($tdDelete);
	}

	// tbody > tr
	$tbody->appendChild($trBody);
}

// HTML出力
echo $doc->saveHTML();

