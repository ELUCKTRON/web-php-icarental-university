<?php

$errors = isset($_GET['errors']) ? urldecode($_GET['errors']) : '';




?>













<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>LoginAndSignUp</title>

  <link rel="stylesheet" href="css/loginAndSignUp.css">
</head>
<body>

<div class="form">
      <ul class="tab-group">
        <li class="tab active"><a href="#signup">Sign Up</a></li>
        <li class="tab"><a href="#login">Log In</a></li>
      </ul>

      <div class="tab-content">
        <div id="signup">
          <h1>Sign Up for Free</h1>

          <form action="Signup.php" method="post">

          <div class="top-row">
            <div class="field-wrap">
              <label>
                First Name<span class="req">*</span>
              </label>
              <input type="text" name="name" required autocomplete="off" />
            </div>

            <div class="field-wrap">
              <label>
                Last Name<span class="req">*</span>
              </label>
              <input type="text" name="lastName" required autocomplete="off"/>
            </div>
          </div>

          <div class="field-wrap">
            <label>
              Email Address<span class="req">*</span>
            </label>
            <input type="email" name="email" required autocomplete="off"/>
          </div>

          <div class="field-wrap">
            <label>
              Set A Password<span class="req">*</span>
            </label>
            <input type="password" name="password" required autocomplete="off"/>
          </div>

          <button type="submit" class="button button-block">Get Started</button>
          <?php if ($errors): ?>
              <span style="color: red;"><?= htmlspecialchars($errors) ?></span>
          <?php endif; ?>
          </form>

        </div>

        <div id="login">
          <h1>Welcome Back!</h1>

          <form action="Login.php" method="post">

            <div class="field-wrap">
            <label>
              Email Address<span class="req">*</span>
            </label>
            <input type="email" name="email" required autocomplete="off"/>
          </div>

          <div class="field-wrap">
            <label>
              Password<span class="req">*</span>
            </label>
            <input type="password" name="password" required autocomplete="off"/>
          </div>

          <p class="forgot"><a href="#">Forgot Password?</a></p>

          <button class="button button-block">Log In</button>
          <?php if ($errors): ?>
              <span style="color: red;"><?= htmlspecialchars($errors) ?></span>
          <?php endif; ?>
          </form>

        </div>

      </div><!-- tab-content -->

</div> <!-- /form -->

<script src="scripts/loginAndSignup.js"></script>

</body>
</html>
