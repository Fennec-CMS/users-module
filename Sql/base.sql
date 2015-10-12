CREATE TABLE users (
  name VARCHAR(64) NOT NULL,
  username VARCHAR(64) NOT NULL,
  email VARCHAR(255) NOT NULL,
  password VARCHAR(255) NOT NULL,
  status INT,
  timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  id SERIAL
);
