
	// 
	//	ファイル概要：(JavaScriptファイル)
	//	
	// データバインドの機能を利用して、コードから名称を取得します
	// 

	/*
		《改定履歴》
		2004/03/09	K.Saito
			subLoadMasterHidden() 関数の追加
			subLoadMasterCheckAlert() 関数の追加
		
		2004/../..
	*/
	
	
	var g_objLoadMasterInForm;								// inputされたオブジェクト
	var g_objLoadMasterOutForm   = new Array();   			// output 対象のオブジェクト
	var g_aryLoadMasterErrorFlag = new Array(); 			// new Boolean(false);		// エラーフラグ
	
	var g_strLoadMasterLoading   = "Loading...";			// データセット接続時
	var g_strLoadMasterNoData    = "(No Data)";				// データセット0件時
	var g_blnLoadMasterDebugFlag = new Boolean(false);		// デバックフラグ
	
	
	
	// ---------------------------------------------------------------------------
	//	概要：
	//		subLoadMaster() 呼び出し、テキストボックス-オブジェクト用ラッパー
	//	引数：
	//		strProcessID		-	処理ID
	//		objInForm			-	入力オブジェクト
	//		objOutForm			-	検索結果埋め込みオブジェクト
	//		arySearchValue		-	検索元値（配列値）
	//		objDataSource		-	DataSourceオブジェクト
	//		lngObjNo			-	使用するオブジェクト番号
	//		blnDebugFlag		-	デバックフラグ
	//	備考：
	//							複数のデータバインドをonChange等のイベントで一度に使用する場合
	//							この番号を使用して分別する
	//							指定が無い場合は 0
	// ---------------------------------------------------------------------------
	function subLoadMasterText(strProcessID, objInForm, objOutForm, arySearchValue, objDataSource, lngObjNo, blnDebugFlag )
	{
		// InForm, OutForm の値をチェック
		if( typeof( objInForm ) == 'undefined' || 
			typeof( objOutForm) == 'undefined' )
		{
			alert("WARNING!! miss match subLoadMasterText() arg object undefined");
			return false;
		}

		// オブジェクトNo
		if( isNaN(lngObjNo) )
		{	// 指定が無い場合は 0
			lngObjNo = 0;
		}
		
		// デバックフラグ設定
		g_blnLoadMasterDebugFlag = blnDebugFlag;
		
		// 入力オブジェクト
		g_objLoadMasterInForm = objInForm;

		// 検索元値１、先頭に半角スペースがある場合、削除
		strInFormVal = objInForm.value.replace(/^[\x20]+/, '');

		// 検索元値１が無い場合
		if( strInFormVal == '' )
		{
			objOutForm.value = '';
			return false;
		}

		// 設定先のオブジェクト
		g_objLoadMasterOutForm[lngObjNo] = objOutForm;
		
		// コードから名称を取得
		subLoadMaster(strProcessID, arySearchValue, objDataSource );
		
		return true;
	}

	// ---------------------------------------------------------------------------
	//	概要：
	//		subLoadMaster() 呼び出し、Hidden-オブジェクト用ラッパー
	//	引数：
	//		strProcessID		-	処理ID
	//		objInForm			-	入力オブジェクト
	//		objOutForm			-	検索結果埋め込みオブジェクト
	//		arySearchValue		-	検索元値（配列値）
	//		objDataSource		-	DataSourceオブジェクト
	//		lngObjNo			-	使用するオブジェクト番号
	//		blnDebugFlag		-	デバックフラグ
	//	備考：
	//							複数のデータバインドをonChange等のイベントで一度に使用する場合
	//							この番号を使用して分別する
	//							指定が無い場合は 0
	// ---------------------------------------------------------------------------
	function subLoadMasterHidden(strProcessID, objInForm, objOutForm, arySearchValue, objDataSource, lngObjNo, blnDebugFlag )
	{
		// InForm, OutForm の値をチェック
		if(	typeof( objOutForm) == 'undefined' )
		{
			alert("WARNING!! miss match subLoadMasterText() arg object undefined");
			return false;
		}

		// オブジェクトNo
		if( isNaN(lngObjNo) )
		{	// 指定が無い場合は 0
			lngObjNo = 0;
		}
		
		// デバックフラグ設定
		g_blnLoadMasterDebugFlag = blnDebugFlag;
		
		// 設定先のオブジェクト
		g_objLoadMasterOutForm[lngObjNo] = objOutForm;
		
		// コードから名称を取得
		subLoadMaster(strProcessID, arySearchValue, objDataSource );
		
		return true;
	}
	
	
	// ---------------------------------------------------------------------------
	// 
	// 概要：subLoadMasterText() の objInForm の存在チェック無し版
	//
	// 備考：この関数は、Script内からの呼び出しに使用して下さい。
	//       UI からの呼び出しには適用しない事。
	// ---------------------------------------------------------------------------
	function subLoadMasterValue(strProcessID, objInForm, objOutForm, arySearchValue, objDataSource, lngObjNo, blnDebugFlag )
	{
		
		// オブジェクトNo
		if( isNaN(lngObjNo) )
		{	// 指定が無い場合は 0
			lngObjNo = 0;
		}

		// デバックフラグ設定
		g_blnLoadMasterDebugFlag = blnDebugFlag;

		// 設定先のオブジェクト
		g_objLoadMasterOutForm[lngObjNo] = objOutForm;

		// コードから名称を取得
		subLoadMaster(strProcessID, arySearchValue, objDataSource );

		return true;
	}
	

	// ---------------------------------------------------------------------------
	//	概要：
	// 		subLoadMaster() 呼び出し、オプション-オブジェクト用ラッパー
	//	引数：
	//		strProcessID		-	処理ID
	//		objInForm			-	検索元値１（コード）入力オブジェクト
	//		objOutOption		-	検索結果埋め込みオブジェクト
	//		arySearchValue		-	検索元値（配列値）
	//		objDataSource		-	DataSourceオブジェクト
	//		lngObjNo			-	使用するオブジェクト番号
	//		blnDebugFlag		-	デバックフラグ
	//	備考：
	//							複数のデータバインドをonChange等のイベントで一度に使用する場合
	//							この番号を使用して分別する
	//							指定が無い場合は 0
	// ---------------------------------------------------------------------------
	function subLoadMasterOption(strProcessID, objInForm, objOutOption, arySearchValue, objDataSource, lngObjNo, blnDebugFlag )
	{
		
		// SELECTオブジェクトでは無い場合
		if( ( objOutOption.type != 'select-one' ) && ( objOutOption.type != 'select-multiple' ) )
		{
			alert("WARNING!! miss match subLoadMasterOption() arg object");
			return false;
		}
		
		// オブジェクトNo
		if( isNaN(lngObjNo) )
		{	// 指定が無い場合は 0
			lngObjNo = 0;
		}

		// デバックフラグ設定
		g_blnLoadMasterDebugFlag = blnDebugFlag;
		
		// 入力オブジェクト
		g_objLoadMasterInForm = objInForm;

		// 設定先のオブジェクト
		g_objLoadMasterOutForm[lngObjNo] = objOutOption;

		// SELECTオブジェクトの初期化
		oOption = objOutOption;
		subLoadMasterOptionClear( oOption, true );
		oOption = document.createElement("OPTION");
		oOption.text = g_strLoadMasterLoading;
		oOption.value = "";
		objOutOption.add(oOption);
		
		// コードから名称を取得
		subLoadMaster(strProcessID, arySearchValue, objDataSource );
		
		return true;
	}


	// ---------------------------------------------------------------------------
	//	コード＋名称　取得関数
	//
	//	引数：
	//		strProcessID	-	処理ID (/lib/sql 下にある .sql を除いたファイル名
	//		arySearchValue	-	検索元値（配列値）
	//		objDataSource	-	DataSourceオブジェクト
	//
	// ---------------------------------------------------------------------------
	function subLoadMaster(strProcessID, arySearchValue, objDataSource )
	{
		
		// エラー無しを設定
		g_aryLoadMasterErrorFlag[0] = false;
		
		// 処理IDの指定を確認
		if( !strProcessID )
		{
			return false;
		}
		
		// 変数初期化
		strURL = "";	// URL情報格納
		strGet = "";	// Get情報格納
		
		// --------------------------------------------
		// データソースの取得先URLを設定
		// 
		// データソースとなるURLを設定
		strURL = "/cmn/getmasterdata.php";
		strURL = strURL + "?lngProcessID=" + strProcessID;
		
		// 検索の配列を指定数分結合
		for( i = 0; i < arySearchValue.length; i++ )
		{
			strGet = strGet + "&strFormValue[" + String(i) + "]=" + arySearchValue[i];
		}
		
		// 検索プログラムとGET部を結合
		strURL = strURL + strGet;
		
		// デバック
		subLoadMasterDebug(location.protocol + '//' + location.hostname + strURL);
		// --------------------------------------------
alert(strURL);
		
		// データソースを取得
		objDataSource.charset = document.charset;
		objDataSource.UseHeader = "True";
		objDataSource.FieldDelim = "\t";
		objDataSource.dataurl  = strURL;
		objDataSource.reset();

		return true;
	}

	// ---------------------------------------------------------------------------
	//	デバック用の関数
	//	引数：
	//		strValue	-	URL
	// ---------------------------------------------------------------------------
	function subLoadMasterDebug(strValue)
	{
		if( g_blnLoadMasterDebugFlag != true)
		{
			return true;
		}
		
		if( confirm('preview URL?') )
		{
			window.prompt('subLoadMasterDebug()', strValue);
		}
		if( confirm('preview datasource?') )
		{
			//ret = window.open(strValue, "debug_datasource", "location=yes,resizable=yes,width=600,height=300,scrollbars=yes,toolbar=yes");
			location.href = 'view-source:'+ strValue;
		}
		// window.prompt('subLoadMasterDebug()', strValue);
		// ret = showModelessDialog(strURL);
		return true;
	}

	// ---------------------------------------------------------------------------
	//	レコードセットから指定オブジェクト値への設定
	//
	//	引数：
	// 		objRst		-	レコードセットオブジェクト
	//		lngObjNo	-	オブジェクトNo
	//
	// ---------------------------------------------------------------------------
	function subLoadMasterSetting(objRst, lngObjNo)
	{
		// オブジェクトNo
		if( isNaN(lngObjNo) )
		{	// 指定が無い場合は 0
			lngObjNo = 0;
		}

		// エラーフラグを設定
		g_aryLoadMasterErrorFlag[lngObjNo] = true;

		// ----------------------------------------------------
		// レコードカウントが0（データが取得できない）の場合
		// ----------------------------------------------------
		if( objRst.RecordCount == 0 )
		{
			
			if( g_objLoadMasterOutForm[lngObjNo].type == 'text' )
			{
				// 入力値を選択状態にし、設定先の値をクリアする
				if( g_objLoadMasterInForm.style.visibility != 'hidden' ) g_objLoadMasterInForm.select();
				g_objLoadMasterOutForm[lngObjNo].value = "";
			}
			else if( ( g_objLoadMasterOutForm[lngObjNo].type == 'select-one' ) || ( g_objLoadMasterOutForm[lngObjNo].type == 'select-multiple' ) )
			{
				subLoadMasterOptionClear(g_objLoadMasterOutForm[lngObjNo], false);
				oOption = document.createElement("OPTION");
				oOption.text = g_strLoadMasterNoData;
				oOption.value = "";
				g_objLoadMasterOutForm[lngObjNo].add(oOption);
			}
			else if( g_objLoadMasterOutForm[lngObjNo].type == 'hidden' )
			{

				strOutFormName = g_objLoadMasterOutForm[lngObjNo].name;
				switch( strOutFormName )
				{
					// 製品重複チェックの場合
					case 'productequalcheck':
					// 受注No.重複チェックの場合
					case 'receivecodeequalcheck':
					
						// 値を初期化
						g_objLoadMasterOutForm[lngObjNo].value = 0;
						break;
					
					default:
						// チェックアラート用である場合
						if( strOutFormName.match(/^check_alert/) )
						{
							break;
						}
						
						alert('WARNING!! program switch Not found subLoadMasterSetting');
						break;
				}
				
			}
			return true;
		}


		// ----------------------------------------------------------------
		// TEXTオブジェクトの場合
		// ----------------------------------------------------------------
		if( g_objLoadMasterOutForm[lngObjNo].type == 'text' )
		{
			// name部分を取得して設定
			g_objLoadMasterOutForm[lngObjNo].value = objRst.Fields('name1');
		}
		
		// ----------------------------------------------------------------
		// SELECTオブジェクトの場合
		// ----------------------------------------------------------------
		else if( ( g_objLoadMasterOutForm[lngObjNo].type == 'select-one' ) || ( g_objLoadMasterOutForm[lngObjNo].type == 'select-multiple' ) )
		{
			// 対象のSELECTオブジェクトを初期化
			subLoadMasterOptionClear( g_objLoadMasterOutForm[lngObjNo] );
			
			// レコードセットが存在する場合
			if (objRst.recordcount)
			{
				objRst.MoveFirst();
				while (!objRst.EOF)
				{
					oOption = document.createElement("OPTION");
					oOption.value = objRst.fields("id").value;
					oOption.text  = objRst.fields("name1").value;
					// 製品名名称（name2 カラム）が存在しない場合、テキスト色を変更する。（name2はSQL結果によって制御している）
					if( objRst.fields.count > 2 )
					{
						if( !objRst.fields("name2").value )
						{
							oOption.style.color = objRst.fields("name3").value;
						}
					}
					g_objLoadMasterOutForm[lngObjNo].add(oOption);
					objRst.MoveNext();
				}
				g_objLoadMasterOutForm[lngObjNo].disabled=false;
			}
		
		}
		
		// ----------------------------------------------------------------
		// HIDDEN オブジェクトの場合
		// ----------------------------------------------------------------
		else if( g_objLoadMasterOutForm[lngObjNo].type == 'hidden' )
		{
			
			strOutFormName = g_objLoadMasterOutForm[lngObjNo].name;
			switch( strOutFormName )
			{
				// 製品重複チェックの場合
				case 'productequalcheck':
				// 受注No.重複チェックの場合
				case 'receivecodeequalcheck':

					// 値を初期化
					g_objLoadMasterOutForm[lngObjNo].value = 0;
						
					// レコードセットが存在する場合
					if (objRst.recordcount)
					{
						// 件数（SQL検索結果）を設定
						objRst.MoveFirst();
						g_objLoadMasterOutForm[lngObjNo].value = parseInt(objRst.fields("id").value);
					}
					break;

				default:
					
					// チェックアラート用である場合 "check_alert..." にマッチ
					if( strOutFormName.match(/^check_alert/) )
					{
						// 値を初期化
						g_objLoadMasterOutForm[lngObjNo].value = "";
						
						// レコードセットが存在する場合
						if (objRst.recordcount)
						{
							// 件数（SQL検索結果）を設定
							objRst.MoveFirst();
							g_objLoadMasterOutForm[lngObjNo].value = objRst.fields("name1").value;
						}
						break;
					}

					alert('WARNING!! program switch Not found subLoadMasterSetting');
				break;

			}

		}
		
		// ----------------------------------------------------------------
		// 出力先が判定外のオブジェクトの場合
		// ----------------------------------------------------------------
		else
		{
			alert("WARNING!! miss match subLoadMasterSetting-type: " + g_objLoadMasterOutForm[lngObjNo].type);
			return true;

		}
		
		// エラー無しを設定
		g_aryLoadMasterErrorFlag[lngObjNo] = false;
		return true;
		
	}


	// ---------------------------------------------------------------------------
	//	オプションオブジェクトの内容をクリア
	//
	//	引数：
	// 		objOption		-	オプションオブジェクト
	//		blnDisabled		-	アクティブ化フラグ
	//
	// ---------------------------------------------------------------------------
	function subLoadMasterOptionClear(objOption, blnDisabled)
	{
		if( ( objOption.type == 'select-one' ) || ( objOption.type == 'select-multiple' ) )
		{
			while (objOption.options.length) objOption.options.remove(0);
			
			if( blnDisabled ) objOption.disabled = true;
			return true;
		}
		return false;
	}


	// ---------------------------------------------------------------------------
	//	レコードセットから指定オブジェクト値への設定
	//
	//	引数：
	// 		objRst		-	レコードセットオブジェクト
	//		strSearchID	-	検索対象のID（文字列）
	//
	// ---------------------------------------------------------------------------
	function subLoadMasterGetIdName(objRst, strSearchID)
	{
		aryMatch = false;
		
		// レコードセットが存在しない場合
		if (!objRst.recordcount)
		{
			return false;
		}

		// 先頭レコードへ移動
		objRst.MoveFirst();
		
		while (!objRst.EOF)
		{
			// 検索IDと同一のidをレコードセットから検索
			if( objRst.fields("id").value == strSearchID )
			{
				// 一致した場合、配列で返却
				aryMatch = new Array();
				aryMatch['id'] = objRst.fields("id").value;
				if( objRst.fields("name2").value )
				{
					aryMatch['name'] = objRst.fields("name2").value;
				}
				else
				{
					aryMatch['name'] = '';
				}
				//alert("Match" + strSearchID);
				break;
			}
			objRst.MoveNext();
		}
		
		return aryMatch;
	}


	// ---------------------------------------------------------------------------
	//	オブジェクト設定値のエラーチェック
	//
	//	引数：
	// 		objForm		-	検索元値１（コード）を入力したオブジェクト
	//
	//  名称取得時にエラーが発生（件数0件）した場合、入力値（コード）を初期化する
	//  [0]のエラーに対してのみ、エラー判定を行う。これは、続けて Setting()を呼ばれた際に
	//  エラーが初期化されてしまう事を回避するためです。
	// ---------------------------------------------------------------------------
	function subLoadMasterCheck(objForm)
	{
		// エラーが発生していた場合
		if( g_aryLoadMasterErrorFlag[0] == true )
		{
			// 同じ入力オブジェクトの場合
			if( g_objLoadMasterInForm.name == objForm.name )
			{
				// 入力値の初期化
				objForm.value = "";
				g_objLoadMasterInForm.value="";
				// エラーフラグの初期化
				g_aryLoadMasterErrorFlag[0] = false;
				return true;
			}
		}
	}

	// ---------------------------------------------------------------------------
	//	概要：警告メッセージを表示させる。
	//
	//	引数：
	// 		objAlert	-	alertメッセージが保持されている(hidden)オブジェクト
	//
	//  subLoadMasterText にて check_alert オブジェクトにメッセージを代入
	//  メッセージは全て /lib/sql/*.sql に格納
	// ---------------------------------------------------------------------------
	function subLoadMasterCheckAlert(objAlert)
	{
		if( typeof(objAlert) == 'undefined' )
		{
			return;
		}
		if( objAlert.value == '' )
		{
			return;
		}
		
		alert(objAlert.value);
	}
