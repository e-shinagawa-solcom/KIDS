
	// ---------------------------------------------------------------------------
	//	���ס�
	// 		�쥳���ɥ��åȤ�����ꥪ�֥��������ͤؤ�����
	//	������
	// 		objRst		-	�쥳���ɥ��åȥ��֥�������
	//		lngObjNo	-	���֥�������No
	//		objTarget	-	�������TOP���إ��֥������ȡ�window.DSO�ʥᥤ�󥦥���ɥ������ / window.parent.DSO�����ٹԤ���ˡ�
	//	���͡�
	//		<Script for=... ��
	//		��ƤӽФ�����ץ��
	//			subLoadMasterSettings(this.recordset,1, window.DSO);
	//			subLoadMasterSettings(this.recordset,1, window.parent.DSO);
	//	��ա�
	//		masterlib.js ��Ʊ���˻��Ѥ����������Ȥ����ؿ��Ǥ���
	//  ��������
	//			2005/10/13
	// ---------------------------------------------------------------------------
	function subLoadMasterSettings(objRst, lngObjNo, objTarget)
	{

		strName1 = "";	// �ܵ�����
		strName2 = "";	// ����̾��
		strName3 = "";	// �����ȥ�����
		strName5 = "";	// ɽ�����祳����
		strName6 = "";	// ����̾��
		strName8 = "";	// ɽ���桼����������
		strName9 = "";	// �桼����̾��

		// �쥳���ɥ��åȤο����ǧ
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
			// ���顼�ե饰������ - subLoadMasterCheck ��
			g_aryLoadMasterErrorFlag[0] = true;
			// ���ϥ��֥������Ȥ��ͤ�����
			if( g_objLoadMasterInForm.style.visibility != 'hidden' ) g_objLoadMasterInForm.select();
		}

		// name��ʬ�������������
		objTarget.strGoodsCode.value			= strName1;
		objTarget.strProductName.value			= strName2;
		objTarget.lngCartonQuantity.value		= strName3;
		objTarget.lngInChargeGroupCode.value	= strName5;
		objTarget.strInChargeGroupName.value	= strName6;
		objTarget.lngInChargeUserCode.value		= strName8;
		objTarget.strInChargeUserName.value		= strName9;

		return true;

	}
