<!doctype html>
<html>
    <head>
        <title>IMDB Series Episode Popularity</title>
        <style>
            th {
                padding: 5px;
                background-color: DarkBlue;
                color: White;
                font-weight: bold;
            }
            .movie_table {
                padding: 10px;
                vertical-align: middle;
                border-spacing: 0px;
            }
            .movie_table tr td {
                padding: 5px;
            }
            .poster {
                height: 50px;
            }
            tr:nth-child(even) {
                background-color: LightBlue;
            }
            tr:nth-child(odd) {
                background-color: Azure;
            }
            .poster_cell {
                text-align: center;
            }
        </style>
        <script>
            function drawPage() {
                const data = <?php
                    /* system("node fetch.js -s " . $_GET['query']); */
                    system("node fetch.js -e " . $_GET['query']);
                 ?>;

                var table = document.createElement('table');
                table.className = 'movie_table';
                var tr = document.createElement('tr');
                ['Poster', 'Title', 'Type', 'Year'].forEach(text => {
                    var th = document.createElement('th');
                    th.appendChild(document.createTextNode(text));
                    tr.appendChild(th);
                });
                table.appendChild(tr);
                console.log(data);
                data.results.forEach(r => {
                    var i = document.createElement('img');
                    var t = document.createTextNode(r.title + ' (' + r.year + ')');
                    i.className = 'poster';
                    if (r.poster != 'N/A') {
                        i.src = r.poster;
                    }
                    var tr = document.createElement('tr');
                    var tdi = document.createElement('td');
                    var tdt = document.createElement('td');
                    var tdtype = document.createElement('td');
                    var tdy = document.createElement('td');

                    tdi.className = 'poster_cell';
                    tdi.appendChild(i);
                    tdt.appendChild(document.createTextNode(r.title));
                    tdtype.appendChild(document.createTextNode(r.type));
                    tdy.appendChild(document.createTextNode(r.year));
                    tr.appendChild(tdi);
                    tr.appendChild(tdt);
                    tr.appendChild(tdtype);
                    tr.appendChild(tdy);
                    table.appendChild(tr);
                });
                document.body.appendChild(table);
            }
        </script>
    </head>
    <body onload="drawPage();">
    </body>
</html>