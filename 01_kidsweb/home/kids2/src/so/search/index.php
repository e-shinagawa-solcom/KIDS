<?php

// ----------------------------------------------------------------------------
/**
*       �������  ��������
*
*
*       @package    K.I.D.S.
*       @license    http://www.kuwagata.co.jp/
*       @copyright  KUWAGATA CO., LTD.
*       @author     K.I.D.S. Groups <info@kids-groups.com>
*       @access     public
*       @version    2.00
*
*
*       ��������
*         ����������ɽ������
*
*       ��������
*
*/
// ----------------------------------------------------------------------------



// ������ɤ߹���
include_once ( "conf.inc" );

// �饤�֥���ɤ߹���
require ( LIB_FILE );

$objDB   = new clsDB();
$objAuth = new clsAuth();
$objDB->open( "", "", "", "" );

//////////////////////////////////////////////////////////////////////////
// POST(����GET)�ǡ�������
//////////////////////////////////////////////////////////////////////////
if ( $_POST )
{
	$aryData = $_POST;
}
elseif ( $_GET )
{
	$aryData = $_GET;
}

setcookie("strSessionID", $aryData["strSessionID"], 0, "/");

// ʸ��������å�
$aryCheck["strSessionID"]   = "null:numenglish(32,32)";
$aryResult = fncAllCheck( $aryData, $aryCheck );
fncPutStringCheckError( $aryResult, $objDB );

// ���å�����ǧ
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

// 402 ��������ʼ�������
if ( !fncCheckAuthority( DEF_FUNCTION_SO2, $objAuth ) )
{
	fncOutputError ( 9052, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
}

// ʸ��������å�
$aryCheck["strSessionID"]          = "null:numenglish(32,32)";
$aryResult = fncAllCheck( $aryData, $aryCheck );
fncPutStringCheckError( $aryResult, $objDB );

// �إ���б�
$aryData["lngFunctionCode"] = DEF_FUNCTION_SO2;

// �ƥ�ץ졼���ɤ߹���
echo fncGetReplacedHtmlWithBase("search/base_search.html", "so/search/so_search.tmpl", $aryData ,$objAuth );

$objDB->close();

return true;

?>

