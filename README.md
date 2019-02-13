## Overview

TorrentFlux-NG is a web based transfer control client.

It allows you to control your bittorrent /wget transfers from anywhere using a highly configurable web based front end for multiple users.


source : http://epsylon3.github.io/torrentflux/

## Prerequisites
A MySQL database

## Quick Start
Pull l'image :
```
docker pull nmanceau/torrentflux-ng
```

### Running
Here an example :
```
docker run -d -e 'DB_HOST=db_server' -e 'DB_NAME=db_name' -e 'DB_USER=db_user' -e 'DB_PASS=db_pass' -v 'data:/var/www/html/downloads' nmanceau/torrentflux-ng
```

## To know !
* At the first login credential, it will create the admin account.

## Environment variable
(i don't think i need to comment :p )

DB_HOST

DB_NAME

DB_USER

DB_PASS

## Volume
/var/www/html/downloads
* download files are store here


## Docker compose example
3 services :
* Torrentflux
* Database
* A web server to download files (it can be a ftp or what you want)
```
version: "3.4"
services:
 torrentflux:
  image: torrentflux:latest
  depends_on:
   - torrentflux_db
  volumes:
   - /data/torrentflux:/var/www/html/downloads
  environment:
   DB_HOST: torrentflux_db
   DB_NAME: torrentflux
   DB_USER: root
   DB_PASS: root

 torrentflux_db:
  image: mariadb:10.3
  command: --transaction-isolation=READ-COMMITTED --binlog-format=ROW
  volumes:
  - torrentflux_db:/var/lib/mysql
  environment:
   MYSQL_ROOT_PASSWORD: root
   MYSQL_DATABASE: torrentflux

 torrentflux_nginx:
  image: nginx:latest
  volumes:
   - /data/torrentflux:/data
```
