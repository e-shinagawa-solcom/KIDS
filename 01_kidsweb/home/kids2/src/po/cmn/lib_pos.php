<?
// ----------------------------------------------------------------------------
/**
 *       発注管理  検索関連関数群
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
 *         ・検索結果関連の関数
 *
 *       更新履歴
 *
 */
// ----------------------------------------------------------------------------

/**
 * 検索項目から一致する最新の発注データを取得するSQL文の作成関数
 *
 *    検索項目から SQL文を作成する
 *
 *    @param  Array     $displayColumns             表示対象カラム名の配列
 *    @param  Array     $searchColumns         検索対象カラム名の配列
 *    @param  Array     $from     検索内容(from)の配列
 *    @param  Array     $to       検索内容(to)の配列
 *    @param  Array     $searchValue  検索内容の配列
 *    @return Array     $strSQL 検索用SQL文
 *    @access public
 */
function fncGetSearchPurchaseSQL($displayColumns, $searchColumns, $from, $to, $searchValue, $optionColumns)
{
    $detailConditionCount = 0;
    // クエリの組立て
    $aryQuery = array();
    $aryQuery[] = "SELECT";
    // $aryQuery[] = "  distinct ";
    $aryQuery[] = "  o.lngOrderNo as lngOrderNo";
    $aryQuery[] = "  , o.lngOrderNo as lngpkno";
    $aryQuery[] = "  , o.lngRevisionNo as lngRevisionNo";
    $aryQuery[] = "  , od.lngOrderDetailNo";
    $aryQuery[] = "  , od.lngOrderDetailNo as lngdetailno";
    $aryQuery[] = "  , od.lngestimateno as lngestimateno";
    $aryQuery[] = "  , od.estimaterevisionno as estimaterevisionno";
    $aryQuery[] = "  , o.strOrderCode as strOrderCode";
    $aryQuery[] = "  , o.strOrderCode || '_' || to_char(o.lngRevisionNo, 'FM00') as strOrderCode_desc";
    $aryQuery[] = "  , to_char(o.dtmInsertDate, 'YYYY/MM/DD HH24:MI:SS') as dtmInsertDate";
    $aryQuery[] = "  , input_u.strUserDisplayCode as strInputUserDisplayCode";
    $aryQuery[] = "  , input_u.strUserDisplayName as strInputUserDisplayName";
    $aryQuery[] = "  , cust_c.strCompanyDisplayCode as strCustomerDisplayCode";
    $aryQuery[] = "  , cust_c.strCompanyDisplayName as strCustomerDisplayName";
    $aryQuery[] = "  , o.lngOrderStatusCode as lngOrderStatusCode";
    $aryQuery[] = "  , os.strOrderStatusName as strOrderStatusName";
    $aryQuery[] = "  , mm.lngMonetaryUnitCode as lngMonetaryUnitCode";
    $aryQuery[] = "  , mm.strMonetaryUnitSign as strMonetaryUnitSign";
    $aryQuery[] = "  , od.strProductCode";
    $aryQuery[] = "  , od.strProductName";
    $aryQuery[] = "  , od.strProductEnglishName";
    $aryQuery[] = "  , od.lngInChargeGroupCode as strgroupdisplaycode";
    $aryQuery[] = "  , od.strInChargeGroupName as strgroupdisplayname";
    $aryQuery[] = "  , od.lngInChargeUserCode as struserdisplaycode";
    $aryQuery[] = "  , od.strInChargeUserName as struserdisplayname";
    $aryQuery[] = "  , od.lngStockSubjectCode";
    $aryQuery[] = "  , od.strStockSubjectName";
    $aryQuery[] = "  , od.lngStockItemCode";
    $aryQuery[] = "  , od.strstockitemname";
    $aryQuery[] = "  , od.dtmDeliveryDate";
    $aryQuery[] = "  , od.strmoldno";
    $aryQuery[] = "  , od.curProductPrice";
    $aryQuery[] = "  , od.lngProductQuantity";
    $aryQuery[] = "  , od.lngproductunitcode";
    $aryQuery[] = "  , od.strproductunitname";
    $aryQuery[] = "  , od.curSubTotalPrice";
    $aryQuery[] = "  , od.strDetailNote ";
    $aryQuery[] = "FROM";
    $aryQuery[] = "  m_Order o ";
    $aryQuery[] = "  INNER JOIN ( ";
    $aryQuery[] = "    select";
    $aryQuery[] = "      lngorderno";
    $aryQuery[] = "      , MAX(lngrevisionno) as lngrevisionno ";
    $aryQuery[] = "    from";
    $aryQuery[] = "      m_order ";
    $aryQuery[] = "    group by";
    $aryQuery[] = "      lngorderno";
    $aryQuery[] = "  ) rev ";
    $aryQuery[] = "    on rev.lngorderno = o.lngorderno ";
    $aryQuery[] = "    and rev.lngrevisionno = o.lngrevisionno ";
    $aryQuery[] = "  LEFT JOIN m_User input_u ";
    $aryQuery[] = "    ON o.lngInputUserCode = input_u.lngUserCode ";
    $aryQuery[] = "  LEFT JOIN m_Company cust_c ";
    $aryQuery[] = "    ON o.lngCustomerCompanyCode = cust_c.lngCompanyCode ";
    $aryQuery[] = "  LEFT JOIN m_OrderStatus os ";
    $aryQuery[] = "    USING (lngOrderStatusCode) ";
    $aryQuery[] = "  LEFT JOIN m_MonetaryUnit mm ";
    $aryQuery[] = "    ON o.lngMonetaryUnitCode = mm.lngMonetaryUnitCode";
    $aryQuery[] = "  , ( ";
    $aryQuery[] = "      select";
    $aryQuery[] = "        od1.lngorderno";
    $aryQuery[] = "        , od1.lngorderDetailNo";
    $aryQuery[] = "        , od1.lngRevisionNo";
    $aryQuery[] = "        , od1.strProductCode || '_' || od1.strReviseCode as strProductCode";
    $aryQuery[] = "        , od1.lngestimateno as lngestimateno";
    $aryQuery[] = "        , mp.strProductName as strProductName";
    $aryQuery[] = "        , mp.lngrevisionno as estimaterevisionno";
    $aryQuery[] = "        , mp.strProductEnglishName as strProductEnglishName";
    $aryQuery[] = "        , mg.strgroupdisplaycode as lngInChargeGroupCode";
    $aryQuery[] = "        , mg.strgroupdisplayname as strInChargeGroupName";
    $aryQuery[] = "        , mu.struserdisplaycode as lngInChargeUserCode";
    $aryQuery[] = "        , mu.struserdisplayname as strInChargeUserName";
    $aryQuery[] = "        , od1.lngStockSubjectCode as lngStockSubjectCode";
    $aryQuery[] = "        , ss.strStockSubjectName as strStockSubjectName";
    $aryQuery[] = "        , od1.lngStockItemCode as lngStockItemCode";
    $aryQuery[] = "        , si.strstockitemname as strstockitemname";
    $aryQuery[] = "        , od1.lngproductunitcode as lngproductunitcode";
    $aryQuery[] = "        , mpu.strproductunitname as strproductunitname";
    $aryQuery[] = "        , to_char(od1.dtmDeliveryDate, 'YYYY/MM/DD') as dtmDeliveryDate";
    $aryQuery[] = "        , od1.curProductPrice";
    $aryQuery[] = "        , od1.strmoldno";
    $aryQuery[] = "        , to_char(od1.lngProductQuantity, '9,999,999,990') as lngProductQuantity";
    $aryQuery[] = "        , od1.curSubTotalPrice";
    $aryQuery[] = "        , od1.strNote as strDetailNote ";
    $aryQuery[] = "      from";
    $aryQuery[] = "        t_orderdetail od1 ";
    $aryQuery[] = "      LEFT JOIN (";
    $aryQuery[] = "          SELECT m_product.* FROM m_product ";
    $aryQuery[] = "          INNER JOIN (";
    $aryQuery[] = "              SELECT ";
    $aryQuery[] = "                 lngproductno,strrevisecode,MAX(lngrevisionno) as lngrevisionno ";
    $aryQuery[] = "              FROM m_product GROUP BY lngproductno,strrevisecode";
    $aryQuery[] = "          ) mp1 ON mp1.lngproductno = m_product.lngproductno";
    $aryQuery[] = "              AND mp1.strrevisecode = m_product.strrevisecode";
    $aryQuery[] = "              AND mp1.lngrevisionno = m_product.lngrevisionno";
    $aryQuery[] = "      )mp ON  od1.strproductcode = mp.strproductcode ";
    $aryQuery[] = "          AND mp.strrevisecode = od1.strrevisecode ";
    $aryQuery[] = "        LEFT JOIN m_group mg ";
    $aryQuery[] = "          on mg.lnggroupcode = mp.lnginchargegroupcode ";
    $aryQuery[] = "        LEFT JOIN m_user mu ";
    $aryQuery[] = "          on mu.lngusercode = mp.lnginchargeusercode ";
    $aryQuery[] = "        LEFT JOIN m_productunit mpu ";
    $aryQuery[] = "          ON od1.lngproductunitcode = mpu.lngproductunitcode ";
    $aryQuery[] = "        LEFT JOIN m_StockSubject ss ";
    $aryQuery[] = "          ON od1.lngstocksubjectcode = ss.lngstocksubjectcode ";
    $aryQuery[] = "        LEFT JOIN m_stockitem si ";
    $aryQuery[] = "          ON od1.lngstockitemcode = si.lngstockitemcode ";
    $aryQuery[] = "          AND od1.lngstocksubjectcode = si.lngstocksubjectcode";

    //製品コード
    if (array_key_exists("strProductCode", $searchColumns) &&
        array_key_exists("strProductCode", $searchValue)) {
        $detailConditionCount += 1;
        $aryQuery[] = $detailConditionCount == 1 ? "WHERE " : "AND ";
        $strProductCodeArray = explode(",", $searchValue["strProductCode"]);
        $aryQuery[] = " (";
        $count = 0;
        foreach ($strProductCodeArray as $strProductCode) {
            $count += 1;
            if ($count != 1) {
                $aryQuery[] = " OR ";
            }
            if (strpos($strProductCode, '-') !== false) {
                $aryQuery[] = "(od1.strProductCode" .
                " between '" . explode("-", $strProductCode)[0] . "'" .
                " AND " . "'" . explode("-", $strProductCode)[1] . "')";
            } else {
                if (strpos($strProductCode, '_') !== false) {
                    $aryQuery[] = "od1.strProductCode = '" . explode("_", $strProductCode)[0] . "'";
                    $aryQuery[] = " AND od1.strrevisecode = '" . explode("_", $strProductCode)[1] . "'";
                } else {
                    $aryQuery[] = "od1.strProductCode = '" . $strProductCode . "'";
                }
            }
        }
        $aryQuery[] = ")";
    }
    // 製品名称（日本語）
    if (array_key_exists("strProductName", $searchColumns) &&
        array_key_exists("strProductName", $searchValue)) {
        $detailConditionCount += 1;
        $aryQuery[] = $detailConditionCount == 1 ? "WHERE " : "AND ";
        // $aryQuery[] = "UPPER( mp.strProductName ) LIKE UPPER( '%" . $searchValue["strProductName"] . "%' ) ";        
        $aryQuery[] = "sf_translate_case(mp.strproductname) like '%' || sf_translate_case('".pg_escape_string($searchValue["strProductName"])."') || '%'";
    }
    // 製品名称（英語）
    if (array_key_exists("strProductEnglishName", $searchColumns) &&
        array_key_exists("strProductEnglishName", $searchValue)) {
        $detailConditionCount += 1;
        $aryQuery[] = $detailConditionCount == 1 ? "WHERE " : "AND ";
        // $aryQuery[] = "UPPER( mp.strProductEnglishName ) LIKE UPPER( '%" . $searchValue["strProductEnglishName"] . "%' ) ";
        $aryQuery[] = "sf_translate_case(mp.strProductEnglishName) like '%' || sf_translate_case('".pg_escape_string($searchValue["strProductEnglishName"])."') || '%'";
    }

    // 部門
    if (array_key_exists("lngInChargeGroupCode", $searchColumns) &&
        array_key_exists("lngInChargeGroupCode", $searchValue)) {
        $detailConditionCount += 1;
        $aryQuery[] = $detailConditionCount == 1 ? "WHERE " : "AND ";
        $aryQuery[] = " mg.strGroupDisplayCode = '" . $searchValue["lngInChargeGroupCode"] . "'";

    }
    // 担当者
    if (array_key_exists("lngInChargeUserCode", $searchColumns) &&
        array_key_exists("lngInChargeUserCode", $searchValue)) {
        $detailConditionCount += 1;
        $aryQuery[] = $detailConditionCount == 1 ? "WHERE " : "AND ";
        $aryQuery[] = " mu.strUserDisplayCode = '" . $searchValue["lngInChargeUserCode"] . "'";

    }

    // 仕入科目
    if (array_key_exists("lngStockSubjectCode", $searchColumns) &&
        array_key_exists("lngStockSubjectCode", $searchValue)) {
        $detailConditionCount += 1;
        $aryQuery[] = $detailConditionCount == 1 ? "WHERE " : "AND ";
        $aryQuery[] = "od1.lngStockSubjectCode = " . $searchValue["lngStockSubjectCode"] . " ";
    }
    // 仕入部品
    if (array_key_exists("lngStockItemCode", $searchColumns) &&
        array_key_exists("lngStockItemCode", $searchValue)) {
        $detailConditionCount += 1;
        $aryQuery[] = $detailConditionCount == 1 ? "WHERE " : "AND ";

        $aryQuery[] = "od1.lngStockSubjectCode = " . explode("-", $searchValue["lngStockItemCode"])[0] . " ";
        $aryQuery[] = " AND od1.lngStockItemCode = " . explode("-", $searchValue["lngStockItemCode"])[1] . " ";
    }

    // 納期
    if (array_key_exists("dtmDeliveryDate", $searchColumns) &&
        array_key_exists("dtmDeliveryDate", $from) && $from["dtmDeliveryDate"] != '') {
        $detailConditionCount += 1;
        $aryQuery[] = $detailConditionCount == 1 ? "WHERE " : "AND ";
        $aryQuery[] = "od1.dtmDeliveryDate >= '" . $from["dtmDeliveryDate"] . "' ";
    }
    if (array_key_exists("dtmDeliveryDate", $searchColumns) &&
        array_key_exists("dtmDeliveryDate", $to) && $to["dtmDeliveryDate"] != '') {
        $detailConditionCount += 1;
        $aryQuery[] = $detailConditionCount == 1 ? "WHERE " : "AND ";
        $aryQuery[] = "od1.dtmDeliveryDate <= '" . $to["dtmDeliveryDate"] . "' ";
    }
    $aryQuery[] = "    ) od ";
    $aryQuery[] = "WHERE";
    $aryQuery[] = "  od.lngorderno = o.lngorderno ";
    $aryQuery[] = "  AND od.lngRevisionNo = o.lngRevisionNo ";

    // ////発注マスタ内の検索条件////
    // 登録日
    if (array_key_exists("dtmInsertDate", $searchColumns) &&
        array_key_exists("dtmInsertDate", $from) && $from["dtmInsertDate"] != '') {
        $dtmSearchDate = $from["dtmInsertDate"] . " 00:00:00";
        $aryQuery[] = " AND o.dtmInsertDate >= '" . $dtmSearchDate . "'";
    }
    if (array_key_exists("dtmInsertDate", $searchColumns) &&
        array_key_exists("dtmInsertDate", $to) && $to["dtmInsertDate"] != '') {
        $dtmSearchDate = $to["dtmInsertDate"] . " 23:59:59.99999";
        $aryQuery[] = " AND o.dtmInsertDate <= '" . $dtmSearchDate . "'";
    }

    // 計上日
    if (array_key_exists("dtmAppropriation", $searchColumns) &&
        array_key_exists("dtmAppropriation", $from) && $from["dtmAppropriation"] != '') {
        $dtmSearchDate = $from["dtmOrderAppDateFrom"] . " 00:00:00";
        $aryQuery[] = " AND o.dtmAppropriationDate >= '" . $dtmSearchDate . "'";
    }
    if (array_key_exists("dtmAppropriation", $searchColumns) &&
        array_key_exists("dtmAppropriation", $to) && $to["dtmAppropriation"] != '') {
        $dtmSearchDate = $to["dtmAppropriation"] . " 23:59:59.99999";
        $aryQuery[] = " AND o.dtmAppropriationDate <= '" . $dtmSearchDate . "'";
    }
    // 発注Ｎｏ
    if (array_key_exists("strOrderCode", $searchColumns) &&
        array_key_exists("strOrderCode", $searchValue)) {
        $strOrdereCodeArray = explode(",", $searchValue["strOrderCode"]);
        $aryQuery[] = " AND (";
        $count = 0;
        foreach ($strOrdereCodeArray as $strOrderCode) {
            $count += 1;
            if ($count != 1) {
                $aryQuery[] = " OR ";
            }
            if (strpos($strOrderCode, '-') !== false) {
                $aryQuery[] = "(o.strOrderCode" .
                " between '" . explode("-", $strOrderCode)[0] . "'" .
                " AND " . "'" . explode("-", $strOrderCode)[1] . "')";
            } else {
                if (strpos($strOrderCode, '_') !== false) {
                    if (explode("_", $strOrderCode)[1] == '00') {
                        $aryQuery[] = " o.strOrderCode = '" . explode("_", $strOrderCode)[0] . "'";
                        $aryQuery[] = " AND o.lngrevisionno = 0 ";
                    } else {
                        $aryQuery[] = " o.strOrderCode = '" . explode("_", $strOrderCode)[0] . "'";
                        $aryQuery[] = " AND o.lngrevisionno = " . ltrim(explode("_", $strOrderCode)[1], '0') . "";
                    }
                } else {
                    $aryQuery[] = "o.strOrderCode = '" . $strOrderCode . "'";
                }
            }
        }
        $aryQuery[] = ")";
    }

    // 入力者
    if (array_key_exists("lngInputUserCode", $searchColumns) &&
        array_key_exists("lngInputUserCode", $searchValue)) {
        $aryQuery[] = " AND input_u.strUserDisplayCode ~* '" . $searchValue["lngInputUserCode"] . "'";
    }
    // 仕入先
    if (array_key_exists("lngCustomerCode", $searchColumns) &&
        array_key_exists("lngCustomerCode", $searchValue)) {
        $aryQuery[] = " AND cust_c.strCompanyDisplayCode ~* '" . $searchValue["lngCustomerCode"] . "'";
    }
    // 状態
    if (array_key_exists("lngOrderStatusCode", $searchColumns) &&
        array_key_exists("lngOrderStatusCode", $searchValue)) {
        // 発注状態は ","区切りの文字列として渡される
        //$arySearchStatus = explode( ",", $arySearchDataColumn["lngOrderStatusCode"] );
        // チェックボックス化により、配列をそのまま代入
        $arySearchStatus = $searchValue["lngOrderStatusCode"];

        if (is_array($arySearchStatus)) {
            $aryQuery[] = " AND ( ";
            // 発注状態は複数設定されている可能性があるので、設定個数分ループ
            for ($j = 0; $j < count($arySearchStatus); $j++) {
                // 初回処理
                if ($j != 0) {
                    $aryQuery[] = " OR ";
                }
                $aryQuery[] = "o.lngOrderStatusCode = " . $arySearchStatus[$j] . "";
            }
            $aryQuery[] = " ) ";
        }
    }
    // 支払条件
    if (array_key_exists("lngPayConditionCode", $searchColumns) &&
        array_key_exists("lngPayConditionCode", $searchValue)) {
        $aryQuery[] = " AND o.lngPayConditionCode = " . $searchValue["lngPayConditionCode"] . "";
    }
    if (!array_key_exists("admin", $optionColumns)) {
        $aryQuery[] = "  AND not exists ( ";
        $aryQuery[] = "    select";
        $aryQuery[] = "      lngorderno ";
        $aryQuery[] = "    from";
        $aryQuery[] = "      m_Order ";
        $aryQuery[] = "    where";
        $aryQuery[] = "      lngorderno = o.lngorderno ";
        $aryQuery[] = "      and lngrevisionno < 0";
        $aryQuery[] = "  ) ";
    } else {
        $aryQuery[] = " AND o.bytInvalidFlag = FALSE ";
        $aryQuery[] = " AND o.lngRevisionNo >= 0";
    }
    $aryQuery[] = "ORDER BY";
    $aryQuery[] = "  o.dtmInsertDate DESC";

    return implode("\n", $aryQuery);
}

/**
 * 検索項目から一致する最新の発注書データを取得するSQL文の作成関数
 *
 *    検索項目から SQL文を作成する
 *
 *    @param  Array     $aryViewColumn             表示対象カラム名の配列
 *    @param  Array     $arySearchColumn         検索対象カラム名の配列
 *    @param  Array     $arySearchDataColumn     検索内容の配列
 *    @param  Object    $objDB                   DBオブジェクト
 *    @param    String    $strOrderCode            発注コード    空白指定時:検索結果出力    発注コード指定時:管理用、同じ発注コードの一覧取得
 *    @param    Integer    $lngOrderNo                発注Ｎｏ    0:検索結果出力    発注Ｎｏ指定時:管理用、同じ発注コードとする時の対象外発注NO
 *    @param    Boolean    $bytAdminMode            有効な削除データの取得用フラグ    FALSE:検索結果出力    TRUE:管理用、削除データ取得
 *    @return Array     $strSQL 検索用SQL文 OR Boolean FALSE
 *    @access public
 */
function fncGetSearchPurcheseOrderSQL($aryViewColumn, $arySearchColumn, $arySearchDataColumn, $objDB, $strOrderCode, $lngOrderNo, $bytAdminMode)
{
    $arySelectQuery = array();
    // 表示用カラムに設定されている内容を検索用に文字列設定
    for ($i = 0; $i < count($aryViewColumn); $i++) {
        $strViewColumnName = $aryViewColumn[$i];

        // 登録日
        if ($strViewColumnName == "dtmInsertDate") {
            $arySelectQuery[] = "  ,to_char(mp.dtminsertdate, 'YYYY/MM/DD HH24:MI:SS') as dtmInsertDate";
        }

        // 入力者
        if ($strViewColumnName == "lngInputUserCode") {
            $arySelectQuery[] = "  ,input_user.struserdisplaycode AS strinputuserdisplaycode";
            $arySelectQuery[] = "  ,mp.strinsertusername AS strinputuserdisplayname";
        }

        // 発注有効期限日
        if ($strViewColumnName == "dtmExpirationDate") {
            $arySelectQuery[] = "  ,to_char(mp.dtmexpirationdate, 'YYYY/MM/DD') as dtmExpirationDate";
        }

        // 発注NO.
        if ($strViewColumnName == "strOrderCode") {
            $arySelectQuery[] = "  ,mp.strordercode || '-' || lpad(to_char(mp.lngrevisionno, 'FM99'), 2, '0') as strOrderCode";
        }

        // 製品
        if ($strViewColumnName == "strProductCode") {
            $arySelectQuery[] = "  ,mp.strproductcode || '_' || mp.strrevisecode as strProductCode";
            $arySelectQuery[] = "  ,mp.strproductname as strProductName";
            $arySelectQuery[] = "  ,mp.strproductenglishname as strProductEnglishName";
        }

        // 営業部署
        if ($strViewColumnName == "lngInChargeGroupCode") {
            $arySelectQuery[] = "  ,mg.strgroupdisplaycode AS strgroupdisplaycode";
            $arySelectQuery[] = "  ,mp.strgroupname as strgroupdisplayname";
        }

        // 開発担当者
        if ($strViewColumnName == "lngInChargeUserCode") {
            $arySelectQuery[] = "  ,mu.struserdisplaycode as struserdisplaycode";
            $arySelectQuery[] = "  ,mp.strusername as struserdisplayname";
        }

        // 仕入先
        if ($strViewColumnName == "lngCustomerCode") {
            $arySelectQuery[] = "  ,mc_stock.strcompanydisplaycode as strcustomerdisplaycode";
            $arySelectQuery[] = "  ,mp.strcustomername as strcustomerdisplayname";
        }

        // 納品場所
        if ($strViewColumnName == "lngDeliveryPlaceCode") {
            $arySelectQuery[] = "  ,mc_delivary.strcompanydisplaycode as strdeliveryplacecode";
            $arySelectQuery[] = "  ,mp.strdeliveryplacename as strDeliveryPlaceName";
        }

        // 通貨
        if ($strViewColumnName == "lngMonetaryunitCode" or $strViewColumnName == "curTotalPrice") {
            $arySelectQuery[] = "  ,mp.lngmonetaryunitcode as lngMonetaryUnitCode";
            $arySelectQuery[] = "  ,mp.strmonetaryunitname as strmonetaryunitname";
            $arySelectQuery[] = "  ,mp.strmonetaryunitsign as strMonetaryUnitSign";
        }

        // 通貨レート
        if ($strViewColumnName == "lngMonetaryRateCode") {
            $arySelectQuery[] = "  ,mp.lngmonetaryratecode as lngMonetaryRateCode";
            $arySelectQuery[] = "  ,mp.strmonetaryratename as strMonetaryRateName";
        }

        // 支払条件
        if ($strViewColumnName == "lngPayConditionCode") {
            $arySelectQuery[] = "  ,mp.lngpayconditioncode as lngPayConditionCode";
            $arySelectQuery[] = "  ,mp.strpayconditionname as strPayConditionName";
        }

        // 合計金額
        if ($strViewColumnName == "curTotalPrice") {
            $arySelectQuery[] = "  ,mp.curtotalprice";
        }

        // 備考
        if ($strViewColumnName == "strNote") {
            $arySelectQuery[] = "  ,mp.strnote as strNote";
        }

        // 印刷回数
        if ($strViewColumnName == "lngPrintCount") {
            $arySelectQuery[] = "  ,mp.lngprintcount as lngPrintCount";
        }
        // 削除
        if ($strViewColumnName == "btnDelete") {
            $arySelectQuery[] = "  ,1 as btnDelete";
        }
    }

    $aryQuery[] = "WHERE mp.lngpurchaseorderno >= 0";
    if (!$bytAdminMode) {
        //管理者モード以外の場合は削除されたデータは対象としない
        $aryQuery[] = " AND mp.lngpurchaseorderno NOT IN (SELECT lngpurchaseorderno from m_purchaseorder where lngrevisionno < 0)";
    }
    // 検索用カラムに設定されている内容を検索条件に文字列設定
    for ($i = 0; $i < count($arySearchColumn); $i++) {
        $strSearchColumnName = $arySearchColumn[$i];

        // 発注書マスタの検索条件
        // 発注日
        if ($strSearchColumnName == "dtmInsertDate") {
            if ($arySearchDataColumn["dtmInsertDateFrom"]) {
                $dtmSearchDate = $arySearchDataColumn["dtmInsertDateFrom"] . " 00:00:00";
                $aryQuery[] = "AND   mp.dtminsertdate >= '" . $dtmSearchDate . "'";
            }
            if ($arySearchDataColumn["dtmInsertDateTo"]) {
                $dtmSearchDate = $arySearchDataColumn["dtmInsertDateTo"] . " 23:59:59.99999";
                $aryQuery[] = "AND   mp.dtminsertdate <= '" . $dtmSearchDate . "'";
            }
        }

        // 入力者
        if ($strSearchColumnName == "lngInputUserCode") {
            if ($arySearchDataColumn["lngInputUserCode"]) {
                $aryQuery[] = "AND   input_user.struserdisplaycode = '" . $arySearchDataColumn["lngInputUserCode"] . "'";
            }
        }

        // 発注有効期限
        if ($strSearchColumnName == "dtmExpirationDate") {
            if ($arySearchDataColumn["dtmExpirationDateFrom"]) {
                $dtmSearchDate = $arySearchDataColumn["dtmExpirationDateFrom"] . " 00:00:00";
                $aryQuery[] = "AND   mp.dtmexpirationdate >= '" . $dtmSearchDate . "'";
            }
            if ($arySearchDataColumn["dtmExpirationDateTo"]) {
                $dtmSearchDate = $arySearchDataColumn["dtmExpirationDateTo"] . " 23:59:59.99999";
                $aryQuery[] = "AND   mp.dtmexpirationdate <= '" . $dtmSearchDate . "'";
            }
        }

        // 発注NO.
        if ($strSearchColumnName == "strOrderCode") {
            $strOrderCodeArray = explode(",", $arySearchDataColumn["strOrderCode"]);
            $aryQuery[] = " AND (";
            $count = 0;
            foreach ($strOrderCodeArray as $strOrderCode) {
                $count += 1;
                if ($count != 1) {
                    $aryQuery[] = " OR ";
                }
                if (strpos($strOrderCode, '-') !== false) {
                    $aryQuery[] = "(mp.strordercode" .
                    " between '" . explode("-", $strOrderCode)[0] . "'" .
                    " AND " . "'" . explode("-", $strOrderCode)[1] . "')";
                } else {
                    if (strpos($strOrderCode, '_') !== false) {
                        $aryQuery[] = "mp.strordercode = '" . explode("_", $strOrderCode)[0] . "'";
                        if (explode("_", $strOrderCode)[1] == '00') {
                            $aryQuery[] = " AND mp.lngrevisionno = 0 ";
                        } else {
                            $aryQuery[] = " AND mp.lngrevisionno = " . ltrim(explode("_", $strOrderCode)[1], '0') . "";
                        }
                    } else {
                        $aryQuery[] = "mp.strordercode = '" . $strOrderCode . "'";
                    }
                }
            }
            $aryQuery[] = ")";
        }
        // 製品
        if ($strSearchColumnName == "strProductCode") {
            $strProductCodeArray = explode(",", $arySearchDataColumn["strProductCode"]);
            $aryQuery[] = " AND (";
            $count = 0;
            foreach ($strProductCodeArray as $strProductCode) {
                $count += 1;
                if ($count != 1) {
                    $aryQuery[] = " OR ";
                }
                if (strpos($strProductCode, '-') !== false) {
                    $aryQuery[] = "(mp.strProductCode" .
                    " between '" . explode("-", $strProductCode)[0] . "'" .
                    " AND " . "'" . explode("-", $strProductCode)[1] . "')";
                } else {
                    if (strpos($strProductCode, '_') !== false) {
                        $aryQuery[] = "mp.strProductCode = '" . explode("_", $strProductCode)[0] . "'";
                        $aryQuery[] = " AND mp.strrevisecode = '" . explode("_", $strProductCode)[1] . "'";
                    } else {
                        $aryQuery[] = "mp.strProductCode = '" . $strProductCode . "'";
                    }
                }
            }
            $aryQuery[] = ")";
        }

        // 製品名称（日本語）
        if ($strSearchColumnName == "strProductName") {
            if ($arySearchDataColumn["strProductName"]) {
                // $aryQuery[] = "AND UPPER( mp.strProductName ) LIKE UPPER( '%" . $arySearchDataColumn["strProductName"] . "%' ) ";
                $aryQuery[] = "AND (sf_translate_case(mp.strproductname) like '%' || sf_translate_case('".pg_escape_string($arySearchDataColumn["strProductName"])."') || '%'";                
                $aryQuery[] = " OR sf_translate_case(mp.strproductenglishname) like '%' || sf_translate_case('".pg_escape_string($arySearchDataColumn["strProductName"])."') || '%')";
            }
        }
        // 営業部署
        if ($strSearchColumnName == "lngInChargeGroupCode") {
            if ($arySearchDataColumn["lngInChargeGroupCode"]) {
                $aryQuery[] = "AND   mg.strgroupdisplaycode = '" . $arySearchDataColumn["lngInChargeGroupCode"] . "'";
            }
        }

        // 開発担当者
        if ($strSearchColumnName == "lngInChargeUserCode") {
            if ($arySearchDataColumn["lngInChargeUserCode"]) {
                $aryQuery[] = "AND   mu.struserdisplaycode = '" . $arySearchDataColumn["lngInChargeUserCode"] . "'";
            }
        }

        // 仕入先
        if ($strSearchColumnName == "lngCustomerCode") {
            if ($arySearchDataColumn["lngCustomerCode"]) {
                $aryQuery[] = "AND   mc_stock.strcompanydisplaycode = '" . $arySearchDataColumn["lngCustomerCode"] . "'";
            }
        }

        // 納品場所
        if ($strSearchColumnName == "lngDeliveryPlaceCode") {
            if ($arySearchDataColumn["lngDeliveryPlaceCode"]) {
                $aryQuery[] = "AND   mc_delivary.strcompanydisplaycode = '" . $arySearchDataColumn["lngDeliveryPlaceCode"] . "'";
            }
        }

        // 通貨
        if ($strSearchColumnName == "lngMonetaryunitCode") {
            $aryQuery[] = "AND   mp.lngmonetaryunitcode = " . $arySearchDataColumn["lngMonetaryunitCode"];
        }

        // 通貨レート
        if ($strSearchColumnName == "lngMonetaryRateCode") {
            $aryQuery[] = "AND   mp.lngmonetaryratecode = " . $arySearchDataColumn["lngMonetaryRateCode"];
        }

        // 支払条件
        if ($strSearchColumnName == "lngPayConditionCode") {
            $aryQuery[] = "AND   mp.lngpayconditioncode = " . $arySearchDataColumn["lngPayConditionCode"];
        }
    }

    // SQL作成
    $aryOutQuery[] = "SELECT";
    $aryOutQuery[] = "   mp.lngpurchaseorderno as lngPurchaseOrderNo";
    $aryOutQuery[] = "  ,mp.lngpurchaseorderno as lngpkno";
    $aryOutQuery[] = "  ,mp.lngrevisionno as lngRevisionNo";
    $aryOutQuery[] = "  ,mp.strrevisecode as strReviseCode";
    $aryOutQuery[] = "  ,max_status.lngorderstatuscode as lngorderstatuscode";
    $aryOutQuery[] = implode("\n", $arySelectQuery);
    $aryOutQuery[] = "FROM m_purchaseorder mp";
    $aryOutQuery[] = "INNER JOIN (";
    $aryOutQuery[] = "    SELECT lngpurchaseorderno, MAX(lngrevisionno) as maxRevisionNo FROM m_purchaseorder GROUP BY lngpurchaseorderno";
    $aryOutQuery[] = ") max_rev ON max_rev.lngpurchaseorderno = mp.lngpurchaseorderno and max_rev.maxRevisionNo = mp.lngrevisionno";
    $aryOutQuery[] = "INNER JOIN (";
    $aryOutQuery[] = "    select ";
    $aryOutQuery[] = "        tpod.lngpurchaseorderno";
    $aryOutQuery[] = "       ,tpod.lngrevisionno";
    $aryOutQuery[] = "       ,max(mo.lngorderstatuscode) as lngorderstatuscode";
    $aryOutQuery[] = "    from t_purchaseorderdetail tpod";
    $aryOutQuery[] = "    inner join m_order mo";
    $aryOutQuery[] = "        on mo.lngorderno = tpod.lngorderno";
    $aryOutQuery[] = "        and mo.lngrevisionno = tpod.lngorderrevisionno";
    $aryOutQuery[] = "    group by";
    $aryOutQuery[] = "        tpod.lngpurchaseorderno";
    $aryOutQuery[] = "       ,tpod.lngrevisionno";
    $aryOutQuery[] = ") max_status ON max_status.lngpurchaseorderno = mp.lngpurchaseorderno and max_status.lngrevisionno = mp.lngrevisionno";
    $aryOutQuery[] = "left join m_user input_user on input_user.lngusercode = mp.lnginsertusercode";
    $aryOutQuery[] = "left join m_group mg on mg.lnggroupcode = mp.lnggroupcode";
    $aryOutQuery[] = "left join m_user mu on mu.lngusercode = mp.lngusercode";
    $aryOutQuery[] = "left join m_company mc_stock on mc_stock.lngcompanycode = mp.lngcustomercode";
    $aryOutQuery[] = "left join m_company mc_delivary on mc_delivary.lngcompanycode = mp.lngdeliveryplacecode";
    $aryOutQuery[] = implode("\n", $aryQuery);
    $aryOutQuery[] = "ORDER BY";
    $aryOutQuery[] = "   mp.dtmInsertDate DESC";
    $aryOutQuery[] = "";

    switch ($arySearchDataColumn["strSort"]) {

    }

    return implode("\n", $aryOutQuery);
}

/**
 * 対応する発注NOのデータに対する明細行を取得するSQL文の作成関数
 *
 *    発注NOから明細を取得する SQL文を作成する
 *
 *    @param  Array     $aryDetailViewColumn     表示対象明細カラム名の配列
 *    @param  String     $lngOrderNo             対象発注NO
 *    @param  Array     $aryData                 POSTデータの配列
 *    @param  Object    $objDB                   DBオブジェクト
 *    @return Array     $strSQL 検索用SQL文 OR Boolean FALSE
 *    @access public
 */
function fncGetOrderToProductSQL($aryDetailViewColumn, $lngOrderNo, $lngRevisionNo, $aryData, $objDB)
{
    reset($aryDetailViewColumn);

    // 表示用カラムに設定されている内容を検索用に文字列設定
    for ($i = 0; $i < count($aryDetailViewColumn); $i++) {
        $strViewColumnName = $aryDetailViewColumn[$i];

        // 表示項目
        // 製品コード
        if ($strViewColumnName == "strProductCode") {
            $arySelectQuery[] = ", od.strProductCode || '_' || od.strReviseCode  as strProductCode";
        }

        // 部門
        if ($strViewColumnName == "lngInChargeGroupCode") {
            $arySelectQuery[] = ", '['||mg.strgroupdisplaycode||'] '|| mg.strgroupdisplayname as lngInChargeGroupCode";
        }

        // 担当者
        if ($strViewColumnName == "lngInChargeUserCode") {
            $arySelectQuery[] = ", '['||mu.struserdisplaycode ||'] '|| mu.struserdisplayname  as lngInChargeUserCode";
        }

        // 製品名称（日本語）
        if ($strViewColumnName == "strProductName") {
            $arySelectQuery[] = ", p.strProductName as strProductName";
            $flgProductCode = true;
        }

        // 製品名称（英語）
        if ($strViewColumnName == "strProductEnglishName") {
            $arySelectQuery[] = ", p.strProductEnglishName as strProductEnglishName";
            $flgProductCode = true;
        }

        // 仕入科目
        if ($strViewColumnName == "lngStockSubjectCode") {
            $arySelectQuery[] = ", od.lngStockSubjectCode as lngStockSubjectCode";
            $arySelectQuery[] = ", ss.strStockSubjectName as strStockSubjectName";
            $flgStockSubject = true;
        }

        // 仕入部品
        if ($strViewColumnName == "lngStockItemCode") {
            $arySelectQuery[] = ", od.lngStockItemCode as lngStockItemCode";
            $flgStockItem = true;
        }

        // 金型番号
        if ($strViewColumnName == "strMoldNo") {
            $arySelectQuery[] = ", od.strMoldNo as strMoldNo";
        }

        // 顧客品番
        if ($strViewColumnName == "strGoodsCode") {
            $arySelectQuery[] = ", p.strGoodsCode as strGoodsCode";
            $flgProductCode = true;
        }

        // 運搬方法
        if ($strViewColumnName == "lngDeliveryMethodCode") {
            $arySelectQuery[] = ", od.lngDeliveryMethodCode as lngDeliveryMethodCode";
            $arySelectQuery[] = ", dm.strDeliveryMethodName as strDeliveryMethodName";
            $flgDeliveryMethod = true;
        }

        // 納期
        if ($strViewColumnName == "dtmDeliveryDate") {
            $arySelectQuery[] = ", to_char( od.dtmDeliveryDate, 'YYYY/MM/DD' ) as dtmDeliveryDate";
        }

        // 単価
        if ($strViewColumnName == "curProductPrice") {
            $arySelectQuery[] = ", To_char( od.curProductPrice, '9,999,999,990.9999' )  as curProductPrice";
        }

        // 単位
        if ($strViewColumnName == "lngProductUnitCode") {
            $arySelectQuery[] = ", od.lngProductUnitCode as lngProductUnitCode";
            $arySelectQuery[] = ", pu.strProductUnitName as strProductUnitName";
            $flgProductUnit = true;
        }

        // 数量
        if ($strViewColumnName == "lngProductQuantity") {
            $arySelectQuery[] = ", To_char( od.lngProductQuantity, '9,999,999,990' )  as lngProductQuantity";
        }

        // 税抜金額
        if ($strViewColumnName == "curSubTotalPrice") {
            $arySelectQuery[] = ", To_char( od.curSubTotalPrice, '9,999,999,990.99' )  as curSubTotalPrice";
        }
        // 明細備考
        if ($strViewColumnName == "strDetailNote") {
            $arySelectQuery[] = ", od.strNote as strDetailNote";
        }
    }

    // 仕入部品のみ表示対象だった時は仕入科目についてもデータを取得する
    if ($flgStockItem == true and $flgStockSubject == false) {
        $arySelectQuery[] = ", od.lngStockSubjectCode as lngStockSubjectCode";
        $arySelectQuery[] = ", ss.strStockSubjectName as strStockSubjectName";
        $flgStockSubject = true;
    }

    // 絶対条件 対象発注NOの指定
    $aryQuery[] = " WHERE od.lngOrderNo = " . $lngOrderNo . " AND od.lngrevisionno = " . $lngRevisionNo;

    // 条件の追加

    // ////発注マスタ内の検索条件////
    // SQL文の作成
    $aryOutQuery = array();
    $aryOutQuery[] = "SELECT od.lngSortKey as lngRecordNo";
    $aryOutQuery[] = "	,od.lngOrderNo as lngOrderNo";
    $aryOutQuery[] = "	,od.lngRevisionNo as lngRevisionNo";

    // select句 クエリー連結
    if (!empty($arySelectQuery)) {
        $aryOutQuery[] = implode("\n", $arySelectQuery);
    }

    // From句 の生成
    $aryFromQuery = array();
    $aryFromQuery[] = " FROM t_OrderDetail od";

    // 追加表示用の参照マスタ対応
    $aryFromQuery[] = " LEFT JOIN (";
    $aryFromQuery[] = "     SELECT m_product.* FROM m_product ";
    $aryFromQuery[] = "      INNER JOIN (";
    $aryFromQuery[] = "          SELECT ";
    $aryFromQuery[] = "              lngproductno,strrevisecode,MAX(lngrevisionno) as lngrevisionno ";
    $aryFromQuery[] = "          FROM m_product GROUP BY lngproductno,strrevisecode";
    $aryFromQuery[] = "      ) mp1 ON mp1.lngproductno = m_product.lngproductno";
    $aryFromQuery[] = "      AND mp1.strrevisecode = m_product.strrevisecode";
    $aryFromQuery[] = "      AND mp1.lngrevisionno = m_product.lngrevisionno";
    $aryFromQuery[] = " ) p on p.strProductCode = od.strProductCode and p.strReviseCode = od.strReviseCode";
    $aryFromQuery[] = " left join m_group mg on mg.lnggroupcode = p.lnginchargegroupcode";
    $aryFromQuery[] = " left join m_user  mu on mu.lngusercode = p.lnginchargeusercode";

    if ($flgStockSubject) {
        $aryFromQuery[] = " LEFT JOIN m_StockSubject ss USING (lngStockSubjectCode)";
    }
    if ($flgStockItem) {
        //        $aryOutQuery[] = " LEFT JOIN m_StockItem si USING (lngStockItemCode)\n";
    }
    if ($flgDeliveryMethod) {
        $aryFromQuery[] = " LEFT JOIN m_DeliveryMethod dm USING (lngDeliveryMethodCode)";
    }
    if ($flgProductUnit) {
        $aryFromQuery[] = " LEFT JOIN m_ProductUnit pu ON od.lngProductUnitCode = pu.lngProductUnitCode";
    }

    // From句 クエリー連結
    $aryOutQuery[] = implode("\n", $aryFromQuery);
    // Where句 クエリー連結
    $aryOutQuery[] = implode("\n", $aryQuery);

    // ソート条件指定
    if ($aryData["strSortOrder"] == "ASC") {
        $strAsDs = " DESC"; // ヘッダ項目とは逆順にする
    } else {
        $strAsDs = " ASC"; //降順
    }

    switch ($aryData["strSort"]) {
        case "strDetailNote":
            $aryOutQuery[] = " ORDER BY od.strNote" . $strAsDs . ", od.lngSortKey ASC";
            break;
        case "lngOrderDetailNo":
            $aryOutQuery[] = " ORDER BY od.lngSortKey" . $strAsDs;
            break;
        case "strProductName":
        case "strProductEnglishName":
        case "strGoodsCode":
            $aryOutQuery[] = " ORDER BY " . $aryData["strSort"] . " " . $strAsDs . ", od.lngSortKey ASC";
            break;
        default:
            $aryOutQuery[] = " ORDER BY od.lngSortKey ASC";
    }

    return implode("\n", $aryOutQuery);
}

/**
 * 発注書データHTML変換
 *
 * @param    Array    $aryViewColumn        カラム情報
 * @param    Array    $aryResult            発注書データ
 * @param    Array    $aryUserAuthority 　  権限
 * @param    boolean  $isMaxObj   　　　　　 最新対象
 * @param    boolean  $rownum   　　　　　 　行番号
 * @access    public
 *
 */
function fncSetPurchaseOrderHtml($aryViewColumn, $aryResult, $aryUserAuthority, $isMaxObj, $rownum)
{
    for ($i = 0; $i < count($aryResult); $i++) {
        if ($isMaxObj) {
            $aryHtml[] = "<tr id=" . $aryResult[$i]["strordercode"] . ">";
            $aryHtml[] = "  <td class=\"rownum\">" . ($i + 1) . "</td>";
        } else {
            $aryHtml[] = "<tr id=" . $aryResult[$i]["strordercode"] . "_" . $aryResult[$i]["lngrevisionno"] . ">";
            $aryHtml[] = "  <td class=\"rownum\">" . $rownum . "." . ($i + 1) . "</td>";
        }
        for ($j = 0; $j < count($aryViewColumn); $j++) {
            $strColumn = $aryViewColumn[$j];
            // 表示対象がボタンの場合
            if ($strColumn == "btnEdit" or $strColumn == "btnRecord" or $strColumn == "btnDelete") {
                // 修正ボタン
                if ($strColumn == "btnEdit" and $aryUserAuthority["Edit"]) {

                    $aryHtml[] = "  <td class=\"exclude-in-clip-board-target\"><img src=\"/mold/img/detail_off_bt.gif\" lngpurchaseorderno=\"" . $aryResult[$i]["lngpurchaseorderno"] . "\" lngrevisionno=\"" . $aryResult[$i]["lngrevisionno"] . "\" class=\"edit button\"></td>";
                    // 履歴ボタン
                } else if ($strColumn == "btnRecord" and $aryResult[$i]["lngrevisionno"] != 0) {
                    // 旧リビジョンの場合、履歴ボタンは非表示
                    $strOrderCode = sprintf("%s_%02d", $aryResult[$i]["strordercode"], $aryResult[$i]["lngrevisionno"]);
                    $aryHtml[] = "  <td class=\"exclude-in-clip-board-target\"><img src=\"/mold/img/detail_off_bt.gif\" rownum=\"" . ($i + 1) . "\" id=\"" . $aryResult[$i]["strordercode"] . "\" lngrevisionno=\"" . $aryResult[$i]["lngrevisionno"] . "\" class=\"history button\"></td>";
                } else {
                    $aryHtml[] = "  <td></td>";
                }
            } else {
                // 発注NO.
                if ($strColumn == "strOrderCode") {
                    $aryHtml[] = "  <td class=\"td-strordercode\" baseordercode=\"" . $aryResult[$i]["strordercode"] . "\">" . sprintf("%s_%02d", $aryResult[$i]["strordercode"], $aryResult[$i]["lngrevisionno"]) . "</td>";
                }
                // 発注有効期限日
                if ($strColumn == "dtmExpirationDate") {
                    $aryHtml[] = "  <td class=\"td-dtmexpirationdate\">" . $aryResult[$i]["dtmexpirationdate"] . "</td>";
                }
                // 製品コード
                if ($strColumn == "strProductCode") {
                    $aryHtml[] = "  <td class=\"td-strproductcode\">" . sprintf("[%s]", $aryResult[$i]["strproductcode"]) . "</td>";
                }
                // 登録日
                if ($strColumn == "dtmInsertDate") {
                    $aryHtml[] = "  <td class=\"td-dtminsertdate\">" . $aryResult[$i]["dtminsertdate"] . "</td>";
                }
                // 入力者
                if ($strColumn == "lngInputUserCode") {
                    $aryHtml[] = "  <td class=\"td-lnginsertusercode\">" . sprintf("[%s] %s", $aryResult[$i]["lnginsertusercode"], $aryResult[$i]["strinsertusername"]) . "</td>";
                }
                // 製品名
                if ($strColumn == "strProductName") {
                    $aryHtml[] = "  <td class=\"td-strproductname\">" . $aryResult[$i]["strproductname"] . "</td>";
                }
                // 製品名(英語)
                if ($strColumn == "strProductEnglishName") {
                    $aryHtml[] = "  <td class=\"td-strproductenglishname\">" . $aryResult[$i]["strproductenglishname"] . "</td>";
                }
                // 営業部署
                if ($strColumn == "lngInChargeGroupCode") {
                    $aryHtml[] = "  <td class=\"td-lnggroupcode\">" . sprintf("[%s] %s", $aryResult[$i]["lnggroupcode"], $aryResult[$i]["strgroupname"]) . "</td>";
                }
                // 開発担当者
                if ($strColumn == "lngInChargeUserCode") {
                    $aryHtml[] = "  <td class=\"td-lngusercode\">" . sprintf("[%s] %s", $aryResult[$i]["lngusercode"], $aryResult[$i]["strusername"]) . "</td>";
                }
                // 仕入先
                if ($strColumn == "lngCustomerCode") {
                    $aryHtml[] = "  <td class=\"td-lngcustomercode\">" . sprintf("[%s] %s", $aryResult[$i]["lngcustomercode"], $aryResult[$i]["strcustomername"]) . "</td>";
                }
                // 支払条件
                if ($strColumn == "lngPayConditionCode") {
                    $aryHtml[] = "  <td class=\"td-strpaycnoditionname\">" . $aryResult[$i]["strpayconditionname"] . "</td>";
                }
                // 税抜金額
                if ($strColumn == "curTotalPrice") {
                    $aryHtml[] = "  <td class=\"td-curtotalprice\">" . sprintf("%s %.2f", $aryResult[$i]["strmonetaryunitsign"], $aryResult[$i]["curtotalprice"]) . "</td>";
                }
                // 納品場所
                if ($strColumn == "lngDeliveryPlaceCode") {
                    $aryHtml[] = "  <td class=\"td-strdeliveryplacename\">" . $aryResult[$i]["strdeliveryplacename"] . "</td>";
                }
                // 明細備考
                if ($strColumn == "strNote") {
                    $aryHtml[] = "  <td class=\"td-strnote\">" . $aryResult[$i]["strnote"] . "</td>";
                }
            }
        }
        $aryHtml[] = "</tr>";
    }

    return implode("\n", $aryHtml);
}

/**
 * 検索結果表示関数
 *
 *    検索結果からテーブル構成で結果を出力する関数
 *
 *    @param  Array     $aryResult             検索結果が格納された配列
 *    @param  Array     $aryViewColumn         表示対象カラム名の配列
 *    @param  Array     $aryData             ＰＯＳＴデータ群
 *    @param    Array    $aryUserAuthority    ユーザーの操作に対する権限が入った配列
 *    @param    Array    $aryTytle            項目名が格納された配列（呼び出し元で日本語用、英語用の切り替え）
 *    @param  Object    $objDB               DBオブジェクト
 *    @param  Object    $objCache           キャッシュオブジェクト
 *    @param    Array    $aryTableName        表示カラム名とマスタ内カラム名変更用
 *    @access public
 */
function fncSetPurchaseTable($aryResult, $arySearchColumn, $aryViewColumn, $aryData, $aryUserAuthority, $aryTytle, $objDB, $objCache, $aryTableName)
{
    // 準備
    include_once 'conf.inc';
    require_once LIB_DEBUGFILE;

    // 表示カラムのヘッダ部と明細部の分離処理
    for ($i = 0; $i < count($aryViewColumn); $i++) {
        $strColumnName = $aryViewColumn[$i];

        // ボタンの場合ここで表示、非表示切り替え
        if ($strColumnName == "btnDetail") {
            if ($aryUserAuthority["Detail"]) {
                $aryHeadViewColumn[] = $strColumnName;
            }
        } else if ($strColumnName == "btnFix") {
            if ($aryUserAuthority["Fix"]) {
                $aryHeadViewColumn[] = $strColumnName;
            }
        } else if ($strColumnName == "btnDelete") {
            if ($aryUserAuthority["Delete"]) {
                $aryHeadViewColumn[] = $strColumnName;
            }
        } else if ($strColumnName == "btnInvalid") {
            if ($aryUserAuthority["Invalid"]) {
                $aryHeadViewColumn[] = $strColumnName;
            }
        } else if ($strColumnName == "btnAdmin") {
            if ($aryUserAuthority["Admin"]) {
                $aryHeadViewColumn[] = $strColumnName;
            }
        }
        // 2004.03.31 suzukaze update start
        // 詳細部
        else if ($strColumnName == "strProductCode"
            or $strColumnName == "lngInChargeGroupCode" or $strColumnName == "lngInChargeUserCode" // <-- added by siato
             or $strColumnName == "lngRecordNo" or $strColumnName == "lngStockSubjectCode" or $strColumnName == "lngStockItemCode"
            or $strColumnName == "strGoodsCode" or $strColumnName == "lngDeliveryMethodCode" or $strColumnName == "curProductPrice"
            or $strColumnName == "lngProductUnitCode" or $strColumnName == "lngProductQuantity" or $strColumnName == "curSubTotalPrice"
            or $strColumnName == "strDetailNote" or $strColumnName == "dtmDeliveryDate"
            or $strColumnName == "strProductName" or $strColumnName == "strProductEnglishName" or $strColumnName == "strMoldNo")
        // 2004.03.31 suzukaze update end
        {
            $aryDetailViewColumn[] = $strColumnName;
            $aryHeadViewColumn[] = $strColumnName;
        }
        // ヘッダ部
        else {
            $aryHeadViewColumn[] = $strColumnName;
        }
    }

    // テーブルの形成
    $lngResultCount = count($aryResult);

    $lngColumnCount = 1;

    // 項目名列の生成 start=========================================
    $aryHtml[] = "<thead>";
    $aryHtml[] = "<tr>";
    $aryHtml[] = "\t<th class=\"exclude-in-clip-board-target\"><img src=\"/mold/img/copy_off_bt.gif\" class=\"copy button\"></th>";

    // 表示対象カラムの配列より項目設定
    for ($j = 0; $j < count($aryViewColumn); $j++) {
        $Addth = "\t<th>";
        $strColumnName = $aryViewColumn[$j];

        // ソート項目以外の場合
        if ($strColumnName == "btnDetail" or $strColumnName == "btnFix" or $strColumnName == "btnDelete") {
            // ソート項目以外の場合
            if (($strColumnName == "btnDetail" and $aryUserAuthority["Detail"])
                or ($strColumnName == "btnFix" and $aryUserAuthority["Fix"])
                or ($strColumnName == "btnDelete" and $aryUserAuthority["Delete"])
                or ($strColumnName == "btnAdmin" and $aryUserAuthority["Admin"])) {
                $Addth .= $aryTytle[$strColumnName];
            }
        }
        // ソート項目の場合
        else {
            $Addth .= $aryTytle[$strColumnName];
        }

        $Addth .= "</th>";
        $aryHtml[] = $Addth;
    }
    $aryHtml[] = "</tr>";
    $aryHtml[] = "</thead>";

    // 項目名列の生成 end=========================================

    $aryHtml[] = "<tbody>";

    for ($i = 0; $i < $lngResultCount; $i++) {
        // 管理モード用過去リバイズ、削除データ出力start==================================
        // 管理モードの場合　同じ発注コードの一覧を取得し表示する

        // リバイズコード無しの発注コードを取得する
        if (strlen($aryResult[$i]["strordercode"]) >= 9) {
            $strOrderCodeBase = preg_replace("/" . strstr($aryResult[$i]["strordercode"] . "/", "_"), "", $aryResult[$i]["strordercode"]);
        } else {
            $strOrderCodeBase = $aryResult[$i]["strordercode"];
        }

        // 明細出力用の調査
        $lngDetailViewCount = count($aryDetailViewColumn);

        if ($lngDetailViewCount) {
            // 明細行数の調査
            $strDetailQuery = fncGetOrderToProductSQL($aryDetailViewColumn, $aryResult[$i]["lngorderno"], $aryResult[$i]["lngrevisionno"], $aryData, $objDB);
//("kids2.log", $strDetailQuery , __FILE__, __LINE__, "a" );
            //echo $strDetailQuery;
            // クエリー実行
            if (!$lngDetailResultID = $objDB->execute($strDetailQuery)) {
                $strMessage = fncOutputError(3, "DEF_FATAL", "クエリー実行エラー", true, "../po/search/index.php?strSessionID=" . $aryData["strSessionID"], $objDB);
            }

            $lngDetailCount = pg_num_rows($lngDetailResultID);

            // 配列のクリア
            unset($aryDetailResult);

            // 結果の取得
            if ($lngDetailCount) {
                for ($k = 0; $k < $lngDetailCount; $k++) {
                    $aryDetailResult[] = pg_fetch_array($lngDetailResultID, $k, PGSQL_ASSOC);
                }
            }

            $objDB->freeResult($lngDetailResultID);
        }

        // １レコード分の出力
        $aryHtml_add = fncSetPurchaseHeadTable($lngColumnCount, $aryResult[$i], $aryDetailResult, $aryDetailViewColumn, $aryHeadViewColumn, $aryData, $aryUserAuthority, $objDB, $objCache, $lngSameOrderCount, $aryResult[$i]["lngrevisionno"], $aryResult[$i]);
//                $aryHtml_add = fncSetPurchaseHeadTable ( $lngColumnCount, $arySameOrderCodeResult[$j], $aryDetailResult, $aryDetailViewColumn, $aryHeadViewColumn, $aryData, $aryUserAuthority, $objDB, $objCache, $lngSameOrderCount, $j, $arySameOrderCodeResult[0] );
        $lngColumnCount = $lngColumnCount + count($aryDetailResult);

        $strColBuff = '';
        for ($k = 0; $k < count($aryHtml_add); $k++) {
            $strColBuff .= $aryHtml_add[$k];
        }
        $aryHtml[] = $strColBuff;
//            }
        //        }

        // 管理モード用過去リバイズデータ出力end==================================

    }

    $aryHtml[] = "</tbody>";

    $strhtml = implode("\n", $aryHtml);

    return $strhtml;
}

/**
 * 発注データHTML変換
 *
 * @param    Array    $aryResult            発注データ
 * @param    Array    $aryViewColumn        表示列
 * @param    Array    $aryUserAuthority    権限
 * @param    Array    $aryTitle            列名
 * @param    Object   $objDB                DBオブジェクト
 * @param    Object   $objCache            キャッシュオブジェクト
 * @param    Array    $aryTableName        テーブル名
 * @param    boolean  $isMaxObj           　最新対象
 */
function fncSetPurchaseOrderTable($aryResult, $aryViewColumn, $aryData, $aryUserAuthority, $aryTitle, $objDB, $objCache, $aryTableName, $isMaxObj)
{
    // 表示カラムのヘッダ部と明細部の分離処理
    for ($i = 0; $i < count($aryViewColumn); $i++) {
        $strColumnName = $aryViewColumn[$i];

        // ボタンの場合ここで表示・非表示切り替え
        if ($strColumnName == "btnEdit") {
            if ($aryUserAuthority["Edit"]) {
                $aryHeadViewColumn[] = $strColumnName;
            }
        } else if ($strColumnName == "btnRecord") {
            $aryHeadViewColumn[] = $strColumnName;
        } else if ($strColumnName == "dtmInsertDate"
            or $strColumnName == "lngInputUserCode"
            or $strColumnName == "dtmExpirationDate"
            or $strColumnName == "strOrderCode"
            or $strColumnName == "strProductCode"
            or $strColumnName == "strProductName"
            or $strColumnName == "strProductEnglishName"
            or $strColumnName == "lngInChargeGroupCode"
            or $strColumnName == "lngInChargeUserCode"
            or $strColumnName == "lngCustomerCode"
            or $strColumnName == "strDeliveryPlaceName"
            or $strColumnName == "lngMonetaryunitCode"
            or $strColumnName == "lngMonetaryRateCode"
            or $strColumnName == "lngPayConditionCode") {
            $aryDetailViewColumn[] = $strColumnName;
            $aryHeadViewColumn[] = $strColumnName;
        } else {
            $aryHeadViewColumn[] = $strColumnName;
        }
    }

    // テーブルの形成
    $lngColumnCount = 1;

    // 項目名列の生成
    $aryHtml[] = "<thead>";
    $aryHtml[] = "<tr>";
    $aryHtml[] = "\t<th class=\"exclude-in-clip-board-target\"><img src=\"/mold/img/copy_off_bt.gif\" class=\"copy button\"></th>";

    // 表示対象カラムの配列より項目設定
    for ($i = 0; $i < count($aryViewColumn); $i++) {
        $addTh = "\t<th>";
        $strColumnName = $aryViewColumn[$i];

        if ($strColumnName == "btnPreview" or $strColumnName == "btnEdit" or $strColumnName == "btnRecord") {
            // ソート項目以外の場合
            if (($strColumnName == "btnPreview" and $aryUserAuthority["Preview"])
                or ($strColumnName == "btnEdit" and $aryUserAuthority["Edit"])
                or ($strColumnName == "btnRecord")
            ) {
                $addTh .= $aryTitle[$strColumnName];
            } else {
                // 表示対象外
                continue;
            }
        } else {
            // ソート項目の場合
            $addTh .= $aryTitle[$strColumnName];
        }

        $addTh .= "</th>";
        $aryHtml[] = $addTh;
    }
    $aryHtml[] = "</tr>";
    $aryHtml[] = "</thead>";

    // データ部
    $aryHtml[] = "<tbody>";
    $lngResultCount = count($aryResult);

    $aryHtml[] = fncSetPurchaseOrderHtml($aryViewColumn, $aryResult, $aryUserAuthority, $isMaxObj, null);
    $aryHtml[] = "</tbody>";
    $strHtml = implode("\n", $aryHtml);

    return $strHtml;
}

function fncResortSearchColumn($aryViewColumn)
{
    $aryResult = array();

    $aryResult[] = "btnDetail";
    $aryResult[] = "btnFix";
    $aryResult[] = "Record";
    if (in_array("btnAdmin", $aryViewColumn)) {$aryResult[] = "btnAdmin";}
    if (in_array("strOrderCode", $aryViewColumn)) {$aryResult[] = "strOrderCode";}
    if (in_array("dtmExpirationDate", $aryViewColumn)) {$aryResult[] = "dtmExpirationDate";}
    if (in_array("strProductCode", $aryViewColumn)) {$aryResult[] = "strProductCode";}
    if (in_array("dtmInsertDate", $aryViewColumn)) {$aryResult[] = "dtmInsertDate";}
    if (in_array("lngInputUserCode", $aryViewColumn)) {$aryResult[] = "lngInputUserCode";}
    if (in_array("strProductName", $aryViewColumn)) {
        $aryResult[] = "strProductName";
        $aryResult[] = "strProductEnglishName";
    }
    if (in_array("lngInChargeGroupCode", $aryViewColumn)) {$aryResult[] = "lngInChargeGroupCode";}
    if (in_array("lngInChargeUserCode", $aryViewColumn)) {$aryResult[] = "lngInChargeUserCode";}
    if (in_array("lngCustomerCode", $aryViewColumn)) {$aryResult[] = "lngCustomerCode";}
    if (in_array("lngStockSubjectCode", $aryViewColumn)) {$aryResult[] = "lngStockSubjectCode";}
    if (in_array("lngStockItemCode", $aryViewColumn)) {$aryResult[] = "lngStockItemCode";}
    if (in_array("dtmDeliveryDate", $aryViewColumn)) {$aryResult[] = "dtmDeliveryDate";}
    if (in_array("lngOrderStatusCode", $aryViewColumn)) {$aryResult[] = "lngOrderStatusCode";}
    if (in_array("lngRecordNo", $aryViewColumn)) {$aryResult[] = "lngRecordNo";}
    if (in_array("curProductPrice", $aryViewColumn)) {$aryResult[] = "curProductPrice";}
    if (in_array("lngProductQuantity", $aryViewColumn)) {$aryResult[] = "lngProductQuantity";}
    if (in_array("curSubTotalPrice", $aryViewColumn)) {$aryResult[] = "curSubTotalPrice";}
    if (in_array("strNote", $aryViewColumn)) {$aryResult[] = "strNote";}
    if (in_array("strDetailNote", $aryViewColumn)) {$aryResult[] = "strDetailNote";}
    if (in_array("btnDelete", $aryViewColumn)) {$aryResult[] = "btnDelete";}

    return $aryResult;
}

function fncResortSearchColumn2($aryViewColumn)
{
    $aryResult = array();

    if (in_array("btnPreview", $aryViewColumn)) {$aryResult[] = "btnPreview";}
    $aryResult[] = "btnEdit";
    $aryResult[] = "btnRecord";
//    $aryResult[] = "btnDelete";
    if (in_array("strOrderCode", $aryViewColumn)) {$aryResult[] = "strOrderCode";}
    if (in_array("dtmExpirationDate", $aryViewColumn)) {$aryResult[] = "dtmExpirationDate";}
    if (in_array("strProductCode", $aryViewColumn)) {$aryResult[] = "strProductCode";}
    if (in_array("dtmInsertDate", $aryViewColumn)) {$aryResult[] = "dtmInsertDate";}
    if (in_array("lngInputUserCode", $aryViewColumn)) {$aryResult[] = "lngInputUserCode";}
    if (in_array("strProductCode", $aryViewColumn)) {
        $aryResult[] = "strProductName";
        $aryResult[] = "strProductEnglishName";
    }
    if (in_array("strOrderStatusName", $aryViewColumn)) {$aryResult[] = "strOrderStatusName";}
    if (in_array("lngInChargeGroupCode", $aryViewColumn)) {$aryResult[] = "lngInChargeGroupCode";}
    if (in_array("lngInChargeUserCode", $aryViewColumn)) {$aryResult[] = "lngInChargeUserCode";}
    if (in_array("lngCustomerCode", $aryViewColumn)) {$aryResult[] = "lngCustomerCode";}
    if (in_array("lngPayConditionCode", $aryViewColumn)) {$aryResult[] = "lngPayConditionCode";}
    if (in_array("curTotalPrice", $aryViewColumn)) {$aryResult[] = "curTotalPrice";}
    if (in_array("lngDeliveryPlaceCode", $aryViewColumn)) {$aryResult[] = "lngDeliveryPlaceCode";}
    if (in_array("strNote", $aryViewColumn)) {$aryResult[] = "strNote";}
    if (in_array("btnDelete", $aryViewColumn)) {$aryResult[] = "btnDelete";}

    return $aryResult;
}

function fncGetPurchseOrderByOrderCodeSQL($strOrderCode, $lngRevisionNo)
{
    $aryQuery[] = "SELECT";
    $aryQuery[] = "  mp.lngpurchaseorderno as lngPurchaseOrderNo";
    $aryQuery[] = "  , mp.lngrevisionno as lngRevisionNo";
    $aryQuery[] = "  , mp.strrevisecode as strReviseCode";
    $aryQuery[] = "  , mp.strordercode as strOrderCode";
    $aryQuery[] = "  , to_char(mp.dtmexpirationdate, 'YYYY/MM/DD') as dtmExpirationDate";
    $aryQuery[] = "  , mp.strproductcode as strProductCode";
    $aryQuery[] = "  , mp.strproductname as strProductName";
    $aryQuery[] = "  , mp.strproductenglishname as strProductEnglishName";
    $aryQuery[] = "  , to_char(mp.dtminsertdate, 'YYYY/MM/DD') as dtmInsertDate";
    $aryQuery[] = "  , input_user.struserdisplaycode AS lngInsertUserCode";
    $aryQuery[] = "  , mp.strinsertusername AS strInsertUserName";
    $aryQuery[] = "  , mg.strgroupdisplaycode AS lngGroupCode";
    $aryQuery[] = "  , mp.strgroupname as strGroupName";
    $aryQuery[] = "  , mu.struserdisplaycode as lngUserCode";
    $aryQuery[] = "  , mp.strusername as strUserName";
    $aryQuery[] = "  , mc_stock.strcompanydisplaycode as lngCustomerCode";
    $aryQuery[] = "  , mp.strcustomername as strCustomerName";
    $aryQuery[] = "  , mp.lngpayconditioncode as lngPayConditionCode";
    $aryQuery[] = "  , mp.strpayconditionname as strPayConditionName";
    $aryQuery[] = "  , mp.lngmonetaryunitcode as lngMonetaryUnitCode";
    $aryQuery[] = "  , mp.strmonetaryunitsign as strMonetaryUnitSign";
    $aryQuery[] = "  , mp.curtotalprice as curTotalPrice";
    $aryQuery[] = "  , mp.strdeliveryplacename as strDeliveryPlaceName";
    $aryQuery[] = "  , mp.strnote as strNote ";
    $aryQuery[] = "FROM";
    $aryQuery[] = "  m_purchaseorder mp ";
    $aryQuery[] = "  left join m_user input_user ";
    $aryQuery[] = "    on input_user.lngusercode = mp.lnginsertusercode ";
    $aryQuery[] = "  left join m_group mg ";
    $aryQuery[] = "    on mg.lnggroupcode = mp.lnggroupcode ";
    $aryQuery[] = "  left join m_user mu ";
    $aryQuery[] = "    on mu.lngusercode = mp.lngusercode ";
    $aryQuery[] = "  left join m_company mc_stock ";
    $aryQuery[] = "    on mc_stock.lngcompanycode = mp.lngcustomercode ";
    $aryQuery[] = "  left join m_company mc_delivary ";
    $aryQuery[] = "    on mc_delivary.lngcompanycode = mp.lngdeliveryplacecode ";
    $aryQuery[] = "WHERE";
    $aryQuery[] = "  mp.strordercode = '" . $strOrderCode . "' ";
    $aryQuery[] = "  AND mp.lngrevisionno <> " . $lngRevisionNo . " ";
    $aryQuery[] = "ORDER BY";
    $aryQuery[] = "  mp.lngpurchaseorderno";
    $aryQuery[] = "  , mp.lngrevisionno DESC";

    return implode("\n", $aryQuery);
}

/**
 * Undocumented function
 *
 * @param [type] $lngorderno
 * @param [type] $revisionno
 * @param [type] $objDB
 * @return void
 */
function fucIsFixObject($lngpurchaseorderno, $lngrevisionno, $objDB)
{
    $aryQuery[] = "select";
    $aryQuery[] = "  mo.lngorderstatuscode ";
    $aryQuery[] = "from";
    $aryQuery[] = "  m_order mo ";
    $aryQuery[] = "  left join t_purchaseorderdetail tpo ";
    $aryQuery[] = "    on mo.lngorderno = tpo.lngorderno ";
    $aryQuery[] = "    and mo.lngrevisionno = tpo.lngorderrevisionno ";
    $aryQuery[] = "where";
    $aryQuery[] = "  tpo.lngpurchaseorderno = " . $lngpurchaseorderno;
    $aryQuery[] = "  and tpo.lngrevisionno = " . $lngrevisionno;
    $aryQuery[] = "  and mo.lngorderstatuscode < " . DEF_ORDER_END;
    $strQuery = "";
    $strQuery = implode("\n", $aryQuery);

    list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);
    if (!$lngResultNum) {
        return false;
    }

    if ($lngResultNum > 0) {
        return true;
    }

    $objDB->freeResult($lngResultID);

    return false;
}
