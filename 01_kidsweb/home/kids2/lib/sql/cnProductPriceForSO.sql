/*
	�T�v�F���i�R�[�h�A����敪�R�[�h�A�ʉݒP�ʃR�[�h����P�����X�g�̎擾
	�ΏہF�󒍊Ǘ��A����Ǘ�
	�쐬�F��ˋM��
	���l�F
*/
SELECT mpp.lngProductPriceCode, mpp.curProductPrice
FROM m_productprice mpp
JOIN m_monetaryunit mmu ON mpp.lngmonetaryunitcode = mmu.lngmonetaryunitcode
JOIN m_product mp ON mp.lngProductNo = mpp.lngProductNo
WHERE mp.strProductCode = '_%strFormValue0%_'
	AND mpp.lngsalesclasscode = '_%strFormValue1%_'
	AND mmu.strmonetaryunitsign = '_%strFormValue2%_'
ORDER BY mpp.lngProductPriceCode DESC
