delete 
from
  t_estimatedetail
where
  exists ( 
    select
      lngestimateno
      , lngestimatedetailno
      , lngestimaterevisionno 
    from
      t_orderdetail 
    where
      lngorderno in ( 
        select
          lngorderno 
        from
          m_order 
        where
          lngorderstatuscode is null
      ) 
      and t_orderdetail.lngestimateno = t_estimatedetail.lngestimateno 
	  and t_orderdetail.lngestimatedetailno = t_estimatedetail.lngestimatedetailno
      and t_orderdetail.lngrevisionno = t_estimatedetail.lngrevisionno
  ) ;


delete 
from
  t_estimatedetail
where
  exists ( 
    select
      lngestimateno
      , lngestimatedetailno
      , lngestimaterevisionno 
    from
      t_receivedetail 
    where
      lngreceiveno in ( 
        select
          lngreceiveno 
        from
          m_receive 
        where
          lngreceivestatuscode is null
      ) 
      and t_receivedetail.lngestimateno = t_estimatedetail.lngestimateno 
	  and t_receivedetail.lngestimatedetailno = t_estimatedetail.lngestimatedetailno
      and t_receivedetail.lngrevisionno = t_estimatedetail.lngrevisionno
  ) ;
  
delete 
from
  m_estimatehistory 
where
  exists ( 
    select
      lngestimateno
      , lngestimatedetailno
      , lngestimaterevisionno 
    from
      t_receivedetail 
    where
      lngreceiveno in ( 
        select
          lngreceiveno 
        from
          m_receive 
        where
          lngreceivestatuscode is null
      ) 
      and t_receivedetail.lngestimateno = m_estimatehistory.lngestimateno 
      and t_receivedetail.lngestimatedetailno = m_estimatehistory.lngestimatedetailno 
      and t_receivedetail.lngrevisionno = m_estimatehistory.lngrevisionno
  ) ;
  
  delete 
from
  m_estimatehistory 
where
  exists ( 
    select
      lngestimateno
      , lngestimatedetailno
      , lngestimaterevisionno 
    from
      t_orderdetail 
    where
      lngorderno in ( 
        select
          lngorderno 
        from
          m_order 
        where
          lngorderstatuscode is null
      ) 
      and t_orderdetail.lngestimateno = m_estimatehistory.lngestimateno 
      and t_orderdetail.lngestimatedetailno = m_estimatehistory.lngestimatedetailno 
      and t_orderdetail.lngrevisionno = m_estimatehistory.lngrevisionno
  ) ;
  
  delete 
from
  t_orderdetail 
where
  lngorderno in ( 
    select
      lngorderno 
    from
      m_order 
    where
      lngorderstatuscode is null
  ); 

delete 
from
  m_order 
where
  lngorderstatuscode is null; 

delete 
from
  t_receivedetail 
where
  lngreceiveno in ( 
    select
      lngreceiveno 
    from
      m_receive 
    where
      lngreceivestatuscode is null
 );

update m_company 
set 
    lngcloseddaycode = 3
   ,strshortname = substr(strcompanydisplayname, 0, 5);

insert into m_group(lnggroupcode,lngcompanycode,strgroupname,bytgroupdisplayflag,strgroupdisplaycode,strgroupdisplayname,strgroupdisplaycolor) values (48,1,'開発',True,'dv','開発','#FFFFFF');
