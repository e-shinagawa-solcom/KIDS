<?php

/**
*	クラス概要	: マッピング
*
*
*	@charset	: utf-8
*/



	class clsMapping
	{
		var $aryMapping			= array();	// マッピング配列
		var $aryUnion			= array();	// 共有配列
		var $strProcessMode		= "";		// 処理判定文字列
		var $blnEOF				= false;	// マッピング処理終了フラグ



		/**
			constructor
		*/
		function clsMapping()
		{
		}
		/**
			destructor
		*/
		function destruct()
		{
			unset( $this->aryMapping, $this->aryUnion );
			unset( $this->strProcessMode, $this->blnEOF );
			return true;
		}



		/**
			setter マッピング配列

			@param	$ary	[Array]	: マッピング配列
		*/
		function setArrayMap( $ary )
		{
			$this->aryMapping	= $ary;

			unset( $ary );
			return true;
		}
		/**
			getter マッピング配列

			@return	$this->aryMapping
		*/
		function getArrayMap()
		{
			return $this->aryMapping;
		}



		/**
			setter 共有配列

			@param	$strHash	[String]	: ハッシュ名
			@param	$data		[Object]	: 値
		*/
		function setArrayUnion( $strHash, $data )
		{
			$this->aryUnion[$strHash]	= $data;

			return true;
		}
		/**
			getter 共有配列

			@return	$this->aryUnion
		*/
		function getArrayUnion()
		{
			return $this->aryUnion;
		}

		/**
			getter 共有値

			@param	$strHash	[String]	: ハッシュ名

			@return	$this->aryUnion[$strHash]
		*/
		function getUnionValue( $strHash )
		{
			return $this->aryUnion[$strHash];
		}



		/**
			setter 処理モード

			@param	$strProc	[String]	: 処理モード文字列
		*/
		function setProcessMode( $strProc )
		{
			$this->strProcessMode	= $strProc;

			unset( $strProc );
			return true;
		}
		/**
			getter 処理モード

			@return	$this->strProcessMode
		*/
		function getProcessMode()
		{
			return $this->strProcessMode;
		}



		/**
			setter マッピング処理終了フラグ

			@param	$blnEOF	[Boolean]	: Bool
		*/
		function setEOF( $blnEOF )
		{
			$this->blnEOF	= $blnEOF;

			unset( $blnEOF );
			return true;
		}
		/**
			getter マッピング処理終了フラグ

			@return	$this->blnEOF
		*/
		function getEOF()
		{
			return $this->blnEOF;
		}





		/**
			マッピング処理


			マッピング処理後、name/value値を返却

			@param	$row		[Integer]	: 行番号
			@param	$col		[String]	: 列文字列
			@param	$data		[Object]	: Cell値

			@return	$strHidden	[String]
		*/
		function getMappingData( $row, $col, $data )
		{
			$aryRetVal		= array();	// 返却用配列
			$strTargetKey	= "";		// データ抽出対象・行/列
			$aryData		= array();	// データ配列
			$aryBuffMap		= array();	// マッピング配列バッファ
			$aryBuffUnion	= array();	// 共有配列バッファ
			$strJumpKey		= "";		// ジャンプキー
			$strBuffProc	= "";		// 処理モードバッファ
			$blnBuffEOF		= false;	// マッピング処理終了判定フラグバッファ
			
			if (is_array($data)) return false;

			$data			= trim( $data );	// データのトリム

			$this->setArrayUnion( ROW, $row );	// ワークシート行番号設定
			$this->setArrayUnion( COL, $col );	// ワークシート列文字列設定
			$strTargetKey	= $row . $col;		// データ抽出対象Key取得



			// マッピング処理終了判定
			$this->fncCheckEOF( $data );

			// マッピング処理終了フラグの取得
			$blnBuffEOF	= $this->getEOF();

			// マッピング処理終了
			if( $blnBuffEOF ) return false;



			// マッピング配列の取得
			$aryBuffMap		= $this->getArrayMap();

			// 共有配列の取得
			$aryBuffUnion	= $this->getArrayUnion();

			// 処理モードの取得
			$strBuffProc	= $this->getProcessMode();

			// Excel換算レート取得
			if( $strTargetKey == ROWCOL_CONVERSION_RATE ) $this->setArrayUnion( NAME_CONVERSION_RATE, $data );


require_once ( LIB_DEBUGFILE );
fncDebug( 'clsMapping_02.txt', $aryBuffMap, __FILE__, __LINE__, 'a');

			// 通常処理モード
			switch( $strBuffProc )
			{
				// 通常
				case PROC_DIFF:

					// マッピング対象外の場合、処理終了
					if( is_null($aryBuffMap[$strBuffProc][$strTargetKey]) || empty($aryBuffMap[$strBuffProc][$strTargetKey]) )
					{
						return false;
					}

					// データ型取得
					$aryData	= $this->getDataType( $aryBuffMap[$strBuffProc][$strTargetKey] );

					// Cell値データ変換
					$data	= $this->fncChangeDataType( $aryData["type"], $data, $col );
					break;


				// 通常
				case PROC_NORMAL:

					// マッピング対象外の場合、処理終了
					if( is_null($aryBuffMap[$strBuffProc][$strTargetKey]) || empty($aryBuffMap[$strBuffProc][$strTargetKey]) )
					{
						return false;
					}

					// データ型取得
					$aryData	= $this->getDataType( $aryBuffMap[$strBuffProc][$strTargetKey] );

					// Cell値データ変換
					$data	= $this->fncChangeDataType( $aryData["type"], $data, $col );


					// 生産予定数補完取得
					if( $strTargetKey == ROWCOL_PRODUCTION_QUANTITY ) $this->setArrayUnion( NAME_PRODUCTION_QUANTITY_HIDDEN, $data );

					// 納価補完取得
					if( $strTargetKey == ROWCOL_PRODUCT_PRICE ) $this->setArrayUnion( NAME_PRODUCT_PRICE_HIDDEN, $data );
					break;


				// 明細行（売上分類・区分）
				case PROC_DETAIL01:

fncDebug( 'clsMapping_05.txt', $strBuffProc . ">" .$aryBuffMap[$strBuffProc][$col] , __FILE__, __LINE__, 'a');

					// マッピング対象外の場合、処理終了
					if( is_null($aryBuffMap[$strBuffProc][$col]) || empty($aryBuffMap[$strBuffProc][$col]) )
					{
						return false;
					}

					// データ型取得
					$aryData	= $this->getDataType( $aryBuffMap[$strBuffProc][$col] );

					// Cell値データ変換
					$data	= $this->fncChangeDataType( $aryData["type"], $data, $col );

					// 数量 取得
					if( $col == COL_PRODUCT_QUANTITY ) $this->setArrayUnion( NAME_PRODUCT_QUANTITY, $data );

					// 単価 取得
					if( $col == COL_PRODUCT_PRICE ) $this->setArrayUnion( NAME_PRODUCT_PRICE, $data );


					// 計画原価取得
					if( $col == COL_SUBTOTAL_PRICE )
					{
fncDebug( 'clsMapping_sub.txt', "0:".$data, __FILE__, __LINE__, 'a');

						// 計画個数及び単価が存在しない場合
						if( ( is_null($aryBuffUnion[NAME_PRODUCT_PRICE]) || empty($aryBuffUnion[NAME_PRODUCT_PRICE]) ) ||
							( is_null($aryBuffUnion[NAME_PRODUCT_QUANTITY]) || empty($aryBuffUnion[NAME_PRODUCT_QUANTITY]) ) )
						{
							// 計画原価
							$data	= $data;
fncDebug( 'clsMapping_sub.txt', "1:".$data, __FILE__, __LINE__, 'a');
						}
						// 計画個数及び単価が存在する場合
						else
						{
							// 計画原価 = 単価 x 計画個数
							$data	= $aryBuffUnion[NAME_PRODUCT_PRICE] * $aryBuffUnion[NAME_PRODUCT_QUANTITY];
fncDebug( 'clsMapping_sub.txt', "2:".$aryBuffUnion[NAME_PRODUCT_PRICE].":".$aryBuffUnion[NAME_PRODUCT_QUANTITY], __FILE__, __LINE__, 'a');
fncDebug( 'clsMapping_sub.txt', "2:".$data, __FILE__, __LINE__, 'a');
						}

						// 計画原価設定
						$this->setArrayUnion( NAME_SUBTOTAL_PRICE, $data );
					}


					break;


				// 明細行（仕入区分）
				case PROC_DETAIL:

					// マッピング対象外の場合、処理終了
					if( is_null($aryBuffMap[$strBuffProc][$col]) || empty($aryBuffMap[$strBuffProc][$col]) )
					{
						return false;
					}

					// データ型取得
					$aryData	= $this->getDataType( $aryBuffMap[$strBuffProc][$col] );

					// Cell値データ変換
					$data	= $this->fncChangeDataType( $aryData["type"], $data, $col );


					// 計画個数取得
					if( $col == COL_PRODUCT_QUANTITY ) $this->setArrayUnion( NAME_PRODUCT_QUANTITY, $data );

					// 単価取得
					if( $col == COL_PRODUCT_PRICE ) $this->setArrayUnion( NAME_PRODUCT_PRICE, $data );

					// 計画原価取得
					if( $col == COL_SUBTOTAL_PRICE )
					{
						// 計画個数及び単価が存在しない場合
						if( ( is_null($aryBuffUnion[NAME_PRODUCT_PRICE]) || empty($aryBuffUnion[NAME_PRODUCT_PRICE]) ) ||
							( is_null($aryBuffUnion[NAME_PRODUCT_QUANTITY]) || empty($aryBuffUnion[NAME_PRODUCT_QUANTITY]) ) )
						{
							// 計画原価
							$data	= $data;
						}
						// 計画個数及び単価が存在する場合
						else
						{
							// 計画原価 = 単価 x 計画個数
							$data	= $aryBuffUnion[NAME_PRODUCT_PRICE] * $aryBuffUnion[NAME_PRODUCT_QUANTITY];
						}

						// 計画原価設定
						$this->setArrayUnion( NAME_SUBTOTAL_PRICE, $data );
					}
					break;


				// 小計・合計他
				case PROC_JUMP:

					// ジャンプキー取得
					$strJumpKey	= $aryBuffUnion[NAME_JUMP_KEY];

					// マッピング対象外の場合、処理終了
					if( is_null($aryBuffMap[$strJumpKey][$col]) || empty($aryBuffMap[$strJumpKey][$col]) )
					{
						return false;
					}

					// データ型取得
					$aryData	= $this->getDataType( $aryBuffMap[$strJumpKey][$col] );

					// Cell値データ変換
					$data	= $this->fncChangeDataType( $aryData["type"], $data, $col );


					// 共有配列に設定
					$this->setArrayUnion( $strJumpKey, $data );

					// ジャンプフラグの設定
					$this->setArrayUnion( NAME_JUMP_FLAG, false );
					break;

				default:
					break;
			}

			// 返却用配列生成
			$aryRetVal	= Array( "name" => $aryData["name"], "value" => $data );

fncDebug( 'clsMapping_04.txt', $aryRetVal, __FILE__, __LINE__, 'a');

			unset( $strTargetKey, $aryData, $aryBuffMap, $strBuffProc );
			return $aryRetVal;
		}





		/**
			データ型取得

			@param	$data	[Object]	: Cell値

			@return	$aryData	[Array]
		*/
		function getDataType( $data )
		{
			$aryData	= array();
			$aryData	= explode( SPLIT_TYPE_CHAR, $data );

			$aryData["name"]	= $aryData[0];
			$aryData["type"]	= ( !is_null($aryData[1]) ) ? $aryData[1] : false;


			return $aryData;
		}

		/**
			Cell値データ変換、返却

			@param	$type	[String]	: 処理モード文字列
			@param	$data	[Object]	: Cell値
			@param	$col	[String]	: ワークシート列文字列

			@return	$aryData	[Object]
		*/
		function fncChangeDataType( $type, $data, $col )
		{
			$retVal;
			$aryBuff	= array();

			// 変換の必要が無い場合、そのまま返却
			if( !$type ) return $data;

			// データ変換
			switch( $type )
			{
				// コード
				case "code":
					// コード・名称を分割
					$retVal	= $this->fncSplitData( $data, "code" );
					break;

				// コード・名称
				case "code_name":
					// コード・名称を分割
					$retVal	= $this->fncSplitData( $data, "code" );

					// 担当者名称設定
					$this->setArrayUnion( NAME_USER_DISPLAY_NAME, $this->fncSplitData($data, "name") );
					break;

				// コード <-> %
				case "code_percent":
					// 計画率（%フラグ）
					if( $this->fncCheckPercentFlag( $data, $col ) )
					{
						$retVal	= "";

						// 計画率設定（「%」を取り除く）
						$this->setArrayUnion( NAME_PRODUCT_RATE, (str_replace(CELL_PERCENT, "", $data))/100 );
					}
					else
					{
						// コード・名称を分割
						$retVal	= $this->fncSplitData( $data, "code" );

						// 仕入先名称設定
						$this->setArrayUnion( NAME_COMPANY_DISPLAY_NAME, $this->fncSplitData($data, "name") );
					}
					break;

				// %
				case "percent":
					// 「%」を取り除く
					$retVal	= str_replace(CELL_PERCENT, "", $data);
					break;

				case "date":
					$retVal	= $this->fncCheckNull( $data );
					break;

				case "float":
					$retVal	= $this->fncCheckNull( $data );
					break;

				case "bool":
					$retVal	= ( $data == CELL_BOOL ) ? CONV_BOOL_T : CONV_BOOL_F;
					break;

				case "money":
					switch( $data )
					{
						// 日本円
						case CELL_MONETARY_JP:
							$retVal	= CONV_MONETARY_JP;
							break;
						// USドル
						case CELL_MONETARY_US:
							$retVal	= CONV_MONETARY_US;
							break;
						// HKDドル
						case CELL_MONETARY_HKD:
							$retVal	= CONV_MONETARY_HKD;
							break;

						// 指定の無い場合、日本円
						default:
							$retVal	= CONV_MONETARY_JP;
							break;
					}

					// 通貨コードを設定
					$this->setArrayUnion( NAME_MONETARY_UNIT_CODE, $retVal );
					break;

				default:
					$retVal	= $this->fncCheckNull( $data );
					break;
			}

			return $retVal;
		}

		/**
			Cell値コード・名称を分割、抽出、返却

			@param	$data	[String]	: Cell値
			@param	$type	[Object]	: 抽出モード文字列

			@return	$retVal	[Object]
		*/
		function fncSplitData( $data, $type )
		{
			$retVal;
			$aryData	= array();
			$aryData	= explode( SPLIT_CODE_CHAR, $data );

			switch( $type )
			{
				case "code":
					$retVal	= $aryData[0];
					break;

				case "name":
					$retVal	= $aryData[1];
					break;

				default:
					break;
			}

			unset( $aryData );
			return $retVal;
		}


		/**
			※未使用・補完

			通貨コードによる換算レート取得

			通貨コードによる換算レートを返却

			@return	$lngRate	[Integer]
		*/
		function getConversionRate()
		{
			$lngRate	= 0;
			$aryBuff	= array();
			$aryBuff	= $this->getArrayUnion();	// 共有配列

			switch( $aryBuff[NAME_MONETARY_UNIT_CODE] )
			{
				case CONV_MONETARY_JP:
					$lngRate	= JP_RATE;
					break;

				case CONV_MONETARY_US:
					$lngRate	= $aryBuff[NAME_CONVERSION_RATE];	// Excelレート
					break;

				case CONV_MONETARY_HKD:
					$lngRate	= $aryBuff[NAME_CONVERSION_RATE];	// Excelレート
					break;

				default:
					break;
			}

			return $lngRate;
		}


		/**
			通貨コード取得

			@return	$aryBuff[NAME_MONETARY_UNIT_CODE	[String]
		*/
		function getMonetaryUnitCode()
		{
			$lngRate	= 0;
			$aryBuff	= array();
			$aryBuff	= $this->getArrayUnion();	// 共有配列

			return $aryBuff[NAME_MONETARY_UNIT_CODE];
		}


		/**
			※未使用・補完

			日本円計画原価小計取得

			日本円計画原価小計 = 計画原価 x 通貨コードによる換算レート

			@return	$lngPrice	[Integer]
		*/
		function getSubTotalPriceJP()
		{
			$lngPrice	= 0;
			$aryBuff	= array();
			$aryBuff	= $this->getArrayUnion();

			// 日本円計画原価小計 = 計画原価 x 通貨コードによる換算レート
			$lngPrice	= $aryBuff[NAME_SUBTOTAL_PRICE_JP] * $this->getConversionRate();

			return $lngPrice;
		}


		/**
			計画率（%フラグ）判定

			Cell値に「%」が含まれているか判定

			@param	[$data]	: [String]	. Cell値（参照渡し）
			@param	[$col]	: [String]	. ワークシート列文字列

			@return	$blnCheck	[Boolean]
		*/
		function fncCheckPercentFlag( $data, $col )
		{
			$blnCheck	= false;

			// ワークシート列が COL_PERCENT_INPUT 以外は処理終了
			if( $col != COL_PERCENT_INPUT ) return false;

			// 「%」が含まれていない場合、処理終了
			if( !strpos($data, CELL_PERCENT) )
			{
				// 計画率判定フラグの設定
				$this->setArrayUnion( NAME_PERCENT_INPUT_FLAG, false );
				return false;
			}

			// 計画率判定フラグの設定
			$this->setArrayUnion( NAME_PERCENT_INPUT_FLAG, true );

			return true;
		}


		/**
			明細行（仕入科目）判定

			@param	$data		[String]	: Cell値
			@param	$strProc	[Object]	: 処理モード文字列

			@return	$blnCheck	[Boolean]
		*/
		function fncCheckDetail( $data, $strProc )
		{
			$i;
			$aryBuff	= array();
			$blnCheck	= false;

			// マッピング配列の取得
			$aryBuff	= $this->getArrayMap();


			// Cell値が空欄の場合、処理終了
			if( empty($data) || is_null($data) ) return false;

			// 'BeforeType'等の配列の場合
			if( is_array($data) ) return false;

			// マッピング配列が存在する場合
			if( !is_null($aryBuff[$strProc][$data]) ) $blnCheck = true;

			unset( $aryBuff );
			return $blnCheck;
		}


		/**
			明細行処理終了判定

			@param	$col	[String]	: 列文字値

			@return	$blnCheck	[Boolean]
		*/
		function fncCheckFinishDetail( $col )
		{
			$blnCheck	= false;

			// 明細行が処理終了列の場合
			if( $col == COL_FINISH_DETAIL )
			{
				$blnCheck	= true;
			}

			return $blnCheck;
		}


		/**
			小計・合計他判定

			@param	$data		[String]	: Cell値
			@param	$strProc	[Object]	: 処理モード文字列

			@return	$blnCheck	[Boolean]
		*/
		function fncCheckJump( $data, $strProc )
		{
			$i;
			$aryBuff	= array();
			$blnCheck	= false;

			// マッピング配列の取得
			$aryBuff	= $this->getArrayMap();

			// Cell値が空欄の場合、処理終了
			if( empty($data) || is_null($data) || is_array($data)) return false;


			while( list($index, $value) = each($aryBuff[$strProc]) )
			{
				// Cell値（$data）に $value が含まれていない場合
				if( !strpos($data, $value) )
				{
					$blnCheck	= false;
					continue;
				}

				$blnCheck	= true;

				// ジャンプフラグの設定
				$this->setArrayUnion( NAME_JUMP_FLAG, true );

				// ジャンプキーに設定
				$this->setArrayUnion( NAME_JUMP_KEY, $value );

				break;
			}


			unset( $aryBuff );
			return $blnCheck;
		}


		/**
			NULL埋め

			@param	$data	[Object]	: Cell値
		*/
		function fncCheckNull( $data )
		{
			$retVal;

			$retVal	= ( is_null($data) || empty($data) ) ? "" : $data;

			return $retVal;
		}


		/**
			マッピング処理終了判定

			@param	$data	[String]	: Cell値
		*/
		function fncCheckEOF( $data )
		{
			$blnCheck = false;

			// マッピング処理終了フラグの設定
			if( $data == PARSE_EOF ) $this->setEOF( true );

			return true;
		}

	}

?>
