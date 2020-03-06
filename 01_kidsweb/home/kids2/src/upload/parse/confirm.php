<?php

/**
*
*	@charset	: utf-8
*/



	require ( 'conf.inc' );										// 設定読み込み
	require ( LIB_DEBUGFILE );									// Debugモジュール

	require ( LIB_ROOT . "mapping/conf_mapping_common.inc" );	// マッピング設定 - 共通
	require ( LIB_ROOT . "mapping/conf_mapping_estimate.inc" );	// マッピング設定 - 見積原価管理

	require ( LIB_FILE );										// ライブラリ読み込み
	require ( SRC_ROOT . "po/cmn/lib_po.php" );					// 発注管理関数ファイル

	require ( LIB_EXCELPARSER_FILE );							// Excel Parser オブジェクト
	require ( LIB_ROOT . "excelparser/lib_excelparser.php" );	// Excel Parser ライブラリ
	require ( CLS_EXCELMAP_FILE );								// Excel Mapping オブジェクト
	require ( "cmn/getWorkSheet2Array.php" );					// ワークシートデータ -> 配列変換モジュール
	require ( "cmn/lib_parse.php" );							// パース処理ライブラリ

	require ( LIB_ROOT . "diff/conf_diff_product.inc" );		// 製品マスタ・差分管理設定
	require ( LIB_ROOT . "diff/lib_diff.php" );					// 製品マスタ・差分管理ライブラリ

	require ( SRC_ROOT . "estimate/cmn/lib_e.php" );			// 見積原価ライブラリ



	$objDB			= new clsDB();
	$objAuth		= new clsAuth();
	$objTemplate	= new clsTemplate();								// テンプレートオブジェクト生成
	$exc			= new ExcelFileParser( "debug.log", ABC_NO_LOG );	// ABC_NO_LOG  ABC_VAR_DUMP;
	$objMap			= new clsMapping();									// マッピングオブジェクト生成


	$objMaster;
	$aryProductHeader	= array();	// ワークシート製品データ取得配列
	$aryMaster			= array();	// 製品マスタデータ取得配列
	$arySystemRate		= array();	// システムレート配列
	$aryExcelRate		= array();	// Excelレート配列
	$aryDiff			= array();	// 差異取得配列
	$aryBuff			= array();	// 表示用データ取得配列バッファ
	$strDiff			= "";

	$aryError			= array();	// エラーチェック配列



	//-------------------------------------------------------------------------
	// Mapping Object 初期設定
	//-------------------------------------------------------------------------
	$objMap->setArrayMap( $aryMapping );	// マッピング配列を設定
	$objMap->setProcessMode( PROC_NORMAL );	// 初回処理モードを設定

	//-------------------------------------------------------------------------
	// DBオープン
	//-------------------------------------------------------------------------
	$objDB->open( "", "", "", "" );

	//-------------------------------------------------------------------------
	// パラメータ取得
	//-------------------------------------------------------------------------
	$aryData	= array();
	$aryData	= $_REQUEST;

	$aryData["CHAR_SET"]			= TMP_CHARSET;					// テンプレート文字コード
	$aryData["lngLanguageCode"]		= $_COOKIE["lngLanguageCode"];	// 言語コード

	// シート番号取得
	$ws_num	= $aryData["lngSelectSheetNo"];

	//-------------------------------------------------------------------------
	// 入力文字列値・セッション・権限チェック
	//-------------------------------------------------------------------------
	// 文字列チェック
	$aryCheck["strSessionID"]	= "null:numenglish(32,32)";
	$aryResult	= fncAllCheck( $aryData, $aryCheck );
	fncPutStringCheckError( $aryResult, $objDB );

	// セッション確認
	$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

	// ユーザーコード取得
	$lngUserCode = $objAuth->UserCode;

	// 権限確認
	if( !fncCheckAuthority( DEF_FUNCTION_UP0, $objAuth ) )
	{
		fncOutputError ( 9060, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
	}

	// 権限グループコードの取得
	$lngAuthorityGroupCode = fncGetUserAuthorityGroupCode( $lngUserCode, $aryData["strSessionID"], $objDB );



	/*-------------------------------------------------------------------------
		承認ルートの生成
	-------------------------------------------------------------------------*/
	$strWorkFlow	= "";

	// 「マネージャー」以上の場合
	if( $lngAuthorityGroupCode <= DEF_DIRECT_REGIST_AUTHORITY_CODE )
	{
		$strWorkFlow	= '<option value="0">承認なし</option>';
	}
	else
	{
		$strWorkFlow	= mb_convert_encoding( fncWorkFlow($lngUserCode , $objDB ,""), ENC_CHARSET, "UTF-8,UTF-8,SJIS,ASCII,JIS" );
	}


	/*-------------------------------------------------------------------------
		Excelファイルパース処理
	-------------------------------------------------------------------------*/
	$aryError	= fncParseExcelFile( $exc, $aryData );

	// Excelファイルエラーチェック
	if( $aryError["bytError"] )
	{
		fncOutputError ( 9059, DEF_WARNING, $aryError["strError"], TRUE, "", $objDB );
		unset ( $aryError );
		return false;
	}


	/*-------------------------------------------------------------------------
		ワークシートデータ配列／ワークシート名取得
	-------------------------------------------------------------------------*/
	$aryWSData	= array();
	$strWSName	= "";

	// Excelワークシートデータ -> Array
	$aryWSData	= getWorkSheet2Array( $exc, $objMap, $ws_num );

	// Excelワークシート名取得
	$strWSName	= getWorkSheetName( $exc, $ws_num );


//fncDebug( 'exc_formats.txt', $exc->populateFormat(), __FILE__, __LINE__);
//fncDebug( 'exc_vars.txt', get_object_vars($exc), __FILE__, __LINE__);
//fncDebug( 'exc_debug.txt', get_object_vars($exc), __FILE__, __LINE__);
//var_dump( get_class_methods($exc)  );	// method
//var_dump( get_object_vars($exc) );	// property


	/*-------------------------------------------------------------------------
		部門・ユーザー差異チェック
	-------------------------------------------------------------------------*/
	// ログインユーザーの表示ユーザーコードを取得
	$lngUserDispCode	= fncGetMasterValue( "m_user", "lngusercode", "struserdisplaycode",  $lngUserCode, '', $objDB );

	// ワークシート(Excel)上、製品情報取得
	$aryProductHeader	= getProductHeader2Array( $objMap, $aryWSData, PROC_DIFF );

	// ワークシート(Excel)上、担当者表示名取得
	$strSheetUserName	= mb_convert_encoding( fncGetMasterValue( "m_user", "struserdisplaycode", "struserdisplayname",  $aryProductHeader[DISP_INCHARGE_USER_CODE].":str", '', $objDB ), ENC_CHARSET, "UTF-8,UTF-8,SJIS,ASCII,JIS" );


	// 製品マスタに登録されているユーザーグループと
	// ログインユーザーのグループが同一であるか、確認
	if( !fncCheckGroup( $objDB, $aryProductHeader, $lngUserDispCode ) )
	{
		$strErrMsg	= "（ログイン者とExcelデータ上の部門が異なります。）";
		$strErrMsg	= mb_convert_encoding( $strErrMsg, "UTF-8", "UTF-8,UTF-8,SJIS,ASCII,JIS" );

		// グループが違う場合、処理終了
		fncOutputError( 1603, DEF_WARNING, $strErrMsg, TRUE, "", $objDB );
	}


	// 否認項目チェック
	// 指定製品が存在する場合
	if( $objMaster	= getMasterData( $objDB, $aryDiffProduct, $aryProductHeader["strproductcode"], "denial" ) )
	{
		while( list($index, $value) = each($objMaster) )
		{
			// 表示コードへ変換
			switch( $index )
			{
				// 担当者コード
				case DISP_INCHARGE_USER_CODE:

					// 表示コードへ変換
					$value	= fncGetMasterValue( "m_user", "lngusercode", "struserdisplaycode",  mb_convert_encoding( $value, ENC_CHARSET, "UTF-8,UTF-8,SJIS,ASCII,JIS" ), '', $objDB );


					// 製品マスタに登録されているユーザーグループと
					// エクセル記載のユーザーグループが同一であるか、確認
					if( !fncCheckGroup( $objDB, $aryProductHeader, $value ) )
					{
						$strErrMsg	= "（製品マスタとExcelデータ上の部門が異なります。）";
						$strErrMsg	= mb_convert_encoding( $strErrMsg, "UTF-8", "UTF-8,UTF-8,SJIS,ASCII,JIS" );

						// グループが違う場合、処理終了
						fncOutputError( 1603, DEF_WARNING, $strErrMsg, TRUE, "", $objDB );
					}

					// 製品マスタとExcel上の担当者が異なる場合、処理終了
					if( $value != $aryProductHeader[DISP_INCHARGE_USER_CODE] )
					{
						$strErrMsg	= "（製品マスタとExcelデータ上の担当者が異なります。）";
						$strErrMsg	= mb_convert_encoding( $strErrMsg, "UTF-8", "UTF-8,UTF-8,SJIS,ASCII,JIS" );

						// 担当者が違う場合、処理終了
						fncOutputError( 1602, DEF_WARNING, $strErrMsg, TRUE, "", $objDB );
					}

					// ログインユーザーが「マネージャー」以下で、かつログインユーザーとExcel上の担当者が異なる場合、処理終了

//39期対応の為					if( ($lngAuthorityGroupCode > DEF_DIRECT_REGIST_AUTHORITY_CODE) && ($value != $lngUserDispCode) )
//					{
//					 	$strErrMsg	= "（ログイン者とExcelデータ上の担当者が異なりますtest1。）";
//						$strErrMsg	= mb_convert_encoding( $strErrMsg, "UTF-8", "UTF-8,UTF-8,SJIS,ASCII,JIS" );
//						// 担当者が違う場合、処理終了
//						fncOutputError( 1602, DEF_WARNING, $strErrMsg, TRUE, "", $objDB )
//					}
//					
//					if( ($lngAuthorityGroupCode > DEF_DIRECT_REGIST_AUTHORITY_CODE) && ($lngUserDispCode NOT IN ('252','118','212','237'))
//					{
//						$strErrMsg	= "（ログイン者とExcelデータ上の担当者が異なりますtest2。）";
//						$strErrMsg	= mb_convert_encoding( $strErrMsg, "UTF-8", "UTF-8,UTF-8,SJIS,ASCII,JIS" );
//						// 担当者が違う場合、処理終了
//						fncOutputError( 1602, DEF_WARNING, $strErrMsg, TRUE, "", $objDB )
//					}
					break;

				// その他
				default:
					$aryMaster[$index]	= mb_convert_encoding( $value, ENC_CHARSET, "UTF-8,UTF-8,SJIS,ASCII,JIS" );
					break;
			}
		}
	}
	// 指定製品が存在しない場合、処理終了
	else
	{
		fncOutputError( 1601, DEF_WARNING, "", TRUE, "", $objDB );
	}





	/*-------------------------------------------------------------------------
		製品状態・見積原価状態チェック
	-------------------------------------------------------------------------*/
	// 製品状態取得
	$lngProductStatusCode	= fncGetMasterValue( "m_product", "strproductcode", "lngproductstatuscode",  $aryProductHeader["strproductcode"].":str", '', $objDB );

	// 製品状態が「マスタ正常」以外の場合
	if( $lngProductStatusCode != DEF_PRODUCT_NORMAL )
	{
		fncOutputError( 308, DEF_WARNING, "", TRUE, "", $objDB );
	}


	// 見積状態取得
	$lngEstimateStatusCode	= fncGetMasterValue( "m_estimate", "strproductcode", "lngestimatestatuscode",  $aryProductHeader["strproductcode"].":str", '', $objDB );

	// 見積原価状態が「申請中」の場合
	if( $lngEstimateStatusCode == DEF_ESTIMATE_APPLICATE )
	{
		fncOutputError( 1509, DEF_WARNING, "", TRUE, "", $objDB );
	}


	// 見積原価情報が存在している場合
	if( $lngEstimateStatusCode )
	{
		// 登録日設定
		$dtmInsertDate	= date("Y/m/d");

		// 既に見積もりが存在する場合、登録日取得
//		$dtmInsertDate	= fncGetMasterValue( "m_estimate", "strproductcode", "to_char( dtminsertdate, 'YYYY/mm/dd' ) as dtminsertdate",  $aryProductHeader["strproductcode"].":str", '', $objDB );
	}
	// 新規時
	else
	{
		// 登録日設定
		$dtmInsertDate	= date("Y/m/d");
	}





	/*-------------------------------------------------------------------------
		製品マスタ情報・レート情報取得
	-------------------------------------------------------------------------*/
	// KIDS製品マスタ情報取得
	$objMaster	= getMasterData( $objDB, $aryDiffProduct, $aryProductHeader["strproductcode"], "select" );

	// システム標準割合取得
	$arySystemRate[NAME_SYSTEM_STANDARD_RATE]	= (string)fncGetEstimateDefault( $objDB );

	// システム社内USドルレート取得
	$arySystemRate[NAME_SYSTEM_CONVERSION_RATE]	= fncGetUSConversionRate( $dtmInsertDate, $objDB );



	$objMap->setProcessMode( PROC_NORMAL );	// 初回処理モードを設定


	/*-------------------------------------------------------------------------
		HTMLデータ／FORMデータ取得
	-------------------------------------------------------------------------*/
	$strExcel	= "";
	$strForm	= "";

	// ExcelワークシートHTML取得
	$strExcel	= getWorkSheet2HTML( $aryWSData, $strWSName, $ws_num, "confirm" );

	// ExcelワークシートデータHIDDEN取得
	$strForm	= getWSArray2Hidden( $objMap, $aryData, $aryWSData, $strWSName, $ws_num );

fncDebug( 'upload_parse_confirm_00.txt', $strForm, __FILE__, __LINE__);




	/*-------------------------------------------------------------------------
		製品マスタ情報・レート差異チェック
	-------------------------------------------------------------------------*/
	// Excel標準割合取得
	$aryExcelRate[NAME_STANDARD_RATE]	= $objMap->getUnionValue( STANDARD_RATE );

	// Excel社内USドルレート取得
	$aryExcelRate[NAME_CONVERSION_RATE]	= $objMap->getUnionValue( NAME_CONVERSION_RATE ) . "0000";

//fncDebug( 'upload_parse_confirm_01.txt', $aryExcelRate, __FILE__, __LINE__);


	while( list($index, $value) = each($objMaster) )
	{
		// 表示コードへ変換
		switch( $index )
		{
			// 部門コード
			case DISP_INCHARGE_GROUP_CODE:/*
				$aryBuff["code"]	= fncGetMasterValue( "m_group", "lnggroupcode", "strgroupdisplaycode",  mb_convert_encoding( $value, ENC_CHARSET, "UTF-8,UTF-8,SJIS,ASCII,JIS" ), '', $objDB );

				$aryBuff["name"]	= fncGetMasterValue( "m_group", "strgroupdisplaycode", "strgroupdisplayname",  mb_convert_encoding( $aryBuff["code"] . ":str", ENC_CHARSET, "UTF-8,UTF-8,SJIS,ASCII,JIS" ), '', $objDB );

				$aryMaster[$index]	= $aryBuff["code"] . " : " . $aryBuff["name"];
*/

				$aryMaster[$index]	= fncGetMasterValue( "m_group", "lnggroupcode", "strgroupdisplaycode",  mb_convert_encoding( $value, ENC_CHARSET, "UTF-8,UTF-8,SJIS,ASCII,JIS" ), '', $objDB );
				break;


			// 担当者コード
			case DISP_INCHARGE_USER_CODE:

//				$aryBuff["code"]	= fncGetMasterValue( "m_user", "lngusercode", "struserdisplaycode",  $value, '', $objDB );
//				$aryBuff["name"]	= fncGetMasterValue( "m_user", "struserdisplaycode", "struserdisplayname",  $aryBuff["code"], '', $objDB );

//				$aryMaster[$index]	= $aryBuff["code"] . " : " . mb_convert_encoding( $aryBuff["name"], ENC_CHARSET, "UTF-8,UTF-8,SJIS,ASCII,JIS" );


				$aryMaster[$index]	= fncGetMasterValue( "m_user", "lngusercode", "struserdisplaycode",  mb_convert_encoding( $value, ENC_CHARSET, "UTF-8,UTF-8,SJIS,ASCII,JIS" ), '', $objDB );
				break;

/*
			case "dtmDeliveryLimitDate":
				$aryMaster[$index]	= ;
				break;
*/

			// その他
			default:
				$aryMaster[$index]	= mb_convert_encoding( $value, ENC_CHARSET, "UTF-8,UTF-8,SJIS,ASCII,JIS" );
				break;
		}
	}



	// 製品マスタとの差異が存在する場合
	if( $aryDiff = fncCheckDiff( $objDB, $aryDiffProduct["diff"], $aryMaster, $aryProductHeader ) )
	{
		$strDiff	.= "<center><h4>以下の項目が製品マスタ情報と異なります。この情報で登録しますか？</h4></center>\n";
		$strDiff	.= "<table border=\"1\">\n";
		$strDiff	.= "\t<tr class=\"column\">\n";
		$strDiff	.= "\t\t<td>項目</td><td>製品マスタ</td><td>Excelデータ</td>\n";
		$strDiff	.= "\t</tr>\n";

		while( list($index, $value) = each($aryDiff) )
		{
			$strDiff	.= "\t<tr>\n";
			$strDiff	.= "\t\t<td bgcolor=\"#eeeeee\">" .mb_convert_encoding( $aryDiffProduct["display"][$index], ENC_CHARSET, "UTF-8,UTF-8,SJIS,ASCII,JIS" ). "</td><td>" .$aryMaster[$index]. "</td><td>" .$value. "</td>\n";
			$strDiff	.= "\t</tr>\n";
		}

		$strDiff	.= "</table>\n";
		$strDiff	.= "<br /><hr size=\"1\" />\n";
	}


	// 社内レートとの差異が存在する場合
	if( $aryDiff = fncCheckDiff( $objDB, $aryDiffProduct["rate"], $arySystemRate, $aryExcelRate ) )
	{
		$strDiff	.= "<center><h4>以下の項目が社内レート情報と異なります。レート情報は社内レートで処理されます。</h4></center>\n";
		$strDiff	.= "<table border=\"1\">\n";
		$strDiff	.= "\t<tr class=\"column\">\n";
		$strDiff	.= "\t\t<td>項目</td><td>社内レート</td><td>Excelレート</td>\n";
		$strDiff	.= "\t</tr>\n";

		while( list($index, $value) = each($aryDiff) )
		{
			// 標準割合の場合
			if( $index == NAME_STANDARD_RATE )
			{
				$arySystemRate[$index]	= $arySystemRate[$index] . "%";
				$value					= $value . "%";
			}

			$strDiff	.= "\t<tr>\n";
			$strDiff	.= "\t\t<td bgcolor=\"#eeeeee\">" .mb_convert_encoding( $aryDiffProduct["display"][$index], ENC_CHARSET, "UTF-8,UTF-8,SJIS,ASCII,JIS" ). "</td><td>" .$arySystemRate[$index]. "</td><td>" .$value. "</td>\n";
			$strDiff	.= "\t</tr>\n";
		}

		$strDiff	.= "</table>\n";
		$strDiff	.= "<br /><hr size=\"1\" />\n";
	}


	//-------------------------------------------------------------------------
	// ファイルテンポラリフラグ有効
	//-------------------------------------------------------------------------
	$aryData["bytTemporaryFlg"] = 1;


	//-------------------------------------------------------------------------
	// DB Close
	//-------------------------------------------------------------------------
	$objDB->close();
	$objDB->freeResult( $lngResultID );





	/*-------------------------------------------------------------------------
		出力
	-------------------------------------------------------------------------*/
	$aryData["DIFF"]		= $strDiff;

	$aryData["EXCEL"]		= $strExcel;
	$aryData["FORM"]		= $strForm;
	$aryData["FORM_NAME"]	= FORM_NAME;
	$aryData["WORKFLOW"]	= $strWorkFlow;

	$aryData["DEBUG"]		= $strDebug;

fncDebug( 'upload_parse_confirm_form_encoding.txt', mb_detect_encoding($strForm), __FILE__, __LINE__);



	// テンプレート読み込み
	$objTemplate->getTemplate( "upload/parse/confirm.tmpl" );

	// テンプレート生成
	$objTemplate->replace( $aryData );
	$objTemplate->complete();

// debug
fncDebug( 'upload_parse_confirm.txt', $objTemplate->strTemplate, __FILE__, __LINE__);

	// HTML出力
	echo $objTemplate->strTemplate;
	/*-----------------------------------------------------------------------*/




	// テンポラリファイルの削除
	//deleteTempFile( $aryData["exc_tmp_name"] );

	// デストラクト
	$objMap->destruct();
	unset( $exc, $objMap, $objDB, $objAuth, $objTemplate, $aryData, $strWorkSheet, $strExcel, $strForm );

	return true;

?>
