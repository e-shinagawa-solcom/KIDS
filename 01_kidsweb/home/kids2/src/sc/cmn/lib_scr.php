<?php
// ----------------------------------------------------------------------------
/**
 *       売上（納品書）登録関数群
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
 *         ・売上（納品書）登録関連の関数
 *
 *       更新履歴
 *
 */
// ----------------------------------------------------------------------------

// 修正対象データに対してロックしている人を確認する
// ロックしている人がいないなら空文字列を返す
function fncGetExclusiveLockUser($lngFunctionCode, $strSlipCode, $objAuth, $objDB)
{
    $lockUserName = "";
    $v_lngusercode = $objAuth->UserCode; //1:ユーザコード
    $v_stripaddress = $objAuth->AccessIP; //2:端末IPアドレス

    $aryQuery = array();
    $aryQuery[] = "SELECT ";
    $aryQuery[] = "  tec.lngfunctioncode, ";
    $aryQuery[] = "  tec.lngusercode, ";
    $aryQuery[] = "  tec.stripaddress, ";
    $aryQuery[] = "  mu.struserdisplayname ";
    $aryQuery[] = " FROM t_exclusivecontrol tec ";
    $aryQuery[] = "  INNER JOIN m_user mu ON tec.lngusercode = mu.lngusercode ";
    $aryQuery[] = " WHERE tec.lngfunctioncode = " . $lngFunctionCode;
    $aryQuery[] = "   AND tec.strexclusivekey1 = " . withQuote($strSlipCode);

    $strQuery = "";
    $strQuery .= implode("\n", $aryQuery);

    list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);
    if (0 < $lngResultNum) {
        for ($i = 0; $i < $lngResultNum; $i++) {
            $aryResult[] = $objDB->fetchArray($lngResultID, $i);
        }
        if ($v_lngusercode != $aryResult[0]["lngusercode"] || $v_stripaddress != $aryResult[0]["stripaddress"]) {
            // ロックしているユーザーの表示用ユーザー名を設定
            $lockUserName = $aryResult[0]["struserdisplayname"];
            $isLock = 1; // 別ユーザーロック中
        } else {
            $lockUserName = "";
            $isLock = 2; // 同ユーザーロック中
        }
    } else {
        // だれもロックしていない
        $lockUserName = "";
        $isLock = 0; // 未ロック
    }
    $objDB->freeResult($lngResultID);

    $aryResult["lockUserName"] = $lockUserName;
    $aryResult["isLock"] = $isLock;
    return $aryResult;
}

// 対象の機能IDに対して排他ロックを取る
function fncTakeExclusiveLock($lngFunctionCode, $strSlipCode, $objAuth, $objDB)
{
    $locked = false;

    // 排他キー1のみ対応（他はデフォルト値）
    $v_lngfunctioncode = $lngFunctionCode; //1:機能コード
    $v_strexclusivekey1 = withQuote($strSlipCode); //2:排他キー1
    $v_lngusercode = $objAuth->UserCode; //5:ユーザコード
    $v_stripaddress = withQuote($objAuth->AccessIP); //6:端末IPアドレス
    $v_dtminsertdate = "now()"; //7:登録日

    $aryInsert[] = "INSERT  ";
    $aryInsert[] = " INTO t_exclusivecontrol(  ";
    $aryInsert[] = "  lngfunctioncode "; //1:機能コード
    $aryInsert[] = "  , strexclusivekey1 "; //2:排他キー1
    $aryInsert[] = "  , lngusercode "; //5:ユーザコード
    $aryInsert[] = "  , stripaddress "; //6:端末IPアドレス
    $aryInsert[] = "  , dtminsertdate "; //7:登録日
    $aryInsert[] = ")  ";
    $aryInsert[] = " VALUES (  ";
    $aryInsert[] = "  " . $v_lngfunctioncode; //1:機能コード
    $aryInsert[] = " ," . $v_strexclusivekey1; //2:排他キー1
    $aryInsert[] = " ," . $v_lngusercode; //5:ユーザコード
    $aryInsert[] = " ," . $v_stripaddress; //6:端末IPアドレス
    $aryInsert[] = " ," . $v_dtminsertdate; //7:登録日
    $aryInsert[] = ") ";
    $strQuery = "";
    $strQuery .= implode("\n", $aryInsert);

    // トランザクション開始
    $objDB->transactionBegin();

    // 登録実行
    if (!$lngResultID = $objDB->execute($strQuery)) {
        // 失敗
        $locked = false;
    } else {
        $objDB->freeResult($lngResultID);
        // コミット
        $objDB->transactionCommit();
        // 成功
        $locked = true;
    }

    return $locked;
}

// 対象の機能IDに対して排他ロックを解除する
function fncReleaseExclusiveLock($lngFunctionCode, $strSlipCode, $objDB)
{
    $unlocked = false;

    $aryDelete[] = " ";
    $aryDelete[] = "DELETE  ";
    $aryDelete[] = " FROM ";
    $aryDelete[] = "  t_exclusivecontrol  ";
    $aryDelete[] = " WHERE ";
    $aryDelete[] = "  lngfunctioncode = " . $lngFunctionCode;
    $aryDelete[] = "  and strexclusivekey1 = " . withQuote($strSlipCode);
    $strQuery = "";
    $strQuery .= implode("\n", $aryDelete);

    // トランザクション開始
    $objDB->transactionBegin();

    // 登録実行
    if (!$lngResultID = $objDB->execute($strQuery)) {
        // 失敗
        $unlocked = false;
    } else {
        $objDB->freeResult($lngResultID);

        // コミット
        $objDB->transactionCommit();

        // 成功
        $unlocked = true;
    }

    return $unlocked;
}

// 消費税率プルダウンの選択項目作成
function fncGetTaxRatePullDown($dtmDeliveryDate, $curDefaultTax, $objDB)
{
    // DBからデータ取得
    $strQuery = "SELECT lngtaxcode, curtax * 100 as curtax "
        . " FROM m_tax "
        . " WHERE dtmapplystartdate <= '$dtmDeliveryDate' "
        . "   AND dtmapplyenddate >= '$dtmDeliveryDate' "
        . " ORDER BY lngpriority ";
    list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);
    if ($lngResultNum) {
        for ($i = 0; $i < $lngResultNum; $i++) {
            $aryResult[] = $objDB->fetchArray($lngResultID, $i);
        }
    } else {
        fncOutputError(9501, DEF_FATAL, "消費税情報の取得に失敗", true, "", $objDB);
    }
    $objDB->freeResult($lngResultID);

    // 選択項目作成
    $strHtml = "";
    for ($i = 0; $i < count($aryResult); $i++) {
        $optionValue = $aryResult[$i]["lngtaxcode"];
        $displayText = $aryResult[$i]["curtax"] * 1; // 小数点末尾の0をカット

        // デフォルト値が設定されている場合、その値を選択
        if ($curDefaultTax == $displayText) {
            $strHtml .= "<OPTION VALUE=\"$optionValue\" SELECTED>$displayText</OPTION>\n";
        } else {
            $strHtml .= "<OPTION VALUE=\"$optionValue\">$displayText</OPTION>\n";
        }
    }

    return $strHtml;
}

// 納品伝票番号に紐づくヘッダ項目取得
function fncGetHeaderBySlipNo($lngSlipNo, $objDB)
{

    $aryQuery = array();
    $aryQuery[] = "SELECT ";
    $aryQuery[] = "  s.lngslipno, ";
    $aryQuery[] = "  s.lngrevisionno, ";
    $aryQuery[] = "  u_ins.lngusercode as lngdrafterusercode,  "; //起票者（ユーザーコード）
    $aryQuery[] = "  u_ins.struserdisplaycode as strdrafteruserdisplaycode,  "; //起票者（表示用ユーザーコード）
    $aryQuery[] = "  u_ins.struserdisplayname as strdrafteruserdisplayname, "; //起票者（表示用ユーザー名）
    $aryQuery[] = "  c_cust.strcompanydisplaycode as strcompanydisplaycode, "; //顧客（表示用会社コード）
    $aryQuery[] = "  c_cust.strcompanydisplayname as strcompanydisplayname, "; //顧客（表示用会社名）
    $aryQuery[] = "  s.strcustomerusername, "; //顧客担当者
    $aryQuery[] = "  TO_CHAR(s.dtmdeliverydate, 'YYYY/MM/DD') as dtmdeliverydate, "; //納品日
    $aryQuery[] = "  TO_CHAR(s.dtmpaymentlimit, 'YYYY/MM/DD') as dtmpaymentlimit, "; //支払期限
    $aryQuery[] = "  s.lngpaymentmethodcode, "; //支払方法
    $aryQuery[] = "  c_deli.strcompanydisplaycode as strdeliveryplacecompanydisplaycode, "; //納品先（表示用会社コード）
    $aryQuery[] = "  s.strdeliveryplacename, "; //納品先（表示用会社名）
    $aryQuery[] = "  s.strdeliveryplaceusername, "; //納品先担当者
    $aryQuery[] = "  s.strnote, "; //備考
    $aryQuery[] = "  s.lngtaxclasscode, "; //消費税区分（コード値）
    $aryQuery[] = "  s.strtaxclassname, "; //消費税区分（名称）
    $aryQuery[] = "  s.curtax, "; //消費税率（数値）
    $aryQuery[] = "  Null as lngtaxcode, "; //消費税率（コード値）
    $aryQuery[] = "  Null as strtaxamount, "; //消費税額
    $aryQuery[] = "  s.curtotalprice "; //合計金額
    $aryQuery[] = " FROM m_slip s ";
    $aryQuery[] = "   LEFT JOIN m_user u_ins ON s.lnginsertusercode = u_ins.lngusercode ";
    $aryQuery[] = "   LEFT JOIN m_company c_cust ON s.lngcustomercode = c_cust.lngcompanycode ";
    $aryQuery[] = "   LEFT JOIN m_company c_deli ON s.lngdeliveryplacecode = c_deli.lngcompanycode ";
    $aryQuery[] = " WHERE ";
    $aryQuery[] = "  s.lngslipno = " . $lngSlipNo;
    $aryQuery[] = " and s.lngrevisionno = (SELECT MAX(lngrevisionno) FROM m_slip WHERE lngslipno = " . $lngSlipNo.")";
    
    // $aryQuery[] = "  AND s.lngrevisionNo = " . $lngRevisionNo;

    $strQuery = "";
    $strQuery .= implode("\n", $aryQuery);

    list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);
    if ($lngResultNum) {
        for ($i = 0; $i < $lngResultNum; $i++) {
            $aryResult[] = $objDB->fetchArray($lngResultID, $i);
        }
    } else {
        fncOutputError(9501, DEF_FATAL, "納品書データの取得に失敗", true, "", $objDB);
    }
    $objDB->freeResult($lngResultID);

    return $aryResult[0];
}

// 納品伝票番号に紐づく受注明細情報を取得する
function fncGetDetailBySlipNo($lngSlipNo, $lngRevisionNo, $objDB)
{
    // 明細部のキーを取得する
    $aryDetailKey = fncGetDetailKeyBySlipNo($lngSlipNo, $lngRevisionNo, $objDB);

    // 明細部のキーに紐づく受注明細情報を取得する
    $aryDetail = array();
    for ($i = 0; $i < count($aryDetailKey); $i++) {

        $aryCondition = array();
        $aryCondition["lngReceiveNo"] = $aryDetailKey[$i]["lngreceiveno"];
        $aryCondition["lngReceiveDetailNo"] = $aryDetailKey[$i]["lngreceivedetailno"];
        $aryCondition["lngReceiveRevisionNo"] = $aryDetailKey[$i]["lngreceiverevisionno"];

        // キーに紐づく明細を1件ずつ取得して全体の配列にマージ
        $arySubDetail = fncGetReceiveDetail($aryCondition, $objDB);
        $aryDetail = array_merge($aryDetail, $arySubDetail);
    }

    return $aryDetail;
}

// 納品伝票番号に紐づく明細のキー項目を取得
function fncGetDetailKeyBySlipNo($lngSlipNo, $lngRevisionNo, $objDB)
{
    $aryQuery = array();
    $aryQuery[] = "SELECT ";
    $aryQuery[] = "  sd.lngslipno, ";
    $aryQuery[] = "  sd.lngreceiveno, ";
    $aryQuery[] = "  sd.lngreceivedetailno, ";
    $aryQuery[] = "  sd.lngreceiverevisionno ";
    $aryQuery[] = " FROM t_slipdetail sd";
    $aryQuery[] = " WHERE ";
    $aryQuery[] = "  sd.lngslipno = " . $lngSlipNo;
    $aryQuery[] = "  AND sd.lngRevisionNo = " . $lngRevisionNo;

    $strQuery = "";
    $strQuery .= implode("\n", $aryQuery);

    list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);
    if ($lngResultNum) {
        for ($i = 0; $i < $lngResultNum; $i++) {
            $aryResult[] = $objDB->fetchArray($lngResultID, $i);
        }
    } else {
        fncOutputError(9501, DEF_FATAL, "明細のキー項目の取得に失敗", true, "", $objDB);
    }
    $objDB->freeResult($lngResultID);

    return $aryResult;
}

// 明細検索
function fncGetReceiveDetail($aryCondition, $objDB)
{
    // -------------------
    //  選択項目
    // -------------------
    $arySelect[] = " SELECT";
    $arySelect[] = "  rd.lngsortkey,"; //No.
    $arySelect[] = "  r.strcustomerreceivecode,"; //顧客受注番号
    $arySelect[] = "  r.strreceivecode,"; //受注番号
    $arySelect[] = "  p.strgoodscode,"; //顧客品番
    $arySelect[] = "  rd.strproductcode,"; //製品コード
    $arySelect[] = "  rd.strrevisecode,"; //リバイズコード（再販コード）
    $arySelect[] = "  p.strproductname,"; //製品名
    $arySelect[] = "  p.strproductenglishname,"; //製品名（英語）
    $arySelect[] = "  g.strgroupdisplayname as strsalesdeptname,"; //営業部署（名称）
    $arySelect[] = "  rd.lngsalesclasscode,"; //売上区分コード
    $arySelect[] = "  sc.strsalesclassname,"; //売上区分（名称）
    $arySelect[] = "  TO_CHAR(rd.dtmdeliverydate, 'YYYY/MM/DD') as dtmdeliverydate,"; //納期
    $arySelect[] = "  rd.lngunitquantity,"; //入数
    $arySelect[] = "  rd.curproductprice,"; //単価
    $arySelect[] = "  rd.lngproductunitcode,"; //単位コード
    $arySelect[] = "  pu.strproductunitname,"; //単位（名称）
    $arySelect[] = "  rd.lngproductquantity,"; //数量
    $arySelect[] = "  rd.cursubtotalprice,"; //税抜金額
    $arySelect[] = "  rd.lngreceiveno,"; //受注番号（明細登録用）
    $arySelect[] = "  rd.lngreceivedetailno,"; //受注明細番号（明細登録用）
    $arySelect[] = "  rd.lngrevisionno as lngreceiverevisionno,"; //リビジョン番号（明細登録用）
    $arySelect[] = "  rd.strnote,"; //備考（明細登録用）
    $arySelect[] = "  r.lngmonetaryunitcode,"; //通貨単位コード（明細登録用）
    $arySelect[] = "  r.lngmonetaryratecode,"; //通貨レートコード（明細登録用）
    $arySelect[] = "  mu.strmonetaryunitsign,"; //通貨単位記号（明細登録用）
    $arySelect[] = "  sc.bytdetailunifiedflg"; //明細統一フラグ（明細登録用）
    $arySelect[] = " FROM";
    $arySelect[] = "  t_receivedetail rd ";
    $arySelect[] = "  INNER JOIN ( ";
    $arySelect[] = "    select";
    $arySelect[] = "      r1.* ";
    $arySelect[] = "    from";
    $arySelect[] = "      m_receive r1 ";
    $arySelect[] = "      inner join ( ";
    $arySelect[] = "        select";
    $arySelect[] = "          max(lngrevisionno) lngrevisionno";
    $arySelect[] = "          , strreceivecode ";
    $arySelect[] = "        from";
    $arySelect[] = "          m_receive r2 ";
    $arySelect[] = "        where";
    $arySelect[] = "          bytinvalidflag = false ";
    $arySelect[] = "          and not exists ( ";
    $arySelect[] = "            select";
    $arySelect[] = "              strreceivecode ";
    $arySelect[] = "            from";
    $arySelect[] = "              m_receive r3 ";
    $arySelect[] = "            where";
    $arySelect[] = "              lngrevisionno < 0 ";
    $arySelect[] = "              and r3.strreceivecode = r2.strreceivecode";
    $arySelect[] = "          ) ";
    $arySelect[] = "        group by";
    $arySelect[] = "          strreceivecode";
    $arySelect[] = "      ) r2 ";
    $arySelect[] = "        ON r1.lngrevisionno = r2.lngrevisionno ";
    $arySelect[] = "        and r1.strreceivecode = r2.strreceivecode ";
    $arySelect[] = "  ) r ";
    $arySelect[] = "    on rd.lngreceiveno = r.lngreceiveno ";
    $arySelect[] = "    AND rd.lngrevisionno = r.lngrevisionno ";
    $arySelect[] = "  LEFT JOIN m_company c ";
    $arySelect[] = "    ON r.lngcustomercompanycode = c.lngcompanycode ";
    $arySelect[] = "  LEFT JOIN ( ";
    $arySelect[] = "    select";
    $arySelect[] = "      p1.* ";
    $arySelect[] = "    from";
    $arySelect[] = "      m_product p1 ";
    $arySelect[] = "      inner join ( ";
    $arySelect[] = "        select";
    $arySelect[] = "          max(lngrevisionno) lngrevisionno";
    $arySelect[] = "          , strproductcode, strrevisecode ";
    $arySelect[] = "        from";
    $arySelect[] = "          m_product p2 ";
    $arySelect[] = "        where";
    $arySelect[] = "          lngrevisionno >= 0 ";
    $arySelect[] = "          and bytinvalidflag = false ";
    $arySelect[] = "          and not exists ( ";
    $arySelect[] = "            select";
    $arySelect[] = "              strproductcode ";
    $arySelect[] = "            from";
    $arySelect[] = "              m_product p3 ";
    $arySelect[] = "            where";
    $arySelect[] = "              lngrevisionno < 0 ";
    $arySelect[] = "              and p3.strproductcode = p2.strproductcode";
    $arySelect[] = "          ) ";
    $arySelect[] = "        group by";
    $arySelect[] = "          strproductcode, strrevisecode";
    $arySelect[] = "      ) p2 ";
    $arySelect[] = "        on p1.strproductcode = p2.strproductcode ";
    $arySelect[] = "        and p1.lngrevisionno = p2.lngrevisionno";
    $arySelect[] = "        and p1.strrevisecode = p2.strrevisecode";
    $arySelect[] = "  ) p ";
    $arySelect[] = "    ON rd.strproductcode = p.strproductcode ";
    $arySelect[] = "    and rd.strrevisecode = p.strrevisecode ";
    $arySelect[] = "  LEFT JOIN m_salesclass sc ";
    $arySelect[] = "    ON rd.lngsalesclasscode = sc.lngsalesclasscode ";
    $arySelect[] = "  LEFT JOIN m_productunit pu ";
    $arySelect[] = "    ON rd.lngproductunitcode = pu.lngproductunitcode ";
    $arySelect[] = "  LEFT JOIN m_group g ";
    $arySelect[] = "    ON p.lnginchargegroupcode = g.lnggroupcode ";
    $arySelect[] = "  LEFT JOIN m_monetaryunit mu ";
    $arySelect[] = "    ON r.lngmonetaryunitcode = mu.lngmonetaryunitcode ";

    // -------------------
    //  検索条件設定
    // -------------------
    $aryWhere[] = " WHERE 1=1"; // ダミー検索条件（常に真。後続の検索条件に最初からANDを付与するために存在）

    // 受注状態コード
    if ($aryCondition["lngreceivestatuscode"]) {
        $aryWhere[] = " AND r.lngreceivestatuscode = '" . $aryCondition["lngreceivestatuscode"] . "'";
    }

    // 顧客（コードで検索）
    if ($aryCondition["strCompanyDisplayCode"]) {
        $aryWhere[] = " AND c.strcompanydisplaycode = '" . $aryCondition["strCompanyDisplayCode"] . "'";
    }

    // 顧客受注番号
    if ($aryCondition["strCustomerReceiveCode"]) {
        $aryWhere[] = " AND r.strcustomerreceivecode = '" . $aryCondition["strCustomerReceiveCode"] . "'";
    }

    // 受注番号
    if ($aryCondition["lngReceiveNo"]) {
        $aryWhere[] = " AND r.lngreceiveno = " . $aryCondition["lngReceiveNo"];
    }

    // 受注明細番号
    if ($aryCondition["lngReceiveDetailNo"]) {
        $aryWhere[] = " AND rd.lngreceivedetailno = " . $aryCondition["lngReceiveDetailNo"];
    }

    // リビジョン番号
    if ($aryCondition["lngReceiveRevisionNo"]) {
        $aryWhere[] = " AND rd.lngrevisionno = " . $aryCondition["lngReceiveRevisionNo"];
    }

    // 製品コード
    if ($aryCondition["strReceiveDetailProductCode"]) {
        $aryWhere[] = " AND rd.strproductcode = '" . $aryCondition["strReceiveDetailProductCode"] . "'";
    }

    // 営業部署（コードで検索）
    if ($aryCondition["lngInChargeGroupCode"]) {
        $aryWhere[] = " AND g.strgroupdisplaycode = '" . $aryCondition["lngInChargeGroupCode"] . "'";
    }

    // 売上区分（コードで検索）
    if ($aryCondition["lngSalesClassCode"]) {
        $aryWhere[] = " AND rd.lngsalesclasscode = " . $aryCondition["lngSalesClassCode"];
    }

    // 顧客品番
    if ($aryCondition["strGoodsCode"]) {
        $aryWhere[] = " AND p2.strgoodscode = " . $aryCondition["strGoodsCode"];
    }

    // 納品日(FROM)
    if ($aryCondition["From_dtmDeliveryDate"]) {
        $dtmSearchDate = $aryCondition["From_dtmDeliveryDate"] . " 00:00:00";
        $aryWhere[] = " AND rd.dtmdeliverydate >= '" . $dtmSearchDate . "'";
    }

    // 納品日(TO)
    if ($aryCondition["To_dtmDeliveryDate"]) {
        $dtmSearchDate = $aryCondition["To_dtmDeliveryDate"] . " 23:59:59";
        $aryWhere[] = " AND rd.dtmdeliverydate <= '" . $dtmSearchDate . "'";
    }

    // 明細備考
    if ($aryCondition["strNote"]) {
        $aryWhere[] = " AND rd.strNote LIKE '%" . $aryCondition["strNote"] . "%'";
    }

    // 再販を含む（offの場合、t_receivedetail.strrevisecode='00'のみを対象）
    if ($aryCondition["IsIncludingResale"] == "Off") {
        $aryWhere[] = " AND rd.strrevisecode = '00'";
    }

    // -------------------
    //  並び順定義
    // -------------------
    $aryOrder[] = " ORDER BY";
    $aryOrder[] = "  rd.lngsortkey";

    // -------------------
    // クエリ作成
    // -------------------
    $strQuery = "";
    $strQuery .= implode("\n", $arySelect);
    $strQuery .= "\n";
    $strQuery .= implode("\n", $aryWhere);
    $strQuery .= "\n";
    $strQuery .= implode("\n", $aryOrder);
    // -------------------
    // クエリ実行
    // -------------------
    list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);

    // 結果を配列に格納
    $aryResult = []; //空の配列で初期化
    if (0 < $lngResultNum) {
        for ($j = 0; $j < $lngResultNum; $j++) {
            $aryResult[] = $objDB->fetchArray($lngResultID, $j);
        }
    }
    $objDB->freeResult($lngResultID);

    return $aryResult;
}

function fncGetReceiveDetailHtml($aryDetail, $isCreateNew)
{
    $strHtml = "";
    for ($i = 0; $i < count($aryDetail); $i++) {
        $strDisplayValue = "";
        //行選択スクリプト埋め込み
        $strHtml .= "<tr onmousedown='RowClick(this,false);'>";

        // 明細選択エリアはチェックボックスあり、出力明細一覧エリアはチェックボックスなしのためこのようなスイッチを用意
        if ($isCreateNew) {
            // データ登録時、明細選択エリアには選択チェックボックスが必要（データ修正時、出力明細一覧エリアにチェックボックスは不要）
            $strHtml .= "<td class='detailCheckbox'><input type='checkbox' name='edit' onmousedown='return false;' onclick='return false;'></td>";
        }

        //NO.
        // データ修正時、出力明細一覧エリアのNo.は明細の配列のインデックス+1とする（行番号）
        $rownumber = $i + 1;
        $strHtml .= "<td name='rownum'>" . $rownumber . "</td>";
        //顧客発注番号
        $strDisplayValue = htmlspecialchars($aryDetail[$i]["strcustomerreceivecode"]);
        $strHtml .= "<td class='detailCustomerReceiveCode'>" . $strDisplayValue . "</td>";
        //受注番号
        $strDisplayValue = htmlspecialchars($aryDetail[$i]["strreceivecode"]);
        $strHtml .= "<td class='detailReceiveCode'>" . $strDisplayValue . "</td>";
        //顧客品番
        $strDisplayValue = htmlspecialchars($aryDetail[$i]["strgoodscode"]);
        $strHtml .= "<td class='detailGoodsCode'>" . $strDisplayValue . "</td>";
        //製品コード
        $strDisplayValue = htmlspecialchars($aryDetail[$i]["strproductcode"]);
        $strDisplayValue .= "_";
        $strDisplayValue .= htmlspecialchars($aryDetail[$i]["strrevisecode"]);
        $strHtml .= "<td class='detailProductCode'>" . $strDisplayValue . "</td>";
        //製品名
        $strDisplayValue = htmlspecialchars($aryDetail[$i]["strproductname"]);
        $strHtml .= "<td class='detailProductName'>" . $strDisplayValue . "</td>";
        //製品名（英語）
        $strDisplayValue = htmlspecialchars($aryDetail[$i]["strproductenglishname"]);
        $strHtml .= "<td class='detailProductEnglishName'>" . $strDisplayValue . "</td>";
        //営業部署
        $strDisplayValue = htmlspecialchars($aryDetail[$i]["strsalesdeptname"]);
        $strHtml .= "<td class='detailSalesDeptName'>" . $strDisplayValue . "</td>";
        //売上区分
        $strDisplayValue = htmlspecialchars($aryDetail[$i]["strsalesclassname"]);
        $strHtml .= "<td class='detailSalesClassName'>" . $strDisplayValue . "</td>";
        //納期
        $strDisplayValue = htmlspecialchars($aryDetail[$i]["dtmdeliverydate"]);
        $strHtml .= "<td class='detailDeliveryDate'>" . $strDisplayValue . "</td>";
        //入数
        $strDisplayValue = htmlspecialchars($aryDetail[$i]["lngunitquantity"]);
        $strHtml .= "<td class='detailUnitQuantity'>" . $strDisplayValue . "</td>";
        //単価
        $strDisplayValue = htmlspecialchars($aryDetail[$i]["curproductprice"]);
        $strHtml .= "<td class='detailProductPrice' style='text-align:right;'>" . number_format($strDisplayValue, 4) . "</td>";
        //単位
        $strDisplayValue = htmlspecialchars($aryDetail[$i]["strproductunitname"]);
        $strHtml .= "<td class='detailProductUnitName'>" . $strDisplayValue . "</td>";
        //数量
        $strDisplayValue = htmlspecialchars($aryDetail[$i]["lngproductquantity"]);
        $strHtml .= "<td class='detailProductQuantity' style='text-align:right;'>" . number_format($strDisplayValue) . "</td>";
        //税抜金額
        $strDisplayValue = htmlspecialchars($aryDetail[$i]["cursubtotalprice"]);
        $strHtml .= "<td class='detailSubTotalPrice' style='text-align:right;'>" . number_format($strDisplayValue) . "</td>";
        //受注番号（明細登録用）
        $strDisplayValue = htmlspecialchars($aryDetail[$i]["lngreceiveno"]);
        $strHtml .= "<td class='forEdit detailReceiveNo'>" . $strDisplayValue . "</td>";
        //受注明細番号（明細登録用）
        $strDisplayValue = htmlspecialchars($aryDetail[$i]["lngreceivedetailno"]);
        $strHtml .= "<td class='forEdit detailReceiveDetailNo'>" . $strDisplayValue . "</td>";
        //リビジョン番号（明細登録用）
        $strDisplayValue = htmlspecialchars($aryDetail[$i]["lngreceiverevisionno"]);
        $strHtml .= "<td class='forEdit detailReceiveRevisionNo'>" . $strDisplayValue . "</td>";
        //再販コード（明細登録用）
        $strDisplayValue = htmlspecialchars($aryDetail[$i]["strrevisecode"]);
        $strHtml .= "<td class='forEdit detailReviseCode'>" . $strDisplayValue . "</td>";
        //売上区分コード（明細登録用）
        $strDisplayValue = htmlspecialchars($aryDetail[$i]["lngsalesclasscode"]);
        $strHtml .= "<td class='forEdit detailSalesClassCode'>" . $strDisplayValue . "</td>";
        //製品単位コード（明細登録用）
        $strDisplayValue = htmlspecialchars($aryDetail[$i]["lngproductunitcode"]);
        $strHtml .= "<td class='forEdit detailProductUnitCode'>" . $strDisplayValue . "</td>";
        //備考（明細登録用）
        $strDisplayValue = htmlspecialchars($aryDetail[$i]["strnote"]);
        $strHtml .= "<td class='forEdit detailNote'>" . $strDisplayValue . "</td>";
        //通貨単位コード（明細登録用）
        $strDisplayValue = htmlspecialchars($aryDetail[$i]["lngmonetaryunitcode"]);
        $strHtml .= "<td class='forEdit detailMonetaryUnitCode'>" . $strDisplayValue . "</td>";
        //通貨レートコード（明細登録用）
        $strDisplayValue = htmlspecialchars($aryDetail[$i]["lngmonetaryratecode"]);
        $strHtml .= "<td class='forEdit detailMonetaryRateCode'>" . $strDisplayValue . "</td>";
        //通貨単位記号（明細登録用）
        $strDisplayValue = htmlspecialchars($aryDetail[$i]["strmonetaryunitsign"]);
        $strHtml .= "<td class='forEdit detailMonetaryUnitSign'>" . $strDisplayValue . "</td>";
        //明細統一フラグ（明細登録用）
        $strDisplayValue = htmlspecialchars($aryDetail[$i]["bytdetailunifiedflg"]);
        $strHtml .= "<td class='forEdit detailUnifiedFlg'>" . $strDisplayValue . "</td>";

        $strHtml .= "</tr>";
    }
    return $strHtml;
}

// 納品伝票マスタより作成日を取得
function fncGetInsertDateBySlipCode($strSlipCode, $objDB)
{
    $strQuery = ""
        . "SELECT"
        . "  TO_CHAR(dtminsertdate, 'yyyy/mm/dd hh24:mm:ss') as dtminsertdate"
        . " FROM"
        . "  m_slip"
        . " WHERE"
        . "  strslipcode = '" . $strSlipCode . "'"
    ;

    list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);
    if ($lngResultNum) {
        for ($i = 0; $i < $lngResultNum; $i++) {
            $aryResult[] = $objDB->fetchArray($lngResultID, $i);
        }
    } else {
        fncOutputError(9501, DEF_FATAL, "納品伝票の作成日の取得に失敗", true, "", $objDB);
    }
    $objDB->freeResult($lngResultID);

    return $aryResult[0]["dtminsertdate"];
}

// 納品伝票マスタより売上番号を取得
function fncGetSalesNoBySlipCode($strSlipCode, $objDB)
{
    $strQuery = ""
        . "SELECT"
        . "  lngsalesno"
        . " FROM"
        . "  m_slip"
        . " WHERE"
        . "  strslipcode = '" . $strSlipCode . "'"
    ;

    list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);
    if ($lngResultNum) {
        for ($i = 0; $i < $lngResultNum; $i++) {
            $aryResult[] = $objDB->fetchArray($lngResultID, $i);
        }
    } else {
        fncOutputError(9501, DEF_FATAL, "納品伝票の売上番号の取得に失敗", true, "", $objDB);
    }
    $objDB->freeResult($lngResultID);

    return $aryResult[0]["lngsalesno"];
}

// 印刷回数を1増やす
function fncIncrementPrintCountBySlipCode($strSlipCode, $objDB)
{

    $aryUpdate = array();
    $aryUpdate[] = "UPDATE m_slip ";
    $aryUpdate[] = " SET lngprintcount = (lngprintcount+1) ";
    $aryUpdate[] = " WHERE ";
    $aryUpdate[] = "  strslipcode = '" . $strSlipCode . "'";
    $strQuery = "";
    $strQuery .= implode("\n", $aryUpdate);

    // トランザクション開始
    $objDB->transactionBegin();

    // 登録実行
    if (!$lngResultID = $objDB->execute($strQuery)) {
        // 失敗
        fncOutputError(9501, DEF_FATAL, "納品伝票の印刷回数の更新に失敗", true, "", $objDB);
    } else {
        // 成功
        $objDB->freeResult($lngResultID);
        $objDB->transactionCommit();
    }

}

// 受注状態コードによるバリデーション
function fncNotReceivedDetailExists($aryDetail, $objDB)
{
    for ($i = 0; $i < count($aryDetail); $i++) {
        $d = $aryDetail[$i];

        $lngReceiveNo = $d["lngreceiveno"];
        $lngRevisionNo = $d["lngreceiverevisionno"];

        $strQuery = ""
            . "SELECT"
            . "  lngreceivestatuscode"
            . " FROM"
            . "  m_receive"
            . " WHERE"
            . "  lngreceivestatuscode not in (2, 4)"
            . "  AND lngreceiveno = " . $lngReceiveNo
            . "  AND lngrevisionno = " . $lngRevisionNo
        ;
        list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);
        if ($lngResultID) {
            // 受注状態コードが2以外の明細が存在するならtrueを返して検索打ち切り
            if ($lngResultNum > 0) {return true;}
        } else {
            fncOutputError(9501, DEF_FATAL, "受注状態コードの取得に失敗", true, "", $objDB);
        }
        $objDB->freeResult($lngResultID);
    }

    // 受注状態コードが2以外の明細は存在しない
    return false;

}

// 明細に紐づく受注マスタの受注状態コードを更新
function fncUpdateReceiveMaster($aryDetail, $objDB)
{
    for ($i = 0; $i < count($aryDetail); $i++) {
        $d = $aryDetail[$i];

        $lngReceiveNo = $d["lngreceiveno"];
        $lngRevisionNo = $d["lngreceiverevisionno"];

        $strQuery = ""
            . "UPDATE"
            . "  m_receive"
            . " SET"
            . "  lngreceivestatuscode = 4"
            . " WHERE"
            . "  lngreceiveno = " . $lngReceiveNo
            . "  AND lngrevisionno = " . $lngRevisionNo
        ;

        // 更新実行
        if (!$lngResultID = $objDB->execute($strQuery)) {
            fncOutputError(9051, DEF_ERROR, "受注マスタ更新失敗。", true, "", $objDB);
            // 失敗
            return false;
        }
        $objDB->freeResult($lngResultID);
    }

    // 成功
    return true;
}

// 表示用会社コードから会社コードを取得する
function fncGetNumericCompanyCode($strCompanyDisplayCode, $objDB)
{
    $lngCompanyCode = fncGetMasterValue("m_company", "strcompanydisplaycode", "lngcompanycode", $strCompanyDisplayCode . ":str", '', $objDB);
    return $lngCompanyCode;
}

// 表示用会社コードから国コードを取得する
function fncGetCountryCode($strCompanyDisplayCode, $objDB)
{
    $lngCountryCode = fncGetMasterValue("m_company", "strcompanydisplaycode", "lngcountrycode", "$strCompanyDisplayCode:str", '', $objDB);
    return $lngCountryCode;
}

// 表示用会社コードから締め日を取得する
function fncGetClosedDay($strCompanyDisplayCode, $objDB)
{
    $strQuery = ""
    . "SELECT"
    . "  cd.lngclosedday"
    . " FROM"
    . "  m_company c "
    . "    INNER JOIN m_closedday cd "
    . "    on c.lngcloseddaycode = cd.lngcloseddaycode"
    . " WHERE"
    . "  c.strcompanydisplaycode = " . withQuote($strCompanyDisplayCode)
    ;

    list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);
    if ($lngResultNum) {
        for ($i = 0; $i < $lngResultNum; $i++) {
            $aryResult[] = $objDB->fetchArray($lngResultID, $i);
        }
    } else {
        fncOutputError(9501, DEF_FATAL, "締め日の取得に失敗", true, "", $objDB);
    }
    $objDB->freeResult($lngResultID);

    return $aryResult[0]["lngclosedday"];
}

// 表示用ユーザーコードからユーザーコードを取得する
function fncGetNumericUserCode($strUserDisplayCode, $objDB)
{
    $lngUserCode = fncGetMasterValue("m_user", "struserdisplaycode", "lngusercode", $strUserDisplayCode . ":str", '', $objDB);
    return $lngUserCode;
}

// 会社コードに紐づく帳票伝票種別を取得
function fncGetSlipKindByCompanyCode($lngCompanyCode, $objDB)
{
    $strQuery = ""
        . "SELECT"
        . "  c.lngcompanycode,"
        . "  c.strcompanydisplaycode,"
        . "  c.strcompanydisplayname,"
        . "  sk.lngslipkindcode,"
        . "  sk.strslipkindname,"
        . "  sk.lngmaxline"
        . " FROM m_slipkindrelation skr"
        . "   LEFT JOIN m_slipkind sk ON skr.lngslipkindcode = sk.lngslipkindcode"
        . "   LEFT JOIN m_company c ON skr.lngcompanycode = c.lngcompanycode"
        . " WHERE c.lngcompanycode = " . $lngCompanyCode
    ;

    list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);
    if ($lngResultNum) {
        for ($i = 0; $i < $lngResultNum; $i++) {
            $aryResult[] = $objDB->fetchArray($lngResultID, $i);
        }
    } else {
        fncOutputError(9501, DEF_FATAL, "帳票伝票種別の取得に失敗", true, "", $objDB);
    }
    $objDB->freeResult($lngResultID);

    return $aryResult[0];
}

// 会社コードに紐づく会社情報を取得
function fncGetCompanyInfoByCompanyCode($lngCompanyCode, $objDB)
{
    $strQuery = ""
        . "SELECT "
        . "  c.lngcompanycode,"
        . "  c.strcompanydisplaycode, "
        . "  c.strcompanydisplayname,"
        . "  c.straddress1,"
        . "  c.straddress2,"
        . "  c.straddress3,"
        . "  c.straddress4,"
        . "  c.strtel1,"
        . "  c.strtel2,"
        . "  c.strfax1,"
        . "  c.strfax2,"
        . "  sc.strstockcompanycode,"
        . "  cp.strprintcompanyname,"
        . "  c.strcompanyname,"
        . "  c.bytorganizationfront,"
        . "  o.lngorganizationcode,"
        . "  CASE o.lngorganizationcode WHEN 0 THEN '' ELSE o.strorganizationname END AS strorganizationname"
        . " FROM m_company c"
        . "  LEFT JOIN m_stockcompanycode sc ON c.lngcompanycode = sc.lngcompanyno"
        . "  LEFT JOIN m_companyprintname cp ON c.lngcompanycode = cp.lngcompanycode"
        . "  LEFT JOIN m_organization o ON c.lngorganizationcode = o.lngorganizationcode"
        . " WHERE c.lngcompanycode = " . $lngCompanyCode
    ;
    list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);
    if ($lngResultNum) {
        for ($i = 0; $i < $lngResultNum; $i++) {
            $aryResult[] = $objDB->fetchArray($lngResultID, $i);
        }
    } else {
        fncOutputError(9501, DEF_FATAL, "会社情報の取得に失敗", true, "", $objDB);
    }
    $objDB->freeResult($lngResultID);

    return $aryResult[0];
}

// 顧客社名を取得
function fncGetCustomerCompanyName($lngCountryCode, $aryCompanyInfo)
{
    if (strlen($aryCompanyInfo["strprintcompanyname"]) != 0) {
        return $aryCompanyInfo["strprintcompanyname"];
    }

    // 帳票用会社名取得
    if ($lngCountryCode != 81) {
        return $aryCompanyInfo["strcompanyname"];
    } else if ($aryCompanyInfo["bytorganizationfront"] == true) {
        return $aryCompanyInfo["strorganizationname"] . $aryCompanyInfo["strcompanyname"];
    } else {
        return $aryCompanyInfo["strcompanyname"] . $aryCompanyInfo["strorganizationname"];
    }
}

// 顧客名を取得
function fncGetCustomerName($aryCompanyInfo)
{
    return $aryCompanyInfo["strcompanyname"];
}

// ユーザーコードに紐づくユーザー情報を取得
function fncGetUserInfoByUserCode($lngUserCode, $objDB)
{
    $strQuery = ""
        . "SELECT"
        . "  u.lngusercode,"
        . "  u.struserdisplaycode,"
        . "  u.struserdisplayname,"
        . "  gr.lnggroupcode"
        . " FROM m_user u"
        . "  LEFT JOIN (select * from m_grouprelation WHERE bytdefaultflag=TRUE) gr ON u.lngusercode = gr.lngusercode "
        . " WHERE u.lngusercode=" . $lngUserCode
    ;

    list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);
    if ($lngResultNum) {
        for ($i = 0; $i < $lngResultNum; $i++) {
            $aryResult[] = $objDB->fetchArray($lngResultID, $i);
        }
    } else {
        fncOutputError(9501, DEF_FATAL, "ユーザー情報の取得に失敗", true, "", $objDB);
    }
    $objDB->freeResult($lngResultID);

    return $aryResult[0];
}

// 受注データに紐づく換算レートを取得
function fncGetConversionRateByReceiveData($lngReceiveNo, $lngReceiveRevisionNo, $dtmAppropriationDate, $objDB)
{
    $strQuery = ""
        . "SELECT"
        . "  r.lngreceiveno,"
        . "  r.lngmonetaryunitcode,"
        . "  r.lngmonetaryratecode,"
        . "  mr.curconversionrate,"
        . "  mr.dtmapplystartdate,"
        . "  mr.dtmapplyenddate"
        . " FROM m_receive r"
        . "  LEFT JOIN (select distinct * from m_monetaryrate "
        . "             where dtmapplystartdate<='" . $dtmAppropriationDate . "' and '" . $dtmAppropriationDate . "'<=dtmapplyenddate) mr "
        . "   ON r.lngmonetaryunitcode = mr.lngmonetaryunitcode AND r.lngmonetaryratecode = mr.lngmonetaryratecode"
        . " WHERE r.lngreceiveno=" . $lngReceiveNo . " AND r.lngrevisionno = " . $lngReceiveRevisionNo
    ;

    list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);
    if ($lngResultNum) {
        for ($i = 0; $i < $lngResultNum; $i++) {
            $aryResult[] = $objDB->fetchArray($lngResultID, $i);
        }
    } else {
        fncOutputError(9501, DEF_FATAL, "換算レートの取得に失敗", true, "", $objDB);
    }
    $objDB->freeResult($lngResultID);

    return $aryResult[0];
}

// 納品書NOの発行
function fncPublishSlipCode($dtmPublishDate, $objDB)
{
    $strYYYYMM = substr($dtmPublishDate, 0, 4) . substr($dtmPublishDate, 5, 2);

    $strQuery = ""
        . "SELECT"
        . "  MAX(strslipcode) as yyyymmnn,"
        . "  SUBSTR(MAX(strslipcode),7,8) as nn"
        . " FROM"
        . "  m_slip"
        . " WHERE"
        . "  strslipcode IS NOT NULL "
        . "  AND LENGTH(strslipcode) = 8"
        . "  AND strslipcode LIKE '" . $strYYYYMM . "__'"
    ;

    list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);
    if ($lngResultNum) {
        for ($i = 0; $i < $lngResultNum; $i++) {
            $aryResult[] = $objDB->fetchArray($lngResultID, $i);
        }
    } else {
        fncOutputError(9501, DEF_FATAL, "納品書NOの発行に失敗", true, "", $objDB);
    }
    $objDB->freeResult($lngResultID);

    // 納品書NOの生成
    if ($lngResultNum != 0) {
        $lngNumber = intval($aryResult[0]["nn"]);
        $lngNumber += 1;
    } else {
        // 当日1件目は nn='01' で開始
        $lngNumber = 1;
    }

    $strNN = sprintf("%02d", $lngNumber);
    $strPublishdSlipCode = $strYYYYMM . $strNN;

    return $strPublishdSlipCode;
}

// 消費税額の計算
function fncCalcTaxPrice($curPrice, $lngTaxClassCode, $curTax)
{
    $curTaxPrice = 0;

    if ($lngTaxClassCode == "1") {
        // 1:非課税
        $curTaxPrice = 0;
    } else if ($lngTaxClassCode == "2") {
        // 2:外税
        $curTaxPrice = floor($curPrice * $curTax);
    } else if ($lngTaxClassCode == "3") {
        // 3:内税
        $curTaxPrice = floor(($curPrice / (1 + $curTax)) * $curTax);
    }

    return $curTaxPrice;
}

// 納品伝票マスタのリビジョン番号の最大値を取得する
function fncGetSlipMaxRevisionNo($lngSlipNo, $objDB)
{
    $aryQuery = array();
    $aryQuery[] = "SELECT ";
    $aryQuery[] = "  lngslipno ";
    $aryQuery[] = "  , MAX(lngrevisionno) as lngrevisionno";
    $aryQuery[] = " FROM ";
    $aryQuery[] = "  m_slip  ";
    $aryQuery[] = " GROUP BY ";
    $aryQuery[] = "  lngslipno  ";
    $aryQuery[] = " HAVING ";
    $aryQuery[] = "  lngslipno = " . $lngSlipNo;

    $strQuery = "";
    $strQuery .= implode("\n", $aryQuery);

    list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);
    if ($lngResultNum) {
        for ($i = 0; $i < $lngResultNum; $i++) {
            $aryResult[] = $objDB->fetchArray($lngResultID, $i);
        }
    } else {
        fncOutputError(9501, DEF_FATAL, "納品伝票マスタのリビジョン番号取得に失敗", true, "", $objDB);
    }
    $objDB->freeResult($lngResultID);

    return $aryResult[0]["lngrevisionno"];
}

// --------------------------------
//
// 売上（納品書）登録メイン関数
//
// --------------------------------
function fncRegisterSalesAndSlip(
    $lngRenewTargetSlipNo, $strRenewTargetSlipCode, $lngRenewTargetSalesNo, $strRenewTargetSalesCode,
    $aryHeader, $aryDetail, $objDB, $objAuth) {
    // 戻り値の初期化
    $aryRegisterResult = array();
    $aryRegisterResult["result"] = false;
    $aryRegisterResult["aryPerPage"] = array();

    // 登録か修正か（修正対象となる納品伝票番号が空なら登録、空でないなら修正）
    $isCreateNew = strlen($lngRenewTargetSlipNo) == 0;

    // 現在日付
    $dtmNowDate = date('Y/m/d', time());
    // 計上日
    $dtmAppropriationDate = $dtmNowDate;
    // 顧客の会社コードを取得
    $lngCustomerCompanyCode = fncGetNumericCompanyCode($aryHeader["strcompanydisplaycode"], $objDB);
    // 顧客の会社コードに紐づく会社情報を取得
    $aryCustomerCompany = fncGetCompanyInfoByCompanyCode($lngCustomerCompanyCode, $objDB);
    // 換算レートの取得
    $aryConversionRate = fncGetConversionRateByReceiveData($aryDetail[0]["lngreceiveno"], $aryDetail[0]["lngreceiverevisionno"], $dtmAppropriationDate, $objDB);

    // 起票者に紐づくユーザー情報を取得
    if ($aryHeader["strdrafteruserdisplaycode"]) {
        // 起票者が入力されている場合
        $lngDrafterUserCode = fncGetNumericUserCode($aryHeader["strdrafteruserdisplaycode"], $objDB);
        $aryDrafter = fncGetUserInfoByUserCode($lngDrafterUserCode, $objDB);
    } else {
        // 起票者が未入力の場合
        $aryDrafter = array();
        $aryDrafter["lngusercode"] = null;
        $aryDrafter["struserdisplaycode"] = null;
        $aryDrafter["struserdisplayname"] = null;
        $aryDrafter["lnggroupcode"] = null;
    }

    // 顧客の国コードを取得
    $lngCustomerCountryCode = fncGetCountryCode($aryHeader["strcompanydisplaycode"], $objDB);
    // 顧客社名の取得
    $strCustomerCompanyName = fncGetCustomerCompanyName($lngCustomerCountryCode, $aryCustomerCompany);
    // 顧客名の取得
    $strCustomerName = fncGetCustomerName($aryCustomerCompany);
    // 納品先の会社コードの取得
    if ($aryHeader["strdeliveryplacecompanydisplaycode"]) {
        // 納品先が入力されている場合
        $lngDeliveryPlaceCode = fncGetNumericCompanyCode($aryHeader["strdeliveryplacecompanydisplaycode"], $objDB);
    } else {
        // 納品先が未入力の場合
        $lngDeliveryPlaceCode = null;
    }

    // 顧客の会社コードに紐づく納品伝票種別を取得
    $aryReport = fncGetSlipKindByCompanyCode($lngCustomerCompanyCode, $objDB);
    // 顧客に紐づく帳票1ページあたりの最大明細数を取得する
    $maxItemPerPage = intval($aryReport["lngmaxline"]);
    // 登録する全明細の数
    $totalItemCount = count($aryDetail);
    // 最大ページ数の計算
    $maxPageCount = ceil($totalItemCount / $maxItemPerPage);

    // ページ単位でのデータ登録
    for ($page = 1; $page <= $maxPageCount; $page++) {

        // 現在のページ数と1ページあたりの明細数から、
        // 登録する明細のインデックスの最小値と最大値を求める
        $itemMinIndex = ($page - 1) * $maxItemPerPage;
        $itemMaxIndex = $page * $maxItemPerPage - 1;
        if ($itemMaxIndex > $totalItemCount - 1) {
            $itemMaxIndex = $totalItemCount - 1;
        }

        // リビジョン番号
        if ($isCreateNew) {
            // 登録：0 固定
            $lngRevisionNo = 0;
        } else {
            // 修正：同一納品伝票番号内での最大値＋１
            $lngRevisionNo = fncGetSlipMaxRevisionNo($lngRenewTargetSlipNo, $objDB) + 1;
        }

        // 売上番号
        if ($isCreateNew) {
            // 登録：シーケンスより発番
            $lngSalesNo = fncGetSequence('m_sales.lngSalesNo', $objDB);
        } else {
            // 修正：修正対象に紐づく値
            $lngSalesNo = $lngRenewTargetSalesNo;
        }

        // 売上コード
        if ($isCreateNew) {
            // 登録：当月に紐づく売上コードの発番
            $strSalesCode = fncGetDateSequence(date('Y', strtotime($dtmNowDate)),
                date('m', strtotime($dtmNowDate)), "m_sales.lngSalesNo", $objDB);
        } else {
            // 修正：修正対象に紐づく値
            $strSalesCode = $strRenewTargetSalesCode;
        }

        // 納品伝票番号
        if ($isCreateNew) {
            // 登録：シーケンスより発番
            $lngSlipNo = fncGetSequence('m_Slip.lngSlipNo', $objDB);
        } else {
            // 修正：修正対象に紐づく値
            $lngSlipNo = $lngRenewTargetSlipNo;
        }

        // 納品伝票コード
        if ($isCreateNew) {
            // 登録：当日に紐づく納品伝票コードの発番
            $strSlipCode = fncPublishSlipCode($dtmNowDate, $objDB);
        } else {
            // 修正：修正対象に紐づく値
            $strSlipCode = $strRenewTargetSlipCode;
        }

        // ページ単位の情報を保存（後の登録結果画面で使う）
        $aryPageInfo = array();
        $aryPageInfo["lngSlipNo"] = $lngSlipNo;
        $aryPageInfo["strSlipCode"] = $strSlipCode;
        $aryPageInfo["lngRevisionNo"] = $lngRevisionNo;
        $aryRegisterResult["aryPerPage"][] = $aryPageInfo;

        // --------------------------------
        //   データベース変更
        // --------------------------------
        // 売上マスタ登録
        if (!fncRegisterSalesMaster($lngSalesNo, $lngRevisionNo, $strSlipCode, $strSalesCode, $dtmAppropriationDate, $aryConversionRate, $aryCustomerCompany, $aryDrafter,
            $aryHeader, $aryDetail, $objDB, $objAuth)) {
            // 失敗
            $aryRegisterResult["result"] = false;
            return $aryRegisterResult;
        }

        // 売上明細登録
        if (!fncRegisterSalesDetail($itemMinIndex, $itemMaxIndex, $lngSalesNo, $lngRevisionNo,
            $aryHeader, $aryDetail, $objDB, $objAuth)) {
            // 失敗
            $aryRegisterResult["result"] = false;
            return $aryRegisterResult;
        }

        // 納品伝票マスタ登録
        if (!fncRegisterSlipMaster($lngSlipNo, $lngRevisionNo, $lngSalesNo, $strSlipCode, $strCustomerCompanyName, $strCustomerName, $aryCustomerCompany, $lngDeliveryPlaceCode,
            $aryHeader, $aryDetail, $objDB, $objAuth)) {
            // 失敗
            $aryRegisterResult["result"] = false;
            return $aryRegisterResult;
        }

        // 納品伝票明細登録
        if (!fncRegisterSlipDetail($itemMinIndex, $itemMaxIndex, $lngSlipNo, $lngRevisionNo,
            $aryHeader, $aryDetail, $objDB, $objAuth)) {
            // 失敗
            $aryRegisterResult["result"] = false;
            return $aryRegisterResult;
        }

    }

    // 成功
    $aryRegisterResult["result"] = true;
    return $aryRegisterResult;
}

// --------------------------------
// パラメータバインド用ヘルパ関数
// --------------------------------
// シングルクォートで囲む
function withQuote($source)
{
    return "'" . $source . "'";
}

// Nullだったら"Null"、Null以外だったら値をそのまま返す
function nullIfEmpty($source)
{
    if (is_null($source)) {
        return "Null";
    }

    if (strlen($source) == 0) {
        return "Null";
    }

    return $source;
}

// Nullだったら"Null"、Null以外だったら値をシングルクォートで囲って返す
function nullIfEmptyWithQuote($source)
{
    if (is_null($source)) {
        return "Null";
    }

    if (strlen($source) == 0) {
        return "Null";
    }

    return withQuote($source);
}

// 売上マスタ登録
function fncRegisterSalesMaster($lngSalesNo, $lngRevisionNo, $strSlipCode, $strSalesCode, $dtmAppropriationDate, $aryConversionRate, $aryCustomerCompany, $aryDrafter,
    $aryHeader, $aryDetail, $objDB, $objAuth) {
    // 換算レートの設定
    if (strlen($aryConversionRate["curconversionrate"]) == 0) {
        $curConversionRate = "Null";
    } else {
        $curConversionRate = $aryConversionRate["curconversionrate"];
    }

    // 登録データのセット
    $v_lngsalesno = $lngSalesNo; //1:売上番号
    $v_lngrevisionno = $lngRevisionNo; //2:リビジョン番号
    $v_strsalescode = withQuote($strSalesCode); //3:売上コード
    $v_dtmappropriationdate = withQuote($dtmAppropriationDate); //4:計上日
    $v_lngcustomercompanycode = $aryCustomerCompany["lngcompanycode"]; //5:顧客コード
    $v_lnggroupcode = nullIfEmpty($aryDrafter["lnggroupcode"]); //6:グループコード
    $v_lngusercode = nullIfEmpty($aryDrafter["lngusercode"]); //7:ユーザコード
    $v_lngsalesstatuscode = "4"; //8:売上状態コード
    $v_lngmonetaryunitcode = $aryDetail[0]["lngmonetaryunitcode"]; //9:通貨単位コード
    $v_lngmonetaryratecode = $aryDetail[0]["lngmonetaryratecode"]; //10:通貨レートコード
    $v_curconversionrate = $curConversionRate; //11:換算レート
    $v_strslipcode = withQuote($strSlipCode); //12:納品書NO
    $v_lnginvoiceno = "Null"; //13:請求書番号
    $v_curtotalprice = $aryHeader["curtotalprice"]; //14:合計金額
    $v_strnote = withQuote($aryHeader["strnote"]); //15:備考
    $v_lnginputusercode = $objAuth->UserCode; //16:入力者コード
    $v_bytinvalidflag = "FALSE"; //17:無効フラグ
    $v_dtminsertdate = "now()"; //18:登録日

    // 登録クエリ作成
    $aryInsert = [];
    $aryInsert[] = "INSERT  ";
    $aryInsert[] = "INTO m_sales(  ";
    $aryInsert[] = "  lngsalesno "; //1:売上番号
    $aryInsert[] = "  , lngrevisionno "; //2:リビジョン番号
    $aryInsert[] = "  , strsalescode "; //3:売上コード
    $aryInsert[] = "  , dtmappropriationdate "; //4:計上日
    $aryInsert[] = "  , lngcustomercompanycode "; //5:顧客コード
    $aryInsert[] = "  , lnggroupcode "; //6:グループコード
    $aryInsert[] = "  , lngusercode "; //7:ユーザコード
    $aryInsert[] = "  , lngsalesstatuscode "; //8:売上状態コード
    $aryInsert[] = "  , lngmonetaryunitcode "; //9:通貨単位コード
    $aryInsert[] = "  , lngmonetaryratecode "; //10:通貨レートコード
    $aryInsert[] = "  , curconversionrate "; //11:換算レート
    $aryInsert[] = "  , strslipcode "; //12:納品書NO
    $aryInsert[] = "  , lnginvoiceno "; //13:請求書番号
    $aryInsert[] = "  , curtotalprice "; //14:合計金額
    $aryInsert[] = "  , strnote "; //15:備考
    $aryInsert[] = "  , lnginputusercode "; //16:入力者コード
    $aryInsert[] = "  , bytinvalidflag "; //17:無効フラグ
    $aryInsert[] = "  , dtminsertdate "; //18:登録日
    $aryInsert[] = ")  ";
    $aryInsert[] = "VALUES (  ";
    $aryInsert[] = "  " . $v_lngsalesno; //1:売上番号
    $aryInsert[] = " ," . $v_lngrevisionno; //2:リビジョン番号
    $aryInsert[] = " ," . $v_strsalescode; //3:売上コード
    $aryInsert[] = " ," . $v_dtmappropriationdate; //4:計上日
    $aryInsert[] = " ," . $v_lngcustomercompanycode; //5:顧客コード
    $aryInsert[] = " ," . $v_lnggroupcode; //6:グループコード
    $aryInsert[] = " ," . $v_lngusercode; //7:ユーザコード
    $aryInsert[] = " ," . $v_lngsalesstatuscode; //8:売上状態コード
    $aryInsert[] = " ," . $v_lngmonetaryunitcode; //9:通貨単位コード
    $aryInsert[] = " ," . $v_lngmonetaryratecode; //10:通貨レートコード
    $aryInsert[] = " ," . $v_curconversionrate; //11:換算レート
    $aryInsert[] = " ," . $v_strslipcode; //12:納品書NO
    $aryInsert[] = " ," . $v_lnginvoiceno; //13:請求書番号
    $aryInsert[] = " ," . $v_curtotalprice; //14:合計金額
    $aryInsert[] = " ," . $v_strnote; //15:備考
    $aryInsert[] = " ," . $v_lnginputusercode; //16:入力者コード
    $aryInsert[] = " ," . $v_bytinvalidflag; //17:無効フラグ
    $aryInsert[] = " ," . $v_dtminsertdate; //18:登録日
    $aryInsert[] = ") ";
    $strQuery = "";
    $strQuery .= implode("\n", $aryInsert);

    // 登録実行
    if (!$lngResultID = $objDB->execute($strQuery)) {
        fncOutputError(9051, DEF_ERROR, "売上マスタ登録失敗。", true, "", $objDB);
        // 失敗
        return false;
    }
    $objDB->freeResult($lngResultID);

    // 成功
    return true;
}

// 売上明細登録
function fncRegisterSalesDetail($itemMinIndex, $itemMaxIndex, $lngSalesNo, $lngRevisionNo,
    $aryHeader, $aryDetail, $objDB, $objAuth) {
    // 消費税率
    $curTax = floatval($aryHeader["curtax"]);
    // 消費税区分
    $lngTaxClassCode = $aryHeader["lngtaxclasscode"];

    for ($i = $itemMinIndex; $i <= $itemMaxIndex; $i++) {
        $d = $aryDetail[$i];

        // 明細単位での消費税金額の計算
        $curTaxPrice = fncCalcTaxPrice($d["cursubtotalprice"], $lngTaxClassCode, $curTax);

        // 登録データのセット
        $v_lngsalesno = $lngSalesNo; //1:売上番号
        $v_lngsalesdetailno = $d["rownumber"]; //2:売上明細番号
        $v_lngrevisionno = $lngRevisionNo; //3:リビジョン番号
        $v_strproductcode = withQuote(mb_substr($d["strproductcode"], 0, 5)); //4:製品コード
        $v_strrevisecode = withQuote($d["strrevisecode"]); //5:再販コード
        $v_lngsalesclasscode = $d["lngsalesclasscode"]; //6:売上区分コード
        $v_lngconversionclasscode = "Null"; //7:換算区分コード
        $v_lngquantity = $d["lngunitquantity"]; //8:入数
        $v_curproductprice = $d["curproductprice"]; //9:製品価格
        $v_lngproductquantity = $d["lngproductquantity"]; //10:製品数量
        $v_lngproductunitcode = $d["lngproductunitcode"]; //11:製品単位コード
        $v_lngtaxclasscode = $aryHeader["lngtaxclasscode"]; //12:消費税区分コード
        $v_lngtaxcode = nullIfEmpty($aryHeader["lngtaxcode"]); //13:消費税率コード
        $v_curtaxprice = $curTaxPrice; //14:消費税金額
        $v_cursubtotalprice = $d["cursubtotalprice"]; //15:小計金額
        $v_strnote = withQuote($d["strnote"]); //16:備考
        $v_lngsortkey = $d["rownumber"]; //17:表示用ソートキー
        $v_lngreceiveno = $d["lngreceiveno"]; //18:受注番号
        $v_lngreceivedetailno = $d["lngreceivedetailno"]; //19:受注明細番号
        $v_lngreceiverevisionno = $d["lngreceiverevisionno"]; //20:受注リビジョン番号

        // 登録クエリ作成
        $aryInsert = [];
        $aryInsert[] = "INSERT  ";
        $aryInsert[] = "INTO t_salesdetail(  ";
        $aryInsert[] = "  lngsalesno "; //1:売上番号
        $aryInsert[] = "  , lngsalesdetailno "; //2:売上明細番号
        $aryInsert[] = "  , lngrevisionno "; //3:リビジョン番号
        $aryInsert[] = "  , strproductcode "; //4:製品コード
        $aryInsert[] = "  , strrevisecode "; //5:再販コード
        $aryInsert[] = "  , lngsalesclasscode "; //6:売上区分コード
        $aryInsert[] = "  , lngconversionclasscode "; //7:換算区分コード
        $aryInsert[] = "  , lngquantity "; //8:入数
        $aryInsert[] = "  , curproductprice "; //9:製品価格
        $aryInsert[] = "  , lngproductquantity "; //10:製品数量
        $aryInsert[] = "  , lngproductunitcode "; //11:製品単位コード
        $aryInsert[] = "  , lngtaxclasscode "; //12:消費税区分コード
        $aryInsert[] = "  , lngtaxcode "; //13:消費税率コード
        $aryInsert[] = "  , curtaxprice "; //14:消費税金額
        $aryInsert[] = "  , cursubtotalprice "; //15:小計金額
        $aryInsert[] = "  , strnote "; //16:備考
        $aryInsert[] = "  , lngsortkey "; //17:表示用ソートキー
        $aryInsert[] = "  , lngreceiveno "; //18:受注番号
        $aryInsert[] = "  , lngreceivedetailno "; //19:受注明細番号
        $aryInsert[] = "  , lngreceiverevisionno "; //20:受注リビジョン番号
        $aryInsert[] = ")  ";
        $aryInsert[] = "VALUES (  ";
        $aryInsert[] = "  " . $v_lngsalesno; //1:売上番号
        $aryInsert[] = " ," . $v_lngsalesdetailno; //2:売上明細番号
        $aryInsert[] = " ," . $v_lngrevisionno; //3:リビジョン番号
        $aryInsert[] = " ," . $v_strproductcode; //4:製品コード
        $aryInsert[] = " ," . $v_strrevisecode; //5:再販コード
        $aryInsert[] = " ," . $v_lngsalesclasscode; //6:売上区分コード
        $aryInsert[] = " ," . $v_lngconversionclasscode; //7:換算区分コード
        $aryInsert[] = " ," . $v_lngquantity; //8:入数
        $aryInsert[] = " ," . $v_curproductprice; //9:製品価格
        $aryInsert[] = " ," . $v_lngproductquantity; //10:製品数量
        $aryInsert[] = " ," . $v_lngproductunitcode; //11:製品単位コード
        $aryInsert[] = " ," . $v_lngtaxclasscode; //12:消費税区分コード
        $aryInsert[] = " ," . $v_lngtaxcode; //13:消費税率コード
        $aryInsert[] = " ," . $v_curtaxprice; //14:消費税金額
        $aryInsert[] = " ," . $v_cursubtotalprice; //15:小計金額
        $aryInsert[] = " ," . $v_strnote; //16:備考
        $aryInsert[] = " ," . $v_lngsortkey; //17:表示用ソートキー
        $aryInsert[] = " ," . $v_lngreceiveno; //18:受注番号
        $aryInsert[] = " ," . $v_lngreceivedetailno; //19:受注明細番号
        $aryInsert[] = " ," . $v_lngreceiverevisionno; //20:受注リビジョン番号
        $aryInsert[] = ") ";
        $strQuery = "";
        $strQuery .= implode("\n", $aryInsert);

        // 登録実行
        if (!$lngResultID = $objDB->execute($strQuery)) {
            fncOutputError(9051, DEF_ERROR, "売上明細登録失敗。", true, "", $objDB);
            // 失敗
            return false;
        }
        $objDB->freeResult($lngResultID);
    }

    // 成功
    return true;
}

// 納品伝票マスタ登録
function fncRegisterSlipMaster($lngSlipNo, $lngRevisionNo, $lngSalesNo, $strSlipCode, $strCustomerCompanyName, $strCustomerName, $aryCustomerCompany, $lngDeliveryPlaceCode,
    $aryHeader, $aryDetail, $objDB, $objAuth) {
    // 仕入先コードの取得（空の場合は明示的にNullをセット）
    if (strlen($aryCustomerCompany["strstockcompanycode"]) != 0) {
        $strShipperCode = withQuote($aryCustomerCompany["strstockcompanycode"]);
    } else {
        $strShipperCode = "Null";
    }

    if (strlen($aryHeader["dtmpaymentlimit"]) != 0) {
        $dtmPaymentLimit = withQuote($aryHeader["dtmpaymentlimit"]);
    } else {
        $dtmPaymentLimit = "Null";
    }

    // 登録データのセット
    $v_lngslipno = $lngSlipNo; //1:納品伝票番号
    $v_lngrevisionno = $lngRevisionNo; //2:リビジョン番号
    $v_strslipcode = withQuote($strSlipCode); //3:納品伝票コード
    $v_lngsalesno = $lngSalesNo; //4:売上番号
    $v_lngcustomercode = nullIfEmpty($aryCustomerCompany["lngcompanycode"]); //5:顧客コード
    $v_strcustomercompanyname = withQuote($strCustomerCompanyName); //6:顧客社名
    $v_strcustomername = withQuote($strCustomerName); //7:顧客名
    $v_strcustomeraddress1 = nullIfEmptyWithQuote($aryCustomerCompany["straddress1"]); //8:顧客住所1
    $v_strcustomeraddress2 = nullIfEmptyWithQuote($aryCustomerCompany["straddress2"]); //9:顧客住所2
    $v_strcustomeraddress3 = nullIfEmptyWithQuote($aryCustomerCompany["straddress3"]); //10:顧客住所3
    $v_strcustomeraddress4 = nullIfEmptyWithQuote($aryCustomerCompany["straddress4"]); //11:顧客住所4
    $v_strcustomerphoneno = nullIfEmptyWithQuote($aryCustomerCompany["strtel1"]); //12:顧客電話番号
    $v_strcustomerfaxno = nullIfEmptyWithQuote($aryCustomerCompany["strfax1"]); //13:顧客FAX番号
    $v_strcustomerusername = withQuote($aryHeader["strcustomerusername"]); //14:顧客担当者名
    $v_strshippercode = $strShipperCode; //15:仕入先コード（出荷者）
    $v_dtmdeliverydate = withQuote($aryHeader["dtmdeliverydate"]); //16:納品日
    $v_lngdeliveryplacecode = nullIfEmpty($lngDeliveryPlaceCode); //17:納品場所コード
    $v_strdeliveryplacename = withQuote($aryHeader["strdeliveryplacename"]); //18:納品場所名
    $v_strdeliveryplaceusername = withQuote($aryHeader["strdeliveryplaceusername"]); //19:納品場所担当者名
    $v_lngpaymentmethodcode = $aryHeader["lngpaymentmethodcode"]; //20:支払方法コード
    $v_dtmpaymentlimit = $dtmPaymentLimit; //21:支払期限
    $v_lngtaxclasscode = $aryHeader["lngtaxclasscode"]; //22:課税区分コード
    $v_strtaxclassname = withQuote($aryHeader["strtaxclassname"]); //23:課税区分
    $v_curtax = $aryHeader["curtax"]; //24:消費税率
    $v_lngusercode = nullIfEmpty($aryHeader["lngdrafterusercode"]); //25:担当者コード
    $v_strusername = withQuote($aryHeader["strdrafteruserdisplayname"]); //26:担当者名
    $v_curtotalprice = $aryHeader["curtotalprice"]; //27:合計金額
    $v_lngmonetaryunitcode = $aryDetail[0]["lngmonetaryunitcode"]; //28:通貨単位コード
    $v_strmonetaryunitsign = withQuote($aryDetail[0]["strmonetaryunitsign"]); //29:通貨単位
    $v_dtminsertdate = "now()"; //30:作成日
    $v_lnginsertusercode = nullIfEmpty($objAuth->UserCode); //31:入力者コード
    $v_strinsertusername = withQuote($objAuth->UserDisplayName); //32:入力者名
    $v_strnote = withQuote($aryHeader["strnote"]); //33:備考
    $v_lngprintcount = 0; //34:印刷回数
    $v_bytinvalidflag = "FALSE"; //35:無効フラグ

    // 登録クエリ作成
    $aryInsert = [];
    $aryInsert[] = "INSERT  ";
    $aryInsert[] = "INTO m_slip(  ";
    $aryInsert[] = "  lngslipno "; //1:納品伝票番号
    $aryInsert[] = "  , lngrevisionno "; //2:リビジョン番号
    $aryInsert[] = "  , strslipcode "; //3:納品伝票コード
    $aryInsert[] = "  , lngsalesno "; //4:売上番号
    $aryInsert[] = "  , lngcustomercode "; //5:顧客コード
    $aryInsert[] = "  , strcustomercompanyname "; //6:顧客社名
    $aryInsert[] = "  , strcustomername "; //7:顧客名
    $aryInsert[] = "  , strcustomeraddress1 "; //8:顧客住所1
    $aryInsert[] = "  , strcustomeraddress2 "; //9:顧客住所2
    $aryInsert[] = "  , strcustomeraddress3 "; //10:顧客住所3
    $aryInsert[] = "  , strcustomeraddress4 "; //11:顧客住所4
    $aryInsert[] = "  , strcustomerphoneno "; //12:顧客電話番号
    $aryInsert[] = "  , strcustomerfaxno "; //13:顧客FAX番号
    $aryInsert[] = "  , strcustomerusername "; //14:顧客担当者名
    $aryInsert[] = "  , strshippercode "; //15:仕入先コード（出荷者）
    $aryInsert[] = "  , dtmdeliverydate "; //16:納品日
    $aryInsert[] = "  , lngdeliveryplacecode "; //17:納品場所コード
    $aryInsert[] = "  , strdeliveryplacename "; //18:納品場所名
    $aryInsert[] = "  , strdeliveryplaceusername "; //19:納品場所担当者名
    $aryInsert[] = "  , lngpaymentmethodcode "; //20:支払方法コード
    $aryInsert[] = "  , dtmpaymentlimit "; //21:支払期限
    $aryInsert[] = "  , lngtaxclasscode "; //22:課税区分コード
    $aryInsert[] = "  , strtaxclassname "; //23:課税区分
    $aryInsert[] = "  , curtax "; //24:消費税率
    $aryInsert[] = "  , lngusercode "; //25:担当者コード
    $aryInsert[] = "  , strusername "; //26:担当者名
    $aryInsert[] = "  , curtotalprice "; //27:合計金額
    $aryInsert[] = "  , lngmonetaryunitcode "; //28:通貨単位コード
    $aryInsert[] = "  , strmonetaryunitsign "; //29:通貨単位
    $aryInsert[] = "  , dtminsertdate "; //30:作成日
    $aryInsert[] = "  , lnginsertusercode "; //31:入力者コード
    $aryInsert[] = "  , strinsertusername "; //32:入力者名
    $aryInsert[] = "  , strnote "; //33:備考
    $aryInsert[] = "  , lngprintcount "; //34:印刷回数
    $aryInsert[] = "  , bytinvalidflag "; //35:無効フラグ
    $aryInsert[] = ")  ";
    $aryInsert[] = "VALUES (  ";
    $aryInsert[] = "  " . $v_lngslipno; //1:納品伝票番号
    $aryInsert[] = " ," . $v_lngrevisionno; //2:リビジョン番号
    $aryInsert[] = " ," . $v_strslipcode; //3:納品伝票コード
    $aryInsert[] = " ," . $v_lngsalesno; //4:売上番号
    $aryInsert[] = " ," . $v_lngcustomercode; //5:顧客コード
    $aryInsert[] = " ," . $v_strcustomercompanyname; //6:顧客社名
    $aryInsert[] = " ," . $v_strcustomername; //7:顧客名
    $aryInsert[] = " ," . $v_strcustomeraddress1; //8:顧客住所1
    $aryInsert[] = " ," . $v_strcustomeraddress2; //9:顧客住所2
    $aryInsert[] = " ," . $v_strcustomeraddress3; //10:顧客住所3
    $aryInsert[] = " ," . $v_strcustomeraddress4; //11:顧客住所4
    $aryInsert[] = " ," . $v_strcustomerphoneno; //12:顧客電話番号
    $aryInsert[] = " ," . $v_strcustomerfaxno; //13:顧客FAX番号
    $aryInsert[] = " ," . $v_strcustomerusername; //14:顧客担当者名
    $aryInsert[] = " ," . $v_strshippercode; //15:仕入先コード（出荷者）
    $aryInsert[] = " ," . $v_dtmdeliverydate; //16:納品日
    $aryInsert[] = " ," . $v_lngdeliveryplacecode; //17:納品場所コード
    $aryInsert[] = " ," . $v_strdeliveryplacename; //18:納品場所名
    $aryInsert[] = " ," . $v_strdeliveryplaceusername; //19:納品場所担当者名
    $aryInsert[] = " ," . $v_lngpaymentmethodcode; //20:支払方法コード
    $aryInsert[] = " ," . $v_dtmpaymentlimit; //21:支払期限
    $aryInsert[] = " ," . $v_lngtaxclasscode; //22:課税区分コード
    $aryInsert[] = " ," . $v_strtaxclassname; //23:課税区分
    $aryInsert[] = " ," . $v_curtax; //24:消費税率
    $aryInsert[] = " ," . $v_lngusercode; //25:担当者コード
    $aryInsert[] = " ," . $v_strusername; //26:担当者名
    $aryInsert[] = " ," . $v_curtotalprice; //27:合計金額
    $aryInsert[] = " ," . $v_lngmonetaryunitcode; //28:通貨単位コード
    $aryInsert[] = " ," . $v_strmonetaryunitsign; //29:通貨単位
    $aryInsert[] = " ," . $v_dtminsertdate; //30:作成日
    $aryInsert[] = " ," . $v_lnginsertusercode; //31:入力者コード
    $aryInsert[] = " ," . $v_strinsertusername; //32:入力者名
    $aryInsert[] = " ," . $v_strnote; //33:備考
    $aryInsert[] = " ," . $v_lngprintcount; //34:印刷回数
    $aryInsert[] = " ," . $v_bytinvalidflag; //35:無効フラグ
    $aryInsert[] = ") ";
    $strQuery = "";
    $strQuery .= implode("\n", $aryInsert);

    // 登録実行
    if (!$lngResultID = $objDB->execute($strQuery)) {
        fncOutputError(9051, DEF_ERROR, "納品伝票マスタ登録失敗。", true, "", $objDB);
        // 失敗
        return false;
    }
    $objDB->freeResult($lngResultID);

    // 成功
    return true;
}

// 納品伝票明細登録
function fncRegisterSlipDetail($itemMinIndex, $itemMaxIndex, $lngSlipNo, $lngRevisionNo,
    $aryHeader, $aryDetail, $objDB, $objAuth) {
    for ($i = $itemMinIndex; $i <= $itemMaxIndex; $i++) {
        $d = $aryDetail[$i];

        // 登録データのセット
        $v_lngslipno = $lngSlipNo; //1:納品伝票番号
        $v_lngslipdetailno = $d["rownumber"]; //2:納品伝票明細番号
        $v_lngrevisionno = $lngRevisionNo; //3:リビジョン番号
        $v_strcustomersalescode = withQuote($d["strcustomerreceivecode"]); //4:顧客受注番号
        $v_lngsalesclasscode = $d["lngsalesclasscode"]; //5:売上区分コード
        $v_strsalesclassname = withQuote($d["strsalesclassname"]); //6:売上区分名
        $v_strgoodscode = withQuote($d["strgoodscode"]); //7:顧客品番
        $v_strproductcode = withQuote(mb_substr($d["strproductcode"], 0, 5)); //8:製品コード
        $v_strrevisecode = withQuote($d["strrevisecode"]); //9:再販コード
        $v_strproductname = withQuote($d["strproductname"]); //10:製品名
        $v_strproductenglishname = withQuote($d["strproductenglishname"]); //11:製品名（英語）
        $v_curproductprice = $d["curproductprice"]; //12:単価
        $v_lngquantity = $d["lngunitquantity"]; //13:入数
        $v_lngproductquantity = $d["lngproductquantity"]; //14:数量
        $v_lngproductunitcode = $d["lngproductunitcode"]; //15:製品単位コード
        $v_strproductunitname = withQuote($d["strproductunitname"]); //16:製品単位名
        $v_cursubtotalprice = $d["cursubtotalprice"]; //17:小計
        $v_strnote = withQuote($d["strnote"]); //18:明細備考
        $v_lngreceiveno = $d["lngreceiveno"]; //19:受注番号
        $v_lngreceivedetailno = $d["lngreceivedetailno"]; //20:受注明細番号
        $v_lngreceiverevisionno = $d["lngreceiverevisionno"]; //21:受注リビジョン番号
        $v_lngsortkey = $d["rownumber"]; //22:表示用ソートキー

        // 登録クエリ作成
        $aryInsert = [];
        $aryInsert[] = "INSERT  ";
        $aryInsert[] = "INTO t_slipdetail(  ";
        $aryInsert[] = "  lngslipno "; //1:納品伝票番号
        $aryInsert[] = "  , lngslipdetailno "; //2:納品伝票明細番号
        $aryInsert[] = "  , lngrevisionno "; //3:リビジョン番号
        $aryInsert[] = "  , strcustomersalescode "; //4:顧客受注番号
        $aryInsert[] = "  , lngsalesclasscode "; //5:売上区分コード
        $aryInsert[] = "  , strsalesclassname "; //6:売上区分名
        $aryInsert[] = "  , strgoodscode "; //7:顧客品番
        $aryInsert[] = "  , strproductcode "; //8:製品コード
        $aryInsert[] = "  , strrevisecode "; //9:再販コード
        $aryInsert[] = "  , strproductname "; //10:製品名
        $aryInsert[] = "  , strproductenglishname "; //11:製品名（英語）
        $aryInsert[] = "  , curproductprice "; //12:単価
        $aryInsert[] = "  , lngquantity "; //13:入数
        $aryInsert[] = "  , lngproductquantity "; //14:数量
        $aryInsert[] = "  , lngproductunitcode "; //15:製品単位コード
        $aryInsert[] = "  , strproductunitname "; //16:製品単位名
        $aryInsert[] = "  , cursubtotalprice "; //17:小計
        $aryInsert[] = "  , strnote "; //18:明細備考
        $aryInsert[] = "  , lngreceiveno "; //19:受注番号
        $aryInsert[] = "  , lngreceivedetailno "; //20:受注明細番号
        $aryInsert[] = "  , lngreceiverevisionno "; //21:受注リビジョン番号
        $aryInsert[] = "  , lngsortkey "; //22:表示用ソートキー
        $aryInsert[] = ")  ";
        $aryInsert[] = "VALUES (  ";
        $aryInsert[] = "  " . $v_lngslipno; //1:納品伝票番号
        $aryInsert[] = " ," . $v_lngslipdetailno; //2:納品伝票明細番号
        $aryInsert[] = " ," . $v_lngrevisionno; //3:リビジョン番号
        $aryInsert[] = " ," . $v_strcustomersalescode; //4:顧客受注番号
        $aryInsert[] = " ," . $v_lngsalesclasscode; //5:売上区分コード
        $aryInsert[] = " ," . $v_strsalesclassname; //6:売上区分名
        $aryInsert[] = " ," . $v_strgoodscode; //7:顧客品番
        $aryInsert[] = " ," . $v_strproductcode; //8:製品コード
        $aryInsert[] = " ," . $v_strrevisecode; //9:再販コード
        $aryInsert[] = " ," . $v_strproductname; //10:製品名
        $aryInsert[] = " ," . $v_strproductenglishname; //11:製品名（英語）
        $aryInsert[] = " ," . $v_curproductprice; //12:単価
        $aryInsert[] = " ," . $v_lngquantity; //13:入数
        $aryInsert[] = " ," . $v_lngproductquantity; //14:数量
        $aryInsert[] = " ," . $v_lngproductunitcode; //15:製品単位コード
        $aryInsert[] = " ," . $v_strproductunitname; //16:製品単位名
        $aryInsert[] = " ," . $v_cursubtotalprice; //17:小計
        $aryInsert[] = " ," . $v_strnote; //18:明細備考
        $aryInsert[] = " ," . $v_lngreceiveno; //19:受注番号
        $aryInsert[] = " ," . $v_lngreceivedetailno; //20:受注明細番号
        $aryInsert[] = " ," . $v_lngreceiverevisionno; //21:受注リビジョン番号
        $aryInsert[] = " ," . $v_lngsortkey; //22:表示用ソートキー
        $aryInsert[] = ") ";
        $strQuery = "";
        $strQuery .= implode("\n", $aryInsert);

        // 登録実行
        if (!$lngResultID = $objDB->execute($strQuery)) {
            fncOutputError(9051, DEF_ERROR, "納品伝票明細登録登録失敗。", true, "", $objDB);
            // 失敗
            return false;
        }
        $objDB->freeResult($lngResultID);
    }

    // 成功
    return true;
}

// 文字コード EUC-JP -> UTF-8 変換用ヘルパ関数
function fncToUtf8($eucjpText)
{
    return mb_convert_encoding($eucjpText, 'UTF-8', 'EUC-JP');
}

// 文字コード UTF-8 -> EUC-JP 変換用ヘルパ関数
function fncToEucjp($utf8Text)
{
    return mb_convert_encoding($utf8Text, 'EUC-JP', 'UTF-8');
}

// 指定アドレスのセルに値をセットするヘルパ関数
function setCellValue($xlWorkSheet, $address, $value)
{
    $value = fncToUtf8($value);
    $xlWorkSheet->GetCell($address)->SetValue($value);
}

// 行を変えながらセルに値をセットするヘルパ関数
function setCellDetailValue($xlWorkSheet, $columnAddress, $rowNumber, $value)
{
    $address = $columnAddress . $rowNumber;
    $value = fncToUtf8($value);
    $xlWorkSheet->GetCell($address)->SetValue($value);
}

function fncConvertArrayHeaderToEucjp($aryHeader)
{
    $aryHeader["strdrafteruserdisplaycode"] = fncToEucjp($aryHeader["strdrafteruserdisplaycode"]);
    $aryHeader["strdrafteruserdisplayname"] = fncToEucjp($aryHeader["strdrafteruserdisplayname"]);
    $aryHeader["strcompanydisplaycode"] = fncToEucjp($aryHeader["strcompanydisplaycode"]);
    $aryHeader["strcompanydisplayname"] = fncToEucjp($aryHeader["strcompanydisplayname"]);
    $aryHeader["strcustomerusername"] = fncToEucjp($aryHeader["strcustomerusername"]);
    $aryHeader["dtmdeliverydate"] = fncToEucjp($aryHeader["dtmdeliverydate"]);
    $aryHeader["strdeliveryplacecompanydisplaycode"] = fncToEucjp($aryHeader["strdeliveryplacecompanydisplaycode"]);
    $aryHeader["strdeliveryplacename"] = fncToEucjp($aryHeader["strdeliveryplacename"]);
    $aryHeader["strdeliveryplaceusername"] = fncToEucjp($aryHeader["strdeliveryplaceusername"]);
    $aryHeader["strnote"] = fncToEucjp($aryHeader["strnote"]);
    $aryHeader["lngtaxclasscode"] = fncToEucjp($aryHeader["lngtaxclasscode"]);
    $aryHeader["strtaxclassname"] = fncToEucjp($aryHeader["strtaxclassname"]);
    $aryHeader["lngtaxcode"] = fncToEucjp($aryHeader["lngtaxcode"]);
    $aryHeader["curtax"] = fncToEucjp($aryHeader["curtax"]);
    $aryHeader["strtaxamount"] = fncToEucjp($aryHeader["strtaxamount"]);
    $aryHeader["dtmpaymentlimit"] = fncToEucjp($aryHeader["dtmpaymentlimit"]);
    $aryHeader["lngpaymentmethodcode"] = fncToEucjp($aryHeader["lngpaymentmethodcode"]);
    $aryHeader["curtotalprice"] = fncToEucjp($aryHeader["curtotalprice"]);

    return $aryHeader;
}

function fncConvertArrayDetailToEucjp($aryDetail)
{
    for ($i = 0; $i < count($aryDetail); $i++) {
        $d = &$aryDetail[$i];

        $d["rownumber"] = fncToEucjp($d["rownumber"]);
        $d["strcustomerreceivecode"] = fncToEucjp($d["strcustomerreceivecode"]);
        $d["strreceivecode"] = fncToEucjp($d["strreceivecode"]);
        $d["strgoodscode"] = fncToEucjp($d["strgoodscode"]);
        $d["strproductcode"] = fncToEucjp($d["strproductcode"]);
        $d["strproductname"] = fncToEucjp($d["strproductname"]);
        $d["strproductenglishname"] = fncToEucjp($d["strproductenglishname"]);
        $d["strsalesdeptname"] = fncToEucjp($d["strsalesdeptname"]);
        $d["strsalesclassname"] = fncToEucjp($d["strsalesclassname"]);
        $d["dtmdeliverydate"] = fncToEucjp($d["dtmdeliverydate"]);
        $d["lngunitquantity"] = fncToEucjp($d["lngunitquantity"]);
        $d["curproductprice"] = fncToEucjp($d["curproductprice"]);
        $d["strproductunitname"] = fncToEucjp($d["strproductunitname"]);
        $d["lngproductquantity"] = fncToEucjp($d["lngproductquantity"]);
        $d["cursubtotalprice"] = fncToEucjp($d["cursubtotalprice"]);
        $d["lngreceiveno"] = fncToEucjp($d["lngreceiveno"]);
        $d["lngreceivedetailno"] = fncToEucjp($d["lngreceivedetailno"]);
        $d["lngreceiverevisionno"] = fncToEucjp($d["lngreceiverevisionno"]);
        $d["strrevisecode"] = fncToEucjp($d["strrevisecode"]);
        $d["lngsalesclasscode"] = fncToEucjp($d["lngsalesclasscode"]);
        $d["lngproductunitcode"] = fncToEucjp($d["lngproductunitcode"]);
        $d["strnote"] = fncToEucjp($d["strnote"]);
        $d["lngmonetaryunitcode"] = fncToEucjp($d["lngmonetaryunitcode"]);
        $d["lngmonetaryratecode"] = fncToEucjp($d["lngmonetaryratecode"]);
        $d["strmonetaryunitsign"] = fncToEucjp($d["strmonetaryunitsign"]);
    }

    return $aryDetail;
}

/**
 * テンプレートから帳票イメージを生成する
 * @param  string  $strMode     動作モード："html"->プレビュー用HTML生成、"download"->ダウンロード用Writer生成
 * @param  array   $aryHeader   ヘッダ部データ
 * @param  array   $aryDetail   明細部データ
 * @param  object  $objDB       データベース操作クラス
 * @return array   $$aryGenerateResult  生成結果。動作モードにより格納される値が異なる
 */
function fncGenerateReportImage($strMode, $aryHeader, $aryDetail,
    $lngSlipNo, $lngRevisionNo, $strSlipCode, $lngSalesNo, $dtmInsertDate,
    $objDB) {
    // 生成結果（戻り値）
    $aryGenerateResult = array();

    // DBG:一時コメントアウト対象
    // --------------------------------------------
    //  データ取得
    // --------------------------------------------
    // 顧客の会社コードを取得
    $lngCustomerCompanyCode = fncGetNumericCompanyCode($aryHeader["strcompanydisplaycode"], $objDB);
    // 顧客の会社コードに紐づく納品伝票種別を取得
    $aryReport = fncGetSlipKindByCompanyCode($lngCustomerCompanyCode, $objDB);
    // 顧客の会社コードに紐づく会社情報を取得
    $aryCustomerCompany = fncGetCompanyInfoByCompanyCode($lngCustomerCompanyCode, $objDB);
    // 顧客の国コードを取得
    $lngCustomerCountryCode = fncGetCountryCode($aryHeader["strcompanydisplaycode"], $objDB);
    // 顧客社名の取得
    $strCustomerCompanyName = fncGetCustomerCompanyName($lngCustomerCountryCode, $aryCustomerCompany);
    // 顧客名の取得
    $strCustomerName = fncGetCustomerName($aryCustomerCompany);
    // 納品先の会社コードの取得
    $lngDeliveryPlaceCode = fncGetNumericCompanyCode($aryHeader["strdeliveryplacecompanydisplaycode"], $objDB);

    // 帳票種別の取得
    $lngSlipKindCode = $aryReport["lngslipkindcode"];
    // 顧客に紐づく帳票1ページあたりの最大明細数を取得する
    $maxItemPerPage = intval($aryReport["lngmaxline"]);
    // 登録する全明細の数
    $totalItemCount = count($aryDetail);
    // 最大ページ数の計算
    $maxPageCount = ceil($totalItemCount / $maxItemPerPage);
    // 帳票種別ごとに異なる情報の取得
    if ($lngSlipKindCode == 1) {
        //1:指定・専用
        $templatFileName = "納品書temple_B社_連絡書付.xlsx";
        $activeSheetName = fncToUtf8("納品書");
    } else if ($lngSlipKindCode == 2) {
        //2:市販
        $templatFileName = "納品書temple_市販_連絡書付.xlsx";
        $activeSheetName = fncToUtf8("納品書");
    } else if ($lngSlipKindCode == 3) {
        //3:DEBIT NOTE
        $templatFileName = "DEBIT NOTE.xlsx";
        $activeSheetName = fncToUtf8("DEBIT NOTE");
    } else {
        throw new Exception("帳票テンプレートを特定できません。lngSlipKindCode=" . $lngSlipKindCode);
    }

    // --------------------------------------------
    //  スプレッドシート初期化
    // --------------------------------------------
    // 日本語対応
    ini_set('default_charset', 'UTF-8');
    // 帳票テンプレートのフルパス
    $spreadSheetFilePath = fncToUtf8(REPORT_TMPDIR . $templatFileName);
    // データを設定するシート名
    $dataSheetName = fncToUtf8("データ設定用");

    // --------------------------------------------
    //  動作モードによる処理分岐
    // --------------------------------------------
    if ($strMode == "download") {
        // --------------------------------------------
        //  ダウンロード用Writer生成
        // --------------------------------------------
        //スプレッドシート生成
        $xlSpreadSheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($spreadSheetFilePath);
        //ワークシート生成
        $xlWorkSheet = $xlSpreadSheet->GetSheetByName($dataSheetName);
        //XlsxWriter生成
        $xlWriter = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($xlSpreadSheet);

        //1ページ分の明細のみ存在するという前提
        $itemMinIndex = 0;
        $itemMaxIndex = count($aryDetail) - 1;

        //ワークシートに納品書データを設定
        fncSetSlipDataToWorkSheet(
            $xlWorkSheet,
            $itemMinIndex, $itemMaxIndex, $strCustomerCompanyName,
            $strCustomerName, $aryCustomerCompany, $lngDeliveryPlaceCode,
            $aryHeader, $aryDetail,
            $lngSlipNo, $lngRevisionNo, $strSlipCode, $lngSalesNo, $dtmInsertDate);

        //アクティブシート変更
        $xlSpreadSheet->setActiveSheetIndexByName($activeSheetName);

        //Writerだけ戻してみる
        $aryGenerateResult["XlsxWriter"] = $xlWriter;
    } else if ($strMode == "html") {
        if ($lngSlipKindCode == 1 || $lngSlipKindCode == 2) {
            // --------------------------------------------
            //  プレビューHTML生成
            // --------------------------------------------
            // プレビュー用CSS
            $previewStyle = "";
            // プレビューHTML
            $previewData = "";

            // ページ単位でのHTML生成
            for ($page = 1; $page <= $maxPageCount; $page++) {

                // 確実に初期化するため1ページ毎にスプレッドシートを読み込みなおす
                $xlSpreadSheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($spreadSheetFilePath);
                $xlWorkSheet = $xlSpreadSheet->GetSheetByName($dataSheetName);
                $xlWriter = new \PhpOffice\PhpSpreadsheet\Writer\Html($xlSpreadSheet);

                if (strlen($previewStyle) == 0) {
                    // CSSは全体で1つあればよい
                    $previewStyle = $xlWriter->generateStyles(true);
                }

                // 現在のページ数と1ページあたりの明細数から
                // 出力する明細のインデックスの最小値と最大値を求める
                $itemMinIndex = ($page - 1) * $maxItemPerPage;
                $itemMaxIndex = $page * $maxItemPerPage - 1;
                if ($itemMaxIndex > $totalItemCount - 1) {
                    $itemMaxIndex = $totalItemCount - 1;
                }

                // 1ページ分のプレビューHTML生成
                fncSetSlipDataToWorkSheet(
                    $xlWorkSheet,
                    $itemMinIndex, $itemMaxIndex, $strCustomerCompanyName,
                    $strCustomerName, $aryCustomerCompany, $lngDeliveryPlaceCode,
                    $aryHeader, $aryDetail,
                    $lngSlipNo, $lngRevisionNo, $strSlipCode, $lngSalesNo, $dtmInsertDate);

                // 全体に追加
                $pageHtml = $xlWriter->generateSheetData();
                $previewData .= $pageHtml;

                // 日本語対応
                ini_set('default_charset', 'EUC-JP');
                // 最後にUTF-8からEUC-JPに変換した結果をセット
                $aryGenerateResult["PreviewStyle"] = fncToEucjp($previewStyle);
                $aryGenerateResult["PreviewData"] = fncToEucjp($previewData);

            }
        } else if ($lngSlipKindCode == 3) {
                // // 日本語対応
                ini_set('default_charset', 'EUC-JP');
            $strTemplateHeaderPath = "list/result/slip_debit_header.html";
            $strTemplatePath = "list/result/slip_debit.html";
            // $strTemplateFooterPath = "list/result/slip_debit_footer.html";

            $aryParts["strslipcode"] = $strSlipCode; //納品書NO.
            $aryParts["strcustomername"] = $strCustomerName; //7:顧客名
            $aryParts["strcustomeraddress1"] = $aryCustomerCompany["straddress1"]; //8:顧客住所1
            $aryParts["strcustomeraddress2"] = $aryCustomerCompany["straddress2"]; //9:顧客住所2
            $aryParts["strcustomeraddress3"] = $aryCustomerCompany["straddress3"]; //10:顧客住所3
            $aryParts["strcustomeraddress4"] = $aryCustomerCompany["straddress4"]; //11:顧客住所4
            $aryParts["dtmdeliverydate"] = $aryHeader["dtmdeliverydate"];            
            $lngmonetaryunitcode = $aryDetail[0]["lngmonetaryunitcode"];
            $strmonetaryunitsign = $aryDetail[0]["strmonetaryunitsign"];
            // 顧客電話番号
            $aryParts["strcustomertel"] = "Tel:" . $aryCustomerCompany["strtel1"] . " " . $aryCustomerCompany["strtel2"];

            // 顧客FAX番号
            $aryParts["strcustomerfax"] = "Fax.:" . $aryCustomerCompany["strfax1"] . " " . $aryCustomerCompany["strfax2"];

            // 合計金額
            $curTotalPrice = ($lngmonetaryunitcode == 1 ? "&yen; " : $strmonetaryunitsign) . " " . number_format($aryHeader["curtotalprice"], 2, '.', ',');

            $aryParts["curtotalprice"] = $curTotalPrice;            
            $aryParts["strpaymentmethodname"] = $aryHeader["strpaymentmethodname"];
            $aryParts["nameofbank"] = $aryHeader["lngpaymentmethodcode"] == 1 ? "MUFG BANK, LTD." : "";
            $aryParts["nameofbranch"] = $aryHeader["lngpaymentmethodcode"] == 1 ? "ASAKUSA BRANCH" : "";
            $aryParts["addressofbank1"] = $aryHeader["lngpaymentmethodcode"] == 1 ? "4-2, ASAKUSA 1-CHOME, " : "";
            $aryParts["addressofbank2"] = $aryHeader["lngpaymentmethodcode"] == 1 ? " TAITO-KU, TOKYO 111-0032, JAPAN" : "";
            $aryParts["swiftcode"] = $aryHeader["lngpaymentmethodcode"] == 1 ? "BOTKJPJT" : "";
            $aryParts["accountname"] = $aryHeader["lngpaymentmethodcode"] == 1 ? "KUWAGATA CO.,LTD." : "";
            $aryParts["accountno"] = $aryHeader["lngpaymentmethodcode"] == 1 ? "1063143" : "";

            // HTML出力
            $objTemplateHeader = new clsTemplate();
            $objTemplateHeader->getTemplate($strTemplateHeaderPath);
            $strTemplateHeader = $objTemplateHeader->strTemplate;

            $objTemplate = new clsTemplate();
            $objTemplate->getTemplate($strTemplatePath);
            $strTemplate = $objTemplate->strTemplate;

            $objTemplate->strTemplate = $strTemplate;

            for ($i = 0; $i < count($aryDetail); $i++) {
                $aryParts["strcustomersalescode". ($i)] = $aryDetail[$i]["strcustomerreceivecode"];
                $aryParts["strproductenglishname". ($i)] = $aryDetail[$i]["strproductenglishname"];
                $aryParts["lngproductquantity". ($i)] = number_format($aryDetail[$i]["lngproductquantity"]);
                $aryParts["curproductprice". ($i)] = number_format($aryDetail[$i]["curproductprice"], 2, '.', ',');
                $aryParts["cursubtotalprice". ($i)] = number_format($aryDetail[$i]["cursubtotalprice"], 2, '.', ',');
                $aryParts["strsalesclassname". ($i)] = $aryDetail[$i]["strsalesclassname"];
                $aryParts["strnote". ($i)] = $aryDetail[$i]["strnote"];

                // 顧客受注番号
                if ($aryParts["strcustomersalescode". ($i)] != "") {
                    $aryParts["strcustomersalescode". ($i)] = "(PO No:" . $aryParts["strcustomersalescode". ($i)] . ")";
                }
            }

            // 置き換え
            $objTemplate->replace($aryParts);
            $objTemplate->complete();
            $strBodyHtml = $objTemplate->strTemplate;
            $aryGenerateResult["PreviewStyle"] = fncToEucjp($strTemplateHeader);
            $aryGenerateResult["PreviewData"] = fncToEucjp($strBodyHtml);
        }
        // $aryGenerateResult["PreviewData"] = $previewData;
    } else {
        // 不明なモード
        throw new Exception("不明なモードが指定されました。strMode=" . $strMode);
    }

    // --------------------------------------------
    //  生成結果を返す
    // --------------------------------------------
    return $aryGenerateResult;
}

// 納品書データを帳票テンプレートのワークシートに設定
function fncSetSlipDataToWorkSheet(
    $xlWorkSheet,
    $itemMinIndex, $itemMaxIndex, $strCustomerCompanyName,
    $strCustomerName, $aryCustomerCompany, $lngDeliveryPlaceCode,
    $aryHeader, $aryDetail,
    $lngSlipNo, $lngRevisionNo, $strSlipCode, $lngSalesNo, $dtmInsertDate) {
    // 【補足】
    // lngSlipNo,lngRevisionNo,strSlipCode,lngSalesNo,dtmInsertDateはデータ登録するまで確定しないため
    // プレビュー表示時は空にせざるを得ない。ダウンロード時はデータ登録済みなため出力可能。

    // ------------------------------------------
    //   マスタデータのセット
    // ------------------------------------------
    // 値の設定
    $v_lngslipno = is_null($lngSlipNo) ? "" : $lngSlipNo; //1:納品伝票番号
    $v_lngrevisionno = is_null($lngRevisionNo) ? "" : $lngRevisionNo; //2:リビジョン番号
    $v_strslipcode = is_null($strSlipCode) ? "" : $strSlipCode; //3:納品伝票コード
    $v_lngsalesno = is_null($lngSalesNo) ? "" : $lngSalesNo; //4:売上番号
    $v_strcustomercode = $aryCustomerCompany["lngcompanycode"]; //5:顧客コード
    $v_strcustomercompanyname = $strCustomerCompanyName; //6:顧客社名
    $v_strcustomername = $strCustomerName; //7:顧客名
    $v_strcustomeraddress1 = $aryCustomerCompany["straddress1"]; //8:顧客住所1
    $v_strcustomeraddress2 = $aryCustomerCompany["straddress2"]; //9:顧客住所2
    $v_strcustomeraddress3 = $aryCustomerCompany["straddress3"]; //10:顧客住所3
    $v_strcustomeraddress4 = $aryCustomerCompany["straddress4"]; //11:顧客住所4
    $v_strcustomerphoneno = $aryCustomerCompany["strtel1"]; //12:顧客電話番号
    $v_strcustomerfaxno = $aryCustomerCompany["strfax1"]; //13:顧客FAX番号
    $v_strcustomerusername = $aryHeader["strcustomerusername"]; //14:顧客担当者名
    $v_dtmdeliverydate = $aryHeader["dtmdeliverydate"]; //15:納品日
    $v_lngdeliveryplacecode = $lngDeliveryPlaceCode; //16:納品場所コード
    $v_strdeliveryplacename = $aryHeader["strdeliveryplacename"]; //17:納品場所名
    $v_strdeliveryplaceusername = $aryHeader["strdeliveryplaceusername"]; //18:納品場所担当者名
    $v_strusercode = $aryHeader["strdrafteruserdisplaycode"]; //19:担当者コード
    $v_strusername = $aryHeader["strdrafteruserdisplayname"]; //20:担当者名
    $v_curtotalprice = $aryHeader["curtotalprice"]; //21:合計金額
    $v_lngmonetaryunitcode = $aryDetail[0]["lngmonetaryunitcode"]; //22:通貨単位コード
    $v_strmonetaryunitsign = $aryDetail[0]["strmonetaryunitsign"]; //23:通貨単位
    $v_lngtaxclasscode = $aryHeader["lngtaxclasscode"]; //24:課税区分コード
    $v_strtaxclassname = $aryHeader["strtaxclassname"]; //25:課税区分
    $v_curtax = $aryHeader["curtax"]; //26:消費税率
    $v_lngpaymentmethodcode = $aryHeader["lngpaymentmethodcode"]; //27:支払方法コード
    $v_dtmpaymentlimit = $aryHeader["dtmpaymentlimit"]; //28:支払期限
    $v_dtminsertdate = is_null($dtmInsertDate) ? "" : $dtmInsertDate; //29:作成日
    $v_strnote = $aryHeader["strnote"]; //30:備考
    $v_strshippercode = $aryCustomerCompany["strstockcompanycode"]; //31:仕入先コード（出荷者）

    // セルに値をセット
    setCellValue($xlWorkSheet, "B3", mb_convert_encoding($v_lngslipno, 'euc-jp', 'UTF8')); //1:納品伝票番号
    setCellValue($xlWorkSheet, "C3", mb_convert_encoding($v_lngrevisionno, 'euc-jp', 'UTF8')); //2:リビジョン番号
    setCellValue($xlWorkSheet, "D3", mb_convert_encoding($v_strslipcode, 'euc-jp', 'UTF8')); //3:納品伝票コード
    setCellValue($xlWorkSheet, "E3", mb_convert_encoding($v_lngsalesno, 'euc-jp', 'UTF8')); //4:売上番号
    setCellValue($xlWorkSheet, "F3", mb_convert_encoding($v_strcustomercode, 'euc-jp', 'UTF8')); //5:顧客コード
    setCellValue($xlWorkSheet, "G3", $v_strcustomercompanyname); //6:顧客社名
    setCellValue($xlWorkSheet, "H3", $v_strcustomername); //7:顧客名
    setCellValue($xlWorkSheet, "I3", mb_convert_encoding($v_strcustomeraddress1, 'euc-jp', 'UTF8')); //8:顧客住所1
    setCellValue($xlWorkSheet, "J3", mb_convert_encoding($v_strcustomeraddress2, 'euc-jp', 'UTF8')); //9:顧客住所2
    setCellValue($xlWorkSheet, "K3", mb_convert_encoding($v_strcustomeraddress3, 'euc-jp', 'UTF8')); //10:顧客住所3
    setCellValue($xlWorkSheet, "L3", mb_convert_encoding($v_strcustomeraddress4, 'euc-jp', 'UTF8')); //11:顧客住所4
    setCellValue($xlWorkSheet, "M3", mb_convert_encoding($v_strcustomerphoneno, 'euc-jp', 'UTF8')); //12:顧客電話番号
    setCellValue($xlWorkSheet, "N3", mb_convert_encoding($v_strcustomerfaxno, 'euc-jp', 'UTF8')); //13:顧客FAX番号
    setCellValue($xlWorkSheet, "O3", mb_convert_encoding($v_strcustomerusername, 'euc-jp', 'UTF8')); //14:顧客担当者名
    setCellValue($xlWorkSheet, "P3", mb_convert_encoding($v_dtmdeliverydate, 'euc-jp', 'UTF8')); //15:納品日
    setCellValue($xlWorkSheet, "Q3", mb_convert_encoding($v_lngdeliveryplacecode, 'euc-jp', 'UTF8')); //16:納品場所コード
    setCellValue($xlWorkSheet, "R3", mb_convert_encoding($v_strdeliveryplacename, 'euc-jp', 'UTF8')); //17:納品場所名
    setCellValue($xlWorkSheet, "S3", mb_convert_encoding($v_strdeliveryplaceusername, 'euc-jp', 'UTF8')); //18:納品場所担当者名
    setCellValue($xlWorkSheet, "T3", mb_convert_encoding($v_strusercode, 'euc-jp', 'UTF8')); //19:担当者コード
    setCellValue($xlWorkSheet, "U3", mb_convert_encoding($v_strusername, 'euc-jp', 'UTF8')); //20:担当者名
    setCellValue($xlWorkSheet, "V3", $v_curtotalprice); //21:合計金額
    setCellValue($xlWorkSheet, "W3", mb_convert_encoding($v_lngmonetaryunitcode, 'euc-jp', 'UTF8')); //22:通貨単位コード
    setCellValue($xlWorkSheet, "X3", mb_convert_encoding($v_strmonetaryunitsign, 'euc-jp', 'UTF8')); //23:通貨単位
    setCellValue($xlWorkSheet, "Y3", mb_convert_encoding($v_lngtaxclasscode, 'euc-jp', 'UTF8')); //24:課税区分コード
    setCellValue($xlWorkSheet, "Z3", mb_convert_encoding($v_strtaxclassname, 'euc-jp', 'UTF8')); //25:課税区分
    setCellValue($xlWorkSheet, "AA3", mb_convert_encoding($v_curtax, 'euc-jp', 'UTF8')); //26:消費税率
    setCellValue($xlWorkSheet, "AB3", mb_convert_encoding($v_lngpaymentmethodcode, 'euc-jp', 'UTF8')); //27:支払方法コード
    setCellValue($xlWorkSheet, "AC3", mb_convert_encoding($v_dtmpaymentlimit, 'euc-jp', 'UTF8')); //28:支払期限
    setCellValue($xlWorkSheet, "AD3", mb_convert_encoding($v_dtminsertdate, 'euc-jp', 'UTF8')); //29:作成日
    setCellValue($xlWorkSheet, "AE3", mb_convert_encoding($v_strnote, 'euc-jp', 'UTF8')); //30:備考
    setCellValue($xlWorkSheet, "AF3", mb_convert_encoding($v_strshippercode, 'euc-jp', 'UTF8')); //31:仕入先コード（出荷者）

    // ------------------------------------------
    //   明細データのセット
    // ------------------------------------------
    // 明細データをセットする開始行
    $startRowIndex = 6;
    for ($i = $itemMinIndex; $i <= $itemMaxIndex; $i++) {
        $d = $aryDetail[$i];

        // 値の設定
        $v_lngslipno = is_null($lngSlipNo) ? "" : $lngSlipNo; //1:納品伝票番号
        $v_lngslipdetailno = $d["rownumber"]; //2:納品伝票明細番号
        $v_lngrevisionno = is_null($lngRevisionNo) ? "" : $lngRevisionNo; //3:リビジョン番号
        $v_strcustomersalescode = $d["strcustomerreceivecode"]; //4:顧客受注番号
        $v_lngsalesclasscode = $d["lngsalesclasscode"]; //5:売上区分コード
        $v_strsalesclassname = $d["strsalesclassname"]; //6:売上区分名
        $v_strgoodscode = $d["strgoodscode"]; //7:顧客品番
        $v_strproductcode = $d["strproductcode"]; //8:製品コード
        $v_strrevisecode = $d["strrevisecode"]; //9:再販コード
        $v_strproductname = $d["strproductname"]; //10:製品名
        $v_strproductenglishname = $d["strproductenglishname"]; //11:製品名（英語）
        $v_curproductprice = $d["curproductprice"]; //12:単価
        $v_lngquantity = $d["lngunitquantity"]; //13:入数
        $v_lngproductquantity = $d["lngproductquantity"]; //14:数量
        $v_lngproductunitcode = $d["lngproductunitcode"]; //15:製品単位コード
        $v_strproductunitname = $d["strproductunitname"]; //16:製品単位名
        $v_cursubtotalprice = $d["cursubtotalprice"]; //17:小計
        $v_strnote = $d["strnote"]; //18:明細備考

        // セルに値をセット
        $r = $startRowIndex + ($i - $itemMinIndex);
        setCellDetailValue($xlWorkSheet, "B", $r, mb_convert_encoding($v_lngslipno, 'euc-jp', 'UTF8')); //1:納品伝票番号
        setCellDetailValue($xlWorkSheet, "C", $r, mb_convert_encoding($v_lngslipdetailno, 'euc-jp', 'UTF8')); //2:納品伝票明細番号
        setCellDetailValue($xlWorkSheet, "D", $r, mb_convert_encoding($v_lngrevisionno, 'euc-jp', 'UTF8')); //3:リビジョン番号
        setCellDetailValue($xlWorkSheet, "E", $r, mb_convert_encoding($v_strcustomersalescode, 'euc-jp', 'UTF8')); //4:顧客受注番号
        setCellDetailValue($xlWorkSheet, "F", $r, mb_convert_encoding($v_lngsalesclasscode, 'euc-jp', 'UTF8')); //5:売上区分コード
        setCellDetailValue($xlWorkSheet, "G", $r, mb_convert_encoding($v_strsalesclassname, 'euc-jp', 'UTF8')); //6:売上区分名
        setCellDetailValue($xlWorkSheet, "H", $r, mb_convert_encoding($v_strgoodscode, 'euc-jp', 'UTF8')); //7:顧客品番
        setCellDetailValue($xlWorkSheet, "I", $r, mb_convert_encoding($v_strproductcode, 'euc-jp', 'UTF8')); //8:製品コード
        setCellDetailValue($xlWorkSheet, "J", $r, mb_convert_encoding($v_strrevisecode, 'euc-jp', 'UTF8')); //9:再販コード
        setCellDetailValue($xlWorkSheet, "K", $r, mb_convert_encoding($v_strproductname, 'euc-jp', 'UTF8')); //10:製品名
        setCellDetailValue($xlWorkSheet, "L", $r, mb_convert_encoding($v_strproductenglishname, 'euc-jp', 'UTF8')); //11:製品名（英語）
        setCellDetailValue($xlWorkSheet, "M", $r, $v_curproductprice); //12:単価
        setCellDetailValue($xlWorkSheet, "N", $r, $v_lngquantity); //13:入数
        setCellDetailValue($xlWorkSheet, "O", $r, $v_lngproductquantity); //14:数量
        setCellDetailValue($xlWorkSheet, "P", $r, $v_lngproductunitcode); //15:製品単位コード
        setCellDetailValue($xlWorkSheet, "Q", $r, mb_convert_encoding($v_strproductunitname, 'euc-jp', 'UTF8')); //16:製品単位名
        setCellDetailValue($xlWorkSheet, "R", $r, $v_cursubtotalprice); //17:小計
        setCellDetailValue($xlWorkSheet, "S", $r, mb_convert_encoding($v_strnote, 'euc-jp', 'UTF8')); //18:明細備考

    }

}

// ページ毎の情報から登録結果HTMLを生成
function fncGetRegisterResultTableBodyHtml($aryPerPage, $objDB)
{
    $strHtml = "";

    for ($i = 0; $i < count($aryPerPage); $i++) {
        $aryPage = $aryPerPage[$i];

        // 納品伝票番号
        $lngSlipNo = $aryPage["lngSlipNo"];
        // 納品伝票コード
        $strSlipCode = $aryPage["strSlipCode"];
        // リビジョン番号
        $lngRevisionNo = $aryPage["lngRevisionNo"];

        // 作成日の取得
        $dtmInsertDate = fncGetInsertDateBySlipCode($strSlipCode, $objDB);

        // HTMLの生成（/sc/finish2/finish2.js のファンクション呼び出しを含む）
        $aryHtml = array();
        $aryHtml[] = "                <tr>";
        $aryHtml[] = "                    <td class='item-value'>" . $strSlipCode . "</td>";
        $aryHtml[] = "                    <td class='item-value'>" . $dtmInsertDate . "</td>";
        $aryHtml[] = "                    <td class='item-value'>";
        $aryHtml[] = "                        <img class='btn-download'";
        $aryHtml[] = "                         onclick='OnClickDownload(this, \"" . $lngSlipNo . "\", \"" . $strSlipCode . "\", \"" . $lngRevisionNo . "\");'";
        $aryHtml[] = "                         onmouseover='OnMouseOverDownload(this);'";
        $aryHtml[] = "                         onmouseout='OnMouseOutDownload(this);'>";
        $aryHtml[] = "                    </td>";
        $aryHtml[] = "                </tr>";

        $strHtml .= implode("\n", $aryHtml);
    }

    return $strHtml;

}
