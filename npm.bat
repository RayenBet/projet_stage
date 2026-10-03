@echo off
set PATH=C:\Program Files\nodejs\;C:\Users\%USERNAME%\AppData\Roaming\npm;%PATH%
cd /d "%~dp0"
npm install
pause