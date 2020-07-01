<?php

	/*
	//****************************************************************************
	// 02 概算売上
		ヘッダーの生成

	*	@param	object	$worksheet		ワークシートオブジェクト
	*	@param	object	$aryFormat		フォーマットオブジェクト
	*	@param	array	$aryDataset		データセット定義
	*	@param	integer	$j				列カウンター
	*	@param	array	$aryResult		データベース取得値
	*
	*	@return	integer					見出し行数

	//****************************************************************************
	*/
	function fncfncSpreadSheetCellHeader02(&$worksheet, &$objFormat, $aryData)
	{
		$worksheet->writeString( 0, 0, mb_convert_encoding('社内統計データ‐概算売上','shift_jis'), $objFormat );
		$worksheet->writeString( 1, 0, mb_convert_encoding('売上計上日　'.$aryData["dtmAppropriationDateFrom"].'-'.$aryData["dtmAppropriationDateTo"], 'shift_jis'), $objFormat );
		$worksheet->writeString( 2, 0, mb_convert_encoding('選択','shift_jis'), $objFormat );
		$worksheet->writeString( 2, 1, mb_convert_encoding('←A3のセルを選択して　｢Ctr｣｢Shift｣｢*｣同時に押すと　当シートの全データを選択されるようになれます','shift_jis'), $objFormat );
		
		return 3;	// ヘッダーで使用した行数を返却
	}


	/*
	//****************************************************************************
	//	セル用データの生成
	*
	*	@param	array	$aryDataset		データセット定義
	*	@param	integer	$i				行カウンター
	*	@param	integer	$j				列カウンター
	*	@param	array	$aryResult		データベース取得値
	*	@param	integer	$lngHeadLineCnt	ヘッダー部の総合行数（データ部の始まる前までの行）
	*
	*	@return							セル設定用データ
	*
	// ・処理内での $j は xxxxx_dataset.txt のデータ数(0～）と一致している（データセット配列番号）
	// ・$aryDataset[19]->data 等のデータは該当する配列番号が参照時 switch文よりも前に処理されているデータのみ参照可能
	// ・aryResult["*****"] に指定するカラム名は小文字であること
	//
	//****************************************************************************
	*/
	function fncSpreadSheetCellData02($aryDataset, $i, $j, $aryResult, $lngHeadLineCnt)
	{

		$strNo = (string)($i + 1 + $lngHeadLineCnt);	// エクセル上での計算用の行番号を設定
		$curCalc = 0;
		$varCellData = "";
		$strReceiveCode='';
		// 置き換え用売上区分コード
		if(!isset($aryResult["lngsalesclasscode"])) {
			return;
		} else {
			switch( (int)$aryResult["lngsalesclasscode"] )
				{
					case 1:
						$SalesClassCode = 1;
						break;
					case 2:
					case 3:
					case 9:
						$SalesClassCode = 2;
						break;
					default:
						$SalesClassCode = 3;
				}
		}
			
		switch((int)$j)
		{

			case 0:	// 通し番号
				$varCellData = (int)$i+1;
				break;
			case 1:	// 計算用①
				$varCellData = $aryResult["strproductcode"] .$SalesClassCode;
				break;
			case 2:	// 計算用②
				$varCellData = $aryResult["strgroupdisplaycode"] .$SalesClassCode;
				break;
							
			case 3:	// 計上日
				$varCellData = $aryResult["dtmappropriationdate"];
				break;

			case 6:	// 置き換え用売上区分コード
				$varCellData = (float)$SalesClassCode;
				break;

			case 14:	// 単価

				$curCalc = (float)$aryResult["curproductpricesales"];
				$curCalc = round( (float)$curCalc, 2);	// 小数点以下2桁で丸め処理

				// カートンの場合
				if( $aryResult["lngproductunitcode"] == "2" )
				{
					// 単価 / カートン入数
					$curCalc = $curCalc / (int)$aryResult["lngcartonquantity"];
				}

				// 日本円以外の場合
				if( (int)$aryResult["lngmonetaryunitcode"] != 1 )
				{
					$curRate = (float)$aryResult["curconversionrate"];
					
						
					// =IF(S3="",9999, IF(S3=1,1*1, IF(S3=2,2*2)))
					//$curCalc = '=IF(S3=0, '.(float)$aryResult["curproductpricereceive"].' * '. $curRate .', IF(S3=1, ' . (float)$aryResult["curproductpricereceive"] .' * '. (float)$aryResult["curconversionrate1"] . ', IF(S3=2, '. (float)$aryResult["curproductpricereceive"] .' * '. (float)$aryResult["curconversionrate2"] . ', 0)))';
					$curCalc = '=' . $curCalc .' * '. (float)$curRate;
				}

				$varCellData = $curCalc;
				break;
			
			case 15:	// 単位
						//	1:pcs,  2:c/t,  3:set
				if( $aryResult["lngproductunitcode"] == "2" )
				{
					$varCellData = "pcs";
				}
				elseif( $aryResult["lngproductunitcode"] == "1" )
				{
					$varCellData = "pcs";
				}
				else
				{
					$varCellData = "set";
				}
				
				break;

			case 16:	// 数量
			
				$varCellData = (int)$aryResult["lngproductquantity"];
				// c/t の場合は計算する
				if( $aryResult["lngproductunitcode"] == "2" )
				{
					$varCellData = (int)$aryResult["lngproductquantity"] * (int)$aryResult["lngcartonquantity"];
				}
				
				break;

			case 19:	// 売上合計 curproductprice * lngproductquantity
/*				$curCalc = (float)$aryResult["curproductpricesales"] * (int)$aryResult["lngproductquantity"];
				$curCalc = floor($curCalc);	// 小数点以下切り捨て
*/
				$curCalc = (int)$aryResult["cursubtotalprice"];
				
				// 日本円以外の場合
				if( (int)$aryResult["lngmonetaryunitcode"] != 1 )
				{
					$curCalc = '=O'.$strNo.'*'.(int)$aryResult["lngproductquantity"].'';
				}

				$varCellData = $curCalc;
				break;
			
			case 20:	// 製品原価＠		
				// W列とQで逆算する（W列/Q列「計算式で埋める」小数点以下2桁で丸め処理)
				$curCalc = '=ROUND(W' . $strNo . '/Q' . $strNo . ', 2)';
				$varCellData = $curCalc;
				break;

			case 22:	// 製品原価合計
				// Y列の利益率で逆算する（X列の売上合計×(1-利益率)「計算式で埋める」）
				$curCalc = '=ROUND(T' . $strNo . '*(1-Y' . $strNo . '), 0)';
				$varCellData = $curCalc; // 小数点以下桁で丸め処理
				break;
			case 23:	// 目標利益
				$curCalc = '=ROUND(T' . $strNo . '*Y' . $strNo . ', 4)';
				$varCellData = $curCalc;
				break;
				
			case 24:	// 目標利益率				
				// 売上分類が製品売上の場合は見積原価書の製品利益率の値を設定、固定費売上の場合は固定費利益率を設定にする。
				// 製品利益率 = (製品売上高-製造費用)/製品売上高の設定
				if ($aryResult["lngsalesdivisioncode"] == 2) {
					$curSalesProfitRate = $aryResult["cursalesamount"] == 0 ? 0.00 : round((float) (($aryResult["cursalesamount"] - $aryResult["curmanufacturingcost"]) / $aryResult["cursalesamount"]), 4);
					$varCellData = $curSalesProfitRate; // 小数点以下2桁で丸め処理
					break;
				} else if ($aryResult["lngsalesdivisioncode"] == 1) {
					// 固定費利益率 = (売上総利益 - 製品売上高 +  製造費用)/ 固定費売上高
					$curFixedCostSalesProfitRate = $aryResult["curfixedcostsales"] == 0 ? 0 : round((float) (($aryResult["curtotalprice"] - $aryResult["cursalesamount"] + $aryResult["curmanufacturingcost"]) / $aryResult["curfixedcostsales"]), 4);
					$varCellData = $curFixedCostSalesProfitRate; // 小数点以下2桁で丸め処理
					break;
				}
			case 26: //受注備考
				// 受注明細行備考で埋め
				$varCellData = (string) $aryResult["strnote"];
				break;
				
			default:
				//
				// 上記の case に当てはまらないもの（SQL実行文のカラム名から一致するものを取得）
				//
				$strKey = strtolower($aryDataset[$j]->name);
				$varCellData = (string)$aryResult[$strKey];
		}
		
		return $varCellData;

	}


	/*
	//****************************************************************************
	// 02 概算売上
		セルの書式別・フォーマットオブジェクトの生成

	*	@param	object	$workbook		ワークブックオブジェクト
	*	@param	array	$aryFormat		フォーマットオブジェクト格納配列
	*	@param	array	$aryDataset		データセット定義
	*	@param	integer	$j				列カウンター
	*	@param	array	$aryResult		データベース取得値
	*
	*	@return	object	$objFormat		フォーマットオブジェクト

	//****************************************************************************
	*/
	function fncSpreadSheetCellFormat02(&$workbook, &$aryFormat, &$aryDataset, $j, $aryResult)
	{

		unset($objFormat);

		switch( (int)$j )
		{
			case 3:
			case 13:
				$objFormat =& fncSpreadSheetCellFormat($workbook, $aryFormat, 'date');
				break;
			case 14:	// 単価
				// 日本円以外の場合
				if( (int)$aryResult["lngmonetaryunitcode"] != 1 )
				{
					// データタイプの変更（計算式）
					$aryDataset[$j]->type = '';
					$objFormat =& fncSpreadSheetCellFormat($workbook, $aryFormat, 'numberCalcDec');
					break;
				}
				$aryDataset[$j]->type = 'float';
				$objFormat =& fncSpreadSheetCellFormat($workbook, $aryFormat, 'numberDec');
				break;

			case 17:	// 製品CD
			case 18:	//製品名称
				$objFormat =& fncSpreadSheetCellFormat($workbook, $aryFormat, 'textPoint');
				break;
				
			case 19:	// 売上合計
				// 日本円以外の場合
				if( (int)$aryResult["lngmonetaryunitcode"] != 1 )
				{
					// データタイプの変更（計算式）
					$aryDataset[$j]->type = '';
					$objFormat =& fncSpreadSheetCellFormat($workbook, $aryFormat, 'numberCalc');
					break;
				}
				$aryDataset[$j]->type = 'float';
				$objFormat =& fncSpreadSheetCellFormat($workbook, $aryFormat, 'numberCalcPoint');
				break;
				
			// 小数点以下指定のカラム
			case 20:	// 製品原価＠
				$aryDataset[$j]->type = '';
				$objFormat = &fncSpreadSheetCellFormat($workbook, $aryFormat, 'numberCalcDec');
				break;
			case 21:
				$objFormat =& fncSpreadSheetCellFormat($workbook, $aryFormat, 'numberCalcPoint');
				break;
			case 22:	// 製品原価合計
				// データタイプの変更（計算式）
				$aryDataset[$j]->type = '';
				$objFormat = &fncSpreadSheetCellFormat($workbook, $aryFormat, 'numberCalc');
				break;


			case 23:	// 目標利益
				// データタイプの変更（計算式）
				$aryDataset[$j]->type = '';
				$objFormat = &fncSpreadSheetCellFormat($workbook, $aryFormat, 'numberCalc');
				break;

			case 24:	// 目標利益率
				if($aryDataset[24]->data == 0)
				{
					$objFormat =& fncSpreadSheetCellFormat($workbook, $aryFormat, $aryDataset[$j]->type);
					break;
				} else
				{
					$objFormat =& fncSpreadSheetCellFormat($workbook, $aryFormat, 'numberPercent');
					break;
				}

			case 26: //受注備考
				$objFormat = &fncSpreadSheetCellFormat($workbook, $aryFormat, 'textPoint');
				break;

			default:
				$objFormat =& fncSpreadSheetCellFormat($workbook, $aryFormat, $aryDataset[$j]->type);
		}

		return $objFormat;

	}

?>