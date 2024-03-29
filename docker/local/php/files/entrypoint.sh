#!/bin/sh

supervisord --nodaemon --configuration /etc/supervisor/supervisord.conf &

php-fpm --nodaemonize
