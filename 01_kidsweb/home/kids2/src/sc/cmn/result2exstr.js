
var DeleteBtJ1 = '<a href="#"><img onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onmouseover="DeleteJOn(this);" onmouseout="DeleteJOff(this);" src="' + delete1J + '" width="72" height="20" border="0" alt="���"></a>';
var CloseBtJ1 = '<a href="#"><img onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onmouseover="CloseJOn(this);" onmouseout="CloseJOff(this);" src="' + close1J + '" width="72" height="20" border="0" alt="�Ĥ���"></a>';

// ���ܸ�Ѹ�����
function fncChgEtoJ( strMode )
{
	if (strMode == 'detail'){
		// -----------------
		//   �ܺٲ���
		// -----------------
		// �������������ȥ�
		ControlTitle.innerText = '�ܺٳ�ǧ';
		// ��å����������ȥ뢪�ʤ�
		document.getElementById("MessageTitle").style.display ="none";
		// �Ĥ���ܥ����ɲ�
		CloseBt.innerHTML = CloseBtJ1;
	}else if (strMode == 'delete'){
		// -----------------
		//   �������
		// -----------------
		// �������������ȥ�
		ControlTitle.innerText = '�����ǧ';
		// ��å����������ȥ�
		document.getElementById("MessageTitle").style.display ="block";
		MessageTitle.innerText = '������ޤ�����';
		// �Ĥ���ܥ����ɲ�
		CloseBt.innerHTML = CloseBtJ1;
		// ����ܥ����ɲ�
		DeleteBt.innerHTML = DeleteBtJ1;
	}

	return false;
}
