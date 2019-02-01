<!--


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
		// *v1* SegA02.innerHTML = '<span class="CheckMark">◎</span>S order No.';
		SegA03.innerHTML = '<span class="CheckMark">◎</span>Vendor';
		// *v1* SegA04.innerHTML = '<span class="CheckMark">◎</span>Dept';
		// *v1* SegA05.innerHTML = '<span class="CheckMark">◎</span>In charge name';
		SegA06.innerHTML = 'Status';
		SegA07.innerHTML = 'Currency';
		SegA08.innerHTML = 'Rate type';
		SegA09.innerHTML = 'Rate';
		SegA10.innerHTML = 'Remark';


		///// QUERY BUTTON /////
		SegAClearBt.innerHTML = clearbtAE1;


		///// INPUT B /////
		SegBHeader.innerHTML = headerBE;

		SegB01.innerHTML   = 'Sales class';
		SegB02.innerHTML   = '<span class="CheckMark">◎</span>Products c/n';
		SegB03.innerHTML   = 'Goods code(Corresp)';
		SegB04_1.innerHTML = '<span class="CheckMark">◎</span>Products Unit';
		SegB04_2.innerHTML = 'Products Price';
		SegB04_3.innerHTML = 'Products Unit';
		SegB04_4.innerHTML = 'Products Qty';
		SegB04_5.innerHTML = 'Amt Bfr tax';
		SegB05_1.innerHTML = '<span class="CheckMark">◎</span>Packing unit';
		SegB05_2.innerHTML = 'Packing price';
		SegB05_3.innerHTML = 'Packing unit';
		SegB05_4.innerHTML = 'Packing Qty';
		SegB05_5.innerHTML = 'Carton Qty';
		SegB06.innerHTML   = '<span class="CheckMark">◎</span>Delivery date';
		SegB07.innerHTML   = 'Remark';
		SegB08.innerHTML   = 'Price list';
		SegB16.innerHTML   = 'Total';


		///// QUERY BUTTON /////
		AddRowBt.innerHTML = addrowbtE1;
		DelRowBt.innerHTML = delrowbtE1;
		CommitBt.innerHTML = commitbtE1;
		RegistBt.innerHTML = registbtE1;
		SegBClearBt.innerHTML = clearbtBE1;





		//-------------------------------------------------------------------------
		// v2 tomita update
		//-------------------------------------------------------------------------
		SegCRC.innerHTML      = 'C order No.';
		SegRC.innerHTML       = 'S order No.';
		SegDept.innerHTML     = 'Dept';
		SegIncharge.innerHTML = 'In charge name';
		SegBWF.innerHTML      = 'Work flow root'; /* Admission root */
		window.WFrootWin.RootWin.fncChgEtoJ( 0 );

		RegistBt.innerHTML = registbtE1;
/*
		var obj = document.DSO.lngWorkflowOrderCode;

		// 「承認なし」の場合の判定 -> 「承認なし」の場合はボタン押下禁止
		for( i=0; i < obj.options.length; i++ )
		{
			if( obj.options[i].text == '承認なし' )
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

		window.MVwin.Msw1ChgEtoJ( 0 );
		window.MDwin.Msw2ChgEtoJ( 0 );
		window.MGwin.Msw3ChgEtoJ( 0 );


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

		SegA01.innerHTML = '計上日';
		// *v1* SegA02.innerHTML = '<span class="CheckMark">◎</span>受注ＮＯ．';
		SegA03.innerHTML = '<span class="CheckMark">◎</span>顧客';
		// *v1* SegA04.innerHTML = '<span class="CheckMark">◎</span>部門';
		// *v1* SegA05.innerHTML = '<span class="CheckMark">◎</span>担当者';
		SegA06.innerHTML = '状態';
		SegA07.innerHTML = '通貨';
		SegA08.innerHTML = 'レートタイプ';
		SegA09.innerHTML = '換算レート';
		SegA10.innerHTML = '備考';


		///// QUERY BUTTON /////
		SegAClearBt.innerHTML = clearbtAJ1;


		///// INPUT B /////
		SegBHeader.innerHTML = headerBJ;

		SegB01.innerHTML   = '売上区分';
		SegB02.innerHTML   = '<span class="CheckMark">◎</span>製品コード名称';
		SegB03.innerHTML   = '顧客品番';
		SegB04_1.innerHTML = '<span class="CheckMark">◎</span>製品単位計上';
		SegB04_2.innerHTML = '製品単価';
		SegB04_3.innerHTML = '製品単位';
		SegB04_4.innerHTML = '製品数量';
		SegB04_5.innerHTML = '税抜金額';
		SegB05_1.innerHTML = '<span class="CheckMark">◎</span>荷姿単位計上';
		SegB05_2.innerHTML = '荷姿単価';
		SegB05_3.innerHTML = '荷姿単位';
		SegB05_4.innerHTML = '荷姿数量';
		SegB05_5.innerHTML = 'カートン入数';
		SegB06.innerHTML   = '<span class="CheckMark">◎</span>納期';
		SegB07.innerHTML   = '備考';
		SegB08.innerHTML   = '単価リスト';
		SegB16.innerHTML   = '総合計金額';


		///// QUERY BUTTON /////
		AddRowBt.innerHTML = addrowbtJ1;
		DelRowBt.innerHTML = delrowbtJ1;
		CommitBt.innerHTML = commitbtJ1;
		RegistBt.innerHTML = registbtJ1;
		SegBClearBt.innerHTML = clearbtBJ1;





		//-------------------------------------------------------------------------
		// v2 tomita update
		//-------------------------------------------------------------------------
		SegCRC.innerHTML      = '顧客受注番号';
		SegRC.innerHTML       = '受注ＮＯ．';
		SegDept.innerHTML     = '部門';
		SegIncharge.innerHTML = '担当者';
		SegBWF.innerHTML      = '承認ルート';
		window.WFrootWin.RootWin.fncChgEtoJ( 1 );

		RegistBt.innerHTML = registbtJ1;
/*
		var obj = document.DSO.lngWorkflowOrderCode;

		// 「承認なし」の場合の判定 -> 「承認なし」の場合はボタン押下禁止
		for( i=0; i < obj.options.length; i++ )
		{
			if( obj.options[i].text == '承認なし' )
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

		window.MVwin.Msw1ChgEtoJ( 1 );
		window.MDwin.Msw2ChgEtoJ( 1 );
		window.MGwin.Msw3ChgEtoJ( 1 );



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