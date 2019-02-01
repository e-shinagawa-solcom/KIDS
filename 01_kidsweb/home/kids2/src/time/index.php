<?php

// ----------------------------------------------------------------------------
/**
*       タイムアウト  更新
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
*         ・タイムアウト更新管理
*
*       更新履歴
*
*/
// ----------------------------------------------------------------------------



	//-------------------------------------------------------------------------
	// ■ ライブラリファイル読込
	//-------------------------------------------------------------------------
	include( 'conf.inc' ); // 設定ファイル
	require( LIB_FILE );   // クラスライブラリファイル


	//-------------------------------------------------------------------------
	// ■ オブジェクト生成
	//-------------------------------------------------------------------------
	$objDB       = new clsDB();       // DBオブジェクト
	$objAuth     = new clsAuth();     // 認証処理オブジェクト
	$objTemplate = new clsTemplate(); // テンプレートオブジェクト


	//-------------------------------------------------------------------------
	// ■ パラメータ取得
	//-------------------------------------------------------------------------
	$aryData["strSessionID"]              = $_REQUEST["strSessionID"]; // セッションID
	$aryData["strProcMode"]               = $_REQUEST["strProcMode"];  // 処理モード
	$aryData["strTimeLimitRemainSeconds"] = LIMIT_REMAIN_SECONDS;      // タイムリミット警告秒


	//-------------------------------------------------------------------------
	// ■ DBオープン
	//-------------------------------------------------------------------------
	$objDB->open( "", "", "", "" );


	//-------------------------------------------------------------------------
	// ■ セッション更新
	//-------------------------------------------------------------------------
	$blnCheck = true;

	switch( $aryData["strProcMode"] )
	{
		case "init":

			// ログイン状態の設定
			$aryData["strLoginStatus"] = "valid";
			break;

		case "update":

			// アクセス日時の更新
			$blnCheck = $objAuth->isLogin( $aryData["strSessionID"], $objDB );

			// ログイン状態の設定
			$aryData["strLoginStatus"] = ( $blnCheck ) ? "valid" : "invalid";
			break;

		default:
			break;
	}


	//-------------------------------------------------------------------------
	// ■ セッション確認
	//-------------------------------------------------------------------------
	$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

	// タイムアウト日付の取得
	$aryData["strTimeLimtDate"] = $objAuth->TimeLimtDate;

	// 処理モードの初期化
	$aryData["strProcMode"] = 'init';


	//-------------------------------------------------------------------------
	// ■ DBクローズ
	//-------------------------------------------------------------------------
	$objDB->close();
	$objDB->freeResult( $lngResultID );


	//-------------------------------------------------------------------------
	// ■ 出力
	//-------------------------------------------------------------------------
	$objTemplate->getTemplate( "/time/parts.tmpl" );

	$objTemplate->replace( $aryData );
	$objTemplate->complete();
	echo $objTemplate->strTemplate;


	return true;

?>
