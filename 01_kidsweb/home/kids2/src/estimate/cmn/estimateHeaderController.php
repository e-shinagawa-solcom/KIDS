<?php

require_once ('conf.inc');
require_once (SRC_ROOT. "/estimate/cmn/const/workSheetConst.php");

/**
*	��������ȥإå����Υǡ��������å����饹
*	
*	�ʲ��Υ������Х��ѿ���������뤳��
*   @param object $objDB        �ǡ����١�����³���֥�������(clsDB�ޤ��ϷѾ����饹)
*   @param object $sheet        phpSpreadSheet�Υ����ȥ��֥�������
*   
*/

class estimateHeaderController {

    protected $errorMessage; // ���顼��å�����
    protected $messageCode;
    
    public $loginUserCode;

    // ��������
    protected $productCode;
    protected $productName;
    protected $productEnglishName;
    protected $retailPrice;
    protected $inchargeGroupCode;
    protected $customerUserName;
    protected $userCode;
    protected $cartonQuantity;
    protected $productionQuantity;

    // �ޥ������ǡ�������

    // ����̾�Υꥹ��
    protected static $nameList; // �إå��������Ϲ��ܤΥ���̾��
    protected static $titleNameList; // �إå����������ȥ���ܤΥ���̾��

    protected $headerTitleNameList; // ���Ϲ��ܤΥ����ȥ�ꥹ��

    protected $cellAddressList; // ����̾�Τ��б�����������֤Υꥹ��

    public function __construct() {

    }

    protected function setNameList() {
        if (!self::$nameList) {
            self::$nameList = workSheetConst::WORK_SHEET_HEADER_DATA_CELL;
        }
    }

    protected function setTitleNameList() {
        if (!self::$titleNameList) {
            self::$titleNameList = workSheetConst::WORK_SHEET_HEADER_TITLE_CELL;
        }
    }

    // ����̾�Τ��б���������Υꥹ�Ȥȹ��ֹ�ˤ�����ǡ�������
    public function initialize($cellAddressList, $loginUserCode) {
        $this->cellAddressList = $cellAddressList;
        $this->setNameList();
        $this->setTitleNameList();
        $params = $this->getCellParams();
        $this->params = $params;
        $this->setCellParams($params);
        $this->setCellTitleParams();
        $this->loginUserCode = $loginUserCode;
        return true;
    }

    // �ƹ��ܤΥǡ������������
    protected function getCellParams() {
        $nameList = self::$nameList;
        $cellAddressList = $this->cellAddressList;
        global $sheet;
        if ($nameList) {
            foreach ($nameList as $key => $cellName) {
                $cellAdress = $cellAddressList[$cellName];
                $param[$key] = $sheet->getCell($cellAdress)->getCalculatedValue();
            }
        } else {
            return false;
        }
        return $param;
    }

    // �ƹ��ܤΥ����ȥ�̾���������
    protected function setCellTitleParams() {
        $nameList = self::$titleNameList;
        $cellAddressList = $this->cellAddressList;
        global $sheet;
        if ($nameList) {
            foreach ($nameList as $key => $cellName) {
                $cellAdress = $cellAddressList[$cellName];
                $param[$key] = $sheet->getCell($cellAdress)->getCalculatedValue();
            }
        } else {
            return false;
        }
        $this->headerTitleNameList = $param;
        return true;
    }

    // ������Υǡ����������˥��åȤ���
    protected function setCellParams($data) {
        $this->productCode = $data['productCode'] ? $data['productCode'] : '';
        $this->productName = $data['productName'] ? $data['productName'] : '';
        $this->productEnglishName = $data['productEnglishName'] ? $data['productEnglishName'] : '';
        $this->retailPrice = $data['retailPrice'] ? $data['retailPrice'] : '';
        $this->inchargeGroupCode = $data['inchargeGroupCode'] ? $data['inchargeGroupCode'] : '';
        $this->customerUserName = $data['customerUserName'] ? $data['customerUserName'] : '';
        $this->userCode = $data['userCode'] ? $data['userCode'] : '';
        $this->cartonQuantity = $data['cartonQuantity'] ? $data['cartonQuantity'] : '';
        $this->productionQuantity = $data['productionQuantity'] ? $data['productionQuantity'] : '';
        return true;
    }

    // ��Ͽ�ѤΥǡ�������Ϥ���
    public function outputRegistData() {
        $registData = array(
            workSheetConst::PRODUCT_CODE => $this->productCode,
            workSheetConst::PRODUCT_NAME => $this->productName,
            workSheetConst::PRODUCT_ENGLISH_NAME => $this->productEnglishName,
            workSheetConst::RETAIL_PRICE => $this->retailPrice,
            workSheetConst::INCHARGE_GROUP_CODE => $this->inchargeGroupCodeNumber,
            workSheetConst::CUSTOMER_USER_NAME => $this->customerUserName,
            workSheetConst::USER_CODE => $this->userCodeNumber,
            workSheetConst::CARTON_QUANTITY => $this->cartonQuantity,
            workSheetConst::PRODUCTION_QUANTITY => $this->productionQuantity,
        );
        return $registData;
    }

    // �Х�ǡ�����������Ԥ�
    public function validate() {
        // ���顼�����ɤ򥻥åȤ���ؿ�
        $this->validateProductCode(); // ���ʥ�����
        $this->validateProductName(); // ����̾
        $this->validateProductEnglishName(); // ����̾(�Ѹ�)
        $this->validateRetailPrice(); // ����
        $this->validateInchargeGroupCode(); // �Ķ�����
        $this->validateUserCode(); // ô����
        $this->validateCartonQuantity(); // �����ȥ������
        $this->validateProductionQuantity(); // ���ѿ�
        
        // �������Х��DB���֥������Ȥ��������
        global $objDB;

        $loginUserCode = $this->loginUserCode;
        $inchargeGroupCodeNumber = $this->inchargeGroupCodeNumber;

        // ��������桼�������Ķ�����˽�°���뤫�����å�����
        if (!$this->messageCode['inchargeGroupCode']) {
            $result = $objDB->loginUserAffiliateCheck($loginUserCode, $inchargeGroupCodeNumber);
            if (!$result) {
                $this->messageCode['loginUser'] = 9202;
            }
        }

        if (!$this->messageCode['productCode'] && $this->productCode) {
            // ���ʥޥ����ȥ����ȤαĶ����𤬰��פ��뤫��ǧ����
            $currentRecord = $objDB->getCurrentRecordForProductCode($this->productCode);
            if($currentRecord !== false) {
                $groupDisplayCode = $currentRecord->strgroupdisplaycode;
                if ($groupDisplayCode != $inchargeGroupCodeNumber) {
                    $this->messageCode['inchargeGroupCode'] = 9202;
                }                
            }
        }
            
        $messageCodeList = $this->messageCode;
        $headerTitleNameList = $this->headerTitleNameList;

        if ($messageCodeList) {
            // ��å������˽��Ϥ�����ܤ򥻥åȤ���
            foreach ($messageCodeList as $key => $messageCode) {
                $message = '';
                switch ($messageCode) {
                    case 9001:
                        $str = "�إå�����". mb_convert_encoding($headerTitleNameList[$key], 'EUC-JP', 'UTF-8');
                        break;
                    case 9201:
                        $str = array(
                            "�إå���",
                            mb_convert_encoding($headerTitleNameList[$key], 'EUC-JP', 'UTF-8')
                        );
                        break;
                    case 9202:
                        $str = array(
                            "�إå���",
                            mb_convert_encoding($headerTitleNameList[$key], 'EUC-JP', 'UTF-8'),
                            $this->params[$key],
                        );
                        break;
                    default:
                        break;    
                }

                $message = fncOutputError($messageCode, DEF_WARNING, $str, FALSE, '', $objDB);

                if ($message) {
                    $errorMessage[] = $message;
                }
            }
        }
        return $errorMessage;
    }

    // ���顼��å��������Ϥ������ͤΥǡ�������Ϥ���

    // ���顼�����ɤ�¸�ߤ��뤫��ǧ����
    protected function messageCodeExist() {
        if ($this->messageCode) {
            return true;
        } else {
            return false;
        }
    }

    // ���ʥ����ɤ������ͤ�Х�ǡ�����󤹤�
    protected function validateProductCode() {
        $productCode = $this->productCode;
        // �Х�ǡ��������
        if (isset($productCode) && $productCode !=='') {
            if(!preg_match("/\A[0-9]{5}\z/", $productCode)) {
                // ���顼����
                $this->messageCode['productCode'] = 9201;
            } else {
                global $objDB;
                $record = $objDB->getRecordValue('m_product', 'strproductcode', $productCode);
                if ($record == false) {
                    // �ޥ����������å����顼
                    $this->messageCode['productCode'] = 9202;
                }
            }
        }
        return true;
    }

    // ����̾
    protected function validateProductName() {
        $productName = $this->productName;
        // �Х�ǡ��������
        if (!isset($productName) || $productName ==='') {
            // ���顼��å�����or���顼�����ɽ��ϡ�ɬ�ܥ��顼��
            $this->messageCode['productName'] = 9001; // ɬ��
        }
        return true;
    }

    // ����̾�ʱѸ��
    protected function validateProductEnglishName() {
        $productEnglishName = $this->productEnglishName;
        // ʸ��������å�(Ⱦ�ѱѿ������� ASCII��0x20���0x7e)
        if (isset($productEnglishName) && $productEnglishName !=='') {
            if(!preg_match("/\A[ -~]+\z/", $productEnglishName)) {
                // ���顼����
                $this->messageCode['productEnglishName'] = 9201;
            }
        } else {
            $this->messageCode['productEnglishName'] = 9001; // ɬ�ܥ����å�
        }
        return true;
    }

    // ����
    protected function validateRetailPrice() {
        $retailPrice = $this->retailPrice;
        if (isset($retailPrice) && $retailPrice !=='') {
            if(!preg_match("/\A[0-9]+\z/", $retailPrice)) {
                // ���顼����
                $this->messageCode['retailPrice'] = 9201;
            }
        } else {
            $this->messageCode['retailPrice'] = 9001; // ɬ�ܥ����å�
        }
        return true;
    }

    // �Ķ���������å�
    protected function validateInchargeGroupCode() {
        $inchargeGroupCode = $this->inchargeGroupCode;
        // �Х�ǡ��������
        if (isset($inchargeGroupCode) && $inchargeGroupCode !=='') {
            if (preg_match("/\A[0-9]+:.+\z/", $inchargeGroupCode)) {
                list ($inchargeGroupCodeNumber, $inchargeGroupCodeName) = explode(':', $inchargeGroupCode);
                global $objDB; // �������Х�Υǡ����١������֥������ȼ���
                $result = $objDB->getGroupRecordForDisplay($inchargeGroupCodeNumber);
                // �ޥ����������å�
                if (!$result) {
                    // �쥳���ɤ������Ǥ��ʤ��ä����
                    $this->messageCode['inchargeGroupCode'] = 9202;
                } else {
                    $this->inchargeGroupCodeNumber = $inchargeGroupCodeNumber; // ���롼�ץ����ɤ򥻥åȤ���
                }
            } else {
                // ���Ϸ�������
                $this->messageCode['inchargeGroupCode'] = 9201;
            }
        } else {
            // ɬ�ܥ��顼
            $this->messageCode['inchargeGroupCode'] = 9001;
        }
        return true;
    }

    // ��ȯô���ԤΥ����å���Ԥ�
    protected function validateUserCode() {
        $userCode = $this->userCode;
        // �Х�ǡ��������
        if (isset($userCode) && $userCode !=='') {
            if (preg_match("/\A[0-9]+:.+\z/", $userCode)) {
                list ($userCodeNumber, $userCodeName) = explode(':', $userCode);
                global $objDB; // �������Х�Υǡ����١������֥������ȼ���
                $result = $objDB->getUserRecordForDisplay($userCodeNumber);
                // �ޥ����������å�
                if (!$result) {
                    // �쥳���ɤ������Ǥ��ʤ��ä����
                    $this->messageCode['userCode'] = 9202;
                } else {
                    $this->userCodeNumber = $userCodeNumber; // ɽ����Υ桼���������ɤ򥻥åȤ���
                }
            } else {
                // ���Ϸ�������
                $this->messageCode['userCode'] = 9201;
            }
        } else {
            // ɬ�ܥ��顼
            $this->messageCode['userCode'] = 9001;
        }
        return true;
    }

    // �����ȥ������
    protected function validateCartonQuantity() {
        $cartonQuantity = $this->cartonQuantity;
        if (isset($cartonQuantity) && $cartonQuantity !=='') {
            if (!preg_match("/\A[1-9][0-9]*\z/", $cartonQuantity)) {
                // ���Ϸ�������
                $this->messageCode['cartonQuantity'] = 9201;
            }
        } else {
            // ɬ�ܥ��顼
            $this->messageCode['cartonQuantity'] = 9001;
        }
        return true;
    }

    // ���ѿ�
    protected function validateProductionQuantity() {
        $productionQuantity = $this->productionQuantity;
        if (isset($productionQuantity) && $productionQuantity !=='') {
            if (!preg_match("/\A[1-9][0-9]*\z/", $productionQuantity)) {
                // ���Ϸ�������
                $this->messageCode['productionQuantity'] = 9201;
            }
        } else {
            // ɬ�ܥ��顼
            $this->messageCode['productionQuantity'] = 9001;
        }
        return true;
    }
}