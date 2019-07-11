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
require LIB_FILE;
//PHPɸ���JSON�Ѵ��᥽�åɤϥ��顼�ˤʤ�Τǳ����Υ饤�֥��(���餯���󥳡��ɤ�����)
require_once 'JSON.php';

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
    //�������Ƚ���
    case 'logoutState':
        //�����ƤӽФ�
        $result = logoutState($lcModel, $data);
        break;
    //L/C�������С����ǡ�����ͭ���ǡ�������PONO�����ȥ��٥��
    case 'getLcInfo':
        $result = getLcInfo($objDB, $data);
        //�оݥǡ�����¿����JSON�Ѵ����˥��ꥪ���С��ˤʤ뤿�ᡢ2000��Ǿ�ʬ�����Ѵ�����
        ini_set('memory_limit', '512M');
        break;
    // ���ߥ�졼�ȥ��٥��
    case 'getSimulateLcInfo':
        // ���ߥ�졼�Ƚ���
        $result = getSimulateLcInfo($objDB, $data);
        //�оݥǡ�����¿����JSON�Ѵ����˥��ꥪ���С��ˤʤ뤿�ᡢ2000��Ǿ�ʬ�����Ѵ�����
        ini_set('memory_limit', '512M');
        break;
    // ȿ�ǥ��٥��
    case 'reflectLcInfo':
        $result = reflectLcInfo($objDB, $lcModel, $usrId);
        break;
    // Ģɼ���Ͻ��ɽ�����٥��
    case 'getSelLcReport':
        //�����ƤӽФ�
        $result = getSelLcReport();
        break;
    // Ģɼ���Ϥΰ������٥��
    case 'exportLcReport':
        //�����ƤӽФ�
        $result = exportLcReport($data);
        break;
}

$objDB->close();
$lcModel->close();

//��̽���
mb_convert_variables('UTF-8', 'EUC-JP', $result);
echo $s->encodeUnsafe($result);

// �������Ƚ���
function logoutState($lcModel, $data)
{
    $result = $lcModel->loginStateLogout($data);
    return $result;
}

/**
 * LC�������
 *
 * @param [object] $objDB
 * @param [array] $data
 * @return void
 */
function getLcInfo($objDB, $data)
{
    $result = fncGetLcInfoData($objDB, $data);
    return $result;
}

/**
 * ���ߥ�졼�Ƚ���
 *
 * @param [object] $objDB
 * @param [array] $data
 * @return void
 */
function getSimulateLcInfo($objDB, $data)
{
    // ��ԥޥ�������μ���
    $bankArry = fncGetValidBankInfo($objDB);

    // �̲߶�ʬ����μ���
    $currencyClassArry = fncGetCurrencyClassList($objDB);

    // ���ѿ��ν����
    $shipym = $data["to"];
    $sumOfMoneypriceByPonoArry = array();
    $mCurTtlMoney = 0;
    $sumOfMoneypriceByBanknameArry = array();

    if (count($currencyClassArry) > 0) {
        foreach ($currencyClassArry as $currencyClass) {
            // �̲���PO�ֹ��̹�׶������
            $sumOfMoneypriceByPonoArry = fncGetSumOfMoneypriceByPono($objDB, $shipym, $currencyClass);
            //
            if (count($sumOfMoneypriceByPonoArry) > 0) {
                // �̲��̹�׶������
                $mCurTtlMoney = fncGetSumOfMoneyprice($objDB, $shipym, $currencyClass);
                // �̲��̶���̤ι�׶�ۼ���
                $sumOfMoneypriceByBanknameArry = fncGetSumOfMoneypriceByBankname($objDB, $shipym, $currencyClass);

                $bankdivMoney = array();
                // ��Գ俶����
                for ($i = 0; $i <= count($bankArry) - 1; $i++) {
                    // ����̳俶���� = �̲��̹�׶�� * ����̳俶Ψ����ʬ.�俶��Ψ
                    $bankdivMoney[$i] = $mCurTtlMoney * $bankArry[$i]->bankdivrate;
                    // �̲���PO�ֹ��̹�׶������ʬ
                    foreach ($sumOfMoneypriceByBanknameArry as $sumOfMoneypriceByBankname) {
                        // ����̳俶Ψ����.���̾��ά̾�� = �̲��̶���̹�׶������.ȯ�Զ��̾�ξ��
                        if ($bankArry[$i]->bankomitname == $sumOfMoneypriceByBankname->bankname) {
                            // ����̳俶���� = ����̳俶���ۡ�- �̲��̶���̹�׶������.��׶��
                            $bankdivMoney[$i] = $bankdivMoney[$i] - $sumOfMoneypriceByBankname->totalmoneyprice;
                        }
                    }
                }

                // �̲���PO�ֹ��̹�׶������ʬ
                foreach ($sumOfMoneypriceByPonoArry as $sumOfMoneypriceByPono) {
                    $curMoney = $sumOfMoneypriceByPono->totalmoneyprice;
                    $blnBkSetFlg = false;
                    for ($i = 0; $i <= count($bankArry) - 1; $i++) {
                        // ����̳俶���� - �̲���PO�ֹ��̹�׶������.��׶�ۡ�>= 0�ξ��
                        if (($bankdivMoney[$i] - $curMoney) >= 0) {
                            // ����̳俶���� = ����̳俶���� - �̲���PO�ֹ��̹�׶������.��׶��
                            $bankdivMoney[$i] = $bankdivMoney[$i] - $sumOfMoneypriceByPono->totalmoneypric;
                            // t_lcinfo�򹹿�
                            fncUpdateBankname($objDB, $bankArry[$i]->bankcd, $bankArry[$i]->bankomitname, $currencyClass, $sumOfMoneypriceByPono->pono);

                            $blnBkSetFlg = true;
                        }
                    }

                    if (!$blnBkSetFlg) {
                        $intBkNum = 0;
                        for ($i = 0; $i <= count($bankArry) - 1; $i++) {
                            if ($i == 0) {
                                $curCkMnyTmp = $bankdivMoney[$i] - $curMoney;
                                $intBkNum = $i;
                            } else {
                                if ($curCkMnyTmp > ($bankdivMoney[$i] - $curMoney)) {
                                    $curCkMnyTmp = $bankdivMoney[$i] - $curMoney;
                                    $intBkNum = $i;
                                }
                            }
                        }
                        $bankdivMoney[$intBkNum] = $bankdivMoney[$intBkNum] - $curMoney;

                        // t_lcinfo�򹹿�
                        fncUpdateBankname($objDB, $bankArry[$intBkNum]->bankcd, $bankArry[$intBkNum]->bankomitname, $currencyClass, $sumOfMoneypriceByPono->pono);

                    }
                }
            }

        }

    }

    // L/C����ǡ��������
    $result = fncGetLcInfoData($objDB, $data);
    return $result;

}

/**
 * L/C�����ackids��ȿ��
 *
 * @param [object] $objDB
 * @param [object] $lcModel
 * @param [array] $data
 * @return boolean
 */
function reflectLcInfo($objDB, $lcModel, $usrId)
{
    // t_lcinfo���L/C������������
    $lcInfoArry = fncGetLcInfoData($objDB, $data);
    if (count($lcInfoArry) > 0) {
        foreach ($lcInfoArry as $lcInfo) {
            $lcInfo["updateuser"] = $usrId;
            $lcInfo["updatedate"] = date("Ymd");
            $lcInfo["updatetime"] = date("H:i:s");
            // t_aclcinfo�˥ǡ�����ȿ�Ǥ���
            $lcModel->updateAcLcInfo($lcInfo);
        }
    }
    return true;

}

// LCĢɼ���ϲ���-���쥯�ȥܥå����������
function getSelLcReport($objDB, $lcModel)
{
    $result["unloading_areas"] = fncGetPortplace($objDB);
    $result["bank_info"] = $lcModel->getBankList();
    return $result;
}

// LCĢɼ���ϲ���-��������
function exportLcReport($objDB, $lcModel, $data)
{
    //�̲߶�ʬ�ꥹ�Ȥμ���
    $currency_class_list = fncGetCurrencyClassList($objDB);
    //�̲߶�ʬ(̤��ǧ�ޤ�)�ꥹ�Ȥμ���
    $currency_class_list_all = fncGetCurrencyClassListAll($objDB);
    //��ԥꥹ�Ȥμ���
    $bank_info = $lcModel->getBankList();
    //���ϥե�����ξ���
    //$data ��������ˤ���ޤ���
    /*
    �ʲ�Ģɼ���Ϥν�����̤�����Ǥ���
    ���ߤ�Ajax�̿������������¹Ԥ���ޤ�����
    �ե��������Ϥ���Τ�Ʊ���̿��Ǥʤ���Ф����ʤ����⤷��ޤ���
    ���ξ��ϲ���������ϥե������<form>�����ǳ�ꡢ
    JS¦�����ϥ����å����SUBMIT��¹Ԥ���ή��ˤ���ɬ�פ�����ޤ���
     */
    return true;
}
