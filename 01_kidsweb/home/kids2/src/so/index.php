<?php

// ----------------------------------------------------------------------------
/**
*       受注管理  メニュー画面
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
*         ・メニュー画面を表示
*
*       更新履歴
*
*/
// ----------------------------------------------------------------------------



	// 読み込み
	include('conf.inc');
	require (LIB_FILE);
	
	
	$objDB		= new clsDB();
	$objAuth	= new clsAuth();
	$objDB->open("", "", "", "");

	$aryData["strSessionID"] = $_POST["strSessionID"];

	
	// 文字列チェック
	$aryCheck["strSessionID"]          = "null:numenglish(32,32)";
	$aryResult = fncAllCheck( $aryData, $aryCheck );
	fncPutStringCheckError( $aryResult, $objDB );
	
	// セッション確認
	$objAuth = fncIsSession( $_POST["strSessionID"], $objAuth, $objDB );


	// 400	受注管理
	if ( !fncCheckAuthority( DEF_FUNCTION_SO0, $objAuth ) )
	{
	        fncOutputError ( 9052, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
	}

	// 401 受注管理（受注登録）
	if ( fncCheckAuthority( DEF_FUNCTION_SO1, $objAuth ) )
	{
		$aryData["strRegistURL"]   = "regist/index.php?strSessionID=" . $aryData["strSessionID"];
	}
	
	// 402 受注管理（受注検索）
	if ( fncCheckAuthority( DEF_FUNCTION_SO2, $objAuth ) )
	{
		$aryData["strSearchURL"]   = "search/index.php?strSessionID=" . $aryData["strSessionID"];
	}
	
	//echo "button : ".$aryData["strRegist"]."<br>";
	//echo "button : ".$aryData["strSearch"]."<br>";

	// ヘルプ対応
	$aryData["lngFunctionCode"] = DEF_FUNCTION_SO0;



	// ユーザーコード取得
	$lngUserCode = $objAuth->UserCode;

	// 権限グループコード(ユーザー以下)チェック
	$blnAG = fncCheckUserAuthorityGroupCode( $lngUserCode, $aryData["strSessionID"], $objDB );

	// 「ユーザー」以下の場合
	if( $blnAG )
	{
		// 承認ルート存在チェック
		$blnWF = fncCheckWorkFlowRoot( $lngUserCode, $aryData["strSessionID"], $objDB );

		// 承認ルートが存在しない場合
		if( !$blnWF )
		{
			$aryData["registview"] = 'hidden';
		}
		else
		{
			$aryData["registview"] = 'visible';
		}
	}



	echo fncGetReplacedHtml( "so/parts.tmpl", $aryData ,$objAuth );
//	echo $_COOKIE["lngLanguageCode"];

	$objDB->close();
	return true;
?>