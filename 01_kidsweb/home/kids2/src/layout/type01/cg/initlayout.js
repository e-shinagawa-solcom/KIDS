
///// LIST UP-DOWN BUTTON /////
var listUp = '<a href="#"><img onmouseover="UpOn( this );" onmouseout="UpOff( this );" src="' + upbt1 + '" width="15" height="15" border="0" alt="LIST UP"></a>';
var listDown = '<a href="#"><img onmouseover="DownOn( this );" onmouseout="DownOff( this );" src="' + downbt1 + '" width="15" height="15" border="0" alt="LIST DOWN"></a>';

///// LIST ADD-DEL BUTTON /////
var listAdd = '<a href="#"><img onmouseover="AddOn( this );" onmouseout="AddOff( this );" src="' + addbt1 + '" width="19" height="19" border="0" alt="LIST ADD"></a>';
var listDel = '<a href="#"><img onmouseover="DelOn( this );" onmouseout="DelOff( this );" src="' + delbt1 + '" width="19" height="19" border="0" alt="LIST DEL"></a>';

///// BUTTON BACK /////
var backAddDel = '<img src="' + btback1 + '" width="24" height="144" border="0">';
var backUpDown = '<img src="' + btback2 + '" width="18" height="146" border="0">';

///// CG HEADER /////
var cgHeaderimg = '<img src="' + cgheader + '" width="798" height="12" border="0">';

var cgLineimg = '<img src="' + cgline + '" width="798" height="1" border="0">';

var cgslctCompanyimg = '<img src="' + cgslctcompany + '" width="122" height="13" border="0">';
var cgslctGrouopimg = '<img src="' + cgslctgroup + '" width="122" height="13" border="0">';
var cgGroupimg = '<img src="' + cggroup + '" width="122" height="13" border="0">';



function initLayoutCG()
{

	ListUpBt.innerHTML = listUp;
	ListDownBt.innerHTML = listDown;

	ListAddBt.innerHTML = listAdd;
	ListDelBt.innerHTML = listDel;

	AddDelBack.innerHTML = backAddDel;
	UpDownBack.innerHTML = backUpDown;

	CompanyGroupHeader.innerHTML = cgHeaderimg;
	CompanyGroupFooter.innerHTML = cgHeaderimg;

	CompanyGroupLine.innerHTML = cgLineimg;
	CompanyGroupStrSelectCompany.innerHTML = cgslctCompanyimg;
	CompanyGroupStrSelectGroup.innerHTML = cgslctGrouopimg;
	CompanyGroupStrGroup.innerHTML = cgGroupimg;

	return false;
}