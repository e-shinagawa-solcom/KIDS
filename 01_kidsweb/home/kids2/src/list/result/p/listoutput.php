<?
/** 
*	Ģɼ���� ���ʴ��� �����ץ�ӥ塼����
*
*	@package   KIDS
*	@license   http://www.wiseknot.co.jp/ 
*	@copyright Copyright &copy; 2003, Wiseknot 
*	@author    Kenji Chiba <k-chiba@wiseknot.co.jp> 
*	@access    public
*	@version   1.00
*
*/
// Ģɼ���� �����ץ�ӥ塼����
// frameset.php -> strSessionID     -> listoutput.php
// frameset.php -> lngReportCode    -> listoutput.php
// frameset.php -> lngReportKeyCode -> listoutput.php

// �����ɤ߹���
include_once('conf.inc');

// �饤�֥���ɤ߹���
require (LIB_FILE);
require (SRC_ROOT . "list/cmn/lib_lo.php");
require (SRC_ROOT . "m/cmn/lib_m.php");

$objDB   = new clsDB();
$objAuth = new clsAuth();
$objDB->open( "", "", "", "" );

//////////////////////////////////////////////////////////////////////////
// POST(����GET)�ǡ�������
//////////////////////////////////////////////////////////////////////////
if ( $_POST )
{
	$aryData = $_POST;
}
elseif ( $_GET )
{
	$aryData = $_GET;
}


// ʸ��������å�
$aryCheck["strSessionID"]       = "null:numenglish(32,32)";
$aryCheck["strReportKeyCode"]   = "null";
$aryCheck["lngReportCode"]      = "ascii(1,7)";
$aryCheck["strReportKeyCode"]   = "null:number(0,9999999)";


$aryResult = fncAllCheck( $aryData, $aryCheck );
fncPutStringCheckError( $aryResult, $objDB );

// ���å�����ǧ
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

// ���³�ǧ
if ( !fncCheckAuthority( DEF_FUNCTION_LO0, $objAuth ) || !fncCheckAuthority( DEF_FUNCTION_P0, $objAuth ) )
{
	fncOutputError ( 9052, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
}

// Ģɼ���ϥ��ԡ��ե�����ѥ���������������
$strQuery = fncGetCopyFilePathQuery( DEF_REPORT_PRODUCT, $aryData["strReportKeyCode"], $aryData["lngReportCode"] );
list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
if ( $lngResultNum > 0 )
{
	$objResult = $objDB->fetchObject( $lngResultID, 0 );
	$strReportPathName = $objResult->strreportpathname;
	unset ( $objResult );
}

///////////////////////////////////////////////////////////////////////////
// Ģɼ�����ɤ����ξ�硢�ե�����ǡ��������
///////////////////////////////////////////////////////////////////////////
if ( $aryData["lngReportCode"] )
{
	if ( !$lngResultNum )
	{
		fncOutputError ( 9056, DEF_FATAL, "Ģɼ���ԡ�������ޤ���", TRUE, "", $objDB );
	}

	if ( !$strHtml =  file_get_contents ( SRC_ROOT . "list/result/cash/" . $strReportPathName . ".tmpl" ) )
	{
		fncOutputError ( 9059, DEF_FATAL, "Ģɼ�ǡ����ե����뤬�����ޤ���Ǥ�����", TRUE, "", $objDB );
	}
	$objDB->freeResult( $lngResultID );
}

///////////////////////////////////////////////////////////////////////////
// �ƥ�ץ졼�Ȥ��֤������ǡ�������
///////////////////////////////////////////////////////////////////////////
else
{
	// �ǡ�������������
	$strQuery = fncGetListOutputQuery( DEF_REPORT_PRODUCT, $aryData["strReportKeyCode"], $objDB );

	$objMaster = new clsMaster();
	$objMaster->setMasterTableData( $strQuery, $objDB );

	$aryParts =& $objMaster->aryData[0];


	/////////////////////////////////////////////////////////////////
	// �ü����
	/////////////////////////////////////////////////////////////////
	// �ܵ�ô���ԥ����ɤ��ʤ��ä���硢�ܵ�ô����̾��ɽ���������
	if ( !$aryParts["lngcustomerusercode"] )
	{
		$aryParts["strcustomeruserdisplayname"] =& $aryParts["strcustomerusername"];
	}
	// ��Ȣ(��)������¸�ߤ����硢������"pcs"��Ĥ���
	if ( $aryParts["lngboxquantity"] > 0 )
	{
		$aryParts["lngboxquantity"] .= "pcs";
	}
	else
	{
		unset ( $aryParts["lngboxquantity"] );
	}
	// �����ȥ�������¸�ߤ����硢������"pcs"��Ĥ���
	if ( $aryParts["lngcartonquantity"] > 0 )
	{
		$aryParts["lngcartonquantity"] .= "pcs";
	}
	else
	{
		unset ( $aryParts["lngcartonquantity"] );
	}
	// ���ʹ�����¸�ߤ����硢�������異�å���֥�פ�ɽ��
	if ( $aryParts["strproductcomposition"] > 0 )
	{
		$aryParts["strproductcomposition"] = "��" . $aryParts["strproductcomposition"] . "�異�å���֥�";
	}
	else
	{
		unset ( $aryParts["strproductcomposition"] );
	}





	//-------------------------------------------------------------------------
	// �� ��̾���᡼������
	//-------------------------------------------------------------------------
	$strFullPath        = SRC_ROOT . "img/signature";
	$bytCheck           = false;

	$strImagePath       = '/img/signature/'; // ���᡼���ǥ��쥯�ȥ�ѥ�
	$strDefaultImage    = 'default.gif';     // �ǥե���ȥ��᡼��
	$strCreateUserImage = '';                // �����ԥ��᡼��
	$strAssentUserImage = '';                // ��ǧ�ԥ��᡼��


	// �����ԤΥ桼���������ɤ����	
	$lngusercode = $aryParts["lnginputusercode"];

	// �桼���������ɤ����
	if(!$lngusercode)
	{
		$bytCheck    = false;
		// ��̾�ե������¸��̵ͭ��ǧ
		$bytCheck = fncSignatureCheckFile( $strFullPath, $lngusercode );
		if( $bytCheck )
		{
			$strCreateUserImage = $strImagePath . $lngusercode . ".gif";
		}
		else
		{
			$strCreateUserImage = $strImagePath . $strDefaultImage;
		}
	}
	// �桼������¸�ߤ��ʤ����
	else
	{
		$strCreateUserImage = $strImagePath . $strDefaultImage;
	}



	// ��ǧ�ԤΥ桼���������ɤ����
	$lngusercode = $aryParts["lnginchargeusercode"];

	// �桼���������ɤ����
	if(!$lngusercode)
	{
		$bytCheck    = false;

		// ��̾�ե������¸��̵ͭ��ǧ
		$bytCheck = fncSignatureCheckFile( $strFullPath, $lngusercode );

		if( $bytCheck )
		{
			$strAssentUserImage = $strImagePath . $lngusercode . ".gif";
		}
		else
		{
			$strAssentUserImage = $strImagePath . $strDefaultImage;
		}
	}
	// �桼������¸�ߤ��ʤ����
	else
	{
		$strAssentUserImage = $strImagePath . $strDefaultImage;
	}



	// ������(���ϼ�)��̾���᡼������
	$aryParts["sigCreateImage"] = $strCreateUserImage;

	// ��ǧ�Խ�̾���᡼������
	$aryParts["sigAssentImage"] = $strAssentUserImage;


	// �ե����ޥåȥ���������
	$aryParts["strProductFormatCode"] = DEF_P_FORMAT_CODE;
	//-------------------------------------------------------------------------





	$objDB->close();

	$objTemplate = new clsTemplate();
	$objTemplate->getTemplate( "list/result/p.tmpl" );

	// �֤�����
	$objTemplate->replace( $aryParts );

	$objTemplate->complete();
	$strHtml .= $objTemplate->strTemplate;
}


echo $strHtml;

?>
