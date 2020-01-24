// No24:工場、納品場所倉庫コード	→納品場所名(発注検索：suzukaze)
SELECT c.strcompanydisplaycode,c.strcompanydisplayname FROM m_company c, m_attribute a, m_attributerelation al WHERE c.lngcompanycode = al.lngcompanycode AND al.lngattributecode = a.lngattributecode AND strcompanydisplaycode = '_%strFormValue0%_' AND (a.lngattributecode = 4 OR a.lngattributecode = 5)
