<!--

//------------------------------------------------------------------------------
// グローバル変数定義
//------------------------------------------------------------------------------
var g_saveRecord = new Array(8);	// 明細部を単位とする配列
g_saveRecord[0]  = new Array();		// ４３１　金型償却高
g_saveRecord[1]  = new Array();		// ４３３　金型海外償却高
g_saveRecord[2]  = new Array();		// ４０３　材料ツール仕入高
g_saveRecord[3]  = new Array();		// ４０２　輸入パーツ仕入高
g_saveRecord[4]  = new Array();		// ４０１　材料パーツ仕入高
g_saveRecord[5]  = new Array();		// ４２０　外注加工費
g_saveRecord[6]  = new Array();		// １２２４　チャージ
g_saveRecord[7]  = new Array();		// １２３０　経費

var g_displayNO                               // 現在作業している明細部番号(g_saveRecordの添字)

var g_strStockSubjectCodeName = new Array(8); // 現在作業している明細部名称（仕入科目コード＋仕入科目名）
g_strStockSubjectCodeName[0] = "431  金型償却高";
g_strStockSubjectCodeName[1] = "433  金型海外償却高" ;
g_strStockSubjectCodeName[2] = "403  材料ツール仕入高" ;
g_strStockSubjectCodeName[3] = "402  輸入パーツ仕入高" ;
g_strStockSubjectCodeName[4] = "401  材料パーツ仕入高" ;
g_strStockSubjectCodeName[5] = "420  外注加工費" ;
g_strStockSubjectCodeName[6] = "1224  チャージ" ;
g_strStockSubjectCodeName[7] = "1230  経費" ;

var g_strStockSubjectCode  = new Array(8); // 現在作業している仕入科目コード
g_strStockSubjectCode[0] = "431";  //"４３１　金型償却高
g_strStockSubjectCode[1] = "433";  //金型海外償却高
g_strStockSubjectCode[2] = "403";  // ４０３　材料ツール仕入高
g_strStockSubjectCode[3] = "402";  //４０２　輸入パーツ仕入高
g_strStockSubjectCode[4] = "401";  //４０１　材料パーツ仕入高
g_strStockSubjectCode[5] = "420";  //４２０　外注加工費
g_strStockSubjectCode[6] = "1224"; //１２２４　チャージ
g_strStockSubjectCode[7] = "1230"; //経費

var g_lngSelIndex        = -1;			// 選択行を格納する変数
var g_lngDecimalCutPoint =  2;			// 小数点以下、計算処理ポイント（初期値：小数点以下2桁で処理）
var g_lngCalcCode        =  0;			// 計算方法種別（0:四捨五入）

var g_strJpnCurrencySign    = "\\";			// 日本円通貨記号

var g_sub_all_curSubTotalPrice   = new Array( g_saveRecord.length ); // ヘッダ部の仕入科目ごとの小計の配列
var g_sub_all_lngProductQuantity = new Array( g_saveRecord.length ); // ヘッダ部の仕入科目ごとの計画個数
var g_TotalFixedCost      = 0; // 固定費の小計
var g_TotalFixedQuantity  = 0; // 固定費の計画個数
var g_TotalMemberCost     = 0; // 部材費の小計
var g_TotalMemberQuantity = 0; // 部材費の計画個数

// ---------------------------------------------------------------
/**
* 概要   : 「ヘッダ部」から「明細部」に画面表示を切り替える
* 対象   : 「明細部」があるものすべて
* 備考   : 
*/
// ---------------------------------------------------------------	
function fncDtBlockOpen( displayNO )
{
// 2004.09.29 tomita update
	// 製品サブウィンドウ非表示処理
	fncProductsClose();
// 2004.09.29 tomita update end

	// 作業する明細部番号を格納
	g_displayNO = displayNO; 

	// ヘッダ部を非表示
	//headerBlock.style.display = "none";

	// 明細部を表示
	InputB.style.visibility      = "visible";
	detailBlock.style.visibility = "visible";

	InputA.style.visibility      = "hidden";
	headerBlock.style.visibility = "hidden";


	// 明細部名称をセット(仕入科目プラス仕入科目名称)
	document.DSO.strStockSubjectCodeName.value = g_strStockSubjectCodeName[ g_displayNO ] ;

	//仕入科目から、仕入部品のオプション値を作成
	fncDtGetStockSubjectOption();

	// 入力欄の初期化
	fncDtClearRecord();
}


// ---------------------------------------------------------------
/**
* 概要   : 「明細部」から「ヘッダ部」に画面表示を切り替える
* 対象   : 「明細部」があるものすべて
* 備考   : 
*/
// ---------------------------------------------------------------	
function fncHdBlockOpen()
{
	// 明細部を非表示
	InputB.style.visibility      = "hidden";
	detailBlock.style.visibility = "hidden";

	InputA.style.visibility      = "visible";
	headerBlock.style.visibility = "visible";

	// 小計を再計算
	fncHdSub_all_curSubTotalPrice();
}


// ---------------------------------------------------------------
/**
* 概要   : 仕入科目から、仕入部品のオプション値を作成
* 対象   : 「明細部」があるものすべて
* 備考   : 
*/
// ---------------------------------------------------------------	
function fncDtGetStockSubjectOption()
{
		//仕入科目から、仕入部品のオプション値を作成
		subLoadMasterOption( 'cnStockItemEstimateRegist',
				 document.DSO.lngStockItemCode, 
				 document.DSO.lngStockItemCode,
				 Array(g_strStockSubjectCode[g_displayNO]),
				 window.document.objDataSourceSetting10,
				 10 );
		//上記後、明細行を表示（遅延対策のためparts.tmplに記述）
}


// ---------------------------------------------------------------
/**
* 概要   : 通貨の設定
* 対象   : 「明細部」があるものすべて
* 備考   : ４３３　金型海外償却高と４０２　輸入パーツ仕入高はデフォルトでドルを設定
*          その他は日本円
*/
// ---------------------------------------------------------------	
function fncDtMonetaryUnitCode()
{
	if( g_displayNO == "1" || g_displayNO == "3" )
	{
		// ドルをセット
		document.DSO.lngMonetaryUnitCode.value = "$";
	}
	else
	{
		// 日本円をセット
		document.DSO.lngMonetaryUnitCode.value = "\\";
	}

	// チェック関数用に、通貨記号をグローバル変数にセットする
	fncCheckNumberCurrencySign(document.DSO.lngMonetaryUnitCode.value);

	// 概算レートの計算
	fncDtConversionRate();
}

// ---------------------------------------------------------------
/**
* 概要   : 通貨を選択したら、概算レートに反映
* 対象   : 「明細枠」があるものすべて
*/
// ---------------------------------------------------------------
function fncDtConversionRate()
{
	// 概算レートに、hiddenで吐き出された初期値を設定
	document.DSO.curConversionRate.value = document.PRE_DSO.elements("lngMonetaryUnitCode[" + document.DSO.lngMonetaryUnitCode.value + "]").value;

	// 計画原価の再計算
	fncDtSubTotalPrice();
}


// ---------------------------------------------------------------
/**
* 概要   : 償却フラグの設定
* 対象   : 「明細部」があるものすべて
* 備考   : 
*/
// ---------------------------------------------------------------	
function fncDtPayOffTargetFlagChecked()
{
	if( g_displayNO == "0" || g_displayNO == "1" || g_displayNO == "2" )
	{
		// 固定費の場合には、チェックON
		document.DSO.bytPayOffTargetFlag.checked = true;
	}
	else
	{
		// 部材費の場合には、チェックOFF
		document.DSO.bytPayOffTargetFlag.checked = false;
	}
}


// ---------------------------------------------------------------
/**
* 概要   : パーセント入力フラグの設定
* 対象   : 「明細部」があるものすべて
* 備考   : 
*/
// ---------------------------------------------------------------	
function fncDtPercentInputFlagChecked()
{
	if( document.DSO.bytPercentInputFlag.checked == true )
	{
		// 通貨を選択できなくする
		document.DSO.lngMonetaryUnitCode.disabled = true;
		// 通貨を強制的に日本円に変更
		document.DSO.lngMonetaryUnitCode.value = g_strJpnCurrencySign;
		// 単価を入力できなくする
		document.DSO.curProductPrice.disabled = true;
		// 単価をクリア
		document.DSO.curProductPrice.value = "";
		// 計画率を入力できるようにする
		document.DSO.curProductRate.disabled = false;
		// 計画個数（デフォルト値生産予定数）の設定
		document.DSO.lngProductQuantity.value     = document.HSO.lngProductionQuantity.value;

		// 単価のテキストボックスボーダーの変更
		document.DSO.curProductPrice.style.borderColor = '#cdcdcd';
		// 計画率のテキストボックスボーダーの変更
		document.all.curProductRate.style.borderColor  = '#7f7f7f';
	}
	else
	{
		// 通貨を選択できるようにする
		document.DSO.lngMonetaryUnitCode.disabled = false;
		// 単価を入力できるようにする
		document.DSO.curProductPrice.disabled = false;
		// 計画率を入力できなくする
		document.DSO.curProductRate.disabled = true;

		// 単価のテキストボックスボーダーの変更
		document.DSO.curProductPrice.style.borderColor = '#7f7f7f';
		// 計画率のテキストボックスボーダーの変更
		document.all.curProductRate.style.borderColor  = '#cdcdcd';
	}

	// 計画原価を再計算
	fncDtSubTotalPrice();
}


// ---------------------------------------------------------------
/**
* 概要   : パーセント入力フラグの設定(行を選択した場合)
* 対象   : 「明細部」があるものすべて
* 備考   : 
*/
// ---------------------------------------------------------------	
function fncDtPercentInputFlagCheckedForSentaku()
{
	if( document.DSO.bytPercentInputFlag.checked == true )
	{
		// 通貨を選択できなくする
		document.DSO.lngMonetaryUnitCode.disabled = true;
		// 単価を入力できなくする
		document.DSO.curProductPrice.disabled = true;
		// 計画率を入力できるようにする
		document.DSO.curProductRate.disabled = false;

		// 単価のテキストボックスボーダーの変更
		document.DSO.curProductPrice.style.borderColor = '#cdcdcd';
		// 計画率のテキストボックスボーダーの変更
		document.all.curProductRate.style.borderColor  = '#7f7f7f';
	}
	else
	{
		// 通貨を選択できるようにする
		document.DSO.lngMonetaryUnitCode.disabled = false;
		// 単価を入力できるようにする
		document.DSO.curProductPrice.disabled = false;
		// 計画率を入力できなくする
		document.DSO.curProductRate.disabled = true;

		// 単価のテキストボックスボーダーの変更
		document.DSO.curProductPrice.style.borderColor = '#7f7f7f';
		// 計画率のテキストボックスボーダーの変更
		document.all.curProductRate.style.borderColor  = '#cdcdcd';
	}
}



// ---------------------------------------------------------------
/**
* 概要   : 計画原価
* 対象   : 「明細部」があるものすべて
* 備考   : ％入力しない場合は、計画個数 × 計画単価
*          ％入力の場合は、納価 × 計画個数 × 計画率にて計算する
*/
// ---------------------------------------------------------------	
function fncDtSubTotalPrice()
{
	
	// - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
	// added by k.saito  2005/02/16
	//
	
	// ［納価］を計算対象とする
	curMultiplicationPrice = fncDelCurrencySign(fncDelKannma(document.HSO.curProductPrice.value)) ;
	
	// 401［材料パーツ仕入高］1［証紙］ の場合
	if( g_strStockSubjectCode[ g_displayNO ] == g_strStockSubjectCode[4] && 
		document.DSO.lngStockItemCode.value == '1' )
	{
		// 計算対象の単価を［納価］から［上代］に変更する
		curMultiplicationPrice = fncDelCurrencySign(fncDelKannma(document.HSO.curRetailPrice.value)) ;
	}
	// - - - - - - - - - - - - - - - - - - - - - - - - - - - - -
	
	
	
	// ％入力の場合
	if( document.DSO.bytPercentInputFlag.checked == true )
	{
		// 納価と計画個数と計画率が入力されていなければ処理終了
		if( document.HSO.curProductPrice.value  == "" || 
			document.DSO.lngProductQuantity.value == "" || 
			document.DSO.curProductRate.value  == "" )
		{
			document.DSO.curSubTotalPrice.value = "";
			document.DSO.curSubTotalPriceJP.value ="";
			return false;
		}

		// 納価
//		var nouka        = fncDelCurrencySign(fncDelKannma(document.HSO.curProductPrice.value)) ;
		var nouka        = curMultiplicationPrice ;	// added by k.saito
		
		// 計画個数
		var kaikakukosuu = fncDelKannma(document.DSO.lngProductQuantity.value) ;
		// 計画率 ／ 100
		var kaikakuritsuv= document.DSO.curProductRate.value / 100 ;

		// 納価 × 計画個数 × 計画率
		// ＊日本円として表示
		document.DSO.curSubTotalPrice.value = g_strJpnCurrencySign + " " + fncAddKannma( nouka * kaikakukosuu * kaikakuritsuv );

		// 計画原価日本円をhidden値に保存
		document.DSO.curSubTotalPriceJP.value = nouka * kaikakukosuu * kaikakuritsuv;

	}
	//％入力しない場合
	else
	{
		// 単価と計画個数が入力されていなければ処理終了
		if( document.DSO.curProductPrice.value == "" ||
			document.DSO.lngProductQuantity.value  == "" )
		{
			document.DSO.curSubTotalPrice.value = "";
			document.DSO.curSubTotalPriceJP.value ="";
			return false;
		}

		// 計画個数
		var kaikakukosuu = fncDelKannma(document.DSO.lngProductQuantity.value) ;
		// 計画単価
		var tanka        = fncDelCurrencySign(fncDelKannma(document.DSO.curProductPrice.value)) ;
		// 計画単価の通貨記号を再設定
		document.DSO.curProductPrice.value = fncAddCurrencySign(fncAddKannma( tanka ));

		// 計画原価 ＝ 計画個数 × 計画単価
		document.DSO.curSubTotalPrice.value = fncAddCurrencySign(fncAddKannma( kaikakukosuu * tanka ));

		// 計画原価日本円をhidden値に保存
		document.DSO.curSubTotalPriceJP.value = kaikakukosuu * tanka * document.DSO.curConversionRate.value;
	}
}


// ---------------------------------------------------------------
/**
* 概要   : 修正のために吐き出されたhidden値および登録ボタンを押した後に
*          戻ってきたhidden値を新規の配列に格納
* 対象   : 「明細部」があるものすべて
*/
// ---------------------------------------------------------------
function fncSetAryList()
{

	for( i=0 ; i < g_saveRecord.length ; i++)
	{
		// ループの初期値
		var j = 0;

		while (document.PRE_DSO.elements("aryDitail[" + i + "][" + j + "][lngStockSubjectCode]") != null)
		{
			// フォームPRE_DSOに戻ってきたhidden値を新規の配列に格納
			var aryRecord = fncDtNewAryForReturn( i, j );

			// 配列に格納
			g_saveRecord[i].push(aryRecord);

			j++;
		}
	}

	// 小計を計算
	fncHdSub_all_curSubTotalPrice();

}


// ---------------------------------------------------------------
/**
* 概要   : 明細枠を再表示
* 対象   : 「明細枠」があるものすべて
* param  : preindex : 表示する位置を決定するために使用
* 備考   : 配列「saveRecord」から、明細枠のテーブルを作成し、表示
*/
// ---------------------------------------------------------------
function fncDtDisplay( preindex )
{
	// カラム名を取得
	strTableHtml = fncStrTableHtmlColumns();

	// 一覧を作成
	for( i = 0; i < g_saveRecord[ g_displayNO ].length; i++ )
	{
		strTableHtml = strTableHtml + 
					'<tr class="Lists01" id ="retsu' + i + '" onClick="fncDtSentaku(' + i + ');return false;"' + 'bgcolor="#ffffff"）>' + 
					fncStrTableHtmlRows( i ) + 
					'</tr>';
	}

	strTableHtml = strTableHtml + '</table>';

	// 明細行が選択されていない場合にのみ、表示を一番最後の行にする処理の準備をする
	if( preindex == -1 )
	{
		strTableHtml = strTableHtml + '<a name="enddisplay"></a>';
	}

	// 既存の一覧を作成し直した一覧に書き換える
	DetailList.innerHTML = strTableHtml;

	// 仕入科目小計を再計算
	fncDtSub_all_curSubTotalPrice();

	// 仕入部品にフォーカスを移動
	document.all.lngStockItemCode.focus();

}


// ---------------------------------------------------------------
/**
* 概要   : 明細部の仕入科目小計を算出する
* 対象   : 「明細枠」があるものすべて
*/
// ---------------------------------------------------------------
function fncDtSub_all_curSubTotalPrice()
{

	if( g_saveRecord[ g_displayNO ].length == 0 )
	{
		// 明細行がない場合は仕入科目小計を空白にする
		document.DSO.sub_all_curSubTotalPrice.value = "";
	}
	else
	{
		// 合計
		var AllTotalPrice = 0;
		//明細行の数
		var saveSubRecordLength = g_saveRecord[ g_displayNO ].length;

		for( gyou = 0; gyou < saveSubRecordLength; gyou++ )
		{
			// 固定費の場合
			if( g_displayNO == "0" || g_displayNO == "1" || g_displayNO == "2" )
			{
				// 償却対象のみ加算
				if( g_saveRecord[ g_displayNO ][ gyou ][2] == "true" )
				{
					AllTotalPrice += parseInt( 10000 * g_saveRecord[ g_displayNO ][ gyou ][11] );
				}
			// 部材費の場合
			}
			else
			{
				// 償却対象以外を加算
				if( g_saveRecord[ g_displayNO ][ gyou ][2] == "false" )
				{
					AllTotalPrice += parseInt( 10000 * g_saveRecord[ g_displayNO ][ gyou ][11] );
				}
			}
		}
		AllTotalPrice = AllTotalPrice / 10000 ;

		// 合計を仕入科目小計に反映
		document.DSO.sub_all_curSubTotalPrice.value = AllTotalPrice;
		// 仕入科目小計をフォーマットする
		fncCheckNumber( document.DSO.sub_all_curSubTotalPrice , 2 );
	}
}


// ---------------------------------------------------------------
/**
* 概要   : ヘッダー部の仕入科目小計を算出する
* 対象   : ヘッダー部のonload 時および、明細部からヘッダー部に遷移する場合
*/
// ---------------------------------------------------------------
function fncHdSub_all_curSubTotalPrice()
{
	// 配列の初期化
	for( n=0 ; n < g_saveRecord.length ; n++ )
	{
		g_sub_all_curSubTotalPrice[ n ]  = 0;
		g_sub_all_lngProductQuantity[ n ]= 0;
	}
	g_TotalFixedCost      = 0; // 固定費の小計
	g_TotalFixedQuantity  = 0; // 固定費の計画個数
	g_TotalMemberCost     = 0; // 部材費の小計
	g_TotalMemberQuantity = 0; // 部材費の計画個数

	// 集計処理
	for( i=0 ; i < g_saveRecord.length ; i++)
	{
		for( j=0 ; j < g_saveRecord[i].length ; j++  )
		{
			// 空白行の場合には、スキップ
			if( g_saveRecord[ i ][ j ][11] == 0 || g_saveRecord[ i ][ j ][11] =="" ) continue;

			// 固定費の場合
			if( i==0 || i==1 || i==2 )
			{
				// 償却対象の場合
//				if( g_saveRecord[ i ][ j ][2] == "true" )
//				{
					g_sub_all_curSubTotalPrice[ i ]   += parseInt( g_saveRecord[ i ][ j ][11] );
					g_sub_all_lngProductQuantity[ i ] += parseInt( g_saveRecord[ i ][ j ][5] );

					g_TotalFixedCost      += parseInt( g_saveRecord[ i ][ j ][11] ); // 固定費の小計
					g_TotalFixedQuantity  += parseInt( g_saveRecord[ i ][ j ][5] );  // 固定費の計画個数
//				}
				//償却対象でない場合
//				else
//				{
//					g_TotalMemberCost     += parseInt( g_saveRecord[ i ][ j ][11] ); // 部材費の小計
//					g_TotalMemberQuantity += parseInt( g_saveRecord[ i ][ j ][5] );  // 部材費の計画個数
//				}
			// 部材費の場合
			}
			else
			{
				// 償却対象の場合
				if( g_saveRecord[ i ][ j ][2] == "true" )
				{
					g_TotalFixedCost      += parseInt( g_saveRecord[ i ][ j ][11] ); // 固定費の小計
					g_TotalFixedQuantity  += parseInt( g_saveRecord[ i ][ j ][5] );  // 固定費の計画個数
				}
				//償却対象でない場合
				else
				{
					g_sub_all_curSubTotalPrice[ i ]   += parseInt( g_saveRecord[ i ][ j ][11] );
					//g_sub_all_lngProductQuantity[ i ] += parseInt( g_saveRecord[ i ][ j ][5] );
					g_TotalMemberCost     += parseInt( g_saveRecord[ i ][ j ][11] ); // 部材費の小計
					//g_TotalMemberQuantity += parseInt( g_saveRecord[ i ][ j ][5] );  // 部材費の計画個数
				}
			}
		}
	}

	// 仕入科目ごとの部材費の計画個数の計算
	// ※ 明細部での個数に関係なく、１行以上の明細があれば問答無用で、生産予定数をセット
	for( m=3 ; m < g_saveRecord.length ; m++ )
	{
		if( g_saveRecord[m].length > 0 )
		{
			g_sub_all_lngProductQuantity[ m ] = fncDelKannma( document.HSO.lngProductionQuantity.value ); 
			g_TotalMemberQuantity             = fncDelKannma( document.HSO.lngProductionQuantity.value );  // 部材費の計画個数
		}
	}




	// ヘッダー部に反映
	for( k=0 ; k < g_saveRecord.length ; k++ )
	{
		document.HSO.elements( "g_sub_all_curSubTotalPrice[" + k + "]" ).value   = g_strJpnCurrencySign + " " + fncAddKannma( g_sub_all_curSubTotalPrice[ k ] );
		document.HSO.elements( "g_sub_all_lngProductQuantity[" + k + "]" ).value = fncAddKannma( g_sub_all_lngProductQuantity[ k ] );
	}
	document.HSO.elements("g_TotalFixedCost").value      = g_strJpnCurrencySign + " " + fncAddKannma( g_TotalFixedCost );      // 固定費の小計
	document.HSO.elements("g_TotalFixedQuantity").value  = fncAddKannma( g_TotalFixedQuantity );                               // 固定費の計画個数
	document.HSO.elements("g_TotalMemberCost").value     = g_strJpnCurrencySign + " " + fncAddKannma( g_TotalMemberCost );     // 部材費の小計
	document.HSO.elements("g_TotalMemberQuantity").value = fncAddKannma( g_TotalMemberQuantity );                              // 部材費の計画個数
}


// ---------------------------------------------------------------
/**
* 概要   : 「明細枠」に「入力枠」の内容を追加
* 対象   : 「明細枠」があるものすべて
* 備考   : 「入力枠」の値を配列「aryRecord」に格納し、それを配列「saveRecord」に格納。
*           このとき、明細行が選択されていれば、選択された配列の上に追加し、されていなければ、
*           最後尾に追加。
*           その後、関数「fncDtDisplay()」を呼び出し、明細枠を再表示。
*/
// ---------------------------------------------------------------
function fncDtAddRecord()
{
	//入力データのチェック
	var addFlg = fncDtAddCheck();

	if( addFlg == false ) return false;

	//入力枠の値を新規の配列に格納
	var aryRecord = fncDtNewAry();

	//明細行が選択されていない場合
	if ( g_lngSelIndex == -1)
	{
		//グローバル配列の最後に追加
		g_saveRecord[ g_displayNO ].push(aryRecord);

		//空白行を追加したときに空白行を選択するためにもとのインデックスを保持する
		var preindex = -1;
	}
	//明細行が選択されている場合
	else
	{
		//選択された配列の上に、新規の配列を追加する
		saveRecordLength = parseInt(g_saveRecord[ g_displayNO ].length); 
		saveRecordLeft  = g_saveRecord[ g_displayNO ].slice( 0 , g_lngSelIndex );
		saveRecordRigft = g_saveRecord[ g_displayNO ].slice( g_lngSelIndex , saveRecordLength );
		g_saveRecord[ g_displayNO ]      = saveRecordLeft;
		g_saveRecord[ g_displayNO ].push(aryRecord);
		g_saveRecord[ g_displayNO ]      = g_saveRecord[ g_displayNO ].concat(saveRecordRigft);

		//空白行を追加したときに空白行を選択するためにもとのインデックスを保持する
		var preindex = g_lngSelIndex;

		//インデックスを初期化
		g_lngSelIndex      = -1;
	}

	//明細枠を再表示
	fncDtDisplay( preindex );

	//空行追加の場合には、追加した空白行を選択(計画原価が空かどうかで判断)
	if( aryRecord[8] == "" )
	{
		//グローバル配列の長さ
		saveRecordLength = g_saveRecord[ g_displayNO ].length;

		//インデックスに追加した空白行の配列番号をセット
		//選択された行がある場合、その上を選択
		if( preindex != -1 )
		{
			g_lngSelIndex = preindex;
		}
		//選択された行がない場合、最後を選択
		else
		{
			g_lngSelIndex = parseInt(saveRecordLength) - 1 ;
		}

		//追加した空白行を反転させる
		document.getElementById("retsu" + g_lngSelIndex).style.backgroundColor="#bbbbbb";
	}

	//明細枠の最後に移動する
	//ディレクトリ構成がきまったら変更すること
	{
		window.location.href = "#enddisplay";
	}
}


// ---------------------------------------------------------------
/**
* 概要   : 選択した行を削除
* 対象   : 「明細枠」があるものすべて(行削除ボタン押下後)
* 備考   : 選択行を除いた配列を作成。
*          その後、関数「fncDtDisplay()」を呼び出し、明細枠を再表示。
* 注意   : 行が選択されていない場合には、エラーメッセージを出力。
*/
// ---------------------------------------------------------------
function fncDtDelRecord()
{
	//明細行が選択されていない場合、アラートを出して処理終了
	if( g_lngSelIndex == -1 )
	{
		alert("明細行を選択してください");
		return false;
	}

	if( res = confirm("選択行を削除してもよろしいですか？") )
	{

		saveRecordLength = parseInt(g_saveRecord[ g_displayNO ].length);
	
		saveRecordLeft  = g_saveRecord[ g_displayNO ].slice( 0, g_lngSelIndex );
		saveRecordRigft = g_saveRecord[ g_displayNO ].slice( g_lngSelIndex + 1, saveRecordLength );
		g_saveRecord[ g_displayNO ] = new Array();
		g_saveRecord[ g_displayNO ] = g_saveRecord[ g_displayNO ].concat( saveRecordLeft, saveRecordRigft );
	
		g_lngSelIndex = -1;
	
		//明細枠を再表示
		fncDtDisplay();
	}
}


// ---------------------------------------------------------------
/**
* 概要   : 入力枠の値を選択行と置き換える
* 対象   : 「明細枠」があるものすべて (行確定ボタン押下後に呼ばれる)
* 備考   : 入力枠の値を選択行と置き換える。
*          その後、関数「fncDtDisplay()」を呼び出し、明細枠を再表示。
* 注意    :行が選択されていない場合には、ヘッダ部分にエラーメッセージを出力
*/
// ---------------------------------------------------------------
function fncDtCommitRecord()
{
	//明細行が選択されている場合
	if( g_lngSelIndex != -1)
	{
		//入力チェック
		if( fncDtAddCheck() == false) return false;

		fncDtReplaceAry();

		//インデックスを初期化
		g_lngSelIndex = -1;

		//明細枠を再表示
		fncDtDisplay();
	}
	//明細行が選択されていない場合
	else
	{
		alert("明細行が選択されていません");
	}
}


// ---------------------------------------------------------------
/**
* 概要   : クリアボタンが押されたときに処理
* 対象   : 「明細枠」があるものすべて
* 備考   : 
*/
// ---------------------------------------------------------------
function fncDtClearRecord()
{
	document.DSO.lngStockItemCode.value       = -1;    // 仕入部品
	document.DSO.lngCustomerCompanyCode.value        = "";    // 仕入先
	document.DSO.strCompanyDisplayName.value        = "";    // 仕入先名
	document.DSO.bytPercentInputFlag.checked  = false; // パーセント入力フラグ
	document.DSO.curProductRate.value         = "";    // 計画率
	document.DSO.curProductPrice.value        = "";    // 単価（―）
	document.DSO.curSubTotalPrice.value       = "";    // 計画原価
	document.DSO.strNote.value                = "";    // 備考
	document.DSO.curSubTotalPriceJP.value     = "";    // 計画原価日本円
	document.DSO.curConversionRate.value      = "";    // 換算レート

	// 計画率を入力できなくする
	document.DSO.curProductRate.disabled      = false; // 計画率

	//既存の選択行があった場合には、その行の反転を解除
	if( g_lngSelIndex != -1 )
	{
		document.getElementById("retsu" + g_lngSelIndex).style.backgroundColor="#ffffff";
	}

	// 選択行をクリア
	g_lngSelIndex        = -1;

	// 通貨の設定
	fncDtMonetaryUnitCode();

	// 償却フラグの設定
	fncDtPayOffTargetFlagChecked();

	// ％入力の設定
	fncDtPercentInputFlagChecked();

	// 計画個数（デフォルト値生産予定数）の設定
	// 固定費の場合
	if( g_displayNO == "0" || g_displayNO == "1" || g_displayNO == "2" )
	{
		document.DSO.lngProductQuantity.value = "";
	// 部材費の場合
	}
	else
	{
		document.DSO.lngProductQuantity.value = document.HSO.lngProductionQuantity.value;
	}
}


// ---------------------------------------------------------------
/**
* 概要   : 明細行の選択時の処理
* 対象   : 「明細枠」があるものすべて
* 備考   : 明細行を選択するときに、すでに選択されている行があった場合は、その行の反転を解除する。
*          既存の選択されている行をもう一度押した場合には、g_lngSelIndexを初期化する。
*          それ以外の場合には、選択行の値を、入力枠に反映させる。
* 注意   : 明細行が選択されている場合で、選択行を変更しようとした場合には、入力枠に変更がないかチェックし、
*          変更があれば、メッセージを出力。
*/
// ---------------------------------------------------------------
function fncDtSentaku(i)
{

	// 明細行が選択されている場合のチェックフラグ(入力枠にエラーがあるとエラーになる)
	var checkFlg = true;

	// 明細行が選択されている場合
	if( g_lngSelIndex != -1 )
	{
		//入力枠に変更がないかチェック
		checkFlg = fncDtCheck();
	}

	if( checkFlg == false ) return false;


	//既存の選択行があった場合には、その行の反転を解除
	if( g_lngSelIndex != -1 )
	{
		document.getElementById("retsu" + g_lngSelIndex).style.backgroundColor="#ffffff";
	}

	//以前の選択行をもう一度クリックした場合
	if (g_lngSelIndex == i)
	{
		//インデックスを初期化
		g_lngSelIndex = -1;
	}
	//以前と違う選択行をクリックした場合
	else
	{
		//インデックスに選択行の配列番号をセット
		g_lngSelIndex = i;

		//「選択行」を反転させる
		document.getElementById("retsu" + g_lngSelIndex).style.backgroundColor="#bbbbbb";

		//「入力枠」に選択行を反映
		fncDtReplaceInput();

		// ％入力を初期化
		fncDtPercentInputFlagCheckedForSentaku();

	}
}


// ---------------------------------------------------------------
/**
* 概要   : 入力枠と選択行の差異をチェックし、違いがあれば、確認ダイアログを表示
* 対象   : 「明細枠」があるものすべて
* @return : [Boolean型] 選択行を移動してもよい場合は、true、移動しない場合は、false
*/
// ---------------------------------------------------------------
function fncDtCheck()
{
	//「入力枠」の値を配列にセット
	var aryRecord = fncDtNewAry();

	for( i = 1; i < aryRecord.length ; i++ )
	{
		//入力枠と選択行の比較
		if( aryRecord[i] != g_saveRecord[g_displayNO][g_lngSelIndex][i] )
		{

//デバックy用
//alert("変更された配列番号 : " + j + "\n" +
//	  "「入力枠」の値 : " + aryRecord[j] + "\n" +
//	  "「明細行」の値 : " + saveRecord[g_lngSelIndex][j]);

			if( res = confirm("変更箇所があります。変更してもよろしいですか？") )
			{
				//入力チェック
				if( fncDtAddCheck() )
				{
					//入力枠の値を選択行と置き換え
					fncDtReplaceAry();
					//明細枠を再表示
					fncDtDisplay();

					return true;
				}
				else
				{
					return false;
				}

			}
			break;
		}
	}
	return true;
}


// ---------------------------------------------------------------
/**
* 概要   : 追加ボタンを押したときの値のチェック
* 対象   : 「明細枠」があるものすべて
* 注意   : 問題があればアラートを出す
*/
// ---------------------------------------------------------------
function fncDtAddCheck()
{
	//値がすべてからだったら、空行を追加できる
	if( (document.DSO.lngStockItemCode.value == "0"  ||         // 仕入部品が0
		document.DSO.lngStockItemCode.selectedIndex  == -1) &&  // または未選択
		document.DSO.lngCustomerCompanyCode.value == "" )       // 仕入先が未入力

		// パーセント入力フラグがtrueのとき
		if( document.DSO.bytPercentInputFlag.checked == true )
		{
			if( document.DSO.curProductRate.value == "" ) //計画率が未入力
			{
				return true;
			}
		}
		// パーセント入力フラグがfalseのとき
		else
		{
		
			if( document.DSO.curProductPrice.value == "" ) //単価が未入力
			{
				return true;
			}
		}


	// エラーがあった場合にメッセ−ジを詰め込む変数
	var alertList = "";

	// 仕入部品が0または未選択だった場合
	if( document.DSO.lngStockItemCode.value == "0" || 
		document.DSO.lngStockItemCode.selectedIndex  == -1 )
	{
		alertList += "仕入部品を選択してください!\n";
	}
//========================================================================================
//仕入先は必須項目にするby高　05/02/17
	// 仕入先コードが未入力だった場合
	if( document.DSO.lngCustomerCompanyCode.value == "")
	//デフォルト入力された403の6検査費用と1224の1と1230の1は除く
           {  if(  g_strStockSubjectCode[ g_displayNO ] ==  g_strStockSubjectCode[2] && document.DSO.lngStockItemCode.value == '6' || 
                   g_strStockSubjectCode[ g_displayNO ] ==  g_strStockSubjectCode[6] && document.DSO.lngStockItemCode.value == '1' ||
                   g_strStockSubjectCode[ g_displayNO ] ==  g_strStockSubjectCode[7] && document.DSO.lngStockItemCode.value == '1' )
            {  }  else{    
　　　		alertList += "仕入先を入力してください!\n";
	              }
          }
//========================================================================================

	// 仕入先コードが不正だった場合
	if( isNaN(document.DSO.lngCustomerCompanyCode.value) )
	{
		alertList += "仕入先コードの値が不正です!\n";
	}

	// 計画個数が未入力だった場合
	if( document.DSO.lngProductQuantity.value == "" )
	{
		alertList += "計画個数を入力してください!\n";
	}
	//計画個数の値が不正だった場合
	if( isNaN(fncDelKannma(document.DSO.lngProductQuantity.value)) )
	{
		alertList += "計画個数の値が不正です!\n";
	}

	// パーセント入力フラグがtrueのとき
	if( document.DSO.bytPercentInputFlag.checked == true )
	{
		//計画率が未入力
		if( document.DSO.curProductRate.value == "" )
		{
			alertList += "計画率を入力してください!\n";
		}
		//計画率が不正だった場合
		if( isNaN(document.DSO.curProductRate.value) )
		{
			alertList += "計画率の値が不正です!\n";
		}
	}
	// パーセント入力フラグがfalseのとき
	else
	{
		//単価が未入力
		if( document.DSO.curProductPrice.value == "" )
		{
			alertList += "単価を入力してください!\n";
		}
		//単価が不正だった場合
		if( isNaN(fncDelKannma(fncDelCurrencySign(document.DSO.curProductPrice.value))) )
		{
			alertList += "単価の値が不正です!\n";
		}
	}


	//エラーがあったらメッセージを出力
	if( alertList != "" )
	{
		alert(alertList);
		return false;
	}

	return true;
}


// ---------------------------------------------------------------
/**
* 概要    : 明細枠テーブルの列名を作成
* 対象    : 「明細枠」があるものすべて
* @return : strTableHtml, [String型], 明細枠の列名
*/
// ---------------------------------------------------------------
function fncStrTableHtmlColumns()
{
	if( lngLanguageCode == 1 )
	{
		arytxt = [ '仕入科目', '仕入部品', '償却対象', '仕入先', '計画個数', '単価/計画率', '計画原価', '備考' ];
	}
	if( lngLanguageCode == 0 )
	{
		arytxt = [ 'Stock subject', 'Stock item', 'Amortized', 'Supplier', 'Plan Qty', 'Price', 'Plan estimate', 'Remark' ];
	}

	strTableHtml ='<table width="100%" cellpadding="0" cellspacing="1" border="0"' + 
				  'bgcolor="#6f8180"><tr class="TrSegs">' + 
				  '<td id="dlStockSubject" nowrap>' + arytxt[0] + '</td>' +
				  '<td id="dlStockItem"    nowrap>' + arytxt[1] + '</td>' +
				  '<td id="dlAmortized"    nowrap>' + arytxt[2] + '</td>' +
				  '<td id="dlSupplier"     nowrap>' + arytxt[3] + '</td>' +
				  '<td id="dlPlanQty"      nowrap>' + arytxt[4] + '</td>' +
				  '<td id="dlprice"        nowrap>' + arytxt[5] + '</td>' +
				  '<td id="dlPlanEstimate" nowrap>' + arytxt[6] + '</td>' +
				  '<td id="dlRemark"       nowrap>' + arytxt[7] + '</td>' +
				  '</tr>';

	return strTableHtml;
}


// ---------------------------------------------------------------
/**
* 概要    : 明細枠テーブルの行を作成
* 対象    : 「明細枠」があるものすべて
* @return : strTableHtml, [String型], 明細枠の内容
*/
// ---------------------------------------------------------------
function fncStrTableHtmlRows(i)
{
	// 償却対象を表示用に変換
	if( g_saveRecord[g_displayNO][i][2] == "true" )
	{
		var syoukyakuDSP = "○";
	}
	else
	{
		var syoukyakuDSP = "";
	}

	// ％入力のチェック状態により単価か計画率の表示を切り替える
	if( g_saveRecord[g_displayNO][i][4] == "true" )
	{
		// 計画率の計算 100掛ける
		if( g_saveRecord[ g_displayNO ][ i ][6] == "" )
		{
			var tankaDSP = ""; // 計画率
		}
		else
		{
			var tankaDSP = ( g_saveRecord[ g_displayNO ][ i ][6] * 100 ) + " %&nbsp;" // 計画率
		}
	}
	else
	{
		var tankaDSP = fncAddCurrencySignForSentaku( g_saveRecord[g_displayNO][i][10] , fncAddKannma(g_saveRecord[g_displayNO][i][7]) ) + "&nbsp;"; // 単価
	}

	// 仕入部品を表示用（コード + "　"+ 名称 )にするため、optionのvalue値からtext値を無理やり取得
	if( g_saveRecord[g_displayNO][i][1] != "" )
	{
		for( optionNo = 0 ; optionNo < document.DSO.lngStockItemCode.length ; optionNo++ )
		{
			if( g_saveRecord[g_displayNO][i][1] == document.DSO.lngStockItemCode.options[ optionNo ].value )
			{
				var stockItemDSP = document.DSO.lngStockItemCode.options[ optionNo ].text;
				break;
			}
		}
	}
	else
	{
		var stockItemDSP = "";
	}


	// 仕入先を表示用（コード + 名称 ）に変更
	if( g_saveRecord[g_displayNO][i][3] != "" )
	{
		var customerCompanyDSP = g_saveRecord[g_displayNO][i][3] + " " + g_saveRecord[g_displayNO][i][13];
	}
	else
	{
		var customerCompanyDSP = "";
	}

	strTableHtml ='<td  nowrap>'                    + g_strStockSubjectCodeName[g_displayNO]  +        // 仕入科目
				  '</td><td nowrap>'                + stockItemDSP  +                                  // 仕入部品
				  '</td><td align="center" nowrap>' + syoukyakuDSP  +                                  // 償却対象
				  '</td><td nowrap>'                + customerCompanyDSP  +                            // 仕入先
				  '</td><td align="center" nowrap>' + fncAddKannma(g_saveRecord[g_displayNO][i][5])  + // 計画個数
				  '</td><td align="right" nowrap>'  + tankaDSP +                                       // 単価 or 計画率
				  '</td><td align="right" nowrap>'  + fncAddCurrencySignForSentaku( g_saveRecord[g_displayNO][i][10] , fncAddKannma(g_saveRecord[g_displayNO][i][8]) ) + "&nbsp;" +  // 計画原価
				  '</td><td nowrap>'                + g_saveRecord[g_displayNO][i][9] +                // 備考
				  '</td>'

	return strTableHtml;
}


// ---------------------------------------------------------------
/**
* 概要   : 修正のために吐き出されたhidden値および登録ボタンを押した後に
*          戻ってきたhidden値を新規の配列に格納
* 対象   : 「明細部」があるものすべて
*/
// ---------------------------------------------------------------
function fncDtNewAryForReturn( i , j )
{
	var aryRecord = new Array();
	aryRecord[0]  = document.PRE_DSO.elements("aryDitail[" + i + "][" + j + "][lngStockSubjectCode]").value;       // 仕入科目
	aryRecord[1]  = document.PRE_DSO.elements("aryDitail[" + i + "][" + j + "][lngStockItemCode]").value;          // 仕入部品
	aryRecord[2]  = document.PRE_DSO.elements("aryDitail[" + i + "][" + j + "][bytPayOffTargetFlag]").value;       // 償却対象
	aryRecord[3]  = document.PRE_DSO.elements("aryDitail[" + i + "][" + j + "][lngCustomerCompanyCode]").value;    // 仕入先
	aryRecord[4]  = document.PRE_DSO.elements("aryDitail[" + i + "][" + j + "][bytPercentInputFlag]").value;       // パーセント入力フラグ
	aryRecord[5]  = document.PRE_DSO.elements("aryDitail[" + i + "][" + j + "][lngProductQuantity]").value;        // 計画個数
	aryRecord[6]  = document.PRE_DSO.elements("aryDitail[" + i + "][" + j + "][curProductRate]").value;            // 計画率
	aryRecord[7]  = document.PRE_DSO.elements("aryDitail[" + i + "][" + j + "][curProductPrice]").value;           // 単価（―）
	aryRecord[8]  = document.PRE_DSO.elements("aryDitail[" + i + "][" + j + "][curSubTotalPrice]").value;          // 計画原価
	aryRecord[9]  = document.PRE_DSO.elements("aryDitail[" + i + "][" + j + "][strNote]").value;                   // 備考
	aryRecord[10] = document.PRE_DSO.elements("aryDitail[" + i + "][" + j + "][lngMonetaryUnitCode]").value;       // 通貨
	aryRecord[11] = document.PRE_DSO.elements("aryDitail[" + i + "][" + j + "][curSubTotalPriceJP]").value;        // 計画原価日本円
	aryRecord[12] = document.PRE_DSO.elements("aryDitail[" + i + "][" + j + "][curConversionRate]").value;         // 換算レート
	aryRecord[13] = document.PRE_DSO.elements("aryDitail[" + i + "][" + j + "][strCompanyDisplayName]").value;     // 仕入先名
	return aryRecord;
}


// ---------------------------------------------------------------
/**
* 概要    : 入力枠の値を新規の配列に格納
* 対象    : 「明細枠」があるものすべて
* @retrun : aryRecord, [配列型], 新規の配列
*/
// ---------------------------------------------------------------
function fncDtNewAry()
{
	var aryRecord = new Array();

	aryRecord[0]   = g_strStockSubjectCode[ g_displayNO ];      // 仕入科目コード
	aryRecord[1]   = document.DSO.lngStockItemCode.value;       // 仕入部品

	if( document.DSO.bytPayOffTargetFlag.checked == true )
	{
		aryRecord[2]   = "true";  // 償却対象
	}
	else
	{
		aryRecord[2]   = "false";  // 償却対象
	}

	aryRecord[3]   = document.DSO.lngCustomerCompanyCode.value; // 仕入先

	if( document.DSO.bytPercentInputFlag.checked == true )
	{
		aryRecord[4]   = "true";  // パーセント入力フラグ
	}
	else
	{
		aryRecord[4]   = "false";  // パーセント入力フラグ
	}

	aryRecord[5]   = fncDelKannma(document.DSO.lngProductQuantity.value);                  // 計画個数

	// 計画率の計算 100で割る
	if( document.DSO.curProductRate.value == "" )
	{
		aryRecord[6] = "";                                                                 // 計画率
	}
	else
	{
		aryRecord[6]   = document.DSO.curProductRate.value / 100;                          // 計画率
	}

	aryRecord[7]   = fncDelCurrencySign(fncDelKannma(document.DSO.curProductPrice.value)); // 単価（―）
	aryRecord[8]   = fncDelCurrencySign(fncDelKannma(document.DSO.curSubTotalPrice.value));// 計画原価
	aryRecord[9]   = fncCheckReplaceString(document.DSO.strNote.value);                    // 備考
	aryRecord[10]  = document.DSO.lngMonetaryUnitCode.value;                               // 通貨
	aryRecord[11]  = document.DSO.curSubTotalPriceJP.value;                                // 計画原価日本円
	aryRecord[12]  = document.DSO.curConversionRate.value;                                 // 換算レート
	aryRecord[13]  = document.DSO.strCompanyDisplayName.value;                             // 仕入先名

	return aryRecord;
}


// ---------------------------------------------------------------
/**
* 概要   : 入力枠の値を選択行と置き換え
* 対象   : 「明細枠」があるものすべて
*/
// ---------------------------------------------------------------
function fncDtReplaceAry()
{
	g_saveRecord[ g_displayNO ][ g_lngSelIndex ][1]  = document.DSO.lngStockItemCode.value ;       // 仕入部品

	if( document.DSO.bytPayOffTargetFlag.checked == true )
	{
		g_saveRecord[ g_displayNO ][ g_lngSelIndex ][2]  = "true" ;  // 償却対象
	}
	else
	{
		g_saveRecord[ g_displayNO ][ g_lngSelIndex ][2]  = "false";  // 償却対象
	}

	g_saveRecord[ g_displayNO ][ g_lngSelIndex ][3]  = document.DSO.lngCustomerCompanyCode.value ; // 仕入先

	if( document.DSO.bytPercentInputFlag.checked == true )
	{
		g_saveRecord[ g_displayNO ][ g_lngSelIndex ][4]  = "true" ;  // パーセント入力フラグ
	}
	else
	{
		g_saveRecord[ g_displayNO ][ g_lngSelIndex ][4]  = "false";  // パーセント入力フラグ
	}

	g_saveRecord[ g_displayNO ][ g_lngSelIndex ][5]  = fncDelKannma(document.DSO.lngProductQuantity.value) ; // 計画個数

	// 計画率の計算 100で割る
	if( document.DSO.curProductRate.value == "" )
	{
		g_saveRecord[ g_displayNO ][ g_lngSelIndex ][6] = ""; // 計画率
	}
	else
	{
		g_saveRecord[ g_displayNO ][ g_lngSelIndex ][6] = document.DSO.curProductRate.value / 100; // 計画率
	}

	g_saveRecord[ g_displayNO ][ g_lngSelIndex ][7]  = fncDelCurrencySign(fncDelKannma(document.DSO.curProductPrice.value)) ;  // 単価（―）
	g_saveRecord[ g_displayNO ][ g_lngSelIndex ][8]  = fncDelCurrencySign(fncDelKannma(document.DSO.curSubTotalPrice.value)) ; // 計画原価
	g_saveRecord[ g_displayNO ][ g_lngSelIndex ][9]  = document.DSO.strNote.value ;                // 備考
	g_saveRecord[ g_displayNO ][ g_lngSelIndex ][10] = document.DSO.lngMonetaryUnitCode.value ;    // 通貨
	g_saveRecord[ g_displayNO ][ g_lngSelIndex ][11] = document.DSO.curSubTotalPriceJP.value ;     // 計画原価日本円
	g_saveRecord[ g_displayNO ][ g_lngSelIndex ][12] = document.DSO.curConversionRate.value ;      // 換算レート
	g_saveRecord[ g_displayNO ][ g_lngSelIndex ][13] = document.DSO.strCompanyDisplayName.value ;  // 仕入先名
}


// ---------------------------------------------------------------
/**
* 概要   : 入力枠に選択行を反映
* 対象   : 「明細枠」があるものすべて
*/
// ---------------------------------------------------------------
function fncDtReplaceInput()
{
	document.DSO.lngStockItemCode.value       = g_saveRecord[ g_displayNO ][ g_lngSelIndex ][1];  // 仕入部品

	if( g_saveRecord[ g_displayNO ][ g_lngSelIndex ][2] == "true" )
	{
		document.DSO.bytPayOffTargetFlag.checked  = true;  // 償却対象
	}
	else
	{
		document.DSO.bytPayOffTargetFlag.checked  = false;  // 償却対象
	}

	document.DSO.lngCustomerCompanyCode.value = g_saveRecord[ g_displayNO ][ g_lngSelIndex ][3];  // 仕入先

	if( g_saveRecord[ g_displayNO ][ g_lngSelIndex ][4] == "true" )
	{
		document.DSO.bytPercentInputFlag.checked  = true;  // パーセント入力フラグ
	}
	else
	{
		document.DSO.bytPercentInputFlag.checked  = false;  // パーセント入力フラグ
	}
	document.DSO.lngProductQuantity.value     = fncAddKannma(g_saveRecord[ g_displayNO ][ g_lngSelIndex ][5]); // 計画個数

	// 計画率の計算 100掛ける
	if( g_saveRecord[ g_displayNO ][ g_lngSelIndex ][6] == "" )
	{
		document.DSO.curProductRate.value = ""; // 計画率
	}
	else
	{
		document.DSO.curProductRate.value  = g_saveRecord[ g_displayNO ][ g_lngSelIndex ][6] * 100;  // 計画率
	}

	document.DSO.curProductPrice.value        = fncAddCurrencySignForSentaku( g_saveRecord[ g_displayNO ][ g_lngSelIndex ][10] , fncAddKannma(g_saveRecord[ g_displayNO ][ g_lngSelIndex ][7]) ); // 単価（―）
	document.DSO.curSubTotalPrice.value       = fncAddCurrencySignForSentaku( g_saveRecord[ g_displayNO ][ g_lngSelIndex ][10] , fncAddKannma(g_saveRecord[ g_displayNO ][ g_lngSelIndex ][8]) ); // 計画原価
	document.DSO.strNote.value                = fncCheckReplaceStringBack(g_saveRecord[ g_displayNO ][ g_lngSelIndex ][9]);        // 備考
	document.DSO.lngMonetaryUnitCode.value    = g_saveRecord[ g_displayNO ][ g_lngSelIndex ][10]; // 通貨
	document.DSO.curSubTotalPriceJP.value     = g_saveRecord[ g_displayNO ][ g_lngSelIndex ][11]; // 計画原価日本円
	document.DSO.curConversionRate.value      = g_saveRecord[ g_displayNO ][ g_lngSelIndex ][12]; // 換算レート
	document.DSO.strCompanyDisplayName.value  = g_saveRecord[ g_displayNO ][ g_lngSelIndex ][13];  // 仕入先名
}


// ---------------------------------------------------------------
/**
* 概要   : 明細部のデータ(g_saveRecord)を登録するためのデータに変換
* 対象   : 「明細部」があるものすべて
* return : strHiddenHtml, [string型], すべて明細部の内容をhiddenに置き換えて吐き出す
*/
// ---------------------------------------------------------------
function fncDtHiddenHtml()
{
	var strHiddenHtml = "";

	for( i=0 ; i < g_saveRecord.length ; i++)
	{
		// ループの初期値
		var j = 0;

		for(j=0; j< g_saveRecord[i].length; j++)
		{
			//空行チェック
			if (g_saveRecord[i][j][8] == "") continue; // 計画原価が空白だったら空白行とみなし出力しない
	
			strHiddenHtml += "<input type='hidden' name='aryDitail[" + i + "][" + j + "][lngStockSubjectCode]'    value='" + g_saveRecord[i][j][0]  + "' >\n"  + // 仕入科目
							 "<input type='hidden' name='aryDitail[" + i + "][" + j + "][lngStockItemCode]'       value='" + g_saveRecord[i][j][1]  + "' >\n"  + // 仕入部品
							 "<input type='hidden' name='aryDitail[" + i + "][" + j + "][bytPayOffTargetFlag]'    value='" + g_saveRecord[i][j][2]  + "' >\n"  + // 償却対象
							 "<input type='hidden' name='aryDitail[" + i + "][" + j + "][lngCustomerCompanyCode]' value='" + g_saveRecord[i][j][3]  + "' >\n"  + // 仕入先
							 "<input type='hidden' name='aryDitail[" + i + "][" + j + "][bytPercentInputFlag]'    value='" + g_saveRecord[i][j][4]  + "' >\n"  + // パーセント入力フラグ
							 "<input type='hidden' name='aryDitail[" + i + "][" + j + "][lngProductQuantity]'     value='" + g_saveRecord[i][j][5]  + "' >\n"  + // 計画個数
							 "<input type='hidden' name='aryDitail[" + i + "][" + j + "][curProductRate]'         value='" + g_saveRecord[i][j][6]  + "' >\n"  + // 計画率
							 "<input type='hidden' name='aryDitail[" + i + "][" + j + "][curProductPrice]'        value='" + g_saveRecord[i][j][7]  + "' >\n"  + // 単価（―）
							 "<input type='hidden' name='aryDitail[" + i + "][" + j + "][curSubTotalPrice]'       value='" + g_saveRecord[i][j][8]  + "' >\n"  + // 計画原価
							 "<input type='hidden' name='aryDitail[" + i + "][" + j + "][strNote]'                value='" + g_saveRecord[i][j][9]  + "' >\n"  + // 備考
							 "<input type='hidden' name='aryDitail[" + i + "][" + j + "][lngMonetaryUnitCode]'    value='" + g_saveRecord[i][j][10] + "' >\n"  + // 通貨
							 "<input type='hidden' name='aryDitail[" + i + "][" + j + "][curSubTotalPriceJP]'     value='" + g_saveRecord[i][j][11] + "' >\n"  + // 計画原価日本円
							 "<input type='hidden' name='aryDitail[" + i + "][" + j + "][curConversionRate]'      value='" + g_saveRecord[i][j][12] + "' >\n"  + // 換算レート
							 "<input type='hidden' name='aryDitail[" + i + "][" + j + "][strCompanyDisplayName]'  value='" + g_saveRecord[i][j][13] + "' >\n";   // 仕入先名
		}
	}
	return strHiddenHtml;
}


// ---------------------------------------------------------------
/**
* 概要   : 登録ボタンを押したときに、header欄に、明細枠のデータをhiddenに吐き出してからサブミット
* 対象   : Header部 登録ボタン
* 備考   : 
*/
// ---------------------------------------------------------------
function fncDtRegistRecord()
{
		// 吐き出すHTMLを格納する変数
		var strHiddenHtml = "";

		// 明細部の配列(g_saveRecord)をhtmlに変換
		strHiddenHtml += fncDtHiddenHtml();
	
		// フォームHSOに明細部の配列データを渡す
		DtHiddenRecord.innerHTML = strHiddenHtml;

//デバック用
//alert(strHiddenHtml);

		//フォームHSOをサブミット
		document.HSO.submit();
}


// ---------------------------------------------------------------
/**
* 概要   : 登録ボタンを押したときに、header欄に、明細枠のデータをhiddenに吐き出す
* 対象   : Header部 登録ボタン
* 備考   : 
*/
// ---------------------------------------------------------------
function fncDtRegistRecordNoSubmit()
{
		// 吐き出すHTMLを格納する変数
		var strHiddenHtml = "";

		// 明細部の配列(g_saveRecord)をhtmlに変換
		strHiddenHtml += fncDtHiddenHtml();
	
		// フォームHSOに明細部の配列データを渡す
		DtHiddenRecord.innerHTML = strHiddenHtml;

//デバック用
//alert(strHiddenHtml);
}


// ---------------------------------------------------------------
/**
* 概要     : カンマをとる
* 対象     : すべて
* @param   : num, [string型], カンマを取りたい値
* @return  : str, [string型], カンマを取り除いた値
*/
// ---------------------------------------------------------------
function fncDelKannma( num )
{

	var str = num.replace(/,/g,"");

	return str;
}


// ---------------------------------------------------------------
/**
* 概要     : カンマを付ける
* 対象     : すべて
* @param   : num, [string型], カンマを付けたい値
* @return  : str, [string型], カンマ付の値
*/
// ---------------------------------------------------------------
function fncAddKannma(num)
{

	var str = num.toString();
	var tmpStr = "";

	while( str != (tmpStr = str.replace(/^([+-]?\d+)(\d\d\d)/,"$1,$2")) )
	{
		str = tmpStr;
	}

	return str;
}


// ---------------------------------------------------------------
/**
* 概要    : 通貨記号を取る（空白からあとの部分を抜き出す）
* 対象    : すべて
* @param  : num, [string型], 通貨記号を取りたい値  (例 \ 1,000.0000)
* @return : str, [string型], 通貨記号を取り除いた値(例   1,000.0000)
*/
// ---------------------------------------------------------------
function fncDelCurrencySign(num)
{
	var i;
	var str;
	str = num.toString();

	if( str != "" )
	{
		if(str.indexOf(' ') != -1){
		i   = str.indexOf(' ');
		str = str.substring(i+1,str.length);
		}
	}

	return str;
}


// ---------------------------------------------------------------
/**
* 概要    : 通貨記号を付ける
* 対象    : すべて
* @param  : num, [string型], 通貨記号を付けたい値 (例   1,000.0000)
* @return : str, [string型], 通貨記号を付けた値   (例 \ 1,000.0000)
*/
// ---------------------------------------------------------------
function fncAddCurrencySign( num )
{
	var str = num.toString();

	var CurrencySign = document.DSO.lngMonetaryUnitCode.value;

	//空白以外の場合に通貨記号をつける
	if( str != "" )
	{
		str = CurrencySign + " " + str;
	}

	return str;
}


// ---------------------------------------------------------------
/**
* 概要    : 通貨記号を付ける
* 対象    : 明細行の表示、明細行を選択した場合
* @param  : num, [string型], 通貨記号を付けたい値 (例   1,000.0000)
* @return : str, [string型], 通貨記号を付けた値   (例 \ 1,000.0000)
*/
// ---------------------------------------------------------------
function fncAddCurrencySignForSentaku( CurrencySign , num )
{
	var str = num.toString();

	//空白以外の場合に通貨記号をつける
	if( str != "" )
	{
		str = CurrencySign + " " + str;
	}

	return str;
}


// ---------------------------------------------------------------
/**
* 概要    : 今日の日付けを返す
* 対象    : すべて
* @return : YYYYMMDD, [string型], YYYY/MM/DD
*/
// ---------------------------------------------------------------
function fncYYMMDD()
{
	var strDate = new Date();
	var strYY = strDate.getYear();
	var strMM = strDate.getMonth() + 1;
	var strDD = strDate.getDate();

	if (strYY < 2000) { strYY += 1900; }
	if (strMM < 10) { strMM = "0" + strMM; }
	if (strDD < 10) { strDD = "0" + strDD; }

	var YYMMDD = strYY + "/" + strMM + "/" + strDD;

	return YYMMDD;
}


// ---------------------------------------------------------------
/**
* 概要    : 特殊文字の変換
* 対象    : すべて
* @return : 変換された文字列
*/
// ---------------------------------------------------------------
function fncCheckReplaceString( strInString )
{
	strValue = strInString;

	strValue = strValue.replace( /&/g ,"&amp;" );
	strValue = strValue.replace( /\"/g ,"&quot;" );
	strValue = strValue.replace( /</g , "&lt;" );
	strValue = strValue.replace( />/g , "&gt;" );
	strValue = strValue.replace( /,/g , "&#44;" );
	strValue = strValue.replace( /\'/g , "&#39;" );
	strValue = strValue.replace( /\r\n/g , "\n" );
	strValue = strValue.replace( /\r/g , "\n" );
//	strValue = strValue.replace( / /g , "&nbsp;" );
	strValue = strValue.replace( /\n/g , "<br>" );

	return strValue;
}


// ---------------------------------------------------------------
/**
* 概要    : 特殊文字変換された文字列をもとの入力値に戻す
* 対象    : すべて
* @return : もとの入力値
*/
// ---------------------------------------------------------------
function fncCheckReplaceStringBack( strInString )
{

	strValue = strInString;

	strValue = strValue.replace( /&amp;/g ,"&" );
	strValue = strValue.replace( /&quot;/g ,"\"" );
	strValue = strValue.replace( /&lt;/g , "<" );
	strValue = strValue.replace( /&gt;/g , ">" );
	strValue = strValue.replace( /&#44;/g , "," );
	strValue = strValue.replace( /&#39;/g , "\'" );
//	strValue = strValue.replace( /&nbsp;/g , " " );
	strValue = strValue.replace( /<br>/g , "\n" );

	return strValue;
}




//-->