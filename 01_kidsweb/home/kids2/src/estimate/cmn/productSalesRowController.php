<?php

require_once ('conf.inc');
require_once (SRC_ROOT. "/estimate/cmn/estimateRowController.php");
require_once (SRC_ROOT. "/estimate/cmn/const/workSheetConst.php");

class productSalesRowController extends estimateRowController {

    protected static $customerCompanyCodeMaster; // 顧客先、仕入先マスター
    protected static $divisionSubjectCodeMaster; // 売上分類、仕入科目マスター(売上区分、仕入部品結合済み）

    protected static $headerNameList; // 対象エリアのヘッダーのセル名称
    protected static $resultNameList; // 対象エリアの計算結果のセル名称(明細最終行の次の行)

    protected static $productionQuantity = 0; // 償却数計算結果

    protected static $PQOutputFlag;

    // 取り込み値
    public $columnNumberList; // 列の番号リスト
    protected $columnDisplayNameList; // 列の表示名リスト

    public function __construct($objDB) {
        $this->areaCode = DEF_AREA_PRODUCT_SALES; // エリアコードのセット
        parent::__construct($objDB);
    }

    protected function setNameList() {
        if (!static::$headerNameList) {
            static::$headerNameList = workSheetConst::PRODUCT_SALES_HEADER_NAME_LIST;
        }
        if (!static::$resultNameList) {
            static::$resultNameList = workSheetConst::PRODUCT_SALES_RESULT_NAME_LIST;
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

    // 償却数に本荷の数量を加算する
    protected function addProductionQuantity() {
        if ($this->invalidFlag !== true) {
            if ($this->divisionSubjectCode === DEF_SALES_DIVISION_CODE_PRODUCT_SALES
                && $this->classItemCode === DEF_SALES_CLASS_CODE_MAIN_PRODUCT) {

                self::$productionQuantity += $this->quantity;

            }            
        }

        return;
    }

    // 登録時のパラメータチェックを行う
    public function workSheetRegistCheck() {
        if (self::$PQOutputFlag === true) {
            // 償却数が一度でも出力されていた場合は不正処理
            return false;
        }
        parent::workSheetRegistCheck();
        $this->addProductionQuantity();
        return;
    }

    // 償却数の計算結果を出力する
    public static function outputProductionQuantity() {
        static $PQOutputFlag = true;
        return self::$productionQuantity;
    }
}