<!--


function fncChgEtoJ()
{


////////////////////////////////// ENGLISH /////////////////////////////////////
	if ( parent.lngLanguageCode == 0 )
	{


		if (typeof(LoginButton)!='undefined')
		{
			LoginButton.innerHTML = loginBt1;
		}

		if (typeof(CloseButton)!='undefined')
		{
			CloseButton.innerHTML = darkcloseBtE1;
		}


	}


////////////////////////////////// JAPANESE /////////////////////////////////////
	else if ( parent.lngLanguageCode == 1 )
	{


		if (typeof(LoginButton)!='undefined')
		{
			LoginButton.innerHTML = loginBt1;
		}

		if (typeof(CloseButton)!='undefined')
		{
			CloseButton.innerHTML = darkcloseBtJ1;
		}


	}

	return false;

}


//-->