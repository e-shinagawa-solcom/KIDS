<?php

require_once ('conf.inc');
require_once (SRC_ROOT. "/estimate/cmn/estimateRowController.php");
require_once (SRC_ROOT. "/estimate/cmn/const/workSheetConst.php");

class fixedCostSalesRowController extends estimateRowController {

    protected static $customerCompanyCodeMaster; // 顧客先、仕入先マスター
    protected static $divisionSubjectCodeMaster; // 売上分類、仕入科目マスター(売上区分、仕入部品結合済み）

    protected static $headerNameList; // 対象エリアのヘッダーのセル名称
    protected static $resultNameList; // 対象エリアの計算結果のセル名称(明細最終行の次の行)

    // 取り込み値
    public $columnNumberList; // 列の番号リスト
    protected $columnDisplayNameList; // 列の表示名リスト

    public function __construct($objDB) {
        $this->areaCode = DEF_AREA_FIXED_COST_SALES; // エリアコードのセット
        parent::__construct($objDB);        
    }

    protected function setNameList() {
        if (!static::$headerNameList) {
            static::$headerNameList = workSheetConst::RECEIVE_FIXED_COST_HEADER_CELL;
        }
        if (!static::$resultNameList) {
            static::$resultNameList = workSheetConst::RECEIVE_FIXED_COST_RESULT_CELL;
        }
    }

    // 売上分類のマスターのデータを取得する
    protected function setDivisionSubjectCodeMaster() {
        if (!static::$divisionSubjectCodeMaster) {
            $areaCode = $this->areaCode;
            $masterData = $this->objDB->getDivisionCodeList($areaCode);
            static::$divisionSubjectCodeMaster = $masterData;
        }
    }
}