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

		SegA01.innerText='ログイン許可';
		SegA02.innerText='ユーザーコード';
		SegA03.innerText='ユーザーID';
		SegA04.innerText='パスワード';
		SegA05.innerText='パスワード確認';
		SegA06.innerText='メール配信許可';
		SegA07.innerText='メールアドレス';
		SegA08.innerText='ユーザー表示';
		SegA09.innerText='表示ユーザーコード';
		SegA10.innerText='表示ユーザー名';
		SegA11.innerText='フルネーム';
		SegA12.innerText='会社';
		SegA13.innerText='グループ';
		SegA14.innerText='権限グループ';
		SegA15.innerText='アクセスIPアドレス';
		SegA16.innerText='備考';


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