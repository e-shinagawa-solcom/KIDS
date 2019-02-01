<!--


function GetlngLanguageCode( lngObj )
{
	lngClickCode = lngObj;

	ChgEtoJ();

	return false;
}

function GetIfrmlngLanguageCode( lngLanguageCode , objName )
{

	var lngObj = lngLanguageCode;

	ChgEtoJ( lngObj );

	if( objName )
	{
		if( objName.value != '' )
		{
			var lngSelfCode = objName.value;
			ChgEtoJ( lngSelfCode );
		}
	}

	return false;
}


function SetlngLanguageCode()
{
	var exp = new Date();
	exp.setTime(exp.getTime()+1000*60*60*24*10);

	document.cookie = "lngLanguageCode=" + lngClickCode + "; expires=" + exp.toGMTString() + ';path=/;';
}


//-->