<!--
/************************* [ ��ҥޥ��� ] *************************/



//////////////////////////////////////////////////////////////////
////////// [����][�ɲ�]�������֥������ȤΥ�����ɽ����ؿ� //////////
function fncEditObjectOnload( lngLangCode )
{
	// ���֥������Ȥμ�ư�쥤������
	fncInitLayoutObjectModule( objColumn , objInput  , 60 , 216 );


	// �ֲ��°�������ѥ쥤������
	Column19.style.padding = '50 0 0 8';
	Column19.style.height = '109';


	// [�ɲ�]
	if( g_strMode == 'add' )
	{
		// ���֥������Ȥ�ID�Ѵ�
		fncChangeObjectIdModule( Array( 'Input0' , 'Input1' , 'Input2' , 'Input3' , 'Input4' ,
										'Input5' , 'Input6' , 'Input7' , 'Input8' , 'Input9' ,
										'Input10' , 'Input11' , 'Input12' , 'Input13' , 'Input14' ,
										'Input15' , 'Input16' , 'Input17' , 'Input18' , 'Input19' ) ,

								 Array( 'TxtDis04L' , 'TxtSlt20' , 'TxtSlt20' , 'CheckBox14' , 'Txt40L' ,
										'CheckBox14' , 'Txt40L' , 'Txt40L' , 'Txt08L' , 'Txt40L' ,
										'Txt40L' , 'Txt40L' , 'Txt40L' , 'Txt40L' , 'Txt40L' ,
										'Txt40L' , 'Txt40L' , 'Txt02L' , 'TxtSlt20' , 'TxtSlt20' ) );
	}
	// [����]
	else if( g_strMode == 'fix' )
	{
		// ���֥������Ȥ�ID�Ѵ�
		fncChangeObjectIdModule( Array( 'Input0' , 'Input1' , 'Input2' , 'Input3' , 'Input4' ,
										'Input5' , 'Input6' , 'Input7' , 'Input8' , 'Input9' ,
										'Input10' , 'Input11' , 'Input12' , 'Input13' , 'Input14' ,
										'Input15' , 'Input16' , 'Input17' , 'Input18' , 'Input19' ) ,

								 Array( 'TxtDis04L' , 'TxtSlt20' , 'TxtSlt20' , 'CheckBox14' , 'Txt40L' ,
										'CheckBox14' , 'Txt40L' , 'Txt40L' , 'Txt08L' , 'Txt40L' ,
										'Txt40L' , 'Txt40L' , 'Txt40L' , 'Txt40L' , 'Txt40L' ,
										'Txt40L' , 'Txt40L' , 'Txt02L' , 'TxtSlt20' , 'TxtSlt20' ) );
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
var headerAJ = '<img src="' + h_companyJ + '" width="949" height="30" border="0" alt="��ҥޥ���">';
var headerAE = '<img src="' + h_companyE + '" width="949" height="30" border="0" alt="COMPANY MASTER">';








//////////////////////////////////////////////////////////////////
////////// ���ܸ졦�Ѹ����إ⥸�塼�� //////////
function ChgEtoJ( g_lngCode )
{

	// �����꡼�ơ��֥�ԣϣк�ɸ
	if( typeof(QueryTable) != 'undefined' )
	{
		QueryTable.style.top = 800;
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
		Column0.innerText	= 'Company code';
		Column1.innerText	= 'Country code';
		Column2.innerText	= 'Organization code';
		Column3.innerText	= 'Organization front';
		Column4.innerText	= 'Company name';
		Column5.innerText	= 'Company permission';
		Column6.innerText	= 'Company display code';
		Column7.innerText	= 'Company display name';
		Column8.innerText	= 'Postal code';
		Column9.innerText	= 'Address 1';
		Column10.innerText	= 'Address 2';
		Column11.innerText	= 'Address 3';
		Column12.innerText	= 'Address 4';
		Column13.innerText	= 'Tel 1';
		Column14.innerText	= 'Tel 2';
		Column15.innerText	= 'Fax 1';
		Column16.innerText	= 'Fax 2';
		Column17.innerText	= 'Distinct code';
		Column18.innerText	= 'Paystyle code';
		Column19.innerText	= 'Company attribute';

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
		Column0.innerText	= '��ҥ�����';
		Column1.innerText	= '�񥳡���';
		Column2.innerText	= '�ȿ�������';
		Column3.innerText	= '�ȿ�ɽ��';
		Column4.innerText	= '���̾��';
		Column5.innerText	= 'ɽ����ҵ���';
		Column6.innerText	= 'ɽ����ҥ�����';
		Column7.innerText	= 'ɽ�����̾��';
		Column8.innerText	= '͹���ֹ�';
		Column9.innerText	= '����1 / ��ƻ�ܸ�';
		Column10.innerText	= '����2 / �ԡ��衢��';
		Column11.innerText	= '����3 / Į������';
		Column12.innerText	= '����4 / �ӥ�������ʪ̾';
		Column13.innerText	= '�����ֹ�1';
		Column14.innerText	= '�����ֹ�2';
		Column15.innerText	= '�ե��å����ֹ�1';
		Column16.innerText	= '�ե��å����ֹ�2';
		Column17.innerText	= '���̥�����';
		Column18.innerText	= '������������';
		Column19.innerText	= '���°��';

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