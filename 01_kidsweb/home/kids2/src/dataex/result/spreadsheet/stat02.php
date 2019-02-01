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
		$worksheet->writeNumber( 2, 14, (int)2, $objFormat );	// 初期値を社内レート
		$worksheet->writeString( 2, 15, mb_convert_encoding('←O3のセル　1:ＴＴＭレート、2:社内レート（色付きの単価が自動計算対象です）','shift_jis'), $objFormat );
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
		if(!isset($aryResult["lngsalesclasscode"])) break;
		{
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
					// レートを求める
//					if( (int)$aryResult["lngmonetaryratecode"] == 1 )
//					{
						$curRate1 = (float)$aryResult["curconversionrate1"];
//					}
//					elseif( (int)$aryResult["lngmonetaryratecode"] == 2 )
//					{
						$curRate2 = (float)$aryResult["curconversionrate2"];
//					}
					
						
					// =IF(S3="",9999, IF(S3=1,1*1, IF(S3=2,2*2)))
					//$curCalc = '=IF(S3=0, '.(float)$aryResult["curproductpricereceive"].' * '. $curRate .', IF(S3=1, ' . (float)$aryResult["curproductpricereceive"] .' * '. (float)$aryResult["curconversionrate1"] . ', IF(S3=2, '. (float)$aryResult["curproductpricereceive"] .' * '. (float)$aryResult["curconversionrate2"] . ', 0)))';
					$curCalc = '=IF(O3=1, ' . $curCalc .' * '. (float)$curRate1 . ', IF(O3=2, '. $curCalc .' * '. (float)$curRate2 . ', 0))';
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
			//	if( !fncSpreadSheetExcelFormatSetting($workbook, "setHAlignRight", $objFormat) ) $objFormat = null;
			
				// 部材費合計金額 が無かったら
//				if( !isset($aryResult["curmembercost"]) )
				//売上区分本荷の場合は製品原価0であればエラー
//部材費用から　総費用に変更
//				if( (float)$aryResult["curmembercost"]==0 && (int)$aryResult["lngsalesclasscode"] == 1)
				if( (float)$aryResult["curmanufacturingcost"]==0 && (int)$aryResult["lngsalesclasscode"] == 1)
				{
					$varCellData = 'ERR';
					break;
				}
				//生産予定数単位コードは2（C/T）の場合は　数量*カートン入数=pcs数
				if( (int)$aryResult["lngproductionunitcode"] == 2)
				{
					$ProductionQuantity = (int)$aryResult["lngproductionquantity"]*(int)$aryResult["lngcartonquantity"];
				}
				else
				{
					$ProductionQuantity = (int)$aryResult["lngproductionquantity"];
				
				}

				// 売上区分コード判定
				if( (int)$aryResult["lngsalesclasscode"] == 1 )
				{
					// グループコード判定
					switch((int)$aryResult["lnginchargegroupcode"])
					{
						case 3:		//キャンディチーム
						case 4:		//トーイチーム
						case 27:	//トイチーム(ガールズトイ)
							// 部材費合計金額 / 生産予定数
							$curCalc = (float)$aryResult["curmembercost"] / (float)$ProductionQuantity;
							break;
						default:
							// 総製造費用 / 生産予定数
							$curCalc = (float)$aryResult["curmanufacturingcost"] / (float)$ProductionQuantity;
					}
				}
				else
				{	// 単価

					if( (int)$aryResult["lngmonetaryunitcode"] != 1 )
					{
						$curCalc = '=O'.$strNo.'';//'*'.(int)$aryResult["lngproductquantity"].'';
						$varCellData = $curCalc;
						break;
					}
					$curCalc = (float)$aryResult["curproductpricesales"];

				}
				$varCellData = round( (float)$curCalc, 2);	// 小数点以下2桁で丸め処理
				break;
				
			case 22:	// 製品原価合計
			//	if( !fncSpreadSheetExcelFormatSetting($workbook, "setHAlignRight", $objFormat) ) $objFormat = null;

//				if( !isset($aryResult["curmembercost"]) )
				//売上区分本荷の場合は製品原価0であればエラー
//部材費用から　総費用に変更
//				if( (float)$aryResult["curmembercost"]==0 && (int)$aryResult["lngsalesclasscode"] == 1)
				if( (float)$aryResult["curmanufacturingcost"]==0 && (int)$aryResult["lngsalesclasscode"] == 1)
				{
					$varCellData = 'ERR';
					break;
				}
				//生産予定数単位コードは2（C/T）の場合は　数量*カートン入数=pcs数
				if( (int)$aryResult["lngproductionunitcode"] == 2)
				{
					$ProductionQuantity = (int)$aryResult["lngproductionquantity"]*(int)$aryResult["lngcartonquantity"];
				}
				else
				{
					$ProductionQuantity = (int)$aryResult["lngproductionquantity"];
				
				}
				// 売上区分コード判定
				if( (int)$aryResult["lngsalesclasscode"] == 1 )
				{
					// グループコード判定
					switch((int)$aryResult["lnginchargegroupcode"])
					{
						case 3:		//キャンディチーム
						case 4:		//トーイチーム
						case 27:	//トイチーム(ガールズトイ)
							// 部材費合計金額 / 生産予定数
							$curCalc1 = (float)$aryResult["curmembercost"] / (int)$ProductionQuantity;
							break;
						default:
							// 総製造費用 / 生産予定数
							$curCalc1 = (float)$aryResult["curmanufacturingcost"] / (int)$ProductionQuantity;
					}
					// c/t 
					if( (int)$aryResult["lngproductunitcode"] == 2 )
					{
						// 数量 * 製品原価＠ * カートン入数
						$curCalc = (int)$aryResult["lngproductquantity"] * (float)$curCalc1 * (int)$aryResult["lngcartonquantity"];
					}
					else
					{
						// 数量 * 製品原価＠
						$curCalc = (int)$aryResult["lngproductquantity"] * (float)$curCalc1;
					}
				}
				else
				{
					if( (int)$aryResult["lngmonetaryunitcode"] != 1 )
					{
						$curCalc = '=T'.$strNo.'*'.(int)$aryResult["lngproductquantity"].'';
						$varCellData = $curCalc;
						break;
					}
					// 数量 * 製品原価＠
//					$curCalc = (int)$aryResult["lngproductquantity"] * (float)$aryDataset[20]->data;///////////////////
					$curCalc = (int)$aryDataset[19]->data;
				}

//				$varCellData = floor((float)$curCalc);	// 小数点以下切り捨て
//				$varCellData = floor($curCalc);	// 小数点以下切り捨て

				$varCellData = round( (float)$curCalc, 0);	// 小数点以下4桁で丸め処理
				break;
				
			case 23:	// 目標利益
				// 売上合計が0では無い
				if ((int)$aryResult["lngsalesclasscode"] == 1 )
				{
					// 売上合計 - 製品原価合計
					$curCalc = (float)$aryDataset[19]->data - $aryDataset[22]->data;
				
				}else
				if( $aryDataset[19]->data != 0 )
				{
					// 売上合計 - 製品原価合計
					$curCalc = (float)$aryDataset[19]->data - $aryDataset[22]->data;
				}
				$varCellData = round( (float)$curCalc, 4);	// 小数点以下4桁で丸め処理
				break;
				
			case 24:	// 目標利益率
				// 目標利益が0では無い、売上合計と製品原価合計が同一では無い
				if( $aryDataset[23]->data != 0 && $aryDataset[19]->data !=  $aryDataset[22]->data && $aryDataset[19]->data != 0)
				{
					//目標利益 / 売上合計
					$curCalc = $aryDataset[23]->data / $aryDataset[19]->data;
					$varCellData = round( (float)$curCalc, 4);	// 小数点以下2桁で丸め処理
					break;
				}
				break;
				
			default:
				//
				// 上記の case に当てはまらないもの（SQL実行文のカラム名から一致するものを取得）
				//
				$strKey = strtolower($aryDataset[$j]->name);	//				$varCellData = preg_replace ( "/\s+?$/", "", $aryResult[$strKey] );// 空白削除
				$varCellData = (string)$aryResult[$strKey];

				//$varCellData = $aryResult[$strKey];
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
//部材費用から　総費用に変更
//				if( (float)$aryResult["curmembercost"]==0 && (int)$aryResult["lngsalesclasscode"] == 1)
				if( (float)$aryResult["curmanufacturingcost"]==0 && (int)$aryResult["lngsalesclasscode"] == 1)
					{
					// ERR の場合
					if($aryDataset[$j]->data == 'ERR')
					{
						// データタイプの変更（計算式）
						$aryDataset[$j]->type = 'text';
						$objFormat =& fncSpreadSheetCellFormat($workbook, $aryFormat, 'errorNumberDec');
						break;
					}

					// データタイプの変更（計算式）
					$aryDataset[$j]->type = 'text';
					$objFormat =& fncSpreadSheetCellFormat($workbook, $aryFormat, 'float');
					break;
				}
				if( (int)$aryResult["lngmonetaryunitcode"] != 1 && (int)$aryResult["lngsalesclasscode"] != 1)
				{
					// データタイプの変更（計算式）
					$aryDataset[$j]->type = '';
					$objFormat =& fncSpreadSheetCellFormat($workbook, $aryFormat, 'numberCalcDec');
					break;
				}
				$aryDataset[$j]->type = 'float';
				$objFormat =& fncSpreadSheetCellFormat($workbook, $aryFormat, 'numberDecPoint');
				break;
			case 21:
				$objFormat =& fncSpreadSheetCellFormat($workbook, $aryFormat, 'numberCalcPoint');
				break;
			case 22:	// 製品原価合計
//部材費用から　総費用に変更
//				if( (float)$aryResult["curmembercost"]==0 && (int)$aryResult["lngsalesclasscode"] == 1)
				if( (float)$aryResult["curmanufacturingcost"]==0 && (int)$aryResult["lngsalesclasscode"] == 1)
				{
					// ERR の場合
					if($aryDataset[$j]->data == 'ERR')
					{
						// データタイプの変更（計算式）
						$aryDataset[$j]->type = 'text';
						$objFormat =& fncSpreadSheetCellFormat($workbook, $aryFormat, 'errorNumber');
						break;
					}

					// データタイプの変更（計算式）
					$aryDataset[$j]->type = 'text';
					$objFormat =& fncSpreadSheetCellFormat($workbook, $aryFormat, 'float');
					break;
				}
				if( (int)$aryResult["lngmonetaryunitcode"] != 1 && (int)$aryResult["lngsalesclasscode"] != 1)
				{
					// データタイプの変更（計算式）
					$aryDataset[$j]->type = '';
					$objFormat =& fncSpreadSheetCellFormat($workbook, $aryFormat, 'numberCalc');
					break;
				}
				$aryDataset[$j]->type = 'float';
				$objFormat =& fncSpreadSheetCellFormat($workbook, $aryFormat, 'numberCalcPoint');
//小数点2桁表示			$objFormat =& fncSpreadSheetCellFormat($workbook, $aryFormat, 'numberDecPoint');
				break;


			case 23:	// 目標利益
//				if( (float)$aryResult["curmembercost"]==0 && (int)$aryResult["lngsalesclasscode"] == 1)
				if( (float)$aryResult["curmanufacturingcost"]==0 && (int)$aryResult["lngsalesclasscode"] == 1)
				{
					if($aryDataset[22]->data == 'ERR')
					{
						// データタイプの変更（計算式）
//						$aryDataset[$j]->type = 'text';
						$objFormat =& fncSpreadSheetCellFormat($workbook, $aryFormat, 'errorNumber');
						break;
					}break;
				}
				$objFormat =& fncSpreadSheetCellFormat($workbook, $aryFormat, 'numberCalcPoint');
				break;			

			case 24:	// 目標利益率
				if($aryDataset[23]->data == 0)
				{
					$objFormat =& fncSpreadSheetCellFormat($workbook, $aryFormat, $aryDataset[$j]->type);
					break;
				}else
				{
					$objFormat =& fncSpreadSheetCellFormat($workbook, $aryFormat, 'numberPercent');
					break;
				}

			default:
			$objFormat =& fncSpreadSheetCellFormat($workbook, $aryFormat, $aryDataset[$j]->type);
		}

		return $objFormat;

	}

?>