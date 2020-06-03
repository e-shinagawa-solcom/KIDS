CREATE OR REPLACE FUNCTION public.set_revision()
 RETURNS trigger
 LANGUAGE plpgsql
AS $function$
                BEGIN
                    IF (TG_OP = 'INSERT' AND TG_RELNAME = 'm_moldreport') THEN

                        NEW.Revision :=  get_next_value_revision(NEW.MoldReportId);

                    END IF;
                    RETURN NEW;
                END;
                $function$
;


CREATE TRIGGER trg_bi_revision_mold_report BEFORE INSERT ON m_moldreport FOR EACH ROW EXECUTE PROCEDURE set_revision();
