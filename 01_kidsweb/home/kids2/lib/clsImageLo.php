<?php
// ----------------------------------------------------------------------------
/**
*       イメージ・ラージオブジェクト操作クラス
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
*			イメージファイルをラージオブジェクトとして用い、t_image / m_imagerelation テーブルを操作する
*
*       更新履歴
*
*/
// ----------------------------------------------------------------------------

	class clsImageLo
	{

		var $aryUploadFileInfo;
		
		function __construct()
		{
			
		}

		//
		//	概要：
		// 		$_FILES 変数から必要なパラーメータを取得
		//
		//	引数：
		//		$aryFiles	- $_FILES構造体
		//		$strAlias	- ファイルを特定する為のエイリアス名（<input type="file" name= に指定された名前）
		//
		//	戻り値：
		//		$aryImageInfo	- イメージ情報格納構造体
		//
		function getUploadFileInfo($aryFiles, $strAlias)
		{
			if(empty($strAlias)) return false;
			
			// アップロードされたファイルかをチェックする
			if(!is_uploaded_file($aryFiles[$strAlias]['tmp_name']))
			{
				return false;
			}

			$aryFilesRet = array();
			// テンポラリ名を取得
			$aryFilesRet['tmp_name'] = $aryFiles[$strAlias]['tmp_name'];
			// ファイル名を取得
			$aryFilesRet['name'] = $aryFiles[$strAlias]['name'];
			// 配列からタイプを取得
			$aryFilesRet['type'] = $aryFiles[$strAlias]['type'];
			// 配列からサイズを取得
			$aryFilesRet['size'] = $aryFiles[$strAlias]['size'];
			
			$this->aryUploadFileInfo = $aryFilesRet;
			
			return $aryFilesRet;

		}

		//
		//	概要：
		//		テンポラリディレクトリにイメージファイルをコピーする
		//
		//	引数：
		//		$strSourcePath	- ソースファイル名
		//		$strDestDir		- 出力先の基準ディレクトリ名
		//		&$strUniqDir	- テンポラリディレクトリ内のユニークディレクトリ名（参照返却）
		//		&$strDestFile	- ユニークディレクトリ名内にコピーされるイメージファイル名（参照返却）
		//
		//	戻り値：
		//		処理成否
		//
		function setTempImage($aryImageInfo, $strDestDir, &$strUniqDir="", &$strDestFile="")
		{

			//	拡張子の取得
			preg_match('/(\.jpg|\.gif|\.tif|\.png|\.bmp|\.avi|\.wmv|\.ai|\.rm|\.mov|\.mpg)$/', $aryImageInfo['name'], $aryRet);
			$strFileextension = $aryRet[0];
//	echo "<br>".'拡張子名'." $strFileextension"."<br>";

			$strSourcePath = $aryImageInfo['tmp_name'];

			if(empty($strUniqDir))
			{
				// ユニークなディレクトリ名の生成
				$strUniqDir = uniqid("",false);
			}
			$strTempDirPath = $strDestDir.$strUniqDir."/";
			
//	echo "<br>".'ディレクトリ名'." $strTempDir"."<br>";
			
			if(!file_exists($strTempDirPath))
			{
				mkdir($strTempDirPath, 0777);
//	echo "<br>".'ディレクトリ作成'." $strTempDir"."<br>";
			}
			// コピー先のファイルパスを生成
			$strDestFile = basename($strSourcePath).$strFileextension;
			$strDestFilePath = $strTempDirPath.$strDestFile;
			
//	echo "<br>ファイルコピー".$strSourcePath."->".$strDestFile."<br>";
	
			// PHPファイルアップロード元から指定ディレクトリへ移動
			if(!move_uploaded_file($strSourcePath, $strDestFilePath))
			{
				return false;
			}
			chmod($strDestFilePath, 0777);
			return true;
		}

		//
		//	概要：
		//		ラージオブジェクトとしてデータベースに保存されているものをイメージファイルとして出力する
		//
		//	引数：
		//		$objDB				- データベースオブジェクト
		//		$strImageKeyCode	- イメージキーコード（製品コード）
		//		$strDestDir			- 出力先のディレクトリ名
		//		&$aryImageInfo		- イメージ情報格納構造体
		//
		//	戻り値：
		//		処理成否
		//
		function getImageLo($objDB, $strImageKeyCode, $strDestDir, &$aryImageInfo)
		{
			// トランザクション開始処理
			if(!$objDB->transactionBegin()) return false;
//echo "<br>トランザクション開始<br>";
			// 取得用SQL作成
			$arySql = array();
			$arySql[] = "select distinct";
			$arySql[] = "	mi.lngimagecode";
			$arySql[] = "	,mi.lngfunctioncode";
			$arySql[] = "	,mi.strimagekeycode";
			$arySql[] = "	,ti.*";
			$arySql[] = "from";
			$arySql[] = "	m_imagerelation mi";
			$arySql[] = "	inner join t_image ti on mi.lngimagecode = ti.lngimagecode";
			$arySql[] = "where";
			$arySql[] = "	mi.strimagekeycode = '$strImageKeyCode'";
			$strSql = implode("\n", $arySql);
//echo "<br>SQL＞$strSql<br>";

			// 選択処理実行
			$lngResultID = $objDB->execute($strSql);
			// 結果数の取得
			$lngRowCount = pg_num_rows($lngResultID);
			
			// 一件も無い場合
			if($lngRowCount <= 0) return false;
			
			// 結果数分の情報を取得
			$aryImageInfo = array();
			while($objImageData = pg_fetch_object($lngResultID))
			{
				// ディレクトリ名とファイル名を設定（引数にて参照返却）
				$aryImageInfo['strTempImageDir'][]  = $objImageData->strdirectoryname;
				$aryImageInfo['strTempImageFile'][] = $objImageData->strfilename;

				// 実態ファイル名の取得
				$strImageFileName = $strDestDir.$objImageData->strdirectoryname."/".$objImageData->strfilename;
				// 既に存在済みかを確認する
				if(file_exists($strImageFileName))
				{
					continue;
				}
//echo "<br>ファイル抽出＞$strImageFileName<br>";
				// ディレクトリの存在確認
				if(!file_exists($strDestDir.$objImageData->strdirectoryname))
				{
					mkdir($strDestDir.$objImageData->strdirectoryname, 0777);
//echo "<br>".'ディレクトリ作成'." $objImageData->strdirectoryname"."<br>";
				}
				

				// 既存ファイルが存在しない場合のみ、ラージオブジェクト抽出
				pg_lo_export($objImageData->objimage, $strImageFileName);
			}

			// トランザクションコミット処理
			$objDB->transactionCommit();
			
			return true;
			
		}

		//
		//	概要：
		//		イメージファイルをラージオブジェクトとしてデータベースへ登録する
		//
		//	引数：
		//		$objDB				- データベースオブジェクト
		//		$strImageKeyCode	- イメージキーコード（製品コード等）
		//		$aryImageInfo		- イメージ情報格納構造体
		//		$strDestPath		- 出力先の基準ディレクトリ名
		//		$strTempImageDir	- テンポラリディレクトリ内のユニークディレクトリ名
		//		$strTempImageFile	- ユニークディレクトリ名内にコピーされるイメージファイル名
		//
		//	戻り値：
		//		処理成否
		//
		function addImageLo($objDB, $strImageKeyCode, $aryImageInfo, $strDestPath, $strTempImageDir, $strTempImageFile)
		{
			// イメージファイルパスの生成
			$strImagePath = $strDestPath.$strTempImageDir."/".$strTempImageFile;
//echo "<br>ファイル名生成$strImagePath<br>";
			if(!file_exists($strImagePath)) return false;

			// トランザクション開始処理
			if(!$objDB->transactionBegin()) return false;
//echo "<br>トランザクション開始<br>";

			// ファイルからラージオブジェクトをインポートし、オブジェクトIDを取得
			$lngOid   = pg_lo_import($objDB->ConnectID, $strImagePath);
//echo "<br>イメージパス＞$strImagePath<br>";
			// オブジェクトIDの取得に失敗した
			if(!$lngOid) return false;

			// テーブルロック処理を行う
			$arySql = array();
			$arySql[] = "select";
			$arySql[] = "	*";
			$arySql[] = "from";
			$arySql[] = "	t_image";
			$arySql[] = "for update";
			$strSql = implode("\n", $arySql);

			// ロック処理実行
			$lngResultID = $objDB->execute($strSql);

			// 最大のイメージコードを取得
			$arySql = array();
			$arySql[] = "select";
			$arySql[] = "	case when max(lngimagecode) is null then 1 else MAX(lngimagecode)+1 end as lngimagecode";
			$arySql[] = "from";
			$arySql[] = "	t_image";
			$strSql = implode("\n", $arySql);

			// 取得処理実行
			$lngResultID = $objDB->execute($strSql);
			
			// 結果をオブジェクトとして取得する
			$objImageCodeResult = $objDB->fetchObject($lngResultID, 0);

			//
			// t_image 登録用SQL作成
			$arySql = array();
			$arySql[] = "insert into t_image ";
			$arySql[] = "(";
			$arySql[] = "	lngimagecode";
			$arySql[] = "	,objimage";
			$arySql[] = "	,strdirectoryname";
			$arySql[] = "	,strfilename";
			$arySql[] = "	,strfiletype";
			$arySql[] = "	,lngfilesize";
			$arySql[] = "	,strnote";
			$arySql[] = "	,blninvalidflag";
			$arySql[] = ") values ";
			$arySql[] = "(";
			$arySql[] = "	".$objImageCodeResult->lngimagecode;
			$arySql[] = "	,".$lngOid;
			$arySql[] = "	,'".$strTempImageDir."'";
			$arySql[] = "	,'". $strTempImageFile."'";
			$arySql[] = "	,'". $aryImageInfo['type']."'";
			$arySql[] = "	,'". $aryImageInfo['size']."'";
			$arySql[] = "	,''";
			$arySql[] = "	,true";
			$arySql[] = ")";
			$strSql = implode("\n", $arySql);

//echo "<br>クラス内SQL＞$strSql<br>";
			// 登録処理実行
			$lngResultID = $objDB->execute($strSql);
			// 変更結果数を確認
			if( pg_affected_rows($lngResultID) <= 0 )
			{
				return false;
			}


			// テーブルロック処理を行う
			$arySql = array();
			$arySql[] = "select";
			$arySql[] = "	*";
			$arySql[] = "from";
			$arySql[] = "	m_imagerelation";
			$arySql[] = "for update";
			$strSql = implode("\n", $arySql);

			// ロック処理実行
			$lngResultID = $objDB->execute($strSql);

			// 最大のイメージコードを取得
			$arySql = array();
			$arySql[] = "select";
			$arySql[] = "	case when max(lngimagerelationcode) is null then 1 else MAX(lngimagerelationcode)+1 end as lngimagerelationcode";
			$arySql[] = "from";
			$arySql[] = "	m_imagerelation";
			$strSql = implode("\n", $arySql);

			// 取得処理実行
			$lngResultID = $objDB->execute($strSql);
			
			// 結果をオブジェクトとして取得する
			$objImageRelationCodeResult = $objDB->fetchObject($lngResultID, 0);

			//
			// m_imagerelation 登録
			$arySql = array();
			$arySql[] = "insert into m_imagerelation";
			$arySql[] = "	(";
			$arySql[] = "	lngimagerelationcode";
			$arySql[] = "	,lngimagecode";
			$arySql[] = "	,lngfunctioncode";
			$arySql[] = "	,strimagekeycode";
			$arySql[] = ") values";
			$arySql[] = "(";
			$arySql[] = "	".$objImageRelationCodeResult->lngimagerelationcode;
			$arySql[] = "	,".$objImageCodeResult->lngimagecode;
			$arySql[] = "	,9999";
			$arySql[] = "	,'".$strImageKeyCode."'";
			$arySql[] = ")";
			$strSql = implode("\n", $arySql);

			// 登録処理実行
			$lngResultID = $objDB->execute($strSql);
			// 変更結果数を確認
			if( pg_affected_rows($lngResultID) <= 0 )
			{
				return false;
			}

			// トランザクションコミット処理
			$objDB->transactionCommit();
			
			return true;
		}
		
		
	}
	
?>
