


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
		col00.innerText = '�ܺ�';
		col01.innerText = '���ʥ�����';
		col02.innerText = '����̾��(���ܸ�)';
		col03.innerText = '����';
		col04.innerText = 'ô����';
		col05.innerText = '���ϼ�';
		col06.innerText = '��������';
		col07.innerText = 'Ǽ��';
		col08.innerText = 'Ǽ��';
		col09.innerText = '����';
		col10.innerText = '�����ȥ�����';
		col11.innerText = '�ײ�C/t';
		col12.innerText = '����ͽ���';
		col13.innerText = '���Ѿ���';
		col14.innerText = '���ѹ�׶��';
		col15.innerText = '��������';
		col16.innerText = '��¤���ѹ��';
		col17.innerText = 'ͽ��������';
		col18.innerText = '�����ɸ������';
		col19.innerText = '��ɸ����Ψ';
		col20.innerText = '������¤����';
		col21.innerText = '���������';
		col22.innerText = '��¸����';

		WFStatus.innerText = '����ե�����';

		FixSegs.innerHTML    = '����';
		DeleteSegs.innerHTML = '���';


		ViewSearch1.innerHTML= vishImgJ;
	}

	return false;
}
