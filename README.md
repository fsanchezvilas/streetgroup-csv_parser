# CSV Name Parser (Laravel)

Console app that parses a CSV where each row may contain one or multiple people and outputs individual people records as JSON.

Output schema per person:

- `title` (required)
- `first_name` (optional)
- `initial` (optional)
- `last_name` (required)

## Example

### Input (CSV row)

```csv
Mr John Smith & Mrs Jane Smith
```

Output (JSON)
```json
[
    {
        "title": "Mr",
        "first_name": "John",
        "initial": null,
        "last_name": "Smith"
    },
    {
        "title": "Mrs",
        "first_name": "Jane",
        "initial": null,
        "last_name": "Smith"
    }
]
```

## Requirements

- PHP 8.2+
- Composer

## Install

1. Clone the repository.
2. Install PHP dependencies:
```bash
composer install
```

## Run
Parse the provided fixture CSV and print JSON to stdout:
```bash
php artisan import:names tests/Fixtures/homeowners.csv
```

If you run the command from outside the project root, use an absolute path:

```bash
php artisan import:names "C:\path\to\project\tests\Fixtures\homeowners.csv"
```


## Testing

The project includes both Unit and Feature tests. You can run them using PHPUnit or the Artisan test runner:

```bash
php artisan test
```

## Supported input patterns (fixture-scoped)

This solution intentionally supports only the formats present in the provided example CSV:

- `Title FirstName LastName` (e.g. `Mr John Smith`)
- `Title Initial LastName` / `Title Initial. LastName` (e.g. `Mr M Mackie`, `Mr F. Fredrickson`)
- Shared last name couple: `Mr and Mrs Smith`
- Two full people in one field: `Mr Tom Staff and Mr John Doe`
- Ampersand shared name: `Dr & Mrs Joe Bloggs`
- Hyphenated last names (e.g. `Mrs Faye Hughes-Eastwood`)

## Assumptions & limitations

- The CSV is treated as a simple single-column file where each row contains a name (often with a trailing comma).
- No persistence/storage is used; the application is stateless and returns JSON output on demand.
- Parsing is fixture-scoped (it is not intended to be a fully generic name parser).

## Development approach

I followed a TDD workflow (Red → Green → Refactor), using PHPUnit tests to lock in the expected behavior for each supported input pattern before refactoring for readability.

## AI usage disclosure

I used PhpStorm (including its AI Assistant) as a chat-based productivity aid during development and refactoring. I did not use an autonomous coding agent.
This reflects my day-to-day workflow in a compliance-restricted finance environment. I validated behavior with PHPUnit tests (TDD: Red → Green → Refactor).