<?php



	/*
	//****************************************************************************
	// 仕入一覧表
		見出しの生成

	*	@param	object	$worksheet		ワークシートオブジェクト
	*	@param	object	$aryFormat		フォーマットオブジェクト
	*	@param	array	$aryDataset		データセット定義
	*	@param	integer	$j				列カウンター
	*	@param	array	$aryResult		データベース取得値
	*
	*	@return	integer					見出し行数

	//****************************************************************************
	*/
	function fncfncSpreadSheetCellHeader_Stock(&$worksheet, &$objFormat, $aryData)
	{
/*
		$worksheet->writeString( 0, 0, mb_convert_encoding('仕入一覧表','shift_jis'), $objFormat );
		$worksheet->writeString( 1, 0, mb_convert_encoding('仕入計上日　'.$aryData["dtmAppropriationDateFrom"].'-'.$aryData["dtmAppropriationDateTo"], 'shift_jis'), $objFormat );
		$worksheet->writeNumber( 2, 18, mb_convert_encoding('','shift_jis'));
		$worksheet->writeString( 2, 19, mb_convert_encoding('','shift_jis'), $objFormat );
*/
//		$worksheet->writeString( 0, 10, mb_convert_encoding(' '.$aryData["dtmAppropriationDateFrom"].''.$aryData[$aryData["lngExportData"]]["strTitleName"], 'shift_jis'), $objFormat );
		$worksheet->writeString( 0, 10, mb_convert_encoding(' '.$aryData[$aryData["lngExportData"]]["strTitleName"], 'shift_jis'), $objFormat );
		$worksheet->writeString( 0, 0, mb_convert_encoding('仕入：'.$aryData[$aryData["lngExportData"]]["strTitleName"], 'shift_jis'), $objFormat );
		$worksheet->writeString( 1, 0, mb_convert_encoding('期　間　'.$aryData["dtmAppropriationDateFrom"].'-'.$aryData["dtmAppropriationDateTo"], 'shift_jis'), $objFormat );
		
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
	function fncSpreadSheetCellData_Stock($aryDataset, $i, $j, $aryResult, $lngHeadLineCnt)
	{

		$strNo = (string)($i + 1 + $lngHeadLineCnt);	// エクセル上での計算用の行番号を設定
		$curCalc = 0;
		$varCellData = "";
		$strReceiveCode='';
		
		switch((int)$j)
		{
			//
			case 0:
			case 1:
			case 2:

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
	//	小計行用のキーカラムを生成
	*
	*	@param	array	$aryData		FORMデータ
	*
	*	@return	Array	指定されたカラム列位置の、キーカラム配列
	*
	//
	//****************************************************************************
	*/
	function fncSpreadSheetCellData_StockKeyCol($aryData)
	{

		$lngRet = 0;
		$aryAfterResult = array();
		
		// ------------------------------------
		// 小計行のまとめ対象のカラム一覧
		// ------------------------------------
		if( $aryData['lngExportConditions'] == 1)	// 選択により 対象のカラムを指定
		{
			$aryKeyCol = array(3, 9);	// 3仕入先コード、9仕入科目コード
		}
		else
		{
			$aryKeyCol = array( 9, 5, 13);	// 5部門コード、9仕入科目コード、13製品コード
		}
		
		return $aryKeyCol;

	}

	/*
	//****************************************************************************
	//	小計行用のカラムタイトルを生成
	*
	*
	//
	//****************************************************************************
	*/
	function fncSpreadSheetCellData_StockSubTitle()
	{
		// 小計カラム・タイトル
		$arySubTitle[1][1] = '仕入科目計';
		$arySubTitle[1][2] = '仕入先計';
		$arySubTitle[1][3] = '総計';

		$arySubTitle[2][1] = '製品計';
		$arySubTitle[2][2] = '部門計';
		$arySubTitle[2][3] = '仕入科目計';
		$arySubTitle[2][4] = '総計';

		return $arySubTitle;
	}
	
	
	/*
	//****************************************************************************
	//	小計行／計算式の埋め込み
	*
	*	@param	array	$aryDataset		データセット定義
	*	@param	integer	$i				行カウンター
	*	@param	integer	$j				列カウンター
	*	@param	array	$aryResult		データベース取得値
	*	@param	integer	$lngHeadLineCnt	ヘッダー部の総合行数（データ部の始まる前までの行）
	*	@param	integer	$lngStartLine	前回に小計行を出した次のカウンタ（計算式の開始行に利用）
	*	@param	integer	$lngSubtotal	小計行で増えたカウンタ（計算式の終了行に利用）
	*
	*	@return							セル設定用データ
	*
	// ・小計行に必要な処理を記述する
	//
	//****************************************************************************
	*/
/*
	function fncSpreadSheetCellData_SalesCalc($aryDataset, $i, $j, $aryResult, $lngHeadLineCnt, $lngStartLine, $lngSubtotal)
	{
		$lngA = 1+ $lngHeadLineCnt + $lngStartLine;
		$lngB = 1+ $lngHeadLineCnt + $i + $lngSubtotal;
		
		switch((int)$j)
		{

			case 17: // 税抜金額
				$curCalc = mb_convert_encoding('=SUM(R'.$lngA.':R'.$lngB.')', "shift_jis");
				$varCellData = $curCalc;
				break;
			case 18: // 税額
				$curCalc = mb_convert_encoding('=SUM(S'.$lngA.':S'.$lngB.')', "shift_jis");
				$varCellData = $curCalc;
				break;
			case 19: // 合計金額
				$curCalc = mb_convert_encoding('=SUM(T'.$lngA.':T'.$lngB.')', "shift_jis");
				$varCellData = $curCalc;
				break;
			default:
		
		}
		
		return $varCellData;
	}
*/

	/*
	//****************************************************************************
	// 01 売上見込
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
	function fncSpreadSheetCellFormat_Stock(&$workbook, &$aryFormat, &$aryDataset, $j, $aryResult)
	{

		unset($objFormat);

		switch( (int)$j )
		{
		case 16://単価
			$objFormat =& fncSpreadSheetCellFormat($workbook, $aryFormat, 'numberDec4');
			break;

			case 0:
			case 1:
			case 2:
			default:
				$objFormat =& fncSpreadSheetCellFormat($workbook, $aryFormat, $aryDataset[$j]->type);
		}

		return $objFormat;

	}

?>