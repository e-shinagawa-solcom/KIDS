<?php

class lcConnect
{
    /**
     *    接続ID
     *    @var string
     */
    public $ConnectID;
    /**
     *    トランザクションフラグ(TRUE:SQLエラー時ROLLBACKを実行)
     *    @var boolean
     */
    public $Transaction;

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
        $this->ConnectID = false;

        // トランザクションフラグの初期化
        $this->Transaction = false;

    }

    /**
     *    接続中かどうかの取得
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
     *    DBから切断
     *    @return boolean TRUE,FALSE
     *    @access public
     */
    public function close()
    {
        // 接続チェック
        if (!$this->isOpen()) {
            return false;
        } else {
            pg_close($this->ConnectID);
            $this->ConnectID = false;
            return true;
        }
    }

    /**
     * データベースに接続する関数
     *
     * @param [string] $query query文
     * @param [array] $params パラメータ
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
                die('接続失敗です。' . pg_last_error());
            }

            pg_set_client_encoding($link, "UTF-8");

            $this->ConnectID = $link;

        } catch (Exception $e) {
            die($e->getMessage());
        }

        return true;
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
     * SELECT文の時に使用する関数(レスポンスが単一の場合)
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
     * SELECT文以外の時に使用する関数
     *
     * @param [string] $query
     * @param [array] $params
     * @return void
     */
    public function executeNonQuery($query, $params)
    {
        if (count($params) > 0) {
            $result = pg_query_params($this->ConnectID, $query, $params);
        } else {
            $result = pg_query($this->ConnectID, $query);
        }

        if (!$result) {
           return -1;
        }

        $result = pg_affected_rows($result);

        return $result;
    }

    /**
     *    トランザクション開始(BEGIN)
     *    @return boolean TRUE,FALSE
     *    @access public
     */
    public function transactionBegin()
    {
        // 接続チェック
        if (!$this->isOpen()) {
            return false;
        }

        // トランザクションスタート
        if (!$this->execute("BEGIN")) {
            return false;
        }
        $this->Trasaction = true;

        return true;
    }

    /**
     *    トランザクション完了(COMMIT)
     *    @return boolean TRUE,FALSE
     *    @access public
     */
    public function transactionCommit()
    {
        // 接続チェック
        if (!$this->isOpen()) {
            return false;
        }

        // トランザクションコミット
        if (!$this->execute("COMMIT")) {
            return false;
        }
        $this->Trasaction = false;

        return true;
    }

    /**
     *    SQLの実行
     *    @param  string  $strQuery  実行するSQL文
     *    @return long    結果バッファID
     *            boolean FALSE
     *    @access public
     */
    public function execute($strQuery)
    {
        // 接続チェック
        if (!$this->isOpen()) {
            return false;
        }

        $lngResultID = false;

        // クエリ実行
        if (!$lngResultID = pg_query($this->ConnectID, $strQuery)) {
            // トランザクションフラグの確認
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
