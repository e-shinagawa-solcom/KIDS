
	// ---------------------------------------------------------------------------
	//	概要：
	// 		レコードセットから指定オブジェクト値への設定
	//	引数：
	// 		objRst		-	レコードセットオブジェクト
	//		lngObjNo	-	オブジェクトNo
	//		objTarget	-	設定先のTOP階層オブジェクト「window.DSO（メインウィンドウから） / window.parent.DSO（明細行から）」
	//	備考：
	//		<Script for=... の
	//		＜呼び出しサンプル＞
	//			subLoadMasterSettings(this.recordset,1, window.DSO);
	//			subLoadMasterSettings(this.recordset,1, window.parent.DSO);
	//	注意：
	//		masterlib.js を同時に使用する事を前提とした関数である
	//  修正履歴：
	//			2005/10/13
	// ---------------------------------------------------------------------------
	function subLoadMasterSettings(objRst, lngObjNo, objTarget)
	{

		strName1 = "";	// 顧客品番
		strName2 = "";	// 製品名称
		strName3 = "";	// カートン入数
		strName5 = "";	// 表示部門コード
		strName6 = "";	// 部門名称
		strName8 = "";	// 表示ユーザーコード
		strName9 = "";	// ユーザー名称

		// レコードセットの数を確認
		if( objRst.recordcount)
		{
			strName1 = objRst.Fields('name1').value;
			strName2 = objRst.Fields('name2').value;
			strName3 = objRst.Fields('name3').value;
			strName5 = objRst.Fields('name5').value;
			strName6 = objRst.Fields('name6').value;
			strName8 = objRst.Fields('name8').value;
			strName9 = objRst.Fields('name9').value;
		}
		else
		{
			// エラーフラグを設定 - subLoadMasterCheck 用
			g_aryLoadMasterErrorFlag[0] = true;
			// 入力オブジェクトの値を選択
			if( g_objLoadMasterInForm.style.visibility != 'hidden' ) g_objLoadMasterInForm.select();
		}

		// name部分を取得して設定
		objTarget.strGoodsCode.value			= strName1;
		objTarget.strProductName.value			= strName2;
		objTarget.lngCartonQuantity.value		= strName3;
		objTarget.lngInChargeGroupCode.value	= strName5;
		objTarget.strInChargeGroupName.value	= strName6;
		objTarget.lngInChargeUserCode.value		= strName8;
		objTarget.strInChargeUserName.value		= strName9;

		return true;

	}
