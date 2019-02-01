<?php

// ----------------------------------------------------------------------------
/**
*       商品管理  登録
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
*         ・登録処理
*         ・エラーチェック
*         ・登録処理完了後、登録完了画面へ
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
	require( SRC_ROOT . "po/cmn/lib_po.php" );
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

	$aryData["strSessionID"]         = $_REQUEST["strSessionID"];          // セッションID
	$aryData["lngLanguageCode"]      = $_COOKIE["lngLanguageCode"];        // 言語コード


	//-------------------------------------------------------------------------
	// ■ 入力文字列値・セッション・権限チェック
	//-------------------------------------------------------------------------
	// 文字列チェック
	$aryCheck["strSessionID"]          = "null:numenglish(32,32)";
	$aryResult = fncAllCheck( $aryData, $aryCheck );
	fncPutStringCheckError( $aryResult, $objDB );

	// セッション確認
	$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

	$lngUserCode = $objAuth->UserCode;



	// 300 商品管理
	if ( !fncCheckAuthority( DEF_FUNCTION_P0, $objAuth ) )
	{
	        fncOutputError ( 9018, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
	}

	// 301 商品管理（商品登録）
	if ( !fncCheckAuthority( DEF_FUNCTION_P1, $objAuth ) )
	{
	        fncOutputError ( 9018, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
	}

	// 入力者コードの取得
	$lngInputUserCode = $objAuth->UserCode;
	if( !$lngInputUserCode )
	{
		fncOutputError ( 9061, DEF_ERROR, "", TRUE, "", $objDB );
	}

	if(strcmp($aryData["strSpecificationDetails"], "") != 0)
	{
		$aryData["strSpecificationDetails"] = stripslashes($aryData["strSpecificationDetails"]);
	}



	// insert用のデータ(displaycodeからcodeへ）
	// 部門コード
	$aryData["lngInChargeGroupCode"]	= fncGetMasterValue( "m_group", "strgroupdisplaycode", "lnggroupcode",  $aryData['lngInChargeGroupCode'] . ":str",'',$objDB);
	// 担当者(入力時に部門コードを参照しているのでここでは部門コードは参照しない
	$aryData["lngInChargeUserCode"]		= fncGetMasterValue( "m_user", "struserdisplaycode" ,"lngusercode" , $aryData["lngInChargeUserCode"] . ":str",'',$objDB);
	// 顧客
	$aryData["lngCompanyCode"]			= fncGetMasterValue( "m_company", "strcompanydisplaycode", "lngcompanycode", $aryData["lngCompanyCode"] . ":str", '',$objDB);
	// 生産工場
	$aryData["lngFactoryCode"]			= fncGetMasterValue( "m_company", "strcompanydisplaycode", "lngcompanycode", $aryData["lngFactoryCode"] . ":str", '',$objDB);
	// アッセンブリ工場
	$aryData["lngAssemblyFactoryCode"]	= fncGetMasterValue( "m_company", "strcompanydisplaycode", "lngcompanycode", $aryData["lngAssemblyFactoryCode"] . ":str", '',$objDB);
	// 納品場所
	$aryData["lngDeliveryPlaceCode"]	= fncGetMasterValue( "m_company", "strcompanydisplaycode", "lngcompanycode", $aryData["lngDeliveryPlaceCode"] . ":str", '',$objDB);


	// 顧客担当者コード
	if( strcmp( $aryData["lngCompanyCode"], "" ) != 0 )
	{
		$aryData["strCustomerUserCode"]	= fncGetMasterValue( "m_user", "struserdisplaycode" ,"lngusercode" , $aryData["strCustomerUserCode"] . ":str","lngCompanycode=".$aryData["lngCompanyCode"],$objDB);
	}




	//-------------------------------------------------------------------------
	// ■ トランザクション開始
	//-------------------------------------------------------------------------
	$objDB->transactionBegin();





	//-------------------------------------------------------------------------
	// ■ m_Productのシーケンス番号を取得
	//-------------------------------------------------------------------------
	// シーケンス関数呼び出し
	$sequence_m_product = fncGetSequence( "m_product.lngproductno", $objDB );

	// シーケンス番号を4桁に統一
	$sequence_code = $sequence_m_product;

	$fig = strlen( $sequence_code );
	$sequence_code = sprintf( "%05d" , $sequence_code );
	// echo "sequence_code : $sequence_code<br>";




	// 状態コードの取得
	$lngProductStatusCode = ( $aryData["lngWorkflowOrderCode"] == 0 ) ? DEF_PRODUCT_NORMAL : DEF_PRODUCT_APPLICATE;




	$strCopyrightNote = ( $aryData["strCopyrightNote"] == "null" ) ? "null" : "'".$aryData["strCopyrightNote"]."'" ;

	$aryQuery = array();

	$aryQuery[] = "INSERT INTO m_product (";
	$aryQuery[] = "lngproductno, ";															// 1:製品番号
	$aryQuery[] = "strProductCode, ";														// 2:製品コード
	$aryQuery[] = "strProductName, ";														// 3:製品名称
	$aryQuery[] = "strProductEnglishName, ";	 											// 4:製品名称(英語)
	$aryQuery[] = "lngInChargeGroupCode, ";													// 5:部門
	$aryQuery[] = "lngInChargeUserCode, ";													// 6:担当者
	$aryQuery[] = "lnginputusercode, ";														// 7:入力者
																							// 8:顧客識別コード(表示のみ)
	$aryQuery[] = "strGoodsCode, ";															// 9:商品コード
	$aryQuery[] = "strGoodsName, ";															// 10:商品名称
	$aryQuery[] = "lngCustomerCompanyCode, ";												// 11:顧客
	$aryQuery[] = "lngcustomergroupcode, ";													// 12:顧客部門(NULL)
	$aryQuery[] = "lngCustomerUserCode, ";													// 13:顧客担当者コード
	$aryQuery[] = "strCustomerUserName, ";													// 14:顧客担当者()
	$aryQuery[] = "lngPackingUnitCode, ";													// 15:荷姿単位(int2)
	$aryQuery[] = "lngProductUnitCode, ";													// 16:製品単位(int2)
	$aryQuery[] = "lngBoxQuantity, ";														// 17:内箱（袋）入数(int4)
	$aryQuery[] = "lngCartonQuantity, ";													// 18:カートン入数(int4)
	$aryQuery[] = "lngProductionQuantity, ";												// 19:生産予定数()
	$aryQuery[] = "lngProductionUnitCode, ";												// 20:生産予定数の単位()
	$aryQuery[] = "lngFirstDeliveryQuantity, ";												// 21:初回納品数(int4)
	$aryQuery[] = "lngFirstDeliveryUnitCode, ";												// 22:初回納品数の単位()
	$aryQuery[] = "lngFactoryCode, ";														// 23:生産工場()
	$aryQuery[] = "lngAssemblyFactoryCode, ";		 										// 24:アッセンブリ工場()
	$aryQuery[] = "lngDeliveryPlaceCode, ";													// 25:納品場所(int2)
	$aryQuery[] = "dtmDeliveryLimitDate, ";													// 26:納品期限日()
	$aryQuery[] = "curProductPrice, ";		 												// 27:卸値()
	$aryQuery[] = "curRetailPrice, ";														// 28:売値()
	$aryQuery[] = "lngTargetAgeCode, ";														// 29:対象年齢()
	$aryQuery[] = "lngRoyalty, ";															// 30:ロイヤルティー()
	$aryQuery[] = "lngCertificateClassCode, "; 												// 31:証紙()
	$aryQuery[] = "lngCopyrightCode, ";														// 32:版権元()
	$aryQuery[] = "strCopyrightDisplayStamp, ";												// 33:版権表示(刻印)
	$aryQuery[] = "strCopyrightDisplayPrint, ";												// 34:版権表示(印刷物)
	$aryQuery[] = "lngProductFormCode, ";													// 35:商品形態()
	$aryQuery[] = "strProductComposition, ";												// 36:製品構成()
	$aryQuery[] = "strAssemblyContents, "; 													// 37:アッセンブリ内容()
	$aryQuery[] = "strSpecificationDetails, "; 												// 38:仕様詳細()
	$aryQuery[] = "strNote, ";																// 39:備考
	$aryQuery[] = "bytinvalidflag, ";														// 40:無効フラグ
	$aryQuery[] = "dtmInsertDate, ";														// 41:登録日
	$aryQuery[] = "dtmUpdateDate, ";														// 42:更新日
	$aryQuery[] = "strcopyrightnote ,";														// 43:版権元備考
	$aryQuery[] = "lngproductstatuscode,";													// 商品状態
	$aryQuery[] = "lngCategoryCode";														// カテゴリーコード


	$aryQuery[] = " ) values ( ";

	$aryQuery[] = "$sequence_m_product," ;													// 1:製品番号
	$aryQuery[] = "'$sequence_code',";														// 2:製品コード()
	$aryQuery[] = "'".$aryData["strProductName"] ."', ";									// 3:製品名称()
	$aryQuery[] = "'".$aryData["strProductEnglishName"]."', ";	 							// 4:製品名称(英語)()
	$aryQuery[] = $aryData["lngInChargeGroupCode"].",";										// 5:部門()
	$aryQuery[] = $aryData["lngInChargeUserCode"].",";										// 6:担当者(int2)
	$aryQuery[] = "$lngInputUserCode,";														// 7:入力者()
																							// 8:顧客識別コード()
	$aryQuery[] = "'".$aryData["strGoodsCode"]."', ";										// 9:商品コード()
	$aryQuery[] = "'".$aryData["strGoodsName"]."', ";										// 10:商品名称()
	if ( $aryData["lngCompanyCode"] and $aryData["lngCompanyCode"] != "" )
	{
		$aryQuery[] = $aryData["lngCompanyCode"].",";										// 11:顧客()
	}
	else
	{
		$aryQuery[] = "null, ";																// 11:顧客()
	}
	$aryQuery[] = "null, ";																	// 12:顧客部門(NULL)

	if( strcmp( $aryData["strCustomerUserCode"], "" ) != 0)
	{
		$aryQuery[] = "'".$aryData["strCustomerUserCode"]."', ";							// 13:顧客担当者コード
		$aryQuery[] = "null, ";																// 14:顧客担当者()
	}
	elseif( strcmp( $aryData["strCustomerUserCode"], "" ) == 0 && strcmp( $aryData["strCustomerUserName"], "") != 0)
	{
		$aryQuery[] = "null, ";																// 13:顧客担当者コード
		$aryQuery[] = "'".$aryData["strCustomerUserName"]."', ";							// 14:顧客担当者()
	}
	else
	{
		$aryQuery[] = "null, ";																// 13:顧客担当者コード
		$aryQuery[] = "null, ";																// 14:顧客担当者()
	}

	$aryQuery[] = $aryData["lngPackingUnitCode"].",";										// 15:荷姿単位(int2)
	$aryQuery[] = $aryData["lngProductUnitCode"].",";										// 16:製品単位(int2)
	if ( $aryData["lngBoxQuantity"] and $aryData["lngBoxQuantity"] != "" )
	{
		$aryQuery[] = "to_number('" .$aryData["lngBoxQuantity"]."','9999999999.9999'),";	// 17:内箱（袋）入数(int4)
	}
	else
	{
		$aryQuery[] = "null, ";																// 17:内箱入数
	}
	$aryQuery[] = "to_number('" .$aryData["lngCartonQuantity"]."','9999999999.9999'),";		// 18:カートン入数(int4)
	$aryQuery[] = "to_number('" .$aryData["lngProductionQuantity"]."','9999999999.9999'),";	// 19:生産予定数()
	$aryQuery[] = $aryData["lngProductionUnitCode"].",";									// 20:生産予定数の単位()
	$aryQuery[] = "to_number('" .$aryData["lngFirstDeliveryQuantity"]."','9999999999.9999'),";			// 21:初回納品数(int4)
	$aryQuery[] = $aryData["lngFirstDeliveryUnitCode"].",";									// 22:初回納品数の単位()
	if ( $aryData["lngFactoryCode"] and $aryData["lngFactoryCode"] != "" )
	{
		$aryQuery[] = $aryData["lngFactoryCode"].",";										// 23:生産工場()
	}
	else
	{
		$aryQuery[] = "null, ";																// 23:生産工場()
	}
	if ( $aryData["lngAssemblyFactoryCode"] and $aryData["lngAssemblyFactoryCode"] != "" )
	{
		$aryQuery[] = $aryData["lngAssemblyFactoryCode"].",";		 						// 24:アッセンブリ工場()
	}
	else
	{
		$aryQuery[] = "null, ";		 														// 24:アッセンブリ工場()
	}
	if ( $aryData["lngDeliveryPlaceCode"] and $aryData["lngDeliveryPlaceCode"] != "" )
	{
		$aryQuery[] = $aryData["lngDeliveryPlaceCode"].",";									// 25:納品場所(int2)
	}
	else
	{
		$aryQuery[] = "null, ";																// 25:納品場所(int2)
	}
	$aryQuery[] = "To_timestamp('". $aryData["dtmDeliveryLimitDate"] ."', 'YYYY/mm'),";		// 26:納品期限日()
	$aryQuery[] = "to_number('" .$aryData["curProductPrice"]."','9999999999.9999'),";		// 27:卸値()
	$aryQuery[] = "to_number('" .$aryData["curRetailPrice"]. "','9999999999.9999'),";		// 28:売値()
	$aryQuery[] = $aryData["lngTargetAgeCode"].",";											// 29:対象年齢()
// 2004.06.17 suzukaze update start

//	$aryQuery[] = "to_number('" .$aryData["lngRoyalty"]."','999.99'),";						// 30:ロイヤルティー()

	if ( $aryData["lngRoyalty"] and $aryData["lngRoyalty"] != "" )
	{
		$aryQuery[] = "to_number('" .$aryData["lngRoyalty"]."','999.99'),";	                                // 30:ロイヤルティー()
	}
	else
	{
		$aryQuery[] = "null, ";	
	}


// 2004.06.17 suzukaze update end
	$aryQuery[] = $aryData["lngCertificateClassCode"].","; 									// 31:証紙()
	$aryQuery[] = $aryData["lngCopyrightCode"].",";											// 32:版権元()
	$aryQuery[] = "'".$aryData["strCopyrightDisplayStamp"]."', ";							// 33:版権表示(刻印)
	$aryQuery[] = "'".$aryData["strCopyrightDisplayPrint"]."', ";							// 34:版権表示(印刷物)
	$aryQuery[] = $aryData["lngProductFormCode"].",";										// 35:商品形態()
	$aryQuery[] = "'".$aryData["strProductComposition"]."', ";								// 36:製品構成()
	$aryQuery[] = "'".$aryData["strAssemblyContents"]."', "; 								// 37:アッセンブリ内容()
//	$aryQuery[] = "'".addslashes( $aryData["strSpecificationDetails"] )."', "; 				// 38:仕様詳細()
	$aryQuery[] = "'". stripslashes( $aryData["strSpecificationDetails"] )."', "; 				// 38:仕様詳細()
	$aryQuery[] = "null, ";																	// 39:備考
	$aryQuery[] = "false, ";																// 40:無効フラグ
	$aryQuery[] = "'now()',";																// 41:登録日
	$aryQuery[] = "'now()',";																// 42:更新日
	$aryQuery[] = $strCopyrightNote . ", ";													// 43:版権元備考
	$aryQuery[] = $lngProductStatusCode. ", ";												// 商品状態
	$aryQuery[] = $aryData["lngCategoryCode"];												// カテゴリーコード
	$aryQuery[] = ")" ;


	$strQuery = "";
	$strQuery = implode("\n", $aryQuery);

// 2004.06.17 suzukaze update start

	//トランザクション開始
//	$objDB->transactionBegin();

// 2004.06.17 suzukaze update start

	if ( !$lngResultID = $objDB->execute( $strQuery ) )
	{
		fncOutputError ( 9051, DEF_ERROR, "", FALSE, "", $objDB );
		$objDB->close();
		return true;
	}



	//GOODS_PLANEの登録
	// シーケンス関数呼び出し
	$sequence_t_goodsplan = fncGetSequence( 't_goodsplan.lnggoodsplancode', $objDB );

	$aryQueryGoods = array();
	$aryQueryGoods[] = "INSERT INTO t_goodsplan ( ";
	$aryQueryGoods[] = "lnggoodsplancode, ";
	$aryQueryGoods[] = "lngrevisionno, ";
	$aryQueryGoods[] = "lngproductno, ";
	$aryQueryGoods[] = "dtmcreationdate, ";
	$aryQueryGoods[] = "dtmrevisiondate, ";
	$aryQueryGoods[] = "lnggoodsplanprogresscode, ";
	$aryQueryGoods[] = "lnginputusercode ";
	$aryQueryGoods[] = " ) values ( ";
	$aryQueryGoods[] = "$sequence_t_goodsplan, ";
	$aryQueryGoods[] = "0, ";
	$aryQueryGoods[] = "$sequence_m_product, ";
	$aryQueryGoods[] = "'now()',";
	$aryQueryGoods[] = "'now()',";
	$aryQueryGoods[] = $aryData["lngGoodsPlanProgressCode"].", ";
	$aryQueryGoods[] = "$lngInputUserCode ";
	$aryQueryGoods[] = ")" ;

	$strQueryGoods = "";
	$strQueryGoods = implode("\n", $aryQueryGoods);


	if ( !$lngResultID = $objDB->execute( $strQueryGoods ) )
	{
		fncOutputError ( 9051, DEF_ERROR, "", FALSE, "", $objDB );
		$objDB->close();

		return true;
	}

	//-------------------------------------------------------------------------
	// イメージファイルの登録処理
	//-------------------------------------------------------------------------
/*
	// アップロード画像が存在するかを確認する
	if(!empty($aryData["uploadimages"]))
	{
		// イメージ処理オブジェクト生成
		$objImageLo = new clsImageLo();
		$lngUploadImageCount = count($aryData["uploadimages"]);

		// 出力先パスの設定
		$strDestPath = constant("USER_IMAGE_PEDIT_TMPDIR");

		// アップロードされた対象の画像パス情報を基に、ラージオブジェクト操作オブジェクトを用いてデータベースへ登録
		for($i = 0; $i < $lngUploadImageCount; $i++)
		{
			$aryImageInfo = array();
			$aryImageInfo['type'] = "";
			$aryImageInfo['size'] = 0;
			$blnRet = $objImageLo->addImageLo($objDB, $sequence_code, $aryImageInfo, $strDestPath, $aryData["strTempImageDir"], $aryData["uploadimages"][$i]);
			if(!$blnRet)
			{
				// DBへ画像の登録が出来ませんでした
			}
		}
	}
*/


//fncDebug( 'lib_so.txt', $aryData, __FILE__, __LINE__);

	//-------------------------------------------------------------------------
	// ■ 承認処理
	//
	//   承認ルート
	//     ・0 : 承認ルートなし
	//-------------------------------------------------------------------------
	$lngWorkflowOrderCode = $aryData["lngWorkflowOrderCode"];	// 承認ルート

	$strWFName   = "商品 [No:" . $sequence_code . "]";
	$lngSequence = $sequence_m_product;
	$strDefFnc   = DEF_FUNCTION_P1;

	//$strProductCode       = $aryData["aryPoDitail"][0]["strProductCode"];
	//$lngApplicantUserCode = fncGetMasterValue( "m_product", "strproductcode", "lnginchargeusercode", $strProductCode . ":str", '', $objDB );
	$lngApplicantUserCode = $aryData["lngInChargeUserCode"];


	// 承認ルートが選択された場合
	if( $lngWorkflowOrderCode != 0 )
	{
		//---------------------------------------------------------------
		// DB -> INSERT : m_workflow
		//---------------------------------------------------------------
		// m_workflow のシーケンスを取得
		$lngworkflowcode = fncGetSequence( 'm_Workflow.lngworkflowcode', $objDB );
		$strworkflowname = $strWFName;

		$aryQuery   = array();
		$aryQuery[] = "INSERT INTO m_workflow (";
		$aryQuery[] = "lngworkflowcode, ";							// 1  : ワークフローコード
		$aryQuery[] = "lngworkflowordercode, ";						// 2  : ワークフロー順序コード
		$aryQuery[] = "strworkflowname, ";							// 3  : ワークフロー名称
		$aryQuery[] = "lngfunctioncode, ";							// 4  : 機能コード
		$aryQuery[] = "strworkflowkeycode, ";						// 5  : ワークフローキーコード
		$aryQuery[] = "dtmstartdate, ";								// 6  : 案件発生日
		$aryQuery[] = "dtmenddate, ";								// 7  : 案件終了日
		$aryQuery[] = "lngapplicantusercode, ";						// 8  : 案件申請者コード
		$aryQuery[] = "lnginputusercode, ";							// 9  : 案件入力者コード
		$aryQuery[] = "bytinvalidflag, ";							// 10 : 無効フラグ
		$aryQuery[] = "strnote";									// 11 : 備考

		$aryQuery[] = " ) values (";
		$aryQuery[] = "$lngworkflowcode, ";							// 1  : ワークフローコード
		$aryQuery[] = ( $lngWorkflowOrderCode != "" ) ? $lngWorkflowOrderCode . ", " : "null, "; // 2  : ワークフロー順序コード
		$aryQuery[] = "'$strworkflowname', ";						// 3  : ワークフロー名称
		$aryQuery[] = $strDefFnc . ", ";							// 4  : 機能コード
		$aryQuery[] = $lngSequence . ", ";							// 5  : ワークフローキーコード
		$aryQuery[] = "now(), ";									// 6  : 案件発生日
		$aryQuery[] = "null, ";										// 7  : 案件終了日
		$aryQuery[] = $lngApplicantUserCode . ", ";					// 8  : 案件申請者コード
		$aryQuery[] = "$lngUserCode, ";								// 9  : 案件入力者コード
		$aryQuery[] = "false, ";									// 10 : 無効フラグ
		$aryQuery[] = "null";										// 11 : 備考
		$aryQuery[] = " )";

		$strQuery = "";
		$strQuery = implode( "\n", $aryQuery );


		// クエリ実行
		$lngResultID = $objDB->execute( $strQuery );


		// クエリ実行失敗の場合
		if( !$lngResultID )
		{
			fncOutputError ( 9051, DEF_ERROR, "", TRUE, "", $objDB );
			$objDB->close();
			return true;
		}

		// 結果IDを解放
		$objDB->freeResult( $lngResultID );
		//---------------------------------------------------------------



		// 有効期限日の取得
		$lngLimitDate = fncGetMasterValue( "m_workfloworder" ,"lngworkflowordercode", "lnglimitdays", $lngWorkflowOrderCode ,"lngworkfloworderno = 1", $objDB );

		//echo "期限日：$lngLimitDate<br>";



		//---------------------------------------------------------------
		// DB -> INSERT : t_workflow
		//---------------------------------------------------------------
		$aryQuery   = array();
		$aryQuery[] = "INSERT INTO t_workflow (";
		$aryQuery[] = "lngworkflowcode, ";								// ワークフローコード
		$aryQuery[] = "lngworkflowsubcode, ";							// ワークフローサブコード
		$aryQuery[] = "lngworkfloworderno, ";							// ワークフロー順序番号
		$aryQuery[] = "lngworkflowstatuscode, ";						// ワークフロー状態コード
		$aryQuery[] = "strnote, ";										// 備考
		$aryQuery[] = "dtminsertdate, ";								// 登録日
		$aryQuery[] = "dtmlimitdate ";									// 期限日

		$aryQuery[] = ") values (";
		$aryQuery[] = "$lngworkflowcode, ";								// ワークフローコード
		$aryQuery[] = DEF_T_WORKFLOW_SUBCODE.", ";						// ワークフローサブコード
		$aryQuery[] = DEF_T_WORKFLOW_ORDERNO.", ";						// ワークフロー順序番号
		$aryQuery[] = DEF_T_WORKFLOW_STATUS.", ";						// ワークフロー状態コード
		$aryQuery[] = "'" . $aryData["strWorkflowMessage"] . "',";		// 11:備考
		$aryQuery[] = "now(), ";										// 登録日
		$aryQuery[] = "now() + (interval '$lngLimitDate day' )";		// 期限日
		$aryQuery[] = ")";

		$strQuery = "";
		$strQuery = implode("\n", $aryQuery );


		// クエリ実行
		$lngResultID = $objDB->execute( $strQuery );


		// クエリ実行失敗の場合
		if( !$lngResultID )
		{
			fncOutputError ( 9051, DEF_ERROR, "", TRUE, "", $objDB );
			$objDB->close();
			return true;
		}

		// 結果IDを解放
		$objDB->freeResult( $lngResultID );
		//---------------------------------------------------------------



		//---------------------------------------------------------------
		// DB -> SELECT : m_workfloworder, m_user, m_authoritygroup
		//---------------------------------------------------------------
		// 承認者にメールを送る
		$arySelect = array();
		$arySelect[] = "SELECT u.strmailaddress, ";									// メールアドレス
		$arySelect[] = "u.bytMailTransmitFlag, ";									// メール配信許可フラグ
		$arySelect[] = "w.strworkflowordername, ";									// ワークフロー名
		$arySelect[] = "u.struserdisplayname ";										// 承認者
		$arySelect[] = "FROM m_workfloworder w, m_user u, m_authoritygroup a ";
		$arySelect[]= "WHERE w.lngworkflowordercode = ";
		$arySelect[] = $lngWorkflowOrderCode." AND ";
		$arySelect[] = "u.lngusercode = w.lnginchargecode AND ";
		$arySelect[] = "u.lngauthoritygroupcode = a.lngauthoritygroupcode ";
		$arySelect[] = "ORDER BY a.lngauthoritylevel DESC";

		$strSelect = "";
		$strSelect = implode("\n", $arySelect );

		// echo "$strSelect";


		// クエリ実行
		$lngResultID = $objDB->execute( $strSelect );


		// クエリ実行成功の場合
		if( $lngResultID )
		{
			$aryResult[] = pg_fetch_array( $lngResultID, 0, PGSQL_ASSOC );
		}

		// 結果IDを解放
		$objDB->freeResult( $lngResultID );
		//---------------------------------------------------------------



		//---------------------------------------------------------------
		// DB -> SELECT : m_User
		//---------------------------------------------------------------
		// 入力者メールアドレスの取得
		$strUserMailQuery = "SELECT bytMailTransmitFlag, strMailAddress FROM m_User WHERE lngUserCode = " . $objAuth->UserCode;

		list( $lngUserMailResultID, $lngUserMailResultNum ) = fncQuery( $strUserMailQuery, $objDB );

		// クエリ実行成功の場合
		if( $lngUserMailResultNum == 1 )
		{
			$objResult = $objDB->fetchObject( $lngUserMailResultID, 0 );
			$bytInputUserMailTransmitFlag = $objResult->bytmailtransmitflag;
			$strInputUserMailAddress      = $objResult->strmailaddress;
		}
		// クエリ実行失敗の場合
		else
		{
			fncOutputError( 9051, DEF_ERROR, "データが異常です", TRUE, "po/regist/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
		}

		// 結果IDを解放
		$objDB->freeResult( $lngUserMailResultID );
		//---------------------------------------------------------------



		//---------------------------------------------------------------
		// メール送信
		//---------------------------------------------------------------
		// メール文面に必要なデータを配列 $aryMailData に格納
		$aryMailData["strmailaddress"] = $aryResult[0]["strmailaddress"];	// 承認者メールアドレス

		// メール配信許可フラグが TRUE に設定されていない場合かつ、
		// 入力者（申請者）のメールアドレスが設定されていない場合は、メール送信しない
		if( $aryResult[0]["bytmailtransmitflag"] == "t" and $aryMailData["strmailaddress"] != "" and $strInputUserMailAddress != "" )
		{
			$aryMailData                       = array();
			//$strMailAddress                    = $aryResult[0]["strmailaddress"];			// 承認者メールアドレス
			$aryMailData["strmailaddress"]     = $aryResult[0]["strmailaddress"];			// 承認者メールアドレス
			$aryMailData["strWorkflowName"]    = $strworkflowname;							// 案件名
			//$aryMailData["strUserDisplayName"] = $aryResult[0]["struserdisplayname"];		// 承認依頼者
			$aryMailData["strUserDisplayName"] = $objAuth->UserDisplayName;					// 入力者（申請者）表示名
			$aryMailData["strURL"]             = LOGIN_URL;									// URL

			// 確認画面上のメッセージをメール内の備考欄として送信
			$aryMailData["strNote"] = $aryNewData["strWorkflowMessage"];


			// メールメッセージ取得
			list( $strSubject, $strTemplate ) = fncGetMailMessage( 807, $aryMailData, $objDB );

			// 管理者メールアドレス取得
			$strAdminMailAddress = fncGetCommonFunction( "adminmailaddress", "m_adminfunction", $objDB );

			// メール送信
			fncSendMail( $aryMailData["strmailaddress"], $strSubject, $strTemplate, "From: $strInputUserMailAddress\nReturn-Path: " . $strAdminMailAddress . "\n" );
		}

		// 帳票出力表示切替
		$aryData["PreviewVisible"] = "hidden";
		//---------------------------------------------------------------
	}




	// トランザクション完了
	$objDB->transactionCommit();

	$aryData["strBodyOnload"] = "";

	//作成日時
	$aryData["dtNowDate"] = date('Y/m/d', time() );
	$aryData["lngProductNumber"] = $sequence_m_product;

	// 製品コード
	$aryData["strProductCode"] = $sequence_code;


	// 成功時戻り先のアドレス指定
	$aryData["strAction"] = "/p/regist/index.php?strSessionID=";

	$objDB->close();


	// 帳票出力対応
	// 権限を持ってない場合もプレビューボタンを表示しない
	if( fncCheckAuthority( DEF_FUNCTION_LO1, $objAuth ) && $lngProductStatusCode != DEF_PRODUCT_APPLICATE )
	{
		$aryData["strPreviewAction"] = "../../list/result/frameset.php?strSessionID=" . $aryData["strSessionID"] . "&lngReportClassCode=" . DEF_REPORT_PRODUCT . "&strReportKeyCode=" . $sequence_m_product . "&bytCopyFlag=TRUE";

		$aryData["listview"] = 'visible';
	}
	else
	{
		$aryData["listview"] = 'hidden';
	}

	// テンプレート読み込み
	$objTemplate = new clsTemplate();
	$objTemplate->getTemplate( "p/finish/parts.tmpl" );
	header("Content-type: text/plain; charset=EUC-JP");


	// テンプレート生成
	$objTemplate->replace( $aryData );
	$objTemplate->complete();

	// HTML出力
	echo $objTemplate->strTemplate;
	return true;

?>