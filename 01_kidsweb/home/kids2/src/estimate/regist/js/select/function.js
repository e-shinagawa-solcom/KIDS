function fncSheetSelector() {
    var trans = document.getElementById('transition');
    var num = trans.value;
    var id = 'sheet' + num;
    var element = document.getElementById(id); // 移動させたい位置の要素を取得
      var rect = element.getBoundingClientRect();
      var position = rect.top;    // 一番上からの位置を取得
      scrollTo(0, position);
  }
  
  function scrollTop() {
    scrollTo(0, 0);
  }