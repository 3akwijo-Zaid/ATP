<?php
require_once 'Database.php';

class User {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    public function register($data) {
        $this->db->query('INSERT INTO users (username, password_hash) VALUES (:username, :password)');
        $this->db->bind(':username', $data['username']);
        $this->db->bind(':password', $data['password']);

        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function login($username, $password) {
        $this->db->query('SELECT * FROM users WHERE username = :username');
        $this->db->bind(':username', $username);

        $row = $this->db->single();

        if ($row) {
            $hashed_password = $row['password_hash'];
            if (password_verify($password, $hashed_password)) {
                return $row;
            }
        }
        
        return false;
    }

    public function findUserByUsername($username) {
        $this->db->query('SELECT * FROM users WHERE username = :username');
        $this->db->bind(':username', $username);
        $this->db->single();

        if ($this->db->rowCount() > 0) {
            return true;
        } else {
            return false;
        }
    }


    public function getScoreboard() {
        // Exclude admin users and users with empty/null usernames
        $this->db->query('SELECT id, username, points FROM users WHERE (is_admin = 0 OR is_admin IS NULL) AND username IS NOT NULL AND username != "" AND username != "admin" ORDER BY points DESC');
        return $this->db->resultSet();
    }

    public function getAllUsers() {
        // Return all users except those with empty/null usernames and the username 'admin'
        $this->db->query('SELECT id, username, points, is_admin, country, created_at FROM users WHERE username IS NOT NULL AND username != "" AND username != "admin" ORDER BY username ASC');
        return $this->db->resultSet();
    }

    public function getUserById($userId) {
        $this->db->query('SELECT id, username, points, is_admin FROM users WHERE id = :id');
        $this->db->bind(':id', $userId);
        return $this->db->single();
    }

    public function promoteToAdmin($userId) {
        $this->db->query('UPDATE users SET is_admin = 1 WHERE id = :user_id');
        $this->db->bind(':user_id', $userId);
        
        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function revokeAdmin($userId) {
        $this->db->query('UPDATE users SET is_admin = 0 WHERE id = :user_id');
        $this->db->bind(':user_id', $userId);
        
        if ($this->db->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function getProfile($userId) {
        $this->db->query('SELECT username, avatar, country, created_at FROM users WHERE id = :id');
        $this->db->bind(':id', $userId);
        $row = $this->db->single();
        // Get rank (by points)
        $this->db->query('SELECT id FROM users ORDER BY points DESC, id ASC');
        $users = $this->db->resultSet();
        $rank = 1;
        foreach ($users as $i => $u) {
            if ($u['id'] == $userId) { $rank = $i+1; break; }
        }
        // Fix avatar path if it's a filename
        $avatar = $row['avatar'];
        if ($avatar && !preg_match('/^https?:\/\//', $avatar) && !str_starts_with($avatar, '/')) {
            $avatar = '/ATP/public/assets/img/' . $avatar;
        }
        return [
            'username' => $row['username'],
            'avatar' => $avatar,
            'flag' => $row['country'],
            'join_date' => substr($row['created_at'], 0, 10),
            'rank' => $rank
        ];
    }

    public function getStats($userId) {
        $this->db->query('SELECT points FROM users WHERE id = :id');
        $this->db->bind(':id', $userId);
        $points = $this->db->single()['points'];
        
        // Check if correct column exists in predictions table
        try {
            $this->db->query('SELECT COUNT(*) as total, SUM(correct) as correct FROM predictions WHERE user_id = :id');
            $this->db->bind(':id', $userId);
            $row = $this->db->single();
            $total = $row['total'] ?: 0;
            $correct = $row['correct'] ?: 0;
        } catch (Exception $e) {
            // If correct column doesn't exist, just count total predictions
            $this->db->query('SELECT COUNT(*) as total FROM predictions WHERE user_id = :id');
            $this->db->bind(':id', $userId);
            $row = $this->db->single();
            $total = $row['total'] ?: 0;
            $correct = 0; // Default to 0 if column doesn't exist
        }
        
        $accuracy = $total > 0 ? round($correct / $total * 100) : 0;
        
        // Streak calculation - handle missing correct column
        $streak = 0;
        try {
            $this->db->query('SELECT correct FROM predictions WHERE user_id = :id ORDER BY created_at DESC LIMIT 20');
            $this->db->bind(':id', $userId);
            $rows = $this->db->resultSet();
            foreach ($rows as $r) {
                if ($r['correct']) $streak++;
                else break;
            }
        } catch (Exception $e) {
            // If correct column doesn't exist, set streak to 0
            $streak = 0;
        }
        
        return [
            'points' => $points,
            'accuracy' => $accuracy,
            'streak' => $streak,
            'total_predictions' => $total
        ];
    }

    public function getBadges($userId) {
        $badges = [];
        $stats = $this->getStats($userId);
        // Streak badges
        if ($stats['streak'] >= 10) $badges[] = [
            'icon' => '🔥',
            'label' => 'Hot Streak',
            'tooltip' => '10+ correct predictions in a row!'
        ];
        elseif ($stats['streak'] >= 5) $badges[] = [
            'icon' => '🔥',
            'label' => 'Streak '.$stats['streak'],
            'tooltip' => $stats['streak'].' correct predictions in a row.'
        ];
        elseif ($stats['streak'] >= 3) $badges[] = [
            'icon' => '🔥',
            'label' => 'Streak '.$stats['streak'],
            'tooltip' => $stats['streak'].' correct predictions in a row.'
        ];
        // Accuracy badges
        if ($stats['accuracy'] >= 90) $badges[] = [
            'icon' => '🎯',
            'label' => 'Sniper',
            'tooltip' => '90%+ prediction accuracy.'
        ];
        elseif ($stats['accuracy'] >= 70) $badges[] = [
            'icon' => '🎯',
            'label' => 'Sharp Shooter',
            'tooltip' => '70%+ prediction accuracy.'
        ];
        // Points badges
        if ($stats['points'] >= 5000) $badges[] = [
            'icon' => '🏆',
            'label' => 'Legend',
            'tooltip' => '5000+ points earned.'
        ];
        elseif ($stats['points'] >= 1000) $badges[] = [
            'icon' => '🏅',
            'label' => 'Pro',
            'tooltip' => '1000+ points earned.'
        ];
        // Participation badges
        if ($stats['total_predictions'] >= 100) $badges[] = [
            'icon' => '📈',
            'label' => 'Century Predictor',
            'tooltip' => '100+ predictions made.'
        ];
        elseif ($stats['total_predictions'] >= 50) $badges[] = [
            'icon' => '📈',
            'label' => 'Seasoned Predictor',
            'tooltip' => '50+ predictions made.'
        ];
        elseif ($stats['total_predictions'] >= 10) $badges[] = [
            'icon' => '📈',
            'label' => 'Rookie',
            'tooltip' => '10+ predictions made.'
        ];
        // Special badges
        // First prediction
        if ($stats['total_predictions'] >= 1) $badges[] = [
            'icon' => '🌟',
            'label' => 'First Prediction',
            'tooltip' => 'You made your first prediction!'
        ];
        // Top 3 leaderboard (dummy, needs real leaderboard logic)
        $scoreboard = $this->getScoreboard();
        foreach ($scoreboard as $i => $u) {
            if ($u['id'] == $userId && $i < 3) {
                $badges[] = [
                    'icon' => '🥇',
                    'label' => 'Top '.($i+1),
                    'tooltip' => 'Currently ranked #'.($i+1).' on the leaderboard.'
                ];
                break;
            }
        }
        return $badges;
    }

    public function updateProfile($userId, $data) {
        $fields = [];
        if (array_key_exists('avatar', $data) && $data['avatar'] !== '') {
            $fields[] = 'avatar = :avatar';
        }
        if (!empty($data['country'])) {
            $fields[] = 'country = :country';
        }
        if (!empty($data['password'])) {
            $fields[] = 'password_hash = :password_hash';
        }
        if (!$fields) return ['success'=>false,'error'=>'No changes'];
        $sql = 'UPDATE users SET ' . implode(', ', $fields) . ' WHERE id = :id';
        $this->db->query($sql);
        if (array_key_exists('avatar', $data) && $data['avatar'] !== '') $this->db->bind(':avatar', $data['avatar']);
        if (!empty($data['country'])) $this->db->bind(':country', $data['country']);
        if (!empty($data['password'])) $this->db->bind(':password_hash', password_hash($data['password'], PASSWORD_DEFAULT));
        $this->db->bind(':id', $userId);
        $ok = $this->db->execute();
        return $ok ? ['success'=>true] : ['success'=>false,'error'=>'Update failed'];
    }
} 