<?php

// ----------------------------------------------------------------------------
/**
*       金型履歴管理  検索処理
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

// 言語コードを取得(0->false: 英語, 1->true: 日本語)
$lngLanguageCode = 1;

// セッション確認
$objAuth = fncIsSession( $_REQUEST["strSessionID"], $objAuth, $objDB );

// 1800 金型履歴管理
if ( !fncCheckAuthority( DEF_FUNCTION_MM0, $objAuth ) )
{
	fncOutputError( 9018, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
}

// 1802 金型履歴管理(検索)
if ( !fncCheckAuthority( DEF_FUNCTION_MM2, $objAuth ) )
{
	fncOutputError( 9018, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
}

// フォームデータから各カテゴリの振り分けを行う
$options = UtilSearchForm::extractArrayByOption($_REQUEST);
$isDisplay = UtilSearchForm::extractArrayByIsDisplay($_REQUEST);
$isSearch = UtilSearchForm::extractArrayByIsSearch($_REQUEST);
$from = UtilSearchForm::extractArrayByFrom($_REQUEST);
$to = UtilSearchForm::extractArrayByTo($_REQUEST);
$searchValue = $_REQUEST;

// クエリの組立に使用するフォームデータを抽出
$optionColumns = array();
$searchColumns = array();
$displayColumns = array();
$conditions = array();

// オプション項目の抽出
foreach ($options as $key => $flag)
{
	if ($flag == "on")
	{
		$optionColumns[$key] = $key;
	}
}

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
$query[] = "      tmrr.moldreportid";
$query[] = "    , tmrr.revision";
$query[] = "    , ms.dtmappropriationdate";   // 仕入マスタ.仕入計上日
$query[] = "    , mo.strOrderCode AS strOrderCode";   // 発注マスタ.発注コード
$query[] = "    , '[' || mc.strCompanyDisplayCode || ']' || mc.strCompanyDisplayname AS strCompanyDisplayCode";   // [会社マスタ.表示会社コード] 会社マスタ.表示会社名称
$query[] = "    , mp.strproductcode";            // 製品マスタ.製品コード
$query[] = "    , mp.strproductname";            // 製品マスタ.製品コード(日本語)
$query[] = "    , mp.strproductenglishname";     // 製品マスタ.製品名称(英語)
$query[] = "    , mp.strgoodscode";              // 製品マスタ.顧客品番
$query[] = "    , '[' || mg.strgroupdisplaycode || ']' || mg.strgroupdisplayname as strgroupdisplaycode";   // [グループマスタ.表示グループコード] グループマスタ.表示グループ名
$query[] = "    , '[' || mu.struserdisplaycode || ']' || mu.struserdisplayname as struserdisplaycode";   // [ユーザマスタ.表示ユーザコード] ユーザマスタ.表示ユーザ名
$query[] = "    , tmh.moldno";                // 金型履歴.金型NO
$query[] = "    , tmh.historyno";             // 金型履歴.履歴番号
$query[] = "    , ms.lngProductQuantity";    // 仕入詳細.数量
$query[] = "    , mou.strMonetaryUnitSign || ms.curSubTotalPrice as strMonetaryUnitSign"; // 通貨単位マスタ.通貨単位 || 仕入詳細.サブトータル価格
$query[] = "    , tmh.status";                // 金型履歴.金型ステータス
$query[] = "    , tmh.actiondate";            // 金型履歴.実施日
$query[] = "  , mc_sf.strcompanydisplaycode as strsourfacdisplaycode";
$query[] = "  , mc_sf.strcompanydisplayname as strsourfacdisplayname";
$query[] = "  , tmh.sourcefactory";
$query[] = "  , tmh.destinationfactory";
$query[] = "  , mc_df.strcompanydisplaycode as strdescfacdisplaycode";
$query[] = "  , mc_df.strcompanydisplayname as strdescfacdisplayname";
$query[] = "  , tmh.created ::date";
$query[] = "  , tmh.createby";
$query[] = "  , mu_c.struserdisplaycode as strcreateuserdisplaycode";
$query[] = "  , mu_c.struserdisplayname as strcreateuserdisplayname";
$query[] = "  , tmh.updated ::date";
$query[] = "  , tmh.updateby";
$query[] = "  , mu_u.struserdisplaycode as strupdateuserdisplaycode";
$query[] = "  , mu_u.struserdisplayname as strupdateuserdisplayname";
$query[] = "    , tmh.version";               // 金型履歴.バージョン
$query[] = "    , tmh.deleteflag";            // 金型履歴.削除フラグ
$query[] = "FROM";
$query[] = "    t_moldhistory tmh";
$query[] = "INNER JOIN";
$query[] = "    m_mold mm";
$query[] = "  ON";
$query[] = "    tmh.moldno = mm.moldno";
$query[] = "LEFT OUTER JOIN";
$query[] = "    t_moldreportrelation tmrr";
$query[] = "  ON";
$query[] = "        tmh.moldno = tmrr.moldno";
$query[] = "    AND tmh.historyno = tmrr.historyno";
// $query[] = "----------------------------------------------";
// $query[] = "--  仕入詳細 - 税 ⇔ 製品 - グループ - ユーザ";
// $query[] = "----------------------------------------------";
$query[] = "  LEFT OUTER JOIN ( ";
$query[] = "    select";
$query[] = "      tsd.*";
$query[] = "      , s.dtmappropriationdate";
$query[] = "      , s.lngcustomercompanycode";
$query[] = "      , s.lngmonetaryunitcode ";
$query[] = "    from";
$query[] = "      t_stockdetail tsd ";
$query[] = "      inner join ( ";
$query[] = "        SELECT";
$query[] = "          ims.* ";
$query[] = "        FROM";
$query[] = "          m_stock ims ";
$query[] = "          inner join ( ";
$query[] = "            SELECT";
$query[] = "              MAX(lngRevisionNo) lngrevisionno";
$query[] = "              , strStockCode ";
$query[] = "            FROM";
$query[] = "              m_Stock ";
$query[] = "            WHERE";
$query[] = "              bytInvalidFlag = false ";
$query[] = "            group by";
$query[] = "              strStockCode";
$query[] = "          ) s ";
$query[] = "            on ims.lngrevisionno = s.lngrevisionno ";
$query[] = "            and ims.strstockcode = s.strStockCode ";
$query[] = "        where";
$query[] = "          not exists ( ";
$query[] = "            select";
$query[] = "              s1.strStockCode ";
$query[] = "            from";
$query[] = "              ( ";
$query[] = "                SELECT";
$query[] = "                  min(lngRevisionNo) lngRevisionNo";
$query[] = "                  , strStockCode ";
$query[] = "                FROM";
$query[] = "                  m_Stock ";
$query[] = "                where";
$query[] = "                  bytInvalidFlag = false ";
$query[] = "                group by";
$query[] = "                  strStockCode";
$query[] = "              ) as s1 ";
$query[] = "            where";
$query[] = "              s1.strStockCode = ims.strstockcode AND s1.lngRevisionNo < 0";
$query[] = "          )";
$query[] = "      ) s ";
$query[] = "        on tsd.lngstockno = s.lngstockno ";
$query[] = "        and ( ";
$query[] = "          ( ";
$query[] = "            tsd.lngStockSubjectCode = 433 ";
$query[] = "            AND tsd.lngStockItemCode = 1";
$query[] = "          ) ";
$query[] = "          OR ( ";
$query[] = "            tsd.lngStockSubjectCode = 431 ";
$query[] = "            AND tsd.lngStockItemCode = 8";
$query[] = "          )";
$query[] = "        )";
$query[] = "  ) ms ";
$query[] = "    ON tmh.moldno = ms.strmoldno ";
$query[] = "LEFT JOIN";
$query[] = "    m_tax   mt";
$query[] = "  ON";
$query[] = "    ms.lngtaxcode = mt.lngtaxcode";
$query[] = "  LEFT OUTER JOIN ( ";
$query[] = "    SELECT";
$query[] = "      p.* ";
$query[] = "    FROM";
$query[] = "      m_product p ";
$query[] = "      inner join ( ";
$query[] = "        SELECT";
$query[] = "          MAX(lngproductno) lngproductno";
$query[] = "          , strproductcode ";
$query[] = "        FROM";
$query[] = "          m_product ";
$query[] = "        WHERE";
$query[] = "          bytInvalidFlag = false ";
$query[] = "        group by";
$query[] = "          strproductcode";
$query[] = "      ) p1 ";
$query[] = "        on p.lngproductno = p1.lngproductno ";
$query[] = "    where";
$query[] = "      lngrevisionno >= 0";
$query[] = "  ) mp ";
$query[] = "  ON";
$query[] = "     ms.strproductcode = mp.strproductcode";
$query[] = "  LEFT JOIN m_group mg ";
$query[] = "    ON mp.lnginchargegroupcode = mg.lnggroupcode ";
$query[] = "  LEFT JOIN m_user mu ";
$query[] = "    ON mp.lnginchargeusercode = mu.lngusercode ";
$query[] = "  LEFT JOIN m_user mu_u";
$query[] = "    ON tmh.updateby = mu_u.lngusercode ";
$query[] = "  LEFT JOIN m_user mu_c";
$query[] = "    ON tmh.createby = mu_c.lngusercode ";
$query[] = "  LEFT OUTER JOIN m_order mo ";
$query[] = "    ON ms.lngorderno = mo.lngorderno ";
$query[] = "  LEFT JOIN m_Company mc ";
$query[] = "    ON ms.lngCustomerCompanyCode = mc.lngCompanyCode ";
$query[] = "  LEFT JOIN m_Company mc_sf";
$query[] = "    ON tmh.sourcefactory = mc_sf.lngCompanyCode ";
$query[] = "  LEFT JOIN m_Company mc_df";
$query[] = "    ON tmh.destinationfactory = mc_df.lngCompanyCode ";
$query[] = "  LEFT JOIN m_MonetaryUnit mou ";
$query[] = "    ON ms.lngMonetaryUnitCode = mou.lngMonetaryUnitCode ";
$query[] = "WHERE";// 履歴の全件出力
if (!array_key_exists("IsDetail", $optionColumns))
{
$query[] = "    tmh.deleteflag = false";
}
$query[] = "AND mm.deleteflag = false";

// ユーティリティのインスタンス取得
$utilBussinesscode = UtilBussinesscode::getInstance();
$utilMold = UtilMold::getInstance();
$utilCompany = UtilCompany::getInstance();
$utilGroup = UtilGroup::getInstance();
$utilUser = UtilUser::getInstance();

// 検索項目のキーを小文字に変換
$searchColumns = array_change_key_case($searchColumns, CASE_LOWER);
// 検索値のキーを小文字に変換
$searchValue = array_change_key_case($searchValue, CASE_LOWER);
$from = array_change_key_case($from, CASE_LOWER);
$to = array_change_key_case($to, CASE_LOWER);

// 金型帳票ID
if (array_key_exists(TableMoldReport::MoldReportId, $searchColumns) &&
	array_key_exists(TableMoldReport::MoldReportId, $from) &&
	array_key_exists(TableMoldReport::MoldReportId, $to))
{
	$query[] = "AND tmrr.moldreportid".
			" between '".pg_escape_string($from[TableMoldReport::MoldReportId])."'".
			" AND "."'".pg_escape_string($to[TableMoldReport::MoldReportId])."'";
}

// 計上日
if (array_key_exists("dtmappropriationdate", $searchColumns) &&
	array_key_exists("dtmappropriationdate", $from) &&
	array_key_exists("dtmappropriationdate", $to) &&
	UtilValidation::checkDateFormatYMD($from[TableMoldHistory::Updated]) &&
	UtilValidation::checkDateFormatYMD($to[TableMoldHistory::Updated]))
{
	$query[] = "AND ms.dtmappropriationdate".
				" between '".pg_escape_string($from["dtmappropriationdate"])."'".
				" AND "."'".pg_escape_string($to["dtmappropriationdate"])."'";
}

// 発注コード
if (array_key_exists("strordercode", $searchColumns) &&
	array_key_exists("strordercode", $from) &&
	array_key_exists("strordercode", $to))
{
	$query[] = "AND mo.strordercode".
				" between '".pg_escape_string($from["strordercode"])."'".
				" AND "."'".pg_escape_string($to["strordercode"])."'";
}

// 仕入先
if (array_key_exists("strcompanydisplaycode", $searchColumns) &&
	array_key_exists("strcompanydisplaycode", $searchValue))
{
	$query[] = "AND mc.strcompanydisplaycode = '".pg_escape_string($searchValue["strcompanydisplaycode"])."'";
}

// 製品コード
if (array_key_exists("strproductcode", $searchColumns) &&
	array_key_exists("strproductcode", $from) &&
	array_key_exists("strproductcode", $to))
{
	$query[] = "AND mp.strproductcode".
				" between '".pg_escape_string($from["strproductcode"])."'".
				" AND "."'".pg_escape_string($to["strproductcode"])."'";
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
if (array_key_exists("strgoodscode", $searchColumns) &&
	array_key_exists("strgoodscode", $searchValue))
{
	$query[] = "AND mp.strgoodscode = '".pg_escape_string($searchValue["strgoodscode"])."'";
}

// 担当部署
if (array_key_exists("strgroupdisplaycode", $searchColumns) &&
	array_key_exists("strgroupdisplaycode", $searchValue))
{
	$query[] = "AND mg.strgroupdisplaycode = '".pg_escape_string($searchValue["strgroupdisplaycode"])."'";
}

// 担当者
if (array_key_exists("struserdisplaycode", $searchColumns) &&
	array_key_exists("struserdisplaycode", $searchValue))
{
	$query[] = "AND mu.struserdisplaycode = '".pg_escape_string($searchValue["struserdisplaycode"])."'";
}

// 金型NO
if (array_key_exists("moldno", $searchColumns) &&
	array_key_exists("choosenmoldlist", $searchValue) &&
	count($searchValue["choosenmoldlist"]))
{
	$query[] = "AND tmh.moldno in";
	$query[] = "    (";

	// 金型件数分走査
	foreach ($searchValue["choosenmoldlist"] as $index => $moldno)
	{
		$query[] = "        '".pg_escape_string($moldno)."',";
	}

	// 末尾のカンマを削除
	$query[] = rtrim(array_pop($query), ',');
	$query[] = "    )";
}

// 金型ステータス
if (array_key_exists(TableMoldHistory::Status, $searchColumns) &&
	array_key_exists(TableMoldHistory::Status, $searchValue))
{
	// 業務コードマスタ上に存在する値の場合
	if($utilBussinesscode->getDescription('金型ステータス', $searchValue[TableMoldHistory::Status], true))
	{
		$query[] = "AND tmh.status = '".$searchValue[TableMoldHistory::Status]."'";
	}
}

// 実施日
if (array_key_exists(TableMoldHistory::ActionDate, $searchColumns) &&
	array_key_exists(TableMoldHistory::ActionDate, $from) &&
	array_key_exists(TableMoldHistory::ActionDate, $to) &&
	UtilValidation::checkDateFormatYMD($from[TableMoldHistory::ActionDate]) &&
	UtilValidation::checkDateFormatYMD($to[TableMoldHistory::ActionDate]))
{
	$query[] = "AND tmh.actiondate".
				" between '".$from[TableMoldHistory::ActionDate]."'".
				" AND "."'".$to[TableMoldHistory::ActionDate]."'";
}

// 保管工場
if (array_key_exists(TableMoldHistory::SourceFactory, $searchColumns) &&
	array_key_exists(TableMoldHistory::SourceFactory, $searchValue))
{
	// 表示会社コードを基に会社コードを索引
	if ($companyCode = $utilCompany->selectCompanyCodeByDisplayCompanyCode($searchValue[TableMoldHistory::SourceFactory], false))
	{
		$query[] = "AND tmh.sourcefactory = '".$companyCode."'";
	}
}

// 移動先工場
if (array_key_exists(TableMoldHistory::DestinationFactory, $searchColumns) &&
	array_key_exists(TableMoldHistory::DestinationFactory, $searchValue))
{
	// 表示会社コードを基に会社コードを索引
	if ($companyCode = $utilCompany->selectCompanyCodeByDisplayCompanyCode($searchValue[TableMoldHistory::DestinationFactory], false))
	{
		$query[] = "AND tmh.destinationfactory = '".$companyCode."'";
	}
}

// 登録日
if (array_key_exists(TableMoldHistory::Created, $searchColumns) &&
	array_key_exists(TableMoldHistory::Created, $from) &&
	array_key_exists(TableMoldHistory::Created, $to) &&
	UtilValidation::checkDateFormatYMD($from[TableMoldHistory::Created]) &&
	UtilValidation::checkDateFormatYMD($to[TableMoldHistory::Created]))
{
	$query[] = "AND tmh.created".
				" between '".$from[TableMoldHistory::Created]." 00:00:00'".
				" AND "."'".$to[TableMoldHistory::Created]." 23:59:59.99999'";
}

// 登録者
if (array_key_exists(TableMoldHistory::CreateBy, $searchColumns) &&
	array_key_exists(TableMoldHistory::CreateBy, $searchValue))
{
	// 表示会社コードを基に会社コードを索引
	if ($userCode = $utilUser->selectUserCodeByDisplayUserCode($searchValue[TableMoldHistory::CreateBy], false))
	{
		$query[] = "AND tmh.createby = '".$userCode."'";
	}
}

// 更新日
if (array_key_exists(TableMoldHistory::Updated, $searchColumns) &&
	array_key_exists(TableMoldHistory::Updated, $from) &&
	array_key_exists(TableMoldHistory::Updated, $to) &&
	UtilValidation::checkDateFormatYMD($from[TableMoldHistory::Updated]) &&
	UtilValidation::checkDateFormatYMD($to[TableMoldHistory::Updated]))
{
	$query[] = "AND tmh.updated".
				" between '".$from[TableMoldHistory::Updated]." 00:00:00'".
				" AND "."'".$to[TableMoldHistory::Updated]." 23:59:59.99999'";
}

// 更新者
if (array_key_exists(TableMoldHistory::UpdateBy, $searchColumns) &&
	array_key_exists(TableMoldHistory::UpdateBy, $searchValue))
{
	// 表示会社コードを基に会社コードを索引
	if ($userCode = $utilUser->selectUserCodeByDisplayUserCode($searchValue[TableMoldHistory::UpdateBy], false))
	{
		$query[] = "AND tmh.updateby = '".$userCode."'";
	}
}


$query[] = "ORDER BY";
$query[] = "      mm.productcode";
$query[] = "    , tmh.historyno desc";
$query[] = "    , tmh.moldno";
$query[] = ";";

// クエリを平易な文字列に変換
$query = implode("\n",$query);

// クエリ実行
$lngResultID = pg_query($query);

$lngResultNum = pg_num_rows($lngResultID);

// 検索件数がありの場合
if ($lngResultNum > 0) {
    // 指定数以上の場合エラーメッセージを表示する
    // if ($lngResultNum > DEF_SEARCH_MAX) {
    //     $errorFlag = true;
    //     $lngErrorCode = 9068;
    //     $aryErrorMessage = DEF_SEARCH_MAX;
    // }
} else {
    $errorFlag = true;
    $lngErrorCode = 603;
    $aryErrorMessage = "";
}
if ($errorFlag) {
    // エラー画面の戻り先
    $strReturnPath = "../mm/search/index.php?strSessionID=" . $aryData["strSessionID"];

    $strMessage = fncOutputError($lngErrorCode, DEF_WARNING, $aryErrorMessage, false, $strReturnPath, $objDB);

    // [strErrorMessage]書き出し
    $aryHtml["strErrorMessage"] = $strMessage;

    // テンプレート読み込み
    $objTemplate = new clsTemplate();
    $objTemplate->getTemplate("/result/error/parts.tmpl");

    // テンプレート生成
    $objTemplate->replace($aryHtml);
    $objTemplate->complete();

    // HTML出力
    echo $objTemplate->strTemplate;

    exit;
}

// 検索結果連想配列を取得
$records = pg_fetch_all($lngResultID);

// テンプレート読み込み
$objTemplate = new clsTemplate ();
$objTemplate->getTemplate ( "/mm/search/mm_search_result.html" );

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
$columnOrder = UtilSearchForm::getColumnOrderForMoldHistory();

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
$allowedDetail = fncCheckAuthority( DEF_FUNCTION_MM3, $objAuth );
// 修正ボタンを表示
$allowedModify = fncCheckAuthority( DEF_FUNCTION_MM4, $objAuth );
// 削除カラムを表示
$allowedDelete = fncCheckAuthority( DEF_FUNCTION_MM5, $objAuth );

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
			// 金型帳票関連.金型帳票ID
			case TableMoldReport::MoldReportId:
				$th = $doc->createElement("th", toUTF8("金型帳票ID"));
				$trHead->appendChild($th);
				break;
			// 仕入マスタ.仕入計上日
			case "dtmappropriationdate":
				$th = $doc->createElement("th", toUTF8("計上日"));
				$trHead->appendChild($th);
				break;
			// 発注マスタ.発注コード-発注マスタ.修正コード
			case "strordercode":
				$th = $doc->createElement("th", toUTF8("発注コード"));
				$trHead->appendChild($th);
				break;
			// [会社マスタ.表示会社コード] 会社マスタ.表示会社名称
			case "strcompanydisplaycode":
				$th = $doc->createElement("th", toUTF8("仕入先"));
				$trHead->appendChild($th);
				break;
			// 製品マスタ.製品コード
			case "strproductcode":
				$th = $doc->createElement("th", toUTF8("製品コード"));
				$trHead->appendChild($th);
				break;
			// 製品マスタ.製品コード(日本語)
			case "strproductname":
				$th = $doc->createElement("th", toUTF8("製品名称"));
				$trHead->appendChild($th);
				break;
			// 製品マスタ.製品名称(英語)
			case "strproductenglishname":
				$th = $doc->createElement("th", toUTF8("製品名称(英語)"));
				$trHead->appendChild($th);
				break;
			// 製品マスタ.顧客品番
			case "strgoodscode":
				$th = $doc->createElement("th", toUTF8("顧客品番"));
				$trHead->appendChild($th);
				break;
			// [グループマスタ.表示グループコード] グループマスタ.表示グループ名
			case "strgroupdisplaycode":
				$th = $doc->createElement("th", toUTF8("担当部署"));
				$trHead->appendChild($th);
				break;
			// [ユーザマスタ.表示ユーザコード] ユーザマスタ.表示ユーザ名
			case "struserdisplaycode":
				$th = $doc->createElement("th", toUTF8("担当者"));
				$trHead->appendChild($th);
				break;
			// 金型履歴.金型NO
			case TableMoldHistory::MoldNo:
				$th = $doc->createElement("th", toUTF8("金型NO"));
				$trHead->appendChild($th);
				break;
			// 金型履歴.履歴番号
			case TableMoldHistory::HistoryNo:
				$th = $doc->createElement("th", toUTF8("履歴番号"));
				$trHead->appendChild($th);
				break;
			// 仕入詳細.製品数量
			case "lngproductquantity":
				$th = $doc->createElement("th", toUTF8("数量"));
				$trHead->appendChild($th);
				break;
			// 通貨単位マスタ.通貨単位 || 仕入詳細.税抜金額
			case "strmonetaryunitsign":
				$th = $doc->createElement("th", toUTF8("税抜金額"));
				$trHead->appendChild($th);
				break;
			// 金型履歴.金型ステータス
			case TableMoldHistory::Status:
				$th = $doc->createElement("th", toUTF8("金型ステータス"));
				$trHead->appendChild($th);
				break;
			// 金型履歴.実施日
			case TableMoldHistory::ActionDate:
				$th = $doc->createElement("th", toUTF8("実施日"));
				$trHead->appendChild($th);
				break;
			// 金型履歴.保管工場
			case TableMoldHistory::SourceFactory:
				$th = $doc->createElement("th", toUTF8("保管元工場"));
				$trHead->appendChild($th);
				break;
			// 金型履歴.移動先工場
			case TableMoldHistory::DestinationFactory:
				$th = $doc->createElement("th", toUTF8("移動先工場"));
				$trHead->appendChild($th);
				break;
			// 金型履歴.登録日時
			case TableMoldHistory::Created :
				$th = $doc->createElement("th", toUTF8("登録日"));
				$trHead->appendChild($th);
				break;
			// 金型履歴.登録者
			case TableMoldHistory::CreateBy :
				$th = $doc->createElement("th", toUTF8("登録者"));
				$trHead->appendChild($th);
				break;
			// 金型履歴.更新日時
			case TableMoldHistory::Updated :
				$th = $doc->createElement("th", toUTF8("更新日"));
				$trHead->appendChild($th);
				break;
			// 金型履歴.更新者
			case TableMoldHistory::UpdateBy :
				$th = $doc->createElement("th", toUTF8("更新者"));
				$trHead->appendChild($th);
				break;
			// 金型履歴.バージョン
			case TableMoldHistory::Version :
				$th = $doc->createElement("th", toUTF8("バージョン"));
				$trHead->appendChild($th);
				break;
			// 金型履歴.削除フラグ
			case TableMoldHistory::DeleteFlag :
				$th = $doc->createElement("th", toUTF8("削除フラグ"));
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

		// 詳細ボタンの表示
		if ($allowedDetail)
		{
			// 詳細ボタン
			$imgDetail = $doc->createElement("img");
			$imgDetail->setAttribute("src", "/mold/img/detail_off_bt.gif");
			$imgDetail->setAttribute("id", $record[TableMoldHistory::MoldNo]);
			$imgDetail->setAttribute("historyno", $record[TableMoldHistory::HistoryNo]);
			$imgDetail->setAttribute("version", $record[TableMoldHistory::Version]);
			$imgDetail->setAttribute("class", "detail button");
			// td > img
			$tdDetail->appendChild($imgDetail);
		}
		// tr > td
		$trBody->appendChild($tdDetail);
	}

	// 修正項目を表示
	if($existsModify)
	{
		// 修正セル
		$tdModify = $doc->createElement("td");
		$tdModify->setAttribute("class", $exclude);

		// 修正ボタンの表示
		if ($allowedModify)
		{
			// 修正ボタン
			$imgModify = $doc->createElement("img");
			$imgModify->setAttribute("src", "/mold/img/renew_off_bt.gif");
			$imgModify->setAttribute("id", $record[TableMoldHistory::MoldNo]);
			$imgModify->setAttribute("historyno", $record[TableMoldHistory::HistoryNo]);
			$imgModify->setAttribute("version", $record[TableMoldHistory::Version]);
			$imgModify->setAttribute("class", "modify button");
			// td > img
			$tdModify->appendChild($imgModify);
		}
		// tr > td
		$trBody->appendChild($tdModify);
	}
	// プレビュー項目を表示(履歴はコピー兼用)
	if ($existsPreview)
	{
		// プレビューセル
		$tdPreview = $doc->createElement("td");
		$tdPreview->setAttribute("class", $exclude);

		// 金型帳票関連が取得できた場合チェック
		if($relation = $utilMold->selectMoldReportRelationByHistory($record[TableMoldHistory::MoldNo], $record[TableMoldHistory::HistoryNo]))
		{
			// プレビューボタンを表示
			$imgPreview = $doc->createElement("img");
			$imgPreview->setAttribute("src", "/mold/img/preview_off_bt.gif");
			$imgPreview->setAttribute("id", $relation[0][TableMoldReportRelation::MoldReportId]);
			$imgPreview->setAttribute("revision", $relation[0][TableMoldReportRelation::Revision]);
			$imgPreview->setAttribute("version", $relation[0]["report_version"]);

			// 原本印刷済の場合
			if ($relation[0][TableMoldReport::Printed] == "t")
			{
				// COPY帳票を出力
				$imgPreview->setAttribute("class", "copy-preview button");
			}
			else
			{
				// 原本帳票を出力
				$imgPreview->setAttribute("class", "preview button");
			}

			// td > img
			$tdPreview->appendChild($imgPreview);
		}

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
				// 金型帳票関連.金型帳票ID
				case TableMoldReport::MoldReportId : // 金型帳票ID
					$td = $doc->createElement("td", $record[TableMoldReport::MoldReportId]);
					$trBody->appendChild($td);
					break;
				// 仕入マスタ.仕入計上日
				case "dtmappropriationdate":
					$td = $doc->createElement("td", $record["dtmappropriationdate"]);
					$trBody->appendChild($td);
					break;
				// 発注マスタ.発注コード-発注マスタ.修正コード
				case "strordercode":
					$td = $doc->createElement("td", $record["strordercode"]);
					$trBody->appendChild($td);
					break;
				// [会社マスタ.表示会社コード] 会社マスタ.表示会社名称
				case "strcompanydisplaycode":
					$td = $doc->createElement("td", toUTF8($record["strcompanydisplaycode"]));
					$trBody->appendChild($td);
					break;
				// 製品マスタ.製品コード
				case "strproductcode":
					$td = $doc->createElement("td", $record["strproductcode"]);
					$trBody->appendChild($td);
					break;
				// 製品マスタ.製品コード(日本語)
				case "strproductname":
					$td = $doc->createElement("td", toUTF8($record["strproductname"]));
					$trBody->appendChild($td);
					break;
				// 製品マスタ.製品名称(英語)
				case "strproductenglishname":
					$td = $doc->createElement("td", toUTF8($record["strproductenglishname"]));
					$trBody->appendChild($td);
					break;
				// 製品マスタ.顧客品番
				case "strgoodscode":
					$td = $doc->createElement("td", toUTF8($record["strgoodscode"]));
					$trBody->appendChild($td);
					break;
				// [グループマスタ.表示グループコード] グループマスタ.表示グループ名
				case "strgroupdisplaycode":
					$td = $doc->createElement("td", toUTF8($record["strgroupdisplaycode"]));
					$trBody->appendChild($td);
					break;
				// [ユーザマスタ.表示ユーザコード] ユーザマスタ.表示ユーザ名
				case "struserdisplaycode":
					$td = $doc->createElement("td", toUTF8($record["struserdisplaycode"]));
					$trBody->appendChild($td);
					break;
				// 金型履歴.金型NO
				case TableMoldHistory::MoldNo:
					$td = $doc->createElement("td", $record[TableMoldHistory::MoldNo]);
					$trBody->appendChild($td);
					break;
				// 金型履歴.履歴番号
				case TableMoldHistory::HistoryNo:
					$td = $doc->createElement("td", $record[TableMoldHistory::HistoryNo]);
					$trBody->appendChild($td);
					break;
				// 仕入詳細.製品数量
				case "lngproductquantity":
					$td = $doc->createElement("td", $record["lngproductquantity"]);
					$trBody->appendChild($td);
					break;
				// 通貨単位マスタ.通貨単位 || 仕入詳細.税抜金額
				case "strmonetaryunitsign":
					$td = $doc->createElement("td", toUTF8($record["strmonetaryunitsign"]));
					$trBody->appendChild($td);
					break;
				// 金型履歴.金型ステータス
				case TableMoldHistory::Status:
					$record[TableMoldHistory::Status] ?
						  $td = $doc->createElement("td", toUTF8($utilBussinesscode->getDescription("金型ステータス", $record[TableMoldHistory::Status])))
						: $td = $doc->createElement("td");
					$trBody->appendChild($td);
					break;
				// 金型履歴.実施日
				case TableMoldHistory::ActionDate:
					$td = $doc->createElement("td", $record[TableMoldHistory::ActionDate]);
					$trBody->appendChild($td);
					break;
				// 金型履歴.保管工場
				case TableMoldHistory::SourceFactory:
					if ($record[TableMoldHistory::SourceFactory] || $record[TableMoldHistory::DestinationFactory] === "0")
					{
						$textContent = "[".$record["strsourfacdisplaycode"]."]"." ".$record["strsourfacdisplayname"];
						$td = $doc->createElement("td", toUTF8($textContent));
					}
					else
					{
						$td = $doc->createElement("td");
					}
					$trBody->appendChild($td);
					break;
				// 金型履歴.移動先工場
				case TableMoldHistory::DestinationFactory:
					if ($record[TableMoldHistory::DestinationFactory] || $record[TableMoldHistory::DestinationFactory] === "0")
					{
						$textContent = "[".$record["strdescfacdisplaycode"]."]"." ".$record["strdescfacdisplayname"];
						$td = $doc->createElement("td", toUTF8($textContent));
					}
					else
					{
						$td = $doc->createElement("td");
					}
					$trBody->appendChild($td);
					break;
				// 金型履歴.登録日時
				case TableMoldHistory::Created :
					$td = $doc->createElement("td", $record[TableMoldHistory::Created]);
					$trBody->appendChild($td);
					break;
				// 金型履歴.登録者
				case TableMoldHistory::CreateBy :
					if ($record[TableMoldHistory::CreateBy])
					{
						$textContent = "[".$record["strcreateuserdisplaycode"]."]"." ".$record["strcreateuserdisplayname"];
						$td = $doc->createElement("td", toUTF8($textContent));
					}
					else
					{
						$td = $doc->createElement("td");
					}
					$trBody->appendChild($td);
					break;
				// 金型履歴.更新日時
				case TableMoldHistory::Updated :
					$td = $doc->createElement("td", $record[TableMoldHistory::Updated]);
					$trBody->appendChild($td);
					break;
				// 金型履歴.更新者
				case TableMoldHistory::UpdateBy :
					if ($record[TableMoldHistory::UpdateBy])
					{
						$textContent = "[".$record["strupdateuserdisplaycode"]."]"." ".$record["strupdateuserdisplayname"];
						$td = $doc->createElement("td", toUTF8($textContent));
					}
					else
					{
						$td = $doc->createElement("td");
					}
					$trBody->appendChild($td);
					break;
				// 金型履歴.バージョン
				case TableMoldHistory::Version :
					$td = $doc->createElement("td", $record[TableMoldHistory::Version]);
					$trBody->appendChild($td);
					break;
				// 金型履歴.削除フラグ
				case TableMoldHistory::DeleteFlag :
					$td = $doc->createElement("td", $record[TableMoldHistory::DeleteFlag]);
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

		// 削除ボタンの表示
		if ($allowedDelete)
		{
			// 削除ボタン
			$imgDelete = $doc->createElement("img");
			$imgDelete->setAttribute("src", "/mold/img/remove_off_bt.gif");
			$imgDelete->setAttribute("id", $record[TableMoldHistory::MoldNo]);
			$imgDelete->setAttribute("historyno", $record[TableMoldHistory::HistoryNo]);
			$imgDelete->setAttribute("version", $record[TableMoldHistory::Version]);
			$imgDelete->setAttribute("class", "delete button");
			// td > img
			$tdDelete->appendChild($imgDelete);
		}
		// tr > td
		$trBody->appendChild($tdDelete);
	}

	// tbody > tr
	$tbody->appendChild($trBody);
}

// HTML出力
echo $doc->saveHTML();

