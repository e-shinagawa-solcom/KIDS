<!--


//var menuBackImg = '<img src="' + menuback1 + '" width="313" height="27">';


///// MAIN MTITLE IMAGE /////
var maintitleJ = '<img src="' + titleJ + '" width="314" height="22" border="0" alt="�����ƥ�">';
var maintitleE = '<img src="' + titleE + '" width="314" height="22" border="0" alt="SYSTEM">';


///// CSS VALUE /////
var fcolor = '#666666'; //���ܥե���ȥ��顼
var segcolor = '#e8f0f1'; //�����طʿ�
var segbody = '#d6d0b1'; //INPUT A BODY �طʿ�
var brcolor01 = '#798787 #e8f0f1 #798787 #798787'; //���ܱ�����BORDER
var brcolor02 = '#798787 #798787 #798787 #e8f0f1'; //���ܺ�����BORDER
var brcolor03 = '#798787 #e8f0f1 #798787 #e8f0f1'; //���ܺ�������BORDER


function initLayout()
{
	///// MAIN TITLE /////
	MainTitle.innerHTML = maintitleJ;

	//MenuBack.innerHTML  = menuBackImg;
	

	if( typeof(MessageBt) != 'undefined' )
	{
		MessageBt.innerHTML = messagenaviJ1;
	}

	if( typeof(ServerBt) != 'undefined' )
	{
		ServerBt.innerHTML = servernaviJ1;
	}

	if( typeof(EmailBt) != 'undefined' )
	{
		EmailBt.innerHTML = emailnaviJ1;
	}

	if( typeof(SessionBt) != 'undefined' )
	{
		SessionBt.innerHTML = sessionnaviJ1;
	}
	

	return false;
}


//-->