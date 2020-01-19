<?php

header("Content-Type: application/json; charset=UTF-8");

require ( 'conf.inc' );										// 設定読み込み
require ( LIB_DEBUGFILE );									// Debugモジュール

require ( LIB_FILE );										// ライブラリ読み込み
require ( LIB_EXCLUSIVEFILE );
require_once ( SRC_ROOT . "/estimate/cmn/estimateDB.php");

$objDB			= new estimateDB();
$objAuth		= new clsAuth();


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
$aryCheck["strSessionID"]	= "null:numenglish(32,32)";
$aryResult	= fncAllCheck( $aryData, $aryCheck );
fncPutStringCheckError( $aryResult, $objDB );

// セッション確認
$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

// ユーザーコード取得
$lngUserCode = $objAuth->UserCode;

// 権限確認
if( !fncCheckAuthority( DEF_FUNCTION_E3, $objAuth ) )
{
    fncOutputError ( 9052, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
}

// 権限グループコードの取得
$lngAuthorityGroupCode = fncGetUserAuthorityGroupCode( $lngUserCode, $aryData["strSessionID"], $objDB );

$sessionID = $aryData["strSessionID"];
$productCode = $aryData["productCode"];
$reviseCode = $aryData["reviseCode"];
$estimateNo = $aryData["estimateNo"];
$revisionNo = $aryData["revisionNo"];
$ipAddress = $_SERVER['REMOTE_ADDR'];

$mode = $aryData['processMode'];

$functionCode = DEF_FUNCTION_E3;

//$ret['result'] = true;

switch($mode) {
    case 'cancel':
        $objDB->transactionBegin();
        $result = unlockExclusive($objAuth, $objDB);
        $objDB->transactionCommit();

        if (!$result) {
            // エラー処理
            $ret['result'] = false;
            $ret['message'] = "編集画面のロック解除が正常に行われませんでした";
        } else {
            // 正常時
            $objDB->transactionCommit();
            $ret['result'] = true;
            $ret['action'] = "/estimate/preview/index.php?strSessionID=". $sessionID. "&estimateNo=". $estimateNo;
        }
        break;
        
    case 'close':
        $objDB->transactionBegin();
        $result = unlockExclusive($objAuth, $objDB);
        $objDB->transactionCommit();
        break;
        
    case 'edit':
        $ret['result'] = true;
        $objDB->transactionBegin();
        if( isEstimateModified($estimateNo, $revisionNo, $objDB) )
        {
            $ret['message'] = "他のユーザによって更新または削除されています。";
            $ret['result'] = 0;
        }
        else{
            if( !lockEstimateEdit($estimateNo, $functionCode, $objDB, $objAuth)){
                $ret['result'] = 0;
                $ret['message'] = "見積原価データは他ユーザーが編集中です。";
            }
        }
        if ($ret['result'] == true) {
            // 正常時
            $objDB->transactionCommit();
            $ret['action'] = "/estimate/preview/edit.php";   
        } else {
            // エラー発生時
//            $objDB->transactionRollback();
            $ret['action'] = "/estimate/cmn/estimateError.php";
        }

        break;

    default:
        break;
}

$ret = json_encode($ret, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
echo $ret;

exit;