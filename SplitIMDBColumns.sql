USE IMDB;
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
            insert into @temptable(items) values(@slice)       

        set @String = right(@String,len(@String) - @idx)       
        if len(@String) = 0 break       
    end   
return 
end;
GO

CREATE TABLE title_directors (
	tconst	NCHAR(9),
	nconst	NCHAR(9),
	PRIMARY KEY (tconst, nconst)
);
GO

INSERT INTO title_directors 
SELECT		tconst, items AS nconst
FROM		title_crew
CROSS APPLY dbo.Split(directors, ',');
GO

CREATE INDEX IX_title_directors_tconst ON title_directors(tconst);
CREATE INDEX IX_title_directors_nconst ON title_directors(nconst);
GO

CREATE TABLE title_writers (
	tconst	NCHAR(9),
	nconst	NCHAR(9),
	PRIMARY KEY (tconst, nconst)
);
GO

INSERT INTO title_writers 
SELECT		tconst, items AS nconst
FROM		title_crew
CROSS APPLY dbo.Split(writers, ',');
GO

CREATE INDEX IX_title_writers_tconst ON title_writers(tconst);
CREATE INDEX IX_title_writers_nconst ON title_writers(nconst);
GO

DROP TABLE title_crew;

GO
CREATE TABLE name_known_for_titles (
	nconst	NCHAR(9),
	tconst	NCHAR(9),
	PRIMARY KEY (nconst, tconst)
);
GO

INSERT INTO name_known_for_titles 
SELECT		nconst, items AS tconst
FROM		name_basics
CROSS APPLY dbo.Split(knownForTitles, ',');
GO

CREATE INDEX IX_name_known_for_titles_tconst ON name_known_for_titles(tconst);
CREATE INDEX IX_name_known_for_titles_nconst ON name_known_for_titles(nconst);
GO

ALTER TABLE name_basics
DROP COLUMN knownForTitles;

GO

CREATE TABLE name_profession (
	nconst		NCHAR(9),
	profession	NVARCHAR(105),
	PRIMARY KEY (nconst, profession)
);
GO

INSERT INTO name_profession
SELECT		nconst, items AS profession
FROM		name_basics
CROSS APPLY dbo.Split(primaryProfession, ',');
GO

CREATE INDEX IX_name_profession_profession ON name_profession(profession);
CREATE INDEX IX_name_profession_nconst ON name_profession(nconst);
GO

ALTER TABLE name_basics
DROP COLUMN primaryProfession;
GO

DBCC CLEANTABLE ('IMDB', 'name_basics', 0);
ALTER INDEX PK__name_bas__49B947A55BC0F53E ON name_basics REBUILD;
GO

CREATE TABLE title_genre (
	tconst		NCHAR(9),
	genre		NVARCHAR(32),
	PRIMARY KEY (tconst, genre)
);
GO

INSERT INTO title_genre
SELECT		tconst, items AS genre
FROM		title_basics
CROSS APPLY dbo.Split(genres, ',');
GO

CREATE INDEX IX_title_genre_genre ON title_genre(genre);
CREATE INDEX IX_title_genre_tconst ON title_genre(tconst);
GO

ALTER TABLE title_basics
DROP COLUMN genres;
GO

DBCC CLEANTABLE ('IMDB', 'title_basics', 0);
ALTER INDEX PK__title_ba__85FD5344BEF7EBF7 ON title_basics REBUILD;
GO


USE IMDB
ALTER DATABASE IMDB SET RECOVERY SIMPLE;
GO
CHECKPOINT;
GO
CHECKPOINT; -- run twice to ensure file wrap-around
GO
DBCC SHRINKFILE(IMDB_log, 200); -- unit is set in MBs
GO
DBCC CHECKDB(IMDB);
GO
