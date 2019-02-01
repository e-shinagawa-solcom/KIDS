<?php

// ----------------------------------------------------------------------------
/**
*       受注管理  関数ライブラリ
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
*         ・初期登録画面を表示
*         ・入力エラーチェック
*         ・登録ボタン押下後、登録確認画面表示
*
*       更新履歴
*         V1
*         ・2004.03.02  明細行のチェック関数から単価、税抜き金額の 0円 計上、マイナス値計上を認めるように修正
*         ・2004.03.29  fncDetailHidden関数で明細行番号についても渡すように処理
*         V2
*         ・2005.10.14  
*/
// ----------------------------------------------------------------------------



	// ------------------------------------------------------------------------
	/**
	*   fncDetailHidden_pc() 関数
	*
	*   処理概要
	*     ・受注登録・修正の明細行をhidden値に変換する
	*
	*   @param   $aryData     [Array]   明細行のデータ
	*   @param   $strMode     [String]  登録と修正の判定(大文字小文字の違いだけ）登録・戻るは大文字、DBから参照時は小文字
	*   @return  $aryJScript  [Array]
	*/
	// ------------------------------------------------------------------------
	function fncDetailHidden_so( $aryData, $strMode, $objDB)
	{
		//-------------------------------------------------
		// 新規登録時
		//-------------------------------------------------
		if( $strMode == "insert" )
		{
			for($i = 0; $i < count( $aryData ); $i++ )
			{
				// 明細行番号
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][lngOrderDetailNo]\" value=\"".$aryData[$i]["lngOrderDetailNo"]."\">";

				// 製品
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][strProductCode]\" value=\"".$aryData[$i]["strProductCode"]."\">";
				
				//品番チェック
//				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][strGoodsCode]\" value=\"".$aryData[$i]["strGoodsCode"]."\">";

				// 単価リスト
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][lngGoodsPriceCode]\" value=\"".$aryData[$i]["lngGoodsPriceCode"]."\">";

				// 換算区分
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][lngConversionClassCode]\" value=\"".$aryData[$i]["lngConversionClassCode"]."\">";

				// 単価
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][curProductPrice]\" value=\"".$aryData[$i]["curProductPrice"]."\">";

				// 単位
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][lngProductUnitCode]\" value=\"".$aryData[$i]["lngProductUnitCode"]."\">";

				// 単位（名称）
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][lngProductUnitCodeName]\" value=\"".$aryData[$i]["lngProductUnitCodeName"]."\">";

				// 数量
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][lngGoodsQuantity]\" value=\"".$aryData[$i]["lngGoodsQuantity"]."\">";

				// 税抜金額
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][curTotalPrice]\" value=\"".$aryData[$i]["curTotalPrice"]."\">";

				// 備考
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][strDetailNote]\" value=\"".fncHTMLSpecialChars($aryData[$i]["strDetailNote"])."\">";

				// 単価リスト追加データ
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][curProductPriceForList]\" value=\"".$aryData[$i]["curProductPriceForList"]."\">";

				// 売上区分
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][lngSalesClassCode]\" value=\"".$aryData[$i]["lngSalesClassCode"]."\">";

				// 売上区分（value + 名称）
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][lngSalesClassCodeName]\" value=\"".$aryData[$i]["lngSalesClassCodeName"]."\">";

				// 納期
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][dtmDeliveryDate]\" value=\"".$aryData[$i]["dtmDeliveryDate"]."\">";
			}
		}
		//-------------------------------------------------
		// DB参照時
		//-------------------------------------------------
		else
		{
			for ($i = 0; $i < count( $aryData ); $i++ )
			{
				// 明細行番号
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][lngorderdetailno]\" value=\"".$aryData[$i]["lngorderdetailno"]."\">";

				// 単位（名称）
				$strProductUnitCodeName = fncGetMasterValue( "m_productunit", "lngproductunitcode", "strproductunitname", $aryData[$i]["lngproductunitcode"],'', $objDB );
				// 売上区分名
				$strSalesClassCodeName = fncGetMasterValue( "m_salesclass", "lngsalesclasscode", "strsalesclassname", $aryData[$i]["lngsalesclasscode"], '', $objDB );

				// 製品コード
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][strProductCode]\" value=\"".$aryData[$i]["strproductcode"]."\">";

				//品番チェック
//				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][strGoodsCode]\" value=\"".$aryData[$i]["strGoodsCode"]."\">";

				// 単価リスト
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][lngGoodsPriceCode]\" value=\"".$aryData[$i]["lngproductunitcode"]."\">";

				// 換算区分
				$lngconversionclasscode = ( $aryData[$i]["lngconversionclasscode"] == 1) ? "gs" : "ps";
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][lngConversionClassCode]\" value=\"$lngconversionclasscode\">";

				// 単価
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][curProductPrice]\" value=\"".$aryData[$i]["curproductprice"]."\">";

				// 単位
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][lngProductUnitCode]\" value=\"".$aryData[$i]["lngproductunitcode"]."\">";

				// 単位（名称）
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][lngProductUnitCodeName]\" value=\"$strProductUnitCodeName\">";

				// 数量
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][lngGoodsQuantity]\" value=\"".$aryData[$i]["lngproductquantity"]."\">";

				// 税抜金額
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][curTotalPrice]\" value=\"".$aryData[$i]["cursubtotalprice"]."\">";

				// 備考
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][strDetailNote]\" value=\"".fncHTMLSpecialChars($aryData[$i]["strnote"])."\">";

				// 単価リスト追加データ
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][curProductPriceForList]\" value=\"".$aryData[$i]["curProductPriceForList"]."\">";

				// 売上区分
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][lngSalesClassCode]\" value=\"".$aryData[$i]["lngsalesclasscode"]."\">";

				// 売上区分名（value + 名称）
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][lngSalesClassCodeName]\" value=\"$strSalesClassCodeName\">";

				// 納期
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][dtmDeliveryDate]\" value=\"".$aryData[$i]["dtmdeliverydate"]."\">";
			}
		}


		$strDetailHidden = implode( "\n", $aryDetailHidden );

		return $strDetailHidden;
	}





	// ------------------------------------------------------------------------
	/**
	*   fncCheckData_so() 関数
	*
	*   処理概要
	*     ・submitされたデータをチェックする
	*
	*   @param   $aryData     [Array]     submitされた値
	*   @param   $objDB       [Object]    DB接続オブジェクト
	*   @return  $aryJScript  [aryError]
	*/
	// ------------------------------------------------------------------------
	function fncCheckData_so( $aryData, $strPart, $objDB )
	{
		if( $strPart == "header")
		{
			$aryCheck["dtmOrderAppDate"]				= "null:date";			// 計上日
			//$aryCheck["strReceiveCode"]					= "null";				// 受注No
			$aryCheck["lngCustomerCode"]				= "null";				// 顧客
			$aryCheck["lngOrderStatusCode"]				= "";					// 状態(オプション値)
			$aryCheck["lngMonetaryUnitCode"]			= "null";				// 通貨
			$aryCheck["curAllTotalPrice"]				= "null";				// 総合計金額（税抜き）

			if( $aryData["lngMonetaryUnitCode"] != DEF_MONETARY_CODE_YEN )		// 通貨が日本以外
			{
				$aryCheck["lngMonetaryRateCode"]		= "number(0,99)";		// レートタイプ
				$aryCheck["curConversionRate"]			= "null";				// 換算レート
				//$aryCheck["lngPayConditionCode"]		= "number(1,99)";		// 支払条件
			}
		}
		else
		{
			$aryCheck["strProductCode"]					= "null";				// 製品
//39期対応
//			$aryCheck["strGoodsCode"]					= "null";				// 品番
//顧客品番
			$aryCheck["lngSalesClassCode"]				= "number(1,99)";		// 売上区分
			$aryCheck["dtmDeliveryDate"]				= "null";				// 納期
			$aryCheck["lngConversionClassCode"]			= "null";				// 製品単位計上
			$aryCheck["curProductPrice"]				= "null";				// 単価
			$aryCheck["lngProductUnitCode"]				= "null";				// 単位
			$aryCheck["lngGoodsQuantity"]				= "null";				// 数量
			$aryCheck["curTotalPrice"]					= "null:money(0,9999999999)";	// 税抜金額
		}


		// チェック関数呼び出し
		$aryCheckResult = fncAllCheck( $aryData, $aryCheck );

		list( $aryData, $bytErrorFlag ) = getArrayErrorVisibility( $aryData, $aryCheckResult, $objDB );

		return array( $aryData, $bytErrorFlag );
	}

?>