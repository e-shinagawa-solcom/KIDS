


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
		SegBHeader.innerHTML = headerBE;
		SegCHeader.innerHTML = headerCE;
		SegDHeader.innerHTML = headerDE;


		colProductsCode.innerText           = 'Products code';
		colProductsName.innerText           = 'Products name';
		colDeliveryDate.innerText           = 'Delivery date';
		colDeptCode.innerText               = 'Dept code';
		colInChargeCode.innerText           = 'In charge code';
		colInChargeName.innerText           = 'In charge name';
		colSellingPrice.innerText           = 'Selling price';
		colCartonQty.innerText              = 'Carton Qty';
		colPlanQty.innerText                = 'Plan Qty';
		colRefoundQty.innerText             = 'Refound Qty';
		colWholesalePrice.innerText         = 'Wholesale price';

		colStockSubject.innerText           = 'Stock subject';
		colInput.innerText                  = 'Input';
		colStockItemSample.innerText        = 'Stock item sample';
		colSubTotal.innerText               = 'Sub total';
		colPlanQuantity.innerText           = 'Plan Qty';

		colAmortizedValue.innerText         = 'Amortized value';
		colOutsideAmortizedValue.innerText  = 'Outside amortized value';
		colMaterialToolsCostPrice.innerText = 'Material tools cost price';
		colTotalFixedCost.innerText         = 'Total fixed cost';
		colImportPartsCostPrice.innerText   = 'Import parts cost price';
		colMaterialPartsCostPrice.innerText = 'Material parts cost price';
		colOutsourcingProcessCost.innerText = 'Outsourcing process cost';
		colCharge.innerText                 = 'Charge';
		colCost.innerText                   = 'Cost';
		colMemberTotal.innerText            = 'Member total';

		colWorkflowRoot.innerText           = 'Work flow root';





		colDetailStockSubject.innerText = 'Stock subject';
		colDetailStockItem.innerText    = 'Stock item';
		colDetailAmortized.innerText    = 'Amortized';
		colDetailSupplier.innerText     = 'Supplier';
		colDetailCurrency.innerText     = 'Currency';
		colDetailRate.innerText         = 'Rate';
		colDetailPlanQty.innerText      = 'Plan Qty';
		colDetailPrice.innerText        = 'Price';
		colDetailPlanEstimate.innerText = 'Plan estimate';
		colDetailInput.innerText        = '% input';
		colDetailPlanRate.innerText     = 'Plan rate';
		colDetailSubTotal.innerText     = 'Stock subject sub total';
		colDetailReamrk.innerText       = 'Remark';




		dlStockSubject.innerText = 'Stock subject';
		dlStockItem.innerText    = 'Stock item';
		dlAmortized.innerText    = 'Amortized';
		dlSupplier.innerText     = 'Supplier';
		dlPlanQty.innerText      = 'Plan Qty';
		dlprice.innerText        = 'Price';
		dlPlanEstimate.innerText = 'Plan estimate';
		dlRemark.innerText       = 'Remark';





		///// QUERY BUTTON /////
		Confirm1Bt.innerHTML = confirmbtE1;
		Confirm2Bt.innerHTML = confirmbtE1;
//		PreSaveBt.innerHTML  = presavebtE1;
		DecisionBt.innerHTML = decisionbtE1;

		AddRowBt.innerHTML    = addrowbtE1;
		DelRowBt.innerHTML    = delrowbtE1;
		CommitBt.innerHTML    = commitbtE1;
		SegAClearBt.innerHTML = clearbtAE1;


		RegistBt.innerHTML = registbtE1;

/*
		var obj = document.HSO.lngWorkflowOrderCode;

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


		lngLanguageCode = 0;
		lngClickCode = 1;

		window.NAVIwin.ChgEtoJ( 0 );
		window.MGwin.Msw3ChgEtoJ( 0 );
		window.SUPwin.Msw8ChgEtoJ( 0 );
		window.WFrootWin.RootWin.fncChgEtoJ( 0 );
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
		SegBHeader.innerHTML = headerBJ;
		SegCHeader.innerHTML = headerCJ;
		SegDHeader.innerHTML = headerDJ;


		colProductsCode.innerText           = '���ʥ�����';
		colProductsName.innerText           = '����̾';
		colDeliveryDate.innerText           = 'Ǽ��';
		colDeptCode.innerText               = '���祳����';
		colInChargeCode.innerText           = 'ô��������';
		colInChargeName.innerText           = 'ô����̾';
		colSellingPrice.innerText           = '����';
		colCartonQty.innerText              = '�����ȥ�����';
		colPlanQty.innerText                = '�ײ�C/t';
		colRefoundQty.innerText             = '����ͽ���';
		colWholesalePrice.innerText         = 'Ǽ��';

		colStockSubject.innerText           = '��������';
		colInput.innerText                  = '����';
		colStockItemSample.innerText        = '����������';
		colSubTotal.innerText               = '����';
		colPlanQuantity.innerText           = '�ײ�Ŀ�';

		colAmortizedValue.innerText         = '�ⷿ���ѹ�';
		colOutsideAmortizedValue.innerText  = '�ⷿ�������ѹ�';
		colMaterialToolsCostPrice.innerText = '�����ġ��������';
		colTotalFixedCost.innerText         = '��������';
		colImportPartsCostPrice.innerText   = '͢���ѡ��Ļ�����';
		colMaterialPartsCostPrice.innerText = '�����ѡ��Ļ�����';
		colOutsourcingProcessCost.innerText = '����ù���';
		colCharge.innerText                 = '���㡼��';
		colCost.innerText                   = '����';
		colMemberTotal.innerText            = '��������';

		colWorkflowRoot.innerText           = '��ǧ�롼��';





		colDetailStockSubject.innerText = '��������';
		colDetailStockItem.innerText    = '��������';
		colDetailAmortized.innerText    = '�����о�';
		colDetailSupplier.innerText     = '������';
		colDetailCurrency.innerText     = '�̲�';
		colDetailRate.innerText         = '�����졼��';
		colDetailPlanQty.innerText      = '�ײ�Ŀ�';
		colDetailPrice.innerText        = 'ñ��';
		colDetailPlanEstimate.innerText = '�ײ踶��';
		colDetailInput.innerText        = '������';
		colDetailPlanRate.innerText     = '�ײ�Ψ';
		colDetailSubTotal.innerText     = '�������ܾ���';
		colDetailReamrk.innerText       = '����';





		dlStockSubject.innerText = '��������';
		dlStockItem.innerText    = '��������';
		dlAmortized.innerText    = '�����о�';
		dlSupplier.innerText     = '������';
		dlPlanQty.innerText      = '�ײ�Ŀ�';
		dlprice.innerText        = 'ñ��';
		dlPlanEstimate.innerText = '�ײ踶��';
		dlRemark.innerText       = '����';





		///// QUERY BUTTON /////
		Confirm1Bt.innerHTML = confirmbtJ1;
		Confirm2Bt.innerHTML = confirmbtJ1;
//		PreSaveBt.innerHTML  = presavebtJ1;
		DecisionBt.innerHTML = decisionbtJ1;

		AddRowBt.innerHTML    = addrowbtJ1;
		DelRowBt.innerHTML    = delrowbtJ1;
		CommitBt.innerHTML    = commitbtJ1;
		SegAClearBt.innerHTML = clearbtAJ1;


		RegistBt.innerHTML = registbtJ1;

/*
		var obj = document.HSO.lngWorkflowOrderCode;

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


		lngLanguageCode = 1;
		lngClickCode = 0;

		window.NAVIwin.ChgEtoJ( 1 );
		window.MGwin.Msw3ChgEtoJ( 1 );
		window.SUPwin.Msw8ChgEtoJ( 1 );
		window.WFrootWin.RootWin.fncChgEtoJ( 1 );
	}

	return false;

}
