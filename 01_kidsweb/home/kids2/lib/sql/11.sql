/*
	概要：「表示ユーザーコード」から「表示ユーザー名」を取得
	対象：ワークフロー管理
	作成：chiba
	備考：「コード」から一致する「名称」を取得
*/
SELECT strUserDisplayCode, strUserDisplayName FROM m_User WHERE strUserDisplayCode = '_%strFormValue0%_'
