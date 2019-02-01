
/**
*
*	@charset	: utf-8
*/



	//@------------------------------------------------------------------------
	/**
	*	概要	: 結果ダイアログ表示用関数
	*
	*	解説	: 結果をダイアログを表示させるための関数。
	*             配列[args]にフォーム要素の値を代入後ダイアログを表示する。
	*
	*	対象	: 結果用テンプレート
	*
	*	@param	[obj1]		: [Object]	. フォームのオブジェクト名
	*	@param	[obj2]		: [String]	. ダイアログ上で呼び出す親HTMLファイルのURL
	*	@param	[strUrl]	: [String]	. ダイアログ上で呼び出す子HTMLファイルのURL(Iframe)
	*	@param	[strID]		: [String]	. ダイアログ上で呼び出す子HTMLファイルの Iframe用ID
	*	@param	[strScroll]	: [String]	. Iframeでスクロールの許可・不許可
	*
	*	@event	: onClick
	*
	*	@charset : euc-jp
	*/
	//-------------------------------------------------------------------------
	function fncOpenDialog( obj1 , obj2 , strUrl , strID , strScroll, strType )
	{
		var j			= 0;
		var k			= 0;
		var blnClose	= false;

		args	= new Array();
		args[0]	= new Array();
		args[1]	= new Array();
		args[2]	= new Array();
		args[3]	= new Array();
		args[4]	= new Array();

		args[0][0]	= strUrl;
		args[0][1]	= strID;
		args[0][2]	= strScroll;


		///// other name /////
		for ( i = 0; i < obj1.elements.length; i++ )
		{

			if( typeof(obj1.elements[i]) == 'undefined' )
			{
				continue;
			}

			if ( obj1.elements[i].type == 'checkbox' )
			{
				if ( obj1.elements[i].checked == true )
				{
					args[1][j] = obj1.elements[i].name;
					args[2][j] = obj1.elements[i].value;
					j++;
				}
				continue;
			}

			// 要素抽出
			if( obj1.elements[i].name != 'OrderStatusObject' )
			{
				args[3][k] = obj1.elements[i].name;
				args[4][k] = obj1.elements[i].value;
				k++;
			}
		}


//alert(args[3].join('\n'));

		//alert(args[2][1]);return;

		switch( strType )
		{
			case "select":
				retVal = window.showModalDialog( obj2 , args , "dialogHeight:700px;dialogWidth:1011px;center:yes;status:no;edge:raised;help:no;scroll:no;" );
				break;

			case "confirm":
				retVal = window.showModalDialog( obj2 , args , "dialogHeight:700px;dialogWidth:1011px;center:yes;status:no;edge:raised;help:no;scroll:no;" );

//alert("confirm 終了\n"+retVal);
				// Return Value取得
				if( retVal )
				{
					var i;

					for( i=0; i<retVal.length; i++ )
					{
						if( retVal[i] == "&RENEW=true" )
						{
							blnClose	= true;
							break;
						}
					}

					// Excelシート確認ダイアログを閉じる
					if( blnClose ) window.close();
				}
				break;

			case "input":
				retVal = window.showModalDialog( obj2 , args , "dialogHeight:600px; dialogWidth:970px;center:yes;status:no;edge:raised;help:no;scroll:no;" );
//alert("input 終了\n"+retVal);
				break;

			default:
				break;
		}


		return true;
	}



	// シート選択処理
	function fncFileSelect( objFrm, lngSelectNo )
	{
		document.all.ActionScriptName.value	= '/upload2/parse/confirm.php';
		document.all.lngSelectSheetNo.value	= lngSelectNo;

		// 結果ダイアログ展開
		fncOpenDialog( objFrm, '/result/index.html', '/result/ifrm.html', 'ResultIframe', 'YES', 'select' );
		return true;
	}


	// ダイアログ表示処理
	function fncFileProcess( objFrm, strType )
	{

//alert(strType);
		switch( strType )
		{
			case "confirm":
				document.all.ActionScriptName.value	= '/estimate/regist/confirm.php';
				document.all.RENEW.value			= false;
				document.all.strPageCondition.value	= 'regist';
				document.all.strProcess.value		= 'confirm';
				document.all.strActionName.value	= 'regist';
				document.all.lngRegistConfirm.value	= '1';
				document.all.strMode.value			= '';

				// 結果ダイアログ展開
				fncOpenDialog( objFrm, '/result/index.html', '/result/ifrm.html', 'ResultIframe', 'YES', strType );
				break;

			case "input":
				document.all.ActionScriptName.value	= '/estimate/regist/edit.php';
				document.all.RENEW.value			= true;
				document.all.strPageCondition.value	= 'renew';
				document.all.strProcess.value		= 'regist';
				document.all.strActionName.value	= '';
				document.all.lngRegistConfirm.value	= '0';
				document.all.strMode.value			= 'onchange';

				// 結果ダイアログ展開
				fncOpenDialog( objFrm, '/result/index.html', '/result/ifrm.html', 'ResultIframeRenew', 'NO', strType );
				break;

			default:
				break;
		}


		return true;
	}
