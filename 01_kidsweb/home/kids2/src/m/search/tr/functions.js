<!--


///// SearchColumn[] Check /////
var checkcount1 = 0;

function fncCheckAll1()
{
	var lngCnt = document.getElementsByName('SearchColumn[]').length;

	if (checkcount1 == 0)
	{

		for( i = 0; i < lngCnt; i++ )
		{
			if( document.getElementsByName('SearchColumn[]')[i].disabled == false)
			{
				document.getElementsByName('SearchColumn[]')[i].checked = true;
			}
		}

		checkcount1 = 1;
		CheckAll1.innerHTML = onBt;
	}

	else if(checkcount1 == 1)
	{

		for( i = 0; i < lngCnt; i++ )
		{
			if( document.getElementsByName('SearchColumn[]')[i].disabled == false)
			{
				document.getElementsByName('SearchColumn[]')[i].checked = false;
			}
		}

		checkcount1 = 0;
		CheckAll1.innerHTML = offBt;
	}
	return false;
}



///// ViewColumn[] Check /////
var checkcount2 = 0;

function fncCheckAll2()
{
	var lngCnt = document.getElementsByName('ViewColumn[]').length;

	if (checkcount2 == 0)
	{

		for( i = 0; i < lngCnt; i++ )
		{
			if( document.getElementsByName('ViewColumn[]')[i].disabled == false)
			{
				document.getElementsByName('ViewColumn[]')[i].checked = true;
			}
		}

		checkcount2 = 1;
		CheckAll2.innerHTML = onBt;
	}

	else if(checkcount2 == 1)
	{

		for( i = 0; i < lngCnt; i++ )
		{
			if( document.getElementsByName('ViewColumn[]')[i].disabled == false)
			{
				document.getElementsByName('ViewColumn[]')[i].checked = false;
			}
		}

		checkcount2 = 0;
		CheckAll2.innerHTML = offBt;
	}
	return false;
}


//-->