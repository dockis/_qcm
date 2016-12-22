<?php
require_once("lib/TestApi.php");

$test = new TestApi();

$test->actionHandler("list");
?>
<!DOCTYPE html>
<html lang="cs-cz">
    <head>
        <meta charset="utf-8" />
        <title>test rozhrani REST</title>
        <script>
            function setAction(action,id = 0)
            {
                document.getElementById("usersId").value = id;
                document.getElementById("usersAction").value = action;
                document.getElementById("usersForm").submit();
            }
        </script>
    </head>
    <body>
        <h1>jednoduchá správa uživatelů</h1>
        <?php
        if($test->message != "") echo "<h2>".$test->message."</h2>";
        ?>
        <form action="index.php" method="post" id="usersForm" name="usersForm">
            <input type="hidden" id="usersId" name="usersId">
            <input type="hidden" id="usersAction" name="usersAction">
            <table>
                <tr>
                    <th colspan="4"><?php echo ($test->action == "detail")? "detail uživatele:" : "přidej uživatele:"; ?></th>
                </tr>
                <tr>
                    <td>jméno</td>
                    <td>příjmení</td>
                    <td>email</td>
                    <td></td>
                </tr>
                <tr>
                    <td><input type="text" name="name" value="<?php echo isset($test->detailUsers->name)? $test->detailUsers->name : ""; ?>"></td>
                    <td><input type="text" name="surname" value="<?php echo isset($test->detailUsers->surname)? $test->detailUsers->surname : ""; ?>"></td>
                    <td><input type="text" name="email" value="<?php echo isset($test->detailUsers->email)? $test->detailUsers->email : ""; ?>"></td>
                    <td><?php
                        if ($test->action == "detail")
                            echo "<button onclick=\"setAction('update', ".$test->detailUsers->users_id.")\">opravit</button>";
                        else
                            echo "<button onclick=\"setAction('insert')\">přidat</button>";
                        ?><button onclick="setAction('reset')">reset</button></td>
                </tr>
            </table>
        </form>

        <table>
            <tr>
                <th colspan="6">výpis uživatelů:</th>
            </tr>
            <?php
            $test->printListUsers();
            ?>
        </table>
    </body>
</html>