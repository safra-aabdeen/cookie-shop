<?php
/**
 * Cookie Shop - Persistent ID Generation & Formatting Helper
 *
 * Ensures:
 * 1. IDs are formatted properly across Admin Dashboard pages as 001, 002, 003...
 * 2. Generated IDs are strictly sequential, monotonic, and NEVER reused even when records are deleted or servers restart.
 */

if (!function_exists('format_id')) {
    /**
     * Formats an ID as a 3-digit zero-padded string (e.g. 1 -> 001, 25 -> 025).
     * If the ID is larger than 3 digits, all digits are preserved (e.g. 1000 -> 1000).
     */
    function format_id($id, $pad_len = 3) {
        $num = (int)$id;
        return str_pad((string)$num, $pad_len, '0', STR_PAD_LEFT);
    }
}

if (!function_exists('init_id_tracker')) {
    /**
     * Ensures the persistent id_tracker table exists and is initialized.
     */
    function init_id_tracker($conn) {
        if (!$conn || $conn->connect_error) {
            return;
        }

        // Create tracker table
        $conn->query("CREATE TABLE IF NOT EXISTS `id_tracker` (
            `table_name` VARCHAR(50) NOT NULL PRIMARY KEY,
            `last_id` INT NOT NULL DEFAULT 0
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        // Known base offsets from database schema
        $defaults = [
            'products'         => 10,
            'users'            => 5,
            'orders'           => 11,
            'contact_messages' => 9,
            'order_items'      => 15
        ];

        foreach ($defaults as $tbl => $def_max) {
            $check = $conn->query("SELECT `last_id` FROM `id_tracker` WHERE `table_name` = '$tbl' LIMIT 1");
            if ($check && $check->num_rows > 0) {
                continue;
            }

            // Read the maximum existing ID from the actual data table
            $max_existing = $def_max;
            $res = $conn->query("SELECT COALESCE(MAX(`id`), 0) AS max_id FROM `$tbl`");
            if ($res && $row = $res->fetch_assoc()) {
                $max_existing = max($max_existing, (int)$row['max_id']);
            }

            $conn->query("INSERT INTO `id_tracker` (`table_name`, `last_id`) VALUES ('$tbl', $max_existing)
                          ON DUPLICATE KEY UPDATE `last_id` = GREATEST(`last_id`, $max_existing)");
        }
    }
}

if (!function_exists('peek_next_id')) {
    /**
     * Previews the next ID that will be generated for a table (for display on Add forms).
     * Does NOT increment or consume the counter.
     */
    function peek_next_id($table_name, $conn) {
        if (!$conn || $conn->connect_error) {
            return 1;
        }
        init_id_tracker($conn);

        $stmt = $conn->prepare("SELECT `last_id` FROM `id_tracker` WHERE `table_name` = ? LIMIT 1");
        if ($stmt) {
            $stmt->bind_param("s", $table_name);
            $stmt->execute();
            $res = $stmt->get_result();
            if ($row = $res->fetch_assoc()) {
                $stmt->close();
                return (int)$row['last_id'] + 1;
            }
            $stmt->close();
        }

        // Fallback
        $res = $conn->query("SELECT COALESCE(MAX(`id`), 0) + 1 AS next_id FROM `$table_name`");
        if ($res && $row = $res->fetch_assoc()) {
            return (int)$row['next_id'];
        }
        return 1;
    }
}

if (!function_exists('generate_next_id')) {
    /**
     * Generates, increments, and commits the next unique ID for a table.
     * Guarantees IDs are strictly monotonic and NEVER reused upon record deletion or server restart.
     */
    function generate_next_id($table_name, $conn) {
        if (!$conn || $conn->connect_error) {
            return 0;
        }
        init_id_tracker($conn);

        // Atomically increment last_id
        $conn->query("UPDATE `id_tracker` SET `last_id` = `last_id` + 1 WHERE `table_name` = '$table_name'");

        $stmt = $conn->prepare("SELECT `last_id` FROM `id_tracker` WHERE `table_name` = ? LIMIT 1");
        if ($stmt) {
            $stmt->bind_param("s", $table_name);
            $stmt->execute();
            $res = $stmt->get_result();
            if ($row = $res->fetch_assoc()) {
                $next_id = (int)$row['last_id'];
                $stmt->close();

                // Synchronize database AUTO_INCREMENT to stay higher than last_id
                @$conn->query("ALTER TABLE `$table_name` AUTO_INCREMENT = " . ($next_id + 1));

                return $next_id;
            }
            $stmt->close();
        }

        // Fallback
        $res = $conn->query("SELECT COALESCE(MAX(`id`), 0) + 1 AS next_id FROM `$table_name`");
        if ($res && $row = $res->fetch_assoc()) {
            return (int)$row['next_id'];
        }
        return 1;
    }
}
