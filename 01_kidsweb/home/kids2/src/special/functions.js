
//------------------------------------------------------------
// ���� : [TD]�طʿ��ѹ��ؿ�
//------------------------------------------------------------
function fncTdColorChange( strMode , obj )
{

	var defaultcolor = '#0b509f'; // �ǥե���ȿ� /* 6d8aab */
	var overcolor    = '#6d8aab'; // ���륪���С���
	var downcolor    = '#ea8555'; // �ޥ���������

	switch( strMode )
	{
		case 'off':
			obj.style.background = defaultcolor;
			break;

		case 'on':
			obj.style.background = overcolor;
			break;

		case 'down':
			obj.style.background = downcolor;
			break;

		default:
			break;
	}

	return false;
}



//------------------------------------------------------------
// ���� : [�ü�ʸ��]�򥯥�åץܡ��ɤ˥��ԡ�����ؿ�
//
// �ʎߎ׎Ҏ����� : strSpecialChar   , �ü�ʸ����������
//           objSpecialBuffer , �Хåե��ѥ��֥�������(hidden)
//------------------------------------------------------------
function fncSpecialCharCopy( strSpecialChar )
{
	// �ü�ʸ����Хåե��˳�Ǽ
	objSpecialBuffer.value = strSpecialChar;

	// �Хåե��Υƥ����ȥ�󥸤����
	var objSpecial = objSpecialBuffer.createTextRange();

	// ���ԡ������ΰ������
	objSpecial.moveStart( 'character' , 0 );
	objSpecial.moveEnd( 'character' );

	//alert(objSpecial);

	// ����åץܡ��ɤ˥��ԡ�
	objSpecial.execCommand("copy");

	// �Хåե��ν����
	objSpecialBuffer.value = '';

	return false;
}