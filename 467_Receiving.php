<html>
   <head>
        <title>CHOP SHOP Auto Parts - Receiving Desk </title>
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
    <body bgcolor="#56ab41">
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


  // Update Quantity DB after submission
    if( ( isset($_POST['TBnumber']) && isset($_POST['TBquantity']) ) && (($_POST['TBnumber'] != "") && ($_POST['TBquantity'] != "") )  )
      {
                $preparedUpdate = $pdo2->prepare("UPDATE QUANTITY SET quantity = (quantity + ?) WHERE number = ?;");

          if($preparedUpdate->execute(array($_POST['TBquantity'], $_POST['TBnumber'])))
          {
             echo "<h3>Successful update!</h3>";
          }
      }





  // Prepare queries
   $sql = "SELECT number, description FROM parts";

   $result = $pdo->query($sql);

   $sql2= "SELECT * FROM QUANTITY";

   $sql3= $pdo2->prepare("SELECT * FROM QUANTITY");
   $sql3->execute();

   $result2 = $pdo2->query($sql2);



   // Begin Printing page
  echo "<h1>CHOP SHOP Auto Parts - Receiving Desk</h1>";

  echo "<form action=\"\" method=\"POST\" >";
  echo "<input type=\"text\" name=\"search\" placeholder=\"Search Name or Number\" >";
  echo "&emsp; <input type=\"submit\" value=\"Search\" >";

  echo "</form>";


  // Print Table

  // Submit search query before printing
  if( isset( $_POST['search']) )
   {
     if( is_numeric( $_POST['search'] ) )
      {
       $preparedSEARCH = $pdo->prepare("SELECT * FROM parts WHERE number = :num ;");
       $preparedSEARCH->bindParam(':num', $_POST['search'] );
       $preparedSEARCH->execute();

      }
     else
      {
       $name = $_POST['search'];
       $name = "%".$name."%";
       $preparedSEARCH = $pdo->prepare("SELECT * FROM parts WHERE description LIKE :desc ;");
       $preparedSEARCH->bindParam(':desc', $name );
       $preparedSEARCH->execute();
      }

   $numArray = array();

   echo "<table class=\"center\" border=1 bgcolor=\"FFFFFF\" style=\"float: left\" width=\"600\">";
   echo "<tr><th>Part Number</th><th>Description</th></tr>";

   while( $rows = $preparedSEARCH->fetch(PDO::FETCH_BOTH) )
     {
      echo "<tr><td>".$rows["number"]."</td><td>".$rows["description"]."</td></tr>";

      $numArray[] = $rows["number"];
     }

   echo "</table>";

   echo "<table class=\"center\" border=\"1\" bgcolor\"FFFFFF\" style=\"float: left\" width=\"50\">";
   echo "<tr><th>Quantity</th></tr>";


   // Create array to fill with quantities
    $qtyArray = array();


    // Fill array with quantity values
     while( $rows2 = $sql3->fetch(PDO::FETCH_BOTH) )
      {

       foreach( $numArray as $testArrayVal )
       {
        if( $testArrayVal == $rows2['number'] )
         {
          $qtyArray[] = $rows2['quantity'];
         }
        }
      }



  // Print quantities table
   foreach ($qtyArray as $row)
     {
     echo "<tr><td>".$row."</td></tr>";
     }

   echo "</table>";

  }
  else
  {

  // Print part name and num table
  echo "<table class=\"center\" border=1 bgcolor=\"FFFFFF\" style=\"float: left\" width=\"600\">";
  echo "<tr><th>Part Number</th><th>Description</th></tr>";

      foreach ($pdo->query($sql) as $row) {
        echo "<tr><td>".$row["number"]."</td><td>".$row["description"]."</td></tr>";
      }

      echo "</table>";

      echo "<table class=\"center\" border=\"1\" bgcolor\"FFFFFF\" style=\"float: left\" width=\"50\">";
      echo "<tr><th>Quantity</th></tr>";

      foreach ($pdo2->query($sql2) as $row) {
        echo "<tr><td>".$row["quantity"]."</td></tr>";
      }

      echo "</table>";
   }


      echo "<br></br>";


  ?>
		<form method="POST" action="">
      <p>
      &nbsp; Type the part number and the quantity you want to submit
      <br><br>
       <input type="text" name="TBquantity" style="position:relative; left: 40px" size="4"/> <span style="padding-left:55px"/> Amount to add to current quantity <br><br>
       <input type="text" name="TBnumber" style="position:relative; left: 40px" size="4"/> <span style="padding-left:55px"/> To Part Number <br><br>
       <input type="submit" name="select" style="position:relative; left: 40px"/> </p>

<?php
      if( isset($_POST['search']) )
       echo "<input type=\"hidden\" name=\"search\" value=\"".$_POST['search']."\" >";

?>
      </form>

 </body>
</html>
