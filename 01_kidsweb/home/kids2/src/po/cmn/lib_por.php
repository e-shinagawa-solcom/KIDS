<?php
// ----------------------------------------------------------------------------
/**
 *       ȯ�����  ȯ������Ϣ�ؿ���
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
 *       ��������
 *         ��ȯ������Ϣ�δؿ�
 *
 *       ��������
 *
 */
// ----------------------------------------------------------------------------

/**
 * ȯ��No.�˰��פ���ȯ��ǡ����Υإå������
 *
 *	ȯ��No.���� ȯ��ǡ������������
 *
 *	@param	Integer	$lngOrderNo ȯ��Σ�
 *	@param  Object	$objDB		DB��³���֥�������
 *	@return String 	$strQuery   ȯ��ǡ���(�إå�)
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
 * ��ҥޥ�������
 * 
 * @param   Integer     $lngCompanyCode     ��ҥ�����
 *	@param  Object		$objDB			    DB���֥�������
 *	@access public
 * 
 */
function fncGetCompany($lngCompanyCode, $objDB){
    $aryQuery[] = "SELECT ";
    $aryQuery[] = "   lngcompanycode ";
    $aryQuery[] = "  ,lngcountrycode ";
    $aryQuery[] = "  ,lngorganizationcode ";
    $aryQuery[] = "  ,bytorganizationfront ";
    $aryQuery[] = "  ,strcompanyname ";
    $aryQuery[] = "  ,bytcompanydisplayflag ";
    $aryQuery[] = "  ,strcompanydisplaycode ";
    $aryQuery[] = "  ,strcompanydisplayname ";
    $aryQuery[] = "  ,strshortname ";
    $aryQuery[] = "  ,strpostalcode ";
    $aryQuery[] = "  ,straddress1 ";
    $aryQuery[] = "  ,straddress2 ";
    $aryQuery[] = "  ,straddress3 ";
    $aryQuery[] = "  ,straddress4 ";
    $aryQuery[] = "  ,strtel1 ";
    $aryQuery[] = "  ,strtel2 ";
    $aryQuery[] = "  ,strfax1 ";
    $aryQuery[] = "  ,strfax2 ";
    $aryQuery[] = "  ,strdistinctcode ";
    $aryQuery[] = "  ,lngcloseddaycode ";
    $aryQuery[] = "FROM m_company ";
    $aryQuery[] = "WHERE lngcompanycode = " . $lngCompanyCode;

    $strQuery = implode("\n", $aryQuery);
    
	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
	if ( !$lngResultNum )
	{
		return FALSE;
	}

	$lngFieldsCount = $objDB->getFieldsCount( $lngResultID );
	
    $result = $objDB->fetchArray( $lngResultID, 0);

    $objDB->freeResult( $lngResultID );
    return $result;
}

/**
 * ��ʧ���ޥ�������
 * 
 * @param   Integer     $lngpayconditioncode    ��ʧ��拾����
 * @param   Object      $objDB                  DB���֥�������
 * @access  public
 * 
 */
function fncGetPayCondition($lngpayconditioncode, $objDB){
    $aryQuery[] = "SELECT ";
    $aryQuery[] = "   lngpayconditioncode ";
    $aryQuery[] = "  ,strpayconditionname";
    $aryQuery[] = "FROM m_paycondition ";
    $aryQuery[] = "WHERE lngpayconditioncode = " . $lngpayconditioncode;

    $strQuery = implode("\n", $aryQuery);
    
	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
	if ( !$lngResultNum )
	{
		return FALSE;
	}

	$lngFieldsCount = $objDB->getFieldsCount( $lngResultID );
	
    $result = $objDB->fetchArray( $lngResultID, 0);

    $objDB->freeResult( $lngResultID );
    return $result;
}

/**
 * ȯ��No.�˰��פ���ȯ��ǡ��������٤����
 *
 *	ȯ��No.���� ȯ�����٥ǡ������������
 *
 *	@param	Integer	$lngOrderNo ȯ��Σ�
 *	@param  Object	$objDB		DB��³���֥�������
 *	@return String 	$strDetail  ȯ��ǡ���(����)
 *	@access public
 */
function fncGetOrderDetail($aryOrderNo, $lngRevisionNo, $objDB){
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
    $aryQuery[] = "  ,mo.lngorderno";
    $aryQuery[] = "  ,mo.lngmonetaryunitcode";
    $aryQuery[] = "  ,mo.lngcustomercompanycode";
    $aryQuery[] = "FROM m_order mo";
    $aryQuery[] = "INNER JOIN t_orderdetail od";
    $aryQuery[] = "  ON  mo.lngorderno = od.lngorderno";
    $aryQuery[] = "  AND mo.lngrevisionno = od.lngrevisionno";
    // $aryQuery[] = "INNER JOIN (SELECT strordercode, lngrevisionno FROM m_order WHERE lngorderno = " . $lngOrderNo . ") o";
    // $aryQuery[] = "INNER JOIN (SELECT strordercode, lngrevisionno FROM m_order WHERE lngorderno IN (" . $aryOrderNo . ")) o";
    $aryQuery[] = "INNER JOIN (SELECT strordercode, lngrevisionno, lngcustomercompanycode, lngmonetaryunitcode FROM m_order WHERE lngorderno = " . $aryOrderNo . " AND lngrevisionno = " . $lngRevisionNo . ") o";
    $aryQuery[] = "  ON  mo.strordercode = o.strordercode";
    $aryQuery[] = "  AND mo.lngrevisionno = o.lngrevisionno";
    $aryQuery[] = "  AND mo.lngcustomercompanycode = o.lngcustomercompanycode";
    $aryQuery[] = "  AND mo.lngmonetaryunitcode = o.lngmonetaryunitcode";
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
        $aryResult[] = $objDB->fetchArray( $lngResultID, $i );
    }

    $objDB->freeResult( $lngResultID );
    return $aryResult;
}

/**
 * ȯ�����ٸ���
 * 
 * @param   Integer     $lngOrderNo         ȯ���ֹ�
 * @param   Integer     $lngOrderDetailNo   ȯ�������ֹ�
 * @param   Integer     $lngRevisionNo      ��ӥ�����ֹ�
 * @param   Object      $objDB              DB���֥�������
 * @access  public
 * 
 */
function fncGetOrderDetail2($lngOrderNo, $lngOrderDetailNo, $lngRevisioNno, $objDB){
    $aryQuery[] = "SELECT ";
    $aryQuery[] = "   mo.lngorderno ";
    $aryQuery[] = "  ,mo.lngrevisionno ";
    $aryQuery[] = "  ,od.lngorderdetailno ";
    $aryQuery[] = "  ,mo.lngcustomercompanycode ";
    $aryQuery[] = "  ,mo.lngdeliveryplacecode ";
    $aryQuery[] = "  ,od.strproductcode ";
    $aryQuery[] = "  ,mp.strproductname ";
    $aryQuery[] = "  ,mp.strproductenglishname ";
    $aryQuery[] = "  ,mo.lngmonetaryunitcode ";
    $aryQuery[] = "  ,mm.strmonetaryunitname ";
    $aryQuery[] = "  ,mm.strmonetaryunitsign ";
    $aryQuery[] = "  ,mo.lnggroupcode ";
    $aryQuery[] = "  ,mg.strgroupdisplayname ";
    $aryQuery[] = "  ,ms.txtsignaturefilename ";
    $aryQuery[] = "  ,mo.lngusercode ";
    $aryQuery[] = "  ,mu.struserdisplayname ";
    $aryQuery[] = "  ,od.lngstockitemcode ";
    $aryQuery[] = "  ,od.lngstocksubjectcode ";
    $aryQuery[] = "  ,msi.strstockitemname ";
    $aryQuery[] = "  ,od.lngdeliverymethodcode ";
    $aryQuery[] = "  ,md.strdeliverymethodname ";
    $aryQuery[] = "  ,od.curproductprice ";
    $aryQuery[] = "  ,od.lngproductquantity ";
    $aryQuery[] = "  ,od.lngproductunitcode ";
    $aryQuery[] = "  ,mpu.strproductunitname ";
    $aryQuery[] = "  ,od.cursubtotalprice ";
    $aryQuery[] = "  ,TO_CHAR(od.dtmdeliverydate, 'YYYY/MM/DD') AS dtmdeliverydate ";
    $aryQuery[] = "  ,od.strnote ";
    $aryQuery[] = "FROM m_order mo ";
    $aryQuery[] = "INNER JOIN t_orderdetail od ";
    $aryQuery[] = "  ON  mo.lngorderno = od.lngorderno ";
    $aryQuery[] = "  AND mo.lngrevisionno = od.lngrevisionno ";
    $aryQuery[] = "LEFT JOIN m_product mp ";
    $aryQuery[] = "  ON  od.strproductcode = mp.strproductcode ";
    $aryQuery[] = "LEFT JOIN m_monetaryunit mm ";
    $aryQuery[] = "  ON  mo.lngmonetaryunitcode = mm.lngmonetaryunitcode ";
    $aryQuery[] = "LEFT JOIN m_group mg ";
    $aryQuery[] = "  ON  mo.lnggroupcode = mg.lnggroupcode ";
    $aryQuery[] = "LEFT JOIN m_signature ms ";
    $aryQuery[] = "  ON  mo.lnggroupcode = ms.lnggroupcode ";
    $aryQuery[] = "LEFT JOIN m_user mu ";
    $aryQuery[] = "  ON  mo.lngusercode = mu.lngusercode ";
    $aryQuery[] = "LEFT JOIN m_stockitem msi ";
    $aryQuery[] = "  ON  od.lngstockitemcode = msi.lngstockitemcode ";
    $aryQuery[] = "  AND od.lngstocksubjectcode = msi.lngstocksubjectcode ";
    $aryQuery[] = "LEFT JOIN m_deliverymethod md ";
    $aryQuery[] = "  ON  od.lngdeliverymethodcode = md.lngdeliverymethodcode ";
    $aryQuery[] = "LEFT JOIN m_productunit mpu ";
    $aryQuery[] = "  ON  od.lngproductunitcode = mpu.lngproductunitcode ";
    $aryQuery[] = "WHERE mo.lngorderno = " . $lngOrderNo;
    $aryQuery[] = "AND   od.lngorderdetailno = " . $lngOrderDetailNo;
    $aryQuery[] = "AND   mo.lngrevisionno = " . $lngRevisioNno;

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

    $aryResult = $objDB->fetchArray( $lngResultID, 0 );

    $objDB->freeResult( $lngResultID );
    return $aryResult;
}

/**
 * ȯ������HTML�ǡ�������
 * 
 * @param   Array   $aryOrderDetail     ȯ�����٥ǡ���
 * @param   String  $strDelivery        ������ˡ
 * @access  public
 * 
 */
function fncGetOrderDetailHtml($aryOrderDetail, $strDelivery){
    $strHtml = "";
    for($i=0; $i < count($aryOrderDetail); $i++){
        $strHtml .= "<tr>";
        // ��������(�����å��ܥå���)
        $strHtml .= "<td class=\"detailCheckbox\" style=\"width:20px;align-items: center;\"><input type=\"checkbox\" name=\"edit\"></td>";
        $strDisplayValue = "";
        // ȯ��NO.
        $strDisplayValue = htmlspecialchars($aryOrderDetail[$i]["strordercode"]);
        $strHtml .= "<td class=\"detailOrderCode\">" . $strDisplayValue . "</td>";
        // ���ٹ��ֹ�
        $strDisplayValue = htmlspecialchars($aryOrderDetail[$i]["lngorderdetailno"]);
        $strHtml .= "<td class=\"detailOrderDetailNo\">" . $strDisplayValue . "</td>";
        // ��������
        $strDisplayCode = htmlspecialchars($aryOrderDetail[$i]["lngstocksubjectcode"]);
        $strDisplayValue = htmlspecialchars($aryOrderDetail[$i]["strstocksubjectname"]);
        $strHtml .= "<td class=\"detailStockSubjectName\">[" . $strDisplayCode . "] " . $strDisplayValue . "</td>";
        // ��������
        $strDisplayCode = htmlspecialchars($aryOrderDetail[$i]["lngstockitemcode"]);
        $strDisplayValue = htmlspecialchars($aryOrderDetail[$i]["strstockitemname"]);
        $strHtml .= "<td class=\"detailStockItemName\">[" . $strDisplayCode . "] " . $strDisplayValue . "</td>";
        // ������
        $strDisplayCode = htmlspecialchars($aryOrderDetail[$i]["strcompanydisplaycode"]);
        $strDisplayValue = htmlspecialchars($aryOrderDetail[$i]["strcompanydisplayname"]);
        $strHtml .= "<td class=\"detailCompanyDisplayCode\">[" . $strDisplayCode . "] " . $strDisplayValue . "</td>";
        // ñ��
        $strDisplayValue = htmlspecialchars($aryOrderDetail[$i]["curproductprice"]);
        $strHtml .= "<td class=\"detailProductPrice\" style=\"text-align:right;\">" . number_format($strDisplayValue, 4) . "</td>";
        // ����
        $strDisplayValue = htmlspecialchars($aryOrderDetail[$i]["lngproductquantity"]);
        $strHtml .= "<td class=\"detailProductQuantity\" style=\"text-align:right;\">" . number_format($strDisplayValue) . "</td>";
        // ��ȴ���
        $strDisplayValue = htmlspecialchars($aryOrderDetail[$i]["cursubtotalprice"]);
        $strHtml .= "<td class=\"detailSubtotalPrice\" style=\"text-align:right;\">" . number_format($strDisplayValue, 4) . "</td>";
        // Ǽ��
        $strDisplayValue = str_replace("-", "/", htmlspecialchars($aryOrderDetail[$i]["dtmdeliverydate"]));
        $strHtml .= "<td class=\"detailDeliveryDate\">" . $strDisplayValue . "</td>";
        // ����
        $strDisplayValue = htmlspecialchars($aryOrderDetail[$i]["strnote"]);
        $strHtml .= "<td class=\"detailNote\">" . $strDisplayValue . "</td>";
        // ������ˡ(����������)
        $strHtml .= "<td class=\"forEdit detailDeliveryMethod\"><select name=\"optDelivery\">" . $strDelivery . "</select></td>";
        // ñ�̥�����(������Ͽ��)
        $strDisplayCode = htmlspecialchars($aryOrderDetail[$i]["lngproductunitcode"]);
        $strHtml .= "<td class=\"forEdit detailProductUnitCode\">" . $strDisplayCode . "</td>";
        // ȯ���ֹ�(������Ͽ��)
        $strDisplayCode = htmlspecialchars($aryOrderDetail[$i]["lngorderno"]);
        $strHtml .= "<td class=\"forEdit detailOrderNo\">" . $strDisplayCode . "</td>";
        // ��ӥ�����ֹ�(������Ͽ��)
        $strDisplayCode = htmlspecialchars($aryOrderDetail[$i]["lngrevisionno"]);
        $strHtml .= "<td class=\"forEdit detailRevisionNo\">" . $strDisplayCode . "</td>";
        // �������ܥ�����(������Ͽ��)
        $strDisplayCode = htmlspecialchars($aryOrderDetail[$i]["lngstocksubjectcode"]);
        $strHtml .= "<td class=\"forEdit detailStockSubjectCode\">" . $strDisplayCode . "</td>";
        // �������ʥ�����(������Ͽ��)
        $strDisplayCode = htmlspecialchars($aryOrderDetail[$i]["lngstockitemcode"]);
        $strHtml .= "<td class=\"forEdit detailStockItemCode\">" . $strDisplayCode . "</td>";
        // �̲�ñ�̥�����(������Ͽ��)
        $strDisplayCode = htmlspecialchars($aryOrderDetail[$i]["lngmonetaryunitcode"]);
        $strHtml .= "<td class=\"forEdit detailMonetaryUnitCode\">" . $strDisplayCode . "</td>";
        // �����襳����(������Ͽ��)
        $strDisplayCode = htmlspecialchars($aryOrderDetail[$i]["lngcustomercompanycode"]);
        $strHtml .= "<td class=\"forEdit detailCustomerCompanyCode\">" . $strDisplayCode . "</td>";

        $strHtml .= "</tr>";
    }
    return $strHtml;
}

/**
 * ȯ���ޥ�������
 * 
 * @param   Array   $aryPurchaseOrderNo     ȯ����ֹ�
 * @param   Object  $objDB                  DB���֥�������
 * @access  public
 * 
 */
function fncGetPurchaseOrder($aryPurchaseOrderNo, $objDB){
    for($i = 0; $i < count($aryPurchaseOrderNo); $i++){
        $aryQuery = [];

        $aryQuery[] = "SELECT ";
        $aryQuery[] = "   mp.lngpurchaseorderno";
        $aryQuery[] = "  ,mp.lngrevisionno";
        $aryQuery[] = "  ,mp.strordercode";
        $aryQuery[] = "  ,mp.strcustomername";
        $aryQuery[] = "  ,mp.strproductcode";
        $aryQuery[] = "  ,mp.strproductname";
        $aryQuery[] = "  ,mp.strdeliveryplacename";
        $aryQuery[] = "  ,mp.strmonetaryunitsign";
        $aryQuery[] = "  ,tp.lngpurchaseorderdetailno";
        $aryQuery[] = "  ,tp.strstockitemname";
        $aryQuery[] = "  ,tp.strdeliverymethodname";
        $aryQuery[] = "  ,tp.curproductprice";
        $aryQuery[] = "  ,tp.lngproductquantity";
        $aryQuery[] = "  ,tp.strproductunitname";
        $aryQuery[] = "  ,tp.cursubtotalprice";
        $aryQuery[] = "  ,TO_CHAR(tp.dtmdeliverydate, 'YYYY/MM/DD') AS dtmdeliverydate";
        $aryQuery[] = "FROM m_purchaseorder mp";
        $aryQuery[] = "INNER JOIN t_purchaseorderdetail tp";
        $aryQuery[] = "  ON  mp.lngpurchaseorderno = tp.lngpurchaseorderno";
        $aryQuery[] = "  AND mp.lngrevisionno = tp.lngrevisionno";
        $aryQuery[] = "WHERE mp.lngpurchaseorderno = " . $aryPurchaseOrderNo[$i]["lngpurchaseorderno"];
        $aryQuery[] = "AND   mp.lngrevisionno = " . $aryPurchaseOrderNo[$i]["lngrevisionno"];
        $aryQuery[] = "ORDER BY";
        $aryQuery[] = "   mp.lngpurchaseorderno";
        $aryQuery[] = "  ,mp.lngrevisionno";
        $aryQuery[] = "  ,tp.lngpurchaseorderdetailno";

        $strQuery = "";
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
    }

    return $aryResult;
}

/**		funPulldownMenu()�ؿ�
 *
 *		�ץ�������˥塼������
 *
 *		@param Long		$lngProcessNo		// �����ֹ�
 *		@param Long		$lngValueCode		// value��
 *		@param String	$strWhere			// ���
 *		@param Object	$objDB				// DB��³���֥�������
 *		@return	Array	$strPulldownMenu
 */
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

/**
 * ȯ��ޥ�������
 * 
 * @param   Array   $aryUpdate          ����ȯ��ǡ���
 * @param   Array   $aryUpdateDetail    ����ȯ�����٥ǡ���
 * @param   Object  $objDB              DB���֥�������
 * @access  public
 * 
 */
function fncUpdateOrder($aryUpdate, $aryUpdateDetail, $objDB){
	$lngcountrycode = fncGetMasterValue( "m_company", "strcompanydisplaycode", "lngcountrycode", $aryUpdate["lngdeliveryplacecode"] . ":str", '', $objDB);
	$lngdeliveryplacecode = fncGetMasterValue( "m_company", "strcompanydisplaycode", "lngcompanycode", $aryUpdate["lngdeliveryplacecode"] . ":str", '', $objDB);

    for($i = 0; $i < count($aryUpdateDetail); $i++){
        $aryQuery = [];
        // m_order����
        $aryQuery[] = "UPDATE m_order SET";
        $aryQuery[] = "   lngdeliveryplacecode = " . $lngdeliveryplacecode;
        $aryQuery[] = "  ,lngorderstatuscode = " . intVal($aryUpdate["lngorderstatuscode"]);
        $aryQuery[] = "WHERE lngorderno = " . intVal($aryUpdateDetail[$i]["lngorderno"]);
        $aryQuery[] = "AND   lngrevisionno = " . intVal($aryUpdateDetail[$i]["lngrevisionno"]);

        $strQuery = "";
        $strQuery = implode("\n", $aryQuery );

        if ( !$lngResultID = $objDB->execute( $strQuery ) )
        {
            fncOutputError ( 9051, DEF_ERROR, "ȯ��ޥ����ؤι��������˼��Ԥ��ޤ�����", TRUE, "", $objDB );
            return FALSE;
        }
        $objDB->freeResult( $lngResultID );

    }

    return true;
}

/**
 * ȯ�����ٹ���
 * 
 * @param   Array   $aryUpdate      ȯ��ޥ����ǡ���
 * @param   Array   $aryDetail      ȯ�����٥ǡ���
 * @param   Object  $objDB          DB���֥�������
 * @access  public
 * 
 */
function fncUpdateOrderDetail($aryUpdate, $aryDetail, $objDB){
    // t_orderdetail����
    for($i = 0; $i < count($aryDetail); $i++){
        $aryDetailQuery = array();
        $aryDetailQuery[] = "UPDATE t_orderdetail SET";
        $aryDetailQuery[] = "   lngdeliverymethodcode = " . intval($aryDetail[$i]["lngdeliverymethodcode"]);
        $aryDetailQuery[] = "  ,lngsortkey = " . intval($aryDetail[$i]["lngsortkey"]);
        $aryDetailQuery[] = "  ,lngproductunitcode = " . intval($aryDetail[$i]["lngproductunitcode"]);
        $aryDetailQuery[] = "WHERE lngorderno = " . intval($aryDetail[$i]["lngorderno"]);
        $aryDetailQuery[] = "AND   lngorderdetailno = ". intval($aryDetail[$i]["lngorderdetailno"]);
        $aryDetailQuery[] = "AND   lngrevisionno = " . intval($aryUpdate["lngrevisionno"]);

        $strDetailQuery = "";
        $strDetailQuery = implode("\n", $aryDetailQuery );

        if ( !$lngResultID = $objDB->execute( $strDetailQuery ) )
        {
            fncOutputError ( 9051, DEF_ERROR, "ȯ�����٥ơ��֥�ؤι��������˼��Ԥ��ޤ�����", TRUE, "", $objDB );
            return FALSE;
        }
        $objDB->freeResult( $lngResultID );
    }
    return true;
}

/**
 * ȯ���ޥ�����Ͽ
 * 
 * @param   Array   $aryOrder       ȯ��ޥ���
 * @param   Array   $aryOrderDetail ȯ������
 * @param   Object  $objAuth        ����
 * @param   Object  $objDB          DB���֥�������
 * @access  public
 * 
 */
function fncInsertPurchaseOrder($aryOrder, $aryOrderDetail, $objAuth, $objDB){

	require_once (LIB_DEBUGFILE);
    $key1 = "lngcustomercompanycode";
    $key2 = "lngmonetaryunitcode";
    $group = [];
    foreach($aryOrderDetail as $row){
        $notKeys = array_filter($row, function($key) use ($key1, $key2) {
            return $key !== $key1 && $key !== $key2;
        }, ARRAY_FILTER_USE_KEY);

        $group[$row[$key1]][$row[$key2]][] = $notKeys;
    }

    foreach($group as $row){
        $aryResult = [];
        foreach ($row as $detail) {
            $curTotalPrice = 0.0;
            foreach($detail as $order){
                $aryOrderNo[] = $order["lngorderno"];
                $detail = fncGetOrderDetail2($order["lngorderno"],$order["lngorderdetailno"], $order["lngrevisionno"], $objDB);
                $aryOrderDetailUpdate[] = $detail;
                $curTotalPrice += floatval($detail["cursubtotalprice"]);
            }

            for($i = 0; $i < count($aryOrderDetailUpdate); $i++){
                if($i == 0){
                    // ȯ���ޥ�����Ͽ
                    $lngpurchaseorderno = fncGetSequence("m_purchaseorder.lngpurchaseorderno", $objDB);
//                    $lngrevisionno = $aryOrderDetailUpdate[$i]["lngrevisionno"] == null ? 0 : intval($aryOrderDetailUpdate[$i]["lngrevisionno"]) + 1;
                    $lngrevisionno = 0;
                    $ym = date('ym');
                    $year = date('y');
                    $month = date('m');
//fncDebug("kids2.log", $year . "/" . $month, __FILE__, __LINE__, "a");
//                    $lngorderno = fncGetSequence("m_purchaseorder.strordercode." . $ym, $objDB);
                    $lngorderno = fncGetDateSequence($year, $month, "m_purchaseorder.strordercode", $objDB);
//fncDebug("kids2.log", $lngorderno, __FILE__, __LINE__, "a");
                    $customer = fncGetCompany($aryOrderDetailUpdate[$i]["lngcustomercompanycode"], $objDB);
                    $delivery = fncGetCompany($aryOrderDetailUpdate[$i]["lngdeliveryplacecode"], $objDB);
                    $payconditioncode = fncPayConditionCode($customer["lngcountrycode"], $aryOrderDetailUpdate[$i], $curTotalPrice, $objDB);
                    $paycondition = fncGetPayCondition($payconditioncode, $objDB);
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
                    $aryQuery[] = "  ,strrevisecode";
                    $aryQuery[] = "  ,strproductname";
                    $aryQuery[] = "  ,strproductenglishname";
                    $aryQuery[] = "  ,dtmexpirationdate";
                    $aryQuery[] = "  ,lngmonetaryunitcode";
                    $aryQuery[] = "  ,strmonetaryunitname";
                    $aryQuery[] = "  ,strmonetaryunitsign";
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
                    $aryQuery[] = "  ,"  . $lngrevisionno;
                    $aryQuery[] = "  ,'" . $lngorderno . "'";
                    $aryQuery[] = "  ,"  . $customer["lngcompanycode"];
                    $aryQuery[] = "  ,'" . $customer["strcompanydisplayname"] . "'";
                    $aryQuery[] = "  ,'" . $customer["straddress1"]. $customer["straddress2"]. $customer["straddress3"] . "'";
                    $aryQuery[] = "  ,'" . $customer["strtel1"] . "'";
                    $aryQuery[] = "  ,'" . $customer["strfax1"] . "'";
                    $aryQuery[] = "  ,'" . $aryOrderDetailUpdate[$i]["strproductcode"] . "'";
                    $aryQuery[] = "  ,'" . sprintf("%02d", $lngrevisionno) . "'";
                    $aryQuery[] = "  ,'" . $aryOrderDetailUpdate[$i]["strproductname"] . "'";
                    $aryQuery[] = "  ,'" . $aryOrderDetailUpdate[$i]["strproductenglishname"] . "'";
                    $aryQuery[] = "  ,'" . $aryOrder["dtmexpirationdate"] . "'";
                    $aryQuery[] = "  ,"  . $aryOrderDetailUpdate[$i]["lngmonetaryunitcode"];
                    $aryQuery[] = "  ,'" . $aryOrderDetailUpdate[$i]["strmonetaryunitname"] . "'";
                    $aryQuery[] = "  ,'" . $aryOrderDetailUpdate[$i]["strmonetaryunitsign"] . "'";
                    $aryQuery[] = "  ,"  . $payconditioncode;
                    $aryQuery[] = "  ,'" . $paycondition["strpayconditionname"] . "'";
                    $aryQuery[] = "  ,"  . $aryOrderDetailUpdate[$i]["lnggroupcode"];
                    $aryQuery[] = "  ,'" . $aryOrderDetailUpdate[$i]["strgroupdisplayname"] . "'";
                    $aryQuery[] = "  ,'" . $aryOrderDetailUpdate[$i]["txtsignaturefilename"] . "'";
                    $aryQuery[] = "  ,"  . $aryOrderDetailUpdate[$i]["lngusercode"];
                    $aryQuery[] = "  ,'" . $aryOrderDetailUpdate[$i]["struserdisplayname"] . "'";
                    $aryQuery[] = "  ,"  . $delivery["lngcompanycode"];
                    $aryQuery[] = "  ,'" . $delivery["strcompanydisplayname"] . "'";
                    $aryQuery[] = "  ,"  . $curTotalPrice;//  . $aryOrderDetailUpdate[$i][0]["curtotalprice"];
                    $aryQuery[] = "  ,NOW()";
                    $aryQuery[] = "  ,'" . $objAuth->UserCode . "'";
                    $aryQuery[] = "  ,'" . $objAuth->UserDisplayName . "'";
                    $aryQuery[] = "  ,'" . $aryOrder["strnote"] . "'";
                    $aryQuery[] = "  ,0";
                    $aryQuery[] = ")";

                    $strQuery = implode("\n", $aryQuery );

                    if ( !$lngResultID = $objDB->execute( $strQuery ) )
                    {
                        fncOutputError ( 9051, DEF_ERROR, "ȯ���ޥ����ؤι��������˼��Ԥ��ޤ�����", TRUE, "", $objDB );
                        return null;
                    }
                    //$aryResult[] = $lngpurchaseorderno . "-" . $lngrevisionno;
                    $aryResult[$i]["lngpurchaseorderno"] = $lngpurchaseorderno;
                    $aryResult[$i]["lngrevisionno"] = $lngrevisionno;
                    $objDB->freeResult( $lngResultID );
                }

                // ȯ���������Ͽ
                $aryQueryDetail = [];
                $aryQueryDetail[] = "INSERT INTO t_purchaseorderdetail ( ";
                $aryQueryDetail[] = "   lngpurchaseorderno";
                $aryQueryDetail[] = "  ,lngpurchaseorderdetailno";
                $aryQueryDetail[] = "  ,lngrevisionno";
                $aryQueryDetail[] = "  ,lngorderno";
                $aryQueryDetail[] = "  ,lngorderdetailno";
                $aryQueryDetail[] = "  ,lngorderrevisionno";
                $aryQueryDetail[] = "  ,lngstocksubjectcode";
                $aryQueryDetail[] = "  ,lngstockitemcode";
                $aryQueryDetail[] = "  ,strstockitemname";
                $aryQueryDetail[] = "  ,lngdeliverymethodcode";
                $aryQueryDetail[] = "  ,strdeliverymethodname";
                $aryQueryDetail[] = "  ,curproductprice";
                $aryQueryDetail[] = "  ,lngproductquantity";
                $aryQueryDetail[] = "  ,lngproductunitcode";
                $aryQueryDetail[] = "  ,strproductunitname";
                $aryQueryDetail[] = "  ,cursubtotalprice";
                $aryQueryDetail[] = "  ,dtmdeliverydate";
                $aryQueryDetail[] = "  ,strnote";
                $aryQueryDetail[] = "  ,lngsortkey";
                $aryQueryDetail[] = ") VALUES (";
                $aryQueryDetail[] = "   "  . $lngpurchaseorderno;
                $aryQueryDetail[] = "  ,"  . ($i + 1);
                $aryQueryDetail[] = "  ,"  . $lngrevisionno;
                $aryQueryDetail[] = "  ,"  . $aryOrderDetailUpdate[$i]["lngorderno"];
                $aryQueryDetail[] = "  ,"  . $aryOrderDetailUpdate[$i]["lngorderdetailno"];
                $aryQueryDetail[] = "  ,"  . $aryOrderDetailUpdate[$i]["lngrevisionno"];
                $aryQueryDetail[] = "  ,"  . $aryOrderDetailUpdate[$i]["lngstocksubjectcode"];
                $aryQueryDetail[] = "  ,"  . $aryOrderDetailUpdate[$i]["lngstockitemcode"];
                $aryQueryDetail[] = "  ,'" . $aryOrderDetailUpdate[$i]["strstockitemname"] . "'";
                $aryQueryDetail[] = "  ,"  . $aryOrderDetailUpdate[$i]["lngdeliverymethodcode"];
                $aryQueryDetail[] = "  ,'" . $aryOrderDetailUpdate[$i]["strdeliverymethodname"] . "'";
                $aryQueryDetail[] = "  ,"  . $aryOrderDetailUpdate[$i]["curproductprice"];
                $aryQueryDetail[] = "  ,"  . $aryOrderDetailUpdate[$i]["lngproductquantity"];
                $aryQueryDetail[] = "  ,"  . $aryOrderDetailUpdate[$i]["lngproductunitcode"];
                $aryQueryDetail[] = "  ,'" . $aryOrderDetailUpdate[$i]["strproductunitname"] . "'";
                $aryQueryDetail[] = "  ,"  . $aryOrderDetailUpdate[$i]["cursubtotalprice"];
                $aryQueryDetail[] = "  ,'" . $aryOrderDetailUpdate[$i]["dtmdeliverydate"] . "'";
                $aryQueryDetail[] = "  ,'" . $aryOrderDetailUpdate[$i]["strnote"] . "'";
                $aryQueryDetail[] = "  ,"  . ($i + 1);
                $aryQueryDetail[] = ")";
   
                $strQueryDetail = implode("\n", $aryQueryDetail );
                if ( !$lngResultID = $objDB->execute( $strQueryDetail ) )
                {
                    fncOutputError ( 9051, DEF_ERROR, "ȯ������٤ؤι��������˼��Ԥ��ޤ�����", TRUE, "", $objDB );
                    return null;
                }
                $objDB->freeResult( $lngResultID );
            }
        }
    }

    return $aryResult;
}

/**
 * ȯ���������Ͽ
 * 
 * @param   Array   $aryOrder   ȯ��ޥ���
 * @param   Array   $aryDetail  ȯ������
 * @param   Object  $objAuth    ����
 * @param   Object  $objDB      DB���֥�������
 * @access  public
 * 
 */
function fncInsertPurchaseOrderDetail($aryOrder, $aryDetail, $objAuth, $objDB){

    // ȯ���ܺ�
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

/**
 * ��ʧ�������
 * 
 * @param   Integer     $lngCountryCode     �񥳡���
 * @param   Array       $aryDetail          ȯ������
 * @param   Currency    $curTotalPrice      ��׶��
 * @param   Object      $objDB              DB���֥�������
 * @access  public
 * 
 */
function fncPayConditionCode($lngContoryCode, $aryDetail, $curTotalPrice, $objDB){
    // �񥳡��ɤ�81�ξ�硢99����
    if($lngContoryCode == 81) { return 0; }

    $arystockitemcode = array(1, 2, 3, 7, 9, 11);
    $aryforeigntable = fncSetForeignTabel();
    for( $i = 0; $i < count( $aryforeigntable ) ; $i++ ){
        if( $aryforeigntable[$i] == $aryDetail["lngcustomercompanycode"] ){
            $flgForeignTable = true;
            break;
        }
    }

    if($flgForeignTable){
        $code = 2;//�����2(T/T)�˥��å�
        if($aryDetail["lngstocksubjectcode"] == "402"){
            if(in_array($aryDetail["lngstockitemcode"], $arystockitemcode, true)){
                $code = 1;
            }
        }
    }else{
        if($aryDetail["lngmonetaryunitcode"] == 2 && $curTotalPrice >= 30000){
            $code = 1;
        } else {
            $code = 2;
        }
    }

    return $code;
}

/**
 * 
 */
function fncSetForeignTabel(){
	
	$aryforeigntable = array(
							1111,
							1112,
							1113,
							1117,
							1119,
							1211,
							1303,
							1304,
							2106,
							2107,
							2207,
							2208,
							2209,
							2215,
							2305,
							2307,
							2308,
							3205,
							3207,
							3209,
							3210,
							3217,
							3220,
							3223,
							3504,
							4202,
							4510,
							4511,
							4512,
							4516,
							5209,
							6106,
							6107,
							6115,
							6117,
							6118,
							6127,
							6318,
							6139,
							6311,
							6320,
							6403,
							6404,
							6505,
							6507,
							8301,
							9101,
							9102,
							9103,
							9104,
							9403,
							9995);
	
	return $aryforeigntable;
}

/**
 * ȯ���ǡ���HTML�Ѵ�
 * 
 * @param   Array   $aryPutchaseOrder   ȯ���ǡ���
 * @access  public
 * 
 */
function fncCreatePurchaseOrderHtml($aryPurchaseOrder, $strSessionID){
    $aryOrderNo = [];
    foreach($aryPurchaseOrder as $row){
        $orderno = $row["lngpurchaseorderno"];
        if(!in_array($orderno, $aryOrderNo)){
            $aryOrderNo[] = $orderno;
        }
    }
    
    foreach($aryOrderNo as $orderno){
        $aryHtml[] = "<p class=\"caption\">ȯ����꤬��λ�����ʲ���ȯ��񤬺�������ޤ�����</p>";
        for($i = 0; $i < count($aryPurchaseOrder); $i++){
            if($aryPurchaseOrder[$i]["lngpurchaseorderno"] != $orderno){ continue;}
            if($i == 0){
                $strUrl = "/list/result/po/listoutput.php?strReportKeyCode=" . $aryPurchaseOrder[$i]["lngpurchaseorderno"] . "&strSessionID=" . $strSessionID;
                $aryHtml[] = "<table class=\"ordercode\">";
                $aryHtml[] = "  <tr>";
                $aryHtml[] = "    <td class=\"ordercodetd\">" . sprintf("%s_%02d", $aryPurchaseOrder[$i]["strordercode"], $aryPurchaseOrder[$i]["lngrevisionno"]) . "</td>";
                $aryHtml[] = "    <td class=\"orderbuttontd\"><a href=\"" . $strUrl . "\"><img src=\"/img/type01/cmn/querybt/preview_off_ja_bt.gif\" alt=\"preview\"></a></td>";
                $aryHtml[] = "  </tr>";
                $aryHtml[] = "</table> ";
                $aryHtml[] = "<br>";
                $aryHtml[] = "<table class=\"orderdetail\">";
                $aryHtml[] = "  <tr>";
                $aryHtml[] = "    <th class=\"SegColumn\">���ʥ�����</th>";
                $aryHtml[] = "    <th class=\"SegColumn\">����̾</th>";
                $aryHtml[] = "    <th class=\"SegColumn\">������</th>";
                $aryHtml[] = "    <th class=\"SegColumn\">Ǽ�ʾ��</th>";
                $aryHtml[] = "  </tr>";
                $aryHtml[] = "  <tr>";
                $aryHtml[] = "    <td class=\"Segs\">" . $aryPurchaseOrder[$i]["strproductcode"] . "</td>";
                $aryHtml[] = "    <td class=\"Segs\">" . $aryPurchaseOrder[$i]["strproductname"] . "</td>";
                $aryHtml[] = "    <td class=\"Segs\">" . $aryPurchaseOrder[$i]["strcustomername"] . "</td>";
                $aryHtml[] = "    <td class=\"Segs\">" . $aryPurchaseOrder[$i]["strdeliveryplacename"] . "</td>";
                $aryHtml[] = "  </tr>";
                $aryHtml[] = "</table>";
                $aryHtml[] = "<table class=\"orderdetail\">";
                $aryHtml[] = "  <tr>";
                $aryHtml[] = "    <th class=\"SegColumn\">No.</th>";
                $aryHtml[] = "    <th class=\"SegColumn\">��������</th>";
                $aryHtml[] = "    <th class=\"SegColumn\">������ˡ</th>";
                $aryHtml[] = "    <th class=\"SegColumn\">ñ��</th>";
                $aryHtml[] = "    <th class=\"SegColumn\">����</th>";
                $aryHtml[] = "    <th class=\"SegColumn\">��ȴ���</th>";
                $aryHtml[] = "    <th class=\"SegColumn\">Ǽ��</th>";
                $aryHtml[] = "  </tr>";
            }
            $aryHtml[] = "  <tr>";
            $aryHtml[] = "    <td class=\"Segs\">" . $aryPurchaseOrder[$i]["lngpurchaseorderdetailno"];
            $aryHtml[] = "    <td class=\"Segs\">" . $aryPurchaseOrder[$i]["strstockitemname"];
            $aryHtml[] = "    <td class=\"Segs\">" . $aryPurchaseOrder[$i]["strdeliverymethodname"];
            $aryHtml[] = "    <td class=\"Segs\">" . $aryPurchaseOrder[$i]["strmonetaryunitsign"] . " " . number_format($aryPurchaseOrder[$i]["curproductprice"], 4);
            $aryHtml[] = "    <td class=\"Segs\">" . number_format($aryPurchaseOrder[$i]["lngproductquantity"]) . " " . $aryPurchaseOrder[$i]["strproductunitname"];
            $aryHtml[] = "    <td class=\"Segs\">" . $aryPurchaseOrder[$i]["strmonetaryunitsign"] . " " . number_format($aryPurchaseOrder[$i]["cursubtotalprice"], 2);
            $aryHtml[] = "    <td class=\"Segs\">" . $aryPurchaseOrder[$i]["dtmdeliverydate"];
            $aryHtml[] = "  </tr>";
        }
        $aryHtml[] = "</table>";
        $aryHtml[] = "<br>";
    }

    $strHtml = "";
    $strHtml = implode("\n", $aryHtml);

    return $strHtml;
}

/**
 * ȯ���ޥ�������
 * 
 * @param   Integer     $lngputchaseorderno ȯ����ֹ�
 * @param   Integer     $lngrevisionno      ��ӥ�����ֹ�
 * @param   Object      $objDb               DB���֥�������
 * @access  public
 * 
 */
function fncGetPurchaseOrderEdit($lngpurchaseorderno, $lngrevisionno, $objDB){
	$aryQuery[] = "SELECT";
	$aryQuery[] = "   mp.lngpurchaseorderno";
	$aryQuery[] = "  ,mp.lngrevisionno";
	$aryQuery[] = "  ,mp.strrevisecode";
	$aryQuery[] = "  ,mp.strordercode";
	$aryQuery[] = "  ,TO_CHAR(mp.dtmexpirationdate, 'YYYY/MM/DD') as dtmexpirationdate";
	$aryQuery[] = "  ,mp.strproductcode";
	$aryQuery[] = "  ,mp.strproductname";
	$aryQuery[] = "  ,mp.strproductenglishname";
	$aryQuery[] = "  ,TO_CHAR(mp.dtminsertdate, 'YYYY/MM/DD') as dtminsertdate";
	$aryQuery[] = "  ,mp.lnggroupcode";
	$aryQuery[] = "  ,mg.strgroupdisplaycode";
	$aryQuery[] = "  ,mg.strgroupdisplayname";
	$aryQuery[] = "  ,mp.lngcustomercode";
	$aryQuery[] = "  ,mc1.strcompanydisplaycode as strcustomercode";
	$aryQuery[] = "  ,mc1.strcompanydisplayname as strcustomername";
	$aryQuery[] = "  ,mp.lngdeliveryplacecode";
	$aryQuery[] = "  ,mc2.strcompanydisplaycode as strdeliveryplacecode";
	$aryQuery[] = "  ,mc2.strcompanydisplayname as strdeliveryplacename";
	$aryQuery[] = "  ,mp.lngpayconditioncode";
    $aryQuery[] = "  ,mp.lngmonetaryunitcode";
    $aryQuery[] = "  ,mp.strmonetaryunitsign";
	$aryQuery[] = "  ,mp.curtotalprice";
	$aryQuery[] = "  ,mp.strnote";
	$aryQuery[] = "  ,pd.lngpurchaseorderdetailno";
	$aryQuery[] = "  ,pd.lngstocksubjectcode";
	$aryQuery[] = "  ,mss.strstocksubjectname";
	$aryQuery[] = "  ,pd.lngstockitemcode";
	$aryQuery[] = "  ,msi.strstockitemname";
    $aryQuery[] = "  ,pd.lngdeliverymethodcode";
    $aryQuery[] = "  ,pd.strdeliverymethodname";
	$aryQuery[] = "  ,pd.curproductprice";
    $aryQuery[] = "  ,pd.lngproductquantity";
    $aryQuery[] = "  ,pd.lngproductunitcode";
    $aryQuery[] = "  ,pd.strproductunitname";
    $aryQuery[] = "  ,pd.cursubtotalprice";
    $aryQuery[] = "  ,TO_CHAR(pd.dtmdeliverydate, 'YYYY/MM/DD') AS dtmdeliverydate";
    $aryQuery[] = "  ,pd.strnote as strdetailnote";
    $aryQuery[] = "  ,pd.lngsortkey";
	$aryQuery[] = "FROM m_purchaseorder mp";
	$aryQuery[] = "LEFT JOIN t_purchaseorderdetail pd ON mp.lngpurchaseorderno = pd.lngpurchaseorderno AND mp.lngrevisionno = pd.lngrevisionno";
	$aryQuery[] = "LEFT JOIN m_group mg ON mp.lnggroupcode = mg.lnggroupcode";
	$aryQuery[] = "LEFT JOIN m_company mc1 ON mp.lngcustomercode = mc1.lngcompanycode";
	$aryQuery[] = "LEFT JOIN m_company mc2 ON mp.lngdeliveryplacecode = mc2.lngcompanycode";
	$aryQuery[] = "LEFT JOIN m_stocksubject mss ON pd.lngstocksubjectcode = mss.lngstocksubjectcode";
    $aryQuery[] = "LEFT JOIN m_stockitem msi ON pd.lngstockitemcode = msi.lngstockitemcode AND pd.lngstocksubjectcode = msi.lngstocksubjectcode";
	$aryQuery[] = "WHERE mp.lngpurchaseorderno = " . $lngpurchaseorderno;
    $aryQuery[] = "AND   mp.lngrevisionno = " . $lngrevisionno;
    $aryQuery[] = "ORDER BY";
    $aryQuery[] = "   pd.lngsortkey";

	$srtQuery = "";
	$strQuery = implode("\n", $aryQuery);

	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
	if ( !$lngResultNum )
	{
		return FALSE;
	}

	for ( $i = 0; $i < $lngResultNum; $i++ ) {
		$aryResult[] = $objDB->fetchArray( $lngResultID, $i );
	}

	$objDB->freeResult( $lngResultID );

	return $aryResult;
}

/**
 * ȯ�������HTML����
 * 
 * @param   Array   $aryResult  
 */
function fncGetPurchaseOrderDetailHtml($aryResult, $objDB){
    for($i = 0; $i < count($aryResult); $i++){
        $aryHtml[] = "  <tr>";
        $aryHtml[] = "      <td name=\"rownum\">" . ($i + 1) . "</td>";
        $aryHtml[] = "      <td class=\"detailOrderCode\">" . sprintf("%s_%02d", $aryResult[$i]["strordercode"], $aryResult[$i]["lngrevisionno"]) . "</td>";
        $aryHtml[] = "      <td class=\"detailPurchaseorderdetailno\">" . $aryResult[$i]["lngpurchaseorderdetailno"] . "</td>";
        $aryHtml[] = "      <td class=\"detailStockSubjectCode\">" . sprintf("[%s] %s", $aryResult[$i]["lngstocksubjectcode"], $aryResult[$i]["strstocksubjectname"]) . "</td>";
        $aryHtml[] = "      <td class=\"detailStockItemCode\">" . sprintf("[%s] %s", $aryResult[$i]["lngstockitemcode"], $aryResult[$i]["strstockitemname"]) . "</td>";
        $aryHtml[] = "      <td class=\"detailDeliveryMethodCode\"><select name=\"lngdeliverymethodcode\">" . fncPulldownMenu(2, $aryResult[$i]["lngdeliverymethodcode"], "", $objDB) ."</select></td>";
        $aryHtml[] = "      <td class=\"detailProductPrice\">" . sprintf("%s %s", $aryResult[$i]["strmonetaryunitsign"], number_format($aryResult[$i]["curproductprice"], 4)) . "</td>";
        $aryHtml[] = "      <td class=\"detailProductQuantity\">" . number_format($aryResult[$i]["lngproductquantity"], 0) . "</td>";
        $aryHtml[] = "      <td class=\"detailSubtotalPrice\">" . sprintf("%s %s", $aryResult[$i]["strmonetaryunitsign"], number_format($aryResult[$i]["cursubtotalprice"], 2)) . "</td>";
        $aryHtml[] = "      <td class=\"detailDeliveryDate\">" . $aryResult[$i]["dtmdeliverydate"] . "</td>";
        $aryHtml[] = "      <td class=\"detailDetailNote\">" . $aryResult[$i]["strdetailnote"] . "</td>";
        $aryHtml[] = "      <td style=\"display:none;\"><input type=\"hidden\" name=\"strProductUnitName\" value=\"" . $aryResult[$i]["strproductunitname"] . "\"></td>";
        $aryHtml[] = "  </tr>";
    }

    return implode("\n", $aryHtml);
}

/**
 * ȯ���ޥ�������
 * 
 * @param   Array   $aryPurchaseOrder   ȯ���ǡ���
 * @param   Object  $objDB              DB���֥�������
 * @access  public
 * 
 */
function fncUpdatePurchaseOrder($aryPurchaseOrder, $objDB){
    $lngLocationCode = fncGetMasterValue("m_company", "strcompanydisplaycode", "lngcompanycode", $aryPurchaseOrder["strLocationCode"] . ":str", '', $objDB);
    $aryQuery[] = "UPDATE m_purchaseorder SET";
    $aryQuery[] = "   dtmexpirationdate = '"    . $aryPurchaseOrder["dtmExpirationDate"] . "'";
    $aryQuery[] = "  ,lngpayconditioncode = "   . $aryPurchaseOrder["lngPayConditionCode"];
    $aryQuery[] = "  ,strpayconditionname = '"  . $aryPurchaseOrder["strPayConditionName"] . "'";
    // $aryQuery[] = "  ,lngdeliveryplacecode = "  . $aryPurchaseOrder["lngLocationCode"];
    $aryQuery[] = "  ,strdeliveryplacename = '" . mb_convert_encoding(urldecode($aryPurchaseOrder["strLocationName"]), "EUC-JP") . "'";
    $aryQuery[] = "  ,lngdeliveryplacecode = "  . $lngLocationCode;
    $aryQuery[] = "  ,strNote = '"              . mb_convert_encoding(urldecode($aryPurchaseOrder["strNote"]), "EUC-JP") . "'";
    $aryQuery[] = "WHERE lngpurchaseorderno = " . $aryPurchaseOrder["lngPurchaseOrderNo"];
    $aryQuery[] = "AND   lngrevisionno = "      . $aryPurchaseOrder["lngRevisionNo"];

    $strQuery = "";
    $strQuery = implode("\n", $aryQuery);

    if ( !$lngResultID = $objDB->execute( $strQuery ) )
    {
        fncOutputError ( 9051, DEF_ERROR, "ȯ������٤ؤι��������˼��Ԥ��ޤ�����", TRUE, "", $objDB );
        return null;
    }
    $objDB->freeResult( $lngResultID );

    return true;
}

/**
 * ȯ������ٹ���
 * 
 * @param   Array   $aryPurchaseOrder   ȯ���ǡ���
 * @param   Object  $objDB              DB���֥�������
 * @access  public
 * 
 */
function fncUpdatePurchaseOrderDetail($aryPurchaseOrder, $objDB){
    for($i = 0; $i < count($aryPurchaseOrder["aryDetail"]); $i++){
        $aryQuery = [];
        $aryQuery[] = "UPDATE t_purchaseorderdetail SET";
        $aryQuery[] = "   lngdeliverymethodcode = "       . $aryPurchaseOrder["aryDetail"][$i]["lngDeliveryMethodCode"];
        $aryQuery[] = "  ,strdeliverymethodname = '"      . $aryPurchaseOrder["aryDetail"][$i]["strDeliveryMethodName"] . "'";
        $aryQuery[] = "  ,lngsortkey = "                  . $aryPurchaseOrder["aryDetail"][$i]["lngSortKey"];
        $aryQuery[] = "WHERE lngpurchaseorderno = "       . $aryPurchaseOrder["lngPurchaseOrderNo"];
        $aryQuery[] = "AND   lngpurchaseorderdetailno = " . $aryPurchaseOrder["aryDetail"][$i]["lngPurchaseOrderDetailNo"];
        $aryQuery[] = "AND   lngrevisionno = "            . $aryPurchaseOrder["lngRevisionNo"];
    
        $strQuery = "";
        $strQuery = implode("\n", $aryQuery);

        if ( !$lngResultID = $objDB->execute( $strQuery ) )
        {
            fncOutputError ( 9051, DEF_ERROR, "ȯ������٤ؤι��������˼��Ԥ��ޤ�����", TRUE, "", $objDB );
            return null;
        }
        $objDB->freeResult( $lngResultID );
    }

    return true;
}

/**
 * ������ȯ���ǡ���HTML�Ѵ�
 * 
 * @param   Array   $aryPurchaseOrder   ȯ���ǡ���
 * @param   String  $strSessionID       ���å����ID
 * @access  public
 * 
 */
function fncCreatePurchaseOrderUpdateHtml($aryPurchaseOrder, $strSessionID){
    $strUrl = "/list/result/po/listoutput.php?strReportKeyCode=" . $aryPurchaseOrder[0]["lngpurchaseorderno"] . "&strSessionID=" . $strSessionID;
    $aryHtml[] = "<p class=\"caption\">ȯ���NO " . $aryPurchaseOrder[0]["strordercode"] . "�ν�������λ���ޤ�����</p>";
    $aryHtml[] = "<table class=\"ordercode\">";
    $aryHtml[] = "  <tr>";
    $aryHtml[] = "    <td class=\"orderbuttontd\" id=\"btnClose\"><img src=\"/img/type01/cmn/querybt/close_blown_off_ja_bt.gif\" alt=\"\" onclick=\"window.open('about:blank','_self').close()\"></td>";
    $aryHtml[] = "    <td class=\"orderbuttontd\"><a href=\"" . $strUrl . "\"><img src=\"/img/type01/cmn/querybt/preview_off_ja_bt.gif\" alt=\"preview\"></a></td>";
    $aryHtml[] = "  </tr>";
    $aryHtml[] = "</table> ";
    $aryHtml[] = "<br>";
    $aryHtml[] = "<table class=\"orderdetail\">";
    $aryHtml[] = "  <tr>";
    $aryHtml[] = "    <th class=\"SegColumn\">���ʥ�����</th>";
    $aryHtml[] = "    <th class=\"SegColumn\">����̾</th>";
    $aryHtml[] = "    <th class=\"SegColumn\">������</th>";
    $aryHtml[] = "    <th class=\"SegColumn\">Ǽ�ʾ��</th>";
    $aryHtml[] = "  </tr>";
    $aryHtml[] = "  <tr>";
    $aryHtml[] = "    <td class=\"Segs\">" . $aryPurchaseOrder[0]["strproductcode"] . "</td>";
    $aryHtml[] = "    <td class=\"Segs\">" . $aryPurchaseOrder[0]["strproductname"] . "</td>";
    $aryHtml[] = "    <td class=\"Segs\">" . sprintf("[%s] %s", $aryPurchaseOrder[0]["strcustomercode"], $aryPurchaseOrder[0]["strcustomername"]) . "</td>";
    $aryHtml[] = "    <td class=\"Segs\">" . sprintf("[%s] %s", $aryPurchaseOrder[0]["strdeliveryplacecode"], $aryPurchaseOrder[0]["strdeliveryplacename"]) . "</td>";
    $aryHtml[] = "  </tr>";
    $aryHtml[] = "</table>";
    $aryHtml[] = "<table class=\"orderdetail\">";
    $aryHtml[] = "  <tr>";
    $aryHtml[] = "    <th class=\"SegColumn\">No.</th>";
    $aryHtml[] = "    <th class=\"SegColumn\">��������</th>";
    $aryHtml[] = "    <th class=\"SegColumn\">������ˡ</th>";
    $aryHtml[] = "    <th class=\"SegColumn\">ñ��</th>";
    $aryHtml[] = "    <th class=\"SegColumn\">����</th>";
    $aryHtml[] = "    <th class=\"SegColumn\">��ȴ���</th>";
    $aryHtml[] = "    <th class=\"SegColumn\">Ǽ��</th>";
    $aryHtml[] = "  </tr>";
    for($i = 0; $i < count($aryPurchaseOrder); $i++){
        $aryHtml[] = "  <tr>";
        $aryHtml[] = "    <td class=\"Segs\">" . $aryPurchaseOrder[$i]["lngsortkey"] . "</td>";
        $aryHtml[] = "    <td class=\"Segs\">" . sprintf("[%s] %s", $aryPurchaseOrder[$i]["lngstockitemcode"], $aryPurchaseOrder[$i]["strstockitemname"]) . "</td>";
        $aryHtml[] = "    <td class=\"Segs\">" . trim($aryPurchaseOrder[$i]["strdeliverymethodname"]) . "</td>";
        $aryHtml[] = "    <td class=\"Segs\">" . sprintf("%s %.4f", $aryPurchaseOrder[$i]["strmonetaryunitsign"], $aryPurchaseOrder[$i]["curproductprice"]) . "</td>";
        $aryHtml[] = "    <td class=\"Segs\">" . sprintf("%d %s", number_format($aryPurchaseOrder[$i]["lngproductquantity"]), $aryPurchaseOrder[$i]["strproductunitname"]) . "</td>";
        $aryHtml[] = "    <td class=\"Segs\">" . sprintf("%s %.4f", $aryPurchaseOrder[$i]["strmonetaryunitsign"], $aryPurchaseOrder[$i]["cursubtotalprice"]) . "</td>";
        $aryHtml[] = "    <td class=\"Segs\">" . $aryPurchaseOrder[$i]["dtmdeliverydate"] . "</td>";
        $aryHtml[] = "  </tr>";
    }
    $aryHtml[] = "</table>";

    $strHtml = "";
    $strHtml = implode("\n", $aryHtml);

    return $strHtml;
}
