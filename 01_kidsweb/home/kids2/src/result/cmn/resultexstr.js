

// �����꡼�ܥ���ơ��֥�񤭽Ф��⥸�塼��
function fncTitleOutput( lngCode )
{

	// �������ܥ���(���ܸ�)
	var closebtJ = '<a href="#" onclick="window.close();"><img onmouseover="CloseJOn( this );" onmouseout="CloseJOff( this );" src="/img/type01/cmn/seg/close_off_ja_bt.gif" width="72" height="20" border="0" alt="CLOSE"></a>';
	// �������ܥ���(�Ѹ�)
	var closebtE = '<a href="#" onclick="window.close();"><img onmouseover="CloseEOn( this );" onmouseout="CloseEOff( this );" src="/img/type01/cmn/seg/close_off_en_bt.gif" width="72" height="20" border="0" alt="CLOSE"></a>';


	if( lngCode == 0 )
	{
		objQuery.innerHTML = closebtE;
	}
	else if( lngCode == 1 )
	{
		objQuery.innerHTML = closebtJ;
	}

	return false;
}





// ���ܸ�Ѹ�����
function fncChgEtoJ()
{

	// [lngLanguageCode]�ͼ���
	var g_lngCode = window.lngLangCode.value;

	// �Ѹ�����
	if( g_lngCode == 0 )
	{

		fncTitleOutput( 0 );

//		strError.innerText = 'SORRY: There is nothing corresponding.';

	}

	// ���ܸ�����
	else if( g_lngCode == 1 )
	{

		fncTitleOutput( 1 );

//		strError.innerText = 'SORRY: ���������Τ�����ޤ���';

	}

	return false;
}