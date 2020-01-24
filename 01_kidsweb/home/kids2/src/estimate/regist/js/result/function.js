function fncAlphaOff( obj )
{
	obj.style.filter = 'alpha(opacity=100)' ;
}

function fncPreviewButton( strMode , obj )
{
	switch( strMode )
	{
		case 'offJ':
			obj.src = previewJ1;
			break;

		case 'onJ':
			obj.src = previewJ2;
			break;

		case 'downJ':
			obj.src = previewJ3;
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
// プレビューgif
var previewJ1 = '/img/type01/estimate/regist/preview/preview_off_bt.gif';
var previewJ2 = '/img/type01/estimate/regist/preview/preview_off_on_bt.gif';
var previewJ3 = '/img/type01/estimate/regist/preview/preview_on_bt.gif';

// 閉じるgif
var closeJ1 = '/img/type01/estimate/regist/close/close_off_ja_bt.gif';
var closeJ2 = '/img/type01/estimate/regist/close/close_off_on_ja_bt.gif';
var closeJ3 = '/img/type01/estimate/regist/close/close_on_ja_bt.gif';