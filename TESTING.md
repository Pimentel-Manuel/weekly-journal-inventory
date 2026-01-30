# Testing Guide for File Upload Fix

## Overview
This document describes the fixes applied to resolve the file upload and entry creation issues in the Weekly Journal Inventory system.

## Issues Fixed

### 1. Database Schema (database/journal_db.sql)
**Problem**: The database table only had `title` and `content` fields, but the form collects `week_number`, `month`, `title`, `description`, and `image`.

**Fix**: Added the following columns to the `journal_entries` table:
- `week_number` (INT) - Required field for week number (1-8)
- `month` (INT) - Required field for month number (1-2)
- `image_url` (VARCHAR 500) - Optional field for storing uploaded image path

### 2. Configuration Fix (api/config.php)
**Problem**: Syntax error on line 7 - missing `$` before `db_name` variable.

**Fix**: Corrected to `$db_name = 'db_name';`

### 3. JavaScript Implementation (public/js/scripts.js)
**Problem**: The file contained only a generic placeholder with example.com API calls. No actual form submission logic existed.

**Fix**: Implemented complete functionality:
- Form submission handler that gathers all form data
- Image upload with client-side validation (file type and size)
- Entry creation with proper error handling
- Display of entries with statistics
- Image preview functionality
- XSS protection through HTML escaping and URL sanitization

### 4. Create Entry API (api/create_entry.php)
**Problem**: Only accepted `title` and `content` fields via JSON, missing `week_number`, `month`, and `image_url`.

**Fix**: 
- Updated to accept all required fields
- Added validation for week_number (must be 1-8)
- Added validation for month (must be 1-2)
- Added validation for image_url format (must start with 'uploads/')
- Added proper error handling for database connections
- Errors logged server-side without exposing database details

### 5. Image Upload API (api/upload_image.php)
**Problem**: 
- Only supported JPG and PNG (not GIF and WebP as advertised)
- No file size validation (5MB limit stated but not enforced)
- Used insecure directory permissions (0777)
- Used user-provided filename without sanitization

**Fix**:
- Added support for all advertised image types: JPG, PNG, GIF, WebP
- Added 5MB file size validation
- Changed directory permissions to 0755 (secure)
- Generate unique filenames using uniqid()
- Determine file extension from MIME type, not user input
- Added comprehensive upload error handling

### 6. Read Entries API (api/read_entries.php)
**Problem**: Missing error handling for database connection failures.

**Fix**: Added proper connection error handling with appropriate HTTP status codes.

## Security Improvements

1. **XSS Prevention**:
   - Image URLs sanitized to only allow 'uploads/' paths
   - All user content HTML-escaped before display
   - MIME-based file extension validation

2. **Input Validation**:
   - Week number validated (1-8 range)
   - Month validated (1-2 range)
   - File type validated (whitelist approach)
   - File size enforced (5MB max)

3. **Error Handling**:
   - Database errors logged but not exposed to users
   - Proper HTTP status codes for all error conditions
   - Upload errors properly detected and reported

4. **File System Security**:
   - Directory permissions set to 0755 (not 0777)
   - Unique filenames prevent collisions and exploits
   - File extensions based on MIME type, not user input

## How to Test

### Prerequisites
1. Set up MySQL database:
   ```bash
   mysql -u root -p
   CREATE DATABASE weekly_journal_db;
   USE weekly_journal_db;
   SOURCE database/journal_db.sql;
   ```

2. Update database credentials in `api/config.php`:
   ```php
   $host = 'localhost';
   $username = 'your_db_user';
   $password = 'your_db_password';
   $db_name = 'weekly_journal_db';
   ```

3. Start PHP development server:
   ```bash
   cd public
   php -S localhost:8000
   ```

### Test Cases

#### Test 1: Create Entry Without Image
1. Open http://localhost:8000 in browser
2. Click "Add Entry" button
3. Fill in:
   - Week Number: 1
   - Month: Month 1
   - Title: "Test Entry"
   - Weekly Statement: "This is a test entry"
4. Click "Save Entry"
5. **Expected**: Success message, entry appears in grid

#### Test 2: Create Entry With JPG Image
1. Click "Add Entry"
2. Fill in all required fields
3. Choose a JPG image file (under 5MB)
4. **Expected**: Image preview appears, entry saves successfully

#### Test 3: Create Entry With PNG Image
1. Repeat Test 2 with a PNG file
2. **Expected**: Success

#### Test 4: Create Entry With GIF Image
1. Repeat Test 2 with a GIF file
2. **Expected**: Success (this would have failed before the fix)

#### Test 5: Create Entry With WebP Image
1. Repeat Test 2 with a WebP file
2. **Expected**: Success (this would have failed before the fix)

#### Test 6: File Size Validation
1. Click "Add Entry"
2. Try to upload an image larger than 5MB
3. **Expected**: Error message "File size exceeds 5MB limit."

#### Test 7: Invalid File Type
1. Click "Add Entry"
2. Try to upload a .txt or .pdf file
3. **Expected**: Error message "Invalid file type. Only JPG, PNG, GIF, and WebP are allowed."

#### Test 8: Week Number Validation
1. Click "Add Entry"
2. Try to submit with week number 0 or 9
3. **Expected**: HTML5 validation prevents submission (client-side)
4. If bypassed: Error message "Week number must be between 1 and 8."

#### Test 9: Required Fields
1. Click "Add Entry"
2. Try to submit without filling required fields
3. **Expected**: HTML5 validation prevents submission

#### Test 10: Display Entries
1. Create several entries
2. **Expected**: All entries display in grid with correct data
3. Statistics update correctly (total entries, current month, progress %)

## Verification Checklist

- [ ] Database schema includes all required fields
- [ ] Form submits successfully with all fields
- [ ] Images upload for all supported types (JPG, PNG, GIF, WebP)
- [ ] 5MB file size limit is enforced
- [ ] Week number validation works (1-8)
- [ ] Month validation works (1-2)
- [ ] Entries display correctly in the grid
- [ ] Statistics update correctly
- [ ] Image preview works before upload
- [ ] Error messages are user-friendly
- [ ] No PHP errors in server logs
- [ ] No JavaScript errors in browser console

## Database Migration (If Updating Existing Database)

If you already have a `journal_entries` table, run this migration:

```sql
ALTER TABLE journal_entries 
ADD COLUMN week_number INT NOT NULL DEFAULT 1 AFTER id,
ADD COLUMN month INT NOT NULL DEFAULT 1 AFTER week_number,
ADD COLUMN image_url VARCHAR(500) AFTER content;
```

Then update existing entries if needed:
```sql
UPDATE journal_entries SET week_number = 1, month = 1 WHERE week_number IS NULL;
```

## Known Limitations

1. **Edit and Delete**: Placeholder buttons exist but functionality is not yet implemented
2. **Notifications**: Using simple alerts; toast notifications planned for future
3. **Image Management**: No image deletion when entry is deleted
4. **Pagination**: All entries load at once; may need pagination for large datasets

## Success Criteria

The fix is successful if:
1. ✅ Users can create journal entries with all required fields
2. ✅ Images of all supported types (JPG, PNG, GIF, WebP) upload successfully
3. ✅ File size is validated (5MB limit)
4. ✅ Entries display correctly with uploaded images
5. ✅ Input validation prevents invalid data
6. ✅ No security vulnerabilities (XSS, SQL injection, file upload exploits)
