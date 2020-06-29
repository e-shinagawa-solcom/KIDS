<?php

/*
//****************************************************************************
// 01 売上見込
見出しの生成

 *    @param    object    $worksheet        ワークシートオブジェクト
 *    @param    object    $aryFormat        フォーマットオブジェクト
 *    @param    array    $aryDataset        データセット定義
 *    @param    integer    $j                列カウンター
 *    @param    array    $aryResult        データベース取得値
 *
 *    @return    integer                    見出し行数

//****************************************************************************
 */
function fncfncSpreadSheetCellHeader01(&$worksheet, &$objFormat, $aryData)
{
    $worksheet->writeString(0, 0, mb_convert_encoding('社内統計データ‐売上見込', 'shift_jis'), $objFormat);
    $worksheet->writeString(1, 0, mb_convert_encoding('受注納期　' . $aryData["dtmAppropriationDateFrom"] . '-' . $aryData["dtmAppropriationDateTo"], 'shift_jis'), $objFormat);
    $worksheet->writeString(2, 19, mb_convert_encoding('レートはデータダウンロード月はTTMレート、その後の2ヶ月は見積原価書のレート（色付きの単価が自動計算対象です）', 'shift_jis'), $objFormat);
    $worksheet->writeString(2, 0, mb_convert_encoding('選択', 'shift_jis'), $objFormat);
    $worksheet->writeString(2, 1, mb_convert_encoding('←A3のセルを選択して　｢Ctr｣｢Shift｣｢*｣同時に押すと　当シートの全データを選択されるようになれます', 'shift_jis'), $objFormat);

    return 3; // ヘッダーで使用した行数を返却
}

/*
//****************************************************************************
//    セル用データの生成
 *
 *    @param    array    $aryDataset        データセット定義
 *    @param    integer    $i                行カウンター
 *    @param    integer    $j                列カウンター
 *    @param    array    $aryResult        データベース取得値
 *    @param    integer    $lngHeadLineCnt    ヘッダー部の総合行数（データ部の始まる前までの行）
 *
 *    @return                            セル設定用データ
 *
// ・処理内での $j は xxxxx_dataset.txt のデータ数(0～）と一致している（データセット配列番号）
// ・$aryDataset[19]->data 等のデータは該当する配列番号が参照時 switch文よりも前に処理されているデータのみ参照可能
// ・aryResult["*****"] に指定するカラム名は小文字であること
//
//****************************************************************************
 */
function fncSpreadSheetCellData01($aryDataset, $i, $j, $aryResult, $lngHeadLineCnt)
{

    $strNo = (string) ($i + 1 + $lngHeadLineCnt); // エクセル上での計算用の行番号を設定
    $curCalc = 0;
    $varCellData = "";
    $strReceiveCode = '';

    // 置き換え用売上区分コード
    if (!isset($aryResult["lngsalesclasscode"])) {
        return;
    } else {
        switch ((int) $aryResult["lngsalesclasscode"]) {
            case 1:
                $SalesClassCode = 1;
                break;
            case 2:
            case 3:
            case 9:
                $SalesClassCode = 2;
                break;
            default:
                $SalesClassCode = 3;
        }
    }
    switch ((int) $j) {
        case 1: // 通し番号
            $varCellData = (int) $i + 1;
            break;
        case 2: // 計算用①
            //                $varCellData = '=IF($G'.$strNo.'="","",CONCATENATE($G'.$strNo.',$H'.$strNo.',$V'.$strNo.',$P'.$strNo.'))';
            //$varCellData = '=IF($G13="","",CONCATENATE($G13,$H13,$V13,$P13))';
            // 受注納期の年月[YYYYMM]＋製品コード[xxxx]＋売上区分[x] ⇒11桁
            $varCellData = substr($aryResult["dtmdeliverydate"], 0, 4) . substr($aryResult["dtmdeliverydate"], 5, 2) . $aryResult["strproductcode"] . $aryResult["lngsalesclasscode"];
            break;
        case 3: // 計算用②
            //                $varCellData = '=IF($G'.$strNo.'="","",CONCATENATE($G'.$strNo.',$H'.$strNo.',$V'.$strNo.'))';
            // 受注納期の年月[YYYYMM]＋製品コード[xxxx] ⇒10桁
            $varCellData = substr($aryResult["dtmdeliverydate"], 0, 4) . substr($aryResult["dtmdeliverydate"], 5, 2) . $aryResult["strproductcode"];
            break;
        case 4: // 計算用③
            //                $varCellData = '=IF($G'.$strNo.'="","",CONCATENATE($G'.$strNo.',$H'.$strNo.',$N'.$strNo.',$P'.$strNo.'))';
            // 受注納期の年月[YYYYMM]＋部門コード[xx]＋売上区分[x] ⇒9桁
            $varCellData = substr($aryResult["dtmdeliverydate"], 0, 4) . substr($aryResult["dtmdeliverydate"], 5, 2) . $aryResult["strgroupdisplaycode"] . $aryResult["lngsalesclasscode"];
            break;
        case 5: // 計算用④
            //                $varCellData = '=IF($G'.$strNo.'="","",CONCATENATE($G'.$strNo.',$H'.$strNo.',$N'.$strNo.'))';
            // 受注納期の年月[YYYYMM]＋部門コード[xx] ⇒8桁
            $varCellData = substr($aryResult["dtmdeliverydate"], 0, 4) . substr($aryResult["dtmdeliverydate"], 5, 2) . $aryResult["strgroupdisplaycode"];
            break;
        case 6: // 計算用⑤
            //                $varCellData = '=IF($R'.$strNo.'="","",YEAR($R'.$strNo.'))';
            //  受注納期の年[YYYY] ⇒4桁
            $varCellData = substr($aryResult["dtmdeliverydate"], 0, 4);
            break;
        case 7: // 計算用⑥
            //                $varCellData = '=IF($R'.$strNo.'="","",IF(MONTH($R'.$strNo.')<10,CONCATENATE("0",MONTH($R'.$strNo.')),MONTH($R'.$strNo.')))';
            // 受注納期の月[MM] ⇒2桁
            $varCellData = substr($aryResult["dtmdeliverydate"], 5, 2);
            break;
        case 8: // 受注NO.
            $strReceiveCode = $aryResult['strreceivecode'];
            $varCellData = $strReceiveCode;
            break;
        case 18: // 単価

            $curCalc = (float) $aryResult["curproductpricereceive"];

            // 日本円以外の場合
            if ((int) $aryResult["lngmonetaryunitcode"] != 1) {
                // レートを求める
                $curRate = (float) $aryResult["curconversionrate"];
                if ((int) $aryResult["lngproductunitcode"] == "2") {
                    // 単価 / カートン入数
                    $curCalc1 = (float) $curCalc / (int) $aryResult["lngcartonquantity"];
                }
                $curCalc1 = (float) $curCalc;

                $curCalc = '=' . (float) $curCalc1 . ' * ' . (float) $curRate . '';

                $varCellData = $curCalc;
                break;
            }

            // カートンの場合
            if ((int) $aryResult["lngproductunitcode"] == "2") {
                // 単価 / カートン入数
                $curCalc = (float) $curCalc / (int) $aryResult["lngcartonquantity"];
            }

            $curCalc = round((float) $curCalc, 2); // 小数点以下2桁で丸め処理
            $varCellData = $curCalc;

            break;

        case 19: // 単位
            //    1:pcs,  2:c/t,  3:set
            if ($aryResult["lngproductunitcode"] == "2") {
                $varCellData = "pcs";
            } elseif ($aryResult["lngproductunitcode"] == "1") {
                $varCellData = "pcs";
            } else {
                $varCellData = "set";
            }

            break;

        case 20: // 数量

            $varCellData = (int) $aryResult["lngproductquantity"];
            // c/t の場合は計算する
            if ($aryResult["lngproductunitcode"] == "2") {
                $varCellData = (int) $aryResult["lngproductquantity"] * (int) $aryResult["lngcartonquantity"];
            }

            break;
        case 10:
            // 置き換え用売上区分コード
            $varCellData = (float) $SalesClassCode;
            break;

        case 23: // 売上合計
            $curCalc = (int) $aryResult["cursubtotalprice"];
            // 日本円以外の場合
            if ((int) $aryResult["lngmonetaryunitcode"] != 1) {
                $curCalc = '=S' . $strNo . '*' . (int) $aryResult["lngproductquantity"] . '';
            }

            $varCellData = $curCalc;
            break;

        case 24: // 製品原価＠
            // AA列とUで逆算する（AA列/U列「計算式で埋める」小数点以下2桁で丸め処理)
            $curCalc = '=ROUND(AA' . $strNo . '/U' . $strNo . ', 2)';
            $varCellData = $curCalc;
            break;

        case 26: // 製品原価合計
            // AC列の利益率で逆算する（X列の売上合計×(1-利益率)「計算式で埋める」）
            $curCalc = '=ROUND(X' . $strNo . '*(1-AC' . $strNo . '), 0)';
            $varCellData = $curCalc; // 小数点以下桁で丸め処理
            break;

        case 27: // 目標利益
            // X列の売上合計×AC見込利益率,小数点以下2桁で丸め処理
            $curCalc = '=ROUND(X' . $strNo . '*AC' . $strNo . ', 2)';
            $varCellData = $curCalc;

            break;

        case 28: // 見込利益率
            // 売上分類が製品売上の場合は見積原価書の製品利益率の値を設定、固定費売上の場合は固定費利益率を設定にする。
            // 製品利益率 = (製品売上高-製造費用)/製品売上高の設定
            if ($aryResult["lngsalesdivisioncode"] == 2) {
                $curSalesProfitRate = $aryResult["cursalesamount"] == 0 ? 0.00 : round((float) (($aryResult["cursalesamount"] - $aryResult["curmanufacturingcost"]) / $aryResult["cursalesamount"]), 4);
                // var_dump($aryResult["cursalesamount"]);
                // var_dump("product sales" .$curSalesProfitRate);
                $varCellData = $curSalesProfitRate; // 小数点以下2桁で丸め処理
                break;
            } else if ($aryResult["lngsalesdivisioncode"] == 1) {
                // 固定費利益率 = (売上総利益 - 製品売上高 +  製造費用)/ 固定費売上高
                // var_dump($aryResult["curfixedcostsales"]);
                // var_dump($aryResult["curtotalprice"]);
                // var_dump($aryResult["cursalesamount"]);
                // var_dump($aryResult["curmanufacturingcost"]);
                $curFixedCostSalesProfitRate = $aryResult["curfixedcostsales"] == 0 ? 0 : round((float) (($aryResult["curtotalprice"] - $aryResult["cursalesamount"] + $aryResult["curmanufacturingcost"]) / $aryResult["curfixedcostsales"]), 4);

                // var_dump("fixed sales" .round((float) $curFixedCostSalesProfitRate, 4) .":".$aryResult["curfixedcostsales"]);
                // var_dump($curFixedCostSalesProfitRate);
                $varCellData = $curFixedCostSalesProfitRate; // 小数点以下2桁で丸め処理
                break;
            }

        case 29: // 単価
            //$varCellData = '=IF(T'.$strNo.'=2,S'.$strNo.'/Z'.$strNo.',S'.$strNo.')';
            // 日本円以外の場合
            if ((int) $aryResult["lngmonetaryunitcode"] != 1) {
                $curCalc = '=S' . $strNo . ')';
                $varCellData = $curCalc;
            } else {
                $varCellData = $aryDataset[18]->data;
            }
            break;
        case 31: // チーム別KIDS利益率
            // 売上区分コード判定
            if ((int) $aryResult["lngsalesclasscode"] == 1) {
                // 売上合計が0、目標利益が0、の場合は空
                if ($aryDataset[23]->data == 0 && $aryDataset[27]->data == 0) {
                    break;
                }

                // 予定売上高が0、の場合は空
                if ((float) $aryResult["cursalesamount"] == 0) {
                    break;
                }

                if (!isset($aryResult["cursalesamount"]) || !isset($aryResult["curproductprice"]) || !isset($aryResult["lngproductionquantity"])) {
                    break;
                }

                // グループコード判定
                switch ((int) $aryResult["lnginchargegroupcode"]) {
                    case 3: //キャンディチーム
                    case 4: //トーイチーム
                    case 27: //トイチーム(ガールズトイ)
                        // 部材費合計金額 / ＠製品売上高(納価 * 生産予定数)
                        $curCalc = 1 - (float) $aryResult["curmembercost"] / ((float) $aryResult["curproductprice"] * (int) $aryResult["lngproductionquantity"]);
                        break;
                    default:
                        // 総製造費用 / 予定売上高
                        $curCalc = 1 - (float) $aryResult["curmanufacturingcost"] / (int) $aryResult["cursalesamount"];
                }
            } else {
                break;
            }

            $varCellData = (float) $curCalc;
            break;
        case 32: // 受注備考
            // 受注明細行備考で埋め
            $varCellData = (string) $aryResult["strnote"];
            break;

        default:
            //
            // 上記の case に当てはまらないもの（SQL実行文のカラム名から一致するものを取得）
            //
            $strKey = strtolower($aryDataset[$j]->name); //$varCellData = preg_replace ( "/\s+?$/", "", $aryResult[$strKey] );// 空白削除
            $varCellData = (string) $aryResult[$strKey];

            //$varCellData = $aryResult[$strKey];
    }

    return $varCellData;

}

/*
//****************************************************************************
// 01 売上見込
セルの書式別・フォーマットオブジェクトの生成

 *    @param    object    $workbook        ワークブックオブジェクト
 *    @param    array    $aryFormat        フォーマットオブジェクト格納配列
 *    @param    array    $aryDataset        データセット定義
 *    @param    integer    $j                列カウンター
 *    @param    array    $aryResult        データベース取得値
 *
 *    @return    object    $objFormat        フォーマットオブジェクト

//****************************************************************************
 */
function fncSpreadSheetCellFormat01(&$workbook, &$aryFormat, &$aryDataset, $j, $aryResult)
{

    unset($objFormat);

    switch ((int) $j) {
        case 18: // 単価
            // 日本円以外の場合
            if ((int) $aryResult["lngmonetaryunitcode"] != 1) {
                // データタイプの変更（計算式）
                $aryDataset[$j]->type = '';
                $objFormat = &fncSpreadSheetCellFormat($workbook, $aryFormat, 'numberCalcDec');
                break;
            }
            $aryDataset[$j]->type = 'float';
            $objFormat = &fncSpreadSheetCellFormat($workbook, $aryFormat, 'numberDec');
            break;
        case 21: //製品CD
        case 22: //製品名
            $objFormat = &fncSpreadSheetCellFormat($workbook, $aryFormat, 'textPoint');
            break;

        case 23: // 売上合計
            // 日本円以外の場合
            if ((int) $aryResult["lngmonetaryunitcode"] != 1) {
                // データタイプの変更（計算式）
                $aryDataset[$j]->type = '';
                $objFormat = &fncSpreadSheetCellFormat($workbook, $aryFormat, 'numberCalc');
                break;
            }
            $aryDataset[$j]->type = 'float';
            $objFormat = &fncSpreadSheetCellFormat($workbook, $aryFormat, 'numberCalcPoint');
            break;

        // 小数点以下指定のカラム
        case 24: // 製品原価＠
            $aryDataset[$j]->type = '';
            $objFormat = &fncSpreadSheetCellFormat($workbook, $aryFormat, 'numberCalcDec');
            break;
        case 25: //カートン入数
            $objFormat = &fncSpreadSheetCellFormat($workbook, $aryFormat, 'numberCalcPoint');
            break;
        case 26: // 製品原価合計
            // データタイプの変更（計算式）
            $aryDataset[$j]->type = '';
            $objFormat = &fncSpreadSheetCellFormat($workbook, $aryFormat, 'numberCalc');
            break;
        case 27: //目標利益
            // データタイプの変更（計算式）
            $aryDataset[$j]->type = '';
            $objFormat = &fncSpreadSheetCellFormat($workbook, $aryFormat, 'numberCalc');
            break;
        case 28: // 見込利益率
        // var_dump($aryDataset[28]->data);
        if ($aryDataset[28]->data == 0) {
            $objFormat = &fncSpreadSheetCellFormat($workbook, $aryFormat, $aryDataset[$j]->type);
            break;
        } else {
            $objFormat = &fncSpreadSheetCellFormat($workbook, $aryFormat, 'numberPercent');
            break;
        }
        case 29: // 単価（計算式）
            if ((int) $aryResult["lngmonetaryunitcode"] != 1) {
                // データタイプの変更（計算式）
                $aryDataset[$j]->type = '';
                $objFormat = &fncSpreadSheetCellFormat($workbook, $aryFormat, 'numberCalcDec');
                break;
            }
            $aryDataset[$j]->type = 'float';
            $objFormat = &fncSpreadSheetCellFormat($workbook, $aryFormat, 'numberDec');
            break;
        case 30: //納価　//case 30追加 by kou 20090611
        case 33: //参考値上代
            $objFormat = &fncSpreadSheetCellFormat($workbook, $aryFormat, 'numberDec');
            break;

        case 31: // チーム別KIDS利益率
            $objFormat = &fncSpreadSheetCellFormat($workbook, $aryFormat, 'numberPercent');
            break;

        case 32: //受注備考
            $objFormat = &fncSpreadSheetCellFormat($workbook, $aryFormat, 'textPoint');
            break;
        default:
            $objFormat = &fncSpreadSheetCellFormat($workbook, $aryFormat, $aryDataset[$j]->type);
    }

    return $objFormat;

}
