<?php
//クラスファイルの読み込み
require_once 'db_common.php';
// ----------------------------------------------------------------------------
/**
 *       LC関連画面クラス
 *
 *
 *       処理概要
 *        getMaxLoginStateNum     ログイン状況テーブルより最大の管理番号を取得する
 *        getUserAuth                   ユーザー権限取得
 *        logout                  ログアウト処理
 *        checkAccessIP           IPアドレスチェック
 *
 *       更新履歴
 *
 */
// ----------------------------------------------------------------------------

class lcModel
{
    /**
     *    接続ID
     *    @var string
     */
    public $lcConn;

    /**
     *    コンストラクタ
     *    クラス内の初期化を行う
     *
     *    @return void
     *    @access public
     */
    public function __construct()
    {
        // 接続IDの初期化
        $db = new lcConnect();
        $db->open();
        $this->lcConn = $db;

    }

    /**
     * クラス内の解放を行う
     *
     * @return void
     */
    public function close()
    {
        $db = $this->lcConn;
        $db->close();
    }

    public function transactionBegin()
    {
        $db = $this->lcConn;
        $db->transactionBegin();
    } 

    public function transactionCommit()
    {
        $db = $this->lcConn;
        $db->transactionCommit();
    } 
    // ---------------------------------------------------------------
    /**
     *    ログインセッションデータの確認
     *    @param  object  $objDB           DBオブジェクト
     *    @access public
     */
    // ---------------------------------------------------------------
    public function fncIsSession($session_id)
    {
        //クラスの生成
        $db = $this->lcConn;
        //クエリの生成
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
        //バインドの設定
        $bind = array($session_id);

        //クエリ実行
        $result = $db->select_single($sql, $bind);

        //検索結果返却
        return $result;
    }

    // ---------------------------------------------------------------
    /**
     *    ログイン状況テーブルより最大の管理番号を取得する
     *    @param  object  $objDB           DBオブジェクト
     *    @access public
     */
    // ---------------------------------------------------------------
    public function getMaxLoginStateNum()
    {
        //クラスの生成
        $db = $this->lcConn;
        //クエリの生成
        $sql = "
                select
                    max(lgno)
                from
                    m_acloginstate
            ";
        //バインドの設定
        $bind = array();

        //クエリ実行
        $result = $db->select_single($sql, $bind);
        //検索結果返却
        return $result->max;

    }

    /**
     * acユーザーIDの存在チェックを行う
     *
     * @param [string] $usrid
     * @return void
     */
    public function checkAcUsrid($usrid)
    {
        //クラスの生成
        $db = $this->lcConn;
        //クエリの生成
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
        //バインドの設定
        $bind = array($usrid);
        //クエリ実行
        $result = $db->select($sql, $bind);

        if (count($result) > 0) {
            return true;
        } else {
            return false;
        }
    }

    // ---------------------------------------------------------------
    /**
     *    ユーザー権限取得
     *    @param  object  $objDB           DBオブジェクト
     *    @param  object  $param           引き渡しパラメータ
     *    @access public
     */
    // ---------------------------------------------------------------
    public function getUserAuth($usrid)
    {
        //クラスの生成
        $db = $this->lcConn;
        //クエリの生成
        $sql = "
                select
                    usrAuth
                from
                    m_acuserinfo
                where
                	m_acuserinfo.usrid = $1
            ";
        //バインドの設定
        $bind = array($usrid);

        //クエリ実行
        $result = $db->select_single($sql, $bind);

        //検索結果返却
        return $result->usrauth;
    }

    // ---------------------------------------------------------------
    /**
     *    排他処理
     *    @param  object  $objDB           DBオブジェクト
     *    @param  object  $param           引き渡しパラメータ
     *    @access public
     */
    // ---------------------------------------------------------------
    public function chkEp($lgno, $userAuth, $usrid)
    {
        //クラスの生成
        $db = $this->lcConn;

        $result;

        //参照権限のみ
        if ($userAuth == 0) {
            //クエリの生成
            $sql = "update
                        m_acloginstate
                    set
                        lgstate = '10000000'
                    where
                        lgno =  $1
                ";
            //バインドの設定
            $bind = array($lgno);

            //クエリ実行
            $result = $db->executeNonQuery($sql, $bind);

            //結果
            $return_res = 0; //ログイン者がいない
            //更新権限在り
        } else if ($userAuth == 1) {
            //ログイン状況の確認
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
            //バインドの設定
            $bind = array($lgno);

            //クエリ実行
            $result = $db->select($sql, $bind);

            if (count($result) > 0) {
                if ($result[0]["usrid"] == $usrid) {
                    //ログイン状況をログアウトに更新する
                    $sql = "update
		                        m_acloginstate
		                    set
		                        lgoutymd = $1,
							    lgouttime = $2,
								lgstate = null
		                    where
		                    	lginymd is not null and
								lgintime is not null and
								lgoutymd is null and
								lgouttime is null and
								lgusrname in(select lgusrname from m_acloginstate where lgno = $3)
		                ";
                    //バインドの設定
                    $bind = array(fncGetDateString(), fncGetTimeString(), $lgno);

                    //クエリ実行
                    $result = $db->executeNonQuery($sql, $bind);

                    $return_res = 1; //同一IDでログインしている
                } else {
                    if (substr($result["lgstate"], 2, 1) == '1') {
                        $lgstate = '10000000';
                        $return_res = 2; //同一権限者がログインしている
                    } else {
                        $lgstate = '01000000';
                        $return_res = 0; //ログイン者がいないと判断する
                    }

                    //クエリの生成
                    $sql = "update
		                        m_acloginstate
		                    set
		                        lgstate = $1
		                    where
		                        lgno =  $2
		                ";
                    //バインドの設定
                    $bind = array($lgstate, $lgno);

                    //クエリ実行
                    $result = $db->executeNonQuery($sql, $bind);
                }
            } else {
                $lgstate = '01000000';
                $return_res = 0; //ログイン者がいないと判断する

                //クエリの生成
                $sql = "update
							m_acloginstate
						set
							lgstate = $1
						where
							lgno =  $2
					";
                //バインドの設定
                $bind = array($lgstate, $lgno);

                //クエリ実行
                $result = $db->executeNonQuery($sql, $bind);
            }
            return $return_res;
        } else {
            return false;
        }
    }

    // ---------------------------------------------------------------
    /**
     *    ログイン者有無チェック
     *    @param  object  $objDB           DBオブジェクト
     *    @access public
     */
    // ---------------------------------------------------------------
    public function getUserCount()
    {
        //クラスの生成
        $db = $this->lcConn;
        //クエリの生成
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
        //バインドの設定
        $bind = array();

        //クエリ実行
        $result = $db->select_single($sql, $bind);

        //検索結果返却
        return $result;
    }

    // ---------------------------------------------------------------
    /**
     *    背景色取得
     *    @param  object  $objDB           DBオブジェクト
     *    @access public
     */
    // ---------------------------------------------------------------
    public function getBackColor()
    {
        //クラスの生成
        $db = $this->lcConn;
        //クエリの生成
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
        //バインドの設定
        $bind = array();

        //クエリ実行
        $result = $db->select($sql, $bind);

        //strclrstatusごとに連想配列として詰める(同一のstrckrstatusのデータは順に上書き)
        $res;
        for ($i = 0; $i < count($result); $i++) {
            $res[$result[$i]["strclrstatus"]] = $result[$i];
        }

        //検索結果返却
        return $res;
    }

    // ---------------------------------------------------------------
    /**
     *    ログイン状況のログアウト処理
     *    @param  object  $objDB           DBオブジェクト
     *    @access public
     */
    // ---------------------------------------------------------------
    public function loginStateLogout($param)
    {
        //クラスの生成
        $db = $this->lcConn;
        //ログイン状況をログアウトに更新する
        $sql = "update
					m_acloginstate
				set
					lgoutymd = $1,
					lgouttime = $2,
					lgstate = null
				where
					lginymd is not null and
					lgintime is not null and
					lgoutymd is null and
					lgouttime is null and
					lgusrname in(select lgusrname from m_acloginstate where lgno = $3)
		    ";
        //バインドの設定
        $bind = array(fncGetDateString(), fncGetTimemString(), $param["lgno"]);

        //クエリ実行
        $result = $db->executeNonQuery($sql, $bind);

        //検索結果返却
        return $result;
    }

    // ---------------------------------------------------------------
    /**
     *    ログイン状況情報を取得
     *    @param  object  $objDB           DBオブジェクト
     *    @access public
     */
    // ---------------------------------------------------------------
    public function getLoginState($user_id)
    {
        //クラスの生成
        $db = $this->lcConn;
        //クエリの生成
        // Webアプリケーションへの移行により、logoutせずに終了するケースがあるため、
        // 直近のlgnoのみを取得する。
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
				order by lgno desc limit 1;

            ";
        //バインドの設定
        $bind = array();

        //クエリ実行
        $result = $db->select($sql, $bind);
        $res = array();
        $res["login_obj"] = $result;
        if (count($result) > 0) {
            if ($result["usrid"] == $user_id) {
                //同一IDでログインしている
                $res["login_state"] = "1";
            } else {
                //同一権限者がログインしている
                $res["login_state"] = "2";
            }
        } else {
            // ログイン者がいない
            $res["login_state"] = "0";
        }

        return $res;
    }

    // ---------------------------------------------------------------
    /**
     *    ログイン状況情報を取得
     *    @param  object  $objDB           DBオブジェクト
     *    @access public
     */
    // ---------------------------------------------------------------
    public function getLcInfoDate()
    {
        //クラスの生成
        $db = $this->lcConn;
        //クエリの生成
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
        //クエリ実行
        $result = $db->select_single($sql, array());

        return $result;
    }

    // ---------------------------------------------------------------
    /**
     *    管理番号によりログイン状況情報を取得
     *    @param  object  $objDB           DBオブジェクト
     *    @access public
     */
    // ---------------------------------------------------------------
    public function getAcLoginstateBylgno($lgno)
    {
        //クラスの生成
        $db = $this->lcConn;
        //クエリの生成
        $sql = "
				select
                    *
				from
					m_acloginstate
				where
					lgno = $1
            ";
        //クエリ実行
        $result = $db->select_single($sql, array($lgno));

        return $result;
    }

    /**
     * ログイン状況の登録
     *
     * @param [integer] $lgno
     * @param [string] $lgusrname
     * @return void
     */
    public function setLcLoginState($lgno, $lgusrname)
    {
        //クラスの生成
        $db = $this->lcConn;
        //クエリの生成
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
					$3,
					$4
				);
            ";
        //バインドの設定
        $bind = array(sprintf('%08d', ($lgno + 1)), $lgusrname, fncGetDateString(), fncGetTimeString());

        //クエリ実行
        $result = $db->executeNonQuery($sql, $bind);

        return $result;
    }

    // ---------------------------------------------------------------
    /**
     *    LC情報取得
     *    @param  object  $objDB           DBオブジェクト
     *    @access public
     */
    // ---------------------------------------------------------------
    public function getLcInfoData($data)
    {
        //クラスの生成
        $db = $this->lcConn;
        //基本取得
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
					productrevisecd,
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
                //抽出条件
                $sql .= "
						where
                            opendate = '" . $data["from"] . "'";

                if ($data["from"] != "" && $data["to"] != "") {
                    $sql .= " and opendate between '" . $data["from"] . "' and '" . $data["to"] . "'";
                }
                if ($data["payfcd"] != "") {
                    $sql .= " and payfcd = '" . $data["payfcd"] . "'";
                }
                if ($data["payfnameomit"] != "") {
                    $sql .= " and payfnameomit = '" . $data["payfnameomit"] . "'";
                }
                if ($data["getDataModeFlg"] == 1) {
                    $sql .= " and lcstate in (0,3,4,7,8) ";
                }
                break;
            case "2":
                //シミュレート条件
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

        //クエリ実行
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
     *    LC情報取得単体
     *    @param  object  $objDB           DBオブジェクト
     *    @access public
     */
    // ---------------------------------------------------------------
    public function getLcInfoSingle($data)
    {
        //クラスの生成
        $db = $this->lcConn;

        //基本取得
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
					productrevisecd,
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
        //バインドの設定
        $bind = array($data["pono"], $data["poreviseno"], $data["polineno"]);

        //クエリ実行
        $result = $db->select_single($sql, $bind);

        if (count($result) > 0) {
            return $result;
        } else {
            return false;
        }
    }

    // ---------------------------------------------------------------
    /**
     *    LC情報単体更新
     *    @param  object  $objDB           DBオブジェクト
     *    @access public
     */
    // ---------------------------------------------------------------
    public function updateLcInfoSingle($data)
    {
        try {
            //クラスの生成
            $db = $this->lcConn;

//以下未実装-----------------------------------------
            /*
            $dataの中身の例
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
            string(9) "三東Ｕ"
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

            //基本取得
            $sql = "

				";
            //バインドの設定
            //$bind = array($data["pono"],$data["poreviseno"],$data["polineno"]);

            //クエリ実行
            //$result = $db->executeNonQuery($sql, $bind);

            if (count($result) > 0) {
                return $result;
            } else {
                return false;
            }
        } catch (Exception $e) {
            //異常終了
            return false;
        }
    }

    // ---------------------------------------------------------------
    /**
     *    基準日の更新
     *    @access public
     */
    // ---------------------------------------------------------------
    public function updateBaseOpenDate($data, $lgusrname)
    {
        //クラスの生成
        $db = $this->lcConn;

        //データ更新
        $sql = "update
					m_acbaseopendate
				set
					updateuser = $1,
					updatedate = $2,
					updatetime = $3,
					invalidflag = TRUE
			";
        //バインドの設定
        $bind = array($lgusrname, fncGetDateString(), fncGetTimeString());
        //クエリ実行
        $result = $db->executeNonQuery($sql, $bind);

        //データ新規追加
        //basenoの最大値＋１の値を取得
        $sql = "SELECT max(baseno) AS baseno FROM m_acbaseopendate";
        //バインドの設定
        $bind = array();
        //クエリ実行
        $result = $db->select_single($sql, $bind);
        $next_baseno = sprintf('%04d', (intval($result->baseno) + 1));

        //クエリの生成
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
					$4 ,
					$5 ,
					$6 ,
					$7 ,
					$8 ,
					FALSE
				);
			";
        //バインドの設定
        $bind = array(
            $next_baseno,
            $data,
            $lgusrname,
            $lgusrname,
            fncGetDateString(),
            fncGetTimeString(),
            fncGetDateString(),
            fncGetTimeString()
        );

        //クエリ実行
        $result = $db->executeNonQuery($sql, $bind);

        return true;
    }

    // ---------------------------------------------------------------
    /**
     *    銀行情報の取得
     *    @access public
     */
    // ---------------------------------------------------------------
    public function getBankInfo()
    {
        //クラスの生成
        $db = $this->lcConn;
        //クエリの生成
        $sql = "
				select
					*
				from
					m_acbankinfo
				order by bankno
            ";
        //バインドの設定
        $bind = array();

        //クエリ実行
        $result = $db->select($sql, $bind);

        if (count($result) > 0) {
            return $result;
        } else {
            return false;
        }
    }

    /**
     * 銀行マスタより有効データを取得する
     *
     * @return array
     */
    public function getValidBankInfo()
    {
        //クラスの生成
        $db = $this->lcConn;
        //クエリの生成
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
        //バインドの設定
        $bind = array();

        //クエリ実行
        $result = $db->select($sql, $bind);

        if (count($result) > 0) {
            return $result;
        } else {
            return false;
        }

    }

    // ---------------------------------------------------------------
    /**
     *    銀行情報の更新
     *    @access public
     */
    // ---------------------------------------------------------------
    public function updateBankInfo($data, $lgusrname)
    {
        //クラスの生成
        $db = $this->lcConn;

        for ($i = 0; $i < count($data); $i++) {
            //クエリの生成
            $sql = "
					select
						*
					from
						m_acbankinfo
					where
						bankcd = $1
				";
            //バインドの設定
            $bind = array($data[$i]["bankcd"]);

            //クエリ実行
            $result = $db->select_single($sql, $bind);

            if ($result != null) {
                //データ更新
                $sql = "update
							m_acbankinfo
						set
							bankcd = $1 ,
							bankomitname = $2 ,
							bankformalname = $3 ,
							bankdivrate = $4 ,
							invalidflag = $5 ,
							updateuser = $6 ,
							updatedate = $7,
							updatetime = $8
						where
							bankcd =  $9
					";
                //バインドの設定
                $bind = array(
                    $data[$i]["bankcd"],
                    $data[$i]["bankomitname"],
                    $data[$i]["bankformalname"],
                    $data[$i]["bankdivrate"],
                    $data[$i]["invalidflag"],
                    $lgusrname,
                    fncGetDateString(),
                    fncGetTimeString(),
                    $data[$i]["bankcd"]
                );
                //クエリ実行
                $result = $db->executeNonQuery($sql, $bind);
            } else {
                //データ新規追加(画面上では新規追加は行われない？)
                //banknoの最大値＋１の値を取得
                $sql = "SELECT max(bankno) AS bankno FROM m_acbankinfo";
                //バインドの設定
                $bind = array();
                //クエリ実行
                $result = $db->select_single($sql, $bind);
                $next_bankno = sprintf('%04d', (intval($result->bankno) + 1));

                //クエリの生成
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
							$8 ,
							$9 ,
							$10 ,
							$11 ,
							$12
						);
					";
                //バインドの設定
                $bind = array(
                    $next_bankno,
                    $data[$i]["bankcd"],
                    $data[$i]["bankomitname"],
                    $data[$i]["bankformalname"],
                    $data[$i]["bankdivrate"],
                    $data[$i]["invalidflag"],
                    $lgusrname,
                    fncGetDateString(),
                    fncGetTimeString(),
                    $lgusrname,
                    fncGetDateString(),
                    fncGetTimeString()
                );

                //クエリ実行
                $result = $db->executeNonQuery($sql, $bind);
            }
        }

        return true;
    }

    // ---------------------------------------------------------------
    /**
     *    支払先情報の取得
     *    @access public
     */
    // ---------------------------------------------------------------
    public function getPayfInfo()
    {
        //クラスの生成
        $db = $this->lcConn;
        //クエリの生成
        $sql = "
				select
					*
				from
					m_acpayfinfo
				order by payfno
            ";
        //バインドの設定
        $bind = array();

        //クエリ実行
        $result = $db->select($sql, $bind);

        if (count($result) > 0) {
            return $result;
        } else {
            return false;
        }
    }

    // ---------------------------------------------------------------
    /**
     *    支払先情報の更新
     *    @access public
     */
    // ---------------------------------------------------------------
    public function updatePayfInfo($data, $lgusrname)
    {
        //クラスの生成
        $db = $this->lcConn;
        for ($i = 0; $i < count($data); $i++) {
            //更新処理
            if ($data[$i]["del_flg"] != true) {
                //クエリの生成
                $sql = "
						select
							payfomitname,
							payfformalname
						from
							m_acpayfinfo
						where
						    payfcd = $1
					";
                //バインドの設定
                $bind = array($data[$i]["payfcd"]);

                //クエリ実行
                $hit_payfinfo = $db->select_single($sql, $bind);
                if ($hit_payfinfo != null) {
                    //データ更新
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
								updatedate = $8,
								updatetime = $9
							where
								payfcd =  $10
						";
                    //バインドの設定
                    $bind = array(
                        $data[$i]["payfcd"],
                        $data[$i]["payfomitname"],
                        $data[$i]["payfformalname"],
                        $data[$i]["payfsendname"],
                        $data[$i]["payfsendfax"],
                        $data[$i]["invalidflag"],
                        $lgusrname,
                        fncGetDateString(),
                        fncGetTimeString(),
                        $data[$i]["payfcd"]
                    );

                    //クエリ実行
                    $result = $db->executeNonQuery($sql, $bind);

                    //取得したpayfinfoの名称類と入力された名称が異なる場合はt_AcLcInfoの更新を行う←仕様書にはt_AcLcInfoとあるがt_lcinfoではないのか？
                    if ($hit_payfinfo->payfomitname != $data[$i]["payfomitname"] || $hit_payfinfo->payfformalname != $data[$i]["payfformalname"]) {
                        $sql = "update
									t_aclcinfo
								set
									payfnameomit = $2,
									payfnameformal = $3
								where
									payfcd =  $1
							";
                        //バインドの設定
                        $bind = array(
                            $data[$i]["payfcd"],
                            $data[$i]["payfomitname"],
                            $data[$i]["payfformalname"],
                        );
                        //クエリ実行
                        $result = $db->executeNonQuery($sql, $bind);
                    }
                } else {
                    //データ新規追加
                    //payfnoの最大値＋１の値を取得
                    $sql = "SELECT max(payfno) AS payfno FROM m_acpayfinfo";
                    //バインドの設定
                    $bind = array();
                    //クエリ実行
                    $result = $db->select_single($sql, $bind);
                    $next_payfno = sprintf('%04d', (intval($result->payfno) + 1));

                    //クエリの生成
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
								$9 ,
								$10 ,
								$11 ,
								$12 ,
								$13
							);
						";
                    //バインドの設定
                    $bind = array(
                        $next_payfno,
                        $data[$i]["payfcd"],
                        $data[$i]["payfomitname"],
                        $data[$i]["payfformalname"],
                        $data[$i]["payfsendname"],
                        $data[$i]["payfsendfax"],
                        $data[$i]["invalidflag"],
                        $lgusrname,
                        fncGetDateString(),
                        fncGetTimeString(),
                        $lgusrname,
                        fncGetDateString(),
                        fncGetTimeString()
                    );

                    //クエリ実行
                    $result = $db->executeNonQuery($sql, $bind);
                }
            } else {
                //削除処理
                $sql = "DELETE FROM m_acpayfinfo WHERE payfcd = $1";
                //バインドの設定
                $bind = array($data[$i]["payfcd"]);
                //クエリ実行
                $result = $db->executeNonQuery($sql, $bind);
            }
        }

        return true;
    }

    // ---------------------------------------------------------------
    /**
     *    銀行リストの取得
     *    @access public
     */
    // ---------------------------------------------------------------
    public function getBankList()
    {
        //クラスの生成
        $db = $this->lcConn;
        //クエリの生成
        $sql = "
				select
					*
				from
					m_acbankinfo
				where
					invalidFlag = false
            ";
        //バインドの設定
        $bind = array();

        //クエリ実行
        $result = $db->select($sql, $bind);

        if (count($result) > 0) {
            return $result;
        } else {
            return false;
        }
    }

    // ---------------------------------------------------------------
    /**
     *    荷揚地情報の取得
     *    @access public
     */
    // ---------------------------------------------------------------
    public function getUnloadingAreas()
    {
        //クラスの生成
        $db = $this->lcConn;
        //クエリの生成
        $sql = "
				SELECT
					DISTINCT unloadingAreas
				FROM
					t_lcinfo
            ";
        //バインドの設定
        $bind = array();

        //クエリ実行
        $result = $db->select($sql, $bind);

        if (count($result) > 0) {
            return $result;
        } else {
            return false;
        }
    }

    // ---------------------------------------------------------------
    /**
     *    通貨区分リストの取得
     *    @access public
     */
    // ---------------------------------------------------------------
    public function getCurrencyClassList()
    {
        //クラスの生成
        $db = $this->lcConn;
        //クエリの生成
        $sql = "
				SELECT
					DISTINCT currencyclass
				FROM
					t_lcinfo
				WHERE
					lcstate = 0 or lcstate = 3 or lcstate = 4 or lcstate = 7 or lcstate = 8
				ORDER BY currencyclass
            ";
        //バインドの設定
        $bind = array();

        //クエリ実行
        $result = $db->select($sql, $bind);

        if (count($result) > 0) {
            return $result;
        } else {
            return false;
        }
    }

    // ---------------------------------------------------------------
    /**
     *    通貨区分リスト(未承認含む)の取得
     *    @access public
     */
    // ---------------------------------------------------------------
    public function getCurrencyClassListAll()
    {
        //クラスの生成
        $db = $this->lcConn;
        //クエリの生成
        $sql = "
				SELECT
					DISTINCT currencyclass
				FROM
					t_lcinfo
				WHERE
					lcstate = 0 or lcstate = 3 or lcstate = 4 or lcstate = 6 or lcstate = 7 or lcstate = 8
				ORDER BY currencyclass
            ";
        //バインドの設定
        $bind = array();

        //クエリ実行
        $result = $db->select($sql, $bind);

        if (count($result) > 0) {
            return $result;
        } else {
            return false;
        }
    }

    /**
     * 権限チェック
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
        if ($usrauth != "") {
            if (substr($usrauth, 1, 1) == "1") {
                $authority["setting"] = true;
            }
            $authority["lcinfo"] = true;
        }
        return $authority;

    }

    /**
     * acLc情報リストを取得する
     *
     * @return void
     */
    public function getAcLcInfo()
    {
        // 現在日付より１２ヶ月前の日付を取得
        $oneyearold = date("Ym", strtotime("-12 month"));
        // 現在日付より４ヶ月後の日付を取得
        $fourmonths = date("Ym", strtotime("+4 month"));
        //クラスの生成
        $db = $this->lcConn;
        //クエリの生
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
                , productrevisecd
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
        //バインドの設定
        $bind = array($oneyearold, $fourmonths, $oneyearold, $oneyearold, $oneyearold);
        //クエリ実行
        $result = $db->select($sql, $bind);

        if (count($result) > 0) {
            return $result;
        } else {
            return false;
        }

    }

    /**
     * L/C情報最新取得日を取得する
     *
     * @return void 最新の取得日
     */
    public function getMaxLcGetDate()
    {
        //クラスの生成
        $db = $this->lcConn;
        //クエリの生成
        $sql = "
                select
                    max(lcgetdate)
                from
                    m_acloginstate
            ";
        //バインドの設定
        $bind = array();

        //クエリ実行
        $result = $db->select_single($sql, $bind);

        //検索結果返却
        return $result->max;

    }

    /**
     * ACL/Cデータを削除する
     *
     * @param [string] $date
     * @param [string] $time
     * @return void 削除件数
     */
    public function deleteAcLcInfo($date, $time)
    {
        //クラスの生成
        $db = $this->lcConn;
        //クエリの生成
        $sql = "
                delete from t_aclcinfo
                where entryDate > $1
                or (entrydate = $2 and entrytime > $3)
            ";
        //バインドの設定
        $bind = array($date, $date, $time);

        //クエリ実行
        $result = $db->executeNonQuery($sql, $bind);

        if ($result < 0) {
            echo "ACL/C情報削除失敗しました。\n";
            exit;
        }
//        return pg_affected_rows($result);

        return $result;

    }

    /**
     * ACL/C状態を削除に更新する
     *
     * @param [string] $pono
     * @param [string] $postate
     * @return void 更新件数
     */
    public function updateAcLcStateToDelete($pono)
    {
        //クラスの生成
        $db = $this->lcConn;
        //クエリの生成
        $sql = "
                update t_aclcinfo
                set lcstate = 2
                where pono = $1
            ";
        //バインドの設定
        $bind = array($pono);
        //クエリ実行
        $result = $db->executeNonQuery($sql, $bind);

        if ($result < 0) {
            echo "ACL/C情報の状態更新失敗しました。\n";
            exit;
        }

        return $result;
    }

    /**
     * ACL/C情報件数を取得する
     *
     * @param [type] $pono
     * @return void
     */
    public function getAcLcCount($pono)
    {
        //クラスの生成
        $db = $this->lcConn;
        //クエリの生成
        $sql = "
                select
                    count(0)
                from
                    t_aclcinfo
                where pono = $1

            ";
        //バインドの設定
        $bind = array($pono);

        //クエリ実行
        $result = $db->select_single($sql, $bind);

        //検索結果返却
        return $result->count;

    }

    /**
     * 発注番号によりACL/C情報を取得する
     *
     * @param [string] $pono
     * @return void
     */
    public function getAcLcInfoByPono($pono)
    {

        //クラスの生成
        $db = $this->lcConn;
        //クエリの生成
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
        //バインドの設定
        $bind = array($pono);

        //クエリ実行
        $result = $db->select($sql, $bind);

        //検索結果返却
        return $result;
    }
    /**
     * 発注番号によりACL/C情報の銀行依頼日を取得する
     *
     * @param [string] $pono
     * @return void
     */
    public function getAcLcBankReqDate($pono)
    {

        //クラスの生成
        $db = $this->lcConn;
        //クエリの生成
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
        //バインドの設定
        $bind = array($pono);

        //クエリ実行
        $result = $db->select($sql, $bind);
        $bankReqDate = "";
        foreach ($result as $data) {
            $bankReqDate = $data["bankreqdate"];
            if ($bankReqDate != "" && $data["poreviseno"] != "00") {
                return $bankReqDate;
            }
        }

        //検索結果返却
        return $bankReqDate;
    }

    /**
     * ACL/C情報の更新日を取得する
     *
     * @param [integer] $pono
     * @param [integer] $polineno
     * @param [integer] $poreviseno
     * @return void
     */
    public function getPoUpdateDate($pono, $polineno, $poreviseno)
    {

        //クラスの生成
        $db = $this->lcConn;
        //クエリの生成
        $sql = "
            select poupdatedate
            from
                t_aclcinfo
            where pono = $1
                and polineno = $2
                and poreviseno = $3
        ";
        //バインドの設定
        $bind = array($pono, $polineno, $poreviseno);

        //クエリ実行
        $result = $db->select_single($sql, $bind);

        //検索結果返却
        return $result->poupdatedate;
    }

    /**
     * ACL/C情報の更新日とLC状態を更新する
     *
     * @param [integer] $pono
     * @param [integer] $polineno
     * @param [integer] $poreviseno
     * @param [string] $lcstate
     * @param [string] $poupdatedate
     * @return 更新件数
     */
    public function updateAcLcUpdatedate($pono, $polineno, $poreviseno, $lcstate, $poupdatedate)
    {
        //クラスの生成
        $db = $this->lcConn;
        //クエリの生成
        $sql = "
                update t_aclcinfo
                set poupdatedate = $1,
                    lcstate = $2
                where pono = $3
                    and polineno = $4
                    and poreviseno = $5
            ";
        //バインドの設定
        $bind = array($poupdatedate, $lcstate, $pono, $polineno, $poreviseno);

        //クエリ実行
        $result = $db->executeNonQuery($sql, $bind);

        if ($result < 0) {
            echo "ACL/C情報の更新日の更新失敗しました。\n";
            exit;
        }

        return $result;
    }

    /**
     * ACL/C情報を登録する
     *
     * @param [array] $data
     * @return 登録件数
     */
    public function insertAcLcInfo($data)
    {
        //クラスの生成
        $db = $this->lcConn;
        //クエリの生成
        $sql = "
            insert into t_aclcinfo(
                pono,
                polineno,
                poreviseno,
                postate,
                opendate,
                portplace,
                payfcd,
                payfnameomit,
                payfnameformal,
                productcd,
                productrevisecd,
                productname,
                productnamee,
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
                bankcd,
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
                lcstate,
                entryuser,
                entrydate,
                entrytime,
                updateuser,
                updatedate,
                updatetime,
                shipym
            )
            values ('" . $data["pono"]
            . "','" . $data["polineno"]
            . "','" . $data["poreviseno"]
            . "'," . ($data["postate"] == null ? "NULL" : "'" . $data["postate"] . "'")
            . "," . ($data["opendate"] == null ? "NULL" : "'" . $data["opendate"] . "'")
            . "," . ($data["unloadingareas"] == null ? "NULL" : "'" . $data["unloadingareas"] . "'")
            . "," . ($data["payfcd"] == null ? "NULL" : "'" . $data["payfcd"] . "'")
            . "," . ($data["payfnameomit"] == null ? "NULL" : "'" . $data["payfnameomit"] . "'")
            . "," . ($data["payfnameformal"] == null ? "NULL" : "'" . $data["payfnameformal"] . "'")
            . "," . ($data["productcd"] == null ? "NULL" : "'" . $data["productcd"] . "'")
            . "," . ($data["productrevisecd"] == null ? "NULL" : "'" . $data["productrevisecd"] . "'")
            . "," . ($data["productname"] == null ? "NULL" : "'" . $data["productname"] . "'")
            . "," . ($data["productnamee"] == null ? "NULL" : "'" . $data["productnamee"] . "'")
            . "," . $data["productnumber"]
            . "," . ($data["unitname"] == NULL ? "null" : "'" . $data["unitname"] . "'")
            . "," . $data["unitprice"]
            . "," . $data["moneyprice"]
            . "," . ($data["shipstartdate"] == null ? "NULL" : "'" . $data["shipstartdate"] . "'")
            . "," . ($data["shipenddate"] == null ? "NULL" : "'" . $data["shipenddate"] . "'")
            . "," . ($data["sumdate"] == null ? "NULL" : "'" . $data["sumdate"] . "'")
            . "," . ($data["poupdatedate"] == null ? "NULL" : "'" .$data["poupdatedate"] . "'")
            . "," . ($data["deliveryplace"] == null ? "NULL" : "'" . $data["deliveryplace"] . "'")
            . "," . ($data["currencyclass"] == null ? "NULL" : "'" . $data["currencyclass"] . "'")
            . "," . ($data["lcnote"] == null ? "NULL" : "'" . $data["lcnote"] . "'")
            . "," . ($data["shipterm"] == null ? "NULL" : "'" . $data["shipterm"] . "'")
            . "," . ($data["validterm"] == null ? "NULL" : "'" . $data["validterm"] . "'")
            . "," . ($data["bankcd"] == null ? "NULL" : "'" . $data["bankcd"] . "'")
            . "," . ($data["bankname"] == null ? "NULL" : "'" . $data["bankname"] . "'")
            . "," . ($data["bankreqdate"] == null ? "NULL" : "'" . $data["bankreqdate"] . "'")
            . "," . ($data["lcno"] == null ? "NULL" : "'" . $data["lcno"] . "'")
            . "," . ($data["lcamopen"] == null ? "NULL" : "'" . $data["lcamopen"] . "'")
            . "," . ($data["validmonth"] == null ? "NULL" : "'" . $data["validmonth"] . "'")
            . "," . ($data["usancesettlement"] == null ? "NULL" : $data["usancesettlement"])
            . "," . ($data["bldetail1date"] == null ? "NULL" : "'" . $data["bldetail1date"] . "'")
            . "," . ($data["bldetail1money"] == null ? "NULL" : $data["bldetail1money"])
            . "," . ($data["bldetail2date"] == null ? "NULL" : "'" . $data["bldetail2date"] . "'")
            . "," . ($data["bldetail2money"] == null ? "NULL" : $data["bldetail2money"])
            . "," . ($data["bldetail3date"] == null ? "NULL" : "'" . $data["bldetail3date"] . "'")
            . "," . ($data["bldetail3money"] == null ? "NULL" : $data["bldetail3money"])
            . "," . $data["lcstate"]
            . "," . ($data["entryuser"] == null ? "NULL" : "'" . $data["entryuser"] . "'")
            . "," . ($data["entrydate"] == null ? "NULL" : "'" . preg_replace ( "/\//", "", $data["entrydate"] ) . "'")
            . "," . ($data["entrytime"] == null ? "NULL" : "'" . $data["entrytime"] . "'")
            . "," . ($data["updateuser"] == null ? "NULL" : "'" . $data["updateuser"] . "'")
            . "," . ($data["updatedate"] == null ? "NULL" : "'" . preg_replace ( "/\//", "", $data["updatedate"] ) . "'")
            . "," . ($data["updatetime"] == null ? "NULL" : "'" . $data["updatetime"] . "'")
            . "," . ($data["shipym"] == null ? "NULL" : "'" . $data["shipym"] . "'")
            . ")";
        //バインドの設定
        $bind = array();
        //クエリ実行
        $result = $db->executeNonQuery($sql, $bind);

        if ($result < 0) {
            echo "ACL/C情報の登録失敗しました。\n";
            exit;
        }

        return $result;
    }

    /**
     * ACL/C状態を更新する
     *
     * @param [string] $pono
     * @param [string] $postate
     * @return void 更新件数
     */
    public function updateAcLcStateByLcState($pono)
    {
        //クラスの生成
        $db = $this->lcConn;
        //クエリの生成
        $sql = "
                update
                    t_aclcinfo
                set lcstate = 2
                where pono = $1
                and lcstate not in (2, 9, 10)
            ";
        //バインドの設定
        $bind = array($pono);

        //クエリ実行
        $result = $db->executeNonQuery($sql, $bind);

        if ($result < 0) {
            echo "LC状態によるACL/C情報の状態更新に失敗しました。\n";
            exit;
        }

        return $result;
    }

    /**
     *　基準日を取得する
     *
     * @return 基準日
     */
    public function getBaseDate()
    {
        //クラスの生成
        $db = $this->lcConn;
        //クエリの生成
        $sql = "
                    select
                        basedate
                    from
                        m_acbaseopendate
                    where
                        invalidflag = false
                ";
        //バインドの設定
        $bind = array();

        //クエリ実行
        $result = $db->select_single($sql, $bind);

        if (!$result) {
            echo "基準日の取得失敗しました。\n";
            exit;
        }

        return $result->basedate;
    }

    /**
     * ACL/C情報のオープン月を更新する
     *
     * @param [integer] $pono
     * @param [string] $opendate
     * @return 更新件数
     */
    public function updateAcLcOpendate($pono, $opendate)
    {
        //クラスの生成
        $db = $this->lcConn;
        //クエリの生成
        $sql = "
                update
                    t_aclcinfo
                set opendate = $1
                where pono = $2
            ";
        //バインドの設定
        $bind = array($opendate, $pono);
        //クエリ実行
        $result = $db->executeNonQuery($sql, $bind);

        if ($result < 0) {
            echo "ACL/C情報のオープン月の更新失敗しました。\n";
            exit;
        }

        return $result;

    }

    /**
     * pono,polinenoによりACL/C情報を取得する
     *
     * @param [integer] $pono
     * @param [integer] $polineno
     * @return ACL/C情報
     */
    public function getReviseAcLcInfo($pono, $polineno)
    {
        //クラスの生成
        $db = $this->lcConn;
        //クエリの生成
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
            order by poreviseno desc";
        //バインドの設定
        $bind = array($pono, $polineno);

        //クエリ実行
        $result = $db->select($sql, $bind);

        //検索結果返却
        return $result;

    }

    /**
     * ACL/C情報を更新する
     *
     * @param [array] $data
     * @return 更新件数
     */
    public function updateReviseAcLcInfo($data)
    {
        //クラスの生成
        $db = $this->lcConn;
        //クエリの生成
        $sql = "
                update
                    t_aclcinfo
                set opendate = " . ($data["opendate"] == null ? "NULL" : "'" . $data["opendate"] . "'")
            . ", bankcd = " . ($data["bankcd"] == null ? "NULL" : "'" . $data["bankcd"] . "'")
            . ", bankname = " . ($data["bankname"] == null ? "NULL" : "'" . $data["bankname"] . "'")
            . ", bankreqdate = " . ($data["bankreqdate"] == null ? "NULL" : $data["bankreqdate"])
            . ", lcno = " . ($data["lcno"] == null ? "NULL" : "'" . $data["lcno"] . "'")
            . ", lcamopen = " . ($data["lcamopen"] == null ? "NULL" : "'" . $data["lcamopen"] . "'")
            . ", validmonth =" . ($data["validmonth"] == null ? "NULL" : "'" . $data["validmonth"] . "'")
            . ", usancesettlement = " . ($data["usancesettlement"] == null ? "NULL" : $data["usancesettlement"])
            . ", bldetail1date = " . ($data["bldetail1date"] == null ? "NULL" : $data["bldetail1date"])
            . ", bldetail1money = " . ($data["bldetail1money"] == null ? "NULL" : $data["bldetail1money"])
            . ", bldetail2date = " . ($data["bldetail2date"] == null ? "NULL" : $data["bldetail2date"])
            . ", bldetail2money = " . ($data["bldetail2money"] == null ? "NULL" : $data["bldetail2money"])
            . ", bldetail3date = " . ($data["bldetail3date"] == null ? "NULL" : $data["bldetail3date"])
            . ", bldetail3money = " . ($data["bldetail3money"] == null ? "NULL" : $data["bldetail3money"])
            . ", lcstate = " . $data["lcstate"]
            . " where pono = " . "'" . $data["pono"] . "'"
            . " and polineno = " . "'" . $data["polineno"] . "'"
            . " and poreviseno = " . "'" . $data["poreviseno"] . "'";
        //バインドの設定
        $bind = array();
        //クエリ実行
        $result = $db->executeNonQuery($sql, $bind);

        if ($result < 0) {
            echo "ACL/C情報の更新失敗しました。\n";
            exit;
        }

        return $result;

    }

    /**
     * ACL/C情報の状態をリバイズに更新する
     *
     * @param [type] $pono
     * @param [type] $poreviseno
     * @return void
     */
    public function updateAcLcStateToRevise($pono, $poreviseno)
    {
        //クラスの生成
        $db = $this->lcConn;
        //クエリの生成
        $sql = "
                update
                    t_aclcinfo
                set lcstate = 1
                where pono = $1
                and poreviseno < $2
                and lcstate not in (1, 2, 5, 10)
            ";
        //バインドの設定
        $bind = array($pono, $poreviseno);
        //クエリ実行
        $result = $db->executeNonQuery($sql, $bind);

        if ($result < 0) {
            echo "ACL/C情報の状態(リバイズ)更新失敗しました。\n";
            exit;
        }

        return $result;
    }

    /**
     * 未承認ACL/C情報の状態を更新する
     *
     * @param [type] $pono
     * @param [type] $poreviseno
     * @return void
     */
    public function updateUnapprovedAcLcState($pono, $polineno, $poreviseno, $lcstate, $postate)
    {
        //クラスの生成
        $db = $this->lcConn;
        //クエリの生成
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
        //バインドの設定
        $bind = array($lcstate, $postate, $pono, $polineno, $poreviseno);
        //クエリ実行
        $result = $db->executeNonQuery($sql, $bind);

        return $result;
    }

    /**
     * 未承認のACL/C情報を取得する
     *
     * @return 未承認のACL/C情報リスト
     */
    public function getUnapprovedAcLcInfo()
    {
        //クラスの生成
        $db = $this->lcConn;
        //クエリの生成
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
                where postate = '未承認'
                and pono is not null
                order by pono, poreviseno desc
            ";
        //バインドの設定
        $bind = array();

        //クエリ実行
        $result = $db->select($sql, $bind);

        if (!$result) {
            echo "未承認のACL/C情報の取得失敗しました。\n";
            exit;
        }

        return $result;
    }

    /**
     * po番号,poリバイズ番号によりACL/C情報の状態を更新する
     *
     * @param [integer] $pono
     * @param [string] $opendate
     * @return 更新件数
     */
    public function updateAcLcState($pono, $poreviseno, $lcstate)
    {
        //クラスの生成
        $db = $this->lcConn;
        //クエリの生成
        $sql = "
                update
                    t_aclcinfo
                set lcstate = $1
                where pono = $2
                and poreviseno =$3
            ";
        //バインドの設定
        $bind = array($lcstate, $pono, $poreviseno);

        //クエリ実行
        $result = $db->executeNonQuery($sql, $bind);

        return $result;
    }
    /**
     * 銀行先情報を取得する
     *
     * @param [integer] $bankcd
     * @return 銀行先情報
     */
    public function getAcBankInfo($bankcd)
    {
        //クラスの生成
        $db = $this->lcConn;
        //クエリの生成
        $sql = "
                    select
                        *
                    from
                        m_acbankinfo
                    where
                        bankcd = $1
                ";
        //バインドの設定
        $bind = array($bankcd);

        //クエリ実行
        $result = $db->select_single($sql, $bind);

        if (!$result) {
            echo "銀行先情報の取得失敗しました。\n";
            exit;
        }

        return $result;
    }

    /**
     * 支払先情報を取得する
     *
     * @param [integer] $payfcd
     * @return 支払先情報
     */
    public function getAcPayfInfo($payfcd)
    {
        //クラスの生成
        $db = $this->lcConn;
        //クエリの生成
        $sql = "
                    select
                        payfOmitname,
                        payfformalname
                    from
                        m_acpayfinfo
                    where
                        payfcd = $1
                ";
        //バインドの設定
        $bind = array($payfcd);

        //クエリ実行
        $result = $db->select_single($sql, $bind);

        if (!$result) {
            echo "支払先情報(" . $payfcd . ")の取得失敗しました。\n";
            exit;
        }

        return $result;
    }

    /**
     * L/C取得日時を更新する
     *
     * @param [string] $lgno
     * @param [string] $lcgetdate
     * @return void
     */
    public function updateLcGetDate($lgno, $lcgetdate)
    {
        //クラスの生成
        $db = $this->lcConn;
        //クエリの生成
        $sql = "update
                        m_acloginstate
                    set
                        lcgetdate = $1
                    where
                        lgno =  $2
                "; //バインドの設定
        $bind = array($lcgetdate, $lgno);
        //クエリ実行
        $result = $db->executeNonQuery($sql, $bind);

        if ($result < 0) {
            echo "L/C取得日時の更新失敗しました。\n";
            exit;
        }

        return $result;
    }

    /**
     * 利用状態の初期化を行う
     *
     * @param [string] $lgno
     * @param [string] $lcgetdate
     * @return void
     */
    public function updateLgStateToInit($lgno)
    {
        //クラスの生成
        $db = $this->lcConn;
        //クエリの生成
        //クエリの生成
        $sql = "update
                        m_acloginstate
                    set
                        lgstate = null
                    where
                        lgno =  $1
                "; //バインドの設定
        $bind = array($lgno);

        //クエリ実行
        $result = $db->executeNonQuery($sql, $bind);

        if ($result < 0) {
            echo "利用状態の初期化に失敗しました。\n";
            exit;
        }

        return $result;
    }

    /**
     * ACL/C情報(全項目)を更新する
     *
     * @param [array] $data
     * @return 更新件数
     */
    public function updateAcLcInfo($data)
    {
        //クラスの生成
        $db = $this->lcConn;
        //クエリの生成
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
                    ,productrevisecd = $8
                    ,productname = $9
                    ,productnamee = $10
                    ,productnumber = $11
                    ,unitname = $12
                    ,unitprice = $13
                    ,moneyprice = $14
                    ,shipstartdate = $15
                    ,shipenddate = $16
                    ,sumdate = $17
                    ,poupdatedate = $18
                    ,deliveryplace = $19
                    ,currencyclass = $20
                    ,lcnote = $21
                    ,shipterm = $22
                    ,validterm = $23
                    ,bankcd = $24
                    ,bankname = $25
                    ,bankreqdate = $26
                    ,lcno = $27
                    ,lcamopen = $28
                    ,validmonth = $29
                    ,usancesettlement = $30
                    ,bldetail1date = $31
                    ,bldetail1money = $32
                    ,bldetail2date = $33
                    ,bldetail2money = $34
                    ,bldetail3date = $35
                    ,bldetail3money = $36
                    ,lcstate = $37
                    ,updateuser = $38
                    ,updatedate = $39
                    ,updatetime = $40
                    ,invalidflag = $41
                    ,shipym = $42
                where pono = $43
                and polineno = $44
                and poreviseno = $45
            ";
        //バインドの設定
        $bind = array($data["postate"]
            , $data["opendate"]
            , $data["portplace"]
            , $data["payfcd"]
            , $data["payfnameomit"]
            , $data["payfnameformal"]
            , $data["productcd"]
            , $data["productrevisecd"]
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
        //クエリ実行
        $result = $db->executeNonQuery($sql, $bind);

        if ($result < 0) {
            echo "ACL/C情報(全項目)の更新に失敗しました。\n";
            exit;
        }

        return $result;

    }

    // ---------------------------------------------------------------
    /**
     *    ログイン状況のログアウト処理
     *    @param  object  $param           DBオブジェクト
     *    @access public
     */
    // ---------------------------------------------------------------
    public function updateLgoutym($param)
    {
        //クラスの生成
        $db = $this->lcConn;
        //ログイン状況をログアウトに更新する
        $sql = "update
					m_acloginstate
				set
					lgoutymd = $1,
					lgouttime = $2,
					lgstate = null
				where
					lginymd is not null and
					lgintime is not null and
					lgoutymd is null and
					lgouttime is null and
					lgusrname in(select lgusrname from m_acloginstate where lgno = $3)
		    ";
        //バインドの設定
        $bind = array(
            fncGetDateString(),
            fncGetTimeString(), 
            $param["lgno"]);

        //クエリ実行
        $result = $db->executeNonQuery($sql, $bind);

        //検索結果返却
        return $result;
    }

    /**
     * L/Cインポート日時を更新する
     *
     * @param [string] $lgno
     * @param [string] $lcimpdate
     * @return void
     */
    public function updateLcImpDate($lgno, $lcimpdate)
    {
        //クラスの生成
        $db = $this->lcConn;
        //クエリの生成
        $sql = "update
                        m_acloginstate
                    set
                        lcimpdate = $1
                    where
                        lgno =  $2
                "; //バインドの設定
        $bind = array($lcimpdate, $lgno);

        //クエリ実行
        $result = $db->executeNonQuery($sql, $bind);

        if ($result < 0) {
            echo "L/Cインポート日時の更新nに失敗しました。\n";
            exit;
        }

        return $result;
    }

    /**
     * L/Cエクスポート日時を更新する
     *
     * @param [string] $lgno
     * @param [string] $lcexpdate
     * @return void
     */
    public function updateLcExpDate($lgno, $lcimpdate)
    {
        //クラスの生成
        $db = $this->lcConn;
        //クエリの生成
        $sql = "update
                        m_acloginstate
                    set
                        lcexpdate = $1
                    where
                        lgno =  $2
                "; //バインドの設定
        $bind = array($lcexpdate, $lgno);

        //クエリ実行
        $result = $db->executeNonQuery($sql, $bind);

        if ($result < 0) {
            echo "L/Cエクスポート日時の更新に失敗しました。\n";
            exit;
        }

        return $result;
    }

    /**
     * 送付元マスタ情報を取得する
     *
     * @return void
     */
    public function getSendInfo()
    {
        //クラスの生成
        $db = $this->lcConn;
        //クエリの生成
        $sql = "
				select
					*
				from
                    m_acsendinfo
                where
                    invalidflag = false
            ";
        //バインドの設定
        $bind = array();

        //クエリ実行
        $result = $db->select($sql, $bind);

        if (count($result) > 0) {
            return $result;
        } else {
            return false;
        }
    }
}
