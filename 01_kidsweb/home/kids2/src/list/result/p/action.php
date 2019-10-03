<?
/** 
*	Ģɼ���� ���ʴ��� ������λ����
*
*	@package   KIDS
*	@license   http://www.wiseknot.co.jp/ 
*	@copyright Copyright &copy; 2003, Wiseknot 
*	@author    Kenji Chiba <k-chiba@wiseknot.co.jp> 
*	@access    public
*	@version   1.00
*
*/
// �����ץ�ӥ塼����( * �ϻ���Ģɼ�Υե�����̾ )
// listoutput.php -> strSessionID       -> action.php
// listoutput.php -> strReportKeyCode   -> action.php
// listoutput.php -> lngReportCode      -> action.php

// �����ɤ߹���
include_once('conf.inc');

// �饤�֥���ɤ߹���
require (LIB_FILE);
require (SRC_ROOT . "/list/cmn/lib_lo.php");
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
$aryCheck["strReportKeyCode"]   = "null:number(0,9999)";


$aryResult = fncAllCheck( $aryData, $aryCheck );
fncPutStringCheckError( $aryResult, $objDB );

// ���å�����ǧ
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

// ���³�ǧ
if ( !fncCheckAuthority( DEF_FUNCTION_LO0, $objAuth ) )
{
	fncOutputError ( 9018, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
}


// ���ꥭ�������ɤ�Ģɼ�ǡ��������
$strQuery = fncGetCopyFilePathQuery( DEF_REPORT_PRODUCT, $aryData["strReportKeyCode"], $aryData["lngReportCode"] );

list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

if ( $lngResultNum === 1 )
{
	$objResult = $objDB->fetchObject( $lngResultID, 0 );
	$strListOutputPath = $objResult->strreportpathname;
	unset ( $objResult );
	$objDB->freeResult( $lngResultID );
	//echo "���ԡ��ե�����ͭ�ꡣ";
}

// Ģɼ��¸�ߤ��ʤ���硢���ԡ�Ģɼ�ե��������������¸
elseif ( $lngResultNum === 0 )
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
	$strDefaultImage    = 'default.gif';    // �ǥե���ȥ��᡼��
	$strCreateUserImage = '';               // �����ԥ��᡼��
	$strAssentUserImage = '';               // ��ǧ�ԥ��᡼��


	// �����ԤΥ桼���������ɤ����
	$aryQuery   = array();
	$aryQuery[] = "SELECT";
	$aryQuery[] = " mp.lnginputusercode as lngusercode";
	$aryQuery[] = "FROM";
	$aryQuery[] = " m_product mp";
	$aryQuery[] = "WHERE";
	$aryQuery[] = " mp.strproductcode = '" . $aryData["strReportKeyCode"] . "'";

	$strQuery = "";
	$strQuery = implode( "\n", $aryQuery );

	list( $lngCheckResultID, $lngCheckResultNum ) = fncQuery( $strQuery, $objDB );

	// �桼���������ɤ����
	if( $lngCheckResultNum == 1 )
	{
		$bytCheck    = false;
		$objResult   = $objDB->fetchObject( $lngCheckResultID, 0 );
		$lngusercode = $objResult->lngusercode;

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





	// HTML����
	//echo getArrayTable( $aryDetail[1], "TABLE" );exit;

	$objTemplate = new clsTemplate();
	$objTemplate->getTemplate( "list/result/p.tmpl" );

	// �֤�����
	$objTemplate->replace( $aryParts );

	$objTemplate->complete();
	$strHtml .= $objTemplate->strTemplate;

	$objDB->transactionBegin();

	// ��������ȯ��
	$lngSequence = fncGetSequence( "t_Report.lngReportCode", $objDB );

	// Ģɼ�ơ��֥��INSERT
	$strQuery = "INSERT INTO t_Report VALUES ( $lngSequence, " . DEF_REPORT_PRODUCT . ", " . $aryParts["lnggoodsplancode"] . ", '', '$lngSequence' )";

	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	// Ģɼ�ե����륪���ץ�
	if ( !$fp = fopen ( SRC_ROOT . "list/result/cash/" . $lngSequence . ".tmpl", "w" ) )
	{
		list ( $lngResultID, $lngResultNum ) = fncQuery( "ROLLBACK", $objDB );
		fncOutputError ( 9059, DEF_FATAL, "Ģɼ�ե�����Υ����ץ�˼��Ԥ��ޤ�����", TRUE, "", $objDB );
	}

	// Ģɼ�ե�����ؤν񤭹���
	if ( !fwrite ( $fp, $strHtml ) )
	{
		list ( $lngResultID, $lngResultNum ) = fncQuery( "ROLLBACK", $objDB );
		fncOutputError ( 9059, DEF_FATAL, "Ģɼ�ե�����ν񤭹��ߤ˼��Ԥ��ޤ�����", TRUE, "", $objDB );
	}


	$objDB->transactionCommit();
	//echo "���ԡ��ե��������";
}
//echo "<script language=javascript>window.form1.submit();window.returnValue=true;window.close();</script>";
echo "<script language=javascript>parent.window.close();</script>";


$objDB->close();



return TRUE;
?>
