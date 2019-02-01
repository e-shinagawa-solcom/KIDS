<?php

// ----------------------------------------------------------------------------
/**
*       仕入管理  検索
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
	require (SRC_ROOT . "pc/cmn/lib_pcs.php");
	require (SRC_ROOT . "pc/cmn/column.php");
	require (LIB_DEBUGFILE);

	// DB接続
	$objDB   = new clsDB();
	$objAuth = new clsAuth();
	$objCache = new clsCache();
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
		$strMessage = fncOutputError( 9058, DEF_WARNING, "" ,FALSE, "../pc/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );

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
	//	fncOutputError( 702, DEF_WARNING, "検索対象項目がチェックされていません",TRUE, "../pc/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );
		$bytSearchFlag = TRUE;
	}

	//////////////////////////////////////////////////////////////////////////
	// セッション、権限確認
	//////////////////////////////////////////////////////////////////////////
	// セッション確認
	$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

	// 権限確認
	// 702 仕入管理（仕入検索）
	if ( !fncCheckAuthority( DEF_FUNCTION_PC2, $objAuth ) )
	{
		fncOutputError ( 9018, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
	}

	//////////////////////////////////////////////////////////////////////////
	// 文字列チェック
	//////////////////////////////////////////////////////////////////////////
	$aryCheck["strSessionID"]			= "null:numenglish(32,32)";
	$aryCheck["dtmInsertDateFrom"] 		= "date(/)";
	$aryCheck["dtmInsertDateTo"]		= "date(/)";
	$aryCheck["dtmStockAppDateFrom"] 	= "date(/)";
	$aryCheck["dtmStockAppDateTo"]		= "date(/)";
	$aryCheck["strStockCodeFrom"]		= "ascii(0,10)";
	$aryCheck["strStockCodeTo"]			= "ascii(0,10)";
	$aryCheck["strSlipCode"]			= "ascii(0,10)";
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
	//$aryCheck["lngStockStatusCode"]		= "length(0,50)";
	// 2004.04.14 suzukaze update end
	$aryCheck["lngPayConditionCode"]	= "numenglish(0,3)";
	$aryCheck["dtmExpirationDateFrom"] 	= "date(/)";
	$aryCheck["dtmExpirationDateTo"]	= "date(/)";
	$aryCheck["strProductCode"]			= "numenglish(0,5)";
	$aryCheck["strProductName"]			= "length(0,100)";
	$aryCheck["lngStockSubjectCode"]	= "ascii(0,7)";
	$aryCheck["lngStockItemCode"]		= "ascii(0,7)";

	// 文字列チェック
	$aryCheckResult = fncAllCheck( $aryData, $aryCheck );
	fncPutStringCheckError( $aryCheckResult, $objDB );

	// 702 仕入管理（仕入検索）
	if ( !fncCheckAuthority( DEF_FUNCTION_PC2, $objAuth ) )
	{
		fncOutputError ( 9052, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
	}

	// 703 仕入管理（仕入検索　管理モード）
	if ( fncCheckAuthority( DEF_FUNCTION_PC3, $objAuth ) and isset( $aryData["Admin"]) )
	{
		$aryUserAuthority["Admin"] = 1;		// 703 管理モードでの検索
	}
	// 704 仕入管理（詳細表示）
	if ( fncCheckAuthority( DEF_FUNCTION_PC4, $objAuth ) )
	{
		$aryUserAuthority["Detail"] = 1;	// 704 詳細表示
	}
	// 707 仕入管理（修正）
	if ( fncCheckAuthority( DEF_FUNCTION_PC5, $objAuth ) )
	{
		$aryUserAuthority["Fix"] = 1;		// 705 修正
	}
	// 706 仕入管理（削除）
	if ( fncCheckAuthority( DEF_FUNCTION_PC6, $objAuth ) )
	{
		$aryUserAuthority["Delete"] = 1;	// 706 削除
	}
	// 707 仕入管理（無効化）
	if ( fncCheckAuthority( DEF_FUNCTION_PC7, $objAuth ) and isset( $aryData["Admin"]) )
	{
		$aryUserAuthority["Invalid"] = 1;	// 707 無効化
	}

	// クッキー取得
	$aryData["lngLanguageCode"] = $_COOKIE["lngLanguageCode"];

	reset($aryViewColumn);
	if ( !$bytSearchFlag )
	{
		reset($arySearchColumn);
	}
	reset($aryData);

	// 検索条件に一致する仕入コードを取得するSQL文の作成
	$strQuery = fncGetSearchStockSQL( $aryViewColumn, $arySearchColumn, $aryData, $objDB, "", 0, FALSE );


	//fncDebug("pcsearch.txt", $strQuery, __FILE__ , __LINE__ );

	// echo "strQuery: <BR>";
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
			$strMessage = fncOutputError( 9057, DEF_WARNING, DEF_SEARCH_MAX ,FALSE, "../pc/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );

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
		$strMessage = fncOutputError( 703, DEF_WARNING, "" ,FALSE, "../pc/search/index.php?strSessionID=".$aryData["strSessionID"], $objDB );

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
	$aryHtml["strHtml"] = fncSetStockTable ( $aryResult, $aryViewColumn, $aryData, $aryUserAuthority, $aryTytle, $objDB, $objCache, $aryTableViewName );

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
		elseif( $strValues == "strSort" || $strValues == "strSortOrder")
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

	$aryHidden[] = "<input type='hidden' name='strSort' value=''>";
	$aryHidden[] = "<input type='hidden' name='strSortOrder' value=''>";
	$strHidden = implode ("\n", $aryHidden );

	$aryHtml["strHidden"] = $strHidden;

	// テンプレート読み込み
	$objTemplate = new clsTemplate();
	$objTemplate->getTemplate( "/pc/result/parts.tmpl" );

	// テンプレート生成
	$objTemplate->replace( $aryHtml );
	$objTemplate->complete();

	// HTML出力
	echo $objTemplate->strTemplate;

	$objCache->Release();

	return true;

?>
