<!--


function fncSetArgsforPreview( objFrm , objLayA , objLayB )
{

	var aryArgs = opener.g_aryFrm;

	aryInner  = new Array();
	aryInner1 = new Array();
	aryInner2 = new Array();


	if( objLayA )
	{
		for( i = 0; i < aryArgs[0].length; i++ )
		{
			aryInner1[i] = '<input type="hidden" name="' + aryArgs[0][i] + '" value="' + aryArgs[1][i] + '">';
		}

		objLayA.innerHTML = aryInner1.join( '\n' );

	}

	if( objLayB )
	{
		for( i = 0; i < aryArgs[2].length; i++ )
		{
			aryInner2[i] = '<input type="hidden" name="' + aryArgs[2][i] + '" value="' + aryArgs[3][i] + '">';
		}

		objLayB.innerHTML = aryInner2.join( '\n' );

	}


	objFrm.submit();

	return false;
}


//-->