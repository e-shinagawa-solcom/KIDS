// NO19:企業コード				→属性（ユーザー管理:chiba)
SELECT count(*), count(*) FROM m_AttributeRelation WHERE lngCompanyCode = _%strFormValue0%_ AND lngAttributeCode = 2
