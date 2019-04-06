<?php
// ----------------------------------------------------------------------------
/**
*       ǧ�ڽ������饹
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
*		isLogin                 ��������ֳ�ǧ(���å����γ�ǧ������)
*		login                   ���������
*		logout                  �������Ƚ���
*		checkAccessIP           IP���ɥ쥹�����å�
*
*       ��������
*
*/
// ----------------------------------------------------------------------------

class clsAuth
{
	var $SessionID;          // ���å����ID
	var $AccessIP;           // ��������IP���ɥ쥹
	var $UserCode;           // �桼����������
	var $UserDisplayName;    // ɽ���桼����̾
	var $UserID;             // �桼����ID
	var $UserFullName;       // �ե�͡���
//	var $UserDisplayName;    // ɽ���桼����̾
	var $GroupDisplayCode;   // ɽ�����롼�ץ�����
	var $GroupDisplayName;   // ɽ�����롼��̾
	var $AuthorityGroupCode; // ���¥��롼�ץ�����
	var $AuthorityGroupName; // ���¥��롼��̾
	var $FunctionCode ;      // ���Ѳ�ǽ�ʵ�ǽ�����ɤ�������Ϣ������(Boolean)

	var $TimeLimtDate;       // �����ॢ��������

	// ---------------------------------------------------------------
	/**
	*	���󥹥ȥ饯��
	*	���饹��ν������Ԥ�
	*	
	*	@return void
	*	@access public
	*/
	// ---------------------------------------------------------------
	function __construct()
	{
		// ���å����ID
		$this->SessionID = "";

		// ��������IP���ɥ쥹
		$this->AccessIP = $_SERVER["REMOTE_ADDR"];

		// �桼����������
		$this->UserCode = 0;

		// ɽ���桼����̾
		$this->UserDisplayName = "";

		// �桼����ID
		$this->UserID = "";

		// �ե�͡���
		$this->UserFullName = "";

		// ɽ�����롼�ץ�����
		$this->GroupDisplayCode = "";

		// ɽ�����롼��̾
		$this->GroupDisplayName = "";

		// �桼���������ե�����
		$this->UserImageFileName = "";

		// ���¥��롼�ץ�����
		$this->AuthorityGroupCode = 0;

		// ���¥��롼��̾
		$this->AuthorityGroupName = "";

		// ���¥ե饰
		$this->AuthorityFlag = FALSE;
	}

	// ---------------------------------------------------------------
	/**
	*	��������֤ˤ��뤫�ɤ������ǧ
	*	@param  string  $strSessionID ���å����ID
	*	@param  object  $objDB        DB���֥�������
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

		// �����󥻥å��������ơ��֥���䤤��碌
		// ���å�����ݻ��γ�ǧ��ID�μ���
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

		// ���å������ݻ�����桼������ID�����
		$objResult = $objDB->fetchObject( $lngResultID, 0 );


		// �����ॢ���������μ���
		$this->TimeLimtDate = date( "Y/m/d/H/i/s", strtotime( "1 hour" ) );


		if ( time() - strtotime ( $objResult->remaining ) > $objResult->timeout * 60 )
		{
			// �����ॢ���Ƚ���
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


		// �����ͤ�ƥץ�ѥƥ����˥��å�
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

		// ��������IP���ɥ쥹�����å�
//		if ( !$this->checkAccessIP( $objDB ) )
//		{
//			return FALSE;
//		}

		// �������������ι���
		$strQuery = "UPDATE t_LoginSession " .
		            "SET dtmLoginTime = now() " .
		            "WHERE trim(from strSessionID) = '$strSessionID'";
		list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

		if ( !$objDB->freeResult( $lngResultID ) )
		{
			return FALSE;
		}

		// ���Ͳ�ǽ��ǽ�����ɼ���
		$this->getFunctionAuthority( $objDB );

		return TRUE;
	}

	// ---------------------------------------------------------------
	/**
	*	���������
	*	@param  string  $strUserID       �桼����ID
	*	@param  string  $strPasswordHash �ѥ����
	*	@param  object  $objDB           DB���֥�������
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

		// �ޥå�����ID���ѥ���ɤ��ĥ桼�����򸡺�
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

		// ���å����ID����
		//$browser = get_browser();
		//echo $browser["authenticodeupdate"];
		//exit;
		$strSessionID = md5 ( uniqid ( rand(), 1 ) );

		// ǧ�ڥ����å�
		if ( pg_Num_Rows ( $lngResultID ) )
		{
			$strPasswordHash = md5 ( $strPasswordHash );

			$objResult = $objDB->fetchObject( $lngResultID, 0 );

			// ������桼����������������ץ�ѥƥ����˥��å�
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

			// ��������IP���ɥ쥹�����å�
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

		// �����󥻥å��������ơ��֥�˽񤭹���
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

		// ���Ͳ�ǽ��ǽ�����ɼ���
		$this->getFunctionAuthority( $objDB );

		return TRUE;
	}

	// ---------------------------------------------------------------
	/**
	*	�������Ƚ���
	*	@param  string  $strSessionID ���å����ID
	*	@param  object  $objDB        DB���֥�������
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

		// �����󥻥å��������ơ��֥���䤤��碌
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
	*	IP���ɥ쥹�����å�
	*	@param  String  $strAccessIPAddress ��������IP���ɥ쥹(,���ڤ�)
	*	@return boolean TRUE,FALSE
	*	@access public
	*/
	// ---------------------------------------------------------------
	function checkAccessIP( $strAccessIPAddress )
	{
		// ����IP����
		$aryAccessIP = mb_split ( ",", $strAccessIPAddress );

		// IP�ξȹ�
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
	*	���Ͳ�ǽ��ǽ�����ɼ���
	*	@param  object  $objDB           DB���֥�������
	*	@access public
	*/
	// ---------------------------------------------------------------
	function getFunctionAuthority( $objDB )
	{
		// ���¥��롼�ץ����ɤˤƸ���
		$aryQuery = Array (
		            "SELECT lngFunctionCode, bytAuthorityFlag " .
		            "FROM m_FunctionAuthority " .
		            "WHERE lngFunctionGroupcode = " . $this->AuthorityGroupCode
		,

		// �桼���������ɤˤƸ���
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
				fncOutputError ( 9017, DEF_WARNING, "�����������¤�����ޤ���", TRUE, "", $objDB );
			}
		}
	}
}
?>
