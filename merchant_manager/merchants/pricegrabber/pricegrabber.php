<?php sec_session_start(); if(login_check($mysqli) == true) { ?>
<div class="notes">
	<h2>Helpful Links</h2>
    <ul>
        <li><a href="https://partner.pricegrabber.com/mss_main.php?sec=2&ccode=#topsection2" target="_blank"title="Price Grabber Data Feed Requirements">Price Grabber Data Feed Requirements</a></li>
    </ul>
</div>
<?php ;} ?>