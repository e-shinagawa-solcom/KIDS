
///// HEADER IMAGE /////
var headerAJ = '<img src="' + search01J + '" width="949" height="30" border="0" alt="マスタ検索">';
var headerAE = '<img src="' + search01E + '" width="949" height="30" border="0" alt="MASTER SEARCH">';


function ChgEtoJ( lngSelfCode )
{

	// 英語
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

	// 日本語
	else if ( lngSelfCode == 1 )
	{

		window.top.SegAHeader.innerHTML = headerAJ;

		SltMaster.innerText       = '選択';
		MasterName.innerText      = 'マスタ名称';
		Description.innerText     = '解説';

		m_Company.innerText       = '会社マスタ管理';
		m_Group.innerText         = 'グループマスタ管理';
		m_MonetaryRate.innerText  = '通貨レートマスタ管理';
		m_TemporaryRate.innerText  = '想定レートマスタ管理';

	}

	return false;

}


//-->