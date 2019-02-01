<?php

	function getFactoryColor()
	{
		$FACTORY_COLOR = array(
			10	=> 33,
			11	=> 34,
			28	=> 35,
			64	=> 36,
			156	=> 37,
			168	=> 38,
			169	=> 39,
			284	=> 40,
			333	=> 41,
			343	=> 42,
			357	=> 43,
			361	=> 44,
			388	=> 45,
			392	=> 46,
			0	=> 47
		);
		return $FACTORY_COLOR;
	}

	function getGroupOriginColor()
	{
		$GROUP_ORIGIN_COLOR = array(
			7	=> 10,
			9	=> 11,
			10	=> 12,
			2	=> 13,
			3	=> 14,
			4	=> 15,
			27	=> 16,
			28	=> 17,
			5	=> 18,
			25	=> 19,
			43	=> 20,
			44	=> 21,
			29	=> 22,
			37	=> 23,
			26	=> 24,
			6	=> 25,
			30	=> 26,
			36	=> 27,
			31	=> 28
		);
		return $GROUP_ORIGIN_COLOR;
	}

	function hex2RGB($hex) 
	{
	        preg_match("/^#{0,1}([0-9a-f]{1,6})$/i",$hex,$match);
	        if(!isset($match[1]))
	        {
	            return false;
	        }

	        if(strlen($match[1]) == 6)
	        {
	            list($r, $g, $b) = array($hex[0].$hex[1],$hex[2].$hex[3],$hex[4].$hex[5]);
	        }
	        elseif(strlen($match[1]) == 3)
	        {
	            list($r, $g, $b) = array($hex[0].$hex[0],$hex[1].$hex[1],$hex[2].$hex[2]);
	        }
	        else if(strlen($match[1]) == 2)
	        {
	            list($r, $g, $b) = array($hex[0].$hex[1],$hex[0].$hex[1],$hex[0].$hex[1]);
	        }
	        else if(strlen($match[1]) == 1)
	        {
	            list($r, $g, $b) = array($hex.$hex,$hex.$hex,$hex.$hex);
	        }
	        else
	        {
	            return false;
	        }

	        $color = array();
	        $color['r'] = hexdec($r);
	        $color['g'] = hexdec($g);
	        $color['b'] = hexdec($b);

	        return $color;
	}

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
	function fncfncSpreadSheetCellHeader_pplan(&$worksheet, &$objFormat, $aryData)
	{
		$worksheet->writeString( 0, 0, mb_convert_encoding('商品計画('.$aryData["dtmAppropriationDateFrom"].'-'.$aryData["dtmAppropriationDateTo"].')','shift_jis'), $objFormat );
		
		$ymd = explode('/', $aryData["dtmAppropriationDateFrom"]);

		for($i=0; $i< 6; $i++)
		{
			$col = 4*$i;
			$month	= $ymd[1]+$i;
			$day	= $ymd[2];
			$year	= $ymd[0];
			$varMktime = mktime( 0,0,0, $month, $day, $year );
			$iMonth = date( "m", $varMktime );

			$worksheet->writeString( 2, 2+$col, mb_convert_encoding($iMonth.'月発売','shift_jis'), $objFormat );
			$worksheet->mergeCells( 2,2+$col,2,5+$col);

			$worksheet->writeString( 3, 2+$col, mb_convert_encoding('4点セット','shift_jis'), $objFormat );
			$worksheet->mergeCells( 3,2+$col,6,2+$col);
			$worksheet->writeString( 3, 3+$col, mb_convert_encoding('商品名','shift_jis'), $objFormat );
			$worksheet->mergeCells( 3,3+$col,3,4+$col);
			$worksheet->writeString( 4, 3+$col, mb_convert_encoding('商品コード','shift_jis'), $objFormat );
			$worksheet->writeString( 5, 3+$col, mb_convert_encoding('工場名','shift_jis'), $objFormat );
			$worksheet->writeString( 6, 3+$col, mb_convert_encoding('企画/開発','shift_jis'), $objFormat );

			$worksheet->writeString( 4, 4+$col, mb_convert_encoding('品目','shift_jis'), $objFormat );
			$worksheet->writeString( 5, 4+$col, mb_convert_encoding('事業部','shift_jis'), $objFormat );
			$worksheet->writeString( 6, 4+$col, mb_convert_encoding('入り数/P/C','shift_jis'), $objFormat );

			$worksheet->writeString( 3, 5+$col, mb_convert_encoding('販売形態','shift_jis'), $objFormat );
			$worksheet->writeString( 4, 5+$col, mb_convert_encoding('上代','shift_jis'), $objFormat );
			$worksheet->writeString( 5, 5+$col, mb_convert_encoding('納品数/C/T','shift_jis'), $objFormat );
			$worksheet->writeString( 6, 5+$col, mb_convert_encoding('証紙','shift_jis'), $objFormat );
		}
		
		return 7;	// ヘッダーで使用した行数を返却
	}

	/*
	//****************************************************************************
	//	工場別のカラムヘッダーの描画処理
	*
	*	@param	object	$workbook		ワークブックオブジェクト
	*	@param	integer	$worksheet		ワークシートオブジェクト
	*	@param	integer	$aryFL			工場の行情報の構造体（ユニーク）
	*	@param	integer	$aryFCInfo		工場情報の構造体（ユニーク）
	*
	*	@return							True
	*
	//****************************************************************************
	*/
	function fncfncSpreadSheetColumnHeader_pplan(&$workbook, &$worksheet, $aryFL, $aryFCInfo)
	{

		// 工場別ボーダーのフォーマット定義
		$aryWriteRow = array("","","","","","","","","","","","","","","","","","","","","","","","");
		unset($objformat);
		$objFormatBorderT =& fncSpreadSheetCellFormat($workbook, $objFormat, 'blockRowBorderT');

		// 工場別カラー
		$aryFactoryColor = getFactoryColor();

		$nowLine=7;
		reset($aryFL);
		while(list($factoryCode, $val) = each($aryFL))
		{
//echo $factoryCode.">$val\n";
//echo "LINE: \n";
//echo $nowLine . " > " . ($nowLine+($val*4)-1);
//echo "\n";
			unset($objFormatColHead);
			//unset($objFormat);
			//unset($objFormatBorderT);

			if(empty($factoryCode))
			{
				$strFactory = mb_convert_encoding("工場なし", 'shift_jis');
			}
			else
			{
				$strFactory =  "[".$aryFCInfo[$factoryCode]->strcompanydisplaycode."] ".mb_convert_encoding($aryFCInfo[$factoryCode]->strcompanydisplayname,'shift_jis');
			}

			$objFormatColHead =& $workbook->addFormat();
			fncSpreadSheetExcelFormatSetting($objFormatColHead, "setVAlignTop");
			fncSpreadSheetExcelFormatSetting($objFormatColHead, "setTextRotation90");
			fncSpreadSheetExcelFormatSettingColor($objFormatColHead, "fg", $aryFactoryColor[(int)$aryFCInfo[$factoryCode]->lngfactorycode]);
//var_dump($aryFactoryColor[(int)$aryFCInfo[$factoryCode]->lngfactorycode]);
			
			// 工場別の行ボーダーを描画
			$worksheet->writeRow( $nowLine, 2, $aryWriteRow, $objFormatBorderT);

			$worksheet->writeString( $nowLine, 0, $strFactory, $objFormatColHead );
			$worksheet->mergeCells( $nowLine, 0, $nowLine+($val*4)-1, 0);

			$nowLine = $nowLine+ ($val*4);
		}
//exit;
		return true;
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
	function fncSpreadSheetCellData_pplan($aryDataset, $i, $j, $aryResult, $lngHeadLineCnt)
	{
		$varCellData = "";

		switch((int)$j)
		{

			case 0: // ■ 商品化企画書
				$varCellData = empty($aryResult["strreportkeycode"]) ? "": "■";
				break;
			case 1:	// 商品名	
				$varCellData = $aryResult["strproductname"];
			case 2:
				break;
			case 3:	// 販売形態
				break;

			case 4:	// ■ 見積原価
				$varCellData = empty($aryResult["lngestimateno"]) ? "": "■";
				break;
			case 5:	// 商品コード
				$varCellData = $aryResult["strproductcode"];
				break;
			case 6:	// 品目
				$varCellData = $aryResult["strgoodscode"];
				break;
			case 7:	// 上代
				$varCellData = $aryResult["curretailprice"];
				break;

			case 8:	// ■ スケジュール表
				break;
			case 9:	// 工場名
				$varCellData = $aryResult["strcompanydisplayname"];
				//$varCellData = $aryResult["lngfactorycode"];
				break;
			case 10:// 事業部
				$varCellData = $aryResult["strcustomercompanyname"];
				break;
			case 11:// 納品数/C/T
				$varCellData = $aryResult["lngdeliveryquantity"];
				break;

			case 12:// ■ 部品表
				break;
			case 13:// 企画/開発
				$varCellData = $aryResult["struserdisplayname"];
				break;
			case 14:// 入り数/P/C
				$varCellData = $aryResult["lngcartonquantity"];
				break;
			case 15:// 証紙
				$varCellData = $aryResult["strcertificateclassname"];
				break;
		}

		return $varCellData;

	}

	/*
	//****************************************************************************
	// 02 概算売上
		セルの書式別・フォーマットオブジェクトの生成

	*	@param	object	$worksheet		ワークブックオブジェクト
	*	@param	array	$aryFormat		フォーマットオブジェクト格納配列
	*	@param	array	$aryDataset		データセット定義
	*	@param	integer	$j				列カウンター
	*	@param	array	$aryResult		データベース取得値
	*
	*	@return	object	$objFormat		フォーマットオブジェクト
40: CCFF99
	//****************************************************************************
	*/
	function fncSpreadSheetCellFormat_pplan(&$workbook, &$aryFormat, &$aryDataset, $j, $aryResult, $iLine)
	{

		unset($objFormat);

		switch( (int)$j )
		{
			case 1://製品名称
			case 2:
				$aryGroupOriginColor = getGroupOriginColor();
				if( !isset($aryFormat['blockGroupColor_'.$aryResult["lnggroupcode"]]))
				{
//var_dump(substr($aryResult["strgroupdisplaycolor"], 1));
					fncSpreadSheetExcelFormatSettingCustomColor($workbook, $aryGroupOriginColor[$aryResult["lnggroupcode"]], hex2RGB(substr($aryResult["strgroupdisplaycolor"],1)) );
				}
				$objFormat =& fncSpreadSheetCellFormat($workbook, $aryFormat, 'blockGroupColor_'.$aryGroupOriginColor[$aryResult["lnggroupcode"]]);
				//$objFormat =& $workbook->addFormat();
				fncSpreadSheetExcelFormatSetting($objFormat, "setSize8");
				fncSpreadSheetExcelFormatSetting($objFormat, "setHAlignLeft");
				fncSpreadSheetExcelFormatSetting($objFormat, "borderT");

				fncSpreadSheetExcelFormatSettingColor($objFormat, "fg", $aryGroupOriginColor[$aryResult["lnggroupcode"]]);
//var_dump(hex2RGB($aryResult["strgroupdisplaycolor"]));

/*				if( $aryResult["lngfactorycode"] == "10")
				{
					$objFormat->setFgColor(33+7);
				}
				else
				{
					$objFormat->setFgColor(36+7);	
				}*/
				break;
			case 3:	// 販売形態
				$objFormat =& fncSpreadSheetCellFormat($workbook, $aryFormat, 'text8pt');
				fncSpreadSheetExcelFormatSetting($objFormat, "borderT");
				break;

			case 4:	// ■ 見積原価
				break;
			case 5:	// 商品コード
				break;
			case 6:	// 品目
				break;
			case 7:	// 上代
				$aryDataset[$j]->type = 'int';
				$objFormat =& fncSpreadSheetCellFormat($workbook, $aryFormat, 'blockNumber');
				break;

			case 8:	// ■ スケジュール表
				break;
			case 9:	// 工場名
				//$objFormat =& fncSpreadSheetCellFormat($workbook, $aryFormat, 'text8pt');
				break;
			case 10:// 事業部
				//$objFormat =& fncSpreadSheetCellFormat($workbook, $aryFormat, 'text8pt');
				break;
			case 11:// 納品数/C/T
				$aryDataset[$j]->type = 'int';
				$objFormat =& fncSpreadSheetCellFormat($workbook, $aryFormat, 'blockNumber');
				break;

			case 12:// ■ 部品表
				break;
			case 13:// 企画/開発
				break;
			case 14:// 入り数/P/C
				$aryDataset[$j]->type = 'int';
				$objFormat =& fncSpreadSheetCellFormat($workbook, $aryFormat, 'blockNumber');
				break;
			case 15:// 証紙
				break;
		}
		if( $j == 0 && $iLine == 0 )
		{
			if(!isset($objFormat)) $objFormat =& fncSpreadSheetCellFormat($workbook, $aryFormat, 'blockRowBorderT');
			//fncSpreadSheetExcelFormatSetting($objFormat, "setTopColor5");
			//fncSpreadSheetExcelFormatSetting($objFormat, "borderT");
		}
		return $objFormat;

	}

?>
