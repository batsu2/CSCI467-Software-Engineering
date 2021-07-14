<html>
   <head>
        <title>CHOP SHOP Auto Parts - Competitive prices at questionable legitimacy </title>
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
          font-size:large;
          font-family: "Arial Black", Gadget, sans-serif;
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





  // Connect to QUANTITY, ORDERS, and SHIPPINGCOST DB

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



  // Submit querys and print table

    $preparedPARTS = $pdo->prepare("SELECT parts.number, parts.description, parts.price, parts.weight, parts.pictureURL FROM parts;");
    $preparedPARTS->execute();

    $preparedQTY = $pdo2->prepare("SELECT QUANTITY.number, QUANTITY.quantity FROM QUANTITY;");
    $preparedQTY->execute();


  echo "<div class=\"center\">";
  echo "<image src=\"gearsIcon.png\" style=\"width:90px;height:90px;\" alt=\"logo\" >";
  echo " <h1>CHOP SHOP Auto Parts</h1>";
  echo "<h4>\"Competitive Prices at Questionable Legitimacy!\"</h3>";

  // Outer table to hold two inner tables (for parts and quantities)
   echo "<table class=\"center\" border = 1 bgcolor=\"FFFFFF\" height=\"13575\" width=\"85%\" >";

  echo "<tr><td>";
   echo "<table class=\"center\" border = 1 bgcolor=\"FFFFFF\" width=\"100%\">";

   echo "<tr><th>Pic</th><th>Number</th><th>Description</th><th>Weight</th><th>Price</th><th>Buy</th></tr>";


   // Print Parts table

     while($rows = $preparedPARTS->fetch(PDO::FETCH_BOTH) )
     {
        echo "<form action=\"http:\/\/students.cs.niu.edu/~z1836033/467_Project_Pg2.php\" method=\"POST\">";
    ?>
        <tr height="50">
        <td><image src="<?php echo $rows["pictureURL"]; ?>" height="90"></td>
     <?php
        echo "<td>".$rows["number"]."</td><td>".$rows["description"]."</td><td>".$rows["weight"]."</td><td>"."$".$rows["price"]."</td>";
        echo "<td><input type=\"text\" name=\"quantity\" id=\"roll-input\" placeholder=\"QTY\" size=\"4\"> <br/> <input type=\"submit\" value=\"Buy\" onClick=\"return tooMuch()\"> </td>";
        echo "</tr>";

        echo "<input type=\"hidden\" name=\"pPrice\" value=\"".$rows["price"]."\" >";
        echo "<input type=\"hidden\" name=\"pWeight\" value=\"".$rows["weight"]."\" >";
        echo "<input type=\"hidden\" name=\"partNum\" value=\"".$rows["number"]."\" >";

	echo "</form>";
     }

   echo "</table>";
   echo "</td><td>";

  echo "<table class=\"center\" border = 1 bgcolor=\"FFFFFF\" height=\"100%\" width=\"100%\" >";
  echo "<tr height=\"10\"><th>OnHand</th></tr>";

 // Show quantity on hand lined up with parts table
 while($rows2 = $preparedQTY->fetch(PDO::FETCH_BOTH) )
     {
        echo "<tr height=\"90\">";
        echo "<td><font id=\"OnHand\">".$rows2["quantity"]."</font></td>";
        echo "</tr>";
     }

  echo "</td>";

   echo "</table>";
   echo "</table>";
  echo "</div>";
  ?>
 </body>
</html>
