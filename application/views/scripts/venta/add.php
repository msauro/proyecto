<?php         
$this->title = "Nueva venta";
$this->headTitle($this->title);
?>
<?= $this->headLink()->prependStylesheet($this->baseUrl().'/admin-public/css/bootstrap/bootstrap-slider.css'); ?>
<!-- START BREADCRUMB -->
<ul class="breadcrumb">
    <li><a href="/">Panel Principal</a></li>
    <li><a href="/usuario/list">Venta</a></li>
    <li class="active">Nueva</li>
</ul>
<!-- END BREADCRUMB -->

<style>
.profile-controls .profile-control-left{display:none;}

</style>

<!-- PAGE CONTENT WRAPPER -->
<div class="page-content-wrap">

    <div class="row">
        <div class="col-md-12">

            <!-- START WIZARD WITH VALIDATION -->
            <div class="panel panel-default">
                <div class="panel-body">
                    <h3>Nueva venta</h3>                                
                    <div class="wizard show-submit form-horizontal">
                        <ul>
                            <li>
                                <a href="#step-1" class="step-1">
                                    <span class="stepNumber">1</span>
                                    <span class="stepDesc">Seleccionar<br /><small>Cliente</small></span>
                                </a>
                            </li>

                            <!-- <li>
                                <a href="#step-2" class="step-2">
                                    <span class="stepNumber">2</span>
                                    <span class="stepDesc">Choose<br /><small>Phone</small></span>
                                </a>
                            </li>
                            <li>
                                <a href="#step-3" class="step-3">
                                    <span class="stepNumber">3</span>
                                    <span class="stepDesc">Choose<br /><small>Location</small></span>
                                </a>
                            </li>
                            <li>
                                <a href="#step-4" class="step-4">
                                    <span class="stepNumber">4</span>
                                    <span class="stepDesc">Optional<br /><small>Driver</small></span>
                                </a>
                            </li> -->
                            <li>
                                <a href="#step-5" class="step-5">
                                    <span class="stepNumber">5</span>
                                    <span class="stepDesc">Seleccioanr<br /><small> productos</small></span>
                                </a>
                            </li>
                            <li>
                                <a href="#step-6" class="step-6">
                                    <span class="stepNumber">6</span>
                                    <span class="stepDesc">Seleccioanr<br /><small>Descuentos/Impuestos</small></span>
                                </a>
                            </li>
                            <!-- <li>
                                <a href="#step-7" class="step-7">
                                    <span class="stepNumber">7</span>
                                    <span class="stepDesc">Optional<br /><small>Delivery Time</small></span>
                                </a>
                            </li> -->
                        </ul>

                       
                        <div id="step-1">

                            <!-- START STRIPED TABLE SAMPLE -->
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h3 class="panel-title">clientes</h3>
                                    <div class="col-md-2" style="float:right;">
                                        <button class="btn btn-success btn-block">
                                            <a href="/admin_patients/add"><span class="fa fa-plus"></span> New</a>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="panel-body">
                                        <p>Busca cliente por razón social o ID.</p>
                                        <div class="col-md-6" style="padding-left:0px;">
                                            <div class="input-group">
                                                <div class="input-group-addon">
                                                    <span class="fa fa-search"></span>
                                                </div>
                                                <input id="patientsearch" name="search" type="text" class="form-control" placeholder="Who are you looking for?" value="<?= $this->patient; ?>"/>
                                                <div class="input-group-btn">
                                                    <button class="btn btn-primary btn-search-patient">Buscar</button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-3" style="padding-left:0px;padding-right:0px;padding-top:6px;width:110px;">
                                            <div class="result-search-patient"></div>
                                        </div>
                                        <div class="col-md-3" style="padding:0px;">
                                            <div class="input-group">
                                                <div class="col-md-12" style="position:relative;top:-9px;width:220px;">
                                                    <ul class="pagination pagination-sm pull-right push-down-10 push-up-10" style="width: 100%;display:none;">
                                                        <span id='back-patient' class='btn btn-default'>« anterior</span>
                                                        <span id='back-patient-disabled' class='btn btn-default' style='display:none;background-color: #DDD;cursor:no-drop;'>« anterior</span>
                                                        <span id='next-patient' class='btn btn-default'>siguiente »</span>
                                                        <span id='next-patient-disabled' class='btn btn-default' style='display:none;background-color: #DDD;cursor:no-drop;'>siguiente »</span>
                                                        <span class='pagination-data' style='position:relative;margin-left:5px;'></span>
                                                        <span id='page-patient' rel='1'></span>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div id="list-patient" class="col-md-12">
                                    <?php if( count($this->patients) == 0) : ?>
                                        <div class="col-md-12">
                                            <div class="panel panel-default">
                                                <div class="panel-body">
                                                    <p style="text-align:center;">Buscar cliente</p>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    <?php foreach($this->patients as $patient): ?>
                                        <div id="<?php echo $patient['id_patient'] ?>" title="<?php echo $patient['first_name'] .' ' . $patient['last_name'] ?>" rel="<?php echo $patient['id_dispenser'] ?>" class="select-patient col-md-4">
                                            <!-- CONTACT ITEM -->
                                            <div class="panel panel-default">
                                                <div class="profile-control-left">
                                                    <div class="panel-body profile">
                                                        <div class="profile-image">
                                                            <img src="<?php echo $patient['avatar_url'] ?>" alt="<?php echo $patient['username'] ?>" <?php echo $patient['status']==0 ? "style='border-color:#FF4D4D;'" : ""; ?> />
                                                        </div>
                                                        <div class="profile-data">
                                                            <div class="profile-data-name"><?php echo $patient['first_name'] .' ' . $patient['last_name'] ?></div>
                                                            <div class="profile-data-title"><?php echo $patient['username'] ?></div>
                                                        </div>
                                                        <div class="profile-controls">
                                                            <?php if($patient['expire_recommendation_id']<=date("Y-m-d") OR !$patient['recommendation_allowed']) : ?>
                                                            <a href="javascript:void()" class="profile-control-right" style="font-size:26px;border-color:#FF4D4D;">
                                                                <span class="fa fa-exclamation-circle"></span>
                                                            </a>
                                                        <?php endif; ?>
                                                        </div>
                                                    </div>      
                                                </div>                          
                                                <div class="panel-body" style="height:145px;">
                                                    <div class="contact-info">
                                                        <p><small>Phone</small><br/><?php echo $patient['phone'] ?></p>
                                                        <p><small>Doctor Recommendation</small><br/><?php echo $patient['recommendation_id'] ?></p>
                                                        <p><small>California ID / Driver ID</small><br/><?php echo $patient['driver_id'] ?></p>
                                                    </div>
                                                </div>                            
                                            </div>
                                            <!-- END CONTACT ITEM -->
                                        </div>
                                    <?php endforeach; ?>

                                </div>

                            </div>
                            <!-- END STRIPED TABLE SAMPLE -->
                        </div>

                        <!-- <div id="step-2">
                            <div id="list-phone" class="col-md-12">
                                <div class="panel panel-default">
                                    <div class="panel-body">
                                        <p>
                                            <button class="btn btn-success" data-toggle="modal" data-target="#modal_add_phone">Add new phone</button>
                                        </p>
                                        <div id="phones" class="table-responsive">
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th>#</th>
                                                        <th>Phone Number</th> 
                                                        <th></th>                                                   
                                                        <th></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div> -->

                       <!--  <div id="step-3">
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <p>
                                        <button class="btn btn-success" data-toggle="modal" data-target="#modal_add_location">Add new location</button>
                                    </p>
                                    <div id="locations" class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Name</th>
                                                    <th>Address</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div> -->

                        <!-- <div id="step-4">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h3 class="panel-title">Donation Map</h3>                                    
                                </div>
                                <div class="panel-body">
                                        <div class="col-md-8 col-xs-12">
                                             <div id="google_map_donation" style="width: 100%; height: 342px;"></div>                                   
                                        </div>
                                       <div class="col-md-3 col-xs-12">               
                                          <ul id="sortable">                                      
                                          </ul>
                                        </div>
                                </div>
                          
                            </div>   
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h3 class="panel-title">Driver (optional)</h3>
                                </div>
                                <div class="panel-body">
                                    <div class="table-responsive drivers">
                                        <table class="table table-striped">
                                            <tbody>
                                                    <?php                                                   
                                                    foreach ($this->drivers as $driver) :
                                                        ?>
                                                    <div class="col-md-6">
                                                        <div id="driver_<?php echo $driver['id_driver'] ?>" class="widget widget-default widget-carousel" style="border-bottom: 10px solid <?php echo $driver['color']; ?>;cursor:pointer;">
                                                            <div class="driver_id" style="display:none;"><?php echo $driver['id_driver'] ?></div>
                                                            <div class="owl-carousel driver_<?php echo $driver['id_driver']?>" id="owl-example">
                                                                <div>                                    
                                                                    <div class="widget-title"><?php echo $driver['first_name'].' '.$driver['last_name'] ?></div>
                                                                    <div class="widget-subtitle" style="font-size: 8px;<?php echo ($cont & 1)? 'color:#FFF' : ''; ?>"><em>(<?php echo $driver['area_name'] ?>)</em></div>
                                                                    <div class="widget-subtitle" style="<?php echo ($cont & 1)? 'color:#FFF' : ''; ?>">queue / total</div>
                                                                    <div class="widget-int"><?php echo $driver['pending'] . "/" . $driver['queue']; ?></div>
                                                                    <div class="widget-subtitle eta" style="<?php echo ($cont & 1)? 'color:#FFF' : ''; ?>"><strong>Next ETA: <?php echo $driver['eta'] ?></strong></div>
                                                                </div>
                                                            </div>
                                                        </div>         
                                                    </div>

                                                   <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div> -->

                        <div id="step-5">

                            <!-- START STRIPED TABLE SAMPLE -->
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h3 id="from_driver" class="panel-title">Productos</h3>
                                </div>
                                <div class="panel-body">
                                    <p>
                                        <div class="form-group">
                                            <label class="col-md-2 control-label" style="width:auto; text-align:left;">Search</label>
                                            <div class="col-md-4">
                                                <input id="product-search" class="form-control" placeholder="Search Products"/>
                                            </div>
                                            <div class="col-md-2">
                                                <button class="btn"><i class="glyphicon glyphicon-search"></i></button>
                                            </div>
                                        </div>
                                    </p>
                                    <div class="table-responsive products">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Nombre</th>
                                                    <th>Marca</th>
                                                    <th>Stock</th>
                                                    <th>Precio</th>
                                                    <th>Status</th>
                                                    <th style="width: 70px"></th>
                                                <tr>
                                            </thead>
                                            <tbody id="productList"></tbody>
                                        </table>
                                    </div>
                                    <span id="prev" class="btn btn-default"> Anterior </span>
                                    <span id="next" class="btn btn-default"> Siguiente </span>
                                    <span id="page" rel="1"></span>
                                </div>
                            </div>
                            <!-- END STRIPED TABLE SAMPLE -->
                        </div>
                        <div id="step-6">
                            <div id="summarydiscounts" style="display: none;">
                            <!-- START STRIPED TABLE SAMPLE -->
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h3 class="panel-title">Descuentos</h3>
                                </div>
                                <div class="panel-body">
                                   <!--  < ?php if($this->reward['enable_points']): ?>
                                        <b id="availablepoints" style="text-align: center;padding-top: 5px;";></b>                           
                                    <div class="form-group">
                                        <h6 style="text-align: center">< ?= $this->reward['pointslabel']?></h6>    
                                        <div class="col-md-12" style="text-align: center">
                                            <b style="margin-right: 15px;padding-top: 5px;">0</b>
                                            <input id="slider" type="text" data-slider-min="0" data-slider-max="0" data-slider-step="0" data-slider-value="0"><b id="points" style="margin-left: 15px;padding-top: 5px;";>0</b><b id="maxpoints" style="margin-left: 15px;padding-top: 5px;";></b><span class="fa fa-question-circle fa-lg col-md-1 qstdon" aria-hidden="true" data-toggle="tooltip" title="" data-original-title="The maximum amount of usable points is the minimum value between patient’s available points and the maximum points for donation set in Reward Program section."></span>
                                            <div style="text-align: center;">
                                                <span id="redeemspan">
                                                    &#9733;<strong id="redeempoints">0</strong> = $<strong id="reedemamount">0</strong>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    < ?php endif;?>  -->
                                    <div class="form-group">
                                        
                                        <label class="col-md-3 control-label">Coupon</label>
                                        <span id="couponspan">
                                            <strong id="couponamount"><span class="fa fa-ticket" style="float:none;color: #719D47;"></span></strong>
                                        </span>  
                                        <div class="col-md-6">
                                            <input id="coupon_value" type="text" class="form-control">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <h6 style="text-align: center"><u>Reward Discount</u></h6>  
                                        <div class="col-md-12" style="text-align: center;">
                                         <h3><span id="rdiscount">0%</span></h3>
                                          <input id="rdiscount_hidden" type="hidden" class="form-control" value="0">                       
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-3 control-label">Discount</label>
                                       
                                        <div class="col-md-3 " id="percent_fa">
                                              
                                            <select class="form-control select" id="discount_options">
                                                <option value="select_discount"><?php echo "Please select a discount" ?></option>
                                                <?php foreach ($this->discount as $i => $discount) {?> 
                                                <option value="<?php echo $discount['id_discount']; ?>" attr-amount="<?php echo $discount['amount']; ?>" attr-reason ="<?php echo $discount['reason']; ?>"> <?php echo $discount['name']; ?></option>
                                                <?php } ?> 
                                            </select>
                                        </div>
                                        <div class="input-group col-md-2">
                                            <input id="discount" name="discount" value="0" type="text" class="form-control" style="border-radius: 4px !important;" readonly>
                                            
                                        <span class="fa fa-question-circle fa-lg col-md-1 qstdon" aria-hidden="true" data-toggle="tooltip" title="Write a number to set the discount in a specific amount, or type '%' after the number to set it as a percentage"></span> 
                                        </div> 
                                        <div class="form-group" style="display:none" id="reason">
                                            <label class="col-md-3 control-label">Reason</label>
                                            <div class="col-md-6">
                                                <textarea class="form-control" name="reason" id="reason_value" ></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="panel panel-default" style="display:none">
                                <div class="panel-heading">
                                    <h3 class="panel-title">Fees</h3>
                                </div>
                                <div class="panel-body">
                                    
                                    <div class="form-group">
                                        <label class="col-md-3 control-label">Shipping</label>
                                        <div class="input-group col-md-3">
                                            <div class="input-group-addon">
                                                <span class="fa fa-truck"></span>
                                            </div>
                                            <input id="shipping" name="shipping" type="number" class="form-control" value="0" min="0" style="border-radius: 4px !important;">
                                            <input id="discount_hidden" type="hidden" class="form-control" value="0">
                                            <span class="fa fa-question-circle fa-lg col-md-1 qstdon" aria-hidden="true" data-toggle="tooltip" title="Add a surcharge for shipping concepts"></span> 
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-md-3 control-label">Convenience</label>
                                        <div class="input-group col-md-3">
                                            <div class="input-group-addon">
                                                <span class="fa fa-money"></span>
                                            </div>
                                            <input id="convenience" name="convenience" type="number" class="form-control" value="0" min="0" style="border-radius: 4px !important;">
                                            <input id="discount_hidden" type="hidden" class="form-control" value="0"> 
                                            <span class="fa fa-question-circle fa-lg col-md-1 qstdon" aria-hidden="true" data-toggle="tooltip" title="Add a surcharge for particular concepts"></span>
                                        </div> 
                                    </div>
                                                                   
                                </div>
                            </div>
                        </div>
                            
                        </div>
                        <div id="step-7">
                            <!-- START STRIPED TABLE SAMPLE -->
                            <div class="panel panel-default times"> 

                                <div class="panel-heading">
                                    <h3 class="panel-title">Add a donation note</h3>
                                </div>
                               <div class="panel-body note_step5">                                                   
                               </div>
                               <div class="panel-heading" style="margin-bottom: 20px;">
                                    <h3 class="panel-title time-allowed">Allowed Time for</h3>

                                </div>
                                <div>
                                    <label class="col-md-3 control-label">Future Deliveries</label>
                                    <div class="col-md-2">
                                            <div class="col-md-6 col-xs-6">
                                                <label class="switch">
                                                    <input name="admin_modules_enableall" id="enable_datetimepicker" type="checkbox" value="">      
                                                    <span></span>
                                                </label>
                                            </div>              
                                    </div>
                                    <div class='col-md-5' id='datetimepicker' style='display: none'>
                                        <div class="form-group">
                                            <div class='input-group date' id='datetimepicker1' >
                                                <input type='text' class="form-control" />
                                                <span class="input-group-addon">
                                                    <span class="glyphicon glyphicon-calendar" ></span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <button class="btn btn-success btn-update col-md-1" id='datetimepickerRefresh' style='display: none;'><span class="fa fa-refresh"> </span></button>
                                </div>
           
                                <div class="panel-body" id="panelBodyTime">
                                    <div class="list-group border-bottom">
                                    </div>
                                </div>
                            </div>
                            <!-- END STRIPED TABLE SAMPLE -->
                        </div>
                        <div id="summary">
                            <!-- START STRIPED TABLE SAMPLE -->
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h3 class="panel-title">Summary</h3>
                                </div>
                                <div class="panel-body">
                                    <form id="create-donation" role="form" class="form-horizontal">
                                        <input type="hidden" id="form-patient" name="id_patient">
                                        <input type="hidden" id="form-phone" name="id_phone">
                                        <input type="hidden" id="form-driver" name="id_driver">
                                        <input type="hidden" id="form-location" name="id_location">
                                        <input type="hidden" id="form-time" name="time">
                                        <input type="hidden" id="form-timeset" name="timeset">
                                        <input type="hidden" id="form-donation_note" name="donation_note">
                                        <input type="hidden" id="form-points" name="points">
                                        <input type="hidden" id="form-id_discount" name="id_discount">
                                        <input type="hidden" id="form-reason" name="reason">
                                        <input type="hidden" id="form-special-discount" name="special-discount">
                                        <input type="hidden" id="form-coupon" name="id_coupon">
                                        <input type="hidden" id="form-discount" name="discount">
                                        <input type="hidden" id="form-future_deliveries" name="future_deliveries">
                                        <ul style="padding:0px;">
                                            <li class="fa fa-square-o">
                                                <span id="selected-patient" class="stepDesc">Selected Patient: </span>
                                            </li>
                                            <li class="fa fa-square-o">
                                                <span id="selected-phone" class="stepDesc">Selected Phone: </span>
                                            </li>
                                            <li class="fa fa-square-o">
                                                <span id="selected-location" class="stepDesc">Selected Location: </span>
                                            </li>
                                            <li class="fa fa-square-o">
                                                <span id="selected-driver" class="stepDesc">Selected Driver: </span>
                                            </li>
                                            <li id="selected-products" class="fa fa-square-o">
                                                <span class="stepDesc">Selected Products:</span>
                                                <div class="table-responsive" style="margin-top:10px;">
                                                    <table class="table table-striped" style="font-size: 10px;display:none;">
                                                        <thead>
                                                            <tr>
                                                                <th>Qty</th>
                                                                <th>Name</th>
                                                                <!--th>Sub-Total</th-->
                                                                <th></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <?php if(!empty($this->tax)): ?>
                                             
                                                <input type="checkbox" id="tax_exempt_checkbox" name="tax_exempt" style="display:none">
                                                <span id="tax_exempt"></span>
                                                <span id="tax" class="label label-warning label-form fee" style="width: 100%;"></span>
                                                <?php endif; ?>
                                                <span id="fees" class="label label-warning label-form fee" style="width: 100%;"></span>
                                                <span id="total_dona" class="label label-warning label-form fee" style="width: 100%;"></span>
                                                <span id="total_cost" class="label label-warning label-form fee" style="width: 100%;"></span>
                                                <input id="total_cost_hidden" name="total" type="hidden" value="0">
                                                <input id="subtotal_cost_hidden" name="subtotal_products" type="hidden" value="0">
                                                <span id="tot_discount" class="label label-default label-form tot_discount" style="width: 100%;"></span>
                                                <span id="tot_donation" class="label label-success label-form tot_donation" style="width: 100%;"></span>
                                                <input id="tax_hidden" name="tax" type="hidden" value="0">
                                                <input id="fee_shipping" name="fee_shipping" type="hidden" value="0">
                                                <input id="fee_convenience" name="fee_convenience" type="hidden" value="0">
                                                 
                                            </li>
                                            <li class="fa fa-square-o">
                                                <span id="selected-time" class="stepDesc">Selected Time: </span>
                                            </li>
                                        </ul>
                                    </form>
                                </div>
                            </div>
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h3 id="previously_ordered" class="panel-title">Previously Ordered</br></h3>
                                </div>
                                <div id='previously_product_name'>

                                </div>
                            </div>
                        </div>
   
                        <!-- Modal -->
                         <div class="modal fade" id="discountModal" role="dialog">
                            <div class="modal-dialog">
                                <!-- Modal content-->
                                <div class="modal-content" >
                                    <div class="modal-header" id="discount-header">
                                      <button type="button" class="close" data-dismiss="modal">&times;</button>
                                      <h4 class="modal-title">Warning!</h4>
                                    </div>
                                    <div class="modal-body">
                                      <p>The selected discount is larger than the sale total</p>
                                      <!-- <button type="button" class="btn btn-default" data-dismiss="modal">Close</button> -->
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>                        
            <!-- END WIZARD WITH VALIDATION -->
        </div>
    </div>                    
    
</div>
<!-- END PAGE CONTENT WRAPPER -->                                                
<div id="spinner">        
</div>
<!-- MODALS -->
<!-- <div class="modal" id="modal_add_phone" tabindex="-1" role="dialog" aria-labelledby="defModalHead" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title" id="smallModalHead">Create new phone number</h4>
            </div>
            <form id="addphone-modal-form" class="form-horizontal" action="/admin_patients/addphoneajax" method="post" enctype="multipart/form-data" novalidate="novalidate">
                <div class="modal-body form-horizontal form-group-separated">                        
                    <div class="form-group">
                        <label class="col-md-3 control-label">Phone number</label>
                        <div class="col-md-9">
                            <input name="id_patient" id="id_patient" type="hidden" class="form-control">
                            <input name="number" id="number" type="text" class="form-control" placeholder="Enter the phone number">
                        </div>
                    </div>
                  
                </div>
                <div class="modal-footer">
                    <button id="save-add" type="button" class="btn btn-success">Save</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div> -->

<!-- <div class="modal" id="modal_edit_phone" tabindex="-1" role="dialog" aria-labelledby="defModalHead" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title" id="smallModalHead">Edit phone number</h4>
            </div>
            <form id="editphone-modal-form" class="form-horizontal">
                <div class="modal-body form-horizontal form-group-separated">                        
                    <div class="form-group">
                        <label class="col-md-3 control-label">Phone number</label>
                        <div class="col-md-9">
                            <input name="id_phone" id="id_phone" type="hidden" class="form-control">
                            <input name="number" id="number" type="text" class="form-control" placeholder="Enter the phone number">
                        </div>
                    </div>
                  
                </div>
                <div class="modal-footer">
                    <button id="save-edit" type="button" class="btn btn-success">Save</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div> -->

<!-- <div class="modal" id="modal_add_location" tabindex="-1" role="dialog" aria-labelledby="defModalHead" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title" id="smallModalHead">Create new location</h4>
            </div>
            <form id="addlocation-modal-form" class="form-horizontal" action="/admin_patients/addlocationajax" method="post" enctype="multipart/form-data" novalidate="novalidate">
                <div class="modal-body form-horizontal form-group-separated">                        
                    <div class="form-group">
                        <label class="col-md-3 control-label">Location name</label>
                        <div class="col-md-9">
                            <input name="id_patient" id="id_patient" type="hidden" class="form-control">
                            <input name="name" id="location_name" type="text" class="form-control" placeholder="Default">
                        </div>
                    </div>
                        <div class="form-group">
                        <label class="col-md-3 control-label">Number</label>
                        <div class="col-md-9">
                            <input name="number" id="number" type="text" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">Street</label>
                        <div class="col-md-9">
                            <input name="street" id="street" type="text" class="form-control">
                        </div>
                    </div>
                      <div class="form-group">
                        <label class="col-md-3 control-label">Apartment (optional)</label>
                        <div class="col-md-9">
                            <input name="apartment" id="apartment" type="text" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">City / Zipcode</label>
                        <div class="col-md-9">
                            <input name="city" id="city" type="text" class="form-control" placeholder="San Diego">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">State</label>
                        <div class="col-md-9">
                            <input name="state" id="state" type="text" value="CA" class="form-control">
                        </div>
                    </div>
                    
                
                    <div class="form-group">
                        <label class="col-md-3 control-label">Meetup</label>
                        <div class="col-md-9">
                            <input name="meetup" id="meetup" type="checkbox" class="checkbox pull-left">
                        </div>
                    </div>
                    <div class="form-group" style="display:none;">
                        <label class="col-md-3 control-label">Floor (optional)</label>
                        <div class="col-md-9">
                            <input name="floor" id="floor" type="text" class="form-control">
                        </div>
                    </div>
                  
                    <div class="form-group">
                        <label class="col-md-3 control-label">Latitude (optional)</label>
                        <div class="col-md-9">
                            <input name="lat" id="lat" type="text" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">Longitude (optional)</label>
                        <div class="col-md-9">
                            <input name="lon" id="lon" type="text" class="form-control">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">Note (optional)</label>
                        <div class="col-md-9">
                            <textarea name="note" id="note" class="form-control" placeholder="Your message" rows="3"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button id="save-add" type="button" class="btn btn-success">Save</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </form>
        </div>
    </div>
</div> -->

<!-- <div class="modal animated fadeIn" id="modal_edit_location" tabindex="-1" role="dialog" aria-labelledby="smallModalHead" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h4 class="modal-title" id="smallModalHead">Edit location</h4>
            </div>
             <form id="editlocation-modal-form" class="form-horizontal">
                <div class="modal-body form-horizontal form-group-separated">                        
                    <div class="form-group">
                        <label class="col-md-3 control-label">Location name</label>
                        <div class="col-md-9">
                            <input name="id_location" id="id_location" type="hidden" class="form-control" />
                            <input name="name" id="location_name" type="text" class="form-control" placeholder="Default"/>
                        </div>
                    </div>
                        <div class="form-group">
                        <label class="col-md-3 control-label">Number</label>
                        <div class="col-md-9">
                            <input name="number" id="number" type="text" class="form-control"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">Street</label>
                        <div class="col-md-9">
                            <input name="street" id="street" type="text" class="form-control"/>
                        </div>
                    </div>
                       <div class="form-group">
                        <label class="col-md-3 control-label">Apartment (optional)</label>
                        <div class="col-md-9">
                            <input name="apartment" id="apartment" type="text" class="form-control"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">City / Zipcode</label>
                        <div class="col-md-9">
                            <input name="city" id="city" type="text" class="form-control"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">State</label>
                        <div class="col-md-9">
                            <input name="state" id="state" type="text" class="form-control"/>
                        </div>
                    </div>              
                    <div class="form-group">
                        <label class="col-md-3 control-label">Meetup</label>
                        <div class="col-md-9">
                            <input name="meetup" id="meetup" type="checkbox" class="checkbox pull-left">                                                 
                        </div>
                    </div>                 
                    <div class="form-group">
                        <label class="col-md-3 control-label">Latitude (optional)</label>
                        <div class="col-md-9">
                            <input name="lat" id="lat" type="text" class="form-control"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">Longitude (optional)</label>
                        <div class="col-md-9">
                            <input name="lon" id="lon" type="text" class="form-control"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">Note (optional)</label>
                        <div class="col-md-9">
                            <textarea name="note" id="note_edit" class="form-control" placeholder="Your message" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <div id="google_map" style="width: 100%; height: 342px;"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button id="save-edit" type="button" class="btn btn-success">Guardar</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                </div>
            </form>
        </div>
    </div>
</div> -->
<!-- END MODALS -->

<!-- THIS PAGE PLUGINS -->
<script type="text/javascript" src="/admin-public/js/plugins/moment.min.js"></script>
<script type="text/javascript" src="/admin-public/js/plugins/bootstrap/bootstrap-datepicker.js"></script>
<script type="text/javascript" src="/admin-public/js/plugins/bootstrap/bootstrap-datetimepicker.js"></script>
<script type="text/javascript" src="/admin-public/js/plugins/bootstrap/bootstrap-file-input.js"></script>
<script type="text/javascript" src="/admin-public/js/plugins/bootstrap/bootstrap-select.js"></script>
<script type="text/javascript" src="/admin-public/js/plugins/tagsinput/jquery.tagsinput.min.js"></script>
<script type="text/javascript" src="/admin-public/js/jquery.validate.min.js"></script>
<script type="text/javascript" src="/admin-public/js/additional-methods.min.js"></script>
<!-- END THIS PAGE PLUGINS -->

<!-- THIS PAGE PLUGINS -->    
<script type='text/javascript' src='/admin-public/js/plugins/icheck/icheck.min.js'></script>
<script type="text/javascript" src="/admin-public/js/plugins/mcustomscrollbar/jquery.mCustomScrollbar.min.js"></script>
<script type="text/javascript" src="/admin-public/js/plugins/owl/owl.carousel.min.js"></script>
<script type="text/javascript" src="/admin-public/js/plugins/smartwizard/jquery.smartWizard-3.3.1.js"></script>
<script type="text/javascript" src="/admin-public/js/plugins/jquery-validation/jquery.validate.js"></script>
<script src="https://maps.google.com/maps/api/js?sensor=false" type="text/javascript"></script>
<script type="text/javascript" src="/admin-public/js/plugins/bootstrap/bootstrap-slider.js"></script>
<!-- END PAGE PLUGINS -->

<script>

$('.datepicker').datepicker({
         format: 'dd-mm-yyyy'       
});

$(document).ready(function(){

    <?php if ( isset($this->patient) ) : ?>
    setTimeout(function(){
      $('.btn-search-patient').click();
    }, 100);
    <?php endif; ?>


    $('#patientsearch').focus();

    $(".btn.btn-primary.pull-right").click(function(e){
        e.preventDefault();
        var params = $("#add-form").serialize();
        var action = $("#add-form").attr("action");
        result = panelAjax(action,params, function(data){
            if(data == true)
                 window.location.href = "/admin_donations";
        });
    });    

    $(".actionBar").insertBefore(".stepContainer")

    $('#discount_options').on('change', function(){
        var reason = $('option:selected', this).attr('attr-reason');
        var amount = $('option:selected', this).attr('attr-amount');       
        if(reason==1){
            $('#reason').show();
            $('#reason').css("display", "block");
            $('#discount').attr('readonly', false);
            $('#discount').val();
        }else{
            $('#reason').hide();
            $('#reason').css("display", "none");
            $('#discount').val(amount);
            $('#discount').attr('readonly', true);
            $('#reason_value').text('');
        }      
        $("#discount").trigger("keyup");     
    }); 
});

$(function() {
    var id_patient;
    var patient_dispenser;
    var id_location;
    var id_phone;
    var id_driver;
    var arrival_time;
    var latitude;
    var longitude;
    var products = new Array();
    var prices = {};
    var time;
    var is_ordered = false;

    //START - date time picker - select date and time - future deliveries
        var futureDeliveries; 
        var today = new Date()
        $('#datetimepicker1').datetimepicker({ 
            format: "hh:mm a / YYYY-MM-DD",
            stepping: 30
        });

        $('#enable_datetimepicker').on('change', function(){
            if ($('#enable_datetimepicker').is(':checked')) {
                futureDeliveries = 1;
                $('#datetimepicker').css('display','inline-block');
                $('#panelBodyTime').hide();
                $("#datetimepicker").on("dp.change", function () {
    
                    var futureDeliveries = $('#datetimepicker1 input').val(); 
                    var firstTime = futureDeliveries.substring(0, 5);
                    var timeAmPm = futureDeliveries.substring(6,8);
                    var secondTimeAmPm;
                    var secondTime = addMinutesFutureDeliveries(firstTime, '30');
                    var dateFutureDeliveries = futureDeliveries.substring(11, 21);
                    
                    if (firstTime=='11:30' && timeAmPm=='am') {
                        secondTimeAmPm = 'pm';
                        var finalTime = firstTime+' '+timeAmPm+' - '+secondTime+' '+secondTimeAmPm;
                    }else if (firstTime=='11:30' && timeAmPm=='pm') {
                        secondTimeAmPm = 'am';
                        var finalTime = firstTime+' '+timeAmPm+' - '+secondTime+' '+secondTimeAmPm;
                    }else{
                        var finalTime = firstTime+' '+timeAmPm+' - '+secondTime+' '+timeAmPm;
                    }
                    if(futureDeliveries!=''){
                        $('#panelBodyTime').show();
                        jQuery('#panelBodyTime .list-group-item').hide();
                        var overtime = $('.list-group-item.overtime'); 
                        if(overtime){$('.list-group-item.overtime').remove();}
                        $(".times .list-group").append('<a href="javascript:void(0);" class="list-group-item system-selected active overtime">'+ finalTime +' <i> / '+dateFutureDeliveries+' Future Deliveries</i></a>');
                        jQuery('#panelBodyTime .list-group-item.overtime').show();
                        jQuery('#panelBodyTime .list-group-item.overtime').click(false);
                        if ($('.list-group-item.overtime.active')) {
                            if (timeAmPm=='pm') {
                                firstTime = addMinutesFutureDeliveries(firstTime,'720');
                            }
                            time = firstTime+':00'+' '+secondTime+':00'+' '+dateFutureDeliveries;
                            if ($('#selected-time small')) {
                                $('#selected-time small').remove();
                            }
                            $("#selected-time").append('<small> '+finalTime+'</small>');
                            $("#form-time").val(time);
                            $("#form-timeset").val("admin");
                        }
                    }
                });
            }else{
                futureDeliveries = '';
                $('#datetimepicker').css('display','none');
                $('#panelBodyTime .list-group-item').show();
                $('#panelBodyTime').css('display','block');
                $('.list-group-item.overtime').remove();
                $(".times .list-group").append('<a href="javascript:void(0);" class="list-group-item overtime">Overtime</a>');
                $('#datetimepickerRefresh').hide(); 
            }   
        })
        
        function addMinutesFutureDeliveries(time, minsToAdd) {
            function D(J){ return (J<10? '0':'') + J;};
            var piece = time.split(':');
            var mins = piece[0]*60 + +piece[1] + +minsToAdd;

            return D(mins%(24*60)/60 | 0) + ':' + D(mins%60);  
        }  
    //END - date time picker - select date and time - future deliveries

    $(".actionBar .buttonFinish").click(function(e){
        if(!validateSteps("6")) return; 
        if (!$(".actionBar .buttonFinish").hasClass("buttonDisabled")){
            $(".buttonFinish").fadeOut(50,function(){
                $("#form-donation_note").val($("textarea#donation_note").val());      
                $("#form-id_discount").val($("#discount_options").val());  
                $("#form-reason").val($("#reason_value").val());  
                $("#form-discount").val($("#rdiscount_hidden").val());  
                $('#form-future_deliveries').val(futureDeliveries);
                
                var reason = $('#discount_options option:selected').attr('attr-reason');
                
                if (reason==1) {
                    if ($('#discount').val().includes("%")) {
                        discount = parseFloat($('#discount').val());
                    } 
                    else{
                        var total = parseFloat($("#subtotal_cost_hidden").val());
                        console.log(total);
                        disc = $('#discount').val();
                        discount = disc * 100 / total;
                    }             

                    $("#form-special-discount").val(discount); 

                }
                
                var params = {};

                params.data = $("#create-donation").serialize();    
                params.futureDeliveries = futureDeliveries;
                panelAjax("/admin_donations/setdonation",params, function(data){              
 
                    if(data){
                        if(is_ordered){                          
                            updateTimeDonations();                           
                        }
                        else{
                            setTimeout(function(){ window.location.href = "/admin_donations"; }, 2000);                               
                        }
                   }else{
                        $(".buttonFinish").fadeIn();
                        $(".btn.btn-default.btn-lg.pull-right.mb-control-close").click();
                    }
                });
            });
            // Armo el Popup
            $("#message-box-warning").children(".mb-container").children(".mb-middle").children(".mb-title").html("Please wait");
            $("#message-box-warning").children(".mb-container").children(".mb-middle").children(".mb-content").html("<p>The system is setting the new donation.</p>");
            $("#message-box-warning").children(".mb-container").children(".mb-middle").children(".mb-footer").children(".mb-control-close").hide();

            setInterval(function() {
                $("#message-box-warning").children(".mb-container").children(".mb-middle").children(".mb-footer").children(".mb-control-close").fadeIn(500);
            }, 8000); // 8 segundos

            // Start Popup
            $(".btn.btn-warning.mb-control").click();
            // End Popup
        }
    
    });

    $(".actionBar .btn-default.pull-right").addClass("disabled");

    $(".note_step5").append('<textarea data-toggle="tooltip" title="Limit 140 characters!" maxlength="140" name="note_donation" id="donation_note" class="form-control" placeholder="Type a note for this donation">');

    $("#list-patient").on( "click", ".select-patient", function(slideEvt) {
        if($(this).attr('id') != ""){
            var params = {};
            // TOMAR ID DE OTRO LADO
            params.id_patient = $(this).attr('id');
            id_patient = $(this).attr('id');
            $("#form-patient").val(id_patient);
            patient_dispenser = $(this).attr('rel');
            patient_name = $(this).attr('title');
            patient_points = $(this).attr('attr-points');      

            if($('#previously_ordered .patientName')){
                $('#previously_ordered .patientName').remove();
            }
            $("#previously_ordered").append('<strong class="patientName">'+patient_name+' </strong>'); 

            // TOMAR ID DE OTRO LADO
             // Borrar los datos de los pasos siguientes
            resetForm(1);
            products = [];
            prices = {};       
            totalSum(prices);
            <?php if($this->reward['enable_points']): ?>
                    maxpoints = patient_points > <?= $this->reward['maxpointsdiscount'] ?> ? <?= $this->reward['maxpointsdiscount'] ?> : patient_points; 
                    partialredemption = <?= $this->reward['partialredemption'] == "0" ? 0 : 1 ?>;
                    step =  partialredemption ? <?= explode(',',$this->reward['redemptionconversionrate'])[0] ?> : maxpoints;
                    $('#maxpoints').text('/'+maxpoints);
                    $('#availablepoints').text("Patien't Available Points: "+patient_points);
                    slider.slider('setValue',0)
                    slider.slider('setAttribute','max',maxpoints);
                    slider.slider('setAttribute','step',step);
            <?php endif ?>

            result = panelAjax("/admin_patients/getpatientphones",params, function(data){
                    $("#step-2 .panel-heading h3").text(patient_name+" Phones");
                    $("#selected-patient small").remove();
                    $("#selected-patient").append('<small>'+patient_name+'</small>');
                    $("#selected-patient").closest("li").removeClass("fa-square-o");
                    $("#selected-patient").closest("li").addClass("fa-check-square-o");
                    
                    $("#phones tbody").empty();
                    $.each(data, function( index, value ) {
                        $("#phones tbody").append('<tr id="'+value.id_phone+'">'+
                                                        '<td>'+value.id_phone+'</td>'+
                                                        '<td><button rel="'+value.id_phone+'" class="btn btn-warning phone-open" data-toggle="modal" data-target="#modal_edit_phone">'+value.number+'</button></td>'+
                                                        ' <th class="selected"></th>'+
                                                        '<th>'+
                                                        '<button class="btn btn-default select-phone">Choose <!--i class="fa fa-arrow-right"></i></button-->'+
                                                        '</th>'+
                                                         
                                                    '</tr>');
                        if(value.default == 1) {
                            $("#"+value.id_phone).find(".selected").html('<span class="label label-default label-form">default</span>');
                        }

                    });                    

                    $(".actionBar .btn-default.pull-right").removeClass("disabled");
                    $('.buttonNext').click();
                });

            panelAjax("/admin_patients/getpreviouslyordered",params, function(data){
                $('#previously_product_name').empty();
                if (data.firstDonationDate!=null) {
                    var firstDate = data.firstDonationDate.substr(0,10);
                    $('#previously_product_name').append('<div class="firstDate">'+firstDate+'</div>');
                    $.each(data.firstDonation, function(index, value) {
                        $('.firstDate').append('<span>'+ value['name_product']+'</span></br>');
                    })
                }
                if (data.secondDonationDate!=null) {
                    var secondDate = data.secondDonationDate.substr(0,10);
                    $('#previously_product_name').append('<div class="secondDate">'+secondDate+'</div>');
                    $.each(data.secondDonation, function(index, value) {
                        $('.secondDate').append('<span>'+ value['name_product'] +'</span></br>');
                    })
                }
                if (data.secondDonationDate==null && data.firstDonationDate==null) {
                    $('#previously_product_name').append('<div class="secondDate">No previously ordered</div>');
                }
            })
        }else{
            if(!$(".actionBar .btn-default.pull-right").hasClass("disabled"))
                $(".actionBar .btn-default.pull-right").addClass("disabled");
        }
    });

    $(".drivers").on( "click", ".widget", function() {
        if($(this).hasClass("disabled")) return;
        $('.drivers .widget').addClass("disabled");
        $("#message-box-info").children(".mb-container").children(".mb-middle").children(".mb-title").html("Please wait");
        $("#message-box-info").children(".mb-container").children(".mb-middle").children(".mb-content").html("<p>Updating selected driver.</p>");
        
        $("#message-box-info").children(".mb-container").children(".mb-middle").children(".mb-footer").children(".mb-control-close").hide();

        setInterval(function() {
            $("#message-box-info").children(".mb-container").children(".mb-middle").children(".mb-footer").children(".mb-control-close").fadeIn(500);
        }, 8000); // 8 segundos

        // Start Popup
        $(".btn.btn-info.mb-control").click();
        // End Popup

        id_driver = $(this).find(".driver_id").text();
        $(".drivers .widget").removeClass("widget-success");
        $(this).addClass("widget-success");
        $("#form-driver").val(id_driver);
        var params = {};
        params.id_patient = id_patient;
        params.id_dispenser = patient_dispenser;
        params.lat = latitude;
        params.lon = longitude;
        params.id_driver = id_driver;
        
        // Traigo los productos del driver para listar
        getProducts(id_driver,null);
        //Cargo el mapa con las locations
        calcRoute(id_driver);
        
        result = panelAjax("/admin_donations/getselecteddriver",params, function(data){
            $("#selected-driver small").remove();
            $("#selected-driver").append('<small> '+data.driver.first_name+' '+data.driver.last_name+'</small>');
            $("#selected-driver").closest("li").removeClass("fa-square-o");
            $("#selected-driver").closest("li").addClass("fa-check-square-o");

            // Nombre del Driver en Products
            $("#from_driver").html("Products from " + data.driver.first_name + ' ' + data.driver.last_name);
            
            $("#note_delivery").append('<small> '+data.driver.first_name+' '+data.driver.last_name+'</small>');
               

            arrival_time = data.arrival_time;
            $(".times time-allowed").empty();
            $(".times h3.time-allowed").empty().text('Allowed Time for '+data.driver.first_name+' '+data.driver.last_name+' ('+data.driver.area_name+')');
            $(".times .list-group").empty();
            $.each(data.times, function( index, value ) {
                if(value.status){
                    time = value[0]+' - '+value[1];
                    $(".times .list-group").append('<a href="javascript:void(0);" class="list-group-item system-selected active">'+value[0]+' - '+value[1]+' <i>Selected by system as the best option</i></a>');                 
                }else{
                    $(".times .list-group").append('<a href="javascript:void(0);" class="list-group-item">'+value[0]+' - '+value[1]+'</a>');
                }
            });
            $(".times .list-group").append('<a href="javascript:void(0);" class="list-group-item overtime">Overtime</a>');
            
            $("#selected-time").closest("li").removeClass("fa-square-o");
            $("#selected-time").closest("li").addClass("fa-check-square-o");
            $("#selected-time small").remove();
            $("#selected-time").append('<small> '+time+'</small>');
            $("#form-driver").val(id_driver);
            $("#form-time").val(time);
            
            //$('.buttonNext').click();

            if(data.extra_errors){
                $.each(data.extra_errors, function( index, value ) {
                    $('.wizard').smartWizard('showMessage',value);
                });
            }
            $(".btn.btn-default.btn-lg.pull-right.mb-control-close").click();
            $(".choose-location").fadeIn(100);
            $('.drivers .widget').removeClass("disabled");
        });
    });

    $(".products tbody").on( "click", "tr .select-product", function() {
        // Add Product
        product_id = $(this).closest("tr").find(".id_product").text();
        product_name = $(this).closest("tr").find(".name").text();
        product_price = $(this).closest("tr").find(".price").text();
        product_price = product_price.substr(1);
        if(jQuery.inArray( product_id, products ) >= 0){
            alert("Product already added");
            return;
        }
        $("#selected-products").removeClass("fa-square-o");
        $("#selected-products").addClass("fa-check-square-o");
        products.push(product_id);
        prices[product_id] = product_price;
        $("#selected-products table tbody").append('<tr>'+
                                                        '<input name="id_product[]" type="hidden" value="'+product_id+'">'+
                                                        '<input name="price[]" type="hidden" value="'+product_price+'">'+
                                                        '<input name="subtotal[]" type="hidden" value="'+product_price+'">'+
                                                        '<input name="gift[]" type="hidden" value="0">'+
                                                        '<td><input name="qty[]" value="1" aria-invalid="false" class="valid quantity"></td>'+
                                                        '<td>'+product_name+'</td>'+
                                                        '<!--td>$'+product_price+'</td-->'+
                                                        '<th><span class="fa fa-trash-o"></span></th>'+
                                                    '</tr>');
        totalSum(prices);
        $("#selected-products table").show();
        $("#total_cost").show();
        $("#tot_donation").show();
        $("#tot_discount").show();
        $("#fees").show();
        $("#tax_exempt_checkbox").show();
        $("#tax_exempt").html("Tax Exempt");
    });

    $(".products tbody").on( "click", "tr .select-gift", function() {
        // Add Gift Product
        product_id = $(this).closest("tr").find(".id_product").text();
        product_name = $(this).closest("tr").find(".name").text();
        product_price = $(this).closest("tr").find(".price").text();
        product_price = product_price.substr(1);
        if(jQuery.inArray( product_id, products ) >= 0){
            alert("Product already added");
            return;
        }
        $("#selected-products").removeClass("fa-square-o");
        $("#selected-products").addClass("fa-check-square-o");
        products.push(product_id);
        prices[product_id] = 0;
        $("#selected-products table tbody").append('<tr>'+
                                                        '<input name="id_product[]" type="hidden" value="'+product_id+'">'+
                                                        '<input name="price[]" type="hidden" value="'+product_price+'">'+
                                                        '<input name="subtotal[]" type="hidden" value="'+product_price+'">'+
                                                        '<input name="gift[]" type="hidden" value="1">'+
                                                        '<td><input name="qty[]" value="1" aria-invalid="false" class="valid quantity"></td>'+
                                                        '<td><i class="fa fa-gift" aria-hidden="true"></i> <strong>'+product_name+'</strong></td>'+
                                                        '<!--td>$'+0+'</td-->'+
                                                        '<th><span class="fa fa-trash-o"></span></th>'+
                                                    '</tr>');
        totalSum(prices);
        $("#total_cost").show();
        $("#fees").show();
        $("#tot_discount").show();
        $("#tot_donation").show();
        $("#selected-products table").show();
        $("#tax_exempt_checkbox").show();
        $("#tax_exempt").html("Tax Exempt");
    });

    $("#selected-products tbody").on( "click", "tr .fa-trash-o", function() {
        // Remove Product
        product_id = $(this).closest("tr").find("input[name='id_product[]']").val();
        index = products.indexOf(product_id);
        products.splice(index, 1);
        delete prices[product_id];
        $(this).closest("tr").remove();
        totalSum(prices);
    });

    $("#selected-products tbody").on( "change", "tr .quantity", function() {
        // Upgrade Product qty
        var product_tr = $(this).closest("tr");
        product_id = $(this).closest("tr").find("input[name='id_product[]']").val();
        qty = $(this).val();
        if(qty<=0){
            $(this).val(1);
            $(this).attr('value',1);
            alert("Qty cant be zero or less");
            return;
        }
        if($(this).closest("tr").find("input[name='gift[]']").val() == "1") return;
        checkTierPrice(product_id,qty,function(data){
            product_tr.find("input[name='subtotal[]']").val(data);
            prices[product_id] = data;
            totalSum(prices);
        });
    });

    var timeoutID = null;
    $('#product-search').keyup(function() {
        clearTimeout(timeoutID);
        var $target = $(this);
        var id_driver = $("#form-driver").val();
        timeoutID = setTimeout(function() { getProducts(id_driver,$target.val()); }, 500); 
    });

    $('#patientsearch').keyup(function(event) {
        var search = $('#patientsearch').val();
        if(search.length > 3 || event.keyCode == 13){
            clearTimeout(timeoutID);
            timeoutID = setTimeout(function() { getPatients(search); }, 500);
        }
    });

    $('.btn-search-patient').click(function() {
        var search = $('#patientsearch').val();
        getPatients(search);
    });

    $(".actionBar .btn-default.pull-right").click(function(){
        if($(".steps_5 .step-1").hasClass("selected"))
            $(".actionBar .btn-default.pull-right").addClass("disabled");
    });

    $("#next").click(function() {
        var val     = $('#page').attr('rel');
        val         = parseInt(val) + parseInt(1);
        page        = $('#page').attr('rel',val);
        search      = $('#product-search').val();
        id_driver   = $("#form-driver").val();
        getProducts(id_driver,search);
    });

    $("#prev").click(function() {
        var val     = $('#page').attr('rel');
        if (val > 1) {
            val         = parseInt(val) - parseInt(1);
            page        = $('#page').attr('rel',val);
            search      = $('#product-search').val();
            id_driver   = $("#form-driver").val();
            getProducts(id_driver,search);
        }
    });

    $(".buttonNext").click(function(){
        var step = $('.wizard').smartWizard('currentStep');
        switch(step){
            case 6:
                $('#summarydiscounts').show();
                break;           
        }      
    });

    $(".buttonPrevious").click(function(){
        var step = $('.wizard').smartWizard('currentStep');
        switch(step){
            case 5:
                $('#summarydiscounts').hide();
                break;          
        }      
    });

    $(".step-5").click(function(){
       $('#summarydiscounts').hide();  
    });

    $(".step-6").click(function(){
       $('#summarydiscounts').show(); 
       
    });

    var donations = [];
    var sortedDonations = [];
    var orderDonations = [];
    var datadonation = {};

    $("#sortable").before($("#sortable :first-child"));

    $("#sortable").sortable({ 
        placeholder: "ui-sortable-placeholder",
        items: 'li:not(:first)',
        start: function(event, ui) {
              ui.item.data('start_pos', ui.item.index());
        },
        stop: function(event, ui) {  
            var start_pos = ui.item.data('start_pos');
            if (start_pos != ui.item.index()) {
                is_ordered = true;
                orderDonations = [];           
                donations = [];
                $("#sortable li").each(function(i){
                     id_donation = $(this).attr("id"); 
                    $("#"+id_donation+" #diff").html("");
                    if(id_donation) donations.push(id_donation);
                    text = $(this).context.innerHTML;
                    orderDonations.push(text.substring(text.lastIndexOf("(")+1,text.lastIndexOf(")")));
                });
                var params = {};         
                params.donations = donations;
                params.datadonation = datadonation;
                params.id_driver = $('#form-driver').val();
                panelAjaxNoMessage("/admin_donations/getnewallowedtime",params, function(data){

                    $.each(data.diff,function(i,v){
                        $("#"+v.id_donation+" #diff").html(" ( "+v.difference+" M )");
                        sortedDonations[i] = {
                            id_donation : v.id_donation,
                            arrival_time : v.arrival_time_new
                        };

                        if(v.id_donation == "new"){
                            $.each(v.range,function(index,rangetime){
                                if(rangetime.status){
                                    $("#selected-time small").remove();
                                    $("#selected-time").append('<small> '+rangetime[0]+' - '+rangetime[1]+'</small>');
                                    $(".times .list-group .system-selected").removeClass("system-selected active");
                                    $('.times .list-group-item:contains('+rangetime[0]+' - '+rangetime[1]+')').remove();
                                    $('.times i').contents().first().remove();
                                    $('.times .list-group .ordertime').remove();
                                    $(".times .list-group").prepend('<a href="javascript:void(0);" class="list-group-item system-selected active ordertime">'+rangetime[0]+' - '+rangetime[1]+' <i>Selected by system as the best option</i></a>');
                                };
                            });
                            $(".times .list-group").append('<a href="javascript:void(0);" class="list-group-item">Overtime</a>');
                        };
                    }); 
                       
                });
                updateRoute();                                     
            }
        }       
    });

    $( "#sortable" ).disableSelection();

    var coupon = {};

    $('#discount').keyup(function(){
        var discount = $(this).val();

        var total = parseFloat($("#subtotal_cost_hidden").val());
        var percent = 0;
        if(discount.indexOf('%') > -1){
            percent = discount;
        }else{
            if(total){
                percent = discount * 100 / total;
            }
        }    
        $('#discount_hidden').val(percent);
        calculateDiscount();
        calculateTotal();
    });

    $('#shipping').change(function(){
        var shipping = $(this).val();
        calculateTotal();
    });

    $('#convenience').change(function(){
        var convenience = $(this).val();
        
        calculateTotal();
    });

    $('#selected-products').on('change','#tax_exempt_checkbox', function(){
        totalSum(prices);
    });

    var calculateDiscount = function(){
        var percent = 0;
        var total = parseFloat($("#subtotal_cost_hidden").val());
        if(!jQuery.isEmptyObject(coupon)){

            if(coupon.type == 'fix'){
                if(total)
                    percent = coupon.discount * 100 / total;    

            }else{
                percent = coupon.discount;
                // percent = coupon.discount * ((100 - percent) / 100) + percent
            }
        }
        if($('#reedemamount').text() != 0){
            if(total)             

                percentpoints = parseFloat($('#reedemamount').text()) * 100 / total;
                percent = percentpoints + percent;         
        }
        if($('#discount').val() != 0){
            if(total){  
                if ($('#discount').val().includes("%")) {
                    discount = parseFloat($('#discount').val());
                    percent = discount + percent;                                   
                } 
                else{
                    disc = parseFloat($('#discount').val());
                    discount = disc * 100 / total;
                    percent = discount  + percent; 
                }             
            }
        }
            total = (100 - percent) * total / 100;                  


        percent = percent ? percent.toFixed(2) : 0;
        $('#rdiscount').text(percent+'%');
        $('#rdiscount_hidden').val(percent);  
          
    }    

    var myCenter=new google.maps.LatLng();
    var marker=new google.maps.Marker({ position:myCenter });
    var map;
    var directionsDisplay = new google.maps.DirectionsRenderer();
    var directionsService = new google.maps.DirectionsService();
    var mapDonation;
    var stepDisplay;
    var markerArray = [];

    function showSteps(directionResult) {
        // For each step, place a marker, and add the text to the marker's
        // info window. Also attach the marker to an array so we
        // can keep track of it and remove it when calculating new
        // routes.

        var myRoute = directionResult;
        var icon;
        var marker;
        for (var i = 0; i < myRoute.request.waypoints.length  ; i++) {
          if(orderDonations[i+1] == "⚑") icon = "https://chart.googleapis.com/chart?chst=d_map_pin_icon&chld=flag|ADDE63"
          else icon = "https://chart.googleapis.com/chart?chst=d_map_pin_letter&chld=" + orderDonations[i+1] + "|FF0000|000000";
          if(jQuery.isEmptyObject(orderDonations)) icon = "https://chart.googleapis.com/chart?chst=d_map_pin_letter&chld=" + (i + 2) + "|FF0000|000000";
          locationLatLng = myRoute.request.waypoints[i].location.split(',');
          marker = new google.maps.Marker({
            position: new google.maps.LatLng(locationLatLng[0],locationLatLng[1]), 
            map: mapDonation,
            icon: icon 
              
          }); 
          markerArray.push(marker);
        };

        marker = new google.maps.Marker({
          position: myRoute.request.origin, 
          map: mapDonation,
          icon:  "https://chart.googleapis.com/chart?chst=d_map_pin_letter&chld=1|FF0000|000000"
         
        });
        markerArray.push(marker);
        if(orderDonations[i+1] == "⚑") icon = "https://chart.googleapis.com/chart?chst=d_map_pin_icon&chld=flag|ADDE63"
        else icon = "https://chart.googleapis.com/chart?chst=d_map_pin_letter&chld=" + orderDonations[i+1] + "|FF0000|000000";
        if(jQuery.isEmptyObject(orderDonations)) icon = "https://chart.googleapis.com/chart?chst=d_map_pin_icon&chld=flag|ADDE63";
        
        marker = new google.maps.Marker({
          position: myRoute.request.destination, 
          map: mapDonation,
          icon: icon 
        });
        markerArray.push(marker);
        
        google.maps.event.trigger(markerArray[0], "click");      

      }
});


var totalSum = function(prices){
    var total_cost = 0;
    
    $.each(prices, function( index, value ) {
        total_cost = parseFloat(total_cost) + parseFloat(value);
    });
    tax = <?= json_encode($this->tax) ?>;
    tax = (total_cost * tax / 100);

    if(tax){
        if ($('#tax_exempt_checkbox').is(':checked')) {tax = 0;}

        $("#tax").html("<span style='float:left'>Tax:</span><span style='float:right;padding-right: 22px;'>$"+tax.toFixed(2)+"</span>");        
    };
   
    $('#subtotal_cost_hidden').val(total_cost.toFixed(2));
    total_cost = total_cost + tax;
    $("#tax_hidden").val(tax.toFixed(2));
    $("#total_cost").html("<span style='float:left'>Cost:</span><span style='float:right;padding-right: 22px;'>$"+total_cost.toFixed(2)+"</span>");
    $("#total_cost_hidden").val(total_cost.toFixed(2));
    calculateTotal();
}

function validateSteps(step) {
    var status = true;
    var form_data = {};
    form_data = $("#create-donation").serializeArray();
    switch(step) {
        case "1":
            if( form_data[0]['value']=="" ){
                status = false;
                $('.wizard').smartWizard('showMessage','Please choose patient and click next.');
                $('.wizard').smartWizard('setError',{stepnum:step,iserror:true});
            }else{
                $('.wizard').smartWizard('hideMessage');
                $('.wizard').smartWizard('setError',{stepnum:step,iserror:false});
            }
            break;
        case "2":
            if( form_data[1]['value']=="" ){
                status = false;
                $('.wizard').smartWizard('showMessage','Please choose phone and click next.');
                $('.wizard').smartWizard('setError',{stepnum:step,iserror:true});         
            }else{
                $('.wizard').smartWizard('hideMessage');
                $('.wizard').smartWizard('setError',{stepnum:step,iserror:false});
            }
            break;
        case "3":
            if( form_data[2]['value']=="" ){
                status = false;
                $('.wizard').smartWizard('showMessage','Please choose location and click next.');
                $('.wizard').smartWizard('setError',{stepnum:step,iserror:true});         
            }else{
                $('.wizard').smartWizard('hideMessage');
                $('.wizard').smartWizard('setError',{stepnum:step,iserror:false});
            }
            break;
        case "4":
            if( form_data[3]['value']=="" ){
                status = false; 
                $('.wizard').smartWizard('showMessage','Please choose driver and click next.');
                $('.wizard').smartWizard('setError',{stepnum:step,iserror:true});         
            }else{
                $('.wizard').smartWizard('hideMessage');
                $('.wizard').smartWizard('setError',{stepnum:step,iserror:false});
            }
            break;

        case "6":
            if ($('#reason').css('display')=='block' && $("#reason_value").val() == '' || ($('#datetimepicker1 input').val() =='' && $('#enable_datetimepicker').is(':checked'))) {
                status = false; 
                if ($('#reason').css('display')=='block' && $("#reason_value").val() == '') {
                    $('.wizard').smartWizard('showMessage','Please write the discount reason and click finish.');
                    $('.wizard').smartWizard('setError',{stepnum:step,iserror:true});
                }else{
                    
                    $('.wizard').smartWizard('showMessage','Please insert a date.');
                    $('.wizard').smartWizard('setError',{stepnum:step,iserror:true});
                }
                         
            }else{
                $('.wizard').smartWizard('hideMessage');
                $('.wizard').smartWizard('setError',{stepnum:step,iserror:false});
            }
            break;       
        default:
            status = true;
            break;
    }

    return status;
}

function addMinutes(date, minutes) {
    return new Date(date.getTime() + minutes*60000);
}

function resetForm(step){
    if (step==1) {
        $("#form-location").val('');
        $("#selected-location").parent().removeClass('fa-check-square-o');
        $("#selected-location").parent().addClass('fa-square-o');
        $("#selected-location").html('Selected Location: ');
        $("#form-driver").val('');
        $("#selected-driver").parent().removeClass('fa-check-square-o');
        $("#selected-driver").parent().addClass('fa-square-o');
        $("#selected-driver").html('Selected Driver: ');
         $(".drivers .widget").removeClass("widget-success");
        $("#form-phone").val('');
        $("#selected-phone").parent().removeClass('fa-check-square-o');
        $("#selected-phone").parent().addClass('fa-square-o');
        $("#selected-phone").html('Selected Phone: ');
        $("#selected-products table tbody").empty();          
        $("#selected-products").removeClass("fa-check-square-o");
        $("#selected-products").addClass("fa-square-o");
        $("#selected-products table").hide();
        $("#tax_exempt_checkbox").hide();
        $("#total_cost").hide();
        $("#fees").hide();
        $("#tot_discount").hide();
        $("#tot_donation").hide();
        $("#donation_note").val('');
        $("#form-donation_note").val(''); 
        $("#selected-time").closest("li").addClass("fa-square-o");
        $("#selected-time").closest("li").removeClass("fa-check-square-o");
        $("#selected-time small").remove();
        $("#form-time").val('');
        $("#form-timeset").val('');
        $("#form-coupon").val('');
        $("#form-points").val('');
        $("#discount").val('');
        $("#rdiscount").val('');
        $("#discount_hidden").val(0);
        $("#rdiscount_hidden").val(0);
        $("#discount").val(0);
        $("#form-discount").val('');      
    };
    if (step==3) {
        $("#step-3").find(".selected").html('<span class="label label-default label-form"></span>');
    };

    if (step==6) {
        $("#step-6").find(".selected").html('<span class="label label-default label-form"></span>');
    };
}

function getProducts(driver,search){
    var params = {};
    params.id       = driver;
    params.search   = search;
    params.page     = $('#page').attr('rel');
    if (params.page < 0) { params.page = 1; };
    panelAjax("/admin_drivers/adminproducts/",params, function(data){
        $(".products tbody").html('');
        $.each(data, function( index, value ){
            item = "<tr id='product_" + value.id_product + "'>" +
                "<td class='id_product'>" + value.id_product + "</td>" +
                "<td><div class='name'>" + value.name + " (" + value.amount + " " + value.measure +  ")</div>";

                if(value.prices){
                    item = item + "<p style='padding-top:6px;'>";
                    $.each(value.prices,function( i, val ) {
                        if (val.qty > 0) {
                            item = item + "<strong>Buy " + val.qty + "</strong> (" + val.label + ") for <strong>$" + val.price + "</strong><br>";
                        };
                    })
                    item = item + "</p>";
                };

                item = item + "</td><td>" + value.categoryName + "</td>" + 
                "<td>" + value.qty + "</td>" +
                "<td class='price'>$" + value.price;
                
                if (value.price_special) {
                    item = item + " <i class='fa fa-tags'></i>";
                };

                item = item + "</td>" + "<td>";
                if (value.qty <= 0 || value.qty == null) {
                    item = item + "<span class='label label-danger'>Out of stock</span>";
                }else if(value.qty < value.driver_point_order){
                    item = item + "<span class='label label-warning'>Point of order</span>";
                }else{
                    item = item + "<span class='label label-success'>In Stock</span>";
                }
                item = item + "</td>" + 
                "<td style='margin:0px;padding:0px;'>" +
                "<div class='btn-group' role='group'>"+
                "<button type='button' class='btn btn-sm btn-default select-product'><span style='margin-right:0px' class='fa fa-plus'></span></button>"+
                "<button type='button' class='btn btn-sm btn-default select-gift'><span style='margin-right:0px' class='fa fa-gift'></span></button>"+ 
                "</div>"              
                "</td>" +
            "</tr>";
            $(".products tbody").append(item);
        });
    });
}

function checkTierPrice(id,qty,callback){
    var price;
    var params = {};
    params.id = id;
    params.qty = qty;
    panelAjax("/admin_products/getprice/",params, function(data){
        return callback(data);
    });
    //return price;
}

    $("#next-patient").click(function() {
    var val     = $('#page-patient').attr('rel');
    val         = parseInt(val) + parseInt(1);
    $('#page-patient').attr('rel',val);
    search      = $('#patientsearch').val();
    getPatients(search);
});

$("#back-patient").click(function() {
    var val     = $('#page-patient').attr('rel');
    if (val > 1) {
        val         = parseInt(val) - parseInt(1);
        $('#page-patient').attr('rel',val);
        search      = $('#patientsearch').val();
        getPatients(search);
    }
});

function getPatients(search){

    $("div#spinner").fadeIn("fast");
    $("#list-patient").hide();
   
    var params = {};
    params.search   = search;
    params.page     = $('#page-patient').attr('rel');
    if (params.page < 0) { params.page = 1; };
    panelAjax("/admin_patients/getpatients/",params, function(data){
        $("#list-patient").html('');
        $(".result-search-patient").html(data.patients.length + ' of ' + data.total + ' patients');

        if (data.patients.length == 0) {
            $('.pagination').fadeOut(500);
            var item = "<div class='col-md-12'>" +
                "<div class='panel panel-default'>" +
                    "<div class='panel-body'>" +
                        "<p style='text-align:center;'>No patient found</p>" + 
                    "</div>" +
                "</div>" +
            "</div>";
            $("#list-patient").append(item);
        }else{
            pager = data.page + " of " + data.pages + "</span>";

            if (data.page == 1) {
                $("#back-patient").hide();
                $("#back-patient-disabled").show();
                $("#next-patient").show();
                $("#next-patient-disabled").hide();
            }
            $(".pagination-data").html(pager);
            if (data.page < data.pages) {
                $('.pagination').fadeIn(500);
                if (data.page > 1) {
                    $("#back-patient").show();
                    $("#next-patient").show();
                    $("#back-patient-disabled").hide();
                    $("#next-patient-disabled").hide();
                };
            }else if(data.page == data.pages){
                $("#next-patient").hide();
                $("#next-patient-disabled").show();
            }

            $.each(data.patients, function( index, value ) {
                     var item = "<div id='" + value.id_patient + "'title='" + value.first_name + " " + value.last_name + "' rel='" + value.id_dispenser + "' attr-points='"+value.points+"' class='select-patient col-md-4' style='cursor:pointer;'>" +
                    "<!-- CONTACT ITEM -->" + 
                    "<div class='panel panel-default'>" +
                        "<div class='profile-control-left'>" + 
                            "<div class='panel-body profile'>" + 
                                "<div class='profile-image'>" +
                                    "<img src='" + value.avatar_url + "' alt='" + value.username + "'" +
                                "</div>" + 
                                "<div class='profile-data'>" +
                                    "<div class='profile-data-name'>" + value.first_name + " " + value.last_name + "</div>" +
                                    "<div class='profile-data-title'>" + value.username + "</div>" + 
                                "</div>" +
                                "<div class='profile-controls'>";
                                    date = new Date();
                                    mm = (date.getMonth()+1).toString();
                                    dd = date.getDate().toString();

                                    birthDate = date.getFullYear() + "-" + (mm[1]?mm:"0"+mm[0]) + "-" + (dd[1]?dd:"0"+dd[0]);                    

                                    birth_date = value.birth_date.substring(4);
                                    birth_date = date.getFullYear() + birth_date;

                                    if(birth_date == birthDate) {
                                    item = item + "<a class='profile-control-right' style='right: 145px;'><span class='fa fa-birthday-cake'></span></a>";
                                    }

                                    strDate = date.getFullYear() + "-" + (mm[1]?mm:"0"+mm[0]) + "-" + (dd[1]?dd:"0"+dd[0]);
                                    if(value.expire_recommendation_id <= strDate) {
                                    item = item + "<a class='profile-control-right-up' style='font-size:26px;border-color:#FF4D4D;right: 10px;'>" +
                                        "<span class='fa fa-exclamation-circle'></span>" + 
                                    "</a>";
                                    }
                                    if(value.recommendation_allowed == 0) {
                                    item = item + "<a class='profile-control-right-down' style='font-size:26px;border-color:#e3e141;right: 10px;'>" +
                                        "<span class='fa fa-exclamation-circle'></span>" + 
                                    "</a>";
                                    }
                                item = item + "</div>" +
                            "</div>" +
                        "</div>" +
                        "<div class='panel-body' style='height:145px;'>" +
                            "<div class='contact-info'>" +
                                "<p><small>Phone</small><br/>" + value.phone + "</p>" +
                                "<p><small>Doctor Recommendation</small><br/>" + value.recommendation_id + "</p>" + 
                                "<p><small>California ID / Driver ID</small><br/>" + value.driver_id + "</p>" +
                            "</div>" + 
                        "</div>" + 
                    "</div>" +
                    "<!-- END CONTACT ITEM -->" + 
                "</div>";            
                $("#list-patient").append(item);
            });
        }
    });
    $("div#spinner").fadeOut("fast");
    $("#list-patient").show();
}


var calculateTotal = function(){
    var tot_discount, feestotal, total;
    tax  = parseFloat($("#tax_hidden").val());
    total = parseFloat($("#subtotal_cost_hidden").val()); 
    tot_discount = parseFloat($('#rdiscount_hidden').val());

    shipping = parseFloat($('#shipping').val());
    convenience = parseFloat($('#convenience').val());
    feestotal = shipping + convenience;

    disc = tot_discount * total / 100;

    subtotal = (total - disc);
   
    if (subtotal >= 0) {
        $(".buttonNext").removeClass('buttonDisabled');
        total = subtotal + tax + feestotal;
        total = total.toFixed(2); 
    }else{
        $(".buttonNext").addClass('buttonDisabled');
        setTimeout(function() {
            $("#discountModal").modal();
        }, 300);
        
    
    }
    
    $("#fees").html("<span style='float:left'>Fees:</span><span style='float:right;padding-right: 22px;'>$"+feestotal.toFixed(2)+"</span>"); 
    $("#tot_discount").html("<span style='float:left'>Discount:</span><span style='float:right;padding-right: 22px;'>$"+disc.toFixed(2)+"</span>"); 
    $("#tot_donation").html("<span style='float:left'>TOTAL:</span><span style='float:right;padding-right: 22px;'>$"+total+"</span>");
    
    $('#label-total').text('$'+total);        
    $('#total_cost_hidden').val(total);
    $('#fee_shipping').val(shipping);
    $('#fee_convenience').val(convenience);

}

</script>


<!-- END SCRIPTS --> 