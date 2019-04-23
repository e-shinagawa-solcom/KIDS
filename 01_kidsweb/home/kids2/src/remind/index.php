<?
/** 
*	�ѥ���ɥ�ޥ���ɡ�����
*
*	�᡼�륢�ɥ쥹����ѥ���ɾ���ڡ����ؤΥ᡼������
*
*	@package   kuwagata
*	@license   http://www.wiseknot.co.jp/ 
*	@copyright Copyright &copy; 2003, Wiseknot 
*	@author    Keiji Suzukaze <k-suzukaze@wiseknot.co.jp> 
*	@access    public
*	@version   1.00
*
*	��������
*	�ѥ���ɥ�ޥ�������Υ�󥯤��᡼�륢�ɥ쥹���ϲ��̤�ɽ����
*	���β��̤�ꡢ�᡼�륢�ɥ쥹��������������å�����
*	���ν�����Ǥϥ��顼���̤�ɽ���������������Ƥ⼺�Ԥ��Ƥ�
*	Ʊ�����̤�ɽ������
*	�����å���OK�Ǥ���С��ѥ���ɾ��󥢥ɥ쥹�����ꤷ��
*	�᡼�����������
*
*	��������
*	2004.02.26	�᡼��˵��ܤ��륢�ɥ쥹��ʬ���������
*	2004.06.01	DB��Ͽ���������ͤʤ���ʬ��Ƚ�ǽ�������
*
*/

// ������ɤ߹���
include_once ( "conf.inc" );

// �饤�֥���ɤ߹���
require ( LIB_FILE );
require ( SRC_ROOT . "remind/reminder.php" );

$objDB   = new clsDB();
$objAuth = new clsAuth();
$objDB->open( "", "", "", "" );

// POST�ǡ����μ���
$aryBase = $_POST;

// �����ե饰������
$bytSuccessFlag = TRUE;

// �������Ƥγ�ǧ������ӡ��᡼����������μ���
if ( !$aryBase["strMailAddress"] )
{
	// ���顼�Ǥ������å�������ɽ��
	require ( SRC_ROOT . 'remind/index.html' );
	$objDB->close();
	exit;
}

// ʸ��������å�
$aryCheck["strMailAddress"]      = "null:email(0,50)";

// �ѿ�̾�Ȥʤ륭�������
$aryKey = array_keys( $aryCheck );
$flag = TRUE;
// �����ο����������å�
foreach ( $aryKey as $strKey )
{
	// $aryData[$strKey]  : �����å��оݥǡ���
	// $aryCheck[$strKey] : �����å�����(���͡��ѿ���������������)
	$strResult = fncCheckString( $aryBase[$strKey], $aryCheck[$strKey] );
	if ( $strResult ) 
	{
		list ( $lngErrorNo, $strErrorMessage ) = explode ( ":", $strResult );
//			fncOutputError ( $lngErrorNo, DEF_ERROR, $strErrorMessage, FALSE, "", $objDB );
		$flag = FALSE;
	}
}

// ʸ��������å����顼
if ( !$flag )
{
	$bytSuccessFlag = FALSE;
}

// �桼��������μ���
if ( $bytSuccessFlag )
{
	if ( !$aryBase["strMailAddress"] or !$aryData = getMailAddressToInfo( $aryBase["strMailAddress"], $objDB ) )
	{
		$bytSuccessFlag = FALSE;
		$aryData["lngUserCode"] = 0;
	}
}

// �᡼���ۿ����ġ��᡼�륢�ɥ쥹�Υ����å�
if ( $bytSuccessFlag )
{
	if ( !$aryData["bytMailTransmitFlag"] || $aryData["strMailAddress"] == "" || $aryData["bytInvalidFlag"] )
	{
		$bytSuccessFlag = FALSE;
	}
}

// �����桼�����Υ��ɥ쥹�μ���
if ( $bytSuccessFlag ) 
{
	$aryData["strAdminAddress"] = fncGetAdminFunction( "adminmailaddress", $objDB );
	if ( !$aryData["strAdminAddress"] )
	{
		$bytSuccessFlag = FALSE;
	}
}

// ��ޥ�����������Τ����conf.inc�ˤ����ꤵ��Ƥ��������ͭ���ʥ��å����κ���
// ���å����ID����
$strSessionID = md5 ( uniqid ( rand(), 1 ) );

// ��������IP���ɥ쥹�����å�
if ( $bytSuccessFlag )
{
	if ( !checkAccessIPSimple( $objDB, $objAuth ) )
	{
		$bytSuccessFlag = FALSE;
	}
}

if ( $bytSuccessFlag )
{
	$SuccessFlag = "TRUE";
}
else
{
	$SuccessFlag = "FALSE";
}

// 2004.06.01 suzukaze update start
if ( $aryData["lngUserCode"] == "" )
{
	$aryData["lngUserCode"] = "null";
}
// 2004.06.01 suzukaze update end

// �����󥻥å��������ơ��֥�˽񤭹���
$strQuery = "INSERT INTO t_LoginSession VALUES (" .
            " '" . $strSessionID . "', " . $aryData["lngUserCode"] . ", '" . $aryData["strUserID"] . "', '" . $aryBase["strMailAddress"] .
			"', now(), '" . $objAuth->AccessIP . "', " . $SuccessFlag . ")";

list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

if ( !$objDB->freeResult( $lngResultID ) )
{
	$bytSuccessFlag = FALSE;
}

// ��ޥ�������������ɥ쥹�κ���
if ( $bytSuccessFlag )
{
// 2004.02.26 suzukaze update start
	$aryData["strURL"] = TOP_URL . 'remind/passwdinfo.php?strInfo=' . $strSessionID;
// 2004.02.26 suzukaze update end
	// �᡼������μ������ִ�
	if ( list( $strSubject, $strBody ) = fncGetMailMessage( DEF_FUNCTION_LOGIN2, $aryData, $objDB ) )
	{
		// �������Ƥ��ϣˤʤ�Х᡼�������
		$strFromMail = fncGetCommonFunction( "adminmailaddress", "m_adminfunction", $objDB );
		mail( $aryData["strMailAddress"], $strSubject, $strBody, "From: $strFromMail\nReturn-Path: " . ERROR_MAIL_TO . "\n" );
	}
}

// ���顼�Ǥ�����Ǥ������ɥ�å�������ɽ��
require ( SRC_ROOT . 'remind/confirm.html' );

$objDB->close();

return TRUE;
?>
