<?
/**
 *    仕入　詳細、削除、無効化関数群
 *
 *    @package   kuwagata
 *    @license   http://www.wiseknot.co.jp/
 *    @copyright Copyright &copy; 2003, Wiseknot
 *    @author    Keiji Suzukaze <k-suzukaze@wiseknot.co.jp>
 *    @access    public
 *    @version   1.01
 *
 *    処理概要
 *    検索結果関連の関数
 *
 *
 */

/**
 * 指定された仕入番号から仕入ヘッダ情報を取得するＳＱＬ文を作成
 *
 *    指定仕入番号のヘッダ情報の取得用ＳＱＬ文作成関数
 *
 *    @param  Integer     $lngStockNo             取得する仕入番号
 *    @param  Integer     $lngRevisionNo             取得する仕入リビジョン番号
 *    @return strQuery     $strQuery 検索用SQL文
 *    @access public
 */
function fncGetStockHeadNoToInfoSQL($lngStockNo, $lngRevisionNo)
{
    // SQL文の作成
    $aryQuery[] = "SELECT distinct on (s.lngStockNo) s.lngStockNo as lngStockNo, s.lngRevisionNo as lngRevisionNo";

    // 登録日
    $aryQuery[] = ", to_char( s.dtmInsertDate, 'YYYY/MM/DD HH24:MI:SS' ) as dtmInsertDate";
    // 計上日
    $aryQuery[] = ", to_char( s.dtmAppropriationDate, 'YYYY/MM/DD' ) as dtmStockAppDate";
    // 仕入No
    $aryQuery[] = ", s.strStockCode as strStockCode";
    // 発注No
    $aryQuery[] = ", mp.strordercode strOrderCode";
    // 発注コード
    $aryQuery[] = ", tsd.lngOrderNo as lngOrderNo";
    // 発注明細番号
    $aryQuery[] = ", tsd.lngorderrevisionno as lngorderrevisionno";
    // 発注コード
    $aryQuery[] = ", mp.strordercode as strRealOrderCode";
    // 製品
    $aryQuery[] = ", p.strproductcode || '_' || p.strReviseCode as strProductCode";
    $aryQuery[] = ", p.strproductname";
    // 伝票コード
    $aryQuery[] = ", s.strSlipCode as strSlipCode";
    // 入力者
    $aryQuery[] = ", s.lngInputUserCode as lngInputUserCode";
    $aryQuery[] = ", input_u.strUserDisplayCode as strInputUserDisplayCode";
    $aryQuery[] = ", input_u.strUserDisplayName as strInputUserDisplayName";
    // 仕入先
    $aryQuery[] = ", s.lngCustomerCompanyCode as lngCustomerCode";
    $aryQuery[] = ", cust_c.strCompanyDisplayCode as strCustomerDisplayCode";
    $aryQuery[] = ", cust_c.strCompanyDisplayName as strCustomerDisplayName";
    // 部門
    $aryQuery[] = ", s.lngGroupCode as lngInChargeGroupCode";
    $aryQuery[] = ", inchg_g.strGroupDisplayCode as strInChargeGroupDisplayCode";
    $aryQuery[] = ", inchg_g.strGroupDisplayName as strInChargeGroupDisplayName";
    // 担当者
    $aryQuery[] = ", s.lngUserCode as lngInChargeUserCode";
    $aryQuery[] = ", inchg_u.strUserDisplayCode as strInChargeUserDisplayCode";
    $aryQuery[] = ", inchg_u.strUserDisplayName as strInChargeUserDisplayName";
    // 納品場所
    $aryQuery[] = ", s.lngDeliveryPlaceCode as lngDeliveryPlaceCode";
    $aryQuery[] = ", delv_c.strCompanyDisplayCode as strDeliveryDisplayCode";
    $aryQuery[] = ", delv_c.strCompanyDisplayName as strDeliveryDisplayName";
    // 通貨
    $aryQuery[] = ", s.lngMonetaryUnitCode as lngMonetaryUnitCode";
    $aryQuery[] = ", mu.strMonetaryUnitName as strMonetaryUnitName";
    $aryQuery[] = ", mu.strMonetaryUnitSign as strMonetaryUnitSign";
    // レートタイプ
    $aryQuery[] = ", s.lngMonetaryRateCode as lngMonetaryRateCode";
    $aryQuery[] = ", mr.strMonetaryRateName as strMonetaryRateName";
    // 換算レート
    $aryQuery[] = ", s.curConversionRate as curConversionRate";
    // 状態
    $aryQuery[] = ", s.lngStockStatusCode as lngStockStatusCode";
    $aryQuery[] = ", ss.strStockStatusName as strStockStatusName";
    // 支払条件
    $aryQuery[] = ", s.lngPayConditionCode as lngPayConditionCode";
    $aryQuery[] = ", pc.strPayConditionName as strPayConditionName";
    // 仕入有効期限日
    $aryQuery[] = ", to_char( s.dtmExpirationDate, 'YYYY/MM/DD' ) as dtmExpirationDate";
    // 備考
    $aryQuery[] = ", s.strNote as strNote";
    // 合計金額
    $aryQuery[] = ", s.curTotalPrice ";

    $aryQuery[] = "FROM m_Stock s ";
    $aryQuery[] = "INNER JOIN t_stockdetail tsd ";
    $aryQuery[] = "    on tsd.lngstockno = s.lngstockno ";
    $aryQuery[] = "    and tsd.lngrevisionno = s.lngrevisionno ";
    $aryQuery[] = " inner JOIN m_Product p ";
    $aryQuery[] = "     on p.strproductcode =  tsd.strproductcode";
    $aryQuery[] = " and p.strrevisecode = tsd.strrevisecode ";
    $aryQuery[] = " inner join( select lngproductno, strrevisecode, max(lngrevisionno) as lngrevisionno from m_product group by lngproductno, strrevisecode) p_rev";
    $aryQuery[] = "     on p_rev.lngproductno = p.lngproductno";
    $aryQuery[] = " and p_rev.strrevisecode = p.strrevisecode";
    $aryQuery[] = " and p_rev.lngrevisionno = p.lngrevisionno";
    $aryQuery[] = "INNER JOIN t_purchaseorderdetail tpd ";
    $aryQuery[] = "    on tpd.lngorderno = tsd.lngorderno ";
    $aryQuery[] = "    and tpd.lngorderdetailno = tsd.lngorderdetailno ";
    $aryQuery[] = "    and tpd.lngorderrevisionno = tsd.lngorderrevisionno ";

    $aryQuery[] = "INNER JOIN m_purchaseorder mp ";
    $aryQuery[] = "    on mp.lngpurchaseorderno = tpd.lngpurchaseorderno ";
    $aryQuery[] = "    and mp.lngrevisionno = tpd.lngrevisionno ";

    $aryQuery[] = "LEFT JOIN m_User input_u ON s.lngInputUserCode = input_u.lngUserCode";
    $aryQuery[] = "LEFT JOIN m_Company cust_c ON s.lngCustomerCompanyCode = cust_c.lngCompanyCode";
    $aryQuery[] = "LEFT JOIN m_Company delv_c ON s.lngDeliveryPlaceCode = delv_c.lngCompanyCode";
    $aryQuery[] = "LEFT JOIN m_Group inchg_g ON s.lngGroupCode = inchg_g.lngGroupCode";
    $aryQuery[] = "LEFT JOIN m_User inchg_u ON s.lngUserCode = inchg_u.lngUserCode";
    $aryQuery[] = "LEFT JOIN m_StockStatus ss USING (lngStockStatusCode)";
    $aryQuery[] = "LEFT JOIN m_PayCondition pc ON s.lngPayConditionCode = pc.lngPayConditionCode";
    $aryQuery[] = "LEFT JOIN m_MonetaryUnit mu ON s.lngMonetaryUnitCode = mu.lngMonetaryUnitCode";
    $aryQuery[] = "LEFT JOIN m_MonetaryRateClass mr ON s.lngMonetaryRateCode = mr.lngMonetaryRateCode";

    $aryQuery[] = " WHERE s.lngStockNo = " . $lngStockNo . "";
    $aryQuery[] = " AND s.lngRevisionNo = " . $lngRevisionNo . "";

    $strQuery = implode("\n", $aryQuery);

    return $strQuery;
}

/**
 * 指定された仕入番号から仕入明細情報を取得するＳＱＬ文を作成
 *
 *    指定仕入番号の明細情報の取得用ＳＱＬ文作成関数
 *
 *    @param  Integer     $lngStockNo             取得する仕入番号
 *    @param  Integer     $lngRevisionNo             取得する仕入リビジョン番号
 *    @return strQuery     $strQuery 検索用SQL文
 *    @access public
 */
function fncGetStockDetailNoToInfoSQL($lngStockNo, $lngRevisionNo)
{
    // SQL文の作成
    $aryQuery[] = "SELECT distinct on (sd.lngSortKey) sd.lngSortKey as lngRecordNo, ";
    $aryQuery[] = "sd.lngStockNo as lngStockNo, sd.lngRevisionNo as lngRevisionNo";

    // 発注番号・発注明細番号
    $aryQuery[] = ", sd.lngorderno as lngorderno";
    $aryQuery[] = ", sd.lngorderdetailno as lngorderdetailno";
    // 製品コード・名称
    $aryQuery[] = ", sd.strProductCode || '_' || sd.strReviseCode as strProductCode";
    $aryQuery[] = ", p.strProductName as strProductName";
    // 仕入科目
    $aryQuery[] = ", sd.lngStockSubjectCode as lngStockSubjectCode";
    $aryQuery[] = ", ss.strStockSubjectName as strStockSubjectName";
    // 仕入部品
    $aryQuery[] = ", sd.lngStockItemCode as lngStockItemCode";
    $aryQuery[] = ", si.strStockItemName as strStockItemName";
    // 金型番号
    $aryQuery[] = ", sd.strMoldNo as strMoldNo";
    // 顧客品番
    $aryQuery[] = ", p.strGoodsCode as strGoodsCode";
    // 運搬方法
    $aryQuery[] = ", sd.lngDeliveryMethodCode as lngDeliveryMethodCode";
    $aryQuery[] = ", dm.strDeliveryMethodName as strDeliveryMethodName";
    // 納期
    $aryQuery[] = ", tod.dtmdeliverydate as dtmDeliveryDate";
    // 単価
    $aryQuery[] = ", sd.curProductPrice";
    // 単位
    $aryQuery[] = ", sd.lngProductUnitCode as lngProductUnitCode";
    $aryQuery[] = ", pu.strProductUnitName as strProductUnitName";
    // 数量
    $aryQuery[] = ", To_char( sd.lngProductQuantity, '9,999,999,990' )  as lngProductQuantity";
    // 税抜金額
    $aryQuery[] = ", sd.curSubTotalPrice";
    // 税区分
    $aryQuery[] = ", sd.lngTaxClassCode as lngTaxClassCode";
    $aryQuery[] = ", tc.strTaxClassName as strTaxClassName";
    // 税率
    $aryQuery[] = ", sd.lngTaxCode as lngTaxCode";
    $aryQuery[] = ", To_char( t.curTax, '9,999,999,990.9999' ) as curTax";
    // 税額
    $aryQuery[] = ", sd.curTaxPrice";
    $aryQuery[] = ", sd.curTaxPrice as curTaxPrice_comm";
    // 明細備考
    $aryQuery[] = ", sd.strNote as strDetailNote";

    // 明細行を表示する場合
    $aryQuery[] = " FROM t_StockDetail sd  ";
    $aryQuery[] = " INNER JOIN t_orderdetail tod ";
    $aryQuery[] = "     on tod.lngorderno = sd.lngorderno ";
    $aryQuery[] = " and tod.lngorderdetailno = sd.lngorderdetailno ";
    $aryQuery[] = " and tod.lngrevisionno = sd.lngorderrevisionno ";
    $aryQuery[] = " inner JOIN m_Product p ";
    $aryQuery[] = "     on p.strproductcode =  sd.strproductcode";
    $aryQuery[] = " and p.strrevisecode = sd.strrevisecode ";
    $aryQuery[] = " inner join( select lngproductno, strrevisecode, max(lngrevisionno) as lngrevisionno from m_product group by lngproductno, strrevisecode) p_rev";
    $aryQuery[] = "     on p_rev.lngproductno = p.lngproductno";
    $aryQuery[] = " and p_rev.strrevisecode = p.strrevisecode";
    $aryQuery[] = " and p_rev.lngrevisionno = p.lngrevisionno";
    $aryQuery[] = " inner JOIN m_StockSubject ss ";
    $aryQuery[] = "     on ss.lngstocksubjectcode =  sd.lngStockSubjectCode ";
    $aryQuery[] = " inner join m_StockItem si";
    $aryQuery[] = "   on   si.lngStockSubjectCode = ss.lngStockSubjectCode";
    $aryQuery[] = "   AND sd.lngStockItemCode = si.lngStockItemCode ";
    $aryQuery[] = " inner JOIN m_DeliveryMethod dm ";
    $aryQuery[] = "     on dm.lngdeliverymethodcode = sd.lngdeliverymethodcode ";
    $aryQuery[] = " inner JOIN m_ProductUnit pu ";
    $aryQuery[] = "    ON sd.lngProductUnitCode = pu.lngProductUnitCode ";
    $aryQuery[] = " LEFT JOIN m_TaxClass tc ";
    $aryQuery[] = "     on tc.lngTaxClassCode = sd.lngtaxclasscode ";
    $aryQuery[] = " LEFT JOIN m_Tax t ";
    $aryQuery[] = "     on t.lngTaxCode = sd.lngtaxcode";
    $aryQuery[] = " WHERE sd.lngStockNo = " . $lngStockNo . "";
    $aryQuery[] = " AND sd.lngRevisionNo = " . $lngRevisionNo . "";
    $aryQuery[] = " AND si.lngStockSubjectCode = ss.lngStockSubjectCode ";
    $aryQuery[] = " AND sd.lngStockItemCode = si.lngStockItemCode ";

    $aryQuery[] = " ORDER BY sd.lngSortKey ASC ";

    $strQuery = implode("\n", $aryQuery);

    return $strQuery;
}

/**
 * 詳細表示関数（ヘッダ用）
 *
 *    テーブル構成で仕入データ詳細を出力する関数
 *    ヘッダ行を表示する
 *
 *    @param  Array     $aryResult                 ヘッダ行の検索結果が格納された配列
 *    @access public
 */
function fncSetStockHeadTabelData($aryResult)
{
    $aryColumnNames = array_keys($aryResult);

    // 表示対象カラムの配列より結果の出力
    for ($i = 0; $i < count($aryColumnNames); $i++) {
        $strColumnName = $aryColumnNames[$i];

        // 登録日
        if ($strColumnName == "dtminsertdate") {
            $aryNewResult[$strColumnName] = str_replace("-", "/", substr($aryResult["dtminsertdate"], 0, 19));
        }

        // 計上日
        else if ($strColumnName == "dtmstockappdate") {
            $aryNewResult[$strColumnName] = str_replace("-", "/", $aryResult["dtmstockappdate"]);
        }

        // 入力者
        else if ($strColumnName == "lnginputusercode") {
            if ($aryResult["strinputuserdisplaycode"]) {
                $aryNewResult[$strColumnName] = "[" . $aryResult["strinputuserdisplaycode"] . "]";
            } else {
                $aryNewResult[$strColumnName] = "     ";
            }
            $aryNewResult[$strColumnName] .= " " . $aryResult["strinputuserdisplayname"];
        }

        // 仕入先
        else if ($strColumnName == "lngcustomercode") {
            if ($aryResult["strcustomerdisplaycode"]) {
                $aryNewResult[$strColumnName] = "[" . $aryResult["strcustomerdisplaycode"] . "]";
            } else {
                $aryNewResult[$strColumnName] = "      ";
            }
            $aryNewResult[$strColumnName] .= " " . $aryResult["strcustomerdisplayname"];
        }

        // 部門
        else if ($strColumnName == "lnginchargegroupcode") {
            if ($aryResult["strinchargegroupdisplaycode"]) {
                $aryNewResult[$strColumnName] = "[" . $aryResult["strinchargegroupdisplaycode"] . "]";
            } else {
                $aryNewResult[$strColumnName] = "    ";
            }
            $aryNewResult[$strColumnName] .= " " . $aryResult["strinchargegroupdisplayname"];
        }

        // 担当者
        else if ($strColumnName == "lnginchargeusercode") {
            if ($aryResult["strinchargeuserdisplaycode"]) {
                $aryNewResult[$strColumnName] = "[" . $aryResult["strinchargeuserdisplaycode"] . "]";
            } else {
                $aryNewResult[$strColumnName] = "     ";
            }
            $aryNewResult[$strColumnName] .= " " . $aryResult["strinchargeuserdisplayname"];
        }

        // 納品場所
        else if ($strColumnName == "lngdeliveryplacecode") {
            if ($aryResult["strdeliverydisplaycode"]) {
                $aryNewResult[$strColumnName] = "[" . $aryResult["strdeliverydisplaycode"] . "]";
            } else {
                $aryNewResult[$strColumnName] = "      ";
            }
            $aryNewResult[$strColumnName] .= " " . $aryResult["strdeliverydisplayname"];
        }

        // 合計金額
        else if ($strColumnName == "curtotalprice") {
            if (!$aryResult["curtotalprice"]) {
                $aryNewResult[$strColumnName] = convertPrice($aryResult["lngmonetaryunitcode"], $aryResult["strmonetaryunitsign"], 0, "price");
            } else {
                $aryNewResult[$strColumnName] = convertPrice($aryResult["lngmonetaryunitcode"], $aryResult["strmonetaryunitsign"], $aryResult["curtotalprice"], "price");
            }
        }

        // 状態
        else if ($strColumnName == "lngstockstatuscode") {
            $aryNewResult[$strColumnName] = $aryResult["strstockstatusname"];
        }

        // 支払条件
        else if ($strColumnName == "lngpayconditioncode") {
            $aryNewResult[$strColumnName] = $aryResult["strpayconditionname"];
        }

        // 通貨
        else if ($strColumnName == "lngmonetaryunitcode") {
            $aryNewResult[$strColumnName] = $aryResult["strmonetaryunitname"];
        }

        // レートタイプ
        else if ($strColumnName == "lngmonetaryratecode") {
            if ($aryResult["lngmonetaryratecode"] and $aryResult["lngmonetaryunitcode"] != DEF_MONETARY_YEN) {
                $aryNewResult[$strColumnName] = $aryResult["strmonetaryratename"];
            } else {
                $aryNewResult[$strColumnName] = "";
            }
        }

        // 発注有効期限日
        else if ($strColumnName == "dtmexpirationdate") {
            $aryNewResult[$strColumnName] = str_replace("-", "/", $aryResult["dtmexpirationdate"]);
        }

        // 備考
        else if ($strColumnName == "strnote") {
            $aryNewResult[$strColumnName] = nl2br($aryResult["strnote"]);
        }

        // その他の項目はそのまま出力
        else {
            $aryNewResult[$strColumnName] = $aryResult[$strColumnName];
        }
    }

    return $aryNewResult;
}

/**
 * 詳細表示関数（明細用）
 *
 *    テーブル構成で仕入データ詳細を出力する関数
 *    明細行を表示する
 *
 *    @param  Array     $aryDetailResult     明細行の検索結果が格納された配列（１データ分）
 *    @param  Array     $aryHeadResult         ヘッダ行の検索結果が格納された配列（参照用）
 *    @access public
 */
function fncSetStockDetailTabelData($aryDetailResult, $aryHeadResult)
{
    $aryColumnNames = array_keys($aryDetailResult);

    // 表示対象カラムの配列より結果の出力
    for ($i = 0; $i < count($aryColumnNames); $i++) {
        $strColumnName = $aryColumnNames[$i];

        // 製品コード名称
        if ($strColumnName == "strproductcode") {
            if ($aryDetailResult["strproductcode"]) {
                $aryNewDetailResult[$strColumnName] = "[" . $aryDetailResult["strproductcode"] . "]";
            } else {
                $aryNewDetailResult[$strColumnName] = "      ";
            }
            $aryNewDetailResult[$strColumnName] .= " " . $aryDetailResult["strproductname"];
        }

        // 仕入科目
        else if ($strColumnName == "lngstocksubjectcode") {
            if ($aryDetailResult["lngstocksubjectcode"]) {
                $aryNewDetailResult[$strColumnName] = "[" . $aryDetailResult["lngstocksubjectcode"] . "]";
            } else {
                $aryNewDetailResult[$strColumnName] = "      ";
            }
            $aryNewDetailResult[$strColumnName] .= " " . $aryDetailResult["strstocksubjectname"];
        }

        // 仕入部品
        else if ($strColumnName == "lngstockitemcode") {
            if ($aryDetailResult["lngstockitemcode"]) {
                $aryNewDetailResult[$strColumnName] = "[" . $aryDetailResult["lngstockitemcode"] . "]";
            } else {
                $aryNewDetailResult[$strColumnName] = "      ";
            }
            $aryNewDetailResult[$strColumnName] .= " " . $aryDetailResult["strstockitemname"];
        }

        // 金型番号
        else if ($strColumnName == "strmoldno") {
            // 仕入科目が４３３　金型海外償却　仕入部品が１ Injection Moldの場合
            // 仕入科目が４３１　金型償却高　　仕入部品が８ 金型の場合
            if ($aryDetailResult["strmoldno"]
                and ($aryDetailResult["lngstocksubjectcode"] == 433 and $aryDetailResult["lngstockitemcode"] == 1)
                or ($aryDetailResult["lngstocksubjectcode"] == 431 and $aryDetailResult["lngstockitemcode"] == 8)) {
                $aryNewDetailResult[$strColumnName] = $aryDetailResult["strmoldno"];
            }
        }

        // 顧客品番
        else if ($strColumnName == "strgoodscode") {
            $aryNewDetailResult[$strColumnName] = $aryDetailResult[$strColumnName];
        }

        // 運搬方法
        else if ($strColumnName == "lngdeliverymethodcode") {
            if ($aryDetailResult["strdeliverymethodname"] == "") {
                $aryDetailResult["strdeliverymethodname"] = "未定";
            }
            $aryNewDetailResult[$strColumnName] .= $aryDetailResult["strdeliverymethodname"];
        }

        // 納期
        else if ($strColumnName == "dtmdeliverydate") {
            $aryNewDetailResult[$strColumnName] = str_replace("-", "/", $aryDetailResult["dtmdeliverydate"]);
        }

        // 単価
        else if ($strColumnName == "curproductprice") {
            if (!$aryDetailResult["curproductprice"]) {
                $aryNewDetailResult[$strColumnName] = convertPrice($aryHeadResult["lngmonetaryunitcode"], $aryHeadResult["strmonetaryunitsign"], 0, "unitprice");
            } else {
                $aryNewDetailResult[$strColumnName] = convertPrice($aryHeadResult["lngmonetaryunitcode"], $aryHeadResult["strmonetaryunitsign"], $aryDetailResult["curproductprice"], "unitprice");
            }
        }

        // 単位
        else if ($strColumnName == "lngproductunitcode") {
            $aryNewDetailResult[$strColumnName] = $aryDetailResult["strproductunitname"];
        }

        // 税抜金額
        else if ($strColumnName == "cursubtotalprice") {
            if (!$aryDetailResult["cursubtotalprice"]) {
                $aryNewDetailResult[$strColumnName] = convertPrice($aryHeadResult["lngmonetaryunitcode"], $aryHeadResult["strmonetaryunitsign"], 0, "price");
            } else {
                $aryNewDetailResult[$strColumnName] = convertPrice($aryHeadResult["lngmonetaryunitcode"], $aryHeadResult["strmonetaryunitsign"], $aryDetailResult["cursubtotalprice"], "price");
            }
        }

        // 税区分
        else if ($strColumnName == "lngtaxclasscode") {
            $aryNewDetailResult[$strColumnName] = $aryDetailResult["strtaxclassname"];
        }

        // 税率
        else if ($strColumnName == "curtax") {
            if (!$aryDetailResult["curtax"]) {
                $aryNewDetailResult[$strColumnName] = "";
            } else {
                $aryNewDetailResult[$strColumnName] = $aryDetailResult["curtax"];
            }
        }

        // 税額
        else if ($strColumnName == "curtaxprice") {
            if (!$aryDetailResult["curtaxprice"]) {
                $aryNewDetailResult[$strColumnName] = convertPrice($aryHeadResult["lngmonetaryunitcode"], $aryHeadResult["strmonetaryunitsign"], 0, "taxprice");
            } else {
                $aryNewDetailResult[$strColumnName] = convertPrice($aryHeadResult["lngmonetaryunitcode"], $aryHeadResult["strmonetaryunitsign"], $aryDetailResult["curtaxprice"], "taxprice");
            }
        }

        // 明細備考
        else if ($strColumnName == "strdetailnote") {
            $aryNewDetailResult[$strColumnName] = nl2br($aryDetailResult[$strColumnName]);
        }

        // その他の項目はそのまま出力
        else {
            $aryNewDetailResult[$strColumnName] = $aryDetailResult[$strColumnName];
        }
    }

    return $aryNewDetailResult;
}

/**
 * 指定の仕入データについて無効化することでどうなるかケースわけする
 *
 *    指定の仕入データの状態を調査し、ケースわけする関数
 *
 *    @param  Array         $aryStockData     仕入データ
 *    @param  Object        $objDB            DBオブジェクト
 *    @return Integer     $lngCase        状態のケース
 *                                        1: 対象仕入データを無効化しても、最新の仕入データが影響受けない
 *                                        2: 対象仕入データを無効化することで、最新の仕入データが入れ替わる
 *                                        3: 対象仕入データが削除データで、仕入が復活する
 *                                        4: 対象仕入データを無効化することで、最新の仕入データになりうる仕入データがない
 *    @access public
 */
function fncGetInvalidCodeToMaster($aryStockData, $objDB)
{
    // 削除対象仕入と同じ仕入コードの最新の仕入Noを調べる
    $strQuery = "SELECT lngStockNo FROM m_Stock s WHERE s.strStockCode = '" . $aryStockData["strstockcode"] . "' AND s.bytInvalidFlag = FALSE "
        . " AND s.lngRevisionNo >= 0"
        . " AND s.lngRevisionNo = ( "
        . "SELECT MAX( s1.lngRevisionNo ) FROM m_Stock s1 WHERE s1.strStockCode = s.strStockCode )";

    // 検索クエリーの実行
    list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);

    if ($lngResultNum == 1) {
        $objResult = $objDB->fetchObject($lngResultID, 0);
        $lngNewStockNo = $objResult->lngstockno;
    } else {
        $lngCase = 4;
    }
    $objDB->freeResult($lngResultID);

    // 削除対象が最新かどうかのチェック
    if ($lngCase != 4) {
        if ($lngNewStockNo == $aryStockData["lngstockno"]) {
            // 最新の場合
            // 削除対象仕入以外でと同じ仕入コードの最新の仕入Noを調べる
            $strQuery = "SELECT lngStockNo FROM m_Stock s WHERE s.strStockCode = '" . $strStockCode . "' AND s.bytInvalidFlag = FALSE ";
            $strQuery .= " AND s.lngStockNo <> " . $aryStockData["lngstockno"] . " AND s.lngRevisionNo >= 0";

            // 検索クエリーの実行
            list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);

            if ($lngResultNum >= 1) {
                $lngCase = 2;
            } else {
                $lngCase = 4;
            }
            $objDB->freeResult($lngResultID);
        }
        // 対象仕入が削除データかどうかの確認
        else if ($aryStockData["lngrevisionno"] < 0) {
            $lngCase = 3;
        } else {
            $lngCase = 1;
        }
    }

    return $lngCase;
}

/**
 * 指定の仕入データの削除に関して、その仕入データを削除することでの状態変更関数
 *
 *    仕入の状態が「納品済」の場合、発注Noを指定していた場合、分納であった場合など
 *    各状態ごとにその仕入に関するデータの状態を変更する
 *
 *    @param  Array         $aryStockData     仕入データ
 *    @param  Object        $objDB            DBオブジェクト
 *    @return Boolean     0                実行成功
 *                        1                実行失敗 情報取得失敗
 *    @access public
 */
function fncStockDeleteSetStatus($aryStockData, $objDB)
{
    // 削除対象仕入は、発注Noを指定しない仕入である
    if ($aryStockData["lngorderno"] == "" or $aryStockData["lngorderno"] == 0) {
        return 0;
    }

    // 発注Noを指定している仕入の場合は、指定している最新の発注のデータを取得する
    $strQuery = "SELECT o.lngOrderNo as lngOrderNo, o.strOrderCode as strOrderCode, "
        . "o.lngOrderStatusCode as lngOrderStatusCode, o.lngMonetaryUnitCode as lngMonetaryUnitCode FROM m_Order o "
        . "WHERE o.strOrderCode = ( "
        . "SELECT o1.strOrderCode FROM m_Order o1 WHERE o1.lngOrderNo = " . $aryStockData["lngorderno"] . " AND o1.lngRevisionNo = " . $aryStockData["lngrevisionno"] . ") "
        . "AND o.bytInvalidFlag = FALSE "
        . "AND o.lngRevisionNo >= 0 "
        . "AND o.lngRevisionNo = ( "
        . "SELECT MAX( o2.lngRevisionNo ) FROM m_Order o2 WHERE o2.strOrderCode = o.strOrderCode ) "
        . "AND 0 <= ( "
        . "SELECT MIN( o3.lngRevisionNo ) FROM m_Order o3 WHERE o3.bytInvalidFlag = false AND o3.strOrderCode = o.strOrderCode ) ";

    // 検索クエリーの実行
    list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);

    if ($lngResultNum == 1) {
        $objResult = $objDB->fetchObject($lngResultID, 0);
        $lngNewOrderNo = $objResult->lngorderno;
        $strNewOrderCode = $objResult->strordercode;
        $lngNewOrderStatusCode = $objResult->lngorderstatuscode;
        $OrderlngMonetaryUnitCode = $objResult->lngmonetaryunitcode;
    } else {
        // 発注Noは指定しているが現在有効な最新発注が存在しない場合はそのまま削除可能とする
        return 0;
    }
    $objDB->freeResult($lngResultID);

    // 最新発注の明細情報を取得する
    $strQuery = "SELECT od.lngOrderDetailNo as lngOrderDetailNo, od.strProductCode as strProductCode, "
        . "od.lngStockSubjectCode as lngStockSubjectCode, od.lngStockItemCode as lngStockItemCode, "
        . "od.lngConversionClassCode as lngConversionClassCode, od.curProductPrice as curProductPrice, "
        . "od.lngProductQuantity as lngProductQuantity, od.lngProductUnitCode as lngProductUnitCode, "
        . "od.curSubTotalPrice as curSubTotalPrice, p.lngCartonQuantity as lngCartonQuantity "
        . "FROM t_OrderDetail od, m_Product p "
        . "WHERE lngOrderNo = " . $lngNewOrderNo . " AND od.strProductCode = p.strProductCode "
        . "ORDER BY lngSortKey";

    list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);

    if ($lngResultNum) {
        for ($i = 0; $i < $lngResultNum; $i++) {
            $aryOrderDetailResult[] = $objDB->fetchArray($lngResultID, $i);
        }
    } else {
        // 明細行が存在しない場合異常データ
        return 1;
    }
    $objDB->freeResult($lngResultID);

    // 削除対象仕入と同じ発注Noを指定している最新仕入を検索
    $strQuery = "SELECT s.lngStockNo as lngStockNo, s.lngStockStatusCode as lngStockStatusCode, "
    . "s.lngMonetaryUnitCode as lngMonetaryUnitCode FROM m_Stock s, t_stockdetail sd, m_Order o "
    . "WHERE o.strOrderCode = '" . $strNewOrderCode . "' AND o.lngOrderNo = sd.lngOrderNo "
    . "AND s.lngStockNo = sd.lngStockNo "
    . "AND s.bytInvalidFlag = FALSE "
    . "AND s.lngRevisionNo >= 0 "
    . "AND s.lngRevisionNo = ( "
    . "SELECT MAX( s2.lngRevisionNo ) FROM m_Stock s2 WHERE s2.strStockCode = s.strStockCode ) "
    . "AND 0 <= ( "
    . "SELECT MIN( s3.lngRevisionNo ) FROM m_Stock s3 WHERE s3.bytInvalidFlag = false AND s3.strStockCode = s.strStockCode ) "
    // 上記条件かつ削除対象の仕入ではない
     . "AND s.strStockCode <> '" . $aryStockData["strstockcode"] . "'";

    list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);

    if ($lngResultNum) {
        // 削除対象以外の仕入データが存在する場合
        for ($i = 0; $i < $lngResultNum; $i++) {
            $aryStockResult[] = $objDB->fetchArray($lngResultID, $i);
            // 明細情報を取得する
            $strStockDetailQuery = "SELECT lngStockDetailNo, strProductCode, lngStockSubjectCode, lngStockItemCode, lngConversionClassCode, "
                . "curProductPrice, lngProductQuantity, lngProductUnitCode, curSubTotalPrice "
                . "FROM t_StockDetail "
                . "WHERE lngStockNo = " . $aryStockResult[$i]["lngstockno"] . " "
                . "ORDER BY lngSortKey";

            list($lngStockDetailResultID, $lngStockDetailResultNum) = fncQuery($strStockDetailQuery, $objDB);

            if ($lngStockDetailResultNum) {
                for ($j = 0; $j < $lngStockDetailResultNum; $j++) {
                    $aryStockDetailResult[$i][] = $objDB->fetchArray($lngStockDetailResultID, $j);
                }
            }
            $objDB->freeResult($lngStockDetailResultID);
        }

        // 参照元発注の明細毎に取得した仕入にてどのような状態になっているのか調査
        for ($i = 0; $i < count($aryOrderDetailResult); $i++) {
            // 参照元発注の明細行番号を取得･････明細行番号にひもづいて仕入が消しこみされるため
            $lngOrderDetailNo = $aryOrderDetailResult[$i]["lngorderdetalno"]; // 明細行番号

            $strProductCode = $aryOrderDetailResult[$i]["strproductcode"]; // 製品コード
            $lngStockSubjectCode = $aryOrderDetailResult[$i]["lngstocksubjectcode"]; // 仕入科目コード
            $lngStockItemCode = $aryOrderDetailResult[$i]["lngstockitemcode"]; // 仕入部品コード
            $lngConversionClassCode = $aryOrderDetailResult[$i]["lngconversionclasscode"]; // 換算区分コード
            $curProductPrice = $aryOrderDetailResult[$i]["curproductprice"]; // 製品単価（荷姿単価）
            $lngProductQuantity = $aryOrderDetailResult[$i]["lngproductquantity"]; // 製品数量（荷姿数量）
            $lngProductUnitCode = $aryOrderDetailResult[$i]["lngproductunitcode"]; // 製品単位（荷姿単位）
            $curSubTotalPrice = $aryOrderDetailResult[$i]["cursubtotalprice"]; // 税抜金額
            $lngCartonQuantity = $aryOrderDetailResult[$i]["lngcartonquantity"]; // カートン入数

            // 換算区分が荷姿単位計上の場合、製品単価へ計算
            if ($lngConversionClassCode != DEF_CONVERSION_SEIHIN) {
                // 0 割り対策
                if ($lngCartonQuantity == 0 or $lngCartonQuantity == "") {
                    // カートン入り数が ０ だった場合は その製品に対する納品済みかどうかの判断ができないため 強制的に １ にて換算
                    $lngCartonQuantity = 1;
                }

                // 製品数量は荷姿数量 * カートン入数
                $lngProductQuantity = $lngProductQuantity * $lngCartonQuantity;

                // 製品価格は荷姿単価 / カートン入数
                $curProductPrice = $curProductPrice / $lngCartonQuantity;
            }

            $bytEndFlag = 0;
            $lngStockProductQuantity = 0;
            $curStockSubTotalPrice = 0;

            for ($j = 0; $j < count($aryStockResult); $j++) {
                $StocklngMonetaryUnitCode = $aryStockResult[$j]["lngmonetaryunitcode"];
                for ($k = 0; $k < count($aryStockDetailResult[$j]); $k++) {
                    // 発注明細行番号に対して仕入明細行番号が同じ、かつ製品コードが同じ明細が見つかった場合
                    // それに加え　通貨が同じ場合
                    if ($lngOrderDetailNo == $aryStockDetailResult[$j][$k]["lngstockdetailno"]
                        and $strProductCode == $aryStockDetailResult[$j][$k]["strproductcode"]
                        and $OrderlngMonetaryUnitCode == $StocklngMonetaryUnitCode) {
                        // 換算区分が荷姿単位計上の場合、製品単価へ計算
                        if ($aryStockDetailResult[$j][$k]["lngconversionclasscode"] != DEF_CONVERSION_SEIHIN) {
                            // 0 割り対策
                            if ($aryStockDetailResult[$j][$k]["lngCartonQuantity"] == 0 or $aryStockDetailResult[$j][$k]["lngCartonQuantity"] == "") {
                                // カートン入り数が ０ だった場合は その製品に対する納品済みかどうかの判断ができないため 強制的に １ にて換算
                                $aryStockDetailResult[$j][$k]["lngCartonQuantity"] = 1;
                            }

                            // 製品数量は荷姿数量 * カートン入数
                            $aryStockDetailResult[$j][$k]["lngProductQuantity"]
                            = $aryStockDetailResult[$j][$k]["lngProductQuantity"] * $aryStockDetailResult[$j][$k]["lngCartonQuantity"];

                            // 製品価格は荷姿単価 / カートン入数
                            $aryStockDetailResult[$j][$k]["curProductPrice"]
                            = $aryStockDetailResult[$j][$k]["curProductPrice"] / $aryStockDetailResult[$j][$k]["lngCartonQuantity"];
                        }

                        // 数量比較
                        if ($lngProductQuauntity > $aryStockDetailResult[$j][$k]["lngproductquantity"]) {
                            $lngStockProductQuantity += $aryStockDetailResult[$j][$k]["lngproductquantity"];
                            // 複数仕入からの合算での数量比較
                            if ($lngProductQuauntity <= $lngStockProductQuantity) {
                                $bytEndFlag = 99;
                                break;
                            }
                        } else {
                            $bytEndFlag = 99;
                            break;
                        }

                        // 税抜金額比較
                        if ($curSubTotalPrice > $aryStockDetailResult[$j]["cursubtotalprice"]) {
                            $curStockSubTotalPrice += $aryStockDetailResult[$j]["cursubtotalprice"];
                            // 複数仕入からの合算での税抜金額比較
                            if ($curSubTotalPrice <= $curStockSubTotalPrice) {
                                $bytEndFlag = 99;
                                break;
                            }
                        } else {
                            $bytEndFlag = 99;
                            break;
                        }

                        // 同じ明細行の情報が発注と仕入で見つかった際には「納品中」となるため以下設定
                        $bytEndFlag = 1;
                    }
                }
                // 仕入明細に発注明細と同内容が見つかった場合は　for 文抜け
                if ($bytEndFlag == 99) {
                    break;
                }
            }
            // 発注明細行毎の仕入明細行が見つかった状態を記憶
            $aryStatus[] = $bytEndFlag;
        }

        // 再度チェック　$aryStatus（明細ごとの状態）により発注全体としての状態を判断
        $flagZERO = 0;
        $flagALL = 0;
        for ($i = 0; $i < count($aryStatus); $i++) {
            if ($aryStatus[$i] == 0) {
                $flagZERO++;
            }
            if ($aryStatus[$i] == 99) {
                $flagALL++;
            }
        }
        // 発注明細に対して一件も仕入が発生していない場合、または完納ではない場合
        // （flagZEROが発注明細数に対してイコールの場合実際は初期状態であるが、仕入にて
        //   発注Noが指定されているのでここでの状態は「納品中」とする）
        if ($flagALL != count($aryStatus)) {
            // 仕入参照発注の状態の状態を「納品中」とする

            // 更新対象発注データをロックする
            $strLockQuery = "SELECT lngOrderNo FROM m_Order WHERE lngOrderNo = " . $lngNewOrderNo . " AND bytInvalidFlag = FALSE FOR UPDATE";

            list($lngLockResultID, $lngLockResultNum) = fncQuery($strLockQuery, $objDB);
            $objDB->freeResult($lngLockResultID);

            // 「納品中」状態への更新処理
            $strUpdateQuery = "UPDATE m_Order set lngOrderStatusCode = " . DEF_ORDER_DELIVER . " WHERE lngOrderNo = " . $lngNewOrderNo;

            list($lngUpdateResultID, $lngUpdateResultNum) = fncQuery($strUpdateQuery, $objDB);
            $objDB->freeResult($lngUpdateResultID);

            // 同じ発注NOを指定している仕入の状態に対しても「納品中」とする
            for ($i = 0; $i < count($aryStockResult); $i++) {
                // 更新対象仕入データをロックする
                $strLockQuery = "SELECT lngStockNo FROM m_Stock "
                    . "WHERE lngStockNo = " . $aryStockResult[$i]["lngstockno"] . " AND bytInvalidFlag = FALSE FOR UPDATE";

                list($lngLockResultID, $lngLockResultNum) = fncQuery($strLockQuery, $objDB);
                $objDB->freeResult($lngLockResultID);

                // 「納品中」状態への更新処理
                $strUpdateQuery = "UPDATE m_Stock set lngStockStatusCode = " . DEF_STOCK_DELIVER
                    . " WHERE lngStockNo = " . $aryStockResult[$i]["lngstockno"];

                list($lngUpdateResultID, $lngUpdateResultNum) = fncQuery($strUpdateQuery, $objDB);
                $objDB->freeResult($lngUpdateResultID);
            }

            return 0;
        } else
        // 削除対象仕入を削除しても対象発注は完納状態であったら
        {
            // 仕入参照発注の状態の状態を「納品済」とする

            // 更新対象発注データをロックする
            $strLockQuery = "SELECT lngOrderNo FROM m_Order WHERE lngOrderNo = " . $lngNewOrderNo . " AND bytInvalidFlag = FALSE FOR UPDATE";
            list($lngLockResultID, $lngLockResultNum) = fncQuery($strLockQuery, $objDB);
            $objDB->freeResult($lngLockResultID);

            // 「納品中」状態への更新処理
            $strUpdateQuery = "UPDATE m_Order set lngOrderStatusCode = " . DEF_ORDER_END . " WHERE lngOrderNo = " . $lngNewOrderNo;

            list($lngUpdateResultID, $lngUpdateResultNum) = fncQuery($strUpdateQuery, $objDB);
            $objDB->freeResult($lngUpdateResultID);

            // 同じ発注NOを指定している仕入の状態に対しても「納品中」とする
            for ($i = 0; $i < count($aryStockResult); $i++) {
                // 更新対象仕入データをロックする
                $strLockQuery = "SELECT lngStockNo FROM m_Stock "
                    . "WHERE lngStockNo = " . $aryStockResult[$i]["lngstockno"] . " AND bytInvalidFlag = FALSE FOR UPDATE";
                list($lngLockResultID, $lngLockResultNum) = fncQuery($strLockQuery, $objDB);
                $objDB->freeResult($lngLockResultID);

                // 「納品中」状態への更新処理
                $strUpdateQuery = "UPDATE m_Stock set lngStockStatusCode = " . DEF_STOCK_END
                    . " WHERE lngStockNo = " . $aryStockResult[$i]["lngstockno"];

                list($lngUpdateResultID, $lngUpdateResultNum) = fncQuery($strUpdateQuery, $objDB);
                $objDB->freeResult($lngUpdateResultID);
            }
            return 0;
        }
    } else {
        // 削除対象以外の仕入データが存在しない場合
        // 仕入の参照元最新発注の状態を「発注」に戻す

        // 更新対象発注データをロックする
        $strLockQuery = "SELECT lngOrderNo FROM m_Order WHERE lngOrderNo = " . $lngNewOrderNo . " AND bytInvalidFlag = FALSE FOR UPDATE";
        list($lngLockResultID, $lngLockResultNum) = fncQuery($strLockQuery, $objDB);
        if (!$lngLockResultNum) {
            fncOutputError(9051, DEF_ERROR, "無効化処理エラー", true, "", $objDB);
        }
        $objDB->freeResult($lngLockResultID);

        // 「発注」状態への更新処理
        $strUpdateQuery = "UPDATE m_Order set lngOrderStatusCode = " . DEF_ORDER_ORDER . " WHERE lngOrderNo = " . $lngNewOrderNo;

        list($lngUpdateResultID, $lngUpdateResultNum) = fncQuery($strUpdateQuery, $objDB);
        $objDB->freeResult($lngUpdateResultID);

        return 0;
    }

    $objDB->freeResult($lngResultID);

    return 0;
}

/**
 * 発注No.により発注情報を取得する
 *
 * @param [String] $strOrderCode
 * @param [DB] $objDB
 * @return void
 */
function fncGetPoInfoSQL($strOrderCode, $objDB)
{
    // SQL生成
    $aryQuery[] = " SELECT ";
//    $aryQuery[] = " mpo.strordercode,";
    $aryQuery[] = " mpo.lngpurchaseorderno,"; // 発注書ビジョン番号
    $aryQuery[] = " mpo.lngrevisionno as lngpurchaseorderrevisionno,"; // 発注書ビジョン番号
    $aryQuery[] = " od.lngorderdetailno,"; // 発注番号
    $aryQuery[] = " od.lngorderno,"; // 発注明細番号
    $aryQuery[] = " od.lngrevisionno,"; // リビジョン番号
    $aryQuery[] = " od.strproductcode || '_' || od.strrevisecode as strproductcode,"; // 製品コード
    $aryQuery[] = " p.strproductname,"; // 製品名称
    $aryQuery[] = " od.lngstocksubjectcode,"; // 仕入科目コード
    $aryQuery[] = " ss.strstocksubjectname,"; // 仕入科目名称
    $aryQuery[] = " c.strcompanydisplaycode,"; // 仕入先コード
    $aryQuery[] = " c.strcompanydisplayname,"; // 仕入先名称
    $aryQuery[] = " dp_c.strcompanydisplaycode as lnglocationcode,"; // 納品場所コード
    $aryQuery[] = " dp_c.strcompanydisplayname as strlocationname,"; // 納品場所名称
    $aryQuery[] = " od.lngstockitemcode,"; // 仕入部品コード
    $aryQuery[] = " si.strstockitemname,"; // 仕入部品名称
    $aryQuery[] = " To_char( od.dtmdeliverydate, 'YYYY/mm/dd' ) as dtmdeliverydate,"; // 納品日
    $aryQuery[] = " od.lngdeliverymethodcode as lngCarrierCode,"; // 運搬方法コード
    $aryQuery[] = " od.lngconversionclasscode,"; // 換算区分コード / 1：単位計上/ 2：荷姿単位計上
    $aryQuery[] = " od.curproductprice as curproductprice,"; // 製品価格
    $aryQuery[] = " od.lngproductquantity,"; // 製品数量
    $aryQuery[] = " od.lngproductunitcode,"; // 製品単位コード
    $aryQuery[] = " pu.strproductunitname,"; // 製品単位名称
    /*
    $aryQuery[] = " od.lngtaxclasscode,";  // 消費税区分コード
    $aryQuery[] = " od.lngtaxcode,";  // 消費税コード
    $aryQuery[] = " od.curtaxprice,";  // 消費税金額
     */
    $aryQuery[] = " od.cursubtotalprice as cursubtotalprice,"; // 小計金額
    $aryQuery[] = " od.strnote,"; // 備考
    $aryQuery[] = " od.strmoldno as strSerialNo,"; // シリアル
    $aryQuery[] = " mo.lngorderstatuscode as lngorderstatuscode,"; // 発注ステータス
    $aryQuery[] = " os.strorderstatusname as strorderstatusname,"; // 発注ステータス
    $aryQuery[] = " mo.lngmonetaryunitcode as lngmonetaryunitcode,"; // 通貨単位コード
    $aryQuery[] = " mu.strmonetaryunitname as strmonetaryunitname,"; // 通貨単位名称
    $aryQuery[] = " mu.strmonetaryunitsign as strmonetaryunitsign,"; // 通貨単位名称
    $aryQuery[] = " c.lngcountrycode as lngcountrycode,"; // 国コード
    $aryQuery[] = " mo.lngmonetaryratecode as lngmonetaryratecode,"; // 通貨レートコード
    $aryQuery[] = " mpo.lngpayconditioncode as lngpayconditioncode,"; // 支払条件
    $aryQuery[] = " dp_pc.strpayconditionname as strpayconditionname"; // 支払条件名
    $aryQuery[] = " FROM t_orderdetail od";
    $aryQuery[] = " inner join m_order mo";
    $aryQuery[] = " on mo.lngorderno = od.lngorderno";
    $aryQuery[] = " and mo.lngrevisionno = od.lngrevisionno";
    $aryQuery[] = " inner join t_purchaseorderdetail pod";
    $aryQuery[] = " on pod.lngorderno = od.lngorderno";
    $aryQuery[] = " and pod.lngorderdetailno = od.lngorderdetailno";
    $aryQuery[] = " and pod.lngorderrevisionno = od.lngrevisionno";
    $aryQuery[] = "  inner join ( ";
    $aryQuery[] = "    select";
    $aryQuery[] = "      mpo1.lngpurchaseorderno";
    $aryQuery[] = "      , mpo1.lngrevisionno";
    $aryQuery[] = "      , mpo1.strordercode ";
    $aryQuery[] = "      , mpo1.lngpayconditioncode ";
    $aryQuery[] = "    from";
    $aryQuery[] = "      m_purchaseorder mpo1 ";
    $aryQuery[] = "      inner join ( ";
    $aryQuery[] = "        select";
    $aryQuery[] = "          lngpurchaseorderno";
    $aryQuery[] = "          , max(lngrevisionno) as lngrevisionno ";
    $aryQuery[] = "        from";
    $aryQuery[] = "          m_purchaseorder ";
    $aryQuery[] = "        group by";
    $aryQuery[] = "          lngpurchaseorderno";
    $aryQuery[] = "      ) max_rev ";
    $aryQuery[] = "        on max_rev.lngpurchaseorderno = mpo1.lngpurchaseorderno ";
    $aryQuery[] = "        and max_rev.lngrevisionno = mpo1.lngrevisionno ";
    $aryQuery[] = "    where";
    $aryQuery[] = "      not exists ( ";
    $aryQuery[] = "        select";
    $aryQuery[] = "          mpo2.lngpurchaseorderno ";
    $aryQuery[] = "        from";
    $aryQuery[] = "          m_purchaseorder mpo2 ";
    $aryQuery[] = "        where";
    $aryQuery[] = "          mpo2.lngpurchaseorderno = mpo1.lngpurchaseorderno ";
    $aryQuery[] = "          and mpo2.lngrevisionno = - 1";
    $aryQuery[] = "      )";
    $aryQuery[] = "  ) mpo ";
    $aryQuery[] = "    on mpo.lngpurchaseorderno = pod.lngpurchaseorderno ";
    $aryQuery[] = "    and mpo.lngrevisionno = pod.lngrevisionno ";
    $aryQuery[] = "  LEFT JOIN (";
    $aryQuery[] = "    select p1.*  from m_product p1 ";
    $aryQuery[] = "    inner join (select max(lngrevisionno) lngrevisionno, strproductcode, strrevisecode from m_Product group by strProductCode, strrevisecode) p2";
    $aryQuery[] = "    on p1.lngrevisionno = p2.lngrevisionno and p1.strproductcode = p2.strproductcode and p1.strrevisecode = p2.strrevisecode";
    $aryQuery[] = " ) p ";
    $aryQuery[] = "  on p.strproductcode = od.strproductcode";
    $aryQuery[] = "  and p.strrevisecode = od.strrevisecode";
    $aryQuery[] = "  LEFT JOIN m_stocksubject ss on ss.lngstocksubjectcode = od.lngstocksubjectcode";
    $aryQuery[] = "  LEFT JOIN m_stockitem si on si.lngstocksubjectcode = od.lngstocksubjectcode and si.lngstockitemcode = od.lngstockitemcode";
    $aryQuery[] = "  LEFT JOIN m_monetaryunit mu on mu.lngmonetaryunitcode = mo.lngmonetaryunitcode";
    $aryQuery[] = "  LEFT JOIN m_orderstatus os on os.lngorderstatuscode = mo.lngorderstatuscode";
    $aryQuery[] = "  LEFT JOIN m_productunit pu on pu.lngproductunitcode = od.lngproductunitcode";
    $aryQuery[] = "  LEFT JOIN m_company c on c.lngcompanycode = mo.lngcustomercompanycode";
    $aryQuery[] = "  LEFT JOIN m_company dp_c on dp_c.lngcompanycode = mo.lngdeliveryplacecode";
    $aryQuery[] = "  LEFT JOIN m_paycondition dp_pc on dp_pc.lngpayconditioncode = mpo.lngpayconditioncode";
    $aryQuery[] = " WHERE mo.lngorderstatuscode = 2";

    $aryQuery[] = " and mpo.strordercode = '" . $strOrderCode . "'"; //
    $aryQuery[] = "  ORDER BY od.lngSortKey";

    $strQuery = implode("\n", $aryQuery);
    list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);

    $aryOrderDetail = array();

    if ($lngResultNum) {
        for ($i = 0; $i < $lngResultNum; $i++) {
            $aryOrderDetail[] = $objDB->fetchArray($lngResultID, $i);
        }
    }

    $objDB->freeResult($lngResultID);

    return $aryOrderDetail;
}

/**
 * 仕入により発注情報を取得する
 *
 * @param [String] $strOrderCode
 * @param [DB] $objDB
 * @return void
 */
function fncGetPoInfoSQLByStock($lngStockNo, $lngRevisionNo, $strOrderCode, $objDB)
{
    // SQL生成
    $aryQuery[] = " SELECT ";
//    $aryQuery[] = " mpo.strordercode,";
    $aryQuery[] = " mpo.lngpurchaseorderno,"; // 発注書ビジョン番号
    $aryQuery[] = " mpo.lngrevisionno as lngpurchaseorderrevisionno,"; // 発注書ビジョン番号
    $aryQuery[] = " od.lngorderdetailno,"; // 発注番号
    $aryQuery[] = " od.lngorderno,"; // 発注明細番号
    $aryQuery[] = " od.lngrevisionno,"; // リビジョン番号
    $aryQuery[] = " od.strproductcode,"; // 製品コード
    $aryQuery[] = " p.strproductname,"; // 製品名称
    $aryQuery[] = " od.lngstocksubjectcode,"; // 仕入科目コード
    $aryQuery[] = " ss.strstocksubjectname,"; // 仕入科目名称
    $aryQuery[] = " c.strcompanydisplaycode,"; // 仕入先コード
    $aryQuery[] = " c.strcompanydisplayname,"; // 仕入先名称
    $aryQuery[] = " dp_c.strcompanydisplaycode as lnglocationcode,"; // 納品場所コード
    $aryQuery[] = " dp_c.strcompanydisplayname as strlocationname,"; // 納品場所名称
    $aryQuery[] = " od.lngstockitemcode,"; // 仕入部品コード
    $aryQuery[] = " si.strstockitemname,"; // 仕入部品名称
    $aryQuery[] = " To_char( od.dtmdeliverydate, 'YYYY/mm/dd' ) as dtmdeliverydate,"; // 納品日
    $aryQuery[] = " od.lngdeliverymethodcode as lngCarrierCode,"; // 運搬方法コード
    $aryQuery[] = " od.lngconversionclasscode,"; // 換算区分コード / 1：単位計上/ 2：荷姿単位計上
    $aryQuery[] = " od.curproductprice as curproductprice,"; // 製品価格
    $aryQuery[] = " od.lngproductquantity,"; // 製品数量
    $aryQuery[] = " od.lngproductunitcode,"; // 製品単位コード
    $aryQuery[] = " pu.strproductunitname,"; // 製品単位名称
    /*
    $aryQuery[] = " od.lngtaxclasscode,";  // 消費税区分コード
    $aryQuery[] = " od.lngtaxcode,";  // 消費税コード
    $aryQuery[] = " od.curtaxprice,";  // 消費税金額
     */
    $aryQuery[] = " od.cursubtotalprice as cursubtotalprice,"; // 小計金額
    $aryQuery[] = " od.strnote,"; // 備考
    $aryQuery[] = " od.strmoldno as strSerialNo,"; // シリアル
    $aryQuery[] = " mo.lngorderstatuscode as lngorderstatuscode,"; // 発注ステータス
    $aryQuery[] = " os.strorderstatusname as strorderstatusname,"; // 発注ステータス
    $aryQuery[] = " mo.lngmonetaryunitcode as lngmonetaryunitcode,"; // 通貨単位コード
    $aryQuery[] = " mu.strmonetaryunitname as strmonetaryunitname,"; // 通貨単位名称
    $aryQuery[] = " mu.strmonetaryunitsign as strmonetaryunitsign,"; // 通貨単位名称
    $aryQuery[] = " c.lngcountrycode as lngcountrycode,"; // 国コード
    $aryQuery[] = " mo.lngmonetaryratecode as lngmonetaryratecode,"; // 通貨レートコード
    $aryQuery[] = " mpo.lngpayconditioncode as lngpayconditioncode,"; // 支払条件
    $aryQuery[] = " dp_pc.strpayconditionname as strpayconditionname"; // 支払条件名
    $aryQuery[] = " FROM t_orderdetail od";
    $aryQuery[] = " inner join m_order mo";
    $aryQuery[] = " on mo.lngorderno = od.lngorderno";
    $aryQuery[] = " and mo.lngrevisionno = od.lngrevisionno";
    $aryQuery[] = " inner join t_purchaseorderdetail pod";
    $aryQuery[] = " on pod.lngorderno = od.lngorderno";
    $aryQuery[] = " and pod.lngorderdetailno = od.lngorderdetailno";
    $aryQuery[] = " and pod.lngorderrevisionno = od.lngrevisionno";
    $aryQuery[] = "  inner join ( ";
    $aryQuery[] = "    select";
    $aryQuery[] = "      mpo1.lngpurchaseorderno";
    $aryQuery[] = "      , mpo1.lngrevisionno";
    $aryQuery[] = "      , mpo1.strordercode ";
    $aryQuery[] = "      , mpo1.lngpayconditioncode ";
    $aryQuery[] = "    from";
    $aryQuery[] = "      m_purchaseorder mpo1 ";
    $aryQuery[] = "      inner join ( ";
    $aryQuery[] = "        select";
    $aryQuery[] = "          lngpurchaseorderno";
    $aryQuery[] = "          , max(lngrevisionno) as lngrevisionno ";
    $aryQuery[] = "        from";
    $aryQuery[] = "          m_purchaseorder ";
    $aryQuery[] = "        group by";
    $aryQuery[] = "          lngpurchaseorderno";
    $aryQuery[] = "      ) max_rev ";
    $aryQuery[] = "        on max_rev.lngpurchaseorderno = mpo1.lngpurchaseorderno ";
    $aryQuery[] = "        and max_rev.lngrevisionno = mpo1.lngrevisionno ";
    $aryQuery[] = "    where";
    $aryQuery[] = "      not exists ( ";
    $aryQuery[] = "        select";
    $aryQuery[] = "          mpo2.lngpurchaseorderno ";
    $aryQuery[] = "        from";
    $aryQuery[] = "          m_purchaseorder mpo2 ";
    $aryQuery[] = "        where";
    $aryQuery[] = "          mpo2.lngpurchaseorderno = mpo1.lngpurchaseorderno ";
    $aryQuery[] = "          and mpo2.lngrevisionno = - 1";
    $aryQuery[] = "      )";
    $aryQuery[] = "  ) mpo ";
    $aryQuery[] = "    on mpo.lngpurchaseorderno = pod.lngpurchaseorderno ";
    $aryQuery[] = "    and mpo.lngrevisionno = pod.lngrevisionno ";
    $aryQuery[] = "  LEFT JOIN (";
    $aryQuery[] = "    select p1.*  from m_product p1 ";
    $aryQuery[] = "    inner join (select max(lngrevisionno) lngrevisionno, strproductcode, strrevisecode from m_Product group by strProductCode, strrevisecode) p2";
    $aryQuery[] = "    on p1.lngrevisionno = p2.lngrevisionno and p1.strproductcode = p2.strproductcode and p1.strrevisecode = p2.strrevisecode";
    $aryQuery[] = " ) p ";
    $aryQuery[] = "  on p.strproductcode = od.strproductcode";
    $aryQuery[] = "  and p.strrevisecode = od.strrevisecode";
    $aryQuery[] = "  LEFT JOIN m_stocksubject ss on ss.lngstocksubjectcode = od.lngstocksubjectcode";
    $aryQuery[] = "  LEFT JOIN m_stockitem si on si.lngstocksubjectcode = od.lngstocksubjectcode and si.lngstockitemcode = od.lngstockitemcode";
    $aryQuery[] = "  LEFT JOIN m_monetaryunit mu on mu.lngmonetaryunitcode = mo.lngmonetaryunitcode";
    $aryQuery[] = "  LEFT JOIN m_orderstatus os on os.lngorderstatuscode = mo.lngorderstatuscode";
    $aryQuery[] = "  LEFT JOIN m_productunit pu on pu.lngproductunitcode = od.lngproductunitcode";
    $aryQuery[] = "  LEFT JOIN m_company c on c.lngcompanycode = mo.lngcustomercompanycode";
    $aryQuery[] = "  LEFT JOIN m_company dp_c on dp_c.lngcompanycode = mo.lngdeliveryplacecode";
    $aryQuery[] = "  LEFT JOIN m_paycondition dp_pc on dp_pc.lngpayconditioncode = mpo.lngpayconditioncode";
    $aryQuery[] = "  LEFT JOIN (select lngstockno, lngrevisionno, lngorderno, lngorderdetailno, lngorderrevisionno from t_stockdetail where lngstockno = " . $lngStockNo;
    $aryQuery[] = "  and lngrevisionno = " . $lngRevisionNo . ") tsd";
    $aryQuery[] = "  on tsd.lngorderno = od.lngorderno AND tsd.lngorderdetailno = od.lngorderdetailno AND tsd.lngorderrevisionno = od.lngrevisionno";

    $aryQuery[] = " WHERE (";
    $aryQuery[] = " mo.lngorderstatuscode = 2";
    $aryQuery[] = " or (tsd.lngstockno = " . $lngStockNo . " AND tsd.lngrevisionno = " . $lngRevisionNo . ")";
    $aryQuery[] = " )";

    $aryQuery[] = " and mpo.strordercode = '" . $strOrderCode . "'"; //
    $aryQuery[] = "  ORDER BY od.lngSortKey";

    $strQuery = implode("\n", $aryQuery);
    list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);

    $aryOrderDetail = array();

    if ($lngResultNum) {
        for ($i = 0; $i < $lngResultNum; $i++) {
            $aryOrderDetail[] = $objDB->fetchArray($lngResultID, $i);
        }
    }

    $objDB->freeResult($lngResultID);

    return $aryOrderDetail;
}

/**
 * 消費税情報を取得する
 *
 * @param [type] $dtmStockAppDate
 * @param [type] $objDB
 * @return void
 */
function fncGetTaxInfo($dtmStockAppDate, $objDB)
{
    $strQuery = "SELECT lngtaxcode, curtax, MAX(dtmapplystartdate) "
        . "FROM m_tax "
        . "WHERE dtmapplystartdate <= to_date('" . $dtmStockAppDate . "', 'yyyy/mm/dd') "
        . "AND dtmapplyenddate >= to_date('" . $dtmStockAppDate . "', 'yyyy/mm/dd') "
        . "GROUP BY lngtaxcode, curtax ";
        // . "ORDER BY lngpriority ";

    $objResult = array();

    // 税率などの取得クエリーの実行
    list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);

    if ($lngResultNum) {
        for ($i = 0; $i < $lngResultNum; $i++) {
            $objResult[] = $objDB->fetchObject($lngResultID, $i);
        }
    }

    $objDB->freeResult($lngResultID);

    return $objResult;
}

/**
 * 消費税区分リストを取得する
 *
 * @param [type] $objDB
 * @return void
 */
function fncGetTaxClassAry($objDB)
{
    $strQuery = "SELECT lngtaxclasscode, strtaxclassname "
        . "FROM m_taxclass "
        . "ORDER BY lngtaxclasscode ";
    $aryTaxclass = array();

    // 税区分の取得クエリーの実行
    list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);
    if ($lngResultNum) {
        for ($i = 0; $i < $lngResultNum; $i++) {
            $aryTaxclass[] = $objDB->fetchArray($lngResultID, $i);
        }
    }
    $objDB->freeResult($lngResultID);

    return $aryTaxclass;
}

/**
 * 換算レートを取得する
 *
 * @param [仕入日] $dtmStockAppDate
 * @param [通貨レート] $lngMonetaryRateCode
 * @param [レートタイプ] $lngMonetaryUnitCode
 * @param [DB] $objDB
 * @return void
 */
function fncGetCurConversionRate($dtmStockAppDate, $lngMonetaryRateCode, $lngMonetaryUnitCode, $objDB)
{
    if ($lngMonetaryUnitCode == 1) {
        return "1.000000";
    }
    $aryQuery = array();
    $aryQuery[] = "SELECT mmr.curConversionRate ";
    $aryQuery[] = "FROM m_MonetaryRate mmr ";
    $aryQuery[] = "WHERE mmr.dtmapplystartdate <= to_date('" . $dtmStockAppDate . "', 'yyyy/mm/dd') ";
    $aryQuery[] = "	AND mmr.dtmapplyenddate >= to_date('" . $dtmStockAppDate . "', 'yyyy/mm/dd') ";
    $aryQuery[] = "	AND mmr.lngmonetaryratecode = '" . $lngMonetaryRateCode . "' ";
    $aryQuery[] = "	AND mmr.lngMonetaryUnitCode = '" . $lngMonetaryUnitCode . "' ";
    $aryQuery[] = "GROUP BY mmr.curConversionRate ";
    $strQuery = implode("\n", $aryQuery);

    list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);
    if ($lngResultNum > 0) {
        $objResult = $objDB->fetchObject($lngResultID, 0);
        $curconversionrate = $objResult->curconversionrate;
    } else {
        $objDB->freeResult($lngResultID);
        unset($aryQuery);
        unset($objResult);
        $aryQuery[] = "SELECT mmr.curConversionRate ";
        $aryQuery[] = "FROM m_MonetaryRate mmr ";
        $aryQuery[] = "WHERE mmr.lngmonetaryratecode = '" . $lngMonetaryRateCode . "' ";
        $aryQuery[] = "	AND mmr.lngMonetaryUnitCode = '" . $lngMonetaryUnitCode . "' ";
        $aryQuery[] = "	AND mmr.dtmapplystartdate = (SELECT MAX(mmr2.dtmapplystartdate) FROM m_MonetaryRate mmr2 WHERE mmr2.lngMonetaryRateCode = mmr.lngMonetaryRateCode AND mmr2.lngMonetaryUnitCode = mmr.lngMonetaryUnitCode) ";
        $aryQuery[] = "GROUP by mmr.curConversionRate ";
        $strQuery = implode("\n", $aryQuery);
        list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);
        if ($lngResultNum > 0) {
            $objResult = $objDB->fetchObject($lngResultID, 0);
            $curconversionrate = $objResult->curconversionrate;
        }
    }

    $objDB->freeResult($lngResultID);

    return $curconversionrate;
}
