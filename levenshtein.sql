DELIMITER //

CREATE FUNCTION levenshtein(s1 VARCHAR(255), s2 VARCHAR(255))
RETURNS INT
DETERMINISTIC
BEGIN
    DECLARE s1_len, s2_len, i, j, c, c_temp INT;
    DECLARE s1_char CHAR;
    DECLARE cv0, cv1 VARBINARY(256);

    SET s1_len = CHAR_LENGTH(s1),
        s2_len = CHAR_LENGTH(s2),
        cv1 = 0x00,
        j = 1,
        i = 1,
        c = 0;

    IF s1 = s2 THEN
        RETURN 0;
    ELSEIF s1_len = 0 THEN
        RETURN s2_len;
    ELSEIF s2_len = 0 THEN
        RETURN s1_len;
    ELSE
        WHILE j <= s2_len DO
            SET cv1 = CONCAT(cv1, UNHEX(HEX(j))), j = j + 1;
        END WHILE;

        WHILE i <= s1_len DO
            SET s1_char = SUBSTRING(s1, i, 1),
                c = i,
                cv0 = UNHEX(HEX(i)),
                j = 1;

            WHILE j <= s2_len DO
                SET c = c + 1;

                IF s1_char = SUBSTRING(s2, j, 1) THEN
                    SET c_temp = c;
                ELSE
                    SET c_temp = c + 1;
                    IF c > 1 AND levenshtein(s1_char, SUBSTRING(s2, j, 1)) > levenshtein(SUBSTRING(s1, i, 1), SUBSTRING(s2, j, 1)) THEN
                        SET c_temp = c + levenshtein(SUBSTRING(s1, i, 1), SUBSTRING(s2, j, 1));
                    END IF;
                END IF;

                IF HEX(SUBSTRING(cv1, j, 1)) = HEX(SUBSTRING(cv0, j, 1)) THEN
                    SET c = c_temp;
                ELSE
                    SET c = c + 1;
                    IF c_temp > 1 AND levenshtein(s1_char, SUBSTRING(s2, j, 1)) > levenshtein(SUBSTRING(s1, i, 1), SUBSTRING(s2, j, 1)) THEN
                        SET c = c + levenshtein(SUBSTRING(s1, i, 1), SUBSTRING(s2, j, 1));
                    END IF;
                END IF;

                SET cv0 = CONCAT(cv0, UNHEX(HEX(c))), j = j + 1;
            END WHILE;

            SET cv1 = cv0, i = i + 1;
        END WHILE;
    END IF;

    RETURN c;
END //

DELIMITER ;