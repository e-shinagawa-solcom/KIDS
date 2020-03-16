
var script = $('#script').attr('data-param');
var result = JSON.parse(script);
$('#script').remove();

var sheetNumber = Object.keys(result).length;

var grid = [];
var table = [];
var startRow = [];
var endRow = [];
var startColumn = [];
var endColumn = [];
var cellData = [];
var cellValue = [];
var merge = [];
var rowHeight = [];
var columnWidth = [];
var cellClass = [];
var gridId = [];
var table = [];
var data = [];
var cellValue = [];
var hiddenCellValue = [];
var hiddenList = [];
var hiddenRowHeight = [];

// 表示用データの作成
for (var sNum = 0; sNum < sheetNumber; sNum++) {
  var sheetData = result[sNum];
  // 開始行列、終了行列の取得
  startRow = sheetData['startRow'];
  endRow = sheetData['endRow'];
  startColumn = sheetData['startColumn'];
  endColumn = sheetData['endColumn'];

  hiddenList[sNum] = sheetData['hiddenList'];

  // セルのデータ取得
  cellData[sNum] = sheetData['cellData'];

  cellValue[sNum] = [];
  hiddenCellValue[sNum] = [];

  // セルの情報を配列に格納する
  for (var i = startRow; i <= endRow; i++) {
    var rowValue = [];
    var hiddenRowValue = [];
    for (var j = startColumn; j <= endColumn; j++) {
      rowValue.push(cellData[sNum][i][j]['value']);
      if (hiddenList[sNum][i] !== true) {
        hiddenRowValue.push(cellData[sNum][i][j]['value']);
      } else {        
        hiddenRowValue.push('');
      }
    }
    cellValue[sNum].push(rowValue);
    hiddenCellValue[sNum].push(hiddenRowValue);
  }
  // マージセルの取得
  merge[sNum] = sheetData['mergedCellsList'];
  // 行の高さ、列の幅を取得
  rowHeight[sNum] = sheetData['rowHeight'];
  columnWidth[sNum] = sheetData['columnWidth'];
  hiddenRowHeight[sNum] = sheetData['hiddenRowHeight'];
  // セルのクラスを取得
  cellClass[sNum] = sheetData['cellClass'];

  gridId[sNum] = 'grid' + sNum;

  grid[sNum] = document.getElementById(gridId[sNum]);
}

viewFlag = setViewFlag(sheetNumber);

for (var sNum = 0; sNum < sheetNumber; sNum++) {
  table[sNum] = new Handsontable(grid[sNum], {
    data: hiddenCellValue[sNum],
    rowHeights: hiddenRowHeight[sNum],
    colWidths: columnWidth[sNum],
    cell: cellClass,
    cells: function (row, col, prop) {
      var cellProperties = {};
      var elements = getElementsForRowAndColumn(row, col, cellClass);

      // エリア5の%入力に書式を設定する
      if (!isEmpty(elements)) {
        var className = elements[0].className;
        if (className.includes('detail')) { 
          if (className.includes('payoff')) { // 償却 
            var area = className.match(/area(\d+)/);
            if (Number(area[1]) === 5) { 
              cellProperties.type = 'numeric';
              cellProperties.numericFormat = {
                pattern: '0.00%',
              };
            }
          }
        }
      }
      cellProperties.renderer = firstRenderer;
      return cellProperties;
    },
    wordWrap: false,
    readOnly: true,
    mergeCells: merge[sNum],
  });
}

// HandsontableのtdタグCSS
function firstRenderer(instance, td, row, col, prop, value, cellProperties) {
  var id = instance.rootElement.id;
  var number = id.replace('grid', '');
//  Handsontable.renderers.TextRenderer.apply(this, arguments);
  if (cellProperties.type === 'date') { // 日付型
    Handsontable.renderers.DateRenderer.apply(this, arguments);
  } else if (cellProperties.type === 'numeric') { // 数値型
    Handsontable.renderers.NumericRenderer.apply(this, arguments);
  } else { // 特に強い拘りがなければテキスト(文字列）型
    Handsontable.renderers.TextRenderer.apply(this, arguments);
  }
  var hidden = hiddenList[number];
  var cellInfoData = cellData[number][row][col];
  if (viewFlag[number] === true) {
    var rowHeightCell = rowHeight[number][row] + 'px';
  } else {
    var rowHeightCell = hiddenRowHeight[number][row] + 'px';;
  }
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
  td.style.height = rowHeightCell;
  td.classList.add(clsRow);
  td.classList.add(clsCol);
 
  if (viewFlag[number] !== true && hidden[row] === true) {
    td.style.padding = '0px';
    td.style.borderWidth = '0px';
  } else {
    td.style.padding = '2px';
    td.style.borderWidth = borderWidth;
  }
}

function setViewFlag(number) {
  var view = [];
  for(var i = 0; i < number; i++) {
    view[i] = false;
  }
  return view;
}

function viewInvalidData(number) {
  var cell = [];
  var rowHeightCell =  [];
  if (viewFlag[number] === true) {
    viewFlag[number] = false;
    cell = hiddenCellValue[number];
    rowHeightCell = hiddenRowHeight[number];
  } else {
    viewFlag[number] = true;
    cell = cellValue[number];
    rowHeightCell = rowHeight[number];
  }
  table[number].updateSettings({
    data: cell,
    rowHeights: rowHeightCell,
  });
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

  /**
   * 値が空かどうか判定を行う
   * 
   * @param value 判定する値
   * 
   * @return {boolean} 空判定結果（true:空 false:空でない）
   */
  function isEmpty(value){
    if (!value) {  //null or undefined or ''(空文字) or 0 or false
        if (value!== 0 && value !== false) {
            return true;
        }
    } else if( typeof value == "object") {  //array or object
        return Object.keys(value).length === 0;
    }
      return false;  //値は空ではない
  }


