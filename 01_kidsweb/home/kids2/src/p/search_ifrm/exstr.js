<!--


function ChgEtoJ( lngCount )
{


////////////////////////////////// ENGLISH /////////////////////////////////////
	if ( lngCount == 0 )
	{

		SegA01.innerText='Products code';
		SegA02.innerText='Goods code(Corresp)';
		SegA03.innerText='Dept';
		SegA04.innerText='In charge name';
		SegA18.innerText='Category';
		SegA05.innerText='Products name(ja)';
		SegA06.innerText='Products name(en)';
		SegA07.innerText='Goods name';
		SegA08.innerText='Unit';
		SegA09.innerText='Packing unit';
		SegA11.innerText='Age';
		SegA12.innerText='Box Qty';
		SegA13.innerText='Carton Qty';
		SegA14.innerText='Products Info';
		SegA15.innerText='Assembly Info';
		SegA17.innerText='Assembly fact';
		CreateDate.innerText='Creation date';
		ProgressStatus.innerText='Plan status';

		DetailSegs.innerText='Detail';
		InputSegs.innerText='Input person';
		FixSegs.innerText='Fix';
		DeleteSegs.innerText='Delete';

		//ReviseNumber.innerText='Revise No.';
		ReviseDate.innerText='Revise date';
		ViewSearch1.innerHTML= vishImgE;
		ViewSearch2.innerHTML= vishImgE;


		SegB01.innerText='Selling price';
		SegB02.innerText='Wholesale price';
		SegB03.innerText='Creation fact';
		SegB04.innerText='Location';
		SegB05.innerText='Deli date';
		SegB06.innerText='Refound Qty';
		SegB07.innerText='Delivery Qty';
		SegB08.innerText='Vendor';
		SegB09.innerText='In charge name';
		SegB10.innerText='Copyright';
		SegB11.innerText='Loyalty';
		SegB12.innerText='Copyright(Stamp)';
		SegB13.innerText='Copyright(Print)';
		SegB14.innerText='Details';
		SegB16.innerText='Inspection';
		SegB17.innerText='Goods form';
		SegB20.innerText='Copyright remark';


		//-------------------------------------------------------------------------
		// v2 tomita update
		//-------------------------------------------------------------------------
		WFStatus.innerText = 'Work Flow Status';
		//-------------------------------------------------------------------------
	}


////////////////////////////////// JAPANESE /////////////////////////////////////
	else if ( lngCount == 1 )
	{

		SegA01.innerText='���ʥ�����';
		SegA02.innerText='�ܵ�����';
		SegA03.innerText='�Ķ�����';
		SegA04.innerText='��ȯô����';
		SegA18.innerText='���ƥ���';
		SegA05.innerText='����̾��(���ܸ�)';
		SegA06.innerText='����̾��(�Ѹ�)';
		SegA07.innerText='����̾��';
		SegA08.innerText='����ñ��';
		SegA09.innerText='�ٻ�ñ��';
		SegA11.innerText='�о�ǯ��';
		SegA12.innerText='��Ȣ(��)����';
		SegA13.innerText='�����ȥ�����';
		SegA14.innerText='���ʹ���';
		SegA15.innerText='���å���֥�����';
		SegA17.innerText='���å���֥깩��';
		CreateDate.innerText='��������';
		ProgressStatus.innerText='���ʹԾ���';

		DetailSegs.innerText='�ܺ�';
		InputSegs.innerText='���ϼ�';
		FixSegs.innerText='����';
		DeleteSegs.innerText='���';

		//ReviseNumber.innerText='�����ֹ�';
		ReviseDate.innerText='��������';
		ViewSearch1.innerHTML= vishImgJ;
		ViewSearch2.innerHTML= vishImgJ;


		SegB01.innerText='����';
		SegB02.innerText='Ǽ��';
		SegB03.innerText='��������';
		SegB04.innerText='Ǽ�ʾ��';
		SegB05.innerText='Ǽ��';
		SegB06.innerText='����ͽ���';
		SegB07.innerText='���Ǽ�ʿ�';
		SegB08.innerText='�ܵ�';
		SegB09.innerText='�ܵ�ô����';
		SegB10.innerText='�Ǹ���';
		SegB11.innerText='�����ƥ�';
		SegB12.innerText='�Ǹ�ɽ��(���)';
		SegB13.innerText='�Ǹ�ɽ��(����ʪ)';
		SegB14.innerText='���;ܺ�';
		SegB16.innerText='�ڻ�';
		SegB17.innerText='���ʷ���';
		SegB20.innerText='�Ǹ�������';

		//-------------------------------------------------------------------------
		// v2 tomita update
		//-------------------------------------------------------------------------
		WFStatus.innerText = '����ե�����';
		//-------------------------------------------------------------------------
	}

	return false;

}


//-->