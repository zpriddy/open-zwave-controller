# Introduction #
After spending some time figuring out how to get the open-zwave-controller Server, Control Panel, and Web Client up and running, I decided to document what I did in case it was helpful to others and as a starting point for future instructions.


# Details #
## Prerequisites ##
Configuration of these elements is beyond the scope of this document. The versions I used are listed in parenthesis.
  1. Ubuntu 11.04 Natty (64-bit)
  1. PHP5 (Version 5.3.5)
  1. Apache2 (Version 2.2.17)
  1. MySQL (Version 14.14)
  1. SVN (Version 1.6.12)


## Server Installation and Setup ##
Download open-zwave from the Google Code repository
```
svn checkout http://open-zwave.googlecode.com/svn/trunk/ open-zwave
```
Download open-zwave-controller from the Google Code repository
```
svn checkout http://open-zwave-controller.googlecode.com/svn/trunk/ open-zwave-controller-read-only
```
Copy all of the files from the open-zwave-controller server directory **except for the Main.cpp file** to open-zwave/cpp/src/. _(Hint: They all have the word 'Socket' in them.)_
```
cp (base_dir)/open-zwave-controller-read-only/server/*Socket* (base_dir)/open-zwave/cpp/src/
```
Copy the Main.cpp file to open-zwave/cpp/examples/linux/MinOZW/
```
cp (base_dir)/open-zwave-controller-read-only/server/Main.cpp (base_dir)/open-zwave/cpp/examples/linux/MinOZW/
```
Install libudev-dev
```
sudo apt-get install libudev-dev
```
Compile open-zwave with the open-zwave-controller code
```
cd (base_dir)/open-zwave/cpp/examples/linux/MinOZW
make
```
Rename the open-zwave server (_Optional_)
```
mv test zwave_server
```
Pair your device(s) with the Z-Wave stick, plug in the Z-Wave stick, and start the open-zwave-controller server
```
cd (base_dir)/open-zwave/cpp/examples/linux/MinOZW
./(zwave_server_name)
```


## Control Panel Setup ##
Configure and enable an Apache site for the Control Panel. If you wish to use the Scheduler feature, then the site must be configured so that it is accessible via http://(server name)/zwave/ because the Scheduler feature assumes that the server.php file is accessible via http://(server name)/zwave/server.php .

Create the MySQL open-zwave-controller database
```
mysql -u (root_username) -p create (OZC_database_name)
```
I recommend creating a separate user for the OZC database instead of using the MySQL root user. I have not had time to verify all of the MySQL permissions required, so I just granted all permissions to the OZC user for the OZC database. The following command will create a user with a username of whatever you specify in place of (zwave\_username) and grant the user all privileges on the OZC database.
```
mysql -u (root_username) -p -e "GRANT ALL on (OZC_database_name).* to '(OZC_username)'@'localhost' WITH GRANT OPTION; FLUSH PRIVILEGES;" (OZC_database_name)
```
Import the SQL database located at (base\_dir)/open-zwave-read-only/controlpanel/zwave\_2012\_06\_21.sql.gz
```
gunzip < zwave_2012_06_21.sql.gz | mysql -u (zwave_username) -p (zwave_database_name)
```
Right now you have a MySQL database with a blank admin user table. You must insert a row in the admin user table so that you have valid login credentials. When you create an admin user, you must make sure to insert the SHA-1 hash of the password in the password field because that is what the PHP code will verify against. Here is how you insert a user named "ozc\_admin" with a password of "password". _If you want to add multiple admin users, you must increment the 'idtbladmin' field and the usernames must be unique._
```
mysql -u (ozc_username) -p -e "INSERT INTO (ozc_database_name).tbl_admin (idtbladmin, username, password) VALUES ('1', 'ozc_admin', SHA1('password'));" (zwave_database_name)
```
Edit the open-zwave-read-only/controlpanel/config.php file and modify the following variables if necessary:
  1. ZWAVE\_HOST
  1. ZWAVE\_PORT
  1. MYSQL\_USER
  1. MYSQL\_PASS
  1. MYSQL\_HOST
  1. MYSQL\_PORT
Edit the open-zwave-read-only/controlpanel/server.php file and modify the following variables if necessary:
  1. ZWAVE\_HOST
  1. ZWAVE\_PORT
If you wish to use the Scheduler option then you need to make additional modifications to the server.php file. You need to modify the parameters passed to Ssh2Connect() to reflect your own host, port, username and password. The Scheduler will attempt to make an SSH connection to the host specified here and login with the specified username and password in order to edit the user's crontab.
> _`*``*``*`CAUTION`*``*``*`_ I have not verified it yet, but it appears that the Scheduler could wipe out your exisiting crontab for this user before installing its own jobs! Be careful! I suggest moving your current crontab to either another user, to the cron.hourly, cron.daily, etc. directories, or to the global crontab (/etc/crontab).

_I'm not sure if the next step is necessary, but since I did it I cannot verify that the setup works without it._

Compile libmicrohttpd from source. You will encounter a compiler error when compiling openzwave-control-panel if you install the libmicrohttpd package provided by Ubuntu. _Original instruction source:_ [Piethein Strengholt's Blog >> Howto compile open-zwave and openzwave-control-panel on Ubuntu](http://www.strengholt-online.nl/howto-compile-open-zwave-and-openzwave-control-panel-on-ubuntu/)
```
wget ftp://ftp.gnu.org/gnu/libmicrohttpd/libmicrohttpd-0.9.19.tar.gz
tar zxf libmicrohttpd-0.9.19.tar.gz
cd libmicrohttpd-0.9.19
./configure
make
sudo make install
```
If you wish to use the Scheduler option of the Control Panel you must install PHP’s SSH2 extension.
```
apt-get install libssh2-php
```


## Web Client Setup ##
Configure and enable an Apache site for the Web Client.


## Final Step ##
> _`*``*``*`Restart the Apache2 service in order to enable the new Apache sites.`*``*``*`_
```
service apache2 restart
```



---

_The following text should be on a different page._

## Web Client and Control Panel Usage ##
  1. Open the Control Panel site and login using the username and password (plain text, not the SHA-1 version).
  1. Add at least one level to your setup.
  1. I had to log out and log back in to use the level when setting up a room.
  1. Add at least one room to your setup.
  1. I had to log out and log back in to use the room when setting up a device.
  1. Configure the devices with the proper levels and rooms.


## Using the Scheduler Feature ##
TBD