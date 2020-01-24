/*
	概要：「売上区分類コード」から「売上区分」を取得
	対象：（共通）
	作成：千葉健司
	備考：「コード」から一致する「コード」を取得
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
