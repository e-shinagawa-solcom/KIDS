<!--


function fncChgEtoJ( strMode )
{
	if ( g_lngCode == 0 )
	{

		// �����ѥơ��֥�񤭽Ф�
		fncProcessingOutputModule( strMode , 0 );

		if( typeof(SimilarProducts) != 'undefined' )
		{
			SimilarProducts.innerText = 	'Similar products(Name)';
		}


		CreationDate.innerText = 			'Creation date';
		GoodsPlanProgress.innerText = 		'Plan status';
		RevisionNo.innerText = 				'Revise No.';
		RevisionDate.innerText = 			'Revise date';
		ProductCode.innerText = 			'Product code';
		ProductName.innerText = 			'Products name(ja)';
		ProductEnglishName.innerText = 		'Products name(en)';
		InChargeGroup.innerText = 			'Dept';
		InChargeUser.innerText = 			'In charge name';
		GoodsCode.innerText = 				'Goods code(Corresp)';
		GoodsName.innerText = 				'Goods name';
		Customer.innerText = 				'Vendor';
		CustomerUser.innerText = 			'In charge name';
		PackingUnit.innerText = 			'Packing Unit';
		ProductUnit.innerText = 			'Product Unit';
		GoodsForm.innerText = 				'Goods Form';
		BoxQuantity.innerText = 			'Box Qty';
		CartonQuantity.innerText = 			'Carton Qty';
		RefoundQuantity.innerText = 		'Refound Qty';
		FirstDeliveryQuantity.innerText = 	'Delivery Qty';
		CreationFactory.innerText = 		'Creation factory';
		AssemblyFactory.innerText = 		'Assembly factory';
		Location.innerText = 				'Location';
		DeliveryLimitDate.innerText = 		'Delivery date';
		SellingPrince.innerText = 			'Selling prince(pcs)';
		WholesalePrice.innerText = 			'Wholesale price(pcs)';
		TargetAge.innerText = 				'Target age';
		Royalty.innerText = 				'Royalty(%)';
		Inspection.innerText = 				'Inspection';
		Copyright.innerText = 				'Copyright';
		CopyrightRemark.innerText = 		'Copyright remark';
		CopyrightStamp.innerText = 			'Copyright(Stamp)';
		CopyrightPrint.innerText = 			'Copyright(Print)';
		ProductsInfo.innerText = 			'Products info';
		AssemblyInfo.innerText = 			'Assembly info';
		Details.innerText = 				'Details';

	}

	else if ( g_lngCode == 1 )
	{

		// �����ѥơ��֥�񤭽Ф�
		fncProcessingOutputModule( strMode , 1 );


		if( typeof(SimilarProducts) != 'undefined' )
		{
			SimilarProducts.innerText = 	'�������(̾��)';
		}


		CreationDate.innerText = 			'��������';
		GoodsPlanProgress.innerText = 		'���ʹԾ���';
		RevisionNo.innerText = 				'�����ֹ�';
		RevisionDate.innerText = 			'��������';
		ProductCode.innerText = 			'���ʥ�����';
		ProductName.innerText = 			'����̾��(���ܸ�)';
		ProductEnglishName.innerText = 		'����̾��(�Ѹ�)';
		InChargeGroup.innerText = 			'�Ķ�����';
		InChargeUser.innerText = 			'��ȯô����';
		GoodsCode.innerText = 				'�ܵ�����';
		GoodsName.innerText = 				'����̾��';
		Customer.innerText = 				'�ܵ�';
		CustomerUser.innerText = 			'�ܵ�ô����';
		PackingUnit.innerText = 			'�ٻ�ñ��';
		ProductUnit.innerText = 			'����ñ��';
		GoodsForm.innerText = 				'���ʷ���';
		BoxQuantity.innerText = 			'��Ȣ(��)����';
		CartonQuantity.innerText = 			'�����ȥ�����';
		RefoundQuantity.innerText = 		'����ͽ���';
		FirstDeliveryQuantity.innerText = 	'���Ǽ�ʿ�';
		CreationFactory.innerText = 		'��������';
		AssemblyFactory.innerText = 		'���å���֥깩��';
		Location.innerText = 				'Ǽ�ʾ��';
		DeliveryLimitDate.innerText = 		'Ǽ��';
		SellingPrince.innerText = 			'Ǽ��(pcs)';
		WholesalePrice.innerText = 			'����(pcs)';
		TargetAge.innerText = 				'�о�ǯ��';
		Royalty.innerText = 				'�����ƥ�(%)';
		Inspection.innerText = 				'�ڻ�';
		Copyright.innerText = 				'�Ǹ���';
		CopyrightRemark.innerText = 		'�Ǹ�������';
		CopyrightStamp.innerText = 			'�Ǹ�ɽ��(���)';
		CopyrightPrint.innerText = 			'�Ǹ�ɽ��(����ʪ)';
		ProductsInfo.innerText = 			'���ʹ���';
		AssemblyInfo.innerText = 			'���å���֥�����';
		Details.innerText = 				'���;ܺ�';

	}

	return false;

}


//-->