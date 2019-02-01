<!--


///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

// subMasterWindowType	: ���֥�����ɥ�������̾
// strIfrmName			: ���֥�����ɥ�IFRAM̾(��)
// objA					: [InputA��]�ǽ��TabindexŬ�ѥ��֥�������̾
// objB					: [InputB��]�ǽ��TabindexŬ�ѥ��֥�������̾

////////// [TABINDEX]����([ApplyButton]�ޤǤ��ä���ǽ���᤹) //////////

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

////////// �������٥�ȼ��� //////////
window.document.onkeydown=onKeyDown;

function onKeyDown( e )
{
	// ���Ǥ�̤����ξ��
	if (typeof(window.event.srcElement.name) == "undefined")
	{
		//BackSpace���������ɻ�
		if( window.event.keyCode == 8	) return false ;
	}

	// ���Ǥ�°���� [disabled] ���Ф������
	if (window.event.srcElement.disabled == true)
	{
		//BackSpace���������ɻ�
		if( window.event.keyCode == 8	) return false ;
	}

	// ���Ǥ� [focus] �ξ��
	if(window.event.srcElement.type != 'text' && window.event.srcElement.focus)
	{
		//BackSpace���������ɻ�
		if( window.event.keyCode == 8	) return false ;
	}

	// [alt] + [��]���������ɻ�
	if( window.event.altKey == true && window.event.keyCode == 37 ) return false ;

	// [alt] + [��]���������ɻ�
	if( window.event.altKey == true && window.event.keyCode == 39 ) return false ;

	// [alt] + [c]���� �������֥ޥ�����������ɥ�������
	//if( window.event.altKey == true && window.event.keyCode == 67 )
	//{
		// �������̥ޥ�����������ɥ��������⥸�塼��ƽФ�
		//fncSubMasterWinClose( document.all.strSubMasterWindowType.value );
	//}

	// [ENTER]����������
	if( window.event.keyCode == 13 )
	{
		// ������Ŭ�ѥܥ����ѥ��٥�ȥ⥸�塼��ƽФ�
		fncSubmasterQueryforEnterKey( document.all.strSubMasterWindowType.value );
		return true;
	}

}







////////// �ɥ�å�����ɥɥ�åפζػ� //////////
window.document.ondragstart=onDragStart;

function onDragStart(e)
{
	return false;
}






////////// [shift] + [tab]���������ɻ� //////////
function fncInvalidKey()
{
	window.document.onkeydown = onKeyDownForfncInvalidKey;
}


function onKeyDownForfncInvalidKey()
{
	if(window.event.keyCode == 9 && event.shiftKey == true) return false;

	// [ENTER]����������
	if( window.event.keyCode == 13 )
	{
		// ������Ŭ�ѥܥ����ѥ��٥�ȥ⥸�塼��ƽФ�
		fncSubmasterQueryforEnterKey( document.all.strSubMasterWindowType.value );
		return true;
	}
}


function fncEffectiveKey()
{
	window.document.onkeydown =onKeyDown;
}







///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

////////// [ENTER]������������������Ŭ�ѥܥ����ѥ��٥�ȥ⥸�塼�� //////////
function fncSubmasterQueryforEnterKey( queryType )
{
	switch( queryType )
	{


		////////// [VENDOR] //////////
		case 'vendor':

			// [����]�����ܥ���˥ե����������ʤ����ƥ����ȥե�����ɤ˥ե���������������
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

			// [����]�����ܥ���˥ե���������������
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

			// [Ŭ��]Ŭ�ѥܥ���˥ե����������ʤ������쥯�ȥե�����ɤ˥ե���������������
			if( window.event.srcElement.name == 'objCustomerName' )
			{
				fncSetMasterDataParent( 'vendor' , document.all.objCustomerName.value );
			}

			// [Ŭ��]Ŭ�ѥܥ���˥ե���������������
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

			// [����]�����ܥ���˥ե����������ʤ����ƥ����ȥե�����ɤ˥ե���������������
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

			// [����]�����ܥ���˥ե���������������
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

			// [Ŭ��]Ŭ�ѥܥ���˥ե����������ʤ������쥯�ȥե�����ɤ˥ե���������������
			if( window.event.srcElement.name == 'objCreationFactoryName' )
			{
				fncSetMasterDataParent( 'creation' , document.all.objCreationFactoryName.value );
			}

			// [Ŭ��]Ŭ�ѥܥ���˥ե���������������
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

			// [����]�����ܥ���˥ե����������ʤ����ƥ����ȥե�����ɤ˥ե���������������
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

			// [����]�����ܥ���˥ե���������������
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

			// [Ŭ��]Ŭ�ѥܥ���˥ե����������ʤ������쥯�ȥե�����ɤ˥ե���������������
			if( window.event.srcElement.name == 'objFactoryName' )
			{
				fncSetMasterDataParent( 'assembly' , document.all.objFactoryName.value );
			}

			// [Ŭ��]Ŭ�ѥܥ���˥ե���������������
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

			///// [����]�ե������ /////
			if( DeptValueFlg == 0 )
			{

				// [����]�����ܥ���˥ե����������ʤ����ƥ����ȥե�����ɤ˥ե���������������
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

				// [����]�����ܥ���˥ե���������������
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

				// [Ŭ��]Ŭ�ѥܥ���˥ե����������ʤ������쥯�ȥե�����ɤ˥ե���������������
				if( window.event.srcElement.name == 'objGroupName' )
				{
					fncSetMasterDataParent( 'dept' ,
												document.all.objGroupName.value ,
												document.all.objUserName ,
												document.all.strUserCode ,
												document.all.strUserName );
				}

				// [Ŭ��]Ŭ�ѥܥ���˥ե���������������
				if( window.event.srcElement.id == 'ApplyButton' )
				{
					fncSetMasterDataforEnterKey( 'dept' ,
													document.all.objGroupName ,
													document.all.strGroupCode ,
													document.all.strGroupName ,
													document.all.strUserCode ,
													document.all.strUserName );
				}

				// [����][IN CHARGE NAME]���֤˥ե���������������
				if( window.event.srcElement.id == 'TabI' )
				{
					DeptShowI( document.all.strUserCode );
				}
				return false;
			}


			///// [ô����]�ե������ /////
			else if( DeptValueFlg == 1 )
			{

				// [����]�����ܥ���˥ե����������ʤ����ƥ����ȥե�����ɤ˥ե���������������
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

				// [����]�����ܥ���˥ե���������������
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

				// [Ŭ��]Ŭ�ѥܥ���˥ե����������ʤ������쥯�ȥե�����ɤ˥ե���������������
				if( window.event.srcElement.name == 'objUserName' )
				{
					fncSetMasterDataChild( 'dept' ,
											document.all.objUserName.value ,
											document.all.strGroupCodeH ,
											document.all.strGroupCode ,
											document.all.strGroupName );
				}

				// [Ŭ��]Ŭ�ѥܥ���˥ե���������������
				if( window.event.srcElement.id == 'ApplyButton' )
				{
					fncSetMasterDataforEnterKey( 'dept' ,
													document.all.objGroupName ,
													document.all.strGroupCode ,
													document.all.strGroupName ,
													document.all.strUserCode ,
													document.all.strUserName );
				}

				// [����][DEPT]���֤˥ե���������������
				if( window.event.srcElement.id == 'TabD' )
				{
					DeptShowD( document.all.strGroupCode );
				}
				return false;
			}

			break;





		////////// [PRODUCTS] //////////
		case 'products':

			// [����]�����ܥ���˥ե����������ʤ����ƥ����ȥե�����ɤ˥ե���������������
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

			// [����]�����ܥ���˥ե���������������
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

			// [Ŭ��]Ŭ�ѥܥ���˥ե����������ʤ������쥯�ȥե�����ɤ˥ե���������������
			if( window.event.srcElement.name == 'objProductName' )
			{
				fncSetMasterDataParent( 'products' , document.all.objProductName.value );
			}

			// [Ŭ��]Ŭ�ѥܥ���˥ե���������������
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

			// [����]�����ܥ���˥ե����������ʤ����ƥ����ȥե�����ɤ˥ե���������������
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

			// [����]�����ܥ���˥ե���������������
			if( window.event.srcElement.id == 'SearchButton01' )
			{
				subLoadMasterOption( 'swLocation',
										document.all.strLocationCode ,
										document.all.objLocationName ,
										Array( document.all.strLocationCode.value ,
										document.all.strLocationName.value ) ,
										objDataSourceSetting );
			}

			// [Ŭ��]Ŭ�ѥܥ���˥ե����������ʤ������쥯�ȥե�����ɤ˥ե���������������
			if( window.event.srcElement.name == 'objLocationName' )
			{
				fncSetMasterDataParent( 'location' , document.all.objLocationName.value );
			}

			// [Ŭ��]Ŭ�ѥܥ���˥ե���������������
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

			// [����]�����ܥ���˥ե����������ʤ����ƥ����ȥե�����ɤ˥ե���������������
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

			// [����]�����ܥ���˥ե���������������
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

			// [Ŭ��]Ŭ�ѥܥ���˥ե����������ʤ������쥯�ȥե�����ɤ˥ե���������������
			if( window.event.srcElement.name == 'objUserName' )
			{
				fncSetMasterDataParent( 'applicant' , document.all.objUserName.value );
			}

			// [Ŭ��]Ŭ�ѥܥ���˥ե���������������
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

			// [����]�����ܥ���˥ե����������ʤ����ƥ����ȥե�����ɤ˥ե���������������
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

			// [����]�����ܥ���˥ե���������������
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

			// [Ŭ��]Ŭ�ѥܥ���˥ե����������ʤ������쥯�ȥե�����ɤ˥ե���������������
			if( window.event.srcElement.name == 'objUserName' )
			{
				fncSetMasterDataParent( 'wfinput' , document.all.objUserName.value );
			}

			// [Ŭ��]Ŭ�ѥܥ���˥ե���������������
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

			///// [�ܵ�]�ե������ /////
			if( ViValueFlg == 0 )
			{

				// [����]�����ܥ���˥ե����������ʤ����ƥ����ȥե�����ɤ˥ե���������������
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

				// [����]�����ܥ���˥ե���������������
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

				// [Ŭ��]Ŭ�ѥܥ���˥ե����������ʤ������쥯�ȥե�����ɤ˥ե���������������
				if( window.event.srcElement.name == 'objCustomerName' )
				{
					fncSetMasterDataParent( 'vi' ,
											document.all.objCustomerName.value ,
											document.all.objInChargeName ,
											document.all.strInChargeCode ,
											document.all.strInChargeName );
				}

				// [Ŭ��]Ŭ�ѥܥ���˥ե���������������
				if( window.event.srcElement.id == 'ApplyButton' )
				{
					fncSetMasterDataforEnterKey( 'vi' ,
													document.all.objCustomerName ,
													document.all.strCustomerCode ,
													document.all.strCustomerName ,
													document.all.strInChargeCode ,
													document.all.strInChargeName );
				}

				// [����][IN CHARGE NAME]���֤˥ե���������������
				if( window.event.srcElement.id == 'viTabI' )
				{
					ViShowI( document.all.strInChargeCode );
				}
				return false;
			}


			///// [�ܵ�ô����]�ե������ /////
			else if( ViValueFlg == 1 )
			{

				// [����]�����ܥ���˥ե����������ʤ����ƥ����ȥե�����ɤ˥ե���������������
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

				// [����]�����ܥ���˥ե���������������
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

				// [Ŭ��]Ŭ�ѥܥ���˥ե����������ʤ������쥯�ȥե�����ɤ˥ե���������������
				if( window.event.srcElement.name == 'objInChargeName' )
				{
					fncSetMasterDataChild( 'vi' ,
											document.all.objInChargeName.value ,
											document.all.lngCustomerCodeH ,
											document.all.strCustomerCode ,
											document.all.strCustomerName );
				}

				// [Ŭ��]Ŭ�ѥܥ���˥ե���������������
				if( window.event.srcElement.id == 'ApplyButton' )
				{
					fncSetMasterDataforEnterKey( 'vi' ,
													document.all.objCustomerName ,
													document.all.strCustomerCode ,
													document.all.strCustomerName ,
													document.all.strInChargeCode ,
													document.all.strInChargeName );
				}

				// [����][VENDOR]���֤˥ե���������������
				if( window.event.srcElement.id == 'viTabD' )
				{
					ViShowD( document.all.strCustomerCode );
				}
				return false;
			}

			break;





		////////// [SUPPLIER] //////////
		case 'supplier':

			// [����]�����ܥ���˥ե����������ʤ����ƥ����ȥե�����ɤ˥ե���������������
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

			// [����]�����ܥ���˥ե���������������
			if( window.event.srcElement.id == 'SearchButton01' )
			{
				subLoadMasterOption( 'swSupplier',
										document.all.strCustomerCode ,
										document.all.objCustomerName ,
										Array( document.all.strCustomerCode.value ,
										document.all.strCustomerName.value ) ,
										objDataSourceSetting );
			}

			// [Ŭ��]Ŭ�ѥܥ���˥ե����������ʤ������쥯�ȥե�����ɤ˥ե���������������
			if( window.event.srcElement.name == 'objCustomerName' )
			{
				fncSetMasterDataParent( 'supplier' , document.all.objCustomerName.value );
			}

			// [Ŭ��]Ŭ�ѥܥ���˥ե���������������
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

			// [����]�����ܥ���˥ե����������ʤ����ƥ����ȥե�����ɤ˥ե���������������
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

			// [����]�����ܥ���˥ե���������������
			if( window.event.srcElement.id == 'SearchButton01' )
			{
				subLoadMasterOption( 'swInputUser',
										document.all.strInputUserCode ,
										document.all.objInputUserName ,
										Array( document.all.strInputUserCode.value ,
										document.all.strInputUserName.value ) ,
										objDataSourceSetting );
			}

			// [Ŭ��]Ŭ�ѥܥ���˥ե����������ʤ������쥯�ȥե�����ɤ˥ե���������������
			if( window.event.srcElement.name == 'objInputUserName' )
			{
				fncSetMasterDataParent( 'input' , document.all.objInputUserName.value );
			}

			// [Ŭ��]Ŭ�ѥܥ���˥ե���������������
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



// ������ᥤ�󥦥���ɥ����֥������ȳ�Ǽ�ѥ����Х��ѿ�
var g_aryElementName = new Array();

// [��]�ޥ������ǡ��������ѥ����Х��ѿ�
var g_aryMasterParent = new Array();

// ���֥�����ɥ����������˥ե������������륪�֥������ȥ����פ�������륰���Х��ѿ�
var g_FocusObject = new Array();

// [��]�ޥ������ǡ��������ѥ����Х��ѿ�
var g_aryMasterChild = new Array();



////////// [Ŭ�ѥܥ���][��][��]�ޥ������ǡ�����ᥤ�󥦥���ɥ������� //////////

// strWindowType	: ���֥�����ɥ�������̾
// m_objValue		: [��]���쥯�ȥܥå���NAME
// lngObjThis		: [��]�������ѥƥ����ȥե������NAME
// strObjThis		: [��]̾���ѥƥ����ȥե������NAME
// lngObjThisChild	: [��]�������ѥƥ����ȥե������NAME
// strObjThisChild	: [��]̾���ѥƥ����ȥե������NAME

function fncSetMasterData( strWindowType , m_objValue , lngObjThis , strObjThis , lngObjThisChild , strObjThisChild )
{

	if( m_objValue.value != '' )
	{

		// �ᥤ�󥦥���ɥ��Υ����Х��ѿ� [g_aryElementName] ��������襪�֥������ȼ���
		// �����塢��Value�ͤ�����
		g_aryElementName[0].value = document.all.lngCodeParentHidden.value;
		g_aryElementName[0].onchange();
		g_aryElementName[1].value = document.all.strNameParentHidden.value;


		////////// �ʲ���[��]���� //////////
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


		// �������̡�������ɥ�������
		fncSubMasterWinClose( strWindowType );

		//���ʥ����ɤ��ä����ν���
		if( m_objValue.name == "objProductName" )
		{
			//HSO�Υ��֥������Ȥ��ʤ��ä��������λ
			if( typeof(window.parent.HSO) != "object" ) return false ;

			//�Ƽ����Ͽ���������̤���ƤӽФ��줿���
			if( typeof(window.parent.HSO.POFlg) == "object" ||
				typeof(window.parent.HSO.PCFlg) == "object" ||
				typeof(window.parent.HSO.SOFlg) == "object" ||
				typeof(window.parent.HSO.SCFlg) == "object" )
			{
				//���ʥ����ɤ�����
				window.parent.DLwin.fncDtProductCodeForMSW( m_objValue.value );
			}
		}
	}

	return false;
}


////////// [ENTER KEY][Ŭ�ѥܥ���][��][��]�ޥ������ǡ�����ᥤ�󥦥���ɥ������� //////////

// strWindowType	: ���֥�����ɥ�������̾
// m_objValue		: [��]���쥯�ȥܥå���NAME
// lngObjThis		: [��]�������ѥƥ����ȥե������NAME
// strObjThis		: [��]̾���ѥƥ����ȥե������NAME
// lngObjThisChild	: [��]�������ѥƥ����ȥե������NAME
// strObjThisChild	: [��]̾���ѥƥ����ȥե������NAME

function fncSetMasterDataforEnterKey( strWindowType , m_objValue , lngObjThis , strObjThis , lngObjThisChild , strObjThisChild )
{
	if( m_objValue.value != '' )
	{

		// �ᥤ�󥦥���ɥ��Υ����Х��ѿ� [g_aryElementName] ��������襪�֥������ȼ���
		// �����塢��Value�ͤ�����
		g_aryElementName[0].value = document.all.lngCodeParentHidden.value;
		g_aryElementName[0].onchange();
		g_aryElementName[1].value = document.all.strNameParentHidden.value;


		////////// �ʲ���[��]���� //////////
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


		// �������̡�������ɥ�������
		fncSubMasterWinCloseforEnterKey( strWindowType );

		//���ʥ����ɤ��ä����ν���
		if( m_objValue.name == "objProductName" )
		{
			//HSO�Υ��֥������Ȥ��ʤ��ä��������λ
			if( typeof(window.parent.HSO) != "object" ) return false ;

			//�Ƽ����Ͽ���������̤���ƤӽФ��줿���
			if( typeof(window.parent.HSO.POFlg) == "object" ||
				typeof(window.parent.HSO.PCFlg) == "object" ||
				typeof(window.parent.HSO.SOFlg) == "object" ||
				typeof(window.parent.HSO.SCFlg) == "object" )
			{
				//���ʥ����ɤ�����
				window.parent.DLwin.fncDtProductCodeForMSW( m_objValue.value );
			}
		}
	}

	return false;
}



////////// [���֥륯��å�][��]�ޥ������ǡ�����ᥤ�󥦥���ɥ������� //////////

// strWindowType	: ���֥�����ɥ�������̾
// m_objValue		: [��]���쥯�ȥܥå���NAME ( obj.value )
// m_objChild		: [��]���쥯�ȥܥå���NAME
// lngObjThisChild	: [��]�������ѥƥ����ȥե������NAME
// strObjThisChild	: [��]̾���ѥƥ����ȥե������NAME

function fncSetMasterDataParent( strWindowType , m_objValue , m_objChild , lngObjThisChild , strObjThisChild )
{

	if( m_objValue != '' )
	{

		// �ޥ������ǡ��������
		g_aryMasterParent = subLoadMasterGetIdName( objDataSourceSetting.recordset , m_objValue );

		// �ᥤ�󥦥���ɥ��Υ����Х��ѿ� [g_aryElementName] ��������襪�֥������ȼ���
		// ������ޥ������ǡ���������
		g_aryElementName[0].value = g_aryMasterParent['id'];
		g_aryElementName[0].onchange();
		g_aryElementName[1].value = g_aryMasterParent['name'];



			////////// �ʲ���[��]���� //////////
			if( m_objChild )
			{
				// [��]���쥯�ȥܥå����� ���ꥢ
				subLoadMasterOptionClear( m_objChild , true );

				// [��]�������ѥƥ����ȥե������ & [��]̾���ѥƥ����ȥե������ ���ꥢ
				lngObjThisChild.value = '';
				strObjThisChild.value = '';

				//�ᥤ�󥦥���ɥ��������Ѥ�[��]�ǡ����ν����
				g_aryElementName[2].value = '';
				g_aryElementName[2].onchange();
				g_aryElementName[3].value = '';
			}


		// �������̡�������ɥ�������
		fncSubMasterWinClose( strWindowType );

	}

	return false;
}



////////// [���֥륯��å�][��]�ޥ������ǡ�����ᥤ�󥦥���ɥ������� //////////

// strWindowType	: ���֥�����ɥ�������̾
// m_objChildValue	: [��]���쥯�ȥܥå���NAME ( obj.value )
// lngObjThisH		: [��]�������ѥƥ����ȥե������(disabled)NAME
// lngObjThis		: [��]�������ѥƥ����ȥե������NAME
// strObjThis		: [��]̾���ѥƥ����ȥե������NAME

function fncSetMasterDataChild( strWindowType , m_objChildValue , lngObjThisH , lngObjThisParent , strObjThisParent )
{

	if( lngObjThisH.value != '' )
	{

		// �ޥ������ǡ��������
		g_aryMasterChild = subLoadMasterGetIdName( objDataSourceSetting1.recordset , m_objChildValue );


		// �ᥤ�󥦥���ɥ��Υ����Х��ѿ� [g_aryElementName] ��������襪�֥������ȼ���
		// ������ޥ������ǡ���������
		if( document.all.lngCodeChildHidden.value != 'undefined' &&
			document.all.strNameChildHidden.value != 'undefined' )
		{
			g_aryElementName[2].value = document.all.lngCodeChildHidden.value;
			g_aryElementName[2].onchange();
			g_aryElementName[3].value = document.all.strNameChildHidden.value;
		}

		//g_aryElementName[2].value = g_aryMasterChild['id'];
		//g_aryElementName[3].value = g_aryMasterChild['name'];

		// �ᥤ�󥦥���ɥ��Υ����Х��ѿ� [g_aryElementName] ��������襪�֥������ȼ���
		// ������[��]�ƥ����ȥե�����ɤ��ͤ�����
		g_aryElementName[0].value = lngObjThisParent.value;
		g_aryElementName[0].onchange();
		g_aryElementName[1].value = strObjThisParent.value;


		// �������̡�������ɥ�������
		fncSubMasterWinClose( strWindowType );

	}

	return false;
}



////////// [���󥰥륯��å�][��]�ޥ������ǡ����򥵥֥�����ɥ���(����)�Υƥ����ȥե�����ɤ����� //////////

// m_objValue		: [��]���쥯�ȥܥå���NAME ( obj.value )
// lngObjThis		: [��]�������ѥƥ����ȥե������NAME
// strObjThis		: [��]̾���ѥƥ����ȥե������NAME
// lngObjThisH		: [��]�������ѥƥ����ȥե������(disabled)NAME
// m_objChild		: [��]���쥯�ȥܥå���NAME
// lngObjThisChild	: [��]�������ѥƥ����ȥե������NAME
// strObjThisChild	: [��]̾���ѥƥ����ȥե������NAME

function fncViewMasterDataParent( m_objValue , lngObjThis , strObjThis , lngObjThisH , m_objChild , lngObjThisChild , strObjThisChild )
{

	if( m_objValue != '' )
	{

		// �ޥ������ǡ��������
		g_aryMasterParent = subLoadMasterGetIdName( objDataSourceSetting.recordset , m_objValue );

		// [��]�������ѥƥ����ȥե������ & [��]̾���ѥƥ����ȥե������ ������
		lngObjThis.value = g_aryMasterParent['id'];
		strObjThis.value = g_aryMasterParent['name'];

		// [HIDDEN][��]�������ѥƥ����ȥե������ & [HIDDEN][��]̾���ѥƥ����ȥե������ ������
		document.all.lngCodeParentHidden.value = g_aryMasterParent['id'];
		document.all.strNameParentHidden.value = g_aryMasterParent['name'];


		////////// �ʲ���[��]���� //////////
		if( lngObjThisH )
		{
			// [��]�������ѥƥ����ȥե������(disabled) ������
			lngObjThisH.value = lngObjThis.value;

			// [��]���쥯�ȥܥå����� ���ꥢ
			subLoadMasterOptionClear( m_objChild , true );

			// [��]�������ѥƥ����ȥե������ & [��]̾���ѥƥ����ȥե������ ���ꥢ
			lngObjThisChild.value = '';
			strObjThisChild.value = '';

			// [HIDDEN][��]�������ѥƥ����ȥե������ & [HIDDEN][��]̾���ѥƥ����ȥե������ ���ꥢ
			document.all.lngCodeChildHidden.value = '';
			document.all.strNameChildHidden.value = '';

			// [HIDDEN][��]�����󥿡����� ���ꥢ
			document.all.lngCounterChildHidden.value = '';
		}


	}

	return false;
}



////////// [���󥰥륯��å�][��]�ޥ������ǡ����򥵥֥�����ɥ���(����)�Υƥ����ȥե������ ������ //////////

// m_objValue		: [��]���쥯�ȥܥå���NAME ( obj.value )
// lngObjThis		: [��]�������ѥƥ����ȥե������NAME
// strObjThis		: [��]̾���ѥƥ����ȥե������NAME

function fncViewMasterDataChild( m_objValue , lngObjThis , strObjThis )
{

	if( m_objValue != '' )
	{

		// �ޥ������ǡ��������
		g_aryMasterChild = subLoadMasterGetIdName( objDataSourceSetting1.recordset , m_objValue );

		// [��]�������ѥƥ����ȥե������ & [��]̾���ѥƥ����ȥե������ ������
		lngObjThis.value = g_aryMasterChild['id'];
		strObjThis.value = g_aryMasterChild['name'];

		// [HIDDEN][��]�������ѥƥ����ȥե������ & [HIDDEN][��]̾���ѥƥ����ȥե������ ������
		document.all.lngCodeChildHidden.value = g_aryMasterChild['id'];
		document.all.strNameChildHidden.value = g_aryMasterChild['name'];

	}

	return false;
}



////////// [��]�������� �� [��]�ƥ������� �ΰ��׽��� //////////

// lngObjThis		: [��]�������ѥƥ����ȥե������NAME
// lngObjThisH		: [��]�������ѥƥ����ȥե������(disabled)NAME

function fncBothCodeCheck( lngObjThis , lngObjThisH )
{
	// [��]�������ѥƥ����ȥե�������� �� [��]�������ѥƥ����ȥե������(disabled)�� �ΰ��׽���
	// [��]�������ѥƥ����ȥե�������� �� �ѹ����졢
	// [��]�������ѥƥ����ȥե������(disabled)�ͤȰۤʤ���
	if( lngObjThis.value != document.all.lngCodeParentHidden.value )
	{
		// [��]�������ѥƥ����ȥե������(disabled)�� ���ꥢ
		lngObjThisH.value = '';
	}

	return false;
}



////////// ���ꥢ���� //////////

// m_obj			: [��]���쥯�ȥܥå���NAME
// lngObjThis		: [��]�������ѥƥ����ȥե������NAME
// strObjThis		: [��]̾���ѥƥ����ȥե������NAME
// lngObjThisH		: [��]�������ѥƥ����ȥե������(disabled)NAME
// m_objChild		: [��]���쥯�ȥܥå���NAME
// lngObjThisChild	: [��]�������ѥƥ����ȥե������NAME
// strObjThisChild	: [��]̾���ѥƥ����ȥե������NAME

function fncClearValue( m_obj , lngObjThis , strObjThis , lngObjThisH , m_objChild , lngObjThisChild , strObjThisChild )
{

	// [��]���쥯�ȥܥå����� ���ꥢ
	subLoadMasterOptionClear( m_obj , true );

	// [��]���쥯�ȥܥå����� ���ꥢ
	if( m_objChild )
	{
		subLoadMasterOptionClear( m_objChild , true );
	}

	// �ƥƥ����ȥե�������� ���ꥢ
	lngObjThis.value = '';
	strObjThis.value = '';

	// [HIDDEN]�ƥƥ����ȥե�������� ���ꥢ
	document.all.lngCodeParentHidden.value = '';
	document.all.strNameParentHidden.value = '';


	// �����󥿡����� ���ꥢ
	document.all.lngSearchResultCount.value = '';
	// [HIDDEN][��]�����󥿡����� ���ꥢ
	document.all.lngCounterParentHidden.value = '';


	////////// �ʲ���[��]���� //////////
	if( lngObjThisH )
	{
		// �ƥƥ����ȥե�������� ���ꥢ
		lngObjThisH.value = '';
		lngObjThisChild.value = '';
		strObjThisChild.value = '';

		// [HIDDEN]�ƥƥ����ȥե�������� ���ꥢ
		document.all.lngCodeChildHidden.value = '';
		document.all.strNameChildHidden.value = '';

		// [HIDDEN][��]�����󥿡����� ���ꥢ
		document.all.lngCounterChildHidden.value = '';
	}


	return false;
}






////////// �������̥ޥ�����������ɥ��������⥸�塼�� //////////

function fncSubMasterWinClose( strWindowType )
{

	// �������̡�������ɥ�������
	switch( strWindowType )
	{
		case 'vendor': // [VENDOR]

			parent.DisplayerM01( '' );
			parent.ExchangeM01( 0 , parent.window.Pwin );

			// �ե��������������
			parent.fncFocusObject( g_FocusObject[0] );
			break;


		case 'creation': // [CREATION FACTORY]

			parent.DisplayerM01_2( '' );
			parent.ExchangeM01_2( 0 , parent.window.Pwin );

			// �ե��������������
			parent.fncFocusObject( g_FocusObject[0] );
			break;


		case 'assembly': // [ASSEMBLY FACTORY]

			parent.DisplayerM01_3( '' );
			parent.ExchangeM01_3( 0 , parent.window.Pwin );

			// �ե��������������
			parent.fncFocusObject( g_FocusObject[0] );
			break;


		case 'dept': // [DEPT & IN CHARGE NAME]

			parent.DisplayerM02( '' );
			parent.ExchangeM02( 0 , parent.window.Pwin );

			// �ե��������������
			parent.fncFocusObject( g_FocusObject[0] );
			break;


		case 'products': // [PRODUCTS]

			parent.DisplayerM03( '' );
			parent.ExchangeM03( 0 , parent.window.Pwin );

			// �ե��������������
			parent.fncFocusObject( g_FocusObject[0] );
			break;


		case 'location': // [LOCATION]

			parent.DisplayerM04( '' );
			parent.ExchangeM04( 0 , parent.window.Pwin );

			// �ե��������������
			parent.fncFocusObject( g_FocusObject[0] );
			break;


		case 'applicant': // [APPLICANT]

			parent.DisplayerM05( '' );
			parent.ExchangeM05( 0 , parent.window.Pwin );

			// �ե��������������
			parent.fncFocusObject( g_FocusObject[0] );
			break;


		case 'wfinput': // [WF INPUT PERSON]

			parent.DisplayerM06( '' );
			parent.ExchangeM06( 0 , parent.window.Pwin );

			// �ե��������������
			parent.fncFocusObject( g_FocusObject[0] );
			break;


		case 'vi': // [VENDOR & IN CHARGE NAME]

			parent.DisplayerM07( '' );
			parent.ExchangeM07( 0 , parent.window.Pwin );

			// �ե��������������
			parent.fncFocusObject( g_FocusObject[0] );
			break;


		case 'supplier': // [SUPPLIER]

			parent.DisplayerM08( '' );
			parent.ExchangeM08( 0 , parent.window.Pwin );

			// �ե��������������
			parent.fncFocusObject( g_FocusObject[0] );
			break;

		case 'input': // [INPUT PERSON]

			parent.DisplayerM09( '' );
			parent.ExchangeM09( 0 , parent.window.Pwin );

			// �ե��������������
			parent.fncFocusObject( g_FocusObject[0] );
			break;


		default:
			break;
	}

	return false;
}







////////// [ENTER KEY]�������̥ޥ�����������ɥ��������⥸�塼�� //////////

function fncSubMasterWinCloseforEnterKey( strWindowType )
{

	// �������̡�������ɥ�������
	switch( strWindowType )
	{
		case 'vendor': // [VENDOR]

			parent.DisplayerM01( 1 );
			parent.ExchangeM01( 1, parent.window.Pwin );

			// �ե��������������
			parent.fncFocusObject( g_FocusObject[0] );
			break;


		case 'creation': // [CREATION FACTORY]

			parent.DisplayerM01_2( 1 );
			parent.ExchangeM01_2( 1 , parent.window.Pwin );

			// �ե��������������
			parent.fncFocusObject( g_FocusObject[0] );
			break;


		case 'assembly': // [ASSEMBLY FACTORY]

			parent.DisplayerM01_3( 1 );
			parent.ExchangeM01_3( 1 , parent.window.Pwin );

			// �ե��������������
			parent.fncFocusObject( g_FocusObject[0] );
			break;


		case 'dept': // [DEPT & IN CHARGE NAME]

			parent.DisplayerM02( 1 );
			parent.ExchangeM02( 1 , parent.window.Pwin );

			// �ե��������������
			parent.fncFocusObject( g_FocusObject[0] );
			break;


		case 'products': // [PRODUCTS]

			parent.DisplayerM03( 1 );
			parent.ExchangeM03( 1 , parent.window.Pwin );

			// �ե��������������
			parent.fncFocusObject( g_FocusObject[0] );
			break;


		case 'location': // [LOCATION]

			parent.DisplayerM04( 1 );
			parent.ExchangeM04( 1 , parent.window.Pwin );

			// �ե��������������
			parent.fncFocusObject( g_FocusObject[0] );
			break;


		case 'applicant': // [APPLICANT]

			parent.DisplayerM05( 1 );
			parent.ExchangeM05( 1 , parent.window.Pwin );

			// �ե��������������
			parent.fncFocusObject( g_FocusObject[0] );
			break;


		case 'wfinput': // [WF INPUT PERSON]

			parent.DisplayerM06( 1 );
			parent.ExchangeM06( 1 , parent.window.Pwin );

			// �ե��������������
			parent.fncFocusObject( g_FocusObject[0] );
			break;


		case 'vi': // [VENDOR & IN CHARGE NAME]

			parent.DisplayerM07( 1 );
			parent.ExchangeM07( 1 , parent.window.Pwin );

			// �ե��������������
			parent.fncFocusObject( g_FocusObject[0] );
			break;


		case 'supplier': // [SUPPLIER]

			parent.DisplayerM08( 1 );
			parent.ExchangeM08( 1 , parent.window.Pwin );

			// �ե��������������
			parent.fncFocusObject( g_FocusObject[0] );
			break;

		case 'input': // [INPUT PERSON]

			parent.DisplayerM09( 1 );
			parent.ExchangeM09( 1 , parent.window.Pwin );

			// �ե��������������
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

	// �����󥿡����� ���ꥢ
	document.all.lngSearchResultCount.value = '';

	// [��]�����Ѥߥ����󥿡����ͤκ�����
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

	// �����󥿡����� ���ꥢ
	document.all.lngSearchResultCount.value = '';

	// [��]�����Ѥߥ����󥿡����ͤκ�����
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

	// �����󥿡����� ���ꥢ
	document.all.lngSearchResultCount.value = '';

	// [��]�����Ѥߥ����󥿡����ͤκ�����
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

	// �����󥿡����� ���ꥢ
	document.all.lngSearchResultCount.value = '';

	// [��]�����Ѥߥ����󥿡����ͤκ�����
	document.all.lngSearchResultCount.value = document.all.lngCounterChildHidden.value;

	return false;
}


//-->
