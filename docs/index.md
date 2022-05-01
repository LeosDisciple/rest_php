# REST API with PHP
## General
This project describes how to create REST APIs with PHP.\
It shows the reasoning behind the implementation and explains the code.\
The project is based on the Udemy course [Create a REST API using basic PHP with Token Authentication by Michael Spinks](https://www.udemy.com/course/create-a-rest-api-using-basic-php-with-token-authentication/)

## About REST APIs
### What are they
REST APIs are a popular way of implementing backend interfaces (see [Representational state transfer](https://en.wikipedia.org/wiki/Representational_state_transfer)).

### Example of a REST API


### Conventions for REST API definition
#### Endpoints
_blabla...._

#### Methods
Methods describes operations that can be executed on an endpoint are defined by the HTTP protocol [Using HTTP Methods for RESTful Services](https://www.restapitutorial.com/lessons/httpmethods.html).
Here are the most common ones and their applications:

|Operation|Application|
|----------|-------------|
|POST|Create new entry|
|GET|Read entry|
|PATCH|Update parts of an entry|
|PUT|Update entire entry|
|DELETE|Delete entry|

#### Responses
Every call on a REST endpoint will return a HTTP response status code informing the client on the outcome of the operation.\
See [List of HTTP status codes](https://en.wikipedia.org/wiki/List_of_HTTP_status_codes).
