<?php

header("Content-Type: application/json; charset=UTF-8");

require ( 'conf.inc' );										// 設定読み込み
require ( LIB_DEBUGFILE );									// Debugモジュール

require ( LIB_FILE );										// ライブラリ読み込み

require_once ( SRC_ROOT . "/estimate/cmn/estimateDB.php");  // データベースオブジェクト

$objDB = new estimateDB();
$objAuth = new clsAuth();

//-------------------------------------------------------------------------
// DBオープン
//-------------------------------------------------------------------------
$objDB->InputEncoding = 'UTF-8';
$objDB->open( "", "", "", "" );

//-------------------------------------------------------------------------
// パラメータ取得
//-------------------------------------------------------------------------
$aryData	= array();
$aryData	= $_POST;

//-------------------------------------------------------------------------
// 入力文字列値・セッション・権限チェック
//-------------------------------------------------------------------------
// 文字列チェック
$aryCheck["strSessionID"] = "null:numenglish(32,32)";
$aryResult	= fncAllCheck( $aryData, $aryCheck );
fncPutStringCheckError( $aryResult, $objDB );

// セッション確認
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

// ユーザーコード取得
$lngUserCode = $objAuth->UserCode;

// 権限確認
if( !fncCheckAuthority( DEF_FUNCTION_UP0, $objAuth ) )
{
    fncOutputError ( 9052, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
}

// 権限グループコードの取得
$lngAuthorityGroupCode = fncGetUserAuthorityGroupCode( $lngUserCode, $aryData["strSessionID"], $objDB );

$monetary = (int)$aryData['monetary'];
$delivery = $aryData['delivery'];

$strQuery = "SELECT";
$strQuery .= " curconversionrate";
$strQuery .= " FROM m_monetaryrate";
$strQuery .= " WHERE TO_DATE('$delivery', 'YYYY/MM/DD') <= dtmapplyenddate";
$strQuery .= " AND TO_DATE('$delivery', 'YYYY/MM/DD') >= dtmapplystartdate";
$strQuery .= " AND lngmonetaryunitcode =". $monetary;
$strQuery .= " AND lngmonetaryratecode =". DEF_MONETARY_RATE_CODE_COMPANY_LOCAL;

list($resultID, $resultNumber) = fncQuery($strQuery, $objDB); // [0]:結果ID [1]:取得行数

if ($resultNumber < 1) {
    $result = '';
} else {
    $result = pg_fetch_object($resultID, 0);
}

$objDB->freeResult($resultID);

$ret = json_encode($result, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);

echo $ret;

exit;