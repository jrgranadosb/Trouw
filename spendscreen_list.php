<?php
$page = 'spendscreen';
include('includes/functions.php');
include('includes/authentication.php');
include('export_csv/export_report.php');
include('includes/header.php');

    //search
    $WHERE = '';

    $_USER_ID = $_SESSION['user_info']['id'];
    if ($_SESSION['user_info']['user_role_id'] == 3) {
        $WHERE .= " AND `spend_screen`.`created_by` = '{$_USER_ID}'";
    }

    $search = secure($_GET['search']);

    if ($search != '') {
        $WHERE .= " AND ( `spend_screen`.`voucher_no` LIKE '%" . $search . "%'
        OR `spend_screen`.`date` LIKE '%" . $search . "%'
        OR `spend_screen`.`report_status` LIKE '%" . $search . "%'
        OR spend_screen.report_name LIKE '%" . $search . "%')";
    }

    //delete all checkbox
    $ids = $_GET['ids'];
    $submit = $_GET['submit'];
    if (isset($ids) && $submit == 'Delete') {
        $db->query("DELETE FROM `spend_screen` WHERE `id` IN(" . join(',', $ids) . ");");
    }

    //fetch data and pagination
    $clients = $db->query("SELECT * FROM `spend_screen`");
    $total_rows = mysqli_num_rows($clients);
    $per_page = 20;
    $total_page = ceil($total_rows / $per_page);
    $page_no = ($_GET['page']);

    if ($_GET['page'] == "") {
        $start = "0";
    } else {
        $start = ceil($_GET['page'] - 1) * $per_page;
    }
    $SQL = "SELECT
        CONCAT ('FACE66-L-001-',`LPAD`(`spend_screen`.`id`,10,'0')) As voucher_no
        , `spend_screen`.`id` AS ssp_id
        , `spend_screen`.`report_name` AS report_name
        , `spend_screen`.`tax_exemption` AS tax_exemption
        , `spend_screen`.`voucher_no` AS voucher_no
        , `spend_screen`.`date` AS voucher_date
        , `spend_screen`.`month` AS `month`
        , `spend_screen`.`year` AS `year`
        , `spend_screen`.`trade_name` AS trade_name
        , `spend_screen`.`id_tax_trade` AS id_tax_trade
        , `spend_screen`.`manager_status` AS `manager_status`
        , `spend_screen`.`taxpayer_id` AS taxpayer_id
        , `spend_screen`.`currency` AS currency
        , `spend_screen`.`total_expenses` AS total_expenses
        , `spend_screen`.`id_tax_trade` AS total_expenses
        , `spend_screen`.`comments` AS comments
        , `spend_screen`.`voucher_file` AS voucher_file
        , `spend_screen`.`created_on` AS created_on
        , `spend_screen`.`created_by` AS created_by
        , `spend_screen`.`deductible` AS deductible
        , `spend_screen`.`spending_class` AS spending_class
        , `type_spend`.`id` AS typespend_id
        , `type_spend`.`type_spend` AS type_spend
        , `users`.`user_name` AS user_name
        , `users`.`user_id` AS user_id
        , `users`.`email_authorize` AS email_authorize
        , `tax_payers`.`id` AS tax_payer_id
        , `tax_payers`.`tax_number` AS tax_number
        , `tax_payers`.`company_name` AS company_name
        , `center_cost`.`center_cost` AS center_cost
        , `center_cost`.`description_center_cost` AS description_center_cost
    FROM `spend_screen`
        INNER JOIN `type_spend` ON (`spend_screen`.`typespend_id` = `type_spend`.`id`)
        INNER JOIN `users` ON (`spend_screen`.`created_by` = `users`.`id`)
        INNER JOIN `tax_payers` ON (`users`.`taxpayer_id` = `tax_payers`.`id`)
        INNER JOIN `center_cost` ON (`spend_screen`.`centercost_id` = `center_cost`.`id`)

    WHERE 1 $WHERE GROUP BY report_name ORDER BY ssp_id DESC";

    if ($page_no != 'All') {
        $SQL .= " LIMIT " . $start . "," . $per_page;
    }

    if ($page_no == 0) {
        $page_no = 1;
    }
    $spendscreen_data = $db->query($SQL);
    $num_rows = $db->query("SELECT FOUND_ROWS() as total")->fetch_object()->total;
    $total_page = ceil($num_rows / $per_page);
    ?>

<div class="container">

    <div class="show-on-small hide-on-large-only">
        <div class="minigap"></div>
        <div class="col s12">
            <div class="top_datetime">
                <?php include('datetime.php'); ?>
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="col s12">
            <center><h5>Spend Screen</h5></center>
        </div>
    </div>

    <div class="hide-on-small-only show-on-large">
        <div class="minigap"></div>
        <i class="material-icons iconz">comments</i>
        <?php include('datetime.php'); ?>
        <h5>Spend Screen</h5>
    </div>

    <div class="card-panel panel_color"><i class="material-icons">assignment</i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Spend Screen Recods</div>
    <div class="card-panel">
        <!--add button-->
        <div class="custom_btn">
            <a href="<?php echo $site_url;?>/add_spendscreen.php"><span class="btn-floating btn-large waves-effect waves-light blue-grey darken-4">
            <span class="tooltipped" data-position="top" data-delay="50" data-tooltip="Add New"><i class="material-icons">library_add</i></span></span></a>
        </div>
        <form method="get">
        <div class="table_responsive">
            <table class="listing highlight bordered centered">
            <thead>
            <td colspan="9">
                <nav>
                    <div class="nav-wrapper searcheee">
                        <div class="input-field">
                            <input type="text" name="search" value="<?php echo $_GET['search'] ?>" placeholder="search your record...">
                            <label for="search"><i class="material-icons">search</i></label>
                        </div>
                    </div>
                </nav>
            </td>
            </thead>
            <thead class="thead_bg">
            <tr>
                <th data-field="check_all">
                    <span class="tooltipped" data-position="top" data-delay="50" data-tooltip="Select All">
                    <input type="checkbox" name="title" class="filled-in checkAll" id="filled-in-box">
                    <label for="filled-in-box"></label></span>
                </th>
                <th>S. #</th>
                <th>Report Name</th>
                <th>Tax Number</th>
                <th>Report Date</th>
                <th>Created By</th>
                <th>Created On</th>
                <th width="150">Action</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $s = 1;
            if (mysqli_num_rows($spendscreen_data) > 0) {
                if ($_GET['page'] == "" || $_GET['page'] == "All") {
                    $s = 1;
                } else {
                    $s = ceil($_GET['page'] - 1) * $per_page;
                    $s = $s + 1;
                }
                while ($row = $spendscreen_data->fetch_assoc()) {

                    /*if ($row['tax_exemption'] != 'Exempt') {
                        $total_expenses = $row['total_expenses'];
                        $subtotal_exp = $total_expenses / '1.12';
                        $tax = $total_expenses - $subtotal_exp;
                    } else {
                        $total_expenses = $row['total_expenses'];
                        $subtotal_exp = $total_expenses;
                        $tax = $total_expenses - $subtotal_exp;
                    }

                    $totalExp += $row['total_expenses'];
                    $subtotalExp += $total_expenses / '1.12';
                    $totalTax += $subtotal_exp - $total_expenses;*/

                    if ($row['tax_exemption'] != 'Si') {
                        $total_expenses = $row['total_expenses'];

                          if ($row['tax_included'] = '1'){
                              $subtotal_exp = $total_expenses / (1+($row['tax_percent']/100));
                              $tax = $total_expenses - $subtotal_exp;
                          } else {
                              $subtotal_exp = $row['total_expenses'];
                              $tax = $subtotal_exp * ($row['tax_percent']/100);
                              $total_expenses = $row['total_expenses'] + $tax;
                            }
                    } else {
                        $total_expenses = $row['total_expenses'];
                        $subtotal_exp = $total_expenses;
                        $tax = $total_expenses - $subtotal_exp;
                    }

                      $totalExp += $total_expenses;
                      $subtotalExp += $subtotal_exp;
                      $totalTax += $tax;

                    $no_image = '<img src="images/noimg.png" class="circle responsive-img valign tooltipped" width="30" alt="img" data-position="top" data-delay="100" data-tooltip="not found">';
                    $image = '<a href="images/voucher/' . $row['voucher_file'] . '" class="fancybox" data-fancybox-group="gallery" title="">
                             <img src="images/voucher/' . $row['voucher_file'] . '" class="circle responsive-img valign tooltipped sameWH" alt="img" data-position="top" data-delay="50" data-tooltip="click to zoom"></a>';

                    if ($row['report_status'] == 'Approved'){
                        $reportStatus = '<span class="badgeCustom green"> Approved </span>';
                    }elseif ($row['report_status'] == 'Sent'){
                        $reportStatus = '<span class="badgeCustom orange"> Sent </span>';
                    }elseif ($row['report_status'] == 'Reject'){
                        $reportStatus = '<span class="badgeCustom red"> Reject </span>';
                    }elseif ($row['report_status'] == 'Pending'){
                        $reportStatus = '<span class="badgeCustom blue"> Pending </span>';
                    }
            ?>
            <tr style="text-transform: capitalize; font-size: 13px">
                <td>
                    <input type="checkbox" name="ids[]" class="filled-in"
                           value="<?php echo $row['ssp_id'] ?>" id="checkbox_<?php echo $row['ssp_id']; ?>">
                    <label for="checkbox_<?php echo $row['ssp_id']; ?>"></label>
                </td>
                <td><?php echo $s; ?></td>
                <!--<td class="imgbdr"><?php /*echo($row['voucher_file'] != '' ? $image : $no_image); */?></td>-->
                <!--<td class="imgbdr"><?php /*echo($row['voucher_file'] != '' ? $image : $no_image);*/?></td>-->
                <td><?php echo $row['report_name'];?></td>
                <!--<td><?php /*echo date('M d, Y', strtotime($row['voucher_date']));*/?></td>-->
                <td><?php echo $row['tax_number'];?></td>
                <td><?php echo $row['month'].', '.$row['year']; ?></td>
                <td><?php echo $row['user_name'];?></td>
                <td><?php echo date('M d, Y', strtotime($row['created_on'])); ?></td>
                <td>
                    <a href="<?php echo $site_url;?>/spendscreen_list_all.php?allReport=<?php echo $row['report_name'];?>" class="tooltipped" data-position="top" data-delay="50" data-tooltip="View All"><i class="material-icons">menu</i></a>
                    <a href="<?php echo $site_url;?>/expenses_report.php?report_name=<?php echo $row['report_name']; ?>" class="tooltipped" data-position="top" data-delay="50" data-tooltip="View Report"><i class="material-icons">desktop_windows</i></a>
                    <a href="<?php echo $site_url;?>/create_pdf.php?report_name=<?php echo $row['report_name']; ?>" class="tooltipped" data-position="top" data-delay="50" data-tooltip="Create PDF"><i class="material-icons">picture_as_pdf</i></a>
                    <a href="<?php echo $site_url;?>/print_report.php?report_name=<?php echo $row['report_name']; ?>" target="_blank" class="tooltipped" data-position="top" data-delay="50" data-tooltip="Print Out"><i class="material-icons"><desktop></desktop>local_printshop</i></a>
                    <a href="<?php echo $site_url; ?>/spendscreen_list.php?report_name=<?php echo $row['report_name']; ?>&export=1&m_status=<?php echo $row['manager_status'];?>" class="tooltipped" data-position="top" data-delay="50" data-tooltip="Export in Excel"><i class="material-icons"><desktop></desktop>cloud_download</i></a>
                </td>
            </tr>
            <?php $s++;}} ?>
            </tbody>
        </table>

            <div class="minigap"></div>
            <p><b>Note:</b> Reports with <b style="color: #f00;">Approved Status</b> will show all options Only.</p>

        </div>
        <div class="minigap"></div>
        <div class="clearfix"></div>
        <!--================= pagination =================-->

        <div class="delAll left">
            <button type="submit" name="submit" value="Delete" class="tooltipped btn-floating btn-small waves-effect waves-light red" data-position="top" data-delay="50" data-tooltip="Delete All"> <i class="material-icons">delete_forever</i> </button>
        </div>

        <div class="data_ftr">
            <div class="totatl left">
                <p> <?php echo "Total No. of Record(s): <b>[ ".$num_rows." ]</b>"; ?> </p>
            </div>

            <div class="right">
                <?php
                $pagingData = ['limit' => $per_page, 'total' => $total_rows, 'page' => $page_no];
                echo createLinks($pagingData);
                ?>
            </div>
        </div>
        <div class="clearfix"></div>
    </div> <!--/card-panel-->
    </form>
</div>
<?php include('includes/footer.php'); ?>
