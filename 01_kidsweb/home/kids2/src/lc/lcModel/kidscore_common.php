<?php

/**
 * LC�������Ͽ��Ԥ�
 *
 * @param [object] $objDB
 * @param [array] $data
 * @return void ��Ͽ���
 */
function fncInsertLcInfo($objDB, $data)
{
    $sql = "
        insert into t_lcinfo(
            payfnameomit,
            opendate,
            portplace,
            pono,
            polineno,
            poreviseno,
            postate,
            payfcd,
            productcd,
            productname,
            productnumber,
            unitname,
            unitprice,
            moneyprice,
            shipstartdate,
            shipenddate,
            sumdate,
            poupdatedate,
            deliveryplace,
            currencyclass,
            lcnote,
            shipterm,
            validterm,
            bankname,
            bankreqdate,
            lcno,
            lcamopen,
            validmonth,
            usancesettlement,
            bldetail1date,
            bldetail1money,
            bldetail2date,
            bldetail2money,
            bldetail3date,
            bldetail3money,
            payfnameformal,
            productnamee,
            lcstate,
            bankcd,
            shipym
        )
        values ($1
        ,$2
        ,$3
        ,$4
        ,$5
        ,$6
        ,$7
        ,$8
        ,$9
        ,$10
        ,$11
        ,$12
        ,$13
        ,$14
        ,$15
        ,$16
        ,$17
        ,$18
        ,$19
        ,$20
        ,$21
        ,$22
        ,$23
        ,$24
        ,$25
        ,$26
        ,$27
        ,$28
        ,$29
        ,$30
        ,$31
        ,$32
        ,$33
        ,$34
        ,$35
        ,$36
        ,$37
        ,$38
        ,$39
        ,$40)";

    //�Х���ɤ�����
    $bind = array($data["payfnameomit"]
        , $data["opendate"]
        , $data["portplace"]
        , $data["pono"]
        , $data["polineno"]
        , $data["poreviseno"]
        , $data["postate"]
        , $data["payfcd"]
        , $data["productcd"]
        , $data["productname"]
        , $data["productnumber"]
        , $data["unitname"]
        , $data["unitprice"]
        , $data["moneyprice"]
        , $data["shipstartdate"]
        , $data["shipenddate"]
        , $data["sumdate"]
        , $data["poupdatedate"]
        , $data["deliveryplace"]
        , $data["currencyclass"]
        , $data["lcnote"]
        , $data["shipterm"]
        , $data["validterm"]
        , $data["bankname"]
        , $data["bankreqdate"]
        , $data["lcno"]
        , $data["lcamopen"]
        , $data["validmonth"]
        , $data["usancesettlement"]
        , $data["bldetail1date"]
        , $data["bldetail1money"]
        , $data["bldetail2date"]
        , $data["bldetail2money"]
        , $data["bldetail3date"]
        , $data["bldetail3money"]
        , $data["payfnameformal"]
        , $data["productnamee"]
        , $data["lcstate"]
        , $data["bankcd"]
        , $data["shipym"]);

    $result = pg_query_params($objDB->ConnectID, $sql, $bind);

    if (!$result) {
        echo "L/C������Ͽ���Ԥ��ޤ�����\n";
        exit("L/C������Ͽ���Ԥ��ޤ�����");
    } else {
        return pg_affected_rows($result);
    }
}

/**
 * LC����ꥹ�Ȥμ�����Ԥ�
 *
 * @param [object] $objDB
 * @param [array] $data
 * @return void
 */
function fncGetLcInfoData($objDB, $data)
{
    //���ܼ���
    $sql = "
        select
            payfnameomit,
            opendate,
            portplace,
            pono,
            polineno,
            poreviseno,
            postate,
            payfcd,
            productcd,
            productname,
            productnumber,
            unitname,
            unitprice,
            moneyprice,
            shipstartdate,
            shipenddate,
            sumdate,
            poupdatedate,
            deliveryplace,
            currencyclass,
            lcnote,
            shipterm,
            validterm,
            bankname,
            bankreqdate,
            lcno,
            lcamopen,
            validmonth,
            usancesettlement,
            bldetail1date,
            bldetail1money,
            bldetail2date,
            bldetail2money,
            bldetail3date,
            bldetail3money,
            payfnameformal,
            productnamee,
            lcstate,
            bankcd,
            shipym
        from
            t_lcinfo
        ";

    switch ($data["mode"]) {
        case "0":
            break;
        case "1":
            //��о��
            if ($data["from"] != "" && $data["to"] == "") {
                $sql .= "where opendate = '" . $data["from"] . "'";
            } else if ($data["from"] != "" && $data["to"] != "") {
                $sql .= " where opendate between '" . $data["from"] . "' and " . $data["to"] . "'";
            }
            if ($data["payfcd"] != "") {
                $sql .= " and payfcd = '" . $data["payfcd"] . "'";
            }
            if ($data["payfnameomit"] != "") {
                $sql .= " and payfnameomit = '" . $data["payfnameomit"] . "'";
            }
            if ($data["getDataModeFlg"] == 1) {
                $sql .= " and lcstate in (0,3,4,7,8) ";
            }
            break;
        case "2":
            //���ߥ�졼�Ⱦ��
            $sql .= "where opendate = '" . $data["to"] . "'";
            if ($data["getDataModeFlg"] == 1) {
                $sql .= " and lcstate in (0,3,4,7,8) ";
            }
            break;
        case "3":
            $sql .= " where lcstate in (0,3,4,7,8) ";
            break;
    }

    $sql .= " order by pono,poreviseno,polineno";

    $result = pg_query($objDB->ConnectID, $sql);

    if (!$result) {
        echo "L/C����������Ԥ��ޤ�����\n";
        exit;
    } else {
        return pg_fetch_all($result);
    }
}

/**
 * L/C����ǡ����κ����Ԥ�
 *
 * @param [object] $objDB
 * @return void ������
 */
function fncDeleteLcInfo($objDB)
{
    $sql = "
            delete from t_lcinfo
        ";

    $result = pg_query($objDB->ConnectID, $sql);

    if (!$result) {
        echo "L/C���������Ԥ��ޤ�����\n";
        exit;
    }

    return pg_affected_rows($result);
}

/**
 * LC����ñ�Τμ�����Ԥ�
 *
 * @param [object] $objDB
 * @param [array] $data
 * @return void LC����ñ��
 */
function fncGetLcInfoSingle($objDB, $data)
{

    //���ܼ���
    $sql = "
        select
            payfnameomit,
            opendate,
            portplace,
            pono,
            polineno,
            poreviseno,
            postate,
            payfcd,
            productcd,
            productname,
            productnumber,
            unitname,
            unitprice,
            moneyprice,
            shipstartdate,
            shipenddate,
            sumdate,
            poupdatedate,
            deliveryplace,
            currencyclass,
            lcnote,
            shipterm,
            validterm,
            bankname,
            bankreqdate,
            lcno,
            lcamopen,
            validmonth,
            usancesettlement,
            bldetail1date,
            bldetail1money,
            bldetail2date,
            bldetail2money,
            bldetail3date,
            bldetail3money,
            payfnameformal,
            productnamee,
            lcstate,
            bankcd,
            shipym
        from
            t_lcinfo
        where
            pono = $1 and
            poreviseno = $2 and
            polineno = $3
        ";

    //�Х���ɤ�����
    $bind = array($data["pono"], $data["poreviseno"], $data["polineno"]);

    $result = pg_query_params($objDB->ConnectID, $sql, $bind);

    if (!$result) {
        echo "L/C����ñ�μ������Ԥ��ޤ�����\n";
        exit;
    } else {
        return pg_fetch_object($result);
    }
}

/**
 * �����Ͼ���ꥹ�Ȥμ���
 *
 * @param [object] $objDB
 * @return array
 */
function fncGetPortplace($objDB)
{
    //�����������
    $sql = "
				SELECT
					DISTINCT portplace
				FROM
					t_lcinfo
            ";

    $result = pg_query($objDB->ConnectID, $sql);

    if (!$result) {
        echo "�����Ͼ���������Ԥ��ޤ�����\n";
        exit;
    } else {
        return pg_fetch_all($result);
    }
}

/**
 * �����Ͼ���(ALL)�ꥹ�Ȥμ���
 *
 * @param [object] $objDB
 * @return array
 */
function fncGetPortplaceAndAll($objDB)
{
    //�����������
    $sql = "
            select
                m_constant.value as portplace
            from
                m_constant
            where
                constantcode1 = '0010'
                and constantcode2 = '01'
            union
            select distinct
                portplace
            from
                t_lcinfo
            order by portplace
            ";

    $result = pg_query($objDB->ConnectID, $sql);

    if (!$result) {
        echo "�����Ͼ���(ALL)�������Ԥ��ޤ�����\n";
        exit;
    } else {
        return pg_fetch_all($result);
    }
}

/**
 * �̲߶�ʬ�ꥹ�Ȥμ���
 *
 * @param [object] $objDB
 * @return array
 */
function fncGetCurrencyClassList($objDB)
{
    //�����������
    $sql = "
				SELECT
					currencyclass
				FROM
					t_lcinfo
				WHERE
					lcstate = 0 or lcstate = 3 or lcstate = 4 or lcstate = 7 or lcstate = 8
				group BY currencyclass
            ";

    $result = pg_query($objDB->ConnectID, $sql);

    if (!$result) {
        echo "�̲߶�ʬ�ꥹ�ȼ������Ԥ��ޤ�����\n";
        exit;
    } else {
        return pg_fetch_all($result);
    }
}

/**
 * �̲߶�ʬ�ꥹ��(̤��ǧ�ޤ�)�μ���
 *
 * @param [object] $objDB
 * @return array
 */
function fncGetCurrencyClassListAll($objDB)
{
    //�����������
    $sql = "
				SELECT
					DISTINCT currencyclass
				FROM
					t_lcinfo
				WHERE
					lcstate = 0 or lcstate = 3 or lcstate = 4 or lcstate = 6 or lcstate = 7 or lcstate = 8
				ORDER BY currencyclass
            ";

    $result = pg_query($objDB->ConnectID, $sql);

    if (!$result) {
        echo "�̲߶�ʬ�ꥹ��(̤��ǧ�ޤ�)�������Ԥ��ޤ�����\n";
        exit;
    } else {
        return pg_fetch_all($result);
    }
}

/**
 * ���߻�����������
 *
 * @param [object] $objDB
 * @return array
 */
function fncGetCurDate($objDB)
{
    //�����������
    $sql = "
            SELECT CURRENT_TIMESTAMP;
        ";

    $result = pg_query($objDB->ConnectID, $sql);

    if (!$result) {
        echo "���߻���������Ԥ��ޤ�����\n";
        exit;
    } else {
        return pg_fetch_object($result)->current_timestamp;
    }
}

/**
 *ȯ��������������
 *
 * @param [object] $objDB
 * @param [string] $lcgetdate
 * @return ȯ����
 */
function fncGetPurchaseOrderCount($objDB, $lcgetdate)
{
    $sql = "
        select count(*)
        from m_purchaseorder
        where dtmInsertDate > $1
    ";
    //�Х���ɤ�����
    $bind = array($lcgetdate);

    $result = pg_query_params($objDB->ConnectID, $sql, $bind);

    if (!$result) {
        echo "ȯ������������Ԥ��ޤ�����\n";
        exit;
    } else {
        return pg_fetch_object($result)->count;
    }
}

// /**
//  *ȯ�������������
//  *
//  * @param [object] $objDB
//  * @param [string] $lcgetdate
//  * @return ȯ����
//  */
// function fncGetOrderCount($objDB, $lcgetdate)
// {
//     $sql = "
//         select count(*)
//         from m_order
//         where dtmInsertDate > $1
//     ";
//     //�Х���ɤ�����
//     $bind = array($lcgetdate);

//     $result = pg_query_params($objDB->ConnectID, $sql, $bind);

//     if (!$result) {
//         echo "ȯ�����������Ԥ��ޤ�����\n";
//         exit;
//     } else {
//         return pg_fetch_object($result)->count;
//     }
// }
/**
 * ȯ���ǡ������������
 *
 * @param [object] $objDB
 * @param [string] $date
 * @return ȯ��ǡ����ꥹ��
 */
function fncGetPurchaseOrderData($objDB, $date)
{
    $sql = "
        select
        lngpurchaseorderno
        , lngrevisionno
        , strordercode
        , lngcustomercode
        , strcustomername
        , strcustomercompanyaddreess
        , strcustomercompanytel
        , strcustomercompanyfax
        , strproductcode
        , strrevisecode
        , strproductname
        , strproductenglishname
        , dtmexpirationdate
        , lngmonetaryunitcode
        , strmonetaryunitname
        , strmonetaryunitsign
        , lngmonetaryratecode
        , strmonetaryratename
        , lngpayconditioncode
        , strpayconditionname
        , lnggroupcode
        , strgroupname
        , txtsignaturefilename
        , lngusercode
        , strusername
        , lngdeliveryplacecode
        , strdeliveryplacename
        , curtotalprice
        , dtminsertdate
        , lnginsertusercode
        , strinsertusername
        , strnote
        , lngprintcount 
    from
        m_purchaseorder 
    where
        dtminsertdate > to_timestamp($1,'YYYYMMDD HH24:MI:SS')
    order by
        strordercode
        , lngpurchaseorderno
    ";
    //�Х���ɤ�����
    $bind = array($date);

    $result = pg_query_params($objDB->ConnectID, $sql, $bind);

    if (!$result) {
        echo "ȯ���ǡ����������Ԥ��ޤ�����\n";
        exit;
    } else {
        return pg_fetch_all($result);
    }
}
// /**
//  * ȯ��ǡ������������
//  *
//  * @param [object] $objDB
//  * @param [string] $date
//  * @return ȯ��ǡ����ꥹ��
//  */
// function fncGetOrderData($objDB, $date)
// {
//     $sql = "
//         select
//             lngorderno
//             , lngcustomercompanycode
//             , dtmappropriationdate
//             , dtminsertdate
//             , lngdeliveryplacecode
//             , lngmonetaryunitcode
//             , strnote
//             , strrevisecode
//             , lngrevisionno
//             , strordercode
//             , lngmonetaryunitcode
//             , lngpayconditioncode
//             , lngorderstatuscode
//             , bytinvalidflag
//         from
//             m_order
//         where
//             dtminsertdate > $1
//             order by
//             strordercode
//             , lngorderno
//             , strrevisecode
//         ";
//     //�Х���ɤ�����
//     $bind = array($date);

//     $result = pg_query_params($objDB->ConnectID, $sql, $bind);

//     if (!$result) {
//         echo "ȯ��ǡ����������Ԥ��ޤ�����\n";
//         exit;
//     } else {
//         return pg_fetch_all($result);
//     }
// }

/**
 * ȯ������٥ǡ������������
 *
 * @param [object] $objDB
 * @param [integer] $lngpurchaseorderno
 * @param [integer] $lngrevisionno
 * @return ȯ�����٥ꥹ��
 */
function fncGetPurchaseOrderDetail($objDB, $lngpurchaseorderno, $lngrevisionno)
{
    $sql = "
        select
            tp.lngpurchaseorderno
            , tp.lngpurchaseorderdetailno
            , tp.lngrevisionno
            , tp.lngorderno
            , tp.lngorderdetailno
            , tp.lngorderrevisionno
            , tp.lngstocksubjectcode
            , tp.lngstockitemcode
            , tp.strstockitemname
            , tp.lngdeliverymethodcode
            , tp.strdeliverymethodname
            , tp.curproductprice
            , tp.lngproductquantity
            , tp.lngproductunitcode
            , tp.strproductunitname
            , tp.cursubtotalprice
            , tp.dtmdeliverydate
            , tp.strnote
            , tp.lngsortkey
        from
            t_purchaseorderdetail tp 
        where
            tp.lngpurchaseorderno = $1
            and tp.lngrevisionno = $2
        order by
            tp.lngorderdetailno
        ";
    //�Х���ɤ�����
    $bind = array($lngpurchaseorderno, $lngrevisionno);

    $result = pg_query_params($objDB->ConnectID, $sql, $bind);

    if (!$result) {
        echo "ȯ������٥ǡ����������Ԥ��ޤ�����\n";
        exit;
    } else {
        return pg_fetch_all($result);
    }

}

/**
 * ȯ�����٥ǡ������������
 *
 * @param [object] $objDB
 * @param [integer] $lngorderno
 * @param [integer] $lngrevisionno
 * @return ȯ�����٥ꥹ��
 */
function fncGetOrderDetail($objDB, $lngorderno, $lngrevisionno)
{
    $sql = "
        select
            lngorderno
            , lngsortkey
            , lngrevisionno
            , strproductcode
            , lngproductquantity
            , lngproductunitcode
            , cursubtotalprice
            , dtmdeliverydate
            , strnote
            , curproductprice
            , dtmdeliverydate
        from
            t_orderdetail
        where
            lngorderno = $1
            and lngrevisionno = $2
        order by
            lngorderdetailno
        ";
    //�Х���ɤ�����
    $bind = array($lngorderno, $lngrevisionno);

    $result = pg_query_params($objDB->ConnectID, $sql, $bind);

    if (!$result) {
        echo "ȯ�����٥ǡ����������Ԥ��ޤ�����\n";
        exit;
    } else {
        return pg_fetch_all($result);
    }

}

/**
 * Ǽ�ʾ��Ȳ����Ϥ��������
 *
 * @param [object] $objDB
 * @param [integer] $lngcompanycode
 * @return Ǽ�ʾ��Ȳ�����
 */
function fncGetCompanyNameAndCountryName($objDB, $lngcompanycode)
{
    $sql = "
        select
            m_company.strCompanyDisplayName
            , m_country.strcountryenglishname
            from
            m_company
            , m_country
            where
            m_company.lngcountrycode = m_country.lngcountrycode
            and m_company.lngcompanycode = $1
        ";
    //�Х���ɤ�����
    $bind = array($lngcompanycode);

    $result = pg_query_params($objDB->ConnectID, $sql, $bind);

    if (!$result) {
        echo "Ǽ�ʾ��̾�ΤȲ����ϼ������Ԥ��ޤ�����\n";
        exit;
    } else {
        return pg_fetch_object($result);
    }

}

// /**
//  * ȯ�����ɤˤ��ȯ��ǡ������������
//  *
//  * @param [object] $objDB
//  * @param [string] $date
//  * @return ȯ��ǡ����ꥹ��
//  */
// function fncGetOrderDataByOrderCode($objDB, $pono, $poreviseno)
// {
//     $sql = "
//         select
//             lngorderno
//             , lngrevisionno
//             , dtmappropriateiondate
//             , lngorderstatuscode
//             , bytinvalidflag
//         from
//             m_order
//         where
//             strordercode = $1
//             and strrevisecode = $2
//         order by
//             lngorderno desc
//         ";
//     //�Х���ɤ�����
//     $bind = array($pono, $poreviseno);

//     $result = pg_query_params($objDB->ConnectID, $sql, $bind);

//     if (!$result) {
//         echo "ȯ�����ɤˤ��ȯ��ǡ����������Ԥ��ޤ�����\n";
//         exit;
//     } else {
//         return pg_fetch_all($result);
//     }
// }

/**
 * �������ȯ���ǡ������������
 *
 * @param [object] $objDB
 * @return ȯ�����ɥꥹ��
 */
function fncGetDeletedPurchaseOrderData($objDB)
{
    $sql = "
        select
            strordercode
        from
            m_purchaseorder
        where
            lngrevisionno = -1
        ";

    $result = pg_query($objDB->ConnectID, $sql);

    if (!$result) {
        echo "�������ȯ���ǡ����������Ԥ��ޤ�����\n";
        exit;
    } else {
        return pg_fetch_all($result);
    }
}

// /**
//  * �������ȯ��ǡ������������
//  *
//  * @param [object] $objDB
//  * @return ȯ�����ɥꥹ��
//  */
// function fncGetDeletedOrderData($objDB)
// {
//     $sql = "
//         select
//             strordercode
//         from
//             m_order
//         where
//             lngrevisionno = -1
//             and bytinvalidflag = true
//         ";

//     $result = pg_query($objDB->ConnectID, $sql);

//     if (!$result) {
//         echo "�������ȯ��ǡ����������Ԥ��ޤ�����\n";
//         exit;
//     } else {
//         return pg_fetch_all($result);
//     }
// }

/**
 * �̲���PO�ֹ��̤ι�׶�ۼ���
 *
 * @param [object] $objDB
 * @param [string] $shipym
 * @param [string] $currencyclass
 * @return array
 */
function fncGetSumOfMoneypriceByPono($objDB, $shipym, $currencyclass)
{
    //�����������
    $sql = "
				SELECT
                    pono,
                    sum(moneyprice) as totalmoneyprice
				FROM
					t_lcinfo
                WHERE
                    shipym = $1
                    and bankname = ''
                    and currencyclass = $2
                    and (lcstate = 0 or lcstate = 3 or lcstate = 4 or lcstate = 7 or lcstate = 8)
                group by pono
                order by totalmoneyprice
            ";

    $bind = array($shipym, $currencyclass);
    $result = pg_query_params($objDB->ConnectID, $sql, $bind);

    if (!$result) {
        echo "�̲���PO�ֹ��̤ι�׶�ۼ����������Ԥ��ޤ�����\n";
        exit;
    } else {
        return pg_fetch_all($result);
    }
}

/**
 * �̲��̤ι�׶�ۼ���
 *
 * @param [object] $objDB
 * @param [string] $shipym
 * @param [string] $currencyclass
 * @return array
 */
function fncGetSumOfMoneyprice($objDB, $shipym, $currencyclass)
{
    //�����������
    $sql = "
				SELECT
                    sum(moneyprice) as totalmoneyprice
				FROM
					t_lcinfo
                WHERE
                    shipym = $1
                    and currencyclass = $2
                    and (lcstate = 0 or lcstate = 3 or lcstate = 4 or lcstate = 7 or lcstate = 8)
            ";

    $bind = array($shipym, $currencyclass);
    $result = pg_query_params($objDB->ConnectID, $sql, $bind);

    if (!$result) {
        echo "�̲��̤ι�׶�ۼ����������Ԥ��ޤ�����\n";
        exit;
    } else {
        return pg_fetch_object($result)->totalmoneyprice;
    }
}

/**
 * �̲��̶���̤ι�׶�ۼ���
 *
 * @param [object] $objDB
 * @param [string] $shipym
 * @param [string] $currencyclass
 * @return array
 */
function fncGetSumOfMoneypriceByBankname($objDB, $shipym, $currencyclass)
{
    //�����������
    $sql = "
				SELECT
                    sum(moneyprice) as totalmoneyprice,
                    bankname
				FROM
					t_lcinfo
                WHERE
                    shipym = $1
                    and currencyclass = $2
                    and (lcstate = 0 or lcstate = 3 or lcstate = 4 or lcstate = 7 or lcstate = 8)
                    and bankname <> ''
                group by bankname
            ";

    $bind = array($shipym, $currencyclass);
    $result = pg_query_params($objDB->ConnectID, $sql, $bind);

    if (!$result) {
        echo "�̲��̶���̤ι�׶�ۼ����������Ԥ��ޤ�����\n";
        exit;
    } else {
        return pg_fetch_all($result);
    }
}

/**
 * L/C�����ȯ�Զ�Ԥι���
 *
 * @param [object] $objDB
 * @param [string] $bankcd
 * @param [string] $bankname
 * @param [string] $currencyclass
 * @param [string] $pono
 * @return �������
 */
function fncUpdateBankname($objDB, $bankcd, $bankname, $currencyclass, $pono)
{
    //�����������
    $sql = "
                update t_lcinfo
                set bankcd = $1,
                    bankname = $2
                where
                    currencyclass = $3
                    and pono = $4
                    and (lcstate = 0 or lcstate = 3 or lcstate = 4 or lcstate = 7 or lcstate = 8)
            ";

    $bind = array($bankcd, $bankname, $currencyclass, $pono);
    $result = pg_query_params($objDB->ConnectID, $sql, $bind);

    if (!$result) {
        echo "L/C�����ȯ�Զ�Ԥι������Ԥ��ޤ�����\n";
        exit;
    } else {
        return pg_affected_rows($result);
    }

}

/**
 * L/C����ι���
 *
 * @param [object] $objDB
 * @param [array] $data
 * @return �������
 */
function fncUpdateLcinfo($objDB, $data)
{
    //�����������
    $sql = "
                update t_lcinfo
                set opendate = $1,
                    portplace = $2,
                    bankcd = $3,
                    bankname = $4,
                    bankreqdate = $5,
                    lcno = $6,
                    lcamopen = $7,
                    validmonth = $8,
                    lcstate = $9
                where
                    pono = $10
                    and poreviseno = $11
            ";
    $bind = array(($data["opendate"] == "") ? null : $data["opendate"],
        ($data["portplace"] == "") ? null : $data["portplace"],
        ($data["bankcd"] == "") ? null : $data["bankcd"],
        ($data["bankname"] == "") ? null : $data["bankname"],
        ($data["bankreqdate"] == "") ? null : date($data["bankreqdate"]),
        ($data["lcno"] == "") ? null : $data["lcno"],
        ($data["lcamopen"] == "") ? null : date($data["lcamopen"]),
        ($data["validmonth"] == "") ? null : str_replace("/", "", $data["validmonth"]),
        $data["lcstate"], $data["pono"], $data["poreviseno"],
    );

    $result = pg_query_params($objDB->ConnectID, $sql, $bind);

    if (!$result) {
        echo "L/C����ι������Ԥ��ޤ�����\n";
        exit;
    } else {
        return pg_affected_rows($result);
    }

}

/**
 * L/C����Υ��ơ�������T/T����ؤι���
 *
 * @param [object] $objDB
 * @param [array] $data
 * @return �������
 */
function fncUpdateLcinfoToAmandCancel($objDB, $data)
{
    //�����������
    $sql = "
                update t_lcinfo
                set opendate = $1,
                    portplace = $2,
                    bankreqdate = $3,
                    lcamopen = $4,
                    validmonth = $5,
                    lcstate = 8
                where
                    pono = $6
                    and poreviseno = $7
            ";

    $bind = array(($data["opendate"] == "") ? null : $data["opendate"],
        ($data["portplace"] == "") ? null : $data["portplace"],
        ($data["bankreqdate"] == "") ? null : $data["bankreqdate"],
        ($data["lcamopen"] == "") ? null : $data["lcamopen"],
        ($data["validmonth"] == "") ? null : $data["validmonth"],
        $data["pono"], $data["poreviseno"]);

    $result = pg_query_params($objDB->ConnectID, $sql, $bind);

    if (!$result) {
        echo "L/C����Υ��ơ������򥢥��ɷٹ����ؤι������Ԥ��ޤ�����\n";
        exit;
    } else {
        return pg_affected_rows($result);
    }

}

/**
 * L/C����ξ��֤ι���
 *
 * @param [object] $objDB
 * @param [array] $data
 * @return �������
 */
function fncUpdateLcState($objDB, $data)
{
    //�����������
    $sql = "
                update t_lcinfo
                set lcstate = $1
                where pono = $2
                and poreviseno = $3
            ";

    $bind = array($data["lcstate"], $data["pono"], $data["poreviseno"]);

    $result = pg_query_params($objDB->ConnectID, $sql, $bind);

    if (!$result) {
        echo "L/C����ξ��ֹ������Ԥ��ޤ�����\n";
        exit;
    } else {
        return pg_affected_rows($result);
    }
}

/**
 * ��ʧ�������������
 *
 * @param [type] $objDB
 * @return void
 */
function fncGetPayfInfo($objDB)
{
    //�����������
    $sql = "
				select
					*
				from
					m_payfinfo
				order by payfcd
            ";
    //�Х���ɤ�����
    $bind = array();

    $result = pg_query_params($objDB->ConnectID, $sql, $bind);

    if (!$result) {
        echo "��ʧ�����������Ԥ��ޤ�����\n";
        exit;
    } else {
        return pg_fetch_all($result);
    }
}

/**
 * ��Ծ�����������
 *
 * @param [type] $objDB
 * @return void
 */
function fncGetBankInfo($objDB)
{
    //�����������
    $sql = "
				select
					*
				from
					m_bank
				order by bankcd
            ";
    //�Х���ɤ�����
    $bind = array();
    $result = pg_query_params($objDB->ConnectID, $sql, $bind);

    if (!$result) {
        echo "��Ծ���������Ԥ��ޤ�����\n";
        exit;
    } else {
        return pg_fetch_all($result);
    }
}

/**
 * ��Ծ���(ALL)���������
 *
 * @param [type] $objDB
 * @return void
 */
function fncGetBankAndAll($objDB)
{
    //�����������
    $sql = "
            select
                '0000' as bankcd
                , m_constant.value as bankomitname
            from
                m_constant
            where
                constantcode1 = '0010'
                and constantcode2 = '01'
            union
            select
                bankcd
                , bankomitname
            from
                m_bank
            order by bankcd
            ";

    $result = pg_query($objDB->ConnectID, $sql);

    if (!$result) {
        echo "��Ծ���(ALL)�������Ԥ��ޤ�����\n";
        exit;
    } else {
        return pg_fetch_all($result);
    }
}
/**
 * ��Ծ���κ����Ԥ�
 *
 * @param [object] $objDB
 * @return void ������
 */
function fncDeleteBank($objDB)
{
    $sql = "
        delete from m_bank
        ";

    $result = pg_query($objDB->ConnectID, $sql);

    if (!$result) {
        echo "��ԥޥ������������Ԥ��ޤ�����\n";
        exit;
    }

    return pg_affected_rows($result);
}

/**
 * ��ʧ�����κ����Ԥ�
 *
 * @param [object] $objDB
 * @return void ������
 */
function fncDeletePayfinfo($objDB)
{
    $sql = "
        delete from m_payfinfo
        ";

    $result = pg_query($objDB->ConnectID, $sql);

    if (!$result) {
        echo "��ʧ��ޥ������������Ԥ��ޤ�����\n";
        exit;
    }

    return pg_affected_rows($result);
}

/**
 * ���ո�����κ����Ԥ�
 *
 * @param [object] $objDB
 * @return void ������
 */
function fncDeleteSendinfo($objDB)
{
    $sql = "
        delete from m_sendinfo
        ";

    $result = pg_query($objDB->ConnectID, $sql);

    if (!$result) {
        echo "���ո��ޥ������������Ԥ��ޤ�����\n";
        exit;
    }

    return pg_affected_rows($result);
}
/**
 * ��Ծ������Ͽ��Ԥ�
 *
 * @param [object] $objDB
 * @param [array] $data
 * @return void ��Ͽ���
 */
function fncInsertBank($objDB, $data)
{
    $sql = "
        insert into m_bank
        values ($1
        ,$2
        ,$3
        ,$4
        ,$5)";

    //�Х���ɤ�����
    $bind = array($data["bankcd"]
        , $data["bankomitname"]
        , $data["bankformalname"]
        , $data["bankdivrate"]
        , $data["invalidflag"]);

    $result = pg_query_params($objDB->ConnectID, $sql, $bind);

    if (!$result) {
        echo "��ԥޥ���������Ͽ���Ԥ��ޤ�����\n";
        exit;
    } else {
        return pg_affected_rows($result);
    }
}

/**
 * ��ʧ��ޥ����������Ͽ��Ԥ�
 *
 * @param [object] $objDB
 * @param [array] $data
 * @return void ��Ͽ���
 */
function fncInsertPayf($objDB, $data)
{
    if ($data["del_flg"] != true) {
        $sql = "
            insert into m_payfinfo
            values ($1
            ,$2
            ,$3
            ,$4
            ,$5
            ,$6)";

        //�Х���ɤ�����
        $bind = array($data["payfcd"]
            , $data["payfomitname"]
            , $data["payfformalname"]
            , $data["payfsendname"]
            , $data["payfsendfax"]
            , $data["invalidflag"]);

        $result = pg_query_params($objDB->ConnectID, $sql, $bind);

        if (!$result) {
            echo "��ʧ��ޥ���������Ͽ���Ԥ��ޤ�����\n";
            exit;
        } else {
            return pg_affected_rows($result);
        }
    }
}
/**
 * ���ո��������Ͽ��Ԥ�
 *
 * @param [object] $objDB
 * @param [array] $data
 * @return void ��Ͽ���
 */
function fncInsertSendInfo($objDB, $data)
{
    $sql = "
            insert into m_sendinfo
            values ($1
            ,$2
            ,$3
            ,$4
            ,$5
            )";

    //�Х���ɤ�����
    $bind = array($data["sendno"]
        , $data["sendfromname"]
        , $data["sendfromfax"]
        , $data["sendcarenote1"]
        , $data["sendcarenote2"],
    );
    $result = pg_query_params($objDB->ConnectID, $sql, $bind);

    if (!$result) {
        echo "���ո��ޥ���������Ͽ���Ԥ��ޤ�����\n";
        exit;
    } else {
        return pg_affected_rows($result);
    }
}

/**
 * ��ԥޥ������ͭ���ǡ������������
 *
 * @return array
 */
function fncGetValidBankInfo($objDB)
{
    //�����������
    $sql = "
				select
                    bankomitname,
                    bankdivrate,
                    bankcd
				from
                    m_bank
                where
                    invalidflag = false
                order by bankdivrate desc
            ";

    $result = pg_query($objDB->ConnectID, $sql);

    if (!$result) {
        echo "��ԥޥ������ͭ���ǡ����������Ԥ��ޤ�����\n";
        exit;
    } else {
        return pg_fetch_all($result);
    }

}
