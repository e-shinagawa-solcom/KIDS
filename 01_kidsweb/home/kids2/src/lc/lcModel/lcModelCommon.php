<?php
//���饹�ե�������ɤ߹���
require_once 'db_common.php';

// ----------------------------------------------------------------------------
/**
 *       LC��Ϣ���̥��饹
 *
 *
 *       ��������
 *        getMaxLoginStateNum     ����������ơ��֥������δ����ֹ���������
 *        getUserAuth                   �桼�������¼���
 *        logout                  �������Ƚ���
 *        checkAccessIP           IP���ɥ쥹�����å�
 *
 *       ��������
 *
 */
// ----------------------------------------------------------------------------

class lcModel
{
    // ---------------------------------------------------------------
    /**
     *    �����󥻥å����ǡ����γ�ǧ
     *    @param  object  $objDB           DB���֥�������
     *    @access public
     */
    // ---------------------------------------------------------------
    public function fncIsSession($session_id)
    {
        //���饹������
        $db = new lcConnect();
        //�����������
        $sql = "
				select
					l.lngUserCode,
					date_trunc('second', l.dtmLoginTime) AS remaining,
					c.strValue AS timeout,
					ag.lngAuthorityGroupCode,
					ag.strAuthorityGroupName,
					u.strUserDisplayName,
					u.strUserID,
					u.strUserFullName,
					g.strGroupDisplayCode,
					g.strGroupDisplayName,
					u.strUserImageFileName
                from
					t_LoginSession l,
					m_CommonFunction c,
					m_User u,
					m_AuthorityGroup ag,
					m_Group g,
					m_GroupRelation gr
				where
					l.strSessionID LIKE '$1'
					AND u.bytinvalidflag = FALSE
					AND l.bytSuccessfulFlag = TRUE
					AND gr.bytDefaultFlag = TRUE
					AND c.strClass = 'timeout'
					AND u.lngAuthorityGroupCode = ag.lngAuthorityGroupCode
					AND u.lngUserCode = l.lngUserCode
					AND u.lngUserCode = gr.lngUserCode
					AND gr.lngGroupCode = g.lngGroupCode


            ";
        //�Х���ɤ�����
        $bind = array($session_id);

        //������¹�
        $result = $db->select_single($sql, $bind);

        //��������ֵ�
        return $result;
    }

    // ---------------------------------------------------------------
    /**
     *    ����������ơ��֥������δ����ֹ���������
     *    @param  object  $objDB           DB���֥�������
     *    @access public
     */
    // ---------------------------------------------------------------
    public function getMaxLoginStateNum()
    {
        //���饹������
        $db = new lcConnect();
        //�����������
        $sql = "
                select
                    max(lgno)
                from
                    m_acloginstate
            ";
        //�Х���ɤ�����
        $bind = array();

        //������¹�
        $result = $db->select_single($sql, $bind);
        //��������ֵ�
        return $result->max;

    }

    /**
     * ac�桼����ID��¸�ߥ����å���Ԥ�
     *
     * @param [string] $usrid
     * @return void
     */
    public function checkAcUsrid($usrid)
    {
        //���饹������
        $db = new lcConnect();
        //�����������
        $sql = "
                select
					usrno,
					usrid,
					usrauth,
					usrname,
					usrnote
                from
                    m_acuserinfo
                where
                	m_acuserinfo.usrid = $1";
        //�Х���ɤ�����
        $bind = array($usrid);
        //������¹�
        $result = $db->select($sql, $bind);

        if (count($result) > 0) {
            return true;
        } else {
            return false;
        }
    }

    // ---------------------------------------------------------------
    /**
     *    �桼�������¼���
     *    @param  object  $objDB           DB���֥�������
     *    @param  object  $param           �����Ϥ��ѥ�᡼��
     *    @access public
     */
    // ---------------------------------------------------------------
    public function getUserAuth($usrid)
    {
        //���饹������
        $db = new lcConnect();
        //�����������
        $sql = "
                select
                    usrAuth
                from
                    m_acuserinfo
                where
                	m_acuserinfo.usrid = $1
            ";
        //�Х���ɤ�����
        $bind = array($usrid);

        //������¹�
        $result = $db->select_single($sql, $bind);

        //��������ֵ�
        return $result->usrauth;
    }

    // ---------------------------------------------------------------
    /**
     *    ��¾����
     *    @param  object  $objDB           DB���֥�������
     *    @param  object  $param           �����Ϥ��ѥ�᡼��
     *    @access public
     */
    // ---------------------------------------------------------------
    public function chkEp($lgno, $userAuth, $usrid)
    {
        //���饹������
        $db = new lcConnect();

        $result;

        //���ȸ��¤Τ�
        if ($userAuth == 0) {
            //�����������
            $sql = "update
                        m_acloginstate
                    set
                        lgstate = '10000000'
                    where
                        lgno =  $1
                ";
            //�Х���ɤ�����
            $bind = array($lgno);

            //������¹�
            $result = $db->update($sql, $bind);

            //���
            $return_res = 0; //������Ԥ����ʤ�
            //�������ºߤ�
        } else if ($userAuth == 1) {
            //����������γ�ǧ
            $sql = "
	                select
	                    m_acuserinfo.usrid,
						m_acuserinfo.usrAuth,
						m_acloginstate.lgno,
						m_acloginstate.lgstate
	                from
	                    m_acuserinfo,m_acloginstate
	                where
						m_acuserinfo.usrname = m_acloginstate.lgusrname and
						lginymd is not null and lgoutymd is null and
						lgno <> $1 and
						lgstate is not null

	            ";
            //�Х���ɤ�����
            $bind = array($lgno);

            //������¹�
            $result = $db->select($sql, $bind);

            if (count($result) > 0) {
                if ($result[0]["usrid"] == $usrid) {
                    //�����������������Ȥ˹�������
                    $sql = "update
		                        m_acloginstate
		                    set
		                        lgoutymd = to_char(now(), 'YYYYMMDD'),
								lgouttime = to_char(now(), 'HH24:MI:SS'),
								lgstate = null
		                    where
		                    	lginymd is not null and
								lgintime is not null and
								lgoutymd is null and
								lgouttime is null and
								lgusrname in(select lgusrname from m_acloginstate where lgno = $1)
		                ";
                    //�Х���ɤ�����
                    $bind = array($lgno);

                    //������¹�
                    $result = $db->update($sql, $bind);

                    $return_res = 1; //Ʊ��ID�ǥ����󤷤Ƥ���
                } else {
                    if (substr($result["lgstate"], 2, 1) == '1') {
                        $lgstate = '10000000';
                        $return_res = 2; //Ʊ�츢�¼Ԥ������󤷤Ƥ���
                    } else {
                        $lgstate = '01000000';
                        $return_res = 0; //������Ԥ����ʤ���Ƚ�Ǥ���
                    }

                    //�����������
                    $sql = "update
		                        m_acloginstate
		                    set
		                        lgstate = $1
		                    where
		                        lgno =  $2
		                ";
                    //�Х���ɤ�����
                    $bind = array($lgstate, $lgno);

                    //������¹�
                    $result = $db->update($sql, $bind);
                }
            } else {
                $lgstate = '01000000';
                $return_res = 0; //������Ԥ����ʤ���Ƚ�Ǥ���

                //�����������
                $sql = "update
							m_acloginstate
						set
							lgstate = $1
						where
							lgno =  $2
					";
                //�Х���ɤ�����
                $bind = array($lgstate, $lgno);

                //������¹�
                $result = $db->update($sql, $bind);
            }
            return $return_res;
        } else {
            return false;
        }
    }

    // ---------------------------------------------------------------
    /**
     *    �������̵ͭ�����å�
     *    @param  object  $objDB           DB���֥�������
     *    @access public
     */
    // ---------------------------------------------------------------
    public function getUserCount()
    {
        //���饹������
        $db = new lcConnect();
        //�����������
        $sql = "
				select
					count(*)
				from
					m_acloginstate
				where
					lginymd is not null and
					lgintime is not null and
					lgoutymd is null and
					lgouttime is null and
					(substr(lgstate,1,1)='1' or substr(lgstate,2,1) = '1')

            ";
        //�Х���ɤ�����
        $bind = array();

        //������¹�
        $result = $db->select_single($sql, $bind);

        //��������ֵ�
        return $result;
    }

    // ---------------------------------------------------------------
    /**
     *    �طʿ�����
     *    @param  object  $objDB           DB���֥�������
     *    @access public
     */
    // ---------------------------------------------------------------
    public function getBackColor()
    {
        //���饹������
        $db = new lcConnect();
        //�����������
        $sql = "
				select
					strclrstatus,
					lngcolorred,
					lngcolorgreen,
					lngcolorblue
				from
					m_acbackcolorinfo
				where
					bytinvalidflag = false
				order by strclrno

            ";
        //�Х���ɤ�����
        $bind = array();

        //������¹�
        $result = $db->select($sql, $bind);

        //strclrstatus���Ȥ�Ϣ������Ȥ��Ƶͤ��(Ʊ���strckrstatus�Υǡ����Ͻ�˾��)
        $res;
        for ($i = 0; $i < count($result); $i++) {
            $res[$result[$i]["strclrstatus"]] = $result[$i];
        }

        //��������ֵ�
        return $res;
    }

    // ---------------------------------------------------------------
    /**
     *    ����������Υ������Ƚ���
     *    @param  object  $objDB           DB���֥�������
     *    @access public
     */
    // ---------------------------------------------------------------
    public function loginStateLogout($param)
    {
        //���饹������
        $db = new lcConnect();
        //�����������������Ȥ˹�������
        $sql = "update
					m_acloginstate
				set
					lgoutymd = to_char(now(), 'YYYYMMDD'),
					lgouttime = to_char(now(), 'HH24:MI:SS'),
					lgstate = null
				where
					lginymd is not null and
					lgintime is not null and
					lgoutymd is null and
					lgouttime is null and
					lgusrname in(select lgusrname from m_acloginstate where lgno = $1)
		    ";
        //�Х���ɤ�����
        $bind = array($param["lgno"]);

        //������¹�
        $result = $db->update($sql, $bind);

        //��������ֵ�
        return $result;
    }

    // ---------------------------------------------------------------
    /**
     *    �����������������
     *    @param  object  $objDB           DB���֥�������
     *    @access public
     */
    // ---------------------------------------------------------------
    public function getLoginState($user_id)
    {
        //���饹������
        $db = new lcConnect();
        //�����������
        $sql = "
				select
					m_acuserinfo.usrid,
					m_acuserinfo.usrAuth,
					m_acloginstate.lgno,
					m_acloginstate.lginymd,
					m_acloginstate.lgintime,
					m_acloginstate.lgusrname
				from
					m_acloginstate,m_acuserinfo
				where
					m_acuserinfo.usrname = m_acloginstate.lgusrname and
					lginymd is not null and lgoutymd is null

            ";
        //�Х���ɤ�����
        $bind = array();

        //������¹�
        $result = $db->select($sql, $bind);
        $res = array();
        $res["login_obj"] = $result;
        if (count($result) > 0) {
            if ($result["usrid"] == $user_id) {
                //Ʊ��ID�ǥ����󤷤Ƥ���
                $res["login_state"] = "1";
            } else {
                //Ʊ�츢�¼Ԥ������󤷤Ƥ���
                $res["login_state"] = "2";
            }
        } else {
            // ������Ԥ����ʤ�
            $res["login_state"] = "0";
        }

        return $res;
    }

    // ---------------------------------------------------------------
    /**
     *    �����������������
     *    @param  object  $objDB           DB���֥�������
     *    @access public
     */
    // ---------------------------------------------------------------
    public function getLcInfoDate()
    {
        //���饹������
        $db = new lcConnect();
        //�����������
        $sql = "
				select
					lcgetdate,
					lgusrname
				from
					m_acloginstate
				where
					lcgetdate is not null
				order by lcgetdate desc
            ";
        //������¹�
        $result = $db->select_single($sql, array());

        return $result->lcgetdate;
    }

    // ---------------------------------------------------------------
    /**
     *    �����ֹ�ˤ������������������
     *    @param  object  $objDB           DB���֥�������
     *    @access public
     */
    // ---------------------------------------------------------------
    public function getAcLoginstateBylgno($lgno)
    {
        //���饹������
        $db = new lcConnect();
        //�����������
        $sql = "
				select
                    *
				from
					m_acloginstate
				where
					lgno = $1
            ";
        //������¹�
        $result = $db->select_single($sql, array($lgno));

        return $result;
    }

    /**
     * �������������Ͽ
     *
     * @param [integer] $lgno
     * @param [string] $lgusrname
     * @return void
     */
    public function setLcLoginState($lgno, $lgusrname)
    {
        //���饹������
        $db = new lcConnect();
        //�����������
        $sql = "
				insert into m_acloginstate
				(
					lgno,
					lgusrname,
					lginymd,
					lgintime
				) VALUES (
					$1,
					$2,
					to_char(now(), 'YYYYMMDD'),
					to_char(now(), 'HH24:MI:SS')
				);
            ";
        //�Х���ɤ�����
        $bind = array(sprintf('%08d', ($lgno + 1)), $lgusrname);

        //������¹�
        $result = $db->insert($sql, $bind);

        return $result;
    }

    // ---------------------------------------------------------------
    /**
     *    LC�������
     *    @param  object  $objDB           DB���֥�������
     *    @access public
     */
    // ---------------------------------------------------------------
    public function getLcInfoData($data)
    {
        //���饹������
        $db = new lcConnect();
        //���ܼ���
        $sql = "
				select
					payfnameomit,
					opendate,
					portplace,
					pono,
					polineno,
					poreviseno,
					postate,
					payfcd,
					productcd,
					productname,
					productnumber,
					unitname,
					unitprice,
					moneyprice,
					shipstartdate,
					shipenddate,
					sumdate,
					poupdatedate,
					deliveryplace,
					currencyclass,
					lcnote,
					shipterm,
					validterm,
					bankname,
					bankreqdate,
					lcno,
					lcamopen,
					validmonth,
					usancesettlement,
					bldetail1date,
					bldetail1money,
					bldetail2date,
					bldetail2money,
					bldetail3date,
					bldetail3money,
					payfnameformal,
					productnamee,
					lcstate,
					bankcd,
					shipym
				from
					t_lcinfo
                ";
                
        switch ($data["mode"]) {
            case "0":
                break;
            case "1":
                //��о��
                $sql .= "
						where
                            opendate = '" . $data["from"] . "'";

                if ($data["from"] != "" && $data["to"] != "") {
                    $sql .= " and opendate between '" .$data["from"] ."' and '" . $data["to"] . "'";
                }
                if ($data["payfcd"] != "") {
                    $sql .= " and payfcd = '".$data["payfcd"] ."'";
                }
                if ($data["payfnameomit"] != "") {
                    $sql .= " and payfnameomit = '".$data["payfnameomit"] ."'";
                }
                if ($data["getDataModeFlg"] == 1) {
                    $sql .= " and lcstate in (0,3,4,7,8) ";
                }
                break;
            case "2":
                //���ߥ�졼�Ⱦ��
                $sql .= "
						where
							opendate = '" . $data["to"] . "'";
                if ($data["getDataModeFlg"] == 1) {
                    $sql .= " and lcstate in (0,3,4,7,8) ";
                }

                break;
        }

        $sql .= "
				order by pono,poreviseno,polineno
                ";
                
        //������¹�
        $result = $db->select($sql, array());
        return $sql;

        if (count($result) > 0) {
            return $result;
        } else {
            return false;
        }
    }

    // ---------------------------------------------------------------
    /**
     *    LC�������ñ��
     *    @param  object  $objDB           DB���֥�������
     *    @access public
     */
    // ---------------------------------------------------------------
    public function getLcInfoSingle($data)
    {
        //���饹������
        $db = new lcConnect();

        //���ܼ���
        $sql = "
				select
					payfnameomit,
					opendate,
					portplace,
					pono,
					polineno,
					poreviseno,
					postate,
					payfcd,
					productcd,
					productname,
					productnumber,
					unitname,
					unitprice,
					moneyprice,
					shipstartdate,
					shipenddate,
					sumdate,
					poupdatedate,
					deliveryplace,
					currencyclass,
					lcnote,
					shipterm,
					validterm,
					bankname,
					bankreqdate,
					lcno,
					lcamopen,
					validmonth,
					usancesettlement,
					bldetail1date,
					bldetail1money,
					bldetail2date,
					bldetail2money,
					bldetail3date,
					bldetail3money,
					payfnameformal,
					productnamee,
					lcstate,
					bankcd,
					shipym
				from
					t_lcinfo
				where
					pono = $1 and
					poreviseno = $2 and
					polineno = $3
				";
        //�Х���ɤ�����
        $bind = array($data["pono"], $data["poreviseno"], $data["polineno"]);

        //������¹�
        $result = $db->select_single($sql, $bind);

        if (count($result) > 0) {
            return $result;
        } else {
            return false;
        }
    }

    // ---------------------------------------------------------------
    /**
     *    LC����ñ�ι���
     *    @param  object  $objDB           DB���֥�������
     *    @access public
     */
    // ---------------------------------------------------------------
    public function updateLcInfoSingle($data)
    {
        try {
            //���饹������
            $db = new lcConnect();

//�ʲ�̤����-----------------------------------------
            /*
            $data����Ȥ���
            array(14) {
            ["method"]=>
            string(12) "updateLcEdit"
            ["pono"]=>
            string(10) "10010001  "
            ["poreviseno"]=>
            string(1) "1"
            ["polineno"]=>
            string(2) "1 "
            ["opendate"]=>
            string(6) "201001"
            ["unloadingareas"]=>
            string(5) "JAPAN"
            ["bankcd"]=>
            string(4) "0005"
            ["bankname"]=>
            string(9) "�����"
            ["bankreqdate"]=>
            string(10) "2010/01/15"
            ["lcno"]=>
            string(16) "2000179         "
            ["lcamopen"]=>
            string(10) "2020/01/18"
            ["validmonth"]=>
            string(10) "2020/03/03"
            ["lcstate"]=>
            string(1) "0"
            ["session_id"]=>
            string(32) "ce12cd7b6182cc60bbc1c8a7afa20432"
            }
             */

            //���ܼ���
            $sql = "

				";
            //�Х���ɤ�����
            //$bind = array($data["pono"],$data["poreviseno"],$data["polineno"]);

            //������¹�
            //$result = $db->update($sql, $bind);

            if (count($result) > 0) {
                return $result;
            } else {
                return false;
            }
        } catch (Exception $e) {
            //�۾ｪλ
            return false;
        }
    }

    // ---------------------------------------------------------------
    /**
     *    ������ι���
     *    @access public
     */
    // ---------------------------------------------------------------
    public function updateBaseOpenDate($data, $lgusrname)
    {
        //���饹������
        $db = new lcConnect();

        //�ǡ�������
        $sql = "update
					m_acbaseopendate
				set
					updateuser = $1,
					updatedate = to_char(now(), 'YYYYMMDD'),
					updatetime = to_char(now(), 'HH24:MI:SS'),
					invalidflag = TRUE
			";
        //�Х���ɤ�����
        $bind = array($lgusrname);
        //������¹�
        $result = $db->update($sql, $bind);

        //�ǡ��������ɲ�
        //baseno�κ����͡ܣ����ͤ����
        $sql = "SELECT max(baseno) AS baseno FROM m_acbaseopendate";
        //�Х���ɤ�����
        $bind = array();
        //������¹�
        $result = $db->select_single($sql, $bind);
        $next_baseno = sprintf('%04d', (intval($result->baseno) + 1));

        //�����������
        $sql = "
				insert into m_acbaseopendate
				(
					baseno,
					basedate,
					entryuser,
					entrydate,
					entrytime,
					updateuser,
					updatedate,
					updatetime,
					invalidflag
				) VALUES (
					$1 ,
					$2 ,
					$3 ,
					to_char(now(), 'YYYYMMDD'),
					to_char(now(), 'HH24:MI:SS'),
					$4 ,
					to_char(now(), 'YYYYMMDD'),
					to_char(now(), 'HH24:MI:SS'),
					FALSE
				);
			";
        //�Х���ɤ�����
        $bind = array(
            $next_baseno,
            $data,
            $lgusrname,
            $lgusrname
        );

        //������¹�
        $result = $db->insert($sql, $bind);

        return true;
    }

    // ---------------------------------------------------------------
    /**
     *    ��Ծ���μ���
     *    @access public
     */
    // ---------------------------------------------------------------
    public function getBankInfo()
    {
        //���饹������
        $db = new lcConnect();
        //�����������
        $sql = "
				select
					*
				from
					m_acbankinfo
				order by bankno
            ";
        //�Х���ɤ�����
        $bind = array();

        //������¹�
        $result = $db->select($sql, $bind);

        if (count($result) > 0) {
            return $result;
        } else {
            return false;
        }
    }

    /**
     * ��ԥޥ������ͭ���ǡ������������
     *
     * @return array
     */
    public function getValidBankInfo()
    {   
        //���饹������
        $db = new lcConnect();
        //�����������
        $sql = "
				select
                    bankomitname,
                    bankdivrate,
                    bankcd
				from
                    m_acbankinfo
                where
                    invalidflag = false
                order by bankdivrate desc
            ";
        //�Х���ɤ�����
        $bind = array();

        //������¹�
        $result = $db->select($sql, $bind);

        if (count($result) > 0) {
            return $result;
        } else {
            return false;
        }

    }

    // ---------------------------------------------------------------
    /**
     *    ��Ծ���ι���
     *    @access public
     */
    // ---------------------------------------------------------------
    public function updateBankInfo($data, $lgusrname)
    {
        //���饹������
        $db = new lcConnect();

        for ($i = 0; $i < count($data); $i++) {
            //�����������
            $sql = "
					select
						*
					from
						m_acbankinfo
					where
						bankcd = $1
				";
            //�Х���ɤ�����
            $bind = array($data[$i]["bankcd"]);

            //������¹�
            $result = $db->select_single($sql, $bind);

            if ($result != null) {
                //�ǡ�������
                $sql = "update
							m_acbankinfo
						set
							bankcd = $1 ,
							bankomitname = $2 ,
							bankformalname = $3 ,
							bankdivrate = $4 ,
							invalidflag = $5 ,
							updateuser = $6 ,
							updatedate = to_char(now(), 'YYYYMMDD'),
							updatetime = to_char(now(), 'HH24:MI:SS')
						where
							bankcd =  $7
					";
                //�Х���ɤ�����
                $bind = array(
                    $data[$i]["bankcd"],
                    $data[$i]["bankomitname"],
                    $data[$i]["bankformalname"],
                    $data[$i]["bankdivrate"],
                    $data[$i]["invalidflag"],
                    $lgusrname,
                    $data[$i]["bankcd"]
                );
                //������¹�
                $result = $db->update($sql, $bind);
            } else {
                //�ǡ��������ɲ�(���̾�ǤϿ����ɲäϹԤ��ʤ���)
                //bankno�κ����͡ܣ����ͤ����
                $sql = "SELECT max(bankno) AS bankno FROM m_acbankinfo";
                //�Х���ɤ�����
                $bind = array();
                //������¹�
                $result = $db->select_single($sql, $bind);
                $next_bankno = sprintf('%04d', (intval($result->bankno) + 1));

                //�����������
                $sql = "
						insert into m_acbankinfo
						(
							bankno,
							bankcd,
							bankomitname,
							bankformalname,
							bankdivrate,
							invalidflag,
							entryuser,
							entrydate,
							entrytime,
							updateuser,
							updatedate,
							updatetime
						) VALUES (
							$1 ,
							$2 ,
							$3 ,
							$4 ,
							$5 ,
							$6 ,
							$7 ,
							to_char(now(), 'YYYYMMDD'),
							to_char(now(), 'HH24:MI:SS'),
							$8 ,
							to_char(now(), 'YYYYMMDD'),
							to_char(now(), 'HH24:MI:SS')
						);
					";
                //�Х���ɤ�����
                $bind = array(
                    $next_bankno,
                    $data[$i]["bankcd"],
                    $data[$i]["bankomitname"],
                    $data[$i]["bankformalname"],
                    $data[$i]["bankdivrate"],
                    $data[$i]["invalidflag"],
                    $lgusrname,
                    $lgusrname
                );

                //������¹�
                $result = $db->insert($sql, $bind);
            }
        }

        return true;
    }

    // ---------------------------------------------------------------
    /**
     *    ��ʧ�����μ���
     *    @access public
     */
    // ---------------------------------------------------------------
    public function getPayfInfo()
    {
        //���饹������
        $db = new lcConnect();
        //�����������
        $sql = "
				select
					*
				from
					m_acpayfinfo
				order by payfno
            ";
        //�Х���ɤ�����
        $bind = array();

        //������¹�
        $result = $db->select($sql, $bind);

        if (count($result) > 0) {
            return $result;
        } else {
            return false;
        }
    }

    // ---------------------------------------------------------------
    /**
     *    ��ʧ�����ι���
     *    @access public
     */
    // ---------------------------------------------------------------
    public function updatePayfInfo($data, $lgusrname)
    {
        //���饹������
        $db = new lcConnect();
        for ($i = 0; $i < count($data); $i++) {
            //��������
            if ($data[$i]["del_flg"] != true) {
                //�����������
                $sql = "
						select
							payfomitname,
							payfformalname
						from
							m_acpayfinfo
						where
						    payfcd = $1
					";
                //�Х���ɤ�����
                $bind = array($data[$i]["payfcd"]);

                //������¹�
                $hit_payfinfo = $db->select_single($sql, $bind);
                if ($hit_payfinfo != null) {
                    //�ǡ�������
                    $sql = "update
								m_acpayfinfo
							set
								payfcd = $1,
								payfomitname = $2,
								payfformalname = $3,
								payfsendname = $4,
								payfsendfax = $5,
								invalidflag = $6,
								updateuser = $7,
								updatedate = to_char(now(), 'YYYYMMDD'),
								updatetime = to_char(now(), 'HH24:MI:SS')
							where
								payfcd =  $8
						";
                    //�Х���ɤ�����
                    $bind = array(
                        $data[$i]["payfcd"],
                        $data[$i]["payfomitname"],
                        $data[$i]["payfformalname"],
                        $data[$i]["payfsendname"],
                        $data[$i]["payfsendfax"],
                        $data[$i]["invalidflag"],
                        $lgusrname,
                        $data[$i]["payfcd"]
                    );

                    
                    //������¹�
                    $result = $db->update($sql, $bind);

                    //��������payfinfo��̾��������Ϥ��줿̾�Τ��ۤʤ����t_AcLcInfo�ι�����Ԥ������ͽ�ˤ�t_AcLcInfo�Ȥ��뤬t_lcinfo�ǤϤʤ��Τ���
                    if ($hit_payfinfo->payfomitname != $data[$i]["payfomitname"] || $hit_payfinfo->payfformalname != $data[$i]["payfformalname"]) {
                        $sql = "update
									t_aclcinfo
								set
									payfnameomit = $2,
									payfnameformal = $3
								where
									payfcd =  $1
							";
                        //�Х���ɤ�����
                        $bind = array(
                            $data[$i]["payfcd"],
                            $data[$i]["payfomitname"],
                            $data[$i]["payfformalname"],
                        );
                        //������¹�
                        $result = $db->update($sql, $bind);
                    }
                } else {
                    //�ǡ��������ɲ�
                    //payfno�κ����͡ܣ����ͤ����
                    $sql = "SELECT max(payfno) AS payfno FROM m_acpayfinfo";
                    //�Х���ɤ�����
                    $bind = array();
                    //������¹�
                    $result = $db->select_single($sql, $bind);
                    $next_payfno = sprintf('%04d', (intval($result->payfno) + 1));

                    //�����������
                    $sql = "
							insert into m_acpayfinfo
							(
								payfno,
								payfcd,
								payfomitname,
								payfformalname,
								payfsendname,
								payfsendfax,
								invalidflag,
								entryuser,
								entrydate,
								entrytime,
								updateuser,
								updatedate,
								updatetime
							) VALUES (
								$1 ,
								$2 ,
								$3 ,
								$4 ,
								$5 ,
								$6 ,
								$7 ,
								$8 ,
								to_char(now(), 'YYYYMMDD'),
								to_char(now(), 'HH24:MI:SS'),
								$9 ,
								to_char(now(), 'YYYYMMDD'),
								to_char(now(), 'HH24:MI:SS')
							);
						";
                    //�Х���ɤ�����
                    $bind = array(
                        $next_payfno,
                        $data[$i]["payfcd"],
                        $data[$i]["payfomitname"],
                        $data[$i]["payfformalname"],
                        $data[$i]["payfsendname"],
                        $data[$i]["payfsendfax"],
                        $data[$i]["invalidflag"],
                        $lgusrname,
                        $lgusrname
                    );

                    //������¹�
                    $result = $db->insert($sql, $bind);
                }
            } else {
                //�������
                $sql = "DELETE FROM m_acpayfinfo WHERE payfcd = $1";
                //�Х���ɤ�����
                $bind = array($data[$i]["payfcd"]);
                //������¹�
                $result = $db->delete($sql, $bind);
            }
        }

        return true;
    }

    // ---------------------------------------------------------------
    /**
     *    ��ԥꥹ�Ȥμ���
     *    @access public
     */
    // ---------------------------------------------------------------
    public function getBankList()
    {
        //���饹������
        $db = new lcConnect();
        //�����������
        $sql = "
				select
					*
				from
					m_acbankinfo
				where
					invalidFlag = false
            ";
        //�Х���ɤ�����
        $bind = array();

        //������¹�
        $result = $db->select($sql, $bind);

        if (count($result) > 0) {
            return $result;
        } else {
            return false;
        }
    }

    // ---------------------------------------------------------------
    /**
     *    �����Ͼ���μ���
     *    @access public
     */
    // ---------------------------------------------------------------
    public function getUnloadingAreas()
    {
        //���饹������
        $db = new lcConnect();
        //�����������
        $sql = "
				SELECT
					DISTINCT unloadingAreas
				FROM
					t_lcinfo
            ";
        //�Х���ɤ�����
        $bind = array();

        //������¹�
        $result = $db->select($sql, $bind);

        if (count($result) > 0) {
            return $result;
        } else {
            return false;
        }
    }

    // ---------------------------------------------------------------
    /**
     *    �̲߶�ʬ�ꥹ�Ȥμ���
     *    @access public
     */
    // ---------------------------------------------------------------
    public function getCurrencyClassList()
    {
        //���饹������
        $db = new lcConnect();
        //�����������
        $sql = "
				SELECT
					DISTINCT currencyclass
				FROM
					t_lcinfo
				WHERE
					lcstate = 0 or lcstate = 3 or lcstate = 4 or lcstate = 7 or lcstate = 8
				ORDER BY currencyclass
            ";
        //�Х���ɤ�����
        $bind = array();

        //������¹�
        $result = $db->select($sql, $bind);

        if (count($result) > 0) {
            return $result;
        } else {
            return false;
        }
    }

    // ---------------------------------------------------------------
    /**
     *    �̲߶�ʬ�ꥹ��(̤��ǧ�ޤ�)�μ���
     *    @access public
     */
    // ---------------------------------------------------------------
    public function getCurrencyClassListAll()
    {
        //���饹������
        $db = new lcConnect();
        //�����������
        $sql = "
				SELECT
					DISTINCT currencyclass
				FROM
					t_lcinfo
				WHERE
					lcstate = 0 or lcstate = 3 or lcstate = 4 or lcstate = 6 or lcstate = 7 or lcstate = 8
				ORDER BY currencyclass
            ";
        //�Х���ɤ�����
        $bind = array();

        //������¹�
        $result = $db->select($sql, $bind);

        if (count($result) > 0) {
            return $result;
        } else {
            return false;
        }
    }

    /**
     * ���¥����å�
     *
     * @param [string] $usrId
     * @return void
     */
    public function checkAuthority($usrId)
    {
        $authority = array();
        $authority["setting"] = false;
        $authority["lcinfo"] = false;
        $usrauth = $this->getUserAuth($usrId);
        if ($usrauth <> "") {
            if (substr($usrauth, 1, 1) == "1") {
                $authority["setting"] = true;
            }
            $authority["lcinfo"] = true;
        }
        return $authority;

    }

    /**
     * acLc����ꥹ�Ȥ��������
     *
     * @return void
     */
    public function getAcLcInfo()
    {
        // �������դ�꣱�������������դ����
        $oneyearold = date("Ym", strtotime("-12 month"));
        // �������դ�ꣴ���������դ����
        $fourmonths = date("Ym", strtotime("+4 month"));
        //���饹������
        $db = new lcConnect();
        //���������
        $sql = "
            SELECT
                pono
                , polineno
                , poreviseno
                , postate
                , opendate
                , portplace
                , payfcd
                , payfnameomit
                , payfnameformal
                , productcd
                , productname
                , productnamee
                , productnumber
                , unitname
                , unitprice
                , moneyprice
                , shipstartdate
                , shipenddate
                , sumdate
                , poupdatedate
                , deliveryplace
                , currencyclass
                , lcnote
                , shipterm
                , validterm
                , bankcd
                , bankname
                , bankreqdate
                , lcno
                , lcamopen
                , validmonth
                , usancesettlement
                , bldetail1date
                , bldetail1money
                , bldetail2date
                , bldetail2money
                , bldetail3date
                , bldetail3money
                , lcstate
                , entryuser
                , entrydate
                , entrytime
                , updateuser
                , updatedate
                , updatetime
                , invalidflag
                , shipym
            FROM
                t_aclcinfo
            WHERE
                opendate between $1 and $2
                or (opendate < $3 and moneyprice is not null and bldetail1money is null and lcstate in (0,3,4,7,8))
                or (opendate < $4 and moneyprice is not null and bldetail1money is not null and bldetail2money is null and moneyprice != bldetail1money and  lcstate in (0,3,4,7,8))
                or (opendate < $5 and moneyprice is not null and bldetail1money is not null and bldetail2money is not null and moneyprice != (bldetail1money + bldetail2money) and  lcstate in (0,3,4,7,8))
        ";
        //�Х���ɤ�����
        $bind = array($oneyearold, $fourmonths, $oneyearold, $oneyearold, $oneyearold);
        //������¹�
        $result = $db->select($sql, $bind);

        if (count($result) > 0) {
            return $result;
        } else {
            return false;
        }

    }

    /**
     * L/C����ǿ����������������
     *
     * @return void �ǿ��μ�����
     */
    public function getMaxLcGetDate()
    {
        //���饹������
        $db = new lcConnect();
        //�����������
        $sql = "
                select
                    max(lcgetdate)
                from
                    m_acloginstate
            ";
        //�Х���ɤ�����
        $bind = array();

        //������¹�
        $result = $db->select_single($sql, $bind);

        //��������ֵ�
        return $result->max;

    }

    /**
     * ACL/C�ǡ�����������
     *
     * @param [string] $date
     * @param [string] $time
     * @return void ������
     */
    public function deleteAcLcInfo($date, $time)
    {
        //���饹������
        $db = new lcConnect();
        //�����������
        $sql = "
                delete from t_aclcinfo
                where entryDate > $1
                or (entrydate = $2 and entrytime > $3)
            ";
        //�Х���ɤ�����
        $bind = array($date, $date, $time);

        //������¹�
        $result = $db->delete($sql, $bind);

        if (!$result) {
            echo "ACL/C���������Ԥ��ޤ�����\n";
            exit;
        }

        return pg_affected_rows($result);

    }

    /**
     * ACL/C���֤����˹�������
     *
     * @param [string] $pono
     * @param [string] $postate
     * @return void �������
     */
    public function updateAcLcStateToDelete($pono, $postate)
    {
        //���饹������
        $db = new lcConnect();
        //�����������
        $sql = "
                update t_aclcinfo
                set lcstate = 2,
                postate = $1
                where pono = $2
            ";
        //�Х���ɤ�����
        $bind = array($pono, $postate);

        //������¹�
        $result = $db->update($sql, $bind);

        if (!$result) {
            echo "ACL/C����ξ��ֹ������Ԥ��ޤ�����\n";
            exit;
        }

        return $result;
    }

    /**
     * ACL/C���������������
     *
     * @param [type] $pono
     * @return void
     */
    public function getAcLcCount($pono)
    {
        //���饹������
        $db = new lcConnect();
        //�����������
        $sql = "
                select
                    count(0)
                from
                    t_aclcinfo
                where pono = $1

            ";
        //�Х���ɤ�����
        $bind = array($pono);

        //������¹�
        $result = $db->select_single($sql, $bind);

        //��������ֵ�
        return $result->count;

    }

    /**
     * ȯ���ֹ�ˤ��ACL/C������������
     *
     * @param [string] $pono
     * @return void
     */
    public function getAcLcInfoByPono($pono)
    {

        //���饹������
        $db = new lcConnect();
        //�����������
        $sql = "
            select distinct
                pono
                , polineno
                , poreviseno
                , opendate
                , bankreqdate
            from
                t_aclcinfo
            where pono = $1
            order by
                poreviseno desc
                , polineno
        ";
        //�Х���ɤ�����
        $bind = array($pono);

        //������¹�
        $result = $db->select($sql, $bind);

        //��������ֵ�
        return $result;
    }
    /**
     * ȯ���ֹ�ˤ��ACL/C����ζ�԰��������������
     *
     * @param [string] $pono
     * @return void
     */
    public function getAcLcBankReqDate($pono)
    {

        //���饹������
        $db = new lcConnect();
        //�����������
        $sql = "
            select pono
                , polineno
                , poreviseno
                , bankreqdate
            from
                t_aclcinfo
            where pono = $1
            order by
                poreviseno desc
                , polineno
        ";
        //�Х���ɤ�����
        $bind = array($pono);

        //������¹�
        $result = $db->select($sql, $bind);
        $bankReqDate = "";
        foreach ($result as $data) {
            $bankReqDate = $data["bankreqdate"];
            if ($bankReqDate != "" && $data["poreviseno"] != "00") {
                return $bankReqDate;
            }
        }

        //��������ֵ�
        return $bankReqDate;
    }

    /**
     * ACL/C����ι��������������
     *
     * @param [integer] $pono
     * @param [integer] $polineno
     * @param [integer] $poreviseno
     * @return void
     */
    public function getPoUpdateDate($pono, $polineno, $poreviseno)
    {

        //���饹������
        $db = new lcConnect();
        //�����������
        $sql = "
            select poupdatedate
            from
                t_aclcinfo
            where pono = $1
                and polineno = $2
                and poreviseno = $3
        ";
        //�Х���ɤ�����
        $bind = array($pono, $polineno, $poreviseno);

        //������¹�
        $result = $db->select_single($sql, $bind);

        //��������ֵ�
        return $result->poupdatedate;
    }

    /**
     * ACL/C����ι�������LC���֤򹹿�����
     *
     * @param [integer] $pono
     * @param [integer] $polineno
     * @param [integer] $poreviseno
     * @param [string] $lcstate
     * @param [string] $poupdatedate
     * @return �������
     */
    public function updateAcLcUpdatedate($pono, $polineno, $poreviseno, $lcstate, $poupdatedate)
    {
        //���饹������
        $db = new lcConnect();
        //�����������
        $sql = "
                update t_aclcinfo
                set poupdatedate = $1,
                    lcstate = $2
                where pono = $3
                    and polineno = $4
                    and poreviseno = $5
            ";
        //�Х���ɤ�����
        $bind = array($poupdatedate, $lcstate, $pono, $polineno, $poreviseno);

        //������¹�
        $result = $db->update($sql, $bind);

        if (!$result) {
            echo "ACL/C����ι������ι������Ԥ��ޤ�����\n";
            exit;
        }

        return $result;
    }

    /**
     * ACL/C�������Ͽ����
     *
     * @param [array] $data
     * @return ��Ͽ���
     */
    public function insertAcLcInfo($data)
    {
        //���饹������
        $db = new lcConnect();
        //�����������
        $sql = "
            insert into t_aclcinfo
            values (" . $data["pono"]
            . "," . $data["polineno"]
            . "," . $data["poreviseno"]
            . "," . $data["postate"]
            . "," . $data["opendate"]
            . "," . $data["unloadingareas"]
            . "," . $data["payfcd"]
            . "," . $data["payfnameomit"]
            . "," . $data["payfnameformal"]
            . "," . $data["productcd"]
            . "," . $data["productname"]
            . "," . $data["productnamee"]
            . "," . $data["productnumber"]
            . "," . $data["unitname"]
            . "," . $data["unitprice"]
            . "," . $data["moneyprice"]
            . "," . $data["shipstartdate"]
            . "," . $data["shipenddate"]
            . "," . $data["sumdate"]
            . "," . $data["poupdatedate"]
            . "," . $data["deliveryplace"]
            . "," . $data["currencyclass"]
            . "," . $data["lcnote"]
            . "," . $data["shipterm"]
            . "," . $data["validterm"]
            . "," . $data["bankcd"]
            . "," . $data["bankname"]
            . "," . $data["bankreqdate"]
            . "," . $data["lcno"]
            . "," . $data["lcamopen"]
            . "," . $data["validmonth"]
            . "," . $data["usancesettlement"]
            . "," . $data["bldetail1date"]
            . "," . $data["bldetail1money"]
            . "," . $data["bldetail2date"]
            . "," . $data["bldetail2money"]
            . "," . $data["bldetail3date"]
            . "," . $data["bldetail3money"]
            . "," . $data["lcstate"]
            . "," . $data["entryuser"]
            . "," . $data["entrydate"]
            . "," . $data["entrytime"]
            . "," . $data["updateuser"]
            . "," . $data["updatedate"]
            . "," . $data["updatetime"]
            . "," . $data["shipym"]
            . ")";

        //�Х���ɤ�����
        $bind = array();

        //������¹�
        $result = $db->insert($sql, $bind);

        if (!$result) {
            echo "ACL/C�������Ͽ���Ԥ��ޤ�����\n";
            exit;
        }

        return $result;
    }

    /**
     * ACL/C���֤򹹿�����
     *
     * @param [string] $pono
     * @param [string] $postate
     * @return void �������
     */
    public function updateAcLcStateByLcState($pono)
    {
        //���饹������
        $db = new lcConnect();
        //�����������
        $sql = "
                update
                    t_aclcinfo
                set lcstate = 2
                where pono = $1
                and lcstate not in (2, 9, 10)
            ";
        //�Х���ɤ�����
        $bind = array($pono);

        //������¹�
        $result = $db->update($sql, $bind);

        if (!$result) {
            echo "LC���֤ˤ��ACL/C����ξ��ֹ������Ԥ��ޤ�����\n";
            exit;
        }

        return $result;
    }

    /**
     *����������������
     *
     * @return �����
     */
    public function getBaseDate()
    {
        //���饹������
        $db = new lcConnect();
        //�����������
        $sql = "
                    select
                        basedate
                    from
                        m_acbaseopendate
                    where
                        invalidflag = false
                ";
        //�Х���ɤ�����
        $bind = array();

        //������¹�
        $result = $db->select_single($sql, $bind);

        if (!$result) {
            echo "������μ������Ԥ��ޤ�����\n";
            exit;
        }

        return $result->basedate;
    }

    /**
     * ACL/C����Υ����ץ��򹹿�����
     *
     * @param [integer] $pono
     * @param [string] $opendate
     * @return �������
     */
    public function updateAcLcOpendate($pono, $opendate)
    {
        //���饹������
        $db = new lcConnect();
        //�����������
        $sql = "
                update
                    t_aclcinfo
                set opendate = $1
                where pono = $2
            ";
        //�Х���ɤ�����
        $bind = array($opendate, $pono);

        //������¹�
        $result = $db->update($sql, $bind);

        if (!$result) {
            echo "ACL/C����Υ����ץ��ι������Ԥ��ޤ�����\n";
            exit;
        }

        return $result;

    }

    /**
     * pono,polineno�ˤ��ACL/C������������
     *
     * @param [integer] $pono
     * @param [integer] $polineno
     * @return ACL/C����
     */
    public function getReviseAcLcInfo($pono, $polineno)
    {
        //���饹������
        $db = new lcConnect();
        //�����������
        $sql = "
            select
                lcno,
                bankname,
                usancesettlement,
                bldetail1date,
                bldetail1money,
                bldetail2date,
                bldetail2money,
                bldetail3date,
                bldetail3money,
                opendate,
                lcstate,
                bankcd,
                bankreqdate
            from
                t_aclcinfo
            where pono = $1
                and polineno = $2
            order by poreviseno desc
        ";
        //�Х���ɤ�����
        $bind = array($pono, $polineno);

        //������¹�
        $result = $db->select($sql, $bind);

        //��������ֵ�
        return $result;

    }

    /**
     * ACL/C����򹹿�����
     *
     * @param [array] $data
     * @return �������
     */
    public function updateReviseAcLcInfo($data)
    {
        //���饹������
        $db = new lcConnect();
        //�����������
        $sql = "
                update
                    t_aclcinfo
                set opendate = '" . $data["opendate"] . "'"
            . ", bankcd = '" . $data["bankcd"] . "'"
            . ", bankname = '" . $data["bankname"] . "'"
            . ", bankreqdate = " . $data["bankreqdate"]
            . ", lcno = '" . $data["lcno"] . "'"
            . ", lcamopen = '" . $data["lcamopen"] . "'"
            . ", validmonth = '" . $data["validmonth"] . "'"
            . ", usancesettlement = " . $data["usancesettlement"]
            . ", bldetail1date = " . $data["bldetail1date"]
            . ", bldetail1money = " . $data["bldetail1money"]
            . ", bldetail2date = " . $data["bldetail2date"]
            . ", bldetail2money = " . $data["bldetail2money"]
            . ", bldetail3date = " . $data["bldetail3date"]
            . ", bldetail3money = " . $data["bldetail3money"]
            . ", lcstate = " . $data["lcstate"]
            . "where pono = " . $data["pono"]
            . "and polineno = " . $data["polineno"]
            . "and poreviseno = " . $data["poreviseno"];
        //�Х���ɤ�����
        $bind = array();

        //������¹�
        $result = $db->update($sql, $bind);

        if (!$result) {
            echo "ACL/C����ι������Ԥ��ޤ�����\n";
            exit;
        }

        return $result;

    }

    /**
     * ACL/C����ξ��֤��Х����˹�������
     *
     * @param [type] $pono
     * @param [type] $poreviseno
     * @return void
     */
    public function updateAcLcStateToRevise($pono, $poreviseno)
    {
        //���饹������
        $db = new lcConnect();
        //�����������
        $sql = "
                update
                    t_aclcinfo
                set lcstate = 1
                where pono = $1
                and poreviseno = $2
                and lcstate not in (1, 2, 5, 10)
            ";
        //�Х���ɤ�����
        $bind = array($pono, $poreviseno);

        //������¹�
        $result = $db->update($sql, $bind);

        if (!$result) {
            echo "ACL/C����ξ���(��Х���)�������Ԥ��ޤ�����\n";
            exit;
        }

        return $result;
    }

    /**
     * ̤��ǧACL/C����ξ��֤򹹿�����
     *
     * @param [type] $pono
     * @param [type] $poreviseno
     * @return void
     */
    public function updateUnapprovedAcLcState($pono, $polineno, $poreviseno, $lcstate, $postate)
    {
        //���饹������
        $db = new lcConnect();
        //�����������
        $sql = "
                update
                    t_aclcinfo
                set lcstate = $1,
                postate = $2
                where pono = $3
                and polineno = $4
                and poreviseno = $5
                and lcstate not in (1, 2, 5, 9, 10)
            ";
        //�Х���ɤ�����
        $bind = array($lcstate, $postate, $pono, $polineno, $poreviseno);
        //������¹�
        $result = $db->update($sql, $bind);

        return $result;
    }

    /**
     * ̤��ǧ��ACL/C������������
     *
     * @return ̤��ǧ��ACL/C����ꥹ��
     */
    public function getUnapprovedAcLcInfo()
    {
        //���饹������
        $db = new lcConnect();
        //�����������
        $sql = "
                select
                    distinct pono,
                    poreviseno,
                    shipstartdate,
                    poupdatedate,
                    polineno,
                    bankreqdate
                from
                    t_aclcinfo
                where postate = '̤��ǧ'
                and pono is not null
                order by pono, poreviseno desc
            ";
        //�Х���ɤ�����
        $bind = array();

        //������¹�
        $result = $db->select($sql, $bind);

        if (!$result) {
            echo "̤��ǧ��ACL/C����μ������Ԥ��ޤ�����\n";
            exit;
        }

        return $result;
    }

    /**
     * po�ֹ�,po��Х����ֹ�ˤ��ACL/C����ξ��֤򹹿�����
     *
     * @param [integer] $pono
     * @param [string] $opendate
     * @return �������
     */
    public function updateAcLcState($pono, $poreviseno, $lcstate)
    {
        //���饹������
        $db = new lcConnect();
        //�����������
        $sql = "
                update
                    t_aclcinfo
                set lcstate = $1
                where pono = $2
                and poreviseno =$3
            ";
        //�Х���ɤ�����
        $bind = array($lcstate, $pono, $poreviseno);

        //������¹�
        $result = $db->update($sql, $bind);

        return $result;
    }
    /**
     * ����������������
     *
     * @param [integer] $bankcd
     * @return ��������
     */
    public function getAcBankInfo($bankcd)
    {
        //���饹������
        $db = new lcConnect();
        //�����������
        $sql = "
                    select
                        *
                    from
                        m_acbankinfo
                    where
                        bankcd = $1
                ";
        //�Х���ɤ�����
        $bind = array($bankcd);

        //������¹�
        $result = $db->select_single($sql, $bind);

        if (!$result) {
            echo "��������μ������Ԥ��ޤ�����\n";
            exit;
        }

        return $result;
    }

    /**
     * ��ʧ�������������
     *
     * @param [integer] $payfcd
     * @return ��ʧ�����
     */
    public function getAcPayfInfo($payfcd)
    {
        //���饹������
        $db = new lcConnect();
        //�����������
        $sql = "
                    select
                        payfOmitname,
                        payfformalname
                    from
                        m_acpayfinfo
                    where
                        payfcd = $1
                ";
        //�Х���ɤ�����
        $bind = array($payfcd);

        //������¹�
        $result = $db->select_single($sql, $bind);

        if (!$result) {
            echo "��ʧ�����μ������Ԥ��ޤ�����\n";
            exit;
        }

        return $result;
    }

    /**
     * L/C���������򹹿�����
     *
     * @param [string] $lgno
     * @param [string] $lcgetdate
     * @return void
     */
    public function updateLcGetDate($lgno, $lcgetdate)
    {
        //���饹������
        $db = new lcConnect();
        //�����������
        $sql = "update
                        m_acloginstate
                    set
                        lcgetdate = $1
                    where
                        lgno =  $2
                "; //�Х���ɤ�����
        $bind = array($lcgetdate, $lgno);

        //������¹�
        $result = $db->update($sql, $bind);

        if (!$result) {
            echo "L/C���������ι������Ԥ��ޤ�����\n";
            exit;
        }

        return $result;
    }

    /**
     * ���Ѿ��֤ν������Ԥ�
     *
     * @param [string] $lgno
     * @param [string] $lcgetdate
     * @return void
     */
    public function updateLgStateToInit($lgno)
    {
        //���饹������
        $db = new lcConnect();
        //�����������
        //�����������
        $sql = "update
                        m_acloginstate
                    set
                        lgstate = null
                    where
                        lgno =  $1
                "; //�Х���ɤ�����
        $bind = array($lgno);

        //������¹�
        $result = $db->update($sql, $bind);

        if (!$result) {
            echo "���Ѿ��֤ν�������Ԥ��ޤ�����\n";
            exit;
        }

        return $result;
    }

    /**
     * ACL/C����(������)�򹹿�����
     *
     * @param [array] $data
     * @return �������
     */
    public function updateAcLcInfo($data)
    {
        //���饹������
        $db = new lcConnect();
        //�����������
        $sql = "
                update
                    t_aclcinfo
                set postate = $1
                    ,opendate = $2
                    ,portplace = $3
                    ,payfcd = $4
                    ,payfnameomit = $5
                    ,payfnameformal = $6
                    ,productcd = $7
                    ,productname = $8
                    ,productnamee = $9
                    ,productnumber = $10
                    ,unitname = $11
                    ,unitprice = $12
                    ,moneyprice = $13
                    ,shipstartdate = $14
                    ,shipenddate = $15
                    ,sumdate = $16
                    ,poupdatedate = $17
                    ,deliveryplace = $18
                    ,currencyclass = $19
                    ,lcnote = $20
                    ,shipterm = $21
                    ,validterm = $22
                    ,bankcd = $23
                    ,bankname = $24
                    ,bankreqdate = $25
                    ,lcno = $26
                    ,lcamopen = $27
                    ,validmonth = $28
                    ,usancesettlement = $29
                    ,bldetail1date = $30
                    ,bldetail1money = $31
                    ,bldetail2date = $32
                    ,bldetail2money = $33
                    ,bldetail3date = $34
                    ,bldetail3money = $35
                    ,lcstate = $36
                    ,updateuser = $37
                    ,updatedate = $38
                    ,updatetime = $39
                    ,invalidflag = $40
                    ,shipym = $41
                where pono = $42
                and polineno = $43
                and poreviseno = $44
            ";
        //�Х���ɤ�����
        $bind = array($data["postate"]
            , $data["opendate"]
            , $data["portplace"]
            , $data["payfcd"]
            , $data["payfnameomit"]
            , $data["payfnameformal"]
            , $data["productcd"]
            , $data["productname"]
            , $data["productnamee"]
            , $data["productnumber"]
            , $data["unitname"]
            , $data["unitprice"]
            , $data["moneyprice"]
            , $data["shipstartdate"]
            , $data["shipenddate"]
            , $data["sumdate"]
            , $data["poupdatedate"]
            , $data["deliveryplace"]
            , $data["currencyclass"]
            , $data["lcnote"]
            , $data["shipterm"]
            , $data["validterm"]
            , $data["bankcd"]
            , $data["bankname"]
            , $data["bankreqdate"]
            , $data["lcno"]
            , $data["lcamopen"]
            , $data["validmonth"]
            , $data["usancesettlement"]
            , $data["bldetail1date"]
            , $data["bldetail1money"]
            , $data["bldetail2date"]
            , $data["bldetail2money"]
            , $data["bldetail3date"]
            , $data["bldetail3money"]
            , $data["lcstate"]
            , $data["updateuser"]
            , $data["updatedate"]
            , $data["updatetime"]
            , $data["invalidflag"]
            , $data["shipym"]
            , $data["pono"]
            , $data["polineno"]
            , $data["poreviseno"]);
            return $bind;
        //������¹�
        $result = $db->update($sql, $bind);

        if (!$result) {
            echo "ACL/C����(������)�ι������Ԥ��ޤ�����\n";
            exit;
        }

        return $result;

    }

    // ---------------------------------------------------------------
    /**
     *    ����������Υ������Ƚ���
     *    @param  object  $param           DB���֥�������
     *    @access public
     */
    // ---------------------------------------------------------------
    public function updateLgoutym($param)
    {
        //���饹������
        $db = new lcConnect();
        //�����������������Ȥ˹�������
        $sql = "update
					m_acloginstate
				set
					lgoutymd = to_char(now(), 'YYYYMMDD'),
					lgouttime = to_char(now(), 'HH24:MI:SS'),
					lgstate = null
				where
					lginymd is not null and
					lgintime is not null and
					lgoutymd is null and
					lgouttime is null and
					lgusrname in(select lgusrname from m_acloginstate where lgno = $1)
		    ";
        //�Х���ɤ�����
        $bind = array($param["lgno"]);

        //������¹�
        $result = $db->update($sql, $bind);

        //��������ֵ�
        return $result;
    }

    /**
     * L/C����ݡ��������򹹿�����
     *
     * @param [string] $lgno
     * @param [string] $lcimpdate
     * @return void
     */
    public function updateLcImpDate($lgno, $lcimpdate)
    {
        //���饹������
        $db = new lcConnect();
        //�����������
        $sql = "update
                        m_acloginstate
                    set
                        lcimpdate = $1
                    where
                        lgno =  $2
                "; //�Х���ɤ�����
        $bind = array($lcimpdate, $lgno);

        //������¹�
        $result = $db->update($sql, $bind);

        if (!$result) {
            echo "L/C����ݡ��������ι������Ԥ��ޤ�����\n";
            exit;
        }

        return $result;
    }

    /**
     * L/C�������ݡ��������򹹿�����
     *
     * @param [string] $lgno
     * @param [string] $lcexpdate
     * @return void
     */
    public function updateLcExpDate($lgno, $lcimpdate)
    {
        //���饹������
        $db = new lcConnect();
        //�����������
        $sql = "update
                        m_acloginstate
                    set
                        lcexpdate = $1
                    where
                        lgno =  $2
                "; //�Х���ɤ�����
        $bind = array($lcexpdate, $lgno);

        //������¹�
        $result = $db->update($sql, $bind);

        if (!$result) {
            echo "L/C�������ݡ��������ι������Ԥ��ޤ�����\n";
            exit;
        }

        return $result;
    }

    /**
     * ���ո��ޥ���������������
     *
     * @return void
     */
    public function getSendInfo()
    {
        //���饹������
        $db = new lcConnect();
        //�����������
        $sql = "
				select
					*
				from
                    m_acsendinfo
                where
                    invalidflag = false
            ";
        //�Х���ɤ�����
        $bind = array();

        //������¹�
        $result = $db->select($sql, $bind);

        if (count($result) > 0) {
            return $result;
        } else {
            return false;
        }
    }
}
