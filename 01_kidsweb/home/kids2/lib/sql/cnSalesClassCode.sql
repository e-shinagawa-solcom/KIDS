/*
	���ס�������ʬ�ॳ���ɡפ��������ʬ�פ����
	�оݡ��ʶ��̡�
	���������շ��
	���͡��֥����ɡפ�����פ���֥����ɡפ����
*/
select
  sc.lngsalesclasscode
  , sc.lngsalesclasscode || ':' || sc.strsalesclassname 
from
  m_salesclass sc 
  left join m_salesclassdivisonlink sdl 
    on sc.lngsalesclasscode = sdl.lngsalesclasscode 
where
  sdl.lngsalesdivisioncode = '_%strFormValue0%_'
