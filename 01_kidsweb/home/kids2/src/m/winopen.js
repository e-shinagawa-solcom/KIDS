$(function() {
	$('#MasterAddBt').on('click', function ( e ) {		
		var strEditURL = $('input[name="strEditURL"]').val();
		if (typeof(strEditURL) == 'undefined') {
			var iframe = $('#SegAIFrm').contents();
			strEditURL = iframe.find('input[name="strEditURL"]').val();
		}
		e.preventDefault();
		args = new Array();
		args[0] = new Array();
		args[0][0] = strEditURL; // �¹���URL
		args[0][1] = 'ResultIframeCommonMaster'; // IFrame�Υ���������ID
		args[0][2] = 'YES'; // IFrame����������ε��ġ��Ե���
		args[0][3] = 1; // $lngLanguageCode
		args[0][4] = 'add'; // 'fix' , 'add' , 'delete'
		var win = window.open('/result/remove_master.html' , args , 'height=510,width=600,centerscreen=yes,resizable=no,scrollbars=yes,chrome=yes');

		$(win).on('load', function(){
			$(win).on('unload', function () {
				if (typeof($('input[name="strEditURL"]').val()) == 'undefined') {
					iframe = $('#SegAIFrm').contents();
					iframe.find("#objForm").submit();
				} else {
					$("#objForm").submit();
				}
			});
		});
	});

	$('a').on('click', function(e){
		e.preventDefault();
		if ($(this).parent().attr('id') == "MasterAddBt") {
			return;
		}
		var href = $(this).attr('href');		
		var name = $(this).attr('name');
		args = new Array();
		args[0] = new Array();
		args[0][0] = href; // �¹���URL
		args[0][1] = 'ResultIframeCommonMaster'; // IFrame�Υ���������ID
		args[0][2] = 'YES'; // IFrame����������ε��ġ��Ե���
		args[0][3] = 1; // $lngLanguageCode
		args[0][4] = name; // 'fix' , 'add' , 'delete'
		var win = window.open('/result/remove_master.html' , args , 'height=510,width=600,centerscreen=yes,resizable=no,scrollbars=yes,chrome=yes');
		$(win).on('load', function(){
			$(win).on('unload', function () {
				$("#objForm").submit();
			});
		});
	});
});