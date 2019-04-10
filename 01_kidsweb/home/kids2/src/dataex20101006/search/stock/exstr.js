<!--


//------------------------------------------------------------
// 解説 : ヘッダーイメージの定義
//------------------------------------------------------------
var headerAJ = '<img src="' + headtitleAJ + '" width="949" height="30" border="0" alt="仕入一覧表">';
var headerAE = '<img src="' + headtitleAE + '" width="949" height="30" border="0" alt="STOCK TABLE FILE">';


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


	// 英語
	if ( lngCount == 0 )
	{

		window.top.SegAHeader.innerHTML = headerAE;

		Column0.innerText = 'Stock Date';
		Column1.innerText = 'Goods set & Supplier';
		Column2.innerText = 'Goods set & Dept & Products';

	}


	// 日本語
	else if ( lngCount == 1 )
	{

		window.top.SegAHeader.innerHTML = headerAJ;

		Column0.innerText = '仕入計上日';
		Column1.innerText = '仕入科目・仕入先別';
		Column2.innerText = '仕入科目・部門・製品別';

	}

	return false;

}


//-->