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
// fncSelectList(select, add) �ꥹ�Ȥ����򤪤�Ӻ��
//
//   select - �����<SELECT>���֥�������
//   addd   - ����<SELECT>���֥�������
// -----------------------------------------------------------------------
function fncSelectList(select, add)
{
	// �����¦������褬���뤫�ɤ����γ�ǧ
	if ( select.selectedIndex > -1 )
	{
		// �����ο�
		selectLength = select.length;

		// �����ɲ�
		for ( i = 0; i < selectLength; i++ )
		{
			if ( select.options[i].selected == true )
			{
				// ����¦�Υꥹ�Ȥ��ɲ�
				add.length++;

				// ����¦�ο�
				valueLength = add.length - 1;
				add.options[valueLength].text = select.options[i].text;
				add.options[valueLength].value = select.options[i].value;
			}
		}

		// ���ܺ��
		j = 0;
		for ( i = 0; i < selectLength; i++ )
		{
			// ���ܤ򤺤餹����
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
// ��ȡ����롼�פ�������֤���ӥꥹ�Ȥ�ȿ��
//
//   objFromCompany - �ǡ����� ���     CODE FORM TEXT ���֥�������
//   objFromGroup   - �ǡ����� ���롼�� CODE FORM TEXT ���֥�������
//   objToCompany   - ȿ����   ���     CODE FORM TEXT ���֥�������
//   objToGroup     - ȿ����   ���롼�� CODE FORM TEXT ���֥�������
//   objTargetGroup - objToGroup�������ȿ�Ǥ��� ���롼�� CODE FORM TEXT ���֥�������
// -----------------------------------------------------------------------
function fncSelectListSet(objFromCompany, objFromGroup, objToCompany, objToGroup, objTargetGroup)
{
	// �������
	for ( i = 0; i < objToCompany.length; i++ )
	{
		if ( objToCompany.options[i].value == objFromCompany.value )
		{
			objToCompany.options[i].selected = true;
		}
	}

	// Ϣ�륰�롼�ץ�����ʸ�������
	strGroupCode = objFromGroup.value;

	// Ϣ��ʸ�����ʬ�䤷�����롼�ץ����ɤ����������
	aryGroupCode = strGroupCode.split(':');

	// ���ꥰ�롼�ץ����ɤ�������֤ˤ���
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
// ��ȡ����롼�פ�������֤���ӥꥹ�Ȥ�ȿ��
//
//   objFromGroup - �����å����ե����॰�롼�� ���֥�������
//   objToGroup   - ȿ����ե����॰�롼�� ���֥�������
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
// OPTION �κ��
//
//   objFormSelect - FORM.SELECT ���֥�������
// -----------------------------------------------------------------------
function fncSelectOptionDel(objFormSelect)
{
	objFormSelect.length = 0;
}



// -----------------------------------------------------------------------
//
// SetCompany( objCode, objName, objValue )
// ���֥�����ɥ������򤵤줿��ȥ����ɡ����롼�ץ����ɤ�ե������ȿ�Ǥ�����
//
//   objCode  - ������ FORM.SELECT ���֥�������
//   objName  - ̾��   FORM.SELECT ���֥�������
//   objValue - ����   FORM.SELECT ���֥�������
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
// ���֥�����ɥ������򤵤줿��ȥ����ɡ����롼�ץ����ɤ�ե������ȿ�Ǥ�����
//
//   objCode  - ������ FORM.SELECT ���֥�������
//   objName  - ̾��   FORM.SELECT ���֥�������
//   objValue - ����   FORM.SELECT ���֥�������
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
// SELECT BOX ��� OPTION �������ؤ���
//
//   select - ���� FORM.SELECT ���֥�������
//   action - ��ư(UP or DOWN)
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

		// �桼��������ʳ��ξ�硢���¥��롼�פ��ѹ������
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
// ��Ȥ�Ȥδ�Ȥ����򤵤줿��硢
// ��Ȥ�ȤΥ��롼�פ�������֤���ӥꥹ�Ȥ�ȿ��
//
//   objFromCompany - �ǡ����� ���     CODE FORM TEXT ���֥�������
//   objFromGroup   - �ǡ����� ���롼�� CODE FORM TEXT ���֥�������
//   objToCompany   - ȿ����   ���     CODE FORM TEXT ���֥�������
//   objToGroup     - ȿ����   ���롼�� CODE FORM TEXT ���֥�������
//   objTargetGroup - objToGroup�������ȿ�Ǥ��� ���롼�� CODE FORM TEXT ���֥�������
// -----------------------------------------------------------------------
function setDefaultGroup(objFromCompany, objFromGroup, objToCompany, objToGroup, objTargetGroup){
	// �����Ȥȥǥե���ȴ�Ȥ�Ʊ����硢�ǥե���ȥ��롼�פ򥻥å�
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