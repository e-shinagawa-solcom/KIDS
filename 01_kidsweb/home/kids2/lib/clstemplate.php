<?php
	define ( "CONFIG_TAG", "config" );
// ----------------------------------------------------------------------------
/**
*       �ƥ�ץ졼�Ƚ������饹
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
*		getTemplate         �ƥ�ץ졼�ȥǡ�������<br>
*		getConfig           ����ǡ�������(�ƥ�ץ졼������ü쥿���������)<br>
*		replace             �ǡ������֤�����<br>
*		complete            �Ѵ�ʸ����κ��<br>
*
*       ��������
*
*/
// ----------------------------------------------------------------------------

class clsTemplate
{
	/**
	*	�ƥ�ץ졼�ȥǡ���
	*	@var string
	*/
	var $strTemplate;
	/**
	*	�ƥ�ץ졼�Ȥ˵��Ҥ���������ʬ����̾��(���꥿��)
	*	@var string
	*/
	var $strConfigTag;
	/**
	*	�ƥ�ץ졼�ȥǥ��쥯�ȥ�롼�ȥѥ�
	*	@var string
	*/
	var $strTemplateRoot;

	/**
	*	���󥹥ȥ饯��
	*	���饹��ν������Ԥ�
	*
	*	@return void
	*	@access public
	*/
	function __construct()
	{
		$this->strTemplate     = "";
		$this->strConfigTag    = CONFIG_TAG;
		$this->aryConfigValue  = "";
		$this->strTemplateRoot = TMP_ROOT;
	}

	// -----------------------------------------------------------------
	/**
	*	�ƥ�ץ졼�ȥǡ�������
	*	@param	string	$strTemplatePath  �ƥ�ץ졼�ȥե�����ѥ�
	*	@return	boolean	���ԡ�����
	*/
	// -----------------------------------------------------------------
	function getTemplate( $strTemplatePath )
	{
		if ( !$this->strTemplate = file_get_contents ( $this->strTemplateRoot . $strTemplatePath ) ) {
			 return FALSE;
		}
	}

	// -----------------------------------------------------------------
	/**
	*	����ǡ�������(�ƥ�ץ졼������ü쥿���������)
	*	@param	array	$aryConfigNames  ���꥿����˽񤫤줿������̾��
	*	@return	boolean	���ԡ���������
	*						$strConfigValues ������̾�Τ��������椬�ͤ�����
	*/
	// -----------------------------------------------------------------
	function getConfig( $aryConfigNames )
	{
		if ( !$aryConfigNames )
		{
			return FALSE;
		}

		// ���꥿����������������
		$strConfig = mb_eregi_replace ( "(<" . $this->strConfigTag . ">.+?</" . $this->strConfigTag . ">).+", "\\1", $this->strTemplate , "m" );
		$strConfig = mb_eregi_replace ( "</?" . $this->strConfigTag . ">", "", $strConfig , "m" );

		// ���꥿����Υǡ������������
		foreach ( $aryConfigNames as $key )
		{
			$strConfigValues[$key] = mb_eregi_replace ( ".*(<$key>.+?</$key>).*", "\\1", $strConfig, "m" );
			$strConfigValues[$key] = mb_eregi_replace ( "</?$key>", "", $strConfigValues[$key], "m" );
		}

		// �ƥ�ץ졼�Ȥ��������������
		$this->strTemplate = mb_ereg_replace ( "<" . $this->strConfigTag . ">.+?<\/" . $this->strConfigTag . ">", "", $this->strTemplate , "m" );

		return $strConfigValues;
	}

	// -----------------------------------------------------------------
	/**
	*	�ǡ������֤�����
	*	@param	array	$aryPost	POST�ǡ���
	*	@return	boolean	����������
	*/
	// -----------------------------------------------------------------
	function replace( $aryPost )
	{
		if ( count ( $aryPost ) )
		{
			$aryPostName = array_keys( $aryPost );

			foreach ( $aryPostName as $key )
			{
				if ( $aryPost[$key] !== "" )
				{
					if ( !is_array( $aryPost[$key] ) )
					{
						$this->strTemplate = preg_replace ( "/_%" . $key . "%_/i", $aryPost[$key], $this->strTemplate );
					}
				}
			}
		}

		// �ִ�����ʤ��ä��֤�����ʸ�������
		// $this->strTemplate = preg_replace ( "/_%.+?%_/", "", $this->strTemplate );
	}

	// -----------------------------------------------------------------
	/**
	 *	�ǡ������֤�����(�ⷿ��) <!-- _%hedaer{N}%_ -->���֤�����
	 *	@param	array	$aryPost	POST�ǡ���
	 *	@return	boolean	����������
	 */
	// -----------------------------------------------------------------
	function replaceForMold( $aryPost )
	{
		if ( count ( $aryPost ) )
		{
			$aryPostName = array_keys( $aryPost );

			foreach ( $aryPostName as $key )
			{
				if ( $aryPost[$key] !== "" )
				{
					if ( !is_array( $aryPost[$key] ) )
					{
						switch ($key)
						{
							case 'header1':
							case 'header2':
							case 'header3':
								$this->strTemplate = preg_replace ( "/<!--.*_%" . $key . "%_.*-->/i", $aryPost[$key], $this->strTemplate );
								break;
							default:
								$this->strTemplate = preg_replace ( "/_%" . $key . "%_/i", $aryPost[$key], $this->strTemplate );
								break;
						}


					}
				}
			}
		}

		// �ִ�����ʤ��ä��֤�����ʸ�������
		// $this->strTemplate = preg_replace ( "/_%.+?%_/", "", $this->strTemplate );
	}



	// -----------------------------------------------------------------
	/**
	*	�쥤�����ȥ����ɤ����ꡢ�Ѵ�ʸ����κ��
	*/
	// -----------------------------------------------------------------
	function complete()
	{
		// �쥤�����ȥ����ɤ�����
		$this->strTemplate = preg_replace ( "/_%lngLayoutCode%_/", LAYOUT_CODE, $this->strTemplate );

		if (mb_ereg_replace ( "<COMMENT>.+?<\/COMMENT>", "", $this->strTemplate )) {
			// ������ʸ�������
			$this->strTemplate = mb_ereg_replace ( "<COMMENT>.+?<\/COMMENT>", "", $this->strTemplate );
		}

		// �ִ�����ʤ��ä��֤�����ʸ�������
		$this->strTemplate = preg_replace ( "/_%.+?%_/", "", $this->strTemplate );
	}
}
?>
