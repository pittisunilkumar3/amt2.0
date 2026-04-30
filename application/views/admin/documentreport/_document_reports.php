<div class="row">
    <div class="col-md-12">
        <div class="box box-primary border0 mb0 margesection">
            <div class="box-header with-border">
                <h3 class="box-title"><i class="fa fa-search"></i> <?php echo $this->lang->line('document_reports'); ?></h3>
            </div>
            <div class="">
                <ul class="reportlists">
                    <li class="col-lg-4 col-md-4 col-sm-6 <?php echo set_SubSubmenu('Reports/document_reports/tc_report'); ?> ">
                        <a href="<?php echo base_url(); ?>admin/documentreport/tcreport"><i class="fa fa-file-text-o"></i> <?php echo $this->lang->line('tc_report'); ?></a>
                    </li>
                </ul>
            </div>
        </div> 
    </div>
</div>
