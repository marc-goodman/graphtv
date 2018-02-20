const imdb = require('imdb-api');
const KEY = '5a2aaaec';

function search(keyword, callback, err) {
    imdb.search({
        title: keyword
    }, {
        apiKey: KEY
    }).then(callback).catch(err);
}

function episodes(id, callback, err) {
    imdb.getById(id, {
        apiKey: KEY
    }).then(series => {
        series.episodes().then(eps => {
            callback(series);
        }).catch(err);
    }).catch(err);
}

function displayResult(result) {
    console.log(JSON.stringify(result));
}

function main() {
    const command = process.argv[2];

    switch (command) {
        case "-s":
            search(process.argv[3], displayResult, displayResult);
            break;
        case "-e":
            episodes(process.argv[3], displayResult, displayResult);
            break;
        default:
            console.log("Error: Command", command, "not found!");
    }
}

main();
