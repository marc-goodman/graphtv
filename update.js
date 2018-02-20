// Run with node --max_old_space_size=14000 update.js

var https = require('https');
var fs = require('fs');
var zlib = require('zlib');
var sql = require('mssql');

const FILES = [
    {
        name: 'title.principals'
    },
    {
        name: 'name.basics'
    },
    {
        name: 'title.basics'
    },
    {
        name: 'title.akas'
    },
    {
        name: 'title.crew'
    },
    {
        name: 'title.episode'
    },
    {
        name: 'title.ratings'
    }
];
const ROOT = 'https://datasets.imdbws.com/';
const LOCAL_DIR = 'data/';
const EXTENSION = '.tsv.gz';

function fetchDataFile(name) {
    var file = fs.createWriteStream(LOCAL_DIR + name);
    var request = https.get(ROOT + name, function (response) {
        response.pipe(file);
    });
}

function fetchData() {
    FILES.map(x => fetchDataFile(x.name));
}

function uncompress(name, callback) {
    var stream = fs.createReadStream(LOCAL_DIR + name + EXTENSION);
    // have to box this as a property so that we can null it out in the callback,
    // so it can be garbage collected later on. Otherwise we end up with ALL the data in RAM.
    var lines = { lines: [] };
    var remainder = '';

    // pipe the response into the gunzip to decompress
    var gunzip = zlib.createGunzip();
    stream.pipe(gunzip);

    gunzip.on('data', function(data) {
        // decompression chunk ready, parse it and add the lines to the buffer
        var pos;

        data = remainder + data;
        do {
            pos = data.indexOf('\n');
            if (pos > 0) {
                var l = data.slice(0, pos);
                var data = data.slice(pos + 1);
                lines.lines.push(l.split('\t').map(v => v === '\\N' ? null : v));
            } else {
                remainder = data;
            }
        } while (pos > 0);
    }).on("end", function() {
        // response and decompression complete, join the buffer and return
        callback(null, lines);

    }).on("error", function(e) {
        callback(e);
    });
}

function findColumnTypes(data) {
    var header = data[0];
    var types = [];
    
    header.map((c, index) => {
        var props = {
            name: c,
            hasNull: false,
            isInt: true,
            isFloat: true,
            width: 0,
            unique: false,
            precision: 0,
            isBit: true,
            isPrimary: false
        };
        var uniques = new Set();

        for (var i = 1; i < data.length; i++) {
            var value = data[i][index];
            uniques.add(value);
            if (index == 0)
                props.isPrimary = true;
            if (value !== '1' && value !=='0')
                props.isBit = false;
            if (value && value.length > props.width)
                props.width = value.length;
            if (value == null)
                props.hasNull = true;
            if (value && !value.match(/^[0-9]*$/))
                props.isInt = false;
            if (value && !value.match(/^[0-9]*\.[0-9]*$/)) {
                props.isFloat = false;
            } else if (value) {
                var s = value.split('.');
                props.precision = Math.max(props.precision, s[1].length);
            }
        }
        if (props.isBit || props.isInt || props.isFloat) {
            for (var i = 1; i < data.length; i++) {
                if (data[i][index] !== null)
                    data[i][index] = Number(data[i][index]);
            }
        }
        props.unique = uniques.size == data.length - 1;
        types.push(props);
    });
    return types;
}

function columnMeta(props, index) {
    query = '    ';
    query += props.name + ' ';
    if (props.isBit) {
        query += `BIT`;
        props.type = sql.Bit;
    } else if (props.isInt) {
        query += `INT`;
        props.type = sql.Int;
    } else if (props.isFloat) {
        query += `NUMERIC(${props.width}, ${props.precision})`;
        props.type = sql.Numeric(props.width, props.precision);
    } else if (props.width < 30) {
        query += `NCHAR(${props.width})`;
        props.type = sql.NChar(props.width);
    } else if (props.width > 1024) {
        query += `NVARCHAR(MAX)`;
        props.type = sql.NVarChar(sql.MAX);
    } else {
        query += `NVARCHAR(${props.width})`;
        props.type = sql.NVarChar(props.width);
    }
    if (!props.hasNull) {
        query += ' NOT NULL';
    }
    if (!props.isPrimary && props.unique) {
        query += ' UNIQUE';
    }
    if (props.isPrimary) {
        query += ' PRIMARY KEY';
    }
    return query;
}

function addRows(position, name, types, result, index, outfile, conn) {
    if (position >= result.lines.length) {
        result.lines = null; // let it get garbage collected
        buildTableSchema(1 + index, outfile, conn);
        return;
    }
    var table = new sql.Table(name.replace(/\./g, '_'));
    types.forEach(props => {
        table.columns.add(props.name, props.type, {
            nullable: props.hasNull,
            primary: props.isPrimary,
        });
    });
    for (var j = 0; j < 100000 && position < result.lines.length; j++) {
        table.rows.add.apply(table.rows, result.lines[position++]);
    }
    console.log("Bulk adding", name, position + '...');
    var request = new sql.Request(conn);
    request.bulk(table, (err, recordSet) => {
        if (err)
            console.log(err);
        else
            console.log(JSON.stringify(recordSet));
        err = null;
        table = null;
        request = null;
        addRows(position, name, types, result, index, outfile, conn);
    });
}

function buildTableSchema(index, outfile, conn) {
    if (index >= FILES.length) {
        outfile.write('GO\n');
        outfile.close();
        conn.close();
        return;
    }
    var name = FILES[index].name;

    uncompress(name, (err, result) => {
        /*
        console.log(name);
        console.log(result.slice(0, 5).map(r => r.join('\t')).join('\n'));
        console.log();
        */
        var types = findColumnTypes(result.lines);
        var query = '';

        query += `\nCREATE TABLE ${name.replace(/\./g, '_')} (\n`;
        query += types.map(columnMeta).join(',\n');
        query += `\n);\n`;
        outfile.write(query);
        addRows(1, name, types, result, index, outfile, conn);
    });
}

function buildSchema(db) {
    var query = '';
    var config = {
        server: 'MARC-PC\\SQLEXPRESS01',
        database: db,
        user: 'IMDB',
        password: 'IMDB001!',
        port: 1433,
        connectionTimeout: 999999999,
        requestTimeout: 999999999
    };
    var outfile = fs.createWriteStream('temp.sql');

    var conn = new sql.ConnectionPool(config);
    var request = new sql.Request(conn);
    conn.connect(err => {
        if (err) {
            console.log(err);
            return;
        }
        /*
        request.query(query, (err, recordset) => {
            if (err)
                console.log(err);
            else
                console.log(JSON.stringify(recordset));
            conn.close();
        });
        */

        buildTableSchema(0, outfile, conn);
    });

    // conn.close();
    // outfile.close();
    // sendQuery('master', 'DROP DATABASE IMDB');
    // sendQuery('master', 'CREATE DATABASE IMDB');
}

function testdb() {
    var query = '';
    var config = {
        server: 'MARC-PC\\SQLEXPRESS01',
        database: 'IMDB',
        user: 'IMDB',
        password: 'IMDB001!',
        port: 1433,
        connectionTimeout: 999999999,
        requestTimeout: 999999999
    };
    var conn = new sql.ConnectionPool(config);
    var request = new sql.Request(conn);
    conn.connect(err => {
        if (err) {
            console.log(err);
            return;
        }
        request.query('SELECT * FROM name_basics', (err, recordset) => {
            if (err)
                console.log(err);
            else
                console.log(JSON.stringify(recordset));
            conn.close();
        });
    });
}

function sendQuery(db, query) {
    // fetchData();
    // buildSchema();


    var conn = new sql.ConnectionPool(config);
    var request = new sql.Request(conn);
    conn.connect(err => {
       if (err) {
           console.log(err);
           return;
       }
       request.query(query, (err, recordset) => {
           if (err)
               console.log(err);
           else
               console.log(JSON.stringify(recordset));
           conn.close();
       });
    });
}

buildSchema('IMDB');
// testdb();