
	// ---------------------------------------------------------------------------
	//	概要：
	// 		既存の受注Ｎｏ．と同一のものが存在しないか、チェックを行う
	//	引数：
	//		objReceiveCode		-	受注Ｎｏ．オブジェクト
	//		objReviseCode		-	リバイズコードオブジェクト
	//		objResult			-	結果格納用hiddenオブジェクト
	//	概要
	//		<Script for=... の同一製品チェックスクリプト内から呼び出します。
	//		＜呼び出しサンプル＞
	//			subLoadMasterReceiveCheck(document.all.strReceiveCode, document.all.strReviseCode, document.all.receivecodeequalcheck);
	//  修正履歴：
	//			2004/03/11
	//			新規登録において、既存の受注Noは登録出来ないと言う考えから、受注Noのみをチェック
	//			リバイズコードは無視するようにした。
	// ---------------------------------------------------------------------------
	function subLoadMasterReceiveCheck(objReceiveCode, objReviseCode, objResult)
	{

		objWindow1.style.visibility = 'hidden';

		// 結果が存在しない場合（同一のデータが存在しない場合）
		if( parseInt(objResult.value) <= 0 )
		{
			return false;
		}
		
		// 値が存在しない場合
		if( objReceiveCode.value == '' )
		{
			return false;
		}
		
		strMessage = '\t受注Ｎｏ．[' + objReceiveCode.value + '] は既に使用されています。';

		objWindow1.style.visibility = 'visible';
		objWindow2.ErrMeg.innerText = strMessage;
		
		objReceiveCode.select();
		
		return true;
	}
