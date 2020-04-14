
// SELECT要素間の選択済み要素を移動する
function selectBoxMoveTo(src, dst)
{
    if (isSelectElement(src) && isSelectElement(dst))
    {
        var selected = $(src).find('option:selected');

        if (0 < selected.length)
        {
            // OPTION要素の移動
            $(dst).append(selected);
            // 対象SELECT要素のイベント着火
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
                    // SELECT要素内の選択済み要素の順番を上げる
                    selected.first().prev().before(selected);
                    break;
                case 'down':
                    // SELECT要素内の選択済み要素の順番を下げる
                    selected.last().next().after(selected);
                    break;
                case 'sort':
                    // value属性を基に文字列ソートをかける
                    var sortedOptions = selectbox.find('option').sort(function(a, b){
                        if (a.value < b.value) return -1;
                        if (b.value < a.value) return 1;
                        return 0;
                    });
                    // ソート結果を反映
                    selectbox.append(sortedOptions);
                    selectbox.focus();
                    break;
                default:
                    break
            }

            // 対象SELECT要素のイベント着火
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
