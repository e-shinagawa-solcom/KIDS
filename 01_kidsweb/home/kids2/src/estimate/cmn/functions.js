<!--


///// Show-Hide Company&Group Window /////
var Clickcnt = 0;

function ShowCompanyGroup( objID )
{
	if( Clickcnt == 0 )
	{
		objID.style.visibility = 'visible';
		PunitBt.innerHTML = punitbt3;
		Clickcnt = 1;
	}
	else if( Clickcnt == 1 )
	{
		objID.style.visibility = 'hidden';
		PunitBt.innerHTML = punitbt1;
		Clickcnt = 0;
	}
	return false;
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
// fncSelectList(select, add) リストの選択および削除
//
//   select - 選択肢<SELECT>オブジェクト
//   addd   - 選択<SELECT>オブジェクト
// -----------------------------------------------------------------------
function fncSelectList(select, add)
{
	// 選択肢側に選択肢があるかどうかの確認
	if ( select.selectedIndex > -1 )
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
				add.options[valueLength].text = select.options[i].text;
				add.options[valueLength].value = select.options[i].value;
			}
		}

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
	}
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
	aryGroupCode = strGroupCode.split(':');

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



// -----------------------------------------------------------------------
//
// SetCompany( objCode, objName, objValue )
// サブウィンドウで選択された企業コード、グループコードをフォームに反映させる
//
//   objCode  - コード FORM.SELECT オブジェクト
//   objName  - 名称   FORM.SELECT オブジェクト
//   objValue - 選択   FORM.SELECT オブジェクト
// -----------------------------------------------------------------------
function SetCompany( objCode, objName, objValue )
{
	if ( objValue.length > 0 )
	{
		lngSelectedNumber = objValue.selectedIndex;
		objCode.value = objValue.options[lngSelectedNumber].value;
		objName.value = objValue.options[lngSelectedNumber].text;
	}
}



// -----------------------------------------------------------------------
//
// SetGroup( objCode, objName, objValue )
// サブウィンドウで選択された企業コード、グループコードをフォームに反映させる
//
//   objCode  - コード FORM.SELECT オブジェクト
//   objName  - 名称   FORM.SELECT オブジェクト
//   objValue - 選択   FORM.SELECT オブジェクト
// -----------------------------------------------------------------------
function SetGroup( objCode, objName, objValue )
{
	if ( objValue.length )
	{
		objName.value = objValue.options[0].text;
	}
	else
	{
		objName.value = "";
	}

	code = "";
	for ( i = 0; i < objValue.length; i++ )
	{
		code = code + ':' + objValue.options[i].value;
	}
	objCode.value = code;
}







// -----------------------------------------------------------------------
//
// ListMove( select, action )
// SELECT BOX 内の OPTION を入れ替える
//
//   select - 選択 FORM.SELECT オブジェクト
//   action - 行動(UP or DOWN)
// -----------------------------------------------------------------------
function ListMove( select, action )
{
	var nums = select.selectedIndex;
	target = -1;

	if ( action == 'UP' && nums > 0 )
	{
		target = nums -1;
	}
	else if ( action == 'DOWN' && nums < select.length - 1 )
	{
		target = nums +1;
	}

	if( nums > -1 && target > -1 )
	{
		selectedValue = select.options[nums].value;
		selectedText  = select.options[nums].text;
		select.options[nums].value = select.options[target].value;
		select.options[nums].text  = select.options[target].text;
		select.options[target].value = selectedValue;
		select.options[target].text  = selectedText;
		select.options[nums].selected   = false;
		select.options[target].selected = true;
	}
	return false;
}


function fncChangeProperty( lngAttribute, form )
{
	if ( lngAttribute > 0 )
	{
		form.bytInvalidFlag.checked = false;
		//form.bytInvalidFlag.contenteditable = false;
		form.bytInvalidFlag.disabled = true;

		form.bytMailTransmitFlag.checked = false;
		//form.bytMailTransmitFlag.contenteditable = false;
		form.bytMailTransmitFlag.disabled = true;

		form.bytUserDisplayFlag.checked = true;
		//form.bytUserDisplayFlag.contenteditable = true;
		form.bytUserDisplayFlag.disabled = true;

		form.lngAuthorityGroupCode.options[5].selected = true;
		//form.lngAuthorityGroupCode..contenteditable = true;
		form.lngAuthorityGroupCode.disabled = true;
	}
	else
	{
		form.bytMailTransmitFlag.disabled = false;

		// ユーザー設定以外の場合、権限グループの変更を許可
		if ( form.lngFunctionCode.value != 1101 )
		{
			form.bytInvalidFlag.checked = true;
			form.bytInvalidFlag.disabled = false;
			form.lngAuthorityGroupCode.disabled = false;
			form.bytUserDisplayFlag.disabled = false;
		}
	}
}



// -----------------------------------------------------------------------
//
// setDefaultGroup(fromCompany, fromGroup, toCompany, toGroup)
// もともとの企業が選択された場合、
// もともとのグループを選択状態およびリストに反映
//
//   objFromCompany - データ元 企業     CODE FORM TEXT オブジェクト
//   objFromGroup   - データ元 グループ CODE FORM TEXT オブジェクト
//   objToCompany   - 反映先   企業     CODE FORM TEXT オブジェクト
//   objToGroup     - 反映先   グループ CODE FORM TEXT オブジェクト
//   objTargetGroup - objToGroupの選択を反映する グループ CODE FORM TEXT オブジェクト
// -----------------------------------------------------------------------
function setDefaultGroup(objFromCompany, objFromGroup, objToCompany, objToGroup, objTargetGroup){
	// 選択企業とデフォルト企業が同じ場合、デフォルトグループをセット
	index = objToCompany.selectedIndex;
	if ( objFromCompany.value == objToCompany.options[index].value )
	{
		fncSelectListSet(objFromCompany, objFromGroup, objToCompany, objToGroup, objTargetGroup);
		SetGroup( parent.document.forms[0].lngGroupCode, parent.document.forms[0].strGroupName,objTargetGroup );
	}
}



function isChecked( checkbox, strComment )
{
	if ( checkbox.checked == false )
	{
		alert ( strComment );
		//retval = window.showModalDialog( 'http://www.wiseknot.co.jp/' , 1 , "center:yes;status:no;edge:raised;help:no;" );
	}
}

//-->