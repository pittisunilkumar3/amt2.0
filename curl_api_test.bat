@echo off
REM Disable Reason API Test Script for Windows
REM This script tests all API endpoints using cURL commands

set BASE_URL=http://localhost/amt/api/disable-reason
set HEADERS=-H "Content-Type: application/json" -H "Client-Service: smartschool" -H "Auth-Key: schoolAdmin@"

echo 🧪 Testing Disable Reason API Endpoints
echo ========================================

REM Test 1: List all disable reasons
echo.
echo 📋 Test 1: List All Disable Reasons
echo URL: %BASE_URL%/list
echo Command: curl -X POST %BASE_URL%/list %HEADERS% -d "{}"
echo Response:
curl -X POST "%BASE_URL%/list" -H "Content-Type: application/json" -H "Client-Service: smartschool" -H "Auth-Key: schoolAdmin@" -d "{}" -w "HTTP Status: %%{http_code}" -s
echo.
echo ----------------------------------------

REM Test 2: Create new disable reason
echo.
echo ➕ Test 2: Create New Disable Reason
echo URL: %BASE_URL%/create
set REASON=API Test Reason - %date% %time%
echo Data: {"reason": "%REASON%"}
echo Response:
curl -X POST "%BASE_URL%/create" -H "Content-Type: application/json" -H "Client-Service: smartschool" -H "Auth-Key: schoolAdmin@" -d "{\"reason\": \"%REASON%\"}" -w "HTTP Status: %%{http_code}" -s
echo.
echo ----------------------------------------

REM Test 3: Get specific disable reason (using ID 1 as example)
echo.
echo 🔍 Test 3: Get Specific Disable Reason (ID: 1)
echo URL: %BASE_URL%/get/1
echo Response:
curl -X POST "%BASE_URL%/get/1" -H "Content-Type: application/json" -H "Client-Service: smartschool" -H "Auth-Key: schoolAdmin@" -d "{}" -w "HTTP Status: %%{http_code}" -s
echo.
echo ----------------------------------------

REM Test 4: Update disable reason (using ID 1 as example)
echo.
echo ✏️ Test 4: Update Disable Reason (ID: 1)
echo URL: %BASE_URL%/update/1
set UPDATED_REASON=Updated API Test Reason - %date% %time%
echo Data: {"reason": "%UPDATED_REASON%"}
echo Response:
curl -X POST "%BASE_URL%/update/1" -H "Content-Type: application/json" -H "Client-Service: smartschool" -H "Auth-Key: schoolAdmin@" -d "{\"reason\": \"%UPDATED_REASON%\"}" -w "HTTP Status: %%{http_code}" -s
echo.
echo ----------------------------------------

REM Test 5: Invalid authentication
echo.
echo 🔒 Test 5: Invalid Authentication
echo URL: %BASE_URL%/list
echo Using invalid headers
echo Response:
curl -X POST "%BASE_URL%/list" -H "Content-Type: application/json" -H "Client-Service: invalid" -H "Auth-Key: invalid" -d "{}" -w "HTTP Status: %%{http_code}" -s
echo.
echo ----------------------------------------

REM Test 6: Invalid method (GET instead of POST)
echo.
echo ❌ Test 6: Invalid Method (GET instead of POST)
echo URL: %BASE_URL%/list
echo Using GET method
echo Response:
curl -X GET "%BASE_URL%/list" -H "Content-Type: application/json" -H "Client-Service: smartschool" -H "Auth-Key: schoolAdmin@" -w "HTTP Status: %%{http_code}" -s
echo.
echo ----------------------------------------

REM Test 7: Invalid ID format
echo.
echo 🔢 Test 7: Invalid ID Format
echo URL: %BASE_URL%/get/invalid
echo Response:
curl -X POST "%BASE_URL%/get/invalid" -H "Content-Type: application/json" -H "Client-Service: smartschool" -H "Auth-Key: schoolAdmin@" -d "{}" -w "HTTP Status: %%{http_code}" -s
echo.
echo ----------------------------------------

REM Test 8: Empty reason field
echo.
echo 📝 Test 8: Empty Reason Field
echo URL: %BASE_URL%/create
echo Data: {"reason": ""}
echo Response:
curl -X POST "%BASE_URL%/create" -H "Content-Type: application/json" -H "Client-Service: smartschool" -H "Auth-Key: schoolAdmin@" -d "{\"reason\": \"\"}" -w "HTTP Status: %%{http_code}" -s
echo.
echo ========================================
echo ✅ API Testing Complete!
echo.
echo Expected Results:
echo - Test 1 (List): HTTP 200
echo - Test 2 (Create): HTTP 201
echo - Test 3 (Get): HTTP 200
echo - Test 4 (Update): HTTP 200
echo - Test 5 (Invalid Auth): HTTP 401
echo - Test 6 (Invalid Method): HTTP 405
echo - Test 7 (Invalid ID): HTTP 400
echo - Test 8 (Empty Reason): HTTP 400
echo.
echo If you see different HTTP status codes, there may be issues with the API.
pause
