
	// ---------------------------------------------------------------------------
	//	���ס�
	// 		��¸�μ���Σ��Ʊ��Τ�Τ�¸�ߤ��ʤ����������å���Ԥ�
	//	������
	//		objReceiveCode		-	����Σ���֥�������
	//		objReviseCode		-	��Х��������ɥ��֥�������
	//		objResult			-	��̳�Ǽ��hidden���֥�������
	//	����
	//		<Script for=... ��Ʊ�����ʥ����å�������ץ��⤫��ƤӽФ��ޤ���
	//		��ƤӽФ�����ץ��
	//			subLoadMasterReceiveCheck(document.all.strReceiveCode, document.all.strReviseCode, document.all.receivecodeequalcheck);
	//  ��������
	//			2004/03/11
	//			������Ͽ�ˤ����ơ���¸�μ���No����Ͽ����ʤ��ȸ����ͤ����顢����No�Τߤ�����å�
	//			��Х��������ɤ�̵�뤹��褦�ˤ�����
	// ---------------------------------------------------------------------------
	function subLoadMasterReceiveCheck(objReceiveCode, objReviseCode, objResult)
	{

		objWindow1.style.visibility = 'hidden';

		// ��̤�¸�ߤ��ʤ�����Ʊ��Υǡ�����¸�ߤ��ʤ�����
		if( parseInt(objResult.value) <= 0 )
		{
			return false;
		}
		
		// �ͤ�¸�ߤ��ʤ����
		if( objReceiveCode.value == '' )
		{
			return false;
		}
		
		strMessage = '\t����Σ[' + objReceiveCode.value + '] �ϴ��˻��Ѥ���Ƥ��ޤ���';

		objWindow1.style.visibility = 'visible';
		objWindow2.ErrMeg.innerText = strMessage;
		
		objReceiveCode.select();
		
		return true;
	}
