#!/bin/bash
# Test script for CRUD operations

echo "Testing CRUD Operations..."
echo ""

BASE_URL="http://127.0.0.1:8000"

# Test 1: Get list of posts
echo "1. Testing GET /posts (List all posts)"
curl -s -o /dev/null -w "Status: %{http_code}\n" "$BASE_URL/posts"
echo ""

# Test 2: Create a post via form (simulating HTML form submission)
echo "2. Testing POST /posts/create (Create post)"
curl -s -X POST "$BASE_URL/posts/create" \
  -d "title=Test Post 1&content=This is a test post" \
  -o /dev/null -w "Status: %{http_code}\n"
echo ""

# Test 3: Get posts again (should have 1 now)
echo "3. Testing GET /posts after creation"
curl -s -o /dev/null -w "Status: %{http_code}\n" "$BASE_URL/posts"
echo ""

# Test 4: Get a specific post
echo "4. Testing GET /posts/1 (View specific post)"
curl -s -o /dev/null -w "Status: %{http_code}\n" "$BASE_URL/posts/1"
echo ""

# Test 5: Update the post
echo "5. Testing POST /posts/1/edit (Update post)"
curl -s -X POST "$BASE_URL/posts/1/edit" \
  -d "title=Updated Test Post&content=This is an updated test post" \
  -o /dev/null -w "Status: %{http_code}\n"
echo ""

# Test 6: Delete the post
echo "6. Testing POST /posts/1/delete (Delete post)"
curl -s -X POST "$BASE_URL/posts/1/delete" \
  -o /dev/null -w "Status: %{http_code}\n"
echo ""

echo "CRUD tests completed!"
