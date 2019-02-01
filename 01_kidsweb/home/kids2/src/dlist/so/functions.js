<!--


///////////////////////////////////////////////////////////////////////////
function SetData01() {

	document.all.Lists01.style.background= '#bbbbbb' ;
	document.all.Rnum01.style.background= '#bbbbbb' ;
	document.all.Lists01.style.color= '#ffffff' ;
	document.all.Rnum01.style.color= '#ffffff' ;

	window.top.DSO.gset.value = DL.hgset.value;
	window.top.DSO.gcode.value = DL.hgcode.value;
	window.top.DSO.price.value = DL.hprice.value;
	window.top.DSO.unit.value = DL.hunit.value;
	window.top.DSO.qty.value = DL.hqty.value;
	window.top.DSO.amttax.value = DL.hamttax.value;
	window.top.DSO.ddate.value = DL.hddate.value;
	window.top.DSO.remark.value = DL.hremark.value;

	window.top.DSO.gcodeco.value = DL.hgcodeco.value;
	window.top.DSO.gname.value = DL.hgname.value;

	return false;

}


//-->