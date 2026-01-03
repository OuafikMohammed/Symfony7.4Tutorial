# CRUD Post Management - Complete Testing Guide

## Project Overview
This is a fully functional CRUD (Create, Read, Update, Delete) application built with Symfony 7.4 for managing blog posts.

## Files Modified/Created

### 1. Routes Configuration
**File:** [config/routes.php](config/routes.php)
- Added routes for all CRUD operations:
  - `POST /posts` - List all posts
  - `POST /posts/{id}` - Show single post
  - `POST /posts/create` - Create new post (GET & POST)
  - `POST /posts/{id}/edit` - Edit post (GET & POST) ← **NEW**
  - `POST /posts/{id}/delete` - Delete post (POST)

### 2. Controller
**File:** [src/Controller/PostController.php](src/Controller/PostController.php)
- `index()` - Displays all posts
- `show($post)` - Displays single post detail
- `create()` - Handle GET (show form) and POST (save new post)
- `edit($post)` - Handle GET (show edit form) and POST (update post) ← **NEW**
- `delete($post)` - Delete a post

### 3. Entity
**File:** [src/Entity/Post.php](src/Entity/Post.php)
- Properties: `id`, `title`, `content`
- Database table: `post`

### 4. Templates
All templates extend `base.html.twig` with responsive styling:

- **[templates/base.html.twig](templates/base.html.twig)** - Base layout with CSS styling
- **[templates/post/index.html.twig](templates/post/index.html.twig)** - List all posts
- **[templates/post/show.html.twig](templates/post/show.html.twig)** - Display single post
- **[templates/post/create.html.twig](templates/post/create.html.twig)** - Create new post form
- **[templates/post/edit.html.twig](templates/post/edit.html.twig)** - Edit post form ← **NEW**

---

## Testing the CRUD Operations

### Step 1: Start the Development Server
```bash
cd "D:\Symfony7.4-Course\my_project_directory"
symfony serve
```

The server will run at: **http://127.0.0.1:8000**

### Step 2: Test READ (List all posts)
1. Navigate to: **http://127.0.0.1:8000/posts**
2. You should see:
   - A list of all posts (if any exist)
   - "Create New Post" button
   - Edit and Delete buttons for each post
   - Empty message if no posts exist

### Step 3: Test CREATE (Add new post)
1. Click "Create New Post" button
2. You'll be taken to: **http://127.0.0.1:8000/posts/create**
3. Fill in the form:
   - **Title:** "My First Post"
   - **Content:** "This is the content of my first post"
4. Click "Create Post" button
5. You should be redirected to the posts list
6. Your new post should appear in the list

**Example posts to create:**
- Title: "Learning Symfony", Content: "Symfony is a powerful PHP framework for web development."
- Title: "Web Development Tips", Content: "Always write clean and maintainable code."

### Step 4: Test READ (View single post)
1. Click on any post title in the list
2. You should see:
   - Full post title
   - Full post content
   - Post ID
   - Edit and Delete buttons
   - Back link

### Step 5: Test UPDATE (Edit post)
1. From the posts list or post detail page, click "Edit" button
2. You'll be taken to: **http://127.0.0.1:8000/posts/{id}/edit**
3. Modify the post:
   - Change title to: "Updated: My First Post"
   - Update content with more information
4. Click "Update Post" button
5. You should be redirected back to the post detail page
6. Verify the changes are saved

### Step 6: Test DELETE (Remove post)
1. From the posts list or post detail page, click "Delete" button
2. A confirmation dialog will appear asking "Are you sure?"
3. Click "OK" to confirm deletion
4. The post should be removed from the list
5. Verify it no longer appears in the posts list

---

## Complete CRUD Workflow Test

### Test Sequence:
1. **Navigate to posts list** → Should see existing posts or empty message
2. **Create 3 test posts** → Each should appear in the list immediately
3. **View each post** → Click on titles to verify content displays correctly
4. **Edit middle post** → Modify title and content, verify changes
5. **Delete first post** → Confirm deletion, verify it's removed
6. **Create another post** → Verify new post appears in list
7. **View deleted post URL** → Try to access deleted post directly (should show 404 error)

---

## Database Schema

The `post` table structure (auto-created by Doctrine):

```sql
CREATE TABLE post (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content LONGTEXT NOT NULL
);
```

---

## Key Features Implemented

✅ **Full CRUD Operations:**
- Create posts with title and content
- List all posts
- View individual post details
- Edit existing posts
- Delete posts

✅ **Enhanced User Interface:**
- Responsive design with CSS styling
- Navigation between pages
- Confirmation dialogs for destructive actions
- Proper form labels and placeholders
- Back navigation links

✅ **Security & Best Practices:**
- Form submission using POST method
- Proper routing with named routes
- Twig template inheritance
- Doctrine ORM for database operations
- Input handling via Request object

✅ **Error Handling:**
- Automatic 404 for non-existent posts (via Doctrine ParamConverter)
- Form validation (required fields)
- Confirmation before deletion

---

## Troubleshooting

### Issue: "No posts found" message even after creating posts
**Solution:** Check if the database migrations have been run:
```bash
php bin/console doctrine:migrations:migrate
```

### Issue: Forms not submitting
**Solution:** Ensure JavaScript is enabled and check browser console for errors

### Issue: Styling not applying
**Solution:** Clear browser cache (Ctrl+Shift+Delete) and restart the development server

### Issue: 404 errors on post routes
**Solution:** Make sure routes.php is loaded in config/services.yaml or composer.json routes section

---

## Advanced Testing with cURL

Test CRUD operations from terminal:

```bash
# List all posts
curl http://127.0.0.1:8000/posts

# Create a post
curl -X POST http://127.0.0.1:8000/posts/create \
  -d "title=Test Post&content=Test Content"

# View a specific post
curl http://127.0.0.1:8000/posts/1

# Edit a post
curl -X POST http://127.0.0.1:8000/posts/1/edit \
  -d "title=Updated Title&content=Updated Content"

# Delete a post
curl -X POST http://127.0.0.1:8000/posts/1/delete
```

---

## Next Steps

To further enhance this application:
1. Add form validation with Symfony validators
2. Implement user authentication
3. Add created/updated timestamps
4. Add slugs for friendly URLs
5. Implement pagination for large post lists
6. Add search and filtering functionality
7. Add tags/categories to posts
8. Implement AJAX for better UX

---

