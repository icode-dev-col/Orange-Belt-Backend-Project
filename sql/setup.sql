-- =============================================================
--  Quest Board — Database Setup
--  HOW TO USE:
--    1. Open phpMyAdmin at http://localhost/phpmyadmin
--    2. Click the "SQL" tab at the top
--    3. Paste this entire file in and click "Go"
-- =============================================================


-- =============================================================
--  WHAT IS SQL?
-- =============================================================
--  SQL (Structured Query Language) is the language we use to
--  talk to databases. Think of a database as a very powerful
--  spreadsheet — it stores data in rows and columns, and SQL
--  is how you tell it what to do.
--
--  There are four main things you can do with SQL (called CRUD):
--    CREATE  →  INSERT INTO  →  Add new data
--    READ    →  SELECT       →  Look at existing data
--    UPDATE  →  UPDATE       →  Change existing data
--    DELETE  →  DELETE       →  Remove data
--
--  This file uses DDL (Data Definition Language) — a subset of
--  SQL used to *create* the structure (database, tables, columns)
--  rather than the data itself.
-- =============================================================


-- Step 1: Create the database
-- A "database" is like a folder that holds related tables.
-- "IF NOT EXISTS" means it won't crash if you run this twice.
-- Without it, MySQL would throw an error saying "database already exists!"
CREATE DATABASE IF NOT EXISTS quest_board;

-- Step 2: Tell MySQL to use that database for everything below
-- Think of this like opening a folder before putting files in it.
USE quest_board;

-- Step 3: Create the quests table
-- A table is like one spreadsheet within the database:
--   each column = a field (id, title, description...)
--   each row    = one quest
--
-- KEY CONCEPTS IN THIS TABLE:
--   PRIMARY KEY  = A column that uniquely identifies each row.
--                  No two rows can have the same id.
--   AUTO_INCREMENT = MySQL automatically assigns the next number (1, 2, 3...)
--                    so you never have to pick an id yourself.
--   NOT NULL     = This column is required — MySQL will reject rows that
--                  leave it empty. Like marking a form field as "required".
--   DEFAULT      = If you don't provide a value, use this instead.
--   VARCHAR(n)   = A text field that holds up to 'n' characters.
--                  Good for short text like names and titles.
--   TEXT         = A text field with no practical character limit.
--                  Good for long content like descriptions.
--   TINYINT(1)   = A tiny number field that stores 0 or 1.
--                  Perfect for true/false (boolean) values.
--   DATETIME     = Stores a date and time (e.g. "2025-03-15 14:30:00")
CREATE TABLE IF NOT EXISTS quests (

    -- 'id' is a unique number that auto-increments (1, 2, 3, ...)
    -- PRIMARY KEY means no two rows can have the same id.
    -- WHY? Every row needs a unique identifier so we can say
    -- "update quest #3" or "delete quest #7" without ambiguity.
    id          INT AUTO_INCREMENT PRIMARY KEY,

    -- 'title' is a short text field (up to 150 characters), required.
    -- VARCHAR = "variable characters" — it uses only as much space as needed.
    -- We chose 150 because quest titles should be concise, not essays.
    title       VARCHAR(150) NOT NULL,

    -- 'description' is a longer text field, also required.
    -- TEXT type has no practical limit, so descriptions can be as long as needed.
    description TEXT NOT NULL,

    -- 'posted_by' stores the name of whoever posted the quest.
    -- DEFAULT 'Anonymous' means if someone doesn't enter their name,
    -- the database automatically fills in "Anonymous" for them.
    posted_by   VARCHAR(100) NOT NULL DEFAULT 'Anonymous',

    -- 'is_complete' is 0 (false) or 1 (true).
    -- It starts as 0 (not complete) for every new quest.
    -- WHY 0 and 1 instead of "yes" and "no"?
    -- Numbers are faster to search and take less storage.
    -- In programming, 0 = false and 1 = true by convention.
    is_complete TINYINT(1) NOT NULL DEFAULT 0,

    -- 'created_at' automatically saves the exact time a quest was posted.
    -- CURRENT_TIMESTAMP is a special MySQL value meaning "right now".
    -- You don't need to set this — MySQL fills it in automatically.
    -- WHY? So we can sort quests by date and show "Posted on March 15"
    created_at  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP

);

-- Step 4: Insert some sample quests so the board isn't empty at the start
-- This is optional — you can delete these rows later from phpMyAdmin.
--
-- SYNTAX BREAKDOWN:
--   INSERT INTO [table] ([columns]) VALUES ([values]);
--
--   We only list (title, description, posted_by) — we skip id, is_complete,
--   and created_at because they all have automatic defaults.
--   MySQL fills them in for us!
INSERT INTO quests (title, description, posted_by) VALUES
    (
        'Slay the Forest Troll',
        'A massive troll has been spotted near the old bridge. It has been stopping merchants from passing. Brave adventurers needed immediately.',
        'Mayor Aldric'
    ),
    (
        'Deliver Medicine to the Village',
        'The healer in Stonepeak Village has run out of fever root. Please collect 10 bundles from the eastern marsh and bring them before nightfall.',
        'Healer Mira'
    ),
    (
        'Find the Lost Spellbook',
        'My spellbook went missing somewhere in the abandoned library. It has a red cover and glows faintly. Reward offered. No questions asked.',
        'Wizard Theron'
    );

-- That's it! You should now have:
--   Database: quest_board
--   Table:    quests
--   Rows:     3 sample quests
--
-- You can verify this by clicking on "quest_board" in the left sidebar
-- of phpMyAdmin and then clicking on the "quests" table.
--
-- TRY IT: After running this, go to phpMyAdmin and click on the quests
-- table. You should see all 3 rows. Try changing a value directly in
-- phpMyAdmin to see how it works!
