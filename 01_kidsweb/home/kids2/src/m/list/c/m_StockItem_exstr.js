<!--
/************************* [ �������ʥޥ��� ] *************************/





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
		fncChangeObjectIdModule( Array( 'Input0' , 'Input1' , 'Input2' , 'Input3', 'Input4', 'Input5', 'Input6', 'Input7' ) ,
								 Array( 'Txt03L' , '' , '' , 'Txt40L', '', '', '' , '' ) );
	}
	// [����]
	else if( g_strMode == 'fix' )
	{
		// ���֥������Ȥ�ID�Ѵ�
		fncChangeObjectIdModule( Array( 'Input0' , 'Input1' , 'Input2' , 'Input3', 'Input4', 'Input5', 'Input6', 'Input7' ) ,
								 Array( 'TxtDis03L' , '' , '' , 'Txt40L', '', '', '', ''  ) );
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
var headerAJ = '<img src="' + h_goodspartsJ + '" width="949" height="30" border="0" alt="�������ʥޥ���">';
var headerAE = '<img src="' + h_goodspartsE + '" width="949" height="30" border="0" alt="GOODS PARTS MASTER">';










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


		// �����꡼�ܥ���ơ��֥�񤭽Ф�
		if( typeof(fncTitleOutput) != 'undefined' )
		{
			fncTitleOutput( 0 );
		}


		// �ɲåܥ��󥤥᡼���񤭽Ф�
		if( typeof(window.top.MasterAddBt) != 'undefined' )
		{
			window.top.MasterAddBt.innerHTML = maddbtE1;
		}



		// �����̾
		if( typeof(Column0) != 'undefined' )
		{
			Column0.innerText	= 'Goods parts code';
		}

		if( typeof(Column1) != 'undefined' )
		{
			Column1.innerText	= 'Goods class name';
		}

		if( typeof(Column2) != 'undefined' )
		{
			Column2.innerText	= 'Goods parts name';
		}

		if( typeof(Column3) != 'undefined' )
		{
			Column3.innerText	= 'Goods parts';
		}

		if( typeof(Column4) != 'undefined' )
		{
			Column4.innerText	= 'Goods Stock Display';
		}

		if( typeof(Column5) != 'undefined' )
		{
			Column5.innerText	= 'Goods Invalid';
		}

		if( typeof(Column6) != 'undefined' )
		{
			Column6.innerText	= 'Goods Estimate Display';
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


		// �����꡼�ܥ���ơ��֥�񤭽Ф�
		if( typeof(fncTitleOutput) != 'undefined' )
		{
			fncTitleOutput( 1 );
		}


		// �ɲåܥ��󥤥᡼���񤭽Ф�
		if( typeof(window.top.MasterAddBt) != 'undefined' )
		{
			window.top.MasterAddBt.innerHTML = maddbtJ1;
		}



		// �����̾
		if( typeof(Column0) != 'undefined' )
		{
			Column0.innerText	= '�������ʥ�����';
		}

		if( typeof(Column1) != 'undefined' )
		{
			Column1.innerText	= '������ʬ̾��';
		}

		if( typeof(Column2) != 'undefined' )
		{
			Column2.innerText	= '��������̾��';
		}

		if( typeof(Column3) != 'undefined' )
		{
			Column3.innerText	= '��������̾��';
		}

		if( typeof(Column4) != 'undefined' )
		{
			Column4.innerText	= 'ȯ������ɽ��';
		}

		if( typeof(Column5) != 'undefined' )
		{
			Column5.innerText	= '���';
		}

		if( typeof(Column6) != 'undefined' )
		{
			Column6.innerText	= '���Ѹ���ɽ��';
		}

		if( typeof(Column7) != 'undefined' )
		{
			Column6.innerText	= '�оݥ��ꥢ';
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


//-->