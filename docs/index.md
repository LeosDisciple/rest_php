# REST APIs with PHP
## General
This project describes how to create REST APIs with PHP.\
It shows the reasoning behind the implementation and explains the code.\
The project is based on the Udemy course [Create a REST API using basic PHP with Token Authentication by Michael Spinks](https://www.udemy.com/course/create-a-rest-api-using-basic-php-with-token-authentication/)

## Why PHP?
PHP is an established programming language for the web. Many hosting providers allow you to develop web applications based on [XAMPP](https://en.wikipedia.org/wiki/XAMPP) in PHP. These are the reasons why it can be very interesting and cost effective to create REST APIs with PHP.

## About REST APIs
### What are they?
REST APIs are a popular way of implementing backend interfaces (see [Representational state transfer](https://en.wikipedia.org/wiki/Representational_state_transfer)).

### Example of a REST API
* www.example.com/tasks
  * Manages the resource "tasks"
* Methods
  * POST
    * Creates a task (CREATE operator)
    * POST GET www.example.com/tasks + JSON object creates a new task and returns its id
  * GET
    * Returns tasks (READ operator)
    * GET www.example.com/tasks returns all tasks
    * GET www.example.com/tasks/page/1 returns first page of all tasks ("pagination")
    * GET www.example.com/tasks/1 returns task with id "1"
  * DELETE
    * Deletes task (DELETE operator)
    * DELETE www.example.com/tasks/1 deletes task with id "1"
  * PUT / PATCH
    * Updates task (UPDATE operator)
    * PUT / PATCH www.example.com/tasks/1 updates + JSON object task with id "1"
    * Difference between PUT and PATCH:
      * PATCH is used to update an existing entity with new information. You can’t patch an entity that doesn’t exist.
      * PUT is used to set an entity’s information completely. PUTting is similar to POSTing, except that it will overwrite the entity if already exists or create it otherwise.

### Conventions for REST API definition
#### Endpoints
Use **nouns** and **plurals** for endpoints.
Examples:
* www.example.com/tasks
* www.example.com/cars
* www.example.com/heroes

#### Methods
Methods describes operations that can be executed on an endpoint are defined by the HTTP protocol [Using HTTP Methods for RESTful Services](https://www.restapitutorial.com/lessons/httpmethods.html).
Here are the most common ones and their applications:

|Operation  |Application              |Parameters |Response         |
|---------- |-------------            |---------- |------           |
|POST       |Create new entry         |JSON       |Status, JSON+id  |
|GET        |Read entry               |id         |Status, JSON+id  |
|PATCH      |Update parts of an entry |id, JSON   |Status, JSON+id  |
|PUT        |Update entire entry      |id, JSON   |Status, JSON+id  |
|DELETE     |Delete entry             |id         |Status, JSON     |

#### Responses
After every call on a REST endpoint, the client will receive a **status code** and a **payload (JSON)**.

##### Status Code
Every call on a REST endpoint will return a HTTP response status code informing the client on the outcome of the operation.\
See [List of HTTP status codes](https://en.wikipedia.org/wiki/List_of_HTTP_status_codes).

##### Payload (JSON)
The payload will contain some information about the operation and the actual data returned from the server.\
It has the following structure:
* Operation outcome:
  * StatusCode
  * Success -> boolean signalling operation outcome
  * Message -> contains a list of humanly readable messages from the server
* Data
  * RowsReturned -> Number of objects returned
  * "Tasks" -> List of every object returned

Here's an example of a successful GET request:
```
{
    "statusCode": 200,
    "success": true,
    "message": [
        "All good =D..."
    ],
    "data": {
        "rows_returned": 1,
        "tasks": [
            {
                "id": "25",
                "title": "Test Post Title",
                "description": "Barilla Description",
                "deadline": null,
                "completed": "Y"
            }
        ]
    }
}
```

## The implementation
This chapter shows how the API was implemented in PHP.

### The process
The client sends a request to the server.

#### Quality checks on request
The server goes through the following steps to process the request:
1. Check request method (GET, POST, PATCH/PUT, DELETE)
2. Check if parameters are correct (parameters, content-type, etc.)
3. Check payload (JSON)
  1. Correct structure, correct data types
  2. Correct content (like mandatory fields)

#### Database access
And then the server:
1. Executes the requested access to the database
2. Returns results to the client

### PHP MySQL queries
Here's a list of often used queries: [PHP MySQL queries](php_mysql.html).
