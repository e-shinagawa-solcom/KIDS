<!--


//----------------------------------------------------
// 解説 : 左メニュー用ナビゲーションボタン英日切替関数
//----------------------------------------------------
function fncChgEtoJ( lngCode )
{

	// 英語
	if( lngCode == 0 )
	{
		if( typeof(MessageBt) != 'undefined' )
		{
			MessageBt.innerHTML = messagenaviE3;
		}

		if( typeof(ServerBt) != 'undefined' )
		{
			ServerBt.innerHTML = servernaviE1;
		}

		if( typeof(EmailBt) != 'undefined' )
		{
			EmailBt.innerHTML = emailnaviE1;
		}

		if( typeof(SessionBt) != 'undefined' )
		{
			SessionBt.innerHTML = sessionnaviE1;
		}
	}

	// 日本語
	else if( lngCode == 1 )
	{
		if( typeof(MessageBt) != 'undefined' )
		{
			MessageBt.innerHTML = messagenaviJ3;
		}

		if( typeof(ServerBt) != 'undefined' )
		{
			ServerBt.innerHTML = servernaviJ1;
		}

		if( typeof(EmailBt) != 'undefined' )
		{
			EmailBt.innerHTML = emailnaviJ1;
		}

		if( typeof(SessionBt) != 'undefined' )
		{
			SessionBt.innerHTML = sessionnaviJ1;
		}
	}

	return false;
}


//-->
