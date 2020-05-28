<?php

// ----------------------------------------------------------------------------
/**
*       金型管理  一覧検索処理
*/
// ----------------------------------------------------------------------------
include( 'conf.inc' );
require_once( LIB_FILE );

require_once SRC_ROOT . '/mold/lib/UtilSearchForm.class.php';
require SRC_ROOT . "search/cmn/lib_search.php";

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
	fncOutputError( 9060, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
}

// 1802 金型履歴管理(検索)
if ( !fncCheckAuthority( DEF_FUNCTION_MM2, $objAuth ) )
{
	fncOutputError( 9060, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
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
$aryQuery = array();
$aryQuery[] = "SELECT";
$aryQuery[] = "  tsd.lngstockno as lngpkno";
$aryQuery[] = "  , tsd.lngrevisionno";
$aryQuery[] = "  , tmrr.moldreportid";
$aryQuery[] = "  , tmrr.revision";
$aryQuery[] = "  , ms.dtmappropriationdate";
$aryQuery[] = "  , mo.strordercode";
$aryQuery[] = "  , '[' || mc.strcompanydisplaycode || ']' || mc.strcompanydisplayname AS strcompanydisplaycode";
$aryQuery[] = "  , mp.strproductcode || '_' || mp.strrevisecode as strproductcode";
$aryQuery[] = "  , mp.strrevisecode";
$aryQuery[] = "  , mp.strproductname";
$aryQuery[] = "  , mp.strproductenglishname";
$aryQuery[] = "  , mp.strgoodscode";
$aryQuery[] = "  , '[' || mg.strgroupdisplaycode || ']' || mg.strgroupdisplayname as strgroupdisplaycode";
$aryQuery[] = "  , '[' || mu.struserdisplaycode || ']' || mu.struserdisplayname as struserdisplaycode";
$aryQuery[] = "  , tsd.strmoldno";
$aryQuery[] = "  , tmh.historyno";
$aryQuery[] = "  , tsd.lngproductquantity";
$aryQuery[] = "  , mou.strmonetaryunitsign || tsd.cursubtotalprice as strmonetaryunitsign";
$aryQuery[] = "  , mb.description as status";
$aryQuery[] = "  , tmh.actiondate";
$aryQuery[] = "  , '[' || mc_sf.strcompanydisplaycode || ']' || mc_sf.strcompanydisplayname as sourcefactory";
$aryQuery[] = "  , '[' || mc_df.strcompanydisplaycode || ']' || mc_df.strcompanydisplayname as destinationfactory";
$aryQuery[] = "  , tmh.created ::date";
$aryQuery[] = "  , '[' || mu_c.struserdisplaycode || ']' || mu_c.struserdisplayname as createby";
$aryQuery[] = "  , tmh.updated ::date";
$aryQuery[] = "  , '[' || mu_u.struserdisplaycode || ']' || mu_u.struserdisplayname as updateby";
$aryQuery[] = "  , tmh.version";
$aryQuery[] = "  , tmh.deleteflag ";
$aryQuery[] = "FROM";
$aryQuery[] = "  t_stockdetail tsd ";
$aryQuery[] = "  inner join ( ";
$aryQuery[] = "    select";
$aryQuery[] = "      ms1.lngstockno";
$aryQuery[] = "      , ms1.lngrevisionno";
$aryQuery[] = "      , ms1.dtmappropriationdate";
$aryQuery[] = "      , ms1.lngcustomercompanycode";
$aryQuery[] = "      , ms1.lngmonetaryunitcode ";
$aryQuery[] = "    from";
$aryQuery[] = "      m_stock ms1 ";
$aryQuery[] = "      inner join ( ";
$aryQuery[] = "        select";
$aryQuery[] = "          max(lngrevisionno) lngrevisionno";
$aryQuery[] = "          , lngstockno ";
$aryQuery[] = "        from";
$aryQuery[] = "          m_Stock ";
$aryQuery[] = "        where";
$aryQuery[] = "          bytInvalidFlag = FALSE ";
$aryQuery[] = "        group by";
$aryQuery[] = "          lngstockno";
$aryQuery[] = "      ) s1 ";
$aryQuery[] = "        on ms1.lngrevisionno = s1.lngrevisionno ";
$aryQuery[] = "        and ms1.lngstockno = s1.lngstockno";
$aryQuery[] = "  ) ms ";
$aryQuery[] = "    on tsd.lngrevisionno = ms.lngrevisionno ";
$aryQuery[] = "    and tsd.lngstockno = ms.lngstockno ";
$aryQuery[] = "  LEFT JOIN t_moldhistory tmh ";
$aryQuery[] = "    on tsd.strmoldno = tmh.moldno ";
$aryQuery[] = "  LEFT JOIN m_mold mm ";
$aryQuery[] = "    ON tsd.strmoldno = mm.moldno ";
$aryQuery[] = "  LEFT JOIN t_moldreportrelation tmrr ";
$aryQuery[] = "    ON tmh.moldno = tmrr.moldno ";
$aryQuery[] = "    AND tmh.historyno = tmrr.historyno ";
$aryQuery[] = "  LEFT JOIN m_tax mt ";
$aryQuery[] = "    ON tsd.lngtaxcode = mt.lngtaxcode ";
$aryQuery[] = "  LEFT OUTER JOIN ( ";
$aryQuery[] = "    SELECT";
$aryQuery[] = "      p.strproductcode";
$aryQuery[] = "      , p.strrevisecode";
$aryQuery[] = "      , p.strgoodscode";
$aryQuery[] = "      , p.strproductname";
$aryQuery[] = "      , p.strproductenglishname";
$aryQuery[] = "      , p.lnginchargegroupcode";
$aryQuery[] = "      , p.lnginchargeusercode ";
$aryQuery[] = "    FROM";
$aryQuery[] = "      m_product p ";
$aryQuery[] = "      inner join ( ";
$aryQuery[] = "        SELECT";
$aryQuery[] = "          MAX(lngrevisionno) lngrevisionno";
$aryQuery[] = "          , lngproductno";
$aryQuery[] = "          , strrevisecode ";
$aryQuery[] = "        FROM";
$aryQuery[] = "          m_product ";
$aryQuery[] = "        WHERE";
$aryQuery[] = "          bytInvalidFlag = false ";
$aryQuery[] = "        group by";
$aryQuery[] = "          lngproductno";
$aryQuery[] = "          , strrevisecode";
$aryQuery[] = "      ) p1 ";
$aryQuery[] = "        on p.lngproductno = p1.lngproductno ";
$aryQuery[] = "        and p.strrevisecode = p1.strrevisecode ";
$aryQuery[] = "        and p.lngrevisionno = p1.lngrevisionno ";
$aryQuery[] = "    where";
$aryQuery[] = "      p.lngrevisionno >= 0";
$aryQuery[] = "  ) mp ";
$aryQuery[] = "    ON tsd.strproductcode = mp.strproductcode ";
$aryQuery[] = "    AND tsd.strrevisecode = mp.strrevisecode ";
$aryQuery[] = "  LEFT JOIN m_group mg ";
$aryQuery[] = "    ON mp.lnginchargegroupcode = mg.lnggroupcode ";
$aryQuery[] = "  LEFT JOIN m_user mu ";
$aryQuery[] = "    ON mp.lnginchargeusercode = mu.lngusercode ";
$aryQuery[] = "  LEFT JOIN m_user mu_u ";
$aryQuery[] = "    ON tmh.updateby = mu_u.lngusercode ";
$aryQuery[] = "  LEFT JOIN m_user mu_c ";
$aryQuery[] = "    ON tmh.createby = mu_c.lngusercode ";
$aryQuery[] = "  LEFT JOIN m_order mo ";
$aryQuery[] = "    ON tsd.lngorderno = mo.lngorderno ";
$aryQuery[] = "    and tsd.lngorderrevisionno = mo.lngrevisionno ";
$aryQuery[] = "  LEFT JOIN m_Company mc ";
$aryQuery[] = "    ON ms.lngcustomercompanycode = mc.lngcompanycode ";
$aryQuery[] = "  LEFT JOIN m_Company mc_sf ";
$aryQuery[] = "    ON tmh.sourcefactory = mc_sf.lngcompanycode ";
$aryQuery[] = "  LEFT JOIN m_Company mc_df ";
$aryQuery[] = "    ON tmh.destinationfactory = mc_df.lngcompanycode ";
$aryQuery[] = "  LEFT JOIN m_MonetaryUnit mou ";
$aryQuery[] = "    ON ms.lngmonetaryunitcode = mou.lngmonetaryunitcode ";
$aryQuery[] = "  LEFT JOIN m_businesscode mb ";
$aryQuery[] = "    ON tmh.status = mb.businesscode ";
$aryQuery[] = "    and mb.businesscodename = '金型ステータス' ";
$aryQuery[] = "WHERE";
$aryQuery[] = "  ( ";
$aryQuery[] = "    ( ";
$aryQuery[] = "      tsd.lngStockSubjectCode = 433 ";
$aryQuery[] = "      AND tsd.lngStockItemCode = 1";
$aryQuery[] = "    ) ";
$aryQuery[] = "    OR ( ";
$aryQuery[] = "      tsd.lngStockSubjectCode = 431 ";
$aryQuery[] = "      AND tsd.lngStockItemCode = 8";
$aryQuery[] = "    )";
$aryQuery[] = "  ) ";

// 検索項目のキーを小文字に変換
$searchColumns = array_change_key_case($searchColumns, CASE_LOWER);
// 検索値のキーを小文字に変換
$searchValue = array_change_key_case($searchValue, CASE_LOWER);
$from = array_change_key_case($from, CASE_LOWER);
$to = array_change_key_case($to, CASE_LOWER);

// 金型帳票ID
if (array_key_exists("moldreportid", $searchColumns) &&
	array_key_exists("moldreportid", $from) &&
	array_key_exists("moldreportid", $to))
{
	$aryQuery[] = "AND tmrr.moldreportid".
			" between '".pg_escape_string($from["moldreportid"])."'".
			" AND "."'".pg_escape_string($to["moldreportid"])."'";
}

// 仕入先
if (array_key_exists("strcompanydisplaycode", $searchColumns) &&
	array_key_exists("customercode", $searchValue))
{
	$aryQuery[] = "AND mc.strcompanydisplaycode = '".pg_escape_string($searchValue["customercode"])."'";
}

// 製品コード
if (array_key_exists("strproductcode", $searchColumns) &&
	array_key_exists("strproductcode", $from) &&
	array_key_exists("strproductcode", $to))
{
	$aryQuery[] = "AND mp.strproductcode".
				" between '".pg_escape_string($from["strproductcode"])."'".
				" AND "."'".pg_escape_string($to["strproductcode"])."'";
}
// 製品名称
if (array_key_exists("strproductname", $searchColumns) &&
	array_key_exists("strproductname", $searchValue))
{
	$aryQuery[] = "AND sf_translate_case(mp.strproductname) like '%' || sf_translate_case('".pg_escape_string($searchValue["strproductname"])."') || '%'";
}
// 製品名称(英語)
if (array_key_exists("strproductenglishname", $searchColumns) &&
	array_key_exists("strproductenglishname", $searchValue))
{
	$aryQuery[] = "AND sf_translate_case(mp.strproductenglishname) like '%' || sf_translate_case('".pg_escape_string($searchValue["strproductenglishname"])."') || '%'";
}

// 顧客品番
if (array_key_exists("strgoodscode", $searchColumns) &&
	array_key_exists("strgoodscode", $searchValue))
{
	$aryQuery[] = "AND mp.strgoodscode = '".pg_escape_string($searchValue["strgoodscode"])."'";
}

// 担当部署
if (array_key_exists("strgroupdisplaycode", $searchColumns) &&
	array_key_exists("kuwagatagroupcode", $searchValue))
{
	$aryQuery[] = "AND mg.strgroupdisplaycode = '".pg_escape_string($searchValue["kuwagatagroupcode"])."'";
}

// 担当者
if (array_key_exists("struserdisplaycode", $searchColumns) &&
	array_key_exists("kuwagatausercode", $searchValue))
{
	$aryQuery[] = "AND mu.struserdisplaycode = '".pg_escape_string($searchValue["kuwagatausercode"])."'";
}

// 金型NO
if (array_key_exists("strmoldno", $searchColumns) &&
	array_key_exists("choosenmoldlist", $searchValue) &&
	count($searchValue["choosenmoldlist"]))
{
	$aryQuery[] = "AND tsd.strmoldno in";
	$aryQuery[] = "    (";

	// 金型件数分走査
	foreach ($searchValue["choosenmoldlist"] as $index => $moldno)
	{
		$aryQuery[] = "        '".pg_escape_string($moldno)."',";
	}

	// 末尾のカンマを削除
	$aryQuery[] = rtrim(array_pop($aryQuery), ',');
	$aryQuery[] = "    )";
}

// 金型ステータス
if (array_key_exists("status", $searchColumns) &&
	array_key_exists("status", $searchValue))
{
	$aryQuery[] = "AND tmh.status = '".$searchValue["status"]."'";
}

// 実施日
if (array_key_exists("actiondate", $searchColumns) &&
	array_key_exists("actiondate", $from) &&
	array_key_exists("actiondate", $to))
{
	$aryQuery[] = "AND tmh.actiondate".
				" between '".$from["actiondate"]."'".
				" AND "."'".$to["actiondate"]."'";
}

// 保管工場
if (array_key_exists("sourcefactory", $searchColumns) &&
	array_key_exists("sourcefactory", $searchValue))
{
	$aryQuery[] = "AND mc_sf.strcompanydisplaycode = '".$searchValue["sourcefactory"]."'";
}

// 移動先工場
if (array_key_exists("destinationfactory", $searchColumns) &&
	array_key_exists("destinationfactory", $searchValue))
{
	$aryQuery[] = "AND mc_df.strcompanydisplaycode = '".$searchValue["destinationfactory"]."'";
}

// 登録日
if (array_key_exists("created", $searchColumns) &&
	array_key_exists("created", $from) &&
	array_key_exists("created", $to))
{
	$aryQuery[] = "AND tmh.created".
				" between '".$from["created"]." 00:00:00'".
				" AND "."'".$to["created"]." 23:59:59.99999'";
}

// 登録者
if (array_key_exists("createby", $searchColumns) &&
	array_key_exists("createby", $searchValue))
{
	$aryQuery[] = "AND mu_c.struserdisplaycode = '".$searchValue["createby"]."'";
}

// 更新日
if (array_key_exists("updated", $searchColumns) &&
	array_key_exists("updated", $from) &&
	array_key_exists("updated", $to))
{
	$aryQuery[] = "AND tmh.updated".
				" between '".$from["updated"]." 00:00:00'".
				" AND "."'".$to["updated"]." 23:59:59.99999'";
}

// 更新者
if (array_key_exists("updateby", $searchColumns) &&
	array_key_exists("updateby", $searchValue))
{
	$aryQuery[] = "AND mu_u.struserdisplaycode = '".$searchValue["updateby"]."'";
}
$aryQuery[] = "  AND not exists ( ";
$aryQuery[] = "    select";
$aryQuery[] = "      s2.lngstockno ";
$aryQuery[] = "    from";
$aryQuery[] = "      ( ";
$aryQuery[] = "        SELECT";
$aryQuery[] = "          min(lngRevisionNo) lngRevisionNo";
$aryQuery[] = "          , lngstockno ";
$aryQuery[] = "        FROM";
$aryQuery[] = "          m_Stock ";
$aryQuery[] = "        group by";
$aryQuery[] = "          lngstockno";
$aryQuery[] = "      ) as s2 ";
$aryQuery[] = "    where";
$aryQuery[] = "      s2.lngstockno = tsd.lngstockno ";
$aryQuery[] = "      AND s2.lngRevisionNo < 0";
$aryQuery[] = "  ) ";
$aryQuery[] = "ORDER BY";
$aryQuery[] = "  tsd.strproductcode";
$aryQuery[] = "  , tsd.strmoldno";

// クエリを平易な文字列に変換
$query = implode("\n",$aryQuery);

// クエリ実行
$lngResultID = pg_query($query);

$lngResultNum = pg_num_rows($lngResultID);

// 検索件数がありの場合
if ($lngResultNum > 0) {
    // 指定数以上の場合エラーメッセージを表示する
    if ($lngResultNum > DEF_SEARCH_MAX) {
        $errorFlag = true;
        $lngErrorCode = 9057;
        $aryErrorMessage = DEF_SEARCH_MAX;
    }
} else {
    $errorFlag = true;
    $lngErrorCode = 9068;
    $aryErrorMessage = "";
}
if ($errorFlag) {
    // エラー画面の戻り先
    $strReturnPath = "../mm/list/index.php?strSessionID=" . $aryData["strSessionID"];

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
$objTemplate = new clsTemplate();
$objTemplate->getTemplate ( "/mm/list/mm_list_search_result.html" );
$aryResult["strSessionID"] = $_REQUEST["strSessionID"];
// テンプレート生成
$objTemplate->replace($aryResult);

// 検索結果テーブル生成の為DOMDocumentを使用
$doc = new DOMDocument();
// パースエラー抑制
libxml_use_internal_errors(true);
// DOMパース
$doc->loadHTML($objTemplate->strTemplate);
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
// -------------------------------------------------------
// 各種ボタン表示チェック/権限チェック
// -------------------------------------------------------
$aryAuthority = fncGetAryAuthority('pc', $objAuth);

// -------------------------------------------------------
// テーブルヘッダ作成
// -------------------------------------------------------
// thead > tr要素作成
// -------------------------------------------------------
// テーブルヘッダ作成
// -------------------------------------------------------
// thead > tr要素作成
$trHead = $doc->createElement("tr");
fncSetTheadData($doc, $trHead, $aryTableHeadBtnName, $aryTableBackBtnName, $aryTableHeaderName_MM_LIST, null, $displayColumns);
$thead->appendChild($trHead);

$index = 0;
// -------------------------------------------------------
// テーブルセル作成
// -------------------------------------------------------
// 検索結果件数分走査
foreach ($records as $i => $record) {
	$index = $index + 1;
    $bgcolor = "";

    // tbody > tr要素作成
	$trBody = $doc->createElement("tr");
	
    // 先頭ボタン設定
    fncSetHeadBtnToTr($doc, $trBody, $bgcolor, $aryTableHeadBtnName, $displayColumns, $record, $aryAuthority, true, false, $index, 'po', null);

    // ヘッダー部データ設定
    fncSetHeadDataToTr($doc, $trBody, $bgcolor, $aryTableHeaderName_MM_LIST, $displayColumns, $record, true);

    // フッターボタン表示
    fncSetBackBtnToTr($doc, $trBody, $bgcolor, $aryTableBackBtnName, $displayColumns, $record, $aryAuthority, true, false, 'po');

    // tbody > tr
    $tbody->appendChild($trBody);

}

// HTML出力
echo $doc->saveHTML();
