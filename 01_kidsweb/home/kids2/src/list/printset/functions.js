<!--


//-------------------------------------------------------
// ����    : ����ե졼���⥳��ƥ�Ĥΰ����ؿ�
// �ʎߎ׎Ҏ����� : objName ,  �����оݥե졼��̾( parent.obj )
//-------------------------------------------------------
function fncPrintFrame( objName )
{
	objName.focus();
	objName.print();

	return false;
}



//-------------------------------------------------------
// Ŭ�ѡ���PRINT�ץܥ���
//-------------------------------------------------------
var printbt1 = '/img/type01/list/print_off_bt.gif';
var printbt2 = '/img/type01/list/print_off_on_bt.gif';

function fncPrintButton( strMode , obj )
{
	switch( strMode )
	{
		case 'off':
			obj.src = printbt1;
			break;

		case 'on':
			obj.src = printbt2;
			break;

		default:
			break;
	}

	return false;
}

//-----------------------------------------------------------
// ���� : �ޥ�����������˥��֥������ȤΥ���ե��ͤ��ѹ�����
//-----------------------------------------------------------
function fncAlphaOn( obj )
{
	obj.style.filter = 'alpha(opacity=50)' ;
}

//-----------------------------------------------------------
// ���� : �ޥ������å׻��˥��֥������ȤΥ���ե��ͤ��ѹ�����
//-----------------------------------------------------------
function fncAlphaOff( obj )
{
	obj.style.filter = 'alpha(opacity=100)' ;
}


//-->