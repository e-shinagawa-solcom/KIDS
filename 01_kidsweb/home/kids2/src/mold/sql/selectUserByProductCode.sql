SELECT
    mu.struserdisplaycode userdisplaycode
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
INNER JOIN
    m_user mu
  ON
    mp.lnginchargeusercode = mu.lngusercode
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