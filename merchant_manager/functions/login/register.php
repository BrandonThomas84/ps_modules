<?php
if(login_check($mysqli) == true) {
	if(isset($_POST['p'])){

		// The hashed password from the form
		$password = $_POST['p']; 
		// Create a random salt
		$random_salt = hash('sha512', uniqid(mt_rand(1, mt_getrandmax()), true));
		// Create salted password (Careful not to over season)
		$password = hash('sha512', $password.$random_salt);
		$username = $_POST['username'];
		$email = $_POST['email'];

		// Add your insert to database script here. 
		// Make sure you use prepared statements!
		if ($insert_stmt = $mysqli->prepare("INSERT INTO `mc_members` (username, email, password, salt) VALUES (?, ?, ?, ?)")) {    
		   $insert_stmt->bind_param('ssss', $username, $email, $password, $random_salt); 
		   // Execute the prepared query.
		   $insert_stmt->execute() or die(mysqli_error($mysqli));
		   echo "New user ($username) successfully created";
		} else { echo 'Could not add user.';}
	} else {
			echo messageReporting();
			echo '
			<!--START js Login-->
			<script type="text/javascript" src="functions/login/sha512.js"></script>
			<script type="text/javascript" src="functions/login/forms.js"></script>
			<!--END js Login-->
			<h1>Add New User</h1>
			<form action="' . $_SERVER['PHP_SELF'] . '?p=usrreg" method="post" name="login_form">
			   UserName: <input type="text" name="username" /><br />
			   Email: <input type="text" name="email" /><br />
			   Password: <input type="password" name="password" id="password"/><br />
			   <input type="button" value="Register New User" onclick="formhash(this.form, this.form.password);" />
			</form>';
		}
}
?>