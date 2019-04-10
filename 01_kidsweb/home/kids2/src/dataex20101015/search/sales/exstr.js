<!--


//------------------------------------------------------------
// 解説 : ヘッダーイメージの定義
//------------------------------------------------------------
var headerAJ = '<img src="' + headtitleAJ + '" width="949" height="30" border="0" alt="売上レシピ">';
var headerAE = '<img src="' + headtitleAE + '" width="949" height="30" border="0" alt="SALES RECIPE FILE">';


//------------------------------------------------------------
// 解説 : プレビューボタンイメージの定義
//------------------------------------------------------------
var blownpreviewBt = '<a href="#"><img onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onmouseover="fncBlownPreviewButton( \'on\' , this );" onmouseout="fncBlownPreviewButton( \'off\' , this );fncAlphaOff( this );" src="' + blownpreviewbt1 + '" width="72" height="20" border="0" alt="PREVIEW"></a>';


//------------------------------------------------------------
// 解説 : エクスポートボタンイメージの定義
//------------------------------------------------------------
var blownexportBt = '<a href="#"><img onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onmouseover="fncBlownExportButton( \'on\' , this );" onmouseout="fncBlownExportButton( \'off\' , this );fncAlphaOff( this );" src="' + blownexportbt1 + '" width="72" height="20" border="0" alt="PREVIEW"></a>';





//------------------------------------------------------------
// 解説 : 日本語・英語切替関数
//------------------------------------------------------------
function ChgEtoJ( lngCount )
{

	// プレビューボタンの書出し
	PreviewBt.innerHTML = blownpreviewBt;
	// エクスポートボタンの書出し
	ExportBt.innerHTML  = blownexportBt;

	// プレビューボタンの書出し
	PreviewBt2.innerHTML = blownpreviewBt;
	// エクスポートボタンの書出し
	ExportBt2.innerHTML  = blownexportBt;


	// 英語
	if ( lngCount == 0 )
	{

		window.top.SegAHeader.innerHTML = headerAE;

		Column0.innerText = 'Sales Date';
		Column1.innerText = 'Dept & Vendor';
		Column2.innerText = 'Dept & Products';

	}


	// 日本語
	else if ( lngCount == 1 )
	{

		window.top.SegAHeader.innerHTML = headerAJ;

		Column0.innerText = '売上計上日';
		Column1.innerText = '部門・顧客別';
		Column2.innerText = '部門・製品別';

	}

	return false;

}


//-->