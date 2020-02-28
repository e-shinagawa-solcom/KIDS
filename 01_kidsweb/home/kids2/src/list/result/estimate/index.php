<?
/**
 *    帳票出力 見積原価計算 検索結果画面
 *
 *    @package   KIDS
 *    @copyright Copyright &copy; 2004, AntsBizShare
 *    @author    Kenji Chiba
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

// 見積原価帳票出力
// コピーファイル取得クエリ生成
$strCopyQuery = "SELECT strReportKeyCode, lngReportCode FROM t_Report WHERE lngReportClassCode = " . DEF_REPORT_ESTIMATE;

// 見積原価書取得クエリ生成
$detailConditionCount = 0;
$aryQuery[] = "SELECT DISTINCT";
$aryQuery[] = " e.lngestimatestatuscode,";
$aryQuery[] = " e.lngrevisionno,";
$aryQuery[] = " e.lngEstimateNo AS strReportKeyCode,";
$aryQuery[] = " e.strProductCode,";
$aryQuery[] = " e.strrevisecode,";
$aryQuery[] = " e.lngInputUserCode,";
$aryQuery[] = " e.lngprintcount,";
$aryQuery[] = " p.strProductName,";
$aryQuery[] = " g.strGroupDisplayCode AS strInchargeGroupDisplayCode,";
$aryQuery[] = " g.strGroupDisplayName AS strInchargeGroupDisplayName,";
$aryQuery[] = " u1.strUserDisplayCode AS strInchargeUserDisplayCode,";
$aryQuery[] = " u1.strUserDisplayName AS strInchargeUserDisplayName,";
$aryQuery[] = " u2.strUserDisplayCode AS strInputUserDisplayCode,";
$aryQuery[] = " u2.strUserDisplayName AS strInputUserDisplayName";
$aryQuery[] = "FROM m_Estimate e";
$aryQuery[] = " INNER JOIN m_estimatehistory emh on emh.lngestimateno = e.lngestimateno and emh.lngrevisionno = e.lngrevisionno";
$aryQuery[] = " INNER JOIN m_product p ";
$aryQuery[] = " ON p.strProductCode       = e.strProductCode";
$aryQuery[] = " AND p.lngrevisionno       = e.lngrevisionno";
$aryQuery[] = " AND p.strrevisecode       = e.strrevisecode";
$aryQuery[] = "  AND p.bytInvalidFlag = FALSE";
$aryQuery[] = "  INNER JOIN ( ";
$aryQuery[] = "    select lngestimateno, lngestimatedetailno, lngrevisionno, dtmdelivery";
$aryQuery[] = "    from";
$aryQuery[] = "      t_estimatedetail ";
// 納期_from
if (array_key_exists("dtmDeliveryLimitDate", $searchColumns) &&
    array_key_exists("dtmDeliveryLimitDate", $from) && $from["dtmDeliveryLimitDate"] != '') {
    $detailConditionCount += 1;
    $aryQuery[] = $detailConditionCount == 1 ? "WHERE " : "AND ";
    $aryQuery[] = " date_trunc('day', dtmdelivery)" .
    " >= '" . pg_escape_string($from["dtmDeliveryLimitDate"]) . "'";
}
// 納期_to
if (array_key_exists("dtmDeliveryLimitDate", $searchColumns) &&
    array_key_exists("dtmDeliveryLimitDate", $to) && $to["dtmDeliveryLimitDate"] != '') {
    $detailConditionCount += 1;
    $aryQuery[] = $detailConditionCount == 1 ? "WHERE " : "AND ";
    $aryQuery[] = " date_trunc('day', dtmdelivery)" .
    " <= " . "'" . pg_escape_string($to["dtmDeliveryLimitDate"]) . "'";
}
$aryQuery[] = "  ) ed ";
$aryQuery[] = "    on emh.lngestimateno = ed.lngestimateno ";
$aryQuery[] = "    and emh.lngestimatedetailno = ed.lngestimatedetailno ";
$aryQuery[] = "    and emh.lngestimatedetailrevisionno = ed.lngrevisionno ";
$aryQuery[] = " LEFT OUTER JOIN m_Group g   ON p.lngInChargeGroupCode = g.lngGroupCode";
$aryQuery[] = " LEFT OUTER JOIN m_User u1   ON p.lngInChargeUserCode  = u1.lngUserCode";
$aryQuery[] = " INNER JOIN m_User u2   ON e.lngInputUserCode     = u2.lngUserCode";
$aryQuery[] = " AND (";
// A:「承認」状態より大きい状態の発注データ
$aryQuery[] = "  e.lngestimatestatuscode > " . 0;
$aryQuery[] = "  OR";
$aryQuery[] = "  (";
$aryQuery[] = "    e.lngestimatestatuscode = " . 2;
$aryQuery[] = "  )";
$aryQuery[] = ")";
$aryQuery[] = "AND e.lngestimatestatuscode != " . DEF_ESTIMATE_DENIAL;
/////////////////////////////////////////////////////////////////
// 検索条件
/////////////////////////////////////////////////////////////////
// 作成日時_from
if (array_key_exists("dtmInsertDate", $searchColumns) &&
    array_key_exists("dtmInsertDate", $from) && $from["dtmInsertDate"] != '') {
    $aryQueryWhere[] = " date_trunc('day', e.dtmInsertDate)" .
    " >= '" . pg_escape_string($from["dtmInsertDate"]) . "'";
}
// 作成日時_to
if (array_key_exists("dtmInsertDate", $searchColumns) &&
    array_key_exists("dtmInsertDate", $to) && $to["dtmInsertDate"] != '') {
    $aryQueryWhere[] = " date_trunc('day', e.dtmInsertDate)" .
    " <= " . "'" . pg_escape_string($to["dtmInsertDate"]) . "'";
}

// 製品コード_from
if (array_key_exists("strProductCode", $searchColumns) &&
    array_key_exists("strProductCode", $from) && $from["strProductCode"] != '') {
    $aryQueryWhere[] = " e.strProductCode" .
    " >= '" . pg_escape_string($from["strProductCode"]) . "'";
}

// 製品コード_to
if (array_key_exists("strProductCode", $searchColumns) &&
    array_key_exists("strProductCode", $to) && $to["strProductCode"] != '') {
    $aryQueryWhere[] = " e.strProductCode" .
    " <= " . "'" . pg_escape_string($to["strProductCode"]) . "'";
}
// 入力者
if (array_key_exists("lngInputUserCode", $searchColumns) &&
    array_key_exists("lngInputUserCode", $searchValue)) {
    $aryQueryWhere[] = " u2.strUserDisplayCode = '" . $searchValue["lngInputUserCode"] . "'";
}
// 部門
if (array_key_exists("lngInChargeGroupCode", $searchColumns) &&
    array_key_exists("lngInChargeGroupCode", $searchValue)) {
    $aryQueryWhere[] = "g.strGroupDisplayCode = '" . $searchValue["lngInChargeGroupCode"] . "'";}
// 担当者
if (array_key_exists("lngInChargeUserCode", $searchColumns) &&
    array_key_exists("lngInChargeUserCode", $searchValue)) {
    $aryQueryWhere[] = "u1.strUserDisplayCode = '" . $searchValue["lngInChargeUserCode"] . "'";
}
$aryQueryWhere[] = " e.lngRevisionNo = ( SELECT MAX ( e2.lngRevisionNo ) FROM m_Estimate e2 WHERE e.lngEstimateNo = e2.lngEstimateNo )";
$aryQueryWhere[] = " 0 <= ( SELECT MIN ( e3.lngRevisionNo ) FROM m_Estimate e3 WHERE e.lngEstimateNo = e3.lngEstimateNo )";
$aryQuery[] = " WHERE " . join(" AND ", $aryQueryWhere);
unset($aryQueryWhere);
$aryQuery[] = "ORDER BY e.strProductCode DESC";

// ナンバーをキーとする連想配列に帳票コードを取得
list($lngResultID, $lngResultNum) = fncQuery($strCopyQuery, $objDB);

for ($i = 0; $i < $lngResultNum; $i++) {
    $objResult = $objDB->fetchObject($lngResultID, $i);
    $aryReportCode[$objResult->strreportkeycode] = $objResult->lngreportcode;
}

if ($lngResultNum > 0) {
    $objDB->freeResult($lngResultID);
}

$strQuery = join("\n", $aryQuery);

// 帳票データ取得クエリ実行・テーブル生成
list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);

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
    $lngErrorCode = 1507;
    $aryErrorMessage = "";
}
if ($errorFlag) {
    // エラー画面の戻り先
    // $strReturnPath = "../list/po/index.php?strSessionID=" . $aryData["strSessionID"];

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

    $objDB->close();

    exit;
}

for ($i = 0; $i < $lngResultNum; $i++) {
    $objResult = $objDB->fetchObject($lngResultID, $i);

    $aryParts["strResult"] .= "<tr class=\"Segs\">\n";

    $aryParts["strResult"] .= "<td>" . $objResult->strproductcode . "_". $objResult->strrevisecode . "</td>\n";
    $aryParts["strResult"] .= "<td>" . $objResult->strproductname . "</td>\n";
    $aryParts["strResult"] .= "<td>" . $objResult->strinputuserdisplaycode . ":" . $objResult->strinputuserdisplayname . "</td>\n";
    $aryParts["strResult"] .= "<td>" . $objResult->strinchargegroupdisplaycode . ":" . $objResult->strinchargegroupdisplayname . "</td>\n";
    $aryParts["strResult"] .= "<td>" . $objResult->strinchargeuserdisplaycode . ":" . $objResult->strinchargeuserdisplayname . "</td>\n";

    $aryParts["strResult"] .= "<td align=center>";

    // 印刷回数が0より大きい場合、コピー帳票出力ボタン表示
    if (intval($objResult->lngprintcount) > 0) {
        // コピー帳票出力ボタン表示
        $aryParts["strResult"] .= "<a href=\"#\"><img onclick=\"fncListOutput( '/list/result/frameset.php?strSessionID=" . $searchValue["strSessionID"] . "&lngReportClassCode=" . DEF_REPORT_ESTIMATE . "&strReportKeyCode=" . $objResult->strreportkeycode . "' );return false;\" onmouseover=\"fncCopyPreviewButton( 'on' , this );\" onmouseout=\"fncCopyPreviewButton( 'off' , this );fncAlphaOff( this );\" onmousedown=\"fncAlphaOn( this );\" onmouseup=\"fncAlphaOff( this );\" src=\"/img/type01/list/copybig_off_bt.gif\" width=\"72\" height=\"20\" border=\"0\" alt=\"COPY PREVIEW\"></a>";
    }

    $aryParts["strResult"] .= "</td>\n<td align=center>";

    // コピーファイルパスが存在しない または コピー解除権限がある場合、
    // 帳票出力ボタン表示
    if ($aryReportCode[$objResult->strreportkeycode] == null || fncCheckAuthority(DEF_FUNCTION_LO4, $objAuth)) {
        // 帳票出力ボタン表示
        $aryParts["strResult"] .= "<a href=\"#\"><img onclick=\"fncListOutput( '/list/result/frameset.php?strSessionID=" . $searchValue["strSessionID"] . "&lngReportClassCode=" . DEF_REPORT_ESTIMATE . "&strReportKeyCode=" . $objResult->strreportkeycode . "' );return false;\" onmouseover=\"fncPreviewButton( 'on' , this );\" onmouseout=\"fncPreviewButton( 'off' , this );fncAlphaOff( this );\" onmousedown=\"fncAlphaOn( this );\" onmouseup=\"fncAlphaOff( this );\" src=\"/img/type01/cmn/querybt/preview_off_bt.gif\" width=\"72\" height=\"20\" border=\"0\" alt=\"PREVIEW\"></a>";
    }

    $aryParts["strResult"] .= "</td></tr>\n";

    unset($strCopyCheckboxObject);
}

$aryParts["strColumn"] = "
					<td id=\"Column0\" nowrap>製品コード</td>
					<td id=\"Column1\" nowrap>製品名称</td>
					<td id=\"Column2\" nowrap>入力者</td>
					<td id=\"Column3\" nowrap>部門</td>
					<td id=\"Column4\" nowrap>担当者</td>
					<td id=\"Column5\" nowrap>COPY プレビュー</td>
					<td id=\"Column6\" nowrap>プレビュー</td>
";

$aryParts["strListType"] = "estimate";
$aryParts["HIDDEN"] = getArrayTable($searchValue, "HIDDEN");

$objDB->close();

// HTML出力
$objTemplate = new clsTemplate();
$objTemplate->getTemplate("list/result/parts.html");
$objTemplate->replace($aryParts);
$objTemplate->replace($searchValue);
$objTemplate->complete();
echo $objTemplate->strTemplate;
