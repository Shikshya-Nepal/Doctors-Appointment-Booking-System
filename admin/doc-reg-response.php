<?php
// Database connection setup
$servername = "localhost:3307";
$username = "root";
$password = "";
$database = "edoc";

$connection = new mysqli($servername, $username, $password, $database);

// Check the connection
if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

// Check if the form was submitted
if (isset($_POST['action']) && isset($_POST['doctor_id'])) {
    $doctorId = $_POST['doctor_id'];

    if ($_POST['action'] === 'approve') {
        // Approve the doctor registration
        $sql = "UPDATE doctor_registrations SET status = 'approved' WHERE id = ?";
    } elseif ($_POST['action'] === 'reject') {
        // Reject the doctor registration
        $sql = "UPDATE doctor_registrations SET status = 'rejected' WHERE id = ?";
    }

    $stmt = $connection->prepare($sql);
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
$result = $connection->query($sql);

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

$connection->close();
?>