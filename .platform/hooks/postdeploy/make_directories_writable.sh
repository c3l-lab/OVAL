#!/bin/sh

# Laravel requires some directories to be writable.

sudo chmod -R 755 /var/app/current/storage
sudo chmod -R 755 /var/app/current/bootstrap 
sudo chmod -R 755 /var/app/current/bootstrap/cache