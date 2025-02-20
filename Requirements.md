# Laravel Backend Developer Assessment Test

## Technology Stack

- Laravel 10.x
- MySQL
- PHP 8.1+

## Project Overview

Create a RESTful API for a simple E-commerce Order Management System. The system should handle products, and basic authentication.

## Requirements

### 1. Authentication

- Implement JWT-based authentication
- Create endpoints for:
  - User registration
  - User login
  - Password reset request
  - User logout

### 2. Product Management

- CRUD operations for products with the following fields:
  - Name
  - Description
  - Price
  - Stock quantity
  - Category
- Implement product search with filters
- Add pagination for product listing

## Technical Requirements

### 1. Database

- Use migrations for database schema
- Create seeders for test data
- Implement proper relationships between models
- Use database transactions where necessary

### 2. Code Organization

- Implement Repository Pattern
- Use Service Layer for business logic
- Proper exception handling
- Input validation using Form Requests

### 3. Testing

- Write PHPUnit tests

## Submission Guidelines

- Create a GitHub repository with your solution
- Postman collection for API testing

## Bonus Points

- Docker setup
- Cache implementation
- Rate limiting

## Time Management Tips

- Start with database design and migrations (30 mins)
- Implement authentication (45 mins)
- Create basic CRUD operations (1 hour)
- Implement business logic (1 hour)
- Write tests (45 mins)
- Documentation and final touches (30 mins)
