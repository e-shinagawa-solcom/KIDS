<!--



///// HEADER IMAGE /////
var headerAJ = '<img src="' + h_listJ + '" width="949" height="30" border="0" alt="�ޥ�������">';
var headerAE = '<img src="' + h_listE + '" width="949" height="30" border="0" alt="MASTER LIST">';


function ChgEtoJ( lngSelfCode )
{

	// �Ѹ�
	if ( lngSelfCode == 0 )
	{

		window.top.SegAHeader.innerHTML = headerAE;

		SltMaster.innerText        = 'Select';
		MasterName.innerText       = 'Master name';
		Description.innerText      = 'Description';

		m_StockClass.innerText       = 'Goods class master';
		m_StockSubject.innerText     = 'Goods set master';
		m_StockItem.innerText        = 'Goods parts master';
		m_AccessIPAddress.innerText  = 'Access IP address master';
		m_CertificateClass.innerText = 'Inspection master';
		m_Country.innerText          = 'Country master';
		m_Copyright.innerText        = 'Copyright master';
		m_Organization.innerText     = 'Organization master';
		m_ProductForm.innerText      = 'Goods form master';
		m_SalesClass.innerText       = 'Sales class master';
		m_TargetAge.innerText        = 'Target age master';
		m_DeliveryMethod.innerText   = 'Means of transport master';

	}

	// ���ܸ�
	else if ( lngSelfCode == 1 )
	{

		window.top.SegAHeader.innerHTML = headerAJ;

		SltMaster.innerText        = '����';
		MasterName.innerText       = '�ޥ���̾��';
		Description.innerText      = '����';

		m_StockClass.innerText       = '������ʬ�ޥ�������';
		m_StockSubject.innerText     = '�������ܥޥ�������';
		m_StockItem.innerText        = '�������ʥޥ�������';
		m_AccessIPAddress.innerText  = '��������IP���ɥ쥹�ޥ�������';
		m_CertificateClass.innerText = '�ڻ����ޥ�������';
		m_Country.innerText          = '��ޥ�������';
		m_Copyright.innerText        = '�Ǹ����ޥ�������';
		m_Organization.innerText     = '�ȿ��ޥ�������';
		m_ProductForm.innerText      = '���ʷ��֥ޥ�������';
		m_SalesClass.innerText       = '����ʬ�ޥ�������';
		m_TargetAge.innerText        = '�о�ǯ��ޥ�������';
		m_DeliveryMethod.innerText   = '������ˡ�ޥ�������';

	}

	return false;

}


//-->