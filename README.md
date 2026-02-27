# Simple Dictionary

Simple Dictionary is a Laravel-based web application for managing and training vocabulary using custom dictionaries and flexible training modes.

## Features

- User dictionaries with language support
- Multiple training types (steps, time, unlimited)
- Training completion reasons and details
- Progress tracking and statistics
- RESTful API (see `/api/v1`)
- Dockerized development and production environments

## Getting Started

### Prerequisites

- Docker & Docker Compose
- Make (optional, for convenience)

### Installation

1. Clone the repository:
    ```bash
    git clone https://github.com/vasilii-kostiuc/simple-dictionary.git
    cd simple-dictionary
    ```
2. Copy and configure environment variables:
    ```bash
    cp .env.example .env
    # Edit .env as needed
    ```
3. Build and start containers:
    ```bash
    docker compose up -d --build
    ```
4. Install dependencies and run migrations:
    ```bash
    docker compose exec app composer install
    docker compose exec app php artisan migrate --seed
    ```

### Running Tests

```bash
docker compose exec app php artisan test
```

## API

API endpoints are available under `/api/v1`. See [OpenAPI/Swagger docs](http://localhost:8876/api/documentation) when running locally.

### Example Endpoints

- `POST /api/v1/trainings` — create training
- `POST /api/v1/trainings/{id}/start` — start training
- `POST /api/v1/trainings/{id}/terminate` — terminate training
- `GET /api/v1/trainings/{id}/summary` — get training summary

## Environment Variables

See `.env.example` for all available configuration options.

## License

This project is open-sourced software licensed under the [MIT license](LICENSE).
