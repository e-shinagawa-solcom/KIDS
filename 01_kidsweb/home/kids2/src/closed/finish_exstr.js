<!--


var BackBtJ1 = '<a href="#"><img onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onmouseover="BlownBackJOn(this);" onmouseout="BlownBackJOff(this);" src="' + blownbackJ1 + '" width="72" height="20" border="0" alt="戻る"></a>';

var BackBtE1 = '<a href="#"><img onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onmouseover="BlownBackEOn(this);" onmouseout="BlownBackEOff(this);fncAlphaOff( this );" src="' + blownbackE1 + '" width="72" height="20" border="0" alt="BACK"></a>';

function ChgEtoJ( lngCode )
{

	if ( lngCode == 0 )
	{

		BackBt.innerHTML = BackBtE1;

		//strComments.innerHTML = 'Selected data is used in the following item.';
		Column0.innerHTML     = 'Control name';
		Column1.innerHTML     = 'No.';

		ControlTitle.innerHTML = 'PROCESSING COMPLETED';
	}

	else if ( lngCode == 1 )
	{

		BackBt.innerHTML = BackBtJ1;

		//strComments.innerHTML = '選択されたデータは下記の項目にて使用されています。';
		Column0.innerHTML     = '管理名称';
		Column1.innerHTML     = 'ＮＯ.';

		ControlTitle.innerHTML = '処理完了';
	}

	return false;

}


//-->