<html>
   <head>
        <title>CHOP SHOP Auto Parts - Admin Page </title>
   </head>

    <style>
     table.center {
       margin-left:auto;
       margin-right:auto;
     }

     .center {
	  padding: 70xp 0;
	  border: 3px solid black;
	  text-align: center;
      }

    </style>

    <body bgcolor="#f06a11">
   <?php

   // Retrieve the Parts DB

     $username = "student";
     $password = "student";


   try
   {
        $dsn = "mysql:host=blitz.cs.niu.edu;dbname=csci467";
        $pdo = new PDO($dsn, $username, $password);
   }
   catch(PDOexception $e) {
        echo "Connection to database failes: " . $e->getMessage();
   }

   $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);





  // Connect to Quantity & Orders DB

    		$username2 = 'z1836033';
       	        $password2 = '1992Sep16';


    //Attempt to connect to the database
	  try
	   {
	     $dsn2 = "mysql:host=courses;dbname=z1836033";
	     $pdo2 = new PDO($dsn2, $username2, $password2);
           }
	  catch(PDOexception $e)
           {
	     echo "Connection to the database failed: " . $e->getMessage();
	   }

	 $pdo2->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);


  // Set new costs

 if( isset($_POST['base']) && $_POST['extra'] == "" )
  {
   $preparedBASE = $pdo2->prepare("UPDATE SHIPPINGCOST SET baseCost= :base ;");
   $preparedBASE->bindParam(':base', $_POST['base'] );
   $preparedBASE->execute();
  }
  else if( isset($_POST['extra']) && $_POST['base'] == "" )
  {
   $preparedEXTRA = $pdo2->prepare("UPDATE SHIPPINGCOST SET extraCost = :extra;");
   $preparedEXTRA->bindParam(':extra', $_POST['extra'] );
   $preparedEXTRA->execute();

  }
  else if( isset($_POST['base']) && isset($_POST['extra']) )
  {
   $preparedBOTH = $pdo2->prepare("UPDATE SHIPPINGCOST SET baseCost = :base, extraCost = :extra;");
   $preparedBOTH->bindParam(':base', $_POST['base'] );
   $preparedBOTH->bindParam(':extra', $_POST['extra'] );
   $preparedBOTH->execute();
  }


  // Display the ORDERS table

    $preparedDJ = $pdo->prepare("SELECT parts.number, parts.description, parts.price, parts.weight, parts.pictureURL FROM parts;");
    $preparedDJ->execute();


  // Sort if sorting fields are set

   if( isset($_POST['date']) && $_POST['date'] != "" )
   {
    $preparedORDER = $pdo2->prepare("SELECT * FROM ORDERS WHERE ORDERS.dateTime= :date ;");
    $preparedORDER->bindParam(':date', $_POST['date'] );
    $preparedORDER->execute();
   }
   else if( isset($_POST['isShipped']) )
    {
     if( $_POST['isShipped'] == 'true' )
       {
        $preparedORDER = $pdo2->prepare("SELECT * FROM ORDERS WHERE ORDERS.shipped= 1 ;");
        $preparedORDER->execute();
       }
     else
       {
        $preparedORDER = $pdo2->prepare("SELECT * FROM ORDERS WHERE ORDERS.shipped= 0 ;");
        $preparedORDER->execute();
       }
    }
   else
   {
    $preparedORDER = $pdo2->prepare("SELECT * FROM ORDERS;");
    $preparedORDER->execute();
   }

   // Retrieve shipping cost values from SHIPPINGCOST DB

    $preparedSHIP = $pdo2->prepare("SELECT * FROM SHIPPINGCOST;");
    $preparedSHIP->execute();


  // Begin printing page

  echo "<div class=\"center\">";
  echo "<h1>CHOP SHOP Auto Parts - Order Tracking</h1>";

  echo "<form action=\"\" method=\"POST\" >";
  echo "Search By Date: <input type=\"date\" name=\"date\"> &emsp;";
  echo "<select name=\"isShipped\">";
  echo "  <option value=\"true\">Shipped</option>";
  echo "  <option value=\"false\">NOT Shipped</option>";
  echo "</select>";
  echo " <input type=\"submit\" value=\"Search\"> <br/>";


  echo "</form>";

  echo "<table class=\"center\" border = 1 bgcolor=\"FFFFFF\" width=\"90%\">";

   echo "<tr><th>Order Num</th><th>Date Ordered</th><th>Name</th><th>Address</th><th>Part Num</th><th>Email</th><th>Total</th><th>Qty</th><th>Shipped?</th></tr>";


     // Print out the table

     while($rows = $preparedORDER->fetch(PDO::FETCH_BOTH) )
     {
        echo "<tr>";
        echo "<td>".$rows["orderNum"]."</td><td>".$rows["dateTime"]."</td><td>".$rows["name"]."</td>";
        echo "<td>".$rows["address"]."<br/>" .$rows["city"]. ", " .$rows["state"]. " " .$rows["zip"]. "</td>";
        echo "<td>".$rows["partNum"]."</td><td>".$rows["email"]."</td><td>"."$".$rows["total"]."</td><td>".$rows["qty"]."</td>";

        if( $rows["shipped"] == true )
         echo "<td><font color=\"green\">YES</font></td>";
        else
         echo "<td><font color=\"red\">NO</font></td>";


        echo "</tr>";
     }


   echo "</table>";


  echo "<h2>Edit Shipping/Handling Cost</h2>";

  echo "<table class=\"center\" border = 1 bgcolor=\"FFFFFF\" width=\"90%\">";

 echo "<tr><th>Current</th><th>New</th></tr>";
 echo "<tr><td>";


 // Print current shipping costs as well as NEW shipping input fields

  while($rows = $preparedSHIP->fetch(PDO::FETCH_BOTH) )
   {
    echo "Current Base Cost: $".$rows["baseCost"];
    echo "<br/><br/>";
    echo "Current Extra Cost: $".$rows["extraCost"]."<br/> (per 3 lbs over first 3 lbs)";
   }

 echo "</td><td>";

  echo "<form action=\"\" method=\"POST\">";

   echo"<br/>&emsp;New Base Cost: <input type=\"text\" name=\"base\" placeholder=\"Enter New Price\"> <br/>";
   echo"<br/>&emsp;New Extra Cost: <input type=\"text\" name=\"extra\" placeholder=\"Enter New Price\"> <br/>";


  echo "</td></tr></table>";
  echo"<br/><input type=\"reset\" value=\"Reset\"> &nbsp; <input type=\"submit\" value=\"Submit\" >";

  echo "</form>";
  echo "</div>";

  ?>
 </body>
</html>
