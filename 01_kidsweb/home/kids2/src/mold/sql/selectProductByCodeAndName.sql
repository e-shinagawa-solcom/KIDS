SELECT
  mp.strproductcode as productcode
  , mp.strrevisecode as revisecode
  , mp.strproductname as productname 
FROM
  m_product mp 
  inner join ( 
    select
      max(lngrevisionno) lngrevisionno
      , strproductcode
      , strrevisecode 
    from
      m_product 
    where
      bytInvalidFlag = false 
    group by
      strproductcode
      , strrevisecode
  ) mp1 
    on mp.strproductcode = mp1.strproductcode 
    and mp.lngrevisionno = mp1.lngrevisionno 
    and mp.strrevisecode = mp1.strrevisecode 
where
  mp.strproductcode = $1
  AND mp.strrevisecode = $2
  AND mp.strproductname LIKE '%' || $3 || '%'
  and not exists ( 
    select
      strproductcode 
    from
      m_product 
    where
      lngrevisionno < 0 
      and strproductcode = mp.strproductcode
  ) 
order by
  mp.strproductcode
  , mp.strrevisecode
;