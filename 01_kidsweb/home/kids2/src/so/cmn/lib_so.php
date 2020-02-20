<?
/**
 *    受注　詳細、削除、無効化関数群
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
 *    修正履歴
 *
 *    2004.03.17    詳細表示時の単価部分の表示方式を小数点以下４桁に変更
 *    2004.03.29    検索結果一覧表示時の明細行番号部分を表示用ソートキーを表示するように変更
 *
 */

/**
 * 指定された受注番号から受注ヘッダ情報を取得するＳＱＬ文を作成
 *
 *    指定受注番号のヘッダ情報の取得用ＳＱＬ文作成関数
 *
 *    @param  Integer     $lngReceiveNo             取得する受注番号
 *    @return strQuery     $strQuery 検索用SQL文
 *    @access public
 */
function fncGetReceiveHeadNoToInfoSQL($lngReceiveNo, $lngRevisionNo)
{
    // SQL文の作成
    $aryQuery[] = "SELECT distinct on (r.lngReceiveNo) r.lngReceiveNo as lngReceiveNo, r.lngRevisionNo as lngRevisionNo";

    // 登録日
    $aryQuery[] = ", to_char( r.dtmInsertDate, 'YYYY/MM/DD HH24:MI:SS' ) as dtmInsertDate";
    // 計上日
    $aryQuery[] = ", to_char( r.dtmAppropriationDate, 'YYYY/MM/DD' ) as dtmReceiveAppDate";
    // 顧客受注番号
    $aryQuery[] = ", r.strCustomerReceiveCode as strCustomerReceiveCode";
    // 受注コード
    $aryQuery[] = ", r.strReceiveCode as strReceiveCode";
    // 製品コード
    $aryQuery[] = "  , p.strproductcode";
    $aryQuery[] = "  , p.strproductname";
    $aryQuery[] = "  , p.strrevisecode";
    // 入力者
    $aryQuery[] = ", r.lngInputUserCode as lngInputUserCode";
    $aryQuery[] = ", input_u.strUserDisplayCode as strInputUserDisplayCode";
    $aryQuery[] = ", input_u.strUserDisplayName as strInputUserDisplayName";
    // 顧客
    $aryQuery[] = ", r.lngCustomerCompanyCode as lngCustomerCode";
    $aryQuery[] = ", cust_c.strCompanyDisplayCode as strCustomerDisplayCode";
    $aryQuery[] = ", cust_c.strCompanyDisplayName as strCustomerDisplayName";
    // 部門
    $aryQuery[] = ", r.lngGroupCode as lngInChargeGroupCode";
    $aryQuery[] = ", inchg_g.strGroupDisplayCode as strInChargeGroupDisplayCode";
    $aryQuery[] = ", inchg_g.strGroupDisplayName as strInChargeGroupDisplayName";
    // 担当者
    $aryQuery[] = ", r.lngUserCode as lngInChargeUserCode";
    $aryQuery[] = ", inchg_u.strUserDisplayCode as strInChargeUserDisplayCode";
    $aryQuery[] = ", inchg_u.strUserDisplayName as strInChargeUserDisplayName";
    // 通貨
    $aryQuery[] = ", r.lngMonetaryUnitCode as lngMonetaryUnitCode";
    $aryQuery[] = ", mu.strMonetaryUnitName as strMonetaryUnitName";
    $aryQuery[] = ", mu.strMonetaryUnitSign as strMonetaryUnitSign";
    // レートタイプ
    $aryQuery[] = ", r.lngMonetaryRateCode as lngMonetaryRateCode";
    $aryQuery[] = ", mr.strMonetaryRateName as strMonetaryRateName";
    // 換算レート
    $aryQuery[] = ", r.curConversionRate as curConversionRate";
    // 状態
    $aryQuery[] = ", r.lngReceiveStatusCode as lngReceiveStatusCode";
    $aryQuery[] = ", rs.strReceiveStatusName as strReceiveStatusName";
    $aryQuery[] = " FROM m_Receive r ";
    if ($lngRevisionNo == "") {
        $aryQuery[] = "  inner join ( ";
        $aryQuery[] = "    select";
        $aryQuery[] = "      max(lngRevisionNo) lngRevisionNo";
        $aryQuery[] = "      , lngReceiveNo ";
        $aryQuery[] = "    from";
        $aryQuery[] = "      m_Receive";
        $aryQuery[] = "    group by";
        $aryQuery[] = "      lngReceiveNo";
        $aryQuery[] = "  ) r1";
        $aryQuery[] = "    on r.lngrevisionno = r1.lngRevisionNo ";
        $aryQuery[] = "    and r.lngReceiveNo = r1.lngReceiveNo ";
    }
    $aryQuery[] = "  LEFT JOIN t_ReceiveDetail rd on rd.lngreceiveno = r.lngreceiveno and rd.lngrevisionno = r.lngrevisionno ";
    $aryQuery[] = "  LEFT JOIN ( ";
    $aryQuery[] = "    select";
    $aryQuery[] = "      p1.strproductcode";
    $aryQuery[] = "      , p1.strproductname";
    $aryQuery[] = "	  , p1.strrevisecode";
    $aryQuery[] = "    from";
    $aryQuery[] = "      m_product p1 ";
    $aryQuery[] = "      inner join ( ";
    $aryQuery[] = "        select";
    $aryQuery[] = "          max(p2.lngRevisionNo) lngRevisionNo";
    $aryQuery[] = "          , p2.strproductcode";
    $aryQuery[] = "          , p2.strrevisecode ";
    $aryQuery[] = "        from";
    $aryQuery[] = "          m_Product p2 ";
    $aryQuery[] = "        where";
    $aryQuery[] = "          p2.bytinvalidflag = false ";
    $aryQuery[] = "          and not exists ( ";
    $aryQuery[] = "            select";
    $aryQuery[] = "              strproductcode ";
    $aryQuery[] = "            from";
    $aryQuery[] = "              m_product p3 ";
    $aryQuery[] = "            where";
    $aryQuery[] = "              p3.lngRevisionNo < 0 ";
    $aryQuery[] = "              and p3.strproductcode = p2.strproductcode ";
    $aryQuery[] = "              and p3.strrevisecode = p2.strrevisecode";
    $aryQuery[] = "          ) ";
    $aryQuery[] = "        group by";
    $aryQuery[] = "          p2.strProductCode";
    $aryQuery[] = "          , p2.strrevisecode";
    $aryQuery[] = "      ) p4 ";
    $aryQuery[] = "        on p1.lngRevisionNo = p4.lngRevisionNo ";
    $aryQuery[] = "        and p1.strproductcode = p4.strproductcode ";
    $aryQuery[] = "        and p1.strrevisecode = p4.strrevisecode";
    $aryQuery[] = "  ) p ";
    $aryQuery[] = "    ON rd.strProductCode = p.strProductCode ";
    $aryQuery[] = "    and rd.strrevisecode = p.strrevisecode ";
    $aryQuery[] = " LEFT JOIN m_User input_u ON r.lngInputUserCode = input_u.lngUserCode";
    $aryQuery[] = " LEFT JOIN m_Company cust_c ON r.lngCustomerCompanyCode = cust_c.lngCompanyCode";
    $aryQuery[] = " LEFT JOIN m_Group inchg_g ON r.lngGroupCode = inchg_g.lngGroupCode";
    $aryQuery[] = " LEFT JOIN m_User inchg_u ON r.lngUserCode = inchg_u.lngUserCode";
    $aryQuery[] = " LEFT JOIN m_ReceiveStatus rs USING (lngReceiveStatusCode)";
    $aryQuery[] = " LEFT JOIN m_MonetaryUnit mu ON r.lngMonetaryUnitCode = mu.lngMonetaryUnitCode";
    $aryQuery[] = " LEFT JOIN m_MonetaryRateClass mr ON r.lngMonetaryRateCode = mr.lngMonetaryRateCode";
    $aryQuery[] = " WHERE r.lngReceiveNo in (" . $lngReceiveNo . ")";
    if ($lngRevisionNo != "") {
        $aryQuery[] = " AND r.lngRevisionNo = " . $lngRevisionNo . "";
    }

    $strQuery = implode("\n", $aryQuery);

    return $strQuery;
}

/**
 * 指定された受注番号から受注ヘッダ情報を取得するＳＱＬ文を作成
 *
 *    指定受注番号のヘッダ情報の取得用ＳＱＬ文作成関数
 *
 *    @param  Integer     $lngReceiveNo             取得する受注番号
 *    @return strQuery     $strQuery 検索用SQL文
 *    @access public
 */
function fncGetReceiveHeadInfoSQL($lngReceiveNo, $lngestimateno)
{
    // SQL文の作成
    $aryQuery[] = "SELECT distinct on (r.lngReceiveNo) r.lngReceiveNo as lngReceiveNo, r.lngRevisionNo as lngRevisionNo";

    // 登録日
    $aryQuery[] = ", to_char( r.dtmInsertDate, 'YYYY/MM/DD HH24:MI:SS' ) as dtmInsertDate";
    // 計上日
    $aryQuery[] = ", to_char( r.dtmAppropriationDate, 'YYYY/MM/DD' ) as dtmReceiveAppDate";
    // 顧客受注番号
    $aryQuery[] = ", r.strCustomerReceiveCode as strCustomerReceiveCode";
    // 受注コード
    $aryQuery[] = ", r.strReceiveCode as strReceiveCode";
    // 製品番号
    $aryQuery[] = "  , p.lngProductNo";
    $aryQuery[] = "  , p.lngRevisionNo as lngProductRevisionNo";
    // 製品コード
    $aryQuery[] = "  , p.strproductcode";
    $aryQuery[] = "  , p.strproductname";
    $aryQuery[] = "  , p.strrevisecode";
    // 顧客品番
    $aryQuery[] = ", p.strGoodsCode as strGoodsCode";
    // 入力者
    $aryQuery[] = ", r.lngInputUserCode as lngInputUserCode";
    $aryQuery[] = ", input_u.strUserDisplayCode as strInputUserDisplayCode";
    $aryQuery[] = ", input_u.strUserDisplayName as strInputUserDisplayName";
    // 顧客
    $aryQuery[] = ", r.lngCustomerCompanyCode as lngCustomerCode";
    $aryQuery[] = ", cust_c.strCompanyDisplayCode as strCustomerDisplayCode";
    $aryQuery[] = ", cust_c.strshortname as strCustomerDisplayName";
    // 部門
    $aryQuery[] = ", r.lngGroupCode as lngInChargeGroupCode";
    $aryQuery[] = ", inchg_g.strGroupDisplayCode as strInChargeGroupDisplayCode";
    $aryQuery[] = ", inchg_g.strGroupDisplayName as strInChargeGroupDisplayName";
    // 担当者
    $aryQuery[] = ", r.lngUserCode as lngInChargeUserCode";
    $aryQuery[] = ", inchg_u.strUserDisplayCode as strInChargeUserDisplayCode";
    $aryQuery[] = ", inchg_u.strUserDisplayName as strInChargeUserDisplayName";
    // 開発担当者
    $aryQuery[] = ", p.lngdevelopusercode as lngdevelopusercode";
    $aryQuery[] = ", delp_u.strUserDisplayCode as strdevelopuserdisplaycode";
    $aryQuery[] = ", delp_u.strUserDisplayName as strdevelopuserdisplayname";
    // 通貨
    $aryQuery[] = ", r.lngMonetaryUnitCode as lngMonetaryUnitCode";
    $aryQuery[] = ", mu.strMonetaryUnitName as strMonetaryUnitName";
    $aryQuery[] = ", mu.strMonetaryUnitSign as strMonetaryUnitSign";
    // レートタイプ
    $aryQuery[] = ", r.lngMonetaryRateCode as lngMonetaryRateCode";
    $aryQuery[] = ", mr.strMonetaryRateName as strMonetaryRateName";
    // 換算レート
    $aryQuery[] = ", r.curConversionRate as curConversionRate";
    // 状態
    $aryQuery[] = ", r.lngReceiveStatusCode as lngReceiveStatusCode";
    $aryQuery[] = ", rs.strReceiveStatusName as strReceiveStatusName";
    $aryQuery[] = " FROM m_Receive r ";
    $aryQuery[] = "  inner join ( ";
    $aryQuery[] = "    select";
    $aryQuery[] = "      max(lngRevisionNo) lngRevisionNo";
    $aryQuery[] = "      , lngreceiveno ";
    $aryQuery[] = "    from";
    $aryQuery[] = "      m_Receive";
    $aryQuery[] = "    group by";
    $aryQuery[] = "      lngreceiveno";
    $aryQuery[] = "  ) r1";
    $aryQuery[] = "    on r.lngrevisionno = r1.lngRevisionNo ";
    $aryQuery[] = "    and r.lngreceiveno = r1.lngreceiveno ";
    $aryQuery[] = "  LEFT JOIN t_ReceiveDetail rd on rd.lngreceiveno = r.lngreceiveno and rd.lngrevisionno = r.lngrevisionno ";
    $aryQuery[] = "  LEFT JOIN t_estimatedetail ed ";
    $aryQuery[] = "    on rd.lngestimateno = ed.lngestimateno ";
    $aryQuery[] = "    and rd.lngestimatedetailno = ed.lngestimatedetailno ";
    $aryQuery[] = "    and rd.lngestimaterevisionno = ed.lngrevisionno ";
    $aryQuery[] = "  LEFT JOIN ( ";
    $aryQuery[] = "    select";
    $aryQuery[] = "      p1.strproductcode";
    $aryQuery[] = "      , p1.strproductname";
    $aryQuery[] = "      , p1.lnginchargegroupcode";
    $aryQuery[] = "      , p1.lnginchargeusercode";
    $aryQuery[] = "      , p1.lngdevelopusercode";
    $aryQuery[] = "      , p1.strgoodscode";
    $aryQuery[] = "	     , p1.strrevisecode";
    $aryQuery[] = "      , p1.lngProductNo";
    $aryQuery[] = "	     , p1.lngRevisionNo";
    $aryQuery[] = "    from";
    $aryQuery[] = "      m_product p1 ";
    $aryQuery[] = "      inner join ( ";
    $aryQuery[] = "        select";
    $aryQuery[] = "          max(p2.lngRevisionNo) lngRevisionNo";
    $aryQuery[] = "          , p2.strproductcode";
    $aryQuery[] = "          , p2.strrevisecode ";
    $aryQuery[] = "        from";
    $aryQuery[] = "          m_Product p2 ";
    $aryQuery[] = "        where";
    $aryQuery[] = "          p2.bytinvalidflag = false ";
    $aryQuery[] = "          and not exists ( ";
    $aryQuery[] = "            select";
    $aryQuery[] = "              strproductcode ";
    $aryQuery[] = "            from";
    $aryQuery[] = "              m_product p3 ";
    $aryQuery[] = "            where";
    $aryQuery[] = "              p3.lngRevisionNo < 0 ";
    $aryQuery[] = "              and p3.strproductcode = p2.strproductcode ";
    $aryQuery[] = "              and p3.strrevisecode = p2.strrevisecode";
    $aryQuery[] = "          ) ";
    $aryQuery[] = "        group by";
    $aryQuery[] = "          p2.strProductCode";
    $aryQuery[] = "          , p2.strrevisecode";
    $aryQuery[] = "      ) p4 ";
    $aryQuery[] = "        on p1.lngRevisionNo = p4.lngRevisionNo ";
    $aryQuery[] = "        and p1.strproductcode = p4.strproductcode ";
    $aryQuery[] = "        and p1.strrevisecode = p4.strrevisecode";
    $aryQuery[] = "  ) p ";
    $aryQuery[] = "    ON rd.strProductCode = p.strProductCode ";
    $aryQuery[] = "    and rd.strrevisecode = p.strrevisecode ";
    $aryQuery[] = " LEFT JOIN m_User input_u ON r.lngInputUserCode = input_u.lngUserCode";
    $aryQuery[] = " LEFT JOIN m_Company cust_c ON r.lngCustomerCompanyCode = cust_c.lngCompanyCode";
    $aryQuery[] = " LEFT JOIN m_Group inchg_g ON p.lnginchargegroupcode = inchg_g.lngGroupCode";
    $aryQuery[] = " LEFT JOIN m_User inchg_u ON p.lnginchargeusercode = inchg_u.lngUserCode";
    $aryQuery[] = " LEFT JOIN m_User delp_u ON p.lngdevelopusercode = delp_u.lngUserCode";
    $aryQuery[] = " LEFT JOIN m_ReceiveStatus rs USING (lngReceiveStatusCode)";
    $aryQuery[] = " LEFT JOIN m_MonetaryUnit mu ON r.lngMonetaryUnitCode = mu.lngMonetaryUnitCode";
    $aryQuery[] = " LEFT JOIN m_MonetaryRateClass mr ON r.lngMonetaryRateCode = mr.lngMonetaryRateCode";
    $aryQuery[] = " WHERE";
    $aryQuery[] = "  ed.lngsalesdivisioncode = ( ";
    $aryQuery[] = "    select distinct";
    $aryQuery[] = "      ed.lngsalesdivisioncode ";
    $aryQuery[] = "    from";
    $aryQuery[] = "      t_receivedetail rd ";
    $aryQuery[] = "      left join t_estimatedetail ed ";
    $aryQuery[] = "        on rd.lngestimateno = ed.lngestimateno ";
    $aryQuery[] = "        and rd.lngestimaterevisionno = ed.lngrevisionno ";
    $aryQuery[] = "        and rd.lngestimatedetailno = ed.lngestimatedetailno ";
    $aryQuery[] = "    where";
    $aryQuery[] = "      rd.lngreceiveno in (" . $lngReceiveNo . ")";
    $aryQuery[] = "  ) ";
    $aryQuery[] = " and r.lngreceivestatuscode = " . DEF_RECEIVE_APPLICATE .  " ";
    $aryQuery[] = " and rd.lngestimateno = " .$lngestimateno . " ";
    $aryQuery[] = " and r.lngcustomercompanycode != 0 ";

    $strQuery = implode("\n", $aryQuery);

    return $strQuery;
}
/**
 * 指定された受注番号から受注明細情報を取得するＳＱＬ文を作成
 *
 *    指定受注番号の明細情報の取得用ＳＱＬ文作成関数
 *
 *    @param  Integer     $lngReceiveNo             取得する受注番号
 *    @return strQuery     $strQuery 検索用SQL文
 *    @access public
 */
function fncGetReceiveDetailNoToInfoSQL($lngReceiveNo, $lngRevisionNo)
{
    // SQL文の作成
    $aryQuery[] = "SELECT rd.lngSortKey as lngRecordNo, ";
    $aryQuery[] = "rd.lngReceiveNo as lngReceiveNo, rd.lngRevisionNo as lngRevisionNo";
    $aryQuery[] = ", rd.lngreceivedetailno";
    // 製品コード・名称
    $aryQuery[] = ", rd.strProductCode || '_' || rd.strReviseCode as strProductCode";
    $aryQuery[] = ", p.strProductName as strProductName";
    $aryQuery[] = ", p.strproductenglishname as strproductenglishname";
    $aryQuery[] = ", me.lngProductQuantity as lngProductQuantity_est";
    $aryQuery[] = ", To_char( me.lngProductQuantity, '9,999,999,990' ) as lngProductQuantity_est2";
    $aryQuery[] = ", r.strCompanyDisplayCode as strCustomerDisplayCode"; 
    $aryQuery[] = ", r.lngMonetaryUnitCode";
    $aryQuery[] = ", r.strMonetaryUnitSign";
    // 顧客コード・名称
    $aryQuery[] = ", r.strCompanyDisplayName as strCustomerDisplayName";
    // 売上区分
    $aryQuery[] = ", rd.lngSalesClassCode as lngSalesClassCode";
    $aryQuery[] = ", ss.strSalesClassName as strSalesClassName";
    // 売上分類
    $aryQuery[] = ", sd.lngsalesdivisioncode";
    $aryQuery[] = ", sd.strsalesdivisionname";
    // 顧客品番
    $aryQuery[] = ", p.strGoodsCode as strGoodsCode";
    // 納期
    $aryQuery[] = ", rd.dtmDeliveryDate as dtmDeliveryDate";
    // 単価
    $aryQuery[] = ", rd.curProductPrice";
    // 単位
    $aryQuery[] = ", rd.lngProductUnitCode as lngProductUnitCode";
    $aryQuery[] = ", pu.strProductUnitName as strProductUnitName";
    $aryQuery[] = ", p.lngcartonquantity";// カートン入数
    // 数量
    $aryQuery[] = ", rd.lngProductQuantity as lngProductQuantity_pre";
    // 数量
    $aryQuery[] = ", To_char( rd.lngProductQuantity, '9,999,999,990' ) as lngProductQuantity";
    // 入数
    $aryQuery[] = ", To_char( rd.lngunitquantity, '9,999,999,990' ) as lngunitquantity";
    // 入数
    $aryQuery[] = ", rd.lngunitquantity as lngunitquantity_pre";
    // 税抜金額
    $aryQuery[] = ", rd.curSubTotalPrice";
    // 明細備考
    $aryQuery[] = ", rd.strNote as strDetailNote";

    // 明細行を表示する場合
    $aryQuery[] = " FROM t_ReceiveDetail rd ";
    if ($lngRevisionNo == "") {
        $aryQuery[] = "  inner join ( ";
        $aryQuery[] = "    select";
        $aryQuery[] = "      max(lngRevisionNo) lngRevisionNo";
        $aryQuery[] = "      , lngreceiveno ";
        $aryQuery[] = "    from";
        $aryQuery[] = "      t_ReceiveDetail ";
        $aryQuery[] = "    group by";
        $aryQuery[] = "      lngreceiveno";
        $aryQuery[] = "  ) rd1 ";
        $aryQuery[] = "    on rd.lngrevisionno = rd1.lngRevisionNo ";
        $aryQuery[] = "    and rd.lngreceiveno = rd1.lngreceiveno ";
    }
    $aryQuery[] = "  INNER JOIN ( ";
    $aryQuery[] = "    SELECT";
    $aryQuery[] = "      r1.*";
    $aryQuery[] = "      , m_MonetaryUnit.strMonetaryUnitSign";
    $aryQuery[] = "      , cust_c.strCompanyDisplayCode";
    $aryQuery[] = "      , cust_c.strshortname as strCompanyDisplayName ";
    $aryQuery[] = "    from";
    $aryQuery[] = "      m_Receive r1 ";
    $aryQuery[] = "      inner join ( ";
    $aryQuery[] = "        select";
    $aryQuery[] = "          max(lngrevisionno) lngrevisionno";
    $aryQuery[] = "          , lngreceiveno ";
    $aryQuery[] = "        from";
    $aryQuery[] = "          m_Receive ";
    $aryQuery[] = "        group by";
    $aryQuery[] = "          lngreceiveno";
    $aryQuery[] = "      ) r2 ";
    $aryQuery[] = "        on r1.lngrevisionno = r2.lngrevisionno ";
    $aryQuery[] = "        and r1.lngreceiveno = r2.lngreceiveno ";
    $aryQuery[] = "      LEFT JOIN m_MonetaryUnit ";
    $aryQuery[] = "        USING (lngMonetaryUnitCode) ";
    $aryQuery[] = "      LEFT JOIN m_Company cust_c ";
    $aryQuery[] = "        ON r1.lngCustomerCompanyCode = cust_c.lngCompanyCode ";
    $aryQuery[] = "     and r1.lngcustomercompanycode != 0 ";
    $aryQuery[] = "  ) r ";
    $aryQuery[] = "    on rd.lngreceiveno = r.lngreceiveno ";
    $aryQuery[] = "    and rd.lngRevisionNo = r.lngRevisionNo ";
    $aryQuery[] = " LEFT JOIN (";
    $aryQuery[] = "   select p1.*  from m_product p1 ";
    $aryQuery[] = "   inner join (select max(lngrevisionno) lngrevisionno, strproductcode, strrevisecode from m_Product group by strProductCode, strrevisecode) p2";
    $aryQuery[] = "   on p1.lngrevisionno = p2.lngrevisionno and p1.strproductcode = p2.strproductcode and p1.strrevisecode = p2.strrevisecode";
    $aryQuery[] = ") p ";
    $aryQuery[] = " ON rd.strProductCode = p.strProductCode and rd.strrevisecode = p.strrevisecode ";
    $aryQuery[] = "  LEFT JOIN t_estimatedetail me ";
    $aryQuery[] = "    on rd.lngestimateno = me.lngestimateno ";
    $aryQuery[] = "    and rd.lngestimatedetailno = me.lngestimatedetailno ";
    $aryQuery[] = "    and rd.lngestimaterevisionno = me.lngrevisionno ";
    $aryQuery[] = " LEFT JOIN m_SalesClass ss on rd.lngSalesClassCode = ss.lngSalesClassCode";
    $aryQuery[] = "  LEFT JOIN m_salesclassdivisonlink ssdl ";
    $aryQuery[] = "    on ssdl.lngSalesClassCode = me.lngSalesClassCode ";
    $aryQuery[] = "    and ssdl.lngsalesdivisioncode = me.lngsalesdivisioncode ";
    $aryQuery[] = " LEFT JOIN m_salesdivision sd on sd.lngsalesdivisioncode = ssdl.lngsalesdivisioncode";
    $aryQuery[] = " LEFT JOIN m_ProductUnit pu ON rd.lngProductUnitCode = pu.lngProductUnitCode";
    $aryQuery[] = " WHERE rd.lngReceiveNo in (" . $lngReceiveNo . ") ";
    $aryQuery[] = " AND r.lngReceiveNo not in (select lngReceiveNo from m_receive where lngrevisionno < 0) ";
    if ($lngRevisionNo != "") {
        $aryQuery[] = " AND rd.lngRevisionNo = " . $lngRevisionNo . "";
    }
    $aryQuery[] = " ORDER BY rd.lngSortKey ASC ";

    $strQuery = implode("\n", $aryQuery);

    return $strQuery;
}

/**
 * 詳細表示関数（ヘッダ用）
 *
 *    テーブル構成で受注データ詳細を出力する関数
 *    ヘッダ行を表示する
 *
 *    @param  Array     $aryResult                 ヘッダ行の検索結果が格納された配列
 *    @access public
 */
function fncSetReceiveHeadTabelData($aryResult)
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
        else if ($strColumnName == "dtmreceiveappdate") {
            $aryNewResult[$strColumnName] = str_replace("-", "/", $aryResult["dtmreceiveappdate"]);
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

        // 顧客
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

        // 合計金額
        else if ($strColumnName == "curtotalprice") {
            $aryNewResult[$strColumnName] = $aryResult["strmonetaryunitsign"] . " ";
            if (!$aryResult["curtotalprice"]) {
                $aryNewResult[$strColumnName] .= "0.00";
            } else {
                $aryNewResult[$strColumnName] .= $aryResult["curtotalprice"];
            }
        }

        // 状態
        else if ($strColumnName == "lngreceivestatuscode") {
            $aryNewResult[$strColumnName] = $aryResult["strreceivestatusname"];
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
 *    テーブル構成で受注データ詳細を出力する関数
 *    明細行を表示する
 *
 *    @param  Array     $aryDetailResult     明細行の検索結果が格納された配列（１データ分）
 *    @param  Array     $aryHeadResult         ヘッダ行の検索結果が格納された配列（参照用）
 *    @access public
 */
function fncSetReceiveDetailTabelData($aryDetailResult, $aryHeadResult)
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

        // 売上区分
        else if ($strColumnName == "lngsalesclasscode") {
            if ($aryDetailResult["lngsalesclasscode"]) {
                $aryNewDetailResult[$strColumnName] = "[" . $aryDetailResult["lngsalesclasscode"] . "]";
            } else {
                $aryNewDetailResult[$strColumnName] = "      ";
            }
            $aryNewDetailResult[$strColumnName] .= " " . $aryDetailResult["strsalesclassname"];
        }

        // 顧客品番
        else if ($strColumnName == "strgoodscode") {
            $aryNewDetailResult[$strColumnName] = $aryDetailResult[$strColumnName];
        }

        // 納期
        else if ($strColumnName == "dtmdeliverydate") {
            $aryNewDetailResult[$strColumnName] = str_replace("-", "/", $aryDetailResult["dtmdeliverydate"]);
        }

        // 単価
        else if ($strColumnName == "curproductprice") {
            if (!$aryDetailResult["curproductprice"]) {
                $aryNewDetailResult[$strColumnName] = convertPrice($aryDetailResult["lngmonetaryunitcode"], $aryDetailResult["strmonetaryunitsign"], 0, "unitprice");
            } else {
                $aryNewDetailResult[$strColumnName] = convertPrice($aryDetailResult["lngmonetaryunitcode"], $aryDetailResult["strmonetaryunitsign"], $aryDetailResult["curproductprice"], "unitprice");
            }
        }

        // 単位
        else if ($strColumnName == "lngproductunitcode") {
            $aryNewDetailResult[$strColumnName] = $aryDetailResult["strproductunitname"];
        }

        // 税抜金額
        else if ($strColumnName == "cursubtotalprice") {
            if (!$aryDetailResult["cursubtotalprice"]) {
                $aryNewDetailResult[$strColumnName] = convertPrice($aryDetailResult["lngmonetaryunitcode"], $aryDetailResult["strmonetaryunitsign"], 0, "price");
            } else {
                $aryNewDetailResult[$strColumnName] = convertPrice($aryDetailResult["lngmonetaryunitcode"], $aryDetailResult["strmonetaryunitsign"], $aryDetailResult["cursubtotalprice"], "price");;
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
 * 詳細表示用カラム名セット関数
 *
 *    詳細表示時のカラム名（日本語、英語）での設定関数
 *
 *    @param  Array     $aryResult         検索結果が格納された配列
 *    @param  Array     $aryTytle         カラム名が格納された配列
 *    @access public
 */
function fncSetReceiveTabelName($aryResult, $aryTytle)
{
    $aryColumnNames = array_values($aryResult);

    // 表示対象カラムの配列より結果の出力
    for ($i = 0; $i < count($aryColumnNames); $i++) {
        $strColumnName = $aryColumnNames[$i];

        if ($aryTytle[$strColumnName]) {
            $strNewColumnName = "CN" . $strColumnName;
            $aryNames[$strNewColumnName] = $aryTytle[$strColumnName];
        }
    }

    return $aryNames;
}

/**
 * 指定のコードのデータを他のマスタで使用しているコード取得
 *
 *    指定コードに対して、指定されたマスタの検索関数
 *
 *    @param  String         $strCode         検索対象コード
 *    @param    Integer        $lngMode        検索モード    1:受注コードから仕入マスタ    （順次追加）
 *    @param  Object        $objDB            DBオブジェクト
 *    @return Array         $aryCode        検索対象コードが使用されているマスタ内のコードの配列
 *    @access public
 */
function fncGetDeleteCodeToMaster($strCode, $lngMode, $objDB)
{
    // SQL文の作成
    $strQuery = "SELECT distinct on (";
    switch ($lngMode) {
        case 1: // 受注コードから売上マスタの検索時
            $strQuery .= "s.strSalesCode) s.strSalesCode as lngSearchNo FROM m_Sales s, m_Receive r ";
            $strQuery .= "WHERE s.lngReceiveNo = r.lngReceiveNo AND s.bytInvalidFlag = FALSE AND r.strReceiveCode = '";
            break;
    }
    $strQuery .= $strCode . "'";

    // 検索クエリーの実行
    list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);

    if ($lngResultNum) {
        for ($i = 0; $i < $lngResultNum; $i++) {
            $aryCode[] = $objDB->fetchArray($lngResultID, $i);
        }
    } else {
        $aryCode = false;
    }
    $objDB->freeResult($lngResultID);

    return $aryCode;
}

/**
 * 指定のNOのデータを他のマスタで使用しているコード取得
 *
 *    指定NOに対して、指定されたマスタの検索関数
 *
 *    @param  Integer     $lngNo             検索対象No
 *    @param    Integer        $lngMode        検索モード    1:受注コードから仕入マスタ    （順次追加）
 *    @param  Object        $objDB            DBオブジェクト
 *    @return Array         $aryCode        検索対象コードが使用されているマスタ内のコードの配列
 *    @access public
 */
function fncGetDeleteNoToMaster($lngNo, $lngMode, $objDB)
{
    // SQL文の作成
    $strQuery = "SELECT distinct on (";
    switch ($lngMode) {
        case 1: // 受注Noから仕入マスタの検索時
            $strQuery .= "s.lngReceiveNo) s.lngReceiveNo as lngSearchNo FROM m_Sales s ";
            $strQuery .= "WHERE s.bytInvalidFlag = FALSE AND s.lngReceiveNo = ";
            break;
        case 2: // 受注Noから売上マスタの検索時
            $strQuery .= "s.lngReceiveNo) s.lngReceiveNo as lngSearchNo FROM m_Sales s ";
            $strQuery .= "WHERE s.bytInvalidFlag = FALSE AND s.lngReceiveNo = ";
            break;
    }
    $strQuery .= $lngNo;

    // 検索クエリーの実行
    list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);

    if ($lngResultNum) {
        for ($i = 0; $i < $lngResultNum; $i++) {
            $aryCode[] = $objDB->fetchArray($lngResultID, $i);
        }
    } else {
        $aryCode = false;
    }
    $objDB->freeResult($lngResultID);

    return $aryCode;
}

/**
 * 指定の受注データについて無効化することでどうなるかケースわけする
 *
 *    指定の受注データの状態を調査し、ケースわけする関数
 *
 *    @param  Array         $aryReceiveData     受注データ
 *    @param  Object        $objDB            DBオブジェクト
 *    @return Integer     $lngCase        状態のケース
 *                                        1: 対象受注データを無効化しても、最新の受注データが影響受けない
 *                                        2: 対象受注データを無効化することで、最新の受注データが入れ替わる
 *                                        3: 対象受注データが削除データで、受注が復活する
 *                                        4: 対象受注データを無効化することで、最新の受注データになりうる受注データがない
 *    @access public
 */
function fncGetInvalidCodeToMaster($aryReceiveData, $objDB)
{
    // 受注コードの取得
    $strReceiveCode = $aryReceiveData["strreceivecode2"];

    // 削除対象受注と同じ受注コードの最新の受注Noを調べる
    $strQuery = "SELECT lngReceiveNo FROM m_Receive r WHERE r.strReceiveCode = '" . $strReceiveCode . "' AND r.bytInvalidFlag = FALSE ";
    $strQuery .= " AND r.lngRevisionNo >= 0";
    $strQuery .= " AND r.lngRevisionNo = ( "
        . "SELECT MAX( r1.lngRevisionNo ) FROM m_Receive r1 WHERE r1.strReceiveCode = r.strReceiveCode ";
    $strQuery .= " AND r1.strReviseCode = ( "
        . "SELECT MAX( r2.strReviseCode ) FROM m_Receive r2 WHERE r2.strReceiveCode = r1.strReceiveCode ) )";

    // 検索クエリーの実行
    list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);

    if ($lngResultNum == 1) {
        $objResult = $objDB->fetchObject($lngResultID, 0);
        $lngNewReceiveNo = $objResult->lngreceiveno;
    } else {
        $lngCase = 4;
    }
    $objDB->freeResult($lngResultID);

    // 削除対象が最新かどうかのチェック
    if ($lngCase != 4) {
        if ($lngNewReceiveNo == $aryReceiveData["lngreceiveno"]) {
            // 最新の場合
            // 削除対象受注以外でと同じ受注コードの最新の受注Noを調べる
            $strQuery = "SELECT lngReceiveNo FROM m_Receive r WHERE r.strReceiveCode = '" . $strReceiveCode . "' AND r.bytInvalidFlag = FALSE ";
            $strQuery .= " AND r.lngReceiveNo <> " . $aryReceiveData["lngreceiveno"] . " AND r.lngRevisionNo >= 0";
            $strQuery .= " AND r.lngRevisionNo = ( "
                . "SELECT MAX( r1.lngRevisionNo ) FROM m_Receive r1 WHERE r1.strReceiveCode = r.strReceiveCode ";
            $strQuery .= " AND r1.strReviseCode = ( "
                . "SELECT MAX( r2.strReviseCode ) FROM m_Receive r2 WHERE r2.strReceiveCode = r1.strReceiveCode ) )";

            // 検索クエリーの実行
            list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);

            if ($lngResultNum >= 1) {
                $lngCase = 2;
            } else {
                $lngCase = 4;
            }
            $objDB->freeResult($lngResultID);
        }
        // 対象受注が削除データかどうかの確認
        else if ($aryReceiveData["lngrevisionno"] < 0) {
            $lngCase = 3;
        } else {
            $lngCase = 1;
        }
    }

    return $lngCase;
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
