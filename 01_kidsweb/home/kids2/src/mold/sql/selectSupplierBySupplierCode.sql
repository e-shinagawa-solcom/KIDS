/*
 *    ���ס��ֲ�ҥ����ɡפ���°������3:����������ϡ�99:����¾�פΡ�ɽ����ҥ����ɡפȡ�ɽ�����̾�פ����
 *    �оݡ���ҥޥ���
 *
 *    �ץ졼���ۥ����
 *        $1����ҥ�����
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
    mc.strcompanydisplaycode = $1
AND mar.lngattributecode in (3, 99)
;
