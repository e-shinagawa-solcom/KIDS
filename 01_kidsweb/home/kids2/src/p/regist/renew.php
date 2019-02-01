<?php

// ----------------------------------------------------------------------------
/**
*       商品管理  修正画面
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
*         ・修正時登録画面を表示
*         ・入力エラーチェック
*         ・登録ボタン押下後、登録確認画面へ
*
*       更新履歴
*
*/
// ----------------------------------------------------------------------------



	//-------------------------------------------------------------------------
	// ■ ライブラリファイル読込
	//-------------------------------------------------------------------------
	include( 'conf.inc' );
	require( LIB_FILE );
	require( SRC_ROOT."p/cmn/lib_p3.php" );
	require( SRC_ROOT."po/cmn/lib_po.php" );
	require( "libsql.php" );
	require_once(LIB_DEBUGFILE);
	require_once(CLS_IMAGELO_FILE);


	//-------------------------------------------------------------------------
	// ■ オブジェクト生成
	//-------------------------------------------------------------------------
	$objDB   = new clsDB();
	$objAuth = new clsAuth();


	//-------------------------------------------------------------------------
	// ■ DBオープン
	//-------------------------------------------------------------------------
	$objDB->open("", "", "", "");


	//-------------------------------------------------------------------------
	// ■ パラメータ取得
	//-------------------------------------------------------------------------
	$aryData = $_POST;

	$aryData["strSessionID"]    = $_REQUEST["strSessionID"];
	$aryData["lngLanguageCode"] = $_COOKIE["lngLanguageCode"];
	//$aryData["lngLanguageCode"] = $_REQUEST["lngLanguageCode"];



	//-------------------------------------------------------------------------
	// ■ 入力文字列値・セッション・権限チェック
	//-------------------------------------------------------------------------
	// 文字列チェック
	$aryCheck["strSessionID"] = "null:numenglish(32,32)";
	$aryResult = fncAllCheck( $aryData, $aryCheck );
	fncPutStringCheckError( $aryResult, $objDB );

	// セッション確認
	$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

	$lngInputUserCode = $objAuth->UserCode;


	// 300 商品管理
	if( !fncCheckAuthority( DEF_FUNCTION_P0, $objAuth ) )
	{
		fncOutputError ( 9018, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
	}

	// 306 商品管理（商品修正）
	if( !fncCheckAuthority( DEF_FUNCTION_P6, $objAuth ) )
	{
		fncOutputError ( 9018, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
	}




	//-------------------------------------------------------------------------
	// ■ 入力チェック
	//-------------------------------------------------------------------------
	if( strcmp ( $aryData["strProcess"], "" ) != 0)
	{
		$strSelectError = fncOutputError ( 9020, "", "", FALSE, "", $objDB );

		if($aryData["strProcess"] == "check" )
		{
			//エラーチェック項目を配列で格納 ====================================
			$aryCheck = array();
			$aryCheck["strProductName"]				= "null:length(1,100)";			// 3:製品名称
			$aryCheck["strProductEnglishName"]		= "null:length(1,100)"; 			// 4:製品名称(英語)
			$aryCheck["lngInChargeGroupCode"]		= "null:length(1,2)";				// 5:部門
			$aryCheck["lngInChargeUserCode"]		= "null:length(1,3)";				// 6:担当者
			$aryCheck["strGoodsCode"]				= "length(1,10)";			// 9:商品コード（null登録可能）
/*必須外す		$aryCheck["lngCategoryCode"]			= "number(1,999999999):length(1,10)";		// xxxx:カテゴリーコード// added by k.saito*/
			$aryCheck["lngCategoryCode"]			= "length(1,10)";
			$aryCheck["strGoodsName"]				= "length(1,80)";			// 10:商品名称
			$aryCheck["lngCustomerCode"]			= "number(0,999999999)";			// 11:顧客
			//$aryCheck["lngCustomerUserCode"]		= "";						// 13:顧客担当者コード 
			// 13が入っている場合のみ
			if( strcmp($aryCheck["lngCustomerUserCode"], "") == 0 )
			{
				$aryCheck["strCustomerUserName"]		= "length(1,50)";			// 14:顧客担当者50byte
			}
			$aryCheck["lngPackingUnitCode"]			= "money(1,99)";				// 15:荷姿単位(int2)
			$aryCheck["lngProductUnitCode"]			= "money(1,99)";				// 16:製品単位(int2)
			$aryCheck["lngBoxQuantity"]				= "number(0,2147483647)";		// 17:内箱（袋）入数10byte"   :length(1,10)
			$aryCheck["lngCartonQuantity"]			= "null:number(1,2147483647)";			// 18:カートン入数10byte   :length(1,10)
			$aryCheck["lngProductionQuantity"]		= "null:number(1,2147483647)";			// 19:生産予定数10byte     :length(1,10)
			$aryCheck["lngProductionUnitCode"]		= "number(1,99):length(1,4)";			// 20:生産予定数の単位4byte
			$aryCheck["lngFirstDeliveryQuantity"]	= "null:number(1,2147483647)";				// 21:初回納品数10byte   :length(1,10)
			$aryCheck["lngFirstDeliveryUnitCode"]	= "number(1,99):length(0,4)";				// 22:初回納品数の単位4byte
			$aryCheck["lngFactoryCode"]				= "length(1,4)";			// 23:生産工場4byte
			$aryCheck["lngAssemblyFactoryCode"]		= "length(1,4)";	 			// 24:アッセンブリ工場4byte
			$aryCheck["lngDeliveryPlaceCode"]		= "length(1,4)";				// 25:納品場所4byte
			$aryCheck["dtmDeliveryLimitDate"]		= "null:date"; 					// 26:納品期限日:date
			$aryCheck["curProductPrice"]			= "null:money(0.0001,99999999999999.9999)";	// 27:卸値
			$aryCheck["curRetailPrice"]				= "null:money(0.0000,99999999999999.9999)";	// 28:売値     :length(1,8)
/*			$aryCheck["lngRoyalty"]					= "null:number(0,999999)";		// 30:ロイヤルティー 
*/
 			$aryCheck["lngRoyalty"]					= "number(0,100)";

			$aryCheck["lngCertificateClassCode"]	= "null";		 				// 31:証紙
/*必須外す			
			if( $aryData["lngCopyrightCode"] == 0 && strcmp( $aryData["strCopyrightNote"], "") == 0 )
			{
				$aryCheck["lngCopyrightCode"]		= "number(1,99)"; 				// 32:版権元
				$aryCheck["strCopyrightNote"]		= "null";					// :版権元備考
			}
*/
			$aryCheck["lngCopyrightCode"]		= "length(1,50)"; 					// 32:版権元
			$aryCheck["strCopyrightNote"]		= "length(1,200)";					// :版権元備考
			
			// ここだけへんです。
			$aryCheck["lngProductFormCode"]			= "number(1,99,The list has not been selected.):length(1,100)"; 	// 35:商品形態100
			if($_COOKIE["lngLanguageCode"])
			{
				$aryCheck["lngProductFormCode"]			= "number(1,99,リストが選択されていません。):length(1,100)"; 	// 35:商品形態100
			}
			$aryCheck["strCopyrightDisplayPrint"]	= "length(1,100)"; 						// 34:版権表示(印刷物)100
			$aryCheck["strProductComposition"]		= "null:number(0,99)";  				// 36:製品構成100byte
			$aryCheck["strAssemblyContents"]		= "length(1,100)";  					// 37:アッセンブリ内容100byte
			$aryCheck["strSpecificationDetails"]	= "length(1,10000)";  						// 38:仕様詳細10000
			// エラー関数の呼び出し
			$aryCheckResult = fncAllCheck( $aryData, $aryCheck );
			list ( $aryData, $bytErrorFlag ) = getArrayErrorVisibility( $aryData, $aryCheckResult, $objDB );
			$errorCount = ($bytErrorFlag == "TRUE" ) ? 1 : 0;
			//-------------------------------------------------
			// データが「申請中」になっていないかどうか確認
			//-------------------------------------------------
			$strCheckQuery = "SELECT lngProductStatusCode FROM m_Product p WHERE p.strProductCode = '" . $aryData["strProductCode"] . "'";
			$strCheckQuery .= " AND p.bytInvalidFlag = FALSE\n";
			// チェッククエリーの実行
			list( $lngCheckResultID, $lngCheckResultNum ) = fncQuery( $strCheckQuery, $objDB );

			if ( $lngCheckResultNum == 1 )
			{
				$objResult            = $objDB->fetchObject( $lngCheckResultID, 0 );
				$lngProductStatusCode = $objResult->lngproductstatuscode;

				if( $lngProductStatusCode == DEF_PRODUCT_APPLICATE )
				{
					fncOutputError( 307, DEF_WARNING, "", TRUE, "../p/regist/index.php?strSessionID=" . $aryData["strSessionID"], $objDB );
				}
			}

			// 結果IDを解放
			$objDB->freeResult( $lngCheckResultID );




			/**
				仕様詳細画像ファイルHIDDEN生成
			*/
			if( $aryData["uploadimages"] )
			{
				for( $i = 0; $i < count($aryData["uploadimages"]); $i ++ )
				{
					$aryUploadImagesHidden[]	= "<input type=\"hidden\" name=\"uploadimages[]\" value=\"" .$aryData["uploadimages"][$i]. "\" />\n";
				}

				// 再取得用に設定
				$aryData["re_uploadimages"]	= implode( "\n", $aryUploadImagesHidden );
				$aryData["re_editordir"]	= "<input type=\"hidden\" name=\"strTempImageDir\" value=\"" .$aryData["strTempImageDir"]. "\" />\n";
			}
			else
			{
				$aryData["re_uploadimages"]	= "";
				$aryData["re_editordir"]	= "";
			}



			//確認画面 ==================================================================================
			if( $errorCount == 0 )
			{

				// 変更があった項目のみ$aryUpdateに格納
				$strProductCode = $aryData["strProductCode"];

				$aryQuery[] = "SELECT ";
				$aryQuery[] = "lngproductno, ";
				$aryQuery[] = "strProductCode, ";																		//2:製品コード
				$aryQuery[] = "strProductName, ";																		//3:製品名称
				$aryQuery[] = "strProductEnglishName, ";	 															//4:製品名称(英語)
				$aryQuery[] = "lngInChargeGroupCode, ";																	//5:部門
				$aryQuery[] = "lngInChargeUserCode, ";																	//6:担当者
				$aryQuery[] = "lnginputusercode, ";																		//7:入力者
																														//8:顧客識別コード(表示のみ)
				$aryQuery[] = "strGoodsCode, ";																			//9:商品コード
				$aryQuery[] = "strGoodsName, ";																			//10:商品名称
				// 2004/03/08 watanabe update start
				$aryQuery[] = "lngCustomerCompanyCode as lngCompanyCode, ";												//11:顧客
				$aryQuery[] = "lngCustomerUserCode as strCustomerUserCode, ";											//13:顧客担当者コード
				// 2004/03/08 watanabe update end
				$aryQuery[] = "strCustomerUserName, ";																	//14:顧客担当者()
				$aryQuery[] = "lngPackingUnitCode, ";																	//15:荷姿単位(int2)
				$aryQuery[] = "lngProductUnitCode, ";																	//16:製品単位(int2)
				$aryQuery[] = "trim(To_char(lngBoxQuantity, '9,999,999,999')) as lngBoxQuantity, ";						//17:内箱（袋）入数(int4)
				$aryQuery[] = "trim(To_char(lngCartonQuantity,'9,999,999,999')) as lngCartonQuantity, ";				//18:カートン入数(int4)
				$aryQuery[] = "trim(To_char(lngProductionQuantity,'9,999,999,999')) as lngProductionQuantity, ";		//19:生産予定数()
				$aryQuery[] = "lngProductionUnitCode, ";																//20:生産予定数の単位()
				$aryQuery[] = "trim(To_char(lngFirstDeliveryQuantity,'9,999,999,999')) as lngFirstDeliveryQuantity, ";	//21:初回納品数(int4)
				$aryQuery[] = "lngFirstDeliveryUnitCode, ";																//22:初回納品数の単位()
				$aryQuery[] = "lngFactoryCode, ";																		//23:生産工場()
				$aryQuery[] = "lngAssemblyFactoryCode, ";	 															//24:アッセンブリ工場()
				$aryQuery[] = "lngDeliveryPlaceCode, ";																	//25:納品場所(int2)
				$aryQuery[] = "To_char(dtmDeliveryLimitDate,'YYYY/MM') as dtmDeliveryLimitDate, ";						//26:納品期限日()
				$aryQuery[] = "trim(To_char(curProductPrice, '9,999,999,990.99')) as curProductPrice, ";				//27:卸値()
				$aryQuery[] = "trim(To_char(curRetailPrice, '9,999,999,990.99')) as curRetailPrice,";					//28:売値()
				$aryQuery[] = "lngTargetAgeCode, ";																		//29:対象年齢()
				$aryQuery[] = "trim(To_char(lngRoyalty, '990.99')) as lngRoyalty,";										//30:ロイヤルティー()
				$aryQuery[] = "lngCertificateClassCode, "; 																//31:証紙()
				$aryQuery[] = "lngCopyrightCode, ";																		//32:版権元()
				$aryQuery[] = "strCopyrightDisplayStamp, ";																//33:版権表示(刻印)
				$aryQuery[] = "strCopyrightDisplayPrint, ";																//34:版権表示(印刷物)
				$aryQuery[] = "lngProductFormCode, ";																	//35:商品形態()
				$aryQuery[] = "strProductComposition, ";																//36:製品構成()
				$aryQuery[] = "strAssemblyContents, "; 																	//37:アッセンブリ内容()
				$aryQuery[] = "strSpecificationDetails, "; 																//38:仕様詳細()
				$aryQuery[] = "strNote, ";																				//39:備考
				$aryQuery[] = "strCopyrightNote, ";																		//40:版権元備考
				$aryQuery[] = "lngProductStatusCode,";																	// 商品状態
				$aryQuery[] = "lngCategoryCode";																		// カテゴリーコード
				$aryQuery[] = "FROM m_product ";
				$aryQuery[] = "WHERE bytinvalidflag = false ";
				$aryQuery[] = "AND strProductCode = '$strProductCode'";

				$strQuery = "";
				$strQuery = implode("\n", $aryQuery);

				//echo "$strQuery<br><br>";

				$objDB->freeResult( $lngResultID );
				if ( !$lngResultID = $objDB->execute( $strQuery ) )
				{
					fncOutputError ( 9051, DEF_ERROR, "", TRUE, "", $objDB );
					$objDB->close();
					return true;
				}


				if ( !$lngResultNum = pg_Num_Rows( $lngResultID ) )
				{
					fncOutputError ( 9051, DEF_ERROR, "", TRUE, "", $objDB );
					$objDB->close();
					return true;
				}

				$aryResult = array();
				$aryResult = $objDB->fetchArray( $lngResultID, 0 );


				// 企画進行状況 ====================================================
				$lngproductno = $aryResult["lngproductno"];

				$aryQuery2[] = "SELECT lnggoodsplancode,lngrevisionno,lnggoodsplanprogresscode, ";
				$aryQuery2[] = "To_char(dtmrevisiondate,'YYYY/MM/DD HH24:MI') as dtmrevisiondate "; 
				$aryQuery2[] = "FROM t_goodsplan WHERE lnggoodsplancode = (";
				$aryQuery2[] = "SELECT max(lnggoodsplancode) FROM t_goodsplan WHERE lngproductno = ";
				$aryQuery2[] = "$lngproductno )";

				$strQuery2 = "";
				$strQuery2 = implode("\n", $aryQuery2);

				//echo "$strQuery2<br><br>";

				$objDB->freeResult( $lngResultID2 );
				if ( !$lngResultID2 = $objDB->execute( $strQuery2 ) )
				{
					fncOutputError ( 9051, DEF_ERROR, "", TRUE, "", $objDB );
				}

				if ( !$lngResultNum = pg_Num_Rows( $lngResultID2 ) )
				{
					fncOutputError ( 9051, DEF_ERROR, "", TRUE, "", $objDB );
				}

				$aryResult2 = array();
				$aryResult2 = $objDB->fetchArray( $lngResultID2, 0 );

				$aryResult["lnggoodsplanprogresscode"] = $aryResult2["lnggoodsplanprogresscode"];


				$aryKeys = array_keys($aryResult);

				// DBのカラム名に合わせて比較
				for( $i = 0; $i < count( $aryKeys ); $i++ )
				{
					for( $j = 0; $j < count( $gbl_ColName[DEF_ARRAY_COL_NAME][DEF_FORMNAME] ) ; $j++ )
					{
						if( $gbl_ColName[DEF_ARRAY_COL_NAME][DEF_FORMNAME][$j] === $aryKeys[$i] )
						{
							$strColmName = $gbl_ColName[DEF_ARRAY_COL_NAME][DEF_COLMNAME][$j];					// 変更後のカラム名
							$strColmName2 = strtolower($gbl_ColName[DEF_ARRAY_COL_NAME][DEF_FORMNAME][$j]);		// 変換対象のカラム名（小文字）
							$strColmName = strtolower($strColmName);

							$aryResult[$strColmName] = $aryResult[$strColmName2];

						}
					}
				}


				// 部門のコード
				$aryData["lngInChargeGroupCode"]	= fncGetMasterValue( "m_group", "strgroupdisplaycode", "lnggroupcode",  $aryData["lngInChargeGroupCode"] . ":str",'bytGroupDisplayFlag=true',$objDB);
				// 担当者
				$aryData["lngInChargeUserCode"]		= fncGetMasterValue( "m_user", "struserdisplaycode", "lngusercode", $aryData["lngInChargeUserCode"] . ":str",'',$objDB);
				// 顧客
				$aryData["lngCompanyCode"]			= fncGetMasterValue( "m_company", "strcompanydisplaycode", "lngcompanycode", $aryData["lngCompanyCode"] . ":str", '',$objDB);
				// 生産工場
				$aryData["lngFactoryCode"]			= fncGetMasterValue( "m_company", "strcompanydisplaycode", "lngcompanycode", $aryData["lngFactoryCode"] . ":str", '',$objDB);
				// アッセンブリ工場
				$aryData["lngAssemblyFactoryCode"]	= fncGetMasterValue( "m_company", "strcompanydisplaycode", "lngcompanycode", $aryData["lngAssemblyFactoryCode"] . ":str", '',$objDB);
				// 納品場所
				$aryData["lngDeliveryPlaceCode"]	= fncGetMasterValue( "m_company", "strcompanydisplaycode", "lngcompanycode", $aryData["lngDeliveryPlaceCode"] . ":str", '',$objDB);
				// 顧客担当者

				if( strcmp( $aryData["strCustomerUserCode"], "" ) != 0)
				{
					$aryData["strCustomerUserCode"]	= fncGetMasterValue( "m_user", "struserdisplaycode", "lngusercode", $aryData["strCustomerUserCode"] . ":str", '',$objDB);
				}

				// --------------------------
				// クワガタ社内サーバーでは、POSTされたデータのダブルクォートに￥マークが付いてしまうため、これを削除する
				// confirm/index.php と regist/renew.php にて対応　2006/10/08 K.Saito
				$aryData["strSpecificationDetails"] = StripSlashes($aryData["strSpecificationDetails"]);
				// --------------------------

				// http:// 又は https:// のホストが含まれている場合、削除する
				$aryData["strSpecificationDetails"] = preg_replace("/(http:\/\/?[^\/]+)|(https:\/\/?[^\/]+)/i", "" , $aryData["strSpecificationDetails"]);


				// 仕様詳細の特殊文字処理
				//$aryData["strSpecificationDetails"] = fncHTMLSpecialChars( $_POST["strSpecificationDetails"] );
				// 仕様詳細表示用
				$aryData["strSpecificationDetails_DIS"] = nl2br( $aryData["strSpecificationDetails"] );



				$aryUpdate = array();
				$aryResult_Keys = array_keys($aryResult);
				$aryData_Keys = array_keys($aryData);


				// 変更があった項目のみアップデートする
				for ( $i = 0; $i < count( $aryResult_Keys ); $i++ ) 
				{
					list ( $strKey_Result, $strValue_Result ) = each ( $aryResult_Keys );

					reset( $aryData_Keys );
					for($j = 0; $j < count( $aryData_Keys ); $j++)
					{
						list ($strKey_Data, $strValue_Data) = each ( $aryData_Keys );

						$strValue_Data_Low = strtolower( $strValue_Data );

						if( strcmp($strValue_Result, $strValue_Data_Low ) == 0 ) //文字列型だけ比較
						{

							//詳細仕様の場合:改行コードの統一
							if($strValue_Result == "strspecificationdetails")
							{
								$aryResult[$strValue_Result] = preg_replace( '/\x0D\x0A|\x0A|\x0D/', "\x0A", htmlspecialchars($aryResult[$strValue_Result], ENT_COMPAT | ENT_HTML401, "ISO-8859-1") );
								$aryData[$strValue_Data] = preg_replace( '/\x0D\x0A|\x0A|\x0D/', "\x0A", $aryData[$strValue_Data] );
							}


							if( $aryResult[$strValue_Result] != $aryData[$strValue_Data] )
							{

								// 担当者コード:登録画面で担当者をcodeで登録した場合、DBにはname値がはいっていない。
								// 入力画面ではcodeからnameを引いている
								// この画面でPOSTのデータとDBを照合するときにnameが更新対象になる
								//if( $strValue_Result == "strcustomerusername")
								//{
								//	if( strcmp( $aryData["lngInChargeUserCode"] ,"" ) == 0 )
								//	{
								//		 $aryUpdate[] = $strValue_Result;
								//	}
								//}
								//else
								//{
						  			$aryUpdate[] = $strValue_Result;
						  		//}
						  	}
						}
					}
				}

				if( count( $aryUpdate ) == 0)
				{

					fncOutputError ( 306, DEF_WARNING, "", TRUE, "../p/regist/renew.php?strProductCode=".$_GET['strProductCode']."&strSessionID=".$_GET["strSessionID"], $objDB );

				}


				// 担当者コード処理
				$lngResult1 = array_search("lngcustomerusercode", $aryUpdate);
				$lngResult2 = array_search("strcustomerusername", $aryUpdate);

				if( strcmp( $lngResult1, "") != 0 && strcmp( $lngResult2, "") != 0 )
				{
					for( $i = 0; $i < count( $aryUpdate ) ; $i++ )
					{
						if( $aryUpdate[$i] != "strcustomerusername")
						{
							$aryUpdate2[] = $aryUpdate[$i];
						}
					}

					//変更があったkeyのみ「,」区切りでつなげる
					if( $aryUpdate )
					{
						while ( list ($strkey, $strvalue) = each ( $aryUpdate2 ) )
						{
							$strUpdate .= $strvalue ;
							$strUpdate .= ",";
						}
					}

				}
				else
				{
					//変更があったkeyのみ「,」区切りでつなげる
					if( $aryUpdate )
					{
						while ( list ($strkey, $strvalue) = each ( $aryUpdate ) )
						{
							$strUpdate .= $strvalue ;
							$strUpdate .= ",";
						}
					}
				}


				// 末尾の「,」をとる
				$strUpdate = ereg_replace (",$", "", $strUpdate);
				$aryData["updatekey"] = $strUpdate;


				// 製品名（英語）の類似検索(POSTのデータとDBのデータが等し場合のみ：修正だけ）
				if( $aryResult["strproductenglishname"] != $_POST["strProductEnglishName"] )
				{
					$lngInChargeGroupCode = $aryData["lngInChargeGroupCode"];
// 2004.02.24 fncNearName 関数のバグ修正対応
					$strOptionValue = fncNearName ( $aryData["strProductEnglishName"], $lngInChargeGroupCode, $strProductCode ,$objDB );
					$aryData["strOptionValue"] = $strOptionValue;
				}



				// 確認画面表示 

				if(strcmp($aryData["strNexturl"], "") == 0)
				{
					$aryData["strNextUrl"] = "renew3.php";
				}

				if(strcmp($aryData["strBackurl"], "") == 0)
				{
					$aryData["strBackurl"] = "renew.php";
				}
				//$aryData["strButton"] = "<input type=\"button\" onClick=\"fncPageback('')\" value=\"戻る\"><input type=\"button\" onClick=\"fncPagenext('renew3.php')\" value=\"登録\">";


				// カラム名＋DISは確認画面の表示上のもの
				// ./tmp/p/confirm/parts.tmpl 内
				
				// カテゴリー
				$aryData["lngCategoryCode_DIS"] = fncGetMasterValue( "m_Category", "lngCategoryCode", "strCategoryName", $_POST["lngCategoryCode"], '', $objDB);
				// 荷姿単価
				$aryData["lngPackingUnitCode_DIS"] = fncGetMasterValue( "m_productunit", "lngProductUnitCode", "strProductUnitName", $_POST["lngPackingUnitCode"], '', $objDB);

				// 製品単位
				$aryData["lngProductUnitCode_DIS"] = fncGetMasterValue( "m_productunit", "lngProductUnitCode", "strProductUnitName", $_POST["lngProductUnitCode"], '', $objDB);

// 2004.05.27 suzukaze update start
				// 商品形態
				$aryData["lngProductFormCode_DIS"] = fncGetMasterValue( "m_ProductForm", "lngProductFormCode", "strProductFormName", $_POST["lngProductFormCode"], '', $objDB);
// 2004.05.27 suzukaze update end

				// 対象年齢
				$aryData["lngTargetAgeCode_DIS"] = fncGetMasterValue( "m_targetage", "lngTargetAgeCode", "strTargetAgeName", $_POST["lngTargetAgeCode"], '', $objDB);

				// 証紙
				$aryData["lngCertificateClassCode_DIS"] = fncGetMasterValue( "m_CertificateClass", "lngcertificateclasscode", "strcertificateclassname", $_POST["lngCertificateClassCode"], '', $objDB);

				// 版権元
				$aryData["lngCopyrightCode_DIS"] = fncGetMasterValue( "m_copyright", "lngcopyrightcode", "strcopyrightname", $_POST["lngCopyrightCode"], '', $objDB);

				// 生産予定数
				$aryData["lngProductionUnitCode_DIS"] = fncGetMasterValue( "m_productunit", "lngProductUnitCode", "strProductUnitName", $_POST["lngProductionUnitCode"], '', $objDB);

				// 初回納品数
				$aryData["lngFirstDeliveryUnitCode_DIS"] = fncGetMasterValue( "m_productunit", "lngProductUnitCode", "strProductUnitName", $_POST["lngFirstDeliveryUnitCode"], '', $objDB);

				// 企画進行状況
				$aryData["lngGoodsPlanProgressCode_DIS"] = fncGetMasterValue( "m_goodsplanprogress", "lnggoodsplanprogresscode", "strgoodsplanprogressname" ,$_POST["lngGoodsPlanProgressCode"], '',  $objDB );

				// 2004/03/19 watanabe update コード→名称は全て処理する。コードがない場合は[]を表示しない（必須項目も全て。処理だけ）
				$aryData["strCustomerUserCode_DISCODE"] = ( $aryData["strCustomerUserCode"] != "" ) ? "[".$aryData["strCustomerUserCode"]."]" : "";
				$aryData["lngInChargeGroupCode_DISCODE"] = ( $aryData["lngInChargeGroupCode"] != "" ) ? "[".$aryData["lngInChargeGroupCode"]."]" : "";
				$aryData["lngInChargeUserCode_DISCODE"] = ( $aryData["lngInChargeUserCode"] != "" ) ? "[".$aryData["lngInChargeUserCode"]."]" : "";
				$aryData["lngCompanyCode_DISCODE"] = ( $aryData["lngCompanyCode"] != "" ) ? "[".$aryData["lngCompanyCode"]."]" : "";
				$aryData["lngFactoryCode_DISCODE"] = ( $aryData["lngFactoryCode"] != "" ) ? "[".$aryData["lngFactoryCode"]."]" : "";
				$aryData["lngAssemblyFactoryCode_DISCODE"] = ( $aryData["lngAssemblyFactoryCode"] != "" ) ? "[".$aryData["lngAssemblyFactoryCode"]."]" : "";
				$aryData["lngDeliveryPlaceCode_DISCODE"] = ( $aryData["lngDeliveryPlaceCode"] != "" ) ? "[".$aryData["lngDeliveryPlaceCode"]."]" : "";
				// watanabe update end

				$aryData["strMonetaryrate"] = DEF_EN_MARK; //通貨マーク




				//---------------------------------------------
				// 承認ルート
				//---------------------------------------------
				if ( $aryData["lngWorkflowOrderCode"] != "" and $aryData["lngWorkflowOrderCode"] != 0 )
				{
					$aryData["strWorkflowOrderName"] = fncGetMasterValue( "m_workfloworder", "lngworkflowordercode", "strworkflowordername", $_POST["lngWorkflowOrderCode"] . ":str", 'lngWorkflowOrderNo = 1', $objDB );

					$aryData["strWorkflowMessage_visibility"] = "block;";
				}
				else
				{
					$aryData["strWorkflowOrderName"] = "承認なし";

					$aryData["strWorkflowMessage_visibility"] = "none;";
				}





				$aryData["strActionURL"] = "/p/regist/renew3.php?strSessionID=".$aryData["strSessionID"];

				$aryData["RENEW"] = TRUE;
				// submit関数

				// 仕様詳細HIDDEN用（HIDDENに埋め込むために余分なタグなどを取り除く）
				if( strcmp( $aryData["strSpecificationDetails"], "") != 0 ) {
					$aryData["strSpecificationDetails"] = stripslashes($aryData["strSpecificationDetails"]);
					$aryData["strSpecificationDetails"] = htmlspecialchars($aryData["strSpecificationDetails"], ENT_COMPAT | ENT_HTML401, "ISO-8859-1");
				}


				$objDB->close();
				$objTemplate = new clsTemplate();
				$objTemplate->getTemplate( "p/confirm/parts.tmpl" );
				// テンプレート生成
				$objTemplate->replace( $aryData );
				$objTemplate->complete();

//fncDebug("renew2.txt", $objTemplate->strTemplate, __FILE__, __LINE__ );

				// HTML出力
				echo $objTemplate->strTemplate;
				return true;

			}
			else
			// エラーで戻った場合
			// 確認画面では項目を表示しないとの事だったが変更になりました。既にdisplaycodeをcodeに変換した後だったのでここに同じ事を書きます。
			{
				// 権限グループコードの取得
				$lngAuthorityGroupCode = fncGetUserAuthorityGroupCode( $lngInputUserCode, $aryData["strSessionID"], $objDB );

				// 承認ルートの生成
				// 「マネージャー」以上の場合
				if( $lngAuthorityGroupCode <= DEF_DIRECT_REGIST_AUTHORITY_CODE )
				{
					$aryData["lngWorkflowOrderCode"] = '<option value="0">承認なし</option>';
				}
				else
				{
					$aryData["lngWorkflowOrderCode"] = fncWorkFlow( $lngInputUserCode , $objDB , '');
				}


				// オプション値の生成
				// カテゴリー
				$aryData["lngCategoryCode"]				= fncGetPulldownQueryExec( fncSqlqueryCategory(array(0=>$objAuth->UserCode)), $aryData["lngCategoryCode"], $objDB);
				// 荷姿単位
				$aryData["lngPackingUnitCode"]			= fncGetPulldown( "m_productunit", "lngProductUnitCode", "strProductUnitName", $aryData["lngPackingUnitCode"], "WHERE bytpackingconversionflag=true", $objDB);
				// 製品単位
				$aryData["lngProductUnitCode"]			= fncGetPulldown( "m_productunit", "lngProductUnitCode", "strProductUnitName", $aryData["lngProductunitCode"], "WHERE bytproductconversionflag=true", $objDB);
				// 生産予定数の単位
				$aryData["lngProductionUnitCode"]		= fncGetPulldown( "m_productunit", "lngProductUnitCode", "strProductUnitName", $aryData["lngProductionUnitCode"], '', $objDB);
				// 初回納品数の単位
				$aryData["lngFirstDeliveryUnitCode"]	= fncGetPulldown( "m_productunit", "lngProductUnitCode", "strProductUnitName", $aryData["lngFirstDeliveryunitCode"], '', $objDB);
				// 対象年齢
				$aryData["lngTargetAgeCode"]			= fncGetPulldown( "m_targetage", "lngTargetAgeCode", "strTargetAgeName", $aryData["lngTargetAgeCode"], '', $objDB);
				// 証紙 テーブルなし
				$aryData["lngCertificateClassCode"]		= fncGetPulldown( "m_CertificateClass", "lngcertificateclasscode", "strcertificateclassname", $aryData["lngCertificateClassCode"], '', $objDB);
				// 版権元
				$aryData["lngCopyrightCode"]			= fncGetPulldown( "m_copyright", "lngcopyrightcode", "strcopyrightname", $aryData["lngCopyrightCode"], '', $objDB);
				// 商品形態 テーブルなし
				$aryData["lngProductFormCode"]			= fncGetPulldown( "m_productform", "lngproductformcode", "strproductformname", $aryData["lngProductFormCode"], '', $objDB);
				// 企画進行状況 
				$aryData["lngGoodsPlanProgressCode"]	= fncGetPulldown( "m_goodsplanprogress", "lnggoodsplanprogresscode", "strgoodsplanprogressname", $aryData["lngGoodsPlanProgressCode"], '', $objDB);

				// 備考
				if( strcmp( $aryData["strSpecificationDetails"], "") != 0 )
				{
					$aryData["strSpecificationDetails"] = stripslashes($aryData["strSpecificationDetails"]);
					$aryData["strSpecificationDetails"] = htmlspecialchars($aryData["strSpecificationDetails"], ENT_COMPAT | ENT_HTML401, "ISO-8859-1");
				}



				//-------------------------------------------------------------------------
				// 状態コードが「 null / "" 」の場合、「0」を再設定
				//-------------------------------------------------------------------------
				$lngProductStatusCode = fncCheckNullStatus( $lngProductStatusCode );


				//---------------------------------------------
				// 状態の取得
				//---------------------------------------------
				$aryData["strProductStatusCodeDisplay"] = fncGetMasterValue( "m_productstatus", "lngproductstatuscode", "strproductstatusname", $lngProductStatusCode, '', $objDB );




				$aryData["strProcess"] = "check";
				$aryData["RENEW"] = TRUE;


				// submit関数
				$aryData["lngRegistConfirm"] = 0;

				echo fncGetReplacedHtml( "p/regist/parts.tmpl", $aryData, $objAuth );
				$objDB->close();
				return true;
			}
		}
	}


	//エラーがあった場合:「戻る」で戻った場合 ==========================================================================
	if($_POST["back"] == "true" )
	{
		/**
			仕様詳細画像ファイルHIDDEN生成
		*/
		if( $aryData["uploadimages"] )
		{
			for( $i = 0; $i < count($aryData["uploadimages"]); $i ++ )
			{
				$aryUploadImagesHidden[]	= "<input type=\"hidden\" name=\"uploadimages[]\" value=\"" .$aryData["uploadimages"][$i]. "\" />\n";
			}

			// 再取得用に設定
			$aryData["re_uploadimages"]	= implode( "\n", $aryUploadImagesHidden );
			$aryData["re_editordir"]	= "<input type=\"hidden\" name=\"strTempImageDir\" value=\"" .$aryData["strTempImageDir"]. "\" />\n";
		}
		else
		{
			$aryData["re_uploadimages"]	= "";
			$aryData["re_editordir"]	= "";
		}



		// 権限グループコードの取得
		$lngAuthorityGroupCode = fncGetUserAuthorityGroupCode( $lngInputUserCode, $aryData["strSessionID"], $objDB );

		// 承認ルートの生成
		// 「マネージャー」以上の場合
		if( $lngAuthorityGroupCode <= DEF_DIRECT_REGIST_AUTHORITY_CODE )
		{
			$aryData["lngWorkflowOrderCode"] = '<option value="0">承認なし</option>';
		}
		else
		{
			$aryData["lngWorkflowOrderCode"] = fncWorkFlow( $lngInputUserCode , $objDB , '');
		}



		// 部門のコード
		$aryData["lngInChargeGroupCode"]		= fncGetMasterValue( "m_group", "lnggroupcode", "strgroupdisplaycode", $aryData["lngInChargeGroupCode"], 'bytGroupDisplayFlag=true', $objDB);
		// 担当者のコード
		$aryData["lngInChargeUserCode"]			= fncGetMasterValue( "m_user", "lngusercode", "struserdisplaycode", $aryData["lngInChargeUserCode"],'', $objDB);
		// 顧客
		$aryData["lngCompanyCode"]				= fncGetMasterValue( "m_company", "lngcompanycode", "strcompanydisplaycode", $aryData["lngCompanyCode"], '',$objDB);
		//生産工場コード
		$aryData["lngFactoryCode"]				= fncGetMasterValue( "m_company", "lngcompanycode", "strcompanydisplaycode", $aryData["lngFactoryCode"],'',$objDB);
		//アッセンブリ工場コード
		$aryData["lngAssemblyFactoryCode"]		= fncGetMasterValue( "m_company", "lngcompanycode", "strcompanydisplaycode", $aryData["lngAssemblyFactoryCode"],'',$objDB);
		//納品場所コード
		$aryData["lngDeliveryPlaceCode"]		= fncGetMasterValue( "m_company", "lngcompanycode", "strcompanydisplaycode" ,$aryData["lngDeliveryPlaceCode"],'',$objDB);
		// 顧客担当者
		$lngCustomerUserCode = $aryData["lngCustomerUserCode"];

		if( strcmp( $aryData["lngCustomerUserCode"], "" ) != 0)
		{

			$aryData["strCustomerUserCode"]		= fncGetMasterValue(m_user ,lngusercode, struserdisplaycode, $aryData["strCustomerUserCode"],'',$objDB);

		}

		// 備考
		if( strcmp( $aryData["strSpecificationDetails"], "") != 0 )
		{
			$aryData["strSpecificationDetails"] = stripslashes($aryData["strSpecificationDetails"]);
			$aryData["strSpecificationDetails"] = htmlspecialchars($aryData["strSpecificationDetails"], ENT_COMPAT | ENT_HTML401, "ISO-8859-1");
		}

		// オプション値の生成
		// カテゴリー
		$aryData["lngCategoryCode"]				= fncGetPulldownQueryExec( fncSqlqueryCategory(array(0=>$objAuth->UserCode)), $aryData["lngcategorycode"], $objDB);
		// 荷姿単位
		$aryData["lngPackingUnitCode"]			= fncGetPulldown( "m_productunit", "lngProductUnitCode", "strProductUnitName", $aryData["lngPackingUnitCode"], "WHERE bytpackingconversionflag=true", $objDB);
		// 製品単位
		$aryData["lngProductUnitCode"]			= fncGetPulldown( "m_productunit", "lngProductUnitCode", "strProductUnitName", $aryData["lngProductunitCode"], "WHERE bytproductconversionflag=true", $objDB);
		// 生産予定数の単位
		$aryData["lngProductionUnitCode"]		= fncGetPulldown( "m_productunit", "lngProductUnitCode", "strProductUnitName", $aryData["lngProductionUnitCode"], '', $objDB);
		// 初回納品数の単位
		$aryData["lngFirstDeliveryUnitCode"]	= fncGetPulldown( "m_productunit", "lngProductUnitCode", "strProductUnitName", $aryData["lngFirstDeliveryunitCode"], '', $objDB);
		// 対象年齢
		$aryData["lngTargetAgeCode"]			= fncGetPulldown( "m_targetage", "lngTargetAgeCode", "strTargetAgeName", $aryData["lngTargetAgeCode"], '', $objDB);
		// 証紙 テーブルなし
		$aryData["lngCertificateClassCode"]		= fncGetPulldown( "m_CertificateClass", "lngcertificateclasscode", "strcertificateclassname", $aryData["lngCertificateClassCode"], '', $objDB);
		// 版権元
		$aryData["lngCopyrightCode"]			= fncGetPulldown( "m_copyright", "lngcopyrightcode", "strcopyrightname", $aryData["lngCopyrightCode"], '', $objDB);
		// 商品形態 テーブルなし
		$aryData["lngProductFormCode"]			= fncGetPulldown( "m_productform", "lngproductformcode", "strproductformname", $aryData["lngProductFormCode"], '', $objDB);
		// 企画進行状況 
		$aryData["lngGoodsPlanProgressCode"]	= fncGetPulldown( "m_goodsplanprogress", "lnggoodsplanprogresscode", "strgoodsplanprogressname", $aryData["lngGoodsPlanProgressCode"], '', $objDB);




		//-------------------------------------------------------------------------
		// 状態コードが「 null / "" 」の場合、「0」を再設定
		//-------------------------------------------------------------------------
		$lngProductStatusCode = fncCheckNullStatus( $lngProductStatusCode );


		//---------------------------------------------
		// 状態の取得
		//---------------------------------------------
		$aryData["strProductStatusCodeDisplay"] = fncGetMasterValue( "m_productstatus", "lngproductstatuscode", "strproductstatusname", $lngProductStatusCode, '', $objDB );



		// フォームURL
		if(strcmp($aryData["strurl"], "") == 0)
		{
			$aryResult["strurl"] = "renew2.php";
		}

		$aryData["strProcess"] = "check";
		$aryData["RENEW"] = TRUE;

		// submit関数
		$aryData["lngRegistConfirm"] = 0;

		echo fncGetReplacedHtml( "p/regist/parts.tmpl", $aryData, $objAuth );
		$objDB->close();
		return true;
	}






	// 権限グループコード(ユーザー以下)チェック
	$blnAG = fncCheckUserAuthorityGroupCode( $lngInputUserCode, $aryData["strSessionID"], $objDB );

	// 「ユーザー」以下の場合
	if( $blnAG )
	{
		// 承認ルート存在チェック
		$blnWF = fncCheckWorkFlowRoot( $lngInputUserCode, $aryData["strSessionID"], $objDB );

		// 承認ルートが存在しない場合
		if( !$blnWF )
		{
			fncOutputError( 9060, DEF_WARNING, "", TRUE, "", $objDB );
		}
	}





	//最初の画面 ========================================================================================================







	$strProductCode = $_GET['strProductCode']; 


	$aryQuery = array();
	$aryQuery[] = "SELECT ";
	$aryQuery[] = "lngproductno, ";
	$aryQuery[] = "strProductCode, ";																		//2:製品コード
	$aryQuery[] = "strProductName, ";																		//3:製品名称
	$aryQuery[] = "strProductEnglishName, ";	 															//4:製品名称(英語)
	$aryQuery[] = "lngInChargeGroupCode, ";																	//5:部門
	$aryQuery[] = "lngInChargeUserCode, ";																	//6:担当者
	$aryQuery[] = "lnginputusercode, ";																		//7:入力者
																											//8:顧客識別コード(表示のみ)
	$aryQuery[] = "strGoodsCode, ";																			//9:商品コード
	$aryQuery[] = "strGoodsName, ";																			//10:商品名称
	$aryQuery[] = "lngCustomerCompanyCode, ";																//11:顧客
	//$aryQuery[] = "lngcustomergroupcode, ";																//12:顧客部門(NULL)
	$aryQuery[] = "lngCustomerUserCode, ";																	//13:顧客担当者コード (NULL)
	$aryQuery[] = "strCustomerUserName, ";																	//14:顧客担当者()
	$aryQuery[] = "lngPackingUnitCode, ";																	//15:荷姿単位(int2)
	$aryQuery[] = "lngProductUnitCode, ";																	//16:製品単位(int2)
	$aryQuery[] = "trim(To_char(lngBoxQuantity, '9,999,999,999')) as lngBoxQuantity, ";						//17:内箱（袋）入数(int4)
	$aryQuery[] = "trim(To_char(lngCartonQuantity,'9,999,999,999')) as lngCartonQuantity, ";				//18:カートン入数(int4)
	$aryQuery[] = "trim(To_char(lngProductionQuantity,'9,999,999,999')) as lngProductionQuantity, ";		//19:生産予定数()
	$aryQuery[] = "lngProductionUnitCode, ";																//20:生産予定数の単位()
	$aryQuery[] = "trim(To_char(lngFirstDeliveryQuantity,'9,999,999,999')) as lngFirstDeliveryQuantity, ";	//21:初回納品数(int4)
	$aryQuery[] = "lngFirstDeliveryUnitCode, ";																//22:初回納品数の単位()
	$aryQuery[] = "lngFactoryCode, ";																		//23:生産工場()
	$aryQuery[] = "lngAssemblyFactoryCode, ";	 															//24:アッセンブリ工場()
	$aryQuery[] = "lngDeliveryPlaceCode, ";																	//25:納品場所(int2)
	$aryQuery[] = "To_char(dtmDeliveryLimitDate,'YYYY/MM') as dtmDeliveryLimitDate, ";						//26:納品期限日()
	$aryQuery[] = "trim(To_char(curProductPrice, '9,999,999,990.99')) as curProductPrice, ";				//27:卸値()
	$aryQuery[] = "trim(To_char(curRetailPrice, '9,999,999,990.99')) as curRetailPrice,";					//28:売値()
	$aryQuery[] = "lngTargetAgeCode, ";																		//29:対象年齢()
	$aryQuery[] = "trim(To_char(lngRoyalty, '990.99')) as lngRoyalty,";										//30:ロイヤルティー()
	$aryQuery[] = "lngCertificateClassCode, "; 																//31:証紙()
	$aryQuery[] = "lngCopyrightCode, ";																		//32:版権元()
	$aryQuery[] = "strCopyrightDisplayStamp, ";																//33:版権表示(刻印)
	$aryQuery[] = "strCopyrightDisplayPrint, ";																//34:版権表示(印刷物)
	$aryQuery[] = "lngProductFormCode, ";																	//35:商品形態()
	$aryQuery[] = "strProductComposition, ";																//36:製品構成()
	$aryQuery[] = "strAssemblyContents, "; 																	//37:アッセンブリ内容()
	$aryQuery[] = "strSpecificationDetails, "; 																//38:仕様詳細()
	$aryQuery[] = "strNote, ";																				//39:備考
	//$aryQuery[] = "bytinvalidflag, ";																		//40:無効フラグ
	$aryQuery[] = "To_char(dtmInsertDate,'YYYY/MM/DD HH24:MI') as dtmInsertDate, ";							//41:登録日
	//$aryQuery[] = "dtmUpdateDate ";																		//42:更新日
	$aryQuery[] = "strcopyrightnote, ";																		//43:版権元備考
	$aryQuery[] = "lngProductStatusCode, ";																	// 商品状態
	$aryQuery[] = "lngCategoryCode ";																		// カテゴリー

	$aryQuery[] = "FROM m_product ";
	$aryQuery[] = "WHERE  bytinvalidflag = false AND ";
	$aryQuery[] = "strProductCode = '$strProductCode'";

	$strQuery = "";
	$strQuery = implode("\n", $aryQuery);

	//echo "$strQuery<br><br>";

	$objDB->freeResult( $lngResultID );
	if ( !$lngResultID = $objDB->execute( $strQuery ) )
	{
		fncOutputError ( 9051, DEF_ERROR, "", TRUE, "", $objDB );
		$objDB->close();
		return true;

	}

	if ( !$lngResultNum = pg_Num_Rows( $lngResultID ) )
	{
		fncOutputError ( 303, DEF_ERROR, "", TRUE, "", $objDB );
		$objDB->close();
		return true;
	}

	$aryResult = array();
	$aryResult = $objDB->fetchArray( $lngResultID, 0 );





	//-------------------------------------------------------------------------
	// ■「製品」にログインユーザーが属しているかチェック
	//-------------------------------------------------------------------------
	$strFncFlag = "P";
	$blnCheck = fncCheckInChargeProduct( $aryResult["lngproductno"], $lngInputUserCode, $strFncFlag, $objDB );

	// ユーザーが対象製品に属していない場合
	if( !$blnCheck )
	{
		fncOutputError( 9060, DEF_WARNING, "", TRUE, "", $objDB );
	}





	//コードから値を参照

	// 部門のコード
	$lngInchargeGroupCode					= $aryResult["lnginchargegroupcode"];
	if ( $lngInchargeGroupCode )
	{
		$aryResult["lnginchargegroupcode"]		= fncGetMasterValue( "m_group", "lnggroupcode", "strgroupdisplaycode", $lngInchargeGroupCode, 'bytGroupDisplayFlag=true', $objDB);
		// 部門の名称 
		$aryResult["strinchargegroupname"]		= fncGetMasterValue( "m_group", "lnggroupcode", "strgroupdisplayname", $lngInchargeGroupCode, "bytgroupdisplayflag=true", $objDB);
	}

	// 担当者のコード
	$lngUserCode = $aryResult["lnginchargeusercode"];

	if ( $lngUserCode )
	{
		$aryResult["lnginchargeusercode"]		= fncGetMasterValue( "m_user", "lngusercode", "struserdisplaycode", $lngUserCode,'', $objDB);
		// 担当者の名称 
		$aryResult["strinchargeusername"]		= fncGetMasterValue( "m_user", "lngusercode", "struserdisplayname", $lngUserCode,'', $objDB);
	}
	// 顧客の名称コード
	$lngCustomerCompanyCode					= $aryResult["lngcustomercompanycode"];
	if ( $lngCustomerCompanyCode ) 
	{
		$aryResult["lngCompanyCode"]			= fncGetMasterValue( "m_company", "lngcompanycode", "strcompanydisplaycode", $lngCustomerCompanyCode, '',$objDB);
		// 顧客の名称
		$aryResult["strCustomerName"]			= fncGetMasterValue( "m_company", "lngcompanycode", "strcompanydisplayname", $lngCustomerCompanyCode, '',$objDB);
		// :顧客識別コード
		$aryResult["strCustomerDistinctCode"]	= fncGetMasterValue( "m_company", "lngcompanycode", "strdistinctcode", $aryResult["lngcustomercompanycode"], '',$objDB);
	}

	//生産工場コード
	$lngFactoryCode							= $aryResult["lngfactorycode"];
	if ( $lngFactoryCode )
	{
		$aryResult["lngfactorycode"]			= fncGetMasterValue( "m_company", "lngcompanycode", "strcompanydisplaycode", $lngFactoryCode,'',$objDB);
		//納品場所の名称
		$aryResult["strFactoryName"]			= fncGetMasterValue( "m_company", "lngcompanycode", "strcompanydisplayname", $lngFactoryCode,'',$objDB);
	}

	//アッセンブリ工場コード
	$lngAssemblyFactoryCode					= $aryResult["lngassemblyfactorycode"];
	if ( $lngAssemblyFactoryCode ) 
	{
		$aryResult["lngassemblyfactorycode"]	= fncGetMasterValue( "m_company", "lngcompanycode", "strcompanydisplaycode", $lngAssemblyFactoryCode,'',$objDB);
		//アッセンブリ工場
		$aryResult["strAssemblyFactoryName"]	= fncGetMasterValue( "m_company", "lngcompanycode", "strcompanydisplayname", $lngAssemblyFactoryCode,'',$objDB);	}

	//納品場所コード
	$lngDeliveryPlaceCode					= $aryResult["lngdeliveryplacecode"];
	if ( $lngDeliveryPlaceCode )
	{
		$aryResult["lngdeliveryplacecode"]		= fncGetMasterValue( "m_company", "lngcompanycode", "strcompanydisplaycode", $lngDeliveryPlaceCode,'',$objDB);
		//納品場所
		$aryResult["strDeliveryPlaceName"]		= fncGetMasterValue( "m_company", "lngcompanycode", "strcompanydisplayname", $lngDeliveryPlaceCode,'',$objDB);
	}

	// 顧客担当者
	$lngCustomerUserCode = $aryResult["lngcustomerusercode"];

	if( strcmp( $aryResult["lngcustomerusercode"], "" ) != 0)
	{
		$aryResult["strcustomerusercode"]	= fncGetMasterValue( "m_user", "lngusercode", "struserdisplaycode", $lngCustomerUserCode, '', $objDB);
		$aryResult["strcustomerusername"]	= fncGetMasterValue( "m_user", "lngusercode", "struserdisplayname", $lngCustomerUserCode,'',$objDB);
	}

	// 仕様詳細の特殊文字変換
	$aryResult["strspecificationdetails"] = fncHTMLSpecialChars( $aryResult["strspecificationdetails"] );


	//オプション値の設定 ==============================================================
	// 連想配列のインデックスには、小文字で指定しないとだめ
	
	// カテゴリー
	$aryResult["lngcategorycode"]			= fncGetPulldownQueryExec( fncSqlqueryCategory(array(0=>$objAuth->UserCode)), $aryResult["lngcategorycode"], $objDB);
	// 荷姿単位
	$aryResult["lngpackingunitcode"]		= fncGetPulldown( "m_productunit", "lngProductUnitCode", "strProductUnitName", $aryResult["lngpackingunitcode"], "WHERE bytpackingconversionflag=true", $objDB);
	// 製品単位
	$aryResult["lngproductunitcode"]		= fncGetPulldown( "m_productunit", "lngProductUnitCode", "strProductUnitName", $aryResult["lngproductunitcode"], "WHERE bytproductconversionflag=true", $objDB);
	// 生産予定数の単位
	$aryResult["lngproductionunitcode"]		= fncGetPulldown( "m_productunit", "lngProductUnitCode", "strProductUnitName", $aryResult["lngproductionunitcode"], '', $objDB);
	// 初回納品数の単位
	$aryResult["lngfirstdeliveryunitcode"]	= fncGetPulldown( "m_productunit", "lngProductUnitCode", "strProductUnitName", $aryResult["lngfirstdeliveryunitcode"], '', $objDB);
	// 対象年齢
	$aryResult["lngtargetagecode"]			= fncGetPulldown( "m_targetage", "lngTargetAgeCode", "strTargetAgeName", $aryResult["lngtargetagecode"], '', $objDB);
	// 証紙 テーブルなし
	$aryResult["lngcertificateclasscode"]	= fncGetPulldown( "m_CertificateClass", "lngcertificateclasscode", "strcertificateclassname", $aryResult["lngcertificateclasscode"], '', $objDB);
	// 版権元
	$aryResult["lngcopyrightcode"]			= fncGetPulldown( "m_copyright", "lngcopyrightcode", "strcopyrightname", $aryResult["lngcopyrightcode"], '', $objDB);
	// 商品形態 テーブルなし
	$aryResult["lngproductformcode"]		= fncGetPulldown( "m_productform", "lngproductformcode", "strproductformname", $aryResult["lngproductformcode"], '', $objDB);


//fncDebug("p_renew_category.txt", $aryResult["lngCategoryCode"], __FILE__, __LINE__ );

	// 企画進行状況 ===================================================================
	$lngproductno = $aryResult["lngproductno"];
	$aryQuery2[] = "SELECT lnggoodsplancode,lngrevisionno,lnggoodsplanprogresscode, ";
	$aryQuery2[] = "To_char(dtmrevisiondate,'YYYY/MM/DD HH24:MI') as dtmrevisiondate "; 
	$aryQuery2[] = "FROM t_goodsplan WHERE lnggoodsplancode = (";
	$aryQuery2[] = "SELECT max(lnggoodsplancode) FROM t_goodsplan WHERE lngproductno = ";
	$aryQuery2[] = "$lngproductno )";


	$strQuery2 = "";
	$strQuery2 = implode("\n", $aryQuery2);


	//echo "$strQuery2<br><br>";
	$objDB->freeResult( $lngResultID2 );
	if ( !$lngResultID2 = $objDB->execute( $strQuery2 ) )
	{
		fncOutputError ( 9051, DEF_ERROR, "", TRUE, "", $objDB );
		$objDB->close();
		return true;

	}

	if ( !$lngResultNum = pg_Num_Rows( $lngResultID2 ) )
	{
		fncOutputError ( 303, DEF_ERROR, "", TRUE, "", $objDB );
		$objDB->close();
		return true;
	}

	$aryResult2 = array();
	$aryResult2 = $objDB->fetchArray( $lngResultID2, 0 );



	// 企画進行状況 =============================================================
	$aryResult["lngGoodsPlanProgressCode"]	= fncGetPulldown(m_goodsplanprogress, lnggoodsplanprogresscode, strgoodsplanprogressname, $aryResult2["lnggoodsplanprogresscode"], '', $objDB);
	//改訂番号
	$aryResult["lngRevisionNo"]				= $aryResult2["lngrevisionno"];
	//改訂日時
	$aryResult["dtmRevisionData"]			= $aryResult2["dtmrevisiondate"];
	//goodsplancode
	$aryResult["lnGgoodsPlanCode"]			= $aryResult2["lnggoodsplancode"];
	$aryResult["strProcess"]				= "check";



//var_dump( $aryResult["lngproductstatuscode"] );exit();

	//-------------------------------------------------------------------------
	// 商品状態のチェック
	//-------------------------------------------------------------------------
	// 申請中の場合
	if( $aryResult["lngproductstatuscode"] == DEF_PRODUCT_APPLICATE )
	{
		fncOutputError( 307, DEF_WARNING, "", TRUE, "", $objDB );
	}




	// 承認ルートの取得
	$lngWorkflowOrderCode = fncGetMasterValue( "m_workflow", "strworkflowkeycode", "lngworkflowordercode", $aryResult["lngproductno"].":str", '', $objDB );


	// 権限グループコードの取得
	$lngAuthorityGroupCode = fncGetUserAuthorityGroupCode( $lngInputUserCode, $aryData["strSessionID"], $objDB );

	// 承認ルートの生成
	// 「マネージャー」以上の場合
	if( $lngAuthorityGroupCode <= DEF_DIRECT_REGIST_AUTHORITY_CODE )
	{
		$aryResult["lngWorkflowOrderCode"] = '<option value="0">承認なし</option>';
	}
	else
	{
		$aryResult["lngWorkflowOrderCode"] = fncWorkFlow( $lngInputUserCode , $objDB , $lngWorkflowOrderCode );
	}

	//-------------------------------------------------------------------------
	// イメージファイルの取得処理
	//-------------------------------------------------------------------------

	$objImageLo = new clsImageLo();
	$strDestPath = constant("USER_IMAGE_PEDIT_TMPDIR");
	// キーコード（製品コード）を基にして、イメージファイルの抽出処理（関連画像がテンポラリディレクトリに出力される）
	$objImageLo->getImageLo($objDB, $strProductCode, $strDestPath, $aryImageInfo);




	// フォームURL
	if( strcmp( $aryData["strurl"], "" ) == 0 )
	{
		$aryResult["strurl"] = 'renew.php?strProductCode=$strProductCode&strSessionID=$aryData["strSessionID"]';
	}

	$aryResult["strActionURL"] = 'renew.php?strProductCode=$strProductCode&strSessionID=$aryData["strSessionID"]';

	$aryResult["strSessionID"] = $aryData["strSessionID"];
	$aryResult["RENEW"] = TRUE;

	// submit関数
	$aryResult["lngRegistConfirm"] = 0;

	// ヘルプ対応
	$aryResult["lngFunctionCode"] = DEF_FUNCTION_P6;



/**
	debug

	仕様詳細画像ファイルHIDDEN生成
*/
// 再取得用に設定
$lngImageCnt	= count($aryImageInfo['strTempImageFile']);

if( $lngImageCnt )
{
	for( $i = 0; $i < $lngImageCnt; $i++ )
	{
		$aryUploadImagesHidden[]	= '<input type="hidden" name="uploadimages[]" value="' .$aryImageInfo['strTempImageFile'][$i]. '" />';
	}

	// 再取得用に設定
	$aryResult["re_uploadimages"]	= implode( "\n", $aryUploadImagesHidden );
	$aryResult["re_editordir"]		= '<input type="hidden" name="strTempImageDir" value="' .$aryImageInfo['strTempImageDir'][0]. '" />';
}

// debug file出力
//fncDebug("p_renew.txt", fncGetReplacedHtml( "p/regist/parts.tmpl", $aryResult, $objAuth ), __FILE__, __LINE__ );



	echo fncGetReplacedHtml( "p/regist/parts.tmpl", $aryResult, $objAuth );

	$objDB->close();
	return true;

?>