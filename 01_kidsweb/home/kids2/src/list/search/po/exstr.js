<!--








//------------------------------------------------------------
// 解説 : ヘッダーイメージの定義
//------------------------------------------------------------
var headtitleAJ = '/img/type01/list/header_po_search_ja.gif';
var poheaderAJ = '<img src="' + headtitleAJ + '" width="949" height="30" border="0" alt="発注書検索">';






//------------------------------------------------------------
// 解説 : ヘッダー切り替え関数
//------------------------------------------------------------
function ChgEtoJ( lngCount )
{

	window.top.SegAHeader.innerHTML = poheaderAJ;

	return false;

}


//-->