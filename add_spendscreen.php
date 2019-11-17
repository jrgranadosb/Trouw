<?php
$page = 'spendscreen';
include('includes/functions.php');
include('includes/authentication.php');
include('includes/header.php');
$_USER = $_SESSION['user_info']['id'];

if (isset($_GET['id'])) {
    $get_data = $db->query("SELECT
        `temp_spend_screen`.`id` AS spendscreen_id
        , `temp_spend_screen`.`report_name` AS report_name
        , `temp_spend_screen`.`voucher_no` AS voucher_no
        , `temp_spend_screen`.`report_status` AS report_status
        , `temp_spend_screen`.`date` AS spendscreen_date
        , `temp_spend_screen`.`trade_name` AS trade_name
        , `temp_spend_screen`.`id_tax_trade` AS id_tax_trade
        , `temp_spend_screen`.`currency` AS currency
        , `temp_spend_screen`.`total_expenses` AS total_expenses
        , `temp_spend_screen`.`comments` AS comments
        , `temp_spend_screen`.`voucher_file` AS voucher_file
        , `temp_spend_screen`.`created_on` AS created_on
        , `temp_spend_screen`.`deductible` AS deductible
        , `temp_spend_screen`.`month` AS month
        , `temp_spend_screen`.`year` AS year
        , `temp_spend_screen`.`date` AS date
        , `temp_spend_screen`.`taxpayer_id` AS taxpayer_id
        , `temp_spend_screen`.`tax_exemption` AS tax_exemption
        , `temp_spend_screen`.`spending_class` AS spending_class
        , `type_spend`.`id` AS typespend_id
        , `type_spend`.`type_spend` AS type_spend
        , `center_cost`.`id` AS center_cost_id
        , `center_cost`.`center_cost` AS center_cost
        , `center_cost`.`description_center_cost` AS description_center_cost
        , `tax_payers`.`company_name` AS company_name
    FROM
        `temp_spend_screen`
        INNER JOIN `type_spend` ON (`temp_spend_screen`.`typespend_id` = `type_spend`.`id`)
        INNER JOIN `tax_payers` ON (`temp_spend_screen`.`taxpayer_id` = `tax_payers`.`id`)
        INNER JOIN `center_cost` ON (`temp_spend_screen`.`centercost_id` = `center_cost`.`id`)
    WHERE `temp_spend_screen`.`id` = '" . $_GET['id'] . "'");
    $row = $get_data->fetch_assoc();
}
?>

<style>
    input{
        text-transform: none !important;
    }
</style>

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
            <center><h5><?php echo ($_GET['id'] > 0 ? 'Update Spend Screen' : 'Add New Spend Screen'); ?></h5></center>
        </div>
    </div>

    <div class="hide-on-small-only show-on-large">
        <div class="minigap"></div>
        <i class="material-icons iconz">work</i>
        <?php include('datetime.php'); ?>
        <h5><?php echo ($_GET['id'] > 0 ? 'Update Spend Screen' : '&nbsp;&nbsp;&nbsp;&nbsp;Add New Spend Screen'); ?></h5>
    </div>

    <div class="card-panel panel_color"><i class="material-icons">assignment</i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Información Ingreso Gastos
        <div class="bkpos">
            <div class="lista">
                <a href="spendscreen_list.php"><i class="material-icons">arrow_back</i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Back</a>
            </div>
        </div>
    </div>
    <div class="card-panel">
        <div class="row">
            <form id="formValidation" method="post" action="<?php echo $site_url;?>/includes/spendscreen_query.php" class="col s12 m6 offset-m3 l6 offset-l3" enctype="multipart/form-data">
                <input type="hidden" name="id" id="id" value="<?php echo $_GET['id']; ?>">
                <input type="hidden" name="action" id="action" value="<?php if ($_GET['id'] > 0) { echo 'update'; } else { echo 'add'; } ?>">


                <?php
                  $SQL = $db->query("SELECT * FROM users WHERE id = $_USER ORDER BY id DESC");
                  $user_row = $SQL->fetch_assoc();
                ?>

                <?php
                  $current_date = date('y-m');
                  $current_month = date('F');
                  $current_year = date('Y');
                  $SQL=$db->query("SELECT (count(*)+1000) total FROM spend_screen WHERE taxpayer_id = $user_row[taxpayer_id] and created_by = $user_row[id] and year = $current_year and month= $current_month");
                  $current_row = $SQL->fetch_assoc();
                ?>
                <div class="row">

                    <div class="input-field col s6">
                        <input id="report_name" name="report_name" value="<?php echo $_USER.'-'.$current_date.'-'.$current_row['total'];?>" readonly type="text" class="validate[required]">
                        <label for="report_name">Nombre Reporte</label>
                    </div>

                    <div class="row">
                        <div class="input-field col s12">
                            <select name="taxpayer_id" id="taxpayer_id"  class="browser-default soflow validate[required]">
                                <option value="" selected>-- Seleccione la Compañia --</option>
                                <?php $SQL = $db->query("SELECT * FROM tax_payers ORDER BY id DESC");
                                while ($taxpayer_row = $SQL->fetch_assoc()) {
                                ?>
                                    <option value="<?php echo $taxpayer_row['id'];?>" <?php echo ($taxpayer_row['id'] == $row['taxpayer_id'] ? 'selected' : ''); ?>> <?php echo $taxpayer_row['tax_number'] . ' - ' . $taxpayer_row['company_name'];?></option>
                                <?php } ?>
                            </select>
                        </div>
                      </div>

                    <div class="input-field col s6">
                        <input id="voucher_no" name="voucher_no" value="<?php echo $row['voucher_no'];?>" type="text" class="validate[required]">
                        <label for="voucher_no">Numero Factura</label>
                    </div>

                    <div class="input-field col m4 s6">
                        <input type="date" id="date" value="<?php echo $row['date'];?>" name="date" class="datepicker" placeholder="Pick voucher date">
                    </div>

                    <div class="row">

                        <div class="input-field col m4 s6">
                            <input id="id_tax_trade" maxlength="15" name="id_tax_trade" value="<?php echo $row['id_tax_trade'];?>" type="text" class="validate[required]">
                            <label for="id_tax_trade">Nit Proveedor</label>
                        </div>

                        <div class="input-field col m4 s6">
                            <input id="trade_name" name="trade_name" value="<?php echo $row['trade_name'];?>" type="text" class="validate[required]">
                            <label for="trade_name">Nombre Proveedor</label>
                        </div>

                    </div>

                    <div class="row">

                        <div class="input-field col m4 s6">
                            <input id="total_expenses" name="total_expenses" value="<?php echo $row['total_expenses'];?>" type="text" class="validate[required]">
                            <label for="total_expenses">Total Gasto</label>
                        </div>

                        <div class="input-field col m4 s6">
                            <select name="currency" class="browser-default soflow validate[required]">
                                <option value="" selected>-- Moneda --</option>
                                <option value="Dollar" <?php echo ($row['currency'] == 'Dollar' ? 'selected' : ''); ?>> Dollar </option>
                                <option value="GTQ" <?php echo ($row['currency'] == 'GTQ' ? 'selected' : ''); ?>> GTQ </option>
                                <option value="EUR" <?php echo ($row['currency'] == 'EUR' ? 'selected' : ''); ?>> EUR </option>
                            </select>
                        </div>

                        <div class="input-field col m4 s6">
                                <input id="tax_exemption" name="tax_exemption" value="<?php echo 'Si';?>" readonly type="text" class="validate[required]">
                                <label for="report_name">-- Exento --</label>
                        </div>

                    </div>

                    <div class="input-field col m4 s6">
                      <select name="deductible"  class="browser-default soflow validate[required]">
                          <option value="" selected>-- deducible --</option>
                          <option value="1" <?php echo $row['deductible'] == '1' ? 'selected' : ''; ?>> SI </option>
                          <option value="0" <?php echo $row['deductible'] == '0' ? 'selected' : ''; ?>> NO </option>
                      </select>
                    </div>

                    <div class="input-field col s8">
                        <select name="centercost_id" class="browser-default soflow">
                            <option value="" selected>-- Select Center Cost --</option>
                            <?php $SQL = $db->query("SELECT * FROM center_cost ORDER BY id DESC");
                            while ($center_cost_row = $SQL->fetch_assoc()) { ?>
                                <option value="<?php echo $center_cost_row['id']; ?>" <?php echo($center_cost_row['id'] == $row['center_cost_id'] ? 'selected' : ''); ?>> <?php echo $center_cost_row['center_cost'] . ' - ' . $center_cost_row['description_center_cost']; ?> </option>
                            <?php } ?>
                        </select>
                    </div>

                </div>


                <div class="row">
                    <div class="input-field col m4 s6">
                        <select name="typespend_id"  class="browser-default soflow validate[required]">
                            <option value="" selected>-- Seleccione tipo gasto --</option>
                            <?php $SQL = $db->query("SELECT * FROM type_spend ORDER BY id DESC");
                            while ($spend_row = $SQL->fetch_assoc()) {
                            ?>
                            <option value="<?php echo $spend_row['id'];?>" <?php echo ($spend_row['id'] == $row['typespend_id'] ? 'selected' : ''); ?>> <?php echo $spend_row['type_spend'];?> </option>
                            <?php } ?>
                        </select>
                    </div>


                    <div class="input-field col s4">
                        <select name="spending_class" class="browser-default soflow validate[required]">
                            <option value="" selected>-- Liquidacion --</option>
                            <option value="T.C.Q" <?php echo ($row['spending_class'] == 'T.C.Q' ? 'selected' : ''); ?>> T.C.Q </option>
                            <option value="T.C.$" <?php echo ($row['spending_class'] == 'T.C.$' ? 'selected' : ''); ?>> T.C. $ </option>
                            <option value="Gastos" <?php echo ($row['spending_class'] == 'Gastos' ? 'selected' : ''); ?>> Gastos </option>
                            <option value="Viaticos" <?php echo ($row['spending_class'] == 'Viaticos' ? 'selected' : ''); ?>> Viaticos </option>
                            <option value="Otros" <?php echo ($row['spending_class'] == 'Otros' ? 'selected' : ''); ?>> Otros </option>
                        </select>
                    </div>

                </div>

                <div class="row">
                    <div class="input-field col s12">
                        <textarea id="comments" name="comments" class="materialize-textarea"><?php echo $row['comments']; ?></textarea>
                        <label for="comments">Comentarios</label>
                    </div>
                </div>


                <div class="row">

                    <div class="input-field col s8">
                        <div class="file-field input-field">
                            <div class="btn">
                                <span>Voucher File</span>
                                <input type="file" name="voucher_file">
                            </div>
                            <div class="file-path-wrapper">
                                <input class="file-path" placeholder="Upload voucher file" type="text">
                            </div>
                        </div>
                    </div>

                    <div class="input-field col offset-s1 s3 imgbdr">
                      <?php echo($row['voucher_file'] != '' ? '<img src="/images/' . $row['voucher_file'] . '" class="responsive-img valign" width="65" alt="img">' : '<img src="/images/noimg.png" class="responsive-img valign" width="65" alt="img">'); ?>
                    </div>
                </div>
                <div class="gap"></div>
                <button class="btn waves-effect waves-light" type="submit"> Guarda Factura&nbsp;
                                    <i class="material-icons right">send</i> </button>
            </form>
        </div>
        <?php include ('temp_spendscreen.php');?>
    </div> <!--/card-panel-->
</div>
<?php
$year = $row['year'];
include('includes/footer.php');
?>
<script>
    (function ($) {
        $(document).ready(function () {

            /*$('#taxpayer_id').change(function () {
                var taxpayer_value = $(this).val();
                if (taxpayer_value == '555555') {
                    $('#company_name').show();
                } else {
                    $('#company_name').hide();
                }
            });

            $('#company_name').hide();*/

            /*select year*/
            $('#year').val('<?php echo $year;?>');

        });
    })(jQuery)
</script>
