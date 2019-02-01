
	// ---------------------------------------------------------------------------
	//	概要：
	// 		subLoadMaster() 呼び出し、オプション-オブジェクト用ラッパー
	//	引数：
	//		objProEng			-	製品名称（英語）オブジェクト
	//		objGroup			-	部門オブジェクト
	//		objResult			-	結果格納用hiddenオブジェクト
	//	概要
	//		<Script for=... の同一製品チェックスクリプト内から呼び出します。
	//		＜呼び出しサンプル＞
	//		subLoadMasterProductCheck(document.all.strProductEnglishName, document.all.lngInChargeGroupCode, document.all.productequalcheck);
	// ---------------------------------------------------------------------------
	function subLoadMasterProductCheck(objProEng, objGroup, objResult)
	{

		objWindow1.style.visibility = 'hidden';

		// 結果が存在しない場合（同一のデータが存在しない場合）
		if( parseInt(objResult.value) <= 0 )
		{
			return false;
		}
		
		// 値のどちらかが存在しない場合
		if( objProEng.value == '' || objGroup.value == '' )
		{
			return false;
		}
		
		strMessage = '\t製品名称（英語）- [' + objProEng.value + '] \n\t部門 - ['+ objGroup.value +'] \n\nで既に同じ登録があります。\n製品名称（英語）＆部門、の条件でデータが登録済みである為、同一データの再登録は行えません。';
		//alert(strMessage);


		objWindow1.style.visibility = 'visible';
		objWindow2.ErrMeg.innerText = '部門 [ '+ objGroup.value +' ] に、この製品名称（英語）データがすでに登録済みです。同一データの再登録は行えません。';


		objProEng.select();
		
		return true;
	}
