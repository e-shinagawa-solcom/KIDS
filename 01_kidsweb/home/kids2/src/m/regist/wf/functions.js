<!--


//*******************************************************************
//objOrderDataFrom     :����SELECT���֥�������
//objOrderDataTo       :������SELECT���֥�������
//objOrderData         :����桼���������ɤȴ���(lngUserCode:lngLimitDate&)
//lngLimitDate         :����
function fncAddGroupUser(objOrderDataFrom, objOrderDataTo, objOrderData, lngLimitDate )
{
	// �����¦������褬���뤫�ɤ����γ�ǧ
	if ( objOrderDataFrom.selectedIndex < 0 )
	{
		alert('���򤷤Ƥ�������');
		return '';
	}
	else if ( !isFinite( lngLimitDate ) || lngLimitDate < 1 )
	{
		alert('���¤����Ϥ��Ƥ�������');
		return '';
	}
	else if ( lngLimitDate > 999 )
	{
		alert('������������¤�Ķ���Ƥ��ޤ�');
		return '';
	}
	else
	{
		// �ꥹ�Ȱ�ư
		fncSelectList(objOrderDataFrom, objOrderDataTo, objOrderData, lngLimitDate);

		// ��Ͽ���줿���٤ƤΥ桼�����ǡ����ȴ��¤�ʸ���� '&' ��ʬ��
		aryData = objOrderData.value.split('&');

		// ������ʸ�������
		var strQuery = '';

		// ʸ���� '=' ��ʬ��
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
// fncSelectList(select, add) �ꥹ�Ȥ����򤪤�Ӻ��
//
//   select       - �����<SELECT>���֥�������
//   add          - ����<SELECT>���֥�������
//   hidden       - ������HIDDEN���֥�������
//   lngLimitDate - ��������
// -----------------------------------------------------------------------
function fncSelectList(select, add, hidden, lngLimitDate)
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
			add.options[valueLength].text = add.length + '.' + select.options[i].text + ':' + lngLimitDate + '����';
			add.options[valueLength].value = select.options[i].value;
			hidden.value += select.options[i].value + '=' + lngLimitDate + '&';
			//add.options[valueLength].selected = true;
		}
	}

	/*
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
	aryGroupCode = strGroupCode.split('=');

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
//-->