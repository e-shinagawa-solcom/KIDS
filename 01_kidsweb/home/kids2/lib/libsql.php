<?php
	
	//
	// カテゴリーマスタの取得
	// 所属グループに関連するカテゴリー一覧を取得
	//
	// @param  Array	$aryParam	パラメータ配列
	//
	// $aryParam[0]	ユーザーコード
	//
	function fncSqlqueryCategory($aryParam)
	{
		// SQL 設定
		// カテゴリー
/*
		$aryQuery = array();
		$aryQuery[] = "SELECT mc.lngcategorycode, mc.strcategoryname";
		$aryQuery[] = "FROM m_Category mc";
		$aryQuery[] = "	LEFT JOIN m_CategoryRelation mcr";
		$aryQuery[] = "	ON mc.lngcategorycode = mcr.lngcategorycode";
		$aryQuery[] = "WHERE mc.bytDisplayFlag=true";
		$aryQuery[] = "AND mcr.lnggroupcode in";
		$aryQuery[] = "	(";
		$aryQuery[] = "	select";
		$aryQuery[] = "		mg.lnggroupcode";
		$aryQuery[] = "	from";
		$aryQuery[] = "		m_group as mg ";
		$aryQuery[] = "		left join m_grouprelation mgr";
		$aryQuery[] = "			on mg.lnggroupcode = mgr.lnggroupcode";
		$aryQuery[] = "	where";
		$aryQuery[] = "		mg.bytgroupdisplayflag = true";
		$aryQuery[] = "	 	and mgr.lngusercode = ". $aryParam[0];
		$aryQuery[] = "	order by ";
		$aryQuery[] = "		mg.lnggroupcode";
		$aryQuery[] = "	)";
		$aryQuery[] = "ORDER BY mc.lngSortKey";
*/

		$aryQuery = array();
		$aryQuery[] = "SELECT m1.lngcategorycode ,m1.strcategoryname";
		$aryQuery[] = "FROM";
		$aryQuery[] = "(";
		$aryQuery[] = "SELECT mc.lngcategorycode,";
		$aryQuery[] = "	mg1.strgroupdisplayname || '：' || mc.strcategoryname as strcategoryname";
		$aryQuery[] = "	,mc.lngSortKey";
		$aryQuery[] = "FROM m_Category mc LEFT JOIN m_CategoryRelation mcr ON mc.lngcategorycode =mcr.lngcategorycode";
		$aryQuery[] = "	,m_Group mg1";
		$aryQuery[] = "WHERE mc.bytDisplayFlag = true";
		$aryQuery[] = "AND mg1.lnggroupcode = mcr.lnggroupcode";
		$aryQuery[] = "AND mcr.lnggroupcode in (select mg.lnggroupcode";
		$aryQuery[] = "	from m_group as mg left join m_grouprelation mgr on mg.lnggroupcode = mgr.lnggroupcode";
		$aryQuery[] = "	,m_user mu";
		$aryQuery[] = "	where mg.bytgroupdisplayflag = true";
		$aryQuery[] = "	and mu.bytinvalidflag = false";
		$aryQuery[] = "	and mgr.lngusercode = mu.lngusercode";
		$aryQuery[] = "	and case when '".$aryParam[0]."' != '' then mu.lngusercode = '".$aryParam[0]."' else true end";
		$aryQuery[] = "	order by mg.lnggroupcode)";
		$aryQuery[] = "union";
		$aryQuery[] = "SELECT";
		$aryQuery[] = "0, mc.strcategoryname, mc.lngsortkey";
		$aryQuery[] = "FROM m_Category mc where mc.lngcategorycode = 0";
		$aryQuery[] = "ORDER BY lngSortKey";
		$aryQuery[] = ") m1";

		return implode("\n", $aryQuery );
	}



	//
	// カテゴリーマスタの取得
	// 所属グループに関連するカテゴリー一覧を取得（商品検索、初期表示用）
	//
	// @param  Array	$aryParam	パラメータ配列
	//
	// $aryParam[0]	ユーザーコード
	//
	function fncSqlqueryCategory2($aryParam)
	{
		// SQL 設定
		// カテゴリー

		$strFile = file_get_contents(LIB_ROOT."sql/cnCategoryGroupUser.sql");
		if(!$strFile) return "";

		return str_replace('_%strFormValue0%_', '0', $strFile);

	}



?>
