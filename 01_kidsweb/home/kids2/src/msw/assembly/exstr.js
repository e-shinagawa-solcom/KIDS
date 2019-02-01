<!--


var applyTabNum = '5';


function Msw1_3ChgEtoJ( MswCount )
{
	//////////////////// ENGLISH ////////////////////
	if ( MswCount == 0 )
	{

		///// SEARCH HEADER /////
		SearchHeader01.innerHTML = searchheader01E;

		///// RESULT HEADER /////
		ResultHeader01.innerHTML = resultheader01E;

		///// SEGMENTS /////
		SegA01_1.innerText='Factory code';
		SegA01_2.innerText='Factory name';

		///// SEARCH BUTTON /////
		SearchButton01.innerHTML = searchbuttonAE1;

		///// APPLY BUTTON /////
		ApplyButton.innerHTML = applybuttonE1;

		///// CLEAR BUTTON /////
		ClearButton.innerHTML = clearbuttonE1;

	}

	//////////////////// JAPANESE ////////////////////
	else if ( MswCount == 1 )
	{

		///// SEARCH HEADER /////
		SearchHeader01.innerHTML = searchheader01J;

		///// RESULT HEADER /////
		ResultHeader01.innerHTML = resultheader01J;

		///// SEGMENTS /////
		SegA01_1.innerText='工場コード';
		SegA01_2.innerText='工場名称';

		///// SEARCH BUTTON /////
		SearchButton01.innerHTML = searchbuttonAJ1;

		///// APPLY BUTTON /////
		ApplyButton.innerHTML = applybuttonJ1;

		///// CLEAR BUTTON /////
		ClearButton.innerHTML = clearbuttonJ1;

	}

	return false;
}


//-->