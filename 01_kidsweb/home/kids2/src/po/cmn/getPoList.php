<?php

include 'conf.inc';
require LIB_FILE;
require SRC_ROOT . "pc/cmn/lib_pc.php";
include 'JSON.php';

$objDB = new clsDB();
$objAuth = new clsAuth();
$objDB->open("", "", "", "");
//JSONクラスインスタンス化
$s = new Services_JSON();

$json_string = file_get_contents('php://input');
$condition = json_decode($json_string, true);

if (!$condition) {
    echo "無効な値が指定されました。";
    exit;
}

$_REQUEST = array_merge($_REQUEST, $condition);

// セッション確認
$objAuth = fncIsSession($_REQUEST["strSessionID"], $objAuth, $objDB);

// 明細行のないPOリスト
$aryQuery[] = "select";
$aryQuery[] = "  distinct mpo.strordercode as strpocode";
$aryQuery[] = "  , mc.strcompanydisplayname";
$aryQuery[] = "  , mpo.lngpurchaseorderno";
$aryQuery[] = "  , mpo.lngrevisionno";
$aryQuery[] = "  , 0 as detailnum";
$aryQuery[] = "from";
$aryQuery[] = "  m_purchaseorder mpo ";
$aryQuery[] = "  inner join ( ";
$aryQuery[] = "    select";
$aryQuery[] = "      lngpurchaseorderno";
$aryQuery[] = "      , max(lngrevisionno) lngrevisionno ";
$aryQuery[] = "    from";
$aryQuery[] = "      m_purchaseorder ";
$aryQuery[] = "    group by";
$aryQuery[] = "      lngpurchaseorderno";
$aryQuery[] = "  ) max_mpo ";
$aryQuery[] = "    on mpo.lngpurchaseorderno = max_mpo.lngpurchaseorderno ";
$aryQuery[] = "    and mpo.lngrevisionno = max_mpo.lngrevisionno ";
$aryQuery[] = "  inner join ( ";
$aryQuery[] = "    select";
$aryQuery[] = "      strproductcode";
$aryQuery[] = "      , strrevisecode ";
$aryQuery[] = "    from";
$aryQuery[] = "      m_estimate ";
$aryQuery[] = "    where";
$aryQuery[] = "      lngestimateno = " . $_REQUEST["lngestimateno"] . "";
$aryQuery[] = "      and lngrevisionno = " . $_REQUEST["lngestimaterevisionno"] . "";
$aryQuery[] = "  ) me ";
$aryQuery[] = "    on mpo.strproductcode = me.strproductcode ";
$aryQuery[] = "    and mpo.strrevisecode = me.strrevisecode ";
$aryQuery[] = "  left join t_purchaseorderdetail tpod ";
$aryQuery[] = "    on mpo.lngpurchaseorderno = tpod.lngpurchaseorderno ";
$aryQuery[] = "    and mpo.lngrevisionno = tpod.lngrevisionno ";
$aryQuery[] = "  left join t_purchaseorderdetail pre_tpod ";
$aryQuery[] = "    on mpo.lngpurchaseorderno = pre_tpod.lngpurchaseorderno ";
$aryQuery[] = "    and (mpo.lngrevisionno - 1) = pre_tpod.lngrevisionno ";
$aryQuery[] = "  LEFT JOIN m_stockitem msi ";
$aryQuery[] = "    on pre_tpod.lngstocksubjectcode = msi.lngstocksubjectcode ";
$aryQuery[] = "    and pre_tpod.lngstockitemcode = msi.lngstockitemcode ";
$aryQuery[] = "  left join m_company mc";
$aryQuery[] = "  on mpo.lngcustomercode = mc.lngcompanycode";
$aryQuery[] = "where";
$aryQuery[] = "  tpod.lngpurchaseorderdetailno is null ";
$aryQuery[] = "  and not exists ( ";
$aryQuery[] = "    select";
$aryQuery[] = "      lngpurchaseorderno ";
$aryQuery[] = "    from";
$aryQuery[] = "      m_purchaseorder mpo1 ";
$aryQuery[] = "    where";
$aryQuery[] = "      mpo1.lngpurchaseorderno = mpo.lngpurchaseorderno ";
$aryQuery[] = "      and mpo1.lngrevisionno < 0";
$aryQuery[] = "  ) ";
$aryQuery[] = "  and exists ( ";
$aryQuery[] = "    select distinct";
$aryQuery[] = "      msi1.lngestimateareaclassno";
$aryQuery[] = "      , mo.lngcustomercompanycode ";
$aryQuery[] = "    from";
$aryQuery[] = "      t_orderdetail tod ";
$aryQuery[] = "      inner join m_order mo ";
$aryQuery[] = "        on mo.lngorderno = tod.lngorderno ";
$aryQuery[] = "        and mo.lngrevisionno = tod.lngrevisionno ";
$aryQuery[] = "      inner join ( ";
$aryQuery[] = "        select";
$aryQuery[] = "          lngorderno";
$aryQuery[] = "          , max(lngrevisionno) lngrevisionno ";
$aryQuery[] = "        from";
$aryQuery[] = "          m_order ";
$aryQuery[] = "        group by";
$aryQuery[] = "          lngorderno";
$aryQuery[] = "      ) max_mo ";
$aryQuery[] = "        on tod.lngorderno = max_mo.lngorderno ";
$aryQuery[] = "        and tod.lngrevisionno = max_mo.lngrevisionno ";
$aryQuery[] = "      LEFT JOIN m_stockitem msi1 ";
$aryQuery[] = "        on tod.lngstocksubjectcode = msi1.lngstocksubjectcode ";
$aryQuery[] = "        and tod.lngstockitemcode = msi1.lngstockitemcode ";
$aryQuery[] = "    where";
$aryQuery[] = "      mo.lngcustomercompanycode = mpo.lngcustomercode ";
$aryQuery[] = "      and mo.lngmonetaryunitcode = mpo.lngmonetaryunitcode ";
$aryQuery[] = "      and msi.lngestimateareaclassno = msi1.lngestimateareaclassno ";
$aryQuery[] = "      and tod.lngorderno in (" . $_REQUEST["lngorderno"] . ")";
$aryQuery[] = "  ) ";
$aryQuery[] = "order by";
$aryQuery[] = "  mpo.strordercode";
$strQuery = implode("\n", $aryQuery);
list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);

$result = array();

if ($lngResultNum) {
    for ($i = 0; $i < $lngResultNum; $i++) {
        $result[] = $objDB->fetchArray($lngResultID, $i);
    }
}

$objDB->freeResult($lngResultID);


// 明細行のあるPOリスト
unset($aryQuery);
$aryQuery[] = "select";
$aryQuery[] = "  distinct mpo.strordercode as strpocode";
$aryQuery[] = "  , mc.strcompanydisplayname";
$aryQuery[] = "  , mpo.lngpurchaseorderno";
$aryQuery[] = "  , mpo.lngrevisionno";
$aryQuery[] = "  , num_tpod.detailnum ";
$aryQuery[] = "from";
$aryQuery[] = "  m_purchaseorder mpo ";
$aryQuery[] = "  inner join ( ";
$aryQuery[] = "    select";
$aryQuery[] = "      lngpurchaseorderno";
$aryQuery[] = "      , max(lngrevisionno) lngrevisionno ";
$aryQuery[] = "    from";
$aryQuery[] = "      m_purchaseorder ";
$aryQuery[] = "    group by";
$aryQuery[] = "      lngpurchaseorderno";
$aryQuery[] = "  ) max_mpo ";
$aryQuery[] = "    on mpo.lngpurchaseorderno = max_mpo.lngpurchaseorderno ";
$aryQuery[] = "    and mpo.lngrevisionno = max_mpo.lngrevisionno ";
$aryQuery[] = "  inner join ( ";
$aryQuery[] = "    select";
$aryQuery[] = "      strproductcode";
$aryQuery[] = "      , strrevisecode ";
$aryQuery[] = "    from";
$aryQuery[] = "      m_estimate ";
$aryQuery[] = "    where";
$aryQuery[] = "      lngestimateno = " . $_REQUEST["lngestimateno"] . "";
$aryQuery[] = "      and lngrevisionno = " . $_REQUEST["lngestimaterevisionno"] . "";
$aryQuery[] = "  ) me ";
$aryQuery[] = "    on mpo.strproductcode = me.strproductcode ";
$aryQuery[] = "    and mpo.strrevisecode = me.strrevisecode ";
$aryQuery[] = "  left join t_purchaseorderdetail tpod ";
$aryQuery[] = "    on mpo.lngpurchaseorderno = tpod.lngpurchaseorderno ";
$aryQuery[] = "    and mpo.lngrevisionno = tpod.lngrevisionno ";
$aryQuery[] = "  LEFT JOIN m_stockitem msi ";
$aryQuery[] = "    on tpod.lngstocksubjectcode = msi.lngstocksubjectcode ";
$aryQuery[] = "    and tpod.lngstockitemcode = msi.lngstockitemcode ";
$aryQuery[] = "  inner join ( ";
$aryQuery[] = "    select";
$aryQuery[] = "      tpod.lngpurchaseorderno";
$aryQuery[] = "      , tpod.lngrevisionno";
$aryQuery[] = "      , count(*) as detailnum ";
$aryQuery[] = "    from";
$aryQuery[] = "      t_purchaseorderdetail tpod ";
$aryQuery[] = "      inner join m_order mo ";
$aryQuery[] = "        on tpod.lngorderno = mo.lngorderno ";
$aryQuery[] = "        and tpod.lngorderrevisionno = mo.lngrevisionno ";
$aryQuery[] = "    where";
$aryQuery[] = "      mo.lngorderstatuscode < 4 ";
$aryQuery[] = "    group by";
$aryQuery[] = "      tpod.lngpurchaseorderno";
$aryQuery[] = "      , tpod.lngrevisionno";
$aryQuery[] = "  ) num_tpod ";
$aryQuery[] = "    on mpo.lngpurchaseorderno = num_tpod.lngpurchaseorderno ";
$aryQuery[] = "    and mpo.lngrevisionno = num_tpod.lngrevisionno ";
$aryQuery[] = "  left join m_company mc ";
$aryQuery[] = "    on mpo.lngcustomercode = mc.lngcompanycode ";
$aryQuery[] = "where";
$aryQuery[] = "  not exists ( ";
$aryQuery[] = "    select";
$aryQuery[] = "      lngpurchaseorderno ";
$aryQuery[] = "    from";
$aryQuery[] = "      m_purchaseorder mpo1 ";
$aryQuery[] = "    where";
$aryQuery[] = "      mpo1.lngpurchaseorderno = mpo.lngpurchaseorderno ";
$aryQuery[] = "      and mpo1.lngrevisionno < 0";
$aryQuery[] = "  ) ";
$aryQuery[] = "  and exists ( ";
$aryQuery[] = "    select distinct";
$aryQuery[] = "      msi1.lngestimateareaclassno";
$aryQuery[] = "      , mo.lngcustomercompanycode ";
$aryQuery[] = "    from";
$aryQuery[] = "      t_orderdetail tod ";
$aryQuery[] = "      inner join m_order mo ";
$aryQuery[] = "        on mo.lngorderno = tod.lngorderno ";
$aryQuery[] = "        and mo.lngrevisionno = tod.lngrevisionno ";
$aryQuery[] = "      inner join ( ";
$aryQuery[] = "        select";
$aryQuery[] = "          lngorderno";
$aryQuery[] = "          , max(lngrevisionno) lngrevisionno ";
$aryQuery[] = "        from";
$aryQuery[] = "          m_order ";
$aryQuery[] = "        where";
$aryQuery[] = "          lngorderstatuscode < 4 ";
$aryQuery[] = "        group by";
$aryQuery[] = "          lngorderno";
$aryQuery[] = "      ) max_mo ";
$aryQuery[] = "        on tod.lngorderno = max_mo.lngorderno ";
$aryQuery[] = "        and tod.lngrevisionno = max_mo.lngrevisionno ";
$aryQuery[] = "      LEFT JOIN m_stockitem msi1 ";
$aryQuery[] = "        on tod.lngstocksubjectcode = msi1.lngstocksubjectcode ";
$aryQuery[] = "        and tod.lngstockitemcode = msi1.lngstockitemcode ";
$aryQuery[] = "    where";
$aryQuery[] = "      mo.lngcustomercompanycode = mpo.lngcustomercode ";
$aryQuery[] = "      and mo.lngmonetaryunitcode = mpo.lngmonetaryunitcode ";
$aryQuery[] = "      and msi.lngestimateareaclassno = msi1.lngestimateareaclassno ";
$aryQuery[] = "      and tod.lngorderno in (" . $_REQUEST["lngorderno"] . ")";
$aryQuery[] = "  ) ";
$aryQuery[] = "order by";
$aryQuery[] = "  mpo.strordercode";
$strQuery = implode("\n", $aryQuery);

list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);

if ($lngResultNum) {
    for ($i = 0; $i < $lngResultNum; $i++) {
        $result[] = $objDB->fetchArray($lngResultID, $i);
    }
}

$objDB->freeResult($lngResultID);

$objDB->close();

//結果出力
echo $s->encodeUnsafe($result);
