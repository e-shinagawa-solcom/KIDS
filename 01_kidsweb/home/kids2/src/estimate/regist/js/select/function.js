function fncSheetSelector() {
    var trans = document.getElementById('transition');
    var num = trans.value;
    var id = 'sheet' + num;
    var element = document.getElementById(id); // ��ư�����������֤����Ǥ����
      var rect = element.getBoundingClientRect();
      var position = rect.top;    // ���־夫��ΰ��֤����
      scrollTo(0, position);
  }
  
  function scrollTop() {
    scrollTo(0, 0);
  }