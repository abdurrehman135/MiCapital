
<!DOCTYPE html>
<html>
<body>

    <?php
    session_start();
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
    $user = $_SESSION['user'];

    $mailing['ADDRLINE1']=$_POST['addrLine1'];
    $mailing['ADDRLINE2']=$_POST['addrLine2'];
    $mailing['CITY']=$_POST['city'];
    $mailing['STATEPROVINCE']=$_POST['province'];
    $mailing['COUNTRY']=$_POST['country'];
    $mailing['POSTALCODE']=$_POST['postalCode'];
    $billing['ADDRLINE1']=$_POST['baddrLine1'];
    $billing['ADDRLINE2']=$_POST['baddrLine2'];
    $billing['CITY']=$_POST['bcity'];
    $billing['STATEPROVINCE']=$_POST['bprovince'];
    $billing['COUNTRY']=$_POST['bcountry'];
    $billing['POSTALCODE']=$_POST['bpostalCode'];
    $home['PHONENUM']=$_POST['HomeNumber'];
    $home['EXTENSION']=$_POST['HomeExtension'];
    $work['PHONENUM']=$_POST['WorkNumber'];
    $work['EXTENSION']=$_POST['WorkExtension'];
    $cell['PHONENUM']=$_POST['CellNumber'];
    $cell['EXTENSION']=$_POST['CellExtension'];
    $fax['PHONENUM']=$_POST['FaxNumber'];
    $fax['EXTENSION']=$_POST['FaxExtension'];

	echo $user;
	echo $mailing['ADDRLINE1'];
	echo $mailing['ADDRLINE2'];
	echo $mailing['CITY'];
	echo $mailing['STATEPROVINCE'];
	echo $mailing['COUNTRY'];
	echo $mailing['POSTALCODE'];
	if( $mailing == $billing )
	{
		echo "YES, MB";
	}
	else
	{
		echo "NO, MB";
	}
	
	

    // Function to create a sql connection.
    function getDB() {
        $dbuser=""; // TODO: Insert database username
        $dbpass=""; // TODO: Insert database user password
        $dbname="CPS510";
        // Create a DB connection
        $conn = oci_connect($dbuser, $dbpass, '(DESCRIPTION=(ADDRESS=(PROTOCOL=TCP)(Host=HOSTNAME)(Port=1521))(CONNECT_DATA=(SID=orcl)))'); // TODO: Replace HOSTNAME with appropriate hostname 
        if (!$conn) {
            //$e = oci_error();   // For oci_connect errors do not pass a handle
            //echo $e['message'];
            echo "</div></nav><div class='container text-center'>";
            echo "Connection not Established.";
            echo "</div>";
            return null;
        }
        else{
            return $conn;
        }
    }

    $conn = getDB();
	
    //Delete all addresses, then insert the updated ones (because I'm lazy)
    $query = 'DELETE FROM Addresses WHERE cliId = (SELECT cliId FROM Clients WHERE userId = :username)';
    $sql = oci_parse($conn, $query);
    oci_bind_by_name($sql, ":username", $user);
    oci_execute($sql);
    if($mailing == $billing && $mailing['ADDRLINE1'] != '')
    {
        $query = "INSERT INTO ADDRESSES (cliID,addrTypeID,addrLine1,addrLine2,city,stateProvince,country,postalCode)
                    VALUES (
                    (SELECT cliID FROM Clients WHERE userId = :username)
                    ,3
                    ,:addrLine1
                    ,:addrLine2
                    ,:city
                    ,:province
                    ,:country
                    ,:postalcode)";
        $sql = oci_parse($conn, $query);
        oci_bind_by_name($sql, ":username", $user);
        oci_bind_by_name($sql, ":addrLine1", $mailing['ADDRLINE1']);
        oci_bind_by_name($sql, ":addrLine2", $mailing['ADDRLINE2']);
        oci_bind_by_name($sql, ":city", $mailing['CITY']);
        oci_bind_by_name($sql, ":province", $mailing['STATEPROVINCE']);
        oci_bind_by_name($sql, ":country", $mailing['COUNTRY']);
        oci_bind_by_name($sql, ":postalCode", $mailing['POSTALCODE']);
        oci_execute($sql);
    }
    else
    {
        if($mailing['ADDRLINE1'] != '')
        {
            $query = "INSERT INTO ADDRESSES (cliID,addrTypeID,addrLine1,addrLine2,city,stateProvince,country,postalCode)
                        VALUES (
                        (SELECT cliID FROM Clients WHERE userId = :username)
                        ,1
                        ,:addrLine1
                        ,:addrLine2
                        ,:city
                        ,:province
                        ,:country
                        ,:postalcode)";
            $sql = oci_parse($conn, $query);
            oci_bind_by_name($sql, ":username", $user);
            oci_bind_by_name($sql, ":addrLine1", $mailing['ADDRLINE1']);
            oci_bind_by_name($sql, ":addrLine2", $mailing['ADDRLINE2']);
            oci_bind_by_name($sql, ":city", $mailing['CITY']);
            oci_bind_by_name($sql, ":province", $mailing['STATEPROVINCE']);
            oci_bind_by_name($sql, ":country", $mailing['COUNTRY']);
            oci_bind_by_name($sql, ":postalCode", $mailing['POSTALCODE']);
            oci_execute($sql);
        }
        if($billing['ADDRLINE1'] != '')
        {
            $query = "INSERT INTO ADDRESSES (cliID,addrTypeID,addrLine1,addrLine2,city,stateProvince,country,postalCode)
                        VALUES (
                        (SELECT cliID FROM Clients WHERE userId = :username)
                        ,2
                        ,:addrLine1
                        ,:addrLine2
                        ,:city
                        ,:province
                        ,:country
                        ,:postalcode)";
            $sql = oci_parse($conn, $query);
            oci_bind_by_name($sql, ":username", $user);
            oci_bind_by_name($sql, ":addrLine1", $billing['ADDRLINE1']);
            oci_bind_by_name($sql, ":addrLine2", $billing['ADDRLINE2']);
            oci_bind_by_name($sql, ":city", $billing['CITY']);
            oci_bind_by_name($sql, ":province", $billing['STATEPROVINCE']);
            oci_bind_by_name($sql, ":country", $billing['COUNTRY']);
            oci_bind_by_name($sql, ":postalCode", $billing['POSTALCODE']);
            oci_execute($sql);
        }
    }
    
    //Delete all phone numbers and insert based on last page (because I'm lazy)
    $query = 'DELETE FROM PhoneNumbers WHERE cliId = (SELECT cliId FROM Clients WHERE userId = :username)';
    $sql = oci_parse($conn, $query);
    oci_bind_by_name($sql, ":username", $user);
    oci_execute($sql);
    if($home['PHONENUM'] != '')
    {
        $query = 'INSERT INTO PHONENUMBERS (cliID,phoneTypeID,phoneNum,extension) VALUES (
                    (SELECT cliID FROM CLIENTS WHERE userId = :username)
                    ,1
                    ,:pnum
                    ,:extension)';
        $sql = oci_parse($conn, $query);
        oci_bind_by_name($sql, ":username", $user);
        oci_bind_by_name($sql, ":pnum", $home['PHONENUM']);
        oci_bind_by_name($sql, ":extension", $home['EXTENSION']);
        oci_execute($sql);
    }
    if($cell['PHONENUM'] != '')
    {
        $query = 'INSERT INTO PHONENUMBERS (cliID,phoneTypeID,phoneNum,extension) VALUES (
                    (SELECT cliID FROM CLIENTS WHERE userId = :username)
                    ,2
                    ,:pnum
                    ,:extension)';
        $sql = oci_parse($conn, $query);
        oci_bind_by_name($sql, ":username", $user);
        oci_bind_by_name($sql, ":pnum", $cell['PHONENUM']);
        oci_bind_by_name($sql, ":extension", $cell['EXTENSION']);
        oci_execute($sql);
    }
    if($work['PHONENUM'] != '')
    {
        $query = 'INSERT INTO PHONENUMBERS (cliID,phoneTypeID,phoneNum,extension) VALUES (
                    (SELECT cliID FROM CLIENTS WHERE userId = :username)
                    ,3
                    ,:pnum
                    ,:extension)';
        $sql = oci_parse($conn, $query);
        oci_bind_by_name($sql, ":username", $user);
        oci_bind_by_name($sql, ":pnum", $work['PHONENUM']);
        oci_bind_by_name($sql, ":extension", $work['EXTENSION']);
        oci_execute($sql);
    }
    if($fax['PHONENUM'] != '')
    {
        $query = 'INSERT INTO PHONENUMBERS (cliID,phoneTypeID,phoneNum,extension) VALUES (
                    (SELECT cliID FROM CLIENTS WHERE userId = :username)
                    ,4
                    ,:pnum
                    ,:extension)';
        $sql = oci_parse($conn, $query);
        oci_bind_by_name($sql, ":username", $user);
        oci_bind_by_name($sql, ":pnum", $fax['PHONENUM']);
        oci_bind_by_name($sql, ":extension", $fax['EXTENSION']);
        oci_execute($sql);
    }
    oci_close($conn);
    header("Location: CPS510_home.php");
    exit();
    ?>

</body>
</html>