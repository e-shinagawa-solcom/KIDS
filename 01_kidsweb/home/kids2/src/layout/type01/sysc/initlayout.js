<!--


//var menuBackImg = '<img src="' + menuback1 + '" width="313" height="27">';


///// MAIN MTITLE IMAGE /////
var maintitleJ = '<img src="' + titleJ + '" width="314" height="22" border="0" alt="システム">';
var maintitleE = '<img src="' + titleE + '" width="314" height="22" border="0" alt="SYSTEM">';


///// CSS VALUE /////
var fcolor = '#666666'; //項目フォントカラー
var segcolor = '#e8f0f1'; //項目背景色
var segbody = '#d6d0b1'; //INPUT A BODY 背景色
var brcolor01 = '#798787 #e8f0f1 #798787 #798787'; //項目右空きBORDER
var brcolor02 = '#798787 #798787 #798787 #e8f0f1'; //項目左空きBORDER
var brcolor03 = '#798787 #e8f0f1 #798787 #e8f0f1'; //項目左右空きBORDER


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