(function(){
    // �ե�����
    var workForm = $('form');
    // Upload�ܥ���
    var btnUpload = $('img.upload');

    // �ե����ॵ�֥ߥå��޻�
    $('document').on('submit', 'form', function(e){
        e.preventDefault();
        return false;
    });

    // �����ܥ��󲡲����ν���
    btnUpload.on('click', function(){

		if( document.exc_upload.excel_file.value.length == 0 )
		{
			alert( '�ե��������ꤷ�Ƥ���������' );
			return false;
		} else if ( !document.exc_upload.excel_file.value.toUpperCase().match(/.*\.(XLSX)$/))
		{
			alert( '��ĥ�Ҥ�xlsx�Υե��������ꤷ�Ƥ�������' );
			return false;
		}
	
		var windowName = 'workSheetView';
		
        // �ҥ�����ɥ���ɽ��        
        if(workForm.length == 0){
	        var IFrames = $('iframe');
	        
	        for (var i = 0; i < IFrames.length;i++){
	        	if(IFrames[i].id == 'SegAIFrm') {
	        		//workForm = IFrames[i].find('form').children(0).getElementById("myid");
	        		workForm = IFrames[i].contentDocument.forms[0];
	        		workForm = $(workForm);
	        		break;
	        	}
	        }
        }
        
        // �ե���������
        workForm.get(0).target = windowName;
        workForm.get(0).method = 'post';
        var baseURI = workForm.get(0).baseURI;
        
        if(baseURI.indexOf('/upload2/index.php') > 0) {
        	var windowResult = open('about:blank', windowName, 'scrollbars=yes, width=985, height=700, resizable=0 location=0');
        	workForm.get(0).action = '/estimate/regist/select.php';
        	// ���֥ߥå�
        	workForm.submit();
        }
        else {
			alert('URL�������Ǥ�');
			return false;
        }

        
    });
})();

function fncAlphaOff( obj )
{
	obj.style.filter = 'alpha(opacity=100)' ;
}

function fncUploadButton( strMode , obj )
{
	switch( strMode )
	{
		case 'offJ':
			obj.src = uploadJ1;
			break;

		case 'onJ':
			obj.src = uploadJ2;
			break;

		case 'downJ':
			obj.src = uploadJ3;
			break;

		default:
			break;
	}
}

// ���åץ���gif
var uploadJ1 = '/img/type01/upload2/upbt_off.gif';
var uploadJ2 = '/img/type01/upload2/upbt_on_off.gif';
var uploadJ3 = '/img/type01/upload2/upbt_on.gif';

