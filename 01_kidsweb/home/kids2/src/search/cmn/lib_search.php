<?
// ----------------------------------------------------------------------------
/**
 *      各検索管理  検索関連関数群
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

$aryTableHeadBtnName = array();
$aryTableHeadBtnName["btndetail"] = "詳細";
$aryTableHeadBtnName["btnfix"] = "修正";
$aryTableHeadBtnName["btnhistory"] = "履歴";
$aryTableHeadBtnName["btndecide"] = "確定";
$aryTableBackBtnName = array();
$aryTableBackBtnName["btninvalid"] = "無効";
$aryTableBackBtnName["btncancel"] = "確定取消";
$aryTableBackBtnName["btndelete"] = "削除";

$aryTableHeaderName_SO = array();
$aryTableHeaderName_SO["dtminsertdate"] = "登録日";
$aryTableHeaderName_SO["lnginputusercode"] = "入力者";
$aryTableHeaderName_SO["strcustomerreceivecode"] = "顧客受注番号";
$aryTableHeaderName_SO["strreceivecode"] = "受注ＮＯ.";
$aryTableHeaderName_SO["lngrevisionno"] = "リビジョン番号";
$aryTableHeaderName_SO["strproductcode"] = "製品コード";
$aryTableHeaderName_SO["strproductname"] = "製品名";
$aryTableHeaderName_SO["strproductenglishname"] = "製品名（英語）";
$aryTableHeaderName_SO["lnginchargegroupcode"] = "営業部署";
$aryTableHeaderName_SO["lnginchargeusercode"] = "開発担当者";
$aryTableHeaderName_SO["lngsalesclasscode"] = "売上区分";
$aryTableHeaderName_SO["strgoodscode"] = "顧客品番";
$aryTableHeaderName_SO["lngcustomercompanycode"] = "顧客";
$aryTableHeaderName_SO["dtmdeliverydate"] = "納期";
$aryTableHeaderName_SO["lngreceivestatuscode"] = "状態";
$aryTableHeaderName_SO["lngrecordno"] = "明細行番号";
$aryTableHeaderName_SO["curproductprice"] = "単価";
$aryTableHeaderName_SO["lngproductunitcode"] = "単位";
$aryTableHeaderName_SO["lngproductquantity"] = "数量";
$aryTableHeaderName_SO["cursubtotalprice"] = "税抜金額";
$aryTableHeaderName_SO["strdetailnote"] = "明細備考";

$aryTableHeaderName_PO["dtminsertdate"] = "登録日";
$aryTableHeaderName_PO["lnginputusercode"] = "入力者";
$aryTableHeaderName_PO["strordercode"] = "発注ＮＯ.";
$aryTableHeaderName_PO["lngrevisionno"] = "リビジョン番号";
$aryTableHeaderName_PO["strproductcode"] = "製品コード";
$aryTableHeaderName_PO["strproductname"] = "製品名";
$aryTableHeaderName_PO["strproductenglishname"] = "製品名（英語）";
$aryTableHeaderName_PO["lnginchargegroupcode"] = "営業部署";
$aryTableHeaderName_PO["lnginchargeusercode"] = "開発担当者";
$aryTableHeaderName_PO["lngcustomercode"] = "仕入先";
$aryTableHeaderName_PO["lngstocksubjectcode"] = "仕入科目";
$aryTableHeaderName_PO["lngstockitemcode"] = "仕入部品";
$aryTableHeaderName_PO["dtmdeliverydate"] = "納期";
$aryTableHeaderName_PO["lngorderstatuscode"] = "状態";
$aryTableHeaderName_PO["strgoodscode"] = "顧客品番";
$aryTableHeaderName_PO["lngcustomercompanycode"] = "顧客";
$aryTableHeaderName_PO["lngreceivestatuscode"] = "状態";
$aryTableHeaderName_PO["lngrecordno"] = "明細行番号";
$aryTableHeaderName_PO["curproductprice"] = "単価";
$aryTableHeaderName_PO["lngproductquantity"] = "数量";
$aryTableHeaderName_PO["cursubtotalprice"] = "税抜金額";
$aryTableHeaderName_PO["strdetailnote"] = "明細備考";

$aryTableHeaderName_SC["dtminsertdate"] = "登録日";
$aryTableHeaderName_SC["dtmappropriationdate"] = "請求日";
$aryTableHeaderName_SC["strsalescode"] = "売上NO.";
$aryTableHeaderName_SC["lngrevisionno"] = "リビジョン番号";
$aryTableHeaderName_SC["strcustomerreceivecode"] = "顧客受注番号";
$aryTableHeaderName_SC["strslipcode"] = "納品書NO.";
$aryTableHeaderName_SC["lnginputusercode"] = "入力者";
$aryTableHeaderName_SC["lngcustomercompanycode"] = "顧客";
$aryTableHeaderName_SC["lngsalesstatuscode"] = "状態";
$aryTableHeaderName_SC["strnote"] = "備考";
$aryTableHeaderName_SC["curtotalprice"] = "合計金額";
$aryTableDetailHeaderName_SC["lngrecordno"] = "明細行番号";
$aryTableDetailHeaderName_SC["strproductcode"] = "製品コード";
$aryTableDetailHeaderName_SC["lnginchargegroupcode"] = "営業部署";
$aryTableDetailHeaderName_SC["lnginchargeusercode"] = "開発担当者";
$aryTableDetailHeaderName_SC["strproductname"] = "製品名";
$aryTableDetailHeaderName_SC["lngsalesclasscode"] = "売上区分";
$aryTableDetailHeaderName_SC["strgoodscode"] = "顧客品番";
$aryTableDetailHeaderName_SC["curproductprice"] = "単価";
$aryTableDetailHeaderName_SC["lngproductunitcode"] = "単位";
$aryTableDetailHeaderName_SC["lngproductquantity"] = "数量";
$aryTableDetailHeaderName_SC["cursubtotalprice"] = "税抜金額";
$aryTableDetailHeaderName_SC["lngtaxclasscode"] = "税区分";
$aryTableDetailHeaderName_SC["curtax"] = "税率";
$aryTableDetailHeaderName_SC["curtaxprice"] = "税額";
$aryTableDetailHeaderName_SC["strdetailnote"] = "明細備考";

$aryTableHeadBtnName_SLIP["btndetail"] = "詳細";
$aryTableHeadBtnName_SLIP["btnfix"] = "修正";
$aryTableHeadBtnName_SLIP["btnhistory"] = "履歴";
$aryTableBackBtnName_SLIP["btndelete"] = "削除";
// ヘッダ部
$aryTableHeaderName_SLIP["lngcustomercode"] = "顧客";
$aryTableHeaderName_SLIP["lngtaxclasscode"] = "課税区分";
$aryTableHeaderName_SLIP["strslipcode"] = "納品書NO";
$aryTableHeaderName_SLIP["dtmdeliverydate"] = "納品日";
$aryTableHeaderName_SLIP["lngdeliveryplacecode"] = "納品先";
$aryTableHeaderName_SLIP["lnginsertusercode"] = "起票者";
$aryTableHeaderName_SLIP["strnote"] = "備考";
$aryTableHeaderName_SLIP["curtotalprice"] = "合計金額";

// 明細部
$aryTableDetailHeaderName_SLIP["lngrecordno"] = "明細行NO";
$aryTableDetailHeaderName_SLIP["strcustomersalescode"] = "注文書NO";
$aryTableDetailHeaderName_SLIP["strgoodscode"] = "顧客品番";
$aryTableDetailHeaderName_SLIP["strproductname"] = "品名";
$aryTableDetailHeaderName_SLIP["lngsalesclasscode"] = "売上区分";
$aryTableDetailHeaderName_SLIP["curproductprice"] = "単価";
$aryTableDetailHeaderName_SLIP["lngquantity"] = "入数";
$aryTableDetailHeaderName_SLIP["lngproductquantity"] = "数量";
$aryTableDetailHeaderName_SLIP["strproductunitname"] = "単位";
$aryTableDetailHeaderName_SLIP["cursubtotalprice"] = "税抜金額";
$aryTableDetailHeaderName_SLIP["strdetailnote"] = "明細備考";

$aryTableHeaderName_PC["dtminsertdate"] = "登録日";
$aryTableHeaderName_PC["dtmappropriationdate"] = "仕入日";
$aryTableHeaderName_PC["strstockcode"] = "仕入ＮＯ.";
$aryTableHeaderName_PC["lngrevisionno"] = "リビジョン番号";
$aryTableHeaderName_PC["strordercode"] = "発注書ＮＯ.";
$aryTableHeaderName_PC["strslipcode"] = "納品書ＮＯ.";
$aryTableHeaderName_PC["lnginputusercode"] = "入力者";
$aryTableHeaderName_PC["lngcustomercode"] = "仕入先";
$aryTableHeaderName_PC["lngstockstatuscode"] = "状態";
$aryTableHeaderName_PC["lngpayconditioncode"] = "支払条件";
$aryTableHeaderName_PC["dtmexpirationdate"] = "製品到着日";
$aryTableHeaderName_PC["strnote"] = "備考";
$aryTableHeaderName_PC["curtotalprice"] = "合計金額";
$aryTableDetailHeaderName_PC["lngrecordno"] = "明細行番号";
$aryTableDetailHeaderName_PC["strproductcode"] = "製品コード";
$aryTableDetailHeaderName_PC["lnginchargegroupcode"] = "営業部署";
$aryTableDetailHeaderName_PC["lnginchargeusercode"] = "開発担当者";
$aryTableDetailHeaderName_PC["strproductname"] = "製品名";
$aryTableDetailHeaderName_PC["lngstocksubjectcode"] = "仕入科目";
$aryTableDetailHeaderName_PC["lngstockitemcode"] = "仕入部品";
$aryTableDetailHeaderName_PC["strmoldno"] = "Ｎｏ．";
$aryTableDetailHeaderName_PC["strgoodscode"] = "顧客品番";
$aryTableDetailHeaderName_PC["lngdeliverymethodcode"] = "運搬方法";
$aryTableDetailHeaderName_PC["curproductprice"] = "単価";
$aryTableDetailHeaderName_PC["lngproductunitcode"] = "単位";
$aryTableDetailHeaderName_PC["lngproductquantity"] = "数量";
$aryTableDetailHeaderName_PC["cursubtotalprice"] = "税抜金額";
$aryTableDetailHeaderName_PC["lngtaxclasscode"] = "税区分";
$aryTableDetailHeaderName_PC["curtax"] = "税率";
$aryTableDetailHeaderName_PC["curtaxprice"] = "税額";
$aryTableDetailHeaderName_PC["strdetailnote"] = "明細備考";

$aryTableHeadBtnName_INV["btndetail"] = "詳細";
$aryTableHeadBtnName_INV["btnfix"] = "修正";
$aryTableHeadBtnName_INV["btnhistory"] = "履歴";
$aryTableBackBtnName_INV["btndelete"] = "削除";
// ヘッダ部
$aryTableHeaderName_INV["lngcustomercode"] = "顧客";
$aryTableHeaderName_INV["strinvoicecode"] = "請求書No";
$aryTableHeaderName_INV["dtminvoicedate"] = "請求日";
$aryTableHeaderName_INV["curlastmonthbalance"] = "先月請求残額";
$aryTableHeaderName_INV["curthismonthamount"] = "当月請求金額";
$aryTableHeaderName_INV["cursubtotal"] = "消費税額";
$aryTableHeaderName_INV["dtminsertdate"] = "作成日";
$aryTableHeaderName_INV["lngusercode"] = "担当者";
$aryTableHeaderName_INV["lnginsertusercode"] = "入力者";
$aryTableHeaderName_INV["lngprintcount"] = "印刷回数";
$aryTableHeaderName_INV["strnote"] = "備考";

// 明細部
$aryTableDetailHeaderName_INV["lnginvoicedetailno"] = "請求書明細番号";
$aryTableDetailHeaderName_INV["dtmdeliverydate"] = "納品日";
$aryTableDetailHeaderName_INV["strslipcode"] = "納品書NO";
$aryTableDetailHeaderName_INV["lngdeliveryplacecode"] = "納品先";
$aryTableDetailHeaderName_INV["cursubtotalprice"] = "税抜金額";
$aryTableDetailHeaderName_INV["lngtaxclasscode"] = "課税区分";
$aryTableDetailHeaderName_INV["curtax"] = "税率";
$aryTableDetailHeaderName_INV["curtaxprice"] = "消費額";
$aryTableDetailHeaderName_INV["strdetailnote"] = "明細備考";

$aryTableHeaderName_PURORDER = array();
$aryTableHeaderName_PURORDER["dtminsertdate"] = "登録日";
$aryTableHeaderName_PURORDER["strordercode"] = "発注書ＮＯ.";
$aryTableHeaderName_PURORDER["lnginputusercode"] = "入力者";
$aryTableHeaderName_PURORDER["dtmexpirationdate"] = "発注有効期限日";
$aryTableHeaderName_PURORDER["strproductcode"] = "製品コード";
$aryTableHeaderName_PURORDER["strproductname"] = "製品名";
$aryTableHeaderName_PURORDER["strproductenglishname"] = "製品名（英語）";
$aryTableHeaderName_PURORDER["lnginchargegroupcode"] = "営業部署";
$aryTableHeaderName_PURORDER["lnginchargeusercode"] = "開発担当者";
$aryTableHeaderName_PURORDER["lngcustomercode"] = "仕入先";
$aryTableHeaderName_PURORDER["lngpayconditioncode"] = "支払条件";
$aryTableHeaderName_PURORDER["lngmonetaryunitcode"] = "通貨";
$aryTableHeaderName_PURORDER["cursubtotalprice"] = "税抜金額";
$aryTableHeaderName_PURORDER["lngdeliveryplacecode"] = "納品場所";
$aryTableHeaderName_PURORDER["lngprintcount"] = "印刷回数";
$aryTableHeaderName_PURORDER["strnote"] = "備考";

$aryTableHeaderName["strstockcode"] = "仕入ＮＯ.";
$aryTableHeaderName["strsalescode"] = "売上NO.";
$aryTableHeaderName["lngrevisionno"] = "リビジョン番号";
$aryTableHeaderName["strcustomerreceivecode"] = "顧客受注番号";
$aryTableHeaderName["strslipcode"] = "納品書NO.";
$aryTableHeaderName["strslipcode"] = "納品書ＮＯ.";
$aryTableHeaderName["strreceivecode"] = "受注ＮＯ.";
$aryTableHeaderName["lngcustomercode"] = "仕入先";
$aryTableHeaderName["lngcustomercompanycode"] = "顧客";
$aryTableHeaderName["lngpayconditioncode"] = "支払条件";
$aryTableHeaderName["strnote"] = "備考";
$aryTableHeaderName["curtotalprice"] = "合計金額";
$aryTableHeaderName["lngtaxclasscode"] = "課税区分";
$aryTableHeaderName["dtmdeliverydate"] = "納品日";
$aryTableHeaderName["strinvoicecode"] = "請求書No";
$aryTableHeaderName["dtminvoicedate"] = "請求日";
$aryTableHeaderName["curlastmonthbalance"] = "先月請求残額";
$aryTableHeaderName["curthismonthamount"] = "当月請求金額";
$aryTableHeaderName["dtminsertdate"] = "作成日";
$aryTableHeaderName["lngusercode"] = "担当者";
$aryTableHeaderName["lnginsertusercode"] = "入力者";
$aryTableHeaderName["lngprintcount"] = "印刷回数";

$aryTableDetailHeaderName["lngrecordno"] = "明細行番号";
$aryTableDetailHeaderName["strproductcode"] = "製品コード";
$aryTableDetailHeaderName["lnginchargegroupcode"] = "営業部署";
$aryTableDetailHeaderName["lnginchargeusercode"] = "開発担当者";
$aryTableDetailHeaderName["strproductname"] = "製品名";
$aryTableDetailHeaderName["lngstocksubjectcode"] = "仕入科目";
$aryTableDetailHeaderName["lngstockitemcode"] = "仕入部品";
$aryTableDetailHeaderName["strmoldno"] = "Ｎｏ．";
$aryTableDetailHeaderName["strgoodscode"] = "顧客品番";
$aryTableDetailHeaderName["lngdeliverymethodcode"] = "運搬方法";
$aryTableDetailHeaderName["curproductprice"] = "単価";
$aryTableDetailHeaderName["lngproductunitcode"] = "単位";
$aryTableDetailHeaderName["lngproductquantity"] = "数量";
$aryTableDetailHeaderName["cursubtotalprice"] = "税抜金額";
$aryTableDetailHeaderName["lngtaxclasscode"] = "税区分";
$aryTableDetailHeaderName["curtax"] = "税率";
$aryTableDetailHeaderName["curtaxprice"] = "税額";
$aryTableDetailHeaderName["strdetailnote"] = "明細備考";
$aryTableDetailHeaderName["strsalesclassname"] = "売上区分";
$aryTableDetailHeaderName["lngquantity"] = "入数";
$aryTableDetailHeaderName["strcustomersalescode"] = "注文書NO";

/**
 * コード、リビジョン番号により履歴データを取得する
 *
 * @param [type] $type
 * @param [type] $strCode
 * @param [type] $lngRevisionNo
 * @param [type] $lngDetailNo
 * @param [type] $objDB
 * @return void
 */
function fncGetHistoryDataByPKSQL($type, $strCode, $lngRevisionNo, $lngDetailNo, $objDB)
{
    $aryQuery = array();
    $result = array();
    if ($type == 'purchaseorder') { // 発注書
        $aryQuery[] = "SELECT";
        $aryQuery[] = "  mp.lngpurchaseorderno as lngpurchaseorderno";
        $aryQuery[] = "  , mp.lngpurchaseorderno as lngPkNo";
        $aryQuery[] = "  , mp.lngrevisionno as lngRevisionNo";
        $aryQuery[] = "  , mp.strrevisecode as strReviseCode";
        $aryQuery[] = "  , mp.strordercode as strOrderCode";
        $aryQuery[] = "  , mp.strordercode as strCode";
        $aryQuery[] = "  , to_char(mp.dtmexpirationdate, 'YYYY/MM/DD') as dtmExpirationDate";
        $aryQuery[] = "  , mp.strproductcode as strProductCode";
        $aryQuery[] = "  , mp.strproductname as strProductName";
        $aryQuery[] = "  , mp.strproductenglishname as strProductEnglishName";
        $aryQuery[] = "  , to_char(mp.dtminsertdate, 'YYYY/MM/DD') as dtmInsertDate";
        $aryQuery[] = "  , input_user.struserdisplaycode AS lngInsertUserCode";
        $aryQuery[] = "  , input_user.struserdisplaycode AS strinputuserdisplaycode";
        $aryQuery[] = "  , mp.strinsertusername AS strInsertUserName";
        $aryQuery[] = "  , mp.strinsertusername AS strinputuserdisplayname";
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
        $aryQuery[] = "  mp.strordercode = '" . $strCode . "' ";
        $aryQuery[] = "  AND mp.lngrevisionno <> " . $lngRevisionNo . " ";
        $aryQuery[] = "  AND mp.lngrevisionno >= 0 ";
        $aryQuery[] = "ORDER BY";
        $aryQuery[] = "  mp.lngpurchaseorderno";
        $aryQuery[] = "  , mp.lngrevisionno DESC";
    } else if ($type == 'po') { // 発注
        $aryQuery[] = "SELECT";
        $aryQuery[] = "  o.lngOrderNo as lngOrderNo";
        $aryQuery[] = "  , o.lngOrderNo as lngPkNo";
        $aryQuery[] = "  , o.lngRevisionNo as lngRevisionNo";
        $aryQuery[] = "  , od.lngOrderDetailNo";
        $aryQuery[] = "  , od.lngOrderDetailNo as lngDetailNo";
        $aryQuery[] = "  , o.strOrderCode as strOrderCode";
        $aryQuery[] = "  , o.strOrderCode as strCode";
        $aryQuery[] = "  , o.strOrderCode || '_' || to_char(o.lngRevisionNo, 'FM00') as strOrderCode_desc";
        $aryQuery[] = "  , to_char(o.dtmInsertDate, 'YYYY/MM/DD HH:MI:SS') as dtmInsertDate";
        $aryQuery[] = "  , input_u.strUserDisplayCode as strInputUserDisplayCode";
        $aryQuery[] = "  , input_u.strUserDisplayName as strInputUserDisplayName";
        $aryQuery[] = "  , cust_c.strCompanyDisplayCode as strCustomerDisplayCode";
        $aryQuery[] = "  , cust_c.strCompanyDisplayName as strCustomerDisplayName";
        $aryQuery[] = "  , o.lngOrderStatusCode as lngOrderStatusCode";
        $aryQuery[] = "  , os.strOrderStatusName as strOrderStatusName";
        $aryQuery[] = "  , os.strOrderStatusName as strStatusName";
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
        $aryQuery[] = "  , od.curProductPrice";
        $aryQuery[] = "  , od.lngProductQuantity";
        $aryQuery[] = "  , od.curSubTotalPrice";
        $aryQuery[] = "  , od.strDetailNote ";
        $aryQuery[] = "FROM";
        $aryQuery[] = "  m_Order o ";
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
        $aryQuery[] = "        , mp.strProductName as strProductName";
        $aryQuery[] = "        , mp.strProductEnglishName as strProductEnglishName";
        $aryQuery[] = "        , mg.strgroupdisplaycode as lngInChargeGroupCode";
        $aryQuery[] = "        , mg.strgroupdisplayname as strInChargeGroupName";
        $aryQuery[] = "        , mu.struserdisplaycode as lngInChargeUserCode";
        $aryQuery[] = "        , mu.struserdisplayname as strInChargeUserName";
        $aryQuery[] = "        , od1.lngStockSubjectCode as lngStockSubjectCode";
        $aryQuery[] = "        , ss.strStockSubjectName as strStockSubjectName";
        $aryQuery[] = "        , od1.lngStockItemCode as lngStockItemCode";
        $aryQuery[] = "        , si.strstockitemname as strstockitemname";
        $aryQuery[] = "        , to_char(od1.dtmDeliveryDate, 'YYYY/MM/DD') as dtmDeliveryDate";
        $aryQuery[] = "        , to_char(od1.curProductPrice, '9,999,999,990.9999') as curProductPrice";
        $aryQuery[] = "        , to_char(od1.lngProductQuantity, '9,999,999,990') as lngProductQuantity";
        $aryQuery[] = "        , to_char(od1.curSubTotalPrice, '9,999,999,990.99') as curSubTotalPrice";
        $aryQuery[] = "        , od1.strNote as strDetailNote ";
        $aryQuery[] = "      from";
        $aryQuery[] = "        t_orderdetail od1 ";
        $aryQuery[] = "        LEFT JOIN m_product mp ";
        $aryQuery[] = "          on mp.strproductcode = od1.strproductcode ";
        $aryQuery[] = "          and mp.strrevisecode = od1.strrevisecode ";
        $aryQuery[] = "          and mp.lngrevisionno = od1.lngrevisionno ";
        $aryQuery[] = "        LEFT JOIN m_group mg ";
        $aryQuery[] = "          on mg.lnggroupcode = mp.lnginchargegroupcode ";
        $aryQuery[] = "        LEFT JOIN m_user mu ";
        $aryQuery[] = "          on mu.lngusercode = mp.lnginchargeusercode ";
        $aryQuery[] = "        LEFT JOIN m_StockSubject ss ";
        $aryQuery[] = "          ON od1.lngstocksubjectcode = ss.lngstocksubjectcode ";
        $aryQuery[] = "        LEFT JOIN m_stockitem si ";
        $aryQuery[] = "          ON od1.lngstockitemcode = si.lngstockitemcode ";
        $aryQuery[] = "          AND od1.lngstocksubjectcode = si.lngstocksubjectcode ";
        $aryQuery[] = "    ) od ";
        $aryQuery[] = "WHERE";
        $aryQuery[] = "  od.lngorderno = o.lngorderno ";
        $aryQuery[] = "  AND od.lngRevisionNo = o.lngRevisionNo ";
        $aryQuery[] = "  AND o.strordercode = '" . $strCode . "'";
        $aryQuery[] = "  AND od.lngOrderDetailNo = '" . $lngDetailNo . "'";
        $aryQuery[] = "  AND o.lngRevisionNo <>  " . $lngRevisionNo . "";
        $aryQuery[] = "  AND o.lngRevisionNo >= 0 ";
        $aryQuery[] = "  AND o.bytInvalidFlag = FALSE ";
        $aryQuery[] = "ORDER BY";
        $aryQuery[] = "  o.strordercode, od.lngOrderDetailNo, o.lngRevisionNo DESC";
    } else if ($type == 'so') { // 受注
        $aryQuery[] = "SELECT";
        $aryQuery[] = "  r.lngReceiveNo as lngPkNo";
        $aryQuery[] = "  , r.lngReceiveNo as lngReceiveNo";
        $aryQuery[] = "  , r.lngRevisionNo as lngRevisionNo";
        $aryQuery[] = "  , rd.lngReceiveDetailNo";
        $aryQuery[] = "  , rd.lngReceiveDetailNo as lngDetailNo";
        $aryQuery[] = "  , r.strReceiveCode";
        $aryQuery[] = "  , r.strReceiveCode as strCode";
        $aryQuery[] = "  , rd.strProductCode";
        $aryQuery[] = "  , rd.strGroupDisplayCode";
        $aryQuery[] = "  , rd.strGroupDisplayName";
        $aryQuery[] = "  , rd.strUserDisplayCode";
        $aryQuery[] = "  , rd.strUserDisplayName";
        $aryQuery[] = "  , rd.strProductName";
        $aryQuery[] = "  , rd.strProductEnglishName";
        $aryQuery[] = "  , rd.lngSalesClassCode";
        $aryQuery[] = "  , rd.strsalesclassname";
        $aryQuery[] = "  , rd.strGoodsCode";
        $aryQuery[] = "  , rd.curProductPrice";
        $aryQuery[] = "  , rd.lngProductUnitCode";
        $aryQuery[] = "  , rd.strproductunitname";
        $aryQuery[] = "  , to_char(rd.lngProductQuantity, '9,999,999,990') as lngProductQuantity";
        $aryQuery[] = "  , rd.curSubTotalPrice";
        $aryQuery[] = "  , rd.strNote as strDetailNote";
        $aryQuery[] = "  , to_char(r.dtmInsertDate, 'YYYY/MM/DD HH24:MI:SS') as dtmInsertDate";
        $aryQuery[] = "  , input_u.strUserDisplayCode as strInputUserDisplayCode";
        $aryQuery[] = "  , input_u.strUserDisplayName as strInputUserDisplayName";
        $aryQuery[] = "  , r.strCustomerReceiveCode as strCustomerReceiveCode";
        $aryQuery[] = "  , cust_c.strCompanyDisplayCode as strCustomerDisplayCode";
        $aryQuery[] = "  , cust_c.strCompanyDisplayName as strCustomerDisplayName";
        $aryQuery[] = "  , to_char(rd.dtmDeliveryDate, 'YYYY/MM/DD') as dtmDeliveryDate";
        $aryQuery[] = "  , r.lngReceiveStatusCode as lngReceiveStatusCode";
        $aryQuery[] = "  , rs.strReceiveStatusName as strReceiveStatusName";
        $aryQuery[] = "  , rs.strReceiveStatusName as strStatusName";
        $aryQuery[] = "  , mu.strMonetaryUnitSign as strMonetaryUnitSign ";
        $aryQuery[] = "  , mu.lngmonetaryunitcode as lngmonetaryunitcode ";
        $aryQuery[] = "FROM";
        $aryQuery[] = "  m_Receive r ";
        $aryQuery[] = "  LEFT JOIN m_User input_u ";
        $aryQuery[] = "    ON r.lngInputUserCode = input_u.lngUserCode ";
        $aryQuery[] = "  LEFT JOIN m_Company cust_c ";
        $aryQuery[] = "    ON r.lngCustomerCompanyCode = cust_c.lngCompanyCode ";
        $aryQuery[] = "  LEFT JOIN m_ReceiveStatus rs ";
        $aryQuery[] = "    USING (lngReceiveStatusCode) ";
        $aryQuery[] = "  LEFT JOIN m_MonetaryUnit mu ";
        $aryQuery[] = "    ON r.lngMonetaryUnitCode = mu.lngMonetaryUnitCode";
        $aryQuery[] = "  , ( ";
        $aryQuery[] = "      SELECT rd1.lngReceiveNo";
        $aryQuery[] = "        , rd1.lngReceiveDetailNo";
        $aryQuery[] = "        , rd1.lngRevisionNo";
        $aryQuery[] = "        , p.strProductCode";
        $aryQuery[] = "        , mg.strGroupDisplayCode";
        $aryQuery[] = "        , mg.strGroupDisplayName";
        $aryQuery[] = "        , mu.struserdisplaycode";
        $aryQuery[] = "        , mu.struserdisplayname";
        $aryQuery[] = "        , p.strProductName";
        $aryQuery[] = "        , p.strProductEnglishName";
        $aryQuery[] = "        , ms.lngSalesClassCode";
        $aryQuery[] = "        , ms.strsalesclassname";
        $aryQuery[] = "        , p.strGoodsCode";
        $aryQuery[] = "        , rd1.dtmDeliveryDate";
        $aryQuery[] = "        , to_char(rd1.curProductPrice, '9,999,999,990.99') as curProductPrice";
        $aryQuery[] = "        , mp.lngProductUnitCode";
        $aryQuery[] = "        , mp.strproductunitname";
        $aryQuery[] = "        , rd1.lngProductQuantity";
        $aryQuery[] = "        , to_char(rd1.curSubTotalPrice, '9,999,999,990.99') as curSubTotalPrice";
        $aryQuery[] = "        , rd1.strNote ";
        $aryQuery[] = "      FROM";
        $aryQuery[] = "        t_ReceiveDetail rd1 ";
        $aryQuery[] = "        LEFT JOIN (";
        $aryQuery[] = "            select p1.*  from m_product p1 ";
        $aryQuery[] = "        	inner join (select max(lngRevisionNo) lngRevisionNo, strproductcode from m_Product group by strProductCode) p2";
        $aryQuery[] = "            on p1.lngRevisionNo = p2.lngRevisionNo and p1.strproductcode = p2.strproductcode ";
        $aryQuery[] = "          ) p ";
        $aryQuery[] = "          ON rd1.strProductCode = p.strProductCode ";
        $aryQuery[] = "        left join m_group mg ";
        $aryQuery[] = "          on p.lnginchargegroupcode = mg.lnggroupcode ";
        $aryQuery[] = "        left join m_user mu ";
        $aryQuery[] = "          on p.lnginchargeusercode = mu.lngusercode ";
        $aryQuery[] = "        left join m_salesclass ms ";
        $aryQuery[] = "          on ms.lngsalesclasscode = rd1.lngsalesclasscode ";
        $aryQuery[] = "        left join m_productunit mp ";
        $aryQuery[] = "          on mp.lngproductunitcode = rd1.lngproductunitcode ";
        $aryQuery[] = "    ) as rd ";
        $aryQuery[] = "WHERE";
        $aryQuery[] = " rd.lngReceiveNo = r.lngReceiveNo ";
        $aryQuery[] = " AND rd.lngRevisionNo = r.lngRevisionNo ";
        $aryQuery[] = " AND r.strReceiveCode = '" . $strCode . "'";
        $aryQuery[] = " AND rd.lngReceiveDetailNo = '" . $lngDetailNo . "'";
        $aryQuery[] = " AND r.lngRevisionNo <> " . $lngRevisionNo . "";
        $aryQuery[] = " AND r.lngRevisionNo >= 0 ";
        $aryQuery[] = " AND r.bytInvalidFlag = FALSE ";
        $aryQuery[] = "ORDER BY";
        $aryQuery[] = " r.strReceiveCode, rd.lngReceiveDetailNo, r.lngRevisionNo DESC";
    } else if ($type == 'sc') { // 売上
        $aryQuery[] = "SELECT distinct";
        $aryQuery[] = "  s.lngSalesNo as lngPkNo";
        $aryQuery[] = "  , s.lngSalesNo as lngSalesNo";
        $aryQuery[] = "  , s.lngRevisionNo as lngRevisionNo";
        $aryQuery[] = "  , to_char(s.dtmInsertDate, 'YYYY/MM/DD HH24:MI:SS') as dtmInsertDate";
        $aryQuery[] = "  , to_char(s.dtmappropriationdate, 'YYYY/MM/DD') as dtmappropriationdate";
        $aryQuery[] = "  , s.strSalesCode as strSalesCode";
        $aryQuery[] = "  , s.strSalesCode as strCode";
        $aryQuery[] = "  , sd.strCustomerReceiveCode as strCustomerReceiveCode";
        $aryQuery[] = "  , s.strSlipCode as strSlipCode";
        $aryQuery[] = "  , s.lngInputUserCode as lngInputUserCode";
        $aryQuery[] = "  , input_u.strUserDisplayCode as strInputUserDisplayCode";
        $aryQuery[] = "  , input_u.strUserDisplayName as strInputUserDisplayName";
        $aryQuery[] = "  , s.lngCustomerCompanyCode";
        $aryQuery[] = "  , cust_c.strCompanyDisplayCode as strcompanydisplaycode";
        $aryQuery[] = "  , cust_c.strCompanyDisplayName as strcompanydisplayname";
        $aryQuery[] = "  , s.lngSalesStatusCode as lngSalesStatusCode";
        $aryQuery[] = "  , ss.strSalesStatusName as strSalesStatusName";
        $aryQuery[] = "  , ss.strSalesStatusName as strStatusName";
        $aryQuery[] = "  , s.strNote as strNote";
        $aryQuery[] = "  , To_char(s.curTotalPrice, '9,999,999,990.99') as curTotalPrice";
        $aryQuery[] = "  , mu.strMonetaryUnitSign as strMonetaryUnitSign ";
        $aryQuery[] = "  , s.lngMonetaryUnitCode as lngMonetaryUnitCode ";
        $aryQuery[] = "FROM";
        $aryQuery[] = "  m_Sales s ";
        $aryQuery[] = "  LEFT JOIN m_User input_u ";
        $aryQuery[] = "    ON s.lngInputUserCode = input_u.lngUserCode ";
        $aryQuery[] = "  LEFT JOIN m_Company cust_c ";
        $aryQuery[] = "    ON s.lngCustomerCompanyCode = cust_c.lngCompanyCode ";
        $aryQuery[] = "  LEFT JOIN m_SalesStatus ss ";
        $aryQuery[] = "    USING (lngSalesStatusCode) ";
        $aryQuery[] = "  LEFT JOIN m_MonetaryUnit mu ";
        $aryQuery[] = "    ON s.lngMonetaryUnitCode = mu.lngMonetaryUnitCode";
        $aryQuery[] = "  , ( ";
        $aryQuery[] = "      SELECT distinct";
        $aryQuery[] = "          on (sd1.lngSalesNo) sd1.lngSalesNo";
        $aryQuery[] = "        , sd1.lngSalesDetailNo";
        $aryQuery[] = "        , sd1.lngRevisionNo";
        $aryQuery[] = "        , p.strProductCode";
        $aryQuery[] = "        , mg.strGroupDisplayCode";
        $aryQuery[] = "        , mg.strGroupDisplayName";
        $aryQuery[] = "        , mu.struserdisplaycode";
        $aryQuery[] = "        , mu.struserdisplayname";
        $aryQuery[] = "        , p.strProductName";
        $aryQuery[] = "        , p.strProductEnglishName";
        $aryQuery[] = "        , sd1.lngSalesClassCode";
        $aryQuery[] = "        , ms.strSalesClassName";
        $aryQuery[] = "        , p.strGoodsCode";
        $aryQuery[] = "        , sd1.curProductPrice";
        $aryQuery[] = "        , sd1.lngProductUnitCode";
        $aryQuery[] = "        , mp.strproductunitname";
        $aryQuery[] = "        , sd1.lngProductQuantity";
        $aryQuery[] = "        , sd1.curSubTotalPrice";
        $aryQuery[] = "        , sd1.lngTaxClassCode";
        $aryQuery[] = "        , mtc.strtaxclassname";
        $aryQuery[] = "        , mt.curtax";
        $aryQuery[] = "        , sd1.curtaxprice";
        $aryQuery[] = "        , sd1.strNote ";
        $aryQuery[] = "        , r.strCustomerReceiveCode ";
        $aryQuery[] = "      FROM";
        $aryQuery[] = "        t_SalesDetail sd1 ";
        $aryQuery[] = "        LEFT JOIN (";
        $aryQuery[] = "            select p1.*  from m_product p1 ";
        $aryQuery[] = "        	inner join (select max(lngRevisionNo) lngRevisionNo, strproductcode, strrevisecode from m_Product group by strProductCode, strrevisecode) p2";
        $aryQuery[] = "            on p1.lngRevisionNo = p2.lngRevisionNo and p1.strproductcode = p2.strproductcode and p1.strrevisecode = p2.strrevisecode";
        $aryQuery[] = "          ) p ";
        $aryQuery[] = "          ON sd1.strProductCode = p.strProductCode ";
        $aryQuery[] = "          AND sd1.strrevisecode = p.strrevisecode ";
        $aryQuery[] = "        left join m_group mg ";
        $aryQuery[] = "          on p.lnginchargegroupcode = mg.lnggroupcode ";
        $aryQuery[] = "        left join m_user mu ";
        $aryQuery[] = "          on p.lnginchargeusercode = mu.lngusercode ";
        $aryQuery[] = "        left join m_tax mt ";
        $aryQuery[] = "          on mt.lngtaxcode = sd1.lngtaxcode ";
        $aryQuery[] = "        left join m_taxclass mtc ";
        $aryQuery[] = "          on mtc.lngtaxclasscode = sd1.lngtaxclasscode ";
        $aryQuery[] = "        left join m_salesclass ms ";
        $aryQuery[] = "          on ms.lngsalesclasscode = sd1.lngsalesclasscode";
        $aryQuery[] = "        left join m_productunit mp ";
        $aryQuery[] = "          on mp.lngproductunitcode = sd1.lngproductunitcode ";
        $aryQuery[] = "        left join m_Receive r ";
        $aryQuery[] = "          on sd1.lngreceiveno = r.lngreceiveno ";
        $aryQuery[] = "          and sd1.lngreceiverevisionno = r.lngrevisionno ";
        $aryQuery[] = "    ) as sd ";
        $aryQuery[] = "WHERE";
        $aryQuery[] = " sd.lngSalesNo = s.lngSalesNo ";
        $aryQuery[] = " AND s.strSalesCode = '" . $strCode . "'";
        $aryQuery[] = " AND s.bytInvalidFlag = FALSE ";
        $aryQuery[] = " AND s.lngRevisionNo <> " . $lngRevisionNo . " ";
        $aryQuery[] = " AND s.lngRevisionNo >= 0 ";
        $aryQuery[] = "ORDER BY";
        $aryQuery[] = "  strSalesCode, lngRevisionNo DESC";
    } else if ($type == 'slip') { //納品書
        $aryQuery[] = "SELECT distinct";
        $aryQuery[] = "  s.lngSlipNo as lngPkNo";
        $aryQuery[] = "  , s.lngSlipNo as lngSlipNo";
        $aryQuery[] = "  , s.lngSalesNo as lngSalesNo";
        $aryQuery[] = "  , s.lngRevisionNo as lngRevisionNo";
        $aryQuery[] = "  , s.dtmInsertDate as dtmInsertDate";
        $aryQuery[] = "  , cust_c.strcompanydisplaycode as strCustomerDisplayCode";
        $aryQuery[] = "  , cust_c.strcompanydisplayname as strCustomerDisplayName";
        $aryQuery[] = "  , cust_c.lngCountryCode as lngcountrycode";
        $aryQuery[] = "  , sa.lngInvoiceNo as lnginvoiceno";
        $aryQuery[] = "  , s.strTaxClassName as strTaxClassName";
        $aryQuery[] = "  , s.strSlipCode as strSlipCode";
        $aryQuery[] = "  , s.strSlipCode as strCode";
        $aryQuery[] = "  , to_char(s.dtmDeliveryDate, 'YYYY/MM/DD HH:MI:SS') as dtmDeliveryDate";
        $aryQuery[] = "  , delv_c.strcompanydisplaycode as strdeliveryplacecode";
        $aryQuery[] = "  , s.strDeliveryPlaceName as strDeliveryPlaceName";
        $aryQuery[] = "  , insert_u.struserdisplaycode as strInsertUserCode";
        $aryQuery[] = "  , s.strInsertUserName as strInsertUserName";
        $aryQuery[] = "  , s.strNote as strNote";
        $aryQuery[] = "  , To_char(s.curTotalPrice, '9,999,999,990.99') as curTotalPrice";
        $aryQuery[] = "  , sa.strSalesCode as strSalesCode";
        $aryQuery[] = "  , sa.lngSalesStatusCode as lngSalesStatusCode";
        $aryQuery[] = "  , ss.strSalesStatusName as strSalesStatusName";
        $aryQuery[] = "  , ss.strSalesStatusName as strStatusName";
        $aryQuery[] = "  , s.lngMonetaryUnitCode ";
        $aryQuery[] = "  , mu.strMonetaryUnitSign as strMonetaryUnitSign ";
        $aryQuery[] = "FROM";
        $aryQuery[] = "  m_Slip s ";
        $aryQuery[] = "  INNER JOIN m_Sales sa ";
        $aryQuery[] = "    ON s.lngSalesNo = sa.lngSalesNo ";
        $aryQuery[] = "    AND s.lngRevisionNo = sa.lngRevisionNo ";
        $aryQuery[] = "  LEFT JOIN m_SalesStatus ss ";
        $aryQuery[] = "    ON sa.lngSalesStatusCode = ss.lngSalesStatusCode ";
        $aryQuery[] = "  LEFT JOIN m_Company cust_c ";
        $aryQuery[] = "    ON s.lngCustomerCode = cust_c.lngCompanyCode ";
        $aryQuery[] = "  LEFT JOIN m_MonetaryUnit mu ";
        $aryQuery[] = "    ON s.lngMonetaryUnitCode = mu.lngMonetaryUnitCode ";
        $aryQuery[] = "  LEFT JOIN m_User insert_u ";
        $aryQuery[] = "    ON s.lngInsertUserCode = insert_u.lngusercode ";
        $aryQuery[] = "  LEFT JOIN m_Company delv_c ";
        $aryQuery[] = "    ON s.lngDeliveryPlaceCode = delv_c.lngCompanyCode";
        $aryQuery[] = "WHERE";
        $aryQuery[] = "  s.bytInvalidFlag = FALSE ";
        $aryQuery[] = "  AND s.lngslipno = " . $strCode . "";
        $aryQuery[] = "  AND s.lngRevisionNo <>" . $lngRevisionNo . "";
        $aryQuery[] = "  AND s.lngRevisionNo >= 0 ";
        $aryQuery[] = "ORDER BY";
        $aryQuery[] = "  s.lngrevisionno DESC";
    } else if ($type == 'pc') { // 仕入
        $aryQuery[] = "SELECT";
        $aryQuery[] = "  s.lngStockNo as lngPkNo";
        $aryQuery[] = "  , s.lngStockNo as lngStockNo";
        $aryQuery[] = "  , s.lngRevisionNo as lngRevisionNo";
        $aryQuery[] = "  , sd.strordercode";
        $aryQuery[] = "  , to_char(s.dtmInsertDate, 'YYYY/MM/DD HH24:MI:SS') as dtmInsertDate";
        $aryQuery[] = "  , to_char(s.dtmappropriationdate, 'YYYY/MM/DD') as dtmappropriationdate";
        $aryQuery[] = "  , to_char(s.dtmexpirationdate, 'YYYY/MM/DD') as dtmexpirationdate";
        $aryQuery[] = "  , input_u.strUserDisplayCode as strInputUserDisplayCode";
        $aryQuery[] = "  , input_u.strUserDisplayName as strInputUserDisplayName";
        $aryQuery[] = "  , s.strStockCode as strStockCode";
        $aryQuery[] = "  , s.strStockCode as strCode";
        $aryQuery[] = "  , s.strslipcode as strslipcode";
        $aryQuery[] = "  , cust_c.strCompanyDisplayCode as strCustomerDisplayCode";
        $aryQuery[] = "  , cust_c.strCompanyDisplayName as strCustomerDisplayName";
        $aryQuery[] = "  , s.lngStockStatusCode as lngStockStatusCode";
        $aryQuery[] = "  , rs.strStockStatusName as strStockStatusName";
        $aryQuery[] = "  , rs.strStockStatusName as strStatusName";
        $aryQuery[] = "  , s.lngpayconditioncode as lngpayconditioncode";
        $aryQuery[] = "  , mp.strpayconditionname as strpayconditionname";
        $aryQuery[] = "  , s.strNote as strNote";
        $aryQuery[] = "  , To_char(s.curTotalPrice, '9,999,999,990.99') as curTotalPrice";
        $aryQuery[] = "  , mu.strMonetaryUnitSign as strMonetaryUnitSign";
        $aryQuery[] = "  , mu.lngmonetaryunitcode as lngmonetaryunitcode ";
        $aryQuery[] = "FROM";
        $aryQuery[] = "  m_Stock s ";
        $aryQuery[] = "  LEFT JOIN m_User input_u ";
        $aryQuery[] = "    ON s.lngInputUserCode = input_u.lngUserCode ";
        $aryQuery[] = "  LEFT JOIN m_Company cust_c ";
        $aryQuery[] = "    ON s.lngCustomerCompanyCode = cust_c.lngCompanyCode ";
        $aryQuery[] = "  LEFT JOIN m_StockStatus rs ";
        $aryQuery[] = "    USING (lngStockStatusCode) ";
        $aryQuery[] = "  LEFT JOIN m_MonetaryUnit mu ";
        $aryQuery[] = "    ON s.lngMonetaryUnitCode = mu.lngMonetaryUnitCode";
        $aryQuery[] = "  LEFT JOIN m_paycondition mp ";
        $aryQuery[] = "    ON s.lngpayconditioncode = mp.lngpayconditioncode";
        $aryQuery[] = "  LEFT JOIN ( ";
        $aryQuery[] = "    SELECT distinct";
        $aryQuery[] = "        on (sd1.lngStockNo) sd1.lngStockNo";
        $aryQuery[] = "      , o.strordercode ";
        $aryQuery[] = "    FROM";
        $aryQuery[] = "      t_StockDetail sd1 ";
        $aryQuery[] = "      INNER JOIN t_purchaseorderdetail tp ";
        $aryQuery[] = "        on tp.lngorderno = sd1.lngorderno ";
        $aryQuery[] = "        and tp.lngorderdetailno = sd1.lngorderdetailno ";
        $aryQuery[] = "        and tp.lngorderrevisionno = sd1.lngorderrevisionno ";
        $aryQuery[] = "      INNER JOIN m_purchaseorder o ";
        $aryQuery[] = "        on o.lngpurchaseorderno = tp.lngpurchaseorderno ";
        $aryQuery[] = "        and o.lngrevisionno = tp.lngrevisionno";
        $aryQuery[] = "  ) as sd ";
        $aryQuery[] = "    on sd.lngStockNo = s.lngStockNo ";
        $aryQuery[] = "WHERE";
        $aryQuery[] = " s.strStockCode = '" . $strCode . "'";
        $aryQuery[] = " AND s.lngrevisionno <> " . $lngRevisionNo . " ";
        $aryQuery[] = " AND s.lngRevisionNo >= 0 ";
        $aryQuery[] = " AND s.bytInvalidFlag = FALSE ";
        $aryQuery[] = "ORDER BY";
        $aryQuery[] = " strStockCode, lngRevisionNo DESC";
    } else if ($type == 'inv') {
        $aryQuery[] = "SELECT distinct";
        $aryQuery[] = "  inv.lnginvoiceno as lngPkNo";
        $aryQuery[] = "  , inv.lnginvoiceno as lnginvoiceno";
        $aryQuery[] = "  , inv.lngrevisionno as lngrevisionno";
        $aryQuery[] = "  , inv.dtminsertdate as dtminsertdate";
        $aryQuery[] = "  , cust_c.strcompanydisplaycode as strcustomerdisplaycode";
        $aryQuery[] = "  , inv.strcustomername as strcustomername";
        $aryQuery[] = "  , inv.strcustomercompanyname as strcustomerdisplayname";
        $aryQuery[] = "  , cust_c.lngCountryCode as lngcountrycode";
        $aryQuery[] = "  , inv.strinvoicecode as strinvoicecode";
        $aryQuery[] = "  , inv.strinvoicecode as strcode";
        $aryQuery[] = "  , to_char(inv.dtminvoicedate, 'YYYY/MM/DD') as dtminvoicedate";
        $aryQuery[] = "  , to_char(inv.dtmchargeternstart, 'YYYY/MM/DD') as dtmchargeternstart";
        $aryQuery[] = "  , to_char(inv.dtmchargeternend, 'YYYY/MM/DD') as dtmchargeternend";
        $aryQuery[] = "  , To_char(inv.curlastmonthbalance, '9,999,999,990.99') as curlastmonthbalance";
        $aryQuery[] = "  , To_char(inv.curthismonthamount, '9,999,999,990.99') as curthismonthamount";
        $aryQuery[] = "  , inv.lngmonetaryunitcode as lngmonetaryunitcode";
        $aryQuery[] = "  , inv.strmonetaryunitsign as strmonetaryunitsign";
        $aryQuery[] = "  , inv.lngtaxclasscode as lngtaxclasscode";
        $aryQuery[] = "  , inv.strtaxclassname as strtaxclassname";
        $aryQuery[] = "  , To_char(inv.cursubtotal1, '9,999,999,990.99') as cursubtotal";
        $aryQuery[] = "  , inv.curtax1 as curtax1";
        $aryQuery[] = "  , To_char(inv.curtaxprice1, '9,999,999,990.99') as curtaxprice";
        $aryQuery[] = "  , u.struserdisplaycode as strusercode";
        $aryQuery[] = "  , inv.strusername as strusername";
        $aryQuery[] = "  , insert_u.struserdisplaycode as strinsertusercode";
        $aryQuery[] = "  , inv.strinsertusername as strinsertusername";
        $aryQuery[] = "  , to_char(inv.dtminsertdate, 'YYYY/MM/DD') as dtminsertdate";
        $aryQuery[] = "  , inv.strnote as strnote";
        $aryQuery[] = "  , inv.lngprintcount as lngprintcount";
        $aryQuery[] = "  , sa.lngSalesStatusCode as lngSalesStatusCode";
        $aryQuery[] = "  , ss.strSalesStatusName as strSalesStatusName ";
        $aryQuery[] = "  , ss.strSalesStatusName as strStatusName ";
        $aryQuery[] = "FROM";
        $aryQuery[] = "  m_invoice inv ";
        $aryQuery[] = "  LEFT JOIN m_sales sa ";
        $aryQuery[] = "    ON inv.lnginvoiceno = sa.lnginvoiceno ";
        $aryQuery[] = "  LEFT JOIN m_SalesStatus ss ";
        $aryQuery[] = "    ON sa.lngSalesStatusCode = ss.lngSalesStatusCode ";
        $aryQuery[] = "  LEFT JOIN m_Company cust_c ";
        $aryQuery[] = "    ON inv.lngcustomercode = cust_c.lngcompanycode ";
        $aryQuery[] = "  LEFT JOIN m_MonetaryUnit mu ";
        $aryQuery[] = "    ON inv.lngmonetaryunitcode = mu.lngMonetaryUnitCode ";
        $aryQuery[] = "  LEFT JOIN m_User insert_u ";
        $aryQuery[] = "    ON inv.lngInsertUserCode = insert_u.lngusercode ";
        $aryQuery[] = "  LEFT JOIN m_User u ";
        $aryQuery[] = "    ON inv.lngusercode = u.lngusercode ";
        $aryQuery[] = "  INNER JOIN t_invoicedetail inv_d ";
        $aryQuery[] = "    ON inv.lnginvoiceno = inv_d.lnginvoiceno ";
        $aryQuery[] = "WHERE";
        $aryQuery[] = "  inv.bytinvalidflag = FALSE ";
        $aryQuery[] = "  AND inv.strinvoicecode = '" . $strCode . "'";
        $aryQuery[] = "  AND inv.lngrevisionno <> " . $lngRevisionNo . "";
        $aryQuery[] = "  AND inv.lngRevisionNo >= 0 ";
        $aryQuery[] = "ORDER BY";
        $aryQuery[] = "  inv.lngrevisionno DESC";
    } else if ($type == 'estimate') {
        $aryQuery[] = "SELECT";
        $aryQuery[] = "  TO_CHAR(me.dtmInsertDate, 'YYYY/MM/DD') AS dtminsertdate";
        $aryQuery[] = "  , mp.strproductcode";
        $aryQuery[] = "  , mp.strproductname";
        $aryQuery[] = "  , mp.strproductenglishname";
        $aryQuery[] = "  , '[' || mg.strgroupdisplaycode || ']' || mg.strgroupdisplayname AS lnginchargegroupcode";
        $aryQuery[] = "  , '[' || mu1.struserdisplaycode || ']' || mu1.struserdisplayname AS lnginchargeusercode";
        $aryQuery[] = "  , '[' || mu2.struserdisplaycode || ']' || mu2.struserdisplayname AS lngdevelopusercode";
        $aryQuery[] = "  , mp.curretailprice";
        $aryQuery[] = "  , '[' || mu3.struserdisplaycode || ']' || mu3.struserdisplayname AS lnginputusercode";
        $aryQuery[] = "  , mp.lngcartonquantity";
        $aryQuery[] = "  , mp.lngproductionquantity";
        $aryQuery[] = "  , me.cursalesamount";
        $aryQuery[] = "  , me.cursalesamount - me.curmanufacturingcost AS cursalesprofit";
        $aryQuery[] = "  , CASE ";
        $aryQuery[] = "    WHEN me.cursalesamount = 0 ";
        $aryQuery[] = "      THEN 0 ";
        $aryQuery[] = "    ELSE (me.cursalesamount - me.curmanufacturingcost) / me.cursalesamount * 100 ";
        $aryQuery[] = "    END AS cursalesprofitrate";
        $aryQuery[] = "  , tsum.curfixedcostsales";
        $aryQuery[] = "  , tsum.curfixedcostsales - tsum.curnotdepreciationcost AS curfixedcostsalesprofit";
        $aryQuery[] = "  , CASE ";
        $aryQuery[] = "    WHEN tsum.curfixedcostsales = 0 ";
        $aryQuery[] = "      THEN 0 ";
        $aryQuery[] = "    ELSE ( ";
        $aryQuery[] = "      tsum.curfixedcostsales - tsum.curnotdepreciationcost";
        $aryQuery[] = "    ) / tsum.curfixedcostsales * 100 ";
        $aryQuery[] = "    END AS curfixedcostsalesprofitrate";
        $aryQuery[] = "  , me.cursalesamount + tsum.curfixedcostsales AS curtotalsales";
        $aryQuery[] = "  , me.curtotalprice";
        $aryQuery[] = "  , CASE ";
        $aryQuery[] = "    WHEN me.cursalesamount + tsum.curfixedcostsales = 0 ";
        $aryQuery[] = "      THEN 0 ";
        $aryQuery[] = "    ELSE me.curtotalprice / (me.cursalesamount + tsum.curfixedcostsales) * 100 ";
        $aryQuery[] = "    END AS curtotalpricerate";
        $aryQuery[] = "  , me.curtotalprice - me.curprofit AS curindirectmanufacturingcost";
        $aryQuery[] = "  , CASE ";
        $aryQuery[] = "    WHEN me.cursalesamount + tsum.curfixedcostsales = 0 ";
        $aryQuery[] = "      THEN 0 ";
        $aryQuery[] = "    ELSE (me.curtotalprice - me.curprofit) / (me.cursalesamount + tsum.curfixedcostsales) * 100 ";
        $aryQuery[] = "    END AS curstandardrate";
        $aryQuery[] = "  , me.curprofit";
        $aryQuery[] = "  , CASE ";
        $aryQuery[] = "    WHEN me.cursalesamount + tsum.curfixedcostsales = 0 ";
        $aryQuery[] = "      THEN 0 ";
        $aryQuery[] = "    ELSE me.curprofit / (me.cursalesamount + tsum.curfixedcostsales) * 100 ";
        $aryQuery[] = "    END AS curprofitrate";
        $aryQuery[] = "  , me.curmembercost";
        $aryQuery[] = "  , CASE ";
        $aryQuery[] = "    WHEN mp.lngproductionquantity = 0 ";
        $aryQuery[] = "      THEN 0 ";
        $aryQuery[] = "    ELSE me.curmembercost / mp.lngproductionquantity ";
        $aryQuery[] = "    END AS curmembercostpieces";
        $aryQuery[] = "  , me.curfixedcost";
        $aryQuery[] = "  , CASE ";
        $aryQuery[] = "    WHEN mp.lngproductionquantity = 0 ";
        $aryQuery[] = "      THEN 0 ";
        $aryQuery[] = "    ELSE me.curfixedcost / mp.lngproductionquantity ";
        $aryQuery[] = "    END AS curfixedcostpieces";
        $aryQuery[] = "  , me.curmanufacturingcost AS curmanufacturingcost";
        $aryQuery[] = "  , CASE ";
        $aryQuery[] = "    WHEN mp.lngproductionquantity = 0 ";
        $aryQuery[] = "      THEN 0 ";
        $aryQuery[] = "    ELSE me.curmanufacturingcost / mp.lngproductionquantity ";
        $aryQuery[] = "    END AS curmanufacturingcostpieces";
        $aryQuery[] = "  , CASE ";
        $aryQuery[] = "    WHEN tsum.countofreceiveandorderdetail = tsum.countofaplicatedetail ";
        $aryQuery[] = "      THEN TRUE ";
        $aryQuery[] = "    ELSE FALSE ";
        $aryQuery[] = "    END AS deleteflag";
        $aryQuery[] = "  , me.lngPkNo";
        $aryQuery[] = "  , mp.strrevisecode";
        $aryQuery[] = "  , me.lngrevisionno";
        $aryQuery[] = "  , me.lngrevisionno AS lngmaxrevisionno ";
        $aryQuery[] = "FROM";
        $aryQuery[] = "  m_estimate me ";
        $aryQuery[] = "  INNER JOIN m_product mp ";
        $aryQuery[] = "    ON mp.strproductcode = me.strproductcode ";
        $aryQuery[] = "    AND mp.strrevisecode = me.strrevisecode ";
        $aryQuery[] = "    AND mp.lngrevisionno = me.lngproductrevisionno ";
        $aryQuery[] = "  INNER JOIN m_group mg ";
        $aryQuery[] = "    ON mg.lnggroupcode = mp.lnginchargegroupcode ";
        $aryQuery[] = "  INNER JOIN m_user mu1 ";
        $aryQuery[] = "    ON mu1.lngusercode = mp.lnginchargeusercode ";
        $aryQuery[] = "  LEFT OUTER JOIN m_user mu2 ";
        $aryQuery[] = "    ON mu2.lngusercode = mp.lngdevelopusercode ";
        $aryQuery[] = "  INNER JOIN m_user mu3 ";
        $aryQuery[] = "    ON mu3.lngusercode = mp.lnginputusercode ";
        $aryQuery[] = "  LEFT OUTER JOIN ( ";
        $aryQuery[] = "    SELECT";
        $aryQuery[] = "      me.lngestimateno";
        $aryQuery[] = "      , me.lngrevisionno";
        $aryQuery[] = "      , SUM( ";
        $aryQuery[] = "        CASE ";
        $aryQuery[] = "          WHEN mscdl.lngestimateareaclassno = 2 ";
        $aryQuery[] = "            THEN ted.curconversionrate * ted.cursubtotalprice ";
        $aryQuery[] = "          ELSE 0 ";
        $aryQuery[] = "          END";
        $aryQuery[] = "      ) AS curfixedcostsales";
        $aryQuery[] = "      , SUM( ";
        $aryQuery[] = "        CASE ";
        $aryQuery[] = "          WHEN msi.lngestimateareaclassno = 3 ";
        $aryQuery[] = "          AND ted.bytpayofftargetflag = FALSE ";
        $aryQuery[] = "            THEN ted.curconversionrate * ted.cursubtotalprice ";
        $aryQuery[] = "          ELSE 0 ";
        $aryQuery[] = "          END";
        $aryQuery[] = "      ) AS curnotdepreciationcost";
        $aryQuery[] = "      , count( ";
        $aryQuery[] = "        mscdl.lngestimateareaclassno <> 0 ";
        $aryQuery[] = "        OR msi.lngestimateareaclassno <> 5 ";
        $aryQuery[] = "        OR NULL";
        $aryQuery[] = "      ) AS countofreceiveandorderdetail";
        $aryQuery[] = "      , count( ";
        $aryQuery[] = "        mr.lngreceivestatuscode = 1 ";
        $aryQuery[] = "        OR mo.lngorderstatuscode = 1 ";
        $aryQuery[] = "        OR NULL";
        $aryQuery[] = "      ) AS countofaplicatedetail ";
        $aryQuery[] = "    FROM";
        $aryQuery[] = "      t_estimatedetail ted ";
        $aryQuery[] = "      INNER JOIN m_estimate me ";
        $aryQuery[] = "        ON me.lngestimateno = ted.lngestimateno ";
        $aryQuery[] = "        AND me.lngrevisionno = ted.lngrevisionno ";
        $aryQuery[] = "      LEFT OUTER JOIN m_salesclassdivisonlink mscdl ";
        $aryQuery[] = "        ON mscdl.lngsalesclasscode = ted.lngsalesclasscode ";
        $aryQuery[] = "        AND mscdl.lngsalesdivisioncode = ted.lngsalesdivisioncode ";
        $aryQuery[] = "      LEFT OUTER JOIN m_stockitem msi ";
        $aryQuery[] = "        ON msi.lngstocksubjectcode = ted.lngstocksubjectcode ";
        $aryQuery[] = "        AND msi.lngstockitemcode = ted.lngstockitemcode ";
        $aryQuery[] = "      LEFT OUTER JOIN t_receivedetail trd ";
        $aryQuery[] = "        ON trd.lngestimateno = ted.lngestimateno ";
        $aryQuery[] = "        AND trd.lngestimatedetailno = ted.lngestimatedetailno ";
        $aryQuery[] = "        AND trd.lngestimaterevisionno = ted.lngrevisionno ";
        $aryQuery[] = "      LEFT OUTER JOIN m_receive mr ";
        $aryQuery[] = "        ON mr.lngreceiveno = trd.lngreceiveno ";
        $aryQuery[] = "        AND mr.lngrevisionno = trd.lngrevisionno ";
        $aryQuery[] = "      LEFT OUTER JOIN t_orderdetail tod ";
        $aryQuery[] = "        ON tod.lngestimateno = ted.lngestimateno ";
        $aryQuery[] = "        AND tod.lngestimatedetailno = ted.lngestimatedetailno ";
        $aryQuery[] = "        AND tod.lngestimaterevisionno = ted.lngrevisionno ";
        $aryQuery[] = "      LEFT OUTER JOIN m_order mo ";
        $aryQuery[] = "        ON mo.lngorderno = tod.lngorderno ";
        $aryQuery[] = "        AND mo.lngrevisionno = tod.lngrevisionno ";
        $aryQuery[] = "    GROUP BY";
        $aryQuery[] = "      me.lngestimateno";
        $aryQuery[] = "      , me.lngrevisionno";
        $aryQuery[] = "  ) tsum ";
        $aryQuery[] = "    ON tsum.lngestimateno = me.lngestimateno ";
        $aryQuery[] = "    AND tsum.lngrevisionno = me.lngrevisionno ";
        $aryQuery[] = "WHERE";
        $aryQuery[] = "  me.lngestimateno = " . $strCode;
        $aryQuery[] = "  AND me.lngrevisionno <> " . $lngRevisionNo;
        $aryQuery[] = "  AND me.lngrevisionno >= 0 ";
        $aryQuery[] = "ORDER BY";
        $aryQuery[] = "  me.lngrevisionno DESC";
    }
    $strQuery = implode("\n", $aryQuery);
    echo $strQuery;
    // 値をとる =====================================
    list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);

    // 指定数以内であれば通常処理
    for ($i = 0; $i < $lngResultNum; $i++) {
        $result = pg_fetch_all($lngResultID);
    }

    $objDB->freeResult($lngResultID);

    return $result;

}

/**
 * 明細データを取得する
 *
 * @param [type] $type
 * @param [type] $lngPKNo
 * @param [type] $lngRevisionNo
 * @param [type] $objDB
 * @return void
 */
function fncGetDetailData($type, $lngPkNo, $lngRevisionNo, $objDB)
{
    $result = array();
    unset($aryQuery);
    if ($type == 'inv') { //請求書
        $aryQuery[] = "SELECT";
        $aryQuery[] = "  inv_d.lnginvoiceno as lnginvoiceno";
        $aryQuery[] = "  , inv_d.lngrevisionno as lngrevisionno";
        $aryQuery[] = "  , inv_d.lnginvoicedetailno";
        $aryQuery[] = "  , inv_d.lnginvoicedetailno as lngdetailno";
        $aryQuery[] = "  , inv_d.lnginvoicedetailno as lngrecodeno";
        $aryQuery[] = "  , to_char(inv_d.dtmdeliverydate, 'YYYY/MM/DD HH:MI:SS') as dtmdeliverydate";
        $aryQuery[] = "  , delv_c.strcompanydisplaycode as strdeliveryplacecode";
        $aryQuery[] = "  , inv_d.strdeliveryplacename as strdeliveryplacename";
        $aryQuery[] = "  , To_char(inv_d.cursubtotalprice, '9,999,999,990.99') as cursubtotalprice";
        $aryQuery[] = "  , To_char(inv_d.curtax * inv_d.cursubtotalprice, '9,999,999,990.99') as curtaxprice";
        $aryQuery[] = "  , inv_d.lngtaxclasscode as lngtaxclasscode";
        $aryQuery[] = "  , inv_d.strtaxclassname as strtaxclassname";
        $aryQuery[] = "  , inv_d.curtax as curtax";
        $aryQuery[] = "  , inv_d.strnote as strnote";
        $aryQuery[] = "  , inv_d.lngslipno as lngslipno";
        $aryQuery[] = "  , inv_d.lngsliprevisionno as lngsliprevisionno";
        $aryQuery[] = "  , slip_m.strslipcode as strslipcode ";
        $aryQuery[] = "FROM";
        $aryQuery[] = "  t_invoicedetail inv_d ";
        $aryQuery[] = "  LEFT JOIN m_slip slip_m ";
        $aryQuery[] = "    ON inv_d.lngslipno = slip_m.lngslipno ";
        $aryQuery[] = "    and inv_d.lngsliprevisionno = slip_m.lngrevisionno ";
        $aryQuery[] = "  LEFT JOIN m_Company delv_c ";
        $aryQuery[] = "    ON inv_d.lngDeliveryPlaceCode = delv_c.lngCompanyCode ";
        $aryQuery[] = "WHERE";
        $aryQuery[] = "  inv_d.lnginvoiceno = " . $lngPkNo;
        $aryQuery[] = "  AND inv_d.lngrevisionno = " . $lngRevisionNo;
        $aryQuery[] = "ORDER BY";
        $aryQuery[] = "  inv_d.lnginvoicedetailno ASC";
    } else if ($type == 'pc') { //仕入
        $aryQuery[] = "SELECT sd.lngStockNo";
        $aryQuery[] = "  , sd.lngStockDetailNo";
        $aryQuery[] = "  , sd.lngStockDetailNo as lngdetailno";
        $aryQuery[] = "  , p.strProductCode";
        $aryQuery[] = "  , mg.strGroupDisplayCode";
        $aryQuery[] = "  , mg.strGroupDisplayName";
        $aryQuery[] = "  , mu.struserdisplaycode";
        $aryQuery[] = "  , mu.struserdisplayname";
        $aryQuery[] = "  , p.strProductName";
        $aryQuery[] = "  , p.strProductEnglishName";
        $aryQuery[] = "  , sd.lngStockSubjectCode";
        $aryQuery[] = "  , ss.strStockSubjectName";
        $aryQuery[] = "  , sd.lngStockItemCode";
        $aryQuery[] = "  , si.strStockItemName";
        $aryQuery[] = "  , sd.strMoldNo";
        $aryQuery[] = "  , p.strGoodsCode";
        $aryQuery[] = "  , sd.lngDeliveryMethodCode";
        $aryQuery[] = "  , dm.strDeliveryMethodName";
        $aryQuery[] = "  , sd.curProductPrice";
        $aryQuery[] = "  , sd.lngProductUnitCode";
        $aryQuery[] = "  , pu.strProductUnitName";
        $aryQuery[] = "  , sd.lngProductQuantity";
        $aryQuery[] = "  , sd.curSubTotalPrice";
        $aryQuery[] = "  , sd.lngTaxClassCode";
        $aryQuery[] = "  , mtc.strTaxClassName";
        $aryQuery[] = "  , mt.curtax";
        $aryQuery[] = "  , to_char(sd.curTaxPrice, '9,999,999,990.99') as curTaxPrice";
        $aryQuery[] = "  , sd.strNote as strdetailnote";
        $aryQuery[] = "FROM";
        $aryQuery[] = "  t_StockDetail sd ";
        $aryQuery[] = "  LEFT JOIN ( ";
        $aryQuery[] = "    select";
        $aryQuery[] = "      p1.* ";
        $aryQuery[] = "    from";
        $aryQuery[] = "      m_product p1 ";
        $aryQuery[] = "      inner join ( ";
        $aryQuery[] = "        select";
        $aryQuery[] = "          max(lngrevisionno) lngrevisionno";
        $aryQuery[] = "          , strproductcode, strrevisecode ";
        $aryQuery[] = "        from";
        $aryQuery[] = "          m_Product ";
        $aryQuery[] = "        group by";
        $aryQuery[] = "          strProductCode,strrevisecode";
        $aryQuery[] = "      ) p2 ";
        $aryQuery[] = "        on p1.lngrevisionno = p2.lngrevisionno ";
        $aryQuery[] = "        and p1.strproductcode = p2.strproductcode";
        $aryQuery[] = "        and p1.strrevisecode = p2.strrevisecode";
        $aryQuery[] = "  ) p ";
        $aryQuery[] = "    ON sd.strProductCode = p.strProductCode ";
        $aryQuery[] = "    AND sd.strrevisecode = p.strrevisecode ";
        $aryQuery[] = "  left join m_group mg ";
        $aryQuery[] = "    on p.lnginchargegroupcode = mg.lnggroupcode ";
        $aryQuery[] = "  left join m_user mu ";
        $aryQuery[] = "    on p.lnginchargeusercode = mu.lngusercode ";
        $aryQuery[] = "  left join m_tax mt ";
        $aryQuery[] = "    on mt.lngtaxcode = sd.lngtaxcode ";
        $aryQuery[] = "  left join m_taxclass mtc ";
        $aryQuery[] = "    on mtc.lngtaxclasscode = sd.lngtaxclasscode ";
        $aryQuery[] = "  LEFT JOIN m_Stocksubject ss ";
        $aryQuery[] = "    on ss.lngStocksubjectcode = sd.lngStocksubjectcode ";
        $aryQuery[] = "  LEFT JOIN m_Stockitem si ";
        $aryQuery[] = "    on si.lngStocksubjectcode = sd.lngStocksubjectcode ";
        $aryQuery[] = "    and si.lngStockitemcode = sd.lngStockitemcode ";
        $aryQuery[] = "  LEFT JOIN m_deliverymethod dm ";
        $aryQuery[] = "    on dm.lngdeliverymethodcode = sd.lngdeliverymethodcode ";
        $aryQuery[] = "  LEFT JOIN m_productunit pu ";
        $aryQuery[] = "    on pu.lngproductunitcode = sd.lngproductunitcode ";
        $aryQuery[] = "where";
        $aryQuery[] = "  sd.lngStockNo = " . $lngPkNo;
        $aryQuery[] = "  and sd.lngrevisionno = " . $lngRevisionNo;
        $aryQuery[] = "order by sd.lngStockDetailNo";
    } else if ($type == 'sc') { //売上
        $aryQuery[] = "SELECT";
        $aryQuery[] = "  sd.lngSalesNo";
        $aryQuery[] = "  , sd.lngSalesDetailNo";
        $aryQuery[] = "  , sd.lngSalesDetailNo as lngdetailno";
        $aryQuery[] = "  , p.strProductCode";
        $aryQuery[] = "  , mg.strGroupDisplayCode";
        $aryQuery[] = "  , mg.strGroupDisplayName";
        $aryQuery[] = "  , mu.struserdisplaycode";
        $aryQuery[] = "  , mu.struserdisplayname";
        $aryQuery[] = "  , p.strProductName";
        $aryQuery[] = "  , p.strProductEnglishName";
        $aryQuery[] = "  , sd.lngSalesClassCode";
        $aryQuery[] = "  , ms.strSalesClassName";
        $aryQuery[] = "  , p.strGoodsCode";
        $aryQuery[] = "  , To_char(sd.curProductPrice, '9,999,999,990.99') as curProductPrice";
        $aryQuery[] = "  , sd.lngProductUnitCode";
        $aryQuery[] = "  , mp.strproductunitname";
        $aryQuery[] = "  , To_char(sd.lngProductQuantity, '9,999,999,990') as lngProductQuantity";
        $aryQuery[] = "  , To_char(sd.curSubTotalPrice, '9,999,999,990') as curSubTotalPrice";
        $aryQuery[] = "  , sd.lngTaxClassCode";
        $aryQuery[] = "  , mtc.strtaxclassname";
        $aryQuery[] = "  , mt.curtax";
        $aryQuery[] = "  , sd.curtaxprice";
        $aryQuery[] = "  , sd.strNote ";
        $aryQuery[] = "FROM";
        $aryQuery[] = "  t_SalesDetail sd ";
        $aryQuery[] = "  LEFT JOIN ( ";
        $aryQuery[] = "    select";
        $aryQuery[] = "      p1.* ";
        $aryQuery[] = "    from";
        $aryQuery[] = "      m_product p1 ";
        $aryQuery[] = "      inner join ( ";
        $aryQuery[] = "        select";
        $aryQuery[] = "          max(lngRevisionNo) lngRevisionNo";
        $aryQuery[] = "          , strproductcode, strrevisecode ";
        $aryQuery[] = "        from";
        $aryQuery[] = "          m_Product ";
        $aryQuery[] = "        group by";
        $aryQuery[] = "          strProductCode, strrevisecode";
        $aryQuery[] = "      ) p2 ";
        $aryQuery[] = "        on p1.lngRevisionNo = p2.lngRevisionNo ";
        $aryQuery[] = "        and p1.strproductcode = p2.strproductcode";
        $aryQuery[] = "        and p1.strrevisecode = p2.strrevisecode";
        $aryQuery[] = "  ) p ";
        $aryQuery[] = "    ON sd.strProductCode = p.strProductCode ";
        $aryQuery[] = "    AND sd.strrevisecode = p.strrevisecode ";
        $aryQuery[] = "  left join m_group mg ";
        $aryQuery[] = "    on p.lnginchargegroupcode = mg.lnggroupcode ";
        $aryQuery[] = "  left join m_user mu ";
        $aryQuery[] = "    on p.lnginchargeusercode = mu.lngusercode ";
        $aryQuery[] = "  left join m_tax mt ";
        $aryQuery[] = "    on mt.lngtaxcode = sd.lngtaxcode ";
        $aryQuery[] = "  left join m_taxclass mtc ";
        $aryQuery[] = "    on mtc.lngtaxclasscode = sd.lngtaxclasscode ";
        $aryQuery[] = "  left join m_salesclass ms ";
        $aryQuery[] = "    on ms.lngsalesclasscode = sd.lngsalesclasscode ";
        $aryQuery[] = "  left join m_productunit mp ";
        $aryQuery[] = "    on mp.lngproductunitcode = sd.lngproductunitcode ";
        $aryQuery[] = "where";
        $aryQuery[] = " sd.lngsalesno = " . $lngPkNo;
        $aryQuery[] = " and sd.lngrevisionno = " . $lngRevisionNo;
        $aryQuery[] = " order by sd.lngSalesDetailNo";
    } else if ($type == 'slip') { //納品書
        $aryQuery[] = "select";
        $aryQuery[] = "  sd.lngSlipDetailNo";
        $aryQuery[] = "  , sd.lngSlipDetailNo as lngdetailno";
        $aryQuery[] = "  , sd.strCustomerSalesCode";
        $aryQuery[] = "  , sd.strGoodsCode";
        $aryQuery[] = "  , sd.strProductCode";
        $aryQuery[] = "  , sd.strProductName";
        $aryQuery[] = "  , sd.lngsalesclasscode";
        $aryQuery[] = "  , sd.strSalesClassName";
        $aryQuery[] = "  , to_char(sd.curProductPrice, '9,999,999,990.99') as curProductPrice";
        $aryQuery[] = "  , to_char(sd.lngQuantity, '9,999,999,990.99') as lngQuantity";
        $aryQuery[] = "  , to_char(sd.lngProductQuantity, '9,999,999,990.99') as lngProductQuantity";
        $aryQuery[] = "  , sd.strProductUnitName";
        $aryQuery[] = "  , to_char(sd.curSubTotalPrice, '9,999,999,990.99') as curSubTotalPrice";
        $aryQuery[] = "  , sd.strNote ";
        $aryQuery[] = "from";
        $aryQuery[] = "  t_slipdetail sd ";
        $aryQuery[] = "where";
        $aryQuery[] = "  sd.lngslipno = " . $lngPkNo;
        $aryQuery[] = "  AND sd.lngrevisionno = " . $lngRevisionNo;
        $aryQuery[] = " ORDER BY";
        $aryQuery[] = "  sd.lngSlipDetailNo ASC";
    }
    // クエリを平易な文字列に変換
    $strQuery = implode("\n", $aryQuery);

    list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);
    // 検索件数がありの場合
    if ($lngResultNum > 0) {
        // 指定数以内であれば通常処理
        for ($i = 0; $i < $lngResultNum; $i++) {
            $detailData = pg_fetch_all($lngResultID);
        }
    }
    $objDB->freeResult($lngResultID);

    return $detailData;
}

function fncSetHeadDataToTr($doc, $trBody, $bgcolor, $aryTableHeaderName, $displayColumns, $record, $rowspan, $toUTF8Flag)
{
    // TODO 要リファクタリング
    // 指定されたテーブル項目のセルを作成する
    foreach ($aryTableHeaderName as $key => $value) {
        $textContent = '';
        // 表示対象のカラムの場合
        if ($displayColumns == null or array_key_exists($key, $displayColumns)) {
            $textContent = fncSetTextContent($record, $key, $toUTF8Flag);
            // if ($toUTF8Flag) {
            //     $textContent = toUTF8($textContent);
            // }
            $td = $doc->createElement("td", $textContent);
            $td->setAttribute("style", $bgcolor);
            $td->setAttribute("rowspan", $rowspan);
            $trBody->appendChild($td);
        }
    }
    return $trBody;
}
/**
 * 明細行データの生成
 *
 * @param [type] $doc
 * @param [type] $trBody
 * @param [type] $bgcolor
 * @param [type] $aryTableDetailHeaderName
 * @param [type] $displayColumns
 * @param [type] $detailData
 * @return void
 */
function fncSetDetailDataToTr($doc, $trBody, $bgcolor, $aryTableDetailHeaderName, $displayColumns, $detailData, $toUTF8Flag)
{
    // 指定されたテーブル項目のセルを作成する
    foreach ($aryTableDetailHeaderName as $key => $value) {
        // 表示対象のカラムの場合
        if ($displayColumns == null or array_key_exists($key, $displayColumns)) {
            $textContent = fncSetTextContent($detailData, $key, $toUTF8Flag);
            // if ($toUTF8Flag) {
            //     $textContent = toUTF8($textContent);
            // }
            $td = $doc->createElement("td", $textContent);
            $td->setAttribute("style", $bgcolor);
            $trBody->appendChild($td);
        }

    }
    return $trBody;
}

function fncSetHeadBtnToTr($doc, $trBody, $bgcolor, $aryTableHeadBtnName, $displayColumns, $record, $rowspan, $aryAuthority, $isMaxData, $isadmin, $index, $type, $maxdetailno)
{
    // 項番
    $td = $doc->createElement("td", $index);
    $td->setAttribute("style", $bgcolor);
    $td->setAttribute("rowspan", $rowspan);
    $trBody->appendChild($td);

    // TODO 要リファクタリング
    // 指定されたテーブル項目のセルを作成する
    foreach ($aryTableHeadBtnName as $key => $value) {
        // 表示対象のカラムの場合
        if ($displayColumns == null or array_key_exists($key, $displayColumns)) {
            // 項目別に表示テキストを設定
            switch ($key) {
                // 詳細
                case "btndetail":
                    $td = $doc->createElement("td");
                    $td->setAttribute("style", $bgcolor . "text-align: center;");
                    $td->setAttribute("rowspan", $rowspan);
                    // 詳細ボタンの表示
                    if ($aryAuthority[$key] && $record["lngrevisionno"] >= 0) {
                        // 詳細ボタン
                        $imgDetail = $doc->createElement("img");
                        $imgDetail->setAttribute("src", "/img/type01/pc/detail_off_bt.gif");
                        $imgDetail->setAttribute("id", $record["lngpkno"]);
                        $imgDetail->setAttribute("revisionno", $record["lngrevisionno"]);
                        $imgDetail->setAttribute("class", "detail button");
                        // td > img
                        $td->appendChild($imgDetail);
                    }
                    // tr > td
                    $trBody->appendChild($td);
                    break;
                // 修正・履歴・確定・無効・削除・確定取消
                case "btnfix":
                    $td = $doc->createElement("td");
                    $td->setAttribute("style", $bgcolor . "text-align: center;");
                    $td->setAttribute("rowspan", $rowspan);
                    // 修正ボタンの表示

                    if ($type == 'slip') {
                        if (!$isadmin and $isMaxData and $aryAuthority[$key] && $record["lngrevisionno"] >= 0 && $bgcolor != "background-color: #B3E0FF;") {
                            // 修正ボタン
                            $imgFix = $doc->createElement("img");
                            $imgFix->setAttribute("src", "/img/type01/pc/renew_off_bt.gif");
                            $imgFix->setAttribute("lngslipno", $record["lngslipno"]);
                            $imgFix->setAttribute("revisionno", $record["lngrevisionno"]);
                            $imgFix->setAttribute("strslipcode", $record["strslipcode"]);
                            $imgFix->setAttribute("lngsalesno", $record["lngsalesno"]);
                            $imgFix->setAttribute("strsalescode", $record["strsalescode"]);
                            $imgFix->setAttribute("strcustomercode", $record["strcustomerdisplaycode"]);
                            $imgFix->setAttribute("class", "renew button");
                            // td > img
                            $td->appendChild($imgFix);
                        }
                    } else if ($type == 'pc') {
                        // 修正ボタンの表示
                        if (!$isadmin and $isMaxData and $aryAuthority[$key] && $record["lngrevisionno"] >= 0 && $record["lngstockstatuscode"] != DEF_STOCK_CLOSED && $bgcolor != "background-color: #B3E0FF;") {
                            // 修正ボタン
                            $imgFix = $doc->createElement("img");
                            $imgFix->setAttribute("src", "/img/type01/pc/renew_off_bt.gif");
                            $imgFix->setAttribute("id", $record["lngstockno"]);
                            $imgFix->setAttribute("revisionno", $record["lngrevisionno"]);
                            $imgFix->setAttribute("class", "fix button");
                            // td > img
                            $td->appendChild($imgFix);
                        }
                    } else if ($type == 'inv') {
                        // 修正ボタンの表示
                        if (!$isadmin and $isMaxData and $aryAuthority[$key] && $record["lngrevisionno"] >= 0 && $bgcolor != "background-color: #B3E0FF;") {
                            // 修正ボタン
                            $imgFix = $doc->createElement("img");
                            $imgFix->setAttribute("src", "/img/type01/pc/renew_off_bt.gif");
                            $imgFix->setAttribute("id", $record["lnginvoiceno"]);
                            $imgFix->setAttribute("revisionno", $record["lngrevisionno"]);
                            $imgFix->setAttribute("class", "fix button");
                            // td > img
                            $td->appendChild($imgFix);
                        }

                    } else if ($type == 'purchaseorder') {
                        // 修正ボタンの表示
                        if (!$isadmin and $isMaxData and $aryAuthority[$key] && $record["lngrevisionno"] >= 0 && $bgcolor != "background-color: #B3E0FF;") {
                            // 修正ボタン
                            $imgFix = $doc->createElement("img");
                            $imgFix->setAttribute("src", "/img/type01/pc/renew_off_bt.gif");
                            $imgFix->setAttribute("id", $record["lngpurchaseorderno"]);
                            $imgFix->setAttribute("revisionno", $record["lngrevisionno"]);
                            $imgFix->setAttribute("class", "fix button");
                            // td > img
                            $td->appendChild($imgFix);
                        }
                    }
                    // tr > td
                    $trBody->appendChild($td);
                    break;
                case "btnhistory":
                    // 履歴セル
                    $td = $doc->createElement("td");
                    $td->setAttribute("style", $bgcolor . "text-align: center;");
                    $td->setAttribute("rowspan", $rowspan);

                    if ($isMaxData and $record["lngrevisionno"] != 0) {
                        // 履歴ボタン
                        $imgHistory = $doc->createElement("img");
                        $imgHistory->setAttribute("src", "/img/type01/so/renew_off_bt.gif");
                        if ($type == 'so') {
                            $imgHistory->setAttribute("id", $record["strcode"] . "_" . $record["lngdetailno"]);
                        } else if ($type == 'sc') {
                            $imgHistory->setAttribute("id", $record["strsalescode"]);
                        } else if ($type == 'pc') {
                            $imgHistory->setAttribute("id", $record["strstockcode"]);
                        } else if ($type == 'slip') {
                            $imgHistory->setAttribute("id", $record["lngslipno"]);
                        } else if ($type == 'inv') {
                            $imgHistory->setAttribute("id", $record["strinvoicecode"]);
                        } else if ($type == 'po') {
                            $imgHistory->setAttribute("id", $record["strordercode"] . "_" . $record["lngdetailno"]);
                        } else if ($type == 'purchaseorder') {
                            $imgHistory->setAttribute("id", $record["strordercode"]);
                        }
                        $imgHistory->setAttribute("lngrevisionno", $record["lngrevisionno"]);
                        $imgHistory->setAttribute("rownum", $index);
                        $imgHistory->setAttribute("maxdetailno", $maxdetailno);
                        $imgHistory->setAttribute("type", $type);
                        $imgHistory->setAttribute("class", "history button");
                        // td > img
                        $td->appendChild($imgHistory);
                    }
                    // tr > td
                    $trBody->appendChild($td);
                    break;
                case "btndecide":
                    // 確定セル
                    $td = $doc->createElement("td");
                    $td->setAttribute("style", $bgcolor . "text-align: center;");
                    $td->setAttribute("rowspan", $rowspan);
                    $isDecideObj = false;
                    if ($type == 'so') {
                        if ($record["lngreceivestatuscode"] == DEF_RECEIVE_APPLICATE) {
                            $isDecideObj = true;
                        }
                        $id = $record["lngreceiveno"];
                    }
                    if ($type == 'po') {
                        if ($record["lngorderstatuscode"] == DEF_ORDER_APPLICATE) {
                            $isDecideObj = true;
                        }
                        $id = $record["lngorderno"];
                    }

                    // 確定ボタンの表示
                    if (!$isadmin and $isMaxData and $aryAuthority[$key] and $record["lngrevisionno"] >= 0 and $isDecideObj) {
                        // 確定ボタン
                        $imgDecide = $doc->createElement("img");
                        $imgDecide->setAttribute("src", "/img/type01/so/renew_off_bt.gif");
                        $imgDecide->setAttribute("id", $id);
                        $imgDecide->setAttribute("revisionno", $record["lngrevisionno"]);
                        $imgDecide->setAttribute("class", "decide button");
                        // td > img
                        $td->appendChild($imgDecide);
                    }
                    // tr > td
                    $trBody->appendChild($td);
                    break;
            }
        }
    }
    return $trBody;
}

function fncSetBackBtnToTr($doc, $trBody, $bgcolor, $aryTableBackBtnName, $displayColumns, $record, $rowspan, $aryAuthority, $isMaxData, $isadmin, $type)
{
    // TODO 要リファクタリング
    // 指定されたテーブル項目のセルを作成する
    foreach ($aryTableBackBtnName as $key => $value) {
        // 表示対象のカラムの場合
        if ($displayColumns == null or array_key_exists($key, $displayColumns)) {
            // 項目別に表示テキストを設定
            switch ($key) {
                case "btncancel":

                    // 確定取消セル
                    $td = $doc->createElement("td");
                    $td->setAttribute("style", $bgcolor . "text-align: center;");
                    $isDecideObj = false;
                    if ($type == 'so') {
                        if ($record["lngreceivestatuscode"] == DEF_RECEIVE_ORDER) {
                            $isDecideObj = true;
                        }
                        $id = $record["lngreceiveno"];
                    }
                    if ($type == 'po') {
                        if ($record["lngorderstatuscode"] == DEF_ORDER_ORDER) {
                            $isDecideObj = true;
                        }
                        $id = $record["lngorderno"];
                    }
                    // 確定取消ボタンの表示
                    if (!$isadmin and $isMaxData and $aryAuthority[$key] and $record["lngrevisionno"] >= 0 and $isDecideObj) {
                        // 確定取消ボタン
                        $imgCancel = $doc->createElement("img");
                        $imgCancel->setAttribute("src", "/img/type01/so/cancel_off_bt.gif");
                        $imgCancel->setAttribute("id", $id);
                        $imgCancel->setAttribute("revisionno", $record["lngrevisionno"]);
                        $imgCancel->setAttribute("class", "cancel button");
                        // td > img
                        $td->appendChild($imgCancel);
                    }
                    // tr > td
                    $trBody->appendChild($td);
                    break;
                case "btndelete":
                    $td = $doc->createElement("td");
                    $td->setAttribute("style", $bgcolor . "text-align: center;");
                    $td->setAttribute("rowspan", $rowspan);

                    // 削除ボタンの表示
                    if ($type == 'pc' and !$isadmin and $isMaxData and $aryAuthority[$key] and $record["lngstockstatuscode"] != DEF_STOCK_CLOSED and $bgcolor != "background-color: #B3E0FF;") {
                        // 削除ボタン
                        $imgDelete = $doc->createElement("img");
                        $imgDelete->setAttribute("src", "/img/type01/pc/delete_off_bt.gif");
                        $imgDelete->setAttribute("id", $record["lngstockno"]);
                        $imgDelete->setAttribute("revisionno", $record["lngrevisionno"]);
                        $imgDelete->setAttribute("class", "delete button");
                        // td > img
                        $td->appendChild($imgDelete);
                    } else if ($type == 'inv' and !$isadmin and $isMaxData and $bgcolor != "background-color: #B3E0FF;") {
                        // 削除ボタン
                        $imgDelete = $doc->createElement("img");
                        $imgDelete->setAttribute("src", "/img/type01/pc/delete_off_bt.gif");
                        $imgDelete->setAttribute("lnginvoiceno", $record["lnginvoiceno"]);
                        $imgDelete->setAttribute("revisionno", $record["lngrevisionno"]);
                        $imgDelete->setAttribute("class", "delete button");
                        // td > img
                        $td->appendChild($imgDelete);
                    }
                    // tr > td
                    $trBody->appendChild($td);
                    break;
                case "btninvalid":
                    $td = $doc->createElement("td");
                    $td->setAttribute("style", $bgcolor . "text-align: center;");
                    $td->setAttribute("rowspan", $rowspan);
                    // 無効ボタンの表示
                    if ($type == 'pc' and !$isadmin and $isMaxData and $aryAuthority[$key] && $record["lngstockstatuscode"] != DEF_STOCK_CLOSED) {
                        // 無効ボタン
                        $imgInvalid = $doc->createElement("img");
                        $imgInvalid->setAttribute("src", "/img/type01/pc/invalid_off_bt.gif");
                        $imgInvalid->setAttribute("id", $record["lngstockno"]);
                        $imgInvalid->setAttribute("revisionno", $record["lngrevisionno"]);
                        $imgInvalid->setAttribute("class", "invalid button");
                        // td > img
                        $td->appendChild($imgInvalid);
                    }
                    // tr > td
                    $trBody->appendChild($td);
                    break;
            }
        }
    }
    return $trBody;
}

function fncSetTextContent($record, $key, $toUTF8Flag)
{
    $textContent = '';
    // 項目別に表示テキストを設定
    switch ($key) {
        // 登録日
        case "dtminsertdate":
        // 仕入日
        case "dtmappropriationdate":
        // 売上ＮＯ.
        case "strsalescode":
        // 受注ＮＯ.
        case "strreceivecode":
        // 仕入ＮＯ.
        case "strstockcode":
        // リビジョン番号
        case "lngrevisionno":
        // 納品書ＮＯ.
        case "strslipcode":
        // 請求書ＮＯ.
        case "strinvoicecode":
        // 製品到着日
        case "dtmexpirationdate":
        // 納品日
        case "dtmdeliverydate":
        // 請求日
        case "dtminvoicedate":
        // 製品コード
        case "strproductcode":
        // 顧客受注番号
        case "strcustomerreceivecode":
        case "dtmdeliverydate":
        case "strcustomersalescode":
        case "strgoodscode":
        case "strsalesclassname": // NO.
        case "strmoldno":
        case "strproductenglishname":
            $textContent = $record[$key];
            break;
        case "lngrecordno":
            $textContent = $record["lngdetailno"];
            break;
        // 備考
        case "strnote":
            $textContent = $record["strnote"];
            if ($toUTF8Flag) {
                $textContent = toUTF8($textContent);
            }
            break;
        // [入力者表示コード] 入力者表示名
        case "lnginputusercode":
            if ($record["strinputuserdisplaycode"] != '') {
                $textContent = "[" . $record["strinputuserdisplaycode"] . "]" . " " . $record["strinputuserdisplayname"];
            } else {
                $textContent .= "     ";
            }
            if ($toUTF8Flag) {
                $textContent = toUTF8($textContent);
            }
            break;
        case "lnginsertusercode":
            if ($record["strinsertusercode"] != '') {
                $textContent = "[" . $record["strinsertusercode"] . "]" . " " . $record["strinsertusername"];
            } else {
                $textContent .= "     ";
            }
            if ($toUTF8Flag) {
                $textContent = toUTF8($textContent);
            }
            break;
        // [仕入先表示コード]・[顧客表示コード] 入力者表示名
        case "lngcustomercompanycode":
            if ($record["strcompanydisplaycode"] != '') {
                $textContent = "[" . $record["strcompanydisplaycode"] . "]" . " " . $record["strcompanydisplayname"];
            } else {
                $textContent .= "     ";
            }
            if ($toUTF8Flag) {
                $textContent = toUTF8($textContent);
            }
            break;
        case "lngcustomercode":
            if ($record["strcustomerdisplaycode"] != '') {
                $textContent = "[" . $record["strcustomerdisplaycode"] . "]" . " " . $record["strcustomerdisplayname"];
            } else {
                $textContent .= "     ";
            }
            if ($toUTF8Flag) {
                $textContent = toUTF8($textContent);
            } else {
                $textContent = htmlspecialchars($textContent);
            }
            break;
        // 状態
        case "lngstockstatuscode":
            $textContent = $record["strstockstatusname"];
            if ($toUTF8Flag) {
                $textContent = toUTF8($textContent);
            }
            break;
        case "lngsalesstatuscode":
            $textContent = $record["strsalesstatusname"];
            if ($toUTF8Flag) {
                $textContent = toUTF8($textContent);
            }
            break;
        case "lngorderstatuscode":
            $textContent = $record["strorderstatusname"];
            if ($toUTF8Flag) {
                $textContent = toUTF8($textContent);
            }
            break;
        case "lngreceivestatuscode":
            $textContent = $record["strreceivestatusname"];
            if ($toUTF8Flag) {
                $textContent = toUTF8($textContent);
            }
            break;
        // 支払条件
        case "lngpayconditioncode":
            $textContent = $record["strpayconditionname"];
            if ($toUTF8Flag) {
                $textContent = toUTF8($textContent);
            }
            break;
        // 合計金額
        case "curtotalprice":
            $textContent = $record["curtotalprice"];
            break;
        // 通貨
        case "lngmonetaryunitcode":
            $textContent = $record["strmonetaryunitname"];
            if ($toUTF8Flag) {
                $textContent = toUTF8($textContent);
            }
            break;
        // 先月請求残額
        case "curlastmonthbalance":
            $textContent = toMoneyFormat($record["lngmonetaryunitcode"], $record["strmonetaryunitsign"], $record["curlastmonthbalance"]);
            break;
        // 当月請求金額.
        case "curthismonthamount":
            $textContent = toMoneyFormat($record["lngmonetaryunitcode"], $record["strmonetaryunitsign"], $record["curthismonthamount"]);
            break;
        // 税区分
        case "lngtaxclasscode":
            $textContent = $record["strtaxclassname"];
            if ($toUTF8Flag) {
                $textContent = toUTF8($textContent);
            }
            break;
        // 担当者
        case "lngusercode":
            if ($record["strusercode"] != '') {
                $textContent = "[" . $record["strusercode"] . "]" . " " . $record["strusername"];
            } else {
                $textContent .= "     ";
            }
            if ($toUTF8Flag) {
                $textContent = toUTF8($textContent);
            }
            break;
        // 印刷回数
        case "lngprintcount":
            if (empty($record["lngprintcount"])) {
                $textContent = '0';
            } else {
                $textContent = $record["lngprintcount"];
            }
            break;
        // [営業部署表示コード] 営業部署表示名
        case "lnginchargegroupcode":
            if ($record["strgroupdisplaycode"] != '') {
                $textContent = "[" . $record["strgroupdisplaycode"] . "]" . " " . $record["strgroupdisplayname"];
            } else {
                $textContent = "     ";
            }
            if ($toUTF8Flag) {
                $textContent = toUTF8($textContent);
            }
            break;
        // [開発担当者表示コード] 開発担当者表示名
        case "lnginchargeusercode":
            if ($record["struserdisplaycode"] != '') {
                $textContent = "[" . $record["struserdisplaycode"] . "]" . " " . $record["struserdisplayname"];
            } else {
                $textContent = "     ";
            }
            if ($toUTF8Flag) {
                $textContent = toUTF8($textContent);
            }
            break;
        // 製品マスタ.製品名称(日本語)
        case "strproductname":
            $textContent = $record["strproductname"];
            if ($toUTF8Flag) {
                $textContent = toUTF8($textContent);
            }
            break;
        // 仕入科目
        case "lngstockitemcode":
            $textContent = "[" . $record["lngstockitemcode"] . "]" . " " . $record["strstockitemname"];
            if ($toUTF8Flag) {
                $textContent = toUTF8($textContent);
            }
            break;
        // 仕入部品
        case "lngstocksubjectcode":
            $textContent = "[" . $record["lngstocksubjectcode"] . "]" . " " . $record["strstocksubjectname"];
            if ($toUTF8Flag) {
                $textContent = toUTF8($textContent);
            }
            break;
        // 運搬方法
        case "lngdeliverymethodcode":
            $textContent = toUTF8($record["strdeliverymethodname"]);
            if ($toUTF8Flag) {
                $textContent = toUTF8($textContent);
            }
            break;
        // 単位
        case "lngproductunitcode":
        case "strproductunitname":
            $textContent = $record["strproductunitname"];
            if ($toUTF8Flag) {
                $textContent = toUTF8($textContent);
            }
            break;
        // 単価
        case "curproductprice":
            $textContent = toMoneyFormat($record["lngmonetaryunitcode"], $record["strmonetaryunitsign"], $record["curproductprice"]);
            break;
        // 入数
        case "lngquantity":
            $textContent = $record["lngquantity"];
            break;
        // 数量
        case "lngproductquantity":
            $textContent = $record["lngproductquantity"];
            break;
        // 税抜金額
        case "cursubtotalprice":
            if ($record["cursubtotalprice"] == '') {
                $textContent = toMoneyFormat($record["lngmonetaryunitcode"], $record["strmonetaryunitsign"], "0.00");
            } else {
                $textContent = toMoneyFormat($record["lngmonetaryunitcode"], $record["strmonetaryunitsign"], $record["cursubtotalprice"]);
            }
            break;
        // 消費税額
        case "cursubtotal":
            $textContent = toMoneyFormat($record["lngmonetaryunitcode"], $record["strmonetaryunitsign"], $record["cursubtotal"]);
            break;
        // 明細備考
        case "strdetailnote":
            $textContent = $record["strnote"];
            if ($toUTF8Flag) {
                $textContent = toUTF8($textContent);
            }
            break;
        // [営業部署表示コード] 営業部署表示名
        case "lnginchargegroupcode":
            if ($record["strgroupdisplaycode"] != '') {
                $textContent = "[" . $record["strgroupdisplaycode"] . "]" . " " . $record["strgroupdisplayname"];
            } else {
                $textContent = "     ";
            }
            if ($toUTF8Flag) {
                $textContent = toUTF8($textContent);
            }
            break;
        // [開発担当者表示コード] 開発担当者表示名
        case "lnginchargeusercode":
            if ($record["struserdisplaycode"] != '') {
                $textContent = "[" . $record["struserdisplaycode"] . "]" . " " . $record["struserdisplayname"];
            } else {
                $textContent = "     ";
            }
            if ($toUTF8Flag) {
                $textContent = toUTF8($textContent);
            }
            break;
        // 製品マスタ.製品名称(日本語)
        case "strproductname":
            $textContent = toUTF8($record["strproductname"]);
            if ($toUTF8Flag) {
                $textContent = toUTF8($textContent);
            }
            break;
        // 仕入科目
        case "lngstockitemcode":
            $textContent = "[" . $record["lngstockitemcode"] . "]" . " " . $record["strstockitemname"];
            if ($toUTF8Flag) {
                $textContent = toUTF8($textContent);
            }
            break;
        // 仕入部品
        case "lngstocksubjectcode":
            $textContent = "[" . $record["lngstocksubjectcode"] . "]" . " " . $record["strstocksubjectname"];
            if ($toUTF8Flag) {
                $textContent = toUTF8($textContent);
            }
            break;
        // 運搬方法
        case "lngdeliverymethodcode":
            $textContent = toUTF8($record["strdeliverymethodname"]);
            if ($toUTF8Flag) {
                $textContent = toUTF8($textContent);
            }
            break;
        // 単位
        case "lngproductunitcode":
        case "strproductunitname":
            $textContent = $record["strproductunitname"];
            if ($toUTF8Flag) {
                $textContent = toUTF8($textContent);
            }
            break;
        // 税区分
        case "lngtaxclasscode":
            $textContent = $record["strtaxclassname"];
            if ($toUTF8Flag) {
                $textContent = toUTF8($textContent);
            }
            break;
        // 税率
        case "curtax":
            $textContent = round($record["curtax"] * 100) . '%';
            if ($toUTF8Flag) {
                $textContent = toUTF8($textContent);
            }
            break;
        // 税額
        case "curtaxprice":
            $textContent = toMoneyFormat($record["lngmonetaryunitcode"], $record["strmonetaryunitsign"], $record["curtaxprice"]);
            break;
        // 単価
        case "curproductprice":
            $textContent = toMoneyFormat($record["lngmonetaryunitcode"], $record["strmonetaryunitsign"], $record["curproductprice"]);
            break;
        // 入数
        case "lngquantity":
            $textContent = $record["lngquantity"];
            break;
        // 数量
        case "lngproductquantity":
            $textContent = $record["lngproductquantity"];
            break;
        // 明細備考
        case "strdetailnote":
            $textContent = $record["strnote"];
            break;
        // 納品先
        case "lngdeliveryplacecode":
            if ($record["strdeliveryplacecode"] != '') {
                $textContent = "[" . $record["strdeliveryplacecode"] . "]" . " " . $record["strdeliveryplacename"];
            } else {
                $textContent = "     ";
            }
            if ($toUTF8Flag) {
                $textContent = toUTF8($textContent);
            }
            break;
        // 売上区分
        case "lngsalesclasscode":
            if ($record["lngsalesclasscode"] != '') {
                $textContent = "[" . $record["lngsalesclasscode"] . "]" . " " . $record["strsalesclassname"];
            } else {
                $textContent = "    ";
            }
            if ($toUTF8Flag) {
                $textContent = toUTF8($textContent);
            }
            break;
        default:
            $textContent = $record[$key];
            break;
    }
    return $textContent;
}

function fncGetAryAuthority($type, $objAuth)
{
    $aryAuthority = array();
    if ($type == 'purchaseorder') { // 発注書
        // 詳細ボタンを表示
        $aryAuthority["btndetail"] = fncCheckAuthority(DEF_FUNCTION_PO12, $objAuth);
        // 修正を表示
        $aryAuthority["btnfix"] = fncCheckAuthority(DEF_FUNCTION_PO13, $objAuth);

    } else if ($type == 'po') { // 発注
        // 詳細ボタンを表示
        $aryAuthority["btndetail"] = fncCheckAuthority(DEF_FUNCTION_PO4, $objAuth);
        // 確定ボタンを表示
        $aryAuthority["btndecide"] = fncCheckAuthority(DEF_FUNCTION_PO5, $objAuth);
        // 確定取消カラムを表示
        $aryAuthority["btncancel"] = fncCheckAuthority(DEF_FUNCTION_PO6, $objAuth);
    } else if ($type == 'so') { // 受注
        // 詳細ボタンを表示
        $aryAuthority["btndetail"] = fncCheckAuthority(DEF_FUNCTION_SO3, $objAuth);
        // 確定ボタンを表示
        $aryAuthority["btndecide"] = fncCheckAuthority(DEF_FUNCTION_SO4, $objAuth);
        // 確定取消カラムを表示
        $aryAuthority["btncancel"] = fncCheckAuthority(DEF_FUNCTION_SO5, $objAuth);
    } else if ($type == 'sc') { // 売上
        // 詳細ボタンを表示
        $aryAuthority["btndetail"] = fncCheckAuthority(DEF_FUNCTION_SC11, $objAuth);
    } else if ($type == 'slip') { //納品書
        // 詳細ボタンを表示
        $aryAuthority["btndetail"] = fncCheckAuthority(DEF_FUNCTION_SC4, $objAuth);
        // 修正を表示
        $aryAuthority["btnfix"] = fncCheckAuthority(DEF_FUNCTION_SC5, $objAuth);
        // 削除を表示
        $aryAuthority["btndelete"] = fncCheckAuthority(DEF_FUNCTION_SC6, $objAuth);
    } else if ($type == 'pc') { // 仕入
        // 詳細ボタンを表示
        $aryAuthority["btndetail"] = fncCheckAuthority(DEF_FUNCTION_PC4, $objAuth);
        // 修正を表示
        $aryAuthority["btnfix"] = fncCheckAuthority(DEF_FUNCTION_PC5, $objAuth);
        // 削除を表示
        $aryAuthority["btndelete"] = fncCheckAuthority(DEF_FUNCTION_PC6, $objAuth);
        // 無効カラムを表示
        $aryAuthority["btninvalid"] = fncCheckAuthority(DEF_FUNCTION_PC7, $objAuth);
    } else if ($type == 'inv') {
        // 詳細ボタンを表示
        $aryAuthority["btndetail"] = fncCheckAuthority(DEF_FUNCTION_INV4, $objAuth);
        // 修正を表示
        $aryAuthority["btnfix"] = fncCheckAuthority(DEF_FUNCTION_INV5, $objAuth);
        // 削除を表示
        $aryAuthority["btndelete"] = fncCheckAuthority(DEF_FUNCTION_INV6, $objAuth);
    } else if ($type == 'estimate') {
    }
    return $aryAuthority;

}
function fncSetBgColor($type, $strCode, $isMaxData, $objDB)
{
    $columnvalue = $strCode;
    if ($type == 'po') { // 発注
        $tablename = 'm_order';
        $columnname = 'strordercode';
    } else if ($type == 'so') { // 受注
        $tablename = 'm_receive';
        $columnname = 'strreceivecode';
    } else if ($type == 'sc') { // 売上
        $tablename = 'm_sales';
        $columnname = 'strsalescode';
    } else if ($type == 'pc') { // 仕入
        $tablename = 'm_stock';
        $columnname = 'strstockcode';
    } else if ($type == 'inv') {
        $tablename = 'm_invoice';
        $columnname = 'strinvoicecode';
    }
    if ($type == 'purchaseorder') {
        $aryQuery[] = "SELECT";
        $aryQuery[] = " min(lngrevisionno) lngrevisionno, strordercode";
        $aryQuery[] = "FROM m_purchaseorder";
        $aryQuery[] = "WHERE strordercode ='" . $columnvalue . "' ";
        $aryQuery[] = "group by strordercode";
    } else if ($type == 'estimate') {
        $aryQuery[] = "SELECT";
        $aryQuery[] = " min(lngrevisionno) lngrevisionno, lngestimateno, bytinvalidflag";
        $aryQuery[] = "FROM m_estimate";
        $aryQuery[] = "WHERE lngestimateno = " . $columnvalue . " ";
        $aryQuery[] = "group by lngestimateno, bytinvalidflag";
    } else if ($type == 'slip') {
        $aryQuery[] = "SELECT";
        $aryQuery[] = " min(lngrevisionno) lngrevisionno, lngslipno, bytinvalidflag";
        $aryQuery[] = "FROM m_slip";
        $aryQuery[] = "WHERE lngslipno = " . $columnvalue . " ";
        $aryQuery[] = "group by lngslipno, bytinvalidflag";
    } else {
        $aryQuery[] = "SELECT";
        $aryQuery[] = " min(lngrevisionno) lngrevisionno, bytInvalidFlag, " . $columnname;
        $aryQuery[] = "FROM " . $tablename;
        $aryQuery[] = "WHERE " . $columnname . "='" . $columnvalue . "' ";
        $aryQuery[] = "group by " . $columnname . ", bytInvalidFlag";
    }

    // クエリを平易な文字列に変換
    $strQuery = implode("\n", $aryQuery);

    list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);

    if ($lngResultNum) {
        $resultObj = $objDB->fetchArray($lngResultID, 0);
    }

    $objDB->freeResult($lngResultID);

    if ($resultObj["lngrevisionno"] < 0 || $resultObj["bytInvalidFlag"]) {
        $result = true;
    }

    if ($result) {
        $bgcolor = "background-color: #B3E0FF;";
    } else {
        if ($isMaxData) {
            $bgcolor = "background-color: #FFB2B2;";
        } else {
            $bgcolor = "background-color: #FEEF8B;";
        }
    }

    return $bgcolor;
}

function fncSetTheadData($doc, $trHead, $aryTableHeadBtnName, $aryTableBackBtnName, $aryTableHeadName, $aryTableDetailName, $displayColumns)
{

    // 項番カラム
    $th = $doc->createElement("th");
    // コピーボタン
    $imgCopy = $doc->createElement("img");
    $imgCopy->setAttribute("src", "/img/type01/cmn/seg/copy_off_bt.gif");
    $imgCopy->setAttribute("class", "copy button");
    // 項番カラム > コピーボタン
    $th->appendChild($imgCopy);
    // ヘッダに追加
    $trHead->appendChild($th);

    // TODO 要リファクタリング
    // 指定されたテーブル項目のカラムを作成する
    foreach ($aryTableHeadBtnName as $key => $value) {
        if ($displayColumns == null or array_key_exists($key, $displayColumns)) {
            $th = $doc->createElement("th", $value);
            $trHead->appendChild($th);
        }
    }
    foreach ($aryTableHeadName as $key => $value) {
        if ($displayColumns == null or array_key_exists($key, $displayColumns)) {
            $th = $doc->createElement("th", $value);
            $trHead->appendChild($th);
        }
    }
    if ($aryTableDetailName != null) {
        foreach ($aryTableDetailName as $key => $value) {
            if ($displayColumns == null or array_key_exists($key, $displayColumns)) {
                $th = $doc->createElement("th", $value);
                $trHead->appendChild($th);
            }
        }
    }
    foreach ($aryTableBackBtnName as $key => $value) {
        if ($displayColumns == null or array_key_exists($key, $displayColumns)) {
            $th = $doc->createElement("th", $value);
            $trHead->appendChild($th);
        }
    }

    return $trHead;
}
