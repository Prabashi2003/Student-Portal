<?php
    session_start();
    require 'db.php';

    if(!isset($_SESSION['user_id'])){
        header('Location: login.html?form=login&error=Please+login+first');
        exit();
    }

    $userId = $_SESSION['user_id'];

    if($_SERVER['REQUEST_METHOD'] == 'POST'){
        $action = $_POST['action'] ?? '';

        if($action === 'update_personal'){
            $fullName = $_POST['full_name'] ?? '';
            $phone = $_POST['phone'] ?? '';
            $dob = $_POST['dob'] ?? '';
            $address = $_POST['address'] ?? '';
            $email = $_POST['email'] ?? '';

            $userStmt = $mysql->prepare("UPDATE users SET email=? WHERE id=?");
            $userStmt->bind_param("si", $email, $userId);
            $userSuccess = $userStmt->execute();

            $checkStmt = $mysql->prepare("SELECT id FROM user_info WHERE user_id=?");
            $checkStmt->bind_param("i", $userId);
            $checkStmt->execute();
            $exists = $checkStmt->get_result()->fetch_assoc();

            if($exists){
                $stmt = $mysql->prepare("UPDATE user_info SET full_name=?, phone=?, dob=?, address=?, email=? WHERE user_id=?");
                $stmt->bind_param("sssssi", $fullName, $phone, $dob, $address, $email, $userId);
            }else{
                $studentId = 'STU'.str_pad($userId, 3, '0', STR_PAD_LEFT);
                $stmt = $mysql->prepare("INSERT INTO user_info (user_id, full_name,phone, dob, address, student_id, email) VALUES(?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("issssss", $userId,$fullName, $phone, $dob, $address, $studentId, $email);;
            }

            $infoSuccess = $stmt->execute();

            if($userSuccess && $infoSuccess){
                $_SESSION['success_message'] = 'Personal Information updated successfully!';
            }else{
                $_SESSION['error_message'] = 'Error updating Personal Information!!';
            }

            header('Location: profile.php');
            exit();
        }

        if($action === 'update_academic'){
            $program = $_POST['program'] ?? '';
            $yearLevel = $_POST['year_level'] ?? '';
            $enrollmentDate = $_POST['enrollment_date'] ?? '';
            $graduationDate = $_POST['graduation_date'] ?? '';

            $enrollmentDate = !empty($enrollmentDate) ? $enrollmentDate.'-01': '';
            $graduationDate = !empty($graduationDate) ? $graduationDate.'-01': '';

            $checkStmt = $mysql->prepare("SELECT id FROM user_info WHERE user_id=?");
            $checkStmt->bind_param("i", $userId);
            $checkStmt->execute();
            $exists = $checkStmt->get_result()->fetch_assoc();

            if($exists){
                $stmt = $mysql->prepare("UPDATE user_info SET program=?, year_level=?, enrollment_date=?, graduation_date=? WHERE user_id=?");
                $stmt->bind_param("ssssi", $program, $yearLevel, $enrollmentDate, $graduationDate, $userId);
            }else{
                $studentId = 'STU'.str_pad($userId, 3, '0', STR_PAD_LEFT);
                $stmt = $mysql->prepare("INSERT INTO user_info (user_id, program, year_level, enrollment_date, graduation_date, student_id) VALUES(?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("isssss", $userId,$program, $yearLevel, $enrollmentDate, $graduationDate, $studentId);
            }

            if($stmt->execute()){
                $_SESSION['success_message'] = 'Academic Information updated successfully!';
            }else{
                $_SESSION['error_message'] = 'Error updating Academic Information !!';
            }

            header('Location: profile.php');
            exit();
        }
    }
    $stmt = $mysql->prepare("SELECT users.username, users.email AS user_email, user_info.* FROM users LEFT JOIN user_info ON users.id = user_info.user_id WHERE users.id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    $fullName = $user['full_name'] ?? 'Your Name';
    $email = $user['user_email'] ?? '';
    $phone = $user['phone'] ?? '';
    $dob = $user['dob'] ?? '';
    $address = $user['address'] ?? '';
    $studentId = $user['student_id'] ?? 'STU'.str_pad($userId, 3, '0', STR_PAD_LEFT);
    $program = $user['program'] ?? '';
    $year = $user['year_level'] ?? '';
    $enrollment = $user['enrollment_date'] ?? '';
    $graduation = $user['graduation_date'] ?? '';

    $initials = '';
    if($fullName && $fullName !== 'Your Name'){
        $nameParts = explode(' ', $fullName);;
        $initials = strtoupper(substr($nameParts[0], 0, 1));

        if(count($nameParts) > 1){
            $initials .= strtoupper(substr($nameParts[0], 0, 1));
        }
    }else{
        $initials = strtoupper(substr($user['username'], 0, 2));
    }

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Profile</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="profile.css">
</head>
<body>
    <nav>
        <a href="index.html"><img src="images/logo.png" alt="logo"></a>
        <div class="nav-links">
            <ul>
                <li><a href="profile.php" class="active">PROFILE</a></li>
                <li><a href="course.php">COURSES</a></li>
                <li><a href="contact.php">CONTACT</a></li>
                <li><button class="logout-btn" onclick="logout()">LOGOUT</button></li>
            </ul>
        </div>
    </nav>


    <div class="container">
        <?php if(isset($_SESSION['success_message'])): ?>
            <div class="message success-message show" id="successMessage">
                <?= htmlspecialchars($_SESSION['success_message']) ?>
            </div>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>

        <?php if(isset($_SESSION['error_message'])): ?>
            <div class="message error-message show" id="errorMessage">
                <?= htmlspecialchars($_SESSION['error_message']) ?>
            </div>
            <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>

        <div class="profile-header">
            <div class="profile-avatar">
                <span id="avatarInitials"><?= $initials ?></span>
            </div>

            <div class="profile-info">
                <h1 id="fullName"><?= htmlspecialchars($fullName) ?></h1>
                <p><i class='bx bx-id_card'></i> Student ID: <?= htmlspecialchars($studentId) ?></p>
                <p><i class='bx bx-envelope'></i> <span id="headerEmail"><?= htmlspecialchars($email) ?></span></p>
                <span class="status-badge">Active Student</span>
            </div>
        </div>

        <div class="profile-content">

            <div class="profile-section">
                <div class="section-header">
                    <h2 class="section-title">
                        <i class='bx bx-user'></i>
                        Personal Information
                    </h2>
                    <button class="edit-btn" onclick="toggleEdit('personal')">
                        <i class='bx bx-edit'></i>
                        Edit
                    </button>
                </div>

                <div id="personalDisplay" class="detail-display">
                    <div class="detail-item">
                        <span class="detail-label">Full Name</span>
                        <span class="detail-value <?= empty($fullName) || $fullName === 'Your Name' ? 'empty-value' : '' ?>">
                            <?= empty($fullName) || $fullName === 'Your Name' ? 'Not provided' : htmlspecialchars($fullName) ?>
                        </span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Email</span>
                        <span class="detail-value"><?= htmlspecialchars($email) ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Phone</span>
                        <span class="detail-value <?= empty($phone) ? 'empty-value' : '' ?>">
                            <?= empty($phone) ? 'Not provided' : htmlspecialchars($phone) ?>
                        </span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Date of Birth</span>
                        <span class="detail-value <?= empty($dob) ? 'empty-value' : '' ?>">
                            <?= empty($dob) ? 'Not provided' : date('F j, Y', strtotime($dob))?>
                        </span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Address</span>
                        <span class="detail-value <?= empty($address) ? 'empty-value' : '' ?>">
                            <?= empty($address) ? 'Not provided' : htmlspecialchars($address) ?>
                        </span>
                    </div>
                </div>

                <div id="personalEdit" class="edit-form">
                    <form method="POST" action="profile.php">
                        <input type="hidden" name="action" value="update_personal">

                        <div class="form-group">
                            <label for="full_name">Full Name</label>
                            <input type="text" id="full_name" name="full_name" value="<?= htmlspecialchars($fullName === 'Your Name' ? '' : $fullName) ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" value="<?= htmlspecialchars($email) ?>" required>
                        </div>

                        <div class="form-group">
                            <label for="phone">Phone</label>
                            <input type="text" id="phone" name="phone" value="<?= htmlspecialchars($phone) ?>">
                        </div>

                        <div class="form-group">
                            <label for="dob">Date of Birth</label>
                            <input type="date" id="dob" name="dob" value="<?= htmlspecialchars($dob) ?>">
                        </div>

                        <div class="form-group">
                            <label for="address">Address</label>
                            <input type="text" id="address" name="address" value="<?= htmlspecialchars($address) ?>">
                        </div>

                        <div class="form-actions">
                            <button type="button" class="cancel-btn" onclick="cancelEdit('personal')">Cancel</button>
                            <button type="submit" class="save-btn" >Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="profile-section">
                <div class="section-header">
                    <h2 class="section-title"><i class='bx bx-book'></i> Academic Information</h2>
                    <button class="edit-btn" onclick="toggleEdit('academic')"><i class='bx bx-edit'></i> Edit</button>
                </div>

                <div id="academicDisplay" class="details-display">
                    <div class="detail-item">
                        <span class="detail-label">Student ID</span>
                        <span class="details-value"><?= htmlspecialchars($studentId) ?></span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Program</span>
                        <span class="details-value <?= empty($program) ? 'empty-value' : '' ?>">
                            <?= empty($program) ? 'Not provided' : htmlspecialchars($program) ?>
                        </span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Year Level</span>
                        <span class="details-value <?= empty($year) ? 'empty-value' : '' ?>">
                            <?= empty($year) ? 'Not provided' : htmlspecialchars($year) ?>
                        </span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Enrollment Date</span>
                        <span class="details-value <?= empty($enrollment) ? 'empty-value' : '' ?>">
                            <?= empty($enrollment) ? 'Not provided' : date('F Y', strtotime($enrollment . '-01')) ?>
                        </span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Expected Graduation</span>
                        <span class="details-value <?= empty($graduation) ? 'empty-value' : '' ?>">
                            <?= empty($graduation) ? 'Not provided' : date('F Y', strtotime($graduation . '-01')) ?>
                        </span>
                    </div>
                </div>

                <div id="academicEdit" class="edit-form">
                    <form method="POST" action="profile.php">
                        <input type="hidden" name="action" value="update_academic">

                        <div class="form-group">
                            <label for="program">Program</label>
                            <select id="program" name="program">
                                <option value="" disabled selected>Select your program</option>
                                <option value="Computer Science" <?= $program === 'Computer Science' ? 'selected' : '' ?>>Computer Science</option>
                                <option value="Information Technology" <?= $program === 'Information Technology' ? 'selected' : '' ?>>Information Technology</option>
                                <option value="Software Engineering" <?= $program === 'Software Engineering' ? 'selected' : '' ?>>Software Engineering</option>
                                <option value="Data Science" <?= $program === 'Data Science' ? 'selected' : '' ?>>Data Science</option>
                                <option value="Cyber Security" <?= $program === 'Cyber Security' ? 'selected' : '' ?>>Cyber Security</option>
                                <option value="Web Development" <?= $program === 'Web Development' ? 'selected' : '' ?>>Web Development</option>
                                <option value="Mobile App Development" <?= $program === 'Mobile App Development' ? 'selected' : '' ?>>Mobile App Development</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="year_level">Year Level</label>
                            <select id="year_level" name="year_level">
                                <option value="" disabled selected>Select your year level</option>
                                <option value="1st Year" <?= $year === '1st Year' ? 'selected' : '' ?>>1st Year</option>
                                <option value="2nd Year" <?= $year === '2nd Year' ? 'selected' : '' ?>>2nd Year</option>
                                <option value="3rd Year" <?= $year === '3rd Year' ? 'selected' : '' ?>>3rd Year</option>
                                <option value="4th Year" <?= $year === '4th Year' ? 'selected' : '' ?>>4th Year</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="enrollment_date">Enrollment Date</label>
                            <input type="month" id="enrollment_date" name="enrollment_date" value="<?= htmlspecialchars($enrollment) ?>">
                        </div>
                        <div class="form-group">
                            <label for="graduation_date">Expected Graduation Date</label>
                            <input type="month" id="graduation_date" name="graduation_date" value="<?= htmlspecialchars($graduation) ?>">
                        </div>
                        <div class="form-actions">
                            <button type="button" class="cancel-btn" onclick="cancelEdit('academic')">Cancel</button>
                            <button type="submit" class="save-btn">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <script>
        function toggleEdit(section) {
            const display = document.getElementById(section + 'Display');
            const edit = document.getElementById(section + 'Edit');
            display.style.display = 'none';
            edit.classList.add('active');
        }

        function cancelEdit(section) {
            const display = document.getElementById(section + 'Display');
            const edit = document.getElementById(section + 'Edit');
            display.style.display = 'block';
            edit.classList.remove('active');
        }

        function logout() {
            if(confirm("Are you sure you want to logout?")) {
                window.location.href = "logout.php";
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const message = document.querySelectorAll('.message.show');
            message.forEach(message => {
                setTimeout(() => {
                    message.style.display = 'none';
                }, 5000);
            });
        });
    </script>

</body>
</html>
