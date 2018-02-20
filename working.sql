USE master;
DROP DATABASE IMDB;
GO

CREATE DATABASE IMDB;
GO
USE IMDB;

CREATE TABLE title_ratings (
    tconst NCHAR(9) NOT NULL PRIMARY KEY,
    averageRating NUMERIC(4, 1) NOT NULL,
    numVotes INT NOT NULL
);

INSERT INTO title_ratings VALUES ('tt0000001', '5.8', '1350');

SELECT TOP 50 * FROM title_ratings;
SELECT TOP 50 * FROM name_basics;
SELECT TOP 50 * FROM title_akas;
SELECT TOP 50 * FROM title_basics;
SELECT TOP 50 * FROM title_principals;

DELETE FROM title_ratings;
DELETE FROM name_basics;
DELETE FROM title_akas;
DELETE FROM title_basics;
DELETE FROM title_principals;

SELECT COUNT(*) FROM title_ratings;
SELECT COUNT(*) FROM title_principals;
GO

SELECT TOP 50	*
FROM			title_basics
JOIN			title_ratings ON title_basics.tconst = title_ratings.tconst
WHERE			numVotes > 50000
ORDER BY		averageRating DESC, numVotes DESC;

SELECT TOP 50	* 
FROM title_principals
JOIN name_basics ON title_principals.nconst = name_basics.nconst
JOIN title_basics ON title_principals.tconst = title_basics.tconst
ORDER BY title_principals.tconst DESC;

SELECT		TOP 50 *
FROM		title_ratings;

SELECT		TOP 50
			seasonNumber,
			episodeNumber,
			primaryTitle,
			averageRating,
			numVotes
FROM		title_episode
JOIN		title_basics ON title_episode.tconst = title_basics.tconst
JOIN		title_ratings ON title_ratings.tconst = title_basics.tconst
WHERE		parentTconst IN (
	SELECT		tconst
	FROM		title_basics
	WHERE		primaryTitle = 'Erased'
)
ORDER BY seasonNumber, episodeNumber;
