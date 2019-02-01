<!--
/**
* 明細枠を操作する関数群
*
* @package k.i.d.s.
* @license http://www.wiseknot.co.jp/
* @copyright Copyright &copy; 2004, Wiseknot
* @author tetsuka takafumi
* @version 0.1
*
* 更新履歴
* 2004.03.02 仕入管理　納期項目を必須から削除
* 2004.03.02 受注、発注、売上、仕入の各登録にて明細行にて単価０円登録を可能に変更
* 2004.03.03 売上管理　納期項目を必須から削除
* 2004.03.19 fncHdMonetaryUnitCode()
*            仕入、売上、日本円以外の場合、税区分を「非課税」にする
* 2004.03.26 受注、発注管理の際にも渡された行番号を記憶するように変更（修正時対応）
* 2004.03.29 fncDtCalTotalPrice、fncDtCalTaxPrice2()
*			売上管理で日本円の場合、切捨て処理
* 2004.03.29 fncDtCheck()、fncDtNewAry()
* 			発注、仕入、「No.」は新規行作成時に継承しない
* 2004.04.06 fncDtCalTotalPrice()
*			受注管理で日本円の場合、切捨て処理
* 2004.04.08 
*			計算方法種別の追加
* 2004.04.19
*			グローバル変数名の変更、他
* 2004.04.22
*			fncHdMonetaryRateCheck() 関数の追加
* 2004.06.14
*			fncDtReplaceInput() 関数にて仕入、売上時に税抜金額、消費税額等を再計算するように修正
*
*/


//------------------------------------------------------------------------------
// グローバル変数定義
//	注意：これらのグローバル変数は以下のスクリプト内で使用されています
//	dlist/record.js
//	dlist/po/index.html
//	dlist/so/index.html
//------------------------------------------------------------------------------
var saveRecord				= new Array();	// 明細行を単位とする配列
var g_lngSelIndex			= -1;			// 選択行を格納する変数
var g_lngReturnFlg			= 1 ;			// Detailのタブを押したときに、登録の戻りがあったら表示
var g_lngSentakufunouFlg	= 0;			// 明細行選択の処理が終わるまでほかの選択をできなくする
var g_curTax				= 0;			// 税率（計上日の税率、Detailのタブを押したときに初期化）
var g_lngTaxCode			= 1;			// 税コード（0.05を保持しているDB上のコード）（計上日の税コード、Detailのタブを押したときに初期化）
var g_lngTaxClassCode		= 0;			// 税区分コード
var g_lngDecimalCutPoint	= 2;			// 小数点以下、計算処理ポイント（初期値：小数点以下2桁で処理）
var g_lngCalcCode			= 0;			// 計算方法種別（0:四捨五入）

var g_strJpnCurrencySign    = "\\";			// 日本円通貨記号
var g_strFreeTaxClass       = "1";			// 税区分：非課税
var g_strOutTaxClass        = "2";			// 税区分：外税
var g_strInTaxClass         = "3";			// 税区分：内税
var g_strNoneMonetaryRate   = "0";			// レートタイプ：-
var g_strTtmMonetaryRate    = "1";			// レートタイプ：TTM
var g_strDefMonetaryRate    = "2";			// レートタイプ：社内

var g_strSOKindOfManagement = "SO";			// 受注管理
var g_strSCKindOfManagement = "SC";			// 売上管理
var g_strPOKindOfManagement = "PO";			// 発注管理
var g_strPCKindOfManagement = "PC";			// 仕入管理

var g_bytDefaultCheckedFlag = 1;			// 行追加時対象チェック状態フラグ  0: なし  1: あり
var g_bytDebugFlag          = 0;			// デバッグ用フラグ  0: 通常  1: デバッグ



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
	// 製品コード相違チェック
	var blnCheck = fncCheckDetailCode( saveRecord );

	if( !blnCheck )
	{
		//受注管理の場合
		if( typeof( window.parent.HSO.SOFlg ) == "object" )
		{
			alert( "製品または、売上区分が違います。" );
		}
		// その他の管理
		else
		{
			alert( "製品が違います。" );
		}

		return false;
	}



	//入力データのチェック
	var addFlg = fncDtAddCheck();

	if( addFlg == false ) return false;

	//入力枠の値を新規の配列に格納
	var aryRecord = fncDtNewAry();

	//明細行が選択されていない場合
	if ( g_lngSelIndex == -1)
	{
		//グローバル配列の最後に追加
		saveRecord.push(aryRecord);

		//空白行を追加したときに空白行を選択するためにもとのインデックスを保持する
		var preindex = -1;
	}
	//明細行が選択されている場合
	else
	{
		//選択された配列の上に、新規の配列を追加する
		saveRecordLength = parseInt(saveRecord.length); 
		saveRecordLeft  = saveRecord.slice(0,g_lngSelIndex);
		saveRecordRigft = saveRecord.slice(g_lngSelIndex, saveRecordLength);
		saveRecord      = saveRecordLeft;
		saveRecord.push(aryRecord);
		saveRecord      = saveRecord.concat(saveRecordRigft);

		//空白行を追加したときに空白行を選択するためにもとのインデックスを保持する
		var preindex = g_lngSelIndex;

		//インデックスを初期化
		g_lngSelIndex      = -1;
	}

	//明細枠を再表示
	fncDtDisplay( preindex );

	//ヘッダの[通貨]を変更できないようにする
	fncHdMonetaryUnitCheck();

	//空行追加の場合には、追加した空白行を選択
	if( aryRecord[0] == "" )
	{
		//グローバル配列の長さ
		saveRecordLength = saveRecord.length;

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
	//発注管理、仕入管理の場合
	if( typeof(window.parent.HSO.POFlg) == "object" || 
		typeof(window.parent.HSO.PCFlg) == "object" )
	{
		window.location.href = "/dlist/po/index.html#enddisplay";
	}
	//発注管理、仕入管理の場合
	else if( typeof(window.parent.HSO.SOFlg) == "object" || 
		typeof(window.parent.HSO.SCFlg) == "object" )
	{
		window.location.href = "/dlist/so/index.html#enddisplay";
	}
}


// ---------------------------------------------------------------
/**
* 概要   : 選択した行を削除
* 対象   : 「明細枠」があるものすべて
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
		saveRecordLength = parseInt(saveRecord.length);
	
		saveRecordLeft  = saveRecord.slice(0, g_lngSelIndex);
		saveRecordRigft = saveRecord.slice(g_lngSelIndex + 1, saveRecordLength);
		saveRecord      = new Array();
		saveRecord      = saveRecord.concat(saveRecordLeft, saveRecordRigft);
	
		g_lngSelIndex = -1;
	
		//明細枠を再表示
		fncDtDisplay();
	
		//明細行がない場合、
		// ヘッダの[通貨]を変更可能にする
		fncHdMonetaryUnitCheck();
		// [レートタイプ]を変更可能にする
		fncHdMonetaryRateCheck();
	}
}


// ---------------------------------------------------------------
/**
* 概要   : 入力枠の値を選択行と置き換える
* 対象   : 「明細枠」があるものすべて
* 備考   : 入力枠の値を選択行と置き換える。
*          その後、関数「fncDtDisplay()」を呼び出し、明細枠を再表示。
* 注意    :行が選択されていない場合には、ヘッダ部分にエラーメッセージを出力
*/
// ---------------------------------------------------------------
function fncDtCommitRecord()
{
	// 製品コード相違チェック
	var blnCheck = fncCheckDetailCode( saveRecord );

	if( !blnCheck )
	{
		//受注管理の場合
		if( typeof( window.parent.HSO.SOFlg ) == "object" )
		{
			alert( "製品または、売上区分が違います。" );
		}
		// その他の管理
		else
		{
			alert( "製品が違います。" );
		}

		return false;
	}



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
	//発注管理、仕入管理の場合
	if( typeof(window.parent.HSO.POFlg) == "object" || 
		typeof(window.parent.HSO.PCFlg) == "object" )
	{
		//仕入部品をクリア
		window.parent.DSO.strStockItemCode.length = 0;
	
		//仕入部品を選択できないようにする
		window.parent.DSO.strStockItemCode.disabled = true;
	}

	//単価リストをクリア
	window.parent.DSO.lngGoodsPriceCode.length = 0;

	//総合計金額まで、クリアされてしまうので、再度求めなおす
	fncDtCalAllTotalPrice();

	//明細行を選択できるようにする
	g_lngSentakufunouFlg = 0;

	fncDtGsChecked();
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
	// 総合計金額の初期化
	g_lngReSumTotalPrice = 0;


	//行番号
	lngTrCount = 1;
	
	//カラム名を取得
	strTableHtml = fncStrTableHtmlColumns();

	//一覧を作成
	for( i = 0; i < saveRecord.length; i++ )
	{
		strTableHtml = strTableHtml + 
					'<tr class="Lists01" id ="retsu' + i + '" onClick="fncDtSentaku(' + i + ');return false;"' + 'bgcolor="#ffffff"）>' + 
					'<td align="center">' + lngTrCount + '</td>' + 
					fncStrTableHtmlRows(i) + 
					'</tr>';
		lngTrCount++;
	}

	strTableHtml = strTableHtml + '</table>';

	//明細行が選択されていない場合にのみ、表示を一番最後の行にする処理の準備をする
	if( preindex == -1 )
	{
		strTableHtml = strTableHtml + '<a name="enddisplay"></a>';
	}

	//既存の一覧を作成し直した一覧に書き換える
	document.all.DetailList.innerHTML = strTableHtml;

	//総合計金額の計算
	fncDtCalAllTotalPrice();


	//明細行を選択できるようにする
	g_lngSentakufunouFlg = 0;

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

	//他の明細行の処理が終わってなければ、選択させない
	if( g_lngSentakufunouFlg == 1 )
	{
		return null;
	}
	else
	{
		//処理中のフラグを立てる
		//(現在は仕入部品の処理が終わったときに解除している)
		g_lngSentakufunouFlg = 1;
	}

	//明細行が選択されている場合のチェックフラグ(入力枠にエラーがあるとエラーになる)
	var checkFlg = true;

	//明細行が選択されている場合
	if( g_lngSelIndex != -1 )
	{
		//入力枠に変更がないかチェック
		checkFlg = fncDtCheck();
	}

	if (checkFlg == true)
	{

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
			//明細行を選択できるようにする
			g_lngSentakufunouFlg = 0;
		}
		//以前と違う選択行をクリックした場合
		else
		{
			//インデックスに選択行の配列番号をセット
			g_lngSelIndex = i;
	
			//「選択行」を反転させる
			document.getElementById("retsu" + g_lngSelIndex).style.backgroundColor="#bbbbbb";

			//入力枠をすべてクリア（空行のときのため）
			window.parent.fncResetFrm( window.parent.DSO );
	
			//発注管理、仕入管理の場合、仕入部品をクリア（空行のときのため）
			if( typeof(window.parent.HSO.POFlg) == "object" || 
				typeof(window.parent.HSO.PCFlg) == "object" )
			{
				window.parent.DSO.strStockItemCode.length  = 0;
			}
	
			//単価リストをクリア（空行のときのため）
			window.parent.DSO.lngGoodsPriceCode.length = 0;
	
			if( saveRecord[g_lngSelIndex][0] != "" )
			{
/*
				//製品から、製品名を作成
				subLoadMasterValue('cnProduct',
						 saveRecord[g_lngSelIndex][0],
						 window.parent.DSO.strProductName,
						 Array(saveRecord[g_lngSelIndex][0]),
						 window.document.objDataSourceSetting,
						 0);
				//製品から、顧客品番を作成
				subLoadMasterValue('cnGoodsCode',
						 saveRecord[g_lngSelIndex][0],
						 window.parent.DSO.strGoodsCode,
						 Array(saveRecord[g_lngSelIndex][0]),
						 window.document.objDataSourceSetting1,
						 1);
				//製品から、カートン入数を作成
				subLoadMasterValue('cnCartonQuantity',
						 saveRecord[g_lngSelIndex][0],
						 window.parent.DSO.lngCartonQuantity,
						 Array(saveRecord[g_lngSelIndex][0]),
						 window.document.objDataSourceSetting13,
						 13);
*/
				
				// 商品情報を取得（subloadmastersettings.js を利用）
				subLoadMasterValue('cnProductInfo', this, this, Array(saveRecord[g_lngSelIndex][0]), window.document.objDataSourceSettingProductInfo, 1);

				//発注管理、仕入管理の場合
				if( typeof(window.parent.HSO.POFlg) == "object" || 
					typeof(window.parent.HSO.PCFlg) == "object" )
				{
					//仕入科目から、仕入部品のオプション値を作成
					subLoadMasterOption( 'cnStockItem',
							 window.parent.DSO.strStockSubjectCode, 
							 window.parent.DSO.strStockItemCode,
							 Array(saveRecord[g_lngSelIndex][2]),
							 window.document.objDataSourceSetting10,
							 10);
				}

				//単価リストを作成
				fncDtGoodsPriceList2();

			}
			//空白行の時、明細行を選択できるようにする
			else
			{
				//明細行を選択できるようにする
				g_lngSentakufunouFlg = 0;
			}
	
			//「入力枠」に選択行を反映
			fncDtReplaceInput();

			//基準通貨を表示
			fncDtCalStdTotalPrice();

			//総合計金額の計算
			fncDtCalAllTotalPrice();
		}
	}
	else
	{
		g_lngSentakufunouFlg = 0;
	}


	//受注管理、売上管理の場合
	if( typeof(window.parent.HSO.SOFlg) == "object" || 
		typeof(window.parent.HSO.SCFlg) == "object" )
	{
		//明細行を選択できるようにする
		g_lngSentakufunouFlg = 0;
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
	// 製品コード相違チェック
	var blnCheck = fncCheckDetailCode( saveRecord );

	if( !blnCheck )
	{
		//受注管理の場合
		if( typeof( window.parent.HSO.SOFlg ) == "object" )
		{
			alert( "製品または、売上区分が違います。" );
		}
		// その他の管理
		else
		{
			alert( "製品が違います。" );
		}

		return false;
	}



	//「入力枠」の値を配列にセット
	var aryRecord = fncDtNewAry();

	//配列の長さ
	var aryRecordLength = aryRecord.length;

	for( j = 0; j < aryRecordLength ; j++ )
	{
		//入力枠と選択行の比較
		if( aryRecord[j] != saveRecord[g_lngSelIndex][j] )
		{
			// 単価リスト,仕入科目名,仕入部品名,単位（名称）,
			// 単価追加リスト,行番号のときスキップ
			// No.（金型番号）スキップ (Added by Kazushi Saito
			// 元数量　スキップ
			if (j==1 || j == 3 || j == 5 || j == 9 ||j == 14 || j==18 || j==22 || j==24 || j==25 || j==26 || j==27) continue;

//デバック中 後で消す
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

	//発注管理、仕入管理の場合
	if( typeof(window.parent.HSO.POFlg) == "object" || 
		typeof(window.parent.HSO.PCFlg) == "object" )
	{
		//値がすべてからだったら、空行を追加できる
		if( window.parent.DSO.strProductCode.value            == "" && //製品コード
			window.parent.DSO.strStockSubjectCode.value       == 0  && //仕入科目
			window.parent.DSO.strStockItemCode.selectedIndex  == -1 )  //仕入部品
		{
			if( window.parent.DSO.lngConversionClassCode[0].checked )
			{
				if( window.parent.DSO.curProductPrice_gs.value  == "" && //製品単価がない
					window.parent.DSO.lngGoodsQuantity_gs.value == "" )  //製品数量がない
				{
					return true;
				}
			}
			else if( window.parent.DSO.lngConversionClassCode[1].checked )
			{
				if( window.parent.DSO.curProductPrice_ps.value  == "" && //荷姿単価がない
					window.parent.DSO.lngGoodsQuantity_ps.value == "" )  //荷姿数量がない
				{
					return true;
				}
			}
		}
	}

	//受注管理、売上管理の場合
	if( typeof(window.parent.HSO.SOFlg) == "object" || 
		typeof(window.parent.HSO.SCFlg) == "object" )
	{
		//値がすべてからだったら、空行を追加できる
		if( window.parent.DSO.strProductCode.value            == "" && //製品コード
			window.parent.DSO.lngSalesClassCode.value         == 0  )  //売上区分
		{
			if( window.parent.DSO.lngConversionClassCode[0].checked )
			{
				if( window.parent.DSO.curProductPrice_gs.value  == "" && //製品単価がない
					window.parent.DSO.lngGoodsQuantity_gs.value == "" )  //製品数量がない
				{
					return true;
				}
			}
			else if( window.parent.DSO.lngConversionClassCode[1].checked )
			{
				if( window.parent.DSO.curProductPrice_ps.value  == "" && //荷姿単価がない
					window.parent.DSO.lngGoodsQuantity_ps.value == "" )  //荷姿数量がない
				{
					return true;
				}
			}
		}
	}

	//エラーがあった場合にメッセ−ジを詰め込む変数
	var alertList = "";

	//値がすべて空ではない場合のチェック
	//製品コードの入力がなかった場合
	if( window.parent.DSO.strProductCode.value == "" )
	{
		alertList += "製品コードを入力してください!\n";
	}
	//製品コードの入力が不正だった場合
	if( isNaN(window.parent.DSO.strProductCode.value) )
	{
		alertList += "製品コードの値が不正です!\n";
	}

	//発注管理、仕入管理の場合
	if( typeof(window.parent.HSO.POFlg) == "object" || 
		typeof(window.parent.HSO.PCFlg) == "object" )
	{
		//仕入科目が選択されなかった場合
		if( window.parent.DSO.strStockSubjectCode.value       == 0 )
		{
			alertList += "仕入科目を選択してください!\n";
		}
		//仕入部品が選択されなかった場合
		if( window.parent.DSO.strStockItemCode.selectedIndex == -1 ||
			window.parent.DSO.strStockItemCode.selectedIndex == 0  )
		{
			alertList += "仕入部品を選択してください!\n";
		}
//2004.03.02 suzukaze update start
		if( typeof(window.parent.HSO.POFlg) == "object" )
		{
			//納期が選択されなかった場合
			if( window.parent.DSO.dtmDeliveryDate.value == "" )
			{
				alertList += "納期を選択してください!\n";
			}
		}
//2004.03.02 suzukaze update end
	}

	//受注管理、売上管理の場合
	if( typeof(window.parent.HSO.SOFlg) == "object" || 
		typeof(window.parent.HSO.SCFlg) == "object" )
	{
		//売上区分が選択されなかった場合
		if( window.parent.DSO.lngSalesClassCode.value == 0 )
		{
			alertList += "売上区分を選択してください!\n";
		}
//2004.03.03 suzukaze update start
		if( typeof(window.parent.HSO.SOFlg) == "object" )
		{
			//納期が入力されなかった場合
			if( window.parent.DSO.dtmDeliveryDate.value == "" )
			{
				alertList += "納期を入力してください!\n";
			}
		}
//2004.03.03 suzukaze update end
	}

	//製品単位計上が選択されている場合
	if (window.parent.DSO.lngConversionClassCode[0].checked)
	{
//2004.03.01 suzukaze update start
		//製品単価が入力されていなかった場合
//		if( window.parent.DSO.curProductPrice_gs.value == "" ||
//			fncDelKannma(fncDelCurrencySign(window.parent.DSO.curProductPrice_gs.value)) == 0 )
		if( window.parent.DSO.curProductPrice_gs.value == "" )
//2004.03.01 suzukaze update end
		{
			alertList += "製品単価を入力してください!\n";
		}
		//製品数量が入力されていなかった場合
		if( window.parent.DSO.lngGoodsQuantity_gs.value == "" ||
			fncDelKannma(window.parent.DSO.lngGoodsQuantity_gs.value) == 0 )
		{
			alertList += "製品数量を入力してください!\n";
		}
//2004.03.01 suzukaze update start
		//発注管理、受注管理の場合
		if( typeof(window.parent.HSO.POFlg) == "object" || 
			typeof(window.parent.HSO.SOFlg) == "object" )
		{
			//製品単価の値が不正だった場合
			if( isNaN(fncDelKannma(fncDelCurrencySign(window.parent.DSO.curProductPrice_gs.value))) || 
				fncDelKannma(fncDelCurrencySign(window.parent.DSO.curProductPrice_gs.value)) < 0    )
			{
				alertList += "製品単価の値が不正です!\n";
			}
		}
		//仕入管理、売上管理の場合
		if( typeof(window.parent.HSO.POFlg) == "object" || 
			typeof(window.parent.HSO.SOFlg) == "object" )
		{
			//製品単価の値が不正だった場合
			if( isNaN(fncDelKannma(fncDelCurrencySign(window.parent.DSO.curProductPrice_gs.value))) )
			{
				alertList += "製品単価の値が不正です!\n";
			}
		}
//2004.03.01 suzukaze update end
//2004.03.17 suzukaze update start
		//発注管理、受注管理の場合
		if( typeof(window.parent.HSO.POFlg) == "object" || 
			typeof(window.parent.HSO.SOFlg) == "object" )
		{
			//製品数量の値が不正だった場合
			if( isNaN(fncDelKannma(window.parent.DSO.lngGoodsQuantity_gs.value)) || 
				fncDelKannma(window.parent.DSO.lngGoodsQuantity_gs.value) < 0    )
			{
				alertList += "製品数量の値が不正です!\n";
			}
		}
		//仕入管理、売上管理の場合
		if( typeof(window.parent.HSO.POFlg) == "object" || 
			typeof(window.parent.HSO.SOFlg) == "object" )
		{
			//製品数量の値が不正だった場合
			if( isNaN(fncDelKannma(window.parent.DSO.lngGoodsQuantity_gs.value)) )
			{
				alertList += "製品数量の値が不正です!\n";
			}
		}
//2004.03.17 suzukaze update end
	}
	//荷姿単位計上が選択されている場合
	else if( window.parent.DSO.lngConversionClassCode[1].checked )
	{
//2004.03.01 suzukaze update start
		//荷姿単価が入力されていなかった場合
//		if( window.parent.DSO.curProductPrice_ps.value == "" ||
//			fncDelKannma(fncDelCurrencySign(window.parent.DSO.curProductPrice_ps.value)) == 0  )
		if( window.parent.DSO.curProductPrice_ps.value == "" )
//2004.03.01 suzukaze update end
		{
			alertList += "荷姿単価を入力してください!\n";
		}
		//荷姿数量が入力されていなかった場合
		if( window.parent.DSO.lngGoodsQuantity_ps.value == "" ||
			fncDelKannma(window.parent.DSO.lngGoodsQuantity_ps.value) == 0 )
		{
			alertList += "荷姿数量を入力してください!\n";
		}
//2004.03.01 suzukaze update start
		//発注管理、受注管理の場合
		if( typeof(window.parent.HSO.POFlg) == "object" || 
			typeof(window.parent.HSO.SOFlg) == "object" )
		{
			//荷姿単価の値が不正だった場合
			if( isNaN(fncDelKannma(fncDelCurrencySign(window.parent.DSO.curProductPrice_ps.value))) || 
				fncDelKannma(fncDelCurrencySign(window.parent.DSO.curProductPrice_ps.value)) < 0 )
			{
				alertList += "荷姿単価の値が不正です!\n";
			}
		}
		//仕入管理、売上管理の場合
		if( typeof(window.parent.HSO.POFlg) == "object" || 
			typeof(window.parent.HSO.SOFlg) == "object" )
		{
			//荷姿単価の値が不正だった場合
			if( isNaN(fncDelKannma(fncDelCurrencySign(window.parent.DSO.curProductPrice_ps.value))) )
			{
				alertList += "荷姿単価の値が不正です!\n";
			}
		}
//2004.03.01 suzukaze update end
//2004.03.17 suzukaze update start
		//発注管理、受注管理の場合
		if( typeof(window.parent.HSO.POFlg) == "object" || 
			typeof(window.parent.HSO.SOFlg) == "object" )
		{
			//荷姿数量の値が不正だった場合
		if( isNaN(fncDelKannma(window.parent.DSO.lngGoodsQuantity_ps.value)) || 
			fncDelKannma(window.parent.DSO.lngGoodsQuantity_ps.value) < 0    )
			{
				alertList += "荷姿数量の値が不正です!\n";
			}
		}
		//仕入管理、売上管理の場合
		if( typeof(window.parent.HSO.POFlg) == "object" || 
			typeof(window.parent.HSO.SOFlg) == "object" )
		{
			//荷姿単価の値が不正だった場合
			if( isNaN(fncDelKannma(window.parent.DSO.lngGoodsQuantity_ps.value)) )
			{
				alertList += "荷姿数量の値が不正です!\n";
			}
		}
//2004.03.17 suzukaze update end
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
* 概要    : 入力枠の値を新規の配列に格納
* 対象    : 「明細枠」があるものすべて
* @retrun : aryRecord, [配列型], 新規の配列
*/
// ---------------------------------------------------------------
function fncDtNewAry()
{
	var aryRecord = new Array();

	aryRecord[0]  = window.parent.DSO.strProductCode.value;            //製品コード
	aryRecord[1]  = window.parent.DSO.lngGoodsPriceCode.value;         //単価リスト

	//発注管理、仕入管理の場合
	if( typeof(window.parent.HSO.POFlg) == "object" || 
		typeof(window.parent.HSO.PCFlg) == "object" )
	{
		aryRecord[2]  = window.parent.DSO.strStockSubjectCode.value;       //仕入科目
		aryRecord[3]  = window.parent.DSO.strStockSubjectCode.options[window.parent.DSO.strStockSubjectCode.selectedIndex].text;     //仕入科目（value + 名称）
		aryRecord[4]  = window.parent.DSO.strStockItemCode.value;          //仕入部品
		if( window.parent.DSO.strStockItemCode.selectedIndex != -1 )
		{
			aryRecord[5]  = window.parent.DSO.strStockItemCode.options[window.parent.DSO.strStockItemCode.selectedIndex].text;           //仕入部品（value + 名称）
		}else{
			aryRecord[5]  = "";
		}
	}

	if( window.parent.DSO.lngConversionClassCode[0].checked )
	{
		aryRecord[6]  = window.parent.DSO.lngConversionClassCode[0].value; // 換算区分(製品単位計上)
		aryRecord[7]  = window.parent.DSO.curProductPrice_gs.value;        // 製品単価
		aryRecord[8]  = window.parent.DSO.lngProductUnitCode_gs.value;     // 製品単位
		aryRecord[9]  = window.parent.DSO.lngProductUnitCode_gs.options[window.parent.DSO.lngProductUnitCode_gs.selectedIndex].text; //製品単位（名称）
		aryRecord[10] = window.parent.DSO.lngGoodsQuantity_gs.value;       // 製品数量
		aryRecord[14] = window.parent.DSO.curProductPrice_gs.value;        // 単価リスト追加データ
	}
	else if( window.parent.DSO.lngConversionClassCode[1].checked )
	{
		aryRecord[6]  = window.parent.DSO.lngConversionClassCode[1].value; // 換算区分(荷姿単位計上)
		aryRecord[7]  = window.parent.DSO.curProductPrice_ps.value;        // 荷姿単価
		aryRecord[8]  = window.parent.DSO.lngProductUnitCode_ps.value;     // 荷姿単位
		aryRecord[9]  = window.parent.DSO.lngProductUnitCode_ps.options[window.parent.DSO.lngProductUnitCode_ps.selectedIndex].text; //荷姿単位（名称）
		aryRecord[10] = window.parent.DSO.lngGoodsQuantity_ps.value;       // 荷姿数量
		aryRecord[14] = fncProductPriceForList();                          // 単価リスト追加データ
	}
	aryRecord[11] = window.parent.DSO.curTotalPrice.value;             // 税抜金額

	// 売上管理、仕入管理
	if( typeof(window.parent.HSO.SCFlg) == "object" || 
		typeof(window.parent.HSO.PCFlg) == "object" )
	{
		aryRecord[24] = aryRecord[10];
	}
	
	//発注管理、仕入管理の場合
	if( typeof(window.parent.HSO.POFlg) == "object" || 
		typeof(window.parent.HSO.PCFlg) == "object" )
	{
		aryRecord[12] = window.parent.DSO.lngCarrierCode.value;            // 運搬方法
	}

	aryRecord[13] = fncCheckReplaceString(window.parent.DSO.strDetailNote.value);             // 備考

	//仕入管理、売上管理の場合
	if( typeof(window.parent.HSO.PCFlg) == "object" || 
		typeof(window.parent.HSO.SCFlg) == "object" )
	{
		aryRecord[15] = window.parent.DSO.lngTaxClassCode.value;             // 消費税区分コード
		aryRecord[16] = window.parent.DSO.lngTaxCode.value;                  // 消費税（率）
		aryRecord[17] = window.parent.DSO.curTaxPrice.value;                 // 消費税額
		aryRecord[18] = "";            // 行番号
	}

	//受注管理、売上管理の場合
	if( typeof(window.parent.HSO.SOFlg) == "object" || 
		typeof(window.parent.HSO.SCFlg) == "object" )
	{
		aryRecord[19] = window.parent.DSO.lngSalesClassCode.value;          // 売上区分
		if( window.parent.DSO.lngSalesClassCode.selectedIndex != -1 )
		{
			aryRecord[20] = window.parent.DSO.lngSalesClassCode.options[window.parent.DSO.lngSalesClassCode.selectedIndex].text;           // 売上区分（value + 名称）
		}else{
			aryRecord[20] = "";
		}
		aryRecord[21] = window.parent.DSO.dtmDeliveryDate.value;          // 納期
	}

	//発注管理、仕入管理の場合
	if( typeof(window.parent.HSO.POFlg) == "object" || 
		typeof(window.parent.HSO.PCFlg) == "object" )
	{
		aryRecord[22] = ""; //window.parent.DSO.strSerialNo.value;         // No	// No.は新規行追加時に継承しないようにする (Modifyed by Kazushi Saito
		aryRecord[23] = window.parent.DSO.dtmDeliveryDate.value;          // 納期
	}




	// 売上管理
	if( typeof(window.parent.HSO.SCFlg) == "object" )
	{
		aryRecord[24] = aryRecord[10];            // 元数量
		aryRecord[25] = "";                       // 受注番号
		aryRecord[26] = "";                       // 明細行番号
		aryRecord[27] = g_bytDefaultCheckedFlag;  // 対象
	}
	// 仕入管理
	if( typeof(window.parent.HSO.PCFlg) == "object" )
	{
		aryRecord[25] = g_bytDefaultCheckedFlag; // 対象
	}


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
	// 対象値再設定
	fncSetCheckValue();



	saveRecord[g_lngSelIndex][0]  = window.parent.DSO.strProductCode.value;         // 製品コード
	saveRecord[g_lngSelIndex][1]  = window.parent.DSO.lngGoodsPriceCode.value;      // 単価リスト

	//発注管理、仕入管理の場合
	if( typeof(window.parent.HSO.POFlg) == "object" || 
		typeof(window.parent.HSO.PCFlg) == "object" )
	{
		saveRecord[g_lngSelIndex][2]  = window.parent.DSO.strStockSubjectCode.value;    // 仕入科目
		saveRecord[g_lngSelIndex][3]  = window.parent.DSO.strStockSubjectCode.options[window.parent.DSO.strStockSubjectCode.selectedIndex].text;     //仕入科目（value + 名称）
		saveRecord[g_lngSelIndex][4]  = window.parent.DSO.strStockItemCode.value;       // 仕入部品
	
		if( window.parent.DSO.strStockItemCode.selectedIndex != -1 )
		{
		saveRecord[g_lngSelIndex][5]  = window.parent.DSO.strStockItemCode.options[window.parent.DSO.strStockItemCode.selectedIndex].text;           //仕入部品（value + 名称）
		}else{
		saveRecord[g_lngSelIndex][5]  = "";
		}
	}





	if( window.parent.DSO.lngConversionClassCode[0].checked )
	{
		saveRecord[g_lngSelIndex][6]  = window.parent.DSO.lngConversionClassCode[0].value; // 換算区分(製品単位計上)
		saveRecord[g_lngSelIndex][7]  = window.parent.DSO.curProductPrice_gs.value;        // 製品単価
		saveRecord[g_lngSelIndex][8]  = window.parent.DSO.lngProductUnitCode_gs.value;     // 製品単位
		saveRecord[g_lngSelIndex][9]  = window.parent.DSO.lngProductUnitCode_gs.options[window.parent.DSO.lngProductUnitCode_gs.selectedIndex].text;     //製品単位（名称）
		saveRecord[g_lngSelIndex][10] = window.parent.DSO.lngGoodsQuantity_gs.value;       // 製品数量
		saveRecord[g_lngSelIndex][14] = window.parent.DSO.curProductPrice_gs.value;        // 単価リスト追加データ
	}
	else if(window.parent.DSO.lngConversionClassCode[1].checked )
	{
		saveRecord[g_lngSelIndex][6]  =  window.parent.DSO.lngConversionClassCode[1].value; // 換算区分(荷姿単位計上)
		saveRecord[g_lngSelIndex][7]  = window.parent.DSO.curProductPrice_ps.value;         // 荷姿単価
		saveRecord[g_lngSelIndex][8]  = window.parent.DSO.lngProductUnitCode_ps.value;      // 荷姿単位
		saveRecord[g_lngSelIndex][9]  = window.parent.DSO.lngProductUnitCode_ps.options[window.parent.DSO.lngProductUnitCode_ps.selectedIndex].text;     //荷姿単位（名称）
		saveRecord[g_lngSelIndex][10] = window.parent.DSO.lngGoodsQuantity_ps.value;        // 荷姿数量
		saveRecord[g_lngSelIndex][14] = fncProductPriceForList();                           // 単価リスト追加データ
	} 


	saveRecord[g_lngSelIndex][11] = window.parent.DSO.curTotalPrice.value;          // 税抜金額

	//発注管理、仕入管理の場合
	if( typeof(window.parent.HSO.POFlg) == "object" || 
		typeof(window.parent.HSO.PCFlg) == "object" )
	{
		saveRecord[g_lngSelIndex][12] = window.parent.DSO.lngCarrierCode.value;         // 運搬方法
	}

	saveRecord[g_lngSelIndex][13] = fncCheckReplaceString(window.parent.DSO.strDetailNote.value);          // 備考




	//仕入管理、売上管理の場合
	if( typeof(window.parent.HSO.PCFlg) == "object" || 
		typeof(window.parent.HSO.SCFlg) == "object" )
	{
		saveRecord[g_lngSelIndex][15] = window.parent.DSO.lngTaxClassCode.value;         // 消費税区分コード
		saveRecord[g_lngSelIndex][16] = window.parent.DSO.lngTaxCode.value;              // 消費税（率）
		saveRecord[g_lngSelIndex][17] = window.parent.DSO.curTaxPrice.value;             // 消費税額
	}

	//受注管理、売上管理の場合
	if( typeof(window.parent.HSO.SOFlg) == "object" || 
		typeof(window.parent.HSO.SCFlg) == "object" )
	{
		saveRecord[g_lngSelIndex][19] = window.parent.DSO.lngSalesClassCode.value;          // 売上区分
		if( window.parent.DSO.lngSalesClassCode.selectedIndex != -1 )
		{
			saveRecord[g_lngSelIndex][20] = window.parent.DSO.lngSalesClassCode.options[window.parent.DSO.lngSalesClassCode.selectedIndex].text;           //売上区分（value + 名称）
		}else{
			saveRecord[g_lngSelIndex][20] = "";
		}
		saveRecord[g_lngSelIndex][21] = window.parent.DSO.dtmDeliveryDate.value;          // 納期
	}

	//発注管理、仕入管理の場合
	if( typeof(window.parent.HSO.POFlg) == "object" || 
		typeof(window.parent.HSO.PCFlg) == "object" )
	{
		saveRecord[g_lngSelIndex][22] = window.parent.DSO.strSerialNo.value;              // No
		saveRecord[g_lngSelIndex][23] = window.parent.DSO.dtmDeliveryDate.value;          // 納期
	}
}


// ---------------------------------------------------------------
/**
* 概要   : 入力枠に選択行を反映
* 対象   : 「明細枠」があるものすべて
*/
// ---------------------------------------------------------------
function fncDtReplaceInput()
{
	window.parent.DSO.strProductCode.value         = saveRecord[g_lngSelIndex][0];  //製品コード
	//単価リスト(saveRecord[g_lngSelIndex][1])は、hmtlに直接書く（遅延のため）

	//発注管理、仕入管理の場合
	if( typeof(window.parent.HSO.POFlg) == "object" || 
		typeof(window.parent.HSO.PCFlg) == "object" )
	{
		window.parent.DSO.strStockSubjectCode.value    = saveRecord[g_lngSelIndex][2];  //仕入科目
		//仕入部品(saveRecord[g_lngSelIndex][4])は、hmtlに直接書く（遅延のため）
	}
//alert(saveRecord[g_lngSelIndex][7]);


	// 製品
	if( saveRecord[g_lngSelIndex][6] == "gs" )
	{
		window.parent.DSO.lngConversionClassCode[0].checked = true;             //換算区分(製品単位計上)
		window.parent.DSO.curProductPrice_gs.value     = saveRecord[g_lngSelIndex][7];  //製品単価
		window.parent.DSO.lngProductUnitCode_gs.value  = saveRecord[g_lngSelIndex][8];  //製品単位
		window.parent.DSO.lngGoodsQuantity_gs.value    = saveRecord[g_lngSelIndex][10]; //製品数量

		//[製品単価][製品単位][製品数量]を入力、選択できるようにする
		window.parent.DSO.curProductPrice_gs.disabled    = false;
		window.parent.DSO.lngProductUnitCode_gs.disabled = false;
		window.parent.DSO.lngGoodsQuantity_gs.disabled   = false;

		//[荷姿単価][荷姿単位][荷姿数量]を入力、選択できないようにする
		window.parent.DSO.curProductPrice_ps.disabled    = true;
		window.parent.DSO.lngProductUnitCode_ps.disabled = true;
		window.parent.DSO.lngGoodsQuantity_ps.disabled   = true;

		// 製品数量
		fncDtGSGoodsQuantityForPC();
	}
	// 荷姿
	else if( saveRecord[g_lngSelIndex][6] == "ps" )
	{
		window.parent.DSO.lngConversionClassCode[1].checked = true;             //換算区分(荷姿単位計上)
		window.parent.DSO.curProductPrice_ps.value     = saveRecord[g_lngSelIndex][7];  //荷姿単価
		window.parent.DSO.lngProductUnitCode_ps.value  = saveRecord[g_lngSelIndex][8];  //荷姿単位
		window.parent.DSO.lngGoodsQuantity_ps.value    = saveRecord[g_lngSelIndex][10]; //荷姿数量

		//[製品単価][製品単位][製品数量]を入力、選択できないようにする
		window.parent.DSO.curProductPrice_gs.disabled    = true;
		window.parent.DSO.lngProductUnitCode_gs.disabled = true;
		window.parent.DSO.lngGoodsQuantity_gs.disabled   = true;
		//[荷姿単価][荷姿単位][荷姿数量]を入力、選択できるようにする
		window.parent.DSO.curProductPrice_ps.disabled    = false;
		window.parent.DSO.lngProductUnitCode_ps.disabled = false;
		window.parent.DSO.lngGoodsQuantity_ps.disabled   = false;

		// 荷姿数量
		fncDtPSGoodsQuantityForPC();
	}


	// *v2* 合計金額
	fncDtCalTotalPrice();

	window.parent.DSO.curTotalPrice.value = saveRecord[g_lngSelIndex][11]; //税抜金額




	//発注管理、仕入管理の場合
	if( typeof(window.parent.HSO.POFlg) == "object" || 
		typeof(window.parent.HSO.PCFlg) == "object" )
	{
		window.parent.DSO.lngCarrierCode.value         = saveRecord[g_lngSelIndex][12]; //運搬方法
	}
	window.parent.DSO.strDetailNote.value          = fncCheckReplaceStringBack(saveRecord[g_lngSelIndex][13]); //備考

	//仕入管理、売上管理の場合
	if( typeof(window.parent.HSO.PCFlg) == "object" || 
		typeof(window.parent.HSO.SCFlg) == "object" )
	{
		window.parent.DSO.lngTaxClassCode.value = saveRecord[g_lngSelIndex][15];         //消費税区分コード
		window.parent.DSO.lngTaxCode.value      = saveRecord[g_lngSelIndex][16];         //消費税（率）
		//window.parent.DSO.curTaxPrice.value     = saveRecord[g_lngSelIndex][17];         //消費税額
		saveRecord[g_lngSelIndex][17] = window.parent.DSO.curTaxPrice.value; //消費税額

// 2004.06.14 suzukaze update start
		if( window.parent.DSO.lngTaxClassCode.value == g_strInTaxClass )
		{
			// 明細枠から入力枠へ反映時に 価格＊数量＝税抜金額＋消費税額 となっていない明細行に対して、消費税額などを再計算するように修正

			var ProductPrice  = 0;	// 単価
			var GoodsQuantity = 0;	// 数量
			var TotalPrice    = 0;	// 税抜金額
			var ComTotalPrice = 0;	// 比較用

			if( saveRecord[g_lngSelIndex][6] == "gs" )
			{
				//入力枠の[製品単価]から値を得て、通貨記号、カンマを取る
				ProductPrice  = fncDelKannma(fncDelCurrencySign(window.parent.DSO.curProductPrice_gs.value));
				//入力枠の[製品数量]から値を得て、カンマを取る
				GoodsQuantity = fncDelKannma(window.parent.DSO.lngGoodsQuantity_gs.value);
			}
			else if( saveRecord[g_lngSelIndex][6] == "ps" )
			{
				//入力枠の荷姿単価から値を得て、通貨記号、カンマを取る
				ProductPrice  = fncDelKannma(fncDelCurrencySign(window.parent.DSO.curProductPrice_ps.value));
				//入力枠の荷姿数量から値を得て、カンマを取る
				GoodsQuantity = fncDelKannma(window.parent.DSO.lngGoodsQuantity_ps.value);
			}

			TotalPrice    = window.parent.fncVBSNumCalc(ProductPrice, "*", GoodsQuantity);
			ComTotalPrice = window.parent.DSO.curTotalPrice.value + window.parent.DSO.curTaxPrice.Value;

			if( TotalPrice != ComTotalPrice )
			{
				fncDtCalTotalPrice();
			}
		}
// 2004.06.14 suzukaze update end
	}

	//受注管理、売上管理の場合
	if( typeof(window.parent.HSO.SOFlg) == "object" || 
		typeof(window.parent.HSO.SCFlg) == "object" )
	{
		window.parent.DSO.lngSalesClassCode.value = saveRecord[g_lngSelIndex][19];          //売上区分
		window.parent.DSO.dtmDeliveryDate.value   = saveRecord[g_lngSelIndex][21];          //納期
	}

	//発注管理、仕入管理の場合
	if( typeof(window.parent.HSO.POFlg) == "object" || 
		typeof(window.parent.HSO.PCFlg) == "object" )
	{
		window.parent.DSO.strSerialNo.value     = saveRecord[g_lngSelIndex][22];              //No
		window.parent.DSO.dtmDeliveryDate.value = saveRecord[g_lngSelIndex][23];              //納期
	}

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


	// 受注管理の場合
	if( typeof(window.parent.HSO.SOFlg) == "object" )
	{
		if( window.parent.lngLanguageCode == 1 )
		{
			strTableHtml ='<table width="910" cellpadding="0" cellspacing="1" border="0"' + 
						  'bgcolor="#6f8180"><tr class="TrSegs">' + 
						  '<td nowrap>&nbsp;</td>'                  +
						  '<td nowrap id="ExStrDL02">製品</td>' +
						  '<td nowrap id="ExStrDL01">売上区分</td>'     +
						  '<td nowrap id="ExStrDL03">単価</td>' +
						  '<td nowrap id="ExStrDL04">単位</td>'     +
						  '<td nowrap id="ExStrDL05">数量</td>'     +
						  '<td nowrap id="ExStrDL06">税抜金額</td>'     +
						  '<td nowrap id="ExStrDL07">納期</td>' +
						  '<td nowrap id="ExStrDL08">備考</td>'     +
						  '</tr>';
		}
		else if( window.parent.lngLanguageCode == 0 )
		{
			strTableHtml ='<table width="910" cellpadding="0" cellspacing="1" border="0"' + 
						  'bgcolor="#6f8180"><tr class="TrSegs">' + 
						  '<td>&nbsp;</td>'                  +
						  '<td nowrap id="ExStrDL02">Products</td>'    +
						  '<td nowrap id="ExStrDL01">Goods set code</td>'     +
						  '<td nowrap id="ExStrDL03">Price</td>'  +
						  '<td nowrap id="ExStrDL04">Unit</td>'        +
						  '<td nowrap id="ExStrDL05">Quantity</td>'         +
						  '<td nowrap id="ExStrDL06">Amt Bfr tax</td>'     +
						  '<td nowrap id="ExStrDL07">Delivery date</td>'  +
						  '<td nowrap id="ExStrDL08">Remark</td>'       +
						  '</tr>';
		}
	}

	//発注管理の場合
	if( typeof(window.parent.HSO.POFlg) == "object" )
	{
		if( window.parent.lngLanguageCode == 1 )
		{
			strTableHtml ='<table width="910" cellpadding="0" cellspacing="1" border="0"' + 
						  'bgcolor="#6f8180"><tr class="TrSegs">' + 
						  '<td nowrap>&nbsp;</td>'                  +
						  '<td nowrap id="ExStrDL01">製品</td>'     +
						  '<td nowrap id="ExStrDL02">仕入科目</td>' +
						  '<td nowrap id="ExStrDL03">仕入部品</td>' +
						  '<td nowrap id="ExStrDL04">単価</td>'     +
						  '<td nowrap id="ExStrDL05">単位</td>'     +
						  '<td nowrap id="ExStrDL06">数量</td>'     +
						  '<td nowrap id="ExStrDL07">税抜金額</td>' +
						  '<td nowrap id="ExStrDL08">納期</td>'     +
						  '<td nowrap id="ExStrDL09">備考</td>'     +
						  '</tr>';
		}
		else if( window.parent.lngLanguageCode == 0 )
		{
			strTableHtml ='<table width="910" cellpadding="0" cellspacing="1" border="0"' + 
						  'bgcolor="#6f8180"><tr class="TrSegs">' + 
						  '<td>&nbsp;</td>'                  +
						  '<td nowrap id="ExStrDL01">Products</td>'     +
						  '<td nowrap id="ExStrDL02">Goods set</td>'    +
						  '<td nowrap id="ExStrDL03">Goods parts</td>'  +
						  '<td nowrap id="ExStrDL04">Price</td>'        +
						  '<td nowrap id="ExStrDL05">Unit</td>'         +
						  '<td nowrap id="ExStrDL06">Quantity</td>'     +
						  '<td nowrap id="ExStrDL07">Amt Bfr tax</td>'  +
						  '<td nowrap id="ExStrDL08">Delivery date</td>'  +
						  '<td nowrap id="ExStrDL09">Remark</td>'       +
						  '</tr>';
		}
	}

	// 売上管理の場合
	if( typeof(window.parent.HSO.SCFlg) == "object" )
	{
		if( window.parent.lngLanguageCode == 1 )
		{
			strTableHtml ='<table width="910" cellpadding="0" cellspacing="1" border="0"' + 
						  'bgcolor="#6f8180"><tr class="TrSegs">'	+ 
						  '<td nowrap>&nbsp;</td>'                  +
						  '<td nowrap id="ExStrDL09">対象</td>'		+
						  '<td nowrap id="ExStrDL02">製品</td>'		+
						  '<td nowrap id="ExStrDL01">売上区分</td>'	+
						  '<td nowrap id="ExStrDL03">単価</td>'		+
						  '<td nowrap id="ExStrDL04">単位</td>'		+
						  '<td nowrap id="ExStrDL05">数量</td>'		+
						  '<td nowrap id="ExStrDL10">元数量</td>'	+
						  '<td nowrap id="ExStrDL06">税抜金額</td>'	+
						  '<td nowrap id="ExStrDL07">納期</td>'		+
						  '<td nowrap id="ExStrDL08">備考</td>'		+
						  '</tr>';
		}
		else if( window.parent.lngLanguageCode == 0 )
		{
			strTableHtml ='<table width="910" cellpadding="0" cellspacing="1" border="0"' + 
						  'bgcolor="#6f8180"><tr class="TrSegs">'		+ 
						  '<td>&nbsp;</td>'								+
						  '<td nowrap id="ExStrDL09">Target</td>'		+
						  '<td nowrap id="ExStrDL02">Products</td>'		+
						  '<td nowrap id="ExStrDL01">Goods set code</td>'	+
						  '<td nowrap id="ExStrDL03">Price</td>'		+
						  '<td nowrap id="ExStrDL04">Unit</td>'			+
						  '<td nowrap id="ExStrDL05">Quantity</td>'		+
						  '<td nowrap id="ExStrDL10">Org Quantity</td>'	+
						  '<td nowrap id="ExStrDL06">Amt Bfr tax</td>'	+
						  '<td nowrap id="ExStrDL07">Delivery date</td>'	+
						  '<td nowrap id="ExStrDL08">Remark</td>'		+
						  '</tr>';
		}
	}

	// 仕入管理の場合
	if( typeof(window.parent.HSO.PCFlg) == "object" )
	{
		if( window.parent.lngLanguageCode == 1 )
		{
			strTableHtml ='<table width="910" cellpadding="0" cellspacing="1" border="0"' + 
						  'bgcolor="#6f8180"><tr class="TrSegs">' + 
						  '<td nowrap>&nbsp;</td>'                  +
						  '<td nowrap id="ExStrDL10">対象</td>'     +
						  '<td nowrap id="ExStrDL01">製品</td>'     +
						  '<td nowrap id="ExStrDL02">仕入科目</td>' +
						  '<td nowrap id="ExStrDL03">仕入部品</td>' +
						  '<td nowrap id="ExStrDL04">単価</td>'     +
						  '<td nowrap id="ExStrDL05">単位</td>'     +
						  '<td nowrap id="ExStrDL06">数量</td>'     +
						  '<td nowrap id="ExStrDL11">元数量</td>'   +
						  '<td nowrap id="ExStrDL07">税抜金額</td>' +
						  '<td nowrap id="ExStrDL08">納期</td>'     +
						  '<td nowrap id="ExStrDL09">備考</td>'     +
						  '</tr>';
		}
		else if( window.parent.lngLanguageCode == 0 )
		{
			strTableHtml ='<table width="910" cellpadding="0" cellspacing="1" border="0"' + 
						  'bgcolor="#6f8180"><tr class="TrSegs">'		+ 
						  '<td>&nbsp;</td>'								+
						  '<td nowrap id="ExStrDL10">Target</td>'		+
						  '<td nowrap id="ExStrDL01">Products</td>'		+
						  '<td nowrap id="ExStrDL02">Goods set</td>'	+
						  '<td nowrap id="ExStrDL03">Goods parts</td>'	+
						  '<td nowrap id="ExStrDL04">Price</td>'		+
						  '<td nowrap id="ExStrDL05">Unit</td>'			+
						  '<td nowrap id="ExStrDL06">Quantity</td>'		+
						  '<td nowrap id="ExStrDL11">Org Quantity</td>'	+
						  '<td nowrap id="ExStrDL07">Amt Bfr tax</td>'	+
						  '<td nowrap id="ExStrDL08">Delivery date</td>'	+
						  '<td nowrap id="ExStrDL09">Remark</td>'		+
						  '</tr>';
		}
	}

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
	var lngOffsetVal  = 0;

	var strChkOnPath  = "/img/type01/cmn/check_on.gif";
	var strChkOffPath = "/img/type01/cmn/check_off.gif";
	var strChkImgPath = strChkOffPath;


	if( typeof(window.parent.HSO.SCFlg) == "object" ||
		typeof(window.parent.HSO.PCFlg) == "object" )
	{
		// 直登録ではない場合
		if( window.parent.document.all.lngDirectRegistFlag.value == 0 )
		{
			// 売上、仕入、のチェックボックス状態の初期値を引継ぎ設定
			if( typeof(eval("window.DL.blnOffset" + i)) != "undefined" )
			{
				if( eval("window.DL.blnOffset" + i + ".value") == 1 )
				{
					lngOffsetVal  = 1;
					strChkImgPath = strChkOnPath;
				}
			}
		}
		// 直登録の場合
		else
		{
			lngOffsetVal  = 1;
			strChkImgPath = strChkOnPath;
		}
	}


	// 売上
	if( typeof(window.parent.HSO.SCFlg) == "object" )
	{
		// 直登録ではない場合
		if( window.parent.document.all.lngDirectRegistFlag.value == 0 )
		{
			if( saveRecord[i][27] == 1 )
			{
				lngOffsetVal  = 1;
				strChkImgPath = strChkOnPath;
			}
		}
		// 直登録の場合
		else
		{
			lngOffsetVal  = 1;
			strChkImgPath = strChkOnPath;
		}
	}

	// 仕入
	if( typeof(window.parent.HSO.PCFlg) == "object" )
	{
		// 直登録ではない場合
		if( window.parent.document.all.lngDirectRegistFlag.value == 0 )
		{
			if( saveRecord[i][25] == 1 )
			{
				lngOffsetVal  = 1;
				strChkImgPath = strChkOnPath;
			}
		}
		// 直登録の場合
		else
		{
			lngOffsetVal  = 1;
			strChkImgPath = strChkOnPath;
		}
	}





	// 受注管理の場合
	if( typeof(window.parent.HSO.SOFlg) == "object" )
	{
		strTableHtml ='<td align     ="center" nowrap>' + saveRecord[i][0]  +				// 製品
					  '</td><td nowrap>'                + saveRecord[i][20] +				// 売上区分
					  '</td><td align="right" nowrap>'  + saveRecord[i][7]  + "&nbsp;" +	// 単価
					  '</td><td align="center" nowrap>' + saveRecord[i][9]  +				// 単位（名称）
					  '</td><td align="right" nowrap>'  + saveRecord[i][10] + "&nbsp;" +	// 数量
					  '</td><td align="right" nowrap>'  + saveRecord[i][11] + "&nbsp;" +	// 税抜金額
					  '</td><td align="center" nowrap>' + saveRecord[i][21] +				// 納品日
					  '</td><td nowrap>'                + saveRecord[i][13] +				// 備考
					  '</td>';
		return strTableHtml;
	}

	// 発注管理の場合
	if( typeof(window.parent.HSO.POFlg) == "object" )
	{
		strTableHtml ='<td align="center" nowrap>'      + saveRecord[i][0]  +				// 製品
					  '</td><td nowrap>'                + saveRecord[i][3]  +				// 仕入科目（名称）
					  '</td><td nowrap>'                + saveRecord[i][5]  +				// 仕入部品（名称）
					  '</td><td align="right" nowrap>'  + saveRecord[i][7]  + "&nbsp;" +	// 単価
					  '</td><td align="center" nowrap>' + saveRecord[i][9]  +				// 単位（名称）
					  '</td><td align="right" nowrap>'  + saveRecord[i][10] + "&nbsp;" +	// 数量
					  '</td><td align="right" nowrap>'  + saveRecord[i][11] + "&nbsp;" +	// 税抜金額
					  '</td><td align="center" nowrap>' + saveRecord[i][23] + "&nbsp;" +	// 納期
					  '</td><td nowrap>'                + saveRecord[i][13] +				// 備考
					  '</td>';
		return strTableHtml;
	}

	// 売上管理の場合
	if( typeof(window.parent.HSO.SCFlg) == "object" )
	{
		
		strTableHtml =''+
					  '<td align="center" valign="top">' +
					  '<img onclick="fncSetCheck( this, ' + i + ' );" src="' + strChkImgPath + '" width="12" height="12">' +

					  '</td><td align="center" nowrap>'	+ saveRecord[i][0]  +				// 製品
					  '</td><td nowrap>'                + saveRecord[i][20] +				// 売上区分
					  '</td><td align="right" nowrap>'  + saveRecord[i][7]  + "&nbsp;" +	// 単価
					  '</td><td align="center" nowrap>' + saveRecord[i][9]  +				// 単位（名称）
					  '</td><td align="right" nowrap>'  + saveRecord[i][10] + "&nbsp;" +	// 数量
					  '</td><td align="right" nowrap>'  + saveRecord[i][24] + "&nbsp;" +	// 元数量
					  '</td><td align="right" nowrap>'  + saveRecord[i][11] + "&nbsp;" +	// 税抜金額
					  '</td><td align="center" nowrap>' + saveRecord[i][21] +				// 納品日
					  '</td><td nowrap>'                + saveRecord[i][13] +				// 備考
					  '</td>' +
					  '<input type="hidden" name="blnOffset' + i + '" value="' + lngOffsetVal + '">';
		return strTableHtml;
	}
	
	// 仕入管理の場合
	if( typeof(window.parent.HSO.PCFlg) == "object" )
	{
		strTableHtml ='' +
					  '<td align="center" valign="top">' +
					  '<img onclick="fncSetCheck( this, ' + i + ' );" src="' + strChkImgPath + '" width="12" height="12">' +

					  '<td align="center" nowrap>'      + saveRecord[i][0]  +				// 製品
					  '</td><td nowrap>'                + saveRecord[i][3]  +				// 仕入科目（名称）
					  '</td><td nowrap>'                + saveRecord[i][5]  +				// 仕入部品（名称）
					  '</td><td align="right" nowrap>'  + saveRecord[i][7]  + "&nbsp;" +	// 単価
					  '</td><td align="center" nowrap>' + saveRecord[i][9]  +				// 単位（名称）
					  '</td><td align="right" nowrap>'  + saveRecord[i][10] + "&nbsp;" +	// 数量
					  '</td><td align="right" nowrap>'  + saveRecord[i][24] + "&nbsp;" +	// 元数量
					  '</td><td align="right" nowrap>'  + saveRecord[i][11] + "&nbsp;" +	// 税抜金額
					  '</td><td align="center" nowrap>' + saveRecord[i][23] + "&nbsp;" +	// 納期
					  '</td><td nowrap>'                + saveRecord[i][13] +				// 備考
					  '</td>' +
					  '<input type="hidden" name="blnOffset' + i + '" value="' + lngOffsetVal + '">';
		return strTableHtml;
	}

}


// ---------------------------------------------------------------
/**
* 概要   : 登録ボタンを押したときに、header欄に、明細枠のデータをhiddenに吐き出す
* 対象   : 「明細枠」があるものすべて
* 備考   : selectボックスのdisabledは、そのままでは、postされないので、
*          明示的にhiddenを書いています
*/
// ---------------------------------------------------------------
function fncDtRegistRecord(){



	if( saveRecord.length <= 0 )
	{
		alert( "明細行がありません。" );
		return;
	}


	// 対象値再設定
	fncSetCheckValue();





	// 明細行製品・売上区分チェック
	var blnCheck = fncCheckDetailRecords( saveRecord );

	if( !blnCheck )
	{
		alert( "製品または、売上区分が違います。" );
		return false;
	}




	//明細行が選択されている場合のチェックフラグ(入力枠にエラーがあるとエラーになる)
	var checkFlg = true;

	//明細行が選択されている場合
	if( g_lngSelIndex != -1 )
	{
		//入力枠に変更がないかチェック
		checkFlg = fncDtCheck();
	}

	if (checkFlg == true)
	{
		var strHiddenHtml = "";
	
		//hiddenで吐き出す連番（空行を削除すると順番がかわるため使用）
		var hiddenNumber = 0 ;
	
		for( i = 0; i < saveRecord.length; i++ )
		{
			//空行チェック
			if (saveRecord[i][0] == "") continue;


			// 相殺チェック確認（チェックボックスがチェックされている行のみを処理対象とする） added by saito
			//alert( eval("window.DL.blnOffset" + i + ".value") );
			if( typeof(eval("window.DL.blnOffset" + i)) != "undefined" )
			{
				if( eval("window.DL.blnOffset" + i + ".value") != 1 ) continue;
			}


			strHiddenHtml = strHiddenHtml + fncDtHiddenHtml(i, hiddenNumber);
			hiddenNumber++; 
		}
	
		//承認ルートを追加
		strHiddenHtml = strHiddenHtml + "<input type='hidden' name='lngWorkflowOrderCode' value='" + window.parent.DSO.lngWorkflowOrderCode.value + "' >\n" ;

		// 部門コードを追加
		strHiddenHtml = strHiddenHtml + "<input type='hidden' name='lngInChargeGroupCode' value='" + window.parent.DSO.lngInChargeGroupCode.value + "' >\n" ;

		// 担当者コードを追加
		strHiddenHtml = strHiddenHtml + "<input type='hidden' name='lngInChargeUserCode' value='" + window.parent.DSO.lngInChargeUserCode.value + "' >\n" ;


		if( window.parent.HSO.strCustomerReceiveCode )
		{
			// 顧客受注番号を追加
			strHiddenHtml = strHiddenHtml + "<input type='hidden' name='strCustomerReceiveCode' value='" + window.parent.HSO.strCustomerReceiveCode.value + "' >\n" ;
		}




		//通貨を追加
		strHiddenHtml = strHiddenHtml + "<input type='hidden' name='lngMonetaryUnitCode' value='" + window.parent.HSO.lngMonetaryUnitCode.value + "' >\n" ;
	
		//レートタイプを追加
		strHiddenHtml = strHiddenHtml + "<input type='hidden' name='lngMonetaryRateCode' value='" + window.parent.HSO.lngMonetaryRateCode.value + "' >\n" ;
	
		//総合計金額（税抜き）を追加
		strHiddenHtml = strHiddenHtml + "<input type='hidden' name='curAllTotalPrice' value='" + fncDelKannma(fncDelCurrencySign(window.parent.DSO.curAllTotalPrice.value)) + "' >\n" ;



		//デバック用
		if( g_bytDebugFlag )
		{
			var blnRes = confirm( strHiddenHtml );

			if( !blnRes )
			{
				return false;
			}
		}



		//フォーム(name="HSO")に明細枠のデータを渡す
		window.parent.document.all.DtHiddenRecord.innerHTML = strHiddenHtml;
	
		//フォームHSOをサブミット
		window.parent.document.HSO.submit();
	}
}


// ---------------------------------------------------------------
/**
* 概要   : 入力枠のhiddenに吐き出すデータを作成
* 対象   : 「明細枠」があるものすべて
* return : strHiddenHtml, [string型], 明細枠の内容をhiddenに置き換えて吐き出す
*/
// ---------------------------------------------------------------
function fncDtHiddenHtml(i, hiddenNumber){

	//仕入管理、売上管理の場合のみのhidden値
	var strPC = "";

	if( typeof(window.parent.HSO.PCFlg) == "object" || 
		typeof(window.parent.HSO.SCFlg) == "object" )
	{
		var zeicodevalue;
		//税コード(値からコードに変換)
		//非課税のとき
		if( saveRecord[i][15] == g_strFreeTaxClass )
		{
			zeicodevalue = "";
		}
		//非課税以外のとき
		else
		{
			zeicodevalue = g_lngTaxCode;
		}

	strPC = "<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][lngTaxClassCode]'  value='" + saveRecord[i][15] + "' >\n" +                                   //消費税区分コード
			"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][lngTaxCode]'       value='" + zeicodevalue + "' >\n"      +                                   //消費税コード
			"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][curTaxPrice]'      value='" + fncDelKannma(fncDelCurrencySign(saveRecord[i][17])) + "' >\n" + //消費税額
			"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][lngOrderDetailNo]' value='" + saveRecord[i][18] + "' >\n" ;                                   //行番号
	}
// 2004.03.26 suzukaze update start
//受注管理、発注管理の場合にも行番号をHidden値で覚えておくように修正
	else
	{
	strPC = "<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][lngOrderDetailNo]' value='" + saveRecord[i][18] + "' >\n" ;                                   //行番号
	}
// 2004.03.26 suzukaze update end


	// 受注管理の場合
	if( typeof(window.parent.HSO.SOFlg) == "object" )
	{
		strHiddenHtml = "<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][strProductCode]'          value='" + saveRecord[i][0] + "' >\n"  + //製品
						"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][lngGoodsPriceCode]'       value='" + saveRecord[i][1] + "' >\n"  + //単価リスト
						"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][lngConversionClassCode]'  value='" + saveRecord[i][6] + "' >\n"  + //換算区分
						"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][curProductPrice]'         value='" + fncDelKannma(fncDelCurrencySign(saveRecord[i][7])) + "' >\n"  + //単価
						"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][lngProductUnitCode]'      value='" + saveRecord[i][8] + "' >\n"  + //単位
						"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][lngProductUnitCodeName]'  value='" + saveRecord[i][9] + "' >\n"  + //単位（名称）
						"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][lngGoodsQuantity]'        value='" + fncDelKannma(saveRecord[i][10]) + "' >\n"  + //数量
						"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][curTotalPrice]'           value='" + fncDelKannma(fncDelCurrencySign(saveRecord[i][11])) + "' >\n" + //税抜金額
						"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][strDetailNote]'           value='" + saveRecord[i][13] + "' >\n" + //備考
						"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][curProductPriceForList]'  value='" + fncDelKannma(fncDelCurrencySign(saveRecord[i][14])) + "' >\n" + //単価リスト追加データ
						"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][lngSalesClassCode]'       value='" + saveRecord[i][19] + "' >\n" + //売上区分
						"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][lngSalesClassCodeName]'   value='" + saveRecord[i][20] + "' >\n" + //売上区分（value + 名称）
						"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][dtmDeliveryDate]'         value='" + saveRecord[i][21] + "' >\n" + //納期
						strPC;
	}

	// 発注管理の場合
	if( typeof(window.parent.HSO.POFlg) == "object" )
	{
		strHiddenHtml = "<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][strProductCode]'          value='" + saveRecord[i][0] + "' >\n"  + //製品
						"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][lngGoodsPriceCode]'       value='" + saveRecord[i][1] + "' >\n"  + //単価リスト
						"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][strStockSubjectCode]'     value='" + saveRecord[i][2] + "' >\n"  + //仕入科目
						"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][strStockSubjectCodeName]' value='" + saveRecord[i][3] + "' >\n"  + //仕入科目（value + 名称）
						"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][strStockItemCode]'        value='" + saveRecord[i][4] + "' >\n"  + //仕入部品
						"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][strStockItemCodeName]'    value='" + saveRecord[i][5] + "' >\n"  + //仕入部品（value + 名称）
						"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][lngConversionClassCode]'  value='" + saveRecord[i][6] + "' >\n"  + //換算区分
						"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][curProductPrice]'         value='" + fncDelKannma(fncDelCurrencySign(saveRecord[i][7])) + "' >\n"  + //単価
						"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][lngProductUnitCode]'      value='" + saveRecord[i][8] + "' >\n"  + //単位
						"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][lngProductUnitCodeName]'  value='" + saveRecord[i][9] + "' >\n"  + //単位（名称）
						"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][lngGoodsQuantity]'        value='" + fncDelKannma(saveRecord[i][10]) + "' >\n"  + //数量
						"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][curTotalPrice]'           value='" + fncDelKannma(fncDelCurrencySign(saveRecord[i][11])) + "' >\n" + //税抜金額
						"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][lngCarrierCode]'          value='" + saveRecord[i][12] + "' >\n" + //運搬方法
						"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][strDetailNote]'           value='" + saveRecord[i][13] + "' >\n" + //備考
						"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][curProductPriceForList]'  value='" + fncDelKannma(fncDelCurrencySign(saveRecord[i][14])) + "' >\n" + //単価リスト追加データ
						"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][strSerialNo]'             value='" + saveRecord[i][22] + "' >\n" + //No
						"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][dtmDeliveryDate]'         value='" + saveRecord[i][23] + "' >\n" + //納期
						strPC;
	}

	// 売上管理の場合
	if( typeof(window.parent.HSO.SCFlg) == "object" )
	{
		strHiddenHtml = "<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][strProductCode]'          value='" + saveRecord[i][0] + "' >\n"  + //製品
						"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][lngGoodsPriceCode]'       value='" + saveRecord[i][1] + "' >\n"  + //単価リスト
						"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][lngConversionClassCode]'  value='" + saveRecord[i][6] + "' >\n"  + //換算区分
						"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][curProductPrice]'         value='" + fncDelKannma(fncDelCurrencySign(saveRecord[i][7])) + "' >\n"  + //単価
						"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][lngProductUnitCode]'      value='" + saveRecord[i][8] + "' >\n"  + //単位
						"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][lngProductUnitCodeName]'  value='" + saveRecord[i][9] + "' >\n"  + //単位（名称）
						"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][lngGoodsQuantity]'        value='" + fncDelKannma(saveRecord[i][10]) + "' >\n"  + //数量
						"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][org_lngGoodsQuantity]'    value='" + fncDelKannma(saveRecord[i][24]) + "' >\n"  + // 元数量
						"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][curTotalPrice]'           value='" + fncDelKannma(fncDelCurrencySign(saveRecord[i][11])) + "' >\n" + //税抜金額
						"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][strDetailNote]'           value='" + saveRecord[i][13] + "' >\n" + //備考
						"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][curProductPriceForList]'  value='" + fncDelKannma(fncDelCurrencySign(saveRecord[i][14])) + "' >\n" + //単価リスト追加データ
						"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][lngSalesClassCode]'       value='" + saveRecord[i][19] + "' >\n" + //売上区分
						"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][lngSalesClassCodeName]'   value='" + saveRecord[i][20] + "' >\n" + //売上区分（value + 名称）
						"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][dtmDeliveryDate]'         value='" + saveRecord[i][21] + "' >\n" + //納期
						"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][lngReceiveNo]'            value='" + saveRecord[i][25] + "' >\n"  + //受注番号
						"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][lngReceiveDetailNo]'      value='" + saveRecord[i][26] + "' >\n"  + //明細行番号

						"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][lngChkVal]'               value='" + saveRecord[i][27] + "' >\n"  + //対象

						strPC;
	}

	// 仕入管理の場合
	if( typeof(window.parent.HSO.PCFlg) == "object" )
	{
		strHiddenHtml = "<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][strProductCode]'          value='" + saveRecord[i][0] + "' >\n"  + //製品
						"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][lngGoodsPriceCode]'       value='" + saveRecord[i][1] + "' >\n"  + //単価リスト
						"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][strStockSubjectCode]'     value='" + saveRecord[i][2] + "' >\n"  + //仕入科目
						"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][strStockSubjectCodeName]' value='" + saveRecord[i][3] + "' >\n"  + //仕入科目（value + 名称）
						"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][strStockItemCode]'        value='" + saveRecord[i][4] + "' >\n"  + //仕入部品
						"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][strStockItemCodeName]'    value='" + saveRecord[i][5] + "' >\n"  + //仕入部品（value + 名称）
						"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][lngConversionClassCode]'  value='" + saveRecord[i][6] + "' >\n"  + //換算区分
						"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][curProductPrice]'         value='" + fncDelKannma(fncDelCurrencySign(saveRecord[i][7])) + "' >\n"  + //単価
						"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][lngProductUnitCode]'      value='" + saveRecord[i][8] + "' >\n"  + //単位
						"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][lngProductUnitCodeName]'  value='" + saveRecord[i][9] + "' >\n"  + //単位（名称）
						"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][lngGoodsQuantity]'        value='" + fncDelKannma(saveRecord[i][10]) + "' >\n"  + //数量
						"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][org_lngGoodsQuantity]'    value='" + fncDelKannma(saveRecord[i][24]) + "' >\n"  + // 元数量
						"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][curTotalPrice]'           value='" + fncDelKannma(fncDelCurrencySign(saveRecord[i][11])) + "' >\n" + //税抜金額
						"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][lngCarrierCode]'          value='" + saveRecord[i][12] + "' >\n" + //運搬方法
						"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][strDetailNote]'           value='" + saveRecord[i][13] + "' >\n" + //備考
						"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][curProductPriceForList]'  value='" + fncDelKannma(fncDelCurrencySign(saveRecord[i][14])) + "' >\n" + //単価リスト追加データ
						"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][strSerialNo]'             value='" + saveRecord[i][22] + "' >\n" + //No
						"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][dtmDeliveryDate]'         value='" + saveRecord[i][23] + "' >\n" + //納期

						"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][lngChkVal]'               value='" + saveRecord[i][25] + "' >\n" + //対象

						strPC;
	}
	
	return strHiddenHtml;
}


// ---------------------------------------------------------------
/**
* 概要   : 修正のために吐き出されたhidden値および登録ボタンを押した後に
*          戻ってきたhidden値を新規の配列に格納
* 対象   : 「明細枠」があるものすべて
*/
// ---------------------------------------------------------------
function fncDtNewAryForReturn(i)
{
	var aryRecord = new Array();
	aryRecord[0]  = window.parent.DSO.elements("aryPoDitail[" + i + "][strProductCode]").value;          //製品コード
	aryRecord[1]  = window.parent.DSO.elements("aryPoDitail[" + i + "][lngGoodsPriceCode]").value;       //単価リスト

	// Added by Kazushi Saito
	// 計算方法種別の取得
	if( typeof(window.parent.DSO.lngCalcCode) != "undefined" )
	{
		g_lngCalcCode = window.parent.DSO.lngCalcCode.value;
	}
	// Added by Kazushi Saito
	// 小数点以下の処理桁数
	g_lngDecimalCutPoint = 2;
	// 日本円の場合、小数点以下の処理桁数を変更
	if( window.parent.HSO.lngMonetaryUnitCode.value == g_strJpnCurrencySign )
	{
		g_lngDecimalCutPoint = 0;
	}

	//発注管理、仕入管理の場合
	if( typeof(window.parent.HSO.POFlg) == "object" || 
		typeof(window.parent.HSO.PCFlg) == "object" )
	{
		aryRecord[2]  = window.parent.DSO.elements("aryPoDitail[" + i + "][strStockSubjectCode]").value;     //仕入科目
		aryRecord[3]  = window.parent.DSO.elements("aryPoDitail[" + i + "][strStockSubjectCodeName]").value; //仕入科目（value + 名称）
		aryRecord[4]  = window.parent.DSO.elements("aryPoDitail[" + i + "][strStockItemCode]").value;        //仕入部品
		aryRecord[5]  = window.parent.DSO.elements("aryPoDitail[" + i + "][strStockItemCodeName]").value;    //仕入部品（value + 名称）
	}

	aryRecord[6]  = window.parent.DSO.elements("aryPoDitail[" + i + "][lngConversionClassCode]").value;  //換算区分(製品単位計上)
	aryRecord[7]  = window.parent.fncCheckNumberValue(window.parent.DSO.elements("aryPoDitail[" + i + "][curProductPrice]").value, 4); //単価
	aryRecord[8]  = window.parent.DSO.elements("aryPoDitail[" + i + "][lngProductUnitCode]").value;      //単位
	aryRecord[9]  = window.parent.DSO.elements("aryPoDitail[" + i + "][lngProductUnitCodeName]").value;  //単位（名称）
	aryRecord[10] = window.parent.fncCheckNumberValue(window.parent.DSO.elements("aryPoDitail[" + i + "][lngGoodsQuantity]").value, 0, false); //数量
	aryRecord[11] = window.parent.fncCheckNumberValue(window.parent.DSO.elements("aryPoDitail[" + i + "][curTotalPrice]").value, 2); //税抜金額

	// 売上管理、仕入管理
	if( typeof(window.parent.HSO.SCFlg) == "object" || 
		typeof(window.parent.HSO.PCFlg) == "object" )
	{
		aryRecord[24] = window.parent.fncCheckNumberValue(window.parent.DSO.elements("aryPoDitail[" + i + "][org_lngGoodsQuantity]").value, 0, false); // 元数量
	}

	// 売上管理
	if( typeof(window.parent.HSO.SCFlg) == "object" )
	{
		aryRecord[25] = window.parent.DSO.elements( "aryPoDitail[" + i + "][lngReceiveNo]" ).value;       // 受注番号
		aryRecord[26] = window.parent.DSO.elements( "aryPoDitail[" + i + "][lngReceiveDetailNo]" ).value; // 明細行番号
		aryRecord[27] = window.parent.DSO.elements( "aryPoDitail[" + i + "][lngChkVal]" ).value;          // 対象
	}
	// 仕入管理
	if( typeof(window.parent.HSO.PCFlg) == "object" )
	{
		aryRecord[25] = window.parent.DSO.elements( "aryPoDitail[" + i + "][lngChkVal]" ).value; // 対象
	}



	//発注管理、仕入管理の場合
	if( typeof(window.parent.HSO.POFlg) == "object" || 
		typeof(window.parent.HSO.PCFlg) == "object" )
	{
		aryRecord[12] = window.parent.DSO.elements("aryPoDitail[" + i + "][lngCarrierCode]").value;          //運搬方法
	}

	aryRecord[13] = fncCheckReplaceString(window.parent.DSO.elements("aryPoDitail[" + i + "][strDetailNote]").value);           //備考
	aryRecord[14] = window.parent.fncCheckNumberValue(window.parent.DSO.elements("aryPoDitail[" + i + "][curProductPriceForList]").value, 4, false); //単価リスト追加データ

	//仕入管理、売上管理の場合
	if( typeof(window.parent.HSO.PCFlg) == "object" || 
		typeof(window.parent.HSO.SCFlg) == "object" )
	{
		//消費税区分コード
		aryRecord[15] = window.parent.DSO.elements("aryPoDitail[" + i + "][lngTaxClassCode]").value;

		//消費税区分コードが指定されてなかったらデフォルト外税
		if( aryRecord[15] == "" )
		{
			aryRecord[15] = g_strOutTaxClass;
		}

		//消費税区分コードが非課税のとき
		if( aryRecord[15] == g_strFreeTaxClass )
		{
			//消費税コード
			aryRecord[16] = "";
			//消費税額
			aryRecord[17] = "";
		}
		//消費税区分コードが非課税以外のとき
		else
		{
			//消費税コード
			aryRecord[16] = g_curTax;
//2004.07.09 suzukaze update start
//更新時は税額はHidden値をそのままにする　再計算時に税抜金額を元に計算しているため内税値がおかしくなるため
//			aryRecord[17] = fncDtCalTaxPrice(aryRecord[11],aryRecord[15]);
			aryRecord[17] = window.parent.DSO.elements("aryPoDitail[" + i + "][curTaxPrice]").value;  //税額;
//2004.07.09 suzukaze update end

			// 税額のフォーマット
			aryRecord[17]   = window.parent.fncCheckNumberValue(aryRecord[17], 2, true, g_lngDecimalCutPoint, g_lngCalcCode);
			//alert("税区分コード："+aryRecord[15]+"税率："+aryRecord[16]+"税額："+aryRecord[17]);
		}

		aryRecord[18] = window.parent.DSO.elements("aryPoDitail[" + i + "][lngorderdetailno]").value; //行番号
	}
// 2004.03.26 suzukaze update start
//受注管理、発注管理の場合にも行番号をHidden値で覚えておくように修正
	else
	{
		aryRecord[18] = window.parent.DSO.elements("aryPoDitail[" + i + "][lngorderdetailno]").value; //行番号
	}
// 2004.03.26 suzukaze update end

	//受注管理、売上管理の場合
	if( typeof(window.parent.HSO.SOFlg) == "object" || 
		typeof(window.parent.HSO.SCFlg) == "object" )
	{
		aryRecord[19] = window.parent.DSO.elements("aryPoDitail[" + i + "][lngSalesClassCode]").value; //売上区分
		aryRecord[20] = window.parent.DSO.elements("aryPoDitail[" + i + "][lngSalesClassCodeName]").value; //売上区分（value + 名称）
		aryRecord[21] = window.parent.DSO.elements("aryPoDitail[" + i + "][dtmDeliveryDate]").value; //納期
	}

	//発注管理、仕入管理の場合
	if( typeof(window.parent.HSO.POFlg) == "object" || 
		typeof(window.parent.HSO.PCFlg) == "object" )
	{
		aryRecord[22] = window.parent.DSO.elements("aryPoDitail[" + i + "][strSerialNo]").value; //No
		aryRecord[23] = window.parent.DSO.elements("aryPoDitail[" + i + "][dtmDeliveryDate]").value; //納期
	}

	return aryRecord;
}


// ---------------------------------------------------------------
/**
* 概要   : 税抜き合計を算出する
* 対象   : 「明細枠」があるものすべて
*/
// ---------------------------------------------------------------
function fncDtCalTotalPrice()
{
	var ProductPrice  = 0;	// 単価
	var GoodsQuantity = 0;	// 数量
	var TotalPrice    = 0;	// 税抜金額


	// Added by Kazushi Saito
	// 計算方法種別の取得
	if( typeof(window.parent.DSO.lngCalcCode) != "undefined" )
	{
		g_lngCalcCode = window.parent.DSO.lngCalcCode.value;
	}

	
	//換算区分(製品単位計上)
	if( window.parent.DSO.lngConversionClassCode[0].checked )
	{
		//空白チェック
		if( window.parent.DSO.curProductPrice_gs.value  == "" || 
			window.parent.DSO.lngGoodsQuantity_gs.value == "" )
		{
			//税抜金額を空白にする
			window.parent.DSO.curTotalPrice.value = "";
			return false;
		}
		
		//入力枠の[製品単価]から値を得て、通貨記号、カンマを取る
		ProductPrice  = fncDelKannma(fncDelCurrencySign(window.parent.DSO.curProductPrice_gs.value));
		//入力枠の[製品数量]から値を得て、カンマを取る
		GoodsQuantity = fncDelKannma(window.parent.DSO.lngGoodsQuantity_gs.value);
	}
	//荷姿単位計上の場合
	else if( window.parent.DSO.lngConversionClassCode[1].checked )
	{
		//[製品数量]に[カートン数]×[荷姿数量]を反映させる
		if( window.parent.DSO.lngGoodsQuantity_ps.value == "" )
		{
			//[荷姿数量]が空白の場合には、空白を反映
			window.parent.DSO.lngGoodsQuantity_gs.value = "";
		}
		else
		{
			//入力枠の荷姿数量から値を得て、カンマを取る
			GoodsQuantity = fncDelKannma(window.parent.DSO.lngGoodsQuantity_ps.value);

			var CartonQuantity   = fncDelKannma(window.parent.DSO.lngCartonQuantity.value);
			var GoodsQuantity_gs = CartonQuantity * GoodsQuantity;
			window.parent.DSO.lngGoodsQuantity_gs.value = GoodsQuantity_gs;
			window.parent.fncCheckNumber( window.parent.DSO.lngGoodsQuantity_gs , 0 , false );
		}

		//[荷姿単価]または[荷姿数量]が空白の場合
		if( window.parent.DSO.curProductPrice_ps.value  == "" || 
			window.parent.DSO.lngGoodsQuantity_ps.value == "" )
		{
			//[税抜金額]を空白にする
			window.parent.DSO.curTotalPrice.value = "";
			return false;
		}
		else
		{
			//入力枠の荷姿単価から値を得て、通貨記号、カンマを取る
			ProductPrice  = fncDelKannma(fncDelCurrencySign(window.parent.DSO.curProductPrice_ps.value));
		}
	}


	//税抜金額
//	TotalPrice    = ProductPrice * GoodsQuantity;
	TotalPrice    = window.parent.fncVBSNumCalc(ProductPrice, "*", GoodsQuantity);

	//税抜金額を入力枠に反映
	window.parent.DSO.curTotalPrice.value = TotalPrice;
	//税抜金額を小数点以下2桁でフォーマットする
//	window.parent.fncCheckNumber( window.parent.DSO.curTotalPrice , 2);
	window.parent.DSO.curTotalPrice.value = window.parent.fncCheckNumberValue( TotalPrice, 2, true, 2, g_lngCalcCode);

	//仕入管理(PCFlg)、売上管理(SCFlg)の場合、税額を計算
	if( typeof(window.parent.HSO.PCFlg) == "object" || 
		typeof(window.parent.HSO.SCFlg) == "object" )
	{
		window.parent.DSO.curTaxPrice.value = fncDtCalTaxPrice(window.parent.DSO.curTotalPrice.value,
																window.parent.DSO.lngTaxClassCode.value,
																window.parent.DSO.lngTaxCode.value); //消費税額

	}

	// Added by Kazushi Saito
	// 小数点以下の処理桁数
	g_lngDecimalCutPoint = 2;
	// 日本円の場合、小数点以下の処理桁数を変更
	if( window.parent.HSO.lngMonetaryUnitCode.value == g_strJpnCurrencySign )
	{
		g_lngDecimalCutPoint = 0;
	}

	// Added by Kazushi Saito
	// 売上管理(SCFlg)
	// 仕入管理(PCFlg)
	//「税抜金額」、「税額」を小数点以下（2桁）計算処理
	if( typeof(window.parent.HSO.SCFlg) == "object" || 
		typeof(window.parent.HSO.PCFlg) == "object" )
	{

		// 税抜金額
		window.parent.DSO.curTotalPrice.value = window.parent.fncCheckNumberValue(window.parent.DSO.curTotalPrice.value, 2, true, g_lngDecimalCutPoint, g_lngCalcCode);
		// 税額
		window.parent.DSO.curTaxPrice.value   = window.parent.fncCheckNumberValue(window.parent.DSO.curTaxPrice.value, 2, true, g_lngDecimalCutPoint, g_lngCalcCode);


		// 内税の場合の再計算
		if( window.parent.DSO.lngTaxClassCode.value == g_strInTaxClass )
		{
			// 通貨記号・カンマを除去
			curTotalPrice = fncDelKannma(fncDelCurrencySign(window.parent.DSO.curTotalPrice.value));
			curTaxPrice   = fncDelKannma(fncDelCurrencySign(window.parent.DSO.curTaxPrice.value));
			// 税抜金額
			curCalcTotalPrice = window.parent.fncVBSNumCalc(curTotalPrice, "-", curTaxPrice);
			window.parent.DSO.curTotalPrice.value = window.parent.fncCheckNumberValue(curCalcTotalPrice, 2, true, g_lngDecimalCutPoint, g_lngCalcCode);
		}
	}


	// Added by Kazushi Saito
	// 受注管理(SOFlg)
	// 発注管理(POFlg)
	// （日本円の場合のみ）
	//「税抜金額」を小数点以下（2桁）計算処理
	if( typeof(window.parent.HSO.SOFlg) == "object" || 
		typeof(window.parent.HSO.POFlg) == "object")
	{
		// 税抜金額
		window.parent.DSO.curTotalPrice.value = window.parent.fncCheckNumberValue(window.parent.DSO.curTotalPrice.value, 2, true, g_lngDecimalCutPoint, g_lngCalcCode);
	}

	//基準通貨を表示
	fncDtCalStdTotalPrice();
}


// ---------------------------------------------------------------
/**
* 概要   : 製品単位計上のラジオボタンが押されたときの処理
* 対象   : 「明細枠」があるものすべて
*/
// ---------------------------------------------------------------
function fncDtGsChecked()
{
	//単価リストがなかったら選択できない
	if( window.parent.DSO.lngGoodsPriceCode.length == 0  ||
		window.parent.DSO.lngGoodsPriceCode.options[0].text == "(No Data)" )
	{
		window.parent.DSO.lngGoodsPriceCode.disabled = true;
	}
	//単価リストがあったら選択できるようにする
	else
	{
		window.parent.DSO.lngGoodsPriceCode.disabled = false;
	}

	//[製品単価][製品単位][製品数量]を入力、選択できるようにする。
	window.parent.DSO.curProductPrice_gs.disabled    = false;
	window.parent.DSO.lngProductUnitCode_gs.disabled = false;
	window.parent.DSO.lngGoodsQuantity_gs.disabled   = false;

	//[荷姿単価][荷姿単位][荷姿数量]を入力、選択できないようにする
	window.parent.DSO.curProductPrice_ps.disabled    = true;
	window.parent.DSO.lngProductUnitCode_ps.disabled = true;
	window.parent.DSO.lngGoodsQuantity_ps.disabled   = true;

	//税抜金額を再計算
	fncDtCalTotalPrice();
}


// ---------------------------------------------------------------
/**
* 概要   : 荷姿単位計上のラジオボタンが押されたときの処理
* 対象   : 「明細枠」があるものすべて
*/
// ---------------------------------------------------------------
function fncDtPsChecked()
{
	//ボタンを移動してもよいかどうかのフラグ
	var checkFlg = true ;


	//仕入管理、売上管理の場合で、行番号のある場合に、荷姿単位計上に選択してもよいかどうかのチェック
	if( (typeof(window.parent.HSO.PCFlg) == "object"   || 
		 typeof(window.parent.HSO.SCFlg) == "object" ) && 
				g_lngSelIndex != -1                            && 
				saveRecord[g_lngSelIndex][18] != ""            )
	{
		checkFlg = fncDtPsCheckedForPC();
	}

	//選択できない場合には、アラートを出して処理を抜ける
	if( checkFlg == false )
	{
		//換算区分(製品単位計上)にチェックを戻す
		window.parent.DSO.lngConversionClassCode[0].checked = true;

		alert( "[製品数量]÷[カートン入数]が割り切れないため\n選択できません");

		return false;
	}

	//[製品単価]が空白でない場合に、[荷姿単価]に、[製品単価]×[カートン入数]をセットする
	if( window.parent.DSO.curProductPrice_gs.value != "" )
	{
		var ProductPrice_gs  = fncDelKannma(fncDelCurrencySign(window.parent.DSO.curProductPrice_gs.value));
		var CartonQuantity   = fncDelKannma(window.parent.DSO.lngCartonQuantity.value);
		var ProductPrice_ps  = ProductPrice_gs * CartonQuantity;
		window.parent.DSO.curProductPrice_ps.value = ProductPrice_ps;
		window.parent.fncCheckNumber(window.parent.DSO.curProductPrice_ps , 4, true);
	}

	//[荷姿数量]が空白の場合に「1」をセットする
	if( window.parent.DSO.lngGoodsQuantity_ps.value == "" )
	{
		window.parent.DSO.lngGoodsQuantity_ps.value = 1;
	}

	//単価リストを選択できないようにする
	window.parent.DSO.lngGoodsPriceCode.disabled = true;

	//[製品単価][製品単位][製品数量]を入力、選択できないようにする
	window.parent.DSO.curProductPrice_gs.disabled    = true;
	window.parent.DSO.lngProductUnitCode_gs.disabled = true;
	window.parent.DSO.lngGoodsQuantity_gs.disabled   = true;

	//[荷姿単価][荷姿単位][荷姿数量]を入力、選択できるようにする
	window.parent.DSO.curProductPrice_ps.disabled    = false;
	window.parent.DSO.lngProductUnitCode_ps.disabled = false;
	window.parent.DSO.lngGoodsQuantity_ps.disabled   = false;

	//税抜金額を再計算
	fncDtCalTotalPrice();
}


// ---------------------------------------------------------------
/**
*  概要   :荷姿単位計上に選択できるかどうかのチェック
*  対象   :仕入管理、売上管理で行番号のある場合
*/
// ---------------------------------------------------------------
function fncDtPsCheckedForPC()
{
	//カートン入数
	var CartonQuantity   = fncDelKannma(window.parent.DSO.lngCartonQuantity.value);

	//デフォルトの製品数量
	var GoodsQuantity_gs_defalt = fncDtGSGoodsQuantityDefalt();

	//カートン数量が0または空のとき選択できない
	if( CartonQuantity == "" || CartonQuantity == 0 )
	{
		return false;
	}

	//荷姿数量(製品数量 / カートン入り数)
	var GoodsQuantity_ps = parseInt( GoodsQuantity_gs_defalt / CartonQuantity );


	//デフォルトの製品数量とカートン数量が整数で割り切れれば選択できる(製品数量 == 荷姿数量×カートン入数)
	if( GoodsQuantity_gs_defalt == (GoodsQuantity_ps * CartonQuantity ) )
	{
		//[荷姿数量]に[製品数量]÷[カートン入数]をセット
		window.parent.DSO.lngGoodsQuantity_ps.value = fncAddKannma( GoodsQuantity_ps );
		return true;
	}
	else
	{
		return false;
	}
}


// ---------------------------------------------------------------
/**
*  概要   :荷姿数量の入力チェック
*  対象   :仕入管理、売上管理
*			行番号のある場合で、荷姿数量にチェックされている場合
*/
// ---------------------------------------------------------------
function fncDtPSGoodsQuantityForPC()
{

	// 選択されていない時は処理しない
	if( g_lngSelIndex == -1 )
	{
		return false;
	}
	// 行番号が無い場合は処理しない
	if( typeof(saveRecord[g_lngSelIndex][18]) == "undefined" ||	saveRecord[g_lngSelIndex][18] == "" )
	{
		return false;
	}

	// 管理を選別
	switch (fncGetKindOfManagement())
	{
		// 売上管理の場合
		case g_strSCKindOfManagement:
			strCheckCode = window.parent.HSO.strReceiveCode.value;	// 「受注No.」を取得
			break;
		// 仕入管理の場合
		case g_strPCKindOfManagement:
			strCheckCode = window.parent.HSO.strOrderCode.value;	// 「発注No.」を取得
			break;
		default:
			return false;
	}
	// 受注No.、発注No. の無いデータの場合はチェックしない
	if( window.parent.HSO.strPageCondition.value == "regist" && strCheckCode == "" )
	{
		return false;
	}
	
	//カートン入り数
	var CartonQuantity   = fncDelKannma(window.parent.DSO.lngCartonQuantity.value);
	//デフォルトの製品数量
	var GoodsQuantity_gs_defalt = fncDtGSGoodsQuantityDefalt();
	//入力されたに荷姿数量
	var GoodsQuantity_ps = parseInt(fncDelKannma(window.parent.DSO.lngGoodsQuantity_ps.value));

	//カートン数量が0または空のときチェック終了
	if( CartonQuantity == "" || CartonQuantity == 0 )
	{
		return false;
	}

	//荷姿数量に入力できる上限
	var GoodsQuantity_ps_max = parseInt(GoodsQuantity_gs_defalt / CartonQuantity) ;

	//入力された荷姿数量が最大値以下の場合、アナウンスしない
	if( GoodsQuantity_ps <= GoodsQuantity_ps_max )
	{
		return false;
	}
	
	// 新規登録時
	if( window.parent.HSO.strPageCondition.value == "regist" )
	{
		// 初期値を設定
		window.parent.DSO.lngGoodsQuantity_ps.value = fncAddKannma(GoodsQuantity_ps_max);
		
		// エラーメッセージを出力
		alert("荷姿数量に入力できる上限は、" + GoodsQuantity_ps_max + "です");
		
		//
		window.parent.DSO.lngGoodsQuantity_ps.select();

	}
	// 修正時
	else
	{
		retVal = confirm("登録済みの荷姿数量は " + GoodsQuantity_ps_max + " です\n任意で " + GoodsQuantity_ps + " に変更しますか？");
		if( retVal == false )
		{
			// 任意値をセット
			window.parent.DSO.lngGoodsQuantity_ps.value = fncAddKannma(GoodsQuantity_ps_max);
		}
	}
	
}


// ---------------------------------------------------------------
/**
*  概要   :製品数量の入力チェック
*  対象   :仕入管理、売上管理
*			行番号のある場合で、製品数量にチェックされている場合
*/
// ---------------------------------------------------------------
function fncDtGSGoodsQuantityForPC()
{
	
	// 選択されていない時は処理しない
	if( g_lngSelIndex == -1 )
	{
		return false;
	}
	// 行番号が無い場合は処理しない
	if( typeof(saveRecord[g_lngSelIndex][18]) == "undefined" ||	saveRecord[g_lngSelIndex][18] == "" )
	{
		return false;
	}

	// 管理を選別
	switch (fncGetKindOfManagement())
	{
		// 売上管理の場合
		case g_strSCKindOfManagement:
			strCheckCode = window.parent.HSO.strReceiveCode.value;	// 「受注No.」を取得
			break;
		// 仕入管理の場合
		case g_strPCKindOfManagement:
			strCheckCode = window.parent.HSO.strOrderCode.value;	// 「発注No.」を取得
			break;
		default:
			return false;
	}
	// 受注No.、発注No. の無いデータの場合はチェックしない
	if( window.parent.HSO.strPageCondition.value == "regist" && strCheckCode == "" )
	{
		return false;
	}

	// デフォルトの製品数量
	var GoodsQuantity_gs_defalt = parseInt(fncDtGSGoodsQuantityDefalt());
	// 入力されたに製品数量
	var GoodsQuantity_gs = parseInt(fncDelKannma(window.parent.DSO.lngGoodsQuantity_gs.value));

	// 入力された製品数量がデフォルト値以下の場合、アナウンスしない
	if( GoodsQuantity_gs <= GoodsQuantity_gs_defalt )
	{
		return false;
	}

	// 新規登録時
	if( window.parent.HSO.strPageCondition.value == "regist" )
	{
		// 初期値を設定
		window.parent.DSO.lngGoodsQuantity_gs.value = fncAddKannma(GoodsQuantity_gs_defalt);

		// エラーメッセージを出力
		alert("製品数量に入力できる上限は、" + GoodsQuantity_gs_defalt + " です");
		
		//
		window.parent.DSO.lngGoodsQuantity_gs.select();

	}
	// 修正時
	else
	{
		retVal = confirm("登録済みの製品数量は " + GoodsQuantity_gs_defalt + " です\n任意で " + GoodsQuantity_gs + " に変更しますか？");
		if( retVal == false )
		{
			// 任意値をセット
			window.parent.DSO.lngGoodsQuantity_gs.value = fncAddKannma(GoodsQuantity_gs_defalt);
		}
	}

}


// ---------------------------------------------------------------
/**
*  概要   :管理画面の種別判定
*  対象   :汎用
*/
// ---------------------------------------------------------------
function fncGetKindOfManagement()
{
	// 管理種別
	var strCheckStatus = "";
	
	// 受注管理
	if( typeof(window.parent.HSO.SOFlg) == "object" )
	{
		strCheckStatus = g_strSOKindOfManagement;
	}
	// 売上管理
	if( typeof(window.parent.HSO.SCFlg) == "object" )
	{
		strCheckStatus = g_strSCKindOfManagement;
	}
	// 発注管理
	if( typeof(window.parent.HSO.POFlg) == "object" )
	{
		strCheckStatus = g_strPOKindOfManagement;
	}
	// 仕入管理
	if( typeof(window.parent.HSO.PCFlg) == "object" )
	{
		strCheckStatus = g_strPCKindOfManagement;
	}

	return strCheckStatus;
}


// ---------------------------------------------------------------
/**
*  概要   :行番号からデフォルトの製品数量を得る
*  対象   :仕入管理で行番号のある場合
*/
// ---------------------------------------------------------------
function fncDtGSGoodsQuantityDefalt()
{
	//デフォルトの製品数量
	var GoodsQuantity_gs_defalt = 0;

	//ループ処理のインデックス
	var i = 0;

	//製品コードがあるかぎりループ
	while( window.parent.DSO.elements("aryPoDitail[" + i + "][strProductCode]") != null )
	{
		//Hiddenで吐き出された行番号と選択された行番号が同一の場合に製品数量を得る
		if( window.parent.DSO.elements("aryPoDitail[" + i + "][lngorderdetailno]").value != saveRecord[g_lngSelIndex][18] )
		{
			i++;
			continue;
		}

		// 売上の場合のみ
		if( typeof(window.parent.HSO.SCFlg) == "object" )
		{
			// 受注番号も一致しているかを確認する（複数受注番号が在り得る為）
			if( window.parent.DSO.elements("aryPoDitail[" + i + "][lngReceiveNo]").value != saveRecord[g_lngSelIndex][25] )
			{
				i++;
				continue;
			}
		}



		//換算区分が製品単位計上の場合
		if( window.parent.DSO.elements("aryPoDitail[" + i + "][lngConversionClassCode]").value == "gs" )
		{
			//デフォルトの製品数量
			var GoodsQuantity_gs_defalt = window.parent.DSO.elements("aryPoDitail[" + i + "][lngGoodsQuantity]").value
		}
		//換算区分が荷姿単位計上の場合
		else if( window.parent.DSO.elements("aryPoDitail[" + i + "][lngConversionClassCode]").value == "ps" )
		{
			//デフォルトの荷姿数量
			var GoodsQuantity_ps_defalt = window.parent.DSO.elements("aryPoDitail[" + i + "][lngGoodsQuantity]").value
			//カートン入数
			var CartonQuantity   = fncDelKannma(window.parent.DSO.lngCartonQuantity.value);
			//デフォルトの製品数量(デフォルトの荷姿数量×カートン入数)
			var GoodsQuantity_gs_defalt = GoodsQuantity_ps_defalt * CartonQuantity;
		}
		//ループ処理を終了
		break;

	}

	return GoodsQuantity_gs_defalt;
}


// ---------------------------------------------------------------
/**
*  概要   : [製品単価]に[荷姿単価]÷[カートン入数]をセット
*  対象   : 受注、売上、発注、仕入管理で[荷姿単位計上]のときに[荷姿単価]からonBlurしたとき
*           各明細行を選択したとき
*/
// ---------------------------------------------------------------
function fncDtPSProductPrice()
{
	//荷姿単価
	var ProductPrice_ps = fncDelKannma(fncDelCurrencySign(window.parent.DSO.curProductPrice_ps.value));
	//カートン入り数
	var CartonQuantity  = fncDelKannma(window.parent.DSO.lngCartonQuantity.value);

	//カートン数量が0または空のとき処理終了
	if( CartonQuantity == "" || CartonQuantity == 0 )
	{
		return false;
	}

	//[製品単価]に[荷姿単価]÷[カートン入数]をセット
	window.parent.DSO.curProductPrice_gs.value = window.parent.fncCheckNumberValue((ProductPrice_ps / CartonQuantity), 4);
}


// ---------------------------------------------------------------
/**
* 概要   : 総合計金額を算出する
* 対象   : 「明細枠」があるものすべて
*/
// ---------------------------------------------------------------
function fncDtCalAllTotalPrice()
{
	if( saveRecord.length == 0 )
	{
		//明細行がない場合は総合計金額をからにする
		window.parent.DSO.curAllTotalPrice.value = "";
	}
	else
	{
		//総合計金額
		var AllTotalPrice = 0;
		//明細行の数
		var saveRecordLength = saveRecord.length;

		for( i = 0; i < saveRecordLength; i++ )
		{
			// 売上
			if( typeof(window.parent.HSO.SCFlg) == "object" )
			{
				// 直登録ではない場合
				if( window.parent.document.all.lngDirectRegistFlag.value == 0 )
				{
					if( saveRecord[i][27] == 1 )
					{
						AllTotalPrice += parseInt( 10000 * fncDelKannma( fncDelCurrencySign( saveRecord[i][11] ) ) );
					}
				}
				// 直登録の場合
				else
				{
					AllTotalPrice += parseInt( 10000 * fncDelKannma( fncDelCurrencySign( saveRecord[i][11] ) ) );
				}
			}

			// 仕入
			if( typeof(window.parent.HSO.PCFlg) == "object" )
			{
				// 直登録ではない場合
				if( window.parent.document.all.lngDirectRegistFlag.value == 0 )
				{
					if( saveRecord[i][25] == 1 )
					{
						AllTotalPrice += parseInt( 10000 * fncDelKannma( fncDelCurrencySign( saveRecord[i][11] ) ) );
					}
				}
				// 直登録の場合
				else
				{
					AllTotalPrice += parseInt( 10000 * fncDelKannma( fncDelCurrencySign( saveRecord[i][11] ) ) );
				}
			}

			// 受注・発注
			if( typeof(window.parent.HSO.SOFlg) == "object" ||
				typeof(window.parent.HSO.POFlg) == "object" )
			{
				AllTotalPrice += parseInt( 10000 * fncDelKannma( fncDelCurrencySign( saveRecord[i][11] ) ) );
			}
		}

		AllTotalPrice = AllTotalPrice / 10000 ;


		//総合計金額を入力枠に反映
		window.parent.DSO.curAllTotalPrice.value = AllTotalPrice;
		//総合計金額をフォーマットする
		window.parent.fncCheckNumber( window.parent.DSO.curAllTotalPrice , 2 );
	}
}


// ---------------------------------------------------------------
/**
* 概要   : 基準通貨を算出する
* 対象   : 発注管理、仕入管理の場合で通貨が日本円以外
*/
// ---------------------------------------------------------------
function fncDtCalStdTotalPrice()
{
	//発注管理、仕入管理の場合のみ
	if( typeof(window.parent.HSO.POFlg) == "object" || 
		typeof(window.parent.HSO.PCFlg) == "object" )
	{
		//日本円のときは表示させない
		if( window.parent.HSO.lngMonetaryUnitCode.value == g_strJpnCurrencySign )
		{
			//[基準通貨]を空白にする
			window.parent.DSO.curStdTotalPrice.value = "";
			//サブウィンドウの[税額]を空白にする
			window.parent.DSO.curSubTaxPrice.value = "";
			//サブウィンドウの[合計金額]を空白にする
			window.parent.DSO.curTotalStdAmt.value = "";
			return false;
		}

		//[税抜金額]を取得
		var TotalPrice = parseFloat(fncDelKannma(fncDelCurrencySign(window.parent.DSO.curTotalPrice.value)));

		//[税抜金額]がなかったら処理終了
		if( TotalPrice == "" || TotalPrice == 0 || isNaN(TotalPrice) )
		{
			//[基準通貨]を空白にする
			window.parent.DSO.curStdTotalPrice.value = "";
			//サブウィンドウの[税額]を空白にする
			window.parent.DSO.curSubTaxPrice.value = "";
			//サブウィンドウの[合計金額]を空白にする
			window.parent.DSO.curTotalStdAmt.value = "";
	
			return false;
		}

		//[換算レート]を取得
		var ConversionRate = fncDelKannma(fncDelCurrencySign(window.parent.HSO.curConversionRate.value));
	
		//[基準通貨]を求めてフォーマット
		var StdTotalPrice = window.parent.fncCheckNumberValue(TotalPrice * ConversionRate, 2 ,false);
	
		//[基準通貨]に円マークをつける
		window.parent.DSO.curStdTotalPrice.value = g_strJpnCurrencySign + " " + StdTotalPrice;
	
		//サブウインドの表示
		//発注管理の場合
		if( typeof(window.parent.HSO.POFlg) == "object" )
		{
			window.parent.DSO.curTotalStdAmt.value = window.parent.DSO.curStdTotalPrice.value; //[合計金額]
		}
		//仕入管理の場合
		else if( typeof(window.parent.HSO.PCFlg) == "object" )
		{
			//消費税額
			var TaxPrice = parseFloat(fncDelKannma(fncDelCurrencySign(window.parent.DSO.curTaxPrice.value)));
	
			//非課税の場合
			if( window.parent.DSO.lngTaxClassCode.value == 1 )
			{
				window.parent.DSO.curSubTaxPrice.value = "" ;                                    //[税額]
				//[合計金額]=基準通貨と同様
				window.parent.DSO.curTotalStdAmt.value = window.parent.DSO.curStdTotalPrice.value ; //[合計金額]
			}
			//外税の場合
			else if( window.parent.DSO.lngTaxClassCode.value == 2 )
			{
				//[税額]=[消費税額]×[換算レート]
				var SubTaxPrice = window.parent.fncCheckNumberValue(TaxPrice * ConversionRate, 2 ,false);
				window.parent.DSO.curSubTaxPrice.value = g_strJpnCurrencySign + " " + SubTaxPrice ; //[税額]
				//[合計金額]=[税抜価格]+[消費税額]
				var TotalStdAmt = window.parent.fncCheckNumberValue(((TotalPrice + TaxPrice) * ConversionRate), 2 ,false);
				window.parent.DSO.curTotalStdAmt.value = g_strJpnCurrencySign + " " + TotalStdAmt ; //[合計金額]
			}
			//内税の場合
			else if( window.parent.DSO.lngTaxClassCode.value == 3 )
			{
				//[税額]=[消費税額]×[換算レート]
				var SubTaxPrice = window.parent.fncCheckNumberValue(TaxPrice * ConversionRate, 2 ,false);
				window.parent.DSO.curSubTaxPrice.value = g_strJpnCurrencySign + " " + SubTaxPrice ;	//[税額]
				//[合計金額]=基準通貨と同様
				window.parent.DSO.curTotalStdAmt.value = window.parent.DSO.curStdTotalPrice.value ;	//[合計金額]
			}
		}
	}
}


// ---------------------------------------------------------------
/**
* 概要   : [通貨]の選択変更による処理
* 対象   : 「明細枠」があるものすべて
*/
// ---------------------------------------------------------------
function fncHdMonetaryUnitCode()
{
	//日本円の場合	
	if( window.parent.HSO.lngMonetaryUnitCode.value == g_strJpnCurrencySign )
	{
		//[レートタイプ]を選択できないようにする
		window.parent.HSO.lngMonetaryRateCode.disabled = true;

		//[レートタイプ]を空白にする
		window.parent.HSO.lngMonetaryRateCode.value = g_strNoneMonetaryRate;

		//[換算レート]を編集できないようにする
		window.parent.HSO.curConversionRate.contentEditable = 'false';

		//[換算レート]をクリアする
		window.parent.HSO.curConversionRate.value = "1.000000";
	}
	//日本円以外の場合
	else
	{
		//[レートタイプ]を選択できるようにする
		window.parent.HSO.lngMonetaryRateCode.disabled = false;

		//仕入管理と売上管理の場合
		if( (typeof(window.parent.HSO.PCFlg) == "object"   || 
			 typeof(window.parent.HSO.SCFlg) == "object" ) )
		{
			//[レートタイプ]のデフォルトを「TTM」にする
			window.parent.HSO.lngMonetaryRateCode.value = g_strTtmMonetaryRate;
			// 税区分を「非課税」にする
			window.parent.DSO.lngTaxClassCode.value = g_strFreeTaxClass;
	
		}
		//発注管理と受注管理の場合
		else
		{
			//[レートタイプ]のデフォルトを「社内レート」にする
			window.parent.HSO.lngMonetaryRateCode.value = g_strOutTaxClass;
		}

		//[換算レート]を編集できるようにする
		window.parent.HSO.curConversionRate.contentEditable = 'true';
	}

	//[製品単価][荷姿単価]をクリア
	window.parent.DSO.curProductPrice_gs.value = "" ;
	window.parent.DSO.curProductPrice_ps.value = "" ;

	//税抜き合計をクリア
	window.parent.DSO.curTotalPrice.value = "" ;

	//発注管理、仕入管理の場合
	if( typeof(window.parent.HSO.POFlg) == "object" || 
		typeof(window.parent.HSO.PCFlg) == "object" )
	{
		//基準通貨をクリア
		window.parent.DSO.curStdTotalPrice.value = "" ;
	}
}


// ---------------------------------------------------------------
/**
* 概要   : 明細行を追加した場合に[通貨][レートタイプ]を選択できなくし、
*           明細行がない場合に[通貨][レートタイプ]を選択できるようにする。
*           ただし、[レートタイプ]を選択できるのは、日本円以外のとき。
* 対象   : 「明細枠」があるものすべて
*/
// ---------------------------------------------------------------
function fncHdMonetaryUnitCheck()
{
	if (saveRecord.length == 0)
	{
		//[通貨]を編集できるようにする
		window.parent.HSO.lngMonetaryUnitCode.disabled = false;
	}
	else
	{
		//[通貨]を編集できないようにする
		window.parent.HSO.lngMonetaryUnitCode.disabled = true;
	}
}

// ---------------------------------------------------------------
/**
* 概要	：レートタイプの状態変化
*
* 対象	：
*/
// ---------------------------------------------------------------
function fncHdMonetaryRateCheck()
{
	// [レートタイプ]の選択状態を変更
	if (saveRecord.length == 0)
	{
		// [通貨]が日本円の場合、無視する
		if( window.parent.HSO.lngMonetaryUnitCode.value == g_strJpnCurrencySign )
		{
			return false;
		}
		// [レートタイプ]を選択できるように
		window.parent.HSO.lngMonetaryRateCode.disabled = false;
	}
	else
	{
		// [レートタイプ]を選択できないように
		window.parent.HSO.lngMonetaryRateCode.disabled = true;
	}
}


// ---------------------------------------------------------------
/**
* 概要   : 通貨を選択したら、概算レートに反映
* 対象   : 「明細枠」があるものすべて
*/
// ---------------------------------------------------------------
function fncCalConversionRate()
{
	//[通貨]が日本円だったら、キャンセル
	if( window.parent.HSO.lngMonetaryUnitCode.value == g_strJpnCurrencySign ) return false;

	//[レートタイプ]の空白を選択したら、「社内」を選択したことにする
	if( window.parent.HSO.lngMonetaryRateCode.value == g_strNoneMonetaryRate )
	{
		window.parent.HSO.lngMonetaryRateCode.value = g_strDefMonetaryRate;
	}

	//[計上日]が空の場合に、現在の日付けを反映
	if( window.parent.HSO.dtmOrderAppDate.value == "" )
	{
		window.parent.HSO.dtmOrderAppDate.value = fncYYMMDD();
	}

	//[換算レート]を[レートタイプ][通貨][計上日]をもとに反映
	subLoadMasterValue('cnConversionRate',
					 window.parent.HSO.lngMonetaryRateCode,
					 window.parent.HSO.curConversionRate,
					 Array(window.parent.HSO.lngMonetaryRateCode.value,
						   window.parent.HSO.lngMonetaryUnitCode.value,
						   window.parent.HSO.dtmOrderAppDate.value),
						   window.document.objDataSourceSetting);
}


// ---------------------------------------------------------------
/**
* 概要    ： フォームDSOに吐き出されたhidden値から入力枠のデータを作成
*            Detailのタブを押したときに実行される
* 対象   : 「明細枠」があるものすべて
*/
// ---------------------------------------------------------------
function fncDtHtml()
{

	//通貨記号の設定
	window.parent.fncCheckNumberCurrencySign( window.parent.HSO.lngMonetaryUnitCode.value );

	//仕入管理、売上管理の場合
	if( typeof(window.parent.HSO.PCFlg) == "object" || 
		typeof(window.parent.HSO.SCFlg) == "object" )
	{
		//[計上日]が空の場合に、現在の日付けを反映
		if( window.parent.HSO.dtmOrderAppDate.value == "" )
		{
			window.parent.HSO.dtmOrderAppDate.value = fncYYMMDD();
		}

		//[税コード]を[計上日]をもとに反映
		subLoadMasterValue('cnTaxCode',
						 null,
						 window.parent.DSO.TaxCode,
						 Array(window.parent.HSO.dtmOrderAppDate.value),
						 window.document.objDataSourceSetting );

		//[税率]を[計上日]をもとに反映
		subLoadMasterValue('cnTaxCodeValue',
						 null,
						 window.parent.DSO.zeiritsu,
						 Array(window.parent.HSO.dtmOrderAppDate.value),
						 window.document.objDataSourceSetting14,
						 14 );
	}
	//仕入管理以外のとき
	else
	{
	//タブを押したときのつづき
	//仕入管理の場合には、subLoadMasterValue('cnTaxCodeValue',...)→fncDtHtmlForPC()のあと実施
	fncDtHtml2();
	}
	
	//alert(window.parent.DSO.TaxCode.value);
}


// ---------------------------------------------------------------
/**
* 概要    ： フォームDSOに吐き出されたhidden値から入力枠のデータを作成つづき
*            Detailのタブを押したときに実行される
* 対象   : 「明細枠」があるものすべて
*/
// ---------------------------------------------------------------
function fncDtHtml2()
{
	//通貨基準を再計算
	fncDtCalStdTotalPrice();

	//
	if( g_lngReturnFlg == -1 || typeof(window.parent.DSO.elements("aryPoDitail[0][strProductCode]")) == "undefined" ) return null;

	//ループの初期値
	var i = 0;

	while (window.parent.DSO.elements("aryPoDitail[" + i + "][strProductCode]") != null)
	{
		//フォームDSOに戻ってきたhidden値を新規の配列に格納
		var aryRecord = fncDtNewAryForReturn(i);

		//配列に格納
		saveRecord.push(aryRecord);

		i++;
	}

	//処理をするのは一度だけのため、フラグを設定
	g_lngReturnFlg = -1;

	//明細枠を再表示
	fncDtDisplay();//
}


// ---------------------------------------------------------------
/**
* 概要    : Detailタブをおしたときの処理
* 対象    : 仕入管理、売上管理
* 備考    : 遅延がおきるため、サブロード関数の処理が終わったあとに処理開始
*/
// ---------------------------------------------------------------
function fncDtHtmlForPC()
{
	//税コードをグローバル変数に格納
	// g_lngTaxClassCode = window.parent.DSO.zeicode.value;
	g_lngTaxCode = window.parent.DSO.TaxCode.value;

	//税率をグローバル変数に格納
	g_curTax = window.parent.fncCheckNumberValue(window.parent.DSO.zeiritsu.value, 3, false);
	//alert("税コード："+g_lngTaxCode+"税率："+g_curTax);

	// Added by Kazushi Saito
	// 計算方法種別の取得
	if( typeof(window.parent.DSO.lngCalcCode) != "undefined" )
	{
		g_lngCalcCode = window.parent.DSO.lngCalcCode.value;
	}
	// Added by Kazushi Saito
	// 小数点以下の処理桁数
	g_lngDecimalCutPoint = 2;
	// 日本円の場合、小数点以下の処理桁数を変更
	if( window.parent.HSO.lngMonetaryUnitCode.value == g_strJpnCurrencySign )
	{
		g_lngDecimalCutPoint = 0;
	}

	//タブを押すのが初回ではなく、明細行があるとき
	//消費税率が変更された可能性があるため税率と税金を再計算
	if( g_lngReturnFlg == -1 && saveRecord.length > 0 )
	{
		for( i=0 ; i < saveRecord.length ; i++ )
		{
			//消費税区分コードが非課税以外のとき
			if( saveRecord[i][15] != g_strFreeTaxClass )
			{
				saveRecord[i][16] = g_curTax;
				saveRecord[i][17] = fncDtCalTaxPrice(saveRecord[i][11], saveRecord[i][15]);

				// 税額のフォーマット
				saveRecord[i][17]   = window.parent.fncCheckNumberValue(saveRecord[i][17], 2, true, g_lngDecimalCutPoint, g_lngCalcCode);

			}
		}
	}
	//入力枠の税金を再計算
	fncDtCalTaxPrice2();

	//タブを押したときのつづき
	fncDtHtml2();
}


// ---------------------------------------------------------------
/**
* 概要    ： 単価リストを表示
* 対象   : 「明細枠」があるものすべて
*/
// ---------------------------------------------------------------
function fncDtGoodsPriceList()
{
	//発注管理、仕入管理の場合
	if( typeof(window.parent.HSO.POFlg) == "object" || 
		typeof(window.parent.HSO.PCFlg) == "object" )
	{
		//製品コードが選択されていなければ、終了
		if( window.parent.DSO.strProductCode.value == ""            ||
			isNaN(window.parent.DSO.strProductCode.value)           ||
			window.parent.DSO.strStockSubjectCode.value       == 0  ||
			window.parent.DSO.strStockItemCode.selectedIndex  == 0  ||
			window.parent.DSO.strStockItemCode.selectedIndex  == -1 ||
			isNaN(window.parent.DSO.strStockItemCode.value)         ) return false;
	
		subLoadMasterOption( "cnProductPrice",
			 window.parent.DSO.strStockItemCode, 
			 window.parent.DSO.lngGoodsPriceCode,
			 Array(window.parent.DSO.strProductCode.value,
				   window.parent.DSO.strStockSubjectCode.value,
				   window.parent.DSO.strStockItemCode.value,
				   window.parent.HSO.lngMonetaryUnitCode.value),
			 window.document.objDataSourceSetting11,11);
	}

	//受注管理、売上管理の場合
	if( typeof(window.parent.HSO.SOFlg) == "object" || 
		typeof(window.parent.HSO.SCFlg) == "object" )
	{
		//製品コード、売上区分が選択されていなければ、終了
		if( window.parent.DSO.strProductCode.value == ""            ||
			isNaN(window.parent.DSO.strProductCode.value)           ||
			window.parent.DSO.lngSalesClassCode.value == 0 ) return false;
	
		subLoadMasterOption( "cnProductPriceForSO",
			 window.parent.DSO.lngSalesClassCode, 
			 window.parent.DSO.lngGoodsPriceCode,
			 Array(window.parent.DSO.strProductCode.value,
				   window.parent.DSO.lngSalesClassCode.value,
				   window.parent.HSO.lngMonetaryUnitCode.value),
			 window.document.objDataSourceSetting11,
			 11);
	}

}


// ---------------------------------------------------------------
/**
* 概要    ： 単価リストを表示(明細行を選択した場合)
* 対象   : 「明細枠」があるものすべて
*/
// ---------------------------------------------------------------
function fncDtGoodsPriceList2()
{
	//発注管理、仕入管理の場合
	if( typeof(window.parent.HSO.POFlg) == "object" || 
		typeof(window.parent.HSO.PCFlg) == "object" )
	{
		//製品コードが選択されていなければ、終了
		if (saveRecord[g_lngSelIndex][0] == ""           ||
			saveRecord[g_lngSelIndex][2]       == 0 ||
			saveRecord[g_lngSelIndex][4]  == -1 ) return false;
	
		subLoadMasterOption( "cnProductPrice",
			 window.parent.DSO.strStockItemCode, 
			 window.parent.DSO.lngGoodsPriceCode,
			 Array(saveRecord[g_lngSelIndex][0],
				   saveRecord[g_lngSelIndex][2],
				   saveRecord[g_lngSelIndex][4],
				   window.parent.HSO.lngMonetaryUnitCode.value),
			 window.document.objDataSourceSetting12,12);
	}

	//受注管理、売上管理の場合
	if( typeof(window.parent.HSO.SOFlg) == "object" || 
		typeof(window.parent.HSO.SCFlg) == "object" )
	{
		//製品コード、売上区分が選択されていなければ、終了
		if( saveRecord[g_lngSelIndex][0] == ""            ||
			saveRecord[g_lngSelIndex][19] == 0 ) return false;
	
		subLoadMasterOption( "cnProductPriceForSO",
			 window.parent.DSO.lngSalesClassCode, 
			 window.parent.DSO.lngGoodsPriceCode,
			 Array(saveRecord[g_lngSelIndex][0],
				   saveRecord[g_lngSelIndex][19],
				   window.parent.HSO.lngMonetaryUnitCode.value),
			 window.document.objDataSourceSetting12,
			 12);
	}
}


// ---------------------------------------------------------------
/**
* 概要    ： 単価リストを選択したら、製品単価に反映
* 対象   : 「明細枠」があるものすべて
*/
// ---------------------------------------------------------------
function fncDtGoodsPriceToProductPrice()
{
	//単価リストがなかったら、EXIT
	if( window.parent.DSO.lngGoodsPriceCode.selectedIndex == -1 ) return false;

	//荷姿単位計上の場合、EXIT
	if( window.parent.DSO.lngConversionClassCode[1].checked ) return false;

	//単価リストの値を取得
	var GoodsPrice = window.parent.DSO.lngGoodsPriceCode[window.parent.DSO.lngGoodsPriceCode.selectedIndex].text;

	//(No Data)だった場合
	if( isNaN(GoodsPrice) )
	{
		window.parent.DSO.curProductPrice_gs.value = 0;
	}

	//値がある場合
	else
	{
		window.parent.DSO.curProductPrice_gs.value = GoodsPrice;
	}

	//製品単価をフォーマットする
	window.parent.fncCheckNumber(window.parent.DSO.curProductPrice_gs, 4);

	//税抜金額を再計算
	fncDtCalTotalPrice();
}


// ---------------------------------------------------------------
/**
// 概要    ： [単価リスト]追加データのチェック
// 対象   : 「明細枠」があるものすべて
*/
// ---------------------------------------------------------------
function fncProductPriceForList()
{
	//製品単価
	var productPrice_gs = fncDelKannma(fncDelCurrencySign(window.parent.DSO.curProductPrice_gs.value));

	//荷姿単価
	var productPrice_ps = fncDelKannma(fncDelCurrencySign(window.parent.DSO.curProductPrice_ps.value));

	//カートン入数
	var cartonQuantity  = fncDelKannma(window.parent.DSO.lngCartonQuantity.value);

	//カートン数量が0または空のとき単価リストを登録しない
	if( cartonQuantity == "" || cartonQuantity == 0 )
	{
		productPriceForList = "";
	}
	//荷姿単価÷カートン入数が製品単価に等しいとき単価リストを登録する
	else if( (productPrice_ps / cartonQuantity) == productPrice_gs )
	{
		productPriceForList = window.parent.DSO.curProductPrice_gs.value;
	}
	//その他の場合、単価リストを登録しない
	else
	{
		productPriceForList = "";
	}

	return productPriceForList;
}


// ---------------------------------------------------------------
/**
* 概要       : 税額の計算
* 対象       : 仕入管理、売上管理
* @param     : zeinuki,  [string型], 税抜金額
*             TaxClassCode,  [int型]   , 税コード
* @return    : str, [string型], 税額
*/
// ---------------------------------------------------------------
function fncDtCalTaxPrice(zeinuki, TaxClassCode)
{
	var str="";

	//非課税以外で引数がすべてある場合に税額を計算

	if (zeinuki != "" && TaxClassCode != 1)
	{
		//税抜合計からカンマと通貨記号を取る
		str = fncDelCurrencySign(fncDelKannma(zeinuki));

		//税区分が外税のとき
		if (TaxClassCode == 2 )
		{
			str = str * g_curTax;
		}
		//税区分が内税のとき
		else if (TaxClassCode == 3 )
		{
			str = (str * g_curTax)/(1 + parseFloat(g_curTax));
		}

		//税額を求めてフォーマット
		str = window.parent.fncCheckNumberValue(str, 2);

	}

	return str;
}


// ---------------------------------------------------------------
/**
* 概要   : 税額の計算２（[税区分]を変更したとき）
* 対象   : 仕入管理、売上管理
* 注意   : 引数があればアラートを出す
* @param   : object, [object型], 税区分
*/
// ---------------------------------------------------------------
function fncDtCalTaxPrice2(object)
{
	
	//[税抜金額]
	var zeinuki  = window.parent.DSO.curTotalPrice.value;

	//[税区分]
	g_lngTaxClassCode  = window.parent.DSO.lngTaxClassCode.value;

	// Added by Kazushi Saito
	// 計算方法種別の取得
	if( typeof(window.parent.DSO.lngCalcCode) != "undefined" )
	{
		g_lngCalcCode = window.parent.DSO.lngCalcCode.value;
	}

	//非課税以外で引数がすべてある場合に税額を計算
	if (zeinuki != "" && g_curTax != "" )
	{
		//税抜合計からカンマと通貨記号を取る
		var str = fncDelCurrencySign(fncDelKannma(zeinuki));

		//税区分が非課税のとき
		if (g_lngTaxClassCode == 1 )
		{
			window.parent.DSO.lngTaxCode.value  = ""; //税率
			window.parent.DSO.curTaxPrice.value = ""; //税額
		}
		//税区分が外税のとき
		else if (g_lngTaxClassCode == 2 )
		{
			window.parent.DSO.lngTaxCode.value  = g_curTax; //税率
			str = str * g_curTax;
			window.parent.DSO.curTaxPrice.value = window.parent.fncCheckNumberValue(str, 2); //税額
		}
		//税区分が内税のとき
		else if (g_lngTaxClassCode == 3 )
		{
			window.parent.DSO.lngTaxCode.value  = g_curTax; //税率
			str = (str * g_curTax)/(1 + parseFloat(g_curTax));
			window.parent.DSO.curTaxPrice.value = window.parent.fncCheckNumberValue(str, 2); //税額
		}

		// Added by Kazushi Saito
		// 小数点以下の処理桁数
		g_lngDecimalCutPoint = 2;
		// 日本円の場合、小数点以下の処理桁数を変更
		if( window.parent.HSO.lngMonetaryUnitCode.value == g_strJpnCurrencySign )
		{
			g_lngDecimalCutPoint = 0;
		}

		// Added by Kazushi Saito
		// 売上管理(SCFlg)
		// 仕入管理(PCFlg)
		//「総合計金額」を小数点以下、切捨て処理（2桁0埋め）
		if( typeof(window.parent.HSO.SCFlg) == "object" ||
			typeof(window.parent.HSO.PCFlg) == "object")
		{
			
			// 総合計金額
			window.parent.DSO.curTaxPrice.value   = window.parent.fncCheckNumberValue(window.parent.DSO.curTaxPrice.value, 2, true, g_lngDecimalCutPoint, g_lngCalcCode);
			// 税抜金額の再計算
			fncDtCalTotalPrice();
		}

	}
	//基準通貨の計算
	fncDtCalStdTotalPrice();

	//税区分が変更されたときには警告を出す（引数がある場合のみ）
	if( typeof(object) != "undefined" )
	{
		alert("税区分が変更されました");
	}
}


// ---------------------------------------------------------------
/**
* 概要     : 仕入科目を選択したら、税区分と税率を決定
* 対象     : 仕入管理
* @param   : object, [object型], 仕入科目
*/
// ---------------------------------------------------------------
function fncDtTaxClassCode( object )
{
	//[仕入科目]が「402 輸入パーツ仕入高」「433 金型海外償却の場合」に
	//税区分を「非課税」に設定
	if (object.value == "402" || object.value == "433")
	{
		window.parent.DSO.lngTaxClassCode.value = 1 ; //消費税区分コード
		window.parent.DSO.lngTaxCode.value      ="" ; //消費税率
		window.parent.DSO.curTaxPrice.value     ="" ; //消費税額
	}
	//上記以外の場合、外税に設定
	else
	{
		window.parent.DSO.lngTaxClassCode.value = 2;        //消費税区分コード
		window.parent.DSO.lngTaxCode.value      = g_curTax; //税率
		window.parent.DSO.curTaxPrice.value     = fncDtCalTaxPrice(window.parent.DSO.curTotalPrice.value,
																window.parent.DSO.lngTaxClassCode.value); //消費税額
	}
}


// ---------------------------------------------------------------
/**
* 概要     : 製品のMSWから値を取得したときの処理
* 対象     : 製品のMSWがあるもの
* @param   : strProductCode, [str型], 製品コード
*/
// ---------------------------------------------------------------
function fncDtProductCodeForMSW(strProductCode)
{

	//製品から、製品名を作成
	subLoadMasterValue('cnProduct',
			 window.parent.DSO.strProductCode,
			 window.parent.DSO.strProductName,
			 Array(strProductCode),
			 window.document.objDataSourceSetting,
			 0);

	//製品から、顧客品番を作成
	subLoadMasterValue('cnGoodsCode',
			 window.parent.DSO.strProductCode,
			 window.parent.DSO.strGoodsCode,
			 Array(strProductCode),
			 window.document.objDataSourceSetting1,
			 1);
	//製品から、カートン入数を作成
	subLoadMasterValue('cnCartonQuantity',
			 window.parent.DSO.strProductCode,
			 window.parent.DSO.lngCartonQuantity,
			 Array(strProductCode),
			 window.document.objDataSourceSetting15,
			 15);

	//単価リストを作成
	fncDtGoodsPriceList();
	fncDtGoodsPriceToProductPrice();
}


// ---------------------------------------------------------------
/**
* 概要     : 商品修正画面を呼び出したあとに製品関連項目を再取得する
* 対象     : 発注管理, 受注管理
* @param   : strProductCode, [str型], 製品コード
*/
// ---------------------------------------------------------------
function fncDtProductCodeForP(strProductCode)
{

	//製品から、製品名を作成
	subLoadMasterValue('cnProduct',
			 window.parent.DSO.strProductCode,
			 window.parent.DSO.strProductName,
			 Array(strProductCode),
			 window.document.objDataSourceSetting,
			 0);

	//製品から、顧客品番を作成
	subLoadMasterValue('cnGoodsCode',
			 window.parent.DSO.strProductCode,
			 window.parent.DSO.strGoodsCode,
			 Array(strProductCode),
			 window.document.objDataSourceSetting1,
			 1);
	//製品から、カートン入数を作成
	subLoadMasterValue('cnCartonQuantity',
			 window.parent.DSO.strProductCode,
			 window.parent.DSO.lngCartonQuantity,
			 Array(strProductCode),
			 window.document.objDataSourceSetting15,
			 15);

	//税抜金額を再計算([製品数量]に[カートン入数]×[荷姿数量]を反映させるため)
	fncDtCalTotalPrice();
}


// ---------------------------------------------------------------
/**
* 概要     : 商品修正画面を呼び出し、変更されたら関連した項目を再取得
* 対象     : 発注管理, 受注管理
* @param   : strSessionID,    [string型], セッションID
* @param   : lngLanguageCode, [string型], 言語コード
*/
// ---------------------------------------------------------------
function fncShowDialogRenewCheck( strSessionID , lngLanguageCode)
{
	//入力枠の[製品コード]
	var ProductCode = window.parent.trim( window.parent.DSO.strProductCode.value );

	//[製品コード]が入力されていない場合、処理終了
	if( ProductCode == "" )
	{
		var strComment = ( lngLanguageCode == "0" ) ? "Please specify the product." : "製品を指定してください。";

		alert( strComment );

		return null;
	}

	args    = new Array();
	args[0] = new Array();

	var strUrl = "/p/regist/renew.php?strProductCode=" + ProductCode + "&strSessionID=" + strSessionID ;

	args[0][0] = strUrl;               // 実行先URL
	args[0][1] = 'ResultIframeRenew';  //IFrameのスタイル用ID
	args[0][2] = 'NO';                 // IFrameスクロールの許可・不許可
	args[0][3] = lngLanguageCode;      // $lngLanguageCode

	retval = window.showModalDialog( '/result/renew.html' , args , "dialogHeight:600px;dialogWidth:970px;center:yes;status:no;edge:raised;help:no;" );

	if( typeof(retval) != "undefined" )
	{
		//製品情報関連を再取得
		fncDtProductCodeForP(ProductCode );
		alert("製品情報が更新されました");
	}
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
function fncAddCurrencySign(num)
{
	var str = num.toString();
	var CurrencySign = window.parent.HSO.lngMonetaryUnitCode.value;

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





// 対象値再設定
function fncSetCheckValue()
{
	// 売上
	if( typeof(window.parent.HSO.SCFlg) == "object" )
	{
		// 直登録ではない場合
		if( window.parent.document.all.lngDirectRegistFlag.value == 0 )
		{
			for( var i = 0; i < saveRecord.length; i++ )
			{
				saveRecord[i][27] = eval( "document.all.blnOffset" + i ).value;
			}
		}
	}

	// 仕入
	if( typeof(window.parent.HSO.PCFlg) == "object" )
	{
		// 直登録ではない場合
		if( window.parent.document.all.lngDirectRegistFlag.value == 0 )
		{
			for( var i = 0; i < saveRecord.length; i++ )
			{
				saveRecord[i][25] = eval( "document.all.blnOffset" + i ).value;
			}
		}
	}
}

// チェックボックス処理
function fncSetCheck( obj, i )
{
	var objHidden = eval( "document.all.blnOffset" + i );
	var strValue  = objHidden.value;

	var imgOff    = '/img/type01/cmn/check_off.gif';
	var imgOn     = '/img/type01/cmn/check_on.gif';


	// 売上
	if( typeof(window.parent.HSO.SCFlg) == "object" )
	{
		// 直登録ではない場合
		if( window.parent.document.all.lngDirectRegistFlag.value == 0 )
		{
			if( strValue == '0' )
			{
				obj.src         = imgOn;
				objHidden.value = '1';

				saveRecord[i][27] = 1;
			}
			else
			{
				obj.src         = imgOff;
				objHidden.value = '0';

				saveRecord[i][27] = 0;
			}
		}
	}

	// 仕入
	if( typeof(window.parent.HSO.PCFlg) == "object" )
	{
		// 直登録ではない場合
		if( window.parent.document.all.lngDirectRegistFlag.value == 0 )
		{
			if( strValue == '0' )
			{
				obj.src         = imgOn;
				objHidden.value = '1';

				saveRecord[i][25] = 1;
			}
			else
			{
				obj.src         = imgOff;
				objHidden.value = '0';

				saveRecord[i][25] = 0;
			}
		}
	}

	// 総合計金額の再計算
	fncDtCalAllTotalPrice();
}





// 明細行チェック
// 明細行第1レコードと、それ以降のレコードを比較する
function fncCheckDetailRecords( saveRecord )
{
	var i;
	var blnCheck       = false;
	var strCodeRecord  = saveRecord[0][0];	// 明細行製品コード
	var strClassRecord = saveRecord[0][19];	// 明細行計上区分


	//受注管理の場合
	if( typeof( window.parent.HSO.SOFlg ) == "object" )
	{
		for( i=0; i<saveRecord.length; i++ )
		{
			blnCheck = fncCheckTargetDetail( strCodeRecord, strClassRecord, saveRecord[i][0], saveRecord[i][19] );

			if( !blnCheck ) break;
		}
	}
	// その他の管理
	else
	{
		blnCheck = true;
	}

	return blnCheck;
}

// 製品コード・計上区分相違チェック
function fncCheckDetailCode( saveRecord )
{
	var blnCheck       = false;
	var strPCode       = '';	// 製品コード
	var strPClass      = '';	// 計上区分
	var strCodeRecord  = '';	// 明細行製品コード
	var strClassRecord = '';	// 明細行計上区分

	// 明細行が1行以上存在する場合
	if( typeof( saveRecord[0] ) != 'undefined' )
	{
		if( g_lngSelIndex != 0 )
		{
			// 製品コードの取得
			strPCode = window.parent.trim( window.parent.DSO.strProductCode.value );

			// 明細行製品コードの取得
			strCodeRecord = saveRecord[0][0];


			//受注管理の場合
			if( typeof( window.parent.HSO.SOFlg ) == "object" )
			{
				strPClass      = window.parent.DSO.lngSalesClassCode.value;
				strClassRecord = saveRecord[0][19];

				// 対象コードの比較
				blnCheck = fncCheckTargetDetail( strCodeRecord, strClassRecord, strPCode, strPClass );
			}
			// その他の管理
			else
			{
				//blnCheck = ( strCodeRecord == strPCode ) ? true : false;
				blnCheck = true;
			}
		}
		else
		{
			blnCheck = true;
		}
	}
	else
	{
		blnCheck = true;
	}


	return blnCheck;
}

// 受注管理 対象コードの比較
function fncCheckTargetDetail( pRecord, cRecord, pcode, pclass )
{
	var blnCheck = false;

	if( pRecord == pcode && cRecord == pclass )
	{
		blnCheck = true;
	}
	else
	{
		blnCheck = false;
	}

	return blnCheck;
}


//-->
