<?

// ----------------------------------------------------------------------------
/**
*       受注管理  受注データ有効性チェック
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
*         ・受注データの修正前に、該当番号のデータが「申請中 : 1」,「締め済 : 99」かどうか確認
*           当てはまる場合、エラーとし修正不可能とする。
*
*       更新履歴
*         V1
*         ・2004.07.14  新規作成
*         V2
*         ・2005.10.17  チェック対象を「申請中 : 1」,「締め済 : 99」に変更
*/
// ----------------------------------------------------------------------------



	// ------------------------------------------------------------------------
	/**
	*   fncSoDataStatusCheck() 関数
	*
	*   処理概要
	*     ・受注データの有効性チェック
	*
	*   @param   $lngReceiveNo  [Number]  受注番号
	*   @param   $objDB         [Object]  接続済みデータベースオブジェクト
	*   @return  [boolean]
	*/
	// ------------------------------------------------------------------------
	function fncSoDataStatusCheck( $lngReceiveNo, $objDB )
	{
		// 引数をチェックする
		if( !trim($lngReceiveNo) || !isset($objDB) )
		{
			fncOutputError( 9054, DEF_ERROR, "", TRUE, "", $objDB );
			return false;
		}

		$lngResultID = 0;


		//-----------------------------------------------------------
		// DB -> SELECT : m_Receive
		//-----------------------------------------------------------
		$aryQuery   = array();
		$aryQuery[] = "SELECT";
		$aryQuery[] = "strReceiveCode			as strreceivecode";			// 受注番号
		$aryQuery[] = ",strCustomerReceiveCode	as strcustomerreceivecode";	// 顧客受注番号
		$aryQuery[] = ",strReviseCode			as strrevisecode";			// リバイズ番号
		$aryQuery[] = ",lngReceiveStatusCode	as lngreceivestatuscode";	// 受注ステータス
		$aryQuery[] = ",bytInvalidFlag			as bytinvalidflag";			// 無効フラグ
		$aryQuery[] = "FROM";
		$aryQuery[] = "m_Receive";
		$aryQuery[] = "WHERE";
		$aryQuery[] = "lngReceiveNo = ". $lngReceiveNo;

		$strQuery = implode( "\n", $aryQuery );

		// 結果IDを解放
		$objDB->freeResult( $lngResultID );

		// クエリー実行
		$lngResultID = $objDB->execute( $strQuery );

		// クエリー実行失敗
		if( !$lngResultID )
		{
			fncOutputError( 9051, DEF_ERROR, "", TRUE, "", $objDB );
			return false;
		}

		// データの取得
		$aryData = pg_fetch_array( $lngResultID, 0, PGSQL_ASSOC );


		// 受注状態の取得
		$lngStatusCheck = (int)$aryData["lngreceivestatuscode"];


		// データが無効の場合、処理終了
		if( $aryData["bytinvalidflag"] == "t" )
		{
			$strErrMsg  = '<br><br>';
			$strErrMsg .= ' 顧客受注番号：['.$aryData["strcustomerreceivecode"] . "]&nbsp;&nbsp;";
			$strErrMsg .= ' 受注No.：['.$aryData["strreceivecode"]."-".$aryData["strrevisecode"] . "]";
			fncOutputError( 408, DEF_ERROR, $strErrMsg, TRUE, "", $objDB );
			return false;
		}

		// 受注状態が「申請中 : 1」の場合、処理終了
		if( $lngStatusCheck == DEF_RECEIVE_APPLICATE )
		{
			$strErrMsg  = '<br><br>';
			$strErrMsg .= ' 顧客受注番号：['.$aryData["strcustomerreceivecode"] . "]&nbsp;&nbsp;";
			$strErrMsg .= ' 受注No.：['.$aryData["strreceivecode"]."-".$aryData["strrevisecode"] . "]";
			fncOutputError( 406, DEF_ERROR, $strErrMsg, TRUE, "", $objDB );
			return false;
		}

		// 受注状態が「締め済 : 99」の場合、処理終了
		if( $lngStatusCheck == DEF_RECEIVE_CLOSED )
		{
			$strErrMsg  = '<br><br>';
			$strErrMsg .= ' 顧客受注番号：['.$aryData["strcustomerreceivecode"] . "]&nbsp;&nbsp;";
			$strErrMsg .= ' 受注No.：['.$aryData["strreceivecode"]."-".$aryData["strrevisecode"] . "]";
			fncOutputError( 407, DEF_ERROR, $strErrMsg, TRUE, "", $objDB );
			return false;
		}

		return true;
	}

?>
