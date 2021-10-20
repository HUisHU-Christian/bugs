ALTER TABLE users ADD COLUMN preferences text DEFAULT 'sidebar=true;orderSidebar=desc;numSidebar=999;template=default' AFTER language;
INSERT INTO activity (id, description, activity) VALUES (13, 'Elapsed time worked on an issue', 'issue_chrono');
