<?php

// ----------------------------------------------------------------------------
/**
*       発注管理  検索
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
*         ・検索結果画面表示処理
*
*       更新履歴
*
*/
// ----------------------------------------------------------------------------



	// 設定読み込み
	include_once('conf.inc');
	
	require_once(SRC_ROOT.'/mold/lib/UtilSearchForm.class.php');

	// ライブラリ読み込み
	require (LIB_FILE);
	require (LIB_ROOT . "clscache.php" );
	require (SRC_ROOT . "po/cmn/lib_pos.php");
	require (SRC_ROOT . "po/cmn/column.php");
	require (LIB_DEBUGFILE);

	// DB接続
	$objDB   = new clsDB();
	$objAuth = new clsAuth();
	$objCache = new clsCache();
	$objDB->open( "", "", "", "" );

	//////////////////////////////////////////////////////////////////////////
	// POST(一部GET)データ取得
	//////////////////////////////////////////////////////////////////////////
	// フォームデータから各カテゴリの振り分けを行う
	$options = UtilSearchForm::extractArrayByOption($_REQUEST);
	$isDisplay = UtilSearchForm::extractArrayByIsDisplay($_REQUEST);
	$isSearch = UtilSearchForm::extractArrayByIsSearch($_REQUEST);
	$from = UtilSearchForm::extractArrayByFrom($_REQUEST);
	$to = UtilSearchForm::extractArrayByTo($_REQUEST);
	$searchValue = $_REQUEST;
	// $adminMode = $_REQUEST["admin-mode"] ? true : false;
	
	$isDisplay=array_keys($isDisplay);
	$isSearch=array_keys($isSearch);
	$aryData['ViewColumn']=$isDisplay;
	$aryData['SearchColumn']=$isSearch;
	$aryData['Admin'] = $_REQUEST["IsDisplay_btnAdmin"];
	foreach($from as $key=> $item){
		$aryData[$key.'From']=$item;
	}
	foreach($to as $key=> $item){
		$aryData[$key.'To']=$item;
	}
	foreach($searchValue as $key=> $item){
		$aryData[$key]=$item;
	}
	
	
	// 検索表示項目取得
	if(empty($isDisplay))
	{
		$strMessage = fncOutputError( 9058, DEF_WARNING, "" ,FALSE, "../so/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );

		// [lngLanguageCode]書き出し
		$aryHtml["lngLanguageCode"] = 1;

		// [strErrorMessage]書き出し
		$aryHtml["strErrorMessage"] = $strMessage;

		// テンプレート読み込み
		$objTemplate = new clsTemplate();
		$objTemplate->getTemplate( "/result/error/parts.tmpl" );
		
		// テンプレート生成
		$objTemplate->replace( $aryHtml );
		$objTemplate->complete();
		// HTML出力
		echo $objTemplate->strTemplate;

		exit;
	}
	
	// 検索条件項目取得
	// 検索条件 $arySearchColumnに格納
	if( empty ( $isSearch ) )
	{
	//	fncOutputError( 502, DEF_WARNING, "検索対象項目がチェックされていません",TRUE, "../so/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
		$bytSearchFlag = TRUE;
	}

	//////////////////////////////////////////////////////////////////////////
	// セッション、権限確認
	//////////////////////////////////////////////////////////////////////////
	// セッション確認
	$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

	// 権限確認
	// 502 発注管理（発注検索）
	if ( !fncCheckAuthority( DEF_FUNCTION_PO2, $objAuth ) )
	{
		fncOutputError ( 9060, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
	}

	//////////////////////////////////////////////////////////////////////////
	// 文字列チェック
	//////////////////////////////////////////////////////////////////////////
	$aryCheck["strSessionID"]			= "null:numenglish(32,32)";
	$aryCheck["dtmInsertDateFrom"] 		= "date(/)";
	$aryCheck["dtmInsertDateTo"]		= "date(/)";
	$aryCheck["dtmOrderAppDateFrom"] 	= "date(/)";
	$aryCheck["dtmOrderAppDateTo"]		= "date(/)";
	$aryCheck["strOrderCodeFrom"]		= "ascii(0,10)";
	$aryCheck["strOrderCodeTo"]			= "ascii(0,10)";
	$aryCheck["lngInputUserCode"]		= "numenglish(0,3)";
	$aryCheck["strInputUserName"]		= "length(0,50)";
	$aryCheck["lngCustomerCode"]		= "numenglish(0,4)";
	$aryCheck["strCustomerName"]		= "length(0,50)";
	$aryCheck["lngInChargeGroupCode"]	= "numenglish(0,2)";
	$aryCheck["strInChargeGroupName"]	= "length(0,50)";
	$aryCheck["lngInChargeUserCode"]	= "numenglish(0,3)";
	$aryCheck["strInChargeUserName"]	= "length(0,50)";
	// 2004.04.14 suzukaze update start
	//$aryCheck["lngOrderStatusCode"]		= "length(0,50)";
	// 2004.04.14 suzukaze update end
	$aryCheck["lngPayConditionCode"]	= "numenglish(0,3)";
	$aryCheck["dtmExpirationDateFrom"] 	= "date(/)";
	$aryCheck["dtmExpirationDateTo"]	= "date(/)";
	// $aryCheck["strProductCode"]			= "numenglish(0,5)";
	$aryCheck["strProductName"]			= "length(0,100)";
	$aryCheck["lngStockSubjectCode"]	= "ascii(0,7)";
	$aryCheck["lngStockItemCode"]		= "ascii(0,7)";

	// 文字列チェック
	$aryCheckResult = fncAllCheck( $aryData, $aryCheck );
	fncPutStringCheckError( $aryCheckResult, $objDB );

	// 502 発注管理（発注検索）
	if ( !fncCheckAuthority( DEF_FUNCTION_PO2, $objAuth ) )
	{
		fncOutputError ( 9052, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
	}

	// 503 発注管理（発注検索　管理モード）
	if ( fncCheckAuthority( DEF_FUNCTION_PO3, $objAuth ) and isset( $aryData["Admin"]) )
	{
		$aryUserAuthority["Admin"] = 1;		// 503 管理モードでの検索
	}
	// 504 発注管理（詳細表示）
	if ( fncCheckAuthority( DEF_FUNCTION_PO4, $objAuth ) )
	{
		$aryUserAuthority["Detail"] = 1;	// 504 詳細表示
	}
	// 505 発注管理（修正）
	if ( fncCheckAuthority( DEF_FUNCTION_PO5, $objAuth ) )
	{
		$aryUserAuthority["Fix"] = 1;		// 505 修正
	}
	// 506 発注管理（削除）
	if ( fncCheckAuthority( DEF_FUNCTION_PO6, $objAuth ) )
	{
		$aryUserAuthority["Delete"] = 1;	// 506 削除
	}
	
	// 表示項目  $aryViewColumnに格納
	// $aryViewColumn=$isDisplay;
	$aryViewColumn=fncResortSearchColumn($isDisplay);
	// 検索項目  $arySearchColumnに格納
	$arySearchColumn=$isSearch;

	// クッキー取得
	$aryData["lngLanguageCode"] = 1;

	reset($aryViewColumn);
	if ( !$bytSearchFlag )
	{
		reset($arySearchColumn);
	}
	reset($aryData);
	
	// 検索条件に一致する発注コードを取得するSQL文の作成
	$strQuery = fncGetSearchPurchaseSQL( $aryViewColumn, $arySearchColumn, $aryData, $objDB, "", 0, FALSE );
//echo $strQuery;
//fncDebug("kids2.log", $strQuery, __FILE__, __LINE__, "w" );
	// 値をとる =====================================
	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
//	fncDebug("kids2.log", "found " . $lngResultNum . " records", __FILE__, __LINE__, "a" );

	if ( $lngResultNum )
	{
		// 検索件数が指定数以上の場合エラーメッセージを表示する
		if ( $lngResultNum > DEF_SEARCH_MAX )
		{
			$strMessage = fncOutputError( 9057, DEF_WARNING, DEF_SEARCH_MAX ,FALSE, "../po/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );

			// [lngLanguageCode]書き出し
			$aryHtml["lngLanguageCode"] = $aryData["lngLanguageCode"];

			// [strErrorMessage]書き出し
			$aryHtml["strErrorMessage"] = $strMessage;

			// テンプレート読み込み
			$objTemplate = new clsTemplate();
			$objTemplate->getTemplate( "/result/error/parts.tmpl" );
			
			// テンプレート生成
			$objTemplate->replace( $aryHtml );
			$objTemplate->complete();

			// HTML出力
			echo $objTemplate->strTemplate;

			exit;
		}

		// 指定数以内であれば通常処理
//	    fncDebug("kids2.log", "passed" , __FILE__, __LINE__, "a" );
		for ( $i = 0; $i < $lngResultNum; $i++ )
		{
			$aryResult[] = $objDB->fetchArray( $lngResultID, $i );
		}
//	    fncDebug("kids2.log", "passed" , __FILE__, __LINE__, "a" );
	}
	else
	{
		$strMessage = fncOutputError( 503, DEF_WARNING, "" ,FALSE, "../po/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );

		// [lngLanguageCode]書き出し
		$aryHtml["lngLanguageCode"] = $aryData["lngLanguageCode"];

		// [strErrorMessage]書き出し
		$aryHtml["strErrorMessage"] = $strMessage;

		// テンプレート読み込み
		$objTemplate = new clsTemplate();
		$objTemplate->getTemplate( "/result/error/parts.tmpl" );
		
		// テンプレート生成
		$objTemplate->replace( $aryHtml );
		$objTemplate->complete();

		// HTML出力
		echo $objTemplate->strTemplate;

		exit;
	}


	$objDB->freeResult( $lngResultID );

	// 言語の設定
	if ( $aryData["lngLanguageCode"] == 1 )
	{
		$aryTytle = $arySearchTableTytle;
	}
	else
	{
		$aryTytle = $arySearchTableTytleEng;
	}

//	    fncDebug("kids2.log", "passed" , __FILE__, __LINE__, "a" );
	// テーブル構成で検索結果を取得、ＨＴＭＬ形式で出力する
	$aryHtml["strHtml"] = fncSetPurchaseTable ( $aryResult, $arySearchColumn, $aryViewColumn, $aryData, $aryUserAuthority, $aryTytle, $objDB, $objCache, $aryTableViewName );
//	    fncDebug("kids2.log", "passed" , __FILE__, __LINE__, "a" );

	// POSTされたデータをHiddenにて設定する
	unset($ary_keys);
	$ary_Keys = array_keys( $aryData );
//	    fncDebug("kids2.log", "passed" , __FILE__, __LINE__, "a" );
	while ( list ($strKeys, $strValues ) = each ( $ary_Keys ) )
	{
		if( $strValues == "ViewColumn")
		{
			reset( $aryData["ViewColumn"] );
			for ( $i = 0; $i < count( $aryData["ViewColumn"] ); $i++ )
			{
				$aryHidden[] = "<input type='hidden' name='ViewColumn[]' value='" .$aryData["ViewColumn"][$i]. "'>";
			}
		}
		elseif( $strValues == "SearchColumn")
		{
			reset( $aryData["SearchColumn"] );
			for ( $j = 0; $j < count( $aryData["SearchColumn"] ); $j++ )
			{
				$aryHidden[] = "<input type='hidden' name='SearchColumn[]' value='". $aryData["SearchColumn"][$j] ."'>";
			}
		}
		elseif( $strValues == "strSort" || $strValues == "strSortOrder" )
		{
			//何もしない
		} 
		else
		{
			// 配列の値の場合（状態、ワークフロー状態）
			if( is_array($aryData[$strValues]) )
			{
				for($k = 0; $k < count($aryData[$strValues]); $k++ )
				{
					$aryHidden[] = '<input type="hidden" name="'.$strValues.'['.$k.']" value="'. $aryData[$strValues][$k] .'">';
				}
			}
			else
			{
				$aryHidden[] = '<input type="hidden" name="'. $strValues.'" value="'.$aryData[$strValues].'">';
			}
		}
	}
//	    fncDebug("kids2.log", "passed" , __FILE__, __LINE__, "a" );

	$aryHidden[] = "<input type='hidden' name='strSort'>";
	$aryHidden[] = "<input type='hidden' name='strSortOrder'>";
	$strHidden = implode ("\n", $aryHidden );

	$aryHtml["strHidden"] = $strHidden;

	// テンプレート読み込み
	$objTemplate = new clsTemplate();
	$objTemplate->getTemplate( "/po/result/po_search_result.html" );
	
	// テンプレート生成
	$objTemplate->replace( $aryHtml );
	$objTemplate->complete();

	// HTML出力
	echo $objTemplate->strTemplate;

	$objCache->Release();
//    fncDebug("kids2.log", "passed" , __FILE__, __LINE__, "a" );

	return true;

?>
