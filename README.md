# hwfarm
A web interface for remote access to Arduino boards
(and potentially other hardware as well).
Relies on `Arduino.mk` for compilation/uploads.
Uses `expect` for user IO handling on the serial port.

A working installation can be seen at https://dimagog.ddns.net/hwfarm/

Installation
---

Running `hw/setup.sh` should install all necessary packages,
expect for the web server, which should already be installed.

The PHP files should be placed under webserver's root (typically `/var/www`).

The working directory (`hw`) which holds the hardware configurations
and compilation results could (and should) be placed elsewhere.
PHP files refer to it using `$work_path` variable which must be adapted.

The available hardware is described in `hw/hwlist.csv` which contains
pairs "config_name","serial_port". Several configurations could be
defined for the same port.

Each configuration is represented by a folder under `hw/configs/config_name`,
which should contain a `Makefile`, a sample software in `sketch.ino`
and a corresponding IO script in `input.exp`.

- the `Makefile` should support `make` and `make upload` targets. The
existing `Makefile` simply includes `Arduino.mk` which supports these.

- `sketch.ino` should be compatible with the installed Arduino IDE.

- `input.exp` contains an Expect script defining user input and output.
If no input is required, simply logging all data from the serial port
can be done by a single line, `expect eof`.

Additional Arduino libraries can be installed under `hw/libraries`.
This location is set up in the `Makefile`.

Finally `hw/sessions` is the directory where all temporary files live.

Todo
---

- Restrict compiler using `chroot`
- Implement cleanup of expired sessions
