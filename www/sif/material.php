<?php
require_once("sif.inc");
print serve_jquery_datatable(connect(), "material", $_REQUEST);
?>