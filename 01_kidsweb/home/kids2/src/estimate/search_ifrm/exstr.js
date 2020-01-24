


function ChgEtoJ( lngCount )
{


////////////////////////////////// ENGLISH /////////////////////////////////////
	if ( lngCount == 0 )
	{
		col00.innerText = 'Detail';
		col01.innerText = 'Products code';
		col02.innerText = 'Products name(ja)';
		col03.innerText = 'Dept';
		col04.innerText = 'In charge name';
		col05.innerText = 'Input person';
		col06.innerText = 'Creation date';
		col07.innerText = 'Delivery date';
		col08.innerText = 'Wholesale price';
		col09.innerText = 'Selling price';
		col10.innerText = 'Carton Qty';
		col11.innerText = 'Plan Qty';
		col12.innerText = 'Refound Qty';
		col13.innerText = 'Estimate info';
		col14.innerText = 'Non Fixed cost';
		col15.innerText = 'Member total';
		col16.innerText = 'Production cost';
		col17.innerText = 'Plan Sales';
		col18.innerText = 'Target profit';
		col19.innerText = 'Profit ratio';
		col20.innerText = 'Production expense';
		col21.innerText = 'Gross profit';
		col22.innerText = 'Saved';

		WFStatus.innerText = 'Work Flow Status';


		FixSegs.innerHTML    = 'Fix';
		DeleteSegs.innerHTML = 'Delete';


		ViewSearch1.innerHTML= vishImgE;
	}


////////////////////////////////// JAPANESE /////////////////////////////////////
	else if ( lngCount == 1 )
	{
		col00.innerText = '詳細';
		col01.innerText = '製品コード';
		col02.innerText = '製品名称(日本語)';
		col03.innerText = '部門';
		col04.innerText = '担当者';
		col05.innerText = '入力者';
		col06.innerText = '作成日時';
		col07.innerText = '納期';
		col08.innerText = '納価';
		col09.innerText = '上代';
		col10.innerText = 'カートン入数';
		col11.innerText = '計画C/t';
		col12.innerText = '生産予定数';
		col13.innerText = '見積情報';
		col14.innerText = '償却合計金額';
		col15.innerText = '部材費合計';
		col16.innerText = '製造費用合計';
		col17.innerText = '予定総売上高';
		col18.innerText = '企画目標総利益';
		col19.innerText = '目標利益率';
		col20.innerText = '間接製造経費';
		col21.innerText = '売上総利益';
		col22.innerText = '保存状態';

		WFStatus.innerText = 'ワークフロー状態';

		FixSegs.innerHTML    = '修正';
		DeleteSegs.innerHTML = '削除';


		ViewSearch1.innerHTML= vishImgJ;
	}

	return false;
}
