# REST API for RAVS



Code docs:  <https://szymonrykala.github.io/ravsAPI>
Built on top of the Slim 4 micro framework



## Installation
Installation steps:
1. Configure environment by setting env variables specified in Environment section.
2. Create a Cloudinary image transformation named `ravs-image-transformation`.
3. Log into the server or container and execute `/ops/install.php` script.
4. Set up a cron job (or corresponding tool) to each day execute `/ops/cleaning.php` script.
5. Log into Your administrator account with credentials specified in the environment.

### Operation scripts
- `dbConnect.php` - providec connection to the database for following scripts
- `cleaning.php` - executes SQL defined functions for reservations and requests history cleaning purposes
- `install.php` - contains all app instalation logic


## Environment

### Database
This application uses PostgreSQL database with TLS connection.
- `DATABASE_URL` - connection string to database with specified schema: postgres://`user_name`:`user_password`@`host`:`port`/`database_name`


### Administrator account
- `ADMIN_PASSWORD` - definition of admin account password
- `ADMIN_EMAIL` - admin email account


### SMTP
SMTP account is used to send notifications to the users.
- `SMTP_USER` - email address used to send emails
- `SMTP_PASSWORD` - password to email account
- `SMTP_HOST` - mailing host
- `SMTP_PORT` - smtp port
- `SMTP_DEBUG` - debug property, default `0`


### Cloudinary
Cloudinary is an external service for image processing.
- `CLOUDINARY_CLOUD_NAME` - cloud name of cloudinary account
- `CLOUDINARY_SECRET` - cloudinary secret
- `CLOUDINARY_KEY` - cloudinary account api key


### Authorization
- `TOKEN_SECRET` - secret string used to encode and decode the token
- `TOKEN_EXPIRY` - days count when token expiers, default `1` day
- `TOKEN_SIPHER_ALGORITHM` - token encoding algorithm, default `HS512`
    Supported algorithms: `ES384`,`ES256`,`HS256`,`HS384`,`HS512`,`RS256`,`RS384`,`RS512`,`EdDSA`.


### App logging
- `LOG_PATH` - path to file with logs, default `php://stdout`
- `LOGGER_LEVEL` - logger verbosity level
    Avaliable logger levels: `DEBUG`,`INFO`,`NOTICE`,`WARNING`,`ERROR`,`CRITICAL`,`ALERT`,`EMERGENCY`.
- `DISPLAY_ERROR_DETAILS` - default `false`
- `LOG_ERROR_DETAILS` - default `false`
- `LOG_ERROR` - default `false`


## Inital App Settings
Installation script will create one administrative access class and one admin account.
In addition its configured with following parameters which can be updated:
- `DEFAULT_USER_ACCESS` - id of default access for new users
- `MAX_IMAGE_SIZE` - maximum size of image in Bytes
- `MAX_RESERVATION_TIME` - maximum time of reservation in minutes
- `MIN_RESERVATION_TIME` - minimum time of reservation in minutes
- `REQUEST_HISTORY` - history of the requests in days
- `RESERVATION_HISTORY`	- history of reservations in days


## Policies

### Removing objects
- address - cannot remove if contains buildings
- building - cannot remove if contains rooms
- room - while removing all reservations will be deleted
- reservation - cannot remove if reservation is pending
- access - cannot remove if there are users assigned to this access class or it's admin or default user class
- user - if user defetes his account, his data are changed to random strings. To delete account in this state, administrator have to delete such user.

### Picking up keys
- if room do not has a key assigned, the key do not have to be provided
- keys can be piccked up till 1 hour after reservation planned start

### Reservations
#### Creating
Policies which each new reservation have to meet:
- Reserved room cannot be blocked
- Reservation time has to be future
- Time slot need to align with app configuration max. and min. time
- Reservation time have to be done when building is open
- There can be no overlaping reservations

#### Updating
- Reservation has not started
- You can update till day before reservation stat - disabled
- if room is changed - room cannot be blocked
- if time is changed
  - Time slot need to align with app configuration max. and min. time
  - Reservation time have to be done when building is open
  - There can be no overlaping reservations

## Endpoints

