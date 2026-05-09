# Quest Board: Setup Instructions

Follow these steps **in order** to get The Quest Board running on your computer. Each step includes screenshots descriptions and troubleshooting tips so you don't get stuck.

> **Estimated time:** 15–20 minutes for first-time setup.

---

## Table of Contents

1. [What You Need](#-step-0--what-you-need)
2. [Download & Install XAMPP](#-step-1--download--install-xampp)
3. [Start Apache & MySQL](#-step-2--start-apache--mysql)
4. [Copy the Project Files](#-step-3--copy-the-project-files)
5. [Create the Database](#-step-4--create-the-database)
6. [Open the App](#-step-5--open-the-app)
7. [Test Everything](#-step-6--test-everything)
8. [Shutting Down](#-step-7--shutting-down)
9. [Troubleshooting](#-troubleshooting)

---

## Step 0: What You Need

Before starting, make sure you have:

| Tool | Why you need it | Already have it? |
|---|---|---|
| **A computer** (Windows or Mac) | To run everything locally | ✅ |
| **A web browser** (Chrome, Firefox, Edge) | To view your app | ✅ |
| **VS Code** (or any code editor) | To read and edit the code | Download: [code.visualstudio.com](https://code.visualstudio.com) |
| **XAMPP** | Runs the web server (Apache) and database (MySQL) | See Step 1 below |

### What is XAMPP?

XAMPP is a free program that installs **three things** on your computer:

1. **Apache** — A web server. When you visit `http://localhost`, Apache is the one answering and serving your PHP files.
2. **MySQL** — A database server. This is where your quest data is stored.
3. **phpMyAdmin** — A visual tool for looking inside your database (like a spreadsheet viewer for your data).

Think of XAMPP as a "mini internet" on your computer. Instead of uploading files to a real server, everything runs locally.

---

## Step 1: Download & Install XAMPP

### Windows

1. Go to: **[https://www.apachefriends.org/download.html](https://www.apachefriends.org/download.html)**
2. Click the **Windows** download button (choose the latest PHP version)
3. Run the installer (`.exe` file)
4. When asked what to install, make sure these are checked:
   - ✅ Apache
   - ✅ MySQL
   - ✅ PHP
   - ✅ phpMyAdmin
   - (You can uncheck the others like FileZilla, Mercury, Tomcat — we don't need them)
5. Install to the default location: `C:\xampp`
6. Click **Finish** when done

### ✅ How to verify it worked

- **Windows:** You should see a "XAMPP Control Panel" shortcut on your desktop or in your Start Menu.
---

## Step 2: Start Apache & MySQL

### Windows

1. Open the **XAMPP Control Panel** (search "XAMPP" in your Start Menu)
2. You'll see a list of services. Find **Apache** and **MySQL**.
3. Click the **Start** button next to **Apache**
4. Click the **Start** button next to **MySQL**
5. Both should turn **green** — this means they're running!

```
┌─────────────────────────────────────────────┐
│  XAMPP Control Panel                        │
├──────────┬──────────┬───────────────────────┤
│ Module   │ Status   │ Action                │
├──────────┼──────────┼───────────────────────┤
│ Apache   │ 🟢 Running │ [Stop]              │
│ MySQL    │ 🟢 Running │ [Stop]              │
│ FileZilla│          │ [Start]               │
│ Mercury  │          │ [Start]               │
│ Tomcat   │          │ [Start]               │
└──────────┴──────────┴───────────────────────┘
```

### Mac

1. Open **XAMPP** from Applications
2. Go to the **Manage Servers** tab
3. Select **Apache Web Server** and click **Start**
4. Select **MySQL Database** and click **Start**
5. Both should show a green dot

### ✅ How to verify it worked

Open your browser and go to: **`http://localhost`**

You should see the XAMPP welcome page (a page with the XAMPP logo and "Welcome to XAMPP"). If you see this, Apache is working!

> **⚠️ Don't see anything?** Jump to the [Troubleshooting](#-troubleshooting) section.

---

## Step 3: Copy the Project Files

The `quest-board` folder needs to be placed inside XAMPP's **htdocs** directory. This is the folder Apache looks in when you visit `http://localhost`.

### Windows

1. Open **File Explorer**
2. Navigate to: `C:\xampp\htdocs\`
3. Copy the entire `quest-board` folder and paste it here
4. You should now have: `C:\xampp\htdocs\quest-board\`

### ✅ How to verify it worked

Check that your folder structure looks like this:

```
htdocs/
└── quest-board/
    ├── README.md
    ├── Instructions.md          ← This file!
    ├── style.css
    ├── index.php
    ├── sql/
    │   └── setup.sql
    ├── includes/
    │   ├── db.php
    │   └── header.php
    └── pages/
        ├── post_quest.php
        ├── complete_quest.php
        └── delete_quest.php
```

> **⚠️ Common mistake:** Don't put the files directly in `htdocs/` — the whole `quest-board` folder goes inside.
>
> **✅ Correct:** `htdocs/quest-board/index.php`
> **❌ Wrong:** `htdocs/index.php`

---

## Step 4: Create the Database

Before the app can work, we need to create the database and table where quests will be stored. We'll do this using **phpMyAdmin** — a visual tool that lets you manage your MySQL database.

### What is phpMyAdmin?

phpMyAdmin is like a spreadsheet viewer for your database. It lets you:
- See all your databases and tables
- View, add, edit, and delete data
- Run SQL commands manually

### Steps

1. Open your browser and go to: **`http://localhost/phpmyadmin`**

2. You should see the phpMyAdmin dashboard. It looks like a web app with a sidebar listing databases.

3. Click the **"SQL"** tab in the top navigation bar
   - This opens a text area where you can type SQL commands

4. Open the file `sql/setup.sql` in **VS Code** (or any text editor)

5. **Select ALL the text** in `setup.sql` (Ctrl+A on Windows, Cmd+A on Mac)

6. **Copy it** (Ctrl+C / Cmd+C)

7. **Paste it** into the SQL text area in phpMyAdmin (Ctrl+V / Cmd+V)

8. Click the **"Go"** button (bottom-right of the text area)

9. You should see green success messages!

### ✅ How to verify it worked

1. Look at the **left sidebar** in phpMyAdmin
2. You should see a database called **`quest_board`**
3. Click on it — you should see a table called **`quests`**
4. Click on `quests` — you should see **3 sample quests** (the ones from setup.sql):
   - "Slay the Forest Troll"
   - "Deliver Medicine to the Village"
   - "Find the Lost Spellbook"

```
┌────┬────────────────────────────────┬─────────────┬──────────────┐
│ id │ title                          │ posted_by   │ is_complete  │
├────┼────────────────────────────────┼─────────────┼──────────────┤
│  1 │ Slay the Forest Troll          │ Mayor Aldric│      0       │
│  2 │ Deliver Medicine to the Village│ Healer Mira │      0       │
│  3 │ Find the Lost Spellbook        │ Wizard Theron│     0       │
└────┴────────────────────────────────┴─────────────┴──────────────┘
```

> **⚠️ See an error?** Make sure MySQL is running in XAMPP (Step 2). If you see "database already exists," that's fine — it means you ran the script before.

---

## Step 5: Open the App

1. Open your browser
2. Go to: **`http://localhost/quest-board/`**
3. You should see the Quest Board home page with the 3 sample quests!

### What's happening behind the scenes?

When you visit `http://localhost/quest-board/`:

1. Your browser sends a request to **Apache** (the web server running on your computer)
2. Apache sees that the URL points to `quest-board/index.php`
3. Apache passes the file to **PHP**, which runs the code
4. PHP connects to **MySQL** and fetches all quests from the database
5. PHP builds an HTML page with the quest data embedded in it
6. Apache sends the finished HTML back to your browser
7. Your browser displays the page — it has no idea PHP or MySQL were involved!

```
Browser  →  Apache  →  PHP  →  MySQL
                                 ↓
Browser  ←  Apache  ←  PHP  ←  (quest data)
                                 ↓
         You see the page!
```

---

## Step 6: Test Everything

Now let's make sure all four CRUD operations work!

### Test 1: View Quests (READ) ✅
- You already did this! The home page shows all quests.

### Test 2: Post a Quest (CREATE)
1. Click **"✍️ Post a Quest"** in the nav bar (or the button on the home page)
2. Fill in the form:
   - **Title:** `Rescue the Dragon Hatchling`
   - **Description:** `A baby dragon has been trapped in the crystal caves. We need someone small and brave to fit through the entrance.`
   - **Your Name:** `Knight Commander Elara`
3. Click **"📌 Post Quest to the Board"**
4. You should be redirected back to the home page
5. Your new quest should appear at the top! 🎉

### Test 3: Mark a Quest Complete (UPDATE)
1. Find any quest card on the board
2. Click the **"✅ Mark Complete"** button
3. A confirmation popup will appear — click **OK**
4. The quest should now look greyed out with a "✅ Complete" badge
5. The "Mark Complete" button disappears (you can't complete it twice!)

### Test 4: Delete a Quest (DELETE)
1. Find any quest card on the board
2. Click the **"🗑️ Delete"** button
3. A confirmation popup will appear — click **OK**
4. The quest should disappear from the board entirely!

### ✅ How to verify behind the scenes

After each test, go to **phpMyAdmin** (`http://localhost/phpmyadmin`):
1. Click on the `quest_board` database in the sidebar
2. Click on the `quests` table
3. You should see the data has changed:
   - After posting: a new row appears
   - After completing: `is_complete` changed from 0 to 1
   - After deleting: the row is gone

This confirms your PHP code is actually talking to MySQL!

---

## Step 7: Shutting Down

When you're done working:

### Windows
1. Open the **XAMPP Control Panel**
2. Click **Stop** next to Apache
3. Click **Stop** next to MySQL
4. Close the Control Panel

### Mac
1. Open XAMPP
2. Go to **Manage Servers**
3. Click **Stop All**

> **💡 Your data is saved!** Even after stopping MySQL, your quests are stored on disk. Next time you start XAMPP and visit the app, everything will still be there.

> **⚠️ Don't forget to start XAMPP again** next time you want to work on the project! The app only works when Apache and MySQL are running.

---

## Troubleshooting

### "I can't access `http://localhost`"

| Possible cause | Fix |
|---|---|
| Apache isn't running | Open XAMPP Control Panel and click **Start** next to Apache |
| Another app is using port 80 | Skype, IIS, or another web server might be using the port. Close them, or change Apache's port in XAMPP config |
| Firewall blocking it | Temporarily disable your firewall to test |

### "I can't access `http://localhost/phpmyadmin`"

| Possible cause | Fix |
|---|---|
| MySQL isn't running | Open XAMPP Control Panel and click **Start** next to MySQL |
| Apache isn't running | phpMyAdmin needs both Apache AND MySQL |

### "Database connection failed" error on the app

| Possible cause | Fix |
|---|---|
| MySQL isn't running | Start MySQL in XAMPP |
| Database doesn't exist | Run `setup.sql` in phpMyAdmin (Step 4) |
| Wrong database name | Check that `db.php` says `$dbname = 'quest_board';` — must match exactly |

### "Page not found" or 404 error

| Possible cause | Fix |
|---|---|
| Wrong folder location | Make sure quest-board is inside `htdocs/` (not deeper, not outside) |
| Wrong folder name | Must be exactly `quest-board` (lowercase, with hyphen) |
| Typo in URL | Should be exactly `http://localhost/quest-board/` |

### "Blank white page" (no error, no content)

This usually means PHP has a **syntax error** (typo in the code).

**How to find the error:**
1. Open XAMPP Control Panel
2. Next to Apache, click **Logs** → **Error Log** (or find the file at `C:\xampp\apache\logs\error.log`)
3. Scroll to the bottom — the most recent error will tell you the file and line number
4. Alternatively, add this line to the TOP of `index.php` to see errors in the browser:
   ```php
   ini_set('display_errors', 1);
   error_reporting(E_ALL);
   ```
   *(Remove these lines when you're done debugging!)*

### "I ran setup.sql but the table is empty"

The setup script inserts 3 sample quests. If the table exists but is empty:
1. Open phpMyAdmin
2. Click the **SQL** tab
3. Paste and run JUST the INSERT part from `setup.sql`:
   ```sql
   USE quest_board;
   INSERT INTO quests (title, description, posted_by) VALUES
       ('Slay the Forest Troll', 'A massive troll...', 'Mayor Aldric'),
       ('Deliver Medicine to the Village', 'The healer...', 'Healer Mira'),
       ('Find the Lost Spellbook', 'My spellbook...', 'Wizard Theron');
   ```

### "Port 3306 in use" (MySQL won't start)

Another program is using MySQL's port. Common culprits:
- Another MySQL installation
- MariaDB
- A previous XAMPP instance that didn't shut down cleanly

**Fix:** Open Task Manager (Ctrl+Shift+Esc), look for `mysqld.exe`, and end the process. Then try starting MySQL in XAMPP again.

### "I made changes to a PHP file but nothing changed"

PHP runs on the **server**, not in the browser. Unlike CSS/JS:
- **Do:** Save the file in VS Code → Refresh the browser page (F5 or Ctrl+R)
- **Don't:** You do NOT need to restart Apache when you change a PHP file
- **Tip:** Hard refresh with Ctrl+Shift+R clears the browser cache

---

## Quick-Start Checklist (After First Setup)

Once everything is set up, here's what you do each time you want to work on the project:

1. ✅ Open **XAMPP Control Panel**
2. ✅ Start **Apache** and **MySQL**
3. ✅ Open browser to `http://localhost/quest-board/`
4. ✅ Open the `quest-board` folder in **VS Code**
5. 🎉 Start coding!

When done:
1. ✅ Stop Apache and MySQL in XAMPP
2. ✅ Close XAMPP

---

## Next Steps

Once you're comfortable with the basic app, check the **README.md** file for:

- 🟢 **Level 1 goals** — Verify everything works
- 🟡 **Level 2 goals** — Read and understand the code
- 🔴 **Level 3 goals** — Add new features (difficulty badges, search bar, timestamps)

Every PHP file is heavily commented with explanations — read through them like a tutorial!

---

*Built for the iCode Orange Belt Program — Backend Module*
