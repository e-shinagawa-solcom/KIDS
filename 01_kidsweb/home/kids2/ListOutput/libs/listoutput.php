<?

/*
̾��: CListOutput ���饹

����: ����ե�����˴�Ť��ƥ�ץ졼���֤������ĤĽ��Ϥ���

*/

	// ���������פ����
	define("CLISTOUTPUT_COMMENT",	"#");		// ������
	define("CLISTOUTPUT_TYPE",		"%");		// ����������
	define("CLISTOUTPUT_DATANAME",	"&");		// �ǡ�������̾
	define("CLISTOUTPUT_REFERENCE",	"@");		// �ǡ�������
	define("CLISTOUTPUT_FILE",		"@@");		// �ե����뻲��
	define("CLISTOUTPUT_DATA",		"");		// �ǡ���
	
	// �֤������ꥹ��ư�����
	define("CLISTOUTPUT_REPLACE_ALL",	1);		// �֤������ꥹ�Ȥ�����ե��������Τ��֤�������
	define("CLISTOUTPUT_REPLACE_SQL",	2);		// �֤������ꥹ�Ȥ�����ե������SQL��ʬ�Τ��֤�������
	
	// EVAL�μ¹ԥ⡼�ɤ�ư�����
	define("CLISTOUTPUT_EVAL_LINE",		1);		// �����eval()��¹Ԥ���
	define("CLISTOUTPUT_EVAL_CLASS",	2);		// ���饹���������eval()����٤����¹Ԥ���
	
	// EVAL�Υ���å����ư�����
	define("CLISTOUTPUT_EVAL_CACHE_OFF",	1);		// ����å�������Ѥ��ʤ�
	define("CLISTOUTPUT_EVAL_CACHE_ON",		2);		// ����å�������Ѥ���
	
	// EVAL�Υ���å���ΤޤȤ�������ư�����(LoadConfig()���ˤޤȤ��EVAL�Υ���å�����������)
	define("CLISTOUTPUT_BIND_EVAL_CACHE_OFF",	1);		// �ޤȤ����������Ѥ��ʤ�
	define("CLISTOUTPUT_BIND_EVAL_CACHE_ON",	2);		// �ޤȤ����������Ѥ���
	
	// �ե�����񤭹��ߤκݤ�Verify�����Υ�٥�
	// 0: Verify�ʤ�
	// 1: �ե����륵���������å�
	// 2: �ե��������ƥ����å�
	define("CLISTOUTPUT_WRITE_VERIFY_LEVEL", 2);

	// �������󥳡��ǥ���
	define("CLISTOUTPUT_HANDLINGENCODING", "EUC-JP");


//
//	CListOutput���饹
//
class CListOutput{
	var $strOutputDir;				// ������ǥ��쥯�ȥ�
	var $strOutputFile;				// ���ϥե�����
	var $aryConfig;					// �������Ƥ���¸
	var $strConfigDir;				// ����ե���������ä��ǥ��쥯�ȥ�
	var $strConfigFile;				// ����ե�����
	var $aryResult;					// DB������������ǡ���������
	var $aryColumnResult;			// DB������������ǡ����Υ��������
	var $aryReplaceList;			// �ƥ�ץ졼���֤�����������
	var $aryColumnList;				// �ƥ�ץ졼���֤������ѥ��������
	var $strTemplateDir;			// �ƥ�ץ졼�ȤΤ���ǥ��쥯�ȥ�
	var $aryFileCache;				// �ƥ�ץ졼�ȥե�����Υ���å������¸
	var $strErrorMessage;			// �ǿ��Υ��顼��å���������¸
	var $lngMaxToken;				// ���Υ��饹������Ѳ�ǽ�ʺ���ȡ�����
	var $lngAlertToken;				// �Ĥ�ȡ����󤬤��ο��ʲ��ˤʤä���ٹ��ȯ����
	var $lngCurrentToken;			// �������ǻ��Ѥ����ȡ�����
	var $bytReplaceMode;			// �֤�����ư��
	var $bytEvalMode;				// EVAL�μ¹ԥ⡼��
	var $bytEvalCacheMode;			// EVAL�Υ���å���ư��⡼��
	var $aryEvalCache;				// EVAL�Υ���å���
	var $bytBindEvalCacheMode;		// EVAL����å���ΤޤȤ�����
	var $objContext;				// ListOutput�Υ��֥������Ȥ��������줿����������äƤ��륳��ƥ�����(��������¸���Ƥ�����)
	
	//	�ؿ�̾:		CListOutput
	//
	//	����:		���󥹥ȥ饯��
	//
	//	����:		�ʤ�
	//				
	//	�����:		�ʤ�
	//
	//	���顼:		�֤��ʤ�
	//
	function CListOutput(){
		// ������ǥ��쥯�ȥ꤬̤����ʤ�Хǥե���Ȥ��������
		if(empty($this->strOutputDir)){
			$this->strOutputDir = './';
		}
		
		// ����ǥ��쥯�ȥ꤬̤����ʤ�Хǥե���Ȥ��������
		if(empty($this->strConfigDir)){
			$this->strConfigDir = './';
		}

		// �ƥ�ץ졼�ȥǥ��쥯�ȥ꤬̤����ʤ�Хǥե���Ȥ��������
		if(empty($this->strTemplateDir)){
			$this->strTemplateDir = './';
		}
		
		// ����ե����뤬�������Ƥ���Хǡ���Ÿ����Ԥ�
		if(!empty($this->strConfigFile)){
			// ����ե����뤫��ǡ���Ÿ��
			$this->LoadConfigFile();
		}
		
		// �֤������ꥹ�Ȥν����
		if(!is_array($this->aryReplaceList) or !is_array($this->aryColumnList)){
			$this->ClearReplaceList();
		}
		
		// ����ȡ�����򥻥åȤ���
		$this->SetMaxToken(4000);
		
		// �ٹ��ȯ����Ĥ�ȡ�����򥻥åȤ���
		$this->lngAlertToken = 200;
		
		// ���Ѻѥȡ������ 0 �Ȥ���
		$this->SetCurrentToken(0);
		
		// �֤������ꥹ�ȤΥǥե����ư��
		$this->SetReplaceMode(CLISTOUTPUT_REPLACE_SQL);
		
		// EVAL�μ¹ԥ⡼�ɤΥǥե����ư��
		$this->SetEvalMode(CLISTOUTPUT_EVAL_LINE);
		
		// EVAL�Υ���å���ư��⡼��
		$this->SetEvalCacheMode(CLISTOUTPUT_EVAL_CACHE_ON);
		
		// EVAL����å������������
		$this->SetBindEvalCacheMode(CLISTOUTPUT_BIND_EVAL_CACHE_ON);
		
		// EVAL����å������������
		$this->ClearEvalCache();

		// ����ƥ����Ȥ���������
		$this->ClearContext();
	}
	
	//	�ؿ�̾:		ClearExecute
	//
	//	����:		�¹Է�̤ν������Ԥ�
	//
	//	����:		�ʤ�
	//
	//	�����:		TRUE:	���
	//
	//	���顼:		�֤��ʤ�
	//
	function ClearExecute(){
		// �¹Է�̤ν����
		$this->aryResult = array();
		// �¹Է�̤Υ��������ν����
		$this->aryColumnResult = array();
		
		return TRUE;
	}
	
	//	�ؿ�̾:		ClearConfig
	//
	//	����:		����ǡ����ν������Ԥ�
	//
	//	����:		�ʤ�
	//
	//	�����:		TRUE:	���
	//
	//	���顼:		�֤��ʤ�
	//
	function ClearConfig(){
		// ����ǡ����ν����
		unset($this->aryConfig);
		
		return TRUE;
	}
	
	//	�ؿ�̾:		ClearReplaceList
	//
	//	����:		�֤������ꥹ�Ȥν������Ԥ�
	//
	//	����:		�ʤ�
	//
	//	�����:		TRUE:	���
	//
	//	���顼:		�֤��ʤ�
	//
	function ClearReplaceList(){
		// �֤������ꥹ�Ȥν����
		$this->aryReplaceList = array();
		// �֤����������ꥹ�Ȥν����
		$this->aryColumnList = array();
		
		return TRUE;
	}
	
	//	�ؿ�̾:		ClearErrorMessage
	//
	//	����:		���顼��å������ν������Ԥ�
	//
	//	����:		�ʤ�
	//
	//	�����:		TRUE:	���
	//
	//	���顼:		�֤��ʤ�
	//
	function ClearErrorMessage(){
		// ���顼��å������ν����
		$this->strErrorMessage = '';
		
		return TRUE;
	}
	
	//	�ؿ�̾:		ClearEvalCache
	//
	//	����:		EVAL����å������������
	//
	//	����:		�ʤ�
	//
	//	�����:		TRUE:	���
	//
	//	���顼:		�֤��ʤ�
	//
	function ClearEvalCache(){
		// EVAL����å������������
		$this->aryEvalCache = array();
		
		return TRUE;
	}

	//	�ؿ�̾��	ClearContext
	//
	//	����:		����ƥ����Ȥ���������
	//
	//	����:		�ʤ�
	//
	//	�����:		TRUE:	���
	//
	//	���顼:		�֤��ʤ�
	//
	function ClearContext() {
		// ����ƥ����Ȥ���������
		$this->objContext = new CListOutputContext();

		return TRUE;
	}
	
	//	�ؿ�̾:		GetErrorMessage
	//
	//	����:		���顼��å��������������
	//
	//	����:		�ʤ�
	//
	//	�����:		�ǿ��Υ��顼��å�����������
	//
	//	���顼:		�֤��ʤ�
	//
	function GetErrorMessage(){
		// ���顼��å������μ���
		return $this->strErrorMessage;
	}
	
	//	�ؿ�̾:		GetCurrentToken
	//
	//	����:		���ѺѤߥȡ�����������
	//
	//	����:		�ʤ�
	//
	//	�����:		�ȡ������
	//
	//	���顼:		�֤��ʤ�
	//
	function GetCurrentToken(){
		// �ȡ�������μ���
		return $this->lngCurrentToken;
	}
	
	//	�ؿ�̾:		SetCurrentToken
	//
	//	����:		���ѺѤߥȡ�������򥻥å�
	//
	//	����:		�ȡ������
	//
	//	�����:		TRUE:	���
	//
	//	���顼:		�֤��ʤ�
	//
	function SetCurrentToken($lngCurrentToken){
		// �ȡ�������Υ��å�
		$this->lngCurrentToken = $lngCurrentToken;
		
		return TRUE;
	}
	
	//	�ؿ�̾:		ExportEvalCache
	//
	//	����:		EVAL����å���򥨥����ݡ��Ȥ���
	//
	//	����:		�ʤ�
	//
	//	�����:		EVAL����å�������
	//
	//	���顼:		�֤��ʤ�
	//
	function ExportEvalCache(){
		// EVAL����å���������������
		return $this->aryEvalCache;
	}
	
	//	�ؿ�̾:		ImportEvalCache
	//
	//	����:		EVAL����å���򥤥�ݡ��Ȥ���
	//
	//	����:		$aryEvalCache	EVAL����å�������
	//
	//	�����:		TRUE:	���
	//
	//	���顼:		�֤��ʤ�
	//
	function ImportEvalCache($aryEvalCache){
		// EVAL����å�������򥤥�ݡ��Ȥ���
		$this->aryEvalCache = $aryEvalCache;
		
		return TRUE;
	}
	
	//	�ؿ�̾:		GetEvalCache
	//
	//	����:		EVAL����å��夫�饪�֥������Ȥ��������
	//
	//	����:		$strScript	������ץ�
	//				&$objEvalClass	���饹���֥�������(�����)
	//
	//	�����:		TRUE:	��������
	//				FALSE:	��������(���顼�ǤϤʤ��ΤǸƤӽФ����ǥ��֥������Ȥ���������)
	//
	//	���顼:		�֤�
	//
	function GetEvalCache($strScript, &$objEvalClass){
		// ������ץȤΥ����������Ȥ��������
		$strScriptDigest = $this->GetDigest($strScript);
		
		if(isset($this->aryEvalCache[$strScriptDigest]['OBJECT']) == FALSE){
			// ¸�ߤ���Ϥ��Υ��֥������ȤΥ���å��夬¸�ߤ��ʤ�
			$this->strErrorMessage = 'No eval cache exist';
			return FALSE;
		}
		
		// EVAL����å��夫�饪�֥������Ȥ��������
		$objEvalClass = $this->aryEvalCache[$strScriptDigest]['OBJECT'];
		
		return TRUE;
	}
	
	//	�ؿ�̾:		SetEvalCache
	//
	//	����:		EVAL����å��夫�饪�֥������Ȥ򥻥åȤ���
	//
	//	����:		$strScript	������ץ�
	//				&$objEvalClass	���饹���֥�������
	//
	//	�����:		TRUE:	���
	//
	//	���顼:		�֤��ʤ�
	//
	function SetEvalCache($strScript, &$objEvalClass){
		// EVAL����å���⡼�ɤ�ͭ���ʤ�Х���å��夹��
		if($this->bytEvalCacheMode == CLISTOUTPUT_EVAL_CACHE_ON){
			// ������ץȤΥ����������Ȥ��������
			$strScriptDigest = $this->GetDigest($strScript);
			
			// ���饹�Υ�����ץȤ򥻥åȤ���
			$this->aryEvalCache[$strScriptDigest]['SCRIPT'] = $strScript;
			
			// ���饹�Υ��֥������Ȥ򥻥åȤ���
			$this->aryEvalCache[$strScriptDigest]['OBJECT'] = $objEvalClass;
		}
		
		return TRUE;
	}

	//	�ؿ�̾:		SetReplaceMode
	//
	//	����:		�֤������ꥹ��ư������򥻥åȤ���
	//
	//	����:		$bytReplaceMode	�֤������ꥹ��ư�����
	//					CLISTOUTPUT_REPLACE_ALL	�֤������ꥹ�Ȥ�����ե��������Τ��֤�������
	//					CLISTOUTPUT_REPLACE_SQL	�֤������ꥹ�Ȥ�����ե������SQL��ʬ�Τ��֤�������
	//
	//	�����:		TRUE:	���
	//
	//	���顼:		�֤��ʤ�
	//
	function SetReplaceMode($bytReplaceMode){
		// �֤������ꥹ�Ȥ�ư������򥻥å�
		$this->bytReplaceMode = $bytReplaceMode;
		
		return TRUE;
	}

	//	�ؿ�̾:		SetEvalMode
	//
	//	����:		EVAL�μ¹ԥ⡼�ɤ򥻥åȤ���
	//
	//	����:		$bytEvalMode	EVAL�μ¹ԥ⡼��
	//					CLISTOUTPUT_EVAL_LINE	�����eval()��¹Ԥ���
	//					CLISTOUTPUT_EVAL_CLASS	���饹���������eval()����٤����¹Ԥ���
	//
	//	�����:		TRUE:	���
	//
	//	���顼:		�֤��ʤ�
	//
	function SetEvalMode($bytEvalMode){
		// EVAL�μ¹ԥ⡼�ɤ򥻥å�
		$this->bytEvalMode = $bytEvalMode;
		
		return TRUE;
	}

	//	�ؿ�̾:		SetEvalCacheMode
	//
	//	����:		EVAL�Υ���å���ư��⡼�ɤ򥻥åȤ���
	//
	//	����:		$bytEvalCacheMode	EVAL�μ¹ԥ⡼��
	//					CLISTOUTPUT_EVAL_CACHE_OFF	����å�������Ѥ��ʤ�
	//					CLISTOUTPUT_EVAL_CACHE_ON	����å�������Ѥ���
	//
	//	�����:		TRUE:	���
	//
	//	���顼:		�֤��ʤ�
	//
	function SetEvalCacheMode($bytEvalCacheMode){
		// EVAL�μ¹ԥ⡼�ɤ򥻥å�
		$this->bytEvalCacheMode = $bytEvalCacheMode;
		
		// EVAL����å������������
		$this->ClearEvalCache();
		
		return TRUE;
	}

	//	�ؿ�̾:		SetBindEvalCacheMode
	//
	//	����:		EVAL����å���ΤޤȤ������⡼�ɤ򥻥åȤ���
	//
	//	����:		$bytBindEvalCacheMode	EVAL�ΤޤȤ������⡼��
	//					CLISTOUTPUT_BIND_EVAL_CACHE_OFF	�ޤȤ����������Ѥ��ʤ�
	//					CLISTOUTPUT_BIND_EVAL_CACHE_ON	�ޤȤ����������Ѥ���
	//
	//	�����:		TRUE:	���
	//
	//	���顼:		�֤��ʤ�
	//
	function SetBindEvalCacheMode($bytBindEvalCacheMode){
		// EVAL����å���ΤޤȤ������⡼�ɤ򥻥å�
		$this->bytBindEvalCacheMode = $bytBindEvalCacheMode;
		
		return TRUE;
	}

	//	�ؿ�̾:		LoadConfig
	//
	//	����:		����ƥ����Ȥ���ǡ���Ÿ��
	//
	//	����:		$strConfig		�ɤ߹�������ƥ�����
	//
	//	�����:		TRUE:	���ｪλ
	//				FALSE:	�۾ｪλ
	//
	//	���顼:		�֤�
	//
	function LoadConfig($strConfig){
		// ����ե�������Ф������֤�����ư��
		switch($this->bytReplaceMode){
			case CLISTOUTPUT_REPLACE_ALL:
				// CLISTOUTPUT_REPLACE_ALL �����ꤵ��Ƥ���������ƥ��������Τ��֤�������
				$strConfig = $this->ReplaceStrings($this->aryColumnList, $this->aryReplaceList, $strConfig);
				break;
		}
		
		// ����ǡ�����ä�
		$this->DeleteConfig();
		
		// <CR><LF>, <CR>, <LF> �����줫�Ƕ��ڤ�
		$aryConfigLine = split("\x0D\x0A|\x0A|\x0A", $strConfig);
		
		// �ǥե���Ȥν��������ס�����̾������
		$strNowType = 'DEFAULT';
		$strNowName = 'DEFAULT';
		
		// ���ƤιԤ��������
		reset($aryConfigLine);
		while(list($strKey, $strValue) = each($aryConfigLine)){
			if(preg_match('/^[ \n\r\v\f]*$/', $strValue)){
				// ���֤����äƤ��ʤ����Ԥ�̵��
				continue;
			}
			
			// �Ԥμ���
			list($strType, $strHandle) = split("\t", $strValue, 2);
			switch($strType){
				case CLISTOUTPUT_COMMENT:
					// ������
					break;
				case CLISTOUTPUT_TYPE:
					// ���������פ�����
					$strNowType = $strHandle;
					break;
				case CLISTOUTPUT_DATANAME:
					// �ǡ�������̾������
					$strNowName = $strHandle;
					// ���������ѤΥ���ǥå����κ���
					if(isset($this->aryConfig['INDEX'][$strNowName]) == FALSE){
						$this->aryConfig['INDEX'][$strNowName] = 1;
					}
					break;
				case CLISTOUTPUT_REFERENCE:
					// �ǡ�������
					// �ͤ����äƤ�������ԤǶ��ڤ�
					if(isset($this->aryConfig[$strNowType][$strNowName])){
						$this->aryConfig[$strNowType][$strNowName] .= "\n";
					}
					// ���Ȥ��Ƥ����ͤ��Ф�
					$this->aryConfig[$strNowType][$strNowName] .= $this->aryConfig[$strNowType][$strHandle];
					break;
				case CLISTOUTPUT_FILE:
					// �ե����뻲��
					// ����ե�������ɤ߹���
					if(!$this->LoadFile($this->strConfigDir . trim($strHandle), $strSubConfig)){
						// ���顼
						// LoadConfigFile()�����顼���֤��ΤǤ����Ǥϥ��顼��������ʤ�
						return FALSE;
					}
					// ����ե�������Ф������֤�����ư��
					switch($this->bytReplaceMode){
						case CLISTOUTPUT_REPLACE_ALL:
							// ����ե�����Υ�������֤�����
							$strSubConfig = $this->ReplaceStrings($this->aryColumnList, $this->aryReplaceList, $strSubConfig);
							break;
					}
					
					// �ͤ����äƤ�������ԤǶ��ڤ�
					if(isset($this->aryConfig[$strNowType][$strNowName])){
						$this->aryConfig[$strNowType][$strNowName] .= "\n";
					}
					// �ե���������ͤ򥻥åȤ���
					$this->aryConfig[$strNowType][$strNowName] .= $strSubConfig;
					break;
				case CLISTOUTPUT_DATA:
					// �ǡ����Υ��å�
					// �ͤ����äƤ�������ԤǶ��ڤ�
					if(isset($this->aryConfig[$strNowType][$strNowName])){
						$this->aryConfig[$strNowType][$strNowName] .= "\n";
					}
					$this->aryConfig[$strNowType][$strNowName] .= $strHandle;
					break;
				default:
					break;
			}
		}
		
		// EVAL����å���ΤޤȤ�������Ԥ�
		if($this->CreateBindEvalCache() == FALSE){
			// CreateBindEvalCache()�Υ��顼��å������򤽤Τޤ����Ѥ���
			return FALSE;
		}
		
		return TRUE;
	}

	//	�ؿ�̾:		LoadConfigFile
	//
	//	����:		����ե����뤫��ǡ���Ÿ��
	//
	//	����:		$strCodeEncoding	�ɤ߹�������ƥ�����
	//
	//	�����:		TRUE:	���ｪλ
	//				FALSE:	�۾ｪλ
	//
	//	���顼:		�֤�
	//
	function LoadConfigFile($strCodeEncoding = CLISTOUTPUT_HANDLINGENCODING){
		if(!$this->LoadFile($this->strConfigDir . $this->strConfigFile, $strConfig, $strCodeEncoding)){
			// ���顼
			// LoadConfigFile()�����顼���֤��ΤǤ����Ǥϥ��顼��������ʤ�
			return FALSE;
		}
		
		// �ɤ߹�������Ƥ���ǡ���Ÿ��
		if(!$this->LoadConfig($strConfig)){
			// LoadConfig()�Υ��顼��å������򤽤Τޤ޻��Ѥ���
			return FALSE;
		}
		
		return TRUE;
	}

	//	�ؿ�̾:		DeleteConfig
	//
	//	����:		����ǡ���������
	//
	//	����:		�ʤ�
	//
	//	�����:		TRUE:	���
	//
	//	���顼:		�֤��ʤ�
	//
	function DeleteConfig(){
		// ����ǡ���������
		$this->aryConfig = array();
		
		return TRUE;
	}

	//	�ؿ�̾:		SetConfigDir
	//
	//	����:		����ե�����Υǥ��쥯�ȥ�򥻥åȤ���
	//
	//	����:		$strDir		�ǥ��쥯�ȥ�ѥ�
	//
	//	�����:		TRUE:	���
	//
	//	���顼:		�֤��ʤ�
	//
	function SetConfigDir($strDir){
		$this->strConfigDir = $strDir;
		
		return TRUE;
	}
	
	//	�ؿ�̾:		SetTemplateDir
	//
	//	����:		�ƥ�ץ졼�ȤΥǥ��쥯�ȥ�򥻥åȤ���
	//
	//	����:		$strDir		�ǥ��쥯�ȥ�ѥ�
	//
	//	�����:		TRUE:	���
	//
	//	���顼:		�֤��ʤ�
	//
	function SetTemplateDir($strDir){
		$this->strTemplateDir = $strDir;
		
		return TRUE;
	}
	
	//	�ؿ�̾:		SetMaxToken
	//
	//	����:		���Ѳ�ǽ�ʺ���ȡ�����������ꤹ��
	//
	//	����:		$lngMaxToken		����ȡ�����
	//
	//	�����:		TRUE:	���
	//
	//	���顼:		�֤��ʤ�
	//
	function SetMaxToken($lngMaxToken){
		$this->lngMaxToken = $lngMaxToken;
		
		return TRUE;
	}
	
	//	�ؿ�̾:		CreateChildObject
	//
	//	����:		�ҥ��֥������Ȥ���
	//
	//	����:		�ʤ�
	//
	//	�����:		���������ҥ��֥�������
	//
	//	���顼:		�֤��ʤ�
	//
	function CreateChildObject(){
		// �ҥ��֥������Ȥ���
		$objChildObject = $this;
		
		// �¹Է�̤ν����
		$objChildObject->ClearExecute();
		
		// ���顼��å������ν����
		$objChildObject->ClearErrorMessage();
		
		// ����ǡ����ν����
		$objChildObject->ClearConfig();
		
		// �֤������ꥹ�Ȥν����
		$objChildObject->ClearReplaceList();

		return $objChildObject;
	}
	
	
	
	//	�ؿ�̾:		ListExecute
	//
	//	����:		SQL���ƥ�ץ졼���֤�����������¹�
	//
	//	����:		&$objDatabase		�ǡ����١������֥�������
	//				&$strPage			���Ϸ��(�����)
	//				$bytInitializePageContextFlag	�ڡ�������ƥ����Ȥν������ԤäƤ���¹Ԥ���(Default: TRUE)
	//
	//	�����:		TRUE:	���ｪλ
	//				FALSE:	�۾ｪλ
	//
	//	���顼:		�֤�
	//
	function ListExecute(&$objDatabase, &$strPage, $bytInitializePageContextFlag = TRUE){
		// ����μ¹Է�̤Υ��ꥢ
		$this->ClearExecute();
		if($bytInitializePageContextFlag == TRUE) {
			// �ڡ�������ƥ����Ȥν����
			$this->objContext->initializePageContext();
		}
		
		// �֤��ͤν����
		$strPage = '';
		
		// ����ǤϤʤ����ޤ��� count() �� 0 �ʤ�Х��顼
		if(!is_array($this->aryConfig) or count($this->aryConfig) == 0){
			// ���꤬¸�ߤ��ʤ����顼
			$this->strErrorMessage = 'No config data exist';
			return FALSE;
		}
		
		// �ƥ�ץ졼�Ȥ��ɤ߹���
		reset($this->aryConfig['INDEX']);
		while(list($strTempName) = each($this->aryConfig['INDEX'])){
			// ��Ƭ�ν���̾�μ��Ф�
			$aryName[] = $strName = $strTempName;
			
			if(empty($this->aryConfig['SQL'][$strName])){
				// �ƥ�ץ졼�ȤϤ��뤬��SQL������̤����ʤΤǽ����ʤ�
				continue;
			}
			
			// �ѿ��ν����
			unset($aryTemplate);
			unset($intFinishLine);
			unset($intLine);
			while(1){
				// ����å���λ�������
				$bolUseCache = ($this->aryConfig['CACHE'][$strName] == 1) ? TRUE : FALSE;
				
				// �ڡ���ɽ���˻��Ѥ���ƥ�ץ졼�Ȥ��ɤ߹���
				if(empty($this->aryConfig['TEMPLATESTRING'][$strName]) == FALSE){
					// ����ե������ľ�ܤ����줿ʸ�����ƥ�ץ졼�ȤȤ���
					$aryTemplate[] = empty($this->aryConfig['TEMPLATESTRING'][$strName]) ? '' : $this->aryConfig['TEMPLATESTRING'][$strName];
				}
				elseif(empty($this->aryConfig['RESULTTEMPLATE'][$strName]) == FALSE){
					// �¹Է�̤�ƥ�ץ졼�ȤȤ������Ѥ���
					$aryTemplate[] = empty($this->aryResult[$this->aryConfig['RESULTTEMPLATE'][$strName]]) ? '' : $this->aryResult[$this->aryConfig['RESULTTEMPLATE'][$strName]];
				}
				elseif(!$this->LoadFileWithCacheControl($this->strTemplateDir . trim($this->aryConfig['TEMPLATE'][$strName]), $aryTemplate[], $bolUseCache)){
					// �ƥ�ץ졼���ɤ߹��ߥ��顼
					// LoadFileWithCacheControl()�Υ��顼��å���������Ѥ���
					return FALSE;
				}
				
				// �Ǿ���OFFSET�μ��Ф�
				if(empty($this->aryConfig['OFFSET'][$strName])){
					// OFFSET̤���
					$intLine = 0;
				}
				else{
					// OFFSET���������Ƥ���
					// $intLine ���������Ƥ��ʤ��ޤ��Ϻ����OFFSET��$intLine��꾮��������OFFSET�򥻥åȤ���
					if((isset($intLine) == FALSE) or ($intLine > $this->aryConfig['OFFSET'][$strName])){
						// OFFSET�Υ��å�
						$intLine = $this->aryConfig['OFFSET'][$strName];
					}
				}
				
				// ������Ф��Կ����¤�����
				if(empty($this->aryConfig['LIMIT'][$strName])){
					// ���Ф��Կ����¤ʤ�
					$intFinishLine = 0;
				}
				else{
					// ���Ф��Կ����¤���
					// ����Υ��ե��åȤ����
					$intThisOffset = (isset($this->aryConfig['OFFSET'][$strName]) == TRUE) ? $this->aryConfig['OFFSET'][$strName] : 0;
					if((isset($intFinishLine) == FALSE) or ($intFinishLine != 0 and $intFinishLine < ($intThisOffset + $this->aryConfig['LIMIT'][$strName]))){
						$intFinishLine = $intThisOffset + $this->aryConfig['LIMIT'][$strName];
					}
				}
				
				// �ѥ���SQL�θ���
				if($this->aryConfig['PARALLEL'][$strName] == 1){
					if((list($strTempName) = each($this->aryConfig['INDEX'])) == FALSE){
						// �ѥ���SQL�ΤϤ������Υ֥�å���¸�ߤ��ʤ�
						$this->strErrorMessage = 'Invalid parallel SQL : ' . $strName;
						return FALSE;
					}
					
					// ���ν���̾�򥻥å�
					$aryName[] = $strName = $strTempName;
				}
				else{
					// �ѥ���SQL��¸�ߤ��ʤ��Τǥƥ�ץ졼���ɤ߹��߽�λ
					break;
				}
			}
			
			// �ѥ���SQL����Ƭ�ν���̾���Ф�
			$strName = $aryName[0];
			
			// ����ե�������Ф������֤�����ư��
			switch($this->bytReplaceMode){
				case CLISTOUTPUT_REPLACE_ALL:
				case CLISTOUTPUT_REPLACE_SQL:
				default:
					// SQL�Υ�������֤�����
					$strSQL = $this->ReplaceStrings($this->aryColumnList, $this->aryReplaceList, $this->aryConfig['SQL'][$strName]);
					break;
			}
			
			// SQL�Υ�������֤������ʾ�Υ֥�å��Ǥμ¹Է�̤���������
			if(is_array($this->aryColumnResult) and is_array($this->aryResult)){
				$strSQL = $this->ReplaceStrings($this->aryColumnResult, $this->aryResult, $strSQL);
			}
			
			// SQL�μ¹�
			$strResultID = $objDatabase->Execute($strSQL);
			if($strResultID == FALSE){
				// SQL�¹ԥ��顼
				$this->strErrorMessage = 'Invalid SQL : ' . $strSQL;
				return FALSE;
			}
			// �¹ԥ���ƥ����Ȥ���������
			$this->objContext->initializeExecuteContext();
			
			// �Ԥ��Ȥ˼��Ф�
			unset($aryReturnValue);
			unset($arySQLResult);
			unset($aryCount);
			unset($aryNoRepeat);
			unset($aryNoRepeatColumn);
			
			// NOREPEAT�����ꤵ��Ƥ�����ϥƥ�ץ졼�Ȥ��Ѱդ��Ƥ���
			reset($aryTemplate);
			while(list($intKey) = each($aryTemplate)){
				if(trim($this->aryConfig['NOREPEAT'][$aryName[$intKey]]) == 1 or
						(empty($this->aryConfig['NOREPEAT'][$aryName[$intKey]]) != TRUE and isset($this->aryConfig['NOREPEATVALUE'][$aryName[$intKey]]))) {
					$aryReturnValue[$intKey][0] = $aryTemplate[$intKey];
				}
			}
			
			// EVAL�Υ��饹�������ؼ�����Ƥ����饯�饹��������뤳�Ȥ��Ǥ���
			// ��������CheckClassToken()�ǥ��饹�ѤΥȡ����󤬻ĤäƤ��뤫�����å�����ɬ�פ�����
			if($this->bytEvalMode == CLISTOUTPUT_EVAL_CLASS and $this->CheckClassToken() == TRUE){
				// EVAL���������Ƥ�����Ǥ�դ�PHP�������򸵤˥��饹����������
				if(strlen(trim($this->aryConfig['EVAL'][$strName])) > 0){
					// EVAL�Υ��饹���֥������Ȥ��������
					if($this->GetClassObject($this->aryConfig['EVAL'][$strName], $objEvalClass) == FALSE){
						// ���顼ȯ��
						// GetClassObject()�Υ��顼��å������򤽤Τޤ޻��Ѥ���
						// ���ID���Ĥ���
						$objDatabase->FreeResult($strResultID);
						return FALSE;
					}
				}
			}
			
			while($objDatabase->SafeFetch($strResultID, $arySQLResult, $intLine)){
				// �ԥ�����ȤΥ��󥯥����
				$intLine++;
				
				// LIMIT�ˤ�뽪λȽ��
				if($intFinishLine != 0 and $intFinishLine < $intLine){
					// LIMIT���ͤ�ã�����Τǽ�λ
					break;
				}
				
				// �Կ�������̿��
				if(empty($this->aryConfig['ROWNUM'][$strName]) == FALSE){
					// ���ֹ����
					$arySQLResult[$this->aryConfig['ROWNUM'][$strName]] = $intLine;
				}
				
				// EVAL���������Ƥ�����Ǥ�դ�PHP��������¹Ԥ���
				if(isset($objEvalClass) == TRUE){
					if($objEvalClass->ExtendSQLResult($arySQLResult, $this->objContext) == FALSE){
						// eval���������줿�ؿ���ǥ��顼ȯ��
						$this->strErrorMessage = 'Eval script returns false : ' . $this->aryConfig['EVAL'][$strName];
						// ���ID���Ĥ���
						$objDatabase->FreeResult($strResultID);
						return FALSE;
					}
				}
				elseif(strlen(trim($this->aryConfig['EVAL'][$strName])) > 0){
					// ���Ѻѥȡ����������å�����
					if($this->lngCurrentToken >= $this->lngMaxToken){
						// �ȡ�����λȤ��������顼
						$this->strErrorMessage = 'Reach max token cache : ' . $this->lngMaxToken;
						// ���ID���Ĥ���
						$objDatabase->FreeResult($strResultID);
						return FALSE;
					}
					
					// ���顼��λ�ե饰��Ω�Ƥ�
					$bolErrorExit = TRUE;
					eval($this->aryConfig['EVAL'][$strName] . "\n" . '$bolErrorExit = FALSE;');
					// ���Ѻѥȡ�����򥤥󥯥����
					$this->lngCurrentToken++;
					
					if($bolErrorExit == TRUE){
						// eval��ǥ��顼ȯ��
						$this->strErrorMessage = 'Invalid script in eval : ' . $this->aryConfig['EVAL'][$strName];
						// ���ID���Ĥ���
						$objDatabase->FreeResult($strResultID);
						return FALSE;
					}
				}
				
				// ENCODEPREFIX ���������Ƥ����餹�٤Ƥη�̤��Ф���HTML�Υ��󥳡��ɤ�Ԥ�
				if(strlen(trim($this->aryConfig['ENCODEPREFIX'][$strName])) > 0){
					// OVERWRITE�ȤʤäƤ�������ܤ��񤭤���(���ԡ��ɸ���Τ���������ѿ�������)
					$strEncodePrefix = (strcmp($this->aryConfig['ENCODEPREFIX'][$strName], 'OVERWRITE') == 0) ? '' : $this->aryConfig['ENCODEPREFIX'][$strName];
					
					reset($arySQLResult);
					while(list($strSQLResultKey) = each($arySQLResult)){
						if(strlen($strEncodePrefix) > 0 and strcmp(substr($strSQLResultKey, 0, strlen($strEncodePrefix)), $strEncodePrefix) == 0){
							// ���Ǥ˥��󥳡��ɺѤߤΤ�Τ����Ф�
							continue;
						}
						// ����ǤϤʤ��ä�������򤹤�
						if(is_array($arySQLResult[$strSQLResultKey]) == FALSE){
							$arySQLResult[$strEncodePrefix . $strSQLResultKey] = htmlspecialchars($arySQLResult[$strSQLResultKey], ENT_QUOTES);
						}
					}
				}
				
				// CHILDOBJECT �����ꤵ��Ƥ������η�̤���ҥ��֥������Ȥ���
				if(strlen(trim($this->aryConfig['CHILDOBJECT'][$strName])) > 0){
					// ���ꤵ�줿����ե����뤫��ҥ��֥������Ȥ���
					$objChildObject = $this->CreateChildObject();
					
					// ����ƥ����Ȥ�����夲��(ParentsExecute -> ChildPage, ParentsPage -> ChildSession)
					$objChildObject->objContext->raise();

					// �ҥ��֥������Ȥ�SQL�η�̤��֤������ꥹ�ȤȤ��ƥ���ݡ��Ȥ���
					
					// CHILDOBJECTIMPORTREPLACELIST�����ꤵ��Ƥ�������import����(ͥ���̤Ϥɤ����褦������)
					if (trim($this->aryConfig['CHILDOBJECTIMPORTREPLACELIST'][$strName]) == 1) {
						$objChildObject->ImportReplaceList($this->array_merge($this->aryReplaceList, $arySQLResult));
					} else {
						// ����ʳ��Ϥ��Ĥ�ɤ���
						$objChildObject->ImportReplaceList($arySQLResult);
					}
					// �ҥ��֥������Ȥ�����ե�������ɤ߹���
					if($objChildObject->LoadConfig(trim($this->aryConfig['CHILDOBJECT'][$strName])) == FALSE){
						// �ҥ��֥������ȤΥ��顼���Ȥ���������
						$this->strErrorMessage = 'Child object returns : ' . $objChildObject->GetErrorMessage();
						// ���ID���Ĥ���
						$objDatabase->FreeResult($strResultID);
						return FALSE;
					}
					
					// �ҥ��֥������Ȥ�¹Ԥ���
					if($objChildObject->ListExecute($objDatabase, $strChildPage, FALSE) == FALSE){
						// �ҥ��֥������ȤΥ��顼���Ȥ���������
						$this->strErrorMessage = 'Child object returns : ' . $objChildObject->GetErrorMessage();
						// ���ID���Ĥ���
						$objDatabase->FreeResult($strResultID);
						return FALSE;
					}
					
					// ��̤��ɤ߹���
					$objChildObject->GetResult($aryChildResult);
					$arySQLResult = $this->array_merge($arySQLResult, $aryChildResult);
					
					// ���ѺѤߥȡ�����������
					$this->lngCurrentToken = $objChildObject->GetCurrentToken();
					
					// �ҥ��֥������Ȥ�EVAL����å�����������Ŭ�Ѥ���
					$this->ImportEvalCache($objChildObject->ExportEvalCache());

					// php�ΥС������4�ʲ��ξ��ϥ���ƥ����Ȥ򥳥ԡ�����
					if (phpversion() < 4) {
						// ����ƥ����Ȥ򥳥ԡ�����
						$this->objContext = $objChildObject->objContext;
					}
					// ����ƥ����Ȥ����������(ChildSession -> ParentsPage, ChildPage ->ParentsExecute)
					$this->objContext->lower();
					
					// �ҥ��֥������Ȥ�������
					unset($objChildObject);
				}
				
				// �֤������ѥ����ꥹ�Ȥ��������
				$arySQLColumn = $this->GetKeyArray($arySQLResult, '/_%', '%_/');
				
				// ��������֤�������Ԥ�
				reset($aryTemplate);
				while(list($intKey) = each($aryTemplate)){
					$lngLimit = $this->aryConfig['OFFSET'][$aryName[$intKey]] + $this->aryConfig['LIMIT'][$aryName[$intKey]];
					
					// LIMIT�ˤ�뽪λȽ��
					if($this->aryConfig['LIMIT'][$aryName[$intKey]] != '' and $lngLimit < $intLine){
						// LIMIT���ͤ�ã�����Τǽ�λ
						continue;
					}
					
					// OFFSET�ˤ��Ƚ��
					if($this->aryConfig['OFFSET'][$aryName[$intKey]] >= $intLine){
						// OFFSET�ʲ����ͤʤΤǼ���
						continue;
					}
					
					// NOREPEAT == 1 �����ꤵ��Ƥ�����ƥ�ץ졼�Ȥ򷫤��֤���Ʊ���ƥ�ץ졼�Ȥ�Ŭ��
					if(trim($this->aryConfig['NOREPEAT'][$aryName[$intKey]]) == 1){
						// ��������Ǥ��Ȥ��֤����������Ʊ���ƥ�ץ졼�Ȥ�Ŭ�Ѥ����
						$aryReturnValue[$intKey][0] = $this->ReplaceStrings($arySQLColumn, $arySQLResult, $aryReturnValue[$intKey][0]);
					}
					elseif(empty($this->aryConfig['NOREPEAT'][$aryName[$intKey]]) != TRUE and isset($this->aryConfig['NOREPEATVALUE'][$aryName[$intKey]])) {
						for($intNoRepeatIndex = ''; TRUE; ++$intNoRepeatIndex) {
							if(empty($this->aryConfig['NOREPEAT' . $intNoRepeatIndex][$aryName[$intKey]]) == TRUE or isset($this->aryConfig['NOREPEATVALUE' . $intNoRepeatIndex][$aryName[$intKey]]) != TRUE) {
								break;
							}
							$aryNoRepeat[$intKey][$this->ReplaceStrings($arySQLColumn, $arySQLResult, $this->aryConfig['NOREPEAT' . $intNoRepeatIndex][$aryName[$intKey]])] = $this->ReplaceStrings($arySQLColumn, $arySQLResult, $this->aryConfig['NOREPEATVALUE' . $intNoRepeatIndex][$aryName[$intKey]]);
						}
						// NOREPEATLIMIT ���������Ƥ�����Ϥ���LIMIT����֤�������Ԥ�(�ʥ���)
						if(isset($this->aryConfig['NOREPEATLIMIT'][$aryName[$intKey]]) and $intLine % $this->aryConfig['NOREPEATLIMIT'][$aryName[$intKey]] == 0) {
							$aryNoRepeatColumn = $this->GetKeyArray($aryNoRepeat[$intKey], '/_%', '%_/');
							$aryReturnValue[$intKey][0] = $this->ReplaceStrings($aryNoRepeatColumn, $aryNoRepeat[$intKey], $aryReturnValue[$intKey][0]);
							unset($aryNoRepeat[$intKey]);
						}
					}
					else{
						if(count($aryReturnValue[$intKey]) > 0){
							// ���Ǥ��ͤ����äƤ����饻�ѥ졼���������
							$aryReturnValue[$intKey][] = $this->aryConfig['SEPARATOR'][$aryName[$intKey]];
						}
						// ��������Ǥ��Ȥ��֤���������
						$aryReturnValue[$intKey][] = $this->ReplaceStrings($arySQLColumn, $arySQLResult, $aryTemplate[$intKey]);
					}
					
					// ������Ȥ򥤥󥯥����
					$aryCount[$intKey]++;
				}
				
				// SQL�¹Է�̤ξõ�
				unset($arySQLResult);
			}
			
			// EVAL�Ѥ˺����������饹���֥������Ȥ�������
			unset($objEvalClass);
			
			// ������Ȥ� 0 �ν���ñ�̤��Ф��Ƥ��� NORECORD ���ͤ�Ŭ�Ѥ���
			reset($aryTemplate);
			while(list($intKey) = each($aryTemplate)){
				// NOREPEAT�����ꤵ��Ƥ������ϥ쥳���ɤ�̵ͭ�ˤ������ʤ��Τǽ�������
				if($aryCount[$intKey] <= 0
						and ($this->aryConfig['NOREPEAT'][$aryName[$intKey]] != 1 and (empty($this->aryConfig['NOREPEAT'][$aryName[$intKey]]) == TRUE or isset($this->aryConfig['NOREPEATVALUE'][$aryName[$intKey]]) != TRUE))){
					$aryReturnValue[$intKey][0] = empty($this->aryConfig['NORECORD'][$aryName[$intKey]]) ? '' : $this->aryConfig['NORECORD'][$aryName[$intKey]];
				}
				// $intKey ���Ф��� $aryNoRepeat ��¸�ߤ�����NOREPEAT���֤�������ʸ����������ˤ��ޤäƤ��롣
				if(isset($aryNoRepeat[$intKey])) {
					$aryNoRepeatColumn = $this->GetKeyArray($aryNoRepeat[$intKey], '/_%', '%_/');
					$aryReturnValue[$intKey][0] = $this->ReplaceStrings($aryNoRepeatColumn, $aryNoRepeat[$intKey], $aryReturnValue[$intKey][0]);
				}
			}
			
			
			// ���ID���Ĥ���
			$objDatabase->FreeResult($strResultID);
			
			if(is_array($aryReturnValue) == TRUE){
				reset($aryReturnValue);
				
				while(list($intKey) = each($aryReturnValue)){
					$strReturnValue = join('', $aryReturnValue[$intKey]);

					if(is_array($this->aryColumnResult) and is_array($this->aryResult)){
						$strReturnValue = $this->ReplaceStrings($this->aryColumnResult, $this->aryResult, $strReturnValue);
					}
					
					// �֤��ͤι���
					$strPage = $strReturnValue;
					
					// �ǡ�����Ͽ
					$this->aryResult[$aryName[$intKey]] = $strReturnValue;
					
					// ������ȿ��μ���
					if(empty($this->aryConfig['COUNT'][$aryName[$intKey]]) == FALSE){
						$this->aryResult[$this->aryConfig['COUNT'][$aryName[$intKey]]] = isset($aryCount[$intKey]) ? $aryCount[$intKey] : 0;
					}
					
					// �ǡ����Υ��������κƹ���
					$this->aryColumnResult = $this->GetKeyArray($this->aryResult, '/_%', '%_/');
				}
			}
			else{
				reset($aryName);
				while(list($intKey, $strName) = each($aryName)){
					if(isset($this->aryConfig['COUNT'][$aryName[$intKey]]) == TRUE){
						// �ǡ�����Ͽ
						if(empty($this->aryResult[$this->aryConfig['COUNT'][$aryName[$intKey]]]) == TRUE){
							$this->aryResult[$this->aryConfig['COUNT'][$aryName[$intKey]]] = 0;
						}
						
						// �ǡ����Υ��������κƹ���
						$this->aryColumnResult = $this->GetKeyArray($this->aryResult, '/_%', '%_/');
					}
				}
				$strPage = '';
			}
			
			reset($aryName);
			while(list($intKey, $strName) = each($aryName)){
				// �ǡ�����Ͽ
				if(isset($this->aryResult[$strName]) == FALSE){
					$this->aryResult[$strName] = '';
				}
				
				// �ǡ����Υ��������κƹ���
				$this->aryColumnResult = $this->GetKeyArray($this->aryResult, '/_%', '%_/');
			}
			
			unset($aryName);
		}
		
		// ����������
		return TRUE;
	}
	
	//	�ؿ�̾:		GetResult
	//
	//	����:		ListExecute(FileOutputExecute)��¹Ԥ�����˳Ƽ¹Է�̥ǡ�������̾���Ȥ˼�������
	//
	//	����:		&$aryResult		���������
	//
	//	��ջ���:	ListExecute(FileOutputExecute)��Ԥä�ľ��˹Ԥ�ʤ������������ʤ���ǽ��������ޤ���
	//
	//	�����:		TRUE:	���
	//
	function GetResult(&$aryResult){
		// ����ͤ�����
		$aryResult = $this->aryResult;
		
		return TRUE;
	}
	
	//	�ؿ�̾:		FileOutputExecute
	//
	//	����:		SQL���ƥ�ץ졼���֤�����������¹Ԥ��ƥե��������¸
	//
	//	����:		&$objDatabase		�ǡ����١������֥�������
	//
	//	�����:		TRUE:	���ｪλ
	//				FALSE:	�۾ｪλ
	//
	//	���顼:		�֤�
	//
	function FileOutputExecute(&$objDatabase){
		// �ޤ��Ͻ����¹�
		if(!$this->ListExecute($objDatabase, $strPage)){
			// ���顼ȯ��
			// ListExecute()�Υ��顼��å������򤽤Τޤ޻��Ѥ���
			return FALSE;
		}
		
		if(!$this->WriteFile($this->strOutputDir . $this->strOutputFile, $strPage)){
			// �񤭹��ߥ��顼
			// WriteFile()�Υ��顼��å������򤽤Τޤ޻��Ѥ���
			return FALSE;
		}
		
		return TRUE;
	}
	
	//	�ؿ�̾:		DeleteAnyConfig
	//
	//	����:		Ǥ�դ������������
	//
	//	����:		$strProcessType		����������
	//				$strProcessName		�ǡ�������̾
	//
	//	�����:		TRUE:	���
	//
	//	���顼:		�֤��ʤ�
	//
	function DeleteAnyConfig($strProcessType, $strProcessName){
		unset($this->aryConfig[$strProcessType][$strProcessName]);
		
		return TRUE;
	}
	
	//	�ؿ�̾:		DeleteFetchLimit
	//
	//	����:		����̾���б�����SQL�ǽ��Ϥ���쥳���ɿ����¤�������
	//
	//	����:		$strProcessName		�ǡ�������̾
	//
	//	�����:		TRUE:	���
	//
	//	���顼:		�֤��ʤ�
	//
	function DeleteFetchLimit($strProcessName){
		$this->DeleteAnyConfig('LIMIT', $strProcessName);
		
		return TRUE;
	}
	
	//	�ؿ�̾:		DeleteFetchOffset
	//
	//	����:		����̾���б�����SQL�ǽ��Ϥ���쥳���ɤΥ��ե��åȤ�������
	//
	//	����:		$strProcessName		�ǡ�������̾
	//
	//	�����:		TRUE:	���
	//
	//	���顼:		�֤��ʤ�
	//
	function DeleteFetchOffset($strProcessName){
		$this->DeleteAnyConfig('OFFSET', $strProcessName);
		
		return TRUE;
	}
	
	//	�ؿ�̾:		SetAnyConfig
	//
	//	����:		Ǥ�դ���������ꤹ��
	//
	//	����:		$strProcessType		����������
	//				$strProcessName		�ǡ�������̾
	//				$strSettingValue	���ꤹ����
	//
	//	�����:		TRUE:	���
	//
	//	���顼:		�֤��ʤ�
	//
	function SetAnyConfig($strProcessType, $strProcessName, $strSettingValue){
		$this->aryConfig[$strProcessType][$strProcessName] = $strSettingValue;
		
		return TRUE;
	}
	
	//	�ؿ�̾:		SetFetchLimit
	//
	//	����:		����̾���б�����SQL�ǽ��Ϥ���쥳���ɿ����¤����ꤹ��
	//
	//	����:		$strProcessName		�ǡ�������̾
	//				$intFetchLimit		�������¿�
	//
	//	�����:		TRUE:	���
	//
	//	���顼:		�֤��ʤ�
	//
	function SetFetchLimit($strProcessName, $intFetchLimit){
		$this->SetAnyConfig('LIMIT', $strProcessName, $intFetchLimit);
		
		return TRUE;
	}
	
	//	�ؿ�̾:		SetFetchOffset
	//
	//	����:		����̾���б�����SQL�ǽ��Ϥ���쥳���ɤΥ��ե��åȤ����ꤹ��
	//
	//	����:		$strProcessName		�ǡ�������̾
	//				$intFetchOffset		���ϥ��ե��å�
	//
	//	�����:		TRUE:	���
	//
	//	���顼:		�֤��ʤ�
	//
	function SetFetchOffset($strProcessName, $intFetchOffset){
		$this->SetAnyConfig('OFFSET', $strProcessName, $intFetchOffset);
		
		return TRUE;
	}
	
	//	�ؿ�̾:		SetOutputFile
	//
	//	����:		���ϥե�����򥻥å�
	//
	//	����:		�ʤ�
	//
	//	�����:		TRUE:	���
	//
	//	���顼:		�֤��ʤ�
	//
	function SetOutputFile($strFileName){
		$this->strOutputFile = $strFileName;
		
		return TRUE;
	}
	
	//	�ؿ�̾:		SetConfigFile
	//
	//	����:		����ե�����򥻥å�
	//
	//	����:		$strFileName	����ե�����̾
	//
	//	�����:		TRUE:	���ｪλ
	//				FALSE:	�۾ｪλ
	//
	//	���顼:		�֤��ʤ�
	//
	function SetConfigFile($strFileName){
		if($this->strConfigFile == $strFileName){
			// ����ե�����̾��Ʊ���ʤΤ��ɤ߹��ޤʤ�
			return TRUE;
		}
		
		$this->strConfigFile = $strFileName;
		
		// ����ե������Ÿ��
		if(!$this->LoadConfigFile()){
			// LoadConfigFile()�Υ��顼��å������򤽤Τޤ޻��Ѥ���
			return FALSE;
		}
		
		return TRUE;
	}
	
	//	�ؿ�̾:		GetKeyArray
	//
	//	����:		����Υ���̾�Τ���Ф�����������
	//
	//	����:		$aryOriginal		��Ȥʤ�����
	//				$strPrefix			����̾���ղä�����Ƭ��
	//				$strSuffix			����̾���ղä���������
	//
	//	�����:		����̾������
	//
	//	���顼:		�֤��ʤ�
	//
	function GetKeyArray(&$aryOriginal, $strPrefix = '', $strSuffix = ''){
		// ����ͤν����
		$aryResult = array();

		reset($aryOriginal);
		while(list($strKey, $strValue) = each($aryOriginal)){
			$aryResult[] = $strPrefix . $strKey . $strSuffix;
			$aryNew[$strKey] = $strValue;
		}
		// ���������������������κƹ���
		$aryOriginal = $aryNew;
		return $aryResult;
	}
	
	//	�ؿ�̾:		LoadFileWithCacheControl
	//
	//	����:		�ե�������ɤ߹���(�ե饰�ǥ���å����Ȥ������椹��)
	//
	//	����:		$strFileName			�ե�����̾
	//				&$strFileValue			�ɤ߹�����ե����������
	//				$bolCacheControlFlag	����å��������(TRUE: ON, FALSE: OFF)
	//				$strCodeEncoding		�ƥ�ץ졼�ȥ�����ʸ��������
	//
	//	�����:		TRUE:	���ｪλ
	//				FALSE:	�۾ｪλ
	//
	//	���顼:		�֤�
	//
	function LoadFileWithCacheControl($strFileName, &$strFileValue, $bolCacheControlFlag = FALSE, $strCodeEncoding = CLISTOUTPUT_HANDLINGENCODING){
		
		if($bolCacheControlFlag == TRUE){
			// ����å������
			if($this->LoadFileUseCache($strFileName, $strFileValue, $strCodeEncoding) == FALSE){
				// LoadFileUseCache()�Υ��顼��å������򤽤Τޤ޻��Ѥ���
				return FALSE;
			}
		}
		else{
			// ����å�����Ѥ���
			if($this->LoadFile($strFileName, $strFileValue, $strCodeEncoding) == FALSE){
				// LoadFile()�Υ��顼��å������򤽤Τޤ޻��Ѥ���
				return FALSE;
			}
		}
		
		// ���ｪλ
		return TRUE;
	}
	
	//	�ؿ�̾:		LoadFileUseCache
	//
	//	����:		��ǽ�ʤ饭��å�����Ѥ��ƥե�������ɤ߹���
	//
	//	����:		$strFileName		�ե�����̾
	//				&$strFileValue		�ɤ߹�����ե����������
	//				$strCodeEncoding	�ƥ�ץ졼�ȥ�����ʸ��������
	//
	//	�����:		TRUE:	���ｪλ
	//				FALSE:	�۾ｪλ
	//
	//	���顼:		�֤�
	//
	function LoadFileUseCache($strFileName, &$strFileValue, $strCodeEncoding = CLISTOUTPUT_HANDLINGENCODING){
		// ����å��夬¸�ߤ��뤫�����å�����
		if(empty($this->aryFileCache[$strFileName][$strCodeEncoding]) == FALSE){
			// ����å���˥ҥåȡ�
			
			// ����å��夫���ɤ߹���
			$strFileValue = $this->aryFileCache[$strFileName][$strCodeEncoding];
			
			// ���ｪλ
			return TRUE;
		}
		
		// ����å���˥ҥåȤ��ʤ��ä�����˥ե����뤫���ɤ߹���
		if($this->LoadFile($strFileName, $strFileValue, $strCodeEncoding) == FALSE){
			// LoadFile()�Υ��顼��å������򤽤Τޤ޻��Ѥ���
			return FALSE;
		}
		
		// ����å������¸
		$this->aryFileCache[$strFileName][$strCodeEncoding] = $strFileValue;
		
		// ���ｪλ
		return TRUE;
	}
	
	//	�ؿ�̾:		LoadFile
	//
	//	����:		�ե�������ɤ߹���
	//
	//	����:		$strFileName		�ե�����̾
	//				&$strFileValue		�ɤ߹�����ե����������
	//				$strCodeEncoding	�ƥ�ץ졼�ȥ�����ʸ��������
	//
	//	�����:		TRUE:	���ｪλ
	//				FALSE:	�۾ｪλ
	//
	//	���顼:		�֤�
	//
	function LoadFile($strFileName, &$strFileValue, $strCodeEncoding = CLISTOUTPUT_HANDLINGENCODING){
		// �ե������¸�ߥ����å�
		if(!file_exists($strFileName)){
			// �ե�����¸�ߤ���
			$this->strErrorMessage = 'No such file or directory : ' . $strFileName;
			return FALSE;
		}
		// �ɤߤȤ�ǥ����ץ�
		$fp = fopen($strFileName, 'rb');
		if($fp == FALSE){
			// �����ץ󥨥顼
			$this->strErrorMessage = 'File open failed : ' . $strFileName;
			return FALSE;
		}
		
		// �ե����륵�����Υ����å�
		$intFileSize = filesize($strFileName);
		if($intFileSize == FALSE){
			// �ե����륵������0�ޤ��ϼ������顼
			$this->strErrorMessage = 'Invalid file size : ' . $strFileName;
			return FALSE;
		}

		// �ɤ߹���
		$strFileValue = fread($fp, $intFileSize);

		// �ե�������Ĥ���
		fclose($fp);
		
		// ���������ɤ�ʸ���������Ѵ�
		$strFileValue = i18n_convert($strFileValue, i18n_internal_encoding(), $strCodeEncoding);

		return TRUE;
	}
	
	//	�ؿ�̾:		WriteFile
	//
	//	����:		�ե������񤭹���Verify��Ԥ�
	//
	//	����:		$strFileName		�ե�����̾
	//				$strFaileValue		�񤭹���ե����������
	//				$strWriteEncoding	�񤭹���ʸ��������
	//
	//	�����:		TRUE:	���ｪλ
	//				FALSE:	�۾ｪλ
	//
	//	���顼:		�֤�
	//
	function WriteFile($strFileName, $strFileValue, $strWriteEncoding = CLISTOUTPUT_HANDLINGENCODING){
		// �ե������¸�ߥ����å�
		if(file_exists($strFileName)){
			// ���Ǥ˥ե�����¸�ߤ��륨�顼
			$this->strErrorMessage = 'Already exist : ' . $strFileName;
			return FALSE;
		}
		
		// �񤭹��ߤǥե����륪���ץ�
		$fp = fopen($strFileName, 'wb');
		if($fp == FALSE){
			// �����ץ󥨥顼
			$this->strErrorMessage = 'Permission denied : ' . $strFileName;
			return FALSE;
		}
		// �ե������å�
		if(!flock($fp, 2)){
			// �ե������å����顼
			$this->strErrorMessage = 'File lock failed : ' . $strFileName;
			return FALSE;
		}
		
		// ���������ɤ���ʸ���������Ѵ�
		$strTempFileValue = i18n_convert($strFileValue, $strWriteEncoding, i18n_internal_encoding());
		
		// �񤭹���
		if(!fputs($fp, $strTempFileValue)){
			// �񤭹��ߥ��顼
			$this->strErrorMessage = 'Write file failed : ' . $strFileName;
			return FALSE;
		}
		
		// �ե�������Ĥ���
		fclose($fp);
		
		switch(CLISTOUTPUT_WRITE_VERIFY_LEVEL){
			case 0:
				// �����å��ʤ�
				break;
			case 1:
				// �ե����륵���������å�
				$intFileSize = filesize($strFileName);
				if($intFileSize == FALSE){
					// �ե����륵������0�ޤ��ϼ������顼
					$this->strErrorMessage = 'Verify file size failed : ' . $strFileName;
					return FALSE;
				}
				
				if(strlen($strFileValue) != $intFileSize){
					// �ե����륵���������פ��ʤ����顼
					$this->strErrorMessage = 'Verify file size unconformable : ' . $strFileName;
					return FALSE;
				}
				break;
			case 2:
			default:
				// �ե��������ƥ����å�
				// �ե������ɤ߹���
				if(!$this->LoadFile($strFileName, $strVerifyFileValue, $strWriteEncoding)){
					// �ɤ߹��ߥ��顼
					// LoadFile()�Υ��顼��å���������Ѥ���
					return FALSE;
				}
				
				// ���Ƥ����פ��뤫��ǧ����
				if($strVerifyFileValue != $strFileValue){
					// ���פ��ʤ����顼
					$this->strErrorMessage = 'Verify file value unconformable : ' . $strFileName;
					return FALSE;
				}
				break;
		}
		
		// �񤭹��߽����ڤӤ��٤ƤΥ����å����ܥ��ꥢ
		return TRUE;
	}
	
	//	�ؿ�̾:		SetReplaceList
	//
	//	����:		�ƥ�ץ졼���֤������ꥹ�Ȥ˥����򥻥åȤ���
	//
	//	����:		$strKeyName		���åȤ��륭��(���Ǥˤ�����Ͼ��)
	//				$strValue		���åȤ�����
	//
	//	�����:		TRUE:	���
	//
	//	���顼:		�֤��ʤ�
	//
	function SetReplaceList($strKeyName, $strValue){
		// �ͤ򥻥å�
		$this->aryReplaceList[$strKeyName] = $strValue;
		
		// �����������ƹ���
		$this->aryColumnList = $this->GetKeyArray($this->aryReplaceList, '/_%', '%_/');
		
		return TRUE;
	}
	
	//	�ؿ�̾:		ImportReplaceList
	//
	//	����:		�ƥ�ץ졼���֤������ꥹ�Ȥ˥���ݡ��Ȥ���(��¸�Υꥹ�ȤϤ��٤ƾä���)
	//
	//	����:		$strKeyName		���åȤ��륭��(���Ǥˤ�����Ͼ��)
	//				$strValue		���åȤ�����
	//
	//	�����:		TRUE:	���ｪλ
	//				FALSE:	�۾ｪλ
	//
	//	���顼:		�֤�
	//
	function ImportReplaceList($aryImportList){
		if(!is_array($aryImportList)){
			// ����Ǥʤ����顼
			$this->strErrorMessage = 'Invalid import list argument';
			return FALSE;
		}
		
		// �ͤ򥤥�ݡ���
		$this->aryReplaceList = $aryImportList;
		
		// �����������ƹ���
		$this->aryColumnList = $this->GetKeyArray($this->aryReplaceList, '/_%', '%_/');
		
		return TRUE;
	}
	
	//	�ؿ�̾:		ReplaceStrings
	//
	//	����:		preg_replace�� \0 ���Ϥ��б�������åѡ�
	//
	//	����:		$aryPattern			�֤�����������(string�Ǥ�OK)
	//				$aryReplacement		�֤�����������(string�Ǥ�OK)
	//				$strSubject			�֤������о�
	//
	//	�����:		�֤�������ʸ����
	//
	//	���顼:		�֤��ʤ�
	//
	function ReplaceStrings($aryPattern, $aryReplacement, $strSubject){
		// ���Ū�� \ �θ�� 0x01 �������
		$aryReplacement = preg_replace('/' . '\x5C' . '/' , "\\0" . "\x01", $aryReplacement);
		
		// ���Ū���������� 0x01 �򤹤٤ƺ������
		return str_replace("\x01", '', preg_replace($aryPattern, $aryReplacement, $strSubject));
	}
	
	//	�ؿ�̾:		CheckToken
	//
	//	����:		�ȡ�������������С����Ƥ��ʤ��������å����롣
	//				(���δؿ��� lngAlertToken �򸵤�Ƚ�Ǥ���)
	//
	//	����:		�ʤ�
	//
	//	�����:		TRUE:	�����С����Ƥ��ʤ�
	//				FALSE:	�����С����Ƥ���
	//
	//	���顼:		�֤��ʤ�
	//
	function CheckToken(){
		if($this->lngAlertToken >= ($this->lngMaxToken - $this->lngCurrentToken)){
			// �Ĥ�ȡ����󤬷ٹ�ȡ�����ʲ��ˤʤäƤ���
			return FALSE;
		}
		
		return TRUE;
	}

	//	�ؿ�̾:		CheckClassToken
	//
	//	����:		���饹�ѤΥȡ�������������С����Ƥ��ʤ��������å����롣
	//				(���δؿ��� lngMaxToken �򸵤�Ƚ�Ǥ���)
	//
	//	����:		�ʤ�
	//
	//	�����:		TRUE:	�����С����Ƥ��ʤ�
	//				FALSE:	�����С����Ƥ���
	//
	//	���顼:		�֤��ʤ�
	//
	function CheckClassToken(){
		if($this->lngCurrentToken >= ((int) $this->lngMaxToken / 2)){
			// eval()�ǥ��饹�����������Ϻ���ȡ��������Ⱦʬ�ʾ夬�����Ƥ���ɬ�פ����롣
			return FALSE;
		}
		
		return TRUE;
	}

	//	�ؿ�̾:		CreateUniqueClassName
	//
	//	����:		��ˡ����ʥ��饹̾����������
	//
	//	����:		�ʤ�
	//
	//	�����:		��ˡ����ʥ��饹̾
	//
	//	���顼:		�֤��ʤ�
	//
	function CreateUniqueClassName(){
		// uniqid()�ǰ�դʥ��饹̾����������
		return 'CLISTOUTPUT_' . uniqid('');
	}

	//	�ؿ�̾:		CreateBindEvalCache
	//
	//	����:		EVAL����å����ޤȤ����������(�ȡ����������Τ���)
	//
	//	����:		�ʤ�
	//
	//	�����:		TRUE:	���ｪλ
	//				FALSE:	�۾ｪλ
	//
	//	���顼:		�֤�
	//
	function CreateBindEvalCache(){
		// ������ץ��������������
		$aryScript = array();
		
		// 1. EVAL����å���⡼�ɤ�����å���ͭ��(CLISTOUTPUT_EVAL_CACHE_ON)�ˤʤäƤ��뤳��
		// 2. EVAL�ΤޤȤ������⡼�ɤ�ͭ��(CLISTOUTPUT_BIND_EVAL_CACHE_ON)�ˤʤäƤ��뤳��
		// 3. EVAL�μ¹ԥ⡼�ɤ����饹����(CLISTOUTPUT_EVAL_CLASS)�ˤʤäƤ��뤳��
		// �ʾ��2�ĤΤ����줫�� FALSE �ʤ��EVAL����å���ΤޤȤ����������Ѥ��ʤ�
		if($this->bytEvalCacheMode == CLISTOUTPUT_EVAL_CACHE_ON and $this->bytBindEvalCacheMode == CLISTOUTPUT_BIND_EVAL_CACHE_ON and $this->bytEvalMode == CLISTOUTPUT_EVAL_CLASS){
			
			if(is_array($this->aryConfig['EVAL']) == TRUE){
				reset($this->aryConfig['EVAL']);
				while(list($strProcessName) = each($this->aryConfig['EVAL'])){
					// EVAL����å��夬¸�ߤ��ʤ����Τߥ���å������������(CheckEvalCache()��FALSE���֤�)
					if($this->CheckEvalCache($this->aryConfig['EVAL'][$strProcessName]) == FALSE){
						// ����å��夬¸�ߤ��ʤ��Τ������ꥹ�Ȥ������
						$aryScript[] = $this->aryConfig['EVAL'][$strProcessName];
					}
				}
			}
		}
		
		// �оݥ�����ץȤ�¸�ߤ�����ϥ���å������������
		if(count($aryScript) >= 1){
			// ���饹���֥������Ȥ�����
			if($this->CreateClassObject($aryScript, $objEvalClass) == FALSE){
				// CreateClassObject()�Υ��顼��å���������Ѥ���
				return FALSE;
			}
			
			// �оݥ�����ץȤο������
			$intMaxLoopIndex = count($aryScript);
			for($intLoopIndex = 0; $intLoopIndex < $intMaxLoopIndex; ++$intLoopIndex){
				// EVAL����å���򥻥åȤ���
				$this->SetEvalCache($aryScript[$intLoopIndex], $objEvalClass[$intLoopIndex]);
			}
		}
		
		return TRUE;
	}

	//	�ؿ�̾:		CreateClassObject
	//
	//	����:		���饹����Υ�����ץȤ򸵤˥��֥������Ȥ���������
	//
	//	����:		$aryScript		������ץȤ�����(ʸ����ˤ��б�)
	//				&$objEvalClass	���饹���֥������Ȥ�����(�����)
	//
	//	�����:		TRUE:	���ｪλ
	//				FALSE:	�۾ｪλ
	//
	//	���顼:		�֤�
	//
	function CreateClassObject($aryScript, &$objEvalClass){
		// ���饹�ѤΥȡ����������å�����
		if($this->CheckClassToken() == FALSE){
			// ���饹�ѤΥȡ�����λȤ��������顼
			$this->strErrorMessage = 'Reach max token cache for class : ' . (int) ($this->lngMaxToken / 2);
			return FALSE;
		}
		
		// ����Ǥʤ�������󲽤���
		if(is_array($aryScript) == FALSE){
			// ʸ������Ϥ��줿�ե饰��Ω�Ƥ�
			$bolStringFlag = TRUE;
			// ����
			$aryScript = array($aryScript);
		}
		
		// �оݥ�����ץȤο������
		$intMaxLoopIndex = count($aryScript);
		
		// ���饹������ץȤ���������
		$strClassScript = '';
		
		for($intLoopIndex = 0; $intLoopIndex < $intMaxLoopIndex; ++$intLoopIndex){
			// �Ȥ��ΤƤΥ��饹̾���������
			$strClassName = $this->CreateUniqueClassName();
			
			// ���饹�������������
			$strClassScript .= 'class ' . $strClassName . "{\n";
			$strClassScript .= 'function ExtendSQLResult(&$arySQLResult, &$objContext)' . "{\n";
			$strClassScript .= $aryScript[$intLoopIndex] . "\n";
			$strClassScript .= "return TRUE;\n";
			$strClassScript .= "}\n";
			$strClassScript .= "}\n";
			if($bolStringFlag == TRUE){
				// ʸ������Ϥ���Ƥ���Τ�ʸ������֤�
				$strClassScript .= '$objEvalClass = new ' . $strClassName . ";\n";
			}
			else{
				// ������Ϥ��줿�Τ�������֤�
				$strClassScript .= '$objEvalClass[' . $intLoopIndex . '] = new ' . $strClassName . ";\n";
			}
		}
		
		// ���顼��λ�ե饰��Ω�Ƥ�
		$bolErrorExit = TRUE;
		eval($strClassScript . "\n" . '$bolErrorExit = FALSE;');
		// ���Ѻѥȡ�����򥤥󥯥����
		$this->lngCurrentToken++;
		
		if($bolErrorExit == TRUE){
			// eval��ǥ��顼ȯ��
			$this->strErrorMessage = 'Invalid script in eval : ' . join("\n", $aryScript);
			return FALSE;
		}
		
		return TRUE;
	}

	//	�ؿ�̾:		GetClassObject
	//
	//	����:		���饹����Υ�����ץȤ򸵤˥��֥������Ȥ���������(����å��������)
	//
	//	����:		$strScript	������ץ�
	//				&$objEvalClass	���饹���֥�������(�����)
	//
	//	�����:		TRUE:	���ｪλ
	//				FALSE:	�۾ｪλ
	//
	//	���顼:		�֤�
	//
	function GetClassObject($strScript, &$objEvalClass){
		// 1. EVAL����å���⡼�ɤ�����å���ͭ��(CLISTOUTPUT_EVAL_CACHE_ON)�ˤʤäƤ��뤳��
		// 2. EVAL����å��夬¸�ߤ��뤳��(CheckEvalCache()��TRUE���֤�)
		// 3. EVAL����å��夬�����Ǥ��뤳��(GetEvalCache()��TRUE���֤�)
		// �ʾ��3�ĤΤ����줫�� FALSE �ʤ�Х���å�������ѤǤ��ʤ��Τ���������
		if((($this->bytEvalCacheMode == CLISTOUTPUT_EVAL_CACHE_ON) and ($this->CheckEvalCache($strScript) == TRUE) and ($this->GetEvalCache($strScript, $objEvalClass) == TRUE)) == FALSE){
			// ����å�������Ѥ��ʤ�
			if($this->CreateClassObject($strScript, $objEvalClass) == FALSE){
				// ���饹�κ�������
				// CreateClassObject()�Υ��顼��å������򤽤Τޤ޻��Ѥ���
				return FALSE;
			}
			
			// EVAL����å���򥻥åȤ���
			$this->SetEvalCache($strScript, $objEvalClass);
		}
		
		// ���ｪλ
		return TRUE;
	}
	
	//	�ؿ�̾:		CheckEvalCache
	//
	//	����:		EVAL�Υ���å��夬¸�ߤ��뤫�����å�����
	//
	//	����:		$strScript	������ץ�
	//
	//	�����:		TRUE:	����å������Ѳ�ǽ
	//				FALSE:	����å��������Բ�ǽ
	//
	//	���顼:		�֤��ʤ�
	//
	function CheckEvalCache($strScript){
		// ������ץȤΥ����������Ȥ��������
		$strScriptDigest = $this->GetDigest($strScript);
		
		// ������ץȤΥ����������Ȥ򥭡��ˤ������������å�����
		if(isset($this->aryEvalCache[$strScriptDigest]) == FALSE){
			// ����å����¸�ߤ��ʤ�
			return FALSE;
		}
		
		// �����������Ȥϰ��פ��Ƥ���ΤǼºݤ����Ƥ�����å�����
		if(strcmp($this->aryEvalCache[$strScriptDigest]['SCRIPT'], $strScript) != 0){
			// ���Ƥ����פ��ʤ�
			return FALSE;
		}
		
		// ���֥������Ȥ�¸�ߥ����å�
		if(isset($this->aryEvalCache[$strScriptDigest]['OBJECT']) == FALSE){
			// ���֥������Ȥ�¸�ߤ��ʤ�
			return FALSE;
		}
		
		// ����å������Ѳ�ǽ
		return TRUE;
	}

	//	�ؿ�̾:		GetDigest
	//
	//	����:		�����������Ȥ���������
	//
	//	����:		$strTarget	�����������Ȥ����������о�
	//
	//	�����:		������������
	//
	//	���顼:		�֤��ʤ�
	//
	function GetDigest($strTarget){
		// MD5�ǥ����������Ȥ���������
		return md5($strTarget);
	}

	//	�ؿ�̾:		array_merge
	// 
	//	����:		��Ĥ������ޡ�������
	//				PHP4�� array_merge ��Ʊ��ư��
	//				�������Ϥ�����������
	//
	//	����:		$Array			�ޡ�����������
	//				$MergeArray		�ޡ�����������
	//
	//	�����:		�ޡ����Ѥߤ�����
	//
	//	���顼:		�֤��ʤ�
	//
	function array_merge($Array, $MergeArray){
		// ����Ǥʤ��������󲽤���
		if(!is_array($Array)){
			$Array = array($Array);
		}
		
		// $MergeArray ������Ǥʤ���� $Array �򤽤Τޤ��֤�
		// $Array ������ǤϤʤ��������󲽤���
		if(!is_array($MergeArray)){
			return $Array;
		}
		
		reset($MergeArray);
		
		while(list($key, $value) = each($MergeArray)){
			$Array[$key] = $value;
		}
		
		return $Array;
	}
}

class CListOutputContext {
	var $aryContext;
	var $intOffset;

	//	�ؿ�̾:		CListOutputContext
	//
	//	����:		���󥹥ȥ饯��
	//
	//	����:		�ʤ�
	//				
	//	�����:		�ʤ�
	//
	//	���顼:		�֤��ʤ�
	//
	function CListOutputContext() {
		$this->initializeContext();
	}

	//	��������:	private
	//
	//	�ؿ�̾:		getExecuteContextIndex
	//
	//	����:		$aryContext��μ¹ԥ���ƥ����ȤΥ���ǥå�����������ޤ���
	//
	//	����:		�ʤ�
	//
	//	�����:		�¹ԥ���ƥ����ȤΥ���ǥå���
	//
	//	���顼:		�֤��ʤ�
	//
	function getExecuteContextIndex() {
		return $this->intOffset + 2;
	}

	//	��������:	private
	//
	//	�ؿ�̾:		getPageContextIndex
	//
	//	����:		$aryContext��Υڡ�������ƥ����ȤΥ���ǥå�����������ޤ���
	//
	//	����:		�ʤ�
	//
	//	�����:		�ڡ�������ƥ����ȤΥ���ǥå���
	//
	//	���顼:		�֤��ʤ�
	//
	function getPageContextIndex() {
		return $this->intOffset + 1;
	}

	//	��������:	private
	//
	//	�ؿ�̾:		getSessionContextIndex
	//
	//	����:		$aryContext��Υ��å���󥳥�ƥ����ȤΥ���ǥå�����������ޤ���
	//
	//	����:		�ʤ�
	//
	//	�����:		���å���󥳥�ƥ����ȤΥ���ǥå���
	//
	//	���顼:		�֤��ʤ�
	//
	function getSessionContextIndex() {
		return $this->intOffset;
	}

	//	�ؿ�̾:		raise
	//
	//	����:		����ƥ����Ȥΰ����夲
	//
	//	����:		�ʤ�
	//
	//	�����:		TRUE
	//
	//	���顼:		�֤��ʤ�
	//
	function raise() {
		$this->intOffset++;
		return TRUE;
	}

	//	�ؿ�̾:		lower
	//
	//	����:		����ƥ����Ȥΰ�������
	//
	//	����:		�ʤ�
	//
	//	�����:		TRUE:	����
	//				FALSE:	����(����ʾ�����������ʤ����)
	//
	//	���顼:		�֤��ʤ�
	//
	function lower() {
		if ($this->intOffset <= 0) {
			return FALSE;
		}
		// ���������ˤ�äƥ������פ��鳰���¹ԥ���ƥ����Ȥ���������
		$this->initializeExecuteContext();
		$this->intOffset--;
		return TRUE;
	}

	//	�ؿ�̾:		initializeContext
	//
	//	����:		����ƥ����Ȥ���������
	//
	//	����:		�ʤ�
	//
	//	�����:		TRUE
	//
	//	���顼:		�֤��ʤ�
	//
	function initializeContext() {
		$this->aryContext = array();
		$this->intOffset = 0;

		$this->initializeExecuteContext();
		$this->initializePageContext();
		$this->initializeSessionContext();
		return TRUE;
	}

	//	�ؿ�̾:		setExecuteContext
	//
	//	���ס�		�¹ԥ���ƥ����Ȥ��ͤ򥻥åȤ���
	//
	//	����:		$Name		�ѿ�̾
	//				$Value		�ѿ���
	//
	//	�����:		TRUE
	//
	//	���顼:		�֤��ʤ�
	//
	function setExecuteContext($Name, $Value) {
		$this->aryContext[$this->getExecuteContextIndex()][$Name] = $Value;
		return TRUE;
	}

	//	�ؿ�̾:		getExecuteContext
	//
	//	���ס�		�¹ԥ���ƥ����Ȥ����ͤ��������
	//
	//	����:		$Name		�ѿ�̾
	//
	//	�����:		�ѿ���(¸�ߤ��ʤ����϶�ʸ��)
	//
	//	���顼:		�֤��ʤ�
	//
	function getExecuteContext($Name) {
		return $this->aryContext[$this->getExecuteContextIndex()][$Name];
	}

	//	�ؿ�̾:		isSetExecuteContext
	//
	//	����:		�¹ԥ���ƥ����Ȥ��ѿ���¸�ߤ��뤫�����å�����
	//
	//	����:		$Name		�ѿ�̾
	//
	//	�����:		TRUE: ¸�ߤ���  FALSE: ¸�ߤ��ʤ�
	//
	//	���顼:		�֤��ʤ�
	//
	function isSetExecuteContext($Name) {
		return isset($this->aryContext[$this->getExecuteContextIndex()][$Name]);
	}

	//	�ؿ�̾:		clearExecuteContext
	//
	//	����:		�¹ԥ���ƥ����Ȥ����ѿ���������
	//
	//	����:		$Name		�ѿ�̾
	//
	//	�����:		TRUE
	//
	//	���顼:		�֤��ʤ�
	//
	function clearExecuteContext($Name) {
		unset($this->aryContext[$this->getExecuteContextIndex()][$Name]);
		return TRUE;
	}

	//	�ؿ�̾:		initializeExecuteContext
	//
	//	����:		�¹ԥ���ƥ����Ȥ���������
	//
	//	����:		�ʤ�
	//
	//	�����:		TRUE
	//
	//	���顼:		�֤��ʤ�
	//
	function initializeExecuteContext() {
		$this->aryContext[$this->getExecuteContextIndex()] = array();
		return TRUE;
	}

	//	�ؿ�̾:		setPageContext
	//
	//	���ס�		�ڡ�������ƥ����Ȥ��ͤ򥻥åȤ���
	//
	//	����:		$Name		�ѿ�̾
	//				$Value		�ѿ���
	//
	//	�����:		TRUE
	//
	//	���顼:		�֤��ʤ�
	//
	function setPageContext($Name, $Value) {
		$this->aryContext[$this->getPageContextIndex()][$Name] = $Value;
		return TRUE;
	}

	//	�ؿ�̾:		getPageContext
	//
	//	���ס�		�ڡ�������ƥ����Ȥ����ͤ��������
	//
	//	����:		$Name		�ѿ�̾
	//
	//	�����:		�ѿ���(¸�ߤ��ʤ����϶�ʸ��)
	//
	//	���顼:		�֤��ʤ�
	//
	function getPageContext($Name) {
		return $this->aryContext[$this->getPageContextIndex()][$Name];
	}

	//	�ؿ�̾:		isSetPageContext
	//
	//	����:		�ڡ�������ƥ����Ȥ��ѿ���¸�ߤ��뤫�����å�����
	//
	//	����:		$Name		�ѿ�̾
	//
	//	�����:		TRUE: ¸�ߤ���  FALSE: ¸�ߤ��ʤ�
	//
	//	���顼:		�֤��ʤ�
	//
	function isSetPageContext($Name) {
		return isset($this->aryContext[$this->getPageContextIndex()][$Name]);
	}

	//	�ؿ�̾:		clearPageContext
	//
	//	����:		�ڡ�������ƥ����Ȥ����ѿ���������
	//
	//	����:		$Name		�ѿ�̾
	//
	//	�����:		TRUE
	//
	//	���顼:		�֤��ʤ�
	//
	function clearPageContext($Name) {
		unset($this->aryContext[$this->getPageContextIndex()][$Name]);
		return TRUE;
	}

	//	�ؿ�̾:		initializePageContext
	//
	//	����:		�ڡ�������ƥ����Ȥ���������
	//
	//	����:		�ʤ�
	//
	//	�����:		TRUE
	//
	//	���顼:		�֤��ʤ�
	//
	function initializePageContext() {
		$this->aryContext[$this->getPageContextIndex()] = array();
		return TRUE;
	}

	//	�ؿ�̾:		setSessionContext
	//
	//	���ס�		���å���󥳥�ƥ����Ȥ��ͤ򥻥åȤ���
	//
	//	����:		$Name		�ѿ�̾
	//				$Value		�ѿ���
	//
	//	�����:		TRUE
	//
	//	���顼:		�֤��ʤ�
	//
	function setSessionContext($Name, $Value) {
		$this->aryContext[$this->getSessionContextIndex()][$Name] = $Value;
		return TRUE;
	}

	//	�ؿ�̾:		getSessionContext
	//
	//	���ס�		���å���󥳥�ƥ����Ȥ����ͤ��������
	//
	//	����:		$Name		�ѿ�̾
	//
	//	�����:		�ѿ���(¸�ߤ��ʤ����϶�ʸ��)
	//
	//	���顼:		�֤��ʤ�
	//
	function getSessionContext($Name) {
		return $this->aryContext[$this->getSessionContextIndex()][$Name];
	}

	//	�ؿ�̾:		isSetSessionContext
	//
	//	����:		���å���󥳥�ƥ����Ȥ��ѿ���¸�ߤ��뤫�����å�����
	//
	//	����:		$Name		�ѿ�̾
	//
	//	�����:		TRUE: ¸�ߤ���  FALSE: ¸�ߤ��ʤ�
	//
	//	���顼:		�֤��ʤ�
	//
	function isSetSessionContext($Name) {
		return isset($this->aryContext[$this->getSessionContextIndex()][$Name]);
	}

	//	�ؿ�̾:		clearSessionContext
	//
	//	����:		���å���󥳥�ƥ����Ȥ����ѿ���������
	//
	//	����:		$Name		�ѿ�̾
	//
	//	�����:		TRUE
	//
	//	���顼:		�֤��ʤ�
	//
	function clearSessionContext($Name) {
		unset($this->aryContext[$this->getSessionContextIndex()][$Name]);
		return TRUE;
	}

	//	�ؿ�̾:		initializeSessionContext
	//
	//	����:		���å���󥳥�ƥ����Ȥ���������
	//
	//	����:		�ʤ�
	//
	//	�����:		TRUE
	//
	//	���顼:		�֤��ʤ�
	//
	function initializeSessionContext() {
		$this->aryContext[$this->getSessionContextIndex()] = array();
		return TRUE;
	}
}
?>
