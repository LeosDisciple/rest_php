# Implementation of REST APIs with PHP
This page describes how a REST API can be implemented in PHP

It follows these steps which are explained more in detail:
1. Processing client requests
2. Data operations
  1. Data validation
  2. Database operations
3. Response to client

## Use case: Notes
To illustrate the mechanisms we are implementing a backend that stores **notes**.

### Operations
The client can perform the following operations with the backend
1. **Create** a new note
2. Make the following **Read** operations
  1. Get one note
  2. Get all notes
  3. Get all notes with pagination
  4. Get all public notes or all private notes
3. **Update** a note
4. **Delete** a note

### Data structure
A note has the following structure:
* **Id:** identifies the note
* **Title**: title of the note
* **Content**: content of the note
* **Public/private**: if the note is public or private
* **Date**: the date of the note

## Processing client requests
### Endpoints
The different client to their operations on the following endpoints:

#### POST
http://localhost/v2/notes

#### GET
http://localhost/v2/notes/[NoteID] \
http://localhost/v2/notes/private \
http://localhost/v2/notes/public \
http://localhost/v2/notes \
http://localhost/v2/notes/page/[PageNumber]

#### PUT
http://localhost/v2/notes/[NoteID]

#### DELETE
http://localhost/v2/notes/[NoteID]

### Endpoint routing
The client requests are all routed to the **controller** (here *note.php*) which handles the requests.\
For the controller to get the requests in the right form they need to be routed properly.\
This is done by adding the following configurations in the *.htaccess* file:
```
php_flag display_errors on

RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_FILENAME} !-f

# Puts the id into a parameter called "noteid"
RewriteRule ^notes/([0-9]+)$ controller/notes.php?noteid=$1 [L]

# Tranforms the text "public" into a parameter value "is_public=Y"
RewriteRule ^notes/public$ controller/notes.php?is_public=Y [L]

# Tranforms the text "public" into a parameter value "is_public=N"
RewriteRule ^notes/private$ controller/notes.php?is_public=N [L]

# Puts the page number into a parameter called "page"
RewriteRule ^notes/page/([0-9]+)$ controller/notes.php?page=$1 [L]

# Reroutes a parameter free request to the controller
RewriteRule ^notes$ controller/notes.php [L]
```
This mechanism makes sure the request paramaters have the correct structure and routes them to the **controller** (here *note.php*). \
The controller receives the relevant information stored in paramaters like *'noteid'*, *'is_public'* or *'page'*. \
These will be accessed by the controller through the **$_GET** superglobal variable.

### Processing the request
The controller goes through the following process:
1. Check the request method with **$_SERVER['REQUEST_METHOD']** for **POST, GET, PUT and DELETE** and retrieve the client parameter through **$_GET**
2. Create a instance of the mode **Note.php**
3. JSON payload operations (only for POST and PUT):
  1. Check JSON payload consistency with *checkContentTypeJson()*
  2. Inject the payload JSON data from client with *injectJson()*
4. Call the corresponding method on the object:
  1. *writeToDB()*
  2. *readFromDB(), readAllFromDB(), readPage(), readAllPublicPrivate()*
  3. *updateOnDB()*
  4. *deleteFromDB()*

## Data validation
The client data is validated in different areas of the application:
1. Endpoint routing (incl. parameter validation) \
As described in the corresponding chapter above *Endpoint routing*, the routing makes sure the parameters have the correct structure (as defined in the *.htaccess* file)

2. JSON payload validation \
As described in the above chapter *Processing the request* the controller checks the payload consistency with *checkContentTypeJson()*

3. Attribute validation\
Then the JSON payload from client is injected into the *Note* object with *injectJson()*. Attributes are set to *null* if they are not in the JSON payload. This function also checks that all values from the payload are structured as defined, it uses the *injectAttributes()* method to do that.

### Invalid data
If data is invalid according to the definition, a **Response** object with the corresponding **HTTP status** and **error message** is returned to the client.

## Response
The server returns a response in case the request could be processed **successfully** or if an **error** occurred in the process.

### Successful request
If the request could be processed successfully, the server processes the request and returns a success status and depending on the request type the requested data.

### Error
If there was a error during the process (for example wrong request parameters from the client or a problem during the process) the server returns an error message and an explanation describing the problem.

### Structure of Response
The response has the following structure:
* Operation outcome:
  * StatusCode
  * Success -> boolean signalling operation outcome
  * Message -> contains a list of humanly readable messages from the server
* Data
  * RowsReturned -> Number of objects returned
  * "Tasks" -> List of every object returned

Here's an example of a the response after a successful GET request:
```
{
    "statusCode": 200,
    "success": true,
    "message": [
        "All good =D..."
    ],
    "data": {
        "rows_returned": 1,
        "notes": [
            {
                "id": "25",
                "title": "Test Post Title",
                "content": "Barilla Description",
                "is_public": "Y",
                "date": "01/01/1990 12:53"
            }
        ]
    }
}
```

## Data processing
The data processing is executed by the model. The model is implemented in the **Note.php** class in this use case.The model contains all the operation specific to that class.\
It contains the following
* Attributes
* Methods to check the validity of the client's input payload (only for POST and PUT):
  1. *checkContentTypeJson()* checks JSON payload consistency
  2. *injectJson()* injects the payload JSON data from client into the attributes. It also checks the consistency of every input
* Methods to interact with the database:
  1. *writeToDB()* to write an object into the DB
  2. *readFromDB(), readAllFromDB(), readPage(), readAllPublicPrivate()* to read the requested data from the DB
  3. *updateOnDB()* to update an object on the DB
  4. *deleteFromDB()* to delete an object in the DB

### The data structure
We will be using a data structure called **Notes** which is used to store notes on the server.\
The will have the following structure:

|Name|Type|Mandatory|
|------|-----|-----|
|id|int(20)|yes|
|title|String(25)|yes|
|content|String(250)|no|
|isPublic|boolean: Y/N|yes|
|date|Date|no|

### Creating DB and table using phpMyAdmin
1. Go into phpMyAdmin
2. Create new **database**
  1. Click "+New" (top left) to create new database
  2. Enter name: for example "notesdb" (with utf8_general_ci)
3. Create new **table**
  1. Go to "+Create table" to create new table
  2. Enter name: for example "tblnotes" (select number of rows, e.g. "5")
  3. Click "Go"
4. Define **fields** (SQL uses snake notation, see 'is_public') -> press "Go"

|Name     |Type         |Mandatory|Primary|Null |Default  |Extra  |
|------   |-----        |-----    |-----  |-----|-----    |-----  |
|id       |bigint(20)   |X        |X      |No   |No       |AUTO_INCREMENT|
|title    |varchar(25)  |X        |-      |No   |No       |       |
|content  |varchar(250) |-        |-      |Yes  |NULL     |       |
|is_public |enum('Y', 'N')|X      |-      |No   |N        |       |
|date     |datetime     |-        |-      |Yes  |NULL     |       |

Tips for setting up the fields:
* id -> set type=BIGINT, set Index=PRIMARY (don't define length), AUTO_INCREMENT
* is_public set as enum with values 'Y' and 'N' and set default "as defined" -> 'N'

### PHP MySQL queries
Here's a list of often used queries: [PHP MySQL queries](php_mysql.html).
