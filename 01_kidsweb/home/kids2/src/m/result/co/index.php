<?
/** 
*	�ޥ������� ��ҥޥ��� �ޥ������ơ��֥��̰�������
*
*	@package   KIDS
*	@license   http://www.wiseknot.co.jp/ 
*	@copyright Copyright &copy; 2003, Wiseknot 
*	@author    Kenji Chiba <k-chiba@wiseknot.co.jp> 
*	@access    public
*	@version   1.00
*
*/
// index.php -> strSessionID          -> index.php
// index.php -> lngAttributeCode      -> index.php
// index.php -> strCompanyDisplayName -> index.php
//
// ��Ͽ����
// index.php -> strSessionID          -> edit.php
// index.php -> lngActionCode         -> edit.php
// index.php -> lngAttributeCode      -> edit.php
// index.php -> strCompanyDisplayName -> edit.php
//
// ��������
// index.php -> strSessionID          -> confirm.php
// index.php -> lngActionCode         -> confirm.php
// index.php -> lngAttributeCode      -> confirm.php
// index.php -> strCompanyDisplayName -> confirm.php
// index.php -> lngcompanycode        -> confirm.php

// �����ɤ߹���
include_once('conf.inc');

// �饤�֥���ɤ߹���
require (LIB_FILE);
require (SRC_ROOT . "m/cmn/lib_m.php");



$objDB   = new clsDB();
$objAuth = new clsAuth();
$objDB->open( "", "", "", "" );

$aryData = $_POST;
$aryData["lngLanguageCode"] = $_COOKIE["lngLanguageCode"];
if ( $aryData["lngLanguageCode"] == "" )
{
	$aryData["lngLanguageCode"] = 0;
}


// ʸ��������å�
$aryCheck["strSessionID"]          = "null:numenglish(32,32)";
$aryCheck["lngAttributeCode"]      = "number(0,2147483647)";
$aryCheck["strCompanyDisplayName"] = "length(1,100)";

$aryResult = fncAllCheck( $aryData, $aryCheck );
fncPutStringCheckError( $aryResult, $objDB );

// ���å�����ǧ
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

// ���³�ǧ
if ( !fncCheckAuthority( DEF_FUNCTION_M0, $objAuth ) )
{
	fncOutputError ( 9052, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
}

// �ޥ��������֥�����������
$objMaster = new clsMaster();
$objMaster->strTableName = "m_Company";

// ��������������
$strQuery = "SELECT DISTINCT ON ( com.lngCompanyCode ) * FROM m_Company com, m_AttributeRelation ar, m_Country con, m_Organization o, m_ClosedDay clo";

// °�������ɾ������
if ( $aryData["lngAttributeCode"] != "" )
{
	$aryWhereString[] = " ar.lngAttributeCode = " . $aryData["lngAttributeCode"];
}

// ɽ�����̾�������
if ( $aryData["strCompanyDisplayName"] )
{
	$aryWhereString[] = " com.strCompanyDisplayName LIKE '%" . $aryData["strCompanyDisplayName"] . "%'";
}

// °���ơ��֥�Ȥη��
$aryWhereString[] = " com.lngCompanyCode = ar.lngCompanyCode";
$aryWhereString[] = " com.lngCountryCode = con.lngCountryCode";
$aryWhereString[] = " com.lngOrganizationCode = o.lngOrganizationCode";
$aryWhereString[] = " com.lngClosedDayCode = clo.lngClosedDayCode";

// ���ʬ����������������ɲ�
$strWhereString = join ( " AND", $aryWhereString );
$strQuery .= " WHERE " . $strWhereString;

// �ǡ����μ����ȥ��֥������ȤؤΥ��å�
$lngResultNum = $objMaster->setMasterTableData( $strQuery, $objDB );


if ( $lngResultNum )
{
	///////////////////////////////////////////////////////////////////
	// �ơ��֥�����
	///////////////////////////////////////////////////////////////////
	// ��̹�ɽ��
	$count = 0;

	// aryOrganizationFront ����
	$aryOrganizationFront = Array ( "t" => "��", "f" => "��" );

	// bytCompanyDisplayFlag ����
	$aryCompanyDisplayFlag = Array ( "t" => "ɽ��", "f" => "��ɽ��" );

	// lngAttributeCode (CODE+NAME)����
	$strQuery = "SELECT * FROM m_Attribute a, m_AttributeRelation ar WHERE a.lngAttributeCode = ar.lngAttributeCode";

	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
	if ( $lngResultNum )
	{
		for ( $i = 0; $i < $lngResultNum; $i++ )
		{
			$objResult = $objDB->fetchObject( $lngResultID, $i );
			$aryAttributeCode[$objResult->lngcompanycode] .= $objResult->strattributename . " ";
		}
	}


	// �쥳����ɽ������
	foreach ( $objMaster->aryData as $record )
	{
		$count++;
		$aryParts["strResultHtml"] .= "	<tr id=\"Mrecord$count\" class=\"Segs\" onclick=\"fncSelectTrColor( this );\" style=\"background:#ffffff;\">\n";

		// ���������
		$aryParts["strResultHtml"] .= "		<th>$count</th>\n";
		$aryParts["strResultHtml"] .= "		<td nowrap>" . $record[$objMaster->aryColumnName[0]] . "</td>\n";
		$aryParts["strResultHtml"] .= "		<td nowrap>" . $record["strcountryname"] . "</td>\n";
		$aryParts["strResultHtml"] .= "		<td nowrap>" . $record["strorganizationname"] . "</td>\n";
		$aryParts["strResultHtml"] .= "		<td nowrap>" . $aryOrganizationFront[$record[$objMaster->aryColumnName[3]]] . "</td>\n";
		$aryParts["strResultHtml"] .= "		<td nowrap>" . $record[$objMaster->aryColumnName[4]] . "</td>\n";
		$aryParts["strResultHtml"] .= "		<td nowrap>" . $aryCompanyDisplayFlag[$record[$objMaster->aryColumnName[5]]] . "</td>\n";
		$aryParts["strResultHtml"] .= "		<td nowrap>" . $record[$objMaster->aryColumnName[6]] . "</td>\n";
		$aryParts["strResultHtml"] .= "		<td nowrap>" . $record[$objMaster->aryColumnName[7]] . "</td>\n";
		$aryParts["strResultHtml"] .= "		<td nowrap>" . $record[$objMaster->aryColumnName[8]] . "</td>\n";
		$aryParts["strResultHtml"] .= "		<td nowrap>" . $record[$objMaster->aryColumnName[9]] . "</td>\n";
		$aryParts["strResultHtml"] .= "		<td nowrap>" . $record[$objMaster->aryColumnName[10]] . "</td>\n";
		$aryParts["strResultHtml"] .= "		<td nowrap>" . $record[$objMaster->aryColumnName[11]] . "</td>\n";
		$aryParts["strResultHtml"] .= "		<td nowrap>" . $record[$objMaster->aryColumnName[12]] . "</td>\n";
		$aryParts["strResultHtml"] .= "		<td nowrap>" . $record[$objMaster->aryColumnName[13]] . "</td>\n";
		$aryParts["strResultHtml"] .= "		<td nowrap>" . $record[$objMaster->aryColumnName[14]] . "</td>\n";
		$aryParts["strResultHtml"] .= "		<td nowrap>" . $record[$objMaster->aryColumnName[15]] . "</td>\n";
		$aryParts["strResultHtml"] .= "		<td nowrap>" . $record[$objMaster->aryColumnName[16]] . "</td>\n";
		$aryParts["strResultHtml"] .= "		<td nowrap>" . $record[$objMaster->aryColumnName[17]] . "</td>\n";
		$aryParts["strResultHtml"] .= "		<td nowrap>" . $record[$objMaster->aryColumnName[18]] . "</td>\n";
		$aryParts["strResultHtml"] .= "		<td nowrap>" . $record["strcloseddaycode"] . ":" . $record["lngclosedday"] . "</td>\n";
		$aryParts["strResultHtml"] .= "		<td nowrap>" . $aryAttributeCode[$record[$objMaster->aryColumnName[0]]] . "</td>\n";


		// �����ܥ�������
		$aryParts["strResultHtml"] .= "		<td bgcolor=\"#ffffff\" onmouseout=\"trClickFlg='on';\" onclick=\"trClickFlg='off';fncSelectSomeTrColor( this, 'TD" . $lngResultNum . "_',1 );\" nowrap><a href=\"/m/regist/co/edit.php?lngActionCode=" . DEF_ACTION_UPDATE . fncGetUrl( $aryData ) . "&lngcompanycode=" . $record[$objMaster->aryColumnName[0]] . "\" name=\"fix\"><img onmouseover=\"RenewOn(this);\" onmouseout=\"RenewOff(this);\" src=\"/img/type01/cmn/seg/renew_off_bt.gif\" width=\"15\" height=\"15\" border=\"0\" alt=\"RENEW\"></a></td>\n";

		// ����ܥ�������
		$aryParts["strResultHtml"] .= "		<td bgcolor=\"#ffffff\" onmouseout=\"trClickFlg='on';\" onclick=\"trClickFlg='off';fncSelectSomeTrColor( this, 'TD" . $lngResultNum . "_',1 );\" nowrap><a href=\"/m/regist/co/confirm.php?lngActionCode=" . DEF_ACTION_DELETE . fncGetUrl( $aryData ) . "&lngcompanycode=" . $record[$objMaster->aryColumnName[0]] . "\" name=\"delete\"><img onmouseover=\"RemoveOn(this);\" onmouseout=\"RemoveOff(this);\" src=\"/img/type01/cmn/seg/remove_off_bt.gif\" width=\"15\" height=\"15\" border=\"0\" alt=\"DELETE\"></a></td>\n";


		$aryParts["strResultHtml"] .= "	</tr>\n";

	}
}
else
{
	$aryParts["strResultHtml"] = "<tr bgcolor=#ffffff><th colspan=" . ( count ( $objMaster->aryColumnName ) + 1 ) . ">���̵����</th></tr>";
}

// ������HTML����
$aryParts["strColumnHtml"] = $objMaster->getColumnHtmlTable( 21 );

$objDB->close();



// index.php -> lngAttributeCode      -> index.php
// index.php -> strCompanyDisplayName -> index.php
$aryParts["HIDDEN"]           = "<input type=hidden name=strSessionID value=" .$aryData["strSessionID"] .">\n";
$aryParts["HIDDEN"]          .= "<input type=hidden name=lngAttributeCode value=" .$aryData["lngAttributeCode"].">\n";
$aryParts["HIDDEN"]          .= "<input type=hidden name=strCompanyDisplayName value=" .$aryData["strCompanyDisplayName"]. ">\n";
// $aryParts["lngLanguageCode"]  =& $aryData["lngLanguageCode"];
$aryParts["strTableName"]     =& $objMaster->strTableName;
$aryParts["lngColumnNum"]     = 20;
$aryParts["strEditURL"]       = "/m/regist/co/edit.php?lngActionCode=" . DEF_ACTION_INSERT . "&strSessionID=" . $aryData["strSessionID"];
//$aryParts["strEditURL"]       = "/m/regist/co/edit.php?lngActionCode=" . DEF_ACTION_INSERT . fncGetUrl( $aryData );


// HTML����
//echo getArrayTable( $aryData, "TABLE" );exit;
$objTemplate = new clsTemplate();
$objTemplate->getTemplate( "m/result/parts.tmpl" );
$objTemplate->replace( $aryParts );
$objTemplate->complete();
echo $objTemplate->strTemplate;



return TRUE;
?>
