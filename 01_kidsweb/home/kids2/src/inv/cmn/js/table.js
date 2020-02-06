$(function () {

  $('body').on('keydown', function (e) {
    console.log('enter');
    if (e.which == 13) {
      console.log('122');
      $('img.add').click();
      console.log('1444');
    }
  });

  // response ディープコピー用配列
  var data = [];

  // dom 初期化用空配列
  var dataEmpty = new Array(20).fill('');

  // テーブルA 複製dom格納用配列
  var domA = [];

  // テーブルB 複製dom格納用配列
  var domB = [];

  // データ一時保管 ＊送信前のデータ格納用配列
  var temp = [];

  // 選択行インデックス格納用配列
  var selectedRowIndexes = [];

  // サブクエリキャッシュ
  $tableA = $('#tableA');
  $tableB = $('#tableB');
  $tableA_chkbox = $('#tableA_chkbox');
  $tableB_no = $('#tableB_no');

  // テーブルA <tbody>, <tr>
  $tableA_tbody = $('tbody', $tableA);
  $tableA_row = $('tr', $tableA_tbody);

  // テーブルA_chkbox <tbody>, <tr>
  $tableA_chkbox_tbody = $('tbody', $tableA_chkbox);
  $tableA_chkbox_row = $('tr', $tableA_chkbox_tbody);

  // テーブルB <tbody>, <tr>
  $tableB_tbody = $('tbody', $tableB);
  $tableB_row = $('tr', $tableB_tbody);
  // テーブルB_no <tbody>, <tr>
  $tableB_no_tbody = $('tbody', $tableB_no);
  $tableB_no_row = $('tr', $tableB_no_tbody);

  // 複製元の $tableA_row を削除
  $tableA_row.remove();
  $tableA_chkbox_row.remove();

  // 複製元の $tableB_row を削除
  $tableB_row.remove();
  $tableB_no_row.remove();

  // 開始日時フォーカスを失ったときの処理
  $('input[name="curlastmonthbalance"]').on('blur', function () {
    var val = $(this).val();
    var thisMonthAmount = Number($('input[name="curthismonthamount"]').val().replace(/,/g, ''));
    var taxPrice = Number($('input[name="curtaxprice"]').val().replace(/,/g, ''));
    var curLastMonthBalance = Number(val);
    // 差引合計額
    // 前月請求残額 + 当月請求額 + 消費税
    var noTaxMonthAmount = curLastMonthBalance + thisMonthAmount + taxPrice;
    $('input[name="notaxcurthismonthamount"]').val(convertNumber(Math.round(noTaxMonthAmount))).change();
    $(this).val(convertNumber(val));
  });
  /**
   * ----------------------------------------------------------------------------------------------------
   * 関数群
   * ----------------------------------------------------------------------------------------------------
   */

  /**
  * @method createSkeletonTable テーブル生成
  * @param data {Array} 基となるデータ配列
  * @param clone {Object} 複製元セレクター
  * @param dom {Array} 複製dom格納用配列
  * @param target {Object} 出力先ターゲットセレクター
  */
  $.createSkeletonTable = function (data, clone, dom, target) {
    dom = [];
    target.empty();
    $.each(data, function (i) {
      var $clone = clone.clone();
      dom.push({ html: $clone });
      var $html = dom[i].html;
      target.append($html);
    });
  };

  /**
   * @method addDataTableA テーブルAにデータを追加
   */
  $.addDataTableA = function () {
    if (data[0] === '') return;
    $.each(data, function (i, v) {
      var $target_row = $('tbody tr', $tableA).eq(i);
      var slipno = v.lngslipno;
      var revisionno = v.lngrevisionno;
      var slipcode = v.strslipcode;
      var customercode = v.lngdeliveryplacecode;
      var customername = v.strdeliveryplacename;
      var deliverydate = v.dtmdeliverydate.replace(/-/g, '/');
      var curtotalprice = Number(v.curtotalprice);
      var taxclasscode = v.lngtaxclasscode;
      var taxclassname = v.strtaxclassname;
      var tax = Number(v.curtax);
      var taxamount = (curtotalprice * (tax * 100)) / 100;
      if (taxclasscode == 1) taxamount = 0;
      var id = v.strslipcode;
      var strnote = v.strnote;

      // html 出力
      $target_row.attr('data-id', id);
      $target_row.attr('slipno', slipno);
      $target_row.attr('revisionno', revisionno);
      $('.slipcode', $target_row).html(slipcode);
      $('.customer .customercode', $target_row).html('[' + customercode + '] ');
      $('.customer .customername', $target_row).html(customername);
      $('.deliverydate', $target_row).html(deliverydate);
      $('.price', $target_row).html(convertNumber(curtotalprice));
      $('.taxclass .taxclasscode', $target_row).html('[' + taxclasscode + '] ');
      $('.taxclass .taxclassname', $target_row).html(taxclassname);
      $('.tax', $target_row).html(tax * 100 + '％');
      $('.taxamount', $target_row).html(convertNumber(Math.round(taxamount)));
      $('.remarks', $target_row).html(strnote);


    });
  };

  /**
   * @method addDataTableB テーブルBにデータを追加
   */
  $.addDataTableB = function () {
    $.each(temp, function (i, v) {
      var $target_row = $('tbody tr', $tableB).eq(i);
      var slipcode = v.strslipcode;
      var customercode = v.lngdeliveryplacecode;
      var customername = v.strdeliveryplacename;
      var deliverydate = v.dtmdeliverydate.replace(/-/g, '/');
      var curtotalprice = Number(v.curtotalprice);
      var taxclasscode = v.lngtaxclasscode;
      var taxclassname = v.strtaxclassname;
      var tax = Number(v.curtax);
      var taxamount = (curtotalprice * (tax * 100)) / 100;
      if (taxclasscode == 1) taxamount = 0;
      var id = v.strslipcode;
      var slipno = v.lngslipno;
      var revisionno = v.lngrevisionno;
      var strnote = v.strnote;

      // html 出力
      $target_row.attr('data-id', id);
      $target_row.attr('slipno', slipno);
      $target_row.attr('revisionno', revisionno);
      $('.slipcode', $target_row).html(slipcode);
      $('.customer .customercode', $target_row).html('[' + customercode + '] ');
      $('.customer .customername', $target_row).html(customername);
      $('.deliverydate', $target_row).html(deliverydate);
      $('.price', $target_row).html(convertNumber(curtotalprice));
      $('.taxclass .taxclasscode', $target_row).html('[' + taxclasscode + '] ');
      $('.taxclass .taxclassname', $target_row).html(taxclassname);
      $('.tax', $target_row).html(tax * 100 + '％');
      $('.taxamount', $target_row).html(convertNumber(Math.round(taxamount)));
      $('.remarks', $target_row).html(strnote);
    });
  };

  /**
   * @method scanAllCheckbox スキャンチェックボックス
   */
  $.scanAllCheckbox = function () {

    var $all_rows = $('tbody tr', $tableA);
    var $all_chkbox_rows = $('tbody tr', $tableA_chkbox);
    var $all_checkbox = $all_chkbox_rows.find('input[type="checkbox"]');

    // 有効 <tr> ＊選択可能行
    var count_checked = 0;
    var count_disabled = 0;

    console.log(data.length);
    console.log(!data.length);

    // data がない場合、全選択／解除チェックボックスを寝かせて無効化
    if (!data.length) {
      $('#allChecked').prop({ 'checked': false, 'disabled': true });
    } else {
      $('#allChecked').prop('disabled', false);
    }

    // <tr> に data-id 属性が存在しない場合、該当チェックボックスを無効化
    $.each($all_rows, function () {
      if (!$(this).data('id')) {
        $all_checkbox.prop({ 'checked': false, 'disabled': true });
      }
    });

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

  /**
   * @method initTableA テーブルA 初期化
   */
  $.initTableA = function () {
    // スキャンチェックボックス
    $.scanAllCheckbox();
    data = [];
    $.createSkeletonTable(dataEmpty, $tableA_row, domA, $tableA_tbody);
  };

  /**
   * @method setTableSorter テーブルソート機能設定
   */
  $.setTableSorter = function () {
    $('#tableA, #tableB').trigger('destroy');
    $('#tableA').tablesorter({
      headers: {
        0: { sorter: false },
        1: { sorter: false }
      }
    });
    $('#tableB').tablesorter({
      headers: {
        0: { sorter: false }
      }
    });
  };

  /**
   * ----------------------------------------------------------------------------------------------------
   * 初期表示
   * ----------------------------------------------------------------------------------------------------
   */

  // スキャンチェックボックス
  $.scanAllCheckbox();

  $.createTable = function (response) {
    data = (response === undefined || response && !response.length) ? dataEmpty : Array.from(new Set(response));
    console.log(data);
    // テーブルA生成
    $.createSkeletonTable(data, $tableA_row, domA, $tableA_tbody);
    $.createSkeletonTable(data, $tableA_chkbox_row, domA, $tableA_chkbox_tbody);
    $.addDataTableA();

    // テーブルソート機能設定
    $.setTableSorter();

    // スキャンチェックボックス
    $.scanAllCheckbox();

    $.resetTableAWidth();

    selectRow($("#tableA_chkbox"), $("#tableA"));
  };

  $.createTableRenew = function (response) {
    data = (response === undefined || response && !response.length) ? dataEmpty : Array.from(new Set(response));

    // テーブルB生成
    $.createSkeletonTable(data, $tableB_row, domB, $tableB_tbody);
    $.createSkeletonTable(data, $tableB_no_row, domB, $tableB_no_tbody);
    $.each(data, function (i) {
      console.log(i);
      console.log(data[i]);
      temp.push(data[i]);
    });
    $.addDataTableB();
    // テーブルソート機能設定
    $.setTableSorter();
    // スキャンチェックボックス
    $.scanAllCheckbox();
    // テーブルBの幅をリセットする
    $.resetTableBWidth();
    // テーブルB行イベントの追加
    selectRow($("#tableB_no"), $("#tableB"));
    // 顧客名称の取得
    $('input[name="lngCustomerCode"]').trigger('change');
　　// テーブルAデータの初期化
    data = [];
  };

  /**
   * ----------------------------------------------------------------------------------------------------
   * イベント設定
   * ----------------------------------------------------------------------------------------------------
   */

  // テーブルA 全選択／解除チェックボックス
  $(document).on('change', '#allChecked', function (e) {
    e.preventDefault();

    var $all_rows = $('tbody tr', $tableA);
    var $all_chkbox_rows = $('tbody tr', $tableA_chkbox);
    var $all_chkbox_rows_checkbox = $('input[type="checkbox"]', $tableA_chkbox);

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

  // テーブルA 追加ボタン
  $('img.add').on('click', function (e) {
    e.preventDefault();

    var $all_chkbox_rows = $('tbody tr', $tableA_chkbox);
    var $all_checkbox = $all_chkbox_rows.find('input[type="checkbox"]');
    var checked = [];

    $.each($all_chkbox_rows, function () {
      var $isChecked = $(this).find('input[type="checkbox"]').prop('checked');
      checked.push($isChecked);
    });

    // チェックボックスがひとつも選択されていない場合、return
    if ($.inArray(true, checked) === -1) return;

    // チェックボックスの該当データをすべて temp に格納
    $.each($all_checkbox, function () {
      if ($(this).prop('checked')) {
        var rowindex = $(this).closest('tr').index();
        console.log(rowindex);
        var $data_id = $('#tableA tbody tr:nth-child(' + (rowindex + 1) + ')').data('id');
        console.log($data_id);
        var data_index = data.findIndex(function (value) { return value.strslipcode == $data_id });
        if (data_index !== -1) {
          // id重複チェック
          let sameId = false;
          $.each(temp, function (i, v) {
            if (v.strslipcode == data[data_index].strslipcode) { sameId = true; }
          });
          if (sameId == true) { return; }
          temp.push(data[data_index]);
          // 該当データを data から削除
          data.splice(data_index, 1);
        }
      }
    });

    // テーブルBにデータを追加
    $.createSkeletonTable(temp, $tableB_row, domB, $tableB_tbody);
    $.createSkeletonTable(temp, $tableB_no_row, domB, $tableB_no_tbody);
    $.addDataTableB();

    // テーブルA 再生成
    $.createSkeletonTable(data, $tableA_row, domA, $tableA_tbody);
    $.createSkeletonTable(data, $tableA_chkbox_row, domA, $tableA_chkbox_tbody);
    $.addDataTableA();

    // テーブルソート機能設定
    $.setTableSorter();

    // スキャンチェックボックス
    $.scanAllCheckbox();

    // 金額計算
    billingAmount();

    // テーブルA 幅再設定
    $.resetTableAWidth();

    // テーブルB 幅再設定
    $.resetTableBWidth();

    selectRow($("#tableA_chkbox"), $("#tableA"));
    selectRow($("#tableB_no"), $("#tableB"));
  });

  // テーブルB 削除ボタン
  $('img.delete').on('click', function (e) {
    e.preventDefault();

    var $all_rows = $('tbody tr', $tableB);

    if (!$all_rows.length) return;

    $.each($all_rows, function () {
      if ($(this).css("background-color") != 'rgb(255, 255, 255)') {
        var $data_id = $(this).data('id');
        var temp_index = temp.findIndex(function (value) { return value.strslipcode == $data_id });
        data.push(temp[temp_index]);
        // 該当データを temp から削除
        if (temp_index !== -1) {
          temp.splice(temp_index, 1);
        }
      }
    });

    // テーブルB 再生成
    $.createSkeletonTable(temp, $tableB_row, domB, $tableB_tbody);
    $.createSkeletonTable(temp, $tableB_no_row, domB, $tableB_no_tbody);
    $.addDataTableB();

    // テーブルA 再生成
    $.createSkeletonTable(data, $tableA_row, domA, $tableA_tbody);
    $.createSkeletonTable(data, $tableA_chkbox_row, domA, $tableA_chkbox_tbody);
    $.addDataTableA();

    // テーブルソート機能設定
    $.setTableSorter();

    // スキャンチェックボックス
    $.scanAllCheckbox();

    // 金額計算
    billingAmount();

    // テーブルA 幅再設定
    $.resetTableAWidth();

    // テーブルB 幅再設定
    $.resetTableBWidth();

    selectRow($("#tableA_chkbox"), $("#tableA"));
    selectRow($("#tableB_no"), $("#tableB"));
  });

  // テーブルB 全削除ボタン
  $('img.alldelete').on('click', function (e) {
    e.preventDefault();

    var $tableB_row = $('tbody tr', $tableB);

    if (!$tableB_row.length) return;

    $.each($tableB_row, function () {
      var $data_id = $(this).data('id');
      var temp_index = temp.findIndex(function (value) { return value.strslipcode == $data_id });
      data.push(temp[temp_index]);
      // 該当データを temp から削除
      if (temp_index !== -1) {
        temp.splice(temp_index, 1);
      }
    });

    // テーブルA 再生成
    $.createSkeletonTable(data, $tableA_row, domA, $tableA_tbody);
    $.createSkeletonTable(data, $tableA_chkbox_row, domA, $tableA_chkbox_tbody);
    $.addDataTableA();

    // テーブルB 初期化
    $tableB_tbody.empty();
    $tableB_no_tbody.empty();

    // テーブルソート機能設定
    $.setTableSorter();

    // スキャンチェックボックス
    $.scanAllCheckbox();

    // 金額計算
    billingAmount();

    // テーブルA 幅再設定
    $.resetTableAWidth();

    // テーブルB 幅再設定
    $.resetTableBWidth();

    selectRow($("#tableA_chkbox"), $("#tableA"));
  });

  // 検索条件入力ボタン
  $('img.search').on('click', function (e) {
    e.preventDefault();

    // selectedRowIndexes 初期化
    selectedRowIndexes = [];

    var $all_rows = $('tbody tr', $tableA);
    var $all_checkbox = $all_rows.find('input[type="checkbox"]');

    // チェックボックスのチェックをすべて解除
    $.each($all_checkbox, function () {
      if ($(this).prop('checked')) {
        $(this).prop('checked', false);
      }
    });

    // すべての current を削除
    $.each($all_rows, function () {
      $(this).children('td').removeClass('current');
    });

    // 全選択／解除チェックボックスのチェックを解除
    if ($('#allChecked').prop('checked')) {
      $('#allChecked').prop('checked', false);
    }

    url = $('input[name="invConditionUrl"]').val();
    // 納品書検索ウィンドウをポップアップ表示
    sub_win = window.open(url, 'winSearch', "width=800,height=500,scrollbars=yes");
    // 請求書検索ウィンドウをポップアップ表示
  });

  /**
   * ----------------------------------------------------------------------------------------------------
   * 特殊キー + click イベント処理
   * ----------------------------------------------------------------------------------------------------
   */

  // ctrl + 左 click コンテクストメニュー非表示
  // $(document).on('contextmenu', function (e) {
  //   if (e.which === 1) return false;
  // });

  // // ctrl, shift キーイベントを document に設定
  // var isCtrlKey = false;
  // var isShiftKey = false;

  // $(document).on({
  //   'keydown': function (e) {
  //     if (e.ctrlKey) isCtrlKey = true;
  //     if (e.shiftKey) isShiftKey = true;
  //   },
  //   'keyup': function (e) {
  //     isCtrlKey = false;
  //     isShiftKey = false;
  //   }
  // });

  // // テーブルA イベント処理
  // $(document).on('mousedown', '#tableA tbody tr', function (e) {
  //   e.preventDefault();

  //   var $tableA_rows = $('#tableA tbody tr');
  //   var $tableA_rows_length = $tableA_rows.length;

  //   // テーブルA <tr> ctrl + click -> テーブルBにデータを追加
  //   if (isCtrlKey && e.which === 1) {
  //     var $data_id = $(this).data('id');
  //     var data_index = data.findIndex(function (value) { return value.strslipcode == $data_id });
  //     if (data_index !== -1) {
  //       temp.push(data[data_index]);
  //       // 該当データを data から削除
  //       data.splice(data_index, 1);
  //     }

  //     // テーブルBにデータを追加
  //     $.createSkeletonTable(temp, $tableB_row, domB, $tableB_tbody);
  //     $.addDataTableB();

  //     // テーブルA 再生成
  //     $.createSkeletonTable(data, $tableA_row, domA, $tableA_tbody);
  //     $.addDataTableA();

  //     // テーブルソート機能設定
  //     $.setTableSorter();

  //   }

  //   // テーブルA <input type="checkbox">
  //   if (e.target.nodeName === 'INPUT') {
  //     if ($tableA_rows_length !== 1 && isShiftKey && e.which === 1) {
  //       var $row_index = $(this).index();

  //       // 選択された行インデックスを selectedRowIndexes に格納
  //       selectedRowIndexes.push($row_index);

  //       // 昇順ソート
  //       selectedRowIndexes.sort(function (a, b) { return a - b });

  //       var min = selectedRowIndexes[0] + 1;
  //       var max = selectedRowIndexes[1] - 1;
  //       var array_range = [];
  //       for (var i = min; i <= max; ++i) {
  //         array_range.push(i);
  //       }

  //       // 複数選択
  //       if (!e.target.checked) {
  //         $(this).addClass('selected').children('td').addClass('current');

  //         $.each(array_range, function (i, v) {
  //           $tableA_rows.eq(v).addClass('selected').children('td').addClass('current');
  //           $tableA_rows.eq(v).find('input[type="checkbox"]').prop('checked', true);
  //         });
  //       }
  //       // 複数選択解除
  //       else {
  //         $(this).removeClass('selected').children('td').removeClass('current');

  //         $.each(array_range, function (i, v) {
  //           $tableA_rows.eq(v).removeClass('selected').children('td').removeClass('current');
  //           $tableA_rows.eq(v).find('input[type="checkbox"]').prop('checked', false);
  //         });
  //       }

  //       // selectedRowIndexes 初期化
  //       if (selectedRowIndexes.length >= 2) selectedRowIndexes = [];
  //     } else {
  //       if (!e.target.checked) {
  //         $(this).addClass('selected').children('td').addClass('current');
  //       } else {
  //         $(this).removeClass('selected').children('td').removeClass('current');
  //       }
  //     }
  //   }

  //   // テーブルA <tr> shift + click -> 複数選択／解除
  //   else if ($tableA_rows_length !== 1 && isShiftKey && e.which === 1) {
  //     var $row_index = $(this).index();

  //     // 選択された行インデックスを selectedRowIndexes に格納
  //     selectedRowIndexes.push($row_index);

  //     // 昇順ソート
  //     selectedRowIndexes.sort(function (a, b) { return a - b });

  //     // 複数選択
  //     if (!$(this).hasClass('selected')) {
  //       $(this).addClass('selected').children('td').addClass('current');
  //       $(this).find('input[type="checkbox"]').prop('checked', true);

  //       var i = selectedRowIndexes[0];
  //       while (i <= selectedRowIndexes[1]) {
  //         $tableA_rows.eq(i).addClass('selected').children('td').addClass('current');
  //         $tableA_rows.eq(i).find('input[type="checkbox"]').prop('checked', true);
  //         ++i;
  //       }
  //     }
  //     // 複数選択解除
  //     else {
  //       $(this).removeClass('selected').children('td').removeClass('current');
  //       $(this).find('input[type="checkbox"]').prop('checked', false);

  //       var i = selectedRowIndexes[0];
  //       while (i <= selectedRowIndexes[1]) {
  //         $tableA_rows.eq(i).removeClass('selected').children('td').removeClass('current');
  //         $tableA_rows.eq(i).find('input[type="checkbox"]').prop('checked', false);
  //         ++i;
  //       }
  //     }

  //     // selectedRowIndexes 初期化
  //     if (selectedRowIndexes.length >= 2) selectedRowIndexes = [];
  //   }

  //   // selectedRowIndexes 初期化
  //   if ($tableA_rows_length === 1) selectedRowIndexes = [];

  //   // スキャンチェックボックス
  //   $.scanAllCheckbox();
  // });

  // // テーブルB <tr> 特殊キー + click イベント処理
  // $(document).on('mousedown', '#tableB tbody tr', function (e) {
  //   e.preventDefault();

  //   var $tableB_rows = $('#tableB tbody tr');
  //   var $tableB_rows_length = $tableB_rows.length;

  //   // テーブルB <tr> shift + click -> 複数選択／解除
  //   if ($tableB_rows_length !== 1 && isShiftKey && e.which === 1) {
  //     var $row_index = $(this).index();

  //     // 選択された行インデックスを selectedRowIndexes に格納
  //     selectedRowIndexes.push($row_index);

  //     // 昇順ソート
  //     selectedRowIndexes.sort(function (a, b) { return a - b });

  //     // 複数選択
  //     if (!$(this).hasClass('selected')) {
  //       $(this).addClass('selected').children('td').addClass('current');

  //       var i = selectedRowIndexes[0];
  //       while (i <= selectedRowIndexes[1]) {
  //         $tableB_rows.eq(i).addClass('selected').children('td').addClass('current');
  //         ++i;
  //       }
  //     }
  //     // 複数選択解除
  //     else {
  //       $(this).removeClass('selected').children('td').removeClass('current');

  //       var i = selectedRowIndexes[0];
  //       while (i <= selectedRowIndexes[1]) {
  //         $tableB_rows.eq(i).removeClass('selected').children('td').removeClass('current');
  //         ++i;
  //       }
  //     }

  //     // selectedRowIndexes 初期化
  //     if (i === selectedRowIndexes[1] + 1) selectedRowIndexes = [];
  //   }
  //   else {
  //     if ($(this).hasClass('selected')) {
  //       $(this).removeClass('selected').children('td').removeClass('current');
  //     } else {
  //       $(this).addClass('selected').children('td').addClass('current');
  //     }
  //   }
  // });

  function convertNumber(str) {
    if (str != "" && str != undefined && str != "null") {
      return Number(str).toLocaleString(undefined, {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
      });
    } else {
      return "0";
    }
  }


  // 金額計算
  function billingAmount() {

    // 出力明細一覧取得
    tableB = $('#tableB');
    tableB_tbody = $('tbody', $tableB);
    tableB_row = $('tbody tr', $tableB);

    // 出力明細一覧エリアの1行目の消費税率を取得する
    let tax = false;
    for (var i = 0, rowlen = tableB_row.length; i < rowlen; i++) {
      if (tax !== false) continue;
      for (var j = 0, collen = tableB_row[i].cells.length; j < collen; j++) {
        if (tax !== false || !tableB_row[i].cells[j]) continue;
        if (tableB_row[i].cells[j].className == 'tax right') {
          // 消費税率
          console.log(tableB_row[i].cells[j].innerText);
          strtax = tableB_row[i].cells[j].innerText.replace(/[^0-9]/g, '');
          tax = Number(strtax) / 100;
        }
      }
    }

    // 前月請求残額
    // 納品日が「自」以前である明細の税抜金額の合計+その合計に対して課税区分に応じて計算された消費税
    let lastMonthBalance = 0;
    let curLastMonthBalance = 0;
    // 当月請求額
    // 納品日が「自」以降である明細の税抜金額の合計
    let thisMonthAmount = 0;
    // 消費税
    // 当月請求額に対して課税区分に応じて計算
    let taxPrice = 0;
    // 差引合計額
    // 前月請求残額 + 当月請求額 + 消費税"
    let noTaxMonthAmount = 0;

    // 「自」「至」を計算する
    // selectClosedDay();

    var chargetern = function () {
      // 「自」取得
      let chargeternstart = $('input[name="dtmchargeternstart"]').val();
      let cs = isEmpty(chargeternstart);
      // 「至」取得
      let chargeternend = $('input[name="dtmchargeternend"]').val();
      let ce = isEmpty(chargeternend);

      if (cs == 0 || ce == 0) return false;

      startStamp = new Date(chargeternstart);
      endStamp = new Date(chargeternend);

      for (var i = 0, rowlen = tableB_row.length; i < rowlen; i++) {
        let deliverydate = false;
        let price = false;
        let data = false;

        for (var j = 0, collen = tableB_row[i].cells.length; j < collen; j++) {
          if (!tableB_row[i].cells[j].innerText) continue;
          if (tableB_row[i].cells[j].className == 'deliverydate') {
            // 納品日
            deliverydate = tableB_row[i].cells[j].innerText;
          }
          if (tableB_row[i].cells[j].className == 'price right') {

            console.log(tableB_row[i].cells[j].innerText);
            // 税抜金
            price = tableB_row[i].cells[j].innerText.replace(/,/g, '');
          }
        }
        console.log(price);
        if (!deliverydate || !price) continue;
        date = splitDate(deliverydate);
        deliverydateStamp = new Date(deliverydate);

        if (deliverydateStamp <= startStamp) {
          // 前月請求残額
          // lastMonthBalance += Number(price);
        } else {
          // 当月請求額
          //          thisMonthAmount += Number(price);
        }
        thisMonthAmount += Number(price);
      }

      // console.log(lastMonthBalance);
      console.log(tax);
      // 前月請求残額(消費税込み)
      // curLastMonthBalance = lastMonthBalance + (lastMonthBalance * (tax * 100)) / 100;
      curLastMonthBalance = Number($('input[name="curlastmonthbalance"]').val().replace(/,/g, ''));
      // 消費税計算
      // 当月請求額に対して課税区分に応じて計算
      taxPrice = (thisMonthAmount * (tax * 100)) / 100;
      // 差引合計額
      // 前月請求残額 + 当月請求額 + 消費税
      noTaxMonthAmount = curLastMonthBalance + thisMonthAmount + taxPrice;
      // 結果を繁栄
      // $('input[name="curlastmonthbalance"]').val(convertNumber(Math.round(curLastMonthBalance))).change();
      $('input[name="curthismonthamount"]').val(convertNumber(thisMonthAmount)).change();
      $('input[name="curtaxprice"]').val(convertNumber(Math.round(taxPrice))).change();
      $('input[name="notaxcurthismonthamount"]').val(convertNumber(Math.round(noTaxMonthAmount))).change();
    };
    var result = setTimeout(chargetern, 500);

  }

  // 真偽値の文字列表現を取得
  function isEmpty(val) {
    if (val) {
      return '1';
    } else {
      return '0';
    }
  }

  // 請求日の日付をチェックして正しければ「/」で分割
  function splitDate(str) {

    // 日付フォーマット yyyy/mm(m)/dd(d)形式
    var regDate = /(\d{4})\/(\d{1,2})\/(\d{1,2})/;

    // yyyy/mm/dd形式か
    if (!(regDate.test(str))) {
      return false;
    }

    // 日付文字列の字句分解
    var regResult = regDate.exec(str);
    var yyyy = regResult[1];
    var mm = regResult[2];
    var dd = regResult[3];
    var di = new Date(yyyy, mm - 1, dd);
    // 日付の有効性チェック
    if (di.getFullYear() == yyyy && di.getMonth() == mm - 1 && di.getDate() == dd) {
      return regResult;
    }

    return false;
  };

  // テーブルAの幅をリセットする
  $.resetTableAWidth = function () {
    $("#tableA thead").css('display', '');
    $("#tableA tbody tr td").width('');
    $("#tableA thead tr th").width('');
    $("#tableA_head tr th").width('');
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


  // テーブルBの幅をリセットする
  $.resetTableBWidth = function () {
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

    $("#tbl_edit_detail_head").width(width + 100);
    $("#tbl_edit_detail_body").width(width + 100);
  }

  // テーブルの行をクリックする時のイベント
  function selectRow(objA, objB) {
    var rows = objA.find('tbody tr');
    var rows = objB.find('tbody tr');
    var lastSelectedRow;
    /* Create 'click' event handler for rows */
    objA.find('tbody tr').on('click', function (e) {
      lastSelectedRow = trClickEvent($(this), lastSelectedRow, e, objA, objB);
      scanAllCheckbox();
    });


    /* Create 'click' event handler for rows */
    objB.find('tbody tr').on('click', function (e) {
      lastSelectedRow = trClickEvent($(this), lastSelectedRow, e, objA, objB);
      scanAllCheckbox();
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
