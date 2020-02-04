DO $$
declare

cur_odtl CURSOR FOR
select m_order.strordercode, t_orderdetail.lngorderno, t_orderdetail.lngorderdetailno, t_orderdetail.lngrevisionno
from t_orderdetail
inner join m_order
on m_order.lngorderno = t_orderdetail.lngorderno
and m_order.lngrevisionno = t_orderdetail.lngrevisionno
order by m_order.strordercode, m_order.lngrevisionno, t_orderdetail.lngorderdetailno;

detail RECORD;
detail_no integer;
last_order text;
last_revision integer;

begin
last_revision = -1;
last_order = '';
last_revision = 0;
open cur_odtl;
LOOP
    FETCH cur_odtl into detail;
    EXIT WHEN NOT FOUND;
    IF last_revision <> detail.lngrevisionno OR last_order <> detail.strordercode THEN
        detail_no = 0;
        last_revision = detail.lngrevisionno;
        last_order = detail.strordercode;
    END IF;
    detail_no = detail_no + 1;
    -- 発注明細の明細番号再採番
    update t_orderdetail
    set lngorderdetailno = detail_no
       ,lngsortkey = detail_no
    where lngorderno = detail.lngorderno
        and lngorderdetailno = detail.lngorderdetailno
        and lngrevisionno = detail.lngrevisionno;
    
    update t_purchaseorderdetail
    set lngorderdetailno = detail_no
       ,lngorderrevisionno = detail_no
    where lngorderno = detail.lngorderno
        and lngorderdetailno = detail.lngorderdetailno
        and lngorderrevisionno = detail.lngrevisionno;

    update t_stockdetail
    set lngorderdetailno = detail_no
       ,lngorderrevisionno = detail_no
    where lngorderno = detail.lngorderno
        and lngorderdetailno = detail.lngorderdetailno
        and lngorderrevisionno = detail.lngrevisionno;

END LOOP;
close cur_odtl;

END $$