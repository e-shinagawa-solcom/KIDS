/************************* [ ���롼�ץޥ��� ] *************************/




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
		fncChangeObjectIdModule( Array( 'Input0' , 'Input1' , 'Input2' , 'Input3' , 'Input4' , 'Input5' , 'Input6' ) ,

								 Array( 'TxtDis05L' , 'TxtSlt38' , 'Txt25L' , 'CheckBox14' , 'Txt02L' , 'Txt25L' , 'Txt08L' ) );
	}
	// [����]
	else if( g_strMode == 'fix' )
	{
		// ���֥������Ȥ�ID�Ѵ�
		fncChangeObjectIdModule( Array( 'Input0' , 'Input1' , 'Input2' , 'Input3' , 'Input4' , 'Input5' , 'Input6' ) ,

								 Array( 'TxtDis05L' , 'TxtSlt38' , 'Txt25L' , 'CheckBox14' , 'Txt02L' , 'Txt25L' , 'Txt08L' ) );
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
////////// �إå������᡼�������� //////////
var headerAJ = '<img src="' + h_groupJ + '" width="949" height="30" border="0" alt="���롼�ץޥ���">';
var headerAE = '<img src="' + h_groupE + '" width="949" height="30" border="0" alt="GROUP MASTER">';








//////////////////////////////////////////////////////////////////
////////// ���ܸ졦�Ѹ����إ⥸�塼�� //////////
function ChgEtoJ( g_lngCode )
{

	// �����꡼�ơ��֥�ԣϣк�ɸ
	if( typeof(QueryTable) != 'undefined' )
	{
		QueryTable.style.top = 300;
	}


	// �Ѹ�
	if ( g_lngCode == 0 )
	{

		// �ɲåܥ��󥤥᡼���񤭽Ф�
		if( typeof(MasterAddBt) != 'undefined' )
		{
			MasterAddBt.innerHTML = maddbtE1;
		}


		// �����꡼�ܥ���ơ��֥�񤭽Ф�
		if( typeof(fncTitleOutput) != 'undefined' )
		{
			fncTitleOutput( 0 );
		}



		// �����̾
		Column0.innerText	= 'Group code';
		Column1.innerText	= 'Company code';
		Column2.innerText	= 'Group name';
		Column3.innerText	= 'Group permission';
		Column4.innerText	= 'Group display code';
		Column5.innerText	= 'Group display name';
		Column6.innerText	= 'Group display color';


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

		// �ɲåܥ��󥤥᡼���񤭽Ф�
		if( typeof(MasterAddBt) != 'undefined' )
		{
			MasterAddBt.innerHTML = maddbtJ1;
		}


		// �����꡼�ܥ���ơ��֥�񤭽Ф�
		if( typeof(fncTitleOutput) != 'undefined' )
		{
			fncTitleOutput( 1 );
		}



		// �����̾
		Column0.innerText	= '���롼�ץ�����';
		Column1.innerText	= '��ҥ�����';
		Column2.innerText	= '���롼��̾��';
		Column3.innerText	= 'ɽ�����롼�׵���';
		Column4.innerText	= 'ɽ�����롼�ץ�����';
		Column5.innerText	= 'ɽ�����롼��̾��';
		Column6.innerText	= 'ɽ�����롼�ץ��顼';


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