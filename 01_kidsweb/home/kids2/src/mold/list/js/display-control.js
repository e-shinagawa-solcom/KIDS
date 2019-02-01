(function(){
    // Ģɼ��ڡ���ñ�̤Ǽ���
    var pages = $('div#mold-report-page');

    // ���ڡ����ܥ���
    var btnPrev = $('img.button.prev-page');
    // ���ڡ����ܥ���
    var btnNext = $('img.button.next-page');

    // ���ߤΥڡ�����٥�
    var labelCurrentPage = $('span.current-page');
    // �Ǹ�Υڡ�����٥�
    var labelLastPage = $('span.last-page');

    // 1�ڡ�����
    var firstPage = 1;

    // ����ڡ������μ���/����
    var lastPage = pages.length;
    labelLastPage.get(0).innerHTML = lastPage;

    // ���ߥڡ���(���)��������
    var currentPage = firstPage;
    labelCurrentPage.get(0).innerHTML = currentPage;

    // ����ڡ�����ɽ��
    $('div#mold-report-page[page=' + currentPage + ']').addClass('show-page');

    // ���Υڡ������ڤ��ؤ���
    btnPrev.on('click', function(){
        // ���Τ�1�ڡ��������ʤ����
        if (lastPage == 1){ return; }

        // ���ߤΥڡ������ǽ��1�ڡ����ܤξ��
        if (currentPage == 1) {
            // �Ǹ�Υڡ�����ɽ��
            changePage(currentPage, lastPage);
        } else {
            // ���Υڡ�����ɽ��
            changePage(currentPage, --currentPage);
        }
    });
    // ���Υڡ������ڤ��ؤ���
    btnNext.on('click', function(){
        // ���Τ�1�ڡ��������ʤ����
        if (lastPage == 1){ return; }

        // ���ߤΥڡ������Ǹ��1�ڡ����ܤξ��
        if (currentPage == lastPage) {
            // �ǽ�Υڡ�����ɽ��
            changePage(lastPage, firstPage);
        } else {
            // ���Υڡ�����ɽ��
            changePage(currentPage, ++currentPage);
        }
    });

    var changePage = function(hidePage, showPage){
        $('div#mold-report-page[page="' + hidePage + '"]').removeClass('show-page');
        $('div#mold-report-page[page="' + showPage + '"]').addClass('show-page');
        labelCurrentPage.get(0).innerHTML = showPage
        currentPage = showPage;
    }
})();
