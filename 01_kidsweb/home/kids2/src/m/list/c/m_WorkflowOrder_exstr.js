<!--
/************************* [ ����ե�����ޥ��� ] *************************/



/*
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
		fncChangeObjectIdModule( Array( 'Input0' , 'Input1' , 'Input2' , 'Input3' , 'Input4'  ) ,

								 Array( 'Txt40L' , 'Txt40L' , 'Txt40L' , 'Txt40L' , 'Txt40L' ) );
	}


	// [lngLanguageCode]�ͤν����
	var g_lngCode = '';

	// [lngLanguageCode]�ͤμ���
	g_lngCode = lngLangCode;


	// ���֥������Ȥ����ܸ졦�Ѹ�����
	ChgEtoJ( g_lngCode );


	return true;
}
*/






//////////////////////////////////////////////////////////////////////
////////// [�ɲ�]���������꡼�ơ��֥�⥸�塼�� //////////
function fncQueryTable( lngCode )
{
	if( lngCode == 0 )
	{
		objQuery.innerHTML = '<table width="90%" cellpadding="5" cellspacing="1" border="0" bgcolor="#6f8180" align="center"><tr><td bgcolor="#f1f1f1">	<a href="#" onclick="window.close();"><img onmouseover="CloseEOn( this );" onmouseout="CloseEOff( this );" src="/img/type01/cmn/seg/close_off_en_bt.gif" width="72" height="20" border="0" alt="CLOSE"></a>&nbsp;&nbsp;<a href="javascript:window.form1.reset();"><img  onmouseover="fncGrayClearButton( \'onE\' , this );" onmouseout="fncGrayClearButton( \'offE\' , this );" src="/img/type01/cmn/querybt/grayclear_off_en_bt.gif" width="72" height="20" border="0" alt="CLEAR"><a>&nbsp;&nbsp;<a href="javascript:window.form1.submit();"><img onmouseover="MasterRegEOn(this);" onmouseout="MasterRegEOff(this);" src="/img/type01/cmn/seg/regist_off_en_bt.gif" width="72" height="20" border="0" alt="REGIST"><a></td></tr></table>';
	}
	else if( lngCode == 1 )
	{
		objQuery.innerHTML = '<table width="90%" cellpadding="5" cellspacing="1" border="0" bgcolor="#6f8180" align="center"><tr><td bgcolor="#f1f1f1"><a href="#" onclick="window.close();"><img onmouseover="CloseJOn( this );" onmouseout="CloseJOff( this );" src="/img/type01/cmn/seg/close_off_ja_bt.gif" width="72" height="20" border="0" alt="CLOSE"></a>&nbsp;&nbsp;<a href="javascript:window.form1.reset();"><img  onmouseover="fncGrayClearButton( \'onJ\' , this );" onmouseout="fncGrayClearButton( \'offJ\' , this );" src="/img/type01/cmn/querybt/grayclear_off_ja_bt.gif" width="72" height="20" border="0" alt="CLEAR"><a>&nbsp;&nbsp;<a href="javascript:window.form1.submit();"><img onmouseover="MasterRegJOn(this);" onmouseout="MasterRegJOff(this);" src="/img/type01/cmn/seg/regist_off_ja_bt.gif" width="72" height="20" border="0" alt="REGIST"><a></td></tr></table>';
	}
}





//////////////////////////////////////////////////////////////////////
////////// [�ɲ�]�������֥������ȤΥ�����ɽ����ؿ� //////////
function fncEditObjectOnload( lngCode )
{

	// �����꡼�ơ��֥�ԣϣк�ɸ
	if( typeof(QueryTable) != 'undefined' )
	{
		QueryTable.style.top = 400;
	}


	// [�ɲ�]
	if( lngCode == 0 )
	{
		ControlTitle.innerText	= 'ADDITION';

		// �����̾
		Column0.innerText	= 'Order name';
		Column1.innerText	= 'Select order group';
		Column2.innerText	= 'Select in charge';
		Column3.innerText	= 'Limit Date';
		Column4.innerText	= 'Order';
		Column5.innerText	= 'Add';

		fncQueryTable( lngCode );
	}

	else if( lngCode == 1 )
	{
		ControlTitle.innerText	= '�ɲ�';

		// �����̾
		Column0.innerText	= '����ե����̾��';
		Column1.innerText	= '���롼������';
		Column2.innerText	= 'ô��������';
		Column3.innerText	= '������';
		Column4.innerText	= '���';
		Column5.innerText	= '�ɲ�';

		fncQueryTable( lngCode );
	}


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
var headerAJ = '<img src="' + h_wfJ + '" width="949" height="30" border="0" alt="����ե�����ޥ���">';
var headerAE = '<img src="' + h_wfE + '" width="949" height="30" border="0" alt="WORK FLOW ORDER MASTER">';











//////////////////////////////////////////////////////////////////
////////// ���ܸ졦�Ѹ����إ⥸�塼�� //////////
function ChgEtoJ( g_lngCode )
{


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
		Column0.innerText	= 'Order name';
		Column1.innerText	= 'Order group name';

		if( typeof(Column2) != 'undefined' )
		{
			Column2.innerText	= 'Order';
		}

		if( typeof(Column3) != 'undefined' )
		{
			Column3.innerText	= 'In charge name';
		}

		if( typeof(Column4) != 'undefined' )
		{
			Column4.innerText	= 'Limit date';
		}


		// ����̾
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
		Column0.innerText	= '���̾��';
		Column1.innerText	= '������롼��̾��';

		if( typeof(Column2) != 'undefined' )
		{
			Column2.innerText	= '���';
		}

		if( typeof(Column3) != 'undefined' )
		{
			Column3.innerText	= 'ô����̾';
		}

		if( typeof(Column4) != 'undefined' )
		{
			Column4.innerText	= '������';
		}


		// ����̾
		if( typeof(DeleteColumn) != 'undefined' )
		{
			DeleteColumn.innerText	= '���';
		}

	}

	return false;

}


//-->
