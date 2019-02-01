<?php
/**
*       売上管理　関数群
*
*       @package   kuwagata
*       @license   http://www.wiseknot.co.jp/
*       @copyright Copyright &copy; 2003, Wiseknot
*       @author    Hiroki Watanabe <h-watanabe@wiseknot.co.jp>
*       @access    public
*       @version   1.00
*
*       処理概要
*       
*	更新履歴
*
*	2004.03.02	明細行のチェック関数から単価、税抜き金額の 0円 計上、マイナス値計上を認めるように修正
*	2004.03.03	明細行のチェック関数から納期項目の必須からチェックをはずすように修正
*
*/


	
	// -----------------------------------------------------------------
	/**		fncCheckData_sc()関数
	*
	*		submitされたデータをチェックする
	*		POとは項目が微妙に違う
	*
	*		@param Array	$aryData			// submitされた値
	*		@param Object	$objDB				// DB接続オブジェクト
	*		@return	Array	$aryError
	*/
	// -----------------------------------------------------------------
	
	function fncCheckData_sc( $aryData, $strPart, $objDB )
	{
		if($strPart == "header")
		{
			$aryCheck["dtmOrderAppDate"]				= "null:date";					// 計上日
			//$aryCheck["strSalesCode"]					= "null";					// 売上ＮＯ．
			$aryCheck["lngCustomerCode"]				= "null";					// 顧客
			//$aryCheck["lngInChargeGroupCode"]			= "null";					// 部門コード
			//$aryCheck["lngInChargeUserCode"]			= "null";					// 担当者

			//if( $aryData["lngMonetaryUnitCode"] != DEF_MONETARY_CODE_YEN )		//通貨が日本以外
			//{
			//	$aryCheck["lngMonetaryRateCode"]		= "number(0,99)";			// レートタイプ
			//	$aryCheck["curConversionRate"]			= "null";					// 換算レート
			//}
			$aryCheck["strSlipCode"]					= "null";					// 納品書ＮＯ．
		}
		else
		// 明細行は他のパートと同じheaderとセットにしておく。。
		{
			$aryCheck["strProductCode"]					= "null";					// 製品
			$aryCheck["lngConversionClassCode"]			= "null";					// 製品単位計上
			$aryCheck["lngProductUnitCode"]				= "null";					// 荷姿単位
			$aryCheck["lngGoodsQuantity"]				= "null:number(-999999999,999999999)";// 製品数量
			$aryCheck["lngTaxClassCode"]				= "null";					// 税区分
//			$aryCheck["dtmDeliveryDate"]				= "null";					// 納期
			$aryCheck["curTotalPrice"]					= "null:money(-9999999999,9999999999)";					// 税抜金額

		}
		
		// チェック関数呼び出し
		$aryCheckResult = fncAllCheck( $aryData, $aryCheck );
		
		list ( $aryData, $bytErrorFlag ) = getArrayErrorVisibility( $aryData, $aryCheckResult, $objDB );
		
		return array ( $aryData, $bytErrorFlag );
	
	}
	
	
	
	// -----------------------------------------------------------------
	/**		fncDetailHidden_sc()関数
	*
	*		受注登録・修正の明細行をhidden値に変換する
	*
	*		@param Array	$aryData			// 明細行のデータ
	*		@param String	$strMode			// 登録と修正の判定(大文字小文字の違いだけ）登録・戻るは大文字、DBから引く時は小文字
	*		@return	Array	$aryJScript			//
	*/
	// -----------------------------------------------------------------

	function fncDetailHidden_sc( $aryData, $strMode, $objDB, &$lngPlusCnt=0)
	{
		//require_once( LIB_DEBUGFILE );

		if( $strMode == "insert" )
		{
			for ($i = 0; $i < count( $aryData ); $i++ )
			{
				//fncDebug( 'sc_detail.txt', $aryData[$i]["org_lngGoodsQuantity"], __FILE__, __LINE__);

				// 行番号
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[".($i+$lngPlusCnt)."][lngOrderDetailNo]\" value=\"".$aryData[$i]["lngOrderDetailNo"]."\">";
				// 製品
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[".($i+$lngPlusCnt)."][strProductCode]\" value=\"".$aryData[$i]["strProductCode"]."\">";
				// 換算区分コード
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[".($i+$lngPlusCnt)."][lngConversionClassCode]\" value=\"".$aryData[$i]["lngConversionClassCode"]."\">";
				// 製品価格
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[".($i+$lngPlusCnt)."][curProductPrice]\" value=\"".$aryData[$i]["curProductPrice"]."\">";
				// 製品単位コード
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[".($i+$lngPlusCnt)."][lngProductUnitCode]\" value=\"".$aryData[$i]["lngProductUnitCode"]."\">";

				// 製品数量
				if ( $aryData[$i]["lngProductQuantity"] == "" and $aryData[$i]["lngGoodsQuantity"] != "" )
				{
					$aryData[$i]["lngProductQuantity"] = $aryData[$i]["lngGoodsQuantity"];
				}

				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[".($i+$lngPlusCnt)."][lngGoodsQuantity]\" value=\"".$aryData[$i]["lngProductQuantity"]."\">";

				// 元数量
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[".($i+$lngPlusCnt)."][org_lngGoodsQuantity]\" value=\"".$aryData[$i]["org_lngGoodsQuantity"]."\">";

				// 備考
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[".($i+$lngPlusCnt)."][strDetailNote]\" value=\"".fncHTMLSpecialChars($aryData[$i]["strDetailNote"])."\">";
				// 税区分
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[".($i+$lngPlusCnt)."][lngTaxClassCode]\" value=\"".$aryData[$i]["lngTaxClassCode"]."\">";
				// 税率
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[".($i+$lngPlusCnt)."][lngTaxCode]\" value=\"".$aryData[$i]["lngTaxCode"]."\">";
				// 税額
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[".($i+$lngPlusCnt)."][curTaxPrice]\" value=\"".$aryData[$i]["curTaxPrice"]."\">";
				// 税抜金額
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[".($i+$lngPlusCnt)."][curTotalPrice]\" value=\"".$aryData[$i]["curTotalPrice"]."\">";
				
				// pcsとかの単位
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[".($i+$lngPlusCnt)."][lngProductUnitCodeName]\" value=\"".$aryData[$i]["lngProductUnitCodeName"]."\">";
				// 単価リスト（表示用）
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[".($i+$lngPlusCnt)."][lngGoodsPriceCode]\" value=\"".$aryData[$i]["lngGoodsPriceCode"]."\">";
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[".($i+$lngPlusCnt)."][curProductPriceForList]\" value=\"".$aryData[$i]["curproductprice"]."\">";
				
				// 売上区分
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[".($i+$lngPlusCnt)."][lngSalesClassCode]\" value=\"".$aryData[$i]["lngSalesClassCode"]."\">";
				// 売上区分（value + 名称)
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[".($i+$lngPlusCnt)."][lngSalesClassCodeName]\" value=\"".$aryData[$i]["lngSalesClassCodeName"]."\">";
				// 納期
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[".($i+$lngPlusCnt)."][dtmDeliveryDate]\" value=\"".$aryData[$i]["dtmDeliveryDate"]."\">";

				// 受注番号
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[".($i+$lngPlusCnt)."][lngReceiveNo]\" value=\"".$aryData[$i]["lngReceiveNo"]."\">";

				// 明細行番号
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[".($i+$lngPlusCnt)."][lngReceiveDetailNo]\" value=\"".$aryData[$i]["lngReceiveDetailNo"]."\">";

				// 対象
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[".($i+$lngPlusCnt)."][lngChkVal]\" value=\"".$aryData[$i]["lngChkVal"]."\">";
			}
		}
		else
		{
		// 修正時
			for ($i = 0; $i < count( $aryData ); $i++ )
			{
				//$lngconversionclasscode = ($aryData[$i]["lngconversionclasscode"] == 1) ? "gs" : "ps";

				// 単位名称
				$lngProductUnitName = fncGetMasterValue("m_productunit", "lngproductunitcode", "strproductunitname", $aryData[$i]["lngproductunitcode"],'', $objDB );
				// 売上区分
				$lngSalesClassCodeName = fncGetMasterValue("m_salesclass", "lngsalesclasscode", "strsalesclassname", $aryData[$i]["lngsalesclasscode"], '', $objDB );

				$lngConversionClassCode = ( $aryData[$i]["lngconversionclasscode"] == 1 ) ? "gs" : "ps";

				// 行番号
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[".($i+$lngPlusCnt)."][lngOrderDetailNo]\" value=\"".$aryData[$i]["lngorderdetailno"]."\">";
				// 製品
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[".($i+$lngPlusCnt)."][strProductCode]\" value=\"".$aryData[$i]["strproductcode"]."\">";
				// 換算区分コード
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[".($i+$lngPlusCnt)."][lngConversionClassCode]\" value=\"$lngConversionClassCode\">";
				// 製品価格
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[".($i+$lngPlusCnt)."][curProductPrice]\" value=\"".$aryData[$i]["curproductprice"]."\">";
				// 製品単位コード
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[".($i+$lngPlusCnt)."][lngProductUnitCode]\" value=\"".$aryData[$i]["lngproductunitcode"]."\">";

				// 製品数量
				if ( $aryData[$i]["lngproductquantity"] == "" and $aryData[$i]["lnggoodsquantity"] != "" )
				{
					$aryData[$i]["lngproductquantity"] = $aryData[$i]["lnggoodsquantity"];
				}

				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[".($i+$lngPlusCnt)."][lngGoodsQuantity]\" value=\"".$aryData[$i]["lngproductquantity"]."\">";

				// 元数量
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[".($i+$lngPlusCnt)."][org_lngGoodsQuantity]\" value=\"".$aryData[$i]["lngproductquantity"]."\">";

				// 備考
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[".($i+$lngPlusCnt)."][strDetailNote]\" value=\"".fncHTMLSpecialChars($aryData[$i]["strdetailnote"])."\">";
				// 税区分
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[".($i+$lngPlusCnt)."][lngTaxClassCode]\" value=\"".$aryData[$i]["lngtaxclasscode"]."\">";
				// 税率
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[".($i+$lngPlusCnt)."][lngTaxCode]\" value=\"".$aryData[$i]["lngtaxcode"]."\">";
				// 税額
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[".($i+$lngPlusCnt)."][curTaxPrice]\" value=\"".$aryData[$i]["curtaxprice"]."\">";
				// 税抜金額
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[".($i+$lngPlusCnt)."][curTotalPrice]\" value=\"".$aryData[$i]["cursubtotalprice"]."\">";
				
				// pcsとかの単位
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[".($i+$lngPlusCnt)."][lngProductUnitCodeName]\" value=\"$lngProductUnitName\">";
				// 単価リスト（表示用）
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[".($i+$lngPlusCnt)."][lngGoodsPriceCode]\" value=\"".$aryData[$i]["lngproductunitcode"]."\">";
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[".($i+$lngPlusCnt)."][curProductPriceForList]\" value=\"\">";
				
				// 売上区分
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[".($i+$lngPlusCnt)."][lngSalesClassCode]\" value=\"".$aryData[$i]["lngsalesclasscode"]."\">";
				// 売上区分（value + 名称)
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[".($i+$lngPlusCnt)."][lngSalesClassCodeName]\" value=\"$lngSalesClassCodeName\">";
				// 納期
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[".($i+$lngPlusCnt)."][dtmDeliveryDate]\" value=\"".$aryData[$i]["dtmdeliverydate"]."\">";

				// 受注番号
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[".($i+$lngPlusCnt)."][lngReceiveNo]\" value=\"".$aryData[$i]["lngreceiveno"]."\">";

				// 明細行番号
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[".($i+$lngPlusCnt)."][lngReceiveDetailNo]\" value=\"".$aryData[$i]["lngreceivedetailno"]."\">";

				// 対象
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[".($i+$lngPlusCnt)."][lngChkVal]\" value=\"".$aryData[$i]["lngchkval"]."\">";
			}
		
		}

		// カウンタ引継ぎ用
		$lngPlusCnt = $i + $lngPlusCnt;
	
		$strDetailHidden = implode("\n", $aryDetailHidden);
		//echo htmlspecialchars( $strDetailHidden );
		
		return $strDetailHidden;
	}





	// -----------------------------------------------------------------
	/**		fncOutPut_sc()関数
	*
	*		fncChangeNumberで計算された値は全て「製品単価」で出力されている。
	*		この関数では最後にインサートされた単位を調べ、その単位にあわせて出力する
	*
	*
	*		lngstockdetailno		// 行番号のカラム名
	*		lngproductquantity		// 数量のカラム名
	*		lngconversionclasscode	// 製品単価（1）:: 荷姿（2）
	*
	*		@param Array	$aryDataA				// fncChangeNumberで変換された値
	*		@param Array	$aryDataB				// サイトにインサートされたでーた（行番号：単価だけ）
	*		@return	Array	$aryOutPut				// 
	*/
	// -----------------------------------------------------------------

	function fncOutPut_sc( $aryDataA, $aryDataB, $objDB )
	{

		for( $i = 0; $i < count( $aryDataA ); $i++ )
		{
			for( $j = 0; $j < count( $aryDataB ); $j++ )
			{
			
				if( $aryDataA[$i]["lngorderdetailno"] == $aryDataB[$j]["lngstockdetailno"] )	// 行番号が等しければ
				{
					//echo "行：$i ".$aryDataA[$i]["lngconversionclasscode"] ." != ". $aryDataB[$i]["lngconversionclasscode"]."<br>";

					if( $aryDataA[$i]["lngconversionclasscode"] != $aryDataB[$i]["lngconversionclasscode"] )
					{
						//割り算
						$lngCarton = fncGetMasterValue( "m_product", "strproductcode", "lngcartonquantity", $aryDataB[$j]["strproductcode"].":str", '',$objDB );

						//echo "carton : $lngCarton<br>";

						// 荷姿数量  製品数量÷カートン入数
						$lngCartonQuanty = $aryDataA[$i]["lngproductquantity"] / $lngCarton;

						//echo "荷姿数量 : $lngCartonQuanty<br>"; 

						// 荷姿単価  製品単価×カートン入数
						$curProductPrice = $aryDataA[$i]["curproductprice"] * $lngCarton;

						//echo "荷姿単価 : $curProductPrice<br>";


						$aryOutPut[$i]["lngorderdetailno"]			= $aryDataA[$i]["lngorderdetailno"];				// 行番号
						$aryOutPut[$i]["strproductcode"]			= $aryDataA[$i]["strproductcode"];					// 製品番号
						$aryOutPut[$i]["lngrevisionno"]				= $aryDataA[$i]["lngrevisionno"];					// 
						$aryOutPut[$i]["lngsalesclasscode"]			= $aryDataA[$i]["lngsalesclasscode"];				// 売上区分
						$aryOutPut[$i]["dtmdeliverydate"]			= $aryDataA[$i]["dtmdeliverydate"];					// 納期
						$aryOutPut[$i]["lngconversionclasscode"]	= $aryDataB[$j]["lngconversionclasscode"];			// 換算区分コード
						$aryOutPut[$i]["curproductprice"]			= $curProductPrice;									// 製品価格
						$aryOutPut[$i]["lnggoodsquantity"]			= $lngCartonQuanty;									// 製品数量
						$aryOutPut[$i]["lngproductunitcode"]		= 2;												// 製品単位コード
						$aryOutPut[$i]["lngtaxclasscode"]			= $aryDataA[$i]["lngtaxclasscode"];					
						$aryOutPut[$i]["lngtaxcode"]				= $aryDataA[$i]["lngtaxcode"];						
						$aryOutPut[$i]["curtaxprice"]				= $aryDataA[$i]["curtaxprice"];						
						$aryOutPut[$i]["cursubtotalprice"]			= $aryDataA[$i]["cursubtotalprice"];				// 税抜金額
						$aryOutPut[$i]["strdetailnote"]				= $aryDataA[$i]["strdetailnote"];					// 備考

						$flg = "true";
					}
				}
			}

			if($flg == "")
			{
				$aryOutPut[$i] = $aryDataA[$i];
			}

			$flg = "";
		}

		return $aryOutPut;
	}

?>