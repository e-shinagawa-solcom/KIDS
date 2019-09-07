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
$aryQuery[] = " u1.strUserDisplayCode AS strInputUserDisplayCode,";
$aryQuery[] = " u1.strUserDisplayName AS strInputUserDisplayName,";
$aryQuery[] = " g.strGroupDisplayCode AS strInChargeGroupDisplayCode,";
$aryQuery[] = " g.strGroupDisplayName AS strInChargeGroupDisplayName,";
$aryQuery[] = " u2.strUserDisplayCode AS strInChargeUserDisplayCode,";
$aryQuery[] = " u2.strUserDisplayName AS strInChargeUserDisplayName,";
$aryQuery[] = " u2.strUserDisplayCode AS strInChargeUserDisplayCode,";
$aryQuery[] = " u2.strUserDisplayName AS strInChargeUserDisplayName,";
$aryQuery[] = " gp.lngProductNo AS strReportKeyCode ";
$aryQuery[] = "FROM ( ";
$aryQuery[] = " select p1.*  from m_product p1 ";
$aryQuery[] = " inner join (select max(lngproductno) lngproductno, strproductcode from m_Product group by strProductCode) p2";
$aryQuery[] = " on p1.lngproductno = p2.lngproductno";
$aryQuery[] = " ) p ";
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
/////////////////////////////////////////////////////////////////
// 検索条件
/////////////////////////////////////////////////////////////////
// 作成日時
if (array_key_exists("dtmInsertDate", $searchColumns) &&
    array_key_exists("dtmInsertDate", $from) &&
    array_key_exists("dtmInsertDate", $to)) {
    $aryQuery[] = " AND date_trunc('day', p.dtmInsertDate)" .
    " between '" . pg_escape_string($from["dtmInsertDate"]) . "'" .
    " AND " . "'" . pg_escape_string($to["dtmInsertDate"]) . "'";
}
// 企画進行状況
if (array_key_exists("lngGoodsPlanProgressCode", $searchColumns) &&
    array_key_exists("lngGoodsPlanProgressCode", $searchValue)) {
		$aryQuery[] = " AND gp.lngGoodsPlanProgressCode = " . $searchValue["lngGoodsPlanProgressCode"];
}
// 改訂日時
if (array_key_exists("dtmRevisionDate", $searchColumns) &&
    array_key_exists("dtmRevisionDate", $from) &&
    array_key_exists("dtmRevisionDate", $to)) {
    $aryQuery[] = " AND date_trunc('day', p.dtmUpdateDate)" .
    " between '" . pg_escape_string($from["dtmRevisionDate"]) . "'" .
    " AND " . "'" . pg_escape_string($to["dtmRevisionDate"]) . "'";
}
// 製品コード
if (array_key_exists("strProductCode", $searchColumns) &&
    array_key_exists("strProductCode", $from) &&
    array_key_exists("strProductCode", $to)) {
    $aryQuery[] = " AND p.strProductCode" .
    " between '" . pg_escape_string($from["strProductCode"]) . "'" .
    " AND " . "'" . pg_escape_string($to["strProductCode"]) . "'";
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
    $aryParts["strResult"] .= "<td>" . $objResult->strproductcode . "</td>\n";
    $aryParts["strResult"] .= "<td>" . $objResult->strproductname . "</td>\n";
    $aryParts["strResult"] .= "<td>" . $objResult->strinputuserdisplaycode . ":" . $objResult->strinputuserdisplayname . "</td>\n";
    $aryParts["strResult"] .= "<td>" . $objResult->strinchargegroupdisplaycode . ":" . $objResult->strinchargegroupdisplayname . "</td>\n";
    $aryParts["strResult"] .= "<td>" . $objResult->strinchargeuserdisplaycode . ":" . $objResult->strinchargeuserdisplayname . "</td>\n";
    $aryParts["strResult"] .= "<td align=center>";

    // コピーファイルパスが存在している場合、コピー帳票出力ボタン表示
    if ($aryReportCode[$objResult->strreportkeycode] != null) {
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
