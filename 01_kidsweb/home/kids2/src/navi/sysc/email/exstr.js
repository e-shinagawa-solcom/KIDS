<!--


//----------------------------------------------------
// ���� : ����˥塼�ѥʥӥ��������ܥ���������شؿ�
//----------------------------------------------------
function fncChgEtoJ( lngCode )
{

	// �Ѹ�
	if( lngCode == 0 )
	{
		if( typeof(MessageBt) != 'undefined' )
		{
			MessageBt.innerHTML = messagenaviE1;
		}

		if( typeof(ServerBt) != 'undefined' )
		{
			ServerBt.innerHTML = servernaviE1;
		}

		if( typeof(EmailBt) != 'undefined' )
		{
			EmailBt.innerHTML = emailnaviE3;
		}

		if( typeof(SessionBt) != 'undefined' )
		{
			SessionBt.innerHTML = sessionnaviE1;
		}
	}

	// ���ܸ�
	else if( lngCode == 1 )
	{
		if( typeof(MessageBt) != 'undefined' )
		{
			MessageBt.innerHTML = messagenaviJ1;
		}

		if( typeof(ServerBt) != 'undefined' )
		{
			ServerBt.innerHTML = servernaviJ1;
		}

		if( typeof(EmailBt) != 'undefined' )
		{
			EmailBt.innerHTML = emailnaviJ3;
		}

		if( typeof(SessionBt) != 'undefined' )
		{
			SessionBt.innerHTML = sessionnaviJ1;
		}
	}

	return false;
}


//-->
