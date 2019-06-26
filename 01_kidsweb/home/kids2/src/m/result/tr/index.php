<?
/** 
*	�ޥ������� ����졼�ȥޥ��� �ޥ������ơ��֥��̰�������
*
*	@package   KIDS
*	@license   http://www.solcom.co.jp/ 
*	@copyright Copyright &copy; 2019, Solcom 
*	@author    solcom rin
*	@access    public
*	@version   1.00
*
*/
// index.php -> strSessionID        -> index.php
// index.php -> lngmonetaryunitcode -> index.php
// index.php -> now                 -> index.php
//
// ��Ͽ����
// index.php -> strSessionID          -> edit.php
// index.php -> lngActionCode         -> edit.php
//
// ��������
// index.php -> strSessionID          -> edit.php
// index.php -> lngActionCode         -> edit.php
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
// $aryData = $_GET;

// ʸ��������å�
$aryCheck["strSessionID"]        = "null:numenglish(32,32)";
$aryCheck["lngmonetaryunitcode"] = "number(0,2147483647)";
$aryCheck["dtmapplystartdate"]   = "date(/)";
$aryCheck["dtmapplyenddate"]   = "date(/)";


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
$objMaster->strTableName = "m_TemporaryRate";

// ��������������
$strQuery = "SELECT * FROM m_TemporaryRate";

// �̲�ñ�̥����ɾ������
if ( $aryData["lngmonetaryunitcode"] )
{
	$aryWhereString[] = " lngmonetaryunitcode = " . $aryData["lngmonetaryunitcode"];
}

// Ŭ�ѳ��Ϸ�������
if ( $aryData["dtmapplystartdate"] )
{
	$aryWhereString[] = " dtmapplystartdate >= " . $aryData["dtmapplystartdate"];
}

// Ŭ�ѽ�λ��������
if ( $aryData["dtmapplyenddate"] )
{
	$aryWhereString[] = "dtmapplyenddate <= " . $aryData["dtmapplystartdate"];
}

// ���ʬ����������������ɲ�
if ( $aryWhereString && count ( $aryWhereString ) )
{
	$strWhereString = join ( " AND", $aryWhereString );
	$strQuery .= " WHERE " . $strWhereString;
}

$strQuery .= " ORDER BY lngmonetaryunitcode, dtmapplystartdate DESC";

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

	// lngmonetaryunitcode �Υץ�������˥塼(CODE+NAME)����
	$aryMonetaryUnitCode = fncGetMasterValue( "m_MonetaryUnit", "lngmonetaryunitcode", "strMonetaryUnitName || ':' || strMonetaryUnitSign", "Array", "", $objDB );

	$dtmNowDate = date ( "Y-m-d" );

	// �쥳����ɽ������
	foreach ( $objMaster->aryData as $record )
	{
		$count++;

		// ���ߤ�ǯ�������ޤޤ��쥳���ɤξ�硢�طʿ����Ѥ���ɽ��
		if ( $record[$objMaster->aryColumnName[2]] <= $dtmNowDate && $record[$objMaster->aryColumnName[3]] >= $dtmNowDate )
		{
			$aryParts["strResultHtml"] .= "	<tr id=\"Mrecord$count\" class=\"Segs\" onclick=\"fncSelectTrColor( this );\" style=\"background:#99CCff;\">\n";
		}
		else
		{
			$aryParts["strResultHtml"] .= "	<tr id=\"Mrecord$count\" class=\"Segs\" onclick=\"fncSelectTrColor( this );\" style=\"background:#ffffff;\">\n";
		}
		// ���������
		$aryParts["strResultHtml"] .= "		<th>$count</th>\n";
		$aryParts["strResultHtml"] .= "		<td nowrap>" . $aryMonetaryUnitCode[$record["lngmonetaryunitcode"]] . "</td>\n";
		$aryParts["strResultHtml"] .= "		<td nowrap>" . $record["curconversionrate"] . "</td>\n";
		$aryParts["strResultHtml"] .= "		<td nowrap>" . $record["dtmapplystartdate"] . "</td>\n";
		$aryParts["strResultHtml"] .= "		<td nowrap>" . $record["dtmapplyenddate"] . "</td>\n";

		// GET���Ϥ�ʸ��������
		$getUrl = "strSessionID=" .$aryData["strSessionID"]
				. "&lngmonetaryunitcode=" . $record["lngmonetaryunitcode"]
				. "&dtmapplystartdate=" . $record["dtmapplystartdate"];
// echo $lngResultNum;
// return;

		// ���Υ졼�Ȥξ�硢�����ܥ������ɽ��
		if ( $record[$objMaster->aryColumnName[3]] < $dtmNowDate )
		{
			// �����ܥ�����ɽ��
			$aryParts["strResultHtml"] .= "		<td bgcolor=\"#ffffff\" nowrap></td>\n";
		}

		else
		{
			// �����ܥ�������
			// $aryParts["strResultHtml"] .= "		<td bgcolor=\"#ffffff\" onmouseout=\"trClickFlg='on';\" onclick=\"trClickFlg='off';fncSelectSomeTrColor( this, 'TD" . $lngResultNum . "_',1 );\" nowrap><a href=\"javascript:fncShowDialogCommonMaster('/m/regist/tr/edit.php?lngActionCode=" . DEF_ACTION_UPDATE . "&$getUrl' , window.form1 , 'ResultIframeCommonMaster' , 'NO' , $_COOKIE[lngLanguageCode] , 'fix' );\"><img onmouseover=\"RenewOn(this);\" onmouseout=\"RenewOff(this);\" src=\"/img/type01/cmn/seg/renew_off_bt.gif\" width=\"15\" height=\"15\" border=\"0\" alt=\"RENEW\"></a></td>\n";
			$aryParts["strResultHtml"] .= "		<td bgcolor=\"#ffffff\" onmouseout=\"trClickFlg='on';\" onclick=\"trClickFlg='off';fncSelectSomeTrColor( this, 'TD" . $lngResultNum . "_',1 );\" nowrap><a href=\"/m/regist/tr/edit.php?lngActionCode=" . DEF_ACTION_UPDATE . "&" . $getUrl ."\" name=\"fix\"><img onmouseover=\"RenewOn(this);\" onmouseout=\"RenewOff(this);\" src=\"/img/type01/cmn/seg/renew_off_bt.gif\" width=\"15\" height=\"15\" border=\"0\" alt=\"RENEW\"></a></td>\n";
		}


		$aryParts["strResultHtml"] .= "	</tr>\n";

	}
}

// ��̤��ʤ��ä���硢���̵����ɽ��
else
{
	$aryParts["strResultHtml"] = "<tr bgcolor=#ffffff><th colspan=" . 6 . ">���̵����</th></tr>";
}




$objDB->close();



$aryParts["strSessionID"] = $aryData["strSessionID"];
$aryParts["lngmonetaryunitcode"] = $aryData["lngmonetaryunitcode"];
$aryParts["dtmapplystartdate"] = $aryData["dtmapplystartdate"];
$aryParts["dtmapplyenddate"]  = $aryData["dtmapplyenddate"];
$aryParts["strTableName"]   = $objMaster->strTableName;
$aryParts["strEditURL"]     = "/m/regist/tr/edit.php?lngActionCode=" . DEF_ACTION_INSERT . "&strSessionID=" . $aryData["strSessionID"];

// HTML����
$objTemplate = new clsTemplate();
$objTemplate->getTemplate( "m/result/tr/parts.tmpl" );
$objTemplate->replace( $aryParts );
$objTemplate->complete();
echo $objTemplate->strTemplate;



return TRUE;
?>
