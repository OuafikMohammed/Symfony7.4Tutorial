# Test script for CRUD operations - PowerShell version
# Run from: D:\Symfony7.4-Course\my_project_directory

Write-Host "Testing CRUD Operations..." -ForegroundColor Green
Write-Host ""

$baseUrl = "http://127.0.0.1:8000"

# Test 1: Get list of posts
Write-Host "1. Testing GET /posts (List all posts)" -ForegroundColor Yellow
$response = Invoke-WebRequest -Uri "$baseUrl/posts" -Method GET -SkipHttpErrorCheck
Write-Host "Status: $($response.StatusCode)" -ForegroundColor Green
Write-Host ""

# Test 2: Create a post via form
Write-Host "2. Testing POST /posts/create (Create post)" -ForegroundColor Yellow
$body = @{
    title   = "Test Post $(Get-Date -Format 'HHmmss')"
    content = "This is a test post created at $(Get-Date)"
} | ConvertTo-Json -Compress
$response = Invoke-WebRequest -Uri "$baseUrl/posts/create" -Method POST -Body $body -ContentType "application/x-www-form-urlencoded" -SkipHttpErrorCheck
Write-Host "Status: $($response.StatusCode)" -ForegroundColor Green
Write-Host ""

# Test 3: Get posts again
Write-Host "3. Testing GET /posts after creation" -ForegroundColor Yellow
$response = Invoke-WebRequest -Uri "$baseUrl/posts" -Method GET -SkipHttpErrorCheck
Write-Host "Status: $($response.StatusCode)" -ForegroundColor Green
Write-Host ""

# Test 4: Get a specific post (ID 1)
Write-Host "4. Testing GET /posts/1 (View specific post)" -ForegroundColor Yellow
$response = Invoke-WebRequest -Uri "$baseUrl/posts/1" -Method GET -SkipHttpErrorCheck
Write-Host "Status: $($response.StatusCode)" -ForegroundColor Green
Write-Host ""

# Test 5: Update the post
Write-Host "5. Testing POST /posts/1/edit (Update post)" -ForegroundColor Yellow
$body = @{
    title   = "Updated Test Post $(Get-Date -Format 'HHmmss')"
    content = "This is an updated test post at $(Get-Date)"
} | ConvertTo-Json -Compress
$response = Invoke-WebRequest -Uri "$baseUrl/posts/1/edit" -Method POST -Body $body -ContentType "application/x-www-form-urlencoded" -SkipHttpErrorCheck
Write-Host "Status: $($response.StatusCode)" -ForegroundColor Green
Write-Host ""

# Test 6: Delete the post
Write-Host "6. Testing POST /posts/1/delete (Delete post)" -ForegroundColor Yellow
$response = Invoke-WebRequest -Uri "$baseUrl/posts/1/delete" -Method POST -SkipHttpErrorCheck
Write-Host "Status: $($response.StatusCode)" -ForegroundColor Green
Write-Host ""

Write-Host "CRUD tests completed!" -ForegroundColor Green
