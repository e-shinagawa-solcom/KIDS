update m_mold 
set
  deleteflag = false
  , updated = tmp_object.current
  , vendercode = tmp_object.lngcustomercompanycode
  , productcode = tmp_object.strproductcode
  , strrevisecode = tmp_object.strrevisecode 
from
  ( 
    select
      src.strmoldno
      , src.lngcustomercompanycode
      , src.strproductcode
      , src.strrevisecode
      , src.current 
    from
      ( 
        select
          tsd.strmoldno
          , s1.lngcustomercompanycode
          , tsd.strproductcode
          , tsd.strrevisecode
          , to_timestamp($1,'YYYY/MM/DD HH24:MI:SS.MS') as current
        from
          t_stockdetail tsd 
          inner join ( 
            SELECT
              s.lngStockno
              , s.lngRevisionNo
              , s.lngcustomercompanycode 
            from
              m_Stock s 
              inner join ( 
                SELECT
                  lngstockno
                  , MAX(lngRevisionNo) lngrevisionno 
                FROM
                  m_Stock 
                WHERE
                  bytInvalidFlag = false 
                GROUP BY
                  lngstockno 
                HAVING
                  MIN(lngrevisionno) >= 0
              ) max_stock 
                on s.lngstockno = max_stock.lngstockno 
                and s.lngrevisionno = max_stock.lngrevisionno 
            order by
              s.strStockCode
              , s.lngRevisionNo
          ) s1 
            on tsd.lngStockno = s1.lngStockno 
            and tsd.lngRevisionNo = s1.lngRevisionNo 
        where
          tsd.strmoldno is not null 
          AND ( 
            ( 
              tsd.lngStockItemCode = 1 
              AND tsd.lngStockSubjectCode = 433
            ) 
            OR ( 
              tsd.lngStockItemCode = 8 
              AND tsd.lngStockSubjectCode = 431
            )
          ) 
        group by
          tsd.strmoldno
          , s1.lngcustomercompanycode
          , tsd.strproductcode
          , tsd.strrevisecode 
        order by
          tsd.strproductcode
          , tsd.strmoldno
      ) as src 
      LEFT OUTER JOIN m_mold mm 
        ON src.strmoldno = mm.moldno 
    WHERE
      mm.moldno is not null 
    ORDER BY
      src.strmoldno
  ) tmp_object 
where
  moldno = tmp_object.strmoldno
;