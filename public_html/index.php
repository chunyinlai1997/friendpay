<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <title>FriendPay -- Online Payment System</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="Free Bootstrap Theme by BootstrapMade.com">
  <meta name="keywords" content="free website templates, free bootstrap themes, free template, free bootstrap, free website template">
  <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Open+Sans|Candal|Alegreya+Sans">
  <link rel="stylesheet" type="text/css" href="css/font-awesome.min.css">
  <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
  <link rel="stylesheet" type="text/css" href="css/imagehover.min.css">
  <link rel="stylesheet" type="text/css" href="css/style.css">
</head>

<body>
  <nav class="navbar navbar-default navbar-fixed-top">
    <div class="container">
      <div class="navbar-header">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
          <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand" href="index">Friend<span>Pay</span></a>
      </div>
      <div class="collapse navbar-collapse" id="myNavbar">
        <ul class="nav navbar-nav navbar-right">
          <li><a href="#feature">Features</a></li>
          <li><a href="#organisations">How it works</a></li>
          <li><a href="#courses">Send</a></li>
          <li><a href="#pricing">Request</a></li>
          <li><a href="app/sign-in">Sign in</a></li>
          <li class="btn-trial"><a href="app/sign-up">Sign Up</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <div class="modal fade" id="login" role="dialog">
    <div class="modal-dialog modal-sm">

      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title text-center form-title">Login</h4>
        </div>
        <div class="modal-body padtrbl">

          <div class="login-box-body">
            <p class="login-box-msg">Sign in to start your session</p>
            <div class="form-group">
              <form name="" id="loginForm">
                <div class="form-group has-feedback">

                  <input class="form-control" placeholder="Username" id="loginid" type="text" autocomplete="off" />
                  <span style="display:none;font-weight:bold; position:absolute;color: red;position: absolute;padding:4px;font-size: 11px;background-color:rgba(128, 128, 128, 0.26);z-index: 17;  right: 27px; top: 5px;" id="span_loginid"></span>

                  <span class="glyphicon glyphicon-user form-control-feedback"></span>
                </div>
                <div class="form-group has-feedback">

                  <input class="form-control" placeholder="Password" id="loginpsw" type="password" autocomplete="off" />
                  <span style="display:none;font-weight:bold; position:absolute;color: grey;position: absolute;padding:4px;font-size: 11px;background-color:rgba(128, 128, 128, 0.26);z-index: 17;  right: 27px; top: 5px;" id="span_loginpsw"></span>

                  <span class="glyphicon glyphicon-lock form-control-feedback"></span>
                </div>
                <div class="row">
                  <div class="col-xs-12">
                    <div class="checkbox icheck">
                      <!--<label>
                        <input type="checkbox" id="loginrem" > Remember Me
                      </label>-->
                    </div>
                  </div>
                  <div class="col-xs-12">
                    <button type="button" class="btn btn-green btn-block btn-flat" onclick="userlogin()">Sign In</button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>

  <div class="banner">
    <div class="bg-color">
      <div class="container">
        <div class="row">
          <div class="banner-text text-center">
            <div class="text-border">
              <h2 class="text-dec">Online Payment System</h2>
            </div>
            <div class="intro-para text-center quote">
              <p class="big-text">Send money to friends and family.</p>
              <p class="small-text">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Laudantium enim repellat sapiente quos architecto<br>Laudantium enim repellat sapiente quos architecto</p>
              <a href="#footer" class="btn get-quote">Try it for Free</a>
            </div>
            <a href="#feature" class="mouse-hover">
              <div class="mouse"></div>
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <section id="feature" class="section-padding">
    <div class="container">
      <div class="row">
        <div class="header-section text-center">
          <h2>Features</h2>
          <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Exercitationem nesciunt vitae,<br> maiores, magni dolorum aliquam.</p>
          <hr class="bottom-line">
        </div>
        <div class="feature-info">
          <div class="fea">
            <div class="col-md-4">
              <div class="heading pull-right">
                <h4>Latest Technologies</h4>
                <p>Donec et lectus bibendum dolor dictum auctor in ac erat. Vestibulum egestas sollicitudin metus non urna in eros tincidunt convallis id id nisi in interdum.</p>
              </div>
              <div class="fea-img pull-left">
                <i class="fa fa-css3"></i>
              </div>
            </div>
          </div>
          <div class="fea">
            <div class="col-md-4">
              <div class="heading pull-right">
                <h4>Toons Background</h4>
                <p>Donec et lectus bibendum dolor dictum auctor in ac erat. Vestibulum egestas sollicitudin metus non urna in eros tincidunt convallis id id nisi in interdum.</p>
              </div>
              <div class="fea-img pull-left">
                <i class="fa fa-drupal"></i>
              </div>
            </div>
          </div>
          <div class="fea">
            <div class="col-md-4">
              <div class="heading pull-right">
                <h4>Award Winning Design</h4>
                <p>Donec et lectus bibendum dolor dictum auctor in ac erat. Vestibulum egestas sollicitudin metus non urna in eros tincidunt convallis id id nisi in interdum.</p>
              </div>
              <div class="fea-img pull-left">
                <i class="fa fa-trophy"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section id="organisations" class="section-padding">
    <div class="container">
      <div class="row">
        <div class="col-md-6">
          <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
            <div class="orga-stru">
              <h3>65%</h3>
              <p>Say NO!!</p>
              <i class="fa fa-male"></i>
            </div>
          </div>
          <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
            <div class="orga-stru">
              <h3>20%</h3>
              <p>Says Yes!!</p>
              <i class="fa fa-male"></i>
            </div>
          </div>
          <div class="col-lg-4 col-md-4 col-sm-4 col-xs-4">
            <div class="orga-stru">
              <h3>15%</h3>
              <p>Can't Say!!</p>
              <i class="fa fa-male"></i>
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="detail-info">
            <hgroup>
              <h3 class="det-txt"> Is inclusive quality education affordable?</h3>
              <h4 class="sm-txt">(Revised and Updated for 2016)</h4>
            </hgroup>
            <p class="det-p">Donec et lectus bibendum dolor dictum auctor in ac erat. Vestibulum egestas sollicitudin metus non urna in eros tincidunt convallis id id nisi in interdum.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section id="cta-2">
    <div class="container">
      <div class="row">
        <div class="col-lg-12">
          <h2 class="text-center">Subscribe Now</h2>
          <p class="cta-2-txt">Sign up for our free weekly software design courses, we’ll send them right to your inbox.</p>
          <div class="cta-2-form text-center">
            <form action="#" method="post" id="workshop-newsletter-form">
              <input name="" placeholder="Enter Your Email Address" type="email">
              <input class="cta-2-form-submit-btn" value="Subscribe" type="submit">
            </form>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section id="work-shop" class="section-padding">
    <div class="container">
      <div class="row">
        <div class="header-section text-center">
          <h2>Upcoming Workshop</h2>
          <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Exercitationem nesciunt vitae,<br> maiores, magni dolorum aliquam.</p>
          <hr class="bottom-line">
        </div>
        <div class="col-md-4 col-sm-6">
          <div class="service-box text-center">
            <div class="icon-box">
              <i class="fa fa-html5 color-green"></i>
            </div>
            <div class="icon-text">
              <h4 class="ser-text">Mentor HTML5 Workshop</h4>
            </div>
          </div>
        </div>
        <div class="col-md-4 col-sm-6">
          <div class="service-box text-center">
            <div class="icon-box">
              <i class="fa fa-css3 color-green"></i>
            </div>
            <div class="icon-text">
              <h4 class="ser-text">Mentor CSS3 Workshop</h4>
            </div>
          </div>
        </div>
        <div class="col-md-4 col-sm-6">
          <div class="service-box text-center">
            <div class="icon-box">
              <i class="fa fa-joomla color-green"></i>
            </div>
            <div class="icon-text">
              <h4 class="ser-text">Mentor Joomla Workshop</h4>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section id="faculity-member" class="section-padding">
    <div class="container">
      <div class="row">
        <div class="header-section text-center">
          <h2>Meet Our Faculty Member</h2>
          <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Exercitationem nesciunt vitae,<br> maiores, magni dolorum aliquam.</p>
          <hr class="bottom-line">
        </div>
        <div class="col-lg-4 col-md-4 col-sm-4">
          <div class="pm-staff-profile-container">
            <div class="pm-staff-profile-image-wrapper text-center">
              <div class="pm-staff-profile-image">
                <img src="img/mentor.jpg" alt="" class="img-thumbnail img-circle" />
              </div>
            </div>
            <div class="pm-staff-profile-details text-center">
              <p class="pm-staff-profile-name">Bryan Johnson</p>
              <p class="pm-staff-profile-title">Lead Software Engineer</p>

              <p class="pm-staff-profile-bio">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec et placerat dui. In posuere metus et elit placerat tristique. Maecenas eu est in sem ullamcorper tincidunt. </p>
            </div>
          </div>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-4">
          <div class="pm-staff-profile-container">
            <div class="pm-staff-profile-image-wrapper text-center">
              <div class="pm-staff-profile-image">
                <img src="img/mentor.jpg" alt="" class="img-thumbnail img-circle" />
              </div>
            </div>
            <div class="pm-staff-profile-details text-center">
              <p class="pm-staff-profile-name">Bryan Johnson</p>
              <p class="pm-staff-profile-title">Lead Software Engineer</p>

              <p class="pm-staff-profile-bio">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec et placerat dui. In posuere metus et elit placerat tristique. Maecenas eu est in sem ullamcorper tincidunt. </p>
            </div>
          </div>
        </div>
        <div class="col-lg-4 col-md-4 col-sm-4">
          <div class="pm-staff-profile-container">
            <div class="pm-staff-profile-image-wrapper text-center">
              <div class="pm-staff-profile-image">
                <img src="img/mentor.jpg" alt="" class="img-thumbnail img-circle" />
              </div>
            </div>
            <div class="pm-staff-profile-details text-center">
              <p class="pm-staff-profile-name">Bryan Johnson</p>
              <p class="pm-staff-profile-title">Lead Software Engineer</p>

              <p class="pm-staff-profile-bio">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec et placerat dui. In posuere metus et elit placerat tristique. Maecenas eu est in sem ullamcorper tincidunt. </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section id="testimonial" class="section-padding">
    <div class="container">
      <div class="row">
        <div class="header-section text-center">
          <h2 class="white">See What Our Customer Are Saying?</h2>
          <p class="white">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Exercitationem nesciunt vitae,<br> maiores, magni dolorum aliquam.</p>
          <hr class="bottom-line bg-white">
        </div>
        <div class="col-md-6 col-sm-6">
          <div class="text-comment">
            <p class="text-par">"Proin gravida nibh vel velit auctor aliquet. Aenean sollicitudin, nec sagittis sem"</p>
            <p class="text-name">Abraham Doe - Creative Dırector</p>
          </div>
        </div>
        <div class="col-md-6 col-sm-6">
          <div class="text-comment">
            <p class="text-par">"Proin gravida nibh vel velit auctor aliquet. Aenean sollicitudin, nec sagittis sem"</p>
            <p class="text-name">Abraham Doe - Creative Dırector</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section id="courses" class="section-padding">
    <div class="container">
      <div class="row">
        <div class="header-section text-center">
          <h2>Courses</h2>
          <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Exercitationem nesciunt vitae,<br> maiores, magni dolorum aliquam.</p>
          <hr class="bottom-line">
        </div>
      </div>
    </div>
    <div class="container">
      <div class="row">
        <div class="col-md-4 col-sm-6 padleft-right">
          <figure class="imghvr-fold-up">
            <img src="img/course01.jpg" class="img-responsive">
            <figcaption>
              <h3>Course Name</h3>
              <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Magnam atque, nostrum veniam consequatur libero fugiat, similique quis.</p>
            </figcaption>
            <a href="#"></a>
          </figure>
        </div>
        <div class="col-md-4 col-sm-6 padleft-right">
          <figure class="imghvr-fold-up">
            <img src="img/course02.jpg" class="img-responsive">
            <figcaption>
              <h3>Course Name</h3>
              <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Magnam atque, nostrum veniam consequatur libero fugiat, similique quis.</p>
            </figcaption>
            <a href="#"></a>
          </figure>
        </div>
        <div class="col-md-4 col-sm-6 padleft-right">
          <figure class="imghvr-fold-up">
            <img src="img/course03.jpg" class="img-responsive">
            <figcaption>
              <h3>Course Name</h3>
              <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Magnam atque, nostrum veniam consequatur libero fugiat, similique quis.</p>
            </figcaption>
            <a href="#"></a>
          </figure>
        </div>
        <div class="col-md-4 col-sm-6 padleft-right">
          <figure class="imghvr-fold-up">
            <img src="img/course04.jpg" class="img-responsive">
            <figcaption>
              <h3>Course Name</h3>
              <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Magnam atque, nostrum veniam consequatur libero fugiat, similique quis.</p>
            </figcaption>
            <a href="#"></a>
          </figure>
        </div>
        <div class="col-md-4 col-sm-6 padleft-right">
          <figure class="imghvr-fold-up">
            <img src="img/course05.jpg" class="img-responsive">
            <figcaption>
              <h3>Course Name</h3>
              <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Magnam atque, nostrum veniam consequatur libero fugiat, similique quis.</p>
            </figcaption>
            <a href="#"></a>
          </figure>
        </div>
        <div class="col-md-4 col-sm-6 padleft-right">
          <figure class="imghvr-fold-up">
            <img src="img/course06.jpg" class="img-responsive">
            <figcaption>
              <h3>Course Name</h3>
              <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Magnam atque, nostrum veniam consequatur libero fugiat, similique quis.</p>
            </figcaption>
            <a href="#"></a>
          </figure>
        </div>
      </div>
    </div>
  </section>

  <section id="pricing" class="section-padding">
    <div class="container">
      <div class="row">
        <div class="header-section text-center">
          <h2>Our Pricing</h2>
          <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Exercitationem nesciunt vitae,<br> maiores, magni dolorum aliquam.</p>
          <hr class="bottom-line">
        </div>
        <div class="col-md-4 col-sm-4">
          <div class="price-table">

            <div class="pricing-head">
              <h4>Monthly Plan</h4>
              <span class="fa fa-usd curency"></span> <span class="amount">200</span>
            </div>

            <div class="price-in mart-15">
              <a href="#" class="btn btn-bg green btn-block">PURCHACE</a>
            </div>
          </div>
        </div>
        <div class="col-md-4 col-sm-4">
          <div class="price-table">

            <div class="pricing-head">
              <h4>Quarterly Plan</h4>
              <span class="fa fa-usd curency"></span> <span class="amount">800</span>
            </div>

            <div class="price-in mart-15">
              <a href="#" class="btn btn-bg yellow btn-block">PURCHACE</a>
            </div>
          </div>
        </div>
        <div class="col-md-4 col-sm-4">
          <div class="price-table">
            <div class="pricing-head">
              <h4>Year Plan</h4>
              <span class="fa fa-usd curency"></span> <span class="amount">1200</span>
            </div>

            <div class="price-in mart-15">
              <a href="#" class="btn btn-bg red btn-block">PURCHACE</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <footer id="footer" class="footer">
    <div class="container text-center">

      <h3>Start to use FriendPay now!</h3>

      <form class="mc-trial row">
        <div class="form-group col-md-3 col-md-offset-2 col-sm-4">
          <div class=" controls">
            <input name="name" placeholder="Enter Your Name" class="form-control" type="text">
          </div>
        </div>

        <div class="form-group col-md-3 col-sm-4">
          <div class=" controls">
            <input name="EMAIL" placeholder="Enter Your email" class="form-control" type="email">
          </div>
        </div>

        <div class="col-md-2 col-sm-4">
          <p>
            <button name="submit" type="submit" class="btn btn-block btn-submit">
            Submit <i class="fa fa-arrow-right"></i></button>
          </p>
        </div>
      </form>

      <ul class="social-links">
        <li><a href="#link"><i class="fa fa-twitter fa-fw"></i></a></li>
        <li><a href="#link"><i class="fa fa-facebook fa-fw"></i></a></li>
        <li><a href="#link"><i class="fa fa-google-plus fa-fw"></i></a></li>
        <li><a href="#link"><i class="fa fa-dribbble fa-fw"></i></a></li>
        <li><a href="#link"><i class="fa fa-linkedin fa-fw"></i></a></li>
      </ul>

      <div class="credits">

      </div>
    </div>
  </footer>

  <script src="js/jquery.min.js"></script>
  <script src="js/jquery.easing.min.js"></script>
  <script src="js/bootstrap.min.js"></script>
  <script src="js/custom.js"></script>
  <script src="contactform/contactform.js"></script>

</body>

</html>
