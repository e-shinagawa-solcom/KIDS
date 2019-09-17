<?php

require_once ('conf.inc');
require_once (SRC_ROOT. "/estimate/cmn/estimateRowController.php");
require_once (SRC_ROOT. "/estimate/cmn/const/workSheetConst.php");

class otherCostOrderRowController extends estimateRowController {
    protected static $customerCompanyCodeMaster; // 顧客先、仕入先マスター
    protected static $divisionSubjectCodeMaster; // 売上分類、仕入科目マスター(売上区分、仕入部品結合済み）

    protected static $headerNameList; // 対象エリアのヘッダーのセル名称
    protected static $resultNameList; // 対象エリアの計算結果のセル名称(明細最終行の次の行)

    // 取り込み値
    public $columnNumberList; // 列の番号リスト
    protected $columnDisplayNameList; // 列の表示名リスト

    protected $stockClassCode; // 仕入区分

    public function __construct($objDB) {
        $this->areaCode = DEF_AREA_OTHER_COST_ORDER; // エリアコードのセット
        $this->stockClassCode = workSheetConst::AREA_ATTRIBUTE_TO_STOCK_CLASS_CODE[$this->areaCode]; // 仕入区分のセット
        parent::__construct($objDB);        
    }

    protected function setNameList() {
        if (!static::$headerNameList) {
            static::$headerNameList = workSheetConst::ORDER_ELEMENT_COST_HEADER_CELL;
        }
        if (!static::$resultNameList) {
            static::$resultNameList = workSheetConst::ORDER_ELEMENT_COST_RESULT_CELL;
        }
    }

    // 売上分類のマスターのデータを取得する
    protected function setDivisionSubjectCodeMaster() {
        if (!static::$divisionSubjectCodeMaster) {
            $areaCode = $this->areaCode;
            $stockClassCode = $this->stockClassCode;
            $masterData = $this->objDB->getSubjectCodeList($areaCode);
            foreach ($masterData as $classCode => $data) {
                if ($classCode == $stockClassCode) {
                    $newMasterData = $data;
                    break;
                }
            }
            static::$divisionSubjectCodeMaster = $newMasterData;
        }
    }

    // 通貨レートマスターから納期に対応する通貨レートを取得する
    protected function getConversionRateForDelivery() {
        $monetary = $this->monetary;
        $delivery = $this->delivery;
        
        if (!$monetary) {
            $acquiredRate = null;
        } else if ($monetary == DEF_MONETARY_YEN) {
            $acquiredRate = 1;
        } else {
            if(!$delivery) {
                $acquiredRate = null;
            } else {
                $conversionRateMaster = self::$conversionRateMaster;
                if (!$conversionRateMaster[$monetary]) {
                    $acquiredRate = null;
                } else {
                    foreach ($conversionRateMaster[$monetary] as $data) {
                        // 納品日に対応する通貨レートを取得する（DBから取得したリスト内の検索）
                        if (strtotime($delivery) <= strtotime($data['endDate']) 
                            && strtotime($data['startDate']) <= strtotime($delivery)) {
                            $acquiredRate = $data['conversionRate'];
                        } else {
                            $acquiredRate = null;
                        }
                    }
                }
            }
        }
        return $acquiredRate;
    }
}