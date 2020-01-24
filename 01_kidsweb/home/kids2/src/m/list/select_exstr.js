<!--



///// HEADER IMAGE /////
var headerAJ = '<img src="' + h_listJ + '" width="949" height="30" border="0" alt="マスタ一覧">';
var headerAE = '<img src="' + h_listE + '" width="949" height="30" border="0" alt="MASTER LIST">';


function ChgEtoJ( lngSelfCode )
{

	// 英語
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

	// 日本語
	else if ( lngSelfCode == 1 )
	{

		window.top.SegAHeader.innerHTML = headerAJ;

		SltMaster.innerText        = '選択';
		MasterName.innerText       = 'マスタ名称';
		Description.innerText      = '解説';

		m_StockClass.innerText       = '仕入区分マスタ管理';
		m_StockSubject.innerText     = '仕入科目マスタ管理';
		m_StockItem.innerText        = '仕入部品マスタ管理';
		m_AccessIPAddress.innerText  = 'アクセスIPアドレスマスタ管理';
		m_CertificateClass.innerText = '証紙種類マスタ管理';
		m_Country.innerText          = '国マスタ管理';
		m_Copyright.innerText        = '版権元マスタ管理';
		m_Organization.innerText     = '組織マスタ管理';
		m_ProductForm.innerText      = '商品形態マスタ管理';
		m_SalesClass.innerText       = '売上区分マスタ管理';
		m_TargetAge.innerText        = '対象年齢マスタ管理';
		m_DeliveryMethod.innerText   = '運搬方法マスタ管理';

	}

	return false;

}


//-->