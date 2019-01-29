DROP FUNCTION IF EXISTS refererID;
DELIMITER $$
CREATE FUNCTION refererID(uid INT(6)) 
RETURNS varchar(25)
BEGIN
	DECLARE refurl VARCHAR(25);
	
	SET refurl = CONCAT( 'P', LPAD( uid, 6, 0) );
					
	RETURN refurl;
END;
$$
DELIMITER ;

DROP FUNCTION IF EXISTS resolveRefererID;
DELIMITER $$
CREATE FUNCTION resolveRefererID(refererkey VARCHAR(25)) 
RETURNS INT(6)
BEGIN
	DECLARE refID INT(6) DEFAULT 0;
	
	SET refererkey = REPLACE(refererkey,'P','');

	SET refID = refererkey;
					
	RETURN refID;
END;
$$
DELIMITER ;