<?php
/**
*       仕入管理　関数群
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
*	2004.03.02	製品到着日の必須チェックをはずす
*	2004.03.12	更新の表示時に税区分が更新対象と違っているバグの修正
*
*/

	// fncDetailHidden_pc	$lngtaxcode = "0.05" 仕様が決まってないので固定値を設定（後で直す）
	
	// -----------------------------------------------------------------
	/**		fncCheckData_pc()関数
	*
	*		submitされたデータをチェックする
	*		POとは項目が微妙に違う
	*
	*		@param Array	$aryData			// submitされた値
	*		@param Object	$objDB				// DB接続オブジェクト
	*		@return	Array	$aryError
	*/
	// -----------------------------------------------------------------
	function fncCheckData_pc( $aryData, $strPart, $objDB )
	{
		// ヘッダー
		if($strPart == "header")
		{
			$aryCheck["dtmOrderAppDate"]		= "null:date";	// 計上日
			$aryCheck["lngStockCode"]			= "";			// 仕入ＮＯ．
			$aryCheck["lngCustomerCode"]		= "null";		// 仕入先
			//$aryCheck["lngInChargeGroupCode"]	= "null";		// 部門コード
			//$aryCheck["lngInChargeUserCode"]	= "null";		// 担当者
			$aryCheck["lngLocationCode"]		= "null";		// 納品場所
			$aryCheck["dtmExpirationDate"] 		= "";			// 製品到着日
			$aryCheck["lngOrderStatusCode"]		= "";			// 状態(オプション値)
			$aryCheck["lngMonetaryUnitCode"]	= "null";		// 通貨
			$aryCheck["strSlipCode"] 			= "null";		// 納品書ＮＯ．


			// 通貨が日本円以外の場合
			if( $aryData["lngMonetaryUnitCode"] != DEF_MONETARY_CODE_YEN )
			{
				//$aryCheck["lngMonetaryRateCode"] = "number(0,99)";	// レートタイプ
				//$aryCheck["curConversionRate"]   = "null";			// 換算レート

				$aryCheck["lngPayConditionCode"] = "number(1,99,The list has not been selected.)";

				// 支払条件
				if( $_COOKIE["lngLanguageCode"] )
				{
					$aryCheck["lngPayConditionCode"] = "number(1,99,リストが選択されていません。)";
				}
			}
		}

		// 明細
		else
		{
			$aryCheck["strProductCode"]			= "null";								// 製品
			$aryCheck["strStockSubjectCode"]	= "number(1,999999999)";				// 仕入科目
			$aryCheck["strStockItemCode"]		= "number(1,999999999)";				// 仕入部品
			$aryCheck["lngConversionClassCode"]	= "null";								// 製品単位計上
			$aryCheck["lngProductUnitCode"]		= "null"; //:money(1,999999999999)";	// 荷姿単位
			$aryCheck["lngGoodsQuantity"]		= "null:number(-999999999,999999999)";	// 製品数量
			$aryCheck["strDetailNote"]			= "";									// 備考
			$aryCheck["lngTaxClassCode"]		= "null";								// 税区分
			$aryCheck["curTotalPrice"]			= "null:money(-9999999999,9999999999)";	// 税抜金額
		}


		// チェック関数呼び出し
		$aryCheckResult  = fncAllCheck( $aryData, $aryCheck );

		list ( $aryData, $bytErrorFlag ) = getArrayErrorVisibility( $aryData, $aryCheckResult, $objDB );

		return array ( $aryData, $bytErrorFlag );
	}





	// -----------------------------------------------------------------
	/**		fncDetailHidden_pc()関数
	*
	*		受注登録・修正の明細行をhidden値に変換する
	*
	*		@param Array	$aryData			// 明細行のデータ
	*		@param String	$strMode			// 登録と修正の判定(大文字小文字の違いだけ）登録・戻るは大文字、DBから引く時は小文字
	*		@return	Array	$aryJScript			//
	*/
	// -----------------------------------------------------------------

	function fncDetailHidden_pc( $aryData, $strMode, $objDB)
	{
		if( $strMode == "insert" )
		{
			for ($i = 0; $i < count( $aryData ); $i++ )
			{
				// 行番号
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][lngOrderDetailNo]\" value=\"".$aryData[$i]["lngOrderDetailNo"]."\">";
				// 製品
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][strProductCode]\" value=\"".$aryData[$i]["strProductCode"]."\">";
				// 仕入科目コード
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][strStockSubjectCode]\" value=\"".$aryData[$i]["strStockSubjectCode"]."\">";
				// 仕入部品コード
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][strStockItemCode]\" value=\"".$aryData[$i]["strStockItemCode"]."\">";
				// 換算区分コード
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][lngConversionClassCode]\" value=\"".$aryData[$i]["lngConversionClassCode"]."\">";
				// 製品価格
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][curProductPrice]\" value=\"".$aryData[$i]["curProductPrice"]."\">";
				// 製品単位コード
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][lngProductUnitCode]\" value=\"".$aryData[$i]["lngProductUnitCode"]."\">";
				// 製品数量
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][lngGoodsQuantity]\" value=\"".$aryData[$i]["lngGoodsQuantity"]."\">";


				// 元数量
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][org_lngGoodsQuantity]\" value=\"".$aryData[$i]["org_lngGoodsQuantity"]."\">";


				// 運搬方法
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][lngCarrierCode]\" value=\"".$aryData[$i]["lngCarrierCode"]."\">";
				// 備考
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][strDetailNote]\" value=\"".fncHTMLSpecialChars($aryData[$i]["strDetailNote"])."\">";
				// 税区分
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][lngTaxClassCode]\" value=\"".$aryData[$i]["lngTaxClassCode"]."\">";
				// 税率
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][lngTaxCode]\" value=\"".$aryData[$i]["lngTaxCode"]."\">";
				// 税額
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][curTaxPrice]\" value=\"".$aryData[$i]["curTaxPrice"]."\">";
				// 税抜金額
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][curTotalPrice]\" value=\"".$aryData[$i]["curTotalPrice"]."\">";
				// 仕入科目の表示用value
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][strStockSubjectCodeName]\" value=\"".$aryData[$i]["strStockSubjectCodeName"]."\">";
				// 仕入部品の表示用value
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][strStockItemCodeName]\" value=\"".$aryData[$i]["strStockItemCodeName"]."\">";
				// pcsとかの単位
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][lngProductUnitCodeName]\" value=\"".$aryData[$i]["lngProductUnitCodeName"]."\">";
				// 
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][lngGoodsPriceCode]\" value=\"".$aryData[$i]["lngGoodsPriceCode"]."\">";
				// 
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][curProductPriceForList]\" value=\"".$aryData[$i]["curProductPriceForList"]."\">";
				// シリアルNO
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][strSerialNo]\" value=\"".$aryData[$i]["strSerialNo"]."\">";
				// 納期
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][dtmDeliveryDate]\" value=\"".$aryData[$i]["dtmDeliveryDate"]."\">";

				// 対象
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][lngChkVal]\" value=\"".$aryData[$i]["lngChkVal"]."\">";
			}
		}
		else
		{
			for ($i = 0; $i < count( $aryData ); $i++ )
			{
				// 
				$lngconversionclasscode = ($aryData[$i]["lngconversionclasscode"] == 1) ? "gs" : "ps";
				// 税区分（表示用）1:無税 2:外税
// 2004.03.12 suzukaze update start
//				$lngtaxclasscode = ( $aryData[$i]["lngstocksubjectcode"] == "402" ||  $aryData[$i]["lngstocksubjectcode"] == 433 ) ? 1 : 2;
// 2004.03.12 suzukaze update end
				
				// 単位名称
				$lngProductUnitName = fncGetMasterValue("m_productunit", "lngproductunitcode", "strproductunitname", $aryData[$i]["lngproductunitcode"],'', $objDB );
				
				// 行番号
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][lngorderdetailno]\" value=\"".$aryData[$i]["lngorderdetailno"]."\">";
				// 製品コード
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][strproductcode]\" value=\"".$aryData[$i]["strproductcode"]."\">";
				// 仕入科目コード
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][strstocksubjectcode]\" value=\"".$aryData[$i]["lngstocksubjectcode"]."\">";
				// 仕入部品コード
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][strstockitemcode]\" value=\"".$aryData[$i]["lngstockitemcode"]."\">";
				// 換算区分コード
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][lngconversionclasscode]\" value=\"$lngconversionclasscode\">";
				// 製品価格
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][curproductprice]\" value=\"".$aryData[$i]["curproductprice"]."\">";
				// 製品単位コード
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][lngproductunitcode]\" value=\"".$aryData[$i]["lngproductunitcode"]."\">";
				// 製品数量
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][lnggoodsquantity]\" value=\"".$aryData[$i]["lngproductquantity"]."\">";


				// 元数量
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][org_lngGoodsQuantity]\" value=\"".$aryData[$i]["lngproductquantity"]."\">";


				// 運搬方法コード
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][lngcarriercode]\" value=\"".$aryData[$i]["lngcarriercode"]."\">";
				// 備考
				if ( $aryData[$i]["strnote"] == "" and $aryData[$i]["strdetailnote"] != "" )
				{
					$aryData[$i]["strnote"] = $aryData[$i]["strdetailnote"];
				}
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][strdetailnote]\" value=\"".fncHTMLSpecialChars($aryData[$i]["strnote"])."\">";
				// 税額
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][curtaxprice]\" value=\"".$aryData[$i]["curtaxprice"]."\">";
				// 税抜金額
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][curtotalprice]\" value=\"".$aryData[$i]["cursubtotalprice"]."\">";
				
				$strStockSubjectName = "";
				$strStockSubjectName = fncGetMasterValue( "m_stocksubject", "lngstocksubjectcode", "strstocksubjectname", $aryData[$i]["lngstocksubjectcode"],'', $objDB );
				
				$strStockItemName = "";
				$strStockItemName = fncGetMasterValue( "m_stockitem", "lngstockitemcode","strstockitemname" , $aryData[$i]["lngstockitemcode"], "lngstocksubjectcode = ".$aryData[$i]["lngstocksubjectcode"],$objDB );
				
				// 仕入科目の表示用value
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][strstocksubjectcodename]\" value=\"".$aryData[$i]["lngstocksubjectcode"]."  $strStockSubjectName\">";
				
				// 仕入部品の表示用value
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][strstockitemcodename]\" value=\"".$aryData[$i]["lngstockitemcode"]."  $strStockItemName\">";
				// pcsとかの単位
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][lngproductunitcodename]\" value=\"$lngProductUnitName\">";
// 2004.03.12 suzukaze update start
				// 税区分（表示用）
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][lngtaxclasscode]\" value=\"" . $aryData[$i]["lngtaxclasscode"] . "\">";
				// 税率
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][lngtaxcode]\" value=\"\">";
				// 単価リスト（表示用）
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][lnggoodspricecode]\" value=\"\">";
				// 
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][curproductpriceforlist]\" value=\"\">";
				// シリアルNO
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][strserialno]\" value=\"".$aryData[$i]["strserialno"]."\">";
				// 納期
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][dtmdeliverydate]\" value=\"".$aryData[$i]["dtmdeliverydate"]."\">";

				// 対象
				$aryDetailHidden[] = "<input type=\"hidden\" name=\"aryPoDitail[$i][lngChkVal]\" value=\"".$aryData[$i]["lngchkval"]."\">";
			}
		}


		$strDetailHidden = implode("\n", $aryDetailHidden);

		return $strDetailHidden;
	}
	


	// -----------------------------------------------------------------
	/**		fncChangeNumber()関数
	*
	*		t_StockDetailとt_OrderDetailで行番号が等しいものに対して
	*		数量が変化しているかを調べる。変更されている場合は「税抜金額」を計算する
	*
	*		@param Array	$aryStock			// t_StockDetailのデータ
	*		@param String	$aryOrder			// t_OrderDetailのデータ
	*		@return	Array	$aryChangeNumber	// 多次元配列で返すfncDetailHiddenが多次元配列用に作っているため
	*/
	// -----------------------------------------------------------------

	function fncChangeNumber(  $aryOrder, $aryStock, $objDB )
	{
/*
for($i=0; $i<count($aryOrder); $i++)
{
	while(list($strKeys, $strValues ) = each($aryOrder[$i]))
	{
		echo "$i : $strKeys ＝＝＝＝＝ $strValues<br>";
	}
	echo "<br><br>";
}
exit();

for($i=0; $i<count($aryStock); $i++)
{
	while(list($strKeys, $strValues ) = each($aryStock[$i]))
	{
		echo "$i : $strKeys +++++ $strValues<br>";
	}
	echo "<br><br>";
}
*/

		$number = 0;
		for ( $h = 0; $h < count( $aryOrder ); $h++ )
		{
			
			$lngProductQuantity = "";
			$curTotalPrice = "";
			
			for( $i = 0; $i < count( $aryOrder[$h] ); $i++ )
			{
				list ( $strKeys, $strValues ) = each( $aryOrder[$h] );
				
				
				
				if( $strKeys == "lngorderdetailno")									// 行番号のカラム
				{
					
					reset($aryStock);
					for( $j = 0; $j < count( $aryStock ); $j++ )
					{
					
						if( $strValues == $aryStock[$j]["lngstockdetailno"])		// 行番号が等しければ
						{
							
							// 単位が等しくなければ・・・
							if( $aryOrder[$h][lngconversionclasscode] != $aryStock[$j][lngconversionclasscode] )
							{
							
								// 製品に対するカートン入り数を取得
								$lngCarton = fncGetMasterValue( "m_product", "strproductcode","lngcartonquantity", $aryOrder[$h]["strproductcode"].":str", '',$objDB );
								// echo "カートン入り数 : $lngCarton<br>";
								// 数量を取得
								// echo "数量".$aryOrder[$h]["lngproductquantity"]."<br>";
								// echo "数量を取得".$aryOrder[$h]["lngproductquantity"] * $lngCarton."<br>";
								
								
								$lngProductQuantity = $aryOrder[$h]["lngproductquantity"] * $lngCarton  - $aryStock[$j]["lngproductquantity"];
								// echo "登録済み個数：".$aryStock[$j]["lngproductquantity"]."<br>";
								// echo "総合数量 : $lngProductQuantity<br>";
								
								// 製品単価を取得  荷姿単価(t_orderdetail)÷カートン入数
								$lngProductPrice = fncGetMasterValue("t_orderdetail", "strproductcode","curproductprice", $aryOrder[$h]["strproductcode"].":str", "lngorderdetailno=$strValues",$objDB );
								
								$curProductPrice = $lngProductPrice / $lngCarton;
								
								
								// 合計値を取得 ： (製品数量×製品単価)
								// echo "製品数量× 製品単価:$lngProductQuantity *** $curProductPrice<br>";
								$curTotalPrice = $lngProductQuantity * $curProductPrice ;
								$curTotalPrice = sprintf("%0.4f", $curTotalPrice);
								
								// echo "合計値を取得 : $curTotalPrice<br>";
								
								//$aryChangeNumber[$number][$strKeys]				= $strValues;						// 行番号
								$ConversionClassFlg = "true";
								
							}
							else
							{
							
								// この関数は売上・仕入れで共通だがWEB表記の名前が違うので↓
								$lngQuantity = "";
								$lngQuantity = ( $aryOrder[$h]["lngproductquantity"] != "" ) ? $aryOrder[$h]["lngproductquantity"] : $aryOrder[$h]["lnggoodsquantity"];
								
								// echo "lngQuantity : $lngQuantity  - ".$aryStock[$j]["lngproductquantity"]."<br>";
								
								$lngProductQuantity = ( $lngQuantity ) - ( $aryStock[$j]["lngproductquantity"]);
								// echo "lngProductQuantity : $lngProductQuantity<br>";
								
								// 合計金額
								$curProductPrice = $aryOrder[$number]["curproductprice"];
								$curTotalPrice = $aryOrder[$number]["curproductprice"] * $lngProductQuantity;
								
								$curTotalPrice = sprintf("%0.4f", $curTotalPrice);
								// echo "合計金額 : $curTotalPrice<br>";
								
								//$aryChangeNumber[$number][$strKeys]				= $strValues;						// 行番号
								
							}
							
							// 発注残高
							$lngZandaka = $aryOrder[$h][cursubtotalprice] - ($aryOrder[$h][cursubtotalprice] - $curTotalPrice);
							
							// echo "個数：$lngProductQuantity<br>";
							// echo "発注残高 : $lngZandaka<br>";
							
							if( $lngProductQuantity <= 0 || $lngZandaka <= 0 )
							{
								// 「0」の場合、仕入れ完了
								$totalflg = "true";
								break;
							}
							else
							{
								//$aryChangeNumber[$number][$strKeys]				= $strValues;						// 行番号
							}
						}

					}
					
					if( $j == count( $aryStock ) )
					{
						$aryChangeNumber[$number][$strKeys] = $strValues;
					}
				}
				else
				{
				
					if( $totalflg != "true" )		// 発注個数から仕入個数を引いた値が「0」でなければ・・
					{
						if( $strKeys == "cursubtotalprice" )					// 合計金額
						{
							$aryChangeNumber[$number]["cursubtotalprice"] = ( $curTotalPrice != "") ? $curTotalPrice : $strValues ; 
						}
						elseif( $strKeys == "lngproductquantity" )				// 製品数量
						{
						
							$aryChangeNumber[$number]["lngproductquantity"] = ( $lngProductQuantity != "" ) ? $lngProductQuantity : $strValues ;
							
							// 製品数量(売上で使用：仕入れと名前が違うので・・）
							$aryChangeNumber[$number]["lnggoodsquantity"] = ( $lngProductQuantity != "" ) ? $lngProductQuantity : $strValues ;
						}
						elseif( $strKeys == "lngconversionclasscode" )			// 製品単位計上：荷姿計上
						{
							$aryChangeNumber[$number]["lngconversionclasscode"] = ( $ConversionClassFlg != "" ) ? 1 : $strValues;
						}
						elseif( $strKeys == "curproductprice")
						{
							$aryChangeNumber[$number]["curproductprice"] = ( $curProductPrice != "" ) ? $curProductPrice : $strValues;
						}
						elseif( $strKeys == "lngproductunitcode")
						{
							$aryChangeNumber[$number]["lngproductunitcode"] = ( $ConversionClassFlg != "" ) ? 1 : $strValues;
						}
						else
						{
							// lngreceivenoは売上で使用（例外）
							if( $strKeys != "lngreceiveno" )
							{
								$aryChangeNumber[$number][$strKeys] = $strValues;
							}
						}
					}
				}
				
			}
			
			if( $totalflg != "true")
			{
				$number++;
			}
			
			$totalflg = "";
			$ConversionClassFlg = "";
			$curTotalPrice = "";
			$lngProductQuantity = "";
			$curProductPrice = "";
		}
		
		$number = 0;
		
		return $aryChangeNumber;

	}

	// -----------------------------------------------------------------
	/**		fncOutPut()関数
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
	*		@param Array	$aryDataB				// インサートされたでーた（行番号：単価だけ）
	*		@return	Array	$aryOutPut				// 
	*/
	// -----------------------------------------------------------------
	
	function fncOutPut( $aryDataA, $aryDataB, $objDB )
	{
	
		for( $i = 0; $i < count( $aryDataA ); $i++ )
		{
			for( $j = 0; $j < count( $aryDataB ); $j++ )
			{
			
				if( $aryDataA[$i]["lngorderdetailno"] == $aryDataB[$j]["lngstockdetailno"] )	// 行番号が等しければ
				{
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
						
						
						
						$aryOutPut[$i]["lngorderdetailno"]			= $aryDataA[$i]["lngorderdetailno"];
						$aryOutPut[$i]["lngrevisionno"]				= $aryDataA[$i]["lngrevisionno"];
						$aryOutPut[$i]["strproductcode"]			= $aryDataA[$i]["strproductcode"];
						$aryOutPut[$i]["lngstocksubjectcode"]		= $aryDataA[$i]["lngstocksubjectcode"];
						$aryOutPut[$i]["lngstockitemcode"]			= $aryDataA[$i]["lngstockitemcode"];
						$aryOutPut[$i]["dtmdeliverydate"]			= $aryDataA[$i]["dtmdeliverydate"];
						$aryOutPut[$i]["lngcarriercode"]			= $aryDataA[$i]["lngcarriercode"];
						$aryOutPut[$i]["lngconversionclasscode"]	= $aryDataB[$j]["lngconversionclasscode"];
						$aryOutPut[$i]["curproductprice"]			= $curProductPrice;
						$aryOutPut[$i]["lngproductquantity"]		= $lngCartonQuanty;
						$aryOutPut[$i]["lngproductunitcode"]		= 2;
						$aryOutPut[$i]["lngtaxclasscode"]			= $aryDataA[$i]["lngtaxclasscode"];
						$aryOutPut[$i]["lngtaxcode"]				= $aryDataA[$i]["lngtaxcode"];
						$aryOutPut[$i]["curtaxprice"]				= $aryDataA[$i]["curtaxprice"];
						$aryOutPut[$i]["cursubtotalprice"]			= $aryDataA[$i]["cursubtotalprice"];
						$aryOutPut[$i]["strnote"]					= $aryDataA[$i]["strnote"];
						
						$aryOutPut[$i]["lnggoodsquantity"]			= $lngCartonQuanty;							// 売上で使用
						$aryOutPut[$i]["lngsalesclasscode"]			= $aryDataA[$i]["lngsalesclasscode"];		// 売上で使用
						
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

	
	
	
	
	
	
	
	// -----------------------------------------------------------------
	/**		fncNewDetail()関数
	*
	*		荷姿単位で登録されたものが途中から製品単価に変更された場合の処理を行う
	*		製品単価：A　荷姿単位：Bとした場合下記のパターンが存在する
	*
	*		    |  A  |  B
	*		----+-----+-----
	*		  A |  1  |  2
	*		  B |  2  |  1
	*
	*		1:そのまま
	*		2:A：Bで同一の行数番号を持っている→製品単価に変換する
	*
	*		lngstockdetailno		// 行番号のカラム名
	*		lngproductquantity		// 数量のカラム名
	*		lngconversionclasscode	// 製品単価（1）:: 荷姿（2）
	*
	*		@param Array	$aryDataA				// 製品単価の明細行
	*		@param Array	$aryDataB				// 荷姿単価の明細行
	*		@return	Array	$aryChangeNumber		// 行番号が等しくお互いの単価が違うものだけを返す(製品単価にして返す）
	*/
	// -----------------------------------------------------------------

	function fncNewDetail(  $aryDataA, $aryDataB ,$objDB )
	{	
	
		// A（製品単位計上）とB（荷姿）の両方の配列に値が入っている場合(BをAに変換する）
		if(is_array($aryDataA) && is_array($aryDataB) )
		{
			// B（荷姿）の行番号を取得
			for( $i = 0; $i < count( $aryDataB ); $i++ )
			{	
				while( list ($strKeys, $strValues ) = each($aryDataB[$i]) )
				{
					if($strKeys == "lngstockdetailno")
					{
						$aryCheckGyou[] = $strValues.":".$i; // 行番号と配列番号を保持（配列番号は合計で使う）
					}

				}
			}
			// print_r( $aryCheckGyou );
		
			// A（製品単位計上）に同じ行番号があるか？？？
			for($i = 0; $i < count( $aryDataA ); $i++ )
			{
				$lnggyou  = $aryDataA[$i][lngstockdetailno];

				reset($aryCheckGyou);
				while( list( $strKeys, $strValues ) = each( $aryCheckGyou ) )
				{
					list( $strKeys2, $strValues2 ) = explode( ":", $strValues );
					
					if( $lnggyou == $strKeys2 )
					{
						$aryNewData[] = $strKeys2.":".$strValues2; // 行番号と配列番号を保持（配列番号は合計で使う）
					}
				}

			}
			
			
			// print_r($aryNewData);
			// A（製品単位計上）とB（荷姿）を結合する（$aryNewDataで得た行数以外の値）
			$aryNewData2 = array_merge($aryDataA, $aryDataB);
			
			if(is_array( $aryNewData ) )
			{
				for( $i = 0; $i < count( $aryNewData2 ); $i++ )
				{
					reset($aryNewData);
					while (list ( $strKeys2, $strValues2 ) = each( $aryNewData ))
					{
						list( $strKeys2, $strValues2 ) = explode(":", $strValues2 );
						
						if( $aryNewData2[$i]["lngstockdetailno"] == $strKeys2 )
						{
							$flg = "true";
						}
					}
					
					if($flg != "true" )
					{
						$aryNewDataA[] =  $aryNewData2[$i];
					}
					$flg = "";
				}
			}

		
		
			// A（製品単位計上）とB（荷姿）に同じ行番号が存在する場合:該当するものだけを配列に格納
			
			if(is_array( $aryNewData ) )
			{
				for( $i = 0; $i < count( $aryDataA ); $i++ )
				{
					reset( $aryNewData );
					while( list ( $strKyes, $strValues ) = each( $aryNewData ) )
					{
					
						list( $strKeys2, $strValues2 ) = explode(":", $strValues );
						// 「荷姿」の単位を「製品」の単価にする
						
						if($aryDataA[$i][lngstockdetailno] == $strKeys2 )
						{
							// 製品に対するカートン入り数を取得
							$lngCarton = fncGetMasterValue( "m_product", "strproductcode","lngcartonquantity" , $aryDataB[$strValues2]["strproductcode"].":str", '',$objDB );
							//echo "変換製品単位：".$aryDataB[$strValues2][lngproductquantity] * $lngCarton."<br>";
							//echo "数量：".$aryDataB[$strValues2][lngproductquantity]."<br>";
							//echo "カートン：$lngCarton<br>";
							$lngQuanty = ( $aryDataB[$strValues2][lngproductquantity] * $lngCarton ) + $aryDataA[$i][lngproductquantity];
							
							$aryNewDataB[$i][lngstockdetailno]			= "$strKeys2";									// 行番号
							$aryNewDataB[$i][lngproductquantity]		= "$lngQuanty";									// 数量（製品単価に変換した値）
							$aryNewDataB[$i][lngconversionclasscode]	= 1;											// 製品単価ににフラグを変更する
							$aryNewDataB[$i][strproductcode]			= $aryDataB[$strValues2]["strproductcode"]; 	// 必要ないかも知れないけど一応他に合わせて
							
						}
					}
				}

				$aryNewData2 = array_merge( $aryNewDataA, $aryNewDataB );
				//print_r($aryNewData);

			}
			// (A=◎ B=◎　同じ行番号が存在しない場合)
			else
			{
				$aryNewData2 = array_merge($aryDataA, $aryDataB);
				
			}
			
		}
		else
		{
			$aryNewData2 = array_merge($aryDataA, $aryDataB);
		}
		
		
		
		return $aryNewData2;
	}

?>
