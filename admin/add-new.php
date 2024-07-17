<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/animations.css">  
    <link rel="stylesheet" href="../css/main.css">  
    <link rel="stylesheet" href="../css/admin.css">
        
    <title>Doctor</title>
    <style>
        .popup{
            animation: transitionIn-Y-bottom 0.5s;
        }
</style>
</head>
<body>
    <?php

    

session_start();

// Check if the user is an admin (you should have an authentication system)
if ($_SESSION['usertype'] === 'admin') {
    include("connection.php");

    // Handle approval and rejection of doctor registrations
    if (isset($_POST['action']) && isset($_POST['doctor_id'])) {
        $doctorId = $_POST['doctor_id'];

        if ($_POST['action'] === 'approve') {
            // Approve the doctor registration
            $stmt = $database->prepare("UPDATE doctor_registrations SET status = 'approved' WHERE id = ?");
        } elseif ($_POST['action'] === 'reject') {
            // Reject the doctor registration
            $stmt = $database->prepare("UPDATE doctor_registrations SET status = 'rejected' WHERE id = ?");
        }

        $stmt->bind_param("i", $doctorId);

        if ($stmt->execute()) {
            echo "Registration updated successfully.";
        } else {
            echo "Error updating registration: " . $stmt->error;
        }

        $stmt->close();
    }

    // Fetch and display pending doctor registrations
    $sql = "SELECT id, name, email, status FROM doctor_registrations WHERE status = 'pending'";
    $result = $database->query($sql);

    if ($result->num_rows > 0) {
        echo "<table border='1'>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Action</th>
                </tr>";

        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>" . $row['id'] . "</td>
                    <td>" . $row['name'] . "</td>
                    <td>" . $row['email'] . "</td>
                    <td>
                        <form method='post' action='admin.php'>
                            <input type='hidden' name='doctor_id' value='" . $row['id'] . "'>
                            <button type='submit' name='action' value='approve'>Approve</button>
                            <button type='submit' name='action' value='reject'>Reject</button>
                        </form>
                    </td>
                </tr>";
        }

        echo "</table>";
    } else {
        echo "No pending doctor registrations found.";
    }
} else {
    echo "You are not authorized to access this page.";
}
?>
</body>
</html>
    

    