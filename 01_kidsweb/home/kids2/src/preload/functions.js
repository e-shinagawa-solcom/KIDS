<!--


	//-----------------------------------------------------
	// 概要    : オブジェクトのセンタリング処理関数
	//
	// ﾊﾟﾗﾒｰﾀｰ : [objId]   . オブジェクトID
	//           [lngTop]  . TOP座標微調整値
	//           [lngLeft] . LEFT座標微調整値
	//
	// 解説    : ウィンドウ枠内表示可能領域サイズから1/2
	//           した値を、オブジェクトのX,Y座標値に代入。
	//
	// ｲﾍﾞﾝﾄ   : body . [onload],[onresize]
	//-----------------------------------------------------
	function fncObjectCentering( objId , lngTop , lngLeft )
	{

		// ウィンドウ枠内表示可能領域の取得
		var winH = document.body.offsetHeight;
		var winW = document.body.offsetWidth;

		// センタリング - 微調整値
		objId.style.top  = (winH / 2) - lngTop;
		objId.style.left = (winW / 2) - lngLeft;

		return false;
	}


//-->