

// �����⡼���ͼ���
var g_strMode = parent.g_aryArgs[0][4];

// [lngLanguageCode]�ͼ���
// var g_lngCode = parent.lngLanguageCode.value;
var g_lngCode = 1;







// �����꡼�ܥ���ơ��֥�񤭽Ф��⥸�塼��
function fncTitleOutput( lngCode )
{

	// �����ǧ�ѥơ��֥륻�å�(���ܸ�)
	var deleteTableJ = '<table width="90%" cellpadding="5" cellspacing="1" border="0" bgcolor="#6f8180" align="center"><tr class="SegColumn"><td>������ޤ�����</td></tr><tr><td bgcolor="#f1f1f1"><a href="#" onclick="window.close();"><img onmouseover="CloseJOn( this );" onmouseout="CloseJOff( this );" src="/img/type01/cmn/seg/close_off_ja_bt.gif" width="72" height="20" border="0" alt="CLOSE"></a>&nbsp;&nbsp;<a href="#" onclick="window.form1.submit();window.returnValue=true;window.close();"><img onmouseover="DeleteJOn( this );" onmouseout="DeleteJOff( this );" src="/img/type01/cmn/seg/delete_off_ja_bt.gif" width="72" height="20" border="0" alt="DELETE"><a></td></tr></table>';
	// �����ǧ�ѥơ��֥륻�å�(�Ѹ�)
	var deleteTableE = '<table width="90%" cellpadding="5" cellspacing="1" border="0" bgcolor="#6f8180" align="center"><tr class="SegColumn"><td>Does it delete?</td></tr><tr><td bgcolor="#f1f1f1"><a href="#" onclick="window.close();"><img onmouseover="CloseEOn( this );" onmouseout="CloseEOff( this );" src="/img/type01/cmn/seg/close_off_en_bt.gif" width="72" height="20" border="0" alt="CLOSE"></a>&nbsp;&nbsp;<a href="#" onclick="window.form1.submit();window.returnValue=true;window.close();"><img onmouseover="DeleteEOn( this );" onmouseout="DeleteEOff( this );" src="/img/type01/cmn/seg/delete_off_en_bt.gif" width="72" height="20" border="0" alt="DELETE"><a></td></tr></table>';

	// �ܺٳ�ǧ�ѥơ��֥륻�å�(���ܸ�)
	var detailTableJ = '<table width="90%" cellpadding="5" cellspacing="1" border="0" bgcolor="#6f8180" align="center"><tr><td bgcolor="#f1f1f1"><a href="#" onclick="alert("test");window.close();"><img onclick="window.close();" onmouseover="CloseJOn( this );" onmouseout="CloseJOff( this );" src="/img/type01/cmn/seg/close_off_ja_bt.gif" width="72" height="20" border="0" alt="CLOSE"></a></td></tr></table>';
	// �ܺٳ�ǧ�ѥơ��֥륻�å�(�Ѹ�)
	var detailTableE = '<table width="90%" cellpadding="5" cellspacing="1" border="0" bgcolor="#6f8180" align="center"><tr><td bgcolor="#f1f1f1"><a href="#" onclick="window.close();"><img onmouseover="CloseEOn( this );" onmouseout="CloseEOff( this );" src="/img/type01/cmn/seg/close_off_en_bt.gif" width="72" height="20" border="0" alt="CLOSE"></a></td></tr></table>';


	if( g_strMode == 'detail' )
	{
		if( lngCode == 0 )
		{
			ControlTitle.innerText = 'DETAIL';
			objQuery.innerHTML = detailTableE;
		}
		else if( lngCode == 1 )
		{
			ControlTitle.innerText = '�ܺٳ�ǧ';
			objQuery.innerHTML = detailTableJ;
		}
	}
	else if( g_strMode == 'delete' )
	{
		if( lngCode == 0 )
		{
			ControlTitle.innerText = 'DELETE';
			objQuery.innerHTML = deleteTableE;
		}
		else if( lngCode == 1 )
		{
			ControlTitle.innerText = '�����ǧ';
			objQuery.innerHTML = deleteTableJ;
		}
	}

	return false;
}





// ���ܸ�Ѹ�����
function fncChgEtoJ( strMode )
{

	// �Ѹ�����
	if( g_lngCode == 0 )
	{

		// �����꡼�ܥ���ơ��֥�񤭽Ф�
		//fncTitleOutput( 0 );

		// �����ѥơ��֥�񤭽Ф�
		fncProcessingOutputModule( strMode , 0 );

		bytInvalidFlag.innerText        = 'Login permission';
		lngUserCode.innerText           = 'User code';
		strUserID.innerText             = 'User ID';
		bytMailTransmitFlag.innerText   = 'Email permission';
		strMailAddress.innerText        = 'Email';
		bytUserDisplayFlag.innerText    = 'User permission';
		strUserDisplayCode.innerText    = 'Display user code';
		strUserDisplayName.innerText    = 'Display user name';
		strUserFullName.innerText       = 'User full name';
		lngCompanyCode.innerText        = 'Company';
		aryGroup.innerText              = 'Group';
		strAuthorityGroupName.innerText = 'Authority group';
		strAccessIPAddress.innerText    = 'Access IP Address';
		strUserImageFileName.innerHTML  = 'User image';
		strNote.innerText               = 'Remark';

		if( typeof(strPasswd) != 'undefined' )
		{
			strPasswd.innerHTML = 'Password';
		}

		if( typeof(strMyPageInfo) != 'undefined' )
		{
			strMyPageInfo.innerHTML = 'My page info';
		}

	}

	// ���ܸ�����
	else if( g_lngCode == 1 )
	{

		// �����꡼�ܥ���ơ��֥�񤭽Ф�
		//fncTitleOutput( 1 );

		// �����ѥơ��֥�񤭽Ф�
		fncProcessingOutputModule( strMode , 1 );

		bytInvalidFlag.innerText        = '���������';
		lngUserCode.innerText           = '�桼����������';
		strUserID.innerText             = '�桼����ID';
		bytMailTransmitFlag.innerText   = '�᡼���ۿ�����';
		strMailAddress.innerText        = '�᡼�륢�ɥ쥹';
		bytUserDisplayFlag.innerText    = '�桼����ɽ��';
		strUserDisplayCode.innerText    = 'ɽ���桼����������';
		strUserDisplayName.innerText    = 'ɽ���桼����̾';
		strUserFullName.innerText       = '�ե�͡���';
		lngCompanyCode.innerText        = '���';
		aryGroup.innerText              = '���롼��';
		strAuthorityGroupName.innerText = '���¥��롼��';
		strAccessIPAddress.innerText    = '��������IP���ɥ쥹';
		strUserImageFileName.innerHTML  = '�桼�������᡼��';
		strNote.innerText               = '����';

		if( typeof(strPasswd) != 'undefined' )
		{
			strPasswd.innerHTML = '�ѥ����';
		}

		if( typeof(strMyPageInfo) != 'undefined' )
		{
			strMyPageInfo.innerHTML = '�ޥ��ڡ�������';
		}

	}

	return false;
}