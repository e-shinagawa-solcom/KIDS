<!--


function ChgEtoJ()
{


////////////////////////////////// ENGLISH /////////////////////////////////////
	if ( lngClickCode == 0 )
	{

		///// SET COOKIE /////
		SetlngLanguageCode();


		///// E TO J /////
		EtoJ.innerHTML = etojJbt;


		///// MAIN TITLE /////
		MainTitle.innerHTML = maintitleE;


		///// INPUT A /////
		SegAHeader.innerHTML = headerAE;


		SegA01.innerText='Login permission';
		SegA02.innerText='User code';
		SegA03.innerText='User ID';
		SegA04.innerText='Password';
		SegA05.innerText='Retry Password';
		SegA06.innerText='Email permission';
		SegA07.innerText='Email';
		SegA08.innerText='User permission';
		SegA09.innerText='Display user code';
		SegA10.innerText='Display user name';
		SegA11.innerText='User full name';
		SegA12.innerText='Company';
		SegA13.innerText='Group';
		SegA14.innerText='Authority group';
		SegA15.innerText='Access IP Address';
		SegA16.innerText='Remark';



		///// QUERY BUTTON /////
		SegAClearBt.innerHTML = clearbtAE1;
		RegistBt.innerHTML = registbtE1;


		lngLanguageCode = 0;
		lngClickCode = 1;

		window.NAVIwin.ChgEtoJ( 0 );


	}


////////////////////////////////// JAPANESE /////////////////////////////////////
	else if ( lngClickCode == 1 )
	{

		///// SET COOKIE /////
		SetlngLanguageCode();


		///// E TO J /////
		EtoJ.innerHTML = etojEbt;


		///// MAIN TITLE /////
		MainTitle.innerHTML = maintitleJ;


		///// INPUT A /////
		SegAHeader.innerHTML = headerAJ;

		SegA01.innerText='���������';
		SegA02.innerText='�桼����������';
		SegA03.innerText='�桼����ID';
		SegA04.innerText='�ѥ����';
		SegA05.innerText='�ѥ���ɳ�ǧ';
		SegA06.innerText='�᡼���ۿ�����';
		SegA07.innerText='�᡼�륢�ɥ쥹';
		SegA08.innerText='�桼����ɽ��';
		SegA09.innerText='ɽ���桼����������';
		SegA10.innerText='ɽ���桼����̾';
		SegA11.innerText='�ե�͡���';
		SegA12.innerText='���';
		SegA13.innerText='���롼��';
		SegA14.innerText='���¥��롼��';
		SegA15.innerText='��������IP���ɥ쥹';
		SegA16.innerText='����';


		///// QUERY BUTTON /////
		SegAClearBt.innerHTML = clearbtAJ1;
		RegistBt.innerHTML = registbtJ1;


		lngLanguageCode = 1;
		lngClickCode = 0;

		window.NAVIwin.ChgEtoJ( 1 );


	}

	return false;

}


//-->