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

		SegA01.innerText='製品コード';
		SegA02.innerText='顧客品番';
		SegA03.innerText='営業部署';
		SegA04.innerText='開発担当者';
		SegA18.innerText='カテゴリ';
		SegA05.innerText='製品名称(日本語)';
		SegA06.innerText='製品名称(英語)';
		SegA07.innerText='商品名称';
		SegA08.innerText='製品単位';
		SegA09.innerText='荷姿単位';
		SegA11.innerText='対象年齢';
		SegA12.innerText='内箱(袋)入数';
		SegA13.innerText='カートン入数';
		SegA14.innerText='製品構成';
		SegA15.innerText='アッセンブリ内容';
		SegA17.innerText='アッセンブリ工場';
		CreateDate.innerText='作成日時';
		ProgressStatus.innerText='企画進行状況';

		DetailSegs.innerText='詳細';
		InputSegs.innerText='入力者';
		FixSegs.innerText='修正';
		DeleteSegs.innerText='削除';

		//ReviseNumber.innerText='改訂番号';
		ReviseDate.innerText='改訂日時';
		ViewSearch1.innerHTML= vishImgJ;
		ViewSearch2.innerHTML= vishImgJ;


		SegB01.innerText='上代';
		SegB02.innerText='納価';
		SegB03.innerText='生産工場';
		SegB04.innerText='納品場所';
		SegB05.innerText='納期';
		SegB06.innerText='生産予定数';
		SegB07.innerText='初回納品数';
		SegB08.innerText='顧客';
		SegB09.innerText='顧客担当者';
		SegB10.innerText='版権元';
		SegB11.innerText='ロイヤリティ';
		SegB12.innerText='版権表示(刻印)';
		SegB13.innerText='版権表示(印刷物)';
		SegB14.innerText='仕様詳細';
		SegB16.innerText='証紙';
		SegB17.innerText='商品形態';
		SegB20.innerText='版権元備考';

		//-------------------------------------------------------------------------
		// v2 tomita update
		//-------------------------------------------------------------------------
		WFStatus.innerText = 'ワークフロー状態';
		//-------------------------------------------------------------------------
	}

	return false;

}


//-->