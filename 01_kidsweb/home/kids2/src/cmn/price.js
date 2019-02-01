<!--


function ListMatch( objSelect , strMatch )
{
	for( i = 0; i < objSelect.options.length; i++ )
	{
		if( objSelect.options[i].text == strMatch )
		{
			return i;
		}
	}
	return -1;
}



function AddList1( objText1 , insertObj )
{
	var elm1 = document.createElement( 'option' );
	elm1.text = objText1.value;
	elm1.value = objText1.value;

	if( !document.all.PriceSelect.options.length <= 0 )
	{
		lngListSelect = ListMatch( document.all.PriceSelect , objText1.value );

		if( lngListSelect >= 0 )
		{
			document.all.PriceSelect.options[lngListSelect].selected = true;
			//alert( 'MATCH' );
			return true;
		}
	}

	if ( elm1.text != '' )
	{
		if ( elm1.text.match(/^\b/ig) != null )
		{
			document.all.PriceSelect.add( elm1 );
			elm1.selected = true ;
			insertObj.value = elm1.value;
		}
	}
	else
	{
		//alert( 'NO VALUE' );
	}
	return false;
}



function AddList2( objText2 )
{
	var elm2 = document.createElement( 'option' );
	elm2.text = objText2.value;
	elm2.value = objText2.value;
	
	if( !document.all.PriceSelect.options.length <= 0 )
	{
		lngListSelect = ListMatch( document.all.PriceSelect , objText2.value );

		if( lngListSelect >= 0 )
		{
			document.all.PriceSelect.options[lngListSelect].selected = true;
			//alert( 'MATCH' );
			return true;
		}
	}

	if ( elm2.text != '' )
	{
		if ( elm2.text.match(/^\b/ig) != null )
		{
			document.all.PriceSelect.add( elm2 );
			elm2.selected = true ;
		}
	}
	else
	{
		//alert( 'NO VALUE' );
	}
	return false;
}




function DelList()
{
	var num = document.all.PriceSelect.selectedIndex;

	if ( num >= 0 )
	{
		document.all.PriceSelect.remove( num );
		document.all.PriceText1.value = '';
	}
	else
	{
		//alert( 'NOT SELECTED' );
	}
	return false;
}



function SetList( obj )
{
	document.all.PriceText1.value = obj;
}







function ListUp( oname )
{
	var nums = oname.selectedIndex;

	if ( nums > 0 )
	{
		var elms = document.createElement( 'option' );
		elms.text = oname.value;
		elms.value = oname.value;
		oname.remove( nums );
		oname.add( elms , nums -1 );
		elms.selected = true;
	}
	return false;
}


function ListDown( oname )
{
	var nums = oname.selectedIndex;

	if ( nums >= 0 )
	{
		var elms = document.createElement( 'option' );
		elms.text = oname.value;
		elms.value = oname.value;
		oname.remove( nums );
		oname.add( elms , nums +1 );
		elms.selected = true;
	}
	return false;
}







////////// OPEN-CLOSE //////////
var countPrice=0;

function ShowPrice(obj)
{
	if (countPrice==0)
	{
		PriceWin.style.visibility = 'visible';
		obj.innerHTML = pricebt3;
		countPrice++;
		document.all.PriceText2.focus();
	}
	else if (countPrice==1)
	{
		PriceWin.style.visibility = 'hidden';
		obj.innerHTML = pricebt1;
		countPrice=0;
	}
	return false;
}


//-->