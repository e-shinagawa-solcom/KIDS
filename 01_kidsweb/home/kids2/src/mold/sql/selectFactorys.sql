/*
 *    概要：属性が「4:工場」又は「99:その他」の「表示会社コード」と「表示会社名」を取得
 *    対象：会社マスタ
 *
 */
SELECT
      mc.strcompanydisplaycode companydisplaycode
    , mc.strcompanydisplayname companydisplayname
FROM
    m_attributerelation mar
INNER JOIN
    m_company mc
  ON
    mar.lngcompanycode = mc.lngcompanycode
WHERE
    mar.lngattributecode in (4, 99)
ORDER BY
    strcompanydisplaycode
;
