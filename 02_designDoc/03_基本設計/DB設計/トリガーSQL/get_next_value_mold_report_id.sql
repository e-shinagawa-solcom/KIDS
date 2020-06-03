CREATE OR REPLACE FUNCTION public.get_next_value_mold_report_id()
 RETURNS trigger
 LANGUAGE plpgsql
AS $function$
                DECLARE
                    monthlyReportCounts integer;

                BEGIN
                    IF (TG_OP = 'INSERT') THEN
                    
                        SELECT
                            COUNT(*) + 1
                        INTO
                            monthlyReportCounts
                        FROM
                            m_moldreport
                        WHERE
                            created between 
                                    DATE_TRUNC('month', now())
                                and DATE_TRUNC('month', now()) + '1 month' +'-0.000001 Second'
                        ;
                        
                        
                        NEW.moldreportid = trim(to_char(NEW.created, 'YYYYMM')) || trim(to_char(monthlyReportCounts, '0000'));

                    END IF;
                    RETURN NEW;
                END;
                $function$
;

CREATE TRIGGER trg_xbi_id_mold_report BEFORE INSERT ON m_moldreport FOR EACH ROW EXECUTE PROCEDURE get_next_value_mold_report_id();
