<?php

require_once ('conf.inc');
require_once (SRC_ROOT. "/estimate/cmn/const/workSheetConst.php");

/**
*	��������ȥإå����Υǡ��������å����饹
*	
*   
*/

class estimateHeaderController {

    protected $errorMessage; // ���顼��å�����
    protected $messageCode;
    
    protected $loginUserCode;

    protected $objDB;

    // ��������
    protected $productCode;
    protected $productName;
    protected $productEnglishName;
    protected $retailPrice;
    protected $inchargeGroupCode;
    protected $inchargeUserCode;
    protected $developUserCode;
    protected $cartonQuantity;
    protected $productionQuantity;
    protected $calculatedProductionQuantity;

    // �ޥ������ǡ�������

    protected static $groupDislayNameMaster;
    protected static $userDisplayNameMaster;
    protected static $salesGroupMaster;
    protected static $developUserMaster;

    // ����̾�Υꥹ��
    protected static $nameList; // �إå��������Ϲ��ܤΥ���̾��
    protected static $titleNameList; // �إå����������ȥ���ܤΥ���̾��

    protected $headerTitleNameList; // ���Ϲ��ܤΥ����ȥ�ꥹ��

    protected $cellAddressList; // ����̾�Τ��б�����������֤Υꥹ��

    protected $reviseFlag; // ���Υե饰

    protected function __construct($objDB) {
        $this->objDB = $objDB;
        $this->setNameList();
        $this->setTitleNameList();
        $this->setGroupAndUserMaster();
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

    public function outputReviseFlag() {
        return $this->reviseFlag;
    }

    // �ޥ��������åȴؿ�
    protected function setGroupAndUserMaster() {
        if (!self::$groupDislayNameMaster
            || !self::$userDisplayNameMaster
            || !self::$salesGroupMaster
            || !self::$developUserMaster) {

           
            $datas = $this->objDB->getSalesGroupAndDevelopGroup();

            foreach ($datas as $data) {

                $attributeCode = (int)$data->lngattributecode;
                $groupDisplayCode = $data->strgroupdisplaycode;
                $groupDisplayName = $data->strgroupdisplayname;
                $userDisplayCode = $data->struserdisplaycode;
                $userDisplayName = $data->struserdisplayname;

                if (!$groupDislayNameMaster[$groupDisplayCode]) {
                    $groupDislayNameMaster[$groupDisplayCode] = $groupDisplayName;
                }

                if (!$userDisplayNameMaster[$userDisplayCode]) {
                    $userDisplayNameMaster[$userDisplayCode] = $userDisplayName;
                }
                
                if ($attributeCode === DEF_GROUP_ATTRIBUTE_CODE_SALES_GROUP) { // �Ķ�����ξ��

                    $salesGroupMaster[$groupDisplayCode][$userDisplayCode] = true;
                    
                } else if ($attributeCode === DEF_GROUP_ATTRIBUTE_CODE_DEVELOP_GROUP) { // ��ȯ����ξ��

                    $developUserMaster[$userDisplayCode] = true;

                }                
            }

            if (!self::$groupDislayNameMaster) {
                self::$groupDislayNameMaster = $groupDislayNameMaster;
            }

            if (!self::$userDisplayNameMaster) {
                self::$userDisplayNameMaster = $userDisplayNameMaster;
            }

            if (!self::$salesGroupMaster) {
                self::$salesGroupMaster = $salesGroupMaster;
            }

            if (!self::$developUserMaster) {
                self::$developUserMaster = $developUserMaster;
            }
        }

        return;
    }

    // ������Υǡ����������˥��åȤ���
    protected function setCellParams($data) {
        $this->productCode = isset($data['productCode']) ? $data['productCode'] : '';
        $this->productName = isset($data['productName']) ? $data['productName'] : '';
        $this->productEnglishName = isset($data['productEnglishName']) ? $data['productEnglishName'] : '';
        $this->retailPrice = isset($data['retailPrice']) ? $data['retailPrice'] : '';
        $this->inchargeGroupCode = isset($data['inchargeGroupCode']) ? $data['inchargeGroupCode'] : '';
        $this->inchargeUserCode = isset($data['inchargeUserCode']) ? $data['inchargeUserCode'] : '';
        $this->developUserCode = isset($data['developUserCode']) ? $data['developUserCode'] : '';
        $this->cartonQuantity = isset($data['cartonQuantity']) ? $data['cartonQuantity'] : '';
        $this->productionQuantity = isset($data['productionQuantity']) ? $data['productionQuantity'] : '';
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
            workSheetConst::INCHARGE_USER_CODE => $this->inchargeUserCodeNumber,
            workSheetConst::DEVELOP_USER_CODE => $this->developUserCodeNumber,
            workSheetConst::CARTON_QUANTITY => $this->cartonQuantity,
            workSheetConst::PRODUCTION_QUANTITY => $this->calculatedProductionQuantity ? $this->calculatedProductionQuantity : $this->productionQuantity,
        );
        return $registData;
    }

    // ɽ��̾��ɽ�������ɤ��礷���ǡ����Υꥹ�Ȥ���Ϥ���
    public function outputDisplayData() {
        $displayData = array(
            workSheetConst::INCHARGE_GROUP_CODE => $this->inchargeGroupCode,
            workSheetConst::INCHARGE_USER_CODE => $this->inchargeUserCode,
            workSheetConst::DEVELOP_USER_CODE => $this->developUserCode,
        );

        return $displayData;
    }

    // �Х�ǡ�����������Ԥ�
    public function validate() {
        // ���顼�����ɤ򥻥åȤ���ؿ�
        $this->validateProductCode(); // ���ʥ�����
        $this->validateProductName(); // ����̾
        $this->validateProductEnglishName(); // ����̾(�Ѹ�)
        $this->validateRetailPrice(); // ����
        $this->validateIncharge(); // �Ķ�����ô��
        $this->validateDevelopUserCode(); // ��ȯô����
        $this->validateCartonQuantity(); // �����ȥ������
        $this->validateProductionQuantity(); // ���ѿ�
        
        $loginUserCode = $this->loginUserCode;
        $inchargeGroupCodeNumber = $this->inchargeGroupCodeNumber;
        $salesGroupMaster = self::$salesGroupMaster; // �Ķ�����Υޥ����������

        // ������桼�������Ķ�����˽�°���뤫�����å�����
        if (!$this->messageCode['inchargeGroupCode'] && $inchargeGroupCodeNumber) {
            $result = $this->objDB->userCodeAffiliateCheck($loginUserCode, $inchargeGroupCodeNumber);
            if (!$result) {
                $this->messageCode['loginUser'] = DEF_MESSAGE_CODE_MASTER_CHECK_ERROR;
            }
        }
        
        $messageCodeList = $this->messageCode;
        $headerTitleNameList = $this->headerTitleNameList;

        if ($messageCodeList) {
            $str = '';
            // ��å������˽��Ϥ�����ܤ򥻥åȤ���
            foreach ($messageCodeList as $key => $messageCode) {
                $message = '';
                switch ($messageCode) {
                    case DEF_MESSAGE_CODE_NOT_ENTRY_ERROR:
                        $str = array(
                            "�إå���",
                            mb_convert_encoding($headerTitleNameList[$key], 'EUC-JP', 'UTF-8')
                        );
                        break;
                    case DEF_MESSAGE_CODE_FORMAT_ERROR:
                        $str = array(
                            "�إå���",
                            mb_convert_encoding($headerTitleNameList[$key], 'EUC-JP', 'UTF-8')
                        );
                        break;
                    case DEF_MESSAGE_CODE_MASTER_CHECK_ERROR:
                        $str = array(
                            "�إå���",
                            mb_convert_encoding($headerTitleNameList[$key], 'EUC-JP', 'UTF-8'),
                        );
                        break;
                    default:
                        break;    
                }

                $message = fncOutputError($messageCode, DEF_WARNING, $str, FALSE, '', $this->objDB);

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
                $this->messageCode['productCode'] = DEF_MESSAGE_CODE_FORMAT_ERROR;
            } else {
                $record = $this->objDB->getRecordValue('m_product', 'strproductcode', $productCode);
                if ($record == false) {
                    // �ޥ����������å����顼
                    $this->messageCode['productCode'] = DEF_MESSAGE_CODE_PRODUCT_CODE_ERROR;
                }
                $this->reviseFlag = true;
            }
        } else {
            $this->reviseFlag = false;
        }
        return true;
    }

    // ����̾
    protected function validateProductName() {
        $productName = $this->productName;
        // �Х�ǡ��������
        if (!isset($productName) || $productName ==='') {
            // ���顼��å�����or���顼�����ɽ��ϡ�ɬ�ܥ��顼��
            $this->messageCode['productName'] = DEF_MESSAGE_CODE_NOT_ENTRY_ERROR; // ɬ��
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
                $this->messageCode['productEnglishName'] = DEF_MESSAGE_CODE_FORMAT_ERROR;
            }
        } else {
            $this->messageCode['productEnglishName'] = DEF_MESSAGE_CODE_NOT_ENTRY_ERROR; // ɬ�ܥ����å�
        }
        return true;
    }

    // ����
    protected function validateRetailPrice() {
        $retailPrice = $this->retailPrice;
        if (isset($retailPrice) && $retailPrice !=='') {
            if(!is_numeric($retailPrice)) {
                // ���顼����
                $this->messageCode['retailPrice'] = DEF_MESSAGE_CODE_FORMAT_ERROR;
            } else {
                // �������ʲ���3�̤�ͼθ���
                $formattedValue = number_format(round($retailPrice, 2), 2, '.', '');
                $this->retailPrice = $formattedValue;
            }
        } else {
            $this->messageCode['retailPrice'] = DEF_MESSAGE_CODE_NOT_ENTRY_ERROR; // ɬ�ܥ����å�
        }
        return true;
    }

    // �Ķ������Ϣ�ΥХ�ǡ�������Ԥ�
    protected function validateIncharge() {
        $this->validateInchargeGroupCode(); // �Ķ�����
        $this->validateInchargeUserCode();  // ô��
        return;
    }

    // �Ķ���������å�
    protected function validateInchargeGroupCode() {
        $inchargeGroupCode = $this->inchargeGroupCode;

        $salesGroupMaster = self::$salesGroupMaster; // �ޥ������Υǡ��������

        // �Х�ǡ��������
        if (isset($inchargeGroupCode) && $inchargeGroupCode !=='') {
            if (preg_match("/\A[0-9]+:.+\z/", $inchargeGroupCode)) {
                list ($inchargeGroupCodeNumber, $inchargeGroupCodeName) = explode(':', $inchargeGroupCode);

                // $result = $this->objDB->getGroupRecordForDisplay($inchargeGroupCodeNumber);
                
                // �ޥ����������å�
                if (!$salesGroupMaster[$inchargeGroupCodeNumber]) {
                    // �Ķ������¸�ߤ��ʤ����
                    $this->messageCode['inchargeGroupCode'] = DEF_MESSAGE_CODE_MASTER_CHECK_ERROR;

                } else {

                    $this->inchargeGroupCodeNumber = $inchargeGroupCodeNumber; // ���롼�ץ����ɤ򥻥åȤ���

                    // ɽ��̾��DB�����ͤ��ִ�             
                    $groupDislayNameMaster = self::$groupDislayNameMaster;
                    $displayName = $groupDislayNameMaster[$inchargeGroupCodeNumber];
                    $this->inchargeGroupCode = $inchargeGroupCodeNumber. ':'. $displayName;

                }
            } else {
                // ���Ϸ�������
                $this->messageCode['inchargeGroupCode'] = DEF_MESSAGE_CODE_FORMAT_ERROR;
            }
        } else {
            // ɬ�ܥ��顼
            $this->messageCode['inchargeGroupCode'] = DEF_MESSAGE_CODE_NOT_ENTRY_ERROR;
        }
        return true;
    }

    // ô���ԤΥ����å���Ԥ�
    protected function validateInchargeUserCode() {
        $inchargeGroupCodeNumber = $this->inchargeGroupCodeNumber;

        if (!$inchargeGroupCodeNumber) {
            return false;
        }

        $inchargeUserCode = $this->inchargeUserCode;

        $salesGroupMaster = self::$salesGroupMaster; // �ޥ������Υǡ��������

        // �Х�ǡ��������
        if (isset($inchargeUserCode) && $inchargeUserCode !=='') {
            if (preg_match("/\A[0-9]+:.+\z/", $inchargeUserCode)) {
                list ($inchargeUserCodeNumber, $inchargeUserCodeName) = explode(':', $inchargeUserCode);

                // �ޥ����������å�
                if ($salesGroupMaster[$inchargeGroupCodeNumber][$inchargeUserCodeNumber] !== true) {
                    // �Ķ������¸�ߤ��ʤ��ޤ��ϥ桼���������ɤ�¸�ߤ��ʤ����
                    $this->messageCode['inchargeUserCode'] = DEF_MESSAGE_CODE_MASTER_CHECK_ERROR;
                } else {
                    $this->inchargeUserCodeNumber = $inchargeUserCodeNumber; // ɽ����Υ桼���������ɤ򥻥åȤ���

                    // ɽ��̾��DB�����ͤ��ִ�             
                    $userDisplayNameMaster = self::$userDisplayNameMaster;
                    $displayName = $userDisplayNameMaster[$inchargeUserCodeNumber];
                    $this->inchargeUserCode = $inchargeUserCodeNumber. ':'. $displayName;
                }
            } else {
                // ���Ϸ�������
                $this->messageCode['inchargeUserCode'] = DEF_MESSAGE_CODE_FORMAT_ERROR;
            }
        } else {
            // ɬ�ܥ��顼
            $this->messageCode['inchargeUserCode'] = DEF_MESSAGE_CODE_NOT_ENTRY_ERROR;
        }
        return true;
    }

    // ��ȯô���ԤΥ����å���Ԥ�
    protected function validateDevelopUserCode() {

        $developUserCode = $this->developUserCode;
        
        $developUserMaster = self::$developUserMaster;

        // �Х�ǡ��������
        if (isset($developUserCode) && $developUserCode !=='') {
            if (preg_match("/\A[0-9]+:.+\z/", $developUserCode)) {
                list ($developUserCodeNumber, $developUserCodeName) = explode(':', $developUserCode);

                // �ޥ����������å�
                if ($developUserMaster[$developUserCodeNumber] !== true) {
                    // ��ȯ���¸�ߤ��Ƥ��ʤ��ޤ��ϥ桼���������ɤ�¸�ߤ��ʤ����
                    $this->messageCode['developUserCode'] = DEF_MESSAGE_CODE_MASTER_CHECK_ERROR;
                } else {
                    $this->developUserCodeNumber = $developUserCodeNumber; // ɽ����Υ桼���������ɤ򥻥åȤ���

                    // ɽ��̾��DB�����ͤ��ִ�             
                    $userDisplayNameMaster = self::$userDisplayNameMaster;
                    $displayName = $userDisplayNameMaster[$developUserCodeNumber];
                    
                    $this->developUserCode = $developUserCodeNumber. ':'. $displayName;
                }
            } else {
                // ���Ϸ�������
                $this->messageCode['developUserCode'] = DEF_MESSAGE_CODE_FORMAT_ERROR;
            }
        } else {
            // ɬ�ܥ��顼
            $this->messageCode['developUserCode'] = DEF_MESSAGE_CODE_NOT_ENTRY_ERROR;
        }
        return true;
    }

    // �����ȥ������
    protected function validateCartonQuantity() {
        $cartonQuantity = $this->cartonQuantity;
        if (isset($cartonQuantity) && $cartonQuantity !=='') {
            if (!preg_match("/\A[1-9][0-9]*\z/", $cartonQuantity)) {
                // ���Ϸ�������
                $this->messageCode['cartonQuantity'] = DEF_MESSAGE_CODE_FORMAT_ERROR;
            }
        } else {
            // ɬ�ܥ��顼
            $this->messageCode['cartonQuantity'] = DEF_MESSAGE_CODE_NOT_ENTRY_ERROR;
        }
        return true;
    }

    // ���ѿ�
    protected function validateProductionQuantity() {
        $productionQuantity = $this->productionQuantity;
        if (isset($productionQuantity) && $productionQuantity !=='') {
            if (!preg_match("/\A\d+\z/", $productionQuantity)) {
                // ���Ϸ�������
                $this->messageCode['productionQuantity'] = DEF_MESSAGE_CODE_FORMAT_ERROR;
            }
        } else {
            // ɬ�ܥ��顼
            $this->messageCode['productionQuantity'] = DEF_MESSAGE_CODE_NOT_ENTRY_ERROR;
        }
        return true;
    }

    public function setProductionQuantity($value) {
        $this->calculatedProductionQuantity = $value;
        return;
    }
}