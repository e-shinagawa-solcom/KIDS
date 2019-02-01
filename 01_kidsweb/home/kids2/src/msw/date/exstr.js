
function fncChgEtoJ( lngCode )
{

	if( lngCode == 1 )
	{
		if( typeof(Column0) != 'undefined' &&
			 typeof(Column1) != 'undefined' &&
			 typeof(Column2) != 'undefined' &&
			 typeof(Column3) != 'undefined' &&
			 typeof(Column4) != 'undefined' &&
			 typeof(Column5) != 'undefined' &&
			 typeof(Column6) != 'undefined' )
		{
			Column0.innerText = '日';
			Column1.innerText = '月';
			Column2.innerText = '火';
			Column3.innerText = '水';
			Column4.innerText = '木';
			Column5.innerText = '金';
			Column6.innerText = '土';
		}
	}
	else if( lngCode == 0 )
	{
		if( typeof(Column0) != 'undefined' &&
			 typeof(Column1) != 'undefined' &&
			 typeof(Column2) != 'undefined' &&
			 typeof(Column3) != 'undefined' &&
			 typeof(Column4) != 'undefined' &&
			 typeof(Column5) != 'undefined' &&
			 typeof(Column6) != 'undefined' )
		{
			Column0.innerText = 'Sun';
			Column1.innerText = 'Mon';
			Column2.innerText = 'Tue';
			Column3.innerText = 'Wed';
			Column4.innerText = 'Thu';
			Column5.innerText = 'Fri';
			Column6.innerText = 'Sat';
		}
	}

	return false;
}