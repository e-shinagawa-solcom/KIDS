SELECT
     mm.vendercode
   , mc.strcompanydisplaycode as companydisplaycode
   , mc.strcompanydisplayname as companydisplayname
FROM
   m_mold mm
INNER JOIN
   m_company mc
  ON
   mm.vendercode = mc.lngcompanycode
WHERE
   mm.moldno = $1
;
