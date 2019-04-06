<?php
	define ( "CONFIG_TAG", "config" );
// ----------------------------------------------------------------------------
/**
*       テンプレート処理クラス
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
*       処理概要
*		getTemplate         テンプレートデータ取得<br>
*		getConfig           設定データ取得(テンプレート内の特殊タグから取得)<br>
*		replace             データの置き換え<br>
*		complete            変換文字列の削除<br>
*
*       更新履歴
*
*/
// ----------------------------------------------------------------------------

class clsTemplate
{
	/**
	*	テンプレートデータ
	*	@var string
	*/
	var $strTemplate;
	/**
	*	テンプレートに記述した設定部分タグ名称(設定タグ)
	*	@var string
	*/
	var $strConfigTag;
	/**
	*	テンプレートディレクトリルートパス
	*	@var string
	*/
	var $strTemplateRoot;

	/**
	*	コンストラクタ
	*	クラス内の初期化を行う
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
	*	テンプレートデータ取得
	*	@param	string	$strTemplatePath  テンプレートファイルパス
	*	@return	boolean	失敗、成功
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
	*	設定データ取得(テンプレート内の特殊タグから取得)
	*	@param	array	$aryConfigNames  設定タグ内に書かれたタグの名称
	*	@return	boolean	失敗。成功時は
	*						$strConfigValues タグの名称がキー、中が値の配列
	*/
	// -----------------------------------------------------------------
	function getConfig( $aryConfigNames )
	{
		if ( !$aryConfigNames )
		{
			return FALSE;
		}

		// 設定タグより設定情報を取得
		$strConfig = mb_eregi_replace ( "(<" . $this->strConfigTag . ">.+?</" . $this->strConfigTag . ">).+", "\\1", $this->strTemplate , "m" );
		$strConfig = mb_eregi_replace ( "</?" . $this->strConfigTag . ">", "", $strConfig , "m" );

		// 設定タグ内のデータタグを取得
		foreach ( $aryConfigNames as $key )
		{
			$strConfigValues[$key] = mb_eregi_replace ( ".*(<$key>.+?</$key>).*", "\\1", $strConfig, "m" );
			$strConfigValues[$key] = mb_eregi_replace ( "</?$key>", "", $strConfigValues[$key], "m" );
		}

		// テンプレートから設定情報を除去
		$this->strTemplate = mb_ereg_replace ( "<" . $this->strConfigTag . ">.+?<\/" . $this->strConfigTag . ">", "", $this->strTemplate , "m" );

		return $strConfigValues;
	}

	// -----------------------------------------------------------------
	/**
	*	データの置き換え
	*	@param	array	$aryPost	POSTデータ
	*	@return	boolean	成功、失敗
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

		// 置換されなかった置き換え文字列を削除
		// $this->strTemplate = preg_replace ( "/_%.+?%_/", "", $this->strTemplate );
	}

	// -----------------------------------------------------------------
	/**
	 *	データの置き換え(金型用) <!-- _%hedaer{N}%_ -->を置き換え
	 *	@param	array	$aryPost	POSTデータ
	 *	@return	boolean	成功、失敗
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

		// 置換されなかった置き換え文字列を削除
		// $this->strTemplate = preg_replace ( "/_%.+?%_/", "", $this->strTemplate );
	}



	// -----------------------------------------------------------------
	/**
	*	レイアウトコードの設定、変換文字列の削除
	*/
	// -----------------------------------------------------------------
	function complete()
	{
		// レイアウトコードを設定
		$this->strTemplate = preg_replace ( "/_%lngLayoutCode%_/", LAYOUT_CODE, $this->strTemplate );

		if (mb_ereg_replace ( "<COMMENT>.+?<\/COMMENT>", "", $this->strTemplate )) {
			// コメント文字列を削除
			$this->strTemplate = mb_ereg_replace ( "<COMMENT>.+?<\/COMMENT>", "", $this->strTemplate );
		}

		// 置換されなかった置き換え文字列を削除
		$this->strTemplate = preg_replace ( "/_%.+?%_/", "", $this->strTemplate );
	}
}
?>
