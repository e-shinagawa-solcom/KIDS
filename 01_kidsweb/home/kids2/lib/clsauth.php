<?php
// ----------------------------------------------------------------------------
/**
*       認証処理クラス
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
*		isLogin                 ログイン状態確認(セッションの確認・更新)
*		login                   ログイン処理
*		logout                  ログアウト処理
*		checkAccessIP           IPアドレスチェック
*
*       更新履歴
*
*/
// ----------------------------------------------------------------------------

class clsAuth
{
	var $SessionID;          // セッションID
	var $AccessIP;           // アクセスIPアドレス
	var $UserCode;           // ユーザーコード
	var $UserDisplayName;    // 表示ユーザー名
	var $UserID;             // ユーザーID
	var $UserFullName;       // フルネーム
//	var $UserDisplayName;    // 表示ユーザー名
	var $GroupDisplayCode;   // 表示グループコード
	var $GroupDisplayName;   // 表示グループ名
	var $AuthorityGroupCode; // 権限グループコード
	var $AuthorityGroupName; // 権限グループ名
	var $FunctionCode ;      // 使用可能な機能コードがキーの連想配列(Boolean)

	var $TimeLimtDate;       // タイムアウト日付

	// ---------------------------------------------------------------
	/**
	*	コンストラクタ
	*	クラス内の初期化を行う
	*	
	*	@return void
	*	@access public
	*/
	// ---------------------------------------------------------------
	function __construct()
	{
		// セッションID
		$this->SessionID = "";

		// アクセスIPアドレス
		$this->AccessIP = $_SERVER["REMOTE_ADDR"];

		// ユーザーコード
		$this->UserCode = 0;

		// 表示ユーザー名
		$this->UserDisplayName = "";

		// ユーザーID
		$this->UserID = "";

		// フルネーム
		$this->UserFullName = "";

		// 表示グループコード
		$this->GroupDisplayCode = "";

		// 表示グループ名
		$this->GroupDisplayName = "";

		// ユーザー画像ファイル
		$this->UserImageFileName = "";

		// 権限グループコード
		$this->AuthorityGroupCode = 0;

		// 権限グループ名
		$this->AuthorityGroupName = "";

		// 権限フラグ
		$this->AuthorityFlag = FALSE;
	}

	// ---------------------------------------------------------------
	/**
	*	ログイン状態にあるかどうかを確認
	*	@param  string  $strSessionID セッションID
	*	@param  object  $objDB        DBオブジェクト
	*	@return boolean TRUE,FALSE
	*	@access public
	*/
	// ---------------------------------------------------------------
	function isLogin( $strSessionID, $objDB )
	{
		if ( !$strSessionID )
		{
			return FALSE;
		}

		// ログインセッション管理テーブルに問い合わせ
		// セッション保持の確認とIDの取得
		$strQuery = "SELECT l.lngUserCode," .
		            " date_trunc('second', l.dtmLoginTime ) AS remaining," .
		            " c.strValue AS timeout," .
		            " ag.lngAuthorityGroupCode, ag.strAuthorityGroupName," .
		            " u.strUserDisplayName, u.strUserID, u.strUserFullName, " .
		            " g.strGroupDisplayCode, g.strGroupDisplayName, " .
		            " u.strUserImageFileName " .
		            "FROM t_LoginSession l, m_CommonFunction c," .
		            " m_User u, m_AuthorityGroup ag," .
		            " m_Group g, m_GroupRelation gr " .
		            "WHERE l.strSessionID LIKE '$strSessionID'" .
		            " AND u.bytinvalidflag        = FALSE" .
		            " AND l.bytSuccessfulFlag     = TRUE" .
		            " AND gr.bytDefaultFlag       = TRUE" .
		            " AND c.strClass              = 'timeout'" .
		            " AND u.lngAuthorityGroupCode = ag.lngAuthorityGroupCode" .
		            " AND u.lngUserCode           = l.lngUserCode" .
		            " AND u.lngUserCode           = gr.lngUserCode" .
		            " AND gr.lngGroupCode         = g.lngGroupCode";
//		            " AND dtmLoginTime > ( now() - ( interval '" . TIMEOUT . " min' ) )" .

		list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

		if ( !$lngResultNum )
		{
			return FALSE;
		}

		// セッションを保持するユーザーのIDを取得
		$objResult = $objDB->fetchObject( $lngResultID, 0 );


		// タイムアウト日時の取得
		$this->TimeLimtDate = date( "Y/m/d/H/i/s", strtotime( "1 hour" ) );


		if ( time() - strtotime ( $objResult->remaining ) > $objResult->timeout * 60 )
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


		// 取得値を各プロパティーにセット
		$objResult = $objDB->fetchObject( $lngResultID, 0 );
		$this->SessionID          = $strSessionID;
		$this->AuthorityGroupCode = $objResult->lngauthoritygroupcode;
		$this->AuthorityGroupName = $objResult->strauthoritygroupname;
		$this->UserCode           = $objResult->lngusercode;
		$this->UserDisplayName    = $objResult->struserdisplayname;
		$this->UserID             = $objResult->struserid;
		$this->UserFullName       = $objResult->struserfullname;
		$this->UserImageFileName  = $objResult->struserimagefilename;
		$this->GroupDisplayCode   = $objResult->strgroupdisplaycode;
		$this->GroupDisplayName   = $objResult->strgroupdisplayname;

		if ( !$objDB->freeResult( $lngResultID ) )
		{
			return FALSE;
		}

		// アクセスIPアドレスチェック
//		if ( !$this->checkAccessIP( $objDB ) )
//		{
//			return FALSE;
//		}

		// アクセス日時の更新
		$strQuery = "UPDATE t_LoginSession " .
		            "SET dtmLoginTime = now() " .
		            "WHERE trim(from strSessionID) = '$strSessionID'";
		list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

		if ( !$objDB->freeResult( $lngResultID ) )
		{
			return FALSE;
		}

		// 仕様可能機能コード取得
		$this->getFunctionAuthority( $objDB );

		return TRUE;
	}

	// ---------------------------------------------------------------
	/**
	*	ログイン処理
	*	@param  string  $strUserID       ユーザーID
	*	@param  string  $strPasswordHash パスワード
	*	@param  object  $objDB           DBオブジェクト
	*	@return boolean TRUE,FALSE
	*	@access public
	*/
	// ---------------------------------------------------------------
	function login( $strUserID, $strPasswordHash, $objDB )
	{
		if ( !$strUserID || !$strPasswordHash )
		{
			return FALSE;
		}

		// マッチするID、パスワードをもつユーザーを検索
		$strQuery = "SELECT u.lngUserCode," .
		            " ag.lngAuthorityGroupCode, ag.strAuthorityGroupName," .
		            " u.strUserDisplayName, u.strUserID, u.strUserFullName, " .
		            " g.strGroupDisplayCode, g.strGroupDisplayName, " .
		            " ip.strAccessIPAddress, u.strUserImageFileName " .
		            "FROM m_User u, m_AuthorityGroup ag," .
		            " m_Group g, m_GroupRelation gr," .
		            " m_AccessIPAddress ip " .
		            "WHERE u.strUserID = '$strUserID'" .
		            " AND u.strPasswordHash = '" . md5 ( $strPasswordHash ) . "'" .
		            " AND u.bytinvalidflag         = FALSE" .
		            " AND gr.bytDefaultFlag        = TRUE" .
		            " AND u.lngAuthorityGroupCode  = ag.lngAuthorityGroupCode" .
		            " AND u.lngAccessIPAddressCode = ip.lngAccessIPAddressCode" .		            " AND u.lngUserCode            = gr.lngUserCode" .
		            " AND gr.lngGroupCode          = g.lngGroupCode";



		list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

		// セッションID作成
		//$browser = get_browser();
		//echo $browser["authenticodeupdate"];
		//exit;
		$strSessionID = md5 ( uniqid ( rand(), 1 ) );

		// 認証チェック
		if ( pg_Num_Rows ( $lngResultID ) )
		{
			$strPasswordHash = md5 ( $strPasswordHash );

			$objResult = $objDB->fetchObject( $lngResultID, 0 );

			// ログインユーザー情報の生成、プロパティーにセット
			$this->SessionID = $strSessionID;
			$this->AuthorityGroupCode = $objResult->lngauthoritygroupcode;
			$this->AuthorityGroupName = $objResult->strauthoritygroupname;
			$this->UserCode           = $objResult->lngusercode;
			$this->UserDisplayName    = $objResult->struserdisplayname;
			$this->UserID             = $objResult->struserid;
			$this->UserFullName       = $objResult->struserfullname;
			$this->UserImageFileName  = $objResult->struserimagefilename;
			$this->GroupDisplayCode   = $objResult->strgroupdisplaycode;
			$this->GroupDisplayName   = $objResult->strgroupdisplayname;

			// アクセスIPアドレスチェック
			if ( $this->checkAccessIP( $objResult->straccessipaddress ) )
			{
				$bytSuccessFlag = "TRUE";
			}
			else
			{
				$bytSuccessFlag = "FALSE";
			}
		}
		else
		{
			$bytSuccessFlag = "FALSE";
		}

		if ( !$objDB->freeResult( $lngResultID ) )
		{
			return FALSE;
		}

		// ログインセッション管理テーブルに書き込み
		$strQuery = "INSERT INTO t_LoginSession VALUES (" .
		            " '$strSessionID', " . $this->UserCode . ", '$strUserID', '$strPasswordHash', now(), '" . $this->AccessIP . "', $bytSuccessFlag )";
		list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

		if ( !$objDB->freeResult( $lngResultID ) )
		{
			return FALSE;
		}

		if ( $bytSuccessFlag != "TRUE" )
		{
			return FALSE;
		}

		// 仕様可能機能コード取得
		$this->getFunctionAuthority( $objDB );

		return TRUE;
	}

	// ---------------------------------------------------------------
	/**
	*	ログアウト処理
	*	@param  string  $strSessionID セッションID
	*	@param  object  $objDB        DBオブジェクト
	*	@return boolean TRUE,FALSE
	*	@access public
	*/
	// ---------------------------------------------------------------
	function logout( $strSessionID, $objDB )
	{
		if ( !$strSessionID )
		{
			return FALSE;
		}

		// ログインセッション管理テーブルに問い合わせ
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

	// ---------------------------------------------------------------
	/**
	*	IPアドレスチェック
	*	@param  String  $strAccessIPAddress アクセスIPアドレス(,区切り)
	*	@return boolean TRUE,FALSE
	*	@access public
	*/
	// ---------------------------------------------------------------
	function checkAccessIP( $strAccessIPAddress )
	{
		// 許可IP取得
		$aryAccessIP = mb_split ( ",", $strAccessIPAddress );

		// IPの照合
		foreach ( $aryAccessIP as $strAccessIP )
		{
			$strAccessIP = mb_ereg_replace ( "\.", "\.", $strAccessIP );
			$strAccessIP = mb_ereg_replace ( "\*", ".+?", $strAccessIP );
			if ( mb_ereg ( $strAccessIP, $this->AccessIP ) )
			{
				return TRUE;
			}
		}
		return FALSE;
	}

	// ---------------------------------------------------------------
	/**
	*	仕様可能機能コード取得
	*	@param  object  $objDB           DBオブジェクト
	*	@access public
	*/
	// ---------------------------------------------------------------
	function getFunctionAuthority( $objDB )
	{
		// 権限グループコードにて検索
		$aryQuery = Array (
		            "SELECT lngFunctionCode, bytAuthorityFlag " .
		            "FROM m_FunctionAuthority " .
		            "WHERE lngFunctionGroupcode = " . $this->AuthorityGroupCode
		,

		// ユーザーコードにて検索
		            "SELECT lngFunctionCode, bytAuthorityFlag " .
		            "FROM m_FunctionAuthority " .
		            "WHERE lngUserCode = " . $this->UserCode
		);

		$flgArray = array ( "t" => TRUE, "f" => FALSE );
		foreach ( $aryQuery as $strQuery )
		{
			list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
			if ( $lngResultNum )
			{
				for ( $i = 0; $i < $lngResultNum; $i++ )
				{
					$objResult = $objDB->fetchObject( $lngResultID, $i );
					$this->FunctionCode[$objResult->lngfunctioncode] = $flgArray[$objResult->bytauthorityflag];
				}
			}
			if ( !$objDB->freeResult( $lngResultID ) )
			{
				fncOutputError ( 9017, DEF_WARNING, "アクセス権限がありません。", TRUE, "", $objDB );
			}
		}
	}
}
?>
