$(function() {
    var recipient = "";
    /* モーダルを開く前処理する内容*/
    $('#payf_dialog,#product_dialog,#group_dialog,#user_dialog,#location_dialog,#customer_dialog,#location_dialog,#supplier_dialog,#inputuser_dialog,#incharge_dialog')
        .on('show.bs.modal', function (event) {
        //モーダルを呼び出すときに使われたボタンを取得
        var button = $(event.relatedTarget);
        //data-whatever の値を取得
        recipient = button.data('whatever');
        //モーダルのコード、名称テキストボックスに値を設定する
        $($('input[name="input' + recipient + 'Code"]')).val($("#" + recipient + "Code").val());
        $($('input[name="input' + recipient + 'Name"]')).val($("#" + recipient + "Name").val());
        //モーダルのセレクトボックスから値を移動する
        $('select#sear' + recipient + 'Result option').remove();
    });

    /* モーダルを開いたら処理する内容*/
    $('#payf_dialog,#product_dialog,#group_dialog,#user_dialog,#location_dialog,#customer_dialog,#location_dialog,#supplier_dialog,#inputuser_dialog,#incharge_dialog')
        .on('shown.bs.modal', function (event) {
        //モーダルを呼び出すときに使われたボタンを取得
        var button = $(event.relatedTarget);
        //data-whatever の値を取得
        recipient = button.data('whatever');
        //モーダルのコードテキストボックスにフォーカスする
        $($('input[name="input' + recipient + 'Code"]')).focus();
    });

    // /* モーダルを閉じる前処理する内容*/
    $('#payf_dialog,#product_dialog,#group_dialog,#user_dialog,#location_dialog,#customer_dialog,#location_dialog,#supplier_dialog,#inputuser_dialog,#incharge_dialog')
        .on('hide.bs.modal', function () {
            //モーダルの各インプットボックスの値をクリアする
            $($('input[name="input' + recipient + 'Code"]')).val('');
            $($('input[name="input' + recipient + 'Name"]')).val('');
            $($('input[name="' + recipient + 'Result"]')).val('');
            $('select#sear' + recipient + 'Result option').remove();
    });

    /* モーダルウィンドウを移動できるように設定する*/
    $('#payf_dialog,#product_dialog,#group_dialog,#user_dialog,#location_dialog,#customer_dialog,#location_dialog,#supplier_dialog,#inputuser_dialog,#incharge_dialog')
        .draggable({
        handle: ".modal-header"
    });
});

$(document).ready(function () {
    //モーダルのリンクを取得する
    var searBtn = document.getElementsByClassName("modal-syncer") ;
    alert("test");
    alert(searBtn);
    //モーダルウィンドウを出現させるクリックイベント
    for(var i=0; i<searBtn.length; i++) {
        //全てのリンクにタッチイベントを設定する
        searBtn[i].onclick = function(){
            //whateverとなるコンテンツを確認
            var objId = this.getAttribute("data-whatever");
            alert(objId);
            //モーダルを開いたとき、閉じた時背景を変更する
            createModalOverlay(this, objId);

            // フォームを取得する
            var searForm = document.getElementById("sear" + objId + "Form");
            if (searForm == null) {
                return false ;
            }
            //フォームのサブミットイベントを定義する
            searForm.onsubmit = function(e){
                //フォームのポストデータを取得する
                var postData = $("#sear" + objId + "Form").serialize();
                if (objId == 'user') {
                    //担当者検索の場合、部門コードをポストパラメータとして渡す
                    postData = postData + "&groupCode=" + $('#groupCode').val();
                } else if(objId == 'incharge') {
                    //顧客担当者検索の場合、顧客コードをポストパラメータとして渡す
                    postData = postData + "&customerCode=" + $('#customerCode').val();
                }
                //アクションURLを取得する
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
                        //レスポンスデータを取得する
                        var $data = josn.data;
                        //リザルトテキストボックスにデータ件数を設定
                        $($('input[name="' + objId + 'Result"]')).val($data.length);
                        //セレクトボックスの既存データを削除する
                        $('select#sear' + objId + 'Result option').remove();
                        //セレクトボックスを取得する
                        var $content = $('#sear' + objId + 'Result');
                        // レスポンスデータが０件の場合、optionにNoDataをセット
                        if ($data.length == 0) {
                            $content.append($('<option>')
                                .html('(No&nbsp;&nbsp;Data)')
                            );
                            $content.prop('disabled', true);
                        } else {
                            //セレクトボックスにデータを設定する
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

            // クリアボタンを取得する
            var clearBtn = document.getElementById("clear" + objId + "Btn");
            if (clearBtn == null) {
                return false ;
            }
            // クリアボタンをクリックすると、各テキストボックスの値をクリアする
            clearBtn.onclick = function(){
                $($('input[name="input' + objId + 'Code"]')).val('');
                $($('input[name="input' + objId + 'Name"]')).val('');
                $('select#sear' + objId + 'Result option').remove();
                $($('input[name="' + objId + 'Result"]')).val('0');
            }

            // 検索ボタンを取得する
            var searBtn = document.getElementById("sear" + objId + "Btn");
            if (searBtn == null) {
                return false ;
            }
            // 検索ボタンをクリックすると、フォームをサブミットする
            searBtn.onclick = function(){
                $("#sear" + objId + "Form").submit();
            }

            // 適用ボタンを取得する
            var applyBtn = document.getElementById("apply" + objId + "Btn");
            if (applyBtn == null) {
                return false ;
            }
            // 適応ボタンをクリックすると、セレクションに選択した値を呼び出し元に引き渡す
            applyBtn.onclick = function() {
                if ($('#sear' + objId + 'Result option:selected').val() == null)
                {
                    return false;
                }
                // 選択されているvalue属性値を取り出す
                // var val = $('#searResult option:selected').val();
                // 選択されている表示文字列を取り出す
                var txt = $('#sear' + objId + 'Result option:selected').text().split('???');
                $("#" + objId + "Code").val(txt[0]);
                $("#" + objId + "Name").val(txt[1]);
                // モーダルウィンドウを閉じる
                $('#' + objId + '_dialog').modal('hide');
            }

            // セレクションボックスを取得する
            var searResult = document.getElementById("sear" + objId + "Result");
            if (searResult == null) {
                return false ;
            }
            // セレクションボックスをクリックすると、セレクションに選択した値をテキストに引き渡す
            searResult.onchange = function () {
                var txt = $('#sear' + objId + 'Result option:selected').text().split('???');
                $($('input[name="input' + objId + 'Code"]')).val(txt[0]);
                $($('input[name="input' + objId + 'Name"]')).val(txt[1]);
            }
            // SHIFT+TABキーでinputCodeテキストボックスを押下したとき、適応ボタンにフォーカスを移動
            $($('input[name="input' + objId + 'Code"]')).on('keydown', function(e) {
                if (e.keyCode == 9 && e.shiftKey) {
                    $("#apply"+objId + "Btn").focus();
                    return false;
                }
            });

            // tabキーで適応ボタンをクリックすると、inputCodeテキストボックスにフォーカスを移動する
            $("#apply"+objId + "Btn").on('keydown', function(e) {
                // Tabのみでmswの最初の要素にフォーカスを戻す
                if(e.keyCode == 9 && !e.shiftKey){
                    //モーダルのコードテキストボックスにフォーカスする
                    $($('input[name="input' + objId + 'Code"]')).focus();
                    return false;
                }
            });
        }
    }
    //リサイズされたら、センタリングをする関数[centeringModalSyncer()]を実行する
    $(window).resize(centeringModalSyncer) ;
});
/**
 * 検索モーダルを作成する
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
    div.push("<small id='dialog" + objId + "Title'>" + objName + "検索</small>");
    div.push("<button type='button' class='close' id='" + objId + "Close' data-dismiss='modal'>");
    div.push("<span aria-hidden='true'>&times</span>");
    div.push("</button>");
    div.push("</div>")
    div.push("<div class='modal-body bg-white p-1 pb-0'>");
    div.push("<div class='my-0 p-0 bg-white rounded box-shadow border'>");
    div.push("<div class='p-1 bg-pervenche rounded-top'>");
    div.push("<h6 class='mb-0 text-white small'>検索</h6>");
    div.push("</div>");
    div.push("<div class='col-12 order-md-1 p-1'>");
    div.push("<form id='sear" + objId + "Form' action='/master/search/" + objId + "' method='POST'>");
    div.push("<div class='row col-12 pb-1'>");
    div.push("<div class='" + lableClass + " pt-1 border-left border-top border-bottom border-gray small'>");
    div.push("<label id='label" + objId + "Code'>" + objName + "コード</label>");
    div.push("</div>");
    div.push("<div class='col-2 p-1 border-right border-top border-bottom border-gray'>");
    div.push("<input type='text' class='form-control form-control-sm' name='input" + objId + "Code' value='" + inputCode + "' tabindex='1'>");
    div.push("</div>");
    div.push("</div>");
    div.push("<div class='row col-12 pb-1'>");
    div.push("<div class='" + lableClass + " pt-1 border-left border-top border-bottom border-gray small'>");
    div.push("<label id='label" + objId + "Name'>" + objName + "名称</label>");
    div.push("</div>");
    div.push("<div class='" + textClass + " p-1 border-right border-top border-bottom border-gray small'>");
    div.push("<input type='text' class='form-control form-control-sm' name='input" + objId + "Name' value='" + inputName + "' tabindex='2'>");
    div.push("</div>");
    div.push("</div>");
    div.push("<div class='col-12 pb-0 text-center'>");
    div.push("<button type='button' class='btn btn-sm btn-info' tabindex='3' id='sear" + objId + "Btn' >検　索</button>");
    div.push("</div>");
    div.push("</form>");
    div.push("</div>");
    div.push("</div>");
    div.push("<div class='bg-white rounded box-shadow border my-1 p-0'>");
    div.push("<div class='p-1 bg-pervenche rounded-top'>");
    div.push("<h6 class='mb-0 text-white small'>検索結果</h6>");
    div.push("</div>");
    div.push("<div class='col-12 p-1'>");
    div.push("<div class='col-12'>");
    div.push("<select class='form-control modal-select' tabindex='4' size='6' id='sear" + objId + "Result' disabled></select>");
    div.push("</div>");
    div.push("</div>");
    div.push("<div class='row col-12 p-0 pb-1'>");
    div.push("<div class='col-8 text-right'>");
    div.push("<button type='button' class='btn btn-sm  btn-info small' tabindex='5' id='clear" + objId + "Btn'>クリア</button> ");
    div.push("<button type='button' class='btn btn-sm  btn-info' tabindex='6' id='apply" + objId + "Btn'>適　用</button>");
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
 * モーダルを開くとき閉じるとき、背景を変化する処理
 * @param {*} btnLink 
 */
function createModalOverlay(btnLink, objId) {
    //ボタンからフォーカスを外す
    btnLink.blur() ;
    //ターゲットとなるコンテンツを確認
    var target = btnLink.getAttribute( "data-target" ) ;
    //ターゲットが存在しなければ終了
    if( typeof( target )=="undefined" || !target || target==null ){
        return false ;
    }
    //コンテンツとなる要素を取得
    nowModalSyncer = document.getElementById(objId + "_dialog") ;
    //ターゲットが存在しなければ終了
    if( nowModalSyncer == null ){
        return false ;
    }

    //キーボード操作などにより、オーバーレイが多重起動するのを防止する
    if( $( "#modal-overlay" )[0] ) return false ;		//新しくモーダルウィンドウを起動しない
    //if($("#modal-overlay")[0]) $("#modal-overlay").remove() ;		//現在のモーダルウィンドウを削除して新しく起動する

    //スクロール位置を記録する
    var dElm = document.documentElement , dBody = document.body;
    sX_syncerModal = dElm.scrollLeft || dBody.scrollLeft;	//現在位置のX座標
    sY_syncerModal = dElm.scrollTop || dBody.scrollTop;		//現在位置のY座標

    //オーバーレイを出現させる
    $("body").append( '<div id="modal-overlay"></div>' ) ;

    $("#modal-overlay").fadeIn( "fast" ) ;

    //コンテンツをセンタリングする
    centeringModalSyncer(nowModalSyncer) ;

    //コンテンツをフェードインする
    $(nowModalSyncer).fadeIn( "slow" ) ;

    //[#modal-overlay]、または[#modal-close]をクリックしたら…
    $("#modal-overlay,#"+objId + "Close,#apply"+objId + "Btn").unbind().click( function() {
        alert(sX_syncerModal);
        //スクロール位置を戻す
        window.scrollTo( sX_syncerModal , sY_syncerModal );

        //[#modal-content]と[#modal-overlay]をフェードアウトした後に…
        $("#modal-content,#modal-overlay").fadeOut( "fast" , function() {

            //[#modal-overlay]を削除する
            $( '#modal-overlay' ).remove() ;

        } ) ;
        //現在のコンテンツ情報を削除
        nowModalSyncer = null ;
    } ) ;
}

/**
 * センタリングを実行する関数
 */
function centeringModalSyncer() {
    //モーダルウィンドウが開いてなければ終了
    if(nowModalSyncer == null)
    {
        return false ;
    }
    //画面(ウィンドウ)の幅、高さを取得
    var w = $( window ).width() ;
    var h = $( window ).height() ;

    //コンテンツ(#modal-content)の幅、高さを取得
    // jQueryのバージョンによっては、引数[{margin:true}]を指定した時、不具合を起こします。
    var cw = $( nowModalSyncer ).outerWidth( {margin:true} ) ;
    var ch = $( nowModalSyncer ).outerHeight( {margin:true} ) ;
    // var cw = $( nowModalSyncer ).outerWidth() ;
    // var ch = $( nowModalSyncer ).outerHeight() ;
    //センタリングを実行する
    $(nowModalSyncer).css( {"left": ((w - cw)/2) + "px","top": ((h - ch)/2) + "px"} ) ;
    
}