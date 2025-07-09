### Artisan command
`php artisan db:table`
to inspect table


`php artisan make:model -m <name_of_model> `
is to make the blueprints for the database

`php artisan make:model -m <name_of_model> -f`
is to make the blueprints for the database and make factory

if make the blueprints but dont have model 
`php artisan make:factory <name_of_model>Factory`

To make a controller empty class
`php artisan make:controller PostController`
with resource 
`php artisan make:controller PostController --resource`

with specific instance of a class in model
`php artisan make:controller PostController --resource --model=<model_name>`

`php artisan migrate`
is to run the database migration (aka save or commit)

`php artisan migrate:fresh --seed`
is to drop every table and reapply every migrations and seeding

`php artisan migrate:rollback --step=<n>`
is to undo the changes to the database that save, n means the number times to revert 

#### To make seeder
To seed the database with records
`php artisan migrate --seed` or we can run `php artisan db:seed`

To seed a specific file 
`php artisan db:seed --class=PostSeeder`

we can create a new seeder class by using
`php artisan make:seeder <class_name>`



`php artisan tinker`
interact with the database

### Tinker
```bash
\App\Models\Category::get()
```
is to view values in the category table


