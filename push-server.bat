@echo off
chcp 65001 >nul
powershell -NoProfile -ExecutionPolicy Bypass -File "%~dp0push-server.ps1" %*
if "%~1"=="" pause
