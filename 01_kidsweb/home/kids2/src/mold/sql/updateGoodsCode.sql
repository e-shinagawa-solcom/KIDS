UPDATE m_product
SET
    strgoodscode = $1
WHERE
    strproductcode = $2
    and strrevisecode = $3
AND (
        strgoodscode is null OR
        strgoodscode = ''
    )
RETURNING
    *
;
