/*
	�T�v�F���i�R�[�h�A�d���ȖڃR�[�h�A�d�����i�R�[�h�A�ʉݒP�ʃR�[�h����P�����X�g�̎擾
	�ΏہF�����Ǘ��A�d���Ǘ�
	�쐬�F��ˋM��
	���l�F
*/
SELECT mpp.lngProductPriceCode, mpp.curProductPrice
FROM m_productprice mpp
JOIN m_monetaryunit mmu ON mpp.lngmonetaryunitcode = mmu.lngmonetaryunitcode
JOIN m_product mp ON mp.lngProductNo = mpp.lngProductNo
WHERE mp.strProductCode = '_%strFormValue0%_'
	AND mpp.lngStockSubjectCode = '_%strFormValue1%_'
	AND mpp.lngStockItemCode = '_%strFormValue2%_'
	AND mmu.strmonetaryunitsign = '_%strFormValue3%_'
ORDER BY mpp.lngProductPriceCode DESC
