<?php
// =============================================================
//  pages/post_quest.php — Post a New Quest
// =============================================================
//  This page does two different things depending on HOW it's loaded:
//
//  1. GET request  → User clicked the link → Show the empty form
//  2. POST request → User submitted the form → Save quest to DB,
//                    then redirect back to the board
//
//  This is the C in CRUD — CREATE.
//
//  WHY does one page do two things?
//  This is a very common PHP pattern. The form's "action" attribute
//  is set to "" (empty), which means "submit back to THIS page."
//  So:
//    - First visit: the browser sends a GET request → PHP skips
//      the form-handling code and just shows the blank form.
//    - When you click "Post Quest": the browser sends a POST request
//      → PHP processes the data, saves it, and redirects you.
//
//  HOW TO TELL WHICH ONE IS HAPPENING:
//    $_SERVER['REQUEST_METHOD'] tells us. It's either 'GET' or 'POST'.
// =============================================================

include '../includes/db.php';

// --- Error tracking ---
// We'll store any validation error messages in this array.
// If it's empty after checking, everything is fine.
// An array is like a list — we can add items with $errors[] = 'message';
$errors = [];


// =============================================================
//  HANDLE FORM SUBMISSION
// =============================================================
//  $_SERVER is a special PHP array that contains information about
//  the current HTTP request. $_SERVER['REQUEST_METHOD'] tells us
//  whether the request was GET (clicking a link) or POST (submitting
//  a form with method="post").
//
//  We only want to process data if a form was actually submitted.
// =============================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // --- Step 1: Read and clean the submitted data ---
    //
    // $_POST is an associative array containing everything the
    // user typed into the form fields. The keys match the "name"
    // attributes of the HTML <input> and <textarea> elements:
    //
    //   <input name="title">      →  $_POST['title']
    //   <textarea name="description">  →  $_POST['description']
    //   <input name="posted_by">  →  $_POST['posted_by']
    //
    // trim() removes accidental spaces from the start and end.
    //   e.g. "  Slay the dragon  " becomes "Slay the dragon"
    //   This is important because "   " (just spaces) would look
    //   empty but technically isn't — trim fixes that.
    //
    // The ?? operator is the "null coalescing operator":
    //   $_POST['title'] ?? ''
    //   means: "use $_POST['title'] if it exists, otherwise use ''"
    //   This prevents an error if someone submits without the field.

    $title       = trim($_POST['title']       ?? '');
    $description = trim($_POST['description'] ?? '');
    $posted_by   = trim($_POST['posted_by']   ?? '');

    // If posted_by was left blank, use "Anonymous" as a default
    if ($posted_by === '') {
        $posted_by = 'Anonymous';
    }


    // --- Step 2: Validate — check for problems before saving ---
    //
    // NEVER trust user input! Always check it before putting it
    // in the database. This is called "server-side validation."
    //
    // WHY server-side?
    // The HTML form has "required" attributes, but a hacker can
    // easily bypass those (just open browser dev tools and remove
    // the attribute). Server-side validation can't be bypassed
    // because it runs on YOUR computer, not the user's browser.
    //
    // We add error messages to the $errors array. If the array
    // is still empty after all checks, the data is safe to save.

    if ($title === '') {
        $errors[] = 'A quest must have a title.';
    }

    if (strlen($title) > 150) {
        $errors[] = 'Quest title must be 150 characters or less.';
    }

    if ($description === '') {
        $errors[] = 'A quest must have a description.';
    }


    // --- Step 3: Save to database (only if no errors) ---
    if (empty($errors)) {

        // =============================================================
        //  PREPARED STATEMENTS & SQL INJECTION — FULL EXPLANATION
        // =============================================================
        //
        //  WHAT IS SQL INJECTION?
        //  SQL injection is one of the most common and dangerous web
        //  attacks. It happens when a hacker types SQL code into a form
        //  field, and your app accidentally runs it as part of a query.
        //
        //  HOW DOES IT WORK? (The dangerous way — DON'T do this!)
        //  Imagine you wrote your query like this:
        //
        //    $sql = "INSERT INTO quests (title) VALUES ('$title')";
        //
        //  If a normal user types "Slay the Troll", the query becomes:
        //    INSERT INTO quests (title) VALUES ('Slay the Troll')
        //    ✅ Works fine!
        //
        //  But what if a hacker types this as the title?
        //    '); DROP TABLE quests; --
        //
        //  The query becomes:
        //    INSERT INTO quests (title) VALUES (''); DROP TABLE quests; --')
        //
        //  MySQL reads this as THREE commands:
        //    1. INSERT INTO quests (title) VALUES ('')        ← inserts empty quest
        //    2. DROP TABLE quests                              ← DELETES YOUR ENTIRE TABLE
        //    3. --')                                           ← the -- is a comment, ignoring the rest
        //
        //  💀 Your entire quests table — ALL the data — is gone. Forever.
        //
        //  Even scarier, a hacker could also:
        //    - Read all usernames and passwords from a users table
        //    - Modify someone else's account
        //    - Delete the entire database
        //    - In some cases, run system commands on your server!
        //
        //  REAL WORLD: SQL injection has been used to hack major companies
        //  including Sony, Yahoo, LinkedIn, and government agencies.
        //  It's consistently ranked #1 on the OWASP Top 10 (a list of
        //  the most critical web security risks).
        //
        //  HOW DO PREPARED STATEMENTS STOP THIS?
        //  Instead of putting user data directly in the SQL string,
        //  we use ? placeholders:
        //
        //    $stmt = $pdo->prepare("INSERT INTO quests (title) VALUES (?)");
        //    $stmt->execute([$title]);
        //
        //  This works in TWO separate steps:
        //    1. prepare() sends the SQL STRUCTURE to MySQL:
        //       "I'm going to insert a value into the title column."
        //       MySQL parses and compiles this plan.
        //
        //    2. execute() sends the DATA separately:
        //       "The value is: '); DROP TABLE quests; --"
        //       MySQL treats this ENTIRELY as data — it will never
        //       try to run it as SQL code. It just stores the text
        //       as a quest title.
        //
        //  The key insight: MySQL already knows the STRUCTURE of the
        //  query from step 1. In step 2, no matter what the user types,
        //  it can only fill in the data slots. It can't change the
        //  query's structure or add new commands.
        //
        //  ANALOGY: It's like a fill-in-the-blank form.
        //  "My name is _____."  No matter what you write in the blank,
        //  you can't change the sentence structure to say something else.
        //
        //  RULE: ALWAYS use prepared statements when user input is
        //        involved. There are NO exceptions to this rule.
        // =============================================================

        $stmt = $pdo->prepare("
            INSERT INTO quests (title, description, posted_by)
            VALUES (?, ?, ?)
        ");

        // execute() sends the actual values to fill in the ? slots.
        // The values are passed as an array, in the same order as the ?s:
        //   First ?  →  $title
        //   Second ? →  $description
        //   Third ?  →  $posted_by
        // PDO handles escaping them safely — you don't have to do anything!
        $stmt->execute([$title, $description, $posted_by]);

        // --- Step 4: Redirect to the home page ---
        //
        // header('Location: ...') tells the browser "go to this URL instead."
        // This is called a REDIRECT.
        //
        // WHY redirect after inserting?
        // This pattern is called POST-REDIRECT-GET (PRG):
        //
        //   1. POST: User submits the form (data is sent)
        //   2. REDIRECT: Server sends back "go to the board page"
        //   3. GET: Browser loads the board page normally
        //
        // Without the redirect, if the user hit the browser's refresh
        // button, the browser would ask "Re-submit form data?" and
        // could accidentally create a duplicate quest! The redirect
        // prevents this because the last request was a GET, not a POST.
        //
        // We pass ?posted=1 in the URL so index.php could optionally
        // show a "Quest posted successfully!" message (stretch goal!).
        header('Location: /quest-board/index.php?posted=1');

        // ALWAYS exit after a redirect!
        // Without exit, PHP continues running the rest of the file,
        // which wastes resources and can cause bugs.
        exit;
    }

} // end if POST


// Set the page title and load the shared header
$pageTitle = 'Post a New Quest';
include '../includes/header.php';
?>

<section class="page-hero">
    <h1>✍️ Post a New Quest</h1>
    <p>Fill out the form below to add your quest to the board.</p>
</section>

<?php
// If there were validation errors, display them all.
// empty() checks if an array has zero items.
// The ! means "not", so !empty() = "has at least one item".
if (!empty($errors)):
?>
    <div class="alert alert-error">
        <strong>⚠️ Please fix the following:</strong>
        <ul>
            <?php foreach ($errors as $error): ?>
                <li><?php echo htmlspecialchars($error); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<!--
    THE HTML FORM
    =============

    method="post" means the data goes in the HTTP body (hidden),
    NOT in the URL. This is the right choice for submitting data
    that creates/changes something.

    method="get" would put the data in the URL (like ?title=Hello).
    That's fine for search forms, but not for creating data.

    action="" means "submit back to this same page."
    The PHP code at the TOP of this file handles the submission.

    HOW THE FORM DATA REACHES PHP:
    1. User types in the fields and clicks "Post Quest"
    2. Browser collects all fields with a "name" attribute
    3. Browser sends them to the server as key=value pairs:
         title=Slay+the+Troll&description=A+big+troll&posted_by=Me
    4. PHP receives them in the $_POST array:
         $_POST['title'] = "Slay the Troll"
         $_POST['description'] = "A big troll"
         $_POST['posted_by'] = "Me"
-->
<form method="post" action="" class="quest-form">

    <!-- Quest Title -->
    <div class="form-group">
        <label for="title">Quest Title <span class="required">*</span></label>
        <!--
            STICKY FORM VALUES:
            If the form failed validation and the page reloaded,
            we refill the field with what the user already typed.
            This is a nice UX (user experience) touch — nobody
            wants to retype everything after a small mistake!

            htmlspecialchars() keeps it safe from XSS.
            The ?? '' is the null coalescing operator again:
            "use $_POST['title'] if set, otherwise use empty string."
        -->
        <input
            type="text"
            id="title"
            name="title"
            maxlength="150"
            placeholder="e.g. Slay the Forest Troll"
            required
            value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>"
        >
        <small>Max 150 characters. Make it clear and exciting!</small>
    </div>

    <!-- Description -->
    <div class="form-group">
        <label for="description">Description <span class="required">*</span></label>
        <textarea
            id="description"
            name="description"
            rows="5"
            placeholder="Describe the quest in detail. Where? What's needed? Any dangers?"
            required
        ><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
    </div>

    <!-- Posted By -->
    <div class="form-group">
        <label for="posted_by">Your Name <span class="optional">(optional)</span></label>
        <input
            type="text"
            id="posted_by"
            name="posted_by"
            maxlength="100"
            placeholder="Leave blank to post as Anonymous"
            value="<?php echo htmlspecialchars($_POST['posted_by'] ?? ''); ?>"
        >
    </div>

    <button type="submit" class="btn btn-primary btn-large">
        📌 Post Quest to the Board
    </button>

</form>

<p style="margin-top: 1.5rem;">
    <a href="/quest-board/index.php">← Back to the Board</a>
</p>

</main>

<footer class="site-footer">
    <p>⚔️ Quest Board — iCode Orange Belt Backend Project</p>
</footer>

</body>
</html>
