
//  ヘッダタブ
$('.tabs__header').on({
	'mouseover' : function() {
		$(this).attr('src', '/img/type01/cmn/seg/h_tab_off_on.gif');
	} ,
	'mouseout' : function() {
		$(this).attr('src', '/img/type01/cmn/seg/h_tab_on.gif');
	} ,
	'click' : function() {
		$(this).attr('disabled', 'disabled');
		$(this).attr('src', '/img/type01/cmn/seg/h_tab_off.gif');

		var tabDetail = $('.tabs__detail');
		tabDetail.removeAttr('disabled');
		tabDetail.attr('src', '/img/type01/cmn/seg/d_tab_on.gif');
	}
});

// フッタタブ
$('.tabs__detail').on({
	'mouseover' : function() {
		$(this).attr('src', '/img/type01/cmn/seg/d_tab_off_on.gif');
	} ,
	'mouseout' : function() {
		$(this).attr('src', '/img/type01/cmn/seg/d_tab_on.gif');
	} ,
	'click' : function() {
		$(this).attr('disabled', 'disabled');
		$(this).attr('src', '/img/type01/cmn/seg/d_tab_off.gif');

		var tabHeader = $('.tabs__header');
		tabHeader.removeAttr('disabled');
		tabHeader.attr('src', '/img/type01/cmn/seg/h_tab_on.gif');
	}
});
