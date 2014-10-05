@echo off
TITLE WorldEditArt PHAR compiler
set PHP_BINARY=bin\php\php.exe
set START_FILE=compile.php
start "" bin\mintty.exe -o Columns=88 -o Rows=32 -o AllowBlinking=0 -o FontQuality=3 -o Font="DejaVu Sans Mono" -o FontHeight=10 -o CursorType=0 -o CursorBlinks=1 -h error -t "PocketMine-MP" -i bin/pocketmine.ico -w max %PHP_BINARY% %START_FILE% --enable-ansi %*
REM bin\php\php compile.php -dphar.readonly=0 --enable-ansi
