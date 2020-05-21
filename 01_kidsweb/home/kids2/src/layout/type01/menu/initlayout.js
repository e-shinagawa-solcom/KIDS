//-----------------------------------------------------------------------------
// 概要 : ローカル変数定義
// 解説 :「TabIndex」値の設定
//-----------------------------------------------------------------------------

//------------------------------------------
// 適用箇所 :「サブウィンドウボタン」
//------------------------------------------
var NumTabA1   = '' ; // vendor
var NumTabA1_2 = '' ; // creation
var NumTabA1_3 = '' ; // assembly
var NumTabB1   = '' ; // dept
var NumTabC1   = '' ; // products
var NumTabD1   = '' ; // location
var NumTabE1   = '' ; // applicant
var NumTabF1   = '' ; // wfinput
var NumTabG1   = '' ; // vi
var NumTabH1   = '' ; // supplier
var NumTabI1   = '' ; // input


//------------------------------------------
// 適用箇所 :「商品管理」タブ
//------------------------------------------
var PTabNumA = '' ; // A
var PTabNumB = '' ; // B


//------------------------------------------
// 適用箇所 :「受注・発注・売上・仕入」タブ
//------------------------------------------
var TabNumA = '' ; // ヘッダー
var TabNumB = '' ; // 明細


//------------------------------------------
// 適用箇所 :「登録ボタン」
//------------------------------------------
var RegistNum = '' ;


//------------------------------------------
// 適用箇所 :「行追加ボタン」
//------------------------------------------
var AddRowNum = '' ;


//------------------------------------------
// 適用箇所 :「カレンダーボタン」
//------------------------------------------
var NumDateTabA = '' ;
var NumDateTabB = '' ;
var NumDateTabC = '' ;


//------------------------------------------
// 適用箇所 :「製品数量ボタン」
//------------------------------------------
var NumPunitTab = '' ;







//---------------------------------------------------------
// タイトルイメージ生成
//---------------------------------------------------------
var maintitleJ = '<img src="' + titleJ + '" width="314" height="22" border="0" alt="メインメニュー">';



//---------------------------------------------------------
// メニューバックメージ生成
//---------------------------------------------------------
var MenuBackImg = '<img src="' + menuback + '" width="978" height="170" border="0">';




//-----------------------------------------------------------------------------
// 概要 : ボタンイメージ生成
// 解説 : イメージ生成・ロールオーバー処理・マウスダウン処理を設定
//-----------------------------------------------------------------------------

//---------------------------------------------------------
//適用 :「商品管理」
//---------------------------------------------------------
var pNaviJ1 = '<a href="#"><button type="button" class="btn btn-light">商品管理</button></a>';


//---------------------------------------------------------
//適用 :「見積原価管理」
//---------------------------------------------------------
var esNaviJ1 = '<a href="#"><button type="button" class="btn btn-light">見積原価管理</button></a>';

//---------------------------------------------------------
// 適用 :「受注管理」
//---------------------------------------------------------
var soNaviJ1 = '<a href="#"><button type="button" class="btn btn-light">受注管理</button></a>';

//---------------------------------------------------------
// 適用 :「発注管理」
//---------------------------------------------------------
var poNaviJ1 = '<a href="#"><button type="button" class="btn btn-light">発注管理</button></a>';

//---------------------------------------------------------
// 適用 :「売上管理」
//---------------------------------------------------------
var scNaviJ1 = '<a href="#"><button type="button" class="btn btn-light">売上管理</button></a>';

//---------------------------------------------------------
// 適用 :「仕入管理」
//---------------------------------------------------------
var pcNaviJ1 = '<a href="#"><button type="button" class="btn btn-light">仕入管理</button></a>';

//---------------------------------------------------------
//適用 :「請求管理」
//---------------------------------------------------------
var invNaviJ1 = '<a href="#"><button type="button" class="btn btn-light">請求管理</button></a>';

//---------------------------------------------------------
// 適用 :「帳票出力」
//---------------------------------------------------------
var listNaviJ1 = '<a href="#"><button type="button" class="btn btn-light">帳票出力</button></a>';

//---------------------------------------------------------
// 適用 :「データエクスポート」
//---------------------------------------------------------
var dataexNaviJ1 = '<a href="#"><button type="button" class="btn btn-light">データエクスポート</button></a>';

//---------------------------------------------------------
//適用 :「金型履歴管理」
//---------------------------------------------------------
var mmNaviJ1 = '<a href="#"><button type="button" class="btn btn-light">金型管理</button></a>';
//---------------------------------------------------------
//適用 :「金型帳票管理」
//---------------------------------------------------------
var mrNaviJ1 = '<a href="#"><button type="button" class="btn btn-light">金型帳票管理</button></a>';
//---------------------------------------------------------
//適用 :「LC管理」
//---------------------------------------------------------
var lcNaviJ1 = '<a href="#"><button type="button" class="btn btn-light">L/C管理</button></a>';

//---------------------------------------------------------
// 適用 :「ユーザー管理」
//---------------------------------------------------------
var ucNavi1 = '<a href="#"><button type="button" class="btn-small btn-light">USER</button></a>';

//---------------------------------------------------------
// 適用 :「マスタ管理」
//---------------------------------------------------------
var mNavi1 = '<a href="#"><button type="button" class="btn-small btn-light">MASTER</button></a>';

//---------------------------------------------------------
// 適用 :「締め日」
//---------------------------------------------------------
var dataclosedNavi1 = '<a href="#"><button type="button" class="btn-small btn-light">DATA</button></a>';

//---------------------------------------------------------
// 適用 :「システム管理」
//---------------------------------------------------------
var sysNavi1 = '<a href="#"><button type="button" class="btn-small btn-light">SYSTEM</button></a>';






function initLayoutSegs( obj1 , obj2 )
{

	var initXpos1 = 24;  //LEFT座標・初期値1
	var initXpos2 = 24;  //LEFT座標・初期値2 185

	var moveXpos = 160;   //LEFT座標・移動値



	var navi1Ypos = 318;  //TOP座標・固定値1
	var navi2Ypos = 388;  //TOP座標・固定値2



	var lay1 = obj1.children; //メニュー１項目
	var lay2 = obj2.children; //メニュー２項目


	///// メニュー１項目展開 /////
	if (typeof(obj1)!='undefined')
	{
		for (i = 0; i < lay1.length; i++)
		{
			lay1[i].style.left = initXpos1 + ( moveXpos * i );
			lay1[i].style.top = navi1Ypos;
			//lay1[i].style.background = BackColors1;
			//lay1[i].style.borderColor = BorderColors1;
			//lay1[i].style.color = FontColors;
		}
	}


	///// メニュー２項目展開 /////
	if (typeof(obj2)!='undefined')
	{
		for (i = 0; i < lay2.length; i++)
		{
			lay2[i].style.left = initXpos2 + ( moveXpos * i );
			lay2[i].style.top = navi2Ypos;
			//lay1[i].style.background = BackColors1;
			//lay1[i].style.borderColor = BorderColors1;
			//lay1[i].style.color = FontColors;
		}
	}

	return false;
}





function initLayoutNavi()
{

	if (typeof(MainTitle)!='undefined')
	{
		MainTitle.innerHTML = maintitleJ;
	}

	if (typeof(MenuBacksImg)!='undefined')
	{
		MenuBacksImg.innerHTML = MenuBackImg;
	}


	if (typeof(ESnavi)!='undefined')
	{
		ESnavi.innerHTML = esNaviJ1;
	}

	if (typeof(Pnavi)!='undefined')
	{
		Pnavi.innerHTML = pNaviJ1;
	}

	if (typeof(SOnavi)!='undefined')
	{
		SOnavi.innerHTML = soNaviJ1;
	}

	if (typeof(POnavi)!='undefined')
	{
		POnavi.innerHTML = poNaviJ1;
	}

	if (typeof(SCnavi)!='undefined')
	{
		SCnavi.innerHTML = scNaviJ1;
	}

	if (typeof(PCnavi)!='undefined')
	{
		PCnavi.innerHTML = pcNaviJ1;
	}

	if (typeof(WFnavi)!='undefined')
	{
		WFnavi.innerHTML = wfNaviJ1;
	}
	
	if (typeof(INVnavi)!='undefined')
	{
		INVnavi.innerHTML = invNaviJ1;
	}

	if (typeof(UCnavi)!='undefined')
	{
		UCnavi.innerHTML = ucNavi1;
	}

	if (typeof(LISTnavi)!='undefined')
	{
		LISTnavi.innerHTML = listNaviJ1;
	}

	if (typeof(DATAEXnavi)!='undefined')
	{
		DATAEXnavi.innerHTML = dataexNaviJ1;
	}

	if (typeof(UPLOADnavi)!='undefined')
	{
		UPLOADnavi.innerHTML = uploadNaviJ1;
	}

	if (typeof(Mnavi)!='undefined')
	{
		Mnavi.innerHTML = mNavi1;
	}

	if (typeof(SYSnavi)!='undefined')
	{
		SYSnavi.innerHTML = sysNavi1;
	}

	if (typeof(DATACLOSEDnavi)!='undefined')
	{
		DATACLOSEDnavi.innerHTML = dataclosedNavi1;
	}

	if (typeof(MMnavi)!='undefined')
	{
		MMnavi.innerHTML = mmNaviJ1;
	}

	if (typeof(MRnavi)!='undefined')
	{
		MRnavi.innerHTML = mrNaviJ1;
	}
	
	if (typeof(LCnavi)!='undefined')
	{
		LCnavi.innerHTML = lcNaviJ1;
	}

	return false;
}

