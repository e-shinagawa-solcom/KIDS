<?
/**
* 納品書一覧　検索結果表示用の項目名定義
*
*	@access public
*
*	更新履歴
*	2019.08.19	新規作成
*/

// ------------------------------
//   納品書一覧画面（検索結果）
// ------------------------------
// 日本語コード
$arySearchTableTytle["btnDetail"]				= "詳細";
$arySearchTableTytle["btnFix"]					= "修正";
$arySearchTableTytle["lngCustomerCode"]			= "顧客";
$arySearchTableTytle["lngTaxClassCode"]			= "課税区分";
$arySearchTableTytle["strSlipCode"]				= "納品書ＮＯ.";
$arySearchTableTytle["dtmDeliveryDate"]			= "納品日";
$arySearchTableTytle["lngDeliveryPlaceCode"]	= "納品先";
$arySearchTableTytle["lngInsertUserCode"]		= "起票者";
$arySearchTableTytle["strNote"]					= "備考";
$arySearchTableTytle["curTotalPrice"]			= "合計金額";
$arySearchTableTytle["lngRecordNo"]			    = "明細行ＮＯ.";
$arySearchTableTytle["strCustomerSalesCode"]	= "注文書ＮＯ.";
$arySearchTableTytle["strGoodsCode"]			= "顧客品番";
$arySearchTableTytle["strProductName"]			= "品名";
$arySearchTableTytle["strSalesClassName"]		= "売上区分";
$arySearchTableTytle["curProductPrice"]			= "単価";
$arySearchTableTytle["lngQuantity"]		        = "入数";
$arySearchTableTytle["lngProductQuantity"]		= "数量";
$arySearchTableTytle["strProductUnitName"]		= "単位";
$arySearchTableTytle["curSubTotalPrice"]		= "税抜金額";
$arySearchTableTytle["strDetailNote"]			= "明細備考";
$arySearchTableTytle["btnDelete"]				= "削除";

// 英語コードは対応不要のため削除
//$arySearchTableTytleEng["btnDetail"]                = "Detail";

// ------------------------------
//   納品書詳細画面（詳細表示）
// ------------------------------
// ヘッダ部
$aryHeadColumnNames["lngSlipNo"]			    = "納品伝票番号";
$aryHeadColumnNames["lngRevisionNo"]			= "リビジョン番号";
$aryHeadColumnNames["strSlipCode"]		    	= "KWGNO.";
$aryHeadColumnNames["strCustomer"]	        	= "顧客";
$aryHeadColumnNames["dtmDeliveryDate"]	    	= "納品日";
$aryHeadColumnNames["strDeliveryPlaceName"] 	= "納品場所";
$aryHeadColumnNames["strDeliveryPlaceUserName"]	= "納品場所担当者";
$aryHeadColumnNames["strTaxClassName"]	    	= "課税区分";
$aryHeadColumnNames["strDrafter"]   	    	= "起票者";
$aryHeadColumnNames["curTotalPrice"]			= "税抜金額";   // 本当は「合計金額」が正しいがあえて「税抜金額」にしている
$aryHeadColumnNames["strMonetaryUnitName"]	    = "通貨";
$aryHeadColumnNames["strNote"]				    = "備考";
$aryHeadColumnNames["dtmInsertDate"]		    = "入力日";
$aryHeadColumnNames["strInsertUser"] 		    = "入力者";
$aryHeadColumnNames["lngPrintCount"]	        = "印刷回数";

// 明細部
$aryDetailColumnNames["lngRecordNo"]			= "明細行番号";
$aryDetailColumnNames["strReceiveStatusName"]	= "売上状態";
$aryDetailColumnNames["strCustomerSalesCode"]   = "顧客受注番号";
$aryDetailColumnNames["lngSalesClassCode"]		= "売上区分";
$aryDetailColumnNames["strGoodsCode"]		    = "顧客品番";
$aryDetailColumnNames["strProductCode"]		    = "製品コード・名称";
$aryDetailColumnNames["strProductEnglishName"]	= "名称（英語）";
$aryDetailColumnNames["curProductPrice"]		= "単価";
$aryDetailColumnNames["lngProductQuantity"]	    = "数量";
$aryDetailColumnNames["strProductUnitName"]	    = "単位";
$aryDetailColumnNames["curSubTotalPrice"]		= "税抜金額";
$aryDetailColumnNames["strDetailNote"]			= "明細備考";

// 英語コードは対応不要のため削除
// 英語コード
//$aryTableTytleEng["btnDetail"]				= "Detail";

?>