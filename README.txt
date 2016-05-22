# Installing Meal API

To install the Meal Nutrition API, follow these steps:

 - Install Memcache server and PHP Memcache extension. You can do that with the 
   following commands:
 
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

After installing the API and update the dependencies, you have to configure the 
Database and Memcache connections. You can do that editing the `.env` 
file on root of API installation.

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

Here, you configure the memcache connection, we use the default configuration, 
but **changing the _CACHE_DRIVER_ to _memcache_**. Also, here we configure the 
Database connection. After you create the database you set the databse name here.

#### Creating tables ####

After you create your database and configure the connection on `.env` file, 
it's time to create the tables the API use. You can do that executing 
the Artisan command from Laravel (inside API root path):

```
php artisan migrate
```

That's it! The tables now were migrated to database.

### Conclusion ###

Now if you access http://your-configured-nameserver/ you should see this 
page as example:

![API](http://s32.postimg.org/cmed03dpx/Laravel_Google_Chrome_004.png)


*******************************************************************************
** CoolRecipes API Usage.
*******************************************************************************

RESTfull Server Resources List and Usage example:

*******************************************************************************
** Register New User:
*******************************************************************************
	- Description: 
		Register a new user on CoolRecipe database.

	- Verb: POST
	- URI: user/register
	- URL Example: {website_url}/user/register
	- Body Payload expected format: JSON
	- Fields:
		- Name: String, rerquired;
		- Email: String, rerquired;
		- Password: String, rerquired;
	- Payload Example:
		{
			"name" : "Example User",
			"email" : "example@user.com",
			"password" : "mypassword"
		}
	
*******************************************************************************
** User Login / API Generation:
*******************************************************************************
	- Description: 
		Authenticate and generate an API key for a registered user on CoolRecipe 
		database. Each time a user hits this resource URL CoolRecipe will 
		generate a new API KEY.

	- Verb: POST
	- URI: user/login
	- URL Example: {website_url}/user/login
	- Body Payload expected format: JSON
	- Fields:
		- Email: String, required;
		- Password: String, required;		
	- Payload Example:
		{
			"email" : "example@user.com",
			"password" : "mypassword"
		}
	- Expected Return:
	{
		"id": 2,
		"name": "Example User",
		"email": "example@user.com",
		"api_token": "b01baa0a46a27cfb8826e8bf6abfc4c2"
	}
	
*******************************************************************************
** Food Search:
*******************************************************************************
	- Description: 
		Submit a search term (food name) and get a response with USDA food ID
		and USDA Food name.

	- Verb: GET
	- URI: search/{term}?key=USER_API_KEY
	- URL Example: {website_url}/search/eggplant?key=b01baa0a46a27cfb8826e8bf6abfc4c2
	- Expected Return:
	[
		{
			"11209": "Eggplant, raw",
			"11210": "Eggplant, cooked, boiled, drained, without salt",
			"11783": "Eggplant, cooked, boiled, drained, with salt",
			"43146": "Eggplant, pickled"
		}
	]

*******************************************************************************
** Food Report:
*******************************************************************************
	- Description: 
		Submit a food USDA id to USDA API and get its complete nutrients list.

	- Verb: GET
	- URI: report/{usda_food_id}?key=USER_API_KEY
	- URL Example: {website_url}/report/11209?key=b01baa0a46a27cfb8826e8bf6abfc4c2
	- Expected Return:
	[
		{
		"name": "Eggplant, raw",
		"nutrients": {
		  "203": {
				"name": "Protein",
				"unit": "g",
				"value": "0.98"
		  },
		  "204": {
				"name": "Total lipid (fat)",
				"unit": "g",
				"value": "0.18"
		  },
	.... JSON response continues....

*******************************************************************************
** Create New Recipe:
*******************************************************************************
	- Description: 
		Submit a new recipe to be stored on CoolRecipe.

	- Verb: POST
	- URI: recipes?key=USER_API_KEY
	- URL Example: {website_url}/recipes?key=b01baa0a46a27cfb8826e8bf6abfc4c2
	- Body Payload expected format: JSON
	- Fields:
		- Name: String, required;
		- Description: String, required;
		- Ingredients: Array(KEY => VALUE), required;
			- FOOD_USDA_ID: QUANTITY(in "g" grams), required;
	- Payload Example:
	 {
		"name":"My first Recipe",
		"description":"New Description and Recipe HOW TO bake it",
		"ingredients":{
			"11209":"350",
			"11210":"450",
			"11783":"500",
			"43146":"50"
	  }
	 }
	- Expected Return:
	{
		"message": "ok",
		"recipe_id": 11
	}

*******************************************************************************
** List My Recipes:
*******************************************************************************
	- Description: 
		Return a list of all recipes for a given API KEY.

	- Verb: GET
	- URI: recipes?key=USER_API_KEY
	- URL Example: {website_url}/recipes?key=b01baa0a46a27cfb8826e8bf6abfc4c2
	- Expected Return:
	[
	  {
		"id": 11,
		"name": "My first Recipe",
		"id_user": "2",
		"description": "New Description and Recipe HOW TO bake it",
		"ingredients": [
		  {
			"id": 25,
			"id_recipe": "11",
			"food_id": "11209",
			"quantity": "350",
			"measure": "1",
			"description": "Eggplant, raw"
		  },
		  {
			"id": 26,
			"id_recipe": "11",
			"food_id": "11210",
			"quantity": "450",
			"measure": "1",
			"description": "Eggplant, cooked, boiled, drained, without salt"
		  },
		  {
			"id": 27,
			"id_recipe": "11",
			"food_id": "11783",
			"quantity": "500",
			"measure": "1",
			"description": "Eggplant, cooked, boiled, drained, with salt"
		  },
		  {
			"id": 28,
			"id_recipe": "11",
			"food_id": "43146",
			"quantity": "50",
			"measure": "1",
			"description": "Eggplant, pickled"
		  }
		]
	  }
	]

*******************************************************************************
** Get Recipe By ID:
*******************************************************************************
	- Description: 
		Return a Recipe with its nutrition facts.

	- Verb: GET
	- URI: recipes/{recipe_id}?key=USER_API_KEY
	- URL Example: {website_url}/recipes/11?key=b01baa0a46a27cfb8826e8bf6abfc4c2
	- Expected Return:
	{
	  "id": 11,
	  "name": "My first Recipe",
	  "id_user": "2",
	  "description": "New Description and Recipe HOW TO bake it",
	  "aggregates_nutrients": [
		{
		  "name": "Water",
		  "unit": "g",
		  "value": 1541.415
		},
		{
		  "name": "Energy",
		  "unit": "kcal",
		  "value": 522
		},
		{
		  "name": "Protein",
		  "unit": "g",
		  "value": 15.195
		},
		{
		  "name": "Total lipid (fat)",
		  "unit": "g",
		  "value": 3.795
		},
		{
		  "name": "Carbohydrate, by difference",
		  "unit": "g",
		  "value": 126.03
		},
		{
		  "name": "Fiber, total dietary",
		  "unit": "g",
		  "value": 46
		},
		{
		  "name": "Sugars, total",
		  "unit": "g",
		  "value": 57.51
		},
		{
		  "name": "Calcium, Ca",
		  "unit": "mg",
		  "value": 132.5
		},
		{
		  "name": "Iron, Fe",
		  "unit": "mg",
		  "value": 4.37
		},
		{
		  "name": "Magnesium, Mg",
		  "unit": "mg",
		  "value": 205.5
		},
		{
		  "name": "Phosphorus, P",
		  "unit": "mg",
		  "value": 315
		},
		{
		  "name": "Potassium, K",
		  "unit": "mg",
		  "value": 2777.5
		},
		{
		  "name": "Sodium, Na",
		  "unit": "mg",
		  "value": 2050.5
		},
		{
		  "name": "Zinc, Zn",
		  "unit": "mg",
		  "value": 2.375
		},
		{
		  "name": "Vitamin C, total ascorbic acid",
		  "unit": "mg",
		  "value": 27.75
		},
		{
		  "name": "Thiamin",
		  "unit": "mg",
		  "value": 1.02
		},
		{
		  "name": "Riboflavin",
		  "unit": "mg",
		  "value": 0.484
		},
		{
		  "name": "Niacin",
		  "unit": "mg",
		  "value": 10.573
		},
		{
		  "name": "Vitamin B-6",
		  "unit": "mg",
		  "value": 1.475
		},
		{
		  "name": "Folate, DFE",
		  "unit": "µg",
		  "value": 297
		},
		{
		  "name": "Vitamin B-12",
		  "unit": "µg",
		  "value": 0
		},
		{
		  "name": "Vitamin A, RAE",
		  "unit": "µg",
		  "value": 27.5
		},
		{
		  "name": "Vitamin A, IU",
		  "unit": "IU",
		  "value": 537.5
		},
		{
		  "name": "Vitamin E (alpha-tocopherol)",
		  "unit": "mg",
		  "value": 6.01
		},
		{
		  "name": "Vitamin D (D2 + D3)",
		  "unit": "µg",
		  "value": 0
		},
		{
		  "name": "Vitamin D",
		  "unit": "IU",
		  "value": 0
		},
		{
		  "name": "Vitamin K (phylloquinone)",
		  "unit": "µg",
		  "value": 53.9
		},
		{
		  "name": "Fatty acids, total saturated",
		  "unit": "g",
		  "value": 0.726
		},
		{
		  "name": "Fatty acids, total monounsaturated",
		  "unit": "g",
		  "value": 0.3335
		},
		{
		  "name": "Fatty acids, total polyunsaturated",
		  "unit": "g",
		  "value": 1.5625
		},
		{
		  "name": "Fatty acids, total trans",
		  "unit": "g",
		  "value": 0
		},
		{
		  "name": "Cholesterol",
		  "unit": "mg",
		  "value": 0
		},
		{
		  "name": "Caffeine",
		  "unit": "mg",
		  "value": 0
		}
	  ],
	  "ingredients": [
		{
		  "id": 25,
		  "id_recipe": "11",
		  "food_id": "11209",
		  "quantity": "350",
		  "measure": "1",
		  "description": "Eggplant, raw"
		},
		{
		  "id": 26,
		  "id_recipe": "11",
		  "food_id": "11210",
		  "quantity": "450",
		  "measure": "1",
		  "description": "Eggplant, cooked, boiled, drained, without salt"
		},
		{
		  "id": 27,
		  "id_recipe": "11",
		  "food_id": "11783",
		  "quantity": "500",
		  "measure": "1",
		  "description": "Eggplant, cooked, boiled, drained, with salt"
		},
		{
		  "id": 28,
		  "id_recipe": "11",
		  "food_id": "43146",
		  "quantity": "50",
		  "measure": "1",
		  "description": "Eggplant, pickled"
		}
	  ]
	}

*******************************************************************************
** Update Recipe:
*******************************************************************************
	- Description: 
		Update a already existing recipe with new data.

	- Verb: PUT
	- URI: recipes/{recipe_id}?key=USER_API_KEY
	- URL Example: {website_url}/recipes/11?key=b01baa0a46a27cfb8826e8bf6abfc4c2
	- Body Payload expected format: JSON
	- Fields:
		- Name: String, required;
		- Description: String, required;
		- Ingredients: Array(KEY => VALUE), required;
			- FOOD_USDA_ID: QUANTITY(in "g" grams), required;
	- Payload Example:
	 {
		"name":"My first Recipe",
		"description":"New Description and Recipe HOW TO bake it",
		"ingredients":{
			"11209":"350",
	  }
	 }
	- Expected Return:
	{
		"update": "ok"
	}

*******************************************************************************
** Delete Recipe:
*******************************************************************************
	- Description: 
		Delete a given recipe by its ID.

	- Verb: DELETE
	- URI: recipes/{recipe_id}?key=USER_API_KEY
	- URL Example: {website_url}/recipes/11?key=b01baa0a46a27cfb8826e8bf6abfc4c2
	- Expected Return:
	{
		"deleted": "OK"
	}