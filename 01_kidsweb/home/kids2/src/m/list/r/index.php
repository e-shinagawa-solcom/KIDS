<?
/** 
*	�ޥ������� �̲ߥ졼�ȥޥ��� �ޥ������ơ��֥��̰�������
*
*	@package   KIDS
*	@license   http://www.wiseknot.co.jp/ 
*	@copyright Copyright &copy; 2003, Wiseknot 
*	@author    Kenji Chiba <k-chiba@wiseknot.co.jp> 
*	@access    public
*	@version   1.00
*
*/
// index.php -> strSessionID    -> index.php
//
// ��Ͽ����
// index.php -> strSessionID          -> edit.php
// index.php -> lngActionCode         -> edit.php
//
// ��������
// index.php -> strSessionID          -> edit.php
// index.php -> lngActionCode         -> edit.php
// index.php -> lngmonetaryratecode   -> edit.php
// index.php -> lngmonetaryunitcode   -> edit.php

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
$aryCheck["strSessionID"]       = "null:numenglish(32,32)";

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
$objMaster->setMasterTable( $aryData["strMasterTableName"], "", "", $aryData, $objDB );





///////////////////////////////////////////////////////////////////
// �ơ��֥�����
///////////////////////////////////////////////////////////////////
// ��̹�ɽ��
$count = 0;
foreach ( $objMaster->aryData as $record )
{
	// �ǽ�Υ����򥭡��Ȥ���
	$aryData["strResultHtml"] .= "	<tr id=\"Mrecord$count\" class=\"Segs\" onclick=\"fncSelectTrColor( this );\" style=\"background:#ffffff;\">\n";

	// ���������
	foreach ( $record as $colmun )
	{
		$aryData["strResultHtml"] .= "		<td nowrap>$colmun</td>\n";
	}

	// GET���Ϥ�ʸ��������
	$getUrl = "strSessionID=" .$aryData["strSessionID"]. "&lngmonetaryratecode=" . $record["lngmonetaryratecode"] . "&lngmonetaryunitcode=" . $record["lngmonetaryunitcode"];


	// �����ܥ�������
	$aryData["strResultHtml"] .= "		<td bgcolor=\"#ffffff\" nowrap><a href=\"javascript:fncShowDialogCommonMaster('/m/regist/r/edit.php?lngActionCode=" . DEF_ACTION_UPDATE . "&$getUrl' , window.form1 , 'ResultIframeCommonMaster' , 'NO' , 1 , 'fix' );\"><img onmouseover=\"RenewOn(this);\" onmouseout=\"RenewOff(this);\" src=\"/img/type01/cmn/seg/renew_off_bt.gif\" width=\"15\" height=\"15\" border=\"0\" alt=\"RENEW\"></a></td>\n";

	$aryData["strResultHtml"] .= "	</tr>\n";

}



$objDB->close();



// ��Ͽ�ܥ����GETʸ��������
$aryData["strInsertForm"] = "/m/regist/r/edit.php?strSessionID=" .$aryData["strSessionID"]. "&lngActionCode=" . DEF_ACTION_INSERT;

$aryData["lngLanguageCode"] =1;

// HTML����
$objTemplate = new clsTemplate();
$objTemplate->getTemplate( "m/list/r/parts.tmpl" );
$objTemplate->replace( $aryData );
$objTemplate->complete();
echo $objTemplate->strTemplate;



return TRUE;
?>
