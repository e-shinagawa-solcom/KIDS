<!--


///// SEARCH BUTTON /////
var searchbuttonAJ1 = '<a href="#"><img onfocus="SearchJOn(this);" onblur="SearchJOff(this);" onmouseover="SearchJOn(this);" onmouseout="SearchJOff(this);" src="' + schbtJ1 + '" width="64" height="21" border="0" alt="検索" tabindex="3"></a>';
var searchbuttonAE1 = '<a href="#"><img onfocus="SearchEOn(this);" onblur="SearchEOff(this);" onmouseover="SearchEOn(this);" onmouseout="SearchEOff(this);" src="' + schbtE1 + '" width="64" height="21" border="0" alt="SEARCH" tabindex="3"></a>';

var searchbuttonBJ1 = '<a href="#"><img onfocus="SearchJOn(this);" onblur="SearchJOff(this);" onmouseover="SearchJOn(this);" onmouseout="SearchJOff(this);" src="' + schbtJ1 + '" width="64" height="21" border="0" alt="検索" tabindex="8"></a>';
var searchbuttonBE1 = '<a href="#"><img onfocus="SearchEOn(this);" onblur="SearchEOff(this);" onmouseover="SearchEOn(this);" onmouseout="SearchEOff(this);" src="' + schbtE1 + '" width="64" height="21" border="0" alt="SEARCH" tabindex="8"></a>';


if( typeof(applyTabNum) != 'undefined' )
{
	///// APPLY BUTTON /////
	var applybuttonJ1 = '<a href="#"><img name="AppBt" onfocus="ApplyJOn(this);" onblur="ApplyJOff(this);" onmouseover="ApplyJOn(this);" onmouseout="ApplyJOff(this);" src="' + aplybtJ1 + '" width="64" height="21" border="0" alt="適用" tabindex="' + applyTabNum + '"></a>';
	var applybuttonE1 = '<a href="#"><img name="AppBt" onfocus="ApplyEOn(this);" onblur="ApplyEOff(this);" onmouseover="ApplyEOn(this);" onmouseout="ApplyEOff(this);" src="' + aplybtE1 + '" width="64" height="21" border="0" alt="APPLY" tabindex="' + applyTabNum + '"></a>';
}



///// CLEAR BUTTON /////
var clearbuttonJ1 = '<a href="#"><img onmouseover="ClearJOn(this);" onmouseout="ClearJOff(this);" src="' + clrbtJ1 + '" width="64" height="21" border="0" alt="クリア"></a>';
var clearbuttonE1 = '<a href="#"><img onmouseover="ClearEOn(this);" onmouseout="ClearEOff(this);" src="' + clrbtE1 + '" width="64" height="21" border="0" alt="CLEAR"></a>';


function initLayoutQuery()
{

	///// SEARCH BUTTON OBJECT JUDGE /////
	if( typeof(SearchButton01) != 'undefined' )
	{
		SearchButton01.innerHTML = searchbuttonAJ1;
	}

	if( typeof(SearchButton02) != 'undefined' )
	{
		SearchButton02.innerHTML = searchbuttonBJ1;
	}


	///// APPLY BUTTON OBJECT JUDGE /////
	if( typeof(ApplyButton) != 'undefined' )
	{
		ApplyButton.innerHTML = applybuttonJ1;
	}

	///// APPLY BUTTON OBJECT JUDGE /////
	if( typeof(ClearButton) != 'undefined' )
	{
		ClearButton.innerHTML = clearbuttonJ1;
	}

	return false;
}


//-->