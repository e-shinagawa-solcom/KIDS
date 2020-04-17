<?php

/**
 * 支払先銀行別の合計金額取得
 *
 * @param [object] $objDB
 * @param [string] $shipym
 * @param [string] $currencyclass
 * @param [string] $type
 * @return array
 */
function fncGetSumOfMoneypriceByPayfAndBank($objDB, $data, $type)
{
    //クエリの生成
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
    // クエリへの設定値の定義
    $bind = array($data["opendate"], $data["currencyclass"]);

    // クエリの実行
    $result = pg_query_params($objDB->ConnectID, $sql, $bind);

    if (!$result) {
        echo "支払先銀行別の合計金額取得失敗しました。\n";
        exit;
    } else {
        return pg_fetch_all($result);
    }
}

/**
 * 支払先月別の合計金額取得
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

    // クエリへの設定値の定義
    $bind = array($data["opendatefrom"], $data["opendateto"], $data["currencyclass"]);
    // クエリの実行
    $result = pg_query_params($objDB->ConnectID, $sql, $bind);

    if (!$result) {
        echo "支払先銀行別の合計金額取得失敗しました。\n";
        exit;
    } else {
        return pg_fetch_all($result);
    }
}

/**
 * 支払先月別の合計金額取得
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
            group by payfcd, shipym, payfnameformal
            order by payfcd, shipym, payfnameformal
            ";

    // クエリへの設定値の定義
    $bind = array($data["opendatefrom"], $data["opendateto"], $data["currencyclass"]);
    // クエリの実行
    $result = pg_query_params($objDB->ConnectID, $sql, $bind);

    if (!$result) {
        echo "支払先銀行別の合計金額取得失敗しました。\n";
        exit;
    } else {
        return pg_fetch_all($result);
    }
}

/**
 * 支払先別の合計金額取得
 *
 * @param [object] $objDB
 * @param [string] $shipym
 * @param [string] $currencyclass
 * @return array
 */
function fncGetSumOfMoneypriceByPayf($objDB, $data, $type)
{

    //クエリの生成
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
    // クエリの実行
    $result = pg_query_params($objDB->ConnectID, $sql, $bind);

    if (!$result) {
        echo "支払先別の合計金額取得失敗しました。\n";
        exit;
    } else {
        return pg_fetch_all($result);
    }
}

/**
 * 帳票BeneBk別合計の削除を行う
 *
 * @param [object] $objDB
 * @return void 削除件数
 */
function fncDeleteReportByBenebktotal($objDB)
{
    $sql = "
        delete from t_reportbybenebktotal
        ";

    $result = pg_query($objDB->ConnectID, $sql);

    if (!$result) {
        echo "帳票BeneBk別合計削除失敗しました。\n";
        exit;
    }

    return pg_affected_rows($result);
}

/**
 * 帳票Bene月別集計の削除を行う
 *
 * @param [object] $objDB
 * @return void 削除件数
 */
function fncDeleteReportByBeneMonthCal($objDB)
{
    $sql = "
        delete from t_reportbybenemonthcalculation
        ";

    $result = pg_query($objDB->ConnectID, $sql);

    if (!$result) {
        echo "帳票Bene月別集計削除失敗しました。\n";
        exit;
    }

    return pg_affected_rows($result);
}

/**
 * 帳票LC別明細の削除を行う
 *
 * @param [object] $objDB
 * @return void 削除件数
 */
function fncDeleteReportByLcDetail($objDB)
{
    $sql = "
        delete from t_reportbylcdetail
        ";

    $result = pg_query($objDB->ConnectID, $sql);

    if (!$result) {
        echo "帳票LC別明細削除に失敗しました。\n";
        exit;
    }

    return pg_affected_rows($result);
}

/**
 * 帳票LC別合計の削除を行う
 *
 * @param [object] $objDB
 * @return void 削除件数
 */
function fncDeleteReportByLcTotal($objDB)
{
    $sql = "
        delete from t_reportbylctotal
        ";

    $result = pg_query($objDB->ConnectID, $sql);

    if (!$result) {
        echo "帳票LC別合計の削除に失敗しました。\n";
        exit;
    }

    return pg_affected_rows($result);
}

/**
 * 帳票輸入信用状発行情報の削除を行う
 *
 * @param [object] $objDB
 * @return void 削除件数
 */
function fncDeleteReportImpLcOrderInfo($objDB)
{
    $sql = "
        delete from t_reportimportlcorderinfo
        ";

    $result = pg_query($objDB->ConnectID, $sql);

    if (!$result) {
        echo "帳票輸入信用状発行情報の削除に失敗しました。\n";
        exit;
    }

    return pg_affected_rows($result);
}

/**
 * 帳票未決済額の削除を行う
 *
 * @param [object] $objDB
 * @return void 削除件数
 */
function fncDeleteReportUnSettedPrice($objDB)
{
    $sql = "
        delete from t_reportunsettedprice
        ";

    $result = pg_query($objDB->ConnectID, $sql);

    if (!$result) {
        echo "帳票未決済額の削除に失敗しました。\n";
        exit;
    }

    return pg_affected_rows($result);
}

/**
 * 帳票未決済額未承認の削除を行う
 *
 * @param [object] $objDB
 * @return void 削除件数
 */
function fncDeleteReportUnSettedPriceUnapproval($objDB)
{
    $sql = "
        delete from t_reportunsettedpriceunapproval
        ";

    $result = pg_query($objDB->ConnectID, $sql);

    if (!$result) {
        echo "帳票未決済額未承認の削除に失敗しました。\n";
        exit;
    }

    return pg_affected_rows($result);
}

/**
 * 帳票未決済額未承認の登録を行う
 *
 * @param [object] $objDB
 * @param [array] $data
 * @return void 登録件数
 */
function fncInsertReportUnSettedPriceUnapproval($objDB, $data)
{
    $sql = "
        insert into t_reportunsettedpriceunapproval
        values ($1
        ,$2)";

    //バインドの設定
    $bind = array($data["payeeformalname"]
        , $data["unsettledprice"]);

    $result = pg_query_params($objDB->ConnectID, $sql, $bind);

    if (!$result) {
        echo "帳票未決済額未承認登録失敗しました。\n";
        exit;
    } else {
        return pg_affected_rows($result);
    }
}

/**
 * 帳票未決済額の登録を行う
 *
 * @param [object] $objDB
 * @param [array] $data
 * @return void 登録件数
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
                ,$6
                ,$7)";

    //バインドの設定
    $bind = array($data["managementno"]
        , $data["bankname"]
        , $data["payeeformalname"]
        , $data["shipstartdate"]
        , $data["lcno"]
        , $data["productcode"]
        , $data["usancesettlement"]);

    $result = pg_query_params($objDB->ConnectID, $sql, $bind);

    if (!$result) {
        echo "帳票未決済額登録失敗しました。\n";
        exit;
    } else {
        return pg_affected_rows($result);
    }
}

/**
 * 帳票輸入信用状発行情報の登録を行う
 *
 * @param [object] $objDB
 * @return void 削除件数
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

    //バインドの設定
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
        echo "帳票輸入信用状発行情報の登録失敗しました。\n";
        exit;
    } else {
        return pg_affected_rows($result);
    }
}

/**
 * 帳票LC別合計の登録を行う
 *
 * @param [object] $objDB
 * @return void 削除件数
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

    //バインドの設定
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
        echo "帳票LC別合計の登録失敗しました。\n";
        exit;
    } else {
        return pg_affected_rows($result);
    }
}

/**
 * 帳票LC別明細の登録を行う
 *
 * @param [object] $objDB
 * @return void 削除件数
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

    //バインドの設定
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
        echo "帳票LC別明細の登録失敗しました。\n";
        exit;
    } else {
        return pg_affected_rows($result);
    }
}

/**
 * 帳票Bene月別集計の登録を行う
 *
 * @param [object] $objDB
 * @return void 削除件数
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

    //バインドの設定
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
        echo "帳票Bene月別集計の登録失敗しました。\n";
        exit;
    } else {
        return pg_affected_rows($result);
    }
}

/**
 * 帳票BeneBk別合計の登録を行う
 *
 * @param [object] $objDB
 * @return void 削除件数
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

    //バインドの設定
    $bind = array($data["beneficiary"]
        , $data["bank1"]
        , $data["bank2"]
        , $data["bank3"]
        , $data["bank4"]
        , $data["total"]);

    $result = pg_query_params($objDB->ConnectID, $sql, $bind);

    if (!$result) {
        echo "帳票BeneBk別合計の登録失敗しました。\n";
        exit;
    } else {
        return pg_affected_rows($result);
    }
}

/**
 * 帳票BeneBk別合計を取得する
 *
 * @return array
 */
function fncGetReportByBenebktotal($objDB, $offset, $limit)
{
    //クエリの生成
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
                    order by beneficiary
                    LIMIT $1 OFFSET $2
            ";

    //バインドの設定
    $bind = array($limit, $offset);

    $result = pg_query_params($objDB->ConnectID, $sql, $bind);

    if (!$result) {
        echo "帳票BeneBk別合計の取得失敗しました。\n";
        exit;
    } else {
        return pg_fetch_all($result);
    }
}

/**
 * 帳票Bene月別集計を取得する
 *
 * @return array
 */
function fncGetReportByBeneMonthCal($objDB, $offset, $limit)
{
    //クエリの生成
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
                    order by beneficiary
                    LIMIT $1 OFFSET $2
            ";

    //バインドの設定
    $bind = array($limit, $offset);

    $result = pg_query_params($objDB->ConnectID, $sql, $bind);

    if (!$result) {
        echo "帳票Bene月別集計の取得失敗しました。\n";
        exit;
    } else {
        return pg_fetch_all($result);
    }
}

/**
 * 帳票LC別明細を取得する
 *
 * @return array
 */
function fncGetReportByLcDetail($objDB, $offset, $limit)
{
    //クエリの生成
    $sql = "
				select
                    lcno,
                    pono,
                    substr(factoryname, 0 ,24) as factoryname,
                    productcd || '_' || productrevisecd,
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
                    LIMIT $1 OFFSET $2
            ";

    //バインドの設定
    $bind = array($limit, $offset);

    $result = pg_query_params($objDB->ConnectID, $sql, $bind);

    if (!$result) {
        echo "帳票LC別明細の取得失敗しました。\n";
        exit;
    } else {
        return pg_fetch_all($result);
    }
}

/**
 * 帳票LC別合計を取得する
 *
 * @return array
 */
function fncGetReportByLcTotal($objDB, $offset, $limit)
{
    //クエリの生成
    $sql = "
				select
                    lcno,
                    substr(factoryname, 0 ,50) as factoryname,
                    price,
                    to_char(shipterm, 'DD-Mon') as shipterm,
                    to_char(validterm, 'DD-Mon') as validterm,
                    bankname,
                    to_char(bankreqdate, 'MM月DD日') as bankreqdate,
                    to_char(lcamopen, 'MM月DD日') as lcamopen
				from
                    t_reportbylctotal
                    LIMIT $1 OFFSET $2
            ";

    //バインドの設定
    $bind = array($limit, $offset);

    $result = pg_query_params($objDB->ConnectID, $sql, $bind);

    if (!$result) {
        echo "帳票LC別合計の取得失敗しました。\n";
        exit;
    } else {
        return pg_fetch_all($result);
    }
}

/**
 * 帳票輸入信用状発行情報を取得する
 *
 * @return array
 */
function fncGetReportImpLcOrderInfo($objDB, $offset, $limit)
{
    //クエリの生成
    $sql = "
				select
                    to_char(bankreqdate, 'MM月DD日') as bankreqdate,
                    pono,
                    productcd || '_' || productrevisecd,
                    substr(productname, 0, 24) as productname,
                    productnumber,
                    unitname,
                    unitprice,
                    moneyprice,
                    to_char(shipstartdate, 'MM月DD日') as shipstartdate,
                    to_char(shipenddate, 'MM月DD日') as shipenddate,
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
                LIMIT $1 OFFSET $2
            ";

    //バインドの設定
    $bind = array($limit, $offset);

    $result = pg_query_params($objDB->ConnectID, $sql, $bind);

    if (!$result) {
        echo "帳票輸入信用状発行情報の取得失敗しました。\n";
        exit;
    } else {
        return pg_fetch_all($result);
    }
}

/**
 * 帳票未決済額未承認を取得する
 *
 * @return array
 */
function fncGetReportUnSettedPriceUnapproval($objDB)
{
    //クエリの生成
    $sql = "
				select
                    *
				from
                    t_reportunsettedpriceunapproval
            ";

    $result = pg_query($objDB->ConnectID, $sql);

    if (!$result) {
        echo "帳票未決済額未承認の取得失敗しました。\n";
        exit;
    } else {
        return pg_fetch_all($result);
    }
}

/**
 * 帳票未決済額を取得する
 *
 * @return array
 */
function fncGetReportUnSettedPrice($objDB, $offset, $limit)
{
    //クエリの生成
    $sql = "
				select
                    managementno,
                    bankname,
                    substr(payeeformalname, 0, 40) as payeeformalname,
                    to_char(shipstartdate, 'MM月DD日') as shipstartdate,
                    lcno,
                    productcode,
                    usancesettlement
				from
                    t_reportunsettedprice
                    LIMIT $1 OFFSET $2
            ";

    //バインドの設定
    $bind = array($limit, $offset);

    $result = pg_query_params($objDB->ConnectID, $sql, $bind);

    if (!$result) {
        echo "帳票未決済額の取得失敗しました。\n";
        exit;
    } else {
        return pg_fetch_all($result);
    }
}

/**
 * 支払先銀行別の合計未決済金額を取得する
 *
 * @return array
 */
function fncGetSumofUnSettedPriceByPayfAndBank($objDB)
{
    //クエリの生成
    $sql = "
				select
                    payeeformalname,
                    bankname,
                    sum(usancesettlement) as totalmoneyprice
				from
                    t_reportunsettedprice
                group by payeeformalname, bankname
                order by payeeformalname, bankname
            ";

    $result = pg_query($objDB->ConnectID, $sql);

    if (!$result) {
        echo "支払先銀行別の合計未決済金額の取得失敗しました。\n";
        exit;
    } else {
        return pg_fetch_all($result);
    }
}

/**
 * 支払先別の合計未決済金額を取得する
 *
 * @return array
 */
function fncGetSumofUnSettedPriceByPayf($objDB)
{
    //クエリの生成
    $sql = "
				select
                    payeeformalname,
                    sum(usancesettlement) as totalmoneyprice
				from
                    t_reportunsettedprice
                group by payeeformalname
                order by payeeformalname
            ";

    $result = pg_query($objDB->ConnectID, $sql);

    if (!$result) {
        echo "支払先別の合計未決済金額の取得失敗しました。\n";
        exit;
    } else {
        return pg_fetch_all($result);
    }
}

/**
 * 帳票出力用のL/C別合計
 *
 * @param [object] $objDB
 * @param [string] $opendate
 * @param [string] $currencyclass
 * @return array
 */
function fncGetSumMoneyPriceForReportTwo($objDB, $opendate, $currencyclass)
{
    //クエリの生成
    $sql = "
        select
            lcno,
            payfnameformal,
            bankcd,
            sum(moneyprice) as itemprice
        from
            t_lcinfo
        WHERE
            opendate = $1
            and currencyclass = $2
            and (lcstate = 0 or lcstate = 3 or lcstate = 4 or lcstate = 7 or lcstate = 8)
            group by lcno, payfnameformal, bankcd
        ";

    // クエリへの設定値の定義
    $bind = array($opendate, $currencyclass);
    $result = pg_query_params($objDB->ConnectID, $sql, $bind);

    if (!$result) {
        echo "帳票2出力用のL/C別合計の取得失敗しました。\n";
        exit;
    } else {
        return pg_fetch_all($result);
    }
}


/**
 * 帳票出力用のL/C別合計
 *
 * @param [object] $objDB
 * @param [string] $opendate
 * @param [string] $currencyclass
 * @return array
 */
function fncGetLcInfoForReportTwo($objDB, $data)
{
    //クエリの生成
    $sql = "
        select
            lcno,
            payfnameformal,
            moneyprice,
            shipterm,
            validterm,
            bankcd,
            bankname,
            bankreqdate,
            lcamopen
        from
            t_lcinfo
        WHERE
            opendate = '". $data["opendate"] ."'";
    $sql .= " and currencyclass = '". $data["currencyclass"] ."'";
    $sql .= " and (lcstate = 0 or lcstate = 3 or lcstate = 4 or lcstate = 7 or lcstate = 8)";
    if ($data["lcno"] == null) {
        $sql .= " and lcno is null ";
    } else {        
        $sql .= " and lcno = '". $data["lcno"] ."'";
    }
    if ($data["payfnameformal"] == null) {
        $sql .= " and payfnameformal is null ";
    } else {        
        $sql .= " and payfnameformal = '". $data["payfnameformal"] ."'";
    }
    if ($data["bankcd"] == null) {
        $sql .= " and bankcd is null ";
    } else {        
        $sql .= " and bankcd = '". $data["bankcd"] ."'";
    }

    $result = pg_query($objDB->ConnectID, $sql);

    if (!$result) {
        echo "帳票2出力用のL/C別情報の取得失敗しました。\n";
        exit;
    } else {
        return pg_fetch_all($result);
    }
}

/**
 * 帳票出力用のL/C別合計
 *
 * @param [object] $objDB
 * @param [string] $opendate
 * @param [string] $currencyclass
 * @return array
 */
function fncGetLcInfoForReportThree($objDB, $opendate, $currencyclass)
{
    //クエリの生成
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

    // クエリへの設定値の定義
    $bind = array($opendate, $currencyclass);
    $result = pg_query_params($objDB->ConnectID, $sql, $bind);

    if (!$result) {
        echo "帳票2出力用のL/C別明細の取得失敗しました。\n";
        exit;
    } else {
        return pg_fetch_all($result);
    }
}

/**
 * 帳票出力用のL/C別合計
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
    //クエリの生成
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
            shipstartdate between to_date($1,'YYYY/MM/DD') and to_date($2,'YYYY/MM/DD')
            and payfnameomit is not null
            and currencyclass = $3
            and moneyprice - ( 
                COALESCE(bldetail1money, 0) + COALESCE(bldetail2money, 0) + COALESCE(bldetail3money, 0)
              ) != 0"
        . $where .
        "   order by bankcd, payfnameomit, shipstartdate
        ";
    // クエリへの設定値の定義
    $bind = array($startYmd, $endYmd, $currencyclass);
    $result = pg_query_params($objDB->ConnectID, $sql, $bind);
    if (!$result) {
        echo "帳票5出力用のL/C別合計の取得失敗しました。\n";
        exit;
    } else {
        return pg_fetch_all($result);
    }
}

/**
 * 通貨レートの取得
 *
 * @param [type] $objDB
 * @param [type] $monetaryRateCode
 * @param [type] $monetaryUnitCode
 * @return void
 */
function fncGetMonetaryRate($objDB, $monetaryRateCode, $monetaryUnitCode)
{
    //クエリの生成
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

    // クエリへの設定値の定義
    $bind = array($monetaryRateCode, $monetaryUnitCode, date('Y/m/d'), date('Y/m/d'));

    $result = pg_query_params($objDB->ConnectID, $sql, $bind);

    if (!$result) {
        echo "通貨レートの取得失敗しました。\n";
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
 * 帳票出力用のL/C別合計
 *
 * @param [object] $objDB
 * @param [string] $opendate
 * @param [string] $currencyclass
 * @return array
 */
function fncGetLcInfoForReportSix($objDB, $currencyclass, $data)
{
    $where = "";
    if ($data["shipYm"] != "") {
        // 船積月
        $shipYm = str_replace("/", "-", $data["shipYm"]);
        $firstDate = date('Y/m/d', strtotime('first day of ' . $shipYm));
        $lastDate = date('Y/m/d', strtotime('last day of ' . $shipYm));
        $where .= " and shipstartdate between to_date('". $firstDate ."','YYYY/MM/DD') and to_date('" .$lastDate . "','YYYY/MM/DD')";
    }
    // 銀行コードがALL以外の場合、検索条件となる
    if ($data["bankcd"] != "0000") {
        $where .= " and bankcd = '" . $data["bankcd"] . "'";
    }
    // L/CopenがALL以外の場合、検索条件となる
    if (trim($data["lcopen"]) == "未発行") {
        $where .= " and lcno = ''";
    } else if (trim($data["lcopen"]) == "既発行") {
        $where .= " and lcno <> ''";
    }
    // 荷揚地がALL以外の場合、検索条件となる
    if (trim($data["portplace"]) != "ALL") {
        $where .= " and portplace = '" . $data["portplace"] . "'";
    }
    //クエリの生成
    $sql = "
        select
            payfnameomit,
            opendate,
            portplace,
            tlc.pono,
            tlc.polineno,
            tlc.poreviseno,
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
            t_lcinfo tlc
            INNER JOIN (
                select
                  pono
                  , polineno
                  , max(poreviseno) as poreviseno
                from
                  t_lcinfo
                group by
                  pono
                  , polineno
              ) tlc1
                on tlc.pono = tlc1.pono
                and tlc.polineno = tlc1.polineno
                and tlc.poreviseno = tlc1.poreviseno
        WHERE
            opendate = $1
            and payfcd = $2"
        . $where .
        "   and currencyclass = $3
            and (lcstate = 0 or lcstate = 3 or lcstate = 4 or lcstate = 7 or lcstate = 8)
        order by productcd, productrevisecd, tlc.pono, tlc.polineno
        ";
    // クエリへの設定値の定義
    $bind = array(str_replace("/", "", $data["openYm"]), $data["payfCode"], $currencyclass);
    $result = pg_query_params($objDB->ConnectID, $sql, $bind);
    if (!$result) {
        echo "帳票6出力用のL/C別合計の取得失敗しました。\n";
        exit;
    } else {
        return pg_fetch_all($result);
    }
}
/**
 * 帳票5未決済リスト合計データを取得する
 *
 * @param [type] $objDB
 * @return void
 */
function fncGetUnSettedTotal($objDB, $offset, $limt)
{
    $sql = "
        SELECT
            SUM(tmp.bank1) AS bank1total
            , SUM(tmp.bank2) AS bank2total
            , SUM(tmp.bank3) AS bank3total
            , SUM(tmp.bank4) AS bank4total
            , SUM(tmp.unapprovaltotal) AS unapprovaltotaltotal
            , SUM(tmp.benetotal) AS benetotaltotal
        FROM
            (
                SELECT
                    bkunapproval.Beneficiary
                    , bkunapproval.bank1
                    , bkunapproval.bank2
                    , bkunapproval.bank3
                    , bkunapproval.bank4
                    , bkunapproval.unapprovaltotal
                    , bkunapproval.benetotal
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
                        unapproval.payeeformal as Beneficiary
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
                LIMIT $1 OFFSET $2
            ) as tmp
        ";


    //バインドの設定
    $bind = array($limt, $offset);

    $result = pg_query_params($objDB->ConnectID, $sql, $bind);

    if (!$result) {
        echo "帳票5未決済リスト合計の取得失敗しました。\n";
        exit;
    } else {
        return pg_fetch_object($result);
    }

}

/**
 * 帳票5未決済リストデータを取得する
 *
 * @param [type] $objDB
 * @return void
 */
function fncGetUnSettedLst($objDB, $offset, $limt)
{
    $sql = "
        SELECT
            tmp.Beneficiary
            , tmp.bank1
            , tmp.bank2
            , tmp.bank3
            , tmp.bank4
            , tmp.unapprovaltotal
            , tmp.benetotal
        FROM
            (
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
                unapproval.payeeformal as Beneficiary
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
                LEFT JOIN t_reportbybenebktotal
                    ON unapproval.payeeformal = t_reportbybenebktotal.Beneficiary
            ) as tmp
            LIMIT $1 OFFSET $2
        ";

    //バインドの設定
    $bind = array($limt, $offset);
    $result = pg_query_params($objDB->ConnectID, $sql, $bind);

    if (!$result) {
        echo "帳票5未決済リストデータの取得失敗しました。\n";
        exit;
    } else {
        return pg_fetch_all($result);
    }

}

/**
 * 帳票BeneBKの合計金額を取得する
 *
 * @return array
 */
function fncGetSumofBeneBkPrice($objDB, $offset, $limt)
{
    //クエリの生成
    $sql = "
				select
                    sum(tmp.bank1) as sum_1,
                    sum(tmp.bank2) as sum_2,
                    sum(tmp.bank3) as sum_3,
                    sum(tmp.bank4) as sum_4,
                    sum(tmp.total) as sum_5
                from
                    (
                    select
                        beneficiary,
                        bank1,
                        bank2,
                        bank3,
                        bank4,
                        total
                    from
                        t_reportbybenebktotal
                        order by beneficiary
                        LIMIT $1 OFFSET $2
                    ) as tmp

            ";

    //バインドの設定
    $bind = array($limt, $offset);
    $result = pg_query_params($objDB->ConnectID, $sql, $bind);

    if (!$result) {
        echo "帳票BeneBKの合計金額の取得失敗しました。\n";
        exit;
    } else {
        return pg_fetch_object($result);
    }
}

/**
 * 帳票Bene月別集計の合計金額を取得する
 *
 * @return array
 */
function fncGetSumofBeneMonCal($objDB, $offset, $limt)
{
    //クエリの生成
    $sql = "
				select
                    sum(tmp.date1) as sum_1,
                    sum(tmp.date2) as sum_2,
                    sum(tmp.date3) as sum_3,
                    sum(tmp.date4) as sum_4,
                    sum(tmp.date5) as sum_5,
                    sum(tmp.date6) as sum_6,
                    sum(tmp.date7) as sum_7,
                    sum(tmp.date8) as sum_8,
                    sum(tmp.date9) as sum_9,
                    sum(tmp.date10) as sum_10,
                    sum(tmp.date11) as sum_11,
                    sum(tmp.total) as sum_12
				from
                    (
                    select
                        beneficiary,
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
                        order by beneficiary
                        LIMIT $1 OFFSET $2
                    ) as tmp
            ";

    //バインドの設定
    $bind = array($limt, $offset);

    $result = pg_query_params($objDB->ConnectID, $sql, $bind);

    if (!$result) {
        echo "帳票Bene月別集計の合計金額の取得失敗しました。\n";
        exit;
    } else {
        return pg_fetch_object($result);
    }
}

/**
 * 送付元マスタ情報を取得する
 *
 * @return void
 */
function fncGetSendInfo($objDB)
{
    //クラスの生成
    $db = new lcConnect();
    //クエリの生成
    $sql = "
				select
					*
				from
                    m_sendinfo
                order by sendno
            ";

    //バインドの設定
    $bind = array();
    $result = pg_query_params($objDB->ConnectID, $sql, $bind);

    if (!$result) {
        echo "送付元マスタ情報取得失敗しました。\n";
        exit;
    } else {
        return pg_fetch_all($result);
    }
}

/**
 * 支払先コードにより支払先マスタ情報を取得する
 *
 * @param [object] $objDB
 * @param [string] $payfcd
 * @return void
 */
function fncGetPayfInfoByPayfcd($objDB, $payfcd)
{
    //クラスの生成
    $db = new lcConnect();
    //クエリの生成
    $sql = "
				select
					*
				from
                    m_payfinfo
                where
                    payfcd = $1
            ";

    //バインドの設定
    $bind = array($payfcd);

    $result = pg_query_params($objDB->ConnectID, $sql, $bind);

    if (!$result) {
        echo "支払先コードにより支払先マスタ情報取得失敗しました。\n";
        exit;
    } else {
        return pg_fetch_object($result);
    }
}

/**
 * 帳票輸入信用状発行情報の合計金額を取得する
 *
 * @return array
 */
function fncGetSumofImpLcOrderPrice($objDB, $offset, $limt)
{
    //クエリの生成
    $sql = "
        select
            sum(tmp.moneyprice) as totalprice
        from
            (
            select
                *
            from
                t_reportimportlcorderinfo
            order by
                productcd
                , productrevisecd
                , pono
            LIMIT $1 OFFSET $2
            ) as tmp
            ";

    //バインドの設定
    $bind = array($limt, $offset);

    $result = pg_query_params($objDB->ConnectID, $sql, $bind);

    if (!$result) {
        echo "帳票輸入信用状発行情報の合計金額の取得失敗しました。\n";
        exit;
    } else {
        return pg_fetch_object($result)->totalprice;
    }
}
