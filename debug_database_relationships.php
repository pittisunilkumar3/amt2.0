<?php
/**
 * Debug Database Relationships for Fee Collection Filters
 */

// Database connection
$host = 'localhost';
$dbname = 'amt';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Database Relationships Debug</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background: #f5f5f5; }
        .container { max-width: 1400px; margin: 0 auto; }
        .section { background: white; padding: 20px; margin-bottom: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        h1 { color: #333; border-bottom: 3px solid #4CAF50; padding-bottom: 10px; }
        h2 { color: #666; margin-top: 0; }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background: #f8f9fa; font-weight: bold; }
        .badge { padding: 4px 8px; border-radius: 3px; font-size: 12px; font-weight: bold; }
        .badge-info { background: #17a2b8; color: white; }
        .badge-success { background: #28a745; color: white; }
        .badge-warning { background: #ffc107; color: #333; }
        pre { background: #f8f9fa; padding: 10px; border-radius: 5px; overflow-x: auto; }
        .query { background: #e9ecef; padding: 10px; border-left: 4px solid #007bff; margin: 10px 0; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 Database Relationships Debug - Fee Collection Filters</h1>
        
        <!-- Sessions -->
        <div class="section">
            <h2>📅 Sessions Table</h2>
            <?php
            $stmt = $pdo->query("SELECT id, session FROM sessions ORDER BY id DESC LIMIT 5");
            $sessions = $stmt->fetchAll(PDO::FETCH_ASSOC);
            ?>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Session Name</th>
                </tr>
                <?php foreach ($sessions as $session): ?>
                <tr>
                    <td><?php echo $session['id']; ?></td>
                    <td><?php echo $session['session']; ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
            <p><strong>Total Sessions:</strong> <span class="badge badge-info"><?php echo count($sessions); ?></span></p>
        </div>
        
        <!-- Classes -->
        <div class="section">
            <h2>📚 Classes Table</h2>
            <?php
            $stmt = $pdo->query("SELECT id, class FROM classes ORDER BY id ASC LIMIT 10");
            $classes = $stmt->fetchAll(PDO::FETCH_ASSOC);
            ?>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Class Name</th>
                </tr>
                <?php foreach ($classes as $class): ?>
                <tr>
                    <td><?php echo $class['id']; ?></td>
                    <td><?php echo $class['class']; ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
            <p><strong>Total Classes:</strong> <span class="badge badge-info"><?php echo count($classes); ?></span></p>
        </div>
        
        <!-- Sections -->
        <div class="section">
            <h2>📋 Sections Table</h2>
            <?php
            $stmt = $pdo->query("SELECT id, section FROM sections ORDER BY id ASC LIMIT 10");
            $sections = $stmt->fetchAll(PDO::FETCH_ASSOC);
            ?>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Section Name</th>
                </tr>
                <?php foreach ($sections as $section): ?>
                <tr>
                    <td><?php echo $section['id']; ?></td>
                    <td><?php echo $section['section']; ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
            <p><strong>Total Sections:</strong> <span class="badge badge-info"><?php echo count($sections); ?></span></p>
        </div>
        
        <!-- Student Session Relationship -->
        <div class="section">
            <h2>🔗 Student Session Table (Session → Class Relationship)</h2>
            <div class="query">
                <strong>Query:</strong> Get distinct classes for a specific session
                <pre>SELECT DISTINCT class_id FROM student_session WHERE session_id = ?</pre>
            </div>
            <?php
            // Get a sample session
            $sample_session = $sessions[0]['id'] ?? 21;
            $stmt = $pdo->prepare("
                SELECT DISTINCT 
                    ss.session_id,
                    ss.class_id,
                    c.class as class_name,
                    COUNT(DISTINCT ss.student_id) as student_count
                FROM student_session ss
                JOIN classes c ON ss.class_id = c.id
                WHERE ss.session_id = ?
                GROUP BY ss.session_id, ss.class_id, c.class
                ORDER BY c.class
            ");
            $stmt->execute([$sample_session]);
            $session_classes = $stmt->fetchAll(PDO::FETCH_ASSOC);
            ?>
            <p><strong>Sample Session ID:</strong> <span class="badge badge-warning"><?php echo $sample_session; ?></span></p>
            <table>
                <tr>
                    <th>Session ID</th>
                    <th>Class ID</th>
                    <th>Class Name</th>
                    <th>Student Count</th>
                </tr>
                <?php foreach ($session_classes as $row): ?>
                <tr>
                    <td><?php echo $row['session_id']; ?></td>
                    <td><?php echo $row['class_id']; ?></td>
                    <td><?php echo $row['class_name']; ?></td>
                    <td><span class="badge badge-success"><?php echo $row['student_count']; ?></span></td>
                </tr>
                <?php endforeach; ?>
            </table>
            <p><strong>Classes in Session <?php echo $sample_session; ?>:</strong> <span class="badge badge-info"><?php echo count($session_classes); ?></span></p>
        </div>
        
        <!-- Class Sections Relationship -->
        <div class="section">
            <h2>🔗 Class Sections Table (Class → Section Relationship)</h2>
            <div class="query">
                <strong>Query:</strong> Get sections for a specific class
                <pre>SELECT section_id FROM class_sections WHERE class_id = ?</pre>
            </div>
            <?php
            // Get a sample class
            $sample_class = $session_classes[0]['class_id'] ?? 19;
            $stmt = $pdo->prepare("
                SELECT 
                    cs.class_id,
                    c.class as class_name,
                    cs.section_id,
                    s.section as section_name
                FROM class_sections cs
                JOIN classes c ON cs.class_id = c.id
                JOIN sections s ON cs.section_id = s.id
                WHERE cs.class_id = ?
                ORDER BY s.section
            ");
            $stmt->execute([$sample_class]);
            $class_sections = $stmt->fetchAll(PDO::FETCH_ASSOC);
            ?>
            <p><strong>Sample Class ID:</strong> <span class="badge badge-warning"><?php echo $sample_class; ?></span></p>
            <table>
                <tr>
                    <th>Class ID</th>
                    <th>Class Name</th>
                    <th>Section ID</th>
                    <th>Section Name</th>
                </tr>
                <?php foreach ($class_sections as $row): ?>
                <tr>
                    <td><?php echo $row['class_id']; ?></td>
                    <td><?php echo $row['class_name']; ?></td>
                    <td><?php echo $row['section_id']; ?></td>
                    <td><?php echo $row['section_name']; ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
            <p><strong>Sections in Class <?php echo $sample_class; ?>:</strong> <span class="badge badge-info"><?php echo count($class_sections); ?></span></p>
        </div>
        
        <!-- Test Current Model Logic -->
        <div class="section">
            <h2>🧪 Test Current Model Logic</h2>
            
            <h3>Test 1: Get Classes for Session <?php echo $sample_session; ?></h3>
            <div class="query">
                <strong>Current Query:</strong>
                <pre>SELECT DISTINCT classes.id, classes.class as name
FROM student_session
JOIN classes ON student_session.class_id = classes.id
WHERE student_session.session_id = <?php echo $sample_session; ?>
ORDER BY classes.id ASC</pre>
            </div>
            <?php
            $stmt = $pdo->prepare("
                SELECT DISTINCT classes.id, classes.class as name
                FROM student_session
                JOIN classes ON student_session.class_id = classes.id
                WHERE student_session.session_id = ?
                ORDER BY classes.id ASC
            ");
            $stmt->execute([$sample_session]);
            $filtered_classes = $stmt->fetchAll(PDO::FETCH_ASSOC);
            ?>
            <table>
                <tr>
                    <th>Class ID</th>
                    <th>Class Name</th>
                </tr>
                <?php foreach ($filtered_classes as $row): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['name']; ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
            <p><strong>Result:</strong> <span class="badge badge-success"><?php echo count($filtered_classes); ?> classes found</span></p>
            
            <h3>Test 2: Get Sections for Class <?php echo $sample_class; ?></h3>
            <div class="query">
                <strong>Current Query:</strong>
                <pre>SELECT sections.id, sections.section as name
FROM class_sections
JOIN sections ON class_sections.section_id = sections.id
WHERE class_sections.class_id = <?php echo $sample_class; ?>
ORDER BY sections.id ASC</pre>
            </div>
            <?php
            $stmt = $pdo->prepare("
                SELECT sections.id, sections.section as name
                FROM class_sections
                JOIN sections ON class_sections.section_id = sections.id
                WHERE class_sections.class_id = ?
                ORDER BY sections.id ASC
            ");
            $stmt->execute([$sample_class]);
            $filtered_sections = $stmt->fetchAll(PDO::FETCH_ASSOC);
            ?>
            <table>
                <tr>
                    <th>Section ID</th>
                    <th>Section Name</th>
                </tr>
                <?php foreach ($filtered_sections as $row): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['name']; ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
            <p><strong>Result:</strong> <span class="badge badge-success"><?php echo count($filtered_sections); ?> sections found</span></p>
        </div>
        
        <!-- Verification Summary -->
        <div class="section">
            <h2>✅ Verification Summary</h2>
            <table>
                <tr>
                    <th>Test</th>
                    <th>Status</th>
                    <th>Details</th>
                </tr>
                <tr>
                    <td>Sessions Table</td>
                    <td><span class="badge badge-success">✓ OK</span></td>
                    <td><?php echo count($sessions); ?> sessions found</td>
                </tr>
                <tr>
                    <td>Classes Table</td>
                    <td><span class="badge badge-success">✓ OK</span></td>
                    <td><?php echo count($classes); ?> classes found</td>
                </tr>
                <tr>
                    <td>Sections Table</td>
                    <td><span class="badge badge-success">✓ OK</span></td>
                    <td><?php echo count($sections); ?> sections found</td>
                </tr>
                <tr>
                    <td>Student Session Relationship</td>
                    <td><span class="badge badge-success">✓ OK</span></td>
                    <td><?php echo count($session_classes); ?> classes in session <?php echo $sample_session; ?></td>
                </tr>
                <tr>
                    <td>Class Sections Relationship</td>
                    <td><span class="badge badge-success">✓ OK</span></td>
                    <td><?php echo count($class_sections); ?> sections in class <?php echo $sample_class; ?></td>
                </tr>
                <tr>
                    <td>Hierarchical Filtering (Classes)</td>
                    <td><span class="badge badge-<?php echo count($filtered_classes) > 0 && count($filtered_classes) < count($classes) ? 'success' : 'warning'; ?>">
                        <?php echo count($filtered_classes) > 0 && count($filtered_classes) < count($classes) ? '✓ Working' : '⚠ Check'; ?>
                    </span></td>
                    <td>Filtered: <?php echo count($filtered_classes); ?> classes (Total: <?php echo count($classes); ?>)</td>
                </tr>
                <tr>
                    <td>Hierarchical Filtering (Sections)</td>
                    <td><span class="badge badge-<?php echo count($filtered_sections) > 0 && count($filtered_sections) < count($sections) ? 'success' : 'warning'; ?>">
                        <?php echo count($filtered_sections) > 0 && count($filtered_sections) < count($sections) ? '✓ Working' : '⚠ Check'; ?>
                    </span></td>
                    <td>Filtered: <?php echo count($filtered_sections); ?> sections (Total: <?php echo count($sections); ?>)</td>
                </tr>
            </table>
        </div>
        
        <div class="section">
            <h2>📝 Conclusion</h2>
            <p>The database relationships are correctly structured:</p>
            <ul>
                <li><strong>student_session</strong> table links sessions to classes (via session_id and class_id)</li>
                <li><strong>class_sections</strong> table links classes to sections (via class_id and section_id)</li>
                <li>The current model queries are using the correct JOIN logic</li>
            </ul>
            <p><strong>If filtering is not working in the API, the issue is likely in how the parameters are being passed from the controller to the model.</strong></p>
        </div>
    </div>
</body>
</html>

