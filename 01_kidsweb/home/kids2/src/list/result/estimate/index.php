<?
/** 
*	Ģɼ���� ���Ѹ����׻� ������̲���
*
*	@package   KIDS
*	@copyright Copyright &copy; 2004, AntsBizShare 
*	@author    Kenji Chiba
*	@access    public
*	@version   1.00
*
*/
// ������̲���( * �ϻ���Ģɼ�Υե�����̾ )
// *.php -> strSessionID       -> index.php

// �������̤�
// index.php -> strSessionID       -> index.php
// index.php -> lngReportCode      -> index.php

// �����ɤ߹���
include_once('conf.inc');

// �饤�֥���ɤ߹���
require (LIB_FILE);
require (SRC_ROOT . "list/cmn/lib_lo.php");

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

// ���������ܼ���
if ( $lngArrayLength = count ( $aryData["SearchColumn"] ) )
{
	$aryColumn = $aryData["SearchColumn"];
	for ( $i = 0; $i < $lngArrayLength; $i++ )
	{
		$aryData[$aryColumn[$i]] = 1;
	}
	unset ( $aryData["SearchColumn"] );
	unset ( $aryColumn );
}


// ʸ��������å�
$aryCheck["strSessionID"]       = "null:numenglish(32,32)";
$aryResult = fncAllCheck( $aryData, $aryCheck );
fncPutStringCheckError( $aryResult, $objDB );

// ���å�����ǧ
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

// ���³�ǧ
if ( !fncCheckAuthority( DEF_FUNCTION_LO0, $objAuth ) )
{
	fncOutputError ( 9052, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
}


// ���Ѹ���Ģɼ����
// ���ԡ��ե������������������
$strCopyQuery = "SELECT strReportKeyCode, lngReportCode FROM t_Report WHERE lngReportClassCode = " . DEF_REPORT_ESTIMATE;

// ���Ѹ������������������
	// SELECT
	$aryQuery[] = "SELECT";

	$aryQuerySelect[] = " e.lngestimatestatuscode";
	$aryQuerySelect[] = " e.lngrevisionno";

	$aryQuerySelect[] = " e.lngEstimateNo AS strReportKeyCode";
	$aryQuerySelect[] = " e.strProductCode";
	$aryQuerySelect[] = " e.lngInputUserCode";
	$aryQuerySelect[] = " p.strProductName";
	$aryQuerySelect[] = " g.strGroupDisplayCode AS strInchargeGroupDisplayCode";
	$aryQuerySelect[] = " g.strGroupDisplayName AS strInchargeGroupDisplayName";
	$aryQuerySelect[] = " u1.strUserDisplayCode AS strInchargeUserDisplayCode";
	$aryQuerySelect[] = " u1.strUserDisplayName AS strInchargeUserDisplayName";
	$aryQuerySelect[] = " u2.strUserDisplayCode AS strInputUserDisplayCode";
	$aryQuerySelect[] = " u2.strUserDisplayName AS strInputUserDisplayName";

	$aryQuery[] = join ( ", ", $aryQuerySelect );
	unset ( $aryQuerySelect );

	// FROM
	$aryQuery[] = "FROM m_Estimate e";
	$aryQuery[] = " INNER JOIN m_Product p ON p.strProductCode       = e.strProductCode";
	$aryQuery[] = "  AND p.bytInvalidFlag = FALSE";
	$aryQuery[] = " LEFT OUTER JOIN m_Group g   ON p.lngInChargeGroupCode = g.lngGroupCode";
	$aryQuery[] = " LEFT OUTER JOIN m_User u1   ON p.lngInChargeUserCode  = u1.lngUserCode";
//	$aryQuery[] = "  AND u1.bytInvalidFlag = FALSE";
	$aryQuery[] = " INNER JOIN m_User u2   ON e.lngInputUserCode     = u2.lngUserCode";
//	$aryQuery[] = "  AND u2.bytInvalidFlag = FALSE";
	//$aryQuery[] = " LEFT OUTER JOIN m_Workflow w  ON w.strWorkflowKeyCode = e.lngEstimateNo";
	//$aryQuery[] = "  AND w.bytInvalidFlag = FALSE";
	//$aryQuery[] = " LEFT OUTER JOIN t_Workflow tw ON w.lngWorkflowCode = tw.lngWorkflowCode";
	//$aryQuery[] = "  AND lngWorkflowSubCode =";
	//$aryQuery[] = "   ( SELECT MAX(tw2.lngWorkflowSubCode) FROM t_Workflow tw2 WHERE tw.lngWorkflowCode = tw2.lngWorkflowCode )";



/////////////////////////////////////////////////////////////////
// �������
/////////////////////////////////////////////////////////////////
// ��������
if ( $aryData["dtmInsertDateConditions"] )
{
	if ( $aryData["dtmInsertDateFrom"] )
	{
		$aryQueryWhere[] = "date_trunc('day', e.dtmInsertDate ) >= '" . $aryData["dtmInsertDateFrom"] . "'";
	}
	if ( $aryData["dtmInsertDateTO"] )
	{
		$aryQueryWhere[] = "date_trunc('day', e.dtmInsertDate ) <= '" . $aryData["dtmInsertDateTo"] . "'";
	}
}

// ���ʥ�����
if ( $aryData["strProductCodeConditions"] )
{
// 2004.10.09 suzukaze update start
	if ( $aryData["strProductCodeFrom"] )
	{
		$aryQueryWhere[] = "e.strProductCode >= '" . $aryData["strProductCodeFrom"] . "'";
	}
	if ( $aryData["strProductCodeTo"] )
	{
		$aryQueryWhere[] = "e.strProductCode <= '" . $aryData["strProductCodeTo"] . "'";
	}
// 2004.10.09 suzukaze update end
}

// ���ϼ�
if ( $aryData["lngInputUserCodeConditions"] && $aryData["strInputUserDisplayCode"] )
{
	$aryQueryWhere[] = "u2.strUserDisplayCode = '" . $aryData["strInputUserDisplayCode"] . "'";
}

// ����
if ( $aryData["lngInChargeGroupCodeConditions"] && $aryData["strInChargeGroupDisplayCode"] )
{
	$aryQueryWhere[] = "g.strGroupDisplayCode = '" . $aryData["strInChargeGroupDisplayCode"] . "'";
}
// ô����
if ( $aryData["lngInChargeUserCodeConditions"] && $aryData["strInChargeUserDisplayCode"] )
{
	$aryQueryWhere[] = "u1.strUserDisplayCode = '" . $aryData["strInChargeUserDisplayCode"] . "'";
}


// WHERE
// $aryQueryWhere[] = "e.lngEstimateStatusCode = " . DEF_ESTIMATE_APPROVE;

$aryQueryWhere[] = " e.lngRevisionNo = ( SELECT MAX ( e2.lngRevisionNo ) FROM m_Estimate e2 WHERE e.lngEstimateNo = e2.lngEstimateNo )";

$aryQueryWhere[] = " 0 <= ( SELECT MIN ( e3.lngRevisionNo ) FROM m_Estimate e3 WHERE e.lngEstimateNo = e3.lngEstimateNo )";
//$aryQueryWhere[] = "w.lngFunctionCode = " . DEF_FUNCTION_E0;
//$aryQueryWhere[] = "tw.lngWorkflowStatusCode = " . DEF_STATUS_APPROVE;





// A:��ȯ��׾��֤���礭�����֤�ȯ��ǡ���
// B:��ȯ��׾��֤Υǡ���
// C:����ե���¸�ߤ��ʤ�(¨ǧ�ڰƷ�)
// D:�־�ǧ�׾��֤ˤ���Ʒ�
// A OR ( B AND ( C OR D ) )
$aryQuery[] = " AND (";

// A:�־�ǧ�׾��֤���礭�����֤�ȯ��ǡ���
$aryQuery[] = "  e.lngestimatestatuscode > " . 0;

$aryQuery[] = "  OR";
$aryQuery[] = "  (";

// B:�־�ǧ�׾��֤Υǡ���
$aryQuery[] = "    e.lngestimatestatuscode = " . 2;
$aryQuery[] = "     AND";
$aryQuery[] = "    (";

// C:����ե���¸�ߤ��ʤ�(¨ǧ�ڰƷ�)
$aryQuery[] = "      0 = ";
$aryQuery[] = "      (";
$aryQuery[] = "        SELECT COUNT ( mw.lngWorkflowCode ) ";
$aryQuery[] = "        FROM m_Workflow mw ";
$aryQuery[] = "        WHERE to_number ( mw.strWorkflowKeyCode, '9999999') = e.lngestimateno";
$aryQuery[] = "         AND mw.lngFunctionCode = " . DEF_FUNCTION_E1;
$aryQuery[] = "      )";

// D:�־�ǧ�׾��֤ˤ���Ʒ�
$aryQuery[] = "      OR " . DEF_STATUS_APPROVE . " = ";
$aryQuery[] = "      (";
$aryQuery[] = "        SELECT tw.lngWorkflowStatusCode";
$aryQuery[] = "        FROM m_Workflow mw2, t_Workflow tw";
$aryQuery[] = "        WHERE to_number ( mw2.strWorkflowKeyCode, '9999999') = e.lngestimateno";
$aryQuery[] = "         AND mw2.lngFunctionCode = " . DEF_FUNCTION_E1;
$aryQuery[] = "         AND tw.lngWorkflowSubCode =";
$aryQuery[] = "        (";
$aryQuery[] = "          SELECT MAX ( tw2.lngWorkflowSubCode ) FROM t_Workflow tw2 WHERE tw.lngWorkflowCode = tw2.lngWorkflowCode";
$aryQuery[] = "        )";
$aryQuery[] = "         AND mw2.lngWorkflowCode = tw.lngWorkflowCode";
$aryQuery[] = "      )";
$aryQuery[] = "    )";
$aryQuery[] = "  )";
$aryQuery[] = ")";


$aryQuery[] = "AND e.lngestimatestatuscode != " . DEF_ESTIMATE_DENIAL;



$aryQuery[] = " WHERE " . join ( " AND ", $aryQueryWhere );
unset ( $aryQueryWhere );
$aryQuery[] = "ORDER BY p.strProductCode DESC";

// �ʥ�С��򥭡��Ȥ���Ϣ�������Ģɼ�����ɤ����
list ( $lngResultID, $lngResultNum ) = fncQuery( $strCopyQuery, $objDB );

for ( $i = 0; $i < $lngResultNum; $i++ )
{
	$objResult = $objDB->fetchObject( $lngResultID, $i );
	$aryReportCode[$objResult->strreportkeycode] = $objResult->lngreportcode;
}

if ( $lngResultNum > 0 )
{
	$objDB->freeResult( $lngResultID );
}



$strQuery = join ( "\n", $aryQuery );

//require( LIB_DEBUGFILE );
//fncDebug( 'lib_list_estimate.txt', $strQuery, __FILE__, __LINE__);

// Ģɼ�ǡ�������������¹ԡ��ơ��֥�����
list ( $lngResultID, $lngResultNum ) = fncQuery( join ( " ", $aryQuery ), $objDB );
unset ( $aryQuery );

for ( $i = 0; $i < $lngResultNum; $i++ )
{
	$objResult = $objDB->fetchObject( $lngResultID, $i );

	$aryParts["strResult"] .= "<tr class=\"Segs\">\n";

	$aryParts["strResult"] .= "<td>" . $objResult->strproductcode . "</td>\n";
	$aryParts["strResult"] .= "<td>" . $objResult->strproductname . "</td>\n";
	$aryParts["strResult"] .= "<td>" . $objResult->strinputuserdisplaycode . ":" . $objResult->strinputuserdisplayname . "</td>\n";
	$aryParts["strResult"] .= "<td>" . $objResult->strinchargegroupdisplaycode . ":" . $objResult->strinchargegroupdisplayname . "</td>\n";
	$aryParts["strResult"] .= "<td>" . $objResult->strinchargeuserdisplaycode . ":" . $objResult->strinchargeuserdisplayname . "</td>\n";

	$aryParts["strResult"] .= "<td align=center>";

	// ���ԡ��ե�����ѥ���¸�ߤ��Ƥ����硢���ԡ�Ģɼ���ϥܥ���ɽ��
	if ( $aryReportCode[$objResult->strreportkeycode] != NULL )
	{
		// ���ԡ�Ģɼ���ϥܥ���ɽ��
		$aryParts["strResult"] .= "<a href=\"#\"><img onclick=\"fncListOutput( '/list/result/frameset.php?strSessionID=" . $aryData["strSessionID"] . "&lngReportClassCode=" . DEF_REPORT_ESTIMATE . "&strReportKeyCode=" . $objResult->strreportkeycode . "&lngReportCode=" . $aryReportCode[$objResult->strreportkeycode] . "' );return false;\" onmouseover=\"fncCopyPreviewButton( 'on' , this );\" onmouseout=\"fncCopyPreviewButton( 'off' , this );fncAlphaOff( this );\" onmousedown=\"fncAlphaOn( this );\" onmouseup=\"fncAlphaOff( this );\" src=\"/img/type01/list/copybig_off_bt.gif\" width=\"72\" height=\"20\" border=\"0\" alt=\"COPY PREVIEW\"></a>";
	}

	$aryParts["strResult"] .= "</td>\n<td align=center>";

	// ���ԡ��ե�����ѥ���¸�ߤ��ʤ� �ޤ��� ���ԡ�������¤������硢
	// Ģɼ���ϥܥ���ɽ��
	if ( $aryReportCode[$objResult->strreportkeycode] == NULL || fncCheckAuthority( DEF_FUNCTION_LO4, $objAuth ) )
	{
		// Ģɼ���ϥܥ���ɽ��
		$aryParts["strResult"] .= "<a href=\"#\"><img onclick=\"fncListOutput( '/list/result/frameset.php?strSessionID=" . $aryData["strSessionID"] . "&lngReportClassCode=" . DEF_REPORT_ESTIMATE . "&strReportKeyCode=" . $objResult->strreportkeycode . "' );return false;\" onmouseover=\"fncPreviewButton( 'on' , this );\" onmouseout=\"fncPreviewButton( 'off' , this );fncAlphaOff( this );\" onmousedown=\"fncAlphaOn( this );\" onmouseup=\"fncAlphaOff( this );\" src=\"/img/type01/cmn/querybt/preview_off_bt.gif\" width=\"72\" height=\"20\" border=\"0\" alt=\"PREVIEW\"></a>";
	}

	$aryParts["strResult"] .= "</td></tr>\n";

	unset ( $strCopyCheckboxObject );
}



$aryParts["strColumn"] = "
					<td id=\"Column0\" nowrap>���ʥ�����</td>
					<td id=\"Column1\" nowrap>����̾��</td>
					<td id=\"Column2\" nowrap>���ϼ�</td>
					<td id=\"Column3\" nowrap>����</td>
					<td id=\"Column4\" nowrap>ô����</td>
					<td id=\"Column5\" nowrap>COPY �ץ�ӥ塼</td>
					<td id=\"Column6\" nowrap>�ץ�ӥ塼</td>
";

$aryParts["strListType"] = "estimate";
$aryParts["HIDDEN"] = getArrayTable( $aryData, "HIDDEN" );


$objDB->close();

$aryParts["lngLanguageCode"] = $_COOKIE["lngLanguageCode"];

// HTML����
$objTemplate = new clsTemplate();
$objTemplate->getTemplate( "list/result/parts.tmpl" );
$objTemplate->replace( $aryParts );
$objTemplate->replace( $aryData );
$objTemplate->complete();
echo $objTemplate->strTemplate;
?>
