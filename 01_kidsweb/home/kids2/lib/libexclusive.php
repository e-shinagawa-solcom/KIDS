<?php
// ----------------------------------------------------------------------------
/**
*       排他制御ライブラリ
*
*
*       @package    K.I.D.S.
*       @license    http://www.kuwagata.co.jp/
*       @copyright  KUWAGATA CO., LTD.
*       @author     K.I.D.S. Groups <info@kids-groups.com>
*       @access     public
*       @version    2.00
*
*
*       処理概要
*
*       更新履歴
*
*/
// ----------------------------------------------------------------------------



// クラスの読み込み
// 設定読み込み
include_once('conf.inc');
include_once (LIB_FILE);
include_once (LIB_DEBUGFILE);

/*
require ( CLS_DB_FILE );
require ( CLS_AUTH_FILE );
require ( CLS_TEMPLATE_FILE );

// ライブラリ読み込み
*/
// 見積原価データ編集ロック取得
function lockEstimateEdit($lngestimateno, $functioncode, $objDB, $objAuth){
    // 見積原価データ排他取得
    if(!lockExclusive($lngestimateno, $functioncode, $objAuth, $objDB)){
        return false;
    }
    // 未確定受注データ排他取得
    if(!lockReceiveFix($lngestimateno, $functioncode, $objDB, $objAuth)){
        return false;
    }
    
    // 未確定発注データ排他取得
    if(!lockOrderFix($lngestimateno, 0, $functioncode, $objDB, $objAuth)){
        return false;
    }
    return true;
}

// 見積原価データ更新有無チェック
function isEstimateModified($lngestimateno, $lngrevisionno, $objDB){
    return isModified("m_estimate", "lngestimateno", $lngestimateno, $lngrevisionno, $objDB);
}


// 受注確定データロック取得
function lockReceiveFix($lngestimateno, $functioncode, $objDB, $objAuth){
    $strQuery  = "SELECT ";
    $strQuery .= "    trd.lngreceiveno, ";
    $strQuery .= "    trd.lngrevisionno ";
    $strQuery .= "FROM m_estimatehistory mhe ";
    $strQuery .= "INNER JOIN t_receivedetail trd ";
    $strQuery .= "    ON trd.lngestimateno = mhe.lngestimateno ";
    $strQuery .= "    AND trd.lngestimatedetailno = mhe.lngestimatedetailno ";
    $strQuery .= "    AND trd.lngestimaterevisionno = mhe.lngestimatedetailrevisionno ";
    $strQuery .= "INNER JOIN m_receive mr ";
    $strQuery .= "    ON mr.lngreceiveno = trd.lngreceiveno ";
    $strQuery .= "    AND mr.lngrevisionno = trd.lngrevisionno ";
    $strQuery .= "WHERE mr.lngreceivestatuscode = 1 AND mhe.lngestimateno = " . $lngestimateno;
    $strQuery .= "    AND mhe.lngrevisionno = ( ";
    $strQuery .= "        SELECT MAX(lngrevisionno) AS lngrevisionno FROM m_estimatehistory WHERE lngestimateno = " . $lngestimateno;
    $strQuery .= "    ) ";
    list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
    $aryReceiveResult = array();
    if ( $lngResultNum )
    {
        $lngReceiveCount = $lngResultNum;
        for ( $i = 0; $i < $lngReceiveCount; $i++ )
        {
            $aryReceiveResult[] = $objDB->fetchArray( $lngResultID, $i );
        }
    }
    $objDB->freeResult( $lngResultID );
    foreach($aryReceiveResult as $dataRow){
        if(!lockExclusive($dataRow["lngreceiveno"], $functioncode, $objAuth, $objDB)){
            return false;
        }
    }
    return true;
}


// 受注データロック取得
function lockReceive($lngreceiveno, $objDB){
    return getLock("m_receive", "lngreceiveno", $lngreceiveno, $objDB);
}

// 受注データ更新有無チェック
function isReceiveModified($lngreceiveno, $statuscode, $objDB){
    return isStatusModified("m_receive", "lngreceiveno", $lngreceiveno, $statuscode, $objDB);
}

// 納品書（売上）データロック取得
function lockSlip($lngslipno, $objDB){
    return getLock("m_slip", "lngslipno", $lngslipno, $objDB);
}

// 納品書（売上）データ更新有無チェック
function isSlipModified($lngslipno, $lngrevisionno, $objDB){
    return isModified("m_slip", "lngslipno", $lngslipno, $lngrevisionno, $objDB);
}

// 請求データロック取得
function lockInvoice($lnginvoiceno, $objDB){
    return getLock("m_invoice", "lnginvoiceno", $lnginvoiceno, $objDB);
}

// 請求データ更新有無チェック
function isInvoiceModified($lnginvoiceno, $lngrevisionno, $objDB){
    return isModified("m_invoice", "lnginvoiceno", $lnginvoiceno, $lngrevisionno, $objDB);
}

// 発注確定データロック取得
function lockOrderFix($lngestimateno, $lngOrderCode, $functioncode, $objDB, $objAuth){
fncDebug("kids2.log", $lngOrderCode, __FILE__, __LINE__, "a");
    $strQuery  = "SELECT ";
    $strQuery .= "    tod.lngorderno, ";
    $strQuery .= "    tod.lngrevisionno ";
    $strQuery .= "FROM m_estimatehistory mhe ";
    $strQuery .= "INNER JOIN t_orderdetail tod ";
    $strQuery .= "    ON tod.lngestimateno = mhe.lngestimateno ";
    $strQuery .= "    AND tod.lngestimatedetailno = mhe.lngestimatedetailno ";
    $strQuery .= "    AND tod.lngestimaterevisionno = mhe.lngestimatedetailrevisionno ";
    $strQuery .= "INNER JOIN m_order mo ";
    $strQuery .= "    ON mo.lngorderno = tod.lngorderno ";
    $strQuery .= "    AND mo.lngrevisionno = tod.lngrevisionno ";
    if( (int)$lngOrderCode != 0){
        $strQuery .= "INNER JOIN (";
        $strQuery .= "    SELECT ";
        $strQuery .= "    lngcustomercompanycode,";
        $strQuery .= "    lngmonetaryunitcode";
        $strQuery .= "    FROM m_order ";
        $strQuery .= "    WHERE lngorderno = " . $lngOrderCode;
        $strQuery .= "    AND lngrevisionno =  ( ";
        $strQuery .= "        SELECT MAX(lngrevisionno) FROM m_order WHERE lngorderno = " . $lngOrderCode;
        $strQuery .= "    )";
        $strQuery .= ") mo2 on mo2.lngcustomercompanycode = mo.lngcustomercompanycode";
        $strQuery .= "    AND mo2.lngmonetaryunitcode = mo.lngmonetaryunitcode ";
    }
    $strQuery .= "WHERE mo.lngorderstatuscode = 1 AND mhe.lngestimateno = " . $lngestimateno;
    $strQuery .= "    AND mhe.lngrevisionno = ( ";
    $strQuery .= "        SELECT MAX(lngrevisionno) AS lngrevisionno FROM m_estimatehistory WHERE lngestimateno = " . $lngestimateno;
    $strQuery .= "    ) ";
    list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
    $aryOrderResult = array();
    if ( $lngResultNum )
    {
        $lngOrderCount = $lngResultNum;
        for ( $i = 0; $i < $lngOrderCount; $i++ )
        {
            $aryOrderResult[] = $objDB->fetchArray( $lngResultID, $i );
        }
    }
    $objDB->freeResult( $lngResultID );
    foreach($aryOrderResult as $dataRow){
        if(!lockExclusive($dataRow["lngorderno"], $functioncode, $objAuth, $objDB)){
            return false;
        }
    }
    return true;
}

// 発注（正確には発注書）データロック取得
function lockOrder($lngpurchaseorderno, $objDB){
    return getLock("m_purchaseorder", "lngpurchaseorderno", $lngpurchaseorderno, $objDB);
}

// 発注データ更新有無チェック
function isOrderModified($lngorderno, $statuscode, $objDB){
    return isStatusModified("m_order", "lngorderno", $lngorderno, $statuscode, $objDB);
}

// 発注書データ更新有無チェック
function isPurchaseOrderModified($lngpurchaseorderno, $lngrevisionno, $objDB){
    return isModified("m_purchaseorder", "lngpurchaseorderno", $lngpurchaseorderno, $lngrevisionno, $objDB);
}

// 仕入データロック取得
function lockStock($lngstockno, $objDB){
    return getLock("m_stock", "lngstockno", $lngstockno, $objDB);
}

// 仕入データ締めチェック
function isStockClosed($lngstockno, $objDB){
    return !isStatusModified("m_stock", "lngstockno", $lngstockno, DEF_STOCK_CLOSED, $objDB);
}

// 仕入データ更新有無チェック
function isStockModified($lngstockno, $lngrevisionno, $objDB){
    return isModified("m_stock", "lngstockno", $lngstockno, $lngrevisionno, $objDB);
}

/* ロック取得
*	@param  string  $table    ロック対象テーブル名
*	@param  string  $keyname  ロックキー項目名
*	@param  int     $key      ロック対象キー
*	@param  object  $objDB    DBオブジェクト
*	@return boolean TRUE,FALSE
*/
function getLock($table, $keyname, $key, $objDB){
    $strQuery = "SELECT " . $keyname . " FROM " . $table . " WHERE " . $keyname . "=" . $key . " FOR UPDATE";
    list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
    if ( !$lngResultNum )
    {
        return false;
    }
    $objDB->freeResult($lngResultID);
    return true;
}

/* 排他ロック取得
*	@param  int     $key          ロック取得キー
*	@param  int     $functioncode 機能コード
*	@param  object  $objAuth      認証情報
*	@param  object  $objDB        DBオブジェクト
*	@return boolean TRUE,FALSE
*/
function lockExclusive($key, $functioncode, $objAuth, $objDB) {
    $strQuery = "LOCK TABLE t_exclusivecontrol IN EXCLUSIVE MODE";
    list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

    $strQuery = "SELECT lngusercode,strsessionid FROM t_exclusivecontrol WHERE strexclusivekey1 = '" . $key . "'";
    if ( !list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB ) )
    {
    	return false;
    }
    $aryResult = array();
    if ( $lngResultNum )
    {
        $lngOrderCount = $lngResultNum;
        if ($lngResultNum > 0) {
            $objDB->freeResult( $lngResultID );
                return false;
        }
    }
    $objDB->freeResult( $lngResultID );

    $strQuery = "INSERT INTO  t_exclusivecontrol (strexclusivekey1,lngfunctioncode,lngusercode,strsessionid, dtminsertdate) ";
    $strQuery .= "VALUES(" . $key . "," . $functioncode . "," . $objAuth->UserID . ",'" . $objAuth->SessionID . "','" . fncGetDateTimeString() . "') ";
    list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

    return true;
}

/* 排他ロック解除
*	@param  int     $key          ロック取得キー
*	@param  object  $objDB        DBオブジェクト
*	@return boolean TRUE,FALSE
*/
function unlockExclusive($objAuth, $objDB){
    return unlockExclusiveBySessionID($objAuth->SessionID, $objDB);
}

/**
 * ロック中のユーザーIDを取得する
 *
 * @param [type] $key
 * @param [type] $objDB
 * @return void
 */
function getLockedUserID($key, $objDB) {
    $strQuery = "SELECT lngusercode,strsessionid FROM t_exclusivecontrol WHERE strexclusivekey1 = '" . $key . "'";
    list($lngResultID, $lngResultNum) = fncQuery( $strQuery, $objDB);
    if ($lngResultNum)
    {
        for ( $i = 0; $i < $lngResultNum; $i++ )
        {
            $aryResult= $objDB->fetchArray($lngResultID, $i);            
            $objDB->freeResult( $lngResultID );
            return $aryResult["lngusercode"];
        }
    }
    return "";
}

/* 更新有無チェック
*	@param  string  $table          チェック対象テーブル名
*	@param  string  $keyname        チェック対象キー項目名
*	@param  int     $key            チェック対象キー
*	@param  int     $lngrevisionno  チェック対象リビジョン番号
*	@param  object  $objDB          DBオブジェクト
*	@return boolean TRUE,FALSE
*/
function isModified($table, $keyname, $key, $lngrevisionno, $objDB){
    $strQuery = "SELECT MAX(lngrevisionno) AS max_revisionno, MIN(lngrevisionno) AS min_revisionno FROM " . $table . " WHERE " . $keyname . " = " . $key;

    list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

    if ( !$lngResultNum )
    {
        return true;
    }
    $aryResult[] = $objDB->fetchArray( $lngResultID, 0 );
    $objDB->freeResult($lngResultID);
    if(($aryResult[0]["min_revisionno"] < 0) || ($aryResult[0]["max_revisionno"] > $lngrevisionno))
    {
        return true;
    }
    return false;

}

/* ステータスチェック
*	@param  string  $table          チェック対象テーブル名("m_order","m_receive","m_sales","m_stock"のいずれか)
*	@param  string  $keyname        チェック対象キー項目名
*	@param  int     $key            チェック対象キー
*	@param  int     $lngrevisionno  チェック対象リビジョン番号
*	@param  object  $objDB          DBオブジェクト
*	@return boolean TRUE,FALSE
*/
function isStatusModified($table, $keyname, $key, $statuscode, $objDB){
    $columnName = "lng" . str_replace("m_", "", strtolower($table)) . "statuscode";
    $strQuery = "SELECT " . $columnName . " FROM " . $table . " WHERE " . $keyname . " = " . $key;
    $strQuery .= " AND lngrevisionno = (SELECT MAX(lngrevisionno) FROM " . $table . " WHERE " . $keyname . " = " . $key . " )";
    list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

    if ( !$lngResultNum )
    {
        return true;
    }
    $aryResult[] = $objDB->fetchArray( $lngResultID, 0 );
    $objDB->freeResult($lngResultID);
    if(($aryResult[0][$columnName] != $statuscode))
    {
        return true;
    }
    return false;

}

