
function ChgEtoJ( lngCount )
{


////////////////////////////////// ENGLISH /////////////////////////////////////
	if ( lngCount == 0 )
	{

		ProductsCode.innerText   = 'Products code';
		ProductsNameJa.innerText = 'Products name(ja)';
		ProductsNameEn.innerText = 'Products name(en)';

		SegA01.innerText='Date';
		SegA02.innerText='P order No.';
		SegA03.innerText='Vendor';
		SegA04.innerText='Dept';
		SegA05.innerText='In charge name';
		SegA07.innerText='Status';
		SegA13.innerText='Remark';
		SegA14.innerText='Detail';
		SegA15.innerText='Regist date';
		SegA16.innerText='Total';
		SegA17.innerText='Record No.';
		SegA18.innerText='Input person';
		// SegB01.innerText='Products code/name';
		SegB02.innerText='Goods set';
		SegB05.innerText='Goods code(Corresp)';
		SegB06.innerText='Delivery date';
		SegB08.innerText='Price';
		SegB09.innerText='Unit';
		SegB10.innerText='Qty';
		SegB11.innerText='Amt Bfr tax';
		SegB12.innerText='Remark';
		SegB13.innerText='Fix';
		SegB14.innerText='Delete';

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
		SegCRC.innerHTML   = 'C order No.';
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

		SegA01.innerText='計上日';
		SegA02.innerText='受注ＮＯ.';
		SegA03.innerText='顧客';
		SegA04.innerText='部門';
		SegA05.innerText='担当者';
		SegA07.innerText='状態';
		SegA13.innerText='備考';
		SegA14.innerText='詳細';
		SegA15.innerText='登録日';
		SegA16.innerText='合計金額';
		SegA17.innerText='明細行番号';
		SegA18.innerText='入力者';
		// SegB01.innerText='製品コード・名称';
		SegB02.innerText='売上区分';
		SegB05.innerText='顧客品番';
		SegB06.innerText='納期';
		SegB08.innerText='単価';
		SegB09.innerText='単位';
		SegB10.innerText='数量';
		SegB11.innerText='税抜金額';
		SegB12.innerText='明細備考';
		SegB13.innerText='修正';
		SegB14.innerText='削除';

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
		SegCRC.innerHTML   = '顧客受注番号';
		WFStatus.innerText = 'ワークフロー状態';
		//-------------------------------------------------------------------------


		ViewSearch1.innerHTML= vishImgJ;


	}

	return false;

}