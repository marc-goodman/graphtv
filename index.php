<!doctype html>
<html>
    <head>
        <title>IMDB Series Episode Popularity</title>
        <style>
            #graph {
                background-color: black;
            }
            body {
                margin: 0px;
                overflow: hidden;
            }
            .note {
                position: absolute;
                display: none;
                background-color: White;
                font-family: Arial, Helvetica, sans-serif;
                padding: 5px;
                border-radius: 5px;
                font-size: 0.75em;
            }
            .title {
                font-weight: bold;
                padding: 3px;
                border-bottom: 1px solid black;
            }
            .info {
                padding: 3px;
            }
        </style>
        <script>
            var AXIS_SIZE = 50;
            var MARGIN = 50;

            function regressSeasonRatings(episodes) {
                var sumx=0.0, sumy=0.0, sumxx=0.0, sumyy=0.0, sumxy=0.0;
                var mx, my, xxvar, yyvar, xyvar;
                var n = episodes.length;

                episodes.forEach(e => {
                    var x = Number(e.episodeNumber);
                    var y = Number(e.averageRating);
                    sumx += x;
                    sumy += y;
                    sumxx += x * x;
                    sumyy += y * y;
                    sumxy += x * y;
                });
                mx = sumx / n;
                my = sumy / n;
                xxvar = (sumxx - 2.0 * mx * sumx + n * mx * mx) / n;
                yyvar = (sumyy - 2.0 * my * sumy + n * my * my)/ n;
                xyvar = (sumxy - mx * sumy - my * sumx + n * mx * my) / n;
                var slope = xyvar / xxvar;
                return [slope, my - slope * mx];
            }

            function isCoprime(v, top) {
                for (var i = 2; i * i < top; i++)
                    if (v % i == 0 && top % i == 0)
                        return false;
                return true;
            }

            function findCoprime(top) {
                if (top < 5)
                    return 1;

                var v = Math.floor(top / 3);

                while (!isCoprime(v, top))
                    v++;
                return v;
            }

            function spectrum2(w, coprime, top) {
                // var STEP = coprime * 2 * Math.PI / top;
                var STEP = 0.6180339887 * 2 * Math.PI;
                var SQRT_2 = Math.sqrt(2);

                var theta = STEP * w;
                var s = Math.sin(theta);
                var c = Math.cos(theta);

                if (s > 0) {
                    var y = 255 * s;
                    var b = 0;
                } else {
                    var y = 0;
                    var b = -255 * s;
                }
                if (c > 0) {
                    var r = 255 * c;
                    var g = 0;
                } else {
                    var r = 0;
                    var g = -255 * c;
                }
                var fr = Math.floor((r + y) / SQRT_2);
                var fg = Math.floor((g + y) / SQRT_2);
                var fb = Math.floor(b);
                return `rgb(${fr}, ${fg}, ${fb})`;
            }

            function spectrum(w) {
                if (w > 1)w = 1;
                if (w < 0)w = 0;

                w = w * (645 - 380) + 380;
                var r, g, b;
                if (w >= 380 && w < 440) {
                    r = -(w - 440.) / (440. - 350.);
                    g = 0.0;
                    b = 1.0;
                } else if (w >= 440 && w < 490) {
                    r = 0.0;
                    g = (w - 440.) / (490. - 440.);
                    b = 1.0;
                } else if (w >= 490 && w < 510) {
                    r = 0.0;
                    g = 1.0;
                    b = (510 - w) / (510. - 490.);
                } else if (w >= 510 && w < 580) {
                    r = (w - 510.) / (580. - 510.);
                    g = 1.0;
                    b = 0.0;
                } else if (w >= 580 && w < 645) {
                    r = 1.0;
                    g = -(w - 645.) / (645. - 580.);
                    b = 0.0;
                } else if (w >= 645 && w <= 780) {
                    r = 1.0;
                    g = 0.0;
                    b = 0.0;
                } else {
                    r = 0.0;
                    g = 0.0;
                    b = 0.0;
                }
                r = Math.round(r * 255);
                g = Math.round(g * 255);
                b = Math.round(b * 255);
                return `rgb(${r}, ${g}, ${b})`;
            }

            function drawPage() {
                data = <?php
                    $query = <<<QUERY
SELECT		TB2.primaryTitle AS seriesTitle,
            title_episode.tconst,
            seasonNumber,
			episodeNumber,
			TB1.primaryTitle,
			averageRating,
			numVotes
FROM		title_episode
JOIN		title_basics AS TB1 ON title_episode.tconst = TB1.tconst
JOIN		title_ratings ON title_ratings.tconst = TB1.tconst
JOIN        title_basics AS TB2 ON title_episode.parentTconst = TB2.tconst
WHERE		TB2.primaryTitle = :title
ORDER BY    seasonNumber, episodeNumber;
QUERY;
                $conn = new PDO("sqlsrv:Server=MARC-PC;Database=IMDB", "IMDB", "IMDB001!");
                // $conn = new PDO("sqlsrv:Server=cisdbss.pcc.edu;Database=IMDB", "275student", "275student");

                $stmt = $conn->prepare($query);
                $stmt->execute(array(':title' => $_GET['title']));
                echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
                ?>;

                var graph = document.getElementById('graph');
                graph.innerHTML = '';

                var width = window.innerWidth;
                var dataWidth = width - AXIS_SIZE - MARGIN;
                var height = window.innerHeight;
                var dataHeight = height - AXIS_SIZE - MARGIN;
                var xSpacing = dataWidth / data.length;

                var yAxis = document.createElementNS('http://www.w3.org/2000/svg', 'line');

                yAxis.setAttribute('stroke', 'Yellow');
                yAxis.setAttribute('stroke-width', '3');
                yAxis.setAttribute('x1', AXIS_SIZE);
                yAxis.setAttribute('x2', AXIS_SIZE);
                yAxis.setAttribute('y1', MARGIN - 20);
                yAxis.setAttribute('y2', MARGIN + dataHeight);
                graph.appendChild(yAxis);

                var xAxis = document.createElementNS('http://www.w3.org/2000/svg', 'line');

                xAxis.setAttribute('stroke', 'Yellow');
                xAxis.setAttribute('stroke-width', '3');
                xAxis.setAttribute('x1', AXIS_SIZE);
                xAxis.setAttribute('x2', width - MARGIN);
                xAxis.setAttribute('y1', MARGIN + dataHeight);
                xAxis.setAttribute('y2', MARGIN + dataHeight);
                graph.appendChild(xAxis);

                var label = document.createElementNS('http://www.w3.org/2000/svg', 'text');
                label.appendChild(document.createTextNode(`Episode ratings for ${data[0].seriesTitle}`));
                label.setAttribute('font-size', 32);
                var bbox = label.getBBox();
                label.setAttribute('x', 0);
                label.setAttribute('y', MARGIN + dataHeight + 32);
                label.setAttribute('fill', 'Black');
                graph.appendChild(label);
                var bbox = label.getBBox();
                label.setAttribute('x', (width - bbox.width) / 2);
                label.setAttribute('fill', 'Yellow');

                graph.style.width = width;
                graph.style.height = height;
                console.log(data);
                var min = Number(data[0].averageRating);
                var max = min;
                var seasons = Number(data[0].seasonNumber);
                data.forEach(episode => {
                    var y = Number(episode.averageRating);
                    if (y > max)
                        max = y;
                    if (y < min)
                        min = y;
                    var s = Number(episode.seasonNumber);
                    if (s > seasons)
                        seasons = s;
                });

                var coprime = findCoprime(seasons);

                max = Math.ceil(max + 0.1);
                min = Math.floor(min - 0.1);

                var ySpacing = dataHeight / (max - min);
                for (var r = min; r <= max; r += 0.5) {
                    var yPos = Math.floor(MARGIN + dataHeight - ySpacing * (r - min));
                    var xGrid = document.createElementNS('http://www.w3.org/2000/svg', 'line');

                    xGrid.setAttribute('stroke', 'Yellow');
                    xGrid.setAttribute('stroke-width', '1');
                    xGrid.setAttribute('x1', AXIS_SIZE - 10);
                    xGrid.setAttribute('x2', width - MARGIN);
                    xGrid.setAttribute('y1', yPos);
                    xGrid.setAttribute('y2', yPos);
                    graph.appendChild(xGrid);
                    var label = document.createElementNS('http://www.w3.org/2000/svg', 'text');
                    label.appendChild(document.createTextNode(r.toFixed(1)));
                    label.setAttribute('x', 10);
                    label.setAttribute('y', yPos + 5);
                    label.setAttribute('fill', 'Yellow');
                    label.setAttribute('font-size', 16);
                    graph.appendChild(label);
                }
                var eps = [];
                var prev = -1;
                var smin;
                var smax;
                var emin;
                var emax;
                for (var i = 0; i <= data.length; i++) {
                    var e = data[i];
                    if (e && e.seasonNumber == prev) {
                        eps.push(e);
                        emax = e.episodeNumber;
                        smax = i;
                    } else {
                        if (eps.length > 1) {
                            var [slope, intercept] = regressSeasonRatings(eps);
                            var line = document.createElementNS('http://www.w3.org/2000/svg', 'line');
                            line.setAttribute('stroke', spectrum2(prev, coprime, seasons));
                            line.setAttribute('stroke-width', 3);
                            line.setAttribute('x1', AXIS_SIZE + xSpacing * (0.5 + smin));
                            line.setAttribute('x2', AXIS_SIZE + xSpacing * (0.5 + smax));
                            line.setAttribute('y1', MARGIN + dataHeight - ySpacing * (emin * slope + intercept - min));
                            line.setAttribute('y2', MARGIN + dataHeight - ySpacing * (emax * slope + intercept - min));
                            graph.appendChild(line);
                        }
                        if (e) {
                            eps = [e];
                            emin = e.episodeNumber;
                            emax = emin;
                            smin = i;
                            smax = i;
                            prev = e.seasonNumber;
                        }
                    }
                }
                data.forEach((episode, pos) => {
                    // var color = spectrum((episode.seasonNumber || 0) / seasons);
                    var color = spectrum2(episode.seasonNumber || 0, coprime, seasons);
                    var point = document.createElementNS('http://www.w3.org/2000/svg', 'circle');
                    var xPos = AXIS_SIZE + xSpacing * (0.5 + pos);
                    var yPos = MARGIN + dataHeight - ySpacing * (episode.averageRating - min);
                    point.setAttribute('fill', color);
                    point.setAttribute('stroke', 'Black');
                    point.setAttribute('r', '4px');
                    point.setAttribute('cx', xPos);
                    point.setAttribute('cy', yPos);
                    graph.appendChild(point);
                    var note = document.createElement('div');
                    note.className = 'note';
                    note.innerHTML = `<div class='title'>${episode.primaryTitle}</div><div class='info'>`
                        + `Season ${episode.seasonNumber}, Episode ${episode.episodeNumber}<br>`
                        + `Rating: ${episode.averageRating}, Votes: ${episode.numVotes}</div>`;

                    if (width - 180 < xPos + 10) {
                        note.style.top = yPos + 10 + 'px';
                        note.style.left = Math.min(width - 180, xPos + 10) + 'px';
                    } else {
                        note.style.top = Math.floor(Math.min(height - 100, yPos - 33)) + 'px';
                        note.style.left = Math.min(width - 180, xPos + 10) + 'px';
                    }
                    document.body.appendChild(note);
                    point.addEventListener('mouseover', () => { note.style.display = 'block' });
                    point.addEventListener('mouseout', () => { note.style.display = 'none' });
                    point.addEventListener('click', () => { location.href = `http://imdb.com/title/${episode.tconst}`; });
                });
            }

            window.addEventListener('resize', drawPage);
        </script>
    </head>
    <body onload="drawPage();">
    <svg id="graph" />
    </body>
</html>