<?php
// ----------------------------------------------------------------------------
/**
 *       発注管理  発注確定関連関数群
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
 *         ・発注確定関連の関数
 *
 *       更新履歴
 *
 */
// ----------------------------------------------------------------------------

/**
 * 発注No.に一致する発注データのヘッダを取得
 *
 *    発注No.から 発注データを取得する
 *
 *    @param    Integer    $lngOrderNo 発注Ｎｏ
 *    @param  Object    $objDB        DB接続オブジェクト
 *    @return String     $strQuery   発注データ(ヘッダ)
 *    @access public
 */

function fncGetOrder_r($lngOrderNo, $objDB)
{
    $aryQuery = array();
    $aryQuery[] = "SELECT";
    $aryQuery[] = "   distinct mo.lngorderno";
    $aryQuery[] = "  ,mo.strordercode";
    $aryQuery[] = "  ,od.lngrevisionno";
    // $aryQuery[] = "  ,mo.dtmexpirationdate";
    $aryQuery[] = "  ,TO_CHAR(NOW(), 'YYYY/MM/DD') AS dtmexpirationdate";
    $aryQuery[] = "  ,od.strproductcode";
    $aryQuery[] = "  ,od.strrevisecode";
    $aryQuery[] = "  ,mo.lngpayconditioncode";
    $aryQuery[] = "  ,mo.lngmonetaryunitcode";
    $aryQuery[] = "  ,mc.strcompanydisplaycode";
    $aryQuery[] = "  ,mc.strcompanydisplayname";
    $aryQuery[] = "  ,mg.strgroupdisplaycode";
    $aryQuery[] = "  ,mg.strgroupdisplayname";
    $aryQuery[] = "  ,mpd.strproductname";
    $aryQuery[] = "  ,mpd.strproductenglishname";
    $aryQuery[] = "  ,mc2.strcompanydisplaycode as strcompanydisplaycode2";
    $aryQuery[] = "  ,mc2.strcompanydisplayname as strcompanydisplayname2";
    $aryQuery[] = "  ,mc.lngcountrycode";
    $aryQuery[] = "  ,mc.straddress1";
    $aryQuery[] = "  ,mc.straddress2";
    $aryQuery[] = "  ,mc.straddress3";
    $aryQuery[] = "  ,mc.strtel1";
    $aryQuery[] = "  ,mc.strfax1";
    $aryQuery[] = "  ,mm.strmonetaryunitname";
    $aryQuery[] = "  ,mm.strmonetaryunitsign";
    $aryQuery[] = "  ,mpc.strpayconditionname";
    $aryQuery[] = "  ,ms.txtsignaturefilename";
    $aryQuery[] = "  ,mo.lngcustomercompanycode";
    $aryQuery[] = "  ,mo.lnggroupcode";
    $aryQuery[] = "  ,mo.lngusercode";
    $aryQuery[] = "  ,mu.struserdisplaycode";
    $aryQuery[] = "  ,mu.struserdisplayname";
    $aryQuery[] = "  ,mc2.lngcompanycode as lngcompanycode2";
    $aryQuery[] = "  ,mc.lngcountrycode as lngcountrycode";
    $aryQuery[] = "FROM m_order mo";
    $aryQuery[] = "  inner join ( ";
    $aryQuery[] = "    select";
    $aryQuery[] = "      max(lngRevisionNo) lngRevisionNo";
    $aryQuery[] = "      , lngorderno ";
    $aryQuery[] = "    from";
    $aryQuery[] = "      m_order";
    $aryQuery[] = "    group by";
    $aryQuery[] = "      lngorderno";
    $aryQuery[] = "  ) mo1";
    $aryQuery[] = "    on mo.lngrevisionno = mo1.lngRevisionNo ";
    $aryQuery[] = "    and mo.lngorderno = mo1.lngorderno ";
    $aryQuery[] = "INNER JOIN t_orderdetail od";
    $aryQuery[] = "  ON  mo.lngorderno = od.lngorderno";
    $aryQuery[] = "  AND mo.lngrevisionno = od.lngrevisionno";
    $aryQuery[] = "LEFT JOIN m_company mc";
    $aryQuery[] = "  ON  mo.lngcustomercompanycode = mc.lngcompanycode";
    $aryQuery[] = "LEFT JOIN m_group mg";
    $aryQuery[] = "  ON  mo.lnggroupcode = mg.lnggroupcode";
    $aryQuery[] = "LEFT JOIN (";
    $aryQuery[] = "    SELECT m_product.* FROM m_product ";
    $aryQuery[] = "     INNER JOIN (";
    $aryQuery[] = "         SELECT ";
    $aryQuery[] = "             lngproductno,strrevisecode,MAX(lngrevisionno) as lngrevisionno ";
    $aryQuery[] = "         FROM m_product GROUP BY lngproductno,strrevisecode";
    $aryQuery[] = "     ) mp1 ON mp1.lngproductno = m_product.lngproductno";
    $aryQuery[] = "     AND mp1.strrevisecode = m_product.strrevisecode";
    $aryQuery[] = "     AND mp1.lngrevisionno = m_product.lngrevisionno";
    $aryQuery[] = ") mpd ON  od.strproductcode = mpd.strproductcode ";
    $aryQuery[] = "  AND mpd.strrevisecode = od.strrevisecode ";
//    $aryQuery[] = "  AND mpd.lngrevisionno = od.lngrevisionno ";
    $aryQuery[] = "LEFT JOIN m_company mc2";
    $aryQuery[] = "  ON  mo.lngdeliveryplacecode = mc2.lngcompanycode";
    $aryQuery[] = "LEFT JOIN m_monetaryunit mm";
    $aryQuery[] = "  ON  mo.lngmonetaryunitcode = mm.lngmonetaryunitcode";
    $aryQuery[] = "LEFT JOIN m_paycondition mpc";
    $aryQuery[] = "  ON  mo.lngpayconditioncode = mpc.lngpayconditioncode";
    $aryQuery[] = "LEFT JOIN m_signature ms";
    $aryQuery[] = "  ON  mo.lnggroupcode = ms.lnggroupcode";
    $aryQuery[] = "LEFT JOIN m_user mu";
    $aryQuery[] = "  ON  mo.lngusercode = mu.lngusercode";
    // $aryQuery[] = "WHERE mo.lngorderno = " . $lngOrderNo;
    $aryQuery[] = "WHERE mo.lngorderno IN (" . $lngOrderNo . ")";

    $strQuery = implode("\n", $aryQuery);
    list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);
    if (!$lngResultNum) {
        return false;
    }

    $lngFieldsCount = $objDB->getFieldsCount($lngResultID);

    if ($lngMaxFieldsCount) {
        if ($lngFieldsCount > $lngMaxFieldsCount) {
            $lngFieldsCount = $lngMaxFieldsCount;
        }

    }

    for ($i = 0; $i < $lngResultNum; $i++) {
        $aryResult[] = $objDB->fetchArray($lngResultID, $i);
    }

    $objDB->freeResult($lngResultID);
    return $aryResult;
}
/**
 * 排他制御チェック
 *
 * @param [type] $lngFunctionCode
 * @param [type] $strProductCode
 * @param [type] $lngRevisionNo
 * @param [type] $objDB
 * @return void [true：排他制御発生　false：排他制御発生していない]
 */
function fncCheckExclusiveControl($lngFunctionCode, $strProductCode, $lngRevisionNo, $objDB)
{
    $strQuery = "select";
    $strQuery .= "  lngfunctioncode,strexclusivekey1,strexclusivekey2  ";
    $strQuery .= "from";
    $strQuery .= "  t_exclusivecontrol ";
    $strQuery .= "where";
    $strQuery .= "  lngfunctioncode = " . $lngFunctionCode;
    $strQuery .= "  and strexclusivekey1 = '" . $strProductCode . "' ";
    $strQuery .= "  and strexclusivekey2 = '" . $lngRevisionNo . "' ";

    // 検索クエリーの実行
    list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);

    if ($lngResultNum >= 1) {
        $result = true;
    } else {
        $result = false;
    }
    $objDB->freeResult($lngResultID);

    return $result;
}
/**
 * 会社マスタ検索
 *
 * @param   Integer     $lngCompanyCode     会社コード
 *    @param  Object        $objDB                DBオブジェクト
 *    @access public
 *
 */
function fncGetCompany($lngCompanyCode, $objDB)
{
    $aryQuery[] = "SELECT ";
    $aryQuery[] = "   lngcompanycode ";
    $aryQuery[] = "  ,lngcountrycode ";
    $aryQuery[] = "  ,lngorganizationcode ";
    $aryQuery[] = "  ,bytorganizationfront ";
    $aryQuery[] = "  ,strcompanyname ";
    $aryQuery[] = "  ,bytcompanydisplayflag ";
    $aryQuery[] = "  ,strcompanydisplaycode ";
    $aryQuery[] = "  ,strcompanydisplayname ";
    $aryQuery[] = "  ,strshortname ";
    $aryQuery[] = "  ,strpostalcode ";
    $aryQuery[] = "  ,straddress1 ";
    $aryQuery[] = "  ,straddress2 ";
    $aryQuery[] = "  ,straddress3 ";
    $aryQuery[] = "  ,straddress4 ";
    $aryQuery[] = "  ,strtel1 ";
    $aryQuery[] = "  ,strtel2 ";
    $aryQuery[] = "  ,strfax1 ";
    $aryQuery[] = "  ,strfax2 ";
    $aryQuery[] = "  ,strdistinctcode ";
    $aryQuery[] = "  ,lngcloseddaycode ";
    $aryQuery[] = "FROM m_company ";
    $aryQuery[] = "WHERE lngcompanycode = " . $lngCompanyCode;

    $strQuery = implode("\n", $aryQuery);

    list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);
    if (!$lngResultNum) {
        return false;
    }

    $lngFieldsCount = $objDB->getFieldsCount($lngResultID);

    $result = $objDB->fetchArray($lngResultID, 0);

    $objDB->freeResult($lngResultID);
    return $result;
}

/**
 * 支払条件マスタ検索
 *
 * @param   Integer     $lngpayconditioncode    支払条件コード
 * @param   Object      $objDB                  DBオブジェクト
 * @access  public
 *
 */
function fncGetPayCondition($lngpayconditioncode, $objDB)
{
    $aryQuery[] = "SELECT ";
    $aryQuery[] = "   lngpayconditioncode ";
    $aryQuery[] = "  ,strpayconditionname";
    $aryQuery[] = "FROM m_paycondition ";
    $aryQuery[] = "WHERE lngpayconditioncode = " . $lngpayconditioncode;

    $strQuery = implode("\n", $aryQuery);

    list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);
    if (!$lngResultNum) {
        return false;
    }

    $lngFieldsCount = $objDB->getFieldsCount($lngResultID);

    $result = $objDB->fetchArray($lngResultID, 0);

    $objDB->freeResult($lngResultID);
    return $result;
}

/**
 * 発注No.に一致する発注データの明細を取得
 *
 *    発注No.から 発注明細データを取得する
 *
 *    @param    Integer    $lngOrderNo 発注Ｎｏ
 *    @param  Object    $objDB        DB接続オブジェクト
 *    @return String     $strDetail  発注データ(明細)
 *    @access public
 */
function fncGetOrderDetail($aryOrderNo, $lngRevisionNo, $objDB)
{
    $aryQuery[] = "SELECT DISTINCT";
//    $aryQuery[] = "    ON ( ";
//    $aryQuery[] = "      mo.strordercode";
//    $aryQuery[] = "      , mo.lngrevisionno";
//    $aryQuery[] = "      , od.lngorderdetailno";
//    $aryQuery[] = "    ) mo.strordercode || '_' || TO_CHAR(mo.lngrevisionno, 'FM00') AS strordercode";
    $aryQuery[] = "    mo.strordercode || '_' || TO_CHAR(mo.lngrevisionno, 'FM00') AS strordercode";
    $aryQuery[] = "  , od.lngorderdetailno";
    $aryQuery[] = "  , mo.lngorderstatuscode";
    $aryQuery[] = "  , od.strproductcode";
    $aryQuery[] = "  , mp.strproductname";
    $aryQuery[] = "  , od.dtmdeliverydate";
    $aryQuery[] = "  , mc.strcompanydisplaycode";
    $aryQuery[] = "  , mc.strcompanydisplayname";
    $aryQuery[] = "  , od.lngstocksubjectcode";
    $aryQuery[] = "  , mss.strstocksubjectname";
    $aryQuery[] = "  , od.lngstockitemcode";
    $aryQuery[] = "  , msi.strstockitemname";
    $aryQuery[] = "  , od.lngdeliverymethodcode";
    $aryQuery[] = "  , od.lngproductunitcode";
    $aryQuery[] = "  , od.lngsortkey";
    $aryQuery[] = "  , mmu.strmonetaryunitsign";
    $aryQuery[] = "  , od.curproductprice";
    $aryQuery[] = "  , od.lngproductquantity";
    $aryQuery[] = "  , od.cursubtotalprice";
    $aryQuery[] = "  , mpu.strproductunitname";
    $aryQuery[] = "  , mdm.strdeliverymethodname";
    $aryQuery[] = "  , od.strnote";
    $aryQuery[] = "  , mo.lngrevisionno";
    $aryQuery[] = "  , mo.lngorderno";
    $aryQuery[] = "  , mo.lngmonetaryunitcode";
    $aryQuery[] = "  , mo.lngcustomercompanycode ";
    $aryQuery[] = "FROM";
    $aryQuery[] = "  m_order mo ";
    $aryQuery[] = "  INNER JOIN t_orderdetail od ";
    $aryQuery[] = "    ON mo.lngorderno = od.lngorderno ";
    $aryQuery[] = "    AND mo.lngrevisionno = od.lngrevisionno ";
    $aryQuery[] = "  LEFT JOIN ( ";
    $aryQuery[] = "    SELECT";
    $aryQuery[] = "      m_product.* ";
    $aryQuery[] = "    FROM";
    $aryQuery[] = "      m_product ";
    $aryQuery[] = "      INNER JOIN ( ";
    $aryQuery[] = "        SELECT";
    $aryQuery[] = "          lngproductno";
    $aryQuery[] = "          , strrevisecode";
    $aryQuery[] = "          , MAX(lngrevisionno) as lngrevisionno ";
    $aryQuery[] = "        FROM";
    $aryQuery[] = "          m_product ";
    $aryQuery[] = "        GROUP BY";
    $aryQuery[] = "          lngproductno";
    $aryQuery[] = "          , strrevisecode";
    $aryQuery[] = "      ) mp1 ";
    $aryQuery[] = "        ON mp1.lngproductno = m_product.lngproductno ";
    $aryQuery[] = "        AND mp1.strrevisecode = m_product.strrevisecode ";
    $aryQuery[] = "        AND mp1.lngrevisionno = m_product.lngrevisionno";
    $aryQuery[] = "  ) mp ";
    $aryQuery[] = "    ON od.strproductcode = mp.strproductcode ";
    $aryQuery[] = "    AND mp.strrevisecode = od.strrevisecode ";
    $aryQuery[] = "  LEFT JOIN m_company mc ";
    $aryQuery[] = "    ON mo.lngcustomercompanycode = mc.lngcompanycode ";
    $aryQuery[] = "  LEFT JOIN m_deliverymethod mdm ";
    $aryQuery[] = "    ON od.lngdeliverymethodcode = mdm.lngdeliverymethodcode ";
    $aryQuery[] = "  LEFT JOIN m_stocksubject mss ";
    $aryQuery[] = "    ON od.lngstocksubjectcode = mss.lngstocksubjectcode ";
    $aryQuery[] = "  LEFT JOIN m_stockitem msi ";
    $aryQuery[] = "    ON od.lngstockitemcode = msi.lngstockitemcode ";
    $aryQuery[] = "    AND od.lngstocksubjectcode = msi.lngstocksubjectcode ";
    $aryQuery[] = "  LEFT JOIN m_productprice mpp ";
    $aryQuery[] = "    ON mp.lngproductno = mpp.lngproductno ";
    $aryQuery[] = "    AND od.lngstockitemcode = mpp.lngstockitemcode ";
    $aryQuery[] = "    AND od.lngstocksubjectcode = mpp.lngstocksubjectcode ";
    $aryQuery[] = "  LEFT JOIN m_monetaryunit mmu ";
    $aryQuery[] = "    ON mo.lngmonetaryunitcode = mmu.lngmonetaryunitcode ";
    $aryQuery[] = "  LEFT JOIN m_productunit mpu ";
    $aryQuery[] = "    ON od.lngproductunitcode = mpu.lngproductunitcode ";
    $aryQuery[] = "  inner join ( ";
    $aryQuery[] = "    select";
    $aryQuery[] = "      mo.strordercode";
    $aryQuery[] = "      , mo.lngrevisionno";
    $aryQuery[] = "      , od.strproductcode";
    $aryQuery[] = "      , od.strrevisecode";
    $aryQuery[] = "      , mo.lngcustomercompanycode";
    $aryQuery[] = "      , mo.lngmonetaryunitcode";
    $aryQuery[] = "      , msi.lngestimateareaclassno ";
    $aryQuery[] = "    from";
    $aryQuery[] = "      m_order mo ";
//    $aryQuery[] = "      inner join ( ";
//    $aryQuery[] = "        select";
//    $aryQuery[] = "          max(lngRevisionNo) lngRevisionNo";
//    $aryQuery[] = "          , lngorderno ";
//    $aryQuery[] = "        from";
//    $aryQuery[] = "          m_order ";
//    $aryQuery[] = "        group by";
//    $aryQuery[] = "          lngorderno";
//    $aryQuery[] = "      ) mo1 ";
//    $aryQuery[] = "        on mo.lngrevisionno = mo1.lngRevisionNo ";
//    $aryQuery[] = "        and mo.lngorderno = mo1.lngorderno ";
    $aryQuery[] = "      LEFT JOIN t_orderdetail od ";
    $aryQuery[] = "        ON mo.lngorderno = od.lngorderno ";
    $aryQuery[] = "        AND mo.lngrevisionno = od.lngrevisionno ";
    $aryQuery[] = "      LEFT JOIN m_stocksubject mss ";
    $aryQuery[] = "        ON od.lngstocksubjectcode = mss.lngstocksubjectcode ";
    $aryQuery[] = "      LEFT JOIN m_stockitem msi ";
    $aryQuery[] = "        ON od.lngstockitemcode = msi.lngstockitemcode ";
    $aryQuery[] = "        AND od.lngstocksubjectcode = msi.lngstocksubjectcode ";
    $aryQuery[] = "    WHERE";
    $aryQuery[] = "      mo.lngorderno in (" .$aryOrderNo .") AND mo.lngrevisionno = ". $lngRevisionNo."";
    $aryQuery[] = "  ) m_key ";
    $aryQuery[] = "    ON od.strproductcode = m_key.strproductcode ";
    $aryQuery[] = "    AND od.strrevisecode = m_key.strrevisecode ";
    $aryQuery[] = "    AND mo.strordercode = m_key.strordercode ";
    $aryQuery[] = "    AND mo.lngcustomercompanycode = m_key.lngcustomercompanycode ";
    $aryQuery[] = "    AND mo.lngmonetaryunitcode = m_key.lngmonetaryunitcode ";
    $aryQuery[] = "    AND msi.lngestimateareaclassno = m_key.lngestimateareaclassno ";
    $aryQuery[] = "inner join ( ";
    $aryQuery[] = "    select ";
    $aryQuery[] = "        lngorderno  ";
    $aryQuery[] = "       ,MAX(lngrevisionno) as lngrevisionno ";
    $aryQuery[] = "    from m_order ";
    $aryQuery[] = "    where lngorderno not in (select lngorderno from m_order where lngrevisionno < 0) ";
    $aryQuery[] = "    group by lngorderno ";
    $aryQuery[] = ") mo_rev ";
    $aryQuery[] = "    on mo_rev.lngorderno = mo.lngorderno ";
    $aryQuery[] = "    and mo_rev.lngrevisionno = mo.lngrevisionno ";
    $aryQuery[] = "WHERE";
    $aryQuery[] = "  mo.lngorderstatuscode = 1";

    $strQuery = implode("\n", $aryQuery);


    list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);
    if (!$lngResultNum) {
        return false;
    }

    $lngFieldsCount = $objDB->getFieldsCount($lngResultID);

    if ($lngMaxFieldsCount) {
        if ($lngFieldsCount > $lngMaxFieldsCount) {
            $lngFieldsCount = $lngMaxFieldsCount;
        }

    }

    for ($i = 0; $i < $lngResultNum; $i++) {
        $aryResult[] = $objDB->fetchArray($lngResultID, $i);
    }

    $objDB->freeResult($lngResultID);
    return $aryResult;
}

/**
 * 発注明細検索
 *
 * @param   Integer     $lngOrderNo         発注番号
 * @param   Integer     $lngOrderDetailNo   発注明細番号
 * @param   Integer     $lngRevisionNo      リビジョン番号
 * @param   Object      $objDB              DBオブジェクト
 * @access  public
 *
 */
function fncGetOrderDetail2($lngOrderNo, $lngOrderDetailNo, $lngRevisioNno, $objDB)
{
    $aryQuery[] = "SELECT ";
    $aryQuery[] = "   mo.lngorderno ";
    $aryQuery[] = "  ,mo.lngrevisionno ";
    $aryQuery[] = "  ,od.lngorderdetailno ";
    $aryQuery[] = "  ,mo.lngcustomercompanycode ";
    $aryQuery[] = "  ,mo.lngdeliveryplacecode ";
    $aryQuery[] = "  ,od.strproductcode ";
    $aryQuery[] = "  ,od.strrevisecode ";
    $aryQuery[] = "  ,mp.strproductname ";
    $aryQuery[] = "  ,mp.strproductenglishname ";
    $aryQuery[] = "  ,mo.lngmonetaryunitcode ";
    $aryQuery[] = "  ,mm.strmonetaryunitname ";
    $aryQuery[] = "  ,mm.strmonetaryunitsign ";
    $aryQuery[] = "  ,mo.lnggroupcode ";
    $aryQuery[] = "  ,mg.strgroupdisplayname ";
    $aryQuery[] = "  ,ms.txtsignaturefilename ";
    $aryQuery[] = "  ,mo.lngusercode ";
    $aryQuery[] = "  ,mu.struserdisplayname ";
    $aryQuery[] = "  ,od.lngstockitemcode ";
    $aryQuery[] = "  ,od.lngstocksubjectcode ";
    $aryQuery[] = "  ,msi.strstockitemname ";
    $aryQuery[] = "  ,od.lngdeliverymethodcode ";
    $aryQuery[] = "  ,md.strdeliverymethodname ";
    $aryQuery[] = "  ,od.curproductprice ";
    $aryQuery[] = "  ,od.lngproductquantity ";
    $aryQuery[] = "  ,od.lngproductunitcode ";
    $aryQuery[] = "  ,mpu.strproductunitname ";
    $aryQuery[] = "  ,od.cursubtotalprice ";
    $aryQuery[] = "  ,TO_CHAR(od.dtmdeliverydate, 'YYYY/MM/DD') AS dtmdeliverydate ";
    $aryQuery[] = "  ,od.strnote ";
    $aryQuery[] = "FROM m_order mo ";
    $aryQuery[] = "INNER JOIN t_orderdetail od ";
    $aryQuery[] = "  ON  mo.lngorderno = od.lngorderno ";
    $aryQuery[] = "  AND mo.lngrevisionno = od.lngrevisionno ";
    $aryQuery[] = "LEFT JOIN m_product mp ";
    $aryQuery[] = "  ON  od.strproductcode = mp.strproductcode ";
    $aryQuery[] = "  AND  od.strrevisecode = mp.strrevisecode ";
    $aryQuery[] = "INNER JOIN(";
    $aryQuery[] = "  SELECT strproductcode, ";    
    $aryQuery[] = "         strrevisecode, ";    
    $aryQuery[] = "         MAX(lngrevisionno) as lngrevisionno ";    
    $aryQuery[] = "  FROM m_product ";    
    $aryQuery[] = "  GROUP BY strproductcode,strrevisecode";    
    $aryQuery[] = ") mp_rev ";    
    $aryQuery[] = "  ON mp_rev.strproductcode = mp.strproductcode ";    
    $aryQuery[] = "  AND mp_rev.strrevisecode = mp.strrevisecode ";    
    $aryQuery[] = "  AND mp_rev.lngrevisionno = mp.lngrevisionno ";    
//    $aryQuery[] = "  AND  od.lngrevisionno = mp.lngrevisionno ";
    $aryQuery[] = "LEFT JOIN m_monetaryunit mm ";
    $aryQuery[] = "  ON  mo.lngmonetaryunitcode = mm.lngmonetaryunitcode ";
    $aryQuery[] = "LEFT JOIN m_group mg ";
    $aryQuery[] = "  ON  mo.lnggroupcode = mg.lnggroupcode ";
    $aryQuery[] = "LEFT JOIN m_signature ms ";
    $aryQuery[] = "  ON  mo.lnggroupcode = ms.lnggroupcode ";
    $aryQuery[] = "LEFT JOIN m_user mu ";
    $aryQuery[] = "  ON  mo.lngusercode = mu.lngusercode ";
    $aryQuery[] = "LEFT JOIN m_stockitem msi ";
    $aryQuery[] = "  ON  od.lngstockitemcode = msi.lngstockitemcode ";
    $aryQuery[] = "  AND od.lngstocksubjectcode = msi.lngstocksubjectcode ";
    $aryQuery[] = "LEFT JOIN m_deliverymethod md ";
    $aryQuery[] = "  ON  od.lngdeliverymethodcode = md.lngdeliverymethodcode ";
    $aryQuery[] = "LEFT JOIN m_productunit mpu ";
    $aryQuery[] = "  ON  od.lngproductunitcode = mpu.lngproductunitcode ";
    $aryQuery[] = "WHERE mo.lngorderno = " . $lngOrderNo;
    $aryQuery[] = "AND   od.lngorderdetailno = " . $lngOrderDetailNo;
    $aryQuery[] = "AND   mo.lngrevisionno = " . $lngRevisioNno;

    $strQuery = implode("\n", $aryQuery);
    list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);
    if (!$lngResultNum) {
        return false;
    }

    $lngFieldsCount = $objDB->getFieldsCount($lngResultID);

    if ($lngMaxFieldsCount) {
        if ($lngFieldsCount > $lngMaxFieldsCount) {
            $lngFieldsCount = $lngMaxFieldsCount;
        }

    }

    $aryResult = $objDB->fetchArray($lngResultID, 0);

    $objDB->freeResult($lngResultID);
    return $aryResult;
}

function fncGetOtherOrderDetail($lngorderNo, $lngrevisionno, $objDB)
{
    $aryQuery[] = "SELECT DISTINCT";
    $aryQuery[] = "    mo.strordercode || '_' || TO_CHAR(mo.lngrevisionno, 'FM00') AS strordercode";
    $aryQuery[] = "  , null as lngpurchaseorderdetailno";
    $aryQuery[] = "  , mo.lngorderstatuscode";
    $aryQuery[] = "  , od.strproductcode";
    $aryQuery[] = "  , mp.strproductname";
    $aryQuery[] = "  , od.dtmdeliverydate";
    $aryQuery[] = "  , mc.strcompanydisplaycode";
    $aryQuery[] = "  , mc.strcompanydisplayname";
    $aryQuery[] = "  , od.lngstocksubjectcode";
    $aryQuery[] = "  , mss.strstocksubjectname";
    $aryQuery[] = "  , od.lngstockitemcode";
    $aryQuery[] = "  , msi.strstockitemname";
    $aryQuery[] = "  , od.lngdeliverymethodcode";
    $aryQuery[] = "  , od.lngproductunitcode";
    $aryQuery[] = "  , od.lngsortkey";
    $aryQuery[] = "  , mmu.strmonetaryunitsign";
    $aryQuery[] = "  , od.curproductprice";
    $aryQuery[] = "  , od.lngproductquantity";
    $aryQuery[] = "  , od.cursubtotalprice";
    $aryQuery[] = "  , mpu.strproductunitname";
    $aryQuery[] = "  , mdm.strdeliverymethodname";
    $aryQuery[] = "  , od.strnote";
    $aryQuery[] = "  , od.lngorderdetailno";
    $aryQuery[] = "  , mo.lngrevisionno as lngorderrevisionno";
    $aryQuery[] = "  , mo.lngorderno";
    $aryQuery[] = "  , mo.lngmonetaryunitcode";
    $aryQuery[] = "  , mo.lngcustomercompanycode ";
    $aryQuery[] = "FROM";
    $aryQuery[] = "  m_order mo ";
    $aryQuery[] = "  INNER JOIN t_orderdetail od ";
    $aryQuery[] = "    ON mo.lngorderno = od.lngorderno ";
    $aryQuery[] = "    AND mo.lngrevisionno = od.lngrevisionno ";
    $aryQuery[] = "  LEFT JOIN ( ";
    $aryQuery[] = "    SELECT";
    $aryQuery[] = "      m_product.* ";
    $aryQuery[] = "    FROM";
    $aryQuery[] = "      m_product ";
    $aryQuery[] = "      INNER JOIN ( ";
    $aryQuery[] = "        SELECT";
    $aryQuery[] = "          lngproductno";
    $aryQuery[] = "          , strrevisecode";
    $aryQuery[] = "          , MAX(lngrevisionno) as lngrevisionno ";
    $aryQuery[] = "        FROM";
    $aryQuery[] = "          m_product ";
    $aryQuery[] = "        GROUP BY";
    $aryQuery[] = "          lngproductno";
    $aryQuery[] = "          , strrevisecode";
    $aryQuery[] = "      ) mp1 ";
    $aryQuery[] = "        ON mp1.lngproductno = m_product.lngproductno ";
    $aryQuery[] = "        AND mp1.strrevisecode = m_product.strrevisecode ";
    $aryQuery[] = "        AND mp1.lngrevisionno = m_product.lngrevisionno";
    $aryQuery[] = "  ) mp ";
    $aryQuery[] = "    ON od.strproductcode = mp.strproductcode ";
    $aryQuery[] = "    AND mp.strrevisecode = od.strrevisecode ";
    $aryQuery[] = "  LEFT JOIN m_company mc ";
    $aryQuery[] = "    ON mo.lngcustomercompanycode = mc.lngcompanycode ";
    $aryQuery[] = "  LEFT JOIN m_deliverymethod mdm ";
    $aryQuery[] = "    ON od.lngdeliverymethodcode = mdm.lngdeliverymethodcode ";
    $aryQuery[] = "  LEFT JOIN m_stocksubject mss ";
    $aryQuery[] = "    ON od.lngstocksubjectcode = mss.lngstocksubjectcode ";
    $aryQuery[] = "  LEFT JOIN m_stockitem msi ";
    $aryQuery[] = "    ON od.lngstockitemcode = msi.lngstockitemcode ";
    $aryQuery[] = "    AND od.lngstocksubjectcode = msi.lngstocksubjectcode ";
    $aryQuery[] = "  LEFT JOIN m_productprice mpp ";
    $aryQuery[] = "    ON mp.lngproductno = mpp.lngproductno ";
    $aryQuery[] = "    AND od.lngstockitemcode = mpp.lngstockitemcode ";
    $aryQuery[] = "    AND od.lngstocksubjectcode = mpp.lngstocksubjectcode ";
    $aryQuery[] = "  LEFT JOIN m_monetaryunit mmu ";
    $aryQuery[] = "    ON mo.lngmonetaryunitcode = mmu.lngmonetaryunitcode ";
    $aryQuery[] = "  LEFT JOIN m_productunit mpu ";
    $aryQuery[] = "    ON od.lngproductunitcode = mpu.lngproductunitcode ";
    $aryQuery[] = "  inner join ( ";
    $aryQuery[] = "    select";
    $aryQuery[] = "      mo.strordercode";
    $aryQuery[] = "      , mo.lngrevisionno";
    $aryQuery[] = "      , od.strproductcode";
    $aryQuery[] = "      , od.strrevisecode";
    $aryQuery[] = "      , mo.lngcustomercompanycode";
    $aryQuery[] = "      , mo.lngmonetaryunitcode";
    $aryQuery[] = "      , msi.lngestimateareaclassno ";
    $aryQuery[] = "    from";
    $aryQuery[] = "      m_order mo ";
    $aryQuery[] = "      LEFT JOIN t_orderdetail od ";
    $aryQuery[] = "        ON mo.lngorderno = od.lngorderno ";
    $aryQuery[] = "        AND mo.lngrevisionno = od.lngrevisionno ";
    $aryQuery[] = "      LEFT JOIN m_stocksubject mss ";
    $aryQuery[] = "        ON od.lngstocksubjectcode = mss.lngstocksubjectcode ";
    $aryQuery[] = "      LEFT JOIN m_stockitem msi ";
    $aryQuery[] = "        ON od.lngstockitemcode = msi.lngstockitemcode ";
    $aryQuery[] = "        AND od.lngstocksubjectcode = msi.lngstocksubjectcode ";
    $aryQuery[] = "    WHERE";
    $aryQuery[] = "      mo.lngorderno = " . $lngorderNo ." AND mo.lngrevisionno = ". $lngrevisionno."";
    $aryQuery[] = "  ) m_key ";
    $aryQuery[] = "    ON od.strproductcode = m_key.strproductcode ";
    $aryQuery[] = "    AND od.strrevisecode = m_key.strrevisecode ";
    $aryQuery[] = "    AND mo.strordercode = m_key.strordercode ";
    $aryQuery[] = "    AND mo.lngcustomercompanycode = m_key.lngcustomercompanycode ";
    $aryQuery[] = "    AND mo.lngmonetaryunitcode = m_key.lngmonetaryunitcode ";
    $aryQuery[] = "    AND msi.lngestimateareaclassno = m_key.lngestimateareaclassno ";
    $aryQuery[] = "inner join ( ";
    $aryQuery[] = "    select ";
    $aryQuery[] = "        lngorderno  ";
    $aryQuery[] = "       ,MAX(lngrevisionno) as lngrevisionno ";
    $aryQuery[] = "    from m_order ";
    $aryQuery[] = "    where lngorderno not in (select lngorderno from m_order where lngrevisionno < 0) ";
    $aryQuery[] = "    group by lngorderno ";
    $aryQuery[] = ") mo_rev ";
    $aryQuery[] = "    on mo_rev.lngorderno = mo.lngorderno ";
    $aryQuery[] = "    and mo_rev.lngrevisionno = mo.lngrevisionno ";
    $aryQuery[] = "WHERE";
    $aryQuery[] = "  mo.lngorderstatuscode = 1";

    $strQuery = implode("\n", $aryQuery);


    list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);
    if (!$lngResultNum) {
        return array();
    }

    $lngFieldsCount = $objDB->getFieldsCount($lngResultID);

    if ($lngMaxFieldsCount) {
        if ($lngFieldsCount > $lngMaxFieldsCount) {
            $lngFieldsCount = $lngMaxFieldsCount;
        }

    }

    for ($i = 0; $i < $lngResultNum; $i++) {
        $aryResult[] = $objDB->fetchArray($lngResultID, $i);
    }

    $objDB->freeResult($lngResultID);
    return $aryResult;
}


/**
 * 発注明細HTMLデータ作成
 *
 * @param   Array   $aryOrderDetail     発注明細データ
 * @param   String  $strDelivery        運搬方法
 * @access  public
 *
 */
function fncGetOrderDetailHtml($aryOrderDetail, $strDelivery, $aryData)
{
    $aryResult = array();
    $tableA_chkbox_body_html = "";
    $tableA_body_html = "";
    $tableB_no_body_html = "";
    $tableB_body_html = "";

    $lngOrderNos = explode(",", $aryData["lngOrderNo"]);
    if ($lngOrderNos) {
        // 表示項目の抽出
        foreach ($lngOrderNos as $key) {
            $lngOrderNos[$key] = $key;
        }
    }

    for ($i = 0; $i < count($aryOrderDetail); $i++) {
        $strHtml = "";
        if (array_key_exists($aryOrderDetail[$i]["lngorderno"], $lngOrderNos)) {
            $tableB_no_body_html .= "<tr><td>" . ($i + 1) . "</td></tr>";
        } else {
            $tableA_chkbox_body_html .= "<tr>";
            // 確定選択(チェックボックス)
            $tableA_chkbox_body_html .= "<td class=\"detailCheckbox\" style=\"width:20px;align-items: center;\"><input type=\"checkbox\" name=\"edit\"></td>";
            $tableA_chkbox_body_html .= "</tr>";
        }
        $strHtml .= "<tr>";
        $strDisplayValue = "";
        // 仕入科目
        $strDisplayCode = htmlspecialchars($aryOrderDetail[$i]["lngstocksubjectcode"]);
        $strDisplayValue = htmlspecialchars($aryOrderDetail[$i]["strstocksubjectname"]);
        $strHtml .= "<td class=\"detailStockSubjectName\">[" . $strDisplayCode . "] " . $strDisplayValue . "</td>";
        // 仕入部品
        $strDisplayCode = htmlspecialchars($aryOrderDetail[$i]["lngstockitemcode"]);
        $strDisplayValue = htmlspecialchars($aryOrderDetail[$i]["strstockitemname"]);
        $strHtml .= "<td class=\"detailStockItemName\">[" . $strDisplayCode . "] " . $strDisplayValue . "</td>";
        // 運搬方法
        $strDisplayCode = htmlspecialchars($aryOrderDetail[$i]["lngstockitemcode"]);
        $strDisplayValue = htmlspecialchars($aryOrderDetail[$i]["strstockitemname"]);
        $strHtml .= "<td class=\"detailDeliveryMethodCode\"><select name=\"optDelivery\">" . $strDelivery . "</select></td>";
        // 単価        
        if (!$aryOrderDetail[$i]["curproductprice"]) {
            $strDisplayValue = convertPrice($aryOrderDetail[$i]["lngmonetaryunitcode"], $aryOrderDetail[$i]["strmonetaryunitsign"], 0, "unitprice");
        } else {
            $strDisplayValue = convertPrice($aryOrderDetail[$i]["lngmonetaryunitcode"], $aryOrderDetail[$i]["strmonetaryunitsign"], $aryOrderDetail[$i]["curproductprice"], "unitprice");
        }
        $strHtml .= "<td class=\"detailProductPrice\" style=\"text-align:right;\">" . $strDisplayValue . "</td>";
        // 数量
        $strDisplayValue = htmlspecialchars($aryOrderDetail[$i]["lngproductquantity"]);
        $strHtml .= "<td class=\"detailProductQuantity\" style=\"text-align:right;\">" . number_format($strDisplayValue) . "</td>";
        // 税抜金額        
        if (!$aryOrderDetail[$i]["cursubtotalprice"]) {
            $strDisplayValue = convertPrice($aryOrderDetail[$i]["lngmonetaryunitcode"], $aryOrderDetail[$i]["strmonetaryunitsign"], 0, "price");
        } else {
            $strDisplayValue = convertPrice($aryOrderDetail[$i]["lngmonetaryunitcode"], $aryOrderDetail[$i]["strmonetaryunitsign"], $aryOrderDetail[$i]["cursubtotalprice"], "price");
        }       
        $strHtml .= "<td class=\"detailSubtotalPrice\" style=\"text-align:right;\">" . $strDisplayValue . "</td>";
        // 納期
        $strDisplayValue = str_replace("-", "/", htmlspecialchars($aryOrderDetail[$i]["dtmdeliverydate"]));
        $strHtml .= "<td class=\"detailDeliveryDate\">" . $strDisplayValue . "</td>";
        // 備考
        $strDisplayValue = htmlspecialchars($aryOrderDetail[$i]["strnote"]);
        // $strHtml .= "<td class=\"detailNote\">". $strDisplayValue . "</td>";
        $strHtml .= "<td class=\"detailNote\"><input type=\"text\" class=\"form-control form-control-sm txt-kids\" style=\"width:240px;\" value=\"". $strDisplayValue ."\"></td>";
//       // 運搬方法(明細入力用)
//        $strHtml .= "<td class=\"forEdit detailDeliveryMethod\"><select name=\"optDelivery\">" . $strDelivery . "</select></td>";
        // 単位コード(明細登録用)
        $strDisplayCode = htmlspecialchars($aryOrderDetail[$i]["lngproductunitcode"]);
        $strHtml .= "<td class=\"forEdit detailProductUnitCode\">" . $strDisplayCode . "</td>";
        // 発注番号(明細登録用)
        $strDisplayCode = htmlspecialchars($aryOrderDetail[$i]["lngorderno"]);
        $strHtml .= "<td class=\"forEdit detailOrderNo\">" . $strDisplayCode . "</td>";
        // リビジョン番号(明細登録用)
        $strDisplayCode = htmlspecialchars($aryOrderDetail[$i]["lngrevisionno"]);
        $strHtml .= "<td class=\"forEdit detailRevisionNo\">" . $strDisplayCode . "</td>";
        // 仕入科目コード(明細登録用)
        $strDisplayCode = htmlspecialchars($aryOrderDetail[$i]["lngstocksubjectcode"]);
        $strHtml .= "<td class=\"forEdit detailStockSubjectCode\">" . $strDisplayCode . "</td>";
        // 仕入部品コード(明細登録用)
        $strDisplayCode = htmlspecialchars($aryOrderDetail[$i]["lngstockitemcode"]);
        $strHtml .= "<td class=\"forEdit detailStockItemCode\">" . $strDisplayCode . "</td>";
        // 通貨単位コード(明細登録用)
        $strDisplayCode = htmlspecialchars($aryOrderDetail[$i]["lngmonetaryunitcode"]);
        $strHtml .= "<td class=\"forEdit detailMonetaryUnitCode\">" . $strDisplayCode . "</td>";
        // 仕入先コード(明細登録用)
        $strDisplayCode = htmlspecialchars($aryOrderDetail[$i]["lngcustomercompanycode"]);
        $strHtml .= "<td class=\"forEdit detailCustomerCompanyCode\">" . $strDisplayCode . "</td>";
        // 発注NO.(明細登録用)
        $strDisplayValue = htmlspecialchars($aryOrderDetail[$i]["strordercode"]);
        $strHtml .= "<td class=\"forEdit detailOrderCode\">" . $strDisplayValue . "</td>";
        // 明細行番号(明細登録用)
        $strDisplayValue = htmlspecialchars($aryOrderDetail[$i]["lngorderdetailno"]);
        $strHtml .= "<td class=\"forEdit detailOrderDetailNo\">" . $strDisplayValue . "</td>";
        $strHtml .= "</tr>";

        if (array_key_exists($aryOrderDetail[$i]["lngorderno"], $lngOrderNos)) {
            $tableB_body_html .= $strHtml;
        } else {
            $tableA_body_html .= $strHtml;
        }
    }

    $aryResult["tableB_no_body"] = $tableB_no_body_html;
    $aryResult["tableA_chkbox_body"] = $tableA_chkbox_body_html;
    $aryResult["tableB_body"] = $tableB_body_html;
    $aryResult["tableA_body"] = $tableA_body_html;

    return $aryResult;
}

/**
 * 発注明細HTMLデータ作成
 *
 * @param   Array   $aryOrderDetail     発注明細データ
 * @param   String  $strDelivery        運搬方法
 * @access  public
 *
 */
function fncGetOtherOrderDetailHtml($aryOrderDetail, $strDelivery, $aryData)
{
    $aryResult = array();
    $tableA_chkbox_body_html = "";
    $tableA_body_html = "";

    $lngOrderNos = explode(",", $aryData["lngOrderNo"]);
    if ($lngOrderNos) {
        // 表示項目の抽出
        foreach ($lngOrderNos as $key) {
            $lngOrderNos[$key] = $key;
        }
    }

    for ($i = 0; $i < count($aryOrderDetail); $i++) {
        $strHtml = "";
        if (array_key_exists($aryOrderDetail[$i]["lngorderno"], $lngOrderNos)) {
            $tableB_no_body_html .= "<tr><td>" . ($i + 1) . "</td></tr>";
        } else {
            $tableA_chkbox_body_html .= "<tr>";
            // 確定選択(チェックボックス)
            $tableA_chkbox_body_html .= "<td class=\"detailCheckbox\" style=\"width:20px;align-items: center;\"><input type=\"checkbox\" name=\"edit\"></td>";
            $tableA_chkbox_body_html .= "</tr>";
        }
        $strHtml .= "<tr>";
        $strDisplayValue = "";
        // 仕入科目
        $strDisplayCode = htmlspecialchars($aryOrderDetail[$i]["lngstocksubjectcode"]);
        $strDisplayValue = htmlspecialchars($aryOrderDetail[$i]["strstocksubjectname"]);
        $strHtml .= "<td class=\"detailStockSubjectCode\">[" . $strDisplayCode . "] " . $strDisplayValue . "</td>";
        // 仕入部品
        $strDisplayCode = htmlspecialchars($aryOrderDetail[$i]["lngstockitemcode"]);
        $strDisplayValue = htmlspecialchars($aryOrderDetail[$i]["strstockitemname"]);
        $strHtml .= "<td class=\"detailStockItemCode\">[" . $strDisplayCode . "] " . $strDisplayValue . "</td>";
        // 運搬方法
        $strHtml .= "<td class=\"detailDeliveryMethodCode\"><select name=\"lngdeliverymethodcode\">" . $strDelivery . "</select></td>";

        // 単価        
        if (!$aryOrderDetail[$i]["curproductprice"]) {
            $strDisplayValue = convertPrice($aryOrderDetail[$i]["lngmonetaryunitcode"], $aryOrderDetail[$i]["strmonetaryunitsign"], 0, "unitprice");
        } else {
            $strDisplayValue = convertPrice($aryOrderDetail[$i]["lngmonetaryunitcode"], $aryOrderDetail[$i]["strmonetaryunitsign"], $aryOrderDetail[$i]["curproductprice"], "unitprice");
        }
        $strHtml .= "<td class=\"detailProductPrice\" style=\"text-align:right;\">" . $strDisplayValue . "</td>";
        // 数量
        $strDisplayValue = htmlspecialchars($aryOrderDetail[$i]["lngproductquantity"]);
        $strHtml .= "<td class=\"detailProductQuantity\" style=\"text-align:right;\">" . number_format($strDisplayValue) . "</td>";
        // 税抜金額        
        if (!$aryOrderDetail[$i]["cursubtotalprice"]) {
            $strDisplayValue = convertPrice($aryOrderDetail[$i]["lngmonetaryunitcode"], $aryOrderDetail[$i]["strmonetaryunitsign"], 0, "price");
        } else {
            $strDisplayValue = convertPrice($aryOrderDetail[$i]["lngmonetaryunitcode"], $aryOrderDetail[$i]["strmonetaryunitsign"], $aryOrderDetail[$i]["cursubtotalprice"], "price");
        }       
        $strHtml .= "<td class=\"detailSubtotalPrice\" style=\"text-align:right;\">" . $strDisplayValue . "</td>";
        // 納期
        $strDisplayValue = str_replace("-", "/", htmlspecialchars($aryOrderDetail[$i]["dtmdeliverydate"]));
        $strHtml .= "<td class=\"detailDeliveryDate\">" . $strDisplayValue . "</td>";
        // 備考
        $strDisplayValue = htmlspecialchars($aryOrderDetail[$i]["strnote"]);
        $strHtml .= "<td class=\"detailDetailNote\"><input type=\"text\" class=\"form-control form-control-sm txt-kids\" style=\"width:240px;\" value=\"". $strDisplayValue ."\"></td>";
        $strHtml .= "<td style=\"display:none;\"><input type=\"hidden\" name=\"strProductUnitName\" value=\"" . $aryOrderDetail[$i]["strproductunitname"] . "\"></td>";
        $strHtml .= "<td style=\"display:none;\"><input type=\"hidden\" name=\"lngorderno\" value=\"" . $aryOrderDetail[$i]["lngorderno"] . "\"></td>";
        $strHtml .= "<td style=\"display:none;\"><input type=\"hidden\" name=\"lngorderrevisionno\" value=\"" . $aryOrderDetail[$i]["lngorderrevisionno"] . "\"></td>";
        $strHtml .= "<td style=\"display:none;\"><input type=\"hidden\" name=\"lngorderdetailno\" value=\"" . $aryOrderDetail[$i]["lngorderdetailno"] . "\"></td>";
        $strHtml .= "</tr>";

        $tableA_body_html .= $strHtml;
    }

    $aryResult["tableB_no_body"] = $tableB_no_body_html;
    $aryResult["tableA_chkbox_body"] = $tableA_chkbox_body_html;
    $aryResult["tableB_body"] = $tableB_body_html;
    $aryResult["tableA_body"] = $tableA_body_html;

    return $aryResult;
}

/**
 * 発注書マスタ検索
 *
 * @param   Array   $aryPurchaseOrderNo     発注書番号
 * @param   Object  $objDB                  DBオブジェクト
 * @access  public
 *
 */
function fncGetPurchaseOrder($aryPurchaseOrderNo, $objDB)
{
    for ($i = 0; $i < count($aryPurchaseOrderNo); $i++) {
        $aryQuery = [];

        $aryQuery[] = "SELECT ";
        $aryQuery[] = "   mp.lngpurchaseorderno";
        $aryQuery[] = "  ,mp.lngrevisionno";
        $aryQuery[] = "  ,mp.strordercode";
        $aryQuery[] = "  ,mp.strcustomername";
        $aryQuery[] = "  ,mp.strproductcode";
        $aryQuery[] = "  ,mp.strrevisecode";
        $aryQuery[] = "  ,mp.strproductname";
        $aryQuery[] = "  ,mp.strdeliveryplacename";
        $aryQuery[] = "  ,mp.lngmonetaryunitcode";
        $aryQuery[] = "  ,mp.strmonetaryunitsign";
        $aryQuery[] = "  ,tp.lngpurchaseorderdetailno";
        $aryQuery[] = "  ,tp.strstockitemname";
        $aryQuery[] = "  ,tp.strdeliverymethodname";
        $aryQuery[] = "  ,tp.curproductprice";
        $aryQuery[] = "  ,tp.lngproductquantity";
        $aryQuery[] = "  ,tp.strproductunitname";
        $aryQuery[] = "  ,tp.cursubtotalprice";
        $aryQuery[] = "  ,TO_CHAR(tp.dtmdeliverydate, 'YYYY/MM/DD') AS dtmdeliverydate";
        $aryQuery[] = "FROM m_purchaseorder mp";
        $aryQuery[] = "INNER JOIN t_purchaseorderdetail tp";
        $aryQuery[] = "  ON  mp.lngpurchaseorderno = tp.lngpurchaseorderno";
        $aryQuery[] = "  AND mp.lngrevisionno = tp.lngrevisionno";
        $aryQuery[] = "WHERE mp.lngpurchaseorderno = " . $aryPurchaseOrderNo[$i]["lngpurchaseorderno"];
        $aryQuery[] = "AND   mp.lngrevisionno = " . $aryPurchaseOrderNo[$i]["lngrevisionno"];
        $aryQuery[] = "ORDER BY";
        $aryQuery[] = "   mp.lngpurchaseorderno";
        $aryQuery[] = "  ,mp.lngrevisionno";
        $aryQuery[] = "  ,tp.lngpurchaseorderdetailno";

        $strQuery = "";
        $strQuery = implode("\n", $aryQuery);

        list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);
        if (!$lngResultNum) {
            return false;
        }

        $lngFieldsCount = $objDB->getFieldsCount($lngResultID);

        if ($lngMaxFieldsCount) {
            if ($lngFieldsCount > $lngMaxFieldsCount) {
                $lngFieldsCount = $lngMaxFieldsCount;
            }

        }

        for ($i = 0; $i < $lngResultNum; $i++) {
            $aryResult[] = $objDB->fetchArray($lngResultID, $i);
        }

        $objDB->freeResult($lngResultID);
    }

    return $aryResult;
}

/**        funPulldownMenu()関数
 *
 *        プルダウンメニューの生成
 *
 *        @param Long        $lngProcessNo        // 処理番号
 *        @param Long        $lngValueCode        // value値
 *        @param String    $strWhere            // 条件
 *        @param Object    $objDB                // DB接続オブジェクト
 *        @return    Array    $strPulldownMenu
 */
function fncGetPulldownMenu($lngProcessNo, $lngValueCode, $strWhere, $objDB)
{
    switch ($lngProcessNo) {
        case 0:
            $strPulldownMenu = fncGetPulldown("m_paycondition", "lngpayconditioncode", "strpayconditionname", $lngValueCode, $strWhere, $objDB);
            break;
        case 1:
            $strPulldownMenu = fncGetPulldown("m_monetaryunit", "lngmonetaryunitcode", "strmonetaryunitname", $lngValueCode, $strWhere, $objDB);
            break;
        case 2:
            $strPulldownMenu = fncGetPulldown("m_deliverymethod", "lngdeliverymethodcode", "strdeliverymethodname", $lngValueCode, $strWhere, $objDB);
            break;
    }
    return $strPulldownMenu;
}

/**
 * 発注マスタ更新
 *
 * @param   Array   $aryUpdate          更新発注データ
 * @param   Array   $aryUpdateDetail    更新発注明細データ
 * @param   Object  $objDB              DBオブジェクト
 * @access  public
 *
 */
function fncUpdateOrder($aryUpdate, $aryUpdateDetail, $objDB)
{
    $lngcountrycode = fncGetMasterValue("m_company", "strcompanydisplaycode", "lngcountrycode", $aryUpdate["lngdeliveryplacecode"] . ":str", '', $objDB);
    $lngdeliveryplacecode = fncGetMasterValue("m_company", "strcompanydisplaycode", "lngcompanycode", $aryUpdate["lngdeliveryplacecode"] . ":str", '', $objDB);

    for ($i = 0; $i < count($aryUpdateDetail); $i++) {
        $aryQuery = [];
        // m_order更新
        $aryQuery[] = "UPDATE m_order SET";
        $aryQuery[] = "   lngdeliveryplacecode = " . $lngdeliveryplacecode;
        $aryQuery[] = "  ,lngorderstatuscode = " . intVal($aryUpdate["lngorderstatuscode"]);
        $aryQuery[] = "WHERE lngorderno = " . intVal($aryUpdateDetail[$i]["lngorderno"]);
        $aryQuery[] = "AND   lngrevisionno = " . intVal($aryUpdateDetail[$i]["lngrevisionno"]);

        $strQuery = "";
        $strQuery = implode("\n", $aryQuery);

        if (!$lngResultID = $objDB->execute($strQuery)) {
            fncOutputError(9051, DEF_ERROR, "発注マスタへの更新処理に失敗しました。", true, "", $objDB);
            return false;
        }
        $objDB->freeResult($lngResultID);

    }

    return true;
}

/**
 * 発注明細更新
 *
 * @param   Array   $aryUpdate      発注マスタデータ
 * @param   Array   $aryDetail      発注明細データ
 * @param   Object  $objDB          DBオブジェクト
 * @access  public
 *
 */
function fncUpdateOrderDetail($aryUpdate, $aryDetail, $objDB)
{
    // t_orderdetail更新
    for ($i = 0; $i < count($aryDetail); $i++) {
        $aryDetailQuery = array();
        $aryDetailQuery[] = "UPDATE t_orderdetail SET";
        $aryDetailQuery[] = "   lngdeliverymethodcode = " . intval($aryDetail[$i]["lngdeliverymethodcode"]);
        $aryDetailQuery[] = "  ,lngsortkey = " . intval($aryDetail[$i]["lngsortkey"]);
        $aryDetailQuery[] = "  ,lngproductunitcode = " . intval($aryDetail[$i]["lngproductunitcode"]);
        $aryDetailQuery[] = "  ,strnote = '" . $aryDetail[$i]["strnote"]. "'";
        if (
            ($aryDetail[$i]["lngstocksubjectcode"] == 433 and $aryDetail[$i]["lngstockitemcode"] == 1)
            or 
            ($aryDetail[$i]["lngstocksubjectcode"] == 431 and $aryDetail[$i]["lngstockitemcode"] == 8)
        ) {
            $strmoldno = fncGetMoldNo( 
                             $aryUpdate["strProductCode"], 
                             $aryUpdate["strReviseCode"], 
                             $aryDetail[$i]["lngstocksubjectcode"], 
                             $aryDetail[$i]["lngstockitemcode"],
                             $objDB
                         );
            $aryDetailQuery[] = "  ,strmoldno = '" . $strmoldno . "'";
        }
        $aryDetailQuery[] = "WHERE lngorderno = " . intval($aryDetail[$i]["lngorderno"]);
        $aryDetailQuery[] = "AND   lngorderdetailno = " . intval($aryDetail[$i]["lngorderdetailno"]);
        $aryDetailQuery[] = "AND   lngrevisionno = " . intval($aryDetail[$i]["lngrevisionno"]);

        $strDetailQuery = "";
        $strDetailQuery = implode("\n", $aryDetailQuery);
        if (!$lngResultID = $objDB->execute($strDetailQuery)) {
            fncOutputError(9051, DEF_ERROR, "発注明細テーブルへの更新処理に失敗しました。", true, "", $objDB);
            return false;
        }
        $objDB->freeResult($lngResultID);
    }
    return true;
}

/**
 * 発注書マスタ登録
 *
 * @param   Array   $aryOrder       発注マスタ
 * @param   Array   $aryOrderDetail 発注明細
 * @param   Object  $objAuth        権限
 * @param   Object  $objDB          DBオブジェクト
 * @access  public
 *
 */
function fncInsertPurchaseOrderByDetail($aryOrder, $aryOrderDetail, $objAuth, $objDB)
{

    require_once LIB_DEBUGFILE;
    $key1 = "lngcustomercompanycode";
    $key2 = "lngmonetaryunitcode";
    $group = [];
    foreach ($aryOrderDetail as $row) {
        $notKeys = array_filter($row, function ($key) use ($key1, $key2) {
            return $key !== $key1 && $key !== $key2;
        }, ARRAY_FILTER_USE_KEY);

        $group[$row[$key1]][$row[$key2]][] = $notKeys;
    }

    foreach ($group as $row) {
        $aryResult = [];
        foreach ($row as $detail) {
            $curTotalPrice = 0.0;
            foreach ($detail as $order) {
                $aryOrderNo[] = $order["lngorderno"];
                $detail = fncGetOrderDetail2($order["lngorderno"], $order["lngorderdetailno"], $order["lngrevisionno"], $objDB);
                $aryOrderDetailUpdate[] = $detail;
                $curTotalPrice += floatval($detail["cursubtotalprice"]);
            }

            for ($i = 0; $i < count($aryOrderDetailUpdate); $i++) {
                if ($i == 0) {
                    // 発注書マスタ登録
                    $lngpurchaseorderno = fncGetSequence("m_purchaseorder.lngpurchaseorderno", $objDB);
//                    $lngrevisionno = $aryOrderDetailUpdate[$i]["lngrevisionno"] == null ? 0 : intval($aryOrderDetailUpdate[$i]["lngrevisionno"]) + 1;
                    $lngrevisionno = 0;
                    $ym = date('ym');
                    $year = date('y');
                    $month = date('m');
//fncDebug("kids2.log", $year . "/" . $month, __FILE__, __LINE__, "a");
                    //                    $lngorderno = fncGetSequence("m_purchaseorder.strordercode." . $ym, $objDB);
                    $lngorderno = fncGetDateSequence($year, $month, "m_purchaseorder.strordercode", $objDB);
//fncDebug("kids2.log", $lngorderno, __FILE__, __LINE__, "a");
                    $customer = fncGetCompany($aryOrderDetailUpdate[$i]["lngcustomercompanycode"], $objDB);
                    $delivery = fncGetCompany($aryOrderDetailUpdate[$i]["lngdeliveryplacecode"], $objDB);
                    $payconditioncode = $aryOrder["lngpayconditioncode"];
                    $paycondition = fncGetPayCondition($payconditioncode, $objDB);
                    $datestring = "'" . fncGetDateTimeString() . "'";
                    $aryQuery[] = "INSERT INTO m_purchaseorder (";
                    $aryQuery[] = "   lngpurchaseorderno";
                    $aryQuery[] = "  ,lngrevisionno";
                    $aryQuery[] = "  ,strordercode";
                    $aryQuery[] = "  ,lngcustomercode";
                    $aryQuery[] = "  ,strcustomername";
                    $aryQuery[] = "  ,strcustomercompanyaddreess";
                    $aryQuery[] = "  ,strcustomercompanytel";
                    $aryQuery[] = "  ,strcustomercompanyfax";
                    $aryQuery[] = "  ,strproductcode";
                    $aryQuery[] = "  ,strrevisecode";
                    $aryQuery[] = "  ,strproductname";
                    $aryQuery[] = "  ,strproductenglishname";
                    // $aryQuery[] = "  ,dtmexpirationdate";
                    $aryQuery[] = "  ,lngmonetaryunitcode";
                    $aryQuery[] = "  ,strmonetaryunitname";
                    $aryQuery[] = "  ,strmonetaryunitsign";
                    $aryQuery[] = "  ,lngpayconditioncode";
                    $aryQuery[] = "  ,strpayconditionname";
                    $aryQuery[] = "  ,lnggroupcode";
                    $aryQuery[] = "  ,strgroupname";
                    $aryQuery[] = "  ,txtsignaturefilename";
                    $aryQuery[] = "  ,lngusercode";
                    $aryQuery[] = "  ,strusername";
                    $aryQuery[] = "  ,lngdeliveryplacecode";
                    $aryQuery[] = "  ,strdeliveryplacename";
                    $aryQuery[] = "  ,curtotalprice";
                    $aryQuery[] = "  ,dtminsertdate";
                    $aryQuery[] = "  ,lnginsertusercode";
                    $aryQuery[] = "  ,strinsertusername";
                    $aryQuery[] = "  ,strnote";
                    $aryQuery[] = "  ,lngprintcount";
                    $aryQuery[] = ") VALUES (";
                    $aryQuery[] = "   " . $lngpurchaseorderno;
                    $aryQuery[] = "  ," . $lngrevisionno;
                    $aryQuery[] = "  ,'" . $lngorderno . "'";
                    $aryQuery[] = "  ," . $customer["lngcompanycode"];
                    $aryQuery[] = "  ,'" . $customer["strcompanydisplayname"] . "'";
                    $aryQuery[] = "  ,'" . $customer["straddress1"] . $customer["straddress2"] . $customer["straddress3"] . "'";
                    $aryQuery[] = "  ,'" . $customer["strtel1"] . "'";
                    $aryQuery[] = "  ,'" . $customer["strfax1"] . "'";
                    $aryQuery[] = "  ,'" . $aryOrderDetailUpdate[$i]["strproductcode"] . "'";
                    $aryQuery[] = "  ,'" . $aryOrderDetailUpdate[$i]["strrevisecode"] . "'";
                    $aryQuery[] = "  ,'" . $aryOrderDetailUpdate[$i]["strproductname"] . "'";
                    $aryQuery[] = "  ,'" . $aryOrderDetailUpdate[$i]["strproductenglishname"] . "'";
                    // $aryQuery[] = "  ,'" . $aryOrder["dtmexpirationdate"] . "'";
                    $aryQuery[] = "  ," . $aryOrderDetailUpdate[$i]["lngmonetaryunitcode"];
                    $aryQuery[] = "  ,'" . $aryOrderDetailUpdate[$i]["strmonetaryunitname"] . "'";
                    $aryQuery[] = "  ,'" . (($aryOrderDetailUpdate[$i]["lngmonetaryunitcode"] == 1) ? "\\\\" : $aryOrderDetailUpdate[$i]["strmonetaryunitsign"]) . "'";
                    $aryQuery[] = "  ," . $payconditioncode;
                    $aryQuery[] = "  ,'" . $paycondition["strpayconditionname"] . "'";
                    $aryQuery[] = "  ," . $aryOrderDetailUpdate[$i]["lnggroupcode"];
                    $aryQuery[] = "  ,'" . $aryOrderDetailUpdate[$i]["strgroupdisplayname"] . "'";
                    $aryQuery[] = "  , (select txtsignaturefilename from m_signature where lnggroupcode = " . $aryOrderDetailUpdate[$i]["lnggroupcode"] . " and dtmapplystartdate <= " . $datestring . " and dtmapplyenddate >= " . $datestring .")";
                    $aryQuery[] = "  ," . $aryOrderDetailUpdate[$i]["lngusercode"];
                    $aryQuery[] = "  ,'" . $aryOrderDetailUpdate[$i]["struserdisplayname"] . "'";
                    $aryQuery[] = "  ," . $delivery["lngcompanycode"];
                    $aryQuery[] = "  ,'" . $delivery["strcompanydisplayname"] . "'";
                    $aryQuery[] = "  ," . $curTotalPrice; //  . $aryOrderDetailUpdate[$i][0]["curtotalprice"];
                    $aryQuery[] = "  ,'" . fncGetDateTimeString() . "'";
                    $aryQuery[] = "  ,'" . $objAuth->UserCode . "'";
                    $aryQuery[] = "  ,'" . $objAuth->UserDisplayName . "'";
                    $aryQuery[] = "  ,'" . $aryOrder["strnote"] . "'";
                    $aryQuery[] = "  ,0";
                    $aryQuery[] = ")";

                    $strQuery = implode("\n", $aryQuery);
                    if (!$lngResultID = $objDB->execute($strQuery)) {
                        fncOutputError(9051, DEF_ERROR, "発注書マスタへの更新処理に失敗しました。", true, "", $objDB);
                        return null;
                    }
                    //$aryResult[] = $lngpurchaseorderno . "-" . $lngrevisionno;
                    $aryResult[$i]["lngpurchaseorderno"] = $lngpurchaseorderno;
                    $aryResult[$i]["lngrevisionno"] = $lngrevisionno;
                    $objDB->freeResult($lngResultID);
                }

                // 発注書明細登録
                $aryQueryDetail = [];
                $aryQueryDetail[] = "INSERT INTO t_purchaseorderdetail ( ";
                $aryQueryDetail[] = "   lngpurchaseorderno";
                $aryQueryDetail[] = "  ,lngpurchaseorderdetailno";
                $aryQueryDetail[] = "  ,lngrevisionno";
                $aryQueryDetail[] = "  ,lngorderno";
                $aryQueryDetail[] = "  ,lngorderdetailno";
                $aryQueryDetail[] = "  ,lngorderrevisionno";
                $aryQueryDetail[] = "  ,lngstocksubjectcode";
                $aryQueryDetail[] = "  ,lngstockitemcode";
                $aryQueryDetail[] = "  ,strstockitemname";
                $aryQueryDetail[] = "  ,lngdeliverymethodcode";
                $aryQueryDetail[] = "  ,strdeliverymethodname";
                $aryQueryDetail[] = "  ,curproductprice";
                $aryQueryDetail[] = "  ,lngproductquantity";
                $aryQueryDetail[] = "  ,lngproductunitcode";
                $aryQueryDetail[] = "  ,strproductunitname";
                $aryQueryDetail[] = "  ,cursubtotalprice";
                $aryQueryDetail[] = "  ,dtmdeliverydate";
                $aryQueryDetail[] = "  ,strnote";
                $aryQueryDetail[] = "  ,lngsortkey";
                $aryQueryDetail[] = ") VALUES (";
                $aryQueryDetail[] = "   " . $lngpurchaseorderno;
                $aryQueryDetail[] = "  ," . ($i + 1);
                $aryQueryDetail[] = "  ," . $lngrevisionno;
                $aryQueryDetail[] = "  ," . $aryOrderDetailUpdate[$i]["lngorderno"];
                $aryQueryDetail[] = "  ," . $aryOrderDetailUpdate[$i]["lngorderdetailno"];
                $aryQueryDetail[] = "  ," . $aryOrderDetailUpdate[$i]["lngrevisionno"];
                $aryQueryDetail[] = "  ," . $aryOrderDetailUpdate[$i]["lngstocksubjectcode"];
                $aryQueryDetail[] = "  ," . $aryOrderDetailUpdate[$i]["lngstockitemcode"];
                $aryQueryDetail[] = "  ,'" . $aryOrderDetailUpdate[$i]["strstockitemname"] . "'";
                $aryQueryDetail[] = "  ," . $aryOrderDetail[$i]["lngdeliverymethodcode"];
                $aryQueryDetail[] = "  ,'" . $aryOrderDetail[$i]["strdeliverymethodname"] . "'";
                $aryQueryDetail[] = "  ," . $aryOrderDetailUpdate[$i]["curproductprice"];
                $aryQueryDetail[] = "  ," . $aryOrderDetailUpdate[$i]["lngproductquantity"];
                $aryQueryDetail[] = "  ," . $aryOrderDetailUpdate[$i]["lngproductunitcode"];
                $aryQueryDetail[] = "  ,'" . $aryOrderDetailUpdate[$i]["strproductunitname"] . "'";
                $aryQueryDetail[] = "  ," . $aryOrderDetailUpdate[$i]["cursubtotalprice"];
                $aryQueryDetail[] = "  ,'" . $aryOrderDetailUpdate[$i]["dtmdeliverydate"] . "'";
                $aryQueryDetail[] = "  ,'" . $aryOrderDetail[$i]["strnote"] . "'";
                $aryQueryDetail[] = "  ," . ($i + 1);
                $aryQueryDetail[] = ")";

                $strQueryDetail = implode("\n", $aryQueryDetail);

                if (!$lngResultID = $objDB->execute($strQueryDetail)) {
                    fncOutputError(9051, DEF_ERROR, "発注書明細への更新処理に失敗しました。", true, "", $objDB);
                    return null;
                }
                $objDB->freeResult($lngResultID);
            }
        }
    }

    return $aryResult;
}

/**
 * 発注書データHTML変換
 *
 * @param   Array   $aryPutchaseOrder   発注書データ
 * @access  public
 *
 */
function fncCreatePurchaseOrderHtml($aryPurchaseOrder, $strSessionID)
{
    $aryOrderNo = [];
    foreach ($aryPurchaseOrder as $row) {
        $orderno = $row["lngpurchaseorderno"];
        if (!in_array($orderno, $aryOrderNo)) {
            $aryOrderNo[] = $orderno;
        }
    }

    foreach ($aryOrderNo as $orderno) {
        $aryHtml[] = "<p class=\"caption\">発注確定が完了し、以下の発注書が作成されました。</p>";
        for ($i = 0; $i < count($aryPurchaseOrder); $i++) {
            if ($aryPurchaseOrder[$i]["lngpurchaseorderno"] != $orderno) {continue;}
            if ($i == 0) {
                $strUrl = "/list/result/frameset.php?strReportKeyCode=" . $aryPurchaseOrder[$i]["lngpurchaseorderno"] . "&lngReportClassCode=2&strSessionID=" . $strSessionID;
                $aryHtml[] = "<table class=\"ordercode\">";
                $aryHtml[] = "  <tr>";
                $aryHtml[] = "    <td class=\"SegColumn\">PO番号</td>";
                $aryHtml[] = "    <td class=\"ordercodetd\">" . sprintf("%s_%02d", $aryPurchaseOrder[$i]["strordercode"], $aryPurchaseOrder[$i]["lngrevisionno"]) . "</td>";
                $aryHtml[] = "  </tr>";
                $aryHtml[] = "  <tr>";
                $aryHtml[] = "    <td class=\"orderbuttontd\" colspan=\"2\"><a href=\"#\" onclick=\"window.open('" . $strUrl . "', 'listWin', 'width=800,height=600,top=10,left=10,status=yes,scrollbars=yes,directories=no,menubar=yes,resizable=yes,location=no,toolbar=no')\"><img src=\"/img/type01/cmn/querybt/blownpreview_off_bt.gif\" alt=\"preview\"></a>";
                $aryHtml[] = "    <a href=\"#\" onclick=\"window.opener.opener.location.reload();window.opener.close();window.close();\"><img src=\"/img/type01/cmn/querybt/close_blown_off_ja_bt.gif\" alt=\"close\"></a></td>";
                $aryHtml[] = "  </tr>";
                $aryHtml[] = "</table> ";
                $aryHtml[] = "<br>";
                $aryHtml[] = "<table class=\"orderdetail\">";
                $aryHtml[] = "  <tr>";
                $aryHtml[] = "    <th class=\"SegColumn\">製品コード</th>";
                $aryHtml[] = "    <th class=\"SegColumn\">製品名</th>";
                $aryHtml[] = "    <th class=\"SegColumn\">仕入先</th>";
                $aryHtml[] = "    <th class=\"SegColumn\">納品場所</th>";
                $aryHtml[] = "  </tr>";
                $aryHtml[] = "  <tr>";
                $aryHtml[] = "    <td class=\"Segs\">" . $aryPurchaseOrder[$i]["strproductcode"] . "_" . $aryPurchaseOrder[$i]["strrevisecode"] . "</td>";
                $aryHtml[] = "    <td class=\"Segs\">" . $aryPurchaseOrder[$i]["strproductname"] . "</td>";
                $aryHtml[] = "    <td class=\"Segs\">" . $aryPurchaseOrder[$i]["strcustomername"] . "</td>";
                $aryHtml[] = "    <td class=\"Segs\">" . $aryPurchaseOrder[$i]["strdeliveryplacename"] . "</td>";
                $aryHtml[] = "  </tr>";
                $aryHtml[] = "</table>";
                $aryHtml[] = "<table class=\"orderdetail\">";
                $aryHtml[] = "  <tr>";
                $aryHtml[] = "    <th class=\"SegColumn\">No.</th>";
                $aryHtml[] = "    <th class=\"SegColumn\">仕入部品</th>";
                $aryHtml[] = "    <th class=\"SegColumn\">運搬方法</th>";
                $aryHtml[] = "    <th class=\"SegColumn\">単価</th>";
                $aryHtml[] = "    <th class=\"SegColumn\">数量</th>";
                $aryHtml[] = "    <th class=\"SegColumn\">税抜金額</th>";
                $aryHtml[] = "    <th class=\"SegColumn\">納期</th>";
                $aryHtml[] = "  </tr>";
            }
            $aryHtml[] = "  <tr>";
            $aryHtml[] = "    <td class=\"Segs\">" . $aryPurchaseOrder[$i]["lngpurchaseorderdetailno"];
            $aryHtml[] = "    <td class=\"Segs\">" . $aryPurchaseOrder[$i]["strstockitemname"];
            $aryHtml[] = "    <td class=\"Segs\">" . $aryPurchaseOrder[$i]["strdeliverymethodname"];
            $aryHtml[] = "    <td class=\"Segs\">" . convertPrice($aryPurchaseOrder[$i]["lngmonetaryunitcode"], $aryPurchaseOrder[$i]["strmonetaryunitsign"], $aryPurchaseOrder[$i]["curproductprice"], "unitprice");
            $aryHtml[] = "    <td class=\"Segs\">" . number_format($aryPurchaseOrder[$i]["lngproductquantity"]) . " " . $aryPurchaseOrder[$i]["strproductunitname"];
            $aryHtml[] = "    <td class=\"Segs\">" . convertPrice($aryPurchaseOrder[$i]["lngmonetaryunitcode"], $aryPurchaseOrder[$i]["strmonetaryunitsign"], $aryPurchaseOrder[$i]["cursubtotalprice"], "price");
            $aryHtml[] = "    <td class=\"Segs\">" . $aryPurchaseOrder[$i]["dtmdeliverydate"];
            $aryHtml[] = "  </tr>";
        }
        $aryHtml[] = "</table>";
        $aryHtml[] = "<br>";
    }

    $strHtml = "";
    $strHtml = implode("\n", $aryHtml);

    return $strHtml;
}

/**
 * 発注書マスタ更新
 *
 * @param   Integer     $lngputchaseorderno 発注書番号
 * @param   Integer     $lngrevisionno      リビジョン番号
 * @param   Object      $objDb               DBオブジェクト
 * @access  public
 *
 */
function fncGetPurchaseOrderEdit($lngpurchaseorderno, $lngrevisionno, $objDB)
{
    $aryResult = array();
    $aryQuery[] = "SELECT";
    $aryQuery[] = "   mp.lngpurchaseorderno";
    $aryQuery[] = "  ,mp.lngrevisionno";
    $aryQuery[] = "  ,mp.strrevisecode";
    $aryQuery[] = "  ,mp.strordercode";
    $aryQuery[] = "  ,mo.strordercode as order_strordercode";
    $aryQuery[] = "  ,mo.lngrevisionno as order_lngrevisionno";
    $aryQuery[] = "  ,TO_CHAR(mp.dtmexpirationdate, 'YYYY/MM/DD') as dtmexpirationdate";
    $aryQuery[] = "  ,mp.strproductcode";
    $aryQuery[] = "  ,mp.strproductname";
    $aryQuery[] = "  ,mp.strproductenglishname";
    $aryQuery[] = "  ,TO_CHAR(mp.dtminsertdate, 'YYYY/MM/DD') as dtminsertdate";
    $aryQuery[] = "  ,mp.lnggroupcode";
    $aryQuery[] = "  ,mg.strgroupdisplaycode";
    $aryQuery[] = "  ,mg.strgroupdisplayname";
    $aryQuery[] = "  ,mp.lnggroupcode as lnginchargegroupcode";
    $aryQuery[] = "  ,mg.strgroupdisplaycode as strinchargegroupdisplaycode";
    $aryQuery[] = "  ,mg.strgroupdisplayname as strinchargegroupdisplayname";
    $aryQuery[] = "  ,mp.lngusercode as lnginchargeusercode";
    $aryQuery[] = "  ,mu1.struserdisplaycode as strinchargeuserdisplaycode";
    $aryQuery[] = "  ,mu1.struserdisplayname as strinchargeuserdisplayname";
    $aryQuery[] = "  ,mp.lnginsertusercode as lnginputusercode";
    $aryQuery[] = "  ,mu2.struserdisplaycode as strinputuserdisplaycode";
    $aryQuery[] = "  ,mu2.struserdisplayname as strinputuserdisplayname";
    $aryQuery[] = "  ,mp.strcustomercompanyaddreess";
    $aryQuery[] = "  ,mp.strcustomercompanytel";
    $aryQuery[] = "  ,mp.strcustomercompanyfax";
    $aryQuery[] = "  ,mp.lngcustomercode";
    $aryQuery[] = "  ,mc1.strcompanydisplaycode as strcustomercode";
    $aryQuery[] = "  ,mc1.strcompanydisplayname as strcustomername";
    $aryQuery[] = "  ,mc1.strcompanydisplaycode as strcustomerdisplaycode";
    $aryQuery[] = "  ,mc1.strcompanydisplayname as strcustomerdisplayname";
    $aryQuery[] = "  ,mc1.lngcountrycode as lngcountrycode";
    $aryQuery[] = "  ,mp.lngdeliveryplacecode";
    $aryQuery[] = "  ,mc2.strcompanydisplaycode as strdeliveryplacecode";
    $aryQuery[] = "  ,mc2.strcompanydisplayname as strdeliveryplacename";
    $aryQuery[] = "  ,mc2.strcompanydisplaycode as strdeliverydisplaycode";
    $aryQuery[] = "  ,mc2.strcompanydisplayname as strdeliverydisplayname";
    $aryQuery[] = "  ,mp.lngpayconditioncode";
    $aryQuery[] = "  ,mp.strpayconditionname";
    $aryQuery[] = "  ,mp.lngmonetaryunitcode";
    $aryQuery[] = "  ,mp.strmonetaryunitname";
    $aryQuery[] = "  ,mp.strmonetaryunitsign";
    $aryQuery[] = "  ,mp.lngmonetaryratecode";
    $aryQuery[] = "  ,mp.strmonetaryratename";
    $aryQuery[] = "  ,mp.curtotalprice";
    $aryQuery[] = "  ,mp.strnote";
    $aryQuery[] = "  ,mp.lngprintcount";
    $aryQuery[] = "  ,pd.lngpurchaseorderdetailno";
    $aryQuery[] = "  ,pd.lngstocksubjectcode";
    $aryQuery[] = "  ,mss.strstocksubjectname";
    $aryQuery[] = "  ,pd.lngstockitemcode";
    $aryQuery[] = "  ,msi.strstockitemname";
    $aryQuery[] = "  ,pd.lngdeliverymethodcode";
    $aryQuery[] = "  ,pd.strdeliverymethodname";
    $aryQuery[] = "  ,pd.curproductprice";
    $aryQuery[] = "  ,pd.lngproductquantity";
    $aryQuery[] = "  ,pd.lngproductunitcode";
    $aryQuery[] = "  ,pd.strproductunitname";
    $aryQuery[] = "  ,pd.cursubtotalprice";
    $aryQuery[] = "  ,TO_CHAR(pd.dtmdeliverydate, 'YYYY/MM/DD') AS dtmdeliverydate";
    $aryQuery[] = "  ,pd.strnote as strdetailnote";
    $aryQuery[] = "  ,pd.lngsortkey";
    $aryQuery[] = "  ,pd.lngorderno";
    $aryQuery[] = "  ,pd.lngorderdetailno";
    $aryQuery[] = "  ,pd.lngorderrevisionno";
    $aryQuery[] = "FROM m_purchaseorder mp";
    $aryQuery[] = "LEFT JOIN t_purchaseorderdetail pd ON mp.lngpurchaseorderno = pd.lngpurchaseorderno AND mp.lngrevisionno = pd.lngrevisionno";
    $aryQuery[] = "LEFT JOIN m_order mo ON pd.lngorderno = mo.lngorderno AND pd.lngorderrevisionno = mo.lngrevisionno";
    $aryQuery[] = "LEFT JOIN m_group mg ON mp.lnggroupcode = mg.lnggroupcode";
    $aryQuery[] = "LEFT JOIN m_user mu1 ON mp.lngusercode = mu1.lngusercode";
    $aryQuery[] = "LEFT JOIN m_user mu2 ON mp.lnginsertusercode = mu2.lngusercode";
    $aryQuery[] = "LEFT JOIN m_company mc1 ON mp.lngcustomercode = mc1.lngcompanycode";
    $aryQuery[] = "LEFT JOIN m_company mc2 ON mp.lngdeliveryplacecode = mc2.lngcompanycode";
    $aryQuery[] = "LEFT JOIN m_stocksubject mss ON pd.lngstocksubjectcode = mss.lngstocksubjectcode";
    $aryQuery[] = "LEFT JOIN m_stockitem msi ON pd.lngstockitemcode = msi.lngstockitemcode AND pd.lngstocksubjectcode = msi.lngstocksubjectcode";
    $aryQuery[] = "WHERE mp.lngpurchaseorderno = " . $lngpurchaseorderno;
    $aryQuery[] = "AND   mp.lngrevisionno = " . intval($lngrevisionno);
    $aryQuery[] = " ORDER BY";
    $aryQuery[] = "   pd.lngsortkey";

    $strQuery = "";
    $strQuery = implode("\n", $aryQuery);

    list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);
    if (!$lngResultNum) {
        return false;
    }

    for ($i = 0; $i < $lngResultNum; $i++) {
        $aryResult[] = $objDB->fetchArray($lngResultID, $i);
    }

    $objDB->freeResult($lngResultID);

    return $aryResult;
}

/**
 * 発注書明細HTML作成
 *
 * @param   Array   $aryResult
 */
function fncGetPurchaseOrderDetailHtml($aryResult, $objDB)
{
    $result = array();
    for ($i = 0; $i < count($aryResult); $i++) {
        $aryNoHtml[] = "  <tr>";
        $aryNoHtml[] = "      <td name=\"rownum\">" . ($i + 1) . "</td>";
        $aryNoHtml[] = "  </tr>";

        $aryHtml[] = "  <tr>";
        // $aryHtml[] = "      <td class=\"detailOrderCode\">" . sprintf("%s_%02d", $aryResult[$i]["order_strordercode"], $aryResult[$i]["order_lngrevisionno"]) . "</td>";
        // $aryHtml[] = "      <td class=\"detailPurchaseorderDetailNo\">" . $aryResult[$i]["lngpurchaseorderdetailno"] . "</td>";
        $aryHtml[] = "      <td class=\"detailStockSubjectCode\">" . sprintf("[%s] %s", $aryResult[$i]["lngstocksubjectcode"], $aryResult[$i]["strstocksubjectname"]) . "</td>";
        $aryHtml[] = "      <td class=\"detailStockItemCode\">" . sprintf("[%s] %s", $aryResult[$i]["lngstockitemcode"], $aryResult[$i]["strstockitemname"]) . "</td>";
        $aryHtml[] = "      <td class=\"detailDeliveryMethodCode\"><select name=\"lngdeliverymethodcode\">" . fncGetPulldownMenu(2, $aryResult[$i]["lngdeliverymethodcode"], "", $objDB) . "</select></td>";    
        $aryHtml[] = "      <td class=\"detailProductPrice\" style=\"text-align:right;\">" . convertPrice($aryResult[$i]["lngmonetaryunitcode"], $aryResult[$i]["strmonetaryunitsign"], $aryResult[$i]["curproductprice"], 'unitprice') . "</td>";
        $aryHtml[] = "      <td class=\"detailProductQuantity\" style=\"text-align:right;\">" . number_format($aryResult[$i]["lngproductquantity"], 0) . "</td>";
        $aryHtml[] = "      <td class=\"detailSubtotalPrice\" style=\"text-align:right;\">" . convertPrice($aryResult[$i]["lngmonetaryunitcode"], $aryResult[$i]["strmonetaryunitsign"], $aryResult[$i]["cursubtotalprice"], 'price') . "</td>";
        $aryHtml[] = "      <td class=\"detailDeliveryDate\">" . $aryResult[$i]["dtmdeliverydate"] . "</td>";
        $aryHtml[] = "      <td class=\"detailDetailNote\"><input type=\"text\" class=\"form-control form-control-sm txt-kids\" style=\"width:240px;\" value=\"". $aryResult[$i]["strdetailnote"] ."\"></td>";
        
        $aryHtml[] = "      <td style=\"display:none;\"><input type=\"hidden\" name=\"strProductUnitName\" value=\"" . $aryResult[$i]["strproductunitname"] . "\"></td>";
        $aryHtml[] = "      <td style=\"display:none;\"><input type=\"hidden\" name=\"lngorderno\" value=\"" . $aryResult[$i]["lngorderno"] . "\"></td>";
        $aryHtml[] = "      <td style=\"display:none;\"><input type=\"hidden\" name=\"lngorderrevisionno\" value=\"" . $aryResult[$i]["lngorderrevisionno"] . "\"></td>";
        $aryHtml[] = "      <td style=\"display:none;\"><input type=\"hidden\" name=\"lngorderdetailno\" value=\"" . $aryResult[$i]["lngorderdetailno"] . "\"></td>";
        // $aryHtml[] = "      <td style=\"display:none;\"><input type=\"hidden\" name=\"lngorderdetailno\" value=\"" . $aryResult[$i]["lngpurchaseorderdetailno"] . "\"></td>";
        $aryHtml[] = "  </tr>";
    }

    $result["purchaseOrderDetail"] = implode("\n", $aryHtml);
    $result["purchaseOrderDetailNo"] = implode("\n", $aryNoHtml);

    return $result;
}

/**
 * 発注書マスタ更新
 *
 * @param   Array   $aryPurchaseOrder   発注書データ
 * @param   Object  $objDB              DBオブジェクト
 * @access  public
 *
 */
function fncUpdatePurchaseOrder($aryPurchaseOrder, $objDB, $objAuth)
{
    $lngcompanycode = fncGetMasterValue("m_company", "strcompanydisplaycode", "lngcompanycode", $aryPurchaseOrder["lngLocationCode"] . ":str", '', $objDB);
    $aryQuery[] = "INSERT INTO m_purchaseorder(";
    $aryQuery[] = "    lngpurchaseorderno,";
    $aryQuery[] = "    lngrevisionno,";
    $aryQuery[] = "    strordercode,";
    $aryQuery[] = "    lngcustomercode,";
    $aryQuery[] = "    strcustomername,";
    $aryQuery[] = "    strcustomercompanyaddreess,";
    $aryQuery[] = "    strcustomercompanytel,";
    $aryQuery[] = "    strcustomercompanyfax,";
    $aryQuery[] = "    strproductcode,";
    $aryQuery[] = "    strrevisecode,";
    $aryQuery[] = "    strproductname,";
    $aryQuery[] = "    strproductenglishname,";
    // $aryQuery[] = "    dtmexpirationdate,";
    $aryQuery[] = "    lngmonetaryunitcode,";
    $aryQuery[] = "    strmonetaryunitname,";
    $aryQuery[] = "    strmonetaryunitsign,";
    $aryQuery[] = "    lngmonetaryratecode,";
    $aryQuery[] = "    strmonetaryratename,";
    $aryQuery[] = "    lngpayconditioncode,";
    $aryQuery[] = "    strpayconditionname,";
    $aryQuery[] = "    lnggroupcode,";
    $aryQuery[] = "    strgroupname,";
    $aryQuery[] = "    txtsignaturefilename,";
    $aryQuery[] = "    lngusercode,";
    $aryQuery[] = "    strusername,";
    $aryQuery[] = "    lngdeliveryplacecode,";
    $aryQuery[] = "    strdeliveryplacename,";
    $aryQuery[] = "    curtotalprice,";
    $aryQuery[] = "    dtminsertdate,";
    $aryQuery[] = "    lnginsertusercode,";
    $aryQuery[] = "    strinsertusername,";
    $aryQuery[] = "    strnote,";
    $aryQuery[] = "    lngprintcount";
    $aryQuery[] = ") ";
    $aryQuery[] = "SELECT ";
    $aryQuery[] = "    lngpurchaseorderno,";
    $aryQuery[] = "    lngrevisionno + 1,";
    $aryQuery[] = "    strordercode,";
    $aryQuery[] = "    lngcustomercode,";
    $aryQuery[] = "    strcustomername,";
    $aryQuery[] = "    strcustomercompanyaddreess,";
    $aryQuery[] = "    strcustomercompanytel,";
    $aryQuery[] = "    strcustomercompanyfax,";
    $aryQuery[] = "    strproductcode,";
    $aryQuery[] = "    strrevisecode,";
    $aryQuery[] = "    strproductname,";
    $aryQuery[] = "    strproductenglishname,";
    // $aryQuery[] = "    '" . $aryPurchaseOrder["dtmExpirationDate"] . "',";
    $aryQuery[] = "    lngmonetaryunitcode,";
    $aryQuery[] = "    strmonetaryunitname,";
    $aryQuery[] = "    strmonetaryunitsign,";
    $aryQuery[] = "    lngmonetaryratecode,";
    $aryQuery[] = "    strmonetaryratename,";
    $aryQuery[] = $aryPurchaseOrder["lngPayConditionCode"] . ",";
    $aryQuery[] = "    '" . $aryPurchaseOrder["strPayConditionName"] . "',";
    $aryQuery[] = "    lnggroupcode,";
    $aryQuery[] = "    strgroupname,";
    $aryQuery[] = "    txtsignaturefilename,";
    $aryQuery[] = "    lngusercode,";
    $aryQuery[] = "    strusername,";
    $aryQuery[] = $lngcompanycode . ",";
    $aryQuery[] = "    '" . $aryPurchaseOrder["strLocationName"] . "',";
    $aryQuery[] = "    curtotalprice,";
    $aryQuery[] = "    '" . fncGetDateTimeString() . "',";
    $aryQuery[] = $objAuth->UserCode . ",";
    $aryQuery[] = "    '" . $objAuth->UserDisplayName . "',";
    $aryQuery[] = "    '" . $aryPurchaseOrder["strNote"] . "',";
    $aryQuery[] = 0 . " ";
    $aryQuery[] = "FROM m_purchaseorder po";
    $aryQuery[] = "WHERE lngpurchaseorderno = " . $aryPurchaseOrder["lngPurchaseOrderNo"];
    $aryQuery[] = "    AND   lngrevisionno = (SELECT MAX( po1.lngRevisionNo ) FROM m_purchaseorder po1 WHERE po1.lngpurchaseorderno = po.lngpurchaseorderno )";
    $strQuery = "";
    $strQuery = implode("\n", $aryQuery);

    if (!$lngResultID = $objDB->execute($strQuery)) {
        fncOutputError(9051, DEF_ERROR, "発注書マスタへの更新処理に失敗しました。", true, "", $objDB);
        return null;
    }
    $objDB->freeResult($lngResultID);

    return true;
}

/**
 * 発注書明細更新
 *
 * @param   Array   $aryPurchaseOrder   発注書データ
 * @param   Object  $objDB              DBオブジェクト
 * @access  public
 *
 */
function fncUpdatePurchaseOrderDetail($aryPurchaseOrder, $objDB)
{
    $lngpurchaseorderno = $aryPurchaseOrder["lngPurchaseOrderNo"];
    $lngrevisionno = intval($aryPurchaseOrder["lngRevisionNo"]) + 1;
    for ($i = 0; $i < count($aryPurchaseOrder["aryDetail"]); $i++) {
        $aryQuery = [];
        $strDeliveryMethodName = fncGetMasterValue("m_deliverymethod", "lngdeliverymethodcode", "strdeliverymethodname", $aryPurchaseOrder["aryDetail"][$i]["lngDeliveryMethodCode"] . ":str", '', $objDB);
        $aryQuery[] = "INSERT INTO t_purchaseorderdetail( ";
        $aryQuery[] = "    lngpurchaseorderno,";
        $aryQuery[] = "    lngpurchaseorderdetailno,";
        $aryQuery[] = "    lngrevisionno,";
        $aryQuery[] = "    lngorderno,";
        $aryQuery[] = "    lngorderdetailno,";
        $aryQuery[] = "    lngorderrevisionno,";
        $aryQuery[] = "    lngstocksubjectcode,";
        $aryQuery[] = "    lngstockitemcode,";
        $aryQuery[] = "    strstockitemname,";
        $aryQuery[] = "    lngdeliverymethodcode,";
        $aryQuery[] = "    strdeliverymethodname,";
        $aryQuery[] = "    curproductprice,";
        $aryQuery[] = "    lngproductquantity,";
        $aryQuery[] = "    lngproductunitcode,";
        $aryQuery[] = "    strproductunitname,";
        $aryQuery[] = "    cursubtotalprice,";
        $aryQuery[] = "    dtmdeliverydate,";
        $aryQuery[] = "    strnote,";
        $aryQuery[] = "    lngsortkey";
        $aryQuery[] = ")";
        $aryQuery[] = "VALUES(";
        $aryQuery[] = $lngpurchaseorderno . ",";                                                  // lngpurchaseorderno
        $aryQuery[] = (intval($i) + 1) . ",";                                                     // lngpurchaseorderdetailno
        $aryQuery[] = $lngrevisionno . ",";                                                       // lngrevisionno
        $aryQuery[] = $aryPurchaseOrder["aryDetail"][$i]["lngOrderNo"] . ",";                     // lngorderno
        $aryQuery[] = $aryPurchaseOrder["aryDetail"][$i]["lngOrderDetailNo"] . ",";               // lngorderdetailno
        $aryQuery[] = $aryPurchaseOrder["aryDetail"][$i]["lngOrderRevisionNo"] . ",";             // lngorderrevisionno

        $aryQuery[] = $aryPurchaseOrder["aryDetail"][$i]["lngStockSubjectCode"] . ",";            // lngstocksubjectcode;
        $aryQuery[] = $aryPurchaseOrder["aryDetail"][$i]["lngStockItemCode"] . ",";               // lngstockitemcode
        $aryQuery[] = "'" . fncGetMasterValue(
                                "m_stockitem", "lngstockitemcode", "strstockitemname",
                                $_POST["aryDetail"][$i]["lngStockItemCode"],
                                "lngstocksubjectcode = " . $_POST["aryDetail"][$i]["lngStockSubjectCode"],
                                $objDB) . "',";                                                   // strstockitemname
        $aryQuery[] = $aryPurchaseOrder["aryDetail"][$i]["lngDeliveryMethodCode"] . ",";          // lngdeliverymethodcode
        $aryQuery[] = "'" . $strDeliveryMethodName . "',";   // strdeliverymethodname
        $aryQuery[] = (real)$aryPurchaseOrder["aryDetail"][$i]["curProductPrice"] . ",";                // curproductprice
        $aryQuery[] = (int)$aryPurchaseOrder["aryDetail"][$i]["lngProductQuantity"] . ",";             // lngproductquantity
        $aryQuery[] = 1 . ",";                                                                    // lngproductunitcode
        $aryQuery[] = "'" . fncGetMasterValue(
                                "m_productunit", "lngproductunitcode", "strProductUnitName", 
                                1, "", $objDB ) . "',";                                           // strproductunitname
        $aryQuery[] = (real)$aryPurchaseOrder["aryDetail"][$i]["curSubtotalPrice"] . ",";               // cursubtotalprice
        $aryQuery[] = "'" . $aryPurchaseOrder["aryDetail"][$i]["dtmDeliveryDate"] . "',";         // dtmdeliverydate
        $aryQuery[] = "'" . $aryPurchaseOrder["aryDetail"][$i]["strDetailNote"] . "',";           // strnote
        $aryQuery[] = (intval($i) + 1);                                                           // lngsortkey

        $aryQuery[] = ")";
/*
        $aryQuery[] = "SELECT";
        $aryQuery[] = "    lngpurchaseorderno,";
        $aryQuery[] = "    lngpurchaseorderdetailno,";
        $aryQuery[] = "    lngrevisionno + 1,";
        $aryQuery[] = "    lngorderno,";
        $aryQuery[] = "    lngorderdetailno,";
        $aryQuery[] = "    lngorderrevisionno,";
        $aryQuery[] = "    lngstocksubjectcode,";
        $aryQuery[] = "    lngstockitemcode,";
        $aryQuery[] = "    strstockitemname,";
        $aryQuery[] = $aryPurchaseOrder["aryDetail"][$i]["lngDeliveryMethodCode"] . ",";
//        $aryQuery[] =      "'" . $aryPurchaseOrder["aryDetail"][$i]["strDeliveryMethodName"] . "',";
        $aryQuery[] = "'" . $strDeliveryMethodName . "',";
        $aryQuery[] = "    curproductprice,";
        $aryQuery[] = "    lngproductquantity,";
        $aryQuery[] = "    lngproductunitcode,";
        $aryQuery[] = "    strproductunitname,";
        $aryQuery[] = "    cursubtotalprice,";
        $aryQuery[] = "    dtmdeliverydate,";
        $aryQuery[] = "    strnote,";
        $aryQuery[] = $aryPurchaseOrder["aryDetail"][$i]["lngSortKey"] . " ";
        $aryQuery[] = "FROM t_purchaseorderdetail pod";
        $aryQuery[] = "WHERE lngpurchaseorderno = " . $aryPurchaseOrder["lngPurchaseOrderNo"];
        $aryQuery[] = "AND   lngpurchaseorderdetailno = " . $aryPurchaseOrder["aryDetail"][$i]["lngPurchaseOrderDetailNo"];
        $aryQuery[] = "AND   lngrevisionno = (SELECT MAX( pod1.lngRevisionNo ) FROM t_purchaseorderdetail pod1 WHERE pod1.lngPurchaseOrderNo = pod.lngPurchaseOrderNo and  pod1.lngpurchaseorderdetailno = pod.lngpurchaseorderdetailno)";
*/
        $strQuery = "";
        $strQuery = implode("\n", $aryQuery);
//fncDebug("kids2.log", $strQuery, __FILE__, __LINE__, "a");
        if (!$lngResultID = $objDB->execute($strQuery)) {
            fncOutputError(9051, DEF_ERROR, "発注書明細への更新処理に失敗しました。", true, "", $objDB);
            return null;
        }
        $objDB->freeResult($lngResultID);
    }

    return true;
}

/**
 * 更新後発注書データHTML変換
 *
 * @param   Array   $aryPurchaseOrder   発注書データ
 * @param   String  $strSessionID       セッションID
 * @access  public
 *
 */
function fncCreatePurchaseOrderUpdateHtml($aryPurchaseOrder, $strSessionID)
{
    $strUrl = "/list/result/frameset.php?strReportKeyCode=" . $aryPurchaseOrder[0]["lngpurchaseorderno"] . "&lngReportClassCode=2&strSessionID=" . $strSessionID;
    $aryHtml[] = "<p class=\"caption\">発注書NO " . $aryPurchaseOrder[0]["strordercode"] . "の修正が完了しました。</p>";
    $aryHtml[] = "<table class=\"ordercode\">";
    $aryHtml[] = "  <tr>";
    $aryHtml[] = "    <td class=\"orderbuttontd\" id=\"btnClose\"><img src=\"/img/type01/cmn/querybt/close_blown_off_ja_bt.gif\" alt=\"\" onclick=\"window.opener.opener.location.reload();window.opener.close();window.close();\"></td>";
    $aryHtml[] = "    <td class=\"orderbuttontd\" colspan=\"2\"><a href=\"#\" onclick=\"window.open('" . $strUrl . "', 'listWin', 'width=800,height=600,top=10,left=10,status=yes,scrollbars=yes,directories=no,menubar=yes,resizable=yes,location=no,toolbar=no')\"><img src=\"/img/type01/cmn/querybt/blownpreview_off_bt.gif\" alt=\"preview\"></a>";
    $aryHtml[] = "  </tr>";
    $aryHtml[] = "</table> ";
    $aryHtml[] = "<br>";
    $aryHtml[] = "<table class=\"orderdetail\">";
    $aryHtml[] = "  <tr>";
    $aryHtml[] = "    <th class=\"SegColumn\">製品コード</th>";
    $aryHtml[] = "    <th class=\"SegColumn\">製品名</th>";
    $aryHtml[] = "    <th class=\"SegColumn\">仕入先</th>";
    $aryHtml[] = "    <th class=\"SegColumn\">納品場所</th>";
    $aryHtml[] = "  </tr>";
    $aryHtml[] = "  <tr>";
    $aryHtml[] = "    <td class=\"Segs\">" . $aryPurchaseOrder[0]["strproductcode"] . "_" . $aryPurchaseOrder[0]["strrevisecode"] . "</td>";
    $aryHtml[] = "    <td class=\"Segs\">" . $aryPurchaseOrder[0]["strproductname"] . "</td>";
    $aryHtml[] = "    <td class=\"Segs\">" . sprintf("[%s] %s", $aryPurchaseOrder[0]["strcustomercode"], $aryPurchaseOrder[0]["strcustomername"]) . "</td>";
    $aryHtml[] = "    <td class=\"Segs\">" . sprintf("[%s] %s", $aryPurchaseOrder[0]["strdeliveryplacecode"], $aryPurchaseOrder[0]["strdeliveryplacename"]) . "</td>";
    $aryHtml[] = "  </tr>";
    $aryHtml[] = "</table>";
    $aryHtml[] = "<table class=\"orderdetail\">";
    $aryHtml[] = "  <tr>";
    $aryHtml[] = "    <th class=\"SegColumn\">No.</th>";
    $aryHtml[] = "    <th class=\"SegColumn\">仕入部品</th>";
    $aryHtml[] = "    <th class=\"SegColumn\">運搬方法</th>";
    $aryHtml[] = "    <th class=\"SegColumn\">単価</th>";
    $aryHtml[] = "    <th class=\"SegColumn\">数量</th>";
    $aryHtml[] = "    <th class=\"SegColumn\">税抜金額</th>";
    $aryHtml[] = "    <th class=\"SegColumn\">納期</th>";
    $aryHtml[] = "  </tr>";
    for ($i = 0; $i < count($aryPurchaseOrder); $i++) {
        $aryHtml[] = "  <tr>";
        $aryHtml[] = "    <td class=\"Segs\">" . $aryPurchaseOrder[$i]["lngsortkey"] . "</td>";
        $aryHtml[] = "    <td class=\"Segs\">" . sprintf("[%s] %s", $aryPurchaseOrder[$i]["lngstockitemcode"], $aryPurchaseOrder[$i]["strstockitemname"]) . "</td>";
        $aryHtml[] = "    <td class=\"Segs\">" . trim($aryPurchaseOrder[$i]["strdeliverymethodname"]) . "</td>";
        $aryHtml[] = "    <td class=\"Segs\">" . convertPrice($aryPurchaseOrder[$i]["lngmonetaryunitcode"], $aryPurchaseOrder[$i]["strmonetaryunitsign"], $aryPurchaseOrder[$i]["curproductprice"], 'unitprice') . "</td>";
        $aryHtml[] = "    <td class=\"Segs\">" . sprintf("%d %s", number_format($aryPurchaseOrder[$i]["lngproductquantity"]), $aryPurchaseOrder[$i]["strproductunitname"]) . "</td>";
        $aryHtml[] = "    <td class=\"Segs\">" . convertPrice($aryPurchaseOrder[$i]["lngmonetaryunitcode"], $aryPurchaseOrder[$i]["strmonetaryunitsign"], $aryPurchaseOrder[$i]["cursubtotalprice"], 'price') . "</td>";
        $aryHtml[] = "    <td class=\"Segs\">" . $aryPurchaseOrder[$i]["dtmdeliverydate"] . "</td>";
        $aryHtml[] = "  </tr>";
    }
    $aryHtml[] = "</table>";

    $strHtml = "";
    $strHtml = implode("\n", $aryHtml);

    return $strHtml;
}


function GetAdditionalOrderDetail($param, $objAuth, $objDB)
{
    foreach($param["aryDetail"] as $detail)
    {
        $lngorderno[] = $detail["lngOrderNo"];
    }
    $in = implode( ',', $lngorderno);
    $aryQuery[] = "select ";
    $aryQuery[] = "    mo.lngorderno";
    $aryQuery[] = "   ,mo.lngrevisionno";
    $aryQuery[] = "from m_order mo";
    $aryQuery[] = "inner join (";
    $aryQuery[] = "    select";
    $aryQuery[] = "        lngorderno,";
    $aryQuery[] = "        max(lngrevisionno) as lngrevisionnno";
    $aryQuery[] = "    from m_order";
    $aryQuery[] = "    where lngorderno not in (select lngorderno from  m_order where lngrevisionno < 0)";
    $aryQuery[] = "    group by lngorderno";
    $aryQuery[] = ") mo_rev";
    $aryQuery[] = "    on mo_rev.lngorderno = mo.lngorderno";
    $aryQuery[] = "    and mo_rev.lngrevisionnno = mo.lngrevisionno";
    $aryQuery[] = "inner join t_exclusivecontrol tex";
    $aryQuery[] = "    on tex.strexclusivekey1 = cast(mo.lngorderno as text)";
    $aryQuery[] = "where tex.strsessionid = '" . $objAuth->SessionID . "'";
    $aryQuery[] = "    and mo.lngorderno in (" . $in . ")";
    $strQuery = "";
    $strQuery = implode("\n", $aryQuery);

    list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);
    if (!$lngResultNum) {
        $aryResult = array();
        return $aryResult;
    }

    for ($i = 0; $i < $lngResultNum; $i++) {
        $aryResult[] = $objDB->fetchArray($lngResultID, $i);
    }

    $objDB->freeResult($lngResultID);

    return $aryResult;
}

// 削除対象発注書明細の取得
function GetRemovalOrderDetail($param, $objAuth, $objDB){
    foreach($param["aryDetail"] as $detail)
    {
        $lngorderno[] = $detail["lngOrderNo"];
    }
    $in = implode( ',', $lngorderno);
     
    $aryQuery[] = "select ";
    $aryQuery[] = "    lngorderno, lngorderrevisionno as lngrevisionno";
    $aryQuery[] = "    from t_purchaseorderdetail";
    $aryQuery[] = "    where lngpurchaseorderno = " . $param["lngPurchaseOrderNo"];
    $aryQuery[] = "    and lngrevisionno = " . $param["lngRevisionNo"];
    $aryQuery[] = "    and lngorderno not in (" . $in . ")";
    $strQuery = "";
    $strQuery = implode("\n", $aryQuery);

    list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);
    if (!$lngResultNum) {
        $aryResult = array();
        return $aryResult;
    }

    for ($i = 0; $i < $lngResultNum; $i++) {
        $aryResult[] = $objDB->fetchArray($lngResultID, $i);
    }

    $objDB->freeResult($lngResultID);

    return $aryResult;
}

// 削除対象発注書明細の発注ステータスチェック
function CanDeletePurchaseOrderDetail($lngorderno, $revisionno, $objDB){
    if(!CheckOrderStatus($lngorderno, $revisionno, 2, $objDB)){
        return "削除対象明細が納品済または締め済です。";
    }
    return null;
}

// 発注書追加対象発注マスタステータスチェック
function CanOrder($lngorderno, $revisionno, $objDB){
    if(!CheckOrderStatus($lngorderno, $revisionno, 1, $objDB)){
        return "削除対象明細が確定済または削除済です。";
    }
    return null;
}

// 削除対象発注書明細の発注ステータス更新
function CancelOrder($lngorderno, $revisionno, $objDB){
    return UpdateOrderStatus($lngorderno, $revisionno, 1, $objDB);
}

// 追加対象発注書明細の発注ステータス更新
function FixOrder($lngorderno, $revisionno, $objDB){
    return UpdateOrderStatus($lngorderno, $revisionno, 2, $objDB);
}
// 発注ステータス取得
function CheckOrderStatus($lngorderno, $revisionno, $status, $objDB){
    $aryQuery[] = "select ";
    $aryQuery[] = "    lngorderstatuscode";
    $aryQuery[] = "    from m_order";
    $aryQuery[] = "    where lngorderno = " . $lngorderno;
    $aryQuery[] = "    and lngrevisionno = " . $revisionno;
    $strQuery = "";
    $strQuery = implode("\n", $aryQuery);

    list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);
    if (!$lngResultNum) {
        return false;
    }

    $result = $objDB->fetchArray($lngResultID, 0);
    $objDB->freeResult($lngResultID);
    if(intval($result[0]) != $status){
        return false;
    }
    return true;
}

function UpdateOrderStatus($lngorderno, $revisionno, $status, $objDB){
    $aryQuery[] = "update m_order";
    $aryQuery[] = "set lngorderstatuscode = " . $status;
    $aryQuery[] = "where lngorderno = " . $lngorderno;
    $aryQuery[] = "    and lngrevisionno = " . $revisionno;
    $strQuery = "";
    $strQuery = implode("\n", $aryQuery);

    if (!$lngResultID = $objDB->execute($strQuery)) {
        return false;
    }
    $objDB->freeResult($lngResultID);
    return true;
}
