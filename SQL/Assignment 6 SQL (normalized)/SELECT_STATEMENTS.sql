
-- SELECT everything from ACCOUNTS where the BILLINGDAY is less than 60 days from now.
SELECT * FROM ACCOUNTS WHERE BILLINGDAY - (SELECT sysdate from DUAL) < 120;
-- SELECT everything FROM ADDRESS where ADDRESSTYPEID refers to a mailing address,
SELECT * FROM ADDRESSES a WHERE a.ADDRTYPEID IN
    (SELECT at.ADDRTYPEID FROM ADDRESSTYPEID at WHERE at.ADDRTYPEDESC LIKE 'MAILING%');
-- SELECT all cities that clients live in.
SELECT DISTINCT city FROM ADDRESSES;
-- SELECT the number of accounts for a given CLIENT, as well as some of their information. Concatenates fName and lName.
SELECT ca.cliID AS "Client ID", c.fName || ' ' || c.lName AS "Name", COUNT( acctID ) AS "Number Of Accounts" FROM CLIACCT ca
    INNER JOIN CLIENTS c ON c.cliID = ca.cliID
    GROUP BY ca.cliID,c.fName,c.lName
    ORDER BY ca.cliID;
-- SELECT everything from CLIENTS whose CLIID is 3 or higher.
SELECT * FROM CLIENTS WHERE CLIID > 2;
-- SELECT everything about a USER
SELECT * FROM ONLINEUSER cu
    INNER JOIN PASSHASH USING (userId)
    INNER JOIN PASSSALT USING (userId);
-- SELECT all 'WORK' PHONENUMBERS
SELECT phoneNum AS "Phone Number" FROM PHONENUMBERS WHERE PHONETYPEID = (SELECT PHONETYPEID FROM PHONETYPEID WHERE phonetypedesc = 'WORK');
-- SELECT the number of phone numbers of each type
SELECT pt.phoneTypeDesc AS "Phone Type",COUNT(p.phoneNum) AS "# Records" FROM PHONENUMBERS p
    INNER JOIN PHONETYPEID pt USING (phoneTypeId)
    GROUP BY phoneTypeDesc
    ORDER BY "# Records" ASC, PhoneTypeDesc DESC;
-- SELECT specific fields from TRANSACTIONS where the SENDERACCTID is 1.
SELECT SENDERACCTID AS "Account 1",RECEIVERACCTID AS "Account 2",TRANSVALUE AS "Amount" FROM TRANSACTIONS WHERE SENDERACCTID = 1;
-- SELECT all values from all lookup tables, and a column indicating the table them came from
SELECT 'ADDRESS' AS "Lookup Table",ADDRTYPEID AS "Lookup Key",ADDRTYPEDESC AS "Description" FROM ADDRESSTYPEID
UNION ALL
    SELECT 'PHONE',PhONETYPEID, PHONETYPEDESC FROM PHONETYPEID
UNION ALL
    SELECT 'TRANSACTION',TRANSTYPEID,TRANSTYPEDESC FROM TRANSTYPEID;

-- SELECT FROM views
SELECT cliID,fname||' '||lname AS "NAME",CLIOPENDATE,acctID,BALANCE,BILLINGDAY,ACCTOPENDATE FROM CLIACCTREL WHERE CLIID = 1;
SELECT ACCOUNTTYPE,ACCTID,BALANCE,BILLINGDAY,ACCTOPENDATE,INTERESTRATE,TRANSLIMIT AS "MAX TRANSACTION" FROM ALLACCOUNTS ORDER BY ACCOUNTTYPE ASC;
SELECT * FROM CLIUSERREL ORDER BY PASSSALT DESC;
SELECT * FROM CLIPHONEREL WHERE PHONETYPEDESC = 'CELL';
SELECT fName || ' ' || lName AS "Name", cliID AS "Client ID", addrLine1 || ' ' || addrLine2 AS "Address", city
, stateprovince AS "State/Province", COUNTRY FROM CLIADDRREL WHERE ADDRTYPEID IN ( SELECT ADDRTYPEID FROM ADDRESSTYPEID
    WHERE ADDRTYPEDESC LIKE 'MAILING%') ORDER BY cliID;

-- more complex SELECT FROM views
SELECT ca.cliID,ca.fName,ca.lName,aa.acctId,aa.accountType,aa.balance,aa.interestRate,aa.transLimit
FROM CLIACCTREL ca INNER JOIN ALLACCOUNTS aa ON ca.acctID = aa.acctID;