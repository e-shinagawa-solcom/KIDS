<!--


///// WINDOW CLOSE BUTTON /////
var closebutton = '<img src="' + closebt + '" width="21" height="18" border="0">';


///// BOTTOM IMAGE /////
var bottomImage01 = '<img src="' + bottomimg01 + '" width="493" height="7" border="0">';
var bottomImage02 = '<img src="' + bottomimg02 + '" width="677" height="7" border="0">';

///// COUNTER IMAGE /////
var countImage = '<img src="' + counter + '" width="100" height="19" border="0">';

///// SEARCH HEADER 01 /////
var searchheader01J = '<img src="' + schheader01J + '" width="493" height="25" border="0" alt="検索">';
var searchheader01E = '<img src="' + schheader01E + '" width="493" height="25" border="0" alt="SEARCH">';

///// RESULT HEADER 01 /////
var resultheader01J = '<img src="' + rltheader01J + '" width="493" height="25" border="0" alt="検索結果">';
var resultheader01E = '<img src="' + rltheader01E + '" width="493" height="25" border="0" alt="RESULT">';

///// SEARCH HEADER 01b /////
var searchheader01bJ = '<img src="' + schheader01J + '" width="493" height="25" border="0" alt="検索">';
var searchheader01bE = '<img src="' + schheader01E + '" width="493" height="25" border="0" alt="SEARCH">';

///// RESULT HEADER 01b /////
var resultheader01bJ = '<img src="' + rltheader01J + '" width="493" height="25" border="0" alt="検索結果">';
var resultheader01bE = '<img src="' + rltheader01E + '" width="493" height="25" border="0" alt="RESULT">';

///// SEARCH HEADER 02 /////
var searchheader02J = '<img src="' + schheader02J + '" width="677" height="25" border="0" alt="検索">';
var searchheader02E = '<img src="' + schheader02E + '" width="677" height="25" border="0" alt="SEARCH">';

///// RESULT HEADER 02 /////
var resultheader02J = '<img src="' + rltheader02J + '" width="677" height="25" border="0" alt="検索結果">';
var resultheader02E = '<img src="' + rltheader02E + '" width="677" height="25" border="0" alt="RESULT">';


///// DRAG BAR /////
var Vdragbar = '<img src="' + vdrag + '" width="484" height="18" border="0">';
var Ddragbar = '<img src="' + ddrag + '" width="503" height="18" border="0">';
var Pdragbar = '<img src="' + pdrag + '" width="668" height="18" border="0">';
var Ldragbar = '<img src="' + ldrag + '" width="484" height="18" border="0">';
var VIdragbar = '<img src="' + vidrag + '" width="503" height="18" border="0">';
var Cradragbar = '<img src="' + cradrag + '" width="484" height="18" border="0">';
var Asmdragbar = '<img src="' + asmdrag + '" width="484" height="18" border="0">';
var Supdragbar = '<img src="' + supdrag + '" width="484" height="18" border="0">';
var Appdragbar = '<img src="' + appdrag + '" width="484" height="18" border="0">';
var Inputdragbar = '<img src="' + inputdrag + '" width="484" height="18" border="0">';
var Caldragbar = '<img src="' + caldrag + '" width="334" height="18" border="0">';



///// WINDOW BORDER COLOR /////
var winborder = '#3885c0';

///// BASE BACK COLOR /////
var basecolor = '#f1f1f1';

///// SEGMENTS BACK COLOR /////
var segbackcolor = '#ffffff';

///// SEGMENTS BORDER COLOR 1 /////
var segbordercolor1 = '#798787 #ffffff #798787 #798787';

///// SEGMENTS BORDER COLOR 2 /////
var segbordercolor2 = '#798787 #798787 #798787 #ffffff';

///// SEGMENTS BODYS BACK COLOR /////
var segbodycolor = '#ffffff';

///// SEGMENTS BODYS BORDER COLOR /////
var segbodybordercolor = '#cdcdcd';

///// FOCUS COLOR /////
var focuscolor = '#c7d0cb';



function initLayoutBase()
{
	if (typeof(InputA)!='undefined')
	{
		///// WINDOW BORDER COLOR /////
		InputA.style.borderColor = winborder;
	}

	if (typeof(InputB)!='undefined')
	{
		///// WINDOW BORDER COLOR /////
		InputB.style.borderColor = winborder;
	}	


	///// COUNTER /////
	if (typeof(Counter)!='undefined')
	{
		Counter.innerHTML = countImage;
	}

	///// BASE BACK COLOR /////
	BaseBack.style.background = basecolor;

	if (typeof(SegA01_1)!='undefined' &&
		typeof(SegA01_2)!='undefined' &&
		typeof(VarsA01_1)!='undefined' &&
		typeof(VarsA01_2)!='undefined' &&
		typeof(SegABodys)!='undefined' &&
		typeof(SegBBodys)!='undefined')
	{
		///// SEGMENTS BACK COLOR /////
		SegA01_1.style.background = segbackcolor;
		SegA01_2.style.background = segbackcolor;
		VarsA01_1.style.background = segbackcolor;
		VarsA01_2.style.background = segbackcolor;

		///// SEGMENTS BORDER COLOR /////
		SegA01_1.style.borderColor = segbordercolor1;
		SegA01_2.style.borderColor = segbordercolor1;
		VarsA01_1.style.borderColor = segbordercolor2;
		VarsA01_2.style.borderColor = segbordercolor2;

		///// SEGMENTS BODYS BACK COLOR /////
		SegABodys.style.background = segbodycolor;
		SegBBodys.style.background = segbodycolor;

		///// SEGMENTS BODYS BORDER COLOR /////
		SegABodys.style.borderColor = segbodybordercolor;
		SegBBodys.style.borderColor = segbodybordercolor;
	}


	///// WINDOW CLOSE BUTTON /////
	MswCloseBt.innerHTML = closebutton;

	///// OBJECT JUDGE /////
	if (typeof(SearchHeader01)!='undefined' &&
		typeof(ResultHeader01)!='undefined')
	{
		///// SEARCH HEADER 01 /////
		SearchHeader01.innerHTML = searchheader01J;

		///// RESULT HEADER 01 /////
		ResultHeader01.innerHTML = resultheader01J;
	}

	if (typeof(SearchHeader01b)!='undefined' &&
		typeof(ResultHeader01b)!='undefined')
	{
		///// SEARCH HEADER 01b /////
		SearchHeader01b.innerHTML = searchheader01bJ;

		///// RESULT HEADER 01b /////
		ResultHeader01b.innerHTML = resultheader01bJ;
	}

	if (typeof(SearchHeader02)!='undefined' &&
			typeof(ResultHeader02)!='undefined')
	{
		///// SEARCH HEADER 02 /////
		SearchHeader02.innerHTML = searchheader02J;

		///// RESULT HEADER 02 /////
		ResultHeader02.innerHTML = resultheader02J;
	}



	if ( typeof(SegABottom) != 'undefined' )
	{
		SegABottom.innerHTML = bottomImage01;
	}

	if ( typeof(SegBBottom) != 'undefined' )
	{
		SegBBottom.innerHTML = bottomImage01;
	}

	if ( typeof(SegCBottom) != 'undefined' )
	{
		SegCBottom.innerHTML = bottomImage01;
	}

	if ( typeof(SegDBottom) != 'undefined' )
	{
		SegDBottom.innerHTML = bottomImage01;
	}


	if ( typeof(SegABottom02) != 'undefined' )
	{
		SegABottom02.innerHTML = bottomImage02;
	}

	if ( typeof(SegBBottom02) != 'undefined' )
	{
		SegBBottom02.innerHTML = bottomImage02;
	}

	return false;
}


////////// DRAG BAR //////////
function initLayoutDragbar()
{
	if (typeof(Vendor01Drag)!='undefined')
	{
		///// VENDOR 01 /////
		Vendor01Drag.innerHTML = Vdragbar;
	}

	if (typeof(Vendor02Drag)!='undefined')
	{
		///// CREATION FACTORY /////
		Vendor02Drag.innerHTML = Cradragbar;
	}

	if (typeof(Vendor03Drag)!='undefined')
	{
		///// ASSEMBLY FACTORY/////
		Vendor03Drag.innerHTML = Asmdragbar;
	}

	if (typeof(DeptDrag)!='undefined')
	{
		///// DEPT & IN CHARGE NAME /////
		DeptDrag.innerHTML = Ddragbar;
	}

	if (typeof(ProductsDrag)!='undefined')
	{
		///// PRODUCTS /////
		ProductsDrag.innerHTML = Pdragbar;
	}

	if (typeof(LocationDrag)!='undefined')
	{
		///// LOCATION /////
		LocationDrag.innerHTML = Ldragbar;
	}

	if (typeof(ApplicantDrag)!='undefined')
	{
		///// APPLICANT /////
		ApplicantDrag.innerHTML = Appdragbar;
	}

	if (typeof(InputDrag)!='undefined')
	{
		///// APPLICANT /////
		InputDrag.innerHTML = Inputdragbar;
	}

	if (typeof(ViDrag)!='undefined')
	{
		///// VENDOR & IN CHARGE NAME /////
		ViDrag.innerHTML = VIdragbar;
	}

	if (typeof(SupDrag)!='undefined')
	{
		///// VENDOR & IN CHARGE NAME /////
		SupDrag.innerHTML = Supdragbar;
	}

	if (typeof(CalDrag)!='undefined')
	{
		///// VENDOR & IN CHARGE NAME /////
		CalDrag.innerHTML = Caldragbar;
	}
	return false;
}


//-->