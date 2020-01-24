<!--


function initLayoutSegs( obj1 , obj2 , obj3 , obj4 , obj5 , obj6 , obj7 , obj8 , obj9 )
{
	Backs.style.background = '#d6d0b1';


	var initYpos1 = 30;  //TOP座標・商品化企画書項目初期値
	var initYpos2 = 172; //TOP座標・商品管理項目初期値

	var moveYpos = 31;   //TOP座標・移動値


	var check1Xpos = 10;  //LEFT座標・チェックボックス[表示]固定値
	var check2Xpos = 58; //LEFT座標・チェックボックス[検索]固定値

	var segsXpos = 32;  //LEFT座標・カラム固定値
	var varsXpos = 218; //LEFT座標・フォーム要素固定値


	var segsWidth = 190; //カラム幅固定値

	var FontColors = '#666666'
	var BackColors1 = '#e8f0f1';
	var BackColors2 = '#f1f1f1';
	var BorderColors1 = '#798787 #e8f0f1 #798787 #798787';
	var BorderColors2 = '#798787 #798787 #798787 #e8f0f1';
	var BorderColors3 = '#798787 #798787 #798787 #798787';


	var lay1 = obj1.children; //商品管理項目カラム
	var lay2 = obj2.children; //商品管理項目フォーム要素
	var lay3 = obj3.children; //商品化企画書項目カラム
	var lay4 = obj4.children; //商品化企画書項目フォーム要素
	var lay5 = obj5.children; //商品化企画書・チェックボックス[表示]
	var lay6 = obj6.children; //商品化企画書・チェックボックス[検索]
	var lay7 = obj7.children; //商品管理・チェックボックス[表示]
	var lay8 = obj8.children; //商品管理・チェックボックス[検索]


	var lngtabindex = 1; //TAB INDEX 初期値


	///// 商品管理項目カラム展開 /////
	if ( obj1 != '' )
	{
		for (i = 0; i < lay1.length; i++)
		{
			lay1[i].style.top = initYpos1 + ( moveYpos * i );
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
			lay2[i].style.top = initYpos1 + ( moveYpos * i );
			lay2[i].style.left = varsXpos;
			lay2[i].style.background = BackColors1;
			lay2[i].style.borderColor = BorderColors2;
		}
	}

	///// 商品化企画書項目カラム展開 /////
	if ( obj3 != '' )
	{
		for (i = 0; i < lay3.length; i++)
		{
			lay3[i].style.top = initYpos1 + ( moveYpos * i );
			lay3[i].style.left = segsXpos;
			lay3[i].style.width = segsWidth;
			lay3[i].style.background = BackColors2;
			lay3[i].style.borderColor = BorderColors1;
			lay3[i].style.color = FontColors;
		}
	}

	///// 商品化企画書項目フォーム要素展開 /////
	if ( obj4 != '' )
	{
		for (i = 0; i < lay4.length; i++)
		{
			lay4[i].style.top = initYpos1 + ( moveYpos * i );
			lay4[i].style.left = varsXpos;
			lay4[i].style.background = BackColors2;
			lay4[i].style.borderColor = BorderColors2;
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
			lay5[i].style.borderColor = BorderColors3;
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

	///// 商品管理・チェックボックス[検索]展開 /////
	if ( obj7 != '' )
	{
		for (i = 0; i < lay7.length; i++)
		{
			lay7[i].style.top = initYpos2 + ( moveYpos * i );
			lay7[i].style.left = check1Xpos;
			lay7[i].style.background = BackColors1;
			lay7[i].style.borderColor = BorderColors3;
		}
	}

	///// 商品管理・チェックボックス[検索]展開 /////
	if ( obj8 != '' )
	{
		for (i = 0; i < lay8.length; i++)
		{
			lay8[i].style.top = initYpos2 + ( moveYpos * i );
			lay8[i].style.left = check2Xpos;
			lay8[i].style.background = BackColors1;
			lay8[i].style.borderColor = BorderColors2;
		}
	}


	///// TAB INDEX 展開 /////
	for (i = 0; i < window.PS.elements.length; i++)
	{
		window.PS.elements[i].tabindex = lngtabindex + 1;
	}

	return false;
}






//-->