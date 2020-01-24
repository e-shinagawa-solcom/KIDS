
//  ヘッダタブ
$('.tabs__header').on({
	'mouseover' : function() {
		$(this).attr('src', '/img/type01/cmn/seg/h_tab_off_on.gif');
	} ,
	'mouseout' : function() {
		if ($(this).css('pointer-events') == 'none') {
			$(this).attr('src', '/img/type01/cmn/seg/h_tab_off.gif');
		} else {
			$(this).attr('src', '/img/type01/cmn/seg/h_tab_on.gif');
		}
	} ,
	'click' : function() {			
		$(this).attr('src', '/img/type01/cmn/seg/h_tab_off.gif');
		$(this).css('pointer-events', 'none');

		var tabDetail = $('.tabs__detail');			
		tabDetail.attr('src', '/img/type01/cmn/seg/d_tab_on.gif');
		tabDetail.css('pointer-events', '');
	}
});

// フッタタブ
$('.tabs__detail').on({
	'mouseover' : function() {
		$(this).attr('src', '/img/type01/cmn/seg/d_tab_off_on.gif');
	} ,
	'mouseout' : function() {		
		if ($(this).css('pointer-events') == 'none') {
			$(this).attr('src', '/img/type01/cmn/seg/d_tab_off.gif');
		} else {
			$(this).attr('src', '/img/type01/cmn/seg/d_tab_on.gif');
		}
	} ,
	'click' : function() {
		$(this).css('pointer-events', 'none');
		$(this).attr('src', '/img/type01/cmn/seg/d_tab_off.gif');	

		var tabHeader = $('.tabs__header');
		tabHeader.css('pointer-events', '');
		tabHeader.attr('src', '/img/type01/cmn/seg/h_tab_on.gif');

		if ($('input[name="ProductCode"]').val() != "" && $('input[name="strReviseCode"]').val() == "")
		{
			$('#detail-product').click();
		}
	}
});
