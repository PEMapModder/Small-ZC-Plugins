ConsolePush
===

Usage: `normal-console-cmd args here\[switch]`

Possible values for `[switch]`:
* `t`: trim the whtiespace before the last `\`
* `c`: cancel the event, which is equivalent to deleting/backspace-ing this line
* `p`: do not dispatch the command line. Print the typed line onto the next line as if you didn't type the commands before.
* `n`: do nothing, simply ignore the `\[switch]`. Useful for retaining the whitespace at the end of a command line.
