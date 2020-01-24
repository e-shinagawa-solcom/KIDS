<!--


function ChgEtoJ( lngSelfCode )
{

	// 英語
	if ( lngSelfCode == 0 )
	{

		if ( typeof(SelectBt) != 'undefined' )
		{
			SelectBt.innerHTML = selectbtE1;
		}

	}

	// 日本語
	else if ( lngSelfCode == 1 )
	{

		if ( typeof(SelectBt) != 'undefined' )
		{
			SelectBt.innerHTML = selectbtJ1;
		}

	}

	return false;

}


//-->