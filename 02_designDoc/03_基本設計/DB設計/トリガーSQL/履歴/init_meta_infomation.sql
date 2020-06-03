CREATE OR REPLACE FUNCTION public.init_meta_infomation()
 RETURNS trigger
 LANGUAGE plpgsql
AS $function$
                BEGIN
                    IF (TG_OP = 'INSERT') THEN

                        NEW.Created := current_timestamp;
                        NEW.Updated := current_timestamp;

                    ELSEIF (TG_OP = 'UPDATE') THEN

                        NEW.Updated := current_timestamp;
                        NEW.Version := NEW.version + 1;

                    END IF;
                    RETURN NEW;
                END;
                $function$
;


CREATE TRIGGER trg_biu_meta_businesscode BEFORE INSERT OR UPDATE ON m_businesscode FOR EACH ROW EXECUTE PROCEDURE init_meta_infomation();
CREATE TRIGGER trg_biu_meta_cache BEFORE INSERT OR UPDATE ON t_cache FOR EACH ROW EXECUTE PROCEDURE init_meta_infomation();
CREATE TRIGGER trg_biu_meta_mold BEFORE INSERT OR UPDATE ON m_mold FOR EACH ROW EXECUTE PROCEDURE init_meta_infomation();
CREATE TRIGGER trg_biu_meta_mold_history BEFORE INSERT OR UPDATE ON t_moldhistory FOR EACH ROW EXECUTE PROCEDURE init_meta_infomation();
CREATE TRIGGER trg_biu_meta_mold_report BEFORE INSERT OR UPDATE ON m_moldreport FOR EACH ROW EXECUTE PROCEDURE init_meta_infomation();
CREATE TRIGGER trg_biu_meta_mold_report_detail BEFORE INSERT OR UPDATE ON t_moldreportdetail FOR EACH ROW EXECUTE PROCEDURE init_meta_infomation();
CREATE TRIGGER trg_biu_meta_mold_report_relation BEFORE INSERT OR UPDATE ON t_moldreportrelation FOR EACH ROW EXECUTE PROCEDURE init_meta_infomation();
