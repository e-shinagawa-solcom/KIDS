<?php

// ----------------------------------------------------------------------------
/**
*       ȯ�����  ȯ��񸡺�����
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
require(SRC_ROOT."po/cmn/lib_po.php");

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

// ���¥����å�
// 500	ȯ�����
if ( !fncCheckAuthority( DEF_FUNCTION_PO0, $objAuth ) )
{
    fncOutputError ( 9052, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
}

// 501 ȯ�������ȯ����Ͽ��
if ( fncCheckAuthority( DEF_FUNCTION_PO1, $objAuth ) )
{
	$aryData["strRegistURL"]   = "regist/index.php?strSessionID=" . $aryData["strSessionID"];
}

// 502 ȯ�������ȯ������
if ( fncCheckAuthority( DEF_FUNCTION_PO2, $objAuth ) )
{
	$aryData["strSearchURL"]   = "search/index.php?strSessionID=" . $aryData["strSessionID"];
}

// 503 ȯ�������ȯ�����������⡼�ɡ�
if ( fncCheckAuthority( DEF_FUNCTION_PO3, $objAuth ) )
{
	$aryData["AdminSet_visibility"] = "visible";
	// 507 ȯ�������̵������
	if ( fncCheckAuthority( DEF_FUNCTION_PO7, $objAuth ) )
	{
		$aryData["btnInvalid_visibility"] = "visible";
		$aryData["btnInvalidVisible"] = "disabled";
	}
	else
	{
		$aryData["btnInvalid_visibility"] = "hidden";
		$aryData["btnInvalidVisible"] = "disabled";
	}
}
else
{
	$aryData["AdminSet_visibility"] = "hidden";
	$aryData["btnInvalid_visibility"] = "hidden";
	$aryData["btnInvalidVisible"] = "";
}
// 504 ȯ������ʾܺ�ɽ����
if ( fncCheckAuthority( DEF_FUNCTION_PO4, $objAuth ) )
{
	$aryData["btnDetail_visibility"] = "visible";
	$aryData["btnDetailVisible"] = "checked";
}
else
{
	$aryData["btnDetail_visibility"] = "hidden";
	$aryData["btnDetailVisible"] = "";
}

// ��ʧ���
$aryData["lngPayConditionCode"] = fncGetPulldown("m_paycondition", "lngpayconditioncode", "strpayconditionname", 0, '', $objDB);

// �̲�
$aryData["lngMonetaryunitCode"] = fncGetPulldown("m_monetaryunit", "lngmonetaryunitcode", "strmonetaryunitname", 0, '', $objDB);
// �̲ߥ졼��
$aryData["lngMonetaryrateCode"] = fncGetPulldown("m_monetaryrate", "lngmonetaryunitcode || '-' || lngmonetaryratecode", "lngmonetaryratecode, curconversionrate", 0, '', $objDB);
// �̲ߥ졼��������
$TmpAry = explode("\n",$aryData["lngMonetaryrateCode"]);

foreach($TmpAry as $key => $value) {
	if ($value) {
		$ValuePosS = 15;
		$ValuePosE = mb_strpos($value, ">", $ValuePosS) -1;
		$DispPosS = $ValuePosE + 2;
		$DispPosE = mb_strpos($value, "OPTION", $DispPosS) - 2;
		if (array_key_exists('lngMonetaryRateCodeValue', $aryData)) {
			$aryData["lngMonetaryRateCodeValue"] 	= $aryData["lngMonetaryRateCodeValue"] . ",," . substr($value,$ValuePosS,$ValuePosE - $ValuePosS);
			$aryData["curConversionRate"] 	= $aryData["curConversionRate"] . ",," . mb_ereg_replace("</OPTION>","",substr($value,$DispPosS));
		}
		else
		{
			$aryData["lngMonetaryRateCodeValue"] 	= substr($value,$ValuePosS,$ValuePosE - $ValuePosS);
			$aryData["curConversionRate"] 	= mb_ereg_replace("</OPTION>","",substr($value,$DispPosS));
		}
	}
}

//���ץ������ꥹ�Ȥμ����˼��Ԥ�����票�顼ɽ��
if ( !$aryData["lngMonetaryunitCode"] or !$aryData["lngMonetaryrateCode"] )
{
	fncOutputError ( 9055, DEF_WARNING, "�����ƥ�����Ԥˤ��䤤��碌��������", TRUE, "", $objDB );
}

$aryData["lngMonetaryRateCodeValue"]	= "<input type=\"hidden\" name=\"lngMonetaryRateCodeValue\" value=\"" . $aryData["lngMonetaryRateCodeValue"] . "\"></option>";
$aryData["curConversionRate"]	= mb_convert_encoding("<input type=\"hidden\" name=\"curConversionRate\" value=\"" . $aryData["curConversionRate"] . "\"></option>","EUC-JP","ASCII,JIS,UTF-8,EUC-JP,SJIS");

// ʸ��������å�
$aryCheck["strSessionID"]          = "null:numenglish(32,32)";
$aryResult = fncAllCheck( $aryData, $aryCheck );
fncPutStringCheckError( $aryResult, $objDB );

// �إ���б�
$aryData["lngFunctionCode"] = DEF_FUNCTION_PO2;

// �ƥ�ץ졼���ɤ߹���
echo fncGetReplacedHtmlWithBase("search/base_search.html", "po/search2/po_search.tmpl", $aryData ,$objAuth );

$objDB->close();

return true;

?>
