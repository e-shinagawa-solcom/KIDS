// handsontable�����ؿ�

$(function() {

  // ��������
  const chargeSubjectPattern = RegExp('^1224:');
  const importItemPattern = RegExp('^2:');
  const tariffItemPattern = RegExp('^3:');
  const importOrTariffPattern = RegExp('^(2|3):');
  const backSlashPattern = RegExp('\xA5', 'g');

  // �ǡ����μ���
  var script = $('#script').attr('data-param');
  var result = JSON.parse(script);

  // html��JSON���
  $('#script').remove();

  var grid = [];
  var table = [];

  // ɽ���ѥǡ����κ���
  var sheetData = result[0];
  // ���Ϲ��󡢽�λ����μ���
  var startRow = sheetData['startRow'];
  var endRow = sheetData['endRow'];
  var startColumn = sheetData['startColumn'];
  var endColumn = sheetData['endColumn'];

  // ����Υǡ�������
  var cellData = sheetData['cellData'];

  var cellValue = [];

  var readOnlyDetailRow = [];

  if (sheetData['readOnlyDetailRow']) {
    readOnlyDetailRow = sheetData['readOnlyDetailRow'];
  }

  var detailNoList = sheetData['detailNoList'];

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
  var columnWidth = sheetData['columnWidth'].map(function(value) {
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

  // ������ɥ��Υꥵ����
  window.resizeTo(width, height);

  // ����Υ��饹�����
  var cellClass = sheetData['cellClass'];

  var gridId = 'grid0';

  grid[0] = document.getElementById(gridId);

  var classList = cellClass;

  // �ץ������Υꥹ�Ȥ����
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

  // ���ʬ��ʻ������ܡˡ�����ʬ�ʻ������ʡˤΥɥ�åץ�����ꥹ������
  for (var i = 0; i < dropdownDSCIList.length; i++) {
    var area = dropdownDSCIList[i].areacode;
    var divisionSubject = dropdownDSCIList[i].divisionsubject;
    var classItem = dropdownDSCIList[i].classitem;

    // ���ꥢ�����ɤΥ���ǥå������ʤ����ϥ���ǥå����������������ͤ�������ɲä���
    if (isEmpty(dropdownDivSub[area])) {
      dropdownDivSub[area] = [''];
    }

    // �ץ������˽�ʣ���ʤ�����ɲ�
    if (dropdownDivSub[area].indexOf(divisionSubject) == -1){
      dropdownDivSub[area].push(divisionSubject);
    }

    // ���ꥢ�����ɤΥ���ǥå������ʤ����ϥ���ǥå����������������ͤ�������ɲä���
    if (isEmpty(dropdownClsItm[area])) {
      dropdownClsItm[area] = {};
    }
    
    if (isEmpty(dropdownClsItm[area][divisionSubject])) {
      dropdownClsItm[area][divisionSubject] = [''];
    }

    // �ץ������˽�ʣ���ʤ�����ɲ�
    if (dropdownClsItm[area][divisionSubject].indexOf(classItem) == -1){
      dropdownClsItm[area][divisionSubject].push(classItem);
    }      
  }

  // �ܵ���(������)�Υɥ�åץ�����ꥹ�Ⱥ���
  for (var i = 0; i < dropdownCompanyList.length; i++) {
    var attributeCode = dropdownCompanyList[i].lngattributecode;
    var customerCompany = dropdownCompanyList[i].customercompany;
    if (isEmpty(dropdownCompany[attributeCode])) {
      dropdownCompany[attributeCode] = [''];
    }
    dropdownCompany[attributeCode].push(customerCompany);
  }

  // �Ķ�����Υɥ�åץ�����ꥹ�Ⱥ���
  for (var i = 0; i < dropdownGUList.length; i++) {
    var groupCode = dropdownGUList[i].groupcode;
    var userCode = dropdownGUList[i].usercode;

    // ���롼�פΥɥ�åץ����󤬶��ξ��϶��ͤ�������ɲä���
    if (isEmpty(dropdownGroup)) {
      dropdownGroup.push('');
    }

    // �ɥ�åץ�����˽�ʣ���ʤ�����ɲ�
    if (dropdownGroup.indexOf(groupCode) == -1){
      dropdownGroup.push(groupCode);
    }

    // ���롼�פΥ���ǥå������ʤ����ϥ���ǥå����������������ͤ�������ɲä���
    if (isEmpty(dropdownUser[groupCode])) {
      dropdownUser[groupCode] = [''];
    }
    
    // �ץ������˽�ʣ���ʤ�����ɲ�
    if (dropdownUser[groupCode].indexOf(userCode) == -1){
      dropdownUser[groupCode].push(userCode);
    }      
  }

  // ��ȯô���ԤΥɥ�åץ�����ꥹ�Ⱥ���
  for (var i = 0; i < dropdownDevList.length; i++) {
    var devUserCode = dropdownDevList[i].usercode;

    // ���롼�פΥɥ�åץ����󤬶��ξ��϶��ͤ�������ɲä���
    if (isEmpty(dropdownDevUser)) {
      dropdownDevUser.push('');
    }

    // �ɥ�åץ�����˽�ʣ���ʤ�����ɲ�
    if (dropdownDevUser.indexOf(devUserCode) == -1){
      dropdownDevUser.push(devUserCode);
    }   
  }

  // Handsontable�ǥ�����ɽ��������
  table[0] = new Handsontable(grid[0], {
    data: cellValue,
    disableVisualSelection: 'area',
    fillHandle: false,
    undo: false,
    beforeChange: function(changes, source) {
      if (source == 'edit' ||
          source == 'CopyPaste.paste' ||
          source == 'Autofill.fill') {
          for (var i = 0; i < changes.length; i++) {
          var row = changes[i][0];
          var col = changes[i][1];
          var oldValue = changes[i][2];
          var newValue = changes[i][3];
          if (newValue !== oldValue) { // �ѹ������ä����
            cellValue[row][col] = newValue;
            cellData[row][col]['value'] = cellValue[row][col];
            onChangedValue(row, col, oldValue, newValue);
          }
        }
      }
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

  // Handsontable�Υ����ƥ��ֲ��Τ��ᡢtable�κ����������֤ˤ���
  table[0].selectCell(0, 0);
  table[0].deselectCell();


  // ���������
  function cells(row, col, prop) {
    var cellProperties = {};
    cellProperties = setCellProperties(row, col, prop);
    return cellProperties;
  }

  //---------------------------------(cellProperties������)-----------------------------------------

  /**
   * ����ץ�ѥƥ�������
   * 
   * @param {integer} row ���ֹ�
   * @param {integer} col ���ֹ�
   * @param {string} prop
   * 
   * @return {object} cellProperties ����ץ�ѥƥ�
   */
  function setCellProperties(row, col, prop) {
    var cellProperties = {};

    var elements = getElementsForRowAndColumn(row, col, cellClass);

    // ����ν񼰡ʥɥ�åץ�����ɽ���������ˤ����ꤹ��
    if (!isEmpty(elements)) {
      var className = elements[0].className;

      if (className === 'inchargegroupcode') { // �Ķ�����
        cellProperties.type = 'dropdown';
        cellProperties.source = dropdownGroup;

      } else if (className === 'inchargeusercode') { // ô��
        // �Ķ�����μ���
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

      // �إå������եå����ο���
      } else if (className === 'cartonquantity'     // �����ȥ������
      || className === 'productionquantity'         // ���ѿ�
      || className === 'member_quantity'            // �������оݿ�
      || className === 'depreciation_quantity'      // �����оݿ�
      || className === 'manufacturing_quantity') {  // ����
        cellProperties.type = 'numeric';
        cellProperties.numericFormat = {
          pattern: '#,##0',
        };

      } else if (className.includes('detail')) {    // ���ٹԤǤ�����

        if (className.includes('divisionSubject')) { // ����ʬ���������ܤν���
          var area = className.match(/area(\d+)/);
          cellProperties.type = 'dropdown';
          cellProperties.source = dropdownDivSub[Number(area[1])];

        } else if (className.includes('classItem')) { // ����ʬ���������ܤν���          
          var area = className.match(/area(\d+)/);
          var divSubCol = getColumnForRowAndClassName(row, 'divisionSubject', cellClass);
          var value = cellValue[row][divSubCol];
          cellProperties.type = 'dropdown';
          cellProperties.source = dropdownClsItm[Number(area[1])][value];

        } else if (className.includes('customerCompany')) { // �ܵ���ʻ�����ˤν���        
          var area = className.match(/area(\d+)/);
          if (Number(area[1]) === 1 || Number(area[1]) === 2) { // ����
            cellProperties.type = 'dropdown';
            cellProperties.source = dropdownCompany[2];

          } else if (Number(area[1]) === 3 || Number(area[1]) === 4) { // ȯ��
            cellProperties.type = 'dropdown';
            cellProperties.source = dropdownCompany[3];

          } else if (Number(area[1]) === 5) { 
            cellProperties.type = 'numeric';
            cellProperties.numericFormat = {
              pattern: '0.00%',
            };
          }
        } else if (className.includes('monetaryDisplay')) { // �̲�
          cellProperties.type = 'dropdown';
          cellProperties.source = ['', 'JP', 'US', 'HK'];

        } else if (className.includes('payoff')) { // ����          
          cellProperties.type = 'dropdown';
          cellProperties.source = ['', '��'];

        } else if (className.includes('delivery')) { // Ǽ��          
          cellProperties.type = 'date';
          cellProperties.dateFormat = 'YYYY/MM/DD'

        } else if (className.includes('price')) { // ñ��
          cellProperties.type = 'numeric';
          cellProperties.numericFormat = {
            pattern: '#,##0.0000',
          };
        } else if (className.includes('quantity')) { // ����
          cellProperties.type = 'numeric';
          cellProperties.numericFormat = {
            pattern: '#,##0',
          };
        } else if (className.includes('conversionRate')) { // Ŭ�ѥ졼��
          cellProperties.type = 'numeric';
          cellProperties.numericFormat = {
            pattern: '#,##0.0000',
          };
        }
      }
    }
    
    // renderer�򥻥åȤ���ʥ���η������طʿ����ν����ꡢ���Ϸ����λ�������
    cellProperties.renderer = firstRenderer;

    // �ɤ߼�����ѥ�������ꤹ��
    var readOnlyRow = readOnlyDetailRow.some(function(value){
      return value = row;
    })
    
    if (readOnlyDetailRow.indexOf(row) > -1) {
      cellProperties.readOnly = true;
    } else {
      cellProperties.readOnly = cellData[row][col]['readOnly'];
    }    
    
    return cellProperties;
  }


  
  //------------------------------------(Renderer������)-----------------------------------------
  
  /**
   * renderer������
   * 
   * @param {integer} row ���ֹ�
   * @param {integer} col ���ֹ�
   * 
   */
  function firstRenderer(instance, td, row, col, prop, value, cellProperties) {
    // ���Ѥ���renderer�λ�������Ϸ�������
    if (cellProperties.type === 'dropdown') { // �ɥ�åץ�����
      Handsontable.renderers.DropdownRenderer.apply(this, arguments);
    } else if (cellProperties.type === 'date') { // ���շ�
      Handsontable.renderers.DateRenderer.apply(this, arguments);
    } else if (cellProperties.type === 'numeric') { // ���ͷ�
      Handsontable.renderers.NumericRenderer.apply(this, arguments);
    } else { // �ä˶������꤬�ʤ���Хƥ�����(ʸ����˷�
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

  // handsontable������ܥ���ν���
  

  // ���򥻥�μ���
  function getSelectedCell() {
    var selected = table[0].getSelected();
    if (selected) {
      return selected[0];
    }
    return false;
  }

  //-----------------------------------------------(�����)-----------------------------------------------------

  // ���ɲ�
  $('.btnRowAdd').on('click', function() {
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

          var newReadOnly = readOnlyDetailRow.map(function(value) {
            if (value >= selectedRow) {
              value = value + 1;
            }
            return value;
          });       

          var copyDetailNoList = JSON.parse(JSON.stringify(detailNoList));


          detailNoList = copyDetailNoList.map(function(value) {
            if (value.row >= selectedRow) {
              value.row += 1;
            }
            return value;
          });

          insertDetailNo = {row: selectedRow, estimateDetailNo: null}
          detailNoList.push(insertDetailNo);

          console.log(detailNoList);
  
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
          alert('���ٹ԰ʳ��ι��ɲäϤǤ��ޤ���');
        }
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

      var checkList = cellClass;

      var selectedCell = getElementsForRowAndColumn(selectedRow, selectedColumn, checkList);

      if (isEmpty(selectedCell) === false) {
        if (selectedCell[0].className.includes('detail')) {
          var area = selectedCell[0].className.match(/area(\d+)/);
          var areaClassName = 'detail ' + area[0] + ' divisionSubject';
  
          var elements = getElementsForClassName(areaClassName, checkList);
  
          if (readOnlyDetailRow.includes(selectedRow)) {
            // ����ѤιԤϺ���Ǥ��ʤ��褦�ˤ���
            alert('����Ѥ����٤Ϻ���Ǥ��ޤ���');
  
          } else if (elements.length > 1) {
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
  
            var newReadOnly = readOnlyDetailRow.map(function(value) {
              if (value > selectedRow) {
                value = value - 1;
              }
              return value;
            });
            
            var copyDetailNoList = JSON.parse(JSON.stringify(detailNoList));

            selectedKey = null;

            detailNoList = copyDetailNoList.map(function(value, key) {
              if (value.row > selectedRow) {
                value.row -= 1;
              } else if (value.row === selectedRow) {
                selectedKey = key;
              }
              return value;
            });
  
            detailNoList.splice(selectedKey, 1);

            console.log(detailNoList);
  
            merge = newMerge;
            cellClass = newCellClass;
            readOnlyDetailRow = newReadOnly;
  
            rowHeight.splice(selectedRow, 1);
  
            // �׻��ե饰������
  
            var calcFlag = {};
  
            // �Ʒ׻��ե饰������
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
            alert('�������ꥢ�����ٹԤ�1�԰ʲ��Τ���Ժ���Ǥ��ޤ���');
          }
        } else {
          alert('���ٹ԰ʳ��κ���ϤǤ��ޤ���');
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
              } else if (!maxRow || (maxRow && maxRow < rowNumber)){
                maxRow = rowNumber;
              }
            }
            var minRowCellValue = cellValue.splice(selectedRow, 1);
            cellValue.splice(minRow, 0, minRowCellValue[0]);
  
            // readOnly��񤭴�����
            var newReadOnly = readOnlyDetailRow.map(function(value){
              if (value == selectedRow) {
                return minRow;
              } else if (value > minRow && value <= selectedRow) {
                return value + 1;
              }
            });
  
            readOnlyDetailRow = newReadOnly;

            var copyDetailNoList = JSON.parse(JSON.stringify(detailNoList));

            detailNoList = copyDetailNoList.map(function(value) {
              if (value.row < selectedRow) {
                value.row += 1;
              } else if (value.row === selectedRow) {
                value.row = minRow;
              }
              return value;
            });
  
            // ���Υ���ǡ����򹹿�����
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
          alert('���ٹ԰ʳ��ΰ�ư�ϤǤ��ޤ���');
        }
      } else {
        alert('���ٹ԰ʳ��ΰ�ư�ϤǤ��ޤ���');
      }

      table[0].selectCell(minRow, selectedRange[1]);
    }
  });

  // �԰�ư(���ꥢ��Ǹ�����)
  $('.btnMoveBottom').on('click', function() {
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
              } else if (!maxRow || (maxRow && maxRow < rowNumber)){
                maxRow = rowNumber;
              }
            }
            var maxRowCellValue = cellValue.splice(selectedRow, 1);
            cellValue.splice(maxRow, 0, maxRowCellValue[0]);
  
            // readOnly��񤭴�����
            var newReadOnly = readOnlyDetailRow.map(function(value) {
              if (value == selectedRow) {
                return maxRow;
              } else if (value < maxRow && value >= selectedRow) {
                return value - 1;
              }
            });
  
            readOnlyDetailRow = newReadOnly;

            var copyDetailNoList = JSON.parse(JSON.stringify(detailNoList));

            detailNoList = copyDetailNoList.map(function(value) {
              if (value.row > selectedRow) {
                value.row -= 1;
              } else if (value.row === selectedRow) {
                value.row = maxRow;
              }
              return value;
            });
  
            // ���Υ���ǡ����򹹿�����
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
          alert('���ٹ԰ʳ��ΰ�ư�ϤǤ��ޤ���');
        }

      } else {
        alert('���ٹ԰ʳ��ΰ�ư�ϤǤ��ޤ���');
      }


      table[0].selectCell(maxRow, selectedRange[1]);
    }
  });

  // �԰�ư(��Ծ��)
  $('.btnMoveUpper').on('click', function() {
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
            
            if(selectedRow != minRow) {
              var insertRow = selectedRow - 1;
              var movecellValue = cellValue.splice(selectedRow, 1);
              cellValue.splice(insertRow, 0, movecellValue[0]);
  
              // readOnly��񤭴�����
              var newReadOnly = readOnlyDetailRow.map(function(value) {
                if (value == selectedRow) {
                  return selectedRow - 1;
                } else if (value == selectedRow + 1) {
                  return selectedRow;
                }
              });
  
              readOnlyDetailRow = newReadOnly;

              var copyDetailNoList = JSON.parse(JSON.stringify(detailNoList));

              detailNoList = copyDetailNoList.map(function(value) {
                if (value.row === selectedRow - 1) {
                  value.row = selectedRow;
                } else if (value.row === selectedRow) {
                  value.row -= 1;
                }
                return value;
              });
  
              // ���Υ���ǡ����򹹿�����
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

              console.log(cellValue);
            }
          }
  
        } else {
          alert('���ٹ԰ʳ��ΰ�ư�ϤǤ��ޤ���');
        }
      } else {
        alert('���ٹ԰ʳ��ΰ�ư�ϤǤ��ޤ���');
      }

      var unlock = screenUnlock();
      table[0].selectCell(insertRow, selectedRange[1]);
    }
  });



  // �԰�ư(��Բ���)
  $('.btnMoveLower').on('click', function() {
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
  
              // readOnly��񤭴�����
              var newReadOnly = readOnlyDetailRow.map(function(value){
                if (value == selectedRow) {
                  return selectedRow + 1;
                } else if (value == selectedRow - 1) {
                  return selectedRow;
                }
              });
  
              readOnlyDetailRow = newReadOnly

              var copyDetailNoList = JSON.parse(JSON.stringify(detailNoList));

              detailNoList = copyDetailNoList.map(function(value) {
                if (value.row === selectedRow + 1) {
                  value.row = selectedRow;
                } else if (value.row === selectedRow) {
                  value.row += 1;
                }
                return value;
              });
  
              // ���Υ���ǡ����򹹿�����
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

              console.log(cellData);
  
            }
          }
  
        } else {
          alert('���ٹ԰ʳ��ΰ�ư�ϤǤ��ޤ���');
        }
      } else {
        alert('���ٹ԰ʳ��ΰ�ư�ϤǤ��ޤ���');
      }


      table[0].selectCell(insertRow, selectedRange[1]);
    }

    return;
  });


  //---------------------------------------(����κƷ׻�����)------------------------------------------

  /**
   * ����������ͤ��ѹ����줿���ν�����Ԥ�
   * @param {integer} row �ѹ�����ι�
   * @param {integer} col �ѹ��������
   * @param {integer} oldValue �ѹ�������
   * @param {integer} newValue �ѹ������
   * 
   */
  function onChangedValue(row, col, oldValue, newValue) {
    var checkList = cellClass;

    var elements = getElementsForRowAndColumn(row, col, checkList);

    var calcFlag = {};

    var className = elements[0].className;
    if (className === 'inchargegroupcode') { // �Ķ�����
      // ô���Υ���μ���
      for (var i = 0; i < checkList.length; i++) {
        if (checkList[i].className == 'inchargeusercode') {
          var userRow = cellClass[i].row;
          var userCol = cellClass[i].col;
          break;
        }
      }
      // ô�������ˤ���
      assignValueForGlobal(userRow, userCol, ''); 
      
    } else if (className.includes('detail')) { // ���ٹ�

      // ����Υ��饹�������
      var cellElement = getElementsForRowAndColumn(row, col, checkList);

      // ���ꥢ�����ɤμ���
      var areaCode = cellElement[0].className.match(/area([0-9]+)/);


      if (className.includes('divisionSubject')) { // ���ʬ������ϻ������ܡ�
        var clsItmCol = getColumnForRowAndClassName(row, 'classItem', checkList);

        // ����ʬ�����ϻ������ʡˤ����ˤ���
        assignValueForGlobal(row, clsItmCol, '');

        var cusComCol = getColumnForRowAndClassName(row, 'customerCompany', checkList);

        if (isNumber(cellValue[row][cusComCol]) === true) { // �������Ϥ���Ƥ�����
          assignValueForGlobal(row, cusComCol, '');  // �ܵ���ʻ�����ˤ򥯥ꥢ

          var priceCol = getColumnForRowAndClassName(row, 'price', checkList);
          assignValueForGlobal(row, priceCol, '');  // ñ���򥯥ꥢ
        }

        if (Number(areaCode[1]) === 1) {
          calcFlag.productionQuantity = true;
        }

        if (Number(areaCode[1]) === 4 || Number(areaCode[1]) === 5) {
          calcFlag.substitutePQ = true;
        }

        calcFlag.subtotal = true;

      } else if (className.includes('classItem')) { // ����ʬ�ʻ�������)
        var cusComCol = getColumnForRowAndClassName(row, 'customerCompany', checkList);

        if (isNumber(cellValue[row][cusComCol]) === true) { // ������˿������Ϥ���Ƥ�����(�ѡ�����Ȥξ��)
          var divSubCol = getColumnForRowAndClassName(row, 'divisionSubject', checkList);
          var divSub = cellValue[row][divSubCol];
          if (divSub.match(chargeSubjectPattern)) { // �������ܤ����㡼���ξ��
            if (oldValue.match(importOrTariffPattern) && !newValue.match(importOrTariffPattern)) { // �ѹ������ͤ�͢���������ϴ��Ǥ��ѹ�����ͤ�����ʳ����ͤξ��
                assignValueForGlobal(row, cusComCol, '');  // �ܵ���ʻ�����ˤ򥯥ꥢ

                var priceCol = getColumnForRowAndClassName(row, 'price', checkList);
                assignValueForGlobal(row, priceCol, '');  // ñ���򥯥ꥢ

                calcFlag.subtotal = true;                            
            } else {
              calcFlag.importOrTariff = true // ͢�����ѡ����ǤκƷ׻��ե饰
            }
          }
        }
        
        if (oldValue == '' || newValue == '') {
          calcFlag.subtotal = true;
        }

        if (Number(areaCode[1]) === 1) {
          calcFlag.productionQuantity = true;
        }

        if (Number(areaCode[1]) === 4 || Number(areaCode[1]) === 5) {
          calcFlag.substitutePQ = true;
        }
  
      } else if (className.includes('customerCompany')) { // �ܵ���ʻ������
        if (Number(areaCode[1]) === 5) {
          calcFlag.subtotal = true;
        }

      } else if (className.includes('quantity')) {
        calcFlag.subtotal = true;

        setQuantityCalculateFlag(Number(areaCode[1]), calcFlag, 'quantity');

      } else if (className.includes('price')) {
        calcFlag.subtotal = true;

      } else if (className.includes('conversionRate')) {
        calcFlag.subtotal = true;
      } else if (className.includes('payoff')) { // ����
        if (Number(areaCode[1]) === 3) {
          calcFlag.depreciation = true;
        } else if (Number(areaCode[1]) === 4 || Number(areaCode[1]) === 5) {
          calcFlag.member = true;
          calcFlag.depreciation = true;
        }
      }

      // ���׺Ʒ׻����ν���
      if (calcFlag.subtotal === true) {
        setCalcFlagForChangeSubtotal(Number(areaCode[1]), calcFlag)
      }

      calculate(calcFlag, row);
    }
    return;
  };

  // ���פ��Ѥ�ä����κƷ׻��ե饰������
  function setCalcFlagForChangeSubtotal(areaCode, calcFlag) {
    if (areaCode === 1) {
      calcFlag.area1TotalPrice = true;
    } else if (areaCode === 2) {
      calcFlag.area2TotalPrice = true;
    } else if (areaCode === 3) {
      calcFlag.area3TotalCost = true;
      calcFlag.area3NotDepreciationCost = true;
      calcFlag.depreciation = true;
    } else if (areaCode === 4 || areaCode === 5) {
      calcFlag.member = true;
      calcFlag.depreciation = true;
    }
    calcFlag.importOrTariff = true;
    return;
  }

  // ����η׻��ؿ�
  function calculate(calcFlag, row = null) {
    
    // �Ʒ׻��ե饰�ν����
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
      // �����Ϥ��ʤ����ϹԴ�Ϣ�ν�����Ԥ�ʤ��褦�ˤ���
      calcFlag.subtotal = false;
      calcFlag.substitutePQ = false;
    }

    // �إå���
    if (calcFlag.productionQuantity === true) { // ���ѿ�
      calProductionQuantity();
      calcFlag.member = true;
      calcFlag.depreciation = true;
      calcFlag.importOrTariff = true;
    }

    // ���ٹ�
    if (calcFlag.subtotal === true) { // ���ס����ѡ���������Ϥξ��ϥѡ���������ϤκƷ׻��ؿ�����ǸƤӽФ��Τ�����
      calculateSubtotal(row);
    }

    if (calcFlag.area1TotalQuantity === true) { // ��������סʿ��̡�
      calculateArea1TotalQuantity();
    }

    if (calcFlag.area1TotalPrice === true) { // ��������סʶ�ۡ�
      calculateArea1TotalPrice();
      calcProductProfit = true;
      calcSalesAmount = true;
    }

    if (calcFlag.area2TotalQuantity === true) { // ����������סʿ��̡�
      calculateArea2TotalQuantity();
    }
    
    if (calcFlag.area2TotalPrice === true) { // ����������סʶ�ۡ�
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

    if (calcFlag.importOrTariff === true) { // ͢�����ѵڤӴ��Ǥ�ñ��������
      calculateImportOrTariffRows();
    }

    if (calcFlag.member === true) { // ������
      calculateMemberCost();
      calcMemberUnit = true;
      calcManufacturing = true;
    }

    if (calcFlag.depreciation === true) { // ������
      calculateDepreciationCost();
      calcDepreciationUnit = true;
      calcManufacturing = true;
    }
    
    if (calcManufacturing === true) { // ��¤����
      calculateManufacturingCost();
      calcManufacturingUnit = true;
      calcProductProfit = true;
    }

    if (calcMemberUnit === true) { // pcs��������
      calculateMemberUnitCost();
    }

    if (calcDepreciationUnit === true) { // pcs��������
      calculateDepreciationUnitCost();
    }
    
    if (calcManufacturingUnit === true) { // pcs������
      calculateManufacturingUnitCost();
    }
    
    if (calcProductProfit === true) { // �������ס���������Ψ
      calculateProductProfit();
      calcProfit = true;
    }

    if (calcFixedCostProfit === true) { // ���������ס�����������Ψ
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
  
  // ���ѿ��κƷ׻�
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
      var mainProductName = '1:�ܲ�';
      if (cellValue[classRow][classCol] == mainProductName) {
        var quantityRow = quantityElements[i].row;
        var quantityCol = quantityElements[i].col;
        var quantity = Number(cellValue[quantityRow][quantityCol]);
        productionQuantity += quantity;
      }
    }

    var PQclass = 'productionquantity';
    var PQCell = getElementsForClassName(PQclass, checkList);

    // ���ѿ������ϥ�����֤����
    var PQRow = PQCell[0].row;
    var PQCol = PQCell[0].col;

    var beforePQ = cellValue[PQRow][PQCol];

    // �����Х��ѿ������
    assignValueForGlobal(PQRow, PQCol, productionQuantity);

    setQuantityForPartsAndOthers(beforePQ, productionQuantity);

    return;
  }

  // pcs��������
  function calculateMemberUnitCost() {
    var checkList = cellClass;

    var costCell = getElementsForClassName('membercost', checkList);
    var unitCell = getElementsForClassName('member_unit_cost', checkList);
    var quantityCell = getElementsForClassName('member_quantity', checkList);

    calculateUnitCost(costCell, unitCell, quantityCell)

    return;
  }

  // pcs��������
  function calculateDepreciationUnitCost() {
    var checkList = cellClass;

    var costCell = getElementsForClassName('depreciation_cost', checkList);
    var unitCell = getElementsForClassName('depreciation_unit_cost', checkList);
    var quantityCell = getElementsForClassName('depreciation_quantity', checkList);

    calculateUnitCost(costCell, unitCell, quantityCell)

    return;
  }

  // pcs������
  function calculateManufacturingUnitCost() {
    var checkList = cellClass;

    var costCell = getElementsForClassName('manufacturingcost', checkList);
    var unitCell = getElementsForClassName('manufacturing_unit_cost', checkList);
    var quantityCell = getElementsForClassName('manufacturing_quantity', checkList);

    calculateUnitCost(costCell, unitCell, quantityCell)

    return;
  }

  // �եå�����ñ���Ʒ׻�
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

    // �����Х��ѿ������
    assignValueForGlobal(unitRow, unitCol, unit);

    return;
  }

  // ������ڤӤ���¾���Ѥο�������
  function setQuantityForPartsAndOthers(beforePQ, productionQuantity = null) {
    var checkList = cellClass;

    if (productionQuantity === null) {
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

        if (beforePQ == cellValue[row][quaCol]) { // ���̤��ѹ����ν��ѿ��Ȱ��פ����������
          cellValue[row][quaCol] = productionQuantity;
          calculateSubtotal(row);
        }
      }
    }

    // ���ꥢ5�ν���
    var areaClass = 'area5';
    var subjectClass = 'detail ' + areaClass + ' divisionSubject';
    var itemClass = 'detail ' + areaClass + ' classItem';
    var quantityClass = 'detail ' + areaClass + ' quantity';

    var checkList = cellClass;

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
        if (beforePQ == cellValue[row][quaCol]) { // ���̤��ѹ����ν��ѿ��Ȱ��פ����������
          cellValue[row][quaCol] = productionQuantity;
          calculateSubtotal(row);
        }
      }
    }

    // �եå����ν���
    var MEQcell = getElementsForClassName('member_quantity', checkList);
    cellValue[MEQcell[0].row][MEQcell[0].col] = productionQuantity;

    var DQcell = getElementsForClassName('depreciation_quantity', checkList);
    cellValue[DQcell[0].row][DQcell[0].col] = productionQuantity;

    var MAQcell = getElementsForClassName('manufacturing_quantity', checkList);
    cellValue[MAQcell[0].row][MAQcell[0].col] = productionQuantity;

    return;
  }

  // ���ٹԤο��̤˽��ѿ�����������
  function substitutePQForDetailQuantity(row) {
    var checkList = cellClass;

    var subCol = getColumnForRowAndClassName(row, 'divisionSubject', checkList);
    var iteCol = getColumnForRowAndClassName(row, 'classItem', checkList);
    var quaCol = getColumnForRowAndClassName(row, 'quantity', checkList);

    if (cellValue[row][subCol] && cellValue[row][iteCol]) {
      // �������ܤȻ������ʤ�ξ�����Ϥ���Ƥ�����Ͽ��̤˽��ѿ��򥻥åȤ���
      var PQElement = getElementsForClassName('productionquantity', checkList);
      var PQRow = PQElement[0].row;
      var PQCol = PQElement[0].col;

      var productionQuantity = cellValue[PQRow][PQCol];

      cellValue[row][quaCol] = productionQuantity;
    } else {
      cellValue[row][quaCol] = '';
    }
    calculateSubtotal(row);
  }

  // �оݥ��ꥢ�ι�פν���
  function setQuantityCalculateFlag(areaCode, calcFlag) {
    if (areaCode === 1) {
      // ��������סʿ��̡ˤκƷ׻�
      calcFlag.area1TotalQuantity = true;

      // ���ѿ��κƷ׻�
      calcFlag.productionQuantity = true;

    } else if (areaCode === 2) {
      // ����������סʿ��̡ˤκƷ׻�
      calcFlag.area2TotalQuantity = true
    }
    return;
  }

   /**
   * ���ٹԤξ��סʤޤ��Ϸײ踶���ˤκƷ׻���Ԥ�
   * 
   */
  function calculateSubtotal(row) {
    var checkList = cellClass;
    var divSubCol = getColumnForRowAndClassName(row, 'divisionSubject', checkList);
    var clsItemCol = getColumnForRowAndClassName(row, 'classItem', checkList);
    var divSub = cellValue[row][divSubCol];
    var clsItm = cellValue[row][clsItemCol];

    var subtotalCol = getColumnForRowAndClassName(row, 'subtotal', checkList);

    if (divSub !=='' && clsItm !== '') {
      var quantityCol = getColumnForRowAndClassName(row, 'quantity', checkList);
      var priceCol = getColumnForRowAndClassName(row, 'price', checkList);
      var rateCol = getColumnForRowAndClassName(row, 'conversionRate', checkList);
      var quantity = Number(cellValue[row][quantityCol]);
      var price = Number(cellValue[row][priceCol]);
      var rate = Number(cellValue[row][rateCol]);
      var subtotal = quantity * price * rate;
      subtotal = '\xA5' + numberFormat(subtotal, 0);
    } else {
      var subtotal = '';
    }

    // �ͤ�����
    assignValueForGlobal(row, subtotalCol, subtotal);

    return subtotal;
  }
  
  
   /**
   * ��������סʿ��̡ˤκƷ׻���Ԥ�
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

    // ����޶��ڤ�ʸ������Ѵ�
    totalQuantity = numberFormat(totalQuantity, 0);

    assignValueForClassNameCell(totalQuantityClassName, totalQuantity);
  }


   /**
   * ��������סʶ�ۡˤκƷ׻���Ԥ�
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
      // ��������ͤ���ͤ��Ѵ�����
      subtotal = Number(cellValue[row][col].replace('\xA5', '').split(',').join(''));
      totalPrice += subtotal;
    }

    var totalPriceClassName = 'receive_p_totalprice'; // ��������סʶ�ۡˤΥ���̾��
    var productTotalPriceClassName = 'product_totalprice'; // ��������Υ���̾��

    // �׻���̤򥫥�޶��ڤꡢ�ߥޡ����դ��ν񼰤��Ѵ�
    totalPrice = totalPrice != 0 ? '\xA5' + numberFormat(totalPrice, 0) : '';

    // �ͤ�����
    assignValueForClassNameCell(totalPriceClassName, totalPrice);
    assignValueForClassNameCell(productTotalPriceClassName, totalPrice);
  }

   /**
   * ����������סʿ��̡ˤκƷ׻���Ԥ�
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

    // ����޶��ڤ�ʸ������Ѵ�
    totalQuantity = numberFormat(totalQuantity, 0);

    assignValueForClassNameCell(totalQuantityClassName, totalQuantity);
  }

  /**
   * ����������סʶ�ۡˤκƷ׻���Ԥ�
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

    var totalPriceClassName = 'receive_f_totalprice'; // ����������סʶ�ۡˤΥ���̾��
    var fixedTotalPriceClassName = 'fixedcost_totalprice'; // ����������Υ���̾��

    totalPrice = totalPrice != 0 ? '\xA5' + numberFormat(totalPrice, 0) : '';

    // �ͤ�����
    assignValueForClassNameCell(totalPriceClassName, totalPrice);
    assignValueForClassNameCell(fixedTotalPriceClassName, totalPrice);
  }

  /**
   * �����񾮷פκƷ׻���Ԥ�
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
   * �����оݳ����פκƷ׻���Ԥ�
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
      if (cellValue[row][payCol] != '��') {
        subtotal = Number(cellValue[row][subCol].replace('\xA5', '').split(',').join(''));
        totalCost += subtotal;
      }
    }

    var totalCostClassName = 'order_f_cost_not_depreciation'; // �����оݳ����פΥ���̾��
    var costNotDepClassName = 'cost_not_depreciation'; // �����оݳ�������Υ���̾��

    totalCost = totalCost != 0 ? '\xA5' + numberFormat(totalCost, 0) : '';

    // �ͤ�����
    assignValueForClassNameCell(totalCostClassName, totalCost);
    assignValueForClassNameCell(costNotDepClassName, totalCost);
  }

  /**
   * ͢�����ѵڤӴ��Ǥ�ñ�������פ�׻�����ʥѡ���������Ϥ���Ƥ�����˻��ѡ�
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
      if (areaCode = checkList[i].className.match(subjectClassNamePattern)) { // �������ܹԤθ���
        checkRow = checkList[i].row;
        checkCol = checkList[i].col;
        if (cellValue[checkRow][checkCol].match(tariffTargetPattern)) { // �������ܤ��ͤ�����оݤξ��
          ItemCol = getColumnForRowAndClassName(checkRow, 'classItem', checkList);
          if (cellValue[checkRow][ItemCol]) { // �������ʤζ��ͥ����å�
            subtotalCol = getColumnForRowAndClassName(checkRow, 'subtotal', checkList);
            sum += Number(cellValue[checkRow][subtotalCol].replace('\xA5', '').split(',').join(''));
          }
        }
      }
    }

    // ����¾���Ѥλ������ܥ���ξ�������
    var subjectClassName = 'detail area5 divisionSubject';
    var subjectElements = getElementsForClassName(subjectClassName, checkList);

    var importRows = [];
    var tariffRows = [];

    // ͢�����ѡ����Ǥ����ϹԼ���
    for (var i = 0; i < subjectElements.length; i++) {
      var targetRow = subjectElements[i].row;
      var subjectCol = subjectElements[i].col;
      if (cellValue[targetRow][subjectCol].match(chargeSubjectPattern)) { // ���㡼���ξ��
        var itemCol = getColumnForRowAndClassName(targetRow, 'classItem', checkList);
        if (cellValue[targetRow][itemCol].match(importItemPattern)) { // ͢�����Ѥξ��
          importRows.push(targetRow);
        } else if (cellValue[targetRow][itemCol].match(tariffItemPattern)) { // ���Ǥξ��
          tariffRows.push(targetRow);
        }       
      }
    }

    // ���ǹԤν���
    var tariffTotal = tariffRows.reduce(function(total, row) {
      var percentCol = getColumnForRowAndClassName(row, 'customerCompany', checkList);
      var percent = (isNaN(cellValue[row][percentCol]) === false) ? Number(cellValue[row][percentCol]) / 100 : 0;

      if (percent) { // �ѡ���������Ϥ���Ƥ������ñ���η׻�
        var quantityCol = getColumnForRowAndClassName(row, 'quantity', checkList);
        var priceCol = getColumnForRowAndClassName(row, 'price', checkList);
        var quantity = (isNaN(cellValue[row][quantityCol]) === false) ? Number(cellValue[row][quantityCol]) : 0;
        if (quantity) {
          var price = Math.floor((sum * percent / quantity) * 10**4) / 10**4;
        } else {
          var price = '';
        }
        // ñ��������
        assignValueForGlobal(row, priceCol, price);
      }

      // ���פ�����������
      var subtotal = calculateSubtotal(row);
      subtotal = Number(subtotal.replace('\xA5', '').split(',').join(''));

      return total + subtotal;
      
    }, sum);

    // ͢�����ѹԤν���
    importRows.forEach(function(row) {
      var percentCol = getColumnForRowAndClassName(row, 'customerCompany', checkList);
      var percent = (isNaN(cellValue[row][percentCol]) === false) ? Number(cellValue[row][percentCol]) / 100 : 0;

      if (percent) { // �ѡ���������Ϥ���Ƥ������ñ���η׻�
        var quantityCol = getColumnForRowAndClassName(row, 'quantity', checkList);
        var priceCol = getColumnForRowAndClassName(row, 'price', checkList);
        var quantity = (isNaN(cellValue[row][quantityCol]) === false) ? Number(cellValue[row][quantityCol]) : 0;
        if (quantity) {
          var price = Math.floor((tariffTotal * percent / quantity) * 10**4) / 10**4;
        } else {
          var price = '';
        }
        // ñ��������
        assignValueForGlobal(row, priceCol, price);
      }

      // ���פκƷ׻�������
      calculateSubtotal(row);
    });

    return;
  }

  /**
   * ������κƷ׻���Ԥ�
   * 
   */
  function calculateMemberCost() {
    var checkList = cellClass;
    var targetClassNamePattern = RegExp('detail area(4|5) subtotal');

    var total = checkList.reduce(function(sum, value) {
      var add = 0;
      if (value.className.match(targetClassNamePattern)) {
        var targetRow = value.row;
        var payoffCol = getColumnForRowAndClassName(targetRow, 'payoff', checkList);
        if (cellValue[targetRow][payoffCol] !== '��') {
          add = Number(cellValue[targetRow][value.col].replace('\xA5', '').split(',').join(''));
        }
      }
      return sum + add;
    }, 0);

    // ������
    var total = '\xA5' + numberFormat(total, 0);

    // �����������
    assignValueForClassNameCell('membercost', total);

    return;
  }


  /**
   * ������κƷ׻���Ԥ�
   * 
   */
  function calculateDepreciationCost() {
    var checkList = cellClass;
    var targetClassNamePattern = RegExp('detail area(3|4|5) subtotal');

    var total = checkList.reduce(function(sum, value) {
      var add = 0;
      if (value.className.match(targetClassNamePattern)) {
        var targetRow = value.row;
        var payoffCol = getColumnForRowAndClassName(targetRow, 'payoff', checkList);
        if (cellValue[targetRow][payoffCol] === '��') {
          add = Number(cellValue[targetRow][value.col].replace('\xA5', '').split(',').join(''));
        }
      }
      return sum + add;
    }, 0);

    // ������
    total = '\xA5' + numberFormat(total, 0);

    // �����������
    assignValueForClassNameCell('depreciation_cost', total);
    
    return;
  }

  /**
   * ��¤���ѤκƷ׻���Ԥ�
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

    // ������
    manufacturingCost = '\xA5' + numberFormat(manufacturingCost, 0);

    // ��¤�������
    assignValueForClassNameCell('manufacturingcost', manufacturingCost);

    return;
  }

  /**
   * �������פ���������Ψ�κƷ׻���Ԥ�
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

    // ������
    profit = '\xA5' + numberFormat(profit, 0);
    profitRate = numberFormat(profitRate * 100, 2, '') + '%';

    // �������פ�����
    assignValueForClassNameCell('product_profit', profit);

    // ��������Ψ������
    assignValueForClassNameCell('product_profit_rate', profitRate);

    return;
  }
  
  /**
   * ���������פȸ���������Ψ�κƷ׻���Ԥ�
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

    // ������
    profit = '\xA5' + numberFormat(profit, 0);
    profitRate = numberFormat(profitRate * 100, 2, '') + '%';

    // ���������פ�����
    assignValueForClassNameCell('fixedcost_profit', profit);

    // ����������Ψ������
    assignValueForClassNameCell('fixedcost_profit_rate', profitRate);

    return;
  }


  /**
   * �����⡢������¤����κƷ׻���Ԥ�
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

    // ������
    salesAmount = '\xA5' + numberFormat(salesAmount, 0);
    indirectCost = '\xA5' + numberFormat(indirectCost, 0);

    // �����������
    assignValueForClassNameCell('salesamount', salesAmount);

    // ������¤���������
    assignValueForClassNameCell('indirect_cost', indirectCost);

    return;
  }


  /**
   * ��������פκƷ׻���Ԥ�
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

    // ������
    profit = '\xA5' + numberFormat(profit, 0);

    // ��������פ�����
    assignValueForClassNameCell('profit', profit);

    return;
  }

  /**
   * �Ķ����ס��Ķ�����Ψ������������Ψ�κƷ׻���Ԥ�
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

    // ������
    opeProfit = '\xA5' + numberFormat(opeProfit, 0);
    opeProfitRate = numberFormat(opeProfitRate * 100, 2, '') + '%';
    profitRate = numberFormat(profitRate * 100, 2, '') + '%';

    // �Ķ����פ�����
    assignValueForClassNameCell('operating_profit', opeProfit);

    // �Ķ�����Ψ������
    assignValueForClassNameCell('operating_profit_rate', opeProfitRate);

    // ����������Ψ������
    assignValueForClassNameCell('profit_rate', profitRate);

    return;
  }

  //----------------------------------------(���ܽ����ؿ�)-----------------------------------------------

  /**
   * ����Υ��饹̾����ĥ�����ͤ���������
   * 
   * @param {string} className ���饹̾
   * @param value �������륻�����
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
   * ������ͤ򥰥��Х��ѿ��Υ����ͥ��֥������Ȥ���������
   * �� cellVAlue�ڤ�cellData�����ե�����������Υ����Х��ѿ��Ȥ��ƻ���
   * 
   * @param {integer} row ���������
   * @param {integer} col ����������
   * @param value �������륻�����
   * 
   */
  function assignValueForGlobal(row, col, value) {
    cellValue[row][col] = value;
    cellData[row][col]['value'] = value;
    return;
  }
  
  /**
   * �ͤ������ɤ���Ƚ���Ԥ�
   * 
   * @param value Ƚ�ꤹ����
   * 
   * @return {boolean} ��Ƚ���̡�true:�� false:���Ǥʤ���
   */
  function isEmpty(value){
    if (!value) {  //null or undefined or ''(��ʸ��) or 0 or false
        if (value!== 0 && value !== false) {
            return true;
        }
    } else if( typeof value == "object") {  //array or object
        return Object.keys(value).length === 0;
    }
      return false;  //�ͤ϶��ǤϤʤ�
  }

    /**
   * �ͤ����ͷ����ɤ���Ƚ���Ԥ�
   * 
   * @param value Ƚ�ꤹ����
   * 
   * @return {boolean} Ƚ����
   */
  function isNumber(value) {
    return ((typeof value === 'number') && (isFinite(value)));
  }

     /**
   * ������ʸ����򥫥�޶��ڤ�ˤ���
   * 
   * @param {string} strDecimal �Ѵ��������
   * @param {string} thousands_sep 3�头�Ȥζ��ڤ�ʸ��(default = ',')
   * 
   * @return {string} �������줿ʸ����
   */
  function commaSeparate(strDecimal, thousands_sep = ',') {
    var afterPoint = String(strDecimal).match(/\.\d+$/);
    if (afterPoint) {
      return String(strDecimal).replace(afterPoint[0], '').replace( /(\d)(?=(\d\d\d)+(?!\d))/g, '$1,') + afterPoint;
    } else {
      return String(strDecimal).replace( /(\d)(?=(\d\d\d)+(?!\d))/g, '$1' + thousands_sep);
    }
  }

   /**
   * ����޶��ڤꡢ������ɽ������ˡ�ˤĤ��ƻ��ꤹ��
   * 
   * @param {integer} num �Ѵ��������
   * @param {integer} decimals �������ʲ��η��(default = 0)
   * @param {string} thousands_sep 3�头�Ȥζ��ڤ�ʸ��(default = ',')
   * 
   * @return {string} �������줿ʸ����
   */
  function numberFormat(num, decimals = 0, thousands_sep = ',') {
    if (!isNaN(num)) {
      var number = Number(num);
      return commaSeparate(number.toFixed(decimals), thousands_sep);
    }
    return false;
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

  /**
   * ���ꤷ���Ԥ��������Υ��饹̾���������������
   * @param {integer} row �����
   * @param {integer} className ���饹̾
   * 
   * @return {Array.object} col ���ֹ�
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

  /**
   * Ŭ�ѥ졼�Ȥ��������
   * @param {integer} row �����
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
      data:{
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
  
    }).fail(function (xhr,textStatus,errorThrown) {
      alert('DB���顼');
    });
  }

  function setConversionRate(row, conversionRate) {
    var checkList = cellClass;
    
    if (!conversionRate) {
      alert('Ŭ�ѥ졼�Ȥ�����Ǥ��ޤ���Ǥ�����');
    }

    var rateCol = getColumnForRowAndClassName(row, 'conversionRate', checkList);

    assignValueForGlobal(row, rateCol, conversionRate);

    return;
  }

  // ---------------------------------------------�ǡ����������к�--------------------------------------------------

  // ���̤Υ�å��ؿ�
  function screenLock() {
    // ��å��Ѥ�div������
    var element = document.createElement('div'); 
    element.id = "screenLock";
    
    // ��å��ѤΥ�������
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
    //     // ��å����̤κ��
    //     screenUnlock();
    // }, 3000 );

    return true;
  }
 
  // ���̤Υ����å��ؿ�
  function screenUnlock() {
      var dom_obj = document.getElementById('screenLock');
      if (dom_obj) {
        var dom_obj_parent=dom_obj.parentNode;
        dom_obj_parent.removeChild(dom_obj);
      } else {
        return false;
      }
      return true;
  }

  // �Խ���¸����
  $('#update_regist').on('click', function() {
    if (window.confirm('�Խ����Ƥ���¸���ƥץ�ӥ塼���̤���ɤ߹��ߤ��ޤ���������Ǥ�����')){
      var postData = {
        value: cellValue,
        class: cellClass,
        estimateDetailNo: detailNoList
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
        'scrollbars=yes, width=' + winWidth + ', height=' + winHeight +', top=' + y + ', left=' + x + 'resizable=0 location=0'
      );

      form.attr('action', '/estimate/preview/update.php');
      form.attr('target', windowName);
      // ���֥ߥå�
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
});







