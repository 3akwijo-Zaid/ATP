-- Migration: Rename set_winner_points to match_score_points in point_settings

ALTER TABLE point_settings ADD COLUMN match_score_points INT DEFAULT 10;

UPDATE point_settings SET match_score_points = set_winner_points;

ALTER TABLE point_settings DROP COLUMN set_winner_points; 