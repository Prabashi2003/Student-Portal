<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Courses - Student Portal</title>
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="course.css">
</head>
<body>
    <nav>
        <a href="index.html"><img src="images/logo.png" alt="Logo"></a>
        <div class="nav-links">
            <ul>
                <li><a href="profile.php">PROFILE</a></li>
                <li><a href="course.php" class="active">COURSES</a></li>
                <li><a href="contact.php">CONTACT</a></li>
                <li><button class="logout-btn" onclick="logout()">LOGOUT</button></li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <div class="page-header">
            <h1><i class='bx bx-book-alt'></i> Available Courses</h1>
            <p>Explore our comprehensive range of courses designed to enhance your learning journey.</p>
        </div>

        <div class="search-container">
            <div class="search-wrapper">
                <i class='bx bx-search search-icon'></i>
                <input type="text" class="search-input" placeholder="Search courses by name..." id="courseSearch">
                <button class="clear-search" id="clearSearch" onclick="clearSearch()">
                    <i class='bx bx-x'></i>
                </button>
            </div>
        </div>

        <div class="results-counter hidden" id="resultsCounter">
            Showing <span id="resultCount">0</span> course(s)
        </div>

        <div class="no-results" id="noResults">
            <i class='bx bx-search-alt'></i>
            <p>No courses found matching your search.</p>
            <small>Try searching with different keywords.</small>
        </div>

        <div class="courses-grid" id="coursesGrid">
            <div class="course-card" data-course="Computer Science">
                <div class="course-image">
                    <i class='bx bx-code-alt'></i>
                </div>
                <div class="course-info">
                    <h3>Computer Science</h3>
                    <p>Fundamentals of Programming</p>
                </div>
            </div>

            <div class="course-card" data-course="Data Structures">
                <div class="course-image">
                    <i class='bx bx-data'></i>
                </div>
                <div class="course-info">
                    <h3>Data Structures</h3>
                    <p>Advanced Data Management</p>
                </div>
            </div>

            <div class="course-card" data-course="Algorithms">
                <div class="course-image">
                    <i class='bx bx-network-chart'></i>
                </div>
                <div class="course-info">
                    <h3>Algorithms</h3>
                    <p>Problem Solving Techniques</p>
                </div>
            </div>

            <div class="course-card" data-course="Web Development">
                <div class="course-image">
                    <i class='bx bx-desktop'></i>
                </div>
                <div class="course-info">
                    <h3>Web Development</h3>
                    <p>Modern Web Technologies</p>
                </div>
            </div>

            <div class="course-card" data-course="Mobile Development">
                <div class="course-image">
                    <i class='bx bx-mobile-alt'></i>
                </div>
                <div class="course-info">
                    <h3>Mobile Development</h3>
                    <p>iOS & Android Applications</p>
                </div>
            </div>

            <div class="course-card" data-course="Machine Learning">
                <div class="course-image">
                    <i class='bx bx-brain'></i>
                </div>
                <div class="course-info">
                    <h3>Machine Learning</h3>
                    <p>AI & Neural Networks</p>
                </div>
            </div>

            <div class="course-card" data-course="Calculus">
                <div class="course-image">
                    <i class='bx bx-math'></i>
                </div>
                <div class="course-info">
                    <h3>Calculus</h3>
                    <p>Advanced Mathematics</p>
                </div>
            </div>

            <div class="course-card" data-course="Statistics">
                <div class="course-image">
                    <i class='bx bx-stats'></i>
                </div>
                <div class="course-info">
                    <h3>Statistics</h3>
                    <p>Data Analysis & Probability</p>
                </div>
            </div>

            <div class="course-card" data-course="Chemistry">
                <div class="course-image">
                    <i class='bx bx-test-tube'></i>
                </div>
                <div class="course-info">
                    <h3>Chemistry</h3>
                    <p>Organic & Inorganic Chemistry</p>
                </div>
            </div>

            <div class="course-card" data-course="Physics">
                <div class="course-image">
                    <i class='bx bx-atom'></i>
                </div>
                <div class="course-info">
                    <h3>Physics</h3>
                    <p>Classical & Modern Physics</p>
                </div>
            </div>

            <div class="course-card" data-course="Business Management">
                <div class="course-image">
                    <i class='bx bx-briefcase'></i>
                </div>
                <div class="course-info">
                    <h3>Business Management</h3>
                    <p>Leadership & Strategy</p>
                </div>
            </div>

            <div class="course-card" data-course="Economics">
                <div class="course-image">
                    <i class='bx bx-line-chart'></i>
                </div>
                <div class="course-info">
                    <h3>Economics</h3>
                    <p>Micro & Macroeconomics</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        function logout() {
            if (confirm('Are you sure you want to logout?')) {
                window.location.href = 'logout.php';
            }
        }

        const searchInput = document.getElementById('courseSearch');
        const clearBtn = document.getElementById('clearSearch');
        const coursesGrid = document.getElementById('coursesGrid');
        const courseCards = document.querySelectorAll('.course-card');
        const resultsCounter = document.getElementById('resultsCounter');
        const resultCount = document.getElementById('resultCount');
        const noResults = document.getElementById('noResults');

        function searchCourses() {
            const searchTerm = searchInput.value.toLowerCase().trim();
            let visibleCount = 0;
            
            courseCards.forEach(card => {
                const courseName = card.getAttribute('data-course').toLowerCase();
                const courseTitle = card.querySelector('h3').textContent.toLowerCase();
                const courseDescription = card.querySelector('p').textContent.toLowerCase();
                
                const matches = courseName.includes(searchTerm) || 
                               courseTitle.includes(searchTerm) || 
                               courseDescription.includes(searchTerm);
                
                if (matches || searchTerm === '') {
                    card.classList.remove('hidden');
                    card.style.display = 'block';
                    visibleCount++;
                    
                    if (searchTerm !== '' && matches) {
                        card.classList.add('highlight');
                    } else {
                        card.classList.remove('highlight');
                    }
                } else {
                    card.classList.add('hidden');
                    card.style.display = 'none';
                }
            });
            
            if (searchTerm === '') {
                resultsCounter.classList.add('hidden');
                noResults.style.display = 'none';
            } else {
                resultsCounter.classList.remove('hidden');
                resultCount.textContent = visibleCount;
                
                if (visibleCount === 0) {
                    noResults.style.display = 'block';
                } else {
                    noResults.style.display = 'none';
                }
            }
            
            if (searchTerm.length > 0) {
                clearBtn.classList.add('show');
            } else {
                clearBtn.classList.remove('show');
            }
        }

        function clearSearch() {
            searchInput.value = '';
            searchCourses();
            searchInput.focus();
        }

        searchInput.addEventListener('input', searchCourses);
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                searchCourses();
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            courseCards.forEach(card => {
                card.addEventListener('mouseenter', function() {
                    if (!this.classList.contains('highlight')) {
                        this.style.transform = 'translateY(-10px) scale(1.02)';
                    }
                });
                
                card.addEventListener('mouseleave', function() {
                    if (!this.classList.contains('highlight')) {
                        this.style.transform = 'translateY(0) scale(1)';
                    }
                });
            });
        });

        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
                e.preventDefault();
                searchInput.focus();
            }
        });
    </script>
</body>
</html>