# Data validation
This pages discusses the data structure and how it is validated.

## Data structure
We will be using a data structure called **Notes** which is used to store notes on the server.\
The will have the following structure:

|Name|Type|Mandatory|
|------|-----|-----|
|id|int(25)|yes|
|title|String(25)|yes|
|content|String(250)|no|
|isPublic|boolean|yes|
|date|Date|no|

## Data types
