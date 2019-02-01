<?
/** 
*	�ѥ���ɥ�ޥ���ɡ��ѥ���ɾ����ɽ�����������
*
*	@package   kuwagata
*	@license   http://www.wiseknot.co.jp/ 
*	@copyright Copyright &copy; 2003, Wiseknot 
*	@author    Keiji Suzukaze <k-suzukaze@wiseknot.co.jp> 
*	@access    public
*	@version   1.00
*
*	��������
*	�ѥ���ɥ�ޥ���������������������줿�᡼����
*	���Υץ�����ƤӽФ������󤬰۾�Ǥʤ����
*	�������ѥ���ɤ�ȯ�Ԥ���
*	���󤬰۾�Ǥ��ä���硢�۾����Ƥˤ�餺Ʊ����å�������
*	ɽ������
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

// GET�ǡ����μ���
$aryBase = $_GET;

// GET�ǡ����γ�ǧ
if ( isset($aryBase["strInfo"]) )
{
	$strSessionID = $aryBase["strInfo"];
	// ���å����ɣĤ���桼��������μ���
	$aryData = getSessionIDToInfo( $strSessionID, $objDB );
	if ( !$aryData )
	{
		fncOutputError( 9052, DEF_ERROR, "�ѥ����ȯ�ԤǤ��ޤ���", TRUE, "", $objDB );
	}

	// �������ѥ���ɤ�����
	$strNewPassword = substr( md5( uniqid( rand(), 1 ) ), 0, 10);

	// �ѥ���ɤι���
	if ( !$aryData["lngUserCode"] )
	{
//		fncOutputError( 9051, DEF_ERROR, "�桼��������ι����˼��Ԥ��ޤ�����", TRUE, "", $objDB );
	}

	if ( !setNewPassword( $aryData["lngUserCode"], $strNewPassword, $objDB ) )
	{
		fncOutputError( 9051, DEF_ERROR, "�桼��������ι����˼��Ԥ��ޤ�����", TRUE, "", $objDB );
	}

	// �������ѥ���ɾ�����̤�ɽ��
	$fp = fopen ( TMP_ROOT . "remind/passwdinfo.html", "r" );

	while ( $strTemplLine = fgets ( $fp, 1000 ) )
	{
		$strTempl .= $strTemplLine;
	}
	// �ִ�
	$strTempl = preg_replace ( "/_%strNewPassword%_/i", $strNewPassword, $strTempl );
	// ����
	echo $strTempl;

	// ���Ѥ������å��������̵����
	if ( !setSessionOff( $strSessionID, $objDB ) )
	{
		fncOutputError( 9052, DEF_ERROR, "���å����۾", TRUE, "", $objDB );
	}
}
else
{
	fncOutputError( 9052, DEF_ERROR, "���ꤵ�줿���ɥ쥹���󤬰۾�Ǥ���", TRUE, "", $objDB );
}

$objDB->close();


return TRUE;
?>
