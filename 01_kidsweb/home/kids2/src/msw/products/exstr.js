<!--


var applyTabNum = '5';


function Msw3ChgEtoJ( MswCount )
{
	//////////////////// ENGLISH ////////////////////
	if ( MswCount == 0 )
	{

		///// SEARCH HEADER /////
		SearchHeader02.innerHTML = searchheader02E;

		///// RESULT HEADER /////
		ResultHeader02.innerHTML = resultheader02E;

		///// SEGMENTS /////
		SegA01_1.innerText='Products code';
		SegA01_2.innerText='Products name';

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
		SearchHeader02.innerHTML = searchheader02J;

		///// RESULT HEADER /////
		ResultHeader02.innerHTML = resultheader02J;

		///// SEGMENTS /////
		SegA01_1.innerText='製品コード';
		SegA01_2.innerText='製品名称';

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