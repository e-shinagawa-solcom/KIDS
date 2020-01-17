<?php
// ----------------------------------------------------------------------------
/**
*       ��¾����饤�֥��
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
*       ��������
*
*       ��������
*
*/
// ----------------------------------------------------------------------------



// ���饹���ɤ߹���
// �����ɤ߹���
include_once('conf.inc');
include_once (LIB_FILE);
include_once (LIB_DEBUGFILE);

/*
require ( CLS_DB_FILE );
require ( CLS_AUTH_FILE );
require ( CLS_TEMPLATE_FILE );

// �饤�֥���ɤ߹���
*/
// ���Ѹ����ǡ����Խ���å�����
function lockEstimateEdit($lngestimateno, $functioncode, $objDB, $objAuth){
    // ���Ѹ����ǡ�����¾����
    if(!lockExclusive($lngestimateno, $functioncode, $objAuth, $objDB)){
        return false;
    }
    // ̤�������ǡ�����¾����
    if(!lockReceiveFix($lngestimateno, $functioncode, $objDB, $objAuth)){
        return false;
    }
    
    // ̤����ȯ��ǡ�����¾����
    if(!lockOrderFix($lngestimateno, $functioncode, $objDB, $objAuth)){
        return false;
    }
    return true;
}

// ���Ѹ����ǡ�������̵ͭ�����å�
function isEstimateModified($lngestimateno, $lngrevisionno, $objDB){
    return isModified("m_estimate", "lngestimateno", $lngestimateno, $lngrevisionno, $objDB);
}


// �������ǡ�����å�����
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


// ����ǡ�����å�����
function lockReceive($lngreceiveno, $objDB){
    return getLock("m_receive", "lngreceiveno", $lngreceiveno, $objDB);
}

// ����ǡ�������̵ͭ�����å�
function isReceiveModified($lngreceiveno, $lngrevisionno, $statuscode, $objDB){
    return isStatusModified("m_receive", "lngreceiveno", $lngreceiveno, $lngrevisionno, $statuscode, $objDB);
}

// Ǽ�ʽ�����˥ǡ�����å�����
function lockSlip($lngslipno, $objDB, $objAuth){
    return getLock("m_slip", "lngslipno", $lngslipno, $objDB);
}

// Ǽ�ʽ�����˥ǡ�������̵ͭ�����å�
function isSlipModified($lngslipno, $lngrevisionno, $objDB){
    return isModified("m_slip", "lngslipno", $lngslipno, $lngrevisionno, $objDB);
}

// ����ǡ�����å�����
function lockInvoice($lnginvoiceno, $objDB){
    return getLock("m_invoice", "lnginvoiceno", $lnginvoiceno, $objDB);
}

// ����ǡ�������̵ͭ�����å�
function isInvoiceModified($lnginvoiceno, $lngrevisionno, $objDB){
    return isModified("m_invoice", "lnginvoiceno", $lnginvoiceno, $lngrevisionno, $objDB);
}

// ȯ�����ǡ�����å�����
function lockOrderFix($lngestimateno, $lngOrderCode, $functioncode, $objDB, $objAuth){
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
    $strQuery .= "WHERE mo.lngorderstatuscode = 1 AND mhe.lngestimateno = " . $lngestimateno;
    if($lngcompanycode == 0){
        $strQuery .= "    AND mo.lngcustomercompanycode <> 0 ";
    }
    else{
        $strQuery .= "    AND mo.lngcustomercompanycode = " . $lngcompanycode;
    }
    if( $lngmonetaryunitcode != 0){
        $strQuery .= "    AND mo.lngmonetaryunitcode = " . $lngmonetaryunitcode;
    }
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

// ȯ������Τˤ�ȯ���˥ǡ�����å�����
function lockOrder($lngpurchaseorderno, $objDB){
    return getLock("m_purchaseorder", "lngpurchaseorderno", $lngpurchaseorderno, $objDB);
}

// ȯ��ǡ�������̵ͭ�����å�
function isOrderModified($lngorderno, $lngrevisionno, $statuscode, $objDB){
    return isStatusModified("m_order", "lngorderno", $lngorderno, $lngrevisionno, $statuscode, $objDB);
}

// �����ǡ�����å�����
function lockStock($lngstockno, $objDB){
    return getLock("m_stock", "lngstockno", $lngstockno, $objDB);
}

// �����ǡ�������̵ͭ�����å�
function isStockModified($lngstockno, $lngrevisionno, $objDB){
    return isModified("m_stock", "lngstockno", $lngstockno, $lngrevisionno, $objDB);
}

/* ��å�����
*	@param  string  $table    ��å��оݥơ��֥�̾
*	@param  string  $keyname  ��å���������̾
*	@param  int     $key      ��å��оݥ���
*	@param  object  $objDB    DB���֥�������
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

/* ��¾��å�����
*	@param  int     $key          ��å���������
*	@param  int     $functioncode ��ǽ������
*	@param  object  $objAuth      ǧ�ھ���
*	@param  object  $objDB        DB���֥�������
*	@return boolean TRUE,FALSE
*/
function lockExclusive($key, $functioncode, $objAuth, $objDB){

    $strQuery = "LOCK TABLE t_exclusivecontrol IN EXCLUSIVE MODE";
    list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

    $strQuery = "SELECT strsessionid FROM t_exclusivecontrol WHERE strexclusivekey1 = '" . $key . "'";
    if ( !list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB ) )
    {
    	return false;
    }
    $aryResult = array();
    if ( $lngResultNum )
    {
        $lngOrderCount = $lngResultNum;
        for ( $i = 0; $i < $aryResult; $i++ )
        {
            $aryResult[] = $objDB->fetchArray( $lngResultID, $i );
            if( $aryResult["strsessionid"] != $objAuth->SessionID )
            {
                $objDB->freeResult( $lngResultID );
                return false;
            }
        }
    }
    $objDB->freeResult( $lngResultID );

    $strQuery = "INSERT INTO  t_exclusivecontrol (strexclusivekey1,lngfunctioncode,lngusercode,strsessionid) ";
    $strQuery .= "VALUES(" . $key . "," . $functioncode . "," . $objAuth->UserID . ",'" . $objAuth->SessionID . "') ";
    list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

/*
    if ( !$lngResultID )
    {
        return false;
    }
*/
    $objDB->freeResult($lngResultID);
    return true;
}

/* ��¾��å����
*	@param  int     $key          ��å���������
*	@param  object  $objDB        DB���֥�������
*	@return boolean TRUE,FALSE
*/
function unlockExclusive($objAuth, $objDB){
    return unlockExclusiveBySessionID($objAuth->SessionID, $objDB);
}

/* ����̵ͭ�����å�
*	@param  string  $table          �����å��оݥơ��֥�̾
*	@param  string  $keyname        �����å��оݥ�������̾
*	@param  int     $key            �����å��оݥ���
*	@param  int     $lngrevisionno  �����å��оݥ�ӥ�����ֹ�
*	@param  object  $objDB          DB���֥�������
*	@return boolean TRUE,FALSE
*/
function isModified($table, $keyname, $key, $lngrevisionno, $objDB){
    $strQuery = "SELECT MAX(lngrevisionno) AS max_revisionno, MIN(lngrevisionno) AS min_revisionno FROM " . $table . " WHERE " . $keyname . " = " . $key;
    list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

    if ( !$lngResultNum )
    {
        return false;
    }
    $aryResult[] = $objDB->fetchArray( $lngResultID, 0 );
    $objDB->freeResult($lngResultID);
    if(($aryResult[0]["min_revisionno"] < 0) || ($aryResult[0]["max_revisionno"] > $lngrevisionno))
    {
        return false;
    }
    return true;

}

/* ���ơ����������å�
*	@param  string  $table          �����å��оݥơ��֥�̾("m_order","m_receive","m_sales","m_stock"�Τ����줫)
*	@param  string  $keyname        �����å��оݥ�������̾
*	@param  int     $key            �����å��оݥ���
*	@param  int     $lngrevisionno  �����å��оݥ�ӥ�����ֹ�
*	@param  object  $objDB          DB���֥�������
*	@return boolean TRUE,FALSE
*/
function isStatusModified($table, $keyname, $key, $lngrevisionno, $statuscode, $objDB){
    $columnName = "lng" . str_replace("m_", "", strtolower($table)) . "statuscode";
    $strQuery = "SELECT " . $columnName . " FROM " . $table . " WHERE " . $keyname . " = " . $key . " AND lngrevisionno = " . $lngrevisionno;
    list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

    if ( !$lngResultNum )
    {
        return false;
    }
    $aryResult[] = $objDB->fetchArray( $lngResultID, 0 );
    $objDB->freeResult($lngResultID);
    if(($aryResult[0][$columnName] != $statuscode))
    {
        return false;
    }
    return true;

}

