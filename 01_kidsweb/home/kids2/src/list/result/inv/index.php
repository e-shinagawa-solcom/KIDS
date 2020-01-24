<?
/**
 *    帳票出力 請求書 検索結果画面
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

// 納品書帳票出力
// コピーファイル取得クエリ生成
$strCopyQuery = "SELECT strReportKeyCode, lngReportCode FROM t_Report WHERE lngReportClassCode = " . DEF_REPORT_INV;

// 納品書取得クエリ生成
$aryQuery[] = "SELECT";
$aryQuery[] = "  distinct i.strinvoicecode";
$aryQuery[] = "  , cust.strCompanyDisplayCode";
$aryQuery[] = "  , cust.strCompanyDisplayname";
$aryQuery[] = "  , to_char(i.dtminvoicedate, 'yyyy/mm/dd') as dtminvoicedate";
$aryQuery[] = "  , i.strtaxclassname";
$aryQuery[] = "  , to_char(i.curthismonthamount, '9,999,999,990') AS curthismonthamount";
$aryQuery[] = "  , u1.strGroupDisplayCode AS strGroupDisplayCode";
$aryQuery[] = "  , u1.strGroupDisplayName AS strGroupDisplayName";
$aryQuery[] = "  , u1.strUserDisplayCode AS strUserDisplayCode";
$aryQuery[] = "  , u1.strUserDisplayName AS strUserDisplayName";
$aryQuery[] = "  , i.lngprintcount";
$aryQuery[] = "  , i.lnginvoiceno AS strReportKeyCode ";
$aryQuery[] = "FROM";
$aryQuery[] = "  m_invoice i ";
$aryQuery[] = "  inner join ( ";
$aryQuery[] = "    SELECT";
$aryQuery[] = "      MAX(lngRevisionNo) lngRevisionNo";
$aryQuery[] = "      , strinvoicecode ";
$aryQuery[] = "    FROM";
$aryQuery[] = "      m_invoice ";
$aryQuery[] = "    where";
$aryQuery[] = "      bytInvalidFlag = false ";
$aryQuery[] = "    group by";
$aryQuery[] = "      strinvoicecode";
$aryQuery[] = "  ) i1 ";
$aryQuery[] = "    on i.strinvoicecode = i1.strinvoicecode ";
$aryQuery[] = "    AND i.lngrevisionno = i1.lngRevisionNo ";
$aryQuery[] = "  left join (select distinct lnginvoiceno, lngrevisionno  from  t_invoicedetail id1 ";
$aryQuery[] = "    left join m_Company c ";
$aryQuery[] = "    on id1.lngdeliveryplacecode = c.lngCompanyCode ";
// 納品先
if (array_key_exists("lngDeliveryPlaceCode", $searchColumns) &&
    array_key_exists("lngDeliveryPlaceCode", $searchValue)) {
    $aryQuery[] = " where c.strCompanyDisplayCode = '" . $searchValue["lngDeliveryPlaceCode"] . "'";
}
$aryQuery[] = "    ) id on id.lnginvoiceno = i.lnginvoiceno and id.lngrevisionno = i.lngrevisionno";
$aryQuery[] = "  left join m_Company cust ";
$aryQuery[] = "    on i.lngcustomercode = cust.lngCompanyCode ";
$aryQuery[] = "  left join ( ";
$aryQuery[] = "    select";
$aryQuery[] = "      u.lngUserCode";
$aryQuery[] = "      , u.strUserDisplayCode";
$aryQuery[] = "      , u.strUserDisplayName";
$aryQuery[] = "      , g.strGroupDisplayCode";
$aryQuery[] = "      , g.strGroupDisplayName ";
$aryQuery[] = "    from";
$aryQuery[] = "      m_user u";
$aryQuery[] = "      , m_Group g";
$aryQuery[] = "      , m_grouprelation gr ";
$aryQuery[] = "    where";
$aryQuery[] = "      g.lnggroupcode = gr.lnggroupcode ";
$aryQuery[] = "      and u.lngusercode = gr.lngusercode ";
$aryQuery[] = "      and gr.bytdefaultflag = true";
$aryQuery[] = "  ) u1 ";
$aryQuery[] = "    on i.lngUserCode = u1.lngUserCode ";
$aryQuery[] = "  left join m_user u2 ";
$aryQuery[] = "    on i.lngInsertUserCode = u2.lngUserCode ";
$aryQuery[] = "WHERE";
$aryQuery[] = "  not exists ( ";
$aryQuery[] = "    select";
$aryQuery[] = "      i1.strinvoicecode ";
$aryQuery[] = "    from";
$aryQuery[] = "      ( ";
$aryQuery[] = "        SELECT";
$aryQuery[] = "          min(lngRevisionNo) lngRevisionNo";
$aryQuery[] = "          , strinvoicecode ";
$aryQuery[] = "        FROM";
$aryQuery[] = "          m_invoice ";
$aryQuery[] = "        where";
$aryQuery[] = "          bytInvalidFlag = false ";
$aryQuery[] = "        group by";
$aryQuery[] = "          strinvoicecode";
$aryQuery[] = "      ) as i1 ";
$aryQuery[] = "    where";
$aryQuery[] = "      i1.strinvoicecode = i.strinvoicecode";
$aryQuery[] = "      AND i1.lngRevisionNo < 0";
$aryQuery[] = "  ) ";
/////////////////////////////////////////////////////////////////
// 検索条件
/////////////////////////////////////////////////////////////////
// 請求日_from
if (array_key_exists("dtmInvoiceDate", $searchColumns) &&
    array_key_exists("dtmInvoiceDate", $from) && $from["dtmInvoiceDate"]!='') {
    $aryQuery[] = " AND date_trunc('day', i.dtmInvoiceDate )" .
    " >= '" . pg_escape_string($from["dtmInvoiceDate"]) . "'";
}
// 請求日_to
if (array_key_exists("dtmInvoiceDate", $searchColumns) &&
    array_key_exists("dtmInvoiceDate", $to) && $to["dtmInvoiceDate"]!='') {
    $aryQuery[] = " AND date_trunc('day', i.dtmInvoiceDate )" .
    " <= " . "'" . pg_escape_string($to["dtmInvoiceDate"]) . "'";
}
// 入力日_from
if (array_key_exists("dtmInsertDate", $searchColumns) &&
    array_key_exists("dtmInsertDate", $from) && $from["dtmInsertDate"]!='') {
    $aryQuery[] = " AND date_trunc('day', i.dtmInsertDate )" .
    " >= '" . pg_escape_string($from["dtmInsertDate"]) . "'";
}
// 入力日_to
if (array_key_exists("dtmInsertDate", $searchColumns) &&
    array_key_exists("dtmInsertDate", $to) && $to["dtmInsertDate"]!='') {
    $aryQuery[] = " AND date_trunc('day', i.dtmInsertDate )" .
    " <= " . "'" . pg_escape_string($to["dtmInsertDate"]) . "'";
}
// 消費税区分
if (array_key_exists("lngTaxClassCode", $searchColumns) &&
    array_key_exists("lngTaxClassCode", $searchValue)) {
    $aryQuery[] = " AND i.lngtaxclasscode = '" . $searchValue["lngTaxClassCode"] . "'";
}
// 請求書NO.
if (array_key_exists("strInvoiceCode", $searchColumns) &&
    array_key_exists("strInvoiceCode", $searchValue)) {
    $strInvoiceCodeArray = explode(",", $searchValue["strInvoiceCode"]);
    $aryQuery[] = " AND (";
    $count = 0;
    foreach ($strInvoiceCodeArray as $strInvoiceCode) {
        $count += 1;
        if ($count != 1) {
            $aryQuery[] = " OR ";
        }
        if (strpos($strInvoiceCode, '-') !== false) {
            $aryQuery[] = "(i.strInvoiceCode" .
            " between '" . explode("-", $strInvoiceCode)[0] . "'" .
            " AND " . "'" . explode("-", $strInvoiceCode)[1] . "')";
        } else {
            $aryQuery[] = "i.strInvoiceCode = '" . $strInvoiceCode . "'";
        }
    }
    $aryQuery[] = ")";
}
// 顧客
if (array_key_exists("lngCustomerCompanyCode", $searchColumns) &&
    array_key_exists("lngCustomerCompanyCode", $searchValue)) {
    $aryQuery[] = " AND cust.strCompanyDisplayCode = '" . $searchValue["lngCustomerCompanyCode"] . "'";
}
// 起票者
if (array_key_exists("lngInsertUserCode", $searchColumns) &&
    array_key_exists("lngInsertUserCode", $searchValue)) {
    $aryQuery[] = " AND u1.strUserDisplayCode = '" . $searchValue["lngInsertUserCode"] . "'";
}
// 入力者
if (array_key_exists("lngInputUserCode", $searchColumns) &&
    array_key_exists("lngInputUserCode", $searchValue)) {
    $aryQuery[] = " AND u2.strUserDisplayCode = '" . $searchValue["lngInputUserCode"] . "'";
}
$aryQuery[] = "ORDER BY strinvoicecode DESC";

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
    $lngErrorCode = 9214;
    $aryErrorMessage = "";
}
if ($errorFlag) {

    $strMessage = fncOutputError($lngErrorCode, DEF_WARNING, $aryErrorMessage, false, "", $objDB);

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
for ($i = 0; $i < $lngResultNum; $i++) {
    $objResult = $objDB->fetchObject($lngResultID, $i);

    $aryParts["strResult"] .= "<tr class=\"Segs\">\n";

    $aryParts["strResult"] .= "<td>" . $objResult->strinvoicecode  . "</td>\n";
    $aryParts["strResult"] .= "<td>" . ($objResult->strcompanydisplaycode == "" ? "&nbsp;" : ($objResult->strcompanydisplaycode . ":" . $objResult->strcompanydisplayname)) . "</td>\n";
    $aryParts["strResult"] .= "<td>" . $objResult->dtminvoicedate . "</td>\n";
    $aryParts["strResult"] .= "<td>" . $objResult->strtaxclassname  . "</td>\n";
    $aryParts["strResult"] .= "<td>" . $objResult->curthismonthamount . "</td>\n";
    $aryParts["strResult"] .= "<td>" . ($objResult->strgroupdisplaycode == "" ? "&nbsp;" : ($objResult->strgroupdisplaycode . ":" . $objResult->strgroupdisplayname) ). "</td>\n";
    $aryParts["strResult"] .= "<td>" . ($objResult->struserdisplaycode == "" ? "&nbsp;" : ($objResult->struserdisplaycode . ":" . $objResult->struserdisplayname)) . "</td>\n";

    $aryParts["strResult"] .= "<td align=center>";

    // 印刷回数が0より大きい場合、コピー帳票出力ボタン表示
    if (intval($objResult->lngprintcount) > 0) {
        // コピー帳票出力ボタン表示
        $aryParts["strResult"] .= "<a href=\"#\"><img onclick=\"fncListOutput( '/list/result/frameset.php?strSessionID=" . $searchValue["strSessionID"] . "&lngReportClassCode=" . DEF_REPORT_INV . "&strReportKeyCode=" . $objResult->strreportkeycode . "&lngReportCode=" . $aryReportCode[$objResult->strreportkeycode] . "' );return false;\" onmouseover=\"fncCopyPreviewButton( 'on' , this );\" onmouseout=\"fncCopyPreviewButton( 'off' , this );fncAlphaOff( this );\" onmousedown=\"fncAlphaOn( this );\" onmouseup=\"fncAlphaOff( this );\" src=\"/img/type01/list/copybig_off_bt.gif\" width=\"72\" height=\"20\" border=\"0\" alt=\"COPY PREVIEW\"></a>";
    }

    $aryParts["strResult"] .= "</td>\n<td align=center>";

    // コピーファイルパスが存在しない または コピー解除権限がある場合、
    // 帳票出力ボタン表示
    if ($aryReportCode[$objResult->strreportkeycode] == null || fncCheckAuthority(DEF_FUNCTION_LO4, $objAuth)) {
        // 帳票出力ボタン表示
        $aryParts["strResult"] .= "<a href=\"#\"><img onclick=\"fncListOutput( '/list/result/frameset.php?strSessionID=" . $searchValue["strSessionID"] . "&lngReportClassCode=" . DEF_REPORT_INV . "&strReportKeyCode=" . $objResult->strreportkeycode . "' );return false;\" onmouseover=\"fncPreviewButton( 'on' , this );\" onmouseout=\"fncPreviewButton( 'off' , this );fncAlphaOff( this );\" onmousedown=\"fncAlphaOn( this );\" onmouseup=\"fncAlphaOff( this );\" src=\"/img/type01/cmn/querybt/preview_off_bt.gif\" width=\"72\" height=\"20\" border=\"0\" alt=\"PREVIEW\"></a>";
    }

    $aryParts["strResult"] .= "</td></tr>\n";

    unset($strCopyCheckboxObject);
}

$aryParts["strColumn"] = "
						<td id=\"Column0\" nowrap>請求書NO.</td>
						<td id=\"Column2\" nowrap>顧客</td>
						<td id=\"Column1\" nowrap>請求日</td>
						<td id=\"Column1\" nowrap>課税区分</td>
						<td id=\"Column1\" nowrap>ご請求金額</td>
						<td id=\"Column3\" nowrap>部門</td>
						<td id=\"Column4\" nowrap>担当者</td>
						<td id=\"Column5\" nowrap>COPY プレビュー</td>
						<td id=\"Column6\" nowrap>プレビュー</td>
	";

$aryParts["strListType"] = "inv";
$aryParts["HIDDEN"] = getArrayTable($searchValue, "HIDDEN");

$objDB->close();

// HTML出力
$objTemplate = new clsTemplate();
$objTemplate->getTemplate("list/result/parts.html");
$objTemplate->replace($aryParts);
$objTemplate->replace($searchValue);
$objTemplate->complete();
echo $objTemplate->strTemplate;
