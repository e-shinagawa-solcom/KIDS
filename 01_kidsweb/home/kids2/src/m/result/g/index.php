<?
/** 
*	�ޥ������� ���롼�ץޥ��� �ޥ������ơ��֥��̰�������
*
*	@package   KIDS
*	@license   http://www.wiseknot.co.jp/ 
*	@copyright Copyright &copy; 2003, Wiseknot 
*	@author    Kenji Chiba <k-chiba@wiseknot.co.jp> 
*	@access    public
*	@version   1.00
*
*/
// index.php -> strSessionID        -> index.php
// index.php -> lngAttributeCode    -> index.php
// index.php -> strGroupDisplayName -> index.php
//
// ��Ͽ����
// index.php -> strSessionID        -> edit.php
// index.php -> lngActionCode       -> edit.php
// index.php -> strGroupDisplayName -> edit.php
//
// ��������
// index.php -> strSessionID        -> edit.php
// index.php -> lngActionCode       -> edit.php
// index.php -> lngGroupCode        -> edit.php
// index.php -> strGroupDisplayName -> edit.php

// �����ɤ߹���
include_once('conf.inc');

// �饤�֥���ɤ߹���
require (LIB_FILE);
require (SRC_ROOT . "m/cmn/lib_m.php");



$objDB   = new clsDB();
$objAuth = new clsAuth();
$objDB->open( "", "", "", "" );

$aryData = $_POST;

// ʸ��������å�
$aryCheck["strSessionID"]        = "null:numenglish(32,32)";
$aryCheck["strGroupDisplayName"] = "length(1,100)";

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

// ��������������
$strQuery = "SELECT *, c.strcompanydisplayname FROM m_Group g, m_Company c";

// ɽ�����롼��̾�ξ������
if ( $aryData["strGroupDisplayName"] )
{
	$aryWhereString[] = " g.strGroupDisplayName LIKE '%" . $aryData["strGroupDisplayName"] . "%'";
}

// °���ơ��֥�Ȥη��
$aryWhereString[] = " c.lngCompanyCode = g.lngCompanyCode";

// ���ʬ����������������ɲ�
$strWhereString = join ( " AND", $aryWhereString );
$strQuery .= " WHERE " . $strWhereString . " ORDER BY c.lngCompanyCode, g.lngGroupCode";



// �ǡ����μ����ȥ��֥������ȤؤΥ��å�
$lngResultNum = $objMaster->setMasterTableData( $strQuery, $objDB );
$objMaster->strTableName = "m_Group";

if ( $lngResultNum )
{
	///////////////////////////////////////////////////////////////////
	// �ơ��֥�����
	///////////////////////////////////////////////////////////////////
	// ��̹�ɽ��
	$count = 0;

	// bytGroupDisplayFlag ����
	$aryGroupDisplayFlag = Array ( "t" => "ɽ��", "f" => "��ɽ��" );


	// �쥳����ɽ������
	foreach ( $objMaster->aryData as $record )
	{
		$count++;
		$aryParts["strResultHtml"] .= "	<tr id=\"Mrecord$count\" class=\"Segs\" onclick=\"fncSelectTrColor( this );\" style=\"background:" . $record[$objMaster->aryColumnName[6]] . ";\">\n";

		// ���������
		$aryParts["strResultHtml"] .= "		<th>$count</th>\n";
		$aryParts["strResultHtml"] .= "		<td nowrap>" . $record[$objMaster->aryColumnName[0]] . "</td>\n";
		$aryParts["strResultHtml"] .= "		<td nowrap>" . fncHTMLSpecialChars( $record[strcompanydisplayname] ) . "</td>\n";
		$aryParts["strResultHtml"] .= "		<td nowrap>" . fncHTMLSpecialChars( $record[$objMaster->aryColumnName[2]] ) . "</td>\n";
		$aryParts["strResultHtml"] .= "		<td nowrap>" . $aryGroupDisplayFlag[$record[$objMaster->aryColumnName[3]]] . "</td>\n";
		$aryParts["strResultHtml"] .= "		<td nowrap>" . $record[$objMaster->aryColumnName[4]] . "</td>\n";
		$aryParts["strResultHtml"] .= "		<td nowrap>" . fncHTMLSpecialChars( $record[$objMaster->aryColumnName[5]] ) . "</td>\n";
		$aryParts["strResultHtml"] .= "		<td nowrap>" . $record[$objMaster->aryColumnName[6]] . "</td>\n";


		// �����ܥ�������
		$aryParts["strResultHtml"] .= "		<td bgcolor=\"#ffffff\" onmouseout=\"trClickFlg='on';\" onclick=\"trClickFlg='off';fncSelectSomeTrColor( this, 'TD" . $lngResultNum . "_',1 );\" nowrap><a href=\"javascript:fncShowDialogCommonMaster('/m/regist/g/edit.php?lngActionCode=" . DEF_ACTION_UPDATE . "&lnggroupcode=" . $record["lnggroupcode"] . fncGetURL( $aryData ) . "' , window.form1 , 'ResultIframeCommonMaster' , 'NO' , $_COOKIE[lngLanguageCode] , 'fix' );\"><img onmouseover=\"RenewOn(this);\" onmouseout=\"RenewOff(this);\" src=\"/img/type01/cmn/seg/renew_off_bt.gif\" width=\"15\" height=\"15\" border=\"0\" alt=\"RENEW\"></a></td>\n";

		// ����ܥ�������
		$aryParts["strResultHtml"] .= "		<td bgcolor=\"#ffffff\" onmouseout=\"trClickFlg='on';\" onclick=\"trClickFlg='off';fncSelectSomeTrColor( this, 'TD" . $lngResultNum . "_',1 );\" nowrap><a href=\"javascript:fncShowDialogCommonMaster('/m/regist/g/confirm.php?lngActionCode=" . DEF_ACTION_DELETE . "&lnggroupcode=" . $record["lnggroupcode"] . fncGetURL( $aryData ) . "' , window.form1 , 'ResultIframeCommonMaster' , 'NO' , $_COOKIE[lngLanguageCode] , 'delete' );\"><img onmouseover=\"RemoveOn(this);\" onmouseout=\"RemoveOff(this);\" src=\"/img/type01/cmn/seg/remove_off_bt.gif\" width=\"15\" height=\"15\" border=\"0\" alt=\"DELETE\"></a></td>\n";


		$aryParts["strResultHtml"] .= "	</tr>\n";

	}



}
else
{
	$aryParts["strResultHtml"] = "<tr bgcolor=#ffffff><th colspan=" . ( count ( $objMaster->aryColumnName ) + 1 ) . ">���̵����</th></tr>";
}

// ������HTML����
$aryParts["strColumnHtml"] = $objMaster->getColumnHtmlTable( 7 );



$objDB->close();



$aryParts["HIDDEN"]          = "<input type=hidden name=strSessionID value=$aryData[strSessionID]>\n";
$aryParts["HIDDEN"]         .= "<input type=hidden name=strGroupDisplayName value=$aryData[strGroupDisplayName]>\n";
$aryParts["lngLanguageCode"] =& $_COOKIE["lngLanguageCode"];
$aryParts["strTableName"]    =& $objMaster->strTableName;
$aryParts["lngColumnNum"]    = 7;
$aryParts["strEditURL"]      = "/m/regist/g/edit.php?lngActionCode=" . DEF_ACTION_INSERT . fncGetURL( $aryData );

// HTML����
$objTemplate = new clsTemplate();
$objTemplate->getTemplate( "m/result/parts.tmpl" );
$objTemplate->replace( $aryParts );
$objTemplate->complete();
echo $objTemplate->strTemplate;



return TRUE;
?>
