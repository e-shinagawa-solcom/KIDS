<?php

// ----------------------------------------------------------------------------
/**
*       スプレッドシート形式、データ生成関数群
*
*
*       @package    K.I.D.S.
*       @license    http://www.kuwagata.co.jp/
*       @copyright  KUWAGATA CO., LTD.
*       @author     kuwagata
*       @access     public
*       @version    2.00
*
*
*       処理概要
*		・JSON形式でのデータ生成を行う
*		・Spreadsheet/Excel/Writer形式での出力を行う
*
*       更新履歴
*
*/
// ----------------------------------------------------------------------------

	// 共通定義
	include_once('conf.inc');
	require_once('./js/JSON.php');
	require_once("Spreadsheet/Excel/Writer.php");
	
	// データ種の定義
	require_once("./spreadsheet/stat01.php");
	require_once("./spreadsheet/stat02.php");
	
	
	
	/*
	//****************************************************************************
	// スプレッドシート形式にデータをフォーマット
	// 
	//****************************************************************************
	*/
	function fncSpreadSheetDataFormat( $lngResultID, $lngResultRows, $lngFieldNum, $aryData, $objDB )
	{

		/////////////////////////////////////////////////////////////////////////////
		// データプレビュー
		if ( $aryData["lngActionCode"] == 1 )
		{

/* HTMLプレビュー（SigmaGrid）を使用しないので コメントアウト */
/*
			// JSON形式のデータに変換
			$strDataJson = fncSpreadSheetDataJson2( $lngResultID, $lngResultRows, $lngFieldNum, $aryData, $objDB );

			if($strDataJson=="")
			{
				return false;
			}
			// SigmaGrid用ヘッダー情報の取得
			if(!$aryHeader = fncSpreadSheetHeaderJson($aryData["lngExportData"]))
			{
				return false;
			}
			
			$aryExportDataInfo = array();
			$aryExportDataInfo[$aryData["lngExportData"]]["__DATASET__"]	= $aryHeader["DATASET"];
			$aryExportDataInfo[$aryData["lngExportData"]]["__COLUMNS__"]	= $aryHeader["COLUMNS"];
			$aryExportDataInfo[$aryData["lngExportData"]]["__DATA__"]	= $strDataJson;
*/
			$aryExportDataInfo[$aryData["lngExportData"]]["__TITLE__"]	= $aryData[$aryData["lngExportData"]]["strTitleName"];	// 社内統計データ


			// 売上区分の配列データを文字列へ変換する
			$strlngSalesClassCode = "";
			if(is_array($aryData["lngSalesClassCode"]) )
			{
				foreach($aryData["lngSalesClassCode"] as $data)
				{
					$strlngSalesClassCode .= "&amp;lngSalesClassCode[]=".$data;
				}
			}
			
			// リンク用のURLを生成する
			$aryExportDataInfo[$aryData["lngExportData"]]["__EXCEL_URL__"]	= '/dataex/result/stat.php?strSessionID='.$aryData["strSessionID"].'&'.'lngExportData='.$aryData["lngExportData"].'&lngActionCode=2&dtmAppropriationDateFrom='.$aryData["dtmAppropriationDateFrom"].'&dtmAppropriationDateTo='.$aryData["dtmAppropriationDateTo"].'&lngGroupCode='.$aryData["lngGroupCode"].$strlngSalesClassCode.'&preview=true';

			// SpreadSheet用テンプレートの取得
			//if( !$strTemplate = file_get_contents( "./spreadsheet_".$aryData[$aryData["lngExportData"]]["prefix"].".html" ) )
			if( !$strTemplate = file_get_contents( "./spreadsheet_preview.html" ) )
			{
				return false;
			}

			// 置き換え文字列の置換
			$aryKeys = array_keys( $aryExportDataInfo[$aryData["lngExportData"]] );
			foreach ( $aryKeys as $strKey )
			{
				$strTemplate = preg_replace( "/_%" . $strKey . "%_/", $aryExportDataInfo[$aryData["lngExportData"]][$strKey], $strTemplate );
			}

			echo $strTemplate;
			return true;
		}
		/////////////////////////////////////////////////////////////////////////////
		// データダウンロード（エクセル形式）
		elseif ( $aryData["lngActionCode"] == 2 )
		{
			// ワークブックの作成
			$workbook = fncSpreadSheetCreateWorkbook();

			
			// ExcelWriter形式のデータに変換
			// $workbook を参照渡し
			if( fncSpreadSheetDataExcel( $lngResultID, $lngResultRows, $lngFieldNum, $aryData, $objDB, $workbook ) == false)
			{
				return false;
			}

			//	header("Content-Type: application/vnd.ms-excel");
			//	header("Content-Disposition: attachment; filename=hoge.xls");

			/**
			 * Excelファイルを出力
			 */
			// ファイル名の生成
			$filename = mb_convert_encoding($aryData[$aryData["lngExportData"]]["strTitleName"].'_('.str_replace('/','',$aryData["dtmAppropriationDateFrom"]).'-'.str_replace('/','',$aryData["dtmAppropriationDateTo"]).')_'.date('Ymd_Hi', mktime()) .'.xls', 'shift_jis');
			if( $aryData["preview"] ) $filename = date('Ymd_hm').'.xls';
			
			// Excelファイルを出力
			$workbook->send($filename);

			// Excelオブジェクトの後処理
			$workbook->close();

			return true;
		}
		/////////////////////////////////////////////////////////////////////////////
		// 配列データを生成する
		else
		{
			$aryResult = array();
			for ( $i = 0; $i < $lngResultRows; $i++ )
			{
				// 行結果取得
				$aryResult[] = $objDB->fetchArray( $lngResultID, $i );
			}
		}
		
		return $aryResult;
	}

	/*
	//****************************************************************************
	//	データベースから取得したデータを
	//	JSON形式に変換する
	//
	*	@param  Integer $lngResultID		SQL結果ID
	*	@param  Integer $lngResultRows		結果行数
	*	@param  Integer $lngFieldNum		結果カラム数
	*	@param  Integer $aryData			入力パラメーターデータ
	*	@param  Object  $objDB				DBオブジェクト
	*	@return String  $strJsonData		JSON形式のデータ
	*	@access public
	//****************************************************************************
	*/
	function fncSpreadSheetDataJson2($lngResultID, $lngResultRows, $lngFieldNum, $aryData, $objDB)
	{

		//
		// JSON形式（SigmaGrid用）ヘッダー情報の取得
		if(!$aryHeader = fncSpreadSheetHeaderJson($aryData["lngExportData"]))
		{
			return false;
		}

		$objJson = new Services_JSON; 
		$aryDataset = $objJson->decode($aryHeader["DATASET"]); // dataset
		$aryColumns = $objJson->decode($aryHeader["COLUMNS"]); // dataset

		$arySetData = array();
		// 結果を成形
		for ( $i = 0; $i < $lngResultRows; $i++ )
		{
			// 行結果取得
			$aryResult = $objDB->fetchArray ( $lngResultID, $i );
			$aryCellData = array();
			for ( $j = 0; $j < count($aryDataset); $j++ )
			{
//				$strKey = strtolower($aryDataset[$j]->name);
//				$aryCellData[$j] = preg_replace ( "/\s+?$/", "", $aryResult[$strKey] );// 空白削除

				$varCellData = "";
				
				// データ取得と生成
				$varCellData = fncSpreadSheetCellData01($aryDataset, $i, $j, $aryResult, 0);
				$aryDataset[$j]->data = $varCellData;

				$aryCellData[$j] = $varCellData;
				
			}
			
			// 2次元配列のセット
			$arySetData[] = $aryCellData;
		}
		
		return $objJson->encode($arySetData);
		
		//var_dump($objJson->encode($arySetData));
		//exit;
	}

	/*
	//****************************************************************************
	//	（fncSpreadSheetDataJson2() へ移管）
	//	データベースから取得したデータをJSON形式に変換する
	//****************************************************************************
	*/
	function fncSpreadSheetDataJson($lngResultID, $lngResultRows, $lngFieldNum, $aryData, $objDB)
	{

		$strSpreadSheetData = "";

		// 結果を成形
		for ( $i = 0; $i < $lngResultRows; $i++ )
		{
			// 行結果取得
			$aryResult = $objDB->fetchArray ( $lngResultID, $i );

			$strSpreadSheetData .= "[";
			
			// カラム数表示
			for ( $j = 0; $j < $lngFieldNum; $j++ )
			{
				$aryResult[$j] = preg_replace ( "/\s+?$/", "", $aryResult[$j] );// 空白削除
				$strResult = "";
				if( $j == 2 )
				{
					$strResult = $aryResult[$j];
				}
				else
				{
					$strResult = '"'.$aryResult[$j].'"';
				}
				
				$strSpreadSheetData .= '"'.$aryResult[$j].'"';
				if( ($j+1) < $lngFieldNum ) $strSpreadSheetData .= ",";
				
			}
			$strSpreadSheetData .= "]";
			if( ($i+1) < $lngResultRows )
			{
				$strSpreadSheetData .= ",\n";
			}
			else
			{
				$strSpreadSheetData .= "\n";
			}
			
		}
		
		return "[\n".$strSpreadSheetData."\n]";
	}
	
	/*
	//****************************************************************************
	//	
	//	データベースから取得したデータをSpreadsheet/Excel/Writer形式に変換する
	//****************************************************************************
	*/
	function fncSpreadSheetDataExcel($lngResultID, $lngResultRows, $lngFieldNum, $aryData, $objDB, &$workbook)
	{

	    /**
	     * ワークシートの追加
	     */
		$worksheet =& $workbook->addWorksheet( mb_convert_encoding($aryData["strTitleName"], 'shift_jis') );
//		$worksheet =& $workbook->addWorksheet('stat01');
//		$worksheet->setInputEncoding("shift_jis");
		$worksheet->setInputEncoding("sjis-win");


		//
		// JSON形式ヘッダー情報の取得
		if(!$aryHeader = fncSpreadSheetHeaderJson($aryData["lngExportData"]))
		{
			return false;
		}
		$objJson = new Services_JSON; 
		$aryDataset = $objJson->decode($aryHeader["DATASET"]); // dataset
		$aryColumns = $objJson->decode($aryHeader["COLUMNS"]); // dataset
//var_dump($aryColumns);
//exit;
		unset($objJson);
		
		
		// ----------------------------------------------------------------
		// ヘッダーの設定

		// フォーマットオブジェクトの生成
		$objFormat0 =& $workbook->addFormat();
		// ヘッダースタイル
		fncSpreadSheetExcelFormatSetting($objFormat0, "header0");
		fncSpreadSheetExcelFormatSetting($objFormat0, "setFontFamilyMsGothic");

		// データ種による取得処理
		switch( (int)$aryData["lngExportData"] )
		{
			// ----------------------------------------------------
			// 01 売上見込
			case 6:
				$lngHeaderCount = fncfncSpreadSheetCellHeader01($worksheet, $objFormat0, $aryData);
				break;
			
			case 7:
				// 最初の見出しの設定
				$lngHeaderCount = fncfncSpreadSheetCellHeader02($worksheet, $objFormat0, $aryData);
				break;
			// ----------------------------------------------------
		}


		$objFormat1 =& $workbook->addFormat();
		// ヘッダースタイル
		fncSpreadSheetExcelFormatSetting($objFormat1, "header");
		fncSpreadSheetExcelFormatSetting($objFormat1, "setFontFamilyMsGothic");

		// ヘッダーカラムの設定
		for( $j = 0; $j < count($aryColumns); $j++ )
		{
			//$worksheet->writeString( 0, $i, $aryColumns[$i]->header, $objFormat1 );
//			$worksheet->writeString( $lngHeaderCount, $j, mb_convert_encoding($aryColumns[$j]->header,'shift_jis'), $objFormat1 );
			$worksheet->writeString( $lngHeaderCount, $j, mb_convert_encoding($aryColumns[$j]->header,'sjis-win'), $objFormat1 );

			// 列の幅設定
			$worksheet->setColumn($j,$j,((int)($aryColumns[$j]->width)/10)+2 );
		}


		// ヘッダーの行数（上記で設定済みの行数）
		$lngHeadLineCnt = $lngHeaderCount + 1;

		// ヘッダー部、最初のx行を固定する
		$worksheet->freezePanes(array($lngHeadLineCnt, 0));

		// フォーマットオブジェクトを保持する配列
		$aryFormat = array();

		// ----------------------------------------------------------------
		// 結果を成形
		//
		//
		for( $i = 0; $i < $lngResultRows; $i++ )
		{
			// 行結果取得
			$aryResult = $objDB->fetchArray( $lngResultID, $i );


			for( $j = 0; $j < count($aryDataset); $j++ )
			{

				$varCellData = "";
				unset($objFormat);
				
				// ----------------------------------------------------
				// データ種による取得処理
				switch( (int)$aryData["lngExportData"] )
				{
					// 01 売上見込
					case 6:
						// データ生成と取得
						$varCellData = fncSpreadSheetCellData01($aryDataset, $i, $j, $aryResult, $lngHeadLineCnt);
						$aryDataset[$j]->data = $varCellData;
						// フォーマットオブジェクトの取得
						$objFormat =& fncSpreadSheetCellFormat01($workbook, $aryFormat, $aryDataset, $j, $aryResult);
						break;
					
					// 02 概算売上
					case 7:
						// データ生成と取得
						$varCellData = fncSpreadSheetCellData02($aryDataset, $i, $j, $aryResult, $lngHeadLineCnt);
						$aryDataset[$j]->data = $varCellData;
						// フォーマットオブジェクトの取得
						$objFormat =& fncSpreadSheetCellFormat02($workbook, $aryFormat, $aryDataset, $j, $aryResult);
						break;
				}
				// ----------------------------------------------------
				
				
				// ワークシートにセルの書き込み
				if( $aryDataset[$j]->type == 'date' )
				{
					$worksheet->write( $i+$lngHeadLineCnt, $j, $varCellData, $objFormat);
				}
				elseif( $aryDataset[$j]->type == 'text' )
				{
					// BIFF5形式（shift_jisに変換）
//					$worksheet->writeString( $i+$lngHeadLineCnt, $j, mb_convert_encoding($varCellData,'shift_jis'), $objFormat );
					$worksheet->writeString( $i+$lngHeadLineCnt, $j, mb_convert_encoding($varCellData,'sjis-win'), $objFormat );
				}
				elseif( $aryDataset[$j]->type == 'int' || $aryDataset[$j]->type == 'float' )
				{
					$worksheet->writeNumber( $i+$lngHeadLineCnt, $j, $varCellData, $objFormat );
				}
				else
				{
					$worksheet->writeFormula( $i+$lngHeadLineCnt, $j, $varCellData, $objFormat );
				}
			}

		}
		
		return true;

	}



	/*
	//****************************************************************************
		セルの書式別・フォーマットオブジェクトの生成
	//****************************************************************************
	*/
	function fncSpreadSheetCellFormat(&$workbook, &$objFormat, $varType)
	{


		switch( $varType )
		{
			case 'date':
				if(!isset($objFormat["date"]))
				{
					$objFormat["date"] =& $workbook->addFormat();
					// Date用
					fncSpreadSheetExcelFormatSetting($objFormat["date"], "detail");
					fncSpreadSheetExcelFormatSetting($objFormat["date"], "setFontFamilyMsGothic");
					fncSpreadSheetExcelFormatSetting($objFormat["date"], "setSize9");
					fncSpreadSheetExcelFormatSetting($objFormat["date"], "setHAlignRight");
					fncSpreadSheetExcelFormatSetting($objFormat["date"], "setNumFormatDate");
				}
				return $objFormat["date"];
				break;
			
			case 'int':
			case 'float':
				if(!isset($objFormat["number"]))
				{
					$objFormat["number"] =& $workbook->addFormat();
					fncSpreadSheetExcelFormatSetting($objFormat["number"], "detail");
					fncSpreadSheetExcelFormatSetting($objFormat["number"], "setFontFamilyMsGothic");
					fncSpreadSheetExcelFormatSetting($objFormat["number"], "setSize9");
					fncSpreadSheetExcelFormatSetting($objFormat["number"], "setHAlignRight");
					fncSpreadSheetExcelFormatSetting($objFormat["number"], "setNumFormatNumber");
				}
				return $objFormat["number"];
				break;

			case 'numberCalc':
				if(!isset($objFormat["numberCalc"]))
				{
					$objFormat["numberCalc"] =& $workbook->addFormat();
					fncSpreadSheetExcelFormatSetting($objFormat["numberCalc"], "detail");
					fncSpreadSheetExcelFormatSetting($objFormat["numberCalc"], "setFontFamilyMsGothic");
					fncSpreadSheetExcelFormatSetting($objFormat["numberCalc"], "setSize9");
					fncSpreadSheetExcelFormatSetting($objFormat["numberCalc"], "setHAlignRight");
					fncSpreadSheetExcelFormatSetting($objFormat["numberCalc"], "setNumFormatNumber");
					fncSpreadSheetExcelFormatSetting($objFormat["numberCalc"], "setFgColor40");
				}
				return $objFormat["numberCalc"];
				break;
			case 'numberCalcDec':
				if(!isset($objFormat["numberCalcDec"]))
				{
					$objFormat["numberCalcDec"] =& $workbook->addFormat();
					fncSpreadSheetExcelFormatSetting($objFormat["numberCalcDec"], "detail");
					fncSpreadSheetExcelFormatSetting($objFormat["numberCalcDec"], "setFontFamilyMsGothic");
					fncSpreadSheetExcelFormatSetting($objFormat["numberCalcDec"], "setSize9");
					fncSpreadSheetExcelFormatSetting($objFormat["numberCalcDec"], "setHAlignRight");
					fncSpreadSheetExcelFormatSetting($objFormat["numberCalcDec"], "setNumFormatNumberDec");
					fncSpreadSheetExcelFormatSetting($objFormat["numberCalcDec"], "setFgColor40");
				}
				return $objFormat["numberCalcDec"];
				break;
				

			case 'numberDec':
				if(!isset($objFormat["numberDec"]))
				{
					$objFormat["numberDec"] =& $workbook->addFormat();
					fncSpreadSheetExcelFormatSetting($objFormat["numberDec"], "detail");
					fncSpreadSheetExcelFormatSetting($objFormat["numberDec"], "setFontFamilyMsGothic");
					fncSpreadSheetExcelFormatSetting($objFormat["numberDec"], "setSize9");
					fncSpreadSheetExcelFormatSetting($objFormat["numberDec"], "setHAlignRight");
					fncSpreadSheetExcelFormatSetting($objFormat["numberDec"], "setNumFormatNumberDec");
				}
				return $objFormat["numberDec"];
				break;

			case 'numberPercent':
				if(!isset($objFormat["numberPercent"]))
				{
					$objFormat["numberPercent"] =& $workbook->addFormat();
					fncSpreadSheetExcelFormatSetting($objFormat["numberPercent"], "detail");
					fncSpreadSheetExcelFormatSetting($objFormat["numberPercent"], "setFontFamilyMsGothic");
					fncSpreadSheetExcelFormatSetting($objFormat["numberPercent"], "setSize9");
					fncSpreadSheetExcelFormatSetting($objFormat["numberPercent"], "setHAlignRight");
					fncSpreadSheetExcelFormatSetting($objFormat["numberPercent"], "setNumFormatnumberPercent");
				}
				return $objFormat["numberPercent"];
				break;
				
//by kou 20090611 目立つ列に背景つける				
			case 'numberCalcPoint':
				if(!isset($objFormat["numberCalcPoint"]))
				{
					$objFormat["numberCalcPoint"] =& $workbook->addFormat();
					fncSpreadSheetExcelFormatSetting($objFormat["numberCalcPoint"], "detail");
					fncSpreadSheetExcelFormatSetting($objFormat["numberCalcPoint"], "setFontFamilyMsGothic");
					fncSpreadSheetExcelFormatSetting($objFormat["numberCalcPoint"], "setSize9");
					fncSpreadSheetExcelFormatSetting($objFormat["numberCalcPoint"], "setHAlignRight");
					fncSpreadSheetExcelFormatSetting($objFormat["numberCalcPoint"], "setNumFormatNumber");
					fncSpreadSheetExcelFormatSetting($objFormat["numberCalcPoint"], "setFgColor43");
				}
				return $objFormat["numberCalcPoint"];
				break;

			case 'numberDecPoint':
				if(!isset($objFormat["numberDecPoint"]))
				{
					$objFormat["numberDecPoint"] =& $workbook->addFormat();
					fncSpreadSheetExcelFormatSetting($objFormat["numberDecPoint"], "detail");
					fncSpreadSheetExcelFormatSetting($objFormat["numberDecPoint"], "setFontFamilyMsGothic");
					fncSpreadSheetExcelFormatSetting($objFormat["numberDecPoint"], "setSize9");
					fncSpreadSheetExcelFormatSetting($objFormat["numberDecPoint"], "setHAlignRight");
					fncSpreadSheetExcelFormatSetting($objFormat["numberDecPoint"], "setNumFormatNumberDec");
					fncSpreadSheetExcelFormatSetting($objFormat["numberDecPoint"], "setFgColor43");
				}
				return $objFormat["numberDecPoint"];
				break;

			case 'textPoint':
				if(!isset($objFormat["textPoint"]))
				{
					$objFormat["textPoint"] =& $workbook->addFormat();
					fncSpreadSheetExcelFormatSetting($objFormat["textPoint"], "detail");
					fncSpreadSheetExcelFormatSetting($objFormat["textPoint"], "setFontFamilyMsGothic");
					fncSpreadSheetExcelFormatSetting($objFormat["textPoint"], "setSize9");
					fncSpreadSheetExcelFormatSetting($objFormat["textPoint"], "setHAlignLeft");
					fncSpreadSheetExcelFormatSetting($objFormat["textPoint"], "setFgColor43");
				}
				return $objFormat["textPoint"];
				break;
//20090611 end
/*			case 'error':
				if(!isset($objFormat["error"]))
				{
					$objFormat["error"] =& $workbook->addFormat();
					fncSpreadSheetExcelFormatSetting($objFormat["error"], "detail");
					fncSpreadSheetExcelFormatSetting($objFormat["error"], "setFontFamilyMsGothic");
					fncSpreadSheetExcelFormatSetting($objFormat["error"], "setSize9");
					fncSpreadSheetExcelFormatSetting($objFormat["error"], "setHAlignLeft");
					fncSpreadSheetExcelFormatSetting($objFormat["error"], "setFgColor45");
				}
				return $objFormat["error"];
				break;
*/
			case 'errorNumberDec':
				if(!isset($objFormat["errorNumberDec"]))
				{
					$objFormat["errorNumberDec"] =& $workbook->addFormat();
					fncSpreadSheetExcelFormatSetting($objFormat["errorNumberDec"], "detail");
					fncSpreadSheetExcelFormatSetting($objFormat["errorNumberDec"], "setFontFamilyMsGothic");
					fncSpreadSheetExcelFormatSetting($objFormat["errorNumberDec"], "setSize9");
					fncSpreadSheetExcelFormatSetting($objFormat["errorNumberDec"], "setHAlignRight");
					fncSpreadSheetExcelFormatSetting($objFormat["errorNumberDec"], "setNumFormatNumberDec");
					fncSpreadSheetExcelFormatSetting($objFormat["errorNumberDec"], "setFgColor45");
				}
				return $objFormat["errorNumberDec"];
				break;
				
				case 'errorNumber':
				if(!isset($objFormat["errorNumber"]))
				{
					$objFormat["errorNumber"] =& $workbook->addFormat();
					fncSpreadSheetExcelFormatSetting($objFormat["errorNumber"], "detail");
					fncSpreadSheetExcelFormatSetting($objFormat["errorNumber"], "setFontFamilyMsGothic");
					fncSpreadSheetExcelFormatSetting($objFormat["errorNumber"], "setSize9");
					fncSpreadSheetExcelFormatSetting($objFormat["errorNumber"], "setHAlignRight");
					fncSpreadSheetExcelFormatSetting($objFormat["errorNumber"], "setNumFormatNumber");
					fncSpreadSheetExcelFormatSetting($objFormat["errorNumber"], "setFgColor45");
				}
				return $objFormat["errorNumber"];
				break;
			
			case 'text':
			default:
				if(!isset($objFormat["text"]))
				{
					$objFormat["text"] =& $workbook->addFormat();
					fncSpreadSheetExcelFormatSetting($objFormat["text"], "detail");
					fncSpreadSheetExcelFormatSetting($objFormat["text"], "setFontFamilyMsGothic");
					fncSpreadSheetExcelFormatSetting($objFormat["text"], "setSize9");
					fncSpreadSheetExcelFormatSetting($objFormat["text"], "setHAlignLeft");
				}
				return $objFormat["text"];
				break;
		}

		return false;
		
	}

	/*
	//****************************************************************************
	//	Excel のセルフォーマットを生成
	//	 
	//****************************************************************************
	*/
	function fncSpreadSheetExcelFormatSetting(&$objFormat, $strFormat)
	{
		// fromat setting
		switch($strFormat)
		{
			case "header0":
				// $objFormat->setFgColor('yellow');
				$objFormat->setHAlign('left');
				break;

			case "header":
				$objFormat->setFgColor('yellow');
				$objFormat->setHAlign('center');
				$objFormat->setBold(1);
				$objFormat->setBorder(1);
				break;
				
			case "detail":
				$objFormat->setFgColor('white');
				$objFormat->setHAlign('left');
				//$objFormat->setBold(1);
				$objFormat->setBorder(1);
				break;

			case "setFgColor40":
				$objFormat->setFgColor('40');
				break;
				
			case "setFgColor43":
				$objFormat->setFgColor('43');
				break;

			case "setFgColor45":
				$objFormat->setFgColor('45');
				break;
				
			case "setFontFamilyTimes":
				$objFormat->setFontFamily("Times New Roman");
				
				break;
				
			case "setFontFamilyMsGothic":
				$objFormat->setFontFamily(mb_convert_encoding("ＭＳ ゴシック", "shift_jis"));
//				$objFormat->setFontFamily("ＭＳ ゴシック");
				break;
				
			case "setSize9":
				$objFormat->setSize(9);
				break;
				
			case "setHAlignRight":
				$objFormat->setHAlign('right');
				break;
				
			case "setHAlignLeft":
				$objFormat->setHAlign('left');
				break;
				
			case "setNumFormatDate":
				$objFormat->setNumFormat("yyyy/m/d");
				break;
				
			case "setNumFormatNumber":
				$objFormat->setNumFormat("##,###,###,###,###");
				break;
				
			case "setNumFormatNumberDec":
				$objFormat->setNumFormat("##,###,###,###,##0.00");
				break;
			
			case "setNumFormatnumberPercent":
				$objFormat->setNumFormat("##,###,###,###,##0.00%");
				break;
			default:
		}
		
		return true;
		
	}



	function fncSpreadSheetSearchDatasetName($aryDataset, $strColumnName)
	{

		for( $i = 0; $i < count($aryDataset); $i++ )
		{
			if( $aryDataset[$i]["name"] == $strColumnName ) break;
		}
		if( $i < count($aryDataset) ) return $i;
		return null;
	}


	/*
	//****************************************************************************
	//	ワークブックの生成
	//	
	//****************************************************************************
	*/
	function fncSpreadSheetCreateWorkbook()
	{

	    /**
	     * ワークブックの作成
	     */
	    $workbook = new Spreadsheet_Excel_Writer();
	    
		// 将来的に DIFF8形式の出力が必要となった場合、setVersion(8) の指定が必要となる
		//$workbook->setVersion(8);

		return $workbook;
	}
	

	/*
	//****************************************************************************
	//	SigmaGrid用のヘッダー情報を生成する
	//	
	//****************************************************************************
	*/
	function fncSpreadSheetHeaderJson($lngExportData)
	{
		$strDirName = SRC_ROOT."dataex/result/spreadsheet/";
		$aryFileName = array();
		
		switch ($lngExportData)
		{
			case 6:
				$aryFileName[0] = $strDirName . "stat01_dataset.txt";
				$aryFileName[1] = $strDirName . "stat01_colmuns.txt";
				break;
			case 7:
				$aryFileName[0] = $strDirName . "stat02_dataset.txt";
				$aryFileName[1] = $strDirName . "stat02_colmuns.txt";
				break;
		}

		$strDataSet = "";
		$strColumns = "";
		
		if ( !$strDataSet = file_get_contents ($aryFileName[0]) )
		{
			return false;
		}
		if ( !$strColumns = file_get_contents ($aryFileName[1]) )
		{
			return false;
		}

		return array('DATASET'=>$strDataSet, 'COLUMNS'=>$strColumns);
	}



	/*
	//****************************************************************************
	//	
	//	
	//****************************************************************************
	*/
	function fncTestJson()
	{
		$arr = array( 
		    array( 
		        "name" => "クワガタ", 
		        "url"  => "http://www.kuwagata.co.jp/" 
		    ), 
		    array( 
		        "name" => "くわがた", 
		        "url"  => "http://www.kuwagata.co.jp/" 
		    ) 
		); 

		$json = new Services_JSON; 
		$encode = $json->encode($arr); 
		header("Content-Type: text/javascript; charset=utf-8"); 
		echo $encode; 
	}
?>