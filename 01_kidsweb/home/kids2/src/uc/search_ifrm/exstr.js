<!--


function ChgEtoJ( lngCount )
{

////////////////////////////////// ENGLISH /////////////////////////////////////
	if ( lngCount == 0 )
	{

		DetailSegs.innerText = 'Detail';
		LoginPermissionSegs.innerText = 'Login permission';
		UserCodeSegs.innerText = 'User code';
		UserIDSegs.innerText = ' user ID';
		EmailPermissionSegs.innerText = 'Email permission';
		EmailSegs.innerText = 'Email';
		DisplayUserSegs.innerText = 'User permission';
		DisplayUserCodeSegs.innerText = 'Display user code';
		DisplayUserNameSegs.innerText = 'Display user name';
		UserFullNameSegs.innerText = 'User full name';
		CompanySegs.innerText = 'Company';
		GroupSegs.innerText = 'Group';
		AuthorityGroupSegs.innerText = 'Authority group';
		AccessIPAddressSegs.innerText = 'Access IP Address';
		RemarkSegs.innerText = 'Remark';
		FixSegs.innerText = 'Fix';


		ViewSearch1.innerHTML= vishImgE;

	}


////////////////////////////////// JAPANESE /////////////////////////////////////
	else if ( lngCount == 1 )
	{

		DetailSegs.innerText = '詳細';
		LoginPermissionSegs.innerText = 'ログイン許可';
		UserCodeSegs.innerText = 'ユーザーコード';
		UserIDSegs.innerText = 'ユーザーID';
		EmailPermissionSegs.innerText = 'メール配信許可';
		EmailSegs.innerText = 'メールアドレス';
		DisplayUserSegs.innerText = 'ユーザー表示';
		DisplayUserCodeSegs.innerText = '表示ユーザーコード';
		DisplayUserNameSegs.innerText = '表示ユーザー名';
		UserFullNameSegs.innerText = 'フルネーム';
		CompanySegs.innerText = '会社';
		GroupSegs.innerText = 'グループ';
		AuthorityGroupSegs.innerText = '権限グループ';
		AccessIPAddressSegs.innerText = 'アクセスIPアドレス';
		RemarkSegs.innerText = '備考';
		FixSegs.innerText = '修正';


		ViewSearch1.innerHTML= vishImgJ;

	}

	return false;

}


//-->