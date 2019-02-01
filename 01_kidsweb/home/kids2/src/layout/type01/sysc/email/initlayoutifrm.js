<!--


//-----------------------------------------------------------------------------
// 概要 : ローカル変数定義
// 解説 :「TabIndex」値の設定 / ボタンイメージの設定
//-----------------------------------------------------------------------------

//------------------------------------------
// 適用箇所 :「サブウィンドウボタン」
//------------------------------------------
var NumTabA1   = ''   ; // vendor
var NumTabA1_2 = '' ; // creation
var NumTabA1_3 = '' ; // assembly
var NumTabB1   = ''  ; // dept
var NumTabC1   = ''   ; // products
var NumTabD1   = '' ; // location
var NumTabE1   = ''   ; // applicant
var NumTabF1   = ''   ; // wfinput
var NumTabG1   = '' ; // vi
var NumTabH1   = ''   ; // supplier
var NumTabI1   = ''   ; // input


//------------------------------------------
// 適用箇所 :「商品管理」タブ
//------------------------------------------
var PTabNumA = '' ; // A
var PTabNumB = '' ; // B


//------------------------------------------
// 適用箇所 :「受注・発注・売上・仕入」タブ
//------------------------------------------
var TabNumA = '' ; // ヘッダー
var TabNumB = '' ; // 明細


//------------------------------------------
// 適用箇所 :「登録ボタン」
//------------------------------------------
var RegistNum = '' ;


//------------------------------------------
// 適用箇所 :「行追加ボタン」
//------------------------------------------
var AddRowNum = '' ;


//------------------------------------------
// 適用箇所 :「カレンダーボタン」
//------------------------------------------
var NumDateTabA = '' ;
var NumDateTabB = '' ;
var NumDateTabC = '' ;


//------------------------------------------
// 適用箇所 :「製品数量ボタン」
//------------------------------------------
var NumPunitTab = '' ;


//------------------------------------------
// 解説 : 登録ボタン生成
//------------------------------------------
var blownRegiBtJ1 = '<a href="#"><img onmouseover="fncBlownRegistButton( \'onJ\' , this );" onmouseout="fncBlownRegistButton( \'offJ\' , this );fncAlphaOff( this );" onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" src="' + blownregistbtJ1 + '" width="72" height="20" border="0" alt="登録"></a>';

var blownRegiBtE1 = '<a href="#"><img onmouseover="fncBlownRegistButton( \'onE\' , this );" onmouseout="fncBlownRegistButton( \'offE\' , this );fncAlphaOff( this );" onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" src="' + blownregistbtE1 + '" width="72" height="20" border="0" alt="REGIST"></a>';


//------------------------------------------
// 解説 : 戻るボタン生成
//------------------------------------------
var BackBtJ1 = '<a href="#"><img onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onmouseover="BlownBackJOn(this);" onmouseout="BlownBackJOff(this);" src="' + blownbackJ1 + '" width="72" height="20" border="0" alt="戻る"></a>';
var BackBtE1 = '<a href="#"><img onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" onmouseover="BlownBackEOn(this);" onmouseout="BlownBackEOff(this);fncAlphaOff( this );" src="' + blownbackE1 + '" width="72" height="20" border="0" alt="BACK"></a>';


//-->