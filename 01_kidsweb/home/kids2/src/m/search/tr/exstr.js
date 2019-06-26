///// HEADER IMAGE /////
var headerA = '<img src="' + mtemporaryrateJ + '" width="949" height="30" border="0" alt="想定レートマスタ検索">';

//////////////////////////////////////////////////////////////////
////////// オブジェクトのオンロード処理関数 //////////
function fncMasterSearchOnload()
{
    parent.schSchButton.style.visibility = 'visible';
    parent.schClrButton.style.visibility = 'visible';

    window.top.SegAHeader.innerHTML = headerA;

    if( typeof(parent.schSchButton) != 'undefined' )
    {
        parent.schSchButton.innerHTML = parent.schSchBtJ1;
    }

    if( typeof(parent.schClrButton) != 'undefined' )
    {
        parent.schClrButton.innerHTML = parent.schClrBtJ1;
    }
}
