<!--


///// PRICE HEADER IMAGE /////
var pheader = '<img src="' + pricehead + '" width="261" height="12" border="0">';

///// PRICE BT IMAGE /////
var pricebt1 = '<a href="#"><img onmouseover="PriceOn(this);" onmouseout="PriceOff(this);" src="' + price1 + '" width="19" height="19" border="0" alt="PRICE" tabindex=""></a>';
var pricebt3 = '<a href="#"><img src="' + price3 + '" width="19" height="19" border="0" alt="PRICE" tabindex=""></a>';

///// PRICE ADD & DEL BT IMAGE /////
var addbt1 = '<a href="#"><img onmouseover="AddListOn(this);" onmouseout="AddListOff(this);" src="' + add1 + '" width="58" height="16" border="0" alt="ADD" tabindex=""></a>';
var addbt3 = '<a href="#"><img src="' + add3 + '" width="58" height="16" border="0" alt="ADD" tabindex=""></a>';

var delbt1 = '<a href="#"><img onmouseover="DelListOn(this);" onmouseout="DelListOff(this);" src="' + del1 + '" width="58" height="16" border="0" alt="DEL" tabindex=""></a>';
var delbt3 = '<a href="#"><img src="' + del3 + '" width="58" height="16" border="0" alt="DEL" tabindex=""></a>';

///// PRICE BT IMAGE /////
var upbt = '<a href="#"><img src="' + upimg + '" width="11" height="17" border="0" alt="LIST UP" tabindex=""></a>';
var downbt = '<a href="#"><img src="' + downimg + '" width="11" height="17" border="0" alt="LIST DOWN" tabindex=""></a>';


function initLayoutPrice()
{
	//// OBJECT JUDGE /////
	if (typeof(PriceBt)!='undefined')
	{
		PriceBt.innerHTML = pricebt1;
	}

	if (typeof(PriceHeader)!='undefined')
	{
		PriceHeader.innerHTML = pheader;
	}

	if (typeof(AddPriceBt)!='undefined')
	{
		AddPriceBt.innerHTML = addbt1;
	}

	if (typeof(DelPriceBt)!='undefined')
	{
		DelPriceBt.innerHTML = delbt1;
	}

	if (typeof(UpBt)!='undefined')
	{
		UpBt.innerHTML = upbt;
	}

	if (typeof(DownBt)!='undefined')
	{
		DownBt.innerHTML = downbt;
	}
	return false;
}


//-->