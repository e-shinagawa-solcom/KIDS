<?php

// ----------------------------------------------------------------------------
/**
 *       �������  ���ٽ񸡺�����
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
 *         �����ٽ�ܺ٤򸡺�����
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
    require_once (SRC_ROOT.'/cmn/exception/SQLException.class.php');
	require_once (LIB_DEBUGFILE);

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

    // ajax ����
    if(isset($aryData["mode"]) && $aryData["mode"] == 'ajax')
    {
        // ��������ޤ�Ǥ�����
        if(array_key_exists("conditions", $_POST) && count($_POST["conditions"]))
        {

            // ������ѥ�᡼���κ���
            foreach ($_POST["conditions"] as $key=>$condition)
            {
                $params[$key] = pg_escape_string($condition);
            }

            // SQLʸ����
            $strQuery = fncGetSearchMSlipSQL ($params, false, $objDB);
//             error_log($strQuery,"3",LOG_FILE);
            // EUC-JP���Ѵ�
//            $strQuery = mb_convert_encoding($strQuery, "EUC-JP", "auto");

            // ������¹�
            list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

            // ��̷�������
            if($lngResultNum > 0)
            {
                // �쥳���ɷ��ʬ����
                for ($i = 0; $i < $lngResultNum; $i++)
                {

    //                 $resultDataSet[] = pg_fetch_array($objResult, $i, PGSQL_ASSOC);
                    $resultDataSet[] = $objDB->fetchObject( $lngResultID, $i );
    //                 $resultDataSet[] = $objDB->fetchArray( $lngResultID, $i );
                    // ������̥쥳���ɤ򥪥֥������ȤǼ�����ɬ�פ�json�ǡ����˲ù�����
                    foreach($objDB->fetchObject( $lngResultID, $i ) as $column => $val)
                    {
                        $json[$i][mb_convert_encoding($column,"UTF-8","auto")] = mb_convert_encoding($val,"UTF-8","auto");
                    }
//                     $json[$i]['sql'] = $strQuery;
                }

                // json�Ѵ��ΰ١����Ū��UTF-8���Ѵ�
//                mb_convert_variables('UTF-8', 'euc-jp', $json);
			    $objDB->close();
	            // �쥹�ݥ󥹥إå�����
	            header('Content-Type: application/json');
                echo json_encode($json);
                exit;
            }
            else
            {
            	$json[mb_convert_encoding("Message","UTF-8", "auto")] = mb_convert_encoding("��������쥳���ɤ����Ĥ���ޤ���Ǥ���", "UTF-8", "auto");
                echo  json_encode($json);
            }
        }
        // ��̤������ʤ��ä�(������˼��Ԥ���)���
        else
        {
            throw new SQLException(
                "�䤤��碌�˼��Ԥ��ޤ���",
                $strQuery,
                $params);
        }

	    $objDB->close();
	    return true;
    }
    else if(isset($aryData["mode"]) && $aryData["mode"] == 'ajaxRenew')
    {
        // SQLʸ����
        $invoiceNo = (int)$aryData["invoiceNo"];
        $strQuery = fncGetSearchMSlipInvoiceNoSQL($invoiceNo);
        // EUC-JP���Ѵ�
        $strQuery = mb_convert_encoding($strQuery, "EUC-JP", "auto");
        // ������¹�
        list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

        // ��̷�������
        if($lngResultNum > 0)
        {
            // �쥳���ɷ��ʬ����
            for ($i = 0; $i < $lngResultNum; $i++)
            {

                //                 $resultDataSet[] = pg_fetch_array($objResult, $i, PGSQL_ASSOC);
                $resultDataSet[] = $objDB->fetchObject( $lngResultID, $i );
                //                 $resultDataSet[] = $objDB->fetchArray( $lngResultID, $i );
                // ������̥쥳���ɤ򥪥֥������ȤǼ�����ɬ�פ�json�ǡ����˲ù�����
                foreach($objDB->fetchObject( $lngResultID, $i ) as $column => $val)
                {
                    $json[$i][$column] = $val;
                }
            }

            // �쥹�ݥ󥹥إå�����
//            header('Content-Type: application/json');
            // json�Ѵ��ΰ١����Ū��UTF-8���Ѵ�
            mb_convert_variables('UTF-8', 'eucjp-win', $json);

            echo json_encode($json);
        }


        $objDB->close();
        return true;

    }

    // ���ٸ�����
    $aryData["invConditionUrl"] = '/inv/regist/condition.php?strSessionID=' . $aryData["strSessionID"] . '&lngFunctionCode=' . $aryData["lngFunctionCode"] . '&lngApplicantUserCodeVisible=1&lngInputUserCodeVisible=1&dtmStartDateVisible=1&lngInChargeCodeVisible=1&lngWorkflowStatusCodeVisible=1&lngWorkflowStatusCodeConditions=1&lngSelectFunctionCode=500';

    // �̲�ñ�̥ޥ��������
    $strQuery = "SELECT lngmonetaryunitcode, strmonetaryunitsign FROM m_monetaryunit ORDER BY lngmonetaryunitcode";
    $aryData["monetaryunitList"] = fncGetPulldownQueryExec($strQuery, 1, $objDB, false);
    // �Ƕ�ʬ�ޥ��������
    $strQuery = "SELECT lngtaxclasscode, strtaxclassname FROM m_taxclass ORDER BY lngtaxclasscode";
    $taxclassList = fncGetPulldownQueryExec($strQuery, 0, $objDB, false);
    $aryData["taxclassList"] = '<OPTION VALUE="0">̤����</OPTION>' .$taxclassList;

    // �����ƥ�date�η�顦���������
    $aryData["DeliveryFrom"] = date('Y/m/d', strtotime('first day of ' . null));
    $aryData["DeliveryTo"]   = date('Y/m/d', strtotime('last day of '  . null));

    // �ƥ�ץ졼���ɤ߹���
//     echo fncGetReplacedHtml( "/inv/regist/condition.tmpl", $aryData, $objAuth );
    // �ƥ�ץ졼���ɤ߹���
    echo fncGetReplacedHtmlWithBase("inv/base_condition.html", "inv/regist/condition.tmpl", $aryData ,$objAuth );

    $objDB->close();

    return true;

?>

