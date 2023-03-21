#!/bin/sh

# Laravel requires some directories to be writable.

sudo chmod -R 755 storage/
sudo chmod -R 755 bootstrap/cache/