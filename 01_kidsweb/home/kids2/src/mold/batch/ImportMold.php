<?php

// -----------------------------------------------------------
//
// 金型マスタインポート処理
//
// -----------------------------------------------------------

include( 'conf.inc' );
require_once( LIB_FILE );
require_once(SRC_ROOT.'/mold/lib/UtilMold.class.php');

// ログ出力時のプレフィックス
const LOG_PREFIX = "[KIDS-ImportMold] ";

$objDB   = new clsDB();
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

syslog(LOG_INFO, LOG_PREFIX."金型マスタインポート処理開始");

// トランザクション開始
$objDB->transactionBegin();

// インポートクエリ実行
$affected = $utilMold->importMoldFromStock();

// 取り込み件数のログ出力
syslog(LOG_INFO, LOG_PREFIX.$affected."件取り込み");

// コミット
$objDB->transactionCommit();

syslog(LOG_INFO, LOG_PREFIX."金型マスタインポート処理終了");

return;