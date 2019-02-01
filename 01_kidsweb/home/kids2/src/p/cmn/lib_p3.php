<?php
	
	// 製品名（英語）の類似検索
	function fncNearName ( $strPostData,$lngInChargeGroupCode, $strProductCode, $objDB )
	{

		// スペースから前文字を取る
		$aryNearName = split(" |　", $strPostData );
		
		
		// 「未定」を引くDEFAINではなくDBに入っている
		$aryQueryNull[] = "SELECT ";
		$aryQueryNull[] = "strvalue ";
		$aryQueryNull[] = "FROM m_commonfunction ";
		$aryQueryNull[] = "WHERE strclass = 'productnull'";
		
		$strQueryNull = implode("\n", $aryQueryNull );
		
		list ( $lngResultID, $lngResultNum ) = fncQuery( $strQueryNull, $objDB );

		if ( $lngResultNum )
		{
			$aryResultNull = $objDB->fetchArray( $lngResultID, 0 );
		}
		$objDB->freeResult( $lngResultID );
		
		// 製品名（英語）の類似検索開始
		$aryQueryNearName[] = "SELECT " ;
		$aryQueryNearName[] = "lngproductno, ";
		$aryQueryNearName[] = "strproductcode, ";
		$aryQueryNearName[] = "strproductenglishname ";
		$aryQueryNearName[] = "FROM ";
		$aryQueryNearName[] = "m_product ";
		$aryQueryNearName[] = "WHERE "; 
		$aryQueryNearName[] = "strproductenglishname ~* ";
		$aryQueryNearName[] = "'".$aryNearName[0]."' AND";
		$aryQueryNearName[] = "strproductenglishname != '".$aryResutNull["strvalue"]."' AND ";
		$aryQueryNearName[] = "lnginchargegroupcode = $lngInChargeGroupCode AND ";
		if ( $strProductCode != "" )
		{
			$aryQueryNearName[] = "strproductcode != '$strProductCode' AND ";
		}
		$aryQueryNearName[] = "bytinvalidflag = false ";
		$aryQueryNearName[] = "ORDER BY strproductcode";
		
		$strQueryNearName = implode("\n", $aryQueryNearName );
		// echo "<br>$strQueryNearName<br>";
		
		if ( !$lngResultID = $objDB->execute( $strQueryNearName ) )
		{
			fncOutputError ( 9051, DEF_ERROR, "", TRUE, "", $objDB );
			$objDB->close();
			return true;
		}
		
		
		if( $lngResultNum = pg_num_rows( $lngResultID ) )
		{
			for( $i = 0; $i < $lngResultNum; $i++ )
			{
				$aryResutNearName[] = pg_fetch_array( $lngResultID, $i, PGSQL_ASSOC );
			}
		
			$aryOptionValue[] = "<table width=\"100%\" cellpadding=\"0\" cellspacing=\"1\" border=\"0\" bgcolor=\"#6f8180\" align=\"center\">";
			$aryOptionValue[] = "<tr><td id=\"SimilarProducts\" class=\"SegColumn2\" width=\"25%\">類似製品</td>";
			$aryOptionValue[] = "<td class=\"Segs\"><select class=\"Slt100P\" name=\"NearName\" size=\"7\">";
			
			for( $i=0; $i<count($aryResutNearName); $i++ )
			{
				 $aryOptionValue[] = "<option value=\"".$aryResutNearName[$i]["lngproductno"]."\">".$aryResutNearName[$i]["strproductcode"]." : ".$aryResutNearName[$i]["strproductenglishname"]."   </option>";
			}
			$aryOptionValue[] = "</select></td></tr></table><br>";
			
			$strOptionValue = implode("\n", $aryOptionValue );
			
			return $strOptionValue;
		}
		
	}
		
		

		
		