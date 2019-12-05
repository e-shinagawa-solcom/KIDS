<?php

/**
 * ��ʧ�����̤ι�׶�ۼ���
 *
 * @param [object] $objDB
 * @param [string] $shipym
 * @param [string] $currencyclass
 * @param [string] $type
 * @return array
 */
function fncGetSumOfMoneypriceByPayfAndBank($objDB, $data, $type)
{
    //�����������
    if ($type == 1) {
        $where = "opendate = $1";
    } else if ($type == 2) {
        $where = "shipym = $1";
    }
    $sql = "
			SELECT
                payfcd,
                bankcd,
                sum(moneyprice) as totalmoneyprice,
                payfnameformal
			FROM
                t_lcinfo
            WHERE
            " . $where .
        "   and currencyclass = $2
                and payfnameomit is not null
                and (lcstate = 0 or lcstate = 3 or lcstate = 4 or lcstate = 7 or lcstate = 8)
                group by payfcd, bankcd, payfnameformal
                order by payfcd, bankcd, payfnameformal
            ";
    // ������ؤ������ͤ����
    $bind = array($data["opendate"], $data["currencyclass"]);

    // ������μ¹�
    $result = pg_query_params($objDB->ConnectID, $sql, $bind);

    if (!$result) {
        echo "��ʧ�����̤ι�׶�ۼ������Ԥ��ޤ�����\n";
        exit;
    } else {
        return pg_fetch_all($result);
    }
}

/**
 * ��ʧ����̤ι�׶�ۼ���
 *
 * @param [object] $objDB
 * @param [string] $shipym
 * @param [string] $currencyclass
 * @param [string] $type
 * @return array
 */
function fncGetSumOfMoneypriceByPayfAndOpenDate($objDB, $data)
{
        $sql = "
			SELECT
                payfcd,
                opendate,
                sum(moneyprice) as totalmoneyprice,
                payfnameformal
			FROM
                t_lcinfo
            WHERE
            opendate between $1 and $2
            and currencyclass = $3
            and payfnameomit is not null
            and (lcstate = 0 or lcstate = 3 or lcstate = 4 or lcstate = 7 or lcstate = 8)
            group by payfcd, opendate, bankcd, payfnameformal
            order by payfcd, opendate, bankcd, payfnameformal
            ";

    // ������ؤ������ͤ����
    $bind = array($data["opendatefrom"], $data["opendateto"], $data["currencyclass"]);
    // ������μ¹�
    $result = pg_query_params($objDB->ConnectID, $sql, $bind);

    if (!$result) {
        echo "��ʧ�����̤ι�׶�ۼ������Ԥ��ޤ�����\n";
        exit;
    } else {
        return pg_fetch_all($result);
    }
}

/**
 * ��ʧ����̤ι�׶�ۼ���
 *
 * @param [object] $objDB
 * @param [string] $shipym
 * @param [string] $currencyclass
 * @param [string] $type
 * @return array
 */
function fncGetSumOfMoneypriceByPayfAndShipDate($objDB, $data)
{
    $sql = "
			SELECT
                payfcd,
                shipym,
                sum(moneyprice) as totalmoneyprice,
                payfnameformal
			FROM
                t_lcinfo
            WHERE
            shipym between $1 and $2
            and currencyclass = $3
            and payfnameomit is not null
            and (lcstate = 0 or lcstate = 3 or lcstate = 4 or lcstate = 7 or lcstate = 8)
            group by payfcd, shipym, bankcd, payfnameformal
            order by payfcd, shipym, bankcd, payfnameformal
            ";

    // ������ؤ������ͤ����
    $bind = array($data["opendatefrom"], $data["opendateto"], $data["currencyclass"]);
    // ������μ¹�
    $result = pg_query_params($objDB->ConnectID, $sql, $bind);

    if (!$result) {
        echo "��ʧ�����̤ι�׶�ۼ������Ԥ��ޤ�����\n";
        exit;
    } else {
        return pg_fetch_all($result);
    }
}

/**
 * ��ʧ���̤ι�׶�ۼ���
 *
 * @param [object] $objDB
 * @param [string] $shipym
 * @param [string] $currencyclass
 * @return array
 */
function fncGetSumOfMoneypriceByPayf($objDB, $data, $type)
{

    //�����������
    if ($type == 1) {
        $where = "opendate = $1 and currencyclass = $2";
        $bind = array($data["opendate"], $data["currencyclass"]);
    } else if ($type == 2) {
        $where = "shipym = $1 and currencyclass = $2";
        $bind = array($data["opendate"], $data["currencyclass"]);
    } else if ($type == 3) {
        $where = "opendate between $1 and $2 and currencyclass = $3";
        $bind = array($data["opendatefrom"], $data["opendateto"], $data["currencyclass"]);
    } else if ($type == 4) {
        $where = "shipym between $1 and $2 and currencyclass = $3";
        $bind = array($data["opendatefrom"], $data["opendateto"], $data["currencyclass"]);
    }
    $sql = "
				SELECT
                    payfcd,
                    sum(moneyprice) as totalmoneyprice,
                    payfnameformal
				FROM
					t_lcinfo
                WHERE
                " . $where .
        "   and payfnameomit is not null
            and (lcstate = 0 or lcstate = 3 or lcstate = 4 or lcstate = 7 or lcstate = 8)
                group by payfcd, payfnameformal
                order by payfcd, payfnameformal
            ";
    // ������μ¹�
    $result = pg_query_params($objDB->ConnectID, $sql, $bind);

    if (!$result) {
        echo "��ʧ���̤ι�׶�ۼ������Ԥ��ޤ�����\n";
        exit;
    } else {
        return pg_fetch_all($result);
    }
}

/**
 * ĢɼBeneBk�̹�פκ����Ԥ�
 *
 * @param [object] $objDB
 * @return void ������
 */
function fncDeleteReportByBenebktotal($objDB)
{
    $sql = "
        delete from t_reportbybenebktotal
        ";

    $result = pg_query($objDB->ConnectID, $sql);

    if (!$result) {
        echo "ĢɼBeneBk�̹�׺�����Ԥ��ޤ�����\n";
        exit;
    }

    return pg_affected_rows($result);
}

/**
 * ĢɼBene���̽��פκ����Ԥ�
 *
 * @param [object] $objDB
 * @return void ������
 */
function fncDeleteReportByBeneMonthCal($objDB)
{
    $sql = "
        delete from t_reportbybenemonthcalculation
        ";

    $result = pg_query($objDB->ConnectID, $sql);

    if (!$result) {
        echo "ĢɼBene���̽��׺�����Ԥ��ޤ�����\n";
        exit;
    }

    return pg_affected_rows($result);
}

/**
 * ĢɼLC�����٤κ����Ԥ�
 *
 * @param [object] $objDB
 * @return void ������
 */
function fncDeleteReportByLcDetail($objDB)
{
    $sql = "
        delete from t_reportbylcdetail
        ";

    $result = pg_query($objDB->ConnectID, $sql);

    if (!$result) {
        echo "ĢɼLC�����ٺ���˼��Ԥ��ޤ�����\n";
        exit;
    }

    return pg_affected_rows($result);
}

/**
 * ĢɼLC�̹�פκ����Ԥ�
 *
 * @param [object] $objDB
 * @return void ������
 */
function fncDeleteReportByLcTotal($objDB)
{
    $sql = "
        delete from t_reportbylctotal
        ";

    $result = pg_query($objDB->ConnectID, $sql);

    if (!$result) {
        echo "ĢɼLC�̹�פκ���˼��Ԥ��ޤ�����\n";
        exit;
    }

    return pg_affected_rows($result);
}

/**
 * Ģɼ͢�����Ѿ�ȯ�Ծ���κ����Ԥ�
 *
 * @param [object] $objDB
 * @return void ������
 */
function fncDeleteReportImpLcOrderInfo($objDB)
{
    $sql = "
        delete from t_reportimportlcorderinfo
        ";

    $result = pg_query($objDB->ConnectID, $sql);

    if (!$result) {
        echo "Ģɼ͢�����Ѿ�ȯ�Ծ���κ���˼��Ԥ��ޤ�����\n";
        exit;
    }

    return pg_affected_rows($result);
}

/**
 * Ģɼ̤��ѳۤκ����Ԥ�
 *
 * @param [object] $objDB
 * @return void ������
 */
function fncDeleteReportUnSettedPrice($objDB)
{
    $sql = "
        delete from t_reportunsettedprice
        ";

    $result = pg_query($objDB->ConnectID, $sql);

    if (!$result) {
        echo "Ģɼ̤��ѳۤκ���˼��Ԥ��ޤ�����\n";
        exit;
    }

    return pg_affected_rows($result);
}

/**
 * Ģɼ̤��ѳ�̤��ǧ�κ����Ԥ�
 *
 * @param [object] $objDB
 * @return void ������
 */
function fncDeleteReportUnSettedPriceUnapproval($objDB)
{
    $sql = "
        delete from t_reportunsettedpriceunapproval
        ";

    $result = pg_query($objDB->ConnectID, $sql);

    if (!$result) {
        echo "Ģɼ̤��ѳ�̤��ǧ�κ���˼��Ԥ��ޤ�����\n";
        exit;
    }

    return pg_affected_rows($result);
}

/**
 * Ģɼ̤��ѳ�̤��ǧ����Ͽ��Ԥ�
 *
 * @param [object] $objDB
 * @param [array] $data
 * @return void ��Ͽ���
 */
function fncInsertReportUnSettedPriceUnapproval($objDB, $data)
{
    $sql = "
        insert into t_reportunsettedpriceunapproval
        values ($1
        ,$2)";

    //�Х���ɤ�����
    $bind = array($data["payeeformalname"]
        , $data["unsettledprice"]);

    $result = pg_query_params($objDB->ConnectID, $sql, $bind);

    if (!$result) {
        echo "Ģɼ̤��ѳ�̤��ǧ��Ͽ���Ԥ��ޤ�����\n";
        exit;
    } else {
        return pg_affected_rows($result);
    }
}

/**
 * Ģɼ̤��ѳۤ���Ͽ��Ԥ�
 *
 * @param [object] $objDB
 * @param [array] $data
 * @return void ��Ͽ���
 */
function fncInsertReportUnSettedPrice($objDB, $data)
{
    $sql = "
        insert into t_reportunsettedprice
        values ($1
                ,$2
                ,$3
                ,$4
                ,$5
                ,$6)";

    //�Х���ɤ�����
    $bind = array($data["managementno"]
        , $data["bankname"]
        , $data["payeeformalname"]
        , $data["shipstartdate"]
        , $data["lcno"]
        , $data["usancesettlement"]);

    $result = pg_query_params($objDB->ConnectID, $sql, $bind);

    if (!$result) {
        echo "Ģɼ̤��ѳ���Ͽ���Ԥ��ޤ�����\n";
        exit;
    } else {
        return pg_affected_rows($result);
    }
}

/**
 * Ģɼ͢�����Ѿ�ȯ�Ծ������Ͽ��Ԥ�
 *
 * @param [object] $objDB
 * @return void ������
 */
function fncInsertReportImpLcOrderInfo($objDB, $data)
{
    $sql = "
        insert into t_reportimportlcorderinfo
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
                ,$18)";

    //�Х���ɤ�����
    $bind = array($data["bankreqdate"]
        , $data["pono"]
        , $data["productcd"]
        , $data["productrevisecd"]
        , $data["productname"]
        , $data["productnumber"]
        , $data["unitname"]
        , $data["unitprice"]
        , $data["moneyprice"]
        , $data["shipstartdate"]
        , $data["shipenddate"]
        , $data["shipterm"]
        , $data["validterm"]
        , $data["lcno"]
        , $data["reckoninginitialdate"]
        , $data["portplace"]
        , $data["bankname"]
        , $data["reserve1"]);

    $result = pg_query_params($objDB->ConnectID, $sql, $bind);

    if (!$result) {
        echo "Ģɼ͢�����Ѿ�ȯ�Ծ������Ͽ���Ԥ��ޤ�����\n";
        exit;
    } else {
        return pg_affected_rows($result);
    }
}

/**
 * ĢɼLC�̹�פ���Ͽ��Ԥ�
 *
 * @param [object] $objDB
 * @return void ������
 */
function fncInsertReportByLcTotal($objDB, $data)
{
    $sql = "
        insert into t_reportbylctotal
        values ($1
                ,$2
                ,$3
                ,$4
                ,$5
                ,$6
                ,$7
                ,$8)";

    //�Х���ɤ�����
    $bind = array($data["lcno"]
        , $data["factoryname"]
        , $data["price"]
        , $data["shipterm"]
        , $data["validterm"]
        , $data["bankname"]
        , $data["bankreqdate"]
        , $data["lcamopen"]);

    $result = pg_query_params($objDB->ConnectID, $sql, $bind);

    if (!$result) {
        echo "ĢɼLC�̹�פ���Ͽ���Ԥ��ޤ�����\n";
        exit;
    } else {
        return pg_affected_rows($result);
    }
}

/**
 * ĢɼLC�����٤���Ͽ��Ԥ�
 *
 * @param [object] $objDB
 * @return void ������
 */
function fncInsertReportByLcDetail($objDB, $data)
{
    $sql = "
        insert into t_reportbylcdetail
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
                ,$18)";

    //�Х���ɤ�����
    $bind = array($data["lcno"]
        , $data["pono"]
        , $data["factoryname"]
        , $data["productcd"]
        , $data["productrevisecd"]
        , $data["productname"]
        , $data["productnumber"]
        , $data["unitname"]
        , $data["unitprice"]
        , $data["moneyprice"]
        , $data["shipstartdate"]
        , $data["shipenddate"]
        , $data["portplace"]
        , $data["shipterm"]
        , $data["validterm"]
        , $data["bankname"]
        , $data["bankreqdate"]
        , $data["lcamopen"]);

    $result = pg_query_params($objDB->ConnectID, $sql, $bind);

    if (!$result) {
        echo "ĢɼLC�����٤���Ͽ���Ԥ��ޤ�����\n";
        exit;
    } else {
        return pg_affected_rows($result);
    }
}

/**
 * ĢɼBene���̽��פ���Ͽ��Ԥ�
 *
 * @param [object] $objDB
 * @return void ������
 */
function fncInsertReportByBeneMonthCal($objDB, $data)
{
    $sql = "
        insert into t_reportbybenemonthcalculation
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
                ,$13)";

    //�Х���ɤ�����
    $bind = array($data["beneficiary"]
        , $data["date1"]
        , $data["date2"]
        , $data["date3"]
        , $data["date4"]
        , $data["date5"]
        , $data["date6"]
        , $data["date7"]
        , $data["date8"]
        , $data["date9"]
        , $data["date10"]
        , $data["date11"]
        , $data["total"]);

    $result = pg_query_params($objDB->ConnectID, $sql, $bind);

    if (!$result) {
        echo "ĢɼBene���̽��פ���Ͽ���Ԥ��ޤ�����\n";
        exit;
    } else {
        return pg_affected_rows($result);
    }
}

/**
 * ĢɼBeneBk�̹�פ���Ͽ��Ԥ�
 *
 * @param [object] $objDB
 * @return void ������
 */
function fncInsertReportByBenebktotal($objDB, $data)
{
    $sql = "
        insert into t_reportbybenebktotal
        values ($1
                ,$2
                ,$3
                ,$4
                ,$5
                ,$6)";

    //�Х���ɤ�����
    $bind = array($data["beneficiary"]
        , $data["bank1"]
        , $data["bank2"]
        , $data["bank3"]
        , $data["bank4"]
        , $data["total"]);

    $result = pg_query_params($objDB->ConnectID, $sql, $bind);

    if (!$result) {
        echo "ĢɼBeneBk�̹�פ���Ͽ���Ԥ��ޤ�����\n";
        exit;
    } else {
        return pg_affected_rows($result);
    }
}

/**
 * ĢɼBeneBk�̹�פ��������
 *
 * @return array
 */
function fncGetReportByBenebktotal($objDB)
{
    //�����������
    $sql = "
				select
                    substr(beneficiary, 0, 50) as beneficiary,
                    bank1,
                    bank2,
                    bank3,
                    bank4,
                    total
				from
                    t_reportbybenebktotal
            ";

    $result = pg_query($objDB->ConnectID, $sql);

    if (!$result) {
        echo "ĢɼBeneBk�̹�פμ������Ԥ��ޤ�����\n";
        exit;
    } else {
        return pg_fetch_all($result);
    }
}

/**
 * ĢɼBene���̽��פ��������
 *
 * @return array
 */
function fncGetReportByBeneMonthCal($objDB)
{
    //�����������
    $sql = "
				select
                    substr(beneficiary, 0, 31) as beneficiary,
                    date1,
                    date2,
                    date3,
                    date4,
                    date5,
                    date6,
                    date7,
                    date8,
                    date9,
                    date10,
                    date11,
                    total
				from
                    t_reportbybenemonthcalculation
            ";

    $result = pg_query($objDB->ConnectID, $sql);

    if (!$result) {
        echo "ĢɼBene���̽��פμ������Ԥ��ޤ�����\n";
        exit;
    } else {
        return pg_fetch_all($result);
    }
}

/**
 * ĢɼLC�����٤��������
 *
 * @return array
 */
function fncGetReportByLcDetail($objDB)
{
    //�����������
    $sql = "
				select
                    lcno,
                    pono,
                    substr(factoryname, 0 ,24) as factoryname,
                    productcd,
                    productrevisecd,
                    substr(productname, 0 ,22) as productname,
                    productnumber,
                    unitname,
                    unitprice,
                    moneyprice,
                    to_char(shipstartdate, 'MM/DD') as shipstartdate,
                    to_char(shipenddate, 'MM/DD') as shipenddate,
                    portplace,
                    to_char(shipterm, 'DD-Mon') as shipterm,
                    to_char(validterm, 'DD-Mon') as validterm,
                    bankname,
                    to_char(bankreqdate, 'MM/DD') as bankreqdate,
                    to_char(lcamopen, 'MM/DD') as lcamopen
				from
                    t_reportbylcdetail
            ";

    $result = pg_query($objDB->ConnectID, $sql);

    if (!$result) {
        echo "ĢɼLC�����٤μ������Ԥ��ޤ�����\n";
        exit;
    } else {
        return pg_fetch_all($result);
    }
}

/**
 * ĢɼLC�̹�פ��������
 *
 * @return array
 */
function fncGetReportByLcTotal($objDB)
{
    //�����������
    $sql = "
				select
                    lcno,
                    substr(factoryname, 0 ,50) as factoryname,
                    price,
                    to_char(shipterm, 'DD-Mon') as shipterm,
                    to_char(validterm, 'DD-Mon') as validterm,
                    bankname,
                    to_char(bankreqdate, 'MM��DD��') as bankreqdate,
                    to_char(lcamopen, 'MM��DD��') as lcamopen
				from
                    t_reportbylctotal
            ";

    $result = pg_query($objDB->ConnectID, $sql);

    if (!$result) {
        echo "ĢɼLC�̹�פμ������Ԥ��ޤ�����\n";
        exit;
    } else {
        return pg_fetch_all($result);
    }
}

/**
 * Ģɼ͢�����Ѿ�ȯ�Ծ�����������
 *
 * @return array
 */
function fncGetReportImpLcOrderInfo($objDB)
{
    //�����������
    $sql = "
				select
                    to_char(bankreqdate, 'MM��DD��') as bankreqdate,
                    pono,
                    productcd,
                    productrevisecd,
                    substr(productname, 0, 24) as productname,
                    productnumber,
                    unitname,
                    unitprice,
                    moneyprice,
                    to_char(shipstartdate, 'MM��DD��') as shipstartdate,
                    to_char(shipenddate, 'MM��DD��') as shipenddate,
                    to_char(shipterm, 'DD-Mon') as shipterm,
                    to_char(validterm, 'DD-Mon') as validterm,
                    lcno,
                    reckoninginitialdate,
                    portplace,
                    bankname,
                    reserve1
				from
                    t_reportimportlcorderinfo
                order by productcd, productrevisecd, pono
            ";

    $result = pg_query($objDB->ConnectID, $sql);

    if (!$result) {
        echo "Ģɼ͢�����Ѿ�ȯ�Ծ���μ������Ԥ��ޤ�����\n";
        exit;
    } else {
        return pg_fetch_all($result);
    }
}

/**
 * Ģɼ̤��ѳ�̤��ǧ���������
 *
 * @return array
 */
function fncGetReportUnSettedPriceUnapproval($objDB)
{
    //�����������
    $sql = "
				select
                    *
				from
                    t_reportunsettedpriceunapproval
            ";

    $result = pg_query($objDB->ConnectID, $sql);

    if (!$result) {
        echo "Ģɼ̤��ѳ�̤��ǧ�μ������Ԥ��ޤ�����\n";
        exit;
    } else {
        return pg_fetch_all($result);
    }
}

/**
 * Ģɼ̤��ѳۤ��������
 *
 * @return array
 */
function fncGetReportUnSettedPrice($objDB)
{
    //�����������
    $sql = "
				select
                    managementno,
                    bankname,
                    substr(payeeformalname, 0, 40) as payeeformalname,
                    to_char(shipstartdate, 'MM��DD��') as shipstartdate,
                    lcno,
                    usancesettlement
				from
                    t_reportunsettedprice
            ";

    $result = pg_query($objDB->ConnectID, $sql);

    if (!$result) {
        echo "Ģɼ̤��ѳۤμ������Ԥ��ޤ�����\n";
        exit;
    } else {
        return pg_fetch_all($result);
    }
}

/**
 * ��ʧ�����̤ι��̤��Ѷ�ۤ��������
 *
 * @return array
 */
function fncGetSumofUnSettedPriceByPayfAndBank($objDB)
{
    //�����������
    $sql = "
				select
                    payeeformalname,
                    bankname,
                    sum(usancesettlement)
				from
                    t_reportunsettedprice
                group by payeeformalname, bankname
                order by payeeformalname, bankname
            ";

    $result = pg_query($objDB->ConnectID, $sql);

    if (!$result) {
        echo "��ʧ�����̤ι��̤��Ѷ�ۤμ������Ԥ��ޤ�����\n";
        exit;
    } else {
        return pg_fetch_all($result);
    }
}

/**
 * ��ʧ���̤ι��̤��Ѷ�ۤ��������
 *
 * @return array
 */
function fncGetSumofUnSettedPriceByPayf($objDB)
{
    //�����������
    $sql = "
				select
                    payeeformalname,
                    sum(usancesettlement)
				from
                    t_reportunsettedprice
                group by payeeformalname
                order by payeeformalname
            ";

    $result = pg_query($objDB->ConnectID, $sql);

    if (!$result) {
        echo "��ʧ���̤ι��̤��Ѷ�ۤμ������Ԥ��ޤ�����\n";
        exit;
    } else {
        return pg_fetch_all($result);
    }
}

/**
 * Ģɼ�����Ѥ�L/C�̹��
 *
 * @param [object] $objDB
 * @param [string] $opendate
 * @param [string] $currencyclass
 * @return array
 */
function fncGetLcInfoForReportTwo($objDB, $opendate, $currencyclass)
{
    //�����������
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
            productrevisecd,
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
        WHERE
            opendate = $1
            and currencyclass = $2
            and (lcstate = 0 or lcstate = 3 or lcstate = 4 or lcstate = 7 or lcstate = 8)
            order by lcno, payfnameformal, bankcd
        ";

    // ������ؤ������ͤ����
    $bind = array($opendate, $currencyclass);
    $result = pg_query_params($objDB->ConnectID, $sql, $bind);

    if (!$result) {
        echo "Ģɼ2�����Ѥ�L/C�̹�פμ������Ԥ��ޤ�����\n";
        exit;
    } else {
        return pg_fetch_all($result);
    }
}

/**
 * Ģɼ�����Ѥ�L/C�̹��
 *
 * @param [object] $objDB
 * @param [string] $opendate
 * @param [string] $currencyclass
 * @return array
 */
function fncGetLcInfoForReportFive($objDB, $startYmd, $endYmd, $currencyclass, $type)
{
    if ($type == 1) {
        $where = " and (lcstate = 0 or lcstate = 3 or lcstate = 4 or lcstate = 7 or lcstate = 8)";
    } else {
        $where = " and lcstate = 6";
    }
    //�����������
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
            productrevisecd,
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
        WHERE
            shipstartdate between $1 and $2
            and payfnameomit is not null
            and currencyclass = $3"
        . $where .
        "   order by bankcd, payfnameomit, shipstartdate
        ";

    // ������ؤ������ͤ����
    $bind = array($startYmd, $endYmd, $currencyclass);
    $result = pg_query_params($objDB->ConnectID, $sql, $bind);

    if (!$result) {
        echo "Ģɼ5�����Ѥ�L/C�̹�פμ������Ԥ��ޤ�����\n";
        exit;
    } else {
        return pg_fetch_all($result);
    }
}

/**
 * �̲ߥ졼�Ȥμ���
 *
 * @param [type] $objDB
 * @param [type] $monetaryRateCode
 * @param [type] $monetaryUnitCode
 * @return void
 */
function fncGetMonetaryRate($objDB, $monetaryRateCode, $monetaryUnitCode)
{
    //�����������
    $sql = "
        select
            curconversionrate
        from
            m_monetaryrate
        WHERE
            lngMonetaryRateCode = $1
            and lngMonetaryUnitCode = $2
            and dtmApplyStartDate <= $3
            and dtmApplyEndDate >= $4
        ";

    // ������ؤ������ͤ����
    $bind = array($monetaryRateCode, $monetaryUnitCode, date('Y/m/d'), date('Y/m/d'));

    $result = pg_query_params($objDB->ConnectID, $sql, $bind);

    if (!$result) {
        echo "�̲ߥ졼�Ȥμ������Ԥ��ޤ�����\n";
        exit;
    } else {
        $data = pg_fetch_all($result);
        if ($data && count($data) > 0) {
            return $data["curconversionrate"];
        } else {
            return 0;
        }
    }
}

/**
 * Ģɼ�����Ѥ�L/C�̹��
 *
 * @param [object] $objDB
 * @param [string] $opendate
 * @param [string] $currencyclass
 * @return array
 */
function fncGetLcInfoForReportSix($objDB, $currencyclass, $data)
{
    // ���ѷ�
    $shipYm = str_replace("/", "-", $data["shipYm"]);
    $firstDate = date('Y/m/d', strtotime('first day of ' . $shipYm));
    $lastDate = date('Y/m/d', strtotime('last day of ' . $shipYm));
    $where = "";
    // ��ԥ����ɤ�ALL�ʳ��ξ�硢�������Ȥʤ�
    if ($data["bankcd"] != "0000") {
        $where .= " and bankcd = '" . $data["bankcd"] . "'";
    }
    // L/Copen��ALL�ʳ��ξ�硢�������Ȥʤ�
    if ($data["lcopen"] == "̤ȯ��") {
        $where .= " and lcno = ''";
    } else if ($data["lcopen"] == "��ȯ��") {
        $where .= " and lcno <> ''";
    }
    // �����Ϥ�ALL�ʳ��ξ�硢�������Ȥʤ�
    if ($data["portplace"] != "ALL") {
        $where .= " and portplace = '" . $data["portplace"] . "'";
    }
    //�����������
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
            productrevisecd,
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
        WHERE
            opendate = $1
            and shipstartdate between $2 and $3
            and payfcd = $4"
        . $where .
        "   and currencyclass = $5
            and (lcstate = 0 or lcstate = 3 or lcstate = 4 or lcstate = 7 or lcstate = 8)
        order by productcd, productrevisecd, pono, polineno
        ";
    // ������ؤ������ͤ����
    $bind = array(str_replace("/", "", $data["openYm"]),
        $firstDate, $lastDate, $data["payfCode"], $currencyclass);

    $result = pg_query_params($objDB->ConnectID, $sql, $bind);

    if (!$result) {
        echo "Ģɼ6�����Ѥ�L/C�̹�פμ������Ԥ��ޤ�����\n";
        exit;
    } else {
        return pg_fetch_all($result);
    }
}
/**
 * Ģɼ5̤��ѥꥹ�ȹ�ץǡ������������
 *
 * @param [type] $objDB
 * @return void
 */
function fncGetUnSettedTotal($objDB)
{
    $sql = "
        SELECT
            SUM(bkunapproval.bank1) AS bank1total
            , SUM(bkunapproval.bank2) AS bank2total
            , SUM(bkunapproval.bank3) AS bank3total
            , SUM(bkunapproval.bank4) AS bank4total
            , SUM(bkunapproval.unapprovaltotal) AS unapprovaltotaltotal
            , SUM(bkunapproval.benetotal) AS benetotaltotal
        FROM
            (
            SELECT
                t_reportbybenebktotal.Beneficiary
                , t_reportbybenebktotal.bank1
                , t_reportbybenebktotal.bank2
                , t_reportbybenebktotal.bank3
                , t_reportbybenebktotal.bank4
                , unapproval.unapprovalprice AS unapprovaltotal
                , COALESCE(t_reportbybenebktotal.total, 0) + COALESCE(unapproval.unapprovalprice, 0) AS benetotal
            FROM
                (
                SELECT
                    t_reportunsettedpriceunapproval.payeeformalname AS payeeformal
                    , sum(t_reportunsettedpriceunapproval.unsettledprice) AS unapprovalprice
                FROM
                    t_reportunsettedpriceunapproval
                GROUP BY
                    t_reportunsettedpriceunapproval.payeeformalname
                ) AS unapproval
                RIGHT JOIN t_reportbybenebktotal
                ON unapproval.payeeformal = t_reportbybenebktotal.Beneficiary
            UNION
            SELECT
                unapproval.payeeformal
                , t_reportbybenebktotal.bank1
                , t_reportbybenebktotal.bank2
                , t_reportbybenebktotal.bank3
                , t_reportbybenebktotal.bank4
                , unapproval.unapprovalprice AS unapprovaltotal
                , COALESCE(t_reportbybenebktotal.total, 0) + COALESCE(unapproval.unapprovalprice, 0) AS benetotal
            FROM
                (
                SELECT
                    t_reportunsettedpriceunapproval.payeeformalname AS payeeformal
                    , sum(t_reportunsettedpriceunapproval.unsettledprice) AS unapprovalprice
                FROM
                    t_reportunsettedpriceunapproval
                GROUP BY
                    t_reportunsettedpriceunapproval.payeeformalname
                ) AS unapproval
                LEFT JOIN t_reportbybenebktotal
                ON unapproval.payeeformal = t_reportbybenebktotal.Beneficiary
            ) AS bkunapproval
        ";

    $result = pg_query($objDB->ConnectID, $sql);

    if (!$result) {
        echo "Ģɼ5̤��ѥꥹ�ȹ�פμ������Ԥ��ޤ�����\n";
        exit;
    } else {
        return pg_fetch_object($result);
    }

}

/**
 * Ģɼ5̤��ѥꥹ�ȥǡ������������
 *
 * @param [type] $objDB
 * @return void
 */
function fncGetUnSettedLst($objDB)
{
    $sql = "
        SELECT
            t_reportbybenebktotal.Beneficiary
            , t_reportbybenebktotal.bank1
            , t_reportbybenebktotal.bank2
            , t_reportbybenebktotal.bank3
            , t_reportbybenebktotal.bank4
            , to_char(unapproval.unapprovalprice, 'FM9,999,999.00') AS unapprovaltotal
            , to_char(COALESCE(t_reportbybenebktotal.total, 0) + COALESCE(unapproval.unapprovalprice, 0), 'FM9,999,999.00') AS benetotal
            FROM
            (
                SELECT
                t_reportunsettedpriceunapproval.payeeformalname AS payeeformal
                , sum(t_reportunsettedpriceunapproval.unsettledprice) AS unapprovalprice
                FROM
                t_reportunsettedpriceunapproval
                GROUP BY
                t_reportunsettedpriceunapproval.payeeformalname
            ) AS unapproval
            RIGHT JOIN t_reportbybenebktotal
                ON unapproval.payeeformal = t_reportbybenebktotal.Beneficiary
            UNION
            SELECT
            unapproval.payeeformal
            , t_reportbybenebktotal.bank1
            , t_reportbybenebktotal.bank2
            , t_reportbybenebktotal.bank3
            , t_reportbybenebktotal.bank4
            , to_char(unapproval.unapprovalprice, 'FM9,999,999.00') AS unapprovaltotal
            , to_char(COALESCE(t_reportbybenebktotal.total, 0) + COALESCE(unapproval.unapprovalprice, 0), 'FM9,999,999.00') AS Benebenetotal
            FROM
            (
                SELECT
                t_reportunsettedpriceunapproval.payeeformalname AS payeeformal
                , sum(t_reportunsettedpriceunapproval.unsettledprice) AS unapprovalprice
                FROM
                t_reportunsettedpriceunapproval
                GROUP BY
                t_reportunsettedpriceunapproval.payeeformalname
            ) AS unapproval
            LEFT JOIN t_reportbybenebktotal
                ON unapproval.payeeformal = t_reportbybenebktotal.Beneficiary
        ";

    $result = pg_query($objDB->ConnectID, $sql);

    if (!$result) {
        echo "Ģɼ5̤��ѥꥹ�ȥǡ����μ������Ԥ��ޤ�����\n";
        exit;
    } else {
        return pg_fetch_all($result);
    }

}

/**
 * ĢɼBeneBK�ι�׶�ۤ��������
 *
 * @return array
 */
function fncGetSumofBeneBkPrice($objDB)
{
    //�����������
    $sql = "
				select
                    sum(bank1) as sum_1,
                    sum(bank2) as sum_2,
                    sum(bank3) as sum_3,
                    sum(bank4) as sum_4,
                    sum(total) as sum_5
				from
                    t_reportbybenebktotal
            ";

    $result = pg_query($objDB->ConnectID, $sql);

    if (!$result) {
        echo "ĢɼBeneBK�ι�׶�ۤμ������Ԥ��ޤ�����\n";
        exit;
    } else {
        return pg_fetch_object($result);
    }
}

/**
 * ĢɼBene���̽��פι�׶�ۤ��������
 *
 * @return array
 */
function fncGetSumofBeneMonCal($objDB)
{
    //�����������
    $sql = "
				select
                    sum(date1) as sum_1,
                    sum(date2) as sum_2,
                    sum(date3) as sum_3,
                    sum(date4) as sum_4,
                    sum(date5) as sum_5,
                    sum(date6) as sum_6,
                    sum(date7) as sum_7,
                    sum(date8) as sum_8,
                    sum(date9) as sum_9,
                    sum(date10) as sum_10,
                    sum(date11) as sum_11,
                    sum(total) as sum_12
				from
                    t_reportbybenemonthcalculation
            ";

    $result = pg_query($objDB->ConnectID, $sql);

    if (!$result) {
        echo "ĢɼBene���̽��פι�׶�ۤμ������Ԥ��ޤ�����\n";
        exit;
    } else {
        return pg_fetch_object($result);
    }
}

/**
 * ���ո��ޥ���������������
 *
 * @return void
 */
function fncGetSendInfo($objDB)
{
    //���饹������
    $db = new lcConnect();
    //�����������
    $sql = "
				select
					*
				from
                    m_sendinfo
                order by sendno
            ";

    //�Х���ɤ�����
    $bind = array();
    $result = pg_query_params($objDB->ConnectID, $sql, $bind);

    if (!$result) {
        echo "���ո��ޥ�������������Ԥ��ޤ�����\n";
        exit;
    } else {
        return pg_fetch_all($result);
    }
}


/**
 * ��ʧ�襳���ɤˤ���ʧ��ޥ���������������
 *
 * @param [object] $objDB
 * @param [string] $payfcd
 * @return void
 */
function fncGetPayfInfoByPayfcd($objDB, $payfcd)
{
    //���饹������
    $db = new lcConnect();
    //�����������
    $sql = "
				select
					*
				from
                    m_payfinfo
                where
                    payfcd = $1
            ";

    //�Х���ɤ�����
    $bind = array($payfcd);

    $result = pg_query_params($objDB->ConnectID, $sql, $bind);

    if (!$result) {
        echo "��ʧ�襳���ɤˤ���ʧ��ޥ�������������Ԥ��ޤ�����\n";
        exit;
    } else {
        return pg_fetch_object($result);
    }
}


/**
 * Ģɼ͢�����Ѿ�ȯ�Ծ���ι�׶�ۤ��������
 *
 * @return array
 */
function fncGetSumofImpLcOrderPrice($objDB)
{
    //�����������
    $sql = "
				select
                    sum(moneyprice) totalprice
				from
                    t_reportimportlcorderinfo
            ";

    $result = pg_query($objDB->ConnectID, $sql);

    if (!$result) {
        echo "Ģɼ͢�����Ѿ�ȯ�Ծ���ι�׶�ۤμ������Ԥ��ޤ�����\n";
        exit;
    } else {
        return pg_fetch_object($result)->totalprice;
    }
}