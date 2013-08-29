<?php echo messageReporting(); ?>
<form action="functions/login/process_login.php" method="post" name="login_form">
   Email: <input type="text" name="email" /><br />
   Password: <input type="password" name="password" id="password"/><br />
   <input type="button" value="Login" onclick="formhash(this.form, this.form.password);" />
</form>