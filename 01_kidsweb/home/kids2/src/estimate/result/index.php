<?
/** 
*	見積原価管理 検索結果表示画面
*
*	@package   kuwagata
*	@license   http://www.wiseknot.co.jp/ 
*	@copyright Copyright &copy; 2004, AntsBizShare 
*	@author    Kenji Chiba
*	@access    public
*	@version   1.00
*
*/
// -------------------------------------------------------------------------


// 設定読み込み
include_once ('conf.inc');
require_once ( LIB_DEBUGFILE );
require_once (SRC_ROOT.'/mold/lib/UtilSearchForm.class.php');

// ライブラリ読み込み
require_once (LIB_FILE);
require_once (SRC_ROOT. "estimate/cmn/makeHTML.php");

// DB接続
$objDB   = new clsDB();
$objAuth = new clsAuth();
$objDB->open( "", "", "", "" );

$aryData = $_REQUEST;

$strSessionID = $aryData["strSessionID"];

// フォームデータから各カテゴリの振り分けを行う
	$options = UtilSearchForm::extractArrayByOption($aryData);
	$isDisplay = UtilSearchForm::extractArrayByIsDisplay($aryData);
	$isSearch = UtilSearchForm::extractArrayByIsSearch($aryData);
	$from = UtilSearchForm::extractArrayByFrom($aryData);
	$to = UtilSearchForm::extractArrayByTo($aryData);
	
	$displayColumns = array_keys($isDisplay);
	$searchColumns = array_keys($isSearch);

	//////////////////////////////////////////////////////////////////////////
	// POST(一部GET)データ取得
	//////////////////////////////////////////////////////////////////////////

	$searchData = array();
	
	// 検索条件の配列を生成する
	foreach ($searchColumns as $column) {
		// 	範囲指定の場合
		if (isset($from[$column]) || isset($to[$column])) {
			$searchData[$column] = array(
				'from' => $from[$column],
				'to' => $to[$column]
			);
		} else {
			$searchData[$column] = $data[$column];
		}
	}

//////////////////////////////////////////////////////////////////////////
// セッション、権限確認
//////////////////////////////////////////////////////////////////////////
// セッション確認
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

// 権限確認
if ( !fncCheckAuthority( DEF_FUNCTION_E2, $objAuth ) )
{
	fncOutputError ( 9052, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
}


// 検索結果のカラム表記の言語設定
$aryColumnLang = Array (
	"btnDetail"						=> "詳細",
	"btnHistory"					=> "履歴",
	"dtmInsertDate"		    		=> "作成日",
	"strProductCode"				=> "製品コード",
	"strProductName"				=> "製品名称(日本語)",
	"strProductEnglishName"			=> "製品名称(英語)",
	"lngInChargeGroupCode"      	=> "営業部署",
	"lngInChargeUserCode"	        => "担当",
	"lngDevelopUserCode"		    => "開発担当者",
	"dtmDeliveryLimitDate"			=> "客先納品日",
	"curRetailPrice"				=> "上代",
	"lngCartonQuantity"				=> "カートン入数",
	"lngProductionQuantity"			=> "償却数(pcs)",
	"curSalesAmount"			    => "製品売上高",
	"curSalesProfit"                => "製品利益",
	"curSalesProfitRate"            => "製品利益率",
	"curFixedCostSales"             => "固定費売上高",
	"curFixedCostSalesProfit"       => "固定費利益",
	"curFixedCostSalesProfitRate"   => "固定費利益率",
	"curTotalSales"                 => "総売上高",
	"curTotalPrice"                 => "売上総利益",
	"curTotalPriceRate"             => "売上総利益率",
	"curIndirectManufacturingCost"  => "間接製造経費",
	"curStandardRate"               => "間接製造経費率",
	"curProfit"                     => "営業利益",
	"curProfitRate"                 => "営業利益率",
	"curMemberCostPieces"           => "pcs部材費用",
	"curMemberCost"                 => "部材費合計",
	"curFixedCostPieces"            => "pcs償却費用",
	"curFixedCost"                  => "償却費合計",
	"curManufacturingCostPieces"    => "pcsコスト",
	"curManufacturingCost"          => "製造費用合計",
	"btnDelete"                     => "削除"
);

//////////////////////////////////////////////////////////////////////////
// 文字列チェック
//////////////////////////////////////////////////////////////////////////

$aryCheck["strSessionID"]                = "null:numenglish(32,32)";
$aryCheck["strProductCodeFrom"]          = "number(0,2147483647)";
$aryCheck["strProductCodeTo"]            = "number(0,2147483647)";
$aryCheck["strProductName"]              = "length(0,80)";
$aryCheck["lngInChargeGroupCode"]        = "numenglish(0,32767)";
$aryCheck["lngInChargeUserCode"]         = "numenglish(0,32767)";
$aryCheck["lngDevelopUserCode"]          = "numenglish(0,32767)";
$aryCheck["dtmCreationDateFrom"]         = "date(/)";
$aryCheck["dtmCreationDateTo"]           = "date(/)";
$aryCheck["dtmDeliveryLimitDateFrom"]    = "date(/)";
$aryCheck["dtmDeliveryLimitDateTo"]      = "date(/)";


// 文字列チェック
$aryCheckResult = fncAllCheck( $aryData, $aryCheck );
fncPutStringCheckError( $aryCheckResult, $objDB );

// 見積原価のデータ取得
$selectQuery = 
	"SELECT
		TO_CHAR(mp.dtminsertdate, 'YYYY/MM/DD') AS dtminsertdate,
		mp.strproductcode,
		mp.strproductname,
		mp.strproductenglishname,
		'[' || mg.strgroupdisplaycode || ']' || mg.strgroupdisplayname AS lnginchargegroupcode,
		'[' || mu1.struserdisplaycode || ']' || mu1.struserdisplayname AS lnginchargeusercode,
		'[' || mu2.struserdisplaycode || ']' || mu2.struserdisplayname AS lngdevelopusercode,
		-- TO_CHAR(mp.dtmdeliverylimitdate, 'YYYY/MM/DD') AS dtmdeliverylimitdate,
		mp.curretailprice,
		mp.lngcartonquantity,
		mp.lngproductionquantity,
		me.cursalesamount,
		me.cursalesamount - me.curmanufacturingcost AS cursalesprofit,
		CASE WHEN me.cursalesamount = 0 THEN 0 ELSE (me.cursalesamount - me.curmanufacturingcost) / me.cursalesamount * 100 END AS cursalesprofitrate,
		tsum.curfixedcostsales,
		tsum.curfixedcostsales - tsum.curnotdepreciationcost AS curfixedcostsalesprofit,
		CASE WHEN tsum.curfixedcostsales = 0 THEN 0 ELSE (tsum.curfixedcostsales - tsum.curnotdepreciationcost) / tsum.curfixedcostsales * 100 END AS curfixedcostsalesprofitrate,
		me.cursalesamount + tsum.curfixedcostsales AS curtotalsales,
		me.curtotalprice,
		CASE WHEN me.cursalesamount + tsum.curfixedcostsales = 0 THEN 0 ELSE me.curtotalprice / (me.cursalesamount + tsum.curfixedcostsales) * 100 END AS curtotalpricerate,
		me.curtotalprice - me.curprofit AS curindirectmanufacturingcost,
		CASE WHEN me.cursalesamount + tsum.curfixedcostsales = 0 THEN 0 ELSE (me.curtotalprice - me.curprofit) / (me.cursalesamount + tsum.curfixedcostsales) * 100 END AS curstandardrate,
		me.curprofit,
		CASE WHEN me.cursalesamount + tsum.curfixedcostsales = 0 THEN 0 ELSE me.curprofit / (me.cursalesamount + tsum.curfixedcostsales) * 100 END AS curprofitrate,
		me.curmembercost,
		CASE WHEN mp.lngproductionquantity = 0 THEN 0 ELSE me.curmembercost / mp.lngproductionquantity END AS curmembercostpieces,
		me.curfixedcost,
		CASE WHEN mp.lngproductionquantity = 0 THEN 0 ELSE me.curfixedcost / mp.lngproductionquantity END AS curfixedcostpieces,
		me.curmanufacturingcost AS curmanufacturingcost,
		CASE WHEN mp.lngproductionquantity = 0 THEN 0 ELSE me.curmanufacturingcost / mp.lngproductionquantity END AS curmanufacturingcostpieces,
		CASE WHEN tsum.countofreceiveandorderdetail = tsum.countofaplicatedetail THEN TRUE ELSE FALSE END AS deleteflag,
		me.lngestimateno,
		mp.strrevisecode,
		me.lngrevisionno,
		me.lngrevisionno AS lngmaxrevisionno
		
	FROM m_estimate me
	
	INNER JOIN m_product mp
		ON mp.strproductcode = me.strproductcode
		AND mp.strrevisecode = me.strrevisecode
		AND mp.lngrevisionno = me.lngproductrevisionno
	
	INNER JOIN m_group mg
		ON mg.lnggroupcode = mp.lnginchargegroupcode
	
	INNER JOIN m_user mu1
		ON mu1.lngusercode = mp.lnginchargeusercode
	
	LEFT OUTER JOIN m_user mu2
		ON mu2.lngusercode = mp.lngdevelopusercode
	
	LEFT OUTER JOIN
	(
		SELECT 
			me.lngestimateno,
			me.lngrevisionno,
			SUM(CASE WHEN mscdl.lngestimateareaclassno = 2 THEN ted.curconversionrate * ted.cursubtotalprice ELSE 0 END) AS curfixedcostsales,
			SUM(CASE WHEN msi.lngestimateareaclassno = 3 AND ted.bytpayofftargetflag = FALSE THEN ted.curconversionrate * ted.cursubtotalprice ELSE 0 END) AS curnotdepreciationcost,
			count(mscdl.lngestimateareaclassno <> 0 OR msi.lngestimateareaclassno <> 5 OR NULL) AS countofreceiveandorderdetail,
			count(mr.lngreceivestatuscode = 1 OR mo.lngorderstatuscode = 1 OR NULL) AS countofaplicatedetail
		FROM t_estimatedetail ted
		INNER JOIN m_estimate me
			ON me.lngestimateno = ted.lngestimateno
			AND me.lngrevisionno = ted.lngrevisionno
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

// // 管理者モードでない場合
// if () {
	$selectQuery .=
	    " HAVING MIN(lngrevisionno) >= 0";

// }
		
$selectQuery .=

	") maxrev

		ON maxrev.lngestimateno = me.lngestimateno
		AND maxrev.lngrevisionno = me.lngrevisionno";
	
	// WHERE句生成
	$where = '';

	foreach ($searchData as $key => $condition) {
		$fromCondition = '';
		$toCondition = '';
		$search = '';

		// 検索条件を振り分ける
		switch ($key) {
			// 入力日
			case 'dtmInsertDate':
				if ($condition['from']) {
					$fromCondition = "me.dtminsertdate >= TO_TIMESTAMP('".$condition['from']." 00:00:00', 'YYYY/MM/DD HH24:MI:SS')";                                 
				}
				if ($condition['to']) {
					$toCondition = "me.dtminsertdate <= TO_TIMESTAMP('".$condition['to']." 23:59:59', 'YYYY/MM/DD HH24:MI:SS')";
				}
				break;

			// 製品コードs
			case 'strProductCode':
				if ($condition['from']) {
					$fromCondition = "mp.strproductcode >= '". $condition['from']. "'";                                 
				}
				if ($condition['to']) {
					$toCondition = "mp.strproductcode <= '". $condition['to']. "'";
				}
				break;

			// 製品名
			case 'strProductName';
				$search = "mp.strProductName = ". $condition;
				break;

			// 営業部署
			case 'lngInChargeGroupCode':
				$search = "mp.strInchargegroupdisplaycode = ". $condition;
				break;

			// 担当
			case 'lngInChargeUserCode':
				$search = "mp.strInchargeuserdisplaycode = ". $condition;
				break;

			// 開発担当者
			case 'lngDevelopUserCode':
				$search = "mp.strdevelopuserdisplaycode = ". $condition;
				break;
			
			// // 納期
			// case 'dtmDeliveryLimitDate';
			// 	if ($condition['from']) {
			// 		$fromCondition = "mp.dtmdeliverylimitdate >= TO_DATE('".$condition['from']."', 'YYYY/MM/DD')";                                 
			// 	}
			// 	if ($condition['to']) {
			// 		$toCondition = "mp.dtmdeliverylimitdate <= TO_DATE('".$condition['to']."', 'YYYY/MM/DD')";
			// 	}
			// 	break;
			
			default:
				break;
		}

		if ($fromCondition && $toCondition) {
			$search = $fromCondition. " AND ". $toCondition; 
		} else if ($fromCondition) {
			$search = $fromCondition;
		} else if ($toCondition) {
			$search = $toCondition;
		}

		if ($search) {
			if ($where) {
				$where .= " AND ". $search;
			} else {
				$where = " WHERE ". $search;
			}
		}
	}

	// ソート設定
	list ($column, $sortNum, $DESC ) = explode ( "_", $aryData["strSort"] );

	if ($column) {
		$orderBy = " ORDER BY ". $sortNum. " ". $DESC. ", me.dtmInsertDate DESC\n";
	}
	else {
		$orderBy = " ORDER BY me.dtmInsertDate DESC\n";
	}

	$strQuery = $selectQuery.$where.$orderBy;

	list($resultID, $resultNum) = fncQuery($strQuery, $objDB);

	if ($resultNum > 1000) {
		$strErrorMessage = fncOutputError( 9057, DEF_WARNING, "1000", FALSE, "/estimate/search/index.php?strSessionID=" . $aryData["strSessionID"], $objDB );
	} else if ($resultNum < 1) {
		$strErrorMessage = fncOutputError( 1507, DEF_WARNING, "", FALSE, "/estimate/search/index.php?strSessionID=" . $aryData["strSessionID"], $objDB );
	}

	// バリデーションでエラーが発生した場合はエラーメッセージを表示する
	if ( $strErrorMessage ) {

		$strMessage = "<div>". $strErrorMessage. "</div>";

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
		$header .= "<th class=\"sortColumns\" nowrap onmouseover=\"SortOn( this );\" onmouseout=\"SortOff( this );\" data-value=\"column_". $sort. "_ASC\"><a href=\"#\">" . $title . "</a></th>";
	}
	++$columns;
}


// 取得した検索結果を変換するための配列
// カンマ区切りにする項目
$commaSeparateList = array(
	"curRetailPrice"				=> true,
	"lngCartonQuantity"				=> true,
	"lngProductionQuantity"			=> true,
	"curSalesAmount"			    => true,
	"curSalesProfit"                => true,
	"curSalesProfitRate"            => true,
	"curFixedCostSales"             => true,
	"curFixedCostSalesProfit"       => true,
	"curFixedCostSalesProfitRate"   => true,
	"curTotalSales"                 => true,
	"curTotalPrice"                 => true,
	"curTotalPriceRate"             => true,
	"curIndirectManufacturingCost"  => true,
	"curStandardRate"               => true,
	"curProfit"                     => true,
	"curProfitRate"                 => true,
	"curMemberCostPieces"           => true,
	"curMemberCost"                 => true,
	"curFixedCostPieces"            => true,
	"curFixedCost"                  => true,
	"curManufacturingCostPieces"    => true,
	"curManufacturingCost"          => true
);

// 円マークをつける項目
$yenAddList = array(
	"curRetailPrice"				=> true,
	"curSalesAmount"			    => true,
	"curSalesProfit"                => true,
	"curFixedCostSales"             => true,
	"curFixedCostSalesProfit"       => true,
	"curTotalSales"                 => true,
	"curTotalPrice"                 => true,
	"curIndirectManufacturingCost"  => true,
	"curProfit"                     => true,
	"curMemberCostPieces"           => true,
	"curMemberCost"                 => true,
	"curFixedCostPieces"            => true,
	"curFixedCost"                  => true,
	"curManufacturingCostPieces"    => true,
	"curManufacturingCost"          => true
);

// パーセント表記にする項目
$percentList = array(
	"curSalesProfitRate"            => true,
	"curFixedCostSalesProfitRate"   => true,
	"curTotalPriceRate"             => true,
	"curProfitRate"                 => true,
	"curStandardRate"               => true,
);


// 検索結果の表を作成
$body = '';

for ($i = 0; $i < $resultNum; ++$i) {

	$result = pg_fetch_array($resultID, $i, PGSQL_ASSOC);

	$estimateNo = htmlspecialchars($result['lngestimateno'], ENT_QUOTES);

	$body .= "<tr id=\"". $estimateNo."\" class=\"estimate_search_result\" style=\"background:#FFFFFF\" onclick=\"fncSelectTrColor( this );\">";

	$number = $i + 1;

	$body .= "<td nowrap>". $number. "</td>";	

	foreach ($displayColumns as $column) {
		if ($column === 'btnDetail') { // 詳細
				$body .= "<td bgcolor=\"#FFFFFF\" align=\"center\" onmouseout=\"trClickFlg='on';\" onclick=\"trClickFlg='off';fncNoSelectSomeTrColor( this, 'TD" . $resultNum . "_',1 );\">";
				$body .= "<button type=\"button\" class=\"cells btnDetail\" action=\"/estimate/preview/index.php?strSessionID=". $strSessionID. "&estimateNo=". $estimateNo."\" value=\"". $result['lngrevisionno']. "\">";
				$body .= "<img onmouseover=\"DetailOn(this);\" onmouseout=\"DetailOff(this);\" src=\"/img/". LAYOUT_CODE. "/wf/result/detail_off_bt.gif\" width=\"15\" height=\"15\" border=\"0\" alt=\"DETAIL\">";
				$body .= "</button></td>";

		} else if ($column === 'btnHistory') { // 履歴
			if ($result['lngmaxrevisionno'] > 1 && $result['lngrevisionno'] === $result['lngmaxrevisionno'] ) {
				$body .= "<td bgcolor=\"#FFFFFF\" align=\"center\" onmouseout=\"trClickFlg='on';\" onclick=\"trClickFlg='off';fncNoSelectSomeTrColor( this, 'TD" . $resultNum . "_',1 );\">";
				$body .= "<button type=\"button\" class=\"cells btnHistory\">";
				$body .= "<img onmouseover=\"RenewOn(this);\" onmouseout=\"RenewOff(this);\" src=\"/img/". LAYOUT_CODE. "/cmn/seg/renew_off_bt.gif\" width=\"15\" height=\"15\" border=\"0\" alt=\"HISTORY\">";
				$body .= "</button></td>";
			} else {
				$body .= "<td nowrap align=\"left\"></td>";
			}

		} else if ($column === 'btnDelete') { // 削除
			if ($result['lngrevisionno'] === $result['lngmaxrevisionno']
				&& $result['deleteflag'] === 't') {
				$body .= "<td bgcolor=\"#FFFFFF\" align=\"center\" onmouseout=\"trClickFlg='on';\" onclick=\"trClickFlg='off';fncNoSelectSomeTrColor( this, 'TD" . $resultNum . "_',1 );\">";
				$body .= "<button type=\"button\" class=\"cells btnDelete\" action=\"/estimate/delete/index.php\" value=\"". $result['lngrevisionno']. "\">";
				$body .= "<img onmouseover=\"RemoveOn(this);\" onmouseout=\"RemoveOff(this);\" src=\"/img/". LAYOUT_CODE. "/cmn/seg/remove_off_bt.gif\" width=\"15\" height=\"15\" border=\"0\" alt=\"DELETE\">";
				
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
					$param = number_format($param, 2);
				}
	
				if ($yenAddList[$column]) {
					$param = '&yen '. $param;
				}
	
				if ($percentList[$column]) {
					$param = $param. '%';
				}
			}

			$body .= "<td nowrap align=\"left\">". $param . "</td>";
		}
	}
	$body .= "</tr>";
}

// 同じ項目のソートは逆順にする処理
list ( $column, $sortNum, $DESC ) = explode ( "_", $aryData["strSort"] );

if ( $DESC == 'ASC' ) {
	$pattern = $aryData["strSort"];
	$replace = str_replace('ASC', 'DESC', $pattern);

	$header = str_replace ($pattern, $replace, $header);
}

// 検索画面の情報をhiddenで渡す
unset($aryData["strSort"]);

$form = makeHTML::getHiddenData($aryData);

// ベーステンプレート読み込み
$objTemplate = new clsTemplate();
$objTemplate->getTemplate( "estimate/result/base.tmpl" );

$baseData['strSessionID'] = $strSessionID;
$baseData['FORM'] = $form;
$baseData['HEADER'] = $header;
$baseData['tabledata'] = $body;

// ベーステンプレート生成
$objTemplate->replace( $baseData );
$objTemplate->complete();

// HTML出力
echo $objTemplate->strTemplate;

$objDB->close();


return TRUE;
?>
