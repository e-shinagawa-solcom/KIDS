<?php

// ----------------------------------------------------------------------------
/**
 *       ���ʴ���  ��������
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
 *         ����������Ͽ���̤�ɽ��
 *         �����ϥ��顼�����å�
 *         ����Ͽ�ܥ��󲡲��塢��Ͽ��ǧ���̤�
 *
 *       ��������
 *
 */
// ----------------------------------------------------------------------------

//-------------------------------------------------------------------------
// �� �饤�֥��ե������ɹ�
//-------------------------------------------------------------------------
include 'conf.inc';
require LIB_FILE;
require "libsql.php";
// require_once LIB_DEBUGFILE;
require_once CLS_IMAGELO_FILE;

//-------------------------------------------------------------------------
// �� ���֥�����������
//-------------------------------------------------------------------------
$objDB = new clsDB();
$objAuth = new clsAuth();

//-------------------------------------------------------------------------
// �� DB�����ץ�
//-------------------------------------------------------------------------
$objDB->open("", "", "", "");

//-------------------------------------------------------------------------
// �� �ѥ�᡼������
//-------------------------------------------------------------------------
if ($_GET) {
    $aryData = $_GET;
} else if ($_POST) {
    $aryData = $_POST;
}

// ���å�����ǧ
$objAuth = fncIsSession($aryData["strSessionID"], $objAuth, $objDB);

$lngInputUserCode = $objAuth->UserCode;

// 300 ���ʴ���
if (!fncCheckAuthority(DEF_FUNCTION_P0, $objAuth)) {
    fncOutputError(9018, DEF_WARNING, "�����������¤�����ޤ���", true, "", $objDB);
}

// 306 ���ʴ����ʾ��ʽ�����
if (!fncCheckAuthority(DEF_FUNCTION_P6, $objAuth)) {
    fncOutputError(9018, DEF_WARNING, "�����������¤�����ޤ���", true, "", $objDB);
}

$lngProductNo = $aryData['lngProductNo'];
$lngRevisionNo = $aryData["lngRevisionNo"];

$aryQuery = array();
$aryQuery[] = "SELECT ";
$aryQuery[] = "lngproductno, ";
$aryQuery[] = "strProductCode, "; //2:���ʥ�����
$aryQuery[] = "strProductName, "; //3:����̾��
$aryQuery[] = "strProductEnglishName, "; //4:����̾��(�Ѹ�)
$aryQuery[] = "lngInChargeGroupCode, "; //5:����
$aryQuery[] = "lngInChargeUserCode, "; //6:ô����
$aryQuery[] = "lnginputusercode, "; //7:���ϼ�
$aryQuery[] = "lngDevelopUserCode, "; //8:��ȯô����
$aryQuery[] = "strGoodsCode, "; //9:���ʥ�����
$aryQuery[] = "strGoodsName, "; //10:����̾��
$aryQuery[] = "lngCustomerCompanyCode, "; //11:�ܵ�
$aryQuery[] = "lngCustomerUserCode, "; //13:�ܵ�ô���ԥ����� (NULL)
$aryQuery[] = "strCustomerUserName, "; //14:�ܵ�ô����()
$aryQuery[] = "lngPackingUnitCode, "; //15:�ٻ�ñ��(int2)
$aryQuery[] = "lngProductUnitCode, "; //16:����ñ��(int2)
$aryQuery[] = "trim(To_char(lngBoxQuantity, '9,999,999,999')) as lngBoxQuantity, "; //17:��Ȣ���ޡ�����(int4)
$aryQuery[] = "trim(To_char(lngCartonQuantity,'9,999,999,999')) as lngCartonQuantity, "; //18:�����ȥ�����(int4)
$aryQuery[] = "trim(To_char(lngProductionQuantity,'9,999,999,999')) as lngProductionQuantity, "; //19:����ͽ���()
$aryQuery[] = "lngProductionUnitCode, "; //20:����ͽ�����ñ��()
$aryQuery[] = "trim(To_char(lngFirstDeliveryQuantity,'9,999,999,999')) as lngFirstDeliveryQuantity, "; //21:���Ǽ�ʿ�(int4)
$aryQuery[] = "lngFirstDeliveryUnitCode, "; //22:���Ǽ�ʿ���ñ��()
$aryQuery[] = "lngFactoryCode, "; //23:��������()
$aryQuery[] = "lngAssemblyFactoryCode, "; //24:���å���֥깩��()
$aryQuery[] = "lngDeliveryPlaceCode, "; //25:Ǽ�ʾ��(int2)
$aryQuery[] = "To_char(dtmDeliveryLimitDate,'YYYY/MM') as dtmDeliveryLimitDate, "; //26:Ǽ�ʴ�����()
$aryQuery[] = "trim(To_char(curProductPrice, '9,999,999,990.99')) as curProductPrice, "; //27:����()
$aryQuery[] = "trim(To_char(curRetailPrice, '9,999,999,990.99')) as curRetailPrice,"; //28:����()
$aryQuery[] = "lngTargetAgeCode, "; //29:�о�ǯ��()
$aryQuery[] = "trim(To_char(lngRoyalty, '990.99')) as lngRoyalty,"; //30:�����ƥ���()
$aryQuery[] = "lngCertificateClassCode, "; //31:�ڻ�()
$aryQuery[] = "lngCopyrightCode, "; //32:�Ǹ���()
$aryQuery[] = "strCopyrightDisplayStamp, "; //33:�Ǹ�ɽ��(���)
$aryQuery[] = "strCopyrightDisplayPrint, "; //34:�Ǹ�ɽ��(����ʪ)
$aryQuery[] = "lngProductFormCode, "; //35:���ʷ���()
$aryQuery[] = "strProductComposition, "; //36:���ʹ���()
$aryQuery[] = "strAssemblyContents, "; //37:���å���֥�����()
$aryQuery[] = "strSpecificationDetails, "; //38:���;ܺ�()
$aryQuery[] = "strNote, "; //39:����
$aryQuery[] = "To_char(dtmInsertDate,'YYYY/MM/DD HH24:MI') as dtmInsertDate, "; //41:��Ͽ��
$aryQuery[] = "strcopyrightnote, "; //43:�Ǹ�������
$aryQuery[] = "lngCategoryCode, "; // ���ƥ��꡼
$aryQuery[] = "strrevisecode "; // ���Υ�����

$aryQuery[] = "FROM m_product ";
$aryQuery[] = "WHERE  bytinvalidflag = false";
$aryQuery[] = " AND lngproductno = " . $lngProductNo ."";
$aryQuery[] = " AND lngRevisionNo = " . $lngRevisionNo ."";
$strQuery = implode("\n", $aryQuery);

$objDB->freeResult($lngResultID);
if (!$lngResultID = $objDB->execute($strQuery)) {
    fncOutputError(9051, DEF_ERROR, "", true, "", $objDB);
    $objDB->close();
    return true;

}

if (!$lngResultNum = pg_Num_Rows($lngResultID)) {
    fncOutputError(303, DEF_ERROR, "", true, "", $objDB);
    $objDB->close();
    return true;
}

$aryResult = array();
$aryResult = $objDB->fetchArray($lngResultID, 0);

//-------------------------------------------------------------------------
// �������ʡפ˥�����桼������°���Ƥ��뤫�����å�
//-------------------------------------------------------------------------
$strFncFlag = "P";
$blnCheck = fncCheckInChargeProduct($aryResult["lngproductno"], $lngInputUserCode, $strFncFlag, $objDB);

// �桼�������о����ʤ�°���Ƥ��ʤ����
if (!$blnCheck) {
    fncOutputError(9060, DEF_WARNING, "", true, "", $objDB);
}

//�����ɤ����ͤ򻲾�

// ����Υ�����
$lngInchargeGroupCode = $aryResult["lnginchargegroupcode"];
if ($lngInchargeGroupCode) {
    $aryResult["lnginchargegroupcode"] = fncGetMasterValue("m_group", "lnggroupcode", "strgroupdisplaycode", $lngInchargeGroupCode, 'bytGroupDisplayFlag=true', $objDB);
    // �����̾��
    $aryResult["strinchargegroupname"] = fncGetMasterValue("m_group", "lnggroupcode", "strgroupdisplayname", $lngInchargeGroupCode, "bytgroupdisplayflag=true", $objDB);
}

// ô���ԤΥ�����
$lngUserCode = $aryResult["lnginchargeusercode"];

if ($lngUserCode) {
    $aryResult["lnginchargeusercode"] = fncGetMasterValue("m_user", "lngusercode", "struserdisplaycode", $lngUserCode, '', $objDB);
    // ô���Ԥ�̾��
    $aryResult["strinchargeusername"] = fncGetMasterValue("m_user", "lngusercode", "struserdisplayname", $lngUserCode, '', $objDB);
}

// ��ȯô���ԤΥ�����
$lngDevelopUserCode = $aryResult["lngdevelopusercode"];
if ($lngDevelopUserCode) {
    $aryResult["lngdevelopusercode"] = fncGetMasterValue("m_user", "lngusercode", "struserdisplaycode", $lngDevelopUserCode, '', $objDB);
    // ��ȯô���Ԥ�̾��
    $aryResult["strdevelopusername"] = fncGetMasterValue("m_user", "lngusercode", "struserdisplayname", $lngDevelopUserCode, '', $objDB);
}
// �ܵҤ�̾�Υ�����
$lngCustomerCompanyCode = $aryResult["lngcustomercompanycode"];
if ($lngCustomerCompanyCode) {
    $aryResult["lngcustomercompanycode"] = fncGetMasterValue("m_company", "lngcompanycode", "strcompanydisplaycode", $lngCustomerCompanyCode, '', $objDB);
    // �ܵҤ�̾��
    $aryResult["strcustomercompanyname"] = fncGetMasterValue("m_company", "lngcompanycode", "strcompanydisplayname", $lngCustomerCompanyCode, '', $objDB);
    // :�ܵҼ��̥�����
    $aryResult["strcustomerdistinctcode"] = fncGetMasterValue("m_company", "lngcompanycode", "strdistinctcode", $aryResult["lngcustomercompanycode"], '', $objDB);
}

//�������쥳����
$lngFactoryCode = $aryResult["lngfactorycode"];
if ($lngFactoryCode) {
    $aryResult["lngfactorycode"] = fncGetMasterValue("m_company", "lngcompanycode", "strcompanydisplaycode", $lngFactoryCode, '', $objDB);
    //Ǽ�ʾ���̾��
    $aryResult["strfactoryname"] = fncGetMasterValue("m_company", "lngcompanycode", "strcompanydisplayname", $lngFactoryCode, '', $objDB);
}

//���å���֥깩�쥳����
$lngAssemblyFactoryCode = $aryResult["lngassemblyfactorycode"];
if ($lngAssemblyFactoryCode) {
    $aryResult["lngassemblyfactorycode"] = fncGetMasterValue("m_company", "lngcompanycode", "strcompanydisplaycode", $lngAssemblyFactoryCode, '', $objDB);
    //���å���֥깩��
    $aryResult["strassemblyfactoryname"] = fncGetMasterValue("m_company", "lngcompanycode", "strcompanydisplayname", $lngAssemblyFactoryCode, '', $objDB);}

//Ǽ�ʾ�ꥳ����
$lngDeliveryPlaceCode = $aryResult["lngdeliveryplacecode"];
if ($lngDeliveryPlaceCode) {
    $aryResult["lngdeliveryplacecode"] = fncGetMasterValue("m_company", "lngcompanycode", "strcompanydisplaycode", $lngDeliveryPlaceCode, '', $objDB);
    //Ǽ�ʾ��
    $aryResult["strdeliveryplacename"] = fncGetMasterValue("m_company", "lngcompanycode", "strcompanydisplayname", $lngDeliveryPlaceCode, '', $objDB);
}

// �ܵ�ô����
$lngCustomerUserCode = $aryResult["lngcustomerusercode"];

if (strcmp($aryResult["lngcustomerusercode"], "") != 0) {
    $aryResult["strcustomerusercode"] = fncGetMasterValue("m_user", "lngusercode", "struserdisplaycode", $lngCustomerUserCode, '', $objDB);
    $aryResult["strcustomerusername"] = fncGetMasterValue("m_user", "lngusercode", "struserdisplayname", $lngCustomerUserCode, '', $objDB);
}

// ���;ܺ٤��ü�ʸ���Ѵ�
$aryResult["strspecificationdetails"] = fncHTMLSpecialChars($aryResult["strspecificationdetails"]);

//���ץ�����ͤ����� ==============================================================
// Ϣ������Υ���ǥå����ˤϡ���ʸ���ǻ��ꤷ�ʤ��Ȥ���

// ���ƥ��꡼
$aryResult["lngcategorycode"] = fncGetPulldownQueryExec(fncSqlqueryCategory(array(0 => $objAuth->UserCode)), $aryResult["lngcategorycode"], $objDB);
// �ٻ�ñ��
$aryResult["lngpackingunitcode"] = fncGetPulldown("m_productunit", "lngProductUnitCode", "strProductUnitName", $aryResult["lngpackingunitcode"], "WHERE bytpackingconversionflag=true", $objDB);
// ����ñ��
$aryResult["lngproductunitcode"] = fncGetPulldown("m_productunit", "lngProductUnitCode", "strProductUnitName", $aryResult["lngproductunitcode"], "WHERE bytproductconversionflag=true", $objDB);
// ����ͽ�����ñ��
$aryResult["lngproductionunitcode"] = fncGetPulldown("m_productunit", "lngProductUnitCode", "strProductUnitName", $aryResult["lngproductionunitcode"], '', $objDB);
// ���Ǽ�ʿ���ñ��
$aryResult["lngfirstdeliveryunitcode"] = fncGetPulldown("m_productunit", "lngProductUnitCode", "strProductUnitName", $aryResult["lngfirstdeliveryunitcode"], '', $objDB);
// �о�ǯ��
$aryResult["lngtargetagecode"] = fncGetPulldown("m_targetage", "lngTargetAgeCode", "strTargetAgeName", $aryResult["lngtargetagecode"], '', $objDB);
// �ڻ� �ơ��֥�ʤ�
$aryResult["lngcertificateclasscode"] = fncGetPulldown("m_CertificateClass", "lngcertificateclasscode", "strcertificateclassname", $aryResult["lngcertificateclasscode"], '', $objDB);
// �Ǹ���
$aryResult["lngcopyrightcode"] = fncGetPulldown("m_copyright", "lngcopyrightcode", "strcopyrightname", $aryResult["lngcopyrightcode"], '', $objDB);
// ���ʷ��� �ơ��֥�ʤ�
$aryResult["lngproductformcode"] = fncGetPulldown("m_productform", "lngproductformcode", "strproductformname", $aryResult["lngproductformcode"], '', $objDB);

// ���ʹԾ��� ===================================================================
$lngproductno = $aryResult["lngproductno"];
$aryQuery2[] = "SELECT lnggoodsplancode,lngrevisionno,lnggoodsplanprogresscode, ";
$aryQuery2[] = "To_char(dtmrevisiondate,'YYYY/MM/DD HH24:MI') as dtmrevisiondate ";
$aryQuery2[] = "FROM t_goodsplan WHERE lnggoodsplancode = (";
$aryQuery2[] = "SELECT max(lnggoodsplancode) FROM t_goodsplan WHERE lngproductno = ";
$aryQuery2[] = "$lngproductno )";

$strQuery2 = "";
$strQuery2 = implode("\n", $aryQuery2);

//echo "$strQuery2<br><br>";
$objDB->freeResult($lngResultID2);
if (!$lngResultID2 = $objDB->execute($strQuery2)) {
    fncOutputError(9051, DEF_ERROR, "", true, "", $objDB);
    $objDB->close();
    return true;

}

if (!$lngResultNum = pg_Num_Rows($lngResultID2)) {
    fncOutputError(303, DEF_ERROR, "", true, "", $objDB);
    $objDB->close();
    return true;
}

$aryResult2 = array();
$aryResult2 = $objDB->fetchArray($lngResultID2, 0);

// ���ʹԾ��� =============================================================
$aryResult["lngGoodsPlanProgressCode"] = fncGetPulldown("m_goodsplanprogress", "lnggoodsplanprogresscode", "strgoodsplanprogressname", $aryResult2["lnggoodsplanprogresscode"], '', $objDB);
//�����ֹ�
$aryResult["lngRevisionNo"] = $aryResult2["lngrevisionno"];
//��������
$aryResult["dtmRevisionData"] = $aryResult2["dtmrevisiondate"];
//goodsplancode
$aryResult["lnGgoodsPlanCode"] = $aryResult2["lnggoodsplancode"];
//-------------------------------------------------------------------------
// ���᡼���ե�����μ�������
//-------------------------------------------------------------------------

$objImageLo = new clsImageLo();
$strDestPath = constant("USER_IMAGE_PEDIT_TMPDIR");
// ���������ɡ����ʥ����ɡˤ��ˤ��ơ����᡼���ե��������н����ʴ�Ϣ�������ƥ�ݥ��ǥ��쥯�ȥ�˽��Ϥ�����
$objImageLo->getImageLo($objDB, $strProductCode, $strDestPath, $aryImageInfo);

// �ե�����URL
if (strcmp($aryData["strurl"], "") == 0) {
    $aryResult["strurl"] = 'renew.php?strProductCode=$strProductCode&strSessionID=$aryData["strSessionID"]';
}

$aryResult["strActionURL"] = 'renew.php?strProductCode=$strProductCode&strSessionID=$aryData["strSessionID"]';

$aryResult["strSessionID"] = $aryData["strSessionID"];
$aryResult["strProductCode"] = $aryData["strProductCode"];
$aryResult["RENEW"] = true;

// submit�ؿ�
$aryResult["lngRegistConfirm"] = 0;

// �إ���б�
$aryResult["lngFunctionCode"] = DEF_FUNCTION_P6;

/**
debug

���;ܺٲ����ե�����HIDDEN����
 */
// �Ƽ����Ѥ�����
if (is_array($aryImageInfo['strTempImageFile'])) {
    $lngImageCnt = count($aryImageInfo['strTempImageFile']);
} else {
    $lngImageCnt = 0;
}

if ($lngImageCnt) {
    for ($i = 0; $i < $lngImageCnt; $i++) {
        $aryUploadImagesHidden[] = '<input type="hidden" name="uploadimages[]" value="' . $aryImageInfo['strTempImageFile'][$i] . '" />';
    }

    // �Ƽ����Ѥ�����
    $aryResult["re_uploadimages"] = implode("\n", $aryUploadImagesHidden);
    $aryResult["re_editordir"] = '<input type="hidden" name="strTempImageDir" value="' . $aryImageInfo['strTempImageDir'][0] . '" />';
}

// �ƥ�ץ졼���ɤ߹���
echo fncGetReplacedHtmlWithBase("base_mold.html", "p/modify/p_modify.html", $aryResult ,$objAuth );

$objDB->close();
return true;
