<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us</title>
    <link rel="stylesheet" href="../styles/style.css">
    <link rel="icon" type="image/png" href="../assets/logo/logo.png">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet"> <!-- Boxicons -->
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
            margin: 0;
            padding: 0;
        }
        h1 {
            color: #34AFEA;
            text-shadow: 1px 1px 1px rgba(0, 0, 0, 1);
        }
        .logo {
            margin-top: 100px;
        }
        .logo img {
            width: 300px;
        }
        .app-description {
            text-align: center;
            margin: 20px 0;
        }
        .slider-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
            max-width: 900px;
            position: relative;
            color: #fff;
        }
        .slider {
            border-radius: 10px;
            background-color: #1D242B;
            width: 70%;
            overflow: hidden;
            padding: 30px 10px;
        }
        .slides {
            display: flex;
            transition: transform 0.5s ease-in-out;
        }
        .slide {
            min-width: 100%;
            box-sizing: border-box;
            padding: 20px;
            text-align: center;
        }
        .nav {
            position: absolute;
            top: 50%;
            display: flex;
            justify-content: space-between;
            width: 100%;
            transform: translateY(-50%);
            pointer-events: none;
        }
        .nav button {
            background: rgba(0, 0, 0, 0.5); /* Semi-transparent black background */
            border: none;
            font-size: 2rem;
            cursor: pointer;
            outline: none;
            pointer-events: all;
            border-radius: 50%; /* Rounded corners */
            padding: 10px;
            color: white; /* White arrow color */
        }
        .nav .prev {
            position: absolute;
            left: 20px;
        }
        .nav .next {
            position: absolute;
            right: 20px;
        }
        .nav button:hover {
            background: rgba(0, 0, 0, 0.8); /* Darker background on hover */
        }
        .indicators {
            display: flex;
            justify-content: center;
            margin-top: 10px;
        }
        .indicator {
            width: 10px;
            height: 10px;
            background-color: #bbb;
            border-radius: 50%;
            margin: 0 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .indicator.active {
            background-color: #717171;
        }
        .dev-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            text-align: left;
        }
        .dev-about {
            flex: 1;
        }
        .dev-img {
            flex: 1;
            display: flex;
            justify-content: right;
        }
        .dev-img img {
            width: 200px;
            height: 200px;
            border-radius: 50%; /* Membuat gambar menjadi lingkaran */
            object-fit: cover; /* Untuk memastikan gambar tidak terdistorsi */
        }
        .connect {
            display: flex;
            justify-content: flex-start;
            margin-top: 10px;
        }
        .connect div {
            margin-right: 15px;
        }
        .connect img {
            width: 50px;
            height: 50px;
        }
        .connect i {
            font-size: 30px;    
            color: #007bff;
        }
        .connect i:hover {
            color: #fefefe;
        }

        @media (max-width: 1000px) {
            .nav {
                position: static;
                flex-direction: row;
                justify-content: center;
                margin-top: 10px;
                transform: translateY(0);
            }
            .nav .prev, .nav .next {
                position: static;
                font-size: 1.5rem;
                margin: 0 10px;
            }
        }
        .contact-section {
            width: 100%;
            max-width: 900px;
            padding: 20px;
            text-align: center;
        }
        .contact-section h2 {
            margin-bottom: 20px;
        }
        .contact-icons {
            display: flex;
            justify-content: center;
            gap: 20px;
        }
        .contact-icons a {
            color: #000;
            font-size: 40px;
        }
        .contact-icons a:hover {
            color: #007bff;
        }
        .app-description p {
            max-width: 800px;
        }
    </style>
</head>
<body>
    <!-- BAGIAN 1 -->
    <!-- Logo -->
    <div class="logo">
        <img src="../assets/logo/logo-transparent.png" alt="App Logo">
    </div>

    <!-- Keterangan Aplikasi -->
    <div class="app-description">
        <h1>MIAWSHARE</h1>
        <p>MiawShare adalah platform komunitas online untuk berbagi karya, ide kreatif, dan gambar menarik. Dengan platform kami yang ramah pengguna, anda dapat dengan mudah menjelajahi berbagai ilustrasi oleh pengguna kami. Kami membangun MiawShare dengan tujuan untuk menciptakan komunitas yang inklusif dan mendukung, di mana setiap orang dapat merasa diterima dan dihargai. Kami menghargai keragaman dalam segala bentuknya dan berkomitmen untuk menciptakan lingkungan yang aman bagi semua orang untuk berekspresi dan berbagi.</p>
    </div>
        
    <!-- BAGIAN 2 -->
    <!-- Developer Slider -->
    <h2>Our Team</h2>
    <div class="slider-container">
        <div class="slider">
            <div class="slides">
                <!-- Clone of the last slide (for seamless transition) -->
                <div class="slide">
                    <div class="dev-content">
                        <div class="dev-about">
                            <h2>Ryan Manganguwi</h2>
                            <div class="position">
                                <h3>Design & Frontend</h3>
                            </div>
                            <div class="connect">
                                <div class="logo-instagram">
                                    <a href="https://www.instagram.com/enokki43at/"><i class='bx bxl-instagram'></i></a>
                                </div>
                                <div class="logo-linkedin">
                                    <a href="https://www.linkedin.com/in/ryan-manganguwi-b4574a312"><i class='bx bxl-linkedin'></i></a>
                                </div>
                                <div class="logo-github">
                                    <a href="https://github.com/RyanManganguwi"><i class='bx bxl-github'></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="dev-img">
                            <img src="../assets/img/ryan.jpg" alt="Foto Ryan">
                        </div>
                    </div>
                </div>
                <!-- Developer 1 -->
                <div class="slide">
                    <div class="dev-content">
                        <div class="dev-about">
                            <h2>Yefta Yosia Asyel</h2>
                            <div class="position">
                                <h3>Backend</h3>
                            </div>
                            <div class="connect">
                                <div class="logo-instagram">
                                    <a href="https://www.instagram.com/yeftaasyel_/"><i class='bx bxl-instagram'></i></a>
                                </div>
                                <div class="logo-linkedin">
                                    <a href="https://www.linkedin.com/in/yefta-yosia-asyel"><i class='bx bxl-linkedin'></i></a>
                                </div>
                                <div class="logo-github">
                                    <a href="https://github.com/yeftakun"><i class='bx bxl-github'></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="dev-img">
                            <img src="../assets/img/yefta.jpg" alt="Foto Yefta">
                        </div>
                    </div>
                </div>
                <!-- Developer 2 -->
                <div class="slide">
                    <div class="dev-content">
                        <div class="dev-about">
                            <h2>Andro Lay</h2>
                            <div class="position">
                                <h3>Frontend</h3>
                            </div>
                            <div class="connect">
                                <div class="logo-instagram">
                                    <a href="https://www.instagram.com/andro.lay/"><i class='bx bxl-instagram'></i></a>
                                </div>
                                <div class="logo-linkedin">
                                    <a href="https://www.linkedin.com/in"><i class='bx bxl-linkedin'></i></a>
                                </div>
                                <div class="logo-github">
                                    <a href="https://github.com/androlay"><i class='bx bxl-github'></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="dev-img">
                            <img src="../assets/img/andro.jpg" alt="Foto Andro">
                        </div>
                    </div>
                </div>
                <!-- Developer 3 -->
                <div class="slide">
                    <div class="dev-content">
                        <div class="dev-about">
                            <h2>Aulia Nurhaliza</h2>
                            <div class="position">
                                <h3>UI/UX</h3>
                            </div>
                            <div class="connect">
                                <div class="logo-instagram">
                                    <a href="https://www.instagram.com/auliaa.ajaaa/"><i class='bx bxl-instagram'></i></a>
                                </div>
                                <div class="logo-linkedin">
                                    <a href="https://www.linkedin.com/in"><i class='bx bxl-linkedin'></i></a>
                                </div>
                                <div class="logo-github">
                                    <a href="https://github.com/aulianurhaliza"><i class='bx bxl-github'></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="dev-img">
                            <img src="../assets/img/aulia.jpg" alt="Foto Aulia">
                        </div>
                    </div>
                </div>
                <!-- Developer 4 -->
                <div class="slide">
                    <div class="dev-content">
                        <div class="dev-about">
                            <h2>Ryan Manganguwi</h2>
                            <div class="position">
                                <h3>Design & Frontend</h3>
                            </div>
                            <div class="connect">
                                <div class="logo-instagram">
                                    <a href="https://www.instagram.com/enokki43at/"><i class='bx bxl-instagram'></i></a>
                                </div>
                                <div class="logo-linkedin">
                                    <a href="https://www.linkedin.com/in/ryan-manganguwi-b4574a312"><i class='bx bxl-linkedin'></i></a>
                                </div>
                                <div class="logo-github">
                                    <a href="https://github.com/RyanManganguwi"><i class='bx bxl-github'></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="dev-img">
                            <img src="../assets/img/ryan.jpg" alt="Foto Ryan">
                        </div>
                    </div>
                </div>
                <!-- Clone of the first slide (for seamless transition) -->
                <div class="slide">
                    <div class="dev-content">
                        <div class="dev-about">
                            <h2>Yefta Yosia Asyel</h2>
                            <div class="position">
                                <h3>Backend</h3>
                            </div>
                            <div class="connect">
                                <div class="logo-instagram">
                                    <a href="https://www.instagram.com/yeftaasyel_/"><i class='bx bxl-instagram'></i></a>
                                </div>
                                <div class="logo-linkedin">
                                    <a href="https://www.linkedin.com/in/yefta-yosia-asyel"><i class='bx bxl-linkedin'></i></a>
                                </div>
                                <div class="logo-github">
                                    <a href="https://github.com/yeftakun"><i class='bx bxl-github'></i></a>
                                </div>
                            </div>
                        </div>
                        <div class="dev-img">
                            <img src="../assets/img/yefta.jpg" alt="Foto Yefta">
                        </div>
                    </div>
                </div>
            <!-- Tambahkan slide lain sesuai kebutuhan -->
            </div>
        </div>
        <div class="nav">
            <button class="prev">&lt;</button>
            <button class="next">&gt;</button>
        </div>
        <div class="indicators">
            <!-- Indikator akan diisi secara dinamis melalui JavaScript -->
        </div>
    </div>

    <!-- BAGIAN 3 -->
    <!-- Contact Section -->
    <div class="contact-section">
        <h2>Contact Us</h2>
        <div class="contact-icons">
            <a href="mailto:your-email@example.com"><i class='bx bx-envelope'></i></a>
            <a href="https://www.instagram.com/yourprofile"><i class='bx bxl-instagram'></i></a>
            <a href="https://www.linkedin.com/in/yourprofile"><i class='bx bxl-linkedin'></i></a>
            <a href="https://github.com/yourprofile"><i class='bx bxl-github'></i></a>
        </div>
    </div>

    <!-- Sidebar -->
    <nav class="sidebar locked">
            <div class="logo_items flex">
            <span class="nav_image">
                <img src="../assets/logo/logo.png" alt="logo_img" />
            </span>
            <span class="logo_name">MiawShare</span>
            <i class="bx bx-lock-alt" id="lock-icon" title="Unlock Sidebar"></i>
            </div>

            <div class="menu_container">
            <div class="menu_items">
                <ul class="menu_item">
                    <div class="menu_title flex">
                    <span class="title">Dashboard</span>
                    <span class="line"></span>
                    </div>
                    <li class="item">
                        <a href="beranda.php" class="link flex">
                    <i class="bx bx-home-alt"></i>
                    <span>Beranda</span>
                    </a>
                </li>
                <li class="item">
                    <a href="aboutus.php" class="link flex">
                    <i class="bx bx-flag"></i>
                    <span>About Us</span>
                    </a>
                </li>
                </ul>
                </div>
            </div>
        </nav>
        <!-- Navbar -->
        <nav class="navbar flex">
            <i class="bx bx-menu" id="sidebar-open"></i>
            <form action="search_result.php" method="GET" class="search_form">
                <input type="text" class="search_box" name="search" placeholder="Judul / #tag / username" id="searchInput"/>
                <input type="submit" value="Search" class="search_button">
            </form>
            
            <span class="nav_image">
            <a href="<?php
            if(isset($_SESSION['level_id'])){
                echo "profile.php?user_name=", $_SESSION['user_name'];
                }else{
                echo "#";
            }
            ?>">
                <img src="<?php
                if(isset($_SESSION['level_id'])){
                    echo '../storage/profile/' . $_SESSION['user_profile_path'];
                }else{
                    echo '../storage/profile/default.png';
                    }
                    ?>" alt="logo_img" />
            </a>
            </span>
        </nav>

    <script>
        let currentIndex = 1;
        const slides = document.querySelector('.slides');
        const totalSlides = document.querySelectorAll('.slide').length;
        const indicatorsContainer = document.querySelector('.indicators');
        const slideWidth = document.querySelector('.slide').offsetWidth;
        
        slides.style.transform = `translateX(-${slideWidth}px)`;

        // Create indicators dynamically based on total slides
        for (let i = 0; i < totalSlides - 2; i++) {
            const indicator = document.createElement('div');
            indicator.classList.add('indicator');
            if (i === 0) indicator.classList.add('active');
            indicator.addEventListener('click', () => {
                currentIndex = i + 1;
                updateSlider();
            });
            indicatorsContainer.appendChild(indicator);
        }

        document.querySelector('.next').addEventListener('click', () => {
            currentIndex++;
            updateSlider();
        });

        document.querySelector('.prev').addEventListener('click', () => {
            currentIndex--;
            updateSlider();
        });

        function updateSlider() {
            slides.style.transition = 'transform 0.5s ease-in-out';
            slides.style.transform = `translateX(-${currentIndex * slideWidth}px)`;

            if (currentIndex >= totalSlides - 1) {
                setTimeout(() => {
                    slides.style.transition = 'none';
                    currentIndex = 1;
                    slides.style.transform = `translateX(-${slideWidth}px)`;
                }, 500);
            }

            if (currentIndex <= 0) {
                setTimeout(() => {
                    slides.style.transition = 'none';
                    currentIndex = totalSlides - 2;
                    slides.style.transform = `translateX(-${(totalSlides - 2) * slideWidth}px)`;
                }, 500);
            }

            document.querySelector('.indicator.active').classList.remove('active');
            if (currentIndex === totalSlides - 1) {
                indicatorsContainer.children[0].classList.add('active');
            } else if (currentIndex === 0) {
                indicatorsContainer.children[indicatorsContainer.children.length - 1].classList.add('active');
            } else {
                indicatorsContainer.children[currentIndex - 1].classList.add('active');
            }
        }

        window.addEventListener('load', () => {
            let randomIndex = Math.floor(Math.random() * (totalSlides - 2)) + 1;
            currentIndex = randomIndex;
            updateSlider();
        });
    </script>
    <script src="../script/script.js"></script>
</body>
</html>
