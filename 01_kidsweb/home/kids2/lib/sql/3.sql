// No3: 顧客コード				→ 顧客名(商品登録：watanabe)
SELECT c.strcompanydisplaycode,c.strcompanydisplayname FROM m_company c, m_attribute a, m_attributerelation al WHERE c.lngcompanycode = al.lngcompanycode AND al.lngattributecode = a.lngattributecode AND c.strcompanydisplaycode = '_%strFormValue0%_' AND a.lngattributecode = 2
