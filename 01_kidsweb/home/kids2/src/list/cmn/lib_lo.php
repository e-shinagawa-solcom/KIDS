<?
/**
 *    帳票出力用ライブラリ
 *
 *    帳票出力用　定義　関数ライブラリ
 *
 *    @package   kuwagata
 *    @license   http://www.wiseknot.co.jp/
 *    @copyright Copyright &copy; 2003, Wiseknot
 *    @author    Keiji Suzukaze <k-suzukaze@wiseknot.co.jp>
 *    @access    public
 *    @version   1.00
 *
 */

// 帳票出力用定義
$aryListOutputMenu = array
    (
    DEF_REPORT_PRODUCT => array
    (
        "name" => "商品管理 商品企画書",
        "file" => "p",
    ),

    DEF_REPORT_ORDER => array
    (
        "name" => "発注管理 発注書",
        "file" => "po",
    ),

    DEF_REPORT_ESTIMATE => array
    (
        "name" => "見積原価管理",
        "file" => "estimate",
    ),
);

// -----------------------------------------------------------------
/**
 *    発注データ承認状態チェック関数
 *
 *    指定した発注データの承認状態をチェックする関数
 *
 *    @param  Integer $lngOrderNo 発注ナンバー
 *    @param  Object  $objDB      DBオブジェクト
 *    @return Boolean $bytApproval  承認状態(TRUE:承認斉 FALSE:未承認)
 *    @access public
 */
// -----------------------------------------------------------------
function fncCheckApprovalProductOrder($lngOrderNo, $objDB)
{
    // 発注ワークフローの承認状態チェッククエリ生成
    $aryQuery[] = "SELECT o.lngOrderNo ";
    $aryQuery[] = "FROM m_Order o ";

    // 指定発注No
    $aryQuery[] = "WHERE o.lngOrderNo = " . $lngOrderNo;

    // A:「発注」状態より大きい状態の発注データ
    // B:「発注」状態のデータ
    // C:ワークフローに存在しない(即認証案件)
    // D:「承認」状態にある案件
    // A OR ( B AND ( C OR D ) )
    $aryQuery[] = " AND (";

    // A:「発注」状態より大きい状態の発注データ
    $aryQuery[] = "  o.lngOrderStatusCode > " . DEF_ORDER_ORDER;

    $aryQuery[] = "  OR";
    $aryQuery[] = "  (";

    // B:「発注」状態のデータ
    $aryQuery[] = "    o.lngOrderStatusCode = " . DEF_ORDER_ORDER;
    $aryQuery[] = "     AND";
    $aryQuery[] = "    (";

    // C:ワークフローに存在しない(即認証案件)
    $aryQuery[] = "      0 = ";
    $aryQuery[] = "      (";
    $aryQuery[] = "        SELECT COUNT ( mw.lngWorkflowCode ) ";
    $aryQuery[] = "        FROM m_Workflow mw ";
// レプリケーションサーバーで、Indexが作成出来ない障害対応 - to_number を消した
    // CREATE INDEX m_workflow_strworkflowkeycode_index ON m_workflow USING btree (to_number(strworkflowkeycode, '9999999'::text));
    //    $aryQuery[] = "        WHERE to_number ( mw.strWorkflowKeyCode, '9999999') = o.lngOrderNo";
    $aryQuery[] = "        WHERE mw.strWorkflowKeyCode = trim(to_char(o.lngOrderNo, '9999999'))";
    $aryQuery[] = "         AND mw.lngFunctionCode = " . DEF_FUNCTION_PO1;
    $aryQuery[] = "      )";

    // D:「承認」状態にある案件
    $aryQuery[] = "      OR " . DEF_STATUS_APPROVE . " = ";
    $aryQuery[] = "      (";
    $aryQuery[] = "        SELECT tw.lngWorkflowStatusCode";
    $aryQuery[] = "        FROM m_Workflow mw2, t_Workflow tw";
// レプリケーションサーバーで、Indexが作成出来ない障害対応 - to_number を消した
    //    $aryQuery[] = "        WHERE to_number ( mw2.strWorkflowKeyCode, '9999999') = o.lngOrderNo";
    $aryQuery[] = "        WHERE mw2.strWorkflowKeyCode = trim(to_char(o.lngOrderNo, '9999999'))";
    $aryQuery[] = "         AND mw2.lngFunctionCode = " . DEF_FUNCTION_PO1;
    $aryQuery[] = "         AND tw.lngWorkflowSubCode =";
    $aryQuery[] = "        (";
    $aryQuery[] = "          SELECT MAX ( tw2.lngWorkflowSubCode ) FROM t_Workflow tw2 WHERE tw.lngWorkflowCode = tw2.lngWorkflowCode";
    $aryQuery[] = "        )";
    $aryQuery[] = "         AND mw2.lngWorkflowCode = tw.lngWorkflowCode";
    $aryQuery[] = "      )";
    $aryQuery[] = "    )";
    $aryQuery[] = "  )";
    $aryQuery[] = ")";

    // 過去リバイズ発注書の非表示処理
    // 指定lngOrderNoと同じ発注コード内において、
    // リビジョンNOが最大のものかどうかのチェック
    $aryQuery[] = " AND o.lngRevisionNo = ";
    $aryQuery[] = "(";
    $aryQuery[] = "  SELECT MAX ( o2.lngRevisionNo )";
    $aryQuery[] = "  FROM m_Order o2";
    $aryQuery[] = "  WHERE o.strOrderCode = o2.strOrderCode AND o2.bytInvalidFlag = false";
    $aryQuery[] = ")";

    // 取消発注書の非表示処理
    // 指定lngOrderNoと同じ発注コード内において、
    // リビジョンNOが最小のものが0以上(発注取消案件以外)かどうかのチェック
    $aryQuery[] = " AND 0 <= ";
    $aryQuery[] = "(";
    $aryQuery[] = "  SELECT MIN ( o3.lngRevisionNo )";
    $aryQuery[] = "  FROM m_Order o3";
    $aryQuery[] = "  WHERE o.strOrderCode = o3.strOrderCode AND o3.bytInvalidFlag = false";
    $aryQuery[] = ")";

    $strQuery = join("", $aryQuery);

    list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);

    // デフォルト FALSE を設定
    $bytApproval = false;

    // 結果レコードが1だった場合(複数存在はイリーガルである可能性有り)、TRUE
    if ($lngResultNum == 1) {
        $bytApproval = true;
        $objDB->freeResult($lngResultID);
    }

    return $bytApproval;
}

// -----------------------------------------------------------------
/**
 *    帳票出力コピーファイルパス取得クエリ生成関数
 *
 *    指定した帳票コピーファイルパスを取得するクエリを生成する関数
 *
 *    @param  Integer $lngReportClassCode 帳票区分コード
 *    @param  String  $strReportKeyCode   帳票キーコード
 *    @param  Integer $lngReportCode      帳票コード
 *    @return String                      帳票出力コピーファイルパス取得クエリ
 *    @access public
 */
// -----------------------------------------------------------------
function fncGetCopyFilePathQuery($lngReportClassCode, $strReportKeyCode, $lngReportCode)
{

    // 帳票コードが真の場合、その条件クエリ分生成
    if ($lngReportCode) {
        $strReportCodeConditions = " AND r.lngReportCode = " . $lngReportCode;
    }

    // 商品企画書出力コピーファイル取得クエリ生成
    if ($lngReportClassCode == DEF_REPORT_PRODUCT) {
        $aryQuery[] = "SELECT r.strReportPathName ";
        $aryQuery[] = "FROM t_GoodsPlan gp, t_Report r ";
        $aryQuery[] = "WHERE r.lngReportClassCode = " . $lngReportClassCode;
        $aryQuery[] = $strReportCodeConditions;

        // 指定製品コード
        $aryQuery[] = " AND gp.lngProductNo = " . $strReportKeyCode;
        // 最新リビジョン
        $aryQuery[] = " AND lngRevisionNo = ( SELECT MAX ( gp2.lngRevisionNo ) FROM t_GoodsPlan gp2 WHERE gp.lngProductNo = gp2.lngProductNo )";

        // 帳票キーコードと製品企画コード結合
        $aryQuery[] = " AND to_number ( r.strReportKeyCode, '9999999') = gp.lngGoodsPlanCode";
    }

    // 発注帳票出力コピーファイル取得クエリ生成
    elseif ($lngReportClassCode == DEF_REPORT_ORDER) {
        $aryQuery[] = "SELECT r.strReportPathName ";
        $aryQuery[] = "FROM m_Order o, t_Report r ";

        // 対象帳票(製品企画 or 発注)指定
        $aryQuery[] = "WHERE r.lngReportClassCode = " . $lngReportClassCode;

        $aryQuery[] = $strReportCodeConditions;

        // 帳票コード指定
        $aryQuery[] = " AND r.strReportKeyCode = '" . $strReportKeyCode . "'";

        // A:「発注」状態より大きい状態の発注データ
        // B:「発注」状態のデータ
        // A OR B
        $aryQuery[] = " AND (";
        // A:「発注」状態より大きい状態の発注データ
        $aryQuery[] = " o.lngOrderStatusCode > " . DEF_ORDER_ORDER;
        $aryQuery[] = " OR";
        // B:「発注」状態のデータ
        $aryQuery[] = " o.lngOrderStatusCode = " . DEF_ORDER_ORDER;
        $aryQuery[] = ")";
        // リビジョンにマイナスの無い
        $aryQuery[] = " AND 0 <= ";
        $aryQuery[] = "( ";
        $aryQuery[] = "  SELECT MIN( o3.lngRevisionNo ) FROM m_Order o3 WHERE o3.bytInvalidFlag = false AND o3.strOrderCode = o.strOrderCode ";
        $aryQuery[] = ")";
        $aryQuery[] = " AND  r.strReportKeyCode = trim(to_char(o.lngOrderNo, '9999999'))";
    }

    // 見積原価帳票出力コピーファイル取得クエリ生成
    elseif ($lngReportClassCode == DEF_REPORT_ESTIMATE) {
        $aryQuery[] = "SELECT r.strReportPathName ";
        $aryQuery[] = "FROM t_Report r ";
        $aryQuery[] = "LEFT OUTER JOIN m_Estimate e ON ( to_number ( r.strReportKeyCode, '9999999') = e.lngEstimateNo ) ";
        $aryQuery[] = "WHERE r.lngReportClassCode = " . $lngReportClassCode;
        $aryQuery[] = $strReportCodeConditions;

        // 指定製品コード
        $aryQuery[] = " AND e.lngEstimateNo = " . $strReportKeyCode;
    }
    return join("", $aryQuery);
}

// -----------------------------------------------------------------
/**
 *    帳票クエリ生成関数
 *
 *    出力対象の帳票クエリを生成する関数
 *
 *    @param  Integer $lngClassCode 帳票区分コード
 *    @param  Integer $lngKeyCode   帳票キーコード
 *    @param  Object  $objDB        DBオブジェクト
 *    @return String                クエリ
 *    @access public
 */
// -----------------------------------------------------------------
function fncGetListOutputQuery($lngClassCode, $lngKeyCode, $objDB)
{
    /////////////////////////////////////////////////////////////////////////
    // 商品化企画書
    /////////////////////////////////////////////////////////////////////////
    // 顧客品番
    // 更新日            dtmUpdateDate
    // 商品名            strProductEnglishName
    // 製品コード        strProductCode - strGoodsCode
    // 製品名(日本語)    strProductName
    // 製品名(英語)        strProductEnglishName
    // 部門                strGroupDisplayCode strGroupDisplayName
    // 担当者            strUserDisplayCode strUserDisplayName
    // 顧客                strCompanyDisplayCode strCompanyDisplayName
    // 顧客担当者        strUserDisplayCode strUserDisplayName
    // 商品形態            strProductFormName
    // 内箱(袋)入数        lngBoxQuantity strProductUnitName
    // カートン入数        lngCartonQuantity strProductUnitName
    // 生産予定数        lngProductionQuantity
    // 初回納品数        lngFirstDeliveryQuantity
    // 納期                dtmDeliveryLimitDate
    // 生産工場            strCompanyDisplayCode strCompanyDisplayName
    // ｱｯｾﾝﾌﾞﾘ工場        strCompanyDisplayCode strCompanyDisplayName
    // 納品場所            strCompanyDisplayCode strCompanyDisplayName
    // 納価                curProductPrice
    // 上代                curRetailPrice
    // 対象年齢            strTargetAgeName
    // ﾛｲﾔﾘﾃｨ(%)        lngRoyalty
    // 証紙                strCertificateClassName
    // 版権元            strCopyrightName
    // 版権表示(刻印)    strCopyrightDisplayStamp
    // 版権表示(印刷物)    strCopyrightDisplayPrint
    // 製品構成            strProductComposition
    // ｱｯｾﾝﾌﾞﾘ内容        strAssemblyContents
    // 仕様詳細            strSpecificationDetails
    if ($lngClassCode == DEF_REPORT_PRODUCT) {
        $aryQuery[] = "SELECT DISTINCT ON (p.lngProductNo)";
        $aryQuery[] = "   p.lngProductNo";
        $aryQuery[] = " , p.lngInChargeGroupCode as lngGroupCode";
        //  作成日
        $aryQuery[] = " , To_Char( p.dtminsertdate, 'YYYY/MM/DD' ) as dtminsertdate";
        //  更新日
        $aryQuery[] = " , To_Char( p.dtmUpdateDate, 'YYYY/MM/DD' ) as dtmUpdateDate";
        //  企画進行状況
        $aryQuery[] = " , t_gp.lngGoodsPlanProgressCode";
        //  改訂番号
        $aryQuery[] = " , t_gp.lngRevisionNo";
        //  改訂日時
        $aryQuery[] = " , To_Char( p.dtmUpdateDate, 'YYYY/MM/DD' ) as dtmRevisionDate";
        //  製品コード
        $aryQuery[] = " , p.strProductCode";
        //  製品名称
        $aryQuery[] = " , p.strProductName";
        //  製品名称（英語）
        $aryQuery[] = " , p.strProductEnglishName";
        //  入力者
        $aryQuery[] = " , input_u.strUserDisplayCode as strInputUserDisplayCode";
        $aryQuery[] = " , input_u.strUserDisplayName as strInputUserDisplayName";
        $aryQuery[] = " , p.lnginputusercode";
        //  部門
        $aryQuery[] = " , inchg_g.strGroupDisplayCode as strInChargeGroupDisplayCode";
        $aryQuery[] = " , inchg_g.strGroupDisplayName as strInChargeGroupDisplayName";
        //  担当者
        $aryQuery[] = " , inchg_u.strUserDisplayCode as strInChargeUserDisplayCode";
        $aryQuery[] = " , inchg_u.strUserDisplayName as strInChargeUserDisplayName";
        //  開発担当者
        $aryQuery[] = " , devp_u.strUserDisplayCode as strDevelopUserDisplayCode";
        $aryQuery[] = " , devp_u.strUserDisplayName as strDevelopUserDisplayName";
        $aryQuery[] = " , category.strCategoryName as strCategoryName";
        //  顧客品番
        $aryQuery[] = " , p.strGoodsCode";
        $aryQuery[] = " , cust_c.strDistinctCode";
        //  商品名称
        $aryQuery[] = " , p.strGoodsName";
        //  顧客
        $aryQuery[] = " , cust_c.strCompanyDisplayCode as strCustomerCompanyDisplayCode";
        $aryQuery[] = " , cust_c.strCompanyDisplayName as strCustomerCompanyDisplayName";
        //  顧客担当者
        $aryQuery[] = " , cust_u.strUserDisplayCode as strCustomerUserDisplayCode";
        $aryQuery[] = " , cust_u.strUserDisplayName as strCustomerUserDisplayName";
        $aryQuery[] = " , p.lngCustomerUserCode";
        $aryQuery[] = " , p.strCustomerUserName";
        //  荷姿単位
        $aryQuery[] = " , packingunit.strProductUnitName as strPackingUnitName";
        //  製品単位
        $aryQuery[] = " , productunit.strProductUnitName as strProductUnitName";
        //  商品形態
        $aryQuery[] = " , productform.strProductFormName";
        //  内箱（袋）入数
        $aryQuery[] = " , To_char( p.lngBoxQuantity, '9,999,999,990' ) as lngBoxQuantity";
        //  カートン入数
        $aryQuery[] = " , To_char( p.lngCartonQuantity, '9,999,999,990' ) as lngCartonQuantity";
        //  生産予定数
        $aryQuery[] = " , To_char( p.lngProductionQuantity, '9,999,999,990' ) as lngProductionQuantity";
        $aryQuery[] = " , productionunit.strProductUnitName AS strProductionUnitName";
        //  初回納品数
        $aryQuery[] = " , To_char( p.lngFirstDeliveryQuantity, '9,999,999,990' ) as lngFirstDeliveryQuantity";
        $aryQuery[] = " , firstdeliveryunit.strProductUnitName AS strFirstDeliveryUnitName";
        //  生産工場
        $aryQuery[] = " , fact_c.strCompanyDisplayCode as strFactoryDisplayCode";
        $aryQuery[] = " , fact_c.strCompanyDisplayName as strFactoryDisplayName";
        //  アッセンブリ工場
        $aryQuery[] = " , assemfact_c.strCompanyDisplayCode as strAssemblyFactoryDisplayCode";
        $aryQuery[] = " , assemfact_c.strCompanyDisplayName as strAssemblyFactoryDisplayName";
        //  納品場所
        $aryQuery[] = " , delv_c.strCompanyDisplayCode as strDeliveryPlaceDisplayCode";
        $aryQuery[] = " , delv_c.strCompanyDisplayName as strDeliveryPlaceDisplayName";
        //  納期
        $aryQuery[] = " , To_Char( p.dtmDeliveryLimitDate, 'YYYY/MM' ) as dtmDeliveryLimitDate";
        //  納価
        $aryQuery[] = " , To_char( p.curProductPrice, '9,999,999,990.99' )  as curProductPrice";
        //  上代
        $aryQuery[] = " , To_char( p.curRetailPrice, '9,999,999,990.99' )  as curRetailPrice";
        //  対象年齢
        $aryQuery[] = " , targetage.strTargetAgeName";
        //  ロイヤリティ
        $aryQuery[] = " , To_char( p.lngRoyalty, '9,999,999,990.99' )  as lngRoyalty";
        //  証紙
        $aryQuery[] = " , certificate.strCertificateClassName";
        //  版権元
        $aryQuery[] = " , copyright.strCopyrightName";
        //  版権元備考
        $aryQuery[] = " , p.strCopyrightNote";
        //  版権表示（刻印）
        $aryQuery[] = " , p.strCopyrightDisplayStamp";
        //  版権表示（印刷物）
        $aryQuery[] = " , p.strCopyrightDisplayPrint";
        //  製品構成
        $aryQuery[] = " , p.strProductComposition";
        //  アッセンブリ内容
        $aryQuery[] = " , p.strAssemblyContents";
        //  仕様詳細
        $aryQuery[] = " , p.strSpecificationDetails ";

        $aryQuery[] = " , t_gp.lngGoodsPlanCode ";

        $aryQuery[] = "FROM m_Product p";

        //  追加表示用の参照マスタ対応
        $aryQuery[] = " LEFT JOIN m_User input_u ON p.lngInputUserCode = input_u.lngUserCode";
        $aryQuery[] = " LEFT JOIN m_Group inchg_g ON p.lngInChargeGroupCode = inchg_g.lngGroupCode";
        $aryQuery[] = " LEFT JOIN m_User inchg_u ON p.lngInChargeUserCode = inchg_u.lngUserCode";
        $aryQuery[] = " LEFT JOIN m_User devp_u ON p.lngdevelopusercode = devp_u.lngUserCode";
        $aryQuery[] = " LEFT JOIN m_Company cust_c ON p.lngCustomerCompanyCode = cust_c.lngCompanyCode";
        $aryQuery[] = " LEFT JOIN m_User cust_u ON p.lngCustomerUserCode = cust_u.lngUserCode";
        $aryQuery[] = " LEFT JOIN m_ProductUnit packingunit ON p.lngPackingUnitCode = packingunit.lngProductUnitCode";
        $aryQuery[] = " LEFT JOIN m_ProductUnit productunit ON p.lngProductUnitCode = productunit.lngProductUnitCode";
        $aryQuery[] = " LEFT JOIN m_ProductUnit productionunit ON p.lngProductionUnitCode = productionunit.lngProductUnitCode";
        $aryQuery[] = " LEFT JOIN m_ProductUnit firstdeliveryunit ON p.lngFirstDeliveryUnitCode = firstdeliveryunit.lngProductUnitCode";
        $aryQuery[] = " LEFT JOIN m_ProductForm productform ON p.lngProductFormCode = productform.lngProductFormCode";
        $aryQuery[] = " LEFT JOIN m_Company fact_c ON p.lngFactoryCode = fact_c.lngCompanyCode";
        $aryQuery[] = " LEFT JOIN m_Company assemfact_c ON p.lngAssemblyFactoryCode = assemfact_c.lngCompanyCode";
        $aryQuery[] = " LEFT JOIN m_Company delv_c ON p.lngDeliveryPlaceCode = delv_c.lngCompanyCode";
        $aryQuery[] = " LEFT JOIN m_TargetAge targetage ON p.lngTargetAgeCode = targetage.lngTargetAgeCode";
        $aryQuery[] = " LEFT JOIN m_CertificateClass certificate ON p.lngCertificateClassCode = certificate.lngCertificateClassCode";
        $aryQuery[] = " LEFT JOIN m_Copyright copyright ON p.lngCopyrightCode = copyright.lngCopyrightCode";
        $aryQuery[] = " LEFT JOIN m_Category category ON p.lngCategoryCode = category.lngCategoryCode";
        $aryQuery[] = ", t_GoodsPlan t_gp ";

        $aryQuery[] = "WHERE p.lngProductNo = " . $lngKeyCode;

        $aryQuery[] = " AND t_gp.lngProductNo = p.lngProductNo";
        $aryQuery[] = " AND t_gp.lngRevisionNo = ( SELECT MAX( t_gp1.lngRevisionNo ) FROM t_GoodsPlan t_gp1 WHERE t_gp1.lngProductNo = p.lngProductNo )";
    }

    /////////////////////////////////////////////////////////////////////////
    // 発注書
    /////////////////////////////////////////////////////////////////////////
    // 登録日            dtmInsertDate
    // 計上日            dtmOrderAppDate
    // 発注No            strOrderCode
    // 入力者            strInputUserDisplayName
    // 仕入先            strCustomerDisplayName
    // 仕入先(住所)        strAddress*
    // 仕入先(TEL)        strTel1
    // 仕入先(FAX)        strFax1
    // 仕入先(組織)        lngOrganizationCode
    // 部門                strInChargeGroupDisplayName
    // 担当者            strInChargeUserDisplayName
    // 納品場所            strDeliveryDisplayName
    // 通貨                strMonetaryUnitSign
    // レートタイプ        strMonetaryRateName
    // 換算レート        curConversionRate
    // 状態                strOrderStatusName
    // 支払条件            strPayConditionName
    // 発注有効期限日    dtmExpirationDate
    // 備考                o.strNote
    // 合計金額            curTotalPrice
    // 最終承認者名        strInChargeUserName
    elseif ($lngClassCode == DEF_REPORT_ORDER) {
        $aryQuery[] = "SELECT distinct on (o.lngOrderNo) o.lngOrderNo, o.lngRevisionNo";

        // 登録日
        $aryQuery[] = ", To_Char( o.dtmInsertDate, 'YYYY/MM/DD' ) as dtmInsertDate";
        // 計上日
        $aryQuery[] = ", To_Char( o.dtmAppropriationDate, 'YYYY/MM/DD' ) as dtmOrderAppDate";
        // 発注No
        $aryQuery[] = ", o.strOrderCode as strOrderCode";
        // 仕入先
        $aryQuery[] = ", cust_c.strCompanyDisplayCode as strCustomerDisplayCode";
        $aryQuery[] = ", CASE";
        $aryQuery[] = "    WHEN cust_c.bytOrganizationFront = TRUE AND cust_c.lngOrganizationCode < 10";
        $aryQuery[] = "      THEN org.strOrganizationName || cust_c.strCompanyName";
        $aryQuery[] = "    WHEN cust_c.bytOrganizationFront = FALSE AND cust_c.lngOrganizationCode < 10";
        $aryQuery[] = "      THEN cust_c.strCompanyName || org.strOrganizationName";
        $aryQuery[] = "    ELSE cust_c.strCompanyName";
        $aryQuery[] = "  END AS strCustomerDisplayName";
        $aryQuery[] = ", cust_c.strAddress1 as strCustomerAddress1";
        $aryQuery[] = ", cust_c.strAddress2 as strCustomerAddress2";
        $aryQuery[] = ", cust_c.strAddress3 as strCustomerAddress3";
        $aryQuery[] = ", cust_c.strAddress4 as strCustomerAddress4";
        $aryQuery[] = ", cust_c.strTel1";
        $aryQuery[] = ", cust_c.strFax1";
        $aryQuery[] = ", cust_c.lngOrganizationCode";
        // 部門
        $aryQuery[] = ", inchg_g.strGroupDisplayCode as strInChargeGroupDisplayCode";
        $aryQuery[] = ", inchg_g.strGroupDisplayName as strInChargeGroupDisplayName";
        // 担当者
        $aryQuery[] = ", inchg_u.strUserDisplayCode as strInChargeUserDisplayCode";
        // 担当者→Group名称使用
        $aryQuery[] = ", inchg_g.strGroupDisplayName as strInChargeUserDisplayName";
        // 納品場所
        $aryQuery[] = ", delv_c.strCompanyDisplayCode as strDeliveryDisplayCode";
        $aryQuery[] = ", delv_c.strCompanyDisplayName as strDeliveryDisplayName";
        // 通貨
        $aryQuery[] = ", mu.strMonetaryUnitName";
        $aryQuery[] = ", mu.strMonetaryUnitSign";
        // レートタイプ
        $aryQuery[] = ", mr.strMonetaryRateName";
        // 換算レート
        $aryQuery[] = ", o.curConversionRate";
        // 状態
        $aryQuery[] = ", os.strOrderStatusName";
        // 支払条件
        $aryQuery[] = ", pc.strPayConditionName";
        // 発注有効期限日
        $aryQuery[] = ", To_Char( o.dtmExpirationDate, 'YYYY/MM/DD' ) as dtmExpirationDate";
        $aryQuery[] = " FROM m_Order o";
        // 発注-会社
        $aryQuery[] = " LEFT JOIN m_Company cust_c ON o.lngCustomerCompanyCode = cust_c.lngCompanyCode";
        // 発注-会社
        $aryQuery[] = " LEFT JOIN m_Company delv_c ON o.lngDeliveryPlaceCode = delv_c.lngCompanyCode";
        $aryQuery[] = " LEFT JOIN m_OrderStatus os USING (lngOrderStatusCode)";
        // 発注-支払条件
        $aryQuery[] = " LEFT JOIN m_PayCondition pc ON o.lngPayConditionCode = pc.lngPayConditionCode";
        // 発注-通貨レート
        $aryQuery[] = " LEFT JOIN m_MonetaryUnit mu ON o.lngMonetaryUnitCode = mu.lngMonetaryUnitCode";
        // 発注-通貨レート種類
        $aryQuery[] = " LEFT JOIN m_MonetaryRateClass mr ON o.lngMonetaryRateCode = mr.lngMonetaryRateCode";
        // 発注-発注詳細
        $aryQuery[] = " LEFT JOIN t_OrderDetail od ON o.lngOrderNo = od.lngOrderNo";
        //発注-製品
        $aryQuery[] = " LEFT JOIN ( ";
        $aryQuery[] = " select p1.*  from m_product p1 ";
        $aryQuery[] = " inner join (select max(lngproductno) lngproductno, strproductcode from m_Product group by strProductCode) p2";
        $aryQuery[] = " on p1.lngproductno = p2.lngproductno";
        $aryQuery[] = " ) p ON od.strProductcode = p.strProductcode";
        // グループ-発注
        $aryQuery[] = " LEFT JOIN m_Group inchg_g ON p.lnginchargegroupcode = inchg_g.lngGroupCode";
        // ユーザー-発注
        $aryQuery[] = " LEFT JOIN m_User inchg_u ON p.lnginchargeusercode = inchg_u.lngUserCode";
        // 組織
        $aryQuery[] = " LEFT JOIN m_Organization org ON cust_c.lngOrganizationCode = org.lngOrganizationCode";

        $aryQuery[] = " WHERE o.lngOrderNo = " . $lngKeyCode;
        // A:「発注」状態より大きい状態の発注データ
        $aryQuery[] = " AND o.lngOrderStatusCode >= " . DEF_ORDER_ORDER;
    }

    return join("", $aryQuery);
}

// -----------------------------------------------------------------
/**
 *    署名イメージファイル存在チェック関数
 *
 *    該当ユーザーの署名イメージファイルが存在するかどうかチェックする関数
 *
 *    GIF / JEPG に対応
 *
 *    @param  String  $strPath  ファイル格納ディレクトリパス
 *    @param  Number  $lnguc    該当ユーザーコード
 *    @access public
 *
 *    @return bool
 */
// -----------------------------------------------------------------
function fncSignatureCheckFile($strPath, $lnguc)
{
    $bl = false;
    $dh = opendir($strPath);

    while ($file = readdir($dh)) {
        $file = strtolower($file);

        if ($file == $lnguc . ".gif" || $file == $lnguc . ".jpg" || $file == $lnguc . ".jpeg") {
            $bl = true;
            break;
        } else {
            $bl = false;
        }
    }

    closedir($dh);

    return $bl;
}

return true;
