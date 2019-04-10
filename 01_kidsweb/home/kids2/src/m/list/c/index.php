<?
/** 
*	�ޥ������� ���̥ޥ��� �ޥ������ơ��֥��̰�������
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
// index.php -> strMasterTableName    -> edit.php
// index.php -> strKeyName            -> edit.php
//
// ��������
// index.php -> strSessionID          -> edit.php
// index.php -> lngActionCode         -> edit.php
// index.php -> strMasterTableName    -> edit.php
// index.php -> strKeyName            -> edit.php
// index.php -> lngKeyCode            -> edit.php
// index.php -> (lngStockSubjectCode) -> edit.php
//
// �������
// index.php -> strSessionID          -> confirm.php
// index.php -> lngActionCode         -> confirm.php
// index.php -> strMasterTableName    -> confirm.php
// index.php -> strKeyName            -> confirm.php
// index.php -> lngKeyCode            -> confirm.php
// index.php -> (lngStockSubjectCode) -> confirm.php


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
$aryCheck["strMasterTableName"] = "null:ascii(1,32)";
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
// ������Ϣ�ޥ����ü����
///////////////////////////////////////////////////////////////////
// �������ܥޥ����ξ�硢�����ɡ�̾�ΤΥ�����ɽ�������ü����
if ( $objMaster->strTableName == "m_StockSubject" )
{
	// ������ʬ�ޥ�������ޥ����ǡ������������code �򥭡��Ȥ���Ϣ�����������
	$aryMaster = fncGetMasterValue( "m_StockClass", "lngStockClassCode", "strStockClassName", "Array", "", $objDB );

	$count = count ( $objMaster->aryData );
	for ( $i = 0; $i < $count; $i++ )
	{
		$objMaster->aryData[$i]["lngstockclasscode"] = $objMaster->aryData[$i]["lngstockclasscode"] . ":" . $aryMaster[$objMaster->aryData[$i]["lngstockclasscode"]];
	}
}

// �������ʥޥ����ξ�硢�����ɡ�̾�ΤΥ�����ɽ�������ü����
elseif ( $objMaster->strTableName == "m_StockItem" )
{
	// �������ܥޥ�������ޥ����ǡ������������code �򥭡��Ȥ���Ϣ�����������
	$aryMaster = fncGetMasterValue( "m_StockSubject", "lngStockSubjectCode", "strStockSubjectName", "Array", "", $objDB );

	$count = count ( $objMaster->aryData );
	for ( $i = 0; $i < $count; $i++ )
	{
		$objMaster->aryData[$i]["lngstocksubjectcode"] = $objMaster->aryData[$i]["lngstocksubjectcode"] . ":" . $aryMaster[$objMaster->aryData[$i]["lngstocksubjectcode"]];
	}
}



///////////////////////////////////////////////////////////////////
// �ơ��֥�����
///////////////////////////////////////////////////////////////////
// �ե������̾ɽ��
$aryData["lngColumnNum"] = 0;
foreach ( $objMaster->aryColumnName as $strColumnName )
{
	$aryData["strColumnHtml"] .= "		<td id=\"Column$aryData[lngColumnNum]\" nowrap onmouseover=\"SortOn( this );\" onmouseout=\"SortOff( this );\" onclick=\"location.href='#';\">$strColumnName</td>\n";
	$aryData["lngColumnNum"]++;
}
$aryData["lngColumnNum"]++;


// ��̹�ɽ��
$count = 0;
foreach ( $objMaster->aryData as $record )
{
	// �ǽ�Υ����򥭡��Ȥ���
	$lngKeyCode = $record[$objMaster->aryColumnName[0]];

	$aryData["strResultHtml"] .= "	<tr id=\"Mrecord$count\" class=\"Segs\" onclick=\"fncSelectTrColor( this );\" style=\"background:#ffffff;\">\n";

	// ���������
	foreach ( $record as $colmun )
	{
		$aryData["strResultHtml"] .= "		<td nowrap>" . fncHTMLSpecialChars( $colmun ) . "</td>\n";
	}

	// GET���Ϥ�ʸ��������
	$getUrl = "strSessionID=".$aryData["strSessionID"]. "&strMasterTableName=" .$aryData["strMasterTableName"]."&strKeyName=" .  $objMaster->aryColumnName[0] ."&" .  $objMaster->aryColumnName[0] ."=" . $lngKeyCode;

	// �������ʥޥ����ξ�硢2���ܤΥ����⥭���Ȥ���
	if ( $objMaster->strTableName == "m_StockItem" )
	{
		$getUrl .= "&" .  $objMaster->aryColumnName[1] ."=" .  $record[$objMaster->aryColumnName[1]];
	}


	// �����ܥ�������
	$aryData["strResultHtml"] .= "		<td bgcolor=\"#ffffff\" nowrap><a href=\"javascript:fncShowDialogCommonMaster('/m/regist/c/edit.php?lngActionCode=" . DEF_ACTION_UPDATE . "&$getUrl' , window.form1 , 'ResultIframeCommonMaster' , 'NO' , $_COOKIE[lngLanguageCode] , 'fix' );\"><img onmouseover=\"RenewOn(this);\" onmouseout=\"RenewOff(this);\" src=\"/img/type01/cmn/seg/renew_off_bt.gif\" width=\"15\" height=\"15\" border=\"0\" alt=\"RENEW\"></a></td>\n";

	// ����ܥ�������
	$aryData["strResultHtml"] .= "		<td bgcolor=\"#ffffff\" nowrap><a href=\"javascript:fncShowDialogCommonMaster('/m/regist/c/confirm.php?lngActionCode=" . DEF_ACTION_DELETE . "&$getUrl' , window.form1 , 'ResultIframeCommonMaster' , 'NO' , $_COOKIE[lngLanguageCode] , 'delete' );\"><img onmouseover=\"RemoveOn(this);\" onmouseout=\"RemoveOff(this);\" src=\"/img/type01/cmn/seg/remove_off_bt.gif\" width=\"15\" height=\"15\" border=\"0\" alt=\"REMOVE\"></a></td>\n";

	$aryData["strResultHtml"] .= "	</tr>\n";

}



$objDB->close();



// ��Ͽ�ܥ����GETʸ��������
$aryData["strInsertForm"] = "/m/regist/c/edit.php?strSessionID=". $aryData["strSessionID"] . "&lngActionCode=" . DEF_ACTION_INSERT . "&strMasterTableName=" . $aryData["strMasterTableName"] ."&strKeyName=" .  $objMaster->aryColumnName[0];

$aryData["strTableName"] =& $objMaster->strTableName;
$aryData["lngLanguageCode"] =& $_COOKIE["lngLanguageCode"];

// HTML����
$objTemplate = new clsTemplate();
$objTemplate->getTemplate( "m/list/c/parts.tmpl" );
$objTemplate->replace( $aryData );
$objTemplate->complete();
echo $objTemplate->strTemplate;



return TRUE;
?>
