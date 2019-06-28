<?php

class lcConnect
{
    /**
     * データベースに接続する関数
     *
     * @param [string] $query query文
     * @param [array] $params パラメータ
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
                die('接続失敗です。' . pg_last_error());
            }

            pg_set_client_encoding($link, "EUC_JP");

            //バインド配列の文字エンコード変更
            // mb_convert_variables('EUC-JP', 'UTF-8', $params);
        } catch (Exception $e) {
            die($e->getMessage());
        }
        return $link;
    }

    /**
     * SELECT文の時に使用する関数(レスポンスが複数件想定される場合)
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

        //切断
        pg_close($link);
        
        return $result;
    }

    /**
     * SELECT文の時に使用する関数(レスポンスが単一の場合)
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

        //切断
        pg_close($link);
        
        return $result;
    }

    /**
     * INSERT文の時に使用する関数
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

        //切断
        pg_close($link);
        
        return $result;
    }

    /**
     * UPDATE文の時に使用する関数
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

        //切断
        pg_close($link);
        
        return $result;
    }

    /**
     * DELETE文の時に使用する関数
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

        //切断
        pg_close($link);
        
        return $result;
    }
}
