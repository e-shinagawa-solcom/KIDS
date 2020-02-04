DO $$
declare
    cur_company cursor for
    select
        m_company.lngcompanycode
       ,case m_company.lngcountrycode 
           when 81 then 
               case when strpos(m_company.strcompanyname,'事業部') > 0
                   then 1
               else 
                   case when strpos(m_company.strcompanyname,'バンダイ') > 0
                       then 1
                   else
                       case when strpos(upper(m_company.strcompanyname),'BANDAI') > 0
                           then 1
                       else
                           2
                       end
                   end
               end
           else 3 
        end as lngslipkindcode 
    from m_attributerelation
    inner join m_company
        on m_company.lngcompanycode = m_attributerelation.lngcompanycode
    where m_attributerelation.lngattributecode = 2;

    write_count integer;
    row RECORD;
begin
    write_count = 1;
    open cur_company;
    LOOP
        FETCH cur_company INTO row;
        EXIT WHEN NOT FOUND;
        insert into m_slipkindrelation
        (
            lngslipkindrelationcode
           ,lngcompanycode
           ,lngslipkindcode
        )
        values
        (
            write_count
           ,row.lngcompanycode
           ,row.lngslipkindcode
        );
        write_count = write_count + 1;
    END LOOP;
    close cur_company;
END $$