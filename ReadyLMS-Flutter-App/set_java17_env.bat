@echo off
REM Sửa đường dẫn bên dưới thành nơi bạn cài Java 17
setx JAVA_HOME "C:\Program Files\Java\jdk-17.0.10" /M
setx PATH "%JAVA_HOME%\\bin;%PATH%" /M
echo JAVA_HOME đã được đặt về Java 17. Hãy mở lại terminal mới để build Flutter!
pause 