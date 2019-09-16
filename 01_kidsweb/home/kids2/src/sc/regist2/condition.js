//
// condition.js
//
jQuery(function($){
    // �̥�����ɥ��򳫤���POST����
    function post_open(url, data, target, features) {

        window.open('', target, features);
       
        // �ե������ưŪ������
        var html = '<form id="temp_form" style="display:none;">';
        for(var x in data) {
          if(data[x] == undefined || data[x] == null) {
            continue;
          }
          var _val = data[x].replace(/'/g, '\'');
          html += "<input type='hidden' name='" + x + "' value='" + _val + "' >";
        }
        html += '</form>';
        $("body").append(html);
       
        $('#temp_form').attr("action",url);
        $('#temp_form').attr("target",target);
        $('#temp_form').attr("method","POST");
        $('#temp_form').submit();
       
        // �ե��������
        $('#temp_form').remove();
    }

    // ------------------------------------
    //  events
    // ------------------------------------
    // OK�ܥ���
    $('#OkBt').on('click', function(){
        alert("OK Clicked");
        //window.opener.$("#PreviewBt").trigger("click");
    });

    // �Ĥ���ܥ���
    $('#CancelBt').on('click', function(){
        window.close();
    });

});