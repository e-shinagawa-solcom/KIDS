//
// condition.js
//
jQuery(function($){
    
    // 親画面から引き継いだ顧客コードをセット
    var strDefaultCompanyDisplayCode = $('#strDefaultCompanyDisplayCode').val();
    if ( 0 < strDefaultCompanyDisplayCode.length){
        // 顧客コードをセット
        $('input[name="lngCustomerCode"]').val(strDefaultCompanyDisplayCode);
        // 顧客名の表示のためchangeイベントを手動発生
        $('input[name="lngCustomerCode"]').trigger('change');
    }
    
    // 納期の設定    
    var now = new Date();
    now.setDate(1);("00" + (now.getMonth() + 1)).slice(-2)
    var start = now.getFullYear() + '/' + ("00" + (now.getMonth() + 1)).slice(-2) + '/' + ("00" + now.getDate()).slice(-2);
    var date2 = new Date(now.getFullYear(), now.getMonth() + 1, 0);
    var end = date2.getFullYear() + '/' + ("00" + (date2.getMonth() + 1)).slice(-2) + '/' + date2.getDate();
    $('input[name="From_dtmDeliveryDate"]').val(start);
    $('input[name="To_dtmDeliveryDate"]').val(end);
    // ------------------------------------
    //  events
    // ------------------------------------
    // OKボタン
    $('#OkBt').on('click', function(){
        
        // ---------------------------
        //  入力値（検索条件）の収集
        // ---------------------------
        var search_condition = {
            strCompanyDisplayCode: $('input[name="lngCustomerCode"]').val(),
            strCompanyDisplayName: $('input[name="strCustomerName"]').val(),
            strCustomerReceiveCode: $('input[name="strCustomerReceiveCode"]').val(),
            lngReceiveNo: $('input[name="lngReceiveNo"]').val(),
            strReceiveDetailProductCode: $('input[name="strReceiveDetailProductCode"]').val(),
            strGoodsCode: $('input[name="strGoodsCode"]').val(),
            lngInChargeGroupCode: $('input[name="lngInChargeGroupCode"]').val(),
            strInChargeGroupName: $('input[name="strInChargeGroupName"]').val(),
            lngSalesClassCode: $('select[name="lngSalesClassCode"]').children('option:selected').val(),
            From_dtmDeliveryDate: $('input[name="From_dtmDeliveryDate"]').val(),
            To_dtmDeliveryDate: $('input[name="To_dtmDeliveryDate"]').val(),
            strNote: $('input[name="strNote"]').val(),
            IsIncludingResale: $('input[name="IsIncludingResale"]').prop("checked") ? 'On' : 'Off',
        };

        // --------------------------------------------------------------
        //   入力値のバリデーション
        // --------------------------------------------------------------
        if (!validateCondition(search_condition)){
            return false;
        }

        // --------------------------------------------------------------
        //   顧客コードまたは売上区分が初期値と異なる場合のチェック
        // --------------------------------------------------------------
        // 初期値の取得
        var strDefaultCompanyDisplayCode = $('#strDefaultCompanyDisplayCode').val();
        var lngDefaultSalesClassCode = $('#lngDefaultSalesClassCode').val();
        
        // チェックを必要とする条件を満たしているかどうか
        var needConfirm = ((0 < strDefaultCompanyDisplayCode.length) 
                           && (strDefaultCompanyDisplayCode != search_condition.strCompanyDisplayCode))
                          ||
                          ((0 < lngDefaultSalesClassCode.length) 
                           && (lngDefaultSalesClassCode != search_condition.lngSalesClassCode));

        // ユーザーに確認
        if (needConfirm){
            if (confirm("選択された明細を全てクリアしますが、よろしいですか？")){
                // 親画面の選択明細を全てクリア
                window.opener.ClearAllEditDetail();
            }else{
                //「キャンセル」が押下された場合は本画面を閉じる
                window.close();
            }
        }

        // --------------------------------------------------------------
        //   親画面に子画面の値を引き継いで明細検索を実行
        // --------------------------------------------------------------
        // 検索条件値設定
        window.opener.SetSearchConditionWindowValue(search_condition);
        // 明細検索実行
        window.opener.SearchReceiveDetail(search_condition);
        // 本画面を閉じる
        window.close();

    });

    // 閉じるボタン
    $('#CancelBt').on('click', function(){
        window.close();
    });

    // ------------------------------------
    //  functions
    // ------------------------------------
    // 入力された検索条件のバリデーション
    function validateCondition(cnd){

        // 顧客コード必須チェック
        if(!cnd.strCompanyDisplayCode){
            alert("顧客コードが未入力です");
            return false;
        }

        // FROM納期が不正
        if (cnd.From_dtmDeliveryDate){
            if (!isValidDate(cnd.From_dtmDeliveryDate)){
                alert("納期（FROM）の入力形式が不正です");
                return false;
            }
        }

        // TO納期が不正
        if (cnd.To_dtmDeliveryDate){
            if (!isValidDate(cnd.To_dtmDeliveryDate)){
                alert("納期（TO）の入力形式が不正です");
                return false;
            }
        }

        // FROM納期＞TO納期
        if (cnd.From_dtmDeliveryDate && cnd.To_dtmDeliveryDate){
            var from = new Date(cnd.From_dtmDeliveryDate);
            var to = new Date(cnd.To_dtmDeliveryDate);

            if (from.getTime() > to.getTime()){
                alert("納期（TO）が納期（FROM）より過去の日です");
                return false;
            }
        }

        return true;
                
    };

    // 日付書式チェック
    function isValidDate(text) {
        if (!/^\d{1,4}(\/|-)\d{1,2}\1\d{1,2}$/.test(text)) {
          return false;
        }
      
        const [year, month, day] = text.split(/\/|-/).map(v => parseInt(v, 10));
      
        return year >= 1
          && (1 <= month && month <= 12)
          && (1 <= day && day <= daysInMonth(year, month));
      
        function daysInMonth(year, month) {
          if (month === 2 && isLeapYear(year)) {
            return 29;
          }
      
          return {
            1: 31, 2: 28, 3: 31, 4: 30,
            5: 31, 6: 30, 7: 31, 8: 31,
            9: 30, 10: 31, 11: 30, 12: 31
          }[month];
        }
      
        function isLeapYear(year) {
          return ((year % 4 === 0) && (year % 100 !== 0)) || (year % 400 === 0);
        }
      };

});