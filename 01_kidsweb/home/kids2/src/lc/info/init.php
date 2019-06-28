<?php

// ----------------------------------------------------------------------------
/**
*       LC管理  LC情報画面
*/
// ----------------------------------------------------------------------------

	//-------------------------------------------------------------------------
	// ■ ライブラリファイル読込
	//-------------------------------------------------------------------------
	// 読み込み
	include('conf.inc');
	//共通ファイル読み込み
	require_once '../lcModel/lcModelCommon.php';
	require_once '../lcModel/db_common.php';
	require_once '../lcModel/kidscore_common.php';
	require_once '../lcModel/lcinfo.php';
	require (LIB_FILE);
	
	//PHP標準のJSON変換メソッドはエラーになるので外部のライブラリ(恐らくエンコードの問題)
	require_once '../lcModel/JSON.php';

	//-------------------------------------------------------------------------
	// ■ オブジェクト生成
	//-------------------------------------------------------------------------
	$objDB   = new clsDB();
	$objAuth = new clsAuth();
	//LC用DB接続インスタンス生成
    $db			= new lcConnect();

	//-------------------------------------------------------------------------
	// ■ DBオープン
	//-------------------------------------------------------------------------
	$objDB->open("", "", "", "");

	//-------------------------------------------------------------------------
	// ■ パラメータ取得
	//-------------------------------------------------------------------------
	$aryData = $_POST;

	$aryData["strSessionID"]    = $_REQUEST["strSessionID"];   // セッションID
	$aryData["reSearchFlg"]    = $_REQUEST["reSearchFlg"];   // 再検索フラグ

	setcookie("strSessionID", $aryData["strSessionID"], 0, "/");

	// セッション確認
	$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

	//ユーザーID取得(半角スペースがあるため)
	$usrId = trim($objAuth->UserID);

	// // 2100 LC管理
	// if ( !fncCheckAuthority( DEF_FUNCTION_LC0, $objAuth ) )
	// {
	//         fncOutputError ( 9018, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
	// }
	
	// // 2101 LC情報
	// if ( !fncCheckAuthority( DEF_FUNCTION_LC1, $objAuth ) )
	// {
	//         fncOutputError ( 9018, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
	// }


	//経理サブシステムDB接続
	$lcModel		= new lcModel();

	//ユーザー権限の取得
	$loginUserAuth = $lcModel->getUserAuth($usrId);

	$userAuth = substr($loginUserAuth, 1, 1);

	//ログイン状況の最大管理番号の取得
	$maxLgno = $lcModel->getMaxLoginStateNum();

	//排他制御
	$chkEpRes = $lcModel->chkEp($maxLgno, $userAuth, $usrId);

	//ログイン者の有無
	$loginCount = $lcModel->getUserCount();


	//結果配列
	$result = array();
	$result["strSessionID"] = $aryData["strSessionID"];
	$lcGetDate = "";
	$result["userAuth"] = $userAuth;
	$result["loginCount"] = $loginCount;
	$result["maxLgno"] = $maxLgno;
	//t_aclcinfoデータの登録・更新処理
	// ユーザ情報のusrAuthの２桁目の文字が"1" あるいは（ユーザ情報のusrAuthの２桁目の文字が"1"ではない　かつ　ログイン状況件数 =1の場合）
	if ($userAuth == "1" || ($userAuth != "1" && $loginCount == 1)) {
		// lcgetdateを取得する
		$acloginstate = $lcModel->getAcLoginstateBylgno($maxLgno);

		$result["lcgetdate"] = $acloginstate->lcgetdate;
	}

	$objDB->close();

	//JSONクラスインスタンス化
	$s = new Services_JSON();
	//結果出力
	mb_convert_variables('UTF-8' , 'EUC-JP' , $result );
	echo $s->encodeUnsafe($result);
?>