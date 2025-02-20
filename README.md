# Laravel Docker Development Environment

This repository provides a Docker-based development environment for Laravel applications, complete with a workspace container for Composer, Node.js, NPM, and Artisan commands. Below are instructions to set up the environment, run migrations, execute tests, and manage the Docker services.

---

## Setting Up the Development Environment

### Clone the Repository
If you haven’t already, clone this repository to your local machine:

\`\`\`bash
git clone <repository-url> laravel-docker
cd laravel-docker
\`\`\`

---

### Copy the Environment File
Copy the example `.env` file and adjust the variables as needed:

\`\`\`bash
cp .env.example .env
\`\`\`

**Hint:** Update the `UID` and `GID` in `.env` to match your local user and group IDs. Find these by running:

\`\`\`bash
id -u  # User ID
id -g  # Group ID
\`\`\`

**Example `.env` adjustment:**
\`\`\`env
UID=1000
GID=1000
\`\`\`

---

### Start Docker Compose Services
Launch the development services in detached mode:

\`\`\`bash
docker compose -f compose.dev.yaml up -d
\`\`\`

---

### Install Laravel Dependencies
Access the workspace container and install the Laravel dependencies:

\`\`\`bash
docker compose -f compose.dev.yaml exec workspace bash
composer install
npm install
npm run dev
\`\`\`

---

### Run Migrations
Run database migrations using the following command:

\`\`\`bash
docker compose -f compose.dev.yaml exec workspace php artisan migrate
\`\`\`

---

### Access the Application
Open your browser and navigate to:  
[http://localhost](http://localhost)

---

## Usage

### Accessing the Workspace Container
The workspace sidecar container includes tools necessary for Laravel development, such as Composer, Node.js, and NPM.

To access the workspace container:

\`\`\`bash
docker compose -f compose.dev.yaml exec workspace bash
\`\`\`

---

### Run Artisan Commands
You can run Artisan commands within the workspace container. Example:

\`\`\`bash
docker compose -f compose.dev.yaml exec workspace php artisan migrate
\`\`\`

---

### Rebuild Containers
Rebuild the Docker containers when necessary:

\`\`\`bash
docker compose -f compose.dev.yaml up -d --build
\`\`\`

---

### Stop Containers
Stop the Docker services:

\`\`\`bash
docker compose -f compose.dev.yaml down
\`\`\`

---

### View Logs
View real-time logs from the containers:

\`\`\`bash
docker compose -f compose.dev.yaml logs -f
\`\`\`

---

## Running Tests

### Set Up a Test Database
Ensure your `.env.testing` file is properly configured for testing. For example:

\`\`\`env
DB_CONNECTION=sqlite
DB_DATABASE=:memory:
\`\`\`

### Execute Tests
Run tests using Composer's `test` script or PHPUnit directly.

#### Using Composer:
\`\`\`bash
docker compose -f compose.dev.yaml exec workspace composer test
\`\`\`

#### Using PHPUnit:
\`\`\`bash
docker compose -f compose.dev.yaml exec workspace ./vendor/bin/phpunit
\`\`\`

---

## Examples

### Running a Specific Test File
You can specify the path to a test file to run only that test:

\`\`\`bash
docker compose -f compose.dev.yaml exec workspace ./vendor/bin/phpunit tests/Feature/ExampleTest.php
\`\`\`

---

### Running Tests with Coverage
Run tests with code coverage if configured in `phpunit.xml`:

\`\`\`bash
docker compose -f compose.dev.yaml exec workspace ./vendor/bin/phpunit --coverage-text
\`\`\`

---

## Troubleshooting

- **Command Not Found:** Ensure dependencies are installed by running `composer install`.
- **Database Issues:** Check your `.env.testing` file or ensure the database service is running.
- **Missing Containers:** Use `docker compose -f compose.dev.yaml ps` to confirm containers are running.

For further assistance, consult the documentation or raise an issue in the repository.
