# Readme
This project provides `REST API` in PHP code (`without framework`) to send an email with authorization (using `Oauth2` *php-amqplib* library) security when sending email to the recepient. The emails are also will be stored into the DB (using `PostgreSQL`).

*Please take note that this project is foced on backend side, the frontend is there to run the program easily

## System Design
### Design
In high level design, we can see image below:
![image](https://github.com/farizalhassyr/6d87dc154b0660a2ffa2b652ca9154fa/assets/68814490/11a76eca-a438-4a73-bd0f-a28a7a7874a0)

`Client-side` will make request to Web Server and get the response. From `Server-side`'s (Apache) perspective, it will authorize the user into Google API and save the data into `PHP SESSION` (server-side storage of user-specific data). Data manipulation (transaction) will be executed by `PostgreSQL` based on request by `Web Server`. In another place, `RabbitMQ` will listen to message request called `send_email_queue` and will execute data for each published message.

### Database
We decided to use 1 table for this DB which called by `emails`.
This table contains columns as we can see at image below.
![image (2)](https://github.com/farizalhassyr/6d87dc154b0660a2ffa2b652ca9154fa/assets/68814490/f9608d82-5150-410f-b345-60f280900ffd)



## Installation
### Requirements
- Install [PHP](https://www.php.net/manual/en/install.php) (Currently we're using v8.3)
- Install [Composer](https://getcomposer.org/download/)
- Install [PostgreSQL](https://www.postgresql.org/download/)
- Install [RabbitMQ](https://www.rabbitmq.com/docs/download) (but need to install [Erlang](https://www.erlang.org/downloads) first)
- Install Web Server [Apache](https://httpd.apache.org/download.cgi) / [PHP Server](https://marketplace.visualstudio.com/items?itemName=brapifra.phpserver) / [XAMPP](https://www.apachefriends.org/download.html)
- Setup [Google Oauth2](https://developers.google.com/identity/protocols/oauth2) `CLIENT_ID` and `CLIENT_SECRET` for authorization access (alternatively, can use Facebook, Twitter, etc)
- Setup [Cacert](https://github.com/FilipQL/cacert.pem)

### Optional
- Install [Docker and Docker Compose](https://docs.docker.com/compose/install/)

## How to Run
### Repository
Clone the repo by running this command
```
git clone https://github.com/farizalhassyr/6d87dc154b0660a2ffa2b652ca9154fa.git
```
### Update the composer (optional)
Run this command in the terminal of `/src` directory to update the dependencies.
```
composer update
```
### Migration
Run this url in the browser to create new table `emails`.
```
http://localhost:3000/6d87dc154b0660a2ffa2b652ca9154fa/src/migrations/create_email_table.php
```

### Connect to PostgreSQL Database
Can check these useful URLs to check how to connect to PostgreSQL database:

```
https://www.postgresql.org/docs/current/app-pg-ctl.html

https://tableplus.com/blog/2018/10/how-to-start-stop-restart-postgresql-server.html
```


### Run the PHP with Webserver
Running ways are different for each Webserver, can check `guide` depending on what WebServer you are using.

We use port 3000 in this project. Therefore, the default homepage url is:
```
http://localhost:3000/6d87dc154b0660a2ffa2b652ca9154fa/src/
```
*You can change port based on what number you are using*


### Worker
- Run `RabbitMQ` that has been already installed
- Run worker in `/src/workes/email_worker.php` by using:
```
php email_worker.php
```
- Or, alternatively can click this  [URL](http://localhost:3000/6d87dc154b0660a2ffa2b652ca9154fa/src/workers/email_worker.php) in the browser / postman (don't forget to change port number if you are using different one)


### Environment
We're using `vlucas/phpdotenv` for managing the environment.

Set `.env` file that will be placed in `./src` directory.
Example of my local `.env`:
```
BASE_URL=http://localhost:3000/6d87dc154b0660a2ffa2b652ca9154fa/src/ # adjust with your port/source location

# database
DB_HOST=
DB_NAME=
DB_USERNAME=
DB_PASSWORD=
DB_PORT=


# email
MAIL_HOST=
MAIL_USERNAME=
MAIL_NAME=
MAIL_PASSWORD=
MAIL_PORT=

# oauth2
CLIENT_ID=
CLIENT_SECRET=
REDIRECT_URL=
URL_AUTHORIZE=
URL_ACCESS_TOKEN=
URL_RESOURCE_OWNER_DETAIL= (optional)

# rabbitmq
RABBITMQ_HOST=
RABBITMQ_PORT=
RABBITMQ_USERNAME=
RABBITMQ_PASSWORD=
```

## Running with Docker (optional)
### Dockerfile
Dockerfile will be placed in `./src` directory.

Here's the configuration example:
```Dockerfile
# Use php:8.3-apache runtime as a parent image
FROM php:8.3-apache

# Copy Apache Configuration
COPY 000-default.conf /etc/apache2/sites-available/000-default.conf
COPY start-apache /usr/local/bin
RUN a2enmod rewrite

# Copy application source
COPY src /var/www/
RUN chown -R www-data:www-data /var/www

# Expose the port Apache listens on
# For this example we will use port 9000
EXPOSE 9000

CMD ["start-apache"]
```

### Docker Compose .YML Configuration
`docker-compose.yml` will be placed in root directory.

Here's the configuration example:
```yml
version: "3.9"
services:
  webapp:
    build:
      context: .
      dockerfile: ./src/Dockerfile
    ports:
      - "9000:9000" # adjust with your port
    volumes:
      - ./src:/var/www
    environment:
      # fill with your environment
      # ...
```

### Apache Configuration
`000-default.conf` will be placed in root directory.

Here's the configuration example:
```
<VirtualHost *:9000>
  ServerAdmin webmaster@localhost
  DocumentRoot /var/www/public

  <Directory /var/www>
    Options Indexes FollowSymLinks
    AllowOverride All
    Require all granted
  </Directory>
</VirtualHost>
```

### Build Image
After the configuration is done, we can build the image of our program.

To build image, we can run this command:

```docker build -t [IMAGE NAME] .```


### Run Container
After building the image, now we can run the container by using this command:

```
docker run -d -p 9000:9000 [IMAGE NAME]
```

*You might need to change port based on your configuration previously*

*Tips for Windows user:*

*To make live easier, Docker provides GUI application to build image, run the container, and check which container is running.*

For more detail guide can read these documentations:
- [Docker documentations](https://docs.docker.com/compose/compose-file/build/)
- [Tutorial](https://semaphoreci.com/community/tutorials/dockerizing-a-php-application) with complete example.
