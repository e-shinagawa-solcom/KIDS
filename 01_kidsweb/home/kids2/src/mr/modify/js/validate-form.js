
(function () {
    // フォーム
    var form = $('form[name="RegistMoldReport"]');
    // ヘッダタブ
    var header = $('div.regist-tab-header');
    // 詳細タブ
    var detail = $('div.regist-tab-detail');
    // エラーアイコンクラス名
    var classNameErrorIcon = 'error-icon';
    // エラーアイコンリソースURL
    var urlErrorIcon = '/img/type01/cmn/seg/seg_error_mark.gif';
    // 選択中の金型リストのラベル
    var labelChoosenMoldList = $('table.mold-selection')
        .find('tr:nth-of-type(1)')
        .find('th:nth-of-type(3)');

    // エラーメッセージ(必須項目)
    var msgRequired = "入力必須項目です。";
    // エラーメッセージ(日付)
    var msgDateFormat = "yyyy/mm/dd形式かつ有効な日付を入力してください。";
    var msgGreaterThanToday = "現在より先の日付しか入力できません。";
    var msgGreaterThanRequestDate = "希望日より先の日付しか入力できません。";
    // エラーメッセージ(移動先が保管元と同一工場)
    var msgSameFactory = "移動先工場に保管元工場と同じ工場を指定することはできません。";
    // 帳票その他欄の最大入力可能文字数
    var noteMaxLen = 38;
    // エラーメッセージ(その他の最大文字数まで)
    var msgNote = noteMaxLen + "文字までしか入力できません。"

    var regDate = /(19[0-9]{2}|2[0-9]{3})\/(0[1-9]|1[0-2])\/([0-2][0-9]|3[0-1])/;

    // validationキック
    $('.hasDatepicker').on({
        'change': function () {
            $(this).blur();
        }
    })

    // 希望日変更時、返却予定日のvalidationキック
    $('input[name="ActionRequestDate"]').on({
        'blur': function () {
            $('input[name="ReturnSchedule"]').blur();
        }
    })

    // 保管工場と移動先工場が不一致かどうか
    $.validator.addMethod(
        "difFactory",
        function (value, element, params) {
            return value != params.val();
        },
        msgSameFactory
    );

    // 日付がyyyy/mm/dd形式にマッチしているか,有効な日付か
    $.validator.addMethod(
        "checkDateFormat",
        function (value, element, params) {
            if (params) {
                if (value.length == 8) {
                    var str = value.trim();
                    var y = str.substr(0, 4);
                    var m = str.substr(4, 2);
                    var d = str.substr(6, 2);
                    value = y + "/" + m + "/" + d;
                }
                // yyyy/mm/dd形式か
                if (!(regDate.test(value))) {
                    return false;
                }

                var regResult = regDate.exec(value);
                var yyyy = regResult[1];
                var mm = regResult[2];
                var dd = regResult[3];
                var di = new Date(yyyy, mm - 1, dd);
                // 日付の有効性チェック
                if (di.getFullYear() == yyyy && di.getMonth() == mm - 1 && di.getDate() == dd) {
                    return true;
                } else {
                    return false;
                }
            }
            return true;
        },
        msgDateFormat
    );

    // 日付が過去でないか 当日不可
    $.validator.addMethod(
        "isGreaterThanToday",
        function (value, element, params) {
            if (params) {
                var regResult = regDate.exec(value);
                var yyyy = regResult[1];
                var mm = regResult[2];
                var dd = regResult[3];
                var di = new Date(yyyy, mm - 1, dd);
                // 現在の日時と比較
                var nowDi = new Date();
                // 入力した年が現在より小さければエラー
                if (nowDi.getFullYear() > di.getFullYear()) {
                    return false;
                    // 入力した年が現在より大きければ正
                } else if (nowDi.getFullYear() < di.getFullYear()) {
                    return true;
                    // 入力した年が現在と同じ場合
                } else if (nowDi.getFullYear() == di.getFullYear()) {
                    // 入力した月が現在より小さければエラー
                    if (nowDi.getMonth() > di.getMonth()) {
                        return false;
                        // 入力した月が現在より大きければ正
                    } else if (nowDi.getMonth() < di.getMonth()) {
                        return true;
                        // 入力した月が現在と同じ場合
                    } else if (nowDi.getMonth() == di.getMonth()) {
                        // 入力した日が現在と同じかそれより小さければエラー
                        if (nowDi.getDate() >= di.getDate()) {
                            return false;
                        }
                    }
                    return true;
                }
            }
            return true;
        },
        msgGreaterThanToday
    );

    // 返却予定日が希望日より大きいか(同日不可)
    $.validator.addMethod(
        "isGreaterThanRequestDate",
        function (value, element, params) {
            // 生産後の処理が20の場合チェック
            if (params) {
                // 希望日が入力されている場合チェック
                if ($('input[name="ActionRequestDate"]').val() != "") {
                    var actionRequestDate = $('input[name="ActionRequestDate"]').val();
                    var regResult = regDate.exec(actionRequestDate);
                    var yyyy = regResult[1];
                    var mm = regResult[2];
                    var dd = regResult[3];
                    var RequestDate = new Date(yyyy, mm - 1, dd);

                    regResult = regDate.exec(value);
                    yyyy = regResult[1];
                    mm = regResult[2];
                    dd = regResult[3];
                    var di = new Date(yyyy, mm - 1, dd);
                    // 希望日の日時と比較
                    // 入力した年が希望日より小さければエラー
                    if (RequestDate.getFullYear() > di.getFullYear()) {
                        return false;
                        // 入力した年が希望日より大きければ正
                    } else if (RequestDate.getFullYear() < di.getFullYear()) {
                        return true;
                        // 入力した年が現在と同じ場合
                    } else if (RequestDate.getFullYear() == di.getFullYear()) {
                        // 入力した月が現在より小さければエラー
                        if (RequestDate.getMonth() > di.getMonth()) {
                            return false;
                            // 入力した月が現在より大きければ正
                        } else if (RequestDate.getMonth() < di.getMonth()) {
                            return true;
                            // 入力した月が現在と同じ場合
                        } else if (RequestDate.getMonth() == di.getMonth()) {
                            // 入力した日が現在と同じかそれより小さければエラー
                            if (RequestDate.getDate() >= di.getDate()) {
                                return false;
                            }
                        }
                        return true;
                    }
                }
            }
            return true;
        },
        msgGreaterThanRequestDate
    );

    $.validator.addMethod(
        "maxlength",
        function (value, element, params) {
            // 未入力の場合チェックしない
            return !value ? true : (value.length <= params) ? true : false;
        },
        msgNote
    );

    // 返却予定日をチェックするかの条件
    $.validator.addMethod(
        "requiredWhenFinalKeepIsReturn",
        function (value, element, params) {
            // 生産後の処理が[20:返却]の場合必須
            return (($('select[name="FinalKeep"]')).val() != 20) ? true : value ? true : false;
        },
        msgRequired
    );

    // 検証設定
    form.validate({
        // -----------------------------------------------
        // エラー表示処理
        // -----------------------------------------------
        errorPlacement: function (error, element) {
            invalidImg = $('<img>')
                .attr('class', classNameErrorIcon)
                .attr('src', urlErrorIcon)
                // CSS設定(表示位置)
                .css({
                    position: 'absolute',
                    top: $(element).position().top,
                    left: $(element).position().left - 20,
                    opacity: 'inherit'
                })
                // ツールチップ表示
                .tooltipster({
                    trigger: 'hover',
                    onlyone: false,
                    position: 'top',
                    content: error.text()
                });

            // エラーアイコンが存在しない場合
            if ($(element).prev('img.' + classNameErrorIcon).length <= 0) {
                // エラーアイコンを表示
                $(element).before(invalidImg);
            }
            // エラーアイコンが存在する場合
            else {
                // 既存のエラーアイコンのツールチップテキストを更新
                $(element).prev('img.' + classNameErrorIcon)
                    .tooltipster('content', error.text());
            }
        },
        // -----------------------------------------------
        // 検証OK時の処理
        // -----------------------------------------------
        unhighlight: function (element) {
            // エラーアイコン削除
            $(element).prev('img.' + classNameErrorIcon).remove();
        },
        // -----------------------------------------------
        // 検証ルール
        // -----------------------------------------------
        rules: {
            // 製品コード
            ProductCode: {
                required: true
            },
            // 顧客品番
            GoodsCode: {
                required: true
            },
            // 帳票区分
            ReportCategory: {
                required: true
            },
            // 依頼日
            RequestDate: {
                checkDateFormat: true,
                required: true
            },
            // 依頼区分
            RequestCategory: {
                required: true
            },
            // 希望日
            ActionRequestDate: {
                checkDateFormat: true,
                isGreaterThanToday: true,
                required: true
            },
            // 移動方法
            TransferMethod: {
                required: true
            },
            // 指示区分
            InstructionCategory: {
                required: true
            },
            // 事業部(顧客)
            CustomerCode: {
                required: true
            },
            // KWG担当部署
            KuwagataGroupCode: {
                required: true
            },
            // KWG担当者
            KuwagataUserCode: {
                required: true
            },
            // 生産後の処理
            FinalKeep: {
                required: true
            },
            // 返却予定日 生産後の処理が返却の場合
            ReturnSchedule: {
                requiredWhenFinalKeepIsReturn: true,
                checkDateFormat: true,
                isGreaterThanToday: true,
                isGreaterThanRequestDate: true
            },
            // その他
            Note: {
                maxlength: noteMaxLen
            },
            // 選択済みの金型リスト
            // ChoosenMoldList: {
            //    required: true
            //},
            // 保管工場
            SourceFactory: {
                required: true
            },
            // 移動先工場
            DestinationFactory: {
                required: true,
                difFactory: $('input[name="SourceFactory"]')
            }
        },
        // -----------------------------------------------
        // エラーメッセージ
        // -----------------------------------------------
        messages: {
            // 製品コード
            ProductCode: {
                required: msgRequired
            },
            // 顧客品番
            GoodsCode: {
                required: msgRequired
            },
            // 帳票区分
            ReportCategory: {
                required: msgRequired
            },
            // 依頼日
            RequestDate: {
                required: msgRequired
            },
            // 依頼区分
            RequestCategory: {
                required: msgRequired
            },
            // 希望日
            ActionRequestDate: {
                required: msgRequired
            },
            // 移動方法
            TransferMethod: {
                required: msgRequired
            },
            // 指示区分
            InstructionCategory: {
                required: msgRequired
            },
            // 事業部(顧客)
            CustomerCode: {
                required: msgRequired
            },
            // KWG担当部署
            KuwagataGroupCode: {
                required: msgRequired
            },
            // KWG担当者
            KuwagataUserCode: {
                required: msgRequired
            },
            // 生産後の処理
            FinalKeep: {
                required: msgRequired
            },
            // 返却予定日 生産後の処理が返却の場合
            ReturnSchedule: {
                required: msgRequired
            },
            // 選択済みの金型リスト
            // ChoosenMoldList: {
            //    required: true
            //},
            // 保管工場
            SourceFactory: {
                required: msgRequired
            },
            // 移動先工場
            DestinationFactory: {
                required: msgRequired
            }
        }
    });
})();
