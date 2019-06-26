<?php

// ----------------------------------------------------------------------------
/**
*       商品管理  検索
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
	require (SRC_ROOT . "p/cmn/lib_ps.php");
	require (SRC_ROOT . "p/cmn/column.php");
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
	$isDisplay=array_keys($isDisplay);
	$isSearch=array_keys($isSearch);
	$aryData['ViewColumn']=$isDisplay;
	$aryData['SearchColumn']=$isSearch;
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

	// ログインユーザーコードの取得
	$lngInputUserCode = $objAuth->UserCode;



	// 権限確認
	// 302 商品管理（商品検索）
	if ( !fncCheckAuthority( DEF_FUNCTION_PO2, $objAuth ) )
	{
		fncOutputError ( 9018, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
	}

	//////////////////////////////////////////////////////////////////////////
	// 文字列チェック
	//////////////////////////////////////////////////////////////////////////
	$aryCheck["strSessionID"]					= "null:numenglish(32,32)";
	$aryCheck["dtmInsertDateFrom"] 				= "date(/)";
	$aryCheck["dtmInsertDateTo"]				= "date(/)";
	$aryCheck["lngGoodsPlanProgressCode"]		= "number(0,2)";
	$aryCheck["dtmRevisionDateFrom"] 			= "date(/)";
	$aryCheck["dtmRevisionDateTo"]				= "date(/)";
	$aryCheck["strProductCode"]					= "ascii(0,10)";

	////////  文字列チェック実施するように！！！未実施・・・ /////////
	// 文字列チェック
	// $aryCheckResult = fncAllCheck( $aryData, $aryCheck );
	// fncPutStringCheckError( $aryCheckResult, $objDB );

	// 302 商品管理（商品検索）
	if ( !fncCheckAuthority( DEF_FUNCTION_P2, $objAuth ) )
	{
		fncOutputError ( 9052, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
	}
	// 303 商品管理（商品検索　削除データの表示）
	if ( fncCheckAuthority( DEF_FUNCTION_P3, $objAuth ) )
	{
		$aryUserAuthority["SearchDelete"] = 1;
	}
	// 304 商品管理（詳細表示）
	if ( fncCheckAuthority( DEF_FUNCTION_P4, $objAuth ) )
	{
		$aryUserAuthority["Detail"] = 1;
	}
	// 305 商品管理（詳細表示　削除データの表示）
	if ( fncCheckAuthority( DEF_FUNCTION_P5, $objAuth ) )
	{
		$aryUserAuthority["DetailDelete"] = 1;
	}
	// 306 商品管理（修正）
	if ( fncCheckAuthority( DEF_FUNCTION_P6, $objAuth ) )
	{
		$aryUserAuthority["Fix"] = 1;
	}
	// 307 商品管理（削除）
	if ( fncCheckAuthority( DEF_FUNCTION_P7, $objAuth ) )
	{
		$aryUserAuthority["Delete"] = 1;
	}
	
	// 表示項目  $aryViewColumnに格納
	$aryViewColumn=$isDisplay;
	
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

	// 検索条件に一致する商品コードを取得するSQL文の作成
	$strQuery = fncGetSearchProductSQL( $aryViewColumn, $arySearchColumn, $aryData, $objDB, $aryUserAuthority );
	// 値をとる =====================================
	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	if ( $lngResultNum )
	{
		// 検索件数が指定数以上の場合エラーメッセージを表示する
		if ( $lngResultNum > DEF_SEARCH_MAX )
		{
			$strMessage = fncOutputError( 9057, DEF_WARNING, DEF_SEARCH_MAX ,FALSE, "../p/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );

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
		for ( $i = 0; $i < $lngResultNum; $i++ )
		{
			$aryResult[] = $objDB->fetchArray( $lngResultID, $i );
		}
	}
	else
	{
		$strMessage = fncOutputError( 303, DEF_WARNING, "" ,FALSE, "../p/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );

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
		$aryTytle = $aryTableTytle;
	}
	else
	{
		$aryTytle = $aryTableTytleEng;
	}

	// テーブル構成で検索結果を取得、ＨＴＭＬ形式で出力する
	$aryHtml["strHtml"] = fncSetProductTable ( $aryResult, $aryViewColumn, $aryData, $aryUserAuthority, $aryTytle, $objDB, $objCache, $aryTableViewName );



	// POSTされたデータをHiddenにて設定する
	unset($ary_keys);
	$ary_Keys = array_keys( $aryData );
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
			$aryHidden[] = "<input type='hidden' name='". $strValues."' value='".$aryData[$strValues]."'>";
		}
	}

	$aryHidden[] = "<input type='hidden' name='strSort'>";
	$aryHidden[] = "<input type='hidden' name='strSortOrder'>";
	$strHidden = implode ("\n", $aryHidden );

	$aryHtml["strHidden"] = $strHidden;

	// テンプレート読み込み
	$objTemplate = new clsTemplate();
	$objTemplate->getTemplate( "/p/result/p_search_result.html" );

	// テンプレート生成
	$objTemplate->replace( $aryHtml );
	$objTemplate->complete();

	// HTML出力
	echo $objTemplate->strTemplate;

	$objCache->Release();

	return true;

?>
