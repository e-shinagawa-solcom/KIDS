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
    $worksheet->writeNumber(2, 18, (int) 2, $objFormat); // 初期値を社内レート
    $worksheet->writeString(2, 19, mb_convert_encoding('←S3のセル　1:ＴＴＭレート、2:社内レート（色付きの単価が自動計算対象です）', 'shift_jis'), $objFormat);
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
//            case 9:    // 顧客受注番号
        //            case 12:    // カテゴリー名称
        //            case 14:    // 部門（名称）
        //            case 16:    // 売上区分（名称）
        /*
        case 18:    // 単価

        $curCalc = (float)$aryResult["curproductpricereceive"];
        //by kou 20090611                $curCalc = round( (float)$curCalc, 2);    // 小数点以下2桁で丸め処理

        // カートンの場合
        if( $aryResult["lngproductunitcode"] == "2" )
        {
        // 単価 / カートン入数
        $curCalc = $curCalc / (int)$aryResult["lngcartonquantity"];
        }

        // 日本円以外の場合
        if( (int)$aryResult["lngmonetaryunitcode"] != 1 )
        {
        // レートを求める
        if( (int)$aryResult["lngmonetaryratecode"] == 1 )
        {
        $curRate = (float)$aryResult["curconversionrate1"];
        }
        elseif( (int)$aryResult["lngmonetaryratecode"] == 2 )
        {
        $curRate = (float)$aryResult["curconversionrate2"];
        }

        // =IF(S3="",9999, IF(S3=1,1*1, IF(S3=2,2*2)))
        //$curCalc = '=IF(S3=0, '.(float)$aryResult["curproductpricereceive"].' * '. $curRate .', IF(S3=1, ' . (float)$aryResult["curproductpricereceive"] .' * '. (float)$aryResult["curconversionrate1"] . ', IF(S3=2, '. (float)$aryResult["curproductpricereceive"] .' * '. (float)$aryResult["curconversionrate2"] . ', 0)))';
        $curCalc = '=IF(S3=1, ' . $curCalc .' * '. (float)$aryResult["curconversionrate1"] . ', IF(S3=2, '. $curCalc .' * '. (float)$aryResult["curconversionrate2"] . ', 0))';
        }
        $curCalc = round( (float)$curCalc, 2);    // 小数点以下2桁で丸め処理
        $varCellData = $curCalc;

        break;

         */

        case 18: // 単価

            $curCalc = (float) $aryResult["curproductpricereceive"];
//by kou 20090611                $curCalc = round( (float)$curCalc, 2);    // 小数点以下2桁で丸め処理

            // 日本円以外の場合
            if ((int) $aryResult["lngmonetaryunitcode"] != 1) {
                // レートを求める
                //                    if( (int)$aryResult["lngmonetaryratecode"] == 1 )
                //                    {
                $curRate1 = (float) $aryResult["curconversionrate1"];
//                    }
                //                    elseif( (int)$aryResult["lngmonetaryratecode"] == 2 )
                //                    {
                $curRate2 = (float) $aryResult["curconversionrate2"];
//                    }
                if ((int) $aryResult["lngproductunitcode"] == "2") {
                    // 単価 / カートン入数
                    $curCalc1 = (float) $curCalc / (int) $aryResult["lngcartonquantity"];
                }

                $curCalc1 = (float) $curCalc;

                // =IF(S3="",9999, IF(S3=1,1*1, IF(S3=2,2*2)))
                //$curCalc = '=IF(S3=0, '.(float)$aryResult["curproductpricereceive"].' * '. $curRate .', IF(S3=1, ' . (float)$aryResult["curproductpricereceive"] .' * '. (float)$aryResult["curconversionrate1"] . ', IF(S3=2, '. (float)$aryResult["curproductpricereceive"] .' * '. (float)$aryResult["curconversionrate2"] . ', 0)))';
                $curCalc = '=IF(S3=1, ' . (float) $curCalc1 . ' * ' . (float) $curRate1 . ', IF(S3=2, ' . (float) $curCalc1 . ' * ' . (float) $curRate2 . ', 0))';
//                    $curCalc = (float)$curCalc1;
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

//            case 22: // 製品名称
        //                $varCellData = "";
        //                break;
        case 10:
            // 置き換え用売上区分コード
            $varCellData = (float) $SalesClassCode;
            break;

        case 23: // 売上合計 curproductprice * lngproductquantity
            /*                $curCalc = (float)$aryResult["curproductpricereceive"] * (int)$aryResult["lngproductquantity"];
            $curCalc = floor($curCalc);    // 小数点以下切り捨て
             */
            $curCalc = (int) $aryResult["cursubtotalprice"];
            // 日本円以外の場合
            if ((int) $aryResult["lngmonetaryunitcode"] != 1) {
                $curCalc = '=S' . $strNo . '*' . (int) $aryResult["lngproductquantity"] . '';
            }

            $varCellData = $curCalc;
            break;

        case 24: // 製品原価＠
            //    if( !fncSpreadSheetExcelFormatSetting($workbook, "setHAlignRight", $objFormat) ) $objFormat = null;

            // 部材費合計金額 が無かったら
            //売上区分本荷の場合は製品原価0であればエラー
            //部材費用から　総費用に変更
            //                if( (float)$aryResult["curmembercost"]==0 && (int)$aryResult["lngsalesclasscode"] == 1)
            if ((float) $aryResult["curmanufacturingcost"] == 0 && (int) $aryResult["lngsalesclasscode"] == 1) {
                $varCellData = 'ERR';
                break;
            }
            //生産予定数単位コードは2（C/T）の場合は　数量*カートン入数=pcs数
            if ((int) $aryResult["lngproductionunitcode"] == 2) {
                $ProductionQuantity = (int) $aryResult["lngproductionquantity"] * (int) $aryResult["lngcartonquantity"];
            } else {
                $ProductionQuantity = (int) $aryResult["lngproductionquantity"];

            }
            // 売上区分コード判定
            if ((int) $aryResult["lngsalesclasscode"] == 1) {
                // グループコード判定
                switch ((int) $aryResult["lnginchargegroupcode"]) {
                    case 3: //キャンディチーム
                    case 4: //トーイチーム
                    case 27: //トイチーム(ガールズトイ)
                        // 部材費合計金額 / 生産予定数
                        $curCalc = (float) $aryResult["curmembercost"] / (float) $ProductionQuantity;
                        break;
                    default:
                        // 総製造費用 / 生産予定数
                        $curCalc = (float) $aryResult["curmanufacturingcost"] / (float) $ProductionQuantity;
                }
            } else { // 単価
                //                    $curCalc = (float)$aryResult["curproductpricesales"];
                if ((int) $aryResult["lngmonetaryunitcode"] != 1) {
                    $curCalc = '=S' . $strNo . ''; //'*'.(int)$aryResult["lngproductquantity"].'';
                    $varCellData = $curCalc;
                    break;
                }
                $curCalc = (float) $aryDataset[18]->data;
            }
            $varCellData = round((float) $curCalc, 2); // 小数点以下2桁で丸め処理
            break;

        case 26: // 製品原価合計
            //    if( !fncSpreadSheetExcelFormatSetting($workbook, "setHAlignRight", $objFormat) ) $objFormat = null;
            //売上区分本荷の場合は製品原価0であればエラー
            //部材費用から　総費用に変更
            //                if( (float)$aryResult["curmembercost"]==0 && (int)$aryResult["lngsalesclasscode"] == 1)
            if ((float) $aryResult["curmanufacturingcost"] == 0 && (int) $aryResult["lngsalesclasscode"] == 1) {
                $varCellData = 'ERR';
//                    $varCellData = mb_convert_encoding('=IF($Y'.$strNo.'="ERR","ERR",$U'.$strNo.'*$Y'.$strNo.')', "shift_jis");
                break;
            }
            //生産予定数単位コードは2（C/T）の場合は　数量*カートン入数=pcs数
            if ((int) $aryResult["lngproductionunitcode"] == 2) {
                $ProductionQuantity = (int) $aryResult["lngproductionquantity"] * (int) $aryResult["lngcartonquantity"];
            } else {
                $ProductionQuantity = (int) $aryResult["lngproductionquantity"];

            }
            // 売上区分コード判定
            if ((int) $aryResult["lngsalesclasscode"] == 1) {

                // グループコード判定
                switch ((int) $aryResult["lnginchargegroupcode"]) {
                    case 3: //キャンディチーム
                    case 4: //トーイチーム
                    case 27: //トイチーム(ガールズトイ)
                        // 部材費合計金額 / 生産予定数
                        $curCalc1 = (float) $aryResult["curmembercost"] / (float) $ProductionQuantity;
                        break;
                    default:
                        // 総製造費用 / 生産予定数
                        $curCalc1 = (float) $aryResult["curmanufacturingcost"] / (float) $ProductionQuantity;
                }
                // c/t
                if ((int) $aryResult["lngproductunitcode"] == 2) {
                    // 数量 * 製品原価＠ * カートン入数
                    //                        $curCalc = (int)$aryResult["lngproductquantity"] * (float)$aryDataset[24]->data * (int)$aryResult["lngcartonquantity"];///////////////////
                    $curCalc = (int) $aryResult["lngproductquantity"] * (float) $curCalc1 * (int) $aryResult["lngcartonquantity"]; /////
                } else {
                    // 数量 * 製品原価＠
                    //                        $curCalc = (int)$aryResult["lngproductquantity"] * (float)$aryDataset[24]->data;///////////////////
                    $curCalc = (int) $aryResult["lngproductquantity"] * (float) $curCalc1; ///////////////////

                }
            } else {
                if ((int) $aryResult["lngmonetaryunitcode"] != 1) {
                    $curCalc = '=S' . $strNo . '*' . (int) $aryResult["lngproductquantity"] . '';
                    $varCellData = $curCalc;
                    break;
                }
                // 数量 * 製品原価＠
                $curCalc = (int) $aryDataset[23]->data; ///////////////////
            }
//                $varCellData = floor((float)$curCalc);    // 小数点以下切り捨て
            $varCellData = round((float) $curCalc, 0); // 小数点以下桁で丸め処理
            break;

        case 27: // 目標利益
            // 日本円以外の場合
            if ((int) $aryResult["lngmonetaryunitcode"] != 1) {
				$curCalc = '=X' . $strNo . '-AA' . $strNo. '';
				$varCellData = $curCalc;
            } else {
                // 売上合計が0では無い
                if ($aryDataset[23]->data != 0 && $aryDataset[26]->data != "ERR") {
                    // 売上合計 - 製品原価合計
                    $curCalc = (float) $aryDataset[23]->data - $aryDataset[26]->data;
                }
                $varCellData = round((float) $curCalc, 2); // 小数点以下2桁で丸め処理
            }

            break;

        case 28: // 見込利益率
			// 目標利益が0では無い、売上合計と製品原価合計が同一では無い
			// 日本円以外の場合
            if ((int) $aryResult["lngmonetaryunitcode"] != 1) {
				$curCalc = '=if(OR(AB'.$strNo .'=0 , X'.$strNo . '=AA'.$strNo .'), "" , AB'. $strNo. '/X'.$strNo .')';
				$varCellData = $curCalc;
			} else {
				if ($aryDataset[27]->data != 0 && $aryDataset[23]->data != $aryDataset[26]->data) {
					//目標利益 / 売上合計
					$curCalc = $aryDataset[27]->data / $aryDataset[23]->data;
					$varCellData = round((float) $curCalc, 4); // 小数点以下2桁で丸め処理
					break;
				}
			}
//                $varCellData = "1212";
            break;

        case 29: // 単価
			//$varCellData = '=IF(T'.$strNo.'=2,S'.$strNo.'/Z'.$strNo.',S'.$strNo.')';
			// 日本円以外の場合
            if ((int) $aryResult["lngmonetaryunitcode"] != 1) {
				$curCalc = '=S'.$strNo .')';
				$varCellData = $curCalc;
			} else {
				$varCellData = $aryDataset[18]->data;
			}
            break;

//            case 30:    // 納価
        //                $varCellData = (float)$aryResult["curproductprice"];
        //                $varCellData = 0;
        //                break;

/*            case 31:    // チーム別KIDS利益率
// 売上合計が0、目標利益が0、の場合は空
if( $aryDataset[23]->data == 0 && $aryDataset[27]->data == 0 ) break;
// 予定売上高が0、の場合は空
if( (float)$aryResult["cursalesamount"] == 0 ) break;
// グループコード判定
switch((int)$aryResult["lnginchargegroupcode"])
{
case 3:        //キャンディチーム
case 4:        //トーイチーム
case 27:    //トイチーム(ガールズトイ)
// 部材費合計金額 / ＠製品売上高(納価 * 生産予定数)
$curCalc = 1- (float)$aryResult["curmembercost"] / ((int)$aryResult["curproductprice"] * (int)$aryResult["lngProductionQuantity"]);
break;
default:
// 総製造費用 / 予定売上高
$curCalc = 1- (float)$aryResult["curmanufacturingcost"] / (int)$aryResult["cursalesamount"];
}

$varCellData = (float)$curCalc;
break;
 */

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
//                            $curCalc = (float)$aryResult["curproductprice"];
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
        case 32: // 納価相違
            /*
            // 売上区分コード判定
            // 単価（単位が’c/t’の場合、単価 / カートン入数）と納価が同一であれば ‘合’、異なれば ‘否’
            if( (int)$aryResult["lngsalesclasscode"] == 1 )
            {
            // c/t
            if( (int)$aryResult["lngproductunitcode"] == 2 )
            {
            $curCalc = (float)$aryResult["curproductprice"] / (int)$aryResult["lngcartonquantity"];
            if(  ) varCellData = '';
            }
            else
            {
            }

            }
             */
            $varCellData = mb_convert_encoding('=IF($V' . $strNo . '="","", IF($P' . $strNo . '=1, IF($AD' . $strNo . '=$AE' . $strNo . ',"○","×"),"") )', "shift_jis");
            break;
        //case 33:    // 参考値上代
        //    $varCellData = "";
        //    break;

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
            // 売上区分本荷の場合は製品原価0であればエラー
            //部材費用から　総費用に変更
            //                if( (float)$aryResult["curmembercost"]==0 && (int)$aryResult["lngsalesclasscode"] == 1)
            if ((float) $aryResult["curmanufacturingcost"] == 0 && (int) $aryResult["lngsalesclasscode"] == 1) {
                // ERR の場合
                if ($aryDataset[$j]->data == 'ERR') {
                    // データタイプの変更（計算式）
                    $aryDataset[$j]->type = 'text';
                    $objFormat = &fncSpreadSheetCellFormat($workbook, $aryFormat, 'errorNumberDec');
                    break;
                }

                // データタイプの変更（計算式）
                $aryDataset[$j]->type = 'text';
                $objFormat = &fncSpreadSheetCellFormat($workbook, $aryFormat, 'float');
                break;
            }
            if ((int) $aryResult["lngmonetaryunitcode"] != 1 && (int) $aryResult["lngsalesclasscode"] != 1) {
                // データタイプの変更（計算式）
                $aryDataset[$j]->type = '';
                $objFormat = &fncSpreadSheetCellFormat($workbook, $aryFormat, 'numberCalcDec');
                break;
            }
            $aryDataset[$j]->type = 'float';
            $objFormat = &fncSpreadSheetCellFormat($workbook, $aryFormat, 'numberDecPoint');
            break;
            $aryDataset[$j]->type = 'float';
            $objFormat = &fncSpreadSheetCellFormat($workbook, $aryFormat, 'numberDecPoint');
            break;
        case 25: //カートン入数
            $objFormat = &fncSpreadSheetCellFormat($workbook, $aryFormat, 'numberCalcPoint');
            break;
        case 26: // 製品原価合計
            //売上区分本荷の場合は製品原価0であればエラー
            //部材費用から　総費用に変更
            //                if( (float)$aryResult["curmembercost"]==0 && (int)$aryResult["lngsalesclasscode"] == 1)
            if ((float) $aryResult["curmanufacturingcost"] == 0 && (int) $aryResult["lngsalesclasscode"] == 1) {
                // ERR の場合
                if ($aryDataset[$j]->data == 'ERR') {
                    // データタイプの変更（計算式）
                    $aryDataset[$j]->type = 'text';
                    $objFormat = &fncSpreadSheetCellFormat($workbook, $aryFormat, 'errorNumber');
                    break;
                }

                // データタイプの変更（計算式）
                $aryDataset[$j]->type = 'text';
                $objFormat = &fncSpreadSheetCellFormat($workbook, $aryFormat, 'float');
                break;
            }
            if ((int) $aryResult["lngmonetaryunitcode"] != 1 && (int) $aryResult["lngsalesclasscode"] != 1) {
                // データタイプの変更（計算式）
                $aryDataset[$j]->type = '';
                $objFormat = &fncSpreadSheetCellFormat($workbook, $aryFormat, 'numberCalc');
                break;
            }
//                $aryDataset[$j]->type = 'float';
            //                $objFormat =& fncSpreadSheetCellFormat($workbook, $aryFormat, 'numberDecPoint');
            //                break;
            $aryDataset[$j]->type = 'float';
            $objFormat = &fncSpreadSheetCellFormat($workbook, $aryFormat, 'numberCalcPoint');
            break;
		case 27: //目標利益
		
            // 日本円以外の場合
            if ((int) $aryResult["lngmonetaryunitcode"] != 1) {
                // データタイプの変更（計算式）
                $aryDataset[$j]->type = '';
                $objFormat = &fncSpreadSheetCellFormat($workbook, $aryFormat, 'numberCalc');
                break;
			}
            $aryDataset[$j]->type = 'float';
            if ($aryDataset[26]->data == 'ERR') {
                if ((float) $aryResult["curmembercost"] == 0 && (int) $aryResult["lngsalesclasscode"] == 1) {
                    {
                        // データタイプの変更（計算式）
                        //                        $aryDataset[$j]->type = 'text';
                        $objFormat = &fncSpreadSheetCellFormat($workbook, $aryFormat, 'errorNumber');
                        break;
                    }break;
                }
            }

            $objFormat = &fncSpreadSheetCellFormat($workbook, $aryFormat, 'numberCalcPoint');
            break;
		case 28: // 見込利益率
		
            // 日本円以外の場合
            if ((int) $aryResult["lngmonetaryunitcode"] != 1) {
                // データタイプの変更（計算式）
                $aryDataset[$j]->type = '';
                $objFormat = &fncSpreadSheetCellFormat($workbook, $aryFormat, 'numberCalcPercent');
                break;
			}
            $aryDataset[$j]->type = 'float';

            if ($aryDataset[27]->data == 0) {
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

        default:
            $objFormat = &fncSpreadSheetCellFormat($workbook, $aryFormat, $aryDataset[$j]->type);
    }

    return $objFormat;

}
