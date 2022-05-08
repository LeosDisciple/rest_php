# Process requests
This page describes how client requests are processed.

## Endpoint routing
The different client requests have the following form:



The client requests are all routed to the **controller** (here *note.php*) which handles the requests.\
For the controller to get the requests in the right form they need to be routed properly.\
This is done by adding the following configurations in the *.htaccess* file:
```
# TODO: Unclear what these ones do...
php_flag display_errors on

# TODO: Unclear what these ones do...
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
