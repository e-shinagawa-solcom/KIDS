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

		SegA01.innerText='����';
		SegA02.innerText='�Ʒ�����';
		SegA03.innerText='�Ʒ����ϼ�';
		SegA04.innerText='������';
		SegA05.innerText='��λ��';
		SegA06.innerText='�����Ԥ���ǧ��';
		SegA07.innerText='�������ɽ�����';
		SegA08.innerText='����';


		//-------------------------------------------------------------------------
		// v2 tomita update
		//-------------------------------------------------------------------------
		SegAPCode.innerText = '���ʥ�����';
		//-------------------------------------------------------------------------

		ViewSearch1.innerHTML= vishImgJ;

	}

	return false;

}


//-->