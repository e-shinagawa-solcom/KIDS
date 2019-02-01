<!--


var applyTabNum = '5';


function Msw1ChgEtoJ( MswCount )
{
	//////////////////// ENGLISH ////////////////////
	if ( MswCount == 0 )
	{

		///// SEARCH HEADER /////
		SearchHeader01.innerHTML = searchheader01E;

		///// RESULT HEADER /////
		ResultHeader01.innerHTML = resultheader01E;

		///// SEGMENTS /////
		SegA01_1.innerText='Vendor code';
		SegA01_2.innerText='Vendor name';

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
		SegA01_1.innerText='ธÜตาฅณกผฅษ';
		SegA01_2.innerText='ธÜตาฬพพฮ';

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