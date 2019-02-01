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
// index.php -> strSessionID        -> index.php
// index.php -> lngMonetaryRateCode -> index.php
// index.php -> lngMonetaryUnitCode -> index.php
// index.php -> now                 -> index.php
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
$aryCheck["strSessionID"]        = "null:numenglish(32,32)";
$aryCheck["lngMonetaryRateCode"] = "number(0,2147483647)";
$aryCheck["lngMonetaryUnitCode"] = "number(0,2147483647)";
$aryCheck["now"]                 = "number(0,1)";


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
$objMaster->strTableName = "m_MonetaryRate";

// ��������������
$strQuery = "SELECT * FROM m_MonetaryRate";

// �̲ߥ졼�ȥ����ɾ������
if ( $aryData["lngMonetaryRateCode"] )
{
	$aryWhereString[] = " lngMonetaryRateCode = " . $aryData["lngMonetaryRateCode"];
}

// �̲�ñ�̥����ɾ������
if ( $aryData["lngMonetaryUnitCode"] )
{
	$aryWhereString[] = " lngMonetaryUnitCode = " . $aryData["lngMonetaryUnitCode"];
}

// ����Ŭ������̲ߥ졼�Ⱦ������
if ( $aryData["now"] )
{
	$aryWhereString[] = " dtmApplyStartDate <= now() AND dtmApplyEndDate >= now()";
}

// ���ʬ����������������ɲ�
if ( count ( $aryWhereString ) )
{
	$strWhereString = join ( " AND", $aryWhereString );
	$strQuery .= " WHERE " . $strWhereString;
}

$strQuery .= " ORDER BY lngMonetaryRateCode, lngMonetaryUnitCode, dtmApplyStartDate DESC";


// �ǡ����μ����ȥ��֥������ȤؤΥ��å�
list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

// ������Ϣ�ǡ����μ����ȥ��å�
$lngColumnNum = $objDB->getFieldsCount ( $lngResultID );

for ( $i = 0; $i < $lngColumnNum; $i++ )
{
	// �����̾���ɤ߹��ߤȥ��å�
	$objMaster->aryColumnName[$i] = pg_field_name ( $lngResultID, $i);

	// �����ɤ߹��ߤȥ��å�
	$objMaster->aryType[$i]       = pg_field_type ( $lngResultID, $i);
}

if ( $lngResultNum )
{
	// �ǡ������ɤ߹��ߤȥ��å�
	$objMaster->aryData = pg_fetch_all ( $lngResultID );

	///////////////////////////////////////////////////////////////////
	// �ơ��֥�����
	///////////////////////////////////////////////////////////////////
	// ��̹�ɽ��
	$count = 0;

	// lngMonetaryRateCode �Υץ�������˥塼(CODE+NAME)����
	$aryMonetaryRateCode = fncGetMasterValue( "m_MonetaryRateClass", "lngMonetaryRateCode", "strMonetaryRateName", "Array", "", $objDB );
	// lngMonetaryUnitCode �Υץ�������˥塼(CODE+NAME)����
	$aryMonetaryUnitCode = fncGetMasterValue( "m_MonetaryUnit", "lngMonetaryUnitCode", "strMonetaryUnitName || ':' || strMonetaryUnitSign", "Array", "", $objDB );

	$dtmNowDate = date ( "Y-m-d" );

	// �쥳����ɽ������
	foreach ( $objMaster->aryData as $record )
	{
		$count++;

		// ���ߤ�ǯ�������ޤޤ��쥳���ɤξ�硢�طʿ����Ѥ���ɽ��
		if ( $record[$objMaster->aryColumnName[3]] <= $dtmNowDate && $record[$objMaster->aryColumnName[4]] >= $dtmNowDate )
		{
			$aryParts["strResultHtml"] .= "	<tr id=\"Mrecord$count\" class=\"Segs\" onclick=\"fncSelectTrColor( this );\" style=\"background:#99CCff;\">\n";
		}
		else
		{
			$aryParts["strResultHtml"] .= "	<tr id=\"Mrecord$count\" class=\"Segs\" onclick=\"fncSelectTrColor( this );\" style=\"background:#ffffff;\">\n";
		}

		// ���������
		$aryParts["strResultHtml"] .= "		<th>$count</th>\n";

		$aryParts["strResultHtml"] .= "		<td nowrap>" . $aryMonetaryRateCode[$record[$objMaster->aryColumnName[0]]] . "</td>\n";
		$aryParts["strResultHtml"] .= "		<td nowrap>" . $aryMonetaryUnitCode[$record[$objMaster->aryColumnName[1]]] . "</td>\n";
		$aryParts["strResultHtml"] .= "		<td nowrap>" . $record[$objMaster->aryColumnName[2]] . "</td>\n";
		$aryParts["strResultHtml"] .= "		<td nowrap>" . $record[$objMaster->aryColumnName[3]] . "</td>\n";
		$aryParts["strResultHtml"] .= "		<td nowrap>" . $record[$objMaster->aryColumnName[4]] . "</td>\n";

		// GET���Ϥ�ʸ��������
		$getUrl = "strSessionID=$aryData[strSessionID]&lngmonetaryratecode=" . $record["lngmonetaryratecode"] . "&lngmonetaryunitcode=" . $record["lngmonetaryunitcode"] . "&dtmapplystartdate=" . $record["dtmapplystartdate"];


		// ���Υ졼�Ȥξ�硢�����ܥ������ɽ��
		if ( $record[$objMaster->aryColumnName[4]] < $dtmNowDate )
		{
			// �����ܥ�����ɽ��
			$aryParts["strResultHtml"] .= "		<td bgcolor=\"#ffffff\" nowrap></td>\n";
		}

		else
		{
			// �����ܥ�������
			$aryParts["strResultHtml"] .= "		<td bgcolor=\"#ffffff\" onmouseout=\"trClickFlg='on';\" onclick=\"trClickFlg='off';fncSelectSomeTrColor( this, 'TD" . $lngResultNum . "_',1 );\" nowrap><a href=\"javascript:fncShowDialogCommonMaster('/m/regist/r/edit.php?lngActionCode=" . DEF_ACTION_UPDATE . "&$getUrl' , window.form1 , 'ResultIframeCommonMaster' , 'NO' , $_COOKIE[lngLanguageCode] , 'fix' );\"><img onmouseover=\"RenewOn(this);\" onmouseout=\"RenewOff(this);\" src=\"/img/type01/cmn/seg/renew_off_bt.gif\" width=\"15\" height=\"15\" border=\"0\" alt=\"RENEW\"></a></td>\n";
		}


		$aryParts["strResultHtml"] .= "	</tr>\n";

	}
}

// ��̤��ʤ��ä���硢���̵����ɽ��
else
{
	$aryParts["strResultHtml"] = "<tr bgcolor=#ffffff><th colspan=" . ( count ( $objMaster->aryColumnName ) + 1 ) . ">���̵����</th></tr>";
}

// ������HTML����
$aryParts["strColumnHtml"] = $objMaster->getColumnHtmlTable( 5 );




$objDB->close();



$aryParts["HIDDEN"]          = "<input type=hidden name=strSessionID value=$aryData[strSessionID]>\n";
$aryParts["HIDDEN"]         .= "<input type=hidden name=lngMonetaryRateCode value=$aryData[lngMonetaryRateCode]>\n";
$aryParts["HIDDEN"]         .= "<input type=hidden name=lngMonetaryUnitCode value=$aryData[lngMonetaryUnitCode]>\n";
$aryParts["HIDDEN"]         .= "<input type=hidden name=now value=$aryData[now]>\n";
$aryParts["lngLanguageCode"] =& $_COOKIE["lngLanguageCode"];
$aryParts["strTableName"]   =& $objMaster->strTableName;
$aryParts["lngColumnNum"]   = 5;
$aryParts["strEditURL"]     = "/m/regist/r/edit.php?lngActionCode=" . DEF_ACTION_INSERT . "&strSessionID=" . $aryData["strSessionID"];


// HTML����
$objTemplate = new clsTemplate();
$objTemplate->getTemplate( "m/result/parts.tmpl" );
$objTemplate->replace( $aryParts );
$objTemplate->complete();
echo $objTemplate->strTemplate;



return TRUE;
?>
