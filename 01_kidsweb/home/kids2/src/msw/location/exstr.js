<!--


var applyTabNum = '5';


function Msw4ChgEtoJ( MswCount )
{
	//////////////////// ENGLISH ////////////////////
	if ( MswCount == 0 )
	{

		///// SEARCH HEADER /////
		SearchHeader01.innerHTML = searchheader01E;

		///// RESULT HEADER /////
		ResultHeader01.innerHTML = resultheader01E;

		///// SEGMENTS /////
		SegA01_1.innerText='Location code';
		SegA01_2.innerText='Location name';

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
		SegA01_1.innerText='納品場所コード';
		SegA01_2.innerText='納品場所名称';

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