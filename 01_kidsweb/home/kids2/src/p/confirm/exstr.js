<!--


function fncChgEtoJ( strMode )
{
	if ( g_lngCode == 0 )
	{

		// 処理用テーブル書き出し
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

		// 処理用テーブル書き出し
		fncProcessingOutputModule( strMode , 1 );


		if( typeof(SimilarProducts) != 'undefined' )
		{
			SimilarProducts.innerText = 	'類似製品(名称)';
		}


		CreationDate.innerText = 			'作成日時';
		GoodsPlanProgress.innerText = 		'企画進行状況';
		RevisionNo.innerText = 				'改訂番号';
		RevisionDate.innerText = 			'改訂日時';
		ProductCode.innerText = 			'製品コード';
		ProductName.innerText = 			'製品名称(日本語)';
		ProductEnglishName.innerText = 		'製品名称(英語)';
		InChargeGroup.innerText = 			'営業部署';
		InChargeUser.innerText = 			'開発担当者';
		GoodsCode.innerText = 				'顧客品番';
		GoodsName.innerText = 				'商品名称';
		Customer.innerText = 				'顧客';
		CustomerUser.innerText = 			'顧客担当者';
		PackingUnit.innerText = 			'荷姿単位';
		ProductUnit.innerText = 			'製品単位';
		GoodsForm.innerText = 				'商品形態';
		BoxQuantity.innerText = 			'内箱(袋)入数';
		CartonQuantity.innerText = 			'カートン入数';
		RefoundQuantity.innerText = 		'生産予定数';
		FirstDeliveryQuantity.innerText = 	'初回納品数';
		CreationFactory.innerText = 		'生産工場';
		AssemblyFactory.innerText = 		'アッセンブリ工場';
		Location.innerText = 				'納品場所';
		DeliveryLimitDate.innerText = 		'納期';
		SellingPrince.innerText = 			'納価(pcs)';
		WholesalePrice.innerText = 			'上代(pcs)';
		TargetAge.innerText = 				'対象年齢';
		Royalty.innerText = 				'ロイヤリティ(%)';
		Inspection.innerText = 				'証紙';
		Copyright.innerText = 				'版権元';
		CopyrightRemark.innerText = 		'版権元備考';
		CopyrightStamp.innerText = 			'版権表示(刻印)';
		CopyrightPrint.innerText = 			'版権表示(印刷物)';
		ProductsInfo.innerText = 			'製品構成';
		AssemblyInfo.innerText = 			'アッセンブリ内容';
		Details.innerText = 				'仕様詳細';

	}

	return false;

}


//-->