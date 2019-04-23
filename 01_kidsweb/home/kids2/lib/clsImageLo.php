<?php
// ----------------------------------------------------------------------------
/**
*       ���᡼�����顼�����֥����������饹
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
*			���᡼���ե������顼�����֥������ȤȤ����Ѥ���t_image / m_imagerelation �ơ��֥������
*
*       ��������
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
		//	���ס�
		// 		$_FILES �ѿ�����ɬ�פʥѥ顼�᡼�������
		//
		//	������
		//		$aryFiles	- $_FILES��¤��
		//		$strAlias	- �ե���������ꤹ��٤Υ����ꥢ��̾��<input type="file" name= �˻��ꤵ�줿̾����
		//
		//	����͡�
		//		$aryImageInfo	- ���᡼�������Ǽ��¤��
		//
		function getUploadFileInfo($aryFiles, $strAlias)
		{
			if(empty($strAlias)) return false;
			
			// ���åץ��ɤ��줿�ե����뤫������å�����
			if(!is_uploaded_file($aryFiles[$strAlias]['tmp_name']))
			{
				return false;
			}

			$aryFilesRet = array();
			// �ƥ�ݥ��̾�����
			$aryFilesRet['tmp_name'] = $aryFiles[$strAlias]['tmp_name'];
			// �ե�����̾�����
			$aryFilesRet['name'] = $aryFiles[$strAlias]['name'];
			// ���󤫤饿���פ����
			$aryFilesRet['type'] = $aryFiles[$strAlias]['type'];
			// ���󤫤饵���������
			$aryFilesRet['size'] = $aryFiles[$strAlias]['size'];
			
			$this->aryUploadFileInfo = $aryFilesRet;
			
			return $aryFilesRet;

		}

		//
		//	���ס�
		//		�ƥ�ݥ��ǥ��쥯�ȥ�˥��᡼���ե�����򥳥ԡ�����
		//
		//	������
		//		$strSourcePath	- �������ե�����̾
		//		$strDestDir		- ������δ��ǥ��쥯�ȥ�̾
		//		&$strUniqDir	- �ƥ�ݥ��ǥ��쥯�ȥ���Υ�ˡ����ǥ��쥯�ȥ�̾�ʻ����ֵѡ�
		//		&$strDestFile	- ��ˡ����ǥ��쥯�ȥ�̾��˥��ԡ�����륤�᡼���ե�����̾�ʻ����ֵѡ�
		//
		//	����͡�
		//		��������
		//
		function setTempImage($aryImageInfo, $strDestDir, &$strUniqDir="", &$strDestFile="")
		{

			//	��ĥ�Ҥμ���
			preg_match('/(\.jpg|\.gif|\.tif|\.png|\.bmp|\.avi|\.wmv|\.ai|\.rm|\.mov|\.mpg)$/', $aryImageInfo['name'], $aryRet);
			$strFileextension = $aryRet[0];
//	echo "<br>".'��ĥ��̾'." $strFileextension"."<br>";

			$strSourcePath = $aryImageInfo['tmp_name'];

			if(empty($strUniqDir))
			{
				// ��ˡ����ʥǥ��쥯�ȥ�̾������
				$strUniqDir = uniqid("",false);
			}
			$strTempDirPath = $strDestDir.$strUniqDir."/";
			
//	echo "<br>".'�ǥ��쥯�ȥ�̾'." $strTempDir"."<br>";
			
			if(!file_exists($strTempDirPath))
			{
				mkdir($strTempDirPath, 0777);
//	echo "<br>".'�ǥ��쥯�ȥ����'." $strTempDir"."<br>";
			}
			// ���ԡ���Υե�����ѥ�������
			$strDestFile = basename($strSourcePath).$strFileextension;
			$strDestFilePath = $strTempDirPath.$strDestFile;
			
//	echo "<br>�ե����륳�ԡ�".$strSourcePath."->".$strDestFile."<br>";
	
			// PHP�ե����륢�åץ��ɸ��������ǥ��쥯�ȥ�ذ�ư
			if(!move_uploaded_file($strSourcePath, $strDestFilePath))
			{
				return false;
			}
			chmod($strDestFilePath, 0777);
			return true;
		}

		//
		//	���ס�
		//		�顼�����֥������ȤȤ��ƥǡ����١�������¸����Ƥ����Τ򥤥᡼���ե�����Ȥ��ƽ��Ϥ���
		//
		//	������
		//		$objDB				- �ǡ����١������֥�������
		//		$strImageKeyCode	- ���᡼�����������ɡ����ʥ����ɡ�
		//		$strDestDir			- ������Υǥ��쥯�ȥ�̾
		//		&$aryImageInfo		- ���᡼�������Ǽ��¤��
		//
		//	����͡�
		//		��������
		//
		function getImageLo($objDB, $strImageKeyCode, $strDestDir, &$aryImageInfo)
		{
			// �ȥ�󥶥�����󳫻Ͻ���
			if(!$objDB->transactionBegin()) return false;
//echo "<br>�ȥ�󥶥�����󳫻�<br>";
			// ������SQL����
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
//echo "<br>SQL��$strSql<br>";

			// ��������¹�
			$lngResultID = $objDB->execute($strSql);
			// ��̿��μ���
			$lngRowCount = pg_num_rows($lngResultID);
			
			// ����̵�����
			if($lngRowCount <= 0) return false;
			
			// ��̿�ʬ�ξ�������
			$aryImageInfo = array();
			while($objImageData = pg_fetch_object($lngResultID))
			{
				// �ǥ��쥯�ȥ�̾�ȥե�����̾������ʰ����ˤƻ����ֵѡ�
				$aryImageInfo['strTempImageDir'][]  = $objImageData->strdirectoryname;
				$aryImageInfo['strTempImageFile'][] = $objImageData->strfilename;

				// ���֥ե�����̾�μ���
				$strImageFileName = $strDestDir.$objImageData->strdirectoryname."/".$objImageData->strfilename;
				// ����¸�ߺѤߤ����ǧ����
				if(file_exists($strImageFileName))
				{
					continue;
				}
//echo "<br>�ե�������С�$strImageFileName<br>";
				// �ǥ��쥯�ȥ��¸�߳�ǧ
				if(!file_exists($strDestDir.$objImageData->strdirectoryname))
				{
					mkdir($strDestDir.$objImageData->strdirectoryname, 0777);
//echo "<br>".'�ǥ��쥯�ȥ����'." $objImageData->strdirectoryname"."<br>";
				}
				

				// ��¸�ե����뤬¸�ߤ��ʤ����Τߡ��顼�����֥����������
				pg_lo_export($objImageData->objimage, $strImageFileName);
			}

			// �ȥ�󥶥�����󥳥ߥåȽ���
			$objDB->transactionCommit();
			
			return true;
			
		}

		//
		//	���ס�
		//		���᡼���ե������顼�����֥������ȤȤ��ƥǡ����١�������Ͽ����
		//
		//	������
		//		$objDB				- �ǡ����١������֥�������
		//		$strImageKeyCode	- ���᡼�����������ɡ����ʥ���������
		//		$aryImageInfo		- ���᡼�������Ǽ��¤��
		//		$strDestPath		- ������δ��ǥ��쥯�ȥ�̾
		//		$strTempImageDir	- �ƥ�ݥ��ǥ��쥯�ȥ���Υ�ˡ����ǥ��쥯�ȥ�̾
		//		$strTempImageFile	- ��ˡ����ǥ��쥯�ȥ�̾��˥��ԡ�����륤�᡼���ե�����̾
		//
		//	����͡�
		//		��������
		//
		function addImageLo($objDB, $strImageKeyCode, $aryImageInfo, $strDestPath, $strTempImageDir, $strTempImageFile)
		{
			// ���᡼���ե�����ѥ�������
			$strImagePath = $strDestPath.$strTempImageDir."/".$strTempImageFile;
//echo "<br>�ե�����̾����$strImagePath<br>";
			if(!file_exists($strImagePath)) return false;

			// �ȥ�󥶥�����󳫻Ͻ���
			if(!$objDB->transactionBegin()) return false;
//echo "<br>�ȥ�󥶥�����󳫻�<br>";

			// �ե����뤫��顼�����֥������Ȥ򥤥�ݡ��Ȥ������֥�������ID�����
			$lngOid   = pg_lo_import($objDB->ConnectID, $strImagePath);
//echo "<br>���᡼���ѥ���$strImagePath<br>";
			// ���֥�������ID�μ����˼��Ԥ���
			if(!$lngOid) return false;

			// �ơ��֥��å�������Ԥ�
			$arySql = array();
			$arySql[] = "select";
			$arySql[] = "	*";
			$arySql[] = "from";
			$arySql[] = "	t_image";
			$arySql[] = "for update";
			$strSql = implode("\n", $arySql);

			// ��å������¹�
			$lngResultID = $objDB->execute($strSql);

			// ����Υ��᡼�������ɤ����
			$arySql = array();
			$arySql[] = "select";
			$arySql[] = "	case when max(lngimagecode) is null then 1 else MAX(lngimagecode)+1 end as lngimagecode";
			$arySql[] = "from";
			$arySql[] = "	t_image";
			$strSql = implode("\n", $arySql);

			// ���������¹�
			$lngResultID = $objDB->execute($strSql);
			
			// ��̤򥪥֥������ȤȤ��Ƽ�������
			$objImageCodeResult = $objDB->fetchObject($lngResultID, 0);

			//
			// t_image ��Ͽ��SQL����
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

//echo "<br>���饹��SQL��$strSql<br>";
			// ��Ͽ�����¹�
			$lngResultID = $objDB->execute($strSql);
			// �ѹ���̿����ǧ
			if( pg_affected_rows($lngResultID) <= 0 )
			{
				return false;
			}


			// �ơ��֥��å�������Ԥ�
			$arySql = array();
			$arySql[] = "select";
			$arySql[] = "	*";
			$arySql[] = "from";
			$arySql[] = "	m_imagerelation";
			$arySql[] = "for update";
			$strSql = implode("\n", $arySql);

			// ��å������¹�
			$lngResultID = $objDB->execute($strSql);

			// ����Υ��᡼�������ɤ����
			$arySql = array();
			$arySql[] = "select";
			$arySql[] = "	case when max(lngimagerelationcode) is null then 1 else MAX(lngimagerelationcode)+1 end as lngimagerelationcode";
			$arySql[] = "from";
			$arySql[] = "	m_imagerelation";
			$strSql = implode("\n", $arySql);

			// ���������¹�
			$lngResultID = $objDB->execute($strSql);
			
			// ��̤򥪥֥������ȤȤ��Ƽ�������
			$objImageRelationCodeResult = $objDB->fetchObject($lngResultID, 0);

			//
			// m_imagerelation ��Ͽ
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

			// ��Ͽ�����¹�
			$lngResultID = $objDB->execute($strSql);
			// �ѹ���̿����ǧ
			if( pg_affected_rows($lngResultID) <= 0 )
			{
				return false;
			}

			// �ȥ�󥶥�����󥳥ߥåȽ���
			$objDB->transactionCommit();
			
			return true;
		}
		
		
	}
	
?>
