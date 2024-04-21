-- Lookup table inserts
INSERT ALL
    INTO ADDRESSTYPEID (addrTypeID,addrTypeDesc) VALUES (1,'MAILING')
    INTO ADDRESSTYPEID (addrTypeID,addrTypeDesc) VALUES (2,'BILLING')
    INTO ADDRESSTYPEID (addrTypeID,addrTypeDesc) VALUES (3,'MAILING+BILLING')
    INTO PHONETYPEID (phoneTypeID,phoneTypeDesc) VALUES (1,'HOME')
    INTO PHONETYPEID (phoneTypeID,phoneTypeDesc) VALUES (2,'CELL')
	INTO PHONETYPEID (phoneTypeID,phoneTypeDesc) VALUES (3,'WORK')
    INTO PHONETYPEID (phoneTypeID,phoneTypeDesc) VALUES (4,'FAX')
    INTO TRANSTYPEID (transTypeID,transTypeDesc) VALUES (1,'DEPOSIT')
    INTO TRANSTYPEID (transTypeID,transTypeDesc) VALUES (2,'WITHDRAW')
    INTO TRANSTYPEID (transTypeID,transTypeDesc) VALUES (3,'TRANSFER')
SELECT * FROM dual;
/* SELECT * FROM dual statement is needed to end this INSERT ALL.
    To summarize, we're making a table of things to insert into the table,
    and by selecting from dual is like saying "insert each of those rows 1 time"
*/

-- data INSERTS for cliID = 1
-- using Sequences with INSERT ALL does not function well, so we'll use INSERT INTO statements and NEXTVAL / CURRVAL instead.
INSERT INTO ONLINEUSER (userId,USERNAME,PRIVILEGE) VALUES (userID_Seq.NEXTVAL,'msiddiqui',0);
INSERT INTO CLIENTS (cliID,FNAME,LNAME,USERID,CLIOPENDATE) VALUES (cliID_Seq.NEXTVAL,'Muhammad','Siddiqui',userID_Seq.CURRVAL,sysdate);
INSERT INTO PASSHASH (USERID,PASSHASH) VALUES (userID_Seq.CURRVAL,'123');
INSERT INTO PASSSALT (USERID,PASSSALT) VALUES (userID_Seq.CURRVAL,'abc');
INSERT INTO ACCOUNTS (ACCTID,BALANCE,BILLINGDAY,ACCTOPENDATE) VALUES (acctID_Seq.NEXTVAL,400,ADD_MONTHS(sysdate,6),sysdate);
INSERT INTO SAVINGSACCOUNT (ACCTID,INTERESTRATE) VALUES (acctID_Seq.CURRVAL,0.05);
INSERT INTO CLIACCT (CLIID,ACCTID) VALUES (cliID_Seq.CURRVAL,acctID_Seq.CURRVAL);
INSERT INTO ACCOUNTS (ACCTID,BALANCE,BILLINGDAY,ACCTOPENDATE) VALUES (acctID_Seq.NEXTVAL,0,ADD_MONTHS(sysdate,6),sysdate);
INSERT INTO CHEQUINGACCOUNT (ACCTID,TRANSLIMIT) VALUES (acctID_Seq.CURRVAL,2000);
INSERT INTO CLIACCT (CLIID,ACCTID) VALUES (cliID_Seq.CURRVAL,acctID_Seq.CURRVAL);

-- data INSERTS for cliID = 2
INSERT INTO ONLINEUSER (userID,USERNAME,PRIVILEGE) VALUES (userId_Seq.NEXTVAL,'client',0);
INSERT INTO CLIENTS (cliID,FNAME,LNAME,userId,CLIOPENDATE) VALUES (cliID_Seq.NEXTVAL,'Client','Guy',userID_Seq.CURRVAL,sysdate);
INSERT INTO PASSHASH (userID,PASSHASH) VALUES (userID_Seq.CURRVAL,'123');
INSERT INTO PASSSALT (userID,PASSSALT) VALUES (userID_Seq.CURRVAL,'abc');
INSERT INTO ACCOUNTS (ACCTID,BALANCE,BILLINGDAY,ACCTOPENDATE) VALUES (acctID_Seq.NEXTVAL,500,ADD_MONTHS(sysdate,2),sysdate);
INSERT INTO CHEQUINGACCOUNT (ACCTID,TRANSLIMIT) VALUES (acctID_Seq.CURRVAL,10000);
INSERT INTO CLIACCT (CLIID,ACCTID) VALUES (cliID_Seq.CURRVAL,acctID_Seq.CURRVAL);

-- data INSERTS for cliID = 3
INSERT INTO ONLINEUSER (userID,USERNAME,PRIVILEGE) VALUES (userID_Seq.NEXTVAL,'notSuperman@dailyplanet.net',0);
INSERT INTO CLIENTS (cliID,FNAME,LNAME,userId,CLIOPENDATE) VALUES (cliID_Seq.NEXTVAL,'Clark','Kent',userID_Seq.CURRVAL,sysdate);
INSERT INTO PASSHASH (userID,PASSHASH) VALUES (userID_Seq.CURRVAL,'123');
INSERT INTO PASSSALT (userID,PASSSALT) VALUES (userID_Seq.CURRVAL,'abc');
INSERT INTO ACCOUNTS (ACCTID,BALANCE,BILLINGDAY,ACCTOPENDATE) VALUES (acctID_Seq.NEXTVAL,350,ADD_MONTHS(sysdate,7),sysdate);
INSERT INTO SAVINGSACCOUNT (ACCTID,INTERESTRATE) VALUES (acctID_Seq.CURRVAL,0.03);
INSERT INTO CLIACCT (CLIID,ACCTID) VALUES (cliID_Seq.CURRVAL,acctID_Seq.CURRVAL);

/*
Let's add a few users with higher privilege (i.e. Bank employees)
In a real project, there would be different levels of access, probably controlled through another system,
but for here, lets assume clients are privilege 0, bankers 1, and sysadmins 2.
*/
INSERT INTO ONLINEUSER(USERID,USERNAME,PRIVILEGE) VALUES (userID_Seq.NEXTVAL,'admin1',2);
INSERT INTO PASSHASH (userID,PASSHASH) VALUES (userID_Seq.CURRVAL,'123');
INSERT INTO PASSSALT (userID,PASSSALT) VALUES (userID_Seq.CURRVAL,'abc');

INSERT INTO ONLINEUSER(USERID,USERNAME,PRIVILEGE) VALUES (userID_Seq.NEXTVAL,'admin2',2);
INSERT INTO PASSHASH (userID,PASSHASH) VALUES (userID_Seq.CURRVAL,'123');
INSERT INTO PASSSALT (userID,PASSSALT) VALUES (userID_Seq.CURRVAL,'abc');

INSERT INTO ONLINEUSER(USERID,USERNAME,PRIVILEGE) VALUES (userID_Seq.NEXTVAL,'banker1',1);
INSERT INTO PASSHASH (userID,PASSHASH) VALUES (userID_Seq.CURRVAL,'123');
INSERT INTO PASSSALT (userID,PASSSALT) VALUES (userID_Seq.CURRVAL,'abc');

/*
Miscellaneous inserts to pad 1-N entity relationships
*/
INSERT INTO ADDRESSES (cliID,addrTypeID,addrLine1,addrLine2,city,stateProvince,country,postalCode)
	VALUES ((SELECT cliID FROM CLIENTS WHERE FNAME = 'Muhammad'),3,'350 Victoria Street','Ryerson University','Toronto','Ontario','Canada','M5B2K3');
INSERT INTO ADDRESSES (cliID,addrTypeID,addrLine1,addrLine2,city,stateProvince,country,postalCode)
	VALUES ((SELECT cliID FROM CLIENTS WHERE FNAME = 'Client'),3,'123 Sesame Street',NULL,'Mississauga','Ontario','Canada','A1B2C3');
INSERT INTO ADDRESSES (cliID,addrTypeID,addrLine1,addrLine2,city,stateProvince,country,postalCode)
	VALUES ((SELECT cliID FROM CLIENTS WHERE FNAME = 'Clark'),3,'334 Clinton Street','Apartment 3B','Metropolis','New York','USA','10001');

INSERT INTO PHONENUMBERS (cliID,phoneTypeID,phoneNum,extension)
	VALUES ((SELECT cliID FROM CLIENTS WHERE FNAME = 'Muhammad'),1,'1112223434',NULL);
INSERT INTO PHONENUMBERS (cliID,phoneTypeID,phoneNum,extension)
	VALUES ((SELECT cliID FROM CLIENTS WHERE FNAME = 'Muhammad'),2,'1234567890',NULL);
INSERT INTO PHONENUMBERS (cliID,phoneTypeID,phoneNum,extension)
	VALUES ((SELECT cliID FROM CLIENTS WHERE FNAME = 'Client'),2,'9998887654',NULL);
INSERT INTO PHONENUMBERS (cliID,phoneTypeID,phoneNum,extension)
	VALUES ((SELECT cliID FROM CLIENTS WHERE FNAME = 'Clark'),3,'5555555555','1234');
INSERT INTO PHONENUMBERS (cliID,phoneTypeID,phoneNum,extension)
	VALUES ((SELECT cliID FROM CLIENTS WHERE FNAME = 'Clark'),4,'1111111111','4321');

/* We can't really insert multiple TRANSACTIONS in one INSERT ALL statement, because we use a TIMESTAMP as part of the primary key.
    INSERT ALL statement treats all inserts as simultaneously occuring,
    so if we do multiple transactions on the same account we'll have a primary key conflict.
*/
INSERT INTO TRANSACTIONS (transID,transTypeID,senderAcctID,receiverAcctID,transValue,transDateTime)
        VALUES (
			transId_Seq.NEXTVAL
            ,1
            ,1
            ,NULL
            ,100
            ,systimestamp);
INSERT INTO TRANSACTIONS (transID,transTypeID,senderAcctID,receiverAcctID,transValue,transDateTime)
    VALUES (
		transId_Seq.NEXTVAL
        ,(SELECT transtypeid FROM TRANSTYPEID WHERE TRANSTYPEDESC = 'DEPOSIT')
        ,(SELECT  a.acctID FROM ACCOUNTS a
            INNER JOIN CLIACCT ca ON a.acctID = ca.acctID
            INNER JOIN CLIENTS c ON c.cliID = ca.cliID
            WHERE c.fName = 'Muhammad' AND ROWNUM = 1)
        ,NULL
        ,500
        ,systimestamp);
INSERT INTO TRANSACTIONS (transID,transTypeID,senderAcctID,receiverAcctID,transValue,transDateTime)
    VALUES (
		transID_Seq.NEXTVAL
		,(SELECT transtypeid FROM TRANSTYPEID WHERE TRANSTYPEDESC = 'TRANSFER')
        ,1
        ,2
        ,200
        ,systimestamp);
INSERT INTO TRANSACTIONS (transID,transTypeID,senderAcctID,receiverAcctID,transValue,transDateTime)
    VALUES (
		transId_Seq.NEXTVAL
		,(SELECT transtypeid FROM TRANSTYPEID WHERE TRANSTYPEDESC = 'DEPOSIT')
        ,3
        ,NULL
        ,1000
        ,systimestamp);
INSERT INTO TRANSACTIONS (transID,transTypeID,senderAcctID,receiverAcctID,transValue,transDateTime)
    VALUES (
		transId_Seq.NEXTVAL
		,(SELECT transtypeid FROM TRANSTYPEID WHERE TRANSTYPEDESC = 'WITHDRAW')
        ,3
        ,NULL
        ,500
        ,systimestamp);
INSERT INTO TRANSACTIONS (transID,transTypeID,senderAcctID,receiverAcctID,transValue,transDateTime)
    VALUES (
		transId_Seq.NEXTVAL
		,(SELECT transtypeid FROM TRANSTYPEID WHERE TRANSTYPEDESC = 'DEPOSIT')
        ,4
        ,NULL
        ,350
        ,systimestamp);
