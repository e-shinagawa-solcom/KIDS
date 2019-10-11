/*
 *    ���ס��ֲ��̾�פ���°������4:��������ϡ�99:����¾�פΡ�ɽ����ҥ����ɡפȡ�ɽ�����̾�פ����
 *    �оݡ���ҥޥ���
 *
 *    �ץ졼���ۥ����
 *        $1�����̾
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
    mc.strcompanydisplayname LIKE '%' || $1 || '%'
AND mar.lngattributecode in (4, 99)
ORDER BY
    mc.strcompanydisplaycode
;
