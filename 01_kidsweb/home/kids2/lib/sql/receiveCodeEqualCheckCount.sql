/*
	概要：受注番号、同一値カウント
	対象：受注管理-受注登録
	作成：斎藤和志
	備考：入力された受注番号から一致する「同一受注番号のカウント数」を取得
*/
SELECT COUNT(*), COUNT(*) AS datacount
FROM m_Receive mr
WHERE
	mr.bytInvalidFlag = false
	AND mr.strReceiveCode = '_%strFormValue0%_'
--	AND mr.strReviseCode = '_%strFormValue1%_'
