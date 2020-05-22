select distinct
  s.strmoldno
  , s.strproductcode
  , s.strrevisecode 
from
  t_stockdetail s 
  inner join ( 
    select
      max(lngrevisionno) lngrevisionno
      , lngstockno 
    from
      m_Stock 
    where
      bytInvalidFlag = FALSE 
    group by
      lngstockno
  ) s1 
    on s.lngrevisionno = s1.lngrevisionno 
    and s.lngstockno = s1.lngstockno 
where
  s.strproductcode = $1
  and s.strmoldno is not null
  and s.strmoldno != ''
  AND not exists ( 
    select
      s2.lngstockno 
    from
      ( 
        SELECT
          min(lngRevisionNo) lngRevisionNo
          , lngstockno 
        FROM
          m_Stock 
        group by
          lngstockno
      ) as s2 
    where
      s2.lngstockno = s.lngstockno 
      AND s2.lngRevisionNo < 0
  ) 
order by
  s.strproductcode
  , s.strrevisecode
;
