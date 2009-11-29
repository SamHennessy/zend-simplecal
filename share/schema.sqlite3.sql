DROP TABLE IF EXISTS events;

CREATE TABLE events (
  id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
  start_time INTEGER UNSIGNED NOT NULL,
  end_time INTEGER UNSIGNED,
  title VARCHAR(150) NOT NULL,
  description TEXT,
  flags SMALLINT UNSIGNED
);