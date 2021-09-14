<?php
namespace BookneticApp\Frontend\view;

use BookneticApp\Providers\Helper;
use BookneticApp\Providers\Date;

defined( 'ABSPATH' ) or die();

print $parameters['table'];
?>
<link rel="stylesheet" href="<?php print Helper::assets('css/info.css', 'Giftcards')?>">
<script type="text/javascript" src="<?php print Helper::assets('js/giftcard.js', 'Giftcards')?>"></script>
<script type="text/javascript" src="<?php print Helper::assets('js/info.js', 'Giftcards')?>"></script>
