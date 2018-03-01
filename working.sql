USE IMDB;

SELECT TOP 50 * FROM name_basics;
SELECT TOP 50 * FROM title_akas;
SELECT TOP 50 * FROM title_basics;
-- SELECT TOP 50 * FROM title_crew;
SELECT TOP 50 * FROM title_episode;
SELECT TOP 50 * FROM title_principals;
SELECT TOP 50 * FROM title_ratings;
SELECT TOP 50 * FROM title_directors;
SELECT TOP 50 * FROM title_writers;
SELECT TOP 50 * FROM name_known_for_titles;
SELECT TOP 50 * FROM name_profession;
SELECT TOP 50 * FROM title_genre;

SELECT COUNT(*) AS count FROM name_basics;
SELECT COUNT(*) AS count FROM title_akas;
SELECT COUNT(*) AS count FROM title_basics;
-- SELECT COUNT(*) AS count FROM title_crew;
SELECT COUNT(*) AS count FROM title_episode;
SELECT COUNT(*) AS count FROM title_principals;
SELECT COUNT(*) AS count FROM title_ratings;
SELECT COUNT(*) AS count FROM title_directors;
SELECT COUNT(*) AS count FROM title_writers;
SELECT COUNT(*) AS count FROM name_known_for_titles;
SELECT COUNT(*) AS count FROM name_profession;
SELECT COUNT(*) AS count FROM title_genre;
GO

SELECT TOP 50	*
FROM			title_basics AS TB
JOIN			title_ratings AS TR ON TB.tconst = TR.tconst
JOIN			title_episode AS TE ON TB.tconst = TE.tconst
JOIN			title_basics AS PTB ON TE.parentTconst = PTB.tconst
WHERE			numVotes > 50000
AND				averageRating >= 9.7
ORDER BY		averageRating DESC, numVotes DESC;

SELECT TOP 50	* 
FROM title_principals
JOIN name_basics ON title_principals.nconst = name_basics.nconst
JOIN title_basics ON title_principals.tconst = title_basics.tconst
ORDER BY title_principals.tconst DESC;

SELECT		TOP 50 *
FROM		title_ratings;

SELECT		seasonNumber,
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

SELECT		primaryName,
			COUNT(*) AS Count
FROM		title_basics AS TB
JOIN		title_episode AS TE ON TB.tconst = TE.parentTconst
JOIN		title_basics AS TBE ON TE.tconst = TBE.tconst
JOIN		title_directors AS TD ON TD.tconst = TBE.tconst
JOIN		name_basics AS NB ON TD.nconst = NB.nconst
WHERE		TB.primaryTitle = 'Steins;Gate'
GROUP BY	primaryName
ORDER BY	COUNT(*) DESC;

SELECT		*
FROM		name_basics
WHERE		primaryName = 'Kazuhiro Ozawa'
OR			primaryName = 'Kanji Wakabayashi';

SELECT		TB2.primaryTitle AS "Series Title",
			TB1.primaryTitle AS "Episode Title",
			averageRating AS "Average Rating"
FROM		title_basics AS TB1
LEFT JOIN	title_episode AS TE ON TB1.tconst = TE.tconst
LEFT JOIN	title_basics AS TB2 ON TE.parentTconst = TB2.tconst
LEFT JOIN	title_ratings AS TR ON TB1.tconst = TR.tconst
WHERE		TB1.tconst IN (
	SELECT		tconst
	FROM		title_directors
	WHERE		nconst IN ('nm2324235', 'nm1159969')
)
ORDER BY averageRating DESC;

SELECT		*
FROM		title_basics
JOIN		title_episode ON title_episode.parentTconst = title_basics.tconst
JOIN		title_ratings ON title_ratings.tconst = title_episode.tconst
WHERE		title_basics.tconst = 'tt0805824';

SELECT		*
FROM		title_writers AS TW
JOIN		name_basics AS NB ON TW.nconst = NB.nconst
JOIN		title_basics AS TB ON TW.tconst = TB.tconst
WHERE		TW.tconst IN (
	SELECT		tconst
	FROM		title_basics
	WHERE		primaryTitle = 'Rick and Morty'
);

SELECT		*
FROM		title_directors AS TD
JOIN		name_basics AS NB ON TD.nconst = NB.nconst
JOIN		title_basics AS TB ON TD.tconst = TB.tconst
WHERE		TD.tconst IN (
	SELECT		tconst
	FROM		title_basics
	WHERE		primaryTitle = 'Rick and Morty'
);

SELECT		TB2.primaryTitle,
			TB2.startYear,
			ISNULL(TB2.endYear, TB2.startYear),
			COUNT(*) AS "Number of Appearances"
FROM		title_basics AS TB
LEFT JOIN	title_episode AS TE ON TB.tconst = TE.tconst
LEFT JOIN	title_basics AS TB2 ON TE.parentTconst = TB2.tconst
WHERE		TB.tconst IN (
	SELECT		tconst
	FROM		title_principals
	WHERE		nconst IN (
		SELECT		nconst
		FROM		name_basics
		WHERE		primaryName = 'Betty White'
	)
)
GROUP BY	TB2.primaryTitle, TB2.startYear, TB2.endYear
ORDER BY	TB2.startYear, TB2.primaryTitle;

SELECT		*
FROM		title_basics
WHERE		tconst IN (
	SELECT		tconst
	FROM		name_known_for_titles
	WHERE		nconst IN (
		SELECT		nconst
		FROM		name_basics
		WHERE		primaryName = 'Betty White'
	)
);

SELECT		*
FROM		name_profession
WHERE		nconst IN (
	SELECT		nconst
	FROM		name_basics
	WHERE		primaryName = 'Sutton Foster'
);

SELECT			profession,
				COUNT(*) AS "Count"
FROM			name_profession
GROUP BY		profession
ORDER BY		COUNT(*) DESC;

SELECT			category,
				COUNT(*) AS "Count"
FROM			title_principals
GROUP BY		category
ORDER BY		COUNT(*) DESC;

SELECT		primaryName,
			category,
			characters
FROM		title_principals AS TP
JOIN		title_basics AS TB ON TP.tconst = TB.tconst
JOIN		name_basics AS NB ON TP.nconst = NB.nconst
WHERE		TB.tconst IN (
	SELECT		tconst
	FROM		title_basics
	WHERE		primaryTitle = 'Your Name'
	AND			titleType = 'movie'
);

SELECT		genre,
			COUNT(*) AS "count"
FROM		title_genre
GROUP BY	genre
ORDER BY	COUNT(*) DESC;

SELECT		titleType,
			COUNT(*) AS "count"
FROM		title_basics
GROUP BY	titleType
ORDER BY	COUNT(*) DESC;

SELECT		primaryTitle,
			startYear,
			averageRating,
			numVotes,
			primaryName AS "director"
FROM		title_basics AS TB
JOIN		title_ratings AS TR ON TB.tconst = TR.tconst
LEFT JOIN	title_directors AS TD ON TB.tconst = TD.tconst
LEFT JOIN	name_basics AS NB ON TD.nconst = NB.nconst
WHERE		TB.tconst IN (
	SELECT		TOP 50 TG.tconst
	FROM		title_genre AS TG
	JOIN		title_ratings AS TR ON TG.tconst = TR.tconst
	JOIN		title_basics AS TB ON TB.tconst = TG.tconst
	WHERE		genre = 'Sci-fi'
	AND			numVotes >= 1000
	AND			titleType = 'tvSeries'
	ORDER BY	averageRating DESC
)
ORDER BY	averageRating DESC, primaryTitle;

SELECT		*
FROM		title_basics
JOIN		title_ratings ON title_basics.tconst = title_ratings.tconst
JOIN		title_genre ON title_basics.tconst = title_genre.tconst
WHERE		primaryTitle = 'Steins;Gate'
AND			titleType = 'tvSeries';


SELECT		primaryName
FROM		name_basics
WHERE		nconst IN (
	SELECT		nconst
	FROM		title_principals
	WHERE		tconst IN (
		SELECT		tconst
		FROM		title_basics
		WHERE		primaryTitle = 'Apocalypse Now'
	)
	AND		characters LIKE '%Kurtz%'
);

USE IMDB;

SELECT		TB2.primaryTitle AS seriesTitle,
            title_episode.tconst,
            seasonNumber,
			episodeNumber,
			TB1.primaryTitle,
			averageRating,
			numVotes,
			TB2.tconst
FROM		title_episode
JOIN		title_basics AS TB1 ON title_episode.tconst = TB1.tconst
JOIN		title_ratings ON title_ratings.tconst = TB1.tconst
JOIN        title_basics AS TB2 ON title_episode.parentTconst = TB2.tconst
WHERE		TB2.primaryTitle = 'steins;gate'
ORDER BY    seasonNumber, episodeNumber;

SELECT	*
FROM	(
	SELECT	nconst
	FROM	title_principals
	WHERE	tconst = 'tt0078788'
	UNION ALL
	SELECT	nconst
	FROM	title_directors
	WHERE	tconst = 'tt0078788'
	UNION ALL
	SELECT	nconst
	FROM	title_writers
	WHERE	tconst = 'tt0078788'
	UNION ALL
	SELECT	nconst
	FROM	name_known_for_titles
	WHERE	tconst = 'tt0078788'
) AS people
JOIN	name_basics ON people.nconst = name_basics.nconst
ORDER BY	name_basics.primaryName;

SELECT		DISTINCT KFT2.*, NB.primaryName, TB.primaryTitle
FROM		name_known_for_titles AS KFT1
JOIN		name_known_for_titles AS KFT2 ON KFT1.nconst = KFT2.nconst
JOIN		name_basics AS NB ON KFT2.nconst = NB.nconst
JOIN		title_basics AS TB ON KFT2.tconst = TB.tconst
WHERE		KFT1.tconst = 'tt0098936';

SELECT		primaryName,
			KFT2.category,
			COUNT(*) AS count
FROM		title_principals AS KFT1
JOIN		title_principals AS KFT2 ON KFT1.nconst = KFT2.nconst
JOIN		name_basics AS NB ON KFT2.nconst = NB.nconst
JOIN		title_basics AS TB ON KFT2.tconst = TB.tconst
WHERE		KFT1.tconst = 'tt0078788'
GROUP BY	primaryName, KFT2.category
ORDER BY	COUNT(*) DESC;

SELECT		*
FROM		title_basics
WHERE		primaryTitle = 'Apocalypse Now';

SELECT		category,
			COUNT(*) AS "count"
FROM		title_principals
GROUP BY	category
ORDER BY	COUNT(*) DESC;

SELECT		DISTINCT nconst
FROM		title_principals
WHERE		tconst IN (
	SELECT		tconst
	FROM		title_principals
	WHERE		nconst IN (
		SELECT		nconst
		FROM		title_principals
		WHERE		tconst IN (
			SELECT		tconst
			FROM		title_principals
			WHERE		nconst IN (
				SELECT		nconst
				FROM		title_principals
				WHERE		tconst IN (
					SELECT		tconst
					FROM		title_principals
					WHERE		nconst = (
						SELECT		nconst
						FROM		name_basics
						WHERE		primaryName = 'Kevin Bacon'
						AND			birthYear = 1958
					)
				)
			)
		)
	)
);

EXEC master..xp_fixeddrives
GO

EXEC IMDB..sp_spaceused
GO