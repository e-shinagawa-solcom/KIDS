<?php

/*-----------------------------------------------------------------------------
	サブメニュー表示設定

-----------------------------------------------------------------------------*/
	function fncSetSubMenu( $aryData, $objAuth, $objDB, $lcModel )
	{
		// ユーザーコード取得
		$lngUserCode = $objAuth->UserCode;


		// ボタン表示可否フラグ
		$aryData["lngSubFlag_p_1"]		= 1;	// 商品検索

		$aryData["lngSubFlag_es_0"]		= 1;	// 見積原価検索
		$aryData["lngSubFlag_es_1"]		= 1;	// ダウンロード
		$aryData["lngSubFlag_es_2"]		= 1;	// アップロード

		$aryData["lngSubFlag_so_1"]		= 1;	// 受注検索

		$aryData["lngSubFlag_po_1"]		= 1;	// 発注検索
		$aryData["lngSubFlag_po_0"]		= 1;	// 発注書修正

		$aryData["lngSubFlag_sc_0"]		= 1;	// 売上(納品書)登録
		$aryData["lngSubFlag_sc_1"]		= 1;	// 納品書検索
		$aryData["lngSubFlag_sc_2"]		= 1;	// 売上検索

		$aryData["lngSubFlag_pc_0"]		= 1;	// 仕入登録
		$aryData["lngSubFlag_pc_1"]		= 1;	// 仕入検索
		
		$aryData["lngSubFlag_inv_0"]	= 1;	// 請求書発行
		$aryData["lngSubFlag_inv_1"]	= 1;	// 請求書検索
		$aryData["lngSubFlag_inv_2"]	= 1;	// 請求集計

		$aryData["lngSubFlag_list_0"]	= 1;	// 商品化企画書
		$aryData["lngSubFlag_list_1"]	= 1;	// 発注書
		$aryData["lngSubFlag_list_2"]	= 1;	// 見積原価書
		$aryData["lngSubFlag_list_3"]	= 1;	// 納品伝票
		$aryData["lngSubFlag_list_4"]	= 1;	// 請求書

		$aryData["lngSubFlag_mm_0"]		= 1;	// 金型履歴登録
		$aryData["lngSubFlag_mm_1"]		= 1;	// 金型履歴検索
		$aryData["lngSubFlag_mm_2"]		= 1;	// 金型一覧検索

		$aryData["lngSubFlag_mr_0"]		= 1;	// 金型帳票登録
		$aryData["lngSubFlag_mr_1"]		= 1;	// 金型帳票検索
		
		$aryData["lngSubFlag_lc_0"]		= 1;	// L/C情報
		$aryData["lngSubFlag_lc_1"]		= 1;	// L/C設定変更

		// リンク
		// 商品管理
		$aryData["lngSubRef_p_1"]		= '/p/search/index.php?strSessionID=' . $aryData["strSessionID"];

		// 見積原価管理
		$aryData["lngSubRef_es_0"]		= '/estimate/search/index.php?strSessionID=' . $aryData["strSessionID"];
		$aryData["lngSubRef_es_1"]		= '/download/index.php?strSessionID=' . $aryData["strSessionID"];
		$aryData["lngSubRef_es_2"]		= '/upload2/index.php?strSessionID=' . $aryData["strSessionID"];

		// 受注管理
		$aryData["lngSubRef_so_1"]		= '/so/search/index.php?strSessionID=' . $aryData["strSessionID"];

		// 発注管理
		$aryData["lngSubRef_po_1"]		= '/po/search/index.php?strSessionID=' . $aryData["strSessionID"];
		$aryData["lngSubRef_po_0"]		= '/po/search2/index.php?strSessionID=' . $aryData["strSessionID"];

		// 売上管理
		$aryData["lngSubRef_sc_0"]		= '/sc/regist2/index.php?strSessionID=' . $aryData["strSessionID"];
		$aryData["lngSubRef_sc_1"]		= '/sc/search2/index.php?strSessionID=' . $aryData["strSessionID"];
		$aryData["lngSubRef_sc_2"]		= '/sc/search/index.php?strSessionID=' . $aryData["strSessionID"];

		// 仕入管理
		$aryData["lngSubRef_pc_0"]		= '/pc/regist/index.php?strSessionID=' . $aryData["strSessionID"];
		$aryData["lngSubRef_pc_1"]		= '/pc/search/index.php?strSessionID=' . $aryData["strSessionID"];

		// 帳票出力
		$aryData["lngSubRef_list_0"]	= '/list/index.php?strSessionID=' . $aryData["strSessionID"] . '&strListMode=p';
		$aryData["lngSubRef_list_1"]	= '/list/index.php?strSessionID=' . $aryData["strSessionID"] . '&strListMode=po';
		$aryData["lngSubRef_list_2"]	= '/list/index.php?strSessionID=' . $aryData["strSessionID"] . '&strListMode=es';
		$aryData["lngSubRef_list_3"]	= '/list/index.php?strSessionID=' . $aryData["strSessionID"] . '&strListMode=slp';
		$aryData["lngSubRef_list_4"]	= '/list/index.php?strSessionID=' . $aryData["strSessionID"] . '&strListMode=inv';

		// 金型管理
		$aryData["lngSubRef_mm_0"]		= '/mm/regist/index.php?strSessionID=' . $aryData["strSessionID"];
		$aryData["lngSubRef_mm_1"]		= '/mm/search/index.php?strSessionID=' . $aryData["strSessionID"];
		$aryData["lngSubRef_mm_2"]		= '/mm/list/index.php?strSessionID=' . $aryData["strSessionID"];

		$aryData["lngSubRef_mr_0"]		= '/mr/regist/index.php?strSessionID=' . $aryData["strSessionID"];
		$aryData["lngSubRef_mr_1"]		= '/mr/search/index.php?strSessionID=' . $aryData["strSessionID"];

//		$aryData["lngSubRef_lc_0"]		= '/lc/info/index.php?strSessionID=' . $aryData["strSessionID"];
		$aryData["lngSubRef_lc_0"]		= '/lc/info/start.php?strSessionID=' . $aryData["strSessionID"];
//		$aryData["lngSubRef_lc_1"]		= '/lc/set/index.php?strSessionID=' . $aryData["strSessionID"];
		$aryData["lngSubRef_lc_1"]		= '/lc/set/start.php?strSessionID=' . $aryData["strSessionID"];

		$aryData["lngSubRef_inv_0"]		= '/inv/regist/index.php?strSessionID=' . $aryData["strSessionID"];
		$aryData["lngSubRef_inv_1"]		= '/inv/search/index.php?strSessionID=' . $aryData["strSessionID"];
		$aryData["lngSubRef_inv_2"]		= '/inv/aggregate/index.php?strSessionID=' . $aryData["strSessionID"];


		//-------------------------------------------------------------------------
		// 商品管理
		//-------------------------------------------------------------------------
		//-------------------------------------------------------------------------


		//-------------------------------------------------------------------------
		// 見積原価管理
		//-------------------------------------------------------------------------
		// 見積原価検索
		if( !fncCheckAuthority( DEF_FUNCTION_E2, $objAuth ) )
		{
			$aryData["lngSubFlag_es_0"] = 0;
		}

		// ダウンロード
		if ( !fncCheckAuthority( DEF_FUNCTION_DWN, $objAuth ) )
		{
			$aryData["lngSubFlag_es_1"] = 0;
		}
		
		// アップロード
		if ( !fncCheckAuthority( DEF_FUNCTION_UP0, $objAuth ) )
		{
			$aryData["lngSubFlag_es_2"] = 0;
		}
		//-------------------------------------------------------------------------


		//-------------------------------------------------------------------------
		// 受注管理
		//-------------------------------------------------------------------------
		// 402 受注検索
		if( !fncCheckAuthority( DEF_FUNCTION_SO2, $objAuth ) )
		{
			$aryData["lngSubFlag_so_1"] = 0;

		}
		//-------------------------------------------------------------------------


		//-------------------------------------------------------------------------
		// 発注管理
		//-------------------------------------------------------------------------
		// 501 発注検索
		if( !fncCheckAuthority( DEF_FUNCTION_PO1, $objAuth ) )
		{
			$aryData["lngSubFlag_po_1"] = 0;
		}

		// 502 発注書検索
		if( !fncCheckAuthority( DEF_FUNCTION_PO2, $objAuth ) )
		{
			$aryData["lngSubFlag_po_0"] = 0;
		}
		//-------------------------------------------------------------------------


		//-------------------------------------------------------------------------
		// 売上管理
		//-------------------------------------------------------------------------
		// 601 売上(納品書)登録
		if( !fncCheckAuthority( DEF_FUNCTION_SC1, $objAuth ) )
		{
			$aryData["lngSubFlag_sc_0"] = 0;
		}

		// 612 納品書検索
		if( !fncCheckAuthority( DEF_FUNCTION_SC12, $objAuth ) )
		{
			$aryData["lngSubFlag_sc_1"] = 0;
		}
		
		// 603 売上検索
		if( !fncCheckAuthority( DEF_FUNCTION_SC2, $objAuth ) )
		{
			$aryData["lngSubFlag_sc_2"] = 0;
		}
		
	
		//-------------------------------------------------------------------------


		//-------------------------------------------------------------------------
		// 仕入管理
		//-------------------------------------------------------------------------
		// 701 仕入登録
		if( !fncCheckAuthority( DEF_FUNCTION_PC1, $objAuth ) )
		{
			$aryData["lngSubFlag_pc_0"] = 0;
		}

		// 702 仕入検索
		if( !fncCheckAuthority( DEF_FUNCTION_PC2, $objAuth ) )
		{
			$aryData["lngSubFlag_pc_1"] = 0;
		}
		//-------------------------------------------------------------------------


		//-------------------------------------------------------------------------
		// ワークフロー
		//-------------------------------------------------------------------------
		// 案件一覧
		if( !fncCheckAuthority( DEF_FUNCTION_WF1, $objAuth ) )
		{
			$aryData["lngSubFlag_wf_0"] = 0;
		}

		// 案件検索
		if( !fncCheckAuthority( DEF_FUNCTION_WF2, $objAuth ) )
		{
			$aryData["lngSubFlag_wf_1"] = 0;
		}
		//-------------------------------------------------------------------------

		//-------------------------------------------------------------------------
		// 請求管理
		//-------------------------------------------------------------------------
		// 請求書発行
		if( !fncCheckAuthority( DEF_FUNCTION_INV1, $objAuth ) )
		{
			$aryData["lngSubFlag_inv_0"] = 0;
		}
		// 請求書検索
		if( !fncCheckAuthority( DEF_FUNCTION_INV2, $objAuth ) )
		{
			$aryData["lngSubFlag_inv_1"] = 0;
		}
		// 請求集計
		if( !fncCheckAuthority( DEF_FUNCTION_INV3, $objAuth ) )
		{
			$aryData["lngSubFlag_inv_2"] = 0;
		}

		//-------------------------------------------------------------------------
		// 帳票出力
		//-------------------------------------------------------------------------
		// 商品化企画書
		if( !fncCheckAuthority( DEF_FUNCTION_LO1, $objAuth ) )
		{
			$aryData["lngSubFlag_list_0"] = 0;
		}
		// 発注書(P.O)
		if( !fncCheckAuthority( DEF_FUNCTION_LO2, $objAuth ) )
		{
			$aryData["lngSubFlag_list_1"] = 0;
		}
		// 見積原価書
		if( !fncCheckAuthority( DEF_FUNCTION_E0, $objAuth ) )
		{
			$aryData["lngSubFlag_list_2"] = 0;
		}
		// 納品書
		if( !fncCheckAuthority( DEF_FUNCTION_LO5, $objAuth ) )
		{
			$aryData["lngSubFlag_list_3"] = 0;
		}
		// 請求書
		if( !fncCheckAuthority( DEF_FUNCTION_LO7, $objAuth ) )
		{
			$aryData["lngSubFlag_list_4"] = 0;
		}
		//-------------------------------------------------------------------------


		//-------------------------------------------------------------------------
		// 金型管理
		//-------------------------------------------------------------------------
		// 1801 金型履歴登録
		if( !fncCheckAuthority( DEF_FUNCTION_MM1, $objAuth ) )
		{
			$aryData["lngSubFlag_mm_0"] = 0;
		}

		// 1802 金型履歴検索
		if( !fncCheckAuthority( DEF_FUNCTION_MM2, $objAuth ) )
		{
			$aryData["lngSubFlag_mm_1"] = 0;
		}

		// 1806 金型一覧検索
		if( !fncCheckAuthority( DEF_FUNCTION_MM6, $objAuth ) )
		{
			$aryData["lngSubFlag_mm_2"] = 0;
		}

		//-------------------------------------------------------------------------
		// 金型帳票管理
		//-------------------------------------------------------------------------
		// 1901 金型帳票登録
		if( !fncCheckAuthority( DEF_FUNCTION_MR1, $objAuth ) )
		{
			$aryData["lngSubFlag_mr_0"] = 0;
		}

		// 1902 金型帳票検索
		if( !fncCheckAuthority( DEF_FUNCTION_MR2, $objAuth ) )
		{
			$aryData["lngSubFlag_mr_1"] = 0;
		}

		//-------------------------------------------------------------------------
		// L/C管理
		//-------------------------------------------------------------------------
		
		$lcAuthority = $lcModel->checkAuthority(trim($objAuth->UserID));

		// L/C情報
		if(!$lcAuthority["lcinfo"])
		{
			$aryData["lngSubFlag_lc_0"] = 0;
		}
		// L/C設定変更
		if(!$lcAuthority["setting"])
		{
			$aryData["lngSubFlag_lc_1"] = 0;
		}

		//-------------------------------------------------------------------------

		return $aryData;
	}

?>
