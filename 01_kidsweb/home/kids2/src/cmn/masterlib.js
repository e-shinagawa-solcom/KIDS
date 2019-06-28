
	// 
	//	�ե����복�ס�(JavaScript�ե�����)
	//	
	// �ǡ����Х���ɤε�ǽ�����Ѥ��ơ������ɤ���̾�Τ�������ޤ�
	// 

	/*
		�Բ��������
		2004/03/09	K.Saito
			subLoadMasterHidden() �ؿ����ɲ�
			subLoadMasterCheckAlert() �ؿ����ɲ�
		
		2004/../..
	*/
	
	
	var g_objLoadMasterInForm;								// input���줿���֥�������
	var g_objLoadMasterOutForm   = new Array();   			// output �оݤΥ��֥�������
	var g_aryLoadMasterErrorFlag = new Array(); 			// new Boolean(false);		// ���顼�ե饰
	
	var g_strLoadMasterLoading   = "Loading...";			// �ǡ������å���³��
	var g_strLoadMasterNoData    = "(No Data)";				// �ǡ������å�0���
	var g_blnLoadMasterDebugFlag = new Boolean(false);		// �ǥХå��ե饰
	
	
	
	// ---------------------------------------------------------------------------
	//	���ס�
	//		subLoadMaster() �ƤӽФ����ƥ����ȥܥå���-���֥��������ѥ�åѡ�
	//	������
	//		strProcessID		-	����ID
	//		objInForm			-	���ϥ��֥�������
	//		objOutForm			-	������������ߥ��֥�������
	//		arySearchValue		-	�������͡������͡�
	//		objDataSource		-	DataSource���֥�������
	//		lngObjNo			-	���Ѥ��륪�֥��������ֹ�
	//		blnDebugFlag		-	�ǥХå��ե饰
	//	���͡�
	//							ʣ���Υǡ����Х���ɤ�onChange���Υ��٥�Ȥǰ��٤˻��Ѥ�����
	//							�����ֹ����Ѥ���ʬ�̤���
	//							���̵꤬������ 0
	// ---------------------------------------------------------------------------
	function subLoadMasterText(strProcessID, objInForm, objOutForm, arySearchValue, objDataSource, lngObjNo, blnDebugFlag )
	{
		// InForm, OutForm ���ͤ�����å�
		if( typeof( objInForm ) == 'undefined' || 
			typeof( objOutForm) == 'undefined' )
		{
			alert("WARNING!! miss match subLoadMasterText() arg object undefined");
			return false;
		}

		// ���֥�������No
		if( isNaN(lngObjNo) )
		{	// ���̵꤬������ 0
			lngObjNo = 0;
		}
		
		// �ǥХå��ե饰����
		g_blnLoadMasterDebugFlag = blnDebugFlag;
		
		// ���ϥ��֥�������
		g_objLoadMasterInForm = objInForm;

		// �������ͣ�����Ƭ��Ⱦ�ѥ��ڡ����������硢���
		strInFormVal = objInForm.value.replace(/^[\x20]+/, '');

		// �������ͣ���̵�����
		if( strInFormVal == '' )
		{
			objOutForm.value = '';
			return false;
		}

		// ������Υ��֥�������
		g_objLoadMasterOutForm[lngObjNo] = objOutForm;
		
		// �����ɤ���̾�Τ����
		subLoadMaster(strProcessID, arySearchValue, objDataSource );
		
		return true;
	}

	// ---------------------------------------------------------------------------
	//	���ס�
	//		subLoadMaster() �ƤӽФ���Hidden-���֥��������ѥ�åѡ�
	//	������
	//		strProcessID		-	����ID
	//		objInForm			-	���ϥ��֥�������
	//		objOutForm			-	������������ߥ��֥�������
	//		arySearchValue		-	�������͡������͡�
	//		objDataSource		-	DataSource���֥�������
	//		lngObjNo			-	���Ѥ��륪�֥��������ֹ�
	//		blnDebugFlag		-	�ǥХå��ե饰
	//	���͡�
	//							ʣ���Υǡ����Х���ɤ�onChange���Υ��٥�Ȥǰ��٤˻��Ѥ�����
	//							�����ֹ����Ѥ���ʬ�̤���
	//							���̵꤬������ 0
	// ---------------------------------------------------------------------------
	function subLoadMasterHidden(strProcessID, objInForm, objOutForm, arySearchValue, objDataSource, lngObjNo, blnDebugFlag )
	{
		// InForm, OutForm ���ͤ�����å�
		if(	typeof( objOutForm) == 'undefined' )
		{
			alert("WARNING!! miss match subLoadMasterText() arg object undefined");
			return false;
		}

		// ���֥�������No
		if( isNaN(lngObjNo) )
		{	// ���̵꤬������ 0
			lngObjNo = 0;
		}
		
		// �ǥХå��ե饰����
		g_blnLoadMasterDebugFlag = blnDebugFlag;
		
		// ������Υ��֥�������
		g_objLoadMasterOutForm[lngObjNo] = objOutForm;
		
		// �����ɤ���̾�Τ����
		subLoadMaster(strProcessID, arySearchValue, objDataSource );
		
		return true;
	}
	
	
	// ---------------------------------------------------------------------------
	// 
	// ���ס�subLoadMasterText() �� objInForm ��¸�ߥ����å�̵����
	//
	// ���͡����δؿ��ϡ�Script�⤫��θƤӽФ��˻��Ѥ��Ʋ�������
	//       UI ����θƤӽФ��ˤ�Ŭ�Ѥ��ʤ�����
	// ---------------------------------------------------------------------------
	function subLoadMasterValue(strProcessID, objInForm, objOutForm, arySearchValue, objDataSource, lngObjNo, blnDebugFlag )
	{
		
		// ���֥�������No
		if( isNaN(lngObjNo) )
		{	// ���̵꤬������ 0
			lngObjNo = 0;
		}

		// �ǥХå��ե饰����
		g_blnLoadMasterDebugFlag = blnDebugFlag;

		// ������Υ��֥�������
		g_objLoadMasterOutForm[lngObjNo] = objOutForm;

		// �����ɤ���̾�Τ����
		subLoadMaster(strProcessID, arySearchValue, objDataSource );

		return true;
	}
	

	// ---------------------------------------------------------------------------
	//	���ס�
	// 		subLoadMaster() �ƤӽФ������ץ����-���֥��������ѥ�åѡ�
	//	������
	//		strProcessID		-	����ID
	//		objInForm			-	�������ͣ��ʥ����ɡ����ϥ��֥�������
	//		objOutOption		-	������������ߥ��֥�������
	//		arySearchValue		-	�������͡������͡�
	//		objDataSource		-	DataSource���֥�������
	//		lngObjNo			-	���Ѥ��륪�֥��������ֹ�
	//		blnDebugFlag		-	�ǥХå��ե饰
	//	���͡�
	//							ʣ���Υǡ����Х���ɤ�onChange���Υ��٥�Ȥǰ��٤˻��Ѥ�����
	//							�����ֹ����Ѥ���ʬ�̤���
	//							���̵꤬������ 0
	// ---------------------------------------------------------------------------
	function subLoadMasterOption(strProcessID, objInForm, objOutOption, arySearchValue, objDataSource, lngObjNo, blnDebugFlag )
	{
		
		// SELECT���֥������ȤǤ�̵�����
		if( ( objOutOption.type != 'select-one' ) && ( objOutOption.type != 'select-multiple' ) )
		{
			alert("WARNING!! miss match subLoadMasterOption() arg object");
			return false;
		}
		
		// ���֥�������No
		if( isNaN(lngObjNo) )
		{	// ���̵꤬������ 0
			lngObjNo = 0;
		}

		// �ǥХå��ե饰����
		g_blnLoadMasterDebugFlag = blnDebugFlag;
		
		// ���ϥ��֥�������
		g_objLoadMasterInForm = objInForm;

		// ������Υ��֥�������
		g_objLoadMasterOutForm[lngObjNo] = objOutOption;

		// SELECT���֥������Ȥν����
		oOption = objOutOption;
		subLoadMasterOptionClear( oOption, true );
		oOption = document.createElement("OPTION");
		oOption.text = g_strLoadMasterLoading;
		oOption.value = "";
		objOutOption.add(oOption);
		
		// �����ɤ���̾�Τ����
		subLoadMaster(strProcessID, arySearchValue, objDataSource );
		
		return true;
	}


	// ---------------------------------------------------------------------------
	//	�����ɡ�̾�Ρ������ؿ�
	//
	//	������
	//		strProcessID	-	����ID (/lib/sql ���ˤ��� .sql ��������ե�����̾
	//		arySearchValue	-	�������͡������͡�
	//		objDataSource	-	DataSource���֥�������
	//
	// ---------------------------------------------------------------------------
	function subLoadMaster(strProcessID, arySearchValue, objDataSource )
	{
		
		// ���顼̵��������
		g_aryLoadMasterErrorFlag[0] = false;
		
		// ����ID�λ�����ǧ
		if( !strProcessID )
		{
			return false;
		}
		
		// �ѿ������
		strURL = "";	// URL�����Ǽ
		strGet = "";	// Get�����Ǽ
		
		// --------------------------------------------
		// �ǡ����������μ�����URL������
		// 
		// �ǡ����������Ȥʤ�URL������
		strURL = "/cmn/getmasterdata.php";
		strURL = strURL + "?lngProcessID=" + strProcessID;
		
		// ���������������ʬ���
		for( i = 0; i < arySearchValue.length; i++ )
		{
			strGet = strGet + "&strFormValue[" + String(i) + "]=" + arySearchValue[i];
		}
		
		// �����ץ�����GET������
		strURL = strURL + strGet;
		
		// �ǥХå�
		subLoadMasterDebug(location.protocol + '//' + location.hostname + strURL);
		// --------------------------------------------
alert(strURL);
		
		// �ǡ��������������
		objDataSource.charset = document.charset;
		objDataSource.UseHeader = "True";
		objDataSource.FieldDelim = "\t";
		objDataSource.dataurl  = strURL;
		objDataSource.reset();

		return true;
	}

	// ---------------------------------------------------------------------------
	//	�ǥХå��Ѥδؿ�
	//	������
	//		strValue	-	URL
	// ---------------------------------------------------------------------------
	function subLoadMasterDebug(strValue)
	{
		if( g_blnLoadMasterDebugFlag != true)
		{
			return true;
		}
		
		if( confirm('preview URL?') )
		{
			window.prompt('subLoadMasterDebug()', strValue);
		}
		if( confirm('preview datasource?') )
		{
			//ret = window.open(strValue, "debug_datasource", "location=yes,resizable=yes,width=600,height=300,scrollbars=yes,toolbar=yes");
			location.href = 'view-source:'+ strValue;
		}
		// window.prompt('subLoadMasterDebug()', strValue);
		// ret = showModelessDialog(strURL);
		return true;
	}

	// ---------------------------------------------------------------------------
	//	�쥳���ɥ��åȤ�����ꥪ�֥��������ͤؤ�����
	//
	//	������
	// 		objRst		-	�쥳���ɥ��åȥ��֥�������
	//		lngObjNo	-	���֥�������No
	//
	// ---------------------------------------------------------------------------
	function subLoadMasterSetting(objRst, lngObjNo)
	{
		// ���֥�������No
		if( isNaN(lngObjNo) )
		{	// ���̵꤬������ 0
			lngObjNo = 0;
		}

		// ���顼�ե饰������
		g_aryLoadMasterErrorFlag[lngObjNo] = true;

		// ----------------------------------------------------
		// �쥳���ɥ�����Ȥ�0�ʥǡ����������Ǥ��ʤ��ˤξ��
		// ----------------------------------------------------
		if( objRst.RecordCount == 0 )
		{
			
			if( g_objLoadMasterOutForm[lngObjNo].type == 'text' )
			{
				// �����ͤ�������֤ˤ�����������ͤ򥯥ꥢ����
				if( g_objLoadMasterInForm.style.visibility != 'hidden' ) g_objLoadMasterInForm.select();
				g_objLoadMasterOutForm[lngObjNo].value = "";
			}
			else if( ( g_objLoadMasterOutForm[lngObjNo].type == 'select-one' ) || ( g_objLoadMasterOutForm[lngObjNo].type == 'select-multiple' ) )
			{
				subLoadMasterOptionClear(g_objLoadMasterOutForm[lngObjNo], false);
				oOption = document.createElement("OPTION");
				oOption.text = g_strLoadMasterNoData;
				oOption.value = "";
				g_objLoadMasterOutForm[lngObjNo].add(oOption);
			}
			else if( g_objLoadMasterOutForm[lngObjNo].type == 'hidden' )
			{

				strOutFormName = g_objLoadMasterOutForm[lngObjNo].name;
				switch( strOutFormName )
				{
					// ���ʽ�ʣ�����å��ξ��
					case 'productequalcheck':
					// ����No.��ʣ�����å��ξ��
					case 'receivecodeequalcheck':
					
						// �ͤ�����
						g_objLoadMasterOutForm[lngObjNo].value = 0;
						break;
					
					default:
						// �����å����顼���ѤǤ�����
						if( strOutFormName.match(/^check_alert/) )
						{
							break;
						}
						
						alert('WARNING!! program switch Not found subLoadMasterSetting');
						break;
				}
				
			}
			return true;
		}


		// ----------------------------------------------------------------
		// TEXT���֥������Ȥξ��
		// ----------------------------------------------------------------
		if( g_objLoadMasterOutForm[lngObjNo].type == 'text' )
		{
			// name��ʬ�������������
			g_objLoadMasterOutForm[lngObjNo].value = objRst.Fields('name1');
		}
		
		// ----------------------------------------------------------------
		// SELECT���֥������Ȥξ��
		// ----------------------------------------------------------------
		else if( ( g_objLoadMasterOutForm[lngObjNo].type == 'select-one' ) || ( g_objLoadMasterOutForm[lngObjNo].type == 'select-multiple' ) )
		{
			// �оݤ�SELECT���֥������Ȥ�����
			subLoadMasterOptionClear( g_objLoadMasterOutForm[lngObjNo] );
			
			// �쥳���ɥ��åȤ�¸�ߤ�����
			if (objRst.recordcount)
			{
				objRst.MoveFirst();
				while (!objRst.EOF)
				{
					oOption = document.createElement("OPTION");
					oOption.value = objRst.fields("id").value;
					oOption.text  = objRst.fields("name1").value;
					// ����̾̾�Ρ�name2 �����ˤ�¸�ߤ��ʤ���硢�ƥ����ȿ����ѹ����롣��name2��SQL��̤ˤ�ä����椷�Ƥ����
					if( objRst.fields.count > 2 )
					{
						if( !objRst.fields("name2").value )
						{
							oOption.style.color = objRst.fields("name3").value;
						}
					}
					g_objLoadMasterOutForm[lngObjNo].add(oOption);
					objRst.MoveNext();
				}
				g_objLoadMasterOutForm[lngObjNo].disabled=false;
			}
		
		}
		
		// ----------------------------------------------------------------
		// HIDDEN ���֥������Ȥξ��
		// ----------------------------------------------------------------
		else if( g_objLoadMasterOutForm[lngObjNo].type == 'hidden' )
		{
			
			strOutFormName = g_objLoadMasterOutForm[lngObjNo].name;
			switch( strOutFormName )
			{
				// ���ʽ�ʣ�����å��ξ��
				case 'productequalcheck':
				// ����No.��ʣ�����å��ξ��
				case 'receivecodeequalcheck':

					// �ͤ�����
					g_objLoadMasterOutForm[lngObjNo].value = 0;
						
					// �쥳���ɥ��åȤ�¸�ߤ�����
					if (objRst.recordcount)
					{
						// �����SQL������̡ˤ�����
						objRst.MoveFirst();
						g_objLoadMasterOutForm[lngObjNo].value = parseInt(objRst.fields("id").value);
					}
					break;

				default:
					
					// �����å����顼���ѤǤ����� "check_alert..." �˥ޥå�
					if( strOutFormName.match(/^check_alert/) )
					{
						// �ͤ�����
						g_objLoadMasterOutForm[lngObjNo].value = "";
						
						// �쥳���ɥ��åȤ�¸�ߤ�����
						if (objRst.recordcount)
						{
							// �����SQL������̡ˤ�����
							objRst.MoveFirst();
							g_objLoadMasterOutForm[lngObjNo].value = objRst.fields("name1").value;
						}
						break;
					}

					alert('WARNING!! program switch Not found subLoadMasterSetting');
				break;

			}

		}
		
		// ----------------------------------------------------------------
		// �����褬Ƚ�곰�Υ��֥������Ȥξ��
		// ----------------------------------------------------------------
		else
		{
			alert("WARNING!! miss match subLoadMasterSetting-type: " + g_objLoadMasterOutForm[lngObjNo].type);
			return true;

		}
		
		// ���顼̵��������
		g_aryLoadMasterErrorFlag[lngObjNo] = false;
		return true;
		
	}


	// ---------------------------------------------------------------------------
	//	���ץ���󥪥֥������Ȥ����Ƥ򥯥ꥢ
	//
	//	������
	// 		objOption		-	���ץ���󥪥֥�������
	//		blnDisabled		-	�����ƥ��ֲ��ե饰
	//
	// ---------------------------------------------------------------------------
	function subLoadMasterOptionClear(objOption, blnDisabled)
	{
		if( ( objOption.type == 'select-one' ) || ( objOption.type == 'select-multiple' ) )
		{
			while (objOption.options.length) objOption.options.remove(0);
			
			if( blnDisabled ) objOption.disabled = true;
			return true;
		}
		return false;
	}


	// ---------------------------------------------------------------------------
	//	�쥳���ɥ��åȤ�����ꥪ�֥��������ͤؤ�����
	//
	//	������
	// 		objRst		-	�쥳���ɥ��åȥ��֥�������
	//		strSearchID	-	�����оݤ�ID��ʸ�����
	//
	// ---------------------------------------------------------------------------
	function subLoadMasterGetIdName(objRst, strSearchID)
	{
		aryMatch = false;
		
		// �쥳���ɥ��åȤ�¸�ߤ��ʤ����
		if (!objRst.recordcount)
		{
			return false;
		}

		// ��Ƭ�쥳���ɤذ�ư
		objRst.MoveFirst();
		
		while (!objRst.EOF)
		{
			// ����ID��Ʊ���id��쥳���ɥ��åȤ��鸡��
			if( objRst.fields("id").value == strSearchID )
			{
				// ���פ�����硢������ֵ�
				aryMatch = new Array();
				aryMatch['id'] = objRst.fields("id").value;
				if( objRst.fields("name2").value )
				{
					aryMatch['name'] = objRst.fields("name2").value;
				}
				else
				{
					aryMatch['name'] = '';
				}
				//alert("Match" + strSearchID);
				break;
			}
			objRst.MoveNext();
		}
		
		return aryMatch;
	}


	// ---------------------------------------------------------------------------
	//	���֥������������ͤΥ��顼�����å�
	//
	//	������
	// 		objForm		-	�������ͣ��ʥ����ɡˤ����Ϥ������֥�������
	//
	//  ̾�μ������˥��顼��ȯ���ʷ��0��ˤ�����硢�����͡ʥ����ɡˤ���������
	//  [0]�Υ��顼���Ф��ƤΤߡ����顼Ƚ���Ԥ�������ϡ�³���� Setting()��ƤФ줿�ݤ�
	//  ���顼�����������Ƥ��ޤ�������򤹤뤿��Ǥ���
	// ---------------------------------------------------------------------------
	function subLoadMasterCheck(objForm)
	{
		// ���顼��ȯ�����Ƥ������
		if( g_aryLoadMasterErrorFlag[0] == true )
		{
			// Ʊ�����ϥ��֥������Ȥξ��
			if( g_objLoadMasterInForm.name == objForm.name )
			{
				// �����ͤν����
				objForm.value = "";
				g_objLoadMasterInForm.value="";
				// ���顼�ե饰�ν����
				g_aryLoadMasterErrorFlag[0] = false;
				return true;
			}
		}
	}

	// ---------------------------------------------------------------------------
	//	���ס��ٹ��å�������ɽ�������롣
	//
	//	������
	// 		objAlert	-	alert��å��������ݻ�����Ƥ���(hidden)���֥�������
	//
	//  subLoadMasterText �ˤ� check_alert ���֥������Ȥ˥�å�����������
	//  ��å����������� /lib/sql/*.sql �˳�Ǽ
	// ---------------------------------------------------------------------------
	function subLoadMasterCheckAlert(objAlert)
	{
		if( typeof(objAlert) == 'undefined' )
		{
			return;
		}
		if( objAlert.value == '' )
		{
			return;
		}
		
		alert(objAlert.value);
	}
