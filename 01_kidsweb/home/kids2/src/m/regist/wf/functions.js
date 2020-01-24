<!--


//*******************************************************************
//objOrderDataFrom     :選択元SELECTオブジェクト
//objOrderDataTo       :選択先SELECTオブジェクト
//objOrderData         :選択ユーザーコードと期限(lngUserCode:lngLimitDate&)
//lngLimitDate         :期限
function fncAddGroupUser(objOrderDataFrom, objOrderDataTo, objOrderData, lngLimitDate )
{
	// 選択肢側に選択肢があるかどうかの確認
	if ( objOrderDataFrom.selectedIndex < 0 )
	{
		alert('選択してください');
		return '';
	}
	else if ( !isFinite( lngLimitDate ) || lngLimitDate < 1 )
	{
		alert('期限を入力してください');
		return '';
	}
	else if ( lngLimitDate > 999 )
	{
		alert('期限日数が上限を超えています');
		return '';
	}
	else
	{
		// リスト移動
		fncSelectList(objOrderDataFrom, objOrderDataTo, objOrderData, lngLimitDate);

		// 登録されたすべてのユーザーデータと期限を文字列 '&' で分割
		aryData = objOrderData.value.split('&');

		// クエリ文字列宣言
		var strQuery = '';

		// 文字列 '=' で分割
		for ( a = 0; a < aryData.length - 1; a++ )
		{
			aryUserCode = aryData[a].split('=');
			strQuery += ' AND u.lngUserCode <> ' + aryUserCode[0];
		}
		return strQuery;
	}
}



// -----------------------------------------------------------------------
//
// fncSelectList(select, add) リストの選択および削除
//
//   select       - 選択肢<SELECT>オブジェクト
//   add          - 選択<SELECT>オブジェクト
//   hidden       - 送信用HIDDENオブジェクト
//   lngLimitDate - 期限日数
// -----------------------------------------------------------------------
function fncSelectList(select, add, hidden, lngLimitDate)
{
	// 選択肢の数
	selectLength = select.length;

	// 項目追加
	for ( i = 0; i < selectLength; i++ )
	{
		if ( select.options[i].selected == true )
		{
			// 選択側のリストを追加
			add.length++;

			// 選択側の数
			valueLength = add.length - 1;
			add.options[valueLength].text = add.length + '.' + select.options[i].text + ':' + lngLimitDate + '日間';
			add.options[valueLength].value = select.options[i].value;
			hidden.value += select.options[i].value + '=' + lngLimitDate + '&';
			//add.options[valueLength].selected = true;
		}
	}

	/*
	// 項目削除
	j = 0;
	for ( i = 0; i < selectLength; i++ )
	{
		// 項目をずらす処理
		if ( select.options[i].selected != true )
		{
			select.options[j].text = select.options[i].text;
			select.options[j].value = select.options[i].value;
			select.options[j].selected = false;
			j++;
		}
	}
	for ( k = j; k < i; k++ )
	{
		select.length--;
	}
	*/
}



//*******************************************************************
//objSelect1: document.all.slctGroup1
//objSelect2: document.all.slctGroup2
//objText: document.all.strGroup or document.all.strCompany
//objValue: this.value


function ListMatch( objSelect , strMatch )
{
	for( i = 0; i < objSelect.options.length; i++ )
	{
		if( objSelect.options[i].text == strMatch )
		{
			return i;
		}
	}
	return -1;
}




// -----------------------------------------------------------------------
//
// fncSelectListSet(fromCompany, fromGroup, toCompany, toGroup)
// 企業、グループを選択状態およびリストに反映
//
//   objFromCompany - データ元 企業     CODE FORM TEXT オブジェクト
//   objFromGroup   - データ元 グループ CODE FORM TEXT オブジェクト
//   objToCompany   - 反映先   企業     CODE FORM TEXT オブジェクト
//   objToGroup     - 反映先   グループ CODE FORM TEXT オブジェクト
//   objTargetGroup - objToGroupの選択を反映する グループ CODE FORM TEXT オブジェクト
// -----------------------------------------------------------------------
function fncSelectListSet(objFromCompany, objFromGroup, objToCompany, objToGroup, objTargetGroup)
{
	// 企業選択
	for ( i = 0; i < objToCompany.length; i++ )
	{
		if ( objToCompany.options[i].value == objFromCompany.value )
		{
			objToCompany.options[i].selected = true;
		}
	}

	// 連結グループコード文字列取得
	strGroupCode = objFromGroup.value;

	// 連結文字列を分割し、グループコードの配列を生成
	aryGroupCode = strGroupCode.split('=');

	// 指定グループコードを選択状態にする
	for ( x = 1; x < aryGroupCode.length; x++ )
	{
		y = 0;
		while ( y < objToGroup.length )
		{
			if ( objToGroup.options[y].value == aryGroupCode[x] )
			{
				objToGroup.options[y].selected = true;
				fncSelectList(objToGroup, objTargetGroup);
				break;
			}
			else
			{
				y++;
			}
		}
	}
}



// -----------------------------------------------------------------------
//
// fncSelectListDel(objFromGroup, objToGroup)
// 企業、グループを選択状態およびリストに反映
//
//   objFromGroup - チェック元フォームグループ オブジェクト
//   objToGroup   - 反映先フォームグループ オブジェクト
// -----------------------------------------------------------------------
function fncSelectListDel(objFromGroup, objToGroup)
{
	if ( objFromGroup.length < 1 )
	{
		objToGroup.value = "";
	}
}



// -----------------------------------------------------------------------
//
// fncSelectOptionDel(objFromGroup, objToGroup)
// OPTION の削除
//
//   objFormSelect - FORM.SELECT オブジェクト
// -----------------------------------------------------------------------
function fncSelectOptionDel(objFormSelect)
{
	objFormSelect.length = 0;
}
//-->