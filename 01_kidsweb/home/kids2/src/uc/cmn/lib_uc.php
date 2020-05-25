<?
/** 
*	ユーザー管理用ライブラリ
*
*	ユーザー管理用関数ライブラリ
*
*	@package   KIDS
*	@license   http://www.wiseknot.co.jp/ 
*	@copyright Copyright &copy; 2003, Wiseknot 
*	@author    Kenji Chiba <k-chiba@wiseknot.co.jp> 
*	@access    public
*	@version   1.00
*
*/



/**
* ユーザー管理
*
*	ユーザーデータ読み込み、検索、詳細情報取得クエリ関数
*
*	@param  String $lngUserCode ユーザーコード
*	@param  Array  $aryData     FORMデータ
*	@param  Object $objDB       DBオブジェクト
*	@access public
*/
function getUserQuery( $lngUserCode, $aryData, $objDB )
{
	$bytInvalidFlag         = $aryData['bytInvalidFlag'];
	$lngUserCode            = $aryData['lngUserCode'];
	$strUserID              = $aryData['strUserID'];
	$strMailAddress         = $aryData['strMailAddress'];
	$bytMailTransmitFlag    = $aryData['bytMailTransmitFlag'];
	$bytUserDisplayFlag     = $aryData['bytUserDisplayFlag'];
	$strUserDisplayCode     = $aryData['strUserDisplayCode'];
	$strUserDisplayName     = $aryData['strUserDisplayName'];
	$strUserFullName        = $aryData['strUserFullName'];
	$lngCompanyCode         = $aryData['lngCompanyCode'];
	$lngGroupCode           = $aryData['lngGroupCode'];
	$lngAuthorityGroupCode  = $aryData['lngAuthorityGroupCode'];
	$lngAccessIPAddressCode = $aryData['lngAccessIPAddressCode'];
	$strNote                = $aryData['strNote'];
	$lngFunctionCode        = $aryData['lngFunctionCode'];
	$strSort                = $aryData['strSort'];

	// ソートするカラムの対象番号設定
	$arySortColumn = array ( 1 => "u.bytInvalidFlag",
	                         2 => "u.lngUserCode",
	                         3 => "u.strUserID",
	                         4 => "u.strMailAddress",
	                         5 => "u.bytMailTransmitFlag",
	                         6 => "u.bytUserDisplayFlag",
	                         7 => "u.strUserDisplayCode",
	                         8 => "u.strUserDisplayName",
	                         9 => "u.strUserFullName",
	                        10 => "u.lngCompanyCode",
	                        11 => "g.lngGroupCode",
	                        12 => "u.lngAuthorityGroupCode",
	                        13 => "u.lngAccessIPAddressCode",
	                        14 => "u.strNote" );

	//////////////////////////////////////////////////////////////////////////
	// 取得項目
	//////////////////////////////////////////////////////////////////////////
	$strQuery = "SELECT\n" .
                " u.bytInvalidFlag, u.lngUserCode," .
                " trim( trailing from u.strUserID ) AS strUserID,\n" .
                " u.strMailAddress, u.bytMailTransmitFlag,\n" .
                " u.bytUserDisplayFlag, u.strUserDisplayCode,\n" .
                " u.strUserDisplayName, u.strUserFullName,\n" .
                " c.lngCompanyCode, c.strCompanyDisplayCode,\n" .
                " c.strCompanyName, g.lngGroupCode, g.strGroupDisplayCode,\n" .
                " g.strGroupName, gr.bytDefaultFlag,\n" .
                " ag.lngAuthorityGroupCode, ag.strAuthorityGroupName,\n" .
                " ip.lngAccessIPAddressCode, ip.strAccessIPAddress,\n" .
                " u.strNote, g.strGroupDisplayColor, u.strUserImageFileName\n";

	$strQuery .= "FROM m_User u, m_Company c, m_Group g, m_GroupRelation gr, m_AuthorityGroup ag, m_AccessIPAddress ip \n" .
                 "WHERE";

	//////////////////////////////////////////////////////////////////////////
	// 条件
	//////////////////////////////////////////////////////////////////////////
	// 一覧            条件式 
	// 検索            条件式       B
	// 詳細・処理(一覧)条件式 A
	// 詳細・処理(検索)条件式 A and B
	//////////////////////////////////////////////////////////////////////////
	// A:指定したユーザーコード
	// B:各検索条件

	// A:指定したユーザーコード
	if ( $aryData["lngUserCodeConditions"] && $lngUserCode != "" )
	{
		$strQuery .= " AND u.lngUserCode = $lngUserCode \n";
	}

	// B:各検索条件
	// ログイン許可
	if ( $aryData["bytInvalidFlagConditions"] && $bytInvalidFlag )
	{
		$strQuery .= " AND u.bytInvalidFlag = $bytInvalidFlag \n";
	}

	// ユーザーID
	if ( $aryData["strUserIDConditions"] && $strUserID != "" )
	{
		$strQuery .= " AND u.strUserID LIKE '%$strUserID%' \n";
	}

	// メールアドレス
	if ( $aryData["strMailAddressConditions"] && $strMailAddress != "" )
	{
		$strQuery .= " AND u.strMailAddress LIKE '%$strMailAddress%' \n";
	}

	// メール配信許可
	if ( $aryData["bytMailTransmitFlagConditions"] && $bytMailTransmitFlag )
	{
		$strQuery .= " AND u.bytMailTransmitFlag = $bytMailTransmitFlag \n";
	}

	// 表示ユーザーフラグ
	if ( $aryData["bytUserDisplayFlagConditions"] && $bytUserDisplayFlag )
	{
		$strQuery .= " AND u.bytUserDisplayFlag = $bytUserDisplayFlag \n";
	}

	// 表示ユーザーコード
	if ( $aryData["strUserDisplayCodeConditions"] && $strUserDisplayCode != "" )
	{
		$strQuery .= " AND u.strUserDisplayCode LIKE '%$strUserDisplayCode%' \n";
	}

	// 表示ユーザー名
	if ( $aryData["strUserDisplayNameConditions"] && $strUserDisplayName != "" )
	{
		$strQuery .= " AND sf_translate_case(u.strUserDisplayName) LIKE '%' || sf_translate_case('$strUserDisplayName') ||  '%' \n";
	}

	// フルネーム
	if ( $aryData["strUserFullNameConditions"] && $strUserFullName != "" )
	{
		$strQuery .= " AND sf_translate_case(u.strUserFullName) LIKE '%' || sf_translate_case('$strUserFullName') || '%' \n";
	}

	// 企業コード
	if ( $aryData["lngCompanyCodeConditions"] && $lngCompanyCode != "" )
	{
		$strQuery .= " AND c.lngCompanyCode = $lngCompanyCode \n";
	}

	// グループコード
	if ( $aryData["lngGroupCodeConditions"] && $lngGroupCode != "" )
	{
		$strQuery .= " AND g.lngGroupCode = $lngGroupCode \n";
	}
	elseif ( $lngFunctionCode == DEF_FUNCTION_UC3 )
	{
		$strQuery .= " AND gr.bytDefaultFlag = TRUE \n";
	}

	// 権限グループコード
	if ( $aryData["lngAuthorityGroupCodeConditions"] && $lngAuthorityGroupCode != "" )
	{
		$strQuery .= " AND ag.lngAuthorityGroupCode = $lngAuthorityGroupCode \n";
	}

	// アクセスIPアドレス
	if ( $aryData["lngAccessIPAddressCodeConditions"] && $lngAccessIPAddressCode != "" )
	{
		$strQuery .= " AND ip.lngAccessIPAddressCode = $lngAccessIPAddressCode \n";
	}

	//////////////////////////////////////////////////////////////////////////
	// 紐付け
	//////////////////////////////////////////////////////////////////////////
	// m_User u, m_Company c, m_Group g, m_GroupRelation gr
	// m_AuthorityGroup ag, m_AccessIPAddress ip
	$strQuery .= " AND u.lngCompanyCode = c.lngCompanyCode\n" .
                 " AND u.lngAuthorityGroupCode = ag.lngAuthorityGroupCode\n" .
                 " AND u.lngAccessIPAddressCode = ip.lngAccessIPAddressCode\n" .
                 " AND u.lngUserCode = gr.lngUserCode\n" .
                 " AND g.lngGroupCode = gr.lngGroupCode\n";

	//////////////////////////////////////////////////////////////////////////
	// ソート処理
	//////////////////////////////////////////////////////////////////////////
	// $strSort 構造 "sort_[対象番号]_[降順・昇順]"
	// $strSort から対象番号、降順・昇順を取得
	list ( $sort, $column, $DESC ) = explode ( "_", $strSort );
	if ( $column )
	{
		$strQuery .= "ORDER BY $arySortColumn[$column] $DESC, u.lngUserCode ASC\n";
	}

	// ユーザー詳細、ユーザー修正の場合、デフォルトグループにてソート
	elseif ( $lngFunctionCode == DEF_FUNCTION_UC4 || $lngFunctionCode == DEF_FUNCTION_UC5 )
	{
		$strQuery .= "ORDER BY gr.bytDefaultFlag ASC\n";
	}

	else
	{
		$strQuery .= "ORDER BY u.lngUserCode ASC\n";
	}
	$strQuery = preg_replace ( "/WHERE AND/", "WHERE", $strQuery );

// echo $strQuery;
	//////////////////////////////////////////////////////////////////////////
	// クエリ実行
	//////////////////////////////////////////////////////////////////////////
	list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );

	//$lngResultNum = pg_Num_Rows( $lngResultID );
	$lngResultNum = pg_Num_Rows( $lngResultID );
	if ( !$lngResultNum )
	{
		$strErrorMessage = fncOutputError( 1107, DEF_WARNING, "", FALSE, "/wf/index.php?strSessionID=" . $aryData["strSessionID"], $objDB );
		$strErrorMessage = "<table cellpadding=\"0\" cellspacing=\"1\" border=\"0\" bgcolor=\"#6f818\"><tr bgcolor=\"#FFFFFF\"><th>" . $strErrorMessage . "</th></tr></table>";
	}

	return array ( $lngResultID, $lngResultNum, $strErrorMessage );
}



/**
* GETデータ引数URL生成関数
*
*	@param  Array  $aryData GETデータ
*	@return String          URL(**.php?・・・以降の文字列)
*	@access public
*/
function fncGetURL( $aryData )
{
	$url = "strSessionID=" .$aryData["strSessionID"] .
           "&lngFunctionCode=" .$aryData["lngFunctionCode"] .
           "&lngUserCode=" .$aryData["lngUserCode"] .
           "&bytInvalidFlag=" .$aryData["bytInvalidFlag"] .
           "&strUserID=" .$aryData["strUserID"] .
           "&strMailAddress=" .$aryData["strMailAddress"] .
           "&bytMailTransmitFlag=" .$aryData["bytMailTransmitFlag"] .
           "&strUserDisplayCode=" .$aryData["strUserDisplayCode"] .
           "&strUserDisplayName=" .$aryData["strUserDisplayName"] .
           "&strUserFullName=" .$aryData["strUserFullName"] .
           "&lngCompanyCode=" .$aryData["lngCompanyCode"] .
           "&lngGroupCode=" .$aryData["lngGroupCode"] .
           "&lngAuthorityGroupCode=" .$aryData["lngAuthorityGroupCode"] .
           "&lngAccessIPAddressCode=" .$aryData["lngAccessIPAddressCode"] .
           "&strNote=" .$aryData["strNote"];

	if ( $aryData["lngFunctionCode"] == DEF_FUNCTION_UC3 )
	{
		$url .= "&detailVisible=" .$aryData["detailVisible"] .
                "&bytInvalidFlagVisible=" .$aryData["bytInvalidFlagVisible"] .
                "&lngUserCodeVisible=" .$aryData["lngUserCodeVisible"] .
                "&strUserIDVisible=" .$aryData["strUserIDVisible"] .
                "&strMailAddressVisible=" .$aryData["strMailAddressVisible"] .
                "&bytMailTransmitFlagVisible=" .$aryData["bytMailTransmitFlagVisible"] .
                "&bytUserDisplayFlagVisible=" .$aryData["bytUserDisplayFlagVisible"] .
                "&strUserDisplayCodeVisible=" .$aryData["strUserDisplayCodeVisible"] .
                "&strUserDisplayNameVisible=" .$aryData["strUserDisplayNameVisible"] .
                "&strUserFullNameVisible=" .$aryData["strUserFullNameVisible"] .
                "&lngCompanyCodeVisible=" .$aryData["lngCompanyCodeVisible"] .
                "&lngGroupCodeVisible=" .$aryData["lngGroupCodeVisible"] .
                "&lngAuthorityGroupCodeVisible=" .$aryData["lngAuthorityGroupCodeVisible"] .
                "&lngAccessIPAddressCodeVisible=" .$aryData["lngAccessIPAddressCodeVisible"] .
                "&strNoteVisible=" .$aryData["strNoteVisible"] .
                "&updateVisible=" .$aryData["updateVisible"] .

                "&bytInvalidFlagConditions=" .$aryData["bytInvalidFlagConditions"] .
                "&lngUserCodeConditions=" .$aryData["lngUserCodeConditions"] .
                "&strUserIDConditions=" .$aryData["strUserIDConditions"] .
                "&strMailAddressConditions=" .$aryData["strMailAddressConditions"] .
                "&bytMailTransmitFlagConditions=" .$aryData["bytMailTransmitFlagConditions"] .
                "&bytUserDisplayFlagConditions=" .$aryData["bytUserDisplayFlagConditions"] .
                "&strUserDisplayCodeConditions=" .$aryData["strUserDisplayCodeConditions"] .
                "&strUserDisplayNameConditions=" .$aryData["strUserDisplayNameConditions"] .
                "&strUserFullNameConditions=" .$aryData["strUserFullNameConditions"] .
                "&lngCompanyCodeConditions=" .$aryData["lngCompanyCodeConditions"] .
                "&lngGroupCodeConditions=" .$aryData["lngGroupCodeConditions"] .
                "&lngAuthorityGroupCodeConditions=" .$aryData["lngAuthorityGroupCodeConditions"] .
                "&lngAccessIPAddressCodeConditions=" .$aryData["lngAccessIPAddressCodeConditions"] .
                "&strNoteConditions=" .$aryData["strNoteConditions"];
	}
	return $url;
}



function checkUniqueUser( $lngUserCode, $strUserID, $lngCompanyCode, $strUserDisplayCode, $lngUserCodeOriginal, $strUserIDOriginal, $lngCompanyCodeOriginal, $strUserDisplayCodeOriginal, $mode, $objDB )
{
	$aryError["lngUserCode"]        = "visibility:hidden;";
	$aryError["strUserID"]          = "visibility:hidden;";
	$aryError["strUserDisplayCode"] = "visibility:hidden;";

	// 更新ではない または ユーザーコードが変わった
	if ( $mode != "UPDATE" || $lngUserCode != $lngUserCodeOriginal )
	{
		// ユーザーコードの重複チェック
		$strQuery  = "SELECT lngUserCode FROM m_User " .
	                 "WHERE lngUserCode = $lngUserCode";
//echo $strQuery;
		list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
		if ( $lngResultNum > 0 )
		{
			$bytErrorFlag = 1;
			$aryError["lngUserCode"]   = "visibility:visible;";
			$aryMessage["lngUserCode"] = "ユーザーが重複しています。";
			$objDB->freeResult( $lngResultID );
		}
	}

	// 更新ではない または ユーザーIDが変わった
	if ( $mode != "UPDATE" || $strUserID != $strUserIDOriginal )
	{
		// ユーザーIDの重複チェック
		$strQuery  = "SELECT lngUserCode FROM m_User " .
	                 "WHERE strUserID = '$strUserID'";

		list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
		if ( $lngResultNum > 0 )
		{
			$bytErrorFlag = 1;
			$aryError["strUserID"]   = "visibility:visible;";
			$aryMessage["strUserID"] = "ユーザーが重複しています。";
			$objDB->freeResult( $lngResultID );
		}
	}

	// 更新ではない または ユーザー表示コードが変わった
	if ( $mode != "UPDATE" || $strUserDisplayCode != $strUserDisplayCodeOriginal || $lngCompanyCode != $lngCompanyCodeOriginal )
	{
		// 所属する会社内に同じ表示コードの者(自分以外)がいた場合エラー
		$strQuery = "SELECT lngUserCode FROM m_User " .
	                "WHERE strUserDisplayCode = '$strUserDisplayCode'" .
	                " AND lngCompanyCode = $lngCompanyCode\n" .
	                " AND lngUserCode != $lngUserCode";

		list ( $lngResultID, $lngResultNum ) = fncQuery( $strQuery, $objDB );
		if ( $lngResultNum > 0 )
		{
			$bytErrorFlag = 1;
			$aryError["strUserDisplayCode"]   = "visibility:visible;";
			$aryMessage["strUserDisplayCode"] = "ユーザーが重複しています。";
			$objDB->freeResult( $lngResultID );
		}
	}

	return array ( $bytErrorFlag, $aryError, $aryMessage );
}



return TRUE;
?>
