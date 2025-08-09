<?php
    session_start();
    require 'db.php';

    if (!isset($_SESSION['user_id'])) {
        header('Location: login.html?form=login&error=Please+login+first');
        exit();
    }

    $userId = $_SESSION['user_id'];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = $_POST['name'] ?? '';
        $email = $_POST['email'] ?? '';
        $phone = $_POST['phone'] ?? '';
        $subject = $_POST['subject'] ?? '';
        $message = $_POST['message'] ?? '';
        $rating = $_POST['rating'] ?? 0;
        $category = $_POST['category'] ?? '';
        $priority = $_POST['priority'] ?? 'medium';
        
        if (empty($name) || empty($email) || empty($subject) || empty($message)) {
            $_SESSION['error_message'] = 'Please fill in all required fields.';
        } else {
            $stmt = $mysql->prepare("INSERT INTO contact_submissions (user_id, name, email, phone, subject, message, rating, category, priority) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("isssssiss", $userId, $name, $email, $phone, $subject, $message, $rating, $category, $priority);
            
            if ($stmt->execute()) {
                $_SESSION['success_message'] = 'Your message has been sent successfully! We will get back to you soon.';
                header('Location: contact.php');
                exit();
            } else {
                $_SESSION['error_message'] = 'Error sending your message. Please try again.';
            }
        }
    }

    $stmt = $mysql->prepare("SELECT users.username, users.email, user_info.full_name, user_info.phone FROM users LEFT JOIN user_info ON users.id = user_info.user_id WHERE users.id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    $userName = $user['full_name'] ?? $user['username'];
    $userEmail = $user['email'];
    $userPhone = $user['phone'] ?? '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Student Portal</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="contact.css">
</head>
<body>
    <nav>
        <a href="index.html"><img src="images/logo.png" alt="Logo"></a>
        <div class="nav-links">
            <ul>
                <li><a href="profile.php">PROFILE</a></li>
                <li><a href="course.php">COURSES</a></li>
                <li><a href="contact.php" class="active">CONTACT</a></li>
                <li><button class="logout-btn" onclick="logout()">LOGOUT</button></li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="message success-message show" id="successMessage">
                <i class='bx bx-check-circle'></i>
                <?= htmlspecialchars($_SESSION['success_message']) ?>
            </div>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="message error-message show" id="errorMessage">
                <i class='bx bx-error-circle'></i>
                <?= htmlspecialchars($_SESSION['error_message']) ?>
            </div>
            <?php unset($_SESSION['error_message']); ?>
        <?php endif; ?>

        <div class="contact-header">
            <h1><i class='bx bx-envelope'></i> Contact Us</h1>
            <p>We'd love to hear from you! Send us a message and we'll respond as soon as possible.</p>
        </div>

        <div class="contact-content">
            <div class="contact-form-section">
                <div class="form-container">
                    <h2><i class='bx bx-message-dots'></i> Send us a Message</h2>
                    
                    <form method="POST" action="contact.php" id="contactForm">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="name">Full Name <span class="required">*</span></label>
                                <input type="text" id="name" name="name" value="<?= htmlspecialchars($userName) ?>" required readonly>
                            </div>
                            
                            <div class="form-group">
                                <label for="email">Email Address <span class="required">*</span></label>
                                <input type="email" id="email" name="email" value="<?= htmlspecialchars($userEmail) ?>" required readonly>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="phone">Phone Number</label>
                                <input type="tel" id="phone" name="phone" value="<?= htmlspecialchars($userPhone) ?>" readonly>
                            </div>
                            
                            <div class="form-group">
                                <label for="category">Category</label>
                                <select id="category" name="category">
                                    <option value="">Select Category</option>
                                    <option value="academic">Academic Support</option>
                                    <option value="technical">Technical Issue</option>
                                    <option value="billing">Billing/Payment</option>
                                    <option value="general">General Inquiry</option>
                                    <option value="complaint">Complaint</option>
                                    <option value="suggestion">Suggestion</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="priority">Priority Level</label>
                                <select id="priority" name="priority">
                                    <option value="low">Low</option>
                                    <option value="medium" selected>Medium</option>
                                    <option value="high">High</option>
                                    <option value="urgent">Urgent</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="subject">Subject <span class="required">*</span></label>
                                <input type="text" id="subject" name="subject" placeholder="Brief description of your inquiry" required>
                            </div>
                        </div>

                        <div class="rating-section">
                            <label>How would you rate our service so far?</label>
                            <div class="rating-container">
                                <div class="star-rating">
                                    <input type="radio" id="star5" name="rating" value="5">
                                    <label for="star5" title="Excellent"><i class='bx bx-star'></i></label>
                                    
                                    <input type="radio" id="star4" name="rating" value="4">
                                    <label for="star4" title="Very Good"><i class='bx bx-star'></i></label>
                                    
                                    <input type="radio" id="star3" name="rating" value="3">
                                    <label for="star3" title="Good"><i class='bx bx-star'></i></label>
                                    
                                    <input type="radio" id="star2" name="rating" value="2">
                                    <label for="star2" title="Fair"><i class='bx bx-star'></i></label>
                                    
                                    <input type="radio" id="star1" name="rating" value="1">
                                    <label for="star1" title="Poor"><i class='bx bx-star'></i></label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="message">Message <span class="required">*</span></label>
                            <textarea id="message" name="message" rows="6" placeholder="Please provide detailed information about your inquiry or issue..." required></textarea>
                            <div class="character-count">
                                <span id="charCount">0</span>/1000 characters
                            </div>
                        </div>

                        

                        <div class="form-actions">
                            <button type="reset" class="reset-btn">
                                <i class='bx bx-refresh'></i>
                                Reset Form
                            </button>
                            <button type="submit" class="submit-btn">
                                <i class='bx bx-send'></i>
                                Send Message
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="contact-info-section">
                <div class="contact-card">
                    <h3><i class='bx bx-info-circle'></i> Contact Information</h3>
                    
                    <div class="contact-item">
                        <i class='bx bx-envelope'></i>
                        <div>
                            <h4>Email</h4>
                            <p>support@studentportal.edu</p>
                        </div>
                    </div>

                    <div class="contact-item">
                        <i class='bx bx-phone'></i>
                        <div>
                            <h4>Phone</h4>
                            <p>+94 112 556 556</p>
                        </div>
                    </div>

                    <div class="contact-item">
                        <i class='bx bx-map'></i>
                        <div>
                            <h4>Address</h4>
                            <p>123 University Avenue<br>Colombo 04</p>
                        </div>
                    </div>

                    <div class="contact-item">
                        <i class='bx bx-time'></i>
                        <div>
                            <h4>Office Hours</h4>
                            <p>Monday - Friday: 8:00 AM - 6:00 PM<br>
                            Saturday: 9:00 AM - 4:00 PM<br>
                            Sunday: Closed</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        const messageTextarea = document.getElementById('message');
        const charCount = document.getElementById('charCount');
        
        messageTextarea.addEventListener('input', function() {
            const currentLength = this.value.length;
            charCount.textContent = currentLength;
            
            if (currentLength > 1000) {
                charCount.style.color = '#ff4757';
            } else {
                charCount.style.color = '#666';
            }
        });

        const starRating = document.querySelector('.star-rating');
        const stars = starRating.querySelectorAll('input[type="radio"]');
        const starLabels = starRating.querySelectorAll('label');

        starLabels.forEach((label, index) => {
            label.addEventListener('mouseenter', function() {
                highlightStars(index + 1);
            });
            
            label.addEventListener('mouseleave', function() {
                const checkedStar = starRating.querySelector('input[type="radio"]:checked');
                if (checkedStar) {
                    highlightStars(parseInt(checkedStar.value));
                } else {
                    highlightStars(0);
                }
            });
            
            label.addEventListener('click', function() {
                const ratingValue = parseInt(this.getAttribute('for').replace('star', ''));
                highlightStars(ratingValue);
            });
        });

        function highlightStars(rating) {
            starLabels.forEach((label, index) => {
                if (index < rating) {
                    label.classList.add('active');
                } else {
                    label.classList.remove('active');
                }
            });
        }

        document.getElementById('contactForm').addEventListener('submit', function(e) {
            const name = document.getElementById('name').value.trim();
            const email = document.getElementById('email').value.trim();
            const subject = document.getElementById('subject').value.trim();
            const message = document.getElementById('message').value.trim();
            
            if (!name || !email || !subject || !message) {
                e.preventDefault();
                alert('Please fill in all required fields.');
                return false;
            }
            
            if (message.length > 1000) {
                e.preventDefault();
                alert('Message cannot exceed 1000 characters.');
                return false;
            }
        });

        function logout() {
            if (confirm('Are you sure you want to logout?')) {
                window.location.href = 'logout.php';
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const messages = document.querySelectorAll('.message.show');
            messages.forEach(message => {
                setTimeout(() => {
                    message.style.display = 'none';
                }, 5000);
            });
        });
    </script>
</body>
</html>