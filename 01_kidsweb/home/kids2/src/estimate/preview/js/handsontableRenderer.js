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

    // セルのクラスを取得
    var cellClass = sheetData['cellClass'];

    var gridId = 'grid' + sheetNum;

    grid[sheetNum] = document.getElementById(gridId);

    // Handsontableでタグに表を埋め込む
    table[sheetNum] = new Handsontable(grid[sheetNum], {
      data: cellValue,
      height: 700,
      width: '100%',
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

  
  // var cellString = JSON.stringify(cellClass);
  // console.log(cellString);
  

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

  console.log(cellData);

  // handsontableを操作するボタンの処理
  $(function() {

    function getSelectedCell() {
      var selected = table[0].getSelected();
      if (selected) {
        return selected[0];
      } else {
        return false;
      }
    }

    // 行追加
    $('.btnRowAdd').on('click', function(){
      var selectedRange = getSelectedCell();
      if (selectedRange) {
        var selectedRow = selectedRange[0];
        var selectedColumn = selectedRange[1];

        var checkList = JSON.parse(JSON.stringify(cellClass));

        var selectedCell = getElementsForRowAndColumn(selectedRow, selectedColumn, checkList);

        // 要検証(8/20)
        console.log(checkList);
        console.log(selectedCell);

        if (selectedCell[0].className.includes('detail')) {
          var newData = [];
          var blankRow = [];
  
          // 空行配列の生成
          blankRow[0] = JSON.parse(JSON.stringify(cellData[selectedRow]));        
          for (var column = startColumn; column <= endColumn; column++) {
            blankRow[0][column]['value'] = '';
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
          
          merge.forEach(function(value) {
            if (value.row == selectedRow) {
              var newValue = $.extend(true, {}, value);
              selectedMerge.push(newValue);
              value.row += 1;
            } else if (value.row > selectedRow) {
              value.row += 1;
            }
          });

          var selectedClassInfo = [];
  
          cellClass.forEach(function(value) {
            if (value.row == selectedRow) {
              var newClassInfo = $.extend(true, {}, value);
              selectedClassInfo.push(newClassInfo);
              value.row += 1;
            } else if (value.row > selectedRow) {
              value.row += 1;
            }
          });


          merge = merge.concat(selectedMerge);
          cellClass = cellClass.concat(selectedClassInfo);
          rowHeight.splice(selectedRow, 0, rowHeight[selectedRow]);
  
          table[0].updateSettings({
            data: cellValue,
            rowHeights: rowHeight,
            mergeCells: merge,
            cell: cellClass
          });

        } else {
          alert('明細行以外の行追加はできません');
        }

        table[0].selectCell(selectedRow, selectedColumn);
      }
    });
    

    // 行削除
    $('.btnRowDelete').on('click', function(){
      var selectedRange = getSelectedCell();
      if (selectedRange) {
        var selectedRow = selectedRange[0];
        var selectedColumn = selectedRange[1];

        var checkList = JSON.parse(JSON.stringify(cellClass));

        var selectedCell = getElementsForRowAndColumn(selectedRow, selectedColumn, checkList);

        if (selectedCell[0].className.includes('detail')) {
          var area = selectedCell[0].className.match(/area[0-9]+/);
          var areaClassName = 'detail ' + area + ' divisionSubject';

          var elements = getElementsForClassName(areaClassName, checkList);

          if (elements.length > 1) {
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
            merge.forEach(function(value) {
              if (value.row == selectedRow) {
                return;
              } else if (value.row > selectedRow) {
                value.row -= 1;
              }
              var newValue = $.extend(true, {}, value);
              newMerge.push(newValue);
            });
    
            var newCellClass = [];
            cellClass.forEach(function(value) {
              if (value.row == selectedRow) {
                return;
              } else if (value.row > selectedRow) {
                value.row -= 1;
              }
              var newValue = $.extend(true, {}, value);
              newCellClass.push(newValue);
            });
  
            merge = newMerge;
            cellClass = newCellClass;
  
            rowHeight.splice(selectedRow, 1);

            var a = reCalProductionQuantity()
    
            table[0].updateSettings({
              data: cellValue,
              rowHeights: rowHeight,
              mergeCells: merge,
              cell: cellClass
            });
          } else {
            alert('該当エリアの明細行が1行以下のため行削除できません');
          }
        } else {
          alert('明細行以外の削除はできません');
        }

        table[0].selectCell(selectedRange[0], selectedRange[1]);
      }
    });

    // 行移動(エリア内先頭に)
    $('.btnMoveTop').on('click', function(){
      var selectedRange = getSelectedCell();
      if (selectedRange) {
        var selectedRow = selectedRange[0];
        var selectedColumn = selectedRange[1];
        
        var checkList = JSON.parse(JSON.stringify(cellClass));

        var selectedCell = getElementsForRowAndColumn(selectedRow, selectedColumn, checkList);

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
              } else if (!maxRow || (maxRow && maxRow < rowNumber)){
                maxRow = rowNumber;
              }
            }
            var minRowCellValue = cellValue.splice(selectedRow, 1);
            cellValue.splice(minRow, 0, minRowCellValue[0]);

            // 元のセルデータを更新する
            for (var i = minRow; i <= maxRow; i++) {
              for (var j = startColumn; j <= endColumn; j++) {
                cellData[i][j]['value'] = cellValue[i][j];
              }
            }

            table[0].updateSettings({
              data: cellValue,
              rowHeights: rowHeight,
              mergeCells: merge,
              cell: cellClass
            });
          }

        } else {
          alert('明細行以外の移動はできません');
        }

        table[0].selectCell(minRow, selectedRange[1]);
      }
    });

    // 行移動(エリア内最後尾に)
    $('.btnMoveBottom').on('click', function(){
      var selectedRange = getSelectedCell();
      if (selectedRange) {
        var selectedRow = selectedRange[0];
        var selectedColumn = selectedRange[1];

        var checkList = JSON.parse(JSON.stringify(cellClass));

        var selectedCell = getElementsForRowAndColumn(selectedRow, selectedColumn, checkList);

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
              } else if (!maxRow || (maxRow && maxRow < rowNumber)){
                maxRow = rowNumber;
              }
            }
            var maxRowCellValue = cellValue.splice(selectedRow, 1);
            cellValue.splice(maxRow, 0, maxRowCellValue[0]);

            // 元のセルデータを更新する
            for (var i = minRow; i <= maxRow; i++) {
              for (var j = startColumn; j <= endColumn; j++) {
                cellData[i][j]['value'] = cellValue[i][j];
              }
            }

            table[0].updateSettings({
              data: cellValue,
              rowHeights: rowHeight,
              mergeCells: merge,
              cell: cellClass
            });
          }

        } else {
          alert('明細行以外の移動はできません');
        }

        table[0].selectCell(maxRow, selectedRange[1]);
      }
    });

    // 行移動(一行上に)
    $('.btnMoveUpper').on('click', function(){
      var selectedRange = getSelectedCell();
      if (selectedRange) {
        var selectedRow = selectedRange[0];
        var selectedColumn = selectedRange[1];

        var checkList = JSON.parse(JSON.stringify(cellClass));

        var selectedCell = getElementsForRowAndColumn(selectedRow, selectedColumn, checkList);

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
            
            if(selectedRow != minRow) {
              var insertRow = selectedRow - 1;
              var movecellValue = cellValue.splice(selectedRow, 1);
              cellValue.splice(insertRow, 0, movecellValue[0]);
  
              // 元のセルデータを更新する
              for (var j = startColumn; j <= endColumn; j++) {
                cellData[selectedRow][j]['value'] = cellValue[insertRow][j];
                cellData[insertRow][j]['value'] = cellValue[selectedRow][j];
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

        table[0].selectCell(insertRow, selectedRange[1]);
      }
    });

    // 行移動(一行上に)
    $('.btnMoveLower').on('click', function(){
      var selectedRange = getSelectedCell();
      if (selectedRange) {
        var selectedRow = selectedRange[0];
        var selectedColumn = selectedRange[1];

        var checkList = JSON.parse(JSON.stringify(cellClass));

        var selectedCell = getElementsForRowAndColumn(selectedRow, selectedColumn, checkList);

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
            
            if(selectedRow != maxRow) {
              var insertRow = selectedRow + 1;
              var movecellValue = cellValue.splice(selectedRow, 1);
              cellValue.splice(insertRow, 0, movecellValue[0]);
  
              // 元のセルデータを更新する
              for (var j = startColumn; j <= endColumn; j++) {
                cellData[selectedRow][j]['value'] = cellValue[insertRow][j];
                cellData[insertRow][j]['value'] = cellValue[selectedRow][j];
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

        table[0].selectCell(insertRow, selectedRange[1]);
      }
    });

    
    // 償却数の再計算処理
    function reCalProductionQuantity() {
      var areaClass = 'area1';
      // var divisionClass = 'detail ' + areaClass + ' divisionSubject';
      var classClass = 'detail ' + areaClass + ' classItem';
      var quantityClass = 'detail ' + areaClass + ' quantity';

      var checkList = JSON.parse(JSON.stringify(cellClass));

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
          var quantity = Number(cellValue[quantityRow][quantityCol].replace(',', ''));
          console.log(cellValue[quantityRow][quantityCol]);
          productionQuantity += quantity;
        }
      }

      var PQclass = 'productionquantity';
      var PQCell = getElementsForClassName(PQclass, checkList);

      // 償却数の入力セル位置を取得
      var PQRow = PQCell[0].row;
      var PQCol = PQCell[0].col;
      // グローバル変数の操作
      var separateNum = commaSeparate(productionQuantity);
      cellValue[PQRow][PQCol] = separateNum;
      cellData[PQRow][PQCol]['value'] = cellValue[PQRow][PQCol];

      setQuantityForPartsAndOthers(separateNum);

      return;
    }


    // 部材費及びその他費用の数量設定
    function setQuantityForPartsAndOthers(productionQuantity = null) {
      if (!productionQuantity) {
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

      var checkList = JSON.parse(JSON.stringify(cellClass));

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
          cellValue[row][quaCol] = productionQuantity;
        }
      }

      // エリア5の処理
      var areaClass = 'area5';
      var subjectClass = 'detail ' + areaClass + ' divisionSubject';
      var itemClass = 'detail ' + areaClass + ' classItem';
      var quantityClass = 'detail ' + areaClass + ' quantity';

      var checkList = JSON.parse(JSON.stringify(cellClass));

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
          cellValue[row][quaCol] = productionQuantity;
        }
      }

      var MEQcell = getElementsForClassName('member_quantity', checkList);
      cellValue[MEQcell[0].row][MEQcell[0].col] = productionQuantity;

      var DQcell = getElementsForClassName('depreciation_quantity', checkList);
      cellValue[DQcell[0].row][DQcell[0].col] = productionQuantity;

      var MAQcell = getElementsForClassName('manufacturing_quantity', checkList);
      cellValue[MAQcell[0].row][MAQcell[0].col] = productionQuantity;

      return;
    }

    function sumif() {

    }


    // 数値の文字列をカンマ区切りにする
    function commaSeparate(num){
      return String(num).replace( /(\d)(?=(\d\d\d)+(?!\d))/g, '$1,');
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
      var elements = searchList.filter(function(item) {
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
      var elements = searchList.filter(function(item) {
        for (var key in filter) {
          if (item[key] === undefined || item[key] != filter[key])
            return false;
        }
        return true;
      });
      return elements;
    }

  });

  $('.area1').change(function(){
      alert('あうあう');
  });
});
