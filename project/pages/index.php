<?php 

require_once('../templates/common.php');
require_once('../templates/home.php');
require_once('../templates/trainers.php');
require_once('../templates/classes.php');

output_header();
output_hero();
output_mission();
output_featured_classes();
output_featured_trainers();
output_footer();

?>