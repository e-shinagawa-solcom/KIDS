<!--


///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

// subMasterWindowType	: サブウインドウタイプ名
// strIfrmName			: サブウインドウIFRAM名(親)
// objA					: [InputA用]最初のTabindex適用オブジェクト名
// objB					: [InputB用]最初のTabindex適用オブジェクト名

////////// [TABINDEX]制御([ApplyButton]までいったら最初に戻す) //////////

function fncSubmasterTabindex( subMasterWindowType , strIfrmName , objA , objB )
{
	switch( subMasterWindowType )
	{

		////////// [VENDOR] //////////
		case 'vendor':

			if( strIfrmName.style.visibility != 'hidden' )
			{
				objA.focus();
			}

			break;

		////////// [CREATION FACTORY] //////////
		case 'creation':

			if( strIfrmName.style.visibility != 'hidden' )
			{
				objA.focus();
			}

			break;


		////////// [ASSEMBLY FACTORY] //////////
		case 'assembly':

			if( strIfrmName.style.visibility != 'hidden' )
			{
				objA.focus();
			}

			break;


		////////// [DEPT & IN CHARGE NAME] //////////
		case 'dept':

			if( strIfrmName.style.visibility != 'hidden' )
			{
				if( DeptValueFlg == 0 )
				{
					objA.focus();
				}
				else if( DeptValueFlg == 1 )
				{
					objB.focus();
				}
			}

			break;


		////////// [PRODUCTS] //////////
		case 'products':

			if( strIfrmName.style.visibility != 'hidden' )
			{
				objA.focus();
			}

			break;


		////////// [LOCATION] //////////
		case 'location':

			if( strIfrmName.style.visibility != 'hidden' )
			{
				objA.focus();
			}

			break;


		////////// [APPLICANT] //////////
		case 'applicant':

			if( strIfrmName.style.visibility != 'hidden' )
			{
				objA.focus();
			}

			break;


		////////// [WF INPUT PERSON] //////////
		case 'wfinput':

			if( strIfrmName.style.visibility != 'hidden' )
			{
				objA.focus();
			}

			break;


		////////// [VENDOR & IN CHARGE NAME] //////////
		case 'vi':

			if( strIfrmName.style.visibility != 'hidden' )
			{
				if( ViValueFlg == 0 )
				{
					objA.focus();
				}
				else if( ViValueFlg == 1 )
				{
					objB.focus();
				}
			}

			break;


		////////// [SUPPLIER] //////////
		case 'supplier':

			if( strIfrmName.style.visibility != 'hidden' )
			{
				objA.focus();
			}

			break;


		////////// [INPUT PERSON] //////////
		case 'input':

			if( strIfrmName.style.visibility != 'hidden' )
			{
				objA.focus();
			}

			break;


		default:
			break;
	}
}





///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

////////// キーイベント取得 //////////
window.document.onkeydown=onKeyDown;

function onKeyDown( e )
{
	// 要素が未定義の場合
	if (typeof(window.event.srcElement.name) == "undefined")
	{
		//BackSpaceキー押下防止
		if( window.event.keyCode == 8	) return false ;
	}

	// 要素の属性が [disabled] に対する処理
	if (window.event.srcElement.disabled == true)
	{
		//BackSpaceキー押下防止
		if( window.event.keyCode == 8	) return false ;
	}

	// 要素が [focus] の場合
	if(window.event.srcElement.type != 'text' && window.event.srcElement.focus)
	{
		//BackSpaceキー押下防止
		if( window.event.keyCode == 8	) return false ;
	}

	// [alt] + [←]キー押下防止
	if( window.event.altKey == true && window.event.keyCode == 37 ) return false ;

	// [alt] + [→]キー押下防止
	if( window.event.altKey == true && window.event.keyCode == 39 ) return false ;

	// [alt] + [c]キー 押下サブマスターウィンドウクローズ
	//if( window.event.altKey == true && window.event.keyCode == 67 )
	//{
		// タイプ別マスターウィンドウクローズモジュール呼出し
		//fncSubMasterWinClose( document.all.strSubMasterWindowType.value );
	//}

	// [ENTER]キー押下時
	if( window.event.keyCode == 13 )
	{
		// 検索・適用ボタン用イベントモジュール呼出し
		fncSubmasterQueryforEnterKey( document.all.strSubMasterWindowType.value );
		return true;
	}

}







////////// ドラックアンドドロップの禁止 //////////
window.document.ondragstart=onDragStart;

function onDragStart(e)
{
	return false;
}






////////// [shift] + [tab]キー押下防止 //////////
function fncInvalidKey()
{
	window.document.onkeydown = onKeyDownForfncInvalidKey;
}


function onKeyDownForfncInvalidKey()
{
	if(window.event.keyCode == 9 && event.shiftKey == true) return false;

	// [ENTER]キー押下時
	if( window.event.keyCode == 13 )
	{
		// 検索・適用ボタン用イベントモジュール呼出し
		fncSubmasterQueryforEnterKey( document.all.strSubMasterWindowType.value );
		return true;
	}
}


function fncEffectiveKey()
{
	window.document.onkeydown =onKeyDown;
}







///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

////////// [ENTER]キー押下時、検索・適用ボタン用イベントモジュール //////////
function fncSubmasterQueryforEnterKey( queryType )
{
	switch( queryType )
	{


		////////// [VENDOR] //////////
		case 'vendor':

			// [検索]検索ボタンにフォーカスがなく、テキストフィールドにフォーカスがある場合
			if( window.event.srcElement.name == 'lngCustomerCode' ||
				window.event.srcElement.name == 'strCustomerName' )
			{
				subLoadMasterOption( 'swCustomer',
										document.all.lngCustomerCode ,
										document.all.objCustomerName ,
										Array( document.all.lngCustomerCode.value ,
										document.all.strCustomerName.value ),
										objDataSourceSetting ,
										0 );
			}

			// [検索]検索ボタンにフォーカスがある場合
			if( window.event.srcElement.id == 'SearchButton01' )
			{
				subLoadMasterOption( 'swCustomer',
										document.all.lngCustomerCode ,
										document.all.objCustomerName ,
										Array( document.all.lngCustomerCode.value ,
										document.all.strCustomerName.value ),
										objDataSourceSetting ,
										0 );
			}

			// [適用]適用ボタンにフォーカスがなく、セレクトフィールドにフォーカスがある場合
			if( window.event.srcElement.name == 'objCustomerName' )
			{
				fncSetMasterDataParent( 'vendor' , document.all.objCustomerName.value );
			}

			// [適用]適用ボタンにフォーカスがある場合
			if( window.event.srcElement.id == 'ApplyButton' )
			{
				fncSetMasterData( 'vendor' ,
									document.all.objCustomerName ,
									document.all.lngCustomerCode ,
									document.all.strCustomerName );
			}

			break;





		////////// [CREATION FACTORY] //////////
		case 'creation':

			// [検索]検索ボタンにフォーカスがなく、テキストフィールドにフォーカスがある場合
			if( window.event.srcElement.name == 'strCreationFactoryCode' ||
				window.event.srcElement.name == 'strCreationFactoryName' )
			{
				subLoadMasterOption( 'swFactory',
										document.all.strCreationFactoryCode ,
										document.all.objCreationFactoryName ,
										Array( document.all.strCreationFactoryCode.value ,
										document.all.strCreationFactoryName.value ) ,
										objDataSourceSetting ,
										0 );
			}

			// [検索]検索ボタンにフォーカスがある場合
			if( window.event.srcElement.id == 'SearchButton01' )
			{
				subLoadMasterOption( 'swFactory',
										document.all.strCreationFactoryCode ,
										document.all.objCreationFactoryName ,
										Array( document.all.strCreationFactoryCode.value ,
										document.all.strCreationFactoryName.value ) ,
										objDataSourceSetting ,
										0 );
			}

			// [適用]適用ボタンにフォーカスがなく、セレクトフィールドにフォーカスがある場合
			if( window.event.srcElement.name == 'objCreationFactoryName' )
			{
				fncSetMasterDataParent( 'creation' , document.all.objCreationFactoryName.value );
			}

			// [適用]適用ボタンにフォーカスがある場合
			if( window.event.srcElement.id == 'ApplyButton' )
			{
				fncSetMasterDataforEnterKey( 'creation' ,
												document.all.objCreationFactoryName ,
												document.all.strCreationFactoryCode ,
												document.all.strCreationFactoryName );
			}

			break;





		////////// [ASSEMBLY FACTORY] //////////
		case 'assembly':

			// [検索]検索ボタンにフォーカスがなく、テキストフィールドにフォーカスがある場合
			if( window.event.srcElement.name == 'strFactoryCode' ||
				window.event.srcElement.name == 'strFactoryName' )
			{
				subLoadMasterOption( 'swFactory',
										document.all.strFactoryCode ,
										document.all.objFactoryName ,
										Array( document.all.strFactoryCode.value ,
										document.all.strFactoryName.value ) ,
										objDataSourceSetting ,
										0 );
			}

			// [検索]検索ボタンにフォーカスがある場合
			if( window.event.srcElement.id == 'SearchButton01' )
			{
				subLoadMasterOption( 'swFactory',
										document.all.strFactoryCode ,
										document.all.objFactoryName ,
										Array( document.all.strFactoryCode.value ,
										document.all.strFactoryName.value ) ,
										objDataSourceSetting ,
										0 );
			}

			// [適用]適用ボタンにフォーカスがなく、セレクトフィールドにフォーカスがある場合
			if( window.event.srcElement.name == 'objFactoryName' )
			{
				fncSetMasterDataParent( 'assembly' , document.all.objFactoryName.value );
			}

			// [適用]適用ボタンにフォーカスがある場合
			if( window.event.srcElement.id == 'ApplyButton' )
			{
				fncSetMasterDataforEnterKey( 'assembly' ,
												document.all.objFactoryName ,
												document.all.strFactoryCode ,
												document.all.strFactoryName );
			}

			break;





		////////// [DEPT & IN CHARGE NAME] //////////
		case 'dept':

			///// [部門]フィールド /////
			if( DeptValueFlg == 0 )
			{

				// [検索]検索ボタンにフォーカスがなく、テキストフィールドにフォーカスがある場合
				if( window.event.srcElement.name == 'strGroupCode' ||
					window.event.srcElement.name == 'strGroupName' )
				{
					subLoadMasterOption( 'swGroup',
											document.all.strGroupCode ,
											document.all.objGroupName ,
											Array( document.all.strGroupCode.value ,
											document.all.strGroupName.value ),
											objDataSourceSetting,
											0 );
				}

				// [検索]検索ボタンにフォーカスがある場合
				if( window.event.srcElement.id == 'SearchButton01' )
				{
					subLoadMasterOption( 'swGroup',
											document.all.strGroupCode ,
											document.all.objGroupName ,
											Array( document.all.strGroupCode.value ,
											document.all.strGroupName.value ),
											objDataSourceSetting,
											0 );
				}

				// [適用]適用ボタンにフォーカスがなく、セレクトフィールドにフォーカスがある場合
				if( window.event.srcElement.name == 'objGroupName' )
				{
					fncSetMasterDataParent( 'dept' ,
												document.all.objGroupName.value ,
												document.all.objUserName ,
												document.all.strUserCode ,
												document.all.strUserName );
				}

				// [適用]適用ボタンにフォーカスがある場合
				if( window.event.srcElement.id == 'ApplyButton' )
				{
					fncSetMasterDataforEnterKey( 'dept' ,
													document.all.objGroupName ,
													document.all.strGroupCode ,
													document.all.strGroupName ,
													document.all.strUserCode ,
													document.all.strUserName );
				}

				// [タブ][IN CHARGE NAME]タブにフォーカスがある場合
				if( window.event.srcElement.id == 'TabI' )
				{
					DeptShowI( document.all.strUserCode );
				}
				return false;
			}


			///// [担当者]フィールド /////
			else if( DeptValueFlg == 1 )
			{

				// [検索]検索ボタンにフォーカスがなく、テキストフィールドにフォーカスがある場合
				if( window.event.srcElement.name == 'strUserCode' ||
					window.event.srcElement.name == 'strUserName' )
				{
					subLoadMasterOption( 'swUser',
											document.all.strUserCode ,
											document.all.objUserName ,
											Array( document.all.strGroupCode.value ,
											document.all.strUserCode.value ,
											document.all.strUserName.value ),
											objDataSourceSetting1,
											1 );
				}

				// [検索]検索ボタンにフォーカスがある場合
				if( window.event.srcElement.id == 'SearchButton02' )
				{
					subLoadMasterOption( 'swUser',
											document.all.strUserCode ,
											document.all.objUserName ,
											Array( document.all.strGroupCode.value ,
											document.all.strUserCode.value ,
											document.all.strUserName.value ),
											objDataSourceSetting1,
											1 );
				}

				// [適用]適用ボタンにフォーカスがなく、セレクトフィールドにフォーカスがある場合
				if( window.event.srcElement.name == 'objUserName' )
				{
					fncSetMasterDataChild( 'dept' ,
											document.all.objUserName.value ,
											document.all.strGroupCodeH ,
											document.all.strGroupCode ,
											document.all.strGroupName );
				}

				// [適用]適用ボタンにフォーカスがある場合
				if( window.event.srcElement.id == 'ApplyButton' )
				{
					fncSetMasterDataforEnterKey( 'dept' ,
													document.all.objGroupName ,
													document.all.strGroupCode ,
													document.all.strGroupName ,
													document.all.strUserCode ,
													document.all.strUserName );
				}

				// [タブ][DEPT]タブにフォーカスがある場合
				if( window.event.srcElement.id == 'TabD' )
				{
					DeptShowD( document.all.strGroupCode );
				}
				return false;
			}

			break;





		////////// [PRODUCTS] //////////
		case 'products':

			// [検索]検索ボタンにフォーカスがなく、テキストフィールドにフォーカスがある場合
			if( window.event.srcElement.name == 'strProductCode' ||
				window.event.srcElement.name == 'strProductName' )
			{
				subLoadMasterOption( document.all.strSqlFileName.value,
										document.all.strProductCode ,
										document.all.objProductName ,
										Array( document.all.strProductCode.value ,
										document.all.strProductName.value,
										document.all.lngInputUserCode.value ) ,
										objDataSourceSetting ,
										0 );
			}

			// [検索]検索ボタンにフォーカスがある場合
			if( window.event.srcElement.id == 'SearchButton01' )
			{
				subLoadMasterOption( document.all.strSqlFileName.value,
										document.all.strProductCode ,
										document.all.objProductName ,
										Array( document.all.strProductCode.value ,
										document.all.strProductName.value,
										document.all.lngInputUserCode.value ) ,
										objDataSourceSetting ,
										0 );
			}

			// [適用]適用ボタンにフォーカスがなく、セレクトフィールドにフォーカスがある場合
			if( window.event.srcElement.name == 'objProductName' )
			{
				fncSetMasterDataParent( 'products' , document.all.objProductName.value );
			}

			// [適用]適用ボタンにフォーカスがある場合
			if( window.event.srcElement.id == 'ApplyButton' )
			{
				fncSetMasterDataforEnterKey( 'products' ,
												document.all.objProductName ,
												document.all.strLocationCode ,
												document.all.strLocationName );
			}

			break;





		////////// [LOCATION] //////////
		case 'location':

			// [検索]検索ボタンにフォーカスがなく、テキストフィールドにフォーカスがある場合
			if( window.event.srcElement.name == 'strLocationCode' ||
				window.event.srcElement.name == 'strLocationName' )
			{
				subLoadMasterOption( 'swLocation',
										document.all.strLocationCode ,
										document.all.objLocationName ,
										Array( document.all.strLocationCode.value ,
										document.all.strLocationName.value ) ,
										objDataSourceSetting );
			}

			// [検索]検索ボタンにフォーカスがある場合
			if( window.event.srcElement.id == 'SearchButton01' )
			{
				subLoadMasterOption( 'swLocation',
										document.all.strLocationCode ,
										document.all.objLocationName ,
										Array( document.all.strLocationCode.value ,
										document.all.strLocationName.value ) ,
										objDataSourceSetting );
			}

			// [適用]適用ボタンにフォーカスがなく、セレクトフィールドにフォーカスがある場合
			if( window.event.srcElement.name == 'objLocationName' )
			{
				fncSetMasterDataParent( 'location' , document.all.objLocationName.value );
			}

			// [適用]適用ボタンにフォーカスがある場合
			if( window.event.srcElement.id == 'ApplyButton' )
			{
				fncSetMasterDataforEnterKey( 'location' ,
												document.all.objLocationName ,
												document.all.strLocationCode ,
												document.all.strLocationName );
			}

			break;





		////////// [APPLICANT] //////////
		case 'applicant':

			// [検索]検索ボタンにフォーカスがなく、テキストフィールドにフォーカスがある場合
			if( window.event.srcElement.name == 'strUserCode' ||
				window.event.srcElement.name == 'strUserName' )
			{
				subLoadMasterOption( 'swUser',
										document.all.strUserCode ,
										document.all.objUserName ,
										Array( document.all.strUserCode.value ,
										document.all.strUserName.value ) ,
										objDataSourceSetting ,
										0 );
			}

			// [検索]検索ボタンにフォーカスがある場合
			if( window.event.srcElement.id == 'SearchButton01' )
			{
				subLoadMasterOption( 'swUser',
										document.all.strUserCode ,
										document.all.objUserName ,
										Array( document.all.strUserCode.value ,
										document.all.strUserName.value ) ,
										objDataSourceSetting ,
										0 );
			}

			// [適用]適用ボタンにフォーカスがなく、セレクトフィールドにフォーカスがある場合
			if( window.event.srcElement.name == 'objUserName' )
			{
				fncSetMasterDataParent( 'applicant' , document.all.objUserName.value );
			}

			// [適用]適用ボタンにフォーカスがある場合
			if( window.event.srcElement.id == 'ApplyButton' )
			{
				fncSetMasterData( 'applicant' ,
									document.all.objUserName ,
									document.all.strUserCode ,
									document.all.strUserName );
			}

			break;





		////////// [WF INPUT PERSON] //////////
		case 'wfinput':

			// [検索]検索ボタンにフォーカスがなく、テキストフィールドにフォーカスがある場合
			if( window.event.srcElement.name == 'strUserCode' ||
				window.event.srcElement.name == 'strUserName' )
			{
				subLoadMasterOption( 'swUser',
										document.all.strUserCode ,
										document.all.objUserName ,
										Array( document.all.strUserCode.value ,
										document.all.strUserName.value ) ,
										objDataSourceSetting ,
										0 );
			}

			// [検索]検索ボタンにフォーカスがある場合
			if( window.event.srcElement.id == 'SearchButton01' )
			{
				subLoadMasterOption( 'swUser',
										document.all.strUserCode ,
										document.all.objUserName ,
										Array( document.all.strUserCode.value ,
										document.all.strUserName.value ) ,
										objDataSourceSetting ,
										0 );
			}

			// [適用]適用ボタンにフォーカスがなく、セレクトフィールドにフォーカスがある場合
			if( window.event.srcElement.name == 'objUserName' )
			{
				fncSetMasterDataParent( 'wfinput' , document.all.objUserName.value );
			}

			// [適用]適用ボタンにフォーカスがある場合
			if( window.event.srcElement.id == 'ApplyButton' )
			{
				fncSetMasterData( 'wfinput' ,
									document.all.objUserName ,
									document.all.strUserCode ,
									document.all.strUserName );
			}

			break;





		////////// [VENDOR & IN CHARGE NAME] //////////
		case 'vi':

			///// [顧客]フィールド /////
			if( ViValueFlg == 0 )
			{

				// [検索]検索ボタンにフォーカスがなく、テキストフィールドにフォーカスがある場合
				if( window.event.srcElement.name == 'strCustomerCode' ||
					window.event.srcElement.name == 'strCustomerName' )
				{
					subLoadMasterOption( 'swCustomer',
											document.all.strCustomerCode ,
											document.all.objCustomerName ,
											Array( document.all.strCustomerCode.value ,
											document.all.strCustomerName.value ),
											objDataSourceSetting ,
											0 );
				}

				// [検索]検索ボタンにフォーカスがある場合
				if( window.event.srcElement.id == 'SearchButton01' )
				{
					subLoadMasterOption( 'swCustomer',
											document.all.strCustomerCode ,
											document.all.objCustomerName ,
											Array( document.all.strCustomerCode.value ,
											document.all.strCustomerName.value ),
											objDataSourceSetting ,
											0 );
				}

				// [適用]適用ボタンにフォーカスがなく、セレクトフィールドにフォーカスがある場合
				if( window.event.srcElement.name == 'objCustomerName' )
				{
					fncSetMasterDataParent( 'vi' ,
											document.all.objCustomerName.value ,
											document.all.objInChargeName ,
											document.all.strInChargeCode ,
											document.all.strInChargeName );
				}

				// [適用]適用ボタンにフォーカスがある場合
				if( window.event.srcElement.id == 'ApplyButton' )
				{
					fncSetMasterDataforEnterKey( 'vi' ,
													document.all.objCustomerName ,
													document.all.strCustomerCode ,
													document.all.strCustomerName ,
													document.all.strInChargeCode ,
													document.all.strInChargeName );
				}

				// [タブ][IN CHARGE NAME]タブにフォーカスがある場合
				if( window.event.srcElement.id == 'viTabI' )
				{
					ViShowI( document.all.strInChargeCode );
				}
				return false;
			}


			///// [顧客担当者]フィールド /////
			else if( ViValueFlg == 1 )
			{

				// [検索]検索ボタンにフォーカスがなく、テキストフィールドにフォーカスがある場合
				if( window.event.srcElement.name == 'strInChargeCode' ||
					window.event.srcElement.name == 'strInChargeName' )
				{
					subLoadMasterOption( 'swInCharge',
											document.all.strInChargeCode ,
											document.all.objInChargeName ,
											Array( document.all.strCustomerCode.value ,
											document.all.strInChargeCode.value ,
											document.all.strInChargeName.value ),
											objDataSourceSetting1,
											1 );
				}

				// [検索]検索ボタンにフォーカスがある場合
				if( window.event.srcElement.id == 'SearchButton02' )
				{
					subLoadMasterOption( 'swInCharge',
											document.all.strInChargeCode ,
											document.all.objInChargeName ,
											Array( document.all.strCustomerCode.value ,
											document.all.strInChargeCode.value ,
											document.all.strInChargeName.value ),
											objDataSourceSetting1,
											1 );
				}

				// [適用]適用ボタンにフォーカスがなく、セレクトフィールドにフォーカスがある場合
				if( window.event.srcElement.name == 'objInChargeName' )
				{
					fncSetMasterDataChild( 'vi' ,
											document.all.objInChargeName.value ,
											document.all.lngCustomerCodeH ,
											document.all.strCustomerCode ,
											document.all.strCustomerName );
				}

				// [適用]適用ボタンにフォーカスがある場合
				if( window.event.srcElement.id == 'ApplyButton' )
				{
					fncSetMasterDataforEnterKey( 'vi' ,
													document.all.objCustomerName ,
													document.all.strCustomerCode ,
													document.all.strCustomerName ,
													document.all.strInChargeCode ,
													document.all.strInChargeName );
				}

				// [タブ][VENDOR]タブにフォーカスがある場合
				if( window.event.srcElement.id == 'viTabD' )
				{
					ViShowD( document.all.strCustomerCode );
				}
				return false;
			}

			break;





		////////// [SUPPLIER] //////////
		case 'supplier':

			// [検索]検索ボタンにフォーカスがなく、テキストフィールドにフォーカスがある場合
			if( window.event.srcElement.name == 'strCustomerCode' ||
				window.event.srcElement.name == 'strCustomerName' )
			{
				subLoadMasterOption( 'swSupplier',
										document.all.strCustomerCode ,
										document.all.objCustomerName ,
										Array( document.all.strCustomerCode.value ,
										document.all.strCustomerName.value ) ,
										objDataSourceSetting );
			}

			// [検索]検索ボタンにフォーカスがある場合
			if( window.event.srcElement.id == 'SearchButton01' )
			{
				subLoadMasterOption( 'swSupplier',
										document.all.strCustomerCode ,
										document.all.objCustomerName ,
										Array( document.all.strCustomerCode.value ,
										document.all.strCustomerName.value ) ,
										objDataSourceSetting );
			}

			// [適用]適用ボタンにフォーカスがなく、セレクトフィールドにフォーカスがある場合
			if( window.event.srcElement.name == 'objCustomerName' )
			{
				fncSetMasterDataParent( 'supplier' , document.all.objCustomerName.value );
			}

			// [適用]適用ボタンにフォーカスがある場合
			if( window.event.srcElement.id == 'ApplyButton' )
			{
				fncSetMasterDataforEnterKey( 'supplier' ,
												document.all.objCustomerName ,
												document.all.strCustomerCode ,
												document.all.strCustomerName );
			}

			break;





		////////// [INPUT PERSON] //////////
		case 'input':

			// [検索]検索ボタンにフォーカスがなく、テキストフィールドにフォーカスがある場合
			if( window.event.srcElement.name == 'strInputUserCode' ||
				window.event.srcElement.name == 'strInputUserName' )
			{
				subLoadMasterOption( 'swInputUser',
										document.all.strInputUserCode ,
										document.all.objInputUserName ,
										Array( document.all.strInputUserCode.value ,
										document.all.strInputUserName.value ) ,
										objDataSourceSetting );
			}

			// [検索]検索ボタンにフォーカスがある場合
			if( window.event.srcElement.id == 'SearchButton01' )
			{
				subLoadMasterOption( 'swInputUser',
										document.all.strInputUserCode ,
										document.all.objInputUserName ,
										Array( document.all.strInputUserCode.value ,
										document.all.strInputUserName.value ) ,
										objDataSourceSetting );
			}

			// [適用]適用ボタンにフォーカスがなく、セレクトフィールドにフォーカスがある場合
			if( window.event.srcElement.name == 'objInputUserName' )
			{
				fncSetMasterDataParent( 'input' , document.all.objInputUserName.value );
			}

			// [適用]適用ボタンにフォーカスがある場合
			if( window.event.srcElement.id == 'ApplyButton' )
			{
				fncSetMasterData( 'input' ,
									document.all.objInputUserName ,
									document.all.strInputUserCode ,
									document.all.strInputUserName );
			}

			break;



		default:
			break;
	}

	return false;
}





///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////



// 代入先メインウィンドウオブジェクト格納用グローバル変数
var g_aryElementName = new Array();

// [親]マスターデータ取得用グローバル変数
var g_aryMasterParent = new Array();

// サブウィンドウクローズ時にフォーカスさせるオブジェクトタイプを取得するグローバル変数
var g_FocusObject = new Array();

// [子]マスターデータ取得用グローバル変数
var g_aryMasterChild = new Array();



////////// [適用ボタン][親][子]マスターデータをメインウィンドウに代入 //////////

// strWindowType	: サブウィンドウタイプ名
// m_objValue		: [親]セレクトボックスNAME
// lngObjThis		: [親]コード用テキストフィールドNAME
// strObjThis		: [親]名称用テキストフィールドNAME
// lngObjThisChild	: [子]コード用テキストフィールドNAME
// strObjThisChild	: [子]名称用テキストフィールドNAME

function fncSetMasterData( strWindowType , m_objValue , lngObjThis , strObjThis , lngObjThisChild , strObjThisChild )
{

	if( m_objValue.value != '' )
	{

		// メインウインドウのグローバル変数 [g_aryElementName] より代入先オブジェクト取得
		// 取得後、各Value値を代入
		g_aryElementName[0].value = document.all.lngCodeParentHidden.value;
		g_aryElementName[0].onchange();
		g_aryElementName[1].value = document.all.strNameParentHidden.value;


		////////// 以下、[子]処理 //////////
		if( lngObjThisChild &&
			document.all.lngCodeChildHidden.value != 'undefined' )
		{
			g_aryElementName[2].value = document.all.lngCodeChildHidden.value;
			g_aryElementName[2].onchange();
		}

		if( strObjThisChild &&
			document.all.strNameChildHidden.value != 'undefined' )
		{
			g_aryElementName[3].value = document.all.strNameChildHidden.value;
		}


		// タイプ別、ウィンドウクローズ
		fncSubMasterWinClose( strWindowType );

		//製品コードだった場合の処理
		if( m_objValue.name == "objProductName" )
		{
			//HSOのオブジェクトがなかったら処理終了
			if( typeof(window.parent.HSO) != "object" ) return false ;

			//各種の登録、修正画面から呼び出された場合
			if( typeof(window.parent.HSO.POFlg) == "object" ||
				typeof(window.parent.HSO.PCFlg) == "object" ||
				typeof(window.parent.HSO.SOFlg) == "object" ||
				typeof(window.parent.HSO.SCFlg) == "object" )
			{
				//製品コードをリロード
				window.parent.DLwin.fncDtProductCodeForMSW( m_objValue.value );
			}
		}
	}

	return false;
}


////////// [ENTER KEY][適用ボタン][親][子]マスターデータをメインウィンドウに代入 //////////

// strWindowType	: サブウィンドウタイプ名
// m_objValue		: [親]セレクトボックスNAME
// lngObjThis		: [親]コード用テキストフィールドNAME
// strObjThis		: [親]名称用テキストフィールドNAME
// lngObjThisChild	: [子]コード用テキストフィールドNAME
// strObjThisChild	: [子]名称用テキストフィールドNAME

function fncSetMasterDataforEnterKey( strWindowType , m_objValue , lngObjThis , strObjThis , lngObjThisChild , strObjThisChild )
{
	if( m_objValue.value != '' )
	{

		// メインウインドウのグローバル変数 [g_aryElementName] より代入先オブジェクト取得
		// 取得後、各Value値を代入
		g_aryElementName[0].value = document.all.lngCodeParentHidden.value;
		g_aryElementName[0].onchange();
		g_aryElementName[1].value = document.all.strNameParentHidden.value;


		////////// 以下、[子]処理 //////////
		if( lngObjThisChild &&
			document.all.lngCodeChildHidden.value != 'undefined' )
		{
			g_aryElementName[2].value = document.all.lngCodeChildHidden.value;
			g_aryElementName[2].onchange();
		}

		if( strObjThisChild &&
			document.all.strNameChildHidden.value != 'undefined' )
		{
			g_aryElementName[3].value = document.all.strNameChildHidden.value;
		}


		// タイプ別、ウィンドウクローズ
		fncSubMasterWinCloseforEnterKey( strWindowType );

		//製品コードだった場合の処理
		if( m_objValue.name == "objProductName" )
		{
			//HSOのオブジェクトがなかったら処理終了
			if( typeof(window.parent.HSO) != "object" ) return false ;

			//各種の登録、修正画面から呼び出された場合
			if( typeof(window.parent.HSO.POFlg) == "object" ||
				typeof(window.parent.HSO.PCFlg) == "object" ||
				typeof(window.parent.HSO.SOFlg) == "object" ||
				typeof(window.parent.HSO.SCFlg) == "object" )
			{
				//製品コードをリロード
				window.parent.DLwin.fncDtProductCodeForMSW( m_objValue.value );
			}
		}
	}

	return false;
}



////////// [ダブルクリック][親]マスターデータをメインウィンドウに代入 //////////

// strWindowType	: サブウィンドウタイプ名
// m_objValue		: [親]セレクトボックスNAME ( obj.value )
// m_objChild		: [子]セレクトボックスNAME
// lngObjThisChild	: [子]コード用テキストフィールドNAME
// strObjThisChild	: [子]名称用テキストフィールドNAME

function fncSetMasterDataParent( strWindowType , m_objValue , m_objChild , lngObjThisChild , strObjThisChild )
{

	if( m_objValue != '' )
	{

		// マスターデータを取得
		g_aryMasterParent = subLoadMasterGetIdName( objDataSourceSetting.recordset , m_objValue );

		// メインウインドウのグローバル変数 [g_aryElementName] より代入先オブジェクト取得
		// 取得後マスターデータを代入
		g_aryElementName[0].value = g_aryMasterParent['id'];
		g_aryElementName[0].onchange();
		g_aryElementName[1].value = g_aryMasterParent['name'];



			////////// 以下、[子]処理 //////////
			if( m_objChild )
			{
				// [子]セレクトボックス値 クリア
				subLoadMasterOptionClear( m_objChild , true );

				// [子]コード用テキストフィールド & [子]名称用テキストフィールド クリア
				lngObjThisChild.value = '';
				strObjThisChild.value = '';

				//メインウインドウの代入済み[子]データの初期化
				g_aryElementName[2].value = '';
				g_aryElementName[2].onchange();
				g_aryElementName[3].value = '';
			}


		// タイプ別、ウィンドウクローズ
		fncSubMasterWinClose( strWindowType );

	}

	return false;
}



////////// [ダブルクリック][子]マスターデータをメインウィンドウに代入 //////////

// strWindowType	: サブウィンドウタイプ名
// m_objChildValue	: [子]セレクトボックスNAME ( obj.value )
// lngObjThisH		: [親]コード用テキストフィールド(disabled)NAME
// lngObjThis		: [親]コード用テキストフィールドNAME
// strObjThis		: [親]名称用テキストフィールドNAME

function fncSetMasterDataChild( strWindowType , m_objChildValue , lngObjThisH , lngObjThisParent , strObjThisParent )
{

	if( lngObjThisH.value != '' )
	{

		// マスターデータを取得
		g_aryMasterChild = subLoadMasterGetIdName( objDataSourceSetting1.recordset , m_objChildValue );


		// メインウインドウのグローバル変数 [g_aryElementName] より代入先オブジェクト取得
		// 取得後マスターデータを代入
		if( document.all.lngCodeChildHidden.value != 'undefined' &&
			document.all.strNameChildHidden.value != 'undefined' )
		{
			g_aryElementName[2].value = document.all.lngCodeChildHidden.value;
			g_aryElementName[2].onchange();
			g_aryElementName[3].value = document.all.strNameChildHidden.value;
		}

		//g_aryElementName[2].value = g_aryMasterChild['id'];
		//g_aryElementName[3].value = g_aryMasterChild['name'];

		// メインウインドウのグローバル変数 [g_aryElementName] より代入先オブジェクト取得
		// 取得後[親]テキストフィールドの値を代入
		g_aryElementName[0].value = lngObjThisParent.value;
		g_aryElementName[0].onchange();
		g_aryElementName[1].value = strObjThisParent.value;


		// タイプ別、ウィンドウクローズ
		fncSubMasterWinClose( strWindowType );

	}

	return false;
}



////////// [シングルクリック][親]マスターデータをサブウィンドウ上(自身)のテキストフィールドに代入 //////////

// m_objValue		: [親]セレクトボックスNAME ( obj.value )
// lngObjThis		: [親]コード用テキストフィールドNAME
// strObjThis		: [親]名称用テキストフィールドNAME
// lngObjThisH		: [親]コード用テキストフィールド(disabled)NAME
// m_objChild		: [子]セレクトボックスNAME
// lngObjThisChild	: [子]コード用テキストフィールドNAME
// strObjThisChild	: [子]名称用テキストフィールドNAME

function fncViewMasterDataParent( m_objValue , lngObjThis , strObjThis , lngObjThisH , m_objChild , lngObjThisChild , strObjThisChild )
{

	if( m_objValue != '' )
	{

		// マスターデータを取得
		g_aryMasterParent = subLoadMasterGetIdName( objDataSourceSetting.recordset , m_objValue );

		// [親]コード用テキストフィールド & [親]名称用テキストフィールド に代入
		lngObjThis.value = g_aryMasterParent['id'];
		strObjThis.value = g_aryMasterParent['name'];

		// [HIDDEN][親]コード用テキストフィールド & [HIDDEN][親]名称用テキストフィールド に代入
		document.all.lngCodeParentHidden.value = g_aryMasterParent['id'];
		document.all.strNameParentHidden.value = g_aryMasterParent['name'];


		////////// 以下、[子]処理 //////////
		if( lngObjThisH )
		{
			// [親]コード用テキストフィールド(disabled) に代入
			lngObjThisH.value = lngObjThis.value;

			// [子]セレクトボックス値 クリア
			subLoadMasterOptionClear( m_objChild , true );

			// [子]コード用テキストフィールド & [子]名称用テキストフィールド クリア
			lngObjThisChild.value = '';
			strObjThisChild.value = '';

			// [HIDDEN][子]コード用テキストフィールド & [HIDDEN][子]名称用テキストフィールド クリア
			document.all.lngCodeChildHidden.value = '';
			document.all.strNameChildHidden.value = '';

			// [HIDDEN][子]カウンターの値 クリア
			document.all.lngCounterChildHidden.value = '';
		}


	}

	return false;
}



////////// [シングルクリック][子]マスターデータをサブウィンドウ上(自身)のテキストフィールド に代入 //////////

// m_objValue		: [子]セレクトボックスNAME ( obj.value )
// lngObjThis		: [子]コード用テキストフィールドNAME
// strObjThis		: [子]名称用テキストフィールドNAME

function fncViewMasterDataChild( m_objValue , lngObjThis , strObjThis )
{

	if( m_objValue != '' )
	{

		// マスターデータを取得
		g_aryMasterChild = subLoadMasterGetIdName( objDataSourceSetting1.recordset , m_objValue );

		// [子]コード用テキストフィールド & [子]名称用テキストフィールド に代入
		lngObjThis.value = g_aryMasterChild['id'];
		strObjThis.value = g_aryMasterChild['name'];

		// [HIDDEN][子]コード用テキストフィールド & [HIDDEN][子]名称用テキストフィールド に代入
		document.all.lngCodeChildHidden.value = g_aryMasterChild['id'];
		document.all.strNameChildHidden.value = g_aryMasterChild['name'];

	}

	return false;
}



////////// [親]コード値 と [子]親コード値 の一致処理 //////////

// lngObjThis		: [親]コード用テキストフィールドNAME
// lngObjThisH		: [親]コード用テキストフィールド(disabled)NAME

function fncBothCodeCheck( lngObjThis , lngObjThisH )
{
	// [親]コード用テキストフィールド値 と [親]コード用テキストフィールド(disabled)値 の一致処理
	// [親]コード用テキストフィールド値 が 変更され、
	// [親]コード用テキストフィールド(disabled)値と異なる場合
	if( lngObjThis.value != document.all.lngCodeParentHidden.value )
	{
		// [親]コード用テキストフィールド(disabled)値 クリア
		lngObjThisH.value = '';
	}

	return false;
}



////////// クリア処理 //////////

// m_obj			: [親]セレクトボックスNAME
// lngObjThis		: [親]コード用テキストフィールドNAME
// strObjThis		: [親]名称用テキストフィールドNAME
// lngObjThisH		: [親]コード用テキストフィールド(disabled)NAME
// m_objChild		: [子]セレクトボックスNAME
// lngObjThisChild	: [子]コード用テキストフィールドNAME
// strObjThisChild	: [子]名称用テキストフィールドNAME

function fncClearValue( m_obj , lngObjThis , strObjThis , lngObjThisH , m_objChild , lngObjThisChild , strObjThisChild )
{

	// [親]セレクトボックス値 クリア
	subLoadMasterOptionClear( m_obj , true );

	// [子]セレクトボックス値 クリア
	if( m_objChild )
	{
		subLoadMasterOptionClear( m_objChild , true );
	}

	// 各テキストフィールド値 クリア
	lngObjThis.value = '';
	strObjThis.value = '';

	// [HIDDEN]各テキストフィールド値 クリア
	document.all.lngCodeParentHidden.value = '';
	document.all.strNameParentHidden.value = '';


	// カウンターの値 クリア
	document.all.lngSearchResultCount.value = '';
	// [HIDDEN][親]カウンターの値 クリア
	document.all.lngCounterParentHidden.value = '';


	////////// 以下、[子]処理 //////////
	if( lngObjThisH )
	{
		// 各テキストフィールド値 クリア
		lngObjThisH.value = '';
		lngObjThisChild.value = '';
		strObjThisChild.value = '';

		// [HIDDEN]各テキストフィールド値 クリア
		document.all.lngCodeChildHidden.value = '';
		document.all.strNameChildHidden.value = '';

		// [HIDDEN][子]カウンターの値 クリア
		document.all.lngCounterChildHidden.value = '';
	}


	return false;
}






////////// タイプ別マスターウィンドウクローズモジュール //////////

function fncSubMasterWinClose( strWindowType )
{

	// タイプ別、ウィンドウクローズ
	switch( strWindowType )
	{
		case 'vendor': // [VENDOR]

			parent.DisplayerM01( '' );
			parent.ExchangeM01( 0 , parent.window.Pwin );

			// フォーカス先の設定
			parent.fncFocusObject( g_FocusObject[0] );
			break;


		case 'creation': // [CREATION FACTORY]

			parent.DisplayerM01_2( '' );
			parent.ExchangeM01_2( 0 , parent.window.Pwin );

			// フォーカス先の設定
			parent.fncFocusObject( g_FocusObject[0] );
			break;


		case 'assembly': // [ASSEMBLY FACTORY]

			parent.DisplayerM01_3( '' );
			parent.ExchangeM01_3( 0 , parent.window.Pwin );

			// フォーカス先の設定
			parent.fncFocusObject( g_FocusObject[0] );
			break;


		case 'dept': // [DEPT & IN CHARGE NAME]

			parent.DisplayerM02( '' );
			parent.ExchangeM02( 0 , parent.window.Pwin );

			// フォーカス先の設定
			parent.fncFocusObject( g_FocusObject[0] );
			break;


		case 'products': // [PRODUCTS]

			parent.DisplayerM03( '' );
			parent.ExchangeM03( 0 , parent.window.Pwin );

			// フォーカス先の設定
			parent.fncFocusObject( g_FocusObject[0] );
			break;


		case 'location': // [LOCATION]

			parent.DisplayerM04( '' );
			parent.ExchangeM04( 0 , parent.window.Pwin );

			// フォーカス先の設定
			parent.fncFocusObject( g_FocusObject[0] );
			break;


		case 'applicant': // [APPLICANT]

			parent.DisplayerM05( '' );
			parent.ExchangeM05( 0 , parent.window.Pwin );

			// フォーカス先の設定
			parent.fncFocusObject( g_FocusObject[0] );
			break;


		case 'wfinput': // [WF INPUT PERSON]

			parent.DisplayerM06( '' );
			parent.ExchangeM06( 0 , parent.window.Pwin );

			// フォーカス先の設定
			parent.fncFocusObject( g_FocusObject[0] );
			break;


		case 'vi': // [VENDOR & IN CHARGE NAME]

			parent.DisplayerM07( '' );
			parent.ExchangeM07( 0 , parent.window.Pwin );

			// フォーカス先の設定
			parent.fncFocusObject( g_FocusObject[0] );
			break;


		case 'supplier': // [SUPPLIER]

			parent.DisplayerM08( '' );
			parent.ExchangeM08( 0 , parent.window.Pwin );

			// フォーカス先の設定
			parent.fncFocusObject( g_FocusObject[0] );
			break;

		case 'input': // [INPUT PERSON]

			parent.DisplayerM09( '' );
			parent.ExchangeM09( 0 , parent.window.Pwin );

			// フォーカス先の設定
			parent.fncFocusObject( g_FocusObject[0] );
			break;


		default:
			break;
	}

	return false;
}







////////// [ENTER KEY]タイプ別マスターウィンドウクローズモジュール //////////

function fncSubMasterWinCloseforEnterKey( strWindowType )
{

	// タイプ別、ウィンドウクローズ
	switch( strWindowType )
	{
		case 'vendor': // [VENDOR]

			parent.DisplayerM01( 1 );
			parent.ExchangeM01( 1, parent.window.Pwin );

			// フォーカス先の設定
			parent.fncFocusObject( g_FocusObject[0] );
			break;


		case 'creation': // [CREATION FACTORY]

			parent.DisplayerM01_2( 1 );
			parent.ExchangeM01_2( 1 , parent.window.Pwin );

			// フォーカス先の設定
			parent.fncFocusObject( g_FocusObject[0] );
			break;


		case 'assembly': // [ASSEMBLY FACTORY]

			parent.DisplayerM01_3( 1 );
			parent.ExchangeM01_3( 1 , parent.window.Pwin );

			// フォーカス先の設定
			parent.fncFocusObject( g_FocusObject[0] );
			break;


		case 'dept': // [DEPT & IN CHARGE NAME]

			parent.DisplayerM02( 1 );
			parent.ExchangeM02( 1 , parent.window.Pwin );

			// フォーカス先の設定
			parent.fncFocusObject( g_FocusObject[0] );
			break;


		case 'products': // [PRODUCTS]

			parent.DisplayerM03( 1 );
			parent.ExchangeM03( 1 , parent.window.Pwin );

			// フォーカス先の設定
			parent.fncFocusObject( g_FocusObject[0] );
			break;


		case 'location': // [LOCATION]

			parent.DisplayerM04( 1 );
			parent.ExchangeM04( 1 , parent.window.Pwin );

			// フォーカス先の設定
			parent.fncFocusObject( g_FocusObject[0] );
			break;


		case 'applicant': // [APPLICANT]

			parent.DisplayerM05( 1 );
			parent.ExchangeM05( 1 , parent.window.Pwin );

			// フォーカス先の設定
			parent.fncFocusObject( g_FocusObject[0] );
			break;


		case 'wfinput': // [WF INPUT PERSON]

			parent.DisplayerM06( 1 );
			parent.ExchangeM06( 1 , parent.window.Pwin );

			// フォーカス先の設定
			parent.fncFocusObject( g_FocusObject[0] );
			break;


		case 'vi': // [VENDOR & IN CHARGE NAME]

			parent.DisplayerM07( 1 );
			parent.ExchangeM07( 1 , parent.window.Pwin );

			// フォーカス先の設定
			parent.fncFocusObject( g_FocusObject[0] );
			break;


		case 'supplier': // [SUPPLIER]

			parent.DisplayerM08( 1 );
			parent.ExchangeM08( 1 , parent.window.Pwin );

			// フォーカス先の設定
			parent.fncFocusObject( g_FocusObject[0] );
			break;

		case 'input': // [INPUT PERSON]

			parent.DisplayerM09( 1 );
			parent.ExchangeM09( 1 , parent.window.Pwin );

			// フォーカス先の設定
			parent.fncFocusObject( g_FocusObject[0] );
			break;


		default:
			break;
	}

	return false;
}




function fncNextFocus( objName )
{
	objName.focus();
	return false;
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////






////////// IME //////////
function setIME(obj)
{
	document.all(obj).style.imeMode = 'disabled';
}



////////// AUTO FOCUS //////////
function autoFocus1()
{
	//document.all.aaa.focus();
	return false;
}

function autoFocus2()
{
	//document.all.ccc.focus();
	return false;
}


////////// FOCUS COLOR CHANGE //////////
function chColorOn(obj)
{
	obj.style.backgroundColor = focuscolor;
	obj.select();
	return false;
}

function chColorOff(obj)
{
	obj.style.backgroundColor='#f1f1f1';
	return false;
}




///////////////////////////////////////////////////////////////////////////
/* CLEAR BUTTON */
function ClearJOff(obj)
{
	obj.src = clrbtJ1;
}

function ClearJOn(obj)
{
	obj.src = clrbtJ2;
}

function ClearEOff(obj)
{
	obj.src = clrbtE1;
}

function ClearEOn(obj)
{
	obj.src = clrbtE2;
}



///////////////////////////////////////////////////////////////////////////
/* APPLY BUTTON */
function ApplyJOff(obj)
{
	obj.src = aplybtJ1;
}

function ApplyJOn(obj)
{
	obj.src = aplybtJ2;
}

function ApplyEOff(obj)
{
	obj.src = aplybtE1;
}

function ApplyEOn(obj)
{
	obj.src = aplybtE2;
}




function fncApplyBtCharModeOn( obj )
{
	if( parent.lngLanguageCode == 1 )
	{
		ApplyJOn(obj);
	}
	else if( parent.lngLanguageCode == 0 )
	{
		ApplyEOn(obj);
	}
}


function fncApplyBtCharModeOff( obj )
{
	if( parent.lngLanguageCode == 1 )
	{
		ApplyJOff(obj);
	}
	else if( parent.lngLanguageCode == 0 )
	{
		ApplyEOff(obj);
	}
}






///////////////////////////////////////////////////////////////////////////
/* SEARCH BUTTON */
function SearchJOff(obj)
{
	obj.src = schbtJ1;
}

function SearchJOn(obj)
{
	obj.src = schbtJ2;
}

function SearchEOff(obj)
{
	obj.src = schbtE1;
}

function SearchEOn(obj)
{
	obj.src = schbtE2;
}



///////////////////////////////////////////////////////////////////////////
/* DEPT TAB & IN CHARGE NAME TAB*/
function DtabOff(obj)
{
	obj.src = taba1;
}

function DtabOn(obj)
{
	obj.src = taba2;
}

function ItabOff(obj)
{
	if( typeof(obj) != 'undefined' )
	{
		obj.src = tabb1;
	}
}

function ItabOn(obj)
{
	if( typeof(obj) != 'undefined' )
	{
		obj.src = tabb2;
	}
}


///////////////////////////////////////////////////////////////////////////
/* VENDOR TAB & IN CHARGE NAME TAB*/
function viDtabOff(obj)
{
	obj.src = vitaba1;
}

function viDtabOn(obj)
{
	obj.src = vitaba2;
}

function viItabOff(obj)
{
	if( typeof(obj) != 'undefined' )
	{
		obj.src = vitabb1;
	}
}

function viItabOn(obj)
{
	if( typeof(obj) != 'undefined' )
	{
		obj.src = vitabb2;
	}
}


///////////////////////////////////////////////////////////////////////////
/* SHOW-HIDE */
var DeptValueFlg = 0;

function DeptShowD( objA ) //PUSH DEPT TAB
{
	document.all.InputA.style.visibility = 'visible' ;
	document.all.InputB.style.visibility = 'hidden' ;

	document.all.VarsB01.style.visibility = 'visible' ;
	document.all.VarsD01.style.visibility = 'hidden' ;

	TabD.innerHTML = objTabA3;
	TabI.innerHTML = objTabB1;

	objA.focus();

	DeptValueFlg = 0;

	// カウンターの値 クリア
	document.all.lngSearchResultCount.value = '';

	// [親]取得済みカウンターの値の再代入
	document.all.lngSearchResultCount.value = document.all.lngCounterParentHidden.value;

	return false;
}


function DeptShowI( objB ) //PUSH IN CHARGE NAME TAB
{
	document.all.InputA.style.visibility = 'hidden' ;
	document.all.InputB.style.visibility = 'visible' ;

	document.all.VarsB01.style.visibility = 'hidden' ;
	document.all.VarsD01.style.visibility = 'visible' ;

	TabI.innerHTML = objTabB3;
	TabD.innerHTML = objTabA1;

	objB.focus();

	DeptValueFlg = 1;

	// カウンターの値 クリア
	document.all.lngSearchResultCount.value = '';

	// [子]取得済みカウンターの値の再代入
	document.all.lngSearchResultCount.value = document.all.lngCounterChildHidden.value;

	return false;
}




var ViValueFlg = 0;

function ViShowD( objA ) //PUSH VENDOR TAB
{
	document.all.InputA.style.visibility = 'visible' ;
	document.all.InputB.style.visibility = 'hidden' ;

	document.all.VarsB01.style.visibility = 'visible' ;
	document.all.VarsD01.style.visibility = 'hidden' ;

	viTabD.innerHTML = ViobjTabA3;
	viTabI.innerHTML = ViobjTabB1;

	objA.focus();

	ViValueFlg = 0;

	// カウンターの値 クリア
	document.all.lngSearchResultCount.value = '';

	// [親]取得済みカウンターの値の再代入
	document.all.lngSearchResultCount.value = document.all.lngCounterParentHidden.value;

	return false;
}


function ViShowI( objB ) //PUSH IN CHARGE NAME TAB
{
	document.all.InputA.style.visibility = 'hidden' ;
	document.all.InputB.style.visibility = 'visible' ;

	document.all.VarsB01.style.visibility = 'hidden' ;
	document.all.VarsD01.style.visibility = 'visible' ;

	viTabI.innerHTML = ViobjTabB3;
	viTabD.innerHTML = ViobjTabA1;

	objB.focus();

	ViValueFlg = 1;

	// カウンターの値 クリア
	document.all.lngSearchResultCount.value = '';

	// [子]取得済みカウンターの値の再代入
	document.all.lngSearchResultCount.value = document.all.lngCounterChildHidden.value;

	return false;
}


//-->
