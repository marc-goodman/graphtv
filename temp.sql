
CREATE TABLE title_principals (
    tconst NCHAR(9) NOT NULL PRIMARY KEY,
    ordering INT NOT NULL,
    nconst NCHAR(9) NOT NULL,
    category NCHAR(19) NOT NULL,
    job NVARCHAR(286),
    characters NVARCHAR(463)
);

CREATE TABLE name_basics (
    nconst NCHAR(9) NOT NULL PRIMARY KEY,
    primaryName NVARCHAR(105) NOT NULL,
    birthYear INT,
    deathYear INT,
    primaryProfession NVARCHAR(66) NOT NULL,
    knownForTitles NVARCHAR(79)
);

CREATE TABLE title_basics (
    tconst NCHAR(9) NOT NULL PRIMARY KEY,
    titleType NCHAR(12) NOT NULL,
    primaryTitle NVARCHAR(408) NOT NULL,
    originalTitle NVARCHAR(408),
    isAdult BIT NOT NULL,
    startYear INT,
    endYear INT,
    runtimeMinutes INT,
    genres NVARCHAR(32)
);

CREATE TABLE title_akas (
    titleId NCHAR(9) NOT NULL PRIMARY KEY,
    ordering INT NOT NULL,
    title NVARCHAR(808) NOT NULL,
    region NCHAR(4),
    language NCHAR(3),
    types NCHAR(16),
    attributes NVARCHAR(62),
    isOriginalTitle INT
);

CREATE TABLE title_crew (
    tconst NCHAR(9) NOT NULL PRIMARY KEY,
    directors NVARCHAR(MAX),
    writers NVARCHAR(MAX)
);

CREATE TABLE title_episode (
    tconst NCHAR(9) NOT NULL PRIMARY KEY,
    parentTconst NCHAR(9) NOT NULL,
    seasonNumber INT,
    episodeNumber INT
);

CREATE TABLE title_ratings (
    tconst NCHAR(9) NOT NULL PRIMARY KEY,
    averageRating NUMERIC(4, 1) NOT NULL,
    numVotes INT NOT NULL
);
GO
