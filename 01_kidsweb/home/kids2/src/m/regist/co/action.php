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
// ��Ͽ�������¹�
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
// ����¹�
// confirm.php -> strSessionID   -> action.php
// confirm.php -> lngActionCode  -> action.php
// confirm.php -> lngcompanycode -> action.php

// �����ɤ߹���
include_once('conf.inc');

// �饤�֥���ɤ߹���
require (LIB_FILE);
require (SRC_ROOT . "m/cmn/lib_m.php");

// DB��³
$objDB   = new clsDB();
$objAuth = new clsAuth();
$objDB->open( "", "", "", "" );

// POST�ǡ�������
$aryData = $_GET;

// °�������ɤ˴ؤ�������å�(ʸ��������å����ܼҡ��ܵҥ����å�)
$aryAttributeCode = explode ( ":", $aryData["strattributecode"] );
for ( $i = 0; $i < count ( $aryAttributeCode ); $i++ )
{
	// °�����ͥ����å�
	if ( fncCheckString( $aryAttributeCode[$i], "number(0,2147483647)" ) == "" )
	{
		// �ܼҤޤ��ϸܵ�°�����ä���硢���줾��Υե饰��
		if ( $aryAttributeCode[$i] == DEF_ATTRIBUTE_HEADOFFICE )
		{
			$bytHeadOfficeFlag = TRUE;
		}
		elseif ( $aryAttributeCode[$i] == DEF_ATTRIBUTE_CLIENT )
		{
			$bytClientFlag = TRUE;
		}
	}
}
// �ܼҤȸܵ�������°������ꤵ��Ƥ�����硢���顼
if ( $bytHeadOfficeFlag && $bytClientFlag )
{
	fncOutputError ( 9056, DEF_WARNING, "�ܼҡ��ܵ�������°�����ղä��뤳�ȤϤǤ��ޤ���", TRUE, "", $objDB );
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
	$aryCheck["bytorganizationfront"]  = "english(4,5)";
	$aryCheck["strcompanyname"]        = "null:length(1,100)";
	$aryCheck["bytcompanydisplayflag"] = "english(4,5)";
	$aryCheck["strcompanydisplaycode"] = "null:numenglish(0,10)";
	$aryCheck["strcompanyomitname"] = "length(1,100)";
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
	$aryCheck["strattributecode"]      = "null";
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
//echo getArrayTable( $aryData, "TABLE" );
//exit;
fncPutStringCheckError( $aryCheckResult, $objDB );



//////////////////////////////////////////////////////////////////////////
// ������ͭ����������å�
//////////////////////////////////////////////////////////////////////////
// ( ��Ͽ �ޤ��� ���� ) ���顼���ʤ� ��硢
// ������Ͽ�����������å��¹�
if ( ( $aryData["lngActionCode"] == DEF_ACTION_INSERT || $aryData["lngActionCode"] == DEF_ACTION_UPDATE ) && !join ( $aryCheckResult ) )
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
		$objDB->freeResult( $lngResultID );
		fncOutputError ( 9056, DEF_WARNING, "��ҥ����ɤ���ʣ���Ƥ��ޤ���", TRUE, "", $objDB );
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

	// ��Ͽ����(INSERT)
	if ( $aryData["lngActionCode"] == DEF_ACTION_INSERT )
	{
		// �������󥹥ơ��֥����ҥ����ɤ����
		//$aryData["lngcompanycode"] = fncGetSequence( "m_company.lngcompanycode", $objDB );
		// ���󥯥���ȸ�Υ������󥹤�9999���ä���礵��˼���
		//if ( $aryData["lngcompanycode"] == 9999 )
		//{
		//	$aryData["lngcompanycode"] = fncGetSequence( "m_company.lngcompanycode", $objDB );
		//}

		$aryQuery[] = "INSERT INTO m_Company VALUES ( " .
                       $aryData["lngcompanycode"] . ", " .
                       $aryData["lngcountrycode"]. ", " .
                       $aryData["lngorganizationcode"] . ", " .
                       $aryData["bytorganizationfront"]. ", " .
                 "'" . $aryData["strcompanyname"]. "', " .
                       $aryData["bytcompanydisplayflag"] . ", " .
                 "'" . $aryData["strcompanydisplaycode"] . "', " .
                 "'" . $aryData["strcompanydisplayname"] . "', " .
                 "'" . $aryData["strcompanyomitname"] . "', " .
                 "'" . $aryData["strpostalcode"] . "', " .
                 "'" . $aryData["straddress1"] . "', " .
                 "'" . $aryData["straddress2"] . "', " .
                 "'" . $aryData["straddress3"] . "', " .
                 "'" . $aryData["straddress4"] . "', " .
                 "'" . $aryData["strtel1"] . "', " .
                 "'" . $aryData["strtel2"] . "', " .
                 "'" . $aryData["strfax1"] . "', " .
                 "'" . $aryData["strfax2"] . "', " .
                 "'" . $aryData["strdistinctcode"] . "', " .
                       $aryData["lngcloseddaycode"].
                    " )";

		for ( $i = 0; $i < count ( $aryAttributeCode ); $i++ )
		{
			$aryQuery[] = "INSERT INTO m_AttributeRelation VALUES ( " .
                           fncGetSequence( "m_AttributeRelation.lngAttributeRelationCode", $objDB ) . ", " .
                           $aryData["lngcompanycode"] . ", " .
                           $aryAttributeCode[$i] .
                          " )";
		}
	}

	// ��������(UPDATE)
	elseif ( $aryData["lngActionCode"] == DEF_ACTION_UPDATE )
	{
		// ��å�
		$aryQuery[] = "SELECT * FROM m_Company WHERE lngcompanycode = " . $aryData["lngcompanycode"];

		// UPDATE ������
		$aryQuery[] = "UPDATE m_Company SET " .
                       "lngcountrycode = " . $aryData["lngcountrycode"]. ", " .
                       "lngorganizationcode = " . $aryData["lngorganizationcode"] . ", " .
                       "bytorganizationfront = " . $aryData["bytorganizationfront"]. ", " .
                       "strcompanyname = '" . $aryData["strcompanyname"]. "', " .
                       "bytcompanydisplayflag = " . $aryData["bytcompanydisplayflag"] . ", " .
                       "strcompanydisplaycode = '" . $aryData["strcompanydisplaycode"] . "', " .
                       "strcompanydisplayname = '" . $aryData["strcompanydisplayname"] . "', " .
                       "strcompanyomitname = '" . $aryData["strcompanyomitname"] . "', " .
                       "strpostalcode = '" . $aryData["strpostalcode"] . "', " .
                       "straddress1 = '" . $aryData["straddress1"] . "', " .
                       "straddress2 = '" . $aryData["straddress2"] . "', " .
                       "straddress3 = '" . $aryData["straddress3"] . "', " .
                       "straddress4 = '" . $aryData["straddress4"] . "', " .
                       "strtel1 = '" . $aryData["strtel1"] . "', " .
                       "strtel2 = '" . $aryData["strtel2"] . "', " .
                       "strfax1 = '" . $aryData["strfax1"] . "', " .
                       "strfax2 = '" . $aryData["strfax2"] . "', " .
                       "strdistinctcode = '" . $aryData["strdistinctcode"] . "', " .
                       "lngcloseddaycode = " . $aryData["lngcloseddaycode"] .
                       " WHERE lngcompanycode = " . $aryData["lngcompanycode"];

		// °�����ѹ������å�(�ѹ��Τ��ä����Τߡ��ѹ�����������)
		// ������°�������
		$strQuery = "SELECT lngAttributeCode FROM m_AttributeRelation WHERE lngCompanyCode = " . $aryData["lngcompanycode"];
		$objAttribute = new clsMaster();
		$objAttribute->setMasterTableData( $strQuery, $objDB );

		// ������Ͽ���줿°���򥳥ԡ�
		$aryAttributeCopy = $aryAttributeCode;

		// ���줾��ο������
		$countDB  = count ( $objAttribute->aryData );
		$countGET = count ( $aryAttributeCode );

		// °���θ����Ƚ��������
		for ( $i = 0; $i < $countDB; $i++ )
		{
			for ( $j = 0; $j < $countGET; $j++ )
			{
				// Ʊ��°����¸�ߤ�����硢
				// �����å��ե饰�򵶡�������Ͽʬ���������롼�פ�ȴ����
				if ( $objAttribute->aryData[$i]["lngattributecode"] == $aryAttributeCopy[$j] )
				{
					$bytCheckFlag = FALSE;
					$aryAttributeCopy[$j] = "";
					break;
				}
				$bytCheckFlag = TRUE;
			}

			// �����å��ե饰�����ξ�硢���°����¸�ߤ���Ȥ������ȤʤΤǡ�
			// °�����������������ե饰�򿿤Ȥ����롼�פ�ȴ����
			if ( $bytCheckFlag )
			{
				$bytAttributeChangeFlag = TRUE;
				break;
			}
		}

		// ���Ϥ��줿°���Υ��ԡ������ʸ����Ȥ��Ʒ�礷����̡�
		// �ͤ�¸�ߤ�����硢�ɲä��줿°����¸�ߤ���Ȥ������ȤʤΤǡ�
		// °�����������������ե饰�򿿤Ȥ���
		if ( join ( "", $aryAttributeCopy ) )
		{
			$bytAttributeChangeFlag = TRUE;
		}

		// °�����������������ե饰�����ξ�硢°�����������������
		if ( $bytAttributeChangeFlag )
		{
			$aryQuery[] = "DELETE FROM m_AttributeRelation WHERE lngCompanyCode = " . $aryData["lngcompanycode"];

			for ( $i = 0; $i < $countGET; $i++ )
			{
				$aryQuery[] = "INSERT INTO m_AttributeRelation VALUES ( " .
                               fncGetSequence( "m_AttributeRelation.lngAttributeRelationCode", $objDB ) . ", " .
                               $aryData["lngcompanycode"] . ", " .
                               $aryAttributeCode[$i] .
                              " )";
			}
		}
	}
}

// ��� ���� ���顼���ʤ� ��硢
// ��������å��¹�
elseif ( $aryData["lngActionCode"] == DEF_ACTION_DELETE && !join ( $aryCheckResult ) )
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
	// if ( $lngResultNum > 0 )
	// {
	// 	$objDB->freeResult( $lngResultID );
	// 	fncOutputError ( 1201, DEF_WARNING, "�ޥ�����������", TRUE, "", $objDB );
	// }

	// �������(DELETE)
	$aryQuery[] = "DELETE FROM m_Company WHERE lngCompanyCode = " . $aryData["lngcompanycode"];
	$aryQuery[] = "DELETE FROM m_AttributeRelation WHERE lngCompanyCode = " . $aryData["lngcompanycode"];
}



////////////////////////////////////////////////////////////////////////////
// ������¹�
////////////////////////////////////////////////////////////////////////////
$objDB->transactionBegin();

for ( $i = 0; $i < count ( $aryQuery ); $i++ )
{
	echo "<p>$aryQuery[$i]</p>\n";
	list ( $lngResultID, $lngResultNum ) = fncQuery( $aryQuery[$i], $objDB );
}

$objDB->transactionCommit();


$objDB->close();



//////////////////////////////////////////////////////////////////////////
// ����
//////////////////////////////////////////////////////////////////////////
?>
<html>
<body>
<script language="javascript">window.returnValue=true;window.open('about:blank','_parent').close();
</script>
</body>
</html>
<?


return TRUE;
?>


