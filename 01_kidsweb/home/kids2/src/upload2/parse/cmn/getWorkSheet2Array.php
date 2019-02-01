<?php
/* ************************************************************************* */
/* UPLOAD Ver.Peruser */
/* ************************************************************************* */

	//@------------------------------------------------------------------------
	/**
	*	概要	: Excelワークシートデータ -> Array
	*
	*
	*	解説	: ワークシートデータから配列生成、返却
	*
	*	@param	[$exc]		: [Object]	. ExcelParser Object
	*	@param	[$objMap]	: [Object]	. Excel Mapping Object
	*	@param	[$ws_num]	: [Integer]	. 選択したExcelワークシート番号
	*
	*	@return	[$aryData]	: [Array]
	*/
	//-------------------------------------------------------------------------
	function getWorkSheet2Array( $exc, $objMap, $ws_num )
	{
//		require_once ( LIB_DEBUGFILE );
//		require_once ( '/home/kids2/intra-v2-1/src/upload2/cmn/lib_peruser.php' );


		$aryData				= array();	// データ配列
		$blnEOF					= false;	// パース終了フラグ
		$strBuffCol				= "";		// 列文字列バッファ
		$lngBuffRow				= 0;		// 行番号バッファ
		$curBuffConversionRate	= 0;		// 換算レートバッファ


		// ワークシートデータ取得
		$ws	= $exc->worksheet['data'][$ws_num];
		

		$lngMaxCol = $exc->maxcell[$ws_num];
		$lngMaxRow = $exc->maxrow[$ws_num];
//fncDebug( 'upload_parse_parse_3.txt', 'COL:'.$lngCol.'/ROW:'.$lngRow, __FILE__, __LINE__, 'a');

/*
$j=0;
$a =chr((int)($j/26)+64);
$b =chr(($j % 26) + 65);
*/

		// データが取得出来ない場合
		if( !isset($lngMaxCol) || !isset($lngMaxRow) )
		{
			// emtpty worksheet
			return false;
		}

/*
		// データが取得出来ない場合
		if( !is_array($ws) || !isset($ws['max_row']) || !isset($ws['max_col']) )
		{
			// emtpty worksheet
			return false;
		}
*/

$aryTypeTest[0] = '';

		// データ解析開始
		for( $i=0; $i <= $lngMaxRow; $i++ )
		{
			// 行番号取得
			$lngBuffRow = ($i+1);



			//if( !isset($ws['cell'][$i]) || !is_array($ws['cell'][$i]) )
//			if( !isset($exc->dispcell($ws_num, $i, $j)) )
//			{
				// print an empty row
				for( $j=0; $j <= $lngMaxRow; $j++ )
				{
					// 列文字列取得
					if( $j>25 ) $strBuffCol = chr((int)($j/26)+64);
					$strBuffCol = chr(($j % 26) + 65);
//fncDebug( 'upload_parse_parse_4.txt', 'strBuffCol:'.$strBuffCol.' - '.outStr($exc->dispcell($ws_num, $i, $j)), __FILE__, __LINE__, 'a');

					// 列終端判定
					if( $j >= COL_EOF_INT ) break;

					$aryData[$i][$strBuffCol]	= "";

				}


//				continue;
//			}

			$aryData[$i]['row'] = $i;


			for( $j=0; $j <= $lngMaxCol; $j++ )
			{
				// 列文字列取得
				if( $j>25 ) $strBuffCol = chr((int)($j/26)+64);
				$strBuffCol = chr(($j % 26) + 65);

				// 列終端判定
				if( $j >= COL_EOF_INT ) continue;

				// 解析終了
				if( fncCheckFinish( $blnEOF, $strBuffCol ) )
				{
					break 2;
				}

				//if( ( !is_array($ws['cell'][$i]) ) || ( !isset($ws['cell'][$i][$j]) ) )
				// Excel表示上のデータを取得
				$strCell = $exc->dispcell($ws_num, $i, $j);
				
				// データタイプを取得
				$aryType = $exc->getAttribute($ws_num, $i, $j);

				// データタイプの保持
				$aryData[$i]['BeforeType'][$strBuffCol]	= $aryType['type'];

				// type Test
				if( !array_key_exists( hexdec($aryType['type']), $aryTypeTest) )
				{
					$aryTypeTest[hexdec($aryType['type'])] ='0x'.dechex($aryType['type']);
				}


				// 空の場合はスキップ
				if( strlen(trim($strCell)) == 0 )
				{
					$aryData[$i][$strBuffCol]	= "";
					continue;
				}

				// ----------------------------------------------------
				// データタイプによる処理の振り分け
				// ----------------------------------------------------
				switch( $aryType['type'] )
				{
					// blank
					case Type_BLANK:
						break;
					
					// string
					//	Type_LABELSST	:0xfd	文字列
					case Type_LABELSST:

						if( strlen(trim($strCell)) == 0 )
						{
							$aryData[$i][$strBuffCol]	= "";
							break;
						}

						// 終了キー判定
						if( fncCheckEOFKey( $strCell ) )
						{
							$aryData[$i][$strBuffCol]	= "";
							$blnEOF	= true;
						}
						else
						{
							$aryData[$i][$strBuffCol]	= $strCell;
//fncDebug( 'upload_parse_cmn_getWorkSheet2Array3.txt', $i.':'.$strBuffCol.'>'.$exc->recXF[$cell['xf']]['formindex'].':'.$aryData[$i][$strBuffCol], __FILE__, __LINE__, 'a');
						}

						break;

					//	Type_FORMULA	: 計算式
					//	Type_FORMULA2	:0x6	計算式
					//	Type_RK2		:0x27e	数値
					//	Type_NUMBER		:0x515	小数点数値
					case Type_FORMULA:
					case Type_FORMULA2:
					case Type_RK2:
					case Type_NUMBER:

fncDebug( 'upload_parse_cmn_getWorkSheet2Array3.txt', $i.':'.$strBuffCol.'>'.$exc->recXF[$cell['xf']]['formindex'].':'.$exc->dispcell($ws_num, $i, $j), __FILE__, __LINE__, 'a');

						$varTemp = $exc->dispcell($ws_num, $i, $j);			// 表示上のデータ
						$varTempNum = $exc->dispcell($ws_num, $i, $j, 1);	// 0フォーマットした数値データ（記号などは取り除かれた数値）

fncDebug( 'upload_parse_cmn_varTemp.txt', $varTemp.':'.$ps.':'.$exc->dispcell($ws_num, $i, $j), __FILE__, __LINE__, 'a');

						// 各種記号などの存在位置を取得
						$psp = strpos($varTemp, '%');
						$psy = strpos($varTemp, '\\');
						$psc = strpos($varTemp, '.');

						// % 表記 の場合
						if( $psp !== false )
						{
							$varCell = $varTemp;	// % が付加されていたら、数値変換値を保持
							$cases=1;
						}
						// ￥マーク表記の場合かつ0の場合
						else if( $psy !== false && ($varTempNum == "0"||ceil($varTempNum)==0) )
						{
							$varCell = "";			// データを消去して保持
							$cases=2;
						}
						// 小数点以下がある場合かつ0の場合
						else if( $psc !== false && ($varTempNum == "0"||ceil($varTempNum)==0) )
						{
							$varCell = "";			// データを消去して保持
							$cases=3;
						}
						// 小数点以下がある場合
						else if( $psc !== false )
						{
							// 小数点以下を0フォーマットで付加する
							$lngPt = (strlen($varTemp)-$psc-1);
							$varCell = number_format( $varTempNum, $lngPt, '.', '');	// 千位毎の区切りがなく小数点以下も表記して保持
							$cases=4;
						}
						else
						{
							//$cell = $exc->cellblock[$ws_num][$i][$j];
							//$exc->recXF[$cell['xf']]['formindex'] = 1;
							if(empty($varTempNum)) $varTempNum=0;
							$varCell = (float)$varTempNum;	// 数値データとして保持
							$cases=5;
						}

						// 変換データ保持
						$aryData[$i][$strBuffCol]	= $varCell;


//fncDebug( 'upload_parse_cmn_getWorkSheet2Array1.txt', $lngBuffRow.':'.$strBuffCol.'::['.$cases."]::[".$lngPt."]::".$varCell."::".$varTempNum, __FILE__, __LINE__, 'a');
//fncDebug( 'upload_parse_cmn_getWorkSheet2Array2.txt', $i.':'.$strBuffCol.'>'.$aryData[$i][$strBuffCol], __FILE__, __LINE__, 'a');


						// 換算レート取得
						if( (string)($lngBuffRow.$strBuffCol) == ROWCOL_CONVERSION_RATE )
						{
							$curBuffConversionRate	= $varCell;
//fncDebug( 'upload_parse_cmn_getWorkSheet2Array.txt', $lngBuffRow.$strBuffCol.'>>'.$varCell, __FILE__, __LINE__, 'a');
						}

						break;

					default:
						break;
				}

			}
			// end for loop

		}
		// end for loop

//fncDebug( 'upload_parse_parse_Data2.txt', $aryData, __FILE__, __LINE__, 'a');


		// 明細行「単価」再計算
		for( $i = 0; $i < count($aryData); $i++ )
		{
			// 再計算処理が必要ではない場合、Continue
			if( !getProductPrice( $objMap, $aryData, $i, $curBuffConversionRate ) ) continue;

//fncDebug( 'upload_parse_parse_Data3.0.txt', $aryData, __FILE__, __LINE__);

			// 単価取得（再計算）
			$aryData[$i][COL_PRODUCT_PRICE]	= getProductPrice( $objMap, $aryData, $i, $curBuffConversionRate );
		}


		// 明細行無効データ消去
		$aryData	= fncDeleteInvalidDetailData( $objMap, $aryData );

//fncDebug( 'upload_parse_parse_Data2.2.txt', $aryData, __FILE__, __LINE__);


		unset( $objMap );
		return $aryData;
	}



	//@------------------------------------------------------------------------
	/**
	*	概要	: 明細行「単価」再計算処理
	*
	*
	*	解説	: 単価 = 計画原価 ÷ 計画個数 で再計算
	*
	*
	*	@param	[$objMap]	: [Object]	. Excel Mapping Object
	*	@param	[$ary]		: [Array]	. 取得したワークシートデータ配列
	*	@param	[$row]		: [Integer]	. ワークシート行番号
	*	@param	[$rate]		: [Integer]	. 換算レート
	*
	*	@return	[$retVal]	: [Boolean/String]
	*/
	//-------------------------------------------------------------------------
	function getProductPrice( $objMap, $ary, $row, $rate )
	{

require_once ( LIB_DEBUGFILE );

		$aryValue	= array();


		// 明細行ではない場合、処理終了
		// 売上分類、仕入科目 をチェック。どちらも該当しない場合 False
		if( !$objMap->fncCheckDetail($ary[$row][COL_SALES_DIVISION_CODE], PROC_SALES) && !$objMap->fncCheckDetail($ary[$row][COL_STOCK_SUBJECT_CODE], PROC_STOCK) )
		{
//fncDebug( 'getWorkSheet2Array_01.txt', '[row]>'.$row.'[data]>'.COL_STOCK_SUBJECT_CODE.'>>>'.$ary[$row][COL_STOCK_SUBJECT_CODE], __FILE__, __LINE__, 'a');
//fncDebug( 'getWorkSheet2Array_01.txt', '[row]>'.$row.'[data]>'.COL_SALES_DIVISION_CODE.'>>>'.$ary[$row][COL_SALES_DIVISION_CODE], __FILE__, __LINE__, 'a');
			return false;
		}



		// 計算対象値を設定
		$aryValue[NAME_SUBTOTAL_PRICE]		= is_numeric($ary[$row][COL_SUBTOTAL_PRICE]) ? 		$ary[$row][COL_SUBTOTAL_PRICE] : 0;	// 計画原価
		$aryValue[NAME_PRODUCT_QUANTITY]	= is_numeric($ary[$row][COL_PRODUCT_QUANTITY]) ?	$ary[$row][COL_PRODUCT_QUANTITY] : 0;	// 計画個数

//fncDebug( 'getWorkSheet2Array_02.txt', '[row]>'.$row.'[data]>'.COL_SUBTOTAL_PRICE.'>>>'.$ary[$row][COL_SUBTOTAL_PRICE], __FILE__, __LINE__, 'a');
//fncDebug( 'getWorkSheet2Array_02.txt', $aryValue, __FILE__, __LINE__, 'a');


		// 計算対象外値の場合、処理終了
		if( !fncCheckCalculateValue($aryValue, $rate) ) return false;

//fncDebug( 'fncDecimalFormat_1.txt', 'Start:'.$aryValue[NAME_SUBTOTAL_PRICE], __FILE__, __LINE__, 'a');
//fncDebug( 'fncDecimalFormat_1.txt', 'End:'.$aryValue[NAME_PRODUCT_QUANTITY], __FILE__, __LINE__, 'a');

		// 単価再計算
		switch( $ary[$row][COL_MONETARY_UNIT_CODE] )
		{
			case CELL_MONETARY_JP:
				// 単価 = 計画原価 ÷ 計画個数
				$retVal	= $aryValue[NAME_SUBTOTAL_PRICE] / $aryValue[NAME_PRODUCT_QUANTITY];

				// 小数点桁数指定
				$retVal	= fncDecimalFormat( (string)$retVal, DECIMAL_DIGIT_YEN );
				break;

			case CELL_MONETARY_US:
				// 単価 = ( 計画原価 ÷ 計画個数 ) ÷ 換算レート
				$retVal	= ( $aryValue[NAME_SUBTOTAL_PRICE] / $aryValue[NAME_PRODUCT_QUANTITY] ) / $rate;

				// 小数点桁数指定
				$retVal	= fncDecimalFormat( (string)$retVal, DECIMAL_DIGIT_US );
				break;

			case CELL_MONETARY_HKD:
				// 単価 = ( 計画原価 ÷ 計画個数 ) ÷ 換算レート
				$retVal	= ( $aryValue[NAME_SUBTOTAL_PRICE] / $aryValue[NAME_PRODUCT_QUANTITY] ) / $rate;

				// 小数点桁数指定
				$retVal	= fncDecimalFormat( (string)$retVal, DECIMAL_DIGIT_HKD );
				break;

			default:
				// 単価 = 計画原価 ÷ 計画個数
				$retVal	= ( $aryValue[NAME_SUBTOTAL_PRICE] / $aryValue[NAME_PRODUCT_QUANTITY] );

				// 小数点桁数指定
				$retVal	= fncDecimalFormat( (string)$retVal, DECIMAL_DIGIT_YEN );
				break;
		}

//fncDebug( 'getWorkSheet2Array_03.txt', $retVal, __FILE__, __LINE__, 'a');

		return $retVal;
	}


	//@------------------------------------------------------------------------
	/**
	*	概要	: 明細行「単価」再計算対象値チェック
	*
	*
	*	解説	: 「単価」再計算時に使用する値をチェックする
	*
	*
	*	@param	[$aryValue]	: [Array]	. 再計算値配列
	*
	*	@return	: [Boolean]
	*/
	//-------------------------------------------------------------------------
	function fncCheckCalculateValue( $aryValue, $rate )
	{
		/*
		while( list($index, $value) = each($aryValue) )
		{
			// 数値が存在しないまたは、「0」の場合、処理終了
			if( is_null($value) || empty($value) || $value == 0 ) return false;
		}
*/
		$value = $rate;
		if( is_null($value) || empty($value) || $value == 0 ) return false;
		$value = $aryValue[NAME_SUBTOTAL_PRICE];
		if( is_null($value) || empty($value) || $value == 0 ) return false;
		$value = $aryValue[NAME_PRODUCT_QUANTITY];
		if( is_null($value) || empty($value) || $value == 0 ) return false;

//fncDebug( 'fncCheckCalculateValue.txt', $rate."::".$aryValue[NAME_SUBTOTAL_PRICE]."::".$aryValue[NAME_PRODUCT_QUANTITY], __FILE__, __LINE__, 'a');

		return true;
	}

	//@------------------------------------------------------------------------
	/**
	*	概要	: 小数点フォーマット
	*
	*
	*	解説	: 小数点指定桁数切り捨て処理
	*
	*
	*	@param	[$strData]	: [String]	. フォーマット対象値
	*	@param	[$lngDigit]	: [Integer]	. 小数点桁数
	*
	*	@return	[$strBuff]	: [String]
	*/
	//-------------------------------------------------------------------------
	function fncDecimalFormat( $strData, $lngDigit )
	{
		$strBuff	= $strData;



		// 小数点が存在しない場合、小数点付加
		if( !strpos( $strBuff, "." ) ) $strBuff	= $strBuff . ".0000";

		$aryBuff			= explode( ".", $strBuff );
		$lngDecimalDigit	= mb_strlen( $aryBuff[1] );

		// 小数点桁数がn桁以上の場合、n桁目以降切り捨て
		if( $lngDecimalDigit > $lngDigit )
		{
			$lngDiffDigit	= $lngDecimalDigit - $lngDigit;
			$aryBuff[1]		= substr_replace( $aryBuff[1], "", $lngDigit, $lngDiffDigit );
			$strBuff		= implode( ".", $aryBuff );
		}


/*
		// 小数点桁数がn桁以上の場合、n桁目で丸める
		if( $lngDecimalDigit > $lngDigit )
		{
			$strBuff	= round( $strBuff, $lngDigit );
		}
*/

		unset( $aryBuff );

		return $strBuff;
	}





	//@------------------------------------------------------------------------
	/**
	*	概要	: 明細行無効データ消去処理
	*
	*
	*	解説	: 明細行データで計画原価が存在しない、または「0」の場合
	*			  配列から消去
	*
	*
	*	@param	[$objMap]	: [Object]	. Excel Mapping Object
	*	@param	[$ary]		: [Array]	. 取得したワークシートデータ配列
	*
	*	@return	[$aryData]	: [Array]	. ワークシートデータ配列
	*/
	//-------------------------------------------------------------------------
	function fncDeleteInvalidDetailData( $objMap, $ary )
	{
		$aryData	= array();
		$lngCnt		= 0;		// カウンタ
		$bytDetail	= 0;		// 明細行判定フラグ
		$bytCheck	= 0;		// 明細行無効データフラグ


		for( $i = 0; $i < count($ary); $i++ )
		{
			// 明細行判定
			// 通常
			// 売上分類、仕入科目 をチェック。どちらも該当しない場合 0
			if( !$objMap->fncCheckDetail( $ary[$i][COL_STOCK_SUBJECT_CODE], PROC_STOCK ) && !$objMap->fncCheckDetail( $ary[$i][COL_SALES_DIVISION_CODE], PROC_SALES ) )
			{
				$bytDetail	= 0;
			}
			// 明細行
			else
			{
				$bytDetail	= 1;
			}

//fncDebug( 'upload_parse_parse_Data2.1.txt', $bytDetail, __FILE__, __LINE__, 'a');
//fncDebug( 'upload_parse_parse_Data2.1.txt', $ary[$i], __FILE__, __LINE__, 'a');

			// 通常
			if( !$bytDetail )
			{
				// データ再設定
				while( list( $index, $value ) = each( $ary[$i] ) )
				{
					$aryData[$lngCnt][$index]	= $value;
				}

				// 数量(E列)および計画原価判定(H列) が0、表示行として加えないようにする
				// かつ成形前データタイプが計算式の場合、
				//  $aryData[$lngCnt][COL_SUBTOTAL_PRICE] == 0
				if( ( $aryData[$lngCnt]['BeforeType'][COL_SUBTOTAL_PRICE] == Type_FORMULA2 ||
						$aryData[$lngCnt]['BeforeType'][COL_SUBTOTAL_PRICE] == Type_FORMULA ) &&
					$aryData[$lngCnt][COL_PRODUCT_QUANTITY] == 0 &&
					$aryData[$lngCnt]['BeforeType'][COL_PRODUCT_PRICE] != Type_LABELSST
				)
				{
					continue;
				}
				
				
				
				// カウントアップ
				$lngCnt++;
				continue;
			}


			// 仕入科目が「1224:チャージ」「1230:経費」の場合、Continue
			if( $ary[$i][COL_STOCK_SUBJECT_CODE] == STOCK_1224 || $ary[$i][COL_STOCK_SUBJECT_CODE] == STOCK_1230 )
			{
				// データ再設定
				while( list( $index, $value ) = each( $ary[$i] ) )
				{
					$aryData[$lngCnt][$index]	= $value;
				}

				// 計画原価(H列) が0、単価(G列) が文字列以外、表示行として加えないようにする
				if( ( $aryData[$lngCnt]['BeforeType'][COL_SUBTOTAL_PRICE] == Type_FORMULA2 ||
						$aryData[$lngCnt]['BeforeType'][COL_SUBTOTAL_PRICE] == Type_FORMULA ) &&
					$aryData[$lngCnt][COL_SUBTOTAL_PRICE] == 0 &&
					( $aryData[$lngCnt]['BeforeType'][COL_PRODUCT_PRICE] != Type_LABELSST)
					)
				{
					continue;
				}

				// カウントアップ
				$lngCnt++;
				continue;
			}

			// 計画原価チェック
			if( is_null($ary[$i][COL_SUBTOTAL_PRICE]) ||
				empty($ary[$i][COL_SUBTOTAL_PRICE]) ||
				$ary[$i][COL_SUBTOTAL_PRICE] == 0 )
			{
				$bytCheck	= 1;
			}
			else
			{
				$bytCheck	= 0;
			}
//fncDebug( 'upload_parse_parse_Data_implode.txt', implode("", $ary[$i]), __FILE__, __LINE__, 'a');

			// 無効データの場合、Continue
			if( $bytCheck ) continue;

			if( trim(implode("", $ary[$i])) == "" )
			{
				continue;
			}


			// データ再設定
			while( list( $index, $value ) = each( $ary[$i] ) )
			{
				$aryData[$lngCnt][$index]	= $value;
			}

			// カウントアップ
			$lngCnt++;
		}


		return $aryData;
	}





	//@------------------------------------------------------------------------
	/**
	*	概要	: 終了キー判定
	*
	*
	*	解説	: ワークシートCELL値が終了キーかどうか判定
	*
	*
	*	@param	[$data]	: [String]	. ワークシートCELL値
	*
	*	@return	: [Boolean]
	*/
	//-------------------------------------------------------------------------
	function fncCheckEOFKey( $data )
	{
		if( $data != PARSE_EOF ) return false;

		return true;
	}

	//@------------------------------------------------------------------------
	/**
	*	概要	: パース終了判定
	*
	*
	*	解説	: パース終了判定が有効で且つ終了列の場合、処理終了->TRUE
	*
	*
	*	@param	[$blnEOF]	: [Boolean]	. 終了判定フラグ
	*	@param	[$col]		: [String]	. ワークシート列文字列
	*
	*	@return	: [Boolean]
	*/
	//-------------------------------------------------------------------------
	function fncCheckFinish( $blnEOF, $col )
	{
		if( !($blnEOF && $col == COL_EOF) ) return false;

		return true;
	}

?>
