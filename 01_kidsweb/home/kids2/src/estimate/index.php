<?
/** 
*	���Ѹ������� TOP����
*
*	@package   KIDS
*	@license   http://www.wiseknot.co.jp/ 
*	@copyright Copyright &copy; 2004, AntsBizShare 
*	@author    Kenji Chiba
*	@access    public
*	@version   1.00
*
*/
// index.php -> strSessionID    -> index.php

// �����ɤ߹���
include_once('conf.inc');

// �饤�֥���ɤ߹���
require (LIB_FILE);

$objDB   = new clsDB();
$objAuth = new clsAuth();
$objDB->open( "", "", "", "" );

if ( $_GET["strSessionID"] )
{
	$aryData["strSessionID"]    = $_GET["strSessionID"];
}
else
{
	$aryData["strSessionID"]    = $_POST["strSessionID"];
}

// ʸ��������å�
$aryCheck["strSessionID"]          = "null:numenglish(32,32)";
$aryResult = fncAllCheck( $aryData, $aryCheck );
fncPutStringCheckError( $aryResult, $objDB );

// ���å�����ǧ
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

// ���³�ǧ
if ( !fncCheckAuthority( DEF_FUNCTION_UC0, $objAuth ) )
{
	fncOutputError ( 9052, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
}

$aryData["visibility1"]			= "visible";
$aryData["visibility2"]			= "hidden";
$aryData["upload_visibility"]	= 'visible';

if ( fncCheckAuthority( DEF_FUNCTION_E2, $objAuth ) )
{
	$aryData["visibility2"]      = "visible";
}

// ���åץ���
if ( !fncCheckAuthority( DEF_FUNCTION_UP0, $objAuth ) )
{
	$aryData["upload_visibility"]	= 'hidden';
}





	// �桼���������ɼ���
	$lngUserCode = $objAuth->UserCode;

	// ���¥��롼�ץ�����(�桼�����ʲ�)�����å�
	$blnAG = fncCheckUserAuthorityGroupCode( $lngUserCode, $aryData["strSessionID"], $objDB );

	// �֥桼�����װʲ��ξ��
	if( $blnAG )
	{
		// ��ǧ�롼��¸�ߥ����å�
		$blnWF = fncCheckWorkFlowRoot( $lngUserCode, $aryData["strSessionID"], $objDB );

		// ��ǧ�롼�Ȥ�¸�ߤ��ʤ����
		if( !$blnWF )
		{
			$aryData["visibility1"] = 'hidden';
		}
		else
		{
			$aryData["visibility1"] = 'visible';
		}
	}


$objDB->close();


// �إ�ץ���ѵ�ǽ�����ɤ򥻥å�
$aryData["lngFunctionCode"] = DEF_FUNCTION_E0;

$aryData["lngFunctionCode1"] = DEF_FUNCTION_E1;

// HTML����
echo fncGetReplacedHtml( "estimate/parts.tmpl", $aryData, $objAuth );
?>
