<?php
/** 
*	���顼����ɽ��������
*
*	���顼���̤�ɽ������
*
*	@package   kuwagata
*	@license   http://www.wiseknot.co.jp/ 
*	@copyright Copyright &copy; 2003, Wiseknot 
*	@author    Keiji Suzukaze <k-suzukaze@wiseknot.co.jp> 
*	@access    public
*	@version   1.00
*
*	��������
*	�����ƥ�Ū���顼�ξ��˥��顼���̤�ɽ��������
*
*	��������
*
*	2004.02.26	���顼ȯ���������Υ��ɥ쥹��LOGIN_URL������Ѥ��ʤ��褦�˽���
*
*
*
*/

// ���顼����ɽ������

// ������ɤ߹���
include_once ( "conf.inc" );

// �饤�֥���ɤ߹���
require ( LIB_FILE );

// GET�ǡ����μ���
$aryData["ref"]				= $_GET["ref"];
$aryData["path"]			= $_GET["path"];
$aryData["strMessage"]		= $_GET["strMessage"];
// COOKIE������쥳���ɤ����
$aryData["lngLanguageCode"] = $_COOKIE[lngLanguageCode];
if ( $aryData["lngLanguageCode"] == "" )
{
	$aryData["lngLanguageCode"] = 0;
}

// �ƥ�ץ졼�ȥ��֥�����������
$objTemplate = new clsTemplate();
// $objTemplate->getTemplate( "error/parts.tmpl" );
$objTemplate->getTemplate( "error/index.html" );

// echo "ref = " . $aryData["ref"] . "<br>";
// echo "path = " . $aryData["path"] . "<br>";

if ( $aryData["ref"] == TOP_URL )
{
	if ( $aryData["path"] != "" )
	{
		$aryData["strEvent"] = TOP_URL . $aryData["path"];
//		$aryData["strEvent"] = "history.back()";
	}
	else
	{
// 2004.02.26 suzukaze update start
		$aryData["strEvent"] = TOP_URL . "login/login.php?value=kids";
// 2004.02.26 suzukaze update end
	}
//	$aryData["strEvent"]     = "javascript:top.location.href='" . LOGIN_URL . "';";
	$aryData["strEventText"] = "���";
	$aryData["lngEventCode"] = DEF_ERROR_BACK;
}
else
{
//	$aryData["strEvent"]     = "window.close();";
	$aryData["strEvent"]     = "close";
	$aryData["strEventText"] = "�Ĥ���";
	$aryData["lngEventCode"] = DEF_ERROR_CLOSE;
}

// �֤�����
$objTemplate->replace( $aryData );
$objTemplate->complete();


echo $objTemplate->strTemplate;

?>

<script language=javascript>

function fncShowHidePreload( strMode )
{
	// ɽ��
	if( strMode == 0 )
	{
		if( typeof(parent.Preload) != 'undefined' )
		{
			parent.Preload.style.visibility = 'visible';
		}
	}

	// ��ɽ��
	else if( strMode == 1 )
	{
		if( typeof(parent.Preload) != 'undefined' )
		{
			parent.Preload.style.visibility = 'hidden';
		}
	}

	return false;
}

if( typeof(fncShowHidePreload) != 'undefined' )
{
	fncShowHidePreload( 1 );
}

</script>

<?

return TRUE;
?>
