
//-------------------------------------------------------
// ���� : ���ǥ��åȥǡ���Ŭ�ѥܥ������
//-------------------------------------------------------
var EditCloseBtJ1 = '<a href="#"><img onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onmouseover="CloseJOn( this );" onmouseout="CloseJOff( this );fncAlphaOff( this );" src="/img/type01/cmn/seg/close_off_ja_bt.gif" width="72" height="20" border="0" alt="�Ĥ���"></a>';

var EditCloseBtE1 = '<a href="#"><img onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onmouseover="CloseEOn( this );" onmouseout="CloseEOff( this );fncAlphaOff( this );" src="/img/type01/cmn/seg/close_off_en_bt.gif" width="72" height="20" border="0" alt="CLOSE"></a>';





//-------------------------------------------------------
// ���� : ���������ܥ������
//-------------------------------------------------------
var EditInsertBtJ1 = '<a href="#"><img onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onmouseover="fncInsertButton( \'onJ\' , this );" onmouseout="fncInsertButton( \'offJ\' , this );fncAlphaOff( this );" src="' + insertbtJ1 + '" width="72" height="20" border="0" alt="����"></a>';

var EditInsertBtE1 = '<a href="#"><img onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onmouseover="fncInsertButton( \'onE\' , this );" onmouseout="fncInsertButton( \'offE\' , this );fncAlphaOff( this );" src="' + insertbtE1 + '" width="72" height="20" border="0" alt="INSERT"></a>';





//-------------------------------------------------------
// ���� : ���ǥ��åȥǡ���Ŭ�ѥܥ��󡦲��������ܥ������ܸ�Ѹ����شؿ�
//-------------------------------------------------------
function fncEditDataSet( lngLanguageCode )
{

	if( lngLanguageCode == 0 )
	{
		strImages.innerText    = 'Images';
		EditCloseBt.innerHTML  = EditCloseBtE1;
		EditInsertBt.innerHTML = EditInsertBtE1;
	}
	else if( lngLanguageCode == 1 )
	{
		strImages.innerText    = '����';
		EditCloseBt.innerHTML  = EditCloseBtJ1;
		EditInsertBt.innerHTML = EditInsertBtJ1;
	}

	return false;
}