<?php

// ----------------------------------------------------------------------------
/**
*       受注管理  登録
*
*
*       @package    K.I.D.S.
*       @license    http://www.kuwagata.co.jp/
*       @copyright  Kuwagata
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
	include( 'conf.inc' );                     // 設定ファイル
	require( LIB_FILE );                       // クラスライブラリファイル
	require( SRC_ROOT . "po/cmn/lib_po.php" ); // 発注管理関数ファイル
	require( SRC_ROOT . "so/cmn/lib_so.php" ); // 受注管理関数ファイル
	require( LIB_DEBUGFILE );

	//var_dump($_POST);
	//-------------------------------------------------------------------------
	// ■ オブジェクト生成
	//-------------------------------------------------------------------------
	$objDB   = new clsDB();   // DBオブジェクト
	$objAuth = new clsAuth(); // 認証処理オブジェクト


	//-------------------------------------------------------------------------
	// ■ パラメータ取得
	//-------------------------------------------------------------------------
	$aryData["strSessionID"]         = $_REQUEST["strSessionID"];          // セッションID
	$aryData["lngLanguageCode"]      = $_COOKIE["lngLanguageCode"];        // 言語コード


	//-------------------------------------------------------------------------
	// ■ DBオープン
	//-------------------------------------------------------------------------
	$objDB->open( "", "", "", "" );


	//-------------------------------------------------------------------------
	// ■ 入力文字列値・セッション・権限チェック
	//-------------------------------------------------------------------------
	// 入力文字列
	$aryCheck["strSessionID"] = "null:numenglish( 32,32 )";
	$aryResult = fncAllCheck( $aryData, $aryCheck );
	fncPutStringCheckError( $aryResult, $objDB );

	// セッション
	$objAuth     = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );
	$lngUserCode = $objAuth->UserCode;


	// 400 発注管理
	if( !fncCheckAuthority( DEF_FUNCTION_SO0, $objAuth ) )
	{
		fncOutputError( 9052, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
	}

	// 401 発注管理（受注登録）
	if( !fncCheckAuthority( DEF_FUNCTION_SO1, $objAuth ) )
	{
		fncOutputError( 9052, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
	}





	// 明細行を除いた値を取得
	for( $i = 0; $i < count( $_POST ); $i++ )
	{
		list( $strKeys, $strValues ) = each( $_POST );

		if( $strKeys != "aryPoDitail")
		{
			$aryData[$strKeys] = $strValues;
		}
	}



	// displayCodeをcodeに変換する
	// fncChangeDataで新しい配列を作る
	$aryNewData = fncChangeData( $aryData, $objDB );


	// 明細行の値を取得
	for( $i=0; $i<count($_POST[aryPoDitail]); $i++ )
	{
		while( list( $strKeys, $strValues ) = each( $_POST[aryPoDitail][$i] ) )
		{
			$aryNewData["aryPoDitail"][$i][$strKeys] = ( $strValues == "" ) ? "null" : $strValues ;
		}
	}



	// 処理モードの取得
	$strProcMode = $aryNewData["strProcMode"]; // 処理モード


	// ワークフローメッセージが空欄の場合
	$aryNewData["strWorkflowMessage"] = ( $aryNewData["strWorkflowMessage"] == "null" ) ? "" : $aryNewData["strWorkflowMessage"];

//fncDebug( 'lib_so.txt', $aryNewData, __FILE__, __LINE__);

	//-------------------------------------------------------------------------
	// ■ DB -> SELECT : m_productprice
	//-------------------------------------------------------------------------
	// 同じ「仕入科目」「仕入部品」の場合に同じ単価があるか
	// m_productPriceに同じ値があるか？在った場合は配列に行番号を記憶
	for( $i = 0; $i < count( $aryNewData["aryPoDitail"] ); $i++ )
	{
		$lngmonetaryunitcode = "";

		$aryNewData["lngMonetaryUnitCode"] = ( $aryNewData["lngMonetaryUnitCode"] == "\\" ) ? "\\\\" : $aryNewData["lngMonetaryUnitCode"];

		$lngmonetaryunitcode = fncGetMasterValue("m_monetaryunit", "strmonetaryunitsign", "lngmonetaryunitcode", $aryNewData["lngMonetaryUnitCode"] . ":str", '', $objDB );
		$strProductCode      = "";
		$strProductCode      = fncGetMasterValue( "m_product", "strproductcode", "lngproductno", $aryNewData["aryPoDitail"][$i]["strProductCode"]. ":str", '', $objDB );

		$arySelect   = array();
		$arySelect[] = "SELECT ";
		$arySelect[] = "lngproductpricecode ";
		$arySelect[] = "FROM ";
		$arySelect[] = "m_productprice ";
		$arySelect[] = "WHERE ";
		$arySelect[] = "lngproductno = $strProductCode AND ";
		$arySelect[] = "lngsalesclasscode = ".$aryNewData["aryPoDitail"][$i]["lngSalesClassCode"]." AND ";
		$arySelect[] = "lngmonetaryunitcode = $lngmonetaryunitcode AND ";
		$arySelect[] = "curproductprice = '".$aryNewData["aryPoDitail"][$i]["curProductPrice"]."'";

		$strSelect = implode("\n", $arySelect );


		// 結果IDを解放
		$objDB->freeResult( $lngResultID );


		// クエリ実行
		$lngResultID = $objDB->execute( $strSelect );


		// クエリ実行成功の場合
		if( $lngResultID )
		{
			// 同一製品価格が見つからない場合、もしくは単位計上が製品単位計上の場合のみ行番号を記憶する
			if( pg_num_rows( $lngResultID ) == 0 and $aryNewData["aryPoDitail"][$i]["lngConversionClassCode"] == "gs" )
			{
				$aryM_ProductPrice[] = $i; //行番号を記憶
			}
		}
	}
	//---------------------------------------------------------------



	// 通貨をコードに変換
	$aryNewData["lngMonetaryUnitCode"] = ( $aryNewData["lngMonetaryUnitCode"] == "\\" ) ? "\\\\" : $aryNewData["lngMonetaryUnitCode"];

	$lngMonetaryUnitCode = fncGetMasterValue("m_monetaryunit", "strmonetaryunitsign", "lngmonetaryunitcode", $aryNewData["lngMonetaryUnitCode"] . ":str", '', $objDB );

	// 入力者コードを取得
	$lngUserCode = $objAuth->UserCode;

	// 備考を取得
	$strDetailNote = ( $aryNewData["strNote"] == "null" ) ? "null" : "'".$aryNewData["strNote"]."'";



	//-------------------------------------------------------------------------
	// ■ m_Receive のシーケンス番号を取得
	//-------------------------------------------------------------------------
	$lngReceiveNo = fncGetSequence( 'm_Receive.lngReceiveNo', $objDB );

	//-------------------------------------------------------------------------
	// ■ トランザクション開始
	//-------------------------------------------------------------------------
	$objDB->transactionBegin();





	//-------------------------------------------------------------------------
	// ■ DB -> INSERT : m_Receive
	//-------------------------------------------------------------------------
	// 受注番号の取得
	$strReceiveCode = $aryNewData["strReceiveCode"];


	//---------------------------------------------------------------
	// 処理モードが「登録」の場合
	//---------------------------------------------------------------
	if( $strProcMode == "regist" )
	{
		// リビジョン番号を初期化
		$lngRevisionNo = 0;

		// リバイズ番号の初期化
		$strReviseCode  = "00";

		// 受注番号の取得
		$strReceiveCode = fncGetDateSequence( date('Y', strtotime( $aryNewData["dtmOrderAppDate"] ) ), date('m',strtotime( $aryNewData["dtmOrderAppDate"] ) ), "m_receive.strreceivecode", $objDB );

		// d 文字付加
		$strReceiveCode = "d" . $strReceiveCode;
		$aryNewData["strReceiveCode"] = $strReceiveCode;

		// 受注状態コードの取得
		$lngReceiveStatusCode = ( $aryNewData["lngWorkflowOrderCode"] == 0 ) ? 2 : 1;
	}
	//---------------------------------------------------------------
	// 処理モードが「修正」の場合
	//---------------------------------------------------------------
	else
	{
		//-------------------------------------------------
		// 最新リバイズデータが「申請中」になっていないかどうか確認
		//-------------------------------------------------
		$strCheckQuery = "SELECT lngReceiveNo, lngReceiveStatusCode FROM m_Receive r WHERE r.strReceiveCode = '" . $aryNewData["strReceiveCode"] . "'";
		$strCheckQuery .= " AND r.bytInvalidFlag = FALSE ";
		$strCheckQuery .= " AND r.lngRevisionNo = ( "
			. "SELECT MAX( r1.lngRevisionNo ) FROM m_Receive r1 WHERE r1.strReceiveCode = r.strReceiveCode )\n";
		$strCheckQuery .= " AND r.strReviseCode = ( "
			. "SELECT MAX( r2.strReviseCode ) FROM m_Receive r2 WHERE r2.strReceiveCode = r.strReceiveCode )\n";

		// チェッククエリーの実行
		list( $lngCheckResultID, $lngCheckResultNum ) = fncQuery( $strCheckQuery, $objDB );

		if ( $lngCheckResultNum == 1 )
		{
			$objResult            = $objDB->fetchObject( $lngCheckResultID, 0 );
			$lngReceiveStatusCode = $objResult->lngreceivestatuscode;

			if ( $lngReceiveStatusCode == DEF_ORDER_APPLICATE )
			{
				fncOutputError( 409, DEF_WARNING, "", TRUE, "../so/regist/index.php?strSessionID=" . $aryData["strSessionID"], $objDB );
			}
		}

		// 結果IDを解放
		$objDB->freeResult( $lngCheckResultID );



		//-------------------------------------------------------------------------
		// 再承認のため、ステータスを「申請中」に変更
		//-------------------------------------------------------------------------
		if( $aryNewData["lngWorkflowOrderCode"] == 0 )
		{
			$lngReceiveStatusCode = DEF_RECEIVE_ORDER;

			// 売上チェック
			$arySql = array();
			$arySql[] = "select count(*) as count";
			$arySql[] = "from";
			$arySql[] = "	m_sales ms";
			$arySql[] = "		left join t_salesdetail tsd on tsd.lngsalesno = ms.lngsalesno";
			$arySql[] = "where";
			$arySql[] = "tsd.lngreceiveno in ";
			$arySql[] = "(";
			$arySql[] = "	select ms1.lngreceiveno";
			$arySql[] = "	from";
			$arySql[] = "		m_receive ms1";
			$arySql[] = "	where";
			$arySql[] = "		ms1.strreceivecode = '" . $aryNewData["strReceiveCode"] . "'";
			$arySql[] = ")";
			$arySql[] = "and ms.bytinvalidflag = false";
			$arySql[] = "AND ms.lngRevisionNo = (";
			$arySql[] = "	SELECT MAX( s1.lngRevisionNo ) FROM m_Sales s1 WHERE s1.bytInvalidFlag = false and s1.strSalesCode = ms.strSalesCode)";
			$arySql[] = "	AND 0 <= (";
			$arySql[] = "		SELECT MIN( s2.lngRevisionNo ) FROM m_Sales s2 WHERE s2.bytInvalidFlag = false and s2.strSalesCode = ms.strSalesCode )";

			$strQuery = implode("\n", $arySql);
			// ＤＢ問い合わせ
			list( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

			if ( $lngResultNum == 1 )
			{
				$objResult	= $objDB->fetchObject( $lngResultID, 0 );
				// 一個以上、売上データがあれば、「納品中」とする
				if( 1 <= (int)$objResult->count)
				{
					$lngReceiveStatusCode = DEF_RECEIVE_DELIVER;
				}
			}

		}
		// 申請中
		else
		{
			$lngReceiveStatusCode = DEF_RECEIVE_APPLICATE;
		}


		//-------------------------------------------------------------------------
		// 状態コードが「 null / "" 」の場合、「0」を再設定
		//-------------------------------------------------------------------------
		$lngReceiveStatusCode = fncCheckNullStatus( $lngReceiveStatusCode );

		//-------------------------------------------------------------------------
		// 状態コードが「0」の場合、「1」を再設定
		//-------------------------------------------------------------------------
		$lngReceiveStatusCode = fncCheckZeroStatus( $lngReceiveStatusCode );


		// 同じ受注Noを使用する
//		$lngReceiveNo = $aryData['lngReceiveNo'];


		// 修正の場合同じ仕入に対してリビジョン番号の最大値を取得する
		// リビジョン番号を現在の最大値をとるように修正する
		// その際にSELECT FOR UPDATEを使用して、同じ受注に対してロック状態にする（MAX集合関数を用いると、FOR UPDATEが効かない）
		$strLockQuery = "SELECT lngRevisionNo FROM m_Receive WHERE strReceiveCode = '" . $strReceiveCode . "' FOR UPDATE";

		// ロッククエリー実行
		list( $lngLockResultID, $lngLockResultNum ) = fncQuery( $strLockQuery, $objDB );

		// 最大リビジョン番号の取得
		$lngMaxRevision = 0;
		for( $i = 0; $i < $lngLockResultNum; $i++ )
		{
			$objResult = $objDB->fetchObject( $lngLockResultID, $i );
			if ( $lngMaxRevision < $objResult->lngrevisionno )
			{
				$lngMaxRevision = $objResult->lngrevisionno;
			}
		}

		// 結果IDを解放
		$objDB->freeResult( $lngLockResultID );

		// リビジョン番号をインクリメント
		$lngRevisionNo = $lngMaxRevision + 1;

		// リバイズ番号をインクリメント
		$strReviseCode = sprintf( "%02d", $lngMaxRevision + 1 );

	}



	//---------------------------------------------------------------
	// ■ 仮受注チェック
	//---------------------------------------------------------------
	// 顧客受注番号のチェック
	$aryNewData["strCustomerReceiveCode"] = trim( $aryNewData["strCustomerReceiveCode"] );

	// 顧客受注番号が空欄では無い場合
	if( $aryNewData["strCustomerReceiveCode"] != "null" )
	{
		// 受注状態を取得 ( 取得した顧客受注番号が「10:仮受注」の場合は、「1:申請中」へ変換 )
		$lngReceiveStatusCode = ( $lngReceiveStatusCode == 10 ) ? 1 : $lngReceiveStatusCode;

		// 顧客受注番号を取得
		$strCustomerReceiveCode = $aryNewData["strCustomerReceiveCode"];
	}
	// 空欄の場合、仮受注
	else
	{
		// 受注状態を取得
		$lngReceiveStatusCode = 10;

		// 顧客受注番号を取得
		$strCustomerReceiveCode = "";
	}



	// リバイズ番号の取得
	$strReviseCode = ( $aryNewData["strReviseCode"] == "null" ) ? "00" : $strReviseCode;

	// 顧客をコード変換
	$aryNewData["lngCustomerCode"] = fncGetMasterValue( "m_company", "strcompanydisplaycode", "lngcompanycode", $aryNewData["lngCustomerCode"] . ":str", '', $objDB );




	$aryQuery   = array();
	$aryQuery[] = "INSERT INTO m_receive( ";
	$aryQuery[] = "lngreceiveno, ";												// 1:受注番号
	$aryQuery[] = "lngrevisionno, ";											// 2:リビジョン番号
	$aryQuery[] = "strreceivecode, ";											// 3:受注コード
	$aryQuery[] = "strrevisecode, ";											// 4:リバイズコード 
	$aryQuery[] = "dtmappropriationdate, ";										// 5:計上日
	$aryQuery[] = "lngcustomercompanycode, ";									// 6:会社コード 
	//$aryQuery[] = "lnggroupcode, ";												// 7:グループコード
	//$aryQuery[] = "lngusercode, ";												// 8:ユーザーコード
	$aryQuery[] = "lngreceivestatuscode, ";										// 9:受注状態コード
	$aryQuery[] = "lngmonetaryunitcode, ";										// 10:通貨単位コード
	$aryQuery[] = "lngmonetaryratecode, ";										// 11:通貨レートコード
	$aryQuery[] = "curconversionrate, ";										// 12:換算レート
	$aryQuery[] = "curtotalprice, ";											// 13:合計金額
	$aryQuery[] = "strnote, ";													// 14:備考
	$aryQuery[] = "lnginputusercode, ";											// 15:入力者コード 
	$aryQuery[] = "bytinvalidflag, ";											// 16:無効フラグ
	$aryQuery[] = "dtminsertdate, ";											// 17:登録日
	$aryQuery[] = "strcustomerreceivecode ";									// 顧客受注番号
	$aryQuery[] = " ) values ( ";
	$aryQuery[] = "$lngReceiveNo, ";											// 1:受注番号
	$aryQuery[] = "$lngRevisionNo, ";											// 2:リビジョン番号
	$aryQuery[] = "'".$strReceiveCode."', ";									// 3:受注コード
	$aryQuery[] = "'$strReviseCode', ";											// 4:リバイズコード 
	$aryQuery[] = "'". $aryNewData["dtmOrderAppDate"]."',";						// 5:計上日
	$aryQuery[] = $aryNewData["lngCustomerCode"].", ";							// 6:会社コード 
	//$aryQuery[] = $aryNewData["lngInChargeGroupCode"].", ";						// 7:グループコード
	//$aryQuery[] = $aryNewData["lngInChargeUserCode"].", ";						// 8:ユーザーコード
	$aryQuery[] = "$lngReceiveStatusCode, ";									// 9:受注状態コード
	$aryQuery[] = "$lngMonetaryUnitCode, ";										// 10:通貨単位コード
	$aryQuery[] = $aryNewData["lngMonetaryRateCode"].", ";						// 11:通貨レートコード
	$aryQuery[] = "'".$aryNewData["curConversionRate"]."', ";					// 12:換算レート
	$aryQuery[] = "'".$aryNewData["curAllTotalPrice"]."', ";					// 13:合計金額
	$aryQuery[] = "$strDetailNote, ";											// 14:備考
	$aryQuery[] = "$lngUserCode, ";												// 15:入力者コード 
	$aryQuery[] = "false, ";													// 16:無効フラグ
	$aryQuery[] = "now(), ";													// 17:登録日
	$aryQuery[] = "'" . $strCustomerReceiveCode . "' ";							// 顧客受注番号
	$aryQuery[] = " ) ";


	$strQuery = "";
	$strQuery = implode( "\n", $aryQuery );


	// 結果IDを解放
	$objDB->freeResult( $lngResultID );


	// クエリ実行
	$lngResultID = $objDB->execute( $strQuery );


	// クエリ実行失敗の場合
	if ( !$lngResultID )
	{
		echo "m_receive : ERROR<br>";
		//fncOutputError ( 9051, DEF_ERROR, "", TRUE, "", $objDB );
		$objDB->close();
		return true;
	}
	//-------------------------------------------------------------------------



	//-------------------------------------------------------------------------
	// ■ DB -> INSERT : t_receivedetail
	//-------------------------------------------------------------------------
	// 明細行番号が不明な行の対処
	$lngMaxDetailNo = 0;


	// 明細行の中で指定されている最大値を求める
	for ( $i = 0; $i < count( $aryNewData["aryPoDitail"] ); $i++ )
	{
		if ( $aryNewData["aryPoDitail"][$i]["lngOrderDetailNo"] != "null" 
			and $aryNewData["aryPoDitail"][$i]["lngOrderDetailNo"] != "" 
			and $aryNewData["aryPoDitail"][$i]["lngOrderDetailNo"] != "undefined" 
			and $aryNewData["aryPoDitail"][$i]["lngOrderDetailNo"] > $lngMaxDetailNo )
		{
			$lngMaxDetailNo = $aryNewData["aryPoDitail"][$i]["lngOrderDetailNo"];
		}
	}

	// 売上での明細行追加に対応するために重なりのない番号を使用する
	if( $strProcMode != "regist" )
	{
		$lngMaxDetailNo = $lngMaxDetailNo + 100;
	}


	for( $i = 0; $i < count( $aryNewData["aryPoDitail"] ); $i++ )
	{
		// 備考
		$strDetailNote = ( $aryNewData["aryPoDitail"][$i]["strDetailNote"] == "null" )  ? "null" : "'".$aryNewData["aryPoDitail"][$i]["strDetailNote"]."'";

		// 消費税コード
		if($aryNewData["aryPoDitail"][$i]["lngTaxCode"] != "null")
		{
			$lngTaxCode = $aryNewData["aryPoDitail"][$i]["lngTaxCode"];
		}
		else
		{
			$lngTaxCode = "null";
		}
		

		// 仕入明細番号
		// 明細行番号がない場合（仕入で追加された明細行の場合）
		if ( $aryNewData["aryPoDitail"][$i]["lngOrderDetailNo"] == "null" 
			or $aryNewData["aryPoDitail"][$i]["lngOrderDetailNo"] == "" 
			or $aryNewData["aryPoDitail"][$i]["lngOrderDetailNo"] == "undefined" )
		{
			$lngMaxDetailNo++;
			$aryNewData["aryPoDitail"][$i]["lngOrderDetailNo"] = $lngMaxDetailNo;
		}

		// ソート番号
		$lngSortKey = $i + 1;

		// 換算区分コード
		$lngConversionClassCode = ( $aryNewData["aryPoDitail"][$i]["lngConversionClassCode"] == "gs" ) ? 1 : 2 ;

		// 税額
		$curTaxPrice = ( $aryNewData["aryPoDitail"][$i]["curTaxPrice"] == "null" ) ? "null" : "'".$aryNewData["aryPoDitail"][$i]["curTaxPrice"]."'";


		$aryQuery   = array();
		$aryQuery[] = "INSERT INTO t_receivedetail ( ";
		$aryQuery[] = "lngreceiveno, ";													// 1:受注番号
		$aryQuery[] = "lngreceivedetailno, ";											// 2:受注明細番号
		$aryQuery[] = "lngrevisionno, ";												// 3:リビジョン番号
		$aryQuery[] = "strproductcode, ";												// 4:製品コード
		$aryQuery[] = "lngsalesclasscode, ";											// 5:売上区分コード
		$aryQuery[] = "dtmdeliverydate, ";												// 6:納品日
		$aryQuery[] = "lngconversionclasscode, ";										// 7:換算区分コード
		$aryQuery[] = "curproductprice, ";												// 8:製品価格
		$aryQuery[] = "lngproductquantity, ";											// 9:製品数量
		$aryQuery[] = "lngproductunitcode, ";											// 10:製品単位コード
		$aryQuery[] = "lngtaxclasscode, ";												// 11:消費税区分コード
		$aryQuery[] = "lngtaxcode, ";													// 12:消費税コード
		$aryQuery[] = "curtaxprice, ";													// 13:消費税金額
		$aryQuery[] = "cursubtotalprice, ";												// 14:小計金額
		$aryQuery[] = "strnote,";														// 15:備考
		$aryQuery[] = "lngSortKey";														// 16:表示用ソートキー
		$aryQuery[] = " ) values ( ";
		$aryQuery[] = "'$lngReceiveNo', ";												// 1:受注番号
		$aryQuery[] = $aryNewData["aryPoDitail"][$i]["lngOrderDetailNo"] . ", ";		// 2:受注明細番号
		$aryQuery[] = "$lngRevisionNo, ";												// 3:リビジョン番号
		$aryQuery[] = "'".$aryNewData["aryPoDitail"][$i]["strProductCode"]."', ";		// 4:製品コード
		$aryQuery[] = $aryNewData["aryPoDitail"][$i]["lngSalesClassCode"].", ";			// 5:売上区分コード
		$aryQuery[] = "'".$aryNewData["aryPoDitail"][$i]["dtmDeliveryDate"]."', ";		// 6:納品日
		$aryQuery[] = "$lngConversionClassCode, ";										// 7:換算区分コード 
		$aryQuery[] = "'".$aryNewData["aryPoDitail"][$i]["curProductPrice"]."', ";		// 8:製品価格
		$aryQuery[] = $aryNewData["aryPoDitail"][$i]["lngGoodsQuantity"].", ";			// 9:製品数量
		$aryQuery[] = $aryNewData["aryPoDitail"][$i]["lngProductUnitCode"].", ";		// 10:製品単位コード
		$aryQuery[] = "null, ";															// 11:消費税区分コードlngTaxClassCode
		$aryQuery[] = "null, ";															// 12:消費税コードlngTaxCode
		$aryQuery[] = "null, ";															// 13:消費税金額curTaxPrice
		$aryQuery[] = $aryNewData["aryPoDitail"][$i]["curTotalPrice"].", ";				// 14:小計金額curSubtotalPrice
		$aryQuery[] = $strDetailNote . ", ";											// 15:備考strNote
		$aryQuery[] = $lngSortKey . " ";												// 16:表示用ソートキー
		$aryQuery[] = ") ";
		
		$strQuery = "";
		$strQuery = implode( "\n", $aryQuery );


		// 結果IDを解放
		$objDB->freeResult( $lngResultID );


		// クエリ実行
		$lngResultID = $objDB->execute( $strQuery );


		// クエリ実行失敗の場合
		if ( !$lngResultID )
		{
			fncOutputError ( 9051, DEF_ERROR, "", TRUE, "", $objDB );
			$objDB->close();

			return true;
		}
	}
	//-------------------------------------------------------------------------



	//-------------------------------------------------------------------------
	// ■ DB -> INSERT : m_productprice
	//-------------------------------------------------------------------------
	if( is_array( $aryM_ProductPrice ) )
	{
		for( $i = 0; $i < count( $aryM_ProductPrice ); $i++ )
		{
			// m_orderのシーケンスを取得
			$sequence_m_productprice = fncGetSequence( 'm_ProductPrice.lngProductPriceCode', $objDB );

			list( $strKeys, $strValues ) = each( $aryM_ProductPrice );

			$aryQuery = array();
			$aryQuery[] = "INSERT INTO m_productprice (";
			$aryQuery[] = "lngproductpricecode, ";												// 1:製品価格コード
			$aryQuery[] = "lngproductno,";														// 2:製品番号
			$aryQuery[] = "lngsalesclasscode,";													// 3:売上区分コード
			$aryQuery[] = "lngmonetaryunitcode,";												// 4:通貨単位コード
			$aryQuery[] = "curproductprice ";													// 5:製品価格
			$aryQuery[] = ") VALUES (";
			$aryQuery[] = "$sequence_m_productprice, ";											// 1:製品価格コード
			$aryQuery[] = $aryNewData["aryPoDitail"][$strValues]["strProductCode"].",";			// 2:製品番号	
			$aryQuery[] = $aryNewData["aryPoDitail"][$strValues]["lngSalesClassCode"].",";		// 3:売上区分コード
			$aryQuery[] = "$lngmonetaryunitcode,";												// 4:通貨単位コード
			$aryQuery[] = $aryNewData["aryPoDitail"][$strValues]["curProductPrice"];			// 5:製品価格
			$aryQuery[] = ")";

			$strQuery = "";
			$strQuery = implode("\n", $aryQuery );


			// 結果IDを解放
			$objDB->freeResult( $lngResultID );


			// クエリ実行
			$lngResultID = $objDB->execute( $strQuery );


			// クエリ実行失敗の場合
			if ( !$lngResultID )
			{
				fncOutputError ( 9051, DEF_ERROR, "", TRUE, "", $objDB );
				$objDB->close();
				return true;
			}
		}
	}
	//-------------------------------------------------------------------------





	//-------------------------------------------------------------------------
	// ■ 承認処理
	//
	//   承認ルート
	//     ・0 : 承認ルートなし
	//-------------------------------------------------------------------------
	$lngWorkflowOrderCode = $aryNewData["lngWorkflowOrderCode"];	// 承認ルート

	//$strWFName   = "受注 [No:" . $strReceiveCode . "-" . $strReviseCode . "]";
	$strWFName   = "受注 [No:" . $aryNewData["strCustomerReceiveCode"] . "]";

	$lngSequence = $lngReceiveNo;
	$strDefFnc   = DEF_FUNCTION_SO1;

	$strProductCode       = $aryNewData["aryPoDitail"][0]["strProductCode"];
	$lngApplicantUserCode = fncGetMasterValue( "m_product", "strproductcode", "lnginchargeusercode", $strProductCode . ":str", '', $objDB );


	// 承認ルートが選択された場合
	if( $lngWorkflowOrderCode != 0 && $lngReceiveStatusCode != 10 )
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
		$aryQuery[] = "''";											// 11 : 備考
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
		$aryQuery[] = "'" . $aryNewData["strWorkflowMessage"] . "',";	// 11:備考
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

/*
fncDebug( 'lib_so.txt',  $aryResult[0]["bytmailtransmitflag"], __FILE__, __LINE__);
fncDebug( 'lib_so.txt',  $aryMailData["strmailaddress"], __FILE__, __LINE__);
fncDebug( 'lib_so.txt',  $strInputUserMailAddress, __FILE__, __LINE__);
*/

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



	//-------------------------------------------------------------------------
	// ■ 即承認の場合
	//-------------------------------------------------------------------------
	// プレビューボタンを表示する
	else
	{
		// 帳票出力対応
		// 権限を持ってない場合はプレビューボタンを表示しない
		if ( fncCheckAuthority( DEF_FUNCTION_LO2, $objAuth ) )
		{
			$aryData["strPreviewAction"] = "../../list/result/frameset.php?strSessionID=" . $aryData["strSessionID"] . "&lngReportClassCode=" . DEF_REPORT_ORDER . "&strReportKeyCode=" . $lngSequence . "&bytCopyFlag=TRUE";

			// 帳票出力表示切替
			$aryData["PreviewVisible"] = "visible";
		}
		else
		{
			// 帳票出力表示切替
			$aryData["PreviewVisible"] = "hidden";
		}
	}





	//-------------------------------------------------------------------------
	// ■ トランザクション完了
	//-------------------------------------------------------------------------
	$objDB->transactionCommit();





	//-------------------------------------------------------------------------
	// ■ 出力
	//-------------------------------------------------------------------------
	$aryData["strPreviewButton"] = "<br><a href=\"index.php?strSessionID=".$aryData["strSessionID"]."\">戻る</a>";

	// 顧客受注番号
	$aryData["lngCRC"]  = $strCustomerReceiveCode;

	// 受注番号
	$aryData["lngPONo"] = $aryNewData["strReceiveCode"] . " - $strReviseCode";

	// 成功時戻り先のアドレス指定
	$aryData["strAction"] = "/so/regist/index.php?strSessionID=";

	// テンプレート読み込み
	$objTemplate = new clsTemplate();
	$objTemplate->getTemplate( "so/finish/parts.tmpl" );
	header("Content-type: text/plain; charset=EUC-JP");

	// テンプレート生成
	$objTemplate->replace( $aryData );
	$objTemplate->complete();

	echo $objTemplate->strTemplate;



	return true;

?>




