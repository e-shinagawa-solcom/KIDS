

//@-------------------------------------------------------------------------------------------------------------------
/**
* ファイル概要 : LC関連処理
*
*
*
* @package k.i.d.s.
* @license http://www.wiseknot.co.jp/
* @copyright Copyright &copy; 2004, Wiseknot, Inc.
* @author Ryosuke Tomita <r-tomita@wiseknot.co.jp>
* @access public
* @version 0.1
*/
//--------------------------------------------------------------------------------------------------------------------

//共通変数
var session_id;
var phpData;// PHPからの取得データ
var settingData;
window.onerror = false;
//---------------------------------------------------
// 画面初期処理
//---------------------------------------------------
function lcInit( json_obj )
{
	phpData = JSON.parse(json_obj);
	if(phpData.lgoutymd == null && phpData.userAuth == 1){
		//反映ボタン使用可
		$("#reflectionBtn").prop("disabled",false);
	} else {
		//反映ボタン使用不可
		$("#reflectionBtn").prop("disabled",true);
	}

	//画面情報取得
	getInfo();
}

//---------------------------------------------------
// 画面情報取得
//---------------------------------------------------
function getInfo()
{
	//LC情報取得
	$("#masking_loader").css("display","block");
	$.ajax({
		url:'../lcModel/lcset_ajax.php',
		type:'POST',
		data:{
			'method': 'getLcSetting',
			'sessionid': phpData["session_id"]
		}
	})
	.done(function(data) {
		// Ajaxリクエストが成功
		var data = JSON.parse(data);
		settingData = data;//データの確保

		//基準日
		$("#baseOpenDateTxt").val(data.base_open_date);

		//取引先銀行情報挿入
		setBankInfo(data.bank_info);

		//仕入れ先情報挿入
		setPayfInfo(data.payf_info);

		//ローダー解除
		$("#masking_loader").css("display","none");
	})
	.fail(function() {  
		// Ajaxリクエストが失敗
	});
}

//---------------------------------------------------
//取引先銀行情報挿入
//---------------------------------------------------
function setBankInfo(bank_info){
	//既存データの削除
	$("#bank_body").empty();
	for(var i = 0; i < bank_info.length; i++){
		var data = bank_info[i];
	
		var invalidflag = "";
		if(data.invalidflag == "t"){
			invalidflag = "checked";
		}
		var html = "<tr id='bank_data_"+ data.bankcd +"'>" + 
			"<td><input type='text' class='form-control form-control-sm' name='bankcd' value='" + data.bankcd + "' ></td>" + 
			"<td><input type='text' class='form-control form-control-sm' name='bankomitname' value='" + data.bankomitname + "' ></td>" + 
			"<td><input type='text' class='form-control form-control-sm' name='bankformalname' value='" + data.bankformalname + "' ></td>" + 
			"<td><input type='text' class='form-control form-control-sm' name='bankdivrate' value='" + data.bankdivrate + "' ></td>" + 
			"<td><input type='checkbox' name='invalidflag' value='1' " + invalidflag + " style='width: 11px;margin-top: 10px;margin-left: 25px;'></td>" + 
			"</tr>";
		$("#bank_body").append(html);
	}
}

//---------------------------------------------------
//仕入れ先情報挿入
//---------------------------------------------------
function setPayfInfo(payf_info){
	//既存データの削除
	$("#payf_body").empty();
	for(var i = 0; i < payf_info.length; i++){
		var data = payf_info[i];
	
		var invalidflag = "";
		if(data.invalidflag == "t"){
			invalidflag = "checked";
		}
		var html = "<tr id='payf_data_"+ data.payfcd +"'>" + 
			"<td><input type='text' class='form-control form-control-sm' name='payfcd' value='" + data.payfcd + "' ></td>" + 
			"<td><input type='text' class='form-control form-control-sm' name='payfomitname' value='" + data.payfomitname + "' ></td>" + 
			"<td><input type='text' class='form-control form-control-sm' name='payfformalname' value='" + data.payfformalname + "' ></td>" + 
			"<td><input type='text' class='form-control form-control-sm' name='payfsendname' value='" + data.payfsendname + "' ></td>" + 
			"<td><input type='text' class='form-control form-control-sm' name='payfsendfax' value='" + data.payfsendfax + "' ></td>" + 
			"<td><input type='checkbox' name='invalidflag' value='1' " + invalidflag + "  style='width: 11px;margin-top: 10px;margin-left: 35px;'></td>" + 
			"</tr>";
		$("#payf_body").append(html);
	}
}

//---------------------------------------------------
//追加ボタンイベント
//---------------------------------------------------
function addPayfInfo(){
	try {
		//入力チェック
		var payfcd = $("#payfcdTxt").val();
		exp = "^[0-9]{4}$";
		if(payfcd == "") {
			alert("支払先コードを入力してください。");
			throw new Error();
		}
		if(!payfcd.match(exp)) {
			alert("支払先コードは4桁の半角数字で入力してください。");
			throw new Error();
		}
		for(var i = 0; i < settingData.payf_info.length; i++){
			if(settingData.payf_info[i].payfcd == payfcd){
				alert("入力された支払先コードはすでに登録されています。");
				throw new Error();
			}
		}

		//新規行の追加
		var html = "<tr id='payf_data_"+ payfcd +"'>" + 
			"<td><input type='text' class='form-control form-control-sm' name='payfcd' value='" + payfcd + "' ></td>" + 
			"<td><input type='text' class='form-control form-control-sm' name='payfomitname' value='' ></td>" + 
			"<td><input type='text' class='form-control form-control-sm' name='payfformalname' value='' ></td>" + 
			"<td><input type='text' class='form-control form-control-sm' name='payfsendname' value='' ></td>" + 
			"<td><input type='text' class='form-control form-control-sm' name='payfsendfax' value='' ></td>" + 
			"<td><input type='checkbox' name='invalidflag' value='1'></td>" + 
			"</tr>";
		$("#payf_body").append(html);

		//マスタデータに追加
		var new_data = {
			payfcd: payfcd,
			payfformalname: "",
			payfno: "",
			payfomitname: "",
			payfsendfax: "",
			payfsendname: "",
			invalidflag: ""
		};
		settingData.payf_info.push(new_data);

		//一番下までスクロール
		$('#payf_body').animate({scrollTop: $('#payf_body')[0].scrollHeight}, 'fast');

		//追加した行の支払い先省略名にフォーカス
		$("#payf_body #payf_data_"+ payfcd + " input[name='payfomitname']").focus();

		//支払先コード入力欄をクリア
		$("#payfcdTxt").val("");
	 
	} catch (e) {
		//支払先コードにフォーカス
		$("#payfcdTxt").focus();
	}
}

//---------------------------------------------------
//削除ボタンイベント
//---------------------------------------------------
function delPayfInfo(){
	try {
		//入力チェック
		var payfcd = $("#payfcdTxt").val();
		exp = "^[0-9]{4}$";
		if(payfcd == "") {
			alert("支払先コードを入力してください。");
			throw new Error();
		}
		if(!payfcd.match(exp)) {
			alert("支払先コードは4桁の半角数字で入力してください。");
			throw new Error();
		}
		var hit = false;
		for(var i = 0; i < settingData.payf_info.length; i++){
			if(settingData.payf_info[i].payfcd == payfcd){
				hit = true;

				//マスタデータの削除フラグを立てる
				settingData.payf_info[i].del_flg = true;

				//テーブルからデータを削除payf_data_1234
				$("#payf_data_"+ payfcd).remove();
			}
		}

		if(!hit){
			alert("対象の支払先がありません。");
			throw new Error();
		}
	 
	} catch (e) {
	}
}

//---------------------------------------------------
//再読み込み
//---------------------------------------------------
function reloadInfo(){
	if( res = confirm("再読込をしますか。") )
	{
		getInfo();
	}
}

//---------------------------------------------------
//再読み込み
//---------------------------------------------------
function reloadInfo(){
	if( res = confirm("再読込をしますか。") )
	{
		getInfo();
	}
}

//---------------------------------------------------
//反映処理
//---------------------------------------------------
function reflection(){
	try {
		if( res = confirm("データを更新しますか。") )
		{
			//送信データ
			var sendData = {};
			//反映フラグ取得
			var bankInfoChk = $("#bankInfoChk").prop('checked');
			var payfInfoChk = $("#payfInfoChk").prop('checked');
			var baseOpenDateChk = $("#baseOpenDateChk").prop('checked');
			sendData["bankInfoChk"] = bankInfoChk;
			sendData["payfInfoChk"] = payfInfoChk;
			sendData["baseOpenDateChk"] = baseOpenDateChk;

			if(!bankInfoChk && !payfInfoChk && !baseOpenDateChk){
				alert("反映する対象をチェックしてください。");
				throw new Error();
			}

			//入力チェック
			//基準日
			if(baseOpenDateChk){
				//日が空の場合
				var val = $("#baseOpenDateTxt").val();
				if(val == ""){
					alert("基準日が入力されていません。");
					$("#baseOpenDateTxt").focus();
					throw new Error();
				} else {
					//日が30を超えた場合
					if(val > 30){
						alert("基準日は30日以下で入力してください。");
						$("#baseOpenDateTxt").focus();
						throw new Error();
					}
				}
				sendData["baseOpenDate"] = val;
			}
			//銀行情報
			if(bankInfoChk){
				//入力データの取得
				var row_dom = $("#bank_body").find("tr");
				var maxbankdivrate = 0;

				for(var i=0; i < settingData.bank_info.length;i++){console.log("test22");
					var bankcd = settingData.bank_info[i].bankcd;
					//行のDOM取得
					var row = $("#bank_data_"+ bankcd);
					var bankcd = row.find("input[name='bankcd']").val();
					var bankomitname = row.find("input[name='bankomitname']").val();
					var bankformalname = row.find("input[name='bankformalname']").val();
					var bankdivrate = parseFloat(row.find("input[name='bankdivrate']").val());
					if(bankdivrate == NaN)bankdivrate = "";
					var invalidflag = row.find("input[name='invalidflag']").prop('checked');
					
					//入力チェック
					//無効フラグがfalseの場合、かつ割振率が0の場合
					if(invalidflag == false && bankdivrate == 0){
						alert("有効な銀行情報の割振率を0にすることは出来ません。");
						row.find("input[name='bankdivrate']").focus();
						throw new Error();
					}
					//銀行コード,銀行名省略名称,銀行名正式名称,割振率のいずれが空の場合
					if(bankcd === "" || bankomitname === "" || bankformalname === "" || bankdivrate === ""){
						alert("銀行情報に未入力の項目が存在します。");
						row.find("input[name='bankcd']").focus();
						throw new Error();
					}
					//グリッドの件数が3件超えだった場合 ToDo 提供されたDBには4件のデータがあったが3件しか取得しないのか？
					
					//マスタデータの値を更新
					settingData.bank_info[i].bankcd = bankcd;
					settingData.bank_info[i].bankomitname = bankomitname;
					settingData.bank_info[i].bankformalname = bankformalname;
					settingData.bank_info[i].bankdivrate = bankdivrate;
					settingData.bank_info[i].invalidflag = invalidflag;

					maxbankdivrate += bankdivrate;
				}
				//グリッドに割振率の合計は１ではない場合
				if(maxbankdivrate > 1){
					alert("割振率の合計が1を超えています。");
					throw new Error();
				}
				
				//送信データとしてセット
				sendData["bank_info"] = settingData.bank_info;
			}

			//仕入れ先情報
			if(payfInfoChk){
				//入力データの取得
				var row_dom = $("#payf_body").find("tr");
				var maxbankdivrate = 0;

				for(var i=0; i < settingData.payf_info.length;i++){
					//削除フラグが立っていなければ入力値をマスタデータに反映
					if(settingData.payf_info[i].del_flg != true){
						var payfcd = settingData.payf_info[i].payfcd;
						//行のDOM取得
						var row = $("#payf_data_"+ payfcd);
						var payfcd = row.find("input[name='payfcd']").val();
						var payfomitname = row.find("input[name='payfomitname']").val();
						var payfformalname = row.find("input[name='payfformalname']").val();
						var payfsendname = row.find("input[name='payfsendname']").val();
						var payfsendfax = row.find("input[name='payfsendfax']").val();
						var invalidflag = row.find("input[name='invalidflag']").prop('checked');
						
						//入力チェック
						//支払先コード,支払先省略名称,支払先正式名称のいずれが空の場合
						if(payfcd === "" || payfomitname === "" || payfformalname === ""){
							alert("仕入先情報に未入力の項目が存在します。");
							row.find("input[name='payfcd']").focus();
							throw new Error();
						}
						
						//マスタデータの値を更新
						settingData.payf_info[i].payfcd = payfcd;
						settingData.payf_info[i].payfomitname = payfomitname;
						settingData.payf_info[i].payfformalname = payfformalname;
						settingData.payf_info[i].payfsendname = payfsendname;
						settingData.payf_info[i].payfsendfax = payfsendfax;
						settingData.payf_info[i].invalidflag = invalidflag;
					}
				}
				
				//送信データとしてセット
				sendData["payf_info"] = settingData.payf_info;
			}

			//反映データ送信
			$("#masking_loader").css("display","block");
			$.ajax({
				url:'../lcModel/lcset_ajax.php',
				type:'POST',
				data:{
					'method': 'updateLcSetting',
					'phpData': phpData,
					'send_data': sendData,
					'sessionid': phpData["session_id"]
				}
			})
			.done(function(data) {
				alert(data);
				// Ajaxリクエストが成功
				var data = JSON.parse(data);
			
				alert("更新設定が完了しました。");
			})
			.fail(function() {  
				// Ajaxリクエストが失敗
			});
			
			//ローダー解除
			$("#masking_loader").css("display","none");
		}
	} catch (e) {
		console.log(e);
	}
}