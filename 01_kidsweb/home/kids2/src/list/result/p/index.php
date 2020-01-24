<?
/**
 *    帳票出力 商品企画書 検索結果画面
 *
 *    @package   KIDS
 *    @license   http://www.wiseknot.co.jp/
 *    @copyright Copyright &copy; 2003, Wiseknot
 *    @author    Kenji Chiba <k-chiba@wiseknot.co.jp>
 *    @access    public
 *    @version   1.00
 *
 *    更新履歴
 *    2004.05.21    商品化企画書検索結果一覧にて製品コードとして表示していた内容が製品番号であったバグの修正
 *
 */
// 検索結果画面( * は指定帳票のファイル名 )
// *.php -> strSessionID       -> index.php

// 印刷画面へ
// index.php -> strSessionID       -> frameset.php
// index.php -> lngReportClassCode -> frameset.php
// index.php -> strReportKeyCode   -> frameset.php
// index.php -> lngReportCode      -> frameset.php

// 設定読み込み
include_once 'conf.inc';

require_once SRC_ROOT . '/mold/lib/UtilSearchForm.class.php';

// ライブラリ読み込み
require LIB_FILE;
require SRC_ROOT . "list/cmn/lib_lo.php";
require LIB_DEBUGFILE;

$objDB = new clsDB();
$objAuth = new clsAuth();
$objDB->open("", "", "", "");

// セッション確認
$objAuth = fncIsSession($_REQUEST["strSessionID"], $objAuth, $objDB);

// 権限確認
if (!fncCheckAuthority(DEF_FUNCTION_LO0, $objAuth)) {
    fncOutputError(9052, DEF_WARNING, "アクセス権限がありません。", true, "", $objDB);
}

//////////////////////////////////////////////////////////////////////////
// POST(一部GET)データ取得
//////////////////////////////////////////////////////////////////////////
// フォームデータから各カテゴリの振り分けを行う
$isSearch = UtilSearchForm::extractArrayByIsSearch($_REQUEST);
$from = UtilSearchForm::extractArrayByFrom($_REQUEST);
$to = UtilSearchForm::extractArrayByTo($_REQUEST);
$searchValue = $_REQUEST;

// クエリの組立に使用するフォームデータを抽出
$searchColumns = array();
$conditions = array();

// 検索項目の抽出
foreach ($isSearch as $key => $flag) {
    if ($flag == "on") {
        $searchColumns[$key] = $key;
    }
}

// 商品企画書出力
// コピーファイル取得クエリ生成
$strCopyQuery = "SELECT gp.lngProductNo AS strReportKeyCode, r.lngReportCode FROM t_GoodsPlan gp inner join (SELECT max(lngRevisionNo) AS lngRevisionNo, lngproductno from t_goodsplan gp1 group by lngproductno ) gp2  ON gp.lngproductno = gp2.lngproductno and gp.lngRevisionNo = gp2.lngRevisionNo, t_Report r WHERE r.lngReportClassCode = " . DEF_REPORT_PRODUCT . " AND to_number ( r.strReportKeyCode, '9999999') = gp.lngGoodsPlanCode";

// 商品企画書取得クエリ生成
$aryQuery[] = "SELECT p.strProductCode, p.strProductName,";
$aryQuery[] = " p.strrevisecode AS strrevisecode,";
$aryQuery[] = " u1.strUserDisplayCode AS strInputUserDisplayCode,";
$aryQuery[] = " u1.strUserDisplayName AS strInputUserDisplayName,";
$aryQuery[] = " g.strGroupDisplayCode AS strInChargeGroupDisplayCode,";
$aryQuery[] = " g.strGroupDisplayName AS strInChargeGroupDisplayName,";
$aryQuery[] = " u2.strUserDisplayCode AS strInChargeUserDisplayCode,";
$aryQuery[] = " u2.strUserDisplayName AS strInChargeUserDisplayName,";
$aryQuery[] = " p.lngprintcount,";
$aryQuery[] = " gp.lnggoodsplancode AS strReportKeyCode ";
$aryQuery[] = "FROM";
$aryQuery[] = "  m_product p ";
$aryQuery[] = "  inner join ( ";
$aryQuery[] = "    select";
$aryQuery[] = "      max(lngrevisionno) lngrevisionno";
$aryQuery[] = "      , strproductcode ";
$aryQuery[] = "      , strrevisecode ";
$aryQuery[] = "    from";
$aryQuery[] = "      m_Product ";
$aryQuery[] = "    where";
$aryQuery[] = "      bytInvalidFlag = false ";
$aryQuery[] = "    group by";
$aryQuery[] = "      strProductCode";
$aryQuery[] = "      ,strrevisecode";
$aryQuery[] = "  ) p1 ";
$aryQuery[] = "    on p.strProductCode = p1.strProductCode ";
$aryQuery[] = "    and p.lngrevisionno = p1.lngrevisionno ";
$aryQuery[] = "    and p.strrevisecode = p1.strrevisecode ";
$aryQuery[] = " LEFT JOIN m_User u1 ON p.lngInputUserCode = u1.lngUserCode";
$aryQuery[] = " LEFT JOIN m_User u2 ON p.lngInChargeUserCode = u2.lngUserCode";
$aryQuery[] = " LEFT JOIN m_Group g ON p.lngInChargeGroupCode = g.lngGroupCode";
$aryQuery[] = ", ( ";
$aryQuery[] = "      select";
$aryQuery[] = "        gp1.* ";
$aryQuery[] = "      from";
$aryQuery[] = "        t_GoodsPlan gp1 ";
$aryQuery[] = "        inner join ( ";
$aryQuery[] = "          SELECT";
$aryQuery[] = "            MAX(lngRevisionNo) lngRevisionNo";
$aryQuery[] = "            , lngProductNo ";
$aryQuery[] = "          FROM";
$aryQuery[] = "            t_GoodsPlan ";
$aryQuery[] = "          group by";
$aryQuery[] = "            lngProductNo";
$aryQuery[] = "        ) gp2 ";
$aryQuery[] = "          on gp1.lngProductNo = gp2.lngProductNo ";
$aryQuery[] = "          and gp1.lngRevisionNo = gp2.lngRevisionNo";
$aryQuery[] = "    ) gp ";
$aryQuery[] = " WHERE p.lngProductNo = gp.lngProductNo";
$aryQuery[] = " AND p.strrevisecode = gp.strrevisecode";
$aryQuery[] = " AND not exists ( ";
$aryQuery[] = "    select";
$aryQuery[] = "      p1.strproductcode ";
$aryQuery[] = "    from";
$aryQuery[] = "      ( ";
$aryQuery[] = "        SELECT";
$aryQuery[] = "          min(lngRevisionNo) lngRevisionNo";
$aryQuery[] = "          , strproductcode ";
$aryQuery[] = "        FROM";
$aryQuery[] = "          m_product ";
$aryQuery[] = "        where";
$aryQuery[] = "          bytInvalidFlag = false ";
$aryQuery[] = "        group by";
$aryQuery[] = "          strproductcode";
$aryQuery[] = "      ) as p1 ";
$aryQuery[] = "    where";
$aryQuery[] = "      p1.strproductcode = p.strproductcode";
$aryQuery[] = "      AND p1.lngRevisionNo < 0";
$aryQuery[] = "  ) ";
/////////////////////////////////////////////////////////////////
// 検索条件
/////////////////////////////////////////////////////////////////
// 作成日時_from
if (array_key_exists("dtmInsertDate", $searchColumns) &&
    array_key_exists("dtmInsertDate", $from) && $from["dtmInsertDate"] != '') {
    $aryQuery[] = " AND date_trunc('day', p.dtmInsertDate)" .
    " >= '" . pg_escape_string($from["dtmInsertDate"]) . "'";
}
// 作成日時_to
if (array_key_exists("dtmInsertDate", $searchColumns) &&
    array_key_exists("dtmInsertDate", $to) && $to["dtmInsertDate"] != '') {
    $aryQuery[] = " AND date_trunc('day', p.dtmInsertDate)" .
    " <= " . "'" . pg_escape_string($to["dtmInsertDate"]) . "'";
}
// 企画進行状況
if (array_key_exists("lngGoodsPlanProgressCode", $searchColumns) &&
    array_key_exists("lngGoodsPlanProgressCode", $searchValue)) {
		$aryQuery[] = " AND gp.lngGoodsPlanProgressCode = " . $searchValue["lngGoodsPlanProgressCode"];
}
// 改訂日時_from
if (array_key_exists("dtmRevisionDate", $searchColumns) &&
    array_key_exists("dtmRevisionDate", $from) && $to["dtmRevisionDate"] != '') {
    $aryQuery[] = " AND date_trunc('day', p.dtmUpdateDate)" .
    " >= '" . pg_escape_string($from["dtmRevisionDate"]) . "'";
}
// 改訂日時_to
if (array_key_exists("dtmRevisionDate", $searchColumns) &&
    array_key_exists("dtmRevisionDate", $to) && $to["dtmRevisionDate"] != '') {
    $aryQuery[] = " AND date_trunc('day', p.dtmUpdateDate)" .
    " <= " . "'" . pg_escape_string($to["dtmRevisionDate"]) . "'";
}
// 製品コード_from
if (array_key_exists("strProductCode", $searchColumns) &&
    array_key_exists("strProductCode", $from) && $from["strProductCode"] != '') {
    $aryQuery[] = " AND p.strProductCode" .
    " >= '" . pg_escape_string($from["strProductCode"]) . "'";
}
// 製品コード_to
if (array_key_exists("strProductCode", $searchColumns) &&
    array_key_exists("strProductCode", $to) && $to["strProductCode"] != '') {
    $aryQuery[] = " AND p.strProductCode" .
    " <= " . "'" . pg_escape_string($to["strProductCode"]) . "'";
}
// 製品名
if (array_key_exists("strProductName", $searchColumns) &&
    array_key_exists("strProductName", $searchValue)) {
		$aryQuery[] = " AND p.strProductName LIKE '%" . $searchValue["strProductName"] . "%'";
}
// 製品名(英語)
if (array_key_exists("strProductEnglishName", $searchColumns) &&
    array_key_exists("strProductEnglishName", $searchValue)) {
		$aryQuery[] = " AND p.strProductEnglishName LIKE '%" . $searchValue["strProductEnglishName"] . "%'";
}
// 入力者コード
if (array_key_exists("lngInputUserCode", $searchColumns) &&
    array_key_exists("lngInputUserCode", $searchValue)) {
    $aryQuery[] = " AND u1.strUserDisplayCode = '" . $searchValue["lngInputUserCode"] . "'";
}
// 部門コード
if (array_key_exists("lngInChargeGroupCode", $searchColumns) &&
    array_key_exists("lngInChargeGroupCode", $searchValue)) {
    $aryQuery[] = " AND g.strGroupDisplayCode = '" . $searchValue["lngInChargeGroupCode"] . "'";
}
// 担当者コード
if (array_key_exists("lngInChargeUserCode", $searchColumns) &&
    array_key_exists("lngInChargeUserCode", $searchValue)) {
    $aryQuery[] = " AND u2.strUserDisplayCode = '" . $searchValue["lngInChargeUserCode"] . "'";
}
$aryQuery[] = " AND p.bytinvalidflag = false";
$aryQuery[] = " ORDER BY p.strProductCode ASC";

// ナンバーをキーとする連想配列に帳票コードを取得
list($lngResultID, $lngResultNum) = fncQuery($strCopyQuery, $objDB);

for ($i = 0; $i < $lngResultNum; $i++) {
    $objResult = $objDB->fetchObject($lngResultID, $i);
    $aryReportCode[$objResult->strreportkeycode] = $objResult->lngreportcode;
}

if ($lngResultNum > 0) {
    $objDB->freeResult($lngResultID);
}

// 帳票データ取得クエリ実行・テーブル生成
$strQuery = join("\n", $aryQuery);

list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);
for ($i = 0; $i < $lngResultNum; $i++) {
    $objResult = $objDB->fetchObject($lngResultID, $i);

    $aryParts["strResult"] .= "<tr class=\"Segs\">\n";
    $aryParts["strResult"] .= "<td>" . $objResult->strproductcode . "_" . $objResult->strrevisecode . "</td>\n";
    $aryParts["strResult"] .= "<td>" . $objResult->strproductname . "</td>\n";
    $aryParts["strResult"] .= "<td>" . $objResult->strinputuserdisplaycode . ":" . $objResult->strinputuserdisplayname . "</td>\n";
    $aryParts["strResult"] .= "<td>" . $objResult->strinchargegroupdisplaycode . ":" . $objResult->strinchargegroupdisplayname . "</td>\n";
    $aryParts["strResult"] .= "<td>" . $objResult->strinchargeuserdisplaycode . ":" . $objResult->strinchargeuserdisplayname . "</td>\n";
    $aryParts["strResult"] .= "<td align=center>";

    // 印刷回数が0より大きい場合、コピー帳票出力ボタン表示
    if (intval($objResult->lngprintcount) > 0) {

        // コピー帳票出力ボタン表示
        $aryParts["strResult"] .= "<a href=\"#\"><img onclick=\"fncListOutput( '/list/result/frameset.php?strSessionID=" . $searchValue["strSessionID"] . "&lngReportClassCode=" . DEF_REPORT_PRODUCT . "&strReportKeyCode=" . $objResult->strreportkeycode . "&lngReportCode=" . $aryReportCode[$objResult->strreportkeycode] . "' );return false;\" onmouseover=\"fncCopyPreviewButton( 'on' , this );\" onmouseout=\"fncCopyPreviewButton( 'off' , this );fncAlphaOff( this );\" onmousedown=\"fncAlphaOn( this );\" onmouseup=\"fncAlphaOff( this );\" src=\"/img/type01/list/copybig_off_bt.gif\" width=\"72\" height=\"20\" border=\"0\" alt=\"COPY PREVIEW\"></a>";
    }

    $aryParts["strResult"] .= "</td>\n<td align=center>";

    // コピーファイルパスが存在しない または コピー解除権限がある場合、
    // 帳票出力ボタン表示
    if ($aryReportCode[$objResult->strreportkeycode] == null || fncCheckAuthority(DEF_FUNCTION_LO3, $objAuth)) {
        // 帳票出力ボタン表示
        $aryParts["strResult"] .= "<a href=\"#\"><img onclick=\"fncListOutput( '/list/result/frameset.php?strSessionID=" . $searchValue["strSessionID"] . "&lngReportClassCode=" . DEF_REPORT_PRODUCT . "&strReportKeyCode=" . $objResult->strreportkeycode . "&strActionList=p' );return false;\" onmouseover=\"fncPreviewButton( 'on' , this );\" onmouseout=\"fncPreviewButton( 'off' , this );fncAlphaOff( this );\" onmousedown=\"fncAlphaOn( this );\" onmouseup=\"fncAlphaOff( this );\" src=\"/img/type01/cmn/querybt/preview_off_bt.gif\" width=\"72\" height=\"20\" border=\"0\" alt=\"PREVIEW\"></a>";
    }

    $aryParts["strResult"] .= "</td></tr>\n";

    unset($strCopyCheckboxObject);
}

// カラム表示
$aryParts["strColumn"] = "
						<td id=\"Column0\" nowrap>製品コード</td>
						<td id=\"Column1\" nowrap>製品名称</td>
						<td id=\"Column2\" nowrap>入力者</td>
						<td id=\"Column3\" nowrap>部門</td>
						<td id=\"Column4\" nowrap>担当者</td>
						<td id=\"Column5\" nowrap>COPY プレビュー</td>
						<td id=\"Column6\" nowrap>プレビュー</td>
	";

$aryParts["strListType"] = "p";
$aryParts["HIDDEN"] = getArrayTable($searchValue, "HIDDEN");

$objDB->close();

// HTML出力
$objTemplate = new clsTemplate();
$objTemplate->getTemplate("list/result/parts.html");
$objTemplate->replace($aryParts);
$objTemplate->replace($searchValue);
$objTemplate->complete();
echo $objTemplate->strTemplate;
