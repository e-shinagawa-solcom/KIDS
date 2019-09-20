<?php

// ----------------------------------------------------------------------------
/**
 *       �������  �����ܺٳ�ǧ����
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
 *         �������ܺٳ�ǧ���̤�ɽ��
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
    require (SRC_ROOT . "inv/cmn/column.php");

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

    // cookie��SET
    if( !empty($aryData["strSessionID"]) )
        setcookie("strSessionID", $aryData["strSessionID"], 0, "/");

    // ʸ��������å�
    $aryCheck["strSessionID"] = "null:numenglish(32,32)";
    $aryCheck["lngInvoiceNo"] = "null:number(0,10)";

    // ���å�����ǧ
    $objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

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

    //�ܺٲ��̤�ɽ��
    $lngInvoiceNo = $aryData["lngInvoiceNo"];

    // ����������ֹ�������ޥ���������SQLʸ�κ���
    $strQuery = fncGetInvoiceMSQL ( $lngInvoiceNo );

    // �ܺ٥ǡ����μ���
    list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

    if ( $lngResultNum )
    {
        if ( $lngResultNum == 1 )
        {
            $aryResult = $objDB->fetchArray( $lngResultID, 0 );
        }
        else
        {
            fncOutputError( 603, DEF_ERROR, "�����ǡ����μ����˼��Ԥ��ޤ���", TRUE, "../inv/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
        }
    }
    else
    {
        fncOutputError( 603, DEF_ERROR, "�ǡ������۾�Ǥ�", TRUE, "../inv/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
    }

    $objDB->freeResult( $lngResultID );

    // �����ǡ�����ɽ���Ѥ�����
    // �ܵ�̾���ܵҼ�̾���ܵҥ�����
    list ($aryResult['printCustomerName'], $aryResult['printCompanyName'], $aryResult['lngCustomerCodeForCompaany']) = fncGetCompanyPrintName( $aryResult['strcustomercode'] ,$objDB);

    $aryNewResult = fncSetInvoiceHeadTableData ( $aryResult );

    // �إå����Υ����̾������ʥ�����Ƭ��"CN"����Ϳ�����
    $aryHeadColumnNames_CN = fncAddColumnNameArrayKeyToCN ( $aryHeadColumnNames );
    // �������Υ����̾������ʥ�����Ƭ��"CN"����Ϳ�����
    $aryDetailColumnNames_CN = fncAddColumnNameArrayKeyToCN ( $aryDetailColumnNames );

    // ��������٥ǡ�������
    $strQuery = fncGetSearchInvoiceDetailSQL($lngInvoiceNo , $aryResult['lngrevisionno']);

    // ���٥ǡ����μ���
    list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

    if ( $lngResultNum )
    {
        for ( $i = 0; $i < $lngResultNum; $i++ )
        {
            $aryDetailResult[] = $objDB->fetchArray( $lngResultID, $i );
        }
    }
    else
    {
        $strMessage = fncOutputError( 603, DEF_WARNING, "������ֹ���Ф������پ��󤬸��Ĥ���ޤ���", FALSE, "../inv/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
    }

    $objDB->freeResult( $lngResultID );

    for ( $i = 0; $i < count($aryDetailResult); $i++)
    {
        $aryNewDetailResult[$i] = fncSetInvoiceDetailTableData ( $aryDetailResult[$i], $aryNewResult );

        // �ƥ�ץ졼���ɤ߹���
        $objTemplate = new clsTemplate();
        $objTemplate->getTemplate( "inv/result/parts_detail.tmpl" );

        // �ƥ�ץ졼������
        $objTemplate->replace( $aryDetailColumnNames_CN );
        $objTemplate->replace( $aryNewDetailResult[$i] );
        $objTemplate->complete();

        // HTML����
        $aryDetailTable[] = $objTemplate->strTemplate;
    }

    $aryNewResult["strDetailTable"] = implode ("\n", $aryDetailTable );

    // �ƥ�ץ졼���ɤ߹���
    $objTemplate = new clsTemplate();
    $objTemplate->getTemplate( "inv/result/parts2.tmpl" );

    // ���̥����ȥ�
    $aryNewResult['Title'] = "�ܺٳ�ǧ";
    // �������
    $aryNewResult['detailCount'] = count($aryDetailResult);
    $aryNewResult["strAction"] = "index2.php";

    // �ƥ�ץ졼������
    $objTemplate->replace( $aryNewResult );
    $objTemplate->replace( $aryHeadColumnNames_CN );
    $objTemplate->complete();

    // HTML����
    echo $objTemplate->strTemplate;

    $objDB->close();

    return true;

?>

