<div class="section-container__contact-container">
    <div class="contact-container">
        <?php
        $pageUrlTest = get_permalink();
        if (strpos($pageUrlTest, 'local')){
            echo do_shortcode( '[contact-form-7 id="21" title="footer-contact-form"]');
        } else {
            echo do_shortcode('[contact-form-7 id="8" title="footer-contact-form"]');
        }
        ?>
    </div>
</div>