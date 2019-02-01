<!--


////////// [MSW]ウィンドウオープン時に代入先オブジェクト取得 //////////

// objIfrm	: サブウィンドウIframe名
// objCodeA	: [親]コード用テキストフィールドNAME
// objNameA	: [親]名称用テキストフィールドNAME
// objCodeB	: [子]コード用テキストフィールドNAME
// objNameB	: [子]名称用テキストフィールドNAME

function fncGetObjectName( objIfrm , objCodeA , objNameA , objCodeB , objNameB )
{

	// [親]テキストフィールドオブジェクトを取得
	objIfrm.g_aryElementName[0] = objCodeA;
	objIfrm.g_aryElementName[1] = objNameA;


	////////// 以下、[子]処理 //////////
	if( objCodeB )
	{
		// [子]テキストフィールドオブジェクトを取得
		objIfrm.g_aryElementName[2] = objCodeB;
		objIfrm.g_aryElementName[3] = objNameB;
	}

	return true;
}




////////// [MSW]タイトルバーダブルクリック時の処理 //////////

function fncTitlebar( objID , objID2 )
{


	if( objID.style.height != "18px" || objID.style.height == "")
	{
		objID.style.height = "18px";
		//小さくしただけだと、tabキーを押したときに表示がおかしくなるため、
		//親ウィンドウにフォーカスを移す。
		window.parent.focus();
	}
	else if( objID.style.height == "18px" )
	{
		//MSWがDATEのとき
		if( objID.name == "MDatewin"  || 
			objID.name == "MDateBwin" || 
			objID.name == "MDateCwin" )
		{
			objID.style.height = "245px";
		}
		//それ以外のMSWのとき
		else
		{
			objID.style.height = "381px";
		}

		//オブジェクトの位置をチェック
		fncMswPositionCheck( objID , objID2 );
	}
	return false;
}




///// [DEPT & IN CHARGE NAME] ドラッグ時のSelectbox 表示-非表示処理 /////
function ShowHideValueDept( obj )
{
	if( obj.DeptValueFlg == 0 )
	{
		obj.VarsB01.style.visibility = 'visible';
	}

	if( obj.DeptValueFlg == 1 )
	{
		obj.VarsD01.style.visibility = 'visible';
	}
	return false;
}


///// [VENDOR & IN CHARGE NAME] ドラッグ時のSelectbox 表示-非表示処理 /////
function ShowHideValueVi( obj )
{
	if( obj.ViValueFlg == 0 )
	{
		obj.VarsB01.style.visibility = 'visible';
	}

	if( obj.ViValueFlg == 1 )
	{
		obj.VarsD01.style.visibility = 'visible';
	}
	return false;
}

///// [OTHER MSW] ドラッグ時のSelectbox 表示-非表示処理 /////
function ShowValue( obj )
{
	obj.VarsB01.style.visibility = 'visible';

	return false;
}



//@*****************************************************************************
// 概要   : サブウィンドウが枠の外にはみ出でたら、強制的に戻るようにする
// 引数   
//        : selectedObject, 対象オブジェクト名の高さと幅を得るために使用
//        : selectedLayer , 対象オブジェクト名のウィンドウ位置を得るために使用
// 作成者 : 手塚 貴文
//******************************************************************************
function fncMswPositionCheck( selectedObject , selectedLayer )
{
	//onMouseUpの設定をクリアする
	document.onmouseup = clearAll;

	////Y軸の処理
	//表示しているウィンドウの高さ
	MAXoffsetY = parseInt(document.body.clientHeight);
	//オブジェクトとウィンドウ枠の高さ
	offset_Y = selectedLayer.offsetTop;
	//オブジェクトの高さ
	layerHeight = selectedObject.offsetHeight;

	//下に行き過ぎたら	
	if( (MAXoffsetY - offset_Y) <= layerHeight )
	{
		//修正位置
		var movetoY = MAXoffsetY - layerHeight;

		//戻り過ぎないようにするための処理
		if(movetoY >= 0)
		{
			selectedLayer.style.pixelTop  = movetoY;
		}
		else
		{
			selectedLayer.style.pixelTop  = 0;
		}
	}
	//上に行き過ぎたら
	else if( (offset_Y) <= 0 )
	{
		selectedLayer.style.pixelTop  = 0;
	}

	////X軸の処理
	//表示しているウィンドウの幅
	MAXoffsetX = parseInt(document.body.clientWidth);
	//オブジェクトとウィンドウ枠の幅
	offset_X = selectedLayer.offsetLeft;
	//オブジェクトの幅
	layerWidth  = selectedObject.offsetWidth;

	//右に行き過ぎたら	
	if( (MAXoffsetX - offset_X) <= layerWidth )
	{
		//修正位置
		var movetoX = MAXoffsetX - layerWidth;

		//戻り過ぎないようにするための処理
		if(movetoX >= 0)
		{
			selectedLayer.style.pixelLeft = movetoX;
		}
		else
		{
			selectedLayer.style.pixelLeft =  0;
		}
	}
	//左に行き過ぎたら
	else if( (offset_X) <= 0 )
	{
		selectedLayer.style.pixelLeft  = 0;
	}

//デバック処理用
/*
alert(   "MAXoffsetY = " + MAXoffsetY + "\n" +
		"offset_Y = " + offset_Y + "\n" +
		"layerHeight = " + layerHeight + "\n" +
		"MAXoffsetX = " + MAXoffsetX + "\n" +
		"offset_X = " + offset_X + "\n" +
		"layerWidth = " + layerWidth + "\n");
*/
}










//----------------------------------------------------------------------
// 解説 : サブウィンドウクローズ時のフォーカス先取得関数
//----------------------------------------------------------------------
function fncFocusType( objIFrm , strType )
{
	objIFrm.g_FocusObject[0] = strType;

	return false;
}



//----------------------------------------------------------------------
// 解説 : サブウィンドウクローズ時のフォーカス適用関数
//----------------------------------------------------------------------
function fncFocusObject( strMode )
{

	switch( strMode )
	{

		case 'vendorTop':
			if( document.all.InputA.style.visibility != 'hidden' )
			{
				document.all.vendorBt.focus();
			}
			break;

		case 'vendorIfrm':
			window.Pwin.fncIfrmFocusObject( 'vendor' );
			break;



		case 'creationTop':
			if( document.all.InputB.style.visibility != 'hidden' )
			{
				document.all.creationBt.focus();
			}

			break;

		case 'creationIfrm':
			window.Pwin.fncIfrmFocusObject( 'creation' );
			break;



		case 'assemblyTop':
			if( document.all.InputB.style.visibility != 'hidden' )
			{
				document.all.assemblyBt.focus();
			}
			break;

		case 'assemblyIfrm':
			window.Pwin.fncIfrmFocusObject( 'assembly' );
			break;



		case 'deptTop':
			if( document.all.InputA.style.visibility != 'hidden' )
			{
				document.all.deptBt.focus();
			}

			break;

		case 'deptIfrm':
			window.Pwin.fncIfrmFocusObject( 'dept' );
			break;



		case 'productsTop':
			if( document.all.InputB.style.visibility != 'hidden' )
			{
				document.all.productsBt.focus();
			}
			break;

		case 'productsIfrm':
			window.Pwin.fncIfrmFocusObject( 'products' );
			break;



		case 'locationTop':
			// 発注管理の場合
			if( typeof( window.HSO ) == "object" )
			{
				if( document.all.InputA.style.visibility != 'hidden' )
				{
					document.all.locationBt.focus();
				}
			}
			// 商品管理の場合
			else
			{
				if( document.all.InputB.style.visibility != 'hidden' )
				{
					document.all.locationBt.focus();
				}
			}
			break;



		case 'locationIfrm':
			window.Pwin.fncIfrmFocusObject( 'location' );
			break;



		case 'applicantTop':
			document.all.applicantBt.focus();
			break;

		case 'applicantIfrm':
			window.Pwin.fncIfrmFocusObject( 'applicant' );
			break;



		case 'wfinputTop':
			document.all.wfinputBt.focus();
			break;

		case 'wfinputIfrm':
			window.Pwin.fncIfrmFocusObject( 'wfinput' );
			break;



		case 'viTop':
			if( document.all.InputA.style.visibility != 'hidden' )
			{
				document.all.viBt.focus();
			}
			break;

		case 'viIfrm':
			window.Pwin.fncIfrmFocusObject( 'vi' );
			break;



		case 'supplierTop':
			if( document.all.InputA.style.visibility != 'hidden' )
			{
				document.all.supplierBt.focus();
			}
			break;

		case 'supplierIfrm':
			window.Pwin.fncIfrmFocusObject( 'supplier' );
			break;



		case 'inputTop':
			document.all.inputBt.focus();
			break;

		case 'inputIfrm':
			window.Pwin.fncIfrmFocusObject( 'input' );
			break;


		default:
			break;
	}


	return false;

}










////////////////////////////// [VENDOR] //////////////////////////////

// レイヤー表示・非表示フラグ
var flagA = 0;

// ENTERキー押下でクローズ時の制御フラグ
var vendorEnterKeyCntDisplay = 0;

////////// レイヤー表示・非表示処理 /////////////////////////////////////////////////

// lngMSW	:	サブウインドウ上からのキーイベント判定値 : '' : デフォルト
//														 : 1  : キーイベント
//
// objID	: サブウインドウIFRAME名
// objA		: [InputA]サブウインドウオープン時に最初にフォーカスするオブジェクト名

/////////////////////////////////////////////////////////////////////////////////////
function DisplayerM01( lngMSW , objID , objA )
{

	if( lngMSW != '' )
	{
		// 制御フラグへキーイベント判定値の代入
		vendorEnterKeyCntDisplay = lngMSW;
	}


	// 通常のクリック操作の場合
	if( vendorEnterKeyCntDisplay == 0 )
	{

		if( lngMSW == '' && flagA == 0 )
		{
			document.onmouseup = clearAll;
			document.all.MiFrame01.style.visibility = "visible";

			objA.focus();

			flagA = 1;
		}
		else if( flagA == 1 )
		{
			document.onmouseup = '';
			document.all.MiFrame01.style.visibility = "hidden";

			if( typeof( objID ) != "undefined")
			{
				objID.style.height = 381;
			}

			flagA = 0;
		}

	}

	// キーイベントからの操作の場合
	else if( vendorEnterKeyCntDisplay == 1 )
	{
		document.onmouseup = '';
		document.all.MiFrame01.style.visibility = "hidden";

		if( typeof( objID ) != "undefined")
		{
			objID.style.height = 381;
		}

		// リダイレクト用に値を代入
		vendorEnterKeyCntDisplay = 2;

		flagA = 0;
	}

	// キーイベントのリダイレクト処理
	else if( vendorEnterKeyCntDisplay == 2 )
	{
		// 制御フラグの初期化
		vendorEnterKeyCntDisplay = 0;
	}


	return false;
}


/////////////////////////////////////////////////////////////////////////////////////

// lngMSW	:	サブウインドウ上からのキーイベント判定値 : 0 : デフォルト
//														 : 1 : キーイベント
// obj		:	ボタン配置ウインドウ名(IFRAME内にボタンを配置した場合に明示的な指定をする)

////////// [MSWBt]のボタン切替処理 //////////////////////////////////////////////////

// イメージ変換フラグ
var countM1=0;

// ENTERキー押下でイメージ変換時の制御フラグ
var vendorEnterKeyCntExchange = 0;

/////////////////////////////////////////////////////////////////////////////////////
function ExchangeM01( lngMSW , obj )
{

	// 制御フラグへキーイベント判定値の代入
	vendorEnterKeyCntExchange = lngMSW;


	// 通常のクリック操作の場合
	if( vendorEnterKeyCntExchange == 0 )
	{

		if( countM1 == 0 )
		{
			MSWBt01.innerHTML = mswbtA3;

			if( typeof(obj) != 'undefined' )
			{
				obj.MSWBt01.innerHTML = mswbtA3;
			}

			countM1 = 1;
		}
		else if( countM1 == 1 )
		{
			MSWBt01.innerHTML = mswbtA1;

			if( typeof(obj) != 'undefined' )
			{
				obj.MSWBt01.innerHTML = mswbtA1;
			}

			// ボタンへフォーカスを戻す
			//document.all.vendorBt.focus();

			countM1 = 0;

		}

	}

	// キーイベントからの操作の場合
	else if( vendorEnterKeyCntExchange == 1 )
	{
		MSWBt01.innerHTML = mswbtA1;

		if( typeof(obj) != 'undefined' )
		{
			obj.MSWBt01.innerHTML = mswbtA1;
		}

		// ボタンへフォーカスを戻す
		//document.all.vendorBt.focus();
	}


	return false;
}






////////////////////////////// [CREATION FACTORY] //////////////////////////////

// レイヤー表示・非表示フラグ
var flagA_2 = 0;

// ENTERキー押下でクローズ時の制御フラグ
var creationEnterKeyCntDisplay = 0;

////////// レイヤー表示・非表示処理 /////////////////////////////////////////////////

// lngMSW	:	サブウインドウ上からのキーイベント判定値 : '' : デフォルト
//														 : 1  : キーイベント
//
// objID	: サブウインドウIFRAME名
// objA		: [InputA]サブウインドウオープン時に最初にフォーカスするオブジェクト名
// objB		: [InputB]サブウインドウオープン時に最初にフォーカスするオブジェクト名

/////////////////////////////////////////////////////////////////////////////////////

function DisplayerM01_2( lngMSW , objID , objA )
{

	if( lngMSW != '' )
	{
		// 制御フラグへキーイベント判定値の代入
		creationEnterKeyCntDisplay = lngMSW;
	}


	// 通常のクリック操作の場合
	if( creationEnterKeyCntDisplay == 0 )
	{
		if( lngMSW == '' && flagA_2 == 0 )
		{
			document.onmouseup = clearAll;
			document.all.MiFrame01_2.style.visibility = "visible";

			objA.focus();

			flagA_2 = 1;
		}

		else if( flagA_2 == 1 )
		{
			document.onmouseup = '';
			document.all.MiFrame01_2.style.visibility = "hidden";

			if( typeof( objID ) != "undefined")
			{
				objID.style.height = 381;
			}

			flagA_2 = 0;
		}
	}

	// キーイベントからの操作の場合
	else if( creationEnterKeyCntDisplay == 1 )
	{
		document.onmouseup = '';
		document.all.MiFrame01_2.style.visibility = "hidden";

		if( typeof( objID ) != "undefined")
		{
			objID.style.height = 381;
		}

		// リダイレクト用に値を代入
		creationEnterKeyCntDisplay = 2;
		flagA_2 = 0;

	}

	// キーイベントのリダイレクト処理
	else if( creationEnterKeyCntDisplay == 2 )
	{
		// 制御フラグの初期化
		creationEnterKeyCntDisplay = 0;
	}

	return false;
}






/////////////////////////////////////////////////////////////////////////////////////

// lngMSW	:	サブウインドウ上からのキーイベント判定値 : 0 : デフォルト
//														 : 1 : キーイベント
// obj		:	ボタン配置ウインドウ名(IFRAME内にボタンを配置した場合に明示的な指定をする)

////////// [MSWBt]のボタン切替処理 //////////////////////////////////////////////////

// イメージ変換フラグ
var countM1_2 = 0;

// ENTERキー押下でイメージ変換時の制御フラグ
var creationEnterKeyCntExchange = 0;

/////////////////////////////////////////////////////////////////////////////////////
function ExchangeM01_2( lngMSW , obj , objFocus )
{

	// 制御フラグへキーイベント判定値の代入
	creationEnterKeyCntExchange = lngMSW;


	// 通常のクリック操作の場合
	if( creationEnterKeyCntExchange == 0 )
	{

		if( countM1_2 == 0 )
		{
			MSWBt01_2.innerHTML = mswbtA3_2;

			if( typeof(obj) != 'undefined' )
			{
				obj.MSWBt01_2.innerHTML = mswbtA3_2;
			}

			countM1_2 = 1;
		}
		else if( countM1_2 == 1 )
		{
			MSWBt01_2.innerHTML = mswbtA1_2;

			if( typeof(obj) != 'undefined' )
			{
				obj.MSWBt01_2.innerHTML = mswbtA1_2;
			}

			// ボタンへフォーカスを戻す
			//document.all.creationBt.focus();

			countM1_2 = 0;
		}

	}


	// キーイベントからの操作の場合
	else if( creationEnterKeyCntExchange == 1 )
	{
		MSWBt01_2.innerHTML = mswbtA1_2;

		if( typeof(obj) != 'undefined' )
		{
			obj.MSWBt01_2.innerHTML = mswbtA1_2;
		}

			// ボタンへフォーカスを戻す
			//document.all.creationBt.focus();

	}

	return false;
}









////////////////////////////// [ASSEMBLY FACTORY] //////////////////////////////

// レイヤー表示・非表示フラグ
var flagA_3 = 0;

// ENTERキー押下でクローズ時の制御フラグ
var assemblyEnterKeyCntDisplay = 0;

////////// レイヤー表示・非表示処理 /////////////////////////////////////////////////

// lngMSW	:	サブウインドウ上からのキーイベント判定値 : '' : デフォルト
//														 : 1  : キーイベント
//
// objID	: サブウインドウIFRAME名
// objA		: [InputA]サブウインドウオープン時に最初にフォーカスするオブジェクト名
// objB		: [InputB]サブウインドウオープン時に最初にフォーカスするオブジェクト名

/////////////////////////////////////////////////////////////////////////////////////

function DisplayerM01_3( lngMSW , objID , objA )
{

	if( lngMSW != '' )
	{
		// 制御フラグへキーイベント判定値の代入
		assemblyEnterKeyCntDisplay = lngMSW;
	}


	// 通常のクリック操作の場合
	if( assemblyEnterKeyCntDisplay == 0 )
	{
		if( lngMSW == '' && flagA_3 == 0 )
		{
			document.onmouseup = clearAll;
			document.all.MiFrame01_3.style.visibility = "visible";

			objA.focus();

			flagA_3 = 1;
		}

		else if( flagA_3 == 1 )
		{
			document.onmouseup = '';
			document.all.MiFrame01_3.style.visibility = "hidden";

			if( typeof( objID ) != "undefined")
			{
				objID.style.height = 381;
			}

			flagA_3 = 0;
		}
	}

	// キーイベントからの操作の場合
	else if( assemblyEnterKeyCntDisplay == 1 )
	{
		document.onmouseup = '';
		document.all.MiFrame01_3.style.visibility = "hidden";

		if( typeof( objID ) != "undefined")
		{
			objID.style.height = 381;
		}

		// リダイレクト用に値を代入
		assemblyEnterKeyCntDisplay = 2;
		flagA_3 = 0;

	}

	// キーイベントのリダイレクト処理
	else if( assemblyEnterKeyCntDisplay == 2 )
	{
		// 制御フラグの初期化
		assemblyEnterKeyCntDisplay = 0;
	}


	return false;
}




/////////////////////////////////////////////////////////////////////////////////////

// lngMSW	:	サブウインドウ上からのキーイベント判定値 : 0 : デフォルト
//														 : 1 : キーイベント
// obj		:	ボタン配置ウインドウ名(IFRAME内にボタンを配置した場合に明示的な指定をする)

////////// [MSWBt]のボタン切替処理 //////////////////////////////////////////////////

// イメージ変換フラグ
var countM1_3 = 0;

// ENTERキー押下でイメージ変換時の制御フラグ
var assemblyEnterKeyCntExchange = 0;

/////////////////////////////////////////////////////////////////////////////////////
function ExchangeM01_3( lngMSW , obj )
{

	// 制御フラグへキーイベント判定値の代入
	assemblyEnterKeyCntExchange = lngMSW;


	// 通常のクリック操作の場合
	if( assemblyEnterKeyCntExchange == 0 )
	{

		if( countM1_3 == 0 )
		{
			MSWBt01_3.innerHTML = mswbtA3_3;

			if( typeof(obj) != 'undefined' )
			{
				obj.MSWBt01_3.innerHTML = mswbtA3_3;
			}

			countM1_3 = 1;
		}
		else if( countM1_3 == 1 )
		{
			MSWBt01_3.innerHTML = mswbtA1_3;

			if( typeof(obj) != 'undefined' )
			{
				obj.MSWBt01_3.innerHTML = mswbtA1_3;
			}

			// ボタンへフォーカスを戻す
			//document.all.assemblyBt.focus();

			countM1_3 = 0;
		}

	}


	// キーイベントからの操作の場合
	else if( assemblyEnterKeyCntExchange == 1 )
	{
		MSWBt01_3.innerHTML = mswbtA1_3;

		if( typeof(obj) != 'undefined' )
		{
			obj.MSWBt01_3.innerHTML = mswbtA1_3;
		}

		// ボタンへフォーカスを戻す
		//document.all.assemblyBt.focus();

	}

	return false;
}






////////////////////////////// [DEPT & IN CHARGE NAME] //////////////////////////////

// レイヤー表示・非表示フラグ
var flagX = 0;

// ENTERキー押下でクローズ時の制御フラグ
var deptEnterKeyCntDisplay = 0;

////////// レイヤー表示・非表示処理 /////////////////////////////////////////////////

// lngMSW	:	サブウインドウ上からのキーイベント判定値 : '' : デフォルト
//														 : 1  : キーイベント
//
// objID	: サブウインドウIFRAME名
// objA		: [InputA]サブウインドウオープン時に最初にフォーカスするオブジェクト名
// objB		: [InputB]サブウインドウオープン時に最初にフォーカスするオブジェクト名

/////////////////////////////////////////////////////////////////////////////////////

function DisplayerM02( lngMSW , objID , objA , objB )
{

	if( lngMSW != '' )
	{
		// 制御フラグへキーイベント判定値の代入
		deptEnterKeyCntDisplay = lngMSW;
	}


	// 通常のクリック操作の場合
	if( deptEnterKeyCntDisplay == 0 )
	{
		if( lngMSW == '' && flagX == 0 )
		{
			document.onmouseup = clearAll;
			document.all.MiFrame02.style.visibility = "visible";

			if( window.MDwin.DeptValueFlg == 0 )
			{
				objA.focus();
			}

			if( window.MDwin.DeptValueFlg == 1 )
			{
				objB.focus();
			}

			flagX = 1;
		}

		else if( flagX == 1 )
		{
			document.onmouseup = '';
			document.all.MiFrame02.style.visibility = "hidden";

			if( typeof( objID ) != "undefined")
			{
				objID.style.height = 381;
			}

			flagX = 0;
		}
	}

	// キーイベントからの操作の場合
	else if( deptEnterKeyCntDisplay == 1 )
	{
		document.onmouseup = '';
		document.all.MiFrame02.style.visibility = "hidden";

		if( typeof( objID ) != "undefined")
		{
			objID.style.height = 381;
		}

		// リダイレクト用に値を代入
		deptEnterKeyCntDisplay = 2;
		flagX = 0;

	}

	// キーイベントのリダイレクト処理
	else if( deptEnterKeyCntDisplay == 2 )
	{
		// 制御フラグの初期化
		deptEnterKeyCntDisplay = 0;
	}

	return false;
}




/////////////////////////////////////////////////////////////////////////////////////

// lngMSW	:	サブウインドウ上からのキーイベント判定値 : 0 : デフォルト
//														 : 1 : キーイベント
// obj		:	ボタン配置ウインドウ名(IFRAME内にボタンを配置した場合に明示的な指定をする)
////////// [MSWBt]のボタン切替処理 //////////////////////////////////////////////////

// イメージ変換フラグ
var countM2 = 0;

// ENTERキー押下でイメージ変換時の制御フラグ
var deptEnterKeyCntExchange = 0;

/////////////////////////////////////////////////////////////////////////////////////

function ExchangeM02( lngMSW , obj )
{

	// 制御フラグへキーイベント判定値の代入
	deptEnterKeyCntExchange = lngMSW;


	// 通常のクリック操作の場合
	if( deptEnterKeyCntExchange == 0 )
	{

		if( countM2 == 0 )
		{
			MSWBt02.innerHTML = mswbtB3;

			if( typeof(obj) != 'undefined' )
			{
				obj.MSWBt02.innerHTML = mswbtB3;
			}

			countM2 = 1;
		}
		else if( countM2 == 1 )
		{
			MSWBt02.innerHTML = mswbtB1;

			if( typeof(obj) != 'undefined' )
			{
				obj.MSWBt02.innerHTML = mswbtB1;
			}

			// ボタンへフォーカスを戻す
			//document.all.deptBt.focus();

			countM2 = 0;
		}

	}


	// キーイベントからの操作の場合
	else if( deptEnterKeyCntExchange == 1 )
	{
		MSWBt02.innerHTML = mswbtB1;

		if( typeof(obj) != 'undefined' )
		{
			obj.MSWBt02.innerHTML = mswbtB1;
		}

		// ボタンへフォーカスを戻す
		//document.all.deptBt.focus();

	}

	return false;
}











////////////////////////////// [PRODUCTS] //////////////////////////////

// レイヤー表示・非表示フラグ
var flagD = 0;

// ENTERキー押下でクローズ時の制御フラグ
var locationEnterKeyCntDisplay = 0;

////////// レイヤー表示・非表示処理 /////////////////////////////////////////////////

// lngMSW	:	サブウインドウ上からのキーイベント判定値 : '' : デフォルト
//														 : 1  : キーイベント
//
// objID	: サブウインドウIFRAME名
// objA		: [InputA]サブウインドウオープン時に最初にフォーカスするオブジェクト名

/////////////////////////////////////////////////////////////////////////////////////
function DisplayerM03( lngMSW , objID , objA )
{

	if( lngMSW != '' )
	{
		// 制御フラグへキーイベント判定値の代入
		locationEnterKeyCntDisplay = lngMSW;
	}


	// 通常のクリック操作の場合
	if( locationEnterKeyCntDisplay == 0 )
	{
		if( flagD == 0 )
		{
			document.onmouseup = clearAll;
			document.all.MiFrame03.style.visibility = "visible";

			objA.focus();

			flagD = 1;
		}
		else if( flagD == 1 )
		{
			document.onmouseup = '';
			document.all.MiFrame03.style.visibility = "hidden";

			if( typeof( objID ) != "undefined")
			{
				objID.style.height = 381;
			}

			flagD = 0;
		}
	}

	// キーイベントからの操作の場合
	else if( locationEnterKeyCntDisplay == 1 )
	{
		document.onmouseup = '';
		document.all.MiFrame03.style.visibility = "hidden";

		if( typeof( objID ) != "undefined")
		{
			objID.style.height = 381;
		}

		// リダイレクト用に値を代入
		locationEnterKeyCntDisplay = 2;

		flagD = 0;
	}

	// キーイベントのリダイレクト処理
	else if( locationEnterKeyCntDisplay == 2 )
	{
		// 制御フラグの初期化
		locationEnterKeyCntDisplay = 0;
	}


	return false;
}


/////////////////////////////////////////////////////////////////////////////////////

// lngMSW	:	サブウインドウ上からのキーイベント判定値 : 0 : デフォルト
//														 : 1 : キーイベント
// obj		:	ボタン配置ウインドウ名(IFRAME内にボタンを配置した場合に明示的な指定をする)

////////// [MSWBt]のボタン切替処理 //////////////////////////////////////////////////

// イメージ変換フラグ
var countM3 = 0;

// ENTERキー押下でイメージ変換時の制御フラグ
var locationEnterKeyCntExchange = 0;

/////////////////////////////////////////////////////////////////////////////////////
function ExchangeM03( lngMSW , obj )
{

	// 制御フラグへキーイベント判定値の代入
	locationEnterKeyCntExchange = lngMSW;


	// 通常のクリック操作の場合
	if( locationEnterKeyCntExchange == 0 )
	{

		if( countM3 == 0 )
		{
			MSWBt03.innerHTML = mswbtC3;

			if( typeof(obj) != 'undefined' )
			{
				obj.MSWBt03.innerHTML = mswbtC3;
			}

			countM3 = 1;
		}
		else if( countM3 == 1 )
		{
			MSWBt03.innerHTML = mswbtC1;

			if( typeof(obj) != 'undefined' )
			{
				obj.MSWBt03.innerHTML = mswbtC1;
			}

			// ボタンへフォーカスを戻す
			//document.all.productsBt.focus();

			countM3 = 0;
		}

	}

	// キーイベントからの操作の場合
	else if( locationEnterKeyCntExchange == 1 )
	{
		MSWBt03.innerHTML = mswbtC1;

		if( typeof(obj) != 'undefined' )
		{
			obj.MSWBt03.innerHTML = mswbtC1;
		}

		// ボタンへフォーカスを戻す
		//document.all.productsBt.focus();
	}


	return false;
}








///////////////////////////////////// [LOCATION] ////////////////////////////////////

// レイヤー表示・非表示フラグ
var flagG = 0;

// ENTERキー押下でクローズ時の制御フラグ
var locationEnterKeyCntDisplay = 0;

////////// レイヤー表示・非表示処理 /////////////////////////////////////////////////

// lngMSW	:	サブウインドウ上からのキーイベント判定値 : '' : デフォルト
//														 : 1  : キーイベント
//
// objID	: サブウインドウIFRAME名
// objA		: [InputA]サブウインドウオープン時に最初にフォーカスするオブジェクト名
// objB		: [InputB]サブウインドウオープン時に最初にフォーカスするオブジェクト名

/////////////////////////////////////////////////////////////////////////////////////

function DisplayerM04( lngMSW , objID , objA )
{

	if( lngMSW != '' )
	{
		// 制御フラグへキーイベント判定値の代入
		locationEnterKeyCntDisplay = lngMSW;
	}


	// 通常のクリック操作の場合
	if( locationEnterKeyCntDisplay == 0 )
	{
		if( lngMSW == '' && flagG == 0 )
		{
			document.onmouseup = clearAll;
			document.all.MiFrame04.style.visibility = "visible";

			objA.focus();

			flagG = 1;
		}

		else if( flagG == 1 )
		{
			document.onmouseup = '';
			document.all.MiFrame04.style.visibility = "hidden";

			if( typeof( objID ) != "undefined")
			{
				objID.style.height = 381;
			}

			flagG = 0;
		}
	}

	// キーイベントからの操作の場合
	else if( locationEnterKeyCntDisplay == 1 )
	{
		document.onmouseup = '';
		document.all.MiFrame04.style.visibility = "hidden";

		if( typeof( objID ) != "undefined")
		{
			objID.style.height = 381;
		}

		// リダイレクト用に値を代入
		locationEnterKeyCntDisplay = 2;
		flagG = 0;

	}

	// キーイベントのリダイレクト処理
	else if( locationEnterKeyCntDisplay == 2 )
	{
		// 制御フラグの初期化
		locationEnterKeyCntDisplay = 0;
	}


	return false;
}




/////////////////////////////////////////////////////////////////////////////////////

// lngMSW	:	サブウインドウ上からのキーイベント判定値 : 0 : デフォルト
//														 : 1 : キーイベント
// obj		:	ボタン配置ウインドウ名(IFRAME内にボタンを配置した場合に明示的な指定をする)

////////// [MSWBt]のボタン切替処理 //////////////////////////////////////////////////

// イメージ変換フラグ
var countM4 = 0;

// ENTERキー押下でイメージ変換時の制御フラグ
var locationEnterKeyCntExchange = 0;

/////////////////////////////////////////////////////////////////////////////////////
function ExchangeM04( lngMSW , obj )
{

	// 制御フラグへキーイベント判定値の代入
	locationEnterKeyCntExchange = lngMSW;


	// 通常のクリック操作の場合
	if( locationEnterKeyCntExchange == 0 )
	{

		if( countM4 == 0 )
		{
			MSWBt04.innerHTML = mswbtD3;

			if( typeof(obj) != 'undefined' )
			{
				obj.MSWBt04.innerHTML = mswbtD3;
			}

			countM4 = 1;
		}
		else if( countM4 == 1 )
		{
			MSWBt04.innerHTML = mswbtD1;

			if( typeof(obj) != 'undefined' )
			{
				obj.MSWBt04.innerHTML = mswbtD1;
			}

			// ボタンへフォーカスを戻す
			//document.all.locationBt.focus();

			countM4 = 0;
		}

	}


	// キーイベントからの操作の場合
	else if( locationEnterKeyCntExchange == 1 )
	{
		MSWBt04.innerHTML = mswbtD1;

		if( typeof(obj) != 'undefined' )
		{
			obj.MSWBt04.innerHTML = mswbtD1;
		}

		// ボタンへフォーカスを戻す
		//document.all.locationBt.focus();

	}

	return false;
}









////////////////////////////// [APPLICANT] //////////////////////////////

// レイヤー表示・非表示フラグ
var Appcnt = 0;

// ENTERキー押下でクローズ時の制御フラグ
var applicantEnterKeyCntDisplay = 0;

////////// レイヤー表示・非表示処理 /////////////////////////////////////////////////

// lngMSW	:	サブウインドウ上からのキーイベント判定値 : '' : デフォルト
//														 : 1  : キーイベント
//
// objID	: サブウインドウIFRAME名
// objA		: [InputA]サブウインドウオープン時に最初にフォーカスするオブジェクト名

/////////////////////////////////////////////////////////////////////////////////////
function DisplayerM05( lngMSW , objID , objA )
{

	if( lngMSW != '' )
	{
		// 制御フラグへキーイベント判定値の代入
		applicantEnterKeyCntDisplay = lngMSW;
	}


	// 通常のクリック操作の場合
	if( applicantEnterKeyCntDisplay == 0 )
	{

		if( lngMSW == '' && Appcnt == 0 )
		{
			document.onmouseup = clearAll;
			document.all.MiFrame05.style.visibility = "visible";

			objA.focus();

			Appcnt = 1;
		}
		else if( Appcnt == 1 )
		{
			document.onmouseup = '';
			document.all.MiFrame05.style.visibility = "hidden";

			if( typeof( objID ) != "undefined")
			{
				objID.style.height = 381;
			}

			Appcnt = 0;
		}

	}

	// キーイベントからの操作の場合
	else if( applicantEnterKeyCntDisplay == 1 )
	{
		document.onmouseup = '';
		document.all.MiFrame05.style.visibility = "hidden";

		if( typeof( objID ) != "undefined")
		{
			objID.style.height = 381;
		}

		// リダイレクト用に値を代入
		applicantEnterKeyCntDisplay = 2;

		Appcnt = 0;
	}

	// キーイベントのリダイレクト処理
	else if( applicantEnterKeyCntDisplay == 2 )
	{
		// 制御フラグの初期化
		applicantEnterKeyCntDisplay = 0;
	}


	return false;
}


/////////////////////////////////////////////////////////////////////////////////////

// lngMSW	:	サブウインドウ上からのキーイベント判定値 : 0 : デフォルト
//														 : 1 : キーイベント
// obj		:	ボタン配置ウインドウ名(IFRAME内にボタンを配置した場合に明示的な指定をする)

////////// [MSWBt]のボタン切替処理 //////////////////////////////////////////////////

// イメージ変換フラグ
var countM5 = 0;

// ENTERキー押下でイメージ変換時の制御フラグ
var applicantEnterKeyCntExchange = 0;

/////////////////////////////////////////////////////////////////////////////////////
function ExchangeM05( lngMSW , obj )
{

	// 制御フラグへキーイベント判定値の代入
	applicantEnterKeyCntExchange = lngMSW;


	// 通常のクリック操作の場合
	if( applicantEnterKeyCntExchange == 0 )
	{

		if( countM5 == 0 )
		{
			MSWBt05.innerHTML = mswbtE3;

			if( typeof(obj) != 'undefined' )
			{
				obj.MSWBt05.innerHTML = mswbtE3;
			}

			countM5 = 1;
		}
		else if( countM5 == 1 )
		{
			MSWBt05.innerHTML = mswbtE1;

			if( typeof(obj) != 'undefined' )
			{
				obj.MSWBt05.innerHTML = mswbtE1;
			}

			// ボタンへフォーカスを戻す
			//document.all.applicantBt.focus();

			countM5 = 0;
		}

	}

	// キーイベントからの操作の場合
	else if( applicantEnterKeyCntExchange == 1 )
	{
		MSWBt05.innerHTML = mswbtE1;

		if( typeof(obj) != 'undefined' )
		{
			obj.MSWBt05.innerHTML = mswbtE1;
		}

		// ボタンへフォーカスを戻す
		//document.all.applicantBt.focus();

	}
	return false;
}









////////////////////////////// [WF INPUT] //////////////////////////////
// レイヤー表示・非表示フラグ
var Inputcnt = 0;

// ENTERキー押下でクローズ時の制御フラグ
var wfinputEnterKeyCntDisplay = 0;

////////// レイヤー表示・非表示処理 /////////////////////////////////////////////////

// lngMSW	:	サブウインドウ上からのキーイベント判定値 : '' : デフォルト
//														 : 1  : キーイベント
//
// objID	: サブウインドウIFRAME名
// objA		: [InputA]サブウインドウオープン時に最初にフォーカスするオブジェクト名

/////////////////////////////////////////////////////////////////////////////////////
function DisplayerM06( lngMSW , objID , objA )
{

	if( lngMSW != '' )
	{
		// 制御フラグへキーイベント判定値の代入
		wfinputEnterKeyCntDisplay = lngMSW;
	}


	// 通常のクリック操作の場合
	if( wfinputEnterKeyCntDisplay == 0 )
	{
		if( lngMSW == '' && Inputcnt == 0 )
		{
			document.onmouseup = clearAll;
			document.all.MiFrame06.style.visibility = "visible";

			objA.focus();

			Inputcnt = 1;
		}
		else if( Inputcnt == 1 )
		{
			document.onmouseup = '';
			document.all.MiFrame06.style.visibility = "hidden";

			if( typeof( objID ) != "undefined")
			{
				objID.style.height = 381;
			}

			Inputcnt = 0;
		}
	}

	// キーイベントからの操作の場合
	else if( wfinputEnterKeyCntDisplay == 1 )
	{
		document.onmouseup = '';
		document.all.MiFrame06.style.visibility = "hidden";

		if( typeof( objID ) != "undefined")
		{
			objID.style.height = 381;
		}

		wfinputEnterKeyCntDisplay = 2;

		Inputcnt = 0;
	}


	// キーイベントのリダイレクト処理
	else if( wfinputEnterKeyCntDisplay == 2 )
	{
		// 制御フラグの初期化
		wfinputEnterKeyCntDisplay = 0;
	}


	return false;
}


/////////////////////////////////////////////////////////////////////////////////////

// lngMSW	:	サブウインドウ上からのキーイベント判定値 : 0 : デフォルト
//														 : 1 : キーイベント
// obj		:	ボタン配置ウインドウ名(IFRAME内にボタンを配置した場合に明示的な指定をする)

////////// [MSWBt]のボタン切替処理 //////////////////////////////////////////////////
// イメージ変換フラグ
var countM6 = 0;

// ENTERキー押下でイメージ変換時の制御フラグ
var wfinputEnterKeyCntExchange = 0;

/////////////////////////////////////////////////////////////////////////////////////
function ExchangeM06( lngMSW , obj )
{

	// 制御フラグへキーイベント判定値の代入
	wfinputEnterKeyCntExchange = lngMSW;

	// 通常のクリック操作の場合
	if( wfinputEnterKeyCntExchange == 0 )
	{

		if( countM6 == 0 )
		{
			MSWBt06.innerHTML = mswbtF3;

			if( typeof(obj) != 'undefined' )
			{
				obj.MSWBt06.innerHTML = mswbtF3;
			}

			countM6 = 1;
		}
		else if( countM6 == 1 )
		{
			MSWBt06.innerHTML = mswbtF1;

			if( typeof(obj) != 'undefined' )
			{
				obj.MSWBt06.innerHTML = mswbtF1;
			}

			// ボタンへフォーカスを戻す
			//document.all.wfinputBt.focus();

			countM6 = 0;
		}

	}


	// キーイベントからの操作の場合
	else if( wfinputEnterKeyCntExchange == 1 )
	{
		MSWBt06.innerHTML = mswbtF1;

		if( typeof(obj) != 'undefined' )
		{
			obj.MSWBt06.innerHTML = mswbtF1;
		}

		// ボタンへフォーカスを戻す
		//document.all.wfinputBt.focus();

	}
	return false;
}







///////////////////////////// [VENDOR & IN CHARGE NAME] /////////////////////////////

// レイヤー表示・非表示フラグ
var VIcnt = 0;

// ENTERキー押下でクローズ時の制御フラグ
var viEnterKeyCntDisplay = 0;

////////// レイヤー表示・非表示処理 /////////////////////////////////////////////////

// lngMSW	:	サブウインドウ上からのキーイベント判定値 : '' : デフォルト
//														 : 1  : キーイベント
//
// objID	: サブウインドウIFRAME名
// objA		: [InputA]サブウインドウオープン時に最初にフォーカスするオブジェクト名
// objB		: [InputB]サブウインドウオープン時に最初にフォーカスするオブジェクト名

/////////////////////////////////////////////////////////////////////////////////////

function DisplayerM07( lngMSW , objID , objA , objB )
{

	if( lngMSW != '' )
	{
		// 制御フラグへキーイベント判定値の代入
		viEnterKeyCntDisplay = lngMSW;
	}


	// 通常のクリック操作の場合
	if( viEnterKeyCntDisplay == 0 )
	{
		if( lngMSW == '' && VIcnt == 0 )
		{
			document.onmouseup = clearAll;
			document.all.MiFrame07.style.visibility = "visible";

			if( window.VIwin.ViValueFlg == 0 )
			{
				objA.focus();
			}

			if( window.VIwin.ViValueFlg == 1 )
			{
				objB.focus();
			}

			VIcnt = 1;
		}
		else if( VIcnt == 1 )
		{
			document.onmouseup = '';
			document.all.MiFrame07.style.visibility = "hidden";

			if( typeof( objID ) != "undefined")
			{
				objID.style.height = 381;
			}

			VIcnt = 0;
		}
	}


	// キーイベントからの操作の場合
	else if( viEnterKeyCntDisplay == 1 )
	{
		document.onmouseup = '';
		document.all.MiFrame07.style.visibility = "hidden";

		if( typeof( objID ) != "undefined")
		{
			objID.style.height = 381;
		}

		// リダイレクト用に値を代入
		viEnterKeyCntDisplay = 2;
		VIcnt = 0;

	}

	// キーイベントのリダイレクト処理
	else if( viEnterKeyCntDisplay == 2 )
	{
		// 制御フラグの初期化
		viEnterKeyCntDisplay = 0;
	}


	return false;
}





/////////////////////////////////////////////////////////////////////////////////////

// lngMSW	:	サブウインドウ上からのキーイベント判定値 : 0 : デフォルト
//														 : 1 : キーイベント
// obj		:	ボタン配置ウインドウ名(IFRAME内にボタンを配置した場合に明示的な指定をする)

////////// [MSWBt]のボタン切替処理 //////////////////////////////////////////////////

// イメージ変換フラグ
var countM7 = 0;

// ENTERキー押下でイメージ変換時の制御フラグ
var viEnterKeyCntExchange = 0;

/////////////////////////////////////////////////////////////////////////////////////

function ExchangeM07( lngMSW , obj )
{

	// 制御フラグへキーイベント判定値の代入
	viEnterKeyCntExchange = lngMSW;


	// 通常のクリック操作の場合
	if( viEnterKeyCntExchange == 0 )
	{
		if( countM7 == 0 )
		{
			MSWBt07.innerHTML = mswbtG3;

			if( typeof(obj) != 'undefined' )
			{
				obj.MSWBt07.innerHTML = mswbtG3;
			}

			countM7 = 1;
		}
		else if( countM7 == 1 )
		{
			MSWBt07.innerHTML = mswbtG1;

			if( typeof(obj) != 'undefined' )
			{
				obj.MSWBt07.innerHTML = mswbtG1;
			}

			// ボタンへフォーカスを戻す
			//document.all.viBt.focus();

			countM7 = 0;
		}
	}


	// キーイベントからの操作の場合
	else if( viEnterKeyCntExchange == 1 )
	{
		MSWBt07.innerHTML = mswbtG1;

		if( typeof(obj) != 'undefined' )
		{
			obj.MSWBt07.innerHTML = mswbtG1;
		}

		// ボタンへフォーカスを戻す
		//document.all.viBt.focus();

	}


	return false;
}







////////////////////////////// [SUPPLIER] //////////////////////////////
////////// レイヤー表示・非表示処理 /////////////////////////////////////////////////

// lngMSW	:	サブウインドウ上からのキーイベント判定値 : '' : デフォルト
//														 : 1  : キーイベント
//
// objID	: サブウインドウIFRAME名
// objA		: [InputA]サブウインドウオープン時に最初にフォーカスするオブジェクト名

/////////////////////////////////////////////////////////////////////////////////////

// レイヤー表示・非表示フラグ
var SUPcnt = 0;

// ENTERキー押下でクローズ時の制御フラグ
var supplierEnterKeyCntDisplay = 0;

function DisplayerM08( lngMSW , objID , objA )
{

	if( lngMSW != '' )
	{
		// 制御フラグへキーイベント判定値の代入
		supplierEnterKeyCntDisplay = lngMSW;
	}


	// 通常のクリック操作の場合
	if( supplierEnterKeyCntDisplay == 0 )
	{
		if( lngMSW == '' && SUPcnt == 0 )
		{
			document.onmouseup = clearAll;
			document.all.MiFrame08.style.visibility = "visible";

			objA.focus();

			SUPcnt = 1;
		}

		else if( SUPcnt == 1 )
		{
			document.onmouseup = '';
			document.all.MiFrame08.style.visibility = "hidden";

			if( typeof( objID ) != "undefined")
			{
				objID.style.height = 381;
			}

			SUPcnt = 0;
		}
	}


	// キーイベントからの操作の場合
	else if( supplierEnterKeyCntDisplay == 1 )
	{
		document.onmouseup = '';
		document.all.MiFrame08.style.visibility = "hidden";

		if( typeof( objID ) != "undefined")
		{
			objID.style.height = 381;
		}

		// リダイレクト用に値を代入
		supplierEnterKeyCntDisplay = 2;
		SUPcnt = 0;
	}


	// キーイベントのリダイレクト処理
	else if( supplierEnterKeyCntDisplay == 2 )
	{
		// 制御フラグの初期化
		supplierEnterKeyCntDisplay = 0;
	}


	return false;
}



/////////////////////////////////////////////////////////////////////////////////////

// lngMSW	:	サブウインドウ上からのキーイベント判定値 : 0 : デフォルト
//														 : 1 : キーイベント
// obj		:	ボタン配置ウインドウ名(IFRAME内にボタンを配置した場合に明示的な指定をする)

////////// [MSWBt]のボタン切替処理 //////////////////////////////////////////////////

// イメージ変換フラグ
var countM8 = 0;

// ENTERキー押下でイメージ変換時の制御フラグ
var supplierEnterKeyCntExchange = 0;
/////////////////////////////////////////////////////////////////////////////////////

function ExchangeM08( lngMSW , obj )
{

	// 制御フラグへキーイベント判定値の代入
	supplierEnterKeyCntExchange = lngMSW;


	// 通常のクリック操作の場合
	if( supplierEnterKeyCntExchange == 0 )
	{

		if( countM8 == 0 )
		{
			MSWBt08.innerHTML = mswbtH3;

			if( typeof(obj) != 'undefined' )
			{
				obj.MSWBt08.innerHTML = mswbtH3;
			}

			countM8 = 1;
		}

		else if( countM8 == 1 )
		{
			MSWBt08.innerHTML = mswbtH1;

			if( typeof(obj) != 'undefined' )
			{
				obj.MSWBt08.innerHTML = mswbtH1;
			}

			// ボタンへフォーカスを戻す
			//document.all.supplierBt.focus();

			countM8 = 0;
		}

	}


	// キーイベントからの操作の場合
	else if( supplierEnterKeyCntExchange == 1 )
	{
		MSWBt08.innerHTML = mswbtH1;

		if( typeof(obj) != 'undefined' )
		{
			obj.MSWBt08.innerHTML = mswbtH1;
		}

		// ボタンへフォーカスを戻す
		//document.all.supplierBt.focus();
	}

	return false;
}
















/////////////////////////////////// [INPUT PERSON] //////////////////////////////////

// レイヤー表示・非表示フラグ
var Input2cnt = 0;

// ENTERキー押下でクローズ時の制御フラグ
var inputEnterKeyCntDisplay = 0;

////////// レイヤー表示・非表示処理 /////////////////////////////////////////////////

// lngMSW	:	サブウインドウ上からのキーイベント判定値 : '' : デフォルト
//														 : 1  : キーイベント
//
// objID	: サブウインドウIFRAME名
// objA		: [InputA]サブウインドウオープン時に最初にフォーカスするオブジェクト名
// objB		: [InputB]サブウインドウオープン時に最初にフォーカスするオブジェクト名

/////////////////////////////////////////////////////////////////////////////////////

function DisplayerM09( lngMSW , objID , objA )
{

	if( lngMSW != '' )
	{
		// 制御フラグへキーイベント判定値の代入
		inputEnterKeyCntDisplay = lngMSW;
	}


	// 通常のクリック操作の場合
	if( inputEnterKeyCntDisplay == 0 )
	{
		if( lngMSW == '' && Input2cnt == 0 )
		{
			document.onmouseup = clearAll;
			document.all.MiFrame09.style.visibility = "visible";

			objA.focus();

			Input2cnt = 1;
		}

		else if( Input2cnt == 1 )
		{
			document.onmouseup = '';
			document.all.MiFrame09.style.visibility = "hidden";

			if( typeof( objID ) != "undefined")
			{
				objID.style.height = 381;
			}

			Input2cnt = 0;
		}
	}

	// キーイベントからの操作の場合
	else if( inputEnterKeyCntDisplay == 1 )
	{
		document.onmouseup = '';
		document.all.MiFrame09.style.visibility = "hidden";

		if( typeof( objID ) != "undefined")
		{
			objID.style.height = 381;
		}

		// リダイレクト用に値を代入
		inputEnterKeyCntDisplay = 2;
		Input2cnt = 0;

	}

	// キーイベントのリダイレクト処理
	else if( inputEnterKeyCntDisplay == 2 )
	{
		// 制御フラグの初期化
		inputEnterKeyCntDisplay = 0;
	}


	return false;
}




/////////////////////////////////////////////////////////////////////////////////////

// lngMSW	:	サブウインドウ上からのキーイベント判定値 : 0 : デフォルト
//														 : 1 : キーイベント
// obj		:	ボタン配置ウインドウ名(IFRAME内にボタンを配置した場合に明示的な指定をする)

////////// [MSWBt]のボタン切替処理 //////////////////////////////////////////////////

// イメージ変換フラグ
var countM9 = 0;

// ENTERキー押下でイメージ変換時の制御フラグ
var inputEnterKeyCntExchange = 0;

/////////////////////////////////////////////////////////////////////////////////////
function ExchangeM09( lngMSW , obj )
{

	// 制御フラグへキーイベント判定値の代入
	inputEnterKeyCntExchange = lngMSW;


	// 通常のクリック操作の場合
	if( inputEnterKeyCntExchange == 0 )
	{

		if( countM9 == 0 )
		{
			MSWBt09.innerHTML = mswbtI3;

			if( typeof(obj) != 'undefined' )
			{
				obj.MSWBt09.innerHTML = mswbtI3;
			}

			countM9 = 1;
		}
		else if( countM9 == 1 )
		{
			MSWBt09.innerHTML = mswbtI1;

			if( typeof(obj) != 'undefined' )
			{
				obj.MSWBt09.innerHTML = mswbtI1;
			}

			// ボタンへフォーカスを戻す
			//window.top.Pwin.document.all.inputBt.focus();

			countM9 = 0;
		}

	}


	// キーイベントからの操作の場合
	else if( inputEnterKeyCntExchange == 1 )
	{
		MSWBt09.innerHTML = mswbtI1;

		if( typeof(obj) != 'undefined' )
		{
			obj.MSWBt09.innerHTML = mswbtI1;
		}

		// ボタンへフォーカスを戻す
		//window.top.Pwin.document.all.inputBt.focus();

	}

	return false;
}


////////////////////////////// [DATE] //////////////////////////////

// レイヤー表示・非表示フラグ
var flagDate = 0;

// ENTERキー押下でクローズ時の制御フラグ
var dateEnterKeyCntDisplay = 0;

////////// レイヤー表示・非表示処理 /////////////////////////////////////////////////

// lngMSW	:	サブウインドウ上からのキーイベント判定値 : '' : デフォルト
//														 : 1  : キーイベント
//
// objID	: サブウインドウIFRAME名

/////////////////////////////////////////////////////////////////////////////////////
function DisplayerM10( lngMSW , objID )
{

	if( lngMSW != '' )
	{
		// 制御フラグへキーイベント判定値の代入
		dateEnterKeyCntDisplay = lngMSW;
	}


	// 通常のクリック操作の場合
	if( dateEnterKeyCntDisplay == 0 )
	{

		if( lngMSW == '' && flagDate == 0 )
		{
			document.onmouseup = clearAll;
			document.all.MiFrame10.style.visibility = "visible";

			flagDate = 1;
		}
		else if( flagDate == 1 )
		{
			document.onmouseup = '';
			document.all.MiFrame10.style.visibility = "hidden";

			if( typeof( objID ) != "undefined")
			{
				objID.style.height = 245;
			}

			flagDate = 0;
		}
	}

	// キーイベントからの操作の場合
	else if( dateEnterKeyCntDisplay == 1 )
	{
		document.onmouseup = '';
		document.all.MiFrame10.style.visibility = "hidden";

		if( typeof( objID ) != "undefined")
		{
			objID.style.height = 245;
		}

		// リダイレクト用に値を代入
		dateEnterKeyCntDisplay = 2;

		flagDate = 0;
	}

	// キーイベントのリダイレクト処理
	else if( dateEnterKeyCntDisplay == 2 )
	{
		// 制御フラグの初期化
		dateEnterKeyCntDisplay = 0;
	}

	return false;
}


/////////////////////////////////////////////////////////////////////////////////////

// lngMSW	:	サブウインドウ上からのキーイベント判定値 : 0 : デフォルト
//														 : 1 : キーイベント
// obj		:	ボタン配置ウインドウ名(IFRAME内にボタンを配置した場合に明示的な指定をする)

////////// [MSWBt]のボタン切替処理 //////////////////////////////////////////////////
// イメージ変換フラグ
var countM10 = 0;

// ENTERキー押下でイメージ変換時の制御フラグ
var dateEnterKeyCntExchange = 0;

/////////////////////////////////////////////////////////////////////////////////////
function ExchangeM10( lngMSW , obj )
{

	// 制御フラグへキーイベント判定値の代入
	dateEnterKeyCntExchange = lngMSW;

	// 通常のクリック操作の場合
	if( dateEnterKeyCntExchange == 0 )
	{

		if( countM10 == 0 )
		{
			DateBtA.innerHTML = datebuttonA3;

			if( typeof(obj) != 'undefined' )
			{
				obj.DateBtA.innerHTML = datebuttonA3;
			}

			countM10 = 1;
		}
		else if( countM10 == 1 )
		{
			DateBtA.innerHTML = datebuttonA;

			if( typeof(obj) != 'undefined' )
			{
				obj.DateBtA.innerHTML = datebuttonA;
			}

			// ボタンへフォーカスを戻す
			//document.all.DateBtA.focus();

			countM10 = 0;
		}

	}


	// キーイベントからの操作の場合
	else if( dateEnterKeyCntExchange == 1 )
	{
		DateBtA.innerHTML = datebuttonA;

		if( typeof(obj) != 'undefined' )
		{
			obj.DateBtA.innerHTML = datebuttonA;
		}

		// ボタンへフォーカスを戻す
		//document.all.DateBtA.focus();

	}


	return false;
}











////////////////////////////// [DATEB] //////////////////////////////

// レイヤー表示・非表示フラグ
var flagDateB = 0;

// ENTERキー押下でクローズ時の制御フラグ
var dateBEnterKeyCntDisplay = 0;

////////// レイヤー表示・非表示処理 /////////////////////////////////////////////////

// lngMSW	:	サブウインドウ上からのキーイベント判定値 : '' : デフォルト
//														 : 1  : キーイベント
//
// objID	: サブウインドウIFRAME名

/////////////////////////////////////////////////////////////////////////////////////
function DisplayerM10_2( lngMSW , objID )
{

	if( lngMSW != '' )
	{
		// 制御フラグへキーイベント判定値の代入
		dateBEnterKeyCntDisplay = lngMSW;
	}


	// 通常のクリック操作の場合
	if( dateBEnterKeyCntDisplay == 0 )
	{

		if( lngMSW == '' && flagDateB == 0 )
		{
			document.onmouseup = clearAll;
			document.all.MiFrame10_2.style.visibility = "visible";

			flagDateB = 1;
		}
		else if( flagDateB == 1 )
		{
			document.onmouseup = '';
			document.all.MiFrame10_2.style.visibility = "hidden";

			if( typeof( objID ) != "undefined")
			{
				objID.style.height = 245;
			}

			flagDateB = 0;
		}
	}

	// キーイベントからの操作の場合
	else if( dateBEnterKeyCntDisplay == 1 )
	{
		document.onmouseup = '';
		document.all.MiFrame10_2.style.visibility = "hidden";

		if( typeof( objID ) != "undefined")
		{
			objID.style.height = 245;
		}

		// リダイレクト用に値を代入
		dateBEnterKeyCntDisplay = 2;

		flagDateB = 0;
	}

	// キーイベントのリダイレクト処理
	else if( dateBEnterKeyCntDisplay == 2 )
	{
		// 制御フラグの初期化
		dateBEnterKeyCntDisplay = 0;
	}

	return false;
}


/////////////////////////////////////////////////////////////////////////////////////

// lngMSW	:	サブウインドウ上からのキーイベント判定値 : 0 : デフォルト
//														 : 1 : キーイベント
// obj		:	ボタン配置ウインドウ名(IFRAME内にボタンを配置した場合に明示的な指定をする)

////////// [MSWBt]のボタン切替処理 //////////////////////////////////////////////////
// イメージ変換フラグ
var countM10_2 = 0;

// ENTERキー押下でイメージ変換時の制御フラグ
var dateBEnterKeyCntExchange = 0;

/////////////////////////////////////////////////////////////////////////////////////
function ExchangeM10_2( lngMSW , obj )
{

	// 制御フラグへキーイベント判定値の代入
	dateBEnterKeyCntExchange = lngMSW;

	// 通常のクリック操作の場合
	if( dateBEnterKeyCntExchange == 0 )
	{

		if( countM10_2 == 0 )
		{
			DateBtB.innerHTML = datebuttonB3;

			if( typeof(obj) != 'undefined' )
			{
				obj.DateBtB.innerHTML = datebuttonB3;
			}

			countM10_2 = 1;
		}
		else if( countM10_2 == 1 )
		{
			DateBtB.innerHTML = datebuttonB;

			if( typeof(obj) != 'undefined' )
			{
				obj.DateBtB.innerHTML = datebuttonB;
			}

			// ボタンへフォーカスを戻す
			//document.all.DateBtB.focus();

			countM10_2 = 0;
		}

	}


	// キーイベントからの操作の場合
	else if( dateBEnterKeyCntExchange == 1 )
	{
		DateBtB.innerHTML = datebuttonB;

		if( typeof(obj) != 'undefined' )
		{
			obj.DateBtB.innerHTML = datebuttonB;
		}

		// ボタンへフォーカスを戻す
		//document.all.DateBtB.focus();

	}


	return false;
}




////////////////////////////// [DATEC] //////////////////////////////

// レイヤー表示・非表示フラグ
var flagDateC = 0;

// ENTERキー押下でクローズ時の制御フラグ
var dateCEnterKeyCntDisplay = 0;

////////// レイヤー表示・非表示処理 /////////////////////////////////////////////////

// lngMSW	:	サブウインドウ上からのキーイベント判定値 : '' : デフォルト
//														 : 1  : キーイベント
//
// objID	: サブウインドウIFRAME名

/////////////////////////////////////////////////////////////////////////////////////
function DisplayerM10_3( lngMSW , objID )
{

	if( lngMSW != '' )
	{
		// 制御フラグへキーイベント判定値の代入
		dateCEnterKeyCntDisplay = lngMSW;
	}


	// 通常のクリック操作の場合
	if( dateCEnterKeyCntDisplay == 0 )
	{

		if( lngMSW == '' && flagDateC == 0 )
		{
			document.onmouseup = clearAll;
			document.all.MiFrame10_3.style.visibility = "visible";

			flagDateC = 1;
		}
		else if( flagDateC == 1 )
		{
			document.onmouseup = '';
			document.all.MiFrame10_3.style.visibility = "hidden";

			if( typeof( objID ) != "undefined")
			{
				objID.style.height = 245;
			}

			flagDateC = 0;
		}
	}

	// キーイベントからの操作の場合
	else if( dateCEnterKeyCntDisplay == 1 )
	{
		document.onmouseup = '';
		document.all.MiFrame10_3.style.visibility = "hidden";

		if( typeof( objID ) != "undefined")
		{
			objID.style.height = 245;
		}

		// リダイレクト用に値を代入
		dateCEnterKeyCntDisplay = 2;

		flagDateC = 0;
	}

	// キーイベントのリダイレクト処理
	else if( dateCEnterKeyCntDisplay == 2 )
	{
		// 制御フラグの初期化
		dateCEnterKeyCntDisplay = 0;
	}

	return false;
}


/////////////////////////////////////////////////////////////////////////////////////

// lngMSW	:	サブウインドウ上からのキーイベント判定値 : 0 : デフォルト
//														 : 1 : キーイベント
// obj		:	ボタン配置ウインドウ名(IFRAME内にボタンを配置した場合に明示的な指定をする)

////////// [MSWBt]のボタン切替処理 //////////////////////////////////////////////////
// イメージ変換フラグ
var countM10_3 = 0;

// ENTERキー押下でイメージ変換時の制御フラグ
var dateCEnterKeyCntExchange = 0;

/////////////////////////////////////////////////////////////////////////////////////
function ExchangeM10_3( lngMSW , obj )
{

	// 制御フラグへキーイベント判定値の代入
	dateCEnterKeyCntExchange = lngMSW;

	// 通常のクリック操作の場合
	if( dateCEnterKeyCntExchange == 0 )
	{

		if( countM10_3 == 0 )
		{
			DateBtC.innerHTML = datebuttonC3;

			if( typeof(obj) != 'undefined' )
			{
				obj.DateBtC.innerHTML = datebuttonC3;
			}

			countM10_3 = 1;
		}
		else if( countM10_3 == 1 )
		{
			DateBtC.innerHTML = datebuttonC;

			if( typeof(obj) != 'undefined' )
			{
				obj.DateBtC.innerHTML = datebuttonC;
			}

			// ボタンへフォーカスを戻す
			//document.all.DateBtC.focus();

			countM10_3 = 0;
		}

	}


	// キーイベントからの操作の場合
	else if( dateCEnterKeyCntExchange == 1 )
	{
		DateBtC.innerHTML = datebuttonC;

		if( typeof(obj) != 'undefined' )
		{
			obj.DateBtC.innerHTML = datebuttonC;
		}

		// ボタンへフォーカスを戻す
		//document.all.DateBtC.focus();

	}


	return false;
}





///////////////////////////////////////////////////////////////////////////
/* Drag Layer */

var selected = null;
var offsetX = 0;
var offsetY = 0;

function getDivLeft(div){
	return div.offsetLeft;
}

function getDivTop(div){
	return div.offsetTop;
}

function getPageX(e){
	return document.body.scrollLeft + window.event.clientX; // document.body.clientWidth;
}

function getPageY(e){
	return document.body.scrollTop + window.event.clientY; // document.body.clientHeight;
}

////////// PICKUP //////////
function pickup01(layerID,e){ // [VENDOR]
	selected = document.getElementById(layerID);
	offsetX = getPageX(e) - getDivLeft(selected);
	offsetY = getPageY(e) - getDivTop(selected);
	document.onmousemove = dragIE01;
}

function pickup01_2(layerID,e){ // [CREATION FACTORY]
	selected = document.getElementById(layerID);
	offsetX = getPageX(e) - getDivLeft(selected);
	offsetY = getPageY(e) - getDivTop(selected);
	document.onmousemove = dragIE01_2;
}

function pickup01_3(layerID,e){ // [ASSEMBLY FACTORY]
	selected = document.getElementById(layerID);
	offsetX = getPageX(e) - getDivLeft(selected);
	offsetY = getPageY(e) - getDivTop(selected);
	document.onmousemove = dragIE01_3;
}

function pickup02(layerID,e){ // [DEPT & IN CHARGE NAME]
	selected = document.getElementById(layerID);
	offsetX = getPageX(e) - getDivLeft(selected);
	offsetY = getPageY(e) - getDivTop(selected);
	document.onmousemove = dragIE02;
}

function pickup03(layerID,e){ // [PRODUCTS]
	selected = document.getElementById(layerID);
	offsetX = getPageX(e) - getDivLeft(selected);
	offsetY = getPageY(e) - getDivTop(selected);
	document.onmousemove = dragIE03;
}

function pickup04(layerID,e){ // [LOCATION]
	selected = document.getElementById(layerID);
	offsetX = getPageX(e) - getDivLeft(selected);
	offsetY = getPageY(e) - getDivTop(selected);
	document.onmousemove = dragIE04;
}

function pickup05(layerID,e){ // [APPLICANT]
	selected = document.getElementById(layerID);
	offsetX = getPageX(e) - getDivLeft(selected);
	offsetY = getPageY(e) - getDivTop(selected);
	document.onmousemove = dragIE05;
}

function pickup06(layerID,e){ // [INPUT]
	selected = document.getElementById(layerID);
	offsetX = getPageX(e) - getDivLeft(selected);
	offsetY = getPageY(e) - getDivTop(selected);
	document.onmousemove = dragIE06;
}

function pickup07(layerID,e){ // [VENDOR & IN CHARGE NAME]
	selected = document.getElementById(layerID);
	offsetX = getPageX(e) - getDivLeft(selected);
	offsetY = getPageY(e) - getDivTop(selected);
	document.onmousemove = dragIE07;
}

function pickup08(layerID,e){ // [SUPPLIER]
	selected = document.getElementById(layerID);
	offsetX = getPageX(e) - getDivLeft(selected);
	offsetY = getPageY(e) - getDivTop(selected);
	document.onmousemove = dragIE08;
}
function pickup08_2(layerID,e){ // [SUPPLIER 2]
	selected = document.getElementById(layerID);
	offsetX = getPageX(e) - getDivLeft(selected);
	offsetY = getPageY(e) - getDivTop(selected);
	document.onmousemove = dragIE08_2;
}

function pickup09(layerID,e){ // [INPUT2]
	selected = document.getElementById(layerID);
	offsetX = getPageX(e) - getDivLeft(selected);
	offsetY = getPageY(e) - getDivTop(selected);
	document.onmousemove = dragIE09;
}

function pickup10(layerID,e){ // [DATE]
	selected = document.getElementById(layerID);
	offsetX = getPageX(e) - getDivLeft(selected);
	offsetY = getPageY(e) - getDivTop(selected);
	document.onmousemove = dragIE10;
}


function pickup10_2(layerID,e){ // [DATEB]
	selected = document.getElementById(layerID);
	offsetX = getPageX(e) - getDivLeft(selected);
	offsetY = getPageY(e) - getDivTop(selected);
	document.onmousemove = dragIE10_2;
}


function pickup10_3(layerID,e){ // [DATEC]
	selected = document.getElementById(layerID);
	offsetX = getPageX(e) - getDivLeft(selected);
	offsetY = getPageY(e) - getDivTop(selected);
	document.onmousemove = dragIE10_3;
}

////////// DRAG & MOVING //////////
function dragIE01(){ // [VENDOR]
	var movetoX = event.clientX+document.body.scrollLeft-offsetX;
	var movetoY = event.clientY+document.body.scrollTop -offsetY;
	selected.style.pixelLeft = movetoX;
	selected.style.pixelTop  = movetoY;

	window.MVwin.VarsB01.style.visibility = 'hidden';

	return false;
}

function dragIE01_2(){ // [CREATION FACTORY]
	var movetoX = event.clientX+document.body.scrollLeft-offsetX;
	var movetoY = event.clientY+document.body.scrollTop -offsetY;
	selected.style.pixelLeft = movetoX;
	selected.style.pixelTop  = movetoY;

	window.MVwin_2.VarsB01.style.visibility = 'hidden';

	return false;
}

function dragIE01_3(){ // [ASSEMBLY FACTORY]
	var movetoX = event.clientX+document.body.scrollLeft-offsetX;
	var movetoY = event.clientY+document.body.scrollTop -offsetY;
	selected.style.pixelLeft = movetoX;
	selected.style.pixelTop  = movetoY;

	window.MVwin_3.VarsB01.style.visibility = 'hidden';

	return false;
}

function dragIE02(){ // [DEPT & IN CHARGE NAME]
	var movetoX = event.clientX + document.body.scrollLeft - offsetX;
	var movetoY = event.clientY + document.body.scrollTop  - offsetY;

	selected.style.pixelLeft = movetoX;

	selected.style.pixelTop  = movetoY;

	
	//alert(document.body.clientWidth+","+document.body.clientHeight);
	//window.status = 'LEFT: ' + selected.style.pixelLeft + 'px TOP: ' + selected.style.pixelTop + 'px';

	if( window.MDwin.DeptValueFlg == 0 )
	{
		window.MDwin.VarsB01.style.visibility = 'hidden';
	}

	if( window.MDwin.DeptValueFlg == 1 )
	{
		window.MDwin.VarsD01.style.visibility = 'hidden';
	}


	return false;
}

function dragIE03(){ // [PRODUCTS]
	var movetoX = event.clientX+document.body.scrollLeft-offsetX;
	var movetoY = event.clientY+document.body.scrollTop -offsetY;
	selected.style.pixelLeft = movetoX;
	selected.style.pixelTop  = movetoY;

	window.MGwin.VarsB01.style.visibility = 'hidden';

	return false;
}

function dragIE04(){ // [LOCATION]
	var movetoX = event.clientX+document.body.scrollLeft-offsetX;
	var movetoY = event.clientY+document.body.scrollTop -offsetY;
	selected.style.pixelLeft = movetoX;
	selected.style.pixelTop  = movetoY;

	window.MLwin.VarsB01.style.visibility = 'hidden';

	return false;
}

function dragIE05(){ // [APPLICANT]
	var movetoX = event.clientX+document.body.scrollLeft-offsetX;
	var movetoY = event.clientY+document.body.scrollTop -offsetY;
	selected.style.pixelLeft = movetoX;
	selected.style.pixelTop  = movetoY;

	window.APPwin.VarsB01.style.visibility = 'hidden';

	return false;
}

function dragIE06(){ // [INPUT]
	var movetoX = event.clientX+document.body.scrollLeft-offsetX;
	var movetoY = event.clientY+document.body.scrollTop -offsetY;
	selected.style.pixelLeft = movetoX;
	selected.style.pixelTop  = movetoY;

	window.INPUTwin.VarsB01.style.visibility = 'hidden';

	return false;
}

function dragIE07(){ // [VENDOR & IN CHARGE NAME]
	var movetoX = event.clientX+document.body.scrollLeft-offsetX;
	var movetoY = event.clientY+document.body.scrollTop -offsetY;
	selected.style.pixelLeft = movetoX;
	selected.style.pixelTop  = movetoY;

	if( window.VIwin.ViValueFlg == 0 )
	{
		window.VIwin.VarsB01.style.visibility = 'hidden';
	}

	if( window.VIwin.ViValueFlg == 1 )
	{
		window.VIwin.VarsD01.style.visibility = 'hidden';
	}

	return false;
}

function dragIE08(){ // [SUPPLIER]
	var movetoX = event.clientX+document.body.scrollLeft-offsetX;
	var movetoY = event.clientY+document.body.scrollTop -offsetY;
	selected.style.pixelLeft = movetoX;
	selected.style.pixelTop  = movetoY;

	window.SUPwin.VarsB01.style.visibility = 'hidden';

	return false;
}
function dragIE08_2(){ // [SUPPLIER]
	var movetoX = event.clientX+document.body.scrollLeft-offsetX;
	var movetoY = event.clientY+document.body.scrollTop -offsetY;
	selected.style.pixelLeft = movetoX;
	selected.style.pixelTop  = movetoY;

	window.SUPwin.VarsB01.style.visibility = 'hidden';

	return false;
}

function dragIE09(){ // [INPUT2]
	var movetoX = event.clientX+document.body.scrollLeft-offsetX;
	var movetoY = event.clientY+document.body.scrollTop -offsetY;
	selected.style.pixelLeft = movetoX;
	selected.style.pixelTop  = movetoY;

	window.INPUT2win.VarsB01.style.visibility = 'hidden';

	return false;
}


function dragIE10(){ // [DATE]
	var movetoX = event.clientX+document.body.scrollLeft-offsetX;
	var movetoY = event.clientY+document.body.scrollTop -offsetY;
	selected.style.pixelLeft = movetoX;
	selected.style.pixelTop  = movetoY;

	return false;
}


function dragIE10_2(){ // [DATEB]
	var movetoX = event.clientX+document.body.scrollLeft-offsetX;
	var movetoY = event.clientY+document.body.scrollTop -offsetY;
	selected.style.pixelLeft = movetoX;
	selected.style.pixelTop  = movetoY;

	return false;
}


function dragIE10_3(){ // [DATEC]
	var movetoX = event.clientX+document.body.scrollLeft-offsetX;
	var movetoY = event.clientY+document.body.scrollTop -offsetY;
	selected.style.pixelLeft = movetoX;
	selected.style.pixelTop  = movetoY;

	return false;
}

////////// CLEAR ALL //////////
function clearAll() {
	document.onmousemove = '';
	selected = null;
	offsetX = 0;
	offsetY = 0;
}


//-->