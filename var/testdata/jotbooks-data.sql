--
-- Authors
--
INSERT INTO `authors` VALUES(1, 'Neil McDonald',   'neil-mcdonald');
INSERT INTO `authors` VALUES(2, 'Colin Crouch',    'colin-crouch');
INSERT INTO `authors` VALUES(3, 'Craig Pritchett', 'craig-pritchett');


--
-- Publishers
--
INSERT INTO `publishers` VALUES(1, 'Everyman Chess', 'everyman-chess');


--
-- Books
--
INSERT INTO `books` VALUES( 1,
    'Chess Secrets: The Giants of Power Play',
    'Learn from Topalov, Geller, Bronstein, Alekhine and Morphy',
    'chess-secrets-the-giants-of-power-play',
    '978-1-85774-597-8', '2009'
);
INSERT INTO `book_authors`    VALUES(1, 1);
INSERT INTO `book_publishers` VALUES(1, 1);


INSERT INTO `books` VALUES( 2,
    'Chess Secrets: Great Attackers',
    'Learn from Kasparov, Tal and Stein',
    'chess-secrets-great-attackers',
    '978-1-85774-579-4', '2009'
);
INSERT INTO `book_authors`    VALUES(2, 2);
INSERT INTO `book_publishers` VALUES(2, 1);


INSERT INTO `books` VALUES( 3,
    'Chess Secrets: The Giants of Strategy',
    'Learn from Kramnik, Karpov, Petrosian, Capablanca and Nimzowitsch',
    'chess-secrets-the-giants-of-strategy',
    '978-1-85774-541-1', '2007'
);
INSERT INTO `book_authors`    VALUES(3, 1);
INSERT INTO `book_publishers` VALUES(3, 1);


INSERT INTO `books` VALUES( 4,
    'Chess Secrets: Heroes of Classical Chess',
    'Learn from Carlsen, Anand, Fischer, Smyslow and Rubinstein',
    'chess-secrets-heroes-of-classical-chess',
    '978-1-85774-619-7', '2009'
);
INSERT INTO `book_authors`    VALUES(4, 3);
INSERT INTO `book_publishers` VALUES(4, 1);

