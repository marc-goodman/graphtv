USE CIS275Sandboxx;

EXEC sp_MSforeachtable @command1 = "DROP TABLE ?"
