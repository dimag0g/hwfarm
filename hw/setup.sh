#!/bin/sh
# HW Farm project (c) Dmitry Grigoryev, 2020
# Released under the terms of GNU AGPLv3

apt-get install arduino-mk expect php
usermod -a -G dialout www-data
chown -R www-data:www-data configs libraries sessions
