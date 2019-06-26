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
//		SegA18.innerHTML         = '<span class="CheckMark">◎</span>Category';
		SegA18.innerHTML         = '<span class="CheckMark">◎</span>Category';
		SegA03.innerHTML         = '<span class="CheckMark">◎</span>Dept';
		SegA04.innerHTML         = '<span class="CheckMark">◎</span>In charge name';
		SegA05.innerHTML         = '<span class="CheckMark">◎</span>Products name(ja)';
		SegA06.innerHTML         = '<span class="CheckMark">◎</span>Products name(en)';
		SegA07.innerHTML         = 'Goods name';
		SegA08.innerHTML         = 'Products Unit';
		SegA09.innerHTML         = 'Packing unit';
		SegA11.innerHTML         = 'Target age';
		SegA12.innerHTML         = 'Box Qty';
		SegA13.innerHTML         = '<span class="CheckMark">◎</span>Carton Qty';
		SegA14.innerHTML         = '<span class="CheckMark">◎</span>Products Info';
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

		SegB01.innerHTML = '<span class="CheckMark">◎</span>Selling price(pcs)';
		SegB02.innerHTML = '<span class="CheckMark">◎</span>Wholesale price(pcs)';
		SegB03.innerHTML = 'Creation factory';
		SegB04.innerHTML = 'Location';
		SegB05.innerHTML = '<span class="CheckMark">◎</span>Delivery date';
		SegB06.innerHTML = '<span class="CheckMark">◎</span>Refound Qty';
		SegB07.innerHTML = '<span class="CheckMark">◎</span>Delivery Qty';
		SegB08.innerHTML = 'Vendor';
		SegB09.innerHTML = 'In charge name';
//		SegB10.innerHTML = '<span class="CheckMark">◎</span>Copyright';
		SegB10.innerHTML = 'Copyright';
//		SegB11.innerHTML = '<span class="CheckMark">◎</span>Loyalty(%)';
		SegB11.innerHTML = 'Loyalty(%)';
		SegB12.innerHTML = 'Copyright(Stamp)';
		SegB13.innerHTML = 'Copyright(Print)';
		SegB14.innerHTML = 'Details';
		SegB16.innerHTML = 'Inspection';
		SegB17.innerHTML = '<span class="CheckMark">◎</span>Goods form';
		SegB20.innerHTML = 'Copyright remark';





		//-------------------------------------------------------------------------
		// v2 tomita update
		//-------------------------------------------------------------------------
/*
		SegBWF.innerHTML = 'Work flow root';
		window.WFrootWin.RootWin.fncChgEtoJ( 0 );

		RegistBt.innerHTML = registbtE1;


		var obj = document.PPP2.lngWorkflowOrderCode;

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

		SegA01.innerHTML         = '製品コード';
		SegA02.innerHTML         = '顧客品番';
//		SegA18.innerHTML         = '<span class="CheckMark">◎</span>カテゴリ';
		SegA18.innerHTML         = 'カテゴリ';
		SegA03.innerHTML         = '<span class="CheckMark">◎</span>営業部署';
		SegA04.innerHTML         = '<span class="CheckMark">◎</span>開発担当者';
		SegA05.innerHTML         = '<span class="CheckMark">◎</span>製品名称(日本語)';
		SegA06.innerHTML         = '<span class="CheckMark">◎</span>製品名称(英語)';
		SegA07.innerHTML         = '商品名称';
		SegA08.innerHTML         = '製品単位';
		SegA09.innerHTML         = '荷姿単位';
		SegA11.innerHTML         = '対象年齢';
		SegA12.innerHTML         = '内箱(袋)入数';
		SegA13.innerHTML         = '<span class="CheckMark">◎</span>カートン入数';
		SegA14.innerHTML         = '<span class="CheckMark">◎</span>製品構成';
		SegA15.innerHTML         = 'アッセンブリ内容';
		SegA17.innerHTML         = 'アッセンブリ工場';
		CreateDate.innerHTML     = '作成日時';
		ProgressStatus.innerHTML = '企画進行状況';
		ReviseNumber.innerHTML   = '改訂番号';
		ReviseDate.innerHTML     = '改訂日時';

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

		SegB01.innerHTML = '<span class="CheckMark">◎</span>上代(pcs単価)';
		SegB02.innerHTML = '<span class="CheckMark">◎</span>納価(pcs単価)';
		SegB03.innerHTML = '生産工場';
		SegB04.innerHTML = '納品場所';
		SegB05.innerHTML = '<span class="CheckMark">◎</span>納期';
		SegB06.innerHTML = '<span class="CheckMark">◎</span>生産予定数';
		SegB07.innerHTML = '<span class="CheckMark">◎</span>初回納品数';
		SegB08.innerHTML = '顧客';
		SegB09.innerHTML = '顧客担当者';
//		SegB10.innerHTML = '<span class="CheckMark">◎</span>版権元';
		SegB10.innerHTML = '版権元';
//		SegB11.innerHTML = '<span class="CheckMark">◎</span>ロイヤリティ(%)';
		SegB11.innerHTML = 'ロイヤリティ(%)';
		SegB12.innerHTML = '版権表示(刻印)';
		SegB13.innerHTML = '版権表示(印刷物)';
		SegB14.innerHTML = '仕様詳細';
		SegB16.innerHTML = '証紙';
		SegB17.innerHTML = '<span class="CheckMark">◎</span>商品形態';
		SegB20.innerHTML = '版権元備考';





		//-------------------------------------------------------------------------
		// v2 tomita update
		//-------------------------------------------------------------------------
/*
		SegBWF.innerHTML = '承認ルート';
		window.WFrootWin.RootWin.fncChgEtoJ( 1 );

		RegistBt.innerHTML = registbtJ1;

		var obj = document.PPP2.lngWorkflowOrderCode;

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