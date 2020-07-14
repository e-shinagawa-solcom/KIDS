window.onbeforeunload = function () {
	var sessionID = $('input[name="strSessionID"]').val();
	var estimateNo = $('input[name="estimateNo"]').val();
	var sortList = getUrlVars(window.opener.location)["sortList"];
	var actionUrl = '/estimate/preview/index.php?strSessionID=' + sessionID +'&estimateNo=' + estimateNo +'&sortList=' + sortList;

	window.opener.location.href = actionUrl;
}

function fncAlphaOff( obj )
{
	obj.style.filter = 'alpha(opacity=100)' ;
}

function fncCloseButton( strMode , obj )
{
	switch( strMode )
	{
		case 'offJ':
			obj.src = closeJ1;
			break;

		case 'onJ':
			obj.src = closeJ2;
			break;

		case 'downJ':
			obj.src = closeJ3;
			break;

		default:
			break;
	}
}

// 閉じるgif
var closeJ1 = '/img/type01/estimate/regist/close/close_off_ja_bt.gif';
var closeJ2 = '/img/type01/estimate/regist/close/close_off_on_ja_bt.gif';
var closeJ3 = '/img/type01/estimate/regist/close/close_on_ja_bt.gif';
