<?php
// =============================================================
//  pages/complete_quest.php — Mark a Quest as Complete
// =============================================================
//  This page has NO visual output for the user.
//  It just:
//    1. Reads the quest id from the URL
//    2. Updates that quest's is_complete column to 1
//    3. Redirects back to the board
//
//  This is the U in CRUD — UPDATE.
//
//  The URL looks like: complete_quest.php?id=3
//  The ?id=3 part is called a "query string" or "GET parameter".
//
//  This page is a "controller" — it does work behind the scenes
//  but never shows anything to the user. Think of it like a
//  backstage worker in a theater: you never see them, but they
//  make things happen.
// =============================================================

include '../includes/db.php';


// --- Step 1: Read the id from the URL ---
//
// $_GET is an associative array of everything in the URL after the ?
// For the URL: complete_quest.php?id=3
//   $_GET['id'] => "3"   (note: it's a string, not an integer!)
//
// You can have multiple GET parameters separated by &:
//   page.php?id=3&sort=asc
//   $_GET['id']   => "3"
//   $_GET['sort'] => "asc"
//
// IMPORTANT — $_GET vs $_POST:
//   $_GET  = data from the URL (visible to everyone, used for links)
//   $_POST = data from a form submission (hidden in the request body)
//
//   Use $_GET for reading/viewing actions (like "show me quest #3")
//   Use $_POST for actions that change data (like "create this quest")
//
//   In this case, we use GET because we're passing data via a link.
//   Ideally, actions that modify data (like completing a quest) should
//   use POST requests for security — but for simplicity, we use GET here.
//   That's something to think about for a stretch goal!
//
// The ?? operator means "use this value, or fall back to null if it doesn't exist"
// This prevents an error if someone visits complete_quest.php without ?id=
$id = $_GET['id'] ?? null;


// --- Step 2: Validate the id ---
//
// NEVER trust user input! Even something as simple as an id.
//
// WHY? Because the id comes from the URL, which the user can modify.
// Someone could type: complete_quest.php?id=abc
// or: complete_quest.php?id=-5
// or: complete_quest.php?id=1;DROP TABLE quests
//
// We check three things:
//   a) $id !== null — the id was actually provided
//   b) is_numeric($id) — it's a number, not letters or symbols
//   c) (int)$id > 0 — it's a positive number (no negatives or zero)
//
// If any check fails, we redirect back to safety instead of crashing.
// This is called "defensive programming" — always expect the worst
// from user input and handle it gracefully.

if ($id === null || !is_numeric($id) || (int)$id <= 0) {
    // Bad or missing id — go back to the board
    header('Location: /quest-board/index.php');
    exit;
}

// Cast to integer now that we've confirmed it's a safe number.
// (int) converts the string "3" into the number 3.
// This is called "type casting."
$id = (int)$id;


// --- Step 3: Run the UPDATE query ---
//
// SQL BREAKDOWN:
//   UPDATE quests          → "I want to change data in the quests table"
//   SET is_complete = 1    → "Change the is_complete column to 1 (true)"
//   WHERE id = ?           → "But ONLY for the row where id matches"
//
// ⚠️  The WHERE clause is CRITICAL!
// Without it, the query would be:
//   UPDATE quests SET is_complete = 1
// This would mark EVERY quest as complete — all of them!
// Always double-check you have a WHERE when using UPDATE.
//
// We use a prepared statement (?) even here because $id came
// from user input ($_GET). Even though we validated it above,
// using prepared statements is a good habit. Defense in depth —
// multiple layers of security are better than one.

$stmt = $pdo->prepare("UPDATE quests SET is_complete = 1 WHERE id = ?");
$stmt->execute([$id]);


// --- Step 4: Redirect back to the board ---
//
// We pass ?completed=1 in the URL so index.php could optionally
// show a success message like "Quest marked as complete!" —
// that's a stretch goal for students to implement!
header('Location: /quest-board/index.php?completed=1');
exit;

// No HTML below — this page is purely a "controller".
// Think of it like a machine in a factory that does a job
// and passes the item along, with no display of its own.
?>
