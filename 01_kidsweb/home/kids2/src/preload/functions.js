<!--


	//-----------------------------------------------------
	// ����    : ���֥������ȤΥ��󥿥�󥰽����ؿ�
	//
	// �ʎߎ׎Ҏ����� : [objId]   . ���֥�������ID
	//           [lngTop]  . TOP��ɸ��Ĵ����
	//           [lngLeft] . LEFT��ɸ��Ĵ����
	//
	// ����    : ������ɥ�����ɽ����ǽ�ΰ襵��������1/2
	//           �����ͤ򡢥��֥������Ȥ�X,Y��ɸ�ͤ�������
	//
	// ���͎ގݎ�   : body . [onload],[onresize]
	//-----------------------------------------------------
	function fncObjectCentering( objId , lngTop , lngLeft )
	{

		// ������ɥ�����ɽ����ǽ�ΰ�μ���
		var winH = document.body.offsetHeight;
		var winW = document.body.offsetWidth;

		// ���󥿥�� - ��Ĵ����
		objId.style.top  = (winH / 2) - lngTop;
		objId.style.left = (winW / 2) - lngLeft;

		return false;
	}


//-->