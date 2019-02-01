<?
/** 
*	�ޥ������� ��ҥޥ��� ��ǧ����
*
*	@package   KIDS
*	@license   http://www.wiseknot.co.jp/ 
*	@copyright Copyright &copy; 2003, Wiseknot 
*	@author    Kenji Chiba <k-chiba@wiseknot.co.jp> 
*	@access    public
*	@version   1.00
*
*/
// ��Ͽ������
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
//
// ���
// index.php -> strSessionID          -> confirm.php
// index.php -> lngActionCode         -> confirm.php
// index.php -> lngcompanycode        -> confirm.php
//
// ��Ͽ�������¹Ԥ�
// confirm.php -> strSessionID           -> action.php
// confirm.php -> lngActionCode          -> action.php
// confirm.php -> lngcompanycode         -> action.php
// confirm.php -> lngcountrycode         -> action.php
// confirm.php -> lngorganizationcode    -> action.php
// confirm.php -> bytorganizationfront   -> action.php
// confirm.php -> strcompanyname         -> action.php
// confirm.php -> bytcompanydisplayflag  -> action.php
// confirm.php -> strcompanydisplaycode  -> action.php
// confirm.php -> strcompanydisplayname  -> action.php
// confirm.php -> strpostalcode          -> action.php
// confirm.php -> straddress1            -> action.php
// confirm.php -> straddress2            -> action.php
// confirm.php -> straddress3            -> action.php
// confirm.php -> straddress4            -> action.php
// confirm.php -> strtel1                -> action.php
// confirm.php -> strtel2                -> action.php
// confirm.php -> strfax1                -> action.php
// confirm.php -> strfax2                -> action.php
// confirm.php -> strdistinctcode        -> action.php
// confirm.php -> lngcloseddaycode       -> action.php
// confirm.php -> strattributecode       -> action.php
//
// ����¹Ԥ�
// confirm.php -> strSessionID          -> action.php
// confirm.php -> lngActionCode         -> action.php
// confirm.php -> lngcompanycode        -> action.php


// �����ɤ߹���
include_once('conf.inc');

// �饤�֥���ɤ߹���
require (LIB_FILE);
require (SRC_ROOT . "m/cmn/lib_m.php");

// DB��³
$objDB   = new clsDB();
$objAuth = new clsAuth();
$objDB->open( "", "", "", "" );

// GET�ǡ�������
$aryData = $_GET;

if ( $aryData["lngActionCode"] != DEF_ACTION_DELETE )
{
	// ʣ�����򥻥쥯�ȥܥå�������°�������ɤ��������
preg_match_all ( "/(aryattributecode)=(\d+)/", $_SERVER["QUERY_STRING"], $aryAttribute );
	$lngLength = count ( $aryAttribute[2] );

	for ( $i = 0; $i < $lngLength; $i++ )
	{
		// °�����ͥ����å�
		if ( fncCheckString( $aryAttribute[2][$i], "number(0,2147483647)" ) == "" )
		{
			$aryAttributeCode[] = $aryAttribute[2][$i];

			// �ܼҤޤ��ϸܵ�°�����ä���硢���줾��Υե饰��
			if ( $aryAttribute[2][$i] == DEF_ATTRIBUTE_HEADOFFICE )
			{
				$bytHeadOfficeFlag = TRUE;
			}
			elseif ( $aryAttribute[2][$i] == DEF_ATTRIBUTE_CLIENT )
			{
				$bytClientFlag = TRUE;
			}
		}
	}
}


// ���å�����ǧ
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );


// ���³�ǧ
if ( !fncCheckAuthority( DEF_FUNCTION_M0, $objAuth ) )
{
	fncOutputError ( 9052, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
}


$aryCheck["strSessionID"]   = "null:numenglish(32,32)";
$aryCheck["lngActionCode"]  = "null:number(" . DEF_ACTION_INSERT . "," . DEF_ACTION_DELETE . ")";
$aryCheck["lngcompanycode"] = "null:number(0,2147483647)";

if ( $aryData["lngActionCode"] != DEF_ACTION_DELETE )
{
	$aryCheck["lngcompanycode"]        = "null:number(0,2147483647)";
	$aryCheck["lngcountrycode"]        = "null:number(0,2147483647)";
	$aryCheck["lngorganizationcode"]   = "null:number(0,2147483647)";
	$aryCheck["bytorganizationfront"]  = "english(1,1)";
	$aryCheck["strcompanyname"]        = "null:length(1,100)";
	$aryCheck["bytcompanydisplayflag"] = "english(1,1)";
	$aryCheck["strcompanydisplaycode"] = "null:numenglish(0,10)";
	$aryCheck["strcompanydisplayname"] = "null:length(1,100)";
	$aryCheck["strpostalcode"]         = "ascii(0,20)";
	$aryCheck["straddress1"]           = "length(1,100)";
	$aryCheck["straddress2"]           = "length(1,100)";
	$aryCheck["straddress3"]           = "length(1,100)";
	$aryCheck["straddress4"]           = "length(1,100)";
	$aryCheck["strtel1"]               = "length(1,100)";
	$aryCheck["strtel2"]               = "length(1,100)";
	$aryCheck["strfax1"]               = "length(1,100)";
	$aryCheck["strfax2"]               = "length(1,100)";
	$aryCheck["lngcloseddaycode"]      = "null:number(,2147483647)";
	$aryCheck["aryattributecode"]      = "null";
	$aryCheck["strdistinctcode"]       = "numenglish(0,100)";

	// �ܵ�°�����Ĥ��Ƥ����硢���̥�����ɬ�ܤ��ѹ�
	//if ( $bytClientFlag )
	//{
	//	$aryCheck["strdistinctcode"] = "null:numenglish(0,100)";
	//}
}

// ʸ��������å�
$aryCheckResult = fncAllCheck( $aryData, $aryCheck );
//echo getArrayTable( $aryCheckResult, "TABLE" );
//exit;
//fncPutStringCheckError( $aryCheckResult, $objDB );

// �ܼҤȸܵ�������°������ꤵ��Ƥ�����硢ʸ��������å����顼��
if ( $bytHeadOfficeFlag && $bytClientFlag )
{
	$aryCheckResult["aryAttributeCode_Error"] = TRUE;
}

// °�����ʤ���硢���顼
if ( $aryData["lngActionCode"] != DEF_ACTION_DELETE && $lngLength < 1 )
{
	$aryCheckResult["aryattributecode"] = TRUE;
}


//////////////////////////////////////////////////////////////////////////
// ������ͭ����������å�
//////////////////////////////////////////////////////////////////////////
// ( ��Ͽ �ޤ��� ���� ) ���顼���ʤ� ��硢
// ������Ͽ�����������å��¹�
if ( ( $aryData["lngActionCode"] == DEF_ACTION_INSERT || $aryData["lngActionCode"] == DEF_ACTION_UPDATE ) && !join ( "", $aryCheckResult ) )
{
	// ��ҥ����ɽ�ʣ�����å�
	$strQuery = "SELECT * FROM m_Company " .
                "WHERE lngCompanyCode = " . $aryData["lngcompanycode"];

	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	// ������Ͽ ���� ��̷����0�ʾ�
	// �ޤ���
	// ���� ���� ��̷����1�ʳ� �ξ�硢���顼
	if ( ( $aryData["lngActionCode"] == DEF_ACTION_INSERT && $lngResultNum > 0 ) || ( $aryData["lngActionCode"] == DEF_ACTION_UPDATE && $lngResultNum != 1 ) )
	{
		$aryCheckResult["lngcompanycode_Error"] = 1;
		$objDB->freeResult( $lngResultID );
	}

	// °����ʣ�����å�
	$count = count ( $aryAttributeCode );
	for ( $i = 0; $i < $count; $i++ )
	{
		for ( $j = $i + 1; $j < $count; $j++ )
		{
			if ( $aryAttributeCode[$i] == $aryAttributeCode[$j] )
			{
				fncOutputError ( 9056, DEF_WARNING, "°�������ɤ���ʣ���Ƥ��ޤ���", TRUE, "", $objDB );
			}
		}
	}
}

// ��� ���� ���顼���ʤ� ��硢
// ��������å��¹�
elseif ( $aryData["lngActionCode"] == DEF_ACTION_DELETE && !join ( "", $aryCheckResult ) )
{
// �����å��оݥơ��֥�̾��������
	// ���롼�ץޥ������桼�����ޥ��� �����å�������
	$aryTableName = Array ( "m_Group", "m_User" );

	// �����å�����������
	for ( $i = 0; $i < count ( $aryTableName ); $i++ )
	{
		$aryQuery[] = "SELECT lngCompanyCode FROM " . $aryTableName[$i] . " WHERE lngCompanyCode = " . $aryData["lngcompanycode"];
	}
	// ȯ��ޥ��� �����å�����������
	$aryQuery[] = "SELECT lngCustomerCompanyCode FROM m_Order WHERE lngCustomerCompanyCode = " . $aryData["lngcompanycode"] . " OR lngDeliveryPlaceCode = " . $aryData["lngcompanycode"];

	// ���ʥޥ��� �����å�����������
	$aryQuery[] = "SELECT lngCustomerCompanyCode FROM m_Product WHERE lngCustomerCompanyCode = " . $aryData["lngcompanycode"] . " OR lngFactoryCode = " . $aryData["lngcompanycode"] . " OR lngAssemblyFactoryCode = " . $aryData["lngcompanycode"] . " OR lngAssemblyFactoryCode = " . $aryData["lngcompanycode"] . " OR lngDeliveryPlaceCode = " . $aryData["lngcompanycode"];

	// ����ޥ��� �����å�����������
	$aryQuery[] = "SELECT lngCustomerCompanyCode FROM m_Receive WHERE lngCustomerCompanyCode = " . $aryData["lngcompanycode"];

	// ���ޥ��� �����å�����������
	$aryQuery[] = "SELECT lngCustomerCompanyCode FROM m_Sales WHERE lngCustomerCompanyCode = " . $aryData["lngcompanycode"];

	// �����ޥ��� �����å�����������
	$aryQuery[] = "SELECT lngCustomerCompanyCode FROM m_Stock WHERE lngCustomerCompanyCode = " . $aryData["lngcompanycode"] . " OR lngDeliveryPlaceCode = " . $aryData["lngcompanycode"];

	$strQuery = join ( " UNION ", $aryQuery );

	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	// ��̤�1��Ǥ⤢�ä���硢����Բ�ǽ�Ȥ������顼����
	if ( $lngResultNum > 0 )
	{
		$objDB->freeResult( $lngResultID );
		fncOutputError ( 1201, DEF_WARNING, "�ޥ�����������", TRUE, "", $objDB );
	}

	// ����о�ɽ���Τ���Υǡ��������
	$strQuery = "SELECT * FROM m_Company WHERE lngCompanyCode = " . $aryData["lngcompanycode"];

	$objMaster = new clsMaster();
	$objMaster->setMasterTableData( $strQuery, $objDB );

	$aryKeys = array_keys ( $objMaster->aryData[0] );

	foreach ( $aryKeys as $strKey )
	{
		$aryData[$strKey] = $objMaster->aryData[0][$strKey];
	}

	$strQuery = "SELECT lngAttributeCode FROM m_AttributeRelation WHERE lngCompanyCode = " . $aryData["lngcompanycode"];
	$objMaster->setMasterTableData( $strQuery, $objDB );

	$lngLength = count ( $objMaster->aryData );
	for ( $i = 0; $i < $lngLength; $i++ )
	{
		$aryAttributeCode[] = $objMaster->aryData[$i]["lngattributecode"];
	}

	$objMaster = new clsMaster();
	$aryKeys = Array ();
}



// ���顼����ɽ������
list ( $aryData, $bytErrorFlag ) = getArrayErrorVisibility( $aryData, $aryCheckResult, $objDB );


// ���ɽ���ե饰����
if ( $aryData["bytcompanydisplayflag"] == "t" )
{
	$aryData["bytcompanydisplayflag"] = "TRUE";
}
else
{
	$aryData["bytcompanydisplayflag"] = "FALSE";
}

// �ȿ�ɽ���ե饰����
if ( $aryData["bytorganizationfront"] == "t" )
{
	$aryData["bytorganizationfront"] = "TRUE";
}
else
{
	$aryData["bytorganizationfront"] = "FALSE";
}

$aryCompanyDisplayFlag = Array ( "TRUE" => "ɽ��", "FALSE" => "��ɽ��" );
$aryOrganizationFront  = Array ( "TRUE" => "��", "FALSE" => "��" );


//////////////////////////////////////////////////////////////////////////
// ����
//////////////////////////////////////////////////////////////////////////
$aryParts["lngLanguageCode"] =& $_COOKIE["lngLanguageCode"];
$aryParts["lngActionCode"]   =& $aryData["lngActionCode"];
$aryParts["strSessionID"]    =& $aryData["strSessionID"];


// lngCompanyCode ��(CODE+NAME)����
$aryCompanyCode = fncGetMasterValue( "m_Company", "lngCompanyCode", "strCompanyName", "Array", "", $objDB );
// bytGroupDisplayFlag ��(CODE+NAME)����
$aryGroupDisplayFlag = Array ( "TRUE" => "ɽ��", "FALSE" => "��ɽ��" );

// ��ҥ�����
if ( $aryData["lngActionCode"] != DEF_ACTION_INSERT )
{
	$strOutputCompanyCode =& $aryData["lngcompanycode"];
}
$aryParts["MASTER"] .= "				<tr><td id=\"Column0\" class=\"SegColumn\" width=\"25%\">Column0</td><td class=\"Segs\">" . $strOutputCompanyCode . "</td></tr>\n";
$aryParts["HIDDEN"] .= "<input type=\"hidden\" name=\"lngcompanycode\" value=\"" . $aryData["lngcompanycode"] . "\">\n";

// �񥳡���
$aryParts["MASTER"] .= "				<tr><td id=\"Column1\" class=\"SegColumn\" width=\"25%\">Column0</td><td class=\"Segs\">" . fncGetMasterValue( "m_Country", "lngCountryCode", "strCountryName", $aryData["lngcountrycode"], "", $objDB ) . "</td></tr>\n";
$aryParts["HIDDEN"] .= "<input type=\"hidden\" name=\"lngcountrycode\" value=\"" . $aryData["lngcountrycode"] . "\">\n";

// �ȿ�������
$aryParts["MASTER"] .= "				<tr><td id=\"Column2\" class=\"SegColumn\" width=\"25%\">Column0</td><td class=\"Segs\">" . fncGetMasterValue( "m_Organization", "lngOrganizationCode", "strOrganizationName", $aryData["lngorganizationcode"], "", $objDB ) . "</td></tr>\n";
$aryParts["HIDDEN"] .= "<input type=\"hidden\" name=\"lngorganizationcode\" value=\"" . $aryData["lngorganizationcode"] . "\">\n";

// �ȿ�ɽ��
$aryParts["MASTER"] .= "				<tr><td id=\"Column3\" class=\"SegColumn\" width=\"25%\">Column0</td><td class=\"Segs\">" . $aryOrganizationFront[$aryData["bytorganizationfront"]] . "</td></tr>\n";
$aryParts["HIDDEN"] .= "<input type=\"hidden\" name=\"bytorganizationfront\" value=\"" . $aryData["bytorganizationfront"] . "\">\n";

// ���̾��
$aryParts["MASTER"] .= "				<tr><td id=\"Column4\" class=\"SegColumn\" width=\"25%\">Column0</td><td class=\"Segs\">" . fncHTMLSpecialChars( $aryData["strcompanyname"] ) . "</td></tr>\n";
$aryParts["HIDDEN"] .= "<input type=\"hidden\" name=\"strcompanyname\" value=\"" . fncHTMLSpecialChars ( $aryData["strcompanyname"] ) . "\">\n";

// ɽ����ҥե饰
$aryParts["MASTER"] .= "				<tr><td id=\"Column5\" class=\"SegColumn\" width=\"25%\">Column0</td><td class=\"Segs\">" . $aryCompanyDisplayFlag[$aryData["bytcompanydisplayflag"]] . "</td></tr>\n";
$aryParts["HIDDEN"] .= "<input type=\"hidden\" name=\"bytcompanydisplayflag\" value=\"" . $aryData["bytcompanydisplayflag"] . "\">\n";

// ɽ����ҥ�����
$aryParts["MASTER"] .= "				<tr><td id=\"Column6\" class=\"SegColumn\" width=\"25%\">Column0</td><td class=\"Segs\">" . fncHTMLSpecialChars ( $aryData["strcompanydisplaycode"] ) . "</td></tr>\n";
$aryParts["HIDDEN"] .= "<input type=\"hidden\" name=\"strcompanydisplaycode\" value=\"" . fncHTMLSpecialChars ( $aryData["strcompanydisplaycode"] ) . "\">\n";

// ɽ�����̾��
$aryParts["MASTER"] .= "				<tr><td id=\"Column7\" class=\"SegColumn\" width=\"25%\">Column0</td><td class=\"Segs\">" . fncHTMLSpecialChars ( $aryData["strcompanydisplayname"] ) . "</td></tr>\n";
$aryParts["HIDDEN"] .= "<input type=\"hidden\" name=\"strcompanydisplayname\" value=\"" . fncHTMLSpecialChars ( $aryData["strcompanydisplayname"] ) . "\">\n";

// ͹���ֹ�
$aryParts["MASTER"] .= "				<tr><td id=\"Column8\" class=\"SegColumn\" width=\"25%\">Column0</td><td class=\"Segs\">" . $aryData["strpostalcode"] . "</td></tr>\n";
$aryParts["HIDDEN"] .= "<input type=\"hidden\" name=\"strpostalcode\" value=\"" . $aryData["strpostalcode"] . "\">\n";

// ����1 / ��ƻ�ܸ�
$aryParts["MASTER"] .= "				<tr><td id=\"Column9\" class=\"SegColumn\" width=\"25%\">Column0</td><td class=\"Segs\">" . fncHTMLSpecialChars ( $aryData["straddress1"] ) . "</td></tr>\n";
$aryParts["HIDDEN"] .= "<input type=\"hidden\" name=\"straddress1\" value=\"" . fncHTMLSpecialChars ( $aryData["straddress1"] ) . "\">\n";

// ����2 / �ԡ��衢��
$aryParts["MASTER"] .= "				<tr><td id=\"Column10\" class=\"SegColumn\" width=\"25%\">Column0</td><td class=\"Segs\">" . fncHTMLSpecialChars ( $aryData["straddress2"] ) . "</td></tr>\n";
$aryParts["HIDDEN"] .= "<input type=\"hidden\" name=\"straddress2\" value=\"" . fncHTMLSpecialChars ( $aryData["straddress2"] ) . "\">\n";

// ����3 / Į������
$aryParts["MASTER"] .= "				<tr><td id=\"Column11\" class=\"SegColumn\" width=\"25%\">Column0</td><td class=\"Segs\">" . fncHTMLSpecialChars ( $aryData["straddress3"] ) . "</td></tr>\n";
$aryParts["HIDDEN"] .= "<input type=\"hidden\" name=\"straddress3\" value=\"" . fncHTMLSpecialChars ( $aryData["straddress3"] ) . "\">\n";

// ����4 / �ӥ�������ʪ̾
$aryParts["MASTER"] .= "				<tr><td id=\"Column12\" class=\"SegColumn\" width=\"25%\">Column0</td><td class=\"Segs\">" . fncHTMLSpecialChars ( $aryData["straddress4"] ) . "</td></tr>\n";
$aryParts["HIDDEN"] .= "<input type=\"hidden\" name=\"straddress4\" value=\"" . fncHTMLSpecialChars ( $aryData["straddress4"] ) . "\">\n";

// �����ֹ�1
$aryParts["MASTER"] .= "				<tr><td id=\"Column13\" class=\"SegColumn\" width=\"25%\">Column0</td><td class=\"Segs\">" . fncHTMLSpecialChars ( $aryData["strtel1"] ) . "</td></tr>\n";
$aryParts["HIDDEN"] .= "<input type=\"hidden\" name=\"strtel1\" value=\"" . fncHTMLSpecialChars ( $aryData["strtel1"] ) . "\">\n";

// �����ֹ�2
$aryParts["MASTER"] .= "				<tr><td id=\"Column14\" class=\"SegColumn\" width=\"25%\">Column0</td><td class=\"Segs\">" . fncHTMLSpecialChars ( $aryData["strtel2"] ) . "</td></tr>\n";
$aryParts["HIDDEN"] .= "<input type=\"hidden\" name=\"strtel2\" value=\"" . fncHTMLSpecialChars ( $aryData["strtel2"] ) . "\">\n";

// �ե��å����ֹ�1
$aryParts["MASTER"] .= "				<tr><td id=\"Column15\" class=\"SegColumn\" width=\"25%\">Column0</td><td class=\"Segs\">" . fncHTMLSpecialChars ( $aryData["strfax1"] ) . "</td></tr>\n";
$aryParts["HIDDEN"] .= "<input type=\"hidden\" name=\"strfax1\" value=\"" . fncHTMLSpecialChars ( $aryData["strfax1"] ) . "\">\n";

// �ե��å����ֹ�2
$aryParts["MASTER"] .= "				<tr><td id=\"Column16\" class=\"SegColumn\" width=\"25%\">Column0</td><td class=\"Segs\">" . fncHTMLSpecialChars ( $aryData["strfax2"] ) . "</td></tr>\n";
$aryParts["HIDDEN"] .= "<input type=\"hidden\" name=\"strfax2\" value=\"" . fncHTMLSpecialChars ( $aryData["strfax2"] ) . "\">\n";

// ���̥�����
$aryParts["MASTER"] .= "				<tr><td id=\"Column17\" class=\"SegColumn\" width=\"25%\">Column0</td><td class=\"Segs\">" . fncHTMLSpecialChars ( $aryData["strdistinctcode"] ) . "</td></tr>\n";
$aryParts["HIDDEN"] .= "<input type=\"hidden\" name=\"strdistinctcode\" value=\"" . fncHTMLSpecialChars ( $aryData["strdistinctcode"] ) . "\">\n";

// ������������
$aryParts["MASTER"] .= "				<tr><td id=\"Column18\" class=\"SegColumn\" width=\"25%\">Column0</td><td class=\"Segs\">" . fncGetMasterValue( "m_ClosedDay", "lngClosedDayCode", "strClosedDayCode || ':' || lngClosedDay", $aryData["lngcloseddaycode"], "", $objDB ) . "</td></tr>\n";
$aryParts["HIDDEN"] .= "<input type=\"hidden\" name=\"lngcloseddaycode\" value=\"" . $aryData["lngcloseddaycode"] . "\">\n";

// °��
// °�����꤬�����硢°���ץ�������˥塼�������°���ǡ���������Ԥ�
if ( $lngLength > 0 )
{
	// °���ץ�������˥塼������(���ľ����������ɬ��)
	$strQueryWhere = "WHERE lngAttributeCode = " . join ( " OR lngAttributeCode = ", $aryAttributeCode );

	// °���ǡ�������(��Ͽ��������ɬ��)
	$aryData["strattributecode"] = join ( ":", $aryAttributeCode );
}
list ( $lngResultID, $lngResultNum ) = fncQuery( "SELECT strAttributeName FROM m_Attribute " . $strQueryWhere, $objDB );
for ( $i = 0; $i < $lngResultNum; $i++ )
{
	$objResult = $objDB->fetchObject( $lngResultID, $i );
	$aryAttributeName[] = $objResult->strattributename;
}
$aryParts["MASTER"] .= "				<tr><td id=\"Column19\" class=\"SegColumn\" width=\"25%\">Column0</td><td class=\"Segs\">" . join ( " / ", $aryAttributeName ) . "</td></tr>\n";
$aryParts["HIDDEN"] .= "<input type=\"hidden\" name=\"strattributecode\" value=\"" . $aryData["strattributecode"] . "\">\n";



if ( $bytErrorFlag )
{
	//echo getArrayTable( $aryData, "TABLE" );exit;
	//echo fncGetReplacedHtml( "m/regist/edit.tmpl", $aryData, $objAuth );
	echo "<meta http-equiv=\"content-type\" content=\"text/html; charset=euc-jp\">";
	echo "<form action=\"/m/regist/co/edit.php\" method=\"POST\">";
	echo getArrayTable( $aryData, "HIDDEN" );
	echo "</form>";
	echo "<script language=\"javascript\">document.forms[0].submit();</script>";
}
else
{
	$objTemplate = new clsTemplate();
	$objTemplate->getTemplate( "m/regist/co/confirm.tmpl" );
	$objTemplate->replace( $aryParts );
	$objTemplate->complete();
	echo $objTemplate->strTemplate;
}


$objDB->close();


return TRUE;
?>


