<div class="content-wrapper">
    <section class="content-header">
        <h1>
            <i class="fa fa-camera"></i> <?php echo $this->lang->line('face_attendance_mark_attendance'); ?>
            <small>Face recognition attendance for students & staff</small>
        </h1>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="box box-primary">
                    <div class="box-header with-border">
                        <h3 class="box-title"><?php echo $this->lang->line('face_attendance_mark_attendance'); ?></h3>
                        <div class="box-tools pull-right">
                            <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
                        </div>
                    </div>

                    <div class="box-body">
                        <!-- Alert Messages -->
                        <div id="alertContainer"></div>

                        <!-- Staff Attendance Integration Info -->
                        <?php if (isset($payroll_info) && $payroll_info): ?>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="callout callout-info" id="payrollInfoBox">
                                    <h4><i class="fa fa-info-circle"></i> Staff Attendance Integration</h4>
                                    <p>Staff face attendance is <strong>automatically synced</strong> to the main <code>staff_attendance</code> table (used by Payroll).</p>
                                    <ul style="margin-top: 8px; margin-bottom: 0;">
                                        <li><strong>Shift Start:</strong> <?php echo isset($payroll_info['attendance_type']['shift_start']) ? $payroll_info['attendance_type']['shift_start'] : '09:00:00'; ?></li>
                                        <li><strong>Grace Period:</strong> <?php echo isset($payroll_info['attendance_type']['grace_minutes']) ? $payroll_info['attendance_type']['grace_minutes'] : 15; ?> minutes (arriving after <?php echo isset($payroll_info['attendance_type']['grace_until']) ? $payroll_info['attendance_type']['grace_until'] : '09:15:00'; ?> = <span class="text-yellow">Late</span>)</li>
                                        <li><strong>Payroll Cutoff Day:</strong> <?php echo isset($payroll_info['cutoff']['cutoff_day']) ? $payroll_info['cutoff']['cutoff_day'] : 1; ?></li>
                                        <li><strong>Current Period:</strong> <?php echo isset($payroll_info['cutoff']['period_start']) ? $payroll_info['cutoff']['period_start'] : ''; ?> to <?php echo isset($payroll_info['cutoff']['period_end']) ? $payroll_info['cutoff']['period_end'] : ''; ?></li>
                                        <li><strong>Attendance Type IDs:</strong> <code>1=Present</code>, <code>2=Late</code>, <code>3=Absent</code>, <code>4=Half Day</code>, <code>5=Holiday</code></li>
                                    </ul>
                                    <?php if (!empty($staff_today_att)): ?>
                                    <hr style="margin: 10px 0; border-color: rgba(0,0,0,0.1);">
                                    <p style="margin-bottom: 5px;"><strong>Staff already marked today (existing records preserved):</strong></p>
                                    <?php foreach ($staff_today_att as $sid => $att): ?>
                                        <?php
                                        $att_type = '';
                                        switch($att['staff_attendance_type_id']) {
                                            case 1: $att_type = '<span class="text-green">Present</span>'; break;
                                            case 2: $att_type = '<span class="text-yellow">Late</span>'; break;
                                            case 3: $att_type = '<span class="text-red">Absent</span>'; break;
                                            case 4: $att_type = '<span class="text-yellow">Half Day</span>'; break;
                                            case 5: $att_type = '<span class="text-blue">Holiday</span>'; break;
                                            default: $att_type = '<span class="text-gray">Type ' . $att['staff_attendance_type_id'] . '</span>';
                                        }
                                        ?>
                                        <span class="label label-default">ID:<?php echo $sid; ?> &mdash; <?php echo $att_type; ?><?php echo $att['check_in_time'] ? ' @ ' . $att['check_in_time'] : ''; ?></span>
                                    <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <!-- Type Selection -->
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <div class="btn-group" style="margin-right: 15px;">
                                    <button type="button" class="btn btn-primary btn-lg active" id="typeStudentBtn" onclick="switchType('student')">
                                        <i class="fa fa-graduation-cap"></i> Students (<span id="studentCountLabel"><?php echo count($registered_students); ?></span>)
                                    </button>
                                    <button type="button" class="btn btn-default btn-lg" id="typeStaffBtn" onclick="switchType('staff')">
                                        <i class="fa fa-user-md"></i> Staff (<span id="staffCountLabel"><?php echo count($registered_staff); ?></span>)
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Control Buttons -->
                        <div class="row mb-3">
                            <div class="col-md-12 text-center">
                                <button id="startBtn" class="btn btn-lg btn-primary">
                                    <i class="fa fa-play"></i> Start Face Recognition
                                </button>
                                <button id="stopBtn" class="btn btn-lg btn-danger" style="display: none;">
                                    <i class="fa fa-stop"></i> Stop Recognition
                                </button>
                                <button id="saveBtn" class="btn btn-lg btn-success" style="display: none;">
                                    <i class="fa fa-save"></i> Save Attendance
                                </button>
                            </div>
                        </div>

                        <!-- Statistics -->
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <div class="info-box bg-aqua">
                                    <span class="info-box-icon"><i class="fa fa-users"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Total Registered</span>
                                        <span class="info-box-number" id="totalCount">0</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-box bg-green">
                                    <span class="info-box-icon"><i class="fa fa-check"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Present</span>
                                        <span class="info-box-number" id="presentCount">0</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-box bg-red">
                                    <span class="info-box-icon"><i class="fa fa-times"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Absent</span>
                                        <span class="info-box-number" id="absentCount">0</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3" id="lateInfoBox" style="display: none;">
                                <div class="info-box bg-yellow">
                                    <span class="info-box-icon"><i class="fa fa-clock-o"></i></span>
                                    <div class="info-box-content">
                                        <span class="info-box-text">Late (after grace)</span>
                                        <span class="info-box-number" id="lateCount">0</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Main Content -->
                        <div class="row">
                            <!-- Video Section -->
                            <div class="col-md-6">
                                <div class="box box-solid">
                                    <div class="box-header">
                                        <h3 class="box-title"><i class="fa fa-video-camera"></i> Camera Feed</h3>
                                        <div class="box-tools pull-right">
                                            <span class="label label-primary" id="currentTypeLabel">Students</span>
                                        </div>
                                    </div>
                                    <div class="box-body">
                                        <div class="video-container-wrapper">
                                            <div class="video-container">
                                                <video id="video" autoplay playsinline></video>
                                                <canvas id="overlay"></canvas>
                                                <div class="face-overlay" id="faceOverlay"></div>
                                                <div class="recognition-status" id="recognitionStatus">
                                                    <span class="status-indicator processing" id="statusIndicator"></span>
                                                    <span id="statusText">Loading...</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Person List Section -->
                            <div class="col-md-6">
                                <div class="box box-solid">
                                    <div class="box-header">
                                        <h3 class="box-title"><i class="fa fa-list"></i> <span id="listTitle">Student</span> List</h3>
                                        <div class="box-tools pull-right">
                                            <span class="label label-default">Date: <?php echo date('d M Y'); ?></span>
                                        </div>
                                    </div>
                                    <div class="box-body">
                                        <div class="student-list" id="studentList">
                                            <div class="alert alert-info">
                                                <i class="fa fa-info-circle"></i> Loading registered persons...
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Staff Sync Results (shown after save) -->
                        <div class="row" id="syncResultsRow" style="display: none;">
                            <div class="col-md-12">
                                <div class="box box-solid box-info">
                                    <div class="box-header">
                                        <h3 class="box-title"><i class="fa fa-exchange"></i> Staff Attendance Sync Results</h3>
                                    </div>
                                    <div class="box-body">
                                        <p class="text-muted">The following staff attendance records were synced to the main <code>staff_attendance</code> table (used by Payroll system):</p>
                                        <div id="syncResultsContent"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Include Face-API.js (local copy for reliability) -->
<script src="<?php echo base_url('assets/face_attendance_models/face-api.min.js'); ?>"></script>

<style>
    .mb-3 { margin-bottom: 20px; }
    .video-container-wrapper {
        position: relative;
        background: #1a1a1a;
        border-radius: 5px;
        overflow: hidden;
    }
    .video-container {
        position: relative;
        width: 100%;
        max-width: 640px;
        margin: 0 auto;
    }
    #video {
        width: 100%;
        height: auto;
        display: block;
        object-fit: cover;
        max-height: 480px;
    }
    #overlay {
        position: absolute;
        top: 0; left: 0;
        pointer-events: none;
        width: 100%; height: 100%;
    }
    .face-overlay {
        position: absolute;
        top: 0; left: 0;
        width: 100%; height: 100%;
        pointer-events: none;
        z-index: 10;
    }
    .face-box {
        position: absolute;
        border: 3px solid;
        border-radius: 8px;
        transition: all 0.2s ease;
        box-shadow: 0 0 10px rgba(0,0,0,0.3);
    }
    .face-box.processing {
        border-color: #ffc107;
        background: rgba(255,193,7,0.1);
        animation: pulse 1.5s infinite;
    }
    .face-box.recognized {
        border-color: #00a65a;
        background: rgba(0,166,90,0.1);
        box-shadow: 0 0 15px rgba(0,166,90,0.5);
    }
    .face-box.unknown {
        border-color: #dd4b39;
        background: rgba(221,75,57,0.1);
    }
    .face-label {
        position: absolute;
        top: -30px;
        left: 50%;
        transform: translateX(-50%);
        background: rgba(0,0,0,0.8);
        color: white;
        padding: 5px 10px;
        border-radius: 15px;
        font-size: 12px;
        font-weight: 600;
        white-space: nowrap;
        min-width: 100px;
        text-align: center;
    }
    .face-label.processing { background: rgba(255,193,7,0.9); }
    .face-label.recognized { background: rgba(0,166,90,0.9); }
    .face-label.unknown { background: rgba(221,75,57,0.9); }
    .face-label.late { background: rgba(243,156,18,0.9); }
    .confidence-score {
        font-size: 10px;
        opacity: 0.8;
        display: block;
        margin-top: 2px;
    }
    @keyframes pulse {
        0% { opacity: 1; } 50% { opacity: 0.6; } 100% { opacity: 1; }
    }
    .recognition-status {
        position: absolute;
        top: 10px; right: 10px;
        background: rgba(0,0,0,0.7);
        color: white;
        padding: 10px 15px;
        border-radius: 20px;
        font-size: 14px;
        font-weight: 600;
        z-index: 20;
    }
    .status-indicator {
        display: inline-block;
        width: 10px; height: 10px;
        border-radius: 50%;
        margin-right: 8px;
        animation: blink 2s infinite;
    }
    .status-indicator.active { background: #00a65a; }
    .status-indicator.processing { background: #ffc107; }
    @keyframes blink {
        0%, 100% { opacity: 1; } 50% { opacity: 0.3; }
    }
    .student-list {
        max-height: 450px;
        overflow-y: auto;
        padding: 10px;
    }
    .student-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 15px;
        margin-bottom: 10px;
        background: #f9f9f9;
        border-radius: 5px;
        border: 1px solid #ddd;
        transition: all 0.3s ease;
    }
    .student-item:hover {
        box-shadow: 0 3px 10px rgba(0,0,0,0.1);
    }
    .student-item.present {
        background: #d4edda;
        border-color: #c3e6cb;
    }
    .student-item.present.late {
        background: #fff3cd;
        border-color: #ffeeba;
    }
    .student-item.already-marked {
        background: #e8ecef;
        border-color: #dee2e6;
        opacity: 0.75;
    }
    .student-item.already-marked .student-name::after {
        content: ' (Already Marked)';
        font-size: 11px;
        color: #6c757d;
        font-weight: normal;
    }
    .student-info { flex: 1; }
    .student-name {
        font-weight: 600;
        color: #333;
        font-size: 15px;
    }
    .student-reg {
        font-size: 13px;
        color: #666;
        margin-top: 3px;
    }
    .status-badge {
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }
    .status-present { background: #00a65a; color: white; }
    .status-absent { background: #dd4b39; color: white; }
    .status-late { background: #f39c12; color: white; }
    .status-existing { background: #6c757d; color: white; }
    .person-type-badge {
        font-size: 10px;
        padding: 1px 6px;
        border-radius: 8px;
        margin-right: 5px;
    }
    .type-student { background: #3c8dbc; color: white; }
    .type-staff { background: #f39c12; color: white; }
    .sync-result-item {
        padding: 8px 12px;
        margin-bottom: 6px;
        border-radius: 4px;
        border-left: 4px solid;
    }
    .sync-result-item.synced { background: #d4edda; border-color: #00a65a; }
    .sync-result-item.skipped { background: #fff3cd; border-color: #f39c12; }
    .sync-result-item.error { background: #f8d7da; border-color: #dd4b39; }
</style>

<script>
    let persons = [];
    let recognizedPersons = new Map();
    let isRecognizing = false;
    let videoStream = null;
    let faceDetectionInterval = null;
    let faceMatcher = null;
    let currentType = 'student';
    let payrollInfo = null;
    let staffAttData = {}; // staff_id => attendance record
    let modelsLoaded = false;
    let cameraReady = false;

    const video = document.getElementById('video');
    const overlay = document.getElementById('overlay');
    const faceOverlay = document.getElementById('faceOverlay');
    const recognitionStatus = document.getElementById('recognitionStatus');
    const statusIndicator = document.getElementById('statusIndicator');
    const statusText = document.getElementById('statusText');
    const startBtn = document.getElementById('startBtn');
    const stopBtn = document.getElementById('stopBtn');
    const saveBtn = document.getElementById('saveBtn');
    const presentCount = document.getElementById('presentCount');
    const absentCount = document.getElementById('absentCount');
    const totalCount = document.getElementById('totalCount');
    const lateCount = document.getElementById('lateCount');
    const alertContainer = document.getElementById('alertContainer');

    startBtn.addEventListener('click', startFaceRecognition);
    stopBtn.addEventListener('click', stopFaceRecognition);
    saveBtn.addEventListener('click', saveAttendance);

    // Check if face-api.js loaded successfully
    if (typeof faceapi === 'undefined') {
        updateRecognitionStatus('', 'face-api.js not loaded');
        showAlert('Error: Face recognition library failed to load. Please refresh the page or check your internet connection.', 'danger');
        startBtn.disabled = true;
        startBtn.classList.add('disabled');
    } else {
        updateRecognitionStatus('processing', 'Ready - Click Start');
    }

    loadPersons(currentType);

    function switchType(type) {
        currentType = type;
        if (type === 'student') {
            document.getElementById('typeStudentBtn').className = 'btn btn-primary btn-lg active';
            document.getElementById('typeStaffBtn').className = 'btn btn-default btn-lg';
            document.getElementById('currentTypeLabel').textContent = 'Students';
            document.getElementById('listTitle').textContent = 'Student';
            document.getElementById('lateInfoBox').style.display = 'none';
        } else {
            document.getElementById('typeStudentBtn').className = 'btn btn-default btn-lg';
            document.getElementById('typeStaffBtn').className = 'btn btn-primary btn-lg active';
            document.getElementById('currentTypeLabel').textContent = 'Staff';
            document.getElementById('listTitle').textContent = 'Staff';
            document.getElementById('lateInfoBox').style.display = 'block';
        }

        if (isRecognizing) { stopFaceRecognition(); }
        recognizedPersons.clear();
        loadPersons(type);
    }

    function loadPersons(type) {
        document.getElementById('studentList').innerHTML = '<div class="alert alert-info"><i class="fa fa-spinner fa-spin"></i> Loading...</div>';

        $.ajax({
            url: '<?php echo base_url("admin/face_attendance_register/get_registered_students"); ?>',
            type: 'POST',
            data: { type: type },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    persons = response.students;
                    staffAttData = response.staff_att || {};
                    payrollInfo = response.payroll_info || null;
                    totalCount.textContent = persons.length;
                    absentCount.textContent = persons.length;
                    presentCount.textContent = 0;
                    lateCount.textContent = 0;
                    renderPersonList(persons);
                } else {
                    showAlert('Error loading data', 'danger');
                }
            },
            error: function() {
                showAlert('Error loading data', 'danger');
            }
        });
    }

    function renderPersonList(personList) {
        let html = '';
        if (!personList || personList.length === 0) {
            html = '<div class="alert alert-warning"><i class="fa fa-exclamation-triangle"></i> No ' + currentType + 's registered yet.</div>';
        } else {
            personList.forEach(function(person) {
                const typeBadge = person.person_type === 'staff'
                    ? '<span class="person-type-badge type-staff">Staff</span>'
                    : '<span class="person-type-badge type-student">Student</span>';

                // Check if staff already has attendance in main staff_attendance table
                let existingAttHtml = '';
                let extraClasses = '';
                let statusBadgeClass = 'status-absent';
                let statusText = 'Absent';

                if (person.person_type === 'staff' && person.staff_id && staffAttData[person.staff_id]) {
                    const existingAtt = staffAttData[person.staff_id];
                    extraClasses = 'already-marked';
                    statusBadgeClass = 'status-existing';
                    const attTypeNames = {1: 'Present', 2: 'Late', 3: 'Absent', 4: 'Half Day', 5: 'Holiday'};
                    const attType = attTypeNames[existingAtt.staff_attendance_type_id] || 'Type ' + existingAtt.staff_attendance_type_id;
                    const timeStr = existingAtt.check_in_time ? ' @ ' + existingAtt.check_in_time : '';
                    existingAttHtml = '<div class="student-reg" style="color: #856404;"><i class="fa fa-exclamation-triangle text-yellow"></i> Already in staff_attendance: <strong>' + attType + timeStr + '</strong></div>';
                    statusText = 'Existing';
                }

                html += `
                    <div class="student-item ${extraClasses}" id="student-${person.registration_number}" data-student-id="${person.id}" data-person-type="${person.person_type}" data-staff-id="${person.staff_id || ''}">
                        <div class="student-info">
                            <div class="student-name">
                                ${typeBadge} ${person.first_name} ${person.last_name}
                            </div>
                            <div class="student-reg">
                                ${person.registration_number}
                                ${person.admission_no ? ' | Adm: ' + person.admission_no : ''}
                            </div>
                            ${existingAttHtml}
                        </div>
                        <span class="status-badge ${statusBadgeClass}" id="status-${person.registration_number}">${statusText}</span>
                    </div>
                `;
            });
        }
        document.getElementById('studentList').innerHTML = html;
    }

    function showAlert(message, type) {
        const iconMap = { success: 'check', danger: 'exclamation-triangle', warning: 'exclamation-circle', info: 'info' };
        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <i class="fa fa-${iconMap[type] || 'info'}"></i> ${message}
            </div>
        `;
        alertContainer.innerHTML = alertHtml;
        setTimeout(function() { $('.alert').fadeOut(); }, 8000);
    }

    function updateRecognitionStatus(status, text) {
        statusIndicator.className = `status-indicator ${status}`;
        statusText.textContent = text;
    }

    function createFaceOverlay(detection, result) {
        const box = detection.detection.box;
        const videoRect = video.getBoundingClientRect();
        const scaleX = videoRect.width / video.videoWidth;
        const scaleY = videoRect.height / video.videoHeight;

        const faceBox = document.createElement('div');
        faceBox.className = 'face-box';
        faceBox.style.left = `${box.x * scaleX}px`;
        faceBox.style.top = `${box.y * scaleY}px`;
        faceBox.style.width = `${box.width * scaleX}px`;
        faceBox.style.height = `${box.height * scaleY}px`;

        const label = document.createElement('div');
        label.className = 'face-label';

        if (result && result.distance < 0.6) {
            const person = persons.find(s => s.registration_number === result.label);
            const personName = person ? `${person.first_name} ${person.last_name}` : result.label;
            const confidence = Math.round((1 - result.distance) * 100);

            // Determine if Late based on payroll grace period
            const now = new Date();
            let isLate = false;
            let attStatusText = 'Present';

            if (currentType === 'staff' && payrollInfo) {
                const graceUntil = payrollInfo.attendance_type ? payrollInfo.attendance_type.grace_until : null;
                if (graceUntil) {
                    const [gH, gM] = graceUntil.split(':').map(Number);
                    if (now.getHours() > gH || (now.getHours() === gH && now.getMinutes() > gM)) {
                        isLate = true;
                        attStatusText = 'Late';
                    }
                }
            }

            if (isLate) {
                faceBox.classList.add('recognized');
                faceBox.style.borderColor = '#f39c12';
                label.classList.add('late');
            } else {
                faceBox.classList.add('recognized');
                label.classList.add('recognized');
            }

            const typeIcon = person && person.person_type === 'staff' ? '👤' : '🎓';
            label.innerHTML = `
                ${typeIcon} ${personName}
                <span class="confidence-score">${confidence}% &mdash; ${attStatusText}</span>
            `;

            updatePersonAttendance(result.label, attStatusText, confidence, person, isLate);
        } else {
            faceBox.classList.add('unknown');
            label.classList.add('unknown');
            label.textContent = result ? 'Unknown Face' : 'Processing...';
        }

        faceBox.appendChild(label);
        return faceBox;
    }

    function clearFaceOverlays() { faceOverlay.innerHTML = ''; }

    function updatePersonAttendance(registrationNumber, status, confidence, personData, isLate) {
        if (!recognizedPersons.has(registrationNumber)) {
            recognizedPersons.set(registrationNumber, {
                registration_number: registrationNumber,
                student_id: personData.id,
                confidence: confidence,
                class_id: personData.class_id,
                section_id: personData.section_id,
                person_type: personData.person_type || currentType,
                staff_id: personData.staff_id || '',
                status: status,
                is_late: isLate,
                timestamp: new Date().toISOString()
            });

            const studentElement = document.getElementById(`student-${registrationNumber}`);
            const statusElement = document.getElementById(`status-${registrationNumber}`);

            if (studentElement && statusElement) {
                studentElement.classList.add('present');
                if (isLate) {
                    studentElement.classList.add('late');
                }

                if (isLate) {
                    statusElement.textContent = 'Late';
                    statusElement.className = 'status-badge status-late';
                } else {
                    statusElement.textContent = 'Present';
                    statusElement.className = 'status-badge status-present';
                }

                studentElement.style.animation = 'pulse 0.5s ease-in-out';
                setTimeout(() => { studentElement.style.animation = ''; }, 500);
            }

            updateStats();
        }
    }

    function updateStats() {
        const present = recognizedPersons.size;
        const total = persons.length;
        const absent = total - present;
        let late = 0;

        recognizedPersons.forEach((data) => {
            if (data.is_late) late++;
        });

        presentCount.textContent = present;
        absentCount.textContent = absent;
        if (currentType === 'staff') {
            lateCount.textContent = late;
        }
    }

    async function startFaceRecognition() {
        // Check face-api.js is loaded
        if (typeof faceapi === 'undefined') {
            showAlert('Error: Face recognition library (face-api.js) is not loaded. Please refresh the page.', 'danger');
            return;
        }

        if (persons.length === 0) {
            showAlert('No registered ' + currentType + 's found. Please register students/staff first on the Registration page.', 'warning');
            return;
        }

        try {
            startBtn.disabled = true;
            startBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Starting...';

            // Step 1: Load models
            if (!modelsLoaded) {
                updateRecognitionStatus('processing', 'Loading AI models...');
                const MODEL_URL = '<?php echo base_url("assets/face_attendance_models"); ?>';
                await Promise.all([
                    faceapi.nets.ssdMobilenetv1.loadFromUri(MODEL_URL),
                    faceapi.nets.faceRecognitionNet.loadFromUri(MODEL_URL),
                    faceapi.nets.faceLandmark68Net.loadFromUri(MODEL_URL)
                ]);
                modelsLoaded = true;
            }

            // Step 2: Start camera
            updateRecognitionStatus('processing', 'Starting camera...');
            try {
                videoStream = await navigator.mediaDevices.getUserMedia({
                    video: { width: { ideal: 640 }, height: { ideal: 480 }, facingMode: 'user' }
                });
            } catch (camErr) {
                let errMsg = 'Camera access denied.';
                if (camErr.name === 'NotAllowedError') {
                    errMsg = 'Camera permission denied. Please allow camera access in your browser settings and try again.';
                } else if (camErr.name === 'NotFoundError') {
                    errMsg = 'No camera found. Please connect a camera and try again.';
                } else if (camErr.name === 'NotReadableError') {
                    errMsg = 'Camera is already in use by another application. Please close it and try again.';
                } else {
                    errMsg = 'Camera error: ' + camErr.message;
                }
                throw new Error(errMsg);
            }

            video.srcObject = videoStream;
            await new Promise((resolve, reject) => {
                video.onloadedmetadata = () => { video.play(); resolve(); };
                video.onerror = () => reject(new Error('Failed to load video stream'));
                setTimeout(() => reject(new Error('Camera start timed out')), 10000);
            });
            cameraReady = true;

            // Step 3: Load face descriptors from registered images
            updateRecognitionStatus('processing', 'Loading face data (' + persons.length + ' persons)...');
            const labeledDescriptors = await loadFaceDescriptors();
            if (labeledDescriptors.length === 0) {
                updateRecognitionStatus('', 'No face images found');
                showAlert('No valid face images found for registered persons. Please re-register with clear face photos.', 'warning');
                stopFaceRecognition();
                return;
            }

            // Step 4: Create matcher and start recognition
            faceMatcher = new faceapi.FaceMatcher(labeledDescriptors, 0.6);
            const canvas = faceapi.createCanvasFromMedia(video);
            const videoContainer = document.querySelector('.video-container');
            videoContainer.appendChild(canvas);
            const displaySize = { width: video.videoWidth, height: video.videoHeight };
            faceapi.matchDimensions(canvas, displaySize);
            canvas.style.cssText = 'position:absolute;top:0;left:0;width:100%;height:100%;';

            isRecognizing = true;
            startBtn.style.display = 'none';
            stopBtn.style.display = 'inline-block';
            updateRecognitionStatus('active', 'Face Recognition Active');
            showAlert('Face recognition started! ' + labeledDescriptors.length + ' face(s) loaded. Look at the camera.', 'success');
            recognizeFaces(canvas, displaySize);

        } catch (error) {
            console.error('Face Recognition Error:', error);
            updateRecognitionStatus('', 'Error: ' + error.message);
            showAlert('Error: ' + error.message, 'danger');
            startBtn.disabled = false;
            startBtn.innerHTML = '<i class="fa fa-play"></i> Start Face Recognition';
            stopFaceRecognition();
        }
    }

    async function loadFaceDescriptors() {
        const labeledDescriptors = [];
        let loadedCount = 0;
        let failedCount = 0;
        for (const person of persons) {
            const descriptions = [];
            if (person.face_images && person.face_images.length > 0) {
                for (const imageUrl of person.face_images) {
                    try {
                        const img = await faceapi.fetchImage(imageUrl);
                        const detection = await faceapi.detectSingleFace(img).withFaceLandmarks().withFaceDescriptor();
                        if (detection) {
                            descriptions.push(detection.descriptor);
                        } else {
                            console.warn('No face detected in image:', imageUrl);
                        }
                    } catch (error) {
                        console.warn('Error loading face image:', imageUrl, error);
                        failedCount++;
                    }
                }
            }
            if (descriptions.length > 0) {
                labeledDescriptors.push(new faceapi.LabeledFaceDescriptors(person.registration_number, descriptions));
                loadedCount++;
            }
        }
        if (failedCount > 0) {
            console.warn('Failed to load ' + failedCount + ' face image(s)');
        }
        return labeledDescriptors;
    }

    async function recognizeFaces(canvas, displaySize) {
        if (!isRecognizing) return;
        try {
            const detections = await faceapi.detectAllFaces(video).withFaceLandmarks().withFaceDescriptors();
            const resizedDetections = faceapi.resizeResults(detections, displaySize);
            canvas.getContext('2d').clearRect(0, 0, canvas.width, canvas.height);
            clearFaceOverlays();
            const results = resizedDetections.map(d => faceMatcher.findBestMatch(d.descriptor));
            resizedDetections.forEach((detection, i) => {
                faceOverlay.appendChild(createFaceOverlay(detection, results[i]));
            });
            const hasRecog = results.some(r => r && r.distance < 0.6);
            if (hasRecog) updateRecognitionStatus('active', 'Face Recognition Active');
            else if (detections.length > 0) updateRecognitionStatus('processing', 'Processing faces...');
        } catch (error) { console.error('Recognition error:', error); }
        faceDetectionInterval = setTimeout(() => recognizeFaces(canvas, displaySize), 150);
    }

    function stopFaceRecognition() {
        isRecognizing = false;
        if (faceDetectionInterval) { clearTimeout(faceDetectionInterval); faceDetectionInterval = null; }
        if (videoStream) { videoStream.getTracks().forEach(t => t.stop()); videoStream = null; }
        cameraReady = false;
        clearFaceOverlays();
        const canvas = document.querySelector('.video-container canvas');
        if (canvas) canvas.remove();

        stopBtn.style.display = 'none';
        startBtn.disabled = false;
        startBtn.innerHTML = '<i class="fa fa-play"></i> Start Face Recognition';

        if (recognizedPersons.size > 0) {
            saveBtn.style.display = 'inline-block';
            let lateCount = 0;
            recognizedPersons.forEach(d => { if (d.is_late) lateCount++; });
            let msg = `Recognition stopped. ${recognizedPersons.size} person(s) marked present.`;
            if (lateCount > 0) msg += ` ${lateCount} marked as Late (after grace period).`;
            msg += ' Click "Save Attendance" to save.';
            showAlert(msg, 'info');
            updateRecognitionStatus('', 'Stopped - ' + recognizedPersons.size + ' recognized');
        } else {
            startBtn.style.display = 'inline-block';
            updateRecognitionStatus('processing', 'Ready - Click Start');
            showAlert('Recognition stopped. No one was recognized.', 'warning');
        }
    }

    function saveAttendance() {
        if (recognizedPersons.size === 0) {
            showAlert('No attendance to save', 'warning');
            return;
        }

        const attendanceData = [];
        recognizedPersons.forEach((data, regNumber) => { attendanceData.push(data); });

        // Add remaining as absent
        persons.forEach(person => {
            if (!recognizedPersons.has(person.registration_number)) {
                // Skip staff who already have attendance in staff_attendance
                if (person.person_type === 'staff' && person.staff_id && staffAttData[person.staff_id]) {
                    return; // Don't mark absent for staff who already have a record
                }
                attendanceData.push({
                    registration_number: person.registration_number,
                    student_id: person.id,
                    class_id: person.class_id,
                    section_id: person.section_id,
                    person_type: person.person_type || currentType,
                    staff_id: person.staff_id || '',
                    status: 'Absent',
                    confidence: 0,
                    is_late: false
                });
            }
        });

        saveBtn.disabled = true;
        saveBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Saving...';

        $.ajax({
            url: '<?php echo base_url("admin/face_attendance_register/save_attendance"); ?>',
            type: 'POST',
            data: {
                attendance_data: JSON.stringify(attendanceData),
                detected_faces: recognizedPersons.size
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    showAlert(response.message, 'success');

                    // Show staff sync results
                    if (response.staff_sync_results && response.staff_sync_results.length > 0) {
                        displaySyncResults(response.staff_sync_results);
                    }

                    recognizedPersons.clear();
                    updateStats();
                    $('.student-item').removeClass('present late');
                    $('.status-badge').not('.status-existing').removeClass('status-present status-late').addClass('status-absent').text('Absent');

                    saveBtn.style.display = 'none';
                    startBtn.style.display = 'inline-block';
                    setTimeout(function() { location.reload(); }, 5000);
                } else {
                    showAlert('Error: ' + response.message, 'danger');
                }
            },
            error: function() { showAlert('Error saving attendance', 'danger'); },
            complete: function() {
                saveBtn.disabled = false;
                saveBtn.innerHTML = '<i class="fa fa-save"></i> Save Attendance';
            }
        });
    }

    function displaySyncResults(results) {
        let html = '';
        results.forEach(function(r) {
            const cls = r.synced ? 'synced' : 'skipped';
            const icon = r.synced ? 'fa-check-circle text-green' : 'fa-exclamation-circle text-yellow';
            const lateBadge = r.is_late ? ' <span class="label label-warning">Late</span>' : '';
            html += `
                <div class="sync-result-item ${cls}">
                    <i class="fa ${icon}"></i>
                    <strong>${r.name}</strong> &mdash; ${r.type}${lateBadge}
                    <br><small>${r.message}</small>
                </div>
            `;
        });
        document.getElementById('syncResultsContent').innerHTML = html;
        document.getElementById('syncResultsRow').style.display = 'block';
    }
</script>
