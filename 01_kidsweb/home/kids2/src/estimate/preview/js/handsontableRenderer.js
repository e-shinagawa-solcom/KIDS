// handsontable�����ؿ�

$(function(){
  // �ǡ����μ���
  var script = $('#script').attr('data-param');
  var result = JSON.parse(script);

  // �����ȿ��μ���
  var sheetNumber = Object.keys(result).length;

  var grid = [];
  var table = [];

  // ɽ���ѥǡ����κ���
  for (var sheetNum = 0; sheetNum < sheetNumber; sheetNum++) {
    var sheetData = result[sheetNum];
    // ���Ϲ��󡢽�λ����μ���
    var startRow = sheetData['startRow'];
    var endRow = sheetData['endRow'];
    var startColumn = sheetData['startColumn'];
    var endColumn = sheetData['endColumn'];

    // ����Υǡ�������
    var cellData = sheetData['cellData'];

    var cellValue = [];

    // ����ξ��������˳�Ǽ����
    for (var i = startRow; i <= endRow; i++) {
      var rowValue = [];
      for (var j = startColumn; j <= endColumn; j++) {
        rowValue.push(cellData[i][j]['value']);
      }
      cellValue.push(rowValue);
    }

    // �ޡ�������μ���
    var merge = sheetData['mergedCellsList'];
    // �Ԥι⤵������������
    var rowHeight = sheetData['rowHeight'];
    var columnWidth = sheetData['columnWidth'];

    // ����Υ��饹�����
    var cellClass = sheetData['cellClass'];

    var gridId = 'grid' + sheetNum;

    grid[sheetNum] = document.getElementById(gridId);

    // Handsontable�ǥ�����ɽ��������
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

  // Handsontable�Υ����ƥ��ֲ��Τ��ᡢtable�κ����������֤ˤ���
  table[0].selectCell(0, 0);
  table[0].deselectCell();

  
  // var cellString = JSON.stringify(cellClass);
  // console.log(cellString);
  

  // Handsontable��td����CSS
  function firstRenderer(instance, td, row, col, prop, value, cellProperties) {
    if (col <= 2) {
      Handsontable.renderers.HtmlRenderer.apply(this, arguments);  //���ץ�ӥ塼�γ�ǧ�����������
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

  // handsontable������ܥ���ν���
  $(function() {

    function getSelectedCell() {
      var selected = table[0].getSelected();
      if (selected) {
        return selected[0];
      } else {
        return false;
      }
    }

    // ���ɲ�
    $('.btnRowAdd').on('click', function(){
      var selectedRange = getSelectedCell();
      if (selectedRange) {
        var selectedRow = selectedRange[0];
        var selectedColumn = selectedRange[1];

        var checkList = JSON.parse(JSON.stringify(cellClass));

        var selectedCell = getElementsForRowAndColumn(selectedRow, selectedColumn, checkList);

        // �׸���(8/20)
        console.log(checkList);
        console.log(selectedCell);

        if (selectedCell[0].className.includes('detail')) {
          var newData = [];
          var blankRow = [];
  
          // �������������
          blankRow[0] = JSON.parse(JSON.stringify(cellData[selectedRow]));        
          for (var column = startColumn; column <= endColumn; column++) {
            blankRow[0][column]['value'] = '';
          }
  
          var newRow = startRow;
          // ���Ԥ�ǡ�������������
          for (var row = startRow; row <= endRow; row++) {
            if (row == selectedRow) {
              newData[newRow] = blankRow[0];
              newRow++;
            }
            newData[newRow] = JSON.parse(JSON.stringify(cellData[row]));
            newRow++;
          }
  
          // ���Υ���ξ����񤭴�����
          cellData = newData;
          endRow += 1;
  
          cellValue = [];
   
          // ����ξ��������˳�Ǽ����
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
          alert('���ٹ԰ʳ��ι��ɲäϤǤ��ޤ���');
        }

        table[0].selectCell(selectedRow, selectedColumn);
      }
    });
    

    // �Ժ��
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
  
            // �Ԥο��ͤ�������
            for (var row = startRow; row <= endRow; row++) {
              if (row == selectedRow) {
                continue;
              }
              newData[newRow] = JSON.parse(JSON.stringify(cellData[row]));
              newRow++;
            }
    
            // ���Υ���ξ����񤭴�����
            cellData = newData;
            endRow -= 1;
    
            cellValue = [];
     
            // ����ξ��������˳�Ǽ����
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
            alert('�������ꥢ�����ٹԤ�1�԰ʲ��Τ���Ժ���Ǥ��ޤ���');
          }
        } else {
          alert('���ٹ԰ʳ��κ���ϤǤ��ޤ���');
        }

        table[0].selectCell(selectedRange[0], selectedRange[1]);
      }
    });

    // �԰�ư(���ꥢ����Ƭ��)
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

            // ���Υ���ǡ����򹹿�����
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
          alert('���ٹ԰ʳ��ΰ�ư�ϤǤ��ޤ���');
        }

        table[0].selectCell(minRow, selectedRange[1]);
      }
    });

    // �԰�ư(���ꥢ��Ǹ�����)
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

            // ���Υ���ǡ����򹹿�����
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
          alert('���ٹ԰ʳ��ΰ�ư�ϤǤ��ޤ���');
        }

        table[0].selectCell(maxRow, selectedRange[1]);
      }
    });

    // �԰�ư(��Ծ��)
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
  
              // ���Υ���ǡ����򹹿�����
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
          alert('���ٹ԰ʳ��ΰ�ư�ϤǤ��ޤ���');
        }

        table[0].selectCell(insertRow, selectedRange[1]);
      }
    });

    // �԰�ư(��Ծ��)
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
  
              // ���Υ���ǡ����򹹿�����
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
          alert('���ٹ԰ʳ��ΰ�ư�ϤǤ��ޤ���');
        }

        table[0].selectCell(insertRow, selectedRange[1]);
      }
    });

    
    // ���ѿ��κƷ׻�����
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
        var mainProductName = '1:�ܲ�';
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

      // ���ѿ������ϥ�����֤����
      var PQRow = PQCell[0].row;
      var PQCol = PQCell[0].col;
      // �����Х��ѿ������
      var separateNum = commaSeparate(productionQuantity);
      cellValue[PQRow][PQCol] = separateNum;
      cellData[PQRow][PQCol]['value'] = cellValue[PQRow][PQCol];

      setQuantityForPartsAndOthers(separateNum);

      return;
    }


    // ������ڤӤ���¾���Ѥο�������
    function setQuantityForPartsAndOthers(productionQuantity = null) {
      if (!productionQuantity) {
        // ���ѿ���Ϳ�����Ƥ��ʤ����ϥ�����֤�����ѿ����������
        var PQclass = 'productionquantity';
        var PQCell = getElementsForClassName(PQclass, checkList);
  
        // ���ѿ������ϥ�����֤����
        var PQRow = PQCell[0].row;
        var PQCol = PQCell[0].col;

        var productionQuantity = cellValue[PQRow][PQCol];
      }

      // ���ꥢ4�ν���
      var areaClass = 'area4';
      var subjectClass = 'detail ' + areaClass + ' divisionSubject';
      var itemClass = 'detail ' + areaClass + ' classItem';
      var quantityClass = 'detail ' + areaClass + ' quantity';

      var checkList = JSON.parse(JSON.stringify(cellClass));

      var subjectElements = getElementsForClassName(subjectClass, checkList);
      var itemElements = getElementsForClassName(itemClass, checkList);
      var quantityElements = getElementsForClassName(quantityClass, checkList);

      // ������������
      for (var i = 0; i < subjectElements.length; i++) {
        var row = subjectElements[i].row;
        var subCol = subjectElements[i].col;
        var iteCol = itemElements[i].col;
        if (cellValue[row][subCol] && cellValue[row][iteCol]) {
          // �������ܤȻ������ʤ�ξ�����Ϥ���Ƥ�����Ͽ��̤˽��ѿ��򥻥åȤ���
          var quaCol = quantityElements[i].col;
          cellValue[row][quaCol] = productionQuantity;
        }
      }

      // ���ꥢ5�ν���
      var areaClass = 'area5';
      var subjectClass = 'detail ' + areaClass + ' divisionSubject';
      var itemClass = 'detail ' + areaClass + ' classItem';
      var quantityClass = 'detail ' + areaClass + ' quantity';

      var checkList = JSON.parse(JSON.stringify(cellClass));

      var subjectElements = getElementsForClassName(subjectClass, checkList);
      var itemElements = getElementsForClassName(itemClass, checkList);
      var quantityElements = getElementsForClassName(quantityClass, checkList);

      // ������������
      for (var i = 0; i < subjectElements.length; i++) {
        var row = subjectElements[i].row;
        var subCol = subjectElements[i].col;
        var iteCol = itemElements[i].col;
        if (cellValue[row][subCol] && cellValue[row][iteCol]) {
          // �������ܤȻ������ʤ�ξ�����Ϥ���Ƥ�����Ͽ��̤˽��ѿ��򥻥åȤ���
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


    // ���ͤ�ʸ����򥫥�޶��ڤ�ˤ���
    function commaSeparate(num){
      return String(num).replace( /(\d)(?=(\d\d\d)+(?!\d))/g, '$1,');
    }

     /**
     * ���ꤷ�����饹̾����ĥ���Υ��饹������������
     * �����饹̾��������
     * @param {integer} className ���ꥯ�饹̾
     * 
     * @return {Array.object} elements ���饹���󥪥֥������Ȥ��������
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
     * ���ꤷ���Ԥ���������ĥ���Υ��饹������������
     * @param {integer} row �����
     * @param {integer} col ������
     * 
     * @return {Array.object} elements ���饹���󥪥֥������Ȥ��������
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
      alert('��������');
  });
});
