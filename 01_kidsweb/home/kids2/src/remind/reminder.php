<?php
/** 
*	パスワードリマインダー　関数群
*
*	@package   kuwagata
*	@license   http://www.wiseknot.co.jp/ 
*	@copyright Copyright &copy; 2003, Wiseknot 
*	@author    Keiji Suzukaze <k-suzukaze@wiseknot.co.jp> 
*	@access    public
*	@version   1.00
*
*	更新履歴
*	2004.02.26	ユーザーマスタ更新時に、選択行をロック状態にて更新するように変更
*
*/

/**
* ユーザー情報取得
*
*	メールアドレスに対する、ユーザー情報の取得
*
*	@param  String $strMailAddress 	メールアドレス
*	@param  Object $objDB       DBオブジェクト
*	@return Array or Boolean $aryData 成功 FALSE 失敗
*	@access public
*/
function getMailAddressToInfo( $strMailAddress, $objDB )
{
	$strQuery  = "SELECT lngUserCode, strUserID, strUserDisplayName, bytMailTransmitFlag, bytInvalidFlag";
	$strQuery .= " FROM m_User";
	$strQuery .= " WHERE strMailAddress = '" . $strMailAddress . "'";

	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	// 取得値を各プロパティーにセット
	if ( $lngResultNum == 1 )
	{
		$objResult = $objDB->fetchObject( $lngResultID, 0 );
		$aryData["lngUserCode"] = $objResult->lngusercode;
		$aryData["strUserID"] = $objResult->struserid;
		$aryData["strUserDisplayName"] = $objResult->struserdisplayname;
		$aryData["strMailAddress"] = $strMailAddress;
		if ( $objResult->bytmailtransmitflag == 't' )
		{
			$aryData["bytMailTransmitFlag"] = 1;
		}
		else
		{
			$aryData["bytMailTransmitFlag"] = 0;
		}
		if ( $objResult->bytinvalidflag == 't' )
		{
			$aryData["bytInvalidFlag"] = 1;
		}
		else
		{
			$aryData["bytInvalidFlag"] = 0;
		}
	}
	else
	{
		return FALSE;
	}

	if ( !$objDB->freeResult( $lngResultID ) )
	{
		return FALSE;
	}
	return $aryData;
}



/**
* パスワード情報変更
*
*	対象ユーザーのパスワード情報の変更関数
*
*	@param  String $lngUserCode 	ユーザーコード
*	@param  String $strPassword     変更パスワード
*	@param  Object $objDB       DBオブジェクト
*	@return Boolean TRUE 成功 FALSE 失敗
*	@access public
*/
function setNewPassword( $lngUserCode, $strPassword, $objDB )
{
// 2004.02.26 suzukaze update start
	// トランザクション開始
	$objDB->transactionBegin();

	// ユーザーマスタ内の更新対象行をロック状態にする
	$strQuery = "SELECT lngUserCode FROM m_User WHERE lngUserCode = " . $lngUserCode . " FOR UPDATE ";
	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
 	if ( !$lngResultNum )
	{
		return FALSE;
	}
	$objDB->freeResult( $lngResultID );

	$strQuery = "UPDATE m_User set strPasswordHash = '" . md5( $strPassword ) . "'";
	$strQuery .= " WHERE lngUserCode = $lngUserCode ";

	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	$objDB->freeResult( $lngResultID );

	// コミット処理
	$objDB->transactionCommit();

	return TRUE;
// 2004.02.26 suzukaze update end
}




// ---------------------------------------------------------------
/**
* IPアドレスチェック（単純なIPアドレスのみのチェック）
*
*	アクセスしているユーザーのＩＰが許可されているＩＰかどうかのチェック
*
*	@param  object  $objDB        DBオブジェクト
*	@param  Object  $objAuth      認証オブジェクト
*	@return boolean TRUE,FALSE
*	@access public
*/
// ---------------------------------------------------------------
function checkAccessIPSimple( $objDB, $objAuth )
{
	// アクセスIPアドレステーブルに問い合わせ
	$strQuery = "SELECT ip.strAccessIPAddress " .
	            "FROM m_AccessIPAddress ip ";

	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	if ( !$lngResultNum = pg_Num_Rows ( $lngResultID ) )
	{
		if ( !$objDB->freeResult( $lngResultID ) )
		{
			return FALSE;
		}
		return FALSE;
	}

	// 許可IP取得
	$objResult = $objDB->fetchObject( $lngResultID, 0 );
	$aryAccessIP = mb_split ( ",", $objResult->straccessipaddress );

	if ( !$objDB->freeResult( $lngResultID ) )
	{
		return FALSE;
	}

	// IPの照合
	foreach ( $aryAccessIP as $strAccessIP )
	{
		$strAccessIP = mb_ereg_replace ( "\.", "\.", $strAccessIP );
		$strAccessIP = mb_ereg_replace ( "\*", ".+?", $strAccessIP );
		if ( mb_ereg ( $strAccessIP, $objAuth->AccessIP ) )
		{
			return TRUE;
		}
	}
	return FALSE;
}



// ---------------------------------------------------------------
/**
* セッション情報の確認
*
*	セッションテーブルより対象セッションＩＤのチェック関数
*
*	@param  string  $strSessionID セッションID
*	@param  object  $objDB        DBオブジェクト
*	@return object  $aryData      ユーザー情報
*			boolean FALSE         セッション情報異常
*	@access public
*/
// ---------------------------------------------------------------
function getSessionIDToInfo( $strSessionID, $objDB )
{
	if ( !$strSessionID )
	{
		return FALSE;
	}

	// ログインセッション管理テーブルに問い合わせ
	// セッション保持の確認とID、パスワードの取得
	$strQuery = "SELECT strLoginUserID, strLoginPassword," .
	            " dtmLoginTime - now() + ( interval '" . REMINDER_LIMIT . " min' ) AS remaining " .
	            "FROM t_LoginSession " .
	            "WHERE strSessionID LIKE '$strSessionID'" .
	            " AND bytSuccessfulFlag = true";

	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	if ( !$lngResultNum )
	{
		return FALSE;
	}

	// セッションを保持するユーザーのIDとパスワードを取得
	$objResult = $objDB->fetchObject( $lngResultID, 0 );

	if ( preg_replace ( "-", $objResult->remaining ) )
	{
		// タイムアウト処理
		$strQuery = "UPDATE t_LoginSession " .
		            "SET bytSuccessfulFlag = false " .
		            "WHERE strSessionID = '$strSessionID'";

		list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

		if ( !$objDB->freeResult( $lngResultID ) )
		{
			return FALSE;
		}
		return FALSE;
	}

	if ( !$objDB->freeResult( $lngResultID ) )
	{
		return FALSE;
	}

	// マッチするID、パスワードをもつユーザーを検索
	$strQuery = "SELECT lngUserCode FROM m_User " .
	            "WHERE strUserID = '" . $objResult->strloginuserid . "'";

	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	// 取得値を戻り値にセット
	if ( $lngResultNum = pg_Num_Rows ( $lngResultID ) )
	{
		$objResult = $objDB->fetchObject( $lngResultID, 0 );
		$aryData["strSessionID"] = $strSessionID;
		$aryData["lngUserCode"]     = $objResult->lngusercode;
	}
	else
	{
		return FALSE;
	}
	if ( !$objDB->freeResult( $lngResultID ) )
	{
		return FALSE;
	}

	return $aryData;
}




// ---------------------------------------------------------------
/**
* リマインダー用セッション情報の無効化処理
*
*	パスワードリマインダー用セッションテーブルの無効化処理
*
*	@param  string  $strSessionID セッションID
*	@param  object  $objDB        DBオブジェクト
*	@return boolean TRUE          セッション無効化成功
*	                FALSE         セッション無効化失敗
*	@access public
*/
// ---------------------------------------------------------------
function setSessionOff( $strSessionID, $objDB )
{
	if ( !$strSessionID )
	{
		return FALSE;
	}

	// 無効化処理
	$strQuery = "UPDATE t_LoginSession " .
	            "SET bytSuccessfulFlag = false " .
	            "WHERE strSessionID = '$strSessionID'";

	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	if ( !$objDB->freeResult( $lngResultID ) )
	{
		return FALSE;
	}
	return TRUE;
}



return TRUE;
?>
