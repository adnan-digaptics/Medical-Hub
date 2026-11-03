<?php
  require_once __DIR__ . '/includes/db.php';
  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['home_contact'])) {
    mmh_guard_public_post('contact', 'index.php#home-contact', 'Your request was sent. Our care team will follow up.');
    $name = trim((string) ($_POST['name'] ?? ''));
    $email = trim((string) ($_POST['email'] ?? ''));
    $phone = trim((string) ($_POST['phone'] ?? ''));
    $subject = trim((string) ($_POST['subject'] ?? ''));
    $message = trim((string) ($_POST['message'] ?? ''));
    if ($name !== '' && filter_var($email, FILTER_VALIDATE_EMAIL) && $message !== '') {
      $stmt = mmh_db()->prepare('INSERT INTO contacts (name, email, phone, subject, message, created_at) VALUES (:name, :email, :phone, :subject, :message, NOW())');
      $stmt->execute(array(
        'name' => $name,
        'email' => $email,
        'phone' => $phone,
        'subject' => $subject,
        'message' => $message,
      ));
      mmh_email_contact(array(
        'name' => $name,
        'email' => $email,
        'phone' => $phone,
        'subject' => $subject,
        'message' => $message,
        'status' => 'new',
        'admin_notes' => '',
      ), 'new');
      mmh_flash('ok', 'Your request was sent. Our care team will follow up.');
      mmh_redirect('index.php#home-contact');
    }
    mmh_flash('error', 'Please enter your name, a valid email, and a message.');
    mmh_redirect('index.php#home-contact');
  }
  $pageTitle = 'MidWest Medical Hub | Primary Care, Senior Care & Home Health';
  $headerType = 'home';
  include __DIR__ . '/includes/header.php';
  $homeOk = mmh_flash('ok');
  $homeErr = mmh_flash('error');
?>
    <!-- Start Hero Section -->
    <section class="position-relative">
      <div class="cs_hero_slider_thumb slick-slider">
        <div class="cs_hero_slider_thumb_item">
          <div class="cs_hero cs_style_1 cs_center cs_bg_filed" data-src="assets/img/unsplash_primary_care.jpg">
            <div class="container">
              <div class="cs_hero_text">
                <div class="cs_hero_text_in">
                  <h1 class="cs_hero_title">Primary Care Built Around <span>Your Life.</span></h1>
                  <p class="cs_hero_subtitle">Preventive visits, chronic condition management, medication review, and ongoing primary care with a team that sees the whole picture.</p>
                  <div class="cs_hero_info"><h3>Coordinated Care Starts Here.</h3><p>Appointments available by request</p></div>
                  <div class="cs_hero_btns">
                    <a href="contact" class="cs_btn cs_style_1 cs_color_1">
                      <span>Book an Appointment </span>
                      <i class="fa-solid fa-angles-right"></i>
                    </a>
                    <a href="about" class="cs_btn cs_style_1 cs_color_2">
                      <span>Explore Our Care </span>
                      <i class="fa-solid fa-angles-right"></i>
                    </a>
                  </div>
                </div>
                <div class="cs_hero_shape">
                  <img src="assets/img/icons/hero_icon.png" alt="Icon" class="cs_spinner_img">
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="cs_hero_slider_thumb_item">
          <div class="cs_hero cs_style_1 cs_center cs_bg_filed" data-src="assets/img/unsplash_geriatric.jpg">
            <div class="container">
              <div class="cs_hero_text">
                <div class="cs_hero_text_in">
                  <h1 class="cs_hero_title">Specialized Care for <br><span>Healthy Aging.</span></h1>
                  <p class="cs_hero_subtitle">Geriatric-focused care designed around mobility, memory, medication safety, independence, and the priorities of older adults and families.</p>
                  <div class="cs_hero_info"><h3>Coordinated Care Starts Here.</h3><p>Appointments available by request</p></div>
                  <div class="cs_hero_btns">
                    <a href="contact" class="cs_btn cs_style_1 cs_color_1">
                      <span>Book an Appointment </span>
                      <i class="fa-solid fa-angles-right"></i>
                    </a>
                    <a href="about" class="cs_btn cs_style_1 cs_color_2">
                      <span>Explore Our Care </span>
                      <i class="fa-solid fa-angles-right"></i>
                    </a>
                  </div>
                </div>
                <div class="cs_hero_shape">
                  <img src="assets/img/icons/hero_icon.png" alt="Icon" class="cs_spinner_img">
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="cs_hero_slider_thumb_item">
          <div class="cs_hero cs_style_1 cs_center cs_bg_filed" data-src="assets/img/unsplash_womens_health.jpg">
            <div class="container">
              <div class="cs_hero_text">
                <div class="cs_hero_text_in">
                  <h1 class="cs_hero_title">Women’s Health With <span>Whole-Person Care.</span></h1>
                  <p class="cs_hero_subtitle">Thoughtful primary and preventive care for women, with attention to screening, chronic disease, healthy aging, and individual goals.</p>
                  <div class="cs_hero_info"><h3>Coordinated Care Starts Here.</h3><p>Appointments available by request</p></div>
                  <div class="cs_hero_btns">
                    <a href="contact" class="cs_btn cs_style_1 cs_color_1">
                      <span>Book an Appointment </span>
                      <i class="fa-solid fa-angles-right"></i>
                    </a>
                    <a href="about" class="cs_btn cs_style_1 cs_color_2">
                      <span>Explore Our Care </span>
                      <i class="fa-solid fa-angles-right"></i>
                    </a>
                  </div>
                </div>
                <div class="cs_hero_shape">
                  <img src="assets/img/icons/hero_icon.png" alt="Icon" class="cs_spinner_img">
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="cs_hero_slider_thumb_item">
          <div class="cs_hero cs_style_1 cs_center cs_bg_filed" data-src="assets/img/unsplash_adult_day.jpg">
            <div class="container">
              <div class="cs_hero_text">
                <div class="cs_hero_text_in">
                  <h1 class="cs_hero_title">Supportive Day Care for <br><span>Older Adults.</span></h1>
                  <p class="cs_hero_subtitle">A safe, engaging environment that supports older adults during the day while giving families dependable care coordination and respite.</p>
                  <div class="cs_hero_info"><h3>Coordinated Care Starts Here.</h3><p>Appointments available by request</p></div>
                  <div class="cs_hero_btns">
                    <a href="contact" class="cs_btn cs_style_1 cs_color_1">
                      <span>Book an Appointment </span>
                      <i class="fa-solid fa-angles-right"></i>
                    </a>
                    <a href="about" class="cs_btn cs_style_1 cs_color_2">
                      <span>Explore Our Care </span>
                      <i class="fa-solid fa-angles-right"></i>
                    </a>
                  </div>
                </div>
                <div class="cs_hero_shape">
                  <img src="assets/img/icons/hero_icon.png" alt="Icon" class="cs_spinner_img">
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="cs_hero_slider_thumb_item">
          <div class="cs_hero cs_style_1 cs_center cs_bg_filed" data-src="assets/img/unsplash_home_care.jpg">
            <div class="container">
              <div class="cs_hero_text">
                <div class="cs_hero_text_in">
                  <h1 class="cs_hero_title">Trusted Care, Right <span>At Home.</span></h1>
                  <p class="cs_hero_subtitle">Home care and skilled home health services help patients remain safer and more independent where they are most comfortable.</p>
                  <div class="cs_hero_info"><h3>Coordinated Care Starts Here.</h3><p>Appointments available by request</p></div>
                  <div class="cs_hero_btns">
                    <a href="contact" class="cs_btn cs_style_1 cs_color_1">
                      <span>Book an Appointment </span>
                      <i class="fa-solid fa-angles-right"></i>
                    </a>
                    <a href="about" class="cs_btn cs_style_1 cs_color_2">
                      <span>Explore Our Care </span>
                      <i class="fa-solid fa-angles-right"></i>
                    </a>
                  </div>
                </div>
                <div class="cs_hero_shape">
                  <img src="assets/img/icons/hero_icon.png" alt="Icon" class="cs_spinner_img">
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="cs_hero_slider_thumb_item">
          <div class="cs_hero cs_style_1 cs_center cs_bg_filed" data-src="assets/img/unsplash_connected_care.jpg">
            <div class="container">
              <div class="cs_hero_text">
                <div class="cs_hero_text_in">
                  <h1 class="cs_hero_title">One Hub for <br><span>Connected Care.</span></h1>
                  <p class="cs_hero_subtitle">Clinic care, senior services, and in-home support coordinated through one medical hub for smoother transitions and clearer communication.</p>
                  <div class="cs_hero_info"><h3>Coordinated Care Starts Here.</h3><p>Appointments available by request</p></div>
                  <div class="cs_hero_btns">
                    <a href="contact" class="cs_btn cs_style_1 cs_color_1">
                      <span>Book an Appointment </span>
                      <i class="fa-solid fa-angles-right"></i>
                    </a>
                    <a href="about" class="cs_btn cs_style_1 cs_color_2">
                      <span>Explore Our Care </span>
                      <i class="fa-solid fa-angles-right"></i>
                    </a>
                  </div>
                </div>
                <div class="cs_hero_shape">
                  <img src="assets/img/icons/hero_icon.png" alt="Icon" class="cs_spinner_img">
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="cs_hero_slider_nav slick-slider">
        <div class="cs_hero_slider_thumb_mini">
          <img src="assets/img/unsplash_primary_care.jpg" alt="Primary care">
        </div>
        <div class="cs_hero_slider_thumb_mini">
          <img src="assets/img/unsplash_geriatric.jpg" alt="Geriatric care">
        </div>
        <div class="cs_hero_slider_thumb_mini">
          <img src="assets/img/unsplash_womens_health.jpg" alt="Women’s health">
        </div>
        <div class="cs_hero_slider_thumb_mini">
          <img src="assets/img/unsplash_adult_day.jpg" alt="Adult day care">
        </div>
        <div class="cs_hero_slider_thumb_mini">
          <img src="assets/img/unsplash_home_care.jpg" alt="Home care">
        </div>
        <div class="cs_hero_slider_thumb_mini">
          <img src="assets/img/unsplash_connected_care.jpg" alt="Connected care">
        </div>
      </div>
    </section>
    <!-- End Hero Section -->
    <!-- Start CTA Section -->
    <section class="cs_cta cs_style_1 cs_blue_bg position-relative overflow-hidden">
      <div class="container">
        <div class="cs_cta_in">
          <div class="cs_cta_left">
            <div class="cs_cta_thumb wow fadeInLeft" data-wow-duration="0.9s" data-wow-delay="0.25s">
              <img src="assets/img/cta_img_1.jpg" alt="">
            </div>
            <div class="cs_cta_info">
              <h2 class="cs_cta_title">Primary Care, Senior Services & Care at Home—Connected. </h2>
              <p class="cs_cta_subtitle">Quality care in the details that matter most to families.</p>
            </div>
          </div>
          <div class="cs_cta_right">
            <a href="appointments" class="cs_btn cs_style_1 cs_color_3">
              <span>Book Now </span>
              <i class="fa-solid fa-angles-right"></i>
            </a>
          </div>
        </div>
      </div>
      <div class="cs_cta_shape"></div>
    </section>
    <!-- End CTA Section -->
    <!-- Start About Section -->
    <section class="cs_about cs_style_1 position-relative">
      <div class="cs_height_120 cs_height_lg_80"></div>
      <div class="container">
        <div class="row align-items-center cs_gap_y_40">
          <div class="col-lg-6">
            <div class="cs_about_thumb">
              <div class="cs_about_thumb_1">
                <img src="assets/img/about_img_1.jpg" alt="About MidWest Medical Hub">
                <a href="https://www.youtube.com/embed/rRid6GCJtgc" class="cs_about_player_btn cs_video_open">
                  <span class="cs_player_btn cs_center">
                    <span></span>
                  </span>
                  <span class="cs_about_play_btn_text">How We Work</span>
                </a>
              </div>
              <div class="cs_about_thumb_2">
                <img src="assets/img/about_img_2.jpg" alt="About Image">
                <img src="assets/img/icons/about_shape_1.png" alt="Shape Image" class="cs_about_thumb_shape_2">
              </div>
              <div class="cs_experience_box cs_center">
                <p class="cs_experience_box_number">26+</p>
                <p class="cs_experience_box_title">Experience</p>
              </div>
            </div>
          </div>
          <div class="col-lg-6 wow fadeInRight" data-wow-duration="0.9s" data-wow-delay="0.25s">
            <div class="cs_about_content">
              <div class="cs_section_heading cs_style_1">
                <p class="cs_section_subtitle cs_accent_color">
                  <span class="cs_shape_left"></span>
                  ABOUT US
                </p>
                <h2 class="cs_section_title">Connected Care for Every Stage of Adult Life</h2>
              </div>
              <p class="cs_about_text">Our services are designed to work together, helping patients and families move smoothly between clinic visits, supportive day programs, and care at home.</p>
              <div class="row cs_gap_y_30">
                <div class="col-sm-6">
                  <div class="cs_iconbox cs_style_1">
                    <div class="cs_iconbox_head">
                      <div class="cs_iconbox_icon cs_center">
                        <img src="assets/img/icons/about_icon_1.png" alt="">
                      </div>
                      <h3 class="cs_iconbox_title m-0">Patient-Centered Support</h3>
                    </div>
                    <p class="cs_iconbox_subtitle mb-0">Care plans built around comfort, safety, and long-term wellbeing.</p>
                  </div>
                </div>
                <div class="col-sm-6">
                  <div class="cs_iconbox cs_style_1">
                    <div class="cs_iconbox_head">
                      <div class="cs_iconbox_icon cs_center">
                        <img src="assets/img/icons/about_icon_2.png" alt="">
                      </div>
                      <h3 class="cs_iconbox_title m-0">Coordinated Clinical Care</h3>
                    </div>
                    <p class="cs_iconbox_subtitle mb-0">Care plans built around comfort, safety, and long-term wellbeing.</p>
                  </div>
                </div>
              </div>
              <div class="cs_about_iconbox">
                <div class="cs_about_iconbox_icon cs_center">
                  <i class="fa-regular fa-circle-check"></i>
                </div>
                <p class="cs_about_iconbox_subtitle">One coordinated hub for clinic visits, day programs, and care at home.  <a href="#">READ MORE +</a></p>
              </div>
              <a href="about" class="cs_btn cs_style_1 cs_color_1">
                <span>Learn More </span>
                <i class="fa-solid fa-angles-right"></i>
              </a>
            </div>
          </div>
        </div>
      </div>
      <div class="cs_section_img"><img src="assets/img/about_section_img_1.png" alt=""></div>
      <div class="cs_height_120 cs_height_lg_80"></div>
    </section>
    <!-- End About Section -->
    <!-- Start Counter -->
    <div class="cs_counter_area cs_gray_bg">
      <div class="container">
        <div class="cs_counter_content cs_blue_bg">
          <div class="cs_counter_shape position-absolute">
            <img src="assets/img/counter_shape.png" alt="Shape">
          </div>
          <div class="cs_counter_1_wrap">
            <div class="cs_counter cs_style_1">
              <div class="cs_counter_icon cs_center">
                <img src="assets/img/icons/counter_icon_1.png" alt="Icon">
              </div>
              <div class="cs_counter_nmber"><span data-count-to="567" class="odometer"></span>+</div>
              <p class="cs_counter_title mb-0">Active Clients</p>
            </div>
            <div class="cs_counter cs_style_1">
              <div class="cs_counter_icon cs_center">
                <img src="assets/img/icons/counter_icon_2.png" alt="Icon">
              </div>
              <div class="cs_counter_nmber"><span data-count-to="23" class="odometer"></span>K+</div>
              <p class="cs_counter_title mb-0">Team Support</p>
            </div>
            <div class="cs_counter cs_style_1">
              <div class="cs_counter_icon cs_center">
                <img src="assets/img/icons/counter_icon_3.png" alt="Icon">
              </div>
              <div class="cs_counter_nmber"><span data-count-to="241" class="odometer"></span>+</div>
              <p class="cs_counter_title mb-0">Care Plans Completed</p>
            </div>
            <div class="cs_counter cs_style_1">
              <div class="cs_counter_icon cs_center">
                <img src="assets/img/icons/counter_icon_4.png" alt="Icon">
              </div>
              <div class="cs_counter_nmber"><span data-count-to="16" class="odometer"></span>K+</div>
              <p class="cs_counter_title mb-0">Families Supported</p>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- End Counter -->
    <!-- Start Service Section -->
    <section class="cs_gray_bg">
      <div class="cs_height_110 cs_height_lg_70"></div>
      <div class="container">
        <div class="cs_section_heading cs_style_1 cs_type_1">
          <div class="cs_section_heading_left">
            <p class="cs_section_subtitle cs_accent_color wow fadeInLeft" data-wow-duration="0.9s" data-wow-delay="0.25s">
              <span class="cs_shape_left"></span>
              OUR SERVICES
            </p>
            <h2 class="cs_section_title">Comprehensive Care, Connected Around You</h2>
          </div>
          <div class="cs_section_heading_right">Our services are designed to work together, helping patients and families move smoothly between clinic visits, supportive day programs, and care at home.</div>
        </div>
        <div class="cs_height_50 cs_height_lg_50"></div>
        <div class="row cs_row_gap_30 cs_gap_y_30">
          <div class="col-xl-3 col-lg-4 col-sm-6">
            <div class="cs_iconbox cs_style_2 cs_radius_15 cs_hover_layer_2">
              <div class="cs_iconbox_overlay cs_bg_filed" data-src="assets/img/service_bg.jpg"></div>
              <div class="cs_iconbox_shape"></div>
              <div class="cs_iconbox_header d-flex align-items-center justify-content-between">
                <div class="cs_iconbox_icon cs_center">
                  <img src="assets/img/icons/service_icon_1.png" alt="Service Icon">
                </div>
                <h3 class="iconbox_index">01</h3>
              </div>
              <h3 class="cs_iconbox_title"><a href="primary-care">Primary Care</a></h3>
              <p class="cs_iconbox_subtitle m-0">Ongoing medical care for adults, with prevention, follow-up, and a team that coordinates the next step.</p>
            </div>
          </div>
          <div class="col-xl-3 col-lg-4 col-sm-6">
            <div class="cs_iconbox cs_style_2 cs_radius_15">
              <div class="cs_iconbox_overlay cs_bg_filed" data-src="assets/img/service_bg.jpg"></div>
              <div class="cs_iconbox_shape"></div>
              <div class="cs_iconbox_header d-flex align-items-center justify-content-between">
                <div class="cs_iconbox_icon cs_center">
                  <img src="assets/img/icons/service_icon_2.png" alt="Service Icon">
                </div>
                <h3 class="iconbox_index">02</h3>
              </div>
              <h3 class="cs_iconbox_title"><a href="geriatric-care">Geriatric Care</a></h3>
              <p class="cs_iconbox_subtitle m-0">Age-friendly care focused on mobility, memory, medications, and independence.</p>
            </div>
          </div>
          <div class="col-xl-3 col-lg-4 col-sm-6">
            <div class="cs_iconbox cs_style_2 cs_radius_15">
              <div class="cs_iconbox_overlay cs_bg_filed" data-src="assets/img/service_bg.jpg"></div>
              <div class="cs_iconbox_shape"></div>
              <div class="cs_iconbox_header d-flex align-items-center justify-content-between">
                <div class="cs_iconbox_icon cs_center">
                  <img src="assets/img/icons/service_icon_3.png" alt="Service Icon">
                </div>
                <h3 class="iconbox_index">03</h3>
              </div>
              <h3 class="cs_iconbox_title"><a href="womens-health">Women's Health</a></h3>
              <p class="cs_iconbox_subtitle m-0">Preventive and primary care for women across adulthood, including screening and healthy aging.</p>
            </div>
          </div>
          <div class="col-xl-3 col-lg-4 col-sm-6">
            <div class="cs_iconbox cs_style_2 cs_radius_15">
              <div class="cs_iconbox_overlay cs_bg_filed" data-src="assets/img/service_bg.jpg"></div>
              <div class="cs_iconbox_shape"></div>
              <div class="cs_iconbox_header d-flex align-items-center justify-content-between">
                <div class="cs_iconbox_icon cs_center">
                  <img src="assets/img/icons/service_icon_4.png" alt="Service Icon">
                </div>
                <h3 class="iconbox_index">04</h3>
              </div>
              <h3 class="cs_iconbox_title"><a href="adult-day-care">Adult Day Care</a></h3>
              <p class="cs_iconbox_subtitle m-0">A safe, engaging daytime setting for older adults, with dependable support for families.</p>
            </div>
          </div>
          <div class="col-xl-3 col-lg-4 col-sm-6">
            <div class="cs_iconbox cs_style_2 cs_radius_15">
              <div class="cs_iconbox_overlay cs_bg_filed" data-src="assets/img/service_bg.jpg"></div>
              <div class="cs_iconbox_shape"></div>
              <div class="cs_iconbox_header d-flex align-items-center justify-content-between">
                <div class="cs_iconbox_icon cs_center">
                  <img src="assets/img/icons/service_icon_5.png" alt="Service Icon">
                </div>
                <h3 class="iconbox_index">05</h3>
              </div>
              <h3 class="cs_iconbox_title"><a href="home-care">Home Care</a></h3>
              <p class="cs_iconbox_subtitle m-0">Personal support at home that helps adults stay safer, more comfortable, and more independent.</p>
            </div>
          </div>
          <div class="col-xl-3 col-lg-4 col-sm-6">
            <div class="cs_iconbox cs_style_2 cs_radius_15">
              <div class="cs_iconbox_overlay cs_bg_filed" data-src="assets/img/service_bg.jpg"></div>
              <div class="cs_iconbox_shape"></div>
              <div class="cs_iconbox_header d-flex align-items-center justify-content-between">
                <div class="cs_iconbox_icon cs_center">
                  <img src="assets/img/icons/service_icon_6.png" alt="Service Icon">
                </div>
                <h3 class="iconbox_index">06</h3>
              </div>
              <h3 class="cs_iconbox_title"><a href="home-health">Home Health Care</a></h3>
              <p class="cs_iconbox_subtitle m-0">Skilled nursing and therapy at home after illness, hospital stays, or when clinic visits are harder.</p>
            </div>
          </div>
        </div>
        <div class="cs_service_footer">
          <div class="cs_service_footer_icon">
            <img src="assets/img/icons/service_footer_icon_1.png" alt="Icon">
          </div>
          <div class="cs_service_footer_text cs_medium">
           Connected care for clinic visits, day programs, and support at home.<br> <a href="service">View all services<span> <i class="fa-solid fa-angles-right"></i></span></a>
          </div>
        </div>
      </div>
      <div class="cs_height_120 cs_height_lg_80"></div>
    </section>
    <!-- End Service Section -->
    <!-- Start Team Section -->
    <section>
      <div class="cs_height_110 cs_height_lg_70"></div>
      <div class="container">
        <div class="cs_section_heading cs_style_1 text-center">
          <p class="cs_section_subtitle cs_accent_color wow fadeInUp" data-wow-duration="0.9s" data-wow-delay="0.25s">
            <span class="cs_shape_left"></span>OUR TEAM MEMBER<span class="cs_shape_right"></span>
          </p>
          <h2 class="cs_section_title">Meet the Care Teams Behind Your Health</h2>
        </div>
        <div class="cs_height_50 cs_height_lg_50"></div>
        <div class="cs_slider cs_style_1 cs_slider_gap_24">
          <div class="cs_slider_container" data-autoplay="0" data-loop="1" data-speed="600" data-center="0"
            data-variable-width="0" data-slides-per-view="responsive" data-xs-slides="1" data-sm-slides="2" data-md-slides="3" data-lg-slides="4" data-add-slides="4">
            <div class="cs_slider_wrapper">
              <div class="cs_slide">
                <div class="cs_team cs_style_1 cs_blue_bg">
                  <div class="cs_team_shape cs_accent_bg"></div>
                  <a href="doctor-details" class="cs_team_thumbnail">
                    <img src="assets/img/team_1.jpg" alt="Team Thumbnail">
                  </a>
                  <div class="cs_team_bio">
                    <h3 class="cs_team_title cs_extra_bold mb-0"><a href="doctor-details">Primary Care Team</a></h3>
                    <p class="cs_team_subtitle">Care Specialist</p>
                    <div class="cs_social_btns cs_style_1">
                      <a href="#" class="cs_center"><i class="fa-brands fa-facebook-f"></i></a>
                      <a href="#" class="cs_center"><i class="fa-brands fa-pinterest-p"></i></a>
                      <a href="#" class="cs_center"><i class="fa-brands fa-twitter"></i></a>
                      <a href="#" class="cs_center"><i class="fa-brands fa-instagram"></i></a>
                    </div>
                  </div>
                </div>
              </div>
              <div class="cs_slide">
                <div class="cs_team cs_style_1 cs_blue_bg">
                  <div class="cs_team_shape cs_accent_bg"></div>
                  <a href="doctor-details" class="cs_team_thumbnail">
                    <img src="assets/img/team_5.jpg" alt="Team Thumbnail">
                  </a>
                  <div class="cs_team_bio">
                    <h3 class="cs_team_title cs_extra_bold mb-0"><a href="doctor-details">Geriatric Care Team</a></h3>
                    <p class="cs_team_subtitle">Care Specialist</p>
                    <div class="cs_social_btns cs_style_1">
                      <a href="#" class="cs_center"><i class="fa-brands fa-facebook-f"></i></a>
                      <a href="#" class="cs_center"><i class="fa-brands fa-pinterest-p"></i></a>
                      <a href="#" class="cs_center"><i class="fa-brands fa-twitter"></i></a>
                      <a href="#" class="cs_center"><i class="fa-brands fa-instagram"></i></a>
                    </div>
                  </div>
                </div>
              </div>
              <div class="cs_slide">
                <div class="cs_team cs_style_1 cs_blue_bg">
                  <div class="cs_team_shape cs_accent_bg"></div>
                  <a href="doctor-details" class="cs_team_thumbnail">
                    <img src="assets/img/team_3.jpg" alt="Team Thumbnail">
                  </a>
                  <div class="cs_team_bio">
                    <h3 class="cs_team_title cs_extra_bold mb-0"><a href="doctor-details">Women’s Health Team</a></h3>
                    <p class="cs_team_subtitle">Care Specialist</p>
                    <div class="cs_social_btns cs_style_1">
                      <a href="#" class="cs_center"><i class="fa-brands fa-facebook-f"></i></a>
                      <a href="#" class="cs_center"><i class="fa-brands fa-pinterest-p"></i></a>
                      <a href="#" class="cs_center"><i class="fa-brands fa-twitter"></i></a>
                      <a href="#" class="cs_center"><i class="fa-brands fa-instagram"></i></a>
                    </div>
                  </div>
                </div>
              </div>
              <div class="cs_slide">
                <div class="cs_team cs_style_1 cs_blue_bg">
                  <div class="cs_team_shape cs_accent_bg"></div>
                  <a href="doctor-details" class="cs_team_thumbnail">
                    <img src="assets/img/team_4.jpg" alt="Team Thumbnail">
                  </a>
                  <div class="cs_team_bio">
                    <h3 class="cs_team_title cs_extra_bold mb-0"><a href="doctor-details">Adult Day Care Team</a></h3>
                    <p class="cs_team_subtitle">Care Specialist</p>
                    <div class="cs_social_btns cs_style_1">
                      <a href="#" class="cs_center"><i class="fa-brands fa-facebook-f"></i></a>
                      <a href="#" class="cs_center"><i class="fa-brands fa-pinterest-p"></i></a>
                      <a href="#" class="cs_center"><i class="fa-brands fa-twitter"></i></a>
                      <a href="#" class="cs_center"><i class="fa-brands fa-instagram"></i></a>
                    </div>
                  </div>
                </div>
              </div>
              <div class="cs_slide">
                <div class="cs_team cs_style_1 cs_blue_bg">
                  <div class="cs_team_shape cs_accent_bg"></div>
                  <a href="doctor-details" class="cs_team_thumbnail">
                    <img src="assets/img/team_6.jpg" alt="Team Thumbnail">
                  </a>
                  <div class="cs_team_bio">
                    <h3 class="cs_team_title cs_extra_bold mb-0"><a href="doctor-details">Home Health Team</a></h3>
                    <p class="cs_team_subtitle">Care Specialist</p>
                    <div class="cs_social_btns cs_style_1">
                      <a href="#" class="cs_center"><i class="fa-brands fa-facebook-f"></i></a>
                      <a href="#" class="cs_center"><i class="fa-brands fa-pinterest-p"></i></a>
                      <a href="#" class="cs_center"><i class="fa-brands fa-twitter"></i></a>
                      <a href="#" class="cs_center"><i class="fa-brands fa-instagram"></i></a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="cs_pagination cs_style_2"></div>
        </div>
      </div>
      <div class="cs_height_120 cs_height_lg_80"></div>
      <hr>
    </section>
    <!-- End Team Section -->
    <!-- Start Why Choose Us Section -->
    <section class="cs_why_choose cs_bg_filed" data-src="assets/img/service_bg_2.jpg">
      <div class="cs_height_110 cs_height_lg_70"></div>
      <div class="container">
        <div class="row">
          <div class="col-lg-8">
            <div class="cs_section_heading cs_style_1">
              <p class="cs_section_subtitle cs_accent_color wow fadeInLeft" data-wow-duration="0.9s" data-wow-delay="0.25s"><span class="cs_shape_left"></span> WHY CHOOSE US</p>
              <h2 class="cs_section_title">Why Families Choose MidWest Medical Hub</h2>
            </div>
            <div class="cs_height_50 cs_height_lg_50"></div>
          </div>
        </div>
        <div class="row">
          <div class="col-xxl-7 col-xl-8 col-lg-9">
            <div class="cs_service_wrapper">
              <div class="cs_service_list">
                <div class="cs_iconbox cs_style_3">
                  <div class="cs_iconbox_icon cs_center cs_radius_5">
                    <img src="assets/img/icons/service_icon_9.png" alt="Icon">
                  </div>
                  <div class="cs_iconbox_text">
                    <h3 class="cs_iconbox_title">Primary Care First</h3>
                    <p class="cs_iconbox_subtitle">A medical home that listens, follows through, and coordinates every next step.</p>
                  </div>
                </div>
                <div class="cs_iconbox cs_style_3">
                  <div class="cs_iconbox_icon cs_center cs_radius_5">
                    <img src="assets/img/icons/service_icon_10.png" alt="Icon">
                  </div>
                  <div class="cs_iconbox_text">
                    <h3 class="cs_iconbox_title">Age-Friendly Care</h3>
                    <p class="cs_iconbox_subtitle">Geriatric visits focused on medications, mobility, memory, and independence.</p>
                  </div>
                </div>
                <div class="cs_iconbox cs_style_3">
                  <div class="cs_iconbox_icon cs_center cs_radius_5">
                    <img src="assets/img/icons/service_icon_11.png" alt="Icon">
                  </div>
                  <div class="cs_iconbox_text">
                    <h3 class="cs_iconbox_title">Care at Home</h3>
                    <p class="cs_iconbox_subtitle">Home care and home health planned with the same team that sees you in clinic.</p>
                  </div>
                </div>
                <div class="cs_iconbox cs_style_3">
                  <div class="cs_iconbox_icon cs_center cs_radius_5">
                    <img src="assets/img/icons/service_icon_12.png" alt="Icon">
                  </div>
                  <div class="cs_iconbox_text">
                    <h3 class="cs_iconbox_title">Family & Caregiver Support</h3>
                    <p class="cs_iconbox_subtitle">Clearer updates, adult day options, and help matching the right level of support at home.</p>
                  </div>
                </div>
                <div class="cs_iconbox cs_style_3">
                  <div class="cs_iconbox_icon cs_center cs_radius_5">
                    <img src="assets/img/icons/service_icon_13.png" alt="Icon">
                  </div>
                  <div class="cs_iconbox_text">
                    <h3 class="cs_iconbox_title">Coordinated Clinical Team</h3>
                    <p class="cs_iconbox_subtitle">Primary, geriatric, women’s health, and in-home teams work from one connected plan.</p>
                  </div>
                </div>
                <div class="cs_iconbox cs_style_3">
                  <div class="cs_iconbox_icon cs_center cs_radius_5">
                    <img src="assets/img/icons/service_icon_14.png" alt="Icon">
                  </div>
                  <div class="cs_iconbox_text">
                    <h3 class="cs_iconbox_title">Respectful, Personalized Care</h3>
                    <p class="cs_iconbox_subtitle">Plans built around comfort, safety, independence, and what matters most to each family.</p>
                  </div>
                </div>
              </div>
            </div>
            
          </div>
        </div>
      </div>
      <div class="cs_height_120 cs_height_lg_80"></div>
    </section>
    <!-- End Why Choose Us Section -->
    <!-- Start Projects Section -->
    <section class="cs_tabs">
      <div class="cs_height_110 cs_height_lg_70"></div>
      <div class="container">
        <div class="cs_section_heading cs_style_1 cs_type_1">
          <div class="cs_section_heading_left">
            <p class="cs_section_subtitle cs_accent_color wow fadeInLeft" data-wow-duration="0.9s" data-wow-delay="0.25s"><span class="cs_shape_left"></span>OUR CARE SETTINGS</p>
            <h2 class="cs_section_title">Care Wherever Life Happens</h2>
          </div>
          <div class="cs_section_heading_right">
            <p class="mb-0">Our connected care model combines medical expertise, practical support, and clear communication for patients and families.</p>
            <ul class="cs_tab_links cs_style_3 cs_mp_0">
              <li class="active"><a href="#dental">Clinic Care</a></li>
              <li><a href="#cardiology">Geriatric Care</a></li>
              <li><a href="#neurology">Home Care</a></li>
              <li><a href="#medical">Day Programs</a></li>
            </ul>
          </div>
        </div>
        <div class="cs_height_50 cs_height_lg_50"></div>
      </div>
      <div class="cs_tab_body">
        <div class="container-fluide cs_tab active" id="dental">
          <div class="row cs_gap_y_30">
            <div class="col-lg-4">
              <div class="cs_card cs_style_1">
                <a href="#" class="cs_card_thumbnail d-block">
                  <img src="assets/img/project_1.jpg" alt="Project Image" class="w-100">
                </a>
                <div class="cs_card_info_wrapper">
                  <div class="cs_card_text">
                    <h3 class="cs_card_title"><a href="primary-care">Primary Care Clinic</a></h3>
                    <p class="cs_card_subtitle mb-0">Connected services that help patients stay healthier, safer, and more independent.</p>
                  </div>
                  <div class="cs_card_index cs_center rounded-circle">01</div>
                </div>
              </div>
            </div>
            <div class="col-lg-4">
              <div class="cs_card cs_style_1">
                <a href="#" class="cs_card_thumbnail d-block">
                  <img src="assets/img/project_2.jpg" alt="Project Image" class="w-100">
                </a>
                <div class="cs_card_info_wrapper">
                  <div class="cs_card_text">
                    <h3 class="cs_card_title"><a href="womens-health">Women’s Health</a></h3>
                    <p class="cs_card_subtitle mb-0">Connected services that help patients stay healthier, safer, and more independent.</p>
                  </div>
                  <div class="cs_card_index cs_center rounded-circle">02</div>
                </div>
              </div>
            </div>
            <div class="col-lg-4">
              <div class="cs_card cs_style_1">
                <a href="#" class="cs_card_thumbnail d-block">
                  <img src="assets/img/project_3.jpg" alt="Project Image" class="w-100">
                </a>
                <div class="cs_card_info_wrapper">
                  <div class="cs_card_text">
                    <h3 class="cs_card_title"><a href="geriatric-care">Geriatric Care</a></h3>
                    <p class="cs_card_subtitle mb-0">Connected services that help patients stay healthier, safer, and more independent.</p>
                  </div>
                  <div class="cs_card_index cs_center rounded-circle">03</div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="container-fluide cs_tab" id="cardiology">
          <div class="row cs_gap_y_30">
            <div class="col-lg-4">
              <div class="cs_card cs_style_1">
                <a href="#" class="cs_card_thumbnail d-block">
                  <img src="assets/img/project_4.jpg" alt="Adult day center">
                </a>
                <div class="cs_card_info_wrapper">
                  <div class="cs_card_text">
                    <h3 class="cs_card_title"><a href="adult-day-care">Adult Day Center</a></h3>
                    <p class="cs_card_subtitle mb-0">Connected services that help patients stay healthier, safer, and more independent.</p>
                  </div>
                  <div class="cs_card_index cs_center rounded-circle">03</div>
                </div>
              </div>
            </div>
            <div class="col-lg-4">
              <div class="cs_card cs_style_1">
                <a href="#" class="cs_card_thumbnail d-block">
                  <img src="assets/img/project_5.jpg" alt="Home care">
                </a>
                <div class="cs_card_info_wrapper">
                  <div class="cs_card_text">
                    <h3 class="cs_card_title"><a href="home-care">Home Care</a></h3>
                    <p class="cs_card_subtitle mb-0">Connected services that help patients stay healthier, safer, and more independent.</p>
                  </div>
                  <div class="cs_card_index cs_center rounded-circle">01</div>
                </div>
              </div>
            </div>
            <div class="col-lg-4">
              <div class="cs_card cs_style_1">
                <a href="#" class="cs_card_thumbnail d-block">
                  <img src="assets/img/project_6.jpg" alt="Skilled home health">
                </a>
                <div class="cs_card_info_wrapper">
                  <div class="cs_card_text">
                    <h3 class="cs_card_title"><a href="home-health">Skilled Home Health</a></h3>
                    <p class="cs_card_subtitle mb-0">Connected services that help patients stay healthier, safer, and more independent.</p>
                  </div>
                  <div class="cs_card_index cs_center rounded-circle">02</div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="container-fluide cs_tab" id="neurology">
          <div class="row cs_gap_y_30">
            <div class="col-lg-4">
              <div class="cs_card cs_style_1">
                <a href="#" class="cs_card_thumbnail d-block">
                  <img src="assets/img/project_7.jpg" alt="Chronic care support">
                </a>
                <div class="cs_card_info_wrapper">
                  <div class="cs_card_text">
                    <h3 class="cs_card_title"><a href="service">Chronic Care Support</a></h3>
                    <p class="cs_card_subtitle mb-0">Connected services that help patients stay healthier, safer, and more independent.</p>
                  </div>
                  <div class="cs_card_index cs_center rounded-circle">02</div>
                </div>
              </div>
            </div>
            <div class="col-lg-4">
              <div class="cs_card cs_style_1">
                <a href="#" class="cs_card_thumbnail d-block">
                  <img src="assets/img/project_8.jpg" alt="Caregiver support">
                </a>
                <div class="cs_card_info_wrapper">
                  <div class="cs_card_text">
                    <h3 class="cs_card_title"><a href="service">Caregiver Support</a></h3>
                    <p class="cs_card_subtitle mb-0">Connected services that help patients stay healthier, safer, and more independent.</p>
                  </div>
                  <div class="cs_card_index cs_center rounded-circle">03</div>
                </div>
              </div>
            </div>
            <div class="col-lg-4">
              <div class="cs_card cs_style_1">
                <a href="#" class="cs_card_thumbnail d-block">
                  <img src="assets/img/project_9.jpg" alt="Preventive care">
                </a>
                <div class="cs_card_info_wrapper">
                  <div class="cs_card_text">
                    <h3 class="cs_card_title"><a href="service">Preventive Care</a></h3>
                    <p class="cs_card_subtitle mb-0">Connected services that help patients stay healthier, safer, and more independent.</p>
                  </div>
                  <div class="cs_card_index cs_center rounded-circle">01</div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="container-fluide cs_tab" id="medical">
          <div class="row cs_gap_y_30">
            <div class="col-lg-4">
              <div class="cs_card cs_style_1">
                <a href="#" class="cs_card_thumbnail d-block">
                  <img src="assets/img/project_10.jpg" alt="Care transitions">
                </a>
                <div class="cs_card_info_wrapper">
                  <div class="cs_card_text">
                    <h3 class="cs_card_title"><a href="service">Care Transitions</a></h3>
                    <p class="cs_card_subtitle mb-0">Connected services that help patients stay healthier, safer, and more independent.</p>
                  </div>
                  <div class="cs_card_index cs_center rounded-circle">03</div>
                </div>
              </div>
            </div>
            <div class="col-lg-4">
              <div class="cs_card cs_style_1">
                <a href="#" class="cs_card_thumbnail d-block">
                  <img src="assets/img/service_2.jpg" alt="Healthy aging">
                </a>
                <div class="cs_card_info_wrapper">
                  <div class="cs_card_text">
                    <h3 class="cs_card_title"><a href="service">Healthy Aging</a></h3>
                    <p class="cs_card_subtitle mb-0">Connected services that help patients stay healthier, safer, and more independent.</p>
                  </div>
                  <div class="cs_card_index cs_center rounded-circle">02</div>
                </div>
              </div>
            </div>
            <div class="col-lg-4">
              <div class="cs_card cs_style_1">
                <a href="#" class="cs_card_thumbnail d-block">
                  <img src="assets/img/service_3.jpg" alt="Supportive care">
                </a>
                <div class="cs_card_info_wrapper">
                  <div class="cs_card_text">
                    <h3 class="cs_card_title"><a href="service">Supportive Care</a></h3>
                    <p class="cs_card_subtitle mb-0">Connected services that help patients stay healthier, safer, and more independent.</p>
                  </div>
                  <div class="cs_card_index cs_center rounded-circle">01</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="cs_height_90 cs_height_lg_50"></div>
      <hr>
    </section>
    <!-- End Projects Section -->
    <!-- Start CTA Section -->
    <section class="cs_cta cs_style_2 cs_connected_care cs_bg_filed cs_center" data-src="assets/img/hero_slider_7.jpg">
      <div class="container">
        <div class="row align-items-center cs_gap_y_40">
          <div class="col-lg-6">
            <div class="cs_cta_btn_wrapper wow zoomIn" data-wow-duration="0.9s" data-wow-delay="0.25s">
              <a href="https://www.youtube.com/embed/rRid6GCJtgc" class="cs_video_open">
                <span class="cs_player_btn cs_center">
                  <span></span>
                </span>
                <span class="cs_play_btn_text">WATCH VIDEO</span>
              </a>
            </div>
          </div>
          <div class="col-lg-6">
            <div class="cs_cta_text">
              <div class="cs_section_heading cs_style_1">
                <p class="cs_section_subtitle cs_accent_color"><span class="cs_shape_left"></span>CONNECTED CARE</p>
                <h2 class="cs_section_title cs_white_color">One Health Hub. More Ways to Care for You.</h2>
                <p class="cs_cta_subtitle cs_white_color">We are privileged to work with hundreds of
                  connected care across primary care, senior services, and support at home.</p>
                  <a href="contact" class="cs_btn cs_style_1 cs_color_3">
                    <span>Learn More </span>
                    <i class="fa-solid fa-angles-right"></i>
                  </a>
              </div>
            </div>
          </div>
        </div>
        <div class="cs_cta_shape position-absolute">
          <img src="assets/img/medical_brand.png" alt="Medical Brand" class="cs_spinner_img">
        </div>
      </div>
    </section>
    <!-- End CTA Section -->
    <!-- Start Medical Tab Section -->
    <section>
      <div class="cs_height_110 cs_height_lg_70"></div>
      <div class="container">
        <div class="cs_section_heading cs_style_1 text-center">
          <p class="cs_section_subtitle cs_accent_color wow fadeInUp" data-wow-duration="0.9s" data-wow-delay="0.25s">
            <span class="cs_shape_left"></span>Service Offerings<span class="cs_shape_right"></span>
          </p>
          <h2 class="cs_section_title">Explore Our Care<br> Services</h2>
        </div>
        <div class="cs_height_50 cs_height_lg_50"></div>
        <div class="cs_tabs">
          <ul class="cs_tab_links cs_style_1 cs_bold">
            <li class="active">
              <a href="#brain_althim">
                <span class="cs_tab_link_icon cs_center"><img src="assets/img/icons/tab_link_icon_1.png" alt="Icon"></span>
                <span>Primary Care</span>
              </a>
            </li>
            <li>
              <a href="#emergency">
                <span class="cs_tab_link_icon cs_center"><img src="assets/img/icons/tab_link_icon_2.png" alt="Icon"></span>
                <span>Geriatric Care</span>
              </a>
            </li>
            <li>
              <a href="#heart_beat">
                <span class="cs_tab_link_icon cs_center"><img src="assets/img/icons/tab_link_icon_3.png" alt="Icon"></span>
                <span>Women’s Health</span>
              </a>
            </li>
            <li>
              <a href="#blood_test">
                <span class="cs_tab_link_icon cs_center"><img src="assets/img/icons/tab_link_icon_4.png" alt="Icon"></span>
                <span>Care at Home</span>
              </a>
            </li>
          </ul>
          <div class="cs_height_50 cs_height_lg_50"></div>
          <div class="tab-content">
            <div id="brain_althim" class="cs_tab active">
              <div class="cs_card cs_style_2">
                <div class="row cs_gap_y_30 align-items-xl-center">
                  <div class="col-lg-6">
                    <div class="cs_card_thumb cs_radius_5">
                      <img src="assets/img/offerings03.jpg" alt="Care service image">
                    </div>
                  </div>
                  <div class="col-lg-6">
                    <div class="cs_card_text">
                      <h2 class="cs_card_title">Primary Care That Listens First</h2>
                      <p class="cs_card_subtitle">Primary care is the medical home at MidWest Medical Hub. We manage prevention, chronic conditions, and the next step into day programs or care at home.
                      </p>
                      <ul class="cs_list cs_style_1 cs_mp0">
                        <li>
                          <i><img src="assets/img/icons/check_icon_1.png" alt="Icon"></i>
                          Care plans are tailored to each patient’s needs, goals, and level of support.
                        </li>
                        <li>
                          <i><img src="assets/img/icons/check_icon_1.png" alt="Icon"></i>
                          Clinic, day, and home services stay connected instead of working in separate silos.
                        </li>
    
                      </ul>
                      
                      <a href="primary-care" class="cs_btn cs_style_1 cs_color_1">
                        <span>Read More </span>
                        <i class="fa-solid fa-angles-right"></i>
                      </a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div id="emergency" class="cs_tab">
              <div class="cs_card cs_style_2">
                <div class="row cs_gap_y_30 align-items-xl-center">
                  <div class="col-lg-6">
                    <div class="cs_card_thumb cs_radius_5">
                      <img src="assets/img/offerings02.jpg" alt="Geriatric care">
                    </div>
                  </div>
                  <div class="col-lg-6">
                    <div class="cs_card_text">
                      <h2 class="cs_card_title">Geriatric Care for Safer Aging</h2>
                      <p class="cs_card_subtitle">Age-friendly care looks at medications, mobility, memory, and the support already at home—so older adults keep as much independence as possible.</p>
                      <ul class="cs_list cs_style_1 cs_mp0">
                        <li>
                          <i><img src="assets/img/icons/check_icon_1.png" alt="Icon"></i>
                          Medication review, fall-risk screening, and family care planning in one visit.
                        </li>
                        <li>
                          <i><img src="assets/img/icons/check_icon_1.png" alt="Icon"></i>
                          Adult day, home care, and home health can be added without starting over.
                        </li>
                      </ul>
                      <a href="geriatric-care" class="cs_btn cs_style_1 cs_color_1">
                        <span>Read More </span>
                        <i class="fa-solid fa-angles-right"></i>
                      </a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div id="heart_beat" class="cs_tab">
              <div class="cs_card cs_style_2">
                <div class="row cs_gap_y_30 align-items-xl-center">
                  <div class="col-lg-6">
                    <div class="cs_card_thumb cs_radius_5">
                      <img src="assets/img/offerings01.jpg" alt="Women’s health">
                    </div>
                  </div>
                  <div class="col-lg-6">
                    <div class="cs_card_text">
                      <h2 class="cs_card_title">Women’s Health Across Adulthood</h2>
                      <p class="cs_card_subtitle">Whole-person primary care for women: prevention, chronic conditions, midlife changes, and a plan that stays connected as needs shift.</p>
                      <ul class="cs_list cs_style_1 cs_mp0">
                        <li>
                          <i><img src="assets/img/icons/check_icon_1.png" alt="Icon"></i>
                          Screenings, medications, and daily health reviewed as one record.
                        </li>
                        <li>
                          <i><img src="assets/img/icons/check_icon_1.png" alt="Icon"></i>
                          Later geriatric, day, or home support can join the same care hub.
                        </li>
                      </ul>
                      <a href="womens-health" class="cs_btn cs_style_1 cs_color_1">
                        <span>Read More </span>
                        <i class="fa-solid fa-angles-right"></i>
                      </a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div id="blood_test" class="cs_tab">
              <div class="cs_card cs_style_2">
                <div class="row cs_gap_y_30 align-items-xl-center">
                  <div class="col-lg-6">
                    <div class="cs_card_thumb cs_radius_5">
                      <img src="assets/img/offerings04.jpg" alt="Care at home">
                    </div>
                  </div>
                  <div class="col-lg-6">
                    <div class="cs_card_text">
                      <h2 class="cs_card_title">Home Care and Home Health, Connected</h2>
                      <p class="cs_card_subtitle">Personal support at home and skilled clinical care at home are different services. MidWest Medical Hub helps families use the right mix, with clinic care still in the loop.</p>
                      <ul class="cs_list cs_style_1 cs_mp0">
                        <li>
                          <i><img src="assets/img/icons/check_icon_1.png" alt="Icon"></i>
                          Home care for daily living, meals, and a safer routine.
                        </li>
                        <li>
                          <i><img src="assets/img/icons/check_icon_1.png" alt="Icon"></i>
                          Home health for nursing, therapy, and recovery after illness.
                        </li>
                      </ul>
                      <a href="home-care" class="cs_btn cs_style_1 cs_color_1">
                        <span>Read More </span>
                        <i class="fa-solid fa-angles-right"></i>
                      </a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="cs_height_120 cs_height_lg_80"></div>
      <hr>
    </section>
    <!-- End Medical Tab Section -->
    <!-- Start Medical Solution Section -->
    <section class="cs_card cs_style_3 cs_gray_bg position-relative">
      <div class="cs_height_110 cs_height_lg_70"></div>
      <div class="container">
        <div class="row cs_gap_y_40">
          <div class="col-lg-6">
          <div class="cs_section_heading cs_style_1">
            <p class="cs_section_subtitle cs_accent_color">
              <span class="cs_shape_left"></span>CONTACT US
            </p>
            <h2 class="cs_section_title">Schedule Your Visit <br>With Our Care Team</h2>
          </div>
          <div class="cs_height_25 cs_height_lg_25"></div>
          <form id="home-contact" class="cs_contact_form row cs_gap_y_30 home_form_area" method="post">
            <?php mmh_csrf_field(); ?>
            <?php mmh_honeypot_field(); ?>
            <input type="hidden" name="home_contact" value="1">
            <?php if (!empty($homeOk)) : ?><div class="col-lg-12"><p style="color:#146c43"><?php echo mmh_h($homeOk); ?></p></div><?php endif; ?>
            <?php if (!empty($homeErr)) : ?><div class="col-lg-12"><p style="color:#9b1c1c"><?php echo mmh_h($homeErr); ?></p></div><?php endif; ?>
            <div class="col-md-6">
              <input type="text" name="name" class="cs_form_field" placeholder="Your name">
            </div>
            <div class="col-md-6">
              <input type="email" name="email" class="cs_form_field" placeholder="Your email">
            </div>
            <div class="col-md-6">
              <input type="text" name="subject" class="cs_form_field" placeholder="Your Subject">
            </div>
            <div class="col-md-6">
              <input type="text" name="phone" class="cs_form_field" placeholder="Your phone">
            </div>
            <div class="col-lg-12">
              <textarea rows="5" name="message" class="cs_form_field" placeholder="Your comments"></textarea>
            </div>
            <div class="col-lg-12">
              <button type="submit" class="cs_btn cs_style_1 cs_color_1">Send Request</button>
            </div>
          </form>
          </div>
          <div class="col-lg-6">
            <div class="cs_solution_thumbnail cs_bg_filed" data-src="assets/img/medical_solution_1.jpg">
            </div>
          </div>
        </div>
      </div>
      <div class="cs_solution_shape position-absolute">
        <img src="assets/img/stethoscope.png" alt="Shape">
      </div>
      <div class="cs_height_120 cs_height_lg_80"></div>
    </section>
    <!-- End Medical Solution Section -->
    <!-- Start Blog Section -->
    <section>
      <div class="cs_height_110 cs_height_lg_70"></div>
      <div class="container">
        <div class="cs_section_heading cs_style_1 text-center">
          <p class="cs_section_subtitle cs_accent_color wow fadeInUp" data-wow-duration="0.9s" data-wow-delay="0.25s">
            <span class="cs_shape_left"></span>OUR LARGEST BLOG<span class="cs_shape_right"></span>
          </p>
          <h2 class="cs_section_title">Health Guidance for Patients & Families</h2>
        </div>
        <div class="cs_height_50 cs_height_lg_50"></div>
        <div class="cs_slider cs_style_1 cs_slider_gap_24">
          <div class="cs_slider_container" data-autoplay="0" data-loop="1" data-speed="600" data-center="0"
            data-variable-width="0" data-slides-per-view="responsive" data-xs-slides="1" data-sm-slides="2" data-md-slides="3" data-lg-slides="3" data-add-slides="3">
            <div class="cs_slider_wrapper">
              <?php foreach (mmh_published_posts(6) as $post) : ?>
              <div class="cs_slide">
                <?php include __DIR__ . '/includes/post-card.php'; ?>
              </div>
              <?php endforeach; ?>
            </div>
          </div>
          <div class="cs_pagination cs_style_2"></div>
        </div>
      </div>
      <div class="cs_height_120 cs_height_lg_80"></div>
    </section>
    <!-- End Blog Section -->
    
<?php include __DIR__ . '/includes/footer.php'; ?>
