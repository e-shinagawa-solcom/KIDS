<?php

// ----------------------------------------------------------------------------
/**
 *       �������  �����������
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
 *         ������������̤�ɽ��
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

    if ( !$aryData["lngInvoiceNo"] )
    {
        fncOutputError ( 9061, DEF_ERROR, "�ǡ����۾�Ǥ���", TRUE, "", $objDB );
    }

    // ʸ��������å�
    $aryCheck["strSessionID"] = "null:numenglish(32,32)";
//     $aryCheck["lngInvoiceNo"] = "null:number(0,10)";

    // ���å�����ǧ
    $objAuth = fncIsSession($aryData["strSessionID"], $objAuth, $objDB);

    // cookie��SET
    if( !empty($aryData["strSessionID"]) )
        setcookie("strSessionID", $aryData["strSessionID"], 0, "/");

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


    // ����оݤ�������ֹ�Υޥ����ǡ�����ǧ
    $lngInvoiceNo = $aryData["lngInvoiceNo"];
    $lngRevisionNo = $aryData["lngRevisionNo"];

    // ����������ֹ�������ޥ���������SQLʸ�κ���
    $strQuery = fncGetInvoiceMSQL ( $lngInvoiceNo, $lngRevisionNo );

    // �ܺ٥ǡ����μ���
    list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

    if ( $lngResultNum == 1 )
    {
        $aryResult = $objDB->fetchArray( $lngResultID, 0 );
    }
    else
    {
        fncOutputError( 9061, DEF_ERROR, "�ǡ������۾�Ǥ�", TRUE, "../inv/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
    }


    $objDB->freeResult( $lngResultID );

    // *****************************************************
    //   ��������¹ԡ�Submit����
    // *****************************************************
    if( $aryData["strSubmit"] )
    {
        // --------------------------------
        //    �����ǽ���ɤ����Υ����å�
        // --------------------------------
        // ��������٤�ɳ�����ޥ�������她�ơ����������ѤߤϺ���Բ�
        if (fncSalesStatusIsClosed($lngInvoiceNo, $objDB))
        {
            MoveToErrorPage("���ѤߤΤ��ᡢ����Ǥ��ޤ���");
        }

        // --------------------------------
        //    �������
        // --------------------------------
        // �ȥ�󥶥�����󳫻�
        $objDB->transactionBegin();

        // �����ޥ�������
        if (!fncDeleteInvoice($lngInvoiceNo, $lngRevisionNo, $objDB, $objAuth))
        {
           fncOutputError ( 9051, DEF_FATAL, "���������ȼ�������ޥ�����������", TRUE, "", $objDB );
        }

        // ������ֹ��ɳ�Ť��Ƥ������ޥ�����������ֹ����ˤ���
        if (!fncUpdateInvoicenoToMSales($lngInvoiceNo, $objDB))
        {
            fncOutputError ( 9051, DEF_FATAL, "���������ȼ�����ޥ����ơ��֥��������", TRUE, "", $objDB );
        }

        // �ȥ�󥶥�����󥳥ߥå�
        $objDB->transactionCommit();

        // �����λ���̤�ɽ��
        $aryDeleteData = $aryHeadResult;
        $aryDeleteData["strAction"] = "/inv/search/index.php?strSessionID=";
        $aryDeleteData["strSessionID"] = $aryData["strSessionID"];

        // ���쥳���ɡ����ܸ�
        $aryDeleteData["lngLanguageCode"] = 1;

        // �ƥ�ץ졼���ɤ߹���
        $objTemplate = new clsTemplate();
        $objTemplate->getTemplate( "inv/result/delete_result.tmpl" );

        // �ƥ�ץ졼������
        $objTemplate->replace( $aryDeleteData );
        $objTemplate->complete();

        // HTML����
        echo $objTemplate->strTemplate;

        $objDB->close();

        return true;
    }

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
    $objTemplate->getTemplate( "inv/result/parts3.tmpl" );
//     $objTemplate->getTemplate( "sc/result2/parts_detail.tmpl" );

    // ���̥����ȥ�
    $aryNewResult['Title'] = "�����ǧ";
    // �������
    $aryNewResult['detailCount'] = count($aryDetailResult);

    $aryNewResult["strSessionID"] = $aryData["strSessionID"];
    $aryNewResult["strAction"]    = "index3.php";
    $aryNewResult["strSubmit"]    = "submit";
    $aryNewResult["strMode"] = "delete";

    // �ƥ�ץ졼������
    $objTemplate->replace( $aryNewResult );
    $objTemplate->replace( $aryHeadColumnNames_CN );
    $objTemplate->complete();

    // HTML����
    echo $objTemplate->strTemplate;

    $objDB->close();

    return true;


    // ���顼���̤ؤ�����
    function MoveToErrorPage($strMessage){

        // ���쥳���ɡ����ܸ�
        $aryHtml["lngLanguageCode"] = 1;

        // ���顼��å�����������
        $aryHtml["strErrorMessage"] = $strMessage;

        // �ƥ�ץ졼���ɤ߹���
        $objTemplate = new clsTemplate();
        $objTemplate->getTemplate( "/result/error/parts.tmpl" );

        // �ƥ�ץ졼������
        $objTemplate->replace( $aryHtml );
        $objTemplate->complete();

        // HTML����
        echo $objTemplate->strTemplate;

        exit;
    }

?>

