<!--



//------------------------------------------------------------
// ²òÀâ : ¥Ø¥Ã¥À¡¼¥¤¥á¡¼¥¸¤ÎÄêµÁ
//------------------------------------------------------------
var headerAJ = '<img src="' + headtitleAJ + '" width="949" height="30" border="0" alt="Ä¢É¼¸¡º÷">';
var headerAE = '<img src="' + headtitleAE + '" width="949" height="30" border="0" alt="LIST SEARCH">';





//------------------------------------------------------------
// ²òÀâ : ÆüËÜ¸ì¡¦±Ñ¸ìÀÚÂØ´Ø¿ô
//------------------------------------------------------------
function ChgEtoJ( lngSelfCode )
{

	// ±Ñ¸ì
	if ( lngSelfCode == 0 )
	{

		window.top.SegAHeader.innerHTML = headerAE;

		SltList.innerText     = 'Select';
		ControlName.innerText = 'Control name';
		ListName.innerText    = 'List name';

	}

	// ÆüËÜ¸ì
	else if ( lngSelfCode == 1 )
	{

		window.top.SegAHeader.innerHTML = headerAJ;

		SltList.innerText     = 'ÁªÂò';
		ControlName.innerText = '´ÉÍıÌ¾¾Î';
		ListName.innerText    = 'Ä¢É¼Ì¾¾Î';

	}

	return false;

}


//-->