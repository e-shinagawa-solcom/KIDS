<!--


function ChgEtoJ()
{


////////////////////////////////// ENGLISH /////////////////////////////////////
	if ( lngClickCode == 0 )
	{

		///// SET COOKIE /////
		SetlngLanguageCode();


		///// E TO J /////
		if( typeof(EtoJ) != 'undefined' )
		{
			EtoJ.innerHTML = etojJbt;
		}


		///// MAIN TITLE /////
		if( typeof(MainTitle) != 'undefined' )
		{
			MainTitle.innerHTML = maintitleE;
		}


		///// INPUT A /////
		if( typeof(SegAHeader) != 'undefined' )
		{
			SegAHeader.innerHTML = headerAE;
		}

		SegA01.innerHTML         = 'Products code';
		SegA02.innerHTML         = 'Goods code(Corresp)';
//		SegA18.innerHTML         = '<span class="CheckMark">��</span>Category';
		SegA18.innerHTML         = '<span class="CheckMark">��</span>Category';
		SegA03.innerHTML         = '<span class="CheckMark">��</span>Dept';
		SegA04.innerHTML         = '<span class="CheckMark">��</span>In charge name';
		SegA05.innerHTML         = '<span class="CheckMark">��</span>Products name(ja)';
		SegA06.innerHTML         = '<span class="CheckMark">��</span>Products name(en)';
		SegA07.innerHTML         = 'Goods name';
		SegA08.innerHTML         = 'Products Unit';
		SegA09.innerHTML         = 'Packing unit';
		SegA11.innerHTML         = 'Target age';
		SegA12.innerHTML         = 'Box Qty';
		SegA13.innerHTML         = '<span class="CheckMark">��</span>Carton Qty';
		SegA14.innerHTML         = '<span class="CheckMark">��</span>Products Info';
		SegA15.innerHTML         = 'Assembly Info';
		SegA17.innerHTML         = 'Assembly factory';
		CreateDate.innerHTML     = 'Creation date';
		ProgressStatus.innerHTML = 'Plan status';
		ReviseNumber.innerHTML   = 'Revise No.';
		ReviseDate.innerHTML     = 'Revise date';


		///// QUERY BUTTON /////
		if( typeof(SegAClearBt) != 'undefined' )
		{
			SegAClearBt.innerHTML = clearbtAE1;
		}


		///// INPUT B /////
		if( typeof(SegBHeader) != 'undefined' )
		{
			SegBHeader.innerHTML = headerBE;
		}

		SegB01.innerHTML = '<span class="CheckMark">��</span>Selling price(pcs)';
		SegB02.innerHTML = '<span class="CheckMark">��</span>Wholesale price(pcs)';
		SegB03.innerHTML = 'Creation factory';
		SegB04.innerHTML = 'Location';
		SegB05.innerHTML = '<span class="CheckMark">��</span>Delivery date';
		SegB06.innerHTML = '<span class="CheckMark">��</span>Refound Qty';
		SegB07.innerHTML = '<span class="CheckMark">��</span>Delivery Qty';
		SegB08.innerHTML = 'Vendor';
		SegB09.innerHTML = 'In charge name';
//		SegB10.innerHTML = '<span class="CheckMark">��</span>Copyright';
		SegB10.innerHTML = 'Copyright';
//		SegB11.innerHTML = '<span class="CheckMark">��</span>Loyalty(%)';
		SegB11.innerHTML = 'Loyalty(%)';
		SegB12.innerHTML = 'Copyright(Stamp)';
		SegB13.innerHTML = 'Copyright(Print)';
		SegB14.innerHTML = 'Details';
		SegB16.innerHTML = 'Inspection';
		SegB17.innerHTML = '<span class="CheckMark">��</span>Goods form';
		SegB20.innerHTML = 'Copyright remark';





		//-------------------------------------------------------------------------
		// v2 tomita update
		//-------------------------------------------------------------------------
/*
		SegBWF.innerHTML = 'Work flow root';
		window.WFrootWin.RootWin.fncChgEtoJ( 0 );

		RegistBt.innerHTML = registbtE1;


		var obj = document.PPP2.lngWorkflowOrderCode;

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





		///// QUERY BUTTON /////
		if( typeof(RegistBt) != 'undefined' )
		{
			RegistBt.innerHTML = registbtE1;
		}

		if( typeof(SegBClearBt) != 'undefined' )
		{
			SegBClearBt.innerHTML = clearbtBE1;
		}


		if( typeof(window.NAVIwin) != 'undefined' )
		{
			window.NAVIwin.ChgEtoJ( 0 );
		}

		if( typeof(window.MVwin_2) != 'undefined' )
		{
			window.MVwin_2.Msw1_2ChgEtoJ( 0 );
		}

		if( typeof(window.MVwin_3) != 'undefined' )
		{
			window.MVwin_3.Msw1_3ChgEtoJ( 0 );
		}

		if( typeof(window.MDwin) != 'undefined' )
		{
			window.MDwin.Msw2ChgEtoJ( 0 );
		}

		if( typeof(window.VIwin) != 'undefined' )
		{
			window.VIwin.Msw7ChgEtoJ( 0 );
		}

		if( typeof(window.MLwin) != 'undefined' )
		{
			window.MLwin.Msw4ChgEtoJ( 0 );
		}

		if( typeof(window.editWin) != 'undefined' )
		{
			window.editWin.fncEditDataSet( 0 );
		}



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
		if( typeof(EtoJ) != 'undefined' )
		{
			EtoJ.innerHTML = etojEbt;
		}


		///// MAIN TITLE /////
		if( typeof(MainTitle) != 'undefined' )
		{
			MainTitle.innerHTML = maintitleJ;
		}


		///// INPUT A /////
		if( typeof(SegAHeader) != 'undefined' )
		{
			SegAHeader.innerHTML = headerAJ;
		}

		SegA01.innerHTML         = '���ʥ�����';
		SegA02.innerHTML         = '�ܵ�����';
//		SegA18.innerHTML         = '<span class="CheckMark">��</span>���ƥ���';
		SegA18.innerHTML         = '���ƥ���';
		SegA03.innerHTML         = '<span class="CheckMark">��</span>�Ķ�����';
		SegA04.innerHTML         = '<span class="CheckMark">��</span>��ȯô����';
		SegA05.innerHTML         = '<span class="CheckMark">��</span>����̾��(���ܸ�)';
		SegA06.innerHTML         = '<span class="CheckMark">��</span>����̾��(�Ѹ�)';
		SegA07.innerHTML         = '����̾��';
		SegA08.innerHTML         = '����ñ��';
		SegA09.innerHTML         = '�ٻ�ñ��';
		SegA11.innerHTML         = '�о�ǯ��';
		SegA12.innerHTML         = '��Ȣ(��)����';
		SegA13.innerHTML         = '<span class="CheckMark">��</span>�����ȥ�����';
		SegA14.innerHTML         = '<span class="CheckMark">��</span>���ʹ���';
		SegA15.innerHTML         = '���å���֥�����';
		SegA17.innerHTML         = '���å���֥깩��';
		CreateDate.innerHTML     = '��������';
		ProgressStatus.innerHTML = '���ʹԾ���';
		ReviseNumber.innerHTML   = '�����ֹ�';
		ReviseDate.innerHTML     = '��������';

		///// QUERY BUTTON /////
		if( typeof(SegAClearBt) != 'undefined' )
		{
			SegAClearBt.innerHTML = clearbtAJ1;
		}


		///// INPUT B /////
		if( typeof(SegBHeader) != 'undefined' )
		{
			SegBHeader.innerHTML = headerBJ;
		}

		SegB01.innerHTML = '<span class="CheckMark">��</span>����(pcsñ��)';
		SegB02.innerHTML = '<span class="CheckMark">��</span>Ǽ��(pcsñ��)';
		SegB03.innerHTML = '��������';
		SegB04.innerHTML = 'Ǽ�ʾ��';
		SegB05.innerHTML = '<span class="CheckMark">��</span>Ǽ��';
		SegB06.innerHTML = '<span class="CheckMark">��</span>����ͽ���';
		SegB07.innerHTML = '<span class="CheckMark">��</span>���Ǽ�ʿ�';
		SegB08.innerHTML = '�ܵ�';
		SegB09.innerHTML = '�ܵ�ô����';
//		SegB10.innerHTML = '<span class="CheckMark">��</span>�Ǹ���';
		SegB10.innerHTML = '�Ǹ���';
//		SegB11.innerHTML = '<span class="CheckMark">��</span>�����ƥ�(%)';
		SegB11.innerHTML = '�����ƥ�(%)';
		SegB12.innerHTML = '�Ǹ�ɽ��(���)';
		SegB13.innerHTML = '�Ǹ�ɽ��(����ʪ)';
		SegB14.innerHTML = '���;ܺ�';
		SegB16.innerHTML = '�ڻ�';
		SegB17.innerHTML = '<span class="CheckMark">��</span>���ʷ���';
		SegB20.innerHTML = '�Ǹ�������';





		//-------------------------------------------------------------------------
		// v2 tomita update
		//-------------------------------------------------------------------------
/*
		SegBWF.innerHTML = '��ǧ�롼��';
		window.WFrootWin.RootWin.fncChgEtoJ( 1 );

		RegistBt.innerHTML = registbtJ1;

		var obj = document.PPP2.lngWorkflowOrderCode;

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





		///// QUERY BUTTON /////
		if( typeof(RegistBt) != 'undefined' )
		{
			RegistBt.innerHTML = registbtJ1;
		}

		if( typeof(SegBClearBt) != 'undefined' )
		{
			SegBClearBt.innerHTML = clearbtBJ1;
		}


		if( typeof(window.NAVIwin) != 'undefined' )
		{
			window.NAVIwin.ChgEtoJ( 1 );
		}

		if( typeof(window.MVwin_2) != 'undefined' )
		{
			window.MVwin_2.Msw1_2ChgEtoJ( 1 );
		}

		if( typeof(window.MVwin_3) != 'undefined' )
		{
			window.MVwin_3.Msw1_3ChgEtoJ( 1 );
		}

		if( typeof(window.MDwin) != 'undefined' )
		{
			window.MDwin.Msw2ChgEtoJ( 1 );
		}

		if( typeof(window.VIwin) != 'undefined' )
		{
			window.VIwin.Msw7ChgEtoJ( 1 );
		}

		if( typeof(window.MLwin) != 'undefined' )
		{
			window.MLwin.Msw4ChgEtoJ( 1 );
		}

		if( typeof(window.editWin) != 'undefined' )
		{
			window.editWin.fncEditDataSet( 1 );
		}



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