/*
' -----------------------------------------------------------------------------
' ファイル概要：
'               VBScript 共通クラス
' 備考：
'       共通で使用するクラス群
'
' 作成日：2003/10/09
' 作成者：Kazushi Saito
' 修正履歴：
'	2004/03/29 K.S
'		fncComFormatNumberValue() 小数点以下切捨て処理の追加
'		fncComDecimalCut() メソッドの追加
'
'
'
' -----------------------------------------------------------------------------
*/

/*
' ------------------------------------------
' グローバル変数定義
' ------------------------------------------
*/
const var c_lngAfterDecimal     	= 4;			// 処理対象の小数点以下の桁数（丸め処理）
const var c_strFormatNumberErr  	= "(Error)";	// fncComFormatNumberValue() エラー時の文字列
const var c_strDecimalPointChar 	= ".";		'// 小数点位置の文字

/*
' ------------------------------------------
' 概要：チェッククラス
' 備考：数値、日付、のチェックを行なう
' ------------------------------------------
*/
class clsCheck{
	
	constructor(){
		Class_Initialize();
	}
	
	var m_strNumberCurrencySign		// 通貨記号
	var m_strNumberConnectionChar	// 通貨記号と金額を接続する文字
	var m_lngNumberAfterDecimal		// 小数点以下の桁
	var m_lngStringMaxLength		// 文字列の最大数
	var m_lngDateFormatNo			// 日付フォーマット（0=YYYY/MM/DD or 1=YYYY/MM）
	
	var m_strFormatNumberValue		// fncComFormatNumberValue() によりフォーマットされた直後の値
	var m_lngDecimalCutPoint		// 小数点以下切捨て位置
	var m_lngCalcClass				// 計算方法種別
	
	/*
	// ------------------------------------------
	// ------------------------------------------
	*/
	Class_Initialize(){
		//数値処理にて小数点以下の桁数を何桁にして処理するかを初期設定
		this.NumberAfterDecimal = c_lngAfterDecimal
	
	}

	/*
	// ------------------------------------------
	// 概要：通貨記号
	// 引数：strValue 値
	// ------------------------------------------
	*/
	set NumberCurrencySign(strValue){
		this.m_strNumberCurrencySign = strValue;
	}

	get NumberCurrencySign(){
		return this.m_strNumberCurrencySign;
	}

	/*
	// ------------------------------------------
	// 概要：通貨記号と金額を接続する文字
	// 引数：strValue 値
	// ------------------------------------------
	*/
	set NumberConnectionChar(strValue){
		this.m_strNumberConnectionChar = strValue;
	}

	get NumberConnectionChar(){
		return this.m_strNumberConnectionChar;
	}

	/*
	// ------------------------------------------
	// 概要：小数点以下の計算を何桁にするか
	// 引数：lngValue 値
	// ------------------------------------------
	*/
	set NumberAfterDecimal(lngValue){
		this.m_lngNumberAfterDecimal = lngValue;
	}

	get NumberAfterDecimal(){
		return this.m_lngNumberAfterDecimal;
	}

	/*
	// ------------------------------------------
	// 概要：文字列の最大数
	// 引数：lngValue 値
	// ------------------------------------------
	*/
	set StringMaxLength(lngValue){
		this.m_lngStringMaxLength = lngValue;
	}

	get StringMaxLength() {
		return this.m_lngStringMaxLength;
	}
	
	/*
	// ------------------------------------------
	// 概要：通貨記号
	// 引数：strValue 値
	// ------------------------------------------
	*/
	set FormatNumberValue(strValue){
		this.m_strFormatNumberValue = strValue;
	}

	get FormatNumberValue(){
		return this.m_strFormatNumberValue;
	}

	/*
	// ------------------------------------------
	// 概要：日付フォーマットNo
	// 引数：lngValue 値
	// ------------------------------------------
	*/
	set DateFormatNo(lngValue){
		this.m_lngDateFormatNo = lngValue;
	}

	get DateFormatNo(){
		return m_lngDateFormatNo;
	}

	/*
	// ------------------------------------------
	// 概要：小数点以下切捨て位置
	// 引数：lngValue 値
	// ------------------------------------------
	*/
	set DecimalCutPoint(lngValue){
		this.m_lngDecimalCutPoint = lngValue;
	}

	get DecimalCutPoint(){
		return this.m_lngDecimalCutPoint;
	}

	/*
	// ------------------------------------------
	// 概要：計算方法種別
	// 引数：lngValue 値
	// ------------------------------------------
	*/
	set CalcClass(lngValue){
		this.m_lngCalcClass = lngValue;
	}

	get CalcClass() {
		return  this.m_lngCalcClass;
	}


	/*
	// ------------------------------------------
	// 概要：数値のフォーマット（実数値版）
	// 引数：strValue			値（String）
	//		blnCurrencyEscape	通貨記号のエスケープ可否（\マーク）
	//
	// 戻り値：フォーマットされた値
	// 備考：
	// ------------------------------------------
	*/
	function fncComFormatNumberValue( strValue, blnCurrencyEscape ){
		try{

			var lngValue;		// テキスト値
			var curValue;		// 変換後の数値
			var aryValue;		// テキスト値（通貨記号＋金額）
			var strAfValue;		// フォーマット後の値
			var strCurrencySign;	// 通貨記号
			var curAmount; 		// 金額 money


			// 存在を確認。取得不可能な場合、失敗で返却
			if (!strValue || strValue.length === 0) {
				return false;
			}

			//エラー判定
			If (strValue.trim().length === 0) {
				//関数を成功で返却
				return true;
			}

			// 接続文字がある場合
			//プロパティ指定通貨記号と同じ通貨記号の場合、オブジェクトの値を変数に代入
			If( indexof(NumberConnectionChar) > 0) && NumberConnectionChar != "" ) {
				
				//通貨記号の処理
				aryValue = strValue.split(NumberConnectionChar);
				strCurrencySign = aryValue[0];
				curAmount = aryValue[1];
				lngValue = curAmount;
				
			//接続文字が無い場合
			}else {
				//通貨記号があった場合、削除
				lngValue = strValue.replace( NumberCurrencySign, "" );
			}
	
			//数値への変換が可能かを調べる
			if (isNan(lngValue ) ) {
				//関数を失敗で返却
				return false;
			}

//			lngValue = Left(CStr(lngValue), NumberAfterDecimal)
			//msgbox "処理対象：" & curValue & vbcrlf & "数値処理：" & CalcClass & vbcrlf & "切捨て位置：" & DecimalCutPoint & vbcrlf & "フォーマット位置：" & NumberAfterDecimal

			//数値に変換
			curValue = new number(lngValue);
	
			switch(CalcClass){
				// 小数点以下切捨て処理
				case 1:
					curValue = fncComDecimalCut( curValue, DecimalCutPoint , NumberAfterDecimal );
				// 小数点以下切上げ処理
				case 2:
					curValue = fncComDecimalUp( curValue, DecimalCutPoint , NumberAfterDecimal );
				default:
					curValue = FormatNumber(curValue, NumberAfterDecimal, True, False);
			}
			
			strCalcValue = FormatNumber(curValue, NumberAfterDecimal, True, False);


			//数値形式にフォーマットして、元のテキストボックスへ設定
			strAfValue = new string(NumberCurrencySign) +  new string(NumberConnectionChar) + new string(strCalcValue);


			//値保持　\ がエスケープされてしまうため、\\ に変換
			if ( blnCurrencyEscape ) {
				FormatNumberValue = strAfValue.replace( "\", "\\");
			} else {
				FormatNumberValue = strAfValue
			}

			//成功を返却
			return true
		}
		catch( exception ) {
			//もしエラーが起きていた場合
			return  c_strFormatNumberErr;
		}

	}

	/*
	// ------------------------------------------
	// 概要：数値の切捨て処理
	// 引数：
	//		curValue		-	切捨て対象の数値
	//		lngPoint		-	切捨て対象の小数点からの位置
	//		lngAfterDecimal	-	切捨て後の数値の小数点以下を何文字0で埋めるか
	//
	// 戻り値：切捨てた数値／エラーの場合 0
	// 備考：小数点以下のみに対応。整数部の切捨て処理には未対応。
	// ------------------------------------------
	*/
	function fncComDecimalCut( curValue , lngPoint, lngAfterDecimal ){

		var strValue;		// 切り捨て対象の数値を文字列変換したもの
		var aryValue;
		var strNumber;
		var strDecimal;
		var curAfValue;		// 切捨て後の数値
		var strZeroFormat;	// 0埋め処理用

		//文字列へ変換
		strValue = new string(curValue);
		
		If (strValue.indexof(c_strDecimalPointChar) > 0) {
			//通貨記号の処理
			aryValue = strValue.split(c_strDecimalPointChar);
			strNumber  = aryValue[0];
			strDecimal = aryValue[1];
		} else {
			strNumber  = strValue;
			strDecimal = "0".repeat(lngAfterDecimal);
		}

		// 切捨て対象の小数点位置によりフォーマット
		if (lngPoint == 0 ) {
			curAfValue = new string(strNumber) +  new string(c_strDecimalPointChar) + "0".repeat(lngAfterDecimal);
		} else if (lngPoint > 0 ) {
			// フォーマット位置の方が切捨て位置より大きい場合のみ0埋め
			if( lngAfterDecimal > lngPoint ) {
				strZeroFormat = "0".repeat(lngAfterDecimal - lngPoint);
			}
			curAfValue = new string(strNumber) + new string(c_strDecimalPointChar) + new string(strDecimal.substring(0, lngPoint)) + new string(strZeroFormat);
		} else {
			curAfValue = strValue;
		}

		// フォーマットした数値を返却
		fncComDecimalCut = new number(curAfValue);

	}

	/*
	// ------------------------------------------
	// 概要：数値の切上げ処理
	// 引数：
	//		curValue		-	切上げ対象の数値
	//		lngPoint		-	切上げ対象の小数点からの位置
	//		lngAfterDecimal	-	切上げ後の数値の小数点以下を何文字0で埋めるか
	//
	// 戻り値：切捨てた数値／エラーの場合 0
	// 備考：小数点以下のみに対応。整数部の切上げ処理には未対応。
	// ------------------------------------------
	*/
	function fncComDecimalUp( curValue , lngPoint, lngAfterDecimal ){

		var strValue;		// 切り捨て対象の数値を文字列変換したもの
		var aryValue;
		var strNumber;
		var strDecimal;
		var curAfValue;		// 切捨て後の数値
		var strZeroFormat;	// 0埋め処理用
		
		//文字列へ変換
		strValue = new string(curValue);
		
		
		if (strValue.indexof(c_strDecimalPointChar) > 0) {
			//通貨記号の処理
			aryValue = strValue.split( c_strDecimalPointChar);
			strNumber  = aryValue[0];
			strDecimal = aryValue[1];
		} else
			strNumber  = strValue;
			strDecimal = "0".repeat(lngAfterDecimal);
		}

		// 切捨て対象の小数点位置によりフォーマット
		if (lngPoint == 0 ) {
			// 切捨て以下が 0 では無い場合、strNumber を切上げ
			if( new number(strDecimal) != 0 ) {
				strNumber = new string(new number(strNumber) + 1);
			}
			// フォーマット（ex: 12345.00）
			curAfValue = new string(strNumber) + new string(c_strDecimalPointChar) + "0".repeat(lngAfterDecimal);
		} else if (lngPoint > 0 ) {
			// 切捨て以下（strDecimalのPOINT右辺）が 0 では無い場合、strDecimal（POINT左辺）を切上げ
			if( new number( strDecimal.substr(lngPoint) ) <> 0 ) {
				strDecimal = new string(new number(strDecimal.substring(0, lngPoint)) + 1);
			}
			// フォーマット位置の方が切捨て位置より大きい場合のみ0埋め
			if( lngAfterDecimal > lngPoint ) {
				strZeroFormat = "0".repeat(lngAfterDecimal - lngPoint);
			}
			curAfValue = new string(strNumber) + new string(c_strDecimalPointChar) + new string(strDecimal) + new string(strZeroFormat);
		} else {
			curAfValue = strValue;
		}

		// フォーマットした数値を返却
		return new number(curAfValue);

	}


	/*
	// ------------------------------------------
	// 概要：文字列から日付の作成
	// 引数：文字列
	// 戻り値：True/False 成功、失敗
	// 備考：文字列から日付が生成不可能な場合、本日日付を設定
	// ------------------------------------------
	*/
	// javascriptは参照渡し引数にstringを使えないため、arrayに変更
	function fncComTextToDate( arrayValue )
		
		var strDate = "";		//文字列日付
	
	
		//文字列分ループ処理
		for (lngCnt = 0; lngCnt < arrayValue[0].length; lngCnt++){
			//一文字だけ取得
			strBuff = arrayValue[0].substring(lngCnt, 1);
			//数値かどうかの判定
			if ( !IsNan( strBuff ) ) {
				strDate = strDate + strBuff
				//4,6文字目に/を設定
				switch( lngCnt ) {
					case 3:
					case 5:
						strDate = strDate + "/";
				}
				
			}
		}
		
		//2003/ の形式の場合、01/01を追加
		if ( strDate.length == 5 ) {
			strDate = strDate + "01/01";
		}
		//2003/10/ の形式の場合、01を追加
		if ( strDate.length == 8 ) {
			strDate = strDate + "01";
		}
		

	    // javascriptのnew Dateが不正な値を補正してしまい、チェックできないため、
	    // 変換不可能チェックの方式を変更する。
		var strArray = strDate.split("/");
		var tempDate = new Date(strArray[0],strArray[1]-1,strArray[2]);
		if( strArray[0] !== string(tempDate.getFullYear()) || 
		    number(strArray[1]) !== tempDate.getMonth() + 1 || 
		    number(strArray[2]) !== tempDate.getDate() ) {
		    return false;
		}
		/*
		//日付に変換不可能な場合
		If Not IsDate(strDate) Then
			fncComTextToDate = False;
			Exit Function
		End If
		*/
		//引数値へ設定
		arrayValue[0] = strDate;
		
		//成功を返却
		return true;
	}

	/*
	// ------------------------------------------
	// 概要：日時のフォーマット
	// 引数：TextBoxオブジェクト
	// 戻り値：True/False 成功、失敗
	// 備考：
	// ------------------------------------------
	*/
	function fncComFormatDateTime( objText ){

		var dtmValue;	//テキスト値

		//オブジェクトの存在を確認。取得不可能な場合、失敗で返却
		If ( !objText.value ) {
		     return false;
		}
		//オブジェクトの値を変数に代入
		dtmValue = new array({objText.value});

		//エラー判定
		if (dtmValue[0].trim().length == 0 ){
			//関数を成功で返却
			return true;
		}
		
		//エラー判定
		if ( dtmValue[0].length < 4 ) {
			// objText.Value = fncComDate(Date, DateFormatNo )
			objText.focus();
			//関数を失敗で返却
			return false:
		}

		//日時への変換が可能かを調べる
		//If Not IsDate( dtmValue[0] ) Then
			//テキストから日付形式へ変換を挑戦
			if ( !fncComTextToDate( dtmValue ) {
				//数値への変換が不可能な場合、現在日付を元のテキストボックスへ設定
				// objText.Value = fncComDate(Date, DateFormatNo )
				objText.focus();
				//関数を失敗で返却
				return false;
			}
		//End If

        // fncComTextToDateで変換した意味がなくなるため削除
		//日付型に変換
		//dtmValue[0] = CDate(dtmValue)

		//Web用のチェック 1600年以下の場合無効
		if( number(dtmValue[0].substr(0,4)) <= 1600 ) {
			var today = new Date();
			var y = today.getFullYear();
			var m = ("00" + (today.getMonth()+1)).slice(-2);
			var d = ("00" + today.getDate()).slice(-2);
			var result = y + "/" + m + "/" + d;
			dtmValue[0] = result;
		}

		//数値形式にフォーマットして、元のテキストボックスへ設定
		objText.Value = fncComDate(dtmValue[0], DateFormatNo );
		
		//成功を返却
		return true;

	}

	/*
	// ------------------------------------------
	// 概要：日付のフォーマット（OS依存なし版）
	// 引数：dtmValue	日付値
	//		lngFormatNo YYYY/MMの場合 1
	//					YYYY/MM/DD はその他
	// 戻り値：フォーマットされた日付文字列
	// 備考：
	// ------------------------------------------
	*/
	function fncComDate( dtmValue, lngFormatNo ){
		// 変換済みの値を再変換するなど、無駄な処理が多すぎるため全面修正
		var varlist = dtmvalue.split("/");
		var result;
		if( lngFormatNo == 1 )
		{
			// YYYY/MM
			result = string(varlist[0]) + "/" +  string(varlist[1]);
		}
		else
		{
			// YYYY/MM/DD
			result = string(varlist[0]) + "/" +  string(varlist[1]) + "/" +  string(varlist[2]);
		}
		return result;
		
		/*
		dtmDate  = FormatDateTime(dtmValue, vbGeneralDate)
 		strYear  = Year(dtmDate)
 		strMonth = Month(dtmDate)
 		strDay   = Day(dtmDate)

		// 月と日のみ、一桁チェック。一桁の場合は先頭に 0 を付加する。
		If Len(strMonth) = 1 Then
			strMonth = "0" & strMonth
		End If
		
		If Len(strDay) = 1 Then
			strDay = "0" & strDay
		End If

		// フォーマットフラグが１の場合 YYYY/MM フォーマット
		If lngFormatNo = 1 Then
			strDate = strYear & "/" & strMonth
		Else
			strDate = strYear & "/" & strMonth & "/" & strDay
		End If

		fncComDate = strDate
		*/
	}

	/*
	// ------------------------------------------
	// 概要：文字列の検査
	// 引数：TextBox or Textareaオブジェクト
	// 戻り値：True/False 成功、失敗
	// 備考：
	// ------------------------------------------
	*/
	/* 利用者なしのため削除
	function fncComInspectionString( ByRef objText ){
	
		strString = objText.value //StrConv(objText.value, 1)
		
		MsgBox(LenB(strString) & ":" & strString)
		fncComInspectionString = True
		
	}
	*/

	/*
	// ------------------------------------------
	// 概要：オブジェクトのフォーマット
	// 引数：
	//       objObject	- TextBoxオブジェクト
	//       lngCheckID	- チェックID
	//                      1:数値処理
	//                      2:日付処理
	//
	// 戻り値：True/False 成功、失敗
	// 備考：
	//       この関数は、親WindowのJavaScript関数 fncErrorMessage() を呼び出します。
	//       同時に実装する必要があります。
	// ------------------------------------------
	*/
	function fncCheckObjectValue( objObject, lngCheckID ){
		//On Error Resume Next
				
		switch(lngCheckID){
			// 数値処理
			case 1:
				if( fncComFormatNumberValue( objObject.value, false ) ) {
					// フォーマット成功の場合、オブジェクトのValueへ直接指定
					objObject.value = FormatNumberValue
					return true;
				}
			// 日付処理
			case 2:
				if( fncComFormatDateTime( objObject ) ){
					return true;
				}
			// 文字列処理
			case 3:
				if( fncComInspectionString( objObject ) ) {
					return true;
				}
		}
		
		//失敗を返却
		return  false

	}

}

/* 全コードJavaScriptに換装するため、コメントアウト
'Class clsCheck
'	
'	
'	Private m_strNumberCurrencySign		' 通貨記号
'	Private m_strNumberConnectionChar	' 通貨記号と金額を接続する文字
'	Private m_lngNumberAfterDecimal		' 小数点以下の桁
'	Private m_lngStringMaxLength		' 文字列の最大数
'	Private m_lngDateFormatNo			' 日付フォーマット（0=YYYY/MM/DD or 1=YYYY/MM）
'	
'	Private m_strFormatNumberValue		' fncComFormatNumberValue() によりフォーマットされた直後の値
'	Private m_lngDecimalCutPoint		' 小数点以下切捨て位置
'	Private m_lngCalcClass				' 計算方法種別
'	
'	' ------------------------------------------
'	' ------------------------------------------
'	Private Sub Class_Initialize()
'	
'	
'		'数値処理にて小数点以下の桁数を何桁にして処理するかを初期設定
'		NumberAfterDecimal = c_lngAfterDecimal
'	
'	End Sub
'
'	' ------------------------------------------
'	' 概要：通貨記号
'	' 引数：strValue 値
'	' ------------------------------------------
'	Public Property Let NumberCurrencySign(strValue)
'		m_strNumberCurrencySign = strValue
'	End Property
'
'	Public Property Get NumberCurrencySign()
'		NumberCurrencySign = m_strNumberCurrencySign
'	End Property
'
'	' ------------------------------------------
'	' 概要：通貨記号と金額を接続する文字
'	' 引数：strValue 値
'	' ------------------------------------------
'	Public Property Let NumberConnectionChar(strValue)
'		m_strNumberConnectionChar = strValue
'	End Property
'
'	Public Property Get NumberConnectionChar()
'		NumberConnectionChar = m_strNumberConnectionChar
'	End Property
'
'	' ------------------------------------------
'	' 概要：小数点以下の計算を何桁にするか
'	' 引数：lngValue 値
'	' ------------------------------------------
'	Public Property Let NumberAfterDecimal(lngValue)
'		m_lngNumberAfterDecimal = lngValue
'	End Property
'
'	Public Property Get NumberAfterDecimal()
'		NumberAfterDecimal = m_lngNumberAfterDecimal
'	End Property
'
'	' ------------------------------------------
'	' 概要：文字列の最大数
'	' 引数：lngValue 値
'	' ------------------------------------------
'	Public Property Let StringMaxLength(lngValue)
'		m_lngStringMaxLength = lngValue
'	End Property
'
'	Public Property Get StringMaxLength()
'		StringMaxLength = m_lngStringMaxLength
'	End Property
'	
'	' ------------------------------------------
'	' 概要：通貨記号
'	' 引数：strValue 値
'	' ------------------------------------------
'	Public Property Let FormatNumberValue(strValue)
'		m_strFormatNumberValue = strValue
'	End Property
'
'	Public Property Get FormatNumberValue()
'		FormatNumberValue = m_strFormatNumberValue
'	End Property
'
'	' ------------------------------------------
'	' 概要：日付フォーマットNo
'	' 引数：lngValue 値
'	' ------------------------------------------
'	Public Property Let DateFormatNo(lngValue)
'		m_lngDateFormatNo = lngValue
'	End Property
'
'	Public Property Get DateFormatNo()
'		DateFormatNo = m_lngDateFormatNo
'	End Property
'
'	' ------------------------------------------
'	' 概要：小数点以下切捨て位置
'	' 引数：lngValue 値
'	' ------------------------------------------
'	Public Property Let DecimalCutPoint(lngValue)
'		m_lngDecimalCutPoint = lngValue
'	End Property
'
'	Public Property Get DecimalCutPoint()
'		DecimalCutPoint = m_lngDecimalCutPoint
'	End Property
'
'	' ------------------------------------------
'	' 概要：計算方法種別
'	' 引数：lngValue 値
'	' ------------------------------------------
'	Public Property Let CalcClass(lngValue)
'		m_lngCalcClass = lngValue
'	End Property
'
'	Public Property Get CalcClass()
'		CalcClass = m_lngCalcClass
'	End Property
'
'
'	' ------------------------------------------
'	' 概要：数値のフォーマット（実数値版）
'	' 引数：strValue			値（String）
'	'		blnCurrencyEscape	通貨記号のエスケープ可否（\マーク）
'	'
'	' 戻り値：フォーマットされた値
'	' 備考：
'	' ------------------------------------------
'	Public Function fncComFormatNumberValue( strValue, blnCurrencyEscape )
'		On Error Resume Next
'
'		Dim lngValue		' テキスト値
'		Dim curValue		' 変換後の数値
'		Dim aryValue		' テキスト値（通貨記号＋金額）
'		Dim strAfValue		' フォーマット後の値
'		Dim strCurrencySign	' 通貨記号
'		Dim curAmount 		' 金額 money
'
'
'		'関数の戻り値を初期化
'		fncComFormatNumberValue = False
'
'		' 存在を確認。取得不可能な場合、失敗で返却
'		If IsEmpty( strValue ) Then Exit Function
'
'		'エラー判定
'		If Len(Trim(strValue)) = 0 Then
'			'関数を成功で返却
'			fncComFormatNumberValue = True
'			Exit Function
'		End If
'
'		' 接続文字がある場合
'		'プロパティ指定通貨記号と同じ通貨記号の場合、オブジェクトの値を変数に代入
'		If( (Instr(strValue, NumberConnectionChar) > 0) AND (NumberConnectionChar <> "") ) Then
'			
'			'通貨記号の処理
'			aryValue = Split(strValue, NumberConnectionChar)
'			strCurrencySign = aryValue(0)
'			curAmount = aryValue(1)
'			lngValue = curAmount
'			
'		'接続文字が無い場合
'		Else
'			'通貨記号があった場合、削除
'			lngValue = Replace( strValue, NumberCurrencySign, "" )
'		End If
'
'		'数値への変換が可能かを調べる
'		If Not IsNumeric( lngValue ) Then
'			'関数を失敗で返却
'			Exit Function
'		End If
'
''		lngValue = Left(CStr(lngValue), NumberAfterDecimal)
'		'msgbox "処理対象：" & curValue & vbcrlf & "数値処理：" & CalcClass & vbcrlf & "切捨て位置：" & DecimalCutPoint & vbcrlf & "フォーマット位置：" & NumberAfterDecimal
'
'		'倍精度浮動小数点数型 (Double) に変換
'		curValue = CDbl(lngValue)
'
'		Select Case CalcClass
'			' 小数点以下切捨て処理
'			Case 1
'				curValue = fncComDecimalCut( curValue, DecimalCutPoint , NumberAfterDecimal )
'			' 小数点以下切上げ処理
'			Case 2
'				curValue = fncComDecimalUp( curValue, DecimalCutPoint , NumberAfterDecimal )
'			Case Else
'				curValue = FormatNumber(curValue, NumberAfterDecimal, True, False)
'		End Select
'		
'		strCalcValue = FormatNumber(curValue, NumberAfterDecimal, True, False)
'
'
'		'数値形式にフォーマットして、元のテキストボックスへ設定
'		strAfValue = NumberCurrencySign & NumberConnectionChar & strCalcValue
'
'
'		'値保持　\ がエスケープされてしまうため、\\ に変換
'		If blnCurrencyEscape Then
'			FormatNumberValue = Replace(strAfValue, "\", "\\")
''		Else
'			FormatNumberValue = strAfValue
'		End If
'
'		'成功を返却
'		fncComFormatNumberValue = True
'
'		'もしエラーが起きていた場合
'		If( Err.number > 0 ) Then
'			FormatNumberValue = c_strFormatNumberErr
'		End If
'
'	End Function
'
'	' ------------------------------------------
'	' 概要：数値の切捨て処理
'	' 引数：
'	'		curValue		-	切捨て対象の数値
'	'		lngPoint		-	切捨て対象の小数点からの位置
'	'		lngAfterDecimal	-	切捨て後の数値の小数点以下を何文字0で埋めるか
'	'
'	' 戻り値：切捨てた数値／エラーの場合 0
'	' 備考：小数点以下のみに対応。整数部の切捨て処理には未対応。
'	' ------------------------------------------
'	Public Function fncComDecimalCut( curValue , lngPoint, lngAfterDecimal )
'
'		Dim strValue		' 切り捨て対象の数値を文字列変換したもの
'		Dim aryValue
'		Dim strNumber
'		Dim strDecimal
'		Dim curAfValue		' 切捨て後の数値
'		Dim strZeroFormat	' 0埋め処理用
'
'		'文字列へ変換
'		strValue = CStr(curValue)
'		
'		If (InStr(strValue, c_strDecimalPointChar) > 0) Then
'			'通貨記号の処理
'			aryValue = Split(strValue, c_strDecimalPointChar)
'			strNumber  = aryValue(0)
'			strDecimal = aryValue(1)
'		Else
'			strNumber  = strValue
'			strDecimal = String( lngAfterDecimal, "0")
'		End If
'
'		' 切捨て対象の小数点位置によりフォーマット
'		If (lngPoint = 0 ) Then
'			curAfValue = strNumber & c_strDecimalPointChar & String( lngAfterDecimal, "0")
'		ElseIf (lngPoint > 0 ) Then
'			' フォーマット位置の方が切捨て位置より大きい場合のみ0埋め
'			If( lngAfterDecimal > lngPoint ) Then
'				strZeroFormat = String( lngAfterDecimal - lngPoint, "0")
'			End If
'			curAfValue = strNumber & c_strDecimalPointChar & Left(strDecimal, lngPoint) & strZeroFormat
'		Else
'			curAfValue = strValue
'		End If
'
'		' フォーマットした数値を返却
'		fncComDecimalCut = (curAfValue)
'
'	End Function
'
'	' ------------------------------------------
'	' 概要：数値の切上げ処理
'	' 引数：
'	'		curValue		-	切上げ対象の数値
'	'		lngPoint		-	切上げ対象の小数点からの位置
'	'		lngAfterDecimal	-	切上げ後の数値の小数点以下を何文字0で埋めるか
'	'
'	' 戻り値：切捨てた数値／エラーの場合 0
'	' 備考：小数点以下のみに対応。整数部の切上げ処理には未対応。
'	' ------------------------------------------]
'	Public Function fncComDecimalUp( curValue , lngPoint, lngAfterDecimal )
'
'		Dim strValue		' 切り捨て対象の数値を文字列変換したもの
'		Dim aryValue
'		Dim strNumber
'		Dim strDecimal
'		Dim curAfValue		' 切捨て後の数値
'		Dim strZeroFormat	' 0埋め処理用
'		
'		'文字列へ変換
'		strValue = CStr(curValue)
'		
'		
'		If (Instr(strValue, c_strDecimalPointChar) > 0) Then
'			'通貨記号の処理
'			aryValue = Split(strValue, c_strDecimalPointChar)
'			strNumber  = aryValue(0)
'			strDecimal = aryValue(1)
'		Else
'			strNumber  = strValue
'			strDecimal = String( lngAfterDecimal, "0")
'		End If
'
'		' 切捨て対象の小数点位置によりフォーマット
'		If (lngPoint = 0 ) Then
'			' 切捨て以下が 0 では無い場合、strNumber を切上げ
'			If(  CInt(strDecimal) <> 0 ) Then
'				strNumber = CStr(CInt(strNumber) + 1)
'			End If
'			' フォーマット（ex: 12345.00）
'			curAfValue = strNumber & c_strDecimalPointChar & String( lngAfterDecimal, "0")
'		ElseIf (lngPoint > 0 ) Then
'			' 切捨て以下（strDecimalのPOINT右辺）が 0 では無い場合、strDecimal（POINT左辺）を切上げ
'			If(  CInt(Mid(strDecimal, lngPoint)) <> 0 ) Then
'				strDecimal = CStr(CInt(Left(strDecimal, lngPoint)) + 1)
'			End If
'			' フォーマット位置の方が切捨て位置より大きい場合のみ0埋め
'			If( lngAfterDecimal > lngPoint ) Then
'				strZeroFormat = String( lngAfterDecimal - lngPoint, "0")
'			End If
'			curAfValue = strNumber & c_strDecimalPointChar & strDecimal & strZeroFormat
'		Else
'			curAfValue = strValue
'		End If
'
'		' フォーマットした数値を返却
'		fncComDecimalUp = (curAfValue)
'
'	End Function
'
'
'	' ------------------------------------------
'	' 概要：文字列から日付の作成
'	' 引数：文字列
'	' 戻り値：True/False 成功、失敗
'	' 備考：文字列から日付が生成不可能な場合、本日日付を設定
'	' ------------------------------------------
'	Public Function fncComTextToDate( ByRef strValue )
'		
'		Dim strDate		'文字列日付
'		fncComTextToDate = False
'	
'		'文字列分ループ処理
'		For lngCnt = 1 To Len(strValue)
'			'一文字だけ取得
'			strBuff = Mid( strValue, lngCnt, 1)
'			'数値かどうかの判定
'			If IsNumeric( strBuff ) Then
'				strDate = strDate & strBuff
'				'4,6文字目に/を設定
'				Select Case lngCnt
'					Case 4,6
'						strDate = strDate & "/"
'				End Select
'				
'			End If
'		Next
'		
'		'2003/ の形式の場合
'		If Len(strDate) = 5 Then
'			strDate = strDate & "01/01"
'		End If
'		'2003/10/ の形式の場合
'		If Len(strDate) = 8 Then
'			strDate = strDate & "01"
'		End If
'
'		'日付に変換不可能な場合
'		If Not IsDate(strDate) Then
'			fncComTextToDate = False
'			Exit Function
'		End If
'		'引数値へ設定
'		strValue = strDate
'		
'		'成功を返却
'		fncComTextToDate = True
'	End Function
'
'	' ------------------------------------------
'	' 概要：日時のフォーマット
'	' 引数：TextBoxオブジェクト
'	' 戻り値：True/False 成功、失敗
'	' 備考：
'	' ------------------------------------------
'	Public Function fncComFormatDateTime( ByRef objText )
'
'		Dim dtmValue	'テキスト値
'
'		'関数の戻り値を初期化
'		fncComFormatDateTime = False
'
'		'オブジェクトの存在を確認。取得不可能な場合、失敗で返却
'		If IsEmpty( objText.value ) Then Exit Function
'		'オブジェクトの値を変数に代入
'		dtmValue = objText.value
'
'		'エラー判定
'		If Len(Trim(dtmValue)) = 0 Then
'			'関数を成功で返却
'			fncComFormatDateTime = True
'			Exit Function
'		End If
'		
'		'エラー判定
'		If Len(dtmValue) < 4 Then
'			' objText.Value = fncComDate(Date, DateFormatNo )
'			objText.select()
'			'関数を失敗で返却
'			Exit Function
'		End If
'
'		'日時への変換が可能かを調べる
'		If Not IsDate( dtmValue ) Then
'			'テキストから日付形式へ変換を挑戦
'			If Not fncComTextToDate( dtmValue ) Then
'				'数値への変換が不可能な場合、現在日付を元のテキストボックスへ設定
'				' objText.Value = fncComDate(Date, DateFormatNo )
'				objText.select()
'				'関数を失敗で返却
'				Exit Function
'			End If
'		End If
'
'		'日付型に変換
'		dtmValue = CDate(dtmValue)
'
'		'Web用のチェック 1600年以下の場合無効
'		if( DatePart("yyyy", dtmValue) <= 1600 ) Then
'			dtmValue = fncComDate(Date, DateFormatNo )
'		End If
'
'		'数値形式にフォーマットして、元のテキストボックスへ設定
'		objText.Value = fncComDate(dtmValue, DateFormatNo )
'		
'		'成功を返却
'		fncComFormatDateTime = True
'
'	End Function
'
'	' ------------------------------------------
'	' 概要：日付のフォーマット（OS依存なし版）
'	' 引数：dtmValue	日付値
'	'		lngFormatNo YYYY/MMの場合 1
'	'					YYYY/MM/DD はその他
'	' 戻り値：フォーマットされた日付文字列
'	' 備考：
'	' ------------------------------------------
'	Public Function fncComDate( dtmValue, lngFormatNo )
'
'		dtmDate  = FormatDateTime(dtmValue, vbGeneralDate)
'		strYear  = Year(dtmDate)
'		strMonth = Month(dtmDate)
'		strDay   = Day(dtmDate)
'
'		' 月と日のみ、一桁チェック。一桁の場合は先頭に 0 を付加する。
'		If Len(strMonth) = 1 Then
'			strMonth = "0" & strMonth
'		End If
'		
'		If Len(strDay) = 1 Then
'			strDay = "0" & strDay
'		End If
'
'		' フォーマットフラグが１の場合 YYYY/MM フォーマット
'		If lngFormatNo = 1 Then
'			strDate = strYear & "/" & strMonth
'		Else
'			strDate = strYear & "/" & strMonth & "/" & strDay
'		End If
'
'		fncComDate = strDate
'
'	End Function
'
'	' ------------------------------------------
'	' 概要：文字列の検査
'	' 引数：TextBox or Textareaオブジェクト
'	' 戻り値：True/False 成功、失敗
'	' 備考：
'	' ------------------------------------------
'	Public Function fncComInspectionString( ByRef objText )
'	
'		strString = objText.value 'StrConv(objText.value, 1)
'		
'		MsgBox(LenB(strString) & ":" & strString)
'		fncComInspectionString = True
'		
'	End Function
'
'	' ------------------------------------------
'	' 概要：オブジェクトのフォーマット
'	' 引数：
'	'       objObject	- TextBoxオブジェクト
'	'       lngCheckID	- チェックID
'	'                      1:数値処理
'	'                      2:日付処理
'	'
'	' 戻り値：True/False 成功、失敗
'	' 備考：
'	'       この関数は、親WindowのJavaScript関数 fncErrorMessage() を呼び出します。
'	'       同時に実装する必要があります。
'	' ------------------------------------------
'	Public Function fncCheckObjectValue( objObject, lngCheckID )
'		On Error Resume Next
'		
'		'関数の戻り値を初期化
'		fncCheckObjectValue = True
'		
'		Select Case lngCheckID
'			' 数値処理
'			Case 1
'				If( fncComFormatNumberValue( objObject.value, False ) ) Then
'					' フォーマット成功の場合、オブジェクトのValueへ直接指定
'					objObject.value = FormatNumberValue
'					Exit Function
'				End If
'			' 日付処理
'			Case 2
'				If( fncComFormatDateTime( objObject ) ) Then Exit Function
'			' 文字列処理
'			case 3
'				If( fncComInspectionString( objObject ) ) Then Exit Function
'		End Select
'		
'		'失敗を返却
'		fncCheckObjectValue = False
'
'	End Function
'
'End Class
*/