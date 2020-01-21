// ---------------------------------------------------------------------------
// ファイル概要：
//	Webオブジェクトチェック用関数
//
// 備考：
//       JavaScript -> VBScript呼び出しを行い、エラー時 fncErrorMessage() をキックする
// 
//		数値チェック	：fncCheckNumber()
//		日付チェック	：fncCheckDate()
// 		文字チェック	：fncCheckString()
// 
// 		通貨記号変換	：fncCheckNumberCurrencySign()
// 
// 		エラーキック	：fncErrorMessage()
// 
// 
// 
// 
// 作成日：2003/10/09
// 作成者：Kazushi Saito
// 修正履歴：
//		2004/03/29 K.S
//			fncCheckNumberValue() 小数点以下切捨て処理の追加
//
// ---------------------------------------------------------------------------

	// エラーが起きたオブジェクトの名前
	var m_strErrorObjectName = "";

	var m_lngErrorFlag = 0;

	// -------------------------------------------------------
	// エラー出力
	// 引数：
	//		lngCheckID	-	チェックID
	//		aryArgs		-	配列受渡値
	// -------------------------------------------------------
	function fncErrorMessage(lngCheckID, strErrorObjectName, aryArgs)
	{


		// 自身を参照する場合
		if( typeof(ErrorMessageFrame) != 'undefined' )
		{
			objWindow1 = window.ErrorMessageFrame;
			objWindow2 = window.errorWin;
		}
		// Iframeから参照する場合
		else
		{
			objWindow1 = parent.ErrorMessageFrame;
			objWindow2 = parent.errorWin;
		}


		switch (lngCheckID)
		{
			// 正常終了
			case 0:
				// 一度でもエラーが起きたオブジェクトの名前と同一だったら
				if( m_strErrorObjectName == strErrorObjectName )
				{
					// 初期化
					m_strErrorObjectName = "";

					// エラーメッセージ非表示
					objWindow1.style.visibility = 'hidden';
					objWindow2.ErrMeg.innerText = '';

					m_lngErrorFlag = 0;
					//window.status = "";
				}
				break;
				
			// 数値エラー
			case 1:
				// エラーオブジェクト名を設定
				m_strErrorObjectName = strErrorObjectName;

				// エラーメッセージ表示
				objWindow1.style.visibility = 'visible';
				objWindow2.ErrMeg.innerText = '正しい数値での入力が必要です。';

				m_lngErrorFlag = 1;
				//window.status = ("正しい数値での入力が必要です。");
				break;
			
			// 日付エラー
			case 2:
				// エラーオブジェクト名を設定
				m_strErrorObjectName = strErrorObjectName;

				// エラーメッセージ表示
				objWindow1.style.visibility = 'visible';
				objWindow2.ErrMeg.innerText = '正しい日付での入力が必要です。';

				m_lngErrorFlag = 1;
				//window.status = ("正しい日付での入力が必要です。");
				break;
			
			// 文字数エラー
			case 3:
				// エラーオブジェクト名を設定
				m_strErrorObjectName = strErrorObjectName;

				// エラーメッセージ表示
				objWindow1.style.visibility = 'visible';
				objWindow2.ErrMeg.innerText = '文字数オーバー : ［' + (aryArgs[1]-aryArgs[0]) + 'バイト］削除して下さい。';

				m_lngErrorFlag = 1;
				//window.status = ("文字数制限［" + aryArgs[0] + "バイト］に対して［" + aryArgs[1] + "バイト］の文字があり多すぎます。\n［" + (aryArgs[1]-aryArgs[0]) + "バイト］削除して下さい。");
				break;
			default:
		}
		
	}
	
	
	// -------------------------------------------------------
	// 通貨記号の文字列値
	var m_strInitCurrencySign = "\\";
	var m_strSetingCurrencySign = m_strInitCurrencySign;
	var m_aryCheckSetValue = new Array();
	
	
	// -------------------------------------------------------
	// 通貨記号の設定
	// -------------------------------------------------------
	function fncCheckNumberCurrencySign(strCurrency)
	{
		// デフォルト通貨記号を設定
		m_strSetingCurrencySign = strCurrency;
	}
	
	
	// -------------------------------------------------------
	// m_aryCheckSetValue エリアへ値を保持する
	// -------------------------------------------------------
	function fncCheckSetValue(lngSetID, objValue)
	{
		
		m_aryCheckSetValue[lngSetID] = objValue;

	}
	
	// -------------------------------------------------------
	// m_aryCheckSetValue エリアの値を取得する
	// -------------------------------------------------------
	function fncCheckGetValue(lngSetID)
	{
		if( typeof(m_aryCheckSetValue[lngSetID]) == 'undefined' )
		{
			return "(Error) lngCheck.js";
		}
		
		return m_aryCheckSetValue[lngSetID];

	}


	// -------------------------------------------------------
	// 概要：数値のチェック
	// 引数：
	//		objObject			-	画面内のオブジェクト
	//		lngAfterDecimal		-	小数点以下何桁で指定するか
	//		blnCurrencySign		-	通貨記号を付加するか
	// 備考：
	// -------------------------------------------------------
	function fncCheckNumber(objObject, lngAfterDecimal, blnCurrencySign)
	{
		// 通貨記号の指定
		strCurrencySign = m_strSetingCurrencySign;
		// 通貨記号を消す場合
		if( blnCurrencySign == false )
		{
			strCurrencySign = "";
		}
		
		/* window.execScriptはEdge非対応のため、fncVBSCheckNumberをJavascriptに実装しなおしてeval実行
		// 対象オブジェクト名の取得
		strObjectName = 'window.'+ objObject.form.name +'.'+ objObject.name;
		strParam = 'Call fncVBSCheckNumber('+ strObjectName +', '+ lngAfterDecimal + ', '+ '"' + strCurrencySign + '"' +')';

		// VBScriptの実行
		window.execScript(strParam, "VBScript");
		*/
		
		eval(strParam);

	}

	// -------------------------------------------------------
	// 概要：数値のフォーマット
	// 引数：
	//		strValue			-	実数値
	//		lngAfterDecimal		-	小数点以下何桁で指定するか
	//		blnCurrencySign		-	通貨記号を付加するか
	//		lngDecimalCutPoint	-	小数点以下を何桁で切り捨てるか（引数を指定しない場合 or null の場合は切捨てなし）
	//		lngCalcCode			-	計算方法種別
	//
	// 備考：この関数はScript内で使用すること。
	// -------------------------------------------------------
	function fncCheckNumberValue(strValue, lngAfterDecimal, blnCurrencySign, lngDecimalCutPoint, lngCalcCode)
	{
		
		// 通貨記号の指定
		strCurrencySign = m_strSetingCurrencySign;
		// 通貨記号を消す場合
		if( blnCurrencySign == false )
		{
			strCurrencySign = "";
		}
		//alert(lngDecimalCutPoint);
		// 指定無しの場合に、null で渡す
		if( typeof(lngDecimalCutPoint) == "undefined" )
		{
			lngDecimalCutPoint = null;
		}

		/* window.execScriptはEdge非対応のため実行関数をjavascriptに置換して直接実行
		// 対象オブジェクト名の取得
		//strParam = 'Call fncVBSCheckNumberValue("'+ strValue +'", '+ lngAfterDecimal + ', '+ '"' + strCurrencySign + '", ' + lngDecimalCutPoint + ', ' + lngCalcCode + ')';
		
		// VBScriptの実行
		//window.execScript(strParam, "VBScript");
		*/

		var ret = fncVBSCheckNumberValue(strValue,lngAfterDecimal,strCurrencySign,lngDecimalCutPoint,lngCalcCode);

		// 取得したフォーマット値を返却
		return fncCheckGetValue(1);

	}

	// -------------------------------------------------------
	// 概要：日付のチェック
	// 引数：
	//		objObject		Textオブジェクト
	//		lngFormatNo		0 or 指定無し:YYYY/MM/DD
	//		                1:YYYY/MM
	// 戻り値：
	// -------------------------------------------------------
	function fncCheckDate(objObject, lngFormatNo)
	{
		lngFormat = 0;	// 0:YYYY/MM/DD でのフォーマット
			
		// 対象オブジェクト名の取得
		strObjectName = 'window.'+ objObject.form.name +'.'+ objObject.name;
		
		var strDate = objObject.value;
		
		// 空文字は無視
	    if(strDate == ""){
	        fncErrorMessage(0, strObjectName);
	        return true;
	    }
		
		// 番号が指定されている場合
		if( isNaN(lngFormatNo) == false )
		{
	        // 年/月の形式のみ許容する
	        if(!strDate.match(/^\d{4}\/\d{1,2}$/)){
	            fncErrorMessage(2, strObjectName);
	            return false;
	        }
	        // 日付変換された日付が入力値と同じ事を確認
	        // new Date()の引数に不正な日付が入力された場合、相当する日付に変換されてしまうため
	        // 
	        var date = new Date(strDate);  
	        if(date.getFullYear() !=  strDate.split("/")[0] 
	            || date.getMonth() != strDate.split("/")[1] - 1 
	        ){
	            fncErrorMessage(2, strObjectName);
	            return false;
	        }

	        if(date.getFullYear() <= 1600){
	    	    var today = new Date();  
	            objObject.value = today.getFullYear() + "/" + (today.getMonth() + 1);
	        }
		}
		else
		{
	        // 年/月/日の形式のみ許容する
	        if(!strDate.match(/^\d{4}\/\d{1,2}\/\d{1,2}$/)){
	            fncErrorMessage(2, strObjectName);
	            return false;
	        }
	        // 日付変換された日付が入力値と同じ事を確認
	        // new Date()の引数に不正な日付が入力された場合、相当する日付に変換されてしまうため
	        // 
	        var date = new Date(strDate);  
	        if(date.getFullYear() !=  strDate.split("/")[0] 
	            || date.getMonth() != strDate.split("/")[1] - 1 
	            || date.getDate() != strDate.split("/")[2]
	        ){
	            fncErrorMessage(2, strObjectName);
	            return false;
	        }

	        if(date.getFullYear() <= 1600){
	    	    var today = new Date();  
	            objObject.value = today.getFullYear() + "/" + (today.getMonth() + 1) + "/" + today.getDate();
	        }
	    }


	    fncErrorMessage(0, strObjectName);
	    return true;
	}

	// -------------------------------------------------------
	// 文字のチェック
	// 引数：
	// 		objObject	Textオブジェクト
	//		lngMaxLength	チェックしたい最大Byte数
	// -------------------------------------------------------
	function fncCheckString(objObject, lngMaxLength)
	{

		lngMaxLen = 0;

		// 最大値を取得
		if( isNaN(lngMaxLength) == false)
		{
			lngMaxLen = lngMaxLength;
		}

		// maxLengthが指定されていない && textオブジェクトの場合、オブジェクトのmaxLength を設定
		if( lngMaxLen == 0 && objObject.type == "text" )
		{
			lngMaxLen = objObject.maxLength;
		}
		
		// 入力文字Byte数の取得
		lngObjLen = getLength(objObject.value);
		
		// 最大文字Byteと入力文字Byteの比較
		if( lngObjLen > lngMaxLen )
		{
			var aryArgs = new Array;
			aryArgs[0] = lngMaxLen;
			aryArgs[1] = lngObjLen;
			// エラー出力
			fncErrorMessage(3, objObject.name ,aryArgs);
			objObject.select();
			return false;
		}
		
		// エラー無し
		fncErrorMessage(0, objObject.name);
		return true;
		
		// 対象オブジェクト名の取得
		//strObjectName = 'window.'+ objObject.form.name +'.'+ objObject.name;
		// VBScriptの実行
		//window.execScript('Call fncVBSCheckString('+ strObjectName +', '+ lngMaxLength +')', "VBScript");
		
	}

	// -------------------------------------------------------
	// 特殊文字の変換
	// -------------------------------------------------------
	function fncCheckReplaceString( strInString )
	{
		if( strInString == "" || typeof( strInString ) == "undefined" ) return "";
	
		var strValue = strInString;

		//strValue = strValue.replace( /</g , "&lt;" ).replace( />/g , "&gt;" );
		//strValue = strValue.replace( /&/g ,"&amp;" );
		//strValue = strValue.replace( /\'/g ,"&#039;" );

		strValue = strValue.replace( /\"/g ,"&quot;" );

		return strValue;
	}

	// -------------------------------------------------------
	// 文字列をByte換算する
	// -------------------------------------------------------
	function getLength(strValue)
	{
		var i,cnt = 0;
		for(i=0; i<strValue.length; i++) if (escape(strValue.charAt(i)).length >= 4 ) cnt+=2; else cnt++;
		return cnt;
	}

	// -------------------------------------------------------
	// 文字のチェック
	// 引数：
	// 		lngCalc1		計算値１
	//		strOperator		算術演算子 ( ^, *, /, \, Mod, +, - )
	// 		lngCalc2		計算値２
	// 備考：
	//  結果ID = 5
	// -------------------------------------------------------
	function fncVBSNumCalc(lngCalc1, strOperator, lngCalc2)
	{
		// alert("[" + parseFloat(lngCalc1) + "]" + strOperator + "[" +  parseFloat(lngCalc2) + "]");
		// 数値では無い場合、0で返却
		if( isNaN(parseFloat(lngCalc1)) || isNaN(parseFloat(lngCalc2)) )
		{
			return 0;
		}

		// 対象オブジェクト名の取得
		//strParam = 'Call fncVBSNumberCalculation('+ lngCalc1 + ', '+ '"' + strOperator + '", ' + lngCalc2 + ')';
		strParam = lngCalc1 + " " + strOperator + " " +  lngCalc2;
		//alert(strParam);
		// VBScriptの実行
		//window.execScript(strParam, "VBScript");
		eval(strParam);
		// 取得した値を返却
		return fncCheckGetValue(5);

	}

