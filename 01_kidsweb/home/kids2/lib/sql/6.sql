// No6: ���å���֥깩�쥳����	�� ���å���֥깩��̾(������Ͽ��watanabe)
SELECT c.strcompanydisplaycode, c.strcompanydisplayname FROM m_company c, m_attribute a, m_attributerelation al WHERE c.lngcompanycode = al.lngcompanycode AND al.lngattributecode = a.lngattributecode AND al.lngattributecode in (3,4) AND strcompanydisplaycode = '_%strFormValue0%_'
