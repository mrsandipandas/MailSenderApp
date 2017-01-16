<?php
/*************************************************
 * Micro Postcard
 *
 * Version: 1.0
 * Date: 2007-07-10
 *
 * Usage:
 * Step 1.
 *     Copy your normal sized image into the images directory
 *     and the thumbnails with the same name into the thumbs directory.
 *     We recommend to use 640x480 for normal images and
 *     128x96 for thumbnails.
 *
 * Step 2.
 *     Edit the senderName and senderEmail to a valid name and email.
 *
 * Step +1.
 *     Set the postcardURL to the URL where you installed the script
 *     if the address in the email is invalid.
 *
 ****************************************************/

// CHANGE PARAMETERS HERE BEGIN
$columns = 5;
$senderName  = 'Sandipan Das'; // Eg.: John's Postcards
$senderEmail = 'sandipan.das@in.pwc.com';  // Eg.: john@postcard.com
// Change only if you have problems with urls
$postcardURL = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'];
// CHANGE PARAMETERS HERE END



// This function displays the available images
function displayPhotos() {
    global $columns;

    $act = 0;
    // Open the actual directory
    if ($handle = opendir("thumbs")) {
        // Read all file from the actual directory
        while ($file = readdir($handle)) {
            if (!is_dir($file)) {
                if ($act == 0) echo "<tr>";
                echo "<td align='center'><img src='thumbs/$file' alt='postcard' /><br/><input type='radio' name='selimg' value='$file' /></td>";
                $act++;
                if ($act == $columns) {
                    $act = 0;
                    echo "</tr>";
                }
            }
        }
        echo "</tr>";
    }
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "DTD/xhtml1-transitional.dtd">
<html>
    <head>
        <title>Send E-card</title>
        <link href="style/style.css" rel="stylesheet" type="text/css" />
    </head>
    <body>
        <div id="main">
            <div id="caption">E-card Sender</div>
            <?php if ( (!isset($_POST['submit'])) && (!isset($_GET['show'])) ) {
    ?>
            <form action="<?php echo $_SERVER['PHP_SELF'];
                        ?>" method="post">
                <table align="center">
    <?php displayPhotos();
    ?>
                </table>
                <h2>Fill the form</h2>
                <table width="100%">
                    <tr><td>Send to (email address):</td><td><input type="text" name="email" size="30"/></td></tr>
                    <tr><td>Message:</td><td><textarea name="message" rows="10" cols="40"></textarea></td></tr>
                    <tr><td colspan="2" align="center"><input type="submit" value="Send card!" name="submit"/></td></tr>
                </table>
            </form>
                <?php
            }
            else if ( (isset($_POST['submit'])) && (!isset($_GET['show'])) ) {
                $pic = isset ($_POST['selimg']) ? $_POST['selimg'] : '';
                $filename = date('YmdGis');
                $f = fopen('messages/'.$filename.".txt","w+");
                fwrite($f,$pic."\n");
                fwrite($f,$_POST['email']."\n");
                fwrite($f,htmlspecialchars($_POST['message'])."\n");
                fclose($f);

                // Compose the mail
                $from   = "From: $senderName <$senderEmail>\r\n";
                $replay = "Reply-To: $senderEmail\r\n";
                $params = "MIME-Version: 1.0\r\n";
                $params .= "Content-type: text/plain; charset=iso-8859-1\r\n";
                $mailtext = "You have just received a virtual postcard!\r\n\r\n"
                    . "You can pick up your postcard at the following web address:\r\n"
                    . "$postcardURL"."?show=$filename\r\n\r\n"
                    . "We hope you enjoy your postcard, and if you do, please take a moment to send a few yourself!\r\n\r\n"
                    . "Regards,\r\n"
                    . "MicroPostcard\r\n"
                    . $postcardURL;


    // Send email
    @mail($_POST['email'],"You've received a postcard",$mailtext,$from.$replay.$params);

                ?>

            <center>
                Your postcard was sended succesfuly!<br/><br/>
                <img src='images/<?php echo $pic;
                ?>' alt="postcard" /><br/><br/><br/><?php echo nl2br(htmlspecialchars($_POST['message']));
                ?></center>
                <?php
            }
            else if ( (!isset($_POST['submit'])) && (isset($_GET['show'])) ) {
                $file = isset($_GET['show']) ?  $_GET['show'] : ''          ;
    $content = file('messages/'.$file.".txt");
    $pic   = $content['0'];
    unset ($content['0']);
    unset ($content['1']);
                $main = "";
    foreach ($content as $value) {
        $main .= $value;
    }
    ?>
            <center>
                Your postcard!<br/><br/>
                <img src='images/<?php echo $pic;
    ?>' alt="postcard" /><br/><br/><br/><?php echo nl2br(htmlspecialchars($main));
    ?></center>

    <?php
}
?>
            <div id="source">Micro Postcard 1.0</div>
        </div>
    </body>
</html>
