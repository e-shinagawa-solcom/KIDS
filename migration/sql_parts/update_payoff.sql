update t_estimatedetail
set bytpayofftargetflag = true

--select lngestimateno, lngestimatedetailno, lngrevisionno from t_estimatedetail
where (lngestimateno, lngestimatedetailno, lngrevisionno) in
(
    select te.lngestimateno, te.lngestimatedetailno, te.lngrevisionno
    from t_estimatedetail te
    where te.lngrevisionno = 1 
    and (te.lngstocksubjectcode, te.lngstockitemcode ) in 
    (
        select lngstocksubjectcode, lngstockitemcode from m_stockitem where lngestimateareaclassno = 3
    )
    and te.lngestimateno in 
    (
        select 
            lngestimateno
        from(
            select 
                te.lngestimateno
               ,count (te.*)
            from t_estimatedetail te
            inner join m_stockitem msi
                on msi.lngstockitemcode = te.lngstockitemcode
                and msi.lngstocksubjectcode = te.lngstocksubjectcode
            where te.lngrevisionno = 0
                and msi.lngestimateareaclassno = 3
                and te.bytpayofftargetflag = true
            group by te.lngestimateno
        )V0
        UNION
        select lngestimateno from m_estimate me
        inner join m_product mp
            on mp.strproductcode = me.strproductcode
            and mp.strrevisecode = me.strrevisecode
            and mp.lngrevisionno = me.lngrevisionno
        where me.lngrevisionno = 1
            and me.lngestimateno not in 
            (
                select distinct
                    te.lngestimateno
                from t_estimatedetail te
                inner join m_stockitem msi
                    on msi.lngstockitemcode = te.lngstockitemcode
                    and msi.lngstocksubjectcode = te.lngstocksubjectcode
                where te.lngrevisionno = 0
                    and msi.lngestimateareaclassno = 3 
                    and te.bytpayofftargetflag = true
                group by te.lngestimateno
            )
            and mp.lnginchargegroupcode in (2,3,45,46,48)
    )
)