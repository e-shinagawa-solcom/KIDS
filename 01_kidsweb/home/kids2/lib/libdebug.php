<?
// ----------------------------------------------------------------------------
/**
*       デバッグ用関数
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
*		DEF_DEBUG_DIR 以下にデバッグ用ログファイルを出力します。
*
*       更新履歴
*
*/
// ----------------------------------------------------------------------------

include_once("conf.inc");

//
// 概要：ダンプデータ、ログ出力
// 引数：
// @param	$strOutFile	出力先ファイル名
// @param	$objValue		ダンプ対象変数
// @param	$strFile	__FILE__
// @param	$strLine	__LINE__
//
// 戻り値：成功時 True／失敗時 False
//
function fncDebug($strOutFile, $objValue, $strFile="", $strLine="", $mode="w")
{
	$strOutFile = DEF_DEBUG_DIR . $strOutFile;
	
	// ディレクトリの存在確認
	if( !file_exists( dirname($strOutFile) ) )
	{
		return false;
	}
	
	// 本番稼動モードの場合は無視する
	if( DEF_DEBUG_MODE == 0 )
	{
		return false;
	}

	
	// バッファリング開始
	ob_start();
	// ダンプ開始
	echo "===== ".date("Y-m-d H:i:s", time()) . " ===== >> " . $strFile . " [" . $strLine . "]\n";

	var_dump($objValue);
	// ファイル書き込み
	$fp = fopen($strOutFile, $mode);
	fwrite($fp, ob_get_contents() );
	fclose($fp);
	
	// バッファリング終了
	ob_end_clean();
	
	return true;

}

?>
