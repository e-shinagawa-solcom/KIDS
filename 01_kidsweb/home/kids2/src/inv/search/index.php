<?php

// ----------------------------------------------------------------------------
/**
 *       �������  �������Ͽ����
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
 *         ����Ͽ�������ϲ��̤�ɽ��
 *
 *       ��������
 *
 */
// ----------------------------------------------------------------------------


    // �����ɤ߹���
    include_once('conf.inc');

    // �饤�֥���ɤ߹���
    require (LIB_FILE);
    require (SRC_ROOT . "m/cmn/lib_m.php");
    require (SRC_ROOT . "inv/cmn/lib_regist.php");

    // ���֥�����������
    $objDB   = new clsDB();
    $objAuth = new clsAuth();

    // DB�����ץ�
    $objDB->open("", "", "", "");

    // �ѥ�᡼������
    if ( $_POST )
    {
        $aryData = $_POST;
    }
    elseif ( $_GET )
    {
        $aryData = $_GET;
    }

    // ���å�����ǧ
    $objAuth = fncIsSession($aryData["strSessionID"], $objAuth, $objDB);

    // cookie��SET
    if( !empty($aryData["strSessionID"]) )
        setcookie("strSessionID", $aryData["strSessionID"], 0, "/");

    // ���å�����ǧ
    $objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );


    // ʸ��������å�
    $aryCheck["strSessionID"]   = "null:numenglish(32,32)";
    $aryResult = fncAllCheck( $aryData, $aryCheck );
    fncPutStringCheckError( $aryResult, $objDB );

    // 2200 �������
    if ( !fncCheckAuthority( DEF_FUNCTION_INV0, $objAuth ) )
    {
        fncOutputError ( 9052, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
    }

    // 2202 ����񸡺�
    if ( !fncCheckAuthority( DEF_FUNCTION_INV2, $objAuth ) )
    {
        fncOutputError ( 9052, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
    }

    // �إ���б�
    $aryData["lngFunctionCode"] = DEF_FUNCTION_INV0;

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
            $aryData["registview"] = 'hidden';
        }
        else
        {
            $aryData["registview"] = 'visible';
        }
    }


    // �ƥ�ץ졼���ɤ߹���
    echo fncGetReplacedHtmlWithBase("inv/base_inv.html", "inv/search/search.tmpl", $aryData ,$objAuth );

    $objDB->close();

    return true;

?>

