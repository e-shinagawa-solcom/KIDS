<?php

/*-----------------------------------------------------------------------------
	���֥�˥塼ɽ������

-----------------------------------------------------------------------------*/
	function fncSetSubMenu( $aryData, $objAuth, $objDB, $lcModel )
	{
		// �桼���������ɼ���
		$lngUserCode = $objAuth->UserCode;


		// �ܥ���ɽ�����ݥե饰
		$aryData["lngSubFlag_p_0"]		= 1;	// ������Ͽ
		$aryData["lngSubFlag_p_1"]		= 1;	// ���ʸ���

//		$aryData["lngSubFlag_es_0"]		= 1;	// ���Ѹ�����Ͽ
		$aryData["lngSubFlag_es_0"]		= 1;	// ���Ѹ�������
		$aryData["lngSubFlag_es_1"]		= 1;	// ���������
		$aryData["lngSubFlag_es_2"]		= 1;	// ���åץ���

		$aryData["lngSubFlag_so_0"]		= 1;	// ������Ͽ
		$aryData["lngSubFlag_so_1"]		= 1;	// ������

		$aryData["lngSubFlag_po_0"]		= 1;	// ȯ����
		$aryData["lngSubFlag_po_1"]		= 1;	// ȯ�����

		$aryData["lngSubFlag_sc_0"]		= 1;	// ���(Ǽ�ʽ�)��Ͽ
		$aryData["lngSubFlag_sc_1"]		= 1;	// ��帡��
		$aryData["lngSubFlag_sc_2"]		= 1;	// Ǽ�ʽ񸡺�

		$aryData["lngSubFlag_pc_0"]		= 1;	// ������Ͽ
		$aryData["lngSubFlag_pc_1"]		= 1;	// ��������

		$aryData["lngSubFlag_wf_0"]		= 1;	// �Ʒ����
		$aryData["lngSubFlag_wf_1"]		= 1;	// �Ʒ︡��
		
		$aryData["lngSubFlag_inv_0"]	= 1;	// �����ȯ��
		$aryData["lngSubFlag_inv_1"]	= 1;	// ����񸡺�
		$aryData["lngSubFlag_inv_2"]	= 1;	// ���ὸ��

		$aryData["lngSubFlag_list_0"]	= 1;	// ���ʲ�����
		$aryData["lngSubFlag_list_1"]	= 1;	// ȯ���
		$aryData["lngSubFlag_list_2"]	= 1;	// ���Ѹ�����
		$aryData["lngSubFlag_list_3"]	= 1;	// �ⷿ�����
		$aryData["lngSubFlag_list_4"]	= 1;	// Ǽ����ɼ
		$aryData["lngSubFlag_list_5"]	= 1;	// �����

		$aryData["lngSubFlag_mm_0"]		= 1;	// �ⷿ������Ͽ
		$aryData["lngSubFlag_mm_1"]		= 1;	// �ⷿ���򸡺�

		$aryData["lngSubFlag_mr_0"]		= 1;	// �ⷿĢɼ��Ͽ
		$aryData["lngSubFlag_mr_1"]		= 1;	// �ⷿĢɼ����
		
		$aryData["lngSubFlag_lc_0"]		= 1;	// L/C����
		$aryData["lngSubFlag_lc_1"]		= 1;	// L/C�����ѹ�

		// ���
		// ���ʴ���
		$aryData["lngSubRef_p_0"]		= '/p/regist/index.php?strSessionID=' . $aryData["strSessionID"];
		$aryData["lngSubRef_p_1"]		= '/p/search/index.php?strSessionID=' . $aryData["strSessionID"];

		// ���Ѹ�������
//		$aryData["lngSubRef_es_0"]		= '/estimate/regist/edit.php?strSessionID=' . $aryData["strSessionID"] . '&lngFunctionCode=' . DEF_FUNCTION_E1 . '&lngRegist=1';
		$aryData["lngSubRef_es_0"]		= '/estimate/search/index.php?strSessionID=' . $aryData["strSessionID"];
		$aryData["lngSubRef_es_1"]		= '/download/index.php?strSessionID=' . $aryData["strSessionID"];
		$aryData["lngSubRef_es_2"]		= '/upload2/index.php?strSessionID=' . $aryData["strSessionID"];

		// �������
		$aryData["lngSubRef_so_0"]		= '/so/regist/index.php?strSessionID=' . $aryData["strSessionID"];
		$aryData["lngSubRef_so_1"]		= '/so/search/index.php?strSessionID=' . $aryData["strSessionID"];

		// ȯ�����
		$aryData["lngSubRef_po_0"]		= '/po/search/index.php?strSessionID=' . $aryData["strSessionID"];
		$aryData["lngSubRef_po_1"]		= '/po/search/index.php?strSessionID=' . $aryData["strSessionID"];

		// ������
		$aryData["lngSubRef_sc_0"]		= '/sc/regist/index.php?strSessionID=' . $aryData["strSessionID"];
		$aryData["lngSubRef_sc_1"]		= '/sc/search/index.php?strSessionID=' . $aryData["strSessionID"];
		$aryData["lngSubRef_sc_2"]		= '/sc/search/index.php?strSessionID=' . $aryData["strSessionID"];

		// ��������
		$aryData["lngSubRef_pc_0"]		= '/pc/regist/index.php?strSessionID=' . $aryData["strSessionID"];
		$aryData["lngSubRef_pc_1"]		= '/pc/search/index.php?strSessionID=' . $aryData["strSessionID"];

		// ����ե�
		$aryData["lngSubRef_wf_0"]		= '/wf/list/index.php?strSessionID=' . $aryData["strSessionID"];
		$aryData["lngSubRef_wf_1"]		= '/wf/search/index.php?strSessionID=' . $aryData["strSessionID"];

		// Ģɼ����
		$aryData["lngSubRef_list_0"]	= '/list/index.php?strSessionID=' . $aryData["strSessionID"] . '&strListMode=p';
		$aryData["lngSubRef_list_1"]	= '/list/index.php?strSessionID=' . $aryData["strSessionID"] . '&strListMode=po';
		$aryData["lngSubRef_list_2"]	= '/list/index.php?strSessionID=' . $aryData["strSessionID"] . '&strListMode=es';
		$aryData["lngSubRef_list_3"]	= '/list/index.php?strSessionID=' . $aryData["strSessionID"] . '&strListMode=??';
		$aryData["lngSubRef_list_4"]	= '/list/index.php?strSessionID=' . $aryData["strSessionID"] . '&strListMode=??';
		$aryData["lngSubRef_list_5"]	= '/list/index.php?strSessionID=' . $aryData["strSessionID"] . '&strListMode=inv';

		// �ⷿ����
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
		// ���ʴ���
		//-------------------------------------------------------------------------
		//-------------------------------------------------------------------------


		//-------------------------------------------------------------------------
		// ���Ѹ�������
		//-------------------------------------------------------------------------
		// ���Ѹ�������
		if( !fncCheckAuthority( DEF_FUNCTION_E2, $objAuth ) )
		{
			$aryData["lngSubFlag_es_0"] = 0;
		}

		// ���������
		if ( !fncCheckAuthority( DEF_FUNCTION_DWN, $objAuth ) )
		{
			$aryData["lngSubFlag_es_1"] = 0;
		}
		
		// ���åץ���
		if ( !fncCheckAuthority( DEF_FUNCTION_UP0, $objAuth ) )
		{
			$aryData["lngSubFlag_es_2"] = 0;
		}
		//-------------------------------------------------------------------------


		//-------------------------------------------------------------------------
		// �������
		//-------------------------------------------------------------------------
		// 401 ������Ͽ
		if( !fncCheckAuthority( DEF_FUNCTION_SO1, $objAuth ) )
		{
			$aryData["lngSubFlag_so_0"] = 0;
		}

		// 402 ������
		if( !fncCheckAuthority( DEF_FUNCTION_SO2, $objAuth ) )
		{
			$aryData["lngSubFlag_so_1"] = 0;

		}
		//-------------------------------------------------------------------------


		//-------------------------------------------------------------------------
		// ȯ�����
		//-------------------------------------------------------------------------
		// 501 ȯ����
		if( !fncCheckAuthority( DEF_FUNCTION_PO1, $objAuth ) )
		{
			$aryData["lngSubFlag_po_0"] = 0;
		}

		// 502 ȯ�����
		if( !fncCheckAuthority( DEF_FUNCTION_PO2, $objAuth ) )
		{
			$aryData["lngSubFlag_po_1"] = 0;
		}
		//-------------------------------------------------------------------------


		//-------------------------------------------------------------------------
		// ������
		//-------------------------------------------------------------------------
		// 601 ���(Ǽ�ʽ�)��Ͽ
		if( !fncCheckAuthority( DEF_FUNCTION_SC1, $objAuth ) )
		{
			$aryData["lngSubFlag_sc_0"] = 0;
		}

		// 602 ��帡��
		if( !fncCheckAuthority( DEF_FUNCTION_SC2, $objAuth ) )
		{
			$aryData["lngSubFlag_sc_1"] = 0;
		}
		
		// 603 Ǽ�ʽ񸡺�
		if( !fncCheckAuthority( DEF_FUNCTION_SC3, $objAuth ) )
		{
			$aryData["lngSubFlag_sc_2"] = 0;
		}
		//-------------------------------------------------------------------------


		//-------------------------------------------------------------------------
		// ��������
		//-------------------------------------------------------------------------
		// 701 ������Ͽ
		if( !fncCheckAuthority( DEF_FUNCTION_PC1, $objAuth ) )
		{
			$aryData["lngSubFlag_pc_0"] = 0;
		}

		// 702 ��������
		if( !fncCheckAuthority( DEF_FUNCTION_PC2, $objAuth ) )
		{
			$aryData["lngSubFlag_pc_1"] = 0;
		}
		//-------------------------------------------------------------------------


		//-------------------------------------------------------------------------
		// ����ե�
		//-------------------------------------------------------------------------
		// �Ʒ����
		if( !fncCheckAuthority( DEF_FUNCTION_WF1, $objAuth ) )
		{
			$aryData["lngSubFlag_wf_0"] = 0;
		}

		// �Ʒ︡��
		if( !fncCheckAuthority( DEF_FUNCTION_WF2, $objAuth ) )
		{
			$aryData["lngSubFlag_wf_1"] = 0;
		}
		//-------------------------------------------------------------------------

		//-------------------------------------------------------------------------
		// �������
		//-------------------------------------------------------------------------
		// �����ȯ��
		if( !fncCheckAuthority( DEF_FUNCTION_INV1, $objAuth ) )
		{
			$aryData["lngSubFlag_inv_0"] = 0;
		}
		// ����񸡺�
		if( !fncCheckAuthority( DEF_FUNCTION_INV2, $objAuth ) )
		{
			$aryData["lngSubFlag_inv_1"] = 0;
		}
		// ���ὸ��
		if( !fncCheckAuthority( DEF_FUNCTION_INV3, $objAuth ) )
		{
			$aryData["lngSubFlag_inv_2"] = 0;
		}

		//-------------------------------------------------------------------------
		// Ģɼ����
		//-------------------------------------------------------------------------
		// ���ʲ�����
		if( !fncCheckAuthority( DEF_FUNCTION_LO1, $objAuth ) )
		{
			$aryData["lngSubFlag_list_0"] = 0;
		}
		// ȯ���(P.O)
		if( !fncCheckAuthority( DEF_FUNCTION_LO2, $objAuth ) )
		{
			$aryData["lngSubFlag_list_1"] = 0;
		}
		// ���Ѹ�����
		if( !fncCheckAuthority( DEF_FUNCTION_E0, $objAuth ) )
		{
			$aryData["lngSubFlag_list_2"] = 0;
		}
		//-------------------------------------------------------------------------


		//-------------------------------------------------------------------------
		// �ⷿ����
		//-------------------------------------------------------------------------
		// 1801 �ⷿ������Ͽ
		if( !fncCheckAuthority( DEF_FUNCTION_MM1, $objAuth ) )
		{
			$aryData["lngSubFlag_mm_0"] = 0;
		}

		// 1802 �ⷿ���򸡺�
		if( !fncCheckAuthority( DEF_FUNCTION_MM2, $objAuth ) )
		{
			$aryData["lngSubFlag_mm_1"] = 0;
		}

		//-------------------------------------------------------------------------
		// �ⷿĢɼ����
		//-------------------------------------------------------------------------
		// 1901 �ⷿĢɼ��Ͽ
		if( !fncCheckAuthority( DEF_FUNCTION_MR1, $objAuth ) )
		{
			$aryData["lngSubFlag_mr_0"] = 0;
		}

		// 1902 �ⷿĢɼ����
		if( !fncCheckAuthority( DEF_FUNCTION_MR2, $objAuth ) )
		{
			$aryData["lngSubFlag_mr_1"] = 0;
		}

		//-------------------------------------------------------------------------
		// L/C����
		//-------------------------------------------------------------------------
		
		$lcAuthority = $lcModel->checkAuthority(trim($objAuth->UserID));

		// L/C����
		if(!$lcAuthority["lcinfo"])
		{
			$aryData["lngSubFlag_lc_0"] = 0;
		}
		// L/C�����ѹ�
		if(!$lcAuthority["setting"])
		{
			$aryData["lngSubFlag_lc_1"] = 0;
		}

		//-------------------------------------------------------------------------
		// ���¥��롼�ץ�����(�桼�����ʲ�)�����å�
		//-------------------------------------------------------------------------
		$blnAG = fncCheckUserAuthorityGroupCode( $lngUserCode, $aryData["strSessionID"], $objDB );

		// �֥桼�����װʲ��ξ��
		if( $blnAG )
		{
			// ��ǧ�롼��¸�ߥ����å�
			$blnWF = fncCheckWorkFlowRoot( $lngUserCode, $aryData["strSessionID"], $objDB );

			// ��ǧ�롼�Ȥ�¸�ߤ��ʤ���硢����Ͽ�ץܥ�����ɽ��
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
