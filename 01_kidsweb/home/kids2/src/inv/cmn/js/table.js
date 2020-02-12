$(function () {

  $('body').on('keydown', function (e) {
    console.log('enter');
    if (e.which == 13) {
      $('img.add').click();
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
      var strmonetaryunitsign = v.strmonetaryunitsign;
      var lngmonetaryunitcode = v.lngmonetaryunitcode;
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
      // $('.price', $target_row).html(convertNumber(curtotalprice));
      $('.price', $target_row).html(money_format(lngmonetaryunitcode, strmonetaryunitsign, curtotalprice, 'price'));
      $('.taxclass .taxclasscode', $target_row).html('[' + taxclasscode + '] ');
      $('.taxclass .taxclassname', $target_row).html(taxclassname);
      $('.tax', $target_row).html(tax * 100 + '％');
      // $('.taxamount', $target_row).html(convertNumber(Math.round(taxamount)));
      $('.taxamount', $target_row).html(money_format(lngmonetaryunitcode, strmonetaryunitsign, taxamount, 'taxprice'));
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
      var strmonetaryunitsign = v.strmonetaryunitsign;
      var lngmonetaryunitcode = v.lngmonetaryunitcode;
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
      // $('.price', $target_row).html(convertNumber(curtotalprice));
      $('.price', $target_row).html(money_format(lngmonetaryunitcode, strmonetaryunitsign, curtotalprice, 'price'));
      $('.taxclass .taxclasscode', $target_row).html('[' + taxclasscode + '] ');
      $('.taxclass .taxclassname', $target_row).html(taxclassname);
      $('.tax', $target_row).html(tax * 100 + '％');
      // $('.taxamount', $target_row).html(convertNumber(Math.round(taxamount)));
      $('.taxamount', $target_row).html(money_format(lngmonetaryunitcode, strmonetaryunitsign, taxamount, 'taxprice'));
      $('.remarks', $target_row).html(strnote);
    });
  };

  /**
   * @method initTableA テーブルA 初期化
   */
  $.initTableA = function () {
    // スキャンチェックボックス
    scanAllCheckbox($("#tableA_chkbox"), $("#allChecked"));
    // チェックボックスクリックイベントの設定
    setCheckBoxClickEvent($('input[name="edit"]'), $("#tableA"), $("#tableA_chkbox"), $("#allChecked"));
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
  scanAllCheckbox($("#tableA_chkbox"), $("#allChecked"));
  // チェックボックスクリックイベントの設定
  setCheckBoxClickEvent($('input[name="edit"]'), $("#tableA"), $("#tableA_chkbox"), $("#allChecked"));

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
    scanAllCheckbox($("#tableA_chkbox"), $("#allChecked"));

    // チェックボックスクリックイベントの設定
    setCheckBoxClickEvent($('input[name="edit"]'), $("#tableA"), $("#tableA_chkbox"), $("#allChecked"));

    resetTableWidth($("#tableA_chkbox_head"), $("#tableA_chkbox"), $("#tableA_head"), $("#tableA"));

    selectRow('hasChkbox', $("#tableA_chkbox"), $("#tableA"), $("#allChecked"));
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
    scanAllCheckbox($("#tableA_chkbox"), $("#allChecked"));
    // チェックボックスクリックイベントの設定
    setCheckBoxClickEvent($('input[name="edit"]'), $("#tableA"), $("#tableA_chkbox"), $("#allChecked"));
    // テーブルBの幅をリセットする
    resetTableWidth($("#tableB_no_head"), $("#tableB_no"), $("#tableB_head"), $("#tableB"));
    // テーブルB行イベントの追加
    selectRow("", $("#tableB_no"), $("#tableB"), "");
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
  // 対象チェックボックスクリックイベントの設定
  setAllCheckClickEvent($("#allChecked"), $("#tableA"), $("#tableA_chkbox"));

  // テーブルA 追加ボタン
  $('img.add').on('click', function (e) {
    e.preventDefault();

    var $all_chkbox_rows = $('tbody tr', $tableA_chkbox);
    var $all_checkbox = $all_chkbox_rows.find('input[type="checkbox"]');
    var checked = [];

    var tax = "";
    if ($('#tableB tbody tr').length > 0) {
      console.log($('#tableB tbody tr:nth-child(1)').find('.tax').text());
      tax = $('#tableB tbody tr:nth-child(1)').find('.tax').text();
    }

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

        var tmptax =  $('#tableA tbody tr:nth-child(' + (rowindex + 1) + ')').find('.tax').text();
        console.log(tmptax);
        if (tax != "" && tmptax != tax)
        {
          alert('消費税率の異なる納品書は請求書の明細に混在できません');
          return false;
        }



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
    scanAllCheckbox($("#tableA_chkbox"), $("#allChecked"));

    // 金額計算
    billingAmount();

    // テーブルA 幅再設定
    resetTableWidth($("#tableA_chkbox_head"), $("#tableA_chkbox"), $("#tableA_head"), $("#tableA"));
    // テーブルB 幅再設定
    resetTableWidth($("#tableB_no_head"), $("#tableB_no"), $("#tableB_head"), $("#tableB"));

    selectRow('hasChkbox', $("#tableA_chkbox"), $("#tableA"), $("#allChecked"));
    selectRow("", $("#tableB_no"), $("#tableB"), "");
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
    scanAllCheckbox($("#tableA_chkbox"), $("#allChecked"));
    // チェックボックスクリックイベントの設定
    setCheckBoxClickEvent($('input[name="edit"]'), $("#tableA"), $("#tableA_chkbox"), $("#allChecked"));
    // 金額計算
    billingAmount();

    // テーブルA 幅再設定
    resetTableWidth($("#tableA_chkbox_head"), $("#tableA_chkbox"), $("#tableA_head"), $("#tableA"));

    // テーブルB 幅再設定
    resetTableWidth($("#tableB_no_head"), $("#tableB_no"), $("#tableB_head"), $("#tableB"));
    selectRow('hasChkbox', $("#tableA_chkbox"), $("#tableA"), $("#allChecked"));
    selectRow("", $("#tableB_no"), $("#tableB"), "");
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
    scanAllCheckbox($("#tableA_chkbox"), $("#allChecked"));
    // チェックボックスクリックイベントの設定
    setCheckBoxClickEvent($('input[name="edit"]'), $("#tableA"), $("#tableA_chkbox"), $("#allChecked"));

    // 金額計算
    billingAmount();

    // テーブルA 幅再設定
    resetTableWidth($("#tableA_chkbox_head"), $("#tableA_chkbox"), $("#tableA_head"), $("#tableA"));


    // テーブルB 幅再設定
    resetTableWidth($("#tableB_no_head"), $("#tableB_no"), $("#tableB_head"), $("#tableB"));

    selectRow('hasChkbox', $("#tableA_chkbox"), $("#tableA"), $("#allChecked"));
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

  // function convertNumber(str) {
  //   if (str != "" && str != undefined && str != "null") {
  //     return Number(str).toLocaleString(undefined, {
  //       minimumFractionDigits: 0,
  //       maximumFractionDigits: 0
  //     });
  //   } else {
  //     return "0";
  //   }
  // }


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
            price = (tableB_row[i].cells[j].innerText.replace(/,/g, '')).split(' ')[1];
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
      console.log($('input[name="lngmonetaryunitcode"]').val());
      var fracctiondigits = 0
      if ($('input[name="lngmonetaryunitcode"]').val() == '1') {
        fracctiondigits = 0;
      } else {
        fracctiondigits = 2;
      }
      $('input[name="curthismonthamount"]').val(convertNumber(thisMonthAmount, fracctiondigits)).change();
      $('input[name="curtaxprice"]').val(convertNumber(Math.round(taxPrice), 0)).change();
      $('input[name="notaxcurthismonthamount"]').val(convertNumber(Math.round(noTaxMonthAmount), fracctiondigits)).change();
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
});
