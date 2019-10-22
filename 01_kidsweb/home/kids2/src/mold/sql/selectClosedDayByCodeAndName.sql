/*
 *    ���ס��ֲ�ҥ����ɡפȡֲ��̾�פ���°������2:�����ϡ�99:����¾�פΡ����������פ��������
 *    �оݡ���ҥޥ��� �������ޥ���
 *
 *    �ץ졼���ۥ����
 *        $1����ҥ�����
 *        $2�����̾��
 *
 */
SELECT
      mc.strcompanydisplaycode customerdisplaycode
    , mc.strcompanydisplayname customerdisplayname
    , mcd.lngclosedday
FROM
    m_company mc
INNER JOIN
    m_closedday mcd
  ON
    mcd.lngcloseddaycode = mc.lngcloseddaycode
WHERE
    mc.strcompanydisplaycode = $1
AND mc.bytcompanydisplayflag = true
;