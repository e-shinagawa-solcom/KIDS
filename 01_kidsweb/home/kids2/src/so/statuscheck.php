<?

// ----------------------------------------------------------------------------
/**
*       �������  ����ǡ���ͭ���������å�
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
*         ������ǡ����ν������ˡ������ֹ�Υǡ������ֿ����� : 1��,������� : 99�פ��ɤ�����ǧ
*           ���ƤϤޤ��硢���顼�Ȥ������Բ�ǽ�Ȥ��롣
*
*       ��������
*         V1
*         ��2004.07.14  ��������
*         V2
*         ��2005.10.17  �����å��оݤ�ֿ����� : 1��,������� : 99�פ��ѹ�
*/
// ----------------------------------------------------------------------------



	// ------------------------------------------------------------------------
	/**
	*   fncSoDataStatusCheck() �ؿ�
	*
	*   ��������
	*     ������ǡ�����ͭ���������å�
	*
	*   @param   $lngReceiveNo  [Number]  �����ֹ�
	*   @param   $objDB         [Object]  ��³�Ѥߥǡ����١������֥�������
	*   @return  [boolean]
	*/
	// ------------------------------------------------------------------------
	function fncSoDataStatusCheck( $lngReceiveNo, $objDB )
	{
		// ����������å�����
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
		$aryQuery[] = "strReceiveCode			as strreceivecode";			// �����ֹ�
		$aryQuery[] = ",strCustomerReceiveCode	as strcustomerreceivecode";	// �ܵҼ����ֹ�
		$aryQuery[] = ",strReviseCode			as strrevisecode";			// ��Х����ֹ�
		$aryQuery[] = ",lngReceiveStatusCode	as lngreceivestatuscode";	// �����ơ�����
		$aryQuery[] = ",bytInvalidFlag			as bytinvalidflag";			// ̵���ե饰
		$aryQuery[] = "FROM";
		$aryQuery[] = "m_Receive";
		$aryQuery[] = "WHERE";
		$aryQuery[] = "lngReceiveNo = ". $lngReceiveNo;

		$strQuery = implode( "\n", $aryQuery );

		// ���ID�����
		$objDB->freeResult( $lngResultID );

		// �����꡼�¹�
		$lngResultID = $objDB->execute( $strQuery );

		// �����꡼�¹Լ���
		if( !$lngResultID )
		{
			fncOutputError( 9051, DEF_ERROR, "", TRUE, "", $objDB );
			return false;
		}

		// �ǡ����μ���
		$aryData = pg_fetch_array( $lngResultID, 0, PGSQL_ASSOC );


		// ������֤μ���
		$lngStatusCheck = (int)$aryData["lngreceivestatuscode"];


		// �ǡ�����̵���ξ�硢������λ
		if( $aryData["bytinvalidflag"] == "t" )
		{
			$strErrMsg  = '<br><br>';
			$strErrMsg .= ' �ܵҼ����ֹ桧['.$aryData["strcustomerreceivecode"] . "]&nbsp;&nbsp;";
			$strErrMsg .= ' ����No.��['.$aryData["strreceivecode"]."-".$aryData["strrevisecode"] . "]";
			fncOutputError( 408, DEF_ERROR, $strErrMsg, TRUE, "", $objDB );
			return false;
		}

		// ������֤��ֿ����� : 1�פξ�硢������λ
		if( $lngStatusCheck == DEF_RECEIVE_APPLICATE )
		{
			$strErrMsg  = '<br><br>';
			$strErrMsg .= ' �ܵҼ����ֹ桧['.$aryData["strcustomerreceivecode"] . "]&nbsp;&nbsp;";
			$strErrMsg .= ' ����No.��['.$aryData["strreceivecode"]."-".$aryData["strrevisecode"] . "]";
			fncOutputError( 406, DEF_ERROR, $strErrMsg, TRUE, "", $objDB );
			return false;
		}

		// ������֤�������� : 99�פξ�硢������λ
		if( $lngStatusCheck == DEF_RECEIVE_CLOSED )
		{
			$strErrMsg  = '<br><br>';
			$strErrMsg .= ' �ܵҼ����ֹ桧['.$aryData["strcustomerreceivecode"] . "]&nbsp;&nbsp;";
			$strErrMsg .= ' ����No.��['.$aryData["strreceivecode"]."-".$aryData["strrevisecode"] . "]";
			fncOutputError( 407, DEF_ERROR, $strErrMsg, TRUE, "", $objDB );
			return false;
		}

		return true;
	}

?>
