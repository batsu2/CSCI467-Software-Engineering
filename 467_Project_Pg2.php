<html>
   <head>
        <title>CHOP SHOP Auto Parts - Competitive prices at questionable legitimacy </title>
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

 <script>
        function orderConfirm()
        {
          var x = confirm("Submit Order? Please confirm all information entered is correct.");

         if( x == true)
          return true;
         else
          return false;
        }
 </script>

    <body bgcolor="#6e35f2">
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





  // Connect to Quantity, SHIPPINGCOST, and ORDERS DB

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



  // Check if submitted quantity is less than amount on hand
    $preparedQTY = $pdo2->prepare("SELECT * FROM QUANTITY WHERE number = ? ;");
    $preparedQTY->execute(array($_POST['partNum']));


  // Retrieve quantity on hand for item
   while($rows = $preparedQTY->fetch(PDO::FETCH_BOTH) )
     $onHand = $rows['quantity'];


   echo "<div class=\"center\">";

  // ERROR CHECKING if customer requested higher quanitity than is on hand
  if( $_POST['quantity'] > $onHand )
  {
    echo "<h1><font color=\"red\" > CANNOT PURCHASE MORE THAN WE HAVE ON HAND!<br/> PLEASE RESELECT SMALLER QUANTITY!</font></h1>";

    echo "<br/><br/> <a href=\" http:\/\/students.cs.niu.edu\/~z1836033\/467_Project_Pg1.php\" >";
    echo "<font color=\"white\" size=\"+2\">RETURN TO ITEM LIST</font></a>";
  }
  else
  {

    $number = $_POST['partNum'];
    $price = $_POST['pPrice'];


  // Default quantity to 1 if field left empty
   if( $_POST['quantity'] == "" )
    $quantity = 1;
   else
    $quantity = $_POST['quantity'];


    $preparedDJ = $pdo->prepare("SELECT parts.number, parts.description, parts.price, parts.weight, parts.pictureURL FROM parts WHERE parts.number = :number;");
    $preparedDJ->bindParam(':number', $number);
    $preparedDJ->execute();


    $preparedSHIP = $pdo2->prepare("SELECT * FROM SHIPPINGCOST;");
    $preparedSHIP->execute();

 // Determine total cost

   $totalWeight = $_POST['pWeight'] * $quantity;

    while($rows = $preparedSHIP->fetch(PDO::FETCH_BOTH) )
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


    $itemTotal = $price * $quantity;
    $total = ($price * $quantity) + $shipping;

 // Print selected buy item

   echo "<h1>CHOP SHOP Auto Parts</h1>";
   echo "<table class=\"center\" border = 1 bgcolor=\"FFFFFF\" width=\"85%\">";

   echo "<tr><th>Pic</th><th>Number</th><th>Description</th><th>Weight</th><th>Price</th><th>Qty to Buy</th></tr>";


     while($rows = $preparedDJ->fetch(PDO::FETCH_BOTH) )
     {
    ?>
        <tr>
        <td><image src="<?php echo $rows["pictureURL"]; ?>" /></td>
     <?php
        echo "<td>".$rows["number"]."</td><td>".$rows["description"]."</td><td>".$rows["weight"]."</td><td>"."$".$rows["price"]."</td>";
        echo "<td>QTY:".$quantity."</td>";
        echo "</tr>";
     }

  // Print shipping and totals

   echo "<tr><td></td><td></td><td></td><td>x".$quantity."</td><td>$".$itemTotal."</td><td></td></tr>";
   echo "<tr><td></td><td></td><td></td><td>SHIPPING: </td><td>$".number_format((float)$shipping, 2, '.', '')."</td><td></td></tr>";
   echo "<tr><td></td><td></td><td></td><td>TOTAL: </td><td>$".number_format((float)$total, 2, '.', '')."</td><td></td></tr>";


   echo "</table>";
  echo "</div>";




  echo "<form action=\"http:\/\/students.cs.niu.edu/~z1836033/467_Project_Pg3.php\" method=\"POST\">";
  echo "<div class=\"center\">";
  echo "<h3>Please enter your shipping information</h3>";


  echo "<table class=\"center\" border = 0 bgcolor=\"FFFFFF\" width=\"85%\">";
  echo "<tr><th>Shipping Address</th><th>Payment Info</th></th><th>";
  echo "<tr><td>";

  // Form for entering shipping info

  echo"Full Name: <input type=\"text\" name=\"name\" placeholder=\"Enter Name\"> <br/>";
  echo"&emsp;Address:   <input type=\"text\" name=\"street\" placeholder=\"Enter Street Address\"> <br/>";
  echo"&emsp; &emsp; City: <input type=\"text\" name=\"city\" placeholder=\"Enter City\"> <br/>";
  echo"&emsp; &emsp;State: <input type=\"text\" name=\"state\" placeholder=\"Enter State\"> <br/>";
  echo"&nbsp; ZIP Code:  <input type=\"text\" name=\"zip\" placeholder=\"Enter ZIP Code\"> <br/>";
  echo"&emsp;&nbsp; E-mail: <input type=\"text\" name=\"email\" placeholder=\"Enter Email\"><br/>";


  echo "</td><td>";


  // Form for entering credit card info

  echo"&nbsp; Cardholder's Name: <input type=\"text\" name=\"ccName\" placeholder=\"Enter Name\"> <br/>";
  echo"Credit Card Number: <input type=\"text\" name=\"ccNum\" placeholder=\"0000 0000 0000 0000\"> <br/>";
  echo"&emsp; &nbsp; Expiration Date: <input type=\"text\" name=\"date\" placeholder=\"MM/YYYY\"> <br/>";


  echo "</td></tr>";
  echo "</table>";

  // Hidden values to be used with credit card authorization system

  echo "<input type=\"hidden\" name=\"vendor\" value=\"VE078-99\" >";
  echo "<input type=\"hidden\" name=\"total\" value=\"".$total."\" >";
  echo "<input type=\"hidden\" name=\"quantity\" value=\"".$quantity."\" >";
  echo "<input type=\"hidden\" name=\"number\" value=\"".$number."\" >";
  echo "<input type=\"hidden\" name=\"shipping\" value=\"".$shipping."\" >";
  echo "<input type=\"hidden\" name=\"itemTotal\" value=\"".$itemTotal."\" >";

  echo"<input type=\"reset\" value=\"Reset\"> <input type=\"submit\" value=\"Submit Order\" onclick=\"return orderConfirm()\" >";

  echo "</form>";

  } // end quantity check

  echo "</div>";
 ?>
 </body>
</html>
