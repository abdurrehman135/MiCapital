

<?php
	session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link href="css/style_home.css" type="text/css" rel="stylesheet">

    <!-- Browser Tab title -->
    <title>User Overview Page</title>
    <script type="text/javascript">
        function logout(){
            location.href = "CPS510_logoff.php";
        }
    </script>
</head>
<body>
    <nav class="navbar fixed-top navbar-expand-lg navbar-light" style="background-color: #3EA055;">
    <div class="collapse navbar-collapse" id="navbarTogglerDemo01">
    <!-- <a class="navbar-brand" href="CPS510_home.php" ><img src="seed_logo.png" style="height: 40px; width: 200px;" alt="SEEDLabs"></a> -->

    <?php
        /* SHOW ERRORS */
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

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
        // Function to check username and password exist and are correct.
        function verifyUser($conn,$input_uname,$input_pwd)
        {
            $query = 'SELECT ou.userId AS "U", ou.privilege AS "P",p.passhash AS "H",s.passsalt AS "S" FROM ONLINEUSER ou
                        INNER JOIN PASSHASH p ON ou.userId = p.userId
                        INNER JOIN PASSSALT s ON ou.userId = s.userId
                        WHERE username = :username';
            $sql = oci_parse($conn, $query);
            $user = $input_uname;
            oci_bind_by_name($sql, ":username", $user);
            oci_execute($sql);
            $result = oci_fetch_assoc($sql);

            if($input_uname == '' || $input_pwd == '')
			{
				return -1;
			}
			//Too lazy to implement an actual hash+salt function, so we just appended the password to the salt.
			// So to verify, we just remove the salt and compare the rest (this isn't a security course, don't judge me!)
			else if(substr($result['H'],strlen($result['S'])) == $input_pwd)
            {
				$_SESSION['access'] = $result['P'];
				$_SESSION['user'] = $result['U'];
                return $result['P'];
            }
            else
            {
				return -1;
            }
        }
		
		function displayAccounts($conn)
		{
            $query = 'SELECT ca.*,sa.interestRate FROM CliAcctRel ca
						LEFT JOIN SAVINGSACCOUNT sa ON sa.acctId = ca.acctId
						WHERE cliId = (SELECT cliId FROM Clients WHERE userId = :userId)';
            $sql = oci_parse($conn, $query);
            $user = $_SESSION['user'];
            oci_bind_by_name($sql, ":userId", $user);
            oci_execute($sql);
			echo "<div class='container col-lg-4 col-lg-offset-4 text-center'>";
			echo "<br>";
			while (($row = oci_fetch_assoc($sql)) != false) {
				echo "<table class='table table-striped table-bordered'>";
				echo "<tr>";
				echo "<th scope='row'>";
				if($row['INTERESTRATE'] == null){ echo "CHEQUING ACCOUNT"; }
				else { echo "SAVINGS ACCOUNT"; }
				echo "</th>";
				echo "<td>" . str_pad($row['ACCTID'], 8, '0', STR_PAD_LEFT) . "</td>";
				echo "</tr>";
				echo "<tr>";
				echo "<th scope='row'>Balance</th>";
				echo "<td>$" . $row['BALANCE'] . "</td>";
				echo "</tr>";
				echo "</table>";
				echo "<details><summary>View Transactions</summary>";
					$query2 = 'SELECT t.*,ttyp.transTypeDesc FROM TRANSACTIONS t
						INNER JOIN TRANSTYPEID ttyp ON t.transTypeId = ttyp.transTypeId
						WHERE senderAcctID = :acct OR receiverAcctID = :acct';
					$sql2 = oci_parse($conn, $query2);
					oci_bind_by_name($sql2, ":acct", $row['ACCTID']);
					oci_execute($sql2);
					echo "<table class='table table-striped table-bordered'>";
					echo "<tr>";
					echo "<th scope='row'>Transaction Type</th>";
					echo "<th scope='row'>Sender</th>";
					echo "<th scope='row'>Receiver</th>";
					echo "<th scope='row'>Amount</th>";
					echo "<th scope='row'>Date</th>";
					echo "</tr>";
					while (($row2 = oci_fetch_assoc($sql2)) != false) {
						echo "<tr>";
						echo "<td>" . $row2['TRANSTYPEDESC'] . "</td>";
						echo "<td>" . str_pad($row2['SENDERACCTID'], 8, '0', STR_PAD_LEFT) . "</td>";
						echo "<td>";
							if($row2['RECEIVERACCTID'] == null) { echo "</td>"; }
							else{ echo str_pad($row2['RECEIVERACCTID'], 8, '0', STR_PAD_LEFT) . "</td>"; }
						echo "<td>$" . $row2['TRANSVALUE'] . "</td>";
						echo "<td>" . $row2['TRANSDATETIME'] . "</td>";
						echo "</tr>";
					}
					echo "</table>";
				// put transaction details here
				echo "</details>";
				echo "<br>";
				
			}
		}

        // create a connection
        $conn = getDB();
		if( isset($_SESSION['access'] ) ) {
			$access = $_SESSION['access'];
		}else {
			// if the session is new extract the username password from the GET request
			$input_uname = $_POST['username'];
			$input_pwd = $_POST['password'];
			$access = verifyUser($conn,$input_uname,$input_pwd);
		}
		if($conn == null)
		{
			return 0;
		}
        
        if($access == -1)
        {
			echo "</div>";
			echo "</nav>";
			echo "<div class='container text-center'>";
			echo "<div class='alert alert-danger'>";
			echo "The account information your provide does not exist.";
			echo "<br>";
			echo "</div>";
			echo "<a href='index.html'>Go back</a>";
			echo "</div>";
			return;
        }
        if($access == 0)
        {
			echo "<ul class='navbar-nav mr-auto mt-2 mt-lg-0' style='padding-left: 30px;'>";
			echo "<li class='nav-item active'>";
			echo "<a class='nav-link' href='CPS510_home.php'>Home <span class='sr-only'>(current)</span></a>";
			echo "</li>";
			echo "<li class='nav-item'>";
			echo "<a class='nav-link' href='CPS510_profile.php'>Profile</a>";
			echo "</li>";
			echo "</ul>";
			echo "<button onclick='logout()' type='button' id='logoffBtn' class='nav-link my-2 my-lg-0'>Logout</button>";
			echo "</div>";
			echo "</nav>";
			displayAccounts($conn);
        }
        if($access == 1)
        {
			echo "<ul class='navbar-nav mr-auto mt-2 mt-lg-0' style='padding-left: 30px;'>";
			echo "<li class='nav-item active'>";
			echo "<a class='nav-link' href='safe_home.php'>Home <span class='sr-only'>(current)</span></a>";
			echo "</li>";
			echo "<li class='nav-item'>";
			echo "<a class='nav-link' href='CPS510_createClient.php'>New Client</a>";
			echo "</li>";
			echo "<li class='nav-item'>";
			echo "<a class='nav-link' href='CPS510_createAccount.php'>New Account</a>";
			echo "</li>";
			echo "</ul>";
			echo "<button onclick='logout()' type='button' id='logoffBtn' class='nav-link my-2 my-lg-0'>Logout</button>";
			echo "</div>";
			echo "</nav>";
        }

        // close the sql connection
        oci_close($conn);
        ?>
    </div>
</body>
</html>