<!--


function ChgEtoJ( lngCount )
{


////////////////////////////////// ENGLISH /////////////////////////////////////
	if ( lngCount == 0 )
	{
		WFStatus.innerText       = 'Work Flow Status';
		ProductsCode.innerText   = 'Products code';
		ProductsNameJa.innerText = 'Products name(ja)';
		ProductsNameEn.innerText = 'Products name(en)';
		InjectionMold.innerText  = 'No.';
		DeliveryDate.innerText   = 'Delivery date';

		SegA01.innerText = 'Date';
		SegA02.innerText = 'P order No.';
		SegA03.innerText = 'Vendor';
		SegA04.innerText = 'Dept';
		SegA05.innerText = 'In charge name';
		SegA07.innerText = 'Status';
		SegA11.innerText = 'Pay condition';
		SegA12.innerText = 'PO limit date';
		SegA13.innerText = 'Remark';
		SegA14.innerText = 'Detail';
		SegA15.innerText = 'Regist date';
		SegA16.innerText = 'Total';
		SegA17.innerText = 'Record No.';
		SegA18.innerText = 'Input person';

		//SegB01.innerText = 'Products code/name';

		SegB02.innerText = 'Goods set';
		SegB03.innerText = 'Goods parts';

		SegB05.innerText = 'Goods code(Corresp)';
		SegB06.innerText = 'Means of transport';

		SegB08.innerText = 'Price';
		SegB09.innerText = 'Unit';
		SegB10.innerText = 'Qty';
		SegB11.innerText = 'Amt Bfr tax';

		SegB12.innerText = 'Remark';

		SegB13.innerText = 'Fix';
		SegB14.innerText = 'Delete';

		if( typeof(InvalidSegs) != 'undefined' )
		{
			InvalidSegs.innerText = 'Invalid';
		}

		if( typeof(AdminSet) != 'undefined' )
		{
			AdminMode.innerText = 'Administrator mode';
		}


		ViewSearch1.innerHTML = vishImgE;

	}


////////////////////////////////// JAPANESE /////////////////////////////////////
	else if ( lngCount == 1 )
	{
		WFStatus.innerText       = '����ե�����';
		ProductsCode.innerText   = '���ʥ�����';
		ProductsNameJa.innerText = '����̾��(���ܸ�)';
		ProductsNameEn.innerText = '����̾��(�Ѹ�)';
		InjectionMold.innerText  = '�Σ';
		DeliveryDate.innerText   = 'Ǽ��';

		SegA01.innerText = '�׾���';
		SegA02.innerText = 'ȯ��Σ�.';
		SegA03.innerText = '������';
		SegA04.innerText = '����';
		SegA05.innerText = 'ô����';
		SegA07.innerText = '����';
		SegA11.innerText = '��ʧ���';
		SegA12.innerText = 'ȯ��ͭ��������';
		SegA13.innerText = '����';
		SegA14.innerText = '�ܺ�';
		SegA15.innerText = '��Ͽ��';
		SegA16.innerText = '��׶��';
		SegA17.innerText = '���ٹ��ֹ�';
		SegA18.innerText = '���ϼ�';

		//SegB01.innerText='���ʥ����ɡ�̾��';

		SegB02.innerText='��������';
		SegB03.innerText='��������';
		SegB05.innerText='�ܵ�����';
		SegB06.innerText='������ˡ';
		SegB08.innerText='ñ��';
		SegB09.innerText='ñ��';
		SegB10.innerText='����';
		SegB11.innerText='��ȴ���';
		SegB12.innerText='��������';

		SegB13.innerText='����';
		SegB14.innerText='���';

		if( typeof(InvalidSegs) != 'undefined' )
		{
			InvalidSegs.innerText='̵��';
		}

		if( typeof(AdminSet) != 'undefined' )
		{
			AdminMode.innerText='�����ԥ⡼��';
		}


		ViewSearch1.innerHTML= vishImgJ;

	}

	return false;

}


//-->