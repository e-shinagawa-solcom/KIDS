// No22:顧客、仕入先コード		→顧客名(発注検索：suzukaze)
SELECT c.strcompanydisplaycode,c.strcompanydisplayname FROM m_company c, m_attribute a, m_attributerelation al WHERE c.lngcompanycode = al.lngcompanycode AND al.lngattributecode = a.lngattributecode AND strcompanydisplaycode = '_%strFormValue0%_' AND (a.lngattributecode = 2 OR a.lngattributecode = 3)
