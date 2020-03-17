<?php

/**
 * LC情報の登録を行う
 *
 * @param [object] $objDB
 * @param [array] $data
 * @return void 登録件数
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
        ,$40
        ,$41)";

    //バインドの設定
    $bind = array($data["payfnameomit"]
        , $data["opendate"]
        , $data["portplace"]
        , $data["pono"]
        , $data["polineno"]
        , $data["poreviseno"]
        , $data["postate"]
        , $data["payfcd"]
        , $data["productcd"]
        , $data["productrevisecd"]
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
        echo "L/C情報登録失敗しました。\n";
        exit("L/C情報登録失敗しました。");
    } else {
        return pg_affected_rows($result);
    }
}

/**
 * LC情報リストの取得を行う
 *
 * @param [object] $objDB
 * @param [array] $data
 * @return void
 */
function fncGetLcInfoData($objDB, $data)
{
    //基本取得
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
        ";

    $condcount = 0;
    switch ($data["mode"]) {
        case "0":
            break;
        case "1":
            //抽出条件
            if ($data["from"] != "" && $data["to"] == "") {
                $condcount += 1;
                $sql .= ($condcount == 1) ? " where " : " and ";
                $sql .= "opendate = '" . $data["from"] . "'";
            } else if ($data["from"] != "" && $data["to"] != "") {
                $condcount += 1;
                $sql .= ($condcount == 1) ? " where " : " and ";
                $sql .= " opendate between '" . $data["from"] . "' and '" . $data["to"] . "'";
            }
            if ($data["payfcd"] != "") {                
                $condcount += 1;
                $sql .= ($condcount == 1) ? " where " : " and ";
                $sql .= " payfcd = '" . $data["payfcd"] . "'";
            }
//            if ($data["payfnameomit"] != "") {
//                $sql .= " and payfnameomit = '" . $data["payfnameomit"] . "'";
//            }
            if ($data["getDataModeFlg"] == 1) {                
                $condcount += 1;
                $sql .= ($condcount == 1) ? " where " : " and ";
                $sql .= " lcstate in (0,3,4,7,8) ";
            }
            break;
        case "2":
            //シミュレート条件
            $sql .= "where opendate = '" . $data["to"] . "'";
            if ($data["getDataModeFlg"] == 1) {
                $sql .= " and lcstate in (0,3,4,7,8) ";
            }
            break;
        case "3":
            $sql .= " where lcstate in (0,3,4,7,8) ";
            break;
    }

    $sql .= " order by poupdatedate desc,pono, polineno";

    $result = pg_query($objDB->ConnectID, $sql);

    if (!$result) {
        echo "L/C情報取得失敗しました。\n";
        exit;
    } else {
        return pg_fetch_all($result);
    }
}

/**
 * L/C情報データの削除を行う
 *
 * @param [object] $objDB
 * @return void 削除件数
 */
function fncDeleteLcInfo($objDB)
{
    $sql = "
            delete from t_lcinfo
        ";

    $result = pg_query($objDB->ConnectID, $sql);

    if (!$result) {
        echo "L/C情報削除失敗しました。\n";
        exit;
    }

    return pg_affected_rows($result);
}

/**
 * LC情報単体の取得を行う
 *
 * @param [object] $objDB
 * @param [array] $data
 * @return void LC情報単体
 */
function fncGetLcInfoSingle($objDB, $data)
{

    //基本取得
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
        where
            pono = $1 and
            poreviseno = $2 and
            polineno = $3
        ";

    //バインドの設定
    $bind = array($data["pono"], $data["poreviseno"], $data["polineno"]);

    $result = pg_query_params($objDB->ConnectID, $sql, $bind);

    if (!$result) {
        echo "L/C情報単体取得失敗しました。\n";
        exit;
    } else {
        return pg_fetch_object($result);
    }
}

/**
 * 荷揚地情報リストの取得
 *
 * @param [object] $objDB
 * @return array
 */
function fncGetPortplace($objDB)
{
    //クエリの生成
    $sql = "
				SELECT
					DISTINCT portplace
				FROM
					t_lcinfo
            ";

    $result = pg_query($objDB->ConnectID, $sql);

    if (!$result) {
        echo "荷揚地情報取得失敗しました。\n";
        exit;
    } else {
        return pg_fetch_all($result);
    }
}

/**
 * 荷揚地情報(ALL)リストの取得
 *
 * @param [object] $objDB
 * @return array
 */
function fncGetPortplaceAndAll($objDB)
{
    //クエリの生成
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
        echo "荷揚地情報(ALL)取得失敗しました。\n";
        exit;
    } else {
        return pg_fetch_all($result);
    }
}

/**
 * 通貨区分リストの取得
 *
 * @param [object] $objDB
 * @return array
 */
function fncGetCurrencyClassList($objDB)
{
    //クエリの生成
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
        echo "通貨区分リスト取得失敗しました。\n";
        exit;
    } else {
        return pg_fetch_all($result);
    }
}

/**
 * 通貨区分リスト(未承認含む)の取得
 *
 * @param [object] $objDB
 * @return array
 */
function fncGetCurrencyClassListAll($objDB)
{
    //クエリの生成
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
        echo "通貨区分リスト(未承認含む)取得失敗しました。\n";
        exit;
    } else {
        return pg_fetch_all($result);
    }
}

/**
 * 現在時刻を取得する
 *
 * @param [object] $objDB
 * @return array
 */
function fncGetCurDate($objDB)
{
    //クエリの生成
    $sql = "
            SELECT CURRENT_TIMESTAMP as nowtime;
        ";

    $result = pg_query($objDB->ConnectID, $sql);

    if (!$result) {
        echo "現在時刻取得失敗しました。\n";
        exit;
    } else {
        return pg_fetch_object($result)->nowtime;
    }
}

/**
 *発注書件数を取得する
 *
 * @param [object] $objDB
 * @param [string] $lcgetdate
 * @return 発注件数
 */
function fncGetPurchaseOrderCount($objDB, $lcgetdate)
{
    $sql = "
        select count(*)
        from m_purchaseorder
        where dtmInsertDate > $1
    ";
    //バインドの設定
    $bind = array($lcgetdate);

    $result = pg_query_params($objDB->ConnectID, $sql, $bind);

    if (!$result) {
        echo "発注書件数取得失敗しました。\n";
        exit;
    } else {
        return pg_fetch_object($result)->count;
    }
}

// /**
//  *発注件数を取得する
//  *
//  * @param [object] $objDB
//  * @param [string] $lcgetdate
//  * @return 発注件数
//  */
// function fncGetOrderCount($objDB, $lcgetdate)
// {
//     $sql = "
//         select count(*)
//         from m_order
//         where dtmInsertDate > $1
//     ";
//     //バインドの設定
//     $bind = array($lcgetdate);

//     $result = pg_query_params($objDB->ConnectID, $sql, $bind);

//     if (!$result) {
//         echo "発注件数取得失敗しました。\n";
//         exit;
//     } else {
//         return pg_fetch_object($result)->count;
//     }
// }
/**
 * 発注書データを取得する
 *
 * @param [object] $objDB
 * @param [string] $date
 * @return 発注データリスト
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
          lngpurchaseorderno
        , lngrevisionno
    ";
    //バインドの設定
    $bind = array($date);
    
    $result = pg_query_params($objDB->ConnectID, $sql, $bind);

    if (!$result) {
        echo "発注書データ取得失敗しました。\n";
        exit;
    } else {
        return pg_fetch_all($result);
    }
}
// /**
//  * 発注データを取得する
//  *
//  * @param [object] $objDB
//  * @param [string] $date
//  * @return 発注データリスト
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
//     //バインドの設定
//     $bind = array($date);

//     $result = pg_query_params($objDB->ConnectID, $sql, $bind);

//     if (!$result) {
//         echo "発注データ取得失敗しました。\n";
//         exit;
//     } else {
//         return pg_fetch_all($result);
//     }
// }

/**
 * 発注書明細データを取得する
 *
 * @param [object] $objDB
 * @param [integer] $lngpurchaseorderno
 * @param [integer] $lngrevisionno
 * @return 発注明細リスト
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
    //バインドの設定
    $bind = array($lngpurchaseorderno, $lngrevisionno);

    $result = pg_query_params($objDB->ConnectID, $sql, $bind);

    if (!$result) {
        echo "発注書明細データ取得失敗しました。\n";
        exit;
    } else {
        return pg_fetch_all($result);
    }

}

/**
 * 発注明細データを取得する
 *
 * @param [object] $objDB
 * @param [integer] $lngorderno
 * @param [integer] $lngrevisionno
 * @return 発注明細リスト
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
    //バインドの設定
    $bind = array($lngorderno, $lngrevisionno);

    $result = pg_query_params($objDB->ConnectID, $sql, $bind);

    if (!$result) {
        echo "発注明細データ取得失敗しました。\n";
        exit;
    } else {
        return pg_fetch_all($result);
    }

}

/**
 * 納品場所と荷揚地を取得する
 *
 * @param [object] $objDB
 * @param [integer] $lngcompanycode
 * @return 納品場所と荷揚地
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
    //バインドの設定
    $bind = array($lngcompanycode);

    $result = pg_query_params($objDB->ConnectID, $sql, $bind);

    if (!$result) {
        echo "納品場所名称と荷揚地取得失敗しました。\n";
        exit;
    } else {
        return pg_fetch_object($result);
    }

}

// /**
//  * 発注コードにより発注データを取得する
//  *
//  * @param [object] $objDB
//  * @param [string] $date
//  * @return 発注データリスト
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
//     //バインドの設定
//     $bind = array($pono, $poreviseno);

//     $result = pg_query_params($objDB->ConnectID, $sql, $bind);

//     if (!$result) {
//         echo "発注コードにより発注データ取得失敗しました。\n";
//         exit;
//     } else {
//         return pg_fetch_all($result);
//     }
// }

/**
 * 削除復活発注書データを取得する
 *
 * @param [object] $objDB
 * @return 発注コードリスト
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
        echo "削除復活発注書データ取得失敗しました。\n";
        exit;
    } else {
        return pg_fetch_all($result);
    }
}

// /**
//  * 削除復活発注データを取得する
//  *
//  * @param [object] $objDB
//  * @return 発注コードリスト
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
//         echo "削除復活発注データ取得失敗しました。\n";
//         exit;
//     } else {
//         return pg_fetch_all($result);
//     }
// }

/**
 * 通貨別PO番号別の合計金額取得
 *
 * @param [object] $objDB
 * @param [string] $shipym
 * @param [string] $currencyclass
 * @return array
 */
function fncGetSumOfMoneypriceByPono($objDB, $shipym, $currencyclass)
{
    //クエリの生成
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
        echo "通貨別PO番号別の合計金額取得取得失敗しました。\n";
        exit;
    } else {
        return pg_fetch_all($result);
    }
}

/**
 * 通貨別の合計金額取得
 *
 * @param [object] $objDB
 * @param [string] $shipym
 * @param [string] $currencyclass
 * @return array
 */
function fncGetSumOfMoneyprice($objDB, $shipym, $currencyclass)
{
    //クエリの生成
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
        echo "通貨別の合計金額取得取得失敗しました。\n";
        exit;
    } else {
        return pg_fetch_object($result)->totalmoneyprice;
    }
}

/**
 * 通貨別銀行別の合計金額取得
 *
 * @param [object] $objDB
 * @param [string] $shipym
 * @param [string] $currencyclass
 * @return array
 */
function fncGetSumOfMoneypriceByBankname($objDB, $shipym, $currencyclass)
{
    //クエリの生成
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
        echo "通貨別銀行別の合計金額取得取得失敗しました。\n";
        exit;
    } else {
        return pg_fetch_all($result);
    }
}

/**
 * L/C情報の発行銀行の更新
 *
 * @param [object] $objDB
 * @param [string] $bankcd
 * @param [string] $bankname
 * @param [string] $currencyclass
 * @param [string] $pono
 * @return 更新件数
 */
function fncUpdateBankname($objDB, $bankcd, $bankname, $currencyclass, $pono)
{
    //クエリの生成
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
        echo "L/C情報の発行銀行の更新失敗しました。\n";
        exit;
    } else {
        return pg_affected_rows($result);
    }

}

/**
 * L/C情報の更新
 *
 * @param [object] $objDB
 * @param [array] $data
 * @return 更新件数
 */
function fncUpdateLcinfo($objDB, $data)
{
    // 同じPONO,POREVISENOの更新
    //クエリの生成
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
        $data["lcstate"],$data["pono"], $data["poreviseno"],
    );

    $result = pg_query_params($objDB->ConnectID, $sql, $bind);

    if (!$result) {
        echo "L/C情報の更新失敗しました。\n";
        exit;
    } else {
        return pg_affected_rows($result);
    }
}

/**
 * L/C情報の更新
 *
 * @param [object] $objDB
 * @param [array] $data
 * @return 更新件数
 */
function fncUpdateSettleInfo($objDB, $data) {
    //クエリの生成
    $sql = "
                update t_lcinfo
                set lcno = $1,
                    usancesettlement = $2,
                    bldetail1date = $3,
                    bldetail1money = $4,
                    bldetail2date = $5,
                    bldetail2money = $6,
                    bldetail3date = $7,
                    bldetail3money = $8
                where
                    pono = $9
                    and polineno = $10
                    and poreviseno = $11
            ";
    $bind = array(($data["lcno"] == "") ? null : $data["lcno"],
        ($data["usancesettlement"] == "") ? null : str_replace(",", "", $data["usancesettlement"]),
        ($data["bldetail1date"] == "") ? null : date($data["bldetail1date"]),
        ($data["bldetail1money"] == "") ? null : str_replace(",", "", $data["bldetail1money"]),
        ($data["bldetail2date"] == "") ? null : date($data["bldetail2date"]),
        ($data["bldetail2money"] == "") ? null : str_replace(",", "", $data["bldetail2money"]),
        ($data["bldetail3date"] == "") ? null : date($data["bldetail3date"]),
        ($data["bldetail3money"] == "") ? null : str_replace(",", "", $data["bldetail3money"]),
        $data["pono"], $data["polineno"], $data["poreviseno"],
    );

    $result = pg_query_params($objDB->ConnectID, $sql, $bind);

    if (!$result) {
        echo "L/C情報の決済金額の更新失敗しました。\n";
        exit;
    } else {
        return pg_affected_rows($result);
    }

}

/**
 * L/C情報のステータスをT/T解除への更新
 *
 * @param [object] $objDB
 * @param [array] $data
 * @return 更新件数
 */
function fncUpdateLcinfoToAmandCancel($objDB, $data)
{
    //クエリの生成
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
        echo "L/C情報のステータスをアメンド警告解除への更新失敗しました。\n";
        exit;
    } else {
        return pg_affected_rows($result);
    }

}

/**
 * L/C情報の状態の更新
 *
 * @param [object] $objDB
 * @param [array] $data
 * @return 更新件数
 */
function fncUpdateLcState($objDB, $data)
{
    //クエリの生成
    $sql = "
                update t_lcinfo
                set lcstate = $1
                where pono = $2
                and poreviseno = $3
            ";

    $bind = array($data["lcstate"], $data["pono"], $data["poreviseno"]);

    $result = pg_query_params($objDB->ConnectID, $sql, $bind);

    if (!$result) {
        echo "L/C情報の状態更新失敗しました。\n";
        exit;
    } else {
        return pg_affected_rows($result);
    }
}

/**
 * 支払先情報を取得する
 *
 * @param [type] $objDB
 * @return void
 */
function fncGetPayfInfo($objDB)
{
    //クエリの生成
    $sql = "
				select
					trim(payfcd) as payfcd
				   ,payfomitname
				   ,payfformalname
				   ,payfsendname
				   ,payfsendfax
				   ,invalidflag
				from
					m_payfinfo
				order by payfcd
            ";
    //バインドの設定
    $bind = array();

    $result = pg_query_params($objDB->ConnectID, $sql, $bind);

    if (!$result) {
        echo "支払先情報取得失敗しました。\n";
        exit;
    } else {
        return pg_fetch_all($result);
    }
}

/**
 * 銀行情報を取得する
 *
 * @param [type] $objDB
 * @return void
 */
function fncGetBankInfo($objDB)
{
    //クエリの生成
    $sql = "
				select
					trim(bankcd) as bankcd
				   ,bankomitname
				   ,bankformalname
				   ,bankdivrate
				   ,invalidflag
				from
					m_bank
				order by bankcd
            ";
    //バインドの設定
    $bind = array();
    $result = pg_query_params($objDB->ConnectID, $sql, $bind);

    if (!$result) {
        echo "銀行情報取得失敗しました。\n";
        exit;
    } else {
        return pg_fetch_all($result);
    }
}

/**
 * 銀行情報(ALL)を取得する
 *
 * @param [type] $objDB
 * @return void
 */
function fncGetBankAndAll($objDB)
{
    //クエリの生成
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
        echo "銀行情報(ALL)取得失敗しました。\n";
        exit;
    } else {
        return pg_fetch_all($result);
    }
}
/**
 * 銀行情報の削除を行う
 *
 * @param [object] $objDB
 * @return void 削除件数
 */
function fncDeleteBank($objDB)
{
    $sql = "
        delete from m_bank
        ";

    $result = pg_query($objDB->ConnectID, $sql);

    if (!$result) {
        echo "銀行マスタ情報削除失敗しました。\n";
        exit;
    }

    return pg_affected_rows($result);
}

/**
 * 支払先情報の削除を行う
 *
 * @param [object] $objDB
 * @return void 削除件数
 */
function fncDeletePayfinfo($objDB)
{
    $sql = "
        delete from m_payfinfo
        ";

    $result = pg_query($objDB->ConnectID, $sql);

    if (!$result) {
        echo "支払先マスタ情報削除失敗しました。\n";
        exit;
    }

    return pg_affected_rows($result);
}

/**
 * 送付元情報の削除を行う
 *
 * @param [object] $objDB
 * @return void 削除件数
 */
function fncDeleteSendinfo($objDB)
{
    $sql = "
        delete from m_sendinfo
        ";

    $result = pg_query($objDB->ConnectID, $sql);

    if (!$result) {
        echo "送付元マスタ情報削除失敗しました。\n";
        exit;
    }

    return pg_affected_rows($result);
}
/**
 * 銀行情報の登録を行う
 *
 * @param [object] $objDB
 * @param [array] $data
 * @return void 登録件数
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

    //バインドの設定
    $bind = array($data["bankcd"]
        , $data["bankomitname"]
        , $data["bankformalname"]
        , $data["bankdivrate"]
        , $data["invalidflag"]);

    $result = pg_query_params($objDB->ConnectID, $sql, $bind);

    if (!$result) {
        echo "銀行マスタ情報登録失敗しました。\n";
        exit;
    } else {
        return pg_affected_rows($result);
    }
}

/**
 * 支払先マスタ情報の登録を行う
 *
 * @param [object] $objDB
 * @param [array] $data
 * @return void 登録件数
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

        //バインドの設定
        $bind = array($data["payfcd"]
            , $data["payfomitname"]
            , $data["payfformalname"]
            , $data["payfsendname"]
            , $data["payfsendfax"]
            , $data["invalidflag"]);

        $result = pg_query_params($objDB->ConnectID, $sql, $bind);

        if (!$result) {
            echo "支払先マスタ情報登録失敗しました。\n";
            exit;
        } else {
            return pg_affected_rows($result);
        }
    }
}
/**
 * 送付元情報の登録を行う
 *
 * @param [object] $objDB
 * @param [array] $data
 * @return void 登録件数
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

    //バインドの設定
    $bind = array($data["sendno"]
        , $data["sendfromname"]
        , $data["sendfromfax"]
        , $data["sendcarenote1"]
        , $data["sendcarenote2"],
    );
    $result = pg_query_params($objDB->ConnectID, $sql, $bind);

    if (!$result) {
        echo "送付元マスタ情報登録失敗しました。\n";
        exit;
    } else {
        return pg_affected_rows($result);
    }
}

/**
 * 銀行マスタより有効データを取得する
 *
 * @return array
 */
function fncGetValidBankInfo($objDB)
{
    //クエリの生成
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
        echo "銀行マスタより有効データ取得失敗しました。\n";
        exit;
    } else {
        return pg_fetch_all($result);
    }

}
