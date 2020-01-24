//<!--


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
		SegA03.innerHTML = '<span class="CheckMark">◎</span>Supplier';
		// *v1* SegA04.innerHTML = '<span class="CheckMark">◎</span>Dept';
		// *v1* SegA05.innerHTML = '<span class="CheckMark">◎</span>In charge name';
		SegA06.innerHTML = 'Status';
		SegA07.innerHTML = 'Currency';
		SegA08.innerHTML = 'Rate type';
		SegA09.innerHTML = 'Rate';
		SegA10.innerHTML = 'Pay condition';
		SegA11.innerHTML = '<span class="CheckMark">◎</span>Location';
		SegA12.innerHTML = '<span class="CheckMark">◎</span>PO due date';
		SegA13.innerHTML = 'Remark';


		///// QUERY BUTTON /////
		SegAClearBt.innerHTML = clearbtAE1;


		///// INPUT B /////
		SegBHeader.innerHTML = headerBE;

		SegB02.innerHTML      = '<span class="CheckMark">◎</span>Products c/n';
		SegB03.innerHTML      = 'Goods code';
		SegB04_1.innerHTML    = '<span class="CheckMark">◎</span>Products Unit';
		SegB04_2.innerHTML    = 'Products Price';
		SegB04_3.innerHTML    = 'Products Unit';
		SegB04_4.innerHTML    = 'Products Qty';
		SegB04_5.innerHTML    = 'Amt Bfr tax';
		SegB05_1.innerHTML    = '<span class="CheckMark">◎</span>Packing unit';
		SegB05_2.innerHTML    = 'Packing price';
		SegB05_3.innerHTML    = 'Packing unit';
		SegB05_4.innerHTML    = 'Packing Qty';
		SegB05_5.innerHTML    = 'Carton Qty';
		SegB06.innerHTML      = 'Means of transport';
		SegB07.innerHTML      = 'Remark';
		SegB08.innerHTML      = 'Price list';
		SegB09.innerHTML      = '<span class="CheckMark">◎</span>Goods set';
		SegB10.innerHTML      = '<span class="CheckMark">◎</span>Goods parts';
		SegB11.innerHTML      = 'Work flow root'; /* Admission root */
		SegB12.innerHTML      = 'Std Amt Bfr tax';
		SegB16.innerHTML      = 'Total';
		SegB20.innerHTML      = '<span class="CheckMark">◎</span>Delivery date';
		SegB21.innerHTML      = 'No.';
		Tax.innerHTML         = 'Tax(SC)';
		TotalStdAmt.innerHTML = 'Total Std Amt';


		///// QUERY BUTTON /////
		AddRowBt.innerHTML    = addrowbtE1;
		DelRowBt.innerHTML    = delrowbtE1;
		CommitBt.innerHTML    = commitbtE1;
		SegBClearBt.innerHTML = clearbtBE1;



		//-------------------------------------------------------------------------
		// v2 tomita update
		//-------------------------------------------------------------------------
		SegDept.innerHTML     = 'Dept';
		SegIncharge.innerHTML = 'In charge name';

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

		window.SUPwin.Msw8ChgEtoJ( 0 );
		window.MDwin.Msw2ChgEtoJ( 0 );
		window.MGwin.Msw3ChgEtoJ( 0 );
		window.MLwin.Msw4ChgEtoJ( 0 );

		window.WFrootWin.RootWin.fncChgEtoJ( 0 );

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
		SegA02.innerHTML = '発注ＮＯ．';
		SegA03.innerHTML = '<span class="CheckMark">◎</span>仕入先';
		// *v1* SegA04.innerHTML = '<span class="CheckMark">◎</span>部門';
		// *v1* SegA05.innerHTML = '<span class="CheckMark">◎</span>担当者';
		SegA06.innerHTML = '状態';
		SegA07.innerHTML = '通貨';
		SegA08.innerHTML = 'レートタイプ';
		SegA09.innerHTML = '換算レート';
		SegA10.innerHTML = '支払条件';
		SegA11.innerHTML = '<span class="CheckMark">◎</span>納品場所';
		SegA12.innerHTML = '<span class="CheckMark">◎</span>発注有効期限日';
		SegA13.innerHTML = '備考';


		///// QUERY BUTTON /////
		SegAClearBt.innerHTML = clearbtAJ1;


		///// INPUT B /////
		SegBHeader.innerHTML = headerBJ;

		SegB02.innerHTML      = '<span class="CheckMark">◎</span>製品コード名称';
		SegB03.innerHTML      = '顧客品番';
		SegB04_1.innerHTML    = '<span class="CheckMark">◎</span>製品単位計上';
		SegB04_2.innerHTML    = '製品単価';
		SegB04_3.innerHTML    = '製品単位';
		SegB04_4.innerHTML    = '製品数量';
		SegB04_5.innerHTML    = '税抜金額';
		SegB05_1.innerHTML    = '<span class="CheckMark">◎</span>荷姿単位計上';
		SegB05_2.innerHTML    = '荷姿単価';
		SegB05_3.innerHTML    = '荷姿単位';
		SegB05_4.innerHTML    = '荷姿数量';
		SegB05_5.innerHTML    = 'カートン入数';
		SegB06.innerHTML      = '運搬方法';
		SegB07.innerHTML      = '備考';
		SegB08.innerHTML      = '単価リスト';
		SegB09.innerHTML      = '<span class="CheckMark">◎</span>仕入科目';
		SegB10.innerHTML      = '<span class="CheckMark">◎</span>仕入部品';
		SegB11.innerHTML      = '承認ルート';
		SegB12.innerHTML      = '基準通貨';
		SegB16.innerHTML      = '総合計金額';
		SegB20.innerHTML      = '<span class="CheckMark">◎</span>納期';
		SegB21.innerHTML      = 'No.';
		Tax.innerHTML         = '税額';
		TotalStdAmt.innerHTML = '合計金額';


		///// QUERY BUTTON /////
		AddRowBt.innerHTML    = addrowbtJ1;
		DelRowBt.innerHTML    = delrowbtJ1;
		CommitBt.innerHTML    = commitbtJ1;
		SegBClearBt.innerHTML = clearbtBJ1;



		//-------------------------------------------------------------------------
		// v2 tomita update
		//-------------------------------------------------------------------------
		SegDept.innerHTML     = '部門';
		SegIncharge.innerHTML = '担当者';

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

		window.SUPwin.Msw8ChgEtoJ( 1 );
		window.MDwin.Msw2ChgEtoJ( 1 );
		window.MGwin.Msw3ChgEtoJ( 1 );
		window.MLwin.Msw4ChgEtoJ( 1 );

		window.WFrootWin.RootWin.fncChgEtoJ( 1 );

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