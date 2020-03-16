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

    // 再計算前の償却数と数量を比較し、再計算前の値と一致した場合は再計算後の償却数を代入する
    public function substitutePQForPrice($compare, $substitute) {
        // 数量を取得
        $quantity = $this->quantity;

        // 単価と再計算前の値が一致した場合のみ再計算結果を代入する。
        if ($quantity === $compare) {
            $this->quantity = $substitute;
            return true;
        }

        return false;
    }

    // 顧客先のチェックを行う
    protected function validateCustomerCompany() {
        $subject = explode(':', $this->divisionSubject)[0];
        $class = explode(':', $this->classItem)[0];
        if($subject != DEF_STOCK_SUBJECT_CODE_MATERIAL_PARTS_COST || $class != DEF_STOCK_ITEM_CODE_CERTIFICATE){ // 証紙以外はチェックしない
            $this->customerCompany = null;
            return true;
        }
        return parent::validateCustomerCompany();;
    }
}