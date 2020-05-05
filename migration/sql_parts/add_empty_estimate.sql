DO $$
declare
    -- 見積原価マスタが存在しない受発注データの製品コード
    cur_product cursor for
        select distinct 
            A.strproductcode as strproductcode
           ,m_product.strrevisecode as strrevisecode
		   ,m_product.lnginchargeusercode
		   ,1 as lngproductionquantity
		   ,m_product.dtminsertdate
		   ,m_product.lngcustomercompanycode
		   ,m_product.dtmdeliverylimitdate
		   ,m_product.curproductprice
        from(
            select distinct strproductcode from t_receivedetail
            UNION
            select distinct strproductcode from t_orderdetail
        ) A

		inner join m_product
		    on m_product.strproductcode = A.strproductcode
			and m_product.strrevisecode = '00'
			and m_product.lngrevisionno = 0

        where A.strproductcode not in (select distinct strproductcode from m_estimate)
        order by 1;
    r_product RECORD;
    
    estimateno integer;
    m_count integer;
begin
    select MAX(lngestimateno) + 1 into estimateno from m_estimate;
    m_count = 0;
    open cur_product;
    LOOP
    
        FETCH cur_product INTO r_product;
        EXIT WHEN NOT FOUND;
-- 見積原価マスタ登録
--RAISE INFO '% % % %', estimateno + m_count, r_product.strproductcode, r_product.lngproductionquantity, r_product.curproductprice;

        insert into m_estimate(
            lngestimateno	-- 見積原価番号
           ,lngrevisionno	-- リビジョン番号
           ,strproductcode	-- 製品コード
           ,strrevisecode	-- 再販コード
           ,bytdecisionflag	-- 決定フラグ
           ,lngestimatestatuscode	-- 見積原価ステータス
           ,curfixedcost	-- 償却費合計
           ,curmembercost	-- 部材費
           ,curtotalprice	-- 売上総利益
           ,curmanufacturingcost	-- 製造費
           ,cursalesamount	-- 製品売上高
           ,curprofit	-- 営業利益
           ,lnginputusercode	-- 入力者
           ,bytinvalidflag	-- 無効フラグ
           ,dtminsertdate	-- 作成日時
           ,lngproductionquantity	-- 製品数量
           ,lngtempno	-- テンポラリNo
           ,strnote	-- 備考
           ,lngproductrevisionno	-- 製品リビジョン番号
           ,lngprintcount	-- 印刷回数
        )
        VALUES(
            estimateno + m_count	-- 見積原価番号
           ,0	-- リビジョン番号
           ,r_product.strproductcode	-- 製品コード
           ,r_product.strrevisecode	-- 再販コード
           ,true	-- 決定フラグ
           ,4	-- 見積原価ステータス
           ,0	-- 償却費合計
           ,0	-- 部材費
           ,0	-- 売上総利益
           ,0	-- 製造費
           ,0	-- 製品売上高
           ,0	-- 営業利益
           ,r_product.lnginchargeusercode	-- 入力者
           ,FALSE	-- 無効フラグ
           ,r_product.dtminsertdate	-- 作成日時
           ,r_product.lngproductionquantity	-- 製品数量
           ,NULL	-- テンポラリNo
           ,NULL	-- 備考
           ,0	-- 製品リビジョン番号
           ,0	-- 印刷回数
        );
        m_count = m_count + 1;
    END LOOP;
    close cur_product;
RAISE INFO 'add_empty_estimate completed';
END $$
