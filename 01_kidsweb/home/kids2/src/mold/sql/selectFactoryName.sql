/*
 *    概要：「表示会社コード」から属性が「4:工場」又は「99:その他」の「表示会社名」を取得
 *    対象：会社マスタ
 *
 *    プレースホルダ：
 *        $1：会社コード
 *
 */
SELECT
    mc.strcompanydisplayname companydisplayname
FROM
    m_attributerelation mar
INNER JOIN
    m_company mc
  ON
    mar.lngcompanycode = mc.lngcompanycode
WHERE
      mc.strcompanydisplaycode = $1
  AND mar.lngattributecode in (4, 99)
;
