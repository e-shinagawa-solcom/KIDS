
///// HEADER IMAGE /////
var headerAJ = '<img src="' + search01J + '" width="949" height="30" border="0" alt="�ޥ�������">';
var headerAE = '<img src="' + search01E + '" width="949" height="30" border="0" alt="MASTER SEARCH">';


function ChgEtoJ( lngSelfCode )
{

	// �Ѹ�
	if ( lngSelfCode == 0 )
	{

		window.top.SegAHeader.innerHTML = headerAE;

		SltMaster.innerText       = 'Select';
		MasterName.innerText      = 'Master name';
		Description.innerText     = 'Description';

		m_Company.innerText       = 'Company master';
		m_Group.innerText         = 'Group master';
		m_MonetaryRate.innerText  = 'Monetary rate master';
		m_TemporaryRate.innerText  = 'Temporary rate master';

	}

	// ���ܸ�
	else if ( lngSelfCode == 1 )
	{

		window.top.SegAHeader.innerHTML = headerAJ;

		SltMaster.innerText       = '����';
		MasterName.innerText      = '�ޥ���̾��';
		Description.innerText     = '����';

		m_Company.innerText       = '��ҥޥ�������';
		m_Group.innerText         = '���롼�ץޥ�������';
		m_MonetaryRate.innerText  = '�̲ߥ졼�ȥޥ�������';
		m_TemporaryRate.innerText  = '����졼�ȥޥ�������';

	}

	return false;

}


//-->