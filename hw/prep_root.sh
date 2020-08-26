#!/bin/bash
# HW Farm project (c) Dmitry Grigoryev, 2020
# Released under the terms of GNU AGPLv3

ROOT="."

# Bind system directories
for dir in /lib /bin /usr ; do
    mkdir -p $ROOT/$dir
    sudo mount --bind -o ro $dir $ROOT/$dir
done

# Create custom /etc
mkdir -p $ROOT/etc
mkdir -p $ROOT/etc/alternatives
cp /etc/avrdude.conf $ROOT/etc
cp /etc/alternatives/awk $ROOT/etc/alternatives/

# Create custom /dev
mkdir -p $ROOT/dev

[ -f $ROOT/dev/null ] || {
    sudo mknod $ROOT/dev/null c 1 3
    sudo chmod 666 $ROOT/dev/null
}

[ -f $ROOT/dev/random ] || {
    sudo mknod $ROOT/dev/random c 1 8
    sudo chmod 666 $ROOT/dev/random
}

[ -f $ROOT/dev/urandom ] || {
    sudo mknod $ROOT/dev/urandom c 1 9
    sudo chmod 666 $ROOT/dev/urandom
}

# all serial ports used in hwlist.csv must be exported to chroot
for dev in /dev/ttyACM0 ; do
    [ -f $ROOT/$dev ] || {
        devclass=`echo $dev | head -c-2 | tail -c-5`
        sudo mknod $ROOT/$dev c `grep $devclass /proc/devices | cut -f1 -d' '` 0
        sudo chown root:dialout $ROOT/$dev
        sudo chmod 660 $ROOT/$dev
    }
done

# Create custom /tmp
mkdir -p $ROOT/tmp
sudo chmod 666 $ROOT/tmp
