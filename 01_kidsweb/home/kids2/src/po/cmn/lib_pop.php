<?
/**
 *    発注　特別関数群
 *
 *    @package   kuwagata
 *    @license   http://www.wiseknot.co.jp/
 *    @copyright Copyright &copy; 2003, Wiseknot
 *    @author    Keiji Suzukaze <k-suzukaze@wiseknot.co.jp>
 *    @access    public
 *    @version   1.01
 *
 *    処理概要
 *    登録関連特別処理関数群
 *
 *    修正履歴
 *    2004.04.02    製品価格マスタ、製品テーブルへの登録関数を追加
 *
 *
 */

/**
 * 指定の発注データに関して、その発注明細データより指定されている製品コードの違いを調査する関数
 *
 *    発注明細情報より製品コードの違う行が存在しないかどうかのチェック関数
 *
 *    @param    Array        $aryOrderDetail    発注登録にて設定された明細情報
 *    @param  Object        $objDB            DBオブジェクト
 *    @return Integer     0                実行成功　明細のデータはすべて同じ製品コードのデータである
 *                        99                実行失敗　明細に違う製品コードのデータが存在する
 *    @access public
 */
function fncCheckOrderDetailProductCode($aryOrderDetail, $objDB)
{
    $bytSearchFlag = 0;

    // 各明細について調査
    for ($i = 0; $i < count($aryOrderDetail); $i++) {
        $strProductCode1 = $aryOrderDetail[$i]["strProductCode"];
        // 指定されたカラム名にデータがない場合
        if ($strProductCode1 == "") {
            $strProductCode1 = $aryOrderDetail[$i]["strproductcode"];
        }

        // 各明細について調査
        for ($j = 0; $j < count($aryOrderDetail); $j++) {
            $strProductCode2 = $aryOrderDetail[$j]["strProductCode"];
            // 指定されたカラム名にデータがない場合
            if ($strProductCode2 == "") {
                $strProductCode2 = $aryOrderDetail[$j]["strproductcode"];
            }

            if ($strProductCode1 != $strProductCode2) {
                $bytSearchFlag = 1;
                break;
            }
        }
        if ($bySeachFlag == 1) {
            break;
        }
    }

    if ($bytSearchFlag == 1) {
        return 99;
    }

    return 0;
}

/**
 * 発注登録時に製品価格マスタ、製品テーブルの情報確認、登録関数
 *
 *    発注明細情報より製品価格マスタにないデータ、製品テーブルにないデータを新規に登録する
 *
 *    @param    Array        $aryOrderDetail            発注登録にて設定された明細情報
 *    @param    Integer        $lngMonetaryUnitCode    通貨単位コード
 *    @param  Object        $objDB                    DBオブジェクト
 *    @return Boolean     TRUE                    実行成功
 *                        FALSE                    実行失敗
 *    @access public
 */
function fncCheckSetProduct($aryOrderDetail, $lngMonetaryUnitCode, $objDB)
{
    // 通貨単位の設定
    if ($lngMonetaryUnitCode == "") {
        $lngMonetaryUnitCode = DEF_MONETARY_YEN;
    }

    // 製品価格マスタへのデータ登録
    for ($i = 0; $i < count($aryOrderDetail); $i++) {
        // 概算区分コード
        $lngConversionClassCode = ($aryOrderDetail[$i]["lngConversionClassCode"] == "gs") ? 1 : 2;
        // 製品番号
        $lngProductNo = intval($aryOrderDetail[$i]["strProductCode"]);
        // 仕入科目コード
        $lngStockSubjectCode = $aryOrderDetail[$i]["lngStockSubjectCode"];
        // 仕入部品コード
        $lngStockItemCode = $aryOrderDetail[$i]["lngStockItemCode"];
        // 製品価格
        $curProductPrice = $aryOrderDetail[$i]["curProductPrice"];
        if ($curProductPrice == "") {
            $curProductPrice == 0;
        }
        // 明細備考
        $strDetailNote = $aryOrderDetail[$i]["strDetailNote"];
        if ($strDetailNote == "null") {
            $strDetailNote = "";
        }

        // 明細行データをチェックしその明細行の情報で製品価格マスタの内容にないものは登録する
        // 対象は製品単位計上の場合のみ
        if ($lngConversionClassCode == DEF_CONVERSION_SEIHIN) {
            $checkFlag = false;
            $strCheckQuery = "SELECT lngProductPriceCode FROM m_ProductPrice WHERE lngProductNo = " . $lngProductNo
                . " AND lngStockSubjectCode = " . $lngStockSubjectCode . " AND lngStockItemCode = " . $lngStockItemCode
                . " AND lngMonetaryUnitCode = " . $lngMonetaryUnitCode . " AND curProductPrice = " . $curProductPrice;
            // チェッククエリーの実行
            list($lngCheckResultID, $lngCheckResultNum) = fncQuery($strCheckQuery, $objDB);

            if ($lngCheckResultNum) {
                $checkFlag = true;
            }
            $objDB->freeResult($lngCheckResultID);

            // 登録されている製品価格がない場合は新たに製品価格マスタに登録する
            if ($checkFlag == false) {
                // m_ProductPriceのシーケンスを取得
                $sequence_m_productprice = fncGetSequence('m_ProductPrice.lngProductPriceCode', $objDB);

                unset($aryQuery);

                $aryQuery[] = "INSERT INTO m_ProductPrice (";
                $aryQuery[] = "lngProductPriceCode, "; // 製品価格コード
                $aryQuery[] = "lngProductNo,"; // 製品番号
                $aryQuery[] = "lngStockSubjectCode,"; // 仕入科目コード
                $aryQuery[] = "lngStockItemCode,"; // 仕入部品コード
                $aryQuery[] = "lngMonetaryUnitCode,"; // 通貨単位コード
                $aryQuery[] = "curProductPrice "; // 製品価格
                $aryQuery[] = ") VALUES (";
                $aryQuery[] = $sequence_m_productprice . ", "; // 製品価格コード
                $aryQuery[] = $lngProductNo . ", "; // 製品番号
                $aryQuery[] = $lngStockSubjectCode . ", "; // 仕入科目コード
                $aryQuery[] = $lngStockItemCode . ", "; // 仕入部品コード
                $aryQuery[] = $lngMonetaryUnitCode . ", "; // 通貨単位コード
                $aryQuery[] = $curProductPrice; // 製品価格
                $aryQuery[] = ")";

                $strQuery = "";
                $strQuery = implode("\n", $aryQuery);

                if (!$lngResultID = $objDB->execute($strQuery)) {
                    fncOutputError(9051, DEF_ERROR, "製品価格マスタへの登録処理に失敗しました。", true, "", $objDB);
                    return false;
                }
                $objDB->freeResult($lngResultID);
            }
        }

        // 明細行データをチェックしその明細行の情報で製品テーブルにないデータを製品テーブルに登録する
        $checkFlag = false;
        $strCheckQuery = "SELECT lngProductSubNo FROM t_Product WHERE lngProductNo = " . $lngProductNo
            . " AND lngStockSubjectCode = " . $lngStockSubjectCode . " AND lngStockItemCode = " . $lngStockItemCode;
        // 仕入部品が ９９ その他 の場合は明細行についても比較する
        if ($lngStockItemCode == 99) {
            $strCheckQuery .= " AND strNote = '" . $strNote . "'";
        }
        // チェッククエリーの実行
        list($lngCheckResultID, $lngCheckResultNum) = fncQuery($strCheckQuery, $objDB);

        if ($lngCheckResultNum) {
            $checkFlag = true;
        }
        $objDB->freeResult($lngCheckResultID);

        // 登録されている製品情報がない場合は新たに製品テーブルに登録する
        if ($checkFlag == false) {
            // 同じ製品コードに対して一意になるようにロックをかける
            $strLockQuery = "SELECT lngProductNo, lngProductSubNo "
                . "FROM t_Product WHERE lngProductNo = " . $lngProductNo
                . " FOR UPDATE";

            // ロッククエリーの実行
            list($lngLockResultID, $lngLockResultNum) = fncQuery($strLockQuery, $objDB);

            $lngMaxProductSubNo = 0;
            if ($lngLockResultNum) {
                for ($i = 0; $i < $lngLockResultNum; $i++) {
                    $objResult = $objDB->fetchObject($lngLockResultID, $i);
                    if ($lngMaxProductSubNo < $objResult->lngproductsubno) {
                        $lngMaxProductSubNo = $objResult->lngproductsubno;
                    }
                }
            }
            $objDB->freeResult($lngLockResultID);

            // 製品サブ番号
            $lngMaxProductSubNo++;

            unset($aryQuery);
            $aryQuery[] = "INSERT INTO t_Product (";
            $aryQuery[] = "lngProductSubNo, "; // 製品サブ番号
            $aryQuery[] = "lngProductNo,"; // 製品番号
            $aryQuery[] = "lngStockSubjectCode,"; // 仕入科目コード
            $aryQuery[] = "lngStockItemCode"; // 仕入部品コード
            if ($lngStockItemCode == 99) {
                $aryQuery[] = ", strNote"; // 備考
            }
            $aryQuery[] = ") VALUES (";
            $aryQuery[] = $lngMaxProductSubNo . ", "; // 製品サブ番号
            $aryQuery[] = $lngProductNo . ", "; // 製品番号
            $aryQuery[] = $lngStockSubjectCode . ", "; // 仕入科目コード
            $aryQuery[] = $lngStockItemCode; // 仕入部品コード
            if ($lngStockItemCode == 99) {
                $aryQuery[] = ", '" . $strDetailNote . "'"; // 備考
            }
            $aryQuery[] = ")";

            $strQuery = "";
            $strQuery = implode("\n", $aryQuery);

            if (!$lngResultID = $objDB->execute($strQuery)) {
                fncOutputError(9051, DEF_ERROR, "製品テーブルへの登録処理に失敗しました。", true, "", $objDB);
                return false;
            }
            $objDB->freeResult($lngResultID);
        }
    }

    return true;
}

function fncPayConditionCodeMatch($aryData, $aryHeadColumnNames, $aryPoDitail, $objDB)
{
    // 推奨支払方法
    $lngPayConditionCode_recommend = "";
    $isSpecifySubject = false;
    //
    // 仕入部品配列の設定
    $arystockitemcode = array("1", "2", "3", "7", "9", "11");
    // 仕入先表示コードの取得
    $strcompanydisplaycode = fncGetMasterValue("m_company", "lngcompanycode", "strcompanydisplaycode", $aryData["lngCustomerCode"] . ":str", '', $objDB);
    // 仕入先国コードの取得
    $strCountryCode = fncGetMasterValue("m_company", "lngcompanycode", "lngcountrycode", $aryData["lngCustomerCode"] . ":str", '', $objDB);
    // 納品先の国コードを取得
    $strLocationCountryCode = fncGetMasterValue("m_company", "strcompanydisplaycode", "lngcountrycode", $aryData["lngLocationCode"] . ":str", '', $objDB);

    // 仕入先が海外の場合
    if ($strCountryCode != 81) {
        // 海外取引先リストの取得
        $foreignCusttomerArray = fncSetForeignTabel();
        // 仕入先が海外取引先の場合
        if (in_array($strcompanydisplaycode, $foreignCusttomerArray)) {
            if (is_array($aryPoDitail)) {
                for ($i = 0; $i < count($aryPoDitail); $i++) {
                    if ($aryPoDitail[$i]["lngStockSubjectCode"] == "402") {
                        if (in_array($aryPoDitail[$i]["lngStockItemCode"], $arystockitemcode)) {
                            $isSpecifySubject = true;
                            break;
                        }
                    }
                }
            }
            // 指定した仕入科目と仕入部品の場合
            if ($isSpecifySubject) {
                // USドルの場合、そして合計金額が30000ドルを超えた場合
                if ($aryData["lngMonetaryUnitCode"] == 2 && $aryData["curAllTotalPrice"] >= 30000) {
                    // 納品先が日本の場合、L/Cを推奨する
                    if ($strLocationCountryCode == 81) {
                        $lngPayConditionCode_recommend = 1;
                    }
                }
            }
        }

        // 上記以外の場合、T/Tを推奨する
        if ($lngPayConditionCode_recommend == "") {
            $lngPayConditionCode_recommend = 2;
        }

        // 推奨した支払方法と受け取った支払方法が不一致の場合、推奨メッセージを出す
        if ($aryData["lngPayConditionCode"] != $lngPayConditionCode_recommend) {
            $strhtml = fncPulldownMenu(2, 0, '', $objDB);
            $frmPayConditionTable = '<span id="VarsA10">
                            <select id="lngPayConditionCodeList" tabindex="3" onchange = "fncPayConditionFrmChanged();">
                                ' . $strhtml .
                '</select></span>';

            //bodyのOnload関数に関数を追加
            $aryData["isPayConditionMatch"] = "false";
            $aryData["lngMatchResult1"] = $aryData["lngPayConditionCode"];
            $aryData["lngMatchResult2"] = $lngPayConditionCode_recommend;
            $aryData["strOptionalScript"] = '	<script type="text/javascript" language="javascript" src="/po/cmn/resultexstr.js"></script>
        <script>
            if( $(\'input[name="isPayConditionMatch"]\') && $(\'input[name="isPayConditionMatch"]\').val() == "false" )
            {
                fncPayConditionConfirm($(\'input[name="lngMatchResult1"]\').val(),$(\'input[name="lngMatchResult2"]\').val());
            }
        </script>';

        }
    }
    if ($frmPayConditionTable == "") {
        $frmPayConditionTable = $aryData["strPayConditionName"];
    }

    $aryData["strPayConditionTable"] = '<tr>
                                    <td id="PayCondition" class="SegColumn">' . $aryHeadColumnNames["CNlngPayConditionCode"] . '</td>
                                    <td class="Segs">' . $frmPayConditionTable . '<span id="strRecommendPayCondition"></span></td>
                                </tr>';
    return $aryData;

}

//2007.12.14 matsuki update start
function fncSetForeignTabel()
{

    $aryforeigntable = array(
        1111,
        1112,
        1113,
        1117,
        1119,
        1211,
        1303,
        1304,
        2106,
        2107,
        2207,
        2208,
        2209,
        2215,
        2305,
        2307,
        2308,
        3205,
        3207,
        3209,
        3210,
        3217,
        3220,
        3223,
        3504,
        4202,
        4510,
        4511,
        4512,
        4516,
        5209,
        6106,
        6107,
        6115,
        6117,
        6118,
        6127,
        6318,
        6139,
        6311,
        6320,
        6403,
        6404,
        6505,
        6507,
        8301,
        9101,
        9102,
        9103,
        9104,
        9403,
        9995);

    return $aryforeigntable;
}
//会社コード8310と9402はこのルールに適用しない。
//最終編集は2012年3月8日。
//8301をルールに追加　//2012年6月25日

//2007.12.14 matsuki update end
