<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Migrate extends Admin_Controller {

    public function __construct() {
        parent::__construct();
        // Note: You can restrict this to Super Admin only for safety
    }

    /**
     * Runs all pending SQL migrations found in application/migrations/
     * Accessible via: your-site-url/admin/migrate
     */
    public function index() {
        $this->run_migrations();
    }

    private function run_migrations() {
        echo "<h3>Database Migration Runner</h3>";
        echo "<hr>";

        // 1. Create a log table to track which migrations have already been applied
        $this->db->query("CREATE TABLE IF NOT EXISTS `migrations_log` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `migration_name` varchar(255) NOT NULL,
            `run_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `idx_migration_name` (`migration_name`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

        $migration_path = APPPATH . 'migrations/';
        
        if (!is_dir($migration_path)) {
            echo "Migration directory not found.";
            return;
        }

        $files = scandir($migration_path);
        // Sort files to ensure 001 runs before 002, etc.
        sort($files);
        
        $count = 0;
        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) == 'sql') {
                // Check if this migration file has already been run
                $check = $this->db->get_where('migrations_log', array('migration_name' => $file))->row();
                
                if (!$check) {
                    echo "Processing: <b>$file</b> ... ";
                    $sql = file_get_contents($migration_path . $file);
                    
                    // Split the SQL file into individual queries
                    $queries = $this->split_sql($sql);
                    
                    $this->db->trans_start();
                    foreach ($queries as $query) {
                        $query = trim($query);
                        if (!empty($query)) {
                            if (!$this->db->query($query)) {
                                break; 
                            }
                        }
                    }
                    $this->db->trans_complete();

                    if ($this->db->trans_status() === FALSE) {
                        echo "<span style='color:red'>FAILED (Transaction Rolled Back)</span><br>";
                        break; // Stop running further migrations if one fails
                    } else {
                        // Log the successful migration
                        $this->db->insert('migrations_log', array('migration_name' => $file));
                        echo "<span style='color:green'>COMPLETED</span><br>";
                        $count++;
                    }
                }
            }
        }

        echo "<hr>";
        if ($count == 0) {
            echo "<b>Database is already up to date.</b> No new migrations found.";
        } else {
            echo "<b>Total $count migrations applied successfully.</b>";
        }
    }

    /**
     * Basic SQL splitter that handles comments and multi-line queries
     */
    private function split_sql($sql) {
        // Remove comments
        $sql = preg_replace('/--.*$/m', '', $sql);
        $sql = preg_replace('/\/\*.*?\*\//s', '', $sql);
        
        // Split by semicolon
        return explode(';', $sql);
    }
}
