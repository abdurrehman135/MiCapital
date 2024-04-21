/*
MSSQL datatype equivalent as recommended by Oracle.
INTEGER = NUMBER(10)
SMALLINT = NUMBER(5)
*/
/*
Oracle v11g doesn't support "IDENTITY" (12c does), so we have to manually manage an incrementing id to ensure we're generating unique Keys.
We use <SequenceName>.NEXTVAL, which returns the next number in the sequences,
and then increments the pointer, so next time we call it we get the next number.
We don't need a sequence for our lookup tables, since once they have data they're unlikely to change frequently.
*/
CREATE SEQUENCE userId_Seq
MINVALUE 0
START WITH 0
INCREMENT BY 1
NOCACHE;
CREATE SEQUENCE cliID_Seq
MINVALUE 0
START WITH 0
INCREMENT BY 1
NOCACHE;
CREATE SEQUENCE acctID_Seq
MINVALUE 0
START WITH 0
INCREMENT BY 1
NOCACHE;
CREATE SEQUENCE transID_Seq
MINVALUE 0
START WITH 0
INCREMENT BY 1
NOCACHE;

/*
Our lookup tables have Primary Keys that other tables use, but don't rely on other tables' keys.
So we can create them first to avoid errors.
*/
CREATE TABLE ADDRESSTYPEID (
addrTypeID NUMBER(5) PRIMARY KEY,
addrTypeDesc VARCHAR(50)
);
CREATE TABLE PHONETYPEID (
phoneTypeID NUMBER(5) PRIMARY KEY,
phoneTypeDesc VARCHAR2(50)
);
CREATE TABLE TRANSTYPEID (
transTypeID NUMBER(5) PRIMARY KEY,
transTypeDesc VARCHAR2(50)
);

/*
Some of our tables use foreign key references, so we have to construct them in a certain order
eg. CLIENTS hosts the cliID field, which ADDRESS, USER, HASHPASS and HASHSALT use, so it must be before them
eg. ACCOUNTS hosts the acctID field, which CHEQUINGACCOUNT, SAVINGSACCOUNT and TRANSACTIONS use, so it must be before them
eg. CLIACCT is the relationship between CLIENTS and ACCOUNTs, and needs to be created after both of those tables.
*/
CREATE TABLE ONLINEUSER (
userId NUMBER,
userName VARCHAR2(50),
privilege NUMBER(5),
lastLogin TIMESTAMP
);
ALTER TABLE ONLINEUSER ADD CONSTRAINT "ONLINEUSER_USERID_PK" PRIMARY KEY (userId);

CREATE TABLE PASSHASH (
userId NUMBER REFERENCES ONLINEUSER(userId),
passHash VARCHAR2(64)
);

CREATE TABLE PASSSALT (
userId NUMBER REFERENCES ONLINEUSER(userId),
passSalt VARCHAR2(16)
);

CREATE TABLE CLIENTS (
cliID NUMBER,
fName VARCHAR2(50),
lName VARCHAR2(50),
userId NUMBER REFERENCES ONLINEUSER(userId),
cliOpenDate DATE
);
ALTER TABLE CLIENTS ADD CONSTRAINT "CLIENTS_CLIID_PK" PRIMARY KEY (cliID);

CREATE TABLE ADDRESSES (
cliID NUMBER REFERENCES CLIENTS(cliID),
addrTypeID REFERENCES ADDRESSTYPEID(addrTypeID),
addrLine1 VARCHAR2(100),
addrLine2 VARCHAR2(100),
city VARCHAR2(100),
stateProvince VARCHAR2(100),
country VARCHAR2(100),
postalCode VARCHAR2(10)
);
ALTER TABLE ADDRESSES ADD CONSTRAINT "ADDRESSES_ADDRID_CLIID_PK" PRIMARY KEY (addrTypeID,cliID);

CREATE TABLE PHONENUMBERS (
cliID NUMBER REFERENCES CLIENTS(cliID),
phoneTypeID NUMBER(5) REFERENCES PHONETYPEID(phoneTypeID),
phoneNum VARCHAR2(15) NOT NULL,
extension VARCHAR2(10)
);
ALTER TABLE PHONENUMBERS ADD CONSTRAINT "PHONENUMBERS_PHONEID_CLIID_PK" PRIMARY KEY (phoneNum,cliID);

CREATE TABLE ACCOUNTS (
acctID NUMBER,
balance NUMBER NOT NULL,
billingDay DATE,
acctOpenDate DATE
);
ALTER TABLE ACCOUNTS ADD CONSTRAINT "ACCOUNTS_ACCTID_PK" PRIMARY KEY (acctID);


/*
CHEQUINGACCOUNT and SAVINGSACCOUNT should have more fields, but this is just a simplified sample of a Retail Banking database.
*/
CREATE TABLE CHEQUINGACCOUNT (
acctID NUMBER REFERENCES ACCOUNTS (acctID),
transLimit NUMBER
);

CREATE TABLE SAVINGSACCOUNT (
acctID NUMBER REFERENCES ACCOUNTS (acctID),
interestRate NUMBER(10,2)
);

CREATE TABLE TRANSACTIONS (
transId NUMBER,
transTypeID NUMBER(5) REFERENCES TRANSTYPEID (transTypeID),
senderAcctID NUMBER REFERENCES ACCOUNTS (acctID),
receiverAcctID NUMBER NULL REFERENCES ACCOUNTS (acctID),
transValue NUMBER NOT NULL,
transDateTime TIMESTAMP DEFAULT systimestamp
);
ALTER TABLE TRANSACTIONS ADD CONSTRAINT "TRANSACTIONS_TRANSID_PK" PRIMARY KEY (transId);

CREATE TABLE CLIACCT (
cliID NUMBER REFERENCES CLIENTS(cliID),
acctID NUMBER REFERENCES ACCOUNTS(acctID)
);
ALTER TABLE CLIACCT ADD CONSTRAINT "CLIACCT_CLIID_ACCTID_PK" PRIMARY KEY (cliID,acctID);
