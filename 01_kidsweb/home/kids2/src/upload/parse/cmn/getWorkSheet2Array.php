<?php

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
		require_once ( LIB_DEBUGFILE );


		$aryData				= array();	// データ配列
		$blnEOF					= false;	// パース終了フラグ
		$strBuffCol				= "";		// 列文字列バッファ
		$lngBuffRow				= 0;		// 行番号バッファ
		$curBuffConversionRate	= 0;		// 換算レートバッファ


		// ワークシートデータ取得
		$ws	= $exc->worksheet['data'][$ws_num];


		// データが取得出来ない場合
		if( !is_array($ws) || !isset($ws['max_row']) || !isset($ws['max_col']) )
		{
			// emtpty worksheet
			return false;
		}


		// データ解析開始
		for( $i=0; $i<=$ws['max_row']; $i++ )
		{
			// 行番号取得
			$lngBuffRow = ($i+1);

			if( !isset($ws['cell'][$i]) || !is_array($ws['cell'][$i]) )
			{
				// print an empty row
				for( $j=0; $j<=$ws['max_col']; $j++ )
				{
					// 列文字列取得
					if( $j>25 ) $strBuffCol = chr((int)($j/26)+64);
					$strBuffCol = chr(($j % 26) + 65);

					// 列終端判定
					if( $j < COL_EOF_INT )
					{
						$aryData[$i][$strBuffCol]	= "";
					}
				}

				continue;
			}


			for( $j=0; $j<=$ws['max_col']; $j++ )
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

				if( ( !is_array($ws['cell'][$i]) ) || ( !isset($ws['cell'][$i][$j]) ) )
				{
					$aryData[$i][$strBuffCol]	= "";
					continue;
				}


				$data = $ws['cell'][$i][$j];

				switch( $data['type'] )
				{
					// string
					case 0:
						$ind	= $data['data'];

						if( $exc->sst['unicode'][$ind] )
						{
							$s	= uc2html($exc->sst['data'][$ind]);
						}
						else
						{
							$s	= $exc->sst['data'][$ind];
						}

						if( strlen(trim($s)) == 0 )
						{
							$aryData[$i][$strBuffCol]	= "";
						}
						else
						{
							// 終了キー判定
							if( fncCheckEOFKey( $s ) )
							{
								$aryData[$i][$strBuffCol]	= "";
								$blnEOF	= true;
							}
							else
							{
								$aryData[$i][$strBuffCol]	= $s;
							}
						}
						break;

					// integer number
					case 1:

					// float number
					case 2:
						//echo $data['data'];

					// date
					case 3:
						//$ret = $data[data];
						//str_replace ( " 00:00:00", "", gmdate("d-m-Y H:i:s",$exc->xls2tstamp($data[data])) );

					case 4: //string
						$aryData[$i][$strBuffCol]	= $data['data'];

						// 換算レート取得
						if( (string)($lngBuffRow.$strBuffCol) == ROWCOL_CONVERSION_RATE )
						{
							$curBuffConversionRate	= $data['data'];
// fncDebug( 'upload_parse_cmn_getWorkSheet2Array.txt', $lngBuffRow.$strBuffCol.'>>'.$data, __FILE__, __LINE__, 'a');
						}

						break;

					case 5: //hlink
/*
						$strHTML	.= "<a href=\"";
						$strHTML	.= uc2html($data['hlink']);
						$strHTML	.= "\">";
						$strHTML	.= uc2html($data['data']);
						$strHTML	.= "</a>";
*/
						break;

					default:
						break;
				}

			}
			// end for loop

		}
		// end for loop



		// 明細行「単価」再計算
		for( $i = 0; $i < count($aryData); $i++ )
		{
			// 再計算処理が必要ではない場合、Continue
			if( !getProductPrice( $objMap, $aryData, $i, $curBuffConversionRate ) ) continue;

			// 単価取得（再計算）
			$aryData[$i][COL_PRODUCT_PRICE]	= getProductPrice( $objMap, $aryData, $i, $curBuffConversionRate );
		}


		// 明細行無効データ消去
		$aryData	= fncDeleteInvalidDetailData( $objMap, $aryData );

//fncDebug( 'exc_array.txt', $aryData, __FILE__, __LINE__);

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
		$aryValue[NAME_SUBTOTAL_PRICE]		= $ary[$row][COL_SUBTOTAL_PRICE];	// 計画原価
		$aryValue[NAME_PRODUCT_QUANTITY]	= $ary[$row][COL_PRODUCT_QUANTITY];	// 計画個数

//fncDebug( 'getWorkSheet2Array_02.txt', '[row]>'.$row.'[data]>'.COL_SUBTOTAL_PRICE.'>>>'.$ary[$row][COL_SUBTOTAL_PRICE], __FILE__, __LINE__, 'a');
//fncDebug( 'getWorkSheet2Array_02.txt', $aryValue, __FILE__, __LINE__, 'a');


		// 計算対象外値の場合、処理終了
		if( !fncCheckCalculateValue($aryValue) ) return false;


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
	function fncCheckCalculateValue( $aryValue )
	{
		while( list($index, $value) = each($aryValue) )
		{
			// 数値が存在しないまたは、「0」の場合、処理終了
			if( is_null($value) || empty($value) || $value == 0 ) return false;
		}

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


			// 通常
			if( !$bytDetail )
			{
				// データ再設定
				while( list( $index, $value ) = each( $ary[$i] ) )
				{
					$aryData[$lngCnt][$index]	= $value;
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

			// 無効データの場合、Continue
			if( $bytCheck ) continue;


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
