<?php
// =============================================================
//  includes/db.php — Database Connection
// =============================================================
//  This file creates ONE connection to the database and stores
//  it in a variable called $pdo.
//
//  Every other PHP file that needs the database does:
//      include 'includes/db.php';
//  ...and then uses $pdo to run queries.
//
//  WHY a separate file?
//  Because if your password ever changes, you only update it
//  HERE — not in every single page. That's called DRY code:
//  "Don't Repeat Yourself."
//
//  Imagine if the password was written in 5 different files.
//  If it changes, you'd have to find and update ALL of them.
//  Miss one, and your site breaks. DRY prevents that by keeping
//  shared information in exactly one place.
// =============================================================


// --- Configuration ---
// These are the settings XAMPP uses by default.
// You probably won't need to change these.

$host   = 'localhost';   // Where MySQL is running (same computer = localhost)
$dbname = 'quest_board'; // The database we created in setup.sql
$user   = 'root';        // XAMPP's default MySQL username
$pass   = '';            // XAMPP's default MySQL password (empty string)

// ⚠️  SECURITY NOTE:
//  In a real production app, you would NEVER hard-code passwords here.
//  You'd store them in a separate file (like .env) that isn't uploaded
//  to the internet. For local learning, this is fine — but it's good
//  to know the "real world" approach!


// --- Connection ---
//
// PDO = PHP Data Objects.
// It's PHP's built-in tool for talking to databases.
//
// Think of PDO like a translator:
//   Your PHP code speaks PHP → PDO translates → MySQL understands
//
// PDO is not the ONLY way to talk to MySQL from PHP, but it's the
// recommended way because:
//   1. It's secure — it supports prepared statements (stops SQL injection)
//   2. It's flexible — it works with MySQL, PostgreSQL, SQLite, etc.
//   3. It's modern — it uses object-oriented code (the -> syntax)
//
// We wrap the connection in try/catch. Here's why:
//
//   try {
//       // Code that MIGHT fail goes here
//   } catch (SomeError $e) {
//       // If it failed, run THIS code instead of crashing
//   }
//
//   Without try/catch, a failed database connection would show a
//   confusing PHP error page. With it, we can show a helpful message.

try {

    // The string 'mysql:host=...;dbname=...' is called a DSN (Data Source Name).
    // It tells PDO:
    //   - What type of database? → mysql
    //   - Where is it?          → host=localhost
    //   - Which database?       → dbname=quest_board
    //   - What encoding?        → charset=utf8 (supports accents, emojis, etc.)
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);

    // ERRMODE_EXCEPTION tells PDO: "If something goes wrong with a query,
    // throw an exception (a PHP error) so I know about it."
    // Without this, PDO fails SILENTLY — your query just doesn't work
    // and you have no idea why. Very confusing when debugging!
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // FETCH_ASSOC makes PDO return rows as associative arrays:
    //   $row['title']        ← easy to read!
    // Instead of numbered arrays:
    //   $row[0]              ← what is column 0? No idea!
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

} catch (PDOException $e) {

    // If the connection failed, stop everything and show the error.
    // die() is like saying "stop the entire program right now".
    //
    // Common reasons this fails:
    //   - MySQL isn't running in XAMPP (check the control panel!)
    //   - The database name is wrong or doesn't exist yet
    //   - The username/password is wrong
    //
    // In a real production app you'd log this error privately (so
    // hackers can't see your database details), but for local
    // development, showing the message is fine.
    die("❌ Database connection failed: " . $e->getMessage());

}

// At this point, $pdo is ready to use.
// Any file that includes this one can now run queries like:
//
//   $stmt = $pdo->prepare("SELECT * FROM quests");
//   $stmt->execute();
//   $quests = $stmt->fetchAll();
//
// The three-step pattern (prepare → execute → fetch) is used everywhere
// in this project. You'll see it in index.php, post_quest.php, etc.
?>
