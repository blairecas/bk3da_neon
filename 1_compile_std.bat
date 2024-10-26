@echo off

php -f ./scripts/convert_font.php
if %ERRORLEVEL% NEQ 0 ( exit /b )
php -f ./scripts/convert_menu.php
if %ERRORLEVEL% NEQ 0 ( exit /b )
php -f ./scripts/convert_tiles.php
if %ERRORLEVEL% NEQ 0 ( exit /b )

php -f ../scripts/preprocess.php acpu.mac
if %ERRORLEVEL% NEQ 0 ( exit /b )
..\scripts\macro11.exe -ysl 32 -yus -m ..\scripts\sysmac.sml -l _acpu.lst _acpu.mac
if %ERRORLEVEL% NEQ 0 ( exit /b )
php -f ../scripts/lst2bin.php _acpu.lst ./release/bk3da.sav sav
if %ERRORLEVEL% NEQ 0 ( exit /b )

..\scripts\rt11dsk.exe d neon.dsk .\release\bk3da.sav >NUL
..\scripts\rt11dsk.exe a neon.dsk .\release\bk3da.sav >NUL

del _acpu.mac
del _acpu.lst
del _menu_zx0.bin
del _loading_zx0.bin
del serial.log

echo.