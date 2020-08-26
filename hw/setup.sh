#!/bin/sh
# HW Farm project (c) Dmitry Grigoryev, 2020
# Released under the terms of GNU AGPLv3

sudo apt-get install arduino-mk expect php schroot
sudo usermod -a -G dialout www-data
sudo chown -R www-data:www-data configs libraries sessions
sudo cp hwfarm.conf /etc/schroot/chroot.d/
sudo service schroot restart
