<!--


function ChgEtoJ( lngCount )
{

////////////////////////////////// ENGLISH /////////////////////////////////////
	if ( lngCount == 0 )
	{

		SegA01.innerText='State';
		SegA02.innerText='Applicant';
		SegA03.innerText='Input person';
		SegA04.innerText='Application day';
		SegA05.innerText='Finish date';
		SegA06.innerText='Recognition person';
		SegA07.innerText='Result view number';
		SegA08.innerText='Class';


		//-------------------------------------------------------------------------
		// v2 tomita update
		//-------------------------------------------------------------------------
		SegAPCode.innerText = 'Products code';
		//-------------------------------------------------------------------------

		ViewSearch1.innerHTML= vishImgE;

	}


////////////////////////////////// JAPANESE /////////////////////////////////////
	else if ( lngCount == 1 )
	{

		SegA01.innerText='状態';
		SegA02.innerText='案件申請者';
		SegA03.innerText='案件入力者';
		SegA04.innerText='申請日';
		SegA05.innerText='完了日';
		SegA06.innerText='処理待ち承認者';
		SegA07.innerText='検索結果表示件数';
		SegA08.innerText='種別';


		//-------------------------------------------------------------------------
		// v2 tomita update
		//-------------------------------------------------------------------------
		SegAPCode.innerText = '製品コード';
		//-------------------------------------------------------------------------

		ViewSearch1.innerHTML= vishImgJ;

	}

	return false;

}


//-->