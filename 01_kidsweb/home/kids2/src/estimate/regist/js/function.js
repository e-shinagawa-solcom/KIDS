function fncWindowClose() {
    var res = confirm("この画面を閉じて見積原価計算書登録処理を中止します。\nよろしいですか？");
    if( res == true ) {
        // OKなら移動       
        window.close();
    }
}

function fncFileProcess() {
    var res = confirm("見積原価計算書を登録してもよろしいですか？");
    if( res == true ) {
        // OKなら移動
        document.formAction.submit();
    }
}

function fncAlphaOff( obj )
{
	obj.style.filter = 'alpha(opacity=100)' ;
}

function fncRegistButton( strMode , obj )
{
	switch( strMode )
	{
		case 'offJ':
			obj.src = registJ1;
			break;

		case 'onJ':
			obj.src = registJ2;
			break;

		case 'offE':
			obj.src = registE1;
			break;

		case 'onE':
			obj.src = registE2;
			break;

		case 'downJ':
			obj.src = registJ3;
			break;

		case 'downE':
			obj.src = registE3;
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

		case 'offE':
			obj.src = closeE1;
			break;

		case 'onE':
			obj.src = closeE2;
			break;

		case 'downJ':
			obj.src = closeJ3;
			break;

		case 'downE':
			obj.src = closeE3;
			break;

		default:
			break;
	}
}
// 登録gif
var registJ1 = '/img/type01/upload2/regist/regist_gray_off_ja_bt.gif';
var registJ2 = '/img/type01/upload2/regist/regist_gray_off_on_ja_bt.gif';
var registJ3 = '/img/type01/upload2/regist/regist_gray_on_ja_bt.gif';
var registE1 = '/img/type01/upload2/regist/regist_gray_off_en_bt.gif';
var registE2 = '/img/type01/upload2/regist/regist_gray_off_on_en_bt.gif';
var registE3 = '/img/type01/upload2/regist/regist_gray_on_en_bt.gif';

// 閉じるgif
var closeJ1 = '/img/type01/upload2/close/close_blown_off_ja_bt.gif';
var closeJ2 = '/img/type01/upload2/close/close_blown_off_on_ja_bt.gif';
var closeJ3 = '/img/type01/upload2/close/close_blown_on_ja_bt.gif';
var closeE1 = '/img/type01/upload2/close/close_blown_off_en_bt.gif';
var closeE2 = '/img/type01/upload2/close/close_blown_off_on_en_bt.gif';
var closeE3 = '/img/type01/upload2/close/close_blown_on_en_bt.gif';