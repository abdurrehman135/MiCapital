
/* See all Client relations to Accounts */
CREATE OR REPLACE VIEW cliAcctRel AS
SELECT c.*,a.* FROM CLIENTS c
    INNER JOIN CLIACCT ca ON c.cliID = ca.cliID
    INNER JOIN ACCOUNTS a ON a.acctID = ca.acctID;

/* View Account details*/
CREATE OR REPLACE VIEW allAccounts AS
SELECT 
CASE 
    WHEN a.acctID = s.acctID THEN 'SAVINGS'
    WHEN a.acctID = c.acctID THEN 'CHEQUING'
    ELSE NULL END AS accountType
,a.*,s.INTERESTRATE,c.TRANSLIMIT FROM ACCOUNTS a
    FULL JOIN SAVINGSACCOUNT s ON a.acctID = s.acctID
    FULL JOIN CHEQUINGACCOUNT c ON a.acctID = c.acctID;

/* View Client-User data */
CREATE OR REPLACE VIEW cliUserRel AS
SELECT cu.userID,cu.privilege,c.cliID,c.fName,c.lName,cu.username,p.passhash,s.passsalt FROM CLIENTS c
    LEFT JOIN ONLINEUSER cu ON c.userId = cu.userId
    INNER JOIN PASSHASH p ON p.userId = cu.userId
    INNER JOIN PASSSALT s ON s.userID = cu.userID;

/* View Client-Address details */
CREATE OR REPLACE VIEW cliAddrRel AS
SELECT c.fName,c.lName,a.*,atyp.addrTypeDesc FROM CLIENTS c
    FULL JOIN ADDRESSES a ON c.cliID = a.cliID
	LEFT JOIN ADDRESSTYPEID atyp ON a.addrtypeid = atyp.addrtypeid;

/* View Client-Phone details */
CREATE OR REPLACE VIEW cliPhoneRel AS
SELECT c.cliID,c.fName,c.lName,pt.phoneTypeDesc,p.phoneNum,p.extension FROM PHONENUMBERS p
    FULL JOIN CLIENTS c ON p.cliID = c.cliID
    LEFT JOIN PHONETYPEID pt ON pt.phoneTypeID = p.phoneTypeID;