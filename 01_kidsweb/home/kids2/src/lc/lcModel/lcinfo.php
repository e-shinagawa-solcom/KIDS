<?php

/**
 * L/C�ǡ�����������
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
    // �������μ���
    $lcGetDate = $lcModel->getMaxLcGetDate();
    $lcGetDateArry = explode(" ", $lcGetDate);
    $lcGetDate_date = $lcGetDateArry[0];
    $lcGetDate_time = $lcGetDateArry[1];

    // ȯ�����μ���
    $orderCount = fncGetPurchaseOrderCount($objDB, $lcGetDate);

    // ��Х�������ν����
    $reviseDataArry = array();

    // ȯ���� > 0 �ξ�硢t_aclcinfo�إǡ�������Ͽ����������
    if ($orderCount > 0) {
        // t_aclcinfo�ǡ����κ��
        $lcModel->deleteAcLcInfo($lcGetDate_date, $lcGetDate_time);

        // ȯ���ǡ������������
        $orderArry = fncGetPurchaseOrderData($objDB, $lcGetDate);
        // ��Ǽ�����ν����
        $strWorkDate = "9999/99/99";

        foreach ($orderArry as $orderData) {
            $pono = $orderData["lngpurchaseorderno"];
            $poreviseno = $orderData["lngrevisionno"];
            $intPayFlg = false;
            $payconditioncode = $orderData["lngpayconditioncode"];

            // ȯ�����٥ǡ������������
            $orderDetailArry = fncGetOrderDetail($objDB, $pono, $poreviseno);
            // // ����ե����֤��������
            // $strDataState = fncWorkFlowStatus($orderData);
            // ȯ��ǡ����Υ�ӥ�����ֹ� < 0�ξ��
            if ($poreviseno < 0) {
                // t_aclcinfo�ξ��֤����˹�������
                $lcModel->updateAcLcStateToDelete($pono, $strDataState);
            } else {
                // ȯ��ǡ����λ�ʧ��拾���� = 2 ���� ȯ��ǡ����Υ�ӥ�����ֹ� <> 0�ξ��
                if ($payconditioncode == DEF_PAYCONDITION_TT && $poreviseno != 0) {
                    // t_aclcinfo��Ʊ��Pono��¸�ߤ��Ƥ��뤫������å�����
                    $acLcCount = $lcModel->getAcLcCount($pono);
                    if ($acLcCount > 0) {
                        $intPayFlg = true;
                    }
                }

                // ȯ��ǡ����Υ�ӥ�����ֹ� <> 0�ξ��
                if ($poreviseno��!= 0) {
                    // t_aclcinfo���ǿ���Х����ǡ����Υ����ץ��ȶ�԰��������������
                    $acLcInfoArry = $lcModel->getAcLcInfoByPono($pono);
                }
            }

            if (count($orderDetailArry) > 0) {
                foreach ($orderDetailArry as $orderDetailData) {
                    $dtmdeliverydate = $orderDetailData["dtmdeliverydate"];
                    // ȯ��ǡ����λ�ʧ��拾���� = 1 ���뤤�� ( ȯ��ǡ����λ�ʧ��拾���� = 2 ����Ʊ��Pono���ˤ���ξ���
                    if (payconditioncode == DEF_PAYCONDITION_LC || ($payconditioncode == DEF_PAYCONDITION_TT && $intPayFlg)) {
                        // po���ֹ������
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
                        // Ǽ�ʾ��̾�ΤȲ����Ϥμ���
                        $companyNameAndCountryName = fncGetCompanyNameAndCountryName($objDB, $orderArry["lngdeliveryplacecode"]);

                        // ���֤�����
                        // ȯ�����٥ǡ�����Ǽ���� < ȯ��ǡ���.��Ͽ��
                        if ($dtmdeliverydate < $orderData["dtminsertdate"]) {
                            $lcstate = 3;
                        } else {
                            $lcstate = 0;
                        }
                        // ȯ��ǡ����λ�ʧ��拾���� = 2 ����Ʊ��Pono���ˤ���ξ��
                        if ($payconditioncode == DEF_PAYCONDITION_TT && $intPayFlg) {
                            $lcstate = 9;
                        }

                        // ��Ǽ�����μ���
                        if ($dtmdeliverydate != null && $dtmdeliverydate < $strWorkDate) {
                            $strWorkDate = $dtmdeliverydate;
                        }

                        // L/C�ǡ����ν�ʣ�����å���Ԥ�
                        $poupdatedate = $lcModel->getPoUpdateDate($pono, $polineno, $poreviseno);
                        if ($poupdatedate != null) {
                            // ������ <  ȯ��ǡ�������Ͽ���ξ��
                            if ($poupdatedate < $orderData["dtminsertdate"]) {

                                if ($lcstate == 0) {
                                    $bankReqDate = $lcModel->getAcLcBankReqDate($pono);
                                    if ($bankReqDate != "") {
                                        $lcstate = 7;
                                    }
                                }

                                // t_aclcinfo�ι������򹹿�����
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

                            // t_aclcinfo�˥ǡ�������Ͽ����
                            $data = array();
                            $data["pono"] = $pono;
                            $data["polineno"] = $polineno;
                            $data["poreviseno"] = $poreviseno;
                            $data["postate"] = "��ǧ��";
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

        // ȯ�����٤Υ����ץ������
        // ������μ���
        $baseDate = $lcModel->getBaseDate();
        if ($baseDate < substr($strWorkDate, 8, 2)) {
            $opendate = date("Ym", $strWorkDate . strtotime("+0 day"));
        } else {
            $opendate = date("Ym", $strWorkDate . strtotime("-1 month"));
        }
        $lcModel->updateAcLcOpendate($pono, $opendate);

    }

    // ��Х������󤬤��ä���硢��Х�������Ѿ�������Ԥ�
    if (count($reviseDataArry) > 0) {
        foreach ($reviseDataArry as $reviseData) {
            fncSetRevData($reviseData);
        }
    }

    // ��Х�������ξ����ѹ�����
    fncUpdRevState($lcModel, $reviseDataArry);

    // ̤��ǧL/C����ξ��ֹ�������
    fncUpdUnapprovedLcInfo($lcModel); ////////////////////////���֤�����

    // ����ǡ�������Ƚ�����
    fncRevivalDeletedLcInfo($objDB, $lcModel);

}

/**
 * ��Х����ǡ����η�³����
 *
 * @param [object] $reviseData
 * @return void
 */
function fncSetRevData($lcModel, $reviseData)
{
    // t_aclcinfo���ǡ������������
    $reviseLcInfoArry = $lcModel->getReviseAcLcInfo($reviseData["pono"], $reviseData["polineno"]);
    $argIntAmdFlg = 0;
    $revDataArry = array();

    foreach ($reviseLcInfoArry as $reviseLcInfo) {
        // �嵭�Ǽ���������԰����������ǤϤʤ����
        if ($reviseLcInfo["bankreqdate"] != "") {
            if (count($revDataArry) == 0) {
                $revDataArry[0] = $reviseLcInfo;
            }
        }
        // �嵭�Ǽ���������԰����������ǤϤʤ������ġ���Х��������poreviseno <> "00"�ξ��
        if ($reviseLcInfo["bankreqdate"] != "" && $reviseData["poreviseno"] != "00") {
            $argIntAmdFlg = 1;
            break;
        }
    }

    if (count($revDataArry) == 0) {
        // ��Х��������polineno > 1�ξ��
        if ($reviseData["polineno"] > 1) {
            reset($reviseLcInfoArry);
            $reviseLcInfoArry = $lcModel->getReviseAcLcInfo($reviseData["pono"], sprintf("%02s", $reviseData["polineno"] - 1));

            foreach ($reviseLcInfoArry as $reviseLcInfo) {
                // �嵭�Ǽ���������԰����������ǤϤʤ����
                if ($reviseLcInfo["bankreqdate"] != "") {
                    if (count($revDataArry) == 0) {
                        $revDataArry[0] = $reviseLcInfo;
                    }
                }
                // �嵭�Ǽ���������԰����������ǤϤʤ������ġ���Х��������poreviseno <> "00"�ξ��
                if ($reviseLcInfo["bankreqdate"] != "" && $reviseData["poreviseno"] != "00") {
                    $argIntAmdFlg = 1;
                    break;
                }
            }
        }
    }
    // ��Х�������˥ǡ��������ä����
    if (count($revDataArry) != 0) {
        foreach ($revDataArry as $revData) {
            // t_aclcinfo�˥ǡ����򹹿�����
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
 * ��Х��������ѹ�����
 *
 * @param [array] $reviseDataArry
 * @return void
 */
function fncUpdRevState($lcModel, $reviseDataArry)
{
    $orderno = "";
    foreach ($reviseDataArry as $reviseData) {
        // ȯ���ֹ� <> ��Х��������pono�ξ��
        if ($orderno != $reviseData["pono"]) {
            // t_aclcinfo�˥ǡ����򹹿�����
            $lcModel->updateAcLcStateToRevise($reviseData["pono"], $reviseData["poreviseno"]);

            $orderno = $reviseData["pono"];
        }
    }
    return true;
}

/**
 * ̤��ǧL/C���󥹥ơ������򹹿�����
 *
 * @param [object] $lcModel
 * @return void
 */
function fncUpdUnapprovedLcInfo($lcModel)
{
    // ̤��ǧ��aclcinfo�ǡ������������
    $unapprovedLcArry = $lcModel->getUnapprovedAcLcInfo();

    foreach ($unapprovedLcArry as $unapprovedLc) {
        $postate = "��ǧ��";
        // ���֤�����
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
        // L/C���֤򹹿�����
        // $lcModel->updateUnapprovedAcLcState($unapprovedLc["pono"], $unapprovedLc["polineno"],
        //     $unapprovedLc["poreviseno"], $lcstate, $postate);

    }
    return true;
}

/**
 * ����ǡ�������Ƚ�����
 *
 * @param [object] $objDB
 * @param [object] $lcModel
 * @return void
 */
function fncRevivalDeletedLcInfo($objDB, $lcModel)
{
    // �������ǡ�����PO�ֹ���������
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
                    // ����Х����ֹ� <> aclcinfo��Po��Х����ֹ�ξ��
                    if ($preporeviseno != $lcinfo["poreviseno"]) {
                        // ����Х����ֹ椬���ξ�硢���뤤�ϡ�����Х����ֹ� = aclcinfo��po��Х����ֹ�ξ��
                        if ($poreviseno == "" || $poreviseno == $lcinfo["poreviseno"]) {
                            $lcstate = 0;
                        } else {
                            $lcstate = 1;
                        }
                        $poreviseno = $lcinfo["poreviseno"];

                        // L/C���֤򹹿�����
                        $lcModel->updateAcLcState($lcinfo["pono"], $lcinfo["poreviseno"], $lcstate);

                    }
                    $preporeviseno = $lcinfo["poreviseno"];
                }
            }
        }
    }
}
/**
 * ����ե����֤��������
 *
 * @param [type] $orderData
 * @return void
 */
function fncWorkFlowStatus($orderData)
{
    if ($orderData["lngrevisionno"] < 0) {
        if ($orderData["bytinvalidflag"] == false) {
            $strDataState = "���";
        } else {
            $strDataState = "�����̵����";
        }
    } else {
        if ($orderData["lngorderstatuscode"] == DEF_ORDER_APPLICATE) {
            $strDataState = "̤��ǧ";
        } else if ($orderData["lngorderstatuscode"] == DEF_ORDER_DELIVER
            || $orderData["lngorderstatuscode"] == DEF_ORDER_END
            || $orderData["lngorderstatuscode"] == DEF_ORDER_CLOSED) {
            $strDataState = "Ǽ��";
        } else {
            $strDataState = "��ǧ��";
        }
    }
    return $strDataState;
}
