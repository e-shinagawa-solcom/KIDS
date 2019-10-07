<?php

header("Content-Type: application/json; charset=UTF-8");

require ( 'conf.inc' );										// 設定読み込み
require ( LIB_DEBUGFILE );									// Debugモジュール

require ( LIB_FILE );										// ライブラリ読み込み

require_once ( SRC_ROOT . "/estimate/cmn/estimateDB.php");  // データベースオブジェクト

$objDB = new estimateDB();
$objAuth = new clsAuth();

//-------------------------------------------------------------------------
// DBオープン
//-------------------------------------------------------------------------
$objDB->InputEncoding = 'UTF-8';
$objDB->open( "", "", "", "" );

//-------------------------------------------------------------------------
// パラメータ取得
//-------------------------------------------------------------------------
$aryData	= array();
$aryData	= $_POST;

//-------------------------------------------------------------------------
// 入力文字列値・セッション・権限チェック
//-------------------------------------------------------------------------
// 文字列チェック
$aryCheck["strSessionID"] = "null:numenglish(32,32)";
$aryResult	= fncAllCheck( $aryData, $aryCheck );
fncPutStringCheckError( $aryResult, $objDB );

// セッション確認
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

// ユーザーコード取得
$lngUserCode = $objAuth->UserCode;

// 権限確認
if( !fncCheckAuthority( DEF_FUNCTION_UP0, $objAuth ) )
{
    fncOutputError ( 9052, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
}

// 権限グループコードの取得
$lngAuthorityGroupCode = fncGetUserAuthorityGroupCode( $lngUserCode, $aryData["strSessionID"], $objDB );

$displayColumns = explode(',', $aryData['display']);

// // 見積原価のデータ取得
// $selectQuery = 
// 	"SELECT
// 		TO_CHAR(mp.dtminsertdate, 'YYYY/MM/DD') AS dtminsertdate,
// 		mp.strproductcode AS strproductcode,
// 		mp.strproductname AS strproductname,
// 		'[' || mg.strgroupdisplaycode || ']' || mg.strgroupdisplayname AS strinchargegroupdisplaycode,
// 		'[' || mu1.struserdisplaycode || ']' || mu1.struserdisplayname AS strinchargeuserdisplaycode,
// 		'[' || mu2.struserdisplaycode || ']' || mu2.struserdisplayname AS strdevelopuserdisplaycode,
// 		TO_CHAR(mp.dtmdeliverylimitdate, 'YYYY/MM/DD') AS dtmdeliverylimitdate,
// 		mp.curretailprice,
// 		mp.lngcartonquantity,
// 		mp.lngproductionquantity,
// 		me.cursalesamount,
// 		me.cursalesamount - me.curmanufacturingcost AS cursalesprofit,
// 		CASE WHEN me.cursalesamount = 0 THEN 0 ELSE (me.cursalesamount - me.curmanufacturingcost) / me.cursalesamount * 100 END AS cursalesprofitrate,
// 		tsum.curfixedcostsales,
// 		tsum.curfixedcostsales - tsum.curnotdepreciationcost AS curfixedcostsalesprofit,
// 		CASE WHEN tsum.curfixedcostsales = 0 THEN 0 ELSE (tsum.curfixedcostsales - tsum.curnotdepreciationcost) / tsum.curfixedcostsales * 100 END AS curfixedcostsalesprofitrate,
// 		me.cursalesamount + tsum.curfixedcostsales AS curtotalsales,
// 		me.curtotalprice,
// 		CASE WHEN me.cursalesamount + tsum.curfixedcostsales = 0 THEN 0 ELSE me.curtotalprice / (me.cursalesamount + tsum.curfixedcostsales) * 100 END AS curtotalpricerate,
// 		me.curtotalprice - me.curprofit AS curindirectmanufacturingcost,
// 		CASE WHEN me.cursalesamount + tsum.curfixedcostsales = 0 THEN 0 ELSE (me.curtotalprice - me.curprofit) / (me.cursalesamount + tsum.curfixedcostsales) * 100 END AS curstandardrate,
// 		me.curprofit,
// 		CASE WHEN me.cursalesamount + tsum.curfixedcostsales = 0 THEN 0 ELSE me.curprofit / (me.cursalesamount + tsum.curfixedcostsales) * 100 END AS curprofitrate,
// 		me.curmembercost,
// 		CASE WHEN mp.lngproductionquantity = 0 THEN 0 ELSE me.curmembercost / mp.lngproductionquantity END AS curmembercostpieces,
// 		me.curfixedcost,
// 		CASE WHEN mp.lngproductionquantity = 0 THEN 0 ELSE me.curfixedcost / mp.lngproductionquantity END AS curfixedcostpieces,
// 		me.curmanufacturingcost AS curmanufacturingcost,
// 		CASE WHEN mp.lngproductionquantity = 0 THEN 0 ELSE me.curmanufacturingcost / mp.lngproductionquantity END AS curmanufacturingcostpieces,
// 		CASE WHEN countofreceiveandorderdetail = countofaplicatedetail THEN TRUE ELSE FALSE END AS deleteflag,
// 		me.lngestimateno,
// 		mp.strrevisecode,
// 		me.lngrevisionno,
// 		me.lngrevisionno AS lngmaxrevisionno
		
// 	FROM m_estimate me
	
// 	INNER JOIN m_product mp
// 		ON mp.strproductcode = me.strproductcode
// 		AND mp.strrevisecode = me.strrevisecode
// 		AND mp.lngrevisionno = me.lngproductrevisionno
	
// 	INNER JOIN m_group mg
// 		ON mg.lnggroupcode = mp.lnginchargegroupcode
	
// 	INNER JOIN m_user mu1
// 		ON mu1.lngusercode = mp.lnginchargeusercode
	
// 	INNER JOIN m_user mu2
// 		ON mu2.lngusercode = mp.lngdevelopusercode
	
// 	LEFT OUTER JOIN
// 	(
// 		SELECT 
// 			me.lngestimateno,
// 			me.lngrevisionno,
// 			SUM(CASE WHEN mscdl.lngestimateareaclassno = 2 THEN ted.curconversionrate * ted.cursubtotalprice ELSE 0 END) AS curfixedcostsales,
// 			SUM(CASE WHEN mscdl.lngestimateareaclassno = 2 AND ted.bytpayofftargetflag = FALSE THEN ted.curconversionrate * ted.cursubtotalprice ELSE 0 END) AS curnotdepreciationcost,
// 			count(mscdl.lngestimateareaclassno <> 0 OR msi.lngestimateareaclassno <> 5 OR NULL) AS countofreceiveandorderdetail,
// 			count(mr.lngreceivestatuscode = 1 OR mo.lngorderstatuscode = 1 OR NULL) AS countofaplicatedetail
// 		FROM t_estimatedetail ted
// 		INNER JOIN m_estimate me
// 			ON me.lngestimateno = ted.lngestimateno
// 			AND me.lngrevisionno = ted.lngrevisionno
// 		LEFT OUTER JOIN  m_salesclassdivisonlink mscdl
// 			ON mscdl.lngsalesclasscode = ted.lngsalesclasscode
// 			AND mscdl.lngsalesdivisioncode = ted.lngsalesdivisioncode		
// 		LEFT OUTER JOIN m_stockitem msi
// 		    ON msi.lngstocksubjectcode = ted.lngstocksubjectcode
// 			AND msi.lngstockitemcode = ted.lngstockitemcode
// 		LEFT OUTER JOIN t_receivedetail trd
// 		    ON trd.lngestimateno = ted.lngestimateno
// 			AND trd.lngestimatedetailno = ted.lngestimatedetailno
// 			AND trd.lngestimaterevisionno = ted.lngrevisionno
// 		LEFT OUTER JOIN m_receive mr
// 		    ON mr.lngreceiveno = trd.lngreceiveno
// 			AND mr.lngrevisionno = trd.lngrevisionno
// 		LEFT OUTER JOIN t_orderdetail tod
// 		    ON tod.lngestimateno = ted.lngestimateno
// 			AND tod.lngestimatedetailno = ted.lngestimatedetailno
// 			AND tod.lngestimaterevisionno = ted.lngrevisionno
// 		LEFT OUTER JOIN m_order mo
// 		    ON mo.lngorderno = tod.lngorderno
// 			AND mo.lngrevisionno = tod.lngrevisionno
// 		GROUP BY me.lngestimateno, me.lngrevisionno
// 	) tsum
		
// 		ON tsum.lngestimateno = me.lngestimateno
//         AND tsum.lngrevisionno = me.lngrevisionno

//     WHERE me.lngrevisionno >= 0
//     AND me.lngestimateno = ". $aryData['estimateNo'];


//     if ($fromCondition && $toCondition) {
//         $search = $fromCondition. " AND ". $toCondition; 
//     } else if ($fromCondition) {
//         $search = $fromCondition;
//     } else if ($toCondition) {
//         $search = $toCondition;
//     }

//     if ($search) {
//         if ($where) {
//             $where .= " AND ". $search;
//         } else {
//             $where = " WHERE ". $search;
//         }
//     }























// list($resultID, $resultNumber) = fncQuery($strQuery, $objDB); // [0]:結果ID [1]:取得行数

// if ($resultNumber < 1) {
//     $result = '';
// } else {
//     $result = pg_fetch_object($resultID, 0);
// }

// $objDB->freeResult($resultID);

// $ret = json_encode($result, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);

// echo $ret;

exit;