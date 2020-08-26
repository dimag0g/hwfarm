#!/bin/bash
# HW Farm project (c) Dmitry Grigoryev, 2020
# Released under the terms of GNU AGPLv3

ROOT="."

mount_chroot() {
    for dir in $@ ; do
        mkdir -p $ROOT/$dir
        mount --bind -o ro $dir $ROOT/$dir
    done
}

mount_chroot /etc/alternatives
mount_chroot /lib
mount_chroot /var
mount_chroot /bin
mount_chroot /usr

cp /etc/avrdude.conf $ROOT/etc

#mount --rbind /dev $ROOT/dev
mkdir -p $ROOT/dev

[ -f $ROOT/dev/null ] || {
    mknod $ROOT/dev/null c 1 3
    chmod 666 $ROOT/dev/null
}

[ -f $ROOT/dev/random ] || {
    mknod $ROOT/dev/random c 1 8
    chmod 666 $ROOT/dev/random
}

[ -f $ROOT/dev/urandom ] || {
    mknod $ROOT/dev/urandom c 1 9
    chmod 666 $ROOT/dev/urandom
}

# all serial ports used in hwlist.csv must be exported to chroot

mknod_chroot() {
    for dev in $@ ; do
	[ -f $ROOT/$dev ] || {
	    mknod $ROOT/$dev c `grep ttyACM /proc/devices | cut -f1 -d' '` 0
	    chown root:dialout $ROOT/$dev
	    chmod 660 $ROOT/$dev
	}
    done
}

mknod_chroot /dev/ttyACM0

mkdir -p $ROOT/tmp
chown -R www-data:www-data $ROOT/tmp
