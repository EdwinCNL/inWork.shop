<footer class="footer">
    <div class="content__container">
        <div class="contact__container">
            <?php include "inc/footer-contact-from.php" ?>
        </div>
    </div>
    <div class="content__container">
	<?php
        if(wp_is_mobile())
        {
            include("inc/footer-contact-mobile.php");
        }
        elseif(!wp_is_mobile())
        {
            include("inc/footer-contact-desktop.php");
        }
	?>
    </div>


</footer>

</div>



<?php
wp_footer();
?>

</body>
</html>