<?
/** 
*	�ޥ������� ��ҥޥ��� �ǡ������ϲ���
*
*	@package   KIDS
*	@license   http://www.wiseknot.co.jp/ 
*	@copyright Copyright &copy; 2003, Wiseknot 
*	@author    Kenji Chiba <k-chiba@wiseknot.co.jp> 
*	@access    public
*	@version   1.00
*
*/
// ��Ͽ����
// index.php -> strSessionID          -> edit.php
// index.php -> lngActionCode         -> edit.php
// index.php -> lngAttributeCode      -> edit.php
// index.php -> strCompanyDisplayName -> edit.php
//
// ��������
// index.php -> strSessionID   -> edit.php
// index.php -> lngActionCode  -> edit.php
// index.php -> lngcompanycode -> edit.php
//
// ��ǧ���̤�
// edit.php -> strSessionID           -> confirm.php
// edit.php -> lngActionCode          -> confirm.php
// edit.php -> lngcompanycode         -> confirm.php
// edit.php -> lngcountrycode         -> confirm.php
// edit.php -> lngorganizationcode    -> confirm.php
// edit.php -> bytorganizationfront   -> confirm.php
// edit.php -> strcompanyname         -> confirm.php
// edit.php -> bytcompanydisplayflag  -> confirm.php
// edit.php -> strcompanydisplaycode  -> confirm.php
// edit.php -> strcompanydisplayname  -> confirm.php
// edit.php -> strpostalcode          -> confirm.php
// edit.php -> straddress1            -> confirm.php
// edit.php -> straddress2            -> confirm.php
// edit.php -> straddress3            -> confirm.php
// edit.php -> straddress4            -> confirm.php
// edit.php -> strtel1                -> confirm.php
// edit.php -> strtel2                -> confirm.php
// edit.php -> strfax1                -> confirm.php
// edit.php -> strfax2                -> confirm.php
// edit.php -> strdistinctcode        -> confirm.php
// edit.php -> lngcloseddaycode       -> confirm.php
// edit.php -> aryattributecode       -> confirm.php



// �����ɤ߹���
include_once('conf.inc');

// �饤�֥���ɤ߹���
require (LIB_FILE);
require (SRC_ROOT . "m/cmn/lib_m.php");

// DB��³
$objDB   = new clsDB();
$objAuth = new clsAuth();
$objDB->open( "", "", "", "" );

// �ǡ�������
if ( $_GET )
{
	$aryData = $_GET;
}
else
{
	$aryData = $_POST;
}


// ���å�����ǧ
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );


// ���³�ǧ
if ( !fncCheckAuthority( DEF_FUNCTION_M0, $objAuth ) )
{
	fncOutputError ( 9052, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
}


$aryCheck["strSessionID"]        = "null:numenglish(32,32)";
$aryCheck["lngActionCode"]       = "null:number(" . DEF_ACTION_INSERT . "," . DEF_ACTION_DELETE . ")";
if ( $aryData["lngActionCode"] != DEF_ACTION_INSERT )
{
	$aryCheck["lngcompanycode"]  = "null:number(0,2147483647)";
}

// ʸ��������å�
$aryCheckResult = fncAllCheck( $aryData, $aryCheck );
fncPutStringCheckError( $aryCheckResult, $objDB );


// ���顼���ʤ���硢�ޥ��������֥�������������ʸ��������å��¹�
if ( join ( $aryCheckResult ) )
{
	fncOutputError ( 9052, DEF_WARNING, "�ޥ�����������", TRUE, "", $objDB );
}

// �ޥ��������֥�����������
$objMaster = new clsMaster();
$objMaster->setMasterTable( "m_Company", "lngcompanycode", $aryData["lngcompanycode"], "", $objDB );
$objMaster->setAryMasterInfo( $aryData["lngcompanycode"], "" );

// ����������
$lngColumnNum = count ( $objMaster->aryColumnName );

//////////////////////////////////////////////////////////////////////////
// ������ɽ������
//////////////////////////////////////////////////////////////////////////
// �����ξ�硢�������Ϲ�������
if ( $aryData["lngActionCode"] == DEF_ACTION_INSERT )
{
	// �ǡ��������
	$objMaster->aryData[0] = Array ();

	//$seq = fncIsSequence( $objMaster->strTableName . "." . $objMaster->aryColumnName[0], $objDB );
	if ( !$aryData[$objMaster->aryColumnName[0]] )
	{
		$aryData[$objMaster->aryColumnName[0]] = $objMaster->lngRecordRow;
	}

	// ���󥯥���ȸ�Υ������󥹤�9999���ä���礵���1­�������������
	//if ( $seq == 9999 )
	//{
		//$seq = fncGetSequence( $objMaster->strTableName . "." . $objMaster->aryColumnName[0], $objDB );
	//	$seq++;
	//}

	// ��ҥ����ɤΥ��å�
	$objMaster->aryData[0][$objMaster->aryColumnName[0]] = $aryData[$objMaster->aryColumnName[0]];

	// �񥳡��ɤΥ��å�
	$objMaster->aryData[0][$objMaster->aryColumnName[1]] =& $aryData[$objMaster->aryColumnName[1]];

	// �ȿ�������
	$objMaster->aryData[0][$objMaster->aryColumnName[2]] =& $aryData[$objMaster->aryColumnName[2]];

	// �ȿ�ɽ��������
	$objMaster->aryData[0][$objMaster->aryColumnName[3]] =& $aryData[$objMaster->aryColumnName[3]];

	// ���̾��
	$objMaster->aryData[0][$objMaster->aryColumnName[4]] =& $aryData[$objMaster->aryColumnName[4]];

	// ɽ����ҥե饰
	$objMaster->aryData[0][$objMaster->aryColumnName[5]] =& $aryData[$objMaster->aryColumnName[5]];

	// ɽ����ҥ�����
	$objMaster->aryData[0][$objMaster->aryColumnName[6]] =& $aryData[$objMaster->aryColumnName[6]];

	// ɽ�����̾��
	$objMaster->aryData[0][$objMaster->aryColumnName[7]] =& $aryData[$objMaster->aryColumnName[7]];

	// ��ά̾��
	$objMaster->aryData[0][$objMaster->aryColumnName[8]] =& $aryData[$objMaster->aryColumnName[8]];

	// ͹���ֹ�
	$objMaster->aryData[0][$objMaster->aryColumnName[9]] =& $aryData[$objMaster->aryColumnName[9]];

	// ��ƻ�ܸ�
	$objMaster->aryData[0][$objMaster->aryColumnName[10]] =& $aryData[$objMaster->aryColumnName[10]];

	// �Զ跴
	$objMaster->aryData[0][$objMaster->aryColumnName[11]] =& $aryData[$objMaster->aryColumnName[11]];

	// Į������
	$objMaster->aryData[0][$objMaster->aryColumnName[12]] =& $aryData[$objMaster->aryColumnName[12]];

	// �ӥ�������ʪ̾
	$objMaster->aryData[0][$objMaster->aryColumnName[13]] =& $aryData[$objMaster->aryColumnName[13]];

	// �����ֹ�1
	$objMaster->aryData[0][$objMaster->aryColumnName[14]] =& $aryData[$objMaster->aryColumnName[14]];

	// �����ֹ�2
	$objMaster->aryData[0][$objMaster->aryColumnName[15]] =& $aryData[$objMaster->aryColumnName[15]];

	// FAX�ֹ�1
	$objMaster->aryData[0][$objMaster->aryColumnName[16]] =& $aryData[$objMaster->aryColumnName[16]];

	// FAX�ֹ�2
	$objMaster->aryData[0][$objMaster->aryColumnName[17]] =& $aryData[$objMaster->aryColumnName[17]];

	// �����ֹ�
	$objMaster->aryData[0][$objMaster->aryColumnName[18]] =& $aryData[$objMaster->aryColumnName[18]];

	// ������������
	$objMaster->aryData[0][$objMaster->aryColumnName[19]] =& $aryData[$objMaster->aryColumnName[19]];

	// °��������
	$aryAttributeCode = explode ( ":", $aryData["strattributecode"] );
}

// HTML�ü�ʸ������Ѵ�
$objMaster->aryData[0] = fncToHTMLString( $objMaster->aryData[0] );


// ��ҥ�����
if ( $aryData["lngActionCode"] == DEF_ACTION_UPDATE )
{
	$strOutputCompanyCode = $objMaster->aryData[0][$objMaster->aryColumnName[0]];
}
$aryParts["MASTER"][0]  = "<span class=\"InputSegs\"><input type=\"text\" id=\"Input0\" value=\"" . $strOutputCompanyCode . "\" maxlength=\"10\" disabled></span>\n";

$aryData["HIDDEN"]  = "<input type=\"hidden\" name=\"" . $objMaster->aryColumnName[0] . "\" value=\"" . $objMaster->aryData[0][$objMaster->aryColumnName[0]] . "\">\n";

// ��
$aryParts["MASTER"][1]  = "<span class=\"InputSegs\"><select id=\"Input1\" name=\"" . $objMaster->aryColumnName[1] . "\">\n";
$aryParts["MASTER"][1] .= fncGetPulldown( "m_Country", "lngCountryCode", "strCountryName",  $objMaster->aryData[0][$objMaster->aryColumnName[1]], "", $objDB );
$aryParts["MASTER"][1] .= "</select></span>\n";

// �ȿ�
$aryParts["MASTER"][2]  = "<span class=\"InputSegs\"><select id=\"Input2\" name=\"" . $objMaster->aryColumnName[2] . "\">\n";
$aryParts["MASTER"][2] .= fncGetPulldown( "m_Organization", "lngOrganizationCode", "strOrganizationName",  $objMaster->aryData[0][$objMaster->aryColumnName[2]], "", $objDB );
$aryParts["MASTER"][2] .= "</select></span>\n";

// �ȿ�ɽ���ե饰
$strChecked = "";
if ( $objMaster->aryData[0][$objMaster->aryColumnName[3]] == "t" )
{
	$strChecked = " checked";
}
$aryParts["MASTER"][3] = "<span class=\"InputSegs\"><input id=\"Input3\" type=\"checkbox\" name=\"" . $objMaster->aryColumnName[3] . "\" value=\"t\"$strChecked></span>\n";

// ���̾��
$aryParts["MASTER"][4] = "<span class=\"InputSegs\"><input id=\"Input4\" type=\"text\" name=\"" . $objMaster->aryColumnName[4] . "\" value=\"" . $objMaster->aryData[0][$objMaster->aryColumnName[4]] . "\" onfocus=\"chColorOn(this);\" onblur=\"chColorOff(this);\" maxlength=\"100\"></span>\n";


// ɽ����ҥե饰
$strChecked = "";
if ( $objMaster->aryData[0][$objMaster->aryColumnName[5]] == "t" || $aryData["lngActionCode"] == DEF_ACTION_INSERT )
{
	$strChecked = " checked";
}
$aryParts["MASTER"][5] = "<span class=\"InputSegs\"><input id=\"Input5\" type=\"checkbox\" name=\"" . $objMaster->aryColumnName[5] . "\" value=\"t\"$strChecked></span>\n";

// ɽ����ҥ�����
$aryParts["MASTER"][6] = "<span class=\"InputSegs\"><input id=\"Input6\" type=\"text\" name=\"" . $objMaster->aryColumnName[6] . "\" value=\"" . $objMaster->aryData[0][$objMaster->aryColumnName[6]] . "\" onfocus=\"chColorOn(this);\" onblur=\"chColorOff(this);\" maxlength=\"10\"></span>\n";

// ɽ�����̾��
$aryParts["MASTER"][7] = "<span class=\"InputSegs\"><input id=\"Input7\" type=\"text\" name=\"" . $objMaster->aryColumnName[7] . "\" value=\"" . $objMaster->aryData[0][$objMaster->aryColumnName[7]] . "\" onfocus=\"chColorOn(this);\" onblur=\"chColorOff(this);\" maxlength=\"100\"></span>\n";

// ��ά̾��
$aryParts["MASTER"][8] = "<span class=\"InputSegs\"><input id=\"Input8\" type=\"text\" name=\"" . $objMaster->aryColumnName[8] . "\" value=\"" . $objMaster->aryData[0][$objMaster->aryColumnName[8]] . "\" onfocus=\"chColorOn(this);\" onblur=\"chColorOff(this);\" maxlength=\"100\"></span>\n";

// ͹���ֹ�
$aryParts["MASTER"][9] = "<span class=\"InputSegs\"><input id=\"Input9\" type=\"text\" name=\"" . $objMaster->aryColumnName[9] . "\" value=\"" . trim ( $objMaster->aryData[0][$objMaster->aryColumnName[9]] ) . "\" onfocus=\"chColorOn(this);\" onblur=\"chColorOff(this);\" maxlength=\"20\"></span>\n";

// ��ƻ�ܸ�
$aryParts["MASTER"][10] = "<span class=\"InputSegs\"><input id=\"Input10\" type=\"text\" name=\"" . $objMaster->aryColumnName[10] . "\" value=\"" . $objMaster->aryData[0][$objMaster->aryColumnName[10]] . "\" onfocus=\"chColorOn(this);\" onblur=\"chColorOff(this);\" maxlength=\"100\"></span>\n";

// �ԡ��衢��
$aryParts["MASTER"][11] = "<span class=\"InputSegs\"><input id=\"Input11\" type=\"text\" name=\"" . $objMaster->aryColumnName[11] . "\" value=\"" . $objMaster->aryData[0][$objMaster->aryColumnName[11]] . "\" onfocus=\"chColorOn(this);\" onblur=\"chColorOff(this);\" maxlength=\"100\"></span>\n";

// Į������
$aryParts["MASTER"][12] = "<span class=\"InputSegs\"><input id=\"Input12\" type=\"text\" name=\"" . $objMaster->aryColumnName[12] . "\" value=\"" . $objMaster->aryData[0][$objMaster->aryColumnName[12]] . "\" onfocus=\"chColorOn(this);\" onblur=\"chColorOff(this);\" maxlength=\"100\"></span>\n";

// �ӥ�������ʪ̾
$aryParts["MASTER"][13] = "<span class=\"InputSegs\"><input id=\"Input13\" type=\"text\" name=\"" . $objMaster->aryColumnName[13] . "\" value=\"" . $objMaster->aryData[0][$objMaster->aryColumnName[13]] . "\" onfocus=\"chColorOn(this);\" onblur=\"chColorOff(this);\" maxlength=\"100\"></span>\n";

// �����ֹ�1
$aryParts["MASTER"][14] = "<span class=\"InputSegs\"><input id=\"Input14\" type=\"text\" name=\"" . $objMaster->aryColumnName[14] . "\" value=\"" . $objMaster->aryData[0][$objMaster->aryColumnName[14]] . "\" onfocus=\"chColorOn(this);\" onblur=\"chColorOff(this);\" maxlength=\"100\"></span>\n";

// �����ֹ�2
$aryParts["MASTER"][15] = "<span class=\"InputSegs\"><input id=\"Input15\" type=\"text\" name=\"" . $objMaster->aryColumnName[15] . "\" value=\"" . $objMaster->aryData[0][$objMaster->aryColumnName[15]] . "\" onfocus=\"chColorOn(this);\" onblur=\"chColorOff(this);\" maxlength=\"100\"></span>\n";

// �ե��å����ֹ�1
$aryParts["MASTER"][16] = "<span class=\"InputSegs\"><input id=\"Input16\" type=\"text\" name=\"" . $objMaster->aryColumnName[16] . "\" value=\"" . $objMaster->aryData[0][$objMaster->aryColumnName[16]] . "\" onfocus=\"chColorOn(this);\" onblur=\"chColorOff(this);\" maxlength=\"100\"></span>\n";

// �ե��å����ֹ�2
$aryParts["MASTER"][17] = "<span class=\"InputSegs\"><input id=\"Input17\" type=\"text\" name=\"" . $objMaster->aryColumnName[17] . "\" value=\"" . $objMaster->aryData[0][$objMaster->aryColumnName[17]] . "\" onfocus=\"chColorOn(this);\" onblur=\"chColorOff(this);\" maxlength=\"100\"></span>\n";

// ���̥�����
$aryParts["MASTER"][18] = "<span class=\"InputSegs\"><input id=\"Input18\" type=\"text\" name=\"" . $objMaster->aryColumnName[18] . "\" value=\"" . $objMaster->aryData[0][$objMaster->aryColumnName[18]] . "\" onfocus=\"chColorOn(this);\" onblur=\"chColorOff(this);\" maxlength=\"100\"></span>\n";

// ������
$aryParts["MASTER"][19]  = "<span class=\"InputSegs\"><select id=\"Input19\" name=\"" . $objMaster->aryColumnName[19] . "\">\n";
$aryParts["MASTER"][19] .= fncGetPulldown( "m_ClosedDay", "lngClosedDayCode", "strClosedDayCode || ':' || lngClosedDay",  $objMaster->aryData[0][$objMaster->aryColumnName[19]], "", $objDB );
$aryParts["MASTER"][19] .= "</select></span>\n";

// °��
$aryParts["MASTER"][20] = fncGetAttributeHtml( $aryData["lngActionCode"], $aryData["lngcompanycode"], $aryAttributeCode, $objDB );




//////////////////////////////////////////////////////////////////////////
// ��̼��������Ͻ���
//////////////////////////////////////////////////////////////////////////
//echo fncGetReplacedHtml( "m/regist/parts.tmpl", $aryData, $objAuth );
$count = count ( $aryParts["MASTER"] );
for ( $i = 0; $i < $count; $i++ )
{
	$aryData["MASTER"] .= $aryParts["MASTER"][$i];

	// �����ɽ��
	$aryData["COLUMN"] .= "<span id=\"Column$i\" class=\"ColumnSegs\"></span>\n";
}

$objDB->close();


$aryData["strTableName"]    = $objMaster->strTableName;


// HTML����
$objTemplate = new clsTemplate();
$objTemplate->getTemplate( "m/regist/co/edit.tmpl" );
$objTemplate->replace( $aryData );
$objTemplate->complete();
echo $objTemplate->strTemplate;

// ��°°��ɽ���ؿ�
function fncGetAttributeHtml( $lngActionCode, $lngcompanycode, $aryAttributeCode, $objDB )
{
	$objAttribute = new clsMaster();

	// ����������äƤ�����硢��°°�����֥������Ȥ����
	if ( $lngActionCode == DEF_ACTION_INSERT && count ( $aryAttributeCode ) )
	{
		$objAttribute->aryData = $aryAttributeCode;
	}

	// �����ξ�硢��°°�����֥������Ȥ����
	elseif ( $lngActionCode == DEF_ACTION_UPDATE )
	{
		$objAttribute->setMasterTable( "m_AttributeRelation", "lngCompanyCode", $lngcompanycode, "", $objDB );
		$objAttribute->setAryMasterInfo( $lngcompanycode, "" );
	}

	// °����������
	list ( $lngResultID, $lngResultNum ) = fncQuery( "SELECT * FROM m_Attribute", $objDB );

	$strParts = "<span class=\"InputSegs_for_attribute\"><select id=\"Input20\" name=\"aryattributecode\" multiple size=\"$lngResultNum\">\n";

	// °������ɽ��
	for ( $i = 0; $i < $lngResultNum; $i++ )
	{
		// "selected" ʸ��������
		$strSelected = "";

		$objResult = $objDB->fetchObject( $lngResultID, $i );

		// ��°°���ο����������å�
		for ( $j = 0; $j < count ( $objAttribute->aryData ); $j++ )
		{
			
			// ɽ������°���˽�°���Ƥ�����硢������֤ˤ������
			if ( !empty($objAttribute->aryData[$j]) && $objResult->lngattributecode == $objAttribute->aryData[$j]["lngattributecode"] )
			{
				$strSelected = " selected";
				break;
			}
		}
		$strParts .= "<option value=" . $objResult->lngattributecode . $strSelected . ">" . $objResult->strattributename . "</option>\n";
	}

	$strParts .= "</select></span>\n";
	return $strParts;
}



return TRUE;
?>
