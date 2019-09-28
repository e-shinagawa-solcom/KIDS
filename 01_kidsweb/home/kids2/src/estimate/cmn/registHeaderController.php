<?php

require_once ('conf.inc');
require_once (SRC_ROOT. "/estimate/cmn/estimateHeaderController.php");

use PhpOffice\PhpSpreadsheet\Reader\Xlsx as XlsxReader;

/**
*	ワークシートヘッダーのデータチェッククラス(登録用)
*	
*   
*/


class registHeaderController extends estimateHeaderController {

    public function __construct($objDB) {
        parent::__construct($objDB);
    }

    // セル名称に対応したセルのリストと行番号による初期データ作成
    public function initialize($cellAddressList, $loginUserCode, $sheet) {
        $this->sheet = $sheet;
        $this->cellAddressList = $cellAddressList;
        $params = $this->getCellParams();
        $this->params = $params;
        $this->setCellParams($params);
        $this->setCellTitleParams();
        $this->loginUserCode = $loginUserCode;
        return true;
    }

    // 各項目のデータを取得する
    protected function getCellParams() {
        $nameList = self::$nameList;
        $cellAddressList = $this->cellAddressList;
        if ($nameList) {
            foreach ($nameList as $key => $cellName) {
                $cellAdress = $cellAddressList[$cellName];
                $param[$key] = $this->sheet->getCell($cellAdress)->getCalculatedValue();
            }
        } else {
            return false;
        }
        return $param;
    }

    // 各項目のタイトル名を取得する
    protected function setCellTitleParams() {
        $nameList = self::$titleNameList;
        $cellAddressList = $this->cellAddressList;
        if ($nameList) {
            foreach ($nameList as $key => $cellName) {
                $cellAdress = $cellAddressList[$cellName];
                $param[$key] = $this->sheet->getCell($cellAdress)->getCalculatedValue();
            }
        } else {
            return false;
        }
        $this->headerTitleNameList = $param;
        return true;
    }
}