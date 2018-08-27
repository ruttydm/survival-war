You many not remove the copyright notice at the bottom of the game to chipmunk scripts if you use any part of this script.
edit: you may remove the copright if you help at this project email:-) 

Needed:
PHP 5 or greater
MYSQL 3 or greater
Ability to set cron jobs

Open up connect.php and admin/connect.php and fill in your mysql username, password, and database name in the fields indicated(ask your host for these details if you do not have them).
run install.php and then delete it.
Upload all files
Run admin/register.php and register yourself an admin name, then delete admin/register.php and admin/reguser.php

You can log into your admin account at admin/login.php
On the regular reguser.php(the one not in the admin folder you need to change the path to the path on your server where you put this game.

You will need to set a cron on the file in the cron folder to run every hour. This will make

Thats it


Changes in 2.2
The user/admin session have been combined
New players forum
