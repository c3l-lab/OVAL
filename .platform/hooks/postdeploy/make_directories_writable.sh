#!/bin/sh

# Laravel requires some directories to be writable.

sudo chmod -R 777 storage/
sudo chmod -R 777 bootstrap/cache/