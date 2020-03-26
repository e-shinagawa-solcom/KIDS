<?php
// ----------------------------------------------------------------------------
/**
 *       納品書プレビュー
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
 *
 *       更新履歴
 *
 */
// ----------------------------------------------------------------------------

//-------------------------------------------------------------------------
// ライブラリファイル読込
//-------------------------------------------------------------------------
include 'conf.inc';
require LIB_FILE;
require_once LIB_EXCLUSIVEFILE;
require SRC_ROOT . "sc/cmn/lib_scr.php";
require PATH_HOME . "/vendor/autoload.php";

$objDB = new clsDB();
$objAuth = new clsAuth();

//-------------------------------------------------------------------------
// パラメータ取得
//-------------------------------------------------------------------------
// セッションID
if ($_POST["strSessionID"]) {
    $aryData["strSessionID"] = $_POST["strSessionID"];
} else {
    $aryData["strSessionID"] = $_REQUEST["strSessionID"];
}
setcookie("strSessionID", $aryData["strSessionID"], 0, "/");

// 処理モード
$strMode = $_POST["strMode"];

//-------------------------------------------------------------------------
// DBオープン
//-------------------------------------------------------------------------
$objDB->open("", "", "", "");

//-------------------------------------------------------------------------
// 入力文字列値・セッション・権限チェック
//-------------------------------------------------------------------------
// 文字列チェック
$aryCheck["strSessionID"] = "null:numenglish(32,32)";
$aryResult = fncAllCheck($aryData, $aryCheck);
fncPutStringCheckError($aryResult, $objDB);

// セッション確認
$objAuth = fncIsSession($aryData["strSessionID"], $objAuth, $objDB);
$lngUserCode = $objAuth->UserCode;
$lngUserGroup = $objAuth->AuthorityGroupCode;

// 600 売上管理
if (!fncCheckAuthority(DEF_FUNCTION_SC0, $objAuth)) {
    fncOutputError(9060, DEF_WARNING, "アクセス権限がありません。", true, "sc/regist/index.php?strSessionID=" . $aryData["strSessionID"], $objDB);
}

// 601 売上管理（売上登録）
if (fncCheckAuthority(DEF_FUNCTION_SC1, $objAuth)) {
    $aryData["strRegistURL"] = "regist/index.php?strSessionID=" . $aryData["strSessionID"];
}

// 610 売上管理（行追加・行削除）
if (!fncCheckAuthority(DEF_FUNCTION_SC10, $objAuth)) {
    $aryData["adddelrowview"] = 'hidden';
}
//-------------------------------------------------------------------------
//  プレビュー画面表示
//-------------------------------------------------------------------------
if ($strMode == "display-preview") {
    // --------------------------
    //  登録/修正データ退避
    // --------------------------
    // 修正対象に紐づくデータ（修正時にセット。登録時は空）
    $lngRenewTargetSlipNo = $_POST["lngRenewTargetSlipNo"];
    $strRenewTargetSlipCode = $_POST["strRenewTargetSlipCode"];
    $lngRenewTargetSalesNo = $_POST["lngRenewTargetSalesNo"];
    $strRenewTargetSalesCode = $_POST["strRenewTargetSalesCode"];
    $aryData["lngRevisionNo"] = $_POST["lngRenewTargetRevisionNo"];
    $aryData["lngRenewTargetSlipNo"] = $lngRenewTargetSlipNo;
    $aryData["strRenewTargetSlipCode"] = $strRenewTargetSlipCode;
    $aryData["lngRenewTargetSalesNo"] = $lngRenewTargetSalesNo;
    $aryData["strRenewTargetSalesCode"] = $strRenewTargetSalesCode;
    // プレビュー表示後に登録/修正処理を行うため、入力データをjsonに変換して退避する
    $aryHeader = $_POST["aryHeader"];
    $aryDetail = $_POST["aryDetail"];
    $aryData["aryHeaderJson"] = EncodeToJson($aryHeader);
    $aryData["aryDetailJson"] = EncodeToJson($aryDetail);

    // --------------------------------
    //  文字コード変換（UTF-8->UTF-8）
    // --------------------------------
    //jQueryのajaxでPOSTすると文字コードが UTF-8 になって
    //データ登録時にエラーになるため、DB処理前にUTF-8に変換する

    // --------------------------
    //  プレビュー生成
    // --------------------------
    //登録データとExcelテンプレートとからプレビューHTMLを生成する
    // $aryGenerateResult = fncGenerateReportImage("html", $aryHeader, $aryDetail,
    //     null, null, null, null, null,
    //     $objDB);
    $aryGenerateResult = fncGenerateReportImage("html", $aryHeader, $aryDetail,
        $lngRenewTargetSlipNo, null, $strRenewTargetSlipCode, $lngRenewTargetSalesNo, null,
        $objDB);

    // --------------------------
    //  プレビュー画面表示
    // --------------------------
    // テンプレートから構築したHTMLを出力
    $aryData["PREVIEW_STYLE"] = $aryGenerateResult["PreviewStyle"];
    $aryData["PREVIEW_DATA"] = $aryGenerateResult["PreviewData"];
    $aryData["strComfirmMessage"] = "上記の内容で売上（納品書）を登録します。よろしいですか？";
    $objTemplate = new clsTemplate();
    $objTemplate->getTemplate("sc/regist2/preview.tmpl");
    $objTemplate->replace($aryData);
    $objTemplate->complete();

    echo $objTemplate->strTemplate;

    // DB切断
    $objDB->close();
    // 処理終了
    return true;
}

//-------------------------------------------------------------------------
//  登録/修正処理
//-------------------------------------------------------------------------
if ($strMode == "regist-or-renew") {
    // --------------------------
    //  登録/修正データ復元
    // --------------------------
    // 修正対象に紐づく納品伝票番号（登録の場合は空）
    $lngRenewTargetSlipNo = $_POST["lngRenewTargetSlipNo"];
    // 修正対象に紐づく納品コード（登録の場合は空）
    $strRenewTargetSlipCode = $_POST["strRenewTargetSlipCode"];
    // 修正対象に紐づく売上番号（登録の場合は空）
    $lngRenewTargetSalesNo = $_POST["lngRenewTargetSalesNo"];
    // 修正対象に紐づく売上コード（登録の場合は空）
    $strRenewTargetSalesCode = $_POST["strRenewTargetSalesCode"];
    $lngrevisionno = $_POST["lngRevisionNo"];
    // 登録か修正か（true:登録、false:修正）
    $isCreateNew = strlen($lngRenewTargetSlipNo) == 0;

    // プレビュー表示前に退避した登録/修正データをjsonから復元する
    $aryHeader = DecodeFromJson($_POST["aryHeaderJson"]);
    $aryDetail = DecodeFromJson($_POST["aryDetailJson"]);

    // --------------------------------
    //  文字コード変換（UTF-8->UTF-8）
    // --------------------------------
    // json変換時に文字コードが UTF-8 になって
    // データ登録時にエラーになるため、UTF-8に戻す

    // --------------------------
    //  データベース処理
    // --------------------------
    // トランザクション開始
    $objDB->transactionBegin();

    // --------------------------
    // 排他制御
    // --------------------------
    if (!$isCreateNew) {
        // 納品書マスタロック
        if (!lockSlip($lngRenewTargetSlipNo, $objDB)) {
            MoveToErrorPage("他ユーザーが納品書を編集中です。");
        }
        if (isSlipModified($lngRenewTargetSlipNo, $lngrevisionno, $objDB)) {
            MoveToErrorPage("納品書が他ユーザーにより更新または削除されています。");
        }
        if (fncInvoiceIssued($lngRenewTargetSlipNo, $lngrevisionno, $objDB)) {
            MoveToErrorPage("納品書は請求処理済みのため修正できません。");
        }
        if (!fncLockReceiveByOldDetail($lngRenewTargetSlipNo, $lngrevisionno, $objDB)) {
            MoveToErrorPage("更新前受注データのロックに失敗しました。");
        }
        // 削除された明細のために、現リビジョンの納品書明細に紐づくの受注データのステータスを一旦元に戻す。
        // （締め済は対象外）
        if (!fncResetReceiveStatus($lngRenewTargetSlipNo, $lngrevisionno, $objDB)) {
            MoveToErrorPage("更新前受注データのリセットに失敗しました。");
        }
    }
    // 受注明細ロック
    foreach ($aryDetail as $row) {
        if (!lockReceive($row["lngreceiveno"], $objDB)) {
            MoveToErrorPage("受注データが他ユーザーによりロックされています。");
        }
    }

    //DBG:一時コメントアウト対象
    // --------------------------
    //  登録/修正前バリデーション
    // --------------------------
    // 受注状態コードが2,4以外の明細が存在するならエラーとする
    if (fncNotReceivedDetailExists($aryDetail, $objDB, $isCreateNew)) {
        MoveToErrorPage("納品書が発行できない状態の明細が選択されています。");
    }

    //DBG:一時コメントアウト対象

    // 受注マスタ更新
    $updResult = fncUpdateReceiveMaster($aryDetail, $objDB);
    if (!$updResult) {
        MoveToErrorPage("受注データの更新に失敗しました。");
    }

    // 売上マスタ、売上詳細、納品伝票マスタ、納品伝票明細へのレコード追加。
    // 納品伝票番号が空なら登録、空でないなら修正を行う
    $aryRegResult = fncRegisterSalesAndSlip(
        $lngRenewTargetSlipNo, $strRenewTargetSlipCode, $lngRenewTargetSalesNo, $strRenewTargetSalesCode,
        $aryHeader, $aryDetail, $objDB, $objAuth);

    if (!$aryRegResult["result"]) {
        MoveToErrorPage("売上・納品伝票データの登録または修正に失敗しました。");
    }

    // コミット
    $objDB->transactionCommit();

    // --------------------------
    //  登録結果画面表示
    // --------------------------
    // 処理結果（テーブル出力）
    $aryPerPage = $aryRegResult["aryPerPage"];

    // //DBG:TESTCODE 仮の処理結果
    // $aryPage1 = array();
    // $aryPage1["lngSlipNo"] = 30487;
    // $aryPage1["lngRevisionNo"] = 1;
    // $aryPage1["strSlipCode"] = "02030457";
    // $aryPage2 = array();
    // $aryPage2["lngSlipNo"] = 27741;
    // $aryPage2["lngRevisionNo"] = 0;
    // $aryPage2["strSlipCode"] = "02028443";
    // $aryPerPage = array();
    // $aryPerPage[] = $aryPage1;
    // $aryPerPage[] = $aryPage2;

    // 処理結果をテーブルのHTMLに出力
    $strHtml = fncGetRegisterResultTableBodyHtml($aryPerPage, $objDB);
    $aryData["tbodyResiterResult"] = $strHtml;

    // 登録完了メッセージ
    $aryData["strMessage"] = "登録が完了しました";

    // テンプレートから構築したHTMLを出力
    $objTemplate = new clsTemplate();
    $objTemplate->getTemplate("sc/finish2/parts.tmpl");
    $objTemplate->replace($aryData);
    $objTemplate->complete();
    echo $objTemplate->strTemplate;

    // DB切断
    $objDB->close();
    // 処理終了
    return true;
}

//-------------------------------------------------------------------------
//  帳票ダウンロード
//-------------------------------------------------------------------------
if ($strMode == "download") {

    // 納品書データのキー項目をパラメータから受け取る
    $lngSlipNo = $_POST["lngSlipNo"];
    $strSlipCode = $_POST["strSlipCode"];
    $lngRevisionNo = $_POST["lngRevisionNo"];

    // レコード登録後に作られるデータをDBより取得する
    $lngSalesNo = fncGetSalesNoBySlipCode($strSlipCode, $objDB);
    $dtmInsertDate = fncGetInsertDateBySlipCode($strSlipCode, $objDB);

    // 帳票テンプレートに設定する納品書データの読み込み（ヘッダ・フッタ部）
    $aryHeader = fncGetHeaderBySlipNo($lngSlipNo, $lngRevisionNo, $objDB);
    $lngRevisionNo = $aryHeader["lngrevisionno"];
    // 帳票テンプレートに設定する納品書データの読み込み（明細部）
    $aryDetail = fncGetDetailBySlipNo($lngSlipNo, $lngRevisionNo, $objDB);

    // 帳票テンプレートに納品書データを設定したExcelのバイナリを生成するXlsxWriterを取得する
    $aryGenerateResult = fncGenerateReportImage("download", $aryHeader, $aryDetail,
        $lngSlipNo, $lngRevisionNo, $strSlipCode, $lngSalesNo, $dtmInsertDate,
        $objDB);
    $xlsxWriter = $aryGenerateResult["XlsxWriter"];

    // 印刷回数を増やす
    fncIncrementPrintCountBySlipCode($strSlipCode, $objDB);

    // MIMEタイプをセットしてダウンロード
    //MIMEタイプ：https://technet.microsoft.com/ja-jp/ee309278.aspx
    header("Content-Description: File Transfer");
    header('Content-Disposition: attachment; filename="weather.xlsx"');
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Transfer-Encoding: binary');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Expires: 0');
    ob_end_clean(); //バッファ消去

    // バイナリイメージをレスポンスとして返す
    $xlsxWriter->save('php://output');

    // 処理終了
    return true;

}

// 通常ここに来ることは無い（不明なモードでPOSTした場合ここに来る）
echo "不明なモードでPOSTされました";
return true;

// ヘルパ関数：jsonエンコード後にbase64エンコード
// base64変換するのは HTMLのhiddenフィールドに安全な形で格納するため。
function EncodeToJson($object)
{
    $json = base64_encode(json_encode($object));
    return $json;
}

// ヘルパ関数：base64デコード後にjsonデコード
function DecodeFromJson($json)
{
    $object = json_decode(base64_decode($json), true);
    return $object;
}

// ヘルパ関数：エラー画面への遷移
function MoveToErrorPage($strMessage)
{

    // 言語コード：日本語
    $aryHtml["lngLanguageCode"] = 1;

    // エラーメッセージの設定
    $aryHtml["strErrorMessage"] = $strMessage;

    // テンプレート読み込み
    $objTemplate = new clsTemplate();
    $objTemplate->getTemplate("/result/error/parts.tmpl");

    // テンプレート生成
    $objTemplate->replace($aryHtml);
    $objTemplate->complete();

    // HTML出力
    echo $objTemplate->strTemplate;

    exit;
}
