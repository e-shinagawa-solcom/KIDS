


		//---------------------------------------------------------------------
		// ���� : �������
		//
		// @member Object [g_objMenu]		: ��˥塼���֥�������
		// @member Array  [g_arySubMenu]	: ��˥塼ɽ��ʸ��������
		// @member Array  [g_aryMenuPos]	: ��˥塼ɽ����ɸ����
		// @member Number [g_lngLang]		: ����ե饰
		//---------------------------------------------------------------------
		var g_objMenu;
		var g_arySubMenu;
		var g_aryMenuPos;
		var g_lngLang;




		//---------------------------------------------------------------------
		// ���� : ��˥塼�����󥹥ȥ饯��
		//
		// @param Object [objMenu]	: ��˥塼���֥�������
		//---------------------------------------------------------------------
		function clsSubMenu( objMenu )
		{
			// ��˥塼���֥������ȼ���
			this.g_objMenu = objMenu;

			// ��˥塼ɽ����ɸ��������
			this.g_aryMenuPos = new Array();

			with( this )
			{
				g_aryMenuPos['es']		= new Array();
				g_aryMenuPos['p']		= new Array();
				g_aryMenuPos['so']		= new Array();
				g_aryMenuPos['po']		= new Array();
				g_aryMenuPos['sc']		= new Array();
				g_aryMenuPos['pc']		= new Array();
				g_aryMenuPos['wf']		= new Array();
				g_aryMenuPos['inv']		= new Array();
				g_aryMenuPos['list']	= new Array();
				g_aryMenuPos['mm']		= new Array();
				g_aryMenuPos['mr']		= new Array();
				g_aryMenuPos['lc']		= new Array();

				g_aryMenuPos['es'][0]	= 24;
				g_aryMenuPos['es'][1]	= 354;

				g_aryMenuPos['p'][0]	= 184;
				g_aryMenuPos['p'][1]	= 354;

				g_aryMenuPos['so'][0]	= 344;
				g_aryMenuPos['so'][1]	= 354;

				g_aryMenuPos['po'][0]	= 504;
				g_aryMenuPos['po'][1]	= 354;

				g_aryMenuPos['sc'][0]	= 664;
				g_aryMenuPos['sc'][1]	= 354;

				g_aryMenuPos['pc'][0]	= 824;
				g_aryMenuPos['pc'][1]	= 354;

				g_aryMenuPos['wf'][0]	= 24;
				g_aryMenuPos['wf'][1]	= 424;
				
				g_aryMenuPos['inv'][0]	= 24;
				g_aryMenuPos['inv'][1]	= 424;

				g_aryMenuPos['list'][0]	= 184;
				g_aryMenuPos['list'][1]	= 424;

				g_aryMenuPos['mm'][0]	= 504;
				g_aryMenuPos['mm'][1]	= 424;

				g_aryMenuPos['mr'][0]	= 664;
				g_aryMenuPos['mr'][1]	= 424;
				
				g_aryMenuPos['lc'][0]	= 824;
				g_aryMenuPos['lc'][1]	= 424;
			}
		}
		//---------------------------------------------------------------------
		// ���� : ��˥塼ɽ��ʸ��������
		//---------------------------------------------------------------------
		function fncInitArySubMenu()
		{
			// ����ե饰����
			this.g_lngLang = lngLanguageCode;

			// ��˥塼ɽ��ʸ������������
			this.g_arySubMenu = new Array();


			with( this )
			{
				g_arySubMenu['es']		= new Array();
				g_arySubMenu['p']		= new Array();
				g_arySubMenu['so']		= new Array();
				g_arySubMenu['po']		= new Array();
				g_arySubMenu['sc']		= new Array();
				g_arySubMenu['pc']		= new Array();
				g_arySubMenu['wf']		= new Array();
				g_arySubMenu['inv']		= new Array();
				g_arySubMenu['list']	= new Array();
				g_arySubMenu['mm']		= new Array();
				g_arySubMenu['mr']		= new Array();
				g_arySubMenu['lc']		= new Array();

				switch( g_lngLang )
				{
					case 0:
						g_arySubMenu['p'][0]	= 'REGISTRATION';
						g_arySubMenu['p'][1]	= 'SEARCH';

//						g_arySubMenu['es'][0]	= 'REGISTRATION';
						g_arySubMenu['es'][0]	= 'SEARCH';
						g_arySubMenu['es'][1]	= 'UPLOAD';

						g_arySubMenu['so'][0]	= 'REGISTRATION';
						g_arySubMenu['so'][1]	= 'SEARCH';

						g_arySubMenu['po'][0]	= 'REGISTRATION';
						g_arySubMenu['po'][1]	= 'SEARCH';

						g_arySubMenu['sc'][0]	= 'REGISTRATION';
						g_arySubMenu['sc'][1]	= 'SEARCH';

						g_arySubMenu['pc'][0]	= 'REGISTRATION';
						g_arySubMenu['pc'][1]	= 'SEARCH';

						g_arySubMenu['wf'][0]	= 'LIST';
						g_arySubMenu['wf'][1]	= 'SEARCH';

						g_arySubMenu['list'][0]	= 'PRODUCT PLAN';
						g_arySubMenu['list'][1]	= 'PO';
						g_arySubMenu['list'][2]	= 'ESTIMATE COST';

						g_arySubMenu['mm'][0]	= 'REGISTRATION';
						g_arySubMenu['mm'][1]	= 'SEARCH';

						g_arySubMenu['mr'][0]	= 'REGISTRATION';
						g_arySubMenu['mr'][1]	= 'SEARCH';
						break;

					case 1:
						g_arySubMenu['p'][0]	= '������Ͽ';
						g_arySubMenu['p'][1]	= '���ʸ���';

//						g_arySubMenu['es'][0]	= '���Ѹ�����Ͽ';
						g_arySubMenu['es'][0]	= '���Ѹ�������';
						g_arySubMenu['es'][1]	= '���������';
						g_arySubMenu['es'][2]	= '���åץ���';

						g_arySubMenu['so'][0]	= '������Ͽ';
						g_arySubMenu['so'][1]	= '������';

						g_arySubMenu['po'][0]	= 'ȯ����';
						g_arySubMenu['po'][1]	= 'ȯ��񸡺�';

						g_arySubMenu['sc'][0]	= '���(Ǽ�ʽ�)��Ͽ';
						g_arySubMenu['sc'][1]	= 'Ǽ�ʽ񸡺�';
						g_arySubMenu['sc'][2]	= '��帡��';

						g_arySubMenu['pc'][0]	= '������Ͽ';
						g_arySubMenu['pc'][1]	= '��������';

						g_arySubMenu['wf'][0]	= '�Ʒ����';
						g_arySubMenu['wf'][1]	= '�Ʒ︡��';
						
						g_arySubMenu['inv'][0]	= '�����ȯ��';
						g_arySubMenu['inv'][1]	= '����񸡺�';
						g_arySubMenu['inv'][2]	= '���ὸ��';

						g_arySubMenu['list'][0]	= '���ʲ�����';
						g_arySubMenu['list'][1]	= 'ȯ���';
						g_arySubMenu['list'][2]	= '���Ѹ�����';
						g_arySubMenu['list'][3]	= '�ⷿ�����';
						g_arySubMenu['list'][4]	= 'Ǽ����ɼ';
						g_arySubMenu['list'][5]	= '�����';

						g_arySubMenu['mm'][0]	= '�ⷿ������Ͽ';
						g_arySubMenu['mm'][1]	= '�ⷿ���򸡺�';

						g_arySubMenu['mr'][0]	= '�ⷿĢɼ��Ͽ';
						g_arySubMenu['mr'][1]	= '�ⷿĢɼ����';
						
						g_arySubMenu['lc'][0]	= 'L/C ����';
						g_arySubMenu['lc'][1]	= 'L/C �����ѹ�';
						g_arySubMenu['lc'][2]	= 'L/C �Խ�';
						g_arySubMenu['lc'][3]	= 'L/CĢɼ����';
						break;

					default:
						break;
				}
			}

			return false;
		}

		//---------------------------------------------------------------------
		// ���� : ��˥塼ɽ��
		//
		// @param  String [strMode]	: ��˥塼����ʸ����
		//---------------------------------------------------------------------
		function fncShowSubMenu( strMode )
		{
			// ��˥塼��ɸ��Ĵ����
			//var lngBuffXpos = 8;
			//var lngBuffYpos = 10;

			// ��˥塼ɽ��ʸ��������
			this.fncInitArySubMenu();

			// HTML����
			this.g_objMenu.innerHTML = this.fncGetSubMenuHTML( strMode, this.g_arySubMenu[strMode] );

			// ��˥塼��ɸĴ��
			this.g_objMenu.style.left = this.g_aryMenuPos[strMode][0] + 'px';
			this.g_objMenu.style.top  = this.g_aryMenuPos[strMode][1] + 'px';

			//this.g_objMenu.style.left = lngBuffXpos + window.event.clientX + 'px';
			//this.g_objMenu.style.top  = lngBuffYpos + window.event.clientY + 'px';
			//alert( this.g_objMenu.style.top );

			// ��˥塼ɽ��
			this.g_objMenu.style.display = 'block';

			return false;
		}


		//---------------------------------------------------------------------
		// ���� : ��˥塼��ɽ��
		//---------------------------------------------------------------------
		function fncHideSubMenu()
		{
			// ���֥�˥塼��¸�ߤ��ʤ���硢������λ
			if( typeof( this.g_objMenu ) == 'undefined' ) return;

			// ��˥塼��ɽ��
			this.g_objMenu.style.display = 'none';

			return false;
		}
		//---------------------------------------------------------------------
		// ���� : HTML����
		//
		// @param  String [strMode]		: ��˥塼����ʸ����
		// @param  Array  [arySubMenu]	: ��˥塼ɽ��ʸ��������
		//
		// @return String [strHTML]		: ��˥塼HTML
		//---------------------------------------------------------------------
		function fncGetSubMenuHTML( strMode, arySubMenu )
		{
			var i, j;
			var aryHTML = new Array();
			var strHTML = '';


			strHTML += '<div></div>';

			for( i = 0; i < arySubMenu.length; i++ )
			{
				// ��˥塼���ơ����������å�
				if( !this.fncGetSubMenuStatus( strMode, i ) ) continue;

				// Ref������HTML����
				aryHTML[i] = '<button onmouseover="fncChangeBtnBGCol( this, \'#bcbcbc\' ); return false;" onmouseout="fncChangeBtnBGCol( this, \'#dedede\' ); return false;" onclick="fncSubMenuLocation( \'' + this.fncGetSubMenuRef( strMode, i ) + '\' ); return false;">' + arySubMenu[i] + '</button><br>';
			}

			// HTMLʸ����η��
			strHTML += aryHTML.join( "" );

			delete aryHTML;

			return strHTML;
		}
		//---------------------------------------------------------------------
		// ���� : ��˥塼���ơ���������
		//
		// @param  String [strMode]		: ��˥塼����ʸ����
		// @param  Number [i]			: ��˥塼ɽ��ʸ���������ֹ�
		//
		// @return Number [lngStatus]	: ��˥塼���ơ�����
		//---------------------------------------------------------------------
		function fncGetSubMenuStatus( strMode, i )
		{
			var strStatus = '';
			var lngStatus = 0;

			// ��˥塼���ơ���������
			strStatus = eval( 'document.all.lngSubFlag_' + strMode + '_' + i ).value;

			lngStatus = Number( strStatus );

			return lngStatus;
		}
		//---------------------------------------------------------------------
		// ���� : ��˥塼Ref����
		//
		// @param  String [strMode]	: ��˥塼����ʸ����
		// @param  Number [i]		: ��˥塼ɽ��ʸ���������ֹ�
		//
		// @return String [strRef]	: ��˥塼Ref
		//---------------------------------------------------------------------
		function fncGetSubMenuRef( strMode, i )
		{
			var strRef = '';

			// ��˥塼Ref����
			strRef = eval( 'document.all.lngSubRef_' + strMode + '_' + i ).value;

			return strRef;
		}
		//---------------------------------------------------------------------
		// ���� : ��˥塼���������¹�
		//
		// @param  String [strURL]	: ��˥塼Refʸ����
		//---------------------------------------------------------------------
		function fncSubMenuLocation( strURL )
		{
			// ��˥塼���������¹�
			window.location.href = strURL;

			return false;
		}
		//---------------------------------------------------------------------
		// ���� : �ܥ����طʿ��ѹ�
		//
		// @param  Object [objBtn]		: �ܥ��󥪥֥�������
		// @param  String [strColor]	: �ѹ���
		//---------------------------------------------------------------------
		function fncChangeBtnBGCol( objBtn, strColor )
		{
			// �ܥ����طʿ��ѹ�
			objBtn.style.backgroundColor = strColor;

			return false;
		}
