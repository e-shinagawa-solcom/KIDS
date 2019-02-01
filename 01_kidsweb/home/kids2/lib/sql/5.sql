// No5: À¸»º¹©¾ì¥³¡¼¥É			¢ª À¸»º¹©¾ìÌ¾(¾¦ÉÊÅÐÏ¿¡§watanabe)
SELECT c.strcompanydisplaycode, c.strcompanydisplayname FROM m_company c, m_attribute a, m_attributerelation al WHERE c.lngcompanycode = al.lngcompanycode AND al.lngattributecode = a.lngattributecode AND al.lngattributecode in (3,4) AND strcompanydisplaycode = '_%strFormValue0%_'
