<html>
    <head/>
<body>

<?php

include('shortlink.class.php');
include('pdo.class.php');
$pdo = false;
if (!file_exists('dbsettings.ini')) {
    if (isset($_POST['host'])) {
        $dsn = 'mysql' .
        ':host=' . $_POST['host'] .
        ';port=' . $_POST['port'] .
        ';dbname=' . $_POST['dbname'];
        try {
            $pdo = new PDO($dsn, $_POST['user'], $_POST['pwd']);
            $shortLink = new shortLink($pdo);
            if (!$shortLink->dbValid('1.0')) {
                print "Creating table<br/>";
                $shortLink->initiateDB();
            }
            $str = "[database]
            version = 1.0
            host = ".$_POST['host']."
            port = ".$_POST['port']."
            dbname = ".$_POST['dbname']."
            username = ".$_POST['user']."
            password = ".$_POST['pwd'];
            $ini = fopen('dbsettings.ini', 'w');
            if (!$ini) {
                die("Unable to write ini file");

            } else {
                fwrite($ini, $str);
                fclose($ini);
            }
        } catch (Exception $e) {
            print 'Caught exception: '.  $e->getMessage(). "\n";
        }
    } else {
?>
DB connection details:
<form method="POST">
<table cellspacing="0" cellpadding="0">
    <tr>
        <td>host:</td><td><input type="text" value="localhost" name="host"/></td>
    </tr>
    <tr>
        <td>port:</td><td><input type="text" value="3306" name="port"/></td>
    </tr>
    <tr>
        <td>dbname:</td><td><input type="text" value="" name="dbname"/></td>
    </tr>
    <tr>
        <td>user:</td><td><input type="text" value="" name="user"/></td>
    </tr>
    <tr>
        <td>password:</td><td><input type="password" value="" name="pwd"/></td>
    </tr>
</table>
<input type="submit" value="submit"/>
</form>
<?php
    }
} else {
    try {
        $pdo = new MyPDO('dbsettings.ini');
    } catch (Exception $e){
        print 'Caught exception: '.  $e->getMessage(). "\n";
        if (file_exists('dbsettings.ini')) {
            unlink('dbsettings.ini');
        }
    }
}

if (is_object($pdo)) {
    $shortLink = new shortLink($pdo);
    if (isset($_POST['link']) && strlen($_POST['link'])) {
        print '<h3>Short link for "'.$_POST['link'].'" is "'.$shortLink->shorten($_POST['link']).'"</h3>';
    }
    if (isset($_POST['short']) && strlen($_POST['short'])) {
        print '<h3>Original link for "'.$_POST['short'].'" is "'.$shortLink->getOrig($_POST['short']).'"</h3>';
    }
    ?>
<form method="post">
Enter link to shorten:
<input type="text" value="" name="link"/><br/>
And/or enter short link to find original one:
<input type="text" value="" name="short"/><br/>
<input type="submit" value="submit" name="submit"/>
</form>
    <?php
}
?>
</body></html>
