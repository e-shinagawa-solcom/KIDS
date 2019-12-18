<?php

require_once ('conf.inc');
require_once (SRC_ROOT. "/estimate/cmn/estimateHeaderController.php");
require_once (LIB_DEBUGFILE);

/**
*	ワークシートヘッダーのデータチェッククラス(登録用)
*	
*	以下のグローバル変数を定義すること
*   @param object $objDB        データベース接続オブジェクト(clsDBまたは継承クラス)
*   
*/

class updateHeaderController extends estimateHeaderController{

    public function __construct($objDB) {
        parent::__construct($objDB);
    }

    // 初期データ作成
    public function initialize($loginUserCode, $params) {
        $this->params = $params;
        $this->setCellParams($params);
        $this->setCellTitleParams($params);
        $this->loginUserCode = $loginUserCode;
        return true;
    }

    // 各項目のタイトル名を設定する（ブックを参照しないため、文字をベタ打ち
    protected function setCellTitleParams() {
        $nameList = self::$titleNameList;
        $cellAddressList = $this->cellAddressList;
        if ($nameList) {
            foreach ($nameList as $key => $cellName) {
                switch($cellName){
                    case workSheetConst::INSERT_DATE_HEADER:
                        $param[$key] = "作成日";
                        break;
                    case workSheetConst::PRODUCT_CODE_HEADER:
                        $param[$key] = "製品コード";
                        break;
                    case workSheetConst::PRODUCT_NAME_HEADER:
                        $param[$key] = "製品名";
                        break;
                    case workSheetConst::PRODUCT_ENGLISH_NAME_HEADER:
                        $param[$key] = "製品名(英語)";
                        break;
                    case workSheetConst::RETAIL_PRICE_HEADER:
                        $param[$key] = "上代";
                        break;
                    case workSheetConst::INCHARGE_GROUP_CODE_HEADER:
                        $param[$key] = "営業部署";
                        break;
                    case workSheetConst::INCHARGE_USER_CODE_HEADER:
                        $param[$key] = "担当";
                        break;
                    case workSheetConst::DEVELOP_USER_CODE_HEADER:
                        $param[$key] = "開発担当者";
                        break;
                    case workSheetConst::CARTON_QUANTITY_HEADER:
                        $param[$key] = "カートン入り数";
                        break;
                    case workSheetConst::PRODUCTION_QUANTITY_HEADER:
                        $param[$key] = "生産数";
                        break;
                }
            }
        } else {
            return false;
        }
        $this->headerTitleNameList = $param;
    }
}