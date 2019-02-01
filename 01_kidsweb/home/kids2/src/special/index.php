<?php



	include_once( 'conf.inc' );
	require( LIB_FILE );



	// ���֥�����������
	$objDB       = new clsDB();
	$objAuth     = new clsAuth();
	$objTemplate = new clsTemplate();


	// DB���ͥ���
	$objDB->open( "", "", "", "" );



	// ���Фν����
	$aryQuery  = array();
	$strQuery  = '';
	$aryResult = array();
	$aryHTML   = array();
	$strHTML   = '';
	$aryData   = array();



	$aryQuery   = array();
	$aryQuery[] = "SELECT";
	$aryQuery[] = "mc.strcharactarcode,";
	$aryQuery[] = "mc.lngordercode,";
	$aryQuery[] = "mc.strkeyword";
	$aryQuery[] = "FROM";
	$aryQuery[] = "m_charactar mc";
	$aryQuery[] = "WHERE";
	$aryQuery[] = "mc.blninvalidflag = false";
	$aryQuery[] = "ORDER BY";
	$aryQuery[] = "mc.lngordercode";

	$strQuery = implode( "\n", $aryQuery );



	// ������¹�
	list( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	for( $i = 0; $i < $lngResultNum; $i++ )
	{
		if( $lngResultNum )
		{
			// �ǡ��������
			$aryResult[$i] = $objDB->fetchArray( $lngResultID, $i );
		}
		else
		{
			fncOutputError( 9051, DEF_ERROR, "", TRUE, "", $objDB );
		}
	}

	// ���ID�����
	$objDB->freeResult( $lngResultID );



	for( $i = 0; $i < count( $aryResult ); $i ++ )
	{
		$aryHTML[] = '<td width="10" class="SpecialTdStyle" onmouseover="fncTdColorChange( \'on\', this );" onmouseout="fncTdColorChange( \'off\', this );" onmousedown="fncTdColorChange( \'down\', this );" onmouseup="fncTdColorChange( \'off\', this ); fncSpecialCharCopy( \'' . $aryResult[$i]["strkeyword"] . '\' );"><a href="javascript:void( 0 );">' . $aryResult[$i]["strkeyword"] . '</a></td>';
	}

	$strHTML = implode( "\n\n", $aryHTML );

	$aryData["strKeyWords"] = $strHTML;



	// DB������
	$objDB->close();



	//require( LIB_DEBUGFILE );
	//fncDebug( 'keywords.txt', $aryData, __FILE__, __LINE__);



	// ����
	$objTemplate->getTemplate( "/special/parts.tmpl" );

	$objTemplate->replace( $aryData );
	$objTemplate->complete();
	echo $objTemplate->strTemplate;


	return TRUE;
?>
