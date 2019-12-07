update
    m_mold
set
    deleteflag = true
where
    (moldno, vendercode, productcode, strrevisecode) in (
        select
            mm.moldno,
            mm.vendercode,
            mm.productcode
            mm.strrevisecode
        from
            m_mold mm
            LEFT OUTER JOIN (
                select
                    tsd.strmoldno,
                    s1.lngcustomercompanycode,
                    tsd.strproductcode,
                    tds.strrevisecode
                from
                    t_stockdetail tsd
                    inner join (
                        SELECT
                            s.lngStockno,
                            s.lngRevisionNo,
                            s.lngcustomercompanycode
                        from
                            m_Stock s
                        WHERE
                            s.lngRevisionNo = (
                                SELECT
                                    MAX (s2.lngRevisionNo)
                                FROM
                                    m_Stock s2
                                WHERE
                                    s.strStockCode = s2.strStockCode
                                    AND s2.bytInvalidFlag = false
                            )
                            AND 0 <= (
                                SELECT
                                    MIN (s3.lngRevisionNo)
                                FROM
                                    m_Stock s3
                                WHERE
                                    s.strStockCode = s3.strStockCode
                                    AND s3.bytInvalidFlag = false
                            )
                        order by
                            s.strStockCode,
                            s.lngRevisionNo
                    ) s1 on tsd.lngStockno = s1.lngStockno
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
                    tsd.strmoldno,
                    s1.lngcustomercompanycode,
                    tsd.strproductcode,
                    tds.strrevisecode
                order by
                    tsd.strproductcode,
                    tds.strrevisecode,
                    tsd.strmoldno
                    
            ) as src ON src.strmoldno = mm.moldno
        WHERE
            src.strmoldno is null
    );