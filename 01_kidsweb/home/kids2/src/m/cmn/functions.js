

//*******************************************************************
// マスター選択にて選択されたマスタ処理のページへリクエストする関数
// strTableName: マスターテーブル名
// objForm     : FORM OBJECT
function fncRequestSearchMasterEdit( strTableName, objForm )
{
	// 空白の場合
	if ( !strTableName )
	{
	}

	// 会社マスタの場合
	else if ( strTableName == 'm_Company' )
	{
		objForm.action = '/m/search/co/search.php';
		objForm.submit();
	}

	// グループマスタの場合
	else if  ( strTableName == 'm_Group' )
	{
		objForm.action = '/m/search/g/search.php';
		objForm.submit();
	}

	// 通貨レートマスタの場合
	else if  ( strTableName == 'm_MonetaryRate' )
	{
		objForm.action = '/m/search/r/search.php';
		objForm.submit();
	}

	// その他のマスタの場合(共通マスタ管理へ)
	else if ( strTableName != '' )
	{
		objForm.action = '/m/list/c/index.php';
		objForm.submit();
	}

	return false;
}



//*******************************************************************
// 共通マスター選択にて選択されたマスタ処理のページへリクエストする関数
// strTableName: マスタテーブル名
// objForm     : FORM OBJECT
function fncRequestCommonMasterEdit( strTableName, objForm )
{
	document.all.strMasterTableName.value = strTableName;

	objForm.action = '/m/list/c/index.php';

	objForm.submit();

	return false;
}





/*********************************************************************************/
// objVars1 : [fncShowDialogCommonMaster]の第１引数
// objVars2 : [fncShowDialogCommonMaster]の第２引数

////////// ダイアログ呼出関数リダイレクト用関数 //////////
function fncQueryDialog( objVars1 , objVars2 )
{

	// ダイアログ呼出
	fncShowDialogCommonMaster( objVars1 ,
								window.Pwin.form1 ,
								'ResultIframeCommonMaster' ,
								'NO' ,
								objVars2 ,
								'add' );

}






/*********************************************************************************/
// objInputArray : 変換元のID用配列
// objStyleArray : 変換用のID用配列

// 上記二つの配列内の順番はそれぞれ１対１の関係にすること。
// 例) fncChangeObjectIdModule( Array( '変換元IDその１' , '変換元IDその３' ) , Array( '変換用IDその１' , '変換用IDその３' ) )

////////// オブジェクトのID変換用モジュール //////////
function fncChangeObjectIdModule( objInputArray , objStyleArray )
{

	for( i = 0; i < objInputArray.length; i ++ )
	{
		// IDの入替え
		document.all( objInputArray[i] ).id = objStyleArray[i];
	}

	return false;
}



////////// [追加][修正]用、オブジェクトの自動レイアウト用モジュール //////////
function fncInitLayoutObjectModule( obj1 , obj2 , lngXpos1 , lngXpos2 )
{
	Backs.style.background = '#f1f1f1'; //d6d0b1


	var initYpos1 = 30;  //TOP座標・カラム初期値
	var initYpos2 = 30;  //TOP座標・フォーム要素初期値

	var moveYpos  = 31;  //TOP座標・移動値

	var segsXpos  = lngXpos1; //LEFT座標・カラム固定値
	var varsXpos  = lngXpos2; //LEFT座標・フォーム要素固定値


	var segsWidth = 165; //カラム幅固定値

	var FontColors    = '#666666'
	var BackColors1   = '#e8f0f1';
	var BackColors2   = '#f1f1f1';
	var BorderColors1 = '#798787 #e8f0f1 #798787 #798787';
	var BorderColors2 = '#798787 #798787 #798787 #e8f0f1';


	var lay1 = obj1.children; //カラム
	var lay2 = obj2.children; //フォーム要素


	var lngtabindex = 1; //TAB INDEX 初期値


	///// カラム展開 /////
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

	///// フォーム要素展開 /////
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


	///// TAB INDEX 展開 /////
	for (i = 0; i < window.PS.elements.length; i++)
	{
		window.PS.elements[i].tabindex = lngtabindex + 1;
	}

	return false;
}



/************************************************************/

var g_offY1 = 0;

function fncStopHeader( ObjID1 )
{

	sy = document.body.scrollTop;

	//ObjID.style.visibility = 'hidden';

	ObjID1.style.top = sy + g_offY1;

	//ObjID.style.visibility = 'visible';

}

var g_off3Y1 = 5;
var g_off3Y2 = 0;

function fncStopHeader3( ObjID1 , ObjID2 )
{

	sy = document.body.scrollTop;

	//ObjID.style.visibility = 'hidden';

	ObjID1.style.top = sy + g_off3Y1;
	ObjID2.style.top = sy + g_off3Y2;

	//ObjID.style.visibility = 'visible';

}
/************************************************************/




/************************************************************/

var g_off2Y1 = 20;
var g_off2Y2 = 0;
var g_off2Y3 = 0;

function fncStopHeader2( ObjID1 , ObjID2 , ObjID3 )
{

	sy = document.body.scrollTop;

	//ObjID.style.visibility = 'hidden';

	ObjID1.style.top = sy + g_off2Y1;
	ObjID2.style.top = sy + g_off2Y2;
	ObjID3.style.top = sy + g_off2Y3;

	//ObjID.style.visibility = 'visible';

}
/************************************************************/




/************************************************************/
var g_clickID;                  // 前回のクリック行
var g_lngTrNum;                 // 前回のTRの数
var g_DefaultColor = '';        // 前回のクリック行の色
var g_SelectColor  = '#bbbbbb'; // 選択したときの色
var trClickFlg     = "on";      // クリックカウントフラグ

/************************************************************/
// objID : 		クリック行のオブジェクト

////////// TR選択行の背景色変更関数 //////////
function fncSelectTrColor( objID )
{

	//trClickFlgがoffのときは処理を終了
	if( trClickFlg == "off" ) return false;

	// 選択行すでにある場合に、以前の色に戻す
	if( g_DefaultColor != '')
	{
		g_clickID.style.background = g_DefaultColor;
	}

	// 以前と同じ選択行を選択した場合に、初期化
	if(g_clickID == objID){
		g_clickID      = '';
		g_DefaultColor = '';
	}
	// 選択行の色と場所を保存し、反転
	else
	{
		g_clickID      = objID;
		g_DefaultColor = objID.style.background;
		//選択行を反転
		objID.style.background = g_SelectColor;
	}

	return false;
}


/************************************************************/
// objID : 		クリック行のオブジェクト
// strIdName : 	TRの数値部分は除くID名( tda0 → 'tda' )
// lngTrNum : 	１レコードに使用しているTRの数( rowspanの数 )

////////// TR選択行の背景色変更関数[複数行対応版] //////////
function fncSelectSomeTrColor( objID , strIdName , lngTrNum )
{

	//trClickFlgがoffのときは処理を終了
	if( trClickFlg == "off" ) return false;


	// 選択行すでにある場合に、以前の色に戻す
	if( g_DefaultColor != '')
	{
		for ( i = 0; i < g_lngTrNum; i++ )
		{
			document.getElementById( g_clickID + i ).style.background = g_DefaultColor;
		}
	}

	// 以前と同じ選択行を選択した場合に、初期化
	if( g_clickID == strIdName )
	{
		g_clickID      = '';
		g_lngTrNum     = '';
		g_DefaultColor = '';
	}

	// 選択行の色と場所を保存し、反転
	else
	{
		g_clickID      = strIdName;
		g_lngTrNum     = lngTrNum;
		g_DefaultColor = objID.style.background;

		for ( i = 0; i < g_lngTrNum; i++ )
		{
			document.getElementById( g_clickID + i ).style.background = g_SelectColor;
		}
	}


	return false;
}
/*****************************************************************************/





function fncNoSelectSomeTrColor( objID , strIdName , lngTrNum )
{

	return false;

}



// ADDITION BUTTON
function MasterAddJOff( obj )
{
	obj.src = maddJ1;
}

function MasterAddJOn( obj )
{
	obj.src = maddJ2;
}

function MasterAddEOff( obj )
{
	obj.src = maddE1;
}

function MasterAddEOn( obj )
{
	obj.src = maddE2;
}




// Sort
function SortOn( obj )
{
	obj.style.background = '#bbbbbb'; //6d8aab
}

function SortOff( obj )
{
	obj.style.backgroundColor = '#799797'; /* 6d8aab */
}




//----------------------------------------------------------
// ワークフロー順序マスタでの担当者追加ボタン処理
//----------------------------------------------------------
function fncWhiteAddButton( strMode , obj )
{
	switch( strMode )
	{
		case 'off':
			obj.src = whiteadd1;
			break;

		case 'on':
			obj.src = whiteadd2;
			break;

		default:
			break;
	}
}



//-->