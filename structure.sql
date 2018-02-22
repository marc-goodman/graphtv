USE master;
DROP DATABASE IMDB;
GO
CREATE DATABASE IMDB;
GO
USE IMDB;

CREATE TABLE name_basics (
    nconst NCHAR(9) NOT NULL PRIMARY KEY,
    primaryName NVARCHAR(105) NOT NULL,
    birthYear INT,
    deathYear INT,
    primaryProfession NVARCHAR(66) NOT NULL,
    knownForTitles NVARCHAR(79)
);

CREATE INDEX IX_name_basics_primaryName ON name_basics(primaryName);
CREATE INDEX IX_name_basics_birthYear ON name_basics(birthYear);
CREATE INDEX IX_name_basics_deathYear ON name_basics(deathYear);

CREATE TABLE title_akas (
    titleId NCHAR(9) NOT NULL,
    ordering INT NOT NULL,
    title NVARCHAR(808) NOT NULL,
    region NCHAR(4),
    language NCHAR(3),
    types NCHAR(16),
    attributes NVARCHAR(62),
    isOriginalTitle INT,
	PRIMARY KEY (titleId, ordering)
);

CREATE INDEX IX_title_akas_titleId ON title_akas(titleId);
CREATE INDEX IX_title_akas_title ON title_akas(title);

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

CREATE INDEX IX_title_basics_titleType ON title_basics(titleType);
CREATE INDEX IX_title_basics_primaryTitle ON title_basics(primaryTitle);
CREATE INDEX IX_title_basics_startYear ON title_basics(startYear);
CREATE INDEX IX_title_basics_endYear ON title_basics(endYear);

CREATE TABLE title_crew (
    tconst NCHAR(9) NOT NULL PRIMARY KEY,
    directors NVARCHAR(MAX),
    writers NVARCHAR(MAX)
);

CREATE INDEX IX_title_crew_directors ON title_crew(directors);
CREATE INDEX IX_title_crew_writers ON title_crew(writers);

CREATE TABLE title_episode (
    tconst NCHAR(9) NOT NULL PRIMARY KEY,
    parentTconst NCHAR(9) NOT NULL,
    seasonNumber INT,
    episodeNumber INT
);

CREATE INDEX IX_title_episode_parentTconst ON title_episode(parentTconst);

CREATE TABLE title_principals (
    tconst NCHAR(9) NOT NULL,
    ordering INT NOT NULL,
    nconst NCHAR(9) NOT NULL,
    category NCHAR(19) NOT NULL,
    job NVARCHAR(286),
    characters NVARCHAR(463),
	PRIMARY KEY (tconst, ordering)
);

CREATE INDEX IX_title_principals_tconst ON title_principals(tconst);
CREATE INDEX IX_title_principals_nconst ON title_principals(nconst);
CREATE INDEX IX_title_principals_category ON title_principals(category);

CREATE TABLE title_ratings (
    tconst NCHAR(9) NOT NULL PRIMARY KEY,
    averageRating NUMERIC(4, 1) NOT NULL,
    numVotes INT NOT NULL
);
GO

create FUNCTION [dbo].[Split](@String varchar(MAX), @Delimiter char(1))       
returns @temptable TABLE (items varchar(MAX))       
as       
begin      
    declare @idx int       
    declare @slice varchar(8000)       

    select @idx = 1       
        if len(@String)<1 or @String is null  return       

    while @idx!= 0       
    begin       
        set @idx = charindex(@Delimiter,@String)       
        if @idx!=0       
            set @slice = left(@String,@idx - 1)       
        else       
            set @slice = @String       

        if(len(@slice)>0)  
            insert into @temptable(Items) values(@slice)       

        set @String = right(@String,len(@String) - @idx)       
        if len(@String) = 0 break       
    end   
return 
end;