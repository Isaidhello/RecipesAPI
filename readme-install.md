# Installing Meal API

To install the Meal Nutrition API, follow these steps:

 - Install Memcache server and PHP Memcache extension. You can do that with the following commands:
 
```
apt-get install memcached
apt-get install php5-memcached
```
> If you are on Ubuntu 16.04, the PHP package is called 'php-memcached'

 - Clone the repository from [Github](https://github.com/CoolRecipes/RecipesAPI)
 - After clone, run the following command to Install Laravel packages

```
composer update
```

### Configuring the API ###

After installing the API and update the dependencies, you have to configure the Database and Memcache connections. You can do that editing the `.env` file on root of API installation.

The file have this structure:

```
APP_ENV=local
APP_DEBUG=true
APP_KEY=base64:S2aB7zV3a122jwqn1qD5Z0PAVW7GqHOFreKUBBeKSvE=
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=coolrecipes_api
DB_USERNAME=root
DB_PASSWORD=yoursecretpassword

CACHE_DRIVER=memcached
SESSION_DRIVER=file
QUEUE_DRIVER=sync

MEMCACHED_HOST=localhost
MEMCACHED_PORT=11211
```

Here, you configure the memcache connection, we use the default configuration, but **changing the _CACHE_DRIVER_ to _memcache_**. Also, here we confdigure the Database connection. After you create the database you set the databse name here.

#### Creating tables ####

After you create your database and configure the connection on `.env` file, it's time to create the tables the API use. You can do that executing the Artisan command from Laravel (inside API root path):

```
php artisan migrate
```

That's it! The tables now were migrated to database.

### Conclusion ###

Now if you access http://your-configured-nameserver/ you should see this page as example:

![API](http://s32.postimg.org/cmed03dpx/Laravel_Google_Chrome_004.png)
