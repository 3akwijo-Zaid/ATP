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
        // Exclude only the user with username 'admin'
        $this->db->query('SELECT id, username, points FROM users WHERE username IS NOT NULL AND username != "" AND username != "admin" ORDER BY points DESC');
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
        if ($avatar && !preg_match('/^https?:\/-\//', $avatar) && !str_starts_with($avatar, '/')) {
            $avatar = 'assets/img/' . $avatar;
        }
        return [
            'username' => $row['username'],
            'avatar' => $avatar,
            'flag' => $row['country'],
            'join_date' => (new DateTime($row['created_at'], new DateTimeZone('Europe/Berlin')))->format('Y-m-d H:i:s'),
            'rank' => $rank
        ];
    }

    public function getStats($userId) {
        $this->db->query('SELECT points FROM users WHERE id = :id');
        $this->db->bind(':id', $userId);
        $points = $this->db->single()['points'];
        
        // Check if correct column exists in predictions table
        try {
            $this->db->query('SELECT COUNT(*) as total, SUM(correct) as correct, SUM(points_awarded) as total_points, MAX(points_awarded) as max_points, MIN(created_at) as first_prediction FROM predictions WHERE user_id = :id');
            $this->db->bind(':id', $userId);
            $row = $this->db->single();
            $total = $row['total'] ?: 0;
            $correct = $row['correct'] ?: 0;
            $totalPoints = $row['total_points'] ?: 0;
            $maxPoints = $row['max_points'] ?: 0;
            $firstPrediction = $row['first_prediction'] ?? null;
        } catch (Exception $e) {
            $this->db->query('SELECT COUNT(*) as total, MIN(created_at) as first_prediction FROM predictions WHERE user_id = :id');
            $this->db->bind(':id', $userId);
            $row = $this->db->single();
            $total = $row['total'] ?: 0;
            $correct = 0; // Default to 0 if column doesn't exist
            $totalPoints = 0;
            $maxPoints = 0;
            $firstPrediction = $row['first_prediction'] ?? null;
        }
        $accuracy = $total > 0 ? round($correct / $total * 100) : 0;
        $avgPoints = $total > 0 ? round($totalPoints / $total, 2) : 0;
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
            $streak = 0;
        }
        // Days active
        $this->db->query('SELECT COUNT(DISTINCT DATE(created_at)) as days_active FROM predictions WHERE user_id = :id');
        $this->db->bind(':id', $userId);
        $daysActive = $this->db->single()['days_active'] ?? 0;
        // Most active day
        $this->db->query('SELECT DATE(created_at) as day, COUNT(*) as cnt FROM predictions WHERE user_id = :id GROUP BY day ORDER BY cnt DESC LIMIT 1');
        $this->db->bind(':id', $userId);
        $mostActiveDayRow = $this->db->single();
        $mostActiveDay = $mostActiveDayRow['day'] ?? null;
        $mostActiveDayCount = $mostActiveDayRow['cnt'] ?? 0;
        // Best ever rank (lowest rank achieved)
        $this->db->query('SELECT id FROM users ORDER BY points DESC, id ASC');
        $users = $this->db->resultSet();
        $bestRank = null;
        foreach ($users as $i => $u) {
            if ($u['id'] == $userId) {
                $bestRank = $i+1;
                break;
            }
        }
        // True Rival: Mix of most competitive and closest win/loss
        $this->db->query('
            SELECT
              p2.user_id,
              SUM(CASE WHEN p1.correct > p2.correct THEN 1 ELSE 0 END) AS user_wins,
              SUM(CASE WHEN p1.correct < p2.correct THEN 1 ELSE 0 END) AS rival_wins,
              COUNT(*) AS overlap
            FROM predictions p1
            JOIN predictions p2 ON p1.match_id = p2.match_id AND p1.user_id != p2.user_id
            WHERE p1.user_id = :id
            GROUP BY p2.user_id
            HAVING (user_wins + rival_wins) > 0
        ');
        $this->db->bind(':id', $userId);
        $rows = $this->db->resultSet();
        $bestScore = -1;
        $trueRival = null;
        foreach ($rows as $row) {
            $decisive = $row['user_wins'] + $row['rival_wins'];
            $win_ratio = $decisive > 0 ? $row['user_wins'] / $decisive : 0;
            $closeness = 1 - abs($win_ratio - 0.5) * 2;
            $score = $decisive * 0.7 + $closeness * 100 * 0.3;
            if ($score > $bestScore) {
                $bestScore = $score;
                $trueRival = $row;
                $trueRival['closeness'] = $closeness;
                $trueRival['score'] = $score;
                $trueRival['win_ratio'] = $win_ratio;
            }
        }
        $topRivalUsername = null;
        $topRivalOverlap = null;
        $winRateVsRival = null;
        $rivalCloseness = null;
        if ($trueRival) {
            // Get username
            $this->db->query('SELECT username FROM users WHERE id = :id');
            $this->db->bind(':id', $trueRival['user_id']);
            $topRivalUsername = $this->db->single()['username'] ?? null;
            $topRivalOverlap = $trueRival['overlap'];
            $winRateVsRival = $trueRival['win_ratio'] !== null ? round($trueRival['win_ratio'] * 100, 1) : null;
            $rivalCloseness = round($trueRival['closeness'] * 100, 1);
        }
        // Prediction timing
        $this->db->query('SELECT p.created_at, m.start_time FROM predictions p JOIN matches m ON p.match_id = m.id WHERE p.user_id = :id');
        $this->db->bind(':id', $userId);
        $rows = $this->db->resultSet();
        $totalSeconds = 0; $count = 0; $lastMinuteCount = 0;
        foreach ($rows as $row) {
            $created = strtotime($row['created_at']);
            $start = strtotime($row['start_time']);
            if ($start > $created) {
                $diff = $start - $created;
                $totalSeconds += $diff;
                $count++;
                if ($diff <= 60*10) $lastMinuteCount++; // within 10 minutes
            }
        }
        $avgTimeBeforeMatch = $count > 0 ? round($totalSeconds / $count / 60, 1) : null; // in minutes
        // Return all stats
        return [
            'points' => $points,
            'accuracy' => $accuracy,
            'streak' => $streak,
            'total_predictions' => $total,
            'avg_points' => $avgPoints,
            'max_points' => $maxPoints,
            'days_active' => $daysActive,
            'first_prediction' => $firstPrediction,
            'most_active_day' => $mostActiveDay,
            'most_active_day_count' => $mostActiveDayCount,
            'best_rank' => $bestRank,
            'top_rival_username' => $topRivalUsername,
            'top_rival_overlap' => $topRivalOverlap,
            'win_rate_vs_rival' => $winRateVsRival,
            'rival_closeness' => $rivalCloseness,
            'rival_score' => $bestScore,
            'avg_time_before_match' => $avgTimeBeforeMatch,
            'last_minute_predictions' => $lastMinuteCount
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
        $updates = [];
        
        // Handle avatar update
        if (isset($data['avatar']) && $data['avatar'] !== '') {
            $fields[] = 'avatar = :avatar';
            $updates[':avatar'] = $data['avatar'];
        }
        
        // Handle country update
        if (isset($data['country']) && $data['country'] !== '') {
            $fields[] = 'country = :country';
            $updates[':country'] = $data['country'];
        }
        
        // Handle password update
        if (isset($data['password']) && $data['password'] !== '') {
            $fields[] = 'password_hash = :password_hash';
            $updates[':password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        
        // If no fields to update, return error
        if (empty($fields)) {
            return ['success' => false, 'error' => 'No changes to update'];
        }
        
        // Build and execute the query
        $sql = 'UPDATE users SET ' . implode(', ', $fields) . ' WHERE id = :id';
        $this->db->query($sql);
        
        // Bind all parameters
        foreach ($updates as $key => $value) {
            $this->db->bind($key, $value);
        }
        $this->db->bind(':id', $userId);
        
        // Execute the update
        if ($this->db->execute()) {
            return ['success' => true, 'message' => 'Profile updated successfully'];
        } else {
            return ['success' => false, 'error' => 'Database update failed'];
        }
    }
} 