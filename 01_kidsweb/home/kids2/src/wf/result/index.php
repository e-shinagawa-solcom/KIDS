<?
	/** 
	*	ワークフロー 案件一覧表示画面
	*
	*	@package   KIDS
	*	@license   http://www.wiseknot.co.jp/ 
	*	@copyright Copyright &copy; 2003, Wiseknot 
	*	@author    Kenji Chiba <k-chiba@wiseknot.co.jp> 
	*	@access    public
	*	@version   1.00
	*
	*/
	// -------------------------------------------------------------------------
	// 共通して投げる変数(渡す時のURLを lib_wf.php から fncGetURL($aryData) で取得可)
	// *.php -> strSessionID          -> index.php

	// どのページからきたのかを判別するための機能コード
	// *.php -> lngFunctionCode       -> index.php

	// *.php -> lngWorkflowStatusCode        -> index.php
	// *.php -> lngApplicantUserDisplayCode  -> index.php
	// *.php -> lngInputUserDisplayCode      -> index.php
	// *.php -> dtmStartDateFrom             -> index.php
	// *.php -> dtmStartDateTo               -> index.php
	// *.php -> dtmEndDateFrom               -> index.php
	// *.php -> dtmEndDateTo                 -> index.php
	// *.php -> lngInChargeCode              -> index.php

	// 表示する案件の機能コード(DEF_FUNCTION)(初期は500:発注管理のみ)
	// *.php -> lngSelectFunctionCode -> index.php

	// -------------------------------------------------------------------------
	// 案件一覧より
	// /wf/list/index.php -> strSessionID                       -> index.php
	// /wf/list/index.php -> lngFunctionCode                    -> index.php
	// /wf/list/index.php -> ViewColumn[]                       -> index.php
	// /wf/list/index.php -> SearchColumn[]                     -> index.php
	//
	// 検索表示項目(ViewColumn[]の中身)keyは数値連番valueは下記文字列
	// /wf/search/search.php -> lngWorkflowStatusCodeVisible       -> index.php
	// /wf/search/search.php -> lngApplicantUserDisplayCodeVisible -> index.php
	// /wf/search/search.php -> lngInputUserCodeVisible            -> index.php
	// /wf/search/search.php -> dtmStartDateVisible                -> index.php
	// /wf/search/search.php -> dtmEndDateVisible                  -> index.php
	// /wf/search/search.php -> lngInChargeCodeVisible             -> index.php
	// /wf/search/search.php -> lngSelectFunctionCodeVisible       -> index.php

	// 検索条件項目(SearchColumn[]の中身)keyは数値連番valueは下記文字列
	// /wf/search/search.php -> lngWorkflowStatusCodeConditions   -> index.php
	// /wf/search/search.php -> lngApplicantUserCodeConditions    -> index.php
	// /wf/search/search.php -> lngInputUserDisplayCodeConditions -> index.php
	// /wf/search/search.php -> dtmStartDateConditions            -> index.php
	// /wf/search/search.php -> dtmEndDateConditions              -> index.php
	// /wf/search/search.php -> lngInChargeCodeConditions         -> index.php
	// /wf/search/search.php -> lngSelectFunctionCodeConditions   -> index.php
	//
	// -------------------------------------------------------------------------
	// 案件検索より
	// 共通して投げる変数＋
	// /wf/search/search.php -> ViewColumn[]                    -> index.php
	// /wf/search/search.php -> SearchColumn[]                  -> index.php
	// /wf/search/search.php -> lngDefaultNumBerofList          -> index.php
	//
	// -------------------------------------------------------------------------
	// ページ変更へ
	// 共通して投げる変数＋
	// index.php -> lngPage                -> index.php
	// index.php -> strSort                -> index.php
	// index.php -> lngDefaultNumBerofList -> index.php
	//
	// -------------------------------------------------------------------------
	// ソートへ
	// 共通して投げる変数＋
	// index.php -> lngPage                -> index.php
	// index.php -> strSort                -> index.php
	// index.php -> lngDefaultNumBerofList -> index.php
	//
	// -------------------------------------------------------------------------
	// 詳細表示へ
	// 共通して投げる変数＋
	// index.php -> lngWorkflowCode       -> detail.php
	//
	// -------------------------------------------------------------------------
	// 処理へ
	// 共通して投げる変数＋
	// index.php -> lngWorkflowCode       -> edit.php

	// lib_wf.phpにて読み込むクエリを区別するための処理コード(基本はDEF_FUNCTION_WF6)
	// index.php -> lngActionFunctionCode -> edit.php

	// 設定読み込み
	include_once('conf.inc');

	// ライブラリ読み込み
	require (LIB_FILE);
	require (SRC_ROOT . "wf/cmn/lib_wf.php");
	require( LIB_DEBUGFILE );

	// DB接続
	$objDB   = new clsDB();
	$objAuth = new clsAuth();
	$objDB->open( "", "", "", "" );

	//////////////////////////////////////////////////////////////////////////
	// POST(一部GET)データ取得
	//////////////////////////////////////////////////////////////////////////
	if ( $_POST )
	{
		$aryData = $_POST;
	}
	elseif ( $_GET )
	{
		$aryData = $_GET;
	}

	// 検索表示項目取得
	if ( $lngArrayLength = count ( $aryData["ViewColumn"] ) )
	{
		$aryColumn = $aryData["ViewColumn"];
		for ( $i = 0; $i < $lngArrayLength; $i++ )
		{
			$aryData[$aryColumn[$i]] = 1;
		}
		$aryData["ViewColumn"] = "";
		$aryColumn = "";
	}

	// 検索条件項目取得
	if ( $lngArrayLength = count ( $aryData["SearchColumn"] ) )
	{
		$aryColumn = $aryData["SearchColumn"];
		for ( $i = 0; $i < $lngArrayLength; $i++ )
		{
			$aryData[$aryColumn[$i]] = 1;
		}
		$aryData["SearchColumn"] = "";
		$aryColumn = "";
	}
	
	// チェックボックスで渡されたWFステータスを文字列で設定
	$aryData["lngWorkflowStatusCode"] = fncGetArrayToWorkflowStatusCode($aryData["lngWorkflowStatusCode"]);

	//////////////////////////////////////////////////////////////////////////
	// セッション、権限確認
	//////////////////////////////////////////////////////////////////////////
	// セッション確認
	$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

	// 権限確認
	if ( ( $aryData["lngFunctionCode"] != DEF_FUNCTION_WF1 && $aryData["lngFunctionCode"] != DEF_FUNCTION_WF2 && $aryData["lngFunctionCode"] != DEF_FUNCTION_WF3 ) || !fncCheckAuthority( $aryData["lngFunctionCode"], $objAuth ) )
	{
		fncOutputError ( 9052, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
	}
	if ( $aryData["lngFunctionCode"] != DEF_FUNCTION_WF1 && fncCheckAuthority( DEF_FUNCTION_WF3, $objAuth ) )
	{
		$aryData["lngFunctionCode"] = DEF_FUNCTION_WF3;
	}


	//////////////////////////////////////////////////////////////////////////
	// 文字列チェック
	//////////////////////////////////////////////////////////////////////////
	$aryCheck["strSessionID"]           = "null:numenglish(32,32)";
	$aryCheck["lngFunctionCode"]        = "null:number(" . DEF_FUNCTION_WF1 . "," . DEF_FUNCTION_WF3 . ")";
	//$aryCheck["lngWorkflowStatusCode"]  = "number(" . DEF_STATUS_VOID . "," . DEF_STATUS_DENIAL . ")";
	$aryCheck["lngApplicantUserDisplayCode"] = "numenglish(1,3)";
	$aryCheck["lngInputUserDisplayCode"]     = "numenglish(1,3)";
	$aryCheck["dtmStartDateFrom"]       = "date(/)";
	$aryCheck["dtmStartDateTo"]         = "date(/)";
	$aryCheck["dtmEndDateFrom"]         = "date(/)";
	$aryCheck["dtmEndDateTo"]           = "date(/)";
	$aryCheck["lngInChargeCode"]        = "number(0,32767)";
	$aryCheck["lngPage"]                = "number";
	$aryCheck["lngWorkflowCode"]        = "number(0,2147483647)";
	$aryCheck["lngSelectFunctionCode"]  = "number(0,32767)";
	$aryCheck["lngDefaultNumBerofList"] = "number(0,100)";

	// ページ数初期化
	if ( !$aryData["lngPage"] )
	{
		$aryData["lngPage"] = 0;
	}

	// 文字列チェック
	$aryCheckResult = fncAllCheck( $aryData, $aryCheck );
	fncPutStringCheckError( $aryCheckResult, $objDB );

	// ソート初期化
	if ( !$aryData["strSort"] )
	{
		$aryData["strSort"] = "column_7_ASC";
	}

	// ワークフロー状態の初期化(デフォルト＝「申請中」案件)
	if ( $aryData["lngFunctionCode"] == DEF_FUNCTION_WF1 )
	{
		$aryData["lngWorkflowStatusCode"] = DEF_STATUS_ORDER;
	}

	// 共通機能マスタから「表示件数」を取得
	if ( $aryData["lngDefaultNumBerofList"] == "" )
	{
		$aryData["lngDefaultNumBerofList"] = fncGetCommonFunction( "defaultnumberoflist", "m_commonfunction", $objDB );
	}

	// 「処理ボタン」表示確認のための
	// ログインユーザーのワークフローコードと番号を取得
	list ( $aryWorkflowOrderCode, $aryWorkflowOrderNo ) = fncGetArrayData( $objAuth->UserCode, 0, $objDB );

	// ワークフロー管理
	// 案件読み込み、検索、詳細情報取得クエリ関数
	list ( $lngResultID, $lngResultNum, $baseData["strErrorMessage"] ) = getWorkflowQuery( $objAuth->UserCode, $aryData, $objDB );

	// 共通受け渡しURL生成(セッションID、ページ、各検索条件)
	$strURL = fncGetURL( $aryData );

	// クッキーから言語コードを取得
	$partsData["lngLanguageCode"] = $baseData["lngLanguageCode"] = $_COOKIE["lngLanguageCode"];

	//////////////////////////////////////////////////////////////////////////
	// 結果取得、出力処理
	//////////////////////////////////////////////////////////////////////////
	// パーツテンプレート読み込み
	$objTemplate = new clsTemplate();
	$objTemplate->getTemplate( "wf/result/parts.tmpl" );
	$strPartsTemplate = $objTemplate->strTemplate;


	// ページ数処理
	if ( $aryData["lngDefaultNumBerofList"] == 0 )
	{
		// 全件表示
		$lngStartView = 0;
		$lngEndView   = $lngResultNum;
		$baseData["prev_visibility"] = "hidden";
		$baseData["next_visibility"] = "hidden";
	}
	else
	{
		// ページ数処理
		$baseData["prev"]            = "index.php?$strURL&strSort=$aryData[strSort]&lngDefaultNumBerofList=$aryData[lngDefaultNumBerofList]&lngPage=" . ( $aryData["lngPage"] - 1 );
		$baseData["prev_visibility"] = "visible";
		if ( $lngResultNum )
		{
			$baseData["page"]            = $aryData["lngPage"] + 1 . "/" . ceil ( $lngResultNum / $aryData["lngDefaultNumBerofList"] );
		}
		$baseData["next"]            = "index.php?$strURL&strSort=$aryData[strSort]&lngDefaultNumBerofList=$aryData[lngDefaultNumBerofList]&lngPage=" . ( $aryData["lngPage"] + 1 );
		$baseData["next_visibility"] = "visible";

		// 検索結果数、表示ページ、表示数から、結果表示領域を設定
		if ( $lngResultNum - $aryData["lngDefaultNumBerofList"] * $aryData["lngPage"] <= $aryData["lngDefaultNumBerofList"] )
		{
			$lngStartView = $aryData["lngPage"] * $aryData["lngDefaultNumBerofList"];
			$lngEndView   = $lngResultNum;
			$baseData["next_visibility"] = "hidden";
		}
		else
		{
			$lngStartView = $aryData["lngPage"] * $aryData["lngDefaultNumBerofList"];
			$lngEndView   = ( $aryData["lngPage"] + 1 ) * $aryData["lngDefaultNumBerofList"];
		}
		// 最初のページの場合、「prev」を表示しない
		if ( !$aryData["lngPage"] )
		{
			$baseData["prev"] = "";
			$baseData["prev_visibility"] = "hidden";
		}
	}


	// テーブルの列名とソート処理
	if ( $aryData["lngSelectFunctionCodeVisible"] )
	{
		// 種別
		$baseData["column9"] = "<td id=\"WF11\" nowrap onmouseover=\"SortOn( this );\" onmouseout=\"SortOff( this );\" onclick=\"location.href='index.php?$strURL&strSort=column_9_ASC&lngPage=$aryData[lngPage]&lngDefaultNumBerofList=$aryData[lngDefaultNumBerofList]';\"><a href=\"#\">種別</a></td>";
	}
	if ( $aryData["dtmStartDateVisible"] )
	{
		// 申請日
		$baseData["column1"] = "<td id=\"WF02\" nowrap onmouseover=\"SortOn( this );\" onmouseout=\"SortOff( this );\" onclick=\"location.href='index.php?$strURL&strSort=column_1_ASC&lngPage=$aryData[lngPage]&lngDefaultNumBerofList=$aryData[lngDefaultNumBerofList]';\"><a href=\"#\">申請日</a></td>";
	}
	// 案件情報
	$baseData["column2"] = "<td id=\"WF03\" nowrap onmouseover=\"SortOn( this );\" onmouseout=\"SortOff( this );\" onclick=\"location.href='index.php?$strURL&strSort=column_2_ASC&lngPage=$aryData[lngPage]&lngDefaultNumBerofList=$aryData[lngDefaultNumBerofList]';\"><a href=\"#\">案件情報</a></td>";

	if ( $aryData["lngApplicantUserDisplayCodeVisible"] )
	{
		// 申請者
		$baseData["column3"] = "<td id=\"WF04\" nowrap onmouseover=\"SortOn( this );\" onmouseout=\"SortOff( this );\" onclick=\"location.href='index.php?$strURL&strSort=column_3_ASC&lngPage=$aryData[lngPage]&lngDefaultNumBerofList=$aryData[lngDefaultNumBerofList]';\"><a href=\"#\">申請者</a></td>";
	}

	if ( $aryData["lngInputUserDisplayCodeVisible"] )
	{
		// 入力者
		$baseData["column4"] = "<td id=\"WF05\" nowrap onmouseover=\"SortOn( this );\" onmouseout=\"SortOff( this );\" onclick=\"location.href='index.php?$strURL&strSort=column_4_ASC&lngPage=$aryData[lngPage]&lngDefaultNumBerofList=$aryData[lngDefaultNumBerofList]';\"><a href=\"#\">入力者</a></td>";
	}

	if ( $aryData["lngInChargeCodeVisible"] )
	{
		// 承認者
		$baseData["column5"] = "<td id=\"WF06\" nowrap onmouseover=\"SortOn( this );\" onmouseout=\"SortOff( this );\" onclick=\"location.href='index.php?$strURL&strSort=column_5_ASC&lngPage=$aryData[lngPage]&lngDefaultNumBerofList=$aryData[lngDefaultNumBerofList]';\"><a href=\"#\">承認者</a></td>";
	}

	// 期限
	$baseData["column6"] = "<td id=\"WF07\" nowrap onmouseover=\"SortOn( this );\" onmouseout=\"SortOff( this );\" onclick=\"location.href='index.php?$strURL&strSort=column_6_ASC&lngPage=$aryData[lngPage]&lngDefaultNumBerofList=$aryData[lngDefaultNumBerofList]';\"><a href=\"#\">期限</a></td>";

	if ( $aryData["dtmEndDateVisible"] )
	{
		// 完了日
		$baseData["column8"] = "<td id=\"WF10\" nowrap onmouseover=\"SortOn( this );\" onmouseout=\"SortOff( this );\" onclick=\"location.href='index.php?$strURL&strSort=column_8_ASC&lngPage=$aryData[lngPage]&lngDefaultNumBerofList=$aryData[lngDefaultNumBerofList]';\"><a href=\"#\">完了日</a></td>";
	}

	if ( $aryData["lngWorkflowStatusCodeVisible"] )
	{
		// 状態
		$baseData["column7"] = "<td id=\"WF08\" nowrap onmouseover=\"SortOn( this );\" onmouseout=\"SortOff( this );\" onclick=\"location.href='index.php?$strURL&strSort=column_7_ASC&lngPage=$aryData[lngPage]&lngDefaultNumBerofList=$aryData[lngDefaultNumBerofList]';\"><a href=\"#\">状態</a></td>";
	}

	// 同じ項目のソートは逆順にする処理
	list ( $column, $lngSort, $DESC ) = explode ( "_", $aryData["strSort"] );

	if ( $DESC == 'ASC' )
	{
		$baseData["column" . $lngSort] = preg_replace ( "/ASC/", "DESC", $baseData["column" . $lngSort] );
	}

	// $lngStartView から $lngEndView だけパーツテンプレートに埋め込み
	//for ( $i = 0; $i < $lngResultNum; $i++ )
	for ( $i = $lngStartView; $i < $lngEndView; $i++ )
	{
		$objResult = $objDB->fetchObject( $lngResultID, $i );

		// 連番
		$partsData["number"]            = $i + 1;
		// 詳細URL
		$partsData["detail"]            = "/wf/result/detail.php?$strURL&lngWorkflowCode=" . $objResult->lngworkflowcode;
		// 種別
		if ( $aryData["lngSelectFunctionCodeVisible"] )
		{
			$partsData["lngSelectFunctionCode"]  = "<td nowrap>" . $aryFunctionCode[$objResult->lngfunctioncode] . "</td>";
		}
		// 申請日
		if ( $aryData["dtmStartDateVisible"] )
		{
			$partsData["dtmStartDate"]  = "<td nowrap>" . $objResult->dtmstartdate . "</td>";
		}
	/*
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
				$partsData["strWorkflowName"]   = "<td onClick=\"javascript:fncShowWfDialogCommon('/po/result/index2.php?strSessionID=" . $aryData["strSessionID"] . "&lngOrderNo=" . $objResult->strworkflowkeycode . "' , window.form1 , 'ResultIframeWf' , 'YES' , " . $_COOKIE["lngLanguageCode"] . " , 'detail', 505, 679, 6, 30 );\"><a class=wfA href=\"/estimate/result/detail.php?strSessionID=" . $aryData["strSessionID"] . "&lngEstimateNo=" . $lngEstimateNo . "\" target=_blank>" . $objResult->strworkflowname . "</a></td>";
			}
		}
		//
		// 見積原価・ワークフローの場合
		//
		elseif( $objResult->lngfunctioncode == DEF_FUNCTION_E0 )
		{
			// 見積原価情報内容のウィンドウを開く処理
			$partsData["strWorkflowName"]   = "<td><a class=wfA href=\"/estimate/result/detail.php?strSessionID=" . $aryData["strSessionID"] . "&lngEstimateNo=" . $objResult->strworkflowkeycode . "\" target=_blank>" . $objResult->strworkflowname . "</a></td>";
		}

		//
		// 上記、発注（見積原価・併用）、見積原価、に該当しない、他のワークフローの場合
		//
		if( empty($partsData["strWorkflowName"]) )
		{
			$partsData["strWorkflowName"]   = "<td onClick=\"javascript:fncShowDialogCommon('".$aryFunctionLink[$objResult->lngfunctioncode]."?strSessionID=" . $aryData["strSessionID"] . "&".$aryWorkflowKeyName[$objResult->lngfunctioncode]."=" . $objResult->strworkflowkeycode . "' , window.form1 , 'ResultIframeCommon' , 'YES' , " . $_COOKIE["lngLanguageCode"] . " , 'detail' );\"><a href=# class=wfA>" . $objResult->strworkflowname . "</a></td>";
		}
	*/
	
		// 案件情報（各ワークフロー状態から生成）
		$partsData["strWorkflowName"] = fncGetWorkflowNameLink( $objDB, $objResult, $aryData["strSessionID"]);

		// 申請者
		if ( $aryData["lngApplicantUserDisplayCodeVisible"] )
		{
			$partsData["strApplicantName"] = "<td nowrap>" . $objResult->strapplicantname . "</td>";
		}
		// 入力者
		if ( $aryData["lngInputUserDisplayCodeVisible"] )
		{
			$partsData["strInputName"]     = "<td nowrap>" . $objResult->strinputname . "</td>";
		}
		// 承認者
		if ( $aryData["lngInChargeCodeVisible"] )
		{
			$partsData["strRecognitionName"]      = "<td nowrap>" . $objResult->strrecognitionname . "</td>";
		}
		// 期限
		$partsData["dtmLimitDate"]  = "<td nowrap>" . $objResult->dtmlimitdate . "</td>";

		// 完了期日
		if ( $aryData["dtmEndDateVisible"] )
		{
			$partsData["dtmEndDate"]    = "<td nowrap>" . $objResult->dtmenddate . "</td>";
		}
		// 状態
		if ( $aryData["lngWorkflowStatusCodeVisible"] )
		{
			$partsData["status"]        = "<td id=\"W0_%statusCode%_\" nowrap>" . $aryWorkflowStatus[$objResult->tstatuscode] . "</td>";
		}

		// 処理URL、処理ボタンの可視、不可視フラグ初期化
		$bytTransactionFlag = 0;

		// 処理URL、処理ボタンの可視、不可視設定
		// 「申請中」かつ承認者または入力者の場合表示
		if ( $objResult->tstatuscode == DEF_STATUS_ORDER && ( $objResult->lnginchargecode == $objAuth->UserCode || $objResult->lnginputusercode == $objAuth->UserCode ) )
		{
			$bytTransactionFlag = 1;

		}
		elseif ( $objResult->tstatuscode == DEF_STATUS_ORDER && count ( $aryWorkflowOrderCode ) )
		{
			// ログインユーザーのワークフロー順番番号が
			// 表示する案件の番号より小さい場合
			for ( $j = 0; $j < count ( $aryWorkflowOrderCode ); $j++ )
			{
				if ( $aryWorkflowOrderCode[$j] == $objResult->lngworkflowordercode && $aryWorkflowOrderNo[$j] < $objResult->lngworkfloworderno )
				{
					$bytTransactionFlag = 1;
					break;
				}
			}
		}

		if ( $bytTransactionFlag )
		{
			$partsData["edit_visibility"] = "visible";
			$partsData["edit"]            = "/wf/regist/edit.php?$strURL&lngActionFunctionCode=" . DEF_FUNCTION_WF6 . "&lngWorkflowCode=" . $objResult->lngworkflowcode;
		}
		else
		{
			$partsData["edit_visibility"] = "hidden";
			$partsData["edit"]            = "";
		}

		// 承認期限切れ処理(文字の色を変える)
		$partsData["limitcolor"] = "";

		if ( $objResult->lnglimitdate < 0 )
		{
			$partsData["limitcolor"] = " style=\"color:#ff0000;\"";
		}

		// テンプレートをコピーし、データ連想配列のキーを配列に取得
		$strParts = $strPartsTemplate;
		$partsDataKeys = array_keys( $partsData );

		// カラムの数だけ置き換え
		foreach ( $partsDataKeys as $key )
		{
			$strParts = preg_replace ( "/_%" . $key . "%_/", "$partsData[$key]", $strParts );
		}


		// パーツテンプレート生成
		$baseData["tabledata"] .= $strParts;
	}

	$objDB->freeResult( $lngResultID );

	$baseData["lngLanguageCode"] = $_COOKIE["lngLanguageCode"];
	// ベーステンプレート読み込み
	$objTemplate->getTemplate( "wf/result/base.tmpl" );

	$baseData["HIDDEN"] = getArrayTable( $aryData, "HIDDEN" );

	// ベーステンプレート生成
	$objTemplate->replace( $baseData );
	$objTemplate->complete();

	// HTML出力
	echo $objTemplate->strTemplate;

	$objDB->close();


	return TRUE;
?>
