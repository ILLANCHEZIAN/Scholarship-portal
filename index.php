<?php include 'includes/header.php'; ?>
<style>
.body{
    margin: 0%;
    padding: 0%;
}

.announcement-bar {
    background-color: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 5px;
    overflow: hidden;
    padding: 10px 0;
}

.marquee {
    width: 100%;
    overflow: hidden;
    white-space: nowrap;
    position: relative;
    cursor:pointer;
}

.marquee-content {
    font-weight: bold;
    display: inline-block;
    padding-left: 100%;
    animation: marquee 25s linear infinite;
}

.marquee-content:hover {
    animation-play-state: paused;
}

@keyframes marquee {
    0% {
        transform: translateX(0);
    }
    100% {
        transform: translateX(-100%);
    }
}

/* Badge styles for announcements */
.announcement-bar .badge {
    font-size: 0.8rem;
    padding: 0.25em 0.6em;
}

/*Information view of how many of them joining*/
.Information {
  background:rgb(255, 255, 255);
  border: 3px solid #d8d8d8;
  border-radius: 20px;
  margin: 0 auto;
  width: 97%;
  height: 120px;
}

.row {
  text-align: center !important;
  display: flex;
  flex-wrap: nowrap;
  font-weight: bolder;
  font-size: larger;
}

.Nu{
  color: rgb(0, 177, 247);
  font-size:x-large;
  font-weight: bolder;
  width: 320px;
}

/*Content*/
.scholarship-illan {
  display: flex;
  align-items: center;
  justify-content: space-between;
  max-width: 1220px;
  margin: 50px auto;
  padding: 20px;
  background-color: #f5eded;
  border-radius: 10px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
  transition: transform 0.3s ease, backgroundColor 0.3s ease;
}

.scholarship-illan:hover{
  transform: scale(1.1);
  background-color: #f5c6c6;
  color: black;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.scholarship-content {
  flex: 1;
  padding-right: 70px;
}

.scholarship-content h2 {
  font-size: 30px;
  color: #333;
  margin-bottom: 15px;
}

.scholarship-content p {
  font-size: 20px;
  color: #504f4f;
  line-height: 1.6;
  margin-bottom: 20px;
}

.scholarship-image {
  flex: 1;
  text-align: right;
  border-radius: 6px;
  border: 2px solid #d8d8d8;
}

/* Scholarship Section - Advanced Styling */
.scholarship-illan1 {
    background: linear-gradient(135deg, #f5f7fa 0%, #e4e8ed 100%);
    padding: 3rem 2rem;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    margin: 2rem 0;
    position: relative;
    overflow: hidden;
}

.scholarship-illan1::before {
    content: '';
    position: absolute;
    top: -50px;
    right: -50px;
    width: 200px;
    height: 200px;
    background: rgba(66, 165, 245, 0.1);
    border-radius: 50%;
    z-index: 0;
}

.scholarship-content1 {
    position: relative;
    z-index: 1;
}

.scholarship-content1 h2 {
    font-size: 2.5rem;
    color: #2c3e50;
    margin-bottom: 1.5rem;
    font-weight: 700;
    position: relative;
    display: inline-block;
}

.scholarship-content1 h2::after {
    content: '';
    position: absolute;
    bottom: -10px;
    left: 0;
    width: 60px;
    height: 4px;
    background: #42a5f5;
    border-radius: 2px;
}

.scholarship-content1 p {
    font-size: 1.1rem;
    line-height: 1.6;
    color: #546e7a;
    max-width: 800px;
}

.scholarship-content1 h3{
        text-shadow: 
        2px 2px 0px rgba(0,0,0,0.1),
        4px 4px 0px rgba(0,0,0,0.05);
}

/* Scholarship Cards */
.scholarship-types .card {
    border: none;
    border-radius: 12px;
    transition: all 0.3s ease;
    background: white;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
    overflow: hidden;
}

.scholarship-types .card:hover {
    
    transform: translateY(-10px);
    box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
}

.scholarship-types .card-body {
    padding: 1.5rem;
}

.scholarship-types .card-title {
    background: linear-gradient(135deg, #1a2a6c, #b21f1f, #fdbb2d);
    -webkit-background-clip: text;
    background-clip: text;
    color: transparent;
    display: inline-block;
    margin: 0.5em 0;
    padding: 0.2em 0;
    font-weight: 600;
    margin-bottom: 1rem;
    position: relative;
    padding-bottom: 0.5rem;
}

.scholarship-types .card-title::after {
    
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 40px;
    height: 3px;
    background: #42a5f5;
}

.scholarship-types .card-text {
    color: #546e7a;
    line-height: 1.7;
}

.scholarship-types .card-text strong {
    color: #2c3e50;
}

/* How to Apply Section */
.how-to-apply {
    background: white;
    padding: 2rem;
    border-radius: 12px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
}

.how-to-apply h3 {
    color: #2c3e50;
    margin-bottom: 1.5rem;
    font-weight: 600;
}

.how-to-apply ol {
    padding-left: 1.5rem;
    counter-reset: step-counter;
}

.how-to-apply ol li {
    position: relative;
    padding-left: 2.5rem;
    margin-bottom: 1rem;
    color: #546e7a;
    line-height: 1.6;
    list-style-type: none;
    font-weight: bold;
}

.how-to-apply ol li::before {
    counter-increment: step-counter;
    content: counter(step-counter);
    position: absolute;
    left: 0;
    top: 0;
    background: #42a5f5;
    color: white;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.8rem;
    font-weight: bold;
}

.alert-info {
    background-color: #e3f2fd;
    border-left: 4px solid #42a5f5;
    border-radius: 0 8px 8px 0;
}

/* Responsive Design */
@media (max-width: 992px) {
    .scholarship-illan1 {
        padding: 2rem 1rem;
    }
    
    .scholarship-content1 h2 {
        font-size: 2rem;
    }
}

@media (max-width: 768px) {
    .scholarship-types .col-md-4 {
        margin-bottom: 1.5rem;
    }
    
    .how-to-apply {
        padding: 1.5rem;
    }
}

/* Animations */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.scholarship-types .card {
    animation: fadeInUp 0.5s ease forwards;
    opacity: 0;
}

.scholarship-types .card:nth-child(1) {
    animation-delay: 0.1s;
}

.scholarship-types .card:nth-child(2) {
    animation-delay: 0.2s;
}

.scholarship-types .card:nth-child(3) {
    animation-delay: 0.3s;
}

/* Hover Effects */
.btn-primary, .btn-secondary, .btn-danger, .btn-success {
    transition: all 0.3s ease;
    transform: perspective(1px) translateZ(0);
}

.btn-primary:hover {
    transform: translateY(-3px) scale(1.02);
    box-shadow: 0 10px 20px rgba(66, 165, 245, 0.3);
}

.btn-secondary:hover {
    transform: translateY(-3px) scale(1.02);
    box-shadow: 0 10px 20px rgba(158, 158, 158, 0.3);
}

.btn-danger:hover {
    transform: translateY(-3px) scale(1.02);
    box-shadow: 0 10px 20px rgba(239, 83, 80, 0.3);
}

.btn-success:hover {
    transform: translateY(-3px) scale(1.02);
    box-shadow: 0 10px 20px rgba(102, 187, 106, 0.3);
}


    </style>
<body>
<!-- Marquee/Sliding Announcement Banner -->
<div class="announcement-bar mb-4">
        <div class="marquee">
            <div class="marquee-content">
                <span class="badge bg-danger me-3">New</span>
                <span>Scholarship applications for 2025 are now open! Deadline: December 31, 2025</span>
                <span class="px-4">|</span>
                <span class="badge bg-warning text-dark me-3">Important</span>
                <span>Upcoming maintenance: System will be unavailable on January 5 from 2-4 AM</span>
            </div>
        </div>
    </div>

 <!--image-->   
    <div style="width:100%; margin:0 auto; position:relative;">
    <div class="slide">
       <a href="user\Filter\Toefl Scl.html"> <img src="New2025.jpg"  style="max-width:100%; height:auto;"></a>
    </div>
    <div class="slide">
        <img src="ILLAN1.webp" style="max-width:100%; height:auto;">
    </div>
    <div class="slide">
        <img src="ILLAN2.webp" style="max-width:100%; height:auto;">
    </div>
</div>

<script>
let slideIndex = 0;
showSlides();

function showSlides() {
    const slides = document.getElementsByClassName("slide");
    
    for (let i = 0; i < slides.length; i++) {
        slides[i].style.display = "none";
    }
    
    slideIndex++;
    if (slideIndex > slides.length) slideIndex = 1;
    
    slides[slideIndex-1].style.display = "block";
    setTimeout(showSlides, 2000); // Change every 3 seconds
}
</script>

</div>
<center><h1 class="mt-5">Welcome to Scholarship Portal</h1></center>
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h3>For Students</h3>
                    <p>Apply for available scholarships</p>
                    <a href="login.php?type=user" class="btn btn-primary">User Login</a>
                    <a href="register.php" class="btn btn-secondary">Register</a>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h3>Administrators</h3>
                    <p>Manage scholarships and applications</p>
                    <a href="login.php?type=admin" class="btn btn-danger">Admin Login</a>
                </div>
            </div>
        </div>
    </div>
    <br>

        <!--Content-->
    <div class="scholarship-illan">
        <div class="scholarship-content">
            <h2>Scholarship Opportunities</h2>
            <p>
                We offer a variety of scholarships to help students achieve their academic goals. 
                Whether you're excelling in academics, sports, or community service, there's a scholarship for you.
                An "opportunity scholarship" is a financial award or grant provided to students to support their education, 
                often with the aim of making higher education more accessible, particularly for those facing financial hardship or attending underperforming schools. 
            </p>
        </div>
        <div class="scholarship-image">
            <img src="women1.jpg" alt="Scholarship Image" width="550px" height="350px">
        </div>
    </div>

    <!-- Scholarship Content -->
    <div class="scholarship-illan1">
        <div class="scholarship-content1">
            <center><h2>Scholarship Opportunities</h2><center>
            <p>
                We offer a variety of scholarships to help students achieve their academic goals. 
                Whether you're excelling in academics, sports, or community service, there's a scholarship for you.
            </p>
            
            <div class="scholarship-types mt-4">
                <h3>Available Scholarship Programs</h3>
                <div class="row">
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <h4 class="card-title">Academic Excellence</h4>
                                <p class="card-text">
                                    <strong>Amount:</strong> Up to $5,000 per year<br>
                                    <strong>Deadline:</strong> June 30, 2024<br>
                                    For students maintaining a 3.5 GPA or higher. Renewable for up to 4 years.
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <h4 class="card-title">STEM Leadership</h4>
                                <p class="card-text">
                                    <strong>Amount:</strong> $7,500 one-time<br>
                                    <strong>Deadline:</strong> May 15, 2024<br>
                                    For women and minorities pursuing degrees in STEM fields.
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <h4 class="card-title">Community Service</h4>
                                <p class="card-text">
                                    <strong>Amount:</strong> $2,000-$4,000<br>
                                    <strong>Deadline:</strong> Rolling applications<br>
                                    Recognizes students with 100+ hours of community service.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="how-to-apply mt-5">
                <h3>How to Apply</h3>
                <ol>
                    <li>Create your student account</li>
                    <li>Complete your personal profile</li>
                    <li>Upload required documents (transcripts, essays, recommendations)</li>
                    <li>Browse and select scholarships</li>
                    <li>Submit your applications before deadlines</li>
                </ol>
                <div class="alert alert-info">
                    <strong>Tip:</strong> Apply early! Some scholarships have limited funds and may close before the deadline.
                </div>
            </div>
        </div>
        
    </div>
 
    
    <!-- Call to Action -->
    <div class="text-center mt-4 mb-5">
        <a href="register.php" class="btn btn-success btn-lg">Start Your Application Today</a>
    </div>
<br>  
<?php include 'includes/footer.php'; ?>