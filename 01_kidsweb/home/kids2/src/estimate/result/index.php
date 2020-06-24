
<?
/**
 *    見積原価管理 検索結果表示画面
 *
 *    @package   kuwagata
 *    @license   http://www.wiseknot.co.jp/
 *    @copyright Copyright &copy; 2004, AntsBizShare
 *    @author    Kenji Chiba
 *    @access    public
 *    @version   1.00
 *
 */
// -------------------------------------------------------------------------

// 設定読み込み
include_once 'conf.inc';
require_once LIB_DEBUGFILE;
require_once SRC_ROOT . '/mold/lib/UtilSearchForm.class.php';

// ライブラリ読み込み
require_once LIB_FILE;
require_once SRC_ROOT . "estimate/cmn/makeHTML.php";
require SRC_ROOT . "search/cmn/lib_search.php";

// DB接続
$objDB = new clsDB();
$objAuth = new clsAuth();
$objDB->open("", "", "", "");

$aryData = $_REQUEST;

$strSessionID = $aryData["strSessionID"];

// フォームデータから各カテゴリの振り分けを行う
$options = UtilSearchForm::extractArrayByOption($aryData);
$isDisplay = UtilSearchForm::extractArrayByIsDisplay($aryData);
$isSearch = UtilSearchForm::extractArrayByIsSearch($aryData);
$from = UtilSearchForm::extractArrayByFrom($aryData);
$to = UtilSearchForm::extractArrayByTo($aryData);

$optionColumns = array();
// オプション項目の抽出
foreach ($options as $key => $flag) {
    if ($flag == "on") {
        $optionColumns[$key] = $key;
    }
}

$displayColumns = array_keys($isDisplay);
$searchColumns = array_keys($isSearch);

//////////////////////////////////////////////////////////////////////////
// POST(一部GET)データ取得
//////////////////////////////////////////////////////////////////////////

$searchData = array();

// 検索条件の配列を生成する
foreach ($searchColumns as $column) {
    //     範囲指定の場合
    if (isset($from[$column]) || isset($to[$column])) {
        $searchData[$column] = array(
            'from' => $from[$column],
            'to' => $to[$column],
        );
    } else {
        $searchData[$column] = $aryData[$column];
    }
}

//////////////////////////////////////////////////////////////////////////
// セッション、権限確認
//////////////////////////////////////////////////////////////////////////
// セッション確認
$objAuth = fncIsSession($aryData["strSessionID"], $objAuth, $objDB);

// 権限確認
if (!fncCheckAuthority(DEF_FUNCTION_E2, $objAuth)) {
    fncOutputError(9060, DEF_WARNING, "アクセス権限がありません。", true, "", $objDB);
}

// 検索結果のカラム表記の言語設定
$aryColumnLang = array(
    "btnDetail" => "詳細",
    "btnHistory" => "履歴",
    "dtmInsertDate" => "作成日",
    "strProductCode" => "製品コード",
    "strProductName" => "製品名称(日本語)",
    "strProductEnglishName" => "製品名称(英語)",
    "lngInChargeGroupCode" => "営業部署",
    "lngInChargeUserCode" => "担当",
    "lngDevelopUserCode" => "開発担当者",
    "dtmDeliveryLimitDate" => "客先納品日",
    "curRetailPrice" => "上代",
    "lngInputUserCode" => "入力者",
    "lngCartonQuantity" => "カートン入数",
    "lngProductionQuantity" => "生産数",
    "curSalesAmount" => "製品売上高",
    "curSalesProfit" => "製品利益",
    "curSalesProfitRate" => "製品利益率",
    "curFixedCostSales" => "固定費売上高",
    "curFixedCostSalesProfit" => "固定費利益",
    "curFixedCostSalesProfitRate" => "固定費利益率",
    "curTotalSales" => "総売上高",
    "curTotalPrice" => "売上総利益",
    "curTotalPriceRate" => "売上総利益率",
    "curIndirectManufacturingCost" => "間接製造経費",
    "curStandardRate" => "間接製造経費率",
    "curProfit" => "営業利益",
    "curProfitRate" => "営業利益率",
    "curMemberCostPieces" => "pcs部材費用",
    "curMemberCost" => "部材費合計",
    "curFixedCostPieces" => "pcs償却費用",
    "curFixedCost" => "償却費合計",
    "curManufacturingCostPieces" => "pcsコスト",
    "curManufacturingCost" => "製造費用合計",
    "btnDelete" => "削除",
);

//////////////////////////////////////////////////////////////////////////
// 文字列チェック
//////////////////////////////////////////////////////////////////////////

$aryCheck["strSessionID"] = "null:numenglish(32,32)";
$aryCheck["strProductName"] = "length(0,80)";
$aryCheck["strProductEnglishName"] = "ascii(0,80)";
$aryCheck["lngInChargeGroupCode"] = "numenglish(0,32767)";
$aryCheck["lngInChargeUserCode"] = "numenglish(0,32767)";
$aryCheck["lngDevelopUserCode"] = "numenglish(0,32767)";
$aryCheck["lngInputUserCode"] = "numenglish(0,32767)";
$aryCheck["dtmCreationDateFrom"] = "date(/)";
$aryCheck["dtmCreationDateTo"] = "date(/)";
$aryCheck["dtmDeliveryLimitDateFrom"] = "date(/)";
$aryCheck["dtmDeliveryLimitDateTo"] = "date(/)";

// 文字列チェック
$aryCheckResult = fncAllCheck($aryData, $aryCheck);

// エラーメッセージを出力する
$strErrorMessage = array();

foreach ($aryCheckResult as $value) {
    if ($value) {
        list($lngErrorNo, $errorReplace) = explode(":", $value);
        $strErrorMessage[] = fncOutputError($lngErrorNo, DEF_ERROR, $errorReplace, false, "", $objDB);
    }
}

if (!count($strErrorMessage)) {
    // 見積原価のデータ取得
    $selectQuery =
        "SELECT
			TO_CHAR(me.dtmInsertDate, 'YYYY/MM/DD HH24:MI:SS') AS dtminsertdate,
			mp.strproductcode || '_' || mp.strrevisecode as strproductcode,
			mp.strproductname,
			mp.strproductenglishname,
			'[' || mg.strgroupdisplaycode || ']' || mg.strgroupdisplayname AS lnginchargegroupcode,
			'[' || mu1.struserdisplaycode || ']' || mu1.struserdisplayname AS lnginchargeusercode,
			'[' || mu2.struserdisplaycode || ']' || mu2.struserdisplayname AS lngdevelopusercode,
			mp.curretailprice,
			'[' || mu3.struserdisplaycode || ']' || mu3.struserdisplayname AS lnginputusercode,
			mp.lngcartonquantity,
			mp.lngproductionquantity,
			me.cursalesamount,
			me.cursalesamount - me.curmanufacturingcost AS cursalesprofit,
			CASE WHEN me.cursalesamount = 0 THEN 0 ELSE (me.cursalesamount - me.curmanufacturingcost) / me.cursalesamount * 100 END AS cursalesprofitrate,
			tsum.curfixedcostsales,
			me.curtotalprice - me.cursalesamount + me.curmanufacturingcost AS curfixedcostsalesprofit,
			CASE WHEN tsum.curfixedcostsales = 0 THEN 0 ELSE (me.curtotalprice - me.cursalesamount + me.curmanufacturingcost) / tsum.curfixedcostsales * 100 END AS curfixedcostsalesprofitrate,
			me.cursalesamount + tsum.curfixedcostsales AS curtotalsales,
			me.curtotalprice,
			CASE WHEN me.cursalesamount + tsum.curfixedcostsales = 0 THEN 0 ELSE me.curtotalprice / (me.cursalesamount + tsum.curfixedcostsales) * 100 END AS curtotalpricerate,
			me.curtotalprice - me.curprofit AS curindirectmanufacturingcost,
			6.08 AS curstandardrate,
			me.curprofit,
			CASE WHEN me.cursalesamount + tsum.curfixedcostsales = 0 THEN 0 ELSE me.curprofit / (me.cursalesamount + tsum.curfixedcostsales) * 100 END AS curprofitrate,
			me.curmembercost,
			CASE WHEN mp.lngproductionquantity = 0 THEN 0 ELSE me.curmembercost / mp.lngproductionquantity END AS curmembercostpieces,
			me.curfixedcost,
			CASE WHEN mp.lngproductionquantity = 0 THEN 0 ELSE me.curfixedcost / mp.lngproductionquantity END AS curfixedcostpieces,
			me.curmanufacturingcost AS curmanufacturingcost,
			CASE WHEN mp.lngproductionquantity = 0 THEN 0 ELSE me.curmanufacturingcost / mp.lngproductionquantity END AS curmanufacturingcostpieces,
			CASE WHEN tsum.countofreceiveandorderdetail = tsum.countofaplicatedetail AND min_rev.lngrevisionno >= 0 THEN TRUE ELSE FALSE END AS deleteflag,
			me.lngestimateno,
			mp.strrevisecode,
			me.lngrevisionno,
			me.lngrevisionno AS lngmaxrevisionno

		FROM m_estimate me

		INNER JOIN m_product mp";

        // 管理者モードでない場合
//        if (!array_key_exists("admin", $optionColumns)) {
            $selectQuery = $selectQuery . " ON mp.strproductcode = me.strproductcode";
//        }
//        else{
//            $selectQuery = $selectQuery . " ON mp.strproductcode = substr(me.strproductcode, 1, 5)";
//        }
		$selectQuery = $selectQuery . " AND mp.strrevisecode = me.strrevisecode
			AND mp.lngrevisionno = me.lngproductrevisionno

		INNER JOIN m_group mg
			ON mg.lnggroupcode = mp.lnginchargegroupcode

		INNER JOIN m_user mu1
			ON mu1.lngusercode = mp.lnginchargeusercode

		LEFT OUTER JOIN m_user mu2
			ON mu2.lngusercode = mp.lngdevelopusercode

		INNER JOIN m_user mu3
			ON mu3.lngusercode = mp.lnginputusercode

		LEFT OUTER JOIN
		(
			SELECT
				me.lngestimateno,
				me.lngrevisionno,
				SUM(CASE WHEN mscdl.lngestimateareaclassno = 2 THEN ted.cursubtotalprice * ted.curconversionrate ELSE 0 END) AS curfixedcostsales,
				count(mscdl.lngestimateareaclassno <> 0 OR (msi.lngestimateareaclassno = 3 OR msi.lngestimateareaclassno = 4 OR (msi.lngstocksubjectcode = 401 and msi.lngstockitemcode = 1)) OR NULL) AS countofreceiveandorderdetail,
				count(mr.lngreceivestatuscode = 1 OR mo.lngorderstatuscode = 1 OR NULL) AS countofaplicatedetail
			FROM m_estimate me
			INNER JOIN m_estimatehistory meh on meh.lngestimateno = me.lngestimateno and meh.lngrevisionno = me.lngrevisionno
			INNER JOIN t_estimatedetail ted
				ON ted.lngestimateno = meh.lngestimateno
				and ted.lngestimatedetailno = meh.lngestimatedetailno
				AND ted.lngrevisionno = meh.lngestimatedetailrevisionno
			LEFT OUTER JOIN  m_salesclassdivisonlink mscdl
				ON mscdl.lngsalesclasscode = ted.lngsalesclasscode
				AND mscdl.lngsalesdivisioncode = ted.lngsalesdivisioncode
			LEFT OUTER JOIN m_stockitem msi
				ON msi.lngstocksubjectcode = ted.lngstocksubjectcode
				AND msi.lngstockitemcode = ted.lngstockitemcode
			LEFT OUTER JOIN t_receivedetail trd
				ON trd.lngestimateno = ted.lngestimateno
				AND trd.lngestimatedetailno = ted.lngestimatedetailno
				AND trd.lngestimaterevisionno = ted.lngrevisionno
			LEFT OUTER JOIN m_receive mr
				ON mr.lngreceiveno = trd.lngreceiveno
				AND mr.lngrevisionno = trd.lngrevisionno
			LEFT OUTER JOIN t_orderdetail tod
				ON tod.lngestimateno = ted.lngestimateno
				AND tod.lngestimatedetailno = ted.lngestimatedetailno
				AND tod.lngestimaterevisionno = ted.lngrevisionno
			LEFT OUTER JOIN m_order mo
				ON mo.lngorderno = tod.lngorderno
				AND mo.lngrevisionno = tod.lngrevisionno
			GROUP BY me.lngestimateno, me.lngrevisionno
		) tsum

			ON tsum.lngestimateno = me.lngestimateno
			AND tsum.lngrevisionno = me.lngrevisionno

		INNER JOIN
		(
			SELECT
				lngestimateno,
				MAX(lngrevisionno) AS lngrevisionno
			FROM m_estimate
			GROUP BY lngestimateno";
    // 管理者モードでない場合
    if (!array_key_exists("admin", $optionColumns)) {
        $selectQuery .=
            " HAVING MIN(lngrevisionno) >= 0";

    }

    $selectQuery .=

        ") maxrev

			ON maxrev.lngestimateno = me.lngestimateno
			AND maxrev.lngrevisionno = me.lngrevisionno";
    $selectQuery .= " INNER JOIN
		(
			SELECT
				lngestimateno,
				MIN(lngrevisionno) AS lngrevisionno
			FROM m_estimate
			GROUP BY lngestimateno
        ) min_rev
        	ON min_rev.lngestimateno = me.lngestimateno
    ";
    // WHERE句生成
    $where = '';

    foreach ($searchData as $key => $condition) {
        $fromCondition = '';
        $toCondition = '';
        $search = '';
        $searchs = array();
        $searchNumber = array();

        // 検索条件を振り分ける
        switch ($key) {
            // 入力日
            case 'dtmInsertDate':
                if ($condition['from']) {
                    $fromCondition = "me.dtminsertdate >= TO_TIMESTAMP('" . $condition['from'] . " 00:00:00', 'YYYY/MM/DD HH24:MI:SS')";
                }
                if ($condition['to']) {
                    $toCondition = "me.dtminsertdate <= TO_TIMESTAMP('" . $condition['to'] . " 23:59:59', 'YYYY/MM/DD HH24:MI:SS')";
                }
                break;

            // 製品コード
            case 'strProductCode':
                $strProductCodeArray = explode(",", $condition);
                $search = " (";
                $count = 0;
                foreach ($strProductCodeArray as $strProductCode) {
                    $count += 1;
                    if ($count != 1) {
                        $search .= " OR ";
                    }
                    if (strpos($strProductCode, '-') !== false) {
                        if (!array_key_exists("admin", $optionColumns)) {
                            $search .= "(mp.strProductCode";
                        }
                        else{
                            $search .= "(substr(me.strproductcode, 1, 5)";
                        }
                        $search .=" between '" . explode("-", $strProductCode)[0] . "'";
                        $search .=" AND " . "'" . explode("-", $strProductCode)[1] . "')";
                    } else {
                        if (strpos($strProductCode, '_') !== false) {
                            if (!array_key_exists("admin", $optionColumns)) {
                                $search .= "mp.strProductCode = '" . explode("_", $strProductCode)[0] . "'";
                            }
                            else{
                                $search .= "substr(me.strproductcode, 1, 5) = '" . explode("_", $strProductCode)[0] . "'";
                            }
                            $search .= " AND mp.strrevisecode = '" . explode("_", $strProductCode)[1] . "'";
                        } else {
                            if (!array_key_exists("admin", $optionColumns)) {
                                $search .= "mp.strProductCode = '" . $strProductCode . "'";
                            }
                            else{
                                $search .= "substr(me.strproductcode, 1, 5) = '" . $strProductCode . "'";
                            }
                        }
                    }
                }
                $search .= ")";
                break;

            // 製品名称
            case 'strProductName';
                if (strlen($condition)) {
                    // $search = "mp.strProductName LIKE '%" . $condition . "%'";
                    $search = "sf_translate_case(mp.strproductname) like '%' || sf_translate_case('".pg_escape_string($condition)."') || '%'";
                }
                break;

            // 製品名称(英語)
            case 'strProductEnglishName';
                if (strlen($condition)) {
                    // $search = "mp.strproductenglishname LIKE '%" . $condition . "%'";
                    $search = "sf_translate_case(mp.strproductenglishname) like '%' || sf_translate_case('".pg_escape_string($condition)."') || '%'";
                }
                break;

            // 営業部署
            case 'lngInChargeGroupCode':
                if (strlen($condition)) {
                    $search = "mg.strgroupdisplaycode = '" . $condition . "'";
                }
                break;

            // 担当
            case 'lngInChargeUserCode':
                if (strlen($condition)) {
                    $search = "mu1.struserdisplaycode = '" . $condition . "'";
                }
                break;

            // 開発担当者
            case 'lngDevelopUserCode':
                if (strlen($condition)) {
                    $search = "mu2.struserdisplaycode = '" . $condition . "'";
                }
                break;

            // 入力者
            case 'lngInputUserCode':
                if (strlen($condition)) {
                    $search = "mu3.struserdisplaycode = '" . $condition . "'";
                }
                break;

            default:
                break;
        }

        if ($fromCondition && $toCondition) {
            $search = $fromCondition . " AND " . $toCondition;
        } else if ($fromCondition) {
            $search = $fromCondition;
        } else if ($toCondition) {
            $search = $toCondition;
        }

        if ($search) {
            if ($where) {
                $where .= " AND " . $search;
            } else {
                $where = " WHERE " . $search;
            }
        }
    }

    // ソート設定
    list($column, $sortNum, $DESC) = explode("_", $aryData["strSort"]);

    if ($column) {
        $orderBy = " ORDER BY " . $sortNum . " " . $DESC . ", me.dtmInsertDate DESC\n";
    } else {
        $orderBy = " ORDER BY me.dtmInsertDate DESC\n";
    }

    $strQuery = $selectQuery . $where . $orderBy;

    list($resultID, $resultNum) = fncQuery($strQuery, $objDB);
    if ($resultNum > 1000) {
        $strErrorMessage = fncOutputError(9057, DEF_WARNING, "1000", false, "/estimate/search/index.php?strSessionID=" . $aryData["strSessionID"], $objDB);
    } else if ($resultNum < 1) {
        $strErrorMessage = fncOutputError(1507, DEF_WARNING, "", false, "/estimate/search/index.php?strSessionID=" . $aryData["strSessionID"], $objDB);
    }
}

// 検索でエラーが発生したらエラーメッセージを画面に出力する
if ($strErrorMessage) {
    makeHTML::outputErrorWindow($strErrorMessage);
}

//////////////////////////////////////////////////////////////////////////
// 結果取得、出力処理
//////////////////////////////////////////////////////////////////////////

$header = '';
$columns = 0;
$sort = 0;

// ヘッダ部の作成
foreach ($displayColumns as $column) {

    $title = htmlspecialchars($aryColumnLang[$column], ENT_QUOTES);

    if ($column === 'btnDetail'
        || $column === 'btnHistory'
        || $column === 'btnDelete') {
        $header .= "<th nowrap>" . $title . "</th>";
    } else {
        ++$sort;
        $header .= "<th class=\"sortColumns\" nowrap onmouseover=\"SortOn( this );\" onmouseout=\"SortOff( this );\" data-value=\"column_" . $sort . "_ASC\"><a href=\"#\">" . $title . "</a></th>";
    }
    ++$columns;
}

// 取得した検索結果を変換するための配列
// カンマ区切りにする項目
$commaSeparateList = array(
    "curRetailPrice" => true,
    "lngCartonQuantity" => true,
    "lngProductionQuantity" => true,
    "curSalesAmount" => true,
    "curSalesProfit" => true,
    "curFixedCostSales" => true,
    "curFixedCostSalesProfit" => true,
    "curTotalSales" => true,
    "curTotalPrice" => true,
    "curIndirectManufacturingCost" => true,
    "curProfit" => true,
    "curMemberCost" => true,
    "curFixedCost" => true,
    "curManufacturingCost" => true,
);

$hasDecimalPointList = array(
    "curMemberCostPieces" => true,
    "curFixedCostPieces" => true,
    "curManufacturingCostPieces" => true
);

// 円マークをつける項目
$yenAddList = array(
    "curRetailPrice" => true,
    "curSalesAmount" => true,
    "curSalesProfit" => true,
    "curFixedCostSales" => true,
    "curFixedCostSalesProfit" => true,
    "curTotalSales" => true,
    "curTotalPrice" => true,
    "curIndirectManufacturingCost" => true,
    "curProfit" => true,
    "curMemberCostPieces" => true,
    "curMemberCost" => true,
    "curFixedCostPieces" => true,
    "curFixedCost" => true,
    "curManufacturingCostPieces" => true,
    "curManufacturingCost" => true,
);

// パーセント表記にする項目
$percentList = array(
    "curSalesProfitRate" => true,
    "curFixedCostSalesProfitRate" => true,
    "curTotalPriceRate" => true,
    "curProfitRate" => true,
    "curStandardRate" => true,
);

// 検索結果の表を作成
$body = '';

for ($i = 0; $i < $resultNum; ++$i) {

    $result = pg_fetch_array($resultID, $i, PGSQL_ASSOC);
    $bgcolor = fncSetBgColor('estimate', $result["lngestimateno"], true, $objDB);
    $beforeClickBgcolor = $bgcolor;

    $bgcolor = "background-color: " .$bgcolor . ";";

    $estimateNo = htmlspecialchars($result['lngestimateno'], ENT_QUOTES);

    $body .= "<tr id=\"" . $estimateNo . "\" before-click-bgcolor=\"". $beforeClickBgcolor . "\"  class=\"estimate_search_result\" style=\"" . $bgcolor . "\" onclick=\"fncSelectTrColor( this );\">";

    $number = $i + 1;

    $body .= "<td nowrap>" . $number . "</td>";

    foreach ($displayColumns as $column) {
        if ($column === 'btnDetail') { // 詳細
            $body .= "<td align=\"center\" onmouseout=\"trClickFlg='on';\" onclick=\"trClickFlg='off';fncNoSelectSomeTrColor( this, 'TD" . $resultNum . "_',1 );\">";
            // $body .= "<button type=\"button\" class=\"cells btnDetail\" action=\"/estimate/preview/index.php?strSessionID=" . $strSessionID . "&estimateNo=" . $estimateNo . "\" value=\"" . $result['lngrevisionno'] . "\">";
            $body .= "<img src=\"/img/type01/pc/detail_off_bt.gif\" class=\"detail button\" width=\"15\" height=\"15\" border=\"0\" alt=\"DETAIL\" action=\"/estimate/preview/index.php?strSessionID=" . $strSessionID . "&estimateNo=" . $estimateNo . "\" value=\"" . $result['lngrevisionno'] . "\">";
            $body .= "</button></td>";

        } else if ($column === 'btnHistory') { // 履歴
            if ($result["lngrevisionno"] <> 0) {
                $body .= "<td align=\"center\" onmouseout=\"trClickFlg='on';\" onclick=\"trClickFlg='off';fncNoSelectSomeTrColor( this, 'TD" . $resultNum . "_',1 );\">";
                $body .= "<img src=\"/img/type01/cmn/seg/history_open_off.gif\" class=\"history button\"  width=\"15\" height=\"15\" border=\"0\" alt=\"HISTORY\"  rownum=\"" . $number . "\" estimateNo=\"" . $estimateNo . "\" revisionNo=\"" . $result['lngrevisionno'] . "\">";
                $body .= "</button></td>";
            } else {
                $body .= "<td nowrap align=\"left\"></td>";
            }

		} else if ($column === 'btnDelete') { // 削除
            if (array_key_exists("admin", $optionColumns) and $result['deleteflag'] === 't') {
                $body .= "<td align=\"center\" onmouseout=\"trClickFlg='on';\" onclick=\"trClickFlg='off';fncNoSelectSomeTrColor( this, 'TD" . $resultNum . "_',1 );\">";
                $body .= "<img src=\"/img/type01/pc/delete_off_bt.gif\" width=\"15\" height=\"15\" border=\"0\" alt=\"DELETE\" class=\"delete button\" action=\"/estimate/delete/index.php\" value=\"" . $result['lngrevisionno'] . "\">";

                $body .= "</button></td>";
            } else {
                $body .= "<td nowrap align=\"left\"></td>";
            }

        } else {

            $key = strtolower($column);

            $param = $result[$key];

            if (strlen($param)) {
                if (!is_numeric($param)) {
                    $param = htmlspecialchars($param, ENT_QUOTES);
                }

                if ($commaSeparateList[$column]) {
                    $param = number_format($param, 0);
                }

                if ($hasDecimalPointList[$column]) {
                    $param = number_format($param, 2);
                }
                
                if ($yenAddList[$column]) {
                    $param = '&yen ' . $param;
                }

                if ($percentList[$column]) {
                    $param = number_format($param, 2) . '%';
                }
            }

            $body .= "<td nowrap align=\"left\">" . $param . "</td>";
        }
    }
    $body .= "</tr>";
}

// 同じ項目のソートは逆順にする処理
list($column, $sortNum, $DESC) = explode("_", $aryData["strSort"]);

if ($DESC == 'ASC') {
    $pattern = $aryData["strSort"];
    $replace = str_replace('ASC', 'DESC', $pattern);

    $header = str_replace($pattern, $replace, $header);
}

// 検索画面の情報をhiddenで渡す
unset($aryData["strSort"]);

$form = makeHTML::getHiddenData($aryData);

// ベーステンプレート読み込み
$objTemplate = new clsTemplate();
$objTemplate->getTemplate("estimate/result/base.tmpl");

$baseData['strSessionID'] = $strSessionID;
$baseData['FORM'] = $form;
$baseData['HEADER'] = $header;
$baseData['tabledata'] = $body;
$baseData["displayColumns"] = implode(",", $displayColumns);
// ベーステンプレート生成
$objTemplate->replace($baseData);
$objTemplate->complete();

// HTML出力
echo $objTemplate->strTemplate;

$objDB->close();

return true;
