<?php
// ----------------------------------------------------------------------------
/**
 *       ��帡�� ����������٥��
 *
 *       ��������
 *         ����女���ɡ���ӥ�����ֹ�ˤ��������������������
 *
 *       ��������
 *
 */
// ----------------------------------------------------------------------------
// �ɤ߹���
include 'conf.inc';
require LIB_FILE;
include 'JSON.php';
require SRC_ROOT . "po/cmn/lib_pos.php";

//�ͤμ���
$postdata = file_get_contents("php://input");
$aryData = json_decode($postdata, true);
$objDB = new clsDB();
$objAuth = new clsAuth();
$objDB->open("", "", "", "");
//JSON���饹���󥹥��󥹲�
$s = new Services_JSON();
//�ͤ�¸�ߤ��ʤ������̾�� POST �Ǽ�����
if ($aryData == null) {
    $aryData = $_POST;
}

$displayColumns = $aryData["displayColumns"];

// ���å�����ǧ
$objAuth = fncIsSession($_REQUEST["strSessionID"], $objAuth, $objDB);


// �������˰��פ���ȯ�����ɤ��������SQLʸ�κ���
$strQuery = fncGetPurchseOrderByOrderCodeSQL($_REQUEST["strOrderCode"], $_REQUEST["lngRevisionNo"]);

// �ͤ�Ȥ� =====================================
list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);
    // ���������Ǥ�����̾����
    for ($i = 0; $i < $lngResultNum; $i++) {
        $aryResult[] = $objDB->fetchArray($lngResultID, $i);
    }

$objDB->freeResult($lngResultID);

// �ơ��֥빽���Ǹ�����̤�������ȣԣ̷ͣ����ǽ��Ϥ���
$strHtml = fncSetPurchaseOrderHtml($displayColumns, $aryResult, null, false, $_REQUEST["rownum"]);

echo $strHtml;

