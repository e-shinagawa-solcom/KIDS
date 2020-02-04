//
// regist.js
//
jQuery(function ($) {
    // ウィンドウクローズ処理
    window.onbeforeunload = unLock;

    scanAllCheckbox();
    resetTableAWidth();
    resetTableBWidth();
    resetTableADisplayStyle();
    resetTableBDisplayStyle();
    selectRow($("#tableA_chkbox"), $("#tableA"));
    selectRow($("#tableB_no"), $("#tableB"));

    // $("#tableB").sortable();
    // $("#tableB").on('sortstop', function () {
    //     resetTableBRowid();
    // });
    function addHiddenValue(i, tr) {
        var hidden = $('#SegHidden');
        var orderNo = $(tr).find($('td.detailOrderCode')).text();
        $(hidden).append($("<input>").attr("type", "hidden").attr("name", "detail[" + (i + 1) + "][orderNo]").val(orderNo));
        var orderDetailNo = $(tr).find($('td.detailOrderDetailNo')).text();
        $(hidden).append($("<input>").attr("type", "hidden").attr("name", "detail[" + (i + 1) + "][orderDetailNo]").val(orderDetailNo));
        var stockSubjectName = $(tr).find($('td.detailStockSubjectName')).text();
        $(hidden).append($("<input>").attr("type", "hidden").attr("name", "detail[" + (i + 1) + "][stockSubjectName]").val(stockSubjectName));
        var stockItenName = $(tr).find($('td.detailStockItemName')).text();
        $(hidden).append($("<input>").attr("type", "hidden").attr("name", "detail[" + (i + 1) + "][stockItenName]").val(stockItenName));
        var companyDisplayCode = $(tr).find($('td.detailCompanyDisplayCode')).text();
        $(hidden).append($("<input>").attr("type", "hidden").attr("name", "detail[" + (i + 1) + "][companyDisplayCode]").val(companyDisplayCode));
        var deliveryMethod = $(tr).find('option:selected').val();
        $(hidden).append($("<input>").attr("type", "hidden").attr("name", "detail[" + (i + 1) + "][deliveryMethod]").val(deliveryMethod));
        var productPrice = $(tr).find($('td.detailProductPrice')).text();
        $(hidden).append($("<input>").attr("type", "hidden").attr("name", "detail[" + (i + 1) + "][productPrice]").val(productPrice));
        var productQuantity = $(tr).find($('td.detailProductQuantity')).text();
        $(hidden).append($("<input>").attr("type", "hidden").attr("name", "detail[" + (i + 1) + "][productQuantity]").val(productQuantity));
        var subTotalPrice = $(tr).find($('td.detailSubtotalPrice')).text();
        $(hidden).append($("<input>").attr("type", "hidden").attr("name", "detail[" + (i + 1) + "][subTotalPrice]").val(subTotalPrice));
        var deliveryDate = $(tr).find($('td.detailDeliveryDate')).text();
        $(hidden).append($("<input>").attr("type", "hidden").attr("name", "detail[" + (i + 1) + "][deliveryDate]").val(deliveryDate));
        var detailNote = $(tr).find($('td.detailNote')).text();
        $(hidden).append($("<input>").attr("type", "hidden").attr("name", "detail[" + (i + 1) + "][detailNote]").val(detailNote));
        var productUnitCode = $(tr).find($('td.detailProductUnitCode')).text();
        $(hidden).append($("<input>").attr("type", "hidden").attr("name", "detail[" + (i + 1) + "][productUnitCode]").val(productUnitCode));
        var orderNo = $(tr).find($('td.detailOrderNo')).text();
        $(hidden).append($("<input>").attr("type", "hidden").attr("name", "detail[" + (i + 1) + "][orderNo]").val(orderNo));
        var revisionNo = $(tr).find($('td.detailRevisionNo')).text();
        $(hidden).append($("<input>").attr("type", "hidden").attr("name", "detail[" + (i + 1) + "][revisionNo]").val(revisionNo));
        var subjectCode = $(tr).find($('td.detailStockSubjectCode')).text();
        $(hidden).append($("<input>").attr("type", "hidden").attr("name", "detail[" + (i + 1) + "][subjectCode]").val(subjectCode));
        var stockItemCode = $(tr).find($('td.detailStockItemCode')).text();
        $(hidden).append($("<input>").attr("type", "hidden").attr("name", "detail[" + (i + 1) + "][stockItemCode]").val(stockItemCode));
        var monetaryUnitCode = $(tr).find($('td.detailMonetaryUnitCode')).text();
        $(hidden).append($("<input>").attr("type", "hidden").attr("name", "detail[" + (i + 1) + "][monetaryUnitCode]").val(monetaryUnitCode));
        var customerCompanyCode = $(tr).find($('td.detailCustomerCompanyCode')).text();
        $(hidden).append($("<input>").attr("type", "hidden").attr("name", "detail[" + (i + 1) + "][customerCompanyCode]").val(customerCompanyCode));

    }

    function validationCheck2() {
        var result = true;
        if (!$('#tableB tbody tr').length) {
            // 発注明細行が1件も選択されていない場合
            alert("発注確定する明細行が選択されていません。");
            result = false;
        }
        var countryCode = $('input[name="lngCountryCode"]').val();
        if (countryCode !== '81') {
            var selected = $('select[name="optPayCondition"]').children('option:selected').val();
            if (selected === '0') {
                // 仕入先のm_company.lngcountrycodeが「81(日本)」以外かつ支払条件が未選択の場合
                alert('仕入先が海外の場合、支払い条件を指定してください。');
                result = false;
            }
        }
        var locationCode = $('input[name="lngLocationCode"]').val();
        if (!locationCode) {
            // 納品場所が未入力の場合
            alert('納品場所が指定されていません。');
            result = false;
        }
        /* 運搬方法は"-"も可とする
        var details = getSelectedRows();
                var message = [];
                $.each(details, function(i, tr){
                    var selected = $(tr).find('option:selected').val();
                    if(selected === "0"){
                        var row = $(tr).children('td[name="rownum"]').text();
                        message.push(row + '行目の運搬方法が指定されていません。');
                    }
                });
                if(message.length){
                    // 運搬方法が1件でも未選択の場合
                    alert(message.join('\n'));
                    result = false;
                }
        */
        return result;
    }
    function isDate(d) {
        if (d == "") { return false; }
        if (!d.match(/^\d{4}\/\d{1,2}\/\d{1,2}$/)) { return false; }

        var date = new Date(d);
        if (date.getFullYear() != d.split("/")[0]
            || date.getMonth() != d.split("/")[1] - 1
            || date.getDate() != d.split("/")[2]
        ) {
            return false;
        }
        return true;
    }
    function getUpdateDetail() {
        var result = [];
        $('#tableB tbody tr').each(function (i, tr) {
            var param = {
                lngOrderDetailNo: $(tr).children('.detailOrderDetailNo').text(),
                lngSortKey: i + 1,
                lngDeliveryMethodCode: $(tr).find('[name="optDelivery"] option:selected').val(),
                strDeliveryMethodName: $(tr).find('[name="optDelivery"] option:selected').text(),
                lngProductUnitCode: $(tr).find('.detailProductUnitCode').text(),
                lngOrderNo: $(tr).find('.detailOrderNo').text(),
                lngRevisionNo: $(tr).find('.detailRevisionNo').text(),
                lngStockSubjectCode: $(tr).find('.detailStockSubjectCode').text(),
                strStockItemCode: $(tr).find('.detailStockItemCode').text(),
                lngMonetaryUnitCode: $(tr).find('.detailMonetaryUnitCode').text(),
                lngCustomerCompanyCode: $(tr).find('.detailCustomerCompanyCode').text(),
                curProductPrice: $(tr).find('.detailProductPrice').text(),
                lngProductQuantity: $(tr).find('.detailProductQuantity').text(),
                curSubtotalPrice: $(tr).find('.detailSubtotalPrice').text(),
                dtmDeliveryDate: $(tr).find('.detailDeliveryDate').text(),
                strDetailNote: $(tr).find('.detailNote').find('input:text').val(),
            };
            result.push(param);
        });
        return result;
    }


    // テーブルA 全選択／解除チェックボックス
    $(document).on('change', '#allChecked', function (e) {
        e.preventDefault();

        var $all_rows = $('#tableA tbody tr');
        var $all_chkbox_rows = $('#tableA_chkbox tbody tr');
        var $all_chkbox_rows_checkbox = $('#tableA_chkbox tbody tr').find('input[type="checkbox"]');

        if (e.target.checked) {
            $all_rows.css("background-color", "#bbbbbb");
            $all_chkbox_rows.css("background-color", "#bbbbbb");
            $all_chkbox_rows_checkbox.not(':disabled').prop('checked', true);
        } else {
            $all_rows.css("background-color", "#ffffff");
            $all_chkbox_rows.css("background-color", "#ffffff");
            $all_chkbox_rows_checkbox.prop('checked', false);
        }
    });


    // 行を一つ上に移動するボタン
    $('img.rowup').click(function () {
        var len = $("#tableB tbody tr").length;
        for (var i = 1; i <= len; i++) {
            var row = $("#tableB tbody tr:nth-child(" + i + ")");
            var backgroud = row.css("background-color");
            if (backgroud != 'rgb(255, 255, 255)') {
                for (var j = i - 1; j >= 1; j--) {
                    var row_prev = $("#tableB tbody tr:nth-child(" + j + ")");
                    var row_prev_backgroud = row_prev.css("background-color");
                    if (row_prev_backgroud == 'rgb(255, 255, 255)') {
                        row.insertBefore(row_prev);
                        break;
                    }
                }
            }
        }

        var len = $("#tableB_no tbody tr").length;
        for (var i = 1; i <= len; i++) {
            var row = $("#tableB_no tbody tr:nth-child(" + i + ")");
            var backgroud = row.css("background-color");
            if (backgroud != 'rgb(255, 255, 255)') {
                for (var j = i - 1; j >= 1; j--) {
                    var row_prev = $("#tableB_no tbody tr:nth-child(" + j + ")");
                    var row_prev_backgroud = row_prev.css("background-color");
                    if (row_prev_backgroud == 'rgb(255, 255, 255)') {
                        row.insertBefore(row_prev);
                        break;
                    }
                }
            }
        }

        resetTableBRowid();

    });

    // 行を一つ下に移動するボタン
    $('img.rowdown').click(function () {
        var len = $("#tableB tbody tr").length;
        for (var i = len; i >= 1; i--) {
            var row = $("#tableB tbody tr:nth-child(" + i + ")");
            var backgroud = row.css("background-color");
            if (backgroud != 'rgb(255, 255, 255)') {
                for (var j = i + 1; j <= len; j++) {
                    var row_prev = $("#tableB tbody tr:nth-child(" + j + ")");
                    var row_prev_backgroud = row_prev.css("background-color");
                    if (row_prev_backgroud == 'rgb(255, 255, 255)') {
                        row.insertAfter(row_prev);
                        break;
                    }
                }
            }
        }


        var len = $("#tableB_no tbody tr").length;
        for (var i = len; i >= 1; i--) {
            var row = $("#tableB_no tbody tr:nth-child(" + i + ")");
            var backgroud = row.css("background-color");
            if (backgroud != 'rgb(255, 255, 255)') {
                for (var j = i + 1; j <= len; j++) {
                    var row_prev = $("#tableB_no tbody tr:nth-child(" + j + ")");
                    var row_prev_backgroud = row_prev.css("background-color");
                    if (row_prev_backgroud == 'rgb(255, 255, 255)') {
                        row.insertAfter(row_prev);
                        break;
                    }
                }
            }
        }

        resetTableBRowid();

    });

    // 行を一番上に移動する
    $('img.rowtop').click(function () {
        var firsttr = $("#tableB tbody").find('tr').first();
        $("#tableB tbody tr").each(function (i, e) {
            var backgroud = $(this).css("background-color");
            if (backgroud != 'rgb(255, 255, 255)') {
                $(this).insertBefore(firsttr);
            }
        });

        firsttr = $("#tableB_no tbody").find('tr').first();
        $("#tableB_no tbody tr").each(function (i, e) {
            var backgroud = $(this).css("background-color");
            if (backgroud != 'rgb(255, 255, 255)') {
                $(this).insertBefore(firsttr);
            }
        });

        resetTableBRowid();

    });

    // 行を一番下に移動する
    $('img.rowbottom').click(function () {
        var lasttr = $("#tableB tbody").find('tr').last();
        $("#tableB tbody tr").each(function (i, e) {
            var backgroud = $(this).css("background-color");
            if (backgroud != 'rgb(255, 255, 255)') {
                $(this).insertAfter(lasttr);
            }
        });

        lasttr = $("#tableB_no tbody").find('tr').last();
        $("#tableB_no tbody tr").each(function (i, e) {
            var backgroud = $(this).css("background-color");
            if (backgroud != 'rgb(255, 255, 255)') {
                $(this).insertAfter(lasttr);
            }
        });

        resetTableBRowid();
    });


    // // 行IDの再設定
    // function resetTableARowid() {
    //     var rownum = 0;
    //     $("#tableA tbody tr").each(function (i, e) {
    //         rownum += 1;
    //         $(this).find('td').first().text(rownum);
    //     });
    // }


    // 行IDの再設定
    function resetTableBRowid() {
        var rownum = 0;
        $("#tableB_no tbody tr").each(function (i, e) {
            rownum += 1;
            $(this).find('td').first().text(rownum);
        });
    }


    // 追加ボタンのイベント
    $('img.add').on('click', function () {
        var isChecked = false;
        $('input[type="checkbox"]')
            .each(function () {
                if (this.checked) {
                    if ($(this).attr('name') != "allSel") {
                        isChecked = true;
                        return false;
                    }
                }
            });

        // 行が選択されてない場合
        if (!isChecked) {
            alert("発注確定する明細データが指定されていません。");
            return;
        }

        $('#tableA_chkbox tbody tr').each(function (i, e) {
            var rownum = i + 1;
            var chkbox = $(this).find('input[type="checkbox"]');
            if (chkbox.prop("checked")) {
                // tableBの追加
                $("#tableB tbody").append('<tr>' + $('#tableA tbody tr:nth-child(' + rownum + ')').html() + '</tr>');
                var no = $("#tableB_no tbody").find('tr').length + 1;
                $("#tableB_no tbody").append('<tr><td>' + no + '</td></tr>');
            }

        });

        // tableBのリセット
        for (var i = $('#tableA_chkbox tbody tr').length; i > 0; i--) {
            var row = $('#tableA_chkbox tbody tr:nth-child(' + i + ')');
            var chkbox = row.find('input[type="checkbox"]');
            if (chkbox.prop("checked")) {
                row.remove();
                $('#tableA tbody tr:nth-child(' + i + ')').remove();
            }
        }

        resetTableBRowid();

        resetTableBWidth();

        resetTableBDisplayStyle();

        selectRow($("#tableB_no"), $("#tableB"));

        scanAllCheckbox();
    });

    // 全削除ボタンのイベント
    $('img.alldelete').on('click', function () {

        $("#tableB tbody tr").each(function (i, e) {
            removeTableBToTableA($(this));
        });

        $("#tableB_no tbody").empty();

        resetTableAWidth();

        resetTableADisplayStyle();

        resetTableBWidth();

        scanAllCheckbox();

        selectRow($("#tableA_chkbox"), $("#tableA"));
    });

    // 削除ボタンのイベント
    $('img.delete').on('click', function () {
        $("#tableB_no tbody tr").each(function (i, e) {
            var backgroud = $(this).css("background-color");
            if (backgroud != 'rgb(255, 255, 255)') {
                $(this).remove();
            }
        });
        $("#tableB tbody tr").each(function (i, e) {
            var backgroud = $(this).css("background-color");
            if (backgroud != 'rgb(255, 255, 255)') {
                removeTableBToTableA($(this));
            }
        });

        resetTableBRowid();

        resetTableAWidth();

        resetTableADisplayStyle();

        resetTableBWidth();

        scanAllCheckbox();

        selectRow($("#tableA_chkbox"), $("#tableA"));
    });

    function removeTableBToTableA(tableBRow) {
        var trhtml = tableBRow.html();
        var detailnoB = tableBRow.find('.detailOrderDetailNo').text();
        var rownum = 0;
        $("#tableA tbody tr").each(function (i, e) {
            var detailnoA = $(this).find('.detailOrderDetailNo').text();
            console.log("detailnoA:" + detailnoA);
            console.log("detailnoB:" + detailnoB);
            console.log(parseInt(detailnoA) > parseInt(detailnoB));
            if (parseInt(detailnoA) > parseInt(detailnoB)) {
                rownum = i + 1;
                return false;
            }
        });
        if (rownum == 0) {
            $('#tableA tbody').append('<tr>' + trhtml + '</tr>');
            $('#tableA_chkbox tbody').append('<tr><td style="text-align:center;"><input type="checkbox"></td></tr>');
            rownum = $("#tableA tbody tr").length;
        } else {
            $('#tableA tbody tr:nth-child(' + rownum + ')').before('<tr>' + trhtml + '</tr>');
            $('#tableA_chkbox tbody tr:nth-child(' + rownum + ')').before('<tr><td style="text-align:center;"><input type="checkbox"></td></tr>');
        }

        tableBRow.remove();
    }
    $('img.decideRegist').on('click', function () {
        if (!validationCheck2()) {
            return false;
        }
        $.ajax({
            type: 'POST',
            url: '/po/confirm/index.php?strSessionID=' + $('input[type="hidden"][name="strSessionID"]').val(),
            data: {
                strSessionID: $('input[type="hidden"][name="strSessionID"]').val(),
                lngOrderNo: $('input[name="lngOrderNo"]').val(),
                strMode: $('input[name="strMode"]').val(),
                lngRevisionNo: $('input[name="lngRevisionNo"]').val(),
                lngPayConditionCode: $('select[name="lngPayConditionCode"]').val(),
                lngLocationCode: $('input[name="lngLocationCode"]').val(),
                strNote: $('input[name="strNote"]').val(),
                strProductCode: $('input[name="strProductCode"]').val(),
                strReviseCode: $('input[name="strReviseCode"]').val(),
                aryDetail: getUpdateDetail(),
            },
            async: true,
        }).done(function (data) {
            console.log("done");
            console.log(data);
            //$('html').html(data);
            // document.write(data);
            
            var w = window.open("", 'Decide Confirm', "width=1011px, height=600px, scrollbars=yes, resizable=yes");
            w.document.open();
            w.document.write(data);
            w.document.close();
        }).fail(function (error) {
            console.log("fail");
            console.log(error);
        });
    });
    $('img.clear').on('click', function () {
        window.location.reload();
    });
    function unLock() {
        $.ajax({
            url: '/po/regist/index.php',
            type: 'post',
            // dataType: 'json',
            type: 'POST',
            //            async: false,
            data: {
                'strSessionID': $('input[type="hidden"][name="strSessionID"]').val(),
                'strMode': 'cancel',
            }
        })
            .done(function (response) {
            })
            .fail(function (response) {
            });

        return false;
    }

    /**
   * @method scanAllCheckbox スキャンチェックボックス
   */
    function scanAllCheckbox() {

        var $all_rows = $('#tableA tbody tr');
        var $all_chkbox_rows = $('#tableA_chkbox tbody tr');
        var $all_checkbox = $all_chkbox_rows.find('input[type="checkbox"]');

        // 有効 <tr> ＊選択可能行
        var count_checked = 0;
        var count_disabled = 0;

        console.log(data.length);
        console.log(!data.length);

        // data がない場合、全選択／解除チェックボックスを寝かせて無効化
        if (!$all_rows.length) {
            $('#allChecked').prop({ 'checked': false, 'disabled': true });
        } else {
            $('#allChecked').prop('disabled', false);
        }

        $.each($all_checkbox, function (i) {
            // チェックボックスがひとつでも外れている場合、全選択／解除チェックボックスを寝かす
            if (!($(this).closest('tr').css("background-color") != 'rgb(255, 255, 255)')) {
                $('#allChecked').prop('checked', false);
            }

            // チェックボックスがすべてチェックされた場合、全選択／解除チェックボックスを立てる
            if ($(this).closest('tr').css("background-color") != 'rgb(255, 255, 255)') {
                ++count_checked;
            }
            if ($all_rows.length === count_checked) {
                $('#allChecked').prop('checked', true);
            }

            // すべてのチェックボックスが無効化された場合、全選択／解除チェックボックスを寝かせて無効化
            if ($(this).prop('disabled')) {
                ++count_disabled;
            }
            if (data.length === count_disabled) {
                $('#allChecked').prop({ 'checked': false, 'disabled': true });
            }
        });
    };


    // テーブルAの幅をリセットする
    function resetTableAWidth() {
        $("#tableA thead").css('display', '');
        $("#tableA tbody tr td").width('');
        $("#tableA thead tr th").width('');
        $("#tableA_head tr th").width('');

        $("#tableA_chkbox tbody tr td").width($("#tableA_chkbox_head tr th").width() + 1);
        var thwidthArry = [];
        var tdwidthArry = [];
        var width = 0;
        var columnNum = $('#tableA_head thead tr th').length;
        for (var i = 1; i <= columnNum; i++) {
            var thwidth = $('#tableA_head thead tr th:nth-child(' + i + ')').width();
            var tdwidth = $('#tableA tbody tr td:nth-child(' + i + ')').width();
            thwidthArry.push(thwidth + 20);
            tdwidthArry.push(tdwidth + 20);
        }

        for (var i = 1; i <= columnNum; i++) {
            if ($("#tableA_head thead tr th:nth-child(" + i + ")").css("display") != "none") {
                if (thwidthArry[i - 1] > tdwidthArry[i - 1]) {
                    $("#tableA_head thead tr th:nth-child(" + i + ")").width(thwidthArry[i - 1]);
                    $("#tableA tbody tr td:nth-child(" + i + ")").width(thwidthArry[i - 1]);
                    width += thwidthArry[i - 1];
                } else {
                    $("#tableA_head thead tr th:nth-child(" + i + ")").width(tdwidthArry[i - 1]);
                    $("#tableA tbody tr td:nth-child(" + i + ")").width(tdwidthArry[i - 1]);
                    width += tdwidthArry[i - 1];
                }
            }
        }
        $("#tableA_head").width(width + 110);
        $("#tableA").width(width + 110);

        $("#tableA thead").css('display', 'none');
    }

    function resetTableADisplayStyle() {
        $("#tableA tbody tr").each(function (i, e) {
            $(this).find(".detailNote").find('input:text').prop('disabled', true);
        });
    }

    function resetTableBDisplayStyle() {
        $("#tableB tbody tr").each(function (i, e) {
            $(this).find(".detailNote").find('input:text').prop('disabled', false);
        });
    }

    // テーブルBの幅をリセットする
    function resetTableBWidth() {
        $("#tableB_no tbody tr td").width($("#tableB_no_head thead tr th").width() + 1);
        $("#tableB tbody tr td").width('');
        $("#tableB_head thead tr th").width('');
        var thwidthArry = [];
        var tdwidthArry = [];
        var columnNum = $('#tableB_head thead tr th').length;
        console.log(columnNum);
        var width = 0;
        for (var i = 1; i <= columnNum; i++) {
            var thwidth = $('#tableB_head thead tr th:nth-child(' + i + ')').width();
            var tdwidth = $('#tableB tbody tr td:nth-child(' + i + ')').width();
            thwidthArry.push(thwidth + 20);
            tdwidthArry.push(tdwidth + 20);
        }

        for (var i = 1; i <= columnNum; i++) {
            if ($("#tableB tr th:nth-child(" + i + ")").css("display") != "none") {
                if (thwidthArry[i - 1] > tdwidthArry[i - 1]) {
                    $("#tableB_head thead tr th:nth-child(" + i + ")").width(thwidthArry[i - 1]);
                    $("#tableB tbody tr td:nth-child(" + i + ")").width(thwidthArry[i - 1]);
                    width += thwidthArry[i - 1];
                } else {
                    $("#tableB_head thead tr th:nth-child(" + i + ")").width(tdwidthArry[i - 1]);
                    $("#tableB tbody tr td:nth-child(" + i + ")").width(tdwidthArry[i - 1]);
                    width += tdwidthArry[i - 1];
                }
            }
        }

        $("#tableB_head").width(width + 100);
        $("#tableB").width(width + 100);
    }

    // テーブルの行をクリックする時のイベント
    function selectRow(objA, objB) {
        var rows = objA.find('tbody tr');
        var rows = objB.find('tbody tr');
        var lastSelectedRow;
        /* Create 'click' event handler for rows */
        objA.find('tbody tr').on('click', function (e) {
            lastSelectedRow = trClickEvent($(this), lastSelectedRow, e, objA, objB);
        });


        /* Create 'click' event handler for rows */
        objB.find('tbody tr').on('click', function (e) {
            lastSelectedRow = trClickEvent($(this), lastSelectedRow, e, objA, objB);
        });

        /* This 'event' is used just to avoid that the table text 
         * gets selected (just for styling). 
         * For example, when pressing 'Shift' keyboard key and clicking 
         * (without this 'event') the text of the 'table' will be selected.
         * You can remove it if you want, I just tested this in 
         * Chrome v30.0.1599.69 */
        $(document).bind('selectstart dragstart', function (e) {
            e.preventDefault(); return false;
        });
    }

    function trClickEvent(row, lastSelectedRow, e, objA, objB) {

        /* Check if 'Ctrl', 'cmd' or 'Shift' keyboard key was pressed
         * 'Ctrl' => is represented by 'e.ctrlKey' or 'e.metaKey'
         * 'Shift' => is represented by 'e.shiftKey' */
        if (e.ctrlKey || e.metaKey) {
            /* If pressed highlight the other row that was clicked */
            objA.find("tbody tr:nth-child(" + (row.index() + 1) + ")").css("background-color", "#bbbbbb");
            objB.find("tbody tr:nth-child(" + (row.index() + 1) + ")").css("background-color", "#bbbbbb");
            console.log(objA.find("tbody tr:nth-child(" + (row.index() + 1) + ")").find('input[type="checkbox"]'));
            objA.find("tbody tr:nth-child(" + (row.index() + 1) + ")").find('input[type="checkbox"]').prop('checked', true);

        } else if (e.shiftKey) {
            /* If pressed highlight the other row that was clicked */
            var indexes = [lastSelectedRow.index(), row.index()];
            indexes.sort(function (a, b) {
                return a - b;
            });
            for (var i = indexes[0]; i <= indexes[1]; i++) {
                objA.find("tbody tr:nth-child(" + (i + 1) + ")").css("background-color", "#bbbbbb");
                objB.find("tbody tr:nth-child(" + (i + 1) + ")").css("background-color", "#bbbbbb");
                console.log(objA.find("tbody tr:nth-child(" + (row.index() + 1) + ")").find('input[type="checkbox"]'));
                objA.find("tbody tr:nth-child(" + (i + 1) + ")").find('input[type="checkbox"]').prop('checked', true);
            }
        } else {
            /* Otherwise just highlight one row and clean others */
            objA.find("tbody tr").css("background-color", "#ffffff");
            objA.find("tbody tr").find('input[type="checkbox"]').prop('checked', false);
            objA.find("tbody tr:nth-child(" + (row.index() + 1) + ")").css("background-color", "#bbbbbb");
            objA.find("tbody tr:nth-child(" + (row.index() + 1) + ")").find('input[type="checkbox"]').prop('checked', true);

            objB.find("tbody tr").css("background-color", "#ffffff");
            objB.find("tbody tr:nth-child(" + (row.index() + 1) + ")").css("background-color", "#bbbbbb");
            lastSelectedRow = row;
        }

        return lastSelectedRow;
    }
});