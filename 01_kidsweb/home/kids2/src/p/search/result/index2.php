<?php
// ----------------------------------------------------------------------------
/**
 *       商品検索 履歴取得イベント
 *
 *       処理概要
 *         ・商品コード、リビジョン番号により商品履歴情報を取得する
 *
 *       更新履歴
 *
 */
// ----------------------------------------------------------------------------
// 読み込み
include 'conf.inc';
require LIB_FILE;
include 'JSON.php';
require SRC_ROOT . "p/cmn/lib_p.php";

//値の取得
$postdata = file_get_contents("php://input");
$aryData = json_decode($postdata, true);
$objDB = new clsDB();
$objAuth = new clsAuth();
$objDB->open("", "", "", "");
//JSONクラスインスタンス化
$s = new Services_JSON();
//値が存在しない場合は通常の POST で受ける
if ($aryData == null) {
    $aryData = $_POST;
}

$displayColumns = array();
// 表示項目の抽出
foreach ($aryData["displayColumns"] as $key) {
    $displayColumns[$key] = $key;
}

// セッション確認
$objAuth = fncIsSession($_REQUEST["strSessionID"], $objAuth, $objDB);

//echo "strReviseCode:" . $aryData["strReviseCode"] . "<br>";

// 検索項目から一致する最新の仕入データを取得するSQL文の作成関数
$strQuery = fncGetProductsByStrProductCodeSQL($aryData["strProductCode"], $aryData["strReviseCode"] ,$aryData["lngRevisionNo"]);

//echo $strQuery . "<br>";

// 値をとる =====================================
list($lngResultID, $lngResultNum) = fncQuery($strQuery, $objDB);

// 指定数以内であれば通常処理
for ($i = 0; $i < $lngResultNum; $i++) {
    $records = pg_fetch_all($lngResultID);
}

$objDB->freeResult($lngResultID);

// 検索結果テーブル生成の為DOMDocumentを使用
$doc = new DOMDocument();

// キー文字列を小文字に変換
$displayColumns = array_change_key_case($displayColumns, CASE_LOWER);

// -------------------------------------------------------
// 各種ボタン表示チェック/権限チェック
// -------------------------------------------------------
// 詳細カラムを表示
$existsDetail = array_key_exists("btndetail", $displayColumns);
// 履歴カラムを表示
$existsHistory = array_key_exists("btnhistory", $displayColumns);
// 詳細ボタンを表示
$allowedDetail = fncCheckAuthority(DEF_FUNCTION_P4, $objAuth);

// 詳細表示　削除データの表示
$allowedDetailDelete = fncCheckAuthority(DEF_FUNCTION_P5, $objAuth);

$aryTableHeaderName = array();
$aryTableHeaderName["dtminsertdate"] = "作成日";
$aryTableHeaderName["lnggoodsplanprogresscode"] = "企画進行状況";
$aryTableHeaderName["dtmupdatedate"] = "改訂日時";
$aryTableHeaderName["strproductcode"] = "製品コード";
$aryTableHeaderName["lngrevisionno"] = "リビジョン番号";
$aryTableHeaderName["strproductname"] = "製品名";
$aryTableHeaderName["strproductenglishname"] = "製品名（英語）";
$aryTableHeaderName["lnginputusercode"] = "入力者";
$aryTableHeaderName["lnginchargegroupcode"] = "営業部署";
$aryTableHeaderName["lnginchargeusercode"] = "担当者";
$aryTableHeaderName["lngdevelopusercode"] = "開発担当者";
$aryTableHeaderName["lngcategorycode"] = "カテゴリ";
$aryTableHeaderName["strgoodscode"] = "顧客品番";
$aryTableHeaderName["strgoodsname"] = "商品名称";
$aryTableHeaderName["lngcustomercompanycode"] = "顧客";
$aryTableHeaderName["lngcustomerusercode"] = "顧客担当者";
$aryTableHeaderName["lngpackingunitcode"] = "荷姿単位";
$aryTableHeaderName["lngproductunitcode"] = "製品単位";
$aryTableHeaderName["lngproductformcode"] = "商品形態";
$aryTableHeaderName["lngboxquantity"] = "内箱（袋）入数";
$aryTableHeaderName["lngcartonquantity"] = "カートン入数";
$aryTableHeaderName["lngproductionquantity"] = "生産予定数";
$aryTableHeaderName["lngfirstdeliveryquantity"] = "初回納品数";
$aryTableHeaderName["lngfactorycode"] = "生産工場";
$aryTableHeaderName["lngassemblyfactorycode"] = "アッセンブリ工場";
$aryTableHeaderName["lngdeliveryplacecode"] = "納品場所";
$aryTableHeaderName["dtmdeliverylimitdate"] = "納期";
$aryTableHeaderName["curproductprice"] = "納価";
$aryTableHeaderName["curretailprice"] = "上代";
$aryTableHeaderName["lngtargetagecode"] = "対象年齢";
$aryTableHeaderName["lngroyalty"] = "ロイヤリティ";
$aryTableHeaderName["lngcertificateclasscode"] = "証紙";
$aryTableHeaderName["lngcopyrightcode"] = "版権元";
$aryTableHeaderName["strcopyrightnote"] = "版権元備考";
$aryTableHeaderName["strcopyrightdisplaystamp"] = "版権表示（刻印）";
$aryTableHeaderName["strcopyrightdisplayprint"] = "版権表示（印刷物）";
$aryTableHeaderName["strproductcomposition"] = "製品構成";
$aryTableHeaderName["strassemblycontents"] = "アッセンブリ内容";
$aryTableHeaderName["strspecificationdetails"] = "仕様詳細";
// -------------------------------------------------------
// テーブルセル作成
// -------------------------------------------------------
$index = 0;
// 検索結果件数分走査
foreach ($records as $i => $record) {
    // 背景色設定
    if ($record["strgroupdisplaycolor"]) {
        $bgcolor = "background-color: " . $record["strgroupdisplaycolor"] . ";";
    } else {
        $bgcolor = "background-color: #FFFFFF;";
    }

    // tbody > tr要素作成
    $trBody = $doc->createElement("tr");
    $trBody->setAttribute("id", $record["strproductcode"]. "_" . $record["strrevisecode"] .  "_" . sprintf("%02d",$record["lngrevisionno"]) );
    
    // 項番
    $index +=1;
    $tdIndex = $doc->createElement("td", $aryData["rownum"]. "." . $record["lngrevisionno"]);
    $tdIndex->setAttribute("style", $bgcolor);
    $trBody->appendChild($tdIndex);

    // 詳細を表示
    if ($existsDetail) {
        // 詳細セル
        $tdDetail = $doc->createElement("td");
        $tdDetail->setAttribute("class", $exclude);
        $tdDetail->setAttribute("style", $bgcolor . "text-align: center;");

        // 詳細ボタンの表示
        if (($allowedDetailDelete) or ($allowedDetail and $record["lngrevisionno"] >= 0)) {
            // 詳細ボタン
            $imgDetail = $doc->createElement("img");
            $imgDetail->setAttribute("src", "/img/type01/so/detail_off_bt.gif");
            $imgDetail->setAttribute("id", $record["lngproductno"]);
            $imgDetail->setAttribute("revisionno", $record["lngrevisionno"]);
            $imgDetail->setAttribute("class", "detail button");
//            $imgDetail->setAttribute("onclick", "alert('test');");
            // td > img
            $tdDetail->appendChild($imgDetail);
        }
        // tr > td
        $trBody->appendChild($tdDetail);
    }

    // 履歴項目を表示
    if ($existsHistory) {
        // 履歴セル
        $tdHistory = $doc->createElement("td");
        $tdHistory->setAttribute("class", $exclude);
        $tdHistory->setAttribute("style", $bgcolor . "text-align: center;");
        // tr > td
        $trBody->appendChild($tdHistory);
    }

    // TODO 要リファクタリング
    // 指定されたテーブル項目のセルを作成する
    foreach ($aryTableHeaderName as $key => $value) {
        // 表示対象のカラムの場合
        if (array_key_exists($key, $displayColumns)) {
            // 項目別に表示テキストを設定
            switch ($key) {
                // 作成日
                case "dtminsertdate":
                    $td = $doc->createElement("td", $record["dtminsertdate"]);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 企画進行状況
                case "lnggoodsplanprogresscode":
                    $td = $doc->createElement("td", $record["strgoodsplanprogressname"]);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 改訂日時
                case "dtmupdatedate":
                    $td = $doc->createElement("td", $record["dtmupdatedate"]);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 製品コード
                case "strproductcode":
                    $td = $doc->createElement("td", $record["strproductcode"] . "_" . $record["strrevisecode"]);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // リビジョン番号
                case "lngrevisionno":
                    $td = $doc->createElement("td", $record["lngrevisionno"]);
                    $td->setAttribute("style", $bgcolor);
                    $td->setAttribute("rowspan", $rowspan);
                    $trBody->appendChild($td);
                    break;
                // 製品名
                case "strproductname":
                    $td = $doc->createElement("td", $record["strproductname"]);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 製品名（英語）
                case "strproductenglishname":
                    $td = $doc->createElement("td", $record["strproductenglishname"]);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 入力者
                case "lnginputusercode":
                    if ($record["strinputuserdisplaycode"] != "") {
                        $textContent = "[" . $record["strinputuserdisplaycode"] . "]" . " " . $record["strinputuserdisplayname"];
                    } else {
                        $textContent = "";
                    }
                    $td = $doc->createElement("td", $textContent);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 営業部署
                case "lnginchargegroupcode":
                    if ($record["strinchargegroupdisplaycode"] != "") {
                        $textContent = "[" . $record["strinchargegroupdisplaycode"] . "]" . " " . $record["strinchargegroupdisplayname"];
                    } else {
                        $textContent = "";
                    }
                    $td = $doc->createElement("td", $textContent);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // [担当者表示コード] 担当者表示名
                case "lnginchargeusercode":
                    if ($record["strinchargeuserdisplaycode"] != "") {
                        $textContent = "[" . $record["strinchargeuserdisplaycode"] . "]" . " " . $record["strinchargeuserdisplayname"];
                    } else {
                        $textContent = "";
                    }
                    $td = $doc->createElement("td", $textContent);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // [開発担当者表示コード] 開発担当者表示名
                case "lngdevelopusercode":
                    if ($record["strdevelopuserdisplaycode"] != "") {
                        $textContent = "[" . $record["strdevelopuserdisplaycode"] . "]" . " " . $record["strdevelopuserdisplayname"];
                    } else {
                        $textContent = "";
                    }
                    $td = $doc->createElement("td", $textContent);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // カテゴリ
                case "lngcategorycode":
                    $td = $doc->createElement("td", $record["strcategoryname"]);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 顧客品番
                case "strgoodscode":
                    $td = $doc->createElement("td", $record["strgoodscode"]);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 商品名称
                case "strgoodsname":
                    $td = $doc->createElement("td", $record["strgoodsname"]);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 顧客
                case "lngcustomercompanycode":
                    if ($record["strcustomercompanycode"] != "") {
                        $textContent = "[" . $record["strcustomercompanycode"] . "]" . " " . $record["strcustomercompanyname"];
                    } else {
                        $textContent = "";
                    }
                    $td = $doc->createElement("td", $textContent);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 顧客担当者
                case "lngcustomerusercode":
                    if ($record["strcustomerusercode"] != "") {
                        $textContent = "[" . $record["strcustomerusercode"] . "]" . " " . $record["strcustomerusername"];
                    } else {
                        $textContent = "";
                    }
                    $td = $doc->createElement("td", $textContent);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 荷姿単位
                case "lngpackingunitcode":
                    $td = $doc->createElement("td", $record["strpackingunitname"]);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 製品単位
                case "lngproductunitcode":
                    $td = $doc->createElement("td", $record["strproductunitname"]);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 商品形態
                case "lngproductformcode":
                    $td = $doc->createElement("td", $record["strproductformname"]);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 内箱（袋）入数
                case "lngboxquantity":
                    $td = $doc->createElement("td", $record["lngboxquantity"]);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // カートン入数
                case "lngcartonquantity":
                    $td = $doc->createElement("td", $record["lngcartonquantity"]);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 生産予定数
                case "lngproductionquantity":
                    $td = $doc->createElement("td", $record["lngproductionquantity"] . " " . $record["strproductionunitname"]);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 初回納品数
                case "lngfirstdeliveryquantity":
                    $td = $doc->createElement("td", $record["lngfirstdeliveryquantity"] . " " . $record["strfirstdeliveryunitname"]);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 生産工場
                case "lngfactorycode":
                    if ($record["strfactorycode"] != "") {
                        $textContent = "[" . $record["strfactorycode"] . "]" . " " . $record["strfactoryname"];
                    } else {
                        $textContent = "";
                    }
                    $td = $doc->createElement("td", $textContent);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // アッセンブリ工場
                case "lngassemblyfactorycode":
                    if ($record["strassemblyfactorycode"] != "") {
                        $textContent = "[" . $record["strassemblyfactorycode"] . "]" . " " . $record["strassemblyfactoryname"];
                    } else {
                        $textContent = "";
                    }
                    $td = $doc->createElement("td", $textContent);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 納品場所
                case "lngdeliveryplacecode":
                    if ($record["strdeliveryplacecode"] != "") {
                        $textContent = "[" . $record["strdeliveryplacecode"] . "]" . " " . $record["strdeliveryplacename"];
                    } else {
                        $textContent = "";
                    }
                    $td = $doc->createElement("td", $textContent);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 納期
                case "dtmdeliverylimitdate":
                    $td = $doc->createElement("td", $record["dtmdeliverylimitdate"]);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 納価
                case "curproductprice":
                    if ($record["curproductprice"] != "") {
                        $textContent = "&yen;" . " " . $record["curproductprice"];
                    } else {
                        $textContent = "";
                    }
                    $td = $doc->createElement("td", $textContent);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 上代
                case "curretailprice":    
                    if ($record["curretailprice"] != "") {
                        $textContent = "&yen;" . " " . $record["curretailprice"];
                    } else {
                        $textContent = "";
                    }
                    $td = $doc->createElement("td", $textContent);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 対象年齢
                case "lngtargetagecode":
                    $td = $doc->createElement("td", $record["strtargetagename"]);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // ロイヤリティ
                case "lngroyalty":
                    $td = $doc->createElement("td", $record["lngroyalty"]);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 証紙
                case "lngcertificateclasscode":
                    $td = $doc->createElement("td", $record["strcertificateclassname"]);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 版権元
                case "lngcopyrightcode":
                    $td = $doc->createElement("td", $record["strcopyrightname"]);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 版権元備考
                case "strcopyrightnote":
                    $td = $doc->createElement("td", $record["strcopyrightnote"]);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 版権表示（刻印）
                case "strcopyrightdisplaystamp":
                    $td = $doc->createElement("td", $record["strcopyrightdisplaystamp"]);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 版権表示（印刷物）
                case "strcopyrightdisplayprint":
                    $td = $doc->createElement("td", $record["strcopyrightdisplayprint"]);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 製品構成
                case "strproductcomposition":
                    $td = $doc->createElement("td", "全" . $record["strproductcomposition"] . "種アッセンブリ");
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // アッセンブリ内容
                case "strassemblycontents":
                    $td = $doc->createElement("td", $record["strassemblycontents"]);
                    $td->setAttribute("style", $bgcolor);
                    $trBody->appendChild($td);
                    break;
                // 仕様詳細
                case "strspecificationdetails":
                    $td = $doc->createElement("td", $record["strspecificationdetails"]);
                    $td->setAttribute("style", $bgcolor . "white-space: pre; ");
                    // $td->setAttribute("style", "white-space: pre; ");
                    $trBody->appendChild($td);
                    break;

            }
        }
    }

    $strHtml .= $doc->saveXML($trBody);

}

// // HTML出力
echo $strHtml;
