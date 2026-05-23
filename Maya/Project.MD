this file can create a connection in the database, and $pdo is the way is stores it in a variable, and the reason it includes -d in it is because other php files include db.php, and then it uses $pdo to run it. 
It takes you to a new quest form so you can type in anything for awebsite or information.
it is dangerous because if someone was going to type something on an input feel on google or something, the website collapses, which is why it is important to use prepared statements.
$_POST contains private infornation like passwords, and $_GET isn't that sensitive, and i isn't that secure.
It prevents a website collapsing, and you can use prepared statement for that, and it states that characters can be allowed in an input feel.
It redirects after saving because th ebrowser will send you a post request, so it can process the data and redirect you.

