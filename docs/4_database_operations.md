



### Creating the database
#### The JSON structure
|Name|Type|Mandatory|
|------|-----|-----|
|id|int(25)|yes|
|title|String(25)|yes|
|content|String(250)|no|
|isPublic|boolean|yes|
|date|Date|no|

#### Using phpMyAdmin
1. Go into phpMyAdmin
2. Create new **database**
  1. Click "+New" (top left) to create new database
  2. Enter name: for example "notesdb" (with utf8_general_ci)
3. Create new **table**
  1. Go to "+Create table" to create new table
  2. Enter name: for example "tblnotes" (select number of rows, e.g. "5")
  3. Click "Go"
4. Define **fields** (SQL uses snake notation, see 'is_public') -> press "Go"
|Name     |Type       |Mandatory|Primary|Null |Default  |Extra  |
|------   |-----      |-----    |-----  |-----|-----    |-----  |
|id       |bigint(20) |X        |X      |No   |No       |AUTO_INCREMENT|
|title    |varchar(25)|X        |-      |No   |No       |       |
|content  |varchar(250)|-       |-      |Yes  |NULL     |       |
|is_public |enum('Y', 'N')|X     |-      |No   |N        |       |
|date     |datetime   |-        |-      |Yes  |NULL     |       |

  1. Some tips for setting up the fields
    1. id -> set type=BIGINT, set Index=PRIMARY (don't define length), AUTO_INCREMENT
    2. is_public set as enum with values 'Y' and 'N' and set default "as defined" -> 'N'

### PHP MySQL queries
Here's a list of often used queries: [PHP MySQL queries](php_mysql.html).
