<!--


	///// MAIN TITLE IMAGE /////
	var titleJ = '/img/type01/m/title_ja.gif';
	var titleE = '/img/type01/m/title_en.gif';

	///// ADDITION BUTTON /////
	var maddJ1 = '/img/type01/m/m_add_off_ja_bt.gif';
	var maddJ2 = '/img/type01/m/m_add_off_on_ja_bt.gif';
	var maddJ3 = '/img/type01/m/m_add_on_ja_bt.gif';

	var maddE1 = '/img/type01/m/m_add_off_en_bt.gif';
	var maddE2 = '/img/type01/m/m_add_off_on_en_bt.gif';
	var maddE3 = '/img/type01/m/m_add_on_en_bt.gif';


	///// WHITE ADD BUTTON /////
	var whiteadd1 = '/img/type01/m/add_off_bt.gif';
	var whiteadd2 = '/img/type01/m/add_off_on_bt.gif';
	var whiteadd3 = '/img/type01/m/add_on_bt.gif';



	////////// ヘッダーイメージ //////////
	// マスタ一覧
	var h_listJ = '/img/type01/m/m_header_title_ja.gif';
	var h_listE = '/img/type01/m/m_header_title_en.gif';

	// アクセスＩＰアドレスマスタ
	var h_ipJ = '/img/type01/m/m_header_ip_ja.gif';
	var h_ipE = '/img/type01/m/m_header_ip_en.gif';

	// 証紙種類マスタ
	var h_inspectionJ = '/img/type01/m/m_header_inspection_ja.gif';
	var h_inspectionE = '/img/type01/m/m_header_inspection_en.gif';

	// 版権元マスタ
	var h_copyJ = '/img/type01/m/m_header_copy_ja.gif';
	var h_copyE = '/img/type01/m/m_header_copy_en.gif';

	// 国マスタ
	var h_countryJ = '/img/type01/m/m_header_country_ja.gif';
	var h_countryE = '/img/type01/m/m_header_country_en.gif';

	// 運搬方法マスタ
	var h_transportJ = '/img/type01/m/m_header_transport_ja.gif';
	var h_transportE = '/img/type01/m/m_header_transport_en.gif';

	// 組織マスタ
	var h_organizationJ = '/img/type01/m/m_header_organization_ja.gif';
	var h_organizationE = '/img/type01/m/m_header_organization_en.gif';

	// 商品形態マスタ
	var h_goodsformJ = '/img/type01/m/m_header_goodsform_ja.gif';
	var h_goodsformE = '/img/type01/m/m_header_goodsform_en.gif';

	// 売上区分マスタ
	var h_salesclassJ = '/img/type01/m/m_header_salesclass_ja.gif';
	var h_salesclassE = '/img/type01/m/m_header_salesclass_en.gif';

	// 仕入区分マスタ
	var h_goodsclassJ = '/img/type01/m/m_header_goodsclass_ja.gif';
	var h_goodsclassE = '/img/type01/m/m_header_goodsclass_en.gif';

	// 仕入部品マスタ
	var h_goodspartsJ = '/img/type01/m/m_header_goodsparts_ja.gif';
	var h_goodspartsE = '/img/type01/m/m_header_goodsparts_en.gif';

	// 仕入科目マスタ
	var h_goodssetJ = '/img/type01/m/m_header_goodsset_ja.gif';
	var h_goodssetE = '/img/type01/m/m_header_goodsset_en.gif';

	// 対象年齢マスタ
	var h_ageJ = '/img/type01/m/m_header_age_ja.gif';
	var h_ageE = '/img/type01/m/m_header_age_en.gif';

	// 会社マスタ
	var h_companyJ = '/img/type01/m/m_header_company_ja.gif';
	var h_companyE = '/img/type01/m/m_header_company_en.gif';

	// グループマスタ
	var h_groupJ = '/img/type01/m/m_header_group_ja.gif';
	var h_groupE = '/img/type01/m/m_header_group_en.gif';

	// 通貨レートマスタ
	var h_rateJ = '/img/type01/m/m_header_rate_ja.gif';
	var h_rateE = '/img/type01/m/m_header_rate_en.gif';

	// ワークフロー順序マスタ
	var h_wfJ = '/img/type01/m/m_header_wf_ja.gif';
	var h_wfE = '/img/type01/m/m_header_wf_en.gif';

	// 通貨レートマスタ
	var h_trateJ = '/img/type01/m/m_header_trate_ja.gif';



	// マスタ検索
	var search01J = '/img/type01/m/ms_header_title_ja.gif';
	var search01E = '/img/type01/m/ms_header_title_en.gif';

	// 会社マスタ検索
	var mcompanyJ = '/img/type01/m/m_company_search_ja.gif';
	var mcompanyE = '/img/type01/m/m_company_search_en.gif';

	// グループマスタ検索
	var mgroupJ = '/img/type01/m/m_group_search_ja.gif';
	var mgroupE = '/img/type01/m/m_group_search_en.gif';

	// ワークフロー順序マスタ検索
	var mwforderJ = '/img/type01/m/m_wf_order_search_ja.gif';
	var mwforderE = '/img/type01/m/m_wf_order_search_en.gif';

	// 通貨レートマスタ検索
	var mmoneyrateJ = '/img/type01/m/m_monetary_rate_search_ja.gif';
	var mmoneyrateE = '/img/type01/m/m_monetary_rate_search_en.gif';

	// 想定レートマスタ検索
	var mtemporaryrateJ = '/img/type01/m/m_temporary_rate_search_ja.gif';












//////////////////////////////////////////////////////////////////
////////// 追加ボタンの生成(このファイルに設定するのは例外的処置のためです) //////////
var maddbtJ1 = '<a href="#"><img onmouseover="MasterAddJOn( this );" onmouseout="MasterAddJOff( this );" src="' + maddJ1 + '" width="72" height="20" border="0" alt="追加"></a>';
var maddbtJ3 = '<img src="' + maddJ3 + '" width="72" height="20" border="0" alt="追加">';
var maddbtE1 = '<a href="#"><img onmouseover="MasterAddEOn( this );" onmouseout="MasterAddEOff( this );" src="' + maddE1 + '" width="72" height="20" border="0" alt="ADDITION"></a>';
var maddbtE3 = '<img src="' + maddE3 + '" width="72" height="20" border="0" alt="ADDITION">';



//-->