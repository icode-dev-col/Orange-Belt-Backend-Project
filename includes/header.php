<?php
// =============================================================
//  includes/header.php — Shared Page Header
// =============================================================
//  Every page includes this file so the nav bar and <head>
//  section don't have to be copy-pasted everywhere.
//
//  GOAL: Understand why shared components ("partials") are useful.
//        If you want to rename the site, you change it here ONCE.
//
//  HOW IT WORKS:
//  Each page does:
//      $pageTitle = 'Some Title';
//      include 'includes/header.php';
//
//  When PHP sees "include", it literally pastes this file's
//  content right there. It's like copy-paste, but automatic.
//  The $pageTitle variable is already set before this runs,
//  so we can use it inside this file.
//
//  WHY is this useful?
//  Imagine you have 5 pages that all need the same nav bar.
//  Without a shared header, you'd copy the same HTML into
//  all 5 files. Then if you want to add a new nav link,
//  you'd have to edit 5 files. With include, you edit ONE.
// =============================================================
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!--
        The page title can be set by each page before including this file.
        If a page doesn't set $pageTitle, we fall back to "Quest Board".

        isset() checks "does this variable exist?"
        The ? : syntax is called a TERNARY OPERATOR — a shorthand if/else:
            condition ? value_if_true : value_if_false

        htmlspecialchars() — SECURITY:
        This function converts special characters like < > & into safe HTML
        codes so they display as text instead of being treated as HTML tags.
        This prevents a type of hack called XSS (Cross-Site Scripting).
        More on this below in index.php!
    -->
    <title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) : 'Quest Board'; ?></title>

    <link rel="stylesheet" href="/quest-board/style.css">
</head>
<body>

<header class="site-header">
    <div class="header-inner">
        <a href="/quest-board/index.php" class="site-logo">⚔️ The Quest Board</a>
        <nav class="site-nav">
            <a href="/quest-board/index.php">📜 View Quests</a>
            <a href="/quest-board/pages/post_quest.php">✍️ Post a Quest</a>
        </nav>
    </div>
</header>

<main class="container">
