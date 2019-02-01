UPDATE m_product
SET
    strgoodscode = $2
WHERE
    strproductcode = $1
AND (
        strgoodscode is null OR
        strgoodscode = ''
    )
RETURNING
    *
;
