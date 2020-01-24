<!--


//------------------------------------------------------------
// 解説 : ヘッダーイメージの定義
//------------------------------------------------------------
var headerAJ = '<img src="' + headtitleAJ + '" width="949" height="30" border="0" alt="社内統計データ  02（概算売上）　検索">';
var headerAE = '<img src="' + headtitleAE + '" width="949" height="30" border="0" alt="Statistical 02(Rough estimate sales)　SEARCH">';


//------------------------------------------------------------
// 解説 : プレビューボタンイメージの定義
//------------------------------------------------------------
var blownpreviewBt = '<a href="#"><img onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onmouseover="fncBlownPreviewButton( \'on\' , this );" onmouseout="fncBlownPreviewButton( \'off\' , this );fncAlphaOff( this );" src="' + blownpreviewbt1 + '" width="72" height="20" border="0" alt="PREVIEW"></a>';


//------------------------------------------------------------
// 解説 : エクスポートボタンイメージの定義
//------------------------------------------------------------
var blownexportBt = '<a href="#"><img onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onmouseover="fncBlownExportButton( \'on\' , this );" onmouseout="fncBlownExportButton( \'off\' , this );fncAlphaOff( this );" src="' + blownexportbt1 + '" width="72" height="20" border="0" alt="EXPORT"></a>';





//------------------------------------------------------------
// 解説 : 日本語・英語切替関数
//------------------------------------------------------------
function ChgEtoJ( lngCount )
{

	// プレビューボタンの書出し
	PreviewBt.innerHTML = blownpreviewBt;
	// エクスポートボタンの書出し
	ExportBt.innerHTML  = blownexportBt;

	return false;

}


//-->