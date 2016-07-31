# FreeRADIUS Genie
An installer to setup and configure FreeRADIUS for use with Sonar.

## Getting started

This installer is designed to be run on [Ubuntu 16.04 64bit](http://www.ubuntu.com/download/server), but should work on most versions of Ubuntu. Download and install Ubuntu on the server you wish to run FreeRADIUS on. If you want to host it online, I recommend [Digital Ocean](https://m.do.co/c/84841b1bca8e).

Once Ubuntu is installed, SSH in and run the following commands to prepare installation:

1. `sudo apt-get update`
2. `sudo apt-get upgrade`
3. `sudo apt-get install php-cli php-mbstring php-mysql unzip`

If you're using an older version of Ubuntu, you may need to run `sudo apt-get install php5-cli php5-mbstring php5-mysql unzip` instead.

1. `sudo apt-get install freeradius freeradius-common freeradius-utils freeradius-mysql`

Once these commands are complete, you can download FreeRADIUS Genie by executing `wget https://github.com/WISPBill/freeradius_genie.git` and then `unzip master.zip`. Once unzipped, enter the directory by typing `cd freeradius_genie-master`.

## Completing preliminary installation


## Configuration

In order to allow the Sonar `genie` tool to setup everything else for you, you need to enter the MySQL root password you setup a minute ago in a **.env** file. Type `cp .env.example .env` and then `nano .env`. You'll see a line that says `MYSQL_PASSWORD=changeme`. Use 
the backspace key to delete `changeme` and replace it with the MySQL root password you setup. Press `CTRL+X` to exit, and save your changes. **Make sure you record this root password somewhere, as you will need it in the future!**

Once that's done, we're ready to start using genie!

## Genie

Genie is a command line tool we built to help automate the setup and configuration of your FreeRADIUS server. We're going to step through each initial setup item to get our initial configuration out of the way. Type `php genie` and you'll see something like this:

![Image of Genie](https://github.com/SonarSoftware/freeradius_genie/blob/master/images/genie.png)

This is the tool you'll use to do **all** of your configuration - no need to jump into configuration files or the MySQL database!

### First steps

Let's start by getting the database setup. Highlight the **Initial Configuration** option, press the space bar to select it, and then press enter. You'll see an option titled **Setup initial database structure** - press the space bar to select it, press enter, and your database will be configured. If you
receive an error message about credentials, double check the root password you placed into your `.env` file in the **Configuration** section.

Once that's completed, we need to setup the FreeRADIUS configuration files. Select **Perform initial FreeRADIUS configuration** by using the space bar to select it, and then pressing enter. This will configure your FreeRADIUS server to use the SQL server as a backend, and restart it.



### Configuring MySQL for remote access

We also need to configure the MySQL server to allow remote access from Sonar, so that Sonar can write and read records for the RADIUS server. Let's do that now. Navigate into the **MySQL remote access configuration** menu, and select **Enable remote access**.

![Enabling remote access](https://github.com/SonarSoftware/freeradius_genie/blob/master/images/enable_remote_access.png)

This makes the MySQL server listen for connections on all interfaces on the server, rather than just to localhost (127.0.0.1). Now we need to setup a remote user account, so that your Sonar instance can access the database. To do this, select **Add a remote access user** in the same menu.

Genie will ask you for the IP address of the remote server. If you don't know the IP of your Sonar instance, you can ping it to get the IP:

![Ping](https://github.com/SonarSoftware/freeradius_genie/blob/master/images/ping.png)

Once you add the remote access user, Genie will give you back a random username and password. Copy this down - we'll need it in a minute!

![Adding a MySQL user](https://github.com/SonarSoftware/freeradius_genie/blob/master/images/add_mysql_user.png)

If you ever need to add a new user, view the existing users, or remove a user, you can also do that in this menu.

### Linking your FreeRADIUS server to Sonar

Once this configuration is done, we need to add the RADIUS server into Sonar. Inside your Sonar instance, enter the **Network** navigation menu entry and click **RADIUS Server**.

