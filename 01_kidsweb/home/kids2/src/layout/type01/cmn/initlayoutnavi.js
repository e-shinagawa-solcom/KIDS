<!--


///// LIST OUTPUT NAVI BT IMAGE /////
var listoutnaviJ1 = '<a href="#"><img onmouseover="ListOutJOn(this);" onmouseout="ListOutJOff(this);" src="' + listoutJ1 + '" width="151" height="25" border="0" alt="帳票出力"></a>';
var listoutnaviE1 = '<a href="#"><img onmouseover="ListOutEOn(this);" onmouseout="ListOutEOff(this);" src="' + listoutE1 + '" width="151" height="25" border="0" alt="LIST OUTPUT"></a>';


///// DATA EXPORT NAVI BT IMAGE /////
var dataexnaviJ1 = '<a href="#"><img onmouseover="DataExJOn(this);" onmouseout="DataExJOff(this);" src="' + dataexJ1 + '" width="151" height="25" border="0" alt="データエクスポート"></a>';
var dataexnaviE1 = '<a href="#"><img onmouseover="DataExEOn(this);" onmouseout="DataExEOff(this);" src="' + dataexE1 + '" width="151" height="25" border="0" alt="DATA EXPORT"></a>';


///// MASTER NAVI BT IMAGE /////
var mstnaviJ1 = '<a href="#"><img onmouseover="MstJOn(this);" onmouseout="MstJOff(this);" src="' + mstJ1 + '" width="151" height="25" border="0" alt="マスタ"></a>';
var mstnaviE1 = '<a href="#"><img onmouseover="MstEOn(this);" onmouseout="MstEOff(this);" src="' + mstE1 + '" width="151" height="25" border="0" alt="MASTER"></a>';


///// MAIN MENU NAVI BT IMAGE /////
var mainnaviJ1 = '<a href="#" onclick="GoNavi();"><img onmouseover="MainJOn(this);" onmouseout="MainJOff(this);" src="' + mainJ1 + '" width="151" height="25" border="0" alt="メインメニュー"></a>';
var mainnaviE1 = '<a href="#" onclick="GoNavi();"><img onmouseover="MainEOn(this);" onmouseout="MainEOff(this);" src="' + mainE1 + '" width="151" height="25" border="0" alt="MAIN MENU"></a>';






function initLayoutNavi()
{
	if (typeof(ListExNaviBt)!='undefined')
	{
		ListExNaviBt.innerHTML = listoutnaviJ1;
	}

	if (typeof(DataExNaviBt)!='undefined')
	{
		DataExNaviBt.innerHTML = dataexnaviJ1;
	}

	if (typeof(MasterNaviBt)!='undefined')
	{
		MasterNaviBt.innerHTML = mstnaviJ1;
	}

	if (typeof(MenuNaviBt)!='undefined')
	{
		MenuNaviBt.innerHTML = mainnaviJ1;
	}


	return false;
}


//-->
