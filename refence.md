### Artisan command
`php artisan db:table`
to inspect table


`php artisan make:model -m <name_of_model> `
is to make the blueprints for the database

`php artisan make:model -m <name_of_model> -f`
is to make the blueprints for the database and make factory

if make the blueprints but dont have model 
`php artisan make:factory <name_of_model>Factory`

`php artisan migrate`
is to run the database migration (aka save or commit)

`php artisan migrate:rollback --step=<n>`
is to undo the changes to the database that save, n means the number times to revert 

To generate seed data to database we can run 
`php artisan migrate --seed` or we can run `php artisan db:seed`

`php artisan tinker`
interact with the database

### Tinker
```bash
\App\Models\Category::get()
```
is to view values in the category table


