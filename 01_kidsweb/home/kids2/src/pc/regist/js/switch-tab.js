(function(){
	// ɽ����Υ��֤�����
	var tabs = $('.tabs');
	tabs.prop('displayTab', 'header');

	// �إå�����
	$('.tabs__header').on({
		'click' : function() {
			tabs.prop('displayTab', 'header');
	        // �إå�/�եå����� ɽ��/��ɽ���ڤ��ؤ�
	        $('.regist-tab-header, .regist-tab-detail').toggle();
	        // ��Ͽ�ܥ��� ɽ��/��ɽ���ڤ��ؤ�
	        $('.form-buttons__regist').toggle();
		}
	});

	// �ܺ٥���
	$('.tabs__detail').on({
		'click' : function() {
			tabs.prop('displayTab', 'detail');
	        // �إå�/�եå�����ɽ��/��ɽ���ڤ��ؤ�
	        $('.regist-tab-header, .regist-tab-detail').toggle();
	        // ��Ͽ�ܥ��� ɽ��/��ɽ���ڤ��ؤ�
	        $('.form-buttons__regist').toggle();

	        // JQuery Validation Plugin�Ǹ��Τ�����٥��٥�ȥ��å�
	        $('input[name="ProductCode"]').trigger('blur');
	        $('input[name="ProductName"]').trigger('blur');
	        $('input[name="GoodsCode"]').trigger('blur');
		}
	});

	// Tab��������
	// �إå�������Ƭ�Ǥ� shift + tab ��������
	$('.regist-tab-header').on(
		'keydown', 'input:eq(0)', function(e){
			if(e.keyCode == 9 && e.shiftKey){
				$('.tabs__detail').click();
				// �ܺ٥��֤ν����˥ե���������ܤ�
				$('.regist-tab-detail').find('input, textarea').last().focus();
				return false;
			}
		}
	);
	// �إå����ֽ����Ǥ�tab��������
	$('.regist-tab-header').on(
		'keydown', 'textarea', function(e){
			if ( $(this)[0] == $('.regist-tab-header textarea:last')[0] ) {
				if(e.keyCode == 9 && !e.shiftKey){
					$('.tabs__detail').click();
					// �ܺ٥��֤���Ƭ�˥ե���������ܤ�
					$('.regist-tab-detail input:eq(0)').focus();
					return false;
				}
			}
		}
	);
	// �ܺ٥�����Ƭ�Ǥ� shift + tab ��������
	$('.regist-tab-detail').on(
		'keydown', 'input:eq(0)', function(e){
			if(e.keyCode == 9��&& e.shiftKey){
				$('.tabs__header').click();
				// �إå����֤ν����˥ե���������ܤ�
				$('.regist-tab-header').find('input, textarea').last().focus();
				return false;
			}
		}
	);
	// �ܺ٥��ֽ����Ǥ�tab��������
	$('.regist-tab-detail').on(
		'keydown', 'input, img', function(e) {
			// table���Ǥ�input��������detail-tab�Ǹ�����Ǥ�input���ʤ����img
			if( $('.regist-tab-detail table input').length ) {
				if ( $(this)[0] == $('.regist-tab-detail input:last')[0] ) {
					if(e.keyCode == 9 && !e.shiftKey){
						$('.tabs__header').click();
						// �إå����֤���Ƭ�˥ե���������ܤ�
						$('.regist-tab-header input:eq(0)').focus();
						return false;
					}
				}
			// �ⷿ�Υơ��֥����Ǥ��ʤ����img��detail-tab�Ǹ������
			} else if ( $(this)[0] == $('.regist-tab-detail img:last')[0] ) {
				if(e.keyCode == 9 && !e.shiftKey){
					$('.tabs__header').click();
					// �ܺ٥��֤���Ƭ�˥ե���������ܤ�
					$('.regist-tab-header input:eq(0)').focus();
					return false;
				}
			}
		}
	);
})();
