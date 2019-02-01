/*
	概要：「表示ユーザーコード」「表示ユーザー名」から「表示ユーザー名」を取得
	対象：ワークフロー管理
	作成：chiba
	更新：斎藤
	備考：「コード」「名称」から一致する「名称」を取得
	lngCompanyCode = 1   クワガタ様のみ
*/
SELECT mu.strUserDisplayCode,
	mu.strUserDisplayCode || ' ' || mu.strUserDisplayName AS strUserDisplayCodeName,
	mu.strUserDisplayName
FROM m_user mu
WHERE
	mu.lngCompanyCode = 1
	AND mu.bytInvalidFlag = false
	AND mu.bytUserDisplayFlag = true
	AND mu.strUserDisplayCode LIKE '%_%strFormValue0%_%'
	AND mu.strUserDisplayName LIKE '%_%strFormValue1%_%'
ORDER BY mu.strUserDisplayCode
