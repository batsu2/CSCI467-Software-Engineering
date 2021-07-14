<html>
   <head>
        <title>Complete Your Order</title>
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
          font-size:large;
      }
    </style>
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


   $number = $_POST['number'];

   $preparedDJ = $pdo->prepare("SELECT parts.number, parts.description, parts.price, parts.weight, parts.pictureURL FROM parts WHERE parts.number = :number;");
   $preparedDJ->bindParam(':number', $number );
   $preparedDJ->execute();




  // Connect to QUANTITY, SHIPPINGCOST, and ORDERS DB

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

  // TRANSACTION NUM GENERATOR, HITTING REFRESH WILL SEND ANOTHER REQUEST WITH NEW NUMBER!!!!!
   $midNum = rand( 100000000, 999999999 );
   $trans = "907-".$midNum."-296";


  // Attempt to Authorize credit card

    $url = 'http://blitz.cs.niu.edu/CreditCard/';
    $data = array(
	'vendor' => $_POST['vendor'],
	'trans' => $trans,
	'cc' => $_POST['ccNum'],
	'name' => $_POST['ccName'],
	'exp' => $_POST['date'],
	'amount' => $_POST['total']);


   $options = array(
      'http' => array(
        'header' => array('Content-type: application/json', 'Accept: application/json'),
        'method' => 'POST',
        'content'=> json_encode($data)
       )
    );

   $context  = stream_context_create($options);
   $result = file_get_contents($url, false, $context);


 //convert JSON variable to array for testing
  $resultArray = json_decode($result,true);
  $key = 'authorization';






  echo "<div class=\"center\">";


  // If authorization succeeded, Print results
  if( array_key_exists( $key, $resultArray ))
  {
   $dateTime = date('Y-m-d');
   $shortCC = substr($_POST['ccNum'], -4);

   echo "<h1>ORDER COMPLETE</h1>";
   echo "<h3><font color=\"red\">DO NOT RELOAD PAGE! RELOADING WILL CAUSE RESUBMISSION OF YOUR ORDER!</font></h3>";
   echo "<table class=\"center\" border = 1 bgcolor=\"FFFFFF\" width=\"85%\">";

   echo "<tr><th>Pic</th><th>Number</th><th>Description</th><th>Weight</th><th>Price</th><th>Qty to Buy</th></tr>";


   //Print Chosen Product
     while($rows = $preparedDJ->fetch(PDO::FETCH_BOTH) )
     {
    ?>
        <tr>
        <td><image src="<?php echo $rows["pictureURL"]; ?>" /></td>
     <?php
        echo "<td>".$rows["number"]."</td><td>".$rows["description"]."</td><td>".$rows["weight"]."</td><td>"."$".$rows["price"]."</td>";
        echo "<td>QTY:".$_POST['quantity']."</td>";
        echo "</tr>";

        echo "<input type=\"hidden\" name=\"partNum\" value=\"".$rows["number"]."\" >";
     }





     // Print shipping and totals
   echo "<tr><td></td><td></td><td></td><td>x".$_POST['quantity']."</td><td>$".$_POST['itemTotal']."</td><td></td></tr>";
   echo "<tr><td></td><td></td><td></td><td>SHIPPING: </td><td>$".number_format((float)$_POST['shipping'], 2, '.', '')."</td><td></td></tr>";
   echo "<tr><td></td><td></td><td></td><td>TOTAL: </td><td>$".number_format((float)$_POST['total'], 2, '.', '')."</td><td></td></tr>";


   echo "</table>";

  echo "<div class=\"center\">";
  echo "<h3>Shipping Information</h3>";


  // Print shipping info
  echo "<table class=\"center\" border = 0 bgcolor=\"FFFFFF\" width=\"85%\">";
  echo "<tr><th>Shipping Address</th><th>Payment Info</th></th><th>";
  echo "<tr><td>";
  echo $_POST['name']." <br/>";
  echo $_POST['street']."<br/>";
  echo $_POST['city'].", ".$_POST['state']." ".$_POST['zip']."<br/>";
  echo $_POST['email']."<br/>";

  echo "</td><td>";

  // Print Card info
  echo "Card Name: ".$_POST['ccName']."<br/>";
  echo "Last 4 of Card Number: ".$shortCC."<br/>";
  echo "Exp: ".$_POST['date']."<br/>";
  echo "Confirmation Number: ".$resultArray['authorization'];

  echo "</td></tr>";
  echo "</table>";


  // Insert order into ORDERS table

         $preparedInto = $pdo2->prepare("INSERT INTO ORDERS VALUES ( DEFAULT, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, false);");
         $preparedInto->execute(array($_POST['name'], $_POST['street'], $_POST['city'], $_POST['state'], $_POST['zip'], $_POST['email'], $_POST['total'], $dateTime, $_POST['number'], $_POST['quantity'] ));

  // Send Confirmation Email
   $to = $_POST['email'];
   $subject = "Order Confirmation";
   $text = "Your Order with CHOP SHOP auto parts is set.\n\nConfirmation Number: ".$resultArray['authorization'].".\nWe'll Email again when it's on its way!\n\nThank you for your buisiness.";
   $headers = "From: auto@CHOPSHOP.com";

   mail( $to, $subject, $text, $headers);
  }
  else
  {
    // If authorization failed, try to pull error info from JSON data and print
   $e = substr( $result, 123, 39);

   echo "<h1>There was a problem, please re-enter Credit Card Info</h1>";
   echo "<h2 color=\"red\"><font color=\"red\">".$e."</font></h2>";
  }






  ?>


  <br/><br/>
  <a href="http://students.cs.niu.edu/~z1836033/467_Project_Pg1.php">
    <font color="white" size="+2">RETURN TO ITEM LIST</font>
  </a>

   <br/>

  </div>

 </body>
</html>
