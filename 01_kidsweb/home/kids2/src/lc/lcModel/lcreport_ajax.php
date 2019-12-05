<?php

// �ɤ߹���
include 'conf.inc';
//���饹�ե�������ɤ߹���
require_once 'db_common.php';
//���̥ե������ɤ߹���
require_once './lcModelCommon.php';
//DB��³�ե�������ɤ߹���
require_once './db_common.php';
require_once './kidscore_common.php';
require_once './report_common.php';
require_once './reportoutput.php';
require LIB_FILE;
//PHPɸ���JSON�Ѵ��᥽�åɤϥ��顼�ˤʤ�Τǳ����Υ饤�֥��(���餯���󥳡��ɤ�����)
require_once 'JSON.php';
// phpspreadsheet�ѥå������򥤥�ݡ��Ȥ���
require PATH_HOME . "/vendor/autoload.php";

use PhpOffice\PhpSpreadsheet\Reader\Xls as XlsReader;
use PhpOffice\PhpSpreadsheet\Writer\Xls as XlsWriter;

//�ͤμ���
$postdata = file_get_contents("php://input");
$data = json_decode($postdata, true);
$objDB = new clsDB();
$objAuth = new clsAuth();
$objDB->open("", "", "", "");
//�������֥����ƥ�DB��³
$lcModel = new lcModel();

//JSON���饹���󥹥��󥹲�
$s = new Services_JSON();

//�ͤ�¸�ߤ��ʤ������̾�� POST �Ǽ�����
if ($data == null) {
    $data = $_POST;
}

// ���å�����ǧ
$objAuth = fncIsSession($data["sessionid"], $objAuth, $objDB);

//�桼����ID����(Ⱦ�ѥ��ڡ��������뤿��)
$usrId = trim($objAuth->UserID);

//�������
$result = array();

//��������ʬ��
switch ($data['method']) {
    // Ģɼ���Ͻ��ɽ�����٥��
    case 'getSelLcReport':
        //�����ƤӽФ�
        $result = getSelLcReport($objDB, $lcModel);
        $objDB->close();
        $lcModel->close();
        break;
        // Ģɼ���Ϥΰ������٥��
        // case 'exportLcReport':
        //     //�����ƤӽФ�
        //     $result = exportLcReport($objDB, $data);
        //     $objDB->close();
        //     break;
}

//��̽���
mb_convert_variables('UTF-8', 'EUC-JP', $result);
echo $s->encodeUnsafe($result);

/**
 * LCĢɼ���ϲ���-���쥯�ȥܥå����������
 *
 * @param [type] $objDB
 * @param [type] $lcModel
 * @return void
 */
function getSelLcReport($objDB, $lcModel)
{
    // ackids�Υǡ�����kidscore2����Ͽ
    // ackids�ζ�Ծ���μ���
    $bankArry = $lcModel->getBankInfo();
    // kidscore2�ζ�Ծ���κ��
    fncDeleteBank($objDB);
    // kidscore2�ζ�Ծ������Ͽ
    if (count($bankArry) > 0) {
        foreach ($bankArry as $bank) {
            fncInsertBank($objDB, $bank);
        }
    }

    // ackids�λ�ʧ�����μ���
    $payfArry = $lcModel->getPayfInfo();
    // kidscore2�λ�ʧ�����κ��
    fncDeletePayfinfo($objDB);
    // kidscore2�λ�ʧ��������Ͽ
    if (count($payfArry) > 0) {
        foreach ($payfArry as $payf) {
            fncInsertPayf($objDB, $payf);
        }
    }

    // ackids��������ޥ�������μ���
    $sendArry = $lcModel->getSendInfo();
    // kidscore2��������ޥ�������κ��
    fncDeleteSendinfo($objDB);
    // kidscore2��������ޥ����������Ͽ
    if (count($sendArry) > 0) {
        foreach ($sendArry as $send) {
            fncInsertSendInfo($objDB, $send);
        }
    }

    // �����ϥꥹ�Ȥμ���
    $result["portplace"] = fncGetPortplaceAndAll($objDB);
    // ��ԥꥹ�Ȥμ���
    $result["bankinfo"] = fncGetBankAndAll($objDB);

    return $result;
}

/**
 * LCĢɼ���ϲ���-��������
 *
 * @param [object] $objDB
 * @param [string] $data
 * @return void
 */
function exportLcReport($objDB, $data)
{

    // �ѥ�᡼���μ���
    // �о�ǯ��
    $objectYm = $data["objectYm"];

    //�̲߶�ʬ�ꥹ�Ȥμ���
    $currencyClassLst = fncGetCurrencyClassList($objDB);
    //�̲߶�ʬ(̤��ǧ�ޤ�)�ꥹ�Ȥμ���
    $currencyClassAllLst = fncGetCurrencyClassListAll($objDB);
    // ��ԥޥ�������μ���
    $bankLst = fncGetValidBankInfo($objDB);

    //�ƥ�ץ졼�ȤΥ��ԡ�

    $reader = new XlsReader();
    $filepath = REPORT_TMPDIR . REPORT_LC_TMPFILE;
    $spreadsheet = $reader->load($filepath); //template.xlsx �ɹ�

    // ����
    if ($data["impletterChk"] == "true") {
        if ($currencyClassLst && count($currencyClassLst) > 0) {
            foreach ($currencyClassLst as $currencyClassObj) {
                $currencyClass = $currencyClassObj["currencyclass"];
                // ͢�����Ѿ�ȯ�Ծ���ν���
                reportSixOutput($objDB, $spreadsheet, $currencyClass, $bankLst, $data);
            }
        }
    }

    if ($data["setChk"] == "true") {
        if ($currencyClassLst && count($currencyClassLst) > 0) {
            foreach ($currencyClassLst as $currencyClassObj) {
                $currencyClass = $currencyClassObj["currencyclass"];
                // LCOpen����(Beneficiary��BK�̹��)�������ץ��ν���
                reportOneOutput($objDB, $spreadsheet, $currencyClass, $bankLst, $objectYm, 1);

                // LCOpen����(Beneficiary��BK�̹��)�����ѷ�ν���
                reportOneOutput($objDB, $spreadsheet, $currencyClass, $bankLst, $objectYm, 2);

                // L/C Open����(LC�̹�סˤν���
                reportTwoOutput($objDB, $spreadsheet, $currencyClass, $objectYm);

                // L/C Open����(LC�����١ˤν���
                reportThreeOutput($objDB, $spreadsheet, $currencyClass, $objectYm);

                // L/C Open�����Open�Beneficiary��L/Cȯ��ͽ�꽸��ɽ�ˤν���
                reportFourOutput($objDB, $spreadsheet, $currencyClass, $objectYm, 3);

                // L/C Open��������ѷBeneficiary��L/Cȯ��ͽ�꽸��ɽ�ˤν���
                reportFourOutput($objDB, $spreadsheet, $currencyClass, $objectYm, 4);
            }
        }

    }

    if ($data["unsetChk"] == "true") {
        if ($currencyClassAllLst && count($currencyClassAllLst) > 0) {
            foreach ($currencyClassAllLst as $currencyClassObj) {
                $currencyClass = $currencyClassObj["currencyclass"];
                // L/C ̤��ѥꥹ�Ȥν���
                reportFiveOutput($objDB, $spreadsheet, $currencyClass, $bankLst, $data);
            }
        }
    }

    $writer = new XlsWriter($spreadsheet);
    $writer->save(REPORT_TMPDIR . REPORT_LC_OUTPUTFILE);

}

/**
 * Ģɼ�ʥ����ץ���_���ν���
 *
 * @param [type] $objDB
 * @param [type] $currencyClass
 * @param [type] $bankLst
 * @param [type] $objectYm
 * @return void
 */
function reportOneOutput($objDB, $spreadsheet, $currencyClass, $bankLst, $objectYm, $type)
{
    $insertData = array();
    $params = array();
    $params["opendate"] = str_replace("/", "", $objectYm);
    $params["currencyclass"] = $currencyClass;

    // ��ʧ�����̤ι�׶�ۤ��������
    $totalPriceByPayfBankLst = fncGetSumOfMoneypriceByPayfAndBank($objDB, $params, $type);

    // ��ʧ���̤ι�׶�ۤ��������
    $totalPriceByPayfLst = fncGetSumOfMoneypriceByPayf($objDB, $params, $type);

    // ĢɼBeneBk�̹�ץơ��֥�Υǡ���������������
    fncDeleteReportByBenebktotal($objDB);

    // ���׻��ơ��֥��ĢɼBeneB���̹�ץơ��֥�˥ǡ�������Ͽ����
    if ($totalPriceByPayfLst && count($totalPriceByPayfLst) > 0) {
        foreach ($totalPriceByPayfLst as $totalPriceByPayf) {
            $payfcd = $totalPriceByPayf["payfcd"];
            $insertData["beneficiary"] = $totalPriceByPayf["payfnameformal"];
            $insertData["total"] = $totalPriceByPayf["totalmoneyprice"];
            if ($totalPriceByPayfBankLst && count($totalPriceByPayfBankLst) > 0) {
                foreach ($totalPriceByPayfBankLst as $totalPriceByPayfBank) {
                    if ($totalPriceByPayfBank["payfcd"] == $payfcd) {
                        $bankcd = $totalPriceByPayfBank["bankcd"];
                        $totalmoneyprice = $totalPriceByPayfBank["totalmoneyprice"];
                        if ($bankcd == $bankLst[0]["bankcd"]) {
                            $insertData["bank1"] = $totalmoneyprice;
                        } else if ($bankcd == $bankLst[1]["bankcd"]) {
                            $insertData["bank2"] = $totalmoneyprice;
                        } else if ($bankcd == $bankLst[2]["bankcd"]) {
                            $insertData["bank3"] = $totalmoneyprice;
                        } else if ($bankcd == $bankLst[3]["bankcd"]) {
                            $insertData["bank4"] = $totalmoneyprice;
                        }
                    } else {
                        continue;
                    }
                }
            }

            // ĢɼBeneB���̹�ץơ��֥����Ͽ����
            fncInsertReportByBenebktotal($objDB, $insertData);

            unset($insertData);

        }
        // �ƥ�ץ졼�Ȥν���
        fncSetReportOne($objDB, $spreadsheet, "1", $currencyClass, $bankLst, $objectYm, $type);

    }
}

/**
 * Ģɼ_2�ν���
 *
 * @param [object] $objDB
 * @param [sheet] $spreadsheet
 * @param [string] $currencyClass
 * @param [stringg] $objectYm
 * @return void
 */
function reportTwoOutput($objDB, $spreadsheet, $currencyClass, $objectYm)
{
    $insertData = array();
    $priceTotal = 0;
    // Ģɼ�����Ѥ�L/C�̹��
    $lcinfoLst = fncGetLcInfoForReportTwo($objDB, str_replace("/", "", $objectYm), $currencyClass);

    // ���׻��ơ��֥��ĢɼLC�̹�ץơ��֥�Υǡ���������������
    fncDeleteReportByLcTotal($objDB);

    if ($lcinfoLst && count($lcinfoLst) > 0) {
        foreach ($lcinfoLst as $lcinfo) {
            $insertData["lcno"] = $lcinfo["lcno"];
            $insertData["factoryname"] = $lcinfo["payfnameformal"];
            $insertData["price"] = $lcinfo["moneyprice"];
            $insertData["shipterm"] = $lcinfo["shipterm"];
            $insertData["validterm"] = $lcinfo["validterm"];
            $insertData["bankname"] = $lcinfo["bankname"];
            $insertData["bankreqdate"] = $lcinfo["bankreqdate"];
            $insertData["lcamopen"] = $lcinfo["lcamopen"];
            // ��׶�ۤ�����
            $priceTotal += $lcinfo["moneyprice"];

            // ���׻��ơ��֥��ĢɼLC�̹�ץơ��֥�˥ǡ�������Ͽ����
            fncInsertReportByLcTotal($objDB, $insertData);

            unset($insertData);
        }

        // �ƥ�ץ졼�Ȥν���
        fncSetReportTwo($objDB, $spreadsheet, "2", $currencyClass, $objectYm, $priceTotal);

    }
}

/**
 * Ģɼ_3�ν���
 *
 * @param [object] $objDB
 * @param [sheet] $spreadsheet
 * @param [string] $currencyClass
 * @param [string] $objectYm
 * @return void
 */
function reportThreeOutput($objDB, $spreadsheet, $currencyClass, $objectYm)
{
    $insertData = array();
    $priceTotal = 0;
    // Ģɼ�����Ѥ�L/C�̹��
    $lcinfoLst = fncGetLcInfoForReportTwo($objDB, str_replace("/", "", $objectYm), $currencyClass);

    // ���׻��ơ��֥��ĢɼLC�����٥ơ��֥�Υǡ���������������
    fncDeleteReportByLcDetail($objDB);

    if ($lcinfoLst && count($lcinfoLst) > 0) {
        foreach ($lcinfoLst as $lcinfo) {
            $insertData["lcno"] = $lcinfo["lcno"];
            $insertData["pono"] = $lcinfo["pono"] . sprintf("%02s", $lcinfo["polineno"]);
            $insertData["factoryname"] = $lcinfo["payfnameformal"];
            $insertData["productcd"] = $lcinfo["productcd"];
            $insertData["productrevisecd"] = $lcinfo["productrevisecd"];
            $insertData["productname"] = $lcinfo["productname"];
            $insertData["productnumber"] = $lcinfo["productnumber"];
            $insertData["unitname"] = $lcinfo["unitname"];
            $insertData["unitprice"] = $lcinfo["unitprice"];
            $insertData["moneyprice"] = $lcinfo["moneyprice"];
            $insertData["shipstartdate"] = $lcinfo["shipstartdate"];
            $insertData["shipenddate"] = $lcinfo["shipenddate"];
            $insertData["portplace"] = $lcinfo["portplace"];
            $insertData["shipterm"] = $lcinfo["shipterm"];
            $insertData["validterm"] = $lcinfo["validterm"];
            $insertData["bankname"] = $lcinfo["bankname"];
            $insertData["bankreqdate"] = $lcinfo["bankreqdate"];
            $insertData["lcamopen"] = $lcinfo["lcamopen"];
            // ��׶�ۤ�����
            $priceTotal += $lcinfo["moneyprice"];

            // ���׻��ơ��֥��ĢɼLC�̹�ץơ��֥�˥ǡ�������Ͽ����
            fncInsertReportByLcDetail($objDB, $insertData);
        }
        // �ƥ�ץ졼�Ȥν���
        fncSetReportThree($objDB, $spreadsheet, "3", $currencyClass, $objectYm, $priceTotal);

    }

}

/**
 * Ģɼ_4�ν���
 *
 * @param [object] $objDB
 * @param [sheet] $spreadsheet
 * @param [string] $currencyClass
 * @param [string] $objectYm
 * @param [string] $type
 * @return void
 */
function reportFourOutput($objDB, $spreadsheet, $currencyClass, $objectYm, $type)
{

    $insertData = array();
    $params = array();
    $dates = array();
    $objectYm = str_replace("/", "-", $objectYm) . "-01";
    $dates["date1"] = date("Ym", strtotime($objectYm . "-6 month"));
    $dates["date2"] = date("Ym", strtotime($objectYm . "-5 month"));
    $dates["date3"] = date("Ym", strtotime($objectYm . "-4 month"));
    $dates["date4"] = date("Ym", strtotime($objectYm . "-3 month"));
    $dates["date5"] = date("Ym", strtotime($objectYm . "-2 month"));
    $dates["date6"] = date("Ym", strtotime($objectYm . "-1 month"));
    $dates["date7"] = date("Ym", strtotime($objectYm . "0 month"));
    $dates["date8"] = date("Ym", strtotime($objectYm . "+1 month"));
    $dates["date9"] = date("Ym", strtotime($objectYm . "+2 month"));
    $dates["date10"] = date("Ym", strtotime($objectYm . "+3 month"));
    $dates["date11"] = date("Ym", strtotime($objectYm . "+4 month"));
    $params["opendatefrom"] = $dates["date1"];
    $params["opendateto"] = $dates["date11"];
    $params["currencyclass"] = $currencyClass;

    // ��ʧ����̤ι�׶�ۤ��������
    $totalPriceByPayfDateLst = fncGetSumOfMoneypriceByPayfAndOpenDate($objDB, $params, $type);

    // ��ʧ���̤ι�׶�ۤ��������
    $totalPriceByPayfLst = fncGetSumOfMoneypriceByPayf($objDB, $params, $type);

    // ĢɼBene���̹�ץơ��֥�Υǡ���������������
    fncDeleteReportByBeneMonthCal($objDB);

    if (count($totalPriceByPayfLst) > 0 && count($totalPriceByPayfDateLst) > 0) {
        foreach ($totalPriceByPayfLst as $totalPriceByPayf) {

            $payfcd = $totalPriceByPayf["payfcd"];

            $insertData["beneficiary"] = $totalPriceByPayf["payfnameformal"];
            $insertData["total"] = $totalPriceByPayf["totalmoneyprice"];
            $total = 0;

            foreach ($totalPriceByPayfDateLst as $totalPriceByPayfDate) {

                if ($totalPriceByPayfDate["payfcd"] == $payfcd) {
                    $opendate = $totalPriceByPayfDate["opendate"];
                    $moneyprice = $totalPriceByPayfDate["totalmoneyprice"];

                    if ($opendate == $dates["date1"]) {
                        $insertData["date1"] = $moneyprice;
                    } else if ($opendate == $dates["date2"]) {
                        $insertData["date2"] = $moneyprice;
                    } else if ($opendate == $dates["date3"]) {
                        $insertData["date3"] = $moneyprice;
                    } else if ($opendate == $dates["date4"]) {
                        $insertData["date4"] = $moneyprice;
                    } else if ($opendate == $dates["date5"]) {
                        $insertData["date5"] = $moneyprice;
                    } else if ($opendate == $dates["date6"]) {
                        $insertData["date6"] = $moneyprice;
                    } else if ($opendate == $dates["date7"]) {
                        $insertData["date7"] = $moneyprice;
                    } else if ($opendate == $dates["date8"]) {
                        $insertData["date8"] = $moneyprice;
                    } else if ($opendate == $dates["date9"]) {
                        $insertData["date9"] = $moneyprice;
                    } else if ($opendate == $dates["date10"]) {
                        $insertData["date10"] = $moneyprice;
                    } else if ($opendate == $dates["date11"]) {
                        $insertData["date11"] = $moneyprice;
                    }
                    $total += $moneyprice;
                } else {
                    continue;
                }
            }

            $insertData["total"] = $total;
            // ���׻��ơ��֥��ĢɼBene���̹�ץơ��֥�˥ǡ�������Ͽ����
            fncInsertReportByBeneMonthCal($objDB, $insertData);

            unset($insertData);

        }
        // �ƥ�ץ졼�Ȥν���
        fncSetReportFour($objDB, $spreadsheet, "4", $currencyClass, $objectYm, $type);

    }

}

/**
 * Ģɼ_5�ν���
 *
 * @param [object] $objDB
 * @param [sheet] $spreadsheet
 * @param [string] $currencyClass
 * @param [array] $bankLst
 * @param [array] $data
 * @return void
 */
function reportFiveOutput($objDB, $spreadsheet, $currencyClass, $bankLst, $data)
{
    $insertData = array();
    // ���׻��ơ��֥��Ģɼ̤��ѳۥơ��֥�ǡ���������������
    fncDeleteReportUnSettedPrice($objDB);
    // L/C�������
    $lcinfoLst = fncGetLcInfoForReportFive($objDB, $data["startDate"], $data["endDate"], $currencyClass, 1);

    // ���׻��ơ��֥��Ģɼ̤��ѳۥơ��֥�˥ǡ�������Ͽ����
    if ($lcinfoLst && count($lcinfoLst) > 0) {
        $data = array();
        $manageno = 1;
        foreach ($lcinfoLst as $lcinfo) {
            $insertData["managementno"] = $manageno;
            $insertData["bankname"] = $lcinfo["bankname"];
            $insertData["payeeformalname"] = $lcinfo["payfnameformal"];
            $insertData["shipstartdate"] = $lcinfo["shipstartdate"];
            $insertData["lcno"] = $lcinfo["lcno"];
            $insertData["usancesettlement"] = $lcinfo["moneyprice"] -
                ($lcinfo["bldetail1money"] + $lcinfo["bldetail2money"] + $lcinfo["bldetail3money"]);

            fncInsertReportUnSettedPrice($objDB, $insertData);
            $manageno += 1;
            unset($insertData);
        }
    }

    // ���׻��ơ��֥��Ģɼ̤��ѳ�̤��ǧ�ơ��֥�ǡ���������������
    fncDeleteReportUnSettedPriceUnapproval($objDB);

    // L/C�������
    $lcinfoLst = fncGetLcInfoForReportFive($objDB, $startYmd, $endYmd, $currencyClass, 2);

    // ���׻��ơ��֥��Ģɼ̤��ѳ�̤��ǧ�ơ��֥�˥ǡ�������Ͽ����
    if ($lcinfoLst && count($lcinfoLst) > 0) {
        foreach ($lcinfoLst as $lcinfo) {
            $insertData["payeeformalname"] = $lcinfo["payfnameformal"];
            $insertData["unsettledprice"] = $lcinfo["moneyprice"] -
                ($lcinfo["bldetail1money"] + $lcinfo["bldetail2money"] + $lcinfo["bldetail3money"]);

            fncInsertReportUnSettedPriceUnapproval($objDB, $insertData);
            unset($insertData);
        }
    }

    // ĢɼBeneBK�̹�ץơ��֥�ǡ���������������
    fncDeleteReportByBenebktotal($objDB);

    // ��ʧ�����̤ι�׶�ۤ��������
    $totalUnSettedPriceByPayfBankLst = fncGetSumofUnSettedPriceByPayfAndBank($objDB);

    // ��ʧ�����̤ι�׶�ۤ��������
    $totalUnSettedPriceByPayfLst = fncGetSumofUnSettedPriceByPayf($objDB);

    // ���׻��ơ��֥��ĢɼBeneBk�̹�ץơ��֥�˥ǡ�������Ͽ����
    if ($totalUnSettedPriceByPayf && count($totalUnSettedPriceByPayf) > 0) {
        foreach ($totalUnSettedPriceByPayfLst as $totalUnSettedPriceByPayf) {
            $payfcd = $totalUnSettedPriceByPayf["payfcd"];
            $insertData["beneficiary"] = $totalUnSettedPriceByPayf["payfnameformal"];
            $insertData["total"] = $totalUnSettedPriceByPayf["totalmoneyprice"];
            foreach ($totalUnSettedPriceByPayfBankLst as $totalUnSettedPriceByPayfBank) {
                if ($totalUnSettedPriceByPayfBank["payfcd"] == $payfcd) {
                    $bankcd = $totalUnSettedPriceByPayfBank["bankcd"];
                    $totalmoneyprice = $totalUnSettedPriceByPayfBank["totalmoneyprice"];
                    if ($bankcd == $bankLst[0]["bankcd"]) {
                        $insertData["bank1"] = $totalmoneyprice;
                    } else if ($bankcd == $bankLst[1]["bankcd"]) {
                        $insertData["bank2"] = $totalmoneyprice;
                    } else if ($bankcd == $bankLst[2]["bankcd"]) {
                        $insertData["bank3"] = $totalmoneyprice;
                    } else if ($bankcd == $bankLst[3]["bankcd"]) {
                        $insertData["bank4"] = $totalmoneyprice;
                    }
                } else {
                    continue;
                }
            }

            // ĢɼBeneBk�̹�ץơ��֥����Ͽ����
            fncInsertReportByBenebktotal($objDB, $insertData);

            unset($insertData);
        }
    }

    // �졼�Ȥ����ꤹ��
    $rate = 0;
    if ($data["rate"] != "" && $data["rate"] > 0) {
        $rate = $data["rate"];
    } else {
        // �ѥ�᡼�����̲߶�ʬ��̤��ǧ�ޤ�ˤ��ɱߡɤξ�硢�̲߶�ʬ = 1
        if ($currencyClass == "��") {
            $monetaryUnitCode = DEF_MONETARY_YEN;

            // �ѥ�᡼�����̲߶�ʬ��̤��ǧ�ޤ�ˤ���US�ɥ�ɤξ�硢�̲߶�ʬ = 2
        } else if ($currencyClass == "US�ɥ�") {
            $monetaryUnitCode = DEF_MONETARY_USD;

            // �ѥ�᡼�����̲߶�ʬ��̤��ǧ�ޤ�ˤ���HK�ɥ�ɤξ�硢�̲߶�ʬ = 3
        } else if ($currencyClass == "HK�ɥ�") {
            $monetaryUnitCode = DEF_MONETARY_HKD;
        }
        // �̲߶�ʬ��̤��ǧ�ޤ�ˤ��ɱߡɤξ�硢
        if ($currencyClass == "��") {
            $rate = 0;
        } else {
            $rate = fncGetMonetaryRate($objDB, DEF_MONETARYCLASS_SHANAI, $monetaryUnitCode);
        }

        // �ƥ�ץ졼�Ȥν���
        fncSetReportFive($objDB, $spreadsheet, "5", $currencyClass, $bankLst, $data["startDate"], $data["endDate"], $rate);

    }
}

/**
 * Ģɼ_6�ν���
 *
 * @param [object] $objDB
 * @param [sheet] $spreadsheet
 * @param [string] $currencyClass
 * @param [array] $bankLst
 * @param [array] $data
 * @return void
 */
function reportSixOutput($objDB, $spreadsheet, $currencyClass, $bankLst, $data)
{
    $insertData = array();
    // L/C������������
    $lcinfoLst = fncGetLcInfoForReportSix($objDB, $currencyClass, $data);

    // ���׻��ơ��֥��Ģɼ͢�����Ѿ�ȯ�Ծ���ơ��֥���ǡ���������������
    fncDeleteReportImpLcOrderInfo($objDB);

    // �嵭��������LC�����Ģɼ͢�����Ѿ�ȯ�Ծ���ơ��֥����Ͽ����
    if ($lcinfoLst && count($lcinfoLst) > 0) {
        foreach ($lcinfoLst as $lcinfo) {
            $insertData["bankreqdate"] = $lcinfo["bankreqdate"];
            $insertData["pono"] = $lcinfo["pono"] . sprintf("%02s", $lcinfo["polineno"]);
            $insertData["productcd"] = $lcinfo["productcd"];
            $insertData["productrevisecd"] = $lcinfo["productrevisecd"];
            $insertData["productname"] = $lcinfo["productname"];
            $insertData["productnumber"] = $lcinfo["productnumber"];
            $insertData["unitname"] = $lcinfo["unitname"];
            $insertData["unitprice"] = $lcinfo["unitprice"];
            $insertData["moneyprice"] = $lcinfo["moneyprice"];
            $insertData["shipstartdate"] = $lcinfo["shipstartdate"];
            $insertData["shipenddate"] = $lcinfo["shipenddate"];
            $insertData["shipterm"] = $lcinfo["shipterm"];
            $insertData["validterm"] = $lcinfo["validterm"];
            $insertData["lcno"] = $lcinfo["lcno"];
            $insertData["reckoninginitialdate"] = $lcinfo["reckoninginitialdate"];
            $insertData["portplace"] = $lcinfo["portplace"];
            $insertData["bankname"] = $lcinfo["bankname"];
            $insertData["reserve1"] = $lcinfo["reserve1"];

            fncInsertReportImpLcOrderInfo($objDB, $insertData);

            unset($insertData);
        }
        // �ƥ�ץ졼�Ȥν���
        return fncSetReportSix($objDB, $spreadsheet, "6", $currencyClass, $bankLst, $data);

    }
}
