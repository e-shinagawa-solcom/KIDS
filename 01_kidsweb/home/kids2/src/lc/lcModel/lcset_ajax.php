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

require_once(LIB_DEBUGFILE);

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

$lgusrname = trim($objAuth->UserDisplayName);

//�������
$result = array();

//��������ʬ��
switch ($data['method']) {
    // LC�����ѹ����ɽ��
    case 'getLcSetting':
        // L/c����������
        $result = getLcSetting($objDB, $lcModel, $data);
        break;
    // L/C�����ѹ���ȿ�ǥ��٥��
    case 'updateLcSetting':
        // L/C���깹����ȿ��
        $result = updateLcSetting($objDB, $lcModel, $data, $lgusrname);
        break;
}

$objDB->close();
$lcModel->close();

//��̽���
mb_convert_variables('UTF-8', 'EUC-JP', $result);
echo $s->encodeUnsafe($result);

/**
 * LC����������
 *
 * @param [object] $lcModel
 * @param [array] $data
 * @return array
 */
function getLcSetting($objDB, $lcModel, $data)
{
    //������μ���
    $base_open_date = $lcModel->getBaseDate();
    //��Ծ���μ���
    $bank_info = fncGetBankInfo($objDB);
    //��ʧ�����μ���
    $payf_info = fncGetPayfInfo($objDB);

    $result["base_open_date"] = $base_open_date;
    $result["bank_info"] = $bank_info;
    $result["payf_info"] = $payf_info;

    return $result;
}

// LC������󹹿�
function updateLcSetting($objDB, $lcModel, $data, $lgusrname)
{
    $data = mb_convert_encoding($data, 'EUC-JP', 'UTF-8');
    //�����ǡ�������
    //������Ծ���
    $bankInfoChk = $data["send_data"]["bankInfoChk"];
    $bankInfos = $data["send_data"]["bank_info"];
    //���������
    $payfInfoChk = $data["send_data"]["payfInfoChk"];
    $payfInfos = $data["send_data"]["payf_info"];
    //�����
    $baseOpenDateChk = $data["send_data"]["baseOpenDateChk"];
    $baseOpenDate = $data["send_data"]["baseOpenDate"];

    // DB��������
    $objDB->transactionBegin();
    // DB��������
    $lcModel->transactionBegin();
    if ($bankInfoChk == "true") {
        // kidscore2�ؤζ�Ծ���ι���
        // kidscore2�ζ�Ծ���κ��
        fncDeleteBank($objDB);
        // kidscore2�ζ�Ծ������Ͽ
        foreach ($bankInfos as $bankInfo) {
            fncInsertBank($objDB, $bankInfo);
        }
        // ackids�ؤζ�Ծ���ι���
        $lcModel->updateBankInfo(fncGetBankInfo($objDB), $lgusrname);
    }

    if ($payfInfoChk == "true") {
        // kidscore2�ؤλ�ʧ�����ι���
        // kidscore2�λ�ʧ�����κ��
        fncDeletePayfinfo($objDB);
        // kidscore2�λ�ʧ��������Ͽ
        foreach ($payfInfos as $payfInfo) {
            fncInsertPayf($objDB, $payfInfo);
        }
        //��ʧ�����ι���
        $lcModel->updatePayfInfo($payfInfos, $lgusrname);
    }

    if ($baseOpenDateChk == "true") {
        //������ι���
        $lcModel->updateBaseOpenDate($baseOpenDate, $lgusrname);
    }

    // DB������λ
    $lcModel->transactionCommit();
    // DB������λ
    $objDB->transactionCommit();

    return $result;
}

