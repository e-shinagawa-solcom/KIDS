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
// 解説 : アパッチ リスタートボタン生成
//------------------------------------------
var restartBtJ1 = '<a href="#"><img onmouseover="fncRestartButton( \'onJ\' , this );" onmouseout="fncRestartButton( \'offJ\' , this );fncAlphaOff( this );" onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" src="' + restartbtJ1 + '" width="151" height="25" border="0" alt="アパッチ再起動"></a>';

var restartBtE1 = '<a href="#"><img onmouseover="fncRestartButton( \'onE\' , this );" onmouseout="fncRestartButton( \'offE\' , this );fncAlphaOff( this );" onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" src="' + restartbtE1 + '" width="151" height="25" border="0" alt="APACHE RESTART"></a>';


//------------------------------------------
// 解説 : アパッチ ストップボタン生成
//------------------------------------------
var stopBtJ1 = '<a href="#"><img onmouseover="fncStopButton( \'onJ\' , this );" onmouseout="fncStopButton( \'offJ\' , this );fncAlphaOff( this );" onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" src="' + stopbtJ1 + '" width="151" height="25" border="0" alt="アパッチ停止"></a>';

var stopBtE1 = '<a href="#"><img onmouseover="fncStopButton( \'onE\' , this );" onmouseout="fncStopButton( \'offE\' , this );fncAlphaOff( this );" onmousedown="fncAlphaOn( this );" onmouseup="fncAlphaOff( this );" src="' + stopbtE1 + '" width="151" height="25" border="0" alt="APACHE STOP"></a>';


//-->