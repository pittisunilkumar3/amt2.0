<style>
    .status-sent { color: #28a745; }
    .status-delivered { color: #17a2b8; }
    .status-read { color: #007bff; }
    .status-failed { color: #dc3545; }
    .status-pending { color: #ffc107; }
    .status-received { color: #6f42c1; }
    .whatsapp-badge {
        background-color: #25D366;
        color: white;
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: bold;
    }
    .msg-detail-modal .modal-body {
        max-height: 500px;
        overflow-y: auto;
    }
    .msg-detail-modal pre {
        max-height: 300px;
        overflow-y: auto;
        font-size: 12px;
    }
</style>

<div class="content-wrapper">
    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title">
                            <i class="fa fa-whatsapp" style="color: #25D366;"></i>
                            WhatsApp Messages Log
                        </h3>
                        <div class="box-tools pull-right">
                            <a href="<?php echo site_url('admin/whatsappconfig'); ?>" class="btn btn-default btn-sm">
                                <i class="fa fa-cog"></i> Settings
                            </a>
                        </div>
                    </div>
                    <div class="box-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Type</th>
                                        <th>Recipient</th>
                                        <th>Template</th>
                                        <th>Event</th>
                                        <th>Status</th>
                                        <th>Error</th>
                                        <th>Sent At</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($messages)) {
                                        $counter = 1;
                                        foreach ($messages as $msg) { ?>
                                            <tr>
                                                <td><?php echo $counter++; ?></td>
                                                <td>
                                                    <?php if ($msg->message_type == 'outgoing') { ?>
                                                        <span class="label label-primary"><i class="fa fa-arrow-right"></i> Outgoing</span>
                                                    <?php } else { ?>
                                                        <span class="label label-purple"><i class="fa fa-arrow-left"></i> Incoming</span>
                                                    <?php } ?>
                                                </td>
                                                <td>
                                                    <?php echo $msg->recipient_phone; ?>
                                                    <?php if ($msg->recipient_name) { ?>
                                                        <br><small class="text-muted"><?php echo $msg->recipient_name; ?></small>
                                                    <?php } ?>
                                                </td>
                                                <td>
                                                    <?php if ($msg->template_name && $msg->template_name != 'text_message') { ?>
                                                        <span class="whatsapp-badge"><?php echo $msg->template_name; ?></span>
                                                    <?php } else { ?>
                                                        <span class="label label-default">Text</span>
                                                    <?php } ?>
                                                </td>
                                                <td>
                                                    <?php if ($msg->event_type) { ?>
                                                        <?php echo $this->lang->line($msg->event_type); ?>
                                                    <?php } else { ?>
                                                        <small class="text-muted">—</small>
                                                    <?php } ?>
                                                </td>
                                                <td>
                                                    <span class="status-<?php echo $msg->status; ?>">
                                                        <i class="fa fa-circle" style="font-size: 8px;"></i>
                                                        <?php echo ucfirst($msg->status); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <?php if ($msg->error_message) { ?>
                                                        <small class="text-danger" title="<?php echo htmlspecialchars($msg->error_message); ?>">
                                                            <?php echo character_limiter($msg->error_message, 40); ?>
                                                        </small>
                                                    <?php } else { ?>
                                                        <small class="text-muted">—</small>
                                                    <?php } ?>
                                                </td>
                                                <td>
                                                    <small><?php echo date('d M Y h:i A', strtotime($msg->created_at)); ?></small>
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-info btn-xs view_msg_detail"
                                                        data-id="<?php echo $msg->id; ?>"
                                                        data-phone="<?php echo $msg->recipient_phone; ?>"
                                                        data-name="<?php echo $msg->recipient_name; ?>"
                                                        data-template="<?php echo $msg->template_name; ?>"
                                                        data-event="<?php echo $msg->event_type; ?>"
                                                        data-status="<?php echo $msg->status; ?>"
                                                        data-error='<?php echo htmlspecialchars($msg->error_message); ?>'
                                                        data-body='<?php echo htmlspecialchars($msg->template_body); ?>'
                                                        data-json='<?php echo htmlspecialchars($msg->message_json); ?>'
                                                        data-wa-id="<?php echo $msg->whatsapp_message_id; ?>"
                                                        data-created="<?php echo date('d M Y h:i A', strtotime($msg->created_at)); ?>">
                                                        <i class="fa fa-eye"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php }
                                    } else { ?>
                                        <tr>
                                            <td colspan="9" class="text-center text-muted">
                                                No WhatsApp messages found
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Message Detail Modal -->
<div class="modal fade msg-detail-modal" id="msgDetailModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title"><i class="fa fa-whatsapp" style="color: #25D366;"></i> Message Detail</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-bordered">
                            <tr><th width="40%">Phone</th><td id="detail_phone"></td></tr>
                            <tr><th>Recipient</th><td id="detail_name"></td></tr>
                            <tr><th>Template</th><td id="detail_template"></td></tr>
                            <tr><th>Event</th><td id="detail_event"></td></tr>
                            <tr><th>Status</th><td id="detail_status"></td></tr>
                            <tr><th>WA Message ID</th><td id="detail_wa_id"></td></tr>
                            <tr><th>Sent At</th><td id="detail_created"></td></tr>
                            <tr><th>Error</th><td id="detail_error"></td></tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h5>Message Body</h5>
                        <pre id="detail_body" class="bg-gray-light" style="padding: 10px;"></pre>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <h5>API Request JSON</h5>
                        <pre id="detail_json" class="bg-black text-green" style="padding: 10px; color: #0f0;"></pre>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('.view_msg_detail').on('click', function() {
        var btn = $(this);
        $('#detail_phone').text(btn.data('phone'));
        $('#detail_name').text(btn.data('name') || '—');
        $('#detail_template').text(btn.data('template') || '—');
        $('#detail_event').text(btn.data('event') || '—');
        $('#detail_status').html('<span class="status-' + btn.data('status') + '"><i class="fa fa-circle" style="font-size:8px;"></i> ' + btn.data('status') + '</span>');
        $('#detail_wa_id').text(btn.data('wa-id') || '—');
        $('#detail_created').text(btn.data('created'));
        $('#detail_error').text(btn.data('error') || '—');
        $('#detail_body').text(btn.data('body') || '—');

        var jsonStr = btn.data('json');
        try {
            var jsonObj = JSON.parse(jsonStr);
            $('#detail_json').text(JSON.stringify(jsonObj, null, 2));
        } catch(e) {
            $('#detail_json').text(jsonStr);
        }

        $('#msgDetailModal').modal('show');
    });
});
</script>
