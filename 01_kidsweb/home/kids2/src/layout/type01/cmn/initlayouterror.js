


var SegErrMark = '<img src="' + errMark + '" width="16" height="17" border="0">';


function initLayoutError1( obj1 )
{
	var lay1 = obj1.children;

	///// π‡Ã‹≈∏≥´ /////
	if (typeof(obj1)!='undefined')
	{
		for (i = 0; i < lay1.length; i++)
		{
			lay1[i].innerHTML = SegErrMark;

			if ( isNaN(parseInt(lay1[i].style.width)) ||
					 parseInt(lay1[i].style.width) == 0 )
			{
				lay1[i].style.visibility = 'hidden';
			}

		}
	}

	return false;
}

function initLayoutError2( obj1 )
{

	var lay1 = obj1.children;

	///// π‡Ã‹≈∏≥´ /////
	if (typeof(obj1)!='undefined')
	{
		for (i = 0; i < lay1.length; i++)
		{
			lay1[i].innerHTML = SegErrMark;

			if ( isNaN(parseInt(lay1[i].style.width)) ||
					 parseInt(lay1[i].style.width) == 0 )
			{
				lay1[i].style.visibility = 'hidden';
			}

			if ( lay1[i].style.visibility == 'visible' )
			{
				lay1[i].style.visibility = 'hidden';
			}

		}
	}
	return false;
}
