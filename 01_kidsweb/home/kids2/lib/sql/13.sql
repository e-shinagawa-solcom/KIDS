// No13:ô���ԥ�����			�� ô����̾(������Ͽ��watanabe)
SELECT u.struserdisplaycode,u.struserdisplayname FROM m_user u, m_company c WHERE u.lngcompanycode = c.lngcompanycode AND u.bytuserdisplayflag = true AND u.struserdisplaycode = '_%strFormValue0%_' AND c.strcompanydisplaycode = '_%strFormValue1%_'
