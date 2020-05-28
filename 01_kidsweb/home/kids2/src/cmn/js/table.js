function setCheckBoxClickEvent(chkboxObj, tableA, tableA_chkbox, allCheckObj, hasCalculation = 0) {
    chkboxObj.on('keydown', function (e) {
        e.stopPropagation();
        console.log('enter');
        if (e.which == 13) {
            $(this).click();
        }
    });
    chkboxObj.on('click', function (e) {
        e.stopPropagation();
        var rowindex = $(this).parent().parent().index();
        if (this.checked) {
            tableA.find("tbody tr:nth-child(" + (rowindex + 1) + ")").css('background-color', '#bbbbbb');
            tableA_chkbox.find("tbody tr:nth-child(" + (rowindex + 1) + ")").css('background-color', '#bbbbbb');
            tableA_chkbox.find("tbody tr:nth-child(" + (rowindex + 1) + ")").find('td').find('input[name="edit"]').prop('checked', true);
        } else {
            tableA.find("tbody tr:nth-child(" + (rowindex + 1) + ")").css('background-color', '#ffffff');
            tableA_chkbox.find("tbody tr:nth-child(" + (rowindex + 1) + ")").css('background-color', '#ffffff');
            tableA_chkbox.find("tbody tr:nth-child(" + (rowindex + 1) + ")").find('td').find('input[name="edit"]').prop('checked', false);
        }
        // 対象チェックボックスチェック状態の設定
        scanAllCheckbox(tableA_chkbox, allCheckObj);

        if (hasCalculation == 1) {
            // 税抜金額の合計の計算
            totalPriceCalculation(tableA_chkbox, tableA);
        }
    });


}

function setAllCheckClickEvent(allCheckObj, tableA, tableA_chkbox, hasCalculation = 0) {
    allCheckObj.on('keydown', function (e) {
        e.stopPropagation();
        console.log('enter');
        if (e.which == 13) {
            $(this).click();
        }
    });
    // チェックボックスの切り替え処理のバインド
    allCheckObj.on({
        'click': function () {
            var status = this.checked;
            tableA_chkbox.find("tbody tr td ").find('input[type="checkbox"]')
                .each(function () {
                    this.checked = status;
                    if (status) {
                        tableA_chkbox.find("tbody tr").css("background-color", "#bbbbbb");
                        tableA.find("tbody tr").css("background-color", "#bbbbbb");
                    } else {
                        tableA_chkbox.find("tbody tr").css("background-color", "#ffffff");
                        tableA.find("tbody tr").css("background-color", "#ffffff");
                    }
                });
            if (hasCalculation == 1) {
                // 税抜金額の合計の計算
                totalPriceCalculation(tableA_chkbox, tableA);
            }
        }
    });
}

function totalPriceCalculation(tableA_chkbox, tableA) {
    var $all_chkbox_rows = tableA_chkbox.find('tbody tr');
    var $all_checkbox = $all_chkbox_rows.find('input[type="checkbox"]');
    var totalprice = 0;
    var strmonetaryunitsign;
    var lngmonetaryunitcode;
    // data がない場合、全選択／解除チェックボックスを寝かせて無効化
    if ($all_chkbox_rows.length == 0) {
        totalprice = 0
    } else {
        strmonetaryunitsign = tableA.find("tbody tr:nth-child(1)").find('.strmonetaryunitsign').text();
        lngmonetaryunitcode = tableA.find("tbody tr:nth-child(1)").find('.lngmonetaryunitcode').text();
    }
    console.log(totalprice);
    $.each($all_checkbox, function (i) {
        // チェックボックスがすべてチェックされた場合、全選択／解除チェックボックスを立てる
        if ($(this).closest('tr').css("background-color") != 'rgb(255, 255, 255)') {
            var rowindex = $(this).closest('tr').index();
            var cursubtotalprice = Number(tableA.find("tbody tr:nth-child(" + (rowindex + 1) + ")").find('.cursubtotalprice').text());
            console.log(cursubtotalprice);
            totalprice += cursubtotalprice;
        }
    });
    if ($all_chkbox_rows.length == 0) {
        $('input[name="totalPrice"]').val(0);
    } else {
        $('input[name="totalPrice"]').val(money_format(lngmonetaryunitcode, strmonetaryunitsign, totalprice, 'price'));
    }
}

function scanAllCheckbox(tableA_chkbox, allCheckObj) {
    var $all_chkbox_rows = tableA_chkbox.find('tbody tr');
    var $all_checkbox = $all_chkbox_rows.find('input[type="checkbox"]');

    // 有効 <tr> ＊選択可能行
    var count_checked = 0;
    var count_disabled = 0;

    // data がない場合、全選択／解除チェックボックスを寝かせて無効化
    if (!$all_chkbox_rows.length) {
        allCheckObj.prop({ 'checked': false, 'disabled': true });
    } else {
        allCheckObj.prop('disabled', false);
    }

    $.each($all_checkbox, function (i) {
        // チェックボックスがひとつでも外れている場合、全選択／解除チェックボックスを寝かす
        if (!($(this).closest('tr').css("background-color") != 'rgb(255, 255, 255)')) {
            allCheckObj.prop('checked', false);
        }

        // チェックボックスがすべてチェックされた場合、全選択／解除チェックボックスを立てる
        if ($(this).closest('tr').css("background-color") != 'rgb(255, 255, 255)') {
            ++count_checked;
        }
        if ($all_chkbox_rows.length === count_checked) {
            allCheckObj.prop('checked', true);
        }

        // すべてのチェックボックスが無効化された場合、全選択／解除チェックボックスを寝かせて無効化
        if ($(this).prop('disabled')) {
            ++count_disabled;
        }
        if ($all_chkbox_rows.length === count_disabled) {
            allCheckObj.prop({ 'checked': false, 'disabled': true });
        }
    });
}
// テーブルの行をクリックする時のイベント
function selectRow(type, tableA_fix, tableA, allCheckObj, hasCalculation = 0) {
    var rows = tableA_fix.find('tbody tr');
    var rows = tableA.find('tbody tr');
    var lastSelectedRow;
    /* Create 'click' event handler for rows */
    tableA_fix.find('tbody tr').on('click', function (e) {
        lastSelectedRow = trClickEvent($(this), lastSelectedRow, e, tableA_fix, tableA);
        if (type == 'hasChkbox') {
            scanAllCheckbox(tableA_fix, allCheckObj);
            if (hasCalculation == 1) {
                // 税抜金額の合計の計算
                totalPriceCalculation(tableA_fix, tableA);
            }
        }
    });

    /* Create 'click' event handler for rows */
    tableA.find('tbody tr').on('click', function (e) {
        lastSelectedRow = trClickEvent($(this), lastSelectedRow, e, tableA_fix, tableA);
        if (type == 'hasChkbox') {
            scanAllCheckbox(tableA_fix, allCheckObj);
            if (hasCalculation == 1) {
                // 税抜金額の合計の計算
                totalPriceCalculation(tableA_fix, tableA);
            }
        }
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

function trClickEvent(row, lastSelectedRow, e, tableA_fix, tableA) {
    /* Check if 'Ctrl', 'cmd' or 'Shift' keyboard key was pressed
     * 'Ctrl' => is represented by 'e.ctrlKey' or 'e.metaKey'
     * 'Shift' => is represented by 'e.shiftKey' */
    if (e.ctrlKey || e.metaKey) {
        /* If pressed highlight the other row that was clicked */
        if (tableA_fix.find("tbody tr:nth-child(" + (row.index() + 1) + ")").css("background-color") != 'rgb(255, 255, 255)') {
            tableA_fix.find("tbody tr:nth-child(" + (row.index() + 1) + ")").css("background-color", "#ffffff");
            tableA.find("tbody tr:nth-child(" + (row.index() + 1) + ")").css("background-color", "#ffffff");
            tableA_fix.find("tbody tr:nth-child(" + (row.index() + 1) + ")").find('input[type="checkbox"]').prop('checked', false);
        } else {
            tableA_fix.find("tbody tr:nth-child(" + (row.index() + 1) + ")").css("background-color", "#bbbbbb");
            tableA.find("tbody tr:nth-child(" + (row.index() + 1) + ")").css("background-color", "#bbbbbb");
            tableA_fix.find("tbody tr:nth-child(" + (row.index() + 1) + ")").find('input[type="checkbox"]').prop('checked', true);
        }

        tableA_fix.find("tbody tr:nth-child(" + (row.index() + 1) + ")").find('input[type="checkbox"]').change();

    } else if (e.shiftKey) {
        /* If pressed highlight the other row that was clicked */
        var indexes = [lastSelectedRow.index(), row.index()];
        indexes.sort(function (a, b) {
            return a - b;
        });
        for (var i = indexes[0]; i <= indexes[1]; i++) {
            tableA_fix.find("tbody tr:nth-child(" + (i + 1) + ")").css("background-color", "#bbbbbb");
            tableA.find("tbody tr:nth-child(" + (i + 1) + ")").css("background-color", "#bbbbbb");
            tableA_fix.find("tbody tr:nth-child(" + (i + 1) + ")").find('input[type="checkbox"]').prop('checked', true);
            tableA_fix.find("tbody tr:nth-child(" + (i + 1) + ")").find('input[type="checkbox"]').change();
        }
    } else {
        /* Otherwise just highlight one row and clean others */
        tableA_fix.find("tbody tr").css("background-color", "#ffffff");
        tableA_fix.find("tbody tr").find('input[type="checkbox"]').prop('checked', false);
        tableA_fix.find("tbody tr").find('input[type="checkbox"]').change();
        tableA_fix.find("tbody tr:nth-child(" + (row.index() + 1) + ")").css("background-color", "#bbbbbb");
        tableA_fix.find("tbody tr:nth-child(" + (row.index() + 1) + ")").find('input[type="checkbox"]').prop('checked', true);
        tableA.find("tbody tr").css("background-color", "#ffffff");
        tableA.find("tbody tr:nth-child(" + (row.index() + 1) + ")").css("background-color", "#bbbbbb");
        tableA_fix.find("tbody tr:nth-child(" + (row.index() + 1) + ")").find('input[type="checkbox"]').change();
        lastSelectedRow = row;
    }

    return lastSelectedRow;
}



// テーブルの幅をリセットする
function resetTableWidth(table_fix_head, table_fix, table_head, table) {
    table_fix.find("tbody tr td").width(table_fix_head.find("thead tr th").width() + 1);
    table.find("tbody tr td").width('');
    table_head.find("thead tr th").width('');
    var thwidthArry = [];
    var tdwidthArry = [];
    var columnNum = table_head.find('thead tr th').length;
    console.log(columnNum);
    var width = 0;
    for (var i = 1; i <= columnNum; i++) {
        var thwidth = table_head.find('thead tr th:nth-child(' + i + ')').width();
        var tdwidth = table.find('tbody tr td:nth-child(' + i + ')').width();
        thwidthArry.push(thwidth + 20);
        tdwidthArry.push(tdwidth + 20);
    }

    for (var i = 1; i <= columnNum; i++) {
        if (table.find("tr td:nth-child(" + i + ")").css("display") != "none") {
            if (thwidthArry[i - 1] > tdwidthArry[i - 1]) {
                table_head.find("thead tr th:nth-child(" + i + ")").width(thwidthArry[i - 1]);
                table.find("tbody tr td:nth-child(" + i + ")").width(thwidthArry[i - 1]);
                width += thwidthArry[i - 1];
            } else {
                table_head.find("thead tr th:nth-child(" + i + ")").width(tdwidthArry[i - 1]);
                table.find("tbody tr td:nth-child(" + i + ")").width(tdwidthArry[i - 1]);
                width += tdwidthArry[i - 1];
            }
        }
    }

    table_head.width(width + 100);
    table.width(width + 100);
}


// 行IDの再設定
function resetTableRowid(tableObj) {
    var rownum = 0;
    tableObj.find("tbody tr").each(function (i, e) {
        rownum += 1;
        $(this).find('td').first().text(rownum);
    });
}

function deleteAllRows(tableA, tableA_head, tableA_chkbox, tableA_chkbox_head, tableB, tableB_fix, allCheckObj, key) {

    // tableB.find('tbody tr').find('td:nth-child(1)').css('display', '');
    tableB.find("tbody tr").each(function (i, e) {
        $(this).find('td:nth-child(1)').css('display', '');
        removeTableBToTableA($(this), tableA, tableA_chkbox, allCheckObj, key);
    });

    tableB_fix.find("tbody").empty();

    // resetTableRowid(tableA);

    resetTableWidth(tableA_chkbox_head, tableA_chkbox, tableA_head, tableA);

    selectRow('hasChkbox', tableA_chkbox, tableA, allCheckObj);

    // 対象チェックボックスチェック状態の設定
    scanAllCheckbox(tableA_chkbox, allCheckObj);
}



function deleteAllRowsForPo(tableA, tableA_head, tableA_chkbox, tableA_chkbox_head, tableB, tableB_fix, allCheckObj, key) {

    // tableB.find('tbody tr').find('td:nth-child(1)').css('display', '');
    tableB.find("tbody tr").each(function (i, e) {
        if ($(this).find("input[name='lngorderstatuscode']").val() != 4) {
            $(this).find('td:nth-child(1)').css('display', '');
            removeTableBToTableA($(this), tableA, tableA_chkbox, allCheckObj, key);
        }
    });

    tableB_fix.find("tbody tr").each(function (i, e) {
        $index = $(this).index();
        console.log(tableB.find('tbody tr:nth-child(' + ($index + 1) + ')').find("input[name='lngorderstatuscode']").val());
        if (tableB.find('tbody tr:nth-child(' + ($index + 1) + ')').find("input[name='lngorderstatuscode']").val() != 4) {
            $(this).remove();
        }
    });

    // resetTableRowid(tableA);

    resetTableWidth(tableA_chkbox_head, tableA_chkbox, tableA_head, tableA);

    selectRow('hasChkbox', tableA_chkbox, tableA, allCheckObj);

    // 対象チェックボックスチェック状態の設定
    scanAllCheckbox(tableA_chkbox, allCheckObj);
}

function deleteRows(tableA, tableA_head, tableA_chkbox, tableA_chkbox_head, tableB, tableB_fix, allCheckObj, key) {
    tableB_fix.find("tbody tr").each(function (i, e) {
        var backgroud = $(this).css("background-color");
        if (backgroud != 'rgb(255, 255, 255)') {
            $(this).remove();
        }
    });
    tableB.find("tbody tr").each(function (i, e) {
        var backgroud = $(this).css("background-color");
        if (backgroud != 'rgb(255, 255, 255)') {
            tableB.find('tbody tr').find('td:nth-child(1)').css('display', '');
            removeTableBToTableA($(this), tableA, tableA_chkbox, allCheckObj, key);
        }
    });

    resetTableRowid(tableB_fix);
    resetTableWidth(tableA_chkbox_head, tableA_chkbox, tableA_head, tableA);

    selectRow('hasChkbox', tableA_chkbox, tableA, allCheckObj);

    // 対象チェックボックスチェック状態の設定
    scanAllCheckbox(tableA_chkbox, allCheckObj);
}



function deleteRowsForPo(tableA, tableA_head, tableA_chkbox, tableA_chkbox_head, tableB, tableB_fix, allCheckObj, key) {
    tableB_fix.find("tbody tr").each(function (i, e) {
        var backgroud = $(this).css("background-color");
        if (backgroud != 'rgb(255, 255, 255)') {
            $index = $(this).index();
            console.log(tableB.find('tbody tr:nth-child(' + ($index + 1) + ')').find("input[name='lngorderstatuscode']").val());
            if (tableB.find('tbody tr:nth-child(' + ($index + 1) + ')').find("input[name='lngorderstatuscode']").val() != 4) {
                $(this).remove();
            } else {
                alert("該当発注明細データが納品済のため、削除できません。");
                return;
            }
        }
    });
    tableB.find("tbody tr").each(function (i, e) {
        var backgroud = $(this).css("background-color");
        if (backgroud != 'rgb(255, 255, 255)') {
            if ($(this).find("input[name='lngorderstatuscode']").val() != 4) {
                tableB.find('tbody tr').find('td:nth-child(1)').css('display', '');
                removeTableBToTableA($(this), tableA, tableA_chkbox, allCheckObj, key);
            }
        }
    });

    resetTableRowid(tableB_fix);
    resetTableWidth(tableA_chkbox_head, tableA_chkbox, tableA_head, tableA);

    selectRow('hasChkbox', tableA_chkbox, tableA, allCheckObj);

    // 対象チェックボックスチェック状態の設定
    scanAllCheckbox(tableA_chkbox, allCheckObj);
}

function removeTableBToTableA(tableBRow, tableA, tableA_chkbox, allCheckObj, key) {
    var trhtml = tableBRow.html();
    var detailnoB = tableBRow.find(key).text();
    console.log(detailnoB);
    var rownum = 0;
    tableA.find("tbody tr").each(function (i, e) {
        var detailnoA = $(this).find(key).text();
        console.log(detailnoA);
        if (parseInt(detailnoA) > parseInt(detailnoB)) {
            rownum = i + 1;
            return false;
        }
    });
    if (rownum == 0) {
        tableA.find("tbody").append('<tr>' + trhtml + '</tr>');
        tableA_chkbox.find("tbody").append('<tr><td style="text-align:center;"><input type="checkbox" name="edit" style="width: 10px;"></td></tr>');
        rownum = tableA.find("tbody tr").length;
    } else {
        tableA.find('tbody tr:nth-child(' + rownum + ')').before('<tr>' + trhtml + '</tr>');
        tableA_chkbox.find('tbody tr:nth-child(' + rownum + ')').before('<tr><td style="text-align:center;"><input type="checkbox" name="edit" style="width: 10px;"></td></tr>');
    }

    tableBRow.remove();

    var chkboxObj = tableA_chkbox.find('tbody tr:nth-child(' + (rownum) + ') td:nth-child(1)').find('input[name="edit"]');
    setCheckBoxClickEvent(chkboxObj, tableA, tableA_chkbox, allCheckObj)
}

function setRowBackGroundColor(table, table_chkbox, rowindex, chkBoxStatus) {
    if (!chkBoxStatus) {
        table.find("tbody tr:nth-child(" + (rowindex + 1) + ")").css('background-color', '#bbbbbb');
        table_chkbox.find("tbody tr:nth-child(" + (rowindex + 1) + ")").css('background-color', '#bbbbbb');
        table_chkbox.find("tbody tr:nth-child(" + (rowindex + 1) + ")").find('td').find('input[name="edit"]').prop('checked', true);
    } else {
        table.find("tbody tr:nth-child(" + (rowindex + 1) + ")").css('background-color', '#ffffff');
        table_chkbox.find("tbody tr:nth-child(" + (rowindex + 1) + ")").css('background-color', '#ffffff');
        table_chkbox.find("tbody tr:nth-child(" + (rowindex + 1) + ")").find('td').find('input[name="edit"]').prop('checked', false);
    }
}

function rowUp(table, table_fix) {
    var len = table.find("tbody tr").length;
    for (var i = 1; i <= len; i++) {
        var row = table.find("tbody tr:nth-child(" + (i) + ")");
        var backgroud = row.css("background-color");
        if (backgroud != 'rgb(255, 255, 255)') {
            for (var j = i - 1; j >= 1; j--) {
                var row_prev = table.find("tbody tr:nth-child(" + (j) + ")");
                var row_prev_backgroud = row_prev.css("background-color");
                if (row_prev_backgroud == 'rgb(255, 255, 255)') {
                    row.insertBefore(row_prev);
                    break;
                }
            }
        }
    }

    len = table_fix.find("tbody tr").length;
    for (var i = 1; i <= len; i++) {
        var row = table_fix.find("tbody tr:nth-child(" + (i) + ")");
        var backgroud = row.css("background-color");
        if (backgroud != 'rgb(255, 255, 255)') {
            for (var j = i - 1; j >= 1; j--) {
                var row_prev = table_fix.find("tbody tr:nth-child(" + (j) + ")");
                var row_prev_backgroud = row_prev.css("background-color");
                if (row_prev_backgroud == 'rgb(255, 255, 255)') {
                    row.insertBefore(row_prev);
                    break;
                }
            }
        }
    }

    resetTableRowid(table_fix);
}

function rowDown(table, table_fix) {
    var len = table.find("tbody tr").length;
    for (var i = len; i >= 1; i--) {
        var row = table.find("tbody tr:nth-child(" + (i) + ")");
        var backgroud = row.css("background-color");
        if (backgroud != 'rgb(255, 255, 255)') {
            for (var j = i + 1; j <= len; j++) {
                var row_prev = table.find("tbody tr:nth-child(" + (j) + ")");
                var row_prev_backgroud = row_prev.css("background-color");
                if (row_prev_backgroud == 'rgb(255, 255, 255)') {
                    row.insertAfter(row_prev);
                    break;
                }
            }
        }
    }


    var len = table_fix.find("tbody tr").length;
    for (var i = len; i >= 1; i--) {
        var row = table_fix.find("tbody tr:nth-child(" + (i) + ")");
        var backgroud = row.css("background-color");
        if (backgroud != 'rgb(255, 255, 255)') {
            for (var j = i + 1; j <= len; j++) {
                var row_prev = table_fix.find("tbody tr:nth-child(" + (j) + ")");
                var row_prev_backgroud = row_prev.css("background-color");
                if (row_prev_backgroud == 'rgb(255, 255, 255)') {
                    row.insertAfter(row_prev);
                    break;
                }
            }
        }
    }

    resetTableRowid(table_fix);
}

function rowTop(table, table_fix) {
    var firsttr = table.find("tbody").find('tr').first();
    table.find("tbody").find('tr').each(function (i, e) {
        var backgroud = $(this).css("background-color");
        if (backgroud != 'rgb(255, 255, 255)') {
            $(this).insertBefore(firsttr);
        }
    });

    firsttr = table_fix.find("tbody").find('tr').first();
    table_fix.find("tbody").find('tr').each(function (i, e) {
        var backgroud = $(this).css("background-color");
        if (backgroud != 'rgb(255, 255, 255)') {
            $(this).insertBefore(firsttr);
        }
    });

    resetTableRowid(table_fix);
}


function rowBottom(table, table_fix) {
    var lasttr = table.find("tbody").find('tr').last();
    table.find("tbody").find('tr').each(function (i, e) {
        var backgroud = $(this).css("background-color");
        if (backgroud != 'rgb(255, 255, 255)') {
            $(this).insertAfter(lasttr);
        }
    });

    lasttr = table_fix.find("tbody").find('tr').last();
    table_fix.find("tbody").find('tr').each(function (i, e) {
        var backgroud = $(this).css("background-color");
        if (backgroud != 'rgb(255, 255, 255)') {
            $(this).insertAfter(lasttr);
        }
    });

    resetTableRowid(table_fix);
}