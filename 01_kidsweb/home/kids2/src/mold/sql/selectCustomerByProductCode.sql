SELECT
  mc.strcompanydisplaycode companydisplaycode 
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
  INNER JOIN m_company mc 
    ON mp.lngcustomercompanycode = mc.lngcompanycode 
where
  mp.strproductcode = $1 
  and mp.strrevisecode = $2 
  and not exists ( 
    select
      strproductcode 
    from
      m_product 
    where
      lngrevisionno < 0 
      and strproductcode = mp.strproductcode
  ) 
;