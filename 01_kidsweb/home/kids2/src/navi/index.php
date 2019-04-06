<?php

// ----------------------------------------------------------------------------
/**
*       ��եȥʥӥ������������
*
*
*       @package    K.I.D.S.
*       @license    http://www.kuwagata.co.jp/
*       @copyright  KUWAGATA CO., LTD.
*       @author     K.I.D.S. Groups <info@kids-groups.com>
*       @access     public
*       @version    2.00
*
*
*       ��������
*         ���ѥ�᡼����ꡢ�ܥ��󥪥֥������Ȥ�����
*
*       ��������
*
*/
// ----------------------------------------------------------------------------



	//-------------------------------------------------------------------------
	// �饤�֥���ɤ߹���
	//-------------------------------------------------------------------------
	include_once ( 'conf.inc' );
	require ( LIB_FILE );
	require ( LIB_DEBUGFILE );
	//-------------------------------------------------------------------------



	//-------------------------------------------------------------------------
	// �ѥ�᡼�������
	//-------------------------------------------------------------------------
	$aryData		= array();
	$strDirName		= "";
	$strNaviCode	= "";
	$length			= 0;
	$aryNaviCode	= array();
	$aryDisabled	= array();
	//-------------------------------------------------------------------------



	//-------------------------------------------------------------------------
	// �ꥯ�����ȼ���
	//-------------------------------------------------------------------------
	$aryData = $_REQUEST;
	//-------------------------------------------------------------------------



	//-------------------------------------------------------------------------
	// ���֥�����������
	//-------------------------------------------------------------------------
	$objDB			= new clsDB();
	$objAuth		= new clsAuth();
	$objTemplate	= new clsTemplate();
	//-------------------------------------------------------------------------



	//-------------------------------------------------------------------------
	// ���å�����ǧ
	//-------------------------------------------------------------------------
	$objDB->open( "", "", "", "" );

	// ���å�����ǧ
	$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );

	// DB������
	$objDB->close();
	//-------------------------------------------------------------------------



	//-------------------------------------------------------------------------
	// �����ǥ��쥯�ȥꡦɽ�������פ����
	//-------------------------------------------------------------------------
	list( $strDirName, $strNaviCode ) = explode( "-", $aryData["strNaviCode"] );

	$length = strlen( $strNaviCode );

	for( $i = 0; $i < $length; $i++ )
	{
		$aryNaviCode[$i] = substr ( $strNaviCode, $i, 1 );

		if( $aryNaviCode[$i] == 3 )
		{
			$aryDisabled[$i] = "disabled";
		}
	}
	//-------------------------------------------------------------------------



	//-------------------------------------------------------------------------
	// META��������
	//-------------------------------------------------------------------------
	$aryData["strMeta"]	 = '<script type="text/javascript" language="javascript" src="/layout/' . LAYOUT_CODE . '/navi/' . $strDirName . '/images.js"></script>';
	$aryData["strMeta"]	.= '<script type="text/javascript" language="javascript" src="/layout/' . LAYOUT_CODE . '/navi/cmn/images.js"></script>';
	$aryData["strMeta"]	.= '<script type="text/javascript" language="javascript" src="/layout/' . LAYOUT_CODE . '/navi/' . $strDirName . '/initlayout.js"></script>';
	$aryData["strMeta"]	.= '<script type="text/javascript" language="javascript" src="/layout/' . LAYOUT_CODE . '/navi/cmn/initlayoutnavi.js"></script>';
	$aryData["strMeta"]	.= '<script type="text/javascript" language="javascript" src="/navi/cmn/exstr.js"></script>';

	if( $strDirName == "uc" )
	{
		$aryData["strMeta"]	.= '<link rel="stylesheet" type="text/css" media="screen" href="/navi/uc/layout.css">';
	}
	else
	{
		$aryData["strMeta"]	.= '<link rel="stylesheet" type="text/css" media="screen" href="/navi/cmn/layout.css">';
	}
	//-------------------------------------------------------------------------



	//-------------------------------------------------------------------------
	// �ʥӥ��������ܥ�������
	//-------------------------------------------------------------------------
	switch( $strDirName )
	{
		// ���ʴ���
		case "p":
			//if( !fncCheckAuthority( DEF_FUNCTION_P0, $objAuth ) ) break;

			$strVisibility1	= ( !fncCheckAuthority( DEF_FUNCTION_P1, $objAuth ) )	? 'style="visibility:hidden"' : '';
			$strVisibility2	= ( !fncCheckAuthority( DEF_FUNCTION_P2, $objAuth ) )	? 'style="visibility:hidden"' : '';
			$strVisibility3	= ( !fncCheckAuthority( DEF_FUNCTION_LO0, $objAuth ) )	? 'style="visibility:hidden"' : '';

			$aryData["strButton"]	 = '<span id="RegistNaviBt' . $aryNaviCode[0] . '" ' . $strVisibility1 . ' onclick="top.location=\'/p/regist/index.php?strSessionID=' . $aryData["strSessionID"] . '\';" ' . $aryDisabled[0] . '></span>';
			$aryData["strButton"]	.= '<span id="SearchNaviBt' . $aryNaviCode[1] . '" ' . $strVisibility2 . ' onclick="top.location=\'/p/search/index.php?strSessionID=' . $aryData["strSessionID"] . '\';" ' . $aryDisabled[1] . '></span>';
			$aryData["strButton"]	.= '<span id="ListExNaviBt' . $aryNaviCode[2] . '" ' . $strVisibility3 . ' onclick="top.location=\'/list/index.php?strSessionID=' . $aryData["strSessionID"] . '\';" ' . $aryDisabled[2] . '></span>';
			break;


		// ���Ѹ�������
		case "estimate":
			//if( !fncCheckAuthority( DEF_FUNCTION_E0, $objAuth ) ) break;

//			$strVisibility1	= ( !fncCheckAuthority( DEF_FUNCTION_E1, $objAuth ) )	? 'style="visibility:hidden"' : '';
			$strVisibility1	= 'style="visibility:hidden"';
			$strVisibility2	= ( !fncCheckAuthority( DEF_FUNCTION_E2, $objAuth ) )	? 'style="visibility:hidden"' : '';
			$strVisibility3	= ( !fncCheckAuthority( DEF_FUNCTION_LO0, $objAuth ) )	? 'style="visibility:hidden"' : '';
			$strVisibility4	= ( !fncCheckAuthority( DEF_FUNCTION_UP0, $objAuth ) )	? 'style="visibility:hidden"' : '';

			$aryData["strButton"]	 = '<span id="RegistNaviBt' . $aryNaviCode[0] . '" ' . $strVisibility1 . ' onclick="top.location=\'/estimate/regist/edit.php?strSessionID=' . $aryData["strSessionID"] . '&lngFunctionCode=1501\';" ' . $aryDisabled[0] . '></span>';
			$aryData["strButton"]	.= '<span id="SearchNaviBt' . $aryNaviCode[1] . '" ' . $strVisibility2 . ' onclick="top.location=\'/estimate/search/index.php?strSessionID=' . $aryData["strSessionID"] . '\';" ' . $aryDisabled[1] . '></span>';
			$aryData["strButton"]	.= '<span id="ListExNaviBt' . $aryNaviCode[2] . '" ' . $strVisibility3 . ' onclick="top.location=\'/list/index.php?strSessionID=' . $aryData["strSessionID"] . '\';" ' . $aryDisabled[2] . '></span>';
			$aryData["strButton"]	.= '<span id="UploadNaviBt' . $aryNaviCode[3] . '" ' . $strVisibility4 . ' onclick="top.location=\'/upload2/index.php?strSessionID=' . $aryData["strSessionID"] . '\';" ' . $aryDisabled[3] . '></span>';
			break;


		// �������
		case "so":
			//if( !fncCheckAuthority( DEF_FUNCTION_SO0, $objAuth ) ) break;

			$strVisibility1	= ( !fncCheckAuthority( DEF_FUNCTION_SO1, $objAuth ) )	? 'style="visibility:hidden"' : '';
			$strVisibility2	= ( !fncCheckAuthority( DEF_FUNCTION_SO2, $objAuth ) )	? 'style="visibility:hidden"' : '';

			$strVisibility3	= ( !fncCheckAuthority( DEF_FUNCTION_SC1, $objAuth ) )	? 'style="visibility:hidden"' : '';
			$strVisibility4	= ( !fncCheckAuthority( DEF_FUNCTION_SC2, $objAuth ) )	? 'style="visibility:hidden"' : '';

			$aryData["strButton"]	 = '<span id="RegistNaviBt' . $aryNaviCode[0] . '" ' . $strVisibility1 . ' onclick="top.location=\'/so/regist/index.php?strSessionID=' . $aryData["strSessionID"] . '\';" ' . $aryDisabled[0] . '></span>';
			$aryData["strButton"]	.= '<span id="SearchNaviBt' . $aryNaviCode[1] . '" ' . $strVisibility2 . ' onclick="top.location=\'/so/search/index.php?strSessionID=' . $aryData["strSessionID"] . '\';" ' . $aryDisabled[1] . '></span>';

			$aryData["strButton"]	.= '<span id="RegistNaviBtA" ' . $strVisibility3 . ' onclick="top.location=\'/sc/regist/index.php?strSessionID=' . $aryData["strSessionID"] . '\';"></span>';
			$aryData["strButton"]	.= '<span id="SearchNaviBtA" ' . $strVisibility4 . ' onclick="top.location=\'/sc/search/index.php?strSessionID=' . $aryData["strSessionID"] . '\';"></span>';
			break;


		// ȯ�����
		case "po":
			//if( !fncCheckAuthority( DEF_FUNCTION_PO0, $objAuth ) ) break;

			$strVisibility1	= ( !fncCheckAuthority( DEF_FUNCTION_PO1, $objAuth ) )	? 'style="visibility:hidden"' : '';
			$strVisibility2	= ( !fncCheckAuthority( DEF_FUNCTION_PO2, $objAuth ) )	? 'style="visibility:hidden"' : '';
			$strVisibility3	= ( !fncCheckAuthority( DEF_FUNCTION_LO0, $objAuth ) )	? 'style="visibility:hidden"' : '';
			$strVisibility4	= ( !fncCheckAuthority( DEF_FUNCTION_DE0, $objAuth ) )	? 'style="visibility:hidden"' : '';

			$strVisibility5	= ( !fncCheckAuthority( DEF_FUNCTION_PC1, $objAuth ) )	? 'style="visibility:hidden"' : '';
			$strVisibility6	= ( !fncCheckAuthority( DEF_FUNCTION_PC2, $objAuth ) )	? 'style="visibility:hidden"' : '';

			$aryData["strButton"]	 = '<span id="RegistNaviBt' . $aryNaviCode[0] . '" ' . $strVisibility1 . ' onclick="top.location=\'/po/regist/index.php?strSessionID=' . $aryData["strSessionID"] . '\';" ' . $aryDisabled[0] . '></span>';
			$aryData["strButton"]	.= '<span id="SearchNaviBt' . $aryNaviCode[1] . '" ' . $strVisibility2 . ' onclick="top.location=\'/po/search/index.php?strSessionID=' . $aryData["strSessionID"] . '\';" ' . $aryDisabled[1] . '></span>';
			$aryData["strButton"]	.= '<span id="ListExNaviBt' . $aryNaviCode[2] . '" ' . $strVisibility3 . ' onclick="top.location=\'/list/index.php?strSessionID=' . $aryData["strSessionID"] . '\';" ' . $aryDisabled[2] . '></span>';
			$aryData["strButton"]	.= '<span id="DataExNaviBt' . $aryNaviCode[3] . '" ' . $strVisibility4 . ' onclick="top.location=\'/dataex/index.php?strSessionID=' . $aryData["strSessionID"] . '\';" ' . $aryDisabled[3] . '></span>';

			$aryData["strButton"]	.= '<span id="RegistNaviBtA" ' . $strVisibility5 . ' onclick="top.location=\'/pc/regist/index.php?strSessionID=' . $aryData["strSessionID"] . '\';"></span>';
			$aryData["strButton"]	.= '<span id="SearchNaviBtA" ' . $strVisibility6 . ' onclick="top.location=\'/pc/search/index.php?strSessionID=' . $aryData["strSessionID"] . '\';"></span>';
			break;


		// ������
		case "sc":
			//if( !fncCheckAuthority( DEF_FUNCTION_SC0, $objAuth ) ) break;

			$strVisibility1	= ( !fncCheckAuthority( DEF_FUNCTION_SC1, $objAuth ) )	? 'style="visibility:hidden"' : '';
			$strVisibility2	= ( !fncCheckAuthority( DEF_FUNCTION_SC2, $objAuth ) )	? 'style="visibility:hidden"' : '';
			$strVisibility3	= ( !fncCheckAuthority( DEF_FUNCTION_DE0, $objAuth ) )	? 'style="visibility:hidden"' : '';

			$strVisibility4	= ( !fncCheckAuthority( DEF_FUNCTION_SO1, $objAuth ) )	? 'style="visibility:hidden"' : '';
			$strVisibility5	= ( !fncCheckAuthority( DEF_FUNCTION_SO2, $objAuth ) )	? 'style="visibility:hidden"' : '';

			$aryData["strButton"]	 = '<span id="RegistNaviBt' . $aryNaviCode[0] . '" ' . $strVisibility1 . ' onclick="top.location=\'/sc/regist/index.php?strSessionID=' . $aryData["strSessionID"] . '\';" ' . $aryDisabled[0] . '></span>';
			$aryData["strButton"]	.= '<span id="SearchNaviBt' . $aryNaviCode[1] . '" ' . $strVisibility2 . ' onclick="top.location=\'/sc/search/index.php?strSessionID=' . $aryData["strSessionID"] . '\';" ' . $aryDisabled[1] . '></span>';
			$aryData["strButton"]	.= '<span id="DataExNaviBt' . $aryNaviCode[2] . '" ' . $strVisibility3 . ' onclick="top.location=\'/dataex/index.php?strSessionID=' . $aryData["strSessionID"] . '\';" ' . $aryDisabled[2] . '></span>';

			$aryData["strButton"]	.= '<span id="RegistNaviBtA" ' . $strVisibility4 . ' onclick="top.location=\'/so/regist/index.php?strSessionID=' . $aryData["strSessionID"] . '\';"></span>';
			$aryData["strButton"]	.= '<span id="SearchNaviBtA" ' . $strVisibility5 . ' onclick="top.location=\'/so/search/index.php?strSessionID=' . $aryData["strSessionID"] . '\';"></span>';
			break;


		// ��������
		case "pc":
			//if( !fncCheckAuthority( DEF_FUNCTION_PC0, $objAuth ) ) break;

			$strVisibility1	= ( !fncCheckAuthority( DEF_FUNCTION_PC1, $objAuth ) )	? 'style="visibility:hidden"' : '';
			$strVisibility2	= ( !fncCheckAuthority( DEF_FUNCTION_PC2, $objAuth ) )	? 'style="visibility:hidden"' : '';
			$strVisibility3	= ( !fncCheckAuthority( DEF_FUNCTION_DE0, $objAuth ) )	? 'style="visibility:hidden"' : '';

			$strVisibility4	= ( !fncCheckAuthority( DEF_FUNCTION_PO1, $objAuth ) )	? 'style="visibility:hidden"' : '';
			$strVisibility5	= ( !fncCheckAuthority( DEF_FUNCTION_PO2, $objAuth ) )	? 'style="visibility:hidden"' : '';

			$aryData["strButton"]	 = '<span id="RegistNaviBt' . $aryNaviCode[0] . '" ' . $strVisibility1 . ' onclick="top.location=\'/pc/regist/index.php?strSessionID=' . $aryData["strSessionID"] . '\';" ' . $aryDisabled[0] . '></span>';
			$aryData["strButton"]	.= '<span id="SearchNaviBt' . $aryNaviCode[1] . '" ' . $strVisibility2 . ' onclick="top.location=\'/pc/search/index.php?strSessionID=' . $aryData["strSessionID"] . '\';" ' . $aryDisabled[1] . '></span>';
			$aryData["strButton"]	.= '<span id="DataExNaviBt' . $aryNaviCode[2] . '" ' . $strVisibility3 . ' onclick="top.location=\'/dataex/index.php?strSessionID=' . $aryData["strSessionID"] . '\';" ' . $aryDisabled[2] . '></span>';

			$aryData["strButton"]	.= '<span id="RegistNaviBtA" ' . $strVisibility4 . ' onclick="top.location=\'/po/regist/index.php?strSessionID=' . $aryData["strSessionID"] . '\';"></span>';
			$aryData["strButton"]	.= '<span id="SearchNaviBtA" ' . $strVisibility5 . ' onclick="top.location=\'/po/search/index.php?strSessionID=' . $aryData["strSessionID"] . '\';"></span>';
			break;


		// ����ե�
		case "wf":
			//if( !fncCheckAuthority( DEF_FUNCTION_WF1, $objAuth ) ) break;

			$strVisibility1	= ( !fncCheckAuthority( DEF_FUNCTION_WF2, $objAuth ) )	? 'style="visibility:hidden"' : '';

			$strVisibility2	= ( !fncCheckAuthority( DEF_FUNCTION_P1, $objAuth ) )	? 'style="visibility:hidden"' : '';
			$strVisibility3	= ( !fncCheckAuthority( DEF_FUNCTION_P2, $objAuth ) )	? 'style="visibility:hidden"' : '';

			$strVisibility4	= ( !fncCheckAuthority( DEF_FUNCTION_E1, $objAuth ) )	? 'style="visibility:hidden"' : '';
			$strVisibility5	= ( !fncCheckAuthority( DEF_FUNCTION_E2, $objAuth ) )	? 'style="visibility:hidden"' : '';

			$strVisibility6	= ( !fncCheckAuthority( DEF_FUNCTION_SO1, $objAuth ) )	? 'style="visibility:hidden"' : '';
			$strVisibility7	= ( !fncCheckAuthority( DEF_FUNCTION_SO2, $objAuth ) )	? 'style="visibility:hidden"' : '';

			$strVisibility8	= ( !fncCheckAuthority( DEF_FUNCTION_PO1, $objAuth ) )	? 'style="visibility:hidden"' : '';
			$strVisibility9	= ( !fncCheckAuthority( DEF_FUNCTION_PO2, $objAuth ) )	? 'style="visibility:hidden"' : '';

			$aryData["strButton"]	 = '<span id="RegistNaviBt' . $aryNaviCode[0] . '" onclick="top.location=\'/wf/list/index.php?strSessionID=' . $aryData["strSessionID"] . '\';" ' . $aryDisabled[0] . '></span>';
			$aryData["strButton"]	.= '<span id="SearchNaviBt' . $aryNaviCode[1] . '" ' . $strVisibility1 . ' onclick="top.location=\'/wf/search/index.php?strSessionID=' . $aryData["strSessionID"] . '\';" ' . $aryDisabled[1] . '></span>';

			$aryData["strButton"]	.= '<span id="RegistNaviBtA" ' . $strVisibility2 . ' onclick="top.location=\'/p/regist/index.php?strSessionID=' . $aryData["strSessionID"] . '\';"></span>';
			$aryData["strButton"]	.= '<span id="SearchNaviBtA" ' . $strVisibility3 . ' onclick="top.location=\'/p/search/index.php?strSessionID=' . $aryData["strSessionID"] . '\';"></span>';

			$aryData["strButton"]	.= '<span id="RegistNaviBtAA" ' . $strVisibility4 . ' onclick="top.location=\'/estimate/regist/edit.php?strSessionID=' . $aryData["strSessionID"] . '&lngFunctionCode=1501\';"></span>';
			$aryData["strButton"]	.= '<span id="SearchNaviBtAA" ' . $strVisibility5 . ' onclick="top.location=\'/estimate/search/index.php?strSessionID=' . $aryData["strSessionID"] . '\';"></span>';

			$aryData["strButton"]	.= '<span id="RegistNaviBtAAA" ' . $strVisibility6 . ' onclick="top.location=\'/so/regist/index.php?strSessionID=' . $aryData["strSessionID"] . '\';"></span>';
			$aryData["strButton"]	.= '<span id="SearchNaviBtAAA" ' . $strVisibility7 . ' onclick="top.location=\'/so/search/index.php?strSessionID=' . $aryData["strSessionID"] . '\';"></span>';

			$aryData["strButton"]	.= '<span id="RegistNaviBtAAAA" ' . $strVisibility8 . ' onclick="top.location=\'/po/regist/index.php?strSessionID=' . $aryData["strSessionID"] . '\';"></span>';
			$aryData["strButton"]	.= '<span id="SearchNaviBtAAAA" ' . $strVisibility9 . ' onclick="top.location=\'/po/search/index.php?strSessionID=' . $aryData["strSessionID"] . '\';"></span>';
			break;


		// Ģɼ����
		case "list":
			//if( !fncCheckAuthority( DEF_FUNCTION_LO0, $objAuth ) ) break;

			$aryData["strButton"]	 = '<span id="RegistNaviBt' . $aryNaviCode[0] . '" onclick="top.location=\'/list/index.php?strSessionID=' . $aryData["strSessionID"] . '\';" ' . $aryDisabled[0] . '></span>';
			break;


		// �ǡ����������ݡ���
		case "dataex":
			//if( !fncCheckAuthority( DEF_FUNCTION_DE0, $objAuth ) ) break;

			$aryData["strButton"]	 = '<span id="RegistNaviBt' . $aryNaviCode[0] . '" onclick="top.location=\'/dataex/index.php?strSessionID=' . $aryData["strSessionID"] . '\';" ' . $aryDisabled[0] . '></span>';
			break;


		// �ⷿ����
		case "mm":

			$strVisibility1	= ( !fncCheckAuthority( DEF_FUNCTION_MM1, $objAuth ) )	? 'style="visibility:hidden"' : '';
			$strVisibility2	= ( !fncCheckAuthority( DEF_FUNCTION_MM2, $objAuth ) )	? 'style="visibility:hidden"' : '';
			$strVisibility3	= ( !fncCheckAuthority( DEF_FUNCTION_LO0, $objAuth ) )	? 'style="visibility:hidden"' : '';

			$strVisibility4	= ( !fncCheckAuthority( DEF_FUNCTION_MR1, $objAuth ) )	? 'style="visibility:hidden"' : '';
			$strVisibility5	= ( !fncCheckAuthority( DEF_FUNCTION_MR2, $objAuth ) )	? 'style="visibility:hidden"' : '';

			$aryData["strButton"]	 = '<span id="RegistNaviBt' . $aryNaviCode[0] . '" ' . $strVisibility1 . ' onclick="top.location=\'/mm/regist/index.php?strSessionID=' . $aryData["strSessionID"] . '\';" ' . $aryDisabled[0] . '></span>';
			$aryData["strButton"]	.= '<span id="SearchNaviBt' . $aryNaviCode[1] . '" ' . $strVisibility2 . ' onclick="top.location=\'/mm/search/index.php?strSessionID=' . $aryData["strSessionID"] . '\';" ' . $aryDisabled[1] . '></span>';
			$aryData["strButton"]	.= '<span id="ListExNaviBt' . $aryNaviCode[2] . '" ' . $strVisibility3 . ' onclick="top.location=\'/list/index.php?strSessionID=' . $aryData["strSessionID"] . '\';" ' . $aryDisabled[2] . '></span>';

			$aryData["strButton"]	.= '<span id="RegistNaviBtA"' . $strVisibility4 . ' onclick="top.location=\'/mr/regist/index.php?strSessionID=' . $aryData["strSessionID"] . '\';" ' . $aryDisabled[3] . '></span>';
			$aryData["strButton"]	.= '<span id="SearchNaviBtA"' . $strVisibility5 . ' onclick="top.location=\'/mr/search/index.php?strSessionID=' . $aryData["strSessionID"] . '\';" ' . $aryDisabled[4] . '></span>';

			break;

		// �ⷿĢɼ����
		case "mr":

			$strVisibility1	= ( !fncCheckAuthority( DEF_FUNCTION_MR1, $objAuth ) )	? 'style="visibility:hidden"' : '';
			$strVisibility2	= ( !fncCheckAuthority( DEF_FUNCTION_MR2, $objAuth ) )	? 'style="visibility:hidden"' : '';
			$strVisibility3	= ( !fncCheckAuthority( DEF_FUNCTION_LO0, $objAuth ) )	? 'style="visibility:hidden"' : '';

			$strVisibility4	= ( !fncCheckAuthority( DEF_FUNCTION_MM1, $objAuth ) )	? 'style="visibility:hidden"' : '';
			$strVisibility5	= ( !fncCheckAuthority( DEF_FUNCTION_MM2, $objAuth ) )	? 'style="visibility:hidden"' : '';

			$aryData["strButton"]	 = '<span id="RegistNaviBt' . $aryNaviCode[0] . '" ' . $strVisibility1 . ' onclick="top.location=\'/mr/regist/index.php?strSessionID=' . $aryData["strSessionID"] . '\';" ' . $aryDisabled[0] . '></span>';
			$aryData["strButton"]	.= '<span id="SearchNaviBt' . $aryNaviCode[1] . '" ' . $strVisibility2 . ' onclick="top.location=\'/mr/search/index.php?strSessionID=' . $aryData["strSessionID"] . '\';" ' . $aryDisabled[1] . '></span>';
			$aryData["strButton"]	.= '<span id="ListExNaviBt' . $aryNaviCode[2] . '" ' . $strVisibility3 . ' onclick="top.location=\'/list/index.php?strSessionID=' . $aryData["strSessionID"] . '\';" ' . $aryDisabled[2] . '></span>';

			$aryData["strButton"]	.= '<span id="RegistNaviBtA"' . $strVisibility4 . ' onclick="top.location=\'/mm/regist/index.php?strSessionID=' . $aryData["strSessionID"] . '\';"></span>';
			$aryData["strButton"]	.= '<span id="SearchNaviBtA"' . $strVisibility5 . ' onclick="top.location=\'/mm/search/index.php?strSessionID=' . $aryData["strSessionID"] . '\';"></span>';

			break;

		// �桼��������
		case "uc":
			//if( !fncCheckAuthority( DEF_FUNCTION_UC1, $objAuth ) ) break;

			$strVisibility1	= ( !fncCheckAuthority( DEF_FUNCTION_UC2, $objAuth ) )	? 'style="visibility:hidden"' : '';
			$strVisibility2	= ( !fncCheckAuthority( DEF_FUNCTION_UC3, $objAuth ) )	? 'style="visibility:hidden"' : '';

			$aryData["strButton"]	 = '<span id="UserInfoNaviBt' . $aryNaviCode[0] . '" onclick="top.location=\'/uc/regist/edit.php?strSessionID=' . $aryData["strSessionID"] . '&lngFunctionCode=' . DEF_FUNCTION_UC1 . '\';" ' . $aryDisabled[0] . '></span>';
			$aryData["strButton"]	 = '<span id="RegistNaviBt' . $aryNaviCode[1] . '" ' . $strVisibility1 . ' onclick="top.location=\'/uc/regist/edit.php?strSessionID=' . $aryData["strSessionID"] . '&lngFunctionCode=' . DEF_FUNCTION_UC2 . '\';" ' . $aryDisabled[1] . '></span>';
			$aryData["strButton"]	 = '<span id="SearchNaviBt' . $aryNaviCode[2] . '" ' . $strVisibility2 . ' onclick="top.location=\'/uc/search/index.php?strSessionID=' . $aryData["strSessionID"] . '&lngFunctionCode=' . DEF_FUNCTION_UC3 . '\';" ' . $aryDisabled[2] . '></span>';
			break;


		// �ޥ���������
		case "m":
			//if( !fncCheckAuthority( DEF_FUNCTION_M0, $objAuth ) ) break;

			$aryData["strButton"]	 = '<span id="RegistNaviBt' . $aryNaviCode[0] . '" onclick="top.location=\'/m/list/index.php?strSessionID=' . $aryData["strSessionID"] . '\';" ' . $aryDisabled[0] . '></span>';
			$aryData["strButton"]	.= '<span id="SearchNaviBt' . $aryNaviCode[1] . '" onclick="top.location=\'/m/search/index.php?strSessionID=' . $aryData["strSessionID"] . '\';" ' . $aryDisabled[1] . '></span>';
			break;


		// �����ƥ����
		case "sysc":
			//if( !fncCheckAuthority( DEF_FUNCTION_SYS0, $objAuth ) ) break;

			$strVisibility1	= ( !fncCheckAuthority( DEF_FUNCTION_SYS1, $objAuth ) )	? 'style="visibility:hidden"' : '';
			$strVisibility2	= ( !fncCheckAuthority( DEF_FUNCTION_SYS2, $objAuth ) )	? 'style="visibility:hidden"' : '';
			$strVisibility3	= ( !fncCheckAuthority( DEF_FUNCTION_SYS3, $objAuth ) )	? 'style="visibility:hidden"' : '';
			$strVisibility4	= ( !fncCheckAuthority( DEF_FUNCTION_SYS4, $objAuth ) )	? 'style="visibility:hidden"' : '';

			$aryData["strButton"]	 = '<span id="MessageBt' . $aryNaviCode[0] . '" ' . $strVisibility1 . ' onclick="top.location=\'/sysc/inf/index.php?strSessionID=' . $aryData["strSessionID"] . '\';" ' . $aryDisabled[0] . '></span>';
			$aryData["strButton"]	.= '<span id="EmailBt' . $aryNaviCode[1] . '" ' . $strVisibility2 . ' onclick="top.location=\'/sysc/mail/index.php?strSessionID=' . $aryData["strSessionID"] . '\';" ' . $aryDisabled[1] . '></span>';
			$aryData["strButton"]	.= '<span id="SessionBt' . $aryNaviCode[2] . '" ' . $strVisibility3 . ' onclick="top.location=\'/sysc/session/index.php?strSessionID=' . $aryData["strSessionID"] . '\';" ' . $aryDisabled[2] . '></span>';
			$aryData["strButton"]	.= '<span id="ServerBt' . $aryNaviCode[3] . '" ' . $strVisibility4 . ' onclick="top.location=\'/sysc/sev/index.php?strSessionID=' . $aryData["strSessionID"] . '\';" ' . $aryDisabled[3] . '></span>';
			break;


		// �������
		case "closed":
			//if( !fncCheckAuthority( DEF_FUNCTION_CLD0, $objAuth ) ) break;

			$aryData["strButton"]	 = '<span id="ClosedBt' . $aryNaviCode[0] . '" onclick="top.location=\'/closed/index.php?strSessionID=' . $aryData["strSessionID"] . '\';" ' . $aryDisabled[0] . '></span>';
			break;


		default:
			break;
	}
	//-------------------------------------------------------------------------


//fncDebug( 'navi.txt', $aryData, __FILE__, __LINE__ );


	//-------------------------------------------------------------------------
	// �ƥ�ץ졼���ɤ߹���
	$objTemplate->getTemplate( "/navi/parts.tmpl" );

	// �ƥ�ץ졼������
	$objTemplate->replace( $aryData );
	$objTemplate->complete();

	// HTML����
	echo $objTemplate->strTemplate;
	//-------------------------------------------------------------------------

	return true;

?>
