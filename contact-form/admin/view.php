<?php
global $wpdb;
$table_name = $wpdb->prefix . 'contact_form';
$sql = "SELECT * FROM $table_name";
$data = $wpdb->get_results("SELECT * FROM $table_name");
// echo "<pre>";
// var_dump($data);
// echo "</pre>";
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>response</title>
</head>
<style>
    .table {
        width: 100%;
        border-collapse: collapse;
    }

    .table,
    th,
    td {
        padding: 1em 2em;
        text-align: center;
        border: 2px black solid;
    }
</style>

<body class="p-5">
    <h2 class="mb-3">Messages send by users</h2>
    <table class="table">
        <thead class="table">
            <tr>
                <th>Id</th>
                <th>First name</th>
                <th>Last name</th>
                <th>Email</th>
                <th>Subject</th>
                <th>Message</th>
                <th>Send date</th>
            </tr>
        </thead>
        <tbody class="table">
            <?php
            foreach ($data as $message) {
                $result = json_encode($message);
                ?>
                <tr>
                    <td>
                        <?php echo $message->id ?>
                    </td>
                    <td>
                        <?php echo $message->prenom ?>
                    </td>
                    <td>
                        <?php echo $message->nom ?>
                    </td>
                    <td>
                        <?php echo $message->email ?>
                    </td>
                    <td>
                        <?php echo $message->sujet ?>
                    </td>
                    <td>
                        <?php echo $message->message ?>
                    </td>
                    <td>
                        <?php echo $message->date_envoi ?>
                    </td>
                </tr>
                <?php
            }
            ?>
        </tbody>
    </table>
</body>

</html>