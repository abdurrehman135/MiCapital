
<?php
    session_start();
    $user = $_SESSION['user'];
?>

<html>
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link href="css/style_home.css" type="text/css" rel="stylesheet">

    <!-- Browser Tab title -->
    <title>Profile</title>
  
    <script type="text/javascript">
        function logout(){
            location.href = "CPS510_logoff.php";
        }
    </script>
</head>

<body>
    <nav class="navbar fixed-top navbar-expand-lg navbar-light" style="background-color: #3EA055;">
        <div class="collapse navbar-collapse" id="navbarTogglerDemo01">
            <ul class='navbar-nav mr-auto mt-2 mt-lg-0' style='padding-left: 30px;'>
                <li class='nav-item'>
                    <a class='nav-link' href='CPS510_home.php'>Home</a>
                </li>
                <li class='nav-item active'>
                    <a class='nav-link' href='CPS510_profile.php'>Profile</a>
                </li>
            </ul>
            <button onclick='logout()' type='button' id='logoffBtn' class='nav-link my-2 my-lg-0'>Logout</button>
        </div>
    </nav>

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
        
        function getProfileData($conn)
        {
            global $user;
            $query = 'SELECT ca.fName,ca.lName FROM Clients ca
                        WHERE userId = :username';
            $sql = oci_parse($conn, $query);
            oci_bind_by_name($sql, ":username", $user);
            oci_execute($sql);
            $row = oci_fetch_assoc($sql);
            return $row;
        }

        // create a connection
        $conn = getDB();
        $result = getProfileData($conn);
        $fName = $result['FNAME'];
        $lName = $result['LNAME'];

    ?>
    <div class="container   text-center" style="padding-top: 50px; text-align: center;">
        <h2><b><?php echo "$fName $lName's Profile" ?></b></h1><hr><br>
        <form action="CPS510_backend.php" method="post">
            <?php
                $query = 'SELECT at.addrTypeDesc,a.addrLine1,a.addrLine2,a.city,a.stateProvince,a.country,a.postalCode FROM ADDRESSES a
                            INNER JOIN ADDRESSTYPEID at ON at.addrTypeId = a.addrTypeId
                            WHERE a.cliId = (SELECT cliId FROM CLIENTS WHERE userId = :username)';
                $sql = oci_parse($conn, $query);
                oci_bind_by_name($sql, ":username", $user);
                oci_execute($sql);
                
                $mailing['ADDRLINE1']='';
                $mailing['ADDRLINE2']='';
                $mailing['CITY']='';
                $mailing['STATEPROVINCE']='';
                $mailing['COUNTRY']='';
                $mailing['POSTALCODE']='';
                $billing['ADDRLINE1']='';
                $billing['ADDRLINE2']='';
                $billing['CITY']='';
                $billing['STATEPROVINCE']='';
                $billing['COUNTRY']='';
                $billing['POSTALCODE']='';
                while(($row = oci_fetch_assoc($sql)) != false)
                {
                    if($row['ADDRTYPEDESC'] == "MAILING")
                    {
                        $mailing = $row;
                        echo "<input id='hasM' name='hasM' type='hidden' value='true'>";
                    }
                    if($row['ADDRTYPEDESC'] == "MAILING+BILLING")
                    {
                        $mailing = $row;
                        $billing = $row;
                        echo "<input id='hasMB' name='hasMB' type='hidden' value='true'>";
                    }
                    if($row['ADDRTYPEDESC'] == "BILLING")
                    {
                        $billing = $row;
                        echo "<input id='hasB' name='hasB' type='hidden' value='true'>";
                    }
                }
                echo "<div class='form-group'>";
                echo "<details><summary>Mailing Address</summary>";
                echo "<label for='addrLine1' class='col-form-label'>Address Line 1</label>";
                echo "<div class='col-sm-*'>";
                echo "<input type='text' class='form-control' id='addrLine1' name='addrLine1' value='".$mailing['ADDRLINE1'];;
                echo "'></div>";
                echo "<label for='addrLine2' class='col-sm-4 col-form-label'>Address Line 2</label>";
                echo "<div class='col-sm-*'>";
                echo "<input type='text' class='form-control' id='addrLine2' name='addrLine2' value='".$mailing['ADDRLINE2'];
                echo "'></div>";
                echo "<label for='city' class='col-sm-4 col-form-label'>City</label>";
                echo "<div>";
                echo "<input type='text' class='form-control' id='city' name='city' value='".$mailing['CITY'];
                echo "'></div>";
                echo "<label for='province' class='col-sm-4 col-form-label'>State/Province</label>";
                echo "<div class='col-sm-*'>";
                echo "<input type='text' class='form-control' id='province' name='province' value='".$mailing['STATEPROVINCE'];
                echo "'></div>";
                echo "<label for='country' class='col-sm-4 col-form-label'>Country</label>";
                echo "<div class='col-sm-*'>";
                echo "<input type='text' class='form-control' id='country' name='country' value='".$mailing['COUNTRY'];
                echo "'></div>";
                echo "<label for='postalCode' class='col-sm-4 col-form-label'>Postal Code</label>";
                echo "<div class='col-sm-*'><input type='text' class='form-control' id='postalCode' name='postalCode' value='".$mailing['POSTALCODE'];
                echo "'></div></details></div>";
                
                echo "<div class='form-group'>";
                echo "<details class='col-form-label'><summary>Billing Address</summary>";
                echo "<label for='baddrLine1' class='col-form-label'>Address Line 1</label>";
                echo "<div class='col-sm-*'>";
                echo "<input type='text' class='form-control' id='baddrLine1' name='baddrLine1' value='".$billing['ADDRLINE1'];;
                echo "'></div>";
                echo "<label for='baddrLine2' class='col-sm-4 col-form-label'>Address Line 2</label>";
                echo "<div class='col-sm-*'>";
                echo "<input type='text' class='form-control' id='baddrLine2' name='baddrLine2' value='".$billing['ADDRLINE2'];
                echo "'></div>";
                echo "<label for='bcity' class='col-sm-4 col-form-label'>City</label>";
                echo "<div class='col-sm-*'>";
                echo "<input type='text' class='form-control' id='bcity' name='bcity' value='".$billing['CITY'];
                echo "'></div>";
                echo "<label for='bprovince' class='col-sm-4 col-form-label'>State/Province</label>";
                echo "<div class='col-sm-*'>";
                echo "<input type='text' class='form-control' id='bprovince' name='bprovince' value='".$billing['STATEPROVINCE'];
                echo "'></div>";
                echo "<label for='bcountry' class='col-sm-4 col-form-label'>Country</label>";
                echo "<div class='col-sm-*'>";
                echo "<input type='text' class='form-control' id='bcountry' name='bcountry' value='".$billing['COUNTRY'];
                echo "'></div>";
                echo "<label for='bpostalCode' class='col-sm-4 col-form-label'>Postal Code</label>";
                echo "<div class='col-sm-*'><input type='text' class='form-control' id='bpostalCode' name='bpostalCode' value='".$billing['POSTALCODE'];
                echo "'></div></details></div>";


                $query = 'SELECT pt.phoneTypeDesc,p.phonenum,p.extension FROM PHONENUMBERS p
                            INNER JOIN PHONETYPEID pt ON pt.phoneTypeId = p.phoneTypeId
                            WHERE p.cliId = (SELECT cliId FROM CLIENTS WHERE userId = :username)';
                $sql = oci_parse($conn, $query);
                oci_bind_by_name($sql, ":username", $user);
                oci_execute($sql);
                
                $home['PHONENUM']='';
                $home['EXTENSION']='';
                $work['PHONENUM']='';
                $work['EXTENSION']='';
                $cell['PHONENUM']='';
                $cell['EXTENSION']='';
                $fax['PHONENUM']='';
                $fax['EXTENSION']='';
                while(($row = oci_fetch_assoc($sql)) != false)
                {
                    if($row['PHONETYPEDESC'] == "HOME")
                    {
                        $home = $row;
                        echo "<input id='hasH' name='hasH' type='hidden' value='true'>";
                    }
                    if($row['PHONETYPEDESC'] == "WORK")
                    {
                        $work = $row;
                        echo "<input id='hasW' name='hasW' type='hidden' value='true'>";
                    }
                    if($row['PHONETYPEDESC'] == "CELL")
                    {
                        $cell = $row;
                        echo "<input id='hasC' name='hasC' type='hidden' value='true'>";
                    }
                    if($row['PHONETYPEDESC'] == "FAX")
                    {
                        $fax = $row;
                        echo "<input id='hasF' name='hasF' type='hidden' value='true'>";
                    }
                }
                echo "<div class='form-group'>";
                echo "<details class='col-form-label'><summary>Home Phone</summary>";
                echo "<label for='HomeNumber' class='col-form-label'>Phone Number</label>";
                echo "<div>";
                echo "<input type='text' class='form-control' id='HomeNumber' name='HomeNumber' value='".$home['PHONENUM'];;
                echo "'>";
                echo "<label for='Extension' class='col-sm-4 col-form-label'>Ext.</label>";
                echo "<div class='col-sm-*'>";
                echo "<input type='text' class='form-control' id='HomeExtension' name='HomeExtension' value='".$home['EXTENSION'];
                echo "'></div></details></div>";
                
                echo "<div class='form-group'>";
                echo "<details class='col-form-label'><summary>Work Phone</summary>";
                echo "<label for='WorkNumber' class='col-form-label'>Phone Number</label>";
                echo "<div>";
                echo "<input type='text' class='form-control' id='WorkNumber' name='WorkNumber' value='".$work['PHONENUM'];;
                echo "'>";
                echo "<label for='Extension' class='col-sm-4 col-form-label'>Ext.</label>";
                echo "<div class='col-sm-*'>";
                echo "<input type='text' class='form-control' id='WorkExtension' name='WorkExtension' value='".$work['EXTENSION'];
                echo "'></div></details></div>";
                
                echo "<div class='form-group'>";
                echo "<details class='col-form-label'><summary>Cell Phone</summary>";
                echo "<label for='CellNumber' class='col-form-label'>Phone Number</label>";
                echo "<div>";
                echo "<input type='text' class='form-control' id='CellNumber' name='CellNumber' value='".$cell['PHONENUM'];;
                echo "'>";
                echo "<label for='CellExtension' class='col-sm-4 col-form-label'>Ext.</label>";
                echo "<div class='col-sm-*'>";
                echo "<input type='text' class='form-control' id='CellExtension' name='CellExtension' value='".$cell['EXTENSION'];
                echo "'></div></details></div>";

                echo "<div class='form-group'>";
                echo "<details class='col-form-label'><summary>Fax</summary>";
                echo "<label for='FaxNumber' class='col-form-label'>Fax Number</label>";
                echo "<div>";
                echo "<input type='text' class='form-control' id='FaxNumber' name='FaxNumber' value='".$fax['PHONENUM'];;
                echo "'>";
                echo "<label for='FaxExtension' class='col-sm-4 col-form-label'>Ext.</label>";
                echo "<div class='col-sm-*'>";
                echo "<input type='text' class='form-control' id='FaxExtension' name='FaxExtension' value='".$fax['EXTENSION'];
                echo "'></div></details></div>";
                
                oci_close($conn);
            ?>

            <br>
            <div class="form-group row">
                <div class="col-sm-12">
                    <button type="submit" class="btn btn-success btn-lg btn-block">Save</button>
                </div>
            </div>
        </form>
        <br>
    </div>
    
    
</body>
</html>