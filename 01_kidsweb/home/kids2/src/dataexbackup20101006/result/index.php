<?

// ----------------------------------------------------------------------------
/**
*       データエクスポート 出力画面
*
*
*       @package    K.I.D.S.
*       @license    http://www.kuwagata.co.jp/
*       @copyright  KUWAGATA CO., LTD.
*       @author     K.I.D.S. Groups <info@kids-groups.com>
*       @access     public
*       @version    2.00
*
*
*       処理概要
*         ・メニュー画面を表示
*
*       更新履歴
*
*/
// ----------------------------------------------------------------------------


	// 設定読み込み
	include_once('conf.inc');

	// ライブラリ読み込み
	require (LIB_FILE);
	require (SRC_ROOT . "dataex/cmn/lib_dataex.php");

	// DB接続
	$objDB   = new clsDB();
	$objAuth = new clsAuth();
	$objDB->open( "", "", "", "" );
//文字化け対策20090610
	$objDB->setInputEncoding("utf-8");
//まで

	// データ取得
	$aryData = $_POST;

	// セッション確認
	$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

	// 権限確認のための出力対象の機能コードを取得
	$lngFunctionCode = getFunctionCode( $aryData["lngExportData"] );

	// 権限確認
	if ( !fncCheckAuthority( $lngFunctionCode, $objAuth ) )
	{
		fncOutputError ( 9052, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
	}


	$aryCheck["strSessionID"]             = "null:numenglish(32,32)";
	$aryCheck["lngExportData"]            = "null:number(DEF_EXPORT_SALES, DEF_EXPORT_ESTIMATE)";
	$aryCheck["lngActionCode"]            = "null:number(1,2)";

	// 見積原価書のみ
	if ( $aryData["lngExportData"] == DEF_EXPORT_ESTIMATE )
	{
		$aryCheck["strProductCode"] = "numenglish(0,4)";
	}
	// 他
	else
	{
		$aryCheck["lngExportConditions"]      = "null:number(1,3)";
		$aryCheck["dtmAppropriationDateFrom"] = "date(/)";
		$aryCheck["dtmAppropriationDateTo"]   = "date(/)";
	}
	if ( $aryData["lngExportData"] == DEF_EXPORT_LC )
	{
		$aryCheck["strOrderCodeFrom"] = "number(0,2147483647)";
		$aryCheck["strOrderCodeTo"]   = "number(0,2147483647)";
	}
	
	// 文字列チェック
	$aryCheckResult = fncAllCheck( $aryData, $aryCheck );
	fncPutStringCheckError( $aryCheckResult, $objDB );

	switch($aryData["lngExportData"])
	{
		case DEF_EXPORT_SALES;	// 1
			
			///////////////////////////////////////////////////////////////////////////
			// 設定 ( 添え字は lngExportData と連動 )
			///////////////////////////////////////////////////////////////////////////
			// 売上レシピ
			///////////////////////////////////////////////////////////////////////////
			$aryExportDataInfo[1]["filename"] = "tsv_sales";
			// ORDER BY の設定
			$aryExportDataInfo[1]["lngExportConditions"][1] = "g.strGroupDisplayCode, c.strCompanyDisplayCode, sa.strSalesCode";
			$aryExportDataInfo[1]["lngExportConditions"][2] = "g.strGroupDisplayCode, p.lngProductNo, sa.strSalesCode";
			
			break;
			
		case DEF_EXPORT_PURCHASE;	// 2
			///////////////////////////////////////////////////////////////////////////
			// Purchase Recipe
			///////////////////////////////////////////////////////////////////////////
			$aryExportDataInfo[2]["filename"] = "tsv_purchase";
			// lngExportConditions の設定
			$aryExportDataInfo[2]["lngExportConditions"][1] = "AND s.lngPayConditionCode = " . DEF_PAYCONDITION_LC 
				. " ORDER BY s.lngMonetaryUnitCode, c.strCompanyDisplayCode, g.strGroupDisplayCode, sd.strProductCode";
			$aryExportDataInfo[2]["lngExportConditions"][2] = "AND s.lngPayConditionCode = " . DEF_PAYCONDITION_TT 
				. " ORDER BY s.lngMonetaryUnitCode, c.strCompanyDisplayCode, g.strGroupDisplayCode, sd.strProductCode";
			$aryExportDataInfo[2]["lngExportConditions"][3] = "AND date_trunc( 'month', s.dtmAppropriationDate ) < date_trunc( 'month', s.dtmExpirationDate )"
				. " ORDER BY s.lngMonetaryUnitCode, g.strGroupDisplayCode, sd.strProductCode";

			break;

		case DEF_EXPORT_LC;		// 3
			///////////////////////////////////////////////////////////////////////////
			// L/C予定表情報
			///////////////////////////////////////////////////////////////////////////
			$aryExportDataInfo[3]["filename"] = "order";
			$aryExportDataInfo[3]["lngExportConditions"][1] = "o.lngPayConditionCode = " . DEF_PAYCONDITION_LC
				. " AND 0 = o.lngRevisionNo "
				. " AND date_trunc ( 'day', o.dtmInsertDate ) >= '_%dtmAppropriationDateFrom%_' "
				. " AND date_trunc ( 'day', o.dtmInsertDate ) <= '_%dtmAppropriationDateTo%_' ";
			$aryExportDataInfo[3]["lngExportConditions"][2] 
				= " ( ( o.lngRevisionNo > 0"
				. " AND o.lngRevisionNo = "
				. "( SELECT MAX ( o1.lngRevisionNo ) FROM m_Order o1 WHERE o.strOrderCode = o1.strOrderCode "
				. " AND date_trunc ( 'day', o1.dtmInsertDate ) >= '_%dtmAppropriationDateFrom%_' "
				. " AND date_trunc ( 'day', o1.dtmInsertDate ) <= '_%dtmAppropriationDateTo%_' ) "
				. " ) OR ( "
				. " o.lngRevisionNo < 0"
				. " AND o.lngRevisionNo = "
				. "( SELECT MIN ( o3.lngRevisionNo ) FROM m_Order o3 WHERE o.strOrderCode = o3.strOrderCode "
				. " AND date_trunc ( 'day', o3.dtmInsertDate ) >= '_%dtmAppropriationDateFrom%_' "
				. " AND date_trunc ( 'day', o3.dtmInsertDate ) <= '_%dtmAppropriationDateTo%_' ) "
				. " ) ) ";

			break;

		case DEF_EXPORT_STOCK;		// 4
			///////////////////////////////////////////////////////////////////////////
			// 仕入一覧表
			///////////////////////////////////////////////////////////////////////////
			$aryExportDataInfo[4]["filename"] = "tsv_stock";
			// $aryExportDataInfo[4]["lngExportConditions"][1] = "sd.lngStockSubjectCode, c.strCompanyDisplayCode";
			$aryExportDataInfo[4]["lngExportConditions"][1] = "c.strCompanyDisplayCode, sd.lngStockSubjectCode";
			$aryExportDataInfo[4]["lngExportConditions"][2] = "sd.lngStockSubjectCode, g.strGroupDisplayCode, p.strProductCode";

			break;
		
		case DEF_EXPORT_ESTIMATE;	// 5
			///////////////////////////////////////////////////////////////////////////
			// 見積原価書
			///////////////////////////////////////////////////////////////////////////
			$aryExportDataInfo[5]["filename"] = "estimate";
			$aryData["lngExportConditions"] =1;
			$aryExportDataInfo[5]["strProductCode"][1] = $aryData["strProductCode"];
	
			break;
	}

	// 期間指定設定
	$aryExportDataInfo[$aryData["lngExportData"]]["dtmAppropriationDateFrom"][$aryData["lngExportConditions"]] = $aryData["dtmAppropriationDateFrom"];
	$aryExportDataInfo[$aryData["lngExportData"]]["dtmAppropriationDateTo"][$aryData["lngExportConditions"]]   = $aryData["dtmAppropriationDateTo"];

	// 発注NO範囲指定
	$aryExportDataInfo[$aryData["lngExportData"]]["strOrderCodeFrom"][$aryData["lngExportConditions"]] = $aryData["strOrderCodeFrom"];
	$aryExportDataInfo[$aryData["lngExportData"]]["strOrderCodeTo"][$aryData["lngExportConditions"]]   = $aryData["strOrderCodeTo"];


	// クエリファイルオープン
	if ( !$strQuery = file_get_contents ( DEF_QUERY_ROOT . $aryExportDataInfo[$aryData["lngExportData"]]["filename"] . ".sql" ) )
	{
		fncOutputError ( 9059, DEF_FATAL, "ファイルオープンに失敗しました。", TRUE, "", $objDB );
	}


	// コメント(//タイプ)の削除
	$strQuery = preg_replace ( "/\/\/.+?\n/", "", $strQuery );
	// 2つのスペース、改行、タブをスペース1つに変換
	$strQuery = preg_replace ( "/(\s{2}|\n|\t)/", " ", $strQuery );
	// コメント(/**/タイプ)の削除
	$strQuery = preg_replace ( "/\/\*.+?\*\//m", "", $strQuery );


	// 置き換え文字列の置換
	$aryKeys = array_keys ( $aryExportDataInfo[$aryData["lngExportData"]] );
	foreach ( $aryKeys as $strKey )
	{
		$strQuery = preg_replace ( "/_%" . $strKey . "%_/", $aryExportDataInfo[$aryData["lngExportData"]][$strKey][$aryData["lngExportConditions"]], $strQuery );
	}
	unset ( $aryKey );



	// 置き換えられなかった変数の WHERE 句、修正
	$strQuery = preg_replace ( "/AND [\w\._\(\)', ]+? [<>]?= '%??%??'/", "", $strQuery );

	// \ の処理(DB 問い合わせのため \ を \\ にする)
	$strQuery = preg_replace ( "/\\\\/", "\\\\\\\\", $strQuery );


	// タイトルの挿入
	$strMasterData = $aryTitleName[$aryData["lngExportData"]][$aryData["lngExportConditions"]];
	// 検索指定内容の挿入
	$strMasterData .= "　";
	if ( $aryData["dtmAppropriationDateFrom"] != "" or $aryData["dtmAppropriationDateTo"] != "" )
	{
		$strMasterData .= "期間" . $aryData["dtmAppropriationDateFrom"] . " ～ " . $aryData["dtmAppropriationDateTo"];
	}
	if ( $aryData["strOrderCodeFrom"] != "" or $aryData["strOrderCodeTo"] != "" )
	{
		$strMasterData .= "発注No" . $aryData["strOrderCodeFrom"] . " ～ " . $aryData["strOrderCodeTo"];
	}
	$strMasterData .= "\n";

	// カラム名セット
	$strMasterData .= join ( "\t", $aryColumnName[$aryData["lngExportData"]] ) . "\n";


	// データ文字列取得(***,***\n)
	$strMasterData .= fncGetExportMasterData( $strQuery, $aryData, $objDB );
	if ( !empty($strMasterData) )
	{
		// マスターデータ出力
		//echo "<html><head><meta http-equiv=content-type content=text/html; charset=euc-jp></head>";

		// データ表示
		if ( $aryData["lngActionCode"] == 1 )
		{
			echo "<pre>";
			echo $strMasterData;
			echo "</pre>";
		}
		// データダウンロード
		elseif ( $aryData["lngActionCode"] == 2 )
		{
			// テンポラリファイル作成
			$strTmpFileName = tempnam ( "", "FOO" );
			$fp = fopen ( $strTmpFileName, "w" );

			// 文字コード変換(EUC->SJIS) UTF-8へ
//文字化け対策20090610
//			$strMasterData = mb_convert_encoding( $strMasterData, "SJIS", "EUC-JP" );

			$strMasterData = mb_convert_encoding( $strMasterData, "sjis-win", "utf-8" );
//まで
			fwrite( $fp, $strMasterData );
			fclose( $fp );
			
			// ダウンロードダイアログ表示
			header ( "Content-Disposition: attachment; filename=export.tsv" );
			header ( "Content-Type: application/tsv" );
			readfile ( $strTmpFileName );

			unlink( $strTmpFileName );
		}
	}



	/**
	*	データ取得クエリを実行する関数
	*
	*	マスターデータ取得クエリを実行、データ取得しCSV形式で生成
	*
	*	@param  String $strQuery      クエリ
	*	@param  Array  $aryData       リクエストデータ
	*	@param  Object $objDB         DBオブジェクト
	*	@return String $strMasterData テーブルデータ
	*	@access public
	*/
	function fncGetExportMasterData( $strQuery, $aryData, $objDB )
	{
		// マスタ取得クエリ実行
		if ( !$result = $objDB->execute( $strQuery ) )
		{
			echo "id\tname1\nマスターデータの結果取得に失敗しました。\n";
		    exit;
		}

		$lngResultRows = pg_Num_Rows( $result );

		if ( $lngResultRows > 0 )
		{
			$lngFieldNum = $objDB->getFieldsCount( $result );

			$strMasterData = fncGetExportResult( $result, $lngResultRows, $lngFieldNum, $aryData["lngExportData"], $aryData["lngExportConditions"], $objDB );
		}
		else
		{
			$strMasterData .= "";
		}
		return $strMasterData;

	}



	/**
	*	データ集計・取得関数
	*
	*	データを集計し、取得する関数
	*
	*	@param  Integer $lngResultID         SQL結果ID
	*	@param  Integer $lngResultRows       結果行数
	*	@param  Integer $lngFieldNum         結果カラム数
	*	@param  Integer $lngExportData       出力対象データ
	*	@param  Integer $lngExportConditions 出力条件
	*	@param  Object  $objDB               DBオブジェクト
	*	@return String  $strMasterData       テーブルデータ
	*	@access public
	*/
	function fncGetExportResult( $lngResultID, $lngResultRows, $lngFieldNum, $lngExportData, $lngExportConditions, $objDB )
	{
		/////////////////////////////////////////////////////////////////////
		// 売上レシピ(部門・顧客別)
		/////////////////////////////////////////////////////////////////////
		if ( $lngExportData == DEF_EXPORT_SALES && $lngExportConditions == 1 )
		{
			// 結果を成形
			for ( $i = 0; $i < $lngResultRows; $i++ )
			{

				// 行結果取得
				$aryResult = $objDB->fetchArray ( $lngResultID, $i );
				$objResult = $objDB->fetchObject ( $lngResultID, $i );

				////////////////////////////////////////////////////////
				// 集計のための処理
				////////////////////////////////////////////////////////
				// 一つ前の企業コードと比較して違う、
				// 「顧客別集計」を出力
				if ( ( $strCompanyDisplayCode != $objResult->strcompanydisplaycode ) && $i != 0 )
				{
					$strMasterData .=  str_repeat ( "\t", 16 ) . "顧客計\t" . sprintf ("%9.2f", $arySubTotalPrice[$strGroupDisplayCode][$strCompanyDisplayCode] ) . "\t" . sprintf ("%9.2f", $aryTaxPrice[$strGroupDisplayCode][$strCompanyDisplayCode] ) . "\t" . sprintf ("%9.2f", $aryTotalPrice[$strGroupDisplayCode][$strCompanyDisplayCode] ) . "\n";
				}

				// 一つ前の部門コードが違う場合、
				// [部門コード][企業コード]をキーとする2次元連想配列にもつ
				// 「部門別集計」を出力
				if ( $strGroupDisplayCode != $objResult->strgroupdisplaycode && $i != 0 )
				{
					$strMasterData .= str_repeat ( "\t", 16 ) . "部門計\t" . sprintf ("%9.2f", array_sum ( $arySubTotalPrice[$strGroupDisplayCode] ) ) . "\t" . sprintf ("%9.2f", array_sum ( $aryTaxPrice[$strGroupDisplayCode] ) ) . "\t" . sprintf ("%9.2f", array_sum ( $aryTotalPrice[$strGroupDisplayCode] ) ) . "\n";
				}
				// 部門コードを保持
				$strGroupDisplayCode   = $objResult->strgroupdisplaycode;
				// 企業コードを保持
				$strCompanyDisplayCode = $objResult->strcompanydisplaycode;

				// [部門コード][企業コード]をキーとする2次元連想配列生成
				$arySubTotalPrice[$objResult->strgroupdisplaycode][$objResult->strcompanydisplaycode] += $objResult->cursubtotalprice;
				$aryTaxPrice[$objResult->strgroupdisplaycode][$objResult->strcompanydisplaycode]      += $objResult->curtaxprice;
				$aryTotalPrice[$objResult->strgroupdisplaycode][$objResult->strcompanydisplaycode]    += $objResult->curtotalprice;

				// 総計集計配列
				$arySumPrice["curSubTotalPrice"] += $objResult->cursubtotalprice;
				$arySumPrice["curTaxPrice"]      += $objResult->curtaxprice;
				$arySumPrice["curTotalPrice"]    += $objResult->curtotalprice;

				// カラム数表示
				for ( $j = 0; $j < $lngFieldNum; $j++ )
				{
					$aryResult[$j] = preg_replace ( "/\s+?$/", "", $aryResult[$j] );// 空白削除
					$aryExportData[] = $aryResult[$j];
				}

				$strMasterData .= join ( "\t", $aryExportData ) . "\n";
				unset ( $aryExportData );
			}

			// 最終行に対する集計処理
			$strMasterData .=  str_repeat ( "\t", 16 ) . "顧客計\t" . sprintf ("%9.2f", $arySubTotalPrice[$strGroupDisplayCode][$strCompanyDisplayCode] ) . "\t" . sprintf ("%9.2f", $aryTaxPrice[$strGroupDisplayCode][$strCompanyDisplayCode] ) . "\t" . sprintf ("%9.2f", $aryTotalPrice[$strGroupDisplayCode][$strCompanyDisplayCode] ) . "\n";
			$strMasterData .= str_repeat ( "\t", 16 ) . "部門計\t" . sprintf ("%9.2f", array_sum ( $arySubTotalPrice[$strGroupDisplayCode] ) ) . "\t" . sprintf ("%9.2f", array_sum ( $aryTaxPrice[$strGroupDisplayCode] ) ) . "\t" . sprintf ("%9.2f", array_sum ( $aryTotalPrice[$strGroupDisplayCode] ) ) . "\n";
			// 総計出力
			$strMasterData .= str_repeat ( "\t", 16 ) . "総計\t" . sprintf ("%9.2f", $arySumPrice["curSubTotalPrice"] ) . "\t" . sprintf ("%9.2f", $arySumPrice["curTaxPrice"] ) . "\t" . sprintf ("%9.2f", $arySumPrice["curTotalPrice"] ) . "\n";

		}
		/////////////////////////////////////////////////////////////////////
		// 売上レシピ(部門・製品別)
		/////////////////////////////////////////////////////////////////////
		elseif ( $lngExportData == DEF_EXPORT_SALES && $lngExportConditions == 2 )
		{
			// 結果を成形
			for ( $i = 0; $i < $lngResultRows; $i++ )
			{

				// 行結果取得
				$aryResult = $objDB->fetchArray ( $lngResultID, $i );
				$objResult = $objDB->fetchObject ( $lngResultID, $i );

				////////////////////////////////////////////////////////
				// 集計のための処理
				////////////////////////////////////////////////////////
				// 一つ前の製品と比較して違う、
				// 「製品」別集計」を出力
				if ( ( $strProductCode != $objResult->strproductcode ) && $i != 0 )
				{
					$strMasterData .= str_repeat ( "\t", 15 ) . "製品計\t" . sprintf ("%9.2f", $aryProductQuantity[$strGroupDisplayCode][$strProductCode] ) . "\t" . sprintf ("%9.2f", $arySubTotalPrice[$strGroupDisplayCode][$strProductCode] ) . "\t" . sprintf ("%9.2f", $aryTaxPrice[$strGroupDisplayCode][$strProductCode] ) . "\t" . sprintf ("%9.2f", $aryTotalPrice[$strGroupDisplayCode][$strProductCode] ) . "\n";
				}


				// 一つ前の部門コードが違う場合、
				// 「部門別集計」を出力
				if ( $strGroupDisplayCode != $objResult->strgroupdisplaycode && $i != 0 )
				{
					$strMasterData .= str_repeat ( "\t", 16 ) . "部門計\t" . sprintf ("%9.2f", array_sum ( $arySubTotalPrice[$strGroupDisplayCode] ) ) . "\t" . sprintf ("%9.2f", array_sum ( $aryTaxPrice[$strGroupDisplayCode] ) ) . "\t" . sprintf ("%9.2f", array_sum ( $aryTotalPrice[$strGroupDisplayCode] ) ) . "\n";
				}
				// 部門コードを保持
				$strGroupDisplayCode   = $objResult->strgroupdisplaycode;
				// 製品を保持
				$strProductCode = $objResult->strproductcode;

				// [部門コード][製品]をキーとする2次元連想配列生成
				$aryProductQuantity[$objResult->strgroupdisplaycode][$objResult->strproductcode] += $objResult->lngproductquantity;
				$arySubTotalPrice[$objResult->strgroupdisplaycode][$objResult->strproductcode]   += $objResult->cursubtotalprice;
				$aryTaxPrice[$objResult->strgroupdisplaycode][$objResult->strproductcode]        += $objResult->curtaxprice;
				$aryTotalPrice[$objResult->strgroupdisplaycode][$objResult->strproductcode]      += $objResult->curtotalprice;

				// 総計集計配列
				$arySumPrice["curSubTotalPrice"] += $objResult->cursubtotalprice;
				$arySumPrice["curTaxPrice"]      += $objResult->curtaxprice;
				$arySumPrice["curTotalPrice"]    += $objResult->curtotalprice;

				// カラム数表示
				for ( $j = 0; $j < $lngFieldNum; $j++ )
				{
					$aryResult[$j] = preg_replace ( "/\s+?$/", "", $aryResult[$j] );// 空白削除
					$aryExportData[] = $aryResult[$j];
				}

				$strMasterData .= join ( "\t", $aryExportData ) . "\n";
				unset ( $aryExportData );
			}

			// 最終行に対する集計処理
			$strMasterData .= str_repeat ( "\t", 15 ) . "製品計\t" . sprintf ("%9.2f", $aryProductQuantity[$strGroupDisplayCode][$strProductCode] ) . "\t" . sprintf ("%9.2f", $arySubTotalPrice[$strGroupDisplayCode][$strProductCode] ) . "\t" . sprintf ("%9.2f", $aryTaxPrice[$strGroupDisplayCode][$strProductCode] ) . "\t" . sprintf ("%9.2f", $aryTotalPrice[$strGroupDisplayCode][$strProductCode] ) . "\n";
			$strMasterData .= str_repeat ( "\t", 16 ) . "部門計\t" . sprintf ("%9.2f", array_sum ( $arySubTotalPrice[$strGroupDisplayCode] ) ) . "\t" . sprintf ("%9.2f", array_sum ( $aryTaxPrice[$strGroupDisplayCode] ) ) . "\t" . sprintf ("%9.2f", array_sum ( $aryTotalPrice[$strGroupDisplayCode] ) ) . "\n";
			// 総計出力
			$strMasterData .= str_repeat ( "\t", 16 ) . "総計\t" . sprintf ("%9.2f", $arySumPrice["curSubTotalPrice"] ) . "\t" . sprintf ("%9.2f", $arySumPrice["curTaxPrice"] ) . "\t" . sprintf ("%9.2f", $arySumPrice["curTotalPrice"] ) . "\n";

		}
		/////////////////////////////////////////////////////////////////////
		// Purchase Recipe(T/T)(L/C)
		/////////////////////////////////////////////////////////////////////
		elseif ( $lngExportData == DEF_EXPORT_PURCHASE && ( $lngExportConditions == 1 || $lngExportConditions == 2 ) )
		{
			// 結果を成形
			for ( $i = 0; $i < $lngResultRows; $i++ )
			{

				// 行結果取得
				$aryResult = $objDB->fetchArray ( $lngResultID, $i );
				$objResult = $objDB->fetchObject ( $lngResultID, $i );

				////////////////////////////////////////////////////////
				// 集計のための処理
				////////////////////////////////////////////////////////
				// 一つ前の仕入先と比較して違う、
				// 「仕入先別集計」を出力
				if ( ( $strCompanyDisplayCode != $objResult->strcompanydisplaycode ) && $i != 0 )
				{
					$strMasterData .= str_repeat ( "\t", 23 ) . "仕入先計\t" . $aryProductQuantity[$strMonetaryUnitName][$strCompanyDisplayCode] . "\t" . $arySubTotalPrice[$strMonetaryUnitName][$strCompanyDisplayCode] . "\n";
				}

				// 一つ前の通貨コードが違う場合、
				// 「通貨別集計」を出力
				if ( ( $strMonetaryUnitName != $objResult->strmonetaryunitname ) && $i != 0 )
				{
					$strMasterData .= str_repeat ( "\t", 23 ) . "通貨計\t" . array_sum ( $aryProductQuantity[$strMonetaryUnitName] ) . "\t" . array_sum ( $arySubTotalPrice[$strMonetaryUnitName] ) . "\n";
				}
				// 通貨コードを保持
				$strMonetaryUnitName   = $objResult->strmonetaryunitname;
				// 製品を保持
				$strCompanyDisplayCode = $objResult->strcompanydisplaycode;

				// [部門コード][製品]をキーとする2次元連想配列生成
				$aryProductQuantity[$objResult->strmonetaryunitname][$objResult->strcompanydisplaycode] += $objResult->lngproductquantity;
				$arySubTotalPrice[$objResult->strmonetaryunitname][$objResult->strcompanydisplaycode]   += $objResult->cursubtotalprice;

				// 総計集計配列
				$arySumPrice["lngProductQuantity"] += $objResult->lngproductquantity;
				// 通貨が日本円以外の場合は税抜金額の合計は日本円に換算しなおす

	// 実際にはここで端数処理を行う！！！！！！！！！！！！！！！！！！！！！

				if ( $objResult->curconversionrate != 1 )
				{
					$arySumPrice["curSubTotalPrice"] += $objResult->cursubtotalprice * $objResult->curconversionrate;
				}
				else
				{
					$arySumPrice["curSubTotalPrice"] += $objResult->cursubtotalprice;
				}

				// カラム数表示
				for ( $j = 0; $j < $lngFieldNum; $j++ )
				{
					$aryResult[$j] = preg_replace ( "/\s+?$/", "", $aryResult[$j] );// 空白削除
					$aryExportData[] = $aryResult[$j];
				}

				$strMasterData .= join ( "\t", $aryExportData ) . "\n";
				unset ( $aryExportData );
			}

			// 最終行に対する集計処理
			$strMasterData .= str_repeat ( "\t", 23 ) . "仕入先計\t" . $aryProductQuantity[$strMonetaryUnitName][$strCompanyDisplayCode] . "\t" . $arySubTotalPrice[$strMonetaryUnitName][$strCompanyDisplayCode] . "\n";
			$strMasterData .= str_repeat ( "\t", 23 ) . "通貨計\t" . array_sum ( $aryProductQuantity[$strMonetaryUnitName] ) . "\t" . array_sum ( $arySubTotalPrice[$strMonetaryUnitName] ) . "\n";

			// 総計出力
	//		$strMasterData .= str_repeat ( "\t", 22 ) . "総計(\)\t" . $arySumPrice["lngProductQuantity"] . "\t" . $arySumPrice["curSubTotalPrice"] . "\n";

		}
		/////////////////////////////////////////////////////////////////////
		// Purchase Recipe(On Board)
		/////////////////////////////////////////////////////////////////////
		elseif ( $lngExportData == DEF_EXPORT_PURCHASE && $lngExportConditions == 3 )
		{
			// 結果を成形
			for ( $i = 0; $i < $lngResultRows; $i++ )
			{

				// 行結果取得
				$aryResult = $objDB->fetchArray ( $lngResultID, $i );
				$objResult = $objDB->fetchObject ( $lngResultID, $i );

				////////////////////////////////////////////////////////
				// 集計のための処理
				////////////////////////////////////////////////////////
				// 一つ前の製品と比較して違う、
				// 「製品別集計」を出力
				if ( ( $strProductCode != $objResult->strproductcode ) && $i != 0 )
				{
					$strMasterData .= str_repeat ( "\t", 23 ) . "製品計\t"
						 . $aryProductQuantity[$strMonetaryUnitName][$strCompanyDisplayCode][$strProductCode]
						 . "\t" . $arySubTotalPrice[$strMonetaryUnitName][$strCompanyDisplayCode][$strProductCode] . "\n";
				}

				// 一つ前の仕入先コードが違う場合、
				// 「仕入先別集計」を出力
				if ( ( $strCompanyDisplayCode != $objResult->strcompanydisplaycode ) && $i != 0 )
				{
	//				$strMasterData .= str_repeat ( "\t", 22 ) . "仕入先計\t"
	//					. array_sum ( $aryProductQuantity[$strMonetaryUnitName][$strCompanyDisplayCode] ) 
	//					. "\t" . array_sum ( $arySubTotalPrice[$strMonetaryUnitName][$strCompanyDisplayCode] ) . "\n";
				}

				// 一つ前の通貨コードが違う場合、
				// 「通貨別集計」を出力
				if ( ( $strMonetaryUnitName != $objResult->strmonetaryunitname ) && $i != 0 )
				{
					$strMasterData .= str_repeat ( "\t", 23 ) . "通貨計\t"
						. $aryProductQuantity_M[$strMonetaryUnitName] 
						. "\t" . $arySubTotalPrice_M[$strMonetaryUnitName] . "\n";
				}

				// 通貨コードを保持
				$strMonetaryUnitName   = $objResult->strmonetaryunitname;
				// 製品コードを保持
				$strProductCode        = $objResult->strproductcode;
				// 仕入先コードを保持
				$strCompanyDisplayCode = $objResult->strcompanydisplaycode;

				// [通貨][仕入先][製品]をキーとする3次元連想配列生成
				$aryProductQuantity[$objResult->strmonetaryunitname][$objResult->strcompanydisplaycode][$objResult->strproductcode] 
					+= $objResult->lngproductquantity;
				$arySubTotalPrice[$objResult->strmonetaryunitname][$objResult->strcompanydisplaycode][$objResult->strproductcode] 
					+= $objResult->cursubtotalprice;
				$aryProductQuantity_M[$objResult->strmonetaryunitname] += $objResult->lngproductquantity;
				$arySubTotalPrice_M[$objResult->strmonetaryunitname]   += $objResult->cursubtotalprice;

				// 総計集計配列
				$arySumPrice["lngProductQuantity"] += $objResult->lngproductquantity;
				// 通貨が日本円以外の場合は税抜金額の合計は日本円に換算しなおす

	// 実際にはここで端数処理を行う！！！！！！！！！！！！！！！！！！！！！

				if ( $objResult->curconversionrate != 1 )
				{
					$arySumPrice["curSubTotalPrice"] += $objResult->cursubtotalprice * $objResult->curconversionrate;
				}
				else
				{
					$arySumPrice["curSubTotalPrice"] += $objResult->cursubtotalprice;
				}

				// カラム数表示
				for ( $j = 0; $j < $lngFieldNum; $j++ )
				{
					$aryResult[$j] = preg_replace ( "/\s+?$/", "", $aryResult[$j] );// 空白削除
					$aryExportData[] = $aryResult[$j];
				}

				$strMasterData .= join ( "\t", $aryExportData ) . "\n";
				unset ( $aryExportData );
			}

			// 最終行に対する集計処理
			$strMasterData .= str_repeat ( "\t", 23 ) . "製品計\t" 
				. $aryProductQuantity[$strMonetaryUnitName][$strCompanyDisplayCode][$strProductCode] 
				. "\t" . $arySubTotalPrice[$strMonetaryUnitName][$strCompanyDisplayCode][$strProductCode] . "\n";
	//		$strMasterData .= str_repeat ( "\t", 22 ) . "仕入先計\t" 
	//			. array_sum ( $aryProductQuantity[$strMonetaryUnitName][$strCompanyDisplayCode] ) 
	//			. "\t" . array_sum ( $arySubTotalPrice[$strMonetaryUnitName][$strCompanyDisplayCode] ) . "\n";
			$strMasterData .= str_repeat ( "\t", 23 ) . "通貨計\t" 
				. $aryProductQuantity_M[$strMonetaryUnitName] 
				. "\t" . $arySubTotalPrice_M[$strMonetaryUnitName] . "\n";

			// 総計出力
			$strMasterData .= str_repeat ( "\t", 23 ) . "総計(\)\t" . $arySumPrice["lngProductQuantity"] . "\t" . $arySumPrice["curSubTotalPrice"] . "\n";
		}
		/////////////////////////////////////////////////////////////////////
		// 仕入一覧表(取引先別)
		/////////////////////////////////////////////////////////////////////
		elseif ( $lngExportData == DEF_EXPORT_STOCK && $lngExportConditions == 1 )
		{
			// 結果を成形
			for ( $i = 0; $i < $lngResultRows; $i++ )
			{

				// 行結果取得
				$aryResult = $objDB->fetchArray ( $lngResultID, $i );
				$objResult = $objDB->fetchObject ( $lngResultID, $i );

				////////////////////////////////////////////////////////
				// 集計のための処理
				////////////////////////////////////////////////////////
				// 一つ前の仕入科目コードが違う場合、
				// または仕入先のコードが違う場合、
				// 「仕入科目コードコード別集計」を出力
				if ( ( $strCompanyDisplayCode != $objResult->strcompanydisplaycode 
					|| $lngStockSubjectCode != $objResult->lngstocksubjectcode ) && $i != 0 )
				{
					$strMasterData .= str_repeat ( "\t", 20 ) . "仕入科目計\t" . sprintf ("%9.2f", $arySumSubPrice["curSubTotalPrice"] ) . "\t" . sprintf ("%9.2f", $arySumSubPrice["curTaxPrice"] ) . "\t" . sprintf ("%9.2f", $arySumSubPrice["curTotalPrice"] ) . "\t" . sprintf ("%9.2f", $arySumSubPrice["curTotalPriceTTM"] )  . "\n";
					$arySumSubPrice = Array();
				}

				// 一つ前の仕入先会社コードと比較して違う、
				// 「会社コード別集計」を出力
				if ( ( $strCompanyDisplayCode != $objResult->strcompanydisplaycode ) && $i != 0 )
				{
					$strMasterData .= str_repeat ( "\t", 20 ) . "仕入先計\t" . sprintf ("%9.2f", $arySumCompanyPrice["curSubTotalPrice"] ) . "\t" . sprintf ("%9.2f", $arySumCompanyPrice["curTaxPrice"] ) . "\t" . sprintf ("%9.2f", $arySumCompanyPrice["curTotalPrice"] ) ."\t" . sprintf ("%9.2f", $arySumCompanyPrice["curTotalPriceTTM"] ) . "\n";
					$arySumCompanyPrice = Array();
				}

				// 会社コードを保持
				$strCompanyDisplayCode = $objResult->strcompanydisplaycode;
				// 仕入科目コードを保持
				$lngStockSubjectCode   = $objResult->lngstocksubjectcode;

				// 仕入科目集計
				$arySumSubPrice["curSubTotalPrice"] += $objResult->cursubtotalprice;
				$arySumSubPrice["curTaxPrice"]      += $objResult->curtaxprice;
				$arySumSubPrice["curTotalPrice"]    += $objResult->curtotalprice;
				$arySumSubPrice["curTotalPriceTTM"] += $objResult->curtotalpricettm;

				// 会社コード集計
				$arySumCompanyPrice["curSubTotalPrice"] += $objResult->cursubtotalprice;
				$arySumCompanyPrice["curTaxPrice"]      += $objResult->curtaxprice;
				$arySumCompanyPrice["curTotalPrice"]    += $objResult->curtotalprice;
				$arySumCompanyPrice["curTotalPriceTTM"] += $objResult->curtotalpricettm;

				// 総計
				$arySumPrice["curSubTotalPrice"] += $objResult->cursubtotalprice;
				$arySumPrice["curTaxPrice"]      += $objResult->curtaxprice;
				$arySumPrice["curTotalPrice"]    += $objResult->curtotalprice;
				$arySumPrice["curTotalPriceTTM"] += $objResult->curtotalpricettm;

				// カラム数表示
				for ( $j = 0; $j < $lngFieldNum; $j++ )
				{
					$aryResult[$j] = preg_replace ( "/\s+?$/", "", $aryResult[$j] );// 空白削除
					$aryExportData[] = $aryResult[$j];
				}

				$strMasterData .= join ( "\t", $aryExportData ) . "\n";
				unset ( $aryExportData );
			}

			// 最終行に対する集計処理
			$strMasterData .= str_repeat ( "\t", 20 ) . "仕入科目計\t" . sprintf ("%9.2f", $arySumSubPrice["curSubTotalPrice"] ) . "\t" . sprintf ("%9.2f", $arySumSubPrice["curTaxPrice"] ) . "\t" . sprintf ("%9.2f", $arySumSubPrice["curTotalPrice"] ) . "\t" . sprintf ("%9.2f", $arySumSubPrice["curTotalPriceTTM"] ) . "\n";
			$strMasterData .= str_repeat ( "\t", 20 ) . "仕入先計\t" . sprintf ("%9.2f", $arySumCompanyPrice["curSubTotalPrice"] ) . "\t" . sprintf ("%9.2f", $arySumCompanyPrice["curTaxPrice"] ) . "\t" . sprintf ("%9.2f", $arySumCompanyPrice["curTotalPrice"] ) ."\t" . sprintf ("%9.2f", $arySumCompanyPrice["curTotalPriceTTM"] ) . "\n";

			// 総計出力
			$strMasterData .= str_repeat ( "\t", 20 ) . "総計\t" . sprintf ("%9.2f", $arySumPrice["curSubTotalPrice"] ) . "\t" . sprintf ("%9.2f", $arySumPrice["curTaxPrice"] ) . "\t" . sprintf ("%9.2f", $arySumPrice["curTotalPrice"] ) ."\t" . sprintf ("%9.2f", $arySumPrice["curTotalPriceTTM"] ) . "\n";
		}
		/////////////////////////////////////////////////////////////////////
		// 仕入一覧表(製品分類・部門・製品別)
		/////////////////////////////////////////////////////////////////////
		elseif ( $lngExportData == DEF_EXPORT_STOCK && $lngExportConditions == 2 )
		{
			// 結果を成形
			for ( $i = 0; $i < $lngResultRows; $i++ )
			{

				// 行結果取得
				$aryResult = $objDB->fetchArray ( $lngResultID, $i );
				$objResult = $objDB->fetchObject ( $lngResultID, $i );

				////////////////////////////////////////////////////////
				// 集計のための処理
				////////////////////////////////////////////////////////
				// 一つ前の製品と比較して違う、
				// または一つ前の部門コードが違う、
				// または一つ前の仕入科目コードが違う場合、
				// 「製品別集計」を出力
	//			if ( ( $strProductCode != $objResult->strproductcode || $strGroupDisplayCode != $objResult->strgroupdisplaycode || $lngStockSubjectCode != $objResult->lngstocksubjectcode ) && $i != 0 )
	//			{
	//				$strMasterData .= str_repeat ( "\t", 18 ) . "製品計\t" . sprintf ("%9.2f", $arySumGoodsPrice["curSubTotalPrice"] ) . "\t" . sprintf ("%9.2f", $arySumGoodsPrice["curTaxPrice"] ) . "\t" . sprintf ("%9.2f", $arySumGoodsPrice["curTotalPrice"] ) . "\n";
	//				$arySumGoodsPrice = Array();
	//			}

				// 一つ前の製品と比較して違う、
				// または一つ前の部門コードが違う場合、
				// 「部門コード別集計」を出力
	//			if ( ( $strProductCode != $objResult->strproductcode || $strGroupDisplayCode != $objResult->strgroupdisplaycode ) && $i != 0 )
	//			{
	//				$strMasterData .= str_repeat ( "\t", 18 ) . "部門計\t" . sprintf ("%9.2f", $arySumGroupPrice["curSubTotalPrice"] ) . "\t" . sprintf ("%9.2f", $arySumGroupPrice["curTaxPrice"] ) . "\t" . sprintf ("%9.2f", $arySumGroupPrice["curTotalPrice"] ) . "\n";
	//				$arySumGroupPrice = Array();
	//			}

				// 一つ前の製品と比較して違う場合、
				// 「仕入科目コード別集計」を出力
	//			if ( $strProductCode != $objResult->strproductcode && $i != 0 )
	//			{
	//				$strMasterData .= str_repeat ( "\t", 18 ) . "仕入科目計\t" . sprintf ("%9.2f", $arySumSubPrice["curSubTotalPrice"] ) . "\t" . sprintf ("%9.2f", $arySumSubPrice["curTaxPrice"] ) . "\t" . sprintf ("%9.2f", $arySumSubPrice["curTotalPrice"] ) . "\n";
	//				$arySumSubPrice = Array();
	//			}

				// 一つ前の製品と比較して違う
				// 「製品別集計」を出力
				if ( ( ( $strProductCode != $objResult->strproductcode ) && $i != 0 )||(( $lngStockSubjectCode != $objResult->lngstocksubjectcode )&& $i != 0))
				{
					$strMasterData .= str_repeat ( "\t", 20 ) . "製品計\t" . sprintf ("%9.2f", $arySumGoodsPrice["curSubTotalPrice"] ) . "\t" . sprintf ("%9.2f", $arySumGoodsPrice["curTaxPrice"] ) . "\t" . sprintf ("%9.2f", $arySumGoodsPrice["curTotalPrice"] ) ."\t" . sprintf ("%9.2f", $arySumGoodsPrice["curTotalPriceTTM"] ) . "\n";
					$arySumGoodsPrice = Array();
				}

				// 一つ前の部門コードが違う場合、
				// 「部門コード別集計」を出力
				if ( ( ( $strGroupDisplayCode != $objResult->strgroupdisplaycode ) && $i != 0 )||(( $lngStockSubjectCode != $objResult->lngstocksubjectcode )&& $i != 0))
				{
					$strMasterData .= str_repeat ( "\t", 20 ) . "部門計\t" . sprintf ("%9.2f", $arySumGroupPrice["curSubTotalPrice"] ) . "\t" . sprintf ("%9.2f", $arySumGroupPrice["curTaxPrice"] ) . "\t" . sprintf ("%9.2f", $arySumGroupPrice["curTotalPrice"] ) ."\t" . sprintf ("%9.2f", $arySumGroupPrice["curTotalPriceTTM"] ) . "\n";
					$arySumGroupPrice = Array();
				}

				// 一つ前の仕入科目と比較して違う場合、
				// 「仕入科目コード別集計」を出力
				if ( ( $lngStockSubjectCode != $objResult->lngstocksubjectcode ) && $i != 0 )
				{
					$strMasterData .= str_repeat ( "\t", 20 ) . "仕入科目計\t" . sprintf ("%9.2f", $arySumSubPrice["curSubTotalPrice"] ) . "\t" . sprintf ("%9.2f", $arySumSubPrice["curTaxPrice"] ) . "\t" . sprintf ("%9.2f", $arySumSubPrice["curTotalPrice"] ) ."\t" . sprintf ("%9.2f", $arySumSubPrice["curTotalPriceTTM"] ) . "\n";
					$arySumSubPrice = Array();
				}

				// 部門コードを保持
				$strGroupDisplayCode = $objResult->strgroupdisplaycode;
				// 製品を保持
				$strProductCode      = $objResult->strproductcode;
				// 仕入科目コードを保持
				$lngStockSubjectCode = $objResult->lngstocksubjectcode;

				// 仕入科目集計
				$arySumSubPrice["curSubTotalPrice"] += $objResult->cursubtotalprice;
				$arySumSubPrice["curTaxPrice"]      += $objResult->curtaxprice;
				$arySumSubPrice["curTotalPrice"]    += $objResult->curtotalprice;
				$arySumSubPrice["curTotalPriceTTM"] += $objResult->curtotalpricettm;

				// 部門コード集計
				$arySumGroupPrice["curSubTotalPrice"] += $objResult->cursubtotalprice;
				$arySumGroupPrice["curTaxPrice"]      += $objResult->curtaxprice;
				$arySumGroupPrice["curTotalPrice"]    += $objResult->curtotalprice;
				$arySumGroupPrice["curTotalPriceTTM"] += $objResult->curtotalpricettm;

				// 製品集計
				$arySumGoodsPrice["curSubTotalPrice"] += $objResult->cursubtotalprice;
				$arySumGoodsPrice["curTaxPrice"]      += $objResult->curtaxprice;
				$arySumGoodsPrice["curTotalPrice"]    += $objResult->curtotalprice;
				$arySumGoodsPrice["curTotalPriceTTM"] += $objResult->curtotalpricettm;

				// 総計
				$arySumPrice["curSubTotalPrice"] += $objResult->cursubtotalprice;
				$arySumPrice["curTaxPrice"]      += $objResult->curtaxprice;
				$arySumPrice["curTotalPrice"]    += $objResult->curtotalprice;
				$arySumPrice["curTotalPriceTTM"] += $objResult->curtotalpricettm;

				// カラム数表示
				for ( $j = 0; $j < $lngFieldNum; $j++ )
				{
					$aryResult[$j] = preg_replace ( "/\s+?$/", "", $aryResult[$j] );// 空白削除
					$aryExportData[] = $aryResult[$j];
				}

				$strMasterData .= join ( "\t", $aryExportData ) . "\n";
				unset ( $aryExportData );
			}

			// 最終行に対する集計処理
			$strMasterData .= str_repeat ( "\t", 20 ) . "製品計\t" . sprintf ("%9.2f", $arySumGoodsPrice["curSubTotalPrice"] ) . "\t" . sprintf ("%9.2f", $arySumGoodsPrice["curTaxPrice"] ) . "\t" . sprintf ("%9.2f", $arySumGoodsPrice["curTotalPrice"] ) ."\t" . sprintf ("%9.2f", $arySumGoodsPrice["curTotalPriceTTM"] ) . "\n";
			$strMasterData .= str_repeat ( "\t", 20 ) . "部門計\t" . sprintf ("%9.2f", $arySumGroupPrice["curSubTotalPrice"] ) . "\t" . sprintf ("%9.2f", $arySumGroupPrice["curTaxPrice"] ) . "\t" . sprintf ("%9.2f", $arySumGroupPrice["curTotalPrice"] ) ."\t" . sprintf ("%9.2f", $arySumGroupPrice["curTotalPriceTTM"] ) . "\n";
			$strMasterData .= str_repeat ( "\t", 20 ) . "仕入科目計\t" . sprintf ("%9.2f", $arySumSubPrice["curSubTotalPrice"] ) . "\t" . sprintf ("%9.2f", $arySumSubPrice["curTaxPrice"] ) . "\t" . sprintf ("%9.2f", $arySumSubPrice["curTotalPrice"] ) ."\t" . sprintf ("%9.2f", $arySumSubPrice["curTotalPriceTTM"] ) . "\n";

			// 総計出力
			$strMasterData .= str_repeat ( "\t", 20 ) . "総計\t" . sprintf ("%9.2f", $arySumPrice["curSubTotalPrice"] ) . "\t" . sprintf ("%9.2f", $arySumPrice["curTaxPrice"] ) . "\t" . sprintf ("%9.2f", $arySumPrice["curTotalPrice"] ) ."\t" . sprintf ("%9.2f", $arySumPrice["curTotalPriceTTM"] ) . "\n";
		}
		/////////////////////////////////////////////////////////////////////
		// L/C予定表情報(L/C 予定表・リバイズ)
		/////////////////////////////////////////////////////////////////////
		elseif ( $lngExportData == DEF_EXPORT_LC )
		{
			// 結果を成形
			for ( $i = 0; $i < $lngResultRows; $i++ )
			{

				// 行結果取得
				$aryResult = $objDB->fetchArray ( $lngResultID, $i );

				// カラム数表示
				for ( $j = 0; $j < $lngFieldNum; $j++ )
				{
					$aryResult[$j] = preg_replace ( "/\s+?$/", "", $aryResult[$j] );// 空白削除
					$aryExportData[] = $aryResult[$j];
				}

				$strMasterData .= join ( "\t", $aryExportData ) . "\n";
				unset ( $aryExportData );
			}
		}
		/////////////////////////////////////////////////////////////////////
		// 見積原価書
		elseif ( $lngExportData == DEF_EXPORT_ESTIMATE )
		{
			// 結果を成形
			for ( $i = 0; $i < $lngResultRows; $i++ )
			{
				// 行結果取得
				$aryResult = $objDB->fetchArray ( $lngResultID, $i );

				// カラム数表示
				for ( $j = 0; $j < $lngFieldNum; $j++ )
				{
					$aryResult[$j] = preg_replace ( "/\s+?$/", "", $aryResult[$j] );// 空白削除
					$aryExportData[] = $aryResult[$j];
				}

				$strMasterData .= join ( "\t", $aryExportData ) . "\n";
				unset ( $aryExportData );

			}
		}
		
		return $strMasterData;
	}

?>
