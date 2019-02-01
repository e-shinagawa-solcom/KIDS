<?php

// ----------------------------------------------------------------------------
/**
*       ȯ�����  ��ȯ��ǡ����פ�ͭ���������å�
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
*       ��������
*         ��ȯ��ǡ����ν������ˡ������ֹ�Υǡ�����
*           ��Ǽ�ʺѡװʾ��Ǽ�ʺ�(4)�������(99)�ˤ����ǧ��
*           ���ƤϤޤ��硢���顼�Ȥ������Բ�ǽ�Ȥ���
*
*       ��������
*
*/
// ----------------------------------------------------------------------------



	//
	// ���ס���ȯ��ǡ����פ�ͭ���������å�
	// ������
	//		$lngOrderNo		ȯ���ֹ�
	//		$objDB			��³�Ѥߥǡ����١������֥�������
	// ����͡�
	//		boolean
	//
	function fncPoDataStatusCheck($lngOrderNo, $objDB)
	{
		//
		// ����������å�����
		//
		if( !trim($lngOrderNo) || !isset($objDB) )
		{
			fncOutputError( 9054, DEF_ERROR, "", TRUE, "", $objDB );
			return false;
		}
		
		$lngResultID = 0;

		//
		// ��ȯ��ǡ����פ�ͭ���������å���Ԥ�
		//
		$aryQuery = array();
		$aryQuery[] = "SELECT";													// 
		$aryQuery[] = "strOrderCode				as strordercode";				// 1:ȯ���ֹ�
		$aryQuery[] = ",strReviseCode			as strrevisecode";				// 2:��Х����ֹ�
		$aryQuery[] = ",lngOrderStatusCode		as lngorderstatuscode";			// 3:ȯ���ơ�����
		$aryQuery[] = ",bytInvalidFlag			as bytinvalidflag";				// 4:̵���ե饰
		$aryQuery[] = "FROM";
		$aryQuery[] = "m_Order";
		$aryQuery[] = "WHERE";
		$aryQuery[] = "lngOrderNo = ".$lngOrderNo;

		$strQuery = implode("\n", $aryQuery );
		// �����꡼�¹�
		$objDB->freeResult( $lngResultID );
		if ( !$lngResultID = $objDB->execute( $strQuery ) )
		{
			fncOutputError( 9051, DEF_ERROR, "", TRUE, "", $objDB );
			return false;
		}
		$aryData2 = pg_fetch_array( $lngResultID, 0, PGSQL_ASSOC );
		
		// Ǽ�ʺ�(4)�ʾ��Ǽ�ʺѡ�����ѡˡ�����̵���Ǥ���С���������ʤ���ΤȤ���
		if( (int)$aryData2["lngorderstatuscode"] >= DEF_ORDER_END || $aryData2["bytinvalidflag"] == "t" )
		{
			$strErrMsg = '������̵���ʥǡ����Ǥ���ȯ��No.��'.$aryData2["strordercode"]."-".$aryData2["strrevisecode"];
			fncOutputError( 708, DEF_ERROR, $strErrMsg, TRUE, "", $objDB );
			return false;
		}
		return true;
	}
	
?>
