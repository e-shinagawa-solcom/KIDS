<?php

// ----------------------------------------------------------------------------
/**
*       LC管理  機能選択画面
*
*       処理概要
*         ・メニュー画面にてLC管理ボタンを直接押下した際に機能選択画面を表示する
*
*/
// ----------------------------------------------------------------------------

	// 読み込み
	include('conf.inc');
	//共通ファイル読み込み
	require SRC_ROOT . "lc/lcModel/lcModelCommon.php";
	require (LIB_FILE);


	$objDB		= new clsDB();
	$objAuth	= new clsAuth();

	$objDB->open("", "", "", "");


	//-------------------------------------------------------------------------
	// ■ パラメータ取得
	//-------------------------------------------------------------------------
	$aryData = $_POST;

	// 文字列チェック
	$aryCheck["strSessionID"]          = "null:numenglish(32,32)";
	$aryResult = fncAllCheck( $aryData, $aryCheck );
	fncPutStringCheckError( $aryResult, $objDB );

	// セッション確認
	$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );
	// クッキーにセッションIDをセット
	setcookie("strSessionID",$aryData["strSessionID"]);

	//ユーザーID取得(半角スペースがあるため)
	$user_id = trim($objAuth->UserID);
	
	$objDB->close();
	
	//経理サブシステムDB接続
	$lcModel		= new lcModel();

	//ログイン状況判定処理
	$logined_flg = false;
	$login_state = $lcModel->getLoginState($user_id);
	if($login_state["login_state"] == "1"){
		//ログアウト処理を行う
		$lcModel->loginStateLogout($login_state["login_obj"]);
	} else if($login_state["login_state"] == "2"){
		//同一権限者がログインしている
		//lginymd < 現在日付の場合
		$ymd = date('Ymd',  strtotime($lcInfoDate["lcgetdate"]));
		if($ymd < time()){
			//ログイン中アラート表示フラグ
			$logined_flg = true;
		}
	}

	//ログイン状況の最大管理番号の取得
	$login_max_num = $lcModel->getMaxLoginStateNum();

	//ログイン状況の登録
	$lcModel->setLcLoginState($login_max_num, $objAuth->UserFullName);

	//LC情報取得日の取得
	$lcgetdate = $lcModel->getLcInfoDate();

	//ユーザー権限の取得
	$login_user_auth = $lcModel->getUserAuth($user_id);

	
	$lcModel->close();

	//HTMLへの引き渡しデータ
	$aryData["lc_info_date"] = date('Ymd',  strtotime($lcgetdate));
	$aryData["lc_info_time"] = date('h:m:s',  strtotime($lcgetdate));
	$aryData["user_nm"] = $lcInfoDate["lgusrname"];
	$aryData["session_id"] = $aryData["strSessionID"];

	echo fncGetReplacedHtmlWithBase("lc/base_lc.html", "lc/select-function/parts.tmpl", $aryData ,$objAuth );

	//初期処理実行
	//jsへの引き渡しデータ
	$arr = array(
		"login_state" => $login_state,
		"session_id" => $aryData["strSessionID"],
		"lcInfoDate" => $lcInfoDate,
		"logined_flg" => $logined_flg,
		"login_user_auth"=> $login_user_auth
	);
	mb_convert_variables('UTF-8' , 'EUC-JP' , $arr );
	echo "<script>$(function(){lcInit('". json_encode($arr) ."');});</script>";

	return true;
?>