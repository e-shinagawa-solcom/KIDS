/************************* [ ��������IP���ɥ쥹�ޥ��� ] *************************/




//////////////////////////////////////////////////////////////////
////////// [����][�ɲ�]�������֥������ȤΥ�����ɽ����ؿ� //////////
function fncEditObjectOnload( lngLangCode )
{

	// ���֥������Ȥμ�ư�쥤������
	fncInitLayoutObjectModule( objColumn , objInput  , 60 , 216 );


	// [�ɲ�]
	if( g_strMode == 'add' )
	{
		// ���֥������Ȥ�ID�Ѵ�
		fncChangeObjectIdModule( Array( 'Input0' , 'Input1' , 'Input2' ) ,
								 Array( 'TxtDis03L' , 'Txt40L' , 'Txt40L' ) );
	}
	// [����]
	else if( g_strMode == 'fix' )
	{
		// ���֥������Ȥ�ID�Ѵ�
		fncChangeObjectIdModule( Array( 'Input0' , 'Input1' , 'Input2' ) ,
								 Array( 'TxtDis03L' , 'Txt40L' , 'Txt40L' ) );
	}


	// [lngLanguageCode]�ͤν����
	var g_lngCode = '';

	// [lngLanguageCode]�ͤμ���
	g_lngCode = lngLangCode;


	// ���֥������Ȥ����ܸ졦�Ѹ�����
	ChgEtoJ( g_lngCode );


	return true;
}




//////////////////////////////////////////////////////////////////
////////// [CONFIRM]�������֥������ȤΥ�����ɽ����ؿ� //////////
function fncConfirmObjectOnload( strMode , lngLangCode )
{

	// �����⡼�ɤν����
	g_strMode = '';

	// �����⡼�ɤμ���
	g_strMode = strMode;


	// [lngLanguageCode]�ͤν����
	var g_lngCode = '';

	// [lngLanguageCode]�ͤμ���
	g_lngCode = lngLangCode;


	// ���֥������Ȥ����ܸ졦�Ѹ�����
	ChgEtoJ( g_lngCode );


	return true;
}










//////////////////////////////////////////////////////////////////
////////// �ɲåܥ����ɽ�� //////////
if( typeof(window.top.MasterAddBt) != 'undefined' )
{
	window.top.MasterAddBt.style.visibility = 'visible';
}









//////////////////////////////////////////////////////////////////
////////// �إå������᡼�������� //////////
var headerAJ = '<img src="' + h_ipJ + '" width="949" height="30" border="0" alt="��������IP���ɥ쥹�ޥ���">';
var headerAE = '<img src="' + h_ipE + '" width="949" height="30" border="0" alt="ACCESS IP ADDRESS MASTER">';



//////////////////////////////////////////////////////////////////
////////// ���ܸ졦�Ѹ����إ⥸�塼�� //////////
function ChgEtoJ( g_lngCode )
{


	// �����꡼�ơ��֥�ԣϣк�ɸ
	if( typeof(QueryTable) != 'undefined' )
	{
		QueryTable.style.top = 400;
	}



	// �Ѹ�
	if ( g_lngCode == 0 )
	{


		// �إå������᡼���񤭽Ф�
		if( typeof(window.top.SegAHeader) != 'undefined' )
		{
			window.top.SegAHeader.innerHTML = headerAE;
		}


		// �ɲåܥ��󥤥᡼���񤭽Ф�
		if( typeof(window.top.MasterAddBt) != 'undefined' )
		{
			window.top.MasterAddBt.innerHTML = maddbtE1;
		}


		// �����꡼�ܥ���ơ��֥�񤭽Ф�
		if( typeof(fncTitleOutput) != 'undefined' )
		{
			fncTitleOutput( 0 );
		}



		// �����̾
		if( typeof(Column0) != 'undefined' )
		{
			Column0.innerText	= 'Access IP address code';
		}

		if( typeof(Column1) != 'undefined' )
		{
			Column1.innerText	= 'Access IP address';
		}

		if( typeof(Column2) != 'undefined' )
		{
			Column2.innerText	= 'Remark';
		}

		// ����̾
		if( typeof(FixColumn) != 'undefined' )
		{
			FixColumn.innerText		= 'Fix';
		}

		if( typeof(DeleteColumn) != 'undefined' )
		{
			DeleteColumn.innerText 	= 'Delete';
		}

	}

	// ���ܸ�
	else if ( g_lngCode == 1 )
	{


		// �إå������᡼���񤭽Ф�
		if( typeof(window.top.SegAHeader) != 'undefined' )
		{
			window.top.SegAHeader.innerHTML = headerAJ;
		}


		// �ɲåܥ��󥤥᡼���񤭽Ф�
		if( typeof(window.top.MasterAddBt) != 'undefined' )
		{
			window.top.MasterAddBt.innerHTML = maddbtJ1;
		}


		// �����꡼�ܥ���ơ��֥�񤭽Ф�
		if( typeof(fncTitleOutput) != 'undefined' )
		{
			fncTitleOutput( 1 );
		}



		// �����̾
		if( typeof(Column0) != 'undefined' )
		{
			Column0.innerText	= '��������IP���ɥ쥹������';
		}

		if( typeof(Column1) != 'undefined' )
		{
			Column1.innerText	= '��������IP���ɥ쥹';
		}

		if( typeof(Column2) != 'undefined' )
		{
			Column2.innerText	= '����';
		}

		// ����̾
		if( typeof(FixColumn) != 'undefined' )
		{
			FixColumn.innerText		= '����';
		}

		if( typeof(DeleteColumn) != 'undefined' )
		{
			DeleteColumn.innerText	= '���';
		}

	}

	return false;

}
