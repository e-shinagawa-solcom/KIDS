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

// ÊÄ¤¸¤ëgif
var closeJ1 = '/img/type01/estimate/regist/close/close_off_ja_bt.gif';
var closeJ2 = '/img/type01/estimate/regist/close/close_off_on_ja_bt.gif';
var closeJ3 = '/img/type01/estimate/regist/close/close_on_ja_bt.gif';