/*
 *    概要：属性が「5:納品場所・倉庫」又は「99:その他」の「表示会社コード」と「表示会社名」を取得
 *    対象：会社マスタ
 *
 */
SELECT
      mc.strcompanydisplaycode locationdisplaycode
    , mc.strcompanydisplayname locationdisplayname
FROM
    m_attributerelation mar
INNER JOIN
    m_company mc
  ON
    mar.lngcompanycode = mc.lngcompanycode
WHERE
    mar.lngattributecode in (5, 99)
ORDER BY
    strcompanydisplaycode
;
