#!/bin/bash

# Disable Reason API Test Script
# This script tests all API endpoints using cURL commands

BASE_URL="http://localhost/amt/api/disable-reason"
HEADERS='-H "Content-Type: application/json" -H "Client-Service: smartschool" -H "Auth-Key: schoolAdmin@"'

echo "🧪 Testing Disable Reason API Endpoints"
echo "========================================"

# Test 1: List all disable reasons
echo ""
echo "📋 Test 1: List All Disable Reasons"
echo "URL: $BASE_URL/list"
echo "Command: curl -X POST $BASE_URL/list $HEADERS -d '{}'"
echo "Response:"
curl -X POST "$BASE_URL/list" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}' \
  -w "\nHTTP Status: %{http_code}\n" \
  -s | jq '.' 2>/dev/null || echo "Response received (jq not available for formatting)"

echo ""
echo "----------------------------------------"

# Test 2: Create new disable reason
echo ""
echo "➕ Test 2: Create New Disable Reason"
echo "URL: $BASE_URL/create"
REASON="API Test Reason - $(date '+%Y-%m-%d %H:%M:%S')"
echo "Data: {\"reason\": \"$REASON\"}"
echo "Response:"
RESPONSE=$(curl -X POST "$BASE_URL/create" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d "{\"reason\": \"$REASON\"}" \
  -w "\nHTTP Status: %{http_code}\n" \
  -s)

echo "$RESPONSE"

# Extract ID from response (basic extraction)
CREATED_ID=$(echo "$RESPONSE" | grep -o '"id":[0-9]*' | grep -o '[0-9]*' | head -1)
echo "Extracted ID: $CREATED_ID"

echo ""
echo "----------------------------------------"

# Test 3: Get specific disable reason (if ID was extracted)
if [ ! -z "$CREATED_ID" ]; then
    echo ""
    echo "🔍 Test 3: Get Specific Disable Reason (ID: $CREATED_ID)"
    echo "URL: $BASE_URL/get/$CREATED_ID"
    echo "Response:"
    curl -X POST "$BASE_URL/get/$CREATED_ID" \
      -H "Content-Type: application/json" \
      -H "Client-Service: smartschool" \
      -H "Auth-Key: schoolAdmin@" \
      -d '{}' \
      -w "\nHTTP Status: %{http_code}\n" \
      -s | jq '.' 2>/dev/null || echo "Response received"

    echo ""
    echo "----------------------------------------"

    # Test 4: Update disable reason
    echo ""
    echo "✏️ Test 4: Update Disable Reason (ID: $CREATED_ID)"
    echo "URL: $BASE_URL/update/$CREATED_ID"
    UPDATED_REASON="Updated API Test Reason - $(date '+%Y-%m-%d %H:%M:%S')"
    echo "Data: {\"reason\": \"$UPDATED_REASON\"}"
    echo "Response:"
    curl -X POST "$BASE_URL/update/$CREATED_ID" \
      -H "Content-Type: application/json" \
      -H "Client-Service: smartschool" \
      -H "Auth-Key: schoolAdmin@" \
      -d "{\"reason\": \"$UPDATED_REASON\"}" \
      -w "\nHTTP Status: %{http_code}\n" \
      -s | jq '.' 2>/dev/null || echo "Response received"

    echo ""
    echo "----------------------------------------"

    # Test 5: Delete disable reason
    echo ""
    echo "🗑️ Test 5: Delete Disable Reason (ID: $CREATED_ID)"
    echo "URL: $BASE_URL/delete/$CREATED_ID"
    echo "Response:"
    curl -X POST "$BASE_URL/delete/$CREATED_ID" \
      -H "Content-Type: application/json" \
      -H "Client-Service: smartschool" \
      -H "Auth-Key: schoolAdmin@" \
      -d '{}' \
      -w "\nHTTP Status: %{http_code}\n" \
      -s | jq '.' 2>/dev/null || echo "Response received"

    echo ""
    echo "----------------------------------------"
else
    echo ""
    echo "⚠️ Tests 3-5 skipped: Could not extract created ID"
    echo "----------------------------------------"
fi

# Test 6: Invalid authentication
echo ""
echo "🔒 Test 6: Invalid Authentication"
echo "URL: $BASE_URL/list"
echo "Using invalid headers"
echo "Response:"
curl -X POST "$BASE_URL/list" \
  -H "Content-Type: application/json" \
  -H "Client-Service: invalid" \
  -H "Auth-Key: invalid" \
  -d '{}' \
  -w "\nHTTP Status: %{http_code}\n" \
  -s | jq '.' 2>/dev/null || echo "Response received"

echo ""
echo "----------------------------------------"

# Test 7: Invalid method (GET instead of POST)
echo ""
echo "❌ Test 7: Invalid Method (GET instead of POST)"
echo "URL: $BASE_URL/list"
echo "Using GET method"
echo "Response:"
curl -X GET "$BASE_URL/list" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -w "\nHTTP Status: %{http_code}\n" \
  -s | jq '.' 2>/dev/null || echo "Response received"

echo ""
echo "----------------------------------------"

# Test 8: Invalid ID format
echo ""
echo "🔢 Test 8: Invalid ID Format"
echo "URL: $BASE_URL/get/invalid"
echo "Response:"
curl -X POST "$BASE_URL/get/invalid" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{}' \
  -w "\nHTTP Status: %{http_code}\n" \
  -s | jq '.' 2>/dev/null || echo "Response received"

echo ""
echo "----------------------------------------"

# Test 9: Empty reason field
echo ""
echo "📝 Test 9: Empty Reason Field"
echo "URL: $BASE_URL/create"
echo "Data: {\"reason\": \"\"}"
echo "Response:"
curl -X POST "$BASE_URL/create" \
  -H "Content-Type: application/json" \
  -H "Client-Service: smartschool" \
  -H "Auth-Key: schoolAdmin@" \
  -d '{"reason": ""}' \
  -w "\nHTTP Status: %{http_code}\n" \
  -s | jq '.' 2>/dev/null || echo "Response received"

echo ""
echo "========================================"
echo "✅ API Testing Complete!"
echo ""
echo "Expected Results:"
echo "- Test 1 (List): HTTP 200"
echo "- Test 2 (Create): HTTP 201"
echo "- Test 3 (Get): HTTP 200"
echo "- Test 4 (Update): HTTP 200"
echo "- Test 5 (Delete): HTTP 200"
echo "- Test 6 (Invalid Auth): HTTP 401"
echo "- Test 7 (Invalid Method): HTTP 405"
echo "- Test 8 (Invalid ID): HTTP 400"
echo "- Test 9 (Empty Reason): HTTP 400"
echo ""
echo "If you see different HTTP status codes, there may be issues with the API."
