SELECT
	to_char( mp.dtmdeliverylimitdate,'YYYY/MM/DD' ) as dtmdeliverylimitdate,	/* 納期（製品マスタ） */
	to_char( mst1.dtmdeliverydate,'YYYY/MM' ) as dtmdeliverydate2,			/* 納品日2（受注マスタ） */
	mst1.strSalesCode,								/* 売上コード */
	to_char( mst1.dtmAppropriationDate,'YYYY/MM/dd' ) as dtmAppropriationDate,	/* 計上日 */
	mst1.strCustomerReceiveCode,							/* 顧客受注番号 */
	mst1.lngmonetaryratecode,							/* レートコード */
	mst1.lngmonetaryunitcode,							/* 通貨コード */
	mst1.curConversionRate1 as curConversionRate,                        /* レート（TTM）*/
	mp.lngcategorycode,								/* カテゴリーコード */
	mc.strcategoryname,								/* カテゴリー名称 */
	mp.lngInchargeGroupCode,							/* 担当部門コード */
	mg.strGroupDisplayCode,								/* 部門表示コード */
	mg.strGroupDisplayName,								/* 部門表示名称 */
	mst1.lngsalesclasscode,								/* 売上区分 */
	ms.strsalesclassname,								/* 売上区分名称 */
	to_char( mst1.dtmdeliverydate,'YYYY/MM/DD' ) as dtmdeliverydate,		/* 納品日（売上マスタ） */
	mst1.curproductpriceSales,							/* 製品価格（売上明細） */
	mst1.lngProductUnitCode,							/* 製品単位コード */
	mst1.lngproductquantity,							/* 数量（売上マスタ） */
	mst1.cursubtotalprice,								/*明細行売上合計金額*/
	mp.strproductcode || '_' || mp.strrevisecode as strproductcode,								/* 製品コード */
	mp.strproductname,								/* 製品名称 */
	mp.lngproductionunitcode,							/* 予定数単位コード*/
	mp.lngProductionQuantity,							/* 生産予定数 */
	coalesce(mst2.curmembercost, 0) as curmembercost,				/* 部材費合計金額 */
	coalesce(mst2.curmanufacturingcost, 0) as curmanufacturingcost,			/* 総製造費用 */
	coalesce(mst2.cursalesamount, 0) as cursalesamount,				/* 予定売上高 */
	coalesce(mst2.curfixedcostsales, 0) as curfixedcostsales,
	coalesce(mst2.curtotalprice, 0) as curtotalprice,

	mp.lngcartonquantity,								/* カートン入数 */
	mp.curproductprice,								/* 商品価格-納価（商品マスタ） */
	mp.curretailprice,								/* 小売価格-上代 */
	mst1.strnote,
	mst1.lngsalesdivisioncode

FROM m_product mp
INNER JOIN (
    SELECT 
		lngproductno,
		MAX(lngrevisionno) as lngrevisionno
	FROM m_product
	GROUP BY lngproductno
) mp_rev
    ON mp_rev.lngproductno= mp.lngproductno
	AND mp_rev.lngrevisionno = mp.lngrevisionno
LEFT JOIN m_category mc ON mp.lngcategorycode = mc.lngcategorycode
LEFT JOIN m_group mg ON mp.lnginchargegroupcode = mg.lnggroupcode
,
(
	/* 受注マスタ・受注明細テーブルより有効なデータを取得 */
	SELECT
		ms.lngSalesno,
		ms.strSalesCode,				/* 売上コード */
		ms.lngmonetaryunitcode,				/* 通貨コード */
		ms.dtmAppropriationDate,			/* 計上日 */
		mr.strCustomerReceiveCode,			/* 顧客受注番号 */
		ms.lngmonetaryratecode,				/* レートコード */
		mstmm.curConversionRate1,			/* レート（TTM） */
		tsd.lngSalesDetailno,				/* 売上明細番号 */
		tsd.strproductcode,				/* 製品コード */
		tsd.strrevisecode,				/* 再販コード */
		tsd.lngsalesclasscode,				/* 売上区分 */
		msl.dtmdeliverydate,				/* 納期 */
		tsd.curproductprice as curproductpriceSales,	/* 単価 */
		tsd.lngProductUnitCode, 			/* 単位 */
		tsd.lngproductquantity,				/* 数量 */
		tsd.cursubtotalprice,				/*明細行売上合計金額*/
		ted.lngsalesdivisioncode,
		trd.strnote
	FROM m_Sales ms
	INNER JOIN m_slip msl on msl.strslipcode = ms.strslipcode
		and msl.lngsalesno = ms.lngsalesno
		and msl.lngrevisionno = ms.lngrevisionno
	LEFT JOIN t_SalesDetail tsd ON ms.lngSalesNo = tsd.lngSalesNo
		and ms.lngrevisionno = tsd.lngrevisionno
	LEFT JOIN m_Receive mr on mr.lngreceiveno = tsd.lngreceiveno 
		and mr.lngrevisionno = tsd.lngreceiverevisionno	/* 顧客受注番号取得の為 */
	LEFT JOIN t_receivedetail trd 
		on trd.lngreceiveno = mr.lngreceiveno
		and trd.lngrevisionno = mr.lngrevisionno
	LEFT JOIN t_estimatedetail ted
		on trd.lngestimateno = ted.lngestimateno
		and trd.lngestimatedetailno = ted.lngestimatedetailno
		and trd.lngestimaterevisionno = ted.lngrevisionno
	LEFT JOIN m_group mg ON ms.lnggroupcode = mg.lnggroupcode
	LEFT JOIN(	/* 社内レート、TTMレートを同行で取得する */
		SELECT DISTINCT
			mm1.lngmonetaryratecode as lngmonetaryratecode1,
			mm1.lngmonetaryunitcode as lngmonetaryunitcode1,
			mm1.dtmapplystartdate   as dtmapplystartdate1,
			mm1.dtmapplyenddate     as dtmapplyenddate1,
			mm1.curConversionRate   as curConversionRate1
		FROM
		(	/* TTMレート */
			SELECT DISTINCT
				mmr.lngmonetaryratecode,
				mmr.lngmonetaryunitcode,
				mmr.dtmapplystartdate,
				mmr.dtmapplyenddate,
				mmr.curConversionRate
			FROM m_MonetaryRate mmr
			JOIN m_monetaryunit mmu on mmr.lngmonetaryunitcode = mmu.lngmonetaryunitcode
			WHERE mmr.lngmonetaryratecode = 1
		) mm1
	) mstmm on /* 受注マスタの計上日でリンク */
		mstmm.dtmapplystartdate1 <= msl.dtmDeliveryDate
		AND mstmm.dtmapplyenddate1 >= msl.dtmDeliveryDate
		AND mstmm.lngmonetaryunitcode1 = ms.lngmonetaryunitcode
	,
	(
		SELECT r1.lngSalesNo, r1.strSalescode, r1.lngRevisionNo
		FROM m_Sales r1
		WHERE r1.strSalesCode NOT IN (
			SELECT r2.strSalesCode  FROM m_Sales r2
			where r2.lngRevisionNo < 0
			group by r2.strSalesCode
		)
		AND r1.lngRevisionNo = (
			SELECT MAX( rr1.lngRevisionNo ) FROM m_Sales rr1 
			WHERE rr1.strSalesCode = r1.strSalesCode 
			AND rr1.bytInvalidFlag = false
		)
	) mstrue
	WHERE ms.lngSalesNo = mstrue.lngSalesNo and ms.lngRevisionNo = mstrue.lngRevisionNo
	AND ms.bytInvalidFlag = FALSE
	AND ms.lngRevisionNo >= 0
	/* 売上マスタ・計上日に対する条件設定 */
	AND to_date( msl.dtmDeliveryDate::text,'YYYY/MM/DD' ) >= to_date( '_%dtmAppropriationDateFrom%_'::text, 'YYYY/MM/DD' ) /* FROM */
	AND to_date( msl.dtmDeliveryDate::text,'YYYY/MM/DD' ) <= to_date( '_%dtmAppropriationDateTo%_'::text, 'YYYY/MM/DD' ) /* TO */
) mst1
LEFT JOIN m_salesclass ms ON mst1.lngsalesclasscode = ms.lngsalesclasscode
LEFT JOIN(
	/* 見積原価マスタ・見積原価明細テーブルより有効なデータを取得 */
	SELECT
		me.strproductcode,		/* 製品コード */
		me.strrevisecode,		/* 再販コード */
 		me.curfixedcost,		/* 固定費合計金額 */
		me.curmembercost,		/* 部材費合計金額 */
		me.curmanufacturingcost,	/* 総製造費用  */
		me.cursalesamount,		/* 予定売上高 */
		tsum.curfixedcostsales,
		me.curtotalprice
	FROM m_estimate me
	INNER JOIN m_estimatehistory meh
	    ON meh.lngestimateno = me.lngestimateno
	    AND meh.lngrevisionno = me.lngrevisionno
	LEFT JOIN t_estimatedetail ted 
		ON ted.lngestimateno = meh.lngestimateno
		AND ted.lngestimatedetailno = meh.lngestimatedetailno
		AND ted.lngrevisionno = meh.lngestimatedetailrevisionno
	,
	(
		SELECT r1.lngestimateno, r1.lngrevisionno, r1.strproductcode
		FROM m_Estimate r1
		WHERE r1.lngestimateno NOT IN (
			SELECT r2.lngestimateno  FROM m_Estimate r2
			where r2.lngrevisionno < 0
			group by r2.lngestimateno
		)
		AND r1.lngrevisionno = (
			SELECT MAX( rr1.lngRevisionNo ) FROM m_Estimate rr1 
			WHERE rr1.lngEstimateNo = r1.lngEstimateNo 
			AND rr1.bytInvalidFlag = false
		)
	) metrue
    , ( 
        SELECT
          me.lngestimateno
          , me.lngrevisionno
          , SUM( 
            CASE 
              WHEN mscdl.lngestimateareaclassno = 2 
                THEN ted.cursubtotalprice * ted.curconversionrate 
              ELSE 0 
              END
          ) AS curfixedcostsales 
        FROM
          m_estimate me 
          INNER JOIN m_estimatehistory meh 
            on meh.lngestimateno = me.lngestimateno 
            and meh.lngrevisionno = me.lngrevisionno 
          INNER JOIN t_estimatedetail ted 
            ON ted.lngestimateno = meh.lngestimateno 
            and ted.lngestimatedetailno = meh.lngestimatedetailno 
            AND ted.lngrevisionno = meh.lngestimatedetailrevisionno 
          LEFT OUTER JOIN m_salesclassdivisonlink mscdl 
            ON mscdl.lngsalesclasscode = ted.lngsalesclasscode 
            AND mscdl.lngsalesdivisioncode = ted.lngsalesdivisioncode 
        GROUP BY
          me.lngestimateno
          , me.lngrevisionno
      ) tsum
	WHERE
		me.lngestimateno = metrue.lngestimateno
		AND me.lngrevisionno = metrue.lngrevisionno
		AND me.lngestimateno = tsum.lngestimateno
		AND me.lngrevisionno = tsum.lngrevisionno
		AND me.bytinvalidflag = FALSE
		AND me.bytdecisionflag = TRUE
		GROUP BY
			me.strproductcode,		/* 製品コード */
			me.strrevisecode,		/* 再販コード */
			me.curfixedcost,		/* 固定費合計金額 */
			me.curmembercost,		/* 部材費合計金額 */
			me.curmanufacturingcost,	/* 総製造費用  */
			me.cursalesamount,		/* 予定売上高 */
			tsum.curfixedcostsales,
			me.curtotalprice 
) mst2
	ON mst1.strproductcode = mst2.strproductcode
	AND mst1.strrevisecode = mst2.strrevisecode

WHERE
	mst1.strproductcode = mp.strproductcode
AND
	mst1.strrevisecode = mp.strrevisecode
AND
	CASE WHEN '0' = '_%lngInchargeGroupCode%_'
	THEN TRUE
	ELSE mp.lnginchargegroupcode = _%lngInchargeGroupCode%_
	END
AND
	CASE WHEN '0' = '_%lngSalesClassCode%_'
	THEN TRUE
	ELSE mst1.lngsalesclasscode in (_%lngSalesClassCode%_)
	END
ORDER BY 
mp.strproductcode, mp.strrevisecode, mst1.lngsalesclasscode