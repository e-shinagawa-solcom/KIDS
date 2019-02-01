<?
// ----------------------------------------------------------------------------
/**
*       データベース処理クラス
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
*		isOpen             接続中かどうかの取得
*		open               DBへ接続
*		close              DBから切断
*		execute            SQLの実行
*		transactionBegin   トランザクション開始(BEGIN)
*		transactionCommit  トランザクション完了(COMMIT)
*		fetchArray         結果配列の指定行を連想配列に取得する
*		freeResult         結果ID（結果）を解放する
*		getFieldsCount     結果のフィールド数を取得する
*		getFieldName       結果のフィールド名を取得する
*		fetchObject        結果配列の指定行をオブジェクトで取得する
*		replaceLineFeed    対象文字列の改行コードを<LF>に統一する
*
*       更新履歴
*
*/
// ----------------------------------------------------------------------------

class clsDB
{
	/**
	*	接続ID
	*	@var string
	*/
	var $ConnectID;
	/**
	*	トランザクションフラグ(TRUE:SQLエラー時ROLLBACKを実行)
	*	@var boolean
	*/
	var $Transaction;

	/*
		入力エンコーディングを保持 
		デフォルト EUC_JP （2009年4月現在）
	*/
	var $InputEncoding = "EUC_JP";
	

	// ---------------------------------------------------------------
	/**
	*	コンストラクタ
	*	クラス内の初期化を行う
	*	
	*	@return void
	*	@access public
	*/
	// ---------------------------------------------------------------
	function clsDB()
	{
		// 接続IDの初期化
		$this->ConnectID   = FALSE;

		// トランザクションフラグの初期化
		$this->Transaction = FALSE;

	}

	// ---------------------------------------------------------------
	/**
	*	接続中かどうかの取得
	*	@return boolean TRUE,FALSE
	*	@access public
	*/
	// ---------------------------------------------------------------
	function isOpen()
	{
		if ( $this->ConnectID == FALSE )
		{
			$res = FALSE;
		}
		else
		{
			$res = TRUE;
		}
		return $res;
	}

	// ---------------------------------------------------------------
	/**
	 *	トランザクションが開始されているか真偽を返す
	 *	@return boolean TRUE,FALSE
	 *	@access public
	 */
	// ---------------------------------------------------------------
	public function isTransaction()
	{
		return $this->Transaction;
	}

	// ---------------------------------------------------------------
	/**
	*	DBへ接続
	*	@param  string  $strUserID  ユーザーID
	*	@param  string  $strPasswd  パスワード
	*	@param  string  $strDBName  データベース名
	*	@param  string  $strOptions データベース接続オプションパラメータ
	*	@return boolean TRUE,FALSE
	*	@access public
	*/
	// ---------------------------------------------------------------
	function open( $strUserID, $strPasswd, $strDBName, $strOptions )
	{
		$lngConID = 0;

		// 指定がなければデフォルトに設定
		//if ( trim($strOptions["POSTGRESQL_HOSTNAME"]) == "" )
		//{
		//	$strOptions["POSTGRESQL_HOSTNAME"] = POSTGRESQL_HOSTNAME;
		//}
		//if ( trim($strOptions["POSTGRESQL_HOSTPORT"]) == "" )
		//{
		//	$strOptions["POSTGRESQL_HOSTPORT"] = POSTGRESQL_HOSTPORT;
		//}
                if ( empty($strOptions) )
		{
			$strOptions["POSTGRESQL_HOSTNAME"] = POSTGRESQL_HOSTNAME;
                        $strOptions["POSTGRESQL_HOSTPORT"] = POSTGRESQL_HOSTPORT;
		}
		if ( $strUserID == "" )
		{
			$strUserID = DB_LOGIN_USERNAME;
		}
		if ( $strPasswd == "" )
		{
			$strPasswd = DB_LOGIN_PASSWORD;
		}
		if ( $strDBName == "" )
		{
			$strDBName = DB_NAME;
		}

		// 接続設定
		$strConnectionConfig = "";
		if ( $strOptions["POSTGRESQL_HOSTNAME"] )
		{
			$strConnectionConfig .= "host=" . $strOptions["POSTGRESQL_HOSTNAME"];
		}
		if ( $strOptions["POSTGRESQL_HOSTPORT"] )
		{
			$strConnectionConfig .= " port=" . $strOptions["POSTGRESQL_HOSTPORT"];
		}
		if ( $strUserID )
		{
			$strConnectionConfig .= " user=" . $strUserID;
		}
		if ( $strPasswd )
		{
			$strConnectionConfig .= " password=" . $strPasswd;
		}
		if ( $strDBName )
		{
			$strConnectionConfig .= " dbname=" . $strDBName;
		}

		// 持続接続の判別
		//if ( ENABLE_COUNTINUE_OPEN )
		//{
		//	$lngConID = pg_pconnect( "$strConnectionConfig" );
		//}
		//else
		//{
			$lngConID = pg_connect( "$strConnectionConfig" );
		//}

		// 正常終了なら接続IDを取得
		if ( $lngConID != FALSE )
		{
			$this->ConnectID = $lngConID;
			$ret = TRUE;
		}
		else
		{
			$ret = FALSE;
		}

		// Encoding を指定（Unicode対応）
		pg_set_client_encoding($lngConID, $this->InputEncoding);

		return $ret;
	}
	
	// ---------------------------------------------------------------
	/**
	*	DBからの入力エンコーディングを設定
	*	@access public
	*/
	// ---------------------------------------------------------------
	function setInputEncoding($strInputEncoding)
	{
		$this->InputEncoding = $strInputEncoding;
		pg_set_client_encoding($this->ConnectID, $this->InputEncoding);
	}
	
	// ---------------------------------------------------------------
	/**
	*	DBから切断
	*	@return boolean TRUE,FALSE
	*	@access public
	*/
	// ---------------------------------------------------------------
	function close()
	{
		// 接続チェック
		if ( !$this->isOpen() )
		{
			$res = FALSE;
		}
		else
		{
			pg_close( $this->ConnectID );
			$this->ConnectID = FALSE;
			$res = TRUE;
		}
		return $res;
	}

	// ---------------------------------------------------------------
	/**
	*	SQLの実行
	*	@param  string  $strQuery  実行するSQL文
	*	@return long    結果バッファID
	*	        boolean FALSE
	*	@access public
	*/
	// ---------------------------------------------------------------
	function execute( $strQuery )
	{
		// 接続チェック
		if ( !$this->isOpen() ){
			return FALSE;
		}

		$lngResultID = FALSE;
		// OutputDebugString($SQL . "<br>\n");

		// 2001/10/16 Added by saito
		// 改行コードの統一、クォート処理
		$strQuery = $this->replaceLineFeed( $strQuery );
		//$strQuery    = fncGetSafeSQLString($strQuery);

		// クエリ実行
		if ( !$lngResultID = pg_query( $this->ConnectID, $strQuery ) )
		{
			// SQLエラー
			// トランザクションフラグの確認
			if ( $this->Transaction )
			{
				if ( !pg_query( $this->ConnectID, "ROLLBACK" ) )
				{
					return FALSE;
				}
				$this->Transaction = FALSE;
			}

			// 実行時エラーのSQLとError文をメールにて送信
			// timestamp for the error entry
			$dt = date ( "Y-m-d H:i:s (T)" );
			$strMailMessage = "DATE $dt\n";
			$strMailMessage .= "Query -->\n" . $strQuery . "\n";
			$strMailMessage .= "Error -->\n" . pg_last_error($this->ConnectID) . "\n";
			mb_send_mail( ERROR_MAIL_TO,"K.I.D.S. Error Message from " . TOP_URL, $strMailMessage, "From: " . ERROR_MAIL_TO . "\nReturn-Path: " . ERROR_MAIL_TO . "\n" );
			//

			return FALSE;
		}

		// echo "SQL:$SQL<br>\n";
		return $lngResultID;
	}

	// ---------------------------------------------------------------
	/**
	*	トランザクション開始(BEGIN)
	*	@return boolean TRUE,FALSE
	*	@access public
	*/
	// ---------------------------------------------------------------
	function transactionBegin()
	{
		// 接続チェック
		if ( !$this->isOpen() )
		{
			return FALSE;
		}

		// トランザクションスタート
		if ( !$this->execute( "BEGIN" ) )
		{
			return FALSE;
		}
		$this->Transaction = TRUE;

		return TRUE;
	}

	// ---------------------------------------------------------------
	/**
	*	トランザクション完了(COMMIT)
	*	@return boolean TRUE,FALSE
	*	@access public
	*/
	// ---------------------------------------------------------------
	function transactionCommit()
	{
		// 接続チェック
		if ( !$this->isOpen() )
		{
			return FALSE;
		}

		// トランザクションコミット
		if ( !$this->execute( "COMMIT" ) )
		{
			return FALSE;
		}
		$this->Transaction = FALSE;

		return TRUE;
	}

	// ---------------------------------------------------------------
	/**
	 *	トランザクション ロールバック(ROLLBACK)
	 *	@return boolean TRUE,FALSE
	 *	@access public
	 */
	// ---------------------------------------------------------------
	function transactionRollback()
	{
		// 接続チェック
		if ( !$this->isOpen() )
		{
			return FALSE;
		}

		// トランザクションコミット
		if (!$this->execute("ROLLBACK"))
		{
			return FALSE;
		}

		$this->Transaction = FALSE;

		return TRUE;
	}

	// ---------------------------------------------------------------
	/**
	*	結果配列の指定行を連想配列に取得する
	*	@param  string  $lngResultID 結果ID
	*	@param  long    $lngResLine  取得する結果行
	*	@return array   取得データ配列
	*	@access public
	*/
	// ---------------------------------------------------------------
	function fetchArray( $lngResultID, $lngResLine )
	{
		// 接続チェック
		if ( !$this->isOpen() ) {
			// OutputDebugString("CDatabase::fetchArray::isOpen Failed");
			return FALSE;
		}

		// 結果IDチェック
		if ( $lngResultID == 0 ) {
			// OutputDebugString("CDatabase::fetchArray::ResId Failed");
			return FALSE;
		}

		return pg_Fetch_Array( $lngResultID, $lngResLine );
	}

	// ---------------------------------------------------------------
	/**
	*	結果配列のカラム名を配列に取得する
	*	@param  string  $lngResultID 結果ID
	*	@param  long    $lngFieldNum フィールド数
	*	@return array   $aryColumns  取得データ配列
	*	@access public
	*/
	// ---------------------------------------------------------------
	function fncColumnsArray( $lngResultID, $lngFieldNum )
	{
		// 接続チェック
		if ( !$this->isOpen() ) {
			// OutputDebugString("CDatabase::fncColumnsArray::isOpen Failed");
			return FALSE;
		}

		// 結果IDチェック
		if ( $lngResultID == 0 ) {
			// OutputDebugString("CDatabase::fncColumnsArray::ResId Failed");
			return FALSE;
		}

		$aryColumns = Array();

		// フィールド名を取得
		for ( $i = 0; $i < $lngFieldNum; $i++ )
		{
			$aryColumns[$i] = pg_field_name ( $lngResultID, $i );
		}

		return $aryColumns;
	}

	// ---------------------------------------------------------------
	/**
	*	結果ID（結果）を解放する
	*	@param  string  $lngResultID 結果ID
	*	@return boolean TRUE,FALSE
	*	@access public
	*/
	// ---------------------------------------------------------------
	function freeResult($lngResultID)
	{
		// 接続チェック
		if ( !$this->isOpen() )
		{
			return FALSE;
		}

		// 結果IDチェック
		if ( $lngResultID == 0)
		{
			return FALSE;
		}

		$ret = 0;

		$ret = pg_Free_Result($lngResultID);

		return TRUE;
	}

	// ---------------------------------------------------------------
	/**
	*	結果のフィールド数を取得する
	*	@param  string  $lngResultID 結果ID
	*	@return long フィールド数
	*	        boolean FALSE
	*	@access public
	*/
	// ---------------------------------------------------------------
	function getFieldsCount($lngResultID)
	{
		// 接続チェック
		if ( !$this->isOpen() )
		{
			return FALSE;
		}

		// 結果IDチェック
		if ( $lngResultID == 0 )
		{
			return FALSE;
		}

		$ret = 0;

		$ret = pg_Num_Fields( $lngResultID );

		return $ret;
	}

	// ---------------------------------------------------------------
	/**
	*	結果のフィールド名を取得する
	*	@param  string  $lngResultID 結果ID
	*	@param  string  $lngResultID フィールドID(0から始まるインデックス)
	*	@return array   $ret         フィールド名
	*	        boolean FALSE
	*	@access public
	*/
	// ---------------------------------------------------------------
	function getFieldName( $lngResultID, $lngFieldNum )
	{
		// 接続チェック
		if ( !$this->isOpen() )
		{
			return FALSE;
		}

		// 結果IDチェック
		if ( $lngResultID == 0 )
		{
			return FALSE;
		}

		$ret = FALSE;

		$ret = pg_Field_Name( $lngResultID, $lngFieldNum );

		return $ret;
	}

	// ---------------------------------------------------------------
	/**
	*	結果配列の指定行をオブジェクトで取得する
	*	@param  string $lngResultID 結果ID
	*	@param  long   $lngResLine  取得する結果行
	*	@return object              フィールド名
	*	@access public
	*/
	// ---------------------------------------------------------------
	function fetchObject( $lngResultID, $lngResLine )
	{
		// 接続チェック
		if ( !$this->isOpen() ) {
			// OutputDebugString("CDatabase::fetchArray::isOpen Failed");
			return FALSE;
		}

		// 結果IDチェック
		if ( $lngResultID == 0 ) {
			// OutputDebugString("CDatabase::fetchArray::ResId Failed");
			return FALSE;
		}

		return pg_fetch_object( $lngResultID, $lngResLine );
	}

	// ---------------------------------------------------------------
	/**
	*	対象文字列の改行コードを<LF>に統一する
	*	@param  string $strTarget 対象文字列
	*	@return string $strTarget 処理済み文字列
	*	@access public
	*/
	// ---------------------------------------------------------------
	function replaceLineFeed( $strTarget ){
		return preg_replace( '/\x0D\x0A|\x0A|\x0D/', "\x0A", $strTarget );
	}

}



// ---------------------------------------------------------------
//
// clsDBLock クラス
//
// DBを使ったロック制御用クラス
//
// ---------------------------------------------------------------
class clsDBLock {
	var $LockKey;         // ロックに使用するセッションID
	var $ServerTimeout;   // サーバー側ロック失敗のタイムアウト（秒）
	var $ClientTimeout;   // クライアント側ロック失敗のタイムアウト（秒）
	var $ServerCheckSpan; // サーバー側ロック再試行間隔（ミリ秒）

	// コンストラクタ
	function clsDBLock()
	{
		global $SERVER_NAME;
		global $REMOTE_ADDR;
		// セッションIDの生成
		$this->LockKey = fncGetSafeSQLString(uniqid($SERVER_NAME . $REMOTE_ADDR . ((string)rand())));
		// サーバー側タイムアウト時間の設定(20秒)
		$this->ServerTimeout = 20;
		// クライアント側タイムアウト時間の設定(5秒)
		$this->ClientTimeout = 5;
		// サーバー側ロック再試行間隔（ミリ秒）
		$this->ServerCheckSpan = 100;
	}

	// ---------------------------------------------------------------
	// 関数名: fncLock
	//
	// 概要:   ロックする
	//
	// 引数:
	//         &$dbconn   DB接続オブジェクト(Open済みであること)
	//         $locktable ロックテーブル
	//         $lockid    ロックIDフィールド
	//         $lockkey   ロックキーフィールド
	//         $locktime  ロック時間フィールド
	//         $lockidval ロックID値
	//
	// 戻り値:
	//         TRUE       ロック完了
	//         FALSE      ロック失敗
	// ---------------------------------------------------------------
	function fncLock(&$dbconn, $locktable, $lockid, $lockkey, $locktime, $lockidval = 1)
	{
		// タイムアウト時間の取得
		$timeout = time() + $this->ClientTimeout;
		while(1) {
			// タイムアウトの判定
			if ( $timeout < time()) break;
			// あいていたら、もしくはタイムアウト後ならロックキーを書き込む
			$SQL = "UPDATE $locktable SET ";
			$SQL = $SQL . "$lockkey = '" . $this->LockKey . "', ";
			$SQL = $SQL . "$locktime=(SYSDATE + (" . $this->ServerTimeout . " / 86400)) ";
			$SQL = $SQL . "WHERE ($lockkey IS NULL or $locktime < SYSDATE) and $lockid = $lockidval";
			$resid = $dbconn->execute($SQL);
			if ( $resid == FALSE) {
				break;
			}
			$dbconn->freeResult($resid);
			// ロックが完了したか確認する
			$SQL = "SELECT $lockkey as KEYVALUE ";
			$SQL = $SQL . "FROM $locktable ";
			$SQL = $SQL . "WHERE $lockid = $lockidval";
			$resid = $dbconn->execute($SQL);
			if ( $resid == FALSE) {
				break;
			}
			if ( !$dbconn->fetchArray($resid, $ResCount, 0)) {
				break;
			}
			// 結果IDを閉じる
			$dbconn->freeResult($resid);
			if ( $ResCount["KEYVALUE"] == $this->LockKey) return TRUE;
			// ちょっと待つ
			usleep($this->ServerCheckSpan);
		};

		return FALSE;
	}

	// ---------------------------------------------------------------
	// 関数名: fncUnlock
	//
	// 概要:   ロック解除する
	//
	// 引数:
	//         &$dbconn   DB接続オブジェクト(Open済みであること)
	//         $locktable ロックテーブル
	//         $lockid    ロックIDフィールド
	//         $lockkey   ロックキーフィールド
	//         $lockidval ロックID値
	//
	// 戻り値:
	//         TRUE       ロック完了
	//         FALSE      ロック失敗
	// ---------------------------------------------------------------
	function fncUnlock(&$dbconn, $locktable, $lockid, $lockkey, $lockidval = 1)
	{
		// タイムアウト時間の取得
		$timeout = time() + $this->ClientTimeout;
		do {
			// ロックキーを空白にする
			$SQL = "UPDATE $locktable SET ";
			$SQL = $SQL . "$lockkey = NULL ";
			$SQL = $SQL . "WHERE $lockkey = '" . $this->LockKey . "' and $lockid = $lockidval";
			$resid = $dbconn->execute($SQL);
			if ( $resid == FALSE) {
				break;
			}
			// 終了
			return TRUE;
		} while(0);

		return FALSE;
	}


}

	// ---------------------------------------------------------------
	// 関数名: fncGetSafeSQLString
	//
	// 概要:   SQL文で使用できる文字列に変換
	//
	// 引数:
	//         $strVal 文字列
	//
	// 戻り値:
	//         変換後の文字列
	// ---------------------------------------------------------------
	function fncGetSafeSQLString( $strVal )
	{
		return pg_escape_string( $strVal );
	}
?>
