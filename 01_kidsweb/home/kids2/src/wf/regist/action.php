<?

// ----------------------------------------------------------------------------
/**
*       ワークフロー 案件処理実行画面
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
*
*		lib_wf.phpにて読み込むクエリを区別するための処理コード(基本はDEF_FUNCTION_WF6)
*		confirm.php -> lngActionFunctionCode -> action.php 処理コード
*	
*		表示する案件の機能コード(DEF_FUNCTION)(初期は500:発注管理のみ)
*		confirm.php -> lngSelectFunctionCode -> action.php
*	
*		押したボタン(DEF_STATUS_ORDER, DEF_STATUS_DENIAL, DEF_STATUS_CANCELL)
*		confirm.php -> lngTransactionCode    -> action.php
*
*       更新履歴
*
*/
// ----------------------------------------------------------------------------


	// 設定読み込み
	require('conf.inc');

	// ライブラリ読み込み
	require (LIB_FILE);
	require (SRC_ROOT . "wf/cmn/lib_wf.php");
	require (LIB_DEBUGFILE);
	require ( CLS_TABLETEMP_FILE ); // Temporary DB Object
	require ( LIB_ROOT . "tabletemp/excel2temp.php" );

	// DB接続
	$objDB   = new clsDB();
	$objAuth = new clsAuth();
	$objDB->open( "", "", "", "" );

	// GETデータ取得
	$aryData = $_GET;


	// 申請中の案件のみ処理が可能なため、状態「申請中」を検索条件として強制
	$aryData["lngWorkflowStatusCodeConditions"] =1;
	$aryData["lngWorkflowStatusCode"] = DEF_STATUS_ORDER;

	$aryCheck["strSessionID"]          = "null:numenglish(32,32)";
	$aryCheck["lngFunctionCode"]       = "null:number(" . DEF_FUNCTION_WF1 . "," . DEF_FUNCTION_WF3 . ")";
	$aryCheck["lngWorkflowStatusCode"] = "number(" . DEF_STATUS_VOID . "," . DEF_STATUS_DENIAL . ")";
	$aryCheck["lngApplicantUserCode"]  = "number(0,32767)";
	$aryCheck["lngInputUserCode"]      = "number(0,32767)";
	$aryCheck["dtmStartDateFrom"]      = "date(/)";
	$aryCheck["dtmStartDateTo"]        = "date(/)";
	$aryCheck["dtmEndDateFrom"]        = "date(/)";
	$aryCheck["dtmEndDateTo"]          = "date(/)";
	$aryCheck["lngInChargeCode"]       = "number(0,32767)";
	$aryCheck["lngPage"]               = "number(0,1000)";
	$aryCheck["lngWorkflowCode"]       = "number(0,2147483647)";
	$aryCheck["lngActionFunctionCode"] = "number(0,32767)";
	$aryCheck["lngSelectFunctionCode"] = "number(0,32767)";
	$aryCheck["lngTransactionCode"]    = "number(0,32767)";
	$aryCheck["strNote"]               = "length(0,300)";


	// セッション確認
	$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

	// 権限確認
	if ( !fncCheckAuthority( DEF_FUNCTION_WF6, $objAuth ) )
	{
		fncOutputError ( 9052, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
	}

	// 文字列チェック
	$aryCheckResult = fncAllCheck( $aryData, $aryCheck );
	//echo getArrayTable( $aryData, "TABLE" );exit;
	fncPutStringCheckError( $aryCheckResult, $objDB );

	// 共通受け渡しURL生成(セッションID、ページ、各検索条件)
	$strURL = fncGetURL( $aryData );

	// ワークフロー管理
	// 案件読み込み、検索、詳細情報取得クエリ関数
	list ( $lngResultID, $lngResultNum, $strErrorMessage ) = getWorkflowQuery( $objAuth->UserCode, $aryData, $objDB );

	if ( !$lngResultNum )
	{
	// この状態で対象案件が見つからない状況＝他のユーザーが処理を実行した
	// 「他のユーザーの処理により、対象案件は「申請中」ではなくなりました。」のメッセージを表示する
		fncOutputError ( 803, DEF_WARNING, "", TRUE, "", $objDB );
	}

	$objResult = $objDB->fetchObject( $lngResultID, 0 );

	//////////////////////////////////////////////////////////////////////////
	// 実行処理
	//////////////////////////////////////////////////////////////////////////

	// lngWorkflowSubCodeインクリメント
	$lngWorkflowSubCode = $objResult->lngworkflowsubcode + 1;


	// トランザクション開始
	$objDB->transactionBegin();

	// テーブルロック
	list ( $lngResultID, $lngResultNum ) = fncQuery( "LOCK TABLE t_Workflow IN EXCLUSIVE MODE", $objDB );
	list ( $lngResultID, $lngResultNum ) = fncQuery( "LOCK TABLE m_Workflow IN EXCLUSIVE MODE", $objDB );
	list ( $lngResultID, $lngResultNum ) = fncQuery( "LOCK TABLE m_Order IN EXCLUSIVE MODE", $objDB );



	// 申請中 または 否認 かつ
	// 承認者がログインユーザーと同じ
	if ( ( $objResult->tstatuscode == DEF_STATUS_ORDER || $objResult->tstatuscode == DEF_STATUS_DENIAL ) && $objResult->lnginchargecode == $objAuth->UserCode )
	{
		////////////////////////////////////////////////
		// 申請・否認
		////////////////////////////////////////////////
		list ( $arySendMailAddress, $aryParts["strStatusName"] ) = fncAction( $aryData["lngWorkflowCode"]
																, $lngWorkflowSubCode
																, $objResult->lngworkfloworderno
																, $objResult->ostatuscode
																, $objResult->lngfunctioncode
																, $objResult->strworkflowname
																, $aryData[strNote]
																, $objResult->lnglimitdays
																, $aryData["lngTransactionCode"]
																, $objResult->strworkflowkeycode
																, $objResult->strrecognitionmail
																, $objResult->strinputmail
																, $objResult->bytrecognitionmailflag
																, $objResult->bytinputmailflag
																, $objAuth->UserDisplayName
																, "ApprovalUser"
																, $objResult->lnginchargecode
																, $objAuth->UserCode
																, $objDB );
	}


	// 申請中 かつ
	// 入力者がログインユーザーと同じ
	elseif ( $objResult->tstatuscode == DEF_STATUS_ORDER && $objResult->lnginputusercode == $objAuth->UserCode )
	{
		////////////////////////////////////////////////
		// 申請取消
		////////////////////////////////////////////////
		list ( $arySendMailAddress, $aryParts["strStatusName"] ) = fncAction( $aryData["lngWorkflowCode"]
																, $lngWorkflowSubCode
																, $objResult->lngworkfloworderno
																, $objResult->ostatuscode
																, $objResult->lngfunctioncode
																, $objResult->strworkflowname
																, $aryData[strNote]
																, $objResult->lnglimitdays
																, $aryData["lngTransactionCode"]
																, $objResult->strworkflowkeycode
																, $objResult->strrecognitionmail
																, $objResult->strinputmail
																, $objResult->bytrecognitionmailflag
																, $objResult->bytinputmailflag
																, $objAuth->UserDisplayName
																, "InputUser"
																, $objResult->lnginchargecode
																, $objAuth->UserCode
																, $objDB );
	}

	// 申請中 かつ
	// ログインユーザーのワークフロー順番＜現在の順番である
	// 場合は「申請取消」を表示
	elseif ( $objResult->tstatuscode == DEF_STATUS_ORDER )
	{
		// ユーザーコードからワークフロー順序コードと順序番号を取得
		list ( $aryWorkflowOrderCode, $aryWorkflowOrderNo ) = fncGetArrayData( $objAuth->UserCode, 0, $objDB );

		// ログインユーザーのワークフロー順番番号が
		// 表示する案件の番号より小さい場合
		// 場合は「申請取消」を表示
		for ( $j = 0; $j < count ( $aryWorkflowOrderCode ); $j++ )
		{
			if ( $aryWorkflowOrderCode[$j] == $objResult->lngworkflowordercode && $aryWorkflowOrderNo[$j] < $objResult->lngworkfloworderno )
			{
				////////////////////////////////////////////////
				// 申請取消
				////////////////////////////////////////////////
				list ( $arySendMailAddress, $aryParts["strStatusName"] ) = fncAction( $aryData["lngWorkflowCode"]
																, $lngWorkflowSubCode
																, $aryWorkflowOrderNo[$j]
																, $objResult->ostatuscode
																, $objResult->lngfunctioncode
																, $objResult->strworkflowname
																, $aryData[strNote]
																, $objResult->lnglimitdays
																, $aryData["lngTransactionCode"]
																, $objResult->strworkflowkeycode
																, $objResult->strrecognitionmail
																, $objResult->strinputmail
																, $objResult->bytrecognitionmailflag
																, $objResult->bytinputmailflag
																, $objAuth->UserDisplayName
																, "ApprovalUser"
																, $objResult->lnginchargecode
																, $objAuth->UserCode
																, $objDB );
				break;
			}
		}
	}

	// トランザクションコミット
	$objDB->transactionCommit();

//	$aryParts["strSessionID"]    &= $aryData["strSessionID"];
	$aryParts["strSessionID"]    = $aryData["strSessionID"];
	$aryParts["strWorkflowName"]  = $objResult->strworkflowname;
	if ( count ( $arySendMailAddress ) > 0 )
	{
		$aryParts["strMailAddress"] = "[" . join ( ", ", $arySendMailAddress ) . "]宛に" . $aryParts["strStatusName"] . "メールを送信しました。";
	}


	//////////////////////////////////////////////////////////////////////////
	// 結果取得、出力処理
	//////////////////////////////////////////////////////////////////////////
	$objTemplate = new clsTemplate();

	// テンプレート読み込み
	$objTemplate->getTemplate( "wf/regist/finish.tmpl" );

	// テンプレート生成
	$objTemplate->replace( $aryParts );
	$objTemplate->complete();

//fncDebug('wf.txt', $objTemplate->strTemplate, __FILE__, __LINE__);

	// HTML出力
	echo $objTemplate->strTemplate;


	$objDB->close();


	//////////////////////////////////////////////////////////////////////////////
	// 処理関数
	//////////////////////////////////////////////////////////////////////////////
	/**
	* 処理関数
	*
	*	処理関数
	*
	*	@param  Long   $lngWorkflowCode        選択した案件のワークフローコード
	*	@param  Long   $lngWorkflowSubCode     選択した案件のワークフローサブコード
	*	@param  Long   $lngWorkflowOrderNo     選択した案件のワークフロー順序コード
	*	@param  Long   $lngWorkflowStatusCode  選択した案件のワークフロー状態コード
	*                                          1:承認者 2:最終承認者
	*	@param  Long   $lngFunctionCode        機能コード(EX.500発注管理)
	*	@param  String $strWorkflowName        ワークフロー名
	*	@param  String $strNote                備考(メールに書き込む)
	*	@param  Long   $lngLimitDays           期限日
	*	@param  Long   $lngTransactionCode     選択した案件の機能コード
	*                                          申請・否認・申請取消
	*	@param  String $strWorkflowKeyCode     キーコード(各機能のキーの値)
	*	@param  String $strRecognitionMail     承認者のメールアドレス
	*	@param  String $strInputMail           申請者のメールアドレス
	*	@param  String $bytRecognitionMailFlag 承認者のメール配信許可フラグ
	*	@param  String $bytInputMailFlag       申請者のメール配信許可フラグ
	*	@param  String $strUserDisplayName     ログインユーザーの表示名
	*	@param  String $strActionUser          処理を実行しているユーザーの状態
	*                                          承認者:ApprovalUser
	*                                          申請者:InputUser
	*	@param  Long   $lngInChargeCode        承認者コード
	*	@param  Long   $lngUserCode            ログインユーザーコード
	*	@param  Object $objDB                  DBオブジェクト
	*	@return Array  $arySendMailAddress     処理、エラーメッセージ
	*	        String $strStatusName          処理内容
	*	@access public
	*/
	function fncAction( $lngWorkflowCode
						, $lngWorkflowSubCode
						, $lngWorkflowOrderNo
						, $lngWorkflowStatusCode
						, $lngFunctionCode
						, $strWorkflowName
						, $strNote
						, $lngLimitDays
						, $lngTransactionCode
						, $strWorkflowKeyCode
						, $strRecognitionMail
						, $strInputMail
						, $bytRecognitionMailFlag
						, $bytInputMailFlag
						, $strUserDisplayName
						, $strActionUser
						, $lngInChargeCode
						, $lngUserCode
						, $objDB )
	{
		$aryData["strWorkflowName"]    = $strWorkflowName;
		$aryData["strNote"]            = $strNote;
		$aryData["strUserDisplayName"] = $strUserDisplayName;
		$aryData["strURL"] = LOGIN_URL;
		$aryQuery = array ();

		$strCommitStatusCode = "null";	// 管理テーブルのステータス更新値

		// 最終承認、否認、申請取消用にログインユーザーのメールアドレスを取得
		$strQuery = "SELECT strMailAddress FROM m_User WHERE lngUserCode = " . $lngUserCode;

		list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

		if ( $lngResultNum > 0 )
		{
			$objResult = $objDB->fetchObject( $lngResultID, 0 );
			$objDB->freeResult( $lngResultID );

			// デフォルト値として送信者アドレスをログインユーザーのメールアドレスとする
			// （ただし、途中承認処理の場合は申請者の情報で上書きする）
			$strFromMailAddress = $objResult->strmailaddress;
		}

		//////////////////////////////////////////////////////////////////
		// 途中承認処理(lngStatusCode = DEF_APPROVER)
		//////////////////////////////////////////////////////////////////
		if ( $lngTransactionCode == DEF_STATUS_ORDER 
			&& $lngWorkflowStatusCode == DEF_APPROVER 
			&& $lngInChargeCode == $lngUserCode )
		{
			// lngWorkflowOrderNoインクリメント
			$lngWorkflowOrderNo++;

			$aryQuery[0] = "INSERT INTO t_Workflow " .
	                       "VALUES ( $lngWorkflowCode, $lngWorkflowSubCode," .
	                       " $lngWorkflowOrderNo, " . DEF_STATUS_ORDER . "," .
	                       " '$strNote', now()," .
	                       " now() + ( interval '$lngLimitDays day' ) )";

			// ワークフローコードから次の承認者のメールアドレスとその許可フラグを取得
			$strQuery = "SELECT u.strMailAddress, u.bytMailTransmitFlag " .
	                    "FROM m_WorkflowOrder o, m_Workflow m, m_User u " .
	                    "WHERE m.lngWorkflowCode = $lngWorkflowCode" .
	                    " AND o.lngWorkflowOrderNo = $lngWorkflowOrderNo" .
	                    " AND u.bytmailtransmitflag = TRUE" .
	                    " AND o.bytWorkflowOrderDisplayFlag = TRUE" .
	                    " AND o.lngWorkflowOrderCode = m.lngWorkflowOrderCode" .
	                    " AND o.lngInChargeCode = u.lngUserCode";

			list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

			if ( $lngResultNum > 0 )
			{
				$objResult = $objDB->fetchObject( $lngResultID, 0 );
				$objDB->freeResult( $lngResultID );

				// 次の承認者にメール
				$aryMailTransmit[0] = FALSE;
				if ( $objResult->strmailaddress && $objResult->bytmailtransmitflag == "t" )
				{
					$aryMailTransmit[0] = TRUE;
					$aryMailAddress[0]  = $objResult->strmailaddress;

					// 途中承認処理の場合、メール本文に記載する人名は申請者（入力者）名とする
					// また、メールの送信者は申請者（入力者）のメールアドレスとする
					$strQuery = "SELECT m.lngApplicantUserCode as lngApplicantUserCode, "
						. "m.lngInputUserCode as lngInputUserCode, "
						. "au.strUserDisplayName as strApplicantUserDisplayName, "
						. "au.strMailAddress as strApplicantUserMailAddress, " 
						. "iu.strUserDisplayName as strInputUserDisplayName, " 
						. "iu.strMailAddress as strInputUserMailAddress " 
						. "FROM m_Workflow m " 
						. "LEFT JOIN m_User au ON m.lngApplicantUserCode = au.lngUserCode " 
						. "LEFT JOIN m_User iu ON m.lngInputUserCode = iu.lngUserCode " 
						. "WHERE m.lngWorkflowCode = " . $lngWorkflowCode;

					list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

					if ( $lngResultNum > 0 )
					{
						$objResult = $objDB->fetchObject( $lngResultID, 0 );
						$objDB->freeResult( $lngResultID );

						// 申請者と入力者の情報を確認する
						if ( $objResult->lngapplicantusercode != $objResult->lnginputusercode )
						// 申請者と入力者が違う場合、入力者情報を使用する
						{
							// $aryData に設定されている内容を上書き
							$aryData["strUserDisplayName"] = $objResult->strinputuserdisplayname;
							// メールの送信者を入力者のメールアドレスに設定
							$strFromMailAddress = $objResult->strinputusermailaddress;
						}
						else
						// 申請者と入力者が同じ場合、申請者情報を使用する
						{
							// $aryData に設定されている内容を上書き
							$aryData["strUserDisplayName"] = $objResult->strapplicantuserdisplayname;
							// メールの送信者を申請者のメールアドレスに設定
							$strFromMailAddress = $objResult->strapplicantusermailaddress;
						}
					}

					list ( $arySubject[0], $aryBody[0] ) = fncGetMailMessage( 807, $aryData, $objDB );
				}
			}
			$strStatusName = "承認";
		}

		//////////////////////////////////////////////////////////////////
		// 最終承認処理(lngStatusCode = DEF_FINAL_APPROVER)
		//////////////////////////////////////////////////////////////////
		elseif ( $lngTransactionCode == DEF_STATUS_ORDER 
				&& $lngWorkflowStatusCode == DEF_FINAL_APPROVER 
				&& $lngInChargeCode == $lngUserCode )
		{
			// ワークフローテーブルに「承認」として追加
			$aryQuery[0] = "INSERT INTO t_Workflow " .
	                       "VALUES ( $lngWorkflowCode, $lngWorkflowSubCode," .
	                       " $lngWorkflowOrderNo, " . DEF_STATUS_APPROVE . "," .
	                       " '$strNote', now()," .
	                       " NULL )";

			// ワークフローマスターの「完了日」を更新
			$aryQuery[1] = "UPDATE m_Workflow " .
	                       "SET dtmEndDate = now() " .
	                       "WHERE lngWorkflowCode = $lngWorkflowCode";

			// 申請元管理テーブルの更新ステータスを決定
			switch ( $lngFunctionCode )
			{
				case DEF_FUNCTION_P1:	// 商品マスタを「マスタ正常」状態にする
					$strCommitStatusCode = DEF_PRODUCT_NORMAL;
					break;
				case DEF_FUNCTION_SO1:	// 受注マスターを「受注」状態にする
					$strCommitStatusCode = DEF_RECEIVE_ORDER;
					break;
				case DEF_FUNCTION_PO1:	// 発注マスターを「発注」状態にする
					$strCommitStatusCode = DEF_ORDER_ORDER;
					break;
				case DEF_FUNCTION_SC1:	// 売上マスターを「納品中」状態にする
					$strCommitStatusCode = DEF_ORDER_DELIVER;
					break;
				case DEF_FUNCTION_SO1:	// 仕入マスターを「納品中」状態にする
					$strCommitStatusCode = DEF_STOCK_DELIVER;
					break;
				case DEF_FUNCTION_E1:	// 見積原価管理に対するワークフロー
					$strCommitStatusCode = DEF_ESTIMATE_APPROVE;
					break;
			}

			// 対応するデータをチェックする
			switch ( $lngFunctionCode )
			{
				case DEF_FUNCTION_SO1:	// 受注　（売上データのチェック）
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
					$arySql[] = "		ms1.strreceivecode = (select strreceivecode from m_receive where lngreceiveno = $strWorkflowKeyCode)";
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
							$strCommitStatusCode = DEF_RECEIVE_DELIVER;
						}
					}
					break;

				case DEF_FUNCTION_PO1:	// 発注　（仕入データのチェック）
					$arySql = array();
					$arySql[] = "select count(*) as count";
					$arySql[] = "from";
					$arySql[] = "	m_stock ms";
					//--		left join t_stockdetail tsd on tsd.lngstockno = ms.lngstockno
					$arySql[] = "where";
					$arySql[] = "ms.lngorderno in ";
					$arySql[] = "(";
					$arySql[] = "	select mo1.lngorderno";
					$arySql[] = "	from";
					$arySql[] = "		m_order mo1";
					$arySql[] = "	where";
					$arySql[] = "		mo1.strordercode = (select strordercode from m_order where lngorderno = $strWorkflowKeyCode)";
					$arySql[] = ")";
					$arySql[] = "and ms.bytinvalidflag = false";
					$arySql[] = "AND ms.lngRevisionNo = (";
					$arySql[] = "	SELECT MAX( s1.lngRevisionNo ) FROM m_stock s1 WHERE s1.bytInvalidFlag = false and s1.strStockCode = ms.strStockCode)";
					$arySql[] = "	AND 0 <= (";
					$arySql[] = "		SELECT MIN( s2.lngRevisionNo ) FROM m_stock s2 WHERE s2.bytInvalidFlag = false and s2.strStockCode = ms.strStockCode )";

					$strQuery = implode("\n", $arySql);
					// ＤＢ問い合わせ
					list( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

					if ( $lngResultNum == 1 )
					{
						$objResult	= $objDB->fetchObject( $lngResultID, 0 );
						// 一個以上、仕入データがあれば、「納品中」とする
						if( 1 <= (int)$objResult->count)
						{
							$strCommitStatusCode = DEF_ORDER_DELIVER;
						}
					}
					break;

				case DEF_FUNCTION_E1:	// 見積原価管理に対するワークフロー
					// Excelからアップロードされたファイルを処理するロジック
					$arySql = array();
					$arySql[] = "select me.lngtempno as lngtempno";
					$arySql[] = "from";
					$arySql[] = "m_estimate me";
					$arySql[] = "where";
					$arySql[] = "me.lngrevisionno = (select max(lngrevisionno) from m_estimate where lngestimateno = me.lngestimateno)";
					$arySql[] = "and me.lngtempno is not null";
					$arySql[] = "and me.lngestimateno=".$strWorkflowKeyCode;

					$strQuery = implode("\n", $arySql);
					// ＤＢ問い合わせ
					list( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
					// lngTempNo の存在を確認
					if( $lngResultNum != 1 )
					{
						break;
					}
					// lngTempNo を取得
					$objResult = $objDB->fetchObject( $lngResultID, 0 );
					$lngTempNo = trim($objResult->lngtempno);
					if(!is_numeric($lngTempNo))
					{
						break;
					}

					// テンポラリテーブルの情報を用いて商品マスタを上書き
					if( !fncTemp2ProductUpdate( $objDB, $lngTempNo) )
					{
						fncOutputError ( 9101, DEF_WARNING, "", TRUE, "", $objDB );
					}

					// 対象見積原価テーブルのlngTempNoを消す
					if( !fncDeleteEstimateTempNo( $objDB, $strWorkflowKeyCode ) )
					{
						fncOutputError ( 9101, DEF_WARNING, "", TRUE, "", $objDB );
					}
					break;
			}

//fncDebug('action.txt', $strQuery, __FILE__, __LINE__);
//fncDebug('action.txt', $objResult->count, __FILE__, __LINE__);

/*2011 12 9 kou and
発注　最終承認メッセージに製品コード追加*/

			if($lngFunctionCode == DEF_FUNCTION_PO1)
			{
				$strQuery = "SELECT distinct mp.strProductCode as strProductCode" .
     						",mp.strProductName as strProductName " .
						" FROM m_Product mp,t_OrderDetail tod " .
						" WHERE tod.strProductCode = mp.strProductCode " .
						" AND tod.lngOrderNo = $strWorkflowKeyCode " ;
					list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

					if ( $lngResultNum > 0 )
					{
						$objResult = $objDB->fetchObject( $lngResultID, 0 );
						$objDB->freeResult( $lngResultID );

						$strProductCode = $objResult->strproductcode;
						$strProductName = $objResult->strproductname;
					}

				$aryData["strWorkflowName"]    = "製品コード：[ ". $strProductCode . "]\n 製品名称：". $strProductName ."\n ". $strWorkflowName;
			
			}


//fncDebug('action.txt', $objResult->strproductcode, $objResult->strproductname __FILE__, __LINE__);
/*
		$aryParts["strMailAddress"] = "[" . join ( ", ", $arySendMailAddress ) . "]宛に" . $aryParts["strStatusName"] . "メールを送信しました。";
2011 12 9 kou end
*/
			// 申請者にメール
			$aryMailTransmit[0] = FALSE;
			if ( $bytInputMailFlag == "t" )
			{
				$aryMailTransmit[0] = TRUE;
				$aryMailAddress[0]  = $strInputMail;
				list ( $arySubject[0], $aryBody[0] ) = fncGetMailMessage( 808, $aryData, $objDB );
			}
			$strStatusName = "最終承認";
		}

		//////////////////////////////////////////////////////////////////
		// 否認処理
		//////////////////////////////////////////////////////////////////
		elseif ( $lngTransactionCode == DEF_STATUS_DENIAL && $lngInChargeCode == $lngUserCode )
		{

			// 申請元管理テーブルの更新ステータスを決定
			switch ( $lngFunctionCode )
			{
				case DEF_FUNCTION_P1:	// 商品マスタを「マスタ正常」状態にする
					$strCommitStatusCode = DEF_PRODUCT_NORMAL;
					break;
				// 商品マスタ以外はNULL
			}

			// ワークフローテーブルに「否認」として追加
			$aryQuery[0] = "INSERT INTO t_Workflow " .
	                       "VALUES ( $lngWorkflowCode, $lngWorkflowSubCode," .
	                       " $lngWorkflowOrderNo, " . DEF_STATUS_DENIAL . "," .
	                       " '$strNote', now()," .
	                       " NULL )";

			// ワークフローマスターの「完了日」を更新
			$aryQuery[1] = "UPDATE m_Workflow " .
	                       "SET dtmEndDate = now() " .
	                       "WHERE lngWorkflowCode = $lngWorkflowCode";

			// 申請者にメール
			$aryMailTransmit[0] = FALSE;
			if ( $bytInputMailFlag == "t" )
			{
				$aryMailTransmit[0] = TRUE;
				$aryMailAddress[0]  = $strInputMail;
				list ( $arySubject[0], $aryBody[0] ) = fncGetMailMessage( 809, $aryData, $objDB );
			}
			$strStatusName = "否認";
		}

		//////////////////////////////////////////////////////////////////
		// 申請取消処理
		//////////////////////////////////////////////////////////////////
		elseif ( $lngTransactionCode == DEF_STATUS_CANCELL )
		{

			// 申請元管理テーブルの更新ステータスを決定
			switch ( $lngFunctionCode )
			{
				case DEF_FUNCTION_P1:	// 商品マスタを「マスタ正常」状態にする
					$strCommitStatusCode = DEF_PRODUCT_NORMAL;
					break;
				// 商品マスタ以外はNULL
			}

			// ワークフローテーブルに「申請取消」として追加
			$aryQuery[0] = "INSERT INTO t_Workflow " .
	                       "VALUES ( $lngWorkflowCode, $lngWorkflowSubCode," .
	                       " $lngWorkflowOrderNo, " . DEF_STATUS_CANCELL . "," .
	                       " '$strNote', now(), NULL )";

			// ワークフローマスターの「完了日」を更新
			$aryQuery[1] = "UPDATE m_Workflow " .
	                       "SET dtmEndDate = now() " .
	                       "WHERE lngWorkflowCode = $lngWorkflowCode";

			// 現在の承認者にメール
			$aryMailTransmit[0] = FALSE;
			if ( $bytRecognitionMailFlag == "t" )
			{
				$aryMailTransmit[0] = TRUE;
				$aryMailAddress[0]  = $strRecognitionMail;
				list ( $arySubject[0], $aryBody[0] ) = fncGetMailMessage( 810, $aryData, $objDB );
			}

			// ログインユーザーが承認者だった場合
			if ( $strActionUser == "ApprovalUser" )
			{
				// 申請者にメール
				$aryMailTransmit[1] = FALSE;
				if ( $bytInputMailFlag == "t" )
				{
					$aryMailTransmit[1] = TRUE;
					$aryMailAddress[1]  = $strInputMail;
					list ( $arySubject[1], $aryBody[1] ) = fncGetMailMessage( 810, $aryData, $objDB );
				}
			}
			$strStatusName = "取消";
		}

		//
		// 最終承認、否認、申請取消処理の場合のみ、申請元管理テーブルのステータスを更新する
		//	$strCommitStatusCode が設定されない場合、ステータスはnullで更新
		//
		if(    ($lngTransactionCode == DEF_STATUS_ORDER  && $lngWorkflowStatusCode == DEF_FINAL_APPROVER )
			|| ($lngTransactionCode == DEF_STATUS_DENIAL && $lngInChargeCode == $lngUserCode)
			|| ($lngTransactionCode == DEF_STATUS_CANCELL)
			)
		{
			$arySqlLine = array();
			
			switch ( $lngFunctionCode )
			{
				case DEF_FUNCTION_P1:	// 商品マスタを「マスタ正常」状態にする
					$arySqlLine[] = "UPDATE m_Product ";
					$arySqlLine[] = "SET lngProductStatusCode = " . $strCommitStatusCode . ",";
					$arySqlLine[] = "dtmUpdateDate = now()";
					$arySqlLine[] = " WHERE lngProductNo = $strWorkflowKeyCode";
					$arySqlLine[] = " AND bytInvalidFlag = FALSE";
					break;
				case DEF_FUNCTION_SO1:	// 受注マスターを「○○」状態にする　（受注、納品中）
					$arySqlLine[] = "UPDATE m_Receive ";
					$arySqlLine[] = "SET lngReceiveStatusCode = " . $strCommitStatusCode . ",";
					$arySqlLine[] = "dtmInsertDate = now()";
					$arySqlLine[] = " WHERE lngReceiveNo = $strWorkflowKeyCode";
					$arySqlLine[] = " AND lngRevisionNo > -1";
					$arySqlLine[] = " AND bytInvalidFlag = FALSE";
					break;
				case DEF_FUNCTION_PO1:	// 発注マスターを「○○」状態にする　（発注、納品中）
					$arySqlLine[] = "UPDATE m_Order ";
					$arySqlLine[] = "SET lngOrderStatusCode = " . $strCommitStatusCode . ",";
					$arySqlLine[] = "dtmInsertDate = now()";
					$arySqlLine[] = " WHERE lngOrderNo = $strWorkflowKeyCode";
					$arySqlLine[] = " AND lngRevisionNo > -1";
					$arySqlLine[] = " AND bytInvalidFlag = FALSE";
					break;
				case DEF_FUNCTION_SC1:	// 売上マスターを「納品中」状態にする
					$arySqlLine[] = "UPDATE m_Order ";
					$arySqlLine[] = "SET lngOrderStatusCode = " . $strCommitStatusCode . ",";
					$arySqlLine[] = "dtmInsertDate = now()";
					$arySqlLine[] = " WHERE lngOrderNo = $strWorkflowKeyCode";
					$arySqlLine[] = " AND lngRevisionNo > -1";
					$arySqlLine[] = " AND bytInvalidFlag = FALSE";
					break;
				case DEF_FUNCTION_PC1:	// 仕入マスターを「納品中」状態にする
					$arySqlLine[] = "UPDATE m_Stock ";
					$arySqlLine[] = "SET lngStockStatusCode = " . $strCommitStatusCode . ",";
					$arySqlLine[] = "dtmInsertDate = now()";
					$arySqlLine[] = " WHERE lngStockNo = $strWorkflowKeyCode";
					$arySqlLine[] = " AND lngRevisionNo > -1";
					$arySqlLine[] = " AND bytInvalidFlag = FALSE";
					break;
				case DEF_FUNCTION_E1:	// 見積原価管理に対するワークフロー
					$arySqlLine[] = "UPDATE m_Estimate ";
					$arySqlLine[] = "SET lngEstimateStatusCode = " . $strCommitStatusCode . ",";
					$arySqlLine[] = "dtmInsertDate = now()";
					$arySqlLine[] = " WHERE lngEstimateNo = $strWorkflowKeyCode";
					$arySqlLine[] = " AND lngRevisionNo > -1";
					$arySqlLine[] = " AND bytInvalidFlag = FALSE";
					break;
			}
			// arySqlLine 結合
			if( !empty($arySqlLine) )
			{
				$aryQuery[2] = implode("\n", $arySqlLine);
			}
		}

		// クエリ実行
		foreach ( $aryQuery as $strQuery )
		{
			//echo "クエリ実行$strQuery\n";
			if ( !list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB ) )
			{
				fncOutputError ( 802, DEF_FATAL, "更新失敗。", TRUE, "", $objDB );
			}

			$objDB->freeResult( $lngResultID );
		}

		// メール送信
		for ( $i = 0; $i < count ( $aryMailTransmit ); $i++ )
		{
			$strAdminMailAddress = fncGetCommonFunction( "adminmailaddress", "m_adminfunction", $objDB );
			$arySendMailAddress[] = $aryMailAddress[$i];
			
			$bytMailSendFlag = fncSendMail( $aryMailAddress[$i], $arySubject[$i], $aryBody[$i], "From: $strFromMailAddress\nReturn-Path: " . $strAdminMailAddress . "\n" );
			
			if ( !$aryMailTransmit[$i] || !$aryMailAddress[$i] || !bytMailSendFlag )
			{
				$arySendMailAddress[] = fncOutputError ( 9053, DEF_WARNING, "メール送信失敗。", FALSE, "", $objDB );
			}
		}
		return Array ( $arySendMailAddress, $strStatusName );
	}

	return TRUE;
?>
