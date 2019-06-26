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
    // L/C�Խ��ν��ɽ�����٥��
    case 'getLcEdit':
        // L/C�������
        $result = getLcEdit($objDB, $lcModel, $data);
        break;
    // L/C�Խ��ι������٥��
    case 'updateLcEdit':
        //L/C����ι���
        $result = updateLcEdit($objDB, $lcModel, $data);
        break;
    // L/C�Խ��β�����٥��
    case 'releaseLcEdit':
        //�����ƤӽФ�
        $result = releaseLcEdit($objDB, $data);
        break;
}

$objDB->close();

//��̽���
mb_convert_variables('UTF-8', 'EUC-JP', $result);
echo $s->encodeUnsafe($result);


/**
 * LC������ѹ�����-�������
 *
 * @param [object] $objDB
 * @param [object] $lcModel
 * @param [array] $data
 * @return void
 */
function getLcEdit($objDB, $lcModel, $data)
{
    //ñ�Τ�L/C�������
    $result["lc_data"] = fncGetLcInfoSingle($objDB, $data);
    //�����ϼ���
    $result["portplace_list"] = fncGetPortplace($objDB);
    //��ԥꥹ�ȼ���
    $result["bank_list"] = $lcModel->getBankList();
    return $result;
}

/**
 * LC������ѹ�����-��������
 *
 * @param [object] $objDB
 * @param [array] $data
 * @return �������
 */
function updateLcEdit($objDB, $lcModel, $data)
{
    $bankreqchk = $data["bankreqchk"];
    if ($bankreqchk == "false") {
        if (intval($data["poreviseno"]) > 0) {
            $poreviseno = intval($data["poreviseno"]);
            do {
                $param = $data;
                $param["poreviseno"] = sprintf("%02d", $poreviseno + 1);
                // Ʊ��PO��ľ���Х����ǡ������������
                $lcinfo = fncGetLcInfoSingle($objDB, $param);
                // ���������ǡ����ζ�԰����������ξ�硢
                if (!$lcinfo) {
                    if ($lcinfo->bankreqdate != "") {
                        $data["bankreqdate"] = $lcinfo->bankreqdate;
                        $data["lcamopen"] = $lcinfo->lcamopen;
                        $data["validmonth"] = $lcinfo->validmonth;
                        break;
                    }
                }
                $poreviseno = $poreviseno - 1;
            } while ($poreviseno != 0);
        }
    }

    // �ѥ�᡼���ξ��� <> 7�ξ��
    if ($data["lcstate"] != 7) {
        if ($data["lcstate"] == 9) {
            $data["lcstate"] = 10;
        }
        $bankinfo = $lcModel->getAcBankInfo($data["bankcd"]);
        $data["bankname"] = $bankinfo->bankomitname;
        // L/C����ι���
        $result = fncUpdateLcinfo($objDB, $data);
    } else {
        // L/C����ι���
        $result = fncUpdateLcinfoToAmandCancel($objDB, $data);
    }

    return $result;
}

/**
 * LC�����ѹ�����-�������
 *
 * @param [object] $objDB
 * @param [array] $data
 * @return ������
 */
function releaseLcEdit($objDB, $data)
{

    $data = $data["lc_data"];

    // �ѥ�᡼���ξ��� = 3�ξ��
    if ($data["lcstate"] == 3) {
        if (intval($data["poreviseno"]) > 0) {
            $poreviseno = intval($data["poreviseno"]);
            do {
                $param = $data;
                $param["poreviseno"] = sprintf("%02d", $poreviseno + 1);
                // Ʊ��PO��ľ���Х����ǡ������������
                $lcinfo = fncGetLcInfoSingle($objDB, $param);
                // ���������ǡ����ζ�԰����������ξ�硢
                if ($lcinfo->bankreqdate != "") {
                    $data["bankreqdate"] = $lcinfo->bankreqdate;
                    $data["lcamopen"] = $lcinfo->lcamopen;
                    $data["validmonth"] = $lcinfo->validmonth;
                    break;
                }
                $poreviseno = $poreviseno - 1;
            } while ($poreviseno != 0);
        }

        if ($data["bankreqdate"] != "" && $data["poreviseno"] != "00") {
            $data["lcstate"] = 7;
        } else {
            $data["lcstate"] = 4;
        }
    } else if ($data["lcstate"] == 7) {
        $data["lcstate"] = 8;
    } else {
        $data["lcstate"] = 10;
    }
    // L/C����ξ��ֹ���
    $result = fncUpdateLcState($objDB, $data);

    return $result;
}

