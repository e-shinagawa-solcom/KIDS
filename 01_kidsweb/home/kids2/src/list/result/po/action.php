<?
/** 
*	Ģɼ���� PO ������λ����
*
*	@package   KIDS
*	@license   http://www.wiseknot.co.jp/ 
*	@copyright Copyright &copy; 2003, Wiseknot 
*	@author    Kenji Chiba <k-chiba@wiseknot.co.jp> 
*	@access    public
*	@version   1.00
*
*	��������
*	2004.03.05	�����β�Ҥΰ�����դ��� TO �β��� : ���ɲä���褦�˽�������
*	2004.04.19	���Υ������ˤƾ��ʲ������б�����Ƥ���ս��ȯ�����ѹ�
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
require (LIB_DEBUGFILE);

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
$aryCheck["strReportKeyCode"]   = "null:number(0,99999999)";
$strTemplateFile = "p";

$aryResult = fncAllCheck( $aryData, $aryCheck );
fncPutStringCheckError( $aryResult, $objDB );

// ���å�����ǧ
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

// ���³�ǧ
if ( !fncCheckAuthority( DEF_FUNCTION_LO0, $objAuth ) )
{
	fncOutputError ( 9052, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
}


// ���ꥭ�������ɤ�Ģɼ�ǡ��������
$strQuery = fncGetCopyFilePathQuery( DEF_REPORT_ORDER, $aryData["strReportKeyCode"], $aryData["lngReportCode"] );

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
	$strQuery = fncGetListOutputQuery( DEF_REPORT_ORDER, $aryData["strReportKeyCode"], $objDB );


	$objMaster = new clsMaster();
	$objMaster->setMasterTableData( $strQuery, $objDB );

	$aryParts =& $objMaster->aryData[0];

	// �ܺټ���
    $aryQuery[] = "select";
    $aryQuery[] = "pod.lngpurchaseorderno,";
    $aryQuery[] = "pod.lngpurchaseorderdetailno,";
    $aryQuery[] = "pod.lngrevisionno,";
    $aryQuery[] = "pod.lngorderno,";
    $aryQuery[] = "pod.lngorderdetailno,";
    $aryQuery[] = "pod.lngorderrevisionno,";
    $aryQuery[] = "pod.lngstocksubjectcode,";
    $aryQuery[] = "pod.lngstockitemcode,";
    $aryQuery[] = "pod.strstockitemname,";
    $aryQuery[] = "pod.lngdeliverymethodcode,";
    $aryQuery[] = "pod.strdeliverymethodname,";
    $aryQuery[] = "pod.curproductprice,";
    $aryQuery[] = "pod.lngproductquantity,";
    $aryQuery[] = "pod.lngproductunitcode,";
    $aryQuery[] = "pod.strproductunitname,";
    $aryQuery[] = "pod.cursubtotalprice,";
    $aryQuery[] = "pod.dtmdeliverydate,";
    $aryQuery[] = "pod.strnote,";
    $aryQuery[] = "pod.lngsortkey";
    $aryQuery[] = "t_purchaseorderdetail pod";
    $aryQuery[] = "WHERE pod.lngpurchaseorderno = " . $aryData["strReportKeyCode"];
    $aryQuery[] = "ORDER BY pod.lngSortKey";

	$strQuery = join ( "", $aryQuery );
	unset ( $aryQuery );

	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );


	if ( $lngResultNum < 1 )
	{
		fncOutputError ( 9051, DEF_FATAL, "Ģɼ�ܺ٥ǡ�����¸�ߤ��ޤ���Ǥ�����", TRUE, "", $objDB );
	}

	// �ե������̾����
	for ( $i = 0; $i < pg_num_fields ( $lngResultID ); $i++ )
	{
		$aryKeys[] = pg_field_name ( $lngResultID, $i );
	}

	// ���ʥ����ɡ�����̾���Ѹ�����̾����
	$aryResult = $objDB->fetchArray( $lngResultID, 0 );
	$aryParts[$aryKeys[2]] = $aryResult[2];
	$aryParts[$aryKeys[0]] = $aryResult[0];
	$aryParts[$aryKeys[1]] = $aryResult[1];

	// �Կ������ǡ������������������
	for ( $i = 0; $i < $lngResultNum; $i++ )
	{
		$aryResult = $objDB->fetchArray( $lngResultID, $i );
		for ( $j = 3; $j < count ( $aryKeys ); $j++ )
		{
			$aryDetail[$i][$aryKeys[$j] . ( ( $i + 5 ) % 5 )] = $aryResult[$j];
		}
	}
	$objDB->freeResult( $lngResultID );

	// ��׶�۽���(�Ǹ�Υڡ���������ɽ��)���ѿ�����¸
	$curTotalPrice = $aryParts["strmonetaryunitsign"] . " " . $aryParts["curtotalprice"];
	//$aryParts["curtotalprice"] = NULL;
	unset ( $aryParts["curtotalprice"] );

	// �ڡ�������
	$aryParts["lngNowPage"] = 1;
	$aryParts["lngAllPage"] = ceil ( $lngResultNum / 5 );
	//$aryParts["lngAllPage"] = 2;


	// HTML����
	// ---------------------------------------- added by Kazushi Saito 2004/04/22 ��
	$objTemplateHeader = new clsTemplate();
	$objTemplateHeader->getTemplate( "list/result/po_header.tmpl" );
	$strTemplateHeader = $objTemplateHeader->strTemplate;

	$objTemplateFooter = new clsTemplate();
	$objTemplateFooter->getTemplate( "list/result/po_footer.tmpl" );
	$strTemplateFooter = $objTemplateFooter->strTemplate;
	// ---------------------------------------- added by Kazushi Saito 2004/04/22 ��
	
	//echo getArrayTable( $aryDetail[1], "TABLE" );exit;
	$objTemplate = new clsTemplate();
	$objTemplate->getTemplate( "list/result/po.tmpl" );
	$strTemplate = $objTemplate->strTemplate;

	// �ڡ�����ʬ�ƥ�ץ졼�Ȥ򷫤��֤��ɤ߹���
	for ( ; $aryParts["lngNowPage"] < ( $aryParts["lngAllPage"] + 1 ); $aryParts["lngNowPage"]++ )
	{
		$objTemplate->strTemplate = $strTemplate;

		// ɽ�����褦�Ȥ��Ƥ���ڡ������Ǹ�Υڡ����ξ�硢
		// ��׶�ۤ�����(ȯ���������̽���)
		if ( $aryParts["lngNowPage"] == $aryParts["lngAllPage"]  )
		{
			$aryParts["curTotalPrice"] = $curTotalPrice;
			$aryParts["strTotalAmount"] = "Total Amount";
		}

		// �֤�����
		$objTemplate->replace( $aryParts );

		// �ܺٹԤ򣵹�ɽ��(ȯ���������̽���)
		$lngRecordCount = 0;
		for ( $j = ( $aryParts["lngNowPage"] - 1 ) * 5; $j < ( $aryParts["lngNowPage"] * 5 ); $j++ )
		{
			$aryDetail[$j]["record" . $lngRecordCount] = $j + 1;

			// ñ����¸�ߤ���С�������̲�ñ�̤�Ĥ���
			if ( $aryDetail[$j]["curproductprice" . ( ( $j + 5 ) % 5 )] > 0 )
			{
				$aryDetail[$j]["curproductprice" . ( ( $j + 5 ) % 5 )] = $aryParts["strmonetaryunitsign"] . " " . $aryDetail[$j]["curproductprice" . ( ( $j + 5 ) % 5 )];
			}

			// ���פ�¸�ߤ���С�������̲�ñ�̤�Ĥ���
			if ( $aryDetail[$j]["cursubtotalprice" . ( ( $j + 5 ) % 5 )] > 0 )
			{
				$aryDetail[$j]["cursubtotalprice" . ( ( $j + 5 ) % 5 )] = $aryParts["strmonetaryunitsign"] . " " . $aryDetail[$j]["cursubtotalprice" . ( ( $j + 5 ) % 5 )];
			}

			// ���ʿ��̤�¸�ߤ���С����������ñ�̤�Ĥ���
			if ( $aryDetail[$j]["lngproductquantity" . ( ( $j + 5 ) % 5 )] > 0 )
			{
				$aryDetail[$j]["lngproductquantity" . ( ( $j + 5 ) % 5 )] .= "(" . $aryDetail[$j]["strproductunitname" . ( ( $j + 5 ) % 5 )] . ")";
			}

			// �����ȥ�������¸�ߤ���С����������ñ�̤�Ĥ���
			if ( $aryDetail[$j]["lngconversionclasscode" . ( ( $j + 5 ) % 5 )] == 2 )
			{
				$aryDetail[$j]["lngcartonquantity" . ( ( $j + 5 ) % 5 )] = "1(c/t) = " . $aryDetail[$j]["lngcartonquantity" . ( ( $j + 5 ) % 5 )] . "(pcs)";
			}
			else
			{
				unset ( $aryDetail[$j]["lngcartonquantity" . ( ( $j + 5 ) % 5 )] );
			}

			// �ⷿ�ֹ椬¸�ߤ���С������()��Ĥ���
			if ( $aryDetail[$j]["strmoldno" . ( ( $j + 5 ) % 5 )] != "" )
			{
				$aryDetail[$j]["strmoldno" . ( ( $j + 5 ) % 5 )] = "(" . $aryDetail[$j]["strmoldno" . ( ( $j + 5 ) % 5 )] . ")";
			}
			else
			{
				unset ( $aryDetail[$j]["strmoldno" . ( ( $j + 5 ) % 5 )] );
			}

			$objTemplate->replace( $aryDetail[$j] );
			$lngRecordCount++;
		}

		$objTemplate->complete();
		$aryHtml[] = $objTemplate->strTemplate;

	}

	// ---------------------------------------- modifyed by Kazushi Saito 2004/04/22 ��
	$strBodyHtml = join ( "<br style=\"page-break-after:always;\">\n", $aryHtml );
	
	$strHtml = $strTemplateHeader . $strBodyHtml . $strTemplateFooter;
	// ---------------------------------------- modifyed by Kazushi Saito 2004/04/22 ��

	$objDB->transactionBegin();

	// ��������ȯ��
	$lngSequence = fncGetSequence( "t_Report.lngReportCode", $objDB );


	// Ģɼ�ơ��֥��INSERT
	$strQuery = "INSERT INTO t_Report VALUES ( $lngSequence, " . DEF_REPORT_ORDER . ", " . $aryParts["lngorderno"] . ", '', '$lngSequence' )";

//fncDebug("list_action.txt", SRC_ROOT . "list/result/cash/" . $lngSequence . ".tmpl", __FILE__ , __LINE__, "w" );
//fncDebug("list_action.txt", $strQuery, __FILE__ , __LINE__, "a" );

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
