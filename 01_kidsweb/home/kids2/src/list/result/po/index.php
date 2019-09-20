<?
/**
 *    帳票出力 発注書 検索結果画面
 *
 *    @package   KIDS
 *    @license   http://www.wiseknot.co.jp/
 *    @copyright Copyright &copy; 2003, Wiseknot
 *    @author    Kenji Chiba <k-chiba@wiseknot.co.jp>
 *    @access    public
 *    @version   1.00
 *
 */
// 検索結果画面( * は指定帳票のファイル名 )
// *.php -> strSessionID       -> index.php

// 印刷画面へ
// index.php -> strSessionID       -> index.php
// index.php -> lngReportCode      -> index.php

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

// 発注帳票出力
// コピーファイル取得クエリ生成
$strCopyQuery = "SELECT strReportKeyCode, lngReportCode FROM t_Report WHERE lngReportClassCode = " . DEF_REPORT_ORDER;

// 発注書取得クエリ生成
$aryQuery[] = "SELECT distinct";
$aryQuery[] = "  po.strOrderCode AS strOrderNo";
$aryQuery[] = "  , po.lnginsertusercode";
$aryQuery[] = "  , po.strinsertusername";
$aryQuery[] = "  , c.strCompanyDisplayCode";
$aryQuery[] = "  , c.strCompanyDisplayName";
$aryQuery[] = "  , g.strgroupdisplaycode";
$aryQuery[] = "  , g.strgroupdisplayname";
$aryQuery[] = "  , u2.struserdisplaycode";
$aryQuery[] = "  , u2.struserdisplayname";
$aryQuery[] = "  , po.lngpurchaseorderno AS strReportKeyCode ";
$aryQuery[] = "FROM";
$aryQuery[] = "  m_purchaseorder po ";
$aryQuery[] = "  inner join ( ";
$aryQuery[] = "    select";
$aryQuery[] = "      max(lngrevisionno) lngrevisionno";
$aryQuery[] = "      , strordercode ";
$aryQuery[] = "    from";
$aryQuery[] = "      m_purchaseorder ";
$aryQuery[] = "    group by";
$aryQuery[] = "      strordercode";
$aryQuery[] = "  ) po1 ";
$aryQuery[] = "    on po.lngrevisionno = po1.lngrevisionno ";
$aryQuery[] = "    and po.strordercode = po1.strordercode ";
$aryQuery[] = "  LEFT JOIN m_user u1 ";
$aryQuery[] = "    on u1.lngusercode = po.lnginsertusercode ";
$aryQuery[] = "  LEFT JOIN m_user u2 ";
$aryQuery[] = "    on u2.lngusercode = po.lngusercode ";
$aryQuery[] = "  LEFT JOIN m_Company c ";
$aryQuery[] = "    ON po.lngcustomercode = c.lngCompanyCode ";
$aryQuery[] = "  LEFT JOIN m_group g ";
$aryQuery[] = "    ON po.lnggroupcode = g.lnggroupcode ";
$aryQuery[] = "WHERE";
$aryQuery[] = "  po.strordercode not in ( ";
$aryQuery[] = "    select";
$aryQuery[] = "      po2.strordercode ";
$aryQuery[] = "    from";
$aryQuery[] = "      ( ";
$aryQuery[] = "        SELECT";
$aryQuery[] = "          min(lngRevisionNo) lngRevisionNo";
$aryQuery[] = "          , strordercode ";
$aryQuery[] = "        FROM";
$aryQuery[] = "          m_purchaseorder ";
$aryQuery[] = "        group by";
$aryQuery[] = "          strordercode";
$aryQuery[] = "      ) as po2 ";
$aryQuery[] = "    where";
$aryQuery[] = "      po2.lngRevisionNo < 0";
$aryQuery[] = "  ) ";
/////////////////////////////////////////////////////////////////
// 検索条件
/////////////////////////////////////////////////////////////////
// 作成日時
if (array_key_exists("dtmInsertDate", $searchColumns) &&
    array_key_exists("dtmInsertDate", $from) &&
    array_key_exists("dtmInsertDate", $to)) {
    $aryQuery[] = " AND date_trunc('day', po.dtmInsertDate )" .
    " between '" . pg_escape_string($from["dtmInsertDate"]) . "'" .
    " AND " . "'" . pg_escape_string($to["dtmInsertDate"]) . "'";
}
// 発注書ＮＯ.
if (array_key_exists("strOrderCode", $searchColumns) &&
    array_key_exists("strOrderCode", $from) &&
    array_key_exists("strOrderCode", $to)) {
    $aryQuery[] = " AND po.strOrderCode" .
    " between '" . pg_escape_string($from["strOrderCode"]) . "'" .
    " AND " . "'" . pg_escape_string($to["strOrderCode"]) . "'";
}
// 製品コード
if (array_key_exists("strProductCode", $searchColumns) &&
    array_key_exists("strProductCode", $from) &&
    array_key_exists("strProductCode", $to)) {
    $aryQuery[] = " AND po.strProductCode" .
    " between '" . pg_escape_string($from["strProductCode"]) . "'" .
    " AND " . "'" . pg_escape_string($to["strProductCode"]) . "'";
}
// 入力者
if (array_key_exists("lngInputUserCode", $searchColumns) &&
    array_key_exists("lngInputUserCode", $searchValue)) {
    $aryQuery[] = " AND u1.struserdisplaycode = '" . $searchValue["lngInputUserCode"] . "'";
}
// 仕入先
if (array_key_exists("lngCustomerCompanyCode", $searchColumns) &&
    array_key_exists("lngCustomerCompanyCode", $searchValue)) {
    $aryQuery[] = " AND c.strCompanyDisplayCode = '" . $searchValue["lngCustomerCompanyCode"] . "'";
}
// 部門
if (array_key_exists("lngInChargeGroupCode", $searchColumns) &&
    array_key_exists("lngInChargeGroupCode", $searchValue)) {
    $aryQuery[] = " AND g.strgroupdisplaycode = '" . $searchValue["lngInChargeGroupCode"] . "'";
}
// 担当者
if (array_key_exists("lngInChargeUserCode", $searchColumns) &&
    array_key_exists("lngInChargeUserCode", $searchValue)) {
    $aryQuery[] = " AND u2.struserdisplaycode = '" . $searchValue["lngInChargeUserCode"] . "'";
}
$aryQuery[] = "ORDER BY strOrderNo DESC";

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
$strQuery = implode("\n", $aryQuery);

list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);
for ($i = 0; $i < $lngResultNum; $i++) {
    $objResult = $objDB->fetchObject($lngResultID, $i);

    $aryParts["strResult"] .= "<tr class=\"Segs\">\n";

    $aryParts["strResult"] .= "<td>" . $objResult->strorderno . "</td>\n";
    $aryParts["strResult"] .= "<td>" . $objResult->strinsertusercode . ":" . $objResult->strinsertusername . "</td>\n";
    $aryParts["strResult"] .= "<td>" . $objResult->strcompanydisplaycode . ":" . $objResult->strcompanydisplayname . "</td>\n";
    $aryParts["strResult"] .= "<td>" . $objResult->strgroupdisplaycode . ":" . $objResult->strgroupdisplayname . "</td>\n";
    $aryParts["strResult"] .= "<td>" . $objResult->struserdisplaycode . ":" . $objResult->struserdisplayname . "</td>\n";

    $aryParts["strResult"] .= "<td align=center>";

    // コピーファイルパスが存在している場合、コピー帳票出力ボタン表示
    if ($aryReportCode[$objResult->strreportkeycode] != null) {
        // コピー帳票出力ボタン表示
        $aryParts["strResult"] .= "<a href=\"#\"><img onclick=\"fncListOutput( '/list/result/frameset.php?strSessionID=" . $searchValue["strSessionID"] . "&lngReportClassCode=" . DEF_REPORT_ORDER . "&strReportKeyCode=" . $objResult->strreportkeycode . "&lngReportCode=" . $aryReportCode[$objResult->strreportkeycode] . "' );return false;\" onmouseover=\"fncCopyPreviewButton( 'on' , this );\" onmouseout=\"fncCopyPreviewButton( 'off' , this );fncAlphaOff( this );\" onmousedown=\"fncAlphaOn( this );\" onmouseup=\"fncAlphaOff( this );\" src=\"/img/type01/list/copybig_off_bt.gif\" width=\"72\" height=\"20\" border=\"0\" alt=\"COPY PREVIEW\"></a>";
    }

    $aryParts["strResult"] .= "</td>\n<td align=center>";

    // コピーファイルパスが存在しない または コピー解除権限がある場合、
    // 帳票出力ボタン表示
    if ($aryReportCode[$objResult->strreportkeycode] == null || fncCheckAuthority(DEF_FUNCTION_LO4, $objAuth)) {
        // 帳票出力ボタン表示
        $aryParts["strResult"] .= "<a href=\"#\"><img onclick=\"fncListOutput( '/list/result/frameset.php?strSessionID=" . $searchValue["strSessionID"] . "&lngReportClassCode=" . DEF_REPORT_ORDER . "&strReportKeyCode=" . $objResult->strreportkeycode . "' );return false;\" onmouseover=\"fncPreviewButton( 'on' , this );\" onmouseout=\"fncPreviewButton( 'off' , this );fncAlphaOff( this );\" onmousedown=\"fncAlphaOn( this );\" onmouseup=\"fncAlphaOff( this );\" src=\"/img/type01/cmn/querybt/preview_off_bt.gif\" width=\"72\" height=\"20\" border=\"0\" alt=\"PREVIEW\"></a>";
    }

    $aryParts["strResult"] .= "</td></tr>\n";

    unset($strCopyCheckboxObject);
}

$aryParts["strColumn"] = "
						<td id=\"Column0\" nowrap>発注 No.</td>
						<td id=\"Column1\" nowrap>入力者</td>
						<td id=\"Column2\" nowrap>仕入先</td>
						<td id=\"Column3\" nowrap>部門</td>
						<td id=\"Column4\" nowrap>担当者</td>
						<td id=\"Column5\" nowrap>COPY プレビュー</td>
						<td id=\"Column6\" nowrap>プレビュー</td>
	";

$aryParts["strListType"] = "po";
$aryParts["HIDDEN"] = getArrayTable($searchValue, "HIDDEN");

$objDB->close();

// HTML出力
$objTemplate = new clsTemplate();
$objTemplate->getTemplate("list/result/parts.html");
$objTemplate->replace($aryParts);
$objTemplate->replace($searchValue);
$objTemplate->complete();
echo $objTemplate->strTemplate;
