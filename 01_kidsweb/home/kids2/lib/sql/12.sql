/*
	概要：「表示ユーザーコード」から「ユーザーコード」を取得
	対象：ワークフロー管理
	作成：chiba
	更新：斎藤
	備考：「コード」から一致する「コード」を取得
*/
SELECT mu.lngUserCode, mu.lngUserCode
FROM m_user mu
WHERE
	mu.bytInvalidFlag = false
	AND mu.bytUserDisplayFlag = true
	AND mu.strUserDisplayCode = '_%strFormValue0%_'
