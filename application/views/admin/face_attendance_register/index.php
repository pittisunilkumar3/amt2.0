<style>
    .nav-tabs-custom > .nav-tabs > li.active {
        border-top-color: #3c8dbc;
    }
    .face-register-card {
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 12px;
        margin-bottom: 10px;
        background: white;
        transition: all 0.3s ease;
        cursor: pointer;
    }
    .face-register-card:hover {
        box-shadow: 0 3px 12px rgba(0,0,0,0.12);
        transform: translateY(-1px);
    }
    .face-register-card.selected {
        border-color: #3c8dbc;
        background: #e8f0fe;
        box-shadow: 0 0 0 2px rgba(60,141,188,0.3);
    }
    .face-register-card.registered {
        border-left: 4px solid #00a65a;
    }
    .face-register-card .person-avatar {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #ddd;
        background: #f5f5f5;
    }
    .badge-registered {
        background: #00a65a;
        color: white;
        padding: 2px 8px;
        border-radius: 10px;
        font-size: 11px;
    }
    .badge-unregistered {
        background: #f39c12;
        color: white;
        padding: 2px 8px;
        border-radius: 10px;
        font-size: 11px;
    }
    .capture-section {
        background: #f9f9f9;
        padding: 20px;
        border-radius: 8px;
        margin: 20px 0;
        border: 1px solid #e0e0e0;
    }
    .video-container {
        position: relative;
        background: #000;
        border-radius: 8px;
        overflow: hidden;
        max-width: 640px;
        margin: 0 auto 20px;
    }
    #video, #staffVideo {
        width: 100%;
        height: auto;
        display: block;
    }
    .video-overlay {
        position: absolute;
        top: 10px;
        right: 10px;
        background: rgba(0,0,0,0.7);
        color: white;
        padding: 8px 15px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 600;
    }
    .captured-images {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        justify-content: center;
        margin-top: 15px;
    }
    .image-preview {
        position: relative;
        width: 100px;
        height: 100px;
        border: 2px solid #ddd;
        border-radius: 8px;
        overflow: hidden;
        background: #f5f5f5;
    }
    .image-preview img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .image-preview .image-number {
        position: absolute;
        top: 5px;
        left: 5px;
        background: rgba(0,0,0,0.7);
        color: white;
        padding: 2px 8px;
        border-radius: 10px;
        font-size: 11px;
    }
    .btn-capture {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        padding: 12px 30px;
        border-radius: 6px;
        font-size: 16px;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    .btn-capture:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
    }
    .btn-capture:disabled {
        background: #ccc;
        cursor: not-allowed;
        transform: none;
    }
    .person-list-container {
        max-height: 550px;
        overflow-y: auto;
        padding-right: 5px;
    }
    .search-box {
        margin-bottom: 15px;
    }
    .selected-person-info {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 15px 20px;
        border-radius: 8px;
        margin-bottom: 20px;
    }
    .selected-person-info h4 {
        margin: 0 0 5px 0;
    }
    .selected-person-info small {
        opacity: 0.85;
    }
    .alert-floating {
        position: fixed;
        top: 70px;
        right: 20px;
        z-index: 9999;
        min-width: 300px;
        animation: slideInRight 0.3s ease;
    }
    @keyframes slideInRight {
        from { transform: translateX(400px); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    .stats-mini {
        display: flex;
        gap: 15px;
        margin-bottom: 15px;
    }
    .stats-mini .stat-box {
        flex: 1;
        text-align: center;
        padding: 10px;
        border-radius: 6px;
        background: #f9f9f9;
        border: 1px solid #e0e0e0;
    }
    .stats-mini .stat-box .stat-num {
        font-size: 24px;
        font-weight: 700;
        color: #333;
    }
    .stats-mini .stat-box .stat-label {
        font-size: 12px;
        color: #777;
    }
    .filter-bar {
        background: #f5f5f5;
        padding: 12px 15px;
        border-radius: 6px;
        margin-bottom: 15px;
    }
    /* Registered images section */
    .registered-images-section {
        background: #fff;
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 15px;
        margin-top: 15px;
    }
    .registered-images-section h5 {
        margin: 0 0 12px 0;
        color: #00a65a;
        font-weight: 700;
    }
    .registered-images-grid {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }
    .registered-img-box {
        position: relative;
        width: 90px;
        height: 90px;
        border: 2px solid #00a65a;
        border-radius: 8px;
        overflow: hidden;
        background: #e8f5e9;
    }
    .registered-img-box img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        cursor: pointer;
        transition: transform 0.2s;
    }
    .registered-img-box img:hover {
        transform: scale(1.05);
    }
    .registered-img-box .img-index {
        position: absolute;
        bottom: 2px;
        left: 2px;
        background: rgba(0,0,0,0.7);
        color: #fff;
        font-size: 10px;
        padding: 1px 6px;
        border-radius: 8px;
    }
    .btn-delete-registration {
        margin-top: 12px;
    }
    /* Image modal */
    .img-modal-overlay {
        display: none;
        position: fixed;
        top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(0,0,0,0.85);
        z-index: 99999;
        justify-content: center;
        align-items: center;
    }
    .img-modal-overlay.active {
        display: flex;
    }
    .img-modal-overlay img {
        max-width: 90vw;
        max-height: 85vh;
        border-radius: 8px;
        box-shadow: 0 0 30px rgba(0,0,0,0.5);
    }
    .img-modal-close {
        position: absolute;
        top: 20px; right: 30px;
        color: white;
        font-size: 36px;
        cursor: pointer;
        z-index: 100000;
    }
    .img-modal-close:hover { opacity: 0.7; }
</style>

<!-- Image Preview Modal -->
<div class="img-modal-overlay" id="imageModal" onclick="closeImageModal()">
    <span class="img-modal-close">&times;</span>
    <img id="modalImage" src="" onclick="event.stopPropagation()">
</div>

<div class="content-wrapper">
    <section class="content-header">
        <h1>
            <i class="fa fa-user-circle"></i> Face Attendance Registration
            <small>Register students & staff with facial recognition</small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="<?php echo base_url(); ?>"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Attendance</a></li>
            <li class="active">Face Attendance Registration</li>
        </ol>
    </section>

    <section class="content">
        <!-- Stats Overview -->
        <div class="row">
            <div class="col-md-12">
                <div class="stats-mini">
                    <div class="stat-box">
                        <div class="stat-num" id="totalStudents"><?php echo count($students); ?></div>
                        <div class="stat-label">Total Students</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-num text-green" id="registeredStudents"><?php echo count($registered_students); ?></div>
                        <div class="stat-label">Students Registered</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-num" id="totalStaff"><?php echo count($staffs); ?></div>
                        <div class="stat-label">Total Staff</div>
                    </div>
                    <div class="stat-box">
                        <div class="stat-num text-green" id="registeredStaff"><?php echo count($registered_staff); ?></div>
                        <div class="stat-label">Staff Registered</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab Navigation -->
        <div class="row">
            <div class="col-md-12">
                <div class="nav-tabs-custom">
                    <ul class="nav nav-tabs">
                        <li class="active">
                            <a href="#tab-students" data-toggle="tab" aria-expanded="true">
                                <i class="fa fa-graduation-cap"></i> Students
                            </a>
                        </li>
                        <li>
                            <a href="#tab-staff" data-toggle="tab" aria-expanded="false">
                                <i class="fa fa-user-md"></i> Staff
                            </a>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <!-- ==================== STUDENTS TAB ==================== -->
                        <div class="tab-pane active" id="tab-students">
                            <div class="row">
                                <!-- Left: Student List from Database -->
                                <div class="col-md-5">
                                    <div class="box box-solid">
                                        <div class="box-header with-border">
                                            <h3 class="box-title"><i class="fa fa-list"></i> Students (From Database)</h3>
                                        </div>
                                        <div class="box-body">
                                            <!-- Filter by Class/Section -->
                                            <div class="filter-bar">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <select class="form-control" id="studentClassFilter">
                                                            <option value="">-- Select Class --</option>
                                                            <?php foreach ($classlist as $cls): ?>
                                                                <option value="<?php echo $cls['id']; ?>"><?php echo htmlspecialchars($cls['class']); ?></option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <select class="form-control" id="studentSectionFilter" disabled>
                                                            <option value="">-- Select Section --</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div style="margin-top: 10px;">
                                                    <input type="text" class="form-control" id="studentSearch" placeholder="Search by name, admission no...">
                                                </div>
                                            </div>

                                            <!-- Student List -->
                                            <div class="person-list-container" id="studentListContainer">
                                                <p class="text-center text-muted" id="studentLoading"><i class="fa fa-spinner fa-spin"></i> Loading students...</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Right: Face Registration -->
                                <div class="col-md-7">
                                    <div class="box box-primary">
                                        <div class="box-header with-border">
                                            <h3 class="box-title"><i class="fa fa-camera"></i> Capture & Register Face</h3>
                                        </div>

                                        <!-- Selected Person Info -->
                                        <div class="box-body" id="studentRegArea" style="display: none;">
                                            <div class="selected-person-info" id="selectedStudentInfo">
                                                <h4 id="selectedName">Select a student</h4>
                                                <small id="selectedDetails">Click on a student from the left panel</small>
                                            </div>

                                            <!-- Existing Registered Images (shown when already registered) -->
                                            <div id="existingStudentImages" style="display: none;">
                                                <div class="registered-images-section">
                                                    <h5><i class="fa fa-image"></i> Registered Face Images</h5>
                                                    <div class="registered-images-grid" id="existingStudentImagesGrid"></div>
                                                    <button type="button" class="btn btn-danger btn-delete-registration" onclick="deleteRegistration('student')">
                                                        <i class="fa fa-trash"></i> Delete Face Registration
                                                    </button>
                                                </div>
                                                <hr>
                                                <p class="text-muted"><i class="fa fa-info-circle"></i> You can re-capture images below to update the registration.</p>
                                            </div>

                                            <!-- Hidden form data -->
                                            <form id="studentFaceForm">
                                                <input type="hidden" id="s_student_id" name="student_id">
                                                <input type="hidden" id="s_admission_no" name="admission_no">
                                                <input type="hidden" id="s_first_name" name="first_name">
                                                <input type="hidden" id="s_last_name" name="last_name">
                                                <input type="hidden" id="s_email" name="email">
                                                <input type="hidden" id="s_phone" name="phone">
                                                <input type="hidden" id="s_class_id" name="class_id">
                                                <input type="hidden" id="s_section_id" name="section_id">
                                                <input type="hidden" id="s_registration_number" name="registration_number">

                                                <!-- Registration Number -->
                                                <div class="form-group">
                                                    <label>Registration Number <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" id="s_reg_number_input" name="reg_number_display" required>
                                                    <small class="text-muted">Unique ID for face recognition system</small>
                                                </div>

                                                <!-- Face Capture -->
                                                <div class="capture-section">
                                                    <h4><i class="fa fa-camera"></i> Capture Face Images (Minimum 3 Required)</h4>
                                                    <p class="text-muted">Ensure good lighting and look directly at the camera</p>

                                                    <div class="video-container" id="videoContainer" style="display: none;">
                                                        <video id="video" autoplay playsinline></video>
                                                        <div class="video-overlay" id="captureStatus">Ready</div>
                                                    </div>

                                                    <div class="capture-controls" style="text-align: center; margin: 15px 0;">
                                                        <button type="button" class="btn btn-capture" id="startCaptureBtn" onclick="startCapture()">
                                                            <i class="fa fa-camera"></i> Start Face Capture
                                                        </button>
                                                        <button type="button" class="btn btn-danger" id="stopCaptureBtn" onclick="stopCapture()" style="display: none;">
                                                            <i class="fa fa-stop"></i> Stop
                                                        </button>
                                                    </div>

                                                    <div class="captured-images" id="capturedImages"></div>
                                                </div>

                                                <!-- Submit -->
                                                <div class="box-footer">
                                                    <button type="submit" class="btn btn-success btn-lg" id="submitBtn" disabled>
                                                        <i class="fa fa-check"></i> Register Face
                                                    </button>
                                                    <button type="button" class="btn btn-default btn-lg" onclick="resetStudentSelection()">
                                                        <i class="fa fa-refresh"></i> Reset
                                                    </button>
                                                </div>
                                            </form>
                                        </div>

                                        <!-- Placeholder when no student selected -->
                                        <div class="box-body" id="noStudentSelected">
                                            <div class="text-center" style="padding: 60px 20px;">
                                                <i class="fa fa-hand-pointer-o" style="font-size: 60px; color: #ccc;"></i>
                                                <h3 style="color: #999; margin-top: 15px;">Select a Student</h3>
                                                <p style="color: #bbb;">Click on a student from the list to register or view their face data</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ==================== STAFF TAB ==================== -->
                        <div class="tab-pane" id="tab-staff">
                            <div class="row">
                                <!-- Left: Staff List from Database -->
                                <div class="col-md-5">
                                    <div class="box box-solid">
                                        <div class="box-header with-border">
                                            <h3 class="box-title"><i class="fa fa-list"></i> Staff (From Database)</h3>
                                        </div>
                                        <div class="box-body">
                                            <!-- Filter by Role -->
                                            <div class="filter-bar">
                                                <div class="row">
                                                    <div class="col-md-8">
                                                        <select class="form-control" id="staffRoleFilter">
                                                            <option value="">-- Select Role --</option>
                                                            <?php foreach ($staff_roles as $role): ?>
                                                                <option value="<?php echo $role['id']; ?>"><?php echo htmlspecialchars($role['type']); ?></option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <input type="text" class="form-control" id="staffSearch" placeholder="Search...">
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Staff List -->
                                            <div class="person-list-container" id="staffListContainer">
                                                <p class="text-center text-muted" id="staffLoading"><i class="fa fa-spinner fa-spin"></i> Loading staff...</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Right: Face Registration -->
                                <div class="col-md-7">
                                    <div class="box box-primary">
                                        <div class="box-header with-border">
                                            <h3 class="box-title"><i class="fa fa-camera"></i> Capture & Register Face</h3>
                                        </div>

                                        <!-- Selected Person Info -->
                                        <div class="box-body" id="staffRegArea" style="display: none;">
                                            <div class="selected-person-info" id="selectedStaffInfo">
                                                <h4 id="selectedStaffName">Select a staff member</h4>
                                                <small id="selectedStaffDetails">Click on a staff member from the left panel</small>
                                            </div>

                                            <!-- Existing Registered Images (shown when already registered) -->
                                            <div id="existingStaffImages" style="display: none;">
                                                <div class="registered-images-section">
                                                    <h5><i class="fa fa-image"></i> Registered Face Images</h5>
                                                    <div class="registered-images-grid" id="existingStaffImagesGrid"></div>
                                                    <button type="button" class="btn btn-danger btn-delete-registration" onclick="deleteRegistration('staff')">
                                                        <i class="fa fa-trash"></i> Delete Face Registration
                                                    </button>
                                                </div>
                                                <hr>
                                                <p class="text-muted"><i class="fa fa-info-circle"></i> You can re-capture images below to update the registration.</p>
                                            </div>

                                            <!-- Hidden form data -->
                                            <form id="staffFaceForm">
                                                <input type="hidden" id="st_staff_id" name="staff_id">
                                                <input type="hidden" id="st_first_name" name="first_name">
                                                <input type="hidden" id="st_last_name" name="last_name">
                                                <input type="hidden" id="st_email" name="email">
                                                <input type="hidden" id="st_phone" name="phone">
                                                <input type="hidden" id="st_designation" name="designation">
                                                <input type="hidden" id="st_registration_number" name="registration_number">

                                                <!-- Registration Number -->
                                                <div class="form-group">
                                                    <label>Registration Number <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" id="st_reg_number_input" name="reg_number_display" required>
                                                    <small class="text-muted">Unique ID for face recognition system</small>
                                                </div>

                                                <!-- Face Capture -->
                                                <div class="capture-section">
                                                    <h4><i class="fa fa-camera"></i> Capture Face Images (Minimum 3 Required)</h4>
                                                    <p class="text-muted">Ensure good lighting and look directly at the camera</p>

                                                    <div class="video-container" id="staffVideoContainer" style="display: none;">
                                                        <video id="staffVideo" autoplay playsinline></video>
                                                        <div class="video-overlay" id="staffCaptureStatus">Ready</div>
                                                    </div>

                                                    <div class="capture-controls" style="text-align: center; margin: 15px 0;">
                                                        <button type="button" class="btn btn-capture" id="staffStartCaptureBtn" onclick="startStaffCapture()">
                                                            <i class="fa fa-camera"></i> Start Face Capture
                                                        </button>
                                                        <button type="button" class="btn btn-danger" id="staffStopCaptureBtn" onclick="stopStaffCapture()" style="display: none;">
                                                            <i class="fa fa-stop"></i> Stop
                                                        </button>
                                                    </div>

                                                    <div class="captured-images" id="staffCapturedImages"></div>
                                                </div>

                                                <!-- Submit -->
                                                <div class="box-footer">
                                                    <button type="submit" class="btn btn-success btn-lg" id="staffSubmitBtn" disabled>
                                                        <i class="fa fa-check"></i> Register Face
                                                    </button>
                                                    <button type="button" class="btn btn-default btn-lg" onclick="resetStaffSelection()">
                                                        <i class="fa fa-refresh"></i> Reset
                                                    </button>
                                                </div>
                                            </form>
                                        </div>

                                        <!-- Placeholder when no staff selected -->
                                        <div class="box-body" id="noStaffSelected">
                                            <div class="text-center" style="padding: 60px 20px;">
                                                <i class="fa fa-hand-pointer-o" style="font-size: 60px; color: #ccc;"></i>
                                                <h3 style="color: #999; margin-top: 15px;">Select a Staff Member</h3>
                                                <p style="color: #bbb;">Click on a staff member from the list to register or view their face data</p>
                                            </div>
                                        </div>
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

<script>
const baseUrl = '<?php echo base_url(); ?>';

// ===========================
// STUDENT SECTION
// ===========================
let capturedImages = [];
let currentCaptureIndex = 0;
let videoStream = null;
let captureInterval = null;
let selectedStudent = null;
let allStudents = [];

$(document).ready(function() {
    loadStudents();
    loadStaffs();
});

// ---- Student Filters ----
$('#studentClassFilter').on('change', function() {
    const classId = $(this).val();
    if (classId) {
        loadSections(classId);
    } else {
        $('#studentSectionFilter').html('<option value="">-- Select Section --</option>').prop('disabled', true);
        loadStudents();
    }
});

$('#studentSectionFilter').on('change', function() {
    loadStudents();
});

$('#studentSearch').on('input', function() {
    filterStudentList();
});

function loadSections(classId) {
    $('#studentSectionFilter').html('<option value="">Loading...</option>').prop('disabled', true);
    $.post(baseUrl + 'admin/face_attendance_register/get_sections_by_class', {
        class_id: classId
    }, function(response) {
        if (response.status === 'success') {
            let html = '<option value="">-- All Sections --</option>';
            response.sections.forEach(function(sec) {
                html += `<option value="${sec.section_id}">${sec.section}</option>`;
            });
            $('#studentSectionFilter').html(html).prop('disabled', false);
        }
        loadStudents();
    }, 'json');
}

function loadStudents() {
    const classId = $('#studentClassFilter').val();
    const sectionId = $('#studentSectionFilter').val();

    $('#studentListContainer').html('<p class="text-center text-muted"><i class="fa fa-spinner fa-spin"></i> Loading students...</p>');

    $.post(baseUrl + 'admin/face_attendance_register/get_students_by_class', {
        class_id: classId,
        section_id: sectionId
    }, function(response) {
        if (response.status === 'success') {
            allStudents = response.students;
            renderStudentList(allStudents);
        }
    }, 'json');
}

function filterStudentList() {
    const search = $('#studentSearch').val().toLowerCase();
    const filtered = allStudents.filter(s => {
        const name = (s.firstname + ' ' + (s.middlename || '') + ' ' + s.lastname).toLowerCase();
        return name.includes(search) || (s.admission_no || '').toLowerCase().includes(search);
    });
    renderStudentList(filtered);
}

function renderStudentList(students) {
    let html = '';
    if (students.length === 0) {
        html = '<p class="text-center text-muted" style="padding: 30px;">No students found</p>';
    } else {
        students.forEach(function(student) {
            const fullName = student.firstname + ' ' + (student.middlename ? student.middlename + ' ' : '') + student.lastname;
            const avatarSrc = student.image ? baseUrl + 'uploads/student_images/' + student.image : baseUrl + 'uploads/student_images/no_image.png';
            const isSelected = selectedStudent && selectedStudent.id == student.id;
            let regBadge = '';
            if (student.face_registered) {
                // Show thumbnail of first registered image
                let thumbHtml = '';
                if (student.face_image_urls && student.face_image_urls.length > 0) {
                    thumbHtml = `<img src="${student.face_image_urls[0]}" style="width:30px;height:30px;border-radius:50%;object-fit:cover;border:2px solid #00a65a;margin-right:5px;vertical-align:middle;" onerror="this.style.display='none'">`;
                }
                regBadge = thumbHtml + '<span class="badge-registered"><i class="fa fa-check"></i> Registered (' + student.face_image_count + ' imgs)</span>';
            } else {
                regBadge = '<span class="badge-unregistered"><i class="fa fa-clock-o"></i> Not Registered</span>';
            }

            html += `
                <div class="face-register-card ${isSelected ? 'selected' : ''} ${student.face_registered ? 'registered' : ''}"
                     onclick="selectStudent(${student.id}, this)" data-id="${student.id}">
                    <div class="media">
                        <div class="media-left">
                            <img class="person-avatar" src="${avatarSrc}" alt="${fullName}" onerror="this.src='${baseUrl}uploads/student_images/no_image.png'">
                        </div>
                        <div class="media-body">
                            <strong style="font-size: 14px;">${fullName}</strong><br>
                            <small class="text-muted">
                                ${student.admission_no ? 'Adm: ' + student.admission_no + ' | ' : ''}
                                ${student.class || ''} - ${student.section || ''}
                            </small><br>
                            ${regBadge}
                        </div>
                    </div>
                </div>
            `;
        });
    }
    $('#studentListContainer').html(html);
}

function selectStudent(studentId, element) {
    const student = allStudents.find(s => s.id == studentId);
    if (!student) return;

    selectedStudent = student;

    $('.face-register-card').removeClass('selected');
    $(element).addClass('selected');

    const fullName = student.firstname + ' ' + (student.middlename ? student.middlename + ' ' : '') + student.lastname;

    // Fill hidden form
    $('#s_student_id').val(student.id);
    $('#s_admission_no').val(student.admission_no);
    $('#s_first_name').val(student.firstname);
    $('#s_last_name').val(student.lastname);
    $('#s_email').val(student.email);
    $('#s_phone').val(student.mobileno);
    $('#s_class_id').val(student.class_id);
    $('#s_section_id').val(student.section_id);

    // Auto-generate registration number
    let regNumber = 'STU_' + (student.admission_no || student.id);
    if (student.face_registered && student.registration_number) {
        regNumber = student.registration_number;
    }
    $('#s_reg_number_input').val(regNumber);
    $('#s_registration_number').val(regNumber);

    // Show registration area
    $('#selectedName').text(fullName);
    $('#selectedDetails').text('Adm: ' + (student.admission_no || 'N/A') + ' | Class: ' + (student.class || 'N/A') + ' - ' + (student.section || 'N/A'));
    $('#noStudentSelected').hide();
    $('#studentRegArea').show();

    // Show existing registered images if available
    if (student.face_registered && student.face_image_urls && student.face_image_urls.length > 0) {
        let imgHtml = '';
        student.face_image_urls.forEach(function(url, idx) {
            imgHtml += `
                <div class="registered-img-box">
                    <img src="${url}" alt="Face ${idx + 1}" onclick="openImageModal('${url}')" onerror="this.parentElement.style.display='none'">
                    <span class="img-index">${idx + 1}</span>
                </div>
            `;
        });
        $('#existingStudentImagesGrid').html(imgHtml);
        $('#existingStudentImages').show();
    } else {
        $('#existingStudentImagesGrid').html('');
        $('#existingStudentImages').hide();
    }

    // Reset capture
    capturedImages = [];
    currentCaptureIndex = 0;
    $('#capturedImages').html('');
    $('#submitBtn').prop('disabled', true);
    stopCapture();
}

function resetStudentSelection() {
    selectedStudent = null;
    $('.face-register-card').removeClass('selected');
    $('#noStudentSelected').show();
    $('#studentRegArea').hide();
    $('#existingStudentImages').hide();
    $('#existingStudentImagesGrid').html('');
    capturedImages = [];
    currentCaptureIndex = 0;
    $('#capturedImages').html('');
    $('#submitBtn').prop('disabled', true);
    stopCapture();
}

// ---- Student Face Capture ----
async function startCapture() {
    try {
        const video = document.getElementById('video');
        const videoContainer = document.getElementById('videoContainer');

        videoStream = await navigator.mediaDevices.getUserMedia({
            video: { width: 640, height: 480, facingMode: 'user' }
        });
        video.srcObject = videoStream;

        await new Promise(resolve => {
            video.onloadedmetadata = () => {
                video.play();
                resolve();
            };
        });

        videoContainer.style.display = 'block';
        document.getElementById('startCaptureBtn').style.display = 'none';
        document.getElementById('stopCaptureBtn').style.display = 'inline-block';

        capturedImages = [];
        currentCaptureIndex = 0;
        document.getElementById('capturedImages').innerHTML = '';
        document.getElementById('captureStatus').textContent = 'Capturing in 2 seconds...';

        setTimeout(() => {
            captureInterval = setInterval(captureImage, 1000);
        }, 2000);

    } catch (error) {
        alert('Unable to access camera. Please ensure camera permissions are granted.\n\nError: ' + error.message);
    }
}

function captureImage() {
    if (currentCaptureIndex >= 5) {
        stopCapture();
        return;
    }

    const video = document.getElementById('video');
    const canvas = document.createElement('canvas');
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    const context = canvas.getContext('2d');
    context.drawImage(video, 0, 0);

    const imageData = canvas.toDataURL('image/png');
    capturedImages.push(imageData);
    displayCapturedImage(imageData, currentCaptureIndex + 1, 'student');
    currentCaptureIndex++;
    document.getElementById('captureStatus').textContent = `Captured ${currentCaptureIndex}/5`;

    if (currentCaptureIndex >= 3) {
        document.getElementById('submitBtn').disabled = false;
    }
}

function stopCapture() {
    const videoContainer = document.getElementById('videoContainer');

    if (captureInterval) {
        clearInterval(captureInterval);
        captureInterval = null;
    }
    if (videoStream) {
        videoStream.getTracks().forEach(track => track.stop());
        videoStream = null;
    }

    videoContainer.style.display = 'none';
    document.getElementById('startCaptureBtn').style.display = 'inline-block';
    document.getElementById('stopCaptureBtn').style.display = 'none';
}

// ---- Student Form Submit ----
$('#studentFaceForm').on('submit', function(e) {
    e.preventDefault();

    if (!selectedStudent) {
        showAlert('error', 'Please select a student first');
        return;
    }
    if (capturedImages.length < 3) {
        showAlert('error', 'Please capture at least 3 face images');
        return;
    }

    const regNumber = $('#s_reg_number_input').val();
    if (!regNumber) {
        showAlert('error', 'Registration number is required');
        return;
    }

    const formData = {
        student_id: $('#s_student_id').val(),
        registration_number: regNumber,
        admission_no: $('#s_admission_no').val(),
        first_name: $('#s_first_name').val(),
        last_name: $('#s_last_name').val(),
        email: $('#s_email').val(),
        phone: $('#s_phone').val(),
        class_id: $('#s_class_id').val(),
        section_id: $('#s_section_id').val()
    };

    capturedImages.forEach((img, index) => {
        formData[`captured_image_${index + 1}`] = img;
    });

    $.ajax({
        url: baseUrl + 'admin/face_attendance_register/register_student_face',
        type: 'POST',
        data: formData,
        dataType: 'json',
        beforeSend: function() {
            $('#submitBtn').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Registering...');
        },
        success: function(response) {
            if (response.status === 'success') {
                showAlert('success', response.message);
                resetStudentSelection();
                loadStudents();
                const count = parseInt($('#registeredStudents').text()) + 1;
                $('#registeredStudents').text(count);
            } else {
                showAlert('error', response.message);
                $('#submitBtn').prop('disabled', false).html('<i class="fa fa-check"></i> Register Face');
            }
        },
        error: function() {
            showAlert('error', 'An error occurred. Please try again.');
            $('#submitBtn').prop('disabled', false).html('<i class="fa fa-check"></i> Register Face');
        }
    });
});


// ===========================
// STAFF SECTION
// ===========================
let staffCapturedImages = [];
let staffCaptureIndex = 0;
let staffVideoStream = null;
let staffCaptureInterval = null;
let selectedStaff = null;
let allStaffs = [];

$('#staffRoleFilter').on('change', function() {
    loadStaffs();
});

$('#staffSearch').on('input', function() {
    filterStaffList();
});

function loadStaffs() {
    const roleId = $('#staffRoleFilter').val();
    $('#staffListContainer').html('<p class="text-center text-muted"><i class="fa fa-spinner fa-spin"></i> Loading staff...</p>');

    $.post(baseUrl + 'admin/face_attendance_register/get_staff_by_role', {
        role_id: roleId
    }, function(response) {
        if (response.status === 'success') {
            allStaffs = response.staffs;
            renderStaffList(allStaffs);
        }
    }, 'json');
}

function filterStaffList() {
    const search = $('#staffSearch').val().toLowerCase();
    const filtered = allStaffs.filter(s => {
        const name = (s.name + ' ' + (s.surname || '')).toLowerCase();
        return name.includes(search) || (s.employee_id || '').toLowerCase().includes(search);
    });
    renderStaffList(filtered);
}

function renderStaffList(staffs) {
    let html = '';
    if (staffs.length === 0) {
        html = '<p class="text-center text-muted" style="padding: 30px;">No staff found</p>';
    } else {
        staffs.forEach(function(staff) {
            const fullName = staff.name + ' ' + (staff.surname || '');
            const avatarSrc = staff.image ? baseUrl + 'uploads/staff_images/' + staff.image : baseUrl + 'uploads/staff_images/no_image.png';
            const isSelected = selectedStaff && selectedStaff.id == staff.id;
            let regBadge = '';
            if (staff.face_registered) {
                let thumbHtml = '';
                if (staff.face_image_urls && staff.face_image_urls.length > 0) {
                    thumbHtml = `<img src="${staff.face_image_urls[0]}" style="width:30px;height:30px;border-radius:50%;object-fit:cover;border:2px solid #00a65a;margin-right:5px;vertical-align:middle;" onerror="this.style.display='none'">`;
                }
                regBadge = thumbHtml + '<span class="badge-registered"><i class="fa fa-check"></i> Registered (' + staff.face_image_count + ' imgs)</span>';
            } else {
                regBadge = '<span class="badge-unregistered"><i class="fa fa-clock-o"></i> Not Registered</span>';
            }

            html += `
                <div class="face-register-card ${isSelected ? 'selected' : ''} ${staff.face_registered ? 'registered' : ''}"
                     onclick="selectStaff(${staff.id}, this)" data-id="${staff.id}">
                    <div class="media">
                        <div class="media-left">
                            <img class="person-avatar" src="${avatarSrc}" alt="${fullName}" onerror="this.src='${baseUrl}uploads/staff_images/no_image.png'">
                        </div>
                        <div class="media-body">
                            <strong style="font-size: 14px;">${fullName}</strong><br>
                            <small class="text-muted">
                                ${staff.employee_id ? 'Emp: ' + staff.employee_id + ' | ' : ''}
                                ${staff.user_type || ''} ${staff.designation ? '- ' + staff.designation : ''}
                            </small><br>
                            ${regBadge}
                        </div>
                    </div>
                </div>
            `;
        });
    }
    $('#staffListContainer').html(html);
}

function selectStaff(staffId, element) {
    const staff = allStaffs.find(s => s.id == staffId);
    if (!staff) return;

    selectedStaff = staff;
    $('.face-register-card').removeClass('selected');
    $(element).addClass('selected');

    const fullName = staff.name + ' ' + (staff.surname || '');

    $('#st_staff_id').val(staff.id);
    $('#st_first_name').val(staff.name);
    $('#st_last_name').val(staff.surname || '');
    $('#st_email').val(staff.email);
    $('#st_phone').val(staff.contact_no);
    $('#st_designation').val(staff.designation || staff.user_type || '');

    let regNumber = 'STAFF_' + (staff.employee_id || staff.id);
    if (staff.face_registered && staff.registration_number) {
        regNumber = staff.registration_number;
    }
    $('#st_reg_number_input').val(regNumber);
    $('#st_registration_number').val(regNumber);

    $('#selectedStaffName').text(fullName);
    $('#selectedStaffDetails').text('Emp: ' + (staff.employee_id || 'N/A') + ' | Role: ' + (staff.user_type || 'N/A') + (staff.designation ? ' - ' + staff.designation : ''));
    $('#noStaffSelected').hide();
    $('#staffRegArea').show();

    // Show existing registered images if available
    if (staff.face_registered && staff.face_image_urls && staff.face_image_urls.length > 0) {
        let imgHtml = '';
        staff.face_image_urls.forEach(function(url, idx) {
            imgHtml += `
                <div class="registered-img-box">
                    <img src="${url}" alt="Face ${idx + 1}" onclick="openImageModal('${url}')" onerror="this.parentElement.style.display='none'">
                    <span class="img-index">${idx + 1}</span>
                </div>
            `;
        });
        $('#existingStaffImagesGrid').html(imgHtml);
        $('#existingStaffImages').show();
    } else {
        $('#existingStaffImagesGrid').html('');
        $('#existingStaffImages').hide();
    }

    staffCapturedImages = [];
    staffCaptureIndex = 0;
    $('#staffCapturedImages').html('');
    $('#staffSubmitBtn').prop('disabled', true);
    stopStaffCapture();
}

function resetStaffSelection() {
    selectedStaff = null;
    $('.face-register-card').removeClass('selected');
    $('#noStaffSelected').show();
    $('#staffRegArea').hide();
    $('#existingStaffImages').hide();
    $('#existingStaffImagesGrid').html('');
    staffCapturedImages = [];
    staffCaptureIndex = 0;
    $('#staffCapturedImages').html('');
    $('#staffSubmitBtn').prop('disabled', true);
    stopStaffCapture();
}

// ---- Staff Face Capture ----
async function startStaffCapture() {
    try {
        const video = document.getElementById('staffVideo');
        const videoContainer = document.getElementById('staffVideoContainer');

        staffVideoStream = await navigator.mediaDevices.getUserMedia({
            video: { width: 640, height: 480, facingMode: 'user' }
        });
        video.srcObject = staffVideoStream;

        await new Promise(resolve => {
            video.onloadedmetadata = () => {
                video.play();
                resolve();
            };
        });

        videoContainer.style.display = 'block';
        document.getElementById('staffStartCaptureBtn').style.display = 'none';
        document.getElementById('staffStopCaptureBtn').style.display = 'inline-block';

        staffCapturedImages = [];
        staffCaptureIndex = 0;
        document.getElementById('staffCapturedImages').innerHTML = '';
        document.getElementById('staffCaptureStatus').textContent = 'Capturing in 2 seconds...';

        setTimeout(() => {
            staffCaptureInterval = setInterval(captureStaffImage, 1000);
        }, 2000);

    } catch (error) {
        alert('Unable to access camera. Please ensure camera permissions are granted.\n\nError: ' + error.message);
    }
}

function captureStaffImage() {
    if (staffCaptureIndex >= 5) {
        stopStaffCapture();
        return;
    }

    const video = document.getElementById('staffVideo');
    const canvas = document.createElement('canvas');
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    const context = canvas.getContext('2d');
    context.drawImage(video, 0, 0);

    const imageData = canvas.toDataURL('image/png');
    staffCapturedImages.push(imageData);
    displayCapturedImage(imageData, staffCaptureIndex + 1, 'staff');
    staffCaptureIndex++;
    document.getElementById('staffCaptureStatus').textContent = `Captured ${staffCaptureIndex}/5`;

    if (staffCaptureIndex >= 3) {
        document.getElementById('staffSubmitBtn').disabled = false;
    }
}

function stopStaffCapture() {
    const videoContainer = document.getElementById('staffVideoContainer');

    if (staffCaptureInterval) {
        clearInterval(staffCaptureInterval);
        staffCaptureInterval = null;
    }
    if (staffVideoStream) {
        staffVideoStream.getTracks().forEach(track => track.stop());
        staffVideoStream = null;
    }

    videoContainer.style.display = 'none';
    document.getElementById('staffStartCaptureBtn').style.display = 'inline-block';
    document.getElementById('staffStopCaptureBtn').style.display = 'none';
}

// ---- Staff Form Submit ----
$('#staffFaceForm').on('submit', function(e) {
    e.preventDefault();

    if (!selectedStaff) {
        showAlert('error', 'Please select a staff member first');
        return;
    }
    if (staffCapturedImages.length < 3) {
        showAlert('error', 'Please capture at least 3 face images');
        return;
    }

    const regNumber = $('#st_reg_number_input').val();
    if (!regNumber) {
        showAlert('error', 'Registration number is required');
        return;
    }

    const formData = {
        staff_id: $('#st_staff_id').val(),
        registration_number: regNumber,
        first_name: $('#st_first_name').val(),
        last_name: $('#st_last_name').val(),
        email: $('#st_email').val(),
        phone: $('#st_phone').val(),
        designation: $('#st_designation').val()
    };

    staffCapturedImages.forEach((img, index) => {
        formData[`captured_image_${index + 1}`] = img;
    });

    $.ajax({
        url: baseUrl + 'admin/face_attendance_register/register_staff_face',
        type: 'POST',
        data: formData,
        dataType: 'json',
        beforeSend: function() {
            $('#staffSubmitBtn').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Registering...');
        },
        success: function(response) {
            if (response.status === 'success') {
                showAlert('success', response.message);
                resetStaffSelection();
                loadStaffs();
                const count = parseInt($('#registeredStaff').text()) + 1;
                $('#registeredStaff').text(count);
            } else {
                showAlert('error', response.message);
                $('#staffSubmitBtn').prop('disabled', false).html('<i class="fa fa-check"></i> Register Face');
            }
        },
        error: function() {
            showAlert('error', 'An error occurred. Please try again.');
            $('#staffSubmitBtn').prop('disabled', false).html('<i class="fa fa-check"></i> Register Face');
        }
    });
});


// ===========================
// SHARED FUNCTIONS
// ===========================
function displayCapturedImage(imageData, index, type) {
    const containerId = type === 'staff' ? 'staffCapturedImages' : 'capturedImages';
    const container = document.getElementById(containerId);

    const imageBox = document.createElement('div');
    imageBox.className = 'image-preview';
    imageBox.id = `preview-${type}-${index}`;

    const img = document.createElement('img');
    img.src = imageData;

    const numberLabel = document.createElement('div');
    numberLabel.className = 'image-number';
    numberLabel.textContent = `Image ${index}`;

    imageBox.appendChild(img);
    imageBox.appendChild(numberLabel);
    container.appendChild(imageBox);
}

function openImageModal(url) {
    document.getElementById('modalImage').src = url;
    document.getElementById('imageModal').classList.add('active');
}

function closeImageModal() {
    document.getElementById('imageModal').classList.remove('active');
    document.getElementById('modalImage').src = '';
}

// Close modal on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeImageModal();
});

function deleteRegistration(type) {
    let regId, personName;

    if (type === 'student') {
        if (!selectedStudent || !selectedStudent.face_registered) return;
        regId = selectedStudent.face_reg_id;
        personName = selectedStudent.firstname + ' ' + (selectedStudent.lastname || '');
    } else {
        if (!selectedStaff || !selectedStaff.face_registered) return;
        regId = selectedStaff.face_reg_id;
        personName = (selectedStaff.name || '') + ' ' + (selectedStaff.surname || '');
    }

    if (!confirm('Are you sure you want to DELETE the face registration for "' + personName + '"?\n\nThis will remove all registered face images.')) {
        return;
    }

    $.ajax({
        url: baseUrl + 'admin/face_attendance_register/delete_registration',
        type: 'POST',
        data: { reg_id: regId },
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                showAlert('success', 'Face registration deleted successfully for ' + personName);
                if (type === 'student') {
                    const count = parseInt($('#registeredStudents').text()) - 1;
                    $('#registeredStudents').text(Math.max(0, count));
                    resetStudentSelection();
                    loadStudents();
                } else {
                    const count = parseInt($('#registeredStaff').text()) - 1;
                    $('#registeredStaff').text(Math.max(0, count));
                    resetStaffSelection();
                    loadStaffs();
                }
            } else {
                showAlert('error', response.message || 'Error deleting registration');
            }
        },
        error: function() {
            showAlert('error', 'Error deleting registration. Please try again.');
        }
    });
}

function showAlert(type, message) {
    const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    const iconClass = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';

    const alertHtml = `
        <div class="alert ${alertClass} alert-floating alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <h4><i class="icon fa ${iconClass}"></i> ${type === 'success' ? 'Success' : 'Error'}!</h4>
            ${message}
        </div>
    `;

    $('body').append(alertHtml);

    setTimeout(function() {
        $('.alert-floating').fadeOut('slow', function() {
            $(this).remove();
        });
    }, 5000);
}

// Cleanup on page unload
window.addEventListener('beforeunload', function() {
    stopCapture();
    stopStaffCapture();
});
</script>
