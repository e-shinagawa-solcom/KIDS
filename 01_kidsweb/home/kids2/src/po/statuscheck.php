<?php

// ----------------------------------------------------------------------------
/**
*       発注管理  「発注データ」の有効性チェック
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
*         ・発注データの修正前に、該当番号のデータが
*           「納品済」以上（納品済(4)、締め済(99)）かを確認し
*           当てはまる場合、エラーとし修正不可能とする
*
*       更新履歴
*
*/
// ----------------------------------------------------------------------------



	//
	// 概要：「発注データ」の有効性チェック
	// 引数：
	//		$lngOrderNo		発注番号
	//		$objDB			接続済みデータベースオブジェクト
	// 戻り値：
	//		boolean
	//
	function fncPoDataStatusCheck($lngOrderNo, $objDB)
	{
		//
		// 引数をチェックする
		//
		if( !trim($lngOrderNo) || !isset($objDB) )
		{
			fncOutputError( 9054, DEF_ERROR, "", TRUE, "", $objDB );
			return false;
		}
		
		$lngResultID = 0;

		//
		// 「発注データ」の有効性チェックを行う
		//
		$aryQuery = array();
		$aryQuery[] = "SELECT";													// 
		$aryQuery[] = "strOrderCode				as strordercode";				// 1:発注番号
		$aryQuery[] = ",strReviseCode			as strrevisecode";				// 2:リバイズ番号
		$aryQuery[] = ",lngOrderStatusCode		as lngorderstatuscode";			// 3:発注ステータス
		$aryQuery[] = ",bytInvalidFlag			as bytinvalidflag";				// 4:無効フラグ
		$aryQuery[] = "FROM";
		$aryQuery[] = "m_Order";
		$aryQuery[] = "WHERE";
		$aryQuery[] = "lngOrderNo = ".$lngOrderNo;

		$strQuery = implode("\n", $aryQuery );
		// クエリー実行
		$objDB->freeResult( $lngResultID );
		if ( !$lngResultID = $objDB->execute( $strQuery ) )
		{
			fncOutputError( 9051, DEF_ERROR, "", TRUE, "", $objDB );
			return false;
		}
		$aryData2 = pg_fetch_array( $lngResultID, 0, PGSQL_ASSOC );
		
		// 納品済(4)以上（納品済、締め済）、又は無効であれば、修正出来ないものとする
		if( (int)$aryData2["lngorderstatuscode"] >= DEF_ORDER_END || $aryData2["bytinvalidflag"] == "t" )
		{
			$strErrMsg = '（又は無効なデータです）発注No.：'.$aryData2["strordercode"]."-".$aryData2["strrevisecode"];
			fncOutputError( 708, DEF_ERROR, $strErrMsg, TRUE, "", $objDB );
			return false;
		}
		return true;
	}
	
?>
