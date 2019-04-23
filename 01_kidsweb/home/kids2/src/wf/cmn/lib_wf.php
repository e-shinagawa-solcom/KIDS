<?
/** 
*	ワークフロー管理用ライブラリ
*
*	ワークフロー管理用関数ライブラリ
*
*	@package   KIDS
*	@license   http://www.wiseknot.co.jp/ 
*	@copyright Copyright &copy; 2003, Wiseknot 
*	@author    Kenji Chiba <k-chiba@wiseknot.co.jp> 
*	@access    public
*	@version   1.00
*
*/


// 種別コードに対する種別名配列を生成
//$aryFunctionCode = Array ( DEF_FUNCTION_PO1 => "発注", DEF_FUNCTION_E0 => "見積原価" );
$aryFunctionCode = Array (	DEF_FUNCTION_P1	 =>  "商品"
							,DEF_FUNCTION_SO1 => "受注"
							,DEF_FUNCTION_PO1 => "発注"
							,DEF_FUNCTION_SC1 => "売上"
							,DEF_FUNCTION_PC1 => "仕入"
							,DEF_FUNCTION_E1 =>  "見積原価"
						);

/**
* ワークフロー管理
*
*	案件読み込み、検索、詳細情報取得クエリ関数
*
*	@param  String $lngUserCode ユーザーコード
*	@param  Array  $aryData     FORMデータ
*	@param  Object $objDB       DBオブジェクト
*	@access public
*/
function getWorkflowQuery( $lngUserCode, $aryData, $objDB )
{
	$lngWorkflowCode       = $aryData['lngWorkflowCode'];
	$lngWorkflowStatusCode = $aryData['lngWorkflowStatusCode'];
	$lngApplicantUserDisplayCode  = $aryData['lngApplicantUserDisplayCode'];
	$lngInputUserDisplayCode      = $aryData['lngInputUserDisplayCode'];
	$dtmStartDateFrom      = $aryData['dtmStartDateFrom'];
	$dtmStartDateTo        = $aryData['dtmStartDateTo'];
	$dtmEndDateFrom        = $aryData['dtmEndDateFrom'];
	$dtmEndDateTo          = $aryData['dtmEndDateTo'];
	$lngInChargeCode       = $aryData['lngInChargeCode'];
	$lngFunctionCode       = $aryData['lngFunctionCode'];
	$lngSelectFunctionCode = $aryData['lngSelectFunctionCode'];
	$lngActionFunctionCode = $aryData['lngActionFunctionCode'];
	$strSort               = $aryData['strSort'];

	// ソートするカラムの対象番号設定
	$arySortColumn = array ( 1 => "m.dtmStartDate",
	                         2 => "m.strWorkflowName",
	                         3 => "strApplicantName",
	                         4 => "strInputName",
	                         5 => "strRecognitionName",
	                         6 => "t.dtmLimitDate",
	                         7 => "t.lngWorkflowStatusCode",
	                         8 => "m.dtmEndDate",
	                         9 => "m.lngFunctionCode" );

	//////////////////////////////////////////////////////////////////////////
	// 取得項目
	//////////////////////////////////////////////////////////////////////////
	if ( !$lngActionFunctionCode )
	{
		$strQuery = "SELECT\n" .
                    " t.lngWorkflowCode, t.lngWorkflowSubCode," .
                    " o.lngWorkflowOrderCode, o.lngWorkflowOrderNo, \n" .
                    " m.lngFunctionCode," .
                    " t.dtmLimitdate - now() AS lngLimitdate,\n" .
                    " to_char( t.dtmLimitdate, 'YYYY/MM/DD' ) AS dtmLimitdate,\n" .
                    " to_char( m.dtmStartDate, 'YYYY/MM/DD' ) AS dtmStartDate,\n" .
                    " to_char( m.dtmEndDate, 'YYYY/MM/DD' ) AS dtmEndDate,\n" .
                    " m.strWorkflowName, t.lngWorkflowStatusCode AS tStatusCode,\n" .
                    " u.strUserDisplayName AS strApplicantName,\n" .
                    " u2.strUserDisplayName AS strInputName,\n" .
                    " u3.strUserDisplayName AS strRecognitionName,\n" .
                    " m.lngInputUserCode, o.lngInChargeCode,\n" .
                    " m.strWorkflowKeyCode,\n" .
                    " t.lngWorkflowStatusCode \n";
	}
	elseif ( $lngActionFunctionCode == DEF_FUNCTION_WF6 )
	{
		$strQuery = "SELECT\n" .
                    " o.lngWorkflowOrderCode, o.lngWorkflowOrderNo, \n" .
                    " m.lngInputUserCode, o.lngInChargeCode, o.lngLimitDays," .
                    " t.lngWorkflowOrderNo, t.lngWorkflowSubCode, t.strNote," .
                    " m.lngFunctionCode, m.strWorkflowKeyCode," .
                    " trim(trailing from m.strWorkflowName) AS strWorkflowName," .
                    " to_char( t.dtmLimitdate, 'YYYY/MM/DD' ) AS dtmLimitdate,\n" .
                    " to_char( m.dtmStartDate, 'YYYY/MM/DD' ) AS dtmStartDate,\n" .
                    " to_char( m.dtmEndDate, 'YYYY/MM/DD' ) AS dtmEndDate,\n" .
                    " u.strUserDisplayName AS strApplicantName," .
                    " u2.strUserDisplayName AS strInputName," .
                    " u3.strUserDisplayName AS strRecognitionName," .
                    " u2.strMailAddress AS strInputMail," .
                    " u3.strMailAddress AS strRecognitionMail," .
                    " u2.bytMailtransmitFlag AS bytInputMailFlag," .
                    " u3.bytMailtransmitFlag AS bytRecognitionMailFlag," .
                    " o.lngWorkflowStatusCode AS oStatusCode," .
                    " t.lngWorkflowStatusCode AS tStatusCode\n";
	}

	$strQuery .= "FROM m_Workflow m, t_Workflow t, m_WorkflowOrder o,\n" .
                 " m_User u, m_User u2, m_User u3 \n" .
                 "WHERE";

	//////////////////////////////////////////////////////////////////////////
	// 条件
	//////////////////////////////////////////////////////////////////////////
	// 一覧            条件式             C and D and ( E or F )
	// 検索            条件式       B and C and D and ( E      or G or H )
	// 詳細・処理(一覧)条件式 A and       C and D and ( E or F )
	// 詳細・処理(検索)条件式 A and B and C and D and ( E      or G or H )
	//////////////////////////////////////////////////////////////////////////
	// A:指定したワークフローコード
	// B:各検索条件
	// C:状態 = $lngWorkflowStatusCode
	// D:無効フラグが否
	// E:入力者がログインユーザーと同じ
	// F:ワークフローテーブルにおける順番がユーザーの順番よりも大きい
	// G:ワークフローテーブルに含まれる
	// H:ログインユーザーの属するグループかつ権限が下のユーザー

	// A:指定したワークフローコード
	if ( $lngWorkflowCode )
	{
		$strQuery .= " AND m.lngWorkflowCode = $lngWorkflowCode \n";
	}

	// B:各検索条件
	if ( $aryData["lngApplicantUserDisplayCodeConditions"] && $lngApplicantUserDisplayCode ) // 申請者
	{
		//$strQuery .= " AND m.lngApplicantUserCode = $lngApplicantUserCode \n";
		$strQuery .= " AND u.strUserDisplayCode = '$lngApplicantUserDisplayCode' \n";
	}
	if ( $aryData["lngInputUserDisplayCodeConditions"] && $lngInputUserDisplayCode ) // 入力者
	{
		$strQuery .= " AND u2.strUserDisplayCode = '$lngInputUserDisplayCode' \n";
	}
	if ( $aryData["dtmStartDateConditions"] && $dtmStartDateFrom ) // 申請日から
	{
		$strQuery .= " AND date_trunc ( 'day', m.dtmStartDate ) >= '$dtmStartDateFrom' \n";
	}
	if ( $aryData["dtmStartDateConditions"] && $dtmStartDateTo ) // 申請日まで
	{
		$strQuery .= " AND date_trunc ( 'day', m.dtmStartDate ) <= '$dtmStartDateTo' \n";
	}
	if ( $aryData["dtmEndDateConditions"] && $dtmEndDateFrom ) // 完了日から
	{
		$strQuery .= " AND date_trunc ( 'day', m.dtmEndDate ) >= '$dtmEndDateFrom' \n";
	}
	if ( $aryData["dtmEndDateConditions"] && $dtmEndDateTo ) // 完了日まで
	{
		$strQuery .= " AND date_trunc ( 'day', m.dtmEndDate ) <= '$dtmEndDateTo' \n";
	}
	if ( $aryData["lngInChargeCodeConditions"] && $lngInChargeCode ) // 承認者
	{
		$strQuery .= " AND o.lngInChargeCode = $lngInChargeCode \n";
	}
	if ( $aryData["lngSelectFunctionCodeConditions"] && $lngSelectFunctionCode ) // 機能コード
	{
		$strQuery .= " AND m.lngFunctionCode = $lngSelectFunctionCode \n";
	}

	if ( $aryData["lngWorkflowStatusCodeConditions"] && $lngWorkflowStatusCode !== "" )
	{
                 // C:状態 = $lngWorkflowStatusCode
//		$strQuery .= " AND t.lngWorkflowStatusCode = $lngWorkflowStatusCode\n";
		$strQuery .= " AND t.lngWorkflowStatusCode in ( $lngWorkflowStatusCode )\n";
	}
	// 「取消」案件に関して権限チェック
	if ( $lngFunctionCode != DEF_FUNCTION_WF3 )
	{
                 // C:状態 != DEF_STATUS_CANCELL
		$strQuery .= " AND t.lngWorkflowStatusCode <> " . DEF_STATUS_CANCELL;
	}

                 // D:無効フラグが否
	$strQuery .= " AND m.bytinvalidflag = FALSE\n" .

                 " AND\n" .
                 "(\n" .

                 // E:入力者がログインユーザーと同じ
                 "  m.lngInputUserCode = $lngUserCode\n";

	if ( $lngFunctionCode == DEF_FUNCTION_WF1 )
	{
		// F:ワークフローテーブルにおける順番がユーザーの順番よりも大きい
		$strQuery .= "   OR t.lngWorkflowOrderNo >= \n" . 
                     "  (\n" .
                     "    SELECT o2.lngWorkflowOrderNo\n" .
                     "    FROM m_WorkflowOrder o2\n" .
                     "    WHERE o2.lngInChargeCode = $lngUserCode\n" .
                     "     AND m.lngWorkflowOrderCode = o2.lngWorkflowOrderCode\n" .
                     "  )\n";
	}
	elseif ( $lngFunctionCode == DEF_FUNCTION_WF2 || $lngFunctionCode == DEF_FUNCTION_WF3 )
	{
		// G:ワークフローテーブルに含まれる
		$strQuery .= "   OR m.lngWorkflowOrderCode = \n" . 
                     "  (\n" .
                     "    SELECT o2.lngWorkflowOrderCode\n" .
                     "    FROM m_WorkflowOrder o2\n" .
                     "    WHERE o2.lngInChargeCode = $lngUserCode\n" .
                     "     AND m.lngWorkflowOrderCode = o2.lngWorkflowOrderCode\n" .
                     "  )\n";

		// H:ログインユーザーの属するグループかつ権限が下のユーザー
		$strQuery .= "   OR u.lngUserCode = \n" .
		             "  (\n" .
		             "    SELECT u5.lngUserCode \n" .
                     "    FROM m_User u5, m_AuthorityGroup ag, m_GroupRelation gr \n" .
                     "    WHERE u.lngUserCode = u5.lngUserCode\n" .
                     "     AND u5.bytinvalidflag = FALSE\n" .
                     "     AND u5.lngUserCode = gr.lngUserCode\n" .
                     "     AND u5.lngAuthorityGroupCode = ag.lngAuthorityGroupCode\n" .

                     // 権限レベルが下(低いほうが権限が上)
                     "     AND ag.lngAuthorityLevel < \n" .
                     "    (\n" .
                     "      SELECT ag.lngAuthorityLevel \n" .
                     "      FROM m_User u, m_AuthorityGroup ag \n" .
                     "      WHERE u.lngUserCode = $lngUserCode\n" .
                     "       AND u.lngAuthorityGroupCode = ag.lngAuthorityGroupCode\n" .
                     "    )\n" .

                     // 同じグループ
                     "     AND gr.lngGroupCode = \n" .
                     "    (\n" .
                     "      SELECT gr2.lngGroupCode \n" .
                     "      FROM m_GroupRelation gr2\n" .
                     "      WHERE gr2.lngUserCode = $lngUserCode\n" .
                     "       AND gr2.lngUserCode = u5.lngUserCode\n" .
                     "    )\n" .
                     "  )\n";
	}

	$strQuery .= ") \n" .

	//////////////////////////////////////////////////////////////////////////
	// 紐付け
	//////////////////////////////////////////////////////////////////////////
	// m_Workflow m, t_Workflow t, m_WorkflowOrder o
	// m_User u, m_User u2, m_User u3
	             " AND m.lngWorkflowOrderCode = o.lngWorkflowOrderCode\n" .
                 " AND t.lngWorkflowOrderNo = o.lngWorkflowOrderNo\n" .
                 " AND m.lngApplicantUserCode = u.lngUserCode\n" .
                 " AND m.lngInputUserCode = u2.lngUserCode\n" .
                 " AND o.lngInChargeCode = u3.lngUserCode\n" .
	             " AND m.lngWorkflowCode = t.lngWorkflowCode\n" .
                 //" AND u.bytinvalidflag = FALSE\n" .
                 //" AND u2.bytinvalidflag = FALSE\n" .
                 //" AND u3.bytinvalidflag = FALSE\n";

	//////////////////////////////////////////////////////////////////////////
	// lngWorkflowSubCode の最大値取得
	//////////////////////////////////////////////////////////////////////////
                 " AND t.lngWorkflowSubCode = \n" .
                 "(\n" .
                 "  SELECT MAX ( t2.lngWorkflowSubCode )\n" .
                 "  FROM t_Workflow t2\n" .
                 "  WHERE t.lngWorkflowCode = t2.lngWorkflowCode\n" .
                 "  GROUP BY t2.lngWorkflowCode\n" .
                 ")\n";

	//////////////////////////////////////////////////////////////////////////
	// ソート処理
	//////////////////////////////////////////////////////////////////////////
	// $strSort 構造 "sort_[対象番号]_[降順・昇順]"

	if ( $lngFunctionCode == DEF_FUNCTION_WF2 || $lngFunctionCode == DEF_FUNCTION_WF3 )
	{
		$arySortColumn[6] = "m.dtmEndDate";
	}

	// $strSort から対象番号、降順・昇順を取得
	list ( $sort, $column, $DESC ) = explode ( "_", $strSort );
	if ( $column )
	{
		$strQuery .= "ORDER BY $arySortColumn[$column] $DESC, m.lngFunctionCode, m.dtmStartDate ASC\n";
	}
	$strQuery = preg_replace ( "/WHERE AND/", "WHERE", $strQuery );


	//////////////////////////////////////////////////////////////////////////
	// クエリ実行
	//////////////////////////////////////////////////////////////////////////
	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	//$lngResultNum = pg_Num_Rows( $lngResultID );
	$lngResultNum = pg_Num_Rows( $lngResultID );
	if ( !$lngResultNum )
	{
		$strErrorMessage = fncOutputError( 801, DEF_WARNING, "", FALSE, "/wf/index.php?strSessionID=" . $aryData["strSessionID"], $objDB );
		$strErrorMessage = "<table cellpadding=\"0\" cellspacing=\"1\" border=\"0\" bgcolor=\"#6f818\"><tr bgcolor=\"#FFFFFF\"><th>" . $strErrorMessage . "</th></tr></table>";
	}

	return array ( $lngResultID, $lngResultNum, $strErrorMessage );
}



/**
* GETデータ引数URL生成関数
*
*	@param  Array  $aryData GETデータ
*	@return String          URL(**.php?・・・以降の文字列)
*	@access public
*/
function fncGetURL( $aryData )
{
	$url = "strSessionID=" .$aryData["strSessionID"] .
           "&lngFunctionCode=" .$aryData["lngFunctionCode"] .
           "&lngSelectFunctionCode=" .$aryData["lngSelectFunctionCode"] .
           "&lngWorkflowStatusCode=" .$aryData["lngWorkflowStatusCode"];
	// 検索から実行された場合は検索条件も投げる
	// (条件:lngSelectFunctionCodeが「一覧」でなかったら)
	if ( $aryData["lngSelectFunctionCode"] != DEF_FUNCTION_WF1 )
	{
		$url .= "&lngApplicantUserDisplayCode=" .$aryData["lngApplicantUserDisplayCode"] .
                "&lngInputUserDisplayCode=" .$aryData["lngInputUserDisplayCode"] .
                "&dtmStartDateFrom=" .$aryData["dtmStartDateFrom"] .
                "&dtmStartDateTo=" .$aryData["dtmStartDateTo"] .
                "&dtmEndDateFrom=" .$aryData["dtmEndDateFrom"] .
                "&dtmEndDateTo=" .$aryData["dtmEndDateTo"] .
                "&lngInChargeCode=" .$aryData["lngInChargeCode"];
	}

	// ページ変更、ソート処理の場合は検索表示項目、検索条件項目も投げる
	// (条件:lngWorkflowCodeがなかったら)
	if ( !$aryData["lngWorkflowCode"] )
	{
		$url .= "&lngWorkflowStatusCodeVisible=" .$aryData["lngWorkflowStatusCodeVisible"] .
                "&lngApplicantUserDisplayCodeVisible=" .$aryData["lngApplicantUserDisplayCodeVisible"] .
                "&lngInputUserDisplayCodeVisible=" .$aryData["lngInputUserDisplayCodeVisible"] .
                "&dtmStartDateVisible=" .$aryData["dtmStartDateVisible"] .
                "&dtmEndDateVisible=" .$aryData["dtmEndDateVisible"] .
                "&lngInChargeCodeVisible=" .$aryData["lngInChargeCodeVisible"] .
                "&lngSelectFunctionCodeVisible=" .$aryData["lngSelectFunctionCodeVisible"] .
                "&lngWorkflowStatusCodeConditions=" .$aryData["lngWorkflowStatusCodeConditions"] .
                "&lngApplicantUserDisplayCodeConditions=" .$aryData["lngApplicantUserDisplayCodeConditions"] .
                "&lngInputUserDisplayCodeConditions=" .$aryData["lngInputUserDisplayCodeConditions"] .
                "&dtmStartDateConditions=" .$aryData["dtmStartDateConditions"] .
                "&dtmEndDateConditions=" .$aryData["dtmEndDateConditions"] .
                "&lngInChargeCodeConditions=" .$aryData["lngInChargeCodeConditions"] .
                "&lngSelectFunctionCodeConditions=" .$aryData["lngSelectFunctionCodeConditions"];
	}
	return $url;
}



/**
* データ配列取得関数
*
*	@param  Long   $lngCode  コード
*	@param  Long   $lngSQL   実行するSQLコード
*	@param  Object $objDB    DBオブジェクト
*	@return Array  $aryData1 配列1
*	        Array  $aryData2 配列2
*	@access public
*/
function fncGetArrayData( $lngCode, $lngSQL, $objDB )
{
	// ユーザーコードからワークフロー順序コードと順序番号を取得
	$strQuery[0] = "SELECT lngWorkflowOrderCode, lngWorkflowOrderNo " .
	               "FROM m_WorkflowOrder " .
	               "WHERE bytWorkflowOrderDisplayFlag = TRUE" .
	               " AND lngInChargeCode = $lngCode";

	// ワークフローコードからワークフロー順番番号とメールアドレスを取得
	//$strQuery[1] = "SELECT o.lngWorkflowOrderNo, u.strMailAddress " .
	//               "FROM m_WorkflowOrder o, m_Workflow m, m_User u " .
	//               "WHERE m.lngWorkflowCode = $lngCode" .
	//               " AND u.bytmailtransmitflag = TRUE" .
	//               " AND o.bytWorkflowOrderDisplayFlag = TRUE" .
	//               " AND o.lngWorkflowOrderCode = m.lngWorkflowOrderCode" .
	//               " AND o.lngInChargeCode = u.lngUserCode";

	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery[$lngSQL], $objDB );

	if ( $lngResultNum )
	{
		for ( $i = 0; $i < $lngResultNum; $i++ )
		{
			$aryResut = $objDB->fetchArray( $lngResultID, $i );
			$aryData1[$i] = $aryResut[0];
			$aryData2[$i] = $aryResut[1];
		}
	}

	$objDB->freeResult( $lngResultID );

	return array ( $aryData1, $aryData2 );
}

/**
* 案件情報のリンク記述を生成
*
*	@param  Object $objDB    DBオブジェクト
*	@param  Object $objResult    呼び出し元、WF検索結果オブジェクト
*	@return Array  $strWorkflowNameLink 生成結果リンク文字列
*	@access public
*	@makedate	2005/11/07
*/
function fncGetWorkflowNameLink( $objDB, $objResult, $strSessionID)
{
// 案件情報のリンク先プログラム名称
$aryFunctionLink = Array (	DEF_FUNCTION_P1	 =>  "/p/result/index2.php"
							,DEF_FUNCTION_SO1 => "/so/result/index2.php"
							,DEF_FUNCTION_PO1 => "/po/result/index2.php"
							,DEF_FUNCTION_SC1 => "/sc/result/index2.php"
							,DEF_FUNCTION_PC1 => "/pc/result/index2.php"
							,DEF_FUNCTION_E1 =>  "/estimate/result/detail.php"
						);

// 案件情報のリンク先に設定するキーコードの対象カラム名称
$aryWorkflowKeyName = array( DEF_FUNCTION_P1  => "lngProductNo"
							,DEF_FUNCTION_SO1 => "lngReceiveNo"
							,DEF_FUNCTION_PO1 => "lngOrderNo"
							,DEF_FUNCTION_SC1 => "lngSalesNo"
							,DEF_FUNCTION_PC1 => "lngStockNo"
							,DEF_FUNCTION_E1 =>  "lngEstimateNo"
						);

	$strWorkflowNameLink = "";
	
	//
	// 発注・ワークフローの場合
	//
	if( $objResult->lngfunctioncode == DEF_FUNCTION_PO1 )
	{
		// 発注にて指定している製品コードの取得処理
		$strProductCodeQuery = "SELECT od.strProductCode as strProductCode FROM t_OrderDetail od WHERE od.lngOrderNo = " . $objResult->strworkflowkeycode;

		// 値をとる =====================================
		$lngEstimateNo = "";
		list ( $lngResultProductCodeID, $lngResultProductCodeNum ) = fncQuery( $strProductCodeQuery, $objDB );
		if ( $lngResultProductCodeNum )
		{
			$objProductCodeResult = $objDB->fetchObject( $lngResultProductCodeID, 0 );
			$strProductCode = $objProductCodeResult->strproductcode;

			// 見積原価データ取得
			$aryEstimateQuery[] = "SELECT e.lngEstimateNo ";
			$aryEstimateQuery[] = "FROM m_Estimate e";
			$aryEstimateQuery[] = "WHERE e.strProductCode = '" . $strProductCode . "'";
			$aryEstimateQuery[] = " AND e.lngRevisionNo = (SELECT MAX(e2.lngRevisionNo) FROM m_Estimate e2 WHERE e.lngEstimateNo = e2.lngEstimateNo)";
			$aryEstimateQuery[] = " AND e.bytDecisionFlag = true ";

			list ( $lngResultEstimateID, $lngResultEstimateNum ) = fncQuery( join ( " ", $aryEstimateQuery ), $objDB );

			if ( $lngResultEstimateNum )
			{
				$objEstimateResult = $objDB->fetchObject( $lngResultEstimateID, 0 );
				$objDB->freeResult( $lngResultEstimateID );
				unset ( $lngResultEstimateID );
				unset ( $lngResultEstimateNum );

				$lngEstimateNo = $objEstimateResult->lngestimateno;
				unset ( $objEstimateResult );
			}
			unset( $aryEstimateQuery );
		}
		$objDB->freeResult( $lngResultProductCodeID );

		// 既に指定の製品コードに対して見積原価情報が存在すれば
		if ( $lngEstimateNo != "" )
		{
			// 発注内容と見積原価双方のウィンドウを開く処理
			$strWorkflowNameLink = "<td class=\"Segs\" onClick=\"javascript:fncShowWfDialogCommon('".$aryFunctionLink[$objResult->lngfunctioncode]."?strSessionID=" . $strSessionID . "&lngOrderNo=" . $objResult->strworkflowkeycode . "' , window.form1 , 'ResultIframeWf' , 'YES' , " . $_COOKIE["lngLanguageCode"] . " , 'detail', 505, 679, 6, 30 );\"><a class=wfA href=\"/estimate/result/detail.php?strSessionID=" . $strSessionID . "&lngEstimateNo=" . $lngEstimateNo . "\" target=_blank>" . $objResult->strworkflowname . "</a></td>";
		}
	}
	//
	// 見積原価のワークフローの場合
	//
	elseif( $objResult->lngfunctioncode == DEF_FUNCTION_E1 )
	{
		// 見積原価のワークフローの場合、見積原価情報内容のウィンドウを開く処理
		$strWorkflowNameLink = "<td class=\"Segs\"><a class=wfA href=\"".$aryFunctionLink[$objResult->lngfunctioncode]."?strSessionID=" . $strSessionID . "&lngEstimateNo=" . $objResult->strworkflowkeycode . "\" target=_blank>" . $objResult->strworkflowname . "</a></td>";
	}

	//
	// 上記、発注（見積原価・併用）、見積原価、に該当しない、他のワークフローの場合
	//
	if( empty($strWorkflowNameLink) )
	{
		$strWorkflowNameLink = "<td class=\"Segs\" onClick=\"javascript:fncShowDialogCommon('".$aryFunctionLink[$objResult->lngfunctioncode]."?strSessionID=" . $strSessionID . "&".$aryWorkflowKeyName[$objResult->lngfunctioncode]."=" . $objResult->strworkflowkeycode . "' , window.form1 , 'ResultIframeCommon' , 'YES' , " . $_COOKIE["lngLanguageCode"] . " , 'detail' );\"><a href=# class=wfA>" . $objResult->strworkflowname . "</a></td>";
	}

	return $strWorkflowNameLink;
	
}

/**
* 配列で渡された lngWorkflowStatusCode を 文字列へ変換する
*
*	@param  Array  $aryStatus "lngWorkflowStatusCode"
*	@return	string	SQL条件文に組み込む、結合された文字列
*	@access public
*	@makedate	2005/11/07
*/
function fncGetArrayToWorkflowStatusCode( $aryStatus )
{

	$aryQuery = array();
	$strRet   = "";
	
	// ワークフロー状態"lngWorkflowStatusCode"
	// チェックボックス値より、配列をそのまま代入
	
	if( is_array( $aryStatus ) )
	{
		$aryQuery[] = "";

		// WF状態は複数設定されている可能性があるので、設定個数分ループ
		$strBuff = "";
		for ( $j = 0; $j < count($aryStatus); $j++ )
		{
			// 初回処理
			if ( $j <> 0 )
			{
				$strBuff .= " ,";
			}
			$strBuff .= "" . $aryStatus[$j] . "";
		}
		$aryQuery[] = $strBuff;
		
		$strRet = implode("", $aryQuery);
		return !empty($strRet) ? $strRet : '0';
	}
	elseif(empty($aryStatus))
	{
		return null;
	}
	
	return '';

}

?>
