// No13:担当者コード			→ 担当者名(商品登録：watanabe)
SELECT u.struserdisplaycode,u.struserdisplayname FROM m_user u, m_company c WHERE u.lngcompanycode = c.lngcompanycode AND u.bytuserdisplayflag = true AND u.struserdisplaycode = '_%strFormValue0%_' AND c.strcompanydisplaycode = '_%strFormValue1%_'
