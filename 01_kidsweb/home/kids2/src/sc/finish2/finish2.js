// ��������ɥܥ���β����ѥ�
var imagepath_download_off = "/img/type01/cmn/querybt/listout_off_ja_bt.gif";
var imagepath_download_on  = "/img/type01/cmn/querybt/listout_off_on_ja_bt.gif";

// HTML������ȹ��۸�
function OnloadBody(){
    // ��������ɥܥ���ν������
    var images = document.getElementsByClassName('btn-download');
    for (var i = 0; i < images.length; i++) 
    {
        var image = images[i];
        image.src = imagepath_download_off;
        image.alt ="���������"
    }
}

// ��������ɥܥ���˥ޥ����������뤬��ä��Ȥ�
function OnMouseOverDownload(obj){
    // �ޥ��������С��Ѳ����˺����ؤ���
    obj.src = imagepath_download_on
}
// ��������ɥܥ��󤫤�ޥ����������뤬���줿�Ȥ�
function OnMouseOutDownload(obj){
    // ����������᤹
    obj.src = imagepath_download_off
}
// ��������ɥܥ��󤬲����줿
function OnClickDownload(obj, strSlipCode, lngRevisionNo){
    alert("strSlipCode="+strSlipCode+",lngRevisionNo="+lngRevisionNo);
    
    $.ajax({
        type: 'POST',
        url: "preview.php",
        data: {
            strMode : "download",
            strSessionID: $('input[name="strSessionID"]').val(),
            strSlipCode: strSlipCode,
            lngRevisionNo: lngRevisionNo,
        },
        async: true,
    }).done(function(data){
        console.log("done:download");
        alert(data);
    }).fail(function(error){
        console.log("fail:download");
        console.log(error);
    });

}
