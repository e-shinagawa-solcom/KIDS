<?
/** 
*	���Ѹ������� �������ɽ������
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


// �����ɤ߹���
include_once ('conf.inc');
require_once ( LIB_DEBUGFILE );
require_once (SRC_ROOT.'/mold/lib/UtilSearchForm.class.php');

// �饤�֥���ɤ߹���
require_once (LIB_FILE);
require_once (SRC_ROOT. "estimate/cmn/makeHTML.php");

// DB��³
$objDB   = new clsDB();
$objAuth = new clsAuth();
$objDB->open( "", "", "", "" );

$aryData = $_REQUEST;

$strSessionID = $aryData["strSessionID"];

// �ե�����ǡ�������ƥ��ƥ���ο���ʬ����Ԥ�
	$options = UtilSearchForm::extractArrayByOption($aryData);
	$isDisplay = UtilSearchForm::extractArrayByIsDisplay($aryData);
	$isSearch = UtilSearchForm::extractArrayByIsSearch($aryData);
	$from = UtilSearchForm::extractArrayByFrom($aryData);
	$to = UtilSearchForm::extractArrayByTo($aryData);
	
	$displayColumns = array_keys($isDisplay);
	$searchColumns = array_keys($isSearch);

	//////////////////////////////////////////////////////////////////////////
	// POST(����GET)�ǡ�������
	//////////////////////////////////////////////////////////////////////////

	$searchData = array();
	
	// ���������������������
	foreach ($searchColumns as $column) {
		// 	�ϰϻ���ξ��
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
// ���å���󡢸��³�ǧ
//////////////////////////////////////////////////////////////////////////
// ���å�����ǧ
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

// ���³�ǧ
if ( !fncCheckAuthority( DEF_FUNCTION_E2, $objAuth ) )
{
	fncOutputError ( 9052, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
}


// ������̤Υ����ɽ���θ�������
$aryColumnLang = Array (
	"btnDetail"						=> "�ܺ�",
	"btnHistory"					=> "����",
	"dtmInsertDate"		    		=> "������",
	"strProductCode"				=> "���ʥ�����",
	"strProductName"				=> "����̾��(���ܸ�)",
	"strProductEnglishName"			=> "����̾��(�Ѹ�)",
	"lngInChargeGroupCode"      	=> "�Ķ�����",
	"lngInChargeUserCode"	        => "ô��",
	"lngDevelopUserCode"		    => "��ȯô����",
	"dtmDeliveryLimitDate"			=> "����Ǽ����",
	"curRetailPrice"				=> "����",
	"lngCartonQuantity"				=> "�����ȥ�����",
	"lngProductionQuantity"			=> "���ѿ�(pcs)",
	"curSalesAmount"			    => "��������",
	"curSalesProfit"                => "��������",
	"curSalesProfitRate"            => "��������Ψ",
	"curFixedCostSales"             => "����������",
	"curFixedCostSalesProfit"       => "����������",
	"curFixedCostSalesProfitRate"   => "����������Ψ",
	"curTotalSales"                 => "������",
	"curTotalPrice"                 => "���������",
	"curTotalPriceRate"             => "���������Ψ",
	"curIndirectManufacturingCost"  => "������¤����",
	"curStandardRate"               => "������¤����Ψ",
	"curProfit"                     => "�Ķ�����",
	"curProfitRate"                 => "�Ķ�����Ψ",
	"curMemberCostPieces"           => "pcs��������",
	"curMemberCost"                 => "��������",
	"curFixedCostPieces"            => "pcs��������",
	"curFixedCost"                  => "��������",
	"curManufacturingCostPieces"    => "pcs������",
	"curManufacturingCost"          => "��¤���ѹ��",
	"btnDelete"                     => "���"
);

//////////////////////////////////////////////////////////////////////////
// ʸ��������å�
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


// ʸ��������å�
$aryCheckResult = fncAllCheck( $aryData, $aryCheck );
fncPutStringCheckError( $aryCheckResult, $objDB );

// ���Ѹ����Υǡ�������
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

// // �����ԥ⡼�ɤǤʤ����
// if () {
	$selectQuery .=
	    " HAVING MIN(lngrevisionno) >= 0";

// }
		
$selectQuery .=

	") maxrev

		ON maxrev.lngestimateno = me.lngestimateno
		AND maxrev.lngrevisionno = me.lngrevisionno";
	
	// WHERE������
	$where = '';

	foreach ($searchData as $key => $condition) {
		$fromCondition = '';
		$toCondition = '';
		$search = '';

		// �������򿶤�ʬ����
		switch ($key) {
			// ������
			case 'dtmInsertDate':
				if ($condition['from']) {
					$fromCondition = "me.dtminsertdate >= TO_TIMESTAMP('".$condition['from']." 00:00:00', 'YYYY/MM/DD HH24:MI:SS')";                                 
				}
				if ($condition['to']) {
					$toCondition = "me.dtminsertdate <= TO_TIMESTAMP('".$condition['to']." 23:59:59', 'YYYY/MM/DD HH24:MI:SS')";
				}
				break;

			// ���ʥ�����s
			case 'strProductCode':
				if ($condition['from']) {
					$fromCondition = "mp.strproductcode >= '". $condition['from']. "'";                                 
				}
				if ($condition['to']) {
					$toCondition = "mp.strproductcode <= '". $condition['to']. "'";
				}
				break;

			// ����̾
			case 'strProductName';
				$search = "mp.strProductName = ". $condition;
				break;

			// �Ķ�����
			case 'lngInChargeGroupCode':
				$search = "mp.strInchargegroupdisplaycode = ". $condition;
				break;

			// ô��
			case 'lngInChargeUserCode':
				$search = "mp.strInchargeuserdisplaycode = ". $condition;
				break;

			// ��ȯô����
			case 'lngDevelopUserCode':
				$search = "mp.strdevelopuserdisplaycode = ". $condition;
				break;
			
			// // Ǽ��
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

	// ����������
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

	// �Х�ǡ������ǥ��顼��ȯ���������ϥ��顼��å�������ɽ������
	if ( $strErrorMessage ) {

		$strMessage = "<div>". $strErrorMessage. "</div>";

		// [strErrorMessage]�񤭽Ф�
		$aryHtml["strErrorMessage"] = $strMessage;

		// �ƥ�ץ졼���ɤ߹���
		$objTemplate = new clsTemplate();
		$objTemplate->getTemplate( "/result/error/parts.tmpl" );
		
		// �ƥ�ץ졼������
		$objTemplate->replace( $aryHtml );
		$objTemplate->complete();

		// HTML����
		echo $objTemplate->strTemplate;

		exit;
	}

//////////////////////////////////////////////////////////////////////////
// ��̼��������Ͻ���
//////////////////////////////////////////////////////////////////////////

$header = '';
$columns = 0;
$sort = 0;

// �إå����κ���
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


// ��������������̤��Ѵ����뤿�������
// ����޶��ڤ�ˤ������
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

// �ߥޡ�����Ĥ������
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

// �ѡ������ɽ���ˤ������
$percentList = array(
	"curSalesProfitRate"            => true,
	"curFixedCostSalesProfitRate"   => true,
	"curTotalPriceRate"             => true,
	"curProfitRate"                 => true,
	"curStandardRate"               => true,
);


// ������̤�ɽ�����
$body = '';

for ($i = 0; $i < $resultNum; ++$i) {

	$result = pg_fetch_array($resultID, $i, PGSQL_ASSOC);

	$estimateNo = htmlspecialchars($result['lngestimateno'], ENT_QUOTES);

	$body .= "<tr id=\"". $estimateNo."\" class=\"estimate_search_result\" style=\"background:#FFFFFF\" onclick=\"fncSelectTrColor( this );\">";

	$number = $i + 1;

	$body .= "<td nowrap>". $number. "</td>";	

	foreach ($displayColumns as $column) {
		if ($column === 'btnDetail') { // �ܺ�
				$body .= "<td bgcolor=\"#FFFFFF\" align=\"center\" onmouseout=\"trClickFlg='on';\" onclick=\"trClickFlg='off';fncNoSelectSomeTrColor( this, 'TD" . $resultNum . "_',1 );\">";
				$body .= "<button type=\"button\" class=\"cells btnDetail\" action=\"/estimate/preview/index.php?strSessionID=". $strSessionID. "&estimateNo=". $estimateNo."\" value=\"". $result['lngrevisionno']. "\">";
				$body .= "<img onmouseover=\"DetailOn(this);\" onmouseout=\"DetailOff(this);\" src=\"/img/". LAYOUT_CODE. "/wf/result/detail_off_bt.gif\" width=\"15\" height=\"15\" border=\"0\" alt=\"DETAIL\">";
				$body .= "</button></td>";

		} else if ($column === 'btnHistory') { // ����
			if ($result['lngmaxrevisionno'] > 1 && $result['lngrevisionno'] === $result['lngmaxrevisionno'] ) {
				$body .= "<td bgcolor=\"#FFFFFF\" align=\"center\" onmouseout=\"trClickFlg='on';\" onclick=\"trClickFlg='off';fncNoSelectSomeTrColor( this, 'TD" . $resultNum . "_',1 );\">";
				$body .= "<button type=\"button\" class=\"cells btnHistory\">";
				$body .= "<img onmouseover=\"RenewOn(this);\" onmouseout=\"RenewOff(this);\" src=\"/img/". LAYOUT_CODE. "/cmn/seg/renew_off_bt.gif\" width=\"15\" height=\"15\" border=\"0\" alt=\"HISTORY\">";
				$body .= "</button></td>";
			} else {
				$body .= "<td nowrap align=\"left\"></td>";
			}

		} else if ($column === 'btnDelete') { // ���
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

// Ʊ�����ܤΥ����Ȥϵս�ˤ������
list ( $column, $sortNum, $DESC ) = explode ( "_", $aryData["strSort"] );

if ( $DESC == 'ASC' ) {
	$pattern = $aryData["strSort"];
	$replace = str_replace('ASC', 'DESC', $pattern);

	$header = str_replace ($pattern, $replace, $header);
}

// �������̤ξ����hidden���Ϥ�
unset($aryData["strSort"]);

$form = makeHTML::getHiddenData($aryData);

// �١����ƥ�ץ졼���ɤ߹���
$objTemplate = new clsTemplate();
$objTemplate->getTemplate( "estimate/result/base.tmpl" );

$baseData['strSessionID'] = $strSessionID;
$baseData['FORM'] = $form;
$baseData['HEADER'] = $header;
$baseData['tabledata'] = $body;

// �١����ƥ�ץ졼������
$objTemplate->replace( $baseData );
$objTemplate->complete();

// HTML����
echo $objTemplate->strTemplate;

$objDB->close();


return TRUE;
?>
