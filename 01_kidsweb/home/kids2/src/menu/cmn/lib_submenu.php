<?php

/*-----------------------------------------------------------------------------
	¥µ¥Ö¥á¥Ë¥å¡¼É½¼¨ÀßÄê

-----------------------------------------------------------------------------*/
	function fncSetSubMenu( $aryData, $objAuth, $objDB, $lcModel )
	{
		// ¥æ¡¼¥¶¡¼¥³¡¼¥É¼èÆÀ
		$lngUserCode = $objAuth->UserCode;


		// ¥Ü¥¿¥óÉ½¼¨²ÄÈÝ¥Õ¥é¥°
		$aryData["lngSubFlag_p_0"]		= 1;	// ¾¦ÉÊÅÐÏ¿
		$aryData["lngSubFlag_p_1"]		= 1;	// ¾¦ÉÊ¸¡º÷

//		$aryData["lngSubFlag_es_0"]		= 1;	// ¸«ÀÑ¸¶²ÁÅÐÏ¿
		$aryData["lngSubFlag_es_0"]		= 1;	// ¸«ÀÑ¸¶²Á¸¡º÷
		$aryData["lngSubFlag_es_1"]		= 1;	// ¥À¥¦¥ó¥í¡¼¥É
		$aryData["lngSubFlag_es_2"]		= 1;	// ¥¢¥Ã¥×¥í¡¼¥É

		$aryData["lngSubFlag_so_0"]		= 1;	// ¼õÃíÅÐÏ¿
		$aryData["lngSubFlag_so_1"]		= 1;	// ¼õÃí¸¡º÷

		$aryData["lngSubFlag_po_0"]		= 1;	// È¯Ãí¸¡º÷
		$aryData["lngSubFlag_po_1"]		= 1;	// È¯Ãí½ñ½¤Àµ

		$aryData["lngSubFlag_sc_0"]		= 1;	// Çä¾å(Ç¼ÉÊ½ñ)ÅÐÏ¿
		$aryData["lngSubFlag_sc_1"]		= 1;	// Çä¾å¸¡º÷
		$aryData["lngSubFlag_sc_2"]		= 1;	// Ç¼ÉÊ½ñ¸¡º÷

		$aryData["lngSubFlag_pc_0"]		= 1;	// »ÅÆþÅÐÏ¿
		$aryData["lngSubFlag_pc_1"]		= 1;	// »ÅÆþ¸¡º÷

		$aryData["lngSubFlag_wf_0"]		= 1;	// °Æ·ï°ìÍ÷
		$aryData["lngSubFlag_wf_1"]		= 1;	// °Æ·ï¸¡º÷
		
		$aryData["lngSubFlag_inv_0"]	= 1;	// ÀÁµá½ñÈ¯¹Ô
		$aryData["lngSubFlag_inv_1"]	= 1;	// ÀÁµá½ñ¸¡º÷
		$aryData["lngSubFlag_inv_2"]	= 1;	// ÀÁµá½¸·×

		$aryData["lngSubFlag_list_0"]	= 1;	// ¾¦ÉÊ²½´ë²è½ñ
		$aryData["lngSubFlag_list_1"]	= 1;	// È¯Ãí½ñ
		$aryData["lngSubFlag_list_2"]	= 1;	// ¸«ÀÑ¸¶²Á½ñ
		$aryData["lngSubFlag_list_3"]	= 1;	// ¶â·¿°ÍÍê½ñ
		$aryData["lngSubFlag_list_4"]	= 1;	// Ç¼ÉÊÅÁÉ¼
		$aryData["lngSubFlag_list_5"]	= 1;	// ÀÁµá½ñ

		$aryData["lngSubFlag_mm_0"]		= 1;	// ¶â·¿ÍúÎòÅÐÏ¿
		$aryData["lngSubFlag_mm_1"]		= 1;	// ¶â·¿ÍúÎò¸¡º÷

		$aryData["lngSubFlag_mr_0"]		= 1;	// ¶â·¿Ä¢É¼ÅÐÏ¿
		$aryData["lngSubFlag_mr_1"]		= 1;	// ¶â·¿Ä¢É¼¸¡º÷
		
		$aryData["lngSubFlag_lc_0"]		= 1;	// L/C¾ðÊó
		$aryData["lngSubFlag_lc_1"]		= 1;	// L/CÀßÄêÊÑ¹¹

		// ¥ê¥ó¥¯
		// ¾¦ÉÊ´ÉÍý
		$aryData["lngSubRef_p_0"]		= '/p/regist/index.php?strSessionID=' . $aryData["strSessionID"];
		$aryData["lngSubRef_p_1"]		= '/p/search/index.php?strSessionID=' . $aryData["strSessionID"];

		// ¸«ÀÑ¸¶²Á´ÉÍý
//		$aryData["lngSubRef_es_0"]		= '/estimate/regist/edit.php?strSessionID=' . $aryData["strSessionID"] . '&lngFunctionCode=' . DEF_FUNCTION_E1 . '&lngRegist=1';
		$aryData["lngSubRef_es_0"]		= '/estimate/search/index.php?strSessionID=' . $aryData["strSessionID"];
		$aryData["lngSubRef_es_1"]		= '/download/index.php?strSessionID=' . $aryData["strSessionID"];
		$aryData["lngSubRef_es_2"]		= '/upload2/index.php?strSessionID=' . $aryData["strSessionID"];

		// ¼õÃí´ÉÍý
		$aryData["lngSubRef_so_0"]		= '/so/regist/index.php?strSessionID=' . $aryData["strSessionID"];
		$aryData["lngSubRef_so_1"]		= '/so/search/index.php?strSessionID=' . $aryData["strSessionID"];

		// È¯Ãí´ÉÍý
		$aryData["lngSubRef_po_0"]		= '/po/search/index.php?strSessionID=' . $aryData["strSessionID"];
		$aryData["lngSubRef_po_1"]		= '/po/search/index.php?strSessionID=' . $aryData["strSessionID"];

		// Çä¾å´ÉÍý
		$aryData["lngSubRef_sc_0"]		= '/sc/regist/index.php?strSessionID=' . $aryData["strSessionID"];
		$aryData["lngSubRef_sc_1"]		= '/sc/search/index.php?strSessionID=' . $aryData["strSessionID"];
		$aryData["lngSubRef_sc_2"]		= '/sc/search/index.php?strSessionID=' . $aryData["strSessionID"];

		// »ÅÆþ´ÉÍý
		$aryData["lngSubRef_pc_0"]		= '/pc/regist/index.php?strSessionID=' . $aryData["strSessionID"];
		$aryData["lngSubRef_pc_1"]		= '/pc/search/index.php?strSessionID=' . $aryData["strSessionID"];

		// ¥ï¡¼¥¯¥Õ¥í¡¼
		$aryData["lngSubRef_wf_0"]		= '/wf/list/index.php?strSessionID=' . $aryData["strSessionID"];
		$aryData["lngSubRef_wf_1"]		= '/wf/search/index.php?strSessionID=' . $aryData["strSessionID"];

		// Ä¢É¼½ÐÎÏ
		$aryData["lngSubRef_list_0"]	= '/list/index.php?strSessionID=' . $aryData["strSessionID"] . '&strListMode=p';
		$aryData["lngSubRef_list_1"]	= '/list/index.php?strSessionID=' . $aryData["strSessionID"] . '&strListMode=po';
		$aryData["lngSubRef_list_2"]	= '/list/index.php?strSessionID=' . $aryData["strSessionID"] . '&strListMode=es';
		$aryData["lngSubRef_list_3"]	= '/list/index.php?strSessionID=' . $aryData["strSessionID"] . '&strListMode=??';
		$aryData["lngSubRef_list_4"]	= '/list/index.php?strSessionID=' . $aryData["strSessionID"] . '&strListMode=??';
		$aryData["lngSubRef_list_5"]	= '/list/index.php?strSessionID=' . $aryData["strSessionID"] . '&strListMode=inv';

		// ¶â·¿´ÉÍý
		$aryData["lngSubRef_mm_0"]		= '/mm/regist/index.php?strSessionID=' . $aryData["strSessionID"];
		$aryData["lngSubRef_mm_1"]		= '/mm/search/index.php?strSessionID=' . $aryData["strSessionID"];

		$aryData["lngSubRef_mr_0"]		= '/mr/regist/index.php?strSessionID=' . $aryData["strSessionID"];
		$aryData["lngSubRef_mr_1"]		= '/mr/search/index.php?strSessionID=' . $aryData["strSessionID"];

		$aryData["lngSubRef_lc_0"]		= '/lc/info/index.php?strSessionID=' . $aryData["strSessionID"];
		$aryData["lngSubRef_lc_1"]		= '/lc/set/index.php?strSessionID=' . $aryData["strSessionID"];

		$aryData["lngSubRef_inv_0"]		= '/inv/regist/index.php?strSessionID=' . $aryData["strSessionID"];
		$aryData["lngSubRef_inv_1"]		= '/inv/search/index.php?strSessionID=' . $aryData["strSessionID"];
		$aryData["lngSubRef_inv_2"]		= '/inv/aggregate/index.php?strSessionID=' . $aryData["strSessionID"];


		//-------------------------------------------------------------------------
		// ¾¦ÉÊ´ÉÍý
		//-------------------------------------------------------------------------
		//-------------------------------------------------------------------------


		//-------------------------------------------------------------------------
		// ¸«ÀÑ¸¶²Á´ÉÍý
		//-------------------------------------------------------------------------
		// ¸«ÀÑ¸¶²Á¸¡º÷
		if( !fncCheckAuthority( DEF_FUNCTION_E2, $objAuth ) )
		{
			$aryData["lngSubFlag_es_0"] = 0;
		}

		// ¥À¥¦¥ó¥í¡¼¥É
		if ( !fncCheckAuthority( DEF_FUNCTION_DWN, $objAuth ) )
		{
			$aryData["lngSubFlag_es_1"] = 0;
		}
		
		// ¥¢¥Ã¥×¥í¡¼¥É
		if ( !fncCheckAuthority( DEF_FUNCTION_UP0, $objAuth ) )
		{
			$aryData["lngSubFlag_es_2"] = 0;
		}
		//-------------------------------------------------------------------------


		//-------------------------------------------------------------------------
		// ¼õÃí´ÉÍý
		//-------------------------------------------------------------------------
		// 401 ¼õÃíÅÐÏ¿
		if( !fncCheckAuthority( DEF_FUNCTION_SO1, $objAuth ) )
		{
			$aryData["lngSubFlag_so_0"] = 0;
		}

		// 402 ¼õÃí¸¡º÷
		if( !fncCheckAuthority( DEF_FUNCTION_SO2, $objAuth ) )
		{
			$aryData["lngSubFlag_so_1"] = 0;

		}
		//-------------------------------------------------------------------------


		//-------------------------------------------------------------------------
		// È¯Ãí´ÉÍý
		//-------------------------------------------------------------------------
		// 501 È¯Ãí¸¡º÷
		if( !fncCheckAuthority( DEF_FUNCTION_PO1, $objAuth ) )
		{
			$aryData["lngSubFlag_po_0"] = 0;
		}

		// 502 È¯Ãí½ñ½¤Àµ
		if( !fncCheckAuthority( DEF_FUNCTION_PO2, $objAuth ) )
		{
			$aryData["lngSubFlag_po_1"] = 0;
		}
		//-------------------------------------------------------------------------


		//-------------------------------------------------------------------------
		// Çä¾å´ÉÍý
		//-------------------------------------------------------------------------
		// 601 Çä¾å(Ç¼ÉÊ½ñ)ÅÐÏ¿
		if( !fncCheckAuthority( DEF_FUNCTION_SC1, $objAuth ) )
		{
			$aryData["lngSubFlag_sc_0"] = 0;
		}

		// 602 Çä¾å¸¡º÷
		if( !fncCheckAuthority( DEF_FUNCTION_SC2, $objAuth ) )
		{
			$aryData["lngSubFlag_sc_1"] = 0;
		}
		
		// 603 Ç¼ÉÊ½ñ¸¡º÷
		if( !fncCheckAuthority( DEF_FUNCTION_SC3, $objAuth ) )
		{
			$aryData["lngSubFlag_sc_2"] = 0;
		}
		//-------------------------------------------------------------------------


		//-------------------------------------------------------------------------
		// »ÅÆþ´ÉÍý
		//-------------------------------------------------------------------------
		// 701 »ÅÆþÅÐÏ¿
		if( !fncCheckAuthority( DEF_FUNCTION_PC1, $objAuth ) )
		{
			$aryData["lngSubFlag_pc_0"] = 0;
		}

		// 702 »ÅÆþ¸¡º÷
		if( !fncCheckAuthority( DEF_FUNCTION_PC2, $objAuth ) )
		{
			$aryData["lngSubFlag_pc_1"] = 0;
		}
		//-------------------------------------------------------------------------


		//-------------------------------------------------------------------------
		// ¥ï¡¼¥¯¥Õ¥í¡¼
		//-------------------------------------------------------------------------
		// °Æ·ï°ìÍ÷
		if( !fncCheckAuthority( DEF_FUNCTION_WF1, $objAuth ) )
		{
			$aryData["lngSubFlag_wf_0"] = 0;
		}

		// °Æ·ï¸¡º÷
		if( !fncCheckAuthority( DEF_FUNCTION_WF2, $objAuth ) )
		{
			$aryData["lngSubFlag_wf_1"] = 0;
		}
		//-------------------------------------------------------------------------

		//-------------------------------------------------------------------------
		// ÀÁµá´ÉÍý
		//-------------------------------------------------------------------------
		// ÀÁµá½ñÈ¯¹Ô
		if( !fncCheckAuthority( DEF_FUNCTION_INV1, $objAuth ) )
		{
			$aryData["lngSubFlag_inv_0"] = 0;
		}
		// ÀÁµá½ñ¸¡º÷
		if( !fncCheckAuthority( DEF_FUNCTION_INV2, $objAuth ) )
		{
			$aryData["lngSubFlag_inv_1"] = 0;
		}
		// ÀÁµá½¸·×
		if( !fncCheckAuthority( DEF_FUNCTION_INV3, $objAuth ) )
		{
			$aryData["lngSubFlag_inv_2"] = 0;
		}

		//-------------------------------------------------------------------------
		// Ä¢É¼½ÐÎÏ
		//-------------------------------------------------------------------------
		// ¾¦ÉÊ²½´ë²è½ñ
		if( !fncCheckAuthority( DEF_FUNCTION_LO1, $objAuth ) )
		{
			$aryData["lngSubFlag_list_0"] = 0;
		}
		// È¯Ãí½ñ(P.O)
		if( !fncCheckAuthority( DEF_FUNCTION_LO2, $objAuth ) )
		{
			$aryData["lngSubFlag_list_1"] = 0;
		}
		// ¸«ÀÑ¸¶²Á½ñ
		if( !fncCheckAuthority( DEF_FUNCTION_E0, $objAuth ) )
		{
			$aryData["lngSubFlag_list_2"] = 0;
		}
		//-------------------------------------------------------------------------


		//-------------------------------------------------------------------------
		// ¶â·¿´ÉÍý
		//-------------------------------------------------------------------------
		// 1801 ¶â·¿ÍúÎòÅÐÏ¿
		if( !fncCheckAuthority( DEF_FUNCTION_MM1, $objAuth ) )
		{
			$aryData["lngSubFlag_mm_0"] = 0;
		}

		// 1802 ¶â·¿ÍúÎò¸¡º÷
		if( !fncCheckAuthority( DEF_FUNCTION_MM2, $objAuth ) )
		{
			$aryData["lngSubFlag_mm_1"] = 0;
		}

		//-------------------------------------------------------------------------
		// ¶â·¿Ä¢É¼´ÉÍý
		//-------------------------------------------------------------------------
		// 1901 ¶â·¿Ä¢É¼ÅÐÏ¿
		if( !fncCheckAuthority( DEF_FUNCTION_MR1, $objAuth ) )
		{
			$aryData["lngSubFlag_mr_0"] = 0;
		}

		// 1902 ¶â·¿Ä¢É¼¸¡º÷
		if( !fncCheckAuthority( DEF_FUNCTION_MR2, $objAuth ) )
		{
			$aryData["lngSubFlag_mr_1"] = 0;
		}

		//-------------------------------------------------------------------------
		// L/C´ÉÍý
		//-------------------------------------------------------------------------
		
		$lcAuthority = $lcModel->checkAuthority(trim($objAuth->UserID));

		// L/C¾ðÊó
		if(!$lcAuthority["lcinfo"])
		{
			$aryData["lngSubFlag_lc_0"] = 0;
		}
		// L/CÀßÄêÊÑ¹¹
		if(!$lcAuthority["setting"])
		{
			$aryData["lngSubFlag_lc_1"] = 0;
		}

		//-------------------------------------------------------------------------
		// ¸¢¸Â¥°¥ë¡¼¥×¥³¡¼¥É(¥æ¡¼¥¶¡¼°Ê²¼)¥Á¥§¥Ã¥¯
		//-------------------------------------------------------------------------
		$blnAG = fncCheckUserAuthorityGroupCode( $lngUserCode, $aryData["strSessionID"], $objDB );

		// ¡Ö¥æ¡¼¥¶¡¼¡×°Ê²¼¤Î¾ì¹ç
		if( $blnAG )
		{
			// ¾µÇ§¥ë¡¼¥ÈÂ¸ºß¥Á¥§¥Ã¥¯
			$blnWF = fncCheckWorkFlowRoot( $lngUserCode, $aryData["strSessionID"], $objDB );

			// ¾µÇ§¥ë¡¼¥È¤¬Â¸ºß¤·¤Ê¤¤¾ì¹ç¡¢¡ÖÅÐÏ¿¡×¥Ü¥¿¥óÈóÉ½¼¨
			if( !$blnWF )
			{
				$aryData["lngSubFlag_p_0"]	= 0;

				$aryData["lngSubFlag_es_0"]	= 0;

				$aryData["lngSubFlag_so_0"]	= 0;

				$aryData["lngSubFlag_po_0"]	= 0;

				$aryData["lngSubFlag_sc_0"]	= 0;

				$aryData["lngSubFlag_pc_0"]	= 0;
			}
		}
		//-------------------------------------------------------------------------

		return $aryData;
	}

?>
