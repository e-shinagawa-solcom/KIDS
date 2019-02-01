<!--


//// [M] MSW BUTTON TAB INDEX NUM /////
var NumTabA1 = '';
var NumTabA1_2 = '';
var NumTabA1_3 = '';
var NumTabB1 = '';
var NumTabC1 = '';
var NumTabD1 = '';
var NumTabE1 = '';
var NumTabF1 = '';
var NumTabG1 = '';
var NumTabH1 = '';
var NumTabI1 = '';



//// [M] DATE BUTTON TAB INDEX NUM /////
var NumDateTabA = '';
var NumDateTabB = '';



// 登録ボタン
var RegistNum = '';

var AddRowNum = '';


var TabNumA = '';
var TabNumB = '';


///// VIEW SEARCH IMAGE /////
var vishImgJ = '<img src="' + vishJ + '" width="100" height="23" border="0">';
var vishImgE = '<img src="' + vishE + '" width="100" height="23" border="0">';

///// OFF ON BUTTON /////
var offBt = '<a href="#"><img onmouseover="OnBt(this);" onmouseout="OffBt(this);" src="' + off + '" width="19" height="19" border="0"></a>';
var onBt = '<a href="#"><img src="' + on + '" width="19" height="19" border="0"></a>';

function initLayoutMSearch()
{

	ViewSearch1.innerHTML = vishImgJ;

	CheckAll1.innerHTML = offBt;
	CheckAll2.innerHTML = offBt;

}


function initLayoutSegs( obj1 , obj2 , obj5 , obj6 )
{
	Backs.style.background = '#d6d0b1';


	var initYpos1 = 50;  //TOP座標・商品化企画書項目初期値
	var initYpos2 = 20; //TOP座標・商品管理項目初期値

	var moveYpos = 31;   //TOP座標・移動値


	var check1Xpos = 10;  //LEFT座標・チェックボックス[表示]固定値
	var check2Xpos = 58; //LEFT座標・チェックボックス[検索]固定値

	var segsXpos = 10;  //LEFT座標・カラム固定値
	var varsXpos = 166; //LEFT座標・フォーム要素固定値


	var segsWidth = 157; //カラム幅固定値

	var FontColors = '#666666'
	var BackColors1 = '#e8f0f1';
	var BackColors2 = '#f1f1f1';
	var BorderColors1 = '#798787 #e8f0f1 #798787 #798787';
	var BorderColors2 = '#798787 #798787 #798787 #e8f0f1';


	var lay1 = obj1.children; //商品管理項目カラム
	var lay2 = obj2.children; //商品管理項目フォーム要素
	//var lay3 = obj3.children; //商品化企画書項目カラム
	//var lay4 = obj4.children; //商品化企画書項目フォーム要素
	var lay5 = obj5.children; //商品化企画書・チェックボックス[表示]
	var lay6 = obj6.children; //商品化企画書・チェックボックス[検索]
	//var lay7 = obj7.children; //商品管理・チェックボックス[表示]
	//var lay8 = obj8.children; //商品管理・チェックボックス[検索]


	var lngtabindex = 1; //TAB INDEX 初期値


	///// 商品管理項目カラム展開 /////
	if ( obj1 != '' )
	{
		for (i = 0; i < lay1.length; i++)
		{
			lay1[i].style.top = initYpos2 + ( moveYpos * i );
			lay1[i].style.left = segsXpos;
			lay1[i].style.width = segsWidth;
			lay1[i].style.background = BackColors1;
			lay1[i].style.borderColor = BorderColors1;
			lay1[i].style.color = FontColors;
		}
	}

	///// 商品管理項目フォーム要素展開 /////
	if ( obj2 != '' )
	{
		for (i = 0; i < lay2.length; i++)
		{
			lay2[i].style.top = initYpos2 + ( moveYpos * i );
			lay2[i].style.left = varsXpos;
			lay2[i].style.background = BackColors1;
			lay2[i].style.borderColor = BorderColors2;
		}
	}

	///// 商品化企画書・チェックボックス[表示]展開 /////
	if ( obj5 != '' )
	{
		for (i = 0; i < lay5.length; i++)
		{
			lay5[i].style.top = initYpos1 + ( moveYpos * i );
			lay5[i].style.left = check1Xpos;
			lay5[i].style.background = BackColors2;
			lay5[i].style.borderColor = BorderColors1;
		}
	}

	///// 商品化企画書・チェックボックス[検索]展開 /////
	if ( obj6 != '' )
	{
		for (i = 0; i < lay6.length; i++)
		{
			lay6[i].style.top = initYpos1 + ( moveYpos * i );
			lay6[i].style.left = check2Xpos;
			lay6[i].style.background = BackColors2;
			lay6[i].style.borderColor = BorderColors2;
		}
	}


	///// TAB INDEX 展開 /////
	for (i = 0; i < window.form1.elements.length; i++)
	{
		window.form1.elements[i].tabindex = lngtabindex + 1;
	}

	return false;
}


//-->