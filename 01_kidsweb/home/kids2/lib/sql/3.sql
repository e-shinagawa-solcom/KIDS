// No3: ¸ÜµÒ¥³¡¼¥É				¢ª ¸ÜµÒÌ¾(¾¦ÉÊÅÐÏ¿¡§watanabe)
SELECT c.strcompanydisplaycode,c.strcompanydisplayname FROM m_company c, m_attribute a, m_attributerelation al WHERE c.lngcompanycode = al.lngcompanycode AND al.lngattributecode = a.lngattributecode AND c.strcompanydisplaycode = '_%strFormValue0%_' AND a.lngattributecode = 2
