<html>
   <head>
        <title>CHOP SHOP Auto Parts - Invoice and Shipping Address </title>
   </head>

    <style>
     table.center {
       margin-left:auto;
       margin-right:auto;
       font-size:large;
     }

     .center {
          padding: 70xp 0;
          border: 3px solid black;
          text-align: center;
          line-height: 1.6;
          font-size:large;
      }

     .right {
         position: absolute;
         right: 0px;
         width: 300px;
         border: 3px solid #73AD21;
         padding: 10px;
      }
    </style>
    <body bgcolor="#ffffff">
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



// Take passed in GET arguments into local variables

        $id = $_GET['orderNum'];
        $partsnums = $_GET['partNum'];
        $quantity = $_GET['quantity'];
	$name = $_GET['name'];
	$address = $_GET['address'];
	$city = $_GET['city'];
	$state = $_GET['state'];
	$zip = $_GET['zip'];
	$email = $_GET['email'];


  // Prepare queries

    $preparedDJ = $pdo->prepare("SELECT parts.number, parts.description, parts.price, parts.weight, parts.pictureURL FROM parts WHERE parts.number = $partsnums;");
    $preparedDJ->bindParam(':number', $partsnum);
    $preparedDJ->execute();

    $preparedORDER = $pdo2->prepare("SELECT * FROM ORDERS WHERE ORDERS.orderNum = :orderNum;");
    $preparedORDER->bindParam(':orderNum', $id);
    $preparedORDER->execute();

    $preparedSHIP = $pdo2->prepare("SELECT * FROM SHIPPINGCOST;");
    $preparedSHIP->execute();

// Print selected buy item

   echo "<div class=\"center\">";
   echo "<h1>INVOICE</h1>";
   echo "<table class=\"center\" border = 1 bgcolor=\"FFFFFF\" width=\"95%\">";

   echo "<tr><th>Pic</th><th>Number</th><th>Description</th><th>Weight</th><th>Price</th><th>Qty to Buy</th></tr>";


     while($rows = $preparedDJ->fetch(PDO::FETCH_BOTH) )
     {
    ?>
        <tr>
        <td><image src="<?php echo $rows["pictureURL"]; ?>" /></td>
     <?php
        echo "<td>".$rows["number"]."</td><td>".$rows["description"]."</td><td>".$rows["weight"]."</td><td>"."$".$rows["price"]."</td>";
        echo "<td>$quantity</td>";
        echo "</tr>";

        $weight = $rows["weight"];
        $price = $rows["price"];
    }


 // Determine shipping total
    $totalWeight = $weight * $quantity;

   while( $rows = $preparedSHIP->fetch(PDO::FETCH_BOTH) )
    {
      $baseCost = $rows["baseCost"];
      $extraCost = $rows["extraCost"];
    }

   // Determine extra weight (every 3 lbs over the first 3 lbs)
   $extraWeight = ($totalWeight-3) / 3;


   if( $extraWeight < 0 )
    $shipping = $baseCost;
   else
    $shipping = $baseCost + ($extraCost * $extraWeight);


   $total = ($price * $quantity) + $shipping;
   $itemTotal = $price * $quantity;


  // Print invoice of order

    while($rows = $preparedORDER->fetch(PDO::FETCH_BOTH) )
   {
     echo "<tr><td></td><td></td><td></td><td>x".$quantity."</td><td>$".$itemTotal."</td><td></td></tr>";
     echo "<tr><td></td><td></td><td></td><td>SHIPPING: </td><td>$".number_format((float)$shipping, 2, '.', '')."</td><td></td></tr>";
     echo "<tr><td></td><td></td><td></td><td>TOTAL: </td><td>$".number_format((float)$total, 2, '.', '')."</td><td></td></tr>";
   }

  echo "Shipping Address : <br/><br/>". $name." <br/>". $address. "<br/>". $city. ", ". $state." ". $zip. "<br/><br/>";



 // Send email
?>

 <form action="" method="post">
 <input type="submit" value="Complete Order" />
 <input type="hidden" name="button_pressed" value="1" />
 </form>

<?php

if(isset($_POST['button_pressed']))
{
   // Mark as shipped in DB
    $preparedBASE = $pdo2->prepare("UPDATE ORDERS SET shipped = 1 WHERE orderNum = $id ;");
    $preparedBASE->execute();

   // Subtract quantity bought from amount on hand
    $preparedSUB = $pdo2->prepare("UPDATE QUANTITY SET quantity = (quantity - ?) WHERE number = ?;");
    $preparedSUB->execute(array($quantity, $partsnums));

    $to      = '$email';
    $subject = 'Chop Shop Order Number: $orderNum';
    $message = 'Hello, your order has been shipped';
    mail($to, $subject, $message);

    echo "<br/>Email Sent";
}

?>

  <br/><br/>
  <a href="http://students.cs.niu.edu/~z1836033/467_Packages.php">
    <font color="blue" size="+2">RETURN TO ORDER LIST</font>
  </a>

 </div>
 </body>
</html>
