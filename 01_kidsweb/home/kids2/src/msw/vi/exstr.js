<!--


var applyTabNum = '11';



function Msw7ChgEtoJ( MswCount )
{
	//////////////////// ENGLISH ////////////////////
	if ( MswCount == 0 )
	{

		///// SEARCH HEADER /////
		SearchHeader01.innerHTML = searchheader01E;

		///// RESULT HEADER /////
		ResultHeader01.innerHTML = resultheader01E;

		///// SEGMENTS /////
		SegA01_1.innerText = 'Vendor code';
		SegA01_2.innerText = 'Vendor name';

		///// SEARCH BUTTON /////
		SearchButton01.innerHTML = searchbuttonAE1;


		///// SEARCH HEADER /////
		SearchHeader01b.innerHTML = searchheader01bE;

		///// RESULT HEADER /////
		ResultHeader01b.innerHTML = resultheader01bE;

		///// SEGMENTS /////
		SegC01_1.innerText = 'In charge code';
		SegC01_2.innerText = 'In charge name';

		///// SEARCH BUTTON /////
		SearchButton02.innerHTML = searchbuttonBE1;


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
		SegA01_1.innerText = '顧客コード';
		SegA01_2.innerText = '顧客名称';

		///// SEARCH BUTTON /////
		SearchButton01.innerHTML = searchbuttonAJ1;


		///// SEARCH HEADER /////
		SearchHeader01b.innerHTML = searchheader01bJ;

		///// RESULT HEADER /////
		ResultHeader01b.innerHTML = resultheader01bJ;

		///// SEGMENTS /////
		SegC01_1.innerText = '顧客担当者コード';
		SegC01_2.innerText = '顧客担当者名称';

		///// SEARCH BUTTON /////
		SearchButton02.innerHTML = searchbuttonBJ1;


		///// APPLY BUTTON /////
		ApplyButton.innerHTML = applybuttonJ1;

		///// CLEAR BUTTON /////
		ClearButton.innerHTML = clearbuttonJ1;

	}

	return false;
}


//-->