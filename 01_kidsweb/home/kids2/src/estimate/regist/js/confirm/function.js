function fncWindowClose() {
    var res = confirm("���β��̤��Ĥ��Ƹ��Ѹ����׻�����Ͽ��������ߤ��ޤ���\n������Ǥ�����");
    if( res == true ) {
        // OK�ʤ��ư       
        window.close();
    }
}

function fncFileProcess() {
    var res = confirm("���Ѹ����׻������Ͽ���Ƥ������Ǥ�����");
    if( res == true ) {
        // OK�ʤ��ư       
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

		case 'downJ':
			obj.src = registJ3;
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
// ��Ͽgif
var registJ1 = '/img/type01/estimate/regist/regist/regist_off_ja_bt.gif';
var registJ2 = '/img/type01/estimate/regist/regist/regist_off_on_ja_bt.gif';
var registJ3 = '/img/type01/estimate/regist/regist/regist_on_ja_bt.gif';


// �Ĥ���gif
var closeJ1 = '/img/type01/estimate/regist/close/close_off_ja_bt.gif';
var closeJ2 = '/img/type01/estimate/regist/close/close_off_on_ja_bt.gif';
var closeJ3 = '/img/type01/estimate/regist/close/close_on_ja_bt.gif';