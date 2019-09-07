<?

// ----------------------------------------------------------------------------
/**
*       発注管理  検索結果・詳細・削除・無効化用カラム
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
*         ・検索結果・詳細・削除・無効化用カラム定義
*
*       更新履歴
*
*/
// ----------------------------------------------------------------------------



// 検索用表示タイトル

// 日本語コード
$arySearchTableTytle["btnDetail"]				= "詳細";
$arySearchTableTytle["btnFix"]					= "確定";
$arySearchTableTytle["Record"]					= "履歴";
$arySearchTableTytle["Resale"]					= "再販";
$arySearchTableTytle["btnAdmin"]                = "削除済";
$arySearchTableTytle["dtmInsertDate"]			= "登録日";
$arySearchTableTytle["dtmOrderAppDate"]			= "計上日";
$arySearchTableTytle["strOrderCode"]			= "発注ＮＯ.";
$arySearchTableTytle["lngInputUserCode"]		= "入力者";
$arySearchTableTytle["lngCustomerCode"]			= "仕入先";
$arySearchTableTytle["lngInChargeGroupCode"]	= "部門";
$arySearchTableTytle["lngInChargeUserCode"]		= "担当者";
$arySearchTableTytle["lngDeliveryPlaceCode"]	= "納品場所";
$arySearchTableTytle["lngMonetaryUnitCode"]		= "通貨";
$arySearchTableTytle["lngMonetaryRateCode"]		= "レートタイプ";
$arySearchTableTytle["curConversionRate"]		= "換算レート";
$arySearchTableTytle["lngOrderStatusCode"]		= "状態";
$arySearchTableTytle["lngWorkflowStatusCode"]	= "ワークフロー状態";
$arySearchTableTytle["lngPayConditionCode"]		= "支払条件";
$arySearchTableTytle["dtmExpirationDate"]		= "発注有効期限日";
$arySearchTableTytle["strNote"]					= "備考";
$arySearchTableTytle["curTotalPrice"]			= "合計金額";
$arySearchTableTytle["lngRecordNo"]				= "明細行番号";
$arySearchTableTytle["strProductCode"]			= "製品コード";
$arySearchTableTytle["strProductName"]			= "製品名";
$arySearchTableTytle["strProductEnglishName"]	= "製品名称（英語）";
$arySearchTableTytle["lngStockSubjectCode"]		= "仕入科目";
$arySearchTableTytle["lngStockItemCode"]		= "仕入部品";
$arySearchTableTytle["strMoldNo"]				= "Ｎｏ．";
$arySearchTableTytle["strGoodsCode"]			= "顧客品番";
$arySearchTableTytle["lngDeliveryMethodCode"]	= "運搬方法";
$arySearchTableTytle["dtmDeliveryDate"]			= "納期";
$arySearchTableTytle["curProductPrice"]			= "単価";
$arySearchTableTytle["lngProductUnitCode"]		= "単位";
$arySearchTableTytle["lngProductQuantity"]		= "数量";
$arySearchTableTytle["curSubTotalPrice"]		= "税抜金額";
$arySearchTableTytle["strDetailNote"]			= "明細備考";
// $arySearchTableTytle["btnDelete"]				= "削除";
$arySearchTableTytle["btnDelete"]				= "確定取消";
$arySearchTableTytle["btnInvalid"]				= "無効";
$arySearchTableTytle["lngRevisionNo"]			= "リビジョン番号";
$arySearchTableTytle["lngWorkFlowStatusCode"]	= "ワークフロー状態";

// 英語コード
$arySearchTableTytleEng["btnDetail"]				= "Detail";
$arySearchTableTytleEng["dtmInsertDate"]			= "Regist date";
$arySearchTableTytleEng["dtmOrderAppDate"]			= "Date";
$arySearchTableTytleEng["strOrderCode"]				= "P order No.";
$arySearchTableTytleEng["lngInputUserCode"]			= "Input person";
$arySearchTableTytleEng["lngCustomerCode"]			= "Vendor";
$arySearchTableTytleEng["lngInChargeGroupCode"]		= "Dept";
$arySearchTableTytleEng["lngInChargeUserCode"]		= "In charge name";
$arySearchTableTytleEng["lngDeliveryPlaceCode"]		= "Location";
$arySearchTableTytleEng["lngMonetaryUnitCode"]		= "Currency";
$arySearchTableTytleEng["lngMonetaryRateCode"]		= "Rate type";
$arySearchTableTytleEng["curConversionRate"]		= "Rate";
$arySearchTableTytleEng["lngOrderStatusCode"]		= "Status";
$arySearchTableTytleEng["lngWorkflowStatusCode"]	= "WorkFlowStatus";
$arySearchTableTytleEng["lngPayConditionCode"]		= "Pay condition";
$arySearchTableTytleEng["dtmExpirationDate"]		= "PO limit date";
$arySearchTableTytleEng["strNote"]					= "Remark";
$arySearchTableTytleEng["curTotalPrice"]			= "Total";
$arySearchTableTytleEng["lngRecordNo"]				= "Record No.";
$arySearchTableTytleEng["strProductCode"]			= "Products code";
$arySearchTableTytleEng["strProductName"]			= "Products name(ja)";
$arySearchTableTytleEng["strProductEnglishName"]	= "Products name(en)";
$arySearchTableTytleEng["lngStockSubjectCode"]		= "Goods set";
$arySearchTableTytleEng["lngStockItemCode"]			= "Goods parts";
$arySearchTableTytleEng["strMoldNo"]				= "No.";
$arySearchTableTytleEng["strGoodsCode"]				= "Goods code(Corresp)";
$arySearchTableTytleEng["lngDeliveryMethodCode"]	= "Means of transport";
$arySearchTableTytleEng["dtmDeliveryDate"]			= "Delivery date";
$arySearchTableTytleEng["curProductPrice"]			= "Price";
$arySearchTableTytleEng["lngProductUnitCode"]		= "Unit";
$arySearchTableTytleEng["lngProductQuantity"]		= "Qty";
$arySearchTableTytleEng["curSubTotalPrice"]			= "Amt Bfr tax";
$arySearchTableTytleEng["strDetailNote"]			= "Remark";
$arySearchTableTytleEng["btnFix"]					= "Fix";
// $arySearchTableTytleEng["btnDelete"]				= "Delete";
$arySearchTableTytleEng["btnDelete"]				= "Cancel";
$arySearchTableTytleEng["btnInvalid"]				= "Invalid";
$arySearchTableTytleEng["lngRevisionNo"]			= "Revision No.";
$arySearchTableTytleEng["lngWorkFlowStatusCode"]	= "Work flow status";


// 日本語コード
$aryTableTytle["btnDetail"]				= "詳細";
$aryTableTytle["dtmInsertDate"]			= "登録日";
$aryTableTytle["dtmOrderAppDate"]		= "計上日";
$aryTableTytle["strOrderCode"]			= "発注ＮＯ.";
$aryTableTytle["lngInputUserCode"]		= "入力者";
$aryTableTytle["lngCustomerCode"]		= "仕入先";
$aryTableTytle["lngInChargeGroupCode"]	= "部門";
$aryTableTytle["lngInChargeUserCode"]	= "担当者";
$aryTableTytle["lngDeliveryPlaceCode"]	= "納品場所";
$aryTableTytle["lngMonetaryUnitCode"]	= "通貨";
$aryTableTytle["lngMonetaryRateCode"]	= "レートタイプ";
$aryTableTytle["curConversionRate"]		= "換算レート";
$aryTableTytle["lngOrderStatusCode"]	= "状態";
$aryTableTytle["lngWorkFlowStatusCode"]	= "ワークフロー状態";
$aryTableTytle["lngPayConditionCode"]	= "支払条件";
$aryTableTytle["dtmExpirationDate"]		= "発注有効期限日";
$aryTableTytle["strNote"]				= "備考";
$aryTableTytle["curTotalPrice"]			= "合計金額";
$aryTableTytle["lngRecordNo"]			= "明細行番号";
$aryTableTytle["strProductCode"]		= "製品コード・名称";
$aryTableTytle["lngStockSubjectCode"]	= "仕入科目";
$aryTableTytle["lngStockItemCode"]		= "仕入部品";
$aryTableTytle["strMoldNo"]				= "Ｎｏ．";
$aryTableTytle["strGoodsCode"]			= "顧客品番";
$aryTableTytle["lngDeliveryMethodCode"]	= "運搬方法";
$aryTableTytle["dtmDeliveryDate"]		= "納期";
$aryTableTytle["curProductPrice"]		= "単価";
$aryTableTytle["lngProductUnitCode"]	= "単位";
$aryTableTytle["lngProductQuantity"]	= "数量";
$aryTableTytle["curSubTotalPrice"]		= "税抜金額";
$aryTableTytle["strDetailNote"]			= "明細備考";
$aryTableTytle["btnFix"]				= "修正";
$aryTableTytle["btnDelete"]				= "削除";
$aryTableTytle["btnInvalid"]			= "無効";
$aryTableTytle["lngRevisionNo"]			= "リビジョン番号";
$aryTableTytle["lngWorkFlowStatusCode"]	= "ワークフロー状態";

// 英語コード
$aryTableTytleEng["btnDetail"]				= "Detail";
$aryTableTytleEng["dtmInsertDate"]			= "Regist date";
$aryTableTytleEng["dtmOrderAppDate"]		= "Date";
$aryTableTytleEng["strOrderCode"]			= "P order No.";
$aryTableTytleEng["lngInputUserCode"]		= "Input person";
$aryTableTytleEng["lngCustomerCode"]		= "Vendor";
$aryTableTytleEng["lngInChargeGroupCode"]	= "Dept";
$aryTableTytleEng["lngInChargeUserCode"]	= "In charge name";
$aryTableTytleEng["lngDeliveryPlaceCode"]	= "Location";
$aryTableTytleEng["lngMonetaryUnitCode"]	= "Currency";
$aryTableTytleEng["lngMonetaryRateCode"]	= "Rate type";
$aryTableTytleEng["curConversionRate"]		= "Rate";
$aryTableTytleEng["lngOrderStatusCode"]		= "Status";
$aryTableTytleEng["lngWorkFlowStatusCode"]	= "WorkFlowStatus";
$aryTableTytleEng["lngPayConditionCode"]	= "Pay condition";
$aryTableTytleEng["dtmExpirationDate"]		= "PO limit date";
$aryTableTytleEng["strNote"]				= "Remark";
$aryTableTytleEng["curTotalPrice"]			= "Total";
$aryTableTytleEng["lngRecordNo"]			= "Record No.";
$aryTableTytleEng["strProductCode"]			= "Products code/name";
$aryTableTytleEng["lngStockSubjectCode"]	= "Goods set";
$aryTableTytleEng["lngStockItemCode"]		= "Goods parts";
$aryTableTytleEng["strMoldNo"]				= "No.";
$aryTableTytleEng["strGoodsCode"]			= "Goods code(Corresp)";
$aryTableTytleEng["lngDeliveryMethodCode"]	= "Means of transport";
$aryTableTytleEng["dtmDeliveryDate"]		= "Delivery date";
$aryTableTytleEng["curProductPrice"]		= "Price";
$aryTableTytleEng["lngProductUnitCode"]		= "Unit";
$aryTableTytleEng["lngProductQuantity"]		= "Qty";
$aryTableTytleEng["curSubTotalPrice"]		= "Amt Bfr tax";
$aryTableTytleEng["strDetailNote"]			= "Remark";
$aryTableTytleEng["btnFix"]					= "Fix";
$aryTableTytleEng["btnDelete"]				= "Delete";
$aryTableTytleEng["btnInvalid"]				= "Invalid";
$aryTableTytleEng["lngRevisionNo"]			= "Revision No.";
$aryTableTytleEng["lngWorkFlowStatusCode"]	= "Work flow status";


// 詳細表示用　表示カラム

$aryTableViewHead[] = "dtmInsertDate";
$aryTableViewHead[] = "dtmOrderAppDate";
$aryTableViewHead[] = "strOrderCode";
$aryTableViewHead[] = "lngInputUserCode";
$aryTableViewHead[] = "lngCustomerCode";
$aryTableViewHead[] = "lngInChargeGroupCode";
$aryTableViewHead[] = "lngInChargeUserCode";
$aryTableViewHead[] = "lngDeliveryPlaceCode";
$aryTableViewHead[] = "lngMonetaryUnitCode";
$aryTableViewHead[] = "lngMonetaryRateCode";
$aryTableViewHead[] = "curConversionRate";
$aryTableViewHead[] = "lngOrderStatusCode";
$aryTableViewHead[] = "lngWorkFlowStatusCode";
$aryTableViewHead[] = "lngPayConditionCode";
$aryTableViewHead[] = "dtmExpirationDate";
$aryTableViewHead[] = "strNote";
$aryTableViewHead[] = "curTotalPrice";
$aryTableViewHead[] = "lngWorkflowOrderCode";

$aryTableViewDetail[] = "lngRecordNo";
$aryTableViewDetail[] = "strProductCode";
$aryTableViewDetail[] = "lngStockSubjectCode";
$aryTableViewDetail[] = "lngStockItemCode";
$aryTableViewDetail[] = "strMoldNo";
$aryTableViewDetail[] = "strGoodsCode";
$aryTableViewDetail[] = "lngDeliveryMethodCode";
$aryTableViewDetail[] = "dtmDeliveryDate";
$aryTableViewDetail[] = "curProductPrice";
$aryTableViewDetail[] = "lngProductUnitCode";
$aryTableViewDetail[] = "lngProductQuantity";
$aryTableViewDetail[] = "curSubTotalPrice";
$aryTableViewDetail[] = "strDetailNote";


// 設定カラム名とマスタ内カラム名取得用

$aryTableViewName["dtmInsertDate"] 			= "dtmInsertDate";
$aryTableViewName["dtmOrderAppDate"] 		= "dtmAppropriationDate";
$aryTableViewName["strOrderCode"] 			= "strOrderCode";
$aryTableViewName["lngInputUserCode"] 		= "lngInputUserCode";
$aryTableViewName["lngCustomerCode"] 		= "lngCustomerCompanyCode";
$aryTableViewName["lngInChargeGroupCode"] 	= "lngGroupCode";
$aryTableViewName["lngInChargeUserCode"] 	= "lngUserCode";
$aryTableViewName["lngDeliveryPlaceCode"] 	= "lngDeliveryPlaceCode";
$aryTableViewName["lngMonetaryUnitCode"] 	= "lngMonetaryUnitCode";
$aryTableViewName["lngMonetaryRateCode"] 	= "lngMonetaryRateCode";
$aryTableViewName["curConversionRate"] 		= "curConversionRate";
$aryTableViewName["lngOrderStatusCode"] 	= "lngOrderStatusCode";
$aryTableViewName["lngWorkFlowStatusCode"] 	= "lngWorkFlowStatusCode";
$aryTableViewName["lngPayConditionCode"] 	= "lngPayConditionCode";
$aryTableViewName["dtmExpirationDate"] 		= "dtmExpirationDate";
$aryTableViewName["strNote"] 				= "strNote";
$aryTableViewName["curTotalPrice"] 			= "curTotalPrice";
$aryTableViewName["lngRecordNo"] 			= "lngOrderDetailNo";
$aryTableViewName["strProductCode"] 		= "strProductCode";
$aryTableViewName["strProductName"] 		= "strProductName";
$aryTableViewName["strProductEnglishName"] 	= "strProductEnglishName";
$aryTableViewName["lngStockSubjectCode"] 	= "lngStockSubjectCode";
$aryTableViewName["lngStockItemCode"] 		= "lngStockItemCode";
$aryTableViewName["strMoldNo"] 				= "strMoldNo";
$aryTableViewName["strGoodsCode"] 			= "strGoodsCode";
$aryTableViewName["lngDeliveryMethodCode"] 	= "lngDeliveryMethodCode";
$aryTableViewName["dtmDeliveryDate"] 		= "dtmDeliveryDate";
$aryTableViewName["curProductPrice"] 		= "curProductPrice";
$aryTableViewName["lngProductUnitCode"] 	= "lngProductUnitCode";
$aryTableViewName["lngProductQuantity"] 	= "lngProductQuantity";
$aryTableViewName["curSubTotalPrice"] 		= "curSubTotalPrice";
$aryTableViewName["strDetailNote"] 			= "strDetailNote";


?>