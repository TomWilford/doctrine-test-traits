# doctrine-test-traits

[![QA & Tests](https://github.com/TomWilford/doctrine-test-traits/actions/workflows/tests.yml/badge.svg)](https://github.com/TomWilford/doctrine-test-traits/actions/workflows/tests.yml)
[![PHP Version](https://img.shields.io/badge/php-%E2%89%A5%208.2%20--%208.5-8892bf.svg)](https://php.net)
[![License](https://img.shields.io/github/license/TomWilford/doctrine-test-traits)](LICENSE)

Traits to facilitate database integration tests with ephemeral databases using `doctrine/dbal` and `doctrine/migrations`.

The aim is to provide helper methods that can create, populate and destroy database tables for use in integration tests,
leveraging doctrine libraries.

This was heavily inspired by [selective/test-traits](https://packagist.org/packages/selective/test-traits) and
[samuelgfeller/test-traits](https://packagist.org/packages/samuelgfeller/test-traits).

## Installation

```bash
composer require tomwilford/doctrine-test-traits --dev
```

This will install the library (and `doctrine/dbal` and `doctrine/migrations` if you haven't installed them yet).

## Usage

> **Warning**: this library relies on a `doctrine/migrations` class that is marked as internal (`MigratorConfiguration`).
The use of this class is confirmed as working with `3.9.7`, but the behaviour could change in a later version and
break this.


### Migrations
In a phpunit test, create an instance of the `DatabaseTestContext` class in the `setUp` method, with your doctrine
connection and the relevant config loader for your application.

If you are working with a [driver that supports in-memory instances](https://www.doctrine-project.org/projects/doctrine-dbal/en/4.4/reference/configuration.html#connection-details),
you should use prefer that option. Alternatively create a new database for your driver (e.g. `my_test_db`) and ensure
you use that name when creating the connection to avoid making changes to your dev environment database.

```php
private DatabaseTestContext $context;

protected function setUp(): void
{
    $connection = DriverManager::getConnection(['driver' => 'pdo_sqlite', 'memory' => true]);
    $migrationsConfig = new PhpFile('/path/to/migrations.php');

     $this->context = new DatabaseTestContext(
        $connection,
        $migrationsConfig,
    );
}
```

This example uses the `migrations.php` that `doctrine/migrations` expects at the root of your project, but all of these
are valid:
```php
// json
$migrationsConfig = new \Doctrine\Migrations\Configuration\Migration\JsonFile('/path/to/migrations.json');

// yaml
$migrationsConfig = new \Doctrine\Migrations\Configuration\Migration\YamlFile('/path/to/migrations.yml');

//xml
$migrationsConfig = new \Doctrine\Migrations\Configuration\Migration\XmlFile('/path/to/migrations.xml');

//raw array
$migrationsConfig = new \Doctrine\Migrations\Configuration\Migration\ConfigurationArray([
    'table_storage' => [
        // etc
    ],
    // etc
]);
```

Next, use `DatabaseTestTrait` in the test case and implement its methods in `setUp` and `tearDown`.
```php
use TomWilford\DoctrineTestTraits\Trait\DatabaseTestTrait;

protected function setUp(): void
{
    // configure DatabaseTestContext

    $this->setUpDatabase($this->context);
}

protected function tearDown(): void
{
    $this->tearDownDatabase($this->context);

    parent::tearDown();
}
```

`doctrine-test-traits` will find your migrations from your `doctrine/migrations` configuration and run them up to the
`latest` version alias before each test method is executed; then drop each table after each test method is executed.

### Fixtures
To populate your test database with data your integration tests can interact with...

Create a class that implements `DatabaseTestFixtureInterface` and populate the methods. Make sure the array keys you use
line up with the names of your table's columns.
```php
class TestTableFixture implements DatabaseTestFixtureInterface
{
    public function getTableName(): string
    {
        return 'test_table';
    }

    public function getRecordsToInsert(): array
    {
        return [
            [
                'id' => 1,
                'value' => 'Record 1',
            ],
            [
                'id' => 99,
                'value' => 'Record 99',
            ],
        ];
    }
}
```

Instantiate the concrete fixture when configuring your `DatabaseTestContext` and pass it to the context inside an
instance of `DatabaseTestFixtureDto`. `DatabaseTestFixtureDto` can accept
as many `DatabaseTestFixtureInterface` instances as you require
```php
protected function setUp(): void
{
    // configure DatabaseTestContext

     $this->context = new DatabaseTestContext(
        $connection,
        $migrationsConfig,
        new DatabaseTestFixtureDto(
            new TestTableFixture()
        )
    );
}
```
### Recommendation
To centralise the configuration of `DatabaseTestContext` and to automatically apply the database actions to
`setUp`/`tearDown` it is recommended to create your own `AppTestTrait` (or whatever you want to call it) as a wrapper
for `DatabaseTestTrait` and use that in your unit tests.

```php
trait AppTestTrait
{
    use DatabaseTestTrait;

    // store the configuration as a static instance to share between test cases
    protected static ?DatabaseTestContext $context = null;

    // automatically configure setUp, no need to implement this in your test case
    protected function setUp(): void
    {
        $this->setUpDatabase($this->createContext());
    }

    // automatically configure tearDown, no need to implement this in your test case
    protected function tearDown(): void
    {
        $this->tearDownDatabase($this->createContext());

        parent::tearDown();
    }

    protected function createContext(): DatabaseTestContext
    {
        if (!self::$context) {
            // configure your connection and migrations config here
            $connection = DriverManager::getConnection([...]);
            $config = new ConfigurationArray([...]);

            self::$context = new DatabaseTestContext(
                $connection,
                $config,
                new DatabaseTestFixtureDto(
                    new TestTableFixture()
                )
            );
        }

        return self::$context;
    }
}
```

### Performance
For performance with a large database/lots of migrations you may want to consider running `DatabaseTestTrait`'s methods
in `setUpBeforeClass` and `tearDownAfterClass`.


## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
