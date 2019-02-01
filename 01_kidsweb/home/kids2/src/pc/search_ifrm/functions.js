<!--


///// SearchColumn[] Check /////
var checkcount1 = 0;

function fncCheckAll1()
{
	var lngCnt = document.getElementsByName('SearchColumn[]').length;

	if (checkcount1 == 0)
	{

		for( i = 0; i < lngCnt; i++ )
		{
			if( document.getElementsByName('SearchColumn[]')[i].disabled == false)
			{
				document.getElementsByName('SearchColumn[]')[i].checked = true;
			}
		}

		checkcount1 = 1;
		CheckAll1.innerHTML = onBt;
	}

	else if(checkcount1 == 1)
	{

		for( i = 0; i < lngCnt; i++ )
		{
			if( document.getElementsByName('SearchColumn[]')[i].disabled == false)
			{
				document.getElementsByName('SearchColumn[]')[i].checked = false;
			}
		}

		checkcount1 = 0;
		CheckAll1.innerHTML = offBt;
	}
	return false;
}



///// ViewColumn[] Check /////
var checkcount2 = 0;

function fncCheckAll2()
{
	var lngCnt = document.getElementsByName('ViewColumn[]').length;

	if (checkcount2 == 0)
	{

		for( i = 0; i < lngCnt; i++ )
		{
			if( document.getElementsByName('ViewColumn[]')[i].disabled == false)
			{
				document.getElementsByName('ViewColumn[]')[i].checked = true;
			}
		}

		checkcount2 = 1;
		CheckAll2.innerHTML = onBt;
	}

	else if(checkcount2 == 1)
	{

		for( i = 0; i < lngCnt; i++ )
		{
			if( document.getElementsByName('ViewColumn[]')[i].disabled == false)
			{
				document.getElementsByName('ViewColumn[]')[i].checked = false;
			}
		}

		checkcount2 = 0;
		CheckAll2.innerHTML = offBt;
	}
	return false;
}





///// Admin Check /////
// 明細行番号、製品名称、仕入科目、仕入部品、顧客品番、運搬方法、単価、単位、数量、税抜金額、明細備考、無効ボタン
// 15, 19〜34 + 1, 36
function fncAdminSet()
{
	if( document.all.Admin.checked == true )
	{
		// 明細行番号
		document.getElementsByName('ViewColumn[]')[15].disabled = true;

		if( document.getElementsByName('ViewColumn[]')[15].checked == true )
		{
			document.getElementsByName('ViewColumn[]')[15].checked = false;
		}

		// 製品名称、仕入科目、仕入部品、顧客品番、運搬方法、単価、単位、数量、税抜金額、明細備考
		for( i = 17; i < 34 + 1; i++ )
		{
			document.getElementsByName('ViewColumn[]')[i].disabled = true;

			if( document.getElementsByName('ViewColumn[]')[i].checked == true )
			{
				document.getElementsByName('ViewColumn[]')[i].checked = false;
			}
		}

		// 無効ボタン
		document.getElementsByName('ViewColumn[]')[36].disabled = false;
		document.getElementsByName('ViewColumn[]')[36].checked  = true;
	}
	else
	{
		// 明細行番号
		document.getElementsByName('ViewColumn[]')[15].disabled = false;

		if( document.getElementsByName('ViewColumn[]')[15].checked == false )
		{
			document.getElementsByName('ViewColumn[]')[15].checked = true;
		}

		// 製品名称、仕入科目、仕入部品、顧客品番、運搬方法、単価、単位、数量、税抜金額、明細備考
		for( i = 17; i < 34 + 1; i++ )
		{
			document.getElementsByName('ViewColumn[]')[i].disabled = false;
		}

		// 無効ボタン
		document.getElementsByName('ViewColumn[]')[36].disabled = true;
		document.getElementsByName('ViewColumn[]')[36].checked  = false;
	}
}


//-->