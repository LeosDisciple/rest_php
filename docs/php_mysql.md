# PHP MySQL Requests
This page shows how to write some often used MySQL requests in PHP.

## Retrieve data from form
```
// Retrieve data from form
print "<br>REQUEST:<br>";
print_r($_REQUEST); // Always works and gives same result as the other two =D !
$entry = $_REQUEST['entry'];
print "<br><br>The data to be entered in to DB: ".$entry."<br><br>";
```

## Open DB connection
```
// Open connection
$connection = mysqli_connect("localhost","root","","udemy_php_tutorial");
if (!$connection) {
	print "Connection error: "+mysqli_connect_error();
} else {
	print "Connection to DB successful!";
}
```

## Write
```
// Write into table
print $sql."<br><br>";
print "<br><br>The SQL statement: ".$sql;
mysqli_query($connection, $sql);
print "<br>SQL query finished!";`
```

## Read
```
// Read from table
$sql = "SELECT * FROM tutorial_table";
print "<br><br>".$sql;
$result = mysqli_query($connection, $sql);

while ($row = mysqli_fetch_array($result)) {
	//print "<br>SQL query finished! Result: "; print_r($row);
	print "<br>Result: ".$row['first_name'];
	print " - ".$row['Mark'];
}
print "<br>Finished, value of row: ---".$row."---";
```

## Where statement
```
// WHERE
$sql = "SELECT * FROM tutorial_table WHERE family_name = 'Rose'";
print "<br><br>".$sql;
$result = mysqli_query($connection, $sql);
while ($row = mysqli_fetch_array($result)) {
	//print "<br>SQL query finished! Result: "; print_r($row);
	print "<br>Result: ".$row['first_name'];
}

// SELECT column & ORDER by
$sql = "SELECT first_name FROM tutorial_table ORDER by 'Mark'";
print "<br><br>".$sql;
$result = mysqli_query($connection, $sql);
while ($row = mysqli_fetch_array($result)) {
	//print "<br>SQL query finished! Result: "; print_r($row);
	print "<br>Result: ".$row['first_name'];
}
```

## Update
```
// UPDATE
$sql = "UPDATE tutorial_table SET mark='".rand(7,10)."' WHERE id='1'";
print "<br><br>".$sql;
$result = mysqli_query($connection, $sql);

// Read from table after UPDATe
$sql = "SELECT * FROM tutorial_table";
print "<br><br>".$sql;
$result = mysqli_query($connection, $sql);

while ($row = mysqli_fetch_array($result)) {
	//print "<br>SQL query finished! Result: "; print_r($row);
	print "<br>Result: ".$row['first_name'];
	print " - ".$row['Mark'];
}
print "<br>Finished, value of row: ---".$row."---";
```

## Delete
```
// DELETE
$sql = "DELETE FROM tutorial_table WHERE id>'20'";
print "<br><br>".$sql;
$result = mysqli_query($connection, $sql);

// Read from table after DELETE
$sql = "SELECT * FROM tutorial_table";
print "<br><br>".$sql;
$result = mysqli_query($connection, $sql);

while ($row = mysqli_fetch_array($result)) {
	//print "<br>SQL query finished! Result: "; print_r($row);
	print "<br>Result: ".$row['first_name'];
	print " - ".$row['Mark'];
}
print "<br>Finished, value of row: ---".$row."---";
```

## Close DB connection
```
mysqli_close($connection);
```
