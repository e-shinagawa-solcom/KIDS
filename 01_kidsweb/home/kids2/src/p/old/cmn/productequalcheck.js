
	// ---------------------------------------------------------------------------
	//	���ס�
	// 		subLoadMaster() �ƤӽФ������ץ����-���֥��������ѥ�åѡ�
	//	������
	//		objProEng			-	����̾�ΡʱѸ�˥��֥�������
	//		objGroup			-	���祪�֥�������
	//		objResult			-	��̳�Ǽ��hidden���֥�������
	//	����
	//		<Script for=... ��Ʊ�����ʥ����å�������ץ��⤫��ƤӽФ��ޤ���
	//		��ƤӽФ�����ץ��
	//		subLoadMasterProductCheck(document.all.strProductEnglishName, document.all.lngInChargeGroupCode, document.all.productequalcheck);
	// ---------------------------------------------------------------------------
	function subLoadMasterProductCheck(objProEng, objGroup, objResult)
	{

		objWindow1.style.visibility = 'hidden';

		// ��̤�¸�ߤ��ʤ�����Ʊ��Υǡ�����¸�ߤ��ʤ�����
		if( parseInt(objResult.value) <= 0 )
		{
			return false;
		}
		
		// �ͤΤɤ��餫��¸�ߤ��ʤ����
		if( objProEng.value == '' || objGroup.value == '' )
		{
			return false;
		}
		
		strMessage = '\t����̾�ΡʱѸ��- [' + objProEng.value + '] \n\t���� - ['+ objGroup.value +'] \n\n�Ǵ���Ʊ����Ͽ������ޤ���\n����̾�ΡʱѸ�ˡ����硢�ξ��ǥǡ�������Ͽ�ѤߤǤ���١�Ʊ��ǡ����κ���Ͽ�ϹԤ��ޤ���';
		//alert(strMessage);


		objWindow1.style.visibility = 'visible';
		objWindow2.ErrMeg.innerText = '���� [ '+ objGroup.value +' ] �ˡ���������̾�ΡʱѸ�˥ǡ��������Ǥ���Ͽ�ѤߤǤ���Ʊ��ǡ����κ���Ͽ�ϹԤ��ޤ���';


		objProEng.select();
		
		return true;
	}
