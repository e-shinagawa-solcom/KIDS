<?php

class lcConnect
{
    /**
     *    ��³ID
     *    @var string
     */
    public $ConnectID;
    /**
     *    �ȥ�󥶥������ե饰(TRUE:SQL���顼��ROLLBACK��¹�)
     *    @var boolean
     */
    public $Transaction;

    /**
     *    ���󥹥ȥ饯��
     *    ���饹��ν������Ԥ�
     *
     *    @return void
     *    @access public
     */
    public function __construct()
    {
        // ��³ID�ν����
        $this->ConnectID = false;

        // �ȥ�󥶥������ե饰�ν����
        $this->Transaction = false;

    }

    /**
     *    ��³�椫�ɤ����μ���
     *    @return boolean TRUE,FALSE
     *    @access public
     */
    // ---------------------------------------------------------------
    public function isOpen()
    {
        if ($this->ConnectID == false) {
            return false;
        } else {
            return true;
        }
    }

    /**
     *    DB��������
     *    @return boolean TRUE,FALSE
     *    @access public
     */
    public function close()
    {
        // ��³�����å�
        if (!$this->isOpen()) {
            return false;
        } else {
            pg_close($this->ConnectID);
            $this->ConnectID = false;
            return true;
        }
    }

    /**
     * �ǡ����١�������³����ؿ�
     *
     * @param [string] $query queryʸ
     * @param [array] $params �ѥ�᡼��
     * @return void
     */
    public function open()
    {
        try {
            $conn = "host=" . LC_POSTGRESQL_HOSTNAME
                . " dbname=" . LC_DB_NAME
                . " user=" . LC_DB_LOGIN_USERNAME
                . " password=" . LC_DB_LOGIN_PASSWORD;
            $link = pg_connect($conn);
            if (!$link) {
                die('��³���ԤǤ���' . pg_last_error());
            }

            pg_set_client_encoding($link, "EUC_JP");

            $this->ConnectID = $link;

            //�Х���������ʸ�����󥳡����ѹ�
            // mb_convert_variables('EUC-JP', 'UTF-8', $params);
        } catch (Exception $e) {
            die($e->getMessage());
        }

        return true;
    }

    /**
     * SELECTʸ�λ��˻��Ѥ���ؿ�(�쥹�ݥ󥹤�ʣ�������ꤵ�����)
     *
     * @param [string] $query
     * @param [array] $params
     * @return void
     */
    public function select($query, $params)
    {
        if (count($params) > 0) {
            $result = pg_query_params($this->ConnectID, $query, $params);
        } else {
            $result = pg_query($this->ConnectID, $query);
        }

        if (!$result) {
            exit("Failed : $query\n");
        }

        $result = pg_fetch_all($result);

        if ($result == false) {
            $result = array();
        }

        return $result;
    }

    /**
     * SELECTʸ�λ��˻��Ѥ���ؿ�(�쥹�ݥ󥹤�ñ��ξ��)
     *
     * @param [string] $query
     * @param [array] $params
     * @return void
     */
    public function select_single($query, $params)
    {
        if (count($params) > 0) {
            $result = pg_query_params($this->ConnectID, $query, $params);
        } else {
            $result = pg_query($this->ConnectID, $query);
        }

        if (!$result) {
            exit("Failed : $query\n");
        }

        $result = pg_fetch_Object($result);

        return $result;
    }

    /**
     * INSERTʸ�λ��˻��Ѥ���ؿ�
     *
     * @param [string] $query
     * @param [array] $params
     * @return void
     */
    public function insert($query, $params)
    {
        if (count($params) > 0) {
            $result = pg_query_params($this->ConnectID, $query, $params);
        } else {
            $result = pg_query($this->ConnectID, $query);
        }

        if (!$result) {
            exit("Failed : $query\n");
        }

        $result = pg_affected_rows($result);

        return $result;
    }

    /**
     * UPDATEʸ�λ��˻��Ѥ���ؿ�
     *
     * @param [string] $query
     * @param [array] $params
     * @return void
     */
    public function update($query, $params)
    {
        if (count($params) > 0) {
            $result = pg_query_params($this->ConnectID, $query, $params);
        } else {
            $result = pg_query($this->ConnectID, $query);
        }

        if (!$result) {
            exit("Failed : $query\n");
        }

        $result = pg_affected_rows($result);

        return $result;
    }

    /**
     * DELETEʸ�λ��˻��Ѥ���ؿ�
     *
     * @param [string] $query
     * @param [array] $params
     * @return void
     */
    public function delete($query, $params)
    {
        if (count($params) > 0) {
            $result = pg_query_params($this->ConnectID, $query, $params);
        } else {
            $result = pg_query($this->ConnectID, $query);
        }

        if (!$result) {
            exit("Failed : $query\n");
        }

        $result = pg_affected_rows($result);

        return $result;
    }

    /**
     *    �ȥ�󥶥�����󳫻�(BEGIN)
     *    @return boolean TRUE,FALSE
     *    @access public
     */
    public function transactionBegin()
    {
        // ��³�����å�
        if (!$this->isOpen()) {
            return false;
        }

        // �ȥ�󥶥�����󥹥�����
        if (!$this->execute("BEGIN")) {
            return false;
        }
        $this->Trasaction = true;

        return true;
    }

    /**
     *    �ȥ�󥶥������λ(COMMIT)
     *    @return boolean TRUE,FALSE
     *    @access public
     */
    public function transactionCommit()
    {
        // ��³�����å�
        if (!$this->isOpen()) {
            return false;
        }

        // �ȥ�󥶥�����󥳥ߥå�
        if (!$this->execute("COMMIT")) {
            return false;
        }
        $this->Trasaction = false;

        return true;
    }

    /**
     *    SQL�μ¹�
     *    @param  string  $strQuery  �¹Ԥ���SQLʸ
     *    @return long    ��̥Хåե�ID
     *            boolean FALSE
     *    @access public
     */
    public function execute($strQuery)
    {
        // ��³�����å�
        if (!$this->isOpen()) {
            return false;
        }

        $lngResultID = false;

        // ������¹�
        if (!$lngResultID = pg_query($this->ConnectID, $strQuery)) {
            // �ȥ�󥶥������ե饰�γ�ǧ
            if ($this->Trasaction) {
                if (!pg_query($this->ConnectID, "ROLLBACK")) {
                    return false;
                }
                $this->Trasaction = false;
            }

            return false;
        }
        return $lngResultID;
    }
}
