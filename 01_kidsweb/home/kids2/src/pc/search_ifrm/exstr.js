

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

		ProductsCode.innerText   = '���ʥ�����';
		ProductsNameJa.innerText = '����̾��(���ܸ�)';
		ProductsNameEn.innerText = '����̾��(�Ѹ�)';
		InjectionMold.innerText  = '�Σ';
		DeliveryDate.innerText   = 'Ǽ��';

		SegA01.innerText='�׾���';
		SegA02.innerText='�����Σ�.';
		SegA03.innerText='������';
		SegA04.innerText='����';
		SegA05.innerText='ô����';
		SegA07.innerText='����';
		SegA11.innerText='��ʧ���';
		SegA12.innerText='����������';
		SegA13.innerText='����';
		SegA14.innerText='ȯ��Σ�.';
		SegA15.innerText='Ǽ�ʽ�Σ�.';
		SegA16.innerText='�ܺ�';
		SegA17.innerText='��Ͽ��';
		SegA20.innerText='���ϼ�';
		SegA18.innerText='��׶��';
		SegA19.innerText='���ٹ��ֹ�';

		// SegB01.innerText='���ʥ����ɡ�̾��';
		SegB02.innerText='��������';
		SegB03.innerText='��������';
		SegB05.innerText='�ܵ�����';
		SegB06.innerText='������ˡ';
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