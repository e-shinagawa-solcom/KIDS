
var DeleteBtJ1 = '<a href="#"><img onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onmouseover="DeleteJOn(this);" onmouseout="DeleteJOff(this);" src="' + delete1J + '" width="72" height="20" border="0" alt="���"></a>';
var CloseBtJ1 = '<a href="#"><img onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onmouseover="CloseJOn(this);" onmouseout="CloseJOff(this);" src="' + close1J + '" width="72" height="20" border="0" alt="�Ĥ���"></a>';

// ���ܸ�Ѹ�����
function fncChgEtoJ( strMode )
{
	//TODO:����Ⱦܺ٤�ʬ��
	ControlTitle.innerText = '�ܺٳ�ǧ';

	// ����ܥ�������
	DeleteBt.innerHTML = DeleteBtJ1;

	// �Ĥ���ܥ�������
	CloseBt.innerHTML = CloseBtJ1;

	return false;
}
