// handsontableの操作関数

$(function(){
  // データの取得
  var script = $('#script').attr('data-param');
  var result = JSON.parse(script);

  // シート数の取得
  var sheetNumber = Object.keys(result).length;

  var grid = [];
  var table = [];

  // 表示用データの作成
  for (var sheetNum = 0; sheetNum < sheetNumber; sheetNum++) {
    var sheetData = result[sheetNum];
    // 開始行列、終了行列の取得
    var startRow = sheetData['startRow'];
    var endRow = sheetData['endRow'];
    var startColumn = sheetData['startColumn'];
    var endColumn = sheetData['endColumn'];

    // セルのデータ取得
    var cellData = sheetData['cellData'];

    var cellValue = [];

    // セルの情報を配列に格納する
    for (var i = startRow; i <= endRow; i++) {
      var rowValue = [];
      for (var j = startColumn; j <= endColumn; j++) {
        rowValue.push(cellData[i][j]['value']);
      }
      cellValue.push(rowValue);
    }

    // マージセルの取得
    var merge = sheetData['mergedCellsList'];
    // 行の高さ、列の幅を取得
    var rowHeight = sheetData['rowHeight'];
    var columnWidth = sheetData['columnWidth'];

    var height = window.outerHeight;

    var gridWidth = 0;
    for (var i = 0; i < columnWidth.length; i++) {
      gridWidth += columnWidth[i];
    }

    var width = gridWidth + 30;

    // ウィンドウのリサイズ
    window.resizeTo(width, height);

    // セルのクラスを取得
    var cellClass = sheetData['cellClass'];

    // 受注番号リストを取得
    var receiveNoList = sheetData['receiveNoList'];

    // 発注番号リストを取得
    var orderNoList = sheetData['orderNoList'];

    var gridId = 'grid' + sheetNum;

    grid[sheetNum] = document.getElementById(gridId);

    // Handsontableでタグに表を埋め込む
    table[sheetNum] = new Handsontable(grid[sheetNum], {
      data: cellValue,
      fillHandle: false,
      undo: false,
      rowHeights: rowHeight,
      colWidths: columnWidth,
      cell: cellClass,
      cells: function (row, col, prop) {
        var cellProperties = {};
        cellProperties.renderer = firstRenderer;
        return cellProperties;
      },
      readOnly: true,
      mergeCells: merge,
      outsideClickDeselects: false,
    });
  }

  // Handsontableのアクティブ化のため、tableの左上を選択状態にする
  table[0].selectCell(0, 0);
  table[0].deselectCell();

  // HandsontableのtdタグCSS
  function firstRenderer(instance, td, row, col, prop, value, cellProperties) {
    if (col <= 2) {
      Handsontable.renderers.HtmlRenderer.apply(this, arguments);  //　プレビューの確認、取消設置用
    } else {
      Handsontable.renderers.TextRenderer.apply(this, arguments);
    }  
    var cellInfoData = cellData[row][col];
    var emphasis = cellInfoData['emphasis'];
    var border = cellInfoData['border'];
    var background = '#' + cellInfoData['backgroundColor'];
    var fontSize = (parseInt(cellInfoData['fontSize']) + 1) + 'px';
    var fontFamily = cellInfoData['fontFamily'];
    var fontColor = '#' + cellInfoData['fontColor'];  
    var verticalAlign = cellInfoData['verticalPosition'];
    var textAlign =  cellInfoData['horizontalPosition'];
    var boldStyle = emphasis['bold'] == true ? 'bold' : 'normal';
    var italicStyle = emphasis['italic'] == true ? 'italic' : 'normal';
    var borderColor = '#' + border['top']['color'] + ' #' + border['right']['color'] + ' #' + border['bottom']['color'] + ' #' + border['left']['color'];
    var borderStyle = border['top']['style'] + ' ' + border['right']['style'] + ' ' + border['bottom']['style'] + ' ' + border['left']['style'];
    var borderWidth = border['top']['width'] + ' ' + border['right']['width'] + ' ' + border['bottom']['width'] + ' ' + border['left']['width'];
    var clsRow = 'row' + row;
    var clsCol = 'col' + col;
    
    td.style.background = background;
    td.style.fontSize = fontSize;
    td.style.fontFamily = fontFamily;
    td.style.color = fontColor;
    td.style.verticalAlign = verticalAlign;
    td.style.textAlign = textAlign;
    td.style.fontWeight = boldStyle;
    td.style.fontStyle = italicStyle;
    td.style.borderColor = borderColor;
    td.style.borderStyle = borderStyle;
    td.style.borderWidth = borderWidth;
    td.style.padding = '2px';
    td.classList.add(clsRow);
    td.classList.add(clsCol);
  }
  
  /**
   * 指定した行および列を持つセルのクラス情報を取得する
   * @param {integer} row 指定行
   * @param {integer} col 指定列
   * 
   * @return {Array.object} elements クラス情報オブジェクトを持つ配列
   */
  function getElementsForRowAndColumn(row, col, searchList) {
    var filter = {
      row: row,
      col: col
    }
    var elements = searchList.filter(function(item) {
      for (var key in filter) {
        if (item[key] === undefined || item[key] != filter[key])
          return false;
      }
      return true;
    });
    return elements;
  }

  // スクロール時にチェックボックスの状態を保持するための関数（確定）
  $(document).on('change', '.checkbox_applicate', function() {
    var row = $(this).parents('td').attr('class').match(/\srow(\d+)\s/);
    var col = $(this).parents('td').attr('class').match(/\scol(\d+)\s/);
    var rowNo = row[1];
    var colNo = col[1];
    var status = $(this).prop('checked');
    if (status === true) {
      cellValue[rowNo][colNo] = cellValue[rowNo][colNo].replace('></div>', ' checked=\"checked\"></div>');
    } else {
      cellValue[rowNo][colNo] = cellValue[rowNo][colNo].replace(' checked=\"checked\"', '');
    }
  });

  // スクロール時にチェックボックスの状態を保持するための関数(取消)
  $(document).on('change', '.checkbox_cancel', function() {
    var row = $(this).parents('td').attr('class').match(/\srow(\d+)\s/);
    var col = $(this).parents('td').attr('class').match(/\scol(\d+)\s/);
    var rowNo = row[1];
    var colNo = col[1];
    var status = $(this).prop('checked');
    if (status === true) {
      cellValue[rowNo][colNo] = cellValue[rowNo][colNo].replace('></div>', ' checked=\"checked\"></div>');
    } else {
      cellValue[rowNo][colNo] = cellValue[rowNo][colNo].replace(' checked=\"checked\"', '');
    }
  });

  // 受注確定処理
  $(document).on('click', '.btn_confirm_receive', function() {
    var searchName = $(this).val();

    var confirmCol = 0;
    var buttonMatch = "name=\"" + searchName + "\"";
    var checkedMatch = "checked=\"checked\"";

    var ret = [];

    var beforeCompanyVal = '';

    var companyClassName = 'customerCompany';

    var searchList = cellClass;

    var check = true;

    var keyLength = Object.keys(cellValue).length;

    // セルに格納されたhtmlの文章から対象のチェックボックスを取得する
    // handsontableが画面外に表を作成しない(= 画面外の表にはhtmlが存在していない)ため、html要素からの取得は行っていない)
    for (var row = 1; row < keyLength; row++) {
      // 押下したボタンに対応する(エリアごと）かつチェック済のチェックボックスを取得
      if (cellValue[row][confirmCol].match(buttonMatch) && cellValue[row][confirmCol].match(checkedMatch)) {   
        // var companyCol = getColumnForRowAndClassName(row, companyClassName, searchList);

        // if (beforeCompanyVal === '') {
        //   beforeCompanyVal = cellValue[row][companyCol];
        // } else if (beforeCompanyVal !== cellValue[row][companyCol]) {
        //   check = false;
        // }

        // if (check === false) {
        //   alert('複数の顧客先が指定されています。');
        //   return false;
        // }

        ret.push(cellValue[row]);
      }
    }
    
    var target = ret.map(function(value) { // チェックボックスに対応する受注又は発注番号を取得し、カンマ区切りの文字列生成
      var ret = value[confirmCol].match(/value=\"(\d+)\"/);
      return ret[1];
    }).join(",");

    if (target) {
//      var text = '選択された受注を確定します。\nよろしいですか？';
//      var result = confirm(text);
//      if (result) {
        var sessionID = $('input[name="strSessionID"]').val();

        var baseUrl = "/so/decide/index.php";
        var estimateNo = 'estimateNo=';
        var revisionNo = '&revisionNo=';
        var numberKey = '&lngReceiveNo=';
        var actionUrl = baseUrl + "?" + estimateNo + $('input[name="estimateNo"]').val() + revisionNo + $('input[name="revisionNo"]').val() + numberKey +  target + "&strSessionID=" + sessionID;
        
        var windowName = 'window_confirm';
        var win = window.open(actionUrl, windowName, 'scrollbars=yes, width=1000, height=700, resizable=0 location=0');

        return false;

//      } else {
//        return false;
//      }
    } else {
      alert('受注を確定する明細行を選択してください。');
    }
  });

  // 発注確定処理
  $(document).on('click', '.btn_confirm_order', function() {
    var searchName = $(this).val();

    var confirmCol = 0;
    var buttonMatch = "name=\"" + searchName + "\"";
    var checkedMatch = "checked=\"checked\"";

    var ret = [];

    var beforeCompanyVal = '';
    var beforeMonetaryVal = '';

    var companyClassName = 'customerCompany';
    var monetaryClassName = 'monetary';

    var searchList = cellClass;

    var check = true;

    var keyLength = Object.keys(cellValue).length;

    // セルに格納されたhtmlの文章から対象のチェックボックスを取得する
    // handsontableが画面外に表を作成しない(= 画面外の表にはhtmlが存在していない)ため、html要素からの取得は行っていない)
    for (var row = 1; row < keyLength; row++) {
      // 押下したボタンに対応する(エリアごと）かつチェック済のチェックボックスを取得
      if (cellValue[row][confirmCol].match(buttonMatch) && cellValue[row][confirmCol].match(checkedMatch)) {   
        var companyCol = getColumnForRowAndClassName(row, companyClassName, searchList);
        var monetaryCol = getColumnForRowAndClassName(row, monetaryClassName, searchList);

        if (beforeCompanyVal === '') {
          beforeCompanyVal = cellValue[row][companyCol];
        } else if (beforeCompanyVal !== cellValue[row][companyCol]) {
          check = false;
        }

        if (beforeMonetaryVal === '') {
          beforeMonetaryVal = cellValue[row][monetaryCol];
        } else if (beforeMonetaryVal !== cellValue[row][monetaryCol]) {
          check = false;
        }

        if (check === false) {
          alert('複数の仕入先または通貨が指定されています。');
          return false;
        }

        ret.push(cellValue[row]);
      }
    }
    
    var target = ret.map(function(value) { // チェックボックスに対応する受注又は発注番号を取得し、カンマ区切りの文字列生成
      var ret = value[confirmCol].match(/value=\"(\d+)\"/);
      return ret[1];
    }).join(",");

    if (target) {
//      var text = '選択された発注を確定します。\nよろしいですか？';
//      var result = confirm(text);
//      if (result) {
        var sessionID = $('input[name="strSessionID"]').val();

        var baseUrl = "/po/regist/index.php";
        
        var estimateNo = 'estimateNo=';
        var revisionNo = '&revisionNo=';
        var numberKey = '&lngOrderNo=';

        var actionUrl = baseUrl + "?" + estimateNo + $('input[name="estimateNo"]').val() + revisionNo + $('input[name="revisionNo"]').val() + numberKey + target + "&strSessionID=" + sessionID;

        var windowName = 'window_confirm';
        var win = window.open(actionUrl, windowName, 'scrollbars=yes, width=1000, height=700, resizable=0 location=0');
        
        return false;

//      } else {
//        return false;
//      }
    } else {
      alert('発注を確定する明細行を選択してください。');
    }
  });

  // 発注取消処理
  $(document).on('click', '.btn_cancel_order', function() {
    var searchName = $(this).val();

    var cancelCol = 1;
    var buttonMatch = "name=\"" + searchName + "\"";
    var checkedMatch = "checked=\"checked\"";

    // セルに格納されたhtmlの文章から対象のチェックボックスを取得する
    // handsontableが画面外に表を作成しない(= 画面外の表にはhtmlが存在していない)ため、html要素からの取得は行っていない)
    var target = cellValue.filter(function(value) {
      // ボタンに対応するチェック済のチェックボックスを取得
      if (value[cancelCol].match(buttonMatch) && value[cancelCol].match(checkedMatch)) {
        return value;
      }
    }).map(function(value) { // チェックボックスに対応する受注又は発注番号を取得し、カンマ区切りの文字列生成
      var ret = value[cancelCol].match(/value=\"(\d+)\"/);
      return ret[1];
    });

    var count = target.length;

    if (count === 1) {
      var text = '選択された発注の確定を取り消します。\nよろしいですか？';
      var result = confirm(text);
      if (result) {
        var sessionID = $('input[name="strSessionID"]').val();

        var baseUrl = "/po/result/index3.php"
        var numberKey = 'lngOrderNo';

        var actionUrl = baseUrl + "?" + numberKey + "=" + target[0] + "&strSessionID=" + sessionID;
        var windowName = 'window_confirm';
        var win = window.open(actionUrl, windowName, 'scrollbars=yes, width=1000, height=700, resizable=0 location=0');
        
        return false;

      } else {
        return false;
      }
    } else if (count > 1) {
      alert('複数明細の選択はできません。');
    } else {
      alert('対象明細が選択されていません。');
    }
  });


  /**
   * 指定した行の中で特定のクラス名を持つ列を取得する
   * @param {integer} row 指定行
   * @param {integer} className クラス名
   * 
   * @return {Array.object} col 列番号
   */
  function getColumnForRowAndClassName(row, className, searchList) {
    var filter = {
      row: row,
    }
    
    var elements = searchList.filter(function(item) {
      for (var key in filter) {
        if (item[key] === undefined || item[key] != filter[key])
          return false;
      }
      return true;
    });
    
    for (var i = 0; i < elements.length; i++) {
      if (elements[i].className.includes(className)) {
        var col = elements[i].col;
        break;
      }
    }

    return col;      
  }

});
