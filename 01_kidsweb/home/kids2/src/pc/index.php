<?php

// ----------------------------------------------------------------------------
/**
*       ��������  ��˥塼����
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
*         ����˥塼���̤�ɽ��
*
*       ��������
*
*/
// ----------------------------------------------------------------------------



	// �ɤ߹���
	include('conf.inc');
	require (LIB_FILE);
	
	
	$objDB		= new clsDB();
	$objAuth	= new clsAuth();
	$objDB->open("", "", "", "");

	$aryData["strSessionID"] = $_POST["strSessionID"];


	// ʸ��������å�
	$aryCheck["strSessionID"]          = "null:numenglish(32,32)";
	$aryResult = fncAllCheck( $aryData, $aryCheck );
	fncPutStringCheckError( $aryResult, $objDB );
	
	// ���å�����ǧ
	$objAuth = fncIsSession( $_POST["strSessionID"], $objAuth, $objDB );

	// 700 ��������
	if ( !fncCheckAuthority( DEF_FUNCTION_PC0, $objAuth ) )
	{
	        fncOutputError ( 9052, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
	}

	// 701 ���������� ������Ͽ��
	if ( fncCheckAuthority( DEF_FUNCTION_PC1, $objAuth ) )
	{
		$aryData["strRegistURL"]   = "regist/index.php?strSessionID=" . $aryData["strSessionID"];
	}
	
	// 702 ���������� ����������
	if ( fncCheckAuthority( DEF_FUNCTION_PC2, $objAuth ) )
	{
		$aryData["strSearchURL"]   = "search/index.php?strSessionID=" . $aryData["strSessionID"];
	}

	//echo "button : ".$aryData["strRegist"]."<br>";
	//echo "button : ".$aryData["strSearch"]."<br>";
	// �إ���б�
	$aryData["lngFunctionCode"] = DEF_FUNCTION_PC0;



	// �桼���������ɼ���
	$lngUserCode = $objAuth->UserCode;

	// ���¥��롼�ץ�����(�桼�����ʲ�)�����å�
	$blnAG = fncCheckUserAuthorityGroupCode( $lngUserCode, $aryData["strSessionID"], $objDB );

	// �֥桼�����װʲ��ξ��
	if( $blnAG )
	{
		// ��ǧ�롼��¸�ߥ����å�
		$blnWF = fncCheckWorkFlowRoot( $lngUserCode, $aryData["strSessionID"], $objDB );

		// ��ǧ�롼�Ȥ�¸�ߤ��ʤ����
		if( !$blnWF )
		{
			$aryData["registview"] = 'hidden';
		}
		else
		{
			$aryData["registview"] = 'visible';
		}
		// 701 ������Ͽ
		if( !fncCheckAuthority( DEF_FUNCTION_PC1, $objAuth ) )
		{
			$aryData["registview"] = 'hidden';
		}
		else
		{
			$aryData["registview"] = 'visible';
		}



	}



	echo fncGetReplacedHtml( "pc/parts.tmpl", $aryData ,$objAuth );
//	echo $_COOKIE["lngLanguageCode"];

	$objDB->close();
	return true;
?>