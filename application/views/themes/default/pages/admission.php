<link rel="stylesheet" href="<?php echo base_url(); ?>backend/datepicker/css/bootstrap-datetimepicker.css">
<script src="<?php echo base_url(); ?>backend/datepicker/js/bootstrap-datetimepicker.js"></script>
<style>
    /* ===== Frontend Admission Form Improvements ===== */
    .printcontent {
        border-radius: 6px;
        border: 1px solid #e0e0e0;
        padding: 0;
        width: 100%;
        margin-bottom: 20px;
        background: #fff;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06);
        overflow: hidden;
    }
    .printcontent > .row {
        margin: 0;
    }
    .printcontent .form-group {
        margin-bottom: 12px;
    }
    .printcontent .form-group label {
        font-weight: 600;
        font-size: 13px;
        color: #444;
        margin-bottom: 4px;
    }
    .printcontent .form-control {
        border-radius: 4px;
        border: 1px solid #ccc;
        padding: 8px 12px;
        font-size: 14px;
        height: auto;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    .printcontent .form-control:focus {
        border-color: #1da0e0;
        box-shadow: 0 0 0 2px rgba(29,160,224,0.15);
        outline: none;
    }
    .pagetitleh2 {
        background: linear-gradient(135deg, #1da0e0 0%, #036494 100%);
        margin: 0;
        font-size: 15px;
        padding: 10px 15px;
        color: #fff;
        margin-bottom: 0;
        font-weight: 600;
        border-bottom: none;
        letter-spacing: 0.3px;
        text-transform: uppercase;
    }
    .onlineform .row {
        margin-bottom: 5px;
    }
    .onlineform .printcontent > .row:first-of-type {
        padding-top: 15px;
    }
    .onlineform .printcontent > .row:last-of-type {
        padding-bottom: 15px;
    }
    .entered {
        font-weight: bold;
        margin-bottom: 0;
        margin-top: 15px;
        color: #036494;
    }
    .admission-header-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 10px;
        padding: 15px 0 20px 0;
        border-bottom: 2px solid #e8e8e8;
        margin-bottom: 20px;
    }
    .admission-header-bar h3 {
        margin: 0;
        padding: 0;
    }
    .admission-header-actions {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        align-items: center;
    }
    .modalclosebtn {
        display: inline-block;
        vertical-align: top;
        padding: 8px 18px;
        background: #036494;
        color: #fff;
        transition: all 0.3s ease;
        border-radius: 4px;
        border: 0;
        text-decoration: none;
        text-align: center;
        line-height: 1.4;
        font-size: 13px;
        font-weight: 500;
    }
    .modalclosebtn:hover {
        background: #1da0e0;
        color: #fff !important;
        outline: none;
        text-decoration: none;
    }
    .onlineformbtn {
        border-radius: 4px;
        border: 0;
        padding: 8px 28px;
        display: inline-block;
        font-size: 14px;
        font-weight: 600;
        background: #1da0e0;
        text-decoration: none !important;
        color: #fff !important;
        text-align: center;
        line-height: 1.4;
        vertical-align: middle;
        transition: all 0.3s ease;
    }
    .onlineformbtn:hover {
        background: #036494;
        color: #fff;
    }
    .capture-icon {
        cursor: pointer;
        padding-left: 12px;
        padding-right: 12px;
        font-size: 18px;
        color: #1da0e0;
    }
    .capture-icon:hover {
        color: #036494;
    }
    .d-flex.align-items-center {
        gap: 10px;
    }
    .d-flex.align-items-center img {
        border-radius: 4px;
    }
    .req {
        color: #e74c3c;
        font-weight: bold;
    }
    .text-danger {
        font-size: 12px;
    }
    /* Photo upload area */
    .photo-upload-area {
        border: 2px dashed #ccc;
        border-radius: 6px;
        padding: 10px;
        text-align: center;
        cursor: pointer;
        transition: border-color 0.2s;
        min-height: 100px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .photo-upload-area:hover {
        border-color: #1da0e0;
        background: #f9fcfe;
    }
    .photo-upload-area input[type="file"] {
        display: none;
    }
    .photo-upload-preview {
        max-width: 80px;
        max-height: 80px;
        border-radius: 4px;
        border: 2px solid #e0e0e0;
        margin-top: 5px;
    }
    /* Document upload area */
    .doc-upload-area {
        border: 2px dashed #ccc;
        border-radius: 6px;
        padding: 15px 20px;
        cursor: pointer;
        transition: border-color 0.2s;
    }
    .doc-upload-area:hover {
        border-color: #1da0e0;
        background: #f9fcfe;
    }
    .doc-upload-area input[type="file"] {
        display: none;
    }
    /* Submit row */
    .submit-row {
        padding: 20px 0 10px 0;
        border-top: 1px solid #e8e8e8;
        margin-top: 20px;
    }
    .captcha-input {
        border-radius: 4px;
        padding: 6px 12px;
        font-size: 14px;
        height: auto;
    }
    /* Responsive */
    @media (max-width: 767px) {
        .admission-header-bar {
            flex-direction: column;
            align-items: flex-start;
            gap: 12px;
        }
        .admission-header-actions {
            width: 100%;
        }
        .admission-header-actions a,
        .admission-header-actions .modalclosebtn {
            width: 100%;
            text-align: center;
        }
        .printcontent .form-group {
            margin-bottom: 15px;
        }
        .onlineform .btnMD {
            float: none;
        }
        .submit-row .d-flex {
            flex-direction: column;
            align-items: flex-start !important;
            gap: 15px;
        }
        .submit-row .col-lg-4,
        .submit-row .col-md-5 {
            width: 100%;
        }
    }
    @media (max-width: 480px) {
        .printcontent {
            padding: 0;
        }
    }
    /* Radio buttons */
    .radio-inline input[type="radio"] {
        margin-top: 2px;
    }
    .printcontent .checkbox label {
        font-weight: normal;
        font-size: 13px;
        color: #555;
    }
</style>

<?php
if (!$form_admission) {
    ?>
    <div class="alert alert-danger" style="border-radius:6px; padding:15px 20px;">
        <i class="fa fa-exclamation-triangle"></i> <?php echo $this->lang->line('admission_form_disable_please_contact_to_administrator'); ?>
    </div>
    <?php
return;
}
?>

<?php
if ($this->session->flashdata('msg')) {
    $message = $this->session->flashdata('msg');
    echo $message;
    $this->session->unset_userdata('msg');
}
?>
    <div class="admission-header-bar">
        <h3 class="entered mt0"><i class="fa fa-graduation-cap"></i> <?php echo $this->lang->line('online_admission'); ?></h3>
        <div class="admission-header-actions">
            <a href="#checkOnlineAdmissionStatus" class="modalclosebtn" onclick="openStatusFormmodal();" data-toggle="modal" data-target="#checkOnlineAdmissionStatus"><i class="fa fa-search"></i> <?php echo $this->lang->line('check_your_form_status') ?></a>
            <?php if (!empty($online_admission_application_form)) {?>
            <a href="<?php echo base_url(); ?>welcome/download/<?php echo $sch_setting->id; ?>" class="modalclosebtn"><i class="fa fa-download"></i> <?php echo $this->lang->line('download_application_form'); ?></a>
            <?php }?>
        </div>
    </div>
   <form id="form1" class="spaceb60 onlineform" action="<?php echo current_url() ?>"  id="employeeform" name="employeeform" method="post" accept-charset="utf-8" enctype="multipart/form-data">
        <?php if ($online_admission_instruction != "") {?>
        <div class="printcontent">
        <div class="row">
         <h4 class="pagetitleh2"><?php echo $this->lang->line('instructions'); ?></h4>
            <div class="col-md-12">
              <div class="form-group">
                    <?php echo $online_admission_instruction; ?>
                </div>
            </div>
        </div>
    </div>
  <?php }?>
 <div class="printcontent">
    <div class="row">
    <h4 class="pagetitleh2"><?php echo $this->lang->line('basic_details'); ?></h4>
        <div class="col-md-3">
            <div class="form-group">
                <label><?php echo $this->lang->line('class'); ?></label><small class="req"> *</small>
                <select  id="class_id" name="class_id" class="form-control"  >
                    <option value=""><?php echo $this->lang->line('select'); ?></option>
                    <?php
foreach ($classlist as $class) {
    ?>
                        <option value="<?php echo $class['id'] ?>"<?php if (set_value('class_id') == $class['id']) {
        echo "selected=selected";
    }
    ?>><?php echo $class['class'] ?></option>
                        <?php
}
?>
                </select>
                <span class="text-danger"><?php echo form_error('class_id'); ?></span>
            </div>
        </div>
        <div class="col-md-3 displaynone">
            <div class="form-group">
                <label><?php echo $this->lang->line('section'); ?></label><small class="req"> *</small>
                <select  id="section_id" name="section_id" class="form-control" >
                    <option value=""><?php echo $this->lang->line('select'); ?></option>
                </select>
                <span class="text-danger"><?php echo form_error('section_id'); ?></span>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label><?php echo $this->lang->line('first_name'); ?></label><small class="req"> *</small>
                <input id="firstname" name="firstname" placeholder="" type="text" class="form-control"  value="<?php echo set_value('firstname'); ?>" autocomplete="off" />
                <span class="text-danger"><?php echo form_error('firstname'); ?></span>
            </div>
        </div>
         <?php if ($this->customlib->getfieldstatus('middlename')) {?>
         <div class="col-md-3">
            <div class="form-group">
                <label><?php echo $this->lang->line('middle_name'); ?></label>
                  <input id="middlename" name="middlename" placeholder="" type="text" class="form-control"  value="<?php echo set_value('middlename'); ?>" autocomplete="off" />
            </div>
        </div>
        <?php }?>
        <?php if ($this->customlib->getfieldstatus('lastname')) {?>
         <div class="col-md-3">
            <div class="form-group">
                <label><?php echo $this->lang->line('last_name'); ?></label>
                <input id="lastname" name="lastname" placeholder="" type="text" class="form-control"  value="<?php echo set_value('lastname'); ?>" autocomplete="off" />
                <span class="text-danger"><?php echo form_error('lastname'); ?></span>
            </div>
        </div>
        <?php }?>
    </div><!--./row-->
    <div class="row">
        <div class="col-md-3">
            <div class="form-group">
                <label><?php echo $this->lang->line('gender'); ?></label><small class="req"> *</small>
                <select class="form-control" name="gender">
                    <option value=""><?php echo $this->lang->line('select'); ?></option>
                    <?php
foreach ($genderList as $key => $value) {
    ?>
                        <option value="<?php echo $key; ?>" <?php if (set_value('gender') == $key) {
        echo "selected";
    }
    ?>><?php echo $value; ?></option>
                        <?php
}
?>
                </select>
                <span class="text-danger"><?php echo form_error('gender'); ?></span>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label><?php echo $this->lang->line('date_of_birth'); ?></label><small class="req"> *</small>
                <input  type="text" class="form-control date2"  value="<?php echo set_value('dob'); ?>" id="dob" name="dob" readonly="readonly"/>
                <span class="text-danger"><?php echo form_error('dob'); ?></span>
            </div>
        </div>
          <?php if ($this->customlib->getfieldstatus('mobile_no')) {?>
        <div class="col-md-3">
            <div class="form-group">
                <label><?php echo $this->lang->line('mobile_number'); ?></label>
                <input  type="text" class="form-control"  value="<?php echo set_value('mobileno'); ?>" id="mobileno" name="mobileno" autocomplete="off"/>
                <span class="text-danger"><?php echo form_error('mobileno'); ?></span>
            </div>
        </div>
        <?php }?>
         <?php if ($this->customlib->getfieldstatus('student_email')) {?>
        <div class="col-md-3">
            <div class="form-group">
                <label><?php echo $this->lang->line('email'); ?></label><small class="req"> *</small>
                <input  type="text" class="form-control"  value="<?php echo set_value('email'); ?>" id="email" name="email" autocomplete="off"/>
                <span class="text-danger"><?php echo form_error('email'); ?></span>
            </div>
        </div>
        <?php }?>
    </div><!--./row-->
    <div class="row">
      <?php if ($this->customlib->getfieldstatus('category')) {
    ?>
        <div class="col-md-3">
            <div class="form-group">
               <label><?php echo $this->lang->line('category'); ?></label>
                    <select  id="category_id" name="category_id" class="form-control" >
                        <option value=""><?php echo $this->lang->line('select'); ?></option>
                            <?php foreach ($categorylist as $category) {?>
                         <option value="<?php echo $category['id'] ?>" <?php
if (set_value('category_id') == $category['id']) {
        echo "selected=selected";
    }
        ?>><?php echo $category['category'] ?>
                         </option>
                                <?php
}
    ?>
                     </select>
            </div>
        </div>
        <?php }?>
       <?php if ($this->customlib->getfieldstatus('religion')) {?>
            <div class="col-md-3">
                <div class="form-group">
                    <label><?php echo $this->lang->line('religion'); ?></label>
                    <input id="religion" name="religion" placeholder="" type="text" class="form-control"  value="<?php echo set_value('religion'); ?>" autocomplete="off" />
                    <span class="text-danger"><?php echo form_error('religion'); ?></span>
                </div>
            </div>
        <?php }if ($this->customlib->getfieldstatus('cast')) {?>
                <div class="col-md-3">
                    <div class="form-group">
                        <label><?php echo $this->lang->line('caste'); ?></label>
                        <input id="cast" name="cast" placeholder="" type="text" class="form-control" autocomplete="off"  value="<?php echo set_value('cast'); ?>" />
                        <span class="text-danger"><?php echo form_error('cast'); ?></span>
                    </div>
                </div>
        <?php }?>
        <?php if ($this->customlib->getfieldstatus('is_student_house')) {
    ?>
            <div class="col-md-3 col-xs-12">
                <div class="form-group">
                    <label><?php echo $this->lang->line('house') ?></label>
                    <select class="form-control" rows="3" placeholder="" name="house">
                        <option value=""><?php echo $this->lang->line('select') ?></option>
                        <?php foreach ($houses as $hkey => $hvalue) {
        ?>
                            <option value="<?php echo $hvalue["id"] ?>" <?php if (set_value('house') == $hvalue["id"]) {  echo "selected"; } ?>><?php echo $hvalue["house_name"] ?></option>

                    <?php }?>
                    </select>
                    <span class="text-danger"><?php echo form_error('house'); ?></span>
                </div>
            </div>
            <?php
}
?>
    </div><!--./row-->
    <div class="row">
     <?php if ($this->customlib->getfieldstatus('is_blood_group')) {
    ?>
        <div class="col-md-3 col-xs-12">
            <div class="form-group">
                <label><?php echo $this->lang->line('blood_group'); ?></label>
                    <?php
?>
                <select class="form-control" rows="3" placeholder="" name="blood_group">
                    <option value=""><?php echo $this->lang->line('select') ?></option>
                    <?php foreach ($bloodgroup as $bgkey => $bgvalue) {
        ?>
                        <option value="<?php echo $bgvalue ?>" <?php if (set_value('blood_group') == $bgvalue) {  echo "selected"; } ?>><?php echo $bgvalue ?></option>

                    <?php }?>
                </select>
                <span class="text-danger"><?php echo form_error('blood_group'); ?></span>
            </div>
        </div>
        <?php }?>
          <?php if ($this->customlib->getfieldstatus('student_height')) {?>
             <div class="col-md-3 col-xs-12">
                <div class="form-group">
                   <label><?php echo $this->lang->line('height'); ?></label>
                 <?php ?>
                <input type="text" name="height" class="form-control" value="<?php echo set_value('height'); ?>" autocomplete="off">
                <span class="text-danger"><?php echo form_error('height'); ?></span>
               </div>
             </div>
           <?php }if ($this->customlib->getfieldstatus('student_weight')) {?>
            <div class="col-md-3 col-xs-12">
                 <div class="form-group">
                      <label><?php echo $this->lang->line('weight'); ?></label>
                    <input type="text" name="weight" class="form-control" value="<?php echo set_value('weight'); ?>" autocomplete="off" >
                    <span class="text-danger"><?php echo form_error('weight'); ?></span>
               </div>
            </div>
            <?php }?>
             <?php if ($this->customlib->getfieldstatus('measurement_date')) {?>
            <div class="col-md-3 col-xs-12">
                <div class="form-group">
                  <label><?php echo $this->lang->line('measurement_date'); ?></label>

                <input type="text" id="measure_date" value="<?php echo set_value('measure_date'); ?>"  name="measure_date" class="form-control date2" autocomplete="off" readonly="" >
                <span class="text-danger"><?php echo form_error('measure_date'); ?></span>
            </div>
        </div>
        <?php }?>
    </div><!--./row-->
    <div class="row">
        <?php if ($this->customlib->getfieldstatus('student_photo')) {?>
                <div class="col-md-3">
                    <div class="form-group">
                        <label><?php echo $this->lang->line('student_photo'); ?></label>
                        <div class="photo-upload-area" onclick="document.getElementById('student_photo_file').click()">
                            <input type="file" name="file" id="student_photo_file" accept="image/*" onchange="previewOnlineAdmissionPhoto(this, 'student_photo_preview')">
                            <div id="student_photo_preview_wrap">
                                <i class="fa fa-camera" style="font-size:24px; color:#999;"></i>
                                <p style="margin:5px 0 0; font-size:11px; color:#999;">Click to upload photo</p>
                            </div>
                            <img id="student_photo_preview" src="#" class="photo-upload-preview" style="display:none;">
                        </div>
                        <span class="text-danger"><?php echo form_error('file'); ?></span></div>
                </div>
                <?php
}?>
    </div>
    <div class="row">
            <?php
echo display_onlineadmission_custom_fields('students');
?>
        </div>
     </div>
    <?php if ($this->customlib->getfieldstatus('father_name') || $this->customlib->getfieldstatus('father_phone') || $this->customlib->getfieldstatus('father_occupation') || $this->customlib->getfieldstatus('father_pic') || $this->customlib->getfieldstatus('mother_name') || $this->customlib->getfieldstatus('mother_phone') || $this->customlib->getfieldstatus('mother_occupation') || $this->customlib->getfieldstatus('mother_pic')) {?>
    <div class="printcontent">
      <div class="row">
        <h4 class="pagetitleh2"><?php echo $this->lang->line('parent_detail'); ?></h4>
       <?php if ($this->customlib->getfieldstatus('father_name')) {?>
        <div class="col-md-3">
            <div class="form-group">
                <label><?php echo $this->lang->line('father_name'); ?></label>
                <input id="father_name" name="father_name" placeholder="" type="text" class="form-control" autocomplete="off"  value="<?php echo set_value('father_name'); ?>" />
                <span class="text-danger"><?php echo form_error('father_name'); ?></span>
            </div>
        </div>
        <?php }?>
        <?php if ($this->customlib->getfieldstatus('father_phone')) {?>
        <div class="col-md-3">
            <div class="form-group">
                <label><?php echo $this->lang->line('father_phone'); ?></label>
                <input id="father_phone" name="father_phone" placeholder="" type="text" class="form-control" autocomplete="off"  value="<?php echo set_value('father_phone'); ?>" />
                <span class="text-danger"><?php echo form_error('father_phone'); ?></span>
            </div>
        </div>
        <?php }?>
        <?php if ($this->customlib->getfieldstatus('father_occupation')) {?>
        <div class="col-md-3">
            <div class="form-group">
                <label><?php echo $this->lang->line('father_occupation'); ?></label>
                <input id="father_occupation" name="father_occupation" placeholder="" type="text" class="form-control"  value="<?php echo set_value('father_occupation'); ?> " autocomplete="off" />
                <span class="text-danger"><?php echo form_error('father_occupation'); ?></span>
            </div>
        </div>
        <?php }?>
        <?php if ($this->customlib->getfieldstatus('father_pic')) {?>
            <div class="col-md-3">
                <div class="form-group">
                    <label><?php echo $this->lang->line('father_photo'); ?></label>
                    <div class="photo-upload-area" onclick="document.getElementById('father_photo_file').click()">
                        <input type="file" name="father_pic" id="father_photo_file" accept="image/*" onchange="previewOnlineAdmissionPhoto(this, 'father_photo_preview')">
                        <div id="father_photo_preview_wrap">
                            <i class="fa fa-camera" style="font-size:24px; color:#999;"></i>
                            <p style="margin:5px 0 0; font-size:11px; color:#999;">Click to upload photo</p>
                        </div>
                        <img id="father_photo_preview" src="#" class="photo-upload-preview" style="display:none;">
                    </div>
                    <span class="text-danger"><?php echo form_error('father_pic'); ?></span></div>
            </div>
        <?php }?>
        </div><!---row-->
        <div class="row">
         <?php if ($this->customlib->getfieldstatus('mother_name')) {?>
        <div class="col-md-3">
            <div class="form-group">
                <label><?php echo $this->lang->line('mother_name'); ?></label>
                <input id="mother_name" name="mother_name" placeholder="" type="text" class="form-control"  value="<?php echo set_value('mother_name'); ?>" autocomplete="off"/>
                <span class="text-danger"><?php echo form_error('mother_name'); ?></span>
            </div>
        </div>
        <?php }?>
        <?php if ($this->customlib->getfieldstatus('mother_phone')) {?>
        <div class="col-md-3">
            <div class="form-group">
                <label><?php echo $this->lang->line('mother_phone'); ?></label>
                <input id="mother_phone" name="mother_phone" placeholder="" type="text" class="form-control"  value="<?php echo set_value('mother_phone'); ?>" autocomplete="off"/>
                <span class="text-danger"><?php echo form_error('mother_phone'); ?></span>
            </div>
        </div>
        <?php }?>
        <?php if ($this->customlib->getfieldstatus('mother_occupation')) {?>
        <div class="col-md-3">
            <div class="form-group">
                <label><?php echo $this->lang->line('mother_occupation'); ?></label>
                <input id="mother_occupation" name="mother_occupation" placeholder="" type="text" class="form-control"  value="<?php echo set_value('mother_occupation'); ?>" autocomplete="off"/>
                <span class="text-danger"><?php echo form_error('mother_occupation'); ?></span>
            </div>
        </div>
        <?php }?>
        <?php if ($this->customlib->getfieldstatus('mother_pic')) {?>
            <div class="col-md-3">
                <div class="form-group">
                    <label><?php echo $this->lang->line('mother_photo'); ?></label>
                    <div class="photo-upload-area" onclick="document.getElementById('mother_photo_file').click()">
                        <input type="file" name='mother_pic' id="mother_photo_file" accept="image/*" onchange="previewOnlineAdmissionPhoto(this, 'mother_photo_preview')">
                        <div id="mother_photo_preview_wrap">
                            <i class="fa fa-camera" style="font-size:24px; color:#999;"></i>
                            <p style="margin:5px 0 0; font-size:11px; color:#999;">Click to upload photo</p>
                        </div>
                        <img id="mother_photo_preview" src="#" class="photo-upload-preview" style="display:none;">
                    </div>
                    <span class="text-danger"><?php echo form_error('mother_pic'); ?></span></div>
            </div>
       <?php }?>
    </div><!--./row-->
    </div><!--./printcontent-->
    <?php }?>
     <?php if ($this->customlib->getfieldstatus('if_guardian_is')) {
    ?>
    <div class="printcontent">
       <div class="row">
        <h4 class="pagetitleh2"><?php echo $this->lang->line('guardian_details'); ?></h4>
          <div class="form-group col-md-12">
            <label><?php echo $this->lang->line('if_guardian_is'); ?><small class="req"> *</small>&nbsp;&nbsp;&nbsp;</label>
            <label class="radio-inline">
                <input type="radio" name="guardian_is" <?php
echo set_value('guardian_is') == "father" ? "checked" : "";
    ?>   value="father"> <?php echo $this->lang->line('father'); ?>
            </label>
            <label class="radio-inline">
                <input type="radio" name="guardian_is" <?php
echo set_value('guardian_is') == "mother" ? "checked" : "";
    ?>   value="mother"> <?php echo $this->lang->line('mother'); ?>
            </label>
            <label class="radio-inline">
                <input type="radio" name="guardian_is" <?php
echo set_value('guardian_is') == "other" ? "checked" : "";
    ?>   value="other"> <?php echo $this->lang->line('other'); ?>
            </label>
            <span class="text-danger"><?php echo form_error('guardian_is'); ?></span>
        </div>
        <?php if ($this->customlib->getfieldstatus('guardian_name')) {?>
        <div class="col-md-3">
            <div class="form-group">
                <label><?php echo $this->lang->line('guardian_name'); ?></label><small class="req"> *</small>
                <input id="guardian_name" name="guardian_name" placeholder="" type="text" class="form-control"  value="<?php echo set_value('guardian_name'); ?>" autocomplete="off" />
                <span class="text-danger"><?php echo form_error('guardian_name'); ?></span>
            </div>
        </div>
        <?php }?>
           <?php if ($this->customlib->getfieldstatus('guardian_relation')) {?>
        <div class="col-md-3">
            <div class="form-group">
                <label><?php echo $this->lang->line('guardian_relation'); ?></label><small class="req"> *</small>
                <input id="guardian_relation" name="guardian_relation" placeholder="" type="text" class="form-control"  value="<?php echo set_value('guardian_relation'); ?>" autocomplete="off"/>
                <span class="text-danger"><?php echo form_error('guardian_relation'); ?></span>
            </div>
        </div>
        <?php }?>
         <?php if ($this->customlib->getfieldstatus('guardian_email')) {?>
        <div class="col-md-3">
            <div class="form-group">
                <label><?php echo $this->lang->line('guardian_email'); ?></label>
                <input id="guardian_email" name="guardian_email" placeholder="" type="text" class="form-control"  value="<?php echo set_value('guardian_email'); ?>" autocomplete="off"/>
                <span class="text-danger"><?php echo form_error('guardian_email'); ?></span>
            </div>
        </div>
        <?php }?>
         <?php if ($this->customlib->getfieldstatus('guardian_photo')) {?>
          <div class="col-md-3">
            <div class="form-group">
                <label><?php echo $this->lang->line('guardian_photo'); ?></label>
                <div class="photo-upload-area" onclick="document.getElementById('guardian_photo_file').click()">
                    <input type="file" name="guardian_pic" id="guardian_photo_file" accept="image/*" onchange="previewOnlineAdmissionPhoto(this, 'guardian_photo_preview')">
                    <div id="guardian_photo_preview_wrap">
                        <i class="fa fa-camera" style="font-size:24px; color:#999;"></i>
                        <p style="margin:5px 0 0; font-size:11px; color:#999;">Click to upload photo</p>
                    </div>
                    <img id="guardian_photo_preview" src="#" class="photo-upload-preview" style="display:none;">
                </div>
                <span class="text-danger"><?php echo form_error('guardian_pic'); ?></span></div>
        </div>
        <?php }?>
    </div><!--./row-->
    <div class="row">
    <?php if ($this->customlib->getfieldstatus('guardian_phone')) {?>
        <div class="col-md-3">
            <div class="form-group">
                <label><?php echo $this->lang->line('guardian_phone'); ?></label>
                <input id="guardian_phone" name="guardian_phone" placeholder="" type="text" class="form-control"  value="<?php echo set_value('guardian_phone'); ?>" autocomplete="off"/>
                <span class="text-danger"><?php echo form_error('guardian_phone'); ?></span>
            </div>
        </div>
        <?php }?>
        <?php if ($this->customlib->getfieldstatus('guardian_occupation')) {?>
        <div class="col-md-3">
            <div class="form-group">
                <label><?php echo $this->lang->line('guardian_occupation'); ?></label>
                <input id="guardian_occupation" name="guardian_occupation" placeholder="" type="text" class="form-control"  value="<?php echo set_value('guardian_occupation'); ?>" autocomplete="off" />
                <span class="text-danger"><?php echo form_error('guardian_occupation'); ?></span>
            </div>
        </div>
        <?php }?>
         <?php if ($this->customlib->getfieldstatus('guardian_address')) {?>
        <div class="col-md-6">
            <div class="form-group">
                <label><?php echo $this->lang->line('guardian_address'); ?></label>
                <textarea id="guardian_address" name="guardian_address" placeholder="" class="form-control" rows="1" autocomplete="off"><?php echo set_value('guardian_address'); ?></textarea>
                <span class="text-danger"><?php echo form_error('guardian_address'); ?></span>
            </div>
        </div>
        <?php }?>
        </div>
       </div>
       <?php }?>
       <?php if ($this->customlib->getfieldstatus('current_address') || $this->customlib->getfieldstatus('permanent_address')) {?>
    <div class="printcontent">
        <div class="row">
            <h4 class="pagetitleh2"><?php echo $this->lang->line('student_address_details'); ?></h4>
            <?php if ($this->customlib->getfieldstatus('current_address')) {?>
                <div class="col-md-6">
                 <?php if ($this->customlib->getfieldstatus('guardian_address')) {?>
                    <div class="checkbox">
                        <label> <input type="checkbox" id="autofill_current_address" onclick="return auto_fill_guardian_address();" autocomplete="off">
                        <?php echo $this->lang->line('if_guardian_address_is_current_address'); ?>
                         </label>
                    </div>
                    <?php } else {echo "<div class='checkbox'><label>&nbsp;</label></div>";}?>
                    <div class="form-group">
                        <label><?php echo $this->lang->line('current_address'); ?></label>
                        <textarea id="current_address" name="current_address" placeholder="" rows="1" class="form-control" autocomplete="off"><?php echo set_value('current_address'); ?></textarea>
                        <span class="text-danger"><?php echo form_error('current_address'); ?></span>
                    </div>
                </div>
                 <?php }if ($this->customlib->getfieldstatus('permanent_address')) {?>
                    <div class="col-md-6">
                         <?php if ($this->customlib->getfieldstatus('current_address')) {?>
                        <div class="checkbox">
                            <label> <input type="checkbox" id="autofill_address"onclick="return auto_fill_address();">
                                <?php echo $this->lang->line('if_permanent_address_is_current_address'); ?>  </label>
                         </div>
                         <?php } else {echo "<div class='checkbox'><label>&nbsp;</label></div>";}?>
                      <div class="form-group">
                            <label><?php echo $this->lang->line('permanent_address'); ?></label>
                            <textarea id="permanent_address" name="permanent_address" rows="1" placeholder="" class="form-control" autocomplete="off"></textarea>
                            <span class="text-danger"><?php echo form_error('permanent_address'); ?></span>
                        </div>
                </div>
                <?php }?>
             </div>
            </div>
             <?php }?>
             <?php if ($this->customlib->getfieldstatus('bank_account_no') || $this->customlib->getfieldstatus('bank_name') || $this->customlib->getfieldstatus('ifsc_code') || $this->customlib->getfieldstatus('national_identification_no') || $this->customlib->getfieldstatus('local_identification_no') || $this->customlib->getfieldstatus('rte') || $this->customlib->getfieldstatus('previous_school_details') || $this->customlib->getfieldstatus('student_note')) {
    ?>
        <div class="printcontent">
            <div class="row">
                <h4 class="pagetitleh2"><?php echo $this->lang->line('miscellaneous_details'); ?></h4>
              <?php if ($this->customlib->getfieldstatus('bank_account_no')) {?>
                <div class="col-md-4">
                    <div class="form-group">
                        <label><?php echo $this->lang->line('bank_account_number'); ?></label>
                        <input id="bank_account_no" name="bank_account_no" placeholder="" type="text" class="form-control"  value="<?php echo set_value('bank_account_no'); ?>" autocomplete="off" />
                        <span class="text-danger"><?php echo form_error('bank_account_no'); ?></span>
                    </div>
                </div>
            <?php }if ($this->customlib->getfieldstatus('bank_name')) {?>
                <div class="col-md-4">
                    <div class="form-group">
                        <label><?php echo $this->lang->line('bank_name'); ?></label>
                        <input id="bank_name" name="bank_name" placeholder="" type="text" class="form-control"  value="<?php echo set_value('bank_name'); ?>" autocomplete="off" />
                        <span class="text-danger"><?php echo form_error('bank_name'); ?></span>
                    </div>
                </div>
            <?php }
    if ($this->customlib->getfieldstatus('ifsc_code')) {?>
                <div class="col-md-4">
                    <div class="form-group">
                        <label><?php echo $this->lang->line('ifsc_code'); ?></label>
                        <input id="ifsc_code" name="ifsc_code" placeholder="" type="text" class="form-control"  value="<?php echo set_value('ifsc_code'); ?>" autocomplete="off" />
                        <span class="text-danger"><?php echo form_error('ifsc_code'); ?></span>
                    </div>
                </div>
            <?php }?>
        </div>
        <div class="row">
            <?php if ($this->customlib->getfieldstatus('national_identification_no')) {?>
              <div class="col-md-4">
                <div class="form-group">
                 <label>  <?php echo $this->lang->line('national_identification_number'); ?>  </label>
                  <input id="adhar_no" name="adhar_no" placeholder="" type="text" class="form-control"  value="<?php echo set_value('adhar_no'); ?>"  autocomplete="off"/>
                  <span class="text-danger"><?php echo form_error('adhar_no'); ?></span>
                 </div>
               </div>
             <?php }if ($this->customlib->getfieldstatus('local_identification_no')) {?>
             <div class="col-md-4">
                 <div class="form-group">
                    <label> <?php echo $this->lang->line('local_identification_number'); ?>     </label>
                     <input id="samagra_id" name="samagra_id" placeholder="" type="text" class="form-control"  value="<?php echo set_value('samagra_id'); ?>" autocomplete="off" />
                         <span class="text-danger"><?php echo form_error('samagra_id'); ?></span>
                 </div>
              </div>
           <?php }if ($this->customlib->getfieldstatus('rte')) {
        ?>
                    <div class="col-md-4">
                        <label><?php echo $this->lang->line('rte'); ?></label>
                         <div class="radio" style="margin-top: 2px;">
                          <label><input class="radio-inline" type="radio" name="rte" value="Yes"  <?php
echo set_value('rte') == "yes" ? "checked" : "";
        ?>  ><?php echo $this->lang->line('yes'); ?></label>
                            <label><input class="radio-inline" checked="checked" type="radio" name="rte" value="No" <?php
echo set_value('rte') == "no" ? "checked" : "";
        ?>  ><?php echo $this->lang->line('no'); ?></label>
                                </div>
                    <span class="text-danger"><?php echo form_error('rte'); ?></span>
                </div>
                <?php }?>
        </div>
        <div class="row">
            <?php if ($this->customlib->getfieldstatus('previous_school_details')) {?>
                <div class="col-md-6">
                    <div class="form-group">
                        <label><?php echo $this->lang->line('previous_school_details'); ?></label>
                        <textarea class="form-control" rows="1" placeholder="" name="previous_school" autocomplete="off"><?php echo set_value('previous_school'); ?></textarea>
                        <span class="text-danger"><?php echo form_error('previous_school'); ?></span>
                    </div>
                </div>
                <?php }?>
                <?php if ($this->customlib->getfieldstatus('student_note')) {?>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label><?php echo $this->lang->line('note'); ?></label>
                            <textarea class="form-control" rows="1" placeholder="" name="note" autocomplete="off"><?php echo set_value('note'); ?></textarea>
                            <span class="text-danger"><?php echo form_error('note'); ?></span>
                        </div>
                    </div>
                <?php }?>
            </div>
         </div>
        <?php }?>
        <?php if ($this->customlib->getfieldstatus('upload_documents')) {?>
        <div class="printcontent">
          <div class="row">
            <h4 class="pagetitleh2"><?php echo $this->lang->line('upload_documents'); ?></h4>
            <div class="col-md-12" style="padding:15px;">
              <div class="form-group">
                <label><?php echo $this->lang->line('documents'); ?> <small style="font-weight:normal; color:#888;">(PDF, JPG, PNG)</small></label>
                <div class="doc-upload-area" onclick="document.getElementById('online_doc_upload').click()">
                    <input id="online_doc_upload" name="document" type="file" accept=".pdf,.jpg,.jpeg,.png" style="display:none;" onchange="showDocName(this)">
                    <div id="doc_upload_placeholder">
                        <i class="fa fa-cloud-upload" style="font-size:28px; color:#999;"></i>
                        <p style="margin:8px 0 0; font-size:13px; color:#666;">Click here to upload your document</p>
                        <p style="margin:2px 0 0; font-size:11px; color:#aaa;">Supported: PDF, JPG, PNG</p>
                    </div>
                    <div id="doc_upload_info" style="display:none;">
                        <i class="fa fa-file" style="font-size:28px; color:#1da0e0;"></i>
                        <p style="margin:8px 0 0; font-size:13px; color:#333;" id="doc_upload_filename"></p>
                    </div>
                </div>
                <span class="text-danger"><?php echo form_error('document'); ?></span>
              </div>
            </div>
         </div>
        </div>
        <?php } ?>
             <div class="row submit-row">

                   <?php if ($is_captcha) {?>
                    <div class="col-lg-6 col-md-6 col-sm-7">
                        <div class="d-flex align-items-center">
                            <span id="captcha_image"><?php echo $this->captchalib->generate_captcha()['image']; ?></span>
                            <span class="fa fa-refresh capture-icon" title='Refresh Captcha' onclick="refreshCaptcha()"></span>
                            <input type="text" name="captcha" placeholder="<?php echo $this->lang->line('captcha'); ?>" class="form-control captcha-input" id="captcha" autocomplete="off" style="max-width:140px;">
                        </div>
                        <span class="text-danger" style="margin-top:5px; display:block;"><?php echo form_error('captcha'); ?></span>
                    </div>
                    <?php }?>

                <div class="col-lg-3 col-md-3 col-sm-5">
                    <div class="form-group" style="margin-bottom:0;">
                       <button type="submit" class="onlineformbtn" style="width:100%; padding:10px 20px;"><i class="fa fa-paper-plane"></i> <?php echo $this->lang->line('submit'); ?></button>
                    </div>
                </div>
                <div class="col-md-3">
                </div>
            </div>
        </div><!--./row-->
</form>
<div id="checkOnlineAdmissionStatus" class="modal fade" role="dialog" tabindex="-1">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header modal-header-small">
        <button type="button" class="close closebtnmodal" data-dismiss="modal">&times;</button>
        <h4 ><?php echo $this->lang->line('check_your_form_status') ?></h4>
      </div>
       <form action="<?php echo base_url() . 'welcome/checkadmissionstatus' ?>" method="post" class="onlineform" id="checkstatusform">
          <div class="modal-body">
            <div class="form-group">
            <label><?php echo $this->lang->line('enter_your_reference_number'); ?></label><small class="req"> *</small>
               <input type="text" class="form-control" name="refno" id="refno" autocomplete="off">
                 <span class="text-danger" id="error_status_refno"></span>
            </div>
             <div class="form-group mb10">
              <label><?php echo $this->lang->line('select_your_date_of_birth'); ?></label><small class="req"> *</small>
               <input type="text"  class="form-control date2"  name="student_dob" id="student_dob" autocomplete="off" readonly="">
                <span class="text-danger" id="error_status_dob"></span>
            </div>
             <span class="text-danger" id="invaliderror"></span>
          </div>
          <div class="modal-footer">
          <button type="button" class="modalclosebtn btn  mdbtn" data-dismiss="modal"><?php echo $this->lang->line('close'); ?></button>
            <button type="submit" class="onlineformbtn mdbtn" ><?php echo $this->lang->line('submit'); ?></button>
          </div>
      </form>
    </div>
  </div>
</div>

<script type="text/javascript">
var date_format = '<?php echo $result     = strtr($this->customlib->getSchoolDateFormat(), ['d' => 'dd', 'm' => 'mm', 'Y' => 'yyyy']) ?>';
var datetime_format = '<?php echo $result = strtr($this->customlib->getSchoolDateFormat(), ['d' => 'DD', 'm' => 'MM', 'M' => 'MMM', 'Y' => 'YYYY']) ?>';

    $(document).ready(function () {
        var class_id = $('#class_id').val();
        var section_id = '<?php echo set_value('section_id', 0) ?>';

        getSectionByClass(class_id, section_id);

        $(document).on('change', '#class_id', function (e) {
            $('#section_id').html("");
            var class_id = $(this).val();
            getSectionByClass(class_id, 0);
        });

        $('.date2').datepicker({
            autoclose: true,
             format: date_format,
            todayHighlight: true
        });

        $('.date').datepicker({
            autoclose: true,
             format: date_format,
            todayHighlight: true
        });

        $('.datetime').datetimepicker({
         format: datetime_format + ' hh:mm a',
          locale:'en'
        });

        function getSectionByClass(class_id, section_id) {
            if (class_id !== "") {
                $('#section_id').html("");

                var div_data = '';
                var url = "";

                $.ajax({
                    type: "POST",
                    url: base_url + "welcome/getSections",
                    data: {'class_id': class_id},
                    dataType: "json",
                    beforeSend: function () {
                        $('#section_id').addClass('dropdownloading');
                    },
                    success: function (data) {
                        $.each(data, function (i, obj)
                        {
                            var sel = "";
                            if (section_id === obj.section_id) {
                                sel = "selected";
                            }
                            div_data += "<option value=" + obj.id + " " + sel + ">" + obj.section + "</option>";
                        });
                        $('#section_id').append(div_data);
                    },
                    complete: function () {
                        $('#section_id').removeClass('dropdownloading');
                    }
                });
            }
        }
    });

    function auto_fill_guardian_address() {
        if ($("#autofill_current_address").is(':checked'))
        {
            $('#current_address').val($('#guardian_address').val());
        }
    }

    function auto_fill_address() {
        if ($("#autofill_address").is(':checked'))
        {
            $('#permanent_address').val($('#current_address').val());
        }
    }

    $('input:radio[name="guardian_is"]').change(
            function () {
                if ($(this).is(':checked')) {
                    var value = $(this).val();
                    if (value === "father") {
                        var father_relation = "<?php echo $this->lang->line('father'); ?>";
                        $('#guardian_name').val($('#father_name').val());
                        $('#guardian_phone').val($('#father_phone').val());
                        $('#guardian_occupation').val($('#father_occupation').val());
                        $('#guardian_relation').val(father_relation);
                    } else if (value === "mother") {
                        var mother_relation = "<?php echo $this->lang->line('mother'); ?>";
                        $('#guardian_name').val($('#mother_name').val());
                        $('#guardian_phone').val($('#mother_phone').val());
                        $('#guardian_occupation').val($('#mother_occupation').val());
                        $('#guardian_relation').val(mother_relation);
                    } else {
                        $('#guardian_name').val("");
                        $('#guardian_phone').val("");
                        $('#guardian_occupation').val("");
                        $('#guardian_relation').val("");
                    }
                }
            });
</script>

<script type="text/javascript">
    function refreshCaptcha(){
        $.ajax({
            type: "POST",
            url: "<?php echo base_url('site/refreshCaptcha'); ?>",
            data: {},
            success: function(captcha){
                $("#captcha_image").html(captcha);
            }
        });
    }
</script>

<script type="text/javascript">
$(document).ready(function(){
$(document).on('submit','#checkstatusform',function(e){
   e.preventDefault(); // avoid to execute the actual submit of the form.
    var form = $(this);
    var url = form.attr('action');
    var form_data = form.serializeArray();

    $.ajax({
           url: url,
           type: "POST",
           dataType:'JSON',
           data: form_data, // serializes the form's elements.
              beforeSend: function () {

               },
              success: function(response) { // your success handler
                if(response.status==0){

                    $.each(response.error, function(key, value) {

                    $('#error_status_' + key).html(value);
                    });
                }else if(response.status==2){

                    $('#error_status_dob').html("");
                    $('#error_status_refno').html("");
                    $('#invaliderror').html(response.error);
                } else{
                    var refno =response.refno ;
                    window.location.href="<?php echo base_url() . 'welcome/online_admission_review/' ?>"+refno ;
                }
              },
             error: function() { // your error handler

             },
             complete: function() {

             }
         });
});
});
</script>

<script>
    function openStatusFormmodal(){
      $('#error_status_dob' ).html("");
      $('#error_status_refno' ).html("");
      $('#invaliderror').html("");
      $('#student_dob').val("");
      $('#student_dob').html("");
      $('#refno' ).val("");
      $(':input').val('');
    }

    function auto_fill_guardian_address() {
        if ($("#autofill_current_address").is(':checked'))
        {
            $('#current_address').val($('#guardian_address').val());
        }
    }

    function auto_fill_address() {
        if ($("#autofill_address").is(':checked'))
        {
            $('#permanent_address').val($('#current_address').val());
        }
    }
</script>

<script>
$(function(){
    $('#checkOnlineAdmissionStatus').modal({
         backdrop: 'static',
         keyboard: false,
         show: false
    });
});
</script>

<script type="text/javascript">
    // Photo preview for frontend admission form
    function previewOnlineAdmissionPhoto(input, previewId) {
        var preview = document.getElementById(previewId);
        var wrap = document.getElementById(previewId + '_wrap');
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
                if (wrap) wrap.style.display = 'none';
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Document upload name display
    function showDocName(input) {
        if (input.files && input.files[0]) {
            var file = input.files[0];
            var name = file.name;
            var size = (file.size / 1024).toFixed(1);
            document.getElementById('doc_upload_filename').textContent = name + ' (' + size + ' KB)';
            document.getElementById('doc_upload_info').style.display = 'block';
            document.getElementById('doc_upload_placeholder').style.display = 'none';
        }
    }
</script>