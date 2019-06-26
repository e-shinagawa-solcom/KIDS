$(function() {
    var recipient = "";
    /* �⡼����򳫤���������������*/
    $('#payf_dialog,#product_dialog,#group_dialog,#user_dialog,#location_dialog,#customer_dialog,#location_dialog,#supplier_dialog,#inputuser_dialog,#incharge_dialog')
        .on('show.bs.modal', function (event) {
        //�⡼�����ƤӽФ��Ȥ��˻Ȥ�줿�ܥ�������
        var button = $(event.relatedTarget);
        //data-whatever ���ͤ����
        recipient = button.data('whatever');
        //�⡼����Υ����ɡ�̾�Υƥ����ȥܥå������ͤ����ꤹ��
        $($('input[name="input' + recipient + 'Code"]')).val($("#" + recipient + "Code").val());
        $($('input[name="input' + recipient + 'Name"]')).val($("#" + recipient + "Name").val());
        //�⡼����Υ��쥯�ȥܥå��������ͤ��ư����
        $('select#sear' + recipient + 'Result option').remove();
    });

    /* �⡼����򳫤����������������*/
    $('#payf_dialog,#product_dialog,#group_dialog,#user_dialog,#location_dialog,#customer_dialog,#location_dialog,#supplier_dialog,#inputuser_dialog,#incharge_dialog')
        .on('shown.bs.modal', function (event) {
        //�⡼�����ƤӽФ��Ȥ��˻Ȥ�줿�ܥ�������
        var button = $(event.relatedTarget);
        //data-whatever ���ͤ����
        recipient = button.data('whatever');
        //�⡼����Υ����ɥƥ����ȥܥå����˥ե�����������
        $($('input[name="input' + recipient + 'Code"]')).focus();
    });

    // /* �⡼������Ĥ�����������������*/
    $('#payf_dialog,#product_dialog,#group_dialog,#user_dialog,#location_dialog,#customer_dialog,#location_dialog,#supplier_dialog,#inputuser_dialog,#incharge_dialog')
        .on('hide.bs.modal', function () {
            //�⡼����γƥ���ץåȥܥå������ͤ򥯥ꥢ����
            $($('input[name="input' + recipient + 'Code"]')).val('');
            $($('input[name="input' + recipient + 'Name"]')).val('');
            $($('input[name="' + recipient + 'Result"]')).val('');
            $('select#sear' + recipient + 'Result option').remove();
    });

    /* �⡼���륦����ɥ����ư�Ǥ���褦�����ꤹ��*/
    $('#payf_dialog,#product_dialog,#group_dialog,#user_dialog,#location_dialog,#customer_dialog,#location_dialog,#supplier_dialog,#inputuser_dialog,#incharge_dialog')
        .draggable({
        handle: ".modal-header"
    });
});

$(document).ready(function () {
    //�⡼����Υ�󥯤��������
    var searBtn = document.getElementsByClassName("modal-syncer") ;
    alert("test");
    alert(searBtn);
    //�⡼���륦����ɥ���и������륯��å����٥��
    for(var i=0; i<searBtn.length; i++) {
        //���ƤΥ�󥯤˥��å����٥�Ȥ����ꤹ��
        searBtn[i].onclick = function(){
            //whatever�Ȥʤ륳��ƥ�Ĥ��ǧ
            var objId = this.getAttribute("data-whatever");
            alert(objId);
            //�⡼����򳫤����Ȥ����Ĥ������طʤ��ѹ�����
            createModalOverlay(this, objId);

            // �ե�������������
            var searForm = document.getElementById("sear" + objId + "Form");
            if (searForm == null) {
                return false ;
            }
            //�ե�����Υ��֥ߥåȥ��٥�Ȥ��������
            searForm.onsubmit = function(e){
                //�ե�����Υݥ��ȥǡ������������
                var postData = $("#sear" + objId + "Form").serialize();
                if (objId == 'user') {
                    //ô���Ը����ξ�硢���祳���ɤ�ݥ��ȥѥ�᡼���Ȥ����Ϥ�
                    postData = postData + "&groupCode=" + $('#groupCode').val();
                } else if(objId == 'incharge') {
                    //�ܵ�ô���Ը����ξ�硢�ܵҥ����ɤ�ݥ��ȥѥ�᡼���Ȥ����Ϥ�
                    postData = postData + "&customerCode=" + $('#customerCode').val();
                }
                //���������URL���������
                var formURL = $(this).attr("action");
                $.ajax({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                      },                    
                    url: formURL,
                    type: "POST",
                    data: postData,
                    dataType : "json",
                    // scriptCharset: 'utf-8',
                    success : function(josn) {
                        //�쥹�ݥ󥹥ǡ������������
                        var $data = josn.data;
                        //�ꥶ��ȥƥ����ȥܥå����˥ǡ������������
                        $($('input[name="' + objId + 'Result"]')).val($data.length);
                        //���쥯�ȥܥå����δ�¸�ǡ�����������
                        $('select#sear' + objId + 'Result option').remove();
                        //���쥯�ȥܥå������������
                        var $content = $('#sear' + objId + 'Result');
                        // �쥹�ݥ󥹥ǡ���������ξ�硢option��NoData�򥻥å�
                        if ($data.length == 0) {
                            $content.append($('<option>')
                                .html('(No&nbsp;&nbsp;Data)')
                            );
                            $content.prop('disabled', true);
                        } else {
                            //���쥯�ȥܥå����˥ǡ��������ꤹ��
                            for (var i =0; i<$data.length; i++) {
                                $content.append($('<option>')
                                    .attr({
                                        code: $data[i].code,
                                        name: $data[i].name
                                    }).html($data[i].code + '&nbsp;&nbsp;&nbsp;' + $data[i].name)
                                );
                            }
                            $content.prop('disabled', false);
                        }
                    },
                    error: function(jqXHR, status, error) {
                        console.log(status + ": " + error);
                        alert(error);
                    }
                });
                e.preventDefault();
            }

            // ���ꥢ�ܥ�����������
            var clearBtn = document.getElementById("clear" + objId + "Btn");
            if (clearBtn == null) {
                return false ;
            }
            // ���ꥢ�ܥ���򥯥�å�����ȡ��ƥƥ����ȥܥå������ͤ򥯥ꥢ����
            clearBtn.onclick = function(){
                $($('input[name="input' + objId + 'Code"]')).val('');
                $($('input[name="input' + objId + 'Name"]')).val('');
                $('select#sear' + objId + 'Result option').remove();
                $($('input[name="' + objId + 'Result"]')).val('0');
            }

            // �����ܥ�����������
            var searBtn = document.getElementById("sear" + objId + "Btn");
            if (searBtn == null) {
                return false ;
            }
            // �����ܥ���򥯥�å�����ȡ��ե�����򥵥֥ߥåȤ���
            searBtn.onclick = function(){
                $("#sear" + objId + "Form").submit();
            }

            // Ŭ�ѥܥ�����������
            var applyBtn = document.getElementById("apply" + objId + "Btn");
            if (applyBtn == null) {
                return false ;
            }
            // Ŭ���ܥ���򥯥�å�����ȡ����쥯���������򤷤��ͤ�ƤӽФ����˰����Ϥ�
            applyBtn.onclick = function() {
                if ($('#sear' + objId + 'Result option:selected').val() == null)
                {
                    return false;
                }
                // ���򤵤�Ƥ���value°���ͤ���Ф�
                // var val = $('#searResult option:selected').val();
                // ���򤵤�Ƥ���ɽ��ʸ�������Ф�
                var txt = $('#sear' + objId + 'Result option:selected').text().split('???');
                $("#" + objId + "Code").val(txt[0]);
                $("#" + objId + "Name").val(txt[1]);
                // �⡼���륦����ɥ����Ĥ���
                $('#' + objId + '_dialog').modal('hide');
            }

            // ���쥯�����ܥå������������
            var searResult = document.getElementById("sear" + objId + "Result");
            if (searResult == null) {
                return false ;
            }
            // ���쥯�����ܥå����򥯥�å�����ȡ����쥯���������򤷤��ͤ�ƥ����Ȥ˰����Ϥ�
            searResult.onchange = function () {
                var txt = $('#sear' + objId + 'Result option:selected').text().split('???');
                $($('input[name="input' + objId + 'Code"]')).val(txt[0]);
                $($('input[name="input' + objId + 'Name"]')).val(txt[1]);
            }
            // SHIFT+TAB������inputCode�ƥ����ȥܥå����򲡲������Ȥ���Ŭ���ܥ���˥ե����������ư
            $($('input[name="input' + objId + 'Code"]')).on('keydown', function(e) {
                if (e.keyCode == 9 && e.shiftKey) {
                    $("#apply"+objId + "Btn").focus();
                    return false;
                }
            });

            // tab������Ŭ���ܥ���򥯥�å�����ȡ�inputCode�ƥ����ȥܥå����˥ե����������ư����
            $("#apply"+objId + "Btn").on('keydown', function(e) {
                // Tab�Τߤ�msw�κǽ�����Ǥ˥ե����������᤹
                if(e.keyCode == 9 && !e.shiftKey){
                    //�⡼����Υ����ɥƥ����ȥܥå����˥ե�����������
                    $($('input[name="input' + objId + 'Code"]')).focus();
                    return false;
                }
            });
        }
    }
    //�ꥵ�������줿�顢���󥿥�󥰤򤹤�ؿ�[centeringModalSyncer()]��¹Ԥ���
    $(window).resize(centeringModalSyncer) ;
});
/**
 * �����⡼������������
 * @param objId
 * @param objName
 * @param inputCode
 * @param inputName
 * @returns {string}
 */
function createModal(objId, objName, inputCode, inputName) {
    var lableClass = "";
    var textClass = "";
    if (objId == 'location' || objId == 'incharge') {
        lableClass = "col-4";
        textClass = "col-8";
    } else {
        lableClass = "col-3";
        textClass = "col-9";
    }
    var div = [];
    div.push("<div class='modal-dialog'>");
    div.push("<div class='modal-content'>");
    div.push("<div class='modal-header text-white bg-steelblue pl-1 pb-0'>");
    div.push("<small id='dialog" + objId + "Title'>" + objName + "����</small>");
    div.push("<button type='button' class='close' id='" + objId + "Close' data-dismiss='modal'>");
    div.push("<span aria-hidden='true'>&times</span>");
    div.push("</button>");
    div.push("</div>")
    div.push("<div class='modal-body bg-white p-1 pb-0'>");
    div.push("<div class='my-0 p-0 bg-white rounded box-shadow border'>");
    div.push("<div class='p-1 bg-pervenche rounded-top'>");
    div.push("<h6 class='mb-0 text-white small'>����</h6>");
    div.push("</div>");
    div.push("<div class='col-12 order-md-1 p-1'>");
    div.push("<form id='sear" + objId + "Form' action='/master/search/" + objId + "' method='POST'>");
    div.push("<div class='row col-12 pb-1'>");
    div.push("<div class='" + lableClass + " pt-1 border-left border-top border-bottom border-gray small'>");
    div.push("<label id='label" + objId + "Code'>" + objName + "������</label>");
    div.push("</div>");
    div.push("<div class='col-2 p-1 border-right border-top border-bottom border-gray'>");
    div.push("<input type='text' class='form-control form-control-sm' name='input" + objId + "Code' value='" + inputCode + "' tabindex='1'>");
    div.push("</div>");
    div.push("</div>");
    div.push("<div class='row col-12 pb-1'>");
    div.push("<div class='" + lableClass + " pt-1 border-left border-top border-bottom border-gray small'>");
    div.push("<label id='label" + objId + "Name'>" + objName + "̾��</label>");
    div.push("</div>");
    div.push("<div class='" + textClass + " p-1 border-right border-top border-bottom border-gray small'>");
    div.push("<input type='text' class='form-control form-control-sm' name='input" + objId + "Name' value='" + inputName + "' tabindex='2'>");
    div.push("</div>");
    div.push("</div>");
    div.push("<div class='col-12 pb-0 text-center'>");
    div.push("<button type='button' class='btn btn-sm btn-info' tabindex='3' id='sear" + objId + "Btn' >������</button>");
    div.push("</div>");
    div.push("</form>");
    div.push("</div>");
    div.push("</div>");
    div.push("<div class='bg-white rounded box-shadow border my-1 p-0'>");
    div.push("<div class='p-1 bg-pervenche rounded-top'>");
    div.push("<h6 class='mb-0 text-white small'>�������</h6>");
    div.push("</div>");
    div.push("<div class='col-12 p-1'>");
    div.push("<div class='col-12'>");
    div.push("<select class='form-control modal-select' tabindex='4' size='6' id='sear" + objId + "Result' disabled></select>");
    div.push("</div>");
    div.push("</div>");
    div.push("<div class='row col-12 p-0 pb-1'>");
    div.push("<div class='col-8 text-right'>");
    div.push("<button type='button' class='btn btn-sm  btn-info small' tabindex='5' id='clear" + objId + "Btn'>���ꥢ</button> ");
    div.push("<button type='button' class='btn btn-sm  btn-info' tabindex='6' id='apply" + objId + "Btn'>Ŭ����</button>");
    div.push("</div>");
    div.push("<div class='input-group col-4'>");
    div.push("<div class='input-group-prepend small'>");
    div.push("<label for='" + objId + "Result'>RESULT</label>");
    div.push("</div>");
    div.push("<input type='text' class='form-control form-control-sm'  name='" + objId + "Result' disabled='disabled'>");
    div.push("</div>");
    div.push("</div>");
    div.push("</div>");
    div.push("</div>");
    div.push("</div>");
    div.push("</div>");

    return div.join("");
}

/**
 * �⡼����򳫤��Ȥ��Ĥ���Ȥ����طʤ��Ѳ��������
 * @param {*} btnLink 
 */
function createModalOverlay(btnLink, objId) {
    //�ܥ��󤫤�ե��������򳰤�
    btnLink.blur() ;
    //�������åȤȤʤ륳��ƥ�Ĥ��ǧ
    var target = btnLink.getAttribute( "data-target" ) ;
    //�������åȤ�¸�ߤ��ʤ���н�λ
    if( typeof( target )=="undefined" || !target || target==null ){
        return false ;
    }
    //����ƥ�ĤȤʤ����Ǥ����
    nowModalSyncer = document.getElementById(objId + "_dialog") ;
    //�������åȤ�¸�ߤ��ʤ���н�λ
    if( nowModalSyncer == null ){
        return false ;
    }

    //�����ܡ������ʤɤˤ�ꡢ�����С��쥤��¿�ŵ�ư����Τ��ɻߤ���
    if( $( "#modal-overlay" )[0] ) return false ;		//�������⡼���륦����ɥ���ư���ʤ�
    //if($("#modal-overlay")[0]) $("#modal-overlay").remove() ;		//���ߤΥ⡼���륦����ɥ��������ƿ�������ư����

    //����������֤�Ͽ����
    var dElm = document.documentElement , dBody = document.body;
    sX_syncerModal = dElm.scrollLeft || dBody.scrollLeft;	//���߰��֤�X��ɸ
    sY_syncerModal = dElm.scrollTop || dBody.scrollTop;		//���߰��֤�Y��ɸ

    //�����С��쥤��и�������
    $("body").append( '<div id="modal-overlay"></div>' ) ;

    $("#modal-overlay").fadeIn( "fast" ) ;

    //����ƥ�Ĥ򥻥󥿥�󥰤���
    centeringModalSyncer(nowModalSyncer) ;

    //����ƥ�Ĥ�ե����ɥ��󤹤�
    $(nowModalSyncer).fadeIn( "slow" ) ;

    //[#modal-overlay]���ޤ���[#modal-close]�򥯥�å��������
    $("#modal-overlay,#"+objId + "Close,#apply"+objId + "Btn").unbind().click( function() {
        alert(sX_syncerModal);
        //����������֤��᤹
        window.scrollTo( sX_syncerModal , sY_syncerModal );

        //[#modal-content]��[#modal-overlay]��ե����ɥ����Ȥ�����ˡ�
        $("#modal-content,#modal-overlay").fadeOut( "fast" , function() {

            //[#modal-overlay]��������
            $( '#modal-overlay' ).remove() ;

        } ) ;
        //���ߤΥ���ƥ�ľ������
        nowModalSyncer = null ;
    } ) ;
}

/**
 * ���󥿥�󥰤�¹Ԥ���ؿ�
 */
function centeringModalSyncer() {
    //�⡼���륦����ɥ��������Ƥʤ���н�λ
    if(nowModalSyncer == null)
    {
        return false ;
    }
    //����(������ɥ�)�������⤵�����
    var w = $( window ).width() ;
    var h = $( window ).height() ;

    //����ƥ��(#modal-content)�������⤵�����
    // jQuery�ΥС������ˤ�äƤϡ�����[{margin:true}]����ꤷ�������Զ��򵯤����ޤ���
    var cw = $( nowModalSyncer ).outerWidth( {margin:true} ) ;
    var ch = $( nowModalSyncer ).outerHeight( {margin:true} ) ;
    // var cw = $( nowModalSyncer ).outerWidth() ;
    // var ch = $( nowModalSyncer ).outerHeight() ;
    //���󥿥�󥰤�¹Ԥ���
    $(nowModalSyncer).css( {"left": ((w - cw)/2) + "px","top": ((h - ch)/2) + "px"} ) ;
    
}