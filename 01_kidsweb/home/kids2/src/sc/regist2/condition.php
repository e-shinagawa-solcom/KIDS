<?php

// ----------------------------------------------------------------------------
/**
*       ȯ������  ��Ͽ
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
*         ����Ͽ����
*         �����顼�����å�
*         ����Ͽ������λ�塢��Ͽ��λ���̤�
*
*       ��������
*
*/
// ----------------------------------------------------------------------------

	//-------------------------------------------------------------------------
	// �饤�֥��ե������ɹ�
	//-------------------------------------------------------------------------
	include('conf.inc');
	require (LIB_FILE);
	require (SRC_ROOT."sc/cmn/lib_scr.php");
	
	//-------------------------------------------------------------------------
	// DB�����ץ�
	//-------------------------------------------------------------------------
	$objDB		= new clsDB();
	$objDB->open("", "", "", "");

	// --------------------------
	//  �ե�������������
	// --------------------------
	// �ܵҥ�����
	$aryData["strDefaultCompanyDisplayCode"] = $_POST["strcompanydisplaycode"];

	// ����ʬ�����ɤν���͡����̤ˤ���¸���Ƥ���
	$lngDefaultSalesClassCode = $_POST["lngsalesclasscode"];
	$aryData["lngDefaultSalesClassCode"] = $lngDefaultSalesClassCode;

	// ����ʬ�ץ������ʿƲ��̤ν������ٰ������ꥢ��1���ܤ�����ʬ�����ɤ����������
	$optSalesClass .= fncGetPulldown("m_salesclass", "lngsalesclasscode","strsalesclassname", $lngDefaultSalesClassCode, "", $objDB);
	$aryData["optSalesClass"] = $optSalesClass;

	// --------------------------
	//  ����ɽ��
	// --------------------------
	// Ǽ�ʽ����ٸ���������ϲ��̤�ɽ��
	echo fncGetReplacedHtmlWithBase("base_mold_noframes.html", "sc/regist2/condition.tmpl", $aryData ,$objAuth );

	// DB����
	$objDB->close();
	
	// ������λ
	return true;

?>