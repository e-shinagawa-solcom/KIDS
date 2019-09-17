<?php

require_once ('conf.inc');
require_once (SRC_ROOT. "/estimate/cmn/const/workSheetConst.php");

// Composer�Υ����ȥ��ɥե������ɤ߹���
require_once ( LIB_COMPOSER_FILE );

use PhpOffice\PhpSpreadsheet\Cell\DataType;

/**
*	�Ԥ��ȤΥǡ��������å����饹
*	
*	�ʲ��Υ����Х��ѿ���������뤳��
*   @param object $objDB        �ǡ����١�����³���֥�������(clsDB�ޤ��ϷѾ����饹)
*   @param object $sheet        phpSpreadSheet�Υ����ȥ��֥�������
*   
*/

abstract class estimateRowController {

    abstract protected function setDivisionSubjectCodeMaster();

    abstract protected function setNameList(); // �оݥ��ꥢ���ȤΥ���̾�Τλ���

    protected $objDB;

    protected $errorMessage; // ���顼��å�����

    // ��������
    public $columnNumberList; // ����ֹ�ꥹ��
    protected $columnDisplayNameList; // ���ɽ��̾�ꥹ��

    // ����μ�����
    public $delivery;
    public $quantity;
    public $price;
    protected $divisionSubject;
    public $classItem;
    protected $subtotal;
    public $conversionRate;
    protected $monetaryDisplay;
    public $monetary;
    public $customerCompany;
    protected $note;

    protected $row;

    // ��Ͽ�Ѥ˥��åȤ�����
    public $divisionSubjectCode;
    public $classItemCode;
    public $customerCompanyCode;

    public $acquiredRate; // �ޥ�����������������̲ߥ졼��
    public $calculatedSubtotal; // ���פκƷ׻����
    public $percentInputFlag; // �ѡ���������ϥե饰

    public $invalidFlag;
    public $messageCode;

    protected $salesOrder;

    // static�ѿ���������������ѤΥǡ����ʥޥ������ǡ�����������ˤ򥻥å�
    // �ޥ������ǡ�������
    protected static $customerCompanyCodeMaster; // �ܵ��衢������ޥ�����
    protected static $divisionSubjectCodeMaster; // ���ʬ�ࡢ�������ܥޥ�����(����ʬ���������ʷ��Ѥߡ�
    protected static $conversionRateMaster; // �̲ߥ졼�ȥޥ�����

    // ����̾�Υꥹ��
    protected static $headerNameList; // �оݥ��ꥢ�Υإå����ʥ����ȥ�ˤΥ���̾��
    protected static $resultNameList; // �оݥ��ꥢ�η׻���̤Υ���̾��(���ٺǽ��Ԥμ��ι�)


    protected function __construct($objDB) {
        // �ޥ������ǡ����ɤ߹���
        $this->objDB = $objDB;
        $this->setSalesOrder();
        $this->setCustomerCompanyCodeMaster();
        $this->setDivisionSubjectCodeMaster();
        $this->setConversionRateMaster();
    }

    // �ܵ��衢������Υޥ������Υǡ������������
    protected function setCustomerCompanyCodeMaster() {
        if (!static::$customerCompanyCodeMaster) {
            $areaCode = $this->areaCode;
            $masterData = $this->objDB->getCustomerCompanyCodeList($areaCode);
            static::$customerCompanyCodeMaster = $masterData;
        }        
    }
    
    // �̲ߥ졼�ȥޥ������Υǡ������������
    protected function setConversionRateMaster() {
        if (!self::$conversionRateMaster) {
            $masterData = $this->objDB->getTemporaryRateList();
            self::$conversionRateMaster = $masterData;
        }
    }

    // ���ܤȥ����å�����ޥ��������б������������̾�μ�����)
    // ɽ���Ѥ˥ޥ�����̾��ɬ�פʾ�硢Ŭ��������ɲä����Ǥ��ɲä�Ԥ�����
    protected function setMasterForDetailInputItems() {
        $areaCode = $this->areaCode;
        if ($this->salesOrder === DEF_ATTRIBUTE_CLIENT) {
            // ����ξ��
            $divisionSubjectTable = 'm_salesdivision'; // ���ʬ��ޥ���
            $classItemTable = 'm_salesclass'; // ����ʬ�ޥ���
        } else if ($this->salesOrder === DEF_ATTRIBUTE_SUPPLIER) {
            // ȯ��ξ��
            $divisionSubjectTable = 'm_stocksubject'; // �������ܥޥ���
            $classItemTable = 'm_stockitem'; // �������ʥޥ���
        }
        $useMaster = array(
            'divisionSubject' => $divisionSubjectTable,
            'classItem' => $classItemTable,          
            'customerCompany' => 'm_company' // ��ҥޥ���
        );
        return $useMaster;
    }

    // ����̾�Τ��б���������Υꥹ�Ȥȹ��ֹ�ˤ�����ǡ�������
    public function initialize($cellAddressList, $row) {
        $this->cellAddressList = $cellAddressList;
        $this->row = $row;
        $this->setNameList();
        $this->setColumnNumberList();
        $this->setColumnDisplayNameList();
        $params = $this->getRowParams();
        $this->setRowParams($params);
        $this->setRowDataType();
        return true;
    }

    public function editInitialize ($params, $row) {
        $this->row = $row;
        $this->setNameList();
        $this->setRowParams($params);
    }

    // �ƹ��ܤ����ֹ���������
    protected function setColumnNumberList() {
        $headerNameList = static::$headerNameList;
        $cellAddressList = $this->cellAddressList;
        foreach ($headerNameList as $key => $name) {
            if (preg_match("/\A[A-Z]+[1-9][0-9]*\z/", $cellAddressList[$name])) {
                // ������֤ο�����ʬ������
                $columnNumber[$key] = preg_replace("/[1-9][0-9]*/", '', $cellAddressList[$name]);
            } else {
                return false;
            }
        }
        $this->columnNumberList = $columnNumber; 
        return true;
    }

    // �ƹ��ܤ�̾�Τ��������
    protected function setColumnDisplayNameList() {
        global $sheet;
        $headerNameList = static::$headerNameList;
        $cellAddressList = $this->cellAddressList;
        foreach ($headerNameList as $key => $name) {
            $cellAddress = $cellAddressList[$name];
            $displayName[$key] = $sheet->getCell($cellAddress)->getCalculatedValue();
        }
        $this->columnDisplayNameList = $displayName;
        return true;
    }

    // �ƹ��ܤΥǡ������������
    protected function getRowParams() {
        $columnNumberList = $this->columnNumberList;
        global $sheet;
        if ($columnNumberList) {
            $row = $this->row;
            foreach ($columnNumberList as $key => $column) {
                $cellAdress = $column.$row;
                if ($key == 'delivery') {
                    $param[$key] = $sheet->getCell($cellAdress)->getFormattedValue();
                } else {
                    $param[$key] = $sheet->getCell($cellAdress)->getCalculatedValue();
                }
            }
        } else {
            return false;
        }
        return $param;
    }

    // �ƹ��ܤΥǡ��������������
    protected function getRowDataType() {
        $columnNumberList = $this->columnNumberList;
        global $sheet;
        if ($columnNumberList) {
            $row = $this->row;
            foreach ($columnNumberList as $key => $column) {
                $cellAdress = $column.$row;
                $dataType[$key] = $sheet->getCell($cellAdress)->getDataType();
            }
        } else {
            return false;
        }
        return $dataType;
    }

    // ������Υǡ����������˥��åȤ���
    protected function setRowParams($data) {
        $this->delivery = $data['delivery'] ? $data['delivery'] : '';
        $this->quantity = $data['quantity'] ? $data['quantity'] : '';
        $this->price = $data['price'] ? $data['price'] : '';
        $this->divisionSubject = $data['divisionSubject'] ? $data['divisionSubject'] : '';
        $this->classItem = $data['classItem'] ? $data['classItem'] : '';
        $this->subtotal = $data['subtotal'] ? $data['subtotal'] : '';
        $this->conversionRate = $data['conversionRate'] ? number_format($data['conversionRate'], 6, '.', '') : '';
        $this->monetaryDisplay = $data['monetaryDisplay'] ? $data['monetaryDisplay'] : '';
        $this->monetary = $data['monetary'] ? (int)$data['monetary'] : ''; // ��Ӥ���Τ�int���Ǽ���
        $this->customerCompany = $data['customerCompany'] ? $data['customerCompany'] : '';
        $this->payoff = $data['payoff'] ? $data['payoff'] : '';
        $this->note = $data['note'] ? $data['note'] : '';
        return true;
    }

    // ����Υǡ������������ꤹ��
    public function setRowDataType() {
        $dataType = $this->getRowDataType();
        $this->dataType = $dataType;
        return true;
    }

    // ̵���ե饰�����ꤹ��
    public function setInvalidFlag() {
        $invalidFlag = $this->getInvalidFlag();
        $this->invalidFlag = $invalidFlag;
        return true;
    }

    // ̵���ե饰���������
    public function getInvalidFlag() {

        // ���ʬ�� or �������ܤΥ����å�
        $this->validateDivisionSubject();

        if ($this->messageCode['divisionSubject']) {
            // ���ʬ�� or �������ܤ����Ϥ����ΤǤʤ�����ɽ�����ʤ�
            return true;
        }

        // ����ʬ���������ʤΥ����å�
        $this->validateClassItem();

        if ($this->messageCode['classItem']) {
            // ����ʬ or �������ʤ����Ϥ����ΤǤʤ�����ɽ�����ʤ�
            return true;
        }

        // ͢�����ѡ����ǥե饰�����ꤹ��
        $this->setDistinctionFlag();

        // Ǽ���Υ����å�
        $this->validateDelivery();

        if ($this->messageCode['delivery']) {
            // Ǽ�������Ϥ����ΤǤʤ�������ɽ���ˤ���
            return true;
        }

        $quantity = $this->quantity; // ����
        $price = $this->price; // ñ��
        $conversionRate = $this->conversionRate; // Ŭ�ѥ졼��

        // ���̡�ñ����Ŭ�ѥ졼�Ȥ����ͷ��ʤ龮�סʷײ踶���ˤ�׻�����
        if (is_numeric($quantity) && is_numeric($price) && is_numeric($conversionRate)) {
            $calculatedSubtotal = $quantity * $price * $conversionRate;
            if ($calculatedSubtotal === 0) {
                return true;
            }
        } else {
            return true;
        }
        

        if ($this->importCostFlag || $this->tariffFlag) {
            // ���Ͻ񼰤��������
            $dataType = $this->dataType;
            if (!$dataType || $dataType['price'] === DataType::TYPE_FORMULA) {
                if (is_numeric($this->customerCompany)) {
                    $this->percentInputFlag = true;
                } else {
                    // ñ���������ξ�硢�ܵ���˿��ͤ����äƤ��ʤ�����̵���ˤ���
                    return true;
                }
            }
        } else {
            // �ܵ���Υ����å���Ԥ�
            $this->validateCustomerCompany();
            if ($this->messageCode['customerCompany']) {
                // �ܵ�������Ϥ����ΤǤʤ������ɽ��
                return true;
            }
        }

        // �̲ߥ졼�ȤΥ����å���Ԥ�
        $this->validateConversionRate();

        if ($this->messageCode['conversionRate'] === 9203) {
            // �̲ߥ졼�Ȥ������Ǥ��ʤ��ä����
            return true;
        }
 
        return false;
    }



    // ñ���κƷ׻���Ԥ�
    protected function calcuratePrice() {
        $quantity = $this->quantity;
        $subtotal = $this->subtotal;
        $acqiredRate = $this->acqiredRate;
        if($quantity || $subtotal || $acqiredRate) {
            $recalculatedPrice = $subtotal / $quantity / $acqiredRate;
        }
        return $recalculatedPrice;
    }

    // ñ���η��Ĵ�������פκƷ׻���Ԥ�
    protected function resettingPriceAndSubtotal() {
        $price = $this->price;
        $monetary = $this->monetary;

        // �̲ߤ��Ȥ�ñ���ξ������ʲ��η�������
        $decimalDigit = workSheetConst::PRICE_DECIMAL_DIGIT;

        // ñ���ξ������ʲ��ν���
        $price = floor($price * pow(10, $decimalDigit[$monetary])) / pow(10, $decimalDigit[$monetary]);
        $price = number_format($price, 4, '.', '');

        // �Ʒ׻���̤򥻥åȤ���
        $this->price = $price;
        
        // ���פη׻���Ԥ�
        $this->calculateSubtotal();

        return true;
    }

    public function calculateSubtotal() {
        $quantity = $this->quantity;
        $price = $this->price;
        $acquiredRate = $this->acquiredRate;

        if(is_numeric($quantity) || is_numeric($price) || is_numeric($acquiredRate)) {
            $calculatedSubtotal = $quantity * $price;
            $calculatedSubtotalJP = $quantity * $price * $acquiredRate;
        }
        // �׻��ͤ�����
        $this->calculatedSubtotal = $calculatedSubtotal;
        $this->calculatedSubtotalJP = $calculatedSubtotalJP;
        
        return true;
    }

    // ��Ͽ�ѤΥǡ�������Ϥ���
    public function outputRegistData() {
        if (!isset($this->percentInputFlag)) {
            $this->percentInputFlag = false;
        }
        $registData = array(
            'salesOrder' => $this->salesOrder, // ����ޤ���ȯ��
            'delivery' => $this->delivery,
            'quantity' => $this->quantity,
            'price' => $this->price,
            'divisionSubject' => $this->divisionSubjectCode,
            'classItem' => $this->classItemCode,
            'subtotal' => $this->calculatedSubtotal, // �Ʒ׻���̤����
            'conversionRate' => $this->acquiredRate, // DB������������̲ߥ����ɤ����
            'monetary' => $this->monetary,
            'customerCompany' => $this->customerCompanyCode,
            'payoff' => $this->payoff,
            'percentInputFlag' => $this->percentInputFlag,
            'percent' => $this->percent
        );
        return $registData;
    }

    // ���ֹ����Ϥ���
    public function outputRow() {
        return $this->row;
    }
    


    // ���顼�Ѥ˽��Ϥ���ѥ�᡼���򥻥åȤ���
    protected function outputErrorValueList() {
        $errorValue = array(
            'delivery' => $this->delivery,
            'quantity' => $this->quantity,
            'price' => $this->price,
            'divisionSubject' => $this->divisionSubjectCode,
            'classItem' => $this->classItemCode,
            'subtotal' => $this->subtotal,
            'conversionRate' => $this->conversionRate,
            'monetaryDisplay' => $this->monetaryDisplay,
            'monetary' => $this->monetary,
            'customerCompany' => $this->customerCompanyCode,
            'payoff' => $this->payoff
        );
        return $errorValue;
    }


    // ɽ����Ƚ��˻��Ѥ�����ܤ����ꤹ��
    protected function makeInputDataCheckListForDisplay() {
        $checkList = array(
            'price',
            'monetary',
            'delivery',
            'quantity'
        );
        return $checkList;
    }

    // ���顼��å��������Ϲ��ܤ������������ܤ����ꤹ��
    protected function makeExclusionList() {
        $checkList = array(
            'price',
            'monetary',
            'delivery',
            'quantity',
            'conversionRate',
            'subtotal'
        );
        return $checkList;
    }

    // ���������������Υ�������ȥ����å���Ԥ�
    public function workSheetSelectCheck() {
        // ̵���ե饰�����ꤹ��
        $this->setInvalidFlag();
        return true;
    }

    // ����ȯ���Ƚ�ꤷ���åȤ���
    protected function setSalesOrder() {
        $areaCode = $this->areaCode;
        $salesOrderList = workSheetConst::ORDER_ATTRIBUTE_FOR_TARGET_AREA;
        if (isset($salesOrderList[DEF_ATTRIBUTE_CLIENT][$areaCode])) {
            $salesOrder = DEF_ATTRIBUTE_CLIENT;
        } else if (isset($salesOrderList[DEF_ATTRIBUTE_SUPPLIER][$areaCode])) {
            $salesOrder = DEF_ATTRIBUTE_SUPPLIER;
        }
        $this->salesOrder = $salesOrder;
        return true;
    }

    // ��Ͽ���̰ܹԻ��ΥХ�ǡ���������
    public function workSheetRegistCheck() {
        $errorMessage = '';
        $this->setInvalidFlag();
        if ($this->invalidFlag === false) {
            if (!$this->importCostFlag && !$this->tariffFlag) {
                // ñ���η��������Ⱦ��פκƷ׻�
                $this->resettingPriceAndSubtotal();
            }
        }
        return;
    }
    
    // �������ܡ��������ʤˤ����̥ե饰������ʥ��㡼���������
    protected function setDistinctionFlag() {
        $messageCodeList = $this->messageCodeList;
        $divisionSubjectCode = $this->divisionSubjectCode;
        $classItemCode = $this->classItemCode;
        
        if (!$messageCodeList['divisionSubject'] && !$messageCodeList['classItem']) {
            switch ($divisionSubjectCode) {
                case DEF_STOCK_SUBJECT_CODE_CHARGE:
                    switch ($classItemCode) {
                        case DEF_STOCK_ITEM_CODE_IMPORT_COST:
                            $this->importCostFlag = true; // ͢�����ѥե饰
                            break;
                        case DEF_STOCK_ITEM_CODE_TARIFF:
                            $this->tariffFlag = true; // ���ǥե饰
                            break;
                        default:
                            break;
                    }
                    $this->chargeFlag = true; // 
                    break;

                case DEF_STOCK_SUBJECT_CODE_EXPENSE:
                    $this->expenseFlag = true; // 
                    break;

                default:
                    break;
            }
        }
        return true;
    }


    // ��å���������Ƭ��ɽ������ʸ������������롧���ʬ��ʻ������ܡˡ�����ʬ�ʻ������ʡ�
    protected function setPrefixOfMessage() {
        $divisionSubjectPrefix = '';
        $classItemPrefix = '';
        $columnDisplayNameList = $this->$columnDisplayNameList;
        if (strlen($this->divisionSubject)) {
            $divisionSubject = $this->divisionSubject;
            $divisionSubjectDisplayName = $columnDisplayNameList['divisionSubject'];
            $divisionSubjectPrefix = $divisionSubjectDisplayName. ' ' .$divisionSubject;
        }
        if (strlen($this->classItem)) {
            $classItem = $this->classItem;
            $classItemDisplayName = $columnDisplayNameList['classItem'];
            $classItemPrefix = $classItemDisplayName. ' ' .$classItem;
        }
        $prefix = $divisionSubjectPrefix.', '. $classItemPrefix;
        return $prefix;
    }

    // ͢�����ѡ����Ǥν���
    public function chargeCalculate($conditionalTotal) {
        $monetary = $this->monetary;
        if ($monetary !== DEF_MONETARY_YEN) {
            $this->invalidFlag = true;
            return false;
        }

        $quantity = $this->quantity; // ����

        if ($this->percentInputFlag) {
            // �ѡ�������ͤμ���
            $percent = $this->customerCompany;
            // ñ���κƷ׻�
            $price = $percent * $conditionalTotal / $quantity;

            // ������ν����ͤ����ˤ���
            $this->customerCompany = null;
            // �ѡ�������ͤ򥻥åȤ���
            $this->percent = $percent;
        }

        // �̲ߤ��Ȥ�ñ���ξ������ʲ��η�������
        $decimalDigit = workSheetConst::PRICE_DECIMAL_DIGIT;

        // ��������4��ʲ��ڤ�Τ�
        $price = floor($price * pow(10, $decimalDigit[$monetary])) / pow(10, $decimalDigit[$monetary]);
        $price = number_format($price, 4, '.', '');

        // �Ʒ׻���̤��ִ�
        $this->price = $price;
        
        $conversionRate = $this->acquiredRate; // �ޥ���������̲ߥ졼��

        // ���פκƷ׻�
        $calculatedSubtotal = $price * $conversionRate * $quantity;
        $this->calculatedSubtotal = $calculatedSubtotal;
        $this->calculatedSubtotalJP = $calculatedSubtotal;

        return true;
    }

    // �ܵ��褬����ξ�硢


    //----------------------------------------------------------------------------------------------------
    
    // �Х�ǡ������ؿ�

    //----------------------------------------------------------------------------------------------------

    // Ǽ���ΥХ�ǡ������
    protected function validateDelivery() {
        $delivery = $this->delivery;
        if (isset($delivery) && $delivery !=='') {
            if (preg_match("/\A(\d{4})\/(0?[1-9]|1[0-2])\/(0?[1-9]|[12][0-9]|3[01])\z/", $delivery)) {
                list ($year, $month, $day) = explode('/', $delivery);
                if (!checkdate($month, $day, $year)) {
                    // ¸�ߤ��ʤ����ե��顼
                    $this->messageCode['delivery'] = 9201;
                }
            } else {
                // ���Ϸ�������
                $this->messageCode['delivery'] = 9201;
            }
        } else {
            // ɬ�ܥ��顼
            $this->messageCode['delivery'] = 9001;
        }
        return true;
    }

    // ���̤������ͤ�Х�ǡ�����󤹤�
    protected function validateQuantity() {
        $quantity = $this->quantity;
        // �Х�ǡ��������
        if (isset($quantity) && $quantity !=='') {
            if(!preg_match("/\A[1-9][0-9]*\z/", $quantity)) {
                // ���顼����
                $this->messageCode['quantity'] = 9201;
            } else if ((int)$quantity > 2147483647) {
                $this->messageCode['quantity'] = 9201;
            }
        } else {
            $this->messageCode['quantity'] = 9001; // ɬ�ܥ����å�
        }
        return true;
    }

    // ñ��
    protected function validatePrice() {
        $price = $this->price;
        // �Х�ǡ��������
        if (isset($price) && $price !=='') {
            if(!preg_match("/\A-?[0-9]*\.?[0-9]\z/", $price)) { // �������ʲ��η���ˤ�ä�Ƚ��
                // ���顼����
                $this->messageCode['price'] = 9201;
            }
        } else {
            // ���顼��å�����or���顼�����ɽ��ϡ�ɬ�ܥ��顼��
            $this->messageCode['price'] = 9001; // ɬ��
        }
        return true;
    }

    // �̲�
    protected function validateMonetary() {
        $monetary = $this->monetary;
        if (!isset($monetary) || $monetary === '') {
            // �����Ǥ��ʤ��ä�����JP�Ȥ��ƽ����򤹤�
            $this->monetary = DEF_MONETARY_YEN;
        }
        return true;
    }

    // ���ʬ�ࡢ��������
    protected function validateDivisionSubject() {
        $divisionSubject = $this->divisionSubject;
        if (isset($divisionSubject) && $divisionSubject !=='') {
            // ʸ��������å�
            if (preg_match("/\A[0-9]+:.+\z/", $divisionSubject)) {
                list ($divisionSubjectCode, $divisionSubjectName) = explode(':', $divisionSubject);
                $masterData = static::$divisionSubjectCodeMaster;

                $this->divisionSubjectCode = (int)$divisionSubjectCode;

                // �ޥ����������å�
                if (!isset($masterData[(int)$divisionSubjectCode])) {
                    // �ޥ����������å����顼
                    $this->messageCode['divisionSubject'] = 9202;
                }

            } else {
                // �񼰥��顼
                $this->messageCode['divisionSubject'] = 9201;
            }
        } else {
            // ɬ�ܥ��顼
            $this->messageCode['divisionSubject'] = 9001;
        }
        return true;
    }

    // ����ʬ����������
    protected function validateClassItem() {
        $classItem = $this->classItem;
        // �Х�ǡ��������
        if (isset($classItem) && $classItem !=='') {
            if (preg_match("/\A[0-9]+:.+\z/", $classItem)) {
                list ($classItemCode, $classItemName) = explode(':', $classItem);
                $masterData = static::$divisionSubjectCodeMaster;
                $divisionSubjectCode = $this->divisionSubjectCode;
                $this->classItemCode = (int)$classItemCode;
                // �ޥ����������å�
                if (!isset($masterData[$divisionSubjectCode][(int)$classItemCode])) {
                    $this->messageCode['classItem'] = 9202;
                }
            } else {
                // ���Ϸ�������
                $this->messageCode['classItem'] = 9201;
            }
        } else {
            // ɬ�ܥ��顼
            $this->messageCode['classItem'] = 9001;
        }
        return true;
    }


    // �̲ߥ졼�ȤΥ����å���Ԥ���DB���̲ߥ졼�Ȥ���Ϥ���
    protected function validateConversionRate() {
        $conversionRate = $this->conversionRate;
        $acquiredRate = $this->getConversionRateForDelivery(); // DB������������̲ߥ졼��
        if ($acquiredRate) {
            $this->acquiredRate = $acquiredRate;
            if ($acquiredRate !== DEF_MONETARY_YEN  && $conversionRate !== $acquiredRate) {
                // DB������������̲ߥ졼�Ȥȥ����Ȥ�����������̲ߥ졼�Ȥ��ۤʤ���
                $this->messageCode['conversionRate'] = 9206;
            }
        } else {
            // DB�����̲ߥ졼�Ȥ������Ǥ��ʤ��ä����
            $this->messageCode['conversionRate'] = 9203;
        }
        return true;
    }

    // �ܵ���
    protected function validateCustomerCompany() {
        $customerCompany = $this->customerCompany;
        if (isset($customerCompany) && $customerCompany !=='') {
            if (preg_match("/\A[0-9]+:.+\z/", $customerCompany)) {
                list ($customerCompanyCode, $customerCompanyName) = explode(':', $customerCompany);
                $masterData = static::$customerCompanyCodeMaster;
                $this->customerCompanyCode = (string)$customerCompanyCode;
                // �ޥ����������å�
                if (!isset($masterData[$customerCompanyCode])) {
                    $this->messageCode['customerCompany'] = 9202;
                }
                $display = $masterData[$customerCompanyCode]['shortName'] ? $masterData[$customerCompanyCode]['shortName'] : $masterData[$customerCompanyCode]['displayName'];
                $this->customerCompany = $customerCompanyCode. ':'. $display;
            } else {
                // ���Ϸ�������
                $this->messageCode['customerCompany'] = 9201;
            }
        } else {
            if ($this->salesOrder === DEF_ATTRIBUTE_CLIENT) {
                // ����ξ��ɬ�ܥ��顼�����
                $this->messageCode['customerCompany'] = 9001;
            } else if ($this->salesOrder === DEF_ATTRIBUTE_SUPPLIER) {
                // ȯ��ξ���'0000'�򥻥å�
                $customerCompanyCode = (string)DEF_DISPLAY_COMPANY_CODE_OTHERS;
                $this->customerCompanyCode = $customerCompanyCode;
                $masterData = static::$customerCompanyCodeMaster;
                $display = $masterData[$customerCompanyCode]['shortName'] ? $masterData[$customerCompanyCode]['shortName'] : $masterData[$customerCompanyCode]['displayName'];
                $this->customerCompany = $customerCompanyCode. ':'. $display;
            }
        }
        return true;
    }

    // ���פΥ����å���Ԥ�
    protected function validateSubtotal() {
        $subtotal = $this->subtotal;
        $acquiredRate = $this->acquiredRate; // DB������������̲ߥ졼��
        $quantity = $this->quantity;
        $price = $this->price;

        if ($acquiredRate && $quantity && $price) {
            $calculatedSubtotal = $quantity * $price * $acquiredRate;
        }

        if (!isset($calculatedSubtotal) || !is_numeric($calculatedSubtotal)) {
            // ���̡�ñ�����̲ߥ졼�ȤΥ����å����Ԥ��Ƥ���д���Ū�ˤ��̤�ʤ�����
            $this->messageCode['subtotal'] = 9204;
            return false;
        } else {
            if ($subtotal != $calculatedSubtotal) {
                // �׻���̤ȥ����Ȥ�������������סʷײ踶���ˤ��ۤʤ���
                if ($this->messageCode['conversionRate'] !== 9206) {
                    // �̲ߥ졼�Ȥ��ѹ����ʤ���Х�å����������ɤ򥻥å�
                    $this->messageCode['subtotal'] = 9205;
                }
            }
            $this->calculatedSubtotal = $calculatedSubtotal;
            return true;
        }
    }
    
    // ����
    protected function validatePayoffFlag() {
        $payoff = $this->payoff;
        // ���Ϸ�������
        if (!$payoff == '��' && !$payoff == '') {
            $this->messageCode['payoff'] = 9201;
        }
        return true;
    }
    
    // �оݥ��ꥢ�ι��ϰϤ��������(���ϹԤȽ�λ��)
    protected function getRowRangeOfTargetArea($addressListOfCellName) {
        // �оݥ��ꥢ�Υ���̾�μ���
        $headerNameList = $this->headerNameList;
        $resultNameList = $this->resultNameList;
        // �إå�������ӥեå����ʷ׻���̡ˤΥ���̾�Τ�1�ĥ��åȤ���
        $upperCellName = $this->getFirstElement($headerNameList);
        $belowCellName = $this->getFirstElement($resultNameList);

        // ����̾�Τ�����ֹ���������
        $upperRow = $this->getRowNumberFromCellName($addressListOfCellName, $upperCellName);
        $belowRow = $this->getRowNumberFromCellName($addressListOfCellName, $belowCellName);
        $rows = array(
            'firstRow' => $upperRow +1,
            'lastRow' => $belowRow -1,
        );
        return $rows;
    }

    // �̲ߥ졼�ȥޥ���������Ǽ�����б������̲ߥ졼�Ȥ��������
    protected function getConversionRateForDelivery() {
        $monetary = $this->monetary;
        $delivery = $this->delivery;
        
        if (!$monetary || !$delivery) {
            $acquiredRate = null;
        } else if ($monetary == DEF_MONETARY_YEN) {
            $acquiredRate = 1;
        } else {
            $conversionRateMaster = self::$conversionRateMaster;
            if (!$conversionRateMaster[$monetary]) {
                $acquiredRate = null;
            } else {
                foreach ($conversionRateMaster[$monetary] as $data) {
                    // Ǽ�������б������̲ߥ졼�Ȥ���������DB������������ꥹ����θ�����
                    if (strtotime($delivery) <= strtotime($data['endDate']) 
                        && strtotime($data['startDate']) <= strtotime($delivery)) {
                        $acquiredRate = $data['conversionRate'];
                        break;
                    } else {
                        $acquiredRate = null;
                    }
                }
            }
        }
        return $acquiredRate;
    }

    // �̲ߥ졼�Ȥκ�ʬ�ǡ������������
    protected function makeDifferenceData() {
        $delivery = $this->delivery;
        $monetary = $this->monetary;
        $sheerConversionRate = $this->conversionRate;
        $acquiredRate = $this->acquiredRate;
        $this->difference = array(
            'delivery' => $delivery,
            'monetary' => $monetary,
            'sheetRate' => strlen($sheerConversionRate) ? number_format($sheerConversionRate, 6, '.', '') : '',
            'temporaryRate' => strlen($acquiredRate) ? number_format($acquiredRate, 6, '.', '') : ''
        );
    }
}