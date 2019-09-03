(function(){
    // �ե�����
    var workForm = $('form');
    // ���ꥢ�ܥ���
    var btnClear = $('img.clear');
    // ��Ͽ�ܥ���
    var btnSearch = $('img.search');

    // �ե����ॵ�֥ߥå��޻�
    $('document').on('submit', 'form', function(e){
        e.preventDefault();
        return false;
    });

    // ���ꥢ�ܥ���
    btnClear.on('click', function(){
        // �ƥ��������ϲս��ꥻ�å�
        workForm.find('input[type="text"], textarea').val('');
        var checks = workForm.find('input[type="checkbox"]');
        for(var i = 0;i < checks.length;i++){
        	checks[i].checked = false;
        }
    });

    // �����ܥ��󲡲����ν���
    btnSearch.on('click', function(){
        var windowName = 'searchResult';
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
        
        if((baseURI.indexOf('/search.php') > 0) && (baseURI.indexOf('/list/search/') > 0)){
            var searchResult = open('about:blank', windowName, 'width=1011px, height=700px, resizable=yes, scrollbars=no, menubar=no');
            GoResult_list( searchResult, workForm.get(0) , "/result/index.html" , "/result/ifrm.html" , "ResultIframe" , "YES" );
        }
        else if((baseURI.indexOf('/search.php') > 0) && (baseURI.indexOf('/m/search/') > 0)){
        	var windowResult = open('about:blank', windowName, 'scrollbars=yes, resizable=yes');
        	var screen = baseURI.slice(baseURI.lastIndexOf('/',baseURI.indexOf('/search.php')-1)+1,baseURI.indexOf('/search.php'))
        	workForm.get(0).action = '/m/result/' + screen + '/index.php?strSessionID=' + $.cookie('strSessionID');
        	// ���֥ߥå�
        	workForm.submit();
        }
        else if((baseURI.indexOf('/search.php') > 0) && (baseURI.indexOf('/uc/search/') > 0)){
        	var windowResult = open('about:blank', windowName, 'scrollbars=yes, resizable=yes');
        	workForm.get(0).action = '/uc/result/index.php?strSessionID=' + $.cookie('strSessionID');
        	// ���֥ߥå�
        	workForm.submit();
        }
        else if(baseURI.indexOf('/search/index.php') > 0){
        	var windowResult = open('about:blank', windowName, 'scrollbars=yes, resizable=yes');
        	var screen = baseURI.slice(baseURI.lastIndexOf('/',baseURI.indexOf('/search/index.php')-1)+1,baseURI.indexOf('/search/index.php'))
        	workForm.get(0).action = '/' + screen + '/result/index.php?strSessionID=' + $.cookie('strSessionID');
        	// ���֥ߥå�
        	workForm.submit();
        }
        
    });
})();

//@-------------------------------------------------------------------------------------------------------------------
/**
* ���� : �������ɽ���Ѵؿ�
*
* ���� : ������̤����������ɽ�������뤿��δؿ���
*        ����[args]�˥ե��������Ǥ��ͤ����������������ɽ�����롣
*
* �о� : �����ѥƥ�ץ졼��
*
* @param [obj1]      : [���֥������ȷ�] . �ե�����Υ��֥�������̾
* @param [obj2]      : [ʸ����]       . ����������ǸƤӽФ���HTML�ե������URL
* @param [strUrl]    : [ʸ����]       . ����������ǸƤӽФ���HTML�ե������URL(Iframe)
* @param [strID]     : [ʸ����]       . ����������ǸƤӽФ���HTML�ե������ Iframe��ID
* @param [strScroll] : [ʸ����]       . Iframe�ǥ�������ε��ġ��Ե���
*
* @event [onclick] : �оݥ��֥�������
*/
//--------------------------------------------------------------------------------------------------------------------
function GoResult_list(TargetWindow, obj1 , obj2 , strUrl , strID , strScroll )
{
	var j = 0;
	var k = 0;

	args = new Array();
	args[0] = new Array();
	args[1] = new Array();
	args[2] = new Array();
	args[3] = new Array();
	args[4] = new Array();

	args[0][0] = strUrl;
	args[0][1] = strID;
	args[0][2] = strScroll;


	///// other name /////
	for ( i = 0; i < obj1.elements.length; i++ )
	{

		if( typeof(obj1.elements[i]) == 'undefined' )
		{
			continue;
		}

		if ( obj1.elements[i].type == 'checkbox' )
		{
			if ( obj1.elements[i].checked == true )
			{
				args[1][j] = obj1.elements[i].name;
				args[2][j] = obj1.elements[i].value;
				j++;
			}
			continue;
		}

		// �����ڡ���[����]���ܰʳ�
		if( obj1.elements[i].name != 'OrderStatusObject' )
		{
			args[3][k] = obj1.elements[i].name;
			args[4][k] = obj1.elements[i].value;
			k++;
		}

	}

	TargetWindow.location.href = obj2;
}