# Laraformer

> Laraformer is a laravel 5.* package that lets you easily introduce a transformation layer for your data.

Laraformer (originated from Laravel Transformers) is a Laravel 5.* package that lets you easily introduce the transformer logic in your Laravel applications.

# Features

- Automatic transformation of the models. Also support for manual transformation
- Lets you transform *almost* any kind of data i.e. arrays, objects, collections, paginated data etc
- Not only you can transform models but also any dataset
- Supports both Eloquent and Moloquent
- Ease of use; for automatic transformation, you just need a `transform` function in your models and for manual, there is a single function call. More to it in a moment.
- Lets you keep your transformation logic separate
- Lets you make sure of the fact that any changes in schema doesn't affect the output

# Two steps installation

All you have to do is install the package and add the service provider

- Run `composer require kamranahmedse/laraformer` in the terminal
- Add Service Provider Open `config/app.php` and add `KamranAhmed\Laraformer\TransformerServiceProvder::class` to the end of providers array:

   ```php
   'providers' => array(
       ....
       KamranAhmed\Laraformer\TransformerServiceProvder::class,
   ),
   ```

# How to use

The installation will automatically setup everything that is there to use the package. Lets get into the real stuff now, shall we?!

# Transforming Models

> Just add the transformation logic to a method called `transform` in your model and directly respond with model/collection of models/paginated model response.

You can transform your models in one of the two ways:

- Automatic transformation of the response
- Manually transform the response

Let me explain the usage with an example.

### Example
**Sample Table/Collection** Lets say that we have a `users` table/collection with the associated model called `User`. The table/collection looks like following

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

### a) Automatically transform the response

For the automatic transformation, all you have to do is return the models directly i.e. with the model object, collection of models or a paginated models in the response. For example, the controller may look like below:

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

### b) Manual transformation

If you would like to transform your model data for internal use, you can also do it. For that, you can either do it using a provided facade called `\KamranAhmed\Laraformer\Facades\Transformer` by using an alias called `Laraformer` i.e.

```php
// Use the registered alias
$user = User::find(120);
$transformedUser = Laraformer::transformModel($user);
// Do something with $transformedUser
```
Also note that you still have to specify the `transform` method in the model.

## Transforming any Dataset

Not only models, but you can also use laraformer to transform any kind of dataset whether it some data from an external source, some dataset that you magically generated etc. In order to do that, you can do one of the following.
 
 - Pass an object of a transformer class having a `transform` method
 - Pass a callback function 
 
 For example:

```php
// Transforming using callback function
return Laraformer::forceTransform($dataset, function ($item) {
    return [
        'name' => $item['name'],
        'slug' => str_slugify($item['name']),
        'occupation' => $item['profession'],
        'is_admin' => (bool) $item['is_admin'],
        'joined_on' => DateHelper::humanize($item['created_at']),
        'profile_design' => json_decode($item['design_options'], true)
    ];
})

// Transforming using Transformer class object
$userTransformer = new UserTransformer;
return Laraformer::forceTransform($dataset, $userTransformer)
```

## Contributing
- Feel free to add some new functionality, polish some existing functionality etc and open up a pull request explaining what you did.
- Report any issues in the [issues section](https://github.com/kamranahmedse/laraformer/issues)
- Also you can reach me directly at kamranahmed.se@gmail.com with any feedback




  


