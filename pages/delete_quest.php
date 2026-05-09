<?php
// =============================================================
//  pages/delete_quest.php — Delete a Quest
// =============================================================
//  Like complete_quest.php, this page has no visual output.
//  It:
//    1. Reads the quest id from the URL
//    2. Deletes that row from the database
//    3. Redirects back to the board
//
//  This is the D in CRUD — DELETE.
//
//  ⚠️  IMPORTANT: In a real app, you'd also check that the
//  logged-in user actually *owns* the quest before deleting it.
//  Otherwise anyone could delete anyone else's quests!
//  We skip that here because we haven't learned authentication yet.
//  That's a great stretch goal to think about!
//
//  ANOTHER REAL-WORLD NOTE:
//  Many real apps don't actually DELETE data — they "soft delete"
//  it by setting a column like is_deleted = 1. This way you can
//  undo mistakes. Our app does a "hard delete" (permanent removal)
//  for simplicity.
// =============================================================

include '../includes/db.php';


// --- Step 1: Read and validate the id ---
// (Same safety check as complete_quest.php — always validate user input!)
//
// DRY NOTE: You might notice this validation code is copy-pasted
// from complete_quest.php. In a larger app, you'd put this in a
// shared helper function to avoid repetition. For now, keeping it
// in each file makes each file self-contained and easier to read.

$id = $_GET['id'] ?? null;

if ($id === null || !is_numeric($id) || (int)$id <= 0) {
    header('Location: /quest-board/index.php');
    exit;
}

$id = (int)$id;


// --- Step 2: Run the DELETE query ---
//
// SQL BREAKDOWN:
//   DELETE FROM quests  →  "Remove a row from the quests table"
//   WHERE id = ?        →  "But ONLY the row where id matches"
//
// ⚠️  The WHERE clause is even MORE critical here than in UPDATE!
// Without WHERE, the query would be:
//   DELETE FROM quests
// This would delete EVERY SINGLE ROW in the table.
// All your quests — gone. No undo button.
//
// REAL-WORLD HORROR STORY:
// In 2017, a programmer at GitLab accidentally ran a DELETE
// without a WHERE clause on a production database and deleted
// 300GB of data. They had to recover from backups.
// Always, always, ALWAYS include WHERE in DELETE and UPDATE queries!
//
// Again, we use a prepared statement (?) because $id came from
// the URL, which means it's user input.

$stmt = $pdo->prepare("DELETE FROM quests WHERE id = ?");
$stmt->execute([$id]);


// --- Step 3: Redirect ---
// After deleting, send the user back to the board.
// The ?deleted=1 parameter lets index.php optionally show
// a "Quest deleted!" message (a stretch goal).
header('Location: /quest-board/index.php?deleted=1');
exit;
?>
