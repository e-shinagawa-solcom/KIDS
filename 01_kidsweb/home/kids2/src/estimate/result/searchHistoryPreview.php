<?php
// ----------------------------------------------------------------------------
/**
 *       見積原価検索 履歴取得イベント
 *
 *       処理概要
 *         ・見積原価コード、リビジョン番号により見積原価履歴情報を取得する
 *
 *       更新履歴
 *
 */

 // 読み込み
include 'conf.inc';
require LIB_FILE;
include 'JSON.php';
require_once (SRC_ROOT. "estimate/cmn/makeHTML.php");
require SRC_ROOT . "search/cmn/lib_search.php";

//値の取得
$postdata = file_get_contents("php://input");
$aryData = json_decode($postdata, true);
$objDB = new clsDB();
$objAuth = new clsAuth();
$objDB->open("", "", "", "");
//JSONクラスインスタンス化
$s = new Services_JSON();
//値が存在しない場合は通常の POST で受ける
if ($aryData == null) {
    $aryData = $_POST;
}

// セッション確認
$objAuth = fncIsSession($_REQUEST["strSessionID"], $objAuth, $objDB);

$displayColumns = array();
// 表示項目の抽出
foreach ($aryData["displayColumns"] as $key) {
    $displayColumns[$key] = $key;
}

// 請求コードにより仕入履歴取得SQL
$aryQuery[] = "SELECT";
$aryQuery[] = "  TO_CHAR(me.dtmInsertDate, 'YYYY/MM/DD') AS dtminsertdate";
$aryQuery[] = "  , mp.strproductcode || '_' || mp.strrevisecode as strproductcode";
$aryQuery[] = "  , mp.strproductname";
$aryQuery[] = "  , mp.strproductenglishname";
$aryQuery[] = "  , '[' || mg.strgroupdisplaycode || ']' || mg.strgroupdisplayname AS lnginchargegroupcode";
$aryQuery[] = "  , '[' || mu1.struserdisplaycode || ']' || mu1.struserdisplayname AS lnginchargeusercode";
$aryQuery[] = "  , '[' || mu2.struserdisplaycode || ']' || mu2.struserdisplayname AS lngdevelopusercode";
$aryQuery[] = "  , mp.curretailprice";
$aryQuery[] = "  , '[' || mu3.struserdisplaycode || ']' || mu3.struserdisplayname AS lnginputusercode";
$aryQuery[] = "  , mp.lngcartonquantity";
$aryQuery[] = "  , mp.lngproductionquantity";
$aryQuery[] = "  , me.cursalesamount";
$aryQuery[] = "  , me.cursalesamount - me.curmanufacturingcost AS cursalesprofit";
$aryQuery[] = "  , CASE ";
$aryQuery[] = "    WHEN me.cursalesamount = 0 ";
$aryQuery[] = "      THEN 0 ";
$aryQuery[] = "    ELSE (me.cursalesamount - me.curmanufacturingcost) / me.cursalesamount * 100 ";
$aryQuery[] = "    END AS cursalesprofitrate";
$aryQuery[] = "  , tsum.curfixedcostsales";
$aryQuery[] = "  , tsum.curfixedcostsales - tsum.curnotdepreciationcost AS curfixedcostsalesprofit";
$aryQuery[] = "  , CASE ";
$aryQuery[] = "    WHEN tsum.curfixedcostsales = 0 ";
$aryQuery[] = "      THEN 0 ";
$aryQuery[] = "    ELSE ( ";
$aryQuery[] = "      tsum.curfixedcostsales - tsum.curnotdepreciationcost";
$aryQuery[] = "    ) / tsum.curfixedcostsales * 100 ";
$aryQuery[] = "    END AS curfixedcostsalesprofitrate";
$aryQuery[] = "  , me.cursalesamount + tsum.curfixedcostsales AS curtotalsales";
$aryQuery[] = "  , me.curtotalprice";
$aryQuery[] = "  , CASE ";
$aryQuery[] = "    WHEN me.cursalesamount + tsum.curfixedcostsales = 0 ";
$aryQuery[] = "      THEN 0 ";
$aryQuery[] = "    ELSE me.curtotalprice / (me.cursalesamount + tsum.curfixedcostsales) * 100 ";
$aryQuery[] = "    END AS curtotalpricerate";
$aryQuery[] = "  , me.curtotalprice - me.curprofit AS curindirectmanufacturingcost";
$aryQuery[] = "  , CASE ";
$aryQuery[] = "    WHEN me.cursalesamount + tsum.curfixedcostsales = 0 ";
$aryQuery[] = "      THEN 0 ";
$aryQuery[] = "    ELSE (me.curtotalprice - me.curprofit) / (me.cursalesamount + tsum.curfixedcostsales) * 100 ";
$aryQuery[] = "    END AS curstandardrate";
$aryQuery[] = "  , me.curprofit";
$aryQuery[] = "  , CASE ";
$aryQuery[] = "    WHEN me.cursalesamount + tsum.curfixedcostsales = 0 ";
$aryQuery[] = "      THEN 0 ";
$aryQuery[] = "    ELSE me.curprofit / (me.cursalesamount + tsum.curfixedcostsales) * 100 ";
$aryQuery[] = "    END AS curprofitrate";
$aryQuery[] = "  , me.curmembercost";
$aryQuery[] = "  , CASE ";
$aryQuery[] = "    WHEN mp.lngproductionquantity = 0 ";
$aryQuery[] = "      THEN 0 ";
$aryQuery[] = "    ELSE me.curmembercost / mp.lngproductionquantity ";
$aryQuery[] = "    END AS curmembercostpieces";
$aryQuery[] = "  , me.curfixedcost";
$aryQuery[] = "  , CASE ";
$aryQuery[] = "    WHEN mp.lngproductionquantity = 0 ";
$aryQuery[] = "      THEN 0 ";
$aryQuery[] = "    ELSE me.curfixedcost / mp.lngproductionquantity ";
$aryQuery[] = "    END AS curfixedcostpieces";
$aryQuery[] = "  , me.curmanufacturingcost AS curmanufacturingcost";
$aryQuery[] = "  , CASE ";
$aryQuery[] = "    WHEN mp.lngproductionquantity = 0 ";
$aryQuery[] = "      THEN 0 ";
$aryQuery[] = "    ELSE me.curmanufacturingcost / mp.lngproductionquantity ";
$aryQuery[] = "    END AS curmanufacturingcostpieces";
$aryQuery[] = "  , CASE ";
$aryQuery[] = "    WHEN tsum.countofreceiveandorderdetail = tsum.countofaplicatedetail ";
$aryQuery[] = "      THEN TRUE ";
$aryQuery[] = "    ELSE FALSE ";
$aryQuery[] = "    END AS deleteflag";
$aryQuery[] = "  , me.lngestimateno";
$aryQuery[] = "  , mp.strrevisecode";
$aryQuery[] = "  , me.lngrevisionno";
$aryQuery[] = "  , me.lngrevisionno AS lngmaxrevisionno ";
$aryQuery[] = "FROM";
$aryQuery[] = "  m_estimate me ";
$aryQuery[] = "  INNER JOIN m_product mp ";
$aryQuery[] = "    ON mp.strproductcode = me.strproductcode ";
$aryQuery[] = "    AND mp.strrevisecode = me.strrevisecode ";
$aryQuery[] = "    AND mp.lngrevisionno = me.lngproductrevisionno ";
$aryQuery[] = "  INNER JOIN m_group mg ";
$aryQuery[] = "    ON mg.lnggroupcode = mp.lnginchargegroupcode ";
$aryQuery[] = "  INNER JOIN m_user mu1 ";
$aryQuery[] = "    ON mu1.lngusercode = mp.lnginchargeusercode ";
$aryQuery[] = "  LEFT OUTER JOIN m_user mu2 ";
$aryQuery[] = "    ON mu2.lngusercode = mp.lngdevelopusercode ";
$aryQuery[] = "  INNER JOIN m_user mu3 ";
$aryQuery[] = "    ON mu3.lngusercode = mp.lnginputusercode ";
$aryQuery[] = "  LEFT OUTER JOIN ( ";
$aryQuery[] = "    SELECT";
$aryQuery[] = "      me.lngestimateno";
$aryQuery[] = "      , me.lngrevisionno";
$aryQuery[] = "      , SUM( ";
$aryQuery[] = "        CASE ";
$aryQuery[] = "          WHEN mscdl.lngestimateareaclassno = 2 ";
$aryQuery[] = "            THEN ted.curconversionrate * ted.cursubtotalprice ";
$aryQuery[] = "          ELSE 0 ";
$aryQuery[] = "          END";
$aryQuery[] = "      ) AS curfixedcostsales";
$aryQuery[] = "      , SUM( ";
$aryQuery[] = "        CASE ";
$aryQuery[] = "          WHEN msi.lngestimateareaclassno = 3 ";
$aryQuery[] = "          AND ted.bytpayofftargetflag = FALSE ";
$aryQuery[] = "            THEN ted.curconversionrate * ted.cursubtotalprice ";
$aryQuery[] = "          ELSE 0 ";
$aryQuery[] = "          END";
$aryQuery[] = "      ) AS curnotdepreciationcost";
$aryQuery[] = "      , count( ";
$aryQuery[] = "        mscdl.lngestimateareaclassno <> 0 ";
$aryQuery[] = "        OR msi.lngestimateareaclassno <> 5 ";
$aryQuery[] = "        OR NULL";
$aryQuery[] = "      ) AS countofreceiveandorderdetail";
$aryQuery[] = "      , count( ";
$aryQuery[] = "        mr.lngreceivestatuscode = 1 ";
$aryQuery[] = "        OR mo.lngorderstatuscode = 1 ";
$aryQuery[] = "        OR NULL";
$aryQuery[] = "      ) AS countofaplicatedetail ";
$aryQuery[] = "    FROM";
$aryQuery[] = "      m_estimate me ";
$aryQuery[] = "      INNER JOIN m_estimatehistory meh ";
$aryQuery[] = "        ON meh.lngestimateno = me.lngestimateno ";
$aryQuery[] = "        AND meh.lngrevisionno = me.lngrevisionno ";
$aryQuery[] = "      INNER JOIN t_estimatedetail ted ";
$aryQuery[] = "        ON meh.lngestimateno = ted.lngestimateno ";
$aryQuery[] = "        AND meh.lngestimatedetailno = ted.lngestimatedetailno ";
$aryQuery[] = "        AND meh.lngestimatedetailrevisionno = ted.lngrevisionno ";
$aryQuery[] = "      LEFT OUTER JOIN m_salesclassdivisonlink mscdl ";
$aryQuery[] = "        ON mscdl.lngsalesclasscode = ted.lngsalesclasscode ";
$aryQuery[] = "        AND mscdl.lngsalesdivisioncode = ted.lngsalesdivisioncode ";
$aryQuery[] = "      LEFT OUTER JOIN m_stockitem msi ";
$aryQuery[] = "        ON msi.lngstocksubjectcode = ted.lngstocksubjectcode ";
$aryQuery[] = "        AND msi.lngstockitemcode = ted.lngstockitemcode ";
$aryQuery[] = "      LEFT OUTER JOIN t_receivedetail trd ";
$aryQuery[] = "        ON trd.lngestimateno = ted.lngestimateno ";
$aryQuery[] = "        AND trd.lngestimatedetailno = ted.lngestimatedetailno ";
$aryQuery[] = "        AND trd.lngestimaterevisionno = ted.lngrevisionno ";
$aryQuery[] = "      LEFT OUTER JOIN m_receive mr ";
$aryQuery[] = "        ON mr.lngreceiveno = trd.lngreceiveno ";
$aryQuery[] = "        AND mr.lngrevisionno = trd.lngrevisionno ";
$aryQuery[] = "      LEFT OUTER JOIN t_orderdetail tod ";
$aryQuery[] = "        ON tod.lngestimateno = ted.lngestimateno ";
$aryQuery[] = "        AND tod.lngestimatedetailno = ted.lngestimatedetailno ";
$aryQuery[] = "        AND tod.lngestimaterevisionno = ted.lngrevisionno ";
$aryQuery[] = "      LEFT OUTER JOIN m_order mo ";
$aryQuery[] = "        ON mo.lngorderno = tod.lngorderno ";
$aryQuery[] = "        AND mo.lngrevisionno = tod.lngrevisionno ";
$aryQuery[] = "    GROUP BY";
$aryQuery[] = "      me.lngestimateno";
$aryQuery[] = "      , me.lngrevisionno";
$aryQuery[] = "  ) tsum ";
$aryQuery[] = "    ON tsum.lngestimateno = me.lngestimateno ";
$aryQuery[] = "    AND tsum.lngrevisionno = me.lngrevisionno ";
$aryQuery[] = "WHERE";
$aryQuery[] = "  me.lngestimateno = ". $aryData["lngEstimateNo"] ;
$aryQuery[] = "  AND me.lngrevisionno <> ". $aryData["lngRevisionNo"] ;
$aryQuery[] = "ORDER BY";
$aryQuery[] = "  me.lngrevisionno DESC";
$strQuery = implode("\n", $aryQuery);

// 値をとる =====================================
list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);


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


for ($i = 0; $i < $lngResultNum; ++$i) {

	$result = pg_fetch_array($lngResultID, $i, PGSQL_ASSOC);
    
	$bgcolor = fncSetBgColor('estimate', $result["lngestimateno"], false, $objDB);
	
	$estimateNo = htmlspecialchars($result['lngestimateno'], ENT_QUOTES);

	$body .= "<tr id=\"". ($estimateNo."_".$result["lngrevisionno"]). "\" class=\"estimate_search_result\" style=\"" . $bgcolor . "\ onclick=\"fncSelectTrColor( this );\">";

	$number = $i + 1;

	$body .= "<td nowrap>". $aryData["rownum"] . "." .$number. "</td>";	

	foreach ($displayColumns as $column) {
		 // 詳細、履歴、削除
		if ($column === 'btnDetail' || $column === 'btnHistory' || $column === 'btnDelete') {
			$body .= "<td nowrap align=\"left\"></td>";
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

// HTML出力
echo $body;
