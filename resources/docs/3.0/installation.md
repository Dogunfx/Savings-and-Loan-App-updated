# Installation
- [Installation](#installation)
    - [Server Requirements](#server-requirements)
    - [Creating Database](#creating-database)
    - [Web Installer](#web-installer)
    - [Direct Install(Artisan Command)](#direct-install)
    - [Email Configuration](#email-configuration)
    - [Cron Configuration](#cron-configuration)
    - [Queue Configuration](#queue-configuration)
    - [Troubleshooting](#troubleshooting)

<a name="installation"></a>
## Installation

<a name="server-requirements"></a>
### Server Requirements
Ultimate Loan Manager is built with [Laravel Framework](https://laravel.com/)
- PHP >= 7.2.5
- BCMath PHP Extension
- Ctype PHP Extension
- Fileinfo PHP extension
- JSON PHP Extension
- Mbstring PHP Extension
- OpenSSL PHP Extension
- PDO PHP Extension
- Tokenizer PHP Extension
- XML PHP Extension

<a name="creating-database"></a>
### Creating Database
You’ll need to create a new database along with a user to access it. Most hosting companies provide an interface to handle this or you can run the SQL statements below.
- Give your database a name e.g ultimateloanmanager
- Create a database user and set up a password
- Add the user to the database and give the user All privileges to the database
```mysql
CREATE DATABASE ultimateloanmanager;
CREATE USER 'ultimateloanmanager' IDENTIFIED BY 'ultimateloanmanager@@';
GRANT ALL PRIVILEGES ON ultimateloanmanager.* to ultimateloanmanager@'%' identified by 'ultimateloanmanager@@';
FLUSH PRIVILEGES;
```
<a name="web-installer"></a>
### Web Installer
Download the files from Envato. These files contain all the required dependencies including vendor folder.
In the steps below we will be using a sample subdomain e.g https://ulm.webstudio.co.zw and assume that you are using cpanel.
- Create a folder inside public_html or www e.g ulm and upload your downloaded files into the folder /public_html/ulm (Make sure it includes hidden files e.g .env)
- Go to sub domains on your cpanel dashboard and create a sub domain
- Enter your preferred sub domain e.g ulm.webstudio.co.zw
- Enter /public_html/ulm/public as your sub domain Document Root.
- Save it and access https://ulm.webstudio.co.zw
- The installer should automatically start
The installer will take you through the following steps:
1. Requirements -Checks the server requirements above
2. File Permissions -Directories within the storage, public and the bootstrap/cache directories should be writable by your web server or the app will not run.
   Here is a sample of how you can set the permissions in ubuntu server.
<a name="direct-install"></a>
### Direct Install(Artisan Command)
You can use this option if you have ssh access to run artisan commands.
```
sudo chown -R ubuntu:www-data /path/to/ultimateloanmanager
cd /path/to/ultimateloanmanager
sudo find -type f -exec chmod 664 {} \;
sudo find -type d -exec chmod 775 {} \;
sudo chgrp -R www-data bootstrap/cache storage
sudo chmod -R ug+rwx bootstrap/cache storage
```
- Edit .env and update your database credentials
- run command `php artisan app:install` to start the installation
- After successful installation you can now access your application https://ulm.webstudio.co.zw
- Default login details, email:admin@webstudio.co.zw password:admin123
Alternatively you can import the sql file into your database then create a file called installed with no extension in storage folder
<a name="email-configuration"></a>

### Email Configuration
Ultimate Loan Manager supports SMTP, Mailgun, Postmark, SparkPost, Amazon SES, and sendmail.
Edit .env and put your mail configurations there.

<a name="cron-configuration"></a>
### Cron Configuration
You only need to add the following Cron entry to your server.
`* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1`
This Cron will call the Ultimate Loan Manager command scheduler every minute. When the schedule:run command is executed, Laravel will evaluate your scheduled tasks and runs the tasks that are due.
<a name="queue-configuration"></a>

### Queue Configuration
Queues allow you to defer the processing of a time consuming task, such as sending an email, until a later time. Deferring these time consuming tasks drastically speeds up web requests to your application.
We have used queues for a number of tasks, however its easier to set queues on a VPS rather than shared hosting. Here is how to set queues on a vps:

#### Installing Supervisor
Supervisor is a process monitor for the Linux operating system, and will automatically restart your queue:work process if it fails. To install Supervisor on Ubuntu, you may use the following command:
`sudo apt-get install supervisor`

#### Configuring Supervisor
Supervisor configuration files are typically stored in the /etc/supervisor/conf.d directory. Within this directory, you may create any number of configuration files that instruct supervisor how your processes should be monitored. For example, let's create a ultimateloanmanager-worker.conf file that starts and monitors a queue:work process:
```
[program:ultimateloanmanager-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/ultimateloanmanager/artisan queue:work --queue=default,high,normal,low --tries=3
autostart=true
autorestart=true
user=ubuntu
numprocs=1
redirect_stderr=true
stdout_logfile=/path/to/ultimateloanmanager/worker.log
```
#### Starting Supervisor
Once the configuration file has been created, you may update the Supervisor configuration and start the processes using the following commands:
```
sudo supervisorctl reread

sudo supervisorctl update

sudo supervisorctl start ultimateloanmanager-worker:*
```
#### Configuring Queue On Shared Hosting
The recommended way to run job workers with Laravel is to use Supervisor but on shared hosting you do not have access to it so we recommend the method below via cronjobs:
`* * * * * cd /path-to-your-project && php artisan queue:work --tries=3 --delay=60 --timeout=90 --stop-when-empty >> /dev/null 2>&1`
The important part of the cronjob command is --stop-when-empty. It tells the master queue worker to quit when the queue is empty. This ensures you do not have long running processes which may require service restart (like we do for the regular queue processes). After all you are running these queue workers with a cronjob and you need the process to end. Otherwise you will spawn new master process every minute. With --stop-when-empty the workers will quit when all jobs are processed and the queue is empty.
### Storage directory symlink
To make them accessible from the web, you should create a symbolic link from public/storage to storage/app/public.
To create the symbolic link, you may use the storage:link Artisan command:
`php artisan storage:link`
If you don't have access to run that command then you can create a one time cron entry which you will delete as soon as the symlink has been created.
`* * * * * cd /path-to-your-project && php artisan storage:link >/dev/null 2>&1`
Or manually create the symlink like this. (Change the path to the public directory.)
`* * * * * cd /path-to-your-project-public-folder && ln -s ../storage/app/public storage >/dev/null 2>&1`
Make sure the cronjob runs every minute so you don't have to wait for exact time and then delate it once the symlink is created.
<a name="troubleshooting"></a>

### Troubleshooting

- Getting 403 Forbidden error or 404 not found when i access https://ulm.webstudio.co.zw?
  Ensure your sub domain or main domain ROOT Document points to /path/to/ultimateloanmanager/public folder and not /path/to/ultimateloanmanager folder. 
  Note that this may be caused by server misconfigurations, you can try:
  #### Apache
  Ultimate Loan Manager includes a public/.htaccess file that is used to provide URLs without the index.php front controller in the path. Before serving Ultimate Loan Manager with Apache, be sure to enable the mod_rewrite module so the .htaccess file will be honored by the server.
  If the .htaccess file that ships with Ultimate Loan Manager does not work with your Apache installation, try this alternative:
  
```shell script
Options +FollowSymLinks -Indexes
RewriteEngine On
RewriteCond %{HTTP:Authorization} .
RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^ index.php [L]
```
  #### Nginx
  If you are using Nginx, the following directive in your site configuration will direct all requests to the index.php front controller:
  ```shell script
    location / {
          try_files $uri $uri/ /index.php?$query_string;
    }
```
- I cannot see a folder named install - The url /install is a laravel route and not a folder. You will be redirected to /installer if the application detects that the app needs to be installed.
- To resolve `file_put_contents(...): failed to open stream: Permission denied` run `chmod -R 777 storage` then `chmod -R 755 storage` or used a web based interface to update storage folder permissions.
- My images are not loading - May be caused by wrong symlink. Try to setup storage directory symlink as specified above.
- Not receiving emails? Have you set your email settings in .env? Also did you setup the queue worker as specified above?
- Permissions Problem on Installation (shown as non-writable even if they are writable)? 
  This issue can cause mostly on VPS servers where the server administrator didn’t configured the folder permissions like its supposed to. While using some popular hosting provider you wont encounter with this problem.
  The numeric permissions are fine for the folders, but the problem is that they don’t have the right user and group.