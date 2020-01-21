SELECT distinct
  mc.strcompanydisplaycode customerdisplaycode
  , mc.strcompanydisplayname customerdisplayname 
FROM
  m_receive mr 
  inner join ( 
    select
      max(lngrevisionno) lngrevisionno
      , strReceiveCode 
    from
      m_Receive 
    group by
      strReceiveCode
  ) mr1 
    on mr.strreceivecode = mr1.strReceiveCode 
    and mr.lngrevisionno = mr1.lngrevisionno 
  LEFT JOIN m_company mc 
    ON mr.lngcustomercompanycode = mc.lngcompanycode 
WHERE
  mr.bytinvalidflag = false 
  and mr.lngcustomercompanycode != 0
  and not exists ( 
    select
      strReceiveCode 
    FROM
      m_Receive 
    where
      lngRevisionNo < 0 
      and strReceiveCode = mr.strReceiveCode
  ) 
  AND mc.strcompanydisplaycode = $1
  AND mc.strcompanydisplayname LIKE '%' || $2 || '%'
ORDER BY
  mc.strcompanydisplaycode
;
