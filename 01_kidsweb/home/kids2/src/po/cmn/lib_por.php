<?php
// ----------------------------------------------------------------------------
/**
*       発注管理  発注確定関連関数群
*
*
*       @package    K.I.D.S.
*       @license    http://www.kuwagata.co.jp/
*       @copyright  KUWAGATA CO., LTD.
*       @author     K.I.D.S. Groups <info@kids-groups.com>
*       @access     public
*       @version    2.00
*
*
*       処理概要
*         ・発注確定関連の関数
*
*       更新履歴
*
*/
// ----------------------------------------------------------------------------

/**
* 発注No.に一致する発注データのヘッダを取得
*
*	発注No.から 発注データを取得する
*
*	@param	Integer	$lngOrderNo 発注Ｎｏ
*	@param  Object	$objDB		DB接続オブジェクト
*	@return String 	$strQuery   発注データ(ヘッダ)
*	@access public
*/
function fncGetOrder($lngOrderNo, $objDB){
    $aryQuery = array();
    $aryQuery[] = "SELECT";
    $aryQuery[] = "   mo.strordercode";
    $aryQuery[] = "  ,od.lngrevisionno";
    // $aryQuery[] = "  ,mo.dtmexpirationdate";
    $aryQuery[] = "  ,TO_CHAR(NOW(), 'YYYY/MM/DD') AS dtmexpirationdate";
    $aryQuery[] = "  ,od.strproductcode";
    $aryQuery[] = "  ,mo.lngpayconditioncode";
    $aryQuery[] = "  ,mo.lngmonetaryunitcode";
    $aryQuery[] = "  ,mc.strcompanydisplaycode";
    $aryQuery[] = "  ,mc.strcompanydisplayname";
    $aryQuery[] = "  ,mg.strgroupdisplaycode";
    $aryQuery[] = "  ,mg.strgroupdisplayname";
    $aryQuery[] = "  ,mpd.strproductname";
    $aryQuery[] = "  ,mpd.strproductenglishname";
    $aryQuery[] = "  ,mc2.strcompanydisplaycode as strcompanydisplaycode2";
    $aryQuery[] = "  ,mc2.strcompanydisplayname as strcompanydisplayname2";
    $aryQuery[] = "  ,mo.lngOrderNo";
    $aryQuery[] = "  ,mc.lngcountrycode";
    $aryQuery[] = "  ,mc.straddress1";
    $aryQuery[] = "  ,mc.straddress2";
    $aryQuery[] = "  ,mc.straddress3";
    $aryQuery[] = "  ,mc.strtel1";
    $aryQuery[] = "  ,mc.strfax1";
    $aryQuery[] = "  ,mm.strmonetaryunitname";
    $aryQuery[] = "  ,mm.strmonetaryunitsign";
    $aryQuery[] = "  ,mpc.strpayconditionname";
    $aryQuery[] = "  ,ms.txtsignaturefilename";
    $aryQuery[] = "  ,mo.lngcustomercompanycode";
    $aryQuery[] = "  ,mo.lnggroupcode";
    $aryQuery[] = "  ,mo.lngusercode";
    $aryQuery[] = "  ,mu.struserdisplaycode";
    $aryQuery[] = "  ,mu.struserdisplayname";
    $aryQuery[] = "  ,mc2.lngcompanycode as lngcompanycode2";
    $aryQuery[] = "FROM m_order mo";
    $aryQuery[] = "INNER JOIN t_orderdetail od";
    $aryQuery[] = "  ON  mo.lngorderno = od.lngorderno";
    $aryQuery[] = "  AND mo.lngrevisionno = od.lngrevisionno";
    $aryQuery[] = "LEFT JOIN m_company mc";
    $aryQuery[] = "  ON  mo.lngcustomercompanycode = mc.lngcompanycode";
    $aryQuery[] = "LEFT JOIN m_group mg";
    $aryQuery[] = "  ON  mo.lnggroupcode = mg.lnggroupcode";
    $aryQuery[] = "LEFT JOIN m_product mpd";
    $aryQuery[] = "  ON  od.strproductcode = mpd.strproductcode";
    $aryQuery[] = "LEFT JOIN m_company mc2";
    $aryQuery[] = "  ON  mo.lngdeliveryplacecode = mc2.lngcompanycode";
    $aryQuery[] = "LEFT JOIN m_monetaryunit mm";
    $aryQuery[] = "  ON  mo.lngmonetaryunitcode = mm.lngmonetaryunitcode";
    $aryQuery[] = "LEFT JOIN m_paycondition mpc";
    $aryQuery[] = "  ON  mo.lngpayconditioncode = mpc.lngpayconditioncode";
    $aryQuery[] = "LEFT JOIN m_signature ms";
    $aryQuery[] = "  ON  mo.lnggroupcode = ms.lnggroupcode";
    $aryQuery[] = "LEFT JOIN m_user mu";
    $aryQuery[] = "  ON  mo.lngusercode = mu.lngusercode";
    // $aryQuery[] = "WHERE mo.lngorderno = " . $lngOrderNo;
    $aryQuery[] = "WHERE mo.lngorderno IN (" . $lngOrderNo . ")";

    $strQuery = implode("\n", $aryQuery);
    
	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
	if ( !$lngResultNum )
	{
		return FALSE;
	}

	$lngFieldsCount = $objDB->getFieldsCount( $lngResultID );
	
	if($lngMaxFieldsCount)
	{
		if($lngFieldsCount > $lngMaxFieldsCount) $lngFieldsCount = $lngMaxFieldsCount;
    }

	for ( $i = 0; $i < $lngResultNum; $i++ ) {
        $aryResult[] = $objDB->fetchArray( $lngResultID, $i );
    }

    $objDB->freeResult( $lngResultID );
    return $aryResult;
}
/**
* 発注No.に一致する発注データの明細を取得
*
*	発注No.から 発注明細データを取得する
*
*	@param	Integer	$lngOrderNo 発注Ｎｏ
*	@param  Object	$objDB		DB接続オブジェクト
*	@return String 	$strDetail  発注データ(明細)
*	@access public
*/
function fncGetOrderDetail($lngOrderNo, $objDB){
    // $aryQuery = array();
    $aryQuery[] = "SELECT DISTINCT ON (mo.strordercode, mo.lngrevisionno, od.lngorderdetailno) ";
    $aryQuery[] = "   mo.strordercode || '_' || TO_CHAR(mo.lngrevisionno,'FM00') AS strordercode";
    $aryQuery[] = "  ,od.lngorderdetailno";
    $aryQuery[] = "  ,od.strproductcode";
    $aryQuery[] = "  ,mp.strproductname";
    $aryQuery[] = "  ,od.dtmdeliverydate";
    $aryQuery[] = "  ,mc.strcompanydisplaycode";
    $aryQuery[] = "  ,mc.strcompanydisplayname";
    $aryQuery[] = "  ,od.lngstocksubjectcode";
    $aryQuery[] = "  ,mss.strstocksubjectname";
    $aryQuery[] = "  ,od.lngstockitemcode";
    $aryQuery[] = "  ,msi.strstockitemname";
    $aryQuery[] = "  ,od.lngdeliverymethodcode";
    $aryQuery[] = "  ,od.lngproductunitcode";
    $aryQuery[] = "  ,od.lngsortkey";
    $aryQuery[] = "  ,mmu.strmonetaryunitsign";
    $aryQuery[] = "  ,od.curproductprice";
    $aryQuery[] = "  ,od.lngproductquantity";
    $aryQuery[] = "  ,od.cursubtotalprice";
    $aryQuery[] = "  ,od.curtaxprice";
    $aryQuery[] = "  ,mpu.strproductunitname";
    $aryQuery[] = "  ,od.lngdeliverymethodcode";
    $aryQuery[] = "  ,od.strnote";
    $aryQuery[] = "  ,mo.lngrevisionno";
    $aryQuery[] = "FROM m_order mo";
    $aryQuery[] = "INNER JOIN t_orderdetail od";
    $aryQuery[] = "  ON  mo.lngorderno = od.lngorderno";
    $aryQuery[] = "  AND mo.lngrevisionno = od.lngrevisionno";
    $aryQuery[] = "INNER JOIN (SELECT strordercode, lngrevisionno FROM m_order WHERE lngorderno = " . $lngOrderNo . ") o";
    $aryQuery[] = "  ON  mo.strordercode = o.strordercode";
    $aryQuery[] = "  AND mo.lngrevisionno = o.lngrevisionno";
    $aryQuery[] = "LEFT JOIN m_product mp";
    $aryQuery[] = "  ON  od.strproductcode = mp.strproductcode";
    $aryQuery[] = "LEFT JOIN m_company mc";
    $aryQuery[] = "  ON  mo.lngcustomercompanycode = mc.lngcompanycode";
    $aryQuery[] = "LEFT JOIN m_stocksubject mss";
    $aryQuery[] = "  ON  od.lngstocksubjectcode = mss.lngstocksubjectcode";
    $aryQuery[] = "LEFT JOIN m_stockitem msi";
    $aryQuery[] = "  ON  od.lngstockitemcode = msi.lngstockitemcode";
    $aryQuery[] = "  AND od.lngstocksubjectcode = msi.lngstocksubjectcode";
    $aryQuery[] = "LEFT JOIN m_productprice mpp";
    $aryQuery[] = "  ON  mp.lngproductno = mpp.lngproductno";
    $aryQuery[] = "  AND od.lngstockitemcode = mpp.lngstockitemcode";
    $aryQuery[] = "  AND od.lngstocksubjectcode = mpp.lngstocksubjectcode";
    $aryQuery[] = "LEFT JOIN m_monetaryunit mmu";
    $aryQuery[] = "  ON  mo.lngmonetaryunitcode = mmu.lngmonetaryunitcode";
    $aryQuery[] = "LEFT JOIN m_productunit mpu";
    $aryQuery[] = "  ON  od.lngproductunitcode = mpu.lngproductunitcode";
    $aryQuery[] = "WHERE mo.lngorderstatuscode = 1";

    $strQuery = implode("\n", $aryQuery);
    
	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
	if ( !$lngResultNum )
	{
		return FALSE;
	}

	$lngFieldsCount = $objDB->getFieldsCount( $lngResultID );
	
	if($lngMaxFieldsCount)
	{
		if($lngFieldsCount > $lngMaxFieldsCount) $lngFieldsCount = $lngMaxFieldsCount;
    }

	for ( $i = 0; $i < $lngResultNum; $i++ ) {
        $aryResult[] = $objDB->fetchArray( $lngResultID, $i1 );
    }

    $objDB->freeResult( $lngResultID );
    return $aryResult;
}
function fncGetOrderDetailHtml($aryOrderDetail, $strDelivery){
    $strHtml = "";
    for($i=0; $i < count($aryOrderDetail); $i++){
        $strHtml .= "<tr>";
        // 確定選択(チェックボックス)
        $strHtml .= "<td class=\"detailCheckbox\" style=\"width:20px;align-items: center;\"><input type=\"checkbox\" name=\"edit\"></td>";
        $strDisplayValue = "";
        // 発注NO.
        $strDisplayValue = htmlspecialchars($aryOrderDetail[$i]["strordercode"]);
        $strHtml .= "<td class=\"detailOrderCode\">" . $strDisplayValue . "</td>";
        // 明細行番号
        $strDisplayValue = htmlspecialchars($aryOrderDetail[$i]["lngorderdetailno"]);
        $strHtml .= "<td class=\"detailOrderDetailNo\">" . $strDisplayValue . "</td>";
        // 仕入科目
        $strDisplayCode = htmlspecialchars($aryOrderDetail[$i]["lngstocksubjectcode"]);
        $strDisplayValue = htmlspecialchars($aryOrderDetail[$i]["strstocksubjectname"]);
        $strHtml .= "<td class=\"detailStockSubjectName\">[" . $strDisplayCode . "] " . $strDisplayValue . "</td>";
        // 仕入部品
        $strDisplayCode = htmlspecialchars($aryOrderDetail[$i]["lngstockitemcode"]);
        $strDisplayValue = htmlspecialchars($aryOrderDetail[$i]["strstockitemname"]);
        $strHtml .= "<td class=\"detailStockItemName\">[" . $strDisplayCode . "] " . $strDisplayValue . "</td>";
        // 仕入先
        $strDisplayCode = htmlspecialchars($aryOrderDetail[$i]["strcompanydisplaycode"]);
        $strDisplayValue = htmlspecialchars($aryOrderDetail[$i]["strcompanydisplayname"]);
        $strHtml .= "<td class=\"detailCompanyDisplayCode\">[" . $strDisplayCode . "] " . $strDisplayValue . "</td>";
        // 単価
        $strDisplayValue = htmlspecialchars($aryOrderDetail[$i]["curproductprice"]);
        $strHtml .= "<td class=\"detailProductPrice\" style=\"text-align:right;\">" . number_format($strDisplayValue, 4) . "</td>";
        // 数量
        $strDisplayValue = htmlspecialchars($aryOrderDetail[$i]["lngproductquantity"]);
        $strHtml .= "<td class=\"detailProductQuantity\" style=\"text-align:right;\">" . number_format($strDisplayValue) . "</td>";
        // 税抜金額
        $strDisplayValue = htmlspecialchars($aryOrderDetail[$i]["cursubtotalprice"]);
        $strHtml .= "<td class=\"detailSubtotalPrice\" style=\"text-align:right;\">" . number_format($strDisplayValue, 4) . "</td>";
        // 納期
        $strDisplayValue = str_replace("-", "/", htmlspecialchars($aryOrderDetail[$i]["dtmdeliverydate"]));
        $strHtml .= "<td class=\"detailDeliveryDate\">" . $strDisplayValue . "</td>";
        // 備考
        $strDisplayValue = htmlspecialchars($aryOrderDetail[$i]["strnote"]);
        $strHtml .= "<td class=\"detailNote\">" . $strDisplayValue . "</td>";
        // 運搬方法(明細入力用)
        $strHtml .= "<td class=\"forEdit detailDeliveryMethod\"><select name=\"optDelivery\">" . $strDelivery . "</select></td>";

        $strHtml .= "</tr>";
    }
    return $strHtml;
}

/**		funPulldownMenu()関数
*
*		プルダウンメニューの生成
*
*		@param Long		$lngProcessNo		// 処理番号
*		@param Long		$lngValueCode		// value値
*		@param String	$strWhere			// 条件
*		@param Object	$objDB				// DB接続オブジェクト
*		@return	Array	$strPulldownMenu
*/
// -----------------------------------------------------------------
function fncPulldownMenu ( $lngProcessNo, $lngValueCode, $strWhere, $objDB )
{
    switch($lngProcessNo){
        case 0:
            $strPulldownMenu = fncGetPulldown("m_paycondition", "lngpayconditioncode", "strpayconditionname", $lngValueCode, $strWhere, $objDB);
            break;
        case 1:
            $strPulldownMenu = fncGetPulldown("m_monetaryunit", "lngmonetaryunitcode", "strmonetaryunitname", $lngValueCode, $strWhere, $objDB);
            break;
        case 2:
            $strPulldownMenu = fncGetPulldown("m_deliverymethod", "lngdeliverymethodcode", "strdeliverymethodname", $lngValueCode, $strWhere, $objDB);
            break;
    }
    return $strPulldownMenu;
}

function fncUpdateOrder($aryUpdate, $aryDetail, $objDB){
    $strQuery = "SELECT lngcompanycode FROM m_company WHERE strcompanydisplaycode = '" . $aryUpdate["lngdeliveryplacecode"] . "'";
	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
	if ( !$lngResultNum )
	{
		return FALSE;
	}
    $lngdeliveryplacecode = $objDB->fetchArray( $lngResultID, 0 )[0];

    // m_order更新
    $aryQuery[] = "UPDATE m_order SET";
    $aryQuery[] = "   dtmexpirationdate = '" . $aryUpdate["dtmexpirationdate"] . "'";
    $aryQuery[] = "  ,lngpayconditioncode = " . intVal($aryUpdate["lngpayconditioncode"]);
    $aryQuery[] = "  ,lngdeliveryplacecode = " . $lngdeliveryplacecode;
    $aryQuery[] = "  ,lngorderstatuscode = " . intVal($aryUpdate["lngorderstatuscode"]);
    $aryQuery[] = "WHERE lngorderno = " . intVal($aryUpdate["lngorderno"]);
    $aryQuery[] = "AND   lngrevisionno = " . intVal($aryUpdate["lngrevisionno"]);

    $strQuery = "";
    $strQuery = implode("\n", $aryQuery );

    if ( !$lngResultID = $objDB->execute( $strQuery ) )
    {
        fncOutputError ( 9051, DEF_ERROR, "発注マスタへの更新処理に失敗しました。", TRUE, "", $objDB );
        return FALSE;
    }
    $objDB->freeResult( $lngResultID );

    // t_orderdetail更新
    for($i = 0; $i < count($aryDetail); $i++){
        $aryDetailQuery[] = "UPDATE t_orderdetail SET";
        $aryDetailQuery[] = "   lngdeliverymethodcode = " . intval($aryDetail[$i]["lngdeliverymethodcode"]);
        $aryDetailQuery[] = "  ,lngsortkey = " . intval($aryDetail[$i]["lngsortkey"]);
        $aryDetailQuery[] = "WHERE lngorderno = " . intval($aryUpdate["lngorderno"]);
        $aryDetailQuery[] = "AND   lngorderdetailno = ". intval($aryDetail[$i]["lngorderdetailno"]);
        $aryDetailQuery[] = "AND   lngrevisionno = " . intval($aryUpdate["lngrevisionno"]);

        $strDetailQuery = "";
        $strDetailQuery = implode("\n", $aryDetailQuery );

        if ( !$lngResultID = $objDB->execute( $strDetailQuery ) )
        {
            fncOutputError ( 9051, DEF_ERROR, "発注明細テーブルへの更新処理に失敗しました。", TRUE, "", $objDB );
            return FALSE;
        }
        $objDB->freeResult( $lngResultID );
        return true;
    }
}

function fncUpdatePurchaseOrder($aryOrder, $aryDetail, $objAuth, $objDB){
    // 発注書マスタ
    $lngpurchaseorderno = fncIsSequence("m_purchaseorder.lngpurchaseorderno", $objDB);
    $aryQuery[] = "INSERT INTO m_purchaseorder (";
    $aryQuery[] = "   lngpurchaseorderno";
    $aryQuery[] = "  ,lngrevisionno";
    $aryQuery[] = "  ,strordercode";
    $aryQuery[] = "  ,lngcustomercode";
    $aryQuery[] = "  ,strcustomername";
    $aryQuery[] = "  ,strcustomercompanyaddreess";
    $aryQuery[] = "  ,strcustomercompanytel";
    $aryQuery[] = "  ,strcustomercompanyfax";
    $aryQuery[] = "  ,strproductcode";
    $aryQuery[] = "  ,strproductname";
    $aryQuery[] = "  ,strproductenglishname";
    $aryQuery[] = "  ,dtmexpirationdate";
    $aryQuery[] = "  ,lngmonetaryunitcode";
    $aryQuery[] = "  ,strmonetaryunitsign";
    // $aryQuery[] = "  ,lngmonetaryratecode";
    // $aryQuery[] = "  ,strmonetaryratename";
    $aryQuery[] = "  ,lngpayconditioncode";
    $aryQuery[] = "  ,strpayconditionname";
    $aryQuery[] = "  ,lnggroupcode";
    $aryQuery[] = "  ,strgroupname";
    $aryQuery[] = "  ,txtsignaturefilename";
    $aryQuery[] = "  ,lngusercode";
    $aryQuery[] = "  ,strusername";
    $aryQuery[] = "  ,lngdeliveryplacecode";
    $aryQuery[] = "  ,strdeliveryplacename";
    $aryQuery[] = "  ,curtotalprice";
    $aryQuery[] = "  ,dtminsertdate";
    $aryQuery[] = "  ,lnginsertusercode";
    $aryQuery[] = "  ,strinsertusername";
    $aryQuery[] = "  ,strnote";
    $aryQuery[] = "  ,lngprintcount";
    $aryQuery[] = ") VALUES (";
    $aryQuery[] = "   "  . $lngpurchaseorderno;
    $aryQuery[] = "  ,"  . $aryOrder["lngrevisionno"];
    $aryQuery[] = "  ,'" . sprintf('%s_%02d', $aryOrder["strordercode"], $aryOrder["lngrevisionno"]) . "'";
    $aryQuery[] = "  ,"  . $aryOrder["lngcustomercompanycode"];
    $aryQuery[] = "  ,'" . $aryOrder["strcompanydisplayname"] . "'";
    $aryQuery[] = "  ,'" . $aryOrder["straddress1"]. $aryOrder["straddress2"]. $aryOrder["straddress3"] . "'";
    $aryQuery[] = "  ,'" . $aryOrder["strtel1"] . "'";
    $aryQuery[] = "  ,'" . $aryOrder["strfax1"] . "'";
    $aryQuery[] = "  ,'" . $aryOrder["strproductcode"] . "'";
    $aryQuery[] = "  ,'" . $aryOrder["strproductname"] . "'";
    $aryQuery[] = "  ,'" . $aryOrder["strproductenglishname"] . "'";
    $aryQuery[] = "  ,'" . $aryOrder["dtmexpirationdate"] . "'";
    $aryQuery[] = "  ,"  . $aryOrder["lngmonetaryunitcode"];
    $aryQuery[] = "  ,'" . $aryOrder["strmonetaryunitsign"] . "'";
    //  ,lngmonetaryratecode
    //  ,strmonetaryratename
    $aryQuery[] = "  ,"  . $aryOrder["lngpayconditioncode"];
    $aryQuery[] = "  ,'" . $aryOrder["strpayconditionname"] . "'";
    $aryQuery[] = "  ,"  . $aryOrder["lnggroupcode"];
    $aryQuery[] = "  ,'" . $aryOrder["strgroupdisplayname"] . "'";
    $aryQuery[] = "  ,'" . $aryOrder["txtsignaturefilename"] . "'";
    $aryQuery[] = "  ,"  . $aryOrder["lngusercode"];
    $aryQuery[] = "  ,'" . $aryOrder["struserdisplayname"] . "'";
    $aryQuery[] = "  ,"  . $aryOrder["lngcompanycode2"];
    $aryQuery[] = "  ,'" . $aryOrder["strcompanydisplayname2"] . "'";
    $aryQuery[] = "  ,0"; //curtotalprice
    $aryQuery[] = "  ,NOW()"; //dtminsertdate
    $aryQuery[] = "  ,'" . $objAuth->UserID . "'"; //lnginsertusercode
    $aryQuery[] = "  ,'" . $objAuth->UserDisplayName . "'";  //strinsertusername
    $aryQuery[] = "  ,''"; //strnote
    $aryQuery[] = "  ,0";
    $aryQuery[] = ")";

    $strQuery = implode("\n", $aryQuery );

    if ( !$lngResultID = $objDB->execute( $strQuery ) )
    {
        fncOutputError ( 9051, DEF_ERROR, "発注書マスタへの更新処理に失敗しました。", TRUE, "", $objDB );
        return FALSE;
    }
    $objDB->freeResult( $lngResultID );

    // 発注書詳細
    for($i = 0; $i < count($aryDetail); $i++){
        $aryDetailQuery[] = "INSERT INTO t_purchaseorderdetail (";
        $aryDetailQuery[] = "   lngpurchaseorderno";
        $aryDetailQuery[] = "  ,lngpurchaseorderdetailno";
        $aryDetailQuery[] = "  ,lngrevisionno";
        // $aryDetailQuery[] = "  ,lngorderno";
        // $aryDetailQuery[] = "  ,lngorderdetailno";
        // $aryDetailQuery[] = "  ,lngorderrevisionno";
        // $aryDetailQuery[] = "  ,lngstocksubjectcode";
        $aryDetailQuery[] = "  ,lngstockitemcode";
        $aryDetailQuery[] = "  ,strstockitemname";
        $aryDetailQuery[] = "  ,lngdeliverymethodcode";
        $aryDetailQuery[] = "  ,strdeliverymethodname";
        $aryDetailQuery[] = "  ,curproductprice";
        $aryDetailQuery[] = "  ,lngproductquantity";
        $aryDetailQuery[] = "  ,lngproductunitcode";
        $aryDetailQuery[] = "  ,strproductunitname";
        $aryDetailQuery[] = "  ,cursubtotalprice";
        $aryDetailQuery[] = "  ,dtmdeliverydate";
        $aryDetailQuery[] = "  ,strnote";
        $aryDetailQuery[] = "  ,lngsortkey";
        $aryDetailQuery[] = ") VALUES (";
        $aryDetailQuery[] = "   "  . $lngpurchaseorderno;
        $aryDetailQuery[] = "  ,"  . $aryDetail[$i]["lngorderdetailno"];
        $aryDetailQuery[] = "  ,'" . sprintf('%s_%02d', $aryOrder["strordercode"], $aryOrder["lngrevisionno"]) . "'";
        // $aryDetailQuery[] = "  ,lngorderno";
        // $aryDetailQuery[] = "  ,lngorderdetailno";
        // $aryDetailQuery[] = "  ,lngorderrevisionno";
        // $aryDetailQuery[] = "  ,lngstocksubjectcode";
        $aryDetailQuery[] = "  ,"  . $aryDetail[$i]["lngstockitemcode"];
        $aryDetailQuery[] = "  ,'" . $aryDetail[$i]["strstockitemname"] . "'";
        $aryDetailQuery[] = "  ,"  . $aryDetail[$i]["lngdeliverymethodcode"];
        $aryDetailQuery[] = "  ,'" . $aryDetail[$i]["strdeliverymethodname"] . "'";
        $aryDetailQuery[] = "  ,"  . $aryDetail[$i]["curproductprice"];
        $aryDetailQuery[] = "  ,"  . $aryDetail[$i]["lngproductquantity"];
        $aryDetailQuery[] = "  ,"  . $aryDetail[$i]["lngproductunitcode"];
        $aryDetailQuery[] = "  ,'" . $aryDetail[$i]["strproductunitname"] . "'";
        $aryDetailQuery[] = "  ,"  . $aryDetail[$i]["cursubtotalprice"];
        $aryDetailQuery[] = "  ,"  . $aryDetail[$i]["dtmdeliverydate"];
        $aryDetailQuery[] = "  ,'" . $aryDetail[$i]["strnote"] . "'";
        // $aryDetailQuery[] = "  ,lngsortkey";
        $aryDetailQuery[] = ")";
    
    }




    return true;
}

