<?php

require_once ('conf.inc');
require_once (SRC_ROOT. "/estimate/cmn/estimateHeaderController.php");

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
        $this->loginUserCode = $loginUserCode;
        return true;
    }

    // 各項目のタイトル名を取得する
    protected function setCellTitleParams() {
        $nameList = self::$titleNameList;
    }
}