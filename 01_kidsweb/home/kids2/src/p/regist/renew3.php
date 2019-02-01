<?php

// ----------------------------------------------------------------------------
/**
*       商品管理  修正登録
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
	require( SRC_ROOT."p/regist/conf.php" );
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

	$aryData["strSessionID"]    = $_REQUEST["strSessionID"];   // セッションID
	$aryData["lngLanguageCode"] = $_COOKIE["lngLanguageCode"]; // 言語コード



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
	$lngUserCode = $objAuth->UserCode;


	// 300 商品管理
	if( !fncCheckAuthority( DEF_FUNCTION_P0, $objAuth ) )
	{
		fncOutputError ( 9018, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
	}

	// 301 商品管理（商品登録）
	if( !fncCheckAuthority( DEF_FUNCTION_P1, $objAuth ) )
	{
		fncOutputError ( 9018, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
	}



	// 全て小文字に変換
	while ( list ($strKeys, $strValues ) = each ( $aryData ))
	{
		$aryData[strtolower($strKeys)] = $strValues;
	}




	// DBの型が数値型のみ update時の型指定で使用
	$aryNumber = array();
	$aryNumber[0] = "lngboxquantity";
	$aryNumber[1] = "lngcartonquantity";
	$aryNumber[2] = "lngproductionquantity";
	$aryNumber[3] = "dtmdeliverylimitDate";
	$aryNumber[4] = "curproductprice";
	$aryNumber[5] = "curretailprice";
	$aryNumber[6] = "lngfirstdeliveryquantity";

	// updateするデータを配列に格納 DBのカラム名に統一する
	$aryUpdate = split (",", $aryData["updatekey"]);

	$aryUpdateKeys =  array_values($aryUpdate);
	for( $i = 0; $i < count( $aryUpdate ); $i++ )
	{
		list ($strKey_Update, $strValue_Update) = each ( $aryUpdateKeys );

		// DBのカラムとWEB上のname属性が一致しないものはDBのカラム名に変更
		for($j = 0; $j < count( $gbl_ColName[DEF_ARRAY_COL_NAME][DEF_FORMNAME] ) ; $j++ )
		{
			if( strtolower( $gbl_ColName[DEF_ARRAY_COL_NAME][DEF_COLMNAME][$j] ) == $strValue_Update )
			{
				$aryNewUpdate[] = $gbl_ColName[DEF_ARRAY_COL_NAME][DEF_FORMNAME][$j];
				break;
			}
		}


		if( $j == count( $gbl_ColName[DEF_ARRAY_COL_NAME][DEF_FORMNAME] ) )
		{
			// t_goods_planeのupdateに使用する $aryUpdate_Goods
			if( DEF_GOODS_PLANE == $strValue_Update )
			{
				$aryUpdate_Goods = $strValue_Update;
			}
			// DBのカラムとWEB上のname属性が一致するもの
			else
			{
				$aryNewUpdate[] = $strValue_Update;
			}
		}
	}








	//-------------------------------------------------------------------------
	// ■ トランザクション開始
	//-------------------------------------------------------------------------
	$objDB->transactionBegin();



	//-------------------------------------------------
	// 最新データが「申請中」になっていないかどうか確認
	//-------------------------------------------------
	$strCheckQuery = "SELECT lngProductStatusCode FROM m_Product p WHERE p.strProductCode = '" . $aryData["strproductcode"] . "'";
	$strCheckQuery .= " AND p.bytInvalidFlag = FALSE\n";

	// チェッククエリーの実行
	list( $lngCheckResultID, $lngCheckResultNum ) = fncQuery( $strCheckQuery, $objDB );

	if( $lngCheckResultNum == 1 )
	{
		$objResult            = $objDB->fetchObject( $lngCheckResultID, 0 );
		$lngProductStatusCode = $objResult->lngproductstatuscode;

		if( $lngProductStatusCode == DEF_PRODUCT_APPLICATE )
		{
			fncOutputError( 307, DEF_WARNING, "", TRUE, "../p/regist/index.php?strSessionID=" . $aryData["strsessionid"], $objDB );
		}
	}

	// 結果IDを解放
	$objDB->freeResult( $lngCheckResultID );



	//-------------------------------------------------------------------------
	// 状態コードが「 null / "" 」の場合、「0」を再設定
	//-------------------------------------------------------------------------
	$lngProductStatusCode = fncCheckNullStatus( $lngProductStatusCode );


	//-------------------------------------------------------------------------
	// 状態コードが「0」の場合、「1」を再設定
	//-------------------------------------------------------------------------
	if( $aryData["lngworkflowordercode"] != 0 )
	{
		$lngProductStatusCode = fncCheckZeroStatus( $lngProductStatusCode );
	}



	if( !empty($aryNewUpdate))
	{
		$count = 0;
		for($i=0; $i < count( $aryNewUpdate ); $i++ )
		{
			list ($strkey, $strvalue) = each ( $aryNewUpdate );

			// タグ処理
			if( strtolower($strvalue) == strtolower("strSpecificationDetails") )
			{
				// 2008/11/18
				// イメージに \マークが付加される場合がある為
				// クォートされた文字列のクォート部分を取り除く
				$aryData["$strvalue"] = stripslashes( $aryData["$strvalue"] );
			}

			// 文字型
			if( ereg("^str", $strvalue) )
			{
				// 2004/03/08 watanabe特別処理：他いじると全部動かなくなりそうなので・・・
				if( $strvalue == "strcustomerusercode" )
				{
					if( $aryData["$strvalue"] == "")
					{
						$aryColumn[] = "lngCustomerUserCode = null,";
					}
					else
					{
						$aryColumn[] = "lngCustomerUserCode = ".$aryData["$strvalue"].", ";
					}
				}
				// watanabe update end
				else
				{
					$aryColumn[] = "$strvalue = '".$aryData["$strvalue"] ."', ";
				}
			}
			// 日付型
			elseif( ereg ("^dtm", $strvalue) )
			{
				$aryColumn[] = "$strvalue = To_timestamp('". $aryData["$strvalue"] ."', 'YYYY-MM-DD'),";
			}
			else
			{
				// 数値型
				for($j=0; $j < count( $aryNumber ); $j++ )
				{
					//echo $aryNumber[$j] . "/" .$strvalue  ."<br>";


					if( $aryNumber[$j] == $strvalue )
					{
// 2004.03.04 suzukaze update start
						// 数値項目が　NULL値　に修正された場合 NULLを設定するように変更
						if ( $aryData["$strvalue"] == "" or $aryData["$strvalue"] == " " or $aryData["$strvalue"] == "null" )
						{
							$aryColumn[] = $strvalue . " = null,";
						}
						else
						{
							$aryColumn[] = "$strvalue = to_number('" .$aryData["$strvalue"]."','9999999999.9999'),";
						}
						break;
					}
				}

				// その他
				if( $j == count( $aryNumber ) )
				{

					if(strcmp ($aryData["$strvalue"],"") != 0)
					{
						$aryColumn[] = "$strvalue = ".$aryData["$strvalue"] .", ";

					}
					else
					{
						// 2004/03/08 watanabe特別処理：他いじると全部動かなくなりそうなので・・・
						if( $strvalue == "lngcustomercompanycode")
						{
							if( $aryData["lngcompanycode"] == "")
							{
								$aryColumn[] = "lngcustomercompanycode = null,";
							}
							else
							{
								$aryColumn[] = "lngcustomercompanycode = ".$aryData["lngcompanycode"] .", ";
							}
						}
						// watanabe update end
						else
						{
							$aryColumn[] = $strvalue . " = null,";
						}
					}
				}
// 2004.03.04 suzukaze update end
			}

		}
		// 2004.03.04 suzukaze update start
		$aryColumn[] = "lnginputusercode = " . $lngInputUserCode . ", ";
		// 2004.03.04 suzukaze update start
		$aryColumn[] = "dtmupdatedate = now()";

		// テーブルロック
		$strQuery = "";
		$strQuery = "SELECT lngproductno FROM m_product WHERE strproductcode ='" .$aryData["strProductCode"] ."' FOR UPDATE";
		// echo "$strQuery<br>";

		$objDB->freeResult( $lngResultID );
		if ( !$lngResultID = $objDB->execute( $strQuery ) )
		{
			fncOutputError ( 9051, DEF_ERROR, "", FALSE, "", $objDB );
		}

		$strUpdate = "";
		$strUpdate  = "Update m_product SET ";
		$strUpdate .= implode("\n", $aryColumn);
		$strUpdate .= " WHERE strproductcode ='" .$aryData["strProductCode"] ."'";

//fncDebug("renew3.txt", $strUpdate, __FILE__, __LINE__);

		// echo "strUpdate : $strUpdate<br>";
		// exit();
		$objDB->freeResult( $lngResultID );
		if ( !$lngResultID = $objDB->execute( $strUpdate ) )
		{
			fncOutputError ( 9051, DEF_ERROR, "", FALSE, "", $objDB );
		}

	}









	//-------------------------------------------------------------------------
	// ■商品状態の更新
	//-------------------------------------------------------------------------
	$aryStatusQuery   = array();
	$aryStatusQuery[] = "UPDATE m_product ";
	$aryStatusQuery[] = "SET lngproductstatuscode = " . $lngProductStatusCode . " ";
	$aryStatusQuery[] = "WHERE lngproductno = " . $aryData["lngproductno"];

	$strStatusQuery = implode( "\n", $aryStatusQuery );

	$objDB->freeResult( $lngResultID );

	if( !$lngResultID = $objDB->execute( $strStatusQuery ) )
	{
		fncOutputError ( 9051, DEF_ERROR, "", FALSE, "", $objDB );
		$objDB->close();
		return true;
	}








	$sequence_t_goodsplan = fncGetSequence( 't_goodsplan.lnggoodsplancode', $objDB );

	$ProductCode				= $aryData["strProductCode"];
	$lngGoodsPlanProgressCode	= $aryData["lngGoodsPlanProgressCode"];
	$lngproductno				= $aryData["lngproductno"];

// 2004.03.03 suzukaze update start
	// 作成日の取得
	$strCreationQuery = "SELECT dtmCreationDate from t_GoodsPlan WHERE lngProductNo = " . $lngproductno . " and lngRevisionNo = 0 ";

	// 検索クエリーの実行
	list ( $lngCreationResultID, $lngCreationResultNum ) = fncQuery( $strCreationQuery, $objDB );

	if ( $lngCreationResultNum == 0 )
	{
		$dtmInsertDate = "now()";
	}
	else
	{
		$objCreationResult = $objDB->fetchObject( $lngCreationResultID, 0 );
		$dtmInsertDate = $objCreationResult->dtmcreationdate;
		if ( $dtmInsertDate == "" )
		{
			$dtmInsertDate = "now()";
		}
		else
		{
			$dtmInsertDate = "'" . $dtmInsertDate . "'";
		}
	}
	$objDB->freeResult( $lngCreationResultID );

// 2004.03.03 suzukaze update end

/////   リビジョン番号を現在の最大値をとるように修正する　その際にSELECT FOR UPDATEを使用して、同じ製品に対してロック状態にする

	// リビジョン番号値を同じ製品に対してロック状態にする
	$strLockQuery = "SELECT lngRevisionNo FROM t_GoodsPlan WHERE lngProductNo = " . $lngproductno . " FOR UPDATE";
	// ロッククエリーの実行
	list ( $lngLockResultID, $lngLockResultNum ) = fncQuery( $strLockQuery, $objDB );

	$lngMaxRevision = 0;
	if ( $lngLockResultNum )
	{
		for ( $i = 0; $i < $lngLockResultNum; $i++ )
		{
			$objRevision = $objDB->fetchObject( $lngLockResultID, $i );
			if ( $lngMaxRevision < $objRevision->lngrevisionno )
			{
				$lngMaxRevision = $objRevision->lngrevisionno;
			}
		}
	}
	$objDB->freeResult( $lngLockResultID );
	$RevisionNo = $lngMaxRevision + 1;

	$aryUpdate_Goods = array();
	$aryUpdate_Goods[] = "INSERT INTO t_goodsplan (";
	$aryUpdate_Goods[] = "lnggoodsplancode, ";
	$aryUpdate_Goods[] = "lngrevisionno, ";
	$aryUpdate_Goods[] = "lngproductno, ";
	$aryUpdate_Goods[] = "dtmcreationdate, ";
	$aryUpdate_Goods[] = "dtmrevisiondate, ";
	$aryUpdate_Goods[] = "lnggoodsplanprogresscode,";
	$aryUpdate_Goods[] = "lnginputusercode ";
	$aryUpdate_Goods[] = ") values (";
	$aryUpdate_Goods[] = "$sequence_t_goodsplan,"; 			//グッズプランコード
	$aryUpdate_Goods[] = "$RevisionNo,";					//リビジョン番号
	$aryUpdate_Goods[] = "$lngproductno,";					//プロダクト
	$aryUpdate_Goods[] = $dtmInsertDate . ", ";
	$aryUpdate_Goods[] = "now(), ";							//更新日
	$aryUpdate_Goods[] = "$lngGoodsPlanProgressCode, " ;
	$aryUpdate_Goods[] = "$lngInputUserCode";
	$aryUpdate_Goods[] = ")" ;

	$strUpdate_Goods = implode("\n", $aryUpdate_Goods);
	// echo "strUpdate_Goods : $strUpdate_Goods<br>";

	$objDB->freeResult( $lngResultID );
	if ( !$lngResultID = $objDB->execute( $strUpdate_Goods ) )
	{
		fncOutputError ( 9051, DEF_ERROR, "", FALSE, "", $objDB );
		$objDB->close();
		return true;
	}


	// シーケンスコードを取得
	$sequence_m_product = $aryData["strproductcode"];
	$sequence_code      = $aryData["lngproductno"];


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
//				 DBへ画像の登録が出来ませんでした
			}
		}
	}
*/

	//-------------------------------------------------------------------------
	// ■ 承認処理
	//
	//   承認ルート
	//     ・0 : 承認ルートなし
	//-------------------------------------------------------------------------
	$lngWorkflowOrderCode = $aryData["lngworkflowordercode"];	// 承認ルート

	$strWFName   = "商品 [No:" . $sequence_code . "]";
	$lngSequence = $sequence_m_product;
	$strDefFnc   = DEF_FUNCTION_P1;


	$lngApplicantUserCode = $aryData["lnginchargeusercode"];


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
		$aryQuery[] = "'" . $aryData["strworkflowmessage"] . "',";		// 11:備考
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

	$objDB->close();

	$aryData["strBodyOnload"] = "opener.window.location.reload();window.close();";
	if ( $aryData["dtmInsertDate"] )
	{
		$aryData["dtNowDate"] = substr( $aryData["dtmInsertDate"], 0, 10);
	}

	// 成功時戻り先のアドレス指定
	$aryData["strAction"] = "/p/regist/index.php?strSessionID=";

	// 帳票出力対応
	// 権限を持ってない場合もプレビューボタンを表示しない
	if( fncCheckAuthority( DEF_FUNCTION_LO1, $objAuth ) && $lngProductStatusCode != DEF_PRODUCT_APPLICATE )
	{
		$aryData["strPreviewAction"] = "../../list/result/frameset.php?strSessionID=" . $aryData["strSessionID"] . "&lngReportClassCode=" . DEF_REPORT_PRODUCT . "&strReportKeyCode=" . $lngproductno . "&bytCopyFlag=TRUE";

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


	$objDB->freeResult( $lngResultID );

	return true;

?>

