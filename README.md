# hwfarm
A web interface for remote access to Arduino boards
(and potentially other hardware as well).
Relies on `Arduino.mk` for compilation/uploads.
Uses `expect` for user IO handling on the serial port.

A working installation can be seen at https://dimagog.ddns.net/hwfarm/

Installation
---

The PHP files should be placed under webserver's root (typically `/var/www`).

The working directory (`hw`) which holds the hardware configurations
and compilation results could (and should) be placed elsewhere.
Its location is defined in `hw/hwfarm.conf`, which should be placed in
`/etc/schroot/chroot.d/`.

Running `hw/setup.sh` should install all necessary packages,
expect for the web server, which should already be installed.

On Linux it is also recommended to install a recent version of
Arduino IDE, instead of the one provided by the package manager.
A custom Arduino IDE location must be specified in `$ARDUINO_DIR`
variable in every board configuration `Makefile` (see below).

The available hardware is described in `hwlist.csv` which contains
pairs "config_name","serial_port". Several configurations could be
defined for the same port.

All used ports must be exported into the `chroot` jail by modifying
`hw/prep_root.sh`. This file must be run after every reboot.

Each board configuration is represented by a folder under
`hw/configs/config_name`, which should contain a `Makefile`, a sample
software in `sketch.ino` and a corresponding IO script in `input.exp`.

- the `Makefile` should support `make` and `make upload` targets. The
existing `Makefile` simply includes `Arduino.mk` which supports these.

- `sketch.ino` should be compatible with the installed Arduino IDE.

- `input.exp` contains an Expect script defining user input and output.
If no input is required, simply logging all data from the serial port
can be done by a single line, `expect eof`.

Additional Arduino libraries can be installed e.g. under `hw/libraries`.
Their location must be set in `$USER_LIB_PATH` variable in the `Makefile`.

Finally `hw/sessions` is the directory where all temporary files live.

Todo
---

- Implement cleanup of expired sessions
