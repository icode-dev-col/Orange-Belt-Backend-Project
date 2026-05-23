# Level 3: Answer Key

## Goal 1: Add a difficulty field (Easy / Medium / Hard)

### Step 1: Update the database

Run this in phpMyAdmin SQL tab to add the column:

```sql
ALTER TABLE quests ADD COLUMN difficulty VARCHAR(10) DEFAULT 'Medium';
```

Or update your `sql/setup.sql` to include it:

```sql
CREATE TABLE quests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    difficulty VARCHAR(10) DEFAULT 'Medium',
    is_complete TINYINT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### Step 2: Update the form in `pages/post_quest.php`

Add a dropdown select for difficulty:

```html
<form method="post" action="">
    <input type="text" name="title" placeholder="Quest Title" required>
    
    <select name="difficulty">
        <option value="Easy">Easy</option>
        <option value="Medium" selected>Medium</option>
        <option value="Hard">Hard</option>
    </select>
    
    <textarea name="description" placeholder="Description"></textarea>
    <button type="submit">Post Quest</button>
</form>
```

### Step 3: Save the difficulty to database

Update the INSERT in `pages/post_quest.php`:

```php
<?php
include '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $difficulty = $_POST['difficulty'];
    
    if (empty($title)) {
        $error = "Title is required!";
    } else {
        // Insert with difficulty column
        $stmt = $pdo->prepare("
            INSERT INTO quests (title, description, difficulty) 
            VALUES (?, ?, ?)
        ");
        $stmt->execute([$title, $description, $difficulty]);
        
        header('Location: ../index.php?posted=1');
        exit;
    }
}
?>
```

### Step 4: Display difficulty badge in `index.php`

Add styling and display the difficulty:

```php
<?php foreach($quests as $quest): ?>
    <div class="quest-card">
        <h3><?= htmlspecialchars($quest['title']) ?></h3>
        
        <!-- Difficulty badge -->
        <span class="difficulty-badge difficulty-<?= strtolower($quest['difficulty']) ?>">
            <?= htmlspecialchars($quest['difficulty']) ?>
        </span>
        
        <p><?= htmlspecialchars($quest['description']) ?></p>
        <button>Mark Complete</button>
        <button>Delete</button>
    </div>
<?php endforeach; ?>
```

### Step 5: Add CSS styling in `style.css`

```css
.difficulty-badge {
    padding: 5px 10px;
    border-radius: 5px;
    font-weight: bold;
    font-size: 0.8em;
    margin-right: 10px;
}

.difficulty-easy {
    background-color: #90EE90;
    color: #006400;
}

.difficulty-medium {
    background-color: #FFD700;
    color: #FF8C00;
}

.difficulty-hard {
    background-color: #FF6347;
    color: white;
}
```

---

## Goal 2: Add a reward field

### Step 1: Update the database

```sql
ALTER TABLE quests ADD COLUMN reward VARCHAR(255);
```

Or in `setup.sql`:

```sql
CREATE TABLE quests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    difficulty VARCHAR(10) DEFAULT 'Medium',
    reward VARCHAR(255),
    is_complete TINYINT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### Step 2: Add reward field to form

```html
<form method="post" action="">
    <input type="text" name="title" placeholder="Quest Title" required>
    <select name="difficulty">
        <option value="Easy">Easy</option>
        <option value="Medium" selected>Medium</option>
        <option value="Hard">Hard</option>
    </select>
    <input type="text" name="reward" placeholder="Reward (e.g., 100 gold coins)">
    <textarea name="description" placeholder="Description"></textarea>
    <button type="submit">Post Quest</button>
</form>
```

### Step 3: Update the INSERT statement

```php
$stmt = $pdo->prepare("
    INSERT INTO quests (title, description, difficulty, reward) 
    VALUES (?, ?, ?, ?)
");
$stmt->execute([$title, $description, $difficulty, $reward]);
```

### Step 4: Display reward on the board

```php
<?php foreach($quests as $quest): ?>
    <div class="quest-card">
        <h3><?= htmlspecialchars($quest['title']) ?></h3>
        
        <span class="difficulty-badge difficulty-<?= strtolower($quest['difficulty']) ?>">
            <?= htmlspecialchars($quest['difficulty']) ?>
        </span>
        
        <?php if (!empty($quest['reward'])): ?>
            <p><strong>Reward:</strong> 💰 <?= htmlspecialchars($quest['reward']) ?></p>
        <?php endif; ?>
        
        <p><?= htmlspecialchars($quest['description']) ?></p>
        <button>Mark Complete</button>
        <button>Delete</button>
    </div>
<?php endforeach; ?>
```

---

## Goal 3: Add a search bar

### Step 1: Add a search form in `index.php` (before the quests)

```html
<form method="get" action="">
    <input type="text" name="search" placeholder="Search quests...">
    <button type="submit">Search</button>
</form>
```

### Step 2: Modify the SELECT query to filter by search

```php
<?php
include 'includes/db.php';

// Get search term from GET parameter
$search = $_GET['search'] ?? '';

// Build the query with LIKE
if (!empty($search)) {
    $stmt = $pdo->prepare("
        SELECT * FROM quests 
        WHERE is_complete = 0 
        AND (title LIKE ? OR description LIKE ?)
        ORDER BY id DESC
    ");
    // Add % wildcards for partial matching
    $search_term = '%' . $search . '%';
    $stmt->execute([$search_term, $search_term]);
} else {
    $stmt = $pdo->prepare("
        SELECT * FROM quests 
        WHERE is_complete = 0 
        ORDER BY id DESC
    ");
    $stmt->execute();
}

$quests = $stmt->fetchAll();
?>
```

### Step 3: Show what was searched (optional)

```php
<?php if (!empty($search)): ?>
    <p>Results for: <strong><?= htmlspecialchars($search) ?></strong></p>
<?php endif; ?>
```

---

## Goal 4: Add timestamps (Posted X days ago)

### Step 1: Database already has `created_at`

If you added it in the setup (TIMESTAMP DEFAULT CURRENT_TIMESTAMP), it's already storing the time!

If not, add it:

```sql
ALTER TABLE quests ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP;
```

### Step 2: Create a helper function for time ago

Add this to `includes/db.php` or a new file `includes/helpers.php`:

```php
<?php
function timeAgo($timestamp) {
    $time = strtotime($timestamp);
    $diff = time() - $time;
    
    if ($diff < 60) {
        return "Just now";
    } elseif ($diff < 3600) {
        $mins = round($diff / 60);
        return "$mins minute" . ($mins != 1 ? "s" : "") . " ago";
    } elseif ($diff < 86400) {
        $hours = round($diff / 3600);
        return "$hours hour" . ($hours != 1 ? "s" : "") . " ago";
    } elseif ($diff < 604800) {
        $days = round($diff / 86400);
        return "$days day" . ($days != 1 ? "s" : "") . " ago";
    } else {
        return date('M d, Y', $time);
    }
}
?>
```

### Step 3: Include helpers and display in `index.php`

```php
<?php
include 'includes/db.php';
include 'includes/helpers.php';  // Add this line

// ... rest of code ...
?>

<?php foreach($quests as $quest): ?>
    <div class="quest-card">
        <h3><?= htmlspecialchars($quest['title']) ?></h3>
        
        <small class="posted-time">Posted <?= timeAgo($quest['created_at']) ?></small>
        
        <span class="difficulty-badge difficulty-<?= strtolower($quest['difficulty']) ?>">
            <?= htmlspecialchars($quest['difficulty']) ?>
        </span>
        
        <p><?= htmlspecialchars($quest['description']) ?></p>
        <button>Mark Complete</button>
        <button>Delete</button>
    </div>
<?php endforeach; ?>
```

---

## Goal 5: Prevent empty submissions (server-side validation)

### Already done partially, but here's the complete version:

In `pages/post_quest.php`:

```php
<?php
include '../includes/db.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';
    $difficulty = $_POST['difficulty'] ?? 'Medium';
    $reward = $_POST['reward'] ?? '';
    
    // Validate
    if (empty(trim($title))) {
        $errors[] = "Title is required!";
    }
    
    if (empty(trim($description))) {
        $errors[] = "Description is required!";
    }
    
    if (strlen($title) > 255) {
        $errors[] = "Title is too long (max 255 characters)";
    }
    
    // If no errors, save to database
    if (empty($errors)) {
        $stmt = $pdo->prepare("
            INSERT INTO quests (title, description, difficulty, reward) 
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$title, $description, $difficulty, $reward]);
        
        header('Location: ../index.php?posted=1');
        exit;
    }
}
?>

<html>
<head>
    <title>Post a Quest</title>
    <link rel="stylesheet" href="../style.css">
</head>
<body>

<!-- Show errors if any -->
<?php if (!empty($errors)): ?>
    <div class="error-box">
        <h3>⚠️ Please fix these errors:</h3>
        <ul>
            <?php foreach($errors as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form method="post" action="">
    <input type="text" name="title" placeholder="Quest Title" 
           value="<?= htmlspecialchars($_POST['title'] ?? '') ?>">
    
    <select name="difficulty">
        <option value="Easy" <?= ($_POST['difficulty'] ?? '') === 'Easy' ? 'selected' : '' ?>>Easy</option>
        <option value="Medium" <?= ($_POST['difficulty'] ?? 'Medium') === 'Medium' ? 'selected' : '' ?>>Medium</option>
        <option value="Hard" <?= ($_POST['difficulty'] ?? '') === 'Hard' ? 'selected' : '' ?>>Hard</option>
    </select>
    
    <input type="text" name="reward" placeholder="Reward" 
           value="<?= htmlspecialchars($_POST['reward'] ?? '') ?>">
    
    <textarea name="description" placeholder="Description"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
    
    <button type="submit">Post Quest</button>
</form>

</body>
</html>
```

### Add CSS for error styling in `style.css`:

```css
.error-box {
    background-color: #FFE4E1;
    border: 2px solid #FF6347;
    padding: 15px;
    margin: 15px 0;
    border-radius: 5px;
    color: #8B0000;
}

.error-box ul {
    margin: 10px 0;
    padding-left: 20px;
}
```

---

## Goal 6: Add success/action messages using GET parameters

### In `index.php`, add this at the top:

```php
<?php
include 'includes/db.php';

// Check what action just happened
$message = '';
if (isset($_GET['posted'])) {
    $message = 'Quest posted successfully!';
} elseif (isset($_GET['completed'])) {
    $message = '✓ Quest marked as complete!';
} elseif (isset($_GET['deleted'])) {
    $message = '🗑️ Quest deleted!';
}
?>
```

### Display the message in your HTML:

```html
<?php if ($message): ?>
    <div class="success-banner">
        <?= $message ?>
    </div>
<?php endif; ?>
```

### Update action pages to pass the parameter:

**`pages/complete_quest.php`:**
```php
<?php
include '../includes/db.php';

$id = $_GET['id'] ?? null;

if ($id && is_numeric($id)) {
    $stmt = $pdo->prepare("UPDATE quests SET is_complete = 1 WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: ../index.php?completed=1');
    exit;
}
?>
```

**`pages/delete_quest.php`:**
```php
<?php
include '../includes/db.php';

$id = $_GET['id'] ?? null;

if ($id && is_numeric($id)) {
    $stmt = $pdo->prepare("DELETE FROM quests WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: ../index.php?deleted=1');
    exit;
}
?>
```

### Add CSS for the banner in `style.css`:

```css
.success-banner {
    background-color: #D4EDDA;
    border: 2px solid #28A745;
    color: #155724;
    padding: 15px;
    margin: 15px 0;
    border-radius: 5px;
    font-weight: bold;
    text-align: center;
}
```

---

## Goal 7: Add a "reopen" button to undo completed quests

### Step 1: Update the complete_quest.php to toggle instead of just marking complete

```php
<?php
include '../includes/db.php';

$id = $_GET['id'] ?? null;

if ($id && is_numeric($id)) {
    // Get current status
    $stmt = $pdo->prepare("SELECT is_complete FROM quests WHERE id = ?");
    $stmt->execute([$id]);
    $quest = $stmt->fetch();
    
    if ($quest) {
        // Toggle: if complete (1), set to incomplete (0), and vice versa
        $new_status = $quest['is_complete'] == 1 ? 0 : 1;
        
        $stmt = $pdo->prepare("UPDATE quests SET is_complete = ? WHERE id = ?");
        $stmt->execute([$new_status, $id]);
        
        header('Location: ../index.php?toggled=1');
        exit;
    }
}
?>
```

### Step 2: Update the button in `index.php`

```php
<?php foreach($quests as $quest): ?>
    <div class="quest-card <?= $quest['is_complete'] ? 'completed' : '' ?>">
        <h3><?= htmlspecialchars($quest['title']) ?></h3>
        <p><?= htmlspecialchars($quest['description']) ?></p>
        
        <?php if ($quest['is_complete']): ?>
            <!-- Show reopen button -->
            <a href="pages/complete_quest.php?id=<?= $quest['id'] ?>" class="btn-reopen">
                ↺ Reopen Quest
            </a>
        <?php else: ?>
            <!-- Show complete button -->
            <a href="pages/complete_quest.php?id=<?= $quest['id'] ?>" class="btn-complete">
                ✓ Mark Complete
            </a>
        <?php endif; ?>
        
        <a href="pages/delete_quest.php?id=<?= $quest['id'] ?>" class="btn-delete">
            Delete
        </a>
    </div>
<?php endforeach; ?>
```

### Step 3: Add CSS styling in `style.css`

```css
.quest-card.completed h3 {
    text-decoration: line-through;
    color: #888;
}

.quest-card.completed {
    opacity: 0.7;
    background-color: #f5f5f5;
}

.btn-complete, .btn-reopen {
    background-color: #4CAF50;
    color: white;
    padding: 8px 12px;
    border: none;
    border-radius: 4px;
    text-decoration: none;
    cursor: pointer;
    margin-right: 5px;
}

.btn-reopen {
    background-color: #FF9800;
}

.btn-delete {
    background-color: #f44336;
    color: white;
    padding: 8px 12px;
    border: none;
    border-radius: 4px;
    text-decoration: none;
    cursor: pointer;
}

.btn-complete:hover {
    background-color: #45a049;
}

.btn-reopen:hover {
    background-color: #e68900;
}

.btn-delete:hover {
    background-color: #da190b;
}
```

---

## Summary of All Changes

| Goal | Database | Form | Display | Action Files |
|------|----------|------|---------|--------------|
| **Difficulty** | Add `difficulty` column | Add select dropdown | Show badge with color | Update INSERT |
| **Reward** | Add `reward` column | Add text input | Show reward text | Update INSERT |
| **Search** | No change | Add search form | Filter with LIKE | Conditional SELECT |
| **Timestamps** | Already has `created_at` | No change | Show "X days ago" | Add helper function |
| **Validation** | No change | No change | Show errors | Add validation logic |
| **Messages** | No change | No change | Show banner | Add GET parameters |
| **Reopen** | No change | No change | Toggle button text | Update to toggle status |

---

## Quick File Checklist

Make sure you update these files:
- `sql/setup.sql` - Add new columns
- `includes/db.php` - Add helpers if needed
- `includes/header.php` - Update as needed
- `pages/post_quest.php` - Add difficulty, reward, validation
- `pages/complete_quest.php` - Add toggle logic
- `pages/delete_quest.php` - Add redirect parameter
- `index.php` - Add search, filter, display new fields, show messages
- `style.css` - Add styling for badges, banners, error boxes, buttons
