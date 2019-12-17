UPDATE m_product
SET
    strgoodscode = $3
WHERE
    strproductcode = $1
    and strrevisecode = $2
AND (
        strgoodscode is null OR
        strgoodscode = ''
    )
RETURNING
    *
;
