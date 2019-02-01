<?
/** 
*	�桼�������� �������ɽ������
*
*	@package   kuwagata
*	@license   http://www.wiseknot.co.jp/ 
*	@copyright Copyright &copy; 2003, Wiseknot 
*	@author    Kenji Chiba <k-chiba@wiseknot.co.jp> 
*	@access    public
*	@version   1.00
*
*/
// -------------------------------------------------------------------------
// search.php -> strSessionID           -> index.php
// search.php -> lngFunctionCode        -> index.php

// search.php -> bytInvalidFlag         -> index.php
// search.php -> lngUserCode            -> index.php
// search.php -> strUserID              -> index.php
// search.php -> strMailAddress         -> index.php
// search.php -> bytMailTransmitFlag    -> index.php
// search.php -> strUserDisplayCode     -> index.php
// search.php -> strUserDisplayName     -> index.php
// search.php -> strUserFullName        -> index.php
// search.php -> lngCompanyCode         -> index.php
// search.php -> lngGroupCode           -> index.php
// search.php -> lngAuthorityGroupCode  -> index.php
// search.php -> lngAccessIPAddressCode -> index.php
// search.php -> strNote                -> index.php

// search.php -> bytInvalidFlagConditions         -> index.php
// search.php -> lngUserCodeConditions            -> index.php
// search.php -> strUserIDConditions              -> index.php
// search.php -> strMailAddressConditions         -> index.php
// search.php -> bytMailTransmitFlagConditions    -> index.php
// search.php -> strUserDisplayCodeConditions     -> index.php
// search.php -> strUserDisplayNameConditions     -> index.php
// search.php -> strUserFullNameConditions        -> index.php
// search.php -> lngCompanyCodeConditions         -> index.php
// search.php -> lngGroupCodeConditions           -> index.php
// search.php -> lngAuthorityGroupCodeConditions  -> index.php
// search.php -> lngAccessIPAddressCodeConditions -> index.php
// search.php -> strNoteConditions                -> index.php

// search.php -> bytInvalidFlagVisible         -> index.php
// search.php -> lngUserCodeVisible            -> index.php
// search.php -> strUserIDVisible              -> index.php
// search.php -> strMailAddressVisible         -> index.php
// search.php -> bytMailTransmitFlagVisible    -> index.php
// search.php -> strUserDisplayCodeVisible     -> index.php
// search.php -> strUserDisplayNameVisible     -> index.php
// search.php -> strUserFullNameVisible        -> index.php
// search.php -> lngCompanyCodeVisible         -> index.php
// search.php -> lngGroupCodeVisible           -> index.php
// search.php -> lngAuthorityGroupCodeVisible  -> index.php
// search.php -> lngAccessIPAddressCodeVisible -> index.php
// search.php -> strNoteVisible                -> index.php

// �����ɤ߹���
include_once('conf.inc');

// �饤�֥���ɤ߹���
require (LIB_FILE);
require (SRC_ROOT . "uc/cmn/lib_uc.php");

// DB��³
$objDB   = new clsDB();
$objAuth = new clsAuth();
$objDB->open( "", "", "", "" );

// �ǡ�������
if ( $_GET )
{
	$aryData = $_GET;
}
elseif ( $_POST )
{
	$aryData = $_POST;
}

// ���å���������쥳���ɤ����
$aryData["lngLanguageCode"] = $_COOKIE["lngLanguageCode"];

// ����ɽ�����ܼ���
if ( $lngArrayLength = count ( $aryData["ViewColumn"] ) )
{
	$aryColumn = $aryData["ViewColumn"];
	for ( $i = 0; $i < $lngArrayLength; $i++ )
	{
		$aryData[$aryColumn[$i]] = 1;
	}
	$aryData["ViewColumn"] = "";
	$aryColumn = "";
}

// ���������ܼ���
if ( $lngArrayLength = count ( $aryData["SearchColumn"] ) )
{
	$aryColumn = $aryData["SearchColumn"];
	for ( $i = 0; $i < $lngArrayLength; $i++ )
	{
		$aryData[$aryColumn[$i]] = 1;
	}
	$aryData["SearchColumn"] = "";
	$aryColumn = "";
}

$aryData = fncToHTMLString( $aryData );

//////////////////////////////////////////////////////////////////////////
// ���å���󡢸��³�ǧ
//////////////////////////////////////////////////////////////////////////
// ���å�����ǧ
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

// ���³�ǧ
if ( $aryData["lngFunctionCode"] != DEF_FUNCTION_UC3 || !fncCheckAuthority( $aryData["lngFunctionCode"], $objAuth ) )
{
	fncOutputError ( 9052, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
}

// ��������ĥե饰��NULL�ξ�������
if ( !$aryData["bytInvalidFlag"] )
{
	$aryData["bytInvalidFlag"] = "TRUE";
}

// �᡼���ۿ����ĥե饰��NULL�ξ�������
if ( !$aryData["bytMailTransmitFlag"] )
{
	$aryData["bytMailTransmitFlag"] = "FALSE";
}

// �桼����ɽ���ե饰��NULL�ξ�������
if ( !$aryData["bytUserDisplayFlag"] )
{
	$aryData["bytUserDisplayFlag"] = "FALSE";
}

// ������̤Υ����ɽ���θ�������
if ( !$aryData["lngLanguageCode"] )
{
	$aryColumnLang = Array (
		"detail"                 => "Detail",
		"bytInvalidFlag"         => "Login permission",
		"lngUserCode"            => "User code",
		"strUserID"              => "User ID",
		"bytMailTransmitFlag"    => "Email permission",
		"strMailAddress"         => "Email",
		"bytUserDisplayFlag"     => "User permission",
		"strUserDisplayCode"     => "Display user code",
		"strUserDisplayName"     => "Display user name",
		"strUserFullName"        => "User full name",
		"lngCompanyCode"         => "Company",
		"lngGroupCode"           => "Group",
		"lngAuthorityGroupCode"  => "Authority group",
		"lngAccessIPAddressCode" => "Access IP Address",
		"strNote"                => "Remark",
		"update"                 => "Fix"
	);
}
else
{
	$aryColumnLang = Array (
		"detail"                 => "�ܺ�",
		"bytInvalidFlag"         => "���������",
		"lngUserCode"            => "�桼����������",
		"strUserID"              => "�桼����ID",
		"bytMailTransmitFlag"    => "�᡼���ۿ�����",
		"strMailAddress"         => "�᡼�륢�ɥ쥹",
		"bytUserDisplayFlag"     => "�桼����ɽ��",
		"strUserDisplayCode"     => "ɽ���桼����������",
		"strUserDisplayName"     => "ɽ���桼����̾",
		"strUserFullName"        => "�ե�͡���",
		"lngCompanyCode"         => "���",
		"lngGroupCode"           => "���롼��",
		"lngAuthorityGroupCode"  => "���¥��롼��",
		"lngAccessIPAddressCode" => "��������IP���ɥ쥹",
		"strNote"                => "����",
		"update"                 => "����"
	);
}


//////////////////////////////////////////////////////////////////////////
// ʸ��������å�
//////////////////////////////////////////////////////////////////////////
$aryCheck["strSessionID"]           = "null:numenglish(32,32)";
$aryCheck["lngFunctionCode"]        = "null:number(" . DEF_FUNCTION_UC1 . "," . DEF_FUNCTION_UC3 . ")";
$aryCheck["bytInvalidFlag"]         = "english(4,5)";
$aryCheck["lngUserCode"]            = "number(0,32767)";
$aryCheck["strUserID"]              = "numenglish(0,32767)";
$aryCheck["strMailAddress"]         = "ascii(1,255)";
$aryCheck["strUserDisplayCode"]     = "numenglish(0,32767)";
$aryCheck["strUserDisplayName"]     = "length(0,120)";
$aryCheck["strUserFullName"]        = "length(0,120)";
$aryCheck["lngCompanyCode"]         = "number(0,32767)";
$aryCheck["lngGroupCode"]           = "number(0,32767)";
$aryCheck["lngAuthorityGroupCode"]  = "number(0,32767)";
$aryCheck["lngAccessIPAddressCode"] = "number(0,32767)";
$aryCheck["strNote"]                = "length(0,1000)";


// ʸ��������å�
$aryCheckResult = fncAllCheck( $aryData, $aryCheck );
fncPutStringCheckError( $aryCheckResult, $objDB );



$aryInvalidFlag      = Array ("t" => "�Ե���", "f" => "����" );
$aryMailTransmitFlag = Array ("t" => "����",   "f" => "�Ե���" );
$aryUserDisplayFlag  = Array ("t" => "ɽ��",   "f" => "��ɽ��" );


// �桼��������
// �ǡ����ɤ߹��ߡ��������ܺپ������������ؿ�
list ( $lngResultID, $lngResultNum, $baseData["strErrorMessage"] ) = getUserQuery( $objAuth->UserCode, $aryData, $objDB );

// ���̼����Ϥ�URL����(���å����ID���ڡ������Ƹ������)
$strURL = fncGetURL( $aryData );
//echo $strURL;exit;

//////////////////////////////////////////////////////////////////////////
// ��̼��������Ͻ���
//////////////////////////////////////////////////////////////////////////
// �ѡ��ĥƥ�ץ졼���ɤ߹���
$objTemplate = new clsTemplate();
$objTemplate->getTemplate( "uc/result/parts.tmpl" );




// �ơ��֥����̾�ȥ����Ƚ���
if ( $aryData["detailVisible"] )
{
	// �ܺ�
	$baseData["detail"] = "<td nowarp>" . $aryColumnLang["detail"] . "</td>";
	$lngColumnNum++;
}

if ( $aryData["bytInvalidFlagVisible"] )
{
	// ��������ĥե饰
	$baseData["column1"] = "<td id=\"Columns\" nowrap onmouseover=\"SortOn( this );\" onmouseout=\"SortOff( this );\" onclick=\"location.href='index.php?$strURL&strSort=column_1_ASC';\"><a href=\"#\">" . $aryColumnLang["bytInvalidFlag"] . "</a></td>";
	$lngColumnNum++;
}

if ( $aryData["lngUserCodeVisible"] )
{
	// �桼����������
	$baseData["column2"] = "<td id=\"Columns\" nowrap onmouseover=\"SortOn( this );\" onmouseout=\"SortOff( this );\" onclick=\"location.href='index.php?$strURL&strSort=column_2_ASC';\"><a href=\"#\">" . $aryColumnLang["lngUserCode"] . "</a></td>";
	$lngColumnNum++;
}

if ( $aryData["strUserIDVisible"] )
{
	// �桼����ID
	$baseData["column3"] = "<td id=\"Columns\" nowrap onmouseover=\"SortOn( this );\" onmouseout=\"SortOff( this );\" onclick=\"location.href='index.php?$strURL&strSort=column_3_ASC';\"><a href=\"#\">" . $aryColumnLang["strUserID"] . "</a></td>";
	$lngColumnNum++;
}

if ( $aryData["strMailAddressVisible"] )
{
	// �᡼�륢�ɥ쥹
	$baseData["column4"] = "<td id=\"Columns\" nowrap onmouseover=\"SortOn( this );\" onmouseout=\"SortOff( this );\" onclick=\"location.href='index.php?$strURL&strSort=column_4_ASC';\"><a href=\"#\">" . $aryColumnLang["strMailAddress"] . "</a></td>";
	$lngColumnNum++;
}

if ( $aryData["bytMailTransmitFlagVisible"] )
{
	// �᡼���ۿ�����
	$baseData["column5"] = "<td id=\"Columns\" nowrap onmouseover=\"SortOn( this );\" onmouseout=\"SortOff( this );\" onclick=\"location.href='index.php?$strURL&strSort=column_5_ASC';\"><a href=\"#\">" . $aryColumnLang["bytMailTransmitFlag"] . "</a></td>";
	$lngColumnNum++;
}

if ( $aryData["bytUserDisplayFlagVisible"] )
{
	// ɽ���桼�����ե饰
	$baseData["column6"] = "<td id=\"Columns\" nowrap onmouseover=\"SortOn( this );\" onmouseout=\"SortOff( this );\" onclick=\"location.href='index.php?$strURL&strSort=column_6_ASC';\"><a href=\"#\">" . $aryColumnLang["bytUserDisplayFlag"] . "</a></td>";
	$lngColumnNum++;
}

if ( $aryData["strUserDisplayCodeVisible"] )
{
	// ɽ���桼����������
	$baseData["column7"] = "<td id=\"Columns\" nowrap onmouseover=\"SortOn( this );\" onmouseout=\"SortOff( this );\" onclick=\"location.href='index.php?$strURL&strSort=column_7_ASC';\"><a href=\"#\">" . $aryColumnLang["strUserDisplayCode"] . "</a></td>";
	$lngColumnNum++;
}

if ( $aryData["strUserDisplayNameVisible"] )
{
	// ɽ���桼����̾
	$baseData["column8"] = "<td id=\"Columns\" nowrap onmouseover=\"SortOn( this );\" onmouseout=\"SortOff( this );\" onclick=\"location.href='index.php?$strURL&strSort=column_8_ASC';\"><a href=\"#\">" . $aryColumnLang["strUserDisplayName"] . "</a></td>";
	$lngColumnNum++;
}

if ( $aryData["strUserFullNameVisible"] )
{
	// �ե�͡���
	$baseData["column9"] = "<td id=\"Columns\" nowrap onmouseover=\"SortOn( this );\" onmouseout=\"SortOff( this );\" onclick=\"location.href='index.php?$strURL&strSort=column_9_ASC';\"><a href=\"#\">" . $aryColumnLang["strUserFullName"] . "</a></td>";
	$lngColumnNum++;
}

if ( $aryData["lngCompanyCodeVisible"] )
{
	// ���
	$baseData["column10"] = "<td id=\"Columns\" nowrap onmouseover=\"SortOn( this );\" onmouseout=\"SortOff( this );\" onclick=\"location.href='index.php?$strURL&strSort=column_10_ASC';\"><a href=\"#\">" . $aryColumnLang["lngCompanyCode"] . "</a></td>";
	$lngColumnNum++;
}

if ( $aryData["lngGroupCodeVisible"] )
{
	// ���롼��
	$baseData["column11"] = "<td id=\"Columns\" nowrap onmouseover=\"SortOn( this );\" onmouseout=\"SortOff( this );\" onclick=\"location.href='index.php?$strURL&strSort=column_11_ASC';\"><a href=\"#\">" . $aryColumnLang["lngGroupCode"] . "</a></td>";
	$lngColumnNum++;
}

if ( $aryData["lngAuthorityGroupCodeVisible"] )
{
	// ���¥��롼��
	$baseData["column12"] = "<td id=\"Columns\" nowrap onmouseover=\"SortOn( this );\" onmouseout=\"SortOff( this );\" onclick=\"location.href='index.php?$strURL&strSort=column_12_ASC';\"><a href=\"#\">" . $aryColumnLang["lngAuthorityGroupCode"] . "</a></td>";
	$lngColumnNum++;
}

if ( $aryData["lngAccessIPAddressCodeVisible"] )
{
	// ��������IP
	$baseData["column13"] = "<td id=\"Columns\" nowrap onmouseover=\"SortOn( this );\" onmouseout=\"SortOff( this );\" onclick=\"location.href='index.php?$strURL&strSort=column_13_ASC';\"><a href=\"#\">" . $aryColumnLang["lngAccessIPAddressCode"] . "</a></td>";
	$lngColumnNum++;
}

if ( $aryData["strNoteVisible"] )
{
	// ����
	$baseData["column14"] = "<td id=\"Columns\" nowrap onmouseover=\"SortOn( this );\" onmouseout=\"SortOff( this );\" onclick=\"location.href='index.php?$strURL&strSort=column_14_ASC';\"><a href=\"#\">" . $aryColumnLang["strNote"] . "</a></td>";
	$lngColumnNum++;
}

if ( $aryData["updateVisible"] )
{
	// ����
	$baseData["update"] = "<td nowarp>" . $aryColumnLang["update"] . "</td>";
	$lngColumnNum++;
}


// Ʊ�����ܤΥ����Ȥϵս�ˤ������
list ( $column, $lngSort, $DESC ) = split ( "_", $aryData["strSort"] );

if ( $DESC == 'ASC' )
{
	$baseData["column" . $lngSort] = ereg_replace ( "ASC", "DESC", $baseData["column" . $lngSort] );
}



// �ѡ��ĥƥ�ץ졼�ȥ��ԡ�
$strTemplate = $objTemplate->strTemplate;

$baseData["lngColumnNum"] =& $lngColumnNum;

// �ѡ��ĥƥ�ץ졼�Ȥ�������
for ( $i = 0; $i < $lngResultNum; $i++ )
{
	$objResult = $objDB->fetchObject( $lngResultID, $i );

	// Ϣ��
	$partsData["number"] = $i + 1;
	// �ܺ�URL
	if ( $aryData["detailVisible"] )
	{
		$partsData["detail"] = "<td bgcolor=\"#FFFFFF\" align=\"center\" onmouseout=\"trClickFlg='on';\" onclick=\"trClickFlg='off';fncNoSelectSomeTrColor( this, 'TD" . $lngResultNum . "_',1 );\"><a class=\"cells\" href=\"javascript:fncShowDialogCommon('/uc/result/detail.php?strSessionID=$aryData[strSessionID]&lngUserCode=" . $objResult->lngusercode . "&lngFunctionCode=" . DEF_FUNCTION_UC4 . "&lngUserCodeCondition=1' , window.form1 , 'ResultIframeCommon' , 'YES' , " . $aryData["lngLanguageCode"] . " , 'detail' );\"><img onmouseover=\"DetailOn(this);\" onmouseout=\"DetailOff(this);\" src=\"/img/type01/wf/result/detail_off_bt.gif\" width=\"15\" height=\"15\" border=\"0\" alt=\"DETAIL\"></a></td>";
	}
	// ���������
	if ( $aryData["bytInvalidFlagVisible"] )
	{
		$partsData["bytInvalidFlag"] = "<td nowrap>" . $aryInvalidFlag[$objResult->bytinvalidflag] . "</td>";
	}
	// �桼����������
	if ( $aryData["lngUserCodeVisible"] )
	{
		$partsData["lngUserCode"] = "<td nowrap>" . $objResult->lngusercode . "</td>";
	}
	// �桼����ID
	if ( $aryData["strUserIDVisible"] )
	{
		$partsData["strUserID"] = "<td nowrap>" . $objResult->struserid . "</td>";
	}
	// �᡼�륢�ɥ쥹
	if ( $aryData["strMailAddressVisible"] )
	{
		$partsData["strMailAddress"] = "<td nowrap>" . $objResult->strmailaddress . "</td>";
	}
	// �᡼���ۿ�����
	if ( $aryData["bytMailTransmitFlagVisible"] )
	{
		$partsData["bytMailTransmitFlag"] = "<td nowrap>" . $aryMailTransmitFlag[$objResult->bytmailtransmitflag] . "</td>";
	}
	// ɽ���桼�����ե饰
	if ( $aryData["bytUserDisplayFlagVisible"] )
	{
		$partsData["bytUserDisplayFlag"] = "<td nowrap>" . $aryUserDisplayFlag[$objResult->bytuserdisplayflag] . "</td>";
	}
	// ɽ���桼����������
	if ( $aryData["strUserDisplayCodeVisible"] )
	{
		$partsData["strUserDisplayCode"] = "<td nowrap>" . $objResult->struserdisplaycode . "</td>";
	}
	// ɽ���桼����̾
	if ( $aryData["strUserDisplayNameVisible"] )
	{
		$partsData["strUserDisplayName"] = "<td nowrap>" . $objResult->struserdisplayname . "</td>";
	}
	// �ե�͡���
	if ( $aryData["strUserFullNameVisible"] )
	{
		$partsData["strUserFullName"] = "<td nowrap>" . $objResult->struserfullname . "</td>";
	}
	// ���
	if ( $aryData["lngCompanyCodeVisible"] )
	{
		$partsData["strCompanyName"] = "<td nowrap>" . $objResult->strcompanyname . "</td>";
	}
	// ���롼��
	if ( $aryData["lngGroupCodeVisible"] )
	{
		$partsData["strGroupName"] = "<td nowrap>" . $objResult->strgroupname . "</td>";
	}
	// ���¥��롼��
	if ( $aryData["lngAuthorityGroupCodeVisible"] )
	{
		$partsData["strAuthorityGroupName"] = "<td nowrap>" . $objResult->strauthoritygroupname . "</td>";
	}
	// ��������IP
	if ( $aryData["lngAccessIPAddressCodeVisible"] )
	{
		$partsData["strAccessIPAddress"] = "<td nowrap>" . $objResult->straccessipaddress . "</td>";
	}
	// ����
	if ( $aryData["strNoteVisible"] )
	{
		$partsData["strNote"] = "<td nowrap>" . $objResult->strnote . "</td>";
	}
	// ����
	if ( $aryData["updateVisible"] )
	{
		$partsData["update"] = "<td bgcolor=\"#FFFFFF\" align=\"center\" onmouseout=\"trClickFlg='on';\" onclick=\"trClickFlg='off';fncNoSelectSomeTrColor( this, 'TD" . $lngResultNum . "_',1 );\"><a class=\"cells\" href=\"javascript:fncShowDialogRenew('/uc/regist/edit.php?strSessionID=$aryData[strSessionID]&lngUserCode=" . $objResult->lngusercode . "&lngFunctionCode=" . DEF_FUNCTION_UC5 . "&lngUserCodeCondition=1' , window.form1 , 'ResultIframeRenew' , 'NO' , " . $aryData["lngLanguageCode"] . " );\"><img onmouseover=\"RenewOn(this);\" onmouseout=\"RenewOff(this);\" src=\"/img/type01/cmn/seg/renew_off_bt.gif\" width=\"15\" height=\"15\" border=\"0\" alt=\"RENEW\"></a></td>";
	}

	// ���롼�ץ��顼
	$partsData["color"] = $objResult->strgroupdisplaycolor;

	// �ǡ���Ϣ������Υ���������˼���
	$objTemplate->replace( $partsData );

	// �ѡ��ĥƥ�ץ졼������
	$baseData["tabledata"] .= $objTemplate->strTemplate;
	// �ƥ�ץ졼�Ȥ����Υƥ�ץ졼�Ⱦ��֤��᤹
	$objTemplate->strTemplate = $strTemplate;
}

$objDB->freeResult( $lngResultID );

//���å����ξ����hidden�ǻ���
$baseData["strSessionID"] = $aryData["strSessionID"];

/////////�ƥ��Ȥ�������
// POST���줿�ǡ�����Hidden�ˤ����ꤹ��
unset($ary_keys);
$ary_Keys = array_keys( $aryData );
while ( list ($strKeys, $strValues ) = each ( $ary_Keys ) )
{
	if( $strValues == "ViewColumn")
	{
//		reset( $aryData["ViewColumn"] );
		for ( $i = 0; $i < count( $aryData["ViewColumn"] ); $i++ )
		{
			$aryHidden[] = "<input type='hidden' name='ViewColumn[]' value='" .$aryData["ViewColumn"][$i]. "'>";
		}
	}
	elseif( $strValues == "SearchColumn")
	{
//		reset( $aryData["SearchColumn"] );
		for ( $j = 0; $j < count( $aryData["SearchColumn"] ); $j++ )
		{
			$aryHidden[] = "<input type='hidden' name='SearchColumn[]' value='". $aryData["SearchColumn"][$j] ."'>";
		}
	}
	elseif( $strValues == "strSort" || $strValues == "strSortOrder" )
	{
		//���⤷�ʤ�
	} 
	else
	{
		$aryHidden[] = "<input type='hidden' name='". $strValues."' value='".$aryData[$strValues]."'>";
	}
}

$aryHidden[] = "<input type='hidden' name='strSort'>";
$aryHidden[] = "<input type='hidden' name='strSortOrder'>";
$strHidden = implode ("\n", $aryHidden );

$baseData["strHidden"] = $strHidden;
/////////�ƥ��Ȥ����ޤ�




// �١����ƥ�ץ졼���ɤ߹���
$objTemplate->getTemplate( "uc/result/base.tmpl" );

// �١����ƥ�ץ졼������
$objTemplate->replace( $baseData );
$objTemplate->complete();

// HTML����
echo $objTemplate->strTemplate;

$objDB->close();


return TRUE;
?>
