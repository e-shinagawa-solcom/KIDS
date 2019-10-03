<?php

/**
 * L/Cデータ取得処理
 *
 * @param [object] $objDB
 * @param [object] $lcModel
 * @param [string] $usrId
 * @param [string] $date
 * @param [string] $time
 * @return void
 */
function fncGetLcData($objDB, $lcModel, $usrId, $date, $time)
{
    // 取得日の取得
    $lcGetDate = $lcModel->getMaxLcGetDate();
    $lcGetDateArry = explode(" ", $lcGetDate);
    $lcGetDate_date = $lcGetDateArry[0];
    $lcGetDate_time = $lcGetDateArry[1];

    // 発注件数の取得
    $orderCount = fncGetPurchaseOrderCount($objDB, $lcGetDate);

    // リバイズ情報の初期化
    $reviseDataArry = array();

    // 発注件数 > 0 の場合、t_aclcinfoへデータの登録・更新処理
    if ($orderCount > 0) {
        // t_aclcinfoデータの削除
        $lcModel->deleteAcLcInfo($lcGetDate_date, $lcGetDate_time);

        // 発注書データを取得する
        $orderArry = fncGetPurchaseOrderData($objDB, $lcGetDate);
        // 若納品日の初期化
        $strWorkDate = "9999/99/99";

        foreach ($orderArry as $orderData) {
            $pono = $orderData["lngpurchaseorderno"];
            $poreviseno = $orderData["lngrevisionno"];
            $intPayFlg = false;
            $payconditioncode = $orderData["lngpayconditioncode"];

            // 発注明細データを取得する
            $orderDetailArry = fncGetOrderDetail($objDB, $pono, $poreviseno);
            // // ワークフロー状態を取得する
            // $strDataState = fncWorkFlowStatus($orderData);
            // 発注データのリビジョン番号 < 0の場合
            if ($poreviseno < 0) {
                // t_aclcinfoの状態を削除に更新する
                $lcModel->updateAcLcStateToDelete($pono, $strDataState);
            } else {
                // 発注データの支払条件コード = 2 かつ 発注データのリビジョン番号 <> 0の場合
                if ($payconditioncode == DEF_PAYCONDITION_TT && $poreviseno != 0) {
                    // t_aclcinfoに同一Ponoが存在しているかをチェックする
                    $acLcCount = $lcModel->getAcLcCount($pono);
                    if ($acLcCount > 0) {
                        $intPayFlg = true;
                    }
                }

                // 発注データのリビジョン番号 <> 0の場合
                if ($poreviseno　!= 0) {
                    // t_aclcinfoより最新リバイズデータのオープン月と銀行依頼日を取得する
                    $acLcInfoArry = $lcModel->getAcLcInfoByPono($pono);
                }
            }

            if (count($orderDetailArry) > 0) {
                foreach ($orderDetailArry as $orderDetailData) {
                    $dtmdeliverydate = $orderDetailData["dtmdeliverydate"];
                    // 発注データの支払条件コード = 1 あるいは ( 発注データの支払条件コード = 2 かつ同一Pono既にありの場合）
                    if (payconditioncode == DEF_PAYCONDITION_LC || ($payconditioncode == DEF_PAYCONDITION_TT && $intPayFlg)) {
                        // po行番号の設定
                        $lngsortkey = $orderDetailArry["lngsortkey"];
                        $sortKeylen = strlen($lngsortkey);
                        if ($sortKeylen == 1) {
                            $polineno = sprintf("%02s", $lngsortkey);
                        }
                        if ($sortKeylen == 2) {
                            $polineno = $lngsortkey;
                        }
                        if ($sortKeylen == 3) {
                            if (substr($lngsortkey, 1, 1) == 0) {
                                $polineno = sprintf("%02s", substr($lngsortkey, 2, 1));
                            } else {
                                $polineno = substr($lngsortkey, 1, 2);
                            }
                        }
                        // 納品場所名称と荷揚地の取得
                        $companyNameAndCountryName = fncGetCompanyNameAndCountryName($objDB, $orderArry["lngdeliveryplacecode"]);

                        // 状態の設定
                        // 発注明細データの納品日 < 発注データ.登録日
                        if ($dtmdeliverydate < $orderData["dtminsertdate"]) {
                            $lcstate = 3;
                        } else {
                            $lcstate = 0;
                        }
                        // 発注データの支払条件コード = 2 かつ同一Pono既にありの場合
                        if ($payconditioncode == DEF_PAYCONDITION_TT && $intPayFlg) {
                            $lcstate = 9;
                        }

                        // 若納品日の取得
                        if ($dtmdeliverydate != null && $dtmdeliverydate < $strWorkDate) {
                            $strWorkDate = $dtmdeliverydate;
                        }

                        // L/Cデータの重複チェックを行う
                        $poupdatedate = $lcModel->getPoUpdateDate($pono, $polineno, $poreviseno);
                        if ($poupdatedate != null) {
                            // 更新日 <  発注データの登録日の場合
                            if ($poupdatedate < $orderData["dtminsertdate"]) {

                                if ($lcstate == 0) {
                                    $bankReqDate = $lcModel->getAcLcBankReqDate($pono);
                                    if ($bankReqDate != "") {
                                        $lcstate = 7;
                                    }
                                }

                                // t_aclcinfoの更新日を更新する
                                $lcModel->updateAcLcUpdatedate($pono, $polineno, $poreviseno, $lcstate);
                            }
                        } else {
                            $reviseNum = 0;
                            if ($orderData["lngrevisionno"] != 0) {
                                $reviseDataArry[$reviseNum]["pono"] = $pono;
                                $reviseDataArry[$reviseNum]["polineno"] = $polineno;
                                $reviseDataArry[$reviseNum]["poreviseno"] = $poreviseno;
                                $reviseDataArry[$reviseNum]["money"] = $orderDetailData["cursubtotalprice"];
                                $reviseDataArry[$reviseNum]["state"] = $lcstate;
                                $reviseNum += 1;
                            }

                            // t_aclcinfoにデータを登録する
                            $data = array();
                            $data["pono"] = $pono;
                            $data["polineno"] = $polineno;
                            $data["poreviseno"] = $poreviseno;
                            $data["postate"] = "承認済";
                            $data["opendate"] = date("Ym");
                            $data["unloadingareas"] = $companyNameAndCountryName["strcountryenglishname"];
                            $payfcd = fncGetMasterValue("m_company", "lngcompanycode", "strcompanyDisplaycode", $orderData["lngcustomercode"], '', $objDB);
                            $data["payfcd"] = $payfcd;
                            $payfinfo = $lcModel->getAcPayfInfo($payfcd);
                            $data["payfnameomit"] = $payfinfo["payfnameomit"];
                            $data["payfnameformal"] = $payfinfo["payfnameformal"];
                            $data["productcd"] = $orderData["strproductcode"];
                            $data["productname"] = $orderData["strproductname"];
                            $data["productnamee"] = orderData["strproductenglishname"];
                            $data["productnumber"] = $orderDetailData["lngproductquantity"];
                            $data["unitname"] = $orderDetailData["strproductunitname"];
                            $data["unitprice"] = $orderDetailData["curproductprice"];
                            $data["moneyprice"] = $orderDetailData["cursubtotalprice"];
                            $data["shipstartdate"] = $orderDetailData["dtmdeliverydate"];
                            $data["shipenddate"] = $orderDetailData["dtmdeliverydate"];
                            $data["sumdate"] = $orderDetailData["dtmappropriationdate"];
                            $data["poupdatedate"] = $orderDetailData["dtminsertdate"];
                            $data["deliveryplace"] = $companyNameAndCountryName["strCompanyDisplayName"];
                            $data["currencyclass"] = $orderData["strmonetaryunitsign"];
                            $data["lcnote"] = $orderData["strnote"];
                            $dtmdeliverydate = $orderDetailData["dtmdeliverydate"];
                            $data["shipterm"] = date("Y/m/d", $dtmdeliverydate . strtotime("+10 day"));
                            $data["validterm"] = date("Y/m/d", $dtmdeliverydate . strtotime("+20 day"));
                            // $data["bankcd"] = "";
                            // $data["bankname"] = "";
                            // $data["bankreqdate"] = "";
                            // $data["lcno"] = "";
                            // $data["lcamopen"] = "";
                            $data["validmonth"] = "";
                            // $data["usancesettlement"] = "";
                            // $data["bldetail1date"] = "";
                            // $data["bldetail1money"] = "";
                            // $data["bldetail2date"] = "";
                            // $data["bldetail2money"] = "";
                            // $data["bldetail3date"] = "";
                            // $data["bldetail3money"] = "";
                            $data["lcstate"] = $lcstate;
                            $data["entryuser"] = $usrId;
                            $data["entrydate"] = $date;
                            $data["entrytime"] = $time;
                            $data["updateuser"] = $usrId;
                            $data["updatedate"] = $date;
                            $data["updatetime"] = $time;
                            $data["shipym"] = date("Ym", $dtmdeliverydate . strtotime("+0 day"));
                            $lcModel->insertAcLcInfo($data);
                        }
                    } else {
                        $lcModel->updateAcLcStateByLcState($pono);
                    }
                }
            }
        }

        // 発注明細のオープン月設定
        // 基準日の取得
        $baseDate = $lcModel->getBaseDate();
        if ($baseDate < substr($strWorkDate, 8, 2)) {
            $opendate = date("Ym", $strWorkDate . strtotime("+0 day"));
        } else {
            $opendate = date("Ym", $strWorkDate . strtotime("-1 month"));
        }
        $lcModel->updateAcLcOpendate($pono, $opendate);

    }

    // リバイズ情報があった場合、リバイズ情報継承処理を行う
    if (count($reviseDataArry) > 0) {
        foreach ($reviseDataArry as $reviseData) {
            fncSetRevData($reviseData);
        }
    }

    // リバイズ情報の状態変更処理
    fncUpdRevState($lcModel, $reviseDataArry);

    // 未承認L/C情報の状態更新処理
    fncUpdUnapprovedLcInfo($lcModel); ////////////////////////時間かかり

    // 削除データ復活判定処理
    fncRevivalDeletedLcInfo($objDB, $lcModel);

}

/**
 * リバイズデータの継続処理
 *
 * @param [object] $reviseData
 * @return void
 */
function fncSetRevData($lcModel, $reviseData)
{
    // t_aclcinfoよりデータを取得する
    $reviseLcInfoArry = $lcModel->getReviseAcLcInfo($reviseData["pono"], $reviseData["polineno"]);
    $argIntAmdFlg = 0;
    $revDataArry = array();

    foreach ($reviseLcInfoArry as $reviseLcInfo) {
        // 上記で取得した銀行依頼日が空ではない場合
        if ($reviseLcInfo["bankreqdate"] != "") {
            if (count($revDataArry) == 0) {
                $revDataArry[0] = $reviseLcInfo;
            }
        }
        // 上記で取得した銀行依頼日が空ではない、かつ　リバイズ情報のporeviseno <> "00"の場合
        if ($reviseLcInfo["bankreqdate"] != "" && $reviseData["poreviseno"] != "00") {
            $argIntAmdFlg = 1;
            break;
        }
    }

    if (count($revDataArry) == 0) {
        // リバイズ情報のpolineno > 1の場合
        if ($reviseData["polineno"] > 1) {
            reset($reviseLcInfoArry);
            $reviseLcInfoArry = $lcModel->getReviseAcLcInfo($reviseData["pono"], sprintf("%02s", $reviseData["polineno"] - 1));

            foreach ($reviseLcInfoArry as $reviseLcInfo) {
                // 上記で取得した銀行依頼日が空ではない場合
                if ($reviseLcInfo["bankreqdate"] != "") {
                    if (count($revDataArry) == 0) {
                        $revDataArry[0] = $reviseLcInfo;
                    }
                }
                // 上記で取得した銀行依頼日が空ではない、かつ　リバイズ情報のporeviseno <> "00"の場合
                if ($reviseLcInfo["bankreqdate"] != "" && $reviseData["poreviseno"] != "00") {
                    $argIntAmdFlg = 1;
                    break;
                }
            }
        }
    }
    // リバイズ情報にデータがあった場合
    if (count($revDataArry) != 0) {
        foreach ($revDataArry as $revData) {
            // t_aclcinfoにデータを更新する
            $data = array();
            $data["pono"] = $reviseData["pono"];
            $data["polineno"] = $reviseData["polineno"];
            $data["poreviseno"] = $reviseData["poreviseno"];

            $data["bankcd"] = ($revData["bankcd"] == "") ? null : $revData["bankcd"];
            $data["bankname"] = ($revData["bankname"] == "") ? null : $revData["bankname"];
            $data["bankreqdate"] = null;
            $data["lcno"] = ($revData["lcno"] == "") ? null : $revData["lcno"];
            $data["lcamopen"] = null;
            $data["validmonth"] = null;
            $data["usancesettlement"] = ($revData["usancesettlement"] == "") ? null : $revData["usancesettlement"];
            $data["bldetail1date"] = ($revData["bldetail1date"] == "") ? null : $revData["bldetail1date"];
            $data["bldetail1money"] = ($revData["bldetail1money"] == "") ? null : $revData["bldetail1money"];
            $data["bldetail2date"] = ($revData["bldetail2date"] == "") ? null : $revData["bldetail2date"];
            $data["bldetail2money"] = ($revData["bldetail2money"] == "") ? null : $revData["bldetail2money"];
            $data["bldetail3date"] = ($revData["bldetail3date"] == "") ? null : $revData["bldetail3date"];
            $data["bldetail3money"] = ($revData["bldetail3money"] == "") ? null : $revData["bldetail3money"];
            if ($argIntAmdFlg && ($reviseData["lcstate"] == 0 || $reviseData["lcstate"] == 4 || $reviseData["lcstate"] == 8)) {
                $data["lcstate"] = 7;
            } else {
                $data["lcstate"] = $reviseData["lcstate"];
            }
            $lcModel->updateReviseAcLcInfo($data);
        }
    }
    return true;
}

/**
 * リバイズ状態変更処理
 *
 * @param [array] $reviseDataArry
 * @return void
 */
function fncUpdRevState($lcModel, $reviseDataArry)
{
    $orderno = "";
    foreach ($reviseDataArry as $reviseData) {
        // 発注番号 <> リバイズ情報のponoの場合
        if ($orderno != $reviseData["pono"]) {
            // t_aclcinfoにデータを更新する
            $lcModel->updateAcLcStateToRevise($reviseData["pono"], $reviseData["poreviseno"]);

            $orderno = $reviseData["pono"];
        }
    }
    return true;
}

/**
 * 未承認L/C情報ステータスを更新する
 *
 * @param [object] $lcModel
 * @return void
 */
function fncUpdUnapprovedLcInfo($lcModel)
{
    // 未承認のaclcinfoデータを取得する
    $unapprovedLcArry = $lcModel->getUnapprovedAcLcInfo();

    foreach ($unapprovedLcArry as $unapprovedLc) {
        $postate = "承認済";
        // 状態の設定
        if ($unapprovedLc["shipstartdate"] < $unapprovedLc["poupdatedate"]) {
            $lcstate = 3;
        } else {
            $lcstate = 0;
        }

        if ($lcstate == 0) {
            if ($unapprovedLc["bankreqdate"] != "" && $unapprovedLc["poreviseno"] != "00") {
                $bankreqdate = $unapprovedLc["bankreqdate"];
                $lcstate = 7;
            }
        }
        // L/C状態を更新する
        // $lcModel->updateUnapprovedAcLcState($unapprovedLc["pono"], $unapprovedLc["polineno"],
        //     $unapprovedLc["poreviseno"], $lcstate, $postate);

    }
    return true;
}

/**
 * 削除データ復活判定処理
 *
 * @param [object] $objDB
 * @param [object] $lcModel
 * @return void
 */
function fncRevivalDeletedLcInfo($objDB, $lcModel)
{
    // 削除復活データのPO番号を取得する
    $orderDataArry = fncGetDeletedPurchaseOrderData($objDB);
    if (!$orderDataArry) {
        return;
    }

    if (count($orderDataArry) > 0) {
        foreach ($orderDataArry as $orderData) {
            $lcinfoArry = $lcModel->getAcLcInfoByPono($orderData["pono"]);
            $preporeviseno = "";
            $poreviseno = "";
            if (count($lcinfoArry) > 0) {
                foreach ($lcinfoArry as $lcinfo) {
                    // 前リバイズ番号 <> aclcinfoのPoリバイズ番号の場合
                    if ($preporeviseno != $lcinfo["poreviseno"]) {
                        // 当リバイズ番号が空の場合、あるいは　当リバイズ番号 = aclcinfoのpoリバイズ番号の場合
                        if ($poreviseno == "" || $poreviseno == $lcinfo["poreviseno"]) {
                            $lcstate = 0;
                        } else {
                            $lcstate = 1;
                        }
                        $poreviseno = $lcinfo["poreviseno"];

                        // L/C状態を更新する
                        $lcModel->updateAcLcState($lcinfo["pono"], $lcinfo["poreviseno"], $lcstate);

                    }
                    $preporeviseno = $lcinfo["poreviseno"];
                }
            }
        }
    }
}
/**
 * ワークフロー状態を取得する
 *
 * @param [type] $orderData
 * @return void
 */
function fncWorkFlowStatus($orderData)
{
    if ($orderData["lngrevisionno"] < 0) {
        if ($orderData["bytinvalidflag"] == false) {
            $strDataState = "削除";
        } else {
            $strDataState = "削除後無効化";
        }
    } else {
        if ($orderData["lngorderstatuscode"] == DEF_ORDER_APPLICATE) {
            $strDataState = "未承認";
        } else if ($orderData["lngorderstatuscode"] == DEF_ORDER_DELIVER
            || $orderData["lngorderstatuscode"] == DEF_ORDER_END
            || $orderData["lngorderstatuscode"] == DEF_ORDER_CLOSED) {
            $strDataState = "納品";
        } else {
            $strDataState = "承認済";
        }
    }
    return $strDataState;
}
