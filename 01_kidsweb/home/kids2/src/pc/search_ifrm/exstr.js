

function ChgEtoJ( lngCount )
{


////////////////////////////////// ENGLISH /////////////////////////////////////
	if ( lngCount == 0 )
	{

		ProductsCode.innerText   = 'Products code';
		ProductsNameJa.innerText = 'Products name(ja)';
		ProductsNameEn.innerText = 'Products name(en)';
		InjectionMold.innerText  = 'No.';
		DeliveryDate.innerText   = 'Delivery date';

		SegA01.innerText='Date';
		SegA02.innerText='P control No.';
		SegA03.innerText='Vendor';
		SegA04.innerText='Dept';
		SegA05.innerText='In charge name';
		SegA07.innerText='Status';
		SegA11.innerText='Pay condition';
		SegA12.innerText='Arraival date';
		SegA13.innerText='Remark';
		SegA14.innerText='P oeder No.';
		SegA15.innerText='Shipping No.';
		SegA16.innerText='Detail';
		SegA17.innerText='Regist date';
		SegA20.innerText='Input person';
		SegA18.innerText='total';
		SegA19.innerText='Record No.';

		// SegB01.innerText='Products code/name';
		SegB02.innerText='Goods set';
		SegB03.innerText='Goods parts';
		SegB05.innerText='Goods code(Corresp)';
		SegB06.innerText='Means of transport';
		SegB08.innerText='Price';
		SegB09.innerText='Unit';
		SegB10.innerText='Qty';
		SegB11.innerText='Amt Bfr tax';
		SegB12.innerText='Remark';
		SegB13.innerText='Tax type';
		SegB14.innerText='Tax rate';
		SegB15.innerText='Tax';
		SegB16.innerText='Fix';
		SegB17.innerText='Delete';

		if( typeof(InvalidSegs) != 'undefined' )
		{
			InvalidSegs.innerText='Invalid';
		}

		if( typeof(AdminSet) != 'undefined' )
		{
			AdminMode.innerText='Administrator mode';
		}


		//-------------------------------------------------------------------------
		// v2 tomita update
		//-------------------------------------------------------------------------
		WFStatus.innerText = 'Work Flow Status';
		//-------------------------------------------------------------------------

		ViewSearch1.innerHTML= vishImgE;



	}


////////////////////////////////// JAPANESE /////////////////////////////////////
	else if ( lngCount == 1 )
	{

		ProductsCode.innerText   = '製品コード';
		ProductsNameJa.innerText = '製品名称(日本語)';
		ProductsNameEn.innerText = '製品名称(英語)';
		InjectionMold.innerText  = 'Ｎｏ．';
		DeliveryDate.innerText   = '納期';

		SegA01.innerText='計上日';
		SegA02.innerText='仕入ＮＯ.';
		SegA03.innerText='仕入先';
		SegA04.innerText='部門';
		SegA05.innerText='担当者';
		SegA07.innerText='状態';
		SegA11.innerText='支払条件';
		SegA12.innerText='製品到着日';
		SegA13.innerText='備考';
		SegA14.innerText='発注ＮＯ.';
		SegA15.innerText='納品書ＮＯ.';
		SegA16.innerText='詳細';
		SegA17.innerText='登録日';
		SegA20.innerText='入力者';
		SegA18.innerText='合計金額';
		SegA19.innerText='明細行番号';

		// SegB01.innerText='製品コード・名称';
		SegB02.innerText='仕入科目';
		SegB03.innerText='仕入部品';
		SegB05.innerText='顧客品番';
		SegB06.innerText='運搬方法';
		SegB08.innerText='単価';
		SegB09.innerText='単位';
		SegB10.innerText='数量';
		SegB11.innerText='税抜金額';
		SegB12.innerText='明細備考';
		SegB13.innerText='税区分';
		SegB14.innerText='税率';
		SegB15.innerText='税額';
		SegB16.innerText='修正';
		SegB17.innerText='削除';

		if( typeof(InvalidSegs) != 'undefined' )
		{
			InvalidSegs.innerText='無効';
		}

		if( typeof(AdminSet) != 'undefined' )
		{
			AdminMode.innerText='管理者モード';
		}


		//-------------------------------------------------------------------------
		// v2 tomita update
		//-------------------------------------------------------------------------
		WFStatus.innerText = 'ワークフロー状態';
		//-------------------------------------------------------------------------

		ViewSearch1.innerHTML= vishImgJ;


	}

	return false;

}