


function ChgEtoJ()
{


////////////////////////////////// ENGLISH /////////////////////////////////////
	if ( lngClickCode == 0 )
	{

		///// SET COOKIE /////
		SetlngLanguageCode();


		///// E TO J /////
		EtoJ.innerHTML = etojJbt;


		///// MAIN TITLE /////
		MainTitle.innerHTML = maintitleE;


		///// INPUT A /////
		SegAHeader.innerHTML = headerAE;

		SegA01.innerHTML = 'Date';
		SegA02.innerHTML = 'P order No.';
		SegA03.innerHTML = '<span class="CheckMark">��</span>Supplier';
		// *v1* SegA04.innerHTML = '<span class="CheckMark">��</span>Dept';
		// *v1* SegA05.innerHTML = '<span class="CheckMark">��</span>In charge name';
		SegA06.innerHTML = 'Status';
		SegA07.innerHTML = 'Currency';
		SegA08.innerHTML = 'Rate type';
		SegA09.innerHTML = 'Rate';
		SegA10.innerHTML = 'Pay condition';
		SegA11.innerHTML = '<span class="CheckMark">��</span>Location';
		SegA12.innerHTML = 'Arrival date';
		SegA13.innerHTML = 'Remark';
		SegA14.innerHTML = 'P control No.';
		SegA15.innerHTML = '<span class="CheckMark">��</span>Shipping No.';


		///// QUERY BUTTON /////
		SegAClearBt.innerHTML = clearbtAE1;


		///// INPUT B /////
		SegBHeader.innerHTML = headerBE;

		SegB02.innerHTML      = '<span class="CheckMark">��</span>Products c/n';
		SegB03.innerHTML      = 'Goods code';
		SegB04_1.innerHTML    = '<span class="CheckMark">��</span>Products Unit';
		SegB04_2.innerHTML    = 'Products Price';
		SegB04_3.innerHTML    = 'Products Unit';
		SegB04_4.innerHTML    = 'Products Qty';
		SegB04_5.innerHTML    = 'Amt Bfr tax';
		SegB05_1.innerHTML    = '<span class="CheckMark">��</span>Packing unit';
		SegB05_2.innerHTML    = 'Packing price';
		SegB05_3.innerHTML    = 'Packing unit';
		SegB05_4.innerHTML    = 'Packing Qty';
		SegB05_5.innerHTML    = 'Carton Qty';
		SegB06.innerHTML      = 'Means of transport';
		SegB07.innerHTML      = 'Remark';
		SegB08.innerHTML      = 'Price list';
		SegB09.innerHTML      = '<span class="CheckMark">��</span>Goods set';
		SegB10.innerHTML      = '<span class="CheckMark">��</span>Goods parts';
		SegB12.innerHTML      = 'Std Amt Bfr tax';
		SegB13.innerHTML      = 'Tax type';
		SegB14.innerHTML      = 'Tax';
		SegB15.innerHTML      = 'Total Amt';
		SegB16.innerHTML      = 'Total';
		SegB20.innerHTML      = 'Delivery date';
		SegB21.innerHTML      = 'No.';
		Tax.innerHTML         = 'Tax(SC)';
		TotalStdAmt.innerHTML = 'Total Std Amt';


		///// QUERY BUTTON /////
		AddRowBt.innerHTML    = addrowbtE1;
		DelRowBt.innerHTML    = delrowbtE1;
		CommitBt.innerHTML    = commitbtE1;
		RegistBt.innerHTML    = registbtE1;
		SegBClearBt.innerHTML = clearbtBE1;



		//-------------------------------------------------------------------------
		// v2 tomita update
		//-------------------------------------------------------------------------
		SegDept.innerHTML     = 'Dept';
		SegIncharge.innerHTML = 'In charge name';
		SegBWF.innerHTML      = 'Work flow root'; /* Admission root */
		window.WFrootWin.RootWin.fncChgEtoJ( 0 );

		RegistBt.innerHTML = registbtE1;
/*
		var obj = document.DSO.lngWorkflowOrderCode;

		// �־�ǧ�ʤ��פξ���Ƚ�� -> �־�ǧ�ʤ��פξ��ϥܥ��󲡲��ػ�
		for( i=0; i < obj.options.length; i++ )
		{
			if( obj.options[i].text == '��ǧ�ʤ�' )
			{
				if( i == 0 )
				{
					RegistBt.innerHTML = registbtENotActive;
				}
			}
			else
			{
				RegistBt.innerHTML = registbtE1;
			}
		}
*/
		//-------------------------------------------------------------------------



		///// INPUT C /////
		SegCHeader.innerHTML = headerCE;


		window.DLwin.ChgEtoJ( 0 );
		window.NAVIwin.ChgEtoJ( 0 );

		window.SUPwin.Msw8ChgEtoJ( 0 );
		window.MDwin.Msw2ChgEtoJ( 0 );
		window.MGwin.Msw3ChgEtoJ( 0 );
		window.MLwin.Msw4ChgEtoJ( 0 );


		if( typeof(window.Mdata10) != 'undefined' )
		{
			window.Mdata10.fncChgEtoJ( 0 );
		}

		if( typeof(window.Mdata10_2) != 'undefined' )
		{
			window.Mdata10_2.fncChgEtoJ( 0 );
		}

		if( typeof(window.Mdata10_3) != 'undefined' )
		{
			window.Mdata10_3.fncChgEtoJ( 0 );
		}


		lngLanguageCode = 0;
		lngClickCode    = 1;

	}


////////////////////////////////// JAPANESE /////////////////////////////////////
	else if ( lngClickCode == 1 )
	{

		///// SET COOKIE /////
		SetlngLanguageCode();


		///// E TO J /////
		EtoJ.innerHTML = etojEbt;


		///// MAIN TITLE /////
		MainTitle.innerHTML = maintitleJ;


		///// INPUT A /////
		SegAHeader.innerHTML = headerAJ;

		SegA01.innerHTML = '������';
		SegA02.innerHTML = 'ȯ���Σϡ�';
		SegA03.innerHTML = '<span class="CheckMark">��</span>������';
		// *v1* SegA04.innerHTML = '<span class="CheckMark">��</span>����';
		// *v1* SegA05.innerHTML = '<span class="CheckMark">��</span>ô����';
		SegA06.innerHTML = '����';
		SegA07.innerHTML = '�̲�';
		SegA08.innerHTML = '�졼�ȥ�����';
		SegA09.innerHTML = 'Ŭ�ѥ졼��';
		SegA10.innerHTML = '��ʧ���';
		SegA11.innerHTML = '<span class="CheckMark">��</span>Ǽ�ʾ��';
		SegA12.innerHTML = '����������';
		SegA13.innerHTML = '����';
		SegA14.innerHTML = '�����Σϡ�';
		SegA15.innerHTML = '<span class="CheckMark">��</span>Ǽ�ʽ�Σ�.';


		///// QUERY BUTTON /////
		SegAClearBt.innerHTML = clearbtAJ1;


		///// INPUT B /////
		SegBHeader.innerHTML = headerBJ;

		SegB02.innerHTML      = '<span class="CheckMark">��</span>���ʥ�����̾��';
		SegB03.innerHTML      = '�ܵ�����';
		SegB04_1.innerHTML    = '<span class="CheckMark">��</span>����ñ�̷׾�';
		SegB04_2.innerHTML    = '����ñ��';
		SegB04_3.innerHTML    = '����ñ��';
		SegB04_4.innerHTML    = '���ʿ���';
		SegB04_5.innerHTML    = '��ȴ���';
		SegB05_1.innerHTML    = '<span class="CheckMark">��</span>�ٻ�ñ�̷׾�';
		SegB05_2.innerHTML    = '�ٻ�ñ��';
		SegB05_3.innerHTML    = '�ٻ�ñ��';
		SegB05_4.innerHTML    = '�ٻѿ���';
		SegB05_5.innerHTML    = '�����ȥ�����';
		SegB06.innerHTML      = '������ˡ';
		SegB07.innerHTML      = '����';
		SegB08.innerHTML      = 'ñ���ꥹ��';
		SegB09.innerHTML      = '<span class="CheckMark">��</span>��������';
		SegB10.innerHTML      = '<span class="CheckMark">��</span>��������';
		SegB12.innerHTML      = '����̲�';
		SegB13.innerHTML      = '�Ƕ�ʬ';
		SegB14.innerHTML      = '��Ψ';
		SegB15.innerHTML      = '�ǳ�';
		SegB16.innerHTML      = '���׶��';
		SegB20.innerHTML      = 'Ǽ��';
		SegB21.innerHTML      = 'No.';
		Tax.innerHTML         = '�ǳ�';
		TotalStdAmt.innerHTML = '��׶��';


		///// QUERY BUTTON /////
		AddRowBt.innerHTML    = addrowbtJ1;
		DelRowBt.innerHTML    = delrowbtJ1;
		CommitBt.innerHTML    = commitbtJ1;
		RegistBt.innerHTML    = registbtJ1;
		SegBClearBt.innerHTML = clearbtBJ1;



		//-------------------------------------------------------------------------
		// v2 tomita update
		//-------------------------------------------------------------------------
		SegDept.innerHTML     = '����';
		SegIncharge.innerHTML = 'ô����';
		SegBWF.innerHTML      = '��ǧ�롼��';
		window.WFrootWin.RootWin.fncChgEtoJ( 1 );

		RegistBt.innerHTML = registbtJ1;
/*
		var obj = document.DSO.lngWorkflowOrderCode;

		// �־�ǧ�ʤ��פξ���Ƚ�� -> �־�ǧ�ʤ��פξ��ϥܥ��󲡲��ػ�
		for( i=0; i < obj.options.length; i++ )
		{
			if( obj.options[i].text == '��ǧ�ʤ�' )
			{
				if( i == 0 )
				{
					RegistBt.innerHTML = registbtJNotActive;
				}
			}
			else
			{
				RegistBt.innerHTML = registbtJ1;
			}
		}
*/
		//-------------------------------------------------------------------------



		///// INPUT C /////
		SegCHeader.innerHTML = headerCJ;


		window.DLwin.ChgEtoJ( 1 );
		window.NAVIwin.ChgEtoJ( 1 );

		window.SUPwin.Msw8ChgEtoJ( 1 );
		window.MDwin.Msw2ChgEtoJ( 1 );
		window.MGwin.Msw3ChgEtoJ( 1 );
		window.MLwin.Msw4ChgEtoJ( 1 );



		if( typeof(window.Mdata10) != 'undefined' )
		{
			window.Mdata10.fncChgEtoJ( 1 );
		}

		if( typeof(window.Mdata10_2) != 'undefined' )
		{
			window.Mdata10_2.fncChgEtoJ( 1 );
		}

		if( typeof(window.Mdata10_3) != 'undefined' )
		{
			window.Mdata10_3.fncChgEtoJ( 1 );
		}


		lngLanguageCode = 1;
		lngClickCode    = 0;

	}

	return false;

}


//-->