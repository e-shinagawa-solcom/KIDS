


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


		colProductsCode.innerText           = '製品コード';
		colProductsName.innerText           = '製品名';
		colDeliveryDate.innerText           = '納期';
		colDeptCode.innerText               = '部門コード';
		colInChargeCode.innerText           = '担当コード';
		colInChargeName.innerText           = '担当者名';
		colSellingPrice.innerText           = '上代';
		colCartonQty.innerText              = 'カートン入数';
		colPlanQty.innerText                = '計画C/t';
		colRefoundQty.innerText             = '生産予定数';
		colWholesalePrice.innerText         = '納価';

		colStockSubject.innerText           = '仕入科目';
		colInput.innerText                  = '入力';
		colStockItemSample.innerText        = '仕入部品例';
		colSubTotal.innerText               = '小計';
		colPlanQuantity.innerText           = '計画個数';

		colAmortizedValue.innerText         = '金型償却高';
		colOutsideAmortizedValue.innerText  = '金型海外償却高';
		colMaterialToolsCostPrice.innerText = '材料ツール仕入高';
		colTotalFixedCost.innerText         = '固定費合計';
		colImportPartsCostPrice.innerText   = '輸入パーツ仕入高';
		colMaterialPartsCostPrice.innerText = '材料パーツ仕入高';
		colOutsourcingProcessCost.innerText = '外注加工費';
		colCharge.innerText                 = 'チャージ';
		colCost.innerText                   = '経費';
		colMemberTotal.innerText            = '部材費合計';

		colWorkflowRoot.innerText           = '承認ルート';





		colDetailStockSubject.innerText = '仕入科目';
		colDetailStockItem.innerText    = '仕入部品';
		colDetailAmortized.innerText    = '償却対象';
		colDetailSupplier.innerText     = '仕入先';
		colDetailCurrency.innerText     = '通貨';
		colDetailRate.innerText         = '換算レート';
		colDetailPlanQty.innerText      = '計画個数';
		colDetailPrice.innerText        = '単価';
		colDetailPlanEstimate.innerText = '計画原価';
		colDetailInput.innerText        = '％入力';
		colDetailPlanRate.innerText     = '計画率';
		colDetailSubTotal.innerText     = '仕入科目小計';
		colDetailReamrk.innerText       = '備考';





		dlStockSubject.innerText = '仕入科目';
		dlStockItem.innerText    = '仕入部品';
		dlAmortized.innerText    = '償却対象';
		dlSupplier.innerText     = '仕入先';
		dlPlanQty.innerText      = '計画個数';
		dlprice.innerText        = '単価';
		dlPlanEstimate.innerText = '計画原価';
		dlRemark.innerText       = '備考';





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


		lngLanguageCode = 1;
		lngClickCode = 0;

		window.NAVIwin.ChgEtoJ( 1 );
		window.MGwin.Msw3ChgEtoJ( 1 );
		window.SUPwin.Msw8ChgEtoJ( 1 );
		window.WFrootWin.RootWin.fncChgEtoJ( 1 );
	}

	return false;

}
