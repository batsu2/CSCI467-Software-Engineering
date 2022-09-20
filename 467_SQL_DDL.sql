create table PARTS(
			number int primary key AUTO_INCREMENT,
			description VARCHAR(255),
			price DECIMAL(15,2),
			weight DECIMAL(4,2),
			pictureURL VARCHAR(255),
			quantity int
		   );



create table QUANTITY(
                         number int primary key,
                         quantity int
                      );


create table ORDERS(
	 	     orderNum INT primary key AUTO_INCREMENT,
                     name VARCHAR(255),
                     address VARCHAR(255),
                     city VARCHAR(255),
                     state VARCHAR(255),
                     zip VARCHAR(255),
                     email VARCHAR(255),
                     total DECIMAL(15,2),
                     dateTime VARCHAR(255),
                     partNum INT,
                     qty INT,
                     shipped BOOL
                  );


create table SHIPPINGCOST(
                       baseCost DECIMAL(4,2),
                       extraCost DECIMAL(4,2)
                     );