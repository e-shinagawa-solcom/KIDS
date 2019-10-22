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
 *         ����������������ϲ��̤�ɽ��
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

    // ���ꥨ�顼��å����� ToDo DB��Ͽ
    define ("ERROR_NO_1", '' );

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


    // ���å�����ǧ
    $objAuth = fncIsSession($aryData["strSessionID"], $objAuth, $objDB);

    // cookie��SET
    if( !empty($aryData["strSessionID"]) )
        setcookie("strSessionID", $aryData["strSessionID"], 0, "/");

    // ���å�����ǧ
    $objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );


    // ʸ��������å�
    $aryCheck["strSessionID"] = "null:numenglish(32,32)";
//     $aryCheck["lngInvoiceNo"] = "null:number(0,10)";

    // 2200 �������
    if ( !fncCheckAuthority( DEF_FUNCTION_INV0, $objAuth ) )
    {
        fncOutputError ( 9052, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
    }

    // 2201 �����ȯ��
    if ( !fncCheckAuthority( DEF_FUNCTION_INV1, $objAuth ) )
    {
        fncOutputError ( 9052, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
    }

    // �إ���б�
    $aryData["lngFunctionCode"] = DEF_FUNCTION_INV0;

    // �桼���������ɼ���
    $lngUserCode = $objAuth->UserCode;


    // ����������ֹ�������ޥ���������SQLʸ�κ���
    $lngInvoiceNo = $aryData["lngInvoiceNo"];
    $strQuery     = fncGetInvoiceMSQL ( $lngInvoiceNo );

    // �ܺ٥ǡ����μ���
    list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

    if ( $lngResultNum == 1 )
    {
        $aryResult = $objDB->fetchArray( $lngResultID, 0 );
    }
    else
    {
        fncOutputError( 9061, DEF_ERROR, "�ǡ������۾�Ǥ�", TRUE, "../inv/regist/renew.php?strSessionID=".$aryData["strSessionID"], $objDB );
    }

    $aryNewResult = fncSetInvoiceHeadTableData($aryResult);
    $aryNewResult['lngInvoiceNo'] = $aryData["lngInvoiceNo"];
    $aryNewResult['strSessionID'] = $aryData["strSessionID"];
    $aryNewResult['actionName']   = 'renew.php';

    // preview����
    if(isset($aryData["strMode"]) && $aryData["strMode"] == 'renewPrev')
    {
        $aryNewResult['strMode']      = 'insertRenew';

        $aryPrevResult = array_merge($aryNewResult, fncSetPreviewTableData($aryData, $lngInvoiceNo, $objDB));
        // �ƥ�ץ졼���ɤ߹���
        $objTemplate = new clsTemplate ();
        $objTemplate->getTemplate ("inv/base_preview.html");


        // �ץ졼���ۥ�����ִ�
        // mb_convert_variables("utf8", "eucjp-win", $recordMoldReport);
        $objTemplate->replace($aryPrevResult);
        $objTemplate->complete();

        $doc = new DOMDocument();

        // �ѡ������顼����
        libxml_use_internal_errors(true);
        // DOM�ѡ���
        $doc->loadHTML($objTemplate->strTemplate);
        // �ѡ������顼���ꥢ
        libxml_clear_errors();
        // �ѡ������顼�������
        libxml_use_internal_errors(false);
        // ���̽���
        // header("Content-type: text/html; charset=utf-8");
        $out = $doc->saveHTML();
        echo $out;
        return true;

    }
    elseif(isset($aryData["strMode"]) && $aryData["strMode"] == 'insertRenew')
    {
        // *****************************************************
        //   UPDATE�����¹ԡ�Submit����
        // *****************************************************

        // --------------------------------
        //    ������ǽ���ɤ����Υ����å�
        // --------------------------------
        // ��������٤�ɳ�����ޥ�������她�ơ����������Ѥߤ��Բ�
        if (fncSalesStatusIsClosed($lngInvoiceNo, $objDB))
        {
            MoveToErrorPage("���ѤߤΤ��ᡢ�����Ǥ��ޤ���");
        }

        // DB��Ͽ�ΰ٤Υǡ���������֤�
        $insertData = fncInvoiceInsertReturnArray($aryData, $aryResult, $objAuth, $objDB);

        // �������٤�1���ʤ����
        $slipCodeArray = $insertData['slipCodeArray'];
        if(count($slipCodeArray) < 0)
        {
            MoveToErrorPage("�������٤����򤵤�Ƥ��ޤ���");
        }

        for( $i=0; $i<COUNT($slipCodeArray); $i++ ) {
            $condition['strSlipCode'] = $slipCodeArray[$i];
            $strQuery = fncGetSearchMSlipSQL($condition, true, $objDB);
            // ���٥ǡ����μ���
            list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
            if ( $lngResultNum )
            {
                for ( $j = 0; $j < $lngResultNum; $j++ )
                {
                    $Result = $objDB->fetchArray( $lngResultID, $j );
                    // ������Ψ������
                    $aryCurTax[] = $Result['curtax'];
                    // Ǽ����
                    $aryDeliveryDate[] = $Result['dtmdeliverydate'];
                }
            }
            else
           {
                $strMessage = fncOutputError( 603, DEF_WARNING, "Ǽ����ɼ�ޥ�����¸�ߤ��ޤ���", FALSE, "../inv/regist/renew.php?strSessionID=".$aryData["strSessionID"], $objDB );
            }
        }
        // ������Ψ��Ʊ���������å�
        $baseTax = null;
        foreach($aryCurTax as $tax){
            $baseTax = empty($baseTax) ? $tax : $baseTax;
            if($baseTax != $tax)
            {
                MoveToErrorPage("������Ψ�ΰۤʤ�Ǽ�ʽ�����������٤˺��ߤǤ��ޤ���");
            }
        }

        // Ǽ����
        $dtminvoicedate = $insertData['dtminvoicedate'];
        // Ǽ�����η�
        $baseMonth = date('m', strtotime($dtminvoicedate));
        // �����ƥ����դǻ��Ф���������������1�������
        $closeDay = fncGetCompanyClosedDay($insertData['strcustomercode'], $dtminvoicedate, $objDB);
        $baseDateTime = new DateTime($closeDay);
        foreach($aryDeliveryDate as $date){
            $deliveryDateTiem = new DateTime($date);
            $diff = $baseDateTime->diff($deliveryDateTiem);
            // Ǽ�����������ƥ����դ�1��������Ǥʤ����
            if($diff->format('%a') > 30)
            {
                MoveToErrorPage("Ǽ�����Ϻ��������1����δ֤���ꤷ�Ƥ�������");
            }
            // Ǽ�����Ȱۤʤ������٤ξ��
            $deliveryDateMonth = date('m', strtotime($date));
            if( (int)$baseMonth != (int)$deliveryDateMonth )
            {
                MoveToErrorPage("�������٤ˤϡ����Ϥ��줿Ǽ�����Ȱۤʤ���Ǽ�ʤ��줿���٤����Ǥ��ޤ���");
            }
        }

        // --------------------------------
        //    ��Ͽ����
        // --------------------------------
        // �ȥ�󥶥�����󳫻�
        $objDB->transactionBegin();

        // ������ֹ��ɳ�Ť��Ƥ������ޥ�����������ֹ����ˤ���
        if (!fncUpdateInvoicenoToMSales($lngInvoiceNo, $objDB))
        {
            fncOutputError ( 9051, DEF_FATAL, "����������ȼ�����ޥ����ơ��֥��������", TRUE, "", $objDB );
        }

        // �����ޥ�������������١����ޥ����򹹿�����
        if (!fncInvoiceInsert( $insertData , $objDB))
        {
            fncOutputError ( 9051, DEF_FATAL, "����������ȼ�����ޥ����ơ��֥��������", TRUE, "", $objDB );
        }

        // �ȥ�󥶥�����󥳥ߥå�
        $objDB->transactionCommit();

        // ��λ���̤�ɽ��
        $insertData["strAction"] = "/inv/renew.php?strSessionID=";
        $insertData["strSessionID"] = $aryData["strSessionID"];
        $insertData["time"]  = date('Y-m-d h:i:s');

        // ���쥳���ɡ����ܸ�
        $insertData["lngLanguageCode"] = 1;

        // �ƥ�ץ졼���ɤ߹���
        $objTemplate = new clsTemplate();
        $objTemplate->getTemplate( "inv/regist/regist_result.tmpl" );

        // �ƥ�ץ졼������
        $objTemplate->replace( $insertData );
        $objTemplate->complete();

        // HTML����
        echo $objTemplate->strTemplate;

        $objDB->close();

        return true;
    }
    else
    {
        $sql = fncGetSearchMSlipInvoiceNoSQL(2);
        $aryNewResult['strMode']      = 'renewPrev';
        // ���ٸ�����
        $aryNewResult["invConditionUrl"] = '/inv/regist/condition.php?strSessionID=' . $aryData["strSessionID"] . '&lngFunctionCode=' . $aryData["lngFunctionCode"] . '&lngApplicantUserCodeVisible=1&lngInputUserCodeVisible=1&dtmStartDateVisible=1&lngInChargeCodeVisible=1&lngWorkflowStatusCodeVisible=1&lngWorkflowStatusCodeConditions=1&lngSelectFunctionCode=500';

        // �������Ѥ��Ѵ�
        $aryNewResult['curThisMonthAmount']  = (int)preg_replace('/,/', '', $aryNewResult['curThisMonthAmount']);
        $aryNewResult['curLastMonthBalance'] = (int)preg_replace('/,/', '', $aryNewResult['curLastMonthBalance']);
        $aryNewResult['curSubTotal1'] = (int)preg_replace('/,/', '', $aryNewResult['curSubTotal1']);
        $aryNewResult['curTaxPrice1'] = (int)preg_replace('/,/', '', $aryNewResult['curTaxPrice1']);


        // �ƥ�ץ졼���ɤ߹���
        echo fncGetReplacedHtmlWithBase("inv/base_inv.html", "inv/regist/renew.tmpl", $aryNewResult ,$objAuth );

    }


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

