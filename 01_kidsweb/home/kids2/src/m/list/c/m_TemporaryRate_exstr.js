/************************* [ ����졼�ȥޥ��� ] *************************/
$(function(){
    var searchMaster = {
                    url: '/cmn/querydata.php?strSessionID=' + $.cookie('strSessionID'),
                    type: 'post',
                    dataType: 'json'
                };
    $('select[name="lngmonetaryunitcode"]').change(function(){
		var val = $('select[name="lngmonetaryunitcode"] option:selected').val();
		var text = $('select[name="lngmonetaryunitcode"] option:selected').text();
		var condition = {
			data: {
				QueryName: 'selectMonetaryRate',
				Conditions: {
					lngmonetaryunitcode: val
				}
			}
		};
		$.ajax($.extend({}, searchMaster, condition))
		.done(function(response){			
			var tblheard = $('th[id="monetaryrateTbl_heard"]');
			tblheard.text(text);
			var tblTbody = document.getElementById('monetaryrateTbl_tbody');
			$.each(response, function(i, elm) {
				tblTbody.rows[i].cells[0].innerText = elm.dtmapplystartdate;
				tblTbody.rows[i].cells[1].innerText = elm.dtmapplyenddate;
				tblTbody.rows[i].cells[2].innerText = elm.curconversionrate;
			});
		})
		.fail(function(response){			
			alert(response.responseText);		
			console.log(response.responseText);
		});
	});
});


function applyMonetaryRate(objID) {
	var elements = document.getElementsByTagName("input") ;
	elements.namedItem( "dtmapplystartdate" ).value = objID.cells[0].innerHTML;	
	elements.namedItem( "dtmapplyenddate" ).value = objID.cells[1].innerHTML;	
	elements.namedItem( "curconversionrate" ).value = objID.cells[2].innerHTML;	
}


//////////////////////////////////////////////////////////////////
////////// [����][�ɲ�]�������֥������ȤΥ�����ɽ����ؿ� //////////
function fncEditObjectOnload()
{
	// ���֥������Ȥμ�ư�쥤������
	fncInitLayoutObjectModule( objColumn , objInput  , 60 , 216 );

	// [�ɲ�]
	if( g_strMode == 'add' )
	{
		// ���֥������Ȥ�ID�Ѵ�
		fncChangeObjectIdModule( Array( 'Input0' , 'Input1' , 'Input2', 'Input3' ) ,
								 Array( 'Txt15L' , 'Txt10L' , 'Txt10L', 'Txt10L' ) );
	}
	// [����]
	else if( g_strMode == 'fix' )
	{
		// ���֥������Ȥ�ID�Ѵ�
		fncChangeObjectIdModule( Array( 'Input0' , 'Input1' , 'Input2', 'Input3' ) ,
								 Array( 'Txt15L' , 'Txt10L' , 'TxtDis10L', 'Txt10L' ) );
	}

	// ���֥������Ȥ�̾������
	setObjectName();

	return true;
}




//////////////////////////////////////////////////////////////////
////////// [CONFIRM]�������֥������ȤΥ�����ɽ����ؿ� //////////
function fncConfirmObjectOnload( strMode )
{

	// �����⡼�ɤν����
	g_strMode = '';

	// �����⡼�ɤμ���
	g_strMode = strMode;


	// ���֥������Ȥ�̾������
	setObjectName();


	return true;
}






//////////////////////////////////////////////////////////////////
////////// �ɲåܥ����ɽ�� //////////
if( typeof(window.top.MasterAddBt) != 'undefined' )
{
	window.top.MasterAddBt.style.visibility = 'visible';
}







//////////////////////////////////////////////////////////////////
////////// �إå������᡼�������� //////////
var headerAJ = '<img src="' + h_trateJ + '" width="949" height="30" border="0" alt="����졼�ȥޥ���">';







//////////////////////////////////////////////////////////////////
////////// ���֥�������̾���� //////////
function setObjectName()
{
	// �����꡼�ơ��֥�ԣϣк�ɸ
	if( typeof(QueryTable) != 'undefined' )
	{
		QueryTable.style.top = 300;
	}

    // �إå������᡼���񤭽Ф�
	if( typeof(window.top.SegAHeader) != 'undefined' )
	{
        window.top.SegAHeader.innerHTML = headerAJ;
    }


    // �����꡼�ܥ���ơ��֥�񤭽Ф�
    if( typeof(fncTitleOutput) != 'undefined' )
    {
        fncTitleOutput( 1 );
    }

    // �ɲåܥ��󥤥᡼���񤭽Ф�
    if( typeof(window.top.MasterAddBt) != 'undefined' )
    {
        window.top.MasterAddBt.innerHTML = maddbtJ1;
    }

	return false;

}
