# URL Validator Library

A PHP URL validation library that provides robust URL validation with support for Unicode domain names (IDN - Internationalized Domain Names).

## Features

- ✅ Validates standard URLs (HTTP/HTTPS)
- ✅ Supports Unicode domain names (IDN)
- ✅ Handles various URL components (scheme, host, port, path, query, fragment)
- ✅ Comprehensive test coverage using PHPSpec
- ✅ PSR-4 autoloading
- ✅ Docker support for containerized deployment

## Requirements

- PHP 8.3 or higher
- `intl` extension (for Unicode domain name support)
- Composer

## Installation

### Using Composer

```bash
composer require longineers/url-validator-lib
```

### Manual Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/longineers/url-validator-lib.git
   cd url-validator-lib
   ```

2. Install dependencies:
   ```bash
   composer install
   ```

## Usage

### Basic Usage

```php
<?php

use App\UrlValidator;

$validator = new UrlValidator();

// Validate a standard URL
$isValid = $validator->isValid('https://example.com');
echo $isValid ? 'Valid' : 'Invalid'; // Output: Valid

// Validate a URL with Unicode characters
$isValid = $validator->isValid('https://example®.com');
echo $isValid ? 'Valid' : 'Invalid'; // Output: Valid

// Invalid URL example
$isValid = $validator->isValid('not-a-url');
echo $isValid ? 'Valid' : 'Invalid'; // Output: Invalid
```

### Supported URL Formats

The validator supports various URL formats including:

- `https://example.com`
- `http://subdomain.example.com`
- `https://example.com:8080`
- `https://example.com/path/to/resource`
- `https://example.com/path?query=value`
- `https://example.com/path#fragment`
- `https://user:pass@example.com`
- `https://example®.com` (Unicode domains)

## Development

### Running Tests

The project uses PHPSpec for behavior-driven development and testing:

```bash
# Run all tests
composer test

# Run tests with verbose output
vendor/bin/phpspec run --config test/phpspec.yml --format=pretty -v
```

### Project Structure

```
url-validator-lib/
├── src/
│   └── UrlValidator.php      # Main validator class
├── test/
│   ├── spec/
│   │   └── UrlValidatorSpec.php  # PHPSpec test specifications
│   └── phpspec.yml           # PHPSpec configuration
├── vendor/                   # Composer dependencies
├── Dockerfile               # Docker configuration
├── composer.json            # Composer configuration
└── README.md               # This file
```

### Docker Support

The project includes Docker support for containerized development and testing.

#### Prerequisites

- Docker installed on your system
- Basic familiarity with Docker commands

#### Step-by-Step Instructions

##### 1. Build the Docker Container

```bash
# Navigate to the project directory
cd url-validator-lib

# Build the Docker image
docker build -t url-validator-lib .
```

##### 2. Run the Container

```bash
# Run the container in interactive mode
docker run -it --name url-validator-container url-validator-lib bash
```

##### 3. Run Tests Inside the Container

Once inside the container, you can run the tests:

```bash
# Run all tests
composer test

# Run quality checks
composer quality
```

##### 4. Alternative: Run Tests in One Command

You can also run tests directly without entering the container:

```bash
# Run tests in a temporary container
docker run --rm url-validator-lib composer test

# Run quality checks
docker run --rm url-validator-lib composer quality
```

##### 5. Development Workflow

For development with live code changes:

```bash
# Mount your local code into the container
docker run -it --rm -v $(pwd):/app url-validator-lib bash

# Inside the container, install dependencies if needed
composer install

# Run tests
composer test
```

##### 6. Clean Up

```bash
# Remove the container (if you used --name)
docker rm url-validator-container

# Remove the image
docker rmi url-validator-lib
```

## How It Works

The `UrlValidator` class uses PHP's built-in `parse_url()` function combined with `filter_var()` to validate URLs. For Unicode domain names, it uses the `intl` extension to convert IDN domains to ASCII format before validation.

### Key Components

1. **URL Parsing**: Uses `parse_url()` to break down the URL into components
2. **IDN Handling**: Converts Unicode domain names to ASCII using `idn_to_ascii()`
3. **URL Reconstruction**: Rebuilds the URL with ASCII domain for validation
4. **Validation**: Uses `filter_var()` with `FILTER_VALIDATE_URL` for final validation

## Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Make your changes
4. Add tests for your changes
5. Run the full quality check suite (`composer quality`)
6. Commit your changes (`git commit -m 'Add some amazing feature'`)
7. Push to the branch (`git push origin feature/amazing-feature`)
8. Open a Pull Request

### Development Scripts

```bash
# Run tests
composer test

# Run tests in watch mode
composer test:watch

# Run static analysis
composer analyse

# Run psalm analysis
composer analyse:psalm

# Check code style
composer cs:check

# Fix code style
composer cs:fix

# Run all quality checks
composer quality

# Run CI checks
composer ci
```

## CI/CD Pipeline

The project uses GitHub Actions for continuous integration and deployment:

### CI Workflows

- **Main CI**: Runs tests on PHP 8.3 and 8.4 with different dependency versions
- **Code Quality**: Runs PHPStan, PHP CS Fixer, and Psalm static analysis
- **Security**: Checks for security vulnerabilities in dependencies
- **Docker**: Builds and tests Docker images

### Deployment

- **Automatic releases**: Creates GitHub releases on version tags
- **Docker Hub**: Publishes Docker images to Docker Hub
- **Packagist**: Updates Packagist automatically on new releases

### Branch Protection

- All commits to `main` require passing CI checks
- Pull requests must be reviewed before merging
- Automatic dependency updates via Dependabot

## Testing

The project follows behavior-driven development (BDD) practices using PHPSpec:

- Tests are located in `test/spec/`
- Run tests with `composer test`
- All new features should include corresponding specifications

## License

This project is open source and available under the [MIT License](LICENSE).

## Changelog

### Version 1.0.0
- Initial release
- Basic URL validation
- Unicode domain name support
- Docker support
- PHPSpec test suite

## Support

If you encounter any issues or have questions, please [open an issue](https://github.com/longineers/url-validator-lib/issues) on GitHub.
