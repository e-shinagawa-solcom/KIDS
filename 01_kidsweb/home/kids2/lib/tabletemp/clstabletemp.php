<?
// ----------------------------------------------------------------------------
/**
*       テンポラリーテーブル操作クラス
*
*
*       @package    K.I.D.S.
*       @license    http://www.kuwagata.co.jp/
*       @copyright  KUWAGATA CO., LTD.
*       @author     K.I.D.S. Groups <info@kids-groups.com>
*       @access     public
*       @version    1.00
*
*
*       処理概要
*   	t_temp テーブルへのデータ登録、取得を行う。
*
*       更新履歴
*		2006/05/27	New
*		2006/05/28  strKey の登録・取得を 全て小文字に変換するようにした
*
*/
// ----------------------------------------------------------------------------
//include_once('conf.inc');
//require_once(LIB_FILE);

class clsTableTemp
{
	var $objDB;
	var $aryTemp = array();

	// -------------------------------------------------------
	// constructer
	// -------------------------------------------------------
	function clsTableTemp()
	{
		unset($this->objDB);
	}

	// -------------------------------------------------------
	// Insert
	// Arg	$aryIn	- product info
	// Return	lngTempNo
	// -------------------------------------------------------
	function fncInsert($aryIn)
	{
		if(!isset($aryIn))
		{
			return false;
		}

		$lngFatalCnt=0;

		// トランザクション開始
		$this->objDB->transactionBegin();

		// t_temp.lngtempno の取得
		$lngtempno = fncGetSequence("t_temp.lngtempno", $this->objDB);

		// $aryIn 配列分の登録処理
		while(list($strKey, $strValue) = each($aryIn))
		{
			$arySql = array();
			$arySql[] = 'insert into t_temp';
			$arySql[] = '(lngtempno, strkey, strvalue)';
			$arySql[] = 'values('.$lngtempno.", '".strtolower($strKey)."', '".$strValue."')";
			$strSql = implode($arySql,"\n");

			// 登録実行
			list ($lngResultID, $lngResultNum) = fncQuery($strSql, $this->objDB);
			// エラーの際はDBエラーで表示される
		}

		// コミット
		$this->objDB->transactionCommit();

		return $lngtempno;
	}

	// -------------------------------------------------------
	// Select
	// -------------------------------------------------------
	function fncSelect($lngTempNo)
	{

		$arySql = array();
		$arySql[] = 'select strKey, strValue from t_temp';
		$arySql[] = 'where lngtempno='.$lngTempNo;
		$strSql = implode($arySql,"\n");
		
		// strKey, strValue を取得
		list ( $lngResultID, $lngResultNum ) = fncQuery( $strSql, $this->objDB );

		// strKey, strValue の組み合わせをメンバー変数へ設定
		for($i = 0; $i < $lngResultNum; $i++)
		{
			$objTemp = $this->objDB->fetchObject( $lngResultID, $i );
			$this->aryTemp[strtolower($objTemp->strkey)] = $objTemp->strvalue;
		}

		return $this->aryTemp;
	}
}

?>
