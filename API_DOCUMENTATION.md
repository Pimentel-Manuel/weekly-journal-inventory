# API Documentation

## Overview
This document provides comprehensive API documentation for the weekly journal inventory management system, detailing all available endpoints and their functionalities.

## Endpoints
### 1. Create Entry
- **Endpoint**: `/api/journal`
- **Method**: POST
- **Request Format**:  
  ```json
  {
      "title": "string",
      "content": "string",
      "date": "YYYY-MM-DD"
  }
  ```
- **Response Format**:  
  ```json
  {
      "id": "integer",
      "title": "string",
      "content": "string",
      "date": "YYYY-MM-DD",
      "created_at": "YYYY-MM-DD HH:MM:SS"
  }
  ```
- **Status Codes**:
  - 201 Created: Successfully created entry.
  - 400 Bad Request: Validation error.

### 2. Read Entry
- **Endpoint**: `/api/journal/{id}`
- **Method**: GET
- **Response Format**:  
  ```json
  {
      "id": "integer",
      "title": "string",
      "content": "string",
      "date": "YYYY-MM-DD",
      "created_at": "YYYY-MM-DD HH:MM:SS"
  }
  ```
- **Status Codes**:
  - 200 OK: Entry found.
  - 404 Not Found: Entry does not exist.

### 3. Update Entry
- **Endpoint**: `/api/journal/{id}`
- **Method**: PUT
- **Request Format**:  
  ```json
  {
      "title": "string",
      "content": "string",
      "date": "YYYY-MM-DD"
  }
  ```
- **Response Format**:  
  ```json
  {
      "id": "integer",
      "title": "string",
      "content": "string",
      "date": "YYYY-MM-DD",
      "updated_at": "YYYY-MM-DD HH:MM:SS"
  }
  ```
- **Status Codes**:
  - 200 OK: Successfully updated entry.
  - 400 Bad Request: Validation error.
  - 404 Not Found: Entry does not exist.

### 4. Delete Entry
- **Endpoint**: `/api/journal/{id}`
- **Method**: DELETE
- **Response Format**:  
  ```json
  {
      "message": "Entry deleted successfully."
  }
  ```
- **Status Codes**:
  - 200 OK: Successfully deleted entry.
  - 404 Not Found: Entry does not exist.

## Error Handling
Common error responses will include messages indicating issues such as:
- Validation errors
- Entry not found
- Server errors (500)

## Data Validation Rules
- Title should be a non-empty string (max 150 characters).
- Content should be a non-empty string.
- Date should match the format YYYY-MM-DD.

## Example Requests
### 1. Create Entry using cURL
```bash
curl -X POST http://yourapi.com/api/journal \
-H "Content-Type: application/json" \
-d '{"title": "My First Journal Entry", "content": "Today was a great day!", "date": "2026-01-30"}'
```

### 2. Read Entry using cURL
```bash
curl -X GET http://yourapi.com/api/journal/1
```

### 3. Update Entry using cURL
```bash
curl -X PUT http://yourapi.com/api/journal/1 \
-H "Content-Type: application/json" \
-d '{"title": "Updated Title", "content": "Updated Content", "date": "2026-01-30"}'
```

### 4. Delete Entry using cURL
```bash
curl -X DELETE http://yourapi.com/api/journal/1
```