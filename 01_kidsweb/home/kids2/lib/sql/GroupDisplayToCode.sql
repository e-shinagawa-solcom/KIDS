//
//	概要：グループ検索
//	対象：コード+名称その他
//	作成：涼風敬二
//	備考：表示グループコードよりグループコードの取得
//
SELECT lngGroupCode, lngGroupCode FROM m_Group WHERE strGroupDisplayCode = '_%strFormValue0%_'
