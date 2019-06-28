
// SELECT���Ǵ֤�����Ѥ����Ǥ��ư����
function selectBoxMoveTo(src, dst)
{
    if (isSelectElement(src) && isSelectElement(dst))
    {
        var selected = $(src).find('option:selected');

        if (0 < selected.length)
        {
            // OPTION���Ǥΰ�ư
            $(dst).append(selected);
            // �о�SELECT���ǤΥ��٥�����
            $(src).trigger('change');
            $(dst).trigger('change');
        }
    }
}


function selectBoxCommand(selectbox, commandName)
{
    if (isSelectElement(selectbox))
    {
        var selected = $(selectbox).find('option:selected');

        if (0 < selected.length)
        {
            switch (commandName.toLowerCase())
            {
                case 'up':
                    // SELECT�����������Ѥ����Ǥν��֤�夲��
                    selected.first().prev().before(selected);
                    break;
                case 'down':
                    // SELECT�����������Ѥ����Ǥν��֤򲼤���
                    selected.last().next().after(selected);
                    break;
                case 'sort':
                    // value°������ʸ���󥽡��Ȥ򤫤���
                    var sortedOptions = selectbox.find('option').sort(function(a, b){
                        if (a.value < b.value) return -1;
                        if (b.value < a.value) return 1;
                        return 0;
                    });
                    // �����ȷ�̤�ȿ��
                    selectbox.append(sortedOptions);
                    break;
                default:
                    break
            }

            // �о�SELECT���ǤΥ��٥�����
            $(selectbox).trigger('change');
        }
    }
}

function isSelectElement(elm)
{
    return elm.prop('nodeName') === 'SELECT';
}

function isNotSelectElement(elm)
{
    return !isSelectElement(elm);
}
