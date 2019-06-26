<?php

class lcConnect
{
    /**
     * �ǡ����١�������³����ؿ�
     *
     * @param [string] $query queryʸ
     * @param [array] $params �ѥ�᡼��
     * @return void
     */
    public function connect_pg()
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

            //�Х���������ʸ�����󥳡����ѹ�
            // mb_convert_variables('EUC-JP', 'UTF-8', $params);
        } catch (Exception $e) {
            die($e->getMessage());
        }
        return $link;
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
        $link = $this->connect_pg();

        if (count($params) > 0) {
            $result = pg_query_params($link, $query, $params);
        } else {
            $result = pg_query($link, $query);
        }

        if (!$result) {
            exit("Failed : $query\n");
        }

        $result = pg_fetch_all($result);

        if ($result == false) {
            $result = array();
        }

        //����
        pg_close($link);
        
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
        $link = $this->connect_pg();

        if (count($params) > 0) {
            $result = pg_query_params($link, $query, $params);
        } else {
            $result = pg_query($link, $query);
        }

        if (!$result) {
            exit("Failed : $query\n");
        }

        $result = pg_fetch_Object($result);

        //����
        pg_close($link);
        
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
        
        $link = $this->connect_pg();

        if (count($params) > 0) {
            $result = pg_query_params($link, $query, $params);
        } else {
            $result = pg_query($link, $query);
        }

        if (!$result) {
            exit("Failed : $query\n");
        }

        $result = pg_affected_rows($result);

        //����
        pg_close($link);
        
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
        
        $link = $this->connect_pg();

        if (count($params) > 0) {
            $result = pg_query_params($link, $query, $params);
        } else {
            $result = pg_query($link, $query);
        }

        if (!$result) {
            exit("Failed : $query\n");
        }

        $result = pg_affected_rows($result);

        //����
        pg_close($link);
        
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
        
        $link = $this->connect_pg();

        if (count($params) > 0) {
            $result = pg_query_params($link, $query, $params);
        } else {
            $result = pg_query($link, $query);
        }

        if (!$result) {
            exit("Failed : $query\n");
        }

        $result = pg_affected_rows($result);

        //����
        pg_close($link);
        
        return $result;
    }
}
