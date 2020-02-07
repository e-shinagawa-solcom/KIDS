<?php

require_once ('conf.inc');
require_once ( LIB_FILE );			// ライブラリ読み込み

// 定数ファイルの読み込み
require_once ( SRC_ROOT . "/estimate/cmn/const/workSheetConst.php");

class estimatePreviewController {
    
    // Database reference
    protected $objDB;

    // master
    protected $divisionCodeMaster;
    protected $subjectCodeMaster;
    protected $groupDisplayList;
    protected $companyDisplayList;
    protected $salesDivisionDisplayList;
    protected $salesClassDisplayList;
    protected $stockSubjectDisplayList;
    protected $stockItemDisplayList;

    public function __construct() {

    }

    public function dataInitialize($product, $estimate, $objDB) {
        $this->objDB = $objDB;
        $this->setMasterData();
        $this->setDisplayMasterData();
        $this->setTemplate();
        $this->setProduct($product);
        $this->setEstimate($estimate);
        $this->setEstimateStandardRate();
    }

    // マスターデータの取得
    protected function setMasterData() {
        $this->divisionCodeMaster = $this->objDB->getDivisionCodeList();
        $this->subjectCodeMaster = $this->objDB->getSubjectCodeList();
        return true;
    }

    // 表示用マスターデータの取得
    protected function setDisplayMasterData() {
        $this->setGroupDisplayList();
        $this->setCompanyDisplayList();
        $this->setSalesDivisionDisplayList();
        $this->setSalesClassDisplayList();
        $this->setStockSubjectDisplayList();
        $this->setStockItemDisplayList();
    }

    protected function setGroupDisplayList() {
        $table = 'm_group';
        $key = 'lnggroupcode';
        $columns = array(
            'strgroupdisplaycode',
            'strgroupdisplayname'
        );
        $this->groupDisplayList = $this->objDB->getMasterToArray($table, $key, $columns);
        return true;
    }

    protected function setCompanyDisplayList() {
        $table = 'm_company';
        $key = 'lngcompanycode';
        $columns = array(
            'strcompanydisplaycode',
            'strcompanydisplayname',
            'strshortname'
        );
        $this->companyDisplayList = $this->objDB->getMasterToArray($table, $key, $columns);
        return true;
    }

    protected function setSalesDivisionDisplayList() {
        $table = 'm_salesdivision';
        $key = 'lngsalesdivisioncode';
        $columns = 'strsalesdivisionname';
        $this->salesDivisionDisplayList = $this->objDB->getMasterToArray($table, $key, $columns);
        return true;
    }

    protected function setSalesClassDisplayList() {
        $table = 'm_salesclass';
        $key = 'lngsalesclasscode';
        $columns = 'strsalesclassname';
        $this->salesClassDisplayList = $this->objDB->getMasterToArray($table, $key, $columns);
        return true;
    }

    protected function setStockSubjectDisplayList() {
        $table = 'm_stocksubject';
        $key = 'lngstocksubjectcode';
        $columns = 'strstocksubjectname';
        $this->stockSubjectDisplayList = $this->objDB->getMasterToArray($table, $key, $columns);
        return true;
    }

    protected function setStockItemDisplayList() {
        $this->stockItemDisplayList = $this->objDB->getStockItemDisplayList();
        return true;
    }

    protected function setTemplate() {

        return true;
    }

    protected function setProduct($product) {
        $inchargeUserDisplay = $this->objDB->getUserDisplayInfo($product->lnginchargeusercode);

        if ($product->lngdevelopusercode) {
            $developUserDisplay = $this->objDB->getUserDisplayInfo($product->lngdevelopusercode);
        } else {
            $developUserDisplay = null;
        }

        if (!$this->groupDisplayList) {
            $this->setGroupDisplayList();
        }

        $groupDisplayCode = $this->groupDisplayList[$product->lnginchargegroupcode]['strgroupdisplaycode'];
        $groupDisplayName = $this->groupDisplayList[$product->lnginchargegroupcode]['strgroupdisplayname'];
        
        $data = array(
            workSheetConst::PRODUCT_CODE => $product->strproductcode . "_" . $product->strrevisecode,
            workSheetConst::PRODUCT_NAME => $product->strproductname,
            workSheetConst::PRODUCT_ENGLISH_NAME => $product->strproductenglishname,
            workSheetConst::RETAIL_PRICE => $product->curretailprice,
            workSheetConst::INCHARGE_GROUP_CODE => $groupDisplayCode. ":". $groupDisplayName,
            workSheetConst::INCHARGE_USER_CODE => $inchargeUserDisplay->struserdisplaycode. ":". $inchargeUserDisplay->struserdisplayname,
            workSheetConst::DEVELOP_USER_CODE => $developUserDisplay ? $developUserDisplay->struserdisplaycode. ":". $developUserDisplay->struserdisplayname : '',
            workSheetConst::CARTON_QUANTITY => $product->lngcartonquantity,
        );

        $this->product = $data;
        return true;
    }

    protected function setEstimate($estimate) {
        $companyDisplayList = $this->companyDisplayList;
        $salesDivisionDisplayList = $this->salesDivisionDisplayList;
        $salesClassDisplayList = $this->salesClassDisplayList;
        $stockSubjectDisplayList = $this->stockSubjectDisplayList;
        $stockItemDisplayList = $this->stockItemDisplayList;

        $monetaryDisplay = workSheetConst::MONETARY_DISPLAY_CODE;

        $divisionCodeMaster = $this->divisionCodeMaster;
        $subjectCodeMaster = $this->subjectCodeMaster;
        $areaAttribute = workSheetConst::AREA_ATTRIBUTE_TO_STOCK_CLASS_CODE;
        
        foreach ($estimate as $key => $record) {
            if (!isset($this->revisionNo)) {
                $this->revisionNo = $record->masterrevisionno;
            }
            if (!isset($this->insertDate)) {
                $this->insertDate = $record->dtminsertdate;
            }
            if ($record->lngsalesdivisioncode && $record->lngsalesclasscode) {
                // 売上
                $divisionCode = $record->lngsalesdivisioncode; // 売上分類
                $classCode = $record->lngsalesclasscode; // 売上区分
                                
                foreach ($divisionCodeMaster as $key => $list) {
                    if ($list[$divisionCode][$classCode]) {
                        $areaCode = $key;
                        break;
                    }
                }

                // 表示に必要なデータを取得する
                $divisionName = $salesDivisionDisplayList[$divisionCode]['strsalesdivisionname'];
                $className = $salesClassDisplayList[$classCode]['strsalesclassname'];
                $companyDisplayCode = $companyDisplayList[$record->lngcustomercompanycode]['strcompanydisplaycode'];
                $companyDisplayName = $companyDisplayList[$record->lngcustomercompanycode]['strshortname'];
                if ($companyDisplayName === null) {
                    $companyDisplayName = '';
                }
                
                $sortKey = $record->lngestimatedetailno;

                if (!$companyDisplayCode) {
                    $companyDisplay = '';
                } else {
                    $companyDisplay = $companyDisplayCode. ":". $companyDisplayName;
                }


                // 表示用データをセットする
                $ret[$areaCode][$sortKey] = array(
                    'divisionSubject' => $divisionCode. ":". $divisionName,
                    'classItem' => $classCode. ":". $className,
                    'customerCompany' => $companyDisplay,
                    'quantity' => $record->lngproductquantity,
                    'monetaryDisplay' => $monetaryDisplay[$record->lngmonetaryunitcode],
                    'price' => $record->curproductprice,
                    'conversionRate' => $record->curconversionrate,
                    'delivery' => $record->dtmdelivery,
                    'note' => $record->strnote,
                    'statusCode' => $record->lngreceivestatuscode,
                    'estimateDetailNo' => $record->lngestimatedetailno,
                    'receiveNo' => $record->lngreceiveno
                );

            } else if ($record->lngstocksubjectcode && $record->lngstockitemcode) {
                // 仕入
                $subjectCode = $record->lngstocksubjectcode; // 仕入科目
                $itemCode = $record->lngstockitemcode; // 仕入部品

                foreach ($subjectCodeMaster as $key => $list) {
                    $classCode = $areaAttribute[$key]; // 仕入区分
                    if ($list[$classCode][$subjectCode][$itemCode]) {
                        $areaCode = $key;
                        break;
                    };
                }

                // 表示に必要なデータを取得する
                $subjectName = $stockSubjectDisplayList[$subjectCode]['strstocksubjectname'];
                $itemName = $stockItemDisplayList[$subjectCode][$itemCode]['strstockitemname'];
                $companyDisplayCode = $companyDisplayList[$record->lngcustomercompanycode]['strcompanydisplaycode'];
                $companyDisplayName = $companyDisplayList[$record->lngcustomercompanycode]['strshortname'];

                if ($companyDisplayName === null) {
                    $companyDisplayName = '';
                }

                $sortKey = $record->lngestimatedetailno;

                if (!$companyDisplayCode) {
                    $companyDisplay = '';
                } else {
                    $companyDisplay = $companyDisplayCode. ":". $companyDisplayName;
                }

                $ret[$areaCode][$sortKey] = array(
                    'divisionSubject' => $subjectCode. ":". $subjectName,
                    'classItem' => $itemCode. ":". $itemName,
                    'customerCompany' => $record->bytpercentinputflag == "f" ? $companyDisplay : $record->curproductrate,
                    'payoff' => $record->bytpayofftargetflag == "t" ? '○' : '',
                    'quantity' => $record->lngproductquantity,
                    'monetaryDisplay' => $monetaryDisplay[$record->lngmonetaryunitcode],
                    'price' => $record->bytpercentinputflag == "f" ? $record->curproductprice : null,
                    'conversionRate' => $record->curconversionrate,
                    'delivery' => $record->dtmdelivery,
                    'note' => $record->strnote,
                    'statusCode' => $record->lngorderstatuscode,
                    'estimateDetailNo' => $record->lngestimatedetailno,
                    'orderNo' => $record->lngorderno
                );

                // 不要データの削除
                // パーセント入力フラグが有効な時は単価を入力しない
                if ($record->bytpercentinputflag == "t") {
                    unset($ret[$areaCode][$sortKey]['price']);
                }
            }
            $deliveryList[] = $record->dtmdelivery;
        }

        $this->estimate = $ret;

        $deliveryList = array_unique($deliveryList);

        $this->newestDate = max($deliveryList);
        $this->oldestDate = min($deliveryList);

        return true;
    }

    protected function setEstimateStandardRate() {
        $rate = $this->objDB->getEstimateStandardRate();
        $this->standardRate = $rate;
        return true;
    }


    public function getProduct() {
        $product = $this->product;
        if ($this->insertDate) {
            $product[workSheetConst::INSERT_DATE] = $this->insertDate;
        }
        return $product;
    }

    public function getEstimate() {
        return $this->estimate;
    }
}