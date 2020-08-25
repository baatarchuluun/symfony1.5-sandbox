<h1>Oauth login via Greensoft account</h1>

<?php if ($sf_user->isAuthenticated()): ?>
    FirstName: <?php echo $user['firstName']; ?> <br/>
    LastName: <?php echo $user['lastName']; ?><br/>
    UserId: <?php echo $user['id']; ?><br/>
    Email address: <?php echo $user['email']; ?>
<?php else: ?>
    <a href="<?php echo sfConfig::get('app_oauth_login_uri'); ?>">Login</a>
<?php endif; ?>
