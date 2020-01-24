<!--


//----------------------------------------------------
// 解説 : 英日切替関数
//----------------------------------------------------
function ChgEtoJ()
{

	// 英語
	if( lngClickCode == 0 )
	{
		// SET COOKIE
		SetlngLanguageCode();

		// E TO J
		EtoJ.innerHTML = etojJbt;

		// MAIN TITLE
		MainTitle.innerHTML = maintitleE;

		strSelectFunctions.innerText = 'SELECT FUNCTIONS';

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
			EmailBt.innerHTML = emailnaviE1;
		}

		if( typeof(SessionBt) != 'undefined' )
		{
			SessionBt.innerHTML = sessionnaviE1;
		}

		lngLanguageCode = 0;
		lngClickCode = 1;
	}

	// 日本語
	else if( lngClickCode == 1 )
	{
		// SET COOKIE
		SetlngLanguageCode();

		// E TO J
		EtoJ.innerHTML = etojEbt;

		// MAIN TITLE
		MainTitle.innerHTML = maintitleJ;

		strSelectFunctions.innerText = '機能選択';

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
			EmailBt.innerHTML = emailnaviJ1;
		}

		if( typeof(SessionBt) != 'undefined' )
		{
			SessionBt.innerHTML = sessionnaviJ1;
		}

		lngLanguageCode = 1;
		lngClickCode = 0;
	}

	return false;
}


//-->