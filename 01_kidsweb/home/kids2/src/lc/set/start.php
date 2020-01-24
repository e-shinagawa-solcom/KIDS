<?php

// ----------------------------------------------------------------------------
/**
*       LC管理  LC情報開始
*       initLcInfoを実行させるだけの空の画面です。
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
	$aryData = $_REQUEST;
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

// select-function/index.phpのログイン状況操作と同等の処理
	//経理サブシステムDB接続
	$lcModel		= new lcModel();

	//LC情報取得日の取得
	$lcgetdate = $lcModel->getLcInfoDate();

	//ログイン状況判定処理
	$logined_flg = false;
	$login_state = $lcModel->getLoginState($user_id);
	if($login_state["login_state"] == "1"){
		//ログアウト処理を行う
		$lcModel->loginStateLogout($login_state["login_obj"]);
	} else if($login_state["login_state"] == "2"){
		//同一権限者がログインしている
		//lginymd < 現在日付の場合
		$ymd = date('Ymd',  strtotime($lcgetdate->lcgetdate));
		if($ymd < time()){
			//ログイン中アラート表示フラグ
			$logined_flg = true;
		}
	}

	//ログイン状況の最大管理番号の取得
	$login_max_num = $lcModel->getMaxLoginStateNum();

	//ログイン状況の登録
	$lcModel->setLcLoginState($login_max_num, $objAuth->UserFullName);


	//ユーザー権限の取得
	$login_user_auth = $lcModel->getUserAuth($user_id);

	
	$lcModel->close();

	//HTMLへの引き渡しデータ
	$aryData["lc_info_date"] = date('Ymd',  strtotime($lcgetdate->lcgetdate));
	$aryData["lc_info_time"] = date('h:m:s',  strtotime($lcgetdate->lcgetdate));
	$aryData["user_nm"] = $login_state["lgusrname"];
	$aryData["session_id"] = $aryData["strSessionID"];


// ここまでselect-function/index.phpのログイン状況操作と同等の処理
	
	//HTMLへの引き渡しデータ
	$aryData["session_id"] = $aryData["strSessionID"];

	echo fncGetReplacedHtmlWithBase("lc/base_lc.html", "lc/set/start.tmpl", $aryData ,$objAuth );
	//jsへの引き渡しデータ
	$lcInfoDate = array(
	    "lcgetdate" => $lcgetdate->lcgetdate, 
	    "lgusrname" => $lcgetdate->lgusrname
	);
	$arr = array(
		"login_state" => $login_state,
		"session_id" => $aryData["strSessionID"],
		"lcInfoDate" => $lcInfoDate,
		"logined_flg" => $logined_flg,
		"login_user_auth"=> $login_user_auth
	);
	mb_convert_variables('UTF-8' , 'EUC-JP' , $arr );
	echo "<script>
	    $(function(){lcInit('". json_encode($arr) ."');});
	    document.location.href='/lc/set/index.php?strSessionID=" .$aryData["strSessionID"] . "';</script>";

	return true;
?>