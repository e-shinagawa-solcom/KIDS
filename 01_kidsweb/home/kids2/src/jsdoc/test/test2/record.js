<!--
//: ----------------------------------------------------------------------------
//: ファイル概要: 明細枠を操作する関数群
//: 備考        : 日々改良中
//: 作成日      : 2003/11/06 〜 
//: 作成者      : Takafumi Tetsuka
//: 修正履歴    : 
//: ----------------------------------------------------------------------------


//------------------------------------------------------------------------------
// グローバル変数定義
//------------------------------------------------------------------------------
var saveRecord = new Array();  //明細行を単位とする配列
var index      = -1;           //選択行を格納する変数
var returnFlg  = 1 ;           //Detailのタブを押したときに、登録の戻りがあったら表示
var sentakufunouFlg = 0;       //明細行選択の処理が終わるまでほかの選択をできなくする


//@*****************************************************************************
// 概要   : 「明細枠」に「入力枠」の内容を追加
// 対象   : 「明細枠」があるものすべて
// 備考   : 「入力枠」の値を配列「aryRecord」に格納し、それを配列「saveRecord」に格納。
//           このとき、明細行が選択されていれば、選択された配列の上に追加し、されていなければ、
//           最後尾に追加。
//           その後、関数「fncDtDisplay()」を呼び出し、明細枠を再表示。
//******************************************************************************
function fncDtAddRecord()
{
	//入力データのチェック
	var addFlg = fncDtAddCheck();

	if( addFlg == false ) return false;

	//入力枠の値を新規の配列に格納
	var aryRecord = fncDtNewAry();

	//明細行が選択されていない場合
	if ( index == -1)
	{
		//グローバル配列の最後に追加
		saveRecord.push(aryRecord);
	}
	//明細行が選択されている場合
	else
	{
		//選択された配列の上に、新規の配列を追加する
		saveRecordLength = parseInt(saveRecord.length); 
		saveRecordLeft  = saveRecord.slice(0,index);
		saveRecordRigft = saveRecord.slice(index, saveRecordLength);
		saveRecord      = saveRecordLeft;
		saveRecord.push(aryRecord);
		saveRecord      = saveRecord.concat(saveRecordRigft);

		//インデックスを初期化
		index      = -1;
	}

	//明細枠を再表示
	fncDtDisplay();

	//ヘッダの通貨設定を変更できないようにする
	fncHdMonetaryUnitCheck();
}

//@*****************************************************************************
// 概要   : 選択した行を削除
// 対象   : 「明細枠」があるものすべて
// 備考   : 選択行を除いた配列を作成。
//          その後、関数「fncDtDisplay()」を呼び出し、明細枠を再表示。
// 注意   : 行が選択されていない場合には、エラーメッセージを出力。
//******************************************************************************
function fncDtDelRecord()
{
	//明細行が選択されている場合
	if( index != -1 )
	{
		saveRecordLength = parseInt(saveRecord.length);

		saveRecordLeft  = saveRecord.slice(0, index);
		saveRecordRigft = saveRecord.slice(index + 1, saveRecordLength);
		saveRecord      = new Array();
		saveRecord      = saveRecord.concat(saveRecordLeft, saveRecordRigft);

		index = -1;

		//明細枠を再表示
		fncDtDisplay();
	}
	//明細行が選択されていない場合
	else
	{
		alert("明細行が選択してください");
	}

	//明細行がない場合、ヘッダの通貨設定を変更可能にする
	fncHdMonetaryUnitCheck();
}


//@*****************************************************************************
// 概要   : クリアボタンが押されたときに処理
// 対象   : 「明細枠」があるものすべて
// 備考   : 
//******************************************************************************
function fncDtClearRecord()
{
	//仕入部品をクリア
	window.parent.DSO.strStockItemCode.length = 0;

	//単価リストをクリア
	window.parent.DSO.lngGoodsPriceCode.length = 0;

	//明細行を選択できるようにする
	sentakufunouFlg = 0;

	fncDtGsChecked();
}


//@*****************************************************************************
// 概要   : 入力枠の値を選択行と置き換える
// 対象   : 「明細枠」があるものすべて
// 備考   : 入力枠の値を選択行と置き換える。
//          その後、関数「fncDtDisplay()」を呼び出し、明細枠を再表示。
// 注意    :行が選択されていない場合には、ヘッダ部分にエラーメッセージを出力
//******************************************************************************
function fncDtCommitRecord()
{
	//明細行が選択されている場合
	if( index != -1)
	{
		//入力枠の値を選択行と置き換え
		fncDtReplaceAry();

		//インデックスを初期化
		index = -1;

		//明細枠を再表示
		fncDtDisplay();
	}
	//明細行が選択されていない場合
	else
	{
		alert("明細行が選択されていません");
	}
}


//@*****************************************************************************
// 概要   : 明細枠を再表示
// 対象   : 「明細枠」があるものすべて
// 備考   : 配列「saveRecord」から、明細枠のテーブルを作成し、表示
//******************************************************************************
function fncDtDisplay()
{
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

	//既存の一覧を作成し直した一覧に書き換える
	document.all.DetailList.innerHTML = strTableHtml;

	//総合計金額の計算
	fncDtCalAllTotalPrice();

	//明細行を選択できるようにする
	sentakufunouFlg = 0;
}


//@*****************************************************************************
// 概要   : 明細行の選択時の処理
// 対象   : 「明細枠」があるものすべて
// 備考   : 明細行を選択するときに、すでに選択されている行があった場合は、その行の反転を解除する。
//          既存の選択されている行をもう一度押した場合には、indexを初期化する。
//          それ以外の場合には、選択行の値を、入力枠に反映させる。
// 注意   : 明細行が選択されている場合で、選択行を変更しようとした場合には、入力枠に変更がないかチェックし、
//          変更があれば、メッセージを出力。
//******************************************************************************
function fncDtSentaku(i)
{
	//他の明細行の処理が終わってなければ、選択させない
	if( sentakufunouFlg == 1 )
	{
		return null;
	}
	else
	{
		//処理中のフラグを立てる
		//(現在は仕入部品の処理が終わったときに解除している)
		sentakufunouFlg = 1;
	}

	//入力枠のチェックフラグ
	res = true;

	//明細行が選択されている場合
	if( index != -1 )
	{
		//入力枠に変更がないかチェック
		res = fncDtCheck();
	}

	//入力枠に変更がない、もしくは選択行を変更しても問題のない場合
	if(res == true)
	{
		//既存の選択行があった場合には、その行の反転を解除
		if( index != -1 )
		{
			document.getElementById("retsu" + index).style.backgroundColor="#ffffff";
		}

		//以前の選択行をもう一度クリックした場合
		if (index == i)
		{
			//インデックスを初期化
			index = -1;
			//明細行を選択できるようにする
			sentakufunouFlg = 0;
		}
		//以前と違う選択行をクリックした場合
		else
		{
			//インデックスに選択行の配列番号をセット
			index = i;

			//「選択行」を反転させる
			document.getElementById("retsu" + index).style.backgroundColor="#bbbbbb";

			//入力枠をすべてクリア（空行のときのため）
			window.parent.fncResetFrm( window.parent.DSO );
			//仕入部品をクリア（空行のときのため）
			window.parent.DSO.strStockItemCode.length  = 0;
			//単価リストをクリア（空行のときのため）
			window.parent.DSO.lngGoodsPriceCode.length = 0;

			if( saveRecord[index][0] != "" )
			{
				//製品から、製品名を作成
				subLoadMasterValue('cnProduct',
						 saveRecord[index][0],
						 window.parent.DSO.strProductName,
						 Array(saveRecord[index][0]),
						 window.document.objDataSourceSetting,
						 0);
				//製品から、顧客品番を作成
				subLoadMasterValue('cnGoodsCode',
						 saveRecord[index][0],
						 window.parent.DSO.strGoodsCode,
						 Array(saveRecord[index][0]),
						 window.document.objDataSourceSetting1,
						 1);
				//製品から、カートン入数を作成
				subLoadMasterValue('cnCartonQuantity',
						 saveRecord[index][0],
						 window.parent.DSO.lngCartonQuantity,
						 Array(saveRecord[index][0]),
						 window.document.objDataSourceSetting13,
						 13);
				//仕入科目から、仕入部品のオプション値を作成
				subLoadMasterOption( 'cnStockItem',
						 window.parent.DSO.strStockSubjectCode, 
						 window.parent.DSO.strStockItemCode,
						 Array(saveRecord[index][2]),
						 window.document.objDataSourceSetting10,
						 10);
				//単価リストを作成
				fncDtGoodsPriceList2();
			}
			//空白行の時、明細行を選択できるようにする
			else
			{
				//明細行を選択できるようにする
				sentakufunouFlg = 0;
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
		//明細行を選択できるようにする
		sentakufunouFlg = 0;
	}
}


//@*****************************************************************************
// 概要   : 入力枠と選択行の差異をチェックし、違いがあれば、確認ダイアログを表示
// 対象   : 「明細枠」があるものすべて
// 引数   : 
// 戻り値 : [Boolean型] 選択行を移動してもよい場合は、true、移動しない場合は、false
// 備考   : 
//******************************************************************************
function fncDtCheck()
{
	//チェックフラグ
	var res = true;

	//「入力枠」の値を配列にセット
	var aryRecord = fncDtNewAry();

	//配列の長さ
	var aryRecordLength = aryRecord.length;

	for( j = 0; j < aryRecordLength ; j++ )
	{
		//入力枠と選択行の比較
		if( aryRecord[j] != saveRecord[index][j] )
		{
			//単価リスト,仕入科目名,仕入部品名,単位（名称）,
			//単価追加リスト,行番号のときスキップ
			if (j==1 || j == 3 || j == 5 || j == 9 ||j == 14 || j==18) continue;

//デバック中 後で消す
//alert("変更された配列番号 : " + j + "\n" +
//	  "「入力枠」の値 : " + aryRecord[j] + "\n" +
//	  "「明細行」の値 : " + saveRecord[index][j]);

			res = confirm("変更箇所があります。変更しなくてもよろしいですか？")
			break;
		}
	}
	return res;

}


//@*****************************************************************************
// 概要   : 追加ボタンを押したときの値のチェック
// 対象   : 「明細枠」があるものすべて
// 注意   : 問題があればアラートを出す
//******************************************************************************
function fncDtAddCheck()
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
	//仕入科目が選択されなかった場合
	if( window.parent.DSO.strStockSubjectCode.value       == 0 )
	{
		alertList += "仕入科目を選択してください!\n";
	}
	//仕入部品が選択されなかった場合
	if( window.parent.DSO.strStockItemCode.selectedIndex  == -1 ||
		window.parent.DSO.strStockItemCode.selectedIndex  == 0  )
	{
		alertList += "仕入部品を選択してください!\n";
	}

	//製品単位計上が選択されている場合
	if (window.parent.DSO.lngConversionClassCode[0].checked)
	{
		//製品単価が入力されていなかった場合
		if( window.parent.DSO.curProductPrice_gs.value == "" ||
			window.parent.DSO.curProductPrice_gs.value == 0  )
		{
			alertList += "製品単価を入力してください!\n";
		}
		//製品数量が入力されていなかった場合
		if( window.parent.DSO.lngGoodsQuantity_gs.value == "" ||
			window.parent.DSO.lngGoodsQuantity_gs.value == 0  )
		{
			alertList += "製品数量を入力してください!\n";
		}
		//製品単価の値が不正だった場合
		if( isNaN(fncDelKannma(fncDelCurrencySign(window.parent.DSO.curProductPrice_gs.value))) )
		{
			alertList += "製品単価の値が不正です!\n";
		}
		//製品数量の値が不正だった場合
		if( isNaN(fncDelKannma(window.parent.DSO.lngGoodsQuantity_gs.value)) )
		{
			alertList += "製品数量の値が不正です!\n";
		}
	}
	//荷姿単位計上が選択されている場合
	else if( window.parent.DSO.lngConversionClassCode[1].checked )
	{
		//荷姿単価が入力されていなかった場合
		if( window.parent.DSO.curProductPrice_ps.value == "" ||
			window.parent.DSO.curProductPrice_ps.value == 0  )
		{
			alertList += "製品単価を入力してください!\n";
		}
		//荷姿数量が入力されていなかった場合
		if( window.parent.DSO.lngGoodsQuantity_ps.value == "" ||
			window.parent.DSO.lngGoodsQuantity_ps.value == 0  )
		{
			alertList += "製品数量を入力してください!\n";
		}
		//荷姿単価の値が不正だった場合
		if( isNaN(fncDelKannma(fncDelCurrencySign(window.parent.DSO.curProductPrice_ps.value))) )
		{
			alertList += "製品単価の値が不正です!\n";
		}
		//荷姿数量の値が不正だった場合
		if( isNaN(fncDelKannma(window.parent.DSO.lngGoodsQuantity_ps.value)) )
		{
			alertList += "製品数量の値が不正です!\n";
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


//@*****************************************************************************
// 概要   : 入力枠の値を新規の配列に格納
// 対象   : 「明細枠」があるものすべて
// 戻り値 : aryRecord, [配列型], 新規の配列
//******************************************************************************
function fncDtNewAry()
{
	var aryRecord = new Array();

	aryRecord[0]  = window.parent.DSO.strProductCode.value;            //製品コード
	aryRecord[1]  = window.parent.DSO.lngGoodsPriceCode.value;         //単価リスト
	aryRecord[2]  = window.parent.DSO.strStockSubjectCode.value;       //仕入科目
	aryRecord[3]  = window.parent.DSO.strStockSubjectCode.options[window.parent.DSO.strStockSubjectCode.selectedIndex].text;     //仕入科目（value + 名称）
	aryRecord[4]  = window.parent.DSO.strStockItemCode.value;          //仕入部品
	if( window.parent.DSO.strStockItemCode.selectedIndex != -1 )
	{
	aryRecord[5]  = window.parent.DSO.strStockItemCode.options[window.parent.DSO.strStockItemCode.selectedIndex].text;           //仕入部品（value + 名称）
	}else{
	aryRecord[5]  = "";
	}

	if( window.parent.DSO.lngConversionClassCode[0].checked )
	{
	aryRecord[6]  = window.parent.DSO.lngConversionClassCode[0].value; //換算区分(製品単位計上)
	aryRecord[7]  = window.parent.DSO.curProductPrice_gs.value;        //製品単価
	aryRecord[8]  = window.parent.DSO.lngProductUnitCode_gs.value;     //製品単位
	aryRecord[9]  = window.parent.DSO.lngProductUnitCode_gs.options[window.parent.DSO.lngProductUnitCode_gs.selectedIndex].text; //製品単位（名称）
	aryRecord[10] = window.parent.DSO.lngGoodsQuantity_gs.value;       //製品数量
	aryRecord[14] = window.parent.DSO.curProductPrice_gs.value;        //単価リスト追加データ
	}
	else if( window.parent.DSO.lngConversionClassCode[1].checked )
	{
	aryRecord[6]  = window.parent.DSO.lngConversionClassCode[1].value; //換算区分(荷姿単位計上)
	aryRecord[7]  = window.parent.DSO.curProductPrice_ps.value;        //荷姿単価
	aryRecord[8]  = window.parent.DSO.lngProductUnitCode_ps.value;     //荷姿単位
	aryRecord[9]  = window.parent.DSO.lngProductUnitCode_ps.options[window.parent.DSO.lngProductUnitCode_ps.selectedIndex].text; //荷姿単位（名称）
	aryRecord[10] = window.parent.DSO.lngGoodsQuantity_ps.value;       //荷姿数量
	aryRecord[14] = fncProductPriceForList();                          //単価リスト追加データ
	}
	aryRecord[11] = window.parent.DSO.curTotalPrice.value;             //税抜金額
	aryRecord[12] = window.parent.DSO.lngCarrierCode.value;            //運搬方法
	aryRecord[13] = window.parent.DSO.strDetailNote.value;             //備考

	//仕入管理の場合
	if( typeof(window.parent.HSO.PCFlg) == "object" )
	{
	aryRecord[15] = window.parent.DSO.lngTaxClassCode.value;             //消費税区分コード
	aryRecord[16] = window.parent.DSO.lngTaxCode.value;                  //消費税
	aryRecord[17] = window.parent.DSO.curTaxPrice.value;                 //消費税額
	aryRecord[18] = "";            //行番号
	}

	return aryRecord;
}


//@*****************************************************************************
// 概要   : 入力枠の値を選択行と置き換え
// 対象   : 「明細枠」があるものすべて
//******************************************************************************
function fncDtReplaceAry()
{
	saveRecord[index][0]  = window.parent.DSO.strProductCode.value;         //製品コード
	saveRecord[index][1]  = window.parent.DSO.lngGoodsPriceCode.value;      //単価リスト
	saveRecord[index][2]  = window.parent.DSO.strStockSubjectCode.value;    //仕入科目
	saveRecord[index][3]  = window.parent.DSO.strStockSubjectCode.options[window.parent.DSO.strStockSubjectCode.selectedIndex].text;     //仕入科目（value + 名称）
	saveRecord[index][4]  = window.parent.DSO.strStockItemCode.value;       //仕入部品
	saveRecord[index][5]  = window.parent.DSO.strStockItemCode.options[window.parent.DSO.strStockItemCode.selectedIndex].text;           //仕入部品（value + 名称）

	if( window.parent.DSO.lngConversionClassCode[0].checked )
	{
	saveRecord[index][6]  = window.parent.DSO.lngConversionClassCode[0].value; //換算区分(製品単位計上)
	saveRecord[index][7]  = window.parent.DSO.curProductPrice_gs.value;        //製品単価
	saveRecord[index][8]  = window.parent.DSO.lngProductUnitCode_gs.value;     //製品単位
	saveRecord[index][9]  = window.parent.DSO.lngProductUnitCode_gs.options[window.parent.DSO.lngProductUnitCode_gs.selectedIndex].text;     //製品単位（名称）
	saveRecord[index][10] = window.parent.DSO.lngGoodsQuantity_gs.value;       //製品数量
	saveRecord[index][14] = window.parent.DSO.curProductPrice_gs.value;        //単価リスト追加データ
	}
	else if(window.parent.DSO.lngConversionClassCode[1].checked )
	{
	saveRecord[index][6]  =  window.parent.DSO.lngConversionClassCode[1].value; //換算区分(荷姿単位計上)
	saveRecord[index][7]  = window.parent.DSO.curProductPrice_ps.value;         //荷姿単価
	saveRecord[index][8]  = window.parent.DSO.lngProductUnitCode_ps.value;      //荷姿単位
	saveRecord[index][9]  = window.parent.DSO.lngProductUnitCode_ps.options[window.parent.DSO.lngProductUnitCode_ps.selectedIndex].text;     //荷姿単位（名称）
	saveRecord[index][10] = window.parent.DSO.lngGoodsQuantity_ps.value;        //荷姿数量
	saveRecord[index][14] = fncProductPriceForList();                           //単価リスト追加データ
	} 

	saveRecord[index][11] = window.parent.DSO.curTotalPrice.value;          //税抜金額
	saveRecord[index][12] = window.parent.DSO.lngCarrierCode.value;         //運搬方法
	saveRecord[index][13] = window.parent.DSO.strDetailNote.value;          //備考

	//仕入管理の場合
	if( typeof(window.parent.HSO.PCFlg) == "object" )
	{
	saveRecord[index][15] = window.parent.DSO.lngTaxClassCode.value;         //消費税区分コード
	saveRecord[index][16] = window.parent.DSO.lngTaxCode.value;              //消費税
	saveRecord[index][17] = window.parent.DSO.curTaxPrice.value;             //消費税額
	}
}


//@*****************************************************************************
// 概要   : 入力枠に選択行を反映
// 対象   : 「明細枠」があるものすべて
//******************************************************************************
function fncDtReplaceInput()
{
	window.parent.DSO.strProductCode.value         = saveRecord[index][0];  //製品コード
	//単価リスト(saveRecord[index][1])は、hmtlに直接書く（遅延のため）
	window.parent.DSO.strStockSubjectCode.value    = saveRecord[index][2];  //仕入科目
	//仕入部品(saveRecord[index][4])は、hmtlに直接書く（遅延のため）

	if( saveRecord[index][6] == "gs" )
	{
	window.parent.DSO.lngConversionClassCode[0].checked = true;             //換算区分(製品単位計上)
	window.parent.DSO.curProductPrice_gs.value     = saveRecord[index][7];  //製品単価
	window.parent.DSO.lngProductUnitCode_gs.value  = saveRecord[index][8];  //製品単位
	window.parent.DSO.lngGoodsQuantity_gs.value    = saveRecord[index][10]; //製品数量

	//[製品単価][製品単位][製品数量]を入力、選択できるようにする
	window.parent.DSO.curProductPrice_gs.disabled    = false;
	window.parent.DSO.lngProductUnitCode_gs.disabled = false;
	window.parent.DSO.lngGoodsQuantity_gs.disabled   = false;

	//[荷姿単価][荷姿単位][荷姿数量]を入力、選択できないようにする
	window.parent.DSO.curProductPrice_ps.disabled    = true;
	window.parent.DSO.lngProductUnitCode_ps.disabled = true;
	window.parent.DSO.lngGoodsQuantity_ps.disabled   = true;
	}
	else if( saveRecord[index][6] == "ps" )
	{
	window.parent.DSO.lngConversionClassCode[1].checked = true;             //換算区分(荷姿単位計上)
	window.parent.DSO.curProductPrice_ps.value     = saveRecord[index][7];  //荷姿単価
	window.parent.DSO.lngProductUnitCode_ps.value  = saveRecord[index][8];  //荷姿単位
	window.parent.DSO.lngGoodsQuantity_ps.value    = saveRecord[index][10]; //荷姿数量
	window.parent.DSO.curProductPrice_gs.value     = saveRecord[index][14]; //単価リスト追加データ

	//[製品単価][製品単位][製品数量]を入力、選択できないようにする
	window.parent.DSO.curProductPrice_gs.disabled    = true;
	window.parent.DSO.lngProductUnitCode_gs.disabled = true;
	window.parent.DSO.lngGoodsQuantity_gs.disabled   = true;
	//[荷姿単価][荷姿単位][荷姿数量]を入力、選択できるようにする
	window.parent.DSO.curProductPrice_ps.disabled    = false;
	window.parent.DSO.lngProductUnitCode_ps.disabled = false;
	window.parent.DSO.lngGoodsQuantity_ps.disabled   = false;
	}

	window.parent.DSO.curTotalPrice.value          = saveRecord[index][11]; //税抜金額
	window.parent.DSO.lngCarrierCode.value         = saveRecord[index][12]; //運搬方法
	window.parent.DSO.strDetailNote.value          = saveRecord[index][13]; //備考

	//仕入管理の場合
	if( typeof(window.parent.HSO.PCFlg) == "object" )
	{
	window.parent.DSO.lngTaxClassCode.value = saveRecord[index][15];         //消費税区分コード
	window.parent.DSO.lngTaxCode.value      = saveRecord[index][16];         //消費税
	window.parent.DSO.curTaxPrice.value     = saveRecord[index][17];         //消費税額
	}
}


//@*****************************************************************************
// 概要   : 明細枠テーブルの列名を作成
// 対象   : 「明細枠」があるものすべて
// 戻り値 : strTableHtml, [String型], 明細枠の列名
//******************************************************************************
function fncStrTableHtmlColumns()
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
					  '<td nowrap id="ExStrDL09">Remark</td>'       +
					  '</tr>';
	}

	return strTableHtml;
}


//@*****************************************************************************
// 概要   : 明細枠テーブルの行を作成
// 対象   : 「明細枠」があるものすべて
// 戻り値 : strTableHtml, [String型], 明細枠の内容
//******************************************************************************
function fncStrTableHtmlRows(i)
{
	strTableHtml ='<td align="center" nowrap>'      + saveRecord[i][0]  +             //製品
				  '</td><td nowrap>'                + saveRecord[i][3]  +             //仕入科目（名称）
				  '</td><td nowrap>'                + saveRecord[i][5]  +             //仕入部品（名称）
				  '</td><td align="right" nowrap>'  + saveRecord[i][7]  + "&nbsp;" +  //単価
				  '</td><td align="center" nowrap>' + saveRecord[i][9]  +             //単位（名称）
				  '</td><td align="right" nowrap>'  + saveRecord[i][10]  + "&nbsp;" + //数量
				  '</td><td align="right" nowrap>'  + saveRecord[i][11] + "&nbsp;" +  //税抜金額
				  '</td><td nowrap>'                + saveRecord[i][13] +             //備考
				  '</td>'

	return strTableHtml;
}


//@*****************************************************************************
// 概要   : 登録ボタンを押したときに、header欄に、明細枠のデータをhiddenに吐き出す
// 対象   : 「明細枠」があるものすべて
//******************************************************************************
function fncDtRegistRecord(){

	var strHiddenHtml = "";

	//hiddenで吐き出す連番（空行を削除すると順番がかわるため使用）
	var hiddenNumber = 0 ;

	for( i = 0; i < saveRecord.length; i++ )
	{
		//空行チェック
		if (saveRecord[i][0] == "") continue;

		strHiddenHtml = strHiddenHtml + fncDtHiddenHtml(i, hiddenNumber);
		hiddenNumber++; 
	}

	//承認ルートを追加（発注管理の場合）
	if( typeof(window.parent.HSO.POFlg) == "object" )
	{
		strHiddenHtml = strHiddenHtml + "<input type='hidden' name='lngWorkflowOrderCode' value='" + window.parent.DSO.lngWorkflowOrderCode.value + "' >\n" ;
	}

	//発注ＮＯを追加
	strHiddenHtml = strHiddenHtml + "<input type='hidden' name='strOrderCode' value='" + window.parent.HSO.strOrderCode.value + "' >\n" ;

	//リビジョンコードを追加
	strHiddenHtml = strHiddenHtml + "<input type='hidden' name='strReviseCode' value='" + window.parent.HSO.strReviseCode.value + "' >\n" ;

	//通貨を追加
	strHiddenHtml = strHiddenHtml + "<input type='hidden' name='lngMonetaryUnitCode' value='" + window.parent.HSO.lngMonetaryUnitCode.value + "' >\n" ;

	//レートタイプを追加
	strHiddenHtml = strHiddenHtml + "<input type='hidden' name='lngMonetaryRateCode' value='" + window.parent.HSO.lngMonetaryRateCode.value + "' >\n" ;

	//換算レートを追加
	strHiddenHtml = strHiddenHtml + "<input type='hidden' name='curConversionRate' value='" + window.parent.HSO.curConversionRate.value + "' >\n" ;

	//支払条件を追加
	strHiddenHtml = strHiddenHtml + "<input type='hidden' name='lngPayConditionCode' value='" + window.parent.HSO.lngPayConditionCode.value + "' >\n" ;

	//総合計金額（税抜き）を追加
	strHiddenHtml = strHiddenHtml + "<input type='hidden' name='curAllTotalPrice' value='" + fncDelKannma(fncDelCurrencySign(window.parent.DSO.curAllTotalPrice.value)) + "' >\n" ;

//デバック中
alert(strHiddenHtml);

	//フォーム(name="HSO")に明細枠のデータを渡す
	window.parent.document.all.DtHiddenRecord.innerHTML = strHiddenHtml;


	//フォームHSOをサブミット
	window.parent.document.HSO.submit();
}


//@*****************************************************************************
// 概要   : 入力枠のhiddenに吐き出すデータを作成
// 対象   : 「明細枠」があるものすべて
// 戻り値 : strHiddenHtml, [string型], 明細枠の内容をhiddenに置き換えて吐き出す
// 備考   : 
//******************************************************************************

function fncDtHiddenHtml(i, hiddenNumber){

	//仕入管理の場合のみのhidden値
	var strPC = "";

	if( typeof(window.parent.HSO.PCFlg) == "object" )
	{
	strPC = "<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][lngTaxClassCode]'  value='" + saveRecord[i][15] + "' >\n" +                                   //消費税区分コード
			"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][lngTaxCode]'       value='" + saveRecord[i][16] + "' >\n"     +                               //消費税
			"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][curTaxPrice]'      value='" + fncDelKannma(fncDelCurrencySign(saveRecord[i][17])) + "' >\n" + //消費税額
			"<input type='hidden' name='aryPoDitail[" + hiddenNumber + "][lngorderdetailno]' value='" + saveRecord[i][18] + "' >\n" ;                                   //行番号
	}

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
					strPC;

	return strHiddenHtml;
}


//@*****************************************************************************
// 概要   : 修正のために吐き出されたhidden値および登録ボタンを押した後に
//          戻ってきたhidden値を新規の配列に格納
// 対象   : 「明細枠」があるものすべて
//******************************************************************************
function fncDtNewAryForReturn(i)
{
	var aryRecord = new Array();
	aryRecord[0]  = window.parent.DSO.elements("aryPoDitail[" + i + "][strProductCode]").value;          //製品コード
	aryRecord[1]  = window.parent.DSO.elements("aryPoDitail[" + i + "][lngGoodsPriceCode]").value;       //単価リスト
	aryRecord[2]  = window.parent.DSO.elements("aryPoDitail[" + i + "][strStockSubjectCode]").value;     //仕入科目
	aryRecord[3]  = window.parent.DSO.elements("aryPoDitail[" + i + "][strStockSubjectCodeName]").value; //仕入科目（value + 名称）
	aryRecord[4]  = window.parent.DSO.elements("aryPoDitail[" + i + "][strStockItemCode]").value;        //仕入部品
	aryRecord[5]  = window.parent.DSO.elements("aryPoDitail[" + i + "][strStockItemCodeName]").value;    //仕入部品（value + 名称）
	aryRecord[6]  = window.parent.DSO.elements("aryPoDitail[" + i + "][lngConversionClassCode]").value;  //換算区分(製品単位計上)
	aryRecord[7]  = window.parent.fncCheckNumberValue(window.parent.DSO.elements("aryPoDitail[" + i + "][curProductPrice]").value, 4); //単価
	aryRecord[8]  = window.parent.DSO.elements("aryPoDitail[" + i + "][lngProductUnitCode]").value;      //単位
	aryRecord[9]  = window.parent.DSO.elements("aryPoDitail[" + i + "][lngProductUnitCodeName]").value;  //単位（名称）
	aryRecord[10]  = window.parent.fncCheckNumberValue(window.parent.DSO.elements("aryPoDitail[" + i + "][lngGoodsQuantity]").value, 0, false); //数量
	aryRecord[11] = window.parent.fncCheckNumberValue(window.parent.DSO.elements("aryPoDitail[" + i + "][curTotalPrice]").value, 2); //税抜金額
	aryRecord[12] = window.parent.DSO.elements("aryPoDitail[" + i + "][lngCarrierCode]").value;          //運搬方法
	aryRecord[13] = window.parent.DSO.elements("aryPoDitail[" + i + "][strDetailNote]").value;           //備考
	aryRecord[14] = window.parent.fncCheckNumberValue(window.parent.DSO.elements("aryPoDitail[" + i + "][curProductPriceForList]").value, 4); //単価リスト追加データ

	//仕入管理の場合
	if( typeof(window.parent.HSO.PCFlg) == "object" )
	{
	aryRecord[15] = window.parent.DSO.elements("aryPoDitail[" + i + "][lngTaxClassCode]").value;  //消費税区分コード
	aryRecord[16] = window.parent.DSO.elements("aryPoDitail[" + i + "][lngTaxCode]").value;       //消費税
		if (window.parent.DSO.elements("aryPoDitail[" + i + "][curTaxPrice]").value == "")
		{
		aryRecord[17] = fncDtCalTaxPrice(aryRecord[11],aryRecord[15], aryRecord[16]);
		}else{
		aryRecord[17] = window.parent.DSO.elements("aryPoDitail[" + i + "][curTaxPrice]").value;      //消費税額
		}
	aryRecord[18] = window.parent.DSO.elements("aryPoDitail[" + i + "][lngorderdetailno]").value; //行番号
	}

	return aryRecord;
}


//@*****************************************************************************
// 概要   : 税抜き合計を算出する
// 対象   : 「明細枠」があるものすべて
//******************************************************************************
function fncDtCalTotalPrice()
{
 var ProductPrice  = 0; //単価
 var GoodsQuantity = 0; //数量
 var TotalPrice    = 0; //税抜金額

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
	TotalPrice    = ProductPrice * GoodsQuantity;
	//税抜金額を入力枠に反映
	window.parent.DSO.curTotalPrice.value = TotalPrice;
	//税抜金額をフォーマットする
	window.parent.fncCheckNumber( window.parent.DSO.curTotalPrice , 2);

	//仕入管理の場合、税額を計算
	if( typeof(window.parent.HSO.PCFlg) == "object" )
	{
		window.parent.DSO.curTaxPrice.value     = fncDtCalTaxPrice(window.parent.DSO.curTotalPrice.value,
																window.parent.DSO.lngTaxClassCode.value,
																window.parent.DSO.lngTaxCode.value); //消費税額
	}

	//基準通貨を表示
	fncDtCalStdTotalPrice();
}


//@*****************************************************************************
// 概要   : 製品単位計上のラジオボタンが押されたときの処理
// 対象   : 「明細枠」があるものすべて
//******************************************************************************
function fncDtGsChecked()
{
	//単価リストを選択できるようにする
	window.parent.DSO.lngGoodsPriceCode.disabled = false;

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


//@*****************************************************************************
// 概要   : 荷姿単位計上のラジオボタンが押されたときの処理
// 対象   : 「明細枠」があるものすべて
//******************************************************************************
function fncDtPsChecked()
{
	//ボタンを移動してもよいかどうかのフラグ
	var checkFlg = true ;

	//仕入管理の場合で、行番号のある場合に、荷姿単位計上に選択してもよいかどうかのチェック
	if( typeof(window.parent.HSO.PCFlg) =="object" && 
			   index != -1 && 
			   saveRecord[index][18] != "" )
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
		window.parent.fncCheckNumber(window.parent.DSO.curProductPrice_ps , 4 );
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


//@*****************************************************************************
//  概要   :荷姿単位計上に選択できるかどうかのチェック
//  対象   :仕入管理で行番号のある場合
//******************************************************************************
function fncDtPsCheckedForPC()
{
	//カートン入り数
	var CartonQuantity   = fncDelKannma(window.parent.DSO.lngCartonQuantity.value);
	//製品数量
	var GoodsQuantity_gs = fncDelKannma(window.parent.DSO.lngGoodsQuantity_gs.value);

	//カートン数量が0または空のとき選択できない
	if( CartonQuantity == "" || CartonQuantity == 0 )
	{
		return false;
	}

	//製品数量とカートン数量が割り切れれば選択できる
	if( (GoodsQuantity_gs % CartonQuantity) == 0 )
	{
		return true;
	}
	else
	{
		return false;
	}
}


//@*****************************************************************************
//  概要   :荷姿数量の入力チェック
//  対象   :仕入管理で行番号のある場合で、荷姿数量にチェックされている場合
//******************************************************************************
function fncDtPSGoodsQuantityForPC()
{
	//行番号があり、選択されているときのみチェック
	if( index != -1 && saveRecord[index][18] != "" )
	{
		//カートン入り数
		var CartonQuantity   = fncDelKannma(window.parent.DSO.lngCartonQuantity.value);
		//デフォルトの製品数量
		var GoodsQuantity_gs_defalt = fncDtGSGoodsQuantityDefalt();
		//入力されたに荷姿数量
		var GoodsQuantity_ps = fncDelKannma(window.parent.DSO.lngGoodsQuantity_ps.value);
	
		//カートン数量が0または空のときチェック終了
		if( CartonQuantity == "" || CartonQuantity == 0 )
		{
			return false;
		}
	
		//荷姿数量に入力できる上限
		var GoodsQuantity_ps_max = parseInt(GoodsQuantity_gs_defalt / CartonQuantity) ;
	
		//入力された荷姿数量が上限を超えた場合
		if( GoodsQuantity_ps > GoodsQuantity_ps_max )
		{
			//上限を超えたら最大値をセット
			window.parent.DSO.lngGoodsQuantity_ps.value = fncAddKannma(GoodsQuantity_ps_max);
			//エラーメッセージを出力
			alert("荷姿数量に入力できる上限は、" + GoodsQuantity_ps_max + "です");
		}
	}
}


//@*****************************************************************************
//  概要   :製品数量の入力チェック
//  対象   :仕入管理で行番号のある場合で、製品数量にチェックされている場合
//******************************************************************************
function fncDtGSGoodsQuantityForPC()
{
	//行番号があり、選択されているときのみチェック
	if( index != -1 && saveRecord[index][18] != "" )
	{
		//デフォルトの製品数量
		var GoodsQuantity_gs_defalt = fncDtGSGoodsQuantityDefalt();
		//入力されたに製品数量
		var GoodsQuantity_gs = fncDelKannma(window.parent.DSO.lngGoodsQuantity_gs.value);
	
		//入力された製品数量がデフォルト値を超えた場合
		if( GoodsQuantity_gs > GoodsQuantity_gs_defalt )
		{
			//上限を超えたら最大値をセット
			window.parent.DSO.lngGoodsQuantity_gs.value = fncAddKannma(GoodsQuantity_gs_defalt);
			//エラーメッセージを出力
			alert("製品数量に入力できる上限は、" + GoodsQuantity_gs_defalt + "です");
		}
	}
}


//@*****************************************************************************
//  概要   :行番号からデフォルトの製品数量を得る
//  対象   :仕入管理で行番号のある場合
//******************************************************************************
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
		if( window.parent.DSO.elements("aryPoDitail[" + i + "][lngorderdetailno]").value == saveRecord[index][18] )
		{
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
		i++;
	}

	return GoodsQuantity_gs_defalt;
}


//@*****************************************************************************
// 概要   : 総合計金額を算出する
// 対象   : 「明細枠」があるものすべて
//******************************************************************************
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
		AllTotalPrice += parseInt(10000 * fncDelKannma(fncDelCurrencySign(saveRecord[i][11])));

	}
		AllTotalPrice = AllTotalPrice / 10000 ;

	//総合計金額を入力枠に反映
	window.parent.DSO.curAllTotalPrice.value = AllTotalPrice;
	//総合計金額をフォーマットする
	window.parent.fncCheckNumber( window.parent.DSO.curAllTotalPrice , 2 );
	}
}


//@*****************************************************************************
// 概要   : 基準通貨を算出する
// 対象   : 「明細枠」があるもので通貨が日本円以外
//******************************************************************************
function fncDtCalStdTotalPrice()
{
	//日本円のときは表示させない
	if( window.parent.HSO.lngMonetaryUnitCode.value == "\\" )
	{
		//[基準通貨]を空白にする
		window.parent.DSO.curStdTotalPrice.value = "";
		return false;
	}

	//[税抜金額]を取得
	var TotalPrice = parseFloat(fncDelKannma(fncDelCurrencySign(window.parent.DSO.curTotalPrice.value)));

	//[換算レート]を取得
	var ConversionRate = fncDelKannma(fncDelCurrencySign(window.parent.HSO.curConversionRate.value));

	//[基準通貨]を求めてフォーマット
	var StdTotalPrice = window.parent.fncCheckNumberValue(TotalPrice * ConversionRate, 2 ,false);

	//[基準通貨]に円マークをつける
	window.parent.DSO.curStdTotalPrice.value = "\\ " + StdTotalPrice;

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
			window.parent.DSO.curSubTaxPrice.value = "\\ " + SubTaxPrice ; //[税額]
			//[合計金額]=[税抜価格]+[消費税額]
			var TotalStdAmt = window.parent.fncCheckNumberValue(((TotalPrice + TaxPrice) * ConversionRate), 2 ,false);
			window.parent.DSO.curTotalStdAmt.value = "\\ " + TotalStdAmt ; //[合計金額]
		}
		//内税の場合
		else if( window.parent.DSO.lngTaxClassCode.value == 3 )
		{
			//[税額]=[消費税額]×[換算レート]
			var SubTaxPrice = window.parent.fncCheckNumberValue(TaxPrice * ConversionRate, 2 ,false);
			window.parent.DSO.curSubTaxPrice.value = "\\ " + SubTaxPrice ;                   //[税額]
			//[合計金額]=基準通貨と同様
			window.parent.DSO.curTotalStdAmt.value = window.parent.DSO.curStdTotalPrice.value ; //[合計金額]
		}
	}
}


//@*****************************************************************************
// 概要   : [通貨]の選択変更による処理
// 対象   : 「明細枠」があるものすべて
//******************************************************************************
function fncHdMonetaryUnitCode()
{
	//日本円の場合	
	if( window.parent.HSO.lngMonetaryUnitCode.value == "\\" )
	{
		//[レートタイプ]を選択できないようにする
		window.parent.HSO.lngMonetaryRateCode.disabled = true;
		//[支払条件]を選択できないようにする
		window.parent.HSO.lngPayConditionCode.disabled = true;

		//[レートタイプ]を空白にする
		window.parent.HSO.lngMonetaryRateCode.value = "0";

		//[換算レート]をクリアする
		window.parent.HSO.curConversionRate.value = "1.000000";

		//[支払条件]を未定にする
		window.parent.HSO.lngPayConditionCode.value = "0";

	}
	//日本円以外の場合
	else
	{
		//[レートタイプ]を選択できるようにする
		window.parent.HSO.lngMonetaryRateCode.disabled = false;

		//[レートタイプ]のデフォルトを「社内レートにする」にする
		window.parent.HSO.lngMonetaryRateCode.value = "2";

		//[支払条件]を選択できるようにする
		window.parent.HSO.lngPayConditionCode.disabled = false;

	}


	//[製品単価][荷姿単価]をクリア
	window.parent.DSO.curProductPrice_gs.value = "" ;
	window.parent.DSO.curProductPrice_ps.value = "" ;

	//税抜き合計をクリア
	window.parent.DSO.curTotalPrice.value = "" ;

	//基準通貨をクリア
	window.parent.DSO.curStdTotalPrice.value = "" ;

}


//@*****************************************************************************
// 概要   : 明細行を追加した場合に[通貨][レートタイプ]を選択できなくし、
//           明細行がない場合に[通貨][レートタイプ]を選択できるようにする。
//           ただし、[レートタイプ]を選択できるのは、日本円以外のとき。
// 対象   : 「明細枠」があるものすべて
//******************************************************************************
function fncHdMonetaryUnitCheck()
{
	if (saveRecord.length == 0)
	{
		//[通貨]を選択できるようにする
		window.parent.HSO.lngMonetaryUnitCode.disabled = false;
		//[レートタイプ]を選択できるようにする
		window.parent.HSO.lngMonetaryRateCode.disabled = false;
	}
	else
	{
		//[通貨]を選択できないようにする
		window.parent.HSO.lngMonetaryUnitCode.disabled = true;
		//[レートタイプ]を選択できないようにする
		window.parent.HSO.lngMonetaryRateCode.disabled = true;
	}
}


//@*****************************************************************************
// 概要   : 通貨を選択したら、概算レートに反映
// 対象   : 「明細枠」があるものすべて
//******************************************************************************
function fncCalConversionRate()
{
	//[通貨]が日本円だったら、キャンセル
	if( window.parent.HSO.lngMonetaryUnitCode.value == "\\" ) return false;

	//[レートタイプ]の空白を選択したら、社員を選択したことにする
	if( window.parent.HSO.lngMonetaryRateCode.value == "0" )
	{
		window.parent.HSO.lngMonetaryRateCode.value = "2";
	}

	//[計上日]が空の場合に、現在の日付けを反映
	if( window.parent.HSO.dtmOrderAppDate.value == "" )
	{
		window.parent.HSO.dtmOrderAppDate.value = fncYYMMDD();
	}

	//[換算レート]を[レートタイプ][通貨][計上日]をもとに反映
	subLoadMasterValue(23,
					 window.parent.HSO.lngMonetaryRateCode,
					 window.parent.HSO.curConversionRate,
					 Array(window.parent.HSO.lngMonetaryRateCode.value,
						   window.parent.HSO.lngMonetaryUnitCode.value,
						   window.parent.HSO.dtmOrderAppDate.value),
						   window.document.objDataSourceSetting);
}


//@*****************************************************************************
// 概要    ： フォームDSOに吐き出されたhidden値から入力枠のデータを作成
//            Detailのタブを押したときに実行される
// 対象   : 「明細枠」があるものすべて
//******************************************************************************
function fncDtHtml()
{
	//通貨基準を再計算
	fncDtCalStdTotalPrice();

	//
	if( returnFlg == -1 || typeof(window.parent.DSO.elements("aryPoDitail[0][strProductCode]")) == "undefined" ) return null;

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
	returnFlg = -1;

	//明細枠を再表示
	fncDtDisplay();//
}


//@*****************************************************************************
// 概要    ： 単価リストを表示
// 対象   : 「明細枠」があるものすべて
//******************************************************************************
function fncDtGoodsPriceList()
{
	//製品コードが選択されていなければ、終了
	if( window.parent.DSO.strProductCode.value == ""            ||
		isNaN(window.parent.DSO.strProductCode.value)           ||
		window.parent.DSO.strStockSubjectCode.value       == 0  ||
		window.parent.DSO.strStockItemCode.selectedIndex  == 0  ||
		window.parent.DSO.strStockItemCode.selectedIndex  == -1 ||
		isNaN(window.parent.DSO.strStockItemCode.value)         ) return false;

	subLoadMasterOption( 26,
		 window.parent.DSO.strStockItemCode, 
		 window.parent.DSO.lngGoodsPriceCode,
		 Array(window.parent.DSO.strProductCode.value,
			   window.parent.DSO.strStockSubjectCode.value,
			   window.parent.DSO.strStockItemCode.value,
			   window.parent.HSO.lngMonetaryUnitCode.value),
		 window.document.objDataSourceSetting11,11);

}


//@*****************************************************************************
// 概要    ： 単価リストを表示(明細行を選択した場合)
// 対象   : 「明細枠」があるものすべて
//******************************************************************************
function fncDtGoodsPriceList2()
{
	//製品コードが選択されていなければ、終了
	if (saveRecord[index][0] == ""           ||
		saveRecord[index][2]       == 0 ||
		saveRecord[index][4]  == -1 ) return false;

	subLoadMasterOption( 26,
		 window.parent.DSO.strStockItemCode, 
		 window.parent.DSO.lngGoodsPriceCode,
		 Array(saveRecord[index][0],
			   saveRecord[index][2],
			   saveRecord[index][4],
			   window.parent.HSO.lngMonetaryUnitCode.value),
		 window.document.objDataSourceSetting12,12);
}


//@*****************************************************************************
// 概要    ： 単価リストを選択したら、製品単価に反映
// 対象   : 「明細枠」があるものすべて
//******************************************************************************
function fncDtGoodsPriceToProductPrice()
{
	//単価リストがなかったら、EXIT
	if( window.parent.DSO.lngGoodsPriceCode.selectedIndex == -1 ) return false;

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


//@*****************************************************************************
// 概要    ： [単価リスト]追加データのチェック
// 対象   : 「明細枠」があるものすべて
//******************************************************************************
function fncProductPriceForList()
{
	//製品単価
	var productPrice_gs = fncDelKannma(fncDelCurrencySign(window.parent.DSO.curProductPrice_gs.value));

	//荷姿単価
	var productPrice_ps = fncDelKannma(fncDelCurrencySign(window.parent.DSO.curProductPrice_ps.value));

	//カートン入数
	var cartonQuantity  = fncDelKannma(window.parent.DSO.lngCartonQuantity.value);

	//
	var productPriceForList = productPrice_ps / cartonQuantity;


	if (productPrice_gs != productPriceForList)
	{
		//製品単価と荷姿単価/カートン入数 が違う場合
		productPriceForList = "";
	}
	else
	{
		productPriceForList = window.parent.DSO.curProductPrice_gs.value;
	}

	return productPriceForList;
}


//@*****************************************************************************
// 概要      : 税額の計算
// 対象      : 仕入管理
// 引数      : zeinuki,  [string型], 税抜金額
//             zeicode,  [int型]   , 税コード
//             zeiritsu, [int型]   , 消費税率
// 戻り値    : str, [string型], 税額
//******************************************************************************
function fncDtCalTaxPrice(zeinuki, zeicode, zeiritsu)
{
	var str="";

	//非課税以外で引数がすべてある場合に税額を計算

	if (zeinuki != "" && zeicode != 1 && zeiritsu != "" )
	{
		//税抜合計からカンマと通貨記号を取る
		str = fncDelCurrencySign(fncDelKannma(zeinuki));

		//税区分が外税のとき
		if (zeicode == 2 )
		{
			str = str * zeiritsu;
		}
		//税区分が内税のとき
		else if (zeicode == 3 )
		{
			str = (str * zeiritsu)/(1 + parseFloat(zeiritsu));
		}

		//税額を求めてフォーマット
		str = window.parent.fncCheckNumberValue(str, 2);

	}

	return str;
}


//@*****************************************************************************
// 概要   : 税額の計算２（[税区分]を変更したとき）
// 対象   : 仕入管理
// 引数   : object, [object型], 税区分
// 注意   : 税区分を変更したときにはアラートを出す
//******************************************************************************
function fncDtCalTaxPrice2(object)
{
	//[税抜金額]
	var zeinuki  = window.parent.DSO.curTotalPrice.value;
	//[税区分]
	var zeicode  = object.value;


	//非課税以外で引数がすべてある場合に税額を計算
	if (zeinuki != "" && zeiritsu != "" )
	{
		//税抜合計からカンマと通貨記号を取る
		var str = fncDelCurrencySign(fncDelKannma(zeinuki));

		//[税率]
		var zeiritsu = 0.05; //要修正（DBが取得するように修正すること）

		//税区分が非課税のとき
		if (zeicode == 1 )
		{
			window.parent.DSO.lngTaxCode.value  = ""; //税率
			window.parent.DSO.curTaxPrice.value = ""; //税額
		}
		//税区分が外税のとき
		else if (zeicode == 2 )
		{
			window.parent.DSO.lngTaxCode.value  = zeiritsu; //税率
			str = str * zeiritsu;
			window.parent.DSO.curTaxPrice.value = window.parent.fncCheckNumberValue(str, 2); //税額
		}
		//税区分が内税のとき
		else if (zeicode == 3 )
		{
			window.parent.DSO.lngTaxCode.value  = zeiritsu; //税率
			str = (str * zeiritsu)/(1 + zeiritsu);
			window.parent.DSO.curTaxPrice.value = window.parent.fncCheckNumberValue(str, 2); //税額
		}
	}
	//税区分が変更されたときには警告を出す。
	alert("税区分が変更されました");
}


//@*****************************************************************************
// 概要   : 仕入科目を選択したら、税区分と税率を決定
// 引数   : object, [object型], 仕入科目
// 対象   : 仕入管理
//******************************************************************************
function fncDtTaxClassCode(object)
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
		window.parent.DSO.lngTaxClassCode.value = 2;    //消費税区分コード
		window.parent.DSO.lngTaxCode.value      = 0.05; //消費税率				要修正（DBが取得するように修正すること）
		window.parent.DSO.curTaxPrice.value     = fncDtCalTaxPrice(window.parent.DSO.curTotalPrice.value,
																window.parent.DSO.lngTaxClassCode.value,
																window.parent.DSO.lngTaxCode.value); //消費税額
	}
}


//@*****************************************************************************
// 概要   : カンマをとる
// 対象   : すべて
// 引数   : num, [string型], カンマを取りたい値
// 戻り値 : str, [string型], カンマを取り除いた値
//******************************************************************************
function fncDelKannma(num)
{
	var i = 0;;
	var str = num.toString();

	while( str.indexOf(',',i) != -1 )
	{
		i = str.indexOf(',',i);
		str = "" + str.substring(0,i) + str.substring(i+1,str.length);
	}
	return str;
}


//@*****************************************************************************
// 概要   : カンマを付ける
// 対象   : すべて
// 引数   : num, [string型], カンマを付けたい値
// 戻り値 : str, [string型], カンマ付の値
//******************************************************************************
function fncAddKannma(num)
{
	var i;
	var max;

	var str = num.toString();
	//小数点の位置
	var strTen = str.indexOf('.');
	//少数点より前
	var strSeisuu = str.substring(0,strTen);
	//小数点より後
	var strShousuu = str.substring(strTen,str.length);

	max = Math.floor(strSeisuu.length/3);
	for( i=max; i>0 ;i-- )
	{
		if( strSeisuu.length-3*i != 0 )
		{
			strSeisuu = "" + strSeisuu.substring(0,strSeisuu.length-3*i) + ',' + strSeisuu.substring(strSeisuu.length-3*i,strSeisuu.length)
		}
	}
	str = strSeisuu + strShousuu;
	return str;
}


//@*****************************************************************************
// 概要   : 通貨記号を取る（空白からあとの部分を抜き出す）
// 対象   : すべて
// 引数   : num, [string型], 通貨記号を取りたい値  (例 \ 1,000.0000)
// 戻り値 : str, [string型], 通貨記号を取り除いた値(例   1,000.0000)
//******************************************************************************
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


//@*****************************************************************************
// 概要   : 通貨記号を付ける
// 対象   : すべて
// 引数   : num, [string型], 通貨記号を付けたい値 (例   1,000.0000)
// 戻り値 : str, [string型], 通貨記号を付けた値   (例 \ 1,000.0000)
//******************************************************************************
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


//@*****************************************************************************
// 概要   : 今日の日付けを返す
// 対象   : すべて
// 戻り値 : YYYYMMDD, [string型], YYYY/MM/DD
//******************************************************************************
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


//@*****************************************************************************
// 概要   : YYYY/MM/DD を YYYY/MM で返す
// 引数   : objObject, [object型], YYYY/MM/DDの値が入力されているオブジェクト
// 対象   : 商品管理
//******************************************************************************
function fncYYMM(objObject)
{
	strDate = new Date(objObject.value);

	var strYY = strDate.getFullYear();
	var strMM = strDate.getMonth() + 1;

	if (strMM < 10) { strMM = "0" + strMM; }

	objObject.value = strYY + "/" + strMM;
}

//-->
