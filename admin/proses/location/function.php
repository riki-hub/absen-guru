<?php
include '../../../koneksi.php';

if (isset($_POST['id'])) {
    $id = $_POST['id'];

    // Get the current status
    $query = "SELECT status FROM location WHERE id = ?";
    $stmt = $koneksi->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $currentStatus = $row['status'];

        // Toggle the status
        $newStatus = ($currentStatus === 'true' || $currentStatus === '1') ? 'false' : 'true';

        // Update the status in the database
        $updateQuery = "UPDATE location SET status = ? WHERE id = ?";
        $updateStmt = $koneksi->prepare($updateQuery);
        $updateStmt->bind_param("si", $newStatus, $id);
        $updateStmt->execute();

        if ($updateStmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'newStatus' => $newStatus]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update status.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Location not found.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
}
?>
