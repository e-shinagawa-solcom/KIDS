(function(){
    // �����å��ܥ������(̤�����å�)
    var unchecked="/img/type01/cmn/seg/off_bt.gif";
    // �����å��ܥ������(�����å��Ѥ�)
    var checked="/img/type01/cmn/seg/on_bt.gif"

    // �����å��ܥå������ڤ��ؤ������ΥХ����
    $('img.toggle-search-header').on({
        'click': function() {
            toggleSearchCheckBox();
            // �����å��Ѥߤξ��
            if (this.checked){this.src = unchecked;}
            // ̤�����å��ξ��
            else {this.src = checked;}
            // �����ե饰��ȿž�����ݻ�
            this.checked = !this.checked;
        }
    });

    // �����å��ܥå������ڤ��ؤ������ΥХ����
    $('img.toggle-display-header').on({
        'click': function() {
            toggleDisplayCheckBox();
            // �����å��Ѥߤξ��
            if (this.checked){this.src = unchecked;}
            // ̤�����å��ξ��
            else {this.src = checked;}
            // �����ե饰��ȿž�����ݻ�
            this.checked = !this.checked;
        }
    });

    // ���������å��ܥå����Υȥ���
    var toggleSearchCheckBox = function(){
        $('input[type="checkbox"].is-search-header')
            .each(function(){
                // disabled�����Ǥ��оݳ�
                if (this.disabled == false){
                    this.checked = !toggleSearchCheckBox.checked;
                }
            });
        // �����ե饰��ȿž�������ݻ�
        toggleSearchCheckBox.checked = !toggleSearchCheckBox.checked;
    };

    // ɽ�������å��ܥå����Υȥ���
    var toggleDisplayCheckBox = function(){
        $('input[type="checkbox"].is-display-header')
            .each(function(){
                // disabled�����Ǥ��оݳ�
                if (this.disabled == false){
                    this.checked = !toggleDisplayCheckBox.checked;
                }
            });
        // �����ե饰��ȿž�������ݻ�
        toggleDisplayCheckBox.checked = !toggleDisplayCheckBox.checked;
    };
})();
