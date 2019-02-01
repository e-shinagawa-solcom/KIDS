<?php
/* ************************************************************************* */
/* UPLOAD Powered by Peruser */
/* ************************************************************************* */

/**
*
*	@charset	: utf-8
*/



	//@------------------------------------------------------------------------
	/**
	*	概要	: Excelファイルパース処理
	*
	*
	*	解説	: アップロードされたExcelファイル（バイナリ）をパースする
	*
	*	対象	: 結果用テンプレート
	*
	*	@param	[$exc]		: [Object]	. ExcelParser Object（参照渡し）
	*	@param	[$aryData]	: [Array]	. $_REQUEST より取得した値
	*
	*	@return	[$aryErr]	: [Array]	. エラーチェック配列
	*/
	//-------------------------------------------------------------------------
	function fncParseExcelFile( &$exc, $aryData )
	{
		$aryErr	= array();	// エラーチェック配列


		// アプリケーションタイプのチェック
		if( strtolower($aryData['exc_type']) != APP_EXCEL_TYPE ) return false;

		// ファイル名空欄チェック
		if( empty($aryData['exc_name']) || is_null($aryData['exc_name']) ) return false;

		// テンポラリファイルの取得
		$excel_file	= FILE_UPLOAD_TMPDIR . $aryData["exc_tmp_name"];

		// パース処理モードの取得
		$exc_style = $aryData['style'];


		// パース処理
		switch( $exc_style )
		{
			// Excel バイナリ
			case 'segment':
//				$time_start	= getmicrotime();
				$objResult	= $exc->fileread( $excel_file );
//				$time_end	= getmicrotime();
				break;

			default:
				break;
		}

		/* エラー処理 */
		if ($exc->isError($objResult))
		{
			$errmes	=	$objResult->getMessage();

			if (strpos($errmes, $aryData["exc_tmp_name"])!==false)
				$errmes=str_replace($aryData["exc_tmp_name"], $aryData['exc_name'], $errmes);
				$errmes=str_replace('Template file', 'Uploaded file', $errmes);
				var_dump($errmes);
				exit;
		}
		else
		{
			$aryErr["bytError"]	= 0;
		}


		return $aryErr;

/*
		// エラーチェック
		switch( $lngRes )
		{
			case 0:
				$aryErr["bytError"]	= 0;
				break;

			case 1:
				$aryErr["bytError"]	= 1;
				$aryErr["strError"]	= "Can't open file";
			case 2:
				$aryErr["bytError"]	= 1;
				$aryErr["strError"]	= "File too small to be an Excel file";
			case 3:
				$aryErr["bytError"]	= 1;
				$aryErr["strError"]	= "Error reading file header";
			case 4:
				$aryErr["bytError"]	= 1;
				$aryErr["strError"]	= "Error reading file";
			case 5:
				$aryErr["bytError"]	= 1;
				$aryErr["strError"]	= "This is not an Excel file or file stored in Excel < 5.0";
			case 6:
				$aryErr["bytError"]	= 1;
				$aryErr["strError"]	= "File corrupted";
			case 7:
				$aryErr["bytError"]	= 1;
				$aryErr["strError"]	= "No Excel data found in file";
			case 8:
				$aryErr["bytError"]	= 1;
				$aryErr["strError"]	= "Unsupported file version";

			default:
				$aryErr["bytError"]	= 1;
				$aryErr["strError"]	= "Unknown error";
		}


		return $aryErr;
*/

	}



	//@------------------------------------------------------------------------
	/**
	*	概要	: 処理選択HTMLスクリプト返却
	*
	*
	*	解説	: 「選択・登録・修正・削除」各処理ボタンHTML生成、返却
	*
	*	対象	: 結果用テンプレート
	*
	*	@param	[$ws_num]	: [Integer]	. 選択したExcelワークシート番号
	*	@param	[$strMode]	: [String]	. ワークシート選択・確認画面判定文字列
	*
	*	@return	[$strHTML]	: [String]
	*/
	//-------------------------------------------------------------------------
	function getFileConfirmScript2HTML( $ws_num, $strMode )
	{
		$aryHTML	=	array();
		$strHTML	= "";

		$aryHTML[]	= "<div align=\"right\">\n";

		switch( $strMode )
		{
			// 選択画面
			case "select":
				$aryHTML[]	= "\t<button onclick=\"fncFileSelect( window." .FORM_NAME.", " .$ws_num. " ); return false;\"> 選択 </button>&nbsp;\n";
				$aryHTML[]	= "\t<button onclick=\"classshow('rowstyle".$ws_num."');\"> 情報表示 </button>&nbsp;&nbsp;&nbsp;\n";
				$aryHTML[]	= "\t<button onclick=\"window.close();\"> 閉じる </button>&nbsp;&nbsp;&nbsp;\n";
				$aryHTML[]	= "\t<a id=\"excHref\" href=\"#top\"><b>↑Page Top</b></a>&nbsp;&nbsp;\n";
				break;

			// 確認画面
			case "confirm":
				$aryHTML[]	= "\t<button onclick=\"fncFileProcess( window." .FORM_NAME. ", 'confirm' ); return false;\"> 登録 </button>&nbsp;\n";
//				$aryHTML[]	= "\t<button onclick=\"fncFileProcess( window." .FORM_NAME. ", 'input' ); return false;\"> 編集 </button>&nbsp;\n";
				$aryHTML[]	= "\t<button onclick=\"window.close();\"> 閉じる </button>&nbsp;&nbsp;\n";
				break;

			default:
				break;
		}

		$aryHTML[]	= "</div>\n\n\n";


		$strHTML	= implode( "", $aryHTML );

		unset( $aryHTML );
		return $strHTML;
	}



	//@------------------------------------------------------------------------
	/**
	*	概要	: フォーム要素返却
	*
	*
	*	解説	: 各ワークシート選択用フォームオブジェクトを生成、返却
	*
	*	対象	: 結果用テンプレート
	*
	*	@param	[$exc]		: [Object]	. ExcelParser Object
	*	@param	[$ws_num]	: [Integer]	. 選択したExcelワークシート番号
	*	@param	[$aryData]	: [Array]	. $_REQUEST より取得した値
	*
	*	@return	[$strHTML]	: [String]
	*/
	//-------------------------------------------------------------------------
	function getForm( $exc, $ws_num, $aryData )
	{
		$aryHTML	= array();
		$strHTML	= "";

		$aryHTML[]	= getHiddenCommon( $ws_num, $aryData );	// 共通HIDDEN要素取得
		$strHTML	= implode( "", $aryHTML );


		unset( $aryHTML );
		return $strHTML;
	}



	//@------------------------------------------------------------------------
	/**
	*	概要	: 共通HIDDEN要素返却
	*
	*
	*	解説	: 各ワークシート毎にHIDDENオブジェクトを生成、返却
	*
	*	対象	: 結果用テンプレート
	*
	*	@param	[$ws_num]	: [Integer]	. 選択したExcelワークシート番号
	*	@param	[$aryData]	: [Array]	. $_REQUEST より取得した値
	*
	*	@return	[$strHTML]	: [String]
	*/
	//-------------------------------------------------------------------------
	function getHiddenCommon( $ws_num, $aryData )
	{
		$aryHTML	= array();
		$strHTML	= "";

		$aryHTML[]	= "\t<input type=\"hidden\" name=\"ActionScriptName\"		value=\"\" />\n";
		$aryHTML[]	= "\t<input type=\"hidden\" name=\"strSessionID\"			value=\"" .$aryData["strSessionID"]. "\" />\n";
		$aryHTML[]	= "\t<input type=\"hidden\" name=\"lngFunctionCode\"		value=\"" .$aryData["lngFunctionCode"]. "\" />\n";
		$aryHTML[]	= "\t<input type=\"hidden\" name=\"ESFlg\"					value=\"1\" />\n";
		$aryHTML[]	= "\t<input type=\"hidden\" name=\"lngEstimateNo\"			value=\"\" />\n";
		$aryHTML[]	= "\t<input type=\"hidden\" name=\"strProcess\"				value=\"\" />\n";
		$aryHTML[]	= "\t<input type=\"hidden\" name=\"strPageCondition\"		value=\"\" />\n";
		$aryHTML[]	= "\t<input type=\"hidden\" name=\"strActionName\"			value=\"\" />\n";
		$aryHTML[]	= "\t<input type=\"hidden\" name=\"lngRegistConfirm\"		value=\"\" />\n";
		$aryHTML[]	= "\t<input type=\"hidden\" name=\"strMode\"				value=\"\" />\n";
		$aryHTML[]	= "\t<input type=\"hidden\" name=\"RENEW\"					value=\"\" />\n\n";

		$aryHTML[]	= "\t<input type=\"hidden\" name=\"lngSelectSheetNo\"	value=\"" .$ws_num. "\" />\n";
		$aryHTML[]	= "\t<input type=\"hidden\" name=\"style\"				value=\"" .$aryData["style"]. "\" />\n";
		$aryHTML[]	= "\t<input type=\"hidden\" name=\"exc_name\"			value=\"" .$aryData["exc_name"]. "\" />\n";
		$aryHTML[]	= "\t<input type=\"hidden\" name=\"exc_type\"			value=\"" .$aryData["exc_type"]. "\" />\n";
		$aryHTML[]	= "\t<input type=\"hidden\" name=\"exc_tmp_name\"		value=\"" .$aryData["exc_tmp_name"]. "\" />\n";
		$aryHTML[]	= "\t<input type=\"hidden\" name=\"exc_error\"			value=\"" .$aryData["exc_error"]. "\" />\n";
		$aryHTML[]	= "\t<input type=\"hidden\" name=\"exc_size\"			value=\"" .$aryData["exc_size"]. "\" />\n";

		$strHTML	= implode( "", $aryHTML );


		unset( $aryHTML );
		return $strHTML;
	}



	//@------------------------------------------------------------------------
	/**
	*	概要	: ワークシートHIDDEN要素返却
	*
	*
	*	解説	: 各ワークシート毎にHIDDENオブジェクトを生成、返却
	*
	*	対象	: 結果用テンプレート
	*
	*	@param	[$objMap]	: [Object]	. Mapping Object
	*	@param	[$ary]		: [Array]	. name/value配列
	*
	*	@return	[$strHTML]	: [String]
	*/
	//-------------------------------------------------------------------------
	function getHiddenWorkSheet( $objMap, $ary )
	{

		require_once ( LIB_DEBUGFILE );

		global $arySystemRate;


		$strHTML		= "";
		$aryBuff		= array();
		$aryBuff		= $objMap->getArrayUnion();			// 共有配列
		$strProc		= $objMap->getProcessMode();		// 処理モード
		$strRowCol		= $aryBuff[ROW] . $aryBuff[COL];	// 行/列


		switch( $strProc )
		{
			// 通常
			case PROC_NORMAL:

				// 列が換算レートの場合
				if( $strRowCol == ROWCOL_CONVERSION_RATE )
				{
					// Systemレートを適用
					$ary["value"]	= $arySystemRate[NAME_SYSTEM_CONVERSION_RATE];
				}


				$strHTML	= "\t<input type=\"hidden\" name=\"" .$ary["name"]. "\" value=\"" .$ary["value"]. "\" />\n";


				// 行/列が終端列の場合
				if( $strRowCol == ROWCOL_LAST_HEADER )
				{
					// [追加]ヘッダーHIDDENを取得
					$strHTML	.= getAddHiddenWorkSheet( $objMap, $aryBuff );
				}
				break;

			// 明細行01（売上分類）
			case PROC_DETAIL01:
				
				// 列が売上区分の場合
				if( $aryBuff[COL] == COL_SALES_CLASS_CODE )
				{
					// 値が空欄の場合、「0」を設定
					$ary["value"]	= ( is_null($ary["value"]) || empty($ary["value"]) ) ? INIT_SALES_ITEM_CODE : $ary["value"];
				}

				// 列が顧客先の場合
				if( $aryBuff[COL] == COL_CUSTOMER_COMPANY_CODE )
				{
					// 値が空欄の場合、「0」を設定
					$ary["value"]	= ( is_null($ary["value"]) || empty($ary["value"]) ) ? INIT_COMPANY_CODE : $ary["value"];
				}

				// 列が数量の場合
				if( $aryBuff[COL] == COL_PRODUCT_QUANTITY )
				{
					// 値が空欄の場合、生産予定数を設定
					$ary["value"]	= ( is_null($ary["value"]) || empty($ary["value"]) ) ? $aryBuff[NAME_PRODUCTION_QUANTITY_HIDDEN] : $ary["value"];

				}

				$strHTML	= "\t\t<input type=\"hidden\" name=\"" .DETAIL_HASH. "[" .$aryBuff[PROC_SALES]. "][" .$aryBuff[NAME_DETAIL_ROW]. "][" .$ary["name"]. "]\" value=\"" .$ary["value"]. "\" />\n";

				// 列が終端列の場合
				if( $aryBuff[COL] == COL_LAST_DETAIL )
				{
					// [追加]明細行HIDDENを取得
					$strHTML	.= getAddDetailHiddenWorkSheet( $objMap, $aryBuff, $strProc );
				}

//fncDebug( 'lib_parse_PROC_DETAIL01.txt', $aryBuff, __FILE__, __LINE__, 'a');


				break;


			// 明細行
			case PROC_DETAIL:

				// 列が仕入部品の場合
				if( $aryBuff[COL] == COL_STOCK_ITEM_CODE )
				{
					// 値が空欄の場合、「0」を設定
					$ary["value"]	= ( is_null($ary["value"]) || empty($ary["value"]) ) ? INIT_STOCK_ITEM_CODE : $ary["value"];
				}

				// 列が仕入先の場合
				if( $aryBuff[COL] == COL_CUSTOMER_COMPANY_CODE )
				{
					// 仕入科目が「1224:チャージ」「1230:経費」でない場合
					if( $aryBuff[NAME_STOCK_KEYNAME] != STOCK_1224 && $aryBuff[NAME_STOCK_KEYNAME] != STOCK_1230 )
					{
						// 値が空欄の場合、「0」を設定
						$ary["value"]	= ( is_null($ary["value"]) || empty($ary["value"]) ) ? INIT_COMPANY_CODE : $ary["value"];
					}
				}

				// 列が計画個数の場合
				if( $aryBuff[COL] == COL_PRODUCT_QUANTITY )
				{
					// 仕入科目が「1224:チャージ」「1230:経費」でない場合
					if( $aryBuff[NAME_STOCK_KEYNAME] != STOCK_1224 && $aryBuff[NAME_STOCK_KEYNAME] != STOCK_1230 )
					{
						// 値が空欄の場合、生産予定数を設定
						$ary["value"]	= ( is_null($ary["value"]) || empty($ary["value"]) ) ? $aryBuff[NAME_PRODUCTION_QUANTITY_HIDDEN] : $ary["value"];
					}

					// 仕入科目が「1224:チャージ」または「1230:経費」の場合
					if( $aryBuff[NAME_STOCK_KEYNAME] == STOCK_1224 || $aryBuff[NAME_STOCK_KEYNAME] == STOCK_1230 )
					{
						// 値が空欄の場合、「0」を設定
						$ary["value"]	= ( is_null($ary["value"]) || empty($ary["value"]) ) ? 0 : $ary["value"];
					}
				}

				// 1230:経費 - 「備考」で「FIXED_COST_REDUCE」の場合
				if( $aryBuff[NAME_STOCK_KEYNAME] == STOCK_1230 && $aryBuff[COL] == COL_NOTE )
				{
					$ary["value"]	= ( $ary["value"] == FIXED_COST_REDUCE ) ? "": $ary["value"];
				}


				$strHTML	= "\t\t<input type=\"hidden\" name=\"" .DETAIL_HASH. "[" .$aryBuff[PROC_STOCK]. "][" .$aryBuff[NAME_DETAIL_ROW]. "][" .$ary["name"]. "]\" value=\"" .$ary["value"]. "\" />\n";


				// 列が終端列の場合
				if( $aryBuff[COL] == COL_LAST_DETAIL )
				{
					// [追加]明細行HIDDENを取得
					$strHTML	.= getAddDetailHiddenWorkSheet( $objMap, $aryBuff, $strProc );
				}
				break;


			// 小計・合計他
			case PROC_JUMP:

				// 標準割合の場合、Systemレートを適用
				if( $ary["name"] == NAME_STANDARD_RATE )
				{
					$ary["value"]	= $arySystemRate[NAME_SYSTEM_STANDARD_RATE];
				}

				$strHTML	= "\t<input type=\"hidden\" name=\"" .$ary["name"]. "\" value=\"" .$ary["value"]. "\" />\n";
				break;


			default:
				break;
		}
//fncDebug( 'getHiddenWorkSheet.txt', $aryBuff, __FILE__, __LINE__, 'a');

//fncDebug( 'getHiddenWorkSheet.txt', $strProc . ">>" . $strHTML, __FILE__, __LINE__, 'a');

		unset( $strRowCol, $strProc );
		return $strHTML;
	}

	//@------------------------------------------------------------------------
	/**
	*	概要	: [追加]ワークシートヘッダーHIDDEN要素返却
	*
	*
	*	解説	: 各ワークシートCell上に無いHIDDENオブジェクトを生成、返却
	*
	*	対象	: 結果用テンプレート
	*
	*	@param	[$objMap]	: [Object]	. Mapping Object
	*	@param	[$aryBuff]	: [Array]	. 共有配列
	*
	*	@return	[$strHTML]	: [String]
	*/
	//-------------------------------------------------------------------------
	function getAddHiddenWorkSheet( $objMap, $aryBuff)
	{
		global $strSheetUserName;


		$strHTML	= "";

		// 担当者名称
		$strHTML	.= "\t<input type=\"hidden\" name=\"" .NAME_USER_DISPLAY_NAME. "\" value=\"" .$strSheetUserName. "\" />\n";
//		$strHTML	.= "\t<input type=\"hidden\" name=\"" .NAME_USER_DISPLAY_NAME. "\" value=\"" .$aryBuff[NAME_USER_DISPLAY_NAME]. "\" />\n"; // Excel上の名前

		// 生産予定数補完
		$strHTML	.= "\t<input type=\"hidden\" name=\"" .NAME_PRODUCTION_QUANTITY_HIDDEN. "\" value=\"" .$aryBuff[NAME_PRODUCTION_QUANTITY_HIDDEN]. "\" />\n";

		// 納価補完
		$strHTML	.= "\t<input type=\"hidden\" name=\"" .NAME_PRODUCT_PRICE_HIDDEN. "\" value=\"" .$aryBuff[NAME_PRODUCT_PRICE_HIDDEN]. "\" />\n\n";


		unset( $aryBuff );
		return $strHTML;
	}




	//@------------------------------------------------------------------------
	/**
	*	概要	: [追加]ワークシート明細行HIDDEN要素返却
	*
	*
	*	解説	: 各ワークシートCell上に無いHIDDENオブジェクトを生成、返却
	*
	*	対象	: 結果用テンプレート
	*
	*	@param	[$objMap]	: [Object]	. Mapping Object
	*	@param	[$aryBuff]	: [Array]	. 共有配列
	*
	*	@return	[$strHTML]	: [String]
	*/
	//-------------------------------------------------------------------------
	function getAddDetailHiddenWorkSheet( $objMap, $aryBuff, $strProc )
	{
		global $arySystemRate;


		$strHTML	= "";
		$blnVal		= CONV_BOOL_F;
		$lngRate	= "";


		$strMonetaryUnitCode	= $objMap->getMonetaryUnitCode();	// 通貨コード
//		$lngConvRate			= $objMap->getConversionRate();		// 通貨コードによる換算レート Excelレート
//		$lngSubTotalJP			= $objMap->getSubTotalPriceJP();	// 日本円計画原価小計


		// 単価が存在しない場合、計画率判定フラグの変換及び、計画率取得
		if( is_null($aryBuff[NAME_PRODUCT_PRICE]) || empty($aryBuff[NAME_PRODUCT_PRICE]) )
		{
			$blnVal	= ( $aryBuff[NAME_PERCENT_INPUT_FLAG] ) ? CONV_BOOL_T : CONV_BOOL_F;

			// 計画率判定フラグが有効の場合、計画率取得
			if( $aryBuff[NAME_PERCENT_INPUT_FLAG] )
			{
				$lngRate	= $aryBuff[NAME_PRODUCT_RATE];
			}
		}


		// 換算レート取得
		switch( $strMonetaryUnitCode )
		{
			case CONV_MONETARY_JP:
				$lngConvRate	= JP_RATE;
				break;

			case CONV_MONETARY_US:
				$lngConvRate	= $arySystemRate[NAME_SYSTEM_CONVERSION_RATE];	// Systemレート
				break;

			case CONV_MONETARY_HKD:
				$lngConvRate	= $arySystemRate[NAME_SYSTEM_CONVERSION_RATE];	// Systemレート
				break;

			default:
				break;
		}


		// 日本円計画原価小計 = 計画原価 * 換算レート
		$lngSubTotalJP	= $aryBuff[NAME_SUBTOTAL_PRICE] * $lngConvRate;


		// 
		switch( $strProc )
		{
			case PROC_DETAIL01:
				// %入力フラグ
				$strHTML	.= "\t\t<input type=\"hidden\" name=\"" .DETAIL_HASH. "[" .$aryBuff[PROC_SALES]. "][" .$aryBuff[NAME_DETAIL_ROW]. "][" .NAME_PERCENT_INPUT_FLAG. "]\" value=\"" .$blnVal. "\" />\n";

				// 計画率
				$strHTML	.= "\t\t<input type=\"hidden\" name=\"" .DETAIL_HASH. "[" .$aryBuff[PROC_SALES]. "][" .$aryBuff[NAME_DETAIL_ROW]. "][" .NAME_PRODUCT_RATE. "]\" value=\"" .$lngRate. "\" />\n";

				// 換算レート
				$strHTML	.= "\t\t<input type=\"hidden\" name=\"" .DETAIL_HASH. "[" .$aryBuff[PROC_SALES]. "][" .$aryBuff[NAME_DETAIL_ROW]. "][" .NAME_CONVERSION_RATE. "]\" value=\"" .$lngConvRate. "\" />\n";

				// 日本円計画原価小計
				$strHTML	.= "\t\t<input type=\"hidden\" name=\"" .DETAIL_HASH. "[" .$aryBuff[PROC_SALES]. "][" .$aryBuff[NAME_DETAIL_ROW]. "][" .NAME_SUBTOTAL_PRICE_JP. "]\" value=\"" .$lngSubTotalJP. "\" />\n";

				// 仕入先名称が空欄の場合、「その他」を設定
				$aryBuff[NAME_COMPANY_DISPLAY_NAME]	= ( is_null($aryBuff[NAME_COMPANY_DISPLAY_NAME]) || empty($aryBuff[NAME_COMPANY_DISPLAY_NAME]) ) ? INIT_COMPANY_NAME : $aryBuff[NAME_COMPANY_DISPLAY_NAME];

				// 仕入先名称
				$strHTML	.= "\t\t<input type=\"hidden\" name=\"" .DETAIL_HASH. "[" .$aryBuff[PROC_SALES]. "][" .$aryBuff[NAME_DETAIL_ROW]. "][" .NAME_COMPANY_DISPLAY_NAME. "]\" value=\"" .$aryBuff[NAME_COMPANY_DISPLAY_NAME]. "\" />\n\n";
			break;

			case PROC_DETAIL:
				// %入力フラグ
				$strHTML	.= "\t\t<input type=\"hidden\" name=\"" .DETAIL_HASH. "[" .$aryBuff[PROC_STOCK]. "][" .$aryBuff[NAME_DETAIL_ROW]. "][" .NAME_PERCENT_INPUT_FLAG. "]\" value=\"" .$blnVal. "\" />\n";

				// 計画率
				$strHTML	.= "\t\t<input type=\"hidden\" name=\"" .DETAIL_HASH. "[" .$aryBuff[PROC_STOCK]. "][" .$aryBuff[NAME_DETAIL_ROW]. "][" .NAME_PRODUCT_RATE. "]\" value=\"" .$lngRate. "\" />\n";

				// 換算レート
				$strHTML	.= "\t\t<input type=\"hidden\" name=\"" .DETAIL_HASH. "[" .$aryBuff[PROC_STOCK]. "][" .$aryBuff[NAME_DETAIL_ROW]. "][" .NAME_CONVERSION_RATE. "]\" value=\"" .$lngConvRate. "\" />\n";

				// 日本円計画原価小計
				$strHTML	.= "\t\t<input type=\"hidden\" name=\"" .DETAIL_HASH. "[" .$aryBuff[PROC_STOCK]. "][" .$aryBuff[NAME_DETAIL_ROW]. "][" .NAME_SUBTOTAL_PRICE_JP. "]\" value=\"" .$lngSubTotalJP. "\" />\n";


				// 仕入科目が「1224:チャージ」「1230:経費」ではない場合
				if( $aryBuff[NAME_STOCK_KEYNAME] != STOCK_1224 && $aryBuff[NAME_STOCK_KEYNAME] != STOCK_1230 )
				{
					// 仕入先名称が空欄の場合、「その他」を設定
					$aryBuff[NAME_COMPANY_DISPLAY_NAME]	= ( is_null($aryBuff[NAME_COMPANY_DISPLAY_NAME]) || empty($aryBuff[NAME_COMPANY_DISPLAY_NAME]) ) ? INIT_COMPANY_NAME : $aryBuff[NAME_COMPANY_DISPLAY_NAME];
				}

				// 仕入先名称
				$strHTML	.= "\t\t<input type=\"hidden\" name=\"" .DETAIL_HASH. "[" .$aryBuff[PROC_STOCK]. "][" .$aryBuff[NAME_DETAIL_ROW]. "][" .NAME_COMPANY_DISPLAY_NAME. "]\" value=\"" .$aryBuff[NAME_COMPANY_DISPLAY_NAME]. "\" />\n\n";
				
			break;
		}

		return $strHTML;
	}


	//-------------------------------------------------------------------------
	//-------------------------------------------------------------------------
	function getSalesKey( $objMap, $data )
	{
		$lngKey		= 0;
		$aryBuff	= array();

		// マッピング配列の取得
		$aryBuff	= $objMap->getArrayMap();

		$lngKey		= $aryBuff[PROC_SALES][$data];

		unset( $aryBuff );
		return $lngKey;
	}
	//@------------------------------------------------------------------------
	/**
	*	概要	: 仕入科目HIDDEN配列キー返却
	*
	*
	*	解説	: 仕入科目のHIDDEN用配列キーを生成、返却
	*
	*	対象	: 結果用テンプレート
	*
	*	@param	[$objMap]	: [Object]	. Mapping Object
	*	@param	[$data]		: [Object]	. ワークシートデータ
	*
	*	@return	[$lngKey]	: [Integer]
	*/
	//-------------------------------------------------------------------------
	function getStockKey( $objMap, $data )
	{
		$lngKey		= 0;
		$aryBuff	= array();

		// マッピング配列の取得
		$aryBuff	= $objMap->getArrayMap();

		$lngKey		= $aryBuff[PROC_STOCK][$data];

		unset( $aryBuff );
		return $lngKey;
	}


	//@------------------------------------------------------------------------
	/**
	*	概要	: 処理モード設定
	*
	*
	*	解説	: マッピング処理が通常、明細のどちらに該当するのかを設定
	*
	*	対象	: 結果用テンプレート
	*
	*	@param	[$objMap]	: [Object]	. Mapping Object（参照渡し）
	*	@param	[$data]		: [Object]	. ワークシートデータ
	*	@param	[$col]		: [String]	. ワークシート列文字列
	*
	*/
	//-------------------------------------------------------------------------
	function setProcessMode( &$objMap, $data, $col )
	{

		require_once ( LIB_DEBUGFILE );

		global $aryMapping;


		$aryMap			= array();
		$aryBuff		= array();
		$aryMap			= $objMap->getArrayMap();		// マッピング配列
		$aryBuff		= $objMap->getArrayUnion();		// 共有配列
		$strProc		= $objMap->getProcessMode();	// 処理モード


		switch( $strProc )
		{
			// 通常
			case PROC_NORMAL:

				// データが「明細行（売上分類）」かどうか判定
				if( $objMap->fncCheckDetail( $data, PROC_SALES ) )
				{
					// 処理モード設定：明細行
					$objMap->setProcessMode( PROC_DETAIL01 );


					// 売上分類HIDDEN配列キー設定
					$objMap->setArrayUnion( PROC_SALES, getSalesKey($objMap, $data) );
//fncDebug( 'exc_array_00.txt', PROC_SALES, __FILE__, __LINE__, 'a');
//fncDebug( 'exc_array_00.txt', getSalesKey($objMap, $data), __FILE__, __LINE__, 'a');


					// 売上分類名（キー名称）設定
					$objMap->setArrayUnion( NAME_SALES_KEYNAME, $data );
//fncDebug( 'exc_array_00.txt', NAME_SALES_KEYNAME, __FILE__, __LINE__, 'a');
//fncDebug( 'exc_array_00.txt', $data, __FILE__, __LINE__, 'a');


					// 明細行番号のカウント設定
					$objMap->setArrayUnion( NAME_DETAIL_ROW, $aryMapping[PROC_COUNT][$data] );
//fncDebug( 'exc_array_00.txt', NAME_DETAIL_ROW, __FILE__, __LINE__, 'a');
//fncDebug( 'exc_array_00.txt', $aryMapping[PROC_COUNT], __FILE__, __LINE__, 'a');

				}


				// データが「明細行（仕入科目）」かどうか判定
				if( $objMap->fncCheckDetail( $data, PROC_STOCK ) )
				{
					// 処理モード設定：明細行
					$objMap->setProcessMode( PROC_DETAIL );


					// 仕入科目HIDDEN配列キー設定
					$objMap->setArrayUnion( PROC_STOCK, getStockKey($objMap, $data) );
//fncDebug( 'exc_array_11.txt', PROC_SALES, __FILE__, __LINE__, 'a');
//fncDebug( 'exc_array_11.txt', getSalesKey($objMap, $data), __FILE__, __LINE__, 'a');


					// 仕入科目名（キー名称）設定
					$objMap->setArrayUnion( NAME_STOCK_KEYNAME, $data );
//fncDebug( 'exc_array_11.txt', NAME_SALES_KEYNAME, __FILE__, __LINE__, 'a');
//fncDebug( 'exc_array_11.txt', $data, __FILE__, __LINE__, 'a');


					// 明細行番号のカウント設定
					$objMap->setArrayUnion( NAME_DETAIL_ROW, $aryMapping[PROC_COUNT][$data] );
//fncDebug( 'exc_array_11.txt', NAME_DETAIL_ROW, __FILE__, __LINE__, 'a');
//fncDebug( 'exc_array_11.txt', $aryMapping[PROC_COUNT][$data], __FILE__, __LINE__, 'a');

				}


				// ジャンプフラグが無効の場合
				if( !$aryBuff[NAME_JUMP_FLAG] )
				{
					// 小計・合計他かどうか判定
					if( $objMap->fncCheckJump( $data, PROC_JUMP ) )
					{
						// 処理モード設定：小計・合計他
						$objMap->setProcessMode( PROC_JUMP );
					}
				}
				break;

			// 明細行（売上分類・区分）
			case PROC_DETAIL01:
				// 明細行処理が処理終了列に達した場合
				if( $objMap->fncCheckFinishDetail( $col ) )
				{
					// 明細行番号のカウント
					$aryMapping[PROC_COUNT][$aryBuff[NAME_SALES_KEYNAME]] = $aryMapping[PROC_COUNT][$aryBuff[NAME_SALES_KEYNAME]] + 1;

					// 処理モード設定：通常
					$objMap->setProcessMode( PROC_NORMAL );
				}
				break;

			// 明細行（仕入科目・部品）
			case PROC_DETAIL:
				// 明細行処理が処理終了列に達した場合
				if( $objMap->fncCheckFinishDetail( $col ) )
				{
					// 明細行番号のカウント
					$aryMapping[PROC_COUNT][$aryBuff[NAME_STOCK_KEYNAME]] = $aryMapping[PROC_COUNT][$aryBuff[NAME_STOCK_KEYNAME]] + 1;

					// 処理モード設定：通常
					$objMap->setProcessMode( PROC_NORMAL );
				}
				break;

			// 小計・合計他
			case PROC_JUMP:
				// ジャンプフラグが無効の場合
				if( !$aryBuff[NAME_JUMP_FLAG] )
				{
					// 処理モード設定：通常
					$objMap->setProcessMode( PROC_NORMAL );
				}
				break;

			default:
				break;
		}

		unset( $aryMap, $aryBuff, $strProc );
		return true;
	}





	//@------------------------------------------------------------------------
	/**
	*	概要	: Excelワークシート名一覧HTML返却
	*
	*
	*	解説	: ワークシート名一覧表示 <option> HTML生成、返却
	*
	*
	*	@param	[$exc]		: [Object]	. ExcelParser Object
	*	@param	[$ws_num]	: [Integer]	. 選択したExcelワークシート番号
	*
	*	@return	[$strHTML]	: [String]
	*/
	//-------------------------------------------------------------------------
	function getWorkSheetName2HTML( $exc, $ws_num )
	{
		$strHTML	= "";

		if( $exc->sheetname[$ws_num] )
		{
			$strHTML .= "\t<option value=\"" .$ws_num. "\">" . $exc->sheetname[$ws_num] . "</option>\n";
		}

		return $strHTML;
	}



	//@------------------------------------------------------------------------
	/**
	*	概要	: Excelワークシート名返却
	*
	*
	*	解説	: ワークシート名返却
	*
	*
	*	@param	[$exc]		: [Object]	. ExcelParser Object
	*	@param	[$ws_num]	: [Integer]	. 選択したExcelワークシート番号
	*
	*	@return	[$strHTML]	: [String]
	*/
	//-------------------------------------------------------------------------
	function getWorkSheetName( $exc, $ws_num )
	{
		$strName	= "";

		if( $exc->sheetname[$ws_num] )
		{
			$strName	= $exc->sheetname[$ws_num];
		}

		return $strName;
	}

	//@------------------------------------------------------------------------
	/**
	*	概要	: ExcelワークシートデータHTML返却
	*
	*
	*	解説	: ワークシートデータ表示 <table> HTML生成、返却
	*
	*
	*	@param	[$aryWSData]	: [Array]	. ワークシートデータ配列
	*	@param	[$strWSName]	: [String]	. ワークシート名
	*	@param	[$ws_num]		: [Integer]	. 選択したExcelワークシート番号
	*	@param	[$strMode]		: [String]	. ワークシート選択・確認画面判定文字列
	*
	*	@return	[$strHTML]		: [String]
	*/
	//-------------------------------------------------------------------------
	function getWorkSheet2HTML( $aryWSData, $strWSName, $sn, $strMode, $obj )
	{ 
//		require_once ( LIB_DEBUGFILE );
//		require_once ( '/home/kids2/intra-v2-1/src/upload2/cmn/peruser.php' );
//		require_once ( '/home/kids2/intra-v2-1/src/upload2/cmn/lib_peruser.php' );


		// ワークシート名取得
		$strHTML	.= "<a name=\"" .$sn. "\"></a>";
		$strHTML	.= "<br />\n";
		$strHTML	.= "&nbsp;&nbsp;<b>Worksheet: \"";
		$strHTML	.= $strWSName;
		$strHTML	.= "\"</b>\n\n";

		// データが取得出来ない場合
		//if( !is_array($aryEdit) || !$aryEdit )
		if( !isset($obj) )
		{
			// emtpty worksheet
			$strHTML	.= "<b> - empty</b>\n";

			$strHTML	.= "<div align=\"right\">\n";
			$strHTML	.= "\t<button onclick=\"window.close();\"> 閉じる </button>&nbsp;&nbsp;&nbsp;\n";
			$strHTML	.= "\t<a id=\"excHref\" href=\"#top\"><b>↑Page Top</b></a>&nbsp;&nbsp;\n";
			$strHTML	.= "</div>\n\n\n";
			$strHTML	.= "<br />";

			$strHTML	.= "\n\n\n<hr size=\"1\"><br />\n";
			return $strHTML;
		}


		// ファイル選択HTMLスクリプト
		$strHTML	.= getFileConfirmScript2HTML( $sn, $strMode );


		// ここからシート成形

		// スタイルシートの読み込み
		$strHTML	.= fncExcelCss($obj);


//fncDebug( 'getWorkSheet2HTML_aryWSData.txt', $aryWSData, __FILE__, __LINE__);

		$w=32;
		if (!isset($obj->maxcell[$sn])) $obj->maxcell[$sn]=0;
		for($i=0; $i <= COL_EOF_INT; $i++)
		{
			$w+=$obj->getColWidth($sn,$i);
//			$w += $aryWSData[$i]['getColWidth'];
		}
		//$strHTML .=  "シート".$sn."： ".outStr($obj->sheetname[$sn]) ."<br>\n";
		$hd=$obj->getHEADER($sn);
		$ft=$obj->getFOOTER($sn);

//		$strHTML .= '<div id="ExcelData">';

		// ヘッダー部の表示用
		if ($hd!==null)
		{
			$hd['left']=(isset($hd['left']))? outStr($hd['left']):'';
			$hd['center']=(isset($hd['center']))? outStr($hd['center']):'';
			$hd['right']=(isset($hd['right']))? outStr($hd['right']):'';

			$strHTML .= '<table width='.$w.' border="0" cellpadding="0" cellspacing="1" bordercolor="#CCCCCC" bgcolor="#CCCCCC">
			<tr>
			    <td width="30" nowrap><font size="1">ヘッダ</font></td>
			    <td bgcolor="#FFFFFF"><div align="left"> '.$hd['left'].' </div></td>
			    <td bgcolor="#FFFFFF"><div align="center"> '.$hd['center'].' </div></td>
			    <td bgcolor="#FFFFFF"><div align="right"> '.$hd['right'].' </div></td>
			</tr></table>';
		}

		// メイン部の表示用
		$strHTML .= '
		<table border="0" cellpadding="0" cellspacing="0" width="'.$w.'" bgcolor="#FFFFFF" style="border-collapse: collapse;">
		  <tr bgcolor="#CCCCCC">
		    <th class="XF" bgcolor="#CCCCCC" scope="col" width="32">&nbsp;</th>';


		//for($i=0;$i<=$obj->maxcell[$sn];$i++)
		for($i=0;$i < COL_EOF_INT; $i++)
		{
			$tdwidth=$obj->getColWidth($sn,$i);
			//$tdwidth = $aryWSData[$i]['getColWidth'];
			$strHTML .= '    <th class="XF" bgcolor="#CCCCCC" scope="col" width="';
			$strHTML .= $tdwidth.'">'.chr(($i % 26) + 65).'</th>'."\n";
		}
		$strHTML .= "  </tr>\n";


		if (!isset($obj->maxrow[$sn])) $obj->maxrow[$sn]=0;

		// ------------------------------------------------------
		// 行単位でループ
		// ------------------------------------------------------
		$lngDispRow =0;
		for( $lngRow = 0; $lngRow <= $obj->maxrow[$sn]; $lngRow++ )
		{
			// 保持データのMaxでBreak
			if($lngDispRow==count($aryWSData)) break;

			$strHTML .= '  <tr height="'.$obj->getRowHeight($sn, $lngRow).'" class="_%classname%_" style="_%rowstyle%_">'."\n";
			$strHTML .= '    <th class="XF" bgcolor="#CCCCCC" scope="row" >'.(string)($lngRow+1)."</th>\n";


			// ------------------------------------------------------
			// 列単位（セル）でループ　//for( $lngCol = 0; $lngCol <= $obj->maxcell[$sn]; $lngCol++ )
			// ------------------------------------------------------
			for( $lngCol = 0; $lngCol < COL_EOF_INT; $lngCol++ )
			{
				
				// 設定データ上のrowを取得
				//$lngRow = $r;
				
				$tdwidth = $obj->getColWidth($sn, $lngCol);

				// 保持した配列の該当ROWと同等であれば、保持した値を表示
				if( $aryWSData[$lngDispRow]['row'] == $lngRow )
				{
					// Excel表記のまま表示
					$dispval = $obj->dispcell($sn, $lngRow, $lngCol);
					// 加工データを表示
					//$dispval = $aryWSData[$lngDispRow][(string)(chr(($lngCol % 26) + 65))];
					
					$rowstyle = 'display:inline';
					$classname = '';
				}
				else
				{
					// Excel表記のまま表示
					$dispval = $obj->dispcell($sn, $lngRow, $lngCol);
					$rowstyle ='background-color:gray;';
					$classname = 'rowstyle'.$sn;
				}

//fncDebug( 'getWorkSheet2HTML_aryWSData_cell.txt', (string)(chr(($lngCol % 26) + 65)).':'.$dispval, __FILE__, __LINE__,'a');

				// 表示データを最適化
				$dispval=outStr($dispval);

				if (isset($obj->hlink[$sn][$lngRow][$lngCol]))
				{
					$dispval='<a href="'.$obj->hlink[$sn][$lngRow][$lngCol].'">'.$dispval.'</a>';
				}
				if ($dispval=='') $dispval=' ';

				// 属性を取得
				$xf = $obj->getAttribute($sn, $lngRow, $lngCol);
				
				if (isset($xf['wrap']))
				if ($xf['wrap']) $dispval = ereg_replace("\n", "<br />", $dispval);
				$xfno = ($xf['xf'] > 0) ? $xf['xf']: 0;

				// アライメントの設定
				$align ='x';
				if (isset($xf['halign'])) if ($xf['halign'] != 0) $align = '';
				if ($align == 'x') 
				{
					if ($xf['type']==Type_RK) $align = ' Align="right"';
					else if ($xf['type']==Type_RK2) $align = ' Align="right"';
					else if ($xf['type']==Type_NUMBER) $align = ' Align="right"';
					else if ($xf['type']==Type_FORMULA && is_numeric($dispval)) $align = ' Align="right"';
					else if ($xf['type']==Type_FORMULA2 && is_numeric($dispval)) $align = ' Align="right"';
					else if ($xf['type']==Type_FORMULA && ($dispval=='TRUE' || $dispval=='FALSE')) $align = ' Align="center"';
					else if ($xf['type']==Type_FORMULA2 && ($dispval=='TRUE' || $dispval=='FALSE')) $align = ' Align="center"';
					else if ($xf['type']==Type_BOOLERR) $align = ' Align="center"';
					else $align= '';
					if ($xf['format']=='@') $align = '';
				} else $align= '';
				if (substr($dispval,0,1)=="'") $dispval=substr($dispval,1);
				if (substr($dispval,0,6)=="&#039;") $dispval=substr($dispval,6);

				// マージセルの設定
				if(isset($obj->celmergeinfo[$sn][$lngRow][$lngCol]['cond']))
				{
					if($obj->celmergeinfo[$sn][$lngRow][$lngCol]['cond']==1)
					{
						$colspan=$obj->celmergeinfo[$sn][$lngRow][$lngCol]['cspan'];
						$rowspan=$obj->celmergeinfo[$sn][$lngRow][$lngCol]['rspan'];
						if($colspan>1) $rcspan =' colspan="'.$colspan.'"';else $rcspan=' width="'.$tdwidth.'"';
						if($rowspan>1) $rcspan.=' rowspan="'.$rowspan.'"';
						$strHTML .= '    <td class="XFs'. $sn . "r" . $lngRow . "c" . $lngCol .'" '.$rcspan.$align.' class="_%classname%_" style="_%rowstyle%_">'.$dispval."</td>\n";
					}
				} else {
					$strHTML .= '    <td class="XF'.$xfno.'" width="'.$tdwidth.'"'.$align.' class="_%classname%_" style="_%rowstyle%_">'.$dispval."</td>\n";
				}
			}
			if($aryWSData[$lngDispRow]['row'] == $lngRow ) $lngDispRow++;	// 表示された行カウンタのインクリメント
			$strHTML .= "</tr>\n";
			$strHTML = str_replace('_%classname%_', $classname, $strHTML);
			$strHTML = str_replace('_%rowstyle%_', $rowstyle, $strHTML);

		}
		$strHTML .= "</table>\n";

		if ($ft!==null){
			$ft['left'] = (isset($ft['left']))? outStr($ft['left']):'';
			$ft['center']= (isset($ft['center']))? outStr($ft['center']):'';
			$ft['right']= (isset($ft['right']))? outStr($ft['right']):'';

			$strHTML .= '
			<table width="'.$w.'" border="0" cellpadding="0" cellspacing="1" bordercolor="#CCCCCC" bgcolor="#CCCCCC"><tr>
			    <td width="30" nowrap><font size="1">フッタ</font></td>
			    <td bgcolor="#FFFFFF"><div align="left">'.$ft['left'].' </div></td>
			    <td bgcolor="#FFFFFF"><div align="center">'.$ft['center'].'</div></td>
			    <td bgcolor="#FFFFFF"><div align="right">'.$ft['right'].'</div></td>
			</tr></table>';
		}


		unset( $aryEdit );
		return $strHTML;

	}



	//@------------------------------------------------------------------------
	/**
	*	概要	: ExcelワークシートデータHIDDEN返却
	*
	*
	*	解説	: ワークシートデータ表示 <input type="hidden"> 生成、返却
	*
	*	対象	: 結果用テンプレート
	*
	*	@param	[$objMap]		: [Object]	. Excel Mapping Object（参照渡し）
	*	@param	[$aryData]		: [Array]	. $_REQUEST より取得した値
	*	@param	[$aryWSData]	: [Array]	. ワークシートデータ配列
	*	@param	[$strWSName]	: [String]	. ワークシート名
	*	@param	[$ws_num]		: [Integer]	. 選択したExcelワークシート番号
	*
	*	@return	[$strHidden]	: [String]
	*/
	//-------------------------------------------------------------------------
	function getWSArray2Hidden( &$objMap, $aryData, $aryWSData, $strWSName, $ws_num )
	{
		require_once ( LIB_DEBUGFILE );


		$aryEdit		= $aryWSData;
		$lngBuffRow		= 0;			// 行番号バッファ
		$strHTML		= "";
		$aryRetVal		= array();		// マッピング結果配列バッファ


		// データが取得出来ない場合
		if( !is_array($aryEdit) || !$aryEdit )
		{
			// emtpty worksheet
			$strHTML	= "\n";
			return $strHTML;
		}


		// 共通HIDDEN要素取得
		$strHTML	.= getHiddenCommon( $ws_num, $aryData ) . "\n";
		$strHTML	.= "\t<input type=\"hidden\" name=\"strWorkSheetName\" value=\"" .$strWSName. "\" />\n";


		// マッピング開始
		for( $i = 0; $i < count($aryEdit); $i++ )
		{
			while( list($index, $value) = each($aryEdit[$i]) )
			{
				// 行番号取得
				$lngBuffRow	= ($i+1);

				// 処理モード設定
				setProcessMode( $objMap, $value, $index );


//fncDebug( 'exc_array_01.txt', $objMap->getProcessMode(), __FILE__, __LINE__, 'a');
fncDebug( 'exc_array_01.txt', "i>".$i.">lngBuffRow>".$lngBuffRow.">index>".$index.">value>".$value, __FILE__, __LINE__, 'a');

				// マッピング  $row, $col, $data
				if( $aryRetVal = $objMap->getMappingData( $lngBuffRow, $index, $value ) )
				{
					$strHTML	.= getHiddenWorkSheet( $objMap, $aryRetVal );
				}

fncDebug( 'exc_array_02.txt', $strHTML, __FILE__, __LINE__);

			}
		}



		unset( $aryEdit, $aryRetVal, $lngBuffRow );
		return $strHTML;
	}



	//@------------------------------------------------------------------------
	/**
	*	概要	: 製品マスタ情報返却
	*
	*
	*	解説	: ワークシートデータ上、製品マスタ情報返却
	*
	*
	*	@param	[$objMap]		: [Object]	. Excel Mapping Object
	*	@param	[$aryWSData]	: [Array]	. ワークシートデータ配列
	*	@param	[$strProc]		: [String]	. 処理モードハッシュ名
	*
	*	@return	[$aryData]	: [Array]
	*/
	//-------------------------------------------------------------------------
	function getProductHeader2Array( $objMap, $aryWSData, $strProc )
	{
		require_once ( LIB_DEBUGFILE );


		$aryEdit	= $aryWSData;
		$lngBuffRow	= 0;			// 行番号バッファ
		$aryData	= array();		// データ配列
		$aryRetVal	= array();		// マッピング結果配列バッファ


		// データが取得出来ない場合
		if( !is_array($aryEdit) || !$aryEdit )
		{
			return false;
		}


		// 処理モード設定
		$objMap->setProcessMode( $strProc );

		// マッピング開始
		for( $i = 0; $i < count($aryEdit); $i++ )
		{
			while( list($index, $value) = each($aryEdit[$i]) )
			{
				// 行番号取得
				$lngBuffRow	= ($i+1);

				// マッピング
				if( $aryRetVal = $objMap->getMappingData( $lngBuffRow, $index, $value ) )
				{
					$aryData[strtolower($aryRetVal["name"])]	= $aryRetVal["value"];
				}
			}
		}


//fncDebug( 'exc_array.txt', $aryEdit, __FILE__, __LINE__);

		unset( $aryEdit, $aryRetVal, $lngBuffRow );
		return $aryData;
	}

?>
