<?php

// ----------------------------------------------------------------------------
/**
*       受注管理  検索
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

	// ライブラリ読み込み
	require (LIB_FILE);
	require (LIB_ROOT . "clscache.php" );
	require (SRC_ROOT . "so/cmn/lib_sos.php");
	require (SRC_ROOT . "so/cmn/column.php");
	require (LIB_DEBUGFILE);

	// DB接続
	$objDB   = new clsDB();
	$objAuth = new clsAuth();
	$objCache = new clsCache();
	$objDB->open( "", "", "", "" );

	//////////////////////////////////////////////////////////////////////////
	// POST(一部GET)データ取得
	//////////////////////////////////////////////////////////////////////////
	$aryData = $_REQUEST;


	// 検索表示項目取得
	// 表示項目  $aryViewColumnに格納
	if( is_array( $_POST["ViewColumn"] ) )
	{
		while ( list( $strKeys, $strValues ) = each( $_POST["ViewColumn"] ) )
		{
			$strValues =  preg_replace("/(.+?)(Visible|Conditions)$/", "\\1", $strValues );
			$aryViewColumn[$strKeys] = $strValues;
		}
	}
	else
	{
		$strMessage = fncOutputError( 9058, DEF_WARNING, "" ,FALSE, "../so/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );

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

	// 検索条件項目取得
	// 検索条件 $arySearchColumnに格納
	if( is_array ( $aryData["SearchColumn"] ) )
	{
		while ( list ($strKeys, $strValues ) = each ( $aryData["SearchColumn"] ))
		{
			$strValues =  preg_replace("/(.+?)(Visible|Conditions)$/", "\\1", $strValues );
			$arySearchColumn[$strKeys] = $strValues;
		}
	}
	else
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
	// 402 受注管理（受注検索）
	if ( !fncCheckAuthority( DEF_FUNCTION_SO2, $objAuth ) )
	{
		fncOutputError ( 9060, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
	}

	//////////////////////////////////////////////////////////////////////////
	// 文字列チェック
	//////////////////////////////////////////////////////////////////////////
	$aryCheck["strSessionID"]			= "null:numenglish(32,32)";
	$aryCheck["dtmInsertDateFrom"] 		= "date(/)";
	$aryCheck["dtmInsertDateTo"]		= "date(/)";
	$aryCheck["dtmReceiveAppDateFrom"] 	= "date(/)";
	$aryCheck["dtmReceiveAppDateTo"]	= "date(/)";
	$aryCheck["strReceiveCodeFrom"]		= "ascii(0,10)";
	$aryCheck["strReceiveCodeTo"]		= "ascii(0,10)";
	$aryCheck["lngInputUserCode"]		= "numenglish(0,3)";
	$aryCheck["strInputUserName"]		= "length(0,50)";
	$aryCheck["lngCustomerCode"]		= "numenglish(0,4)";
	$aryCheck["strCustomerName"]		= "length(0,50)";
	$aryCheck["lngInChargeGroupCode"]	= "numenglish(0,2)";
	$aryCheck["strInChargeGroupName"]	= "length(0,50)";
	$aryCheck["lngInChargeUserCode"]	= "numenglish(0,3)";
	$aryCheck["strInChargeUserName"]	= "length(0,50)";
	// 2004.04.14 suzukaze update start
//	$aryCheck["lngReceiveStatusCode"]	= "length(0,50)";
	// 2004.04.14 suzukaze update end
	$aryCheck["strProductCode"]			= "numenglish(0,5)";
	$aryCheck["strProductName"]			= "length(0,100)";
	// 2004.10.20 suzukaze update start
//	$aryCheck["lngSalesClassCode"]		= "number(0,100)";
	// 2004.10.20 suzukaze update end
	$aryCheck["dtmDeliveryDateFrom"] 	= "date(/)";
	$aryCheck["dtmDeliveryDateTo"]		= "date(/)";

	// 文字列チェック
	$aryCheckResult = fncAllCheck( $aryData, $aryCheck );
	fncPutStringCheckError( $aryCheckResult, $objDB );

	// 402 受注管理（受注検索）
	if ( !fncCheckAuthority( DEF_FUNCTION_PO2, $objAuth ) )
	{
		fncOutputError ( 9052, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
	}

	// 403 受注管理（受注検索　管理モード）
	if ( fncCheckAuthority( DEF_FUNCTION_SO3, $objAuth ) and isset( $aryData["Admin"]) )
	{
		$aryUserAuthority["Admin"] = 1;		// 403 管理モードでの検索
	}
	// 404 受注管理（詳細表示）
	if ( fncCheckAuthority( DEF_FUNCTION_SO4, $objAuth ) )
	{
		$aryUserAuthority["Detail"] = 1;	// 404 詳細表示
	}
	// 405 受注管理（修正）
	if ( fncCheckAuthority( DEF_FUNCTION_SO5, $objAuth ) )
	{
		$aryUserAuthority["Fix"] = 1;		// 405 修正
	}
	// 406 受注管理（削除）
	if ( fncCheckAuthority( DEF_FUNCTION_SO6, $objAuth ) )
	{
		$aryUserAuthority["Delete"] = 1;	// 406 削除
	}
	// 407 受注管理（無効化）
	if ( fncCheckAuthority( DEF_FUNCTION_SO7, $objAuth ) and isset( $aryData["Admin"]) )
	{
		$aryUserAuthority["Invalid"] = 1;	// 407 無効化
	}

	// クッキー取得
	$aryData["lngLanguageCode"] = $_COOKIE["lngLanguageCode"];

	reset($aryViewColumn);
	if ( !$bytSearchFlag )
	{
		reset($arySearchColumn);
	}
	reset($aryData);




	// 検索条件に一致する受注コードを取得するSQL文の作成
	$strQuery = fncGetSearchReceiveSQL( $aryViewColumn, $arySearchColumn, $aryData, $objDB, "", 0, FALSE );

	//fncDebug( 'lib_sos.txt', $strQuery, __FILE__, __LINE__);




	// echo "<br><br>strQuery: <BR>";
	// var_dump( $strQuery );
	// echo "<BR>aryViewColumn: <BR>";
	// var_dump( $aryViewColumn );
	// echo "<BR>arySearchColumn: <BR>";
	// var_dump( $arySearchColumn );
	// echo "<BR>aryData: <BR>";
	// var_dump( $aryData );
	// exit;

	// 値をとる =====================================
	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	if ( $lngResultNum )
	{
		// 検索件数が指定数以上の場合エラーメッセージを表示する
		if ( $lngResultNum > DEF_SEARCH_MAX )
		{
			$strMessage = fncOutputError( 9057, DEF_WARNING, DEF_SEARCH_MAX ,FALSE, "../so/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );

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
		$strMessage = fncOutputError( 403, DEF_WARNING, "" ,FALSE, "../so/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );

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

	// テーブル構成で検索結果を取得、ＨＴＭＬ形式で出力する
	$aryHtml["strHtml"] = fncSetReceiveTable ( $aryResult, $aryViewColumn, $aryData, $aryUserAuthority, $aryTytle, $objDB, $objCache, $aryTableViewName );



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

	$aryHidden[] = "<input type='hidden' name='strSort'>";
	$aryHidden[] = "<input type='hidden' name='strSortOrder'>";
	$strHidden = implode ("\n", $aryHidden );

	$aryHtml["strHidden"] = $strHidden;

	// テンプレート読み込み
	$objTemplate = new clsTemplate();
	$objTemplate->getTemplate( "/so/result/parts.tmpl" );

	// テンプレート生成
	$objTemplate->replace( $aryHtml );
	$objTemplate->complete();

	// HTML出力
	echo $objTemplate->strTemplate;

	$objCache->Release();

	return true;

?>
