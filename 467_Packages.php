<html>
   <head>
        <title> CHOP SHOP Auto Parts - Shipping</title>
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
    <body bgcolor="#392cf2">
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




  // Connect to Orders DB

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


// Prepare queries

    $preparedDJ = $pdo->prepare("SELECT parts.number, parts.description, parts.price, parts.weight, parts.pictureURL FROM parts;");
    $preparedDJ->execute();

    $preparedORDER = $pdo2->prepare("SELECT * FROM ORDERS;");
    $preparedORDER->execute();

    $preparedSHIP = $pdo2->prepare("SELECT * FROM SHIPPINGCOST;");
    $preparedSHIP->execute();


  // Print table of Orders

   echo "<div class=\"center\">";
   echo "<h1>CHOP SHOP Auto Parts - Order Tracking</h1>";

   echo "<table class=\"center\" border = 1 bgcolor=\"FFFFFF\" width=\"1200\">";

   echo "<tr><th>Order Num</th><th>Name</th><th>Address</th><th>Part Num</th><th>Total</th><th>Qty</th><th>Print Label</th><th>Shipped?</th></tr>";

 while($rows = $preparedORDER->fetch(PDO::FETCH_BOTH) )
     {
        echo "<form action=\"http:\/\/students.cs.niu.edu/~z1836033/467_Invoice.php?\" method=\"POST\">";
        echo "<tr>";
        echo "<td>".$rows["orderNum"]."</td><td>".$rows["name"]."</td>";
        echo "<td>".$rows["address"]."<br/>" .$rows["city"]. ", " .$rows["state"]. " " .$rows["zip"]. "</td>";
        echo "<td>".$rows["partNum"]."</td><td>"."$".$rows["total"]."</td><td>".$rows["qty"]."</td>";

     // Link to print invoice and shipping label
        echo "<td> <a href= \"http:/\/\students.cs.niu.edu/~z1836033/467_Invoice.php?orderNum=$rows[orderNum]&partNum=$rows[partNum]&quantity=$rows[qty]&name=$rows[name]&address=$rows[address]&city=$rows[city]&state=$rows[state]&zip=$rows[zip]&email=$rows[email]\">Print </a> </td>";

        if( $rows["shipped"] == true )
         echo "<td><font color=\"green\">YES</font></td>";
        else
         echo "<td><font color=\"red\">NO</font></td>";


        echo "</tr>";
     }


   echo "</table>";


  echo "</div>";
 echo "</form>";
  ?>
 </body>
</html>



