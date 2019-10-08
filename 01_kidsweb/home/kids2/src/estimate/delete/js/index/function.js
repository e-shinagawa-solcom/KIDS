function fncWindowClose() {      
    window.close();
}

function fncDeleteProcess() {      
    document.formAction.submit();
}

function fncAlphaOff( obj )
{
	obj.style.filter = 'alpha(opacity=100)' ;
}

function fncDeleteButton( strMode , obj )
{
	switch( strMode )
	{
		case 'offJ':
			obj.src = deleteJ1;
			break;

		case 'onJ':
			obj.src = deleteJ2;
			break;

		case 'downJ':
			obj.src = deleteJ3;
			break;

		default:
			break;
	}
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
// ºï½ügif
var deleteJ1 = '/img/type01/estimate/delete/delete/delete_off_ja_bt.gif';
var deleteJ2 = '/img/type01/estimate/delete/delete/delete_off_on_ja_bt.gif';
var deleteJ3 = '/img/type01/estimate/delete/delete/delete_on_ja_bt.gif';


// ÊÄ¤¸¤ëgif
var closeJ1 = '/img/type01/estimate/delete/close/close_off_ja_bt.gif';
var closeJ2 = '/img/type01/estimate/delete/close/close_off_on_ja_bt.gif';
var closeJ3 = '/img/type01/estimate/delete/close/close_on_ja_bt.gif';