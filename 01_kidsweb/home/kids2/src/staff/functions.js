<!--


//---------------------------------------------------
// ���� : �����ޡ���ư��Υ��᡼�������ؤ��⥸�塼��
//---------------------------------------------------
function fncModuleChangePict()
{
	pictTomita.innerHTML = imgTomita1;
}


//---------------------------------------------------
// ���� : �����ޡ�����⥸�塼��
//---------------------------------------------------
function fncModuleSetTimer( lngTime )
{
	setTimeout( 'fncModuleChangePict()' , lngTime );
}


//---------------------------------------------------
// ���� : �����ޡ��ƽдؿ�
//---------------------------------------------------
function fncBeepOn()
{

	// ������ɺ���
	BeepSound.src='/error/lupin.wav';

	// ���᡼�������ؤ�
	pictTomita.innerHTML = imgTomita2;

	// �����ޡ���ư
	fncModuleSetTimer( 2800 );

	return false;
}


//-->