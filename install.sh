#!/bin/sh
set -e

rm -f .env
rm -rf var/db var/cache var/session var/log

./maho install -n --force \
  --license_agreement_accepted yes \
  --locale en_US --timezone UTC --default_currency USD \
  --db_engine sqlite --db_name maho --db_name db.sqlite \
  --url http://maho.test/ --use_secure 0 --secure_base_url http://maho.test/ --use_secure_admin 0 \
  --admin_lastname Test --admin_firstname Test --admin_email test@example.com \
  --admin_username admin --admin_password "admin@admin123@"

chmod -R 777 var/*
chmod -R 777 public/*
chmod 777 .env

php script.php
