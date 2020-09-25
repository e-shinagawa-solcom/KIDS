<?php

// -----------------------------------------------------------
//
// 金型マスタインポート処理
//
// -----------------------------------------------------------

include 'conf.inc';
require_once LIB_FILE;
require_once SRC_ROOT . '/mold/lib/UtilMold.class.php';

// ログ出力時のプレフィックス
const LOG_PREFIX = "[KIDS-ImportMold] ";

$objDB = new clsDB();
$objAuth = new clsAuth();

// DBオープン
$objDB->open("", "", "", "");

// リクエスト取得
$aryData = $_REQUEST;

// トランザクション開始
$objDB->transactionBegin();

// Utilインスタンスの取得
$utilMold = UtilMold::getInstance();
// ダミーのユーザコードを設定
$utilMold->setUserCode(99999);

// 仕入関連のテーブルロック
pg_query("LOCK m_stock");
pg_query("LOCK t_stockdetail");

// 金型関連テーブルのロック
pg_query("LOCK m_mold");

syslog(LOG_INFO, LOG_PREFIX . "金型マスタインポート処理開始");

// トランザクション開始
$objDB->transactionBegin();

// 金型マスタデータの無効化
$invalided = $utilMold->updateMoldToInvalid();

// 有効化クエリ実行
$valided = $utilMold->updateMoldToValid();

// 無効化件数のログ出力
syslog(LOG_INFO, LOG_PREFIX.($invalided - $valided)."件無効化");

// インポートクエリ実行
$affected = $utilMold->importMoldFromStock();

// 取り込み件数のログ出力
syslog(LOG_INFO, LOG_PREFIX . $affected . "件取り込み");

// コミット
$objDB->transactionCommit();

syslog(LOG_INFO, LOG_PREFIX . "金型マスタインポート処理終了");

if ($aryData["printFlag"]) {
    echo "金型マスタインポート処理終了しました。" . "<br>";
    if ($invalided > $valided) {
        echo ($invalided - $valided) . "件無効化しました。" . "<br>";
    }
    if ($invalided < $valided) {
        echo ($valided - $invalided) . "件有効化しました。" . "<br>";
    }
    echo $affected . "件取り込みました。" . "<br>";
}

return;
