// handsontableの操作関数

$(function () {

  // 定数の定義
  const chargeSubjectPattern = RegExp('^1224:');
  const partsSubjectPattern = RegExp('^401:');
  //  const importItemPattern = RegExp('^2:');
  const certificationPattern = RegExp('^1:');  // 証紙
  const tariffItemPattern = RegExp('^3:');
  const importOrTariffPattern = RegExp('^(2|3):');
  const certificationOrTariffPattern = RegExp('^(1|3):');
  const backSlashPattern = RegExp('\xA5', 'g');

  // データの取得
  var script = $('#script').attr('data-param');
  var result = JSON.parse(script);

  // htmlのJSON削除
  $('#script').remove();

  var grid = [];
  var table = [];

  // 表示用データの作成
  var sheetData = result[0];
  // 開始行列、終了行列の取得
  var startRow = sheetData['startRow'];
  var endRow = sheetData['endRow'];
  var startColumn = sheetData['startColumn'];
  var endColumn = sheetData['endColumn'];

  // セルのデータ取得
  var cellData = sheetData['cellData'];
  var lastSelectFrom;
  var lastSelectTo;
  var cellValue = [];
  var cellColorList = [];

  var readOnlyDetailRow = [];

  if (sheetData['readOnlyDetailRow']) {
    readOnlyDetailRow = sheetData['readOnlyDetailRow'];
  }

  var detailNoList = sheetData['detailNoList'];

  // セルの情報を配列に格納する
  for (var i = startRow; i <= endRow; i++) {
    var rowValue = [];
    var colorValue = [];
    for (var j = startColumn; j <= endColumn; j++) {
      rowValue.push(cellData[i][j]['value']);
      colorValue.push(cellData[i][j]['backgroundColor']);
    }
    cellValue.push(rowValue);
    cellColorList.push(colorValue);
  }

  // マージセルの取得
  var merge = sheetData['mergedCellsList'];
  // 行の高さ、列の幅を取得
  var rowHeight = sheetData['rowHeight'];
  var columnWidth = sheetData['columnWidth'].map(function (value) {
    if (value < 80) {
      return value + 10;
    } else {
      return value;
    }
  });


  var height = window.outerHeight;

  var gridWidth = 0;
  for (var i = 0; i < columnWidth.length; i++) {
    gridWidth += columnWidth[i];
  }

  var sideWidth = $('#sideMenu').width();

  var width = gridWidth + sideWidth + 30;

  // ウィンドウのリサイズ
  window.resizeTo(width, height);

  // セルのクラスを取得
  var cellClass = sheetData['cellClass'];

  var gridId = 'grid0';

  grid[0] = document.getElementById(gridId);

  var classList = cellClass;

  // プルダウンのリストを取得
  var dropdownDSCIList = sheetData['dropdownDSCI'];
  var dropdownCompanyList = sheetData['dropdownCompany'];
  var dropdownGUList = sheetData['dropdownGU'];
  var dropdownDevList = sheetData['dropdownDev'];

  var dropdownDivSub = {};
  var dropdownClsItm = {};
  var dropdownCompany = {};
  var dropdownGroup = [];
  var dropdownUser = {};
  var dropdownDevUser = [];

  // 売上分類（仕入科目）、売上区分（仕入部品）のドロップダウンリスト生成
  for (var i = 0; i < dropdownDSCIList.length; i++) {
    var area = dropdownDSCIList[i].areacode;
    var divisionSubject = dropdownDSCIList[i].divisionsubject;
    var classItem = dropdownDSCIList[i].classitem;

    // エリアコードのインデックスがない場合はインデックスを生成し、空値を配列に追加する
    if (isEmpty(dropdownDivSub[area])) {
      dropdownDivSub[area] = [''];
    }

    // プルダウンに重複がなければ追加
    if (dropdownDivSub[area].indexOf(divisionSubject) == -1) {
      dropdownDivSub[area].push(divisionSubject);
    }

    // エリアコードのインデックスがない場合はインデックスを生成し、空値を配列に追加する
    if (isEmpty(dropdownClsItm[area])) {
      dropdownClsItm[area] = {};
    }

    if (isEmpty(dropdownClsItm[area][divisionSubject])) {
      dropdownClsItm[area][divisionSubject] = [''];
    }

    // プルダウンに重複がなければ追加
    if (dropdownClsItm[area][divisionSubject].indexOf(classItem) == -1) {
      dropdownClsItm[area][divisionSubject].push(classItem);
    }
  }

  // 顧客先(仕入先)のドロップダウンリスト作成
  for (var i = 0; i < dropdownCompanyList.length; i++) {
    var areano = dropdownCompanyList[i].areano;
    var name = dropdownCompanyList[i].name;
    var customerCompany = dropdownCompanyList[i].customercompany;
    if (isEmpty(dropdownCompany[areano])) {
      dropdownCompany[areano] = {};
    }
    if (isEmpty(dropdownCompany[areano][name])) {
      dropdownCompany[areano][name] = [''];
    }
    if (dropdownCompany[areano][name].indexOf(customerCompany) == -1) {
      dropdownCompany[areano][name].push(customerCompany);
    }
  }

  // 営業部署のドロップダウンリスト作成
  for (var i = 0; i < dropdownGUList.length; i++) {
    var groupCode = dropdownGUList[i].groupcode;
    var userCode = dropdownGUList[i].usercode;

    // グループのドロップダウンが空の場合は空値を配列に追加する
    if (isEmpty(dropdownGroup)) {
      dropdownGroup.push('');
    }

    // ドロップダウンに重複がなければ追加
    if (dropdownGroup.indexOf(groupCode) == -1) {
      dropdownGroup.push(groupCode);
    }

    // グループのインデックスがない場合はインデックスを生成し、空値を配列に追加する
    if (isEmpty(dropdownUser[groupCode])) {
      dropdownUser[groupCode] = [''];
    }

    // プルダウンに重複がなければ追加
    if (dropdownUser[groupCode].indexOf(userCode) == -1) {
      dropdownUser[groupCode].push(userCode);
    }
  }

  // 開発担当者のドロップダウンリスト作成
  for (var i = 0; i < dropdownDevList.length; i++) {
    var devUserCode = dropdownDevList[i].usercode;

    // グループのドロップダウンが空の場合は空値を配列に追加する
    if (isEmpty(dropdownDevUser)) {
      dropdownDevUser.push('');
    }

    // ドロップダウンに重複がなければ追加
    if (dropdownDevUser.indexOf(devUserCode) == -1) {
      dropdownDevUser.push(devUserCode);
    }
  }

  // カレンダー入力のlocaleを日本に設定
  moment.locale('ja');



  // Handsontableでタグに表を埋め込む
  table[0] = new Handsontable(grid[0], {
    data: cellValue,
    disableVisualSelection: 'area',
    fillHandle: false,
    undo: false,
    beforePaste: function (data, coords) {

      for (var i = 0; i < coords.length; i++) {
        //alert(coords[i]['startRow'] + "," + coords[i]['endRow'] + "," + coords[i]['startCol'] + "," + coords[i]['endCol']);
        var startRow = coords[i]['startRow'];
        var startCol = coords[i]['startCol'];
        var endCol = coords[i]['endCol'];
        // ペースト対象行数はデータ数/列数
        var rowNum = data.length / (endCol - startCol + 1);
        var colNum = endCol - startCol + 1;
        //alert("startRow:" + startRow + ",startCol:" + startCol + ",endRow:" + (startRow+rowNum) + ",endCol:" + endCol);
        for (var j = 0; j < rowNum; j++) {
          for (var k = 0; k < colNum; k++) {
            //alert(data[j][k]);
            // ペースト対象データを対象列の型式に合わせて補正
            var correctedValue = correctValue(startRow + j, startCol + k, data[j][k]);
            data[j][k] = correctedValue;
            //alert(data[j][k]);
          }
        }
      }

    },
    beforeValidate: function (value, row, col, source) {
      if (source == 'edit' ||
        source == 'CopyPaste.paste' ||
        source == 'Autofill.fill') {
        var correctedValue = correctValue(row, col, value);
        return correctedValue;
      }
    },
    beforeChange: function (changes, source) {
      if (source == 'edit' ||
        source == 'CopyPaste.paste' ||
        source == 'Autofill.fill') {
        for (var i = 0; i < changes.length; i++) {
          var row = changes[i][0];
          var col = changes[i][1];
          var oldValue = changes[i][2];
          var newValue = changes[i][3];
          // 変更後データを対象列の型式に合わせて補正
          var correctedValue = correctValue(row, col, newValue);
          changes[i][3] = correctedValue;
          if (oldValue !== correctedValue) { // 変更があった場合
            cellValue[row][col] = correctedValue;
            cellData[row][col]['value'] = cellValue[row][col];
            onChangedValue(row, col, oldValue, newValue);
          }
        }
      }
    },
    afterSelectionEnd: function (rowFrom, colFrom, rowTo, colTo, leyerLevel) {
      changeBackColor(lastSelectFrom, lastSelectTo, false);
      changeBackColor(rowFrom, rowTo, true);
      lastSelectFrom = rowFrom;
      lastSelectTo = rowTo;
    },
    // height: 700,
    // width: '100vw',
    rowHeights: rowHeight,
    colWidths: columnWidth,
    cell: cellClass,
    cells: cells,
    mergeCells: merge,
    outsideClickDeselects: false,
  });

  // Handsontableのアクティブ化のため、tableの左上を選択状態にする
  table[0].selectCell(0, 0);
  table[0].deselectCell();


  // セルの設定
  function cells(row, col, prop) {
    var cellProperties = {};
    cellProperties = setCellProperties(row, col, prop);
    return cellProperties;
  }

  //---------------------------------(cellPropertiesの設定)-----------------------------------------

  /**
   * セルプロパティの設定
   * 
   * @param {integer} row 行番号
   * @param {integer} col 列番号
   * @param {string} prop
   * 
   * @return {object} cellProperties セルプロパティ
   */
  function setCellProperties(row, col, prop) {
    var cellProperties = {};

    var elements = getElementsForRowAndColumn(row, col, cellClass);

    // セルの書式（ドロップダウン、表示形式等）を設定する
    if (!isEmpty(elements)) {
      var className = elements[0].className;

      if (className === 'inchargegroupcode') { // 営業部署
        cellProperties.type = 'dropdown';
        cellProperties.source = dropdownGroup;

      } else if (className === 'inchargeusercode') { // 担当
        // 営業部署の取得
        for (var i = 0; i < cellClass.length; i++) {
          if (cellClass[i].className == 'inchargegroupcode') {
            var groupRow = cellClass[i].row;
            var groupCol = cellClass[i].col;
            break;
          }
        }
        var groupCode = cellValue[groupRow][groupCol];

        cellProperties.type = 'dropdown';
        cellProperties.source = dropdownUser[groupCode];

      } else if (className === 'developusercode') {
        cellProperties.type = 'dropdown';
        cellProperties.source = dropdownDevUser;

        // ヘッダ部、フッタ部の数量
      } else if (className === 'cartonquantity'     // カートン入り数
        || className === 'productionquantity'         // 償却数
        || className === 'member_quantity'            // 部材費対象数
        || className === 'depreciation_quantity'      // 償却対象数
        || className === 'manufacturing_quantity') {  // 数量
        cellProperties.type = 'numeric';
        cellProperties.numericFormat = {
          pattern: '#,##0',
        };

      } else if (className.includes('detail')) {    // 明細行である場合

        if (className.includes('divisionSubject')) { // 売上区分、仕入科目の処理
          var area = className.match(/area(\d+)/);
          cellProperties.type = 'dropdown';
          cellProperties.source = dropdownDivSub[Number(area[1])];

        } else if (className.includes('classItem')) { // 売上区分、仕入科目の処理          
          var area = className.match(/area(\d+)/);
          var divSubCol = getColumnForRowAndClassName(row, 'divisionSubject', cellClass);
          var value = cellValue[row][divSubCol];
          cellProperties.type = 'dropdown';
          cellProperties.source = dropdownClsItm[Number(area[1])][value];

        } else if (className.includes('customerCompany')) { // 顧客先（仕入先）の処理        
          var area = className.match(/area(\d+)/);
          var divSubCol = getColumnForRowAndClassName(row, 'divisionSubject', cellClass);
          var value = cellValue[row][divSubCol];
          cellProperties.type = 'dropdown';
          cellProperties.source = dropdownCompany[Number(area[1])][value];
          /*
          if (Number(area[1]) === 1 || Number(area[1]) === 2) { // 受注
            cellProperties.type = 'dropdown';
            cellProperties.source = dropdownCompany[2];

          } else if (Number(area[1]) === 3 || Number(area[1]) === 4) { // 発注
            cellProperties.type = 'dropdown';
            cellProperties.source = dropdownCompany[3];

          } else if (Number(area[1]) === 5) { 
            cellProperties.type = 'numeric';
            cellProperties.numericFormat = {
              pattern: '0.00%',
            };
          }
          */

        } else if (className.includes('monetaryDisplay')) { // 通貨
          cellProperties.type = 'dropdown';
          cellProperties.source = ['', 'JP', 'US', 'HK'];

        } else if (className.includes('payoff')) { // 償却          
          var area = className.match(/area(\d+)/);
          if (Number(area[1]) === 5) {
            cellProperties.type = 'numeric';
            cellProperties.numericFormat = {
              pattern: '0.00%',
            };

            // エリア5の償却は仕入科目、仕入部品で入力可否が変わる
            var divSubCol = getColumnForRowAndClassName(row, 'divisionSubject', cellClass);
            var div = cellValue[row][divSubCol];
            var itemCol = getColumnForRowAndClassName(row, 'classItem', cellClass);
            var item = cellValue[row][itemCol];

            if ((div == 401 && item == 1) || (div == 1224 && item == 3)) {
              cellProperties.readOnly = false;
            }
            else {
              cellProperties.readOnly = true;
            }
          }
          else {
            cellProperties.type = 'dropdown';
            cellProperties.source = ['', '○'];
          }
        } else if (className.includes('delivery')) { // 納期          
          cellProperties.type = 'date';
          cellProperties.datePickerConfig = {
            yearSuffix: '年',
            showMonthAfterYear: true,
            showDaysInNextAndPreviousMonths: true,
            i18n: {
              previousMonth: '前月',
              nextMonth: '次月',
              months: moment.localeData()._monthsShort,
              weekdays: moment.localeData()._weekdays,
              weekdaysShort: moment.localeData()._weekdaysShort
            },
          };
          cellProperties.dateFormat = 'YYYY/MM/DD';

        } else if (className.includes('price')) { // 単価
          cellProperties.type = 'numeric';
          cellProperties.numericFormat = {
            pattern: '#,##0.0000',
          };
        } else if (className.includes('quantity')) { // 数量
          cellProperties.type = 'numeric';
          cellProperties.numericFormat = {
            pattern: '#,##0',
          };
        } else if (className.includes('conversionRate')) { // 適用レート
          cellProperties.type = 'numeric';
          cellProperties.numericFormat = {
            pattern: '#,##0.0000',
          };
        }
      }
    }

    // 読み取り専用セルを設定する
    var readOnlyRow = readOnlyDetailRow.some(function (value) {
      return value = row;
    })

    if (readOnlyDetailRow.indexOf(row) > -1) {
      cellProperties.readOnly = true;
      cellData[row][col]['emphasis']['bold'] = false;
    } else {
      cellProperties.readOnly = cellData[row][col]['readOnly'];
      cellData[row][col]['emphasis']['bold'] = true;
    }

    // rendererをセットする（セルの罫線や背景色等の書式設定、入力形式の指定等）
    cellProperties.renderer = firstRenderer;


    return cellProperties;
  }



  //------------------------------------(Rendererの設定)-----------------------------------------

  /**
   * rendererの設定
   * 
   * @param {integer} row 行番号
   * @param {integer} col 列番号
   * 
   */
  function firstRenderer(instance, td, row, col, prop, value, cellProperties) {
    // 使用するrendererの指定（入力形式等）
    if (cellProperties.type === 'dropdown') { // ドロップダウン型
      Handsontable.renderers.DropdownRenderer.apply(this, arguments);
    } else if (cellProperties.type === 'date') { // 日付型
      Handsontable.renderers.DateRenderer.apply(this, arguments);
    } else if (cellProperties.type === 'numeric') { // 数値型
      Handsontable.renderers.NumericRenderer.apply(this, arguments);
    } else { // 特に強い拘りがなければテキスト(文字列）型
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
    var textAlign = cellInfoData['horizontalPosition'];
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

  // handsontableを操作するボタンの処理


  // 選択セルの取得
  function getSelectedCell() {
    var selected = table[0].getSelected();
    if (selected) {
      return selected[0];
    }
    return false;
  }

  //-----------------------------------------------(行操作)-----------------------------------------------------

  // 行追加
  $('.btnRowAdd').on('click', function () {
    var selectedRange = getSelectedCell();
    if (selectedRange) {
      var selectedRow = selectedRange[0];
      var selectedColumn = selectedRange[1];

      var checkList = cellClass;

      var selectedCell = getElementsForRowAndColumn(selectedRow, selectedColumn, checkList);

      if (isEmpty(selectedCell) === false) {
        if (selectedCell[0].className.includes('detail')) {
          var newData = [];
          var blankRow = [];

          // 空行配列の生成
          blankRow[0] = JSON.parse(JSON.stringify(cellData[selectedRow]));
          for (var column = startColumn; column <= endColumn; column++) {
            blankRow[0][column]['value'] = '';
            blankRow[0][column]['border']['bottom'] = cellData[selectedRow - 1][column]['border']['bottom'];
          }

          var newRow = startRow;
          // 空行をデータに挿入する
          for (var row = startRow; row <= endRow; row++) {
            if (row == selectedRow) {
              newData[newRow] = blankRow[0];
              newRow++;
            }
            newData[newRow] = JSON.parse(JSON.stringify(cellData[row]));
            newRow++;
          }

          // 元のセルの情報を書き換える
          cellData = newData;
          endRow += 1;

          cellValue = [];

          // セルの情報を配列に格納する
          for (var i = startRow; i <= endRow; i++) {
            var rowValue = [];
            for (var j = startColumn; j <= endColumn; j++) {
              rowValue.push(cellData[i][j]['value']);
            }
            cellValue.push(rowValue);
          }

          var selectedMerge = [];

          merge.forEach(function (value) {
            if (value.row == selectedRow) {
              var newValue = $.extend(true, {}, value);
              selectedMerge.push(newValue);
              value.row += 1;
            } else if (value.row > selectedRow) {
              value.row += 1;
            }
          });

          var selectedClassInfo = [];

          cellClass.forEach(function (value) {
            if (value.row == selectedRow) {
              var newClassInfo = $.extend(true, {}, value);
              selectedClassInfo.push(newClassInfo);
              value.row += 1;
            } else if (value.row > selectedRow) {
              value.row += 1;
            }
          });

          var newReadOnly = readOnlyDetailRow.map(function (value) {
            if (value >= selectedRow) {
              value = value + 1;
            }
            return value;
          });

          var copyDetailNoList = JSON.parse(JSON.stringify(detailNoList));


          detailNoList = copyDetailNoList.map(function (value) {
            if (value.row >= selectedRow) {
              value.row += 1;
            }
            return value;
          });

          insertDetailNo = { row: selectedRow, estimateDetailNo: null }
          detailNoList.push(insertDetailNo);

          merge = merge.concat(selectedMerge);
          cellClass = cellClass.concat(selectedClassInfo);
          readOnlyDetailRow = newReadOnly;

          rowHeight.splice(selectedRow, 0, rowHeight[selectedRow]);

          table[0].updateSettings({
            data: cellValue,
            rowHeights: rowHeight,
            cells: cells,
            mergeCells: merge,
            cell: cellClass
          });

        } else {
          alert('明細行以外の行追加はできません');
        }
      } else {
        alert('明細行以外の行追加はできません');
      }


      table[0].selectCell(selectedRow, selectedColumn);
    }
  });


  // 行削除
  $('.btnRowDelete').on('click', function () {
    var selectedRange = getSelectedCell();
    if (selectedRange) {
      var selectedRow = selectedRange[0];
      var selectedColumn = selectedRange[1];

      var checkList = cellClass;

      var selectedCell = getElementsForRowAndColumn(selectedRow, selectedColumn, checkList);

      if (isEmpty(selectedCell) === false) {
        if (selectedCell[0].className.includes('detail')) {
          var area = selectedCell[0].className.match(/area(\d+)/);
          var areaClassName = 'detail ' + area[0] + ' divisionSubject';

          var elements = getElementsForClassName(areaClassName, checkList);

          if (readOnlyDetailRow.includes(selectedRow)) {
            // 確定済の行は削除できないようにする
            alert('確定済の明細は削除できません。');

          } else if (elements.length > 1) {
            var newData = [];

            var newRow = startRow;

            // 行の数値を削除する
            for (var row = startRow; row <= endRow; row++) {
              if (row == selectedRow) {
                continue;
              }
              newData[newRow] = JSON.parse(JSON.stringify(cellData[row]));
              newRow++;
            }

            // 元のセルの情報を書き換える
            cellData = newData;
            endRow -= 1;

            cellValue = [];

            // セルの情報を配列に格納する
            for (var i = startRow; i <= endRow; i++) {
              var rowValue = [];
              for (var j = startColumn; j <= endColumn; j++) {
                rowValue.push(cellData[i][j]['value']);
              }
              cellValue.push(rowValue);
            }

            var newMerge = [];
            merge.forEach(function (value) {
              if (value.row == selectedRow) {
                return;
              } else if (value.row > selectedRow) {
                value.row -= 1;
              }
              var newValue = $.extend(true, {}, value);
              newMerge.push(newValue);
            });

            var newCellClass = [];
            cellClass.forEach(function (value) {
              if (value.row == selectedRow) {
                return;
              } else if (value.row > selectedRow) {
                value.row -= 1;
              }
              var newValue = $.extend(true, {}, value);
              newCellClass.push(newValue);
            });

            var newReadOnly = readOnlyDetailRow.map(function (value) {
              if (value > selectedRow) {
                value = value - 1;
              }
              return value;
            });

            var copyDetailNoList = JSON.parse(JSON.stringify(detailNoList));

            selectedKey = null;

            detailNoList = copyDetailNoList.map(function (value, key) {
              if (value.row > selectedRow) {
                value.row -= 1;
              } else if (value.row === selectedRow) {
                selectedKey = key;
              }
              return value;
            });

            detailNoList.splice(selectedKey, 1);

            merge = newMerge;
            cellClass = newCellClass;
            readOnlyDetailRow = newReadOnly;

            rowHeight.splice(selectedRow, 1);

            // 計算フラグの設定

            var calcFlag = {};

            // 再計算フラグの設定
            setCalcFlagForChangeSubtotal(Number(area[1]), calcFlag);
            setQuantityCalculateFlag(Number(area[1]), calcFlag);

            calculate(calcFlag);

            table[0].updateSettings({
              data: cellValue,
              rowHeights: rowHeight,
              cells: cells,
              mergeCells: merge,
              cell: cellClass,
            });
          } else {
            alert('該当エリアの明細行が1行以下のため行削除できません');
          }
        } else {
          alert('明細行以外の削除はできません');
        }
      } else {
        alert('明細行以外の削除はできません');
      }

      table[0].selectCell(selectedRange[0], selectedRange[1]);
    }
  });

  // 行移動(エリア内先頭に)
  $('.btnMoveTop').on('click', function () {
    var selectedRange = getSelectedCell();
    if (selectedRange) {
      var selectedRow = selectedRange[0];
      var selectedColumn = selectedRange[1];

      var checkList = cellClass;

      var selectedCell = getElementsForRowAndColumn(selectedRow, selectedColumn, checkList);

      if (isEmpty(selectedCell) === false) {
        if (selectedCell[0].className.includes('detail')) {
          var area = selectedCell[0].className.match(/area[0-9]+/);
          var areaClassName = 'detail ' + area + ' divisionSubject';

          var elements = getElementsForClassName(areaClassName, checkList);

          if (elements.length > 1) {
            var minRow = null;
            var maxRow = null;
            for (var i = 0; i < elements.length; i++) {
              var rowNumber = elements[i].row;
              if (!minRow || (minRow && minRow > rowNumber)) {
                minRow = rowNumber;
              } else if (!maxRow || (maxRow && maxRow < rowNumber)) {
                maxRow = rowNumber;
              }
            }
            var minRowCellValue = cellValue.splice(selectedRow, 1);
            cellValue.splice(minRow, 0, minRowCellValue[0]);

            // readOnlyを書き換える
            var newReadOnly = readOnlyDetailRow.map(function (value) {
              if (value == selectedRow) {
                return minRow;
              } else if (value < selectedRow && value >= minRow) {
                return value + 1;
              } else {
                return value;
              }
            });

            readOnlyDetailRow = newReadOnly;

            var copyDetailNoList = JSON.parse(JSON.stringify(detailNoList));

            detailNoList = copyDetailNoList.map(function (value) {
              if (value.row < selectedRow && value.row >= minRow) {
                value.row += 1;
              } else if (value.row === selectedRow) {
                value.row = minRow;
              }
              return value;
            });

            // 元のセルデータを更新する
            for (var i = minRow; i <= maxRow; i++) {
              for (var j = startColumn; j <= endColumn; j++) {
                cellData[i][j]['value'] = cellValue[i][j];
              }
            }

            table[0].updateSettings({
              data: cellValue,
              rowHeights: rowHeight,
              cells: cells,
              mergeCells: merge,
              cell: cellClass
            });
          }

        } else {
          alert('明細行以外の移動はできません');
        }
      } else {
        alert('明細行以外の移動はできません');
      }

      table[0].selectCell(minRow, selectedRange[1]);
    }
  });

  // 行移動(エリア内最後尾に)
  $('.btnMoveBottom').on('click', function () {
    var selectedRange = getSelectedCell();
    if (selectedRange) {
      var selectedRow = selectedRange[0];
      var selectedColumn = selectedRange[1];

      var checkList = cellClass;

      var selectedCell = getElementsForRowAndColumn(selectedRow, selectedColumn, checkList);

      if (isEmpty(selectedCell) === false) {
        if (selectedCell[0].className.includes('detail')) {
          var area = selectedCell[0].className.match(/area[0-9]+/);
          var areaClassName = 'detail ' + area + ' divisionSubject';

          var elements = getElementsForClassName(areaClassName, checkList);

          if (elements.length > 1) {
            var minRow = null;
            var maxRow = null;
            for (var i = 0; i < elements.length; i++) {
              var rowNumber = elements[i].row;
              if (!minRow || (minRow && minRow > rowNumber)) {
                minRow = rowNumber;
              } else if (!maxRow || (maxRow && maxRow < rowNumber)) {
                maxRow = rowNumber;
              }
            }
            var maxRowCellValue = cellValue.splice(selectedRow, 1);
            cellValue.splice(maxRow, 0, maxRowCellValue[0]);
            // readOnlyを書き換える
            var newReadOnly = readOnlyDetailRow.map(function (value) {
              if (value == selectedRow) {
                return maxRow;
              } else if (value > selectedRow && value <= maxRow) {
                return value - 1;
              } else {
                return value;
              }
            });

            readOnlyDetailRow = newReadOnly;

            var copyDetailNoList = JSON.parse(JSON.stringify(detailNoList));

            detailNoList = copyDetailNoList.map(function (value) {
              if (value.row > selectedRow && value.row <= maxRow) {
                value.row -= 1;
              } else if (value.row === selectedRow) {
                value.row = maxRow;
              }
              return value;
            });

            // 元のセルデータを更新する
            for (var i = minRow; i <= maxRow; i++) {
              for (var j = startColumn; j <= endColumn; j++) {
                cellData[i][j]['value'] = cellValue[i][j];
              }
            }

            table[0].updateSettings({
              data: cellValue,
              rowHeights: rowHeight,
              cells: cells,
              mergeCells: merge,
              cell: cellClass
            });
          }

        } else {
          alert('明細行以外の移動はできません');
        }

      } else {
        alert('明細行以外の移動はできません');
      }


      table[0].selectCell(maxRow, selectedRange[1]);
    }
  });

  // 行移動(一行上に)
  $('.btnMoveUpper').on('click', function () {
    var lock = screenLock();
    var selectedRange = getSelectedCell();
    if (selectedRange) {
      var selectedRow = selectedRange[0];
      var selectedColumn = selectedRange[1];

      var checkList = cellClass;

      var selectedCell = getElementsForRowAndColumn(selectedRow, selectedColumn, checkList);

      if (isEmpty(selectedCell) === false) {
        if (selectedCell[0].className.includes('detail')) {
          var area = selectedCell[0].className.match(/area[0-9]+/);
          var areaClassName = 'detail ' + area + ' divisionSubject';

          var elements = getElementsForClassName(areaClassName, checkList);

          if (elements.length > 1) {
            var minRow = null;
            for (var i = 0; i < elements.length; i++) {
              var rowNumber = elements[i].row;
              if (!minRow || (minRow && minRow > rowNumber)) {
                minRow = rowNumber;
              }
            }

            if (selectedRow != minRow) {
              var insertRow = selectedRow - 1;
              var movecellValue = cellValue.splice(selectedRow, 1);
              cellValue.splice(insertRow, 0, movecellValue[0]);

              // readOnlyを書き換える
              var newReadOnly = readOnlyDetailRow.map(function (value) {
                if (value == selectedRow) {
                  if (value == minRow) {
                    return minRow;
                  } else {
                    return value - 1;
                  }
                } else if (value == selectedRow -1) {
                  return selectedRow;
                } else {
                  return value;
                }
              });

              readOnlyDetailRow = newReadOnly;

              var copyDetailNoList = JSON.parse(JSON.stringify(detailNoList));

              detailNoList = copyDetailNoList.map(function (value) {
                if (value.row === selectedRow - 1) {
                  value.row = selectedRow;
                } else if (value.row === selectedRow) {
                  if (value.row != minRow) {
                    value.row -= 1;
                  }
                }
                return value;
              });

              // 元のセルデータを更新する
              for (var j = startColumn; j <= endColumn; j++) {
                cellData[selectedRow][j]['value'] = cellValue[selectedRow][j];
                cellData[insertRow][j]['value'] = cellValue[insertRow][j];
              }

              table[0].updateSettings({
                data: cellValue,
                rowHeights: rowHeight,
                cells: cells,
                mergeCells: merge,
                cell: cellClass
              });
            }
          }

        } else {
          alert('明細行以外の移動はできません');
        }
      } else {
        alert('明細行以外の移動はできません');
      }

      var unlock = screenUnlock();
      table[0].selectCell(insertRow, selectedRange[1]);
    }
  });



  // 行移動(一行下に)
  $('.btnMoveLower').on('click', function () {
    $('[class~="btn"]').prop('disabled', true);

    var selectedRange = getSelectedCell();

    if (selectedRange) {
      var selectedRow = selectedRange[0];
      var selectedColumn = selectedRange[1];

      var checkList = cellClass;

      var selectedCell = getElementsForRowAndColumn(selectedRow, selectedColumn, checkList);

      if (isEmpty(selectedCell) === false) {
        if (selectedCell[0].className.includes('detail')) {
          var area = selectedCell[0].className.match(/area[0-9]+/);
          var areaClassName = 'detail ' + area + ' divisionSubject';

          var elements = getElementsForClassName(areaClassName, checkList);

          if (elements.length > 1) {
            var maxRow = null;
            for (var i = 0; i < elements.length; i++) {
              var rowNumber = elements[i].row;
              if (!maxRow || (maxRow && maxRow < rowNumber)) {
                maxRow = rowNumber;
              }
            }

            if (selectedRow != maxRow) {
              var insertRow = selectedRow + 1;
              var movecellValue = cellValue.splice(selectedRow, 1);
              cellValue.splice(insertRow, 0, movecellValue[0]);

              // readOnlyを書き換える
              var newReadOnly = readOnlyDetailRow.map(function (value) {
                if (value == selectedRow) {
                  if (value == maxRow) {
                    return maxRow;
                  } else {
                    return selectedRow + 1;
                  }
                } else if (value == selectedRow + 1) {
                  return selectedRow;
                } else {
                  return value;
                }
              });

              readOnlyDetailRow = newReadOnly;
          

              var copyDetailNoList = JSON.parse(JSON.stringify(detailNoList));

              detailNoList = copyDetailNoList.map(function (value) {
                if (value.row === selectedRow + 1) {
                  value.row = selectedRow;
                } else if (value.row === selectedRow) {
                  if (value.row != maxRow) {
                    value.row += 1;
                  }
                }
                return value;
              });

              // 元のセルデータを更新する
              for (var j = startColumn; j <= endColumn; j++) {
                cellData[selectedRow][j]['value'] = cellValue[selectedRow][j];
                cellData[insertRow][j]['value'] = cellValue[insertRow][j];
              }

              table[0].updateSettings({
                data: cellValue,
                rowHeights: rowHeight,
                mergeCells: merge,
                cell: cellClass
              });
            }
          }

        } else {
          alert('明細行以外の移動はできません');
        }
      } else {
        alert('明細行以外の移動はできません');
      }


      table[0].selectCell(insertRow, selectedRange[1]);
    }

    return;
  });


  //---------------------------------------(セルの再計算処理)------------------------------------------

  /**
   * セルの入力値が変更された時の処理を行う
   * @param {integer} row 変更セルの行
   * @param {integer} col 変更セルの列
   * @param {integer} oldValue 変更前の値
   * @param {integer} newValue 変更後の値
   * 
   */
  function onChangedValue(row, col, oldValue, newValue) {
    var checkList = cellClass;

    var elements = getElementsForRowAndColumn(row, col, checkList);

    var calcFlag = {};// subtotal:金額合計、member:部材費、substitutePQ:償却、productionQuantity:数量（償却数）、importOrTariff:関税、輸入費

    var className = elements[0].className;
    if (className === 'inchargegroupcode') { // 営業部署
      // 担当のセルの取得
      for (var i = 0; i < checkList.length; i++) {
        if (checkList[i].className == 'inchargeusercode') {
          var userRow = cellClass[i].row;
          var userCol = cellClass[i].col;
          break;
        }
      }
      // 担当を空欄にする
      assignValueForGlobal(userRow, userCol, '');

    } else if (className == 'retailprice') {  //上代
      calcFlag.subtotal = true;
      calcFlag.member = true;
    } else if (className.includes('detail')) { // 明細行

      // セルのクラス情報取得
      var cellElement = getElementsForRowAndColumn(row, col, checkList);

      // エリアコードの取得
      var areaCode = cellElement[0].className.match(/area([0-9]+)/);


      if (className.includes('divisionSubject')) { // 売上分類（又は仕入科目）
        var clsItmCol = getColumnForRowAndClassName(row, 'classItem', checkList);

        // 売上区分（又は仕入部品）を空欄にする
        assignValueForGlobal(row, clsItmCol, '');

        var cusComCol = getColumnForRowAndClassName(row, 'customerCompany', checkList);
        assignValueForGlobal(row, cusComCol, '');  // 顧客先（仕入先）をクリア

        if (isNumber(cellValue[row][cusComCol]) === true) { // 数値入力されている場合

          var priceCol = getColumnForRowAndClassName(row, 'price', checkList);
          assignValueForGlobal(row, priceCol, '');  // 単価をクリア
        }

        if (Number(areaCode[1]) === 1) {
          calcFlag.productionQuantity = true;
        }

        if (Number(areaCode[1]) === 4) { // || Number(areaCode[1]) === 5) {
          calcFlag.substitutePQ = true;   // 償却再計算
        }

        if (Number(areaCode[1]) === 5) {
          var cusPayoffCol = getColumnForRowAndClassName(row, 'payoff', checkList);
          assignValueForGlobal(row, cusPayoffCol, 0);  // 償却をクリアし入力不可に
          cellData[row][cusPayoffCol]['readOnly'] = true;
        }
        calcFlag.subtotal = true;

      } else if (className.includes('classItem')) { // 売上区分（仕入部品)
        var cusPayoffCol = getColumnForRowAndClassName(row, 'payoff', checkList);

        //      if (isNumber(cellValue[row][cusComCol]) === true) { // 償却に数値入力されている場合(パーセントの場合)
        var divSubCol = getColumnForRowAndClassName(row, 'divisionSubject', checkList);
        var divSub = cellValue[row][divSubCol];
        if (divSub.match(chargeSubjectPattern)) { // 仕入科目がチャージの場合
          if (oldValue.match(tariffItemPattern) && !newValue.match(tariffItemPattern)) { // 変更前の値が関税で変更後の値がそれ以外の値の場合

            assignValueForGlobal(row, cusPayoffCol, '');  // 償却をクリアし入力不可に

            calcFlag.subtotal = true;
            cellData[row][cusPayoffCol]['readOnly'] = true;
          } else if (newValue.match(tariffItemPattern)) {
            calcFlag.importOrTariff = true // 輸入費用、関税、証紙の再計算フラグ
            cellData[row][cusPayoffCol]['readOnly'] = false;
          }
        }
        else if (divSub.match(partsSubjectPattern)) { // 仕入科目が材料パーツ仕入高の場合
          if (oldValue.match(certificationPattern) && !newValue.match(certificationPattern)) { // 変更前の値が証紙で変更後の値がそれ以外の値の場合

            assignValueForGlobal(row, cusPayoffCol, '');  // 償却をクリアし入力不可に
            calcFlag.subtotal = true;
            cellData[row][cusPayoffCol]['readOnly'] = true;

            cusComCol = getColumnForRowAndClassName(row, 'customerCompany', checkList);
            assignValueForGlobal(row, cusComCol, '');  // 顧客先（仕入先）をクリア

          } else if (newValue.match(certificationPattern)) {
            calcFlag.importOrTariff = true // 輸入費用、関税、証紙の再計算フラグ
            cellData[row][cusPayoffCol]['readOnly'] = false;
          }
        }
        //      }

        if (oldValue == '' || newValue == '') {
          calcFlag.subtotal = true;
        }

        if (Number(areaCode[1]) === 1) {
          calcFlag.productionQuantity = true;
        }

        if (Number(areaCode[1]) === 4 || Number(areaCode[1]) === 5) {
          calcFlag.substitutePQ = true;
        }

      } else if (className.includes('quantity')) {
        calcFlag.subtotal = true;

        setQuantityCalculateFlag(Number(areaCode[1]), calcFlag, 'quantity');

      } else if (className.includes('price')) {
        //        if (Number(areaCode[1]) === 5) {
        //          var cusComCol = getColumnForRowAndClassName(row, 'customerCompany', checkList); // 仕入先列を取得
        //          assignValueForGlobal(row, cusComCol, '');  // 仕入先列の値をクリア
        //        }

        calcFlag.subtotal = true;

      } else if (className.includes('conversionRate')) {
        calcFlag.subtotal = true;
      } else if (className.includes('payoff')) { // 償却
        var cusPayoffCol = getColumnForRowAndClassName(row, 'payoff', checkList);
        //assignValueForGlobal(row, cusComCol, newValue / 100 );
        if (Number(areaCode[1]) === 3) {
          calcFlag.depreciation = true;
        } else if (Number(areaCode[1]) === 4) { // || Number(areaCode[1]) === 5) {
          calcFlag.member = true;
          calcFlag.depreciation = true;
        }
        else if (Number(areaCode[1]) === 5) {
          calcFlag.member = true;
          calcFlag.importOrTariff = true;
          calcFlag.subtotal = true;
        }
      }

      // 小計再計算時の処理
      if (calcFlag.subtotal === true) {
        setCalcFlagForChangeSubtotal(Number(areaCode[1]), calcFlag)
      }

      calculate(calcFlag, row);
    }
    return;
  };

  // 小計が変わった時の再計算フラグの設定
  function setCalcFlagForChangeSubtotal(areaCode, calcFlag) {
    if (areaCode === 1) {
      calcFlag.area1TotalPrice = true;
    } else if (areaCode === 2) {
      calcFlag.area2TotalPrice = true;
    } else if (areaCode === 3) {
      calcFlag.area3TotalCost = true;
      calcFlag.area3NotDepreciationCost = true;
      calcFlag.depreciation = true;
    } else if (areaCode === 4) { // || areaCode === 5) {
      calcFlag.member = true;
      calcFlag.depreciation = true;
    }
    else if (areaCode === 5) {
      calcFlag.member = true;
    }
    calcFlag.importOrTariff = true;
    return;
  }

  // セルの計算関数
  function calculate(calcFlag, row = null) {

    // 再計算フラグの初期化
    var calcManufacturing = false;
    var calcProductProfit = false;
    var calcFixedCostProfit = false;
    var calcSalesAmount = false;
    var calcProfit = false;
    var calcOperatingProfit = false;
    var calcMemberUnit = false;
    var calcDepreciationUnit = false;
    var calcManufacturingUnit = false;

    if (!row) {
      // 行入力がない場合は行関連の処理を行わないようにする
      calcFlag.subtotal = false;
      calcFlag.substitutePQ = false;
    }

    // ヘッダ部
    if (calcFlag.productionQuantity === true) { // 償却数
      calProductionQuantity();
      calcFlag.member = true;
      calcFlag.depreciation = true;
      calcFlag.importOrTariff = true;
    }

    // 明細行
    if (calcFlag.subtotal === true) { // 小計　※パーセント入力の場合はパーセント入力の再計算関数の中で呼び出すので不要
      calculateSubtotal(row);
    }

    if (calcFlag.area1TotalQuantity === true) { // 製品売上合計（数量）
      calculateArea1TotalQuantity();
    }

    if (calcFlag.area1TotalPrice === true) { // 製品売上合計（金額）
      calculateArea1TotalPrice();
      calcProductProfit = true;
      calcSalesAmount = true;
    }

    if (calcFlag.area2TotalQuantity === true) { // 固定費売上合計（数量）
      calculateArea2TotalQuantity();
    }

    if (calcFlag.area2TotalPrice === true) { // 固定費売上合計（金額）
      calculateArea2TotalPrice();
      calcFixedCostProfit = true;
      calcSalesAmount = true;
    }

    if (calcFlag.area3TotalCost === true) {
      calculateArea3TotalCost();
    }

    if (calcFlag.area3NotDepreciationCost === true) {
      calculateArea3NotDepreciationCost();
      calcFixedCostProfit = true;
    }

    if (calcFlag.substitutePQ === true) {
      substitutePQForDetailQuantity(row);
      calcFlag.importOrTariff = true;
      calcFlag.member = true;
      calcManufacturing = true;
    }

    if (calcFlag.importOrTariff === true) { // 輸入費用及び関税の単価、小計
      calculateImportOrTariffRows();
    }

    if (calcFlag.member === true) { // 部材費
      calculateMemberCost();
      calcMemberUnit = true;
      calcManufacturing = true;
    }

    if (calcFlag.depreciation === true) { // 償却費
      calculateDepreciationCost();
      calcDepreciationUnit = true;
      calcManufacturing = true;
    }

    if (calcManufacturing === true) { // 製造費用
      calculateManufacturingCost();
      calcManufacturingUnit = true;
      calcProductProfit = true;
    }

    if (calcMemberUnit === true) { // pcs部材費用
      calculateMemberUnitCost();
    }

    if (calcDepreciationUnit === true) { // pcs償却費用
      calculateDepreciationUnitCost();
    }

    if (calcManufacturingUnit === true) { // pcsコスト
      calculateManufacturingUnitCost();
    }

    if (calcProductProfit === true) { // 製品利益、製品利益率
      calculateProductProfit();
      calcProfit = true;
    }

    if (calcFixedCostProfit === true) { // 固定費利益、固定費利益率
      calculateFixedCostProfit();
      calcProfit = true;
    }

    if (calcSalesAmount === true) {
      calculateSalesAmount();
      calcOperatingProfit = true;
    }

    if (calcProfit === true) {
      calculateProfit();
      calcOperatingProfit = true;
    }

    if (calcOperatingProfit === true) {
      calculateOperatingProfit();
    }

  }

  // 償却数の再計算
  function calProductionQuantity() {
    var areaClass = 'area1';
    // var divisionClass = 'detail ' + areaClass + ' divisionSubject';
    var classClass = 'detail ' + areaClass + ' classItem';
    var quantityClass = 'detail ' + areaClass + ' quantity';

    var checkList = cellClass;

    var classElements = getElementsForClassName(classClass, checkList);

    var quantityElements = getElementsForClassName(quantityClass, checkList);

    var productionQuantity = 0;

    for (var i = 0; i < classElements.length; i++) {
      var classRow = classElements[i].row;
      var classCol = classElements[i].col;
      var mainProductName = '1:本荷';
      if (cellValue[classRow][classCol] == mainProductName) {
        var quantityRow = quantityElements[i].row;
        var quantityCol = quantityElements[i].col;
        var quantity = Number(cellValue[quantityRow][quantityCol]);
        productionQuantity += quantity;
      }
    }

    var PQclass = 'productionquantity';
    var PQCell = getElementsForClassName(PQclass, checkList);

    // 償却数の入力セル位置を取得
    var PQRow = PQCell[0].row;
    var PQCol = PQCell[0].col;

    var beforePQ = cellValue[PQRow][PQCol];

    // グローバル変数の操作
    assignValueForGlobal(PQRow, PQCol, productionQuantity);

    setQuantityForPartsAndOthers(beforePQ, productionQuantity);

    return;
  }

  // pcs部材費用
  function calculateMemberUnitCost() {
    var checkList = cellClass;

    var costCell = getElementsForClassName('membercost', checkList);
    var unitCell = getElementsForClassName('member_unit_cost', checkList);
    var quantityCell = getElementsForClassName('member_quantity', checkList);

    calculateUnitCost(costCell, unitCell, quantityCell)

    return;
  }

  // pcs償却費用
  function calculateDepreciationUnitCost() {
    var checkList = cellClass;

    var costCell = getElementsForClassName('depreciation_cost', checkList);
    var unitCell = getElementsForClassName('depreciation_unit_cost', checkList);
    var quantityCell = getElementsForClassName('depreciation_quantity', checkList);

    calculateUnitCost(costCell, unitCell, quantityCell)

    return;
  }

  // pcsコスト
  function calculateManufacturingUnitCost() {
    var checkList = cellClass;

    var costCell = getElementsForClassName('manufacturingcost', checkList);
    var unitCell = getElementsForClassName('manufacturing_unit_cost', checkList);
    var quantityCell = getElementsForClassName('manufacturing_quantity', checkList);

    calculateUnitCost(costCell, unitCell, quantityCell)

    return;
  }

  // フッタ部の単価再計算
  function calculateUnitCost(costCell, unitCell, quantityCell) {
    var costRow = costCell[0].row;
    var costCol = costCell[0].col;
    var unitRow = unitCell[0].row;
    var unitCol = unitCell[0].col;
    var quantityRow = quantityCell[0].row;
    var quantityCol = quantityCell[0].col;

    var cost = Number(cellValue[costRow][costCol].replace('\xA5', '').split(',').join(''));
    var quantity = cellValue[quantityRow][quantityCol];

    if (quantity === '' || quantity === 0) {
      var unit = '';
    } else {
      var unit = cost / quantity;
      unit = '\xA5' + numberFormat(unit, 2);
    }

    // グローバル変数の操作
    assignValueForGlobal(unitRow, unitCol, unit);

    return;
  }

  // 部材費及びその他費用の数量設定
  function setQuantityForPartsAndOthers(beforePQ, productionQuantity = null) {
    var checkList = cellClass;

    if (productionQuantity === null) {
      // 償却数が与えられていない場合はセル位置から償却数を取得する

      var PQclass = 'productionquantity';
      var PQCell = getElementsForClassName(PQclass, checkList);

      // 償却数の入力セル位置を取得
      var PQRow = PQCell[0].row;
      var PQCol = PQCell[0].col;

      var productionQuantity = cellValue[PQRow][PQCol];
    }

    // エリア4の処理
    var areaClass = 'area4';
    var subjectClass = 'detail ' + areaClass + ' divisionSubject';
    var itemClass = 'detail ' + areaClass + ' classItem';
    var quantityClass = 'detail ' + areaClass + ' quantity';

    var subjectElements = getElementsForClassName(subjectClass, checkList);
    var itemElements = getElementsForClassName(itemClass, checkList);
    var quantityElements = getElementsForClassName(quantityClass, checkList);

    // 数量代入処理
    for (var i = 0; i < subjectElements.length; i++) {
      var row = subjectElements[i].row;
      var subCol = subjectElements[i].col;
      var iteCol = itemElements[i].col;
      if (cellValue[row][subCol] && cellValue[row][iteCol]) {
        // 仕入科目と仕入部品が両方入力されている場合は数量に償却数をセットする
        var quaCol = quantityElements[i].col;

        if (beforePQ == cellValue[row][quaCol]) { // 数量が変更前の償却数と一致する場合は代入
          //          cellValue[row][quaCol] = productionQuantity;
          calculateSubtotal(row);
        }
      }
    }

    // エリア5の処理
    var areaClass = 'area5';
    var subjectClass = 'detail ' + areaClass + ' divisionSubject';
    var itemClass = 'detail ' + areaClass + ' classItem';
    var quantityClass = 'detail ' + areaClass + ' quantity';

    var checkList = cellClass;

    var subjectElements = getElementsForClassName(subjectClass, checkList);
    var itemElements = getElementsForClassName(itemClass, checkList);
    var quantityElements = getElementsForClassName(quantityClass, checkList);

    // 数量代入処理
    for (var i = 0; i < subjectElements.length; i++) {
      var row = subjectElements[i].row;
      var subCol = subjectElements[i].col;
      var iteCol = itemElements[i].col;
      if (cellValue[row][subCol] && cellValue[row][iteCol]) {
        // 仕入科目と仕入部品が両方入力されている場合は数量に償却数をセットする
        var quaCol = quantityElements[i].col;
        if (beforePQ == cellValue[row][quaCol]) { // 数量が変更前の償却数と一致する場合は代入
          //          cellValue[row][quaCol] = productionQuantity;
          calculateSubtotal(row);
        }
      }
    }

    // フッタ部の処理
    var MEQcell = getElementsForClassName('member_quantity', checkList);
    cellValue[MEQcell[0].row][MEQcell[0].col] = productionQuantity;

    var DQcell = getElementsForClassName('depreciation_quantity', checkList);
    cellValue[DQcell[0].row][DQcell[0].col] = productionQuantity;

    var MAQcell = getElementsForClassName('manufacturing_quantity', checkList);
    cellValue[MAQcell[0].row][MAQcell[0].col] = productionQuantity;

    return;
  }

  // 明細行の数量に償却数を代入する
  function substitutePQForDetailQuantity(row) {
    var checkList = cellClass;

    var subCol = getColumnForRowAndClassName(row, 'divisionSubject', checkList);
    var iteCol = getColumnForRowAndClassName(row, 'classItem', checkList);
    var quaCol = getColumnForRowAndClassName(row, 'quantity', checkList);

    if (cellValue[row][subCol] && cellValue[row][iteCol]) {
      // 仕入科目と仕入部品が両方入力されている場合は数量に償却数をセットする
      var PQElement = getElementsForClassName('productionquantity', checkList);
      var PQRow = PQElement[0].row;
      var PQCol = PQElement[0].col;

      var productionQuantity = cellValue[PQRow][PQCol];

      //      cellValue[row][quaCol] = productionQuantity;
    } else {
      cellValue[row][quaCol] = '';
    }
    calculateSubtotal(row);
  }

  // 対象エリアの合計の処理
  function setQuantityCalculateFlag(areaCode, calcFlag) {
    if (areaCode === 1) {
      // 製品売上合計（数量）の再計算
      calcFlag.area1TotalQuantity = true;

      // 償却数の再計算
      calcFlag.productionQuantity = true;

    } else if (areaCode === 2) {
      // 固定費売上合計（数量）の再計算
      calcFlag.area2TotalQuantity = true
    }
    return;
  }

  /**
  * 明細行の小計（または計画原価）の再計算を行う
  * 
  */
  function calculateSubtotal(row) {
    var checkList = cellClass;
    var divSubCol = getColumnForRowAndClassName(row, 'divisionSubject', checkList);
    var clsItemCol = getColumnForRowAndClassName(row, 'classItem', checkList);
    var divSub = cellValue[row][divSubCol];
    var clsItm = cellValue[row][clsItemCol];

    var subtotalCol = getColumnForRowAndClassName(row, 'subtotal', checkList);

    if (divSub !== '' && clsItm !== '') {
      var quantityCol = getColumnForRowAndClassName(row, 'quantity', checkList);
      var priceCol = getColumnForRowAndClassName(row, 'price', checkList);
      var rateCol = getColumnForRowAndClassName(row, 'conversionRate', checkList);
      var percentCol = getColumnForRowAndClassName(row, 'payoff', checkList);
      var quantity = Number(cellValue[row][quantityCol]);
      var price = Number(cellValue[row][priceCol]);
      var rate = Number(cellValue[row][rateCol]);
      var subtotal = quantity * price * rate;
      subtotal = '\xA5' + numberFormat(subtotal, 0);
    } else {
      var subtotal = '';
    }

    // 値の代入
    assignValueForGlobal(row, subtotalCol, subtotal);

    return subtotal;
  }

  /**
  * ％入力のある明細行の小計（または計画原価）の再計算を行う
  * 
  */
  function calculateSubtotalByPercentage(row, rateBase) {
    var checkList = cellClass;
    var divSubCol = getColumnForRowAndClassName(row, 'divisionSubject', checkList);
    var clsItemCol = getColumnForRowAndClassName(row, 'classItem', checkList);
    var divSub = cellValue[row][divSubCol];
    var clsItm = cellValue[row][clsItemCol];

    var subtotalCol = getColumnForRowAndClassName(row, 'subtotal', checkList);

    if (divSub !== '' && clsItm !== '') {
      var quantityCol = getColumnForRowAndClassName(row, 'quantity', checkList);
      var priceCol = getColumnForRowAndClassName(row, 'price', checkList);
      var rateCol = getColumnForRowAndClassName(row, 'conversionRate', checkList);
      var percentCol = getColumnForRowAndClassName(row, 'payoff', checkList);
      var quantity = Number(cellValue[row][quantityCol]);
      var price = Number(cellValue[row][priceCol]);
      var rate = Number(cellValue[row][rateCol]);
      var percent = (isNaN(cellValue[row][percentCol]) === false) ? Number(cellValue[row][percentCol]) / 100 : 0;
      if (percent != 0 && rateBase != 0) {
        var subtotal = rateBase * percent * quantity * rate;
      }
      else {
        var subtotal = quantity * price * rate;
      }
      subtotal = '\xA5' + numberFormat(subtotal, 0);
    } else {
      var subtotal = '';
    }

    // 値の代入
    assignValueForGlobal(row, subtotalCol, subtotal);

    return subtotal;
  }


  /**
  * 製品売上合計（数量）の再計算を行う
  * 
  */
  function calculateArea1TotalQuantity() {
    var checkList = cellClass;
    var subtotalClassName = 'detail area1 quantity';
    var subtotalCells = getElementsForClassName(subtotalClassName, checkList);
    var row = null;
    var col = null;
    var totalQuantity = 0;
    for (var i = 0; i < subtotalCells.length; i++) {
      row = subtotalCells[i].row;
      col = subtotalCells[i].col;
      totalQuantity += Number(cellValue[row][col]);
    }

    var totalQuantityClassName = 'receive_p_totalquantity';

    // カンマ区切り文字列に変換
    totalQuantity = numberFormat(totalQuantity, 0);

    assignValueForClassNameCell(totalQuantityClassName, totalQuantity);
  }


  /**
  * 製品売上合計（金額）の再計算を行う
  * 
  */
  function calculateArea1TotalPrice() {
    var checkList = cellClass;
    var subtotalClassName = 'detail area1 subtotal';
    var subtotalCells = getElementsForClassName(subtotalClassName, checkList);
    var row = null;
    var col = null;
    var subtotal = null;
    var totalPrice = 0;
    for (var i = 0; i < subtotalCells.length; i++) {
      row = subtotalCells[i].row;
      col = subtotalCells[i].col;
      // 小計列の値を数値に変換する
      subtotal = Number(cellValue[row][col].replace('\xA5', '').split(',').join(''));
      totalPrice += subtotal;
    }

    var totalPriceClassName = 'receive_p_totalprice'; // 製品売上合計（金額）のセル名称
    var productTotalPriceClassName = 'product_totalprice'; // 製品売上高のセル名称

    // 計算結果をカンマ区切り、円マーク付きの書式に変換
    totalPrice = totalPrice != 0 ? '\xA5' + numberFormat(totalPrice, 0) : '';

    // 値の代入
    assignValueForClassNameCell(totalPriceClassName, totalPrice);
    assignValueForClassNameCell(productTotalPriceClassName, totalPrice);
  }

  /**
  * 固定費売上合計（数量）の再計算を行う
  * 
  */
  function calculateArea2TotalQuantity() {
    var checkList = cellClass;
    var subtotalClassName = 'detail area2 quantity';
    var subtotalCells = getElementsForClassName(subtotalClassName, checkList);
    var row = null;
    var col = null;
    var totalQuantity = 0;
    for (var i = 0; i < subtotalCells.length; i++) {
      row = subtotalCells[i].row;
      col = subtotalCells[i].col;
      totalQuantity += Number(cellValue[row][col]);
    }

    var totalQuantityClassName = 'receive_f_totalquantity';

    // カンマ区切り文字列に変換
    totalQuantity = numberFormat(totalQuantity, 0);

    assignValueForClassNameCell(totalQuantityClassName, totalQuantity);
  }

  /**
   * 固定費売上合計（金額）の再計算を行う
   * 
   */
  function calculateArea2TotalPrice() {
    var checkList = cellClass;
    var subtotalClassName = 'detail area2 subtotal';
    var subtotalCells = getElementsForClassName(subtotalClassName, checkList);
    var row = null;
    var col = null;
    var subtotal = null;
    var totalPrice = 0;
    for (var i = 0; i < subtotalCells.length; i++) {
      row = subtotalCells[i].row;
      col = subtotalCells[i].col;
      subtotal = Number(cellValue[row][col].replace('\xA5', '').split(',').join(''));
      totalPrice += subtotal;
    }

    var totalPriceClassName = 'receive_f_totalprice'; // 固定費売上合計（金額）のセル名称
    var fixedTotalPriceClassName = 'fixedcost_totalprice'; // 固定費売上高のセル名称

    totalPrice = totalPrice != 0 ? '\xA5' + numberFormat(totalPrice, 0) : '';

    // 値の代入
    assignValueForClassNameCell(totalPriceClassName, totalPrice);
    assignValueForClassNameCell(fixedTotalPriceClassName, totalPrice);
  }

  /**
   * 固定費小計の再計算を行う
   * 
   */
  function calculateArea3TotalCost() {
    var checkList = cellClass;
    var subtotalClassName = 'detail area3 subtotal';
    var subtotalCells = getElementsForClassName(subtotalClassName, checkList);
    var row = null;
    var col = null;
    var subtotal = null;
    var totalCost = 0;
    for (var i = 0; i < subtotalCells.length; i++) {
      row = subtotalCells[i].row;
      col = subtotalCells[i].col;
      subtotal = Number(cellValue[row][col].replace('\xA5', '').split(',').join(''));
      totalCost += subtotal;
    }

    var totalCostClassName = 'order_f_fixedcost';

    totalCost = totalCost != 0 ? '\xA5' + numberFormat(totalCost, 0) : '';

    assignValueForClassNameCell(totalCostClassName, totalCost);
  }

  /**
   * 償却対象外小計の再計算を行う
   * 
   */
  function calculateArea3NotDepreciationCost() {
    var checkList = cellClass;
    var subtotalClassName = 'detail area3 subtotal';
    var subtotalCells = getElementsForClassName(subtotalClassName, checkList);

    var payoffClassName = 'detail area3 payoff';
    var payoffCells = getElementsForClassName(payoffClassName, checkList);


    var row = null;
    var payCol = null;
    var subCol = null;
    var subtotal = null;

    var totalCost = 0;

    for (var i = 0; i < subtotalCells.length; i++) {

      row = subtotalCells[i].row;
      payCol = payoffCells[i].col;
      subCol = subtotalCells[i].col;
      if (cellValue[row][payCol] != '○') {
        subtotal = Number(cellValue[row][subCol].replace('\xA5', '').split(',').join(''));
        totalCost += subtotal;
      }
    }

    var totalCostClassName = 'order_f_cost_not_depreciation'; // 償却対象外小計のセル名称
    var costNotDepClassName = 'cost_not_depreciation'; // 償却対象外固定費のセル名称

    totalCost = totalCost != 0 ? '\xA5' + numberFormat(totalCost, 0) : '';

    // 値の代入
    assignValueForClassNameCell(totalCostClassName, totalCost);
    assignValueForClassNameCell(costNotDepClassName, totalCost);
  }

  /**
   * 輸入費用及び関税、証紙の単価、小計を計算する（パーセント入力されている場合に使用）
   * 
   */
  function calculateImportOrTariffRows() {
    var checkList = cellClass;
    var subjectClassNamePattern = RegExp('detail area(3|4|5) divisionSubject');

    var tariffTargetPattern = RegExp('(^433:|^402:)');

    var checkRow = null;
    var checkCol = null;

    var areaCode = null;

    var ItemCol = null;

    var subtotalCol = null;

    var sum = 0;

    var targetFlag = false;

    for (var i = 0; i < checkList.length; i++) {
      targetFlag = false;
      if (areaCode = checkList[i].className.match(subjectClassNamePattern)) { // 仕入科目行の検索
        checkRow = checkList[i].row;
        checkCol = checkList[i].col;
        if (cellValue[checkRow][checkCol].match(tariffTargetPattern)) { // 仕入科目の値が合計対象の場合
          ItemCol = getColumnForRowAndClassName(checkRow, 'classItem', checkList);
          if (cellValue[checkRow][ItemCol]) { // 仕入部品の空値チェック
            subtotalCol = getColumnForRowAndClassName(checkRow, 'subtotal', checkList);
            sum += Number(cellValue[checkRow][subtotalCol].replace('\xA5', '').split(',').join(''));
          }
        }
      }
    }

    // その他費用の仕入科目セルの情報を取得
    var subjectClassName = 'detail area5 divisionSubject';
    var subjectElements = getElementsForClassName(subjectClassName, checkList);

    var importRows = [];
    var tariffRows = [];
    var certificationRows = [];

    // 輸入費用、関税の入力行取得
    for (var i = 0; i < subjectElements.length; i++) {
      var targetRow = subjectElements[i].row;
      var subjectCol = subjectElements[i].col;
      if (cellValue[targetRow][subjectCol].match(chargeSubjectPattern)) { // チャージの場合
        var itemCol = getColumnForRowAndClassName(targetRow, 'classItem', checkList);
        /*if (cellValue[targetRow][itemCol].match(importItemPattern)) { // 輸入費用の場合
          importRows.push(targetRow);
        } else */
        if (cellValue[targetRow][itemCol].match(tariffItemPattern)) { // 関税の場合
          tariffRows.push(targetRow);
        }
      }
      else if (cellValue[targetRow][subjectCol].match(partsSubjectPattern)) {
        var itemCol = getColumnForRowAndClassName(targetRow, 'classItem', checkList);
        if (cellValue[targetRow][itemCol].match(certificationPattern)) {  // 証紙の場合
          certificationRows.push(targetRow);
        }
      }
    }

    // 関税行の処理
    var tariffTotal = tariffRows.reduce(function (total, row) {
      var subtotal = calculateSubtotalByPercentage(row, sum);
      subtotal = Number(subtotal.replace('\xA5', '').split(',').join(''));

      return total + subtotal;

    }, sum);

    // 輸入費用行の処理
    /*
        importRows.forEach(function(row) {
          var percentCol = getColumnForRowAndClassName(row, 'customerCompany', checkList);
          var percent = (isNaN(cellValue[row][percentCol]) === false) ? Number(cellValue[row][percentCol]) / 100 : 0;
    
          if (percent) { // パーセント入力されている場合は単価の計算
            var quantityCol = getColumnForRowAndClassName(row, 'quantity', checkList);
            var priceCol = getColumnForRowAndClassName(row, 'price', checkList);
            var quantity = (isNaN(cellValue[row][quantityCol]) === false) ? Number(cellValue[row][quantityCol]) : 0;
            if (quantity) {
              var price = Math.floor((tariffTotal * percent / quantity) * 10**4) / 10**4;
            } else {
              var price = '';
            }
            // 単価の代入
            assignValueForGlobal(row, priceCol, price);
          }
       
          // 小計の再計算、代入
          calculateSubtotal(row);
        });
    */
    // 証紙の再計算
    // 上代の取得
    var checkList = cellClass;
    for (var i = 0; i < checkList.length; i++) {
      if (checkList[i].className == 'retailprice') {
        var retailRow = cellClass[i].row;
        var retailCol = cellClass[i].col;
        break;
      }
    }
    // 上代の値を数値に変換する
    var retail = (isNaN(cellValue[retailRow][retailCol].replace('\xA5', '').split(',').join('')) === false) ?
      Number(cellValue[retailRow][retailCol].replace('\xA5', '').split(',').join('')) : 0;

    certificationRows.forEach(function (row) {
      // 小計の再計算、代入
      calculateSubtotalByPercentage(row, retail);
    });

    return;
  }

  /**
   * 部材費の再計算を行う
   * 
   */
  function calculateMemberCost() {
    var checkList = cellClass;
    var targetClassNamePattern = RegExp('detail area(4|5) subtotal');

    var total = checkList.reduce(function (sum, value) {
      var add = 0;
      if (value.className.match(targetClassNamePattern)) {
        var targetRow = value.row;
        var payoffCol = getColumnForRowAndClassName(targetRow, 'payoff', checkList);
        if (cellValue[targetRow][payoffCol] !== '○') {
          add = Number(cellValue[targetRow][value.col].replace('\xA5', '').split(',').join(''));
        }
      }
      return sum + add;
    }, 0);

    // 書式設定
    var total = '\xA5' + numberFormat(total, 0);

    // 部材費の代入
    assignValueForClassNameCell('membercost', total);

    return;
  }


  /**
   * 償却費の再計算を行う
   * 
   */
  function calculateDepreciationCost() {
    var checkList = cellClass;
    var targetClassNamePattern = RegExp('detail area(3|4|5) subtotal');

    var total = checkList.reduce(function (sum, value) {
      var add = 0;
      if (value.className.match(targetClassNamePattern)) {
        var targetRow = value.row;
        var payoffCol = getColumnForRowAndClassName(targetRow, 'payoff', checkList);
        if (cellValue[targetRow][payoffCol] === '○') {
          add = Number(cellValue[targetRow][value.col].replace('\xA5', '').split(',').join(''));
        }
      }
      return sum + add;
    }, 0);

    // 書式設定
    total = '\xA5' + numberFormat(total, 0);

    // 償却費の代入
    assignValueForClassNameCell('depreciation_cost', total);

    return;
  }

  /**
   * 製造費用の再計算を行う
   * 
   */
  function calculateManufacturingCost() {
    var checkList = cellClass;
    var memberElement = getElementsForClassName('membercost', checkList);
    var depreciationElement = getElementsForClassName('depreciation_cost', checkList);

    var memberRow = memberElement[0].row;
    var memberCol = memberElement[0].col;
    var depRow = depreciationElement[0].row;
    var depCol = depreciationElement[0].col;

    var memberCost = Number(cellValue[memberRow][memberCol].replace('\xA5', '').split(',').join(''));
    var depreciationCost = Number(cellValue[depRow][depCol].replace('\xA5', '').split(',').join(''));

    var manufacturingCost = memberCost + depreciationCost;

    // 書式設定
    manufacturingCost = '\xA5' + numberFormat(manufacturingCost, 0);

    // 製造費の代入
    assignValueForClassNameCell('manufacturingcost', manufacturingCost);

    return;
  }

  /**
   * 製品利益と製品利益率の再計算を行う
   * 
   */
  function calculateProductProfit() {
    var checkList = cellClass;
    var totalElement = getElementsForClassName('product_totalprice', checkList);
    var costElement = getElementsForClassName('manufacturingcost', checkList);

    var totalRow = totalElement[0].row;
    var totalCol = totalElement[0].col;
    var costRow = costElement[0].row;
    var costCol = costElement[0].col;

    var total = Number(cellValue[totalRow][totalCol].replace('\xA5', '').split(',').join(''));
    var cost = Number(cellValue[costRow][costCol].replace('\xA5', '').split(',').join(''));

    var profit = total - cost;
    var profitRate = profit / total;

    // 書式設定
    profit = '\xA5' + numberFormat(profit, 0);
    profitRate = numberFormat(profitRate * 100, 2, '') + '%';

    // 製品利益の代入
    assignValueForClassNameCell('product_profit', profit);

    // 製品利益率の代入
    assignValueForClassNameCell('product_profit_rate', profitRate);

    return;
  }

  /**
   * 固定費利益と固定費利益率の再計算を行う
   * 
   */
  function calculateFixedCostProfit() {
    var checkList = cellClass;
    var totalElement = getElementsForClassName('fixedcost_totalprice', checkList);
    var costElement = getElementsForClassName('order_f_cost_not_depreciation', checkList);

    var totalRow = totalElement[0].row;
    var totalCol = totalElement[0].col;
    var costRow = costElement[0].row;
    var costCol = costElement[0].col;

    var total = Number(cellValue[totalRow][totalCol].replace('\xA5', '').split(',').join(''));
    var cost = Number(cellValue[costRow][costCol].replace('\xA5', '').split(',').join(''));

    var profit = total - cost;
    var profitRate = profit / total;

    // 書式設定
    profit = '\xA5' + numberFormat(profit, 0);
    profitRate = numberFormat(profitRate * 100, 2, '') + '%';

    // 固定費利益の代入
    assignValueForClassNameCell('fixedcost_profit', profit);

    // 固定費利益率の代入
    assignValueForClassNameCell('fixedcost_profit_rate', profitRate);

    return;
  }


  /**
   * 総売上高、間接製造経費の再計算を行う
   * 
   */
  function calculateSalesAmount() {
    var checkList = cellClass;
    var productElement = getElementsForClassName('product_totalprice', checkList);
    var fixedElement = getElementsForClassName('fixedcost_totalprice', checkList);
    var rateElement = getElementsForClassName('standard_rate', checkList);

    var productRow = productElement[0].row;
    var productCol = productElement[0].col;
    var fixedRow = fixedElement[0].row;
    var fixedCol = fixedElement[0].col;
    var rateRow = rateElement[0].row
    var rateCol = rateElement[0].col

    var product = Number(cellValue[productRow][productCol].replace('\xA5', '').split(',').join(''));
    var fixed = Number(cellValue[fixedRow][fixedCol].replace('\xA5', '').split(',').join(''));
    var standardRate = Number(cellValue[rateRow][rateCol].replace('%', '')) / 100;

    var salesAmount = product + fixed;
    var indirectCost = Math.floor(salesAmount * standardRate);

    // 書式設定
    salesAmount = '\xA5' + numberFormat(salesAmount, 0);
    indirectCost = '\xA5' + numberFormat(indirectCost, 0);

    // 総売上高の代入
    assignValueForClassNameCell('salesamount', salesAmount);

    // 間接製造経費の代入
    assignValueForClassNameCell('indirect_cost', indirectCost);

    return;
  }


  /**
   * 売上総利益の再計算を行う
   * 
   */
  function calculateProfit() {
    var checkList = cellClass;
    var productElement = getElementsForClassName('product_profit', checkList);
    var fixedElement = getElementsForClassName('fixedcost_profit', checkList);

    var productRow = productElement[0].row;
    var productCol = productElement[0].col;
    var fixedRow = fixedElement[0].row;
    var fixedCol = fixedElement[0].col;

    var product = Number(cellValue[productRow][productCol].replace('\xA5', '').split(',').join(''));
    var fixed = Number(cellValue[fixedRow][fixedCol].replace('\xA5', '').split(',').join(''));

    var profit = product + fixed;

    // 書式設定
    profit = '\xA5' + numberFormat(profit, 0);

    // 売上総利益の代入
    assignValueForClassNameCell('profit', profit);

    return;
  }

  /**
   * 営業利益、営業利益率、（売上）利益率の再計算を行う
   * 
   */
  function calculateOperatingProfit() {
    var checkList = cellClass;
    var profitElement = getElementsForClassName('profit', checkList);
    var indirectElement = getElementsForClassName('indirect_cost', checkList);
    var salesAmountElement = getElementsForClassName('salesamount', checkList);

    var profitRow = profitElement[0].row;
    var profitCol = profitElement[0].col;
    var indirectRow = indirectElement[0].row;
    var indirectCol = indirectElement[0].col;
    var SARow = salesAmountElement[0].row;
    var SACol = salesAmountElement[0].col;

    var profit = Number(cellValue[profitRow][profitCol].replace('\xA5', '').split(',').join(''));
    var indirect = Number(cellValue[indirectRow][indirectCol].replace('\xA5', '').split(',').join(''));
    var salesAmount = Number(cellValue[SARow][SACol].replace('\xA5', '').split(',').join(''));

    var opeProfit = profit - indirect;
    var opeProfitRate = opeProfit / salesAmount;
    var profitRate = profit / salesAmount;

    // 書式設定
    opeProfit = '\xA5' + numberFormat(opeProfit, 0);
    opeProfitRate = numberFormat(opeProfitRate * 100, 2, '') + '%';
    profitRate = numberFormat(profitRate * 100, 2, '') + '%';

    // 営業利益の代入
    assignValueForClassNameCell('operating_profit', opeProfit);

    // 営業利益率の代入
    assignValueForClassNameCell('operating_profit_rate', opeProfitRate);

    // （売上）利益率の代入
    assignValueForClassNameCell('profit_rate', profitRate);

    return;
  }

  //----------------------------------------(基本処理関数)-----------------------------------------------

  /**
   * 特定のクラス名を持つセルに値を代入する
   * 
   * @param {string} className クラス名
   * @param value 代入するセルの値
   * 
   */
  function assignValueForClassNameCell(className, value) {
    var cellElement = getElementsForClassName(className, cellClass);
    var row = cellElement[0].row;
    var col = cellElement[0].col;
    assignValueForGlobal(row, col, value);
    return;
  }

  /**
   * セルの値をグローバル変数のセル値オブジェクトに代入する
   * ※ cellVAlue及びcellDataを当ファイル内処理のグローバル変数として使用
   * 
   * @param {integer} row 代入する行
   * @param {integer} col 代入する列
   * @param value 代入するセルの値
   * 
   */
  function assignValueForGlobal(row, col, value) {
    cellValue[row][col] = value;
    cellData[row][col]['value'] = value;
    return;
  }

  /**
   * 値が空かどうか判定を行う
   * 
   * @param value 判定する値
   * 
   * @return {boolean} 空判定結果（true:空 false:空でない）
   */
  function isEmpty(value) {
    if (!value) {  //null or undefined or ''(空文字) or 0 or false
      if (value !== 0 && value !== false) {
        return true;
      }
    } else if (typeof value == "object") {  //array or object
      return Object.keys(value).length === 0;
    }
    return false;  //値は空ではない
  }

  /**
 * 値が数値型かどうか判定を行う
 * 
 * @param value 判定する値
 * 
 * @return {boolean} 判定結果
 */
  function isNumber(value) {
    return ((typeof value === 'number') && (isFinite(value)));
  }

  /**
* 小数の文字列をカンマ区切りにする
* 
* @param {string} strDecimal 変換する数値
* @param {string} thousands_sep 3桁ごとの区切り文字(default = ',')
* 
* @return {string} 整形された文字列
*/
  function commaSeparate(strDecimal, thousands_sep = ',') {
    var afterPoint = String(strDecimal).match(/\.\d+$/);
    if (afterPoint) {
      return String(strDecimal).replace(afterPoint[0], '').replace(/(\d)(?=(\d\d\d)+(?!\d))/g, '$1,') + afterPoint;
    } else {
      return String(strDecimal).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, '$1' + thousands_sep);
    }
  }

  /**
  * カンマ区切り、小数点表記の方法について指定する
  * 
  * @param {integer} num 変換する数値
  * @param {integer} decimals 小数点以下の桁数(default = 0)
  * @param {string} thousands_sep 3桁ごとの区切り文字(default = ',')
  * 
  * @return {string} 整形された文字列
  */
  function numberFormat(num, decimals = 0, thousands_sep = ',') {
    if (!isNaN(num)) {
      var number = Number(num) + 0.00001;
      var tmp = 1;
      if (decimals == 1) {
        tmp = 10;
      } else if (decimals == 2) {
        tmp = 100;
      } else if (decimals == 3) {
        tmp = 1000;
      } else if (decimals == 4) {
        tmp = 10000;
      }
      return commaSeparate(Math.floor((num * tmp)) / tmp, thousands_sep);
    }
    return false;
  }

  /**
 * 指定したクラス名を持つセルのクラス情報を取得する
 * ※クラス名完全一致
 * @param {integer} className 指定クラス名
 * 
 * @return {Array.object} elements クラス情報オブジェクトを持つ配列
 */
  function getElementsForClassName(className, searchList) {
    var filter = {
      className: className
    }
    var elements = searchList.filter(function (item) {
      for (var key in filter) {
        if (item[key] === undefined || item[key] != filter[key])
          return false;
      }
      return true;
    });
    return elements;
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
    var elements = searchList.filter(function (item) {
      for (var key in filter) {
        if (item[key] === undefined || item[key] != filter[key])
          return false;
      }
      return true;
    });
    return elements;
  }

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

    var elements = searchList.filter(function (item) {
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

  /**
   * 適用レートを取得する
   * @param {integer} row 指定行
   * 
   */
  function getConversionRate(row) {
    var checkList = cellClass;

    var monetaryCol = getColumnForRowAndClassName(row, 'monetaryDisplay', checkList);
    var deliveryCol = getColumnForRowAndClassName(row, 'delivery', checkList);

    var monetaryDisplay = cellValue[row][monetaryCol];

    if (monetaryDisplay == 'JP' || monetaryDisplay == '') {
      var conversionRate = numberFormat(1, 4);
      setConversionRate(row, conversionRate);
      return;
    } else if (monetaryDisplay == 'US') {
      var monetary = 2;
    } else if (monetaryDisplay == 'HK') {
      var monetary = 3;
    }

    var delivery = cellValue[row][deliveryCol];

    $.ajax({
      url: "/estimate/preview/ajaxPreview.php",
      type: "post",
      dataType: "json",
      async: false,
      data: {
        'strSessionID': $('input[name="strSessionID"]').val(),
        'monetary': monetary,
        'delivery': delivery
      }

    }).done(function (response) {
      var conversionRate = Number(response.curconversionrate);
      if (conversionRate) {
        conversionRate = numberFormat(conversionRate, 4);
      } else {
        conversionRate = '';
      }

      setConversionRate(row, conversionRate);

    }).fail(function (xhr, textStatus, errorThrown) {
      alert('DBエラー');
    });
  }

  function setConversionRate(row, conversionRate) {
    var checkList = cellClass;

    if (!conversionRate) {
      alert('適用レートを取得できませんでした。');
    }

    var rateCol = getColumnForRowAndClassName(row, 'conversionRate', checkList);

    assignValueForGlobal(row, rateCol, conversionRate);

    return;
  }

  // ---------------------------------------------データ不整合対策--------------------------------------------------

  // 画面のロック関数
  function screenLock() {
    // ロック用のdivを生成
    var element = document.createElement('div');
    element.id = "screenLock";

    // ロック用のスタイル
    element.style.height = '100%';
    element.style.left = '0px';
    element.style.position = 'fixed';
    element.style.top = '0px';
    element.style.width = '100%';
    element.style.zIndex = '9999';
    element.style.opacity = '0';

    var objBody = document.getElementsByTagName("body").item(0);
    objBody.appendChild(element);

    // setTimeout( function() {
    //     // ロック画面の削除
    //     screenUnlock();
    // }, 3000 );

    return true;
  }

  // 画面のアンロック関数
  function screenUnlock() {
    var dom_obj = document.getElementById('screenLock');
    if (dom_obj) {
      var dom_obj_parent = dom_obj.parentNode;
      dom_obj_parent.removeChild(dom_obj);
    } else {
      return false;
    }
    return true;
  }
  // レート編集イベント
  $('input[name="rate"]').on('change', function() {
    var deliveryYm = $('input[name="deliveryYm"]').val();
    var rate = $('input[name="rate"]').val();
    var monetaryUnit = $('input[name="monetaryUnit"]').val();    
    for (var row = 0; row < cellValue.length; row++) {      
      var rateCol = getColumnForRowAndClassName(row, 'conversionRate', cellClass);
      var deliveryCol = getColumnForRowAndClassName(row, 'delivery', cellClass);    
      var monetaryDisplayCol = getColumnForRowAndClassName(row, 'monetaryDisplay', cellClass);
      var rate_pre = cellValue[row][rateCol];      
      if (rateCol == undefined || deliveryCol == undefined || deliveryCol == undefined) {
        continue;
      }
      var conversionRate_pre = cellValue[row][rateCol];
      var delivery_pre = cellValue[row][deliveryCol];
      var monetary_pre = cellValue[row][monetaryDisplayCol];
      if (delivery_pre.substr(0,7) == deliveryYm && monetary_pre == monetaryUnit)
      {
        setConversionRate(row, rate);
        var calcFlag = {};
        calcFlag.subtotal = true;
        var rateCol = getColumnForRowAndClassName(row, 'conversionRate', cellClass);
        // セルのクラス情報取得
        var cellElement = getElementsForRowAndColumn(row, rateCol, cellClass);  
        // エリアコードの取得
        var areaCode = cellElement[0].className.match(/area([0-9]+)/);
        // 小計再計算時の処理
        if (calcFlag.subtotal === true) {
          setCalcFlagForChangeSubtotal(Number(areaCode[1]), calcFlag)
        }
        calculate(calcFlag, row);
      }
    }
      
    table[0].selectCell(0, 0);

  });
  // 編集保存処理
  $('#update_regist').on('click', function () {    
    var estimateDetailNoCount = 0
    $.map(detailNoList, function(value, index){
      if (value.estimateDetailNo != null) {
        estimateDetailNoCount += 1;
      }
    });
    if (estimateDetailNoCount == 0) {
      alert("見積原価明細を一つ以上追加してください。")
      return false;
    }

    if (window.confirm('編集内容を保存してプレビュー画面を再読み込みします。よろしいですか？')) {
      var postData = {
        value: cellValue,
        class: cellClass,
        estimateDetailNo: detailNoList,
        readOnlyDetailRow: readOnlyDetailRow,
        rateEditInfoArry: rateEditInfoArry
      }

      var postJson = JSON.stringify(postData, replacer);

      $("<input>", {
        type: 'hidden',
        name: 'postData',
        value: postJson
      }).appendTo('#formData');

      var form = $('#formData');

      windowName = 'updateResult';

      winWidth = 700;
      winHeight = 500;

      var x = (screen.width - winWidth) / 2;
      var y = (screen.height - winHeight) / 2;
      var windowResult = open(
        'about:blank',
        windowName,
        'scrollbars=yes, width=' + winWidth + ', height=' + winHeight + ', top=' + y + ', left=' + x + 'resizable=0 location=0'
      );

      form.attr('action', '/estimate/preview/update.php');
      form.attr('target', windowName);
      // サブミット
      form.submit();

    } else {
      return false;
    }
  });


  function replacer(key, value) {
    var ret = value;
    if (typeof value === 'string') {
      ret = value.replace(backSlashPattern, '\\');
    }
    return ret;
  }

  /**
   * セルの入力値が変更された時の処理を行う
   * @param {integer} row 変更セルの行
   * @param {integer} col 変更セルの列
   * @param {integer} oldValue 変更前の値
   * @param {integer} newValue 変更後の値
   * 
   */
  function correctValue(row, col, oldValue) {
    var checkList = cellClass;
    var newValue = oldValue;
    var elements = getElementsForRowAndColumn(row, col, checkList);

    var calcFlag = {};// subtotal:金額合計、member:部材費、substitutePQ:償却、productionQuantity:数量（償却数）、importOrTariff:関税、輸入費

    var className = elements[0].className;
    if (className == 'retailprice') {  //上代
      if (isNaN(oldValue)) {
        tmpValue = toHalfWidth(oldValue);
        newValue = tmpValue.replace(/[^0-9.]/g, '');
      }
    }
    else if (className.includes('cartonquantity')) { // カートン入数
      if (isNaN(oldValue)) {
        tmpValue = toHalfWidth(oldValue);
        newValue = tmpValue.replace(/[^0-9.]/g, '');
      }
    }
    else if (className.includes('detail')) { // 明細行

      // セルのクラス情報取得
      var cellElement = getElementsForRowAndColumn(row, col, checkList);

      // エリアコードの取得
      var areaCode = cellElement[0].className.match(/area([0-9]+)/);


      if (className.includes('quantity')) {
        if (isNaN(oldValue)) {
          tmpValue = toHalfWidth(oldValue);
          newValue = tmpValue.replace(/[^0-9.]/g, '');
        }
      } else if (className.includes('price')) {    // 単価
        if (isNaN(oldValue)) {
          tmpValue = toHalfWidth(oldValue);
          newValue = tmpValue.replace(/[^0-9.]/g, '');
        }
      } else if (className.includes('conversionRate')) {   // 通貨レート
        if (isNaN(oldValue)) {
          tmpValue = toHalfWidth(oldValue);
          newValue = tmpValue.replace(/[^0-9.]/g, '');
        }
      } else if (className.includes('delivery')) {  // 納期
        if (isNaN(oldValue)) {
          tmpValue = toHalfWidth(oldValue);
        }
        else {
          tmpValue = oldValue;
        }
        var reg = new RegExp(/^[0-9]{8}$/)
        if (reg.test(tmpValue)) {
          var yyyy = tmpValue.substr(0, 4);
          var mm = tmpValue.substr(4, 2);
          var dd = tmpValue.substr(6);
          newValue = yyyy + "/" + mm + "/" + dd;
        }
      }
      else if (className.includes('payoff')) { // 償却          
        var area = className.match(/area(\d+)/);
        if (Number(area[1]) === 5) {  // ただし、エリア5の場合に限る
          if (isNaN(oldValue)) {
            tmpValue = toHalfWidth(oldValue);
            newValue = tmpValue.replace(/[^0-9.]/g, '');
          }
        }
      }
    }
    return newValue;
  };

  /**
   * 全角から半角への変換関数
   * 入力値の英数記号を半角変換して返却
   * [引数]   strVal: 入力値
   * [返却値] String(): 半角変換された文字列
   */
  function toHalfWidth(strVal) {
    if (isNaN(strVal)) {
      // 半角変換
      var halfVal = strVal.replace(/[！-～]/g,
        function (tmpStr) {
          // 文字コードをシフト
          return String.fromCharCode(tmpStr.charCodeAt(0) - 0xFEE0);
        }
      );
    }
    else {
      halfVal = strVal;
    }
    return halfVal;
  }

  function changeBackColor(rowFrom, rowTo, isSelected) {
    for (var row = rowFrom; row <= rowTo; row++) {
      for (var column = startColumn; column <= endColumn; column++) {
        if (isSelected == false) {
          cellData[row][column]['backgroundColor'] = cellColorList[row][column];
        }
        else {
          cellData[row][column]['backgroundColor'] = 'DDDDDD';
        }
      }
    }
    table[0].render();
  }

});








