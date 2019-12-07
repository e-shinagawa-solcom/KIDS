<?php

// ----------------------------------------------------------------------------
/**
*       LC����  LC���󳫻�
*       initLcInfo��¹Ԥ���������ζ��β��̤Ǥ���
*/
// ----------------------------------------------------------------------------

	// �ɤ߹���
	include('conf.inc');
	//���̥ե������ɤ߹���
	require SRC_ROOT . "lc/lcModel/lcModelCommon.php";
	require (LIB_FILE);


	$objDB		= new clsDB();
	$objAuth	= new clsAuth();

	$objDB->open("", "", "", "");


	//-------------------------------------------------------------------------
	// �� �ѥ�᡼������
	//-------------------------------------------------------------------------
	$aryData = $_REQUEST;
	// ʸ��������å�
	$aryCheck["strSessionID"]          = "null:numenglish(32,32)";
	$aryResult = fncAllCheck( $aryData, $aryCheck );
	fncPutStringCheckError( $aryResult, $objDB );

	// ���å�����ǧ
	$objAuth = fncIsSession( $aryData["strSessionID"], $objAuth, $objDB );
	// ���å����˥��å����ID�򥻥å�
	setcookie("strSessionID",$aryData["strSessionID"]);

	//�桼����ID����(Ⱦ�ѥ��ڡ��������뤿��)
	$user_id = trim($objAuth->UserID);
	
	$objDB->close();

// select-function/index.php�Υ������������Ʊ���ν���
	//�������֥����ƥ�DB��³
	$lcModel		= new lcModel();

	//LC����������μ���
	$lcgetdate = $lcModel->getLcInfoDate();

	//���������Ƚ�����
	$logined_flg = false;
	$login_state = $lcModel->getLoginState($user_id);
	if($login_state["login_state"] == "1"){
		//�������Ƚ�����Ԥ�
		$lcModel->loginStateLogout($login_state["login_obj"]);
	} else if($login_state["login_state"] == "2"){
		//Ʊ�츢�¼Ԥ������󤷤Ƥ���
		//lginymd < �������դξ��
		$ymd = date('Ymd',  strtotime($lcgetdate->lcgetdate));
		if($ymd < time()){
			//�������楢�顼��ɽ���ե饰
			$logined_flg = true;
		}
	}

	//����������κ�������ֹ�μ���
	$login_max_num = $lcModel->getMaxLoginStateNum();

	//�������������Ͽ
	$lcModel->setLcLoginState($login_max_num, $objAuth->UserFullName);


	//�桼�������¤μ���
	$login_user_auth = $lcModel->getUserAuth($user_id);

	
	$lcModel->close();

	//HTML�ؤΰ����Ϥ��ǡ���
	$aryData["lc_info_date"] = date('Ymd',  strtotime($lcgetdate->lcgetdate));
	$aryData["lc_info_time"] = date('h:m:s',  strtotime($lcgetdate->lcgetdate));
	$aryData["user_nm"] = $login_state["lgusrname"];
	$aryData["session_id"] = $aryData["strSessionID"];


// �����ޤ�select-function/index.php�Υ������������Ʊ���ν���
	
	//HTML�ؤΰ����Ϥ��ǡ���
	$aryData["session_id"] = $aryData["strSessionID"];

	echo fncGetReplacedHtmlWithBase("lc/base_lc.html", "lc/set/start.tmpl", $aryData ,$objAuth );
	//js�ؤΰ����Ϥ��ǡ���
	$lcInfoDate = array(
	    "lcgetdate" => $lcgetdate->lcgetdate, 
	    "lgusrname" => $lcgetdate->lgusrname
	);
	$arr = array(
		"login_state" => $login_state,
		"session_id" => $aryData["strSessionID"],
		"lcInfoDate" => $lcInfoDate,
		"logined_flg" => $logined_flg,
		"login_user_auth"=> $login_user_auth
	);
	mb_convert_variables('UTF-8' , 'EUC-JP' , $arr );
	echo "<script>
	    $(function(){lcInit('". json_encode($arr) ."');});
	    document.location.href='/lc/set/index.php?strSessionID=" .$aryData["strSessionID"] . "';</script>";

	return true;
?>