@echo off
start "" bin\mintty.exe -o AllowBlinking=0 -o FontQuality=3 -o Font="Consolas" -o FontHeight=10 -o CursorType=0 -h error -o CursorBlinks=1 -t "Compile" -w max bin\php\php.exe compile.php -dphar.readonly=0 --enable-ansi %*
