## Bagisto<a id="bagisto"></a>

[Bagisto](https://github.com/bagisto/bagisto) - это популярная eCommerce платформа на Laravel.
Bagisto имеет несколько реализаций API, нас интересует реализация [GraphQL API](https://bagisto.com/en/headless-ecommerce/).
Это решение поставляется отдельно от ядра bagisto и доступно в репозитории [bagisto/headless-ecommerce](https://github.com/bagisto/headless-ecommerce). Для реализации API используется популярная библиотека [nuwave/lighthouse](https://github.com/nuwave/lighthouse).

Процесс разработки API выглядит следующим образом:
1. Описание GraphQL API схемы
2. Регистрация GraphQL API схемы
3. Создание моделей и связей
4. Создание правил валидации
5. Создание политик для контроля доступа
6. Реализация бизнес требований


### Описание GraphQL API схемы<a id="описание-graphql-api-схемы"></a>

Библиотека [nuwave/lighthouse](https://github.com/nuwave/lighthouse) предлагает SDL First подход, при котором итоговый API должен быть описан на языке описания GraphQL, чтобы связать описание  API с логикой обработки этого API используются специальные [директивы](https://lighthouse-php.com/master/api-reference/directives.html).

В базовом виде описание API выглядит следующим образом:

```graphql

    extend type Query {
       blogPosts(title: String @like, content: String @like): [BlogPost!]!
       blogPost(id: Int! @eq): BlogPost
       blogCategories: [BlogCategory!]
    }

    extend type Mutation {
       blogPostCreate(input: BlogPostInput! @spread): BlogPost
       blogPostUpdate(input: BlogPostInput! @spread): BlogPost
       blogPostDelete(id: ID!): BlogPost
       blogCategoryCreate(input: BlogCategoryInput! @spread): BlogCategory
       blogCategoryUpdate(input: BlogCategoryInput! @spread): BlogCategory
       blogCategoryDelete(id: ID!): BlogCategory
    }

    input BlogPostInput {
       id: ID
       title: String
       content: String
       published_at: DateTime
       categories: BlogBelongsToManyCategoriesRelationInput
       tags: BlogHasManyTagsRelationInput
    }

    input BlogCategoryInput {
       id: ID
       title: String
       slug: String
       status: BlogCategoryStatus
    }

    input BlogPostTagInput {
       title: String
    }

    input BlogHasManyTagsRelationInput {
       create: [BlogPostTagInput!]
       upsert: [BlogPostTagInput!]
       update: [BlogPostTagInput!]
       delete: [ID!]
    }

    input BlogBelongsToManyCategoriesRelationInput {
       connect: [ID!]
       sync: [ID!]
       disconnect: [ID!]
    }

    type BlogPost {
       id: ID!
       title: String!
       content: String!
       created_at: DateTime!
       updated_at: DateTime
       published_at: DateTime
       author_id: Int!
       author: BlogPostAuthor!
       categories: [BlogCategory!] @belongsToMany
       tags: [BlogPostTag!] @hasMany
    }

    type BlogPostAuthor {
       id: ID!
       name: String!
       created_at: DateTime!
       updated_at: DateTime
    }

    type BlogCategory {
       id: ID!
       title: String!
       slug: String!
       status: BlogCategoryStatus!
       created_at: DateTime!
       updated_at: DateTime
    }

    type BlogPostTag {
       id: ID!
       title: String!
       post_id: ID!
       created_at: DateTime!
       updated_at: DateTime
    }

    enum BlogCategoryStatus {
       ACTIVE @enum(value: A)
       DISABLE @enum(value: D)
    }

```

Каждый из следующих шагов будет обогащать схему API своими директивами.


### Регистрация GraphQL API схемы<a id="регистрация-graphql-api-схемы"></a>

Библиотека [nuwave/lighthouse](https://github.com/nuwave/lighthouse) ожидает [единую точку](https://lighthouse-php.com/master/digging-deeper/schema-organisation.html#schema-organisation) описания всего API и дает множество инструментов для организации, расширения схемы. 
К сожалению, регистрация схемы API модуля требует изменений вне модуля, bagisto в этом случае никак не решает проблему, как это сделать можно подсмотреть [тут](https://github.com/incrize/bagisto-test-project/blob/main/schema.graphql).


### Создание моделей и связей<a id="создание-моделей-и-связей"></a>

Модели в Bagisto - это Eloquent модели Laravel.
Для организации модульности Bagisto использует библиотеку [artkonekt/concord](https://github.com/artkonekt/concord), которая дает возможность переопределить модели модуля на уровне проекта. Этот подход имеет свои ограничения и никак не адаптирован под GraphQL API Bagisto, поэтому для модуля “Блог” модели описаны стандартным для Laravel способом.
В результате получилось 3 модели: [Category](https://github.com/incrize/bagisto-blog/blob/main/src/Models/Category.php), [Post](https://github.com/incrize/bagisto-blog/blob/main/src/Models/Post.php), [PostTag](https://github.com/incrize/bagisto-blog/blob/main/src/Models/PostTag.php).
В качестве модели отвечающей за автора и модератора используется уже существующая в Bagisto модель `\Webkul\User\Models\Admin`.

Для того чтобы связать схему GraphQL API  с моделями необходимо добавить в схему следующие директивы:

- [@paginate](https://lighthouse-php.com/master/api-reference/directives.html#paginate) - директива выполняет SQL запрос с условиями на основе аргументов и возвращает список моделей с постраничной навигацией. 

```graphql
    @paginate(model:"CSCart\\Bagisto\\Blog\\Models\\Post")
```

- [@find](https://lighthouse-php.com/master/api-reference/directives.html#find) - директива выполняет SQL запрос  с условиями на основе аргументов и возвращает одну модель

```graphql
    @find(model:"CSCart\\Bagisto\\Blog\\Models\\Post")
```

- [@all](https://lighthouse-php.com/master/api-reference/directives.html#all) - директива выполняет SQL запрос  с условиями на основе аргументов и возвращает полный список моделей

```graphql
    @all(model:"CSCart\\Bagisto\\Blog\\Models\\Category")
```

- [@create](https://lighthouse-php.com/master/api-reference/directives.html#create) - директива выполняет создание модели на основе аргументов

```graphql
    @create(model: "CSCart\\Bagisto\\Blog\\Models\\Post")
```

- [@update](https://lighthouse-php.com/master/api-reference/directives.html#update) - директива выполняет обновление модели на основе аргументов

```graphql
    @update(model: "CSCart\\Bagisto\\Blog\\Models\\Post")

```

- [@delete](https://lighthouse-php.com/master/api-reference/directives.html#delete) - директива выполняет удаление модели на основе аргументов 

```graphql
    @delete(model:"CSCart\\Bagisto\\Blog\\Models\\Category")
```


### Создание правил валидации<a id="создание-правил-валидации"></a>

Библиотека nuwave/lighthouse использует механизм [валидации Laravel](https://laravel.com/docs/10.x/validation), поддерживает все доступные правила валидации и предоставляет [несколько вариантов](https://lighthouse-php.com/master/security/validation.html#validation) для организации правил валидации. 
В рамках модуля “Блог” валидации реализованы в виде [отдельных классов](https://lighthouse-php.com/master/security/validation.html#validator-classes): [BlogCategoryInputValidator](https://github.com/incrize/bagisto-blog/blob/main/src/Validators/BlogCategoryInputValidator.php), [BlogPostInputValidator](https://github.com/incrize/bagisto-blog/blob/main/src/Validators/BlogPostInputValidator.php), [BlogPostTagInputValidator](https://github.com/incrize/bagisto-blog/blob/main/src/Validators/BlogPostTagInputValidator.php).

Пример реализации валидатора для поста:

```php
    // src/Validators/BlogPostInputValidator.php
    final class BlogPostInputValidator extends Validator
    {
       public function rules(): array
       {
           $isUpdate = $this->args->has('id');

           return [
               'title'              => [$isUpdate ? 'filled' : 'required', 'string'],
               'content'            => [$isUpdate ? 'filled' : 'required', 'string'],
               'publishedAt'        => ['nullable', 'date'],
               'categories'         => [$isUpdate ? null : 'required'],
               'categories.connect' => [Rule::exists(Category::class, 'id')->where('status', CategoryStatus::ACTIVE->value)],
               'categories.sync'    => [Rule::exists(Category::class, 'id')->where('status', CategoryStatus::ACTIVE->value)],
           ];
       }
    }
```

Для того чтобы связать Input типы GraphQL API с валидаторами необходимо добавить в схему директиву [@validator](https://lighthouse-php.com/master/api-reference/directives.html#validator):

```
    @validator(class: "CSCart\\Bagisto\\Blog\\Validators\\BlogPostInputValidator")
```


### Создание политик для контроля доступа<a id="создание-политик-для-контроля-доступа"></a>

Библиотека nuwave/lighthouse для проверки прав использует [механизм политик Laravel](https://lighthouse-php.com/master/security/authorization.html#restrict-fields-through-policies).

Для каждого ресурса реализована своя политика: [CategoryPolicy](https://github.com/incrize/bagisto-blog/blob/main/src/Policies/CategoryPolicy.php), [PostPolicy](https://github.com/incrize/bagisto-blog/blob/main/src/Policies/PostPolicy.php), [PostTagPolicy](https://github.com/incrize/bagisto-blog/blob/main/src/Policies/PostTagPolicy.php).

Пример политики для поста:

```php
    // src/Policies/PostPolicy.php
    class PostPolicy
    {
       use HandlesAuthorization;

       public function create(Admin $user)
       {
           return $user->hasPermission('blog.blog-post:create') || $user->hasPermission('blog.blog-post:manage');
       }

       public function update(Admin $user, Post $post)
       {
           return ($user->is($post->author) && $user->hasPermission('blog.blog-post:update')) || $user->hasPermission('blog.blog-post:manage');
       }

       public function viewAny(?Admin $user)
       {
           return true;
       }

       public function view(?Admin $user, Post $post)
       {
           if ($post->published_at) {
               return true;
           }

           return $user && $user->is($post->author);
       }

       public function delete(Admin $user, Post $post)
       {
           return $this->update($user, $post);
       }
    }
```

Для того чтобы связать схему GraphQL API  с логикой проверки прав необходимо добавить директиву [@can](https://lighthouse-php.com/master/api-reference/directives.html#can):

```graphql
    @can(ability: "viewAny", model: "CSCart\\Bagisto\\Blog\\Models\\Post")
    @can(ability: "view", model: "CSCart\\Bagisto\\Blog\\Models\\Post")
    @can(ability: "create", model: "CSCart\\Bagisto\\Blog\\Models\\Post")
    @can(ability: "update", model: "CSCart\\Bagisto\\Blog\\Models\\Post", find: "id")
    @can(ability: "delete", model: "CSCart\\Bagisto\\Blog\\Models\\Post", find: "id")

```


### Реализация бизнес требований<a id="реализация-бизнес-требований"></a>

1. Категории постов могут быть деактивированы.
Реализовано за счет модели `Category`, каждая категория имеет статус: ACTIVE или DISABLED.

2. Неактивные категории не должны быть доступны для гостей и авторов.
Для выполнения этого требования необходимо реализовать [scope](https://laravel.com/docs/10.x/eloquent#query-scopes) для модели [Category](https://github.com/incrize/bagisto-blog/blob/91536140c4f8792c7f7c336d25d4d4bca5634715/src/Models/Category.php#L44-L54):

```php
    // src/Models/Category.php
    public function scopeReadPolicy(Builder $query): Builder
    {
       /** @var \Webkul\User\Models\Admin|null $user */
       $user = auth()->user();

       if (!$user || !$user->hasPermission('blog.blog-category:manage')) {
           $query->where('status', '=', CategoryStatus::ACTIVE);
       }

       return $query;
    }

```

Так же необходимо добавить правило валидации при создании поста в [BlogPostInputValidator](https://github.com/incrize/bagisto-blog/blob/91536140c4f8792c7f7c336d25d4d4bca5634715/src/Validators/BlogPostInputValidator.php#L24-L25), запрещающее выбор неактивной категории:

```php
    // src/Validators/BlogPostInputValidator.php
    'categories.connect' => [Rule::exists(Category::class, 'id')->where('status', CategoryStatus::ACTIVE->value)],
    'categories.sync'    => [Rule::exists(Category::class, 'id')->where('status', CategoryStatus::ACTIVE->value)],
```

Кроме этого необходимо добавить параметр `scopes` в директивы @all и @belongsToMany, которые отвечают за выборку категорий:

```graphql
    @all(model:"CSCart\\Bagisto\\Blog\\Models\\Category", scopes: ["readPolicy"])
```

```graphql
    @belongsToMany(relation: "categories", scopes: ["readPolicy"])
```


3. Посты могут быть в нескольких категориях.
Реализовано за счет модели `Post`, `Category` и промежуточной таблицы `blog_post_categories`.

4. Посты могут иметь неограниченное кол-во тегов
Реализовано за счет модели `Post` и `PostTag`.

5. Гости могут видеть все опубликованные посты, которые находятся хотя бы в одной активной категории

Для выполнения этого требования необходимо реализовать [scope](https://laravel.com/docs/10.x/eloquent#query-scopes) для модели [Post](https://github.com/incrize/bagisto-blog/blob/91536140c4f8792c7f7c336d25d4d4bca5634715/src/Models/Post.php#L93-L108):

```php
    // src/Models/Post.php
    public function scopeReadPolicy(Builder $query): Builder
    {
       $query->whereHas('categories', function (Builder $q) {
           $q->where('status', '=', CategoryStatus::ACTIVE);
       });

       return $query->whereNotNull('published_at');
    }
```

Кроме этого необходимо добавить параметр `scopes` в директивы @all и @paginate, которые отвечают за выборку постов:

```graphql
    @paginate(model:"CSCart\\Bagisto\\Blog\\Models\\Post", scopes: ["readPolicy"])
```

```graphql
    @all(model:"CSCart\\Bagisto\\Blog\\Models\\Category", scopes: ["readPolicy"])
```

6. Авторы могут видеть все свои посты

Для выполнения этого требования необходимо расширить scopeReadPolicy для модели [Post](https://github.com/incrize/bagisto-blog/blob/91536140c4f8792c7f7c336d25d4d4bca5634715/src/Models/Post.php#L93-L108):

```php
    // src/Models/Post.php
    public function scopeReadPolicy(Builder $query): Builder
    {
       $user = auth()->user();

       if ($user !== null) {
           return $query->where(function (Builder $q) use ($user) {
               return $q->whereNotNull('published_at')->orWhere('author_id', $user->getKey());
           });
       }

       $query->whereHas('categories', function (Builder $q) {
           $q->where('status', '=', CategoryStatus::ACTIVE);
       });

       return $query->whereNotNull('published_at');
    }

```

### Итоги<a id="итоги"></a>

API документация: [API docs](https://www.postman.com/telecoms-explorer-89845901/workspace/laravel-simple-blog-api/api/f6e65f19-d791-4069-8f7b-43a372ef9622/documentation/15340954-32a56e7a-6e59-4b36-aafa-debd3681e36c)

Пример API запроса на создание поста с тегами:

```graphql
mutation {
  blogPostCreate(
    input: {
      title: "How to Create GraphQL API", 
      content: "In our second blog post…",
      categories: {sync: [1]}, 
      tags: {create: [{title: "GraphQL"}, {title: "Laravel"}]}
    }
  ) {
    id
  }
}
```

Библиотека [nuwave/lighthouse](https://github.com/nuwave/lighthouse) хороша, проделана большая работа, чтобы сильно упростить разработку GraphQL API на Laravel, отдельно стоит выделить:
- Расширяемость API
- Вложенные мутации
- Стандартизация работы со связями
- Решение проблем N+1 запросов для связей
- Кэширование запросов

Проблемы, которые кажутся критичными:
1. Описание ограничений модели и соблюдение инвариантов реализовано в слое реализации API. Как и в случае с Lunar, нет возможности переиспользовать правила и ограничения вне контекста API.
2. Проверка прав мутаций ограничена верхним уровнем: [Security considerations](https://lighthouse-php.com/master/eloquent/nested-mutations.html#security-considerations). 
3. Ограниченная работа со связями. Аналогично Lunar, чтобы связать пост и категорию необходимо использовать идентификаторы категорий.
4. Сложно отслеживать изменения. Нет инструментов, чтобы по завершению мутации  поста понять изменился ли набор категорий поста, какие категории были добавлены, а какие удалены.
