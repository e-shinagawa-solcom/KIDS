<?php

// ----------------------------------------------------------------------------
/**
*       商品管理  登録画面
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
*         ・初期登録画面を表示
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
	require( SRC_ROOT . "po/cmn/lib_po.php" );
	require( "libsql.php" );


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

	$aryData["strSessionID"]    = $_REQUEST["strSessionID"];   // セッションID
	$aryData["lngLanguageCode"] = $_COOKIE["lngLanguageCode"]; // 言語コード


//var_dump($aryData);exit;

	//-------------------------------------------------------------------------
	// ■ 入力文字列値・セッション・権限チェック
	//-------------------------------------------------------------------------
	// 文字列チェック(session)
	$aryCheck["strSessionID"] = "null:numenglish(32,32)";
	$aryResult = fncAllCheck( $aryData, $aryCheck );
	fncPutStringCheckError( $aryResult, $objDB );

	// セッション確認
	$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

	$lngUserCode = $objAuth->UserCode;


	// 300 商品管理
	if ( !fncCheckAuthority( DEF_FUNCTION_P0, $objAuth ) )
	{
		fncOutputError( 9018, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
	}

	// 301 商品管理（商品登録）
	if ( !fncCheckAuthority( DEF_FUNCTION_P1, $objAuth ) )
	{
		fncOutputError( 9018, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
	}


	//-------------------------------------------------------------------------
	// ■ 入力チェック
	//-------------------------------------------------------------------------
	if( strcmp ( $aryData["strProcess"], "" ) != 0)
	{

		if($aryData["strProcess"] == "check" )
		{

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
			if( strcmp($aryCheck["lngCustomerUserCode"], "") == 0 )
			{
				$aryCheck["strCustomerUserName"]	= "length(1,50)";				// 14:顧客担当者50byte
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
			$aryCheck["curProductPrice"]			= "null:money(0.0001,99999999999999.9999)";		// 27:卸値
			$aryCheck["curRetailPrice"]				= "null:money(0.0000,99999999999999.9999)";	// 28:売値     :length(1,8)
// 2004.06.17 suzukaze update start
//			$aryCheck["lngRoyalty"]					= "null:number(0,999999)";			// 30:ロイヤルティー 
/*必須外す		$aryCheck["lngRoyalty"]					= "null:number(0,100)";				// 30:ロイヤルティー*/
 			$aryCheck["lngRoyalty"]					= "number(0,100)";
/*			if(strcmp($aryCheck["lngRoyalty"], "") == 0)
			{
				$aryCheck["lngRoyalty"]					= "0.00";
			}
*/
// 2004.06.17 suzukaze update start
			$aryCheck["lngCertificateClassCode"]	= "null";		 					// 31:証紙

/*必須外す		if( $aryData["lngCopyrightCode"] == 0 && strcmp( $aryData["strCopyrightNote"], "") == 0 )
			{
				$aryCheck["lngCopyrightCode"]		= "number(1,99)"; 					// 32:版権元
				$aryCheck["strCopyrightNote"]		= "null";						// :版権元備考
			}
*/
			$aryCheck["lngCopyrightCode"]		= "length(1,50)"; 					// 32:版権元
			$aryCheck["strCopyrightNote"]		= "length(1,200)";						// :版権元備考
			$aryCheck["strCopyrightDisplayPrint"]	= "length(1,100)"; 						// 34:版権表示(印刷物)100
			$aryCheck["lngProductFormCode"]			= "number(1,99,The list has not been selected.):length(1,100)"; 	// 35:商品形態100

			if($_COOKIE["lngLanguageCode"])
			{
				$aryCheck["lngProductFormCode"]			= "number(1,99,リストが選択されていません。):length(1,100)"; 	// 35:商品形態100
			}
			$aryCheck["strProductComposition"]		= "null:number(0,99)";  						// 36:製品構成100byte
			$aryCheck["strAssemblyContents"]		= "length(1,100)";  							// 37:アッセンブリ内容100byte
			$aryCheck["strSpecificationDetails"]	= "length(1,10000)";  							// 38:仕様詳細300


			// エラー関数の呼び出し 
			$aryCheckResult = fncAllCheck( $aryData, $aryCheck );

			list ( $aryData, $bytErrorFlag ) = getArrayErrorVisibility( $aryData, $aryCheckResult, $objDB );


			$errorCount = ($bytErrorFlag == "TRUE" ) ? 1 : 0;


			// エラーが無かったら・・
			if( $errorCount == 0 )
			{
				// 製品名（英語）の類似検索開始 ================================================================================

				$lngInchargeGroupCode = fncGetMasterValue("m_group", "strgroupdisplaycode", "lnggroupcode", $aryData["lngInChargeGroupCode"] . ":str",'',$objDB);

				$aryQueryName[] = "SELECT " ;
				$aryQueryName[] = "strproductcode, ";
				$aryQueryName[] = "strproductenglishname ";
				$aryQueryName[] = "FROM ";
				$aryQueryName[] = "m_product ";
				$aryQueryName[] = "WHERE "; 
				$aryQueryName[] = "strproductenglishname = '".$aryData["strProductEnglishName"]."' AND ";
				$aryQueryName[] = "lnginchargegroupcode = $lngInchargeGroupCode AND ";
				$aryQueryName[] = "bytinvalidflag = false ";
				$aryQueryName[] = "ORDER BY strproductcode";


				$strQueryName = implode("\n", $aryQueryName );
				// echo "$strQueryName<br>";

				if ( !$lngResultID = $objDB->execute( $strQueryName ) )
				{
					fncOutputError ( 9051, DEF_ERROR, "", TRUE, "", $objDB );
					$objDB->close();
					return true;
				}

				$lngResultNum = pg_num_rows( $lngResultID );

				if( $lngResultNum != 0 )
				{
					$aryData["strProductEnglishName_Error"] = "visibility:visible;width:16;";
					$aryData["strProductEnglishName_Error_Message"] = fncOutputError ( 305, "", "", FALSE, "", $objDB );
					$errorCount++;
				}
			}

/*
			// これは良くわからない？？修正を重ねるなかで省かれたみたいです。
			if( $errorCount == 0 )
			{

				// 確認画面表示
				$aryData["strBodyOnload"] = "";

			}
*/

		}

		// プルダウンメニューの生成 値が入っている場合 
		// カテゴリー
		$aryData["lngCategoryCode"]				= fncGetPulldownQueryExec( fncSqlqueryCategory(array(0=>$lngUserCode)), $aryData["lngCategoryCode"], $objDB);
		// 荷姿単位
		$aryData["lngPackingUnitCode"]			= fncGetPulldown( "m_productunit", "lngProductUnitCode", "strProductUnitName", $aryData["lngPackingUnitCode"], "WHERE bytpackingconversionflag=true", $objDB);
		// 製品単位
		$aryData["lngProductUnitCode"]			= fncGetPulldown( "m_productunit", "lngProductUnitCode", "strProductUnitName", $aryData["lngProductUnitCode"], "WHERE bytproductconversionflag=true", $objDB);
		// 生産予定数の単位
		$aryData["lngProductionUnitCode"]		= fncGetPulldown( "m_productunit", "lngProductUnitCode", "strProductUnitName", $aryData["lngProductionUnitCode"], '', $objDB);
		// 初回納品数の単位
		$aryData["lngFirstDeliveryUnitCode"]		= fncGetPulldown( "m_productunit", "lngProductUnitCode", "strProductUnitName", $aryData["lngFirstDeliveryUnitCode"], '', $objDB);
		// 対象年齢
		$aryData["lngTargetAgeCode"]			= fncGetPulldown( "m_targetage", "lngTargetAgeCode", "strTargetAgeName", $aryData["lngTargetAgeCode"], '', $objDB);
		// 証紙 テーブルなし
		$aryData["lngCertificateClassCode"]		= fncGetPulldown( "m_CertificateClass", "lngcertificateclasscode", "strcertificateclassname", $aryData["lngCertificateClassCode"], '', $objDB);
		// 版権元
		$aryData["lngCopyrightCode"]			= fncGetPulldown( "m_copyright", "lngcopyrightcode", "strcopyrightname", $aryData["lngCopyrightCode"], '', $objDB);
		// 商品形態 テーブルなし
		$aryData["lngProductFormCode"]			= fncGetPulldown( "m_productform","lngproductformcode","strproductformname", $aryData["lngProductFormCode"], '', $objDB);
		// 企画進行状況
//		$aryData["lngGoodsPlanProgressCode"]	= fncGetPulldown( "m_goodsplanprogress","lnggoodsplanprogresscode", "strgoodsplanprogressname",1,'', $objDB);
		$aryData["lngGoodsPlanProgressCode"]	= fncGetPulldown( "m_goodsplanprogress","lnggoodsplanprogresscode", "strgoodsplanprogressname", $aryData["lngGoodsPlanProgressCode"], '', $objDB);
//プレゼンテーションを登録できるように修正　050328　by高　
		// アッセンブル内容
		if( strcmp( $aryData["lngAssemblyFactoryCode"],"" ) != 0 )
		{
			addslashes( $aryData["lngAssemblyFactoryCode"] );
		}


		// 仕様詳細の特殊文字変換
		//$aryData["strSpecificationDetails"] = fncHTMLSpecialChars( $aryData["strSpecificationDetails"] );
		//============================================================================================
		// 仕様詳細HIDDEN用（HIDDENに埋め込むために余分なタグなどを取り除く）
		if( strcmp( $aryData["strSpecificationDetails"], "") != 0 )
		{
			$aryData["strSpecificationDetails"] = stripslashes($aryData["strSpecificationDetails"]);
				$aryData["strSpecificationDetails"] = htmlspecialchars($aryData["strSpecificationDetails"], ENT_COMPAT | ENT_HTML401, "ISO-8859-1");
		}



		/**
			戻り時、仕様詳細画像ファイルHIDDEN生成
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



		//=============================================================================================
		if( $errorCount == 0 )
		{
			// 権限グループコードの取得
			$lngAuthorityGroupCode = fncGetUserAuthorityGroupCode( $lngUserCode, $aryData["strSessionID"], $objDB );

			// 承認ルートの生成
			// 「マネージャー」以上の場合
			if( $lngAuthorityGroupCode <= DEF_DIRECT_REGIST_AUTHORITY_CODE )
			{
				$aryData["lngWorkflowOrderCode"] = '<option value="0">承認なし</option>';
			}
			else
			{
				$aryData["lngWorkflowOrderCode"] = fncWorkFlow( $lngUserCode , $objDB , $aryData["lngWorkflowOrderCode"] );
			}



			$objDB->close();


			// submit関数
			define( "DEF_EN_MARK", "\\" );
			$aryData["strMonetaryrate"] = DEF_EN_MARK; //通貨マーク


			$aryData["strBodyOnload"] = "";
			$aryData["lngRegistConfirm"] = 1;
			$aryData["strurl"] = "/p/confirm/index.php?strSessionID=".$aryData["strSessionID"];
			$aryData["strActionURL"] = "index.php";

			// テンプレート読み込み
			$objDB->freeResult( $lngResultID );
			echo fncGetReplacedHtml( "/p/regist/parts.tmpl", $aryData, $objAuth );
			return true;
		}
		// 入力エラー
		else
		{
			// 権限グループコードの取得
			$lngAuthorityGroupCode = fncGetUserAuthorityGroupCode( $lngUserCode, $aryData["strSessionID"], $objDB );

			// 承認ルートの生成
			// 「マネージャー」以上の場合
			if( $lngAuthorityGroupCode <= DEF_DIRECT_REGIST_AUTHORITY_CODE )
			{
				$aryData["lngWorkflowOrderCode"] = '<option value="0">承認なし</option>';
			}
			else
			{
				$aryData["lngWorkflowOrderCode"] = fncWorkFlow( $lngUserCode , $objDB , $aryData["lngWorkflowOrderCode"] );
			}


			// submit関数
			$aryData["lngRegistConfirm"] = 0;
			$aryData["strActionURL"] = "index.php";

			$objDB->close();
			$objDB->freeResult( $lngResultID );
			echo fncGetReplacedHtml( "/p/regist/parts.tmpl", $aryData,$objAuth );
			return true;
		}

	}

	// 最初に表示される画面
	// プルダウンメニューの生成 値が入っている場合 
	
	// カテゴリー
	$aryData["lngCategoryCode"]				= fncGetPulldownQueryExec( fncSqlqueryCategory(array(0=>$lngUserCode)), $aryData["lngCategoryCode"], $objDB);
	// 荷姿単位
	$aryData["lngPackingUnitCode"]			= fncGetPulldown( "m_productunit", "lngProductUnitCode", "strProductUnitName", 1, "WHERE bytpackingconversionflag=true", $objDB);
	// 製品単位 
	$aryData["lngProductUnitCode"]			= fncGetPulldown( "m_productunit", "lngProductUnitCode", "strProductUnitName", 1, "WHERE bytproductconversionflag=true", $objDB);
	// 生産予定数の単位
	$aryData["lngProductionUnitCode"]		= fncGetPulldown( "m_productunit", "lngProductUnitCode", "strProductUnitName", 1, '', $objDB);
	// 初回納品数の単位
	$aryData["lngFirstDeliveryUnitCode"]		= fncGetPulldown( "m_productunit", "lngProductUnitCode", "strProductUnitName", 1, '', $objDB);
	// 対象年齢
	$aryData["lngTargetAgeCode"]			= fncGetPulldown( "m_targetage", "lngTargetAgeCode", "strTargetAgeName", 0, '', $objDB);
	// 証紙　テーブルなし
	$aryData["lngCertificateClassCode"]		= fncGetPulldown( "m_CertificateClass", "lngcertificateclasscode", "strcertificateclassname", 0, '', $objDB);
	// 版権元
	$aryData["lngCopyrightCode"]			= fncGetPulldown( "m_copyright", "lngcopyrightcode", "strcopyrightname", 0, '', $objDB);
	// 商品形態　テーブルなし
	$aryData["lngProductFormCode"]			= fncGetPulldown( "m_productform", "lngproductformcode", "strproductformname", 0, '', $objDB);

	// 企画進行状況
	$aryData["lngGoodsPlanProgressCode"]	= fncGetPulldown( "m_goodsplanprogress", "lnggoodsplanprogresscode", "strgoodsplanprogressname", 1, '', $objDB );
	$aryData["strProcess"] = "check";

	// submit関数
	$aryData["lngRegistConfirm"] = 0;



	// 権限グループコードの取得
	$lngAuthorityGroupCode = fncGetUserAuthorityGroupCode( $lngUserCode, $aryData["strSessionID"], $objDB );

	// 承認ルートの生成
	// 「マネージャー」以上の場合
	if( $lngAuthorityGroupCode <= DEF_DIRECT_REGIST_AUTHORITY_CODE )
	{
		$aryData["lngWorkflowOrderCode"] = '<option value="0">承認なし</option>';
	}
	else
	{
		$aryData["lngWorkflowOrderCode"] = fncWorkFlow( $lngUserCode , $objDB ,"" );
	}



	// フォームURL
	if(strcmp($aryData["strurl"], "") == 0)
	{
		$aryData["strurl"] = "/p/confirm/index.php?strSessionID=".$aryData["strSessionID"];
	}
	$aryData["strActionURL"] = "index.php";



/**
	仕様詳細定型文字
*/
$aryData["strSpecificationDetails"]	= "設計色 : <br />可動性 : <br />";



	$objDB->close();
	$objDB->freeResult( $lngResultID );

	// ヘルプ対応
	$aryData["lngFunctionCode"] = DEF_FUNCTION_P1;
	echo fncGetReplacedHtml( "p/regist/parts.tmpl", $aryData ,$objAuth);

	return true;


?>