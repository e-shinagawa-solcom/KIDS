



// 売上（納品書）登録
var regiJ1 = '/img/type01/sc/regist_off_ja_bt.gif';
var regiJ2 = '/img/type01/sc/regist_off_on_ja_bt.gif';
var regiJ3 = '/img/type01/sc/regist_on_ja_bt.gif';

// 売上検索
var schJA = '/img/type01/sc/search_off_ja_bt.gif';
var schJB = '/img/type01/sc/search_off_on_ja_bt.gif';
var schJC = '/img/type01/sc/search_on_ja_bt.gif';

// 納品書検索
var schJ1 = '/img/type01/sc/hoge_off_ja_bt.gif';
var schJ2 = '/img/type01/sc/hoge_off_on_ja_bt.gif';
var schJ3 = '/img/type01/sc/hoge_on_ja_bt.gif';

// 受注検索
var schJAA = '/img/type01/so/search_off_ja_bt.gif';
var schJBB = '/img/type01/so/search_off_on_ja_bt.gif';
var schJCC = '/img/type01/so/search_on_ja_bt.gif';

// 請求検索
var schJAAA = '/img/type01/inv/search_off_ja_bt.gif';
var schJBBB = '/img/type01/inv/search_off_on_ja_bt.gif';
var schJCCC = '/img/type01/inv/search_on_ja_bt.gif';

var schEA = '/img/type01/cmn/navi/search_off_en_bt.gif';
var schEB = '/img/type01/cmn/navi/search_off_on_en_bt.gif';
var schEC = '/img/type01/cmn/navi/search_on_en_bt.gif';






// 売上（納品書）登録
var reginaviJ1 = '<a href="#"><img onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onmouseover="RegiJOn(this);" onmouseout="RegiJOff(this);fncAlphaOff( this );" src="' + regiJ1 + '" width="151" height="25" border="0" alt="売上(納品書)登録"></a>';
var reginaviJ3 = '<img src="' + regiJ3 + '" width="151" height="25" border="0" alt="売上（納品書）登録">';

// 納品書検索
var schnaviJ1 = '<a href="#"><img onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onmouseover="SchJOn(this);" onmouseout="SchJOff(this);fncAlphaOff( this );" src="' + schJ1 + '" width="151" height="25" border="0" alt="納品書検索"></a>';

var schnaviJ3 = '<img src="' + schJ3 + '" width="151" height="25" border="0" alt="納品書検索">';

// 売上検索
var schnaviJA = '<a href="#"><img onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onmouseover="fncChangeBtnImg( this, schJB );" onmouseout="fncChangeBtnImg( this, schJA ); fncAlphaOff( this );" src="' + schJA + '" width="151" height="25" border="0" alt="売上検索"></a>';

var schnaviJC = '<img src="' + schJC + '" width="151" height="25" border="0" alt="売上検索">';



// 受注検索
var schnaviJAA = '<a href="#"><img onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onmouseover="fncChangeBtnImg( this, schJBB );" onmouseout="fncChangeBtnImg( this, schJAA ); fncAlphaOff( this );" src="' + schJAA + '" width="151" height="25" border="0" alt="売上検索"></a>';

var schnaviJCC = '<img src="' + schJC + '" width="151" height="25" border="0" alt="受注検索">';

// 請求検索
var reginaviJAA = '<a href="#"><img onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onmouseover="fncChangeBtnImg( this, schJBBB );" onmouseout="fncChangeBtnImg( this, schJAAA ); fncAlphaOff( this );" src="' + schJAAA + '" width="151" height="25" border="0" alt="請求検索"></a>';

var reginaviJCC = '<img src="' + schJCCC + '" width="151" height="25" border="0" alt="請求検索">';
