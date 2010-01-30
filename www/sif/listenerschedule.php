<?php
require_once("sif.inc");
print serve_jquery_datatable(connect(), "listener_active_schedule", $_REQUEST);
?>
