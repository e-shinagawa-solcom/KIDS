/*
 *    ���ס�°������5:Ǽ�ʾ�ꡦ�Ҹˡ����ϡ�99:����¾�פΡ�ɽ����ҥ����ɡפȡ�ɽ�����̾�פ����
 *    �оݡ���ҥޥ���
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