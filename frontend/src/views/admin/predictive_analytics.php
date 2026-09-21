<?php

$page_title = "Admin Control Center · SwiftFreight";

include_once '../../includes/header.php';
require_once '../../helpers/api_helper.php';


?>

<!-- SIDEBAR INCLUDE -->
<?php include_once '../../includes/sidebar.php'; ?>

<!-- MAIN CONTENT AREA -->
<main class="flex-1 overflow-y-auto bg-[#F8FAFC] p-6 lg:p-8">

  <!-- TOP HEADER -->
  <?php include_once '../../components/top_header.php'; ?>

  <!-- DESCRIPTIVE ANALYTICS COMPONENT -->
  <div class="mt-6">
    <?php include_once 'components/predictive_analytics.php'; ?>
  </div>


</main>



<!-- FOOTER INCLUDE -->
<?php include_once '../../includes/footer.php'; ?>