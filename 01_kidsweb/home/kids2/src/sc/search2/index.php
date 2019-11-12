<?php

// ----------------------------------------------------------------------------
/**
*       Ǽ�ʽ񸡺�����
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
*         ��Ǽ�ʽ�ǡ��������������
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

// 602 ����������帡����
if ( !fncCheckAuthority( DEF_FUNCTION_SC2, $objAuth ) )
{
	fncOutputError ( 9052, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
}

// 603 ����������帡���������⡼�ɡ�
if ( fncCheckAuthority( DEF_FUNCTION_SC3, $objAuth ) )
{
    $aryData["AdminSet_visibility"] = 'style="visibility: visible"';
}
else
{
    $aryData["btnInvalidVisible"] = "";
}
// �إ���б�
$aryData["lngFunctionCode"] = DEF_FUNCTION_SC2;

// ����ʬ�ץ�������˥塼 ����
$aryData["lngSalesClassCode"] = "<option value=\"\"></option>\n";
$aryData["lngSalesClassCode"] .= fncGetPulldown("m_salesclass", "lngsalesclasscode", "lngsalesclasscode, strsalesclassname", "", '', $objDB);

// �����Ƕ�ʬ�ץ�������˥塼 ����
$aryData["lngTaxClassCode"] = "<option value=\"\"></option>\n";
$aryData["lngTaxClassCode"] .= fncGetPulldown("m_taxClass", "lngTaxClassCode", "lngTaxClassCode, strtaxclassname", "", '', $objDB);


// �ƥ�ץ졼���ɤ߹���
echo fncGetReplacedHtmlWithBase("search/base_search.html", "sc/search2/sc_search.tmpl", $aryData ,$objAuth );

$objDB->close();

return true;

?>

