

//@-------------------------------------------------------------------------------------------------------------------
/**
* �ե����복�� : ��������γƽ����Ѵؿ���
*
*
*
* ���� : ��������˻��Ѥ���ؿ���������Ƥ��롣
*
* @package k.i.d.s.
* @license http://www.wiseknot.co.jp/
* @copyright Copyright &copy; 2004, Wiseknot, Inc.
* @author Ryosuke Tomita <r-tomita@wiseknot.co.jp>
* @access public
* @version 0.1
*/ 
//--------------------------------------------------------------------------------------------------------------------




















//@-------------------------------------------------------------------------------------------------------------------
/**
* ���� : �֥饦�����С������OS�Υ����å��ؿ�
*
* ���� : �ȥåץڡ����ɤ߹��߻��˥֥饦�����С������OS�Υ����å���Ԥ���
*        Windows Internet Explorer 6 �δĶ��ʳ��ϥ��顼�ڡ����ذ�ư�����롣
*
* �о� : K.I.D.S.�����ƥ�ȥåץڡ���
*
* �����ؿ� : [fncCheckDisplaySizeModule] �ǥ����ץ쥤�������Υ����å��⥸�塼��
*
* @event [onload] : body
*/
//--------------------------------------------------------------------------------------------------------------------
function fncNavigatorCheck()
{
	// �ǥХå�
//	alert( '[platform] ' +  navigator.platform + '\n[appName] ' + navigator.appName + '\n[appVersion] ' + navigator.appVersion );

	// Windows Internet Explorer 6 �ʳ��δĶ��ξ��
	if ( !((navigator.platform.indexOf('Win') > -1) &&
			(navigator.appName.indexOf('Microsoft') > -1) &&
			((navigator.appVersion.indexOf('MSIE 7') > -1)||
			(navigator.appVersion.indexOf('MSIE 8') > -1)||
			(navigator.appVersion.indexOf('MSIE 9') > -1)||
			(navigator.appVersion.indexOf('MSIE 6') > -1))) )
	{
		// ���顼�ڡ����ذ�ư
		location.href = '/error/env.html';
	}
	else
	{
		// ������ɥ��������Υ����å�
		fncCheckDisplaySizeModule();
	}

	return false;
}


//@-------------------------------------------------------------------------------------------------------------------
/**
* ���� : �ǥ����ץ쥤�������Υ����å��⥸�塼��
*
* ���� : �ȥåץڡ����ɤ߹��߻��˥ǥ����ץ쥤�������Υ����å���Ԥ���
*        �����Ͱʲ��ξ��ϥ�å�������ɽ�������롣
*
* �о� : [fncNavigatorCheck] �֥饦�����С������OS�Υ����å��ؿ�
*
* @param [lngW] : �����ͤβ�������
* @param [lngH] : �����ͤνĥ�����
*/
//--------------------------------------------------------------------------------------------------------------------
function fncCheckDisplaySizeModule()
{
	var lngW = 1024; // �����Ͳ�������
	var lngH = 768;  // �����ͽĥ�����

	var lngWdisp = screen.width;  // ������ɥ�������������
	var lngHdisp = screen.height; // ������ɥ��ĥ���������

	if ( ( lngWdisp < lngW ) || ( lngHdisp < lngH ) )
	{
		EnterButton.style.display = 'none';  // [ENTER]�ܥ������ɽ��
		MessageDisp.style.display = 'block'; // ���顼��å�������ɽ��
	}

	return false;
}











/*
function LoginCheck() {

	var strUid = document.all.strUserID.value;
	var strPasswd = document.all.strPassword.value;

	location.href = "/login/login.php?strUserID=" + strUid + "&strPassword=" + strPasswd;

	return false;

}
*/

function LoginCheck() {

	document.frmLogin.action = "/login/login.php";
	document.frmLogin.submit();

	return false;
}





////////////////////////// GO MENU //////////////////////////
/*
function GoMENU() {

	var menuWin = "Menu";

	if(window.name != menuWin)
	{ 
		window.opener = window.open( '/navi/index.html', 'menuWin','fullscreen=yes');

		window.close(); 
	}

	return false;

}
*/

/*
function GoMENU() {

	var menuWin = "Menu";

	if(window.name != menuWin)
	{ 
		window.opener = window.open( '/navi/index.html', 'menuWin','width=1012,height=689,status=yes,scrollbars=yes,directories=no,menubar=no,resizable=yes,location=no,toolbar=no,left=0,top=0');

		window.close(); 
	}

	return false;

}
*/


function GoMENU()
{

	location.href = '/menu/index.html';

	return false;

}








////////////////////////// PASSWORD REMINDER //////////////////////////
function RemindSet() {

	document.frmLogin.action = "/remind/index.html";
	document.frmLogin.submit();

	return false;
}