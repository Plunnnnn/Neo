function searchMovies() {
    var movieTitle = document.getElementById("movieTitle").value;
    // Effectuer la requête AJAX
    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            var resultsDiv = document.getElementById("results");
            resultsDiv.innerHTML = xhr.responseText;
        }
    };
    
    xhr.open("GET", "search.php?title=" + encodeURIComponent(movieTitle), true);
    xhr.send();
}

function showMovieInfo(movieId) {
    
    var xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            var resultsDiv = document.getElementById("results");
            resultsDiv.innerHTML = xhr.responseText;
        }
    };
    
    xhr.open("GET", "search.php?movie_id=" + encodeURIComponent(movieId), true);
    xhr.send();
}