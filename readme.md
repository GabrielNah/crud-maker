# Laravel CRUD Generator

A simple, customizable CRUD generator for Laravel projects.

## Features

- Generates basic CRUD structure with a single command
- Creates Model, Migration, Controller, Service class, and Request classes
- Supports multiple response types: View, JSON, or Inertia
- Streamlines the process of setting up new CRUD operations in Laravel

## Installation

You can install the package via composer:

```bash
composer require kaysr/crud-maker
```

## Usage

To generate a new CRUD structure, use the following command:

```bash
php artisan make:crud {ModelName}
```

The command will prompt you to choose the type of response you want to return (View, JSON, or Inertia).

## Generated Files

For each CRUD operation, this package generates the following files:

1. Model
2. Migration
3. Controller
4. Service class
5. Two Request classes:
    - One for the store method
    - One for the update method

## Customization

The generated files provide a basic structure and need to be filled with your specific data and logic. This allows for maximum flexibility while still saving time on initial setup.

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## License

MIT

## About

This package was created for personal use to streamline CRUD creation in Laravel projects. So I hope that others might find it useful.

