

//-------------------------------------------------------------------
// ���� : �����Х��ѿ�[g_strEmail]�������ϺѤ�EmailAddress�ͤμ���
//-------------------------------------------------------------------
var strEmailAddr = parent.g_strEmail;



//-------------------------------------------------------------------
// ���� : ���֥ߥåȽ����ؿ�
//-------------------------------------------------------------------
function fncRemindSubmit( objFrm , objName )
{
	// EmailAddress�ͤμ���
	var strEmail = objName.value;

	// EmailAddress�η��������������
	if( strEmail.match(/.*@.*\..*/i) )
	{
		// �����Х��ѿ�[g_strEmail]�˳�Ǽ
		parent.g_strEmail = objName.value;

		// ���֥ߥåȽ���
		objFrm.submit();
	}
}



//-------------------------------------------------------------------
// ���� : ��å�������Ф������ؿ�
//-------------------------------------------------------------------
function fncSetEmailAddress()
{
	// �ѿ�[strEmailAddr]�����ͤ����
	var strEmail = strEmailAddr;

	if( typeof(strEmail) != 'undefined' )
	{
		// ��å�������Ф�
		Message2.innerHTML = '<b>COMPLETED :</b><br>�ѥ����ǧ�ھ��� [ <b>' + strEmail + '</b> ] ������������ޤ�����';
		//Message2.innerHTML = '<b>COMPLETED :</b><br>�ѥ����ǧ�ھ��󤬡����Ϥ����᡼�륢�ɥ쥹������������ޤ�����';
	}
	else
	{
		// ��å�������Ф�
		Message2.innerHTML = '<b>ERROR :</b><br>�᡼�륢�ɥ쥹�η����������Ǥ���';
	}

	// �ѿ�[strEmail]�ν����
	parent.g_strEmail = '';
}



//-------------------------------------------------------------------
// ���� : [ENTER]�������������֥ߥåȽ����ؿ�
//-------------------------------------------------------------------
window.document.onkeydown=fncEnterKeyDown;

function fncEnterKeyDown( e )
{
	if( typeof(SubmitButton) != 'undefined' )
	{
		if( window.event.keyCode == 13 ||
			window.event.keyCode == 14 )
		{

			fncAlphaOn( document.all.submitbutton );
			fncRemindSubmit( window.frmRemind , document.all.strMailAddress );

		}
	}

}