# Laraformer - Easily introduce a transformation layer for your data

> Laraformer is a laravel 5.* package that lets you easily introduce a transformation layer for your data.

Laraformer (originated from Laravel Transformers) is a Laravel 5.* package that lets you easily introduce the transformer logic in your Laravel applications.

## Features

- Automatic transformation of response
- Lets you manualy transform data i.e. arrays, objects, collections, paginated data etc
- Supports both Eloquent and Moloquent
- Ease of use; for automatic transformation, you just need a `transform` function in your models and for manual, there is a single function call. More to it in a moment.
- Lets you keep your transformation logic separate
- Lets you make sure of the fact that any changes in schema doesn't affect the output

## Two steps installation

All you have to do is install the package and add the service provider

- Run `composer require kamranahmedse/laraformer` in the terminal
- Add Service Provider Open `config/app.php` and add `KamranAhmed\Laraformer\TransformerServiceProvder::class` to the end of providers array:

   ```php
   'providers' => array(
       ....
       KamranAhmed\Laraformer\TransformerServiceProvder::class,
   ),
   ```

## How to use

The installation will automatically setup everything that is there to use the package. Lets get into the real stuff now, shall we?!

## Automatic Transformation

> Just add a `transform` method in your model

If you would like your models to be automatically transformed, all you need is a `transform` method in your model. Let me explain it with an example.

**Sample Table/Cllection** Lets say that we have a `users` table/collection with the associated model called `User`. The table/collection looks like following

|Column|Type|Sample Data|
|---|---|---|
|id|`int`|120|
|name|`string`|John Doe|
|profession|`string`|Engineer|
|design_options|`string` (JSON for example)|[{"theme_name": "larology", "fields":[{"type": "integer", "name": "some-dummy-field"}]}]|
|is_admin|`bool`|true|
|created_at|`datetime`|2016-03-04|

**Required Output**  And here is the output we need

```json
[
    {
        "public_id": "x72sl1",
        "name": "John Doe",
        "slug": "john-doe",
        "occupation": "Engineer",
        "is_admin": true,
        "joined_on": "2 days ago",
        "profile_design": [
            {
                "theme_name": "larology",
                "fields": [
                    {
                        "type": "integer",
                        "name": "some-dummy-field"
                    }
                ]
            }
        ]
    }
]
```

**The Model** 
In order to generate the above ouput, all you need to do is, add a `transform` method in your model i.e.

```php
class User extends Eloquent 
{
    ...
    public function transform(User $user) {
        return [
            'public_id' => $this->amalgamate($user->id),
            'name' => $user->name,
            'slug' => str_slugify($user->name),
            'occupation' => $user->profession,
            'is_admin' => (bool) $user->is_admin,
            'joined_on' => DateHelper::humanize($user->created_at),
            'profile_design' => json_decode($user->design_options, true)
        ];
    }
}
```

And then in your controller, all you have to do is return the model data e.g.

```php
class UserController extends Controller 
{
    ...
    // Works well with model object
    public function show($id) {
        return User::find($id);
    }
    ...
    // Or you can return the collection
    public function all() {
        return User::all();
    }
    ...
    // Also paginated data is gracefully handled
    public function paginate() {
        return User::paginate(10);
    }
}
```






  


