/*
 *    ���ס�°������3:����������ϡ�99:����¾�פΡ�ɽ����ҥ����ɡפȡ�ɽ�����̾�פ����
 *    �оݡ���ҥޥ���
 *
 */
SELECT
      mc.strcompanydisplaycode supplierdisplaycode
    , mc.strcompanydisplayname supplierdisplayname
FROM
    m_attributerelation mar
INNER JOIN
    m_company mc
  ON
    mar.lngcompanycode = mc.lngcompanycode
WHERE
    mar.lngattributecode in (3, 99)
ORDER BY
    strcompanydisplaycode
;
