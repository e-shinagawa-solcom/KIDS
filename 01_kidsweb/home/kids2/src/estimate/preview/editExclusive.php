<?php

header("Content-Type: application/json; charset=UTF-8");

require ( 'conf.inc' );										// 設定読み込み
require ( LIB_DEBUGFILE );									// Debugモジュール

require ( LIB_FILE );										// ライブラリ読み込み

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

switch($mode) {
    case 'cancel':
        $strQuery = "DELETE";
        $strQuery .= " FROM t_exclusivecontrol";
        $strQuery .= " WHERE lngfunctioncode = ". $functionCode;
        $strQuery .= " AND strexclusivekey1 = '". $productCode. "'";
        $strQuery .= " AND strexclusivekey2 = '". $reviseCode. "'";
    
        $result = pg_query($objDB->ConnectID, $strQuery);

        if (!$result) {
            // エラー処理
            $ret['result'] = false;
            $ret['message'] = "編集画面のロック解除が正常に行われませんでした";
        } else {
            // 正常時
            $ret['result'] = true;
            $ret['action'] = "/estimate/preview/index.php?strSessionID=". $sessionID. "&estimateNo=". $estimateNo;
        }
        break;
    case 'close':
        $strQuery = "DELETE";
        $strQuery .= " FROM t_exclusivecontrol";
        $strQuery .= " WHERE lngfunctioncode = ". $functionCode;
        $strQuery .= " AND strexclusivekey1 = '". $productCode. "'";
        $strQuery .= " AND strexclusivekey2 = '". $reviseCode. "'";

        $result = pg_query($objDB->ConnectID, $strQuery);
        
        break;
    case 'edit':
        $strQuery = "SELECT";
        $strQuery .= " max(lngrevisionno) AS maxrevisionno,";
        $strQuery .= " min(lngrevisionno) AS minrevisionno";
        $strQuery .= " FROM m_estimate";
        $strQuery .= " WHERE lngestimateno = ". $estimateNo;

        $resultID = pg_query($objDB->ConnectID, $strQuery);

        if (!pg_num_rows($resultID)) {
            // 最大値と最小値を取得できなかった場合
            $ret['result'] = false;
            $ret['message'] = "見積原価情報を取得できませんでした";        
        } else {
            $result = pg_fetch_object($resultID, 0);

            $max = $result->maxrevisionno;
            $min = $result->minrevisionno;

            if ($min < 0) {
                $ret['result'] = false;
                $ret['message'] = "他のユーザによって削除された見積原価明細です。";
            } else if ($max !== $revisionNo) {
                $ret['result'] = false;
                $ret['message'] = "他のユーザによって編集された見積原価明細です。";
            } else {
                $check = $objDB->checkExclusiveStatus($functionCode, $productCode, $reviseCode);
                if ($check === false) {
                    $strQuery = "INSERT";
                    $strQuery .= " INTO t_exclusivecontrol";
                    $strQuery .= " (";
                    $strQuery .= "lngfunctioncode,";
                    $strQuery .= " strexclusivekey1,";
                    $strQuery .= " strexclusivekey2,";
                    $strQuery .= " strexclusivekey3,";
                    $strQuery .= " lngusercode,";
                    $strQuery .= " stripaddress";
                    $strQuery .= ") VALUES (";
                    $strQuery .= $functionCode. ",";
                    $strQuery .= " '".$productCode. "',";
                    $strQuery .= " '".$reviseCode. "',";
                    $strQuery .= " '0',";
                    $strQuery .= " ".$lngUserCode .",";
                    $strQuery .= " '".$ipAddress. "'";
                    $strQuery .= ")";
            
                    $resultID = pg_query($objDB->ConnectID, $strQuery);
                    if (!$resultID) {
                        // エラー処理
                        $ret['result'] = false;
                        $ret['message'] = "編集画面のロックが正常に行われませんでした";
                    } else {
                        $ret['result'] = true;
                    }
                } else {
                    $getAddress = $check->stripaddress;
                    $getUserCode = $check->lngusercode;
                    if ($ipAddress === $getAddress && $lngUserCode === $getUserCode) {
                        $ret['result'] = true;        
                    } else {
                        // 表示名取得
                        $userDisplayName = $check->struserdisplayname;
        
                        $ret['result'] = false;
                        $ret['message'] = "ユーザ名：". $userDisplayName."が編集中です";
                    }  
                }
            }
        }
        if ($ret['result'] === true) {
            // 正常時
            $ret['action'] = "/estimate/preview/edit.php";   
        } else {
            // エラー発生時
            $ret['action'] = "/estimate/cmn/estimateError.php";
        }

        break;

    default:
        break;
}

$ret = json_encode($ret, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
echo $ret;

exit;