/*
 *    ���ס��ֲ�ҥ����ɡפȡֲ��̾�פ���°������4:��������ϡ�99:����¾�פΡ�ɽ����ҥ����ɡפȡ�ɽ�����̾�פ����
 *    �оݡ���ҥޥ���
 *
 *    �ץ졼���ۥ����
 *        $1����ҥ�����
 *        $2�����̾��
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
    mc.strcompanydisplaycode = $1
AND mc.strcompanydisplayname LIKE '%' || $2 || '%'
AND mar.lngattributecode in (3, 4, 99)
;
