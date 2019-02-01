

//---------------------------------------------------------
// 適用 :「アップロード」
//---------------------------------------------------------
var uploadNaviJ1 = '<a href="#"><img onmouseover="fncChangeBtImg( this, uploadJ2 );" onmouseout="fncChangeBtImg( this, uploadJ1 ); fncAlphaOff( this );" onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" src="' + uploadJ1 + '" width="151" height="25" border="0" alt="アップロード" tabindex="0"></a>';
var uploadNaviE1 = '<a href="#"><img onmouseover="fncChangeBtImg( this, uploadE2 );" onmouseout="fncChangeBtImg( this, uploadE1 ); fncAlphaOff( this );" onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" src="' + uploadE1 + '" width="151" height="25" border="0" alt="UPLOAD" tabindex="0"></a>';



///// LIST OUTPUT NAVI BT IMAGE /////
var listoutnaviJ1 = '<a href="#"><img onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onmouseover="ListOutJOn(this);" onmouseout="ListOutJOff(this);fncAlphaOff( this );" src="' + listoutJ1 + '" width="151" height="25" border="0" alt="帳票出力"></a>';
var listoutnaviE1 = '<a href="#"><img onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onmouseover="ListOutEOn(this);" onmouseout="ListOutEOff(this);fncAlphaOff( this );" src="' + listoutE1 + '" width="151" height="25" border="0" alt="LIST OUTPUT"></a>';


///// DATA EXPORT NAVI BT IMAGE /////
var dataexnaviJ1 = '<a href="#"><img onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onmouseover="DataExJOn(this);" onmouseout="DataExJOff(this);fncAlphaOff( this );" src="' + dataexJ1 + '" width="151" height="25" border="0" alt="データエクスポート"></a>';
var dataexnaviE1 = '<a href="#"><img onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onmouseover="DataExEOn(this);" onmouseout="DataExEOff(this);fncAlphaOff( this );" src="' + dataexE1 + '" width="151" height="25" border="0" alt="DATA EXPORT"></a>';


///// MASTER NAVI BT IMAGE /////
var mstnaviJ1 = '<a href="#"><img onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onmouseover="fncMButton( \'onJ\' , this );" onmouseout="fncMButton( \'offJ\' , this );fncAlphaOff( this );" onmousedown="fncMButton( \'downJ\' , this );" onmouseup="fncMButton( \'offJ\' , this );" src="' + mstJ1 + '" width="151" height="25" border="0" alt="マスタ"></a>';
var mstnaviE1 = '<a href="#"><img onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onmouseover="fncMButton( \'onE\' , this );" onmouseout="fncMButton( \'offE\' , this );fncAlphaOff( this );" onmousedown="fncMButton( \'downE\' , this );" onmouseup="fncMButton( \'offE\' , this );" src="' + mstE1 + '" width="151" height="25" border="0" alt="MASTER"></a>';







///// CSS VALUE /////
var navibordercolor = '#cdcdcd';
var navibackcolor = '#72828b';





function initLayoutNavigation()
{

	///// NAVIGATION BODY COLOR /////
	if (typeof(NaviBodys)!='undefined')
	{
		NaviBodys.style.background = navibackcolor;
		NaviBodys.style.bordercolor = navibordercolor;
	}


	///// REGISTRATION /////
	if (typeof(RegistNaviBt1)!='undefined')
	{
		RegistNaviBt1.innerHTML = reginaviJ1;
	}

	if (typeof(RegistNaviBt3)!='undefined')
	{
		RegistNaviBt3.innerHTML = reginaviJ3;
	}


	///// SEARCH /////
	if (typeof(SearchNaviBt1)!='undefined')
	{
		SearchNaviBt1.innerHTML = schnaviJ1;
	}

	if (typeof(SearchNaviBt3)!='undefined')
	{
		SearchNaviBt3.innerHTML = schnaviJ3;
	}


	///// LIST OUTPUT /////
	if (typeof(ListExNaviBt1)!='undefined')
	{
		ListExNaviBt1.innerHTML = listoutnaviJ1;
	}

	if (typeof(ListExNaviBt3)!='undefined')
	{
		ListExNaviBt3.innerHTML = listoutnaviJ3;
	}


	///// DATA EXPORT /////
	if (typeof(DataExNaviBt1)!='undefined')
	{
		DataExNaviBt1.innerHTML = dataexnaviJ1;
	}

	if (typeof(DataExNaviBt3)!='undefined')
	{
		DataExNaviBt3.innerHTML = dataexnaviJ3;
	}


	///// MASTER /////
	if (typeof(MasterNaviBt1)!='undefined')
	{
		MasterNaviBt1.innerHTML = mstnaviJ1;
	}

	if (typeof(MasterNaviBt3)!='undefined')
	{
		MasterNaviBt3.innerHTML = mstnaviJ3;
	}


	///// USER INFO /////
	if (typeof(UserInfoNaviBt1)!='undefined')
	{
		UserInfoNaviBt1.innerHTML = infonaviJ1;
	}

	if (typeof(UserInfoNaviBt3)!='undefined')
	{
		UserInfoNaviBt3.innerHTML = infonaviJ3;
	}


	// メッセージ
	if( typeof(MessageBt1) != 'undefined' )
	{
		MessageBt1.innerHTML = messagenaviJ1;
	}

	if( typeof(MessageBt3) != 'undefined' )
	{
		MessageBt3.innerHTML = messagenaviJ3;
	}


	// サーバ
	if( typeof(ServerBt1) != 'undefined' )
	{
		ServerBt1.innerHTML = servernaviJ1;
	}

	if( typeof(ServerBt3) != 'undefined' )
	{
		ServerBt3.innerHTML = servernaviJ3;
	}


	// 管理者メール
	if( typeof(EmailBt1) != 'undefined' )
	{
		EmailBt1.innerHTML = emailnaviJ1;
	}

	if( typeof(EmailBt3) != 'undefined' )
	{
		EmailBt3.innerHTML = emailnaviJ3;
	}


	// セッション
	if( typeof(SessionBt1) != 'undefined' )
	{
		SessionBt1.innerHTML = sessionnaviJ1;
	}

	if( typeof(SessionBt3) != 'undefined' )
	{
		SessionBt3.innerHTML = sessionnaviJ3;
	}


	// 締め処理
	if( typeof(ClosedBt) != 'undefined' )
	{
		ClosedBt.innerHTML = closednaviBtJ1;
	}



	// アップロード
	if (typeof(UploadNaviBt1)!='undefined')
	{
		UploadNaviBt1.innerHTML = uploadNaviJ1;
	}



	// 登録2
	if( typeof( RegistNaviBtA ) != 'undefined' )
	{
		RegistNaviBtA.innerHTML = reginaviJA;
	}
	if( typeof( RegistNaviBtC ) != 'undefined' )
	{
		RegistNaviBtC.innerHTML = reginaviJC;
	}

	// 検索2
	if( typeof( SearchNaviBtA ) != 'undefined' )
	{
		SearchNaviBtA.innerHTML = schnaviJA;
	}
	if( typeof( SearchNaviBtC ) != 'undefined' )
	{
		SearchNaviBtC.innerHTML = schnaviJC;
	}



	// 登録3
	if( typeof( RegistNaviBtAA ) != 'undefined' )
	{
		RegistNaviBtAA.innerHTML = reginaviJAA;
	}
	if( typeof( RegistNaviBtCC ) != 'undefined' )
	{
		RegistNaviBtCC.innerHTML = reginaviJCC;
	}

	// 検索3
	if( typeof( SearchNaviBtAA ) != 'undefined' )
	{
		SearchNaviBtAA.innerHTML = schnaviJAA;
	}
	if( typeof( SearchNaviBtCC ) != 'undefined' )
	{
		SearchNaviBtCC.innerHTML = schnaviJCC;
	}


	// 登録4
	if( typeof( RegistNaviBtAAA ) != 'undefined' )
	{
		RegistNaviBtAAA.innerHTML = reginaviJAAA;
	}
	if( typeof( RegistNaviBtCCC ) != 'undefined' )
	{
		RegistNaviBtCCC.innerHTML = reginaviJCCC;
	}

	// 検索4
	if( typeof( SearchNaviBtAAA ) != 'undefined' )
	{
		SearchNaviBtAAA.innerHTML = schnaviJAAA;
	}
	if( typeof( SearchNaviBtCCC ) != 'undefined' )
	{
		SearchNaviBtCCC.innerHTML = schnaviJCCC;
	}


	// 登録5
	if( typeof( RegistNaviBtAAAA ) != 'undefined' )
	{
		RegistNaviBtAAAA.innerHTML = reginaviJAAAA;
	}
	if( typeof( RegistNaviBtCCCC ) != 'undefined' )
	{
		RegistNaviBtCCCC.innerHTML = reginaviJCCCC;
	}

	// 検索5
	if( typeof( SearchNaviBtAAAA ) != 'undefined' )
	{
		SearchNaviBtAAAA.innerHTML = schnaviJAAAA;
	}
	if( typeof( SearchNaviBtCCCC ) != 'undefined' )
	{
		SearchNaviBtCCC.innerHTML = schnaviJCCCC;
	}



	return false;
}
