
/**
*
*	@charset	: euc-jp
*/



	// ��������å����ե����륢�åץ���
	function fncCheckField()
	{
		if( document.exc_upload.excel_file.value.length == 0 )
		{
			alert( '�ե��������ꤷ�Ƥ���������' );
			return false;
		}

		window.exc_upload.submit();
		return true;
	}

	// ��������ɽ������
	function fncFileUpload( lngValue, objFrm )
	{
		// ��������ɽ���ե饰��̵���ξ��
		if( lngValue != "1" )
		{
			return false;
		}
		else
		{
			// ��̥�������Ÿ��
			GoResult( objFrm, '/result/index.html', '/result/ifrm.html', 'ResultIframe', 'YES' );
		}

		return true;
	}
