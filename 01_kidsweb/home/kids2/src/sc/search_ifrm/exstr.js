

function ChgEtoJ( lngCount )
{


////////////////////////////////// ENGLISH /////////////////////////////////////
	if ( lngCount == 0 )
	{

		ProductsCode.innerText   = 'Products code';
		ProductsNameJa.innerText = 'Products name(ja)';
		ProductsNameEn.innerText = 'Products name(en)';

		SegA01.innerText='Date';
		SegA02.innerText='S control No.';
		SegA03.innerText='Vendor';
		SegA04.innerText='Dept';
		SegA05.innerText='In charge name';
		SegA07.innerText='Status';
		SegA13.innerText='Remark';
		SegA14.innerText='Customer receive code';
		SegA15.innerText='Shipping No.';

		SegA16.innerText='Detail';
		SegA17.innerText='Regist date';
		SegA18.innerText='Total';
		SegA19.innerText='Record No.';
		SegA20.innerText='Input person';

		// SegB01.innerText='Products';
		SegB02.innerText='Goods set';
		SegB05.innerText='Goods code';
		SegB06.innerText='Delivery date';
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

		ProductsCode.innerText   = '���ʥ�����';
		ProductsNameJa.innerText = '����̾��(���ܸ�)';
		ProductsNameEn.innerText = '����̾��(�Ѹ�)';

		SegA01.innerText='�׾���';
		SegA02.innerText='���Σ�.';
		SegA03.innerText='�ܵ�';
		SegA04.innerText='����';
		SegA05.innerText='ô����';
		SegA07.innerText='����';
		SegA13.innerText='����';
		SegA14.innerText='�ܵҼ����ֹ�';
		SegA15.innerText='Ǽ�ʽ�Σ�.';
		SegA16.innerText='�ܺ�';
		SegA17.innerText='��Ͽ��';
		SegA18.innerText='��׶��';
		SegA19.innerText='���ٹ��ֹ�';
		SegA20.innerText='���ϼ�';

		// SegB01.innerText='���ʥ����ɡ�̾��';
		SegB02.innerText='����ʬ';
		SegB05.innerText='�ܵ�����';
		SegB06.innerText='Ǽ��';
		SegB08.innerText='ñ��';
		SegB09.innerText='ñ��';
		SegB10.innerText='����';
		SegB11.innerText='��ȴ���';
		SegB12.innerText='��������';
		SegB13.innerText='�Ƕ�ʬ';
		SegB14.innerText='��Ψ';
		SegB15.innerText='�ǳ�';
		SegB16.innerText='����';
		SegB17.innerText='���';

		if( typeof(InvalidSegs) != 'undefined' )
		{
			InvalidSegs.innerText='̵��';
		}

		if( typeof(AdminSet) != 'undefined' )
		{
			AdminMode.innerText='�����ԥ⡼��';
		}


		//-------------------------------------------------------------------------
		// v2 tomita update
		//-------------------------------------------------------------------------
		WFStatus.innerText = '����ե�����';
		//-------------------------------------------------------------------------

		ViewSearch1.innerHTML= vishImgJ;


	}

	return false;

}